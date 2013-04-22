<?php
/**
 * @package All-in-One-SEO-Pack 
 */

/** Ajax callback functions for quickedit functionality */
//add_action( 'wp_ajax_aioseop_ajax_save_meta', 'aioseop_ajax_save_meta');

if (!function_exists('aioseop_activate')) {
	function aioseop_activate() {
	  global $aiosp_activation;
	  $aiosp_activation = true;
	}
}

if (!function_exists('aioseop_update_settings_check')) {
	function aioseop_update_settings_check() {
		if(isset($_POST['aioseop_migrate'])) aioseop_mrt_fix_meta();
		if ( ( isset( $_POST['aioseop_migrate_options'] ) )  ||
			 ( !get_option( 'aiosp_post_title_format' ) && !get_option('aioseop_options') ) ) {
			aioseop_mrt_mkarry();
		}
	}
}

if (!function_exists('aioseop_class_defined_error')) {
	function aioseop_class_defined_error() {
		$aioseop_class_error = "The All in One SEO Pack class is already defined";
		if ( class_exists( 'ReflectionClass' ) ) {
			$r = new ReflectionClass( 'All_in_One_SEO_Pack' );
			$aioseop_class_error .= " in " . $r->getFileName();
		}
		$aioseop_class_error .= ", preventing All in One SEO Pack from loading.";
		echo "<div class='error'>$aioseop_class_error</div>";
	}
}

if (!function_exists('aioseop_mrt_fix_meta')) {
	function aioseop_mrt_fix_meta(){
		global $wpdb, $aiosp_activation;
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_aioseop_keywords' WHERE meta_key = 'keywords'");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_aioseop_title' WHERE meta_key = 'title'");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_aioseop_description' WHERE meta_key = 'description'");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_aioseop_meta' WHERE meta_key = 'aiosp_meta'");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_aioseop_disable' WHERE meta_key = 'aiosp_disable'");
		if ( !$aiosp_activation ) // don't echo on initial plugin activation
			echo "<div class='updated fade' style='background-color:green;border-color:green;'><p><strong>" . __( "Updating SEO post meta in database.", 'all_in_one_seo_pack' ) . "</strong></p></div>";
	}
}

if (!function_exists('aioseop_mrt_mkarry')) {
	function aioseop_mrt_mkarry() {
		global $aiosp;
		$naioseop_options = array(
		"aiosp_can"=>1,
		"aiosp_donate"=>0,
		"aiosp_home_title"=>null,
		"aiosp_home_description"=>'',
		"aiosp_home_keywords"=>null,
		"aiosp_max_words_excerpt"=>'something',
		"aiosp_rewrite_titles"=>1,
		"aiosp_post_title_format"=>'%post_title% | %blog_title%',
		"aiosp_page_title_format"=>'%page_title% | %blog_title%',
		"aiosp_category_title_format"=>'%category_title% | %blog_title%',
		"aiosp_archive_title_format"=>'%date% | %blog_title%',
		"aiosp_tag_title_format"=>'%tag% | %blog_title%',
		"aiosp_search_title_format"=>'%search% | %blog_title%',
		"aiosp_description_format"=>'%description%',
		"aiosp_404_title_format"=>'Nothing found for %request_words%',
		"aiosp_paged_format"=>' - Part %page%',
		"aiosp_google_analytics_id"=>null,
		"aiosp_ga_track_outbound_links"=>0,
		"aiosp_use_categories"=>0,
		"aiosp_dynamic_postspage_keywords"=>1,
		"aiosp_category_noindex"=>0,
		"aiosp_archive_noindex"=>0,
		"aiosp_tags_noindex"=>0,
		"aiosp_cap_cats"=>1,
		"aiosp_generate_descriptions"=>0,
		"aiosp_debug_info"=>null,
		"aiosp_post_meta_tags"=>'',
		"aiosp_page_meta_tags"=>'',
		"aiosp_home_meta_tags"=>'',
		"aiosp_enabled" =>0,
		"aiosp_enablecpost" => 0,
		"aiosp_use_tags_as_keywords" =>1,
		"aiosp_seopostcol" =>1,
		"aiosp_seocustptcol" => 0,
		"aiosp_posttypecolumns" => array('post','page'),
		"aiosp_do_log"=>null);
		
		if(get_option('aiosp_post_title_format')){
		foreach( $naioseop_options as $aioseop_opt_name => $value ) {
				if( $aioseop_oldval = get_option($aioseop_opt_name) ) {
					$naioseop_options[$aioseop_opt_name] = $aioseop_oldval;
				}
				if( $aioseop_oldval == '') {
					$naioseop_options[$aioseop_opt_name] = '';
				}
				delete_option($aioseop_opt_name);
			}
		}

		add_option('aioseop_options',$naioseop_options);
	}
}

