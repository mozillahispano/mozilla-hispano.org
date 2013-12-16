<?php
/*----------------------------------------------------------------------------------------------------------------------
Plugin Name: Yet Another Related Posts Plugin
Description: Adds related posts to your site and in RSS feeds, based on a powerful, customizable algorithm. Enabling YARPP Pro gives you access to even more powerful features. <a href="http://yarpp.com" target="_blank">Find out more</a>.
Version: 4.1.1
Author: Adknowledge
Author URI: http://yarpp.com/
Plugin URI: http://yarpp.com/
----------------------------------------------------------------------------------------------------------------------*/

if(!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if(!defined('WP_CONTENT_DIR')){
    $tr = get_theme_root();
    define('WP_CONTENT_DIR', substr($tr,0,strrpos($tr,'/')));
}

define('YARPP_VERSION', '4.1.1');
define('YARPP_DIR', dirname(__FILE__));
define('YARPP_NO_RELATED', ':(');
define('YARPP_RELATED', ':)');
define('YARPP_NOT_CACHED', ':/');
define('YARPP_DONT_RUN', 'X(');

/*----------------------------------------------------------------------------------------------------------------------
Sice v3.2: YARPP uses it own cache engine, which uses custom db tables by default.
Use postmeta instead to avoid custom tables by un-commenting postmeta line and comment out the tables one.
----------------------------------------------------------------------------------------------------------------------*/
/* Enable postmeta cache: */
//if(!defined('YARPP_CACHE_TYPE')) define('YARPP_CACHE_TYPE', 'postmeta');

/* Enable Yarpp cache engine - Default: */
if(!defined('YARPP_CACHE_TYPE')) define('YARPP_CACHE_TYPE', 'tables');

/* Load proper cache constants */
switch(YARPP_CACHE_TYPE){
    case 'tables':
        define('YARPP_TABLES_RELATED_TABLE', 'yarpp_related_cache');
        break;
    case 'postmeta':
        define('YARPP_POSTMETA_KEYWORDS_KEY', '_yarpp_keywords');
        define('YARPP_POSTMETA_RELATED_KEY',  '_yarpp_related');
        break;
}

/* New in 3.5: Set YARPP extra weight multiplier */
if(!defined('YARPP_EXTRA_WEIGHT')) define('YARPP_EXTRA_WEIGHT', 3);

/* Includes ----------------------------------------------------------------------------------------------------------*/
include_once(YARPP_DIR.'/includes/init_functions.php');
include_once(YARPP_DIR.'/includes/related_functions.php');
include_once(YARPP_DIR.'/includes/template_functions.php');

include_once(YARPP_DIR.'/classes/YARPP_Core.php');
include_once(YARPP_DIR.'/classes/YARPP_Widget.php');
include_once(YARPP_DIR.'/classes/YARPP_Cache.php');
include_once(YARPP_DIR.'/classes/YARPP_Cache_Bypass.php');
include_once(YARPP_DIR.'/classes/YARPP_Cache_'.ucfirst(YARPP_CACHE_TYPE).'.php');

/* WP hooks ----------------------------------------------------------------------------------------------------------*/
add_action('init', 'yarpp_init');
add_action('activate_'.plugin_basename(__FILE__), 'yarpp_plugin_activate', 10, 1);