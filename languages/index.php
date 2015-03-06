<?php
/*
ScrapeBreaker - index.php
Version: 1.3.1

This script keeps search engines, bots, and unwanted visitors from viewing your private plugin directory contents.
 
You can avoid the need for pages like this by adding a single line of code to the beginning of your .htaccess file:
	## Add the following line to the beginning of your .htaccess for security and SEO.
	Options All -Indexes
	## This will turn off indexes so your site won't reveal contents of directories that don't have an index file.
*/

error_reporting(0);

// We're going to redirect bots and human visitors to the website root.
$new_url =  rssb_get_site_url_alt();
header( 'Location: '.$new_url, true, 301 );

function rssb_get_site_url_alt() {
	if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) { $url = 'https://'; } else { $url = 'http://'; }
	$url .= rssb_get_server_name_alt();
	return $url;
	}

function rssb_get_server_name_alt() {
	$rssb_site_dom_nw = $server_name = $_SERVER['SERVER_NAME'];
	if ( substr( $rssb_site_dom_nw, 0, 4 ) == 'www.' ) { $rssb_site_dom_nw = substr( $rssb_site_dom_nw, 4 ); }
	$rssb_env_http_host = getenv('HTTP_HOST'); $rssb_env_srvr_name = getenv('SERVER_NAME');
	if ( !empty( $_SERVER['HTTP_HOST'] ) && strpos( $_SERVER['HTTP_HOST'], $rssb_site_dom_nw ) !== FALSE ) { $server_name = $_SERVER['HTTP_HOST']; }
	elseif ( !empty( $rssb_env_http_host ) && strpos( $rssb_env_http_host, $rssb_site_dom_nw ) !== FALSE ) { $server_name = $rssb_env_http_host; }
	elseif ( !empty( $_SERVER['SERVER_NAME'] ) ) { $server_name = $_SERVER['SERVER_NAME']; }
	elseif ( !empty( $rssb_env_srvr_name ) ) { $server_name = $rssb_env_srvr_name; }
	return strtolower( $server_name );
	}

?>