if (!function_exists('aioseop_activation_notice')) {
	function aioseop_activation_notice() {
		global $aioseop_options;
		echo '<div class="error fade"><p><strong>' . __('All in One SEO Pack must be configured.', 'all_in_one_seo_pack' )
			 . ' ' . sprintf( __('Go to %s to enable and configure the plugin.', 'all_in_one_seo_pack' ), '<a href="' 
			 . admin_url( 'options-general.php?page=' . AIOSEOP_PLUGIN_DIRNAME  . '/aioseop.class.php' ) . '">' 
			 . __('the admin page', 'all_in_one_seo_pack') . '</a>' ) . '</strong><br />' 
			 . __( 'All in One SEO Pack now supports Custom Post Types and Google Analytics.', 'all_in_one_seo_pack' ) . '</p></div>';
	}
}

if (!function_exists('aioseop_activate_pl')) {
	function aioseop_activate_pl(){
		if(get_option('aioseop_options')){
			$aioseop_options = get_option('aioseop_options');
			$aioseop_options['aiosp_enabled'] = "0";

			if(!$aioseop_options['aiosp_posttypecolumns']){
				$aioseop_options['aiosp_posttypecolumns'] = array('post','page');
			}

			update_option('aioseop_options',$aioseop_options);
		}
	}
}

if (!function_exists('aioseop_get_version')) {
	function aioseop_get_version(){
		return AIOSEOP_VERSION;
	}
}

if (!function_exists('aioseop_add_plugin_row')) {
	function aioseop_add_plugin_row($links, $file) {
	echo '<td colspan="5" style="background-color:yellow;">';
	echo  wp_remote_fopen('http://aioseoppro.semperfiwebdesign.com/');
	echo '</td>';
	}
}

if (!function_exists('aioseop_option_isset')) {
	function aioseop_option_isset( $option ) {
		global $aioseop_options;
		return ( ( isset( $aioseop_options[$option] ) ) && $aioseop_options[$option] );
	}
}

if (!function_exists('aioseop_addmycolumns')) {
	function aioseop_addmycolumns(){
		$aioseop_options = get_option('aioseop_options');
		$aiosp_posttypecolumns = $aioseop_options['aiosp_posttypecolumns'];

		if ( !isset($_GET['post_type']) )
			$post_type = 'post';
		else	$post_type = $_GET['post_type'];
		add_action( 'admin_head', 'aioseop_admin_head');
		
		if(is_array($aiosp_posttypecolumns) && in_array($post_type,$aiosp_posttypecolumns)) {
			if($post_type == 'page'){
				add_action('manage_pages_custom_column', 'aioseop_mrt_pccolumn', 10, 2);
				add_filter('manage_pages_columns', 'aioseop_mrt_pcolumns');
			} else {
				add_action('manage_posts_custom_column', 'aioseop_mrt_pccolumn', 10, 2);
				add_filter('manage_posts_columns', 'aioseop_mrt_pcolumns');
			}
		}
	}
}

if (!function_exists('aioseop_mrt_pcolumns')) {
	function aioseop_mrt_pcolumns($aioseopc) {
		global $aioseop_options;
		$aioseopc['seotitle'] = __('SEO Title');
		$aioseopc['seokeywords'] = __('SEO Keywords');
		$aioseopc['seodesc'] = __('SEO Description');
		return $aioseopc;
	}	
}

if (!function_exists('aioseop_admin_head')) {
	function aioseop_admin_head() {
//		echo '<script type="text/javascript" src="' . AIOSEOP_PLUGIN_URL . 'quickedit_functions.js" ></script>';
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
			Edit: "Edit", Post: "Post", Save: "Save", Cancel: "Cancel", postType: "post", 
			pleaseWait: "Please wait...", slugEmpty: "Slug may not be empty!", 
			Revisions: "Post Revisions", Time: "Insert time"
		}
		//]]>
		</script>
		<?php
	}
}

