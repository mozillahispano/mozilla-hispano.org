<?php

/**
 * W3 PgCache plugin - administrative interface
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_PgCacheAdmin
 */
class W3_Plugin_PgCacheAdmin extends W3_Plugin {
    /**
     * Activate plugin action
     */
    function activate() {
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_string('pgcache.engine') == 'file_generic') {
            /**
             * Disable enhanced mode if permalink structure is disabled
             */
            $permalink_structure = get_option('permalink_structure');

            if ($permalink_structure == '') {
                $this->_config->set('pgcache.engine', 'file');
                $this->_config->save();
            } else {
                if (w3_can_modify_rules(w3_get_pgcache_rules_core_path())) {
                    try {
                        $this->write_rules_core();
                    } catch (Exception $e) {}
                }

                if (w3_can_modify_rules(w3_get_pgcache_rules_cache_path())) {
                    try {
                        $this->write_rules_cache();
                    } catch (Exception $e)
                    {}
                }
            }
        }

        try{
            w3_copy_if_not_equal(W3TC_INSTALL_FILE_ADVANCED_CACHE, W3TC_ADDIN_FILE_ADVANCED_CACHE);
        }catch (Exception $ex) {}

        if (file_exists(W3TC_ADDIN_FILE_ADVANCED_CACHE)) {
            if ((!defined('WP_CACHE') || !WP_CACHE)) {
                try {
                    $this->enable_wp_cache();
                } catch(Exception $ex) {}
            }
        }

