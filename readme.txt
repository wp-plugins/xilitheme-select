=== Xilitheme select ===
Contributors: MS xiligroup
Donate link: http://dev.xiligroup.com/
Tags: theme, ipod touch, iphone
Requires at least: 2.6.0
Tested up to: 2.6.2
Stable tag: trunk

Xilitheme select provides an automatic selection of theme one for current browsers and another for iphone/ipod touch browser. 

== Description ==
** first public release - preliminary doc - updated 081017 - see also code **
Xilitheme select provides an automatic selection of theme one for current browsers and another for iphone/ipod touch browser.

- this first release must be used by users/webmasters with good knowledge of WP themes.

**prerequisite:**
By default:
In addition of your current selected theme add in the themes folder a theme folder named as the current with extension '_4touch' dedicaced for iphone browser.

**Note about theme with template_page**
Both themes (the current and the one for iphone / ipodtouch) must contain the same template (name) - the contents can differ obviously -

**admin dashboard UI:**
The interface provide ways to change default extension or choose a specific fullname (for the "iphone" theme)


**Options**
See the source of the plugin to discover other type of instantiation of the class `xilithemeselector`. One provide a way to have or not a admin dashboard interface to set the specs with only php coding.
see `$wp_ismobile = new xilithemeselector(true);` at end of code

Soon : More informations on the site [dev.xiligroup.com](http://dev.xiligroup.com/ "Xilitheme select")

== Installation ==

1. Upload `xilithemeselect.php` to the `/wp-content/plugins/` directory,
2. Upload a **specific theme** for iPhone with a folder named as current theme **+ extension** "_4touch" but don't activate it as usual.
3. Activate the plugin through the 'Plugins' menu in WordPress,
4. Go to the dashboard settings tab - Xilitheme select theme.

== Frequently Asked Questions ==

= When I visit the site with iPhone or iPhone simulator, I don't see the specific theme =

Verify that the folder of this theme has a right name with right extension.

= Why the option full name or extension ? =

It is an easiness to choose a theme with a folder without the proposed extension.

- Do you provide a theme for these mobile device as iPhone or iTouch ?

Soon in the next release. But now you can use the famous iwphone theme (2007).

== Screenshots ==

1. an example of wp-content/themes folder
2. the admin settings UI

== Arbitrary section ==

soon more infos...
