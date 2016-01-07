<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! is_admin() ) die;
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