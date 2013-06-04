<?php
/**
 * @package All-in-One-SEO-Pack
 */
/**
 * The Feature Manager class.
 */
if ( !class_exists( 'All_in_One_SEO_Pack_Feature_Manager' ) ) {
	class All_in_One_SEO_Pack_Feature_Manager extends All_in_One_SEO_Pack_Module {

		protected $module_info = Array( );

		function All_in_One_SEO_Pack_Feature_Manager( $mod ) {
			$this->name = __('Feature Manager', 'all_in_one_seo_pack');		// Human-readable name of the plugin
			$this->prefix = 'aiosp_feature_manager_';						// option prefix
			$this->file = __FILE__;									// the current file
			parent::__construct();
			$this->module_info = Array( 'performance' => Array( 'name'			=> __( 'Performance', 'all_in_one_seo_pack' ),
									'description'	=> __( 'Optimize performance related to SEO and check your system status.', 'all_in_one_seo_pack' ),
									'default'	=> 'on'),
			'coming_soon' => Array( 'name'			=> __( 'Coming Soon...', 'all_in_one_seo_pack' ),
									'description'	=> __( 'XML Sitemaps', 'all_in_one_seo_pack' ),
									'save'		=> false ) );

			// Set up default settings fields
			// name			- Human-readable name of the setting
			// help_text	- Inline documentation for the setting
			// type			- Type of field; this defaults to checkbox; currently supported types are checkbox, text, select, multiselect
			// default		- Default value of the field
			// initial_options - Initial option list used for selects and multiselects
			// Other supported options: class, id, style -- allows you to set these HTML attributes on the field

			$this->default_options = array();
			$this->module_info = apply_filters( 'aioseop_module_info', $this->module_info );
			$mod[] = 'coming_soon';
			
			foreach ( $mod  as $m ) {
				$module_name = ucwords( strtr( $m, '_', ' ' ) );
				$this->default_options["enable_$m"] = Array( 'name'		 => $this->module_info[$m]['name'],
				 											 'help_text' => $this->module_info[$m]['description'],
				 											 'type'		 => 'custom',
															 'class'	 => 'aioseop_feature',
															 'id'		 => "aioseop_$m",
															 'save'		 => true );
				
				if ( !empty( $this->module_info[$m]['image'] ) )
					$this->default_options["enable_$m"]['image'] = $this->module_info[$m]['image'];
				if ( !empty( $this->module_info[$m] ) )
					foreach( Array( 'save', 'default' ) as $option )
						if ( isset( $this->module_info[$m][$option] ) )
							$this->default_options["enable_$m"][$option] = $this->module_info[$m][$option];
			}
			
			// load initial options / set defaults
			$this->update_options( );
			add_filter( $this->prefix . 'output_option', Array( $this, 'display_option_div' ), 10, 2 );
			add_filter( $this->prefix . 'submit_options', Array( $this, 'filter_submit' ) );
		}
		
		function menu_order() {
			return 20;
		}
		
		function filter_submit( $submit, $location = null ) {
			$submit['Submit']['value'] = __( 'Update Features', 'all_in_one_seo_pack' )  . ' &raquo;';
			$submit['Submit']['class'] .= " hidden";
			$submit['Submit_Default']['value'] = __( 'Reset Features', 'all_in_one_seo_pack' ) . ' &raquo;';
			return $submit;
		}
		
		function display_option_div( $buf, $args ) {
			$name = $img = $desc = $checkbox = $class = '';
			if ( isset( $args['options']['help_text'] ) && !empty( $args['options']['help_text'] ) )
				$desc .= '<p class="aioseop_desc">' . $args['options']['help_text'] . '</p>';
			if ($args['value']) $class = ' active';
			if ( isset( $args['options']['image'] ) && !empty( $args['options']['image'] ) )
				$img .= '<p><img src="' . AIOSEOP_PLUGIN_IMAGES_URL . $args['options']['image'] . '"></p>';
			else
				$img .= '<p><span class="aioseop_featured_image' . $class . '"></span></p>';
			
			if ( $args['options']['save'] ) {
				$name = "<h3>{$args['options']['name']}</h3>";
				$checkbox .= '<input type="checkbox" onchange="jQuery(\'#' . $args["options"]["id"] . ' .aioseop_featured_image, #' . $args["options"]["id"] . ' .feature_button\').toggleClass(\'active\', this.checked);jQuery(\'input[name=Submit]\').trigger(\'click\');" style="display:none;" id="' . $args['name'] . '" name="' . $args['name'] . '"';
				if ($args['value']) $checkbox .= " CHECKED";
				$checkbox .= '><span class="button-primary feature_button' . $class . '"></span>';
			} else {
				$name = "<b>{$args['options']['name']}</b>";
			}
			if ( !empty( $args['options']['id'] ) ) $args['attr'] .= " id='{$args['options']['id']}'";
			return $buf . "<div {$args['attr']}><label for='{$args['name']}'>{$name}{$img}{$desc}{$checkbox}</label></div>";
		}
	}
}
