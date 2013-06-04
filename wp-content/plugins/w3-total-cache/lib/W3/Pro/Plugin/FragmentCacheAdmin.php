<?php
/**
 * W3 FragmentCache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_FragmentCacheAdmin
 */
class W3_Pro_Plugin_FragmentCacheAdmin extends W3_Plugin {

    function activate() {
        $this->schedule();
    }

    function deactivate() {
        $this->unschedule();
    }

    /**
     * Schedules events
     */
    function schedule() {
        if ($this->_config->get_boolean('fragmentcache.enabled') && $this->_config->get_string('fragmentcache.engine') == 'file') {
            if (!wp_next_scheduled('w3_fragmentcache_cleanup')) {
                wp_schedule_event(current_time('timestamp'), 'w3_fragmentcache_cleanup', 'w3_fragmentcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Unschedules events
     */
    function unschedule() {
        if (wp_next_scheduled('w3_fragmentcache_cleanup')) {
            wp_clear_scheduled_hook('w3_fragmentcache_cleanup');
        }
    }

    function cleanup() {
        $engine = $this->_config->get_string('fragmentcache.engine');

        switch ($engine) {
            case 'file':
                w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php');

                $w3_cache_file_cleaner = new W3_Cache_File_Cleaner(array(
                    'cache_dir' => w3_cache_blog_dir('fragment'),
                    'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
                ));

                $w3_cache_file_cleaner->clean();
                break;
        }
    }

    function get_required_rules() {
        return null;
    }
}
