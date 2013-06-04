<?php

/**
 * Class W3_ConfigData
 */
class W3_ConfigData {
    /*
     * Normalized data
     * @var array
     */
    public $data = array();

    /*
     * Array of config keys descriptors. In a format of
     * <key> => array('type' => <key type>, 'default' => <default value>)
     * 
     * @var array
     */
    private $_keys;

    /*
     * Maps http key to options key.
     * Fixes problem when php replaces 'my.super_option' to 'my_super_option'
     * <http name> => <config name>
     * 
     * @var array
     */
    private $_http_keys_map;
    
    /**
     * Constructor
     */
    function __construct($keys) {
        $this->data = array('version' => W3TC_VERSION);
        $this->_keys = $keys;
        
        $this->_http_keys_map = array();
        foreach (array_keys($keys) as $key) {
            $http_key = str_replace('.', '_', $key);
            $this->_http_keys_map[$http_key] = $key;
            // add also non-escaped key
            $this->_http_keys_map[$key] = $key;
        }
    }
    
    /*
     * Converts configuration key returned in http _GET/_POST
     * to configuration key
     * 
     * @param $http_key string
     * @return string
     */
    function resolve_http_key($http_key) {
        if (!isset($this->_http_keys_map[$http_key]))
            return null;
        
        return $this->_http_keys_map[$http_key];
    }
    
    /*
     * Removes data
     */
    function clear() {
      $this->data = array('version' => W3TC_VERSION);
    }

    /**
     * Sets config value
     *
     * @param string $key
     * @param string $value
     * @return value set
     */
    function set($key, $value) {
        if (!array_key_exists($key, $this->_keys))
            return null;
        
        $type = $this->_keys[$key]['type'];
        if (!($type == 'array' && is_string($value)))
            settype($value, $type);
        else {
            $value = str_replace("\r\n", "\n", $value);
            $value = explode("\n", $value);
        }

        
        $this->data[$key] = $value;

        return $value;
    }
    
    /**
     * Sets default values
     */
    function set_defaults() {
        foreach ($this->_keys as $key => $value)
            $this->data[$key] = $value['default'];
    }

    /**
     * Sets group of keys
     * 
     * @param $data array
     */
    function set_group($data) {
        foreach ($data as $key => $value)
            $this->set($key, $value);
    }

    /**
     * Reads config from file and returns it's content as array (or null)
     *
     * @param string $filename
     * @return array or null
     */
    function get_array_from_file($filename) {

        if (file_exists($filename) && is_readable($filename)) {
            // include errors not hidden by @ since they still terminate
            // process (code not functonal), but hides reason why
            $config = include $filename;

            if (is_array($config)) {
                return $config;
            }
        }

        return null;
    }

    /**
     * Reads config from file using "set" method to fill object with data.
     *
     * @param string $filename
     * @return boolean
     */
    function read($filename) {
        $config = $this->get_array_from_file($filename);
        if (is_null($config))
            return false;
        
        foreach ($config as $key => $value)
            $this->set($key, $value);

        return true;
    }

    /**
     * Saves modified config
     */
    function write($filename) {
        $config = "<?php\r\n\r\nreturn array(\r\n";
        foreach ($this->data as $key => $value)
            $config .= $this->_write_item(1, $key, $value);
        $config .= ");";
        return @$this->file_put_contents_atomic($filename, $config);
    }
    
    
    /**
     * Writes array item to file
     *
     * @param int $tabs
     * @param string $key
     * @param mixed $value
     * @return string
     */
    private function _write_item($tabs, $key, $value) {
        $item = str_repeat("\t", $tabs);

        if (is_numeric($key) && (string)(int)$key === (string)$key) {
            $item .= sprintf("%d => ", $key);
        } else {
            $item .= sprintf("'%s' => ", addcslashes($key, "'\\"));
        }

        switch (gettype($value)) {
            case 'object':
            case 'array':
                $item .= "array(\r\n";
                foreach ((array)$value as $k => $v) {
                    $item .= $this->_write_item($tabs + 1, $k, $v);
                }
                $item .= sprintf("%s),\r\n", str_repeat("\t", $tabs));
                return $item;

            case 'integer':
                $data = (string)$value;
                break;

            case 'double':
                $data = (string)$value;
                break;

            case 'boolean':
                $data = ($value ? 'true' : 'false');
                break;

            case 'NULL':
                $data = 'null';
                break;

            default:
            case 'string':
                $data = "'" . addcslashes($value, "'\\") . "'";
                break;
        }

        $item .= $data . ",\r\n";

        return $item;
    }

    /**
     * @param $filename
     * @param $content
     * @return bool
     */
    function file_put_contents_atomic($filename, $content) {
        if (is_dir(W3TC_CACHE_TMP_DIR) && is_writable(W3TC_CACHE_TMP_DIR)) {
            $temp = tempnam(W3TC_CACHE_TMP_DIR, 'temp');
        } else {
            trigger_error("file_put_contents_atomic() : error writing temporary file to '" . W3TC_CACHE_TMP_DIR . "'", E_USER_WARNING);
            return false;
        }

        $chmod = 0644;
        if (defined('FS_CHMOD_FILE'))
            $chmod = FS_CHMOD_FILE;
        @chmod($temp, $chmod);

        if (!($f = @fopen($temp, 'wb'))) {
            if (file_exists($temp))
                @unlink($temp);
           trigger_error("file_put_contents_atomic() : error writing temporary file '$temp'", E_USER_WARNING);
           return false;
        }

        fwrite($f, $content);
        fclose($f);

        if (!@rename($temp, $filename)) {
            @unlink($filename);
            @rename($temp, $filename);
        }

        if (file_exists($temp))
            @unlink($temp);

        $chmod = 0644;
        if (defined('FS_CHMOD_FILE'))
            $chmod = FS_CHMOD_FILE;
        @chmod($filename, $chmod);
        return true;
    }
}
