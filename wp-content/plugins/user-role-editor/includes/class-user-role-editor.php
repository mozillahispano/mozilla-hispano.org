<?php
/*
 * main class of User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * License: GPL v3
 * 
*/

class User_Role_Editor {
	// common code staff, including options data processor
  protected $lib = null;
  
  public $key_capability = 'not allowed';
	
    /**
     * class constructor
     */
    function __construct($library) 
    {
        
        // activation action
        register_activation_hook(URE_PLUGIN_FULL_PATH, array(&$this, 'setup'));

        // deactivation action
        register_deactivation_hook(URE_PLUGIN_FULL_PATH, array(&$this, 'cleanup'));

        // get plugin specific library object
        $this->lib = $library;
		
        // Who may use this plugin
        $this->key_capability = $this->lib->get_key_capability();
        
        if ($this->lib->multisite) {
            // new blog may be registered not at admin back-end only but automatically after new user registration, e.g. 
            // Gravity Forms User Registration Addon does
            add_action( 'wpmu_new_blog', array( &$this, 'duplicate_roles_for_new_blog'), 10, 2 );
        }
        
        if (!is_admin()) {
            return;
        }
        
        add_action('admin_init', array(&$this, 'plugin_init'), 1);

        // Add the translation function after the plugins loaded hook.
        add_action('plugins_loaded', array(&$this, 'load_translation'));

        // add own submenu 
        add_action('admin_menu', array(&$this, 'plugin_menu'));
      		
        if ($this->lib->multisite) {
            // add own submenu 
            add_action('network_admin_menu', array(&$this, 'network_plugin_menu'));
        }


        // add a Settings link in the installed plugins page
        add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);

