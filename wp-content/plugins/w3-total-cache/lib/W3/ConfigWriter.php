<?php

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/ConfigData.php');

/**
 * Class W3_dataWriter
 */
class W3_ConfigWriter {
    /*
     * blog id of loaded config
     * @var integer
     */
    private $_blog_id;
    
    /*
     * Configuration
     * @var object
     */
    private $_data;

    /*
     * Is this preview config
     * @var boolean
     */
    private $_preview;

    /**
     * Sealing keys from master config which blocks key overrides
     * @var array
     */
    private $_sealing_keys_scope;

    /**
     * Constructor
     * 
     * @param integer $blog_id
     * @param boolean $preview
     */
    function __construct($blog_id, $preview) {
        $this->_blog_id = $blog_id;

        /**
         * defines $keys with descriptors
         * @var array $keys config keys
         * @var array $sealing_keys_scope
         */
        include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
        
        $this->_sealing_keys_scope = $sealing_keys_scope;
        $this->_data = new W3_ConfigData($keys);

        if (!$this->own_config_exists() && $blog_id  <= 0)
             $this->_data->set_defaults();

        $this->_preview = $preview;
        
        $this->_data->read($this->_get_config_filename());
    }

    /*
     * Compiles active config from different files
     *
     * @param string $cache_filename
     * @return config values
     */
    function create_compiled_config($cache_filename) {
        /**
         * defines $keys with descriptors
         * @var array $keys config keys
         */
        include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
        $compiled_config = new W3_ConfigData($keys);
        $compiled_config->set_defaults();

        // collect config data from master config
        if (!$compiled_config->read($this->_get_config_filename(true))) {
            // try to read production master config
            if ($this->_preview)
                $compiled_config->read($this->_get_config_filename(true, false));
        }

        $this->_put_instance_value_in_config($compiled_config);

        // append data from blog config
        $config_admin = w3_instance('W3_ConfigAdmin');
        $data = $compiled_config->get_array_from_file($this->_get_config_filename());
        if (is_null($data)) {
            $data = $this->_import_legacy_config($compiled_config);
        }
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                if (!$this->_key_sealed($key, $compiled_config->data, $config_admin, $value))
                    $compiled_config->set($key, $value);
            }
        }
        
        // save the value for 'home' in the config
        $this->_put_home_value_in_config($compiled_config->data);

        // save the bad_behavior path in config if plugin exists
        $this->_put_bad_behavior_in_config($compiled_config->data);

        $this->_post_process_values($compiled_config);

        // write cache
        if (!$compiled_config->write($cache_filename)) {
            // retry with folder creation
            w3_mkdir_from(dirname($cache_filename), W3TC_CACHE_DIR);
            $compiled_config->write($cache_filename);
        }
        $this->flush_apc($cache_filename, true);

        return $compiled_config->data;
    }

    /*
     * Converts configuration key returned in http _GET/_POST
     * to configuration key
     * 
     * @param $http_key string
     * @return string
     */
    function resolve_http_key($http_key) {
        return $this->_data->resolve_http_key($http_key);
    }
    
    /**
     * Sets config value
     *
     * @param string $key
     * @param string $value
     * @return mixed set value
     */
    function set($key, $value) {
        return $this->_data->set($key, $value);
    }

    /**
     * Sets default values
     */
    function set_defaults() {
        $this->_data->clear();
    }

    /**
     * Saves modified config
     */
    function save() {
        $filename = $this->_get_config_filename();
        if (!is_dir(dirname($filename))) {
            w3_require_once(W3TC_INC_DIR . '/functions/file.php');
            w3_mkdir_from(dirname($filename), WP_CONTENT_DIR);
        }

        if (!$this->_data->write($filename)) {
            if (is_dir(W3TC_CONFIG_DIR)) {
                w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
                w3_throw_on_write_error($filename, array(W3TC_CACHE_TMP_DIR, W3TC_CONFIG_DIR));
            }
        } else {
            $this->flush_apc($filename);
        }
    }

    /**
     * Deploys the config file from a preview config file
     *
     * @param integer $direction +1: preview->production
     *                           -1: production->preview
     * @param boolean $remove_source remove source file
     */
    function preview_production_copy($direction, $remove_source) {
        $preview_filename = $this->_get_config_filename(false, true);
        $production_filename = $this->_get_config_filename(false, false);

        if ($direction > 0) {
            $src = $preview_filename;
            $dest = $production_filename;
        } else {
            $src = $production_filename;
            $dest = $preview_filename;
        }

        if (!@copy($src, $dest)) {
            w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
            w3_throw_on_write_error($dest);
        }

        if ($remove_source)
            @unlink($src);
    }

    /**
     * Checks if own configuration file exists
     *
     * @return bool
     */
    function own_config_exists() {
        return @file_exists($this->_get_config_filename());
    }

    /**
     * Returns true is key is not modifiable anymore
     *
     * @param string $key
     * @param object $config_data
     * @param object $config_admin
     * @param mixed $value
     * @return bool
     */
    private function _key_sealed($key, &$config_data, $config_admin, $value) {
        // skip finalized values by confif sealing options
        foreach ($this->_sealing_keys_scope as $i) {
            if (substr($key, 0, strlen($i['prefix'])) == $i['prefix']) {
                if ($config_admin->get_boolean($i['key']))
                    return true;
            }
        }
        if ($key == 'minify.enabled' && !$config_data['minify.enabled'] && $value)
            return true;

        if ($key == 'pgcache.engine' && ($config_data['pgcache.engine'] != 'file_generic') && $value == 'file_generic')
           return true;

        if ($key == 'minify.engine')
            return true;

        if ($key == 'minify.rewrite' && !$config_data['minify.rewrite'] && $value)
            return true;

        if ($key == 'cloudflare.ips.ip4' || $key == 'cloudflare.ips.ip6')
            return true;

        return false;
    }

    /*
     * Returns config filename
     *
     * @param bool $master
     * @return string
     */
    private function _get_config_filename($force_master = false, 
            $forced_preview = null) {
        if (w3_force_master())
            $force_master = true;
        $preview = (!is_null($forced_preview) ? $forced_preview : $this->_preview);
        $postfix = ($preview ? '-preview' : '') . '.php';

        if ($this->_blog_id <= 0 || $force_master)
            return W3TC_CONFIG_DIR . '/master' . $postfix;

        return W3TC_CONFIG_DIR . '/' . 
            sprintf('%06d', $this->_blog_id) . $postfix;
    }

    /**
     * Checks that config options are compatible
     *
     * @param object $config
     */
    private function _post_process_values($config) {
        $helper = new W3_ConfigBase($config->data);

        // caching pages with query string is not supported by Disk Basic
        if ($helper->get_boolean('pgcache.cache.query')) {
            if ($helper->get_string('pgcache.engine') == 'file_generic')
                $config->data['pgcache.cache.query'] = false;
        }

        // When feeds are cached - we have to switch on .xml files handling
        // by rules for nginx to return correct headers
        if ($helper->get_boolean('pgcache.cache.feed')) {
            if ($helper->get_string('pgcache.engine') == 'file_generic')
                $config->data['pgcache.cache.nginx_handle_xml'] = true;
        }
    }

    /**
     * Reads legacy config file
     *
     * @param object $compiled_config
     * @return array
     */
    private function _import_legacy_config($compiled_config) {
        $suffix = '';

        if ($this->_blog_id > 0) {
            if (w3_is_network()) {
                if (w3_is_subdomain_install())
                    $suffix = '-' . w3_get_domain(w3_get_host());
                else {
                    // try subdir blog
                    $request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
                    $site_home_uri = w3_get_base_path();

                    if (substr($request_uri, 0, strlen($site_home_uri)) == $site_home_uri) {
                        $request_path_in_wp = '/' . substr($request_uri, strlen($site_home_uri));

                        $n = strpos($request_path_in_wp, '/', 1);
                        if ($n === false)
                            $blog_path_in_wp = substr($request_path_in_wp, 1);
                        else
                            $blog_path_in_wp = substr($request_path_in_wp, 1, $n - 1);

                        $suffix = '-' . ($blog_path_in_wp != 'wp-admin'? $blog_path_in_wp . '.': '') . w3_get_domain(w3_get_host());
                    }

                }
            }
        }

        $filename = WP_CONTENT_DIR . '/w3-total-cache-config' . $suffix . '.php';

        return $compiled_config->get_array_from_file($filename);
    }
    
    /**
     * Saves the value for 'wordpress.home' in the config cache.
     *
     * @param array
     * @return array
     **/
    private function _put_home_value_in_config(&$config_data) {
        // this also gets called during activation, when get_option is not yet available
        if (function_exists("get_option") && function_exists('wp_cache_get')) {
            global $wp_object_cache;
            if (isset($wp_object_cache) && !empty($wp_object_cache))
                $config_data['wordpress.home'] = get_option('home');
        }
        return $config_data;
    }

    /**
     * Saves the path to bad_behavior in config cache if it exists
     * @param $config_data
     */
    private function _put_bad_behavior_in_config(&$config_data) {
        if (function_exists('bb2_start')) {
            if (file_exists(WP_CONTENT_DIR . '/plugins/bad-behavior/bad-behavior-generic.php')) {
                $bb_file = WP_CONTENT_DIR . '/plugins/bad-behavior/bad-behavior-generic.php';
            } elseif (file_exists(WP_CONTENT_DIR . '/plugins/Bad-Behavior/bad-behavior-generic.php')) {
                $bb_file = WP_CONTENT_DIR . '/plugins/Bad-Behavior/bad-behavior-generic.php';
            } else {
                $bb_file = false;
            }

            if ($bb_file) {
                $config_data['pgcache.bad_behavior_path'] = $bb_file;
            }
        } else {
            $config_data['pgcache.bad_behavior_path'] = '';
        }
    }

    /**
     * Store instance in master config.
     * @param W3_ConfigData $compiled_config
     */
    private function _put_instance_value_in_config($compiled_config) {
        if (!isset($compiled_config->data['common.instance_id']) || $compiled_config->data['common.instance_id'] == 0) {
            $config2 = null;
            if ($this->_preview) {
                /**
                 * defines $keys with descriptors
                 * @var array $keys config keys
                 */
                include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
                $config2 = new W3_ConfigData($keys);
                $config2->read($this->_get_config_filename(true, false));
            }
            if (!is_null($config2) && isset($config2->data['common.instance_id'])
                && $config2->data['common.instance_id'] != 0) {
                $compiled_config->data['common.instance_id'] = $config2->data['common.instance_id'];
            } else {
                $compiled_config->data['common.instance_id'] = mt_rand();
                if (!isset($keys))
                    include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
                $master_config = new W3_ConfigData($keys);
                $master_config->read($this->_get_config_filename(true, false));
                $master_config->data['common.instance_id'] = $compiled_config->data['common.instance_id'];
                $master_config->write($this->_get_config_filename(true, false));
            }
        }
    }

    /**
     * Overloads the current ConfigWriter with found legacy values and saves to config file.
     */
    public function import_legacy_config_and_save() {
        if ($this->own_config_exists())
            return;

        /**
         * defines $keys with descriptors
         * @var array $keys config keys
         */
        include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
        $compiled_config = new W3_ConfigData($keys);
        $compiled_config->set_defaults();

        $config_admin = w3_instance('W3_ConfigAdmin');

        $data = $this->_import_legacy_config($compiled_config);
        if (!is_null($data)) {
            $master_data = array();
            if ($this->_blog_id != 0 && w3_is_network()) {
                $master_config = new W3_ConfigData($keys);
                $master_config->read($this->_get_config_filename(true));
                $master_data = $master_config->data;
            }

            foreach ($data as $key => $value) {
                if ($master_data && isset($master_data[$key]) && $master_data[$key] === $value)
                    continue;
                if (!$this->_key_sealed($key, $this->_data->data, $config_admin, $value))
                    $this->_data->set($key, $value);
            }
            // Since configs don't exist create them.
            $this->save();
            $config_admin->save();
        }
    }

    /**
     * Flush the APC
     * @param string $filename file to reload
     * @param bool $local_flush default false. Flushes over SNS if false. Local if true
     */
    private function flush_apc($filename, $local_flush = false) {
        // If apc.stat is set to 0 (= no automatic detection of changes files), we explicitly flush the apc cache
        if(function_exists('apc_clear_cache') && ini_get('apc.stat') == '0' && !(defined('DONOTFLUSHAPC') && DONOTFLUSHAPC)) {
            if ($local_flush) {
                /** @var $w3_cacheflush W3_CacheFlushLocal */
               $w3_cacheflush = w3_instance('W3_CacheFlushLocal');
                $w3_cacheflush->apc_reload_file($filename);
            } else {
                /** @var $w3_cacheflush W3_CacheFlush */
                $w3_cacheflush = w3_instance('W3_CacheFlush');
                $w3_cacheflush->apc_reload_file($filename);
            }
        }
    }
}
