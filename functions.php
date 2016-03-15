<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$table_name = $wpdb->prefix . "twp_logs";
/**
* Add table to db for logs
*/
function twp_install() {
   if (!get_option('twp_db_version')) add_option('twp_db_version', TWP_DB_VERSION);
   if ( twp_db_need_update() ) twp_install_db_tables();
}
register_activation_hook( TWP_PLUGIN_DIR.'/twp.php', 'twp_install' );

/**
 * Install twp table
 */
function twp_install_db_tables() {
	global $wpdb;
	$table_name = $wpdb->prefix . "twp_logs";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		post_id bigint(20) NOT NULL,
		sending_result text NOT NULL,
		UNIQUE KEY id (id)
		) $charset_collate;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}

#forked from alo-easymail plugin. Thanks!
/**
 * Check if plugin tables are already properly installed
 */
function twp_db_need_update() {
	global $wpdb;
	$installed_db = get_option('twp_db_version');
	$missing_table = false; // Check if tables not yet installed
	if ( $wpdb->get_var("show tables like '$table_name'") != $table_name ) $missing_table = true;
	return ( $missing_table || TWP_DB_VERSION != $installed_db ) ? true : false;
}
/**
 * Since 3.1 the register_activation_hook is not called when a plugin
 * is updated, so to run the above code on automatic upgrade you need
 * to check the plugin db version on another hook.
 */
function twp_check_db_when_loaded() {
   if ( twp_db_need_update() ) twp_install_db_tables();
}
add_action('plugins_loaded', 'twp_check_db_when_loaded');
#End forked functions

/**
 * An optimized function for getting our options from db
 *
 * @since 1.5
 */
function twp_get_option() {
	global $wpdb;
	$query = "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'twp_%%'";
	$twp_data = $wpdb->get_results($query, OBJECT_K);
	return $twp_data;
}

/**
* Print admin settings page
*/
function twp_settings_page() {
require_once('admin/settings-page.php');
}

/**
* Print admin subpage
*/
function twp_broadcast_page_callback() {
    require_once('broadcast.php');
}

/**
 * Enqueue scripts in the WordPress admin, excluding edit.php.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function twp_enqueue_script( $hook ) {
	$pages_array = array('toplevel_page_telegram-for-wp', 'post-new.php', 'post.php');
	if (!in_array($hook, $pages_array)){
		return;
	}
	wp_enqueue_script( 'textrange', TWP_PLUGIN_URL. '/inc/js/textrange.js', array(), '', true );
	wp_enqueue_script( 'emojione', TWP_PLUGIN_URL. '/inc/js/emojione.js', array(), '', true );
	wp_enqueue_script( 'twp-functions', TWP_PLUGIN_URL. '/inc/js/fn.js', array(), '', true );
}
add_action( 'admin_enqueue_scripts', 'twp_enqueue_script' );

/**
 * Sanitize a string from user input or from the db
 *
 * check for invalid UTF-8,
 * Convert single < characters to entity,
 * strip all tags,
 * strip octets.
 *
 * @since 2.9.0
 *
 * @param string $str
 * @return string
 */
function twp_sanitize_text_field($str) {
	$filtered = wp_check_invalid_utf8( $str );

	if ( strpos($filtered, '<') !== false ) {
		$filtered = wp_pre_kses_less_than( $filtered );
		// This will strip extra whitespace for us.
		$filtered = strip_tags( $filtered, "<b><strong><em><i><a><code><pre>");
	}
	$found = false;
	while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
		$filtered = str_replace($match[0], '', $filtered);
		$found = true;
	}
	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
	}

	/**
	 * Filter a sanitized text field string.
	 *
	 * @since 2.9.0
	 *
	 * @param string $filtered The sanitized string.
	 * @param string $str      The string prior to being sanitized.
	 */
	return apply_filters( 'twp_sanitize_text_field', $filtered, $str );
}

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function twp_load_textdomain() {
	load_plugin_textdomain( 'twp-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
}
add_action( 'plugins_loaded', 'twp_load_textdomain' );

/**
* Add action links to the plugin list for TWP.
*
* @param $links
* @return array
*/
function twp_plugin_action_links($links) {
	$links[] = '<a href="http://hamyarwp.com/telegram-for-wp/">' . __('Persian Tutorial in HamyarWP', 'twp-plugin') . '</a>';
	return $links;
}
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'twp_plugin_action_links');

