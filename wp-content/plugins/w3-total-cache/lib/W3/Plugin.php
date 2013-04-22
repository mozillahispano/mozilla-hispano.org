<?php

/**
 * W3 Plugin base class
 */

/**
 * Class W3_Plugin
 */
class W3_Plugin {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * PHP5 Constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
    }

    /**
     * Runs plugin
     */
    function run() {
    }

    /**
     * Get the corresponding Admin plugin for the module
     * @return null|W3_Plugin
     */
    function get_admin() {
        return null;
    }

    /**
     * Activate plugin action (called by W3_Plugins)
     * @return mixed
     */
    function activate() {
    }

    /**
     * Deactivate plugin action (called by W3_Plugins)
     * @return mixed
     */
    function deactivate() {
    }
}
