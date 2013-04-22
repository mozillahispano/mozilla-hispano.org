<?php

/**
 * W3 CDN Netdna Class
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/Mirror.php');

define('W3TC_CDN_NETDNA_URL', 'netdna-cdn.com');

/**
 * Class W3_Cdn_Mirror_Netdna
 */
class W3_Cdn_Mirror_Netdna extends W3_Cdn_Mirror {
    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'alias' => '',
            'consumerkey' => '',
            'consumersecret' => ''
        ), $config);

        parent::__construct($config);
    }

    /**
     * Purges remote files
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function purge($files, &$results) {
        if (empty($this->_config['alias'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty Alias.');

            return false;
        }

        if (empty($this->_config['consumerkey'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty Consumer Key.');

            return false;
        }

        if (empty($this->_config['consumersecret'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty Consumer Secret.');

            return false;
        }

        if (!class_exists('NetDNA')) {
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
        }

        $api = new NetDNA($this->_config['alias'], $this->_config['consumerkey'], $this->_config['consumersecret']);

        $results = array();
        $local_path = $remote_path = '';
        $domain_is_valid = 0;
        $found_domain = false;

        try {
            $customdomains =  json_decode($api->get('/zones/pull.json'));

            if (preg_match("(200|201)", $customdomains->code)) {

                foreach ($files as $file) {
                    $local_path = $file['local_path'];
                    $remote_path = $file['remote_path'];

                    $domain_is_valid = 0;
                    $found_domain = false;

                    foreach ($customdomains->data->pullzones as $zone) {

                        if ($zone->name . '.' . $this->_config['alias'] . '.' . W3TC_CDN_NETDNA_URL === $this->_config['domain'][0]) {
                            try {
                                $params = array('file' => '/' . $local_path);

                                $file_purge = json_decode($api->delete('/zones/pull.json/' . $zone->id . '/cache', $params));

                                if(preg_match("(200|201)", $customdomains->code)) {
                                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
                                } else {
                                    if(preg_match("(401|500)", $file_purge->code)) {
                                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $file_purge->code . '. Please check your alias, consumer key, and private key.');
                                    } else {
                                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $file_purge->code);
                                    }
                                }

                                $found_domain = true;
                            } catch (CurlException $e) {
                                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, sprintf('Unable to purge (%s).', $e->getMessage()));
                            }
                        } else {
                            $domain_is_valid++;
                        }
                    }
                }
            } else {
                if (preg_match("(401|500)", $customdomains->code)) {
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $customdomains->code . '. Please check your alias, consumer key, and private key.');
                } else {
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $customdomains->code);
                }
            }

        } catch (CurlException $e) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, 'Failure to pull list of zones: ' . $e->getMessage());
        } 

        if ($domain_is_valid > 0 && !$found_domain) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'No zones matching custom domain.');
        }

        return !$this->_is_error($results);
    }

    /**
     * Purge CDN completely
     * @param $results
     * @return bool
     */
    function purge_all(&$results) {
        if (empty($this->_config['alias'])) {
            $results = $this->_get_results(array(), W3TC_CDN_RESULT_HALT, 'Empty Alias.');

            return false;
        }

        if (empty($this->_config['consumerkey'])) {
            $results = $this->_get_results(array(), W3TC_CDN_RESULT_HALT, 'Empty Consumer Key.');

            return false;
        }

        if (empty($this->_config['consumersecret'])) {
            $results = $this->_get_results(array(), W3TC_CDN_RESULT_HALT, 'Empty Consumer Secret.');

            return false;
        }

        if (!class_exists('NetDNA')) {
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
        }

        $api = new NetDNA($this->_config['alias'], $this->_config['consumerkey'], $this->_config['consumersecret']);

        $results = array();
        $local_path = $remote_path = '';
        $domain_is_valid = 0;
        $found_domain = false;

        try {
            $customdomains =  json_decode($api->get('/zones/pull.json'));

            if (preg_match("(200|201)", $customdomains->code)) {

                $local_path = 'all';
                $remote_path = 'all';

                $domain_is_valid = 0;
                $found_domain = false;

                foreach ($customdomains->data->pullzones as $zone) {

                    if ($zone->name . '.' . $this->_config['alias'] . '.' . W3TC_CDN_NETDNA_URL === $this->_config['domain'][0]) {
                        try {

                            $file_purge = json_decode($api->delete('/zones/pull.json/' . $zone->id . '/cache'));

                            if(preg_match("(200|201)", $customdomains->code)) {
                                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
                            } else {
                                if(preg_match("(401|500)", $file_purge->code)) {
                                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $file_purge->code . '. Please check your alias, consumer key, and private key.');
                                } else {
                                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $file_purge->code);
                                }
                            }

                            $found_domain = true;
                        } catch (CurlException $e) {
                            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, sprintf('Unable to purge (%s).', $e->getMessage()));
                        }
                    } else {
                        $domain_is_valid++;
                    }
                }
            } else {
                if (preg_match("(401|500)", $customdomains->code)) {
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $customdomains->code . '. Please check your alias, consumer key, and private key.');
                } else {
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Failed with error code ' . $customdomains->code);
                }
            }

        } catch (CurlException $e) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, 'Failure to pull list of zones: ' . $e->getMessage());
        }

        if ($domain_is_valid > 0 && !$found_domain) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'No zones matching custom domain.');
        }

        return !$this->_is_error($results);
    }

    /**
     * If the CDN supports fullpage mirroring
     * @return bool
     */
    function supports_full_page_mirroring() {
        return true;
    }
}
