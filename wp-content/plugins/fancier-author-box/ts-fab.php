<?php

/*
Plugin Name: Fancier Author Box
Plugin URI: http://wordpress.org/extend/plugins/fancier-author-box/
Description: Adds feature rich author box to your posts, pages and custom post types. If you decide to switch to <a href="http://fanciestauthorbox.com">Fanciest Author Box</a>, please deactivate Fancier Author Box first.
Version: 1.1.2
Author: ThematoSoup
Author URI: http://thematosoup.com
License: GPL2
*/



/*
 * Include files
 * Add action links in Plugins page
 * Add meta links in Plugins page
 * Helper functions for default settings
 * Add Fancier Author Box to posts
 * Enqueue Fancier Author Box scripts and styles
 */


if ( is_admin() ) {

	// Register plugin settings page
	require_once( dirname(__FILE__) . '/includes/ts-fab-settings.php' );
	
	// Add user settings
	require_once( dirname(__FILE__) . '/includes/ts-fab-user-settings.php' );
	
}

// Include tab constructor
require_once( dirname(__FILE__) . '/includes/ts-fab-construct-tabs.php' );

// Load text domain
load_plugin_textdomain( 'ts-fab', false, 'fancier-author-box/languages' );



/**
 * Add action links in Plugins page
 *
 * @since 1.0
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ts_fab_plugin_action_links' );
function ts_fab_plugin_action_links( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/tools.php?page=fancier_author_box">' . __( 'Settings', 'ts-fab' ) . '</a>'
		),
		$links
	);

}



/**
 * Add meta links in Plugins page
 *
 * @since 1.0
 * @todo Figure out which links to use
 */
add_filter( 'plugin_row_meta', 'ts_fab_plugin_meta_links', 10, 2 );
function ts_fab_plugin_meta_links( $links, $file ) {

	$plugin = plugin_basename(__FILE__);
	
	// create link
	if ( $file == $plugin ) {
		return array_merge(
			$links,
			array( '<a href="http://codecanyon.net/item/fanciest-author-box/2504522?ref=ThematoSoup">Buy premium version from CodeCanyon</a>' )
		);
	}
	return $links;

}



/**
 * Helper functions to get default option for Display Settings
 *
 * Default options are not stored in a database, these arrays are used instead, unless the option has been set
 *
 * @since 1.0
 */
function ts_fab_get_display_settings() {

	$default_display_settings = array(
		'show_in_posts'				=> 'below',
		'show_in_pages'				=> 'below',
		'latest_posts_count'		=> 3,
		
		'inactive_tab_background'	=> '#e9e9e9',
		'inactive_tab_border'		=> '#e9e9e9',
		'inactive_tab_color'		=> '#333',
		
		'active_tab_background'		=> '#333',
		'active_tab_border'			=> '#333',
		'active_tab_color'			=> '#fff',
		
		'tab_content_background'	=> '#f9f9f9',
		'tab_content_border'		=> '#333',
		'tab_content_color'			=> '#555',
	);
	
	$custom_post_types_display_settings = array();
	
	$args = array(
		'public'   => true,
		'_builtin' => false
	); 
	$output = 'names';
	$operator = 'and';
	$custom_post_types = get_post_types( $args, $output, $operator ); 
	
	foreach ( $custom_post_types  as $custom_post_type ) {
		$custom_post_types_display_settings['show_in_' . $custom_post_type] = ( 'below' );
	}
	
	$default_display_settings = array_merge( $default_display_settings, $custom_post_types_display_settings );


	$display_settings = wp_parse_args( get_option( 'ts_fab_display_settings' ), $default_display_settings );
	
	return $display_settings;

}



/**
 * Add Fancier Author Box to post/page content
 *
 * @since 1.0
 */
