<?php
/*
Plugin Name: ScrapeBreaker
Plugin URI: http://www.redsandmarketing.com/plugins/scrapebreaker/
Description: A combination of frame-breaker and scraper protection. Protect your website content from both frames and server-side scraping techniques. If either happens, visitors will be redirected to the original content.
Author: Scott Allen
Version: 1.3.6
Author URI: http://www.redsandmarketing.com/
Text Domain: scrapebreaker
License: GPLv2
*/

/*  Copyright 2014-2015    Scott Allen  (email : plugins [at] redsandmarketing [dot] com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* PLUGIN - BEGIN */

/* Note to any other PHP developers reading this:
My use of the closing curly braces "}" is a little funky in that I indent them, I know. IMO it's easier to debug. Just know that it's on purpose even though it's not standard. One of my programming quirks, and just how I roll. :)
*/

/* Make sure plugin remains secure if called directly */
if ( !defined( 'ABSPATH' ) ) {
	if ( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
	die( 'ERROR: This plugin requires WordPress and will not function if called directly.' );
	}

define( 'RSSB_VERSION', '1.3.6' );
define( 'RSSB_REQUIRED_WP_VERSION', '3.8' );
//define( 'RSSB_REQUIRED_PHP_VERSION', '5.3' ); /* Implement in future version */

if ( !defined( 'RSSB_DEBUG' ) ) 				{ define( 'RSSB_DEBUG', FALSE ); } 		// Do not change value unless developer asks you to - for debugging only. Change in wp-config.php.
if ( !defined( 'RSSB_OVERRIDE' ) ) 				{ define( 'RSSB_OVERRIDE', FALSE ); } 	// To improve speed by eliminating DB calls. Enables overriding the DB options. Change in wp-config.php.
if ( !defined( 'RSSB_JS_ONLY' ) ) 				{ define( 'RSSB_JS_ONLY', FALSE ); } 	// To improve speed by eliminating DB calls. Sets option to only use JS Frame Breaker & not use X-Frames-Options Change in wp-config.php.
if ( !defined( 'RSSB_PLUGIN_BASENAME' ) ) 		{ define( 'RSSB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
if ( !defined( 'RSSB_PLUGIN_FILE_BASENAME' ) ) 	{ define( 'RSSB_PLUGIN_FILE_BASENAME', trim( basename( __FILE__ ), '/' ) ); }
if ( !defined( 'RSSB_PLUGIN_NAME' ) ) 			{ define( 'RSSB_PLUGIN_NAME', trim( dirname( RSSB_PLUGIN_BASENAME ), '/' ) ); }
/* Constants prefixed with 'RSMP_' are shared with other RSM Plugins for efficiency. */
if ( !defined( 'RSMP_SITE_URL' ) ) 				{ define( 'RSMP_SITE_URL', untrailingslashit( site_url() ) ); }
if ( !defined( 'RSMP_SITE_DOMAIN' ) ) 			{ define( 'RSMP_SITE_DOMAIN', rssb_get_domain( RSMP_SITE_URL ) ); }
if ( !defined( 'RSSB_SERVER_ADDR' ) ) 			{ define( 'RSSB_SERVER_ADDR', rssb_get_server_addr() ); }
if ( !defined( 'RSSB_SERVER_NAME' ) ) 			{ define( 'RSSB_SERVER_NAME', rssb_get_server_name() ); }
if ( !defined( 'RSSB_SERVER_NAME_REV' ) ) 		{ define( 'RSSB_SERVER_NAME_REV', strrev( RSSB_SERVER_NAME ) ); }
if ( !defined( 'RSMP_DEBUG_SERVER_NAME' ) ) 	{ define( 'RSMP_DEBUG_SERVER_NAME', '.redsandmarketing.com' ); }
if ( !defined( 'RSMP_DEBUG_SERVER_NAME_REV' ) )	{ define( 'RSMP_DEBUG_SERVER_NAME_REV', strrev( RSMP_DEBUG_SERVER_NAME ) ); }
if ( !defined( 'RSMP_RSM_URL' ) ) 				{ define( 'RSMP_RSM_URL', 'http://www.redsandmarketing.com/' ); }
if ( !defined( 'RSSB_HOME_URL' ) ) 				{ define( 'RSSB_HOME_URL', RSMP_RSM_URL.'plugins/'.RSSB_PLUGIN_NAME.'/' ); }
if ( !defined( 'RSSB_SUPPORT_URL' ) ) 			{ define( 'RSSB_SUPPORT_URL', RSMP_RSM_URL.'plugins/wordpress-plugin-support/?plugin='.RSSB_PLUGIN_NAME.'/' ); }
if ( !defined( 'RSSB_WP_URL' ) ) 				{ define( 'RSSB_WP_URL', 'https://wordpress.org/extend/plugins/'.RSSB_PLUGIN_NAME.'/' ); }
if ( !defined( 'RSSB_WP_RATING_URL' ) ) 		{ define( 'RSSB_WP_RATING_URL', 'https://wordpress.org/support/view/plugin-reviews/'.RSSB_PLUGIN_NAME ); }
if ( !defined( 'RSSB_DONATE_URL' ) ) 			{ define( 'RSSB_DONATE_URL', 'http://bit.ly/'.RSSB_PLUGIN_NAME.'-donate' ); }
if ( !defined( 'RSSB_PHP_VERSION' ) ) 			{ define( 'RSSB_PHP_VERSION', PHP_VERSION ); }
if ( !defined( 'RSSB_WP_VERSION' ) ) 			{ global $wp_version; define( 'RSSB_WP_VERSION', $wp_version ); }

add_action( 'send_headers', 'rssb_add_headers' );
add_action( 'wp_head', 'rssb_scrapebreaker', -10 );

function rssb_add_headers() {
	/* Check options */
	if ( TRUE === RSSB_OVERRIDE ) { $rssb_js_only = TRUE === RSSB_JS_ONLY ? 1 : 0; }
	else {
		global $rssb_options;
		if ( empty( $rssb_options ) ) { $rssb_options = get_option('rssb_options'); }
		$rssb_js_only = $rssb_options['use_js_frame_breaker_only'];
		}
	if ( empty( $rssb_js_only ) ) {
		@header( 'X-Frame-Options: SAMEORIGIN' );
		/* Also WP Function: send_frame_options_header() */
		}
	}
function rssb_scrapebreaker() {
	$rssb_this_page_url = rssb_get_url();
	$rssb_activated = rssb_is_activated();
	if ( $rssb_activated=='yes' ) {
		global $rssb_ao_active; $ao_noop_open = $ao_noop_close = '';
		if ( empty( $rssb_ao_active ) ) { $rssb_ao_active = rssb_is_plugin_active( 'autoptimize/autoptimize.php' ); }
		if ( !empty( $rssb_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
		echo PHP_EOL.$ao_noop_open."<script type=\"text/javascript\" >".PHP_EOL."/* <![CDATA[ */".PHP_EOL."function strpos(haystack,needle,offset){var i=(haystack+'').indexOf(needle,(offset||0));return i===-1?false:i;}".PHP_EOL."var thispage = \"".$rssb_this_page_url."\";".PHP_EOL."if (strpos(top.location,thispage)!==0||window!=top){top.location.href=thispage;window.open(thispage,'_top');}".PHP_EOL."/* ]]> */".PHP_EOL."</script>".$ao_noop_close." ".PHP_EOL;
		}
	}
function rssb_is_activated() {
	$rssb_activated = 'no';
	if ( !is_admin() && !is_user_logged_in() && !is_preview() ){
		/* Not active when in Admin or logged in on rest of site. No reason for logged in users to be blocked. Also not active on previews. */
		$rssb_activated = 'yes';
		}
	return $rssb_activated;
	}

/* Standard Functions - BEGIN */
function rssb_casetrans( $type, $string ) {
	/***
	* Convert case using multibyte version if available, if not, use defaults
	* Added 1.8.4
	***/
	switch ($type) {
		case 'upper':
			if ( function_exists( 'mb_strtoupper' ) ) { return mb_strtoupper($string, 'UTF-8'); } else { return strtoupper($string); }
		case 'lower':
			if ( function_exists( 'mb_strtolower' ) ) { return mb_strtolower($string, 'UTF-8'); } else { return strtolower($string); }
		case 'ucfirst':
			if ( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) ) { return mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, NULL, 'UTF-8'); } else { return ucfirst($string); }
		case 'ucwords':
			if ( function_exists( 'mb_convert_case' ) ) { return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8'); } else { return ucwords($string); }
			/***
			* Note differences in results between ucwords() and this. 
			* ucwords() will capitalize first characters without altering other characters, whereas this will lowercase everything, but capitalize the first character of each word.
			* This works better for our purposes, but be aware of differences.
			***/
		default:
			return $string;
		}
	}
function rssb_get_server_addr() {
	if ( !empty( $_SERVER['SERVER_ADDR'] ) ) { $server_addr = $_SERVER['SERVER_ADDR']; } else { $server_addr = getenv('SERVER_ADDR'); }
	if ( empty( $server_addr ) ) { $server_addr = ''; }
	return $server_addr;
	}
function rssb_get_server_name() {
	$rssb_site_domain	= $server_name = RSMP_SITE_DOMAIN;
	$rssb_env_http_host	= getenv('HTTP_HOST');
	$rssb_env_srvr_name	= getenv('SERVER_NAME');
	if 		( !empty( $_SERVER['HTTP_HOST'] ) 	&& strpos( $rssb_site_domain, $_SERVER['HTTP_HOST'] ) 	!== FALSE ) { $server_name = $_SERVER['HTTP_HOST']; }
	elseif 	( !empty( $rssb_env_http_host ) 	&& strpos( $rssb_site_domain, $rssb_env_http_host ) 	!== FALSE ) { $server_name = $rssb_env_http_host; }
	elseif 	( !empty( $_SERVER['SERVER_NAME'] ) && strpos( $rssb_site_domain, $_SERVER['SERVER_NAME'] ) !== FALSE ) { $server_name = $_SERVER['SERVER_NAME']; }
	elseif 	( !empty( $rssb_env_srvr_name ) 	&& strpos( $rssb_site_domain, $rssb_env_srvr_name ) 	!== FALSE ) { $server_name = $rssb_env_srvr_name; }
	return rssb_casetrans( 'lower', $server_name );
	}
function rssb_get_url() {
	$url  = rssb_is_https() ? 'https://' : 'http://';
	$url .= RSSB_SERVER_NAME.$_SERVER['REQUEST_URI'];
	return $url;
	}
function rssb_is_https() {
	if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) { return TRUE; }
	return FALSE;
	}
function rssb_get_domain($url) {
	// Get domain from URL
	// Filter URLs with nothing after http
	if ( empty( $url ) || preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	// Fix poorly formed URLs so as not to throw errors when parsing
	$url = rssb_fix_url($url);
	// NOW start parsing
	$parsed = parse_url($url);
	// Filter URLs with no domain
	if ( empty( $parsed['host'] ) ) { return ''; }
	return rssb_casetrans( 'lower', $parsed['host'] );
	}
function rssb_fix_url( $url, $rem_frag = FALSE, $rem_query = FALSE, $rev = FALSE ) {
	// Fix poorly formed URLs so as not to throw errors or cause problems
	// Too many forward slashes or colons after http
	$url = preg_replace( "~^(https?)\:+/+~i", "$1://", $url);
	// Too many dots
	$url = preg_replace( "~\.+~i", ".", $url);
	// Too many slashes after the domain
	$url = preg_replace( "~([a-z0-9]+)/+([a-z0-9]+)~i", "$1/$2", $url);
	// Remove fragments
	if ( !empty( $rem_frag ) && strpos( $url, '#' ) !== FALSE ) { $url_arr = explode( '#', $url ); $url = $url_arr[0]; }
	// Remove query string completely
	if ( !empty( $rem_query ) && strpos( $url, '?' ) !== FALSE ) { $url_arr = explode( '?', $url ); $url = $url_arr[0]; }
	// Reverse
	if ( !empty( $rev ) ) { $url = strrev($url); }
	return $url;
	}
function rssb_get_query_string( $url ) {
	/***
	* Get query string from URL
	* Filter URLs with nothing after http
	***/
	if ( empty( $url ) || preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url = rssb_fix_url( $url );
	/* NOW start parsing */
	$parsed = @parse_url($url);
	/* Filter URLs with no query string */
	if ( empty( $parsed['query'] ) ) { return ''; }
	$query_str = $parsed['query'];
	return $query_str;
	}
function rssb_get_query_args( $url ) {
	/***
	* Get query string array from URL
	***/
	if ( empty( $url ) ) { return array(); }
	$query_str = rssb_get_query_string( $url );
	parse_str( $query_str, $args );
	return $args;
	}
function rssb_is_plugin_active( $plugin_name ) {
	/***
	* Using this because is_plugin_active() only works in Admin 
	* ex. $plugin_name = 'folder/filename.php';
	***/
	if ( empty( $plugin_name ) ){ return FALSE; }
	global $rssb_active_plugins;
	if ( empty( $rssb_active_plugins ) ) { $rssb_active_plugins = get_option( 'active_plugins' ); }
	if ( in_array( $plugin_name, $rssb_active_plugins, TRUE ) ) { return TRUE; }
	return FALSE;
	}
function rssb_append_log_data( $str = NULL, $rsds_only = FALSE ) {
	/***
	* Adds data to the log for debugging - only use when Debugging - Use with WP_DEBUG & RSSB_DEBUG
	*
	* Example:
	* rssb_append_log_data( PHP_EOL.'$rssb_example_variable: "'.$rssb_example_variable.'" Line: '.__LINE__.' | '.__FUNCTION__.' | MEM USED: ' . rssb_format_bytes( memory_get_usage() ), TRUE );
	* rssb_append_log_data( PHP_EOL.'[A]$rssb_example_array_var: "'.serialize($rssb_example_array_var).'" Line: '.__LINE__.' | '.__FUNCTION__.' | MEM USED: ' . rssb_format_bytes( memory_get_usage() ), TRUE );
	***/
	if ( WP_DEBUG === TRUE && RSSB_DEBUG === TRUE ) {
		if ( !empty( $rsds_only ) && strpos( RSSB_SERVER_NAME_REV, RSMP_DEBUG_SERVER_NAME_REV ) !== 0 ) { return; }
		$rssb_log_str = 'ScrapeBreaker DEBUG: '.str_replace(PHP_EOL, "", $str);
		error_log( $rssb_log_str, 0 ); /* Logs to debug.log */
		}
	}
function rssb_format_bytes( $size, $precision = 2 ) {
	if ( !is_numeric( $size ) || empty( $size ) ) { return $size; }
    $base = log($size) / log(1024);
    $base_floor = floor($base);
    $suffixes = array('', 'k', 'M', 'G', 'T');
    $suffix = isset( $suffixes[$base_floor] ) ? $suffixes[$base_floor] : '';
	if ( empty($suffix) ) { return $size; }
	$formatted_num = round(pow(1024, $base - $base_floor), $precision) . $suffix;
    return $formatted_num;
	}
function rssb_date_diff( $start, $end ) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = ($end_ts-$start_ts);
	$start_array = explode('-', $start);
	$start_year = $start_array[0];
	$end_array = explode('-', $end);
	$end_year = $end_array[0];
	$years = $end_year-$start_year;
	if (($years%4) == 0) { $extra_days = ((($end_year-$start_year)/4)-1); } else { $extra_days = ((($end_year-$start_year)/4)); }
	$extra_days = round($extra_days);
	return round($diff/86400)+$extra_days;
	}
function rssb_is_lang_en_us( $strict = TRUE ) {
	// Test if site is set to use English (US) - the default - or another language/localization
	$rssb_locale = get_locale();
	if ( $strict != TRUE ) {
		// Not strict - English, but localized translations may be in use
		if ( !empty( $rssb_locale ) && !preg_match( "~^(en(_[a-z]{2})?)?$~i", $rssb_locale ) ) { $lang_en_us = FALSE; } else { $lang_en_us = TRUE; }
		}
	else {
		// Strict - English (US), no translation being used
		if ( !empty( $rssb_locale ) && !preg_match( "~^(en(_us)?)?$~i", $rssb_locale ) ) { $lang_en_us = FALSE; } else { $lang_en_us = TRUE; }
		}
	return $lang_en_us;
	}
function rssb_doc_txt() {
	return __( 'Documentation', RSSB_PLUGIN_NAME );
	}
function rssb_is_user_admin() {
	global $rssb_user_can_manage_options;
	if ( empty( $rssb_user_can_manage_options ) ) { $rssb_user_can_manage_options = current_user_can( 'manage_options' ) ? 'YES' : 'NO' ; }
	if ( $rssb_user_can_manage_options === 'YES' ) { return TRUE; }
	return FALSE;
	}
/* Standard Functions - END */

/* Admin Functions - BEGIN */
register_activation_hook( __FILE__, 'rssb_activation' );
function rssb_activation() {
	$installed_ver 	= get_option('scrapebreaker_version');
	$rssb_options 	= get_option('rssb_options');
	rssb_upgrade_check( $installed_ver, $rssb_options );
	if ( empty( $installed_ver ) || $installed_ver != RSSB_VERSION ) {
		// Set Initial Options
		if ( !empty( $rssb_options ) ) { $rssb_options_update = $rssb_options; }
		else {
			$rssb_options_update = array (
				'use_js_frame_breaker_only' => 0, 
				// Add more options here when added
				);
			}
		update_option( 'rssb_options', $rssb_options_update );
		}
	}
add_action( 'admin_init', 'rssb_check_version' );
function rssb_check_version() {
	if ( current_user_can( 'manage_network' ) ) {
		/* Check for pending admin notices */
		$admin_notices = get_option( 'rssb_admin_notices' );
		if ( !empty( $admin_notices ) ) { add_action( 'network_admin_notices', 'rssb_admin_notices' ); }
		/* Make sure not network activated */
		if ( is_plugin_active_for_network( RSSB_PLUGIN_BASENAME ) ) {
			deactivate_plugins( RSSB_PLUGIN_BASENAME, TRUE, TRUE );
			$notice_text = __( 'Plugin deactivated. ScrapeBreaker is not available for network activation.', RSSB_PLUGIN_NAME );
			$new_admin_notice = array( 'style' => 'error', 'notice' => $notice_text );
			update_option( 'rssb_admin_notices', $new_admin_notice );
			add_action( 'network_admin_notices', 'rssb_admin_notices' );
			return FALSE;
			}
		}
	if ( current_user_can( 'manage_options' ) ) {
		/* Check if plugin has been upgraded */
		rssb_upgrade_check();
		/* Check for pending admin notices */
		$admin_notices = get_option( 'rssb_admin_notices' );
		if ( !empty( $admin_notices ) ) { add_action( 'admin_notices', 'rssb_admin_notices' ); }
		/* Make sure user has minimum required WordPress version, in order to prevent issues */
		$rssb_wp_version = RSSB_WP_VERSION;
		if ( version_compare( $rssb_wp_version, RSSB_REQUIRED_WP_VERSION, '<' ) ) {
			deactivate_plugins( RSSB_PLUGIN_BASENAME );
			$notice_text = sprintf( __( 'Plugin deactivated. WordPress Version %s required. Please upgrade WordPress to the latest version.', RSSB_PLUGIN_NAME ), RSSB_REQUIRED_WP_VERSION );
			$new_admin_notice = array( 'style' => 'error', 'notice' => $notice_text );
			update_option( 'rssb_admin_notices', $new_admin_notice );
			add_action( 'admin_notices', 'rssb_admin_notices' );
			return FALSE;
			}
		rssb_check_nag_notices();
		}
	}
function rssb_admin_notices() {
	$admin_notices = get_option('rssb_admin_notices');
	if ( !empty( $admin_notices ) ) {
		$style 	= $admin_notices['style']; /* 'error' or 'updated' */
		$notice	= $admin_notices['notice'];
		echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
		}
	delete_option('rssb_admin_notices');
	}
function rssb_admin_nag_notices() {
	global $current_user;
	$nag_notices = get_user_meta( $current_user->ID, 'rssb_nag_notices', TRUE );
	if ( !empty( $nag_notices ) ) {
		$nid			= $nag_notices['nid'];
		$style			= $nag_notices['style']; /* 'error'  or 'updated' */
		$timenow		= time();
		$url			= rssb_get_url();
		$query_args		= rssb_get_query_args( $url );
		$query_str		= '?' . http_build_query( array_merge( $query_args, array( 'rssb_hide_nag' => '1', 'nid' => $nid ) ) );
		$query_str_con	= 'QUERYSTRING';
		$notice			= str_replace( array( $query_str_con ), array( $query_str ), $nag_notices['notice'] );
		echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
		}
	}
function rssb_check_nag_notices() {
	global $current_user;
	$status			= get_user_meta( $current_user->ID, 'rssb_nag_status', TRUE );
	if ( !empty( $status['currentnag'] ) ) { add_action( 'admin_notices', 'rssb_admin_nag_notices' ); return; }
	if ( !is_array( $status ) ) { $status = array(); update_user_meta( $current_user->ID, 'rssb_nag_status', $status ); }
	$timenow		= time();
	$num_days_inst	= rssb_num_days_inst();
	$query_str_con	= 'QUERYSTRING';
	/* Notices (Positive Nags) */
	if ( empty( $status['currentnag'] ) && ( empty( $status['lastnag'] ) || $status['lastnag'] <= $timenow - 1209600 ) ) {
		if ( empty( $status['vote'] ) && $num_days_inst >= 14 ) { /* TO DO: TRANSLATE */
			$nid = 'n01'; $style = 'updated';
			$notice_text = __( 'It looks like you\'ve been using ScrapeBreaker for a while now. That\'s great! :)', RSSB_PLUGIN_NAME ) .'</p><p>'. __( 'If you find this plugin useful, would you take a moment to give it a rating on WordPress.org?', RSSB_PLUGIN_NAME ) .'</p><p>'. sprintf( __( '<strong><a href=%1$s>%2$s</a></strong>', RSSB_PLUGIN_NAME ), '"'.RSSB_WP_RATING_URL.'" target="_blank" rel="external" ', __( 'Yes, I\'d like to rate it!', RSSB_PLUGIN_NAME ) ) .' &mdash; '.  sprintf( __( '<strong><a href=%1$s>%2$s</a></strong>', RSSB_PLUGIN_NAME ), '"'.$query_str_con.'" ', __( 'I already did!', RSSB_PLUGIN_NAME ) );
			$status['currentnag'] = TRUE; $status['vote'] = FALSE;
			}
		elseif ( empty( $status['donate'] ) && $num_days_inst >= 90 ) { /* TO DO: TRANSLATE */
			$nid = 'n02'; $style = 'updated';
			$notice_text = __( 'You\'ve been using ScrapeBreaker for quite a while now. Outstanding! We hope that means you like it and are finding it helpful. :)', RSSB_PLUGIN_NAME ) .'</p><p>'. __( 'ScrapeBreaker is provided for free.', RSSB_PLUGIN_NAME ) . ' ' . __( 'If you like the plugin, consider a donation to help further its development.', RSSB_PLUGIN_NAME ) .'</p><p>'. sprintf( __( '<strong><a href=%1$s>%2$s</a></strong>', RSSB_PLUGIN_NAME ), '"'.RSSB_DONATE_URL.'" target="_blank" rel="external" ', __( 'Yes, I\'d like to donate!', RSSB_PLUGIN_NAME ) ) .' &mdash; '. sprintf( __( '<strong><a href=%1$s>%2$s</a></strong>', RSSB_PLUGIN_NAME ), '"'.$query_str_con.'" ', __( 'I already did!', RSSB_PLUGIN_NAME ) );
			$status['currentnag'] = TRUE; $status['donate'] = FALSE;
			}
		}
	/* Warnings (Negative Nags) */
	/* TO DO: Add Negative Nags - warnings about plugin conflicts and missing PHP functions */
	if ( !empty( $status['currentnag'] ) ) {
		add_action( 'admin_notices', 'rssb_admin_nag_notices' );
		$new_nag_notice = array( 'nid' => $nid, 'style' => $style, 'notice' => $notice_text );
		update_user_meta( $current_user->ID, 'rssb_nag_notices', $new_nag_notice );
		update_user_meta( $current_user->ID, 'rssb_nag_status', $status );
		}
	}
add_action( 'admin_init', 'rssb_hide_nag_notices', -10 );
function rssb_hide_nag_notices() {
	if ( !rssb_is_user_admin() ) { return; }
	$ns_codes		= array( 'n01' => 'vote', 'n02' => 'donate', ); /* Nag Status Codes */
	if ( !isset( $_GET['rssb_hide_nag'], $_GET['nid'], $ns_codes[$_GET['nid']] ) || $_GET['rssb_hide_nag'] != '1' ) { return; }
	global $current_user;
	$status			= get_user_meta( $current_user->ID, 'rssb_nag_status', TRUE );
	$timenow		= time();
	$url			= rssb_get_url();
	$query_args		= rssb_get_query_args( $url ); unset( $query_args['rssb_hide_nag'],$query_args['nid'] );
	$query_str		= http_build_query( $query_args ); if ( $query_str != '' ) { $query_str = '?'.$query_str; }
	$redirect_url	= rssb_fix_url( $url, TRUE, TRUE ) . $query_str;
	$status['currentnag'] = FALSE; $status['lastnag'] = $timenow; $status[$ns_codes[$_GET['nid']]] = TRUE;
	update_user_meta( $current_user->ID, 'rssb_nag_status', $status );
	update_user_meta( $current_user->ID, 'rssb_nag_notices', array() );
	wp_redirect( $redirect_url );
	exit;
	}
function rssb_upgrade_check( $installed_ver = NULL, $rssb_options = NULL ) {
	if ( empty( $installed_ver ) ) { $installed_ver = get_option( 'scrapebreaker_version' ); }
	if ( $installed_ver != RSSB_VERSION ) {
		$upd_options = array( 'scrapebreaker_version' => RSSB_VERSION, );
		foreach( $upd_options as $option => $val ) { update_option( $option, $val ); }
		if ( empty( $rssb_options ) ) {
			global $rssb_options;
			if ( empty( $rssb_options ) ) { $rssb_options = get_option('rssb_options'); }
			}
		if ( empty( $rssb_options['install_date'] ) ) {
			$rssb_options['install_date'] = date('Y-m-d');
			update_option( 'rssb_options', $rssb_options );
			}
		}
	}
add_action( 'plugins_loaded', 'rssb_load_languages' );
function rssb_load_languages() {
	load_plugin_textdomain( RSSB_PLUGIN_NAME, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}
add_action( 'admin_menu', 'rssb_add_plugin_settings_page' );
add_filter( 'plugin_action_links', 'rssb_filter_plugin_actions', 10, 2 );
add_filter( 'plugin_row_meta', 'rssb_filter_plugin_meta', 10, 2 );
function rssb_filter_plugin_actions( $links, $file ) {
	// Add "Settings" Link on Admin Plugins page, in plugin listings
	if ( $file == RSSB_PLUGIN_BASENAME ){
		$settings_link = '<a href="options-general.php?page='.RSSB_PLUGIN_NAME.'">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		}
	return $links;
	}
function rssb_filter_plugin_meta( $links, $file ) {
	// Add Links on Dashboard Plugins page, in plugin meta
	if ( $file == RSSB_PLUGIN_BASENAME ){
		$links[] = '<a href="'.RSSB_HOME_URL.'" target="_blank" rel="external" >' . rssb_doc_txt() . '</a>';
		$links[] = '<a href="'.RSSB_SUPPORT_URL.'" target="_blank" rel="external" >' . __( 'Support', RSSB_PLUGIN_NAME ) . '</a>';
		$links[] = '<a href="'.RSSB_WP_RATING_URL.'" target="_blank" rel="external" >' . __( 'Rate the Plugin', RSSB_PLUGIN_NAME ) . '</a>';
		$links[] = '<a href="'.RSSB_DONATE_URL.'" target="_blank" rel="external" >' . __( 'Donate', RSSB_PLUGIN_NAME ) . '</a>';
		}
	return $links;
	}
function rssb_add_plugin_settings_page() {
	add_options_page( 'ScrapeBreaker ' . __('Settings'), 'ScrapeBreaker', 'manage_options', RSSB_PLUGIN_NAME, 'rssb_plugin_settings_page' );
	}
function rssb_plugin_settings_page() {
	if ( !current_user_can('manage_options') ) {
		$restricted_area_warning = __( 'You do not have sufficient permissions to access this page.' );
		wp_die( $restricted_area_warning );
		}
	global $rssb_options;
	if ( empty( $rssb_options ) ) { $rssb_options = get_option('rssb_options'); }
	echo PHP_EOL."\t\t\t".'<div class="wrap">'.PHP_EOL."\t\t\t".'<h2>ScrapeBreaker ' . __( 'Settings' ) . '</h2>'.PHP_EOL;
	if ( !empty( $_REQUEST['submit_rssb_options'] ) && current_user_can('manage_options') && check_admin_referer('rssb_options_nonce') ) {
		echo '<div class="updated fade"><p>' . __( 'Plugin settings saved.', RSSB_PLUGIN_NAME ) . '</p></div>';
		// Validate Data
		if ( !empty( $_REQUEST['use_js_frame_breaker_only'] ) && ( $_REQUEST['use_js_frame_breaker_only'] == 'on' || $_REQUEST['use_js_frame_breaker_only'] == 1 || $_REQUEST['use_js_frame_breaker_only'] == '1' ) ) { 
			$rssb_options['use_js_frame_breaker_only'] = 1; 
			}
		else { $rssb_options['use_js_frame_breaker_only'] = 0; }
		// Update Values
		$rssb_options_update = array (
			'use_js_frame_breaker_only' => $rssb_options['use_js_frame_breaker_only'], 
			);
		// Update Options in WP DB
		update_option( 'rssb_options', $rssb_options_update );
		}
	?>
<form name="rssb_general_options" method="post">
<input type="hidden" name="submitted_rssb_general_options" value="1" />
<?php wp_nonce_field('rssb_options_nonce'); ?>
<fieldset class="options">
	<ul style="list-style-type:none;padding-left:30px;">
		<li>
		<label for="use_js_frame_breaker_only">
			<input type="checkbox" id="use_js_frame_breaker_only" name="use_js_frame_breaker_only" <?php echo ($rssb_options['use_js_frame_breaker_only']==TRUE?"checked=\"checked\"":"") ?> value="1" />
			<strong><?php _e( 'Use the JavaScript Frame Breaker only.', RSSB_PLUGIN_NAME ); ?></strong><br /><?php _e( 'Use this option if you prefer to only use the JavaScript frame breaker and not use the X-Frame-Options. The default uses the X-Frame-Options as the first line of defense and falls back to the JavaScript redirect in older browsers that do not recognize X-Frame-Options. You may want to test and see which is the best option for you.', RSSB_PLUGIN_NAME ); ?><br />&nbsp;
		</label>
		</li>
	</ul>
</fieldset>
<p class="submit">
<input type="submit" name="submit_rssb_options" value="<?php _e( 'Save Changes' ); ?>" class="button-primary" style="float:left;" />
</p>
</form>
	<?php
	if ( TRUE === RSSB_OVERRIDE ) {
		$rssb_override_message = sprintf( __( 'Your %s settings are currently overriding the settings on this page.', RSSB_PLUGIN_NAME ), 'wp-config.php' );
		echo PHP_EOL.'<p>&nbsp;</p>'.PHP_EOL.'<p>&nbsp;</p><strong style="color:#ff0000">'.$rssb_override_message.'</strong>'.PHP_EOL;
		}
	?>

<p>&nbsp;</p>
<p>&nbsp;</p>

<p><strong><a href="http://www.redsandmarketing.com/scrapebreaker-donate/" target="_blank" ><?php _e( 'Donate to ScrapeBreaker', RSSB_PLUGIN_NAME ); ?></a></strong><br />
<?php echo __( 'ScrapeBreaker is provided for free.', RSSB_PLUGIN_NAME ) . ' ' . __( 'If you like the plugin, consider a donation to help further its development.', RSSB_PLUGIN_NAME ); ?></p>
<p>&nbsp;</p>

<p><strong><?php _e( 'Check out our other plugins', RSSB_PLUGIN_NAME ); ?>:</strong></p>
<p><?php _e( 'If you like ScrapeBreaker, you might want to check out our other plugins:', RSSB_PLUGIN_NAME ); ?></p>
<ul style="list-style-type:disc;padding-left:30px;">
	<li><a href="http://www.redsandmarketing.com/plugins/wp-spamshield/" target="_blank" ><?php echo 'WP-SpamShield ' . __( 'Anti-Spam', RSSB_PLUGIN_NAME ); ?></a> <?php _e( 'An extremely powerful and user friendly WordPress anti-spam plugin that stops blog comment spam cold, including trackback and pingback spam. Includes spam-blocking contact form feature, and protection from user registration spam as well. WP-SpamShield is an all-in-one spam solution for WordPress. See what it\'s like to run a WordPress site without spam!', RSSB_PLUGIN_NAME ); ?></li>
	<li><a href="http://www.redsandmarketing.com/plugins/rs-head-cleaner/" target="_blank" ><?php echo 'RS Head Cleaner Plus'; ?></a> <?php _e( 'This plugin cleans up a number of issues, doing the work of multiple plugins, improving speed, efficiency, security, SEO, and user experience. It removes junk code from the HEAD & HTTP headers, moves JavaScript from header to footer, combines/minifies/caches CSS & JavaScript files, hides the Generator/WordPress Version number, removes version numbers from CSS and JS links, and fixes the "Read more" link so it displays the entire post.', RSSB_PLUGIN_NAME ); ?></li>
	<li><a href="http://www.redsandmarketing.com/plugins/rs-feedburner/" target="_blank" ><?php echo 'RS FeedBurner'; ?></a> <?php _e( 'This plugin redirects all requests for your native WordPress feeds to your FeedBurner, FeedPress, or FeedBlitz feeds so you can track all your subscribers and maximize your blog/site readership and user engagement.', RSSB_PLUGIN_NAME ); ?></li>
</ul>
<p>&nbsp;</p>

	<?php
	/* Recommended Partners - BEGIN */
	if ( rssb_is_lang_en_us() ) {
	?>
			
<div style='width:797px;border-style:solid;border-width:1px;border-color:#333333;background-color:#FEFEFE;padding:0px 15px 0px 15px;margin-top:15px;margin-right:15px;float:left;clear:left;'>
<p><h3>Recommended Partners</h3></p>
<p>Each of these products or services are ones that we highly recommend, based on our experience and the experience of our clients. We do receive a commission if you purchase one of these, but these are all products and services we were already recommending because we believe in them. By purchasing from these providers, you get quality and you help support the further development of ScrapeBreaker.</p>
</div>

	<?php
		$rssb_rpd 	= array(
			array('clear:left;','RSM_Genesis','Genesis WordPress Framework','Other themes and frameworks have nothing on Genesis. Optimized for site speed and SEO.','Simply put, the Genesis framework is one of the best ways to design and build a WordPress site. Built-in SEO and optimized for speed. Create just about any kind of design with child themes.'),
			array('','RSM_AIOSEOP','All in One SEO Pack Pro','The best way to manage the code-related SEO for your WordPress site.','Save time and effort optimizing the code of your WordPress site with All in One SEO Pack. One of the top rated, and most downloaded plugins on WordPress.org, this time-saving plugin is incredibly valuable. The pro version provides powerful features not available in the free version.'),
			);
		foreach( $rssb_rpd as $i => $v ) {
			echo "\t".'<div style="width:375px;height:280px;border-style:solid;border-width:1px;border-color:#333333;background-color:#FEFEFE;padding:0px 15px 0px 15px;margin-top:15px;margin-right:15px;float:left;'.$v[0].'">'.PHP_EOL."\t".'<p><strong><a href="http://bit.ly/'.$v[1].'" target="_blank" rel="external" >'.$v[2].'</a></strong></p>'.PHP_EOL."\t".'<p><strong>'.$v[3].'</strong></p>'.PHP_EOL."\t".'<p>'.$v[4].'</p>'.PHP_EOL."\t".'<p><a href="http://bit.ly/'.$v[1].'" target="_blank" rel="external" >Click here to find out more. >></a></p>'.PHP_EOL."\t".'</div>'.PHP_EOL;
			}
		}
	/* Recommended Partners - END */
	?>
<p style="clear:both;">&nbsp;</p>
</div>
	<?php
	}
function rssb_num_days_inst() {
	global $rssb_options;
	if ( empty( $rssb_options ) ) { $rssb_options = get_option('rssb_options'); }
	$current_date	= date('Y-m-d');
	$install_date	= empty( $rssb_options['install_date'] ) ? $current_date : $rssb_options['install_date'];
	$num_days_inst	= rssb_date_diff($install_date, $current_date); if ( $num_days_inst < 1 ) { $num_days_inst = 1; }
	return $num_days_inst;
	}
/* Admin Functions - END */

/*PLUGIN - END */
