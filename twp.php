<?php
/**
 * @package Telegram for Wordpress
 * @version 1.4.1
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
require_once("functions.php");
$twp_settings = 
// create custom plugin settings menu
add_action('admin_menu', 'twp_create_menu');
function twp_create_menu() {
 //create new top-level menu
 add_menu_page('TWP Plugin Settings', __('TWP Settings','twp-plugin'), 'administrator', __FILE__, 'twp_settings_page',plugins_url('icon.png', __FILE__));

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

// If api_token has been set, then add our hook to phpmailer, else show a warning message.
if (get_option('twp_api_token') != null ) {
    add_action( 'phpmailer_init', 'twp_phpmailer_hook' );
} else {
    add_action( 'admin_notices', 'twp_api_admin_notice' ); 
}

?>