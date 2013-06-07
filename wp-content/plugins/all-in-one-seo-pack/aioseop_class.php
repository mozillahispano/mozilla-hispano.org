<?php
/**
 * @package All-in-One-SEO-Pack
 */
/**
 * Include the module base class.
 */
require_once( 'aioseop_module_class.php' );
/**
 * The main class.
 */
class All_in_One_SEO_Pack extends All_in_One_SEO_Pack_Module {

	/** The current version of the plugin. **/
 	var $version = AIOSEOP_VERSION;
 	
 	/** Max numbers of chars in auto-generated description */
 	var $maximum_description_length = 160;
 	
 	/** Minimum number of chars an excerpt should be so that it can be used
 	 * as description. Touch only if you know what you're doing
 	 */
 	var $minimum_description_length = 1;
 	
	/** Whether output buffering is already being used during forced title rewrites. **/
 	var $ob_start_detected = false;

 	/** The start of the title text in the head section for forced title rewrites. **/
 	var $title_start = -1;
 	
 	/** The end of the title text in the head section for forced title rewrites. **/
 	var $title_end = -1;
 	
 	/** The title before rewriting */
 	var $orig_title = '';
 	 	
 	/** Filename of log file. */
 	var $log_file;
 	
 	/** Flag whether there should be logging. */
 	var $do_log;
 	
