<?php

/*
 * Add settings page, under Tools menu
 * Contextual help callback function
 * Register settings
 * Settings sections and fields callback functions
 * Settings page callback function
 */



/**
 * Add settings page, under Tools menu
 *
 * @since 1.0
 */
function ts_fab_add_settings_page() {

	global $ts_fab_settings_page;

	$ts_fab_settings_page = add_management_page(
		'Fancier Author Box',
		'Fancier Author Box',
		'manage_options',
		'fancier_author_box',
		'ts_fab_show_settings_page'
	);
	add_action( 'admin_print_styles-' . $ts_fab_settings_page, 'ts_fab_admin_scripts' );

}
add_action( 'admin_menu', 'ts_fab_add_settings_page' );



/**
 * Enqueue admin scripts for color picker
 *
 * @since 1.0
 */
function ts_fab_admin_scripts() {

	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );
	
	$js_url = plugins_url( 'js/ts-fab-admin.min.js', dirname(__FILE__) );
	wp_enqueue_script( 'ts_fab_admin_js', $js_url, array( 'farbtastic', 'jquery' ) );
	
}



/**
 * Register settings
 *
 * Plugin stores two options arrays, one for each tab in settings page, each one has its own settings section as well
 *
 * @since 1.0
 */
add_action( 'admin_init', 'ts_fab_initialize_plugin_options' );
function ts_fab_initialize_plugin_options() {

	// If the theme options don't exist, create them.
	if( false == get_option( 'ts_fab_display_settings' ) ) {
		add_option( 'ts_fab_display_settings' );
	}

	// Add Display Settings section
	add_settings_section(
		'ts_fab_display_settings_section',
		__( 'Display Settings', 'ts-fab' ),
		'ts_fab_display_settings_callback',
		'ts_fab_display_settings'
	);

	// Add Display Settings fields
	add_settings_field(
		'show_in_posts',
		__( 'Show in posts', 'ts-fab' ),
		'ts_fab_show_in_posts_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Toggle displaying Fancier Author Box in posts', 'ts-fab' )
		)
	);

	add_settings_field(
		'show_in_pages',
		__( 'Show in pages', 'ts-fab' ),
		'ts_fab_show_in_pages_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Toggle displaying Fancier Author Box in pages.', 'ts-fab' )
		)
	);
	
	// Add a settings field for each public custom post type
	$args = array(
		'public'   => true,
		'_builtin' => false
	); 
	$output = 'names';
	$operator = 'and';
	$custom_post_types = get_post_types( $args, $output, $operator ); 
	foreach ( $custom_post_types  as $custom_post_type ) {
	
		$custom_post_type_object = get_post_type_object( $custom_post_type );
		add_settings_field(
			'show_in_' . $custom_post_type,
			__( 'Show in', 'ts-fab' ) . ' ' . $custom_post_type_object->label,
			'ts_fab_show_in_custom_post_type_callback',
			'ts_fab_display_settings',
			'ts_fab_display_settings_section',
			array(
				__( 'Toggle displaying Fancier Author Box in ' . $custom_post_type_object->label . ' custom post type.', 'ts-fab' ),
				$custom_post_type
			)
		);
		
	}	

	add_settings_field(
		'latest_posts_count',
		__( 'Latest posts', 'ts-fab' ),
		'ts_fab_latest_posts_count_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Toggle displaying latest posts tab', 'ts-fab' ),
			__( 'Number of latest posts to show:', 'ts-fab' )
		)
	);

	// Add Display Settings section
	add_settings_section(
		'ts_fab_color_settings_section',
		__( 'Color Settings', 'ts-fab' ),
		'ts_fab_color_settings_callback',
		'ts_fab_display_settings'
	);

	add_settings_field(
		'inactive_tab',
		__( 'Inactive tab colors', 'ts-fab' ),
		'ts_fab_color_picker_callback',
		'ts_fab_display_settings',
		'ts_fab_color_settings_section',
		array(
			'inactive_tab',
			array(
				'_background'	=> __( 'Background', 'ts-fab' ),
				'_border'		=> __( 'Border', 'ts-fab' ),
				'_color'		=> __( 'Color', 'ts-fab' )
			)
		)
	);

	add_settings_field(
		'active_tab',
		__( 'Active tab colors', 'ts-fab' ),
		'ts_fab_color_picker_callback',
		'ts_fab_display_settings',
		'ts_fab_color_settings_section',
		array(
			'active_tab',
			array(
				'_background'	=> __( 'Background', 'ts-fab' ),
				'_border'		=> __( 'Border', 'ts-fab' ),
				'_color'		=> __( 'Color', 'ts-fab' )
			)
		)
	);

	add_settings_field(
		'tab_content',
		__( 'Tab content colors', 'ts-fab' ),
		'ts_fab_color_picker_callback',
		'ts_fab_display_settings',
		'ts_fab_color_settings_section',
		array(
			'tab_content',
			array(
				'_background'	=> __( 'Background', 'ts-fab' ),
				'_border'		=> __( 'Border', 'ts-fab' ),
				'_color'		=> __( 'Color', 'ts-fab' )
			)
		)
	);
	// End adding Display Settings fields

	// Register Display Settings setting
	register_setting(
		'ts_fab_display_settings',
		'ts_fab_display_settings'
	);

}




/**
 * Display Settings add_settings_section function callback
 *
 * @since 1.0
 */
