=== Xili-theme select ===
Contributors: michelwppi, MS dev.xiligroup
Donate link: http://dev.xiligroup.com/
Tags: theme,ipod touch,iphone, iPad, Post,plugin,posts,admin,opera mini,windows mobile, multilingual,bilingual,ipad, chrome, gecko
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 2.0
License: GPLv2

Xili-theme select is an automatic selector of theme according defined rules: one for current browsers and anothers for mobile browsers (iPhone, iPad)

== Description ==

*Xili-theme select is an automatic selector of theme according a series of defined rules: one for current browsers and anothers for (each) mobile browsers (iPhone, iPad, and more).*

The plugin xili-theme select don't content itself themes. The available themes remain in Themes folder and are selected between the current browser and the rulers defined by webmaster. Webmaster is totally free to define (the look of) the theme on each side (desktop or other). There is no automatic look transformation. Each available theme can be responsive or not. Plugin is compatible with child themes.

> Prerequisites : themes must be well developped according current WP core rules like well done in bundled themes.

By default, Xili-theme select is provided with a small series of default rules for iPhone, iPad, and mobile based Chrome browsers. With a filter, it will be possible to define your own rules

If the themes are "international", xili-theme select don't interfere and so is full compatible with [xili-language](https://wordpress.org/extend/plugins/xili-language/ "xili-language").

This version 2.0 includes latest famous free library/class from Anthony Hand : [Github of mobilesp](https://github.com/ahand/mobileesp).

= roadmap =
* readme rewritting
* other new rules (more flexible, new filter for theme developer/webmaster)

= 2.0 =

> it is a major update, you need to renew Settings via Dashboard Menu Settings/xilitheme select.

* Last Updated 2015-05-28

* completely rewritten
* no need to use specific suffix theme naming as previously.
* see [tab and chapters in changelog](https://wordpress.org/extend/plugins/xilitheme-select/changelog/)


**prerequisite:**

**Caution:** - Before use *xili-theme select*: uninstall plugins like  *'any-mobile-theme-switcher'*, *'Device Theme Switcher'*, *'iwphone'* or *'WordPress PDA & iPhone'* which do theme's redirections as here.

**Note about theme with template_page:**
Both themes (the current and the one for iphone / ipodtouch) must contain the same template (name) - the contents can differ obviously -
A good architecture : multiple child themes with same parent!

Check out the [screenshots](https://wordpress.org/extend/plugins/xilitheme-select/screenshots/) to see it in action.

== Installation ==

1. Upload the folder containing `xilithemeselect.php` and language files to the `/wp-content/plugins/` directory,
2. Be sure you have one theme optimized for mobile and one for desktop
3. Activate the plugin through the *'Plugins'* menu in WordPress,
4. Go to the dashboard settings tab - Xili-theme select - and adapt default values if necessary.

== Frequently Asked Questions ==

= soon according returns in forum =


== Screenshots ==

1. the admin settings UI

== Upgrade Notice ==

* Easy to upgrade through Plugins admin UI or via FTP.
* Don't forget to proceed DB backup before.

== More infos ==
= soon more infos =

= link to display current theme even in mobile =
If `$wp_ismobile->cookienable` is set to 1, the theme can include a tag to refresh theme as viewing on a desktop browser even if on mobile. See tag example in php code.

`[xili-theme-link]` shortcode is also available and can be inserted everywhere (the link built detects if current or not current theme active).

== how to refresh xilitheme cookie ==

use param : ?xilitheme=reset at end of URI

== Changelog ==

= 2.0 (2015-05-28) =
* MAJOR UPDATE
* if using a previous version, you must to RENEW Settings via Dashboard Menu Settings/xilitheme select.
* completely rewritten with latest WordPress functions and filters
* no need to use specific suffix theme naming as previously.

= 1.0.4 (2013-05-25) =
* add __construct() php5
= 1.0.2, 1.0.3 =
* maintenance releases
= 1.0.1 = 
* Add Android as selector - see line #50. - a newer demo theme default_4touch is available in [here](http://dev.xiligroup.com/?p=123#loadlinks "Xilitheme select links").

= 1.0 = 
* Admin settings improvments for latest 2.8 WordPress.

= 0.9.2 = 
* option: now able to detect opera mini browser.

= 0.9.1 = 
* option: now able to display current theme view on iPhone / iPod.

© 2015-05-15 dev.xiligroup.com

== Upgrade Notice ==

* Plugin only use Options table in WP database.
* As usual before upgrading, read carefully the readme.txt and backup your database.
* Read code source if you are developer.