        add_filter('plugin_row_meta', array(&$this, 'plugin_row_meta'), 10, 2);
        
    }
    // end of __construct()

    
    /**
   * Plugin initialization
   * 
   */
  public function plugin_init() {

    global $current_user;

    if (!empty($current_user->ID)) {
      $user_id = $current_user->ID;
    } else {
      $user_id = 0;
    }

    // these filters and actions should prevent editing users with administrator role
    // by other users with URE_KEY_CAPABILITY capability    
    if (!$this->lib->user_is_admin($user_id)) {
      // Exclude administrator role from edit list.
      add_filter('editable_roles', array( &$this, 'exclude_admin_role' ) );      
      // prohibit any actions with user who has Administrator role
      add_filter('user_has_cap', array( &$this, 'not_edit_admin' ), 10, 3);
      // exclude users with 'Administrator' role from users list
      add_action('pre_user_query', array( &$this, 'exclude_administrators' ) );
      // do not show 'Administrator (s)' view above users list
      add_filter('views_users',  array( &$this, 'exclude_admins_view' ) );            
    }
    
    add_action( 'admin_enqueue_scripts', array( &$this, 'admin_load_js' ) );
    add_action( 'user_row_actions', array( &$this, 'user_row'), 10, 2 );
    add_action( 'edit_user_profile', array(&$this, 'edit_user_profile'), 10, 2 );
    add_filter( 'manage_users_columns', array(&$this, 'user_role_column'), 10, 5 );
    add_filter( 'manage_users_custom_column', array(&$this, 'user_role_row'), 10, 3 );
    add_action( 'profile_update', array(&$this, 'user_profile_update'), 10 );

    
    if ($this->lib->multisite) {
      add_filter( 'all_plugins', array( &$this, 'exclude_from_plugins_list' ) );    
      $allow_edit_users_to_not_super_admin = $this->lib->get_option('allow_edit_users_to_not_super_admin', 0);
      if ($allow_edit_users_to_not_super_admin) {
          add_filter( 'map_meta_cap', array($this, 'restore_users_edit_caps'), 1, 4 );
          remove_all_filters( 'enable_edit_any_user_configuration' );
          add_filter( 'enable_edit_any_user_configuration', '__return_true');
          add_filter( 'admin_head', array($this, 'edit_user_permission_check'), 1, 4 );
      }
    }
    
  }
  // end of plugin_init()
    
  
  /**
   * restore edit_users, delete_users, create_users capabilities for non-superadmin users under multisite
   * (code is provided by http://wordpress.org/support/profile/sjobidoo)
   * 
   * @param type $caps
   * @param type $cap
   * @param type $user_id
   * @param type $args
   * @return type
   */
  public function restore_users_edit_caps($caps, $cap, $user_id, $args) {

        foreach ($caps as $key => $capability) {

            if ($capability != 'do_not_allow')
                continue;

            switch ($cap) {
                case 'edit_user':
                case 'edit_users':
                    $caps[$key] = 'edit_users';
                    break;
                case 'delete_user':
                case 'delete_users':
                    $caps[$key] = 'delete_users';
                    break;
                case 'create_users':
                    $caps[$key] = $cap;
                    break;
            }
        }

        return $caps;
    }
    // end of restore_user_edit_caps()
    
    
    /**
     * Checks that both the editing user and the user being edited are
     * members of the blog and prevents the super admin being edited.
     * (code is provided by http://wordpress.org/support/profile/sjobidoo)
     * 
     */
    function edit_user_permission_check() {
        global $current_user, $profileuser;

        $screen = get_current_screen();

        get_currentuserinfo();

        if ($screen->base == 'user-edit' || $screen->base == 'user-edit-network') { // editing a user profile
            if (!is_super_admin($current_user->ID) && is_super_admin($profileuser->ID)) { // trying to edit a superadmin while himself is less than a superadmin
                wp_die(__('You do not have permission to edit this user.'));
            } elseif (!( is_user_member_of_blog($profileuser->ID, get_current_blog_id()) && is_user_member_of_blog($current_user->ID, get_current_blog_id()) )) { // editing user and edited user aren't members of the same blog
                wp_die(__('You do not have permission to edit this user.'));
            }
        }
    }
    // end of edit_user_permission_check()
    

  /**
   * exclude administrator role from the roles list
   * 
   * @param string $roles
   * @return array
   */
  public function exclude_admin_role($roles) 
  {

    if (isset($roles['administrator'])) {
      unset($roles['administrator']);
    }

    return $roles;
  }
  // end of exclude_admin_role()

  
  /**
     * We have two vulnerable queries with user id at admin interface, which should be processed
     * 1st: http://blogdomain.com/wp-admin/user-edit.php?user_id=ID&wp_http_referer=%2Fwp-admin%2Fusers.php
     * 2nd: http://blogdomain.com/wp-admin/users.php?action=delete&user=ID&_wpnonce=ab34225a78
     * If put Administrator user ID into such request, user with lower capabilities (if he has 'edit_users')
     * can edit, delete admin record
     * This function removes 'edit_users' capability from current user capabilities
     * if request has admin user ID in it
     *
     * @param array $allcaps
     * @param type $caps
     * @param string $name
     * @return array
     */
    public function not_edit_admin($allcaps, $caps, $name) 
    {

        $user_keys = array('user_id', 'user');
        foreach ($user_keys as $user_key) {
            $access_deny = false;
            $user_id = $this->lib->get_request_var($user_key, 'get');
            if (!empty($user_id)) {
                if ($user_id == 1) {  // built-in WordPress Admin
                    $access_deny = true;
                } else {
                    if (!isset($this->lib->user_to_check[$user_id])) {
                        // check if user_id has Administrator role
                        $access_deny = $this->lib->has_administrator_role($user_id);
                    } else {
                        // user_id was checked already, get result from cash
                        $access_deny = $this->lib->user_to_check[$user_id];
                    }
                }
                if ($access_deny) {
                    unset($allcaps['edit_users']);
                }
                break;
            }
        }

        return $allcaps;
    }
    // end of not_edit_admin()

    
    /**
     * add where criteria to exclude users with 'Administrator' role from users list
     * 
     * @global wpdb $wpdb
     * @param  type $user_query
     */
    public function exclude_administrators($user_query) 
    {

        global $wpdb;

		$result = false;
		$links_to_block = array('profile.php', 'users.php');
		foreach ( $links_to_block as $key => $value ) {
			$result = stripos($_SERVER['REQUEST_URI'], $value);
			if ( $result !== false ) {
				break;
			}
		}

		if ( $result===false ) {	// block the user edit stuff only
			return;
		}
				
        // get user_id of users with 'Administrator' role  
        $tableName = (!$this->lib->multisite && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
        $meta_key = $wpdb->prefix . 'capabilities';
        $admin_role_key = '%"administrator"%';
        $query = "select user_id
              from $tableName
              where meta_key='$meta_key' and meta_value like '$admin_role_key'";
        $ids_arr = $wpdb->get_col($query);
        if (is_array($ids_arr) && count($ids_arr) > 0) {
            $ids = implode(',', $ids_arr);
            $user_query->query_where .= " AND ( $wpdb->users.ID NOT IN ( $ids ) )";
        }
    }
    // end of exclude_administrators()
	
    
    /*
     * Exclude view of users with Administrator role
     * 
     */
    public function exclude_admins_view($views) {

        unset($views['administrator']);

        return $views;
    }
    // end of exclude_admins_view()

    
  /**
   * Add/hide edit actions for every user row at the users list
   * 
   * @global type $pagenow
   * @global type $current_user
   * @param string $actions
   * @param type $user
   * @return string
   */
  public function user_row($actions, $user) 
  {

    global $pagenow, $current_user;

    if ($pagenow == 'users.php') {				
		if ($current_user->has_cap($this->key_capability)) {
          $actions['capabilities'] = '<a href="' . 
                  wp_nonce_url("users.php?page=users-".URE_PLUGIN_FILE."&object=user&amp;user_id={$user->ID}", "ure_user_{$user->ID}") . 
                  '">' . __('Capabilities', 'ure') . '</a>';
        }      
    }

    return $actions;
  }
  // end of user_row()

  
    /**
   * every time when new blog created - duplicate to it roles from the main blog (1)  
   * @global wpdb $wpdb
   * @global WP_Roles $wp_roles
   * @param int $blog_id
   * @param int $user_id
   *
   */
  public function duplicate_roles_for_new_blog($blog_id) 
  {
  
    global $wpdb, $wp_roles;
    
    // get Id of 1st (main) blog
    $main_blog_id = $this->lib->get_main_blog_id();
    if ( empty($main_blog_id) ) {
      return;
    }
    $current_blog = $wpdb->blogid;
    switch_to_blog( $main_blog_id );
    $main_roles = new WP_Roles();  // get roles from primary blog
    $default_role = get_option('default_role');  // get default role from primary blog
    switch_to_blog($blog_id);  // switch to the new created blog
    $main_roles->use_db = false;  // do not touch DB
    $main_roles->add_cap('administrator', 'dummy_123456');   // just to save current roles into new blog
    $main_roles->role_key = $wp_roles->role_key;
    $main_roles->use_db = true;  // save roles into new blog DB
    $main_roles->remove_cap('administrator', 'dummy_123456');  // remove unneeded dummy capability
    update_option('default_role', $default_role); // set default role for new blog as it set for primary one
    switch_to_blog($current_blog);  // return to blog where we were at the begin
  }
  // end of duplicate_roles_for_new_blog()

  
  /** 
   * Filter out URE plugin from not superadmin users
   * @param type array $plugins plugins list
   * @return type array $plugins updated plugins list
   */
  public function exclude_from_plugins_list($plugins) 
  {
    
    // if multi-site, then allow plugin activation for network superadmins and, if that's specially defined, - for single site administrators too    
    if (is_super_admin() || (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE==1)) {    
      return $plugins;
    }

    // exclude URE from plugins list
    foreach ($plugins as $key => $value) {
      if ($key == 'user-role-editor/'.URE_PLUGIN_FILE) {
        unset($plugins[$key]);
      }
    }

    return $plugins;
  }
  // end of exclude_from_plugins_list()

  
    /**
     * Load plugin translation files - linked to the 'plugins_loaded' action
     * 
     */
    function load_translation() 
    {

        load_plugin_textdomain('ure', '', dirname( plugin_basename( URE_PLUGIN_FULL_PATH ) ) .'/lang');
        
    }
    // end of ure_load_translation()

    /**
     * Modify plugin actions link
     * 
     * @param array $links
     * @param string $file
     * @return array
     */
    public function plugin_action_links($links, $file) 
    {

        if ($file == plugin_basename(dirname(URE_PLUGIN_FULL_PATH).'/'.URE_PLUGIN_FILE)) {
            $settings_link = "<a href='options-general.php?page=settings-".URE_PLUGIN_FILE."'>" . __('Settings', 'ure') . "</a>";
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    // end of plugin_action_links()


    public function plugin_row_meta($links, $file) {

        if ($file == plugin_basename(dirname(URE_PLUGIN_FULL_PATH) .'/'.URE_PLUGIN_FILE)) {
            $links[] = '<a target="_blank" href="http://role-editor.com/changelog">' . __('Changelog', 'ure') . '</a>';
        }

        return $links;
    }

    // end of plugin_row_meta
    

    public function plugin_menu() {

        if (function_exists('add_submenu_page')) {
            $ure_page = add_submenu_page('users.php', __('User Role Editor', 'ure'), __('User Role Editor', 'ure'), 
                    $this->key_capability, 'users-'.URE_PLUGIN_FILE, array(&$this, 'edit_roles'));
            add_action("admin_print_styles-$ure_page", array(&$this, 'admin_css_action'));
        }

        if (!$this->lib->multisite) {
			add_options_page(
				 esc_html__('User Role Editor', 'ure'), 
				 esc_html__('User Role Editor', 'ure'), 
				 $this->key_capability, 'settings-'.URE_PLUGIN_FILE, array(&$this, 'settings'));
        }
    }
    // end of plugin_menu()

    
	public function network_plugin_menu() {

		if (is_multisite()) {
			add_submenu_page('settings.php', __('User Role Editor', 'ure'), __('User Role Editor', 'ure'), 
                    $this->key_capability, 'settings-'.URE_PLUGIN_FILE, array(&$this, 'settings'));
		}
		
	}
	// end of network_plugin_menu()
    

	public function settings() {
        if (isset($_POST['user_role_editor_settings_update'])) {  // process update from the options form
            $nonce = $_POST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'user-role-editor')) {
                wp_die('Security check');
            }

			if (defined('URE_SHOW_ADMIN_ROLE') && (URE_SHOW_ADMIN_ROLE==1) ) {
                $show_admin_role = 1;
			} else {
				$show_admin_role = $this->lib->get_request_var('show_admin_role', 'checkbox');
			}
            $this->lib->put_option('show_admin_role', $show_admin_role);
            
            $caps_readable = $this->lib->get_request_var('caps_readable', 'checkbox');
            $this->lib->put_option('ure_caps_readable', $caps_readable);
            
            $show_deprecated_caps = $this->lib->get_request_var('show_deprecated_caps', 'checkbox');
            $this->lib->put_option('ure_show_deprecated_caps', $show_deprecated_caps);
            
            if ($this->lib->multisite) {
                $allow_edit_users_to_not_super_admin = $this->lib->get_request_var('allow_edit_users_to_not_super_admin', 'checkbox');
                $this->lib->put_option('allow_edit_users_to_not_super_admin', $allow_edit_users_to_not_super_admin);
            }
            
            do_action('ure_settings_update');
            
            $this->lib->flush_options();            
            $this->lib->show_message(__('User Role Editor options are updated', 'ure'));
        } else { // get options from the options storage
            
            if (defined('URE_SHOW_ADMIN_ROLE') && (URE_SHOW_ADMIN_ROLE==1) ) {
                $show_admin_role = 1;
            } else {
                $show_admin_role = $this->lib->get_option('show_admin_role', 0);
            }
            $caps_readable = $this->lib->get_option('ure_caps_readable', 0);
            $show_deprecated_caps = $this->lib->get_option('ure_show_deprecated_caps', 0);
            if ($this->lib->multisite) {
                $allow_edit_users_to_not_super_admin = $this->lib->get_option('allow_edit_users_to_not_super_admin', 0);
            }
            do_action('ure_settings_load');
        }

        if (is_multisite()) {
            $link = 'settings.php';
        } else {
            $link = 'options-general.php';
        }
        require_once(URE_PLUGIN_DIR . 'includes/settings-template.php');
                        
    }
    // end of settings()


    public function admin_css_action() {

        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style('ure_admin_css', URE_PLUGIN_URL . 'css/ure-admin.css', array(), false, 'screen');
    }
    // end of admin_css_action()
    
    
    // call roles editor page
    public function edit_roles() {

        global $current_user;

        if (!empty($current_user)) {
            $user_id = $current_user->ID;
        } else {
            $user_id = false;
        }
        if (!$this->lib->user_is_admin($user_id)) {
            if (is_multisite()) {
                $admin = 'SuperAdministrator';
            } else {
                $admin = 'Administrator';
            }
            die(__('Only', 'ure') . ' ' . $admin . ' ' . __('is allowed to use', 'ure') . ' ' . 'User Role Editor');
        }

        $this->lib->editor();
    }
    // end of edit_roles()
	
   
	// move old version option to the new storage 'user_role_editor' option, array, containing all URE options
	private function convert_option($option_name) {
		
		$option_value = get_option($option_name, 0);
		delete_option($option_name);
		$this->lib->put_option( $option_name, $option_value );
		
	}

	/**
	 *  execute on plugin activation
	 */
	function setup() {
		
		$this->convert_option('ure_caps_readable');				
		$this->convert_option('ure_show_deprecated_caps');
		$this->convert_option('ure_hide_pro_banner');		
		$this->lib->flush_options();
		
		$this->lib->make_roles_backup();
  
	}
	// end of setup()

 
 /**
  * Load plugin javascript stuff
  * 
  * @param string $hook_suffix
  */
 public function admin_load_js($hook_suffix){
    
     if (class_exists('User_Role_Editor_Pro')) {
         $ure_hook_suffix = 'users_page_users-user-role-editor-pro';
     } else {
         $ure_hook_suffix = 'users_page_users-user-role-editor';
     }
	if ($hook_suffix===$ure_hook_suffix) {
    wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core','jquery-ui-button', 'jquery') );
    wp_register_script( 'ure-js', plugins_url( '/js/ure-js.js', URE_PLUGIN_FULL_PATH ) );
    wp_enqueue_script ( 'ure-js' );
    wp_localize_script( 'ure-js', 'ure_data', array(
        'wp_nonce' => wp_create_nonce('user-role-editor'),          
        'page_url' => URE_WP_ADMIN_URL . URE_PARENT .'?page=users-'.URE_PLUGIN_FILE,  
        'is_multisite' => is_multisite() ? 1 : 0,  
        'select_all' => __('Select All', 'ure'),
        'unselect_all' => __('Unselect All', 'ure'),
        'reverse' => __('Reverse', 'ure'),  
        'update' => __('Update', 'ure'),
        'confirm_submit' => __('Please confirm permissions update', 'ure'),
        'add_new_role_title' => __('Add New Role', 'ure'),
        'role_name_required' => __(' Role name (ID) can not be empty!', 'ure'),  
        'role_name_valid_chars' => __(' Role name (ID) must contain latin characters, digits, hyphens or underscore only!', 'ure'),  
        'add_role' => __('Add Role', 'ure'),
        'delete_role' => __('Delete Role', 'ure'),
        'cancel' =>  __('Cancel', 'ure'),  
        'add_capability' => __('Add Capability', 'ure'),
        'delete_capability' => __('Delete Capability', 'ure'),
        'reset' => __('Reset', 'ure'),  
        'reset_warning' => __('Reset Roles to WordPress defaults. Be careful, all changes made by you or plugins will be lost. Some plugins, e.g. S2Member, WooCommerce reactivation could be needed. Continue?', 'ure'),  
        'default_role' => __('Default Role', 'ure'),    
        'set_new_default_role' => __('Set New Default Role', 'ure'),
        'delete_capability' => __('Delete Capability', 'ure'),
        'delete_capability_warning' => __('Warning! Be careful - removing critical capability could crash some plugin or other custom code', 'ure'),
        'capability_name_required' => __(' Capability name (ID) can not be empty!', 'ure'),    
        'capability_name_valid_chars' => __(' Capability name (ID) must contain latin characters, digits, hyphens or underscore only!', 'ure'),    
    ) );
    // load additional JS stuff for Pro version, if exists
    do_action('ure_load_js');
	}
  
}
// end of admin_load_js()



    public function edit_user_profile($user) {

        global $current_user, $wp_roles;

        $result = stripos($_SERVER['REQUEST_URI'], 'network/user-edit.php');
        if ($result !== false) {  // exit, this code just for single site user profile only, not for network admin center
            return;
        }
        if (!$this->lib->user_is_admin($current_user->ID)) {
            return;
        }
?>
        <h3><?php _e('User Role Editor', 'ure'); ?></h3>
        <table class="form-table">
        		<tr>
        			<th scope="row"><?php _e('Other Roles', 'ure'); ?></th>
        			<td>
        <?php
        $roles = $this->lib->other_user_roles($user);
        if (is_array($roles) && count($roles) > 0) {
            foreach ($roles as $role) {
                echo '<input type="hidden" name="ure_other_roles[]" value="' . $role . '" />';
            }
        }
        $output = $this->lib->roles_text($roles);
        echo $output . '&nbsp;&nbsp;&gt;&gt;&nbsp;<a href="' . wp_nonce_url("users.php?page=users-".URE_PLUGIN_FILE."&object=user&amp;user_id={$user->ID}", "ure_user_{$user->ID}") . '">' . __('Edit', 'ure') . '</a>';
        ?>
        			</td>
        		</tr>
        </table>		
        <?php
        /*
          <script type="text/javascript">
          jQuery('#role').attr('disabled', 'disabled');
          </script>
         */
        ?>
        <?php
    }

// end of ure_edit_user_profile()

    /**
     *  add 'Other Roles' column to WordPress users list table
     * 
     * @param array $columns WordPress users list table columns list
     * @return array
     */
    public function user_role_column($columns = array()) {

        $columns['ure_roles'] = __('Other Roles', 'ure');

        return $columns;
    }
    // end of user_role_column()

    
    /**
     * Return user's roles list for display in the WordPress Users list table
     *
     * @param string $retval
     * @param string $column_name
     * @param int $user_id
     *
     * @return string all user roles
     */
    public function user_role_row($retval = '', $column_name = '', $user_id = 0) 
    {

        // Only looking for User Role Editor other user roles column
        if ('ure_roles' == $column_name) {
            $user = get_userdata($user_id);
            // Get the users roles
            $roles = $this->lib->other_user_roles($user);
            $retval = $this->lib->roles_text($roles);
        }

        // Pass retval through
        return $retval;
    }
    // end of user_role_row()
    

    // save additional user roles when user profile is updated, as WordPress itself doesn't know about them
    public function user_profile_update($user_id) {

        if (!current_user_can('edit_user', $user_id)) {
            return;
        }
        $user = get_userdata($user_id);

        if (isset($_POST['ure_other_roles'])) {
            $new_roles = array_intersect($user->roles, $_POST['ure_other_roles']);
            $skip_roles = array();
            foreach ($new_roles as $role) {
                $skip_roles['$role'] = 1;
            }
            unset($new_roles);
            foreach ($_POST['ure_other_roles'] as $role) {
                if (!isset($skip_roles[$role])) {
                    $user->add_role($role);
                }
            }
        }
                
    }
    // update_user_profile()

    

    // execute on plugin deactivation
    function cleanup() 
    {
		
    }
    // end of setup()

 
}
// end of User_Role_Editor
