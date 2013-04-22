=== Plugin Name ===
Contributors: Giuseppe Argento
Tags: phpbb, forum, topics
Requires at least: 1.5
Tested up to: 2.1
Stable tag: trunk
== Description ==

Plugin used to display the most recent topics of your phpBB forum(it works with phpbb 2.* and also phpbb 3.*)
You can set the number of topics to display, wrap long words or exclude a category of the forum.
[Plugin Homepage](http://www.4mj.it/wordpress-phpbb-last-topics-plugin/ "Phpbb Last Topics")

== Installation ==

1. Upload `wp-phpbb.php` to the `/wp-content/plugins/` directory
2. edit it to set:
    $host = “http://www.yourblog.com/phpbb”;
    $forum_path = “phpbb”;
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Insert
<?php if (function_exists('wpphpbb_topics')): ?>
<? wpphpbb_topics(); ?>
<?php endif; ?>
where you want to show the last topics

[Plugin Homepage](http://www.4mj.it/wordpress-phpbb-last-topics-plugin/ "Phpbb Last Topics")