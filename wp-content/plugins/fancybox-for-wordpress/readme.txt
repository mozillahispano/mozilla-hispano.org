=== FancyBox for WordPress ===
Contributors: moskis
Tags: fancybox, lightbox, jquery, gallery, image, images, photo, photos, picture, pictures
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 3.0.2
License: GPL/MIT

Seamlessly integrates FancyBox into your blog: Upload, activate, and you're done. Additional configuration optional.


== Description ==

Seamlessly integrates FancyBox into your blog: Upload, activate, and you're done. Additional configuration optional.

You can easely customize almost anything you can think about fancybox: the border, margin width and color, zoom speed, animation type, close button position, overlay color and opacity and even more advanced option like several options to group images into galleries, and more...

By default, the plugin will use jQuery to apply [FancyBox](http://fancybox.net/) to ANY thumbnails that link directly to an image. This includes posts, the sidebar, etc, so you can activate it and it will be applied automatically.

= Demo =

You can see the plugin working on [this blog](http://plugins.josepardilla.com/fancybox-for-wordpress/) although there's nothing amazing to see, just a FancyBox simple implementation, that's the point ;) You can take a look at the code if you're curious, though.

== Changelog ==

This changelog is for the WordPress plugin. For the Fancybox main changelog go to its [home page](http://fancybox.net/changelog/).

= 3.0.2 =
* Added support for disabling fancybox on individual hyperlinked images by adding class='nolightbox'. (Thanks to Artem Russakovskii)
* Added a link to the github project page in the info tab in the settings page.
* Fixed and cleaned the installation code, new installations of the plugin should work now without need to go to the settings page.
* Fixed false positives in filenames. (Thanks to Artem Russakovskii)
* Fixed incompatibility with wordpress installations where the wp-content directory had been renamed.
* Fixed an issue that could cause the version of the plugin to be removed from settings when deactivating the plugin.
* Improved HTTPS support by using better code to retrieve the plugin url and load files.
* Removed legacy code to suport upgrading settings from 2.x versions of the plugin. This was done to avoid possible issues with clean installations of the plugin.
* Updated some CSS rules in jQuery UI
* Some minor reformatting and cleanup of code (PHP comments, empty lines, )

= 3.0.1 =
* Updated: Localization catalog updated.
* Updated: Spanish localization.
* Fixed: Minor change in settings page that may fix options page being invisible in some cases.

= 3.0.0 =
* New: Fancybox v1.3.4 support This includes many new options, like title position.
* New: Additional FancyBox Calls option that lets the user write their own additional code to use FancyBox on specific areas of the blog, like email subscription buttons, login, etc.
* New: Revert settings button added to options page. When pressed, a confirmation dialog will appear.
* New: Improvements in options page, irrelevant settings (settings that depend on a disabled setting) will hide on real time, meaning a cleaner look in the options page.
* Updated: New cleaner code to select thumbnails on which to apply the fancbox script.
* Updated: Many parts of plugins rewriten with many improvements in code.
* Updated: Options are now serialized into a single row in the database.
* Fixed: Plugin should be SSL friendly from now on.
* Fixed: Do not call jQuery option in troubleshooting section didn't work if easing was enabled.
* Fixed: Load at footer options should work better now.
* Fixed: CSS external files now addded with wp_enqueue_style().
* Fixed: has_cap error: User level value for options page removed, using role now instead. Thanks to [vonkanehoffen](http://wordpress.org/support/topic/plugin-fancybox-for-wordpress-has_cap-fix).
* Removed: jQuery "noConflict" Mode option removed bacause jQuery bundled with WordPress always used noConflict.
* Removed: Base64 data ("data:image/gif;base64,AAAA") in left and right fancybox link's backgrounds: It didn't seem to be working and it is usually regarded as suspicious code, so it has been removed.

= 2.7.5 =
* Fixed: Callback arguments are no longer added as "null" when they are not set in options page.

= 2.7.4 =
* Fixed: Little error tagging 2.7.3, a file didn't upload and broke options page.
* Update: Language POT file

= 2.7.3 =
* Fixed: Settings not saving in some browsers. Thanks to [supertomate](http://wordpress.org/support/topic/plugin-fancybox-for-wordpress-save-changes-button-doesnt-submit-form?replies=7#post-1765041)
* Fixed: JS being added to other plugins' configuration pages. Thanks to [Brandon Dove](http://wordpress.org/support/topic/plugin-fancybox-for-wordpress-theres-a-problem-with-is_plugin_page?replies=1#post-1888828)
* Added: Support section in options page with better information

= 2.7.2 =
* Fixed: Layout problem in options page in WordPress 2.9

= 2.7.1 =
* Fixed: Z-index issue was left out in previus release
* Fixed: Setting to close fancybox when clicking on the overlay wasn't available in the menu
* Fixed: Frame width and height options are now in the "Other" tab
* Fixed: Tabs now translated in Spanish localization

= 2.7.0 =
* New: Fancybox v1.2.6 support
* New: New Admin page with tabs for better organization of all the options
* Added: Setting to change the speed of the animation when changing gallery items
* Added: Setting to enable or disable Escape key to close Fancybox
* Added: Setting to show or hide close button
* Added: Setting to close fancybox when clicking on the overlay
* Added: Setting to enable or disable callback function on start, show and close events
* Added: Italian translation
* Added: Russian translation
* Added: "Load JS at Footer" option
* Added: New Changelog tab in  Wordpress Plugin Directory
* Fixed: Some typos in Spanish translation
* Fixed: FancyBox not showing above some elements (those with zindex higher than 90)
* Fixed: JavaScript code being included in all admin pages instead of just the plugin's options page.
* Fixed: noClonflict preventing frames to work in Fancybox
* Fixed: Custom frame width and height not being applied
* Updated: Japanese translation
* Updated: JS is now Minified instead of Packed

= 2.6.0 =
* Optimized the JavaScript code used to apply FancyBox
* Updated Custom Expression section in Options Page
* Fixed uppercase image extensions not being recognized
* CSS is now loaded before the JavaScript for better parallelization
* jquery.easing.1.3.js compressed (from 8,10kb to 3,47kb) and renamed to jquery.easing.1.3.pack.js
* Added Turkish translation (some strings missing)
* Added Japanese translation (some strings missing)
* Updated Spanish translation
* Updated to use new Plugin API in WP2.7 for better forward compatibility
* Removed /wp-content/ reference in fancybox.php for better WP2.8 support
* Optimized some code readability

= 2.5.1 =
* Fixed the plugin not working when selecting Gallery Type "By Post"
* Fixed a bug that would prevent the title in the IMG tag from being copied to the A tag in some cases
* Fixed the Custom Expression showing in the Admin panel when other gallery types are selected

= 2.5 =
* Support for localizations (Spanish and German localizations included)
* Some parts of the code completely rewritten
* Fixed fancybox files being loaded on the admin pages
* New options for close button position, custom jquery expressions, iframe content
* Options page mostly rewritten, better organized
* Medium/advanced, troubleshooting/uninstall options collapsable, hidden by default
* Better support guidelines and links on options page
* Settings link on the Manage plugins page
* Custom expression hidden when not used
* Title atribute on IMG tags is now copied to its parent A tag for better caption support
* New uninstall options and better handling of new options when installing/updating
* Cleans any old options no longer needed when plugin is activated/updated

= 2.2 =
* Updated to FancyBox 1.2.1
* Added new settings to Options Page: Easing, padding size, border color
* Tweaked CSS to prevent some themes from adding unwanted styles to fancybox (especially background colors and link outlines)
* Options Page reorganized in three sections: Appearance, Behaviour and Troubleshooting Settings, to make settings easier to find

= 2.1.1 =
* Fixed a new bug introduced in 2.1 that prevented options from being saved. Sorry about the mess :(

= 2.1 =
* Fixed a major bug in 2.0 that prevented it from working until plugin's options page was visited
* Added two options for troubleshooting that might help in some cases if the plugin doesn't work: disable jQuery noConflict and skip jQuery call
* Additional fixes to caption CSS: Captions should look better now in Hybrid theme, child themes, and other situations where general table elements are improperly styled

= 2.0 =
* Brand new Options Page in Admin Panel lets you easely customize many options: fancybox auto apply, image resize to fit, opacity fade while zooming, zoom speed, overlay on/off, overlay color, overlay opacity, close fancybox on image click, keep fancybox centered while scrolling
* CSS completely updated for FancyBox 1.2.0
* Captions fixed in IE

= 1.3 =
* Shadows and Close button should be fixed now

= 1.2 =
* Updated to FancyBox 1.2.0
* Uses packed version of the JavaScript file (8kb instead of 14kb)

= 1.1 =
* Fixed FancyBox not being applied to .jpeg files
* Fixed "Click to close" overlay text
* Moved images to /img/ folder


== Installation ==

1. Upload the `fancybox-for-wordpress` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it, [FancyBox](http://fancybox.net/) will be automatically applied to all your image links and galleries.
4. If you want to customize a bit the look and feel of FancyBox, go to the Options Page under General Options in the WordPress Admin panel


== Screenshots ==

1. Simple example of fancybox on a post. [Live demo here](http://plugins.josepardilla.com/fancybox-for-wordpress/)
2. Basic settings on Options Page in the Admin Panel. This makes it very easy to customize the plugin to your needs


== Frequently Asked Questions ==

You can find a FAQ section ay [plugins.josepardilla.com](http://plugins.josepardilla.com/fancybox-for-wordpress/faq/).