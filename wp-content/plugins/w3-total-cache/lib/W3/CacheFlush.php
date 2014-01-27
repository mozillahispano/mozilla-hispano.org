<?php

/**
 * W3 Cache flushing
 */

/**
 * Class W3_PgCacheFlush
 */
class W3_CacheFlush {

    /**
     * PHP5 Constructor
     */
    function __construct() {
        $config = w3_instance('W3_Config');
        $sns = $config->get_boolean('cluster.messagebus.enabled');

        if ($sns)
            $this->_executor = w3_instance('W3_Enterprise_SnsClient');
        else
            $this->_executor = w3_instance('W3_CacheFlushLocal');
    }

    /**
     * Flushes database cache
     */
    function dbcache_flush() {
        $this->_executor->dbcache_flush();
    }
    
    /**
     * Flushes minify cache
     */
    function minifycache_flush() {
        $this->_executor->minifycache_flush();
    }
    
    /**
     * Flushes object cache
     */
    function objectcache_flush() {
        $this->_executor->objectcache_flush();
    }

    /**
     * Flushes fragment cache
     */
    function fragmentcache_flush() {
        $this->_executor->fragmentcache_flush();
    }


    /**
     * Flushes fragment cache based on group
     */
    function fragmentcache_flush_group($group, $global = false) {
        $this->_executor->fragmentcache_flush_group($group, $global);
    }

    /**
     * Updates Browser Query String
     */
    function browsercache_flush() {
        $this->_executor->browsercache_flush();
    }  

    /**
     * Purges CDN files
     */
    function cdn_purge_files($purgefiles) {
        $this->_executor->cdn_purge_files($purgefiles);
    }  

    /**
     * Flushes page cache
     *
     * @return boolean
     */
    function pgcache_flush() {
        $this->_executor->pgcache_flush();
    }

    /**
     * Flushes post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function pgcache_flush_post($post_id) {
        return $this->_executor->pgcache_flush_post($post_id);
    }

    /**
     * Flushes post cache
     *
     * @param string $url
     * @return boolean
     */
    function pgcache_flush_url($url) {
        return $this->_executor->pgcache_flush_url($url);
    }

    /**
     * Purges varnish cache
     * @return mixed
     */
    function varnish_flush() {
        return $this->_executor->varnish_flush();
    }

    /**
     * Purges post from varnish cache
     * @param $post_id
     * @return mixed
     */
    function varnish_flush_post($post_id) {
        return $this->_executor->varnish_flush_post($post_id);
    }

    /**
     * Purges url from varnish cache
     * @param string $url
     * @return mixed
     */
    function varnish_flush_url($url) {
        return $this->_executor->varnish_flush_url($url);
    }

    /**
     * Purge CDN mirror cache
     */
    function cdncache_purge() {
        return $this->_executor->cdncache_purge();
    }

    /**
     * Purges post from CDN mirror cache
     * @param $post_id
     * @return boolean
     */
    function cdncache_purge_post($post_id) {
        return $this->_executor->cdncache_purge_post($post_id);
    }

    /**
     * Purges post from CDN mirror cache
     * @param $url
     * @return boolean
     */
    function cdncache_purge_url($url) {
        return $this->_executor->cdncache_purge_url($url);
    }

    /**
     * Clears the system APC
     * @return mixed
     */
    function apc_system_flush() {
        return $this->_executor->apc_system_flush();
    }

    /**
     * Reloads/compiles a PHP file.
     * @param string $filename
     * @return mixed
     */
    function apc_reload_file($filename) {
        return $this->_executor->apc_reload_file($filename);
    }

    /**
     * Reloads/compiles a PHP file.
     * @param string $filenames
     */
    function apc_reload_files($filenames) {
        $this->_executor->apc_reload_files($filenames);
    }

    /**
     * Deletes files based on regular expression matching.
     * @param string $mask
     * @return bool
     */
    function apc_delete_files_based_on_regex($mask) {
        return $this->_executor->apc_delete_files_based_on_regex($mask);
    }

    /**
     * Purges/Flushes post from page caches, varnish and cdncache
     */
    function flush_post($post_id) {
        static $flushed_posts = array();

        if (!in_array($post_id, $flushed_posts)) {
            $flushed_posts[] = $post_id;
            return $this->_executor->flush_post($post_id);
        }
        return true;
    }

    /**
     * Purges/Flushes page caches, varnish and cdncache
     */
    function flush() {
        static $flushed = false;
        if (!$flushed) {
            $flushed = true;
            return $this->_executor->flush();
        }
        return true;
    }

    /**
     * Purges/Flushes url from page caches, varnish and cdncache
     */
    function flush_url($url) {
        static $flushed_urls = array();

        if (!in_array($url, $flushed_urls)) {
            $flushed_urls[] = $url;
            return $this->_executor->flush_url($url);
        }
        return true;
    }

    /**
     * Makes get request to url specific to post, ie permalinks
     * @param $post_id
     * @return boolean
     */
    function prime_post($post_id) {
        return $this->_executor->prime_post($post_id);
    }
}
