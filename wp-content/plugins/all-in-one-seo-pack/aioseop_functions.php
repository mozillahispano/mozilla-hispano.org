<?php
/**
 * @package All-in-One-SEO-Pack
 */
/**
 * Load the module manager.
 */
if (!function_exists('aioseop_load_modules')) {
	function aioseop_load_modules() {
		global $aioseop_modules, $aioseop_module_list;
	 	require_once( AIOSEOP_PLUGIN_DIR . 'aioseop_module_manager.php' );
	 	$aioseop_modules = new All_in_One_SEO_Pack_Module_Manager( apply_filters( 'aioseop_module_list', $aioseop_module_list ) );
	 	$aioseop_modules->load_modules();
	}
}

/**
 * Check if we just got activated.
 */
if ( !function_exists( 'aioseop_activate' ) ) {
	function aioseop_activate() {
	  global $aiosp_activation;
	  $aiosp_activation = true;
	}
}

/**
 * Check if settings need to be updated / migrated from old version.
 */
if ( !function_exists( 'aioseop_update_settings_check' ) ) {
	function aioseop_update_settings_check() {
		global $aioseop_options;
		if ( ( isset( $_POST['aioseop_migrate_options'] ) )  ||
			 ( empty( $aioseop_options ) ) )
			aioseop_mrt_mkarry();

		// WPML has now attached to filters, read settings again so they can be translated
		$aioseop_options = get_option( 'aioseop_options' );

		if ( !empty( $aioseop_options['aiosp_archive_noindex'] ) ) { // migrate setting for noindex archives
			$aioseop_options['aiosp_archive_date_noindex'] = $aioseop_options['aiosp_archive_author_noindex'] = $aioseop_options['aiosp_archive_noindex'];
			unset( $aioseop_options['aiosp_archive_noindex'] );
			update_option( 'aioseop_options', $aioseop_options );
		}
	}
}

/**
 * Initialize settings to defaults.
 */
if ( !function_exists( 'aioseop_mrt_mkarry' ) ) {
	function aioseop_mrt_mkarry() {
		global $aiosp;
		global $aioseop_options;
		$naioseop_options = $aiosp->default_options();

		if( get_option( 'aiosp_post_title_format' ) ) {
		foreach( $naioseop_options as $aioseop_opt_name => $value ) {
				if( $aioseop_oldval = get_option( $aioseop_opt_name ) ) {
					$naioseop_options[$aioseop_opt_name] = $aioseop_oldval;
				}
				if( $aioseop_oldval == '' ) {
					$naioseop_options[$aioseop_opt_name] = '';
				}
				delete_option( $aioseop_opt_name );
			}
		}
		add_option( 'aioseop_options', $naioseop_options );
		$aioseop_options = $naioseop_options;
	}
}

if ( !function_exists( 'aioseop_activate_pl' ) ) {
	function aioseop_activate_pl() {
		if( $aioseop_options = get_option( 'aioseop_options' ) ) {
			$aioseop_options['aiosp_enabled'] = "0";

			if( empty( $aioseop_options['aiosp_posttypecolumns'] ) ) {
				$aioseop_options['aiosp_posttypecolumns'] = array('post','page');
			}

			update_option('aioseop_options', $aioseop_options);
		}
	}
}

if ( !function_exists( 'aioseop_get_version' ) ) {
	function aioseop_get_version() {
		return AIOSEOP_VERSION;
	}
}

if ( !function_exists( 'aioseop_option_isset' ) ) {
	function aioseop_option_isset( $option ) {
		global $aioseop_options;
		return ( ( isset( $aioseop_options[$option] ) ) && $aioseop_options[$option] );
	}
}

