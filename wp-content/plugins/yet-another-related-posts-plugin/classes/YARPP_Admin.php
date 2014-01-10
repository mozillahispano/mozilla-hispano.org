<?php

class YARPP_Admin {
	public $core;
	public $hook;
	
	function __construct(&$core) {
		$this->core = &$core;

        /* YARPP Pro Default Options */
        add_option(
            'yarpp_pro',
            array(
                'active' => '0',
                'aid'    => null,
                'st'     => null,
                'v'      => null,
                'optin'  => false
            )
        );
		
		/* If action = flush and the nonce is correct, reset the cache */
		if (isset($_GET['action']) && $_GET['action'] === 'flush' && check_ajax_referer('yarpp_cache_flush', false, false) !== false) {
			$this->core->cache->flush();
			wp_redirect(admin_url('/options-general.php?page=yarpp'));
			die();
		}

		/* If action = copy_templates and the nonce is correct, copy templates */
		if (isset($_GET['action']) && $_GET['action'] === 'copy_templates' && check_ajax_referer('yarpp_copy_templates', false, false) !== false) {
			$this->copy_templates();
			wp_redirect(admin_url('/options-general.php?page=yarpp'));
			die();
		}


		add_action('admin_init', array($this, 'ajax_register'));
		add_action('admin_menu', array($this, 'ui_register'));

		add_filter('current_screen', array($this, 'settings_screen'));
		add_filter('screen_settings', array($this, 'render_screen_settings'), 10, 2);
		add_filter('default_hidden_meta_boxes', array($this, 'default_hidden_meta_boxes'), 10, 2);
	}

    /**
     * @since 4.0.3 Moved method to Core.
     */
	public function get_templates() {
		return $this->core->get_templates();
	}

    /**
     * Register AJAX services
     */
	function ajax_register() {
		if (defined('DOING_AJAX') && DOING_AJAX) {
			add_action('wp_ajax_yarpp_display_exclude_terms',   array($this, 'ajax_display_exclude_terms'));
			add_action('wp_ajax_yarpp_display_demo',            array($this, 'ajax_display_demo'));
			add_action('wp_ajax_yarpp_display',                 array($this, 'ajax_display'));
			add_action('wp_ajax_yarpp_optin_data',              array($this, 'ajax_optin_data'));
            add_action('wp_ajax_yarpp_optin_enable',            array($this, 'ajax_optin_enable'));
            add_action('wp_ajax_yarpp_optin_disable',           array($this, 'ajax_optin_disable'));
			add_action('wp_ajax_yarpp_set_display_code',        array($this, 'ajax_set_display_code'));
		}
	}
	
	function ui_register() {
		global $wp_version;

		if (get_option('yarpp_activated')) {

			delete_option('yarpp_activated');
 			delete_option('yarpp_upgraded');

            /* Optin/Pro message */
            add_action('admin_notices', array($this, 'install_notice'));

		} elseif (get_option('yarpp_upgraded') && current_user_can('manage_options') && $this->core->get_option('optin')) {
			add_action('admin_notices', array($this, 'upgrade_notice'));
		}
		
		if ($this->core->get_option('optin')) delete_option('yarpp_upgraded');
		
		/*
		 * Setup Admin
		 */
        $titleName = 'YARPP';
		$this->hook = add_options_page($titleName, $titleName, 'manage_options', 'yarpp', array($this, 'options_page'));
		
		/**
         * @since 3.0.12  Add settings link to the plugins page.
         */
		add_filter('plugin_action_links', array($this, 'settings_link'), 10, 2);

		$metabox_post_types = $this->core->get_option('auto_display_post_types');
		if (!in_array('post', $metabox_post_types)) $metabox_post_types[] = 'post';

		/**
         * @since 3.0  Add meta box.
         */
        if(!$this->core->yarppPro['active']){
            foreach ($metabox_post_types as $post_type) {
                $optionsUrl    = esc_url(admin_url('options-general.php?page=yarpp'));
                $title  =
                    __('Related Posts' , 'yarpp').
                    '<span class="postbox-title-action">'.
                        '<a href="'.$optionsUrl. '" class="edit-box open-box">'.__('Configure').'</a>'.
                    '</span>';
                add_meta_box('yarpp_relatedposts',$title, array($this, 'metabox'), $post_type, 'normal');
            }
        }
		
		/**
         * @since 3.3: properly enqueue scripts for admin.
         */
		add_action('admin_enqueue_scripts', array($this, 'enqueue'));
	}