/**
* This will get information about sent mail from PHPMailer and send it to user
*/
function twp_mail_action($result, $to, $cc, $bcc, $subject, $body){
	global $tdata;
	$nt = new Notifcaster_Class();
	$_apitoken = $tdata['twp_api_token']->option_value;
	$_msg = $body;
	if($tdata['twp_hashtag']->option_value != '') {
		$_msg = $tdata['twp_hashtag']->option_value."\n".$_msg;
	}
	$nt->Notifcaster($_apitoken);
	if(mb_strlen($_msg) > 4096){
		$splitted_text = $this->str_split_unicode($_msg, 4096);
		foreach ($splitted_text as $text_part) {
			$nt->notify($text_part);
		}
	} else {
		$nt->notify($_msg);
	}
}
/**
 * Setup a custom PHPMailer action callback. This will let us to fire our action every time a mail sent
 * Thanks to Birgire (http://xlino.com/) for creating this code snippet.
 */
function twp_phpmailer_hook ( $phpmailer ){
	$phpmailer->action_function = 'twp_mail_action';
	
}
/**
* Show a warning message for admins.
*/
function twp_api_admin_notice($message) {
	$class = "updated notice is-dismissible";
	$message = sprintf(__('Your API token isn\'t set. Please go to %s and set it.','twp-plugin'), "<a href='".admin_url('admin.php?page=telegram-for-wp/twp.php')."'>".__("TWP Settings", "twp-plugin")."</a>");
	echo"<div class=\"$class\"> <p>$message</p></div>"; 
}


/**
* Adds a box to the main column on the Post and Page edit screens.
*/
function twp_add_meta_box() {
	$screens = get_post_types( '', 'names' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'twp_meta_box',
			__( 'Telegram Channel Options', 'twp-plugin' ),
			'twp_meta_box_callback',
			$screen,
			"normal",
			"high"
			);
	}
}
add_action( 'add_meta_boxes', 'twp_add_meta_box' );

