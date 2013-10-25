=== PHP Code Widget ===
Contributors: Otto42
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom
Tags: php, widget, execphp
Requires at least: 2.8
Tested up to: 3.7
Stable tag: 2.2
License: GPLv2
License URI: http://www.opensource.org/licenses/GPL-2.0

Like the Text widget, but also allows working PHP code to be inserted.

== Description ==

The normal Text widget allows you to insert arbitrary Text and/or HTML code. This allows that too, but also parses any PHP code in the text widget and executes it. 

This can make it easier to migrate to a widget-based theme. However, this plugin should not be used long term, as anybody with access to edit the widgets on your site will be able to execute arbitrary PHP code.

All PHP code must be enclosed in the standard php opening and closing tags ( `<?php` and `?>` ) for it to be recognized and executed.

Only users with the unfiltered_html role will be allowed to insert unfiltered HTML. This includes PHP code, so users without admin or editor permissions will not be able to use this to execute code, even if they have widget editing permissions.

== Frequently Asked Questions ==

= There's some kind of error on line 26! =

That error means that your PHP code is incorrect or otherwise broken. 

= But my code is fine! =

No, it's not. Really. 

This widget has no real errors in it, it's about the simplest widget one can possibly make. Any errors coming out of the "execphp.php" file are errors in code you put into one of the widgets. The reason that it shows the error being in the execphp.php file is because that is where your widget's code is actually being run.

So, if it says that you have an error on line 26, I assure you, the problem is yours. Please don't email me about that error.

= I have code that works normally in a template but doesn't work when in the widget? =

Code in a template runs in the global context. Code in the widget will run in a function context. Make sure that you declare any global variables as global before attempting to use them.

== Screenshots ==

1. The widgets screen showing a PHP code widget in use.
2. The output of the widget on the site.

== Changelog ==

= 2.2 =
* Translation fixes for WP 3.7
* Remove donation link

= 2.1 =
* Fixed broken wpautop filter. Checkbox on widget works now.

= 2.0 =
* Changed widget to use new Class methods for creating widget. This simplifies the widget and should eliminate any problems with it losing code or disappearing from sidebars and so forth.