if ( !function_exists( 'aioseop_addmycolumns' ) ) {
	function aioseop_addmycolumns() {
		global $aioseop_options, $pagenow;
		$aiosp_posttypecolumns = Array();
		if ( !empty( $aioseop_options) && !empty( $aioseop_options['aiosp_posttypecolumns'] ) ) {
			$aiosp_posttypecolumns = $aioseop_options['aiosp_posttypecolumns'];			
		}
		if ( !empty( $pagenow ) && ( $pagenow == 'upload.php' ) )
			$post_type = 'attachment';
		elseif ( !isset( $_GET['post_type'] ) )
			$post_type = 'post';
		else
			$post_type = $_GET['post_type'];
		add_action( 'admin_head', 'aioseop_admin_head' );
		
		if( is_array( $aiosp_posttypecolumns ) && in_array( $post_type, $aiosp_posttypecolumns ) ) {
			if ( $post_type == 'page' )
				add_filter( 'manage_pages_columns', 'aioseop_mrt_pcolumns' );
			elseif ( $post_type == 'attachment' )
				add_filter( 'manage_media_columns', 'aioseop_mrt_pcolumns' );			
			else
				add_filter( 'manage_posts_columns', 'aioseop_mrt_pcolumns' );
			if ( $post_type == 'attachment' )
				add_action( 'manage_media_custom_column', 'aioseop_mrt_pccolumn', 10, 2 );
			elseif ( is_post_type_hierarchical( $post_type ) )
				add_action( 'manage_pages_custom_column', 'aioseop_mrt_pccolumn', 10, 2 );
			else
				add_action( 'manage_posts_custom_column', 'aioseop_mrt_pccolumn', 10, 2 );
		}
	}
}

if ( !function_exists( 'aioseop_mrt_pcolumns' ) ) {
	function aioseop_mrt_pcolumns( $aioseopc ) {
		global $aioseop_options;
	    $aioseopc['seotitle'] = __( 'SEO Title', 'all_in_one_seo_pack' );
	    $aioseopc['seodesc'] = __( 'SEO Description', 'all_in_one_seo_pack' );
	    if ( empty( $aioseop_options['aiosp_togglekeywords'] ) )
			$aioseopc['seokeywords'] = __( 'SEO Keywords', 'all_in_one_seo_pack' );
	    return $aioseopc;
	}
}

if ( !function_exists( 'aioseop_admin_head' ) ) {
	function aioseop_admin_head() {
		echo '<script type="text/javascript" src="' . AIOSEOP_PLUGIN_URL . 'quickedit_functions.js" ></script>';
		?><style>
		.aioseop_edit_button {
		margin: 0 0 0 5px;
		opacity: 0.6;
		width: 12px;
		}
		.aioseop_mpc_SEO_admin_options_edit img {
			margin: 3px 2px;
			opacity: 0.7;
		}
		.aioseop_mpc_admin_meta_options {
			float: left;
			display: block;
			opacity: 1;
		}
		.aioseop_mpc_admin_meta_content {
			float:left;
			width: 100%;
			margin: 0 0 10px 0;
		}	
		</style>
		<?php wp_print_scripts( Array( 'sack' ) );
		?><script type="text/javascript">
		//<![CDATA[
		var aioseopadmin = {
			blogUrl: "<?php print get_bloginfo( 'url'); ?>", 
			pluginPath: "<?php print AIOSEOP_PLUGIN_DIR; ?>", 
			pluginUrl: "<?php print AIOSEOP_PLUGIN_URL; ?>", 
			requestUrl: "<?php print WP_ADMIN_URL . '/admin-ajax.php' ?>", 
			imgUrl: "<?php print AIOSEOP_PLUGIN_IMAGES_URL; ?>",
			Edit: "<?php _e( 'Edit', 'all_in_one_seo_pack'); ?>", Post: "<?php _e( 'Post', 'all_in_one_seo_pack'); ?>", Save: "<?php _e( 'Save', 'all_in_one_seo_pack'); ?>", Cancel: "<?php _e( 'Cancel', 'all_in_one_seo_pack'); ?>", postType: "post", 
			pleaseWait: "<?php _e( 'Please wait...', 'all_in_one_seo_pack'); ?>", slugEmpty: "<?php _e( 'Slug may not be empty!', 'all_in_one_seo_pack'); ?>", 
			Revisions: "<?php _e( 'Revisions', 'all_in_one_seo_pack'); ?>", Time: "<?php _e( 'Insert time', 'all_in_one_seo_pack'); ?>"
		}
		//]]>
		</script>
		<?php
	}
}

