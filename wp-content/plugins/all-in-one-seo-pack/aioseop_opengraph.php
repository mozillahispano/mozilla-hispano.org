<?php
/**
 * @package All-in-One-SEO-Pack 
 */
/**
 * The Opengraph class.
 */
if ( !class_exists( 'All_in_One_SEO_Pack_Opengraph' ) ) {
	class All_in_One_SEO_Pack_Opengraph extends All_in_One_SEO_Pack_Module {
		var $fb_object_types;
		
		function All_in_One_SEO_Pack_Opengraph( ) {
			$this->name = __('Social Meta', 'all_in_one_seo_pack');	// Human-readable name of the plugin
			$this->prefix = 'aiosp_opengraph_';						// option prefix
			$this->file = __FILE__;									// the current file
			$this->fb_object_types = Array(
				'Activities' => Array(
					'activity' => __( 'Activity', 'all_in_one_seo_pack' ),
					'sport' => __( 'Sport', 'all_in_one_seo_pack' )
				),
				'Businesses' => Array(
					'bar' => __( 'Bar', 'all_in_one_seo_pack' ),
					'company' => __( 'Company', 'all_in_one_seo_pack' ),
					'cafe' => __( 'Cafe', 'all_in_one_seo_pack' ),
					'hotel' => __( 'Hotel', 'all_in_one_seo_pack' ),
					'restaurant' => __( 'Restaurant', 'all_in_one_seo_pack' )
				),
				'Groups' => Array(
					'cause' => __( 'Cause', 'all_in_one_seo_pack' ),
					'sports_league' => __( 'Sports League', 'all_in_one_seo_pack' ),
					'sports_team' => __( 'Sports Team', 'all_in_one_seo_pack' )
				),
				'Organizations' => Array(
					'band' => __( 'Band', 'all_in_one_seo_pack' ),
					'government' => __( 'Government', 'all_in_one_seo_pack' ),
					'non_profit' => __( 'Non Profit', 'all_in_one_seo_pack' ),
					'school' => __( 'School', 'all_in_one_seo_pack' ),
					'university' => __( 'University', 'all_in_one_seo_pack' )
				),
				'People' => Array(
					'actor' => __( 'Actor', 'all_in_one_seo_pack' ),
					'athlete' => __( 'Athlete', 'all_in_one_seo_pack' ),
					'author' => __( 'Author', 'all_in_one_seo_pack' ),
					'director' => __( 'Director', 'all_in_one_seo_pack' ),
					'musician' => __( 'Musician', 'all_in_one_seo_pack' ),
					'politician' => __( 'Politician', 'all_in_one_seo_pack' ),
					'profile' => __( 'Profile', 'all_in_one_seo_pack' ),
					'public_figure' => __( 'Public Figure', 'all_in_one_seo_pack' )
				),
				'Places' => Array(
					'city' => __( 'City', 'all_in_one_seo_pack' ),
					'country' => __( 'Country', 'all_in_one_seo_pack' ),
					'landmark' => __( 'Landmark', 'all_in_one_seo_pack' ),
					'state_province' => __( 'State Province', 'all_in_one_seo_pack' )
				),
				'Products and Entertainment' => Array(
					'album' => __( 'Album', 'all_in_one_seo_pack' ),
					'book' => __( 'Book', 'all_in_one_seo_pack' ),
					'drink' => __( 'Drink', 'all_in_one_seo_pack' ),
					'food' => __( 'Food', 'all_in_one_seo_pack' ),
					'game' => __( 'Game', 'all_in_one_seo_pack' ),
					'movie' => __( 'Movie', 'all_in_one_seo_pack' ),
					'product' => __( 'Product', 'all_in_one_seo_pack' ),
					'song' => __( 'Song', 'all_in_one_seo_pack' ),
					'tv_show' => __( 'TV Show', 'all_in_one_seo_pack' )
				),'Websites' => Array(
					'article' => __( 'Article', 'all_in_one_seo_pack' ),
					'blog' => __( 'Blog', 'all_in_one_seo_pack' ),
					'website' => __( 'Website', 'all_in_one_seo_pack' )
				)
			);
			parent::__construct();
			$categories = Array( 'blog' => 'blog', 'website' => 'website' );
			
			$help_text = Array(
				"setmeta" 				=> __( "Checking this box will use the Home Title and Home Description set in All in One SEO Pack, General Settings as the Open Graph title and description for your home page.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"key"	  				=> __( "Your Profile Admin ID is your Facebook profile ID. You can find out your Facebook ID using the lookup tool here: https://graph.facebook.com/yourusername<br />NOTE: Replace 'yourusername' with your Facebook profile name.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"sitename"				=> __( "The Site Name is the name that is used to identify your website.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"hometitle"				=> __( "The Home Title is the Open Graph title for your home page.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"description"			=> __( "The Home Description is the Open Graph description for your home page.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"generate_descriptions"	=> __( "Check this and your Open Graph descriptions will be auto-generated from your content.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"disable_jetpack"		=> __( "Check this box to disable the Open Graph meta output by the Jetpack plugin.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"defimg"				=> __( "This option lets you choose which image will be displayed by default for the Open Graph image. You may override this on individual posts.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"dimg"					=> __( "This option sets a default image that can be used for the Open Graph image. You can upload an image, select an image from your Media Library or paste the URL of an image here.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"meta_key"				=> __( "Enter the name of a custom field (or multiple field names separated by commas) to use that field to specify the Open Graph image on Pages or Posts.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"categories"			=> __( "Set the Open Graph type for your website as either a blog or a website.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"image"					=> __( "This option lets you select the Open Graph image that will be used for this Page or Post, overriding the default settings.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"customimg"				=> __( "This option lets you upload an image to use as the Open Graph image for this Page or Post.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"imagewidth"			=> __( "Enter the width for your Open Graph image in pixels (i.e. 600).<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"imageheight"			=> __( "Enter the height for your Open Graph image in pixels (i.e. 600).<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"types"					=> __( "Select which Post Types you want to use All in One SEO Pack to set Open Graph meta values for.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"title"					=> __( "This is the Open Graph title of this Page or Post.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"desc"					=> __( "This is the Open Graph description of this Page or Post.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' ),
				"category"				=> __( "Select the Open Graph type that best describes the content of this Page or Post.<br /><a href='http://semperplugins.com/documentation/social-meta-module/' target='_blank'>Click here for documentation on this setting</a>", 'all_in_one_seo_pack' )
			);
			$count_desc = __( " characters. Open Graph allows up to a maximum of %s chars for the %s.", 'all_in_one_seo_pack' );
			$this->default_options = array(
					'scan_header'=> Array( 'name' => __( 'Scan Header', 'all_in_one_seo_pack' ), 'type' => 'custom', 'save' => true ),
					'setmeta'		=> Array( 	'name'			=> __( 'Use AIOSEO Title and Description',  'all_in_one_seo_pack'), 'type' => 'checkbox' ),
					'key'			=> Array( 	'name'			=> __( 'Profile Admins ID',  'all_in_one_seo_pack'), 'default' => '', 'type' => 'text' ),
					'sitename'		=> Array( 	'name'			=> __( 'Site Name',  'all_in_one_seo_pack' ), 'default'	=> get_bloginfo('name'), 'type' => 'text' ),
					'hometitle' 	=> Array(	'name'			=> __( 'Home Title',  'all_in_one_seo_pack'),
												'default'		=> '', 'type' => 'textarea', 'condshow' => Array( 'aiosp_opengraph_setmeta' => Array( 'lhs' => "aiosp_opengraph_setmeta", 'op' => '!=', 'rhs' => 'on' ) ) ),
					'description' 	=> Array(	'name'			=> __( 'Home Description',  'all_in_one_seo_pack'),
												'default'		=> '', 'type' => 'textarea', 'condshow' => Array( 'aiosp_opengraph_setmeta' => Array( 'lhs' => "aiosp_opengraph_setmeta", 'op' => '!=', 'rhs' => 'on' ) ) ),
					'generate_descriptions' => Array( 'name'	=> __( 'Autogenerate OG Descriptions', 'all_in_one_seo_pack' ), 'default' => 1 ),
					'disable_jetpack'		=> Array( 'name'	=> __( 'Disable Jetpack Tags', 'all_in_one_seo_pack' ), 'default' => 0 ),
					'defimg'		=> Array( 	'name'			=> __( 'Select OG:Image Source', 'all_in_one_seo_pack' ), 'type' => 'select', 'initial_options' => Array( '' => __( 'Default Image' ), 'featured' => __( 'Featured Image' ), 'attach' => __( 'First Attached Image' ), 'content' => __( 'First Image In Content' ), 'custom' => __( 'Image From Custom Field' ), 'auto' => __( 'First Available Image' ) ) ),
					'dimg' 			=> Array(	'name'			=> __( 'Default OG:Image',  'all_in_one_seo_pack' ), 'default' => AIOSEOP_PLUGIN_IMAGES_URL . 'default-user-image.png', 'type' => 'image' ),
					'meta_key'		=> Array(	'name'			=> __( 'Use Custom Field For Image', 'all_in_one_seo_pack' ), 'type' => 'text', 'default' => '' ),
					'categories' 	=> Array( 	'name'	  		=> __( 'Facebook Object Type', 'all_in_one_seo_pack'),
												'type'			=> 'radio', 'initial_options' => $categories, 'default' => 'blog' ),
					'image'			=> Array(	'name'			=> __( 'Image', 'all_in_one_seo_pack' ),
					 							'type'			=> 'radio', 'initial_options' => Array( 0 => '<img style="width:50px;height:auto;display:inline-block;vertical-align:bottom;" src="' . AIOSEOP_PLUGIN_IMAGES_URL . 'default-user-image.png' . '">' ) ),
					'customimg'		=> Array(	'name'			=> __( 'Custom Image', 'all_in_one_seo_pack' ),
					 							'type'			=> 'image' ),
					'imagewidth'	=> Array(	'name'			=> __( 'Specify Image Width', 'all_in_one_seo_pack' ),
											 	'type'			=> 'text', 'default' => '' ),
					'imageheight'	=> Array(	'name'			=> __( 'Specify Image Height', 'all_in_one_seo_pack' ),
											 	'type'			=> 'text', 'default' => '' ),
					'types' 		=> Array( 	'name'	  		=> __( 'Enable Facebook Meta for', 'all_in_one_seo_pack'),
												'type'			=> 'multicheckbox', 'initial_options' => $this->get_post_type_titles( Array( '_builtin' => false ) ),
												'default'		=> Array( 'post' => 'post', 'page' => 'page' ) ),
					'title' 		=> Array(	'name'			=> __( 'Title',  'all_in_one_seo_pack'),
												'default'		=> '', 'type' => 'text', 'size' => 95, 'count' => 1, 'count_desc' => $count_desc ),
					'desc'			=> Array(	'name'			=> __( 'Description',  'all_in_one_seo_pack'),
												'default'		=> '', 'type' => 'textarea', 'cols' => 250, 'rows' => 4, 'count' => 1, 'count_desc' => $count_desc ),
					'category'		=> Array(	'name'	  		=> __( 'Facebook Object Type', 'all_in_one_seo_pack'),
												'type'			=> 'select', 'style' => '',
												'initial_options' => $this->fb_object_types,
												'default'		=> ''
										)
			);
			
			if ( !empty( $help_text ) )
				foreach( $help_text as $k => $v )
					$this->default_options[$k]['help_text'] = $v;
			
			// load initial options / set defaults
			$this->update_options( );

			$display = Array();
			if ( isset( $this->options['aiosp_opengraph_types'] ) ) $display = $this->options['aiosp_opengraph_types'];

			$this->locations = array(
				'opengraph'	=> 	Array( 'name' => $this->name, 'prefix' => 'aiosp_', 'type' => 'settings',
									   'options' => Array('scan_header', 'setmeta', 'key', 'sitename', 'hometitle', 'description', 'disable_jetpack', 'generate_descriptions', 'defimg', 'dimg', 'meta_key', 'categories', 'types') ),
				'settings'	=>	Array(	'name'		=> __('Social Settings', 'all_in_one_seo_pack'),
														  'type'		=> 'metabox', 'help_link' => 'http://semperplugins.com/documentation/social-meta-module/#pagepost_settings',
														  'options'	=> Array( 'title', 'desc', 'image', 'customimg', 'imagewidth', 'imageheight', 'category' ),
														  'display' => $display, 'prefix' => 'aioseop_opengraph_'
									)
			);
			
			$this->layout = Array(
				'default' => Array(
						'name' => __( 'Social Meta', 'all_in_one_seo_pack' ),
						'help_link' => 'http://semperplugins.com/documentation/social-meta-module/',
						'options' => Array() // this is set below, to the remaining options -- pdb
					),
				'scan_meta'  => Array(
						'name' => __( 'Scan Social Meta', 'all_in_one_seo_pack' ),
						'help_link' => 'http://semperplugins.com/documentation/social-meta-module/#scan_meta',
						'options' => Array( 'scan_header' )
					)
			);
			
			$other_options = Array();
			foreach( $this->layout as $k => $v )
				$other_options = array_merge( $other_options, $v['options'] );
			
			$this->layout['default']['options'] = array_diff( array_keys( $this->default_options ), $other_options );
			
			add_action( 'admin_init', Array( $this, 'debug_post_types' ), 5 );	
			if( !is_admin() || defined( 'DOING_AJAX' ) ){ $this->do_opengraph(); }
		}
		
		function settings_page_init() {
			add_filter( 'aiosp_output_option', Array( $this, 'display_custom_options' ), 10, 2 );
			$cat = $this->options["{$this->prefix}categories"];
			if ( !empty( $cat ) ) {
				if ( $cat == 'blog' ) {
					$show_on_front = get_option( 'show_on_front' );
					if ( ( $show_on_front == 'page' ) && ( get_option( 'page_on_front' ) ) ) {
						$this->output_error( '<p>' . __( "Static front page detected, suggested Facebook Object Type is 'website'.", 'all_in_one_seo_pack' ) . '</p>' );
					}
				} elseif ( $cat == 'website' ) {
					$show_on_front = get_option( 'show_on_front' );
					if ( ( $show_on_front == 'posts' ) )
						$this->output_error( '<p>' . __( "Blog on front page detected, suggested Facebook Object Type is 'blog'.", 'all_in_one_seo_pack' ) . '</p>' );
				}
			}
		}
		
		function filter_options( $options, $location ) {
			if ( $location == 'settings' ) {
				$prefix = $this->get_prefix( $location ) . $location . '_';
				list( $legacy, $images ) = $this->get_all_images( $options );
				if ( isset( $options ) && isset( $options["{$prefix}image"] ) ) {
					$thumbnail = $options["{$prefix}image"];
					if ( ctype_digit( (string)$thumbnail ) || ( $thumbnail == 'post' ) ) {
						if ( $thumbnail == 'post' )
							$thumbnail = $images['post1'];
						else
							if ( !empty( $legacy[$thumbnail] ) )
								$thumbnail = $legacy[$thumbnail];
					}
					$options["{$prefix}image"] = $thumbnail;
				}
				if ( empty( $options[ $prefix . 'image' ] ) ) {
					$img = array_keys( $images );
					if ( !empty( $img ) && !empty( $img[1] ) )
						$options[ $prefix . 'image' ] = $img[1];
				}
			}
			return $options;
		}
		
		function filter_settings( $settings, $location, $current ) {
			if ( $location == 'opengraph' || $location == 'settings' ) {
				$prefix = $this->get_prefix( $location ) . $location . '_';
				if ( $location == 'opengraph' ) return $settings;
				if ( $location == 'settings'  ) {
					list( $legacy, $settings[ $prefix . 'image' ]['initial_options'] ) = $this->get_all_images( $current );
					$opts = Array( 'title', 'desc' );
					$current_post_type = get_post_type();
					if ( isset( $this->options["aiosp_opengraph_{$current_post_type}_fb_object_type"] ) ) {
						$settings[$prefix . 'category']['initial_options'] = array_merge( Array( '' => 'Default - ' . $this->options["aiosp_opengraph_{$current_post_type}_fb_object_type"] ), $settings[$prefix . 'category']['initial_options'] );
					}
				}
				if ( isset( $current[ $prefix . 'setmeta' ] ) && $current[ $prefix . 'setmeta' ] )
					foreach ( $opts as $opt )
						if ( isset( $settings[ $prefix . $opt ] ) ) {
							$settings[ $prefix . $opt ]['type'] = 'hidden';
							$settings[ $prefix . $opt ]['label'] = 'none';
							$settings[ $prefix . $opt ]['help_text'] = '';
							unset( $settings[ $prefix . $opt ]['count'] );
						}
			}
			return $settings;
		}
		
		function override_options( $options, $location, $settings ) {
			$opts = Array();
			foreach ( $settings as $k => $v ) if ( $v['save'] ) $opts[$k] = $v['default'];
			foreach( $options as $k => $v ) if ( $v === NULL ) unset( $options[$k] );
			$options = wp_parse_args( $options, $opts );
			return $options;
		}
		
		function filter_metabox_options( $options, $location, $post_id ) {
			if ( $location == 'settings' ) {
				$prefix = $this->get_prefix( $location ) . $location;
				if ( !empty( $options[$prefix . '_customimg'] ) ) {
					$old_options = get_post_meta( $post_id, '_' . $prefix );
					$prefix .= '_';
					if ( empty( $old_options[$prefix . 'customimg'] ) || ( $old_options[$prefix . 'customimg'] != $options[$prefix . 'customimg'] ) )
						$options[$prefix . 'image'] = $options[$prefix . 'customimg'];
				}
			}
			return $options;
		}
		
		/** Custom settings **/
		function display_custom_options( $buf, $args ) {
			if ( $args['name'] == 'aiosp_opengraph_scan_header' ) {
				$buf .= '<div class="aioseop aioseop_options aiosp_opengraph_settings"><div class="aioseop_wrapper aioseop_custom_type" id="aiosp_opengraph_scan_header_wrapper"><div class="aioseop_input" id="aiosp_opengraph_scan_header" style="padding-left:20px;">';
				$args['options']['type'] = 'submit';
				$args['attr'] = " class='button-primary' ";
				$args['value'] = $args['options']['default'] = __( 'Scan Now', 'all_in_one_seo_pack' );
			}
			$buf .= __( 'Scan your site for duplicate social meta tags.', 'all_in_one_seo_pack' );
			$buf .= '<br /><br />' . $this->get_option_html( $args );
			$buf .= '</div></div></div>';
			return $buf;
		}

		function add_attributes( $output ) { // avoid having duplicate meta tags
			if ( !empty( $this->options[ 'aiosp_opengraph_disable_jetpack' ] ) )
				remove_action( 'wp_head', 'jetpack_og_tags' );
			foreach( Array( 'xmlns="http://www.w3.org/1999/xhtml"', 'xmlns:og="http://ogp.me/ns#"', 'xmlns:fb="http://www.facebook.com/2008/fbml"' ) as $xmlns ) {
				if ( strpos( $output, $xmlns ) === false ) {
					$output .= "\n\t$xmlns ";
				}
			}
			return $output;
		}
		
		function add_meta( ) {
			global $post, $aiosp, $aioseop_options, $wp_query;
			$metabox = $this->get_current_options( Array(), 'settings' );
			$key = $this->options['aiosp_opengraph_key'];
			$dimg = $this->options['aiosp_opengraph_dimg'];
			$current_post_type = get_post_type();
			$title = $description = $image = '';
			
			$sitename = $this->options['aiosp_opengraph_sitename'];
			
			if ( !empty( $aioseop_options['aiosp_hide_paginated_descriptions'] ) ) {
				$first_page = false;
				if ( $aiosp->get_page_number() < 2 ) $first_page = true;				
			} else {
				$first_page = true;
			}
			$url = $aiosp->aiosp_mrt_get_url( $wp_query );
			$url = apply_filters( 'aioseop_canonical_url',$url );			
			$setmeta = $this->options['aiosp_opengraph_setmeta'];
			if ( is_home( ) || $aiosp->is_static_front_page() ) {
				$title = $this->options['aiosp_opengraph_hometitle'];
				if ( $first_page )
					$description = $this->options['aiosp_opengraph_description'];
				$type = $this->options['aiosp_opengraph_categories'];
				$thumbnail = $this->options['aiosp_opengraph_dimg'];
				
				/* If Use AIOSEO Title and Desc Selected */
				if( $setmeta ) {
					$title = $aioseop_options['aiosp_home_title'];
					if ( $first_page )
						$description = $aioseop_options['aiosp_home_description'];
				}
				
				/* Add some defaults */
				if( empty($title) ) $title = get_bloginfo('name');
				if( empty($sitename) ) $sitename = get_bloginfo('name');
				
				if ( empty( $description ) && $first_page && ( !empty( $this->options['aiosp_opengraph_generate_descriptions'] ) ) )
					$description = $aiosp->trim_excerpt_without_filters( $aiosp->internationalize( $post->post_content ), 1000 );
				
				if ( empty($description) && $first_page ) $description = get_bloginfo('description');
			
			} elseif ( is_singular( ) && $this->option_isset('types') 
						&& is_array( $this->options['aiosp_opengraph_types'] ) 
						&& in_array( $current_post_type, $this->options['aiosp_opengraph_types'] ) ) {
				if ( !empty( $metabox['aioseop_opengraph_settings_category'] ) ) {
					$type = $metabox['aioseop_opengraph_settings_category'];
				} elseif ( isset( $this->options["aiosp_opengraph_{$current_post_type}_fb_object_type"] ) ) {
					$type = $this->options["aiosp_opengraph_{$current_post_type}_fb_object_type"];
				}
				
				$image = $metabox['aioseop_opengraph_settings_image'];
				$title = $metabox['aioseop_opengraph_settings_title'];
				$description = $metabox['aioseop_opengraph_settings_desc'];
				
				/* Add AIOSEO variables if Site Title and Desc from AIOSEOP not selected */
				global $aiosp;
				if( empty( $title ) )
					$title = $aiosp->get_aioseop_title( $post );
				if ( empty( $description ) )
					$description = trim( strip_tags( get_post_meta( $post->ID, "_aioseop_description", true ) ) );
				
				/* Add some defaults */
				if ( empty( $title ) ) $title = get_the_title();
				if ( empty( $description ) && ( $this->options['aiosp_opengraph_generate_descriptions'] ) )
					$description = $post->post_content;
				if ( empty( $type ) ) $type = 'article';
			} else return;
			
			if ( !empty( $description ) )
				$description = $aiosp->trim_excerpt_without_filters( $aiosp->internationalize( $description ), 1000 );
			
			/* Data Validation */
			$title = strip_tags( esc_attr( $title ) );
			$sitename = strip_tags( esc_attr( $sitename ) );
			$description = strip_tags( esc_attr( $description ) );
			
			if ( empty( $thumbnail ) && !empty( $image ) )
				$thumbnail = $image;
			
			/* Get the first image attachment on the post */
			// if( empty($thumbnail) ) $thumbnail = $this->get_the_image();
			
			/* Add user supplied default image */
			if( empty($thumbnail) ) {
				if ( empty( $this->options['aiosp_opengraph_defimg'] ) )
					$thumbnail = $this->options['aiosp_opengraph_dimg'];
				else {
					switch ( $this->options['aiosp_opengraph_defimg'] ) {
						case 'featured'	:	$thumbnail = $this->get_the_image_by_post_thumbnail( );
											break;
						case 'attach'	:	$thumbnail = $this->get_the_image_by_attachment( );
											break;
						case 'content'	:	$thumbnail = $this->get_the_image_by_scan( );
											break;
						case 'custom'	:	$meta_key = $this->options['aiosp_opengraph_meta_key'];
											if ( !empty( $meta_key ) && !empty( $post ) ) {
												$meta_key = explode( ',', $meta_key );
												$thumbnail = $this->get_the_image_by_meta_key( Array( 'post_id' => $post->ID, 'meta_key' => $meta_key ) );				
											}
											break;
						case 'auto'		:	$thumbnail = $this->get_the_image();
											break;
						default			:	$thumbnail = $this->options['aiosp_opengraph_dimg'];
					}
				}
			}
			
			$width = $height = '';
			if ( !empty( $thumbnail ) ) {
				if ( !empty( $metabox['aioseop_opengraph_settings_imagewidth'] ) )
					$width = $metabox['aioseop_opengraph_settings_imagewidth'];
				if ( !empty( $metabox['aioseop_opengraph_settings_imageheight'] ) )
					$height = $metabox['aioseop_opengraph_settings_imageheight'];
			}
			
			$card = 'summary';
			
			/* OG only: */
			$meta = Array(
				'facebook'	=> Array(
						'title'			=> 'og:title',
						'type'			=> 'og:type',
						'url'			=> 'og:url',
						'thumbnail'		=> 'og:image',
						'width'			=> 'og:image:width',
						'height'		=> 'og:image:height',
						'sitename'		=> 'og:site_name',
						'key'			=> 'fb:admins',
						'description'	=> 'og:description'
					),
				'twitter'	=> Array(
						'card'			=> 'twitter:card',
						'description'	=> 'twitter:description',
					)
			);
			
			// Add links to testing tools
			
			/*
			http://developers.facebook.com/tools/debug
			https://dev.twitter.com/docs/cards/preview
			http://www.google.com/webmasters/tools/richsnippets
			*/
			/*
			$meta = Array(
				'facebook'	=> Array(
						'title'			=> 'og:title',
						'type'			=> 'og:type',
						'url'			=> 'og:url',
						'thumbnail'		=> 'og:image',
						'sitename'		=> 'og:site_name',
						'key'			=> 'fb:admins',
						'description'	=> 'og:description'
					),
				'google+'	=> Array(
						'thumbnail'		=> 'image',
						'title'			=> 'name',
						'description'	=> 'description'
					),
				'twitter'	=> Array(
						'card'			=> 'twitter:card',
						'url'			=> 'twitter:url',
						'title'			=> 'twitter:title',
						'description'	=> 'twitter:description',
						'thumbnail'		=> 'twitter:image'
						
					)
			);
			*/
			
			/*
				Note -- add support for these too, as per https://dev.twitter.com/docs/cards
				
				Card Property	Description	Required
				twitter:site	@username for the website used in the card footer.	No
				twitter:site:id	Same as twitter:site, but the website's Twitter user ID instead. Note that user ids never change, while @usernames can be changed by the user.	No
				twitter:creator	@username for the content creator / author.	No
				twitter:creator:id	Same as twitter:creator, but the Twitter user's ID.	No
			*/
			
			$tags = Array(
					'facebook'	=> Array( 'name' => 'property', 'value' => 'content' ),
					'twitter'	=> Array( 'name' => 'name', 'value' => 'content' ),
					'google+'	=> Array( 'name' => 'itemprop', 'value' => 'content' )
			);
			
			foreach ( $meta as $t => $data )
				foreach ( $data as $k => $v ) {
					if ( empty( $$k ) ) $$k = '';
					$filtered_value = $$k;
					$filtered_value = apply_filters( $this->prefix . 'meta', $filtered_value, $t, $k );
					if ( !empty( $filtered_value ) ) echo '<meta ' . $tags[$t]['name'] . '="' . $v . '" ' . $tags[$t]['value'] . '="' . $filtered_value . '" />' . "\n";					
				}
		}
		
		function do_opengraph( ) {
			add_filter( 'language_attributes', Array( $this, 'add_attributes' ) );
			if ( !defined( 'DOING_AJAX' ) )
				add_action( 'aioseop_modules_wp_head', Array( $this, 'add_meta' ), 5 );	
		}
		
		function debug_post_types( ) {
			add_filter( $this->prefix . 'display_settings', Array( $this, 'filter_settings' ), 10, 3 );
			add_filter( $this->prefix . 'override_options', Array( $this, 'override_options' ), 10, 3 );
			add_filter( $this->get_prefix( 'settings' ) . 'filter_metabox_options', Array( $this, 'filter_metabox_options' ), 10, 3 );
			$post_types = $this->get_post_type_titles( );
			$rempost = array( 'revision' => 1, 'nav_menu_item' => 1 );
			$post_types = array_diff_key( $post_types, $rempost );
			$this->default_options['types']['initial_options']  = $post_types;
			foreach( $post_types as $slug => $name ) {
				$field = $slug . '_fb_object_type';
				$this->default_options[$field] = Array(
						'name' => "$name " . __( 'Object Type', 'all_in_one_seo_pack' ) . "<br />($slug)",
						'help_text' => __( 'Choose a default value that best describes the content of your post type.', 'all_in_one_seo_pack' ),
						'type'			=> 'select',
						'style'	  		=> '',
						'initial_options' => $this->fb_object_types,
						'default'		=> 'article',
						'condshow' => Array( 'aiosp_opengraph_types\[\]' => $slug )
				);
				$this->locations['opengraph']['options'][] = $field;
				$this->layout['default']['options'][] = $field;
			}
			$this->setting_options();
		}
				
		function get_all_images( $options = null ) {
			static $img = Array();
			if ( empty( $img ) ) {
				$size = apply_filters( 'post_thumbnail_size', 'large' );
				$default = $this->get_the_image_by_default();
				if ( !empty( $default ) )
				$img[$default] = 0;
				global $post, $aioseop_options, $wp_query;

				$count = 1;

				if ( !empty( $post ) ) {
					if ( !is_object( $post ) ) $post = get_post( $post );
					if ( is_object( $post ) && function_exists('get_post_thumbnail_id' ) ) {
						if ( $post->post_type == 'attachment' )
							$post_thumbnail_id = $post->ID;
						else
							$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
						if ( !empty( $post_thumbnail_id ) )	{
							$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );
							if ( is_array( $image ) )
								$img[$image[0]] = 1;
						}
					}

					$post_id = $post->ID;
					$p = $post; $w = $wp_query;

					if ( !empty( $this->options['aiosp_opengraph_meta_key'] ) )
						$meta_key = $this->options['aiosp_opengraph_meta_key'];
					if ( !empty( $meta_key ) ) {
						$image = $this->get_the_image_by_meta_key( Array( 'post_id' => $post_id, 'meta_key' => $meta_key ) );
						if ( !empty( $image ) )
							$img[$image] = $meta_key;
					}
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
					$wp_query->queried_object = $post;

					$attachments = get_children( Array( 'post_parent' 		=> $post->ID, 
														'post_status' 		=> 'inherit', 
														'post_type' 		=> 'attachment', 
														'post_mime_type'	=> 'image', 
														'order' 			=> 'ASC', 
														'orderby' 			=> 'menu_order ID' ) );
					if ( !empty( $attachments ) )
						foreach( $attachments as $id => $attachment ) {
							$image = wp_get_attachment_image_src( $id, $size );
							if ( is_array( $image ) )
								$img[$image[0]] = $id;
						}
					$matches = Array();
					preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $post->ID ), $matches );
					if ( isset( $matches ) && !empty( $matches[1] ) && !empty( $matches[1][0] ) )
						foreach( $matches[1] as $i => $m )
							$img[$m] = 'post' . $count++;
					wp_reset_postdata();
					$wp_query = $w; $post = $p;
				}
			}

			if ( !empty( $options ) && !empty( $options['aioseop_opengraph_settings_customimg'] ) ) {
				$img[$options['aioseop_opengraph_settings_customimg']] = 'customimg';				
			}
			$image = array_flip( $img );
			$images = Array();
			if ( !empty( $image ) )
				foreach( $image as $k => $v )
					$images[$v] = '<img height=150 src="' . $v . '">';
			return Array( $image, $images );
		}
		
		/*** Thanks to Justin Tadlock for the original get-the-image code - http://themehybrid.com/plugins/get-the-image ***/
		
		function get_the_image( ) {
			global $post;
			$meta_key = $this->options['aiosp_opengraph_meta_key'];
			if ( !empty( $meta_key ) && !empty( $post ) ) {
				$meta_key = explode( ',', $meta_key );
				$image = $this->get_the_image_by_meta_key( Array( 'post_id' => $post->ID, 'meta_key' => $meta_key ) );				
			}
			if ( empty( $image ) ) $image = $this->get_the_image_by_post_thumbnail( );
			if ( empty( $image ) ) $image = $this->get_the_image_by_attachment( );
			if ( empty( $image ) ) $image = $this->get_the_image_by_scan( );
			if ( empty( $image ) ) $image = $this->get_the_image_by_default( );
			return $image;
		}
		
		function get_the_image_by_meta_key( $args = array() ) {

			/* If $meta_key is not an array. */
			if ( !is_array( $args['meta_key'] ) )
				$args['meta_key'] = array( $args['meta_key'] );

			/* Loop through each of the given meta keys. */
			foreach ( $args['meta_key'] as $meta_key ) {
				/* Get the image URL by the current meta key in the loop. */
				$image = get_post_meta( $args['post_id'], $meta_key, true );
				/* If a custom key value has been given for one of the keys, return the image URL. */
				if ( !empty( $image ) )
					return $image;
			}
			return false;
		}
		
		function get_the_image_by_post_thumbnail( ) {

			global $post;

			$post_thumbnail_id = null;
			if ( function_exists('get_post_thumbnail_id' ) ) {
				$post_thumbnail_id = get_post_thumbnail_id( $post->ID );				
			}
			
			if ( empty( $post_thumbnail_id ) ) 
				return false;
			
			$size = apply_filters( 'post_thumbnail_size', 'large' );
			$image = wp_get_attachment_image_src( $post_thumbnail_id, $size ); 
				return $image[0];
		}
		
		function get_the_image_by_attachment( $args = array() ) {

			global $post;
			$attachments = get_children( Array( 'post_parent' 		=> $post->ID, 
												'post_status' 		=> 'inherit', 
												'post_type' 		=> 'attachment', 
												'post_mime_type'	=> 'image', 
												'order' 			=> 'ASC', 
												'orderby' 			=> 'menu_order ID' ) );
												
			if ( empty( $attachments ) ) {
				if ( 'attachment' == get_post_type( $post->ID ) ) {
					$image = wp_get_attachment_image_src( $post->ID, 'large' );
				}
			}

			/* If no attachments or image is found, return false. */
			if ( empty( $attachments ) && empty( $image ) )
				return false;

			/* Set the default iterator to 0. */
			$i = 0;

			/* Loop through each attachment. Once the $order_of_image (default is '1') is reached, break the loop. */
			foreach ( $attachments as $id => $attachment ) {
				if ( ++$i == 1 ) {
					$image = wp_get_attachment_image_src( $id, 'large' );
					$alt = trim( strip_tags( get_post_field( 'post_excerpt', $id ) ) );
					break;
				}
			}
			
			/* Return the image URL. */
			return $image[0];
			
		}
		
		function get_the_image_by_scan( ) {
			
			global $post;
			
			/* Search the post's content for the <img /> tag and get its URL. */
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $post->ID ), $matches );

			/* If there is a match for the image, return its URL. */
			if ( isset( $matches ) && !empty( $matches[1][0] ) )
				return $matches[1][0];

			return false;
			
		}
		
		function get_the_image_by_default( $args = array() ) {
			return $this->options['aiosp_opengraph_dimg'];
		}
		
		function settings_update( ) {
			
		}
	}
}