function ts_fab_display_settings_callback() { 

	'<p>' . _e( 'Select where and how Fancier Author Box appears in your posts, pages and custom posts.', 'ts-fab' ) . '</p>';
	
}



/**
 * Color Settings add_settings_section function callback
 *
 * @since 1.0
 */
function ts_fab_color_settings_callback() { 

	// Returns nothing
	
}



/**
 * Show in posts field callback
 *
 * @since 1.0
 */
function ts_fab_show_in_posts_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<select id="show_in_posts" name="ts_fab_display_settings[show_in_posts]">
		<option value="above" <?php selected( $options['show_in_posts'], 'above', true); ?>><?php _e( 'Above', 'ts-fab' ); ?></option>
		<option value="below" <?php selected( $options['show_in_posts'], 'below', true); ?>><?php _e( 'Below', 'ts-fab' ); ?></option>
		<option value="both" <?php selected( $options['show_in_posts'], 'both', true); ?>><?php _e( 'Both', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options['show_in_posts'], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select><br />

<?php }



/**
 * Show in pages field callback
 *
 * @since 1.0
 */
function ts_fab_show_in_pages_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<select id="show_in_pages" name="ts_fab_display_settings[show_in_pages]">
		<option value="above" <?php selected( $options['show_in_pages'], 'above', true); ?>><?php _e( 'Above', 'ts-fab' ); ?></option>
		<option value="below" <?php selected( $options['show_in_pages'], 'below', true); ?>><?php _e( 'Below', 'ts-fab' ); ?></option>
		<option value="both" <?php selected( $options['show_in_pages'], 'both', true); ?>><?php _e( 'Both', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options['show_in_pages'], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select><br />

<?php }



/**
 * Show in custom post types callback
 *
 * @since 1.0
 */
function ts_fab_show_in_custom_post_type_callback( $args ) { 

	$options = ts_fab_get_display_settings();
	$custom_post_type = 'show_in_' . $args[1]; ?>

	<select id="<?php echo $custom_post_type; ?>" name="ts_fab_display_settings[<?php echo $custom_post_type; ?>]">
		<option value="above" <?php selected( $options["$custom_post_type"], 'above', true); ?>><?php _e( 'Above', 'ts-fab' ); ?></option>
		<option value="below" <?php selected( $options["$custom_post_type"], 'below', true); ?>><?php _e( 'Below', 'ts-fab' ); ?></option>
		<option value="both" <?php selected( $options["$custom_post_type"], 'both', true); ?>><?php _e( 'Both', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options["$custom_post_type"], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select><br />

<?php }



/**
 * Color picker callback
 *
 * @since 1.0
 */
function ts_fab_color_picker_callback( $args ) { 

	$options = ts_fab_get_display_settings(); 
	$background = $args[0] . '_background';
	$border = $args[0] . '_border_color';
	$color = $args[0] . '_color';
	
	foreach( $args[1] as $key => $value ) {
		$field = $args[0] . $key;	
		?>

		<span>
			<input type="text" id="<?php echo $field; ?>" name="ts_fab_display_settings[<?php echo $field; ?>]" class="ts-fab-color-input"  value="<?php echo $options[$field]; ?>" />
			<a href="#" id="pickcolor_<?php echo $field; ?>" class="pickcolor" style="padding: 4px 11px; border: 1px solid #dfdfdf; margin: 0 7px 0 3px; background-color: <?php echo $options[$field]; ?>;"></a>
			<div style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
			<span class="description"><?php echo $value; ?></span>
		</span><br />
		
		<?php
	}
    		
}



/**
 * Show latest posts tab field callback
 *
 * @since 1.0
 */
function ts_fab_latest_posts_count_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<label for="latest_posts_count"><span><?php echo $args[1]; ?></span>
	<select id="latest_posts_count" name="ts_fab_display_settings[latest_posts_count]">
		<option value="1" <?php selected( $options['latest_posts_count'], 1, true); ?>>1</option>
		<option value="2" <?php selected( $options['latest_posts_count'], 2, true); ?>>2</option>
		<option value="3" <?php selected( $options['latest_posts_count'], 3, true); ?>>3</option>
		<option value="4" <?php selected( $options['latest_posts_count'], 4, true); ?>>4</option>
		<option value="5" <?php selected( $options['latest_posts_count'], 5, true); ?>>5</option>
	</select>
	</label>
	
<?php }



/**
 * Show settings page callback function
 *
 * @since 1.0
 */
function ts_fab_show_settings_page() { ?>

	<div class="wrap">
		<div id="icon-users" class="icon32"></div>
		<h2>Fancier Author Box</h2>
		<p class="description"><?php _e( 'The only author box plugin you\'ll ever need. Unless you need something fancier, like <a href="http://codecanyon.net/item/fanciest-author-box/2504522?ref=ThematoSoup">Fanciest Author Box</a>.', 'ts-fab' ); ?></p>
	</div>

	<?php settings_errors(); ?>

	<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_settings'; ?>

	<form method="post" action="options.php">
		<?php

			settings_fields( 'ts_fab_display_settings' );
			do_settings_sections( 'ts_fab_display_settings' );
			echo '<a id="ts-fab-reset-colors" href="#" style="margin:15px 0 0 230px;display:inline-block">' . __( 'Reset all color settings', 'ts-fab' ) . '</a>';

			submit_button();

		?>
	</form>

<?php }