if ( !function_exists( 'aioseop_ajax_save_meta' ) ) {
	function aioseop_ajax_save_meta() {
		if ( !empty( $_POST['_inline_edit'] ) && ( $_POST['_inline_edit'] != 'undefined' ) )
			check_ajax_referer( 'inlineeditnonce', '_inline_edit' );
		else
			check_ajax_referer( 'screen-options-nonce', 'screenoptionnonce' );
		$post_id = intval( $_POST['post_id'] );
		$new_meta = $_POST['new_meta'];
		$target = $_POST['target_meta'];
		update_post_meta( $post_id, '_aioseop_' . $target, esc_attr( $new_meta ) );
		$result = get_post_meta( $post_id, '_aioseop_' . $target, true );
		if( $result != '' ): $label = $result;  
		else: $label = ''; $result = '<strong><i>' . __( 'No', 'all_in_one_seo_pack' ) . ' ' . $target . '</i></strong>' ; endif;
		$output = $result . '<a id="' . $target . 'editlink' . $post_id . '" href="javascript:void(0);"'; 
		$output .= 'onclick=\'aioseop_ajax_edit_meta_form(' . $post_id . ', ' . json_encode( $label ) . ', "' . $target . '");return false;\' title="' . __('Edit') . '">';
		$output .= '<img class="aioseop_edit_button" id="aioseop_edit_id" src="' . AIOSEOP_PLUGIN_IMAGES_URL . '/cog_edit.png" /></a>';
		die( "jQuery('div#aioseop_" . $target . "_" . $post_id . "').fadeOut('fast', function() {
			  jQuery('div#aioseop_" . $target . "_" . $post_id . "').html(" . json_encode( $output ) . ").fadeIn('fast');
		});" );
	}
}

if ( !function_exists( 'aioseop_ajax_init' ) ) {
	function aioseop_ajax_init() {
		if ( !empty( $_POST ) && !empty( $_POST['settings'] ) && !empty( $_POST['nonce-aioseop']) && !empty( $_POST['options'] ) ) {
			$_POST = stripslashes_deep( $_POST );
			$settings = esc_attr( $_POST['settings'] );
			if ( ! defined( 'AIOSEOP_AJAX_MSG_TMPL' ) )
			    define( 'AIOSEOP_AJAX_MSG_TMPL', "jQuery('div#aiosp_$settings').fadeOut('fast', function(){jQuery('div#aiosp_$settings').html('%s').fadeIn('fast');});" );
			if ( !wp_verify_nonce($_POST['nonce-aioseop'], 'aioseop-nonce') ) die( sprintf( AIOSEOP_AJAX_MSG_TMPL, __( "Unauthorized access; try reloading the page.", 'all_in_one_seo_pack' ) ) );
		} else {
			die(0);
		}
	}
}

if ( !function_exists( 'aioseop_ajax_save_url' ) ) {
	function aioseop_ajax_save_url() {
		aioseop_ajax_init();
		$options = Array();
		parse_str( $_POST['options'], $options );
		foreach( $options as $k => $v ) $_POST[$k] = $v;
		$_POST['action'] = 'aiosp_update_module';
		global $aiosp, $aioseop_module_list, $aioseop_modules;
		aioseop_load_modules( $aioseop_module_list );
		$aiosp->admin_menu();
		$module = $aioseop_modules->return_module( "All_in_One_SEO_Pack_Sitemap" );
		$_POST['location'] = null;
		$_POST['Submit'] = 'ajax';
		$module->add_page_hooks();
		$_POST = $module->get_current_options( $_POST, null );
		$module->handle_settings_updates( null );
		$options = $module->get_current_options( Array(), null );			
		$output = $module->display_custom_options( '', Array( 'name' => 'aiosp_sitemap_addl_pages', 'type' => 'custom', 'save' => true, 'value' => $options['aiosp_sitemap_addl_pages'], 'attr' => '' ) );
		$output = str_replace( "'", "\'", $output );
		$output = str_replace( "\n", '\n', $output );
		die( sprintf( AIOSEOP_AJAX_MSG_TMPL, $output ) );
	}
}