add_filter( 'the_content', 'ts_fab_add_author_box', 15 );
function ts_fab_add_author_box( $content ) {

	global $authordata;
	global $post;
	
	// Use helper functions to get plugin settings
	$ts_fab_display_settings = ts_fab_get_display_settings();

	if ( is_singular() ) {
		if ( !get_user_meta( $authordata->ID, 'ts_fab_user_hide', false ) && !get_post_meta( $post->ID, 'ts_fab_hide', false ) ) {
	
			// Show Fancier Author Box in posts
			if ( is_singular( 'post' ) ) {
	
				$show_in_posts = $ts_fab_display_settings['show_in_posts'];
				if ( $show_in_posts == 'above' ) {
					$content = ts_fab_construct_fab( 'above', $authordata->ID ) . $content;
				} elseif ( $show_in_posts == 'below' ) {
					$content .= ts_fab_construct_fab( 'below', $authordata->ID );
				} elseif ( $show_in_posts == 'both' ) {
					$content = ts_fab_construct_fab( 'above', $authordata->ID ) . $content . ts_fab_construct_fab( 'below', $authordata->ID );
				}
	
			}
	
			// Show Fancier Author Box in pages
			if ( is_singular( 'page' ) ) {
	
				$show_in_pages = $ts_fab_display_settings['show_in_pages'];
				if ( $show_in_pages == 'above' ) {
					$content = ts_fab_construct_fab( 'above', $authordata->ID ) . $content;
				} elseif ( $show_in_pages == 'below' ) {
					$content .= ts_fab_construct_fab( 'below', $authordata->ID );
				} elseif ( $show_in_pages == 'both' ) {
					$content = ts_fab_construct_fab( 'above', $authordata->ID ) . $content . ts_fab_construct_fab( 'below', $authordata->ID );
				}
	
			}
	
			// Show Fancier Author Box in custom post types
			$args = array(
				'public'   => true,
				'_builtin' => false
			); 
			$output = 'names';
			$operator = 'and';
			$custom_post_types = get_post_types( $args, $output, $operator ); 
			foreach ( $custom_post_types  as $custom_post_type ) {
				if( is_singular( $custom_post_type ) ) {
			
					$show_in_custom = $ts_fab_display_settings['show_in_' . $custom_post_type];
					if( $show_in_custom == 'above' ) {
						$content = ts_fab_construct_fab( 'above', $authordata->ID ) . $content;
					} elseif( $show_in_custom == 'below' ) {
						$content .= ts_fab_construct_fab( 'below', $authordata->ID );
					} elseif( $show_in_custom == 'both' ) {
						$content = ts_fab_construct_fab( 'above', $authordata->ID ) . $content . ts_fab_construct_fab( 'below', $authordata->ID );
					}	
				
				}	
			}
		}	
	}
  
	return $content;

}



/**
 * Enqueue Fancier Author Box scripts and styles
 *
 * @since 1.0
 */
add_action( 'wp_enqueue_scripts', 'ts_fab_add_scripts_styles' );
function ts_fab_add_scripts_styles() {

	$css_url = plugins_url( 'css/ts-fab.min.css', __FILE__ );
	wp_register_style( 'ts_fab_css', $css_url, '', '1.0' );
	wp_enqueue_style( 'ts_fab_css' );

	$js_url = plugins_url( 'js/ts-fab.min.js', __FILE__ );
	wp_register_script( 'ts_fab_js', $js_url, array( 'jquery' ), '1.0' );
	wp_enqueue_script( 'ts_fab_js' );
	
}



/**
 * Print CSS for color options
 *
 * @since 1.0
 */
add_action( 'wp_head', 'ts_fab_print_color_settings' );
function ts_fab_print_color_settings() {

	$default_colors = array(
		'#e9e9e9',		// Inactive tab background
		'#e9e9e9',		// Inactive tab border
		'#333',			// Inactive tab text color
		
		'#333',			// Active tab background
		'#333',			// Active tab border
		'#fff',			// Active tab text color
		
		'#f9f9f9',		// Tab content background
		'#333',			// Tab content border
		'#555'			// Tab content text color
	);
	
	$options = ts_fab_get_display_settings();

	$current_colors = array(
		$options['inactive_tab_background'],
		$options['inactive_tab_border'],
		$options['inactive_tab_color'],
		
		$options['active_tab_background'],
		$options['active_tab_border'],
		$options['active_tab_color'],
		
		$options['tab_content_background'],
		$options['tab_content_border'],
		$options['tab_content_color'],
	);
	
	// Check if default colors should be used
	if( count( array_diff( $current_colors, $default_colors ) ) > 0 ) {
	?>
	<style>
	.ts-fab-list li a { background-color: <?php echo $options['inactive_tab_background']; ?>; border: 1px solid <?php echo $options['inactive_tab_border']; ?>; color: <?php echo $options['inactive_tab_color']; ?>; }
	.ts-fab-list li.active a { background-color: <?php echo $options['active_tab_background']; ?>; border: 1px solid <?php echo $options['active_tab_border']; ?>; color: <?php echo $options['active_tab_color']; ?>; }		
	.ts-fab-tab { background-color: <?php echo $options['tab_content_background']; ?>; border: 2px solid <?php echo $options['tab_content_border']; ?>; color: <?php echo $options['tab_content_color']; ?>; }		
	</style>
	<?php
	}
}