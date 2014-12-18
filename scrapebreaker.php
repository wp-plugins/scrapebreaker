<?php
/*
Plugin Name: ScrapeBreaker
Plugin URI: http://www.redsandmarketing.com/plugins/scrapebreaker/
Description: A combination of frame-breaker and scraper protection. Protect your website content from both frames and server-side scraping techniques. If either happens, visitors will be redirected to the original content.
Author: Scott Allen
Version: 1.2
Author URI: http://www.redsandmarketing.com/
Text Domain: scrapebreaker
License: GPLv2
*/

/*  Copyright 2014    Scott Allen  (email : plugins [at] redsandmarketing [dot] com)

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

// PLUGIN - BEGIN

/* Note to any other PHP developers reading this:
My use of the closing curly braces "}" is a little funky in that I indent them, I know. IMO it's easier to debug. Just know that it's on purpose even though it's not standard. One of my programming quirks, and just how I roll. :)
*/

// Make sure plugin remains secure if called directly
if ( !function_exists( 'add_action' ) ) {
	if ( !headers_sent() ) {
		header('HTTP/1.1 403 Forbidden');
		}
	die('ERROR: This plugin requires WordPress and will not function if called directly.');
	}

// Setting constants in case we expand later on
define( 'RSSB_VERSION', '1.2' );
define( 'RSSB_REQUIRED_WP_VERSION', '3.6' );

