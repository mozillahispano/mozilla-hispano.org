<?php
/*
Plugin Name: All In One SEO Pack
Plugin URI: http://semperfiwebdesign.com
Description: Out-of-the-box SEO for your WordPress blog. <a href="admin.php?page=all-in-one-seo-pack/aioseop_class.php">Options configuration panel</a> | <a href="http://semperplugins.com/plugins/">Upgrade to Pro Version</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mrtorbert%40gmail%2ecom&item_name=All%20In%20One%20SEO%20Pack&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8">Donate</a> | <a href="http://semperplugins.com/support/" >Support</a> |  <a href="https://www.amazon.com/wishlist/1NFQ133FNCOOA/ref=wl_web" target="_blank" title="Amazon Wish List">Amazon Wishlist</a>
Version: 2.0.2
Author: Michael Torbert
Author URI: http://michaeltorbert.com
*/

/*
Copyright (C) 2008-2013 Michael Torbert, semperfiwebdesign.com (michael AT semperfiwebdesign DOT com)
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
 * @version 2.0.2
 */

if ( ! defined( 'AIOSEOP_VERSION' ) )
    define( 'AIOSEOP_VERSION', '2.0.2' );

if ( ! defined( 'AIOSEOP_PLUGIN_DIR' ) ) {
    define( 'AIOSEOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
} elseif ( AIOSEOP_PLUGIN_DIR != plugin_dir_path( __FILE__ ) ) {
	add_action( 'admin_notices', create_function( '', 'echo "' . "<div class='error'>" . sprintf(
				__( "%s detected a conflict; please deactivate the plugin located in %s.", 'all_in_one_seo_pack' ),
				$aioseop_plugin_name, AIOSEOP_PLUGIN_DIR ) . "</div>" . '";' ) );
	return;
}

if ( ! defined( 'AIOSEOP_PLUGIN_BASENAME' ) )
    define( 'AIOSEOP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'AIOSEOP_PLUGIN_DIRNAME' ) )
    define( 'AIOSEOP_PLUGIN_DIRNAME', dirname( AIOSEOP_PLUGIN_BASENAME ) );

if ( ! defined( 'AIOSEOP_PLUGIN_URL' ) )
    define( 'AIOSEOP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'AIOSEOP_PLUGIN_IMAGES_URL' ) )
    define( 'AIOSEOP_PLUGIN_IMAGES_URL', AIOSEOP_PLUGIN_URL . 'images/' );

if ( ! defined( 'AIOSEOP_BASELINE_MEM_LIMIT' ) )
	define( 'AIOSEOP_BASELINE_MEM_LIMIT', 268435456 ); // 256MB

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

global $aiosp, $aioseop_options, $aioseop_modules, $aioseop_module_list, $aiosp_activation, $aioseop_mem_limit, $aioseop_get_pages_start, $aioseop_admin_menu;
$aioseop_get_pages_start = $aioseop_admin_menu = 0;

$aioseop_options = get_option( 'aioseop_options' );

$aioseop_mem_limit = @ini_get( 'memory_limit' );

if ( !function_exists( 'aioseop_convert_bytestring' ) ) {
	function aioseop_convert_bytestring( $byteString ) {
		preg_match( '/^\s*([0-9.]+)\s*([KMGTPE])B?\s*$/i', $byteString, $matches );
		$num = ( float )$matches[1];
		switch ( strtoupper( $matches[2] ) ) {
			case 'E': $num = $num * 1024;
			case 'P': $num = $num * 1024;
			case 'T': $num = $num * 1024;
			case 'G': $num = $num * 1024;
			case 'M': $num = $num * 1024;
			case 'K': $num = $num * 1024;
		}
		return intval( $num );
	}
}

if ( is_array( $aioseop_options ) && isset( $aioseop_options['modules'] ) && isset( $aioseop_options['modules']['aiosp_performance_options'] ) ) {
	$perf_opts = $aioseop_options['modules']['aiosp_performance_options'];
	if ( isset( $perf_opts['aiosp_performance_memory_limit'] ) )
		$aioseop_mem_limit = $perf_opts['aiosp_performance_memory_limit'];
	if ( isset( $perf_opts['aiosp_performance_execution_time'] ) && ( $perf_opts['aiosp_performance_execution_time'] !== '' ) ) {
		@ini_set( 'max_execution_time', (int)$perf_opts['aiosp_performance_execution_time'] );
		@set_time_limit( (int)$perf_opts['aiosp_performance_execution_time'] );
	}
} else {
	$aioseop_mem_limit = aioseop_convert_bytestring( $aioseop_mem_limit );
	if ( ( $aioseop_mem_limit > 0 ) && ( $aioseop_mem_limit < AIOSEOP_BASELINE_MEM_LIMIT ) )
		$aioseop_mem_limit = AIOSEOP_BASELINE_MEM_LIMIT;
}

if ( !empty( $aioseop_mem_limit ) ) {
	if ( !is_int( $aioseop_mem_limit ) )
		$aioseop_mem_limit = aioseop_convert_bytestring( $aioseop_mem_limit );
	if ( ( $aioseop_mem_limit > 0 ) && ( $aioseop_mem_limit <= AIOSEOP_BASELINE_MEM_LIMIT ) )
		@ini_set( 'memory_limit', $aioseop_mem_limit );
}

$aiosp_activation = false;
$aioseop_module_list = Array( 'performance' ); // list all available modules here

if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
	add_action( 'admin_notices', create_function( '', 'echo "<div class=\'error\'>The All In One SEO Pack class is already defined";'
	. "if ( class_exists( 'ReflectionClass' ) ) { \$r = new ReflectionClass( 'All_in_One_SEO_Pack' ); echo ' in ' . \$r->getFileName(); } "
	. ' echo ", preventing All In One SEO Pack from loading.</div>";' ) );
	return;	
}

require_once( AIOSEOP_PLUGIN_DIR . 'aioseop_functions.php' );

require_once( AIOSEOP_PLUGIN_DIR . 'aioseop_class.php' );

$aiosp = new All_in_One_SEO_Pack();

register_activation_hook( __FILE__, 'aioseop_activate' );

add_action( 'after_setup_theme', 'aioseop_load_modules' );

add_action( 'init', array( $aiosp, 'add_hooks' ) );

if ( is_admin() ) {
	add_action( 'wp_ajax_aioseop_ajax_save_meta',	'aioseop_ajax_save_meta' );
}

if ( aioseop_option_isset( 'aiosp_unprotect_meta' ) )
	add_filter( 'is_protected_meta', 'aioseop_unprotect_meta', 10, 3 );
