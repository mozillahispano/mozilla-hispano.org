=== Wp2BB ===

Contributors: adehoces
Donate link: http://www.alfredodehoces.com/wp2bb
Tags: comments, phpBB, forum, integration
Requires at least: 2.0
Tested up to: 2.6.2
Stable tag: 1.4.1

Wp2BB integrates WordPress and PhpBB. It automatically creates new topics in your forum for the new posts and/or pages in your blog.

== Description ==

Wp2BB integrates your WordPress blog and your phpBB forum. It automatically creates new topics in the forum for every new post in your blog. Provides a Wp template for displaying the current number of  responses to the post in the forum and a quick link to add a reply, and also a widget to display recent forum responses in the sidebar. It can coexist with the WordPress comments system or replace it completely.

== Installation ==

1. Upload 'wp2bb.x.y.zip' to the '/wp-content/plugins/' directory
2. Uncompress the file (should create the 'wp-content/plugins/wp2bb' directory)
3. Create a backup of your WordPress and PhpBB databases, just in case!
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Configure it in 'Options -> Wp2BB' 
6. Place '&lt;?php if (function_exists("wp2bb")) wp2bb(); ?&gt;' in your templates

== Frequently Asked Questions ==

= Will Wp2BB create duplicate content that might be penalised by Google? =

It could theoretically happen if you choose to include the WP post text in the correspondent PhpBB topic, but this is fully configurable in the settings ("Topic message text" option). It is definitely better to create PhpBB topics generic text such as "Discussion about the post 'xxx' in the blog", just using the WP post title

= Can I use images/fonts/colors in the PhpBB topic? =

Yes, the "Topic message text" setting will accept HTML and BBCode

= Can Wp2BB sync registered members in WP/phpBB with single sign on? =

No, Wp2BB does not handle users in any way. This feature could be implemented in the near future. In the mean time you might want to check WP-United. Goes pretty well with Wp2BB
.


== Screenshots ==

1. Wp2BB showing the number of responses to a WP post in the PhpBB forum and a quick link to add a reply

2. Wp2BB administration

3. Recent forum responses widget

== Usage ==

Check http://www.alfredodehoces.com/wp2bb/usage for further details

== TO-DO ==

1. Externalize strings so Wp2BB can be easily translated into other languages