	function All_in_One_SEO_Pack() {
		global $aioseop_options;
		$this->log_file = dirname( __FILE__ ) . '/all_in_one_seo_pack.log';
		if ( $aioseop_options['aiosp_do_log'] )
			$this->do_log = true;
		else
			$this->do_log = false;

		$this->init();
		global $aioseop_plugin_name;
		$aioseop_plugin_name = __( 'All in One SEO Pack', 'all_in_one_seo_pack' );
		if ( ! defined( 'AIOSEOP_PLUGIN_NAME' ) )
		    define( 'AIOSEOP_PLUGIN_NAME', $aioseop_plugin_name );
		$this->name = sprintf( __( '%s Plugin Options', 'all_in_one_seo_pack' ), AIOSEOP_PLUGIN_NAME );
		$this->menu_name = __( 'General Settings', 'all_in_one_seo_pack' );
		
		$this->prefix = 'aiosp_';						// option prefix
		$this->option_name = 'aioseop_options';
		$this->store_option = true;
		$this->file = __FILE__;								// the current file
		parent::__construct();
		$this->default_options = array( 
		   "donate"=> Array( 
		       'name' => __( 'I enjoy this plugin and have made a donation:', 'all_in_one_seo_pack' ), 
		       'help_text' => __( 'All donations support continued development of this free software.', 'all_in_one_seo_pack' ),
		       'default' => 0),
		   "home_title"=> Array( 
				'name' => __( 'Home Title:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'As the name implies, this will be the title of your homepage. This is independent of any other option. If not set, the default blog title will get used.', 'all_in_one_seo_pack' ), 
				'default' => null, 'type' => 'textarea', 'sanitize' => 'text' ),
		   "home_description"=> Array( 
				'name' => __( 'Home Description:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'The META description for your homepage. Independent of any other options, the default is no META description at all if this is not set.', 'all_in_one_seo_pack' ), 
				'default' => '', 'type' => 'textarea', 'sanitize' => 'text' ),
		   "togglekeywords" => Array( 
				'name' => __( 'Use Keywords:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'This option allows you to toggle the use of meta keywords throughout the whole of the site.', 'all_in_one_seo_pack' ), 
				'default' =>  0,
				'type' => 'radio',
			    'initial_options' => Array( 0 => __( 'Enabled', 'all_in_one_seo_pack' ),
			                                1 => __( 'Disabled', 'all_in_one_seo_pack' ) )
				),
		   "home_keywords"=> Array( 
				'name' => __( 'Home Keywords (comma separated):', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'A comma separated list of your most important keywords for your site that will be written as META keywords on your homepage. Don\'t stuff everything in here.', 'all_in_one_seo_pack' ), 
				'default' => null, 'type' => 'textarea', 'sanitize' => 'text',
				'condshow' => Array( "aiosp_togglekeywords" => 0 ) ),
		   "can"=> Array(
				'name' => __( 'Canonical URLs:', 'all_in_one_seo_pack' ),
				'help_text' => __( "This option will automatically generate Canonical URLS for your entire WordPress installation.  This will help to prevent duplicate content penalties by <a href='http://googlewebmastercentral.blogspot.com/2009/02/specify-your-canonical.html' target='_blank'>Google</a>.", 'all_in_one_seo_pack' ),
				'default' => 1),
			"rewrite_titles"=> Array( 
				'name' => __( 'Rewrite Titles:', 'all_in_one_seo_pack' ), 
				'help_text' => __( "Note that this is all about the title tag. This is what you see in your browser's window title bar. This is NOT visible on a page, only in the window title bar and of course in the source. If set, all page, post, category, search and archive page titles get rewritten. You can specify the format for most of them. For example: The default templates puts the title tag of posts like this: ‚ÄúBlog Archive >> Blog Name >> Post Title‚Äù (maybe I've overdone slightly). This is far from optimal. With the default post title format, Rewrite Title rewrites this to ‚ÄúPost Title | Blog Name‚Äù. If you have manually defined a title (in one of the text fields for All in One SEO Plugin input) this will become the title of your post in the format string.", 'all_in_one_seo_pack' ),
				'default' => 1,
				'type' => 'radio',
				'initial_options' => Array( 1 => __( 'Enabled', 'all_in_one_seo_pack' ),
											0 => __( 'Disabled', 'all_in_one_seo_pack' ) )
				),
			"force_rewrites"=> Array( 
				'name' => __( 'Force Rewrites:', 'all_in_one_seo_pack' ), 
				'help_text' => __( "Use output buffering to ensure that the title gets rewritten.", 'all_in_one_seo_pack' ),
				'default' => 1,
				'type' => 'hidden',
				'prefix' => $this->prefix,
				'initial_options' => Array( 1 => __( 'Enabled', 'all_in_one_seo_pack' ),
											0 => __( 'Disabled', 'all_in_one_seo_pack' ) )
				),
			"use_original_title"=> Array(
					'name' => __( 'Use Original Title:', 'all_in_one_seo_pack' ),
					'help_text' => __( "Use wp_title to set the title; disable this option if you run into conflicts with the title being set by your theme or another plugin.", 'all_in_one_seo_pack' ),
					'type' => 'radio',
					'default' => 0,
					'initial_options' => Array( 1 => __( 'Enabled', 'all_in_one_seo_pack' ),
												0 => __( 'Disabled', 'all_in_one_seo_pack' ) )					
				),
			"cap_titles"=> Array(
				'name' => __( 'Capitalize Titles:', 'all_in_one_seo_pack' ),
				'help_text' => __( "Check this and Search Page Titles and Tag Page Titles will have the first letter of each word capitalized.", 'all_in_one_seo_pack' ),
				'default' => 1),
			"cap_cats"=> Array(
				'name' => __( 'Capitalize Category Titles:', 'all_in_one_seo_pack' ),
				'help_text' => __( "Check this and Category Titles will have the first letter of each word capitalized.", 'all_in_one_seo_pack' ),
				'default' => 1),
		   "page_title_format"=> Array( 
				'name' => __( 'Page Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => 
				__( 'The following macros are supported:', 'all_in_one_seo_pack' )
				. '<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%page_title% - The original title of the page', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%category_title% - The (main) category of the page', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%category% - Alias for %category_title%', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%page_author_login% - This page's author' login", 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%page_author_nicename% - This page's author' nicename", 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%page_author_firstname% - This page's author' first name (capitalized)", 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%page_author_lastname% - This page's author' last name (capitalized)", 'all_in_one_seo_pack' ) . '</li>' . 
				'</ul>',
				'type' => 'text',
				'default' => '%page_title% | %blog_title%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "post_title_format"=> Array( 
				'name' => __( 'Post Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => 
				__( 'The following macros are supported:', 'all_in_one_seo_pack' )
				. '<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%post_title% - The original title of the post', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%category_title% - The (main) category of the post', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%category% - Alias for %category_title%', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%post_author_login% - This post's author' login", 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%post_author_nicename% - This post's author' nicename", 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%post_author_firstname% - This post's author' first name (capitalized)", 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( "%post_author_lastname% - This post's author' last name (capitalized)", 'all_in_one_seo_pack' ) . '</li>' . 
				'</ul>',
				'type' => 'text',
				'default' => '%post_title% | %blog_title%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "category_title_format"=> Array( 
				'name' => __( 'Category Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => 
				__( 'The following macros are supported:', 'all_in_one_seo_pack' ) .
				'<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%category_title% - The original title of the category', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%category_description% - The description of the category', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',
				'default' => '%category_title% | %blog_title%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "archive_title_format"=> Array(
				'name' => __( 'Archive Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => 
				__( 'The following macros are supported:', 'all_in_one_seo_pack' ) . 
				'<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%date% - The original archive title given by wordpress, e.g. "2007" or "2007 August"', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',
				'default' => '%date% | %blog_title%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "tag_title_format"=> Array( 
				'name' => __( 'Tag Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => 
				__( 'The following macros are supported:', 'all_in_one_seo_pack' ) . 
				'<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%tag% - The name of the tag', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',		
				'default' => '%tag% | %blog_title%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "search_title_format"=> Array( 
				'name' => __( 'Search Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => 
				__( 'The following macros are supported:', 'all_in_one_seo_pack' ) . 
				'<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%search% - What was searched for', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',
				'default' => '%search% | %blog_title%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "description_format"=> Array( 
				'name' => __( 'Description Format', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'The following macros are supported:', 'all_in_one_seo_pack' ) . 
				'<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%description% - The original description as determined by the plugin, e.g. the excerpt if one is set or an auto-generated one if that option is set', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%wp_title% - The original wordpress title, e.g. post_title for posts', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',
				'default' => '%description%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
		   "404_title_format"=> Array( 
				'name' => __( '404 Title Format:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'The following macros are supported:', 'all_in_one_seo_pack' ) .
				'<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%request_url% - The original URL path, like "/url-that-does-not-exist/"', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%request_words% - The URL path in human readable form, like "Url That Does Not Exist"', 'all_in_one_seo_pack' ) . '</li><li>' . 
				__( '%404_title% - Additional 404 title input"', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',
				'default' => 'Nothing found for %request_words%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
			"paged_format"=> Array(
				'name' => __( 'Paged Format:', 'all_in_one_seo_pack' ),
				'help_text' =>	  __( 'This string gets appended/prepended to titles when they are for paged index pages (like home or archive pages).', 'all_in_one_seo_pack' )
								. __( 'The following macros are supported:', 'all_in_one_seo_pack' )
								. '<ul><li>' . __( '%page% - The page number', 'all_in_one_seo_pack' ) . '</li></ul>',
				'type' => 'text',
				'default' => ' - Part %page%',
				'condshow' => Array( "aiosp_rewrite_titles" => 1 ) ),
			"enablecpost"=> Array(
				'name' => __( 'SEO for Custom Post Types:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Check this if you want your enable AIOSEOP support for Custom Post Types on this site.', 'all_in_one_seo_pack' ),
				'default' => 'on',
				'type' => 'radio',
				'initial_options' => Array( 'on' => __( 'Enabled', 'all_in_one_seo_pack' ),
											0 => __( 'Disabled', 'all_in_one_seo_pack' ) )
				),
			"cpostadvanced" => Array(
				'name' => __( 'Enable Advanced Options:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'This allows you to do more with your custom post types.', 'all_in_one_seo_pack' ),
				'default' => 0,
				'type' => 'radio',
				'initial_options' => Array( 'on' => __( 'Enabled', 'all_in_one_seo_pack' ),
											0 => __( 'Disabled', 'all_in_one_seo_pack' ) ),
				'label' => null,
				'condshow' => Array( "aiosp_enablecpost" => 'on' )
				),
			"cpostactive" => Array(
				'name' => __( 'SEO on only these post types:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'Select specific custom post types for AIOSEOP support.', 'all_in_one_seo_pack' ),
				'type' => 'multicheckbox',
				'default' => array('post', 'page'),
				'condshow' => Array( 'aiosp_enablecpost' => 'on', 'aiosp_cpostadvanced' => 'on' )
				),
			"cposttitles" => Array(
				'name' => __( 'Custom titles:', 'all_in_one_seo_pack' ), 
				'help_text' => __( 'Allows setting specific custom titles for all post types.', 'all_in_one_seo_pack' ),
				'type' => 'checkbox',
				'default' => 0,
				'condshow' => Array( 'aiosp_enablecpost' => 'on', 'aiosp_cpostadvanced' => 'on' )
				),
			"posttypecolumns" => Array(
				'name' => __( 'Show Column Labels for Custom Post Types:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Choose which post types you want to have SEO columns on the edit.php screen. You can select as many as you like.', 'all_in_one_seo_pack' ),
				'type' => 'multicheckbox',
				'default' =>  array('post', 'page') ),
			"admin_bar" => Array(
				'name' => __( 'Display Menu In Admin Bar:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Check this for easy access to settings from the admin menu bar.', 'all_in_one_seo_pack' ),
				'default' => 'on',
				),
			"custom_menu_order" => Array(
				'name' => __( 'Display Menu At The Top:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Check this for easy access to settings from the top of your WordPress Dashboard.', 'all_in_one_seo_pack' ),
				'default' => 'on',
				),
			"google_verify" => Array(
				'name' => __( 'Google Webmaster Tools:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Enter your verification code here to add a meta tag to your homepage to verify your site.', 'all_in_one_seo_pack' ),
				'default' => '', 'type' => 'text' ),
			"bing_verify" => Array(
				'name' => __( 'Bing Webmaster Center:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Enter your verification code here to add a meta tag to your homepage to verify your site.', 'all_in_one_seo_pack' ),
				'default' => '', 'type' => 'text' ),
			"pinterest_verify" => Array(
				'name' => __( 'Pinterest Site Verification:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Enter your verification code here to add a meta tag to your homepage to verify your site.', 'all_in_one_seo_pack' ),
				'default' => '', 'type' => 'text' ),
			"google_publisher"=> Array(
				'name' => __( 'Google Plus Default Profile:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Enter your Google Plus Profile URL here to link your site\'s pages to Google Plus.', 'all_in_one_seo_pack' ),
				'default' => '', 'type' => 'text' ),
			"google_disable_profile"=> Array(
				'name' => __( 'Disable Google Plus Profile:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Check this option to remove the Google Plus Profile field on user profile pages.', 'all_in_one_seo_pack' ),
				'default' => 0, 'type' => 'checkbox' ),
			"google_analytics_id"=> Array(
				'name' => __( 'Google Analytics ID:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Enter your Google Analytics ID here to track your site with Google Analytics.', 'all_in_one_seo_pack' ),
				'default' => null, 'type' => 'text' ),
			"ga_domain"=> Array(
				'name' => __( 'Tracking Domain:', 'all_in_one_seo_pack' ),
				'type' => 'text',
				'help_text' => __( 'Enter domain name for tracking with Google Analytics.', 'all_in_one_seo_pack' ),
				'condshow' => Array( 'aiosp_google_analytics_id' => Array( 'lhs' => 'aiosp_google_analytics_id', 'op' => '!=', 'rhs' => '' ) ) ),
			"ga_multi_domain"=> Array(
				'name' => __( 'Track Multiple Domains:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Enable multi-domain tracking for Google Analytics.', 'all_in_one_seo_pack' ),
				'default' => 0,
				'condshow' => Array( 'aiosp_google_analytics_id' => Array( 'lhs' => 'aiosp_google_analytics_id', 'op' => '!=', 'rhs' => '' ) ) ),
			"ga_track_outbound_links"=> Array(
				'name' => __( 'Track Outbound Links:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Add functionality to track outbound links with Google Analytics.', 'all_in_one_seo_pack' ),
				'default' => 0,
				 'condshow' => Array( 'aiosp_google_analytics_id' => Array( 'lhs' => 'aiosp_google_analytics_id', 'op' => '!=', 'rhs' => '' ) ) ),
			"use_categories"=> Array(
				'name' => __( 'Use Categories for META keywords:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'Check this if you want your categories for a given post used as the META keywords for this post (in addition to any keywords and tags you specify on the post edit page).', 'all_in_one_seo_pack' ),
				'default' => 0,
				'condshow' => Array( "aiosp_togglekeywords" => 0 ) ),
			"use_tags_as_keywords" => Array(
				'name' => __( 'Use Tags for META keywords:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Check this if you want your tags for a given post used as the META keywords for this post (in addition to any keywords you specify on the post edit page).', 'all_in_one_seo_pack' ),
				'default' => 1,
				'condshow' => Array( "aiosp_togglekeywords" => 0 ) ),
			"dynamic_postspage_keywords"=> Array(
				'name' => __( 'Dynamically Generate Keywords for Posts Page:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'Check this if you want your keywords on a custom posts page (set it in options->reading) to be dynamically generated from the keywords of the posts showing on that page.  If unchecked, it will use the keywords set in the edit page screen for the posts page.', 'all_in_one_seo_pack' ),
				'default' => 1,
				'condshow' => Array( "aiosp_togglekeywords" => 0 ) ),
			"category_noindex"=> Array(
				'name' => __( 'Use noindex for Categories:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'Check this for excluding category pages from being crawled. Useful for avoiding duplicate content.', 'all_in_one_seo_pack' ),
				'default' => 1),
			"archive_noindex"=> Array(
				'name' => __( 'Use noindex for Archives:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'Check this for excluding archive pages from being crawled. Useful for avoiding duplicate content.', 'all_in_one_seo_pack' ),
				'default' => 1),
			"tags_noindex"=> Array(
				'name' => __( 'Use noindex for Tag Archives:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'Check this for excluding tag pages from being crawled. Useful for avoiding duplicate content.', 'all_in_one_seo_pack' ),
				'default' => 0),
			"generate_descriptions"=> Array(
				'name' => __( 'Autogenerate Descriptions:', 'all_in_one_seo_pack' ),
				'help_text' => __( "Check this and your META descriptions will get autogenerated if there's no excerpt.", 'all_in_one_seo_pack' ),
				'default' => 1),
			"hide_paginated_descriptions"=> Array(
				'name' => __( 'Remove Descriptions For Paginated Pages:', 'all_in_one_seo_pack' ),
				'help_text' => __( "Check this and your META descriptions will get hidden if on Page 2 or later of paginated content.", 'all_in_one_seo_pack' ),
				'default' => 0),
			"unprotect_meta"=> Array(
				'name' => __( 'Unprotect Post Meta Fields:', 'all_in_one_seo_pack' ),
				'help_text' => __( "Unprotect internal postmeta fields for use with XMLRPC -- if you don't know what that is, leave it unchecked.", 'all_in_one_seo_pack' ),
				'default' => 0),
			"ex_pages" => Array(
				'name' => __( 'Exclude Pages:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( "Enter any comma separated pages here to be excluded by All in One SEO Pack.  This is helpful when using plugins which generate their own non-WordPress dynamic pages.  Ex: <em>/forum/,/contact/</em>  For instance, if you want to exclude the virtual pages generated by a forum plugin, all you have to do is give forum or /forum or /forum/ or and any URL with the word \"forum\" in it, such as http://mysite.com/forum or http://mysite.com/forum/someforumpage will be excluded from All in One SEO Pack.", 'all_in_one_seo_pack' ),
				'type' => 'textarea', 'default' =>  '' ),
			"post_meta_tags"=> Array(
				'name' => __( 'Additional Post Headers:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'What you enter here will be copied verbatim to your header on posts.', 'all_in_one_seo_pack' ) . ' ' . __( 'You can enter whatever additional headers you want here, even references to stylesheets.', 'all_in_one_seo_pack' ),
				'type' => 'textarea', 'default' => '', 'sanitize' => 'default' ),
			"page_meta_tags"=> Array(
				'name' => __( 'Additional Page Headers:', 'all_in_one_seo_pack' ),
				'help_text' => __( 'What you enter here will be copied verbatim to your header on pages.', 'all_in_one_seo_pack' ) . ' ' . __( 'You can enter whatever additional headers you want here, even references to stylesheets.', 'all_in_one_seo_pack' ),
				'type' => 'textarea', 'default' => '', 'sanitize' => 'default' ),
			"front_meta_tags"=> Array(
				'name' => __( 'Additional Front Page Headers:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'What you enter here will be copied verbatim to your header on the front page if you have a static page set in Settings, Reading.', 'all_in_one_seo_pack' ) . ' ' . __( 'You can enter whatever additional headers you want here, even references to stylesheets.', 'all_in_one_seo_pack' ) . ' ' . __( 'This will fall back to using Additional Page Headers if you have them set and nothing is entered here.', 'all_in_one_seo_pack' ),
				'type' => 'textarea', 'default' => '', 'sanitize' => 'default' ),
			"home_meta_tags"=> Array(
				'name' => __( 'Additional Blog Page Headers:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'What you enter here will be copied verbatim to your header on the home page if you have Front page displays your latest posts selected in Settings, Reading.  It will also be copied verbatim to your header on the Posts page if you have one set in Settings, Reading.', 'all_in_one_seo_pack' ) . ' ' . __( 'You can enter whatever additional headers you want here, even references to stylesheets.', 'all_in_one_seo_pack' ),
				'type' => 'textarea', 'default' => '', 'sanitize' => 'default' ),
			"do_log"=> Array(
				'name' => __( 'Log important events:', 'all_in_one_seo_pack' ),
				'help_text' => 	__( 'Check this and SEO pack will create a log of important events (all_in_one_seo_pack.log) in its plugin directory which might help debugging it. Make sure this directory is writable.', 'all_in_one_seo_pack' ),
				'default' => null ),
			);
			
			$this->locations = Array(
					'default' => Array( 'name' => $this->name, 'prefix' => 'aiosp_', 'type' => 'settings', 'options' => null ),
				    'aiosp' => Array( 'name' => $this->plugin_name, 'type' => 'metabox', 'prefix' => '',
																	'options' => Array( 'edit', 'nonce-aioseop-edit', 'upgrade', 'snippet', 'title', 'description', 'keywords', 'noindex', 'nofollow', 'titleatr', 'menulabel', 'disable', 'disable_analytics' ),
																	'default_options' => Array( 
																		'edit' 				 => Array( 'type' => 'hidden', 'default' => 'aiosp_edit', 'prefix' => true, 'nowrap' => 1 ),
																		'nonce-aioseop-edit' => Array( 'type' => 'hidden', 'default' => null, 'prefix' => false, 'nowrap' => 1 ),
																		'upgrade' 			 => Array( 'type' => 'html', 'label' => 'none',
																										'default' => '<a target="__blank" href="http://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/">' 
																										. __( 'Upgrade to All in One SEO Pack Pro Version', 'all_in_one_seo_pack' ) . '</a>'
																					 		),
																		'snippet'			 => Array( 'name' => __( 'Preview Snippet', 'all_in_one_seo_pack' ), 'type' => 'custom', 'help_text' => __( 'A preview of what this page might look like in search engine results.', 'all_in_one_seo_pack' ), 'label' => 'top', 'default' => '<div class="preview_snippet"><div id="aioseop_snippet"><h3><a>%s</a></h3><div><div><cite>%s</cite></div><span>%s</span></div></div></div>' ),
																		'title'				 => Array( 'name' => __( 'Title', 'all_in_one_seo_pack' ), 'type' => 'text', 'help_text' => __( 'A custom title that shows up in the title tag for this page.', 'all_in_one_seo_pack' ), 'count' => true, 'size' => 60 ),
																		'description'		 => Array( 'name' => __( 'Description', 'all_in_one_seo_pack' ), 'type' => 'textarea', 'help_text' => __( 'The META description for this page. This will override any autogenerated descriptions.', 'all_in_one_seo_pack' ), 'count' => true, 'cols' => 80, 'rows' => 2 ),
																		'keywords'			 => Array( 'name' => __( 'Keywords (comma separated)', 'all_in_one_seo_pack' ), 'type' => 'text', 'help_text' => __( 'A comma separated list of your most important keywords for this page that will be written as META keywords.', 'all_in_one_seo_pack' ) ),
																		'noindex'			 => Array( 'name' => __( "Robots Meta NOINDEX", 'all_in_one_seo_pack' ), 'help_text' => __( 'Check this box to ask search engines not to index this page.', 'all_in_one_seo_pack' ) ),
																		'nofollow'			 => Array( 'name' => __( "Robots Meta NOFOLLOW", 'all_in_one_seo_pack' ), 'help_text' => __( 'Check this box to ask search engines not to follow links from this page.', 'all_in_one_seo_pack' ) ),
																		'titleatr'			 => Array( 'name' => __( 'Title Attribute', 'all_in_one_seo_pack' ), 'type' => 'text', 'help_text' => __( 'Set the title attribute for menu links.', 'all_in_one_seo_pack' ), 'size' => 60 ),
																		'menulabel'			 => Array( 'name' => __( 'Menu Label', 'all_in_one_seo_pack' ), 'type' => 'text', 'help_text' => __( 'Set the label for this page menu item.', 'all_in_one_seo_pack' ), 'size' => 60 ),
																		'disable'			 => Array( 'name' => __( 'Disable on this page/post', 'all_in_one_seo_pack' ), 'help_text' => __( 'Disable SEO on this page.', 'all_in_one_seo_pack' ) ),
																		'disable_analytics'	 => Array( 'name' => __( 'Disable Google Analytics', 'all_in_one_seo_pack' ), 'help_text' => __( 'Disable Google Analytics on this page.', 'all_in_one_seo_pack' ), 'condshow' => Array( 'aiosp_disable' => 'on' ) ) ),
																	'display' => null )
				);
			
			$this->layout = Array(
				'default' => Array(
						'name' => __( 'General Settings', 'all_in_one_seo_pack' ),
						'options' => Array() // this is set below, to the remaining options -- pdb
					),
				'home'  => Array(
						'name' => __( 'Home Page Settings', 'all_in_one_seo_pack' ),
						'options' => Array( 'home_title', 'home_description', 'home_keywords' )
					),
				'keywords' => Array(
					'name' => __( 'Keyword Settings', 'all_in_one_seo_pack' ),
					'options' => Array( "togglekeywords", "use_categories", "use_tags_as_keywords", "dynamic_postspage_keywords" )
					),
				'title'	=> Array(
						'name' => __( 'Title Settings', 'all_in_one_seo_pack' ),
						'options' => Array( "rewrite_titles", "force_rewrites", "cap_titles", "cap_cats", "page_title_format", "post_title_format", "category_title_format", "archive_title_format",
						 					"tag_title_format", "search_title_format", "description_format", "404_title_format", "paged_format" )						
					),
				'cpt' => Array(
						'name' => __( 'Custom Post Type Settings', 'all_in_one_seo_pack' ),
						'options' => Array( "enablecpost", "cpostadvanced", "cpostactive", "cposttitles" )
					),
				'display' => Array(
						'name' => __( 'Display Settings', 'all_in_one_seo_pack' ),
						'options' => Array( "posttypecolumns", "admin_bar", "custom_menu_order" )
					),
				'webmaster' => Array(
						'name' => __( 'Webmaster Verification', 'all_in_one_seo_pack' ),
						'options' => Array( "google_verify", "bing_verify", "pinterest_verify" )
					),
				'google' => Array(
						'name' => __( 'Google Settings', 'all_in_one_seo_pack' ),
						'options' => Array( "google_publisher", "google_disable_profile", "google_analytics_id", "ga_domain", "ga_multi_domain", "ga_track_outbound_links" )
					),
				'noindex' => Array(
						'name' => __( 'Noindex Settings', 'all_in_one_seo_pack' ),
						'options' => Array( 'category_noindex', 'archive_noindex', 'tags_noindex' )
					),
				'advanced' => Array(
						'name' => __( 'Advanced Settings', 'all_in_one_seo_pack' ),
						'options' => Array( 'generate_descriptions', 'hide_paginated_descriptions', 'unprotect_meta', 'ex_pages', 'post_meta_tags', 'page_meta_tags', 'front_meta_tags', 'home_meta_tags' )
					)
				);

			$other_options = Array();
			foreach( $this->layout as $k => $v )
				$other_options = array_merge( $other_options, $v['options'] );
			
			$this->layout['default']['options'] = array_diff( array_keys( $this->default_options ), $other_options );
			
			if ( is_admin() ) {
				add_action( "aioseop_global_settings_header",	Array( $this, 'display_right_sidebar' ) );
				add_action( "aioseop_global_settings_footer",	Array( $this, 'display_settings_footer' ) );
				add_action( "output_option", Array( $this, 'custom_output_option' ), 10, 2 );
			}
	}
	
	/*** Use custom callback for outputting snippet ***/
	function custom_output_option( $buf, $args ) {
		if ( $args['name'] == 'aiosp_snippet' )  {
			global $post, $aioseop_options, $wp_query;
			if ( is_object( $post ) ) {
				$post_id = $post->ID;
				$p = $post; $w = $wp_query;
				if (! $post->post_modified_gmt != '' )
					$wp_query = new WP_Query( array( 'p' => $post_id, 'post_type' => $post->post_type ) );
				if ( $post->post_type == 'page' )
					$wp_query->is_page = true;
				elseif ( $post->post_type == 'attachment' )
					$wp_query->is_attachment = true;
				else
					$wp_query->is_single = true;
				if 	( get_option( 'show_on_front' ) == 'page' ) {
					if ( is_page() && $post->ID == get_option( 'page_on_front' ) )
						$wp_query->is_front_page = true;
					elseif ( $post->ID == get_option( 'page_for_posts' ) )
						$wp_query->is_home = true;
				}
				
				$args['options']['type'] = 'html';
				$args['options']['nowrap'] = false;
				$args['options']['save'] = false;
				
				$title = $this->wp_title();
				if ( empty( $title ) ) $title = $post->post_title;
				
				if ( ( $aioseop_options['aiosp_can'] ) && ( $url = $this->aiosp_mrt_get_url( $wp_query ) ) )
					$url = apply_filters( 'aioseop_canonical_url', $url );
				if ( !$url ) $url = get_permalink();

				$description = $this->get_post_description( $post );
				
				if ( strlen( $title ) > 70 ) $title = $this->trim_excerpt_without_filters( $title, 70 ) . '...';
				if ( strlen( $description ) > 156 ) $description = $this->trim_excerpt_without_filters( $description, 156 ) . '...';
				
				$args['value'] = sprintf( $args['value'], esc_attr( strip_tags( $title ) ), esc_url( $url ), esc_attr( strip_tags( $description ) ) );
				$buf = $this->get_option_row( $args['name'], $args['options'], $args );
				
				wp_reset_postdata();
				$wp_query = $w; $post = $p;
			}
		}
		return $buf;
	}
	
	function add_page_icon() {
		wp_enqueue_script( 'wp-pointer', false, array( 'jquery' ) );
		wp_enqueue_style( 'wp-pointer' );
		$this->add_admin_pointers();
		?>
	    <style>
	        #toplevel_page_all-in-one-seo-pack-aioseop_class .wp-menu-image {
	            background: url(<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>shield-sprite-16.png) no-repeat 6px 6px !important;
	        }
	        #toplevel_page_all-in-one-seo-pack-aioseop_class .wp-menu-image img {
	            display: none;
	        }
	        #toplevel_page_all-in-one-seo-pack-aioseop_class:hover .wp-menu-image, #toplevel_page_all-in-one-seo-pack-aioseop_class.wp-has-current-submenu .wp-menu-image {
	            background-position: 6px -26px !important;
	        }
	        #icon-aioseop.icon32 {
	            background: url(<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>shield32.png) no-repeat left top !important;
	        }
			#aioseop_settings_header #message {
				padding: 5px 0px 5px 50px;
				background-image: url(<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>update32.png);
				background-repeat: no-repeat;
				background-position: 10px;
				font-size: 14px;
				min-height: 32px;
			}

	        @media
	        only screen and (-webkit-min-device-pixel-ratio: 1.5),
	        only screen and (   min--moz-device-pixel-ratio: 1.5),
	        only screen and (     -o-min-device-pixel-ratio: 3/2),
	        only screen and (        min-device-pixel-ratio: 1.5),
	        only screen and (                min-resolution: 1.5dppx) {

	            #toplevel_page_all-in-one-seo-pack-aioseop_class .wp-menu-image {
	                background-image: url('<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>shield-sprite-32.png') !important;
	                -webkit-background-size: 16px 48px !important;
	                -moz-background-size: 16px 48px !important;
	                background-size: 16px 48px !important;
	            } 

	            #icon-aioseop.icon32 {
	                background-image: url('<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>shield64.png') !important;
	                -webkit-background-size: 32px 32px !important;
	                -moz-background-size: 32px 32px !important;
	                background-size: 32px 32px !important;
	            }
	
				#aioseop_settings_header #message {
					background-image: url(<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>update64.png) !important;
				    -webkit-background-size: 32px 32px !important;
				    -moz-background-size: 32px 32px !important;
				    background-size: 32px 32px !important;
				}
	        }
	    </style>
		<script>
			function aioseop_show_pointer( handle, value ) {
				if ( typeof( jQuery ) != 'undefined' ) {
					var p_edge = 'bottom';
					var p_align = 'center';
					if ( typeof( jQuery( value.pointer_target ).pointer) != 'undefined' ) {
						if ( typeof( value.pointer_edge ) != 'undefined' ) p_edge = value.pointer_edge;
						if ( typeof( value.pointer_align ) != 'undefined' ) p_align = value.pointer_align;
						jQuery(value.pointer_target).pointer({
									content    : value.pointer_text,
									position: {
										edge: p_edge,
										align: p_align
									},
									close  : function() {
										jQuery.post( ajaxurl, {
											pointer: handle,
											action: 'dismiss-wp-pointer'
										});
									}
								}).pointer('open');
					}
				}
			}
			<?php 
			if ( !empty( $this->pointers ) ) {
			?>
			if ( typeof( jQuery ) != 'undefined' ) {
				jQuery(document).ready(function() {
					var admin_pointer;
					var admin_index;
					<?php 
						foreach( $this->pointers as $k => $p )
							if ( !empty( $p["pointer_scope"] ) && ( $p["pointer_scope"] == 'global' ) ) {
								?>admin_index = "<?php echo esc_attr($k); ?>";
								admin_pointer = <?php echo json_encode( $p ); ?>;
								aioseop_show_pointer( admin_index, admin_pointer );
								<?php
							}
					?>
				});
			}
			<?php
			}
			?>
		</script>
		<?php
	}
	
	function add_page_hooks() {
		$post_objs = get_post_types( '', 'objects' );
		$pt = array_keys( $post_objs );
		$rempost = array( 'revision', 'nav_menu_item' );
		$pt = array_diff( $pt, $rempost );
		$post_types = Array();
		foreach ( $pt as $p ) {
			if ( !empty( $post_objs[$p]->label ) )
				$post_types[$p] = $post_objs[$p]->label;
			else
				$post_types[$p] = $p;
		}
		$this->default_options["posttypecolumns"]['initial_options'] = $post_types;
		$this->default_options["cpostactive"]['initial_options'] = $post_types;
		foreach ( $post_types as $p => $pt ) {
			$field = $p . "_title_format";
			$name = $post_objs[$p]->labels->singular_name;
			if ( !isset( $this->default_options[$field] ) ) {
				$this->default_options[$field] = Array (
						'name' => "$name " . __( 'Title Format:', 'all_in_one_seo_pack' ) . "<br />($p)",
						'help_text' => 
						__( 'The following macros are supported:', 'all_in_one_seo_pack' )
						. '<ul><li>' . __( '%blog_title% - Your blog title', 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( '%blog_description% - Your blog description', 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( '%post_title% - The original title of the post', 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( '%category_title% - The (main) category of the post', 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( '%category% - Alias for %category_title%', 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( "%post_author_login% - This post's author' login", 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( "%post_author_nicename% - This post's author' nicename", 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( "%post_author_firstname% - This post's author' first name (capitalized)", 'all_in_one_seo_pack' ) . '</li><li>' . 
						__( "%post_author_lastname% - This post's author' last name (capitalized)", 'all_in_one_seo_pack' ) . '</li>' . 
						'</ul>',
						'type' => 'text',
						'default' => '%post_title% | %blog_title%',
						'condshow' => Array( 'aiosp_enablecpost' => 'on', 'aiosp_cpostadvanced' => 'on', 'aiosp_cposttitles' => 'on' )
				);
				$this->layout['cpt']['options'][] = $field;
			}
		}
		
		$this->setting_options();
		add_filter( "{$this->prefix}display_options", Array( $this, 'filter_options' ), 10, 3 );
		parent::add_page_hooks();
	}
	
	function add_admin_pointers() {
		$this->pointers['aioseop_menu_202'] = Array( 'pointer_target' => '#toplevel_page_all-in-one-seo-pack-aioseop_class',
												 'pointer_text' => 	'<h3>' . sprintf( __( 'Welcome to Version %s!', 'all_in_one_seo_pack' ), AIOSEOP_VERSION )
													. '</h3><p>' . __( 'Thank you for running the latest and greatest All in One SEO Pack ever! Please review your settings, as we\'re always adding new features for you!', 'all_in_one_seo_pack' ) . '</p>',
												 'pointer_edge' => 'top',
												 'pointer_align' => 'left',
												 'pointer_scope' => 'global'
											);
		$this->pointers['aioseop_welcome_202'] = Array( 'pointer_target' => '#aioseop_top_button',
													'pointer_text' => '<h3>' . sprintf( __( 'Review Your Settings', 'all_in_one_seo_pack' ), AIOSEOP_VERSION )
													. '</h3><p>' . __( 'Thank you for running the latest and greatest All in One SEO Pack ever! New in 2.0: manage your performance with our Performance module; enable it from our new feature manager! And please review your settings, we have added some new ones!', 'all_in_one_seo_pack' ) . '</p>',
													 'pointer_edge' => 'bottom',
													 'pointer_align' => 'left',
													 'pointer_scope' => 'local'
											 );
		$this->filter_pointers();
	}
	
	function settings_page_init() {
		add_filter( "{$this->prefix}submit_options",	Array( $this, 'filter_submit'   ) );
	}
	
	function enqueue_scripts() {
		add_filter( "{$this->prefix}display_settings",	Array( $this, 'filter_settings' ), 10, 3 );
		parent::enqueue_scripts();
	}
	
	function filter_submit( $submit ) {
		$submit['Submit_Default']['value'] = __( 'Reset General Settings to Defaults', 'all_in_one_seo_pack' ) . ' &raquo;';
		$submit['Submit_All_Default'] = Array( 'type' => 'submit', 'class' => 'button-primary', 'value' => __( 'Reset ALL Settings to Defaults', 'all_in_one_seo_pack' ) . ' &raquo;' );
		return $submit;
	}
	
	/**
	 * Handle resetting options to defaults.
	 */
	function reset_options( $location = null, $delete = false ) {
		if ( $delete === true ) {
			$this->delete_class_option( $delete );
			$this->options = Array();
		}
		$default_options = $this->default_options( $location );
		foreach ( $default_options as $k => $v )
				$this->options[$k] = $v;
		$this->update_class_option( $this->options );
	}

	function get_current_options( $opts = Array(), $location = null, $defaults = null ) {
		if ( ( $location === 'aiosp' ) && ( $this->locations[$location]['type'] == 'metabox' ) ) {
			global $post;
			$post_id = $post;
			if ( is_object( $post_id ) )
				$post_id = $post_id->ID;
			$get_opts = $this->default_options( $location );
			foreach ( Array( 'keywords', 'description', 'title', 'disable', 'disable_analytics', 'noindex', 'nofollow', 'titleatr', 'menulabel' ) as $f ) {
				$field = "aiosp_$f";
				$get_opts[$field] = htmlspecialchars( stripcslashes( get_post_meta( $post_id, '_aioseop_' . $f, true ) ) );
			}
			$opts = wp_parse_args( $opts, $get_opts );
			return $opts;
		} else {
			$options = parent::get_current_options( $opts, $location, $defaults );
			return $options;
		}
	}
	
	function filter_settings( $settings, $location, $current ) {
		if ( $location == null ) {
			$prefix = $this->prefix;
			
			foreach ( Array( 'seopostcol', 'seocustptcol', 'debug_info', 'max_words_excerpt' ) as $opt )
				unset( $settings["{$prefix}$opt"] );
			
		} elseif ( $location == 'aiosp' ) {
			global $post;
			$prefix = $this->get_prefix( $location ) . $location . '_';
			if ( is_object( $post ) ) {
				if ( $post->post_type != 'page' ) {
					unset( $settings["{$prefix}titleatr"] );
					unset( $settings["{$prefix}menulabel"] );
				}
			}
			if ( !empty( $this->options[$this->prefix . 'togglekeywords'] ) ) {
				unset( $settings["{$prefix}keywords"] );
				unset( $settings["{$prefix}togglekeywords"] );
			} elseif ( !empty( $current["{$prefix}togglekeywords"] ) ) {
				unset( $settings["{$prefix}keywords"] );
			}
		}
		return $settings;
	}
	
	function filter_options( $options, $location ) {
		if ( $location == null ) {
			$prefix = $this->prefix;
			if ( isset( $options["{$prefix}rewrite_titles"] ) && ( !empty( $options["{$prefix}rewrite_titles"] ) ) )
				$options["{$prefix}rewrite_titles"] = 1;
			if ( ( isset( $options["{$prefix}enablecpost"] ) ) && ( $options["{$prefix}enablecpost"] === '' ) )
				$options["{$prefix}enablecpost"] = 0;
			if ( ( isset( $options["{$prefix}use_original_title"] ) ) && ( $options["{$prefix}use_original_title"] === '' ) )
				$options["{$prefix}use_original_title"] = 0;
		}
		return $options;
	}
	
	function display_extra_metaboxes( $add, $meta ) {
		echo "<div class='aioseop_metabox_wrapper' >";
		switch ( $meta['id'] ) {
			case "aioseop-about":
				?><div class="aioseop_metabox_text">
							<p><h2 style="display:inline;"><?php echo AIOSEOP_PLUGIN_NAME; ?></h2> by Michael Torbert of <a target="_blank" title="Semper Fi Web Design"
							href="http://semperfiwebdesign.com/">Semper Fi Web Design</a>.</p>
							<p>
							<a target="_blank" title="<?php _e('All in One SEO Plugin Support Forum', 'all_in_one_seo_pack' ); ?>"
							href="http://semperplugins.com/support/"><?php _e('Support Forum', 'all_in_one_seo_pack' ); ?></a>
							| <strong><a target="_blank" title="<?php _e('Pro Version', 'all_in_one_seo_pack' ); ?>"
							href="http://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/">
							<?php _e('UPGRADE TO PRO VERSION', 'all_in_one_seo_pack' ); ?></a></strong></p>
						</div>
				<?php
		    case "aioseop-donate":
		        ?>
				<div>
							<div class="aioseop_metabox_text">
								<p>If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the <a 				href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mrtorbert%40gmail%2ecom&item_name=All%20In%20One%20SEO%20Pack&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8" 
									target="_blank"><strong>donate</strong></a> button or send me a gift from my <a 
									href="https://www.amazon.com/wishlist/1NFQ133FNCOOA/ref=wl_web" target="_blank">
									<strong>Amazon wishlist</strong></a>.  Also, don't forget to follow me on <a 
									href="http://twitter.com/michaeltorbert/" target="_blank"><strong>Twitter</strong></a>.</p>
								</div>
								
							<div class="aioseop_metabox_feature">
								<a target="_blank" title="<?php _e( 'Donate', 'all_in_one_seo_pack' ); ?>"
	href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mrtorbert%40gmail%2ecom&item_name=All%20In%20One%20SEO%20Pack&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8">
					<img src="<?php echo AIOSEOP_PLUGIN_URL; ?>images/donate.jpg" alt="<?php _e('Donate with Paypal', 'all_in_one_seo_pack' ); ?>" />	</a>
					<a target="_blank" title="Amazon Wish List" href="https://www.amazon.com/wishlist/1NFQ133FNCOOA/ref=wl_web">
					<img src="<?php echo AIOSEOP_PLUGIN_URL; ?>images/amazon.jpg" alt="<?php _e('My Amazon Wish List', 'all_in_one_seo_pack' ); ?>" /> </a>
					<a target="_blank" title="<?php _e( 'Follow us on Facebook', 'all_in_one_seo_pack' ); ?>" href="http://www.facebook.com/pages/Semper-Fi-Web-Design/121878784498475"><span class="aioseop_follow_button aioseop_facebook_follow"></span></a>
					<a target="_blank" title="<?php _e( 'Follow us on Twitter', 'all_in_one_seo_pack' ); ?>" href="http://twitter.com/semperfidev/"><span class="aioseop_follow_button aioseop_twitter_follow"></span></a>
					</div>
				
				</div>
		        <?php
		        break;
			case "aioseop-list":
			?>
				<div class="aioseop_metabox_text">
						<form action="http://semperfiwebdesign.us1.list-manage.com/subscribe/post?u=794674d3d54fdd912f961ef14&amp;id=af0a96d3d9" 
						method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
						<h2><?php _e( 'Join our mailing list for tips, tricks, and WordPress secrets.', 'all_in_one_seo_pack' ); ?></h2>
						<p><i><?php _e( 'Sign up today and receive a free copy of the e-book 5 SEO Tips for WordPress ($39 value).', 'all_in_one_seo_pack' ); ?></i></p>
						<p><input type="text" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Email Address">
							<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn"></p>
						</form>
				</div>
			<?php
				break;
		    case "aioseop-support":
		        ?><div class="aioseop_metabox_text">
		        	<p><?php _e( 'For support please visit the Semper Plugins Support Forum at http://semperplugins.com/support/', 'all_in_one_seo_pack' ); ?></p>
				</div>
		        <?php
		        break;
		}
		echo "</div>";
	}
	
	function get_queried_object() {
		static $p = null;
		global $wp_query, $post;
		if ( $p !== null ) return $p;
		if ( is_object( $post ) )
			$p = $post;
		else {
			if ( !$wp_query ) return null;
			$p = $wp_query->get_queried_object();			
		}
		return $p;
	}
	
	function template_redirect() {
		global $aioseop_options;

		$post = $this->get_queried_object();

		if( aioseop_mrt_exclude_this_page() ) return;

		if ( is_feed() ) return;

		if ( is_single() || is_page() ) {
		    $aiosp_disable = htmlspecialchars(stripcslashes( get_post_meta( $post->ID, '_aioseop_disable', true ) ) );
		    if ( $aiosp_disable ) {
				$aiosp_disable_analytics = htmlspecialchars(stripcslashes( get_post_meta( $post->ID, '_aioseop_disable_analytics', true ) ) );
				if ( !$aiosp_disable_analytics ) {
					if ( aioseop_option_isset( 'aiosp_google_analytics_id' ) ) {
						remove_action( 'aioseop_modules_wp_head', array( $this, 'aiosp_google_analytics' ) );
						add_action( 'wp_head', array( $this, 'aiosp_google_analytics' ) );
					}
				}
				return;
			}
			
			if ( !empty( $aioseop_options['aiosp_cpostadvanced'] ) ) {
				$wp_post_types = $aioseop_options['aiosp_cpostactive'];
				if ( empty( $aioseop_options['aiosp_cpostactive'] ) ) return;
				if( !is_singular( $wp_post_types ) ) return;
			}
		}

		if ( !empty( $aioseop_options['aiosp_rewrite_titles'] ) ) {
			$force_rewrites = 1;
			if ( isset( $aioseop_options['aiosp_force_rewrites'] ) )
				$force_rewrites = $aioseop_options['aiosp_force_rewrites'];
			if ( $force_rewrites )
				ob_start( array( $this, 'output_callback_for_title' ) );
			else
				add_filter( 'wp_title', array( $this, 'wp_title' ) );
		}
	}
	
	function output_callback_for_title( $content ) {
		return $this->rewrite_title( $content );
	}

	function init() {
		if ( !defined( 'WP_PLUGIN_DIR' ) ) {
			load_plugin_textdomain( 'all_in_one_seo_pack', str_replace( ABSPATH, '', dirname( __FILE__ ) ) );
		} else {
			load_plugin_textdomain( 'all_in_one_seo_pack', false, AIOSEOP_PLUGIN_DIRNAME );
		}
	}
	
	function add_hooks() {
		global $aioseop_options;
		aioseop_update_settings_check();
		add_filter( 'user_contactmethods', 'aioseop_add_contactmethods' );
		if ( is_user_logged_in() && function_exists( 'is_admin_bar_showing' ) && is_admin_bar_showing() && current_user_can( 'manage_options' ) )
				add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 1000 );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_head', array( $this, 'add_page_icon' ) );
			add_action( 'admin_init', 'aioseop_addmycolumns', 1 );
		} else {
			if ( $aioseop_options['aiosp_can'] == '1' || $aioseop_options['aiosp_can'] == 'on' )
			        remove_action( 'wp_head', 'rel_canonical' );
			////analytics
			if ( aioseop_option_isset( 'aiosp_google_analytics_id' ) )
				add_action( 'aioseop_modules_wp_head', array( $this, 'aiosp_google_analytics' ) );
			add_filter( 'wp_list_pages', 'aioseop_list_pages' );
			add_action( 'wp_head', array( $this, 'wp_head') );
			add_action( 'template_redirect', array( $this, 'template_redirect' ) );
			add_filter( 'wp_list_pages_excludes', 'aioseop_get_pages_start' );
			add_filter( 'get_pages', 'aioseop_get_pages' );	
		}
		
	}

	function is_static_front_page() {
		static $is_front_page = null;
		if ( $is_front_page !== null ) return $is_front_page;
		$post = $this->get_queried_object();
		$is_front_page = ( get_option( 'show_on_front' ) == 'page' && is_page() && $post->ID == get_option( 'page_on_front' ) );
		return $is_front_page;
	}
	
	function is_static_posts_page() {
		static $is_posts_page = null;
		if ( $is_posts_page !== null ) return $is_posts_page;
		$post = $this->get_queried_object();
		$is_posts_page = ( get_option( 'show_on_front' ) == 'page' && is_home() && $post->ID == get_option( 'page_for_posts' ) );
		return $is_posts_page;
	}

	function wp_head() {
			if ( is_feed() ) return;

			if( aioseop_mrt_exclude_this_page() ) return;

			global $wp_query;
			global $aioseop_options;
			$post = $this->get_queried_object();
			$meta_string = null;

			if ( is_single() || is_page() ) {
			    $aiosp_disable = htmlspecialchars( stripcslashes( get_post_meta( $post->ID, '_aioseop_disable', true ) ) );
			    if ( $aiosp_disable ) return;
				if( empty( $aioseop_options['aiosp_enablecpost'] ) ) {
					$wp_post_types = get_post_types( Array( '_builtin' => true ) ); // don't display meta if SEO isn't enabled on custom post types -- pdb
					if( !is_singular( $wp_post_types ) ) return;
				} else {
					if ( !empty( $aioseop_options['aiosp_cpostadvanced'] ) ) {
						$wp_post_types = $aioseop_options['aiosp_cpostactive'];
						if ( empty( $aioseop_options['aiosp_cpostactive'] ) ) return;
						if( !is_singular( $wp_post_types ) ) return;
					}
				}
			}

			$force_rewrites = 1;
			if ( isset( $aioseop_options['aiosp_force_rewrites'] ) )
				$force_rewrites = $aioseop_options['aiosp_force_rewrites'];

			if ( !empty( $aioseop_options['aiosp_rewrite_titles'] ) && $force_rewrites ) {
				// make the title rewrite as short as possible
				if (function_exists( 'ob_list_handlers' ) ) {
					$active_handlers = ob_list_handlers();
				} else {
					$active_handlers = array();
				}
				if (sizeof($active_handlers) > 0 &&
					strtolower( $active_handlers[sizeof( $active_handlers ) - 1] ) ==
					strtolower( 'All_in_One_SEO_Pack::output_callback_for_title' ) ) {
					ob_end_flush();
				} else {
					$this->log( "another plugin interfering?" );
					// if we get here there *could* be trouble with another plugin :(
					$this->ob_start_detected = true;
					if ( function_exists( 'ob_list_handlers' ) ) {
						foreach ( ob_list_handlers() as $handler ) {
							$this->log( "detected output handler $handler" );
						}
					}
				}
			}

			echo "\n<!-- All in One SEO Pack $this->version by Michael Torbert of Semper Fi Web Design";
			if ( $this->ob_start_detected )
				echo "ob_start_detected ";
			echo "[$this->title_start,$this->title_end] ";
			echo "-->\n";
			$is_front_page = is_front_page();
			
			$is_front_page_keywords = ( ( $is_front_page && $aioseop_options['aiosp_home_keywords'] && !$this->is_static_posts_page() ) || $this->is_static_front_page() );

			$page_for_posts = get_option( 'page_for_posts' );
			$blog_page = '';
			if ( $page_for_posts && ( !is_object( $post ) || ( $page_for_posts != $post->ID ) ) && is_home() )
				$blog_page = get_post( $page_for_posts );
			
			if ( $is_front_page_keywords )
				$keywords = trim( $this->internationalize( $aioseop_options['aiosp_home_keywords'] ) );
			elseif ( $this->is_static_posts_page() && !$aioseop_options['aiosp_dynamic_postspage_keywords'] )  // and if option = use page set keywords instead of keywords from recent posts
					$keywords = stripcslashes( $this->internationalize( get_post_meta( $post->ID, "_aioseop_keywords", true ) ) );
			elseif ( !empty( $blog_page )  && !$aioseop_options['aiosp_dynamic_postspage_keywords'] )
					$keywords = stripcslashes( $this->internationalize( get_post_meta( $blog_page->ID, "_aioseop_keywords", true ) ) );
			else	$keywords = $this->get_all_keywords();

			if ( is_category() && $this->show_page_description() )
				$description = $this->internationalize( category_description() );
			elseif ( is_tag()  && $this->show_page_description() )
				$description = $this->internationalize( tag_description() );
			elseif ( is_tax()  && $this->show_page_description() )
				$description = $this->internationalize( term_description() );
			elseif ( is_author()  && $this->show_page_description() )
				$description = $this->internationalize( get_the_author_meta( 'description' ) );
			else if ( $is_front_page )
				$description = trim( stripcslashes( $this->internationalize( $aioseop_options['aiosp_home_description'] ) ) );
			else if ( is_single() || is_page() || is_home() || $this->is_static_posts_page() )
				if ( $this->is_static_front_page() )
					$description = trim( stripcslashes( $this->internationalize( $aioseop_options['aiosp_home_description'] ) ) );
				elseif ( !empty( $blog_page ) )
					$description = $this->get_post_description( $blog_page );
			if ( empty( $description ) && is_object( $post ) && !is_archive() && empty( $blog_page ) )
				$description = $this->get_post_description( $post );
			
			$description = apply_filters( 'aioseop_description', $description );				
			
			if ( isset($description) && (strlen($description) > $this->minimum_description_length ) && !( $is_front_page && is_paged() ) ) {
				$description = trim( strip_tags( $description ) );
				$description = str_replace( '"', '', $description );

				// replace newlines on mac / windows?
				$description = str_replace( "\r\n", ' ', $description );

				// maybe linux uses this alone
				$description = str_replace( "\n", ' ', $description );

				if ( !isset( $meta_string) ) {
					$meta_string = '';
				}

				// description format
				$description_format = $aioseop_options['aiosp_description_format'];
				if ( !isset( $description_format ) || empty( $description_format ) ) {
					$description_format = "%description%";
				}
				$description = str_replace( '%description%', apply_filters( 'aioseop_description_override', $description ), $description_format );
				$description = str_replace( '%blog_title%', get_bloginfo( 'name' ), $description );
				$description = str_replace( '%blog_description%', get_bloginfo( 'description' ), $description );
				$description = str_replace( '%wp_title%', $this->get_original_title(), $description );
				if( $aioseop_options['aiosp_can'] && is_attachment() ) {
					$url = $this->aiosp_mrt_get_url( $wp_query );
					if ( $url ) {
						$matches = Array();
						preg_match_all( '/(\d+)/', $url, $matches );
						if ( is_array( $matches ) ){
							$uniqueDesc = join( '', $matches[0] );
						}
					}
					$description .= ' ' . $uniqueDesc;
				}
				$meta_string .= sprintf( "<meta name=\"description\" content=\"%s\" />\n", $description );
			}

			$keywords = apply_filters( 'aioseop_keywords', $keywords );

			if ( isset( $aioseop_options['aiosp_togglekeywords'] ) )
				$togglekeywords = $aioseop_options['aiosp_togglekeywords'];
			else
				$togglekeywords = 0;

			if ( isset( $keywords ) && $togglekeywords == 0 && !empty( $keywords ) && !( is_home() && is_paged() ) ) {
				if ( isset( $meta_string ) ) $meta_string .= "\n";
				$keywords = str_replace( '"', '', $keywords );
				$meta_string .= sprintf( "<meta name=\"keywords\" content=\"%s\" />\n", $keywords );
			}

			$is_tag = is_tag();

			if ( ( is_category() && $aioseop_options['aiosp_category_noindex'] ) || ( !is_category() && is_archive() &&!$is_tag && $aioseop_options['aiosp_archive_noindex'] ) || ( $aioseop_options['aiosp_tags_noindex'] && $is_tag ) ) {
				$meta_string .= '<meta name="robots" content="noindex,follow" />' . "\n";
			} else {
				if ( is_single() || is_page() ) {
				    $aiosp_noindex = htmlspecialchars( stripcslashes( get_post_meta( $post->ID, '_aioseop_noindex', true ) ) );
				    $aiosp_nofollow = htmlspecialchars( stripcslashes( get_post_meta( $post->ID, '_aioseop_nofollow', true ) ) );
					if ( $aiosp_noindex || $aiosp_nofollow ) {
						$noindex = "index";
						$nofollow = "follow";
						if ( $aiosp_noindex ) $noindex = "no" . $noindex;
						if ( $aiosp_nofollow ) $nofollow = "no" . $nofollow;
						$meta_string .= '<meta name="robots" content="' . $noindex . ',' . $nofollow . '" />' . "\n";
					}
				}
			}
			
			if ( $is_front_page )
				foreach( Array( 'google' => 'google-site-verification', 'bing' => 'msvalidate.01', 'pinterest' => 'p:domain_verify' ) as $k => $v )
					if ( !empty( $aioseop_options["aiosp_{$k}_verify"] ) )
						$meta_string .= '<meta name="' . $v . '" content="' . trim( strip_tags( $aioseop_options["aiosp_{$k}_verify"] ) ) . '" />' . "\n";
			
			foreach( Array( 'page_meta', 'post_meta', 'home_meta', 'front_meta' ) as $meta ) {
				if ( !empty( $aioseop_options["aiosp_{$meta}_tags" ] ) )
					$$meta = html_entity_decode( stripcslashes( $aioseop_options["aiosp_{$meta}_tags" ] ) );
				else
					$$meta = '';
			}

			if ( is_page() && isset( $page_meta ) && !empty( $page_meta ) && ( !$is_front_page || empty( $front_meta ) ) ) {
				if ( isset( $meta_string ) ) $meta_string .= "\n";
				$meta_string .= $page_meta;
			}

			if ( is_single() && isset( $post_meta ) && !empty( $post_meta ) ) {
				if ( isset( $meta_string ) ) $meta_string .= "\n";
				$meta_string .= $post_meta;
			}

			if ( !empty( $post ) && isset( $post->post_author ) && empty( $aioseop_options['aiosp_google_disable_profile'] ) )
				$googleplus = get_the_author_meta( 'googleplus', $post->post_author );

			if ( empty( $googleplus ) && !empty( $aioseop_options['aiosp_google_publisher'] ) )
				$googleplus = $aioseop_options['aiosp_google_publisher'];

			if ( $is_front_page && !empty( $aioseop_options['aiosp_google_publisher'] ) )
				$meta_string = '<link rel="publisher" href="' . $aioseop_options['aiosp_google_publisher'] . '" />' . "\n" . $meta_string;				
			if ( is_singular() && ( $googleplus ) ) {
				$meta_string = '<link rel="author" href="' . $googleplus . '" />' . "\n" . $meta_string;
			} else if ( !empty( $aioseop_options['aiosp_google_publisher'] ) ) {
				$meta_string = '<link rel="author" href="' . $aioseop_options['aiosp_google_publisher'] . '" />' . "\n" . $meta_string;
			}
			
			if ( $is_front_page && !empty( $front_meta ) ) {
				if ( isset( $meta_string ) ) $meta_string .= "\n";
				$meta_string .= $front_meta;
			} else {
				if ( is_home() && !empty( $home_meta ) ) {
					if ( isset( $meta_string ) ) $meta_string .= "\n";
					$meta_string .= $home_meta;
				}
			}
			
			if ( $meta_string != null ) echo "$meta_string\n";

			if ( ( $aioseop_options['aiosp_can'] ) && ( $url = $this->aiosp_mrt_get_url( $wp_query ) ) ) {
				$url = apply_filters( 'aioseop_canonical_url',$url );
				echo "".'<link rel="canonical" href="'.$url.'" />'."\n";
			}
			do_action( 'aioseop_modules_wp_head' );
			echo "<!-- /all in one seo pack -->\n";
	}

function aiosp_google_analytics() {
	global $aioseop_options;
	?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo $aioseop_options['aiosp_google_analytics_id'];  ?>']);
<?php if ( !empty( $aioseop_options['aiosp_ga_multi_domain'] ) ) {
?>		  _gaq.push(['_setAllowLinker', true]);
<?php
}
?>
<?php if ( !empty( $aioseop_options['aiosp_ga_domain'] ) ) {
?>		  _gaq.push(['_setDomainName', '<?php echo $aioseop_options['aiosp_ga_domain']; ?>']);
<?php
}
?>		  _gaq.push(['_trackPageview']);
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
<?php
	if ( $aioseop_options['aiosp_ga_track_outbound_links'] ) {
?>
		<script type="text/javascript">
			function recordOutboundLink(link, category, action) {
				_gat._getTrackerByName()._trackEvent(category, action);
				if ( link.target == '_blank' ) return true;
				setTimeout('document.location = "' + link.href + '"', 100);
				return false;
			}
			/* use regular Javascript for this */
			function getAttr(ele, attr) {
				var result = (ele.getAttribute && ele.getAttribute(attr)) || null;
				if( !result ) {
					var attrs = ele.attributes;
					var length = attrs.length;
					for(var i = 0; i < length; i++)
					if(attr[i].nodeName === attr) result = attr[i].nodeValue;
				}
				return result;
			}

			window.onload = function () {
				var links = document.getElementsByTagName('a');
				for (var x=0; x < links.length; x++) {
					if (typeof links[x] == 'undefined') continue;
					if (typeof links[x].onclick != 'undefined') continue;
					links[x].onclick = function () {
						var mydomain = new RegExp(document.domain, 'i');
						href = getAttr(this, 'href');
						if(href && href.toLowerCase().indexOf('http') === 0 && !mydomain.test(href)) {
							recordOutboundLink(this, 'Outbound Links', href);
						}
					}
				}
			};
		</script>
<?php
	}
}
	
// Thank you, Yoast de Valk, for much of this code.	

	function aiosp_mrt_get_url( $query ) {
		if ( $query->is_404 || $query->is_search )
			return false;
		
		$haspost = count( $query->posts ) > 0;

		if ( get_query_var( 'm' ) ) {
			$m = preg_replace( '/[^0-9]/', '', get_query_var( 'm' ) );
			switch ( strlen( $m ) ) {
				case 4: $link = get_year_link( $m ); break;
        		case 6: $link = get_month_link( substr( $m, 0, 4), substr($m, 4, 2 ) ); break;
        		case 8: $link = get_day_link( substr( $m, 0, 4 ), substr( $m, 4, 2 ), substr( $m, 6, 2 ) ); break;
       			default:
       			return false;
			}
		} elseif ( ( $query->is_single || $query->is_page ) && $haspost ) {
			$post = $query->posts[0];
			$link = get_permalink( $post->ID );
		} elseif ( $query->is_author && $haspost ) {
			$author = get_userdata( get_query_var( 'author' ) );
     		if ($author === false) return false;
			$link = get_author_posts_url( $author->ID, $author->user_nicename );
  		} elseif ( $query->is_category && $haspost ) {
    		$link = get_category_link( get_query_var( 'cat' ) );
		} elseif ( $query->is_tag && $haspost ) {
			$tag = get_term_by( 'slug', get_query_var( 'tag' ), 'post_tag' );
       		if ( !empty( $tag->term_id ) )
				$link = get_tag_link( $tag->term_id );
  		} elseif ( $query->is_day && $haspost ) {
  			$link = get_day_link( get_query_var( 'year' ),
	                              get_query_var( 'monthnum' ),
	                              get_query_var( 'day' ) );
	    } elseif ( $query->is_month && $haspost ) {
	        $link = get_month_link( get_query_var( 'year' ),
	                               get_query_var( 'monthnum' ) );
	    } elseif ( $query->is_year && $haspost ) {
	        $link = get_year_link( get_query_var( 'year' ) );
	    } elseif ( $query->is_home ) {
	        if ( (get_option( 'show_on_front' ) == 'page' ) &&
	            ( $pageid = get_option( 'page_for_posts' ) ) ) {
	            $link = get_permalink( $pageid );
	        } else {
				if ( function_exists( 'icl_get_home_url' ) ) {
					$link = icl_get_home_url();
				} else {
					$link = get_option( 'home' );
				}
			}
		} elseif ( $query->is_tax && $haspost ) {
			$taxonomy = get_query_var( 'taxonomy' );
			$term = get_query_var( 'term' );
			$link = get_term_link( $term, $taxonomy );
        } elseif ( $query->is_archive && function_exists( 'get_post_type_archive_link' ) && ( $post_type = get_query_var( 'post_type' ) ) ) {
            $link = get_post_type_archive_link( $post_type );
	    } else {
	        return false;
	    }
		return $this->yoast_get_paged( $link );
	}
	
	function get_page_number() {
		$page = get_query_var( 'page' );
		if ( empty( $page ) )
			$page = get_query_var( 'paged' );
		return $page;
	}

	function yoast_get_paged( $link ) {
		$page = $this->get_page_number();
        if ( !empty( $page ) && $page > 1 ) {
            $link = trailingslashit( $link ) ."page/". "$page";
			$link = user_trailingslashit( $link, 'paged' );
		}
		return $link;
	}

	function show_page_description() {
		global $aioseop_options;
		if ( !empty( $aioseop_options['aiosp_hide_paginated_descriptions'] ) ) {
			$page = $this->get_page_number();
			if ( !empty( $page ) && ( $page > 1 ) )
				return false;			
		}
		return true;
	}

	function get_post_description( $post ) {
		global $aioseop_options;

		if ( !$this->show_page_description() )
			return '';
		
	    $description = trim( stripcslashes( $this->internationalize( get_post_meta( $post->ID, "_aioseop_description", true ) ) ) );
		if ( !$description ) {
			$description = $this->trim_excerpt_without_filters_full_length( $this->internationalize( $post->post_excerpt ) );
			if ( !$description && $aioseop_options["aiosp_generate_descriptions"] ) {
				$description = $this->trim_excerpt_without_filters( $this->internationalize( $post->post_content ) );				
			}
		}
		
		// "internal whitespace trim"
		$description = preg_replace( "/\s\s+/u", " ", $description );
		return $description;
	}


	function replace_title( $content, $title ) {
		$title = trim( strip_tags( $title ) );
		$title_tag_start = "<title>";
		$title_tag_end = "</title>";
		$len_start = strlen( $title_tag_start );
		$title = stripcslashes( trim( $title ) );
		$start = strpos( $content, $title_tag_start );
		$end = strpos( $content, $title_tag_end );

		$this->title_start = $start;
		$this->title_end = $end;
		$this->orig_title = $title;

		if ( $start && $end )
			$header = substr( $content, 0, $start + $len_start ) . $title .  substr( $content, $end );
		else
			$header = $content;
		
		return $header;
	}
	
	function internationalize( $in ) {
		if ( function_exists( 'langswitch_filter_langs_with_message' ) )
			$in = langswitch_filter_langs_with_message( $in );

		if ( function_exists( 'polyglot_filter' ) )
			$in = polyglot_filter( $in );

		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) )
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $in );

		return apply_filters( 'localization', $in );
	}

	/** @return The original title as delivered by WP (well, in most cases) */
	function get_original_title( $sep = '', $echo = false, $seplocation = '' ) {
		global $aioseop_options;
		if ( !empty( $aioseop_options['aiosp_use_original_title'] ) ) {
			$has_filter = has_filter( 'wp_title', Array( $this, 'wp_title' ) );
			if ( $has_filter !== false )
				remove_filter( 'wp_title', Array( $this, 'wp_title' ), $has_filter );
			$title = wp_title( $sep, $echo, $seplocation );
			if ( $has_filter !== false )
				add_filter( 'wp_title', Array( $this, 'wp_title' ), $has_filter );
			if ( $title && ( $title = trim( $title ) ) )
				return trim( $title );
		}
		
		// the_search_query() is not suitable, it cannot just return
		global $s;
		
		$title = null;
		
		if ( is_home() ) {
			$title = get_option( 'blogname' );
		} else if ( is_single() ) {
			$title = $this->internationalize( single_post_title( '', false ) );
		} else if ( is_search() && isset($s) && !empty($s) ) {
			$search = esc_attr( stripcslashes($s) );
			if ( !empty( $aioseop_options['aiosp_cap_titles'] ) )
				$search = $this->capitalize( $search );
			$title = $search;
		} else if ( ( is_tax() || is_category() ) && !is_feed() ) {
			$category_name = ucwords($this->internationalize( single_cat_title( '', false ) ) );
			$title = $category_name;
		} else if ( is_page() ) {
			$title = $this->internationalize( single_post_title( '', false ) );
		} else if ( is_tag() ) {
			global $utw;
			if ( $utw ) {
				$tags = $utw->GetCurrentTagSet();
				$tag = $tags[0]->tag;
		        $tag = str_replace('-', ' ', $tag);
			} else {
				// wordpress > 2.3
				$tag = $this->internationalize( single_term_title( '', false ) );
			}
			if ( $tag ) $title = $tag;
		} else if ( is_author() ) {
			$author = get_userdata( get_query_var( 'author' ) );
			if ( $author === false ) {
				global $wp_query;
				$author = $wp_query->get_queried_object();
			}
			if ($author !== false)
				$title = $author->display_name;
		} else if ( is_day() ) {
			$title = get_the_date();
		} else if ( is_month() ) {
			$title = get_the_date( 'F, Y' );
		} else if ( is_year() ) {
			$title = get_the_date( 'Y' );
		} else if ( is_archive() ) {
			$title = $this->internationalize( post_type_archive_title( '', false) );
		} else if ( is_404() ) {
		    $title_format = $aioseop_options['aiosp_404_title_format'];
		    $new_title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name' ) ), $title_format );
		    $new_title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description' ) ), $new_title );
		    $new_title = str_replace( '%request_url%', $_SERVER['REQUEST_URI'], $new_title);
		    $new_title = str_replace( '%request_words%', $this->request_as_words( $_SERVER['REQUEST_URI'] ), $new_title );
			$title = $new_title;
		}
		return trim( $title );
	}
	
	function paged_title( $title ) {
		// the page number if paged
		global $paged;
		global $aioseop_options;
		// simple tagging support
		global $STagging;
		$page = get_query_var( 'page' );
		if ( $paged > $page ) $page = $paged;
		if ( is_paged() || ( isset($STagging) && $STagging->is_tag_view() && $paged ) || $page ) {
			$part = $this->internationalize( $aioseop_options['aiosp_paged_format'] );
			if ( isset( $part ) || !empty( $part ) ) {
				$part = " " . trim( $part );
				$part = str_replace( '%page%', $page, $part );
				$this->log( "paged_title() [$title] [$part]" );
				$title .= $part;
			}
		}
		return $title;
	}

	function get_post_title_format() {
		global $aioseop_options;
		$title_format = '%post_title% | %blog_title%';
		if ( isset( $aioseop_options['aiosp_post_title_format'] ) )
			$title_format = $aioseop_options['aiosp_post_title_format'];
		if( !empty( $aioseop_options['aiosp_enablecpost'] ) && !empty( $aioseop_options['aiosp_cpostadvanced'] ) && !empty( $aioseop_options['aiosp_cpostactive'] ) ) {
			$wp_post_types = $aioseop_options['aiosp_cpostactive'];
			if ( is_singular( $wp_post_types ) && !empty( $aioseop_options["aiosp_cposttitles"] ) ) {
				$post_type = get_post_type();
				if ( !empty( $aioseop_options["aiosp_{$post_type}_title_format"] ) )
					$title_format = $aioseop_options["aiosp_{$post_type}_title_format"];
			}
		}
		return $title_format;
	}
	
	function apply_post_title_format( $title, $category = '' ) {
		global $post;
		$title_format = $this->get_post_title_format();
		$authordata = get_userdata( $post->post_author );
		$r_title = array( '%blog_title%', '%blog_description%', '%post_title%', '%category%', '%category_title%', '%post_author_login%', '%post_author_nicename%', '%post_author_firstname%', '%post_author_lastname%' );
		$d_title = array( $this->internationalize( get_bloginfo('name') ), $this->internationalize( get_bloginfo( 'description' ) ), $title, $category, $category, $authordata->user_login, $authordata->user_nicename, ucwords( $authordata->first_name ), ucwords( $authordata->last_name ) );
		$title = trim( str_replace( $r_title, $d_title, $title_format ) );
		return $title;
	}
	
	function apply_page_title_format( $title ) {
		global $aioseop_options;
		$title_format = $aioseop_options['aiosp_page_title_format'];
        $new_title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name' ) ), $title_format );
        $new_title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description' ) ), $new_title );
        $new_title = str_replace( '%page_title%', $title, $new_title );
        $new_title = str_replace( '%page_author_login%', $authordata->user_login, $new_title );
        $new_title = str_replace( '%page_author_nicename%', $authordata->user_nicename, $new_title );
        $new_title = str_replace( '%page_author_firstname%', ucwords($authordata->first_name ), $new_title );
        $new_title = str_replace( '%page_author_lastname%', ucwords($authordata->last_name ), $new_title );
		$title = trim( $new_title );
		return $title;
	}

	/*** Gets the title that will be used by AIOSEOP for title rewrites or returns false. ***/
	function get_aioseop_title( $post ) {
		global $aioseop_options;
		// the_search_query() is not suitable, it cannot just return
		global $s, $STagging;
		if ( is_front_page() ) {
			$title = $this->internationalize( $aioseop_options['aiosp_home_title'] );
			if ( empty( $title ) && !empty( $post ) && $this->is_static_front_page() ) {
				$title = $this->internationalize( get_post_meta( $post->ID, "_aioseop_title", true ) );
				if ( empty( $title ) )
					$title = $this->internationalize( $post->post_title );
				if ( empty( $title ) )
					$title = $this->internationalize( $this->get_original_title( '', false ) );
				if ( !empty( $title ) )
					$title = $this->apply_page_title_format( $title );
			}
			if (empty( $title ) )
				$title = $this->internationalize( get_option( 'blogname' ) );
			return $this->paged_title( $title );
		} else if ( is_attachment() ) {
			if ( $post === null ) return false;
			$title = $this->internationalize( get_post_meta( $post->ID, "_aioseop_title", true ) );
			if ( !empty( $title ) )
				return apply_filters( 'aioseop_attachment_title', $this->apply_post_title_format( $title ) );
			$title = $this->internationalize( $post->post_title );
			if ( !$title )
				$title = $this->internationalize( $this->get_original_title( '', false ) );
			$title = get_the_title( $post->post_parent ) . ' ' . $title . ' – ' . get_option( 'blogname' );
			apply_filters( 'aioseop_attachment_title', $title );
		} else if ( is_page() || $this->is_static_posts_page() || ( is_home() && !$this->is_static_posts_page() ) ) {
			if ( $post === null ) return false;
			// we're not in the loop :(
			$authordata = get_userdata( $post->post_author );
			if ( ( $this->is_static_front_page() ) && ( $home_title = $this->internationalize( $aioseop_options['aiosp_home_title'] ) ) ) {
				//home title filter
				return apply_filters( 'aioseop_home_page_title', $home_title );
			} else {
				$page_for_posts = '';
				if ( is_home() )
					$page_for_posts = get_option( 'page_for_posts' );
				if ( $page_for_posts ) {
					$title = $this->internationalize( get_post_meta( $page_for_posts, "_aioseop_title", true ) );
					if ( !$title ) {
						$post_page = get_post( $page_for_posts );
						$title = $this->internationalize( $post_page->post_title );						
					}
				} else {
					$title = $this->internationalize( get_post_meta( $post->ID, "_aioseop_title", true ) );
					if ( !$title )
						$title = $this->internationalize( $post->post_title );
				}
				if ( !$title )
					$title = $this->internationalize( $this->get_original_title( '', false ) );

				$title = $this->apply_page_title_format( $title );
                $title = $this->paged_title( $title );
				$title = apply_filters( 'aioseop_title_page', $title );
				if ( $this->is_static_posts_page() )
					$title = apply_filters( 'single_post_title', $title );
				return $title;
			}
		} else if ( is_single() ) {
			// we're not in the loop :(
			if ( $post === null ) return false;
			$authordata = get_userdata( $post->post_author );
			$categories = get_the_category();
			$category = '';
			if ( count( $categories ) > 0 ) {
				$category = $categories[0]->cat_name;
			}
			$title = $this->internationalize( get_post_meta( $post->ID, "_aioseop_title", true ) );
			if ( !$title ) {
				$title = $this->internationalize( get_post_meta( $post->ID, "title_tag", true ) );
				if ( !$title ) $title = $this->internationalize($this->get_original_title( '', false ) );
			}
			if ( empty( $title ) ) $title = $post->post_title;
			if ( !empty( $title ) )
				$title = $this->apply_post_title_format( $title, $category );
			$title = $this->paged_title( $title );
			return apply_filters( 'aioseop_title_single', $title );
		} else if ( is_search() && isset( $s ) && !empty( $s ) ) {
			$search = esc_attr( stripcslashes( $s ) );
			if ( !empty( $aioseop_options['aiosp_cap_titles'] ) )
				$search = $this->capitalize( $search );
            $title_format = $aioseop_options['aiosp_search_title_format'];
            $title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name' ) ), $title_format );
            $title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description' ) ), $title );
            $title = str_replace( '%search%', $search, $title);
			return $title;
		} else if ( is_tag() ) {
			global $utw;
			if ( $utw ) {
				$tags = $utw->GetCurrentTagSet();
				$tag = $tags[0]->tag;
	            $tag = str_replace('-', ' ', $tag);
			} else {
				// wordpress > 2.3
				$tag = $this->internationalize( $this->get_original_title( '', false ) );
			}
			if ($tag) {
				if ( !empty( $aioseop_options['aiosp_cap_titles'] ) )
					$tag = $this->capitalize( $tag );
	            $title_format = $aioseop_options['aiosp_tag_title_format'];
	            $title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo('name') ), $title_format );
	            $title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description') ), $title );
	            $title = str_replace( '%tag%', $tag, $title );
	            $title = $this->paged_title( $title );
				return $title;
			}
		} else if ( ( is_tax() || is_category() ) && !is_feed() ) {
			$category_description = $this->internationalize( category_description() );
				if( !empty( $aioseop_options['aiosp_cap_cats'] ) ) {
					$category_name = ucwords( $this->internationalize( single_cat_title( '', false ) ) );
				} else {
						$category_name = $this->internationalize( single_cat_title( '', false ) );
				}
            $title_format = $aioseop_options['aiosp_category_title_format'];
            $title = str_replace( '%category_title%', $category_name, $title_format );
            $title = str_replace( '%category_description%', $category_description, $title );
            $title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name' ) ), $title );
            $title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description' ) ), $title );
            $title = $this->paged_title( $title );
			return $title;
		} else if ( isset( $STagging ) && $STagging->is_tag_view() ) { // simple tagging support
			$tag = $STagging->search_tag;
			if ( $tag ) {
				if ( !empty( $aioseop_options['aiosp_cap_titles'] ) )
					$tag = $this->capitalize($tag);
	            $title_format = $aioseop_options['aiosp_tag_title_format'];
	            $title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name') ), $title_format);
	            $title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description') ), $title);
	            $title = str_replace( '%tag%', $tag, $title );
	            $title = $this->paged_title( $title );
				return $title;
			}
		} else if ( is_archive() ) {
			$date = $this->internationalize( $this->get_original_title( '', false ) );
            $title_format = $aioseop_options['aiosp_archive_title_format'];
            $new_title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name' ) ), $title_format );
            $new_title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description' ) ), $new_title );
            $new_title = str_replace( '%date%', $date, $new_title );
			$title = trim( $new_title );
            $title = $this->paged_title( $title );
			return $title;
		} else if ( is_404() ) {
            $title_format = $aioseop_options['aiosp_404_title_format'];
            $new_title = str_replace( '%blog_title%', $this->internationalize( get_bloginfo( 'name') ), $title_format );
            $new_title = str_replace( '%blog_description%', $this->internationalize( get_bloginfo( 'description' ) ), $new_title );
            $new_title = str_replace( '%request_url%', $_SERVER['REQUEST_URI'], $new_title );
            $new_title = str_replace( '%request_words%', $this->request_as_words( $_SERVER['REQUEST_URI'] ), $new_title );
			$new_title = str_replace( '%404_title%', $this->internationalize( $this->get_original_title( '', false ) ), $new_title );
			return $new_title;
		}
		return false;
	}
	
	/*** Used to filter wp_title(), get our title. ***/
	function wp_title() {
		global $aioseop_options;
		$title = false;
		$post = $this->get_queried_object();
		if ( !empty( $aioseop_options['aiosp_rewrite_titles'] ) )
			$title = $this->get_aioseop_title( $post );
		if ( $title === false )
			$title = $this->get_original_title();
		return apply_filters( 'aioseop_title', $title );
	}

	/*** Used for forcing title rewrites. ***/
	function rewrite_title($header) {
		global $wp_query;
		if (!$wp_query) {
			$header .= "<!-- no wp_query found! -->\n";
			return $header;	
		}
		$title = $this->wp_title();
		if ( !empty( $title ) )
			$header = $this->replace_title( $header, $title );
		return $header;
	}
	
	/**
	 * @return User-readable nice words for a given request.
	 */
	function request_as_words( $request ) {
		$request = htmlspecialchars( $request );
		$request = str_replace( '.html', ' ', $request );
		$request = str_replace( '.htm', ' ', $request );
		$request = str_replace( '.', ' ', $request );
		$request = str_replace( '/', ' ', $request );
		$request_a = explode( ' ', $request );
		$request_new = array();
		foreach ( $request_a as $token ) {
			$request_new[] = ucwords( trim( $token ) );
		}
		$request = implode( ' ', $request_new );
		return $request;
	}
	
	function capitalize( $s ) {
		$s = trim( $s );
		$tokens = explode( ' ', $s );
		while ( list( $key, $val ) = each( $tokens ) ) {
			$tokens[ $key ] = trim( $tokens[ $key ] );
			$tokens[ $key ] = $this->strtoupper( substr( $tokens[$key], 0, 1 ) ) . substr( $tokens[$key], 1 );
		}
		$s = implode( ' ', $tokens );
		return $s;
	}
	
	function trim_excerpt_without_filters( $text, $max = 0 ) {
		$text = str_replace( ']]>', ']]&gt;', $text );
                $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
		$text = strip_tags( $text );
		if ( !$max ) $max = $this->maximum_description_length;
		
		if ( $max < strlen( $text ) ) {
			while( $text[$max] != ' ' && $max > $this->minimum_description_length ) {
				$max--;
			}
		}
		$text = substr( $text, 0, $max );
		return trim( stripcslashes( $text ) );
	}
	
	function trim_excerpt_without_filters_full_length( $text ) {
		$text = str_replace( ']]>', ']]&gt;', $text );
                $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
		$text = strip_tags( $text );
		return trim( stripcslashes( $text ) );
	}
	
	/**
	 * @return comma-separated list of unique keywords
	 */
	function get_all_keywords() {
		global $posts;
		global $aioseop_options;

		if ( is_404() ) return null;
		
		// if we are on synthetic pages
		if ( !is_home() && !is_page() && !is_single() && !$this->is_static_front_page() && !$this->is_static_posts_page() )
			return null;

	    $keywords = array();
	    if ( is_array( $posts ) ) {
	        foreach ( $posts as $post ) {
	            if ( $post ) {
	                // custom field keywords
	                $keywords_i = null;
           
					$id = $post->ID;
	                $keywords_i = stripcslashes( $this->internationalize( get_post_meta( $id, "_aioseop_keywords", true ) ) );
					if ( is_attachment() ) {
						$id = $post->post_parent;
						if ( empty( $keywords_i ) )
							$keywords_i = stripcslashes( $this->internationalize( get_post_meta( $id, "_aioseop_keywords", true ) ) );
					}
	                $keywords_i = str_replace( '"', '', $keywords_i );
	                if (isset( $keywords_i ) && !empty( $keywords_i ) ) {
	                	$traverse = explode( ',', $keywords_i );
	                	foreach ( $traverse as $keyword ) $keywords[] = $keyword;
	                }
	                
	                // WP 2.3 tags
				if ( !empty( $aioseop_options['aiosp_use_tags_as_keywords'] ) && function_exists( 'get_the_tags' ) ) {
					$tags = get_the_tags( $id );
	                if ( $tags && is_array( $tags) )
		                foreach ( $tags as $tag )
		                	$keywords[] = $this->internationalize( $tag->name );
				}
	                // Ultimate Tag Warrior integration
	                global $utw;
	                if ( $utw ) {
	                	$tags = $utw->GetTagsForPost( $post );
	                	if ( is_array( $tags ) )
		                	foreach ( $tags as $tag ) {
								$tag = $tag->tag;
								$tag = str_replace( '_', ' ', $tag );
								$tag = str_replace( '-', ' ', $tag );
								$tag = stripcslashes( $tag );
		                		$keywords[] = $tag;
		                	}
	                }
	                
	                // autometa
					$autometa = stripcslashes( get_post_meta( $id, 'autometa', true ) );
	                if ( isset( $autometa ) && !empty( $autometa ) ) {
	                	$autometa_array = explode( ' ', $autometa );
	                	foreach ( $autometa_array as $e )
	                		$keywords[] = $e;
	                }

	            	if ( $aioseop_options['aiosp_use_categories'] && !is_page() ) {
		                $categories = get_the_category( $id ); 
		                foreach ( $categories as $category )
		                	$keywords[] = $this->internationalize( $category->cat_name );
	            	}
	            }
	        }
	    }
	    return $this->get_unique_keywords($keywords);
	}
	
	function get_meta_keywords() {
		global $posts;

	    $keywords = array();
	    if (is_array( $posts) ) {
	        foreach ( $posts as $post ) {
	            if ( $post ) {
	                // custom field keywords
	                $keywords_i = null;
		            $keywords_i = stripcslashes( get_post_meta( $post->ID, "_aioseop_keywords", true ) );
	                $keywords_i = str_replace( '"', '', $keywords_i );
	                if ( isset( $keywords_i) && !empty( $keywords_i ) )
	                    $keywords[] = $keywords_i;
	            }
	        }
	    }
	    return $this->get_unique_keywords( $keywords );
	}
	
	function get_unique_keywords($keywords) {
		$small_keywords = array();
		foreach ( $keywords as $word )
			$small_keywords[] = $this->strtolower( $word );

		$keywords_ar = array_unique( $small_keywords );
		return implode( ',', $keywords_ar );
	}
	
	function log( $message ) {
		if ( $this->do_log ) {
			@error_log( date( 'Y-m-d H:i:s' ) . " " . $message . "\n", 3, $this->log_file );
		}
	}

	function save_post_data( $id ) {
		$awmp_edit = $nonce = null;
		if ( isset( $_POST[ 'aiosp_edit' ] ) )				$awmp_edit = $_POST['aiosp_edit'];
		if ( isset( $_POST[ 'nonce-aioseop-edit' ] ) )		$nonce     = $_POST['nonce-aioseop-edit'];

	    if ( isset($awmp_edit) && !empty($awmp_edit) && wp_verify_nonce($nonce, 'edit-aioseop-nonce') ) {

		    foreach ( Array( 'keywords', 'description', 'title', 'disable', 'disable_analytics', 'noindex', 'nofollow', 'titleatr', 'menulabel' ) as $f ) {
				$field = "aiosp_$f";
				if ( isset( $_POST[$field] ) ) $$field = $_POST[$field];
		    }

		    delete_post_meta( $id, '_aioseop_keywords' );
		    delete_post_meta( $id, '_aioseop_description' );
		    delete_post_meta( $id, '_aioseop_noindex' );
		    delete_post_meta( $id, '_aioseop_nofollow' );
		    delete_post_meta( $id, '_aioseop_title' );
		    delete_post_meta( $id, '_aioseop_titleatr' );
		    delete_post_meta( $id, '_aioseop_menulabel' );
			
		    if ( $this->is_admin() ) {
		    	delete_post_meta($id, '_aioseop_disable' );
		    	delete_post_meta($id, '_aioseop_disable_analytics' );
			}
		
			foreach ( Array( 'keywords', 'description', 'title', 'noindex', 'nofollow', 'titleatr', 'menulabel' ) as $f ) {
				$var = "aiosp_$f";
				$field = "_aioseop_$f";
				if ( isset( $$var ) && !empty( $$var ) )
				    add_post_meta( $id, $field, $$var );
		    }

		    if (isset( $aiosp_disable ) && !empty( $aiosp_disable ) && $this->is_admin() ) {
			    add_post_meta( $id, '_aioseop_disable', $aiosp_disable );
			    if (isset( $aiosp_disable_analytics ) && !empty( $aiosp_disable_analytics ) )
				    add_post_meta( $id, '_aioseop_disable_analytics', $aiosp_disable_analytics );
			}
	    }
	}

	function display_tabbed_metabox( $post, $metabox ) {
		$tabs = $metabox['args'];
		echo '<div class="aioseop_tabs">';
		$header = $this->get_metabox_header( $tabs );
		echo $header;
		$active = "";
		foreach( $tabs as $m ) {
			echo '<div id="'.$m['id'].'" class="aioseop_tab"' . $active . '>';
			if ( !$active ) $active = ' style="display:none;"';
			$m['args'] = $m['callback_args'];
			$m['callback'][0]->{$m['callback'][1]}( $post, $m );
			echo '</div>';
		}
		echo '</div>';
	}
	
	function admin_bar_menu() {
		global $wp_admin_bar, $aioseop_admin_menu, $aioseop_options;
		if ( !empty( $aioseop_options['aiosp_admin_bar'] ) ) {
			$menu_slug = plugin_basename( __FILE__ );
			if ( function_exists( 'menu_page_url' ) )
				$url = menu_page_url( $menu_slug, 0 );
			else
				$url = esc_url( admin_url( 'admin.php?page=' . $menu_slug ) );
			$wp_admin_bar->add_menu( array( 'id' => AIOSEOP_PLUGIN_DIRNAME, 'title' => __( 'SEO', 'all_in_one_seo_pack' ), 'href' => $url ) );
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_upgrade_menu' ), 1101 );
			$aioseop_admin_menu = 1;
		}
	}
	
	function admin_bar_upgrade_menu() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array( 'parent' => AIOSEOP_PLUGIN_DIRNAME, 'title' => __( 'Upgrade To Pro', 'all_in_one_seo_pack' ), 'id' => 'aioseop-pro-upgrade', 'href' => 'http://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/', 'meta' => Array( 'target' => '_blank' ) ) );
	}

	function menu_order() {
		return 5;
	}

	function admin_menu() {
		$file = plugin_basename( __FILE__ );
		$menu_name = __( 'All in One SEO', 'all_in_one_seo_pack' );

		$this->locations['aiosp']['default_options']['nonce-aioseop-edit']['default'] = wp_create_nonce('edit-aioseop-nonce');
		
		$custom_menu_order = false;
		global $aioseop_options;
		if ( !isset( $aioseop_options['custom_menu_order'] ) )
			$custom_menu_order = true;		

		$this->update_options( );
		
		$this->add_admin_pointers();
		if ( !empty( $this->pointers ) )
			foreach( $this->pointers as $k => $p )
				if ( !empty( $p["pointer_scope"] ) && ( $p["pointer_scope"] == 'global' ) )
					unset( $this->pointers[$k] );
		
		$donated = false;
		if ( ( isset( $_POST ) ) && ( isset( $_POST['module'] ) ) && ( isset( $_POST['nonce-aioseop'] ) ) && ( $_POST['module'] == 'All_in_One_SEO_Pack' ) && ( wp_verify_nonce( $_POST['nonce-aioseop'], 'aioseop-nonce' ) ) ) {
			if ( isset( $_POST["aiosp_donate"] ) )
				$donated = $_POST["aiosp_donate"];
			if ( isset($_POST["Submit"] ) ) {
				if ( isset( $_POST["aiosp_custom_menu_order"] ) )
					$custom_menu_order = $_POST["aiosp_custom_menu_order"];
				else
					$custom_menu_order = false;				
			} else if ( ( isset($_POST["Submit_Default"] ) ) || ( ( isset($_POST["Submit_All_Default"] ) ) ) ) {
				$custom_menu_order = true;				
			}
		} else {
			if ( isset( $this->options["aiosp_donate"] ) )
				$donated = $this->options["aiosp_donate"];
			if ( isset( $this->options["aiosp_custom_menu_order"] ) )
				$custom_menu_order = $this->options["aiosp_custom_menu_order"];
		}
		if ( $custom_menu_order ) {
			add_filter( 'custom_menu_order', '__return_true' );
			add_filter( 'menu_order', array( $this, 'set_menu_order' ) );
		}
		
		if ( $donated ) {
			// Thank you for your donation
			$this->pointers['aioseop_donate'] = Array( 'pointer_target' => '#aiosp_donate_wrapper',
														'pointer_text' => '<h3>' . __( 'Thank you!', 'all_in_one_seo_pack' ) 
														. '</h3><p>' . __( 'Thank you for your donation, it helps keep this plugin free and actively developed!', 'all_in_one_seo_pack' ) . '</p>'
												 );
		}
		
		if ( $this->options['aiosp_enablecpost'] ) {
			if ( !empty( $this->options['aiosp_cpostadvanced'] ) ) {
				$this->locations['aiosp']['display'] = $this->options['aiosp_cpostactive'];
			} else {
				$this->locations['aiosp']['display'] = get_post_types( '', 'names' );
			}
		} else {
			$this->locations['aiosp']['display'] = Array( 'post', 'page' );
		}
		
		if ( $custom_menu_order )
			add_menu_page( $menu_name, $menu_name, 'manage_options', $file, Array( $this, 'display_settings_page' ) );
		else
			add_utility_page( $menu_name, $menu_name, 'manage_options', $file, Array( $this, 'display_settings_page' ) );
		
		add_meta_box('aioseop-list', "Join Our Mailing List", array( $this, 'display_extra_metaboxes'), 'aioseop_metaboxes', 'normal', 'core');
		add_meta_box('aioseop-about', "About <span style='float:right;'>Version <b>" . AIOSEOP_VERSION . "</b></span>", array( $this, 'display_extra_metaboxes'), 'aioseop_metaboxes', 'side', 'core');
		
		
		add_action( 'aioseop_modules_add_menus', Array( $this, 'add_menu' ), 5 );
		do_action( 'aioseop_modules_add_menus', $file );

		$metaboxes = apply_filters( 'aioseop_add_post_metabox', Array() );
		
		if ( !empty( $metaboxes ) ) {
			if ( $this->tabbed_metaboxes ) {
				$tabs = Array();
				$tab_num = 0;
				foreach ( $metaboxes as $m ) {
					if ( !isset( $tabs[ $m['post_type'] ] ) ) $tabs[ $m['post_type'] ] = Array();
					$tabs[ $m['post_type'] ][] = $m;
				}
				
				if ( !empty( $tabs ) ) {
					foreach( $tabs as $p => $m ) {
						$tab_num = count( $m );
						$title = $m[0]['title'];
						if ( $title != $this->plugin_name ) $title = $this->plugin_name . ' - ' . $title;
						if ( $tab_num == 1 ) {
							add_meta_box( $m[0]['id'], $title, $m[0]['callback'], $m[0]['post_type'], $m[0]['context'], $m[0]['priority'], $m[0]['callback_args'] );
						} elseif ( $tab_num > 1 ) {
							add_meta_box( $m[0]['id'] . '_tabbed', $title, Array( $this, 'display_tabbed_metabox' ), $m[0]['post_type'], $m[0]['context'], $m[0]['priority'], $m );
						}
					}
				}
			} else {
				foreach ( $metaboxes as $m ) {
					$title = $m['title'];
					if ( $title != $this->plugin_name ) $title = $this->plugin_name . ' - ' . $title;
					add_meta_box( $m['id'], $title, $m['callback'], $m['post_type'], $m['context'], $m['priority'], $m['callback_args'] );
				}
			}
		}
	}
	
	function get_metabox_header( $tabs ) {
		$header = '<ul class="aioseop_header_tabs hide">';
		$active = ' active';
		foreach( $tabs as $t ) {
			if ( $active )
				$title = __( 'Main Settings', 'all_in_one_seo_pack' );
			else
				$title = $t['title'];
			$header .= '<li><label class="aioseop_header_nav"><a class="aioseop_header_tab' . $active . '" href="#'. $t['id'] .'">'.$title.'</a></label></li>';
			$active = '';
		}
		$header .= '</ul>';
		return $header;
	}
	
	function set_menu_order( $menu_order ) {
		$order = array();
		$file = plugin_basename( __FILE__ );
		foreach ( $menu_order as $index => $item ) {
			if ( $item != $file ) $order[] = $item;
			if ( $index == 0 )    $order[] = $file;
		}
		return $order;
	}

	function display_settings_header() { ?>
		<?php
	}
	function display_settings_footer( ) {
	}

	function display_right_sidebar( ) { ?>
		
<?php
/* <label class="aioseop_generic_label"><?php _e('Click on option titles to get help!', 'all_in_one_seo_pack' ); ?></label> */
		global $wpdb;

		$somecount = $wpdb->get_var( "SELECT COUNT(1) FROM $wpdb->postmeta WHERE meta_key IN ('keywords', 'title', 'description', 'aiosp_meta', 'aiosp_disable' )" );

		if( $somecount > 0) {
		echo "<div class='error' style='text-align:center;'>
		<p><strong>Your database meta needs to be updated.  " . $somecount . " old fields remaining</strong> <em>(Back up your database before updating.)</em>
			<FORM action='' method='post' name='aioseop-migrate'>
				<input type='hidden' name='nonce-aioseop-migrate' value='" . wp_create_nonce('aioseop-migrate-nonce') . "' />
				<input type='submit' name='aioseop_migrate' class='button-primary' value='Update Database'>
			 </FORM>
		</p></div>";
		}

		if( !get_option( 'aioseop_options' ) ) {
			echo "<div class='error' style='text-align:center;'>
					<p><strong>Your database options need to be updated.</strong><em>(Back up your database before updating.)</em>
						<FORM action='' method='post' name='aioseop-migrate-options'>
							<input type='hidden' name='nonce-aioseop-migrate-options' value='" . wp_create_nonce( 'aioseop-migrate-nonce-options' ) . "' />
							<input type='submit' name='aioseop_migrate_options' class='button-primary' value='Update Database Options'>
				 		</FORM>
					</p></div>";
		}
		
?>
		<div class="aioseop_top">
			<div class="aioseop_top_sidebar aioseop_options_wrapper">
				<?php do_meta_boxes( 'aioseop_metaboxes', 'normal', Array( 'test' ) ); ?>
			</div>
		</div>
		<style>
			#wpbody-content {
				min-width: 900px;
			}
		</style>
		<div class="aioseop_right_sidebar aioseop_options_wrapper">
		
		<div class="aioseop_sidebar">
			<?php
			do_meta_boxes( 'aioseop_metaboxes', 'side', Array( 'test' ) );		
			?>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready( function($) {
					// close postboxes that should be closed
					$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
					// postboxes setup
					postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
					$('.meta-box-sortables').removeClass('meta-box-sortables');
				});
				//]]>
			</script>

		<!-- Headway Themes-->
		<div class="aioseop_advert"> 
					<div>
					<h3>Drag and Drop WordPress Design</h3>
					<p><a href="http://semperfiwebdesign.com/headwayaio/" target="_blank">Headway Themes</a> allows you to easily create your own stunning website designs! Stop using premade themes start making your own design with Headway's easy to use Drag and Drop interface. All in One SEO Pack users have an exclusive discount by using coupon code <strong>SEMPERFI30</strong> at checkout.</p>
					</div>
				<a href="http://semperfiwebdesign.com/headwayaio/" target="_blank"><img src="<?php echo AIOSEOP_PLUGIN_IMAGES_URL; ?>headwaybanner.png"></a>
		</div>
	</div>
</div>	
<?php
	}
}

