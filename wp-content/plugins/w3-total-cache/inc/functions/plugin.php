<?php

/**
 * W3 Total Cache plugins API
 */

/**
 * Add W3TC action callback
 *
 * @param string $action
 * @param mixed $callback
 * @return void
 */
function w3tc_add_action($action, $callback) {
    $GLOBALS['_w3tc_actions'][$action][] = $callback;
}

/**
 * Do W3TC action
 *
 * @param string $action
 * @param mixed $value
 * @return mixed
 */
function w3tc_do_action($action, $value = null) {
    if (isset($GLOBALS['_w3tc_actions'][$action])) {
        foreach ((array) $GLOBALS['_w3tc_actions'][$action] as $callback) {
            if (is_callable($callback)) {
                $value = call_user_func($callback, $value);
            }
        }
    }

    return $value;
}

/**
 * Shortcut for page cache flush
 *
 * @return boolean
 */
function w3tc_pgcache_flush() {
    $w3_pgcache = w3_instance('W3_PgCacheFlush');
    return $w3_pgcache->flush();
}

/**
 * Shortcut for page post cache flush
 *
 * @param integer $post_id
 * @return boolean
 */
function w3tc_pgcache_flush_post($post_id) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->pgcache_flush_post($post_id);
}

/**
 * Shortcut for url page cache flush
 *
 * @param string $url
 * @return boolean
 */
function w3tc_pgcache_flush_url($url) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->pgcache_flush_url($url);
}

/**
 * Shortcut for database cache flush
 *
 * @return boolean
 */
function w3tc_dbcache_flush() {
    $w3_db = w3_instance('W3_DbCache');
    return $w3_db->flush_cache();
}

/**
 * Shortcut for minify cache flush
 *
 * @return boolean
 */
function w3tc_minify_flush() {
    $w3_minify = w3_instance('W3_Minify');

    return $w3_minify->flush();
}

/**
 * Shortcut for objectcache cache flush
 *
 * @return boolean
 */
function w3tc_objectcache_flush() {
    $w3_objectcache = w3_instance('W3_ObjectCache');
    return $w3_objectcache->flush();
}

/**
 * Shortcut for CDN cache post purge
 * @param $post_id
 * @return mixed
 */
function w3tc_cdncache_purge_post($post_id) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdncache_purge_post($post_id);
}

/**
 * Shortcut for CDN cache url purge
 * @param string $url
 * @return mixed
 */
function w3tc_cdncache_purge_url($url) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdncache_purge_url($url);
}

/**
 * Shortcut for CDN cache purge
 * @return mixed
 */
function w3tc_cdncache_purge() {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdncache_purge();
}

/**
 * Shortcut for CDN purge files
 * @param array $files Array consisting of uri paths (i.e wp-content/uploads/image.pnp)
 * @return mixed
 */
function w3tc_cdn_purge_files($files) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdn_purge_files($files);
}

/**
 * Prints script tag for scripts group
 *
 * @param string $location
 * @retun void
 */
function w3tc_minify_script_group($location) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    $w3_plugin_minify->printed_scripts[] = $location;

    echo $w3_plugin_minify->get_script_group($location);
}

/**
 * Prints style tag for styles group
 *
 * @param string $location
 * @retun void
 */
function w3tc_minify_style_group($location) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    $w3_plugin_minify->printed_styles[] = $location;

    echo $w3_plugin_minify->get_style_group($location);
}

/**
 * Prints script tag for custom scripts
 *
 * @param string|array $files
 * @param boolean $blocking
 * @return void
 */
function w3tc_minify_script_custom($files, $blocking = true) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    echo $w3_plugin_minify->get_script_custom($files, $blocking);
}

/**
 * Prints style tag for custom styles
 *
 * @param string|array $files
 * @param boolean $import
 * @return void
 */
function w3tc_minify_style_custom($files, $import = false) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    echo $w3_plugin_minify->get_style_custom($files, $import);
}

/**
 * @param string $fragment_group
 * @param boolean $global If group is for whole network in MS install
 * @return mixed
 */
function w3tc_fragmentcache_flush_group($fragment_group, $global = false) {
    $w3_fragmentcache = w3_instance('W3_CacheFlush');
    return $w3_fragmentcache->fragmentcache_flush_group($fragment_group, $global);
}

/**
 * Flush all fragment groups
 * @return mixed
 */
function w3tc_fragmentcache_flush() {
    $w3_fragmentcache = w3_instance('W3_CacheFlush');
    return $w3_fragmentcache->fragmentcache_flush();
}

/**
 * Register a fragment group and connected actions for current blog
 * @param string $group
 * @param array $actions on which actions group should be flushed
 * @return mixed
 */
function w3tc_register_fragment_group($group, $actions) {
    $w3_fragmentcache = w3_instance('W3_Pro_Plugin_FragmentCache');
    return $w3_fragmentcache->register_group($group, $actions);
}

/**
 * Register a fragment group for whole network in MS install
 * @param $group
 * @param $actions
 * @return mixed
 */