if (!function_exists('aioseop_ajax_save_meta')) {
	function aioseop_ajax_save_meta() {
		$post_id = intval( $_POST['post_id'] );
		$new_meta = $_POST['new_meta'];
		$target = $_POST['target_meta'];
		update_post_meta( $post_id, '_aioseop_' . $target, $new_meta );
		$result = get_post_meta( $post_id, '_aioseop_' . $target, true );
		if( $result != '' ): $label = $result;  
		else: $label = ''; $result = '<strong><i>No ' . $target . '</i></strong>' ; endif;
		$output = $result . '<a id="' . $target . 'editlink' . $post_id . '" href="javascript:void(0);"'; 
		$output .= 'onclick="aioseop_ajax_edit_meta_form(' . $post_id . ', \'' . $label . '\', \'' . $target . '\');return false;" title="' . __('Edit') . '">';
		$output .= '<img class="aioseop_edit_button" id="aioseop_edit_id" src="' . AIOSEOP_PLUGIN_IMAGES_URL . '/cog_edit.png" /></a>';
		die( "jQuery('div#aioseop_" . $target . "_" . $post_id . "').fadeOut('fast', function() {
			  jQuery('div#aioseop_" . $target . "_" . $post_id . "').html('" . addslashes_gpc($output) . "').fadeIn('fast');
		});" );
	}
}

if (!function_exists('aioseop_mrt_pccolumn')) {
	function aioseop_mrt_pccolumn($aioseopcn, $aioseoppi) {
		if( $aioseopcn == 'seotitle' ) {
			echo htmlspecialchars(stripcslashes(get_post_meta($aioseoppi,'_aioseop_title',TRUE)));
		}
		if( $aioseopcn == 'seokeywords' ) {
			echo htmlspecialchars(stripcslashes(get_post_meta($aioseoppi,'_aioseop_keywords',TRUE)));
		}
		if( $aioseopcn == 'seodesc' ) {
			echo htmlspecialchars(stripcslashes(get_post_meta($aioseoppi,'_aioseop_description',TRUE)));
		}
	}	
}

if ( !function_exists( 'aioseop_unprotect_meta' ) ) {
	function aioseop_unprotect_meta( $protected, $meta_key, $meta_type ) {
		if ( isset( $meta_key ) && ( substr( $meta_key, 0, 9 ) === '_aioseop_' ) ) return false;
		return $protected;
	}
}

if (!function_exists('aioseop_get_pages_start')) {
	function aioseop_get_pages_start($excludes) {
		global $aioseop_get_pages_start;
		$aioseop_get_pages_start = 1;
		return $excludes;
	}
}

if (!function_exists('aioseop_get_pages')) {
	function aioseop_get_pages($pages) {
		global $aioseop_get_pages_start;
		if (!$aioseop_get_pages_start) return $pages;
		foreach ($pages as $k => $v) {
			$postID = $v->ID;
			$menulabel = stripslashes(get_post_meta($postID, '_aioseop_menulabel', true));
			if ($menulabel)    $pages[$k]->post_title = $menulabel;
		}
		$aioseop_get_pages_start = 0;
		return $pages;
	}
}

