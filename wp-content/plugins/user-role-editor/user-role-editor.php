<?php
/*
Plugin Name: User Role Editor
Plugin URI: http://role-editor.com
Description: Change/add/delete WordPress user roles and capabilities.
Version: 4.4
Author: Vladimir Garagulya
Author URI: http://www.shinephp.com
Text Domain: ure
Domain Path: /lang/
*/

/*
Copyright 2010-2013  Vladimir Garagulya  (email: vladimir@shinephp.com)
*/

if (!function_exists("get_option")) {
  header('HTTP/1.0 403 Forbidden');
  die;  // Silence is golden, direct call is prohibited
}

define('URE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('URE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('URE_PLUGIN_FILE', basename(__FILE__));
define('URE_PLUGIN_FULL_PATH', __FILE__);

require_once(URE_PLUGIN_DIR.'includes/class-garvs-wp-lib.php');
require_once(URE_PLUGIN_DIR.'includes/class-ure-lib.php');

// check PHP version
$ure_required_php_version = '5.2.4';
$exit_msg = sprintf( 'User Role Editor requires PHP %s or newer.', $ure_required_php_version ) . 
                         '<a href="http://wordpress.org/about/requirements/"> ' . 'Please update!' . '</a>';
Ure_Lib::check_version( PHP_VERSION, $ure_required_php_version, $exit_msg, __FILE__ );

// check WP version
$ure_required_wp_version = '3.5';
$exit_msg = sprintf( 'User Role Editor requires WordPress %s or newer.', $ure_required_wp_version ) . 
                        '<a href="http://codex.wordpress.org/Upgrading_WordPress"> ' . 'Please update!' . '</a>';
Ure_Lib::check_version(get_bloginfo('version'), $ure_required_wp_version, $exit_msg, __FILE__ );

require_once(URE_PLUGIN_DIR .'includes/define-constants.php');
require_once(URE_PLUGIN_DIR .'includes/misc-support-stuff.php');
require_once( URE_PLUGIN_DIR .'includes/class-user-role-editor.php');


$ure_lib = new Ure_Lib('user-role-editor');
new User_Role_Editor($ure_lib);
