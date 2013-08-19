<?php
/*
 * General stuff for usage at WordPress plugins
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * 
*/


/**
 * This class contains general stuff for usage at WordPress plugins
 */
class Garvs_WP_Lib {

  private static $instance = null; // object exemplar reference  
  protected $options_id = ''; // identifire to save/retrieve plugin options to/from wp_option DB table
  protected $options = array(); // plugin options data
  
  public $log_to_file = false;  // set to true in order to record data about critical actions to log file
  private $log_file_name = '';  // file name to write log messages
  
  /**
   * class constructor
   * @param string $option_name   identifire to save/retrieve plugin options to/from wp_option DB table
   */
  public function __construct($option_name) {
    
    $this->init_options($option_name);

    add_action('admin_notices', array(&$this, 'show_message'));
    
  }
  // end of __construct()

  
  /**
   * get current options for this plugin
   */
  protected function init_options($options_id) {
    $this->options_id = $options_id;
    $this->options = get_option($options_id);
  }

  /**
   * Return HTML formatted message
   * 
   * @param string $message   message text
   * @param string $error_style message div CSS style
   */
  public function show_message($message, $error_style=false) {
  
    if ($message) {
      if ($error_style) {
        echo '<div id="message" class="error" >';
      } else {
        echo '<div id="message" class="updated fade">';
      }
      echo $message . '</div>';
    }

  }
  // end of show_message()
  

  /**
   * Returns value by name from GET/POST/REQUEST. Minimal type checking is provided
   * 
   * @param string $var_name  Variable name to return
   * @param string $request_type  type of request to process get/post/request (default)
   * @param string $var_type  variable type to provide value checking
   * @return mix variable value from request
   */
  public function get_request_var($var_name, $request_type='request', $var_type='string') {

    $result = 0;
    if ($request_type == 'get') {
      if (isset($_GET[$var_name])) {
        $result = $_GET[$var_name];
      }
    } else if ($request_type == 'post') {
      if (isset($_POST[$var_name])) {
        if ($var_type!='checkbox') {
          $result = $_POST[$var_name];
        } else {
          $result = 1;
        }
      }
    } else {
      if (isset($_REQUEST[$var_name])) {
          $result = $_REQUEST[$var_name];
      }
    }

    if ($result) {
      if ($var_type == 'int' && !is_numeric($result)) {
        $result = 0;
      }
      if ($var_type!='int') {
        $result = esc_attr($result);
      }
    }
    
    return $result;
  }
  // end of get_request_var
 

  /**
   * returns option value for option with name in $option_name
   */
  public function get_option($option_name, $default = false) {
    
    if ( isset( $this->options[ $option_name ] ) ) {
      return $this->options[$option_name];
    } else {
      return $default;
    }
    
  }
  // end of get_option()


  /**
   * puts option value according to $option_name option name into options array property
   */
  public function put_option($option_name, $option_value, $flush_options=false) {
    
    $this->options[$option_name] = $option_value;
    if ($flush_options) {
      $this->flush_options();
    }
    
  }
  // end of put_option()

  
  /**
   * saves options array into WordPress database wp_options table
   */ 
  public function flush_options() {
    
    update_option($this->options_id, $this->options);
    
  }
  // end of flush_options()

  
  /**
   * Check product versrion and stop execution if product version is not compatible
   * @param type $must_have_version
   * @param type $version_to_check
   * @param type $error_message
   * @return type
   */
  public static function check_version($must_have_version, $version_to_check, $error_message, $plugin_file_name) {

    if (version_compare( $must_have_version, $version_to_check, '<' ) ) {
      if ( is_admin() && ( !defined('DOING_AJAX') || !DOING_AJAX ) ) {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
        deactivate_plugins( $plugin_file_name );
        wp_die( $error_message );
      } else {
        return;
      }
    }
  }
  // end of check_version()
    
  
  /**
   * returns 'selected' HTML cluster if $value matches to $etalon
   * 
   * @param string $value
   * @param string $etalon
   * @return string
   */
  public function option_selected($value, $etalon) {
    $selected = '';
    if (strcasecmp($value, $etalon) == 0) {
      $selected = 'selected="selected"';
    }

    return $selected;
  }
  // end of option_selected()
  
  
//
}
// end of Garvs_WP_Lib class

?>