if ( !function_exists( 'aioseop_ajax_delete_url' ) ) {
	function aioseop_ajax_delete_url() {
		aioseop_ajax_init();
		$options = Array();
		$options = esc_attr( $_POST['options'] );
		$_POST['action'] = 'aiosp_update_module';
		global $aiosp, $aioseop_module_list, $aioseop_modules;
		aioseop_load_modules( $aioseop_module_list );
		$aiosp->admin_menu();
		$module = $aioseop_modules->return_module( "All_in_One_SEO_Pack_Sitemap" );
		$_POST['location'] = null;
		$_POST['Submit'] = 'ajax';
		$module->add_page_hooks();
		$_POST = $module->get_current_options( $_POST, null );
		if ( !empty( $_POST['aiosp_sitemap_addl_pages'] ) && ( !empty( $_POST['aiosp_sitemap_addl_pages'][ $options ] ) ) ) {
			unset( $_POST['aiosp_sitemap_addl_pages'][ $options ] );
			if ( empty( $_POST['aiosp_sitemap_addl_pages'] ) )
				$_POST['aiosp_sitemap_addl_pages'] = '';
			else
				$_POST['aiosp_sitemap_addl_pages'] = serialize( $_POST['aiosp_sitemap_addl_pages'] );
			$module->handle_settings_updates( null );
			$options = $module->get_current_options( Array(), null );
			$output = $module->display_custom_options( '', Array( 'name' => 'aiosp_sitemap_addl_pages', 'type' => 'custom', 'save' => true, 'value' => $options['aiosp_sitemap_addl_pages'], 'attr' => '' ) );
			$output = str_replace( "'", "\'", $output );
			$output = str_replace( "\n", '\n', $output );
		} else {
			$output = sprintf( __( "Row %s not found; no rows were deleted.", 'all_in_one_seo_pack' ), esc_attr( $options ) );
		}
		die( sprintf( AIOSEOP_AJAX_MSG_TMPL, $output ) );
	}
}

if (!function_exists('aioseop_ajax_save_settings')) {
	function aioseop_ajax_save_settings() {
		aioseop_ajax_init();
		$options = Array();
		parse_str( $_POST['options'], $options );
		$_POST = $options;
		$_POST['action'] = 'aiosp_update_module';
		global $aiosp, $aioseop_module_list, $aioseop_modules;
		aioseop_load_modules( $aioseop_module_list );
		$aiosp->admin_menu();
		$module = $aioseop_modules->return_module( $_POST['module'] );
		unset( $_POST['module'] );
		if ( empty( $_POST['location'] ) ) $_POST['location'] = null;
		$_POST['Submit'] = 'ajax';
		$module->add_page_hooks();
//		$_POST = $module->get_current_options( $_POST, $_POST['location'] );
		$output = $module->handle_settings_updates( $_POST['location'] );
		$output = '<div id="aioseop_settings_header"><div id="message" class="updated fade"><p>' . $output . '</p></div></div><style>body.all-in-one-seo_page_all-in-one-seo-pack-aioseop_feature_manager .aioseop_settings_left { margin-top: 45px !important; }</style>';
		die( sprintf( AIOSEOP_AJAX_MSG_TMPL, $output ) );
	}
}

