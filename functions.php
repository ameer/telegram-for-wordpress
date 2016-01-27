<?php
/**
* Print admin settings page
*/
function twp_settings_page() {
require_once("admin/settings-page.php");
}

/**
* Print admin subpage
*/
function twp_broadcast_page_callback() {
    require_once("broadcast.php");
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
    // Add a nonce field so we can check for it later.
    $ID = $post->ID;
	wp_nonce_field( 'twp_save_meta_box_data', 'twp_meta_box_nonce' );
	$error = "";
	$dis = "";
	$check_state = "";
	if (get_option("twp_channel_username") == "" || get_option('twp_bot_token') == "")
		{
			$dis = "disabled=disabled"; 
			$error = "<span style='color:red;font-weight:700;'>".__("Bot token or Channel username aren't set!", "twp-plugin")."</span><br>";
		}
	$twp_meta_data = get_post_meta($ID, '_twp_meta_data', true);
	if($twp_meta_data != "" || $twp_meta_data != null) {
		$check_state = "checked=\'checked\'";
	}
	echo 
	'<div style="padding-top: 7px;">'
	.$error.
	'<input type="checkbox" id="twp_send_to_channel" name="twp_send_to_channel"  value="1" '.$check_state.' '.$dis.'/><label for="twp_send_to_channel">'.__('Send to Telegram Channel', 'twp-plugin' ).'</label>
	<br>
	<fieldset id="twp_fieldset" style="margin: 10px 20px;line-height: 2em;" disabled="disabled">
		<textarea id="send_pattern" name="send_pattern" cols="40" rows="8" style="resize:vertical">'.$twp_meta_data.'</textarea>
		<br>
	</fieldset>
	<hr>
	<p>'.__("Sending result: ", "twp-plugin").'</p><span id="twp_last_publish" style="font-weight:700">'.print_r($category).'</span>
</div>
<script>
	jQuery("#twp_send_to_channel").click(function() {
		if (jQuery("#twp_fieldset").prop("disabled") == false){
			jQuery("#twp_fieldset").prop("disabled", true);
			jQuery("#twp_fieldset label").css("color", "grey")
		}
		else {
			jQuery("#twp_fieldset").prop("disabled", false);
			jQuery("#twp_fieldset label").css("color", "black")
		}
	});
</script>';
}

/**
* When the post is saved, saves our custom data.
*
* @param int $post_id The ID of the post being saved.
*/
function twp_save_meta_box_data( $post_id ) {
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
    if ( false !== wp_is_post_revision( $post_id ) ){
    	return;
    }
    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

    	if ( ! current_user_can( 'edit_page', $post_id ) ) {
    		return;
    	}

    } else {

    	if ( ! current_user_can( 'edit_post', $post_id ) ) {
    		return;
    	}
    }
    /* OK, it's safe for us to save the data now. */
    // Make sure that it is set.
    if ( ! isset( $_POST['twp_send_to_channel'] ) ) {
    	return;
    }
    if ($_POST['twp_send_to_channel'] == 1) {
    	$twp_meta_data = $_POST['send_pattern'];
    	update_post_meta( $post_id, '_twp_meta_data', $twp_meta_data);
    } else {

    }
}
add_action( 'save_post', 'twp_save_meta_box_data' );

/**
* When the post is published, send the messages.
* @param int $ID
* @param obj $post
*/
function twp_post_published ( $ID, $post ) {
	# $a stands for array
	if (! isset($_POST['twp_send_to_channel']) ) {
		$a = get_post_meta($ID, '_twp_meta_data', true);
	} else {
		$a = $_POST['send_pattern'];
	}
	# If there is no pattern then return!
	if ($a == ""){
		return;
	}
	# Initialize Telegram information
	$ch_name = get_option('twp_channel_username');
	$token = get_option('twp_bot_token');
	if ($token == "" || $ch_name == ""){
		update_post_meta( $ID, '_twp_meta_data', __('Bot token or Channel username aren\'t set!', 'twp-plugin') );
		return;
	}

	$tags = wp_get_post_tags( $ID, array( 'fields' => 'names' ) );
	$categories = wp_get_post_categories($ID, array( 'fields' => 'names' ));
	$nt = new Notifcaster_Class();
	$nt->_telegram($token);
	# Preparing message for sending
	$method = "photo";
	$photo =  get_attached_file( get_post_thumbnail_id($ID));
	
	$re = array("/({title})/iu","/({excerpt})/iu","/({content})/iu","/({author})/iu","/({short_url})/iu","/({full_url})/iu","/{(tags\\|(.))}/iu","/{(categories\\|(.))}/iu");
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
	$msg = preg_replace($re, $subst, $a);
	// switch ($a['send_type']) {
	// 	case '1':
	// 	$msg = $post->post_title;
	// 	break;
	// 	case '2':
	// 	$msg = $post->post_excerpt;
	// 	break;
	// 	case '3':
	// 	$msg = $post->post_content;
	// 	$method = "text";
	// 	break;
	// 	default:
	// 	break;
	// }
	// if ($a['url_type'] == "1") {
	// 	$msg .= "\n".wp_get_shortlink($ID);
	// } else if ($a['url_type'] == 2) {
	// 	$msg .= "\n".get_permalink($ID);
	// }
	// if (get_option("twp_channel_signature") == 1 ) {
	// 	$msg .= "\n".$ch_name;
	// }
	# Applying Telegram markdown format (bold, italic, inline-url)
	$msg = $nt->markdown($msg, get_option('twp_markdown_bold'), get_option('twp_markdown_italic'), get_option('twp_markdown_inline_url') );
	
	if ($method == "photo" && $photo != false ) {
		$r = $nt->channel_photo($ch_name, $msg, $photo);
	} else {
		$r = $nt->channel_text($ch_name, $msg);
		$publish_date = current_time( "mysql", $gmt = 0 );
	}
	if ($r["ok"] == true){
		$publish_date = current_time( "mysql", $gmt = 0 );
		update_post_meta( $ID, '_twp_meta_data', __('Published succesfully on ', 'twp-plugin').$publish_date );
	} else {
		update_post_meta( $ID, '_twp_meta_data', $r["description"] );  
	}
	$_POST['twp_send_to_channel'] = 0;
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