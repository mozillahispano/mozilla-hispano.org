<?php

/**
 * W3 Minify plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/rule.php');
w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_MinifyAdmin
 */
class W3_Plugin_MinifyAdmin extends W3_Plugin {

    /**
     * Activate plugin action
     */
    function activate() {
        if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.rewrite')) {
            if (w3_can_modify_rules(w3_get_minify_rules_core_path())) {
                $this->write_rules_core();
            }

            if ($this->_config->get_string('minify.engine') == 'file') {

                if (w3_can_modify_rules(w3_get_minify_rules_cache_path())) {
                    try {
                        $this->write_rules_cache();
                    } catch (Exception $e) {
                    }
                }

                if (!file_exists(W3TC_CACHE_MINIFY_DIR .'/index.html')) {
                    if (!is_dir(W3TC_CACHE_MINIFY_DIR))
                        w3_mkdir(W3TC_CACHE_MINIFY_DIR, W3TC_CACHE_DIR);
                    @file_put_contents(W3TC_CACHE_MINIFY_DIR .'/index.html', '');
                }
            }
        }

        $this->schedule();
    }

    /**
     * Deactivate plugin action
     */
    function deactivate() {
        $this->unschedule();

        if (w3_can_modify_rules(w3_get_minify_rules_cache_path()) && w3_get_blog_id() == 0) {
            $this->remove_rules_cache();
        }

        if (w3_can_modify_rules(w3_get_minify_rules_core_path()) && w3_get_blog_id() == 0) {
            $this->remove_rules_core();
        }
    }

    /**
     * Schedules events
     */
    function schedule() {
        if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_string('minify.engine') == 'file') {
            if (!wp_next_scheduled('w3_minify_cleanup')) {
                wp_schedule_event(current_time('timestamp'), 'w3_minify_cleanup', 'w3_minify_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Unschedules events
     */
    function unschedule() {
        if (wp_next_scheduled('w3_minify_cleanup')) {
            wp_clear_scheduled_hook('w3_minify_cleanup');
        }
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner/Generic.php');

        $w3_cache_file_cleaner_generic = new W3_Cache_File_Cleaner_Generic(array(
            'exclude' => array(
                '*.files',
                '.htaccess',
                'index.php'
            ),
            'cache_dir' => w3_cache_blog_dir('minify'),
            'expire' => $this->_config->get_integer('minify.file.gc'),
            'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
        ));

        $w3_cache_file_cleaner_generic->clean();
    }

    /**
     * Called from admin interface before configuration is changed
     *
     * @param object $old_config
     * @param object $new_config
     * @return void
     */
    function before_config_change(&$old_config, &$new_config) {
        if ($old_config->get_integer('minify.file.gc') !=
                $new_config->get_integer('minify.file.gc')) {
            $this->unschedule();
        }
    }

    /**
     * Called from admin interface after configuration is changed
     */
    function after_config_change() {
        $this->schedule();

        if ($this->_config->get_boolean('minify.enabled') &&
                $this->_config->get_boolean('minify.rewrite')) {
            if (w3_can_modify_rules(w3_get_minify_rules_core_path())) {
                $this->write_rules_core();
            }

            if ($this->_config->get_string('minify.engine') == 'file') {
                if (w3_can_modify_rules(w3_get_minify_rules_cache_path())) {
                    try {
                        $this->write_rules_cache();
                    } catch (Exception $e) {
                    }
                }
            } else {
                if (w3_can_modify_rules(w3_get_minify_rules_cache_path()) && w3_get_blog_id() == 0) {
                    $this->remove_rules_cache();
                }
            }
        } else {
            if (w3_can_modify_rules(w3_get_minify_rules_core_path()) && w3_get_blog_id() == 0) {
                $this->remove_rules_core();
            }

            if (w3_can_modify_rules(w3_get_minify_rules_cache_path()) && w3_get_blog_id() == 0) {
                $this->remove_rules_cache();
            }
        }
    }

    /**
     * Generates rules
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
     * Generates rules
     *
     * @return string
     */
    function generate_rules_core_apache() {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);
        $minify_filename = w3_make_relative_path(W3TC_DIR . '/pub/minify.php',
            W3TC_CACHE_MINIFY_DIR);

        $engine = $this->_config->get_string('minify.engine');
        $browsercache = $this->_config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CORE . "\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteBase " . $cache_dir . "/\n";
        $rules .= "    RewriteRule /w3tc_rewrite_test$ $minify_filename?w3tc_rewrite_test=1 [L]\n";

        if ($engine == 'file') {
            if ($compression) {
                $rules .= "    RewriteCond %{HTTP:Accept-Encoding} gzip\n";
                $rules .= "    RewriteRule .* - [E=APPEND_EXT:.gzip]\n";
            }

            $rules .= "    RewriteCond %{REQUEST_FILENAME}%{ENV:APPEND_EXT} -" . ($this->_config->get_boolean('minify.file.nfs') ? 'F' : 'f') . "\n";
            $rules .= "    RewriteRule (.*) $1%{ENV:APPEND_EXT} [L]\n";
        }

        $rules .= "    RewriteRule ^(.+\\.(css|js))$ $minify_filename?file=$1 [L]\n";

        $rules .= "</IfModule>\n";
        $rules .= W3TC_MARKER_END_MINIFY_CORE . "\n";

        return $rules;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_core_nginx() {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);
        $minify_filename = w3_filename_to_uri(W3TC_DIR . '/pub/minify.php');

        $engine = $this->_config->get_string('minify.engine');
        $browsercache = $this->_config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CORE . "\n";
        $rules .= "rewrite ^$cache_dir.*/w3tc_rewrite_test$ $minify_filename?w3tc_rewrite_test=1 last;\n";

        if ($engine == 'file') {
            $rules .= "set \$w3tc_enc \"\";\n";

            if ($compression) {
                $rules .= "if (\$http_accept_encoding ~ gzip) {\n";
                $rules .= "    set \$w3tc_enc .gzip;\n";
                $rules .= "}\n";
            }

            $rules .= "if (-f \$request_filename\$w3tc_enc) {\n";
            $rules .= "    rewrite (.*) $1\$w3tc_enc break;\n";
            $rules .= "}\n";
        }

        $rules .= "rewrite ^$cache_dir/(.+\\.(css|js))$ $minify_filename?file=$1 last;\n";
        $rules .= W3TC_MARKER_END_MINIFY_CORE . "\n";

        return $rules;
    }

    /**
     * Generates rules
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
     * Generates rules
     *
     * @return string
     */
    function generate_rules_cache_apache() {
        $browsercache = $this->_config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));
        $expires = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.expires'));
        $lifetime = ($browsercache ? $this->_config->get_integer('browsercache.cssjs.lifetime') : 0);
        $cache_control = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.cache.control'));
        $etag = ($browsercache && $this->_config->get_integer('browsercache.html.etag'));
        $w3tc = ($browsercache && $this->_config->get_integer('browsercache.cssjs.w3tc'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";
        $rules .= "Options -MultiViews\n";

        if ($etag) {
            $rules .= "FileETag MTime Size\n";
        }

        if ($compression) {
            $rules .= "<IfModule mod_mime.c>\n";
            $rules .= "    AddEncoding gzip .gzip\n";
            $rules .= "    <Files *.css.gzip>\n";
            $rules .= "        ForceType text/css\n";
            $rules .= "    </Files>\n";
            $rules .= "    <Files *.js.gzip>\n";
            $rules .= "        ForceType application/x-javascript\n";
            $rules .= "    </Files>\n";
            $rules .= "</IfModule>\n";
            $rules .= "<IfModule mod_deflate.c>\n";
            $rules .= "    <IfModule mod_setenvif.c>\n";
            $rules .= "        SetEnvIfNoCase Request_URI \\.gzip$ no-gzip\n";
            $rules .= "    </IfModule>\n";
            $rules .= "</IfModule>\n";
        }

        if ($expires) {
            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";
            $rules .= "    ExpiresByType text/css M" . $lifetime . "\n";
            $rules .= "    ExpiresByType application/x-javascript M" . $lifetime . "\n";
            $rules .= "</IfModule>\n";
        }

        if ($w3tc || $compression || $cache_control) {
            $rules .= "<IfModule mod_headers.c>\n";

            if ($w3tc) {
                $rules .= "    Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
            }

            if ($compression) {
                $rules .= "    Header set Vary \"Accept-Encoding\"\n";
            }

            if ($cache_control) {
                $cache_policy = $this->_config->get_string('browsercache.cssjs.cache.policy');

                switch ($cache_policy) {
                    case 'cache':
                        $rules .= "    Header set Pragma \"public\"\n";
                        $rules .= "    Header set Cache-Control \"public\"\n";
                        break;

                    case 'cache_public_maxage':
                        $rules .= "    Header set Pragma \"public\"\n";

                        if ($expires) {
                            $rules .= "    Header append Cache-Control \"public\"\n";
                        } else {
                            $rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public\"\n";
                        }
                        break;

                    case 'cache_validation':
                        $rules .= "    Header set Pragma \"public\"\n";
                        $rules .= "    Header set Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                        break;

                    case 'cache_noproxy':
                        $rules .= "    Header set Pragma \"public\"\n";
                        $rules .= "    Header set Cache-Control \"public, must-revalidate\"\n";
                        break;

                    case 'cache_maxage':
                        $rules .= "    Header set Pragma \"public\"\n";

                        if ($expires) {
                            $rules .= "    Header append Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                        } else {
                            $rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\"\n";
                        }
                        break;

                    case 'no_cache':
                        $rules .= "    Header set Pragma \"no-cache\"\n";
                        $rules .= "    Header set Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\"\n";
                        break;
                }
            }

            $rules .= "</IfModule>\n";
        }

        $rules .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rules;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_cache_nginx() {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);

        $browsercache = $this->_config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression'));
        $expires = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.expires'));
        $lifetime = ($browsercache ? $this->_config->get_integer('browsercache.cssjs.lifetime') : 0);
        $cache_control = ($browsercache && $this->_config->get_boolean('browsercache.cssjs.cache.control'));
        $w3tc = ($browsercache && $this->_config->get_integer('browsercache.cssjs.w3tc'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";

        $common_rules = '';

        if ($expires) {
            $common_rules .= "    expires modified " . $lifetime . "s;\n";
        }

        if ($w3tc) {
            $common_rules .= "    add_header X-Powered-By \"" . W3TC_POWERED_BY . "\";\n";
        }

        if ($compression) {
            $common_rules .= "    add_header Vary \"Accept-Encoding\";\n";
        }

        if ($cache_control) {
            $cache_policy = $this->_config->get_string('browsercache.cssjs.cache.policy');

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

        $rules .= "location ~ " . $cache_dir . ".*\\.js$ {\n";
        $rules .= "    types {}\n";
        $rules .= "    default_type application/x-javascript;\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        $rules .= "location ~ " . $cache_dir . ".*\\.css$ {\n";
        $rules .= "    types {}\n";
        $rules .= "    default_type text/css;\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        if ($compression) {
            $rules .= "location ~ " . $cache_dir . ".*js\\.gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type application/x-javascript;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";

            $rules .= "location ~ " . $cache_dir . ".*css\\.gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type text/css;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";
        }

        $rules .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rules;
    }

    /**
     * Writes rules to file cache .htaccess
     *
     * @return boolean
     */
    function write_rules_core() {
        $path = w3_get_minify_rules_core_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data !== false) {
                $data = $this->erase_rules_legacy($data);
            } else {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_MINIFY_CORE);
        $replace_end = strpos($data, W3TC_MARKER_END_MINIFY_CORE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_MINIFY_CORE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
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

        return @file_put_contents($path, $data);
    }

    /**
     * Generate test rules for multisite subfolder sites on Apache.
     * @return string
     */
    function generate_multisite_subfolder_rewrite_test_rules_apache() {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);
        $minify_filename = w3_filename_to_uri(W3TC_DIR . '/pub/minify.php');

        $rule  = W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";
        $rule .= "<IfModule mod_rewrite.c> \n";
        $rule .= "    RewriteEngine On\n";
        $rule .= "    RewriteBase /\n";
        $rule .= "    RewriteRule ^[_0-9a-zA-Z-]+$cache_dir/[0-9]+/w3tc_rewrite_test$ $minify_filename?w3tc_rewrite_test=1 [L]\n";
        $rule .= "</IfModule>\n";
        $rule .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rule;
    }

    /**
     * Write rules to handle multisite subfolder rewrite test
     */
    function write_multiste_subfolder_rewrite_test_rules_apache() {
        if (!(w3_is_apache() ||  w3_is_litespeed()))
            return;
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

        $path = w3_get_home_root() . '/.htaccess';

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                w3_throw_on_read_error($path);
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_MINIFY_CACHE);
        $replace_end = strpos($data, W3TC_MARKER_END_MINIFY_CACHE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_PGCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0
            );

            foreach ($search as $string => $length) {
                $replace_start = strpos($data, $string);

                if ($replace_start !== false) {
                    $replace_start += $length;
                    break;
                }
            }
        }

        $rule = $this->generate_multisite_subfolder_rewrite_test_rules_apache();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rule, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rule);
        }

        if (!@file_put_contents($path, $data)) {
            w3_throw_on_write_error($path);
        }
    }

    /**
     * Writes rules to file cache .htaccess
     * Throw exceptions
     *
     */
    function write_rules_cache() {
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

        $path = w3_get_minify_rules_cache_path();

        if (!file_exists(dirname($path)))
            w3_mkdir_from(dirname($path), W3TC_CACHE_DIR);

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data !== false) {
                $data = $this->erase_rules_legacy($data);
            } else {
                w3_throw_on_read_error($path);
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_MINIFY_CACHE);
        $replace_end = strpos($data, W3TC_MARKER_END_MINIFY_CACHE);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_PGCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0
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

        if (!@file_put_contents($path, $data)) {
            w3_throw_on_write_error($path);
        }
    }

    /**
     * Erases Minify core directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_core($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_MINIFY_CORE, W3TC_MARKER_END_MINIFY_CORE);

        return $data;
    }

    /**
     * Erases Minify cache directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_cache($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_MINIFY_CACHE, W3TC_MARKER_END_MINIFY_CACHE);

        return $data;
    }

    /**
     * Erases Minify legacy directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_legacy($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_MINIFY_LEGACY, W3TC_MARKER_END_MINIFY_LEGACY);

        return $data;
    }

    /**
     * Removes Minify core directives
     *
     * @return boolean
     */
    function remove_rules_core() {
        $path = w3_get_minify_rules_core_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_core($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Removes Minify cache directives
     *
     * @return boolean
     */
    function remove_rules_cache() {
        $path = w3_get_minify_rules_cache_path();

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
     * Removes Minify legacy directives
     *
     * @return boolean
     */
    function remove_rules_legacy() {
        $path = w3_get_minify_rules_cache_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules_legacy($data);

                return @file_put_contents($path, $data);
            }

            return false;
        }

        return true;
    }

    /**
     * Check if core rules exists
     *
     * @return boolean
     */
    function check_rules_has_core() {
        $path = w3_get_minify_rules_core_path();

        return (($data = @file_get_contents($path)) && w3_has_rules(w3_clean_rules($data), W3TC_MARKER_BEGIN_MINIFY_CORE, W3TC_MARKER_END_MINIFY_CORE));
    }

    /**
     * Check if legacy rules exists
     *
     * @return boolean
     */
    function check_rules_has_legacy() {
        $path = w3_get_minify_rules_core_path();

        return (($data = @file_get_contents($path)) && w3_has_rules(w3_clean_rules($data), W3TC_MARKER_BEGIN_MINIFY_LEGACY, W3TC_MARKER_END_MINIFY_LEGACY));
    }

    /**
     * Checks if core rules exists
     *
     * @return boolean
     */
    function check_rules_core() {
        $path = w3_get_minify_rules_core_path();
        $search = $this->generate_rules_core();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Checks if cache rules exists
     *
     * @return boolean
     */
    function check_rules_cache() {
        $path = w3_get_minify_rules_cache_path();
        $search = $this->generate_rules_cache();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Checks if the subfolder rewrite test rules exists.
     * @return bool
     */
    function check_multisite_subfolder_test_rules_cache_apache() {
        $path = w3_get_home_root() . '/.htaccess';
        $search = $this->generate_multisite_subfolder_rewrite_test_rules_apache();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);

    }

    /**
     * @return array
     */
    function get_required_rules() {
        $rewrite_rules = array();
        if ($this->_config->get_string('minify.engine') == 'file') {
            $minify_rules_cache_path = w3_get_minify_rules_cache_path();
            $rewrite_rules[] = array('filename' => $minify_rules_cache_path, 'content'  => $this->generate_rules_cache());
        }
        $minify_rules_core_path = w3_get_minify_rules_core_path();
        $rewrite_rules[] = array('filename' => $minify_rules_core_path, 'content'  => $this->generate_rules_core());

        return $rewrite_rules;
    }

    /**
     * Add index.html to minify folder
     */
    function add_index_files_if_required() {
        if ($this->_config->get_string('minify.engine') == 'file') {
            if (!file_exists(W3TC_CACHE_MINIFY_DIR .'/index.html')) {
                if (!is_dir(W3TC_CACHE_MINIFY_DIR))
                    w3_mkdir(W3TC_CACHE_MINIFY_DIR, W3TC_CACHE_DIR);
                @file_put_contents(W3TC_CACHE_MINIFY_DIR .'/index.html', '');
            }
        }
    }
}