if (!function_exists('aioseop_ajax_get_menu_links')) {
	function aioseop_ajax_get_menu_links() {
		aioseop_ajax_init();
		$options = Array();
		parse_str( $_POST['options'], $options );
		$_POST = $options;
		$_POST['action'] = 'aiosp_update_module';
		global $aiosp, $aioseop_module_list, $aioseop_modules;
		aioseop_load_modules( $aioseop_module_list );
		$aiosp->admin_menu();
		if ( empty( $_POST['location'] ) ) $_POST['location'] = null;
		$_POST['Submit'] = 'ajax';
//		$module->add_page_hooks();
		
//		include_once( ABSPATH . "/wp-admin/admin.php" );
		
		$modlist = $aioseop_modules->get_loaded_module_list();
		$links = Array();
		$link_list = Array();
		$link = $aiosp->get_admin_links();
		if ( !empty( $link ) )
			foreach( $link as $l )
				if ( !empty( $l ) ) {
					if ( empty( $link_list[$l['order']] ) ) $link_list[$l['order']] = Array();
					$link_list[$l['order']][$l['title']] = $l['href'];							
				}
		if ( !empty( $modlist ) )
			foreach( $modlist as $k => $v ) {
				$mod = $aioseop_modules->return_module( $v );
				if ( is_object( $mod ) ) {
					$mod->add_page_hooks();
					$link = $mod->get_admin_links();
					foreach( $link as $l )
						if ( !empty( $l ) ) {
							if ( empty( $link_list[$l['order']] ) ) $link_list[$l['order']] = Array();
							$link_list[$l['order']][$l['title']] = $l['href'];							
						}
				}
			}
		if ( !empty( $link_list ) ) {
			ksort( $link_list );
			foreach( $link_list as $ll )
				foreach( $ll as $k => $v )
					$links[$k] = $v;
		}
		$output = "<ul>";
		if ( !empty( $links ) )
			foreach( $links as $k => $v ) {
				if ( $k == "Feature Manager" )
					$current = ' class="current"';
				else
					$current = '';
				$output .= "<li{$current}><a href='" . esc_url($v) . "'>" . esc_attr( $k ) . "</a></li>";
			}
		$output .= "</ul>";
		die( sprintf( "jQuery('{$_POST['target']}').fadeOut('fast', function(){jQuery('{$_POST['target']}').html('%s').fadeIn('fast');});", addslashes( $output ) ));
	}
}

if ( !function_exists( 'aioseop_mrt_pccolumn' ) ) {
	function aioseop_mrt_pccolumn($aioseopcn, $aioseoppi) {
		$id = $aioseoppi;
		$target = null;
		if( $aioseopcn == 'seotitle' ) $target = 'title';
		if( $aioseopcn == 'seokeywords' ) $target = 'keywords';
		if( $aioseopcn == 'seodesc' ) $target = 'description';
		if ( !$target ) return;
		if( current_user_can( 'edit_post', $id ) ) { ?>
			<div class="aioseop_mpc_admin_meta_container">
				<div 	class="aioseop_mpc_admin_meta_options" 
						id="aioseop_<?php print $target; ?>_<?php echo $id; ?>" 
						style="float:left;">
					<?php $content = htmlspecialchars( stripslashes( get_post_meta( $id, "_aioseop_" . $target,	TRUE ) ) ); 
					if( !empty($content) ): $label = esc_js( $content );  
					else: $label = ''; $content = '<strong><i>No ' . $target . '</i></strong>' ; endif;
						print $content . '<a id="' . $target . 'editlink' . $id . '" href="javascript:void(0);" onclick=\'aioseop_ajax_edit_meta_form(' .
						$id . ', "' . str_replace( Array( '"', '&quot;' ), Array( '\x22', '\x22' ), esc_js( $label ) ) . '", "' . $target . '");return false;\' title="' . __('Edit') . '">';
						print "<img class='aioseop_edit_button' 
											id='aioseop_edit_id' 
											src='" . AIOSEOP_PLUGIN_IMAGES_URL . "cog_edit.png' /></a>";
					 ?>
				</div>
			</div>
		<?php }
	}	
}

if ( !function_exists( 'aioseop_unprotect_meta' ) ) {
	function aioseop_unprotect_meta( $protected, $meta_key, $meta_type ) {
		if ( isset( $meta_key ) && ( substr( $meta_key, 0, 9 ) === '_aioseop_' ) ) return false;
		return $protected;
	}
}

if ( !function_exists( 'aioseop_mrt_exclude_this_page' ) ) {
	function aioseop_mrt_exclude_this_page( $url = null ) {
		static $excluded = false;
		if ( $excluded === false ) {
			global $aioseop_options;
			$ex_pages = '';
			if ( isset( $aioseop_options['aiosp_ex_pages'] ) )
				$ex_pages = trim( $aioseop_options['aiosp_ex_pages'] );
			if ( !empty( $ex_pages ) ) {
				$excluded = explode( ',', $ex_pages );
				if ( !empty( $excluded ) )
					foreach( $excluded as $k => $v ) {
						$excluded[$k] = trim( $v );
						if ( empty( $excluded[$k] ) ) unset( $excluded[$k] );
					}
				if ( empty( $excluded ) ) $excluded = null;
			}
		}
		if ( !empty( $excluded ) ) {
			if ( $url === null )
				$url = $_SERVER['REQUEST_URI'];
			else {
				$url = parse_url( $url );
				if ( !empty( $url['path'] ) )
					$url = $url['path'];
				else
					return false;
			}
			if ( !empty( $url ) )
				foreach( $excluded as $exedd )
				    if ( ( $exedd ) && ( stripos( $url, $exedd ) !== FALSE ) )
				       return true;
		}
		return false;
	}
}

