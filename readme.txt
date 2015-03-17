=== ScrapeBreaker ===
Contributors: RedSand
Donate link: http://www.redsandmarketing.com/scrapebreaker-donate/
Tags: anti-scraping, break, content, Digg, framebreaker, frame-breaker, frame, frames, iframe, javascript, scraping, seo
Requires at least: 3.7
Tested up to: 4.1
Stable tag: trunk

A combination of frame-breaker and scraper protection. Protect your website content from both frames and server-side scraping techniques.

== Description == 

A combination of frame-breaker and scraper protection. Protect your website content from both frames and server-side scraping techniques. If either happens, visitors will be redirected to the original content.

Website scraping comes in two forms: 1) sites that place your content in frames on their site, and 2) server-side scripts that get the contents of your page and insert them into another page using, PHP, ASP, etc, making it look like the scraper site created the content. 

Also some well-known sites display a bar with their content above your content, rob you of traffic, and skew the look and feel of your page. Anyone remember the Digg Bar? Yeah.

Other scrapers just outright jack your site content. Well, that's over now.

This plugin is better than other any frame-breaker plugins out there. Other frame-breakers will only test to see if your site is framed or not. This one will test to see if your content is present on a different domain, and if so, will redirect to the original content's URL. Also, uses X-Frame-Options in HTTP headers to keep your content from showing in frames on other web domains.

The plugin won't activate the protection features in the WordPress Admin area or if a registered user is navigating your site while logged in.

Features:

* Get your website visitors back.
* Retain credit for your work.
* Protect your website content and your brand.
* Improve your SEO.

Try it and see for yourself.

= More Info / Documentation =

If you want to test it out, be sure you are logged out, as it won't activate while you're logged in.

For more info and full documentation, visit the [ScrapeBreaker homepage](http://www.redsandmarketing.com/plugins/scrapebreaker/).

= Languages Available =

* Serbian (sr_RS)

== Installation ==

= Installation Instructions =

**Option 1:** Install the plugin directly through the WordPress Admin Dashboard (Recommended)

1. Go to *Plugins* -> *Add New*.

2. Type *ScrapeBreaker* into the Search box, and click *Search Plugins*.

3. When the results are displayed, click *Install Now*.

4. When it says the plugin has successfully installed, click **Activate Plugin** to activate the plugin (or you can do this on the Plugins page).

**Option 2:** Install .zip file through WordPress Admin Dashboard

1. Go to *Plugins* -> *Add New* -> *Upload*.

2. Click *Choose File* and find `scrapebreaker.zip` on your computer's hard drive.

3. Click *Install Now*.

4. Click **Activate Plugin** to activate the plugin (or you can do this on the Plugins page).

**Option 3:** Install .zip file through an FTP Client (Recommended for Advanced Users Only)

1. After downloading, unzip file and use an FTP client to upload the enclosed `scrapebreaker` directory to your WordPress plugins directory (usually `/wp-content/plugins/`) on your web server.

2. Go to your Plugins page in the WordPress Admin Dashboard, and find this plugin in the list.

3. Click **Activate** to activate the plugin.

== Frequently Asked Questions ==

= Can I set an option to just use the JavaScript Frame Breaker (redirect) and not use the X-Frame-Options HTTP header? =

Yes, the option was added in version 1.1. Just check the option on the Settings page and click 'Save Changes'.

If you don't want slow your site down by adding additional database calls during page loads, you can set this option with a constant in the `wp-config.php` file. If you're not familiar with editing this file, then don't edit it. If you are, add these 2 lines to your `wp-config.php` file (before the line where it says to stop editing):

`define( 'RSSB_OVERRIDE', true );
define( 'RSSB_JS_ONLY', true );`

Or if just want the speed improvement, but don't want to use that option:

`define( 'RSSB_OVERRIDE', true );
define( 'RSSB_JS_ONLY', false );`

Keep in mind that this completely overrides anything you set in the Settings page, and we're not responsible if you break your site when you edit this file. (As always, back it up first.)

= You do great work...can I hire you? =

Absolutely...go to my <a href="http://www.redsandmarketing.com/web-design/wordpress-consulting/">WordPress Consulting</a> page for more information.

== Changelog ==


= 1.3.2 =
*released 03/017/15*

* Fixed an issue with URLs containing fragments ("#") that were incorrectly redirecting to the non-fragment version. This was fixed by modifying the JavaScript redirect to match the main part of the URL instead of doing an exact match for the full URL.

= 1.3.1 =
*released 03/06/15*

* Fixed a minor bug.
* Made various code improvements.

= 1.3 =
*released 01/19/15*

* Increased minimum required WordPress version to 3.7.
* Minor update to translation files.
* Added recommended partners to settings page.

= 1.2 =
*released 12/18/14*

* Prepared the plugin for internationalization and localization, and created .pot file for translation.
* Added Serbian Translation (sr_RS). Thank you to Ogi Djuraskovic of First Site Guide for doing the Serbian translation.
* Increased minimum required WordPress version to 3.6.
* Minor code improvements.

= 1.1 =
*released 09/06/14*

* Added a Settings page in the Dashboard.
* Added an option to only use the JavaScript Frame Breaker (redirect) and not use the X-Frame-Options HTTP header.

= 1.0.1.4 =
*released 04/28/14*

* Fixed a bug that caused an error message on certain server configurations.

= 1.0.1.3 =
*released 04/13/14*

* Added additional security checks.

= 1.0.1.2 =
*released 04/03/14*

* Minor code improvement.

= 1.0.1.1 =
*released 03/16/14*

* Improved the implementation and removed deprecated code.

= 1.0.1 =
*released 03/15/14*

* Added *X-Frame-Options* to HTTP headers for increased protection against framing and clickjacking.

= 1.0 =
*released 03/10/14*

* Initial release.

== Upgrade Notice ==
= 1.3.2 =
Fixed an issue with URLs containing fragments ("#") that were incorrectly redirecting to the non-fragment version. Please see Changelog for details.
