=== iRate ===
Contributors: sebaxtian
Tags: iratemyday, microblog, microblogging
Requires at least: 2.4
Tested up to: 3.1
Stable tag: 0.9.4

iRate displays your latest iRateMyDay rate in your WordPress blog.

== Description ==

The iRateMyDay official widget just show your image, but not your comment. This plugin fits in a sidebar and show your status and your image too.

It only needs your username, a Widget title (if you want) and the plugin will check your status from time to time.

iRate has been translated to french by the __[InMotion Hosting Team](http://www.inmotionhosting.com/)__. Thanks for your time guys!

Screenshots are in spanish because it's my native language. As you should know yet I __spe'k__ english, and the plugin use it by default.

== Installation ==

1. Decompress irate.zip and upload `/irate/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the __Plugins__ menu in WordPress
3. Add the iRate widget into your side bar.
4. Configure widget title (if you want) and iRateMyDay username.

== Frequently Asked Questions ==

= Is this plugin bug free? =

I don't think so. So far it works with my configuration, but i didn't test it 
with other. Feedbacks would be appreciated.

= Can I set my own CSS? =

Yes. Copy the file irate.css to your theme folder. The plugin will check for it.

== Screenshots ==

1. Widget configuration
2. This is my iRateMyDay status

== Changelog ==

= 0.9.4 =
* Checked for WP 3.1

= 0.9.3 =
* Checked for WP 3.0

= 0.9.2 =
* Solved minor bugs.

= 0.9.1 =
* Using WP functions to add safely scripts and css. 

= 0.9 =
* Stable release

= 0.8.1 =
* Solved bug in the readfile function.

= 0.8 =
* Solved an error in HTML code generation.
* First release to not use minimax.

= 0.7 =
* Modified to use ajax only when the cache is old.

= 0.6 =
* Stable release.

= 0.5.3 =
* Modified the layout and css file to fit when long rates are displayed.

= 0.5.2 =
* First release with belarusian translation.

= 0.5.1 =
* Using nonce to not show data when someone call the ajax script outside the plugin.
* Silence is gold.

= 0.5 =
* Using minimax 0.3

= 0.4.8.1 =
* The code has been indented, documented and standardised.
* Solved a bug with the headers, now iRate works with the plugin POD.

= 0.4.8 =
* Now you can set your own css file (see FAQ).