	/**
     * @since 3.5.4 Only load metabox code if we're going to be on the settings page.
     */
	function settings_screen($current_screen) {
		if ($current_screen->id !== 'settings_page_yarpp') return $current_screen;

		/**
         * @since 3.3: Load options page sections as meta-boxes.
         */
		include_once(YARPP_DIR.'/classes/YARPP_Meta_Boxes.php');

		/**
         * @since 3.5.5 Check that add_help_tab method callable (WP >= 3.3).
         */
		if (is_callable(array($current_screen, 'add_help_tab'))) {
			$current_screen->add_help_tab(array(
				'id'        => 'faq',
				'title'     => __('Frequently Asked Questions', 'yarpp'),
				'callback'  => array(&$this, 'help_faq')
			));

			$current_screen->add_help_tab(array(
				'id'        => 'dev',
				'title'     => __('Developing with YARPP', 'yarpp'),
				'callback'  => array(&$this, 'help_dev')
			));

			$current_screen->add_help_tab(array(
				'id'        => 'optin',
				'title'     => __('Optional Data Collection', 'yarpp'),
				'callback'  => array(&$this, 'help_optin')
			));
		}
		
		return $current_screen;
	}
	
	private $readme = null;
	
	public function help_faq() {
		if (is_null($this->readme)) $this->readme = file_get_contents(YARPP_DIR.'/readme.txt');

		if (preg_match('!== Frequently Asked Questions ==(.*?)^==!sm', $this->readme, $matches)) {
			echo $this->markdown($matches[1]);
        } else {
			echo(
                '<a href="https://wordpress.org/extend/plugins/yet-another-related-posts-plugin/faq/">'.
                    __('Frequently Asked Questions', 'yarpp').
                '</a>'
            );
        }
	}
	
	public function help_dev() {
		if (is_null($this->readme)) $this->readme = file_get_contents(YARPP_DIR.'/readme.txt');

		if (preg_match('!== Developing with YARPP ==(.*?)^==!sm', $this->readme, $matches)) {
			echo $this->markdown( $matches[1] );
        } else {
			echo(
                '<a href="https://wordpress.org/extend/plugins/yet-another-related-posts-plugin/faq/" target="_blank">'.
                    __('Developing with YARPP', 'yarpp').
                '</a>'
            );
        }
	}