if ( !defined( 'RSSB_DEBUG' ) ) 				{ define( 'RSSB_DEBUG', false ); } // Do not change value unless developer asks you to - for debugging only. Change in wp-config.php.
if ( !defined( 'RSSB_OVERRIDE' ) ) 				{ define( 'RSSB_OVERRIDE', false ); } // To improve speed by eliminating DB calls. Enables overriding the DB options. Change in wp-config.php.
if ( !defined( 'RSSB_JS_ONLY' ) ) 				{ define( 'RSSB_JS_ONLY', false ); } // To improve speed by eliminating DB calls. Sets option to only use JS Frame Breaker & not use X-Frames-Options Change in wp-config.php.
if ( !defined( 'RSSB_PLUGIN_BASENAME' ) ) 		{ define( 'RSSB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
if ( !defined( 'RSSB_PLUGIN_FILE_BASENAME' ) ) 	{ define( 'RSSB_PLUGIN_FILE_BASENAME', trim( basename( __FILE__ ), '/' ) ); }
if ( !defined( 'RSSB_PLUGIN_NAME' ) ) 			{ define( 'RSSB_PLUGIN_NAME', trim( dirname( RSSB_PLUGIN_BASENAME ), '/' ) ); }
// Constants prefixed with 'RSMP_' are shared with other RSM Plugins for efficiency.
if ( !defined( 'RSMP_SERVER_ADDR' ) ) 			{ define( 'RSMP_SERVER_ADDR', rssb_get_server_addr() ); }
if ( !defined( 'RSMP_SERVER_NAME' ) ) 			{ define( 'RSMP_SERVER_NAME', rssb_get_server_name() ); }
if ( !defined( 'RSMP_SERVER_NAME_REV' ) ) 		{ define( 'RSMP_SERVER_NAME_REV', strrev( RSMP_SERVER_NAME ) ); }
if ( !defined( 'RSMP_DEBUG_SERVER_NAME' ) ) 	{ define( 'RSMP_DEBUG_SERVER_NAME', '.redsandmarketing.com' ); }
if ( !defined( 'RSMP_DEBUG_SERVER_NAME_REV' ) )	{ define( 'RSMP_DEBUG_SERVER_NAME_REV', strrev( RSMP_DEBUG_SERVER_NAME ) ); }

add_action( 'send_headers', 'rssb_add_headers' );
add_action( 'wp_head', 'rssb_scrapebreaker', -10 );

function rssb_add_headers() {
	// Check options
	if ( RSSB_OVERRIDE ) {
		if ( RSSB_JS_ONLY ) { $rssb_js_only = 1; } else { $rssb_js_only = 0; }
		}
	else {
		$rssb_options = get_option('rssb_options');
		$rssb_js_only = $rssb_options['use_js_frame_breaker_only'];
		}
	if ( empty( $rssb_js_only ) ) {
		@header( 'X-Frame-Options: SAMEORIGIN' );
		// Also WP Function: send_frame_options_header()
		}
	}

function rssb_scrapebreaker() {
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		$rs_this_page_prefix = 'https://';
		}
	else {
		$rs_this_page_prefix = 'http://';
		}
	$rs_this_page_url = $rs_this_page_prefix.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	$rssb_activated = rssb_is_activated();
	if ( $rssb_activated=='yes' ) {
		echo "\n<script type=\"text/javascript\" >\n// <![CDATA[\nvar thispage = \"".$rs_this_page_url."\";\nif (top.location!=thispage||window!=top){top.location.href=thispage;window.open(thispage,'_top');}\n// ]]>\n</script>\n";
		}
	}

function rssb_is_activated() {
	$rssb_activated = 'no';
	if (!is_admin()&&!is_user_logged_in()){
		// Not active when in Admin or logged in on rest of site. No reason for logged in users to be blocked.
		$rssb_activated = 'yes';
		}
	return $rssb_activated;
	}

// Standard Functions - BEGIN
function rssb_get_server_addr() {
	if ( !empty( $_SERVER['SERVER_ADDR'] ) ) { $server_addr = $_SERVER['SERVER_ADDR']; } else { $server_addr = getenv('SERVER_ADDR'); }
	return $server_addr;
	}
function rssb_get_server_name() {
	if ( !empty( $_SERVER['SERVER_NAME'] ) ) { $server_name = strtolower( $_SERVER['SERVER_NAME'] ); } else { $server_name = strtolower( getenv('SERVER_NAME') ); }
	return $server_name;
	}
// Standard Functions - END

// Admin Functions - BEGIN
register_activation_hook( __FILE__, 'rssb_install_on_first_activation' );
function rssb_install_on_first_activation() {
	$installed_ver 	= get_option('scrapebreaker_version');
	$rssb_options 	= get_option('rssb_options');
	if ( empty( $installed_ver ) || $installed_ver != RSSB_VERSION ) {
		update_option('scrapebreaker_version', RSSB_VERSION);
		// Set Initial Options
		if ( !empty( $spamshield_options ) ) {
			$rssb_options_update = $rssb_options;
			}
		else {
			$rssb_options_update = array (
				'use_js_frame_breaker_only' => 0, 
				);
			}
		update_option( 'rssb_options', $rssb_options_update );
		}
	}
add_action( 'admin_init', 'rssb_check_version' );
function rssb_check_version() {
	if ( current_user_can('manage_options') ) {
		// Make sure user has minimum required WordPress version, in order to prevent issues
		global $wp_version;
		$rssb_wp_version = $wp_version;
		if ( version_compare( $rssb_wp_version, RSSB_REQUIRED_WP_VERSION, '<' ) ) {
			deactivate_plugins( RSSB_PLUGIN_BASENAME );
			$notice_text = sprintf( __( 'Plugin deactivated. WordPress Version %s required. Please upgrade WordPress to the latest version.', RSSB_PLUGIN_NAME ), RSSB_REQUIRED_WP_VERSION );
			$new_admin_notice = array( 'style' => 'error', 'notice' => $notice_text );
			update_option( 'rssb_admin_notices', $new_admin_notice );
			add_action( 'admin_notices', 'rssb_admin_notices' );
			return false;
			}
		add_action( 'admin_notices', 'rssb_admin_notices' );
		}
	}
function rssb_admin_notices() {
	$admin_notices = get_option('rssb_admin_notices');
	if ( !empty( $admin_notices ) ) {
		$style 	= $admin_notices['style']; // 'error'  or 'updated'
		$notice	= $admin_notices['notice'];
		echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
		}
	delete_option('rssb_admin_notices');
	}
add_action( 'plugins_loaded', 'rssb_load_languages' );
function rssb_load_languages() {
	load_plugin_textdomain( RSSB_PLUGIN_NAME, false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
add_action( 'admin_menu', 'rssb_add_plugin_settings_page' );
add_filter( 'plugin_action_links', 'rssb_filter_plugin_actions', 10, 2 );
function rssb_filter_plugin_actions( $links, $file ) {
	// Add "Settings" Link on Admin Plugins page, in plugin listings
	if ( $file == RSSB_PLUGIN_BASENAME ){
		$settings_link = '<a href="options-general.php?page='.RSSB_PLUGIN_NAME.'">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
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

	$rssb_options = get_option('rssb_options');
	
	echo "\n\t\t\t".'<div class="wrap">'."\n\t\t\t".'<h2>ScrapeBreaker ' . __( 'Settings' ) . '</h2>'."\n";

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
						<input type="checkbox" id="use_js_frame_breaker_only" name="use_js_frame_breaker_only" <?php echo ($rssb_options['use_js_frame_breaker_only']==true?"checked=\"checked\"":"") ?> value="1" />
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
			if ( RSSB_OVERRIDE ) {
				$rssb_override_message = sprintf( __( 'Your %s settings are currently overriding the settings on this page.', RSSB_PLUGIN_NAME ), 'wp-config.php' );
				echo "\n\t\t\t".'<p>&nbsp;</p>'."\n\t\t\t".'<p>&nbsp;</p><strong style="color:#ff0000">'.$rssb_override_message.'</strong>'."\n";
				}
			?>

			<p>&nbsp;</p>
			<p>&nbsp;</p>

  			<p><strong><?php _e( 'Check out our other plugins', RSSB_PLUGIN_NAME ); ?>:</strong></p>
			<p><?php _e( 'If you like ScrapeBreaker, you might want to check out our other plugins:', RSSB_PLUGIN_NAME ); ?></p>
			<ul style="list-style-type:disc;padding-left:30px;">
				<li><a href="http://www.redsandmarketing.com/plugins/wp-spamshield/" target="_blank" ><?php echo 'WP-SpamShield ' . __( 'Anti-Spam', RSSB_PLUGIN_NAME ); ?></a> <?php _e( 'An extremely powerful and user friendly WordPress anti-spam plugin that stops blog comment spam cold, including trackback and pingback spam. Includes spam-blocking contact form feature, and protection from user registration spam as well. WP-SpamShield is an all-in-one spam solution for WordPress. See what it\'s like to run a WordPress site without spam!', RSSB_PLUGIN_NAME ); ?></li>
				<li><a href="http://www.redsandmarketing.com/plugins/rs-head-cleaner/" target="_blank" ><?php echo 'RS Head Cleaner Plus'; ?></a> <?php _e( 'This plugin cleans up a number of issues, doing the work of multiple plugins, improving speed, efficiency, security, SEO, and user experience. It removes junk code from the HEAD & HTTP headers, moves JavaScript from header to footer, combines/minifies/caches CSS & JavaScript files, hides the Generator/WordPress Version number, removes version numbers from CSS and JS links, and fixes the "Read more" link so it displays the entire post.', RSSB_PLUGIN_NAME ); ?></li>
				<li><a href="http://www.redsandmarketing.com/plugins/rs-feedburner/" target="_blank" ><?php echo 'RS FeedBurner'; ?></a> <?php _e( 'This plugin redirects all requests for your native WordPress feeds to your Feedburner feeds so you can track all your subscribers and maximize your blog/site readership and user engagement.', RSSB_PLUGIN_NAME ); ?></li>
			</ul>
			<p>&nbsp;</p>

			</div>
			<?php
	}
// Admin Functions - END

// PLUGIN - END
?>