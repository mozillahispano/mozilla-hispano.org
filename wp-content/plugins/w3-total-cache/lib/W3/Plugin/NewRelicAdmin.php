<?php
/**
 * W3 NewRelicAdmin plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');

/**
 * Class W3_Plugin_NewRelicAdmin
 */
class W3_Plugin_NewRelicAdmin extends W3_Plugin {

    /**
     * Called on plugin instantiation
     */
    function run() {
        add_filter('w3tc_compatibility_test', array($this, 'verify_compatibility'));
        if (is_admin()) {
            add_action('wp_ajax_admin_w3tc_verify_newrelic_api_key', array($this, 'verify_newrelic_api_key'));
            add_action('wp_ajax_w3tc_verify_newrelic_api_key', array($this, 'verify_newrelic_api_key'));
            add_action('wp_ajax_admin_w3tc_get_newrelic_applications', array($this, 'get_newrelic_applications'));
            add_action('wp_ajax_w3tc_get_newrelic_applications', array($this, 'get_newrelic_applications'));
        }
    }

    /**
     * Activate plugin action (called by W3_Plugins)
     */
    function activate() {}

    /**
     * Deactivate plugin action (called by W3_Plugins)
     */
    function deactivate() {}

    /**
     * Returns a list of the verification status of the the new relic requirements. To be used on the compability page
     * @param $verified_list
     * @return array
     */
    function verify_compatibility($verified_list) {
        $nerser = w3_instance('W3_NewRelicService');
        $nr_verified = $nerser->verify_compatibility();
        $verified_list[] = '<strong>New Relic</strong>';
        foreach($nr_verified as $criteria => $result)
            $verified_list[] = sprintf("$criteria: %s", $result);
        return $verified_list;
    }

    /**
     * Retrieve the new relic account id. Used in AJAX requests.
     * Requires request param api_key with the API key
     */
    function verify_newrelic_api_key() {
        $api_key = W3_Request::get_string('api_key');
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');
        try {
            $account_id = $nerser->get_account_id($api_key);
            if ($account_id) {
                $this->_config->set('newrelic.account_id', $account_id);
                $this->_config->save();
                echo $account_id;
            }
        }catch (Exception $ex) {}
        die();
    }

    /**
     * Retrieves applications. Used in AJAX requests.
     * Requires request param api_key with the API key and account_id with the Account id.
     */
    function get_newrelic_applications() {
        w3_require_once(W3TC_LIB_W3_DIR . '/NewRelicService.php');
        $api_key = W3_Request::get_string('api_key');
        $account_id = W3_Request::get_string('account_id');
        if ($api_key == '0') {
            $config_master = new W3_Config(true);
            $api_key = $config_master->get_string('newrelic.api_key');
        }
        $nerser = new W3_NewRelicService($api_key);
        $newrelic_applications = array();
        try {
            if(empty($account_id) || $account_id == '')
                $account_id = $nerser->get_account_id();
            $newrelic_applications = $nerser->get_applications($account_id);
        } catch (Exception $ex) {}
        echo json_encode($newrelic_applications);
        die();
    }


    /**
     * Writes rules to file cache .htaccess
     *
     * @return boolean
     */
    function write_rules_core() {
        $path = w3_get_new_relic_rules_core_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_NEW_RELIC_CORE);
        $replace_end = strpos($data, W3TC_MARKER_END_NEW_RELIC_CORE);

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

        if (!$rules)
            return;

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
        w3_wp_write_to_file($path, $data);
    }

    /**
     * Erases Minify core directives
     *
     * @param string $data
     * @return string
     */
    function erase_rules_core($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_NEW_RELIC_CORE, W3TC_MARKER_END_NEW_RELIC_CORE);

        return $data;
    }

    /**
     * Removes NewRelic core directives
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
     * Generates rules
     *
     * @return string
     */
    function generate_rules_core() {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->generate_rules_core_apache();
        }

        return false;
    }

    /**
     * Generates rules
     *
     * @return string
     */
    function generate_rules_core_apache() {
        $new_relic_app_name = $this->_config->get_string('newrelic.appname');

        $rules = '';
        if ($new_relic_app_name) {
            $rules .= W3TC_MARKER_BEGIN_NEW_RELIC_CORE . "\n";
            $rules .= sprintf('php_value newrelic.appname \'%s\'', $new_relic_app_name) . "\n";
            $rules .= W3TC_MARKER_END_NEW_RELIC_CORE . "\n";
        }
        return $rules;
    }


    /**
     * @return array
     */
    function get_required_rules() {
        $rewrite_rules = array();
        $newrelic_core_path = w3_get_minify_rules_core_path();
        $rewrite_rules[] = array('filename' => $newrelic_core_path, 'content'  => $this->generate_rules_core());

        return $rewrite_rules;
    }

    /**
     * Check if core rules exists
     *
     * @return boolean
     */
    function check_rules_has_core() {
        $path = w3_get_new_relic_rules_core_path();

        return (($data = @file_get_contents($path)) && w3_has_rules(w3_clean_rules($data), W3TC_MARKER_BEGIN_NEW_RELIC_CORE, W3TC_MARKER_END_NEW_RELIC_CORE));
    }
}