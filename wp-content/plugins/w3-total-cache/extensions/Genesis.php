<?php

class W3_Genesis {
    /**
     * Request URI
     * @var string
     */
    private $_request_uri = '';
    /**
     * @var W3_Config $_config
     */
    private $_config;

    function run() {
        add_action('w3tc_register_fragment_groups', array($this, 'register_groups'));
        $this->_config = w3_instance('W3_Config');
        if ($this->_config->get_boolean('fragmentcache.enabled')) {
            if (!is_admin()) {
                /**
                 * Register the caching of content to specific hooks
                 */
                foreach(array('genesis_header', 'genesis_footer', 'genesis_sidebar', 'genesis_loop', 'wp_head', 'wp_footer', 'genesis_comments', 'genesis_pings') as $hook) {
                    add_action($hook, array($this, 'cache_genesis_start'),-999999999);
                    add_action($hook, array($this, 'cache_genesis_end'), 999999999);
                }
                foreach(array('genesis_do_subnav', 'genesis_do_nav') as $filter) {
                    add_filter($filter, array($this, 'cache_genesis_filter_start'),-999999999);
                    add_filter($filter, array($this, 'cache_genesis_filter_end'), 999999999);
                }
            }

            /**
             * Since posts pages etc are cached individually need to be able to flush just those and not all fragment
             */
            add_action('clean_post_cache', array($this, 'flush_post_fragment'), 1);

            $this->_request_uri = $_SERVER['REQUEST_URI'];
        }
    }

    /**
     * Start outputbuffering or return fragment on a per page/hook basis
     */
    function cache_genesis_start() {
        $hook = current_filter();
        $keys = $this->_get_id_group($hook);
        if (is_null($keys))
            return;
        list($id, $group) = $keys;
        w3tc_fragmentcache_start($id, $group, $hook);
    }

    /**
     * Store the output buffer per page/post hook basis.
     */
    function cache_genesis_end() {
        $keys = $this->_get_id_group(current_filter());
        if (is_null($keys))
            return;
        list($id, $group) = $keys;
        w3tc_fragmentcache_end($id, $group, $this->_config->get_boolean('fragmentcache.debug'));
    }

    /**
     * Start filter buffering and return filter result
     */
    function cache_genesis_filter_start($data) {
        $hook = current_filter();
        $keys = $this->_get_id_group($hook, strpos($data,'current')!==false);
        if (is_null($keys))
            return $data;
        list($id, $group) = $keys;
        return w3tc_fragmentcache_filter_start($id, $group, $hook, $data);
    }

    /**
     * Store the filter result and return filter result.
     */
    function cache_genesis_filter_end($data) {
        $keys = $this->_get_id_group(current_filter(), strpos($data,'current')!==false);
        if (is_null($keys))
            return $data;
        list($id, $group) = $keys;
        return w3tc_fragmentcache_filter_end($id, $group, $data);
    }

    /**
     * Constructs the fragment grouping for a subgroup
     * @param $subgroup
     * @param $state
     * @return string
     */
    private function _genesis_group($subgroup, $state = false) {
        $postfix = '';
        if ($state && is_user_logged_in())
            $postfix = 'logged_in_';
        return ($subgroup ? "genesis_fragment_{$subgroup}_" : 'genesis_fragment_') . $postfix;
    }

