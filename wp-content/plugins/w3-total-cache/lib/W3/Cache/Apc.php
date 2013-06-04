<?php

/**
 * APC class
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Base.php');

/**
 * Class W3_Cache_Apc
 */
class W3_Cache_Apc extends W3_Cache_Base {

    /*
     * Used for faster flushing
     *
     * @var integer $_key_version
     */
    private $_key_version = array();

    /**
     * Adds data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function add($key, &$var, $expire = 0, $group = '0') {
        if ($this->get($key, $group) === false) {
            return $this->set($key, $var, $expire, $group);
        }

        return false;
    }

    /**
     * Sets data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function set($key, &$var, $expire = 0, $group = '0') {
        $key = $this->get_item_key($key);

        $var['key_version'] = $this->_get_key_version($group);

        return apc_store($key . '_' . $this->_blog_id, serialize($var), $expire);
    }

    /**
     * Returns data
     *
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return mixed
     */
    function get($key, $group = '0') {
        $key = $this->get_item_key($key);

        $v = @unserialize(apc_fetch($key . '_' . $this->_blog_id));
        if (!is_array($v) || !isset($v['key_version']))
            return null;

        $key_version = $this->_get_key_version($group);
        if ($v['key_version'] == $key_version)
            return $v;

        if ($v['key_version'] > $key_version) {
            $this->_set_key_version($v['key_version'], $group);
            return $v;
        }

        // key version is old
        if (!$this->_use_expired_data)
            return null;

        // if we have expired data - update it for future use and let
        // current process recalculate it
        $expires_at = isset($v['expires_at']) ? $v['expires_at'] : null;
        if ($expires_at == null || time() > $expires_at) {
            $v['expires_at'] = time() + 30;
            apc_store($key . '_' . $this->_blog_id, serialize($v), 0);

            return null;
        }

        // return old version
        return $v;
    }

    /**
     * Replaces data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function replace($key, &$var, $expire = 0, $group ='0') {
        if ($this->get($key, $group) !== false) {
            return $this->set($key, $var, $expire, $group);
        }

        return false;
    }

    /**
     * Deletes data
     *
     * @param string $key
     * @return boolean
     */
    function delete($key) {
        $key = $this->get_item_key($key);

        if ($this->_use_expired_data) {
            $v = @unserialize(apc_fetch($key . '_' . $this->_blog_id));
            if (is_array($v)) {
                $v['key_version'] = 0;
                apc_store($key . '_' . $this->_blog_id, serialize($v), 0);
                return true;
            }
        }

        return apc_delete($key . '_' . $this->_blog_id);
    }

    /**
     * Flushes all data
     *
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function flush($group = '0') {
        $this->_get_key_version($group);  // initialize $this->_key_version
        $this->_key_version[$group]++;
        $this->_set_key_version($this->_key_version[$group], $group);
        return true;
    }

    /**
     * Returns key postfix
     *
     * @param string $group Used to differentiate between groups of cache values
     * @return integer
     */
    private function _get_key_version($group = '0') {
        if (!isset($this->_key_version[$group]) || $this->_key_version[$group] <= 0) {
            $v = apc_fetch($this->_get_key_version_key($group));
            $v = intval($v);
            $this->_key_version[$group] = ($v > 0 ? $v : 1);
        }

        return $this->_key_version[$group];
    }

    /**
     * Sets new key version
     *
     * @param $v
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    private function _set_key_version($v, $group = '0') {
        apc_store($this->_get_key_version_key($group), $v, 0);
    }
}