/*
* Gets the excerpt of a specific post ID or object
* @param - $post - object - the object of the post to get the excerpt of
* @param - $length - int - the length of the excerpt in words
* @param - $tags - string - the allowed HTML tags. These will not be stripped out
* @param - $extra - string - text to append to the end of the excerpt
* @author https://pippinsplugins.com
*/
function excerpt_by_id($post, $length = 55, $tags = '<a><em><strong>', $extra = '') {
 
	if(has_excerpt($post->ID)) {
		$the_excerpt = $post->post_excerpt;
		return apply_filters('the_content', $the_excerpt);
	} else {
		$the_excerpt = $post->post_content;
	}
	$the_excerpt = mb_split('/\w+/u', $the_excerpt, $length * 2+1);
	$excerpt_waste = array_pop($the_excerpt);
	$the_excerpt = implode($the_excerpt);
 
	return apply_filters('the_content', $the_excerpt);
}
/**
* Prints the box content.
* 
* @param WP_Post $post The object for the current post/page.
*/
function twp_meta_box_callback( $post ) {
	global $wpdb;
	global $table_name;
	global $tdata;
	// Add a nonce field so we can check for it later.
	$ID = $post->ID;
	wp_nonce_field( 'twp_save_meta_box_data', 'twp_meta_box_nonce' );
	$error = "";
	$dis = "";
	$check_state = "";
	$is_product = false;
	$twp_log = $wpdb->get_row( "SELECT * FROM $table_name WHERE post_id = $ID", ARRAY_A );
	if ($tdata['twp_channel_username']->option_value == "" || $tdata['twp_bot_token']->option_value == "")
		{
			$dis = "disabled=disabled"; 
			$error = "<span style='color:red;font-weight:700;'>".__("Bot token or Channel username aren't set!", "twp-plugin")."</span><br>";
		}
	#Experimental feature : Always check Send to channel option
	// $sc = get_post_meta($ID, '_twp_send_to_channel', true);
	// $sc = $sc != "" ? $sc : get_option( 'twp_send_to_channel');
	$sc = $tdata['twp_send_to_channel']->option_value;
	$cp = get_post_meta($ID, '_twp_meta_pattern', true);
	$cp = $cp != "" ? $cp : $tdata['twp_channel_pattern']->option_value;
	$s = get_post_meta($ID, '_twp_send_thumb', true);
	$s = $s != "" ? $s : $tdata[ 'twp_send_thumb']->option_value;
	if ($post->post_type == 'product'){
		$is_product = true;
	}
	$upload_link = esc_url( get_upload_iframe_src( 'image', $ID ));
	// See if there's a media id already saved as post meta
	$twp_img_id = get_post_meta( $ID, '_twp_img_id', true );
	// Get the image src
	$twp_img_src = wp_get_attachment_image_src( $twp_img_id);
	// For convenience, see if the array is valid
	$twp_have_img = is_array( $twp_img_src );
	?>
	<div id="twp_metabox">
	<style type="text/css">
		.patterns li {display: inline-block; width: auto; padding: 2px 7px 2px 7px; margin-bottom: 10px; border-radius: 3px; text-decoration: none; background-color: #309152; color: white; cursor: pointer;}
		.wc-patterns li {background-color: #a46497;}
		#send-thumb-select {line-height: 2em;}
		#send-thumb-select input {margin-top:1px;}
	</style>
	<?php echo $error ?>
	<table class="form-table">
	<tr>
	<th scope="row"><h3><?php echo __('Send to Telegram channel', 'twp-plugin' ) ?></h3> </th>
	<td>
	<input type="checkbox" id="twp_send_to_channel" name="twp_send_to_channel" <?php echo $dis ?> value="1" <?php checked( '1', $sc ); ?>/><label for="twp_send_to_channel"><?php echo __('Send this post to channel', 'twp-plugin' ) ?> </label>
	</td>
	</tr>
	<?php require_once(TWP_PLUGIN_DIR."/inc/composer.php");?>
	</fieldset>
	</table>
	<hr>
	<p><?php echo __("Sending result: ", "twp-plugin") ?></p>
	<span id="twp_last_publish" style="font-weight:700">
	<?php 
	if($twp_log['sending_result'] == 1){
		# This prevents adding a repetitive phrase to db.
		$sending_result = __("Published successfully", "twp-plugin");
	} else {
		$sending_result = $twp_log['sending_result'];
	}
	echo $sending_result.' || '.__("Date: ", "twp-plugin").$twp_log['time'] 
	?>
	</span>
</div>

<?php
}

/**
* When the post is saved, saves our custom data.
*
* @param int $ID The ID of the post being saved.
*/
function twp_save_meta_box_data( $ID, $post ) {
	global $tdata;
    /*
    * We need to verify this came from our screen and with proper authorization,
    * because the save_post action can be triggered at other times.
    */
    // Check if our nonce is set.
    if ( ! isset( $_POST['twp_meta_box_nonce'] ) ) {
    	return;
    }
    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['twp_meta_box_nonce'], 'twp_save_meta_box_data' ) ) {
    	return;
    }
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    	return;
    }
    // Return if it's a post revision
    if ( false !== wp_is_post_revision( $ID ) ){
    	return;
    }
    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

    	if ( ! current_user_can( 'edit_page', $ID ) ) {
    		return;
    	}

    } else {

    	if ( ! current_user_can( 'edit_post', $ID ) ) {
    		return;
    	}
    }
    # OK, it's safe for us to save the data now.
    // Make sure that it is set.
    if ( isset( $_POST['twp_send_to_channel'] ) ) {
    	update_post_meta( $ID, '_twp_send_to_channel', $_POST['twp_send_to_channel']);
    	# Load global options
    	# p_ prefix stands for $_POST data
    	$tcp = $tdata['twp_channel_pattern']->option_value;
    	$tst = $tdata['twp_send_thumb']->option_value;
    	if ( $tcp != $_POST['twp_channel_pattern']) {
    		$p_tcp = $_POST['twp_channel_pattern'];
    		update_post_meta( $ID, '_twp_meta_pattern', $p_tcp);
    	} else {
    		update_post_meta( $ID, '_twp_meta_pattern', $tcp);
    	}
    	if ( $tst != $_POST['twp_send_thumb']){
    		$p_tst = $_POST['twp_send_thumb'];
    		update_post_meta( $ID, '_twp_send_thumb', $p_tst);
    	} else {
    		update_post_meta( $ID, '_twp_send_thumb', $tst);
    	}
    	if (isset($_POST['twp_img_id'])){
    		update_post_meta( $ID, '_twp_img_id', $_POST['twp_img_id']);
    	}
    } else {
    	update_post_meta( $ID, '_twp_send_to_channel', 0);
    }
    if (get_post_status( $ID ) == 'publish'){
   		twp_post_published ( $ID, $post );
    }
}
add_action( 'save_post', 'twp_save_meta_box_data', 10, 2 );

