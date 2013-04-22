<?php

/**
 * W3 Total Cache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_TotalCache
 */
class W3_Plugin_TotalCache extends W3_Plugin {

    /**
     * Runs plugin
     *
     * @return void
     */
    function run() {
        register_activation_hook(W3TC_FILE, array(
            &$this,
            'activate'
        ));
        
        register_deactivation_hook(W3TC_FILE, array(
            &$this,
            'deactivate'
        ));

        add_action('init', array(
            &$this,
            'init'
        ));

        add_action('admin_bar_menu', array(
            &$this,
            'admin_bar_menu'
        ), 150);

        if (isset($_REQUEST['w3tc_theme']) && isset($_SERVER['HTTP_USER_AGENT']) &&
                $_SERVER['HTTP_USER_AGENT'] == W3TC_POWERED_BY) {
            add_filter('template', array(
                &$this,
                'template_preview'
            ));

            add_filter('stylesheet', array(
                &$this,
                'stylesheet_preview'
            ));
        } elseif ($this->_config->get_boolean('mobile.enabled') || $this->_config->get_boolean('referrer.enabled')) {
            add_filter('template', array(
                &$this,
                'template'
            ));

            add_filter('stylesheet', array(
                &$this,
                'stylesheet'
            ));
        }
        
        /**
         * Create cookies to flag if a pgcache role was loggedin
         */
        if (!$this->_config->get_boolean('pgcache.reject.logged') && $this->_config->get_array('pgcache.reject.logged_roles')) {
            add_action( 'set_logged_in_cookie', array(
                &$this,
                'check_login_action'
            ), 0, 5);
            add_action( 'clear_auth_cookie', array(
                &$this,
                'check_login_action'
            ), 0, 5);
        }

        /**
         * CloudFlare support
         */
        if ($this->_config->get_boolean('cloudflare.enabled')) {
            w3_require_once(W3TC_LIB_W3_DIR . '/CloudFlare.php');
            @$w3_cloudflare = new W3_CloudFlare();
            $w3_cloudflare->fix_remote_addr();
        }

        if ($this->_config->get_string('common.support') == 'footer') {
            add_action('wp_footer', array(
                &$this,
                'footer'
            ));
        }

        if ($this->can_ob()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }

        $plugins = w3_instance('W3_Plugins');
        $plugins->run();
    }

    /**
     * Activate plugin action
     *
     * @param $network_wide
     * @return void
     */
    function activate($network_wide) {
        $activation_worker = w3_instance('W3_Plugin_TotalCacheActivation');
        $activation_worker->activate($network_wide);
    }

    /**
     * Deactivate plugin action
     *
     * @return void
     */
    function deactivate() {
        $activation_worker = w3_instance('W3_Plugin_TotalCacheActivation');
        $activation_worker->deactivate();
    }