    /**
     * Constructs the correct fragment group and id for the hook
     * @param $hook
     * @param bool $current_menu
     * @return array|null
     */
    private function _get_id_group($hook, $current_menu = false) {
        if (is_user_logged_in() && w3tc_get_extension_config('genesis.theme', 'fragment_reject_logged_roles')) {

            $current_user = wp_get_current_user();
            $roles  = w3tc_get_extension_config('genesis.theme', 'fragment_reject_roles');
            if (empty($roles))
                return true;

            $hooks = w3tc_get_extension_config('genesis.theme', 'fragment_reject_logged_roles_on_actions');

            foreach($roles as $role) {
                if ($hooks && current_user_can($role) && in_array($hook, $hooks)) {
                    return null ;
                }
            }
        }

        $group = $hook;
        if (strpos($hook, 'sidebar')) {
            $genesis_id = $hook;
            $group = 'sidebar';
        } elseif ($hook == 'genesis_loop') {
            if (is_front_page()) {
                $group = 'loop_front_page';
                if (is_paged()) {
                    global $wp_query;
                    $page = $wp_query->query_vars['paged'];
                    $genesis_id = "{$page}_$hook";
                } else
                    $genesis_id = $hook;
            } else {
                $group = 'loop_single';

                if (is_paged()) {
                    global $wp_query;
                    $page = $wp_query->query_vars['paged'];
                    $genesis_id = get_the_ID(). "_{$page}_$hook";
                } else
                    $genesis_id = get_the_ID(). "_$hook";
            }
        } elseif ($hook == 'genesis_comments' || $hook == 'genesis_pings'){
            if ($hook == 'genesis_comments' && is_user_logged_in())
                return null;

            $group = 'loop_single_' . $hook;
            if (is_paged()) {
                global $wp_query;
                $page = $wp_query->query_vars['paged'];
                $genesis_id = get_the_ID(). "_{$page}_$hook";
            } else
                $genesis_id = get_the_ID()."_$hook";
        } elseif (strpos($hook, '_nav') && $current_menu) {
            if (is_front_page()) {
                $genesis_id = 0;
            } else {
                $genesis_id = get_the_ID();
            }
        } else
            $genesis_id = $hook;

        if ($this->_cache_group($group) && !$this->_exclude_page($group)) {
            return array($genesis_id, $this->_genesis_group($group, true));
        }
        return null;
    }

    /**
     * Checks if the fragment group should be cached
     *
     * @param $group
     * @return array|bool|int|null|string
     */
    private function _cache_group($group) {
        return w3tc_get_extension_config('genesis.theme', $group);
    }

    /**
     * Checks if current page is excluded from caching
     *
     * @param $group
     * @return bool
     */
    private function _exclude_page($group) {
        $reject_uri  = w3tc_get_extension_config('genesis.theme', "{$group}_excluded");
        if ($reject_uri)
            $reject_uri = explode("\n", $reject_uri);

        if (is_null($reject_uri) || !is_array($reject_uri) || empty($reject_uri)) {
            return false;
        }

        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );
        foreach ($auto_reject_uri as $uri) {
            if (strstr($this->_request_uri, $uri) !== false) {
                return true;
            }
        }

        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $this->_request_uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register the various fragments groups to be used. no_action is used since fragments requires actions.
     */
    function register_groups() {
        //blog specific group and an array of actions that will trigger a flush of the group
        $groups = array (
            $this->_genesis_group('') => array(
                'clean_post_cache', 
                'update_option_sidebars_widgets', 
                'wp_update_nav_menu_item'),
            $this->_genesis_group('sidebar') => array(
                'update_option_sidebars_widgets'),
            $this->_genesis_group('loop_single') => array(
                'no_action'),
            $this->_genesis_group('loop_front_page') => array(
                'clean_post_cache')
        );
        foreach($groups as $group => $actions)
            w3tc_register_fragment_group($group, $actions, 3600);
    }

    /**
     * Flush the fragments connected to a post id
     * @param $post_ID
     */
    function flush_post_fragment($post_ID) {
        /**
         * @var W3_SharedPageUrls $W3_SharedPageUrls
         */
        $W3_SharedPageUrls = w3_instance('W3_SharedPageUrls');
        $urls = $W3_SharedPageUrls->get_post_urls($post_ID);
        $hooks = array('genesis_loop', 'genesis_comments', 'genesis_pings');
        foreach($hooks as $hook) {
            w3tc_fragmentcache_flush_fragment("{$post_ID}_$hook", $this->_genesis_group('loop_single_logged_in'));
            w3tc_fragmentcache_flush_fragment("{$post_ID}_$hook", $this->_genesis_group('loop_single'));
            for($page = 0; $page<=sizeof($urls); $page++) {
                w3tc_fragmentcache_flush_fragment("{$post_ID}_$hook", $this->_genesis_group('loop_single_logged_in'));
                w3tc_fragmentcache_flush_fragment("{$post_ID}_$hook", $this->_genesis_group('loop_single'));
            }
        }
    }
}

$ext = new W3_Genesis();
$ext->run();