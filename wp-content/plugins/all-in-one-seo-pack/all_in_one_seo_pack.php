<?php
/*
Plugin Name: All in One SEO Pack
Plugin URI: http://semperfiwebdesign.com
Description: Out-of-the-box SEO for your WordPress blog. <a href="options-general.php?page=all-in-one-seo-pack/aioseop.class.php">Options configuration panel</a> | <a href="http://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/">Upgrade to Pro Version</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mrtorbert%40gmail%2ecom&item_name=All%20In%20One%20SEO%20Pack&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8">Donate</a> | <a href="http://semperfiwebdesign.com/forum/" >Support</a> |  <a href="https://www.amazon.com/wishlist/1NFQ133FNCOOA/ref=wl_web" target="_blank" title="Amazon Wish List">Amazon Wishlist</a>
Version: 1.6.15.3
Author: Michael Torbert
Author URI: http://michaeltorbert.com
*/

/*
Copyright (C) 2008-2012 Michael Torbert, semperfiwebdesign.com (michael AT semperfiwebdesign DOT com)
Original code by uberdose of uberdose.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//register_activation_hook(__FILE__,'aioseop_activate_pl');

/**
 * @package All-in-One-SEO-Pack
 * @version 1.6.15.3
 */

if ( ! defined( 'AIOSEOP_VERSION' ) )
    define( 'AIOSEOP_VERSION', '1.6.15.3' );

if ( ! defined( 'AIOSEOP_PLUGIN_DIR' ) )
    define( 'AIOSEOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'AIOSEOP_PLUGIN_BASENAME' ) )
    define( 'AIOSEOP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'AIOSEOP_PLUGIN_DIRNAME' ) )
    define( 'AIOSEOP_PLUGIN_DIRNAME', dirname( AIOSEOP_PLUGIN_BASENAME ) );

if ( ! defined( 'AIOSEOP_PLUGIN_URL' ) )
    define( 'AIOSEOP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'AIOSEOP_PLUGIN_IMAGES_URL' ) )
    define( 'AIOSEOP_PLUGIN_IMAGES_URL', AIOSEOP_PLUGIN_URL . 'images/' );

if ( ! defined( 'WP_CONTENT_URL' ) )
    define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_ADMIN_URL' ) )
    define( 'WP_ADMIN_URL', get_option( 'siteurl' ) . '/wp-admin' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
	add_action( 'activation_notice', 'aioseop_class_defined_error' );
	return;
}

require_once( plugin_dir_path( __FILE__ ) . 'aioseop.class.php');

global $aiosp, $aioseop_options, $aiosp_activation;

$aiosp_activation = false;
$aioseop_options = get_option('aioseop_options');
$aioseopcc = 0;
$aiosp = new All_in_One_SEO_Pack();

require_once( AIOSEOP_PLUGIN_DIR . 'aioseop_functions.php');

////checking to see if things need to be updated

register_activation_hook( __FILE__, 'aioseop_activate' );

add_action( 'init', 'aioseop_update_settings_check' );

////end checking to see if things need to be updated

if ( $aioseop_options['aiosp_can'] == '1' || $aioseop_options['aiosp_can'] == 'on' )
        remove_action( 'wp_head', 'rel_canonical' );

add_action( 'load-edit.php', 'aioseop_addmycolumns', 1 );
add_filter( 'user_contactmethods', 'aioseop_add_contactmethods' );
add_filter( 'wp_list_pages', 'aioseop_list_pages' );
add_action( 'edit_post', array( $aiosp, 'post_meta_tags') );
add_action( 'publish_post', array( $aiosp, 'post_meta_tags') );
add_action( 'save_post', array( $aiosp, 'post_meta_tags') );
add_action( 'add_attachment', array( $aiosp, 'post_meta_tags') );
add_action( 'edit_attachment', array( $aiosp, 'post_meta_tags') );
add_action( 'edit_page_form', array( $aiosp, 'post_meta_tags') );
add_action( 'init', array( $aiosp, 'init' ), 5 );
add_action( 'wp_head', array( $aiosp, 'wp_head') );
add_action( 'template_redirect', array( $aiosp, 'template_redirect') );
add_action( 'admin_menu', array( $aiosp, 'admin_menu') );
add_action( 'admin_menu', 'aioseop_meta_box_add' );

////analytics
if ( aioseop_option_isset( 'aiosp_google_analytics_id' ) )
	add_action( 'wp_head', array( $aiosp, 'aiosp_google_analytics') );

if ( aioseop_option_isset( 'aiosp_unprotect_meta' ) )
	add_filter( 'is_protected_meta', 'aioseop_unprotect_meta', 10, 3 );

if ( ( !isset($_POST['aiosp_enabled']) || $_POST['aiosp_enabled'] == null ) &&
	 ( !isset($aioseop_options['aiosp_enabled']) || $aioseop_options['aiosp_enabled']!='1' ) ||
	 ( isset($_POST['aiosp_enabled']) && $_POST['aiosp_enabled']=='0' ) )
	add_action( 'admin_notices', 'aioseop_activation_notice');

global $aioseop_get_pages_start;
$aioseop_get_pages_start = 0;
add_filter( 'wp_list_pages_excludes', 'aioseop_get_pages_start');
add_filter( 'get_pages', 'aioseop_get_pages' );
