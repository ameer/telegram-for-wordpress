<?php
$twp_db_version = '1.0';
$table_name = $wpdb->prefix . "twp_logs";
/**
* Add table to db for logs
*/
function twp_install() {
   global $wpdb;
   global $twp_db_version;
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
register_activation_hook( TWP_PLUGIN_DIR.'/twp.php', 'twp_install' );
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
		$filtered = wp_strip_all_tags( $filtered, true );
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
	$nt = new Notifcaster_Class();
	$_apitoken = get_option('twp_api_token');
	$_msg = $body;
	if(get_option('twp_hashtag') != '') {
		$_msg = get_option('twp_hashtag')."\n".$_msg;
	}
	$nt->Notifcaster($_apitoken);
	$nt->notify($_msg);
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

add_action( 'add_meta_boxes', 'twp_add_meta_box' );
/**
* Adds a box to the main column on the Post and Page edit screens.
*/
function twp_add_meta_box() {
	$screens = get_post_types( '', 'names' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'twp_meta_box',
			__( 'Send to Telegram Channel', 'twp-plugin' ),
			'twp_meta_box_callback',
			$screen,
			"normal",
			"high"
			);
	}
}

/**
* Prints the box content.
* 
* @param WP_Post $post The object for the current post/page.
*/
function twp_meta_box_callback( $post ) {
	global $wpdb;
	global $table_name;
	// Add a nonce field so we can check for it later.
	$ID = $post->ID;
	wp_nonce_field( 'twp_save_meta_box_data', 'twp_meta_box_nonce' );
	$error = "";
	$dis = "";
	$check_state = "";
	$is_product = false;
	$twp_log = $wpdb->get_row( "SELECT * FROM $table_name WHERE post_id = $ID", ARRAY_A );
	if (get_option('twp_channel_username') == "" || get_option('twp_bot_token') == "")
		{
			$dis = "disabled=disabled"; 
			$error = "<span style='color:red;font-weight:700;'>".__("Bot token or Channel username aren't set!", "twp-plugin")."</span><br>";
		}
	$twp_send_to_channel = get_option( 'twp_send_to_channel');
	$twp_channel_pattern = get_post_meta($ID, '_twp_meta_pattern', true) != "" ? get_post_meta($ID, '_twp_meta_pattern', true) : get_option( 'twp_channel_pattern');
	$twp_send_thumb = get_post_meta($ID, '_twp_send_thumb', true) != "" ? get_post_meta($ID, '_twp_send_thumb', true) : get_option( 'twp_send_thumb');
	if ($post->post_type == 'product'){
		$is_product = true;
	}
	?>
	<div style="padding-top: 7px;">
	<style type="text/css">
		.patterns li {display: inline-block; width: auto; padding: 2px 7px 2px 7px; margin-bottom: 10px; border-radius: 3px; text-decoration: none; background-color: #309152; color: white; cursor: pointer;}
		.wc-patterns li {background-color: #a46497;}
		#send-thumb-select {line-height: 2em;}
		#send-thumb-select input {margin-top:1px;}
	</style>
	<?php echo $error ?>
	<input type="checkbox" id="twp_send_to_channel" name="twp_send_to_channel" <?php echo $dis ?> value="1" <?php checked( '1', $twp_send_to_channel ); ?>/><label for="twp_send_to_channel"><?php echo __('Send to Telegram Channel', 'twp-plugin' ) ?> </label>
	<p class="howto"><?php echo __("Click on the below tags to customize the pattern", "twp-plugin") ?></p>
	<fieldset id="twp_fieldset" style="margin: 10px 20px;line-height: 2em;" <?php echo $dis ?> >
		<div class="toolbar">
			<ul class="patterns" style="margin-bottom:0;">
				<li>{title}</li>
				<li>{excerpt}</li>
				<li>{content}</li>
				<li>{author}</li>
				<li>{short_url}</li>
				<li>{full_url}</li>
				<li>{tags}</li>
				<li>{categories}</li>
			</ul>
		<?php 
		if($is_product){
			?>
			<ul class="patterns wc-patterns" style="margin-top:0;">
				<li>{width}</li>
				<li>{length}</li>
				<li>{height}</li>
				<li>{weight}</li>
				<li>{price}</li>
				<li>{regular_price}</li>
				<li>{sale_price}</li>
				<li>{sku}</li>
				<li>{stock}</li>
				<li>{downloadable}</li>
				<li>{virtual}</li>
				<li>{sold_individually}</li>
				<li>{tax_status}</li>
				<li>{tax_class}</li>
				<li>{stock_status}</li>
				<li>{backorders}</li>
				<li>{featured}</li>
				<li>{visibility}</li>
			</ul>
			<?php 
		} 
			?>
		</div>
		<textarea id="twp_channel_pattern" name="twp_channel_pattern"style="resize: vertical; width: 65%; height: auto;min-height: 128px;"><?php echo $twp_channel_pattern ?></textarea>
		<br>
		<div id="send-thumb-select">
		<input type="radio" name="twp_send_thumb" id="twp-send-thumb-0" <?php echo ($twp_send_thumb==0)?'checked=checked':'' ?> value="0">
			<label for="twp-send-thumb-0">Don't send featured image</label>
			<br>
			<input type="radio" name="twp_send_thumb" id="twp-send-thumb-1" <?php echo ($twp_send_thumb==1)?'checked=checked':'' ?> value="1">
			<label for="twp-send-thumb-1">Send featured image</label>
			<br>
		</div>
	</fieldset>
	<hr>
	<p><?php echo __("Sending result: ", "twp-plugin") ?></p><span id="twp_last_publish" style="font-weight:700"><?php echo $twp_log['sending_result'].' '.$twp_log['time'] ?></span>
</div>
<script>
	jQuery("#twp_send_to_channel").click(function() {
		if (jQuery(this).prop("checked") == false){
			jQuery("#twp_fieldset").prop("disabled", true);
			jQuery("#twp_fieldset label").css("color", "grey")
		}
		else {
			jQuery("#twp_fieldset").prop("disabled", false);
			jQuery("#twp_fieldset label").css("color", "black")
		}
	});
	jQuery(document).ready(function(){
		if (jQuery('#twp_send_to_channel').prop("checked") == false){
			jQuery("#twp_fieldset").prop("disabled", true);
			jQuery("#twp_fieldset label").css("color", "grey")
		}
		else {
			jQuery("#twp_fieldset").prop("disabled", false);
			jQuery("#twp_fieldset label").css("color", "black")
		}
		jQuery('.patterns li').click(function(){
			jQuery('#twp_channel_pattern').textrange('insert', jQuery(this).text())
			jQuery('#twp_channel_pattern').textrange('setcursor', jQuery('#twp_channel_pattern').textrange('get', 'end'));
		})
	})
	 
	jQuery("#twp_channel_pattern").keyup(function(){
		var re = /(:\w+:)/gi;
		var str = jQuery("#twp_channel_pattern").val(); 
		var emojis = str.match(re);
		if ()
		for (var i = 0; i <= emojis.length; i++) {
			var emoji = emojione.shortnameToUnicode(emojis[i]);
			str.replace(emojis[i], emoji);
		}
		jQuery(this).val()= str;
	})
</script>
<?php
}

/**
* When the post is saved, saves our custom data.
*
* @param int $ID The ID of the post being saved.
*/
function twp_save_meta_box_data( $ID, $post ) {
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
    	$tcp = get_option( 'twp_channel_pattern');
    	$tst = get_option('twp_send_thumb');
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
	# Checks whether user wants to send this post to channel.
	if(get_post_meta($ID, '_twp_send_to_channel', true) == 1){
		$pattern = get_post_meta($ID, '_twp_meta_pattern', true);
		if ($pattern == "" || $pattern == false){
			$pattern = get_option( 'twp_channel_pattern');
		}
		if( ! in_array( '_twp_send_thumb', get_post_custom_keys( $ID ) ) ) {
			$thumb_method = get_option( 'twp_send_thumb');
		} else {
			$thumb_method = get_post_meta($ID, '_twp_send_thumb', true);
		}
	}
	# If there is no pattern then return!
	if ($pattern == ""){
		return;
	}
	if($thumb_method == 1){
		$method = 'photo';
		$photo =  get_attached_file(get_post_thumbnail_id($ID));
	} else {
		$method = false;
	}
	# Initialize Telegram information
	$ch_name = get_option('twp_channel_username');
	$token = get_option('twp_bot_token');
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
	$nt->_telegram($token, "markdown");
	# Preparing message for sending
	
	# The variables are case-sensitive.
	$re = array("{title}","{excerpt}","{content}","{author}","{short_url}","{full_url}","{tags}","{categories}");
	$subst = array(
		$post->post_title,
		$post->post_excerpt,
		$post->post_content,
		get_the_author_meta("display_name",$post->post_author),
		wp_get_shortlink($ID),
		get_permalink($ID),
		$tags,
		$categories
		);
	if ($post->post_type == 'product'){
		$p = $product;
		array_push($re, "{width}", "{length}", "{height}", "{weight}", "{price}", "{regular_price}", "{sale_price}", "{sku}", "{stock}", "{downloadable}", "{virtual}", "{sold_individually}", "{tax_status}", "{tax_class}", "{stock_status}", "{backorders}", "{featured}", "{visibility}");
		array_push($subst, $p->width, $p->length, $p->height, $p->weight, $p->price, $p->regular_price, $p->sale_price, $p->sku, $p->stock, $p->downloadable, $p->virtual, $p->sold_individually, $p->tax_status, $p->tax_class, $p->stock_status, $p->backorders, $p->featured, $p->visibility);
	}
	$msg = str_replace($re, $subst, $pattern);
	# Applying Telegram markdown format (bold, italic, inline-url)
	if (get_option('twp_markdown') == 1){
		$msg = $nt->markdown($msg, 1, 1, 1 );
	}
	//Emoji
	//$msg = html_entity_decode(preg_replace("/U\+([0-9A-F]{5})/", "&#x\\1;", $msg), ENT_NOQUOTES, 'UTF-8');

	if ($method == 'photo' && $photo != false ) {
		$r = $nt->channel_photo($ch_name, $msg, $photo);
	} else {
		$r = $nt->channel_text($ch_name, $msg);
	}
	$publish_date = current_time( "mysql", $gmt = 0 );
	if ($r["ok"] == true){
		$sending_result = __('Published succesfully on ', 'twp-plugin');
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
			$nt->_telegram($_POST['bot_token']);
			$result = $nt->channel_text($_POST['channel_username'], $_POST['msg']);
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