    /**
     * Init action
     *
     * @return void
     */
    function init() {
        if (is_multisite()) {
            global $w3_current_blog_id, $current_blog;
            if ($w3_current_blog_id != $current_blog->blog_id && !isset($GLOBALS['w3tc_blogmap_register_new_item'])) {
				$url = w3_get_host() . $_SERVER['REQUEST_URI'];
				$pos = strpos($url, '?');
				if ($pos !== false)
					$url = substr($url, 0, $pos);
				$GLOBALS['w3tc_blogmap_register_new_item'] = $url;
			}
		}

        if (isset($GLOBALS['w3tc_blogmap_register_new_item'])) {
            $do_redirect = false;
            // true value is a sign to just generate config cache
            if ($GLOBALS['w3tc_blogmap_register_new_item'] !== true) {
                w3_require_once(W3TC_INC_DIR . '/functions/multisite.php');
                $do_redirect = w3_blogmap_register_new_item(
                    $GLOBALS['w3tc_blogmap_register_new_item'], $this->_config);

                // reset cache of blog_id
                global $w3_current_blog_id;
                $w3_current_blog_id = null;

                // change config to actual blog, it was master before
                $this->_config = new W3_Config();
            }

            $do_redirect |= $this->_config->fill_missing_cache_options_and_save();

            // need to repeat request processing, since we was not able to realize
            // blog_id before so we are running with master config now.
            // redirect to the same url causes "redirect loop" error in browser,
            // so need to redirect to something a bit different
            if ($do_redirect) {
                if (strpos($_SERVER['REQUEST_URI'], '?') === false)
                    w3_redirect($_SERVER['REQUEST_URI'] . '?repeat=w3tc');
                else {
                    if (strpos($_SERVER['REQUEST_URI'], 'repeat=w3tc') === false)
                        w3_redirect($_SERVER['REQUEST_URI'] . '&repeat=w3tc');
                }
            }
        }

        /**
         * Check request and handle w3tc_request_data requests
         */
        $pos = strpos($_SERVER['REQUEST_URI'], '/w3tc_request_data/');

        if ($pos !== false) {
            $hash = substr($_SERVER['REQUEST_URI'], $pos + 19, 32);

            if (strlen($hash) == 32) {
                $request_data = (array) get_option('w3tc_request_data');

                if (isset($request_data[$hash])) {
                    echo '<pre>';
                    foreach ($request_data[$hash] as $key => $value) {
                        printf("%s: %s\n", $key, $value);
                    }
                    echo '</pre>';

                    unset($request_data[$hash]);
                    update_option('w3tc_request_data', $request_data);
                } else {
                    echo 'Requested hash expired or invalid';
                }

                exit();
            }
        }

        /**
         * Check for rewrite test request
         */
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $rewrite_test = W3_Request::get_boolean('w3tc_rewrite_test');

        if ($rewrite_test) {
            echo 'OK';
            exit();
        }
        $admin_bar = false;
        if (function_exists('is_admin_bar_showing'))
            $admin_bar = is_admin_bar_showing();

        if (current_user_can('manage_options') && $admin_bar) {
            add_action('wp_print_scripts', array($this, 'popup_script'));
        }
    }

    /**
     * Admin bar menu
     *
     * @return void
     */
    function admin_bar_menu() {
        global $wp_admin_bar;

        if (current_user_can('manage_options')) {
            /**
             * @var $modules W3_ModuleStatus
             */
            $modules = w3_instance('W3_ModuleStatus');

            $can_empty_memcache = $modules->can_empty_memcache();

            $can_empty_opcode = $modules->can_empty_opcode();

            $can_empty_file = $modules->can_empty_file();

            $can_empty_varnish = $modules->can_empty_varnish();

            $browsercache_update_media_qs = ($this->_config->get_boolean('browsercache.cssjs.replace') || $this->_config->get_boolean('browsercache.other.replace'));

            //$cdn_enabled = $modules->is_enabled('cdn');
            $cdn_engine = $modules->get_module_engine('cdn');
            $cdn_mirror = w3_is_cdn_mirror($cdn_engine);

            $menu_items = array(
                array(
                    'id' => 'w3tc',
                    'title' => 'Performance',
                    'href' => admin_url('admin.php?page=w3tc_dashboard')
                ));

            if ($modules->is_enabled('pgcache') && w3_detect_post_id() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
                $menu_items[] = array(
                    'id' => 'w3tc-pgcache-purge-post',
                    'parent' => 'w3tc',
                    'title' => 'Purge From Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_pgcache_purge_post&amp;post_id=' . $this->_detect_post_id()), 'w3tc')
                );
            }