function w3tc_register_fragment_global_group($group, $actions) {
    $w3_fragmentcache = w3_instance('W3_Pro_Plugin_FragmentCache');
    return $w3_fragmentcache->register_global_group($group, $actions);
}

/**
 * Shortcut for varnish flush
 *
 * @return boolean
 */
function w3tc_varnish_flush() {
    $w3_pgcache = w3_instance('W3_CacheFlush');
    return $w3_pgcache->varnish_flush();
}

/**
 * Shortcut for post varnish flush
 *
 * @param integer $post_id
 * @return boolean
 */
function w3tc_varnish_flush_post($post_id) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->varnish_flush_post($post_id);
}

/**
 * Shortcut for url varnish flush
 *
 * @param string $url
 * @return boolean
 */
function w3tc_varnish_flush_url($url) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->varnish_flush_url($url);
}


/**
 * Deletes files.
 *
 * @param string $mask regular expression matching files to be deleted
 * @param bool $http if delete request should be made over http to current site. Default false.
 * @return mixed
 */
function w3tc_apc_delete_files_based_on_regex($mask, $http = false) {
    if (!$http) {
        $w3_cacheflush = w3_instance('W3_CacheFlush');

        return $w3_cacheflush->apc_delete_files_based_on_regex($mask);
    } else {
        $url = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/apc.php';
        $path = parse_url($url, PHP_URL_PATH);
        $post = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'body' => array( 'nonce' => wp_hash($path), 'command' => 'delete_files', 'regex' => $mask),
        );
        $result = wp_remote_post($url, $post);
        if (is_wp_error($result)) {
            return $result;
        } elseif ($result['response']['code'] != '200') {
            return $result['response']['code'];
        }

        return true;
    }
}

/**
 * Reloads files.
 * @param string[] $files list of files supports, fullpath, from root, wp-content
 * @param bool $http if delete request should be made over http to current site. Default false.
 * @return mixed
 */
function w3tc_apc_reload_files($files, $http = false) {

    if (!$http) {
        $w3_cacheflush = w3_instance('W3_CacheFlush');

        return $w3_cacheflush->apc_reload_files($files);
    } else {
        $url = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/apc.php';
        $path = parse_url($url, PHP_URL_PATH);

        $post = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'body' => array( 'nonce' => wp_hash($path), 'command' => 'reload_files', 'files' => $files),
        );
        $result = wp_remote_post($url, $post);
        if (is_wp_error($result)) {
            return $result;
        } elseif ($result['response']['code'] != '200') {
            return $result['response']['code'];
        }

        return true;
    }
}

/**
 * Use w3tc_get_themes() to get a list themenames to use with user agent groups
 * @param $group_name
 * @param string $theme the themename default is default theme. For childtheme it should be parentthemename/childthemename
 * @param string $redirect
 * @param array $agents Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.
 * @param bool $enabled
 */
function w3tc_save_user_agent_group($group_name, $theme = 'default', $redirect = '', $agents = array(), $enabled = false) {
    /**
     * @var $w3_mobile W3_Mobile
     */
    $w3_mobile = w3_instance('W3_Mobile');
    $w3_mobile->save_group($group_name, $theme, $redirect, $agents, $enabled);
}

/**
 * @param $group
 */
function w3tc_delete_user_agent_group($group) {
    /**
     * @var $w3_mobile W3_Mobile
     */
    $w3_mobile = w3_instance('W3_Mobile');
    $w3_mobile->delete_group($group);

}

/**
 * @param $group
 * @return mixed
 */
function w3tc_get_user_agent_group($group) {
    /**
     * @var $w3_mobile W3_Mobile
     */
    $w3_mobile = w3_instance('W3_Mobile');
    return $w3_mobile->get_group_values($group);
}

/**
 * Use w3tc_get_themes() to get a list themenames to use with referrer groups
 * @param $group_name
 * @param string $theme the themename default is default theme. For childtheme it should be parentthemename/childthemename
 * @param string $redirect
 * @param array $referrers Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.
 * @param bool $enabled
 */
function w3tc_save_referrer_group($group_name, $theme = 'default', $redirect = '', $referrers = array(), $enabled = false) {
    /**
     * @var $w3_referrer W3_Referrer
     */
    $w3_referrer = w3_instance('W3_Referrer');
    $w3_referrer->save_group($group_name, $theme, $redirect, $referrers, $enabled);
}

/**
 * @param $group
 */
function w3tc_delete_referrer_group($group) {
    /**
     * @var $w3_referrer W3_Referrer
     */
    $w3_referrer = w3_instance('W3_Referrer');
    $w3_referrer->delete_group($group);
}

/**
 * @param $group
 * @return mixed
 */
function w3tc_get_referrer_group($group) {
    /**
     * @var $w3_mobile W3_Referrer
     */
    $w3_referrer = w3_instance('W3_Referrer');
    return $w3_referrer->get_group_values($group);
}