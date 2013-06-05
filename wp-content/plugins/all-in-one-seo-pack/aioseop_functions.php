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
		if ( isset( $_POST['aioseop_migrate'] ) ) aioseop_mrt_fix_meta();
		if ( ( isset( $_POST['aioseop_migrate_options'] ) )  ||
			 ( empty( $aioseop_options ) ) ) {
			aioseop_mrt_mkarry();
		}
		// WPML has now attached to filters, read settings again so they can be translated
		$aioseop_options = get_option( 'aioseop_options' );
	}
}

/**
 * Update old settings to current format.
 */
if ( !function_exists( 'aioseop_mrt_fix_meta' ) ) {
	function aioseop_mrt_fix_meta() {
		global $wpdb, $aiosp_activation;
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_aioseop_keywords' WHERE meta_key = 'keywords'" );
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_aioseop_title' WHERE meta_key = 'title'" );
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_aioseop_description' WHERE meta_key = 'description'" );
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_aioseop_meta' WHERE meta_key = 'aiosp_meta'" );
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_aioseop_disable' WHERE meta_key = 'aiosp_disable'" );
		if ( !$aiosp_activation ) // don't echo on initial plugin activation
			echo "<div class='updated fade' style='background-color:green;border-color:green;'><p><strong>" . __( "Updating SEO post meta in database.", 'all_in_one_seo_pack' ) . "</strong></p></div>";
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
		$aiosp_posttypecolumns = $aioseop_options['aiosp_posttypecolumns'];
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
	    if ( !empty( $aioseop_options['aiosp_togglekeywords'] ) ) $aioseopc['seokeywords'] = __( 'SEO Keywords', 'all_in_one_seo_pack' );
	    $aioseopc['seodesc'] = __( 'SEO Description', 'all_in_one_seo_pack' );
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
		check_ajax_referer( 'inlineeditnonce', '_inline_edit' );
		$post_id = intval( $_POST['post_id'] );
		$new_meta = $_POST['new_meta'];
		$target = $_POST['target_meta'];
		update_post_meta( $post_id, '_aioseop_' . $target, esc_attr( $new_meta ) );
		$result = get_post_meta( $post_id, '_aioseop_' . $target, true );
		if( $result != '' ): $label = $result;  
		else: $label = ''; $result = '<strong><i>' . __( 'No', 'all_in_one_seo_pack' ) . ' ' . $target . '</i></strong>' ; endif;
		$output = $result . '<a id="' . $target . 'editlink' . $post_id . '" href="javascript:void(0);"'; 
		$output .= 'onclick="aioseop_ajax_edit_meta_form(' . $post_id . ', \'' . $label . '\', \'' . $target . '\');return false;" title="' . __('Edit') . '">';
		$output .= '<img class="aioseop_edit_button" id="aioseop_edit_id" src="' . AIOSEOP_PLUGIN_IMAGES_URL . '/cog_edit.png" /></a>';
		die( "jQuery('div#aioseop_" . $target . "_" . $post_id . "').fadeOut('fast', function() {
			  jQuery('div#aioseop_" . $target . "_" . $post_id . "').html('" . addslashes_gpc($output) . "').fadeIn('fast');
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
					<?php $content = htmlspecialchars( stripcslashes( get_post_meta( $id, "_aioseop_" . $target,	TRUE ) ) ); 
					if( !empty($content) ): $label = str_replace( "'", "\'", $content );  
					else: $label = ''; $content = '<strong><i>No ' . $target . '</i></strong>' ; endif;
						print $content . '<a id="' . $target . 'editlink' . $id . '" href="javascript:void(0);" onclick="aioseop_ajax_edit_meta_form(' .
						$id . ', \'' . $label . '\', \'' . $target . '\');return false;" title="' . __('Edit') . '">';
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
