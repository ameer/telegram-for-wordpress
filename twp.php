<?php
/**
 * @package Telegram for Wordpress
 * @version 1.4
 */
/*
Plugin Name: Telegram for WordPress
Description: Receive your WordPress site notifications in your Telegram account and publish your posts to Telegram channel.
Author: Ameer Mousavi
Version: 1.4
Author URI: http://ameer.ir/
Plugin URI: http://notifcaster.com
License: GPLv2 or later.
Text Domain: twp-plugin
Domain Path: /lang
*/
require_once("Notifcaster.class.php");
$twp_settings = 
// create custom plugin settings menu
add_action('admin_menu', 'twp_create_menu');
function twp_create_menu() {
 //create new top-level menu
 add_menu_page('TWP Plugin Settings', __('TWP Settings','twp-plugin'), 'administrator', __FILE__, 'twp_settings_page',plugins_url('icon.png', __FILE__));
 //create sub-menu
 add_submenu_page( __FILE__, __('Broadcast','twp-plugin'), __('Broadcast','twp-plugin'), 'manage_options', "/broadcast.php", 'twp_broadcast_page_callback' );
 //call register settings function
 add_action( 'admin_init', 'register_twp_settings' );
}
function register_twp_settings() {
            //register our settings
            register_setting( 'twp-settings-group', 'twp_api_token' );
            register_setting( 'twp-settings-group', 'twp_bot_token' );
            register_setting( 'twp-settings-group', 'twp_channel_username' );
            register_setting( 'twp-settings-group', 'twp_hashtag' );
            register_setting( 'twp-settings-group', 'twp_channel_signature' );
}

function twp_broadcast_page_callback() {
    require_once("broadcast.php");
}

