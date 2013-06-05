<?php
/**
 * @package Stealth_Publish
 * @author Scott Reilly
 * @version 2.3
 */
/*
Plugin Name: Stealth Publish
Version: 2.3
Plugin URI: http://coffee2code.com/wp-plugins/stealth-publish/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: stealth-publish
Domain Path: /lang/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Description: Prevent specified posts from being featured on the front page or in feeds, and from notifying external services of publication.

Compatible with WordPress 2.9+ through 3.5+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/stealth-publish/
*/

/*
	Copyright (c) 2007-2013 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_StealthPublish' ) ) :

class c2c_StealthPublish {

	private static $field                   = 'stealth_publish';
	private static $meta_key                = '_stealth-publish'; // Filterable via 'c2c_stealth_publish_meta_key' filter
	private static $stealth_published_posts = array(); // For memoization

	/**
	 * Returns version of the plugin.
	 *
	 * @since 2.2.1
	 */
	public static function version() {
		return '2.3';
	}

	/**
	 * Initializer
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Register actions/filters and allow for configuration
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_stealth_publish_meta_key' with default meta key name
	 */
	public static function do_init() {

		// Load textdomain
		load_plugin_textdomain( 'stealth-publish', false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'lang' );

		// Deprecated as of 2.3.
		$meta_key = apply_filters( 'stealth_publish_meta_key', self::$meta_key );

		// Apply custom filter to obtain meta key name.
		$meta_key = esc_attr( apply_filters( 'c2c_stealth_publish_meta_key', $meta_key ) );

		// Only override the meta key name if one was specified. Otherwise the
		// default remains (since a meta key is necessary)
		if ( ! empty( $meta_key ) )
			self::$meta_key = $meta_key;

		// Register hooks
		add_filter( 'posts_where',                 array( __CLASS__, 'stealth_publish_where' ), 1, 2 );
		//add_action( 'pre_get_posts',               array( __CLASS__, 'maybe_exclude_stealth_posts' ) );
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_ui' ) );
		add_filter( 'wp_insert_post_data',         array( __CLASS__, 'save_stealth_publish_status' ), 2, 2 );
		add_action( 'publish_post',                array( __CLASS__, 'publish_post' ), 1, 1 );
	}

	/*
	// This approach (instead of hooking 'posts_where') is more efficient, allowing WP to handle excluding the stealth posts
	// itself during the primary query (and no need to run an additional query) to find stealth post IDs to exclude.
	// However, it requires that ALL posts be assigned the custom field, which means hooking activation
	// Update: WP 3.5 introduces the NOT EXISTS meta query comparator.
	public static function maybe_exclude_stealth_posts( $wpquery ) {
		if ( $wpquery->is_home || $wpquery->is_feed ||
			( trailingslashit( get_option( 'siteurl' ) ) == trailingslashit( 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) ) ) {
			$wpquery->query_vars['meta_compare'] = '!=';
			$wpquery->query_vars['meta_key'] = self::$meta_key;
			$wpquery->query_vars['meta_value'] = '1';
		}
	}
	*/

	/**
	 * Draws the UI to prompt user if stealth publish should be enabled for the post.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_stealth_publish_default' with stealth publish state default (false)
	 *
	 * @return void (Text is echoed.)
	 */
	public static function add_ui() {
		global $post;

		if ( apply_filters( 'c2c_stealth_publish_default', false, $post ) )
			$value = '1';
		else
			$value = get_post_meta( $post->ID, self::$meta_key, true );

		$checked = checked( $value, '1', false );

		echo "<div class='misc-pub-section'><label class='selectit c2c-stealth-publish' for='" . self::$field . "' title='";
		esc_attr_e( 'If checked, the post will not appear on the front page or in the main feed.', 'stealth-publish' );
		echo "'>\n";
		echo "<input id='" . self::$field . "' type='checkbox' $checked value='1' name='" . self::$field . "' />\n";
		_e( 'Stealth publish?', 'stealth-publish' );
		echo '</label></div>' . "\n";
	}

	/**
	 * Update the value of the stealth publish custom field, but only if it is supplied.
	 *
	 * @since 2.0
	 *
	 * @param array $data Data
	 * @param array $postarr Array of post fields and values for post being saved
	 * @return array The unmodified $data
	 */
	public static function save_stealth_publish_status( $data, $postarr ) {
		if ( isset( $postarr['post_type'] ) &&
			 ( 'revision' != $postarr['post_type'] ) &&
			 ! ( isset( $_POST['action'] ) && 'inline-save' == $_POST['action'] )
			) {
			$new_value = isset( $postarr[ self::$field ] ) ? $postarr[ self::$field ] : '';
			update_post_meta( $postarr['ID'], self::$meta_key, $new_value );
		}
		return $data;
	}

	/**
	 * Returns an array of post IDs that are to be stealth published
	 *
	 * @since 1.0
	 *
	 * @return array Post IDs of all stealth published posts
	 */
	public static function find_stealth_published_post_ids() {
		if ( ! empty( self::$stealth_published_posts ) )
			return self::$stealth_published_posts;

		global $wpdb;
		$sql = "SELECT DISTINCT ID FROM $wpdb->posts AS p
				LEFT JOIN $wpdb->postmeta AS pm ON (p.ID = pm.post_id)
				WHERE pm.meta_key = %s AND pm.meta_value = '1'
				GROUP BY pm.post_id";
		self::$stealth_published_posts = $wpdb->get_col( $wpdb->prepare( $sql, self::$meta_key ) );
		return self::$stealth_published_posts;
	}

	/**
	 * Modifies the WP query to exclude stealth published posts from feeds and the home page
	 *
	 * @since 1.0
	 *
	 * @param string $where The current WHERE condition string
	 * @param WP_Query $wp_query The query object (not provided by WP prior to WP 3.0)
	 * @return string The potentially amended WHERE condition string to exclude stealth published posts
	 */
	public static function stealth_publish_where( $where, $wp_query = null ) {
		global $wpdb;
		if ( ! $wp_query )
			global $wp_query;

		// The third condition is for when a query_posts() (or similar) query from the front page is called that
		// undermines is_home() (such as when querying for posts in a particular category)
		if ( $wp_query->is_home || $wp_query->is_feed ||
			( trailingslashit( get_option( 'siteurl' ) ) == trailingslashit( 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) ) ) {
			$stealth_published_posts = implode( ',', self::find_stealth_published_post_ids() );
			if ( ! empty( $stealth_published_posts ) )
				$where .= " AND $wpdb->posts.ID NOT IN ($stealth_published_posts)";
		}
		return $where;
	}

	/**
	 * Handles silent publishing if the associated checkbox is checked.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_stealth_publish_silent' with stealth publish silent state default (true)
	 *
	 * @param int $post_id Post ID
	 */
	public static function publish_post( $post_id ) {
		// Deprecated as of 2.3.
		$stealth_publish_silent = (bool) apply_filters( 'stealth_publish_silent', true, $post_id );

		if ( isset( $_POST[ self::$field ] ) && $_POST[ self::$field ] && (bool) apply_filters( 'c2c_stealth_publish_silent', $stealth_publish_silent, $post_id ) )
			define( 'WP_IMPORTING', true );
	}

} // end class

c2c_StealthPublish::init();

endif;