// The following two functions are GPLed from Sarah G's Page Menu Editor, http://wordpress.org/extend/plugins/page-menu-editor/.
if (!function_exists('aioseop_list_pages')) {
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

if (!function_exists('aioseop_filter_callback')) {
	function aioseop_filter_callback( $matches ) {
		global $wpdb;
		if ( $matches[1] && !empty( $matches[1] ) ) $postID = $matches[1];
		if ( empty( $postID ) ) $postID = get_option( "page_on_front" );
		$title_attrib = stripslashes( get_post_meta($postID, '_aioseop_titleatr', true ) );
		if ( empty( $title_attrib ) && !empty( $matches[4] ) ) $title_attrib = $matches[4];
		if ( !empty( $title_attrib ) ) $title_attrib = ' title="' . strip_tags( $title_attrib ) . '"';
		return '<li class="page_item page-item-'.$postID.$matches[2].'"><a href="'.$matches[3].'"'.$title_attrib.'>';
	}
}

if ( !function_exists('aioseop_add_contactmethods' ) ) {
	function aioseop_add_contactmethods( $contactmethods ) {
		$contactmethods['googleplus'] = 'Google+';
		return $contactmethods;
	}
}

if (!function_exists('aioseop_meta_box_add')) {
	function aioseop_meta_box_add() {
			$mrt_aioseop_pts=get_post_types('','names'); 
			$aioseop_options = get_option('aioseop_options');
			$aioseop_mrt_cpt = $aioseop_options['aiosp_enablecpost'];
			foreach ($mrt_aioseop_pts as $mrt_aioseop_pt) {
				if($mrt_aioseop_pt == 'post' || $mrt_aioseop_pt == 'page' || $aioseop_mrt_cpt){
					add_meta_box('aiosp',__('All in One SEO Pack', 'all_in_one_seo_pack'),'aiosp_meta',$mrt_aioseop_pt);
				}
			}
	}
}

if (!function_exists('aiosp_meta')) {
	function aiosp_meta() {
		global $post;
		$post_id = $post;
		if (is_object($post_id)) $post_id = $post_id->ID;
	 	$keywords = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_keywords', true)));
	    	$title = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_title', true)));
		$description = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_description', true)));
	   	$aiosp_meta = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aiosp_meta', true)));
	    	$aiosp_disable = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_disable', true)));
	    	$aiosp_disable_analytics = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_disable_analytics', true)));
	    	$aiosp_titleatr = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_titleatr', true)));
	    	$aiosp_menulabel = htmlspecialchars(stripcslashes(get_post_meta($post_id, '_aioseop_menulabel', true)));	
		?>
			<SCRIPT LANGUAGE="JavaScript">
			<!-- Begin
			function countChars(field,cntfield) {
			cntfield.value = field.value.length;
			}
			//  End -->
			</script>
			<input value="aiosp_edit" type="hidden" name="aiosp_edit" />

			<a target="__blank" href="http://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/"><?php _e('Upgrade to All in One SEO Pack Pro Version', 'all_in_one_seo_pack') ?></a>
			<table style="margin-bottom:40px">
			<tr>
			<th style="text-align:left;" colspan="2">
			</th>
			</tr>

			<tr>
			<th scope="row" style="text-align:right;"><?php _e('Title:', 'all_in_one_seo_pack') ?></th>
			<td><input value="<?php echo $title ?>" type="text" name="aiosp_title" size="62" onKeyDown="countChars(document.post.aiosp_title,document.post.lengthT)" onKeyUp="countChars(document.post.aiosp_title,document.post.lengthT)"/><br />
				<input readonly type="text" name="lengthT" size="3" maxlength="3" style="text-align:center;" value="<?php echo strlen($title);?>" />
				<?php _e(' characters. Most search engines use a maximum of 60 chars for the title.', 'all_in_one_seo_pack') ?>
				</td>
			</tr>

			<tr>
			<th scope="row" style="text-align:right;"><?php _e('Description:', 'all_in_one_seo_pack') ?></th>
			<td><textarea name="aiosp_description" rows="3" cols="60"
			onKeyDown="countChars(document.post.aiosp_description,document.post.length1)"
			onKeyUp="countChars(document.post.aiosp_description,document.post.length1)"><?php echo $description ?></textarea><br />
			<input readonly type="text" name="length1" size="3" maxlength="3" value="<?php echo strlen($description);?>" />
			<?php _e(' characters. Most search engines use a maximum of 160 chars for the description.', 'all_in_one_seo_pack') ?>
			</td>
			</tr>

			<tr>
			<th scope="row" style="text-align:right;"><?php _e('Keywords (comma separated):', 'all_in_one_seo_pack') ?></th>
			<td><input value="<?php echo $keywords ?>" type="text" name="aiosp_keywords" size="62"/></td>
			</tr>
			<input type="hidden" name="nonce-aioseop-edit" value="<?php echo wp_create_nonce('edit-aioseop-nonce') ?>" />
	<?php if($post->post_type=='page'){ ?>
			<tr>
			<th scope="row" style="text-align:right;"><?php _e('Title Attribute:', 'all_in_one_seo_pack') ?></th>
			<td><input value="<?php echo $aiosp_titleatr ?>" type="text" name="aiosp_titleatr" size="62"/></td>
			</tr>

			<tr>
			<th scope="row" style="text-align:right;"><?php _e('Menu Label:', 'all_in_one_seo_pack') ?></th>
			<td><input value="<?php echo $aiosp_menulabel ?>" type="text" name="aiosp_menulabel" size="62"/></td>
			</tr>
	<?php } ?>
			<tr>
			<th scope="row" style="text-align:right; vertical-align:top;">
			<?php _e('Disable on this page/post:', 'all_in_one_seo_pack')?>
			</th>
			<td>
			<input type="checkbox" name="aiosp_disable" <?php if ($aiosp_disable) echo "checked=\"1\""; ?>/>
			</td>
			</tr>

<?php if ( $aiosp_disable ) { ?>
                                                <tr>
                                                        <th scope="row" style="text-align:right; vertical-align:top;">
                                                                <?php _e('Disable Google Analytics:', 'all_in_one_seo_pack')?>
                                                        </th>
                                                        <td>
                                                                <input type="checkbox" name="aiosp_disable_analytics" <?php if ($aiosp_disable_analytics) echo "checked=\"1\""; ?>/>
                                                        </td>
                                                </tr>
<?php } ?>

			</table>
		<?php
	}
}