function twp_settings_page() {
?>
<style type="text/css">
    #twp-wrap h1, h2, h3, h4, h5, h6 {line-height: normal;}
    #twp-wrap .howto span {color:#6495ED;font-weight:700;font-style: normal;}
    #twp-wrap input, button {vertical-align: middle !important;}
    #twp-wrap a {text-decoration: none !important; border-bottom: 1px solid #0091CD;padding-bottom: 2px;}
    #twp-wrap code {padding: 2px 4px; font-size: 90%; color: #c7254e; background-color: #f9f2f4; border-radius: 4px;font-style: normal;}
    #twp-wrap input[type=text] {font-size: 1.5em; font-family: monospace; font-weight: 300; }
    #twp-wrap table {width: 100%}
    #twp-wrap tr, #twp-wrap th,#twp-wrap td {vertical-align: baseline !important;}
</style>
<div id="twp-wrap" class="wrap">
    <h1><?php  echo __("Telegram for WordPress", "twp-plugin") ?></h1>
    <p> <?php printf(__("Join our channel in Telegram: %s", "twp-plugin"), "<a href='https://telegram.me/notifcaster'>@notifcaster</a>"); ?> </p>
    <hr>
    <form method="post" action="options.php" id="twp_form">
        <?php settings_fields( 'twp-settings-group' ); ?>
        <h2> <?php echo __("Notifications", "twp-plugin") ?> </h2>
        <p style="font-size:14px;">
                <?php echo __("You will receive messages in Telegram with the contents of every emails that sent from your WordPress site.<br>
                For example, once a new comment has been submitted, you will receive the comment in your Telegram account.<br>
                ", "twp-plugin");
                ?>
                </p>
        <table class="form-table">
            <tr>
                <th scope="row"><h3><?php echo __("Instructions", "twp-plugin") ?></h3></th>
                <td><br>
                <p>
                <strong><?php echo __("If you want to send notifications to single user:", "twp-plugin") ?></strong><br>
                <ol>
                <li><?php printf(__("In Telegram app start a new chat with %s.", "twp-plugin"), "<a href='https://Telegram.me/notifcaster_bot' target='_blank' style='text-decoration:none !important'>Notifcaster_Bot</a>"); ?></li>
                <li><?php echo __("Send <code>/token</code> command and the bot will give you an API token for the user.", "twp-plugin") ?></li>
                <li><?php echo __("Copy and paste it in the below field and hit the save button!", "twp-plugin") ?></li> 
                <li><?php echo __("Kaboom! You are ready to go.", "twp-plugin") ?></li>
                </ol>
                </p>
                <p><strong><?php echo __("If you want to send notifications to group:", "twp-plugin") ?></strong><br>
                <ol>
                <li><?php echo __("Add the bot to your group and bot will give you an API token for the group( token must be started with <code>g:</code> ) ", "twp-plugin") ?></li>
                <li><?php echo __("Copy and paste it in the below field and hit the save button!", "twp-plugin") ?></li>
                </ol>
                </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><h3>API Token</h3></th>
                <td>
                    <input id="twp_api_token" type="text" name="twp_api_token" maxlength="34" size="32" value="<?php echo get_option('twp_api_token'); ?>" dir="auto"/>
                </td>
            </tr>
            <tr>
                <th scope="row"><h3><?php  echo __("Send a test Message", "twp-plugin") ?></h3></th>
                <td><button id="sendbtn" type="button" class="button-primary" onclick="sendTest();"> <?php  echo __("Send now!", "twp-plugin") ?> </button></td>
            </tr>
            <tr>
                <th scope="row"><h3><?php  echo __("Hashtag (optional)", "twp-plugin") ?></h3></th>
                <td><input id="twp_hashtag" type="text" name="twp_hashtag" size="32" value="<?php echo get_option('twp_hashtag'); ?>" dir="auto" />
                <p class="howto">
                <?php echo __("Insert a custom hashtag at the beginning of the messages.", "twp-plugin") ?><br>
                <?php echo __("Don't forget <code>#</code> at the beginning", "twp-plugin") ?>
                </p>
                </td>
            </tr>
        </table>
        <hr>
        <h2> <?php echo __("Post to Channel", "twp-plugin") ?> </h2>
        <table class="form-table">
         <tr>
         <th scope="row"><h3><?php  echo __("Introduction", "twp-plugin") ?></h3></th>
                <td>
                    <p style="font-weight:700;font-size: 16px;">
                    <?php echo __("Telegram channel is a great way for attracting people to your site.<br> This option allows you to send posts to your Telegram channel. Intresting, no?<br>
                     So let's start!<br>", "twp-plugin") ?> 
                    </p>
                    <ol>
                        <li><?php echo __("Create a channel (if you don't already have one).", "twp-plugin") ?></li>
                        <li><?php echo __("Create a bot (if you don't already have one).", "twp-plugin") ?></li>
                        <li><?php echo __("Go to channel options and select 'Administrator' option.", "twp-plugin") ?></li>
                        <li><?php echo __("Select 'Add Administrator' option.", "twp-plugin") ?></li>
                        <li><?php echo __("Search the username of your bot and add it as administrator.", "twp-plugin") ?></li>
                        <li><?php echo __("Copy the bot token (you got it in step two) and paste it in the below field.", "twp-plugin") ?></li>
                        <li><?php echo __("Enter the username of the channel and hit SAVE button!!!", "twp-plugin") ?></li>
                        <li><?php echo __("Yes! Now, whenever you publish or update a post you can choose whether send it to Telegram (from post editor page)", "twp-plugin") ?></li>
                    </ol>
                </td>
            </tr>
            <tr>
                <th scope="row"><h3>Bot Token</h3></th>
                <td>
                    <input id="twp_bot_token" type="text" name="twp_bot_token" size="32" value="<?php echo get_option('twp_bot_token'); ?>" dir="auto" />
                    <button id="checkbot" class="button-secondary" type="button" onclick="botTest()"><?php echo __("Check bot token", "twp-plugin") ?></button>
                    <p class="howto">
                    <?php echo __("Bot Info: ", "twp-plugin") ?>
                    <span id="bot_name"></span>
                    </p>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><h3><?php echo __("Channel Username", "twp-plugin") ?></h3></th>
                <td>
                    <input id="twp_channel_username" type="text" name="twp_channel_username" size="32" value="<?php echo get_option('twp_channel_username'); ?>" dir="auto" />
                    <button id="channelbtn" type="button" class="button-secondary" onclick="channelTest();"> <?php  echo __("Send now!", "twp-plugin") ?></button>
                    <p class="howto"><?php echo __("Don't forget <code>@</code> at the beginning", "twp-plugin") ?></p>                
                </td>
            </tr>
            <tr>
                <th scope="row"><h3><?php echo __("Channel Signature", "twp-plugin") ?></h3></th>
                <td>
                    <input id="twp_channel_signature" type="checkbox" name="twp_channel_signature"  value="1" <?php checked( '1', get_option( 'twp_channel_signature' ) ); ?> /><?php echo __("Add channel username at the end of the messages", "twp-plugin") ?>
                    <p class="howto"><?php echo __("When people forwards your messages, this signature helps others to join your channel faster.", "twp-plugin") ?></p>                
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    <div id="support">
    <?php  
    $message = sprintf (__('If you like this plugin, please rate it in %1$s. You can also support us by %2$s', "twp-plugin"), '<a href="https://wordpress.org/plugins/telegram-for-wp" target="_blank">'.__('Telegram for Wordpress page in wordpress.org', "twp-plugin").'</a>', '<a href="https://notifcaster.com/donate" target="_blank" title="donate">'.__("donating", "twp-plugin").'</a>' );
    echo $message; ?>
    </div>
</div>
<script type="text/javascript">
    function sendTest() {
        var api_token = jQuery('input[name=twp_api_token]').val(), h = '';
        if(api_token != '' ) {
            jQuery('#sendbtn').prop('disabled', true);
            jQuery('#sendbtn').text('<?php echo __("Please wait...", "twp-plugin") ?> ');
            if(jQuery("#twp_hashtag").val() != ''){
                var h = '<?php  echo get_option("twp_hashtag"); ?>';
            }
        var msg = h +'\n'+'<?php echo __("This is a test message", "twp-plugin") ?>';
        jQuery.post('<?php echo plugins_url( "test.php", __FILE__ ) ?>', 
        { 
            msg: msg , api_token: api_token, subject: 'm'
        }, function( data ) {
            alert(data.description);
            jQuery('#sendbtn').prop('disabled', false);
            jQuery('#sendbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>');
        }, 
         'json'); 
        } else {
            alert(' <?php  echo __("api_token field is empty", "twp-plugin") ?>') 
        }
    };
    function channelTest() {
            var bot_token = jQuery('input[name=twp_bot_token]').val(), channel_username = jQuery('input[name=twp_channel_username]').val(), h = '';
            if(bot_token != '' && channel_username != '' ) {
            var c = confirm('<?php echo __("This will send a test message to your channel. Do you want to continue?", "twp-plugin") ?>');
                       if( c == true ){ 
                        jQuery('#channelbtn').prop('disabled', true);
                        jQuery('#channelbtn').text('<?php echo __("Please wait...", "twp-plugin") ?> '); 
                        if(jQuery('#twp_hashtag').val() != ''){
                            var h = '<?php  echo get_option("twp_hashtag"); ?>';
                        }
                        var msg = h +'\n'+'<?php echo __("This is a test message", "twp-plugin") ?>';

                        jQuery.post('<?php echo plugins_url( "test.php", __FILE__ ) ?>', 
                        { 
                            channel_username: channel_username, msg: msg , bot_token: bot_token, subject: 'c'
                        }, function( data ) {
                            jQuery('#channelbtn').prop('disabled', false);
                            jQuery('#channelbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>'); 
                            alert((data.ok == true ? 'The message sent succesfully.' : data.description))}, 'json');
                    }
                } else {
                    alert(' <?php  echo __("bot token/channel username field is empty", "twp-plugin") ?>') 
                }
    }
    function botTest() {
       if(jQuery('input[name=twp_bot_token]').val() != '' ) {
            var bot_token = jQuery('input[name=twp_bot_token]').val();
            jQuery('#checkbot').prop('disabled', true);
            jQuery('#checkbot').text('<?php echo __("Please wait...", "twp-plugin") ?> ');
            jQuery.post('<?php echo plugins_url( "test.php", __FILE__ ) ?>', 
            { 
                bot_token: bot_token, subject: 'b'
            }, function( data ) {
                if (data != undefined && data.ok != false){
                    jQuery('#bot_name').text(data.result.first_name + ' ' + (data.result.last_name == undefined ? ' ' :  data.result.last_name ) + '(@' + data.result.username + ')')
                }else {
                    jQuery('#bot_name').text(data.description)
                }
                jQuery('#checkbot').prop('disabled', false);
                jQuery('#checkbot').text('<?php echo __("Check bot token", "twp-plugin") ?>');
            }, 'json'); 
        } else {
            alert(' <?php  echo __("bot token field is empty", "twp-plugin") ?>') 
        }
    }
</script>
<?php }
    add_action( 'plugins_loaded', 'twp_load_textdomain' );
    /**
     * Load plugin textdomain.
     *
     * @since 1.0.0
     */
    function twp_load_textdomain() {
      load_plugin_textdomain( 'twp-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
    }
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

 // Checks if TOKEN and API has been set. If not, show a warning message.
    if (get_option('twp_api_token') != null ) {
    //This will get information about sent mail from PHPMailer and send it to user
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
    add_action( 'phpmailer_init', 'twp_phpmailer_hook' );
    
    } else {
        function twp_api_error_notice() {
        $class = "error";
        $message = sprintf(__('Your API token isn\'t set. Please go to %s and set it.','twp-plugin'), "<a href='".admin_url('admin.php?page=telegram-for-wp/twp.php')."'>".__("TWP Settings", "twp-plugin")."</a>");
            echo"<div class=\"$class\"> <p>$message</p></div>"; 
        }
    add_action( 'admin_notices', 'twp_api_error_notice' ); 
    }
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
                    "side",
                    "high"
                    );
            }
        }
        add_action( 'add_meta_boxes', 'twp_add_meta_box' );
        /**
        * Prints the box content.
        * 
        * @param WP_Post $post The object for the current post/page.
        */
        function twp_meta_box_callback( $post ) {

        // Add a nonce field so we can check for it later.
            wp_nonce_field( 'twp_save_meta_box_data', 'twp_meta_box_nonce' );
            $error = "";
            if (get_option("twp_channel_username") == "" || get_option('twp_bot_token') == ""){$dis = "disabled=disabled"; $error = "<span style='color:red;font-weight:700;'>".__("Bot token or Channel username aren't set!", "twp-plugin")."</span><br>";}
            echo 
            '<div style="padding-top: 7px;">'
            .$error.
            '<input type="checkbox" id="twp_send_to_channel" name="twp_send_to_channel"  value="1" '.$dis.'/><label for="twp_send_to_channel">'.__('Send to Telegram Channel', 'twp-plugin' ).'</label>
            <br>
            <fieldset id="twp_fieldset" style="margin: 10px 20px;line-height: 2em;" disabled="disabled">
            <input id="send_type_featured" type="radio" name="send_type" value="1" checked="checked">
            <label style="color:grey" for="send_type_content">'.__('Title + Featured Image', 'twp-plugin').'</label>
            <br>
            <input id="send_type_excerpt" type="radio" name="send_type" value="2">
            <label style="color:grey" for="send_type_excerpt">'.__('Excerpt + Featured Image', 'twp-plugin').'</label>
            <br>
            <input id="send_type_content" type="radio" name="send_type" value="3">
            <label style="color:grey" for="send_type_content">'.__('Text without image(s)', 'twp-plugin').'</label>
            <hr>
            <input id="url_type_short" type="radio" name="url_type" value="1">
            <label style="color:grey" for="url_type_short">'.__('Add short url at the end', 'twp-plugin').'</label>
            <br>
            <input id="url_type_long" type="radio" name="url_type" value="2">
            <label style="color:grey" for="url_type_long">'.__('Add full url at the end', 'twp-plugin').'</label>
            </fieldset>
            <hr>
            <p>'.__("Sending result: ", "twp-plugin").'</p><span id="twp_last_publish" style="font-weight:700">'.get_post_meta( $post->ID, '_twp_meta_value_date', true ).'</span>
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

        /* OK, it's safe for us to send message now. */
        // Make sure that it is set.
        if ( ! isset( $_POST['twp_send_to_channel'] ) ) {
            return;
        }

        if ($_POST['twp_send_to_channel'] == 1) {
            $method = "photo";
            $ch_name = get_option('twp_channel_username');
            $token = get_option('twp_bot_token');
            $post_obj = get_post($post_id);
            $photo =  get_attached_file( get_post_thumbnail_id($post->ID));
            if ($token == "" || $ch_name == ""){
                update_post_meta( $post_id, '_twp_meta_value_date', __("Bot token or Channel username aren't set!", "twp-plugin") );
                return;
            }
            switch ($_POST['send_type']) {
                case '1':
                    $msg = $post_obj->post_title;
                    break;
                case '2':
                    $msg = $post_obj->post_excerpt;
                    break;
                case '3':
                    $msg = $post_obj->post_content;
                    $method = "text";
                    break;
                default:
                    break;
            }
            if ($_POST['url_type'] == 1) {
                $msg .= "\n".wp_get_shortlink($post_id);
            } else if ($_POST['url_type'] == 2) {
                $msg .= "\n".get_permalink($post_id);
            }
            if (get_option("twp_channel_signature") == 1 ) {
                $msg .= "\n".$ch_name;
            }
            $nt = new Notifcaster_Class();
            $nt->_telegram($token);
                if ($method == "photo" && $photo != false ) {
                    $r = $nt->channel_photo($ch_name, $msg, $photo);
                } else {
                    $r = $nt->channel_text($ch_name, $msg);
                }
            if ($r["ok"] == true){
            $publish_date = current_time( "mysql", $gmt = 0 );
            update_post_meta( $post_id, '_twp_meta_value_date', $publish_date );
            } else {
            update_post_meta( $post_id, '_twp_meta_value_date', $r["description"] );  
            }
        }
    }
    add_action( 'save_post', 'twp_save_meta_box_data' );
?>