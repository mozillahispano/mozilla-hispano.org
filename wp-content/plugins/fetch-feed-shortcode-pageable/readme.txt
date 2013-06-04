=== Plugin Name ===
Contributors: soundwaves-productions
Plugin Name: Fetch Feed shortcode pageable
Donate link: http://www.soundwaves-productions.com/soundwavesblog/wordpress-plugins/fetch-feed-shortcode-pageable/
Tags: RSS, fetch_feed, RSS fetch feed, RSS shortcode, RSS fetch feed shortcode, feed shortcode, pageable feed, paging, RSS pageable,fetch_feed
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 1.1

This plugin uses the fetch_feed function to display external RSS Feeds on your Wordpress Blog / Site!
This plugin also supports configurable paging  !


== Description ==

This plugin uses the fetch_feed function to display external RSS Feeds on your Wordpress Blog / Site!
This plugin supports paging of the feed !
With this plugin you can configure four item elements to be displayed (like author, date, title, description)

Paging code found here: http://www.phpbuilder.com/board/showthread.php?t=10353797
Thanks to joostdevalk for his plugin:
http://wordpress.org/extend/plugins/rss-shortcode/


Features:

* Display external feeds in your posts or pages by using shortcode
* Set the URL of the feed by using the `feed` parameter
* Configure the number of items per page by using the `pagesize` parameter
* Configure the number of pages in the paging footer  by using the `pagenum` parameter
* Configure the number of total items of the feed by using the `showall` parameter (set to `no`)
* Link or not link the title to the detail page by setting the `linktitle` parameter to `yes` or `no`
* Configure the target of the link by setting the `target` parameter to `_blank` or `_self` (default)
* Configure the desired item elements by setting the `itemelements` parameter (default: itemelements="title,author,date,description")
* Style the output by using CSS


== Installation ==
1. Upload the `fetch-feed-shortcode-pageable` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress
1. Add shortcodes in your posts or pages

e.g.:

[FetchFeedPageable feed="http://www.rottentomatoes.com/syndication/rss/in_theaters.xml" pagesize=5 pagenum=5  linktitle="yes" target="_blank" itemelements="title,date,author,description" ]

== Frequently Asked Questions ==
= How do I include an external RSS Feed to my post or page? =

Use shortcode:

e.g.:
[FetchFeedPageable feed="http://www.rottentomatoes.com/syndication/rss/in_theaters.xml" pagesize=5 pagenum=5  linktitle="yes" target="_blank" itemelements="title,date,author,description" ]


= Can I set number of items per page? =

Yes! just set the `pagesize` parameter of the shortcode

e.g.:
[FetchFeedPageable feed="http://www.rottentomatoes.com/syndication/rss/in_theaters.xml" pagesize=5 pagenum=5  linktitle="yes" target="_blank" itemelements="title,date,author,description" ]


= Can I set number of pages displayed in the paging footer? =

Yes! just set the `pagenum` parameter of the shortcode

e.g.:
[FetchFeedPageable feed="http://www.rottentomatoes.com/syndication/rss/in_theaters.xml" pagesize=5 pagenum=5  linktitle="yes" target="_blank" itemelements="title,date,author,description" ]


= Can I limit the total feed items? =
Yes!  set the `showall` parameter to `no` and the `num` parameter to a desired number

e.g.:
[FetchFeedPageable feed="http://www.rottentomatoes.com/syndication/rss/in_theaters.xml" pagesize=5 num=5 showall=no itemelements="title,date,author,description" ]

= Can I configure the elements I wish to display ? =

Yes!  set the `itemelements` parameter of the shortcode

Four values are available: title,date,author,description

e.g.:
[FetchFeedPageable feed="http://www.rottentomatoes.com/syndication/rss/in_theaters.xml" pagesize=5 pagenum=5  linktitle="yes" target="_blank" itemelements="title,description" ]

= Can I style the output with CSS? =
Yes!  just have a look at the included css file:

`fetch-feed-shortcode-pageable/css/style.css`



== Changelog ==

= 1.1 =
* Configurable link to feed item page
* Configurable feed item elements
* Changed <p> elements to <div> elements

= 1.0 =
* First release