if ( !function_exists( 'aioseop_get_pages_start' ) ) {
	function aioseop_get_pages_start( $excludes ) {
		global $aioseop_get_pages_start;
		$aioseop_get_pages_start = 1;
		return $excludes;
	}
}

if ( !function_exists( 'aioseop_get_pages' ) ) {
	function aioseop_get_pages( $pages ) {
		global $aioseop_get_pages_start;
		if ( !$aioseop_get_pages_start ) return $pages;
		foreach ( $pages as $k => $v ) {
			$postID = $v->ID;
			$menulabel = stripslashes( get_post_meta( $postID, '_aioseop_menulabel', true ) );
			if ( $menulabel ) $pages[$k]->post_title = $menulabel;
		}
		$aioseop_get_pages_start = 0;
		return $pages;
	}
}

// The following two functions are GPLed from Sarah G's Page Menu Editor, http://wordpress.org/extend/plugins/page-menu-editor/.
if ( !function_exists( 'aioseop_list_pages' ) ) {
	function aioseop_list_pages( $content ) {
		global $wp_version;
		$matches = array();
		if ( preg_match_all( '/<li class="page_item page-item-(\d+)/i', $content, $matches ) ) {
			update_postmeta_cache( array_values( $matches[1] ) );
			unset( $matches );
			if ( $wp_version >= 3.3 ) {
				$pattern = '@<li class="page_item page-item-(\d+)([^\"]*)"><a href=\"([^\"]+)">@is';
			} else {
				$pattern = '@<li class="page_item page-item-(\d+)([^\"]*)"><a href=\"([^\"]+)" title="([^\"]+)">@is';
			}
			return preg_replace_callback( $pattern, "aioseop_filter_callback", $content );
		}
		return $content;
	}
}

if ( !function_exists( 'aioseop_filter_callback' ) ) {
	function aioseop_filter_callback( $matches ) {
		if ( $matches[1] && !empty( $matches[1] ) ) $postID = $matches[1];
		if ( empty( $postID ) ) $postID = get_option( "page_on_front" );
		$title_attrib = stripslashes( get_post_meta($postID, '_aioseop_titleatr', true ) );
		if ( empty( $title_attrib ) && !empty( $matches[4] ) ) $title_attrib = $matches[4];
		if ( !empty( $title_attrib ) ) $title_attrib = ' title="' . strip_tags( $title_attrib ) . '"';
		return '<li class="page_item page-item-'.$postID.$matches[2].'"><a href="'.$matches[3].'"'.$title_attrib.'>';
	}
}

if ( !function_exists( 'aioseop_add_contactmethods' ) ) {
	function aioseop_add_contactmethods( $contactmethods ) {
		global $aioseop_options;
		if ( empty( $aioseop_options['aiosp_google_disable_profile'] ) )
			$contactmethods['googleplus'] = __( 'Google+', 'all_in_one_seo_pack' );
		return $contactmethods;
	}
}

/***
 * JSON support for PHP < 5.2
 */
if ( !function_exists( 'aioseop_load_json_services' ) ) {
	function aioseop_load_json_services() {
		static $services_json = null;
		if ( $services_json ) return $services_json;
		if ( !class_exists( 'Services_JSON' ) ) require_once( 'JSON.php' );
		if ( !$services_json ) $services_json = new Services_JSON();
		return $services_json;
	}
}

if ( !function_exists( 'json_encode' ) ) {
	function json_encode( $arg ) {
		$services_json = aioseop_load_json_services();
		return $services_json->encode( $arg );
	}
}

if ( !function_exists( 'json_decode' ) ) {
	function json_decode( $arg ) {
		$services_json = aioseop_load_json_services();
		return $services_json->decode( $arg );
	}
}
