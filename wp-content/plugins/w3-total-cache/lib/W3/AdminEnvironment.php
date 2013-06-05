<?php

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

/**
 * Class W3_Environment
 */
class W3_AdminEnvironment {
    /*
     * Fixes environment
     * @param W3_Config $config
     * @throws SelfTestExceptions
     **/
    function fix_in_wpadmin($config, $force_all_checks = false) {
        $exs = new SelfTestExceptions();
        $fix_on_event = false;
        if (w3_is_multisite() && w3_get_blog_id() != 0) {
            if (get_transient('w3tc_config_changes') != ($md5_string = $config->get_md5() )) {
                $fix_on_event = true;
                set_transient('w3tc_config_changes', $md5_string, 3600);
            }
        }
        // call plugin-related handlers
        foreach ($this->get_handlers() as $h) {
            try {
                $h->fix_on_wpadmin_request($config, $force_all_checks);
                if ($fix_on_event) {
                    $this->fix_on_event($config, 'admin_request');
                }
            } catch (SelfTestExceptions $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        $exs = new SelfTestExceptions();

        // call plugin-related handlers
        foreach ($this->get_handlers() as $h) {
            try {
                $h->fix_on_event($config, $event);
            } catch (SelfTestExceptions $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment after plugin deactivation
     * @throws SelfTestExceptions
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        // call plugin-related handlers
        foreach ($this->get_handlers() as $h) {
            try {
                $h->fix_after_deactivation();
            } catch (SelfTestExceptions $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns an array[filename]=rules of rules for .htaccess or nginx files
     * @param W3_Config $config
     * @return array
     */
    public function get_required_rules($config) {
        $rewrite_rules_descriptors = array();

        foreach ($this->get_handlers() as $h) {
            $required_rules = $h->get_required_rules($config);

            if (!is_null($required_rules)) {
                foreach ($required_rules as $descriptor) {
                    $filename = $descriptor['filename'];
                    $content = isset($rewrite_rules_descriptors[$filename]) ? 
                        $rewrite_rules_descriptors[$filename]['content'] : '';
                    $rewrite_rules_descriptors[$filename] = array(
                        'filename' => $filename, 
                        'content' => $content . $descriptor['content']
                    );
                }
            }
        }

        ksort($rewrite_rules_descriptors);
        return $rewrite_rules_descriptors;
    }

    /**
     * Returns plugin-related environment handlers
     */
    private function get_handlers() {
        $a = array(
            w3_instance('W3_GenericAdminEnvironment'),
            w3_instance('W3_PgCacheAdminEnvironment'),
            w3_instance('W3_ObjectCacheAdminEnvironment'),
            w3_instance('W3_DbCacheAdminEnvironment'),
            w3_instance('W3_BrowserCacheAdminEnvironment'),
            w3_instance('W3_MinifyAdminEnvironment'),
            w3_instance('W3_CdnAdminEnvironment'),
            w3_instance('W3_NewRelicAdminEnvironment')
        );

        if (w3_is_pro() || w3_is_enterprise())
            array_push($a,
                w3_instance('W3_Pro_FragmentCacheAdminEnvironment'));
        
        return $a;
    }
}