/**
* When the post is published, send the messages.
* @param int $ID
* @param obj $post
*/
function twp_post_published ( $ID, $post ) {
	global $wpdb;
	global $table_name;
	global $tdata;
	# Checks whether user wants to send this post to channel.
	if(get_post_meta($ID, '_twp_send_to_channel', true) == 1){
		$pattern = get_post_meta($ID, '_twp_meta_pattern', true);
		if ($pattern == "" || $pattern == false){
			$pattern = $tdata['twp_channel_pattern']->option_value;
		}
		if( ! in_array( '_twp_send_thumb', get_post_custom_keys( $ID ) ) ) {
			$thumb_method = $tdata['twp_send_thumb']->option_value;
		} else {
			$thumb_method = get_post_meta($ID, '_twp_send_thumb', true);
		}
	}
	# If there is no pattern then return!
	if ($pattern == ""){
		return;
	}
	switch ($thumb_method) {
		case 1:
			$method = 'photo';
			$photo =  get_attached_file(get_post_thumbnail_id($ID));
			break;
		case 2:
			$method = 'photo';
			$photo =  get_attached_file(get_post_meta( $ID, '_twp_img_id', true ));
			break;
		
		default:
			$method = false;
			break;
	}
	# Initialize Telegram information
	$ch_name = $tdata['twp_channel_username']->option_value;
	$token = $tdata['twp_bot_token']->option_value;
	$web_preview = $tdata['twp_web_preview']->option_value;
	if ($token == "" || $ch_name == ""){
		update_post_meta( $ID, '_twp_meta_data', __('Bot token or Channel username aren\'t set!', 'twp-plugin') );
		return;
	}
	if($post->post_type == 'product'){
		$_pf = new WC_Product_Factory();
		$product = $_pf->get_product($ID);
		$tags_array = explode(', ' , $product->get_tags());
		$categories_array = explode(', ' ,$product->get_categories());
	} else {
		$tags_array = wp_get_post_tags( $ID, array( 'fields' => 'names' ) );
		$categories_array = wp_get_post_categories($ID, array( 'fields' => 'names' ));	
	}
	foreach ($tags_array as $tag) {
		$tags .= " #".$tag;
	}
	foreach ($categories_array as $cat) {
		$categories .= "|".$cat;
	}

	$nt = new Notifcaster_Class();
	switch ($tdata['twp_markdown']->option_value) {
		case 0:
			$format = null;
			break;
		case 1:
		$format = "markdown";
			break;
		case 2:
		$format = "html";
			break;
		default:
			$format = null;
			break;
	}
	$nt->_telegram($token, $format, $web_preview);
	# Preparing message for sending
	#Wordpress default tags and substitutes array
	$wp_tags = array("{title}","{excerpt}","{content}","{author}","{short_url}","{full_url}","{tags}","{categories}");
	$wp_subs = array(
		$post->post_title,
		#Change the below number to change the number of words in excerpt
		wp_trim_excerpt($post->post_content),
		$post->post_content,
		get_the_author_meta("display_name",$post->post_author),
		wp_get_shortlink($ID),
		get_permalink($ID),
		$tags,
		$categories
		);
	#WooCommerce tags and substitutes array
	$wc_tags = array("{width}", "{length}", "{height}", "{weight}", "{price}", "{regular_price}", "{sale_price}", "{sku}", "{stock}", "{downloadable}", "{virtual}", "{sold_indiidually}", "{tax_status}", "{tax_class}", "{stock_status}", "{backorders}", "{featured}", "{visibility}");
	 if ($post->post_type == 'product'){
		$p = $product;
		$wc_subs = array ($p->width, $p->length, $p->height, $p->weight, $p->price, $p->regular_price, $p->sale_price, $p->sku, $p->stock, $p->downloadable, $p->virtual, $p->sold_individually, $p->tax_status, $p->tax_class, $p->stock_status, $p->backorders, $p->featured, $p->visibility);
	}
	
	# The variables are case-sensitive.
	$re = array("{title}","{excerpt}","{content}","{author}","{short_url}","{full_url}","{tags}","{categories}");
	$subst = $wp_subs;
	if ($post->post_type == 'product'){
		$p = $product;
		array_merge($re, $wc_tags);
		array_merge($subst, $wc_subs);
	} else {
	#if it's not a product post then strip out all of the WooCommerce tags
		$strip_wc = true;
	}
	$msg = str_replace($re, $subst, $pattern);
	if ($strip_wc){
		$msg = str_replace($wc_tags, '', $msg);
	}
	
	if ($method == 'photo' && $photo != false ) {
		$r = $nt->channel_photo($ch_name, $msg, $photo);
	} else {
		$r = $nt->channel_text($ch_name, $msg);
	}
	$publish_date = current_time( "mysql", $gmt = 0 );
	if ($r["ok"] == true){
		$sending_result = 1;
	} else {
		$sending_result = $r["description"];  
	}
	$twp_log = $wpdb->get_row( "SELECT * FROM $table_name WHERE post_id = $ID");
	if($twp_log == null){
		$wpdb->replace( 
			$table_name, 
			array( 
				'time' => $publish_date,
				'post_id' => $ID,
				'sending_result' => $sending_result
				)
			);	
	} else {
		$wpdb->update( 
			$table_name, 
			array( 
				'time' => $publish_date,
				'post_id' => $ID,
				'sending_result' => $sending_result
				),
			array ('post_id' => $ID)
			);
	}
	update_post_meta( $ID, '_twp_send_to_channel', 0);
	//unset($_POST['twp_send_to_channel']);
}
add_action( 'publish_post', 'twp_post_published', 10, 2 );

function twp_ajax_test_callback() {
	$nt = new Notifcaster_Class();
	switch ($_POST['subject']) {
		case 'm':
			//This will send a test message.
			$nt->Notifcaster($_POST['api_token']);
			$result = $nt->notify($_POST['msg']);
			echo json_encode($result);
			wp_die();
			break;
		case 'c':
			$nt->_telegram($_POST['bot_token'], $_POST['markdown'], $_POST['web_preview']);
			$msg = str_replace("\\", "", $_POST['msg']);
			$result = $nt->channel_text($_POST['channel_username'], $msg );
			echo json_encode($result);
			wp_die();
			break;
		case 'b':
			$nt->_telegram($_POST['bot_token']);
			$result = $nt->get_bot();
			echo json_encode($result);
			wp_die();
			break;
		default:
			return "Invalid POST request";
			break;
	}
}
add_action( 'wp_ajax_twp_ajax_test', 'twp_ajax_test_callback' );
?>