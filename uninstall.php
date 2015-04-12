<?php
/*
ScrapeBreaker - uninstall.php
Version: 1.3.3

This script uninstalls ScrapeBreaker and removes all options and traces of its existence.
*/

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); }

function rssb_uninstall_plugin() {
	// Options to Delete
	$rssb_option_names = array( 'scrapebreaker_version', 'rssb_options', 'rssb_admin_notices' );
	foreach( $rssb_option_names as $i => $rssb_option ) {
		delete_option( $rssb_option );
		}
	}

rssb_uninstall_plugin();

?>