<?php

/**
 * Interplugin communication
 */

/**
 * Class W3_Dispatcher
 */
class W3_Dispatcher {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * minify plugin enabled flag
     *
     * @var boolean
     */
    var $_minify_enabled = false;
    /**
     * minify
     *
     * @var W3_Minify
     */
    var $_minify = null;

    /**
     * @var W3_Plugin_CdnAdmin
     */
    var $_cdnadmin = null;

    /**
     * @var W3_Plugin_CdnCommon
     */
    var $_cdncommon = null;

    /**
     * PHP5 constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_minify_enabled = $this->_config->get_boolean('minify.enabled');
    }

    /**
     * Checks if specific local url is uploaded to CDN
     * @param string $url
     * @return bool
     */
    function is_url_cdn_uploaded($url) {
        if ($this->_minify_enabled) {
            $data = $this->_get_minify()->get_url_custom_data($url);
            if (is_array($data) && isset($data['cdn.status']) && $data['cdn.status'] == 'uploaded') {
                return true;
            }
        }
        // supported only for minify-based urls, futher is not needed now
        return false;
    }
    
    /**
     * Creates file for CDN upload.
     * Needed because minify can handle urls of non-existing files but CDN needs
     * real file to upload it
     */
    function create_file_for_cdn($file_name) {
        if ($this->_minify_enabled) {
            $minify_document_root = w3_cache_blog_dir('minify') . '/';
        
            if (!substr($file_name, 0, strlen($minify_document_root)) == $minify_document_root) {
                // unexpected file name
                return;
            }
            
            $short_file_name = substr($file_name, strlen($minify_document_root));
            $this->_get_minify()->store_to_file($short_file_name, $file_name);
        }
    }
    
    /**
     * Called on successful file upload to CDN
     * 
     * @param $file_name
     */
    function on_cdn_file_upload($file_name) {
        if ($this->_minify_enabled) {
            $minify_document_root = w3_cache_blog_dir('minify') . '/';
        
            if (!substr($file_name, 0, strlen($minify_document_root)) == $minify_document_root) {
                // unexpected file name
                return;
            }
            
            $short_file_name = substr($file_name, strlen($minify_document_root));
            $this->_get_minify()->set_file_custom_data($short_file_name, 
                    array('cdn.status' => 'uploaded'));
        }
    }
    
    /**
     * Returns cached minify object
     * @return W3_Minify
     */
    function _get_minify() {
        if (is_null($this->_minify)) {
            $this->_minify = w3_instance('W3_Minify');
        }
        
        return $this->_minify;
    }

    /**
     * Generates canonical header code for nginx if appropriate
     * @param boolean $cdnftp if CDN FTP is used
     * @return string
     */
    function on_browsercache_nginx_generation($cdnftp) {
        if (is_null($this->_cdnadmin)) {
            $this->_cdnadmin = w3_instance('W3_Plugin_CdnAdmin');
        }
        $rules = '';

        if ($this->_should_browsercache_generate_canonical($cdnftp)) {
            $rules = $this->_cdnadmin->generate_canonical_nginx($cdnftp);
        }
        return $rules;
    }

    /**
     * Checks whether canonical should be generated or not
     * @param boolean $cdnftp
     * @return bool
     */
    public function should_cdn_generate_canonical($cdnftp = false) {
        // CDN should not generate when using both nginx and browsercache due to limitation in nginx location checks
        // when having more than one check for same location
        if (w3_is_nginx() && $this->_config->get_boolean('browsercache.enabled'))
            return false;
        return $this->_canonical_generation_general_check($cdnftp);
    }

    /**
     * Checks whether canonical should be generated or not
     * @param boolean $cdnftp
     * @return bool
     */
    private function _should_browsercache_generate_canonical($cdnftp = false) {
        return $this->_canonical_generation_general_check($cdnftp) &&
                w3_is_nginx();
    }

    /**
     * Basic check if canonical generation should be done
     * @param boolean $cdnftp
     * @return bool
     */
    private function _canonical_generation_general_check($cdnftp) {
        if (is_null($this->_cdncommon)) {
            $this->_cdncommon = w3_instance('W3_Plugin_CdnCommon');
        }
        $cdn = $this->_cdncommon->get_cdn();
        // Use with cloudflare because they cache frontend
        return $this->_config->get_boolean('cdn.canonical_header') &&
            ((($this->_config->get_string('cdn.engine') != 'ftp' || $cdnftp) &&
                $cdn->headers_support() == W3TC_CDN_HEADER_MIRRORING) ||
                $this->_config->get_boolean('cloudflare.enabled'));
    }

    /**
     * If BrowserCache should generate rules specific for CDN. Used with CDN FTP
     * @return boolean;
     */
    public function should_browsercache_generate_rules_for_cdn() {
        if ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_string('cdn.engine') == 'ftp') {
            if (is_null($this->_cdncommon)) {
                $this->_cdncommon = w3_instance('W3_Plugin_CdnCommon');
            }
            $cdn = $this->_cdncommon->get_cdn();
            $domain = $cdn->get_domain();

            if ($domain)
                return true;
        }
        return false;
    }

    /**
     * Returns the domain used with the cdn.
     * @param string
     * @return string
     */
    public function get_cdn_domain($path = '') {
        if (is_null($this->_cdncommon)) {
            $this->_cdncommon = w3_instance('W3_Plugin_CdnCommon');
        }
        $cdn = $this->_cdncommon->get_cdn();
        return $cdn->get_domain($path);
    }

    /**
     * If rules should be generated for CloudFlare
     * @return bool
     */
    private function _should_generate_cloudflare_rules() {
        return (!$this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cloudflare.enabled'));
    }

    /**
     * Returns array of rule descriptors array('filename'=>'', 'content'=> '')
     * @return array
     */
    public function get_required_rules_for_cloudflare() {
        $rules = array();
        if ($this->_should_generate_cloudflare_rules()) {
            if (is_null($this->_cdnadmin)) {
                $this->_cdnadmin = w3_instance('W3_Plugin_CdnAdmin');
            }
            $rules = $this->_cdnadmin->get_required_rules();
        }
        return $rules;
    }

    /**
     * Makes get requests to url specific to a post, its permalink
     * @param $post_id
     * @return boolean returns true on success
     */
    public function prime_post($post_id) {
        /** @var $purges W3_PageUrls */
        $purges = w3_instance('W3_PageUrls');
        $post_urls = $purges->get_post_urls($post_id);

        foreach ($post_urls as $url) {
            $result = w3_http_get($url);
            if (is_wp_error($result))
                return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function remove_cloudflare_rules_with_message() {
        if (is_null($this->_cdnadmin)) {
            $this->_cdnadmin = w3_instance('W3_Plugin_CdnAdmin');
        }
        return $this->_cdnadmin->remove_rules_with_message();
    }

    public function send_minify_headers($config) {
        $cf = w3_instance('W3_CloudFlare');
        return !$config->get_boolean('cloudflare.enabled') ||
                ($config->get_boolean('cloudflare.enabled') && !$cf->minify_enabled());
    }
}

