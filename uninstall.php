<?php
global $wpdb;

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_name = 'exchange-rate';

delete_option($option_name);

// for site options in Multisite
delete_site_option($option_name);

$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}iso_wallet`;" );