	public function help_optin() {
		echo(
            '<p>'.
                __("With your permission, YARPP will send information about YARPP's settings, usage, and environment
                    back to a central server at ", 'yarpp').'<code>yarpp.org</code>'.'.&nbsp;'.
            '</p>'.
            '<p>'.
                'We would really appreciate your input to help us continue to improve the product. We are primarily looking '.
                'for country, domain, and date installed information.'.
            '</p>'.
            '<p>'.
		        __("This information will be used to improve YARPP in the future and help decide future development
		            decisions for YARPP.",
                    'yarpp'
                ).' '.
            '</p>'.
            '<p>'.
		        '<strong>'.
                __("Contributing this data will help make YARPP better for you and for other YARPP users.",
                    'yarpp' ).'</strong>'.
            '</p>'
        );
		
		echo(
            '<p>'.
                __("The following information is sent back to YARPP:", 'yarpp').
            '</p>'.
		    '<div id="optin_data_frame"></div>'.
            '<p>'.
                __("In addition, YARPP also loads an invisible pixel image with your YARPP results to know how often YARPP is being used.", 'yarpp').
            '</p>'
        );
	}
	
	function the_optin_button($action, $echo = false) {

        $status = ($this->core->yarppPro['active']) ? 'disabled' : null;

        if ($action === 'disable'){
            $out =
            '<a id="yarpp-optin-button'.$status.'" class="button" '.$status.'>'.
                'No, Thanks. Please <strong>'.$action.'</strong> sending usage data'.
            '</a>';
        } else {
            $out =
            '<a id="yarpp-optin-button'.$status.'" class="button" '.$status.'>'.
                'Yes, <strong>'.$action.'</strong> sending usage data back to help improve YARPP'.
            '</a>';
        }

        if ($echo){
            echo $out;
            return null;
        } else {
            return $out;
        }
	}

	function the_donothing_button($msg, $echo = false) {

        $out ='<a href="options-general.php?page=yarpp" class="button">'.$msg.'</a>';

        if ($echo){
            echo $out;
            return null;
        } else {
            return $out;
        }
	}

	function optin_button_script($optinAction, $echo=false) {
		wp_nonce_field('yarpp_optin_'.$optinAction, 'yarpp_optin-nonce', false);

        ob_start();
           include(YARPP_DIR.'/includes/optin_notice.js.php');
           $out = ob_get_contents();
        ob_end_clean();

        if($echo){
            echo $out;
            return null;
        } else {
            return $out;
        }

	}

    function upgrade_notice() {
        $proAction   = ($this->core->yarppPro['active'])  ? 'disable' : 'enable';
        $optinAction = ($this->core->get_option('optin')) ? 'disable' : 'enable';
        $this->optin_notice('upgrade', $proAction, $optinAction);
    }

    public function install_notice(){
        $proAction   = ($this->core->yarppPro['active'])  ? 'disable' : 'enable';
        $optinAction = ($this->core->get_option('optin')) ? 'disable' : 'enable';
        $this->optin_notice('install', $proAction, $optinAction);
    }

	function optin_notice($type=false, $proAction, $optinAction) {
		$screen = get_current_screen();
		if(is_null($screen) || $screen->id == 'settings_page_yarpp') return;

		switch($type) {
			case 'upgrade':
				delete_option('yarpp_upgraded');
				break;
			case 'install':
			default:
				$user = get_current_user_id();
				update_user_option($user, 'yarpp_saw_optin', true);
		}

		$out = '<div class="updated fade"><p>';

		if($type === 'upgrade'){
			$out .= '<strong>'.sprintf(__('%1$s updated successfully.'), 'Yet Another Related Posts Plugin').'</strong>';
        }

		if ($type === 'install'){
            $tmp  = __('Thank you for installing <span>Yet Another Related Posts Plugin</span>!', 'yarpp');
			$out .= '<strong>'.str_replace('<span>','<span style="font-style:italic; font-weight: inherit;">', $tmp).'</strong>';
        }

        if($this->core->yarppPro['active']){

            $out .=
                '<p>'.
                    'You are currently using <em>YARPP Pro</em>!&nbsp;&nbsp;No need to enable sending usage data.<br/><br/>'.
                    '<a href="options-general.php?page=yarpp" class="button">Take me to the settings page</a>'.
                '</p>';

        } else {

            $out .=
                '<p>'.
                'We would really appreciate your input to help us continue to <a href="http://yarpp.com" target="_blank">improve the product</a>. We are primarily looking '.
                'for country, domain, and date installed information. Please help us make YARPP better by providing this information and by filling '.
                'out our quick, 5 question survey: <a href="http://www.surveymonkey.com/s/Z278L88" target="_blank">http://www.surveymonkey.com/s/Z278L88</a>'.
                '</p>';

            $out .= '</p><p>';
            if($optinAction !== 'disable'){
                $out .= $this->the_donothing_button('No, thanks').'&nbsp;&nbsp;';
            } else {
                $out .= $this->the_donothing_button('Yes, keep sending usage data').'&nbsp;&nbsp;';
            }
            $out .= $this->the_optin_button($optinAction);
            $out .= $this->optin_button_script($optinAction);

        }

        echo $out.'</div>';
	}
	
	// faux-markdown, required for the help text rendering
	protected function markdown( $text ) {
		$replacements = array(
			// strip each line
			'!\s*[\r\n] *!' => "\n",
			
			// headers
			'!^=(.*?)=\s*$!m' => '<h3>\1</h3>',
			
			// bullets
			'!^(\* .*([\r\n]\* .*)*)$!m' => "<ul>\n\\1\n</ul>",
			'!^\* (.*?)$!m' => '<li>\1</li>',
			'!^(\d+\. .*([\r\n]\d+\. .*)*)$!m' => "<ol>\n\\1\n</ol>",
			'!^\d+\. (.*?)$!m' => '<li>\1</li>',
			
			// code block
			'!^(\t.*([\r\n]\t.*)*)$!m' => "<pre>\n\\1\n</pre>",
			
			// wrap p
			'!^([^<\t].*[^>])$!m' => '<p>\1</p>',
			// bold
			'!\*([^*]*?)\*!' => '<strong>\1</strong>',
			// code
			'!`([^`]*?)`!' => '<code>\1</code>',
			// links
			'!\[([^]]+)\]\(([^)]+)\)!' => '<a href="\2" target="_new">\1</a>',
		);
		$text = preg_replace(array_keys($replacements), array_values($replacements), $text);
		
		return $text;
	}
	
	function render_screen_settings( $output, $current_screen ) {
		if ( $current_screen->id != 'settings_page_yarpp' )
			return $output;

		$output .= "<div id='yarpp_extra_screen_settings'><label for='yarpp_display_code'><input type='checkbox' name='yarpp_display_code' id='yarpp_display_code'";
		$output .= checked($this->core->get_option('display_code'), true, false);
		$output .= " />";
		$output .= __('Show example code output', 'yarpp');
		$output .= '</label></div>';

		return $output;
	}
	
	// since 3.3
	public function enqueue() {
		$version = defined('WP_DEBUG') && WP_DEBUG ? time() : YARPP_VERSION;
		$screen = get_current_screen();
		if (!is_null($screen) && $screen->id === 'settings_page_yarpp') {

            wp_enqueue_style('yarpp_switch_options',  plugins_url('style/options_switch.css', dirname(__FILE__)), array(), $version );
            wp_enqueue_script('yarpp_switch_options', plugins_url('js/options_switch.js', dirname(__FILE__)), array('jquery'), $version );

            wp_enqueue_style('wp-pointer');
            wp_enqueue_style('yarpp_options', plugins_url('style/options_basic.css', dirname(__FILE__)), array(), $version );

            wp_enqueue_script('postbox');
            wp_enqueue_script('wp-pointer');
            wp_enqueue_script('yarpp_options', plugins_url('js/options_basic.js', dirname(__FILE__)), array('jquery'), $version );

		}

		$metabox_post_types = $this->core->get_option('auto_display_post_types');
		if (!is_null($screen) && ($screen->id == 'post' || in_array( $screen->id, $metabox_post_types))) {
			wp_enqueue_script('yarpp_metabox', plugins_url('js/metabox.js', dirname(__FILE__)), array('jquery'), $version );
		}
	}
	
	function settings_link($links, $file) {
		$this_plugin = dirname(plugin_basename(dirname(__FILE__))).'/yarpp.php';
		if($file == $this_plugin) {
			$links[] = '<a href="options-general.php?page=yarpp">'.__('Settings').'</a>';
		}
		return $links;
	}
	
	function options_page() {

        if($this->core->yarppPro['active']){
            include_once(YARPP_DIR.'/includes/yarpp_pro_options.php');
        } else {
            include_once(YARPP_DIR . '/includes/yarpp_options.php');
        }
	}

	// @since 3.4: don't actually compute results here, but use ajax instead		
	function metabox() {
		?>
		<style>
		#yarpp_relatedposts h3 .postbox-title-action {
			right: 30px;
			top: 5px;
			position: absolute;
			padding: 0;
		}
		#yarpp_relatedposts:hover .edit-box {
			display: inline;
		}
		</style>
		<?php
		if ( !get_the_ID() ) {
			echo "<div><p>".__("Related entries may be displayed once you save your entry",'yarpp').".</p></div>";
		} else {
			wp_nonce_field( 'yarpp_display', 'yarpp_display-nonce', false );
			echo '<div id="yarpp-related-posts"><img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" alt="" /></div>';
		}
	}
	
	// @since 3.3: default metaboxes to show:
	function default_hidden_meta_boxes($hidden, $screen) {
		if ($screen->id === 'settings_page_yarpp') {
			$hidden = $this->core->default_hidden_metaboxes;
        }
		return $hidden;
	}
	
	// @since 4: UI to copy templates
	function can_copy_templates() {
		$theme_dir = get_stylesheet_directory();
		// If we can't write to the theme, return false
		if ( !is_dir($theme_dir) || !is_writable($theme_dir) )
			return false;
		
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		WP_Filesystem( false, get_stylesheet_directory() );
		global $wp_filesystem;			
		// direct method is the only method that I've tested so far
		return $wp_filesystem->method == 'direct';
	}
	
	function copy_templates() {
		$templates_dir = trailingslashit(trailingslashit(YARPP_DIR) . 'yarpp-templates');
		
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		WP_Filesystem( false, get_stylesheet_directory() );
		global $wp_filesystem;
		if ( $wp_filesystem->method != 'direct' )
			return false;
		
		return copy_dir( $templates_dir, get_stylesheet_directory(), array('.svn') );
	}
	
	/*
	 * AJAX SERVICES
	 * TODO: Move to its own class.
	 */

	function ajax_display_exclude_terms() {
		check_ajax_referer('yarpp_display_exclude_terms');
		
		if (!isset($_REQUEST['taxonomy'])) return;
		
		$taxonomy = (string) $_REQUEST['taxonomy'];
		
		header("HTTP/1.1 200");
		header("Content-Type: text/html; charset=UTF-8");
		
		$exclude_tt_ids = wp_parse_id_list($this->core->get_option('exclude'));
		$exclude_term_ids = $this->get_term_ids_from_tt_ids( $taxonomy, $exclude_tt_ids );
//		if ('category' == $taxonomy) $exclude .= ','.get_option('default_category');

		$terms = get_terms($taxonomy, array(
			'exclude' => $exclude_term_ids,
			'hide_empty' => false,
			'hierarchical' => false,
			'number' => 100,
			'offset' => $_REQUEST['offset']
		));
		
		if ( !count($terms) ) {
			echo ':('; // no more :(
			exit;
		}
		
		foreach ($terms as $term) {
			echo "<span><input type='checkbox' name='exclude[{$term->term_taxonomy_id}]' id='exclude_{$term->term_taxonomy_id}' value='true' /> <label for='exclude_{$term->term_taxonomy_id}'>" . esc_html($term->name) . "</label></span> ";
		}
		exit;
	}
	
	function get_term_ids_from_tt_ids( $taxonomy, $tt_ids ) {
		global $wpdb;
		$tt_ids = wp_parse_id_list($tt_ids);
		if ( empty($tt_ids) )
			return array();
		return $wpdb->get_col("select term_id from $wpdb->term_taxonomy where taxonomy = '{$taxonomy}' and term_taxonomy_id in (" . join(',', $tt_ids) . ")");
	}
	
	function ajax_display() {
		check_ajax_referer('yarpp_display');

		if (!isset($_REQUEST['ID'])) return;

		$args = array(
			'post_type' => array('post'),
			'domain' => isset($_REQUEST['domain']) ? $_REQUEST['domain'] : 'website'
		);

		if ($this->core->get_option('cross_relate')) $args['post_type'] = $this->core->get_post_types();
			
		$return = $this->core->display_related(absint($_REQUEST['ID']), $args, false);

        header("HTTP/1.1 200");
        header("Content-Type: text/html; charset=UTF-8");
		echo $return;

		die();
	}

	function ajax_display_demo() {
		check_ajax_referer('yarpp_display_demo');

		header("HTTP/1.1 200");
		header("Content-Type: text/html; charset=UTF-8");
	
		$args = array(
			'post_type' => array('post'),
			'domain'    => (isset($_REQUEST['domain'])) ? $_REQUEST['domain'] : 'website'
		);
			
		$return = $this->core->display_demo_related($args, false);
		echo preg_replace("/[\n\r]/",'',nl2br(htmlspecialchars($return)));
		exit;
	}

    /**
     * Display optin data in a human readable format on the help tab.
     */
	function ajax_optin_data() {
		check_ajax_referer('yarpp_optin_data');

		header("HTTP/1.1 200");
		header("Content-Type: text/html; charset=UTF-8");

		$data = $this->core->optin_data();
		$this->core->pretty_echo($data);
		die();
	}

    public function ajax_optin_disable() {
		check_ajax_referer('yarpp_optin_disable');

        $this->core->set_option('optin', false);

        header("HTTP/1.1 200");
        header("Content-Type: text; charset=UTF-8");
		echo 'ok';

		die();
    }

	public function ajax_optin_enable() {
		check_ajax_referer('yarpp_optin_enable');

        $this->core->set_option('optin', true);
		$this->core->optin_ping();

        header("HTTP/1.1 200");
        header("Content-Type: text; charset=UTF-8");
		echo 'ok';

		die();
	}

	function ajax_set_display_code() {
		check_ajax_referer( 'yarpp_set_display_code' );

		header("HTTP/1.1 200");
		header("Content-Type: text; charset=UTF-8");
		
		$data = $this->core->set_option( 'display_code', isset($_REQUEST['checked']) );
		echo 'ok';
		die();
	}
}
