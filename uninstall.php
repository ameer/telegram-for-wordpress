<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
//drop our twp_log table and delete all of our options from WordPress options table.
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}twp_logs" );
$wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name = 'twp_%'" );