=== Xilitheme select ===
Contributors: MS xiligroup
Donate link: http://dev.xiligroup.com/
Tags: theme,ipod touch,iphone,Post,plugin,posts,admin,opera mini,windows mobile, multilingual,bilingual,ipad
Requires at least: 2.9.2
Tested up to: 3.0
Stable tag: 1.0.2
License: GPLv2

Xilitheme select provides an automatic selection of themes : one for current browsers and another for iphone/ipod touch browser (and more).

== Description ==

*Xilitheme select provides an automatic selection of themes : one for current browsers and another for iphone/ipod touch browser (and more non "Apple touch" mobiles).*

The plugin xilitheme-select don't content itself themes. The themes remain in Themes folder and are selected by the current browser and the rulers defined by webmaster. Webmaster is totally free to define (the look of) the theme on each side (desktop or iPhone). There is no automatic look transformation. 

If the themes are "international", xilitheme select don't interfere and so is full compatible with [xili-language](http://wordpress.org/extend/plugins/xili-language/ "xili-language").


= 1.0.2 =

* compatibility if current theme is a child theme - as demo [here](http://dev.xiligroup.com/) *child of default twentyten*.

= 1.0.1 =
* More descriptions for non "Apple touch" mobiles/
* A demo theme with more features - see [here](http://WordPress.org/extend/plugins/xilitheme-select/faq/) -

*this first release can be used by users/webmasters with knowledges about WP themes - see php code.*

**prerequisite:**
By default:
In addition of your current selected theme add in the themes folder a theme folder named as the current with extension *'_4touch'* dedicaced for iphone browser.

**Caution:** - Before use *xilitheme select*: uninstall plugins like *'iwphone'* or *'WordPress PDA & iPhone'* which do theme's redirections as this.

**Note about theme with template_page:**
Both themes (the current and the one for iphone / ipodtouch) must contain the same template (name) - the contents can differ obviously -

**admin dashboard UI:**
The interface provide ways to change default extension or choose a specific fullname (for the "iphone" theme)


**Options:**
See the source of the plugin to discover other type of instantiation of the class *xilithemeselector*. One provide a way to have or not a admin dashboard interface to set the specs with only php coding.
see `$wp_ismobile = new xilithemeselector(true);` at end of code

More informations on the site [dev.xiligroup.com](http://dev.xiligroup.com/ "Xilitheme select")

Check out the [screenshots](http://wordpress.org/extend/plugins/xilitheme-select/screenshots/) to see it in action.

== Installation ==

1. Upload the folder containing `xilithemeselect.php` and language files to the `/wp-content/plugins/` directory,
2. Upload a **specific theme** for iPhone with a folder named as current theme *+ extension* "_4touch" but don't activate it as usual. A newer demo theme default_4touch is available [here](http://dev.xiligroup.com/?p=123#loadlinks "Xilitheme select links")

3. Activate the plugin through the *'Plugins'* menu in WordPress,
4. Go to the dashboard settings tab - Xilitheme select - and adapt default values if necessary.

== Frequently Asked Questions ==

= When I visit the site with iPhone or iPhone simulator, I don't see the specific theme =

Verify that the folder of this theme has a right name with right extension.

= Why the option full name or extension ? =

It is an easiness to choose a theme with a folder without the proposed file extension.

= Do you provide a theme for these mobile device as iPhone or iTouch ? =

A lite release of a theme is [here](http://dev.xiligroup.com/?p=123#loadlinks "Xilitheme select links").
*(this theme now contains accordion features for categories list and is ready for multilingual website with plugin xili-language)*

= Where can I see a running example =

dev.xiligroup.com [here](http://dev.xiligroup.com/ "dev.xiligroup site")
and
www.xiliphone.mobi [here](http://www.xiliphone.mobi "a theme for mobile") usable with mobile as iPhone or Safari with options developer activated and agent set to iPhone.

= What about iPad ? =
iPad's Browser is very very similar to current Safari. So we decide yet to not add it in list. But it possible to modify var in top of class.

== Screenshots ==

1. an example of wp-content/themes folder
2. the admin settings UI

== Upgrade Notice ==

* Easy to upgrade through Plugins admin UI or via FTP.
* If you changed some values inside source - backup it before upgrade -
* Don't forget to proceed DB backup before.

== More infos ==

= coding info =
* Add Android as selector - see line #50. - and select a theme named as current with _4touch as end (webkit).
* a newer demo theme default_4touch is available [here](http://dev.xiligroup.com/?p=123#loadlinks "Xilitheme select links").
* readme updated 090218 - see also php code 
* The plugin post is frequently updated [dev.xiligroup.com](http://dev.xiligroup.com/xilitheme-select/ "Xilitheme select")
* See also the [xiligroup plugins forum](http://forum2.dev.xiligroup.com/).
* For WordPress theme's developers, now give specific info if mobile (not apple) use *opera mini* browser.
[see Opera doc](http://dev.opera.com/articles/view/designing-with-opera-mini-in-mind/)
= display current theme view =
If `$wp_ismobile->cookienable` is set to 1, the theme can include a tag to refresh theme as viewing on a desktop browser. See tag example in php code.

== Changelog ==

= 1.0.1 = 
* Add Android as selector - see line #50. - a newer demo theme default_4touch is available in [here](http://dev.xiligroup.com/?p=123#loadlinks "Xilitheme select links").

= 1.0 = 
* Admin settings improvments for latest 2.8 WordPress.

= 0.9.2 = 
* option: now able to detect opera mini browser.

= 0.9.1 = 
* option: now able to display current theme view on iPhone / iPod.

© 2010-11-09 dev.xiligroup.com

== Upgrade Notice ==

* Plugin only use Options table in WP database.
* As usual before upgrading, read carefully the readme.txt and backup your database.
* Read code source if you are developer.