        $this->schedule();
        $this->schedule_prime();
    }

    /**
     * Deactivate plugin action
     */
    function deactivate() {
        $errors = array('errors' => array(), 'errors_short_form' => array(), 'ftp_form' => null);
        $results = array();
        $this->unschedule_prime();
        $this->unschedule();
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
        $results[] = $this->disable_wp_cache_with_message();

        if (w3_can_modify_rules(w3_get_pgcache_rules_cache_path())) {
            $this->remove_rules_cache();
        }

        if (w3_can_modify_rules(w3_get_pgcache_rules_core_path())) {
            $results[] = $this->remove_rules_core_with_message();
        }

        if ($results) {
            foreach ($results as $result) {
                if ($result['errors']) {
                    $errors['errors'] = array_merge($errors['errors'], $result['errors']);
                    $errors['errors_short_form'] = array_merge($errors['errors_short_form'], $result['errors_short_form']);
                    if (!isset($errors['ftp_form']))
                        $errors['ftp_form'] = $result['ftp_form'];
                }
            }
        }

        return $errors;
    }

    /**
     * Called from admin interface before configuration is changed
     *
     * @param object $old_config
     */
    function before_config_change(&$old_config, &$new_config) {
        if ($old_config->get_integer('pgcache.file.gc') !=
                $new_config->get_integer('pgcache.file.gc')) {
            $this->unschedule();
        }

        if ($old_config->get_integer('pgcache.prime.interval') !=
                $new_config->get_integer('pgcache.prime.interval')) {
            $this->unschedule_prime();
        }
    }

    /**
     * Called from admin interface after configuration is changed
     */
    function after_config_change() {
        $this->schedule();
        $this->schedule_prime();

        /**
         * Write page cache rewrite rules
         */
        if ($this->_config->get_boolean('pgcache.enabled') &&
                $this->_config->get_string('pgcache.engine') == 'file_generic') {
            if (w3_can_modify_rules(w3_get_pgcache_rules_core_path())) {
                try {
                    $this->write_rules_core();
                } catch (Exception $e) {}
            }

            if (w3_can_modify_rules(w3_get_pgcache_rules_cache_path())) {
                try {
                    $this->write_rules_cache();
                } catch (Exception $e)
                {}
            }
        } else {
            if (w3_can_modify_rules(w3_get_pgcache_rules_core_path())) {
                try {
                    $this->remove_rules_core();
                } catch (Exception $e) {}
            }

            if (w3_can_modify_rules(w3_get_pgcache_rules_cache_path())) {
                $this->remove_rules_cache();
            }
        }
    }

    function cleanup() {
        // We check to see if we're dealing with a cluster
        $config = w3_instance('W3_Config');
        $is_cluster = $config->get_boolean('cluster.messagebus.enabled');

        // If we are, we notify the subscribers. If not, we just cleanup in here
        if ($is_cluster) {
            $this->cleanup_cluster();
        } else {
            $this->cleanup_local();
        }

    }
    
    /**
     * Will trigger notifications to be sent to the cluster to 'order' them to clean their page cache.
     */
    function cleanup_cluster() {
        $sns_client = w3_instance('W3_Enterprise_SnsClient');
        $sns_client->pgcache_cleanup();
    }
    
    function cleanup_local() {
        $engine = $this->_config->get_string('pgcache.engine');

        switch ($engine) {
            case 'file':
                w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php');

                $w3_cache_file_cleaner = new W3_Cache_File_Cleaner(array(
                    'cache_dir' => w3_cache_blog_dir('page'),
                    'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
                ));

                $w3_cache_file_cleaner->clean();
                break;

            case 'file_generic':
                w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner/Generic.php');

                if (w3_get_blog_id() == 0)
                    $flush_dir = W3TC_CACHE_PAGE_ENHANCED_DIR;
                else
                    $flush_dir = W3TC_CACHE_PAGE_ENHANCED_DIR . '/' . w3_get_domain(w3_get_host());

                $w3_cache_file_cleaner_generic = new W3_Cache_File_Cleaner_Generic(array(
                    'exclude' => array(
                        '.htaccess'
                    ),
                    'cache_dir' => $flush_dir,
                    'expire' => $this->_config->get_integer('browsercache.html.lifetime'),
                    'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
                ));

                $w3_cache_file_cleaner_generic->clean();
                break;
        }
    }

    /**
     * Prime cache
     *
     * @param integer $start
     * @return void
     */
    function prime($start = 0) {
        $start = (int) $start;

        /**
         * Don't start cache prime if queues are still scheduled
         */
        if ($start == 0) {
            $crons = _get_cron_array();

            foreach ($crons as $timestamp => $hooks) {
                foreach ($hooks as $hook => $keys) {
                    foreach ($keys as $key => $data) {
                        if ($hook == 'w3_pgcache_prime' && count($data['args'])) {
                            return;
                        }
                    }
                }
            }
        }

        $interval = $this->_config->get_integer('pgcache.prime.interval');
        $limit = $this->_config->get_integer('pgcache.prime.limit');
        $sitemap = $this->_config->get_string('pgcache.prime.sitemap');

        /**
         * Parse XML sitemap
         */
        $urls = $this->parse_sitemap($sitemap);

        /**
         * Queue URLs
         */
        $queue = array_slice($urls, $start, $limit);

        if (count($urls) > ($start + $limit)) {
            wp_schedule_single_event(time() + $interval, 'w3_pgcache_prime', array(
                $start + $limit
            ));
        }

        /**
         * Make HTTP requests and prime cache
         */
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');
        w3_require_once(W3TC_INC_DIR . '/functions/url.php');

        // use empty user-agent since by default we use W3TC-powered by
        // which blocks caching
        foreach ($queue as $url)
            w3_http_get($url, array('user-agent' => ''));
    }

    /**
     * Parses sitemap
     *
     * @param string $url
     * @return array
     */
    function parse_sitemap($url) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');

        $urls = array();
        $response = w3_http_get($url);

        if (!is_wp_error($response) && $response['response']['code'] == 200) {
            $url_matches = null;
            $sitemap_matches = null;

            if (preg_match_all('~<sitemap>(.*?)</sitemap>~is', $response['body'], $sitemap_matches)) {
                $loc_matches = null;

                foreach ($sitemap_matches[1] as $sitemap_match) {
                    if (preg_match('~<loc>(.*?)</loc>~is', $sitemap_match, $loc_matches)) {
                        $loc = trim($loc_matches[1]);

                        if ($loc) {
                            $urls = array_merge($urls, $this->parse_sitemap($loc));
                        }
                    }
                }
            } elseif (preg_match_all('~<url>(.*?)</url>~is', $response['body'], $url_matches)) {
                $locs = array();
                $loc_matches = null;
                $priority_matches = null;

                foreach ($url_matches[1] as $url_match) {
                    $loc = '';
                    $priority = 0;

                    if (preg_match('~<loc>(.*?)</loc>~is', $url_match, $loc_matches)) {
                        $loc = trim($loc_matches[1]);
                    }

                    if (preg_match('~<priority>(.*?)</priority>~is', $url_match, $priority_matches)) {
                        $priority = (double) trim($priority_matches[1]);
                    }

                    if ($loc && $priority) {
                        $locs[$loc] = $priority;
                    }
                }

                arsort($locs);

                $urls = array_keys($locs);
            }
        }

        return $urls;
    }

    /**
     * Schedules events
     */
    function schedule() {
        if ($this->_config->get_boolean('pgcache.enabled') && ($this->_config->get_string('pgcache.engine') == 'file' || $this->_config->get_string('pgcache.engine') == 'file_generic')) {
            if (!wp_next_scheduled('w3_pgcache_cleanup')) {
                wp_schedule_event(current_time('timestamp'), 'w3_pgcache_cleanup', 'w3_pgcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Schedule prime event
     */
    function schedule_prime() {
        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_boolean('pgcache.prime.enabled')) {
            if (!wp_next_scheduled('w3_pgcache_prime')) {
                wp_schedule_event(current_time('timestamp'), 'w3_pgcache_prime', 'w3_pgcache_prime');
            }
        } else {
            $this->unschedule_prime();
        }
    }

    /**
     * Unschedules events
     */
    function unschedule() {
        if (wp_next_scheduled('w3_pgcache_cleanup')) {
            wp_clear_scheduled_hook('w3_pgcache_cleanup');
        }
    }

    /**
     * Unschedules prime
     */
    function unschedule_prime() {
        if (wp_next_scheduled('w3_pgcache_prime')) {
            wp_clear_scheduled_hook('w3_pgcache_prime');
        }
    }

    /**
     * Erases WP_CACHE define
     *
     * @param string $content
     * @return mixed
     */
    function erase_wp_cache($content) {
        $content = preg_replace("~\\/\\*\\* Enable W3 Total Cache \\*\\*?\\/.*?\\/\\/ Added by W3 Total Cache(\r\n)*~s", '', $content);
        $content = preg_replace("~(\\/\\/\\s*)?define\\s*\\(\\s*['\"]?WP_CACHE['\"]?\\s*,.*?\\)\\s*;+\\r?\\n?~is", '', $content);

        return $content;
    }

    /**
     * Enables WP_CACHE
     * @return boolean
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function enable_wp_cache() {

        $config_path = w3_get_wp_config_path();
        $config_data = w3_wp_read_from_file($config_path);

        if ($config_data === false) {
            return false;
        }

        $new_config_data = $this->erase_wp_cache($config_data);
        $new_config_data = preg_replace('~<\?(php)?~', "\\0\r\n/** Enable W3 Total Cache */\r\ndefine('WP_CACHE', true); // Added by W3 Total Cache\r\n", $new_config_data, 1);

        if ($new_config_data != $config_data) {
            w3_wp_write_to_file($config_path, $new_config_data);
         }

        return true;
    }

    /**
     * Disables WP_CACHE
     * @return bool
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function disable_wp_cache() {
        $config_path = w3_get_wp_config_path();
        $config_data = @file_get_contents($config_path);

        if ($config_data === false) {
            return false;
        }

        $new_config_data = $this->erase_wp_cache($config_data);

        if ($new_config_data != $config_data) {
            w3_wp_write_to_file($config_path, $new_config_data);
        }

        return true;
    }

    /**
     * Generates rules for WP dir
     *
     * @return string
     */
    function generate_rules_core() {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->generate_rules_core_apache();

            case w3_is_nginx():
                return $this->generate_rules_core_nginx();
        }

        return false;
    }

    /**
     * Generates directives for file cache dir
     *
     * @return string
     */
    function generate_rules_cache() {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->generate_rules_cache_apache();

            case w3_is_nginx():
                return $this->generate_rules_cache_nginx();
        }

        return false;
    }

    /**
     * Generates rules for WP dir
     *
     * @return string
     */
    function generate_rules_core_apache() {
        $is_network = w3_is_network();

        $base_path = w3_get_base_path();
        $home_path = w3_get_home_path();
        $rewrite_base = ($is_network ? $base_path : $home_path);
        $cache_dir = w3_path(W3TC_CACHE_PAGE_ENHANCED_DIR);
        $permalink_structure = get_option('permalink_structure');

        $current_user = get_currentuserinfo();

        /**
         * Auto reject cookies
         */
        $reject_cookies = array(
            'comment_author',
            'wp-postpass'
        );

        if ($this->_config->get_string('pgcache.engine') == 'file_generic') {
            $reject_cookies[] = 'w3tc_logged_out';
        }

        /**
         * Reject cache for logged in users
         * OR
         * Reject cache for roles if any
         */
        if ($this->_config->get_boolean('pgcache.reject.logged')) {
            $reject_cookies = array_merge($reject_cookies, array(
                'wordpress_logged_in'
            ));
        } elseif($this->_config->get_boolean('pgcache.reject.logged_roles')) {
            $new_cookies = array();
            foreach( $this->_config->get_array('pgcache.reject.roles') as $role ) {
                $new_cookies[] = 'w3tc_logged_' . md5(NONCE_KEY . $role);
            }
            $reject_cookies = array_merge($reject_cookies, $new_cookies);
        }

        /**
         * Custom config
         */
        $reject_cookies = array_merge($reject_cookies, $this->_config->get_array('pgcache.reject.cookie'));
        w3_array_trim($reject_cookies);

        $reject_user_agents = $this->_config->get_array('pgcache.reject.ua');
        if ($this->_config->get_boolean('pgcache.compatibility')) {
            $reject_user_agents = array_merge(array(W3TC_POWERED_BY), $reject_user_agents);
        }

        w3_array_trim($reject_user_agents);

        /**
         * Generate directives
         */
        $env_W3TC_UA = '';
        $env_W3TC_REF = '';
        $env_W3TC_SSL = '';
        $env_W3TC_ENC = '';

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_PGCACHE_CORE . "\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteBase " . $rewrite_base . "\n";

        if ($this->_config->get_boolean('pgcache.debug')) {
            $rules .= "    RewriteRule ^(.*\\/)?w3tc_rewrite_test$ $1?w3tc_rewrite_test=1 [L]\n";
        }

        /**
         * Check for mobile redirect
         */
        if ($this->_config->get_boolean('mobile.enabled')) {
            $mobile_groups = $this->_config->get_array('mobile.rgroups');

            foreach ($mobile_groups as $mobile_group => $mobile_config) {
                $mobile_enabled = (isset($mobile_config['enabled']) ? (boolean) $mobile_config['enabled'] : false);
                $mobile_agents = (isset($mobile_config['agents']) ? (array) $mobile_config['agents'] : '');
                $mobile_redirect = (isset($mobile_config['redirect']) ? $mobile_config['redirect'] : '');

                if ($mobile_enabled && count($mobile_agents) && $mobile_redirect) {
                    $rules .= "    RewriteCond %{HTTP_USER_AGENT} (" . implode('|', $mobile_agents) . ") [NC]\n";
                    $rules .= "    RewriteRule .* " . $mobile_redirect . " [R,L]\n";
                }
            }
        }

        /**
         * Check for referrer redirect
         */
        if ($this->_config->get_boolean('referrer.enabled')) {
            $referrer_groups = $this->_config->get_array('referrer.rgroups');

            foreach ($referrer_groups as $referrer_group => $referrer_config) {
                $referrer_enabled = (isset($referrer_config['enabled']) ? (boolean) $referrer_config['enabled'] : false);
                $referrer_referrers = (isset($referrer_config['referrers']) ? (array) $referrer_config['referrers'] : '');
                $referrer_redirect = (isset($referrer_config['redirect']) ? $referrer_config['redirect'] : '');

                if ($referrer_enabled && count($referrer_referrers) && $referrer_redirect) {
                    $rules .= "    RewriteCond %{HTTP_COOKIE} w3tc_referrer=.*(" . implode('|', $referrer_referrers) . ") [NC]\n";
                    $rules .= "    RewriteRule .* " . $referrer_redirect . " [R,L]\n";
                }
            }
        }

        /**
         * Set mobile groups
         */
        if ($this->_config->get_boolean('mobile.enabled')) {
            $mobile_groups = array_reverse($this->_config->get_array('mobile.rgroups'));

            foreach ($mobile_groups as $mobile_group => $mobile_config) {
                $mobile_enabled = (isset($mobile_config['enabled']) ? (boolean) $mobile_config['enabled'] : false);
                $mobile_agents = (isset($mobile_config['agents']) ? (array) $mobile_config['agents'] : '');
                $mobile_redirect = (isset($mobile_config['redirect']) ? $mobile_config['redirect'] : '');

                if ($mobile_enabled && count($mobile_agents) && !$mobile_redirect) {
                    $rules .= "    RewriteCond %{HTTP_USER_AGENT} (" . implode('|', $mobile_agents) . ") [NC]\n";
                    $rules .= "    RewriteRule .* - [E=W3TC_UA:_" . $mobile_group . "]\n";
                    $env_W3TC_UA = '%{ENV:W3TC_UA}';
                }
            }
        }

        /**
         * Set referrer groups
         */
        if ($this->_config->get_boolean('referrer.enabled')) {
            $referrer_groups = array_reverse($this->_config->get_array('referrer.rgroups'));

            foreach ($referrer_groups as $referrer_group => $referrer_config) {
                $referrer_enabled = (isset($referrer_config['enabled']) ? (boolean) $referrer_config['enabled'] : false);
                $referrer_referrers = (isset($referrer_config['referrers']) ? (array) $referrer_config['referrers'] : '');
                $referrer_redirect = (isset($referrer_config['redirect']) ? $referrer_config['redirect'] : '');

                if ($referrer_enabled && count($referrer_referrers) && !$referrer_redirect) {
                    $rules .= "    RewriteCond %{HTTP_COOKIE} w3tc_referrer=.*(" . implode('|', $referrer_referrers) . ") [NC]\n";
                    $rules .= "    RewriteRule .* - [E=W3TC_REF:_" . $referrer_group . "]\n";
                    $env_W3TC_REF = '%{ENV:W3TC_REF}';
                }
            }
        }

        /**
         * Set HTTPS
         */
        if ($this->_config->get_boolean('pgcache.cache.ssl')) {
            $rules .= "    RewriteCond %{HTTPS} =on\n";
            $rules .= "    RewriteRule .* - [E=W3TC_SSL:_ssl]\n";
            $rules .= "    RewriteCond %{SERVER_PORT} =443\n";
            $rules .= "    RewriteRule .* - [E=W3TC_SSL:_ssl]\n";
            $env_W3TC_SSL = '%{ENV:W3TC_SSL}';
        }

        $cache_path = str_replace(w3_get_document_root(), '', $cache_dir);

        /**
         * Set Accept-Encoding
         */
        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.compression')) {
            $rules .= "    RewriteCond %{HTTP:Accept-Encoding} gzip\n";
            $rules .= "    RewriteRule .* - [E=W3TC_ENC:_gzip]\n";
            $env_W3TC_ENC = '%{ENV:W3TC_ENC}';
        }

        $use_cache_rules = '';
        /**
         * Don't accept POSTs
         */
        $use_cache_rules .= "    RewriteCond %{REQUEST_METHOD} !=POST\n";

        /**
         * Query string should be empty
         */
        $use_cache_rules .= "    RewriteCond %{QUERY_STRING} =\"\"\n";

        /**
         * Check permalink structure trailing slash
         */
        if (substr($permalink_structure, -1) == '/') {
            $use_cache_rules .= "    RewriteCond %{REQUEST_URI} \\/$\n";
        }

        /**
         * Check for rejected cookies
         */
        $use_cache_rules .= "    RewriteCond %{HTTP_COOKIE} !(" . implode('|', array_map('w3_preg_quote', $reject_cookies)) . ") [NC]\n";

        /**
         * Check for rejected user agents
         */
        if (count($reject_user_agents)) {
            $use_cache_rules .= "    RewriteCond %{HTTP_USER_AGENT} !(" . implode('|', array_map('w3_preg_quote', $reject_user_agents)) . ") [NC]\n";
        }

        /**
         * Make final rewrites for specific files
         */
        $uri_prefix =  $cache_path . '/%{HTTP_HOST}/%{REQUEST_URI}/' .
            '_index' . $env_W3TC_UA . $env_W3TC_REF . $env_W3TC_SSL;
        $switch = " -" . ($this->_config->get_boolean('pgcache.file.nfs') ? 'F' : 'f');

        // support for GoDaddy servers configuration which uses
        // SUBDOMAIN_DOCUMENT_ROOT variable
        if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']) &&
            $_SERVER['SUBDOMAIN_DOCUMENT_ROOT'] != $_SERVER['DOCUMENT_ROOT'])
            $document_root = '%{ENV:SUBDOMAIN_DOCUMENT_ROOT}';
        elseif (isset($_SERVER['PHP_DOCUMENT_ROOT']) &&
            $_SERVER['PHP_DOCUMENT_ROOT'] != $_SERVER['DOCUMENT_ROOT'])
            $document_root = '%{ENV:PHP_DOCUMENT_ROOT}';
        else
            $document_root = '%{DOCUMENT_ROOT}';

        // write rule to rewrite to .html file
        $ext = '.html';
        $rules .= $use_cache_rules;
        $rules .= "    RewriteCond \"" . $document_root . $uri_prefix . $ext .
            $env_W3TC_ENC . "\"" . $switch . "\n";
        $rules .= "    RewriteRule .* \"" . $uri_prefix . $ext .
            $env_W3TC_ENC . "\" [L]\n";

        $rules .= "</IfModule>\n";

        $rules .= W3TC_MARKER_END_PGCACHE_CORE . "\n";

        return $rules;
    }

    /**
     * Generates rules for WP dir
     *
     * @return string
     */
    function generate_rules_core_nginx() {
        $is_network = w3_is_network();

        $base_path = w3_get_base_path();
        $cache_dir = w3_path(W3TC_CACHE_PAGE_ENHANCED_DIR);
        $permalink_structure = get_option('permalink_structure');

        /**
         * Auto reject cookies
         */
        $reject_cookies = array(
            'comment_author',
            'wp-postpass'
        );

        if ($this->_config->get_string('pgcache.engine') == 'file_generic') {
            $reject_cookies[] = 'w3tc_logged_out';
        }

        /**
         * Reject cache for logged in users
         * OR
         * Reject cache for roles if any
         */
        if ($this->_config->get_boolean('pgcache.reject.logged')) {
            $reject_cookies = array_merge($reject_cookies, array(
                'wordpress_logged_in'
            ));
        } elseif ($this->_config->get_boolean('pgcache.reject.logged_roles')) {
            $new_cookies = array();
            foreach( $this->_config->get_array('pgcache.reject.roles') as $role ) {
                $new_cookies[] = 'w3tc_logged_' . md5(NONCE_KEY . $role);
            }
            $reject_cookies = array_merge($reject_cookies, $new_cookies);
        }

        /**
         * Custom config
         */
        $reject_cookies = array_merge($reject_cookies, $this->_config->get_array('pgcache.reject.cookie'));
        w3_array_trim($reject_cookies);
        
        $reject_user_agents = $this->_config->get_array('pgcache.reject.ua');
        if ($this->_config->get_boolean('pgcache.compatibility')) {
            $reject_user_agents = array_merge(array(W3TC_POWERED_BY), $reject_user_agents);
        }
        w3_array_trim($reject_user_agents);

        /**
         * Generate rules
         */
        $env_w3tc_ua = '';
        $env_w3tc_ref = '';
        $env_w3tc_ssl = '';
        $env_w3tc_ext = '';
        $env_w3tc_enc = '';

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_PGCACHE_CORE . "\n";
        if ($this->_config->get_boolean('pgcache.debug')) {
            $rules .= "rewrite ^(.*\\/)?w3tc_rewrite_test$ $1?w3tc_rewrite_test=1 last;\n";
        }

        /**
         * Check for mobile redirect
         */
        if ($this->_config->get_boolean('mobile.enabled')) {
            $mobile_groups = $this->_config->get_array('mobile.rgroups');

            foreach ($mobile_groups as $mobile_group => $mobile_config) {
                $mobile_enabled = (isset($mobile_config['enabled']) ? (boolean) $mobile_config['enabled'] : false);
                $mobile_agents = (isset($mobile_config['agents']) ? (array) $mobile_config['agents'] : '');
                $mobile_redirect = (isset($mobile_config['redirect']) ? $mobile_config['redirect'] : '');

                if ($mobile_enabled && count($mobile_agents) && $mobile_redirect) {
                    $rules .= "if (\$http_user_agent ~* \"(" . implode('|', $mobile_agents) . ")\") {\n";
                    $rules .= "    rewrite .* " . $mobile_redirect . " last;\n";
                    $rules .= "}\n";
                }
            }
        }

        /**
         * Check for referrer redirect
         */
        if ($this->_config->get_boolean('referrer.enabled')) {
            $referrer_groups = $this->_config->get_array('referrer.rgroups');

            foreach ($referrer_groups as $referrer_group => $referrer_config) {
                $referrer_enabled = (isset($referrer_config['enabled']) ? (boolean) $referrer_config['enabled'] : false);
                $referrer_referrers = (isset($referrer_config['referrers']) ? (array) $referrer_config['referrers'] : '');
                $referrer_redirect = (isset($referrer_config['redirect']) ? $referrer_config['redirect'] : '');

                if ($referrer_enabled && count($referrer_referrers) && $referrer_redirect) {
                    $rules .= "if (\$http_cookie ~* \"w3tc_referrer=.*(" . implode('|', $referrer_referrers) . ")\") {\n";
                    $rules .= "    rewrite .* " . $referrer_redirect . " last;\n";
                    $rules .= "}\n";
                }
            }
        }

        /**
         * Don't accept POSTs
         */
        $rules .= "set \$w3tc_rewrite 1;\n";
        $rules .= "if (\$request_method = POST) {\n";
        $rules .= "    set \$w3tc_rewrite 0;\n";
        $rules .= "}\n";

        /**
         * Query string should be empty
         */
        $rules .= "if (\$query_string != \"\") {\n";
        $rules .= "    set \$w3tc_rewrite 0;\n";
        $rules .= "}\n";

        /**
         * Check permalink structure trailing slash
         */
        if (substr($permalink_structure, -1) == '/') {
            $rules .= "if (\$request_uri !~ \\/$) {\n";
            $rules .= "    set \$w3tc_rewrite 0;\n";
            $rules .= "}\n";
        }

        /**
         * Check for rejected cookies
         */
        $rules .= "if (\$http_cookie ~* \"(" . implode('|', array_map('w3_preg_quote', $reject_cookies)) . ")\") {\n";
        $rules .= "    set \$w3tc_rewrite 0;\n";
        $rules .= "}\n";

        /**
         * Check for rejected user agents
         */
        if (count($reject_user_agents)) {
            $rules .= "if (\$http_user_agent ~* \"(" . implode('|', array_map('w3_preg_quote', $reject_user_agents)) . ")\") {\n";
            $rules .= "    set \$w3tc_rewrite 0;\n";
            $rules .= "}\n";
        }

        /**
         * Check mobile groups
         */
        if ($this->_config->get_boolean('mobile.enabled')) {
            $mobile_groups = array_reverse($this->_config->get_array('mobile.rgroups'));

            foreach ($mobile_groups as $mobile_group => $mobile_config) {
                $mobile_enabled = (isset($mobile_config['enabled']) ? (boolean) $mobile_config['enabled'] : false);
                $mobile_agents = (isset($mobile_config['agents']) ? (array) $mobile_config['agents'] : '');
                $mobile_redirect = (isset($mobile_config['redirect']) ? $mobile_config['redirect'] : '');

                if ($mobile_enabled && count($mobile_agents) && !$mobile_redirect) {
                    $rules .= "set \$w3tc_ua \"\";\n";

                    $rules .= "if (\$http_user_agent ~* \"(" . implode('|', $mobile_agents) . ")\") {\n";
                    $rules .= "    set \$w3tc_ua _" . $mobile_group . ";\n";
                    $rules .= "}\n";

                    $env_w3tc_ua = "\$w3tc_ua";
                }
            }
        }

        /**
         * Check referrer groups
         */
        if ($this->_config->get_boolean('referrer.enabled')) {
            $referrer_groups = array_reverse($this->_config->get_array('referrer.rgroups'));

            foreach ($referrer_groups as $referrer_group => $referrer_config) {
                $referrer_enabled = (isset($referrer_config['enabled']) ? (boolean) $referrer_config['enabled'] : false);
                $referrer_referrers = (isset($referrer_config['referrers']) ? (array) $referrer_config['referrers'] : '');
                $referrer_redirect = (isset($referrer_config['redirect']) ? $referrer_config['redirect'] : '');

                if ($referrer_enabled && count($referrer_referrers) && !$referrer_redirect) {
                    $rules .= "set \$w3tc_ref \"\";\n";

                    $rules .= "if (\$http_cookie ~* \"w3tc_referrer=.*(" . implode('|', $referrer_referrers) . ")\") {\n";
                    $rules .= "    set \$w3tc_ref _" . $referrer_group . ";\n";
                    $rules .= "}\n";

                    $env_w3tc_ref = "\$w3tc_ref";
                }
            }
        }

        if ($this->_config->get_boolean('pgcache.cache.ssl')) {
            $rules .= "set \$w3tc_ssl \"\";\n";

            $rules .= "if (\$scheme = https) {\n";
            $rules .= "    set \$w3tc_ssl _ssl;\n";
            $rules .= "}\n";

            $env_w3tc_ssl = "\$w3tc_ssl";
        }

        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.compression')) {
            $rules .= "set \$w3tc_enc \"\";\n";

            $rules .= "if (\$http_accept_encoding ~ gzip) {\n";
            $rules .= "    set \$w3tc_enc _gzip;\n";
            $rules .= "}\n";

            $env_w3tc_enc = "\$w3tc_enc";
        }

        $cache_path = str_replace(w3_get_document_root(), '', $cache_dir);
        $uri_prefix = $cache_path . "/\$http_host/" .
            "\$request_uri/_index" . $env_w3tc_ua . $env_w3tc_ref . $env_w3tc_ssl;

        if (!$this->_config->get_boolean('pgcache.cache.nginx_handle_xml')) {
            $env_w3tc_ext = '.html';

            $rules .= "if (-f \"\$document_root" . $uri_prefix . ".html" .
                $env_w3tc_enc . "\") {\n";
            $rules .= "  set \$w3tc_rewrite 0;\n";
            $rules .= "}\n";
        } else {
            $env_w3tc_ext = "\$w3tc_ext";

            $rules .= "set \$w3tc_ext \"\";\n";
            $rules .= "if (-f \"\$document_root" . $uri_prefix . ".html" .
                $env_w3tc_enc . "\") {\n";
            $rules .= "    set \$w3tc_ext .html;\n";
            $rules .= "}\n";

            $rules .= "if (-f \"\$document_root" . $uri_prefix . ".xml" .
                $env_w3tc_enc . "\") {\n";
            $rules .= "    set \$w3tc_ext .xml;\n";
            $rules .= "}\n";

            $rules .= "if (\$w3tc_ext = \"\") {\n";
            $rules .= "  set \$w3tc_rewrite 0;\n";
            $rules .= "}\n";
        }

        $rules .= "if (\$w3tc_rewrite = 1) {\n";
        $rules .= "    rewrite .* \"" . $uri_prefix . $env_w3tc_ext . $env_w3tc_enc . "\" last;\n";
        $rules .= "}\n";
        $rules .= W3TC_MARKER_END_PGCACHE_CORE . "\n";

        return $rules;
    }

    /**
     * Generates directives for file cache dir
     *
     * @return string
     */
    function generate_rules_cache_apache() {
        $charset = get_option('blog_charset');
        $pingback_url = get_bloginfo('pingback_url');

        $browsercache = $this->_config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.html.compression'));
        $expires = ($browsercache && $this->_config->get_boolean('browsercache.html.expires'));
        $lifetime = ($browsercache ? $this->_config->get_integer('browsercache.html.lifetime') : 0);
        $cache_control = ($browsercache && $this->_config->get_boolean('browsercache.html.cache.control'));
        $etag = ($browsercache && $this->_config->get_integer('browsercache.html.etag'));
        $w3tc = ($browsercache && $this->_config->get_integer('browsercache.html.w3tc'));
        $compatibility = $this->_config->get_boolean('pgcache.compatibility');

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_PGCACHE_CACHE . "\n";
        if ($compatibility) {
            $rules .= "Options -MultiViews\n";

            // allow to read files by apache if they are blocked at some level above
            $rules .= "<Files ~ \"\.(html|html_gzip|xml|xml_gzip)$\">\n";
            $rules .= "  Allow from all\n";
            $rules .= "</Files>\n";

            if (!$etag) {
                $rules .= "FileETag None\n";
            }

            $rules .= "AddDefaultCharset " . ($charset ? $charset : 'UTF-8') . "\n";
        }

        if ($etag) {
            $rules .= "FileETag MTime Size\n";
        }

        if ($compression) {
            $rules .= "<IfModule mod_mime.c>\n";
            $rules .= "    AddType text/html .html_gzip\n";
            $rules .= "    AddEncoding gzip .html_gzip\n";
            $rules .= "    AddType text/xml .xml_gzip\n";
            $rules .= "    AddEncoding gzip .xml_gzip\n";
            $rules .= "</IfModule>\n";
            $rules .= "<IfModule mod_deflate.c>\n";
            $rules .= "    SetEnvIfNoCase Request_URI \\.html_gzip$ no-gzip\n";
            $rules .= "    SetEnvIfNoCase Request_URI \\.xml_gzip$ no-gzip\n";
            $rules .= "</IfModule>\n";
        }

        if ($expires) {
            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";
            $rules .= "    ExpiresByType text/html M" . $lifetime . "\n";
            $rules .= "</IfModule>\n";
        }

        $header_rules = '';

        if ($compatibility) {
            $header_rules .= "    Header set X-Pingback \"" . $pingback_url . "\"\n";
        }

        if ($w3tc) {
            $header_rules .= "    Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
        }

        if ($compression) {
            $header_rules .= "    Header set Vary \"Accept-Encoding, Cookie\"\n";
        } else {
            if ($compatibility) {
                $header_rules .= "    Header set Vary \"Cookie\"\n";
            }
        }


        $set_last_modified = $this->_config->get_boolean('browsercache.html.last_modified');

        if (!$set_last_modified && $this->_config->get_boolean('browsercache.enabled')) {
            $header_rules .= "    Header unset Last-Modified\n";
        }

        if ($cache_control) {
            $cache_policy = $this->_config->get_string('browsercache.html.cache.policy');

            switch ($cache_policy) {
                case 'cache':
                    $header_rules .= "    Header set Pragma \"public\"\n";
                    $header_rules .= "    Header set Cache-Control \"public\"\n";
                    break;

                case 'cache_public_maxage':
                    $header_rules .= "    Header set Pragma \"public\"\n";

                    if ($expires) {
                        $header_rules .= "    Header append Cache-Control \"public\"\n";
                    } else {
                        $header_rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public\"\n";
                    }
                    break;

                case 'cache_validation':
                    $header_rules .= "    Header set Pragma \"public\"\n";
                    $header_rules .= "    Header set Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    break;

                case 'cache_noproxy':
                    $header_rules .= "    Header set Pragma \"public\"\n";
                    $header_rules .= "    Header set Cache-Control \"public, must-revalidate\"\n";
                    break;

                case 'cache_maxage':
                    $header_rules .= "    Header set Pragma \"public\"\n";

                    if ($expires) {
                        $header_rules .= "    Header append Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    } else {
                        $header_rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\"\n";
                    }
                    break;

                case 'no_cache':
                    $header_rules .= "    Header set Pragma \"no-cache\"\n";
                    $header_rules .= "    Header set Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\"\n";
                    break;
            }
        }

        if (strlen($header_rules) > 0) {
            $rules .= "<IfModule mod_headers.c>\n";
            $rules .= $header_rules;
            $rules .= "</IfModule>\n";
        }

        $rules .= W3TC_MARKER_END_PGCACHE_CACHE . "\n";

        return $rules;
    }

    /**
     * Generates directives for file cache dir
     *
     * @return string
     */
    function generate_rules_cache_nginx() {
        $cache_root = w3_path(W3TC_CACHE_PAGE_ENHANCED_DIR);
        $cache_dir = rtrim(str_replace(w3_get_document_root(), '', $cache_root), '/');

        if (w3_is_network()) {
            $cache_dir = preg_replace('~/w3tc.*?/~', '/w3tc.*?/', $cache_dir, 1);
        }

        $browsercache = $this->_config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.html.compression'));
        $expires = ($browsercache && $this->_config->get_boolean('browsercache.html.expires'));
        $lifetime = ($browsercache ? $this->_config->get_integer('browsercache.html.lifetime') : 0);
        $cache_control = ($browsercache && $this->_config->get_boolean('browsercache.html.cache.control'));
        $w3tc = ($browsercache && $this->_config->get_integer('browsercache.html.w3tc'));

        $common_rules = '';

        if ($expires) {
            $common_rules .= "    expires modified " . $lifetime . "s;\n";
        }

        if ($w3tc) {
            $common_rules .= "    add_header X-Powered-By \"" . W3TC_POWERED_BY . "\";\n";
        }

        if ($compression) {
            $common_rules .= "    add_header Vary \"Accept-Encoding, Cookie\";\n";
        } else {
            $common_rules .= "    add_header Vary Cookie;\n";
        }

        if ($cache_control) {
            $cache_policy = $this->_config->get_string('browsercache.html.cache.policy');

            switch ($cache_policy) {
                case 'cache':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public\";\n";
                    break;

                case 'cache_public_maxage':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public\";\n";
                    break;

                case 'cache_validation':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public, must-revalidate, proxy-revalidate\";\n";
                    break;

                case 'cache_noproxy':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public, must-revalidate\";\n";
                    break;

                case 'cache_maxage':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\";\n";
                    break;

                case 'no_cache':
                    $common_rules .= "    add_header Pragma \"no-cache\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\";\n";
                    break;
            }
        }

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_PGCACHE_CACHE . "\n";

        $rules .= "location ~ " . $cache_dir . ".*html$ {\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        if ($compression) {
            $rules .= "location ~ " . $cache_dir . ".*gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type text/html;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";
        }

        $rules .= W3TC_MARKER_END_PGCACHE_CACHE . "\n";

        return $rules;
    }

    /**
     * Writes directives to WP .htaccess
     *
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function write_rules_core() {
        $path = w3_get_pgcache_rules_core_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data !== false) {
                $data = $this->erase_rules_legacy($data);
                $data = $this->erase_rules_wpsc($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_PGCACHE_CORE);
        $replace_end = strpos($data, W3TC_MARKER_END_PGCACHE_CORE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_PGCACHE_CORE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_MINIFY_CORE => strlen(W3TC_MARKER_END_MINIFY_CORE) + 1,
                W3TC_MARKER_END_BROWSERCACHE_CACHE => strlen(W3TC_MARKER_END_BROWSERCACHE_CACHE) + 1,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            );

            foreach ($search as $string => $length) {
                $replace_start = strpos($data, $string);

                if ($replace_start !== false) {
                    $replace_start += $length;
                    break;
                }
            }
        }

        $rules = $this->generate_rules_core();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
        w3_wp_write_to_file($path, $data);
    }

    /**
     * Writes directives to file cache .htaccess
     * Throws exception on error
     */
    function write_rules_cache($use_fs = false) {
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

        $path = w3_get_pgcache_rules_cache_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                w3_throw_on_read_error($path);
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_PGCACHE_CACHE);
        $replace_end = strpos($data, W3TC_MARKER_END_PGCACHE_CACHE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            );

            foreach ($search as $string => $length) {
                $replace_start = strpos($data, $string);

                if ($replace_start !== false) {
                    $replace_start += $length;
                    break;
                }
            }
        }

        $rules = $this->generate_rules_cache();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        if (!@file_exists(dirname($path))) {
            w3_mkdir_from(dirname($path), W3TC_CACHE_DIR);
        }

        if ($use_fs) {
            w3_wp_write_to_file($path, $data);
        } else {
            if (!@file_put_contents($path, $data)) {
                w3_throw_on_write_error($path);
            }
        }
    }

    /**
     * Erases Page Cache core directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_core($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_PGCACHE_CORE, W3TC_MARKER_END_PGCACHE_CORE);

        return $data;
    }

    /**
     * Erases Page Cache cache directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_cache($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_PGCACHE_CACHE, W3TC_MARKER_END_PGCACHE_CACHE);

        return $data;
    }

    /**
     * Erases Page Cache legacy directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_legacy($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_PGCACHE_LEGACY, W3TC_MARKER_END_PGCACHE_LEGACY);

        return $data;
    }

    /**
     * Erases WP Super Cache rules directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_wpsc($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_PGCACHE_WPSC, W3TC_MARKER_END_PGCACHE_WPSC);

        return $data;
    }

    /**
     * Removes Page Cache core directives
     *
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function remove_rules_core() {
        $path = w3_get_pgcache_rules_core_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_core($data);

                w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
                w3_wp_write_to_file($path, $data);
            }
        }
    }

    /**
     * Removes Page Cache cache directives
     *
     * @return boolean
     */
    function remove_rules_cache() {
        $path = w3_get_pgcache_rules_cache_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_cache($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Removes Page Cache legacy directives
     *
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function remove_rules_legacy() {
        $path = w3_get_pgcache_rules_core_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_legacy($data);

                w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
                w3_wp_write_to_file($path, $data);
            }
        }
    }

    /**
     * Removes WPSC directives
     *
     * @return boolean
     */
    function remove_rules_wpsc() {
        $path = w3_get_pgcache_rules_core_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_wpsc($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Check if legacy rules exists
     *
     * @return boolean
     */
    function check_rules_has_legacy() {
        $path = w3_get_pgcache_rules_core_path();

        return (($data = @file_get_contents($path)) && w3_has_rules(w3_clean_rules($data), W3TC_MARKER_BEGIN_PGCACHE_LEGACY, W3TC_MARKER_END_PGCACHE_LEGACY));
    }

    /**
     * Check if legacy rules exists
     *
     * @return boolean
     */
    function check_rules_has_core() {
        $path = w3_get_pgcache_rules_core_path();

        return (($data = @file_get_contents($path)) && w3_has_rules(w3_clean_rules($data), W3TC_MARKER_BEGIN_PGCACHE_CORE, W3TC_MARKER_END_PGCACHE_CORE));
    }

    /**
     * Checks if core rules exists
     *
     * @return boolean
     */
    function check_rules_core() {
        $path = w3_get_pgcache_rules_core_path();
        $search = $this->generate_rules_core();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Checks if cache rules exists
     *
     * @return boolean
     */
    function check_rules_cache() {
        $path = w3_get_pgcache_rules_cache_path();
        $search = $this->generate_rules_cache();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Check if WPSC rules exists
     *
     * @return boolean
     */
    function check_rules_wpsc() {
        $path = w3_get_pgcache_rules_core_path();

        return (($data = @file_get_contents($path)) && w3_has_rules(w3_clean_rules($data), W3TC_MARKER_BEGIN_PGCACHE_WPSC, W3TC_MARKER_END_PGCACHE_WPSC));
    }

    /**
     * Returns required rules for module
     * @return array
     */
    function get_required_rules() {
        $rewrite_rules = array();
        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_string('pgcache.engine') == 'file_generic') {
            $pgcache_rules_cache_path = w3_get_pgcache_rules_cache_path();
            $rewrite_rules[] = array('filename' => $pgcache_rules_cache_path, 'content' => $this->generate_rules_cache());
            $pgcache_rules_core_path = w3_get_pgcache_rules_core_path();
            $rewrite_rules[] = array('filename' => $pgcache_rules_core_path, 'content' => $this->generate_rules_core());
        }
        return $rewrite_rules;
    }

    /**
     * @return array
     */
    function disable_wp_cache_with_message() {
        $ftp_form = null;
        $errors = array();
        $errors_short_form = array();

        try {
            $this->disable_wp_cache();
        } catch(Exception $e) {
            $errors[] = sprintf('To disable Page Cache remove <strong>define(\'WP_CACHE\', true);</strong>, edit the configuration file (<strong>%s</strong>)', w3_get_wp_config_path());
            $errors_short_form[] = sprintf('Edit file (<strong>%s</strong>) and remove <strong>define(\'WP_CACHE\', true);</strong>', w3_get_wp_config_path());
            if ($e instanceof FilesystemCredentialException)
                $ftp_form = $e->ftp_form();
        }
        return array('errors' => $errors, 'ftp_form' => $ftp_form, 'errors_short_form' => $errors_short_form);
    }

    /**
     * @param bool $check_engine if pgcache should be taken into account when removing
     * @return array
     */
    function remove_rules_core_with_message($check_engine = false) {
        $ftp_form = null;
        $errors = array();
        $errors_short_form = array();

        $engine_remove = $check_engine ?
                                $this->_config->get_string('pgcache.engine') != 'file_generic' ||
                                !$this->_config->get_boolean('pgcache.enabled') :
                                true;
        if ($engine_remove) {
            if ($this->check_rules_has_core()) {
                try {
                    $this->remove_rules_core();
                } catch(Exception $e) {
                    $errors[] = sprintf('To disable Page Cache Disc: Enhanced rules need to be removed. To remove them manually, edit the configuration file (<strong>%s</strong>) and remove all lines between and including <strong>%s</strong> and <strong>%s</strong> markers inclusive.'
                        , w3_get_pgcache_rules_core_path(),
                        W3TC_MARKER_BEGIN_PGCACHE_CORE, W3TC_MARKER_END_PGCACHE_CORE);
                    $errors_short_form[] = sprintf('Edit file (<strong>%s</strong>) and remove all lines between and including <strong>%s</strong> and <strong>%s</strong> markers inclusive.'
                        , w3_get_pgcache_rules_core_path(),
                        W3TC_MARKER_BEGIN_PGCACHE_CORE, W3TC_MARKER_END_PGCACHE_CORE);
                    if (!isset($ftp_form) && $e instanceof FilesystemCredentialException)
                        $ftp_form = $e->ftp_form();
                }
            }
        }
        return array('errors' => $errors, 'ftp_form' => $ftp_form, 'errors_short_form' => $errors_short_form);
    }

    /**
     * @return array
     */
    function remove_rules_cache_multisite_nginx_with_message() {
        $ftp_form = null;
        $errors = array();
        $errors_short_form = array();

        if (w3_is_multisite() && w3_is_nginx() && $this->check_rules_cache()) {
            try {
                $this->remove_rules_cache();
            } catch(Exception $e) {
                $errors[] = sprintf('To fully disable Page Cache Disc: Enhanced for Network Sites rules need to be removed. To remove them manually, edit the configuration file (<strong>%s</strong>) and remove all lines between and including <strong>%s</strong> and <strong>%s</strong> markers inclusive.'
                    , w3_get_pgcache_rules_core_path(),
                    W3TC_MARKER_BEGIN_PGCACHE_CACHE, W3TC_MARKER_END_PGCACHE_CACHE);
                $errors_short_form[] = sprintf('Edit file (<strong>%s</strong>) and remove all lines between and including <strong>%s</strong> and <strong>%s</strong> markers inclusive.'
                    , w3_get_pgcache_rules_core_path(),
                    W3TC_MARKER_BEGIN_PGCACHE_CACHE, W3TC_MARKER_END_PGCACHE_CACHE);

                if (!isset($ftp_form) && $e instanceof FilesystemCredentialException)
                    $ftp_form = $e->ftp_form();
            }
        }
        return array('errors' => $errors, 'ftp_form' => $ftp_form, 'errors_short_form' => $errors_short_form);
    }
}