            if ($can_empty_file && ($can_empty_opcode || $can_empty_memcache)) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-file',
                    'parent' => 'w3tc-empty-caches',
                    'title' => 'Empty Disc Cache(s)',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_file'), 'w3tc')
                );
            }

            if ($can_empty_opcode && ($can_empty_file || $can_empty_memcache)) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-opcode',
                    'parent' => 'w3tc-empty-caches',
                    'title' => 'Empty Opcode Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_opcode'), 'w3tc')
                );
            }

            if ($can_empty_memcache && ($can_empty_file || $can_empty_opcode)) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-memcached',
                    'parent' => 'w3tc-empty-caches',
                    'title' => 'Empty Memcached Cache(s)',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_memcached'), 'w3tc')
                );
            }

            if ($modules->is_enabled('browsercache') && $browsercache_update_media_qs) {
                $menu_items[] = array(
                    'id' => 'w3tc-update-media-qs',
                    'parent' => 'w3tc',
                    'title' => 'Update Media Query String',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_browser_cache'), 'w3tc')
                );
            }

            if ($modules->plugin_is_enabled()) {
                $menu_items[] = array(
                    'id' => 'w3tc-empty-caches',
                    'parent' => 'w3tc',
                    'title' => 'Empty All Caches',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_all'), 'w3tc')
                );

                $menu_items[] = array(
                    'id' => 'w3tc-modules',
                    'parent' => 'w3tc',
                    'title' => 'Empty Modules'
                );
            }

            if ($modules->is_enabled('pgcache')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-pgcache',
                    'parent' => 'w3tc-modules',
                    'title' => 'Empty Page Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_pgcache'), 'w3tc')
                );
            }

            if ($modules->is_enabled('minify')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-minify',
                    'parent' => 'w3tc-modules',
                    'title' => 'Empty Minify Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_minify'), 'w3tc')
                );
            }

            if ($modules->is_enabled('dbcache')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-dbcache',
                    'parent' => 'w3tc-modules',
                    'title' => 'Empty Database Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_dbcache'), 'w3tc')
                );
            }

            if ($modules->is_enabled('objectcache')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-objectcache',
                    'parent' => 'w3tc-modules',
                    'title' => 'Empty Object Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_objectcache'), 'w3tc')
                );
            }

            if (w3_is_pro() || w3_is_enterprise()) {
                if ($modules->is_enabled('fragmentcache')) {
                    $menu_items[] = array(
                        'id' => 'w3tc-flush-fragmentcache',
                        'parent' => 'w3tc-modules',
                        'title' => 'Empty Fragment Cache',
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_fragmentcache'), 'w3tc')
                    );
                }
            }

            if ($modules->is_enabled('varnish')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-varnish',
                    'parent' => 'w3tc-modules',
                    'title' => 'Purge Varnish Cache',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_varnish'), 'w3tc')
                );
            }

            if ($modules->is_enabled('cdn')) {
                if (w3_can_cdn_purge($cdn_engine)) {
                    $menu_items[] = array(
                        'id' => 'w3tc-cdn-purge',
                        'parent' => 'w3tc',
                        'title' => 'Purge CDN',
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_cdn_purge'), 'w3tc'),
                        'meta' => array('onclick' => "w3tc_popupadmin_bar(this.href); return false")
                    );
                }

                if (w3_cdn_can_purge_all($cdn_engine)) {
                    $menu_items[] = array(
                        'id' => 'w3tc-cdn-purge-full',
                        'parent' => 'w3tc',
                        'title' => 'Purge CDN Completely',
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_flush_cdn'), 'w3tc')
                    );
                }
                if (!$cdn_mirror) {
                    $menu_items[] = array(
                        'id' => 'w3tc-cdn-queue',
                        'parent' => 'w3tc',
                        'title' => 'Unsuccessfull file transfers',
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue'), 'w3tc'),
                        'meta' => array('onclick' => "w3tc_popupadmin_bar(this.href); return false")
                    );
                }
            }

            $menu_items = array_merge($menu_items, array(
                array(
                    'id' => 'w3tc-faq',
                    'parent' => 'w3tc',
                    'title' => 'FAQ',
                    'href' => admin_url('admin.php?page=w3tc_faq')
                ),
                array(
                    'id' => 'w3tc-support',
                    'parent' => 'w3tc',
                    'title' => '<span style="color: red; background: none;">Support</span>',
                    'href' => admin_url('admin.php?page=w3tc_support')
                )
            ));

            if ($modules->is_enabled('cloudflare')) {
                $menu_items = array_merge($menu_items, array(
                    array(
                        'id' => 'cloudflare',
                        'title' => 'CloudFlare',
                        'href' => 'https://www.cloudflare.com'
                    ),
                    array(
                        'id' => 'cloudflare-my-websites',
                        'parent' => 'cloudflare',
                        'title' => 'My Websites',
                        'href' => 'https://www.cloudflare.com/my-websites.html'
                    ),
                    array(
                        'id' => 'cloudflare-analytics',
                        'parent' => 'cloudflare',
                        'title' => 'Analytics',
                        'href' => 'https://www.cloudflare.com/analytics.html'
                    ),
                    array(
                        'id' => 'cloudflare-account',
                        'parent' => 'cloudflare',
                        'title' => 'Account',
                        'href' => 'https://www.cloudflare.com/my-account.html'
                    )
                ));
            }

            foreach ($menu_items as $menu_item) {
                $wp_admin_bar->add_menu($menu_item);
            }
        }
    }

    /**
     * Template filter
     *
     * @param $template
     * @return string
     */
    function template($template) {
        $w3_mobile = w3_instance('W3_Mobile');

        $mobile_template = $w3_mobile->get_template();

        if ($mobile_template) {
            return $mobile_template;
        } else {
            $w3_referrer = w3_instance('W3_Referrer');

            $referrer_template = $w3_referrer->get_template();

            if ($referrer_template) {
                return $referrer_template;
            }
        }

        return $template;
    }

    /**
     * Stylesheet filter
     *
     * @param $stylesheet
     * @return string
     */
    function stylesheet($stylesheet) {
        $w3_mobile = w3_instance('W3_Mobile');

        $mobile_stylesheet = $w3_mobile->get_stylesheet();

        if ($mobile_stylesheet) {
            return $mobile_stylesheet;
        } else {
            $w3_referrer = w3_instance('W3_Referrer');

            $referrer_stylesheet = $w3_referrer->get_stylesheet();

            if ($referrer_stylesheet) {
                return $referrer_stylesheet;
            }
        }

        return $stylesheet;
    }

    /**
     * Template filter
     *
     * @param $template
     * @return string
     */
    function template_preview($template) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $theme_name = W3_Request::get_string('w3tc_theme');

        $theme = w3tc_get_theme($theme_name);

        if ($theme) {
            return $theme['Template'];
        }

        return $template;
    }

    /**
     * Stylesheet filter
     *
     * @param $stylesheet
     * @return string
     */
    function stylesheet_preview($stylesheet) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $theme_name = W3_Request::get_string('w3tc_theme');

        $theme = w3tc_get_theme($theme_name);

        if ($theme) {
            return $theme['Stylesheet'];
        }

        return $stylesheet;
    }

    /**
     * Footer plugin action
     *
     * @return void
     */
    function footer() {
        echo '<div style="text-align: center;">Performance Optimization <a href="http://www.w3-edge.com/wordpress-plugins/" rel="external nofollow">WordPress Plugins</a> by W3 EDGE</div>';
    }

    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        global $wpdb;

        if ($buffer != '' && w3_is_xml($buffer)) {
            if (w3_is_database_error($buffer)) {
                status_header(503);
            } else {
                /**
                 * Replace links for preview mode
                 */
                if (w3_is_preview_mode() && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != W3TC_POWERED_BY) {
                    $domain_url_regexp = w3_get_domain_url_regexp();

                    $buffer = preg_replace_callback('~(href|src|action)=([\'"])(' . $domain_url_regexp . ')?(/[^\'"]*)~', array(
                        &$this,
                        'link_replace_callback'
                    ), $buffer);
                }

                /**
                 * Add footer comment
                 */
                $date = date_i18n('Y-m-d H:i:s');
                $host = (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');

                if ($this->_config->get_string('common.support') != '' || $this->_config->get_boolean('common.tweeted')) {
                    $buffer .= sprintf("\r\n<!-- Served from: %s @ %s by W3 Total Cache -->", w3_escape_comment($host), $date);
                } else {
                    $strings = array();

                    if ($this->_config->get_boolean('minify.enabled') && !$this->_config->get_boolean('minify.debug')) {
                        $w3_plugin_minify = w3_instance('W3_Plugin_Minify');

                        $strings[] = sprintf("Minified using %s%s", w3_get_engine_name($this->_config->get_string('minify.engine')), ($w3_plugin_minify->minify_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_minify->minify_reject_reason) : ''));
                    }

                    if ($this->_config->get_boolean('pgcache.enabled') && !$this->_config->get_boolean('pgcache.debug')) {
                        $w3_pgcache = w3_instance('W3_PgCache');

                        $strings[] = sprintf("Page Caching using %s%s", w3_get_engine_name($this->_config->get_string('pgcache.engine')), ($w3_pgcache->cache_reject_reason != '' ? sprintf(' (%s)', $w3_pgcache->cache_reject_reason) : ''));
                    }

                    if ($this->_config->get_boolean('dbcache.enabled') &&
                            !$this->_config->get_boolean('dbcache.debug')) {
                        $db = w3_instance('W3_DbCache');
                        $append = (!is_null($db->cache_reject_reason) ?
                            sprintf(' (%s)', $db->cache_reject_reason) :
                            '');

                        if ($db->query_hits) {
                            $strings[] = sprintf("Database Caching %d/%d queries in %.3f seconds using %s%s",
                                $db->query_hits, $db->query_total, $db->time_total,
                                w3_get_engine_name($this->_config->get_string('dbcache.engine')),
                                $append);
                        } else {
                            $strings[] = sprintf("Database Caching using %s%s",
                                w3_get_engine_name($this->_config->get_string('dbcache.engine')),
                                $append);
                        }
                    }

                    if (w3_is_dbcluster()) {
                        $db_cluster = w3_instance('W3_Enterprise_DbCluster');
                        $strings[] = $db_cluster->status_message();
                    }

                    if ($this->_config->get_boolean('objectcache.enabled') && !$this->_config->get_boolean('objectcache.debug')) {
                        $w3_objectcache = w3_instance('W3_ObjectCache');
                        
                        $append = ($w3_objectcache->cache_reject_reason != '' ?
                            sprintf(' (%s)', $w3_objectcache->cache_reject_reason) :
                            '');

                        $strings[] = sprintf("Object Caching %d/%d objects using %s%s",
                            $w3_objectcache->cache_hits, $w3_objectcache->cache_total,
                            w3_get_engine_name($this->_config->get_string('objectcache.engine')),
                            $append);
                    }

                    if ($this->_config->get_boolean('fragmentcache.enabled') && !$this->_config->get_boolean('fragmentcache.debug')) {
                        $w3_fragmentcache = w3_instance('W3_Pro_FragmentCache');
                        $append = ($w3_fragmentcache->cache_reject_reason != '' ?
                            sprintf(' (%s)', $w3_fragmentcache->cache_reject_reason) :'');
                        $strings[] = sprintf("Fragment Caching %d/%d fragments using %s%s",
                            $w3_fragmentcache->cache_hits, $w3_fragmentcache->cache_total,
                            w3_get_engine_name($this->_config->get_string('fragmentcache.engine')),
                            $append);
                    }

                    if ($this->_config->get_boolean('cdn.enabled') && !$this->_config->get_boolean('cdn.debug')) {
                        $w3_plugin_cdn = w3_instance('W3_Plugin_Cdn');
                        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');
                        $cdn = & $w3_plugin_cdncommon->get_cdn();
                        $via = $cdn->get_via();

                        $strings[] = sprintf("Content Delivery Network via %s%s", ($via ? $via : 'N/A'), ($w3_plugin_cdn->cdn_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_cdn->cdn_reject_reason) : ''));
                    }

                    if ($this->_config->get_boolean('newrelic.enabled')) {
                        $w3_newrelic = w3_instance('W3_Plugin_NewRelic');
                        $append = ($w3_newrelic->newrelic_reject_reason != '') ?
                                            sprintf(' (%s)', $w3_newrelic->newrelic_reject_reason) : '';
                        $strings[] = sprintf(__("Application Monitoring using New Relic%s", 'w3-total-cache'), $append);
                    }
                    $buffer .= "\r\n<!-- Performance optimized by W3 Total Cache. Learn more: http://www.w3-edge.com/wordpress-plugins/\r\n";

                    if (count($strings)) {
                        $buffer .= "\r\n" . implode("\r\n", $strings) . "\r\n";
                    }

                    $buffer .= sprintf("\r\n Served from: %s @ %s by W3 Total Cache -->", w3_escape_comment($host), $date);
                }

                if ($this->is_debugging()) {
                    if ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_boolean('dbcache.debug')) {
                        $db = w3_instance('W3_DbCache');
                        $buffer .= "\r\n\r\n" . $db->_get_debug_info();
                    }

                    if ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_boolean('objectcache.debug')) {
                        $w3_objectcache = w3_instance('W3_ObjectCache');
                        $buffer .= "\r\n\r\n" . $w3_objectcache->_get_debug_info();
                    }

                    if ($this->_config->get_boolean('fragmentcache.enabled') && $this->_config->get_boolean('fragmentcache.debug')) {
                        $w3_fragmentcache = w3_instance('W3_Pro_FragmentCache');
                        $buffer .= "\r\n\r\n" . $w3_fragmentcache->_get_debug_info();
                    }
                }
            }
        }

        return $buffer;
    }

    /**
     * Check if we can do modify contents
     *
     * @return boolean
     */
    function can_ob() {
        $enabled = w3_is_preview_mode();
        $enabled = $enabled || $this->_config->get_boolean('pgcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('dbcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('objectcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('browsercache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('minify.enabled');
        $enabled = $enabled || $this->_config->get_boolean('cdn.enabled');
        $enabled = $enabled || $this->_config->get_boolean('fragmentcache.enabled');
        $enabled = $enabled || w3_is_dbcluster();

        /**
         * Check if plugin enabled
         */
        if (!$enabled) {
            return false;
        }

        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }

        /**
         * Skip if doing AJAX
         */
        if (defined('DOING_AJAX')) {
            return false;
        }

        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            return false;
        }

        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            return false;
        }

        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            return false;
        }

        /**
         * Check User Agent
         */
        if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], W3TC_POWERED_BY) !== false) {
            return false;
        }

        return true;
    }

    /**
     * Preview link replace callback
     *
     * @param array $matches
     * @return string
     */
    function link_replace_callback($matches) {
        list (, $attr, $quote, $domain_url, , , $path) = $matches;

        $path .= (strstr($path, '?') !== false ? '&amp;' : '?') . 'w3tc_preview=1';

        return sprintf('%s=%s%s%s', $attr, $quote, $domain_url, $path);
    }

    /**
     * Now actually allow CF to see when a comment is approved/not-approved.
     *
     * @param int $id
     * @param string $status
     * @return void
     */
    function cloudflare_set_comment_status($id, $status) {
        if ($status == 'spam') {
            $email = $this->_config->get_string('cloudflare.email');
            $key = $this->_config->get_string('cloudflare.key');

            if ($email && $key) {
                w3_require_once(W3TC_LIB_W3_DIR . '/CloudFlare.php');
                @$w3_cloudflare = new W3_CloudFlare(array(
                    'email' => $email,
                    'key' => $key
                ));

                $comment = get_comment($id);

                $value = array(
                    'a' => $comment->comment_author,
                    'am' => $comment->comment_author_email,
                    'ip' => $comment->comment_author_IP,
                    'con' => substr($comment->comment_content, 0, 100)
                );

                $w3_cloudflare->external_event('WP_SPAM', json_encode($value));
            }
        }
    }

    /**
     * User login hook
     * Check if current user is not listed in pgcache.reject.* rules
     * If so, set a role cookie so the requests wont be cached
     */
    function check_login_action($logged_in_cookie = false, $expire = ' ', $expiration = 0, $user_id = 0, $action = 'logged_out') {
        global $current_user;
        if (isset($current_user->ID) && !$current_user->ID)
            $user_id = new WP_User($user_id);
        else
            $user_id = $current_user;
        
        $role = array_shift( $user_id->roles );
        $role_hash = md5(NONCE_KEY . $role);
        
        if ('logged_out' == $action) {
            setcookie('w3tc_logged_' . $role_hash, $expire, time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
            return;
        }
        
        if ('logged_in' != $action)
            return;
        
        if (in_array( $role, $this->_config->get_array('pgcache.reject.roles')))
            setcookie('w3tc_logged_' . $role_hash, true, $expire, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    }

    /**
     * @return int
     */
    function _detect_post_id() {
        global $posts, $comment_post_ID, $post_ID;

        if ($post_ID) {
            return $post_ID;
        } elseif ($comment_post_ID) {
            return $comment_post_ID;
        } elseif (is_single() || is_page() && count($posts)) {
            return $posts[0]->ID;
        } elseif (isset($_REQUEST['p'])) {
            return (integer) $_REQUEST['p'];
        }

        return 0;
    }

    function popup_script() {
        ?>
        <script type="text/javascript">
            function w3tc_popupadmin_bar(url) {
                return window.open(url, '', 'width=800,height=600,status=no,toolbar=no,menubar=no,scrollbars=yes');
            }
        </script>
            <?php
    }

    private function is_debugging() {
        $debug = $this->_config->get_boolean('pgcache.enabled') && $this->_config->get_boolean('pgcache.debug');
        $debug = $debug || ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_boolean('dbcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_boolean('objectcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.debug'));
        $debug = $debug || ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.debug'));
        $debug = $debug || ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cdn.debug'));
        $debug = $debug || ($this->_config->get_boolean('fragmentcache.enabled') && $this->_config->get_boolean('fragmentcache.debug'));

        return $debug;
    }
}
