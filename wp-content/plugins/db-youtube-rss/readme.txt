=== DB YouTube RSS ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QNY4BB9LT5VM8
Tags: youtube, you tube, rss, widget
Requires at least: 3.0
Tested up to: 3.1.3
Stable tag: 0.2

Plugin display widget with latest videos from user RSS channel. You can set up username, number of thumbnails and width of thumbnail.

== Description ==

Plugin display widget with latest videos from user channel.

At the admin panel - 'Settings->DB YouTube RSS' you can set up username, number of thumbnails and width of thumbnail.

There are two way to display this list: activate widget DB YouTube RSS or place `<?php if(function_exists('db_yt_rss_markup')) { db_yt_rss_markup(); }; ?>` in your templates.

== Installation ==

1. Upload folder `db-youtube-rss` to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress
1. Activate widget DB YouTube RSS or place `<?php if(function_exists('db_yt_rss_markup')) { db_yt_rss_markup(); }; ?>` in your templates.

== Frequently Asked Questions ==


= Can this plugin be configured to use a YouTube general rss feed? Not a channel feed? =
You need to change two lines (108,109) in db-youtube-rss.php file
There is:

`$db_yt_rss_url = "http://www.youtube.com/rss/user/" . get_option( 'db_yt_user' ) . "/videos.rss";
$db_yt_channel_url = "http://www.youtube.com/user/" . get_option( 'db_yt_user' );`

change for this:

`$db_yt_rss_url = get_option( 'db_yt_user' );
$db_yt_channel_url = get_option( 'db_yt_link' );`

Now you can specify link to any YouTube rss.

== Screenshots ==

1. DB YouTube RSS settings
2. List of latest videos

== Changelog ==

= 0.1 =
* Very first version of plugin
= 0.2 =
* Added possibility to define multiple YouTube users. You can set up different user for each instance of widget.

== Upgrade Notice ==


