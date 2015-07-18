<?php
/*
ScrapeBreaker - uninstall.php
Version: 1.3.6

This script uninstalls ScrapeBreaker and removes all options and traces of its existence.
*/

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); }

function rssb_uninstall_plugin() {
	/* Delete Options */
	$rssb_option_names = array( 'scrapebreaker_version', 'rssb_options', 'rssb_admin_notices' );
	foreach( $rssb_option_names as $i => $rssb_option ) { delete_option( $rssb_option ); }
	/* Delete User Meta */
	$del_user_meta = array( 'rssb_nag_status', 'rssb_nag_notices' );
	$user_ids = get_users( array( 'blog_id' => '', 'fields' => 'ID' ) );
	foreach ( $user_ids as $user_id ) { foreach( $del_user_meta as $i => $key ) { delete_user_meta( $user_id, $key ); } }
	}

rssb_uninstall_plugin();

?>