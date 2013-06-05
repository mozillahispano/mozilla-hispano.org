<?php

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

/**
 * Class W3_Environment
 */
class W3_GenericAdminEnvironment {
    /*
     * Fixes environment
     * @param $config
     * @throws SelfTestExceptions
     **/
    function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();
        // create add-ins
        $this->create_required_files($config, $exs);

        // create folders
        $this->create_required_folders($exs);
        $this->add_index_to_folders();

        // create wp-loader file
        $wp_loader = w3_instance('W3_Environment_WpLoader');

        if ($wp_loader->should_create()) {
            try {
                $wp_loader->create();
            } catch (FilesystemOperationException $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) <= 0) {
            $this->notify_no_config_present($config, $exs);
            $this->notify_config_cache_not_writeable($config, $exs);
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        if ($event == 'activate') {
            delete_option('w3tc_request_data');
            add_option('w3tc_request_data', '', null, 'no');
        }
    }

    /**
     * Fixes environment after plugin deactivation
     * @return array
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        $this->delete_required_files($exs);

        delete_option('w3tc_request_data');

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns required rules for module
     * @return array
     */
    function get_required_rules($config) {
        return null;
    }

    /**
     * Checks if addins in wp-content is available and correct version.
     * @throws SelfTestExceptions
     */
    private function create_required_files($config, $exs) {
        $src = W3TC_INSTALL_FILE_ADVANCED_CACHE;
        $dst = W3TC_ADDIN_FILE_ADVANCED_CACHE;

        if (file_exists($dst)) {
            $script_data = @file_get_contents($dst);
            if ($script_data == @file_get_contents($src))
                return;
        }

        try {
            w3_wp_copy_file($src, $dst);
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }
    }

    /**
     * Checks if addins in wp-content are available and deletes them.
     * @throws SelfTestExceptions
     */
    private function delete_required_files($exs) {
        try {
            w3_wp_delete_file(W3TC_ADDIN_FILE_ADVANCED_CACHE);
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }
    }

    /**
     * Checks if addins in wp-content is available and correct version.
     * @throws SelfTestExceptions
     */
    private function create_required_folders($exs) {
        // folders that we create if not exists
        $directories = array(
            W3TC_CACHE_DIR,
            W3TC_CONFIG_DIR
        );

        foreach ($directories as $directory) {
            try{
                w3_wp_create_writeable_folder($directory, WP_CONTENT_DIR);
            } catch (FilesystemOperationException $ex) {
                $exs->push($ex);
            }
        }

        // folders that we delete if exists and not writeable
        $directories = array(
            W3TC_CACHE_CONFIG_DIR,
            W3TC_CACHE_TMP_DIR,
            W3TC_CACHE_BLOGMAP_FILENAME,
            W3TC_CACHE_DIR . '/object',
            W3TC_CACHE_DIR . '/db'
        );

        foreach ($directories as $directory) {
            try{
                if (file_exists($directory) && !is_writeable($directory))
                    w3_wp_delete_folder($directory);
            } catch (FilesystemRmdirException $ex) {
                $exs->push($ex);
            }
        }
    }

    /**
     * Adds index files
     */
    private function add_index_to_folders() {
        $directories = array(
            W3TC_CACHE_DIR,
            W3TC_CONFIG_DIR,
            W3TC_CACHE_CONFIG_DIR);
        $add_files = array();
        foreach ($directories as $dir) {
            if (is_dir($dir) && !file_exists($dir . '/index.html'))
                @file_put_contents($dir . '/index.html', '');
        }
    }

    /**
     * Check config file
     */
    private function notify_no_config_present($config, $exs) {
        if ($config->own_config_exists() 
                && $config->get_integer('common.instance_id', 0) != 0)
            return;

        $onclick = 'document.location.href=\'' . 
            addslashes(wp_nonce_url(
                'admin.php?page=w3tc_general&w3tc_save_options')) . 
            '\';';
        $button = '<input type="button" class="button w3tc" ' .
            'value="save the settings" onclick="' . $onclick . '" />';

        $exs->push(new SelfTestFailedException('<strong>W3 Total Cache:</strong> ' .
            'Default settings are in use. The configuration file could ' .
            'not be read or doesn\'t exist. Please ' . $button . 
            ' to create the file.'));
    }

    /**
     * Check config cache is in sync with config
     **/
    private function notify_config_cache_not_writeable($config, $exs) {
        try {
            $config->validate_cache_actual();
        } catch (Exception $ex) {
            // we could just create cache folder, so try again
            $config->load();
            try {
                $config->validate_cache_actual();
            } catch (Exception $ex) {
                $exs->push(new SelfTestFailedException(
                    '<strong>W3 Total Cache Error:</strong> ' .
                    $ex->getMessage()));
            }
        }
    }
}