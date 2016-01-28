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
if ( ! defined( 'TWP_PLUGIN_URL' ) ){
    define( 'TWP_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
}
if ( ! defined( 'TWP_PLUGIN_DIR' ) ){
    define( 'TWP_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
}
require_once(TWP_PLUGIN_DIR."/inc/Notifcaster.class.php");
require_once("functions.php");
$twp_settings = 
// create custom plugin settings menu
add_action('admin_menu', 'twp_create_menu');
function twp_create_menu() {
 //create new top-level menu
    add_menu_page('TWP Plugin Settings', __('TWP Settings','twp-plugin'), 'administrator', 'telegram-for-wp', 'twp_settings_page',plugins_url('icon.png', __FILE__));
//create sub-menu
   add_submenu_page( __FILE__, __('Broadcast','twp-plugin'), __('Broadcast','twp-plugin'), 'manage_options', "/broadcast.php", 'twp_broadcast_page_callback' );
 //call register settings function
   add_action( 'admin_init', 'register_twp_settings' );
}
function register_twp_settings() {
            //register our settings
            register_setting( 'twp-settings-group', 'twp_api_token' , 'sanitize_text_field');
            register_setting( 'twp-settings-group', 'twp_bot_token', 'sanitize_text_field');
            register_setting( 'twp-settings-group', 'twp_channel_username', 'sanitize_text_field' );
            register_setting( 'twp-settings-group', 'twp_channel_pattern', 'twp_sanitize_text_field' );
            register_setting( 'twp-settings-group', 'twp_hashtag', 'sanitize_text_field' );
            register_setting( 'twp-settings-group', 'twp_markdown', 'sanitize_text_field' );
}

// If api_token has been set, then add our hook to phpmailer.
if (get_option('twp_api_token') != null ) {
    add_action( 'phpmailer_init', 'twp_phpmailer_hook' );
}

?>