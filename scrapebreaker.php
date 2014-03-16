<?php
/*
Plugin Name: ScrapeBreaker
Plugin URI: http://www.redsandmarketing.com/plugins/scrapebreaker/
Description: A combination of frame-breaker and scraper protection. Protect your website content from both frames and server-side scraping techniques. If either happens, visitors will be redirected to the original content.
Author: Scott Allen
Version: 1.0.1.1
Author URI: http://www.redsandmarketing.com/
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
My use of the end curly braces "}" is a little funky in that I indent them, I know. IMO it's easier to debug. Just know that it's on purpose even though it's not standard. One of my programming quirks, and just how I roll. :)
*/

// Setting constants in case we expand later on
define( 'RSSB_VERSION', '1.0.1.1' );
define( 'RSSB_REQUIRED_WP_VERSION', '2.5' );

add_action( 'send_headers', 'rssb_add_headers' );
add_action( 'wp_head', 'rssb_scrapebreaker', -10 );

function rssb_add_headers() {
	//header( 'X-RSSB-Test: TRUE' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	}

function rssb_scrapebreaker() {
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		$rs_this_page_prefix = 'https://';
		}
	else {
		$rs_this_page_prefix = 'http://';
		}
	$rs_this_page_url = $rs_this_page_prefix.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	$rssb_activated = rssb_activate();
	if ( $rssb_activated=='yes' ) {
		echo "\n<script type=\"text/javascript\" async >\n// <![CDATA[\nvar thispage = \"".$rs_this_page_url."\";\nif (top.location!=thispage){top.location.href=thispage}\n// ]]>\n</script>\n";
		}
	}

function rssb_activate() {
	$rssb_activated = 'no';
	if (!is_admin()&&!is_user_logged_in()){ // Not in Admin section and not logged in on rest of site
		// Was using current_user_can('level_10') - but is deprecated.
		// Alternate is current_user_can('manage_options') - also viable.
		// No reason for logged in users to be blocked, so is_user_logged_in() will suffice.
		$rssb_activated = 'yes';
		}
	return $rssb_activated;
	}

// PLUGIN - END
?>