<?php

/*
 * Constant definitions for use in WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * 
*/

// general for any plugin
define('URE_WP_ADMIN_URL', admin_url());
define('URE_ERROR', 'Error is encountered');

// specific for User Role Editor plugin only
define('URE_PRO_VERSION', 0);

define('URE_SPACE_REPLACER', '_URE-SR_');
define('URE_PARENT', is_network_admin() ? 'network/users.php':'users.php');
define('URE_KEY_CAPABILITY', 'administrator');