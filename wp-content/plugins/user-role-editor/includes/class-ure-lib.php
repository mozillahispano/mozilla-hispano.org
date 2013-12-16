<?php
/*
 * Stuff specific for User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * 
*/


/**
 * This class contains general stuff for usage at WordPress plugins
 */
class Ure_Lib extends Garvs_WP_Lib {

	public $roles = null;     
	public $notification = '';   // notification message to show on page
	public $apply_to_all = 0; 
	public $user_to_check = array();  // cached list of user IDs, who has Administrator role     	 
  
	protected $capabilities_to_save = null; 
	protected $current_role = '';
	protected $wp_default_role = '';
	protected $current_role_name = '';  
	protected $user_to_edit = ''; 
	protected $show_deprecated_caps = false; 
	protected $caps_readable = false;
	protected $hide_pro_banner = false;	
	protected $full_capabilities = false;
	protected $ure_object = 'role';  // what to process, 'role' or 'user'  
	protected $role_default_html = '';
	protected $role_to_copy_html = '';
	protected $role_select_html = '';
	protected $role_delete_html = '';
	protected $capability_remove_html = '';
	protected $integrate_with_gravity_forms = false;
	protected $advert = null; 	
  
  
    /** class constructor
     * 
     * @param string $option_name
     * 
     */
    public function __construct($options_id) {
                                           
        parent::__construct($options_id);        
        
        $this->integrate_with_gravity_forms = class_exists('GFForms');
         
        
    }
    // end of __construct()
        
    
    /**
     * get options for User Role Editor plugin
     * User Role Editor stores its options at the main blog/site only and applies them to the all network
     * 
     */
    protected function init_options($options_id) {
        
        global $wpdb;

        $current_blog = $wpdb->blogid;
        if ($this->multisite && $current_blog!=$this->main_blog_id) {            
            switch_to_blog($this->main_blog_id);  // in order to get URE options from the main blog
        }
        
        $this->options_id = $options_id;
        $this->options = get_option($options_id);
        
        if ($this->multisite && $current_blog!=$this->main_blog_id) {
            // return back to the current blog
            restore_current_blog();
        }

    }
    // end of init_options()
    
    
    /**
     * saves options array into WordPress database wp_options table
     */
    public function flush_options() {

        global $wpdb;
        
        if ($this->multisite) {
            $current_blog = $wpdb->blogid;
            if ($current_blog!==$this->main_blog_id) {
                switch_to_blog($this->main_blog_id);  // in order to save URE options to the main blog
            }
        }
        
        update_option($this->options_id, $this->options);
        
        if ($this->multisite && $current_blog!==$this->main_blog_id) {            
            // return back to the current blog
            restore_current_blog();
        }
        
    }
    // end of flush_options()
    
    
    public function get_main_blog_id() {
        
        return $this->main_blog_id;
        
    }
    

    /**
     * return key capability to have access to User Role Editor Plugin
     * 
     * @return string
     */
    public function get_key_capability() {
        if (!$this->multisite) {
            $key_capability = URE_KEY_CAPABILITY;
        } else {
            $enable_simple_admin_for_multisite = $this->get_option('enable_simple_admin_for_multisite', 0);
            if ( (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE == 1) || 
                 $enable_simple_admin_for_multisite) {
                $key_capability = URE_KEY_CAPABILITY;
            } else {
                $key_capability = 'manage_network_users';
            }
        }
        
        return $key_capability;
    }
    // end of get_key_capability()
    

    /**
     *  return front-end according to the context - role or user editor
     */
    public function editor() {

        if (!$this->editor_init0()) {
            $this->show_message(__('Error: wrong request', 'URE'));
            return false;
        }                
        $this->process_user_request();
        $this->editor_init1();
        $this->show_editor();
        
    }
    // end of editor()

    
    protected function advertisement() {

        if (!class_exists('User_Role_Editor_Pro')) {
            $this->advert = new ure_Advertisement();
            $this->advert->display();
        }
    }
    // end of advertisement()

    
    protected function output_role_edit_dialogs() {
?>        
<script language="javascript" type="text/javascript">

  var ure_current_role = '<?php echo $this->current_role; ?>';

</script>

<!-- popup dialogs markup -->
<div id="ure_add_role_dialog" class="ure-modal-dialog" style="padding: 10px;">
  <form id="ure_add_role_form" name="ure_add_role_form" method="POST">    
    <div class="ure-label"><?php echo __('Role name (ID): ', 'ure'); ?></div>
    <div class="ure-input"><input type="text" name="user_role_id" id="user_role_id" size="25"/></div>
    <div class="ure-label"><?php echo __('Display Role Name: ', 'ure'); ?></div>
    <div class="ure-input"><input type="text" name="user_role_name" id="user_role_name" size="25"/></div>
    <div class="ure-label"><?php echo __('Make copy of: ', 'ure'); ?></div>
    <div class="ure-input"><?php echo $this->role_to_copy_html; ?></div>        
  </form>
</div>

<div id="ure_delete_role_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php _e('Select Role:', 'ure');?></div>
    <div class="ure-input"><?php echo $this->role_delete_html; ?></div>
  </div>
</div>


<div id="ure_default_role_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <?php echo $this->role_default_html; ?>
  </div>  
</div>


<div id="ure_delete_capability_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php _e('Delete:', 'ure');?></div>
    <div class="ure-input"><?php echo $this->capability_remove_html; ?></div>
  </div>  
</div>

<div id="ure_add_capability_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php echo __('Capability name (ID): ', 'ure'); ?></div>
    <div class="ure-input"><input type="text" name="capability_id" id="capability_id" size="25"/></div>
  </div>  
</div>     

<?php        
    do_action('ure_dialogs_html');
    }
    // end of output_role_edit_dialogs()
    

    protected function show_editor() {
    
    $this->show_message($this->notification);
?>
<div class="wrap">
		  <div id="ure-icon" class="icon32"><br/></div>
    <h2><?php _e('User Role Editor', 'ure'); ?></h2>
    <div id="poststuff">
        <div class="ure-sidebar" >
            <?php
            $this->advertisement();
?>            
        </div>

        <div class="has-sidebar" >
            <form id="ure_form" method="post" action="<?php echo URE_WP_ADMIN_URL . URE_PARENT.'?page=users-'.URE_PLUGIN_FILE;?>" >			
                <div id="ure_form_controls">				
<?php
                    wp_nonce_field('user-role-editor', 'ure_nonce');
                    settings_fields('ure-options');
                    if ($this->ure_object == 'user') {
                        require_once(URE_PLUGIN_DIR . 'includes/ure-user-edit.php');
                    } else {
                        $this->set_current_role();
                        $this->role_edit_prepare_html();
                        require_once(URE_PLUGIN_DIR . 'includes/ure-role-edit.php');
                    }
?>
                </div>      
            </form>		      
<?php	
	$this->advertise_pro_version();	
	
	if ($this->ure_object == 'role') {
        $this->output_role_edit_dialogs();
    }
?>
        </div>          
    </div>
</div>
<?php
        
    }
    // end of show_editor()
    

	// content of User Role Editor Pro advertisement slot - for direct call
	protected function advertise_pro_version() {
		if (class_exists('User_Role_Editor_Pro')) {
			return;
		}
?>		
			<div id="ure_pro_advertisement" style="clear:left;display:block; float: left;">
				<a href="http://role-editor.com?utm_source=UserRoleEditor&utm_medium=banner&utm_campaign=Plugins " target="_new" >
<?php 
	if ($this->hide_pro_banner) {
		echo 'User Role Editor Pro: extended functionality, no advertisement - from $29.</a>';
	} else {
?>
					<img src="<?php echo URE_PLUGIN_URL;?>images/user-role-editor-pro-728x90.jpg" alt="User Role Editor Pro" 
						 title="More functionality and premium support with Pro version of User Role Editor."/>
				</a><br />
				<label for="ure_hide_pro_banner">
					<input type="checkbox" name="ure_hide_pro_banner" id="ure_hide_pro_banner" onclick="ure_hide_pro_banner();"/>&nbsp;Thanks, hide this banner.
				</label>
<?php 
	}
?>
			</div>  			
<?php		
		
	}
	// end of user_role_editor()
	
	
    // validate information about user we intend to edit
    protected function check_user_to_edit() {

        if ($this->ure_object == 'user') {
            if (!isset($_REQUEST['user_id'])) {
                return false; // user_id value is missed
            }
            $user_id = $_REQUEST['user_id'];
            if (!is_numeric($user_id)) {
                return false;
            }
            if (!$user_id) {
                return false;
            }
            $this->user_to_edit = get_user_to_edit($user_id);
            if (empty($this->user_to_edit)) {
                return false;
            }
        }
        
        return true;
    }
    // end of check_user_to_edit()
    
    
    protected function init_current_role_name() {
        
        if (!isset($this->roles[$_POST['user_role']])) {
            $mess = __('Error: ', 'ure') . __('Role', 'ure') . ' <em>' . esc_html($_POST['user_role']) . '</em> ' . __('does not exist', 'ure');
            $this->current_role = '';
            $this->current_role_name = '';
        } else {
            $this->current_role = $_POST['user_role'];
            $this->current_role_name = $this->roles[$this->current_role]['name'];
            $mess = '';
        }
        
        return $mess;
        
    }
    // end of init_current_role_name()

    
    /**
     *  prepare capabilities from user input to save at the database
     */
    protected function prepare_capabilities_to_save() {
        $this->capabilities_to_save = array();
        foreach ($this->full_capabilities as $available_capability) {
            $cap_id = str_replace(' ', URE_SPACE_REPLACER, $available_capability['inner']);
            if (isset($_POST[$cap_id])) {
                $this->capabilities_to_save[$available_capability['inner']] = true;
            }
        }
    }
    // end of prepare_capabilities_to_save()
    

    /**
     *  save changes to the roles or user
     *  @param string $mess - notification message to the user
     *  @return string - notification message to the user
     */
    protected function permissions_object_update($mess) {

        if ($this->ure_object == 'role') {  // save role changes to database
            if ($this->update_roles()) {
                if ($mess) {
                    $mess .= '<br/>';
                }
                if (!$this->apply_to_all) {
                    $mess = __('Role is updated successfully', 'ure');
                } else {
                    $mess = __('Roles are updated for all network', 'ure');
                }
            } else {
                if ($mess) {
                    $mess .= '<br/>';
                }
                $mess = __('Error occured during role(s) update', 'ure');
            }
        } else {
            if ($this->update_user($this->user_to_edit)) {
                if ($mess) {
                    $mess .= '<br/>';
                }
                $mess = __('User capabilities are updated successfully', 'ure');
            } else {
                if ($mess) {
                    $mess .= '<br/>';
                }
                $mess = __('Error occured during user update', 'ure');
            }
        }
        return $mess;
    }
    // end of permissions_object_update()

    
    /**
     * Process user request
     */
    protected function process_user_request() {

        $this->notification = '';
        if (isset($_POST['action'])) {
            if (empty($_POST['ure_nonce']) || !wp_verify_nonce($_POST['ure_nonce'], 'user-role-editor')) {
                echo '<h3>Wrong nonce. Action prohibitied.</h3>';
                exit;
            }

            $action = $_POST['action'];
            
            if ($action == 'reset') {
                $this->reset_user_roles();
                exit;
            } else if ($action == 'add-new-role') {
                // process new role create request
                $this->notification = $this->add_new_role();
            } else if ($action == 'delete-role') {
                $this->notification = $this->delete_role();
            } else if ($action == 'change-default-role') {
                $this->notification = $this->change_default_role();
            } else if ($action == 'caps-readable') {
                if ($this->caps_readable) {
                    $this->caps_readable = 0;					
                } else {
                    $this->caps_readable = 1;
                }
                set_site_transient( 'ure_caps_readable', $this->caps_readable, 600 );
            } else if ($action == 'show-deprecated-caps') {
                if ($this->show_deprecated_caps) {
                    $this->show_deprecated_caps = 0;
                } else {
                    $this->show_deprecated_caps = 1;
                }
                set_site_transient( 'ure_show_deprecated_caps', $this->show_deprecated_caps, 600 );
            } else if ($action == 'hide-pro-banner') {
                $this->hide_pro_banner = 1;
                $this->put_option('ure_hide_pro_banner', 1);	
                $this->flush_options();				
            } else if ($action == 'add-new-capability') {
                $this->notification = $this->add_new_capability();
            } else if ($action == 'delete-user-capability') {
                $this->notification = $this->delete_capability();
            } else if ($action == 'roles_restore_note') {
                $this->notification = __('User Roles are restored to WordPress default values. ', 'ure');
            } else if ($action == 'update') {
                $this->roles = $this->get_user_roles();
                $this->init_full_capabilities();
                if (isset($_POST['user_role'])) {
                    $this->notification = $this->init_current_role_name();                    
                }
                $this->prepare_capabilities_to_save();
                $this->notification = $this->permissions_object_update($this->notification);
            } else {
                do_action('ure_process_user_request');
            } // if ($action
        }
        
    }
    // end of process_user_request()

	
	protected function set_apply_to_all() {
    if (isset($_POST['ure_apply_to_all'])) {
        $this->apply_to_all = 1;
    } else {
        $this->apply_to_all = 0;
    }
}
	// end of set_apply_to_all()
	

    protected function editor_init0() {
        $this->caps_readable = get_site_transient('ure_caps_readable');
        if (false === $this->caps_readable) {
            $this->caps_readable = $this->get_option('ure_caps_readable');
            set_site_transient('ure_caps_readable', $this->caps_readable, 600);
        }
        $this->show_deprecated_caps = get_site_transient('ure_show_deprecated_caps');
        if (false === $this->show_deprecated_caps) {
            $this->show_deprecated_caps = $this->get_option('ure_show_deprecated_caps');
            set_site_transient('ure_caps_readable', $this->caps_readable, 600);
        }

        $this->hide_pro_banner = $this->get_option('ure_hide_pro_banner', 0);
        $this->wp_default_role = get_option('default_role');

        // could be sent as by POST, as by GET
        if (isset($_REQUEST['object'])) {
            $this->ure_object = $_REQUEST['object'];
            if (!$this->check_user_to_edit()) {
                return false;
            }
        } else {
            $this->ure_object = 'role';
        }

        $this->set_apply_to_all();

        return true;
    }
    // end of editor_init0()


    protected function editor_init1() {

        if (!isset($this->roles) || !$this->roles) {
            // get roles data from database
            $this->roles = $this->get_user_roles();
        }

        $this->init_full_capabilities();

        if (!class_exists('User_Role_Editor_Pro')) {
            require_once(URE_PLUGIN_DIR . 'includes/class-advertisement.php');
        }
        
    }
    // end of editor_init1()


    /**
     * return id of role last in the list of sorted roles
     * 
     */
    protected function get_last_role_id() {
        
        // get the key of the last element in roles array
        $keys = array_keys($this->roles);
        $last_role_id = array_pop($keys);
        
        return $last_role_id;
    }
    // end of get_last_role_id()
    
    
    /**
     * Check if user has "Administrator" role assigned
     * 
     * @global wpdb $wpdb
     * @param int $user_id
     * @return boolean returns true is user has Role "Administrator"
     */
    public function has_administrator_role($user_id) {
        global $wpdb;

        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $table_name = (!$this->multisite && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
        $meta_key = $wpdb->prefix . 'capabilities';
        $query = "SELECT count(*)
                FROM $table_name
                WHERE user_id=$user_id AND meta_key='$meta_key' AND meta_value like '%administrator%'";
        $has_admin_role = $wpdb->get_var($query);
        if ($has_admin_role > 0) {
            $result = true;
        } else {
            $result = false;
        }
        // cache checking result for the future use
        $this->lib->user_to_check[$user_id] = $result;

        return $result;
    }

    // end of has_administrator_role()

  
    /**
     * Checks if user is allowed to user User Role Editor
     * 
     * @global int $current_user
     * @param int $user_id
     * @return boolean true 
     */
    public function user_is_admin($user_id = false) 
    {
        global $current_user;

        $ure_key_capability = $this->get_key_capability();
        if (empty($user_id)) {                    
            $user_id = $current_user->ID;
        }
        $result = user_can($user_id, $ure_key_capability);
        
        return $result;
/*        
 // Checks if user is superadmin under multi-site environment or has administrator role for the standalone WP
        if (!$user_id) {
            if (empty($current_user) && function_exists('get_currentuserinfo')) {
                get_currentuserinfo();
            }
            $user_id = !empty($current_user) ? $current_user->ID : 0;
        }

        if (!$user_id) {
            return false;
        }

        $user = new WP_User($user_id);

        $simple_admin = $this->has_administrator_role($user_id);

        if ($this->multisite) {
            $super_admins = get_super_admins();
            $super_admin = is_array($super_admins) && in_array($user->user_login, $super_admins);
        } else {
            $super_admin = false;
        }

        return $simple_admin || $super_admin;
 * 
 */
    }
    // end of user_is_admin()

    
  /**
     * return array with WordPress user roles
     * 
     * @global WP_Roles $wp_roles
     * @global type $wp_user_roles
     * @return array
     */
    public function get_user_roles() {

        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        if (function_exists('bbp_filter_blog_editable_roles')) {  // bbPress plugin is active
            $this->roles = bbp_filter_blog_editable_roles($wp_roles->roles);  // exclude bbPress roles	
            $bbp_full_caps = bbp_get_caps_for_role(bbp_get_keymaster_role());
            // exclude capabilities automatically added by bbPress bbp_dynamic_role_caps() and not bbPress related: read, level_0, all s2Member levels, e.g. access_s2member_level_0, etc.
            $built_in_wp_caps = $this->get_built_in_wp_caps();
            $bbp_only_caps = array();
            foreach ($bbp_full_caps as $bbp_cap => $val) {
                if (isset($built_in_wp_caps[$bbp_cap]) || substr($bbp_cap, 0, 15) == 'access_s2member') {
                    continue;
                }
                $bbp_only_caps[$bbp_cap] = $val;
            }
            // remove bbPress dynamically created capabilities from WordPress persistent roles in order to not save them to database with any role update
            $cap_removed = false;
            foreach ($bbp_only_caps as $bbp_cap => $val) {
                foreach ($this->roles as &$role) {
                    if (isset($role['capabilities'][$bbp_cap])) {
                        unset($role['capabilities'][$bbp_cap]);
                        $cap_removed = true;
                    }
                }
            }
            /*
              if ($cap_removed) {
              // save changes to database
              $option_name = $wpdb->prefix.'user_roles';
              update_option($option_name, $this->roles);
              }
             */
        } else {
            $this->roles = $wp_roles->roles;
        }

        if (is_array($this->roles) && count($this->roles) > 0) {
            asort($this->roles);
        }

        return $this->roles;
    }
    // end of get_user_roles()
     
/*    
    // restores User Roles from the backup record
    protected function restore_user_roles() 
    {
        global $wpdb, $wp_roles;

        $error_message = 'Error! ' . __('Database operation error. Check log file.', 'ure');
        $option_name = $wpdb->prefix . 'user_roles';
        $backup_option_name = $wpdb->prefix . 'backup_user_roles';
        $query = "select option_value
              from $wpdb->options
              where option_name='$backup_option_name'
              limit 0, 1";
        $option_value = $wpdb->get_var($query);
        if ($wpdb->last_error) {
            $this->log_event($wpdb->last_error, true);
            return $error_message;
        }
        if ($option_value) {
            $query = "update $wpdb->options
                    set option_value='$option_value'
                    where option_name='$option_name'
                    limit 1";
            $record = $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return $error_message;
            }
            $wp_roles = new WP_Roles();
            $reload_link = wp_get_referer();
            $reload_link = remove_query_arg('action', $reload_link);
            $reload_link = add_query_arg('action', 'roles_restore_note', $reload_link);
?>    
            <script type="text/javascript" >
              document.location = '<?php echo $reload_link; ?>';
            </script>  
            <?php
            $mess = '';
        } else {
            $mess = __('No backup data. It is created automatically before the first role data update.', 'ure');
        }
        if (isset($_REQUEST['user_role'])) {
            unset($_REQUEST['user_role']);
        }

        return $mess;
    }
    // end of restore_user_roles()
*/

    protected function convert_caps_to_readable($caps_name) 
    {

        $caps_name = str_replace('_', ' ', $caps_name);
        $caps_name = ucfirst($caps_name);

        return $caps_name;
    }
    // ure_ConvertCapsToReadable
    
            
    public function make_roles_backup() 
    {
        global $wpdb;

        // check if backup user roles record exists already
        $backup_option_name = $wpdb->prefix . 'backup_user_roles';
        $query = "select option_id
              from $wpdb->options
              where option_name='$backup_option_name'
          limit 0, 1";
        $option_id = $wpdb->get_var($query);
        if ($wpdb->last_error) {
            $this->log_event($wpdb->last_error, true);
            return false;
        }
        if (!$option_id) {
            $roles_option_name = $wpdb->prefix.'user_roles';
            $query = "select option_value 
                        from $wpdb->options 
                        where option_name like '$roles_option_name' limit 0,1";
            $serialized_roles = $wpdb->get_var($query);
            // create user roles record backup            
            $query = "insert into $wpdb->options
                (option_name, option_value, autoload)
                values ('$backup_option_name', '$serialized_roles', 'no')";
            $record = $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return false;
            }
        }

        return true;
    }
    // end of ure_make_roles_backup()

    
    protected function role_contains_caps_not_allowed_for_simple_admin($role_id) {
        
        $result = false;
        $role = $this->roles[$role_id];
        if (!is_array($role['capabilities'])) {
            return false;
        }
        foreach (array_keys($role['capabilities']) as $cap) {
            if ($this->block_cap_for_single_admin($cap)) {
                $result = true;
                break;
            }
        }
        
        return $result;
    } 
    // end of role_contains_caps_not_allowed_for_simple_admin()
    
    /**
     * return array with roles which we could delete, e.g self-created and not used with any blog user
     * 
     * @global wpdb $wpdb   - WP database object
     * @return array 
     */
    protected function get_roles_can_delete() 
    {
        global $wpdb;

        $table_name = (!$this->multisite && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
        $meta_key = $wpdb->prefix . 'capabilities';
        $default_role = get_option('default_role');
        $standard_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
        $roles_can_delete = array();
        foreach ($this->roles as $key => $role) {
            $can_delete = true;
            // check if it is default role for new users
            if ($key == $default_role) {
                $can_delete = false;
                continue;
            }
            // check if it is standard role            
            if (in_array($key, $standard_roles)) {
                continue;
            }
            // check if role has capabilities prohibited for the single site administrator
            if ($this->role_contains_caps_not_allowed_for_simple_admin($key)) {
                continue;
            }
            
            // check if user with such role exists
            $query = "SELECT meta_value
                FROM $table_name
                WHERE meta_key='$meta_key' AND meta_value like '%$key%'";
            $roles_used = $wpdb->get_results($query);
            if ($roles_used && count($roles_used > 0)) {
                foreach ($roles_used as $role_used) {
                    $role_name = unserialize($role_used->meta_value);
                    foreach ($role_name as $key1 => $value1) {
                        if ($key == $key1) {
                            $can_delete = false;
                            break;
                        }
                    }
                    if (!$can_delete) {
                        break;
                    }
                }
            }
            if ($can_delete) {
                $roles_can_delete[$key] = $role['name'] . ' (' . $key . ')';
            }
        }

        return $roles_can_delete;
    }
    // end of get_roles_can_delete()
    
    
    /**
     * return array of built-in WP capabilities (WP 3.1 wp-admin/includes/schema.php) 
     * 
     * @return array 
     */
    public function get_built_in_wp_caps() {
        $caps = array();
        $caps['switch_themes'] = 1;
        $caps['edit_themes'] = 1;
        $caps['activate_plugins'] = 1;
        $caps['edit_plugins'] = 1;
        $caps['edit_users'] = 1;
        $caps['edit_files'] = 1;
        $caps['manage_options'] = 1;
        $caps['moderate_comments'] = 1;
        $caps['manage_categories'] = 1;
        $caps['manage_links'] = 1;
        $caps['upload_files'] = 1;
        $caps['import'] = 1;
        $caps['unfiltered_html'] = 1;
        $caps['edit_posts'] = 1;
        $caps['edit_others_posts'] = 1;
        $caps['edit_published_posts'] = 1;
        $caps['publish_posts'] = 1;
        $caps['edit_pages'] = 1;
        $caps['read'] = 1;
        $caps['level_10'] = 1;
        $caps['level_9'] = 1;
        $caps['level_8'] = 1;
        $caps['level_7'] = 1;
        $caps['level_6'] = 1;
        $caps['level_5'] = 1;
        $caps['level_4'] = 1;
        $caps['level_3'] = 1;
        $caps['level_2'] = 1;
        $caps['level_1'] = 1;
        $caps['level_0'] = 1;
        $caps['edit_others_pages'] = 1;
        $caps['edit_published_pages'] = 1;
        $caps['publish_pages'] = 1;
        $caps['delete_pages'] = 1;
        $caps['delete_others_pages'] = 1;
        $caps['delete_published_pages'] = 1;
        $caps['delete_posts'] = 1;
        $caps['delete_others_posts'] = 1;
        $caps['delete_published_posts'] = 1;
        $caps['delete_private_posts'] = 1;
        $caps['edit_private_posts'] = 1;
        $caps['read_private_posts'] = 1;
        $caps['delete_private_pages'] = 1;
        $caps['edit_private_pages'] = 1;
        $caps['read_private_pages'] = 1;
        $caps['unfiltered_upload'] = 1;
        $caps['edit_dashboard'] = 1;
        $caps['update_plugins'] = 1;
        $caps['delete_plugins'] = 1;
        $caps['install_plugins'] = 1;
        $caps['update_themes'] = 1;
        $caps['install_themes'] = 1;
        $caps['update_core'] = 1;
        $caps['list_users'] = 1;
        $caps['remove_users'] = 1;
        $caps['add_users'] = 1;
        $caps['promote_users'] = 1;
        $caps['edit_theme_options'] = 1;
        $caps['delete_themes'] = 1;
        $caps['export'] = 1;
        $caps['delete_users'] = 1;
        $caps['create_users'] = 1;
        if ($this->multisite) {
            $caps['manage_network'] = 1;        
            $caps['manage_network_users'] = 1;
            $caps['manage_network_themes'] = 1;
            $caps['manage_network_plugins'] = 1;
            $caps['manage_network_options'] = 1;
        }
                
        return $caps;
    }
    // end of get_built_in_wp_caps()

    
    /**
     * return the array of unused user capabilities
     * 
     * @global WP_Roles $wp_roles
     * @global wpdb $wpdb
     * @return array 
     */
    protected function get_caps_to_remove() 
    {
        global $wp_roles;

        // build full capabilities list from all roles except Administrator 
        $full_caps_list = array();
        foreach ($wp_roles->roles as $role) {
            // validate if capabilities is an array
            if (isset($role['capabilities']) && is_array($role['capabilities'])) {
                foreach ($role['capabilities'] as $capability => $value) {
                    if (!isset($full_caps_list[$capability])) {
                        $full_caps_list[$capability] = 1;
                    }
                }
            }
        }

        $caps_to_exclude = $this->get_built_in_wp_caps();

        $caps_to_remove = array();
        foreach ($full_caps_list as $capability => $value) {
            if (!isset($caps_to_exclude[$capability])) {    // do not touch built-in WP caps
                // check roles
                $cap_in_use = false;
                foreach ($wp_roles->role_objects as $wp_role) {
                    if ($wp_role->name != 'administrator') {
                        if ($wp_role->has_cap($capability)) {
                            $cap_in_use = true;
                            break;
                        }
                    }
                }
                if (!$cap_in_use) {
                    $caps_to_remove[$capability] = 1;
                }
            }
        }

        return $caps_to_remove;
    }
    // end of get_caps_to_remove()

    
    /**
     * Build HTML for select drop-down list from capabilities we can remove
     * 
     * @return string
     */
    protected function get_caps_to_remove_html() {
        
        $caps_to_remove = $this->get_caps_to_remove();
        if (!empty($caps_to_remove) && is_array($caps_to_remove) && count($caps_to_remove) > 0) {
            $html = '<select id="remove_user_capability" name="remove_user_capability" width="200" style="width: 200px">';
            foreach ($caps_to_remove as $key => $value) {
                $html .= '<option value="' . $key . '">' . $key . '</option>';
            }
            $html .= '</select>';
        } else {
            $html = '';
        }

        return $html;
    }
    // end of getCapsToRemoveHTML()
    

    /**
     * returns array of deprecated capabilities
     * 
     * @return array 
     */
    protected function get_deprecated_caps() 
    {

        $dep_caps = array(
            'level_0' => 0,
            'level_1' => 0,
            'level_2' => 0,
            'level_3' => 0,
            'level_4' => 0,
            'level_5' => 0,
            'level_6' => 0,
            'level_7' => 0,
            'level_8' => 0,
            'level_9' => 0,
            'level_10' => 0,
            'edit_files' => 0);
        if ($this->multisite) {
            $dep_caps['unfiltered_html'] = 0;
        }

        return $dep_caps;
    }
    // end of get_deprecated_caps()

    
    /**
     * Return true if $capability is included to the list of capabilities allowed for the single site administrator
     * @param string $capability - capability ID
     * @param boolean $ignore_super_admin - if 
     * @return boolean
     */
    protected function block_cap_for_single_admin($capability, $ignore_super_admin=false) {
        
        if (!class_exists('User_Role_Editor_Pro')) {    // this functionality is for the Pro version only.
            return false;
        }
        
        if (!$this->multisite) {    // work for multisite only
            return false;
        }
        if (!$ignore_super_admin && is_super_admin()) { // Do not block superadmin
            return false;
        }
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        if (!$caps_access_restrict_for_simple_admin) {
            return false;
        }
        $allowed_caps = $this->get_option('caps_allowed_for_single_admin', array());
        if (in_array($capability, $allowed_caps)) {
            $block_this_cap = false;
        } else {
            $block_this_cap = true;
        }
        
        return $block_this_cap;
    }
    // end of block_cap_for_single_admin()
    
    
    /**
     * output HTML-code for capabilities list
     * @param boolean $core - if true, then show WordPress core capabilities, else custom (plugins and themes created)
     * @param boolean $for_role - if true, it is role capabilities list, else - user specific capabilities list
     */
    protected function show_capabilities($core = true, $for_role = true) {
                
        if ($this->multisite && !is_super_admin()) {
            $help_links_enabled = $this->get_option('enable_help_links_for_simple_admin_ms', 1);
        } else {
            $help_links_enabled = true;
        }
        
        $onclick_for_admin = '';
        if (!( $this->multisite && is_super_admin() )) {  // do not limit SuperAdmin for multi-site
            if ($core && 'administrator' == $this->current_role) {
                $onclick_for_admin = 'onclick="turn_it_back(this)"';
            }
        }

        if ($core) {
            $quant = count($this->get_built_in_wp_caps());
            $deprecated_caps = $this->get_deprecated_caps();
        } else {
            $quant = count($this->full_capabilities) - count($this->get_built_in_wp_caps());
            $deprecated_caps = array();
        }
        $quant_in_column = (int) $quant / 3;
        $printed_quant = 0;
        foreach ($this->full_capabilities as $capability) {            
            if ($core) {
                if (!$capability['wp_core']) { // show WP built-in capabilities 1st
                    continue;
                }
            } else {
                if ($capability['wp_core']) { // show plugins and themes added capabilities
                    continue;
                }
            }
            if (!$this->show_deprecated_caps && isset($deprecated_caps[$capability['inner']])) {
                $hidden_class = 'class="hidden"';
            } else {
                $hidden_class = '';
            }
            if (isset($deprecated_caps[$capability['inner']])) {
                $label_style = 'style="color:#BBBBBB;"';
            } else {
                $label_style = '';
            }
            if ($this->multisite && $this->block_cap_for_single_admin($capability['inner'], true)) {
                if (is_super_admin()) {
                    if (!is_network_admin()) {
                        $label_style = 'style="color: red;"';
                    }
                } else {
                    $hidden_class = 'class="hidden"';
                }
            }
            $checked = '';
            $disabled = '';
            if ($for_role) {
                if (isset($this->roles[$this->current_role]['capabilities'][$capability['inner']]) &&
                        !empty($this->roles[$this->current_role]['capabilities'][$capability['inner']])) {
                    $checked = 'checked="checked"';
                }
            } else {
                if ($this->user_can($capability['inner'])) {
                    $checked = 'checked="checked"';
                    if (!isset($this->user_to_edit->caps[$capability['inner']])) {
                        $disabled = 'disabled="disabled"';
                    }
                }
            }
            $cap_id = str_replace(' ', URE_SPACE_REPLACER, $capability['inner']);
            echo '<div id="ure_div_cap_'. $cap_id.'" '. $hidden_class .'><input type="checkbox" name="' . $cap_id . '" id="' . 
                    $cap_id . '" value="' . $capability['inner'] .'" '. $checked . ' ' . $disabled . ' ' . $onclick_for_admin . '>';
            if (empty($hidden_class)) {
                if ($this->caps_readable) {
                    $cap_ind = 'human';
                    $cap_ind_alt = 'inner';
                } else {
                    $cap_ind = 'inner';
                    $cap_ind_alt = 'human';
                }
                $help_link = $help_links_enabled ? $this->capability_help_link($capability['inner']) : '';
                echo '<label for="' . $cap_id . '" title="' . $capability[$cap_ind_alt] . '" ' . $label_style . ' > ' . 
                     $capability[$cap_ind] . '</label> ' . $help_link . '</div>';
                $printed_quant++;
                if ($printed_quant >= $quant_in_column) {
                    $printed_quant = 0;
                    echo '</td>
                          <td style="vertical-align:top;">';
                }
            }  else {   // if (empty($hidden_class
                echo '</div>';
            } // if (empty($hidden_class
        }
    }
    // end of show_capabilities()


    /**
     * output HTML code to create URE toolbar
     * 
     * @param string $this->current_role
     * @param boolean $role_delete
     * @param boolean $capability_remove
     */
    protected function toolbar($role_delete = false, $capability_remove = false) {
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        if ($caps_access_restrict_for_simple_admin) {
            $add_del_role_for_simple_admin = $this->get_option('add_del_role_for_simple_admin', 1);
        } else {
            $add_del_role_for_simple_admin = 1;
        }
        $super_admin = is_super_admin();
        
?>	
        <div id="ure_toolbar" >
           <button id="ure_select_all" class="ure_toolbar_button">Select All</button>
<?php
        if ('administrator' != $this->current_role) {
?>   
               <button id="ure_unselect_all" class="ure_toolbar_button">Unselect All</button> 
               <button id="ure_reverse_selection" class="ure_toolbar_button">Reverse</button> 
<?php
        }
        if ($this->ure_object == 'role') {
?>              
               <hr />
               <div id="ure_update">
                <button id="ure_update_role" class="ure_toolbar_button button-primary" >Update</button> 
<?php
            do_action('ure_role_edit_toolbar_update');
?>                                   
               </div>
<?php
            if (!$this->multisite || $super_admin || $add_del_role_for_simple_admin) { // restrict single site admin
?>
               <hr />               
               <button id="ure_add_role" class="ure_toolbar_button">Add New Role</button>   
<?php
            }   // restrict single site admin
            if (!$this->multisite || $super_admin || !$caps_access_restrict_for_simple_admin) { // restrict single site admin
?>
               <button id="ure_add_capability" class="ure_toolbar_button">Add New Capability</button>
<?php
            }   // restrict single site admin
            
            if (!$this->multisite || $super_admin || $add_del_role_for_simple_admin) { // restrict single site admin
                if (!empty($role_delete)) {
?>  
                   <button id="ure_delete_role" class="ure_toolbar_button">Delete Role</button>
<?php
                }
            } // restrict single site admin
            
            if (!$this->multisite || $super_admin || !$caps_access_restrict_for_simple_admin) { // restrict single site admin            
                if ($capability_remove) {
?>
                   <button id="ure_delete_capability" class="ure_toolbar_button">Delete Capability</button>
<?php
                }
?>
               <hr />
               <button id="ure_default_role" class="ure_toolbar_button">Default Role</button>
               <hr />
               <div id="ure_service_tools">
<?php
                do_action('ure_role_edit_toolbar_service');
                if (!is_multisite() || 
                    (is_main_site( get_current_blog_id()) || (is_network_admin() && is_super_admin()))
                   ) {
?>                   
                  <button id="ure_reset_roles" class="ure_toolbar_button" style="color: red;" title="Reset Roles to its original state">Reset</button> 
<?php
                }
?>
               </div>
            <?php
            }   // restrict single site admin
        } else {
            ?>
               
               <hr />
            	 <div id="ure_update_user">
                <button id="ure_update_role" class="ure_toolbar_button button-primary">Update</button> 
<?php
    do_action('ure_user_edit_toolbar_update');
?>                   
                
            	 </div>	 
            <?php
        }
            ?>
           
        </div>  
        <?php
    }
    // end of toolbar()
    
    
    /**
     * return link to the capability according its name in $capability parameter
     * 
     * @param string $capability
     * @return string 
     */
    protected function capability_help_link($capability) {

        if (empty($capability)) {
            return '';
        }

        switch ($capability) {
            case 'activate_plugins':
                $url = 'http://www.shinephp.com/activate_plugins-wordpress-capability/';
                break;
            case 'add_users':
                $url = 'http://www.shinephp.com/add_users-wordpress-user-capability/';
                break;
            case 'create_users':
                $url = 'http://www.shinephp.com/create_users-wordpress-user-capability/';
                break;
            case 'delete_others_pages':
            case 'delete_others_posts':
            case 'delete_pages':
            case 'delete_posts':
            case 'delete_protected_pages':
            case 'delete_protected_posts':
            case 'delete_published_pages':
            case 'delete_published_posts':
                $url = 'http://www.shinephp.com/delete-posts-and-pages-wordpress-user-capabilities-set/';
                break;
            case 'delete_plugins':
                $url = 'http://www.shinephp.com/delete_plugins-wordpress-user-capability/';
                break;
            case 'delete_themes':
                $url = 'http://www.shinephp.com/delete_themes-wordpress-user-capability/';
                break;
            case 'delete_users':
                $url = 'http://www.shinephp.com/delete_users-wordpress-user-capability/';
                break;
            case 'edit_dashboard':
                $url = 'http://www.shinephp.com/edit_dashboard-wordpress-capability/';
                break;
            case 'edit_files':
                $url = 'http://www.shinephp.com/edit_files-wordpress-user-capability/';
                break;
            case 'edit_plugins':
                $url = 'http://www.shinephp.com/edit_plugins-wordpress-user-capability';
                break;
            case 'moderate_comments':
                $url = 'http://www.shinephp.com/moderate_comments-wordpress-user-capability/';
                break;
            case 'read':
                $url = 'http://shinephp.com/wordpress-read-capability/';
                break;
            case 'update_core':
                $url = 'http://www.shinephp.com/update_core-capability-for-wordpress-user/';
                break;
            default:
                $url = '';
        }
        // end of switch
        if (!empty($url)) {
            $link = '<a href="' . $url . '" title="read about ' . $capability . ' user capability" target="new"><img src="' . URE_PLUGIN_URL . '/images/help.png" alt="' . __('Help', 'ure') . '" /></a>';
        } else {
            $link = '';
        }

        return $link;
    }
    // end of capability_help_link()
    

    /**
     *  Go through all users and if user has non-existing role lower him to Subscriber role
     * 
     */   
    protected function validate_user_roles() {

        global $wp_roles;

        $default_role = get_option('default_role');
        if (empty($default_role)) {
            $default_role = 'subscriber';
        }
        $users_query = new WP_User_Query(array('fields' => 'ID'));
        $users = $users_query->get_results();
        foreach ($users as $user_id) {
            $user = get_user_by('id', $user_id);
            if (is_array($user->roles) && count($user->roles) > 0) {
                foreach ($user->roles as $role) {
                    $user_role = $role;
                    break;
                }
            } else {
                $user_role = is_array($user->roles) ? '' : $user->roles;
            }
            if (!empty($user_role) && !isset($wp_roles->roles[$user_role])) { // role doesn't exists
                $user->set_role($default_role); // set the lowest level role for this user
                $user_role = '';
            }

            if (empty($user_role)) {
                // Cleanup users level capabilities from non-existed roles
                $cap_removed = true;
                while (count($user->caps) > 0 && $cap_removed) {
                    foreach ($user->caps as $capability => $value) {
                        if (!isset($this->full_capabilities[$capability])) {
                            $user->remove_cap($capability);
                            $cap_removed = true;
                            break;
                        }
                        $cap_removed = false;
                    }
                }  // while ()
            }
        }  // foreach()
    }
    // end of validate_user_roles()

        
    protected function add_capability_to_full_caps_list($cap_id) {
        if (!isset($this->full_capabilities[$cap_id])) {
            $cap = array();
            $cap['inner'] = $cap_id;
            $cap['human'] = __($this->convert_caps_to_readable($cap_id), 'ure');
            if (isset($this->built_in_wp_caps[$cap_id])) {
                $cap['wp_core'] = true;
            } else {
                $cap['wp_core'] = false;
            }

            $this->full_capabilities[$cap_id] = $cap;
        }
    }
    // end of add_capability_to_full_caps_list()


    protected function init_full_capabilities() {
        $this->built_in_wp_caps = $this->get_built_in_wp_caps();
        $this->full_capabilities = array();
        foreach ($this->roles as $role) {
            // validate if capabilities is an array
            if (isset($role['capabilities']) && is_array($role['capabilities'])) {
                foreach ($role['capabilities'] as $key => $value) {
                    $this->add_capability_to_full_caps_list($key);
                }
            }
        }
        // Get Gravity Forms plugin capabilities, if available
        if ($this->integrate_with_gravity_forms) {
            $gf_caps = GFCommon::all_caps();
            foreach ($gf_caps as $gf_cap) {
                $this->add_capability_to_full_caps_list($gf_cap);
            }
        }
        
        if ($this->ure_object=='user') {
            foreach($this->user_to_edit->caps as $key=>$value)  {
                if (!isset($this->roles[$key])) {   // it is the user capability, not role
                    $this->add_capability_to_full_caps_list($key);
                }
            }
        }
        
        foreach ($this->built_in_wp_caps as $cap=>$val) {
            if (!isset($this->full_capabilities[$cap])) {
                $this->add_capability_to_full_caps_list($cap);
            }
        }
        
        unset($this->built_in_wp_caps);
        asort($this->full_capabilities);
    }
    // end of init_full_capabilities()


    /**
     * return WordPress user roles to its initial state, just like after installation
     * @global WP_Roles $wp_roles
     */
    protected function wp_roles_reinit() {
        global $wp_roles;
        
        $wp_roles->roles = array();
        $wp_roles->role_objects = array();
        $wp_roles->role_names = array();
        $wp_roles->use_db = true;

        require_once(ABSPATH . '/wp-admin/includes/schema.php');
        populate_roles();
        $wp_roles->reinit();
        
        $this->roles = $this->get_user_roles();
        
    }
    // end of wp_roles_reinit()
    
    /**
     * reset user roles to WordPress default roles
     */
    protected function reset_user_roles() {
              
        $this->wp_roles_reinit();
        if ($this->is_full_network_synch() || $this->apply_to_all) {
            $this->current_role = '';
            $this->direct_network_roles_update();
        }
        //$this->validate_user_roles();  // if user has non-existing role lower him to Subscriber role
        
        $reload_link = wp_get_referer();
        $reload_link = remove_query_arg('action', $reload_link);        
        ?>    
        	<script type="text/javascript" >
             jQuery.ure_postGo('<?php echo $reload_link; ?>', 
                      { action: 'roles_restore_note', 
                        ure_nonce: ure_data.wp_nonce} );
        	</script>  
        <?php
    }
    // end of reset_user_roles()

    
    /**
     * if returns true - make full syncronization of roles for all sites with roles from the main site
     * else - only currently selected role update is replicated
     * 
     * @return boolean
     */
    public function is_full_network_synch() {
        
        $result = defined('URE_MULTISITE_DIRECT_UPDATE') && URE_MULTISITE_DIRECT_UPDATE == 1;
        
        return $result;
    }
    // end of is_full_network_synch()
    
    
    protected function last_check_before_update() {
        if (empty($this->roles) || !is_array($this->roles) || count($this->roles)==0) { // Nothing to save - something goes wrong - stop ...
            return false;
        }
        
        return true;
    }
    // end of last_check_before_update()
    
    
    // Save Roles to database
    protected function save_roles() {
        global $wpdb;

        if (!$this->last_check_before_update()) {
            return false;
        }
        if (!isset($this->roles[$this->current_role])) {
            return false;
        }
        
        $this->capabilities_to_save = $this->remove_caps_not_allowed_for_single_admin($this->capabilities_to_save);
        $this->roles[$this->current_role]['capabilities'] = $this->capabilities_to_save;
        $option_name = $wpdb->prefix . 'user_roles';

        update_option($option_name, $this->roles);

        return true;
    }
    // end of save_roles()
    
    
    /**
     * Update roles for all network using direct database access - quicker in several times
     * 
     * @global wpdb $wpdb
     * @return boolean
     */
    function direct_network_roles_update() {
        global $wpdb;

        if (!$this->last_check_before_update()) {
            return false;
        }
        if (!empty($this->current_role)) {
            if (!isset($this->roles[$this->current_role])) {
                $this->roles[$this->current_role]['name'] = $this->current_role_name;
            }
            $this->roles[$this->current_role]['capabilities'] = $this->capabilities_to_save;
        }        

        $serialized_roles = serialize($this->roles);
        foreach ($this->blog_ids as $blog_id) {
            $prefix = $wpdb->get_blog_prefix($blog_id);
            $options_table_name = $prefix . 'options';
            $option_name = $prefix . 'user_roles';
            $query = "update $options_table_name
                set option_value='$serialized_roles'
                where option_name='$option_name'
                limit 1";
            $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return false;
            }
        }
        
        return true;
    }
    // end of direct_network_roles_update()

    
    protected function wp_api_network_roles_update() {
        global $wpdb;
        
        $result = true;
        $old_blog = $wpdb->blogid;
        foreach ($this->blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            $this->roles = $this->get_user_roles();
            if (!isset($this->roles[$this->current_role])) { // add new role to this blog
                $this->roles[$this->current_role] = array('name' => $this->current_role_name, 'capabilities' => array('read' => 1));
            }
            if (!$this->save_roles()) {
                $result = false;
                break;
            }
        }
        switch_to_blog($old_blog);
        // cleanup blog switching data
        $GLOBALS['_wp_switched_stack'] = array();
        $GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );
        $this->roles = $this->get_user_roles();
        
        return $result;
    }
    // end of wp_api_network_roles_update()
    
        
    /**
     * Update role for all network using WordPress API
     * 
     * @return boolean
     */
    protected function multisite_update_roles() {
        
        if (defined('URE_DEBUG') && URE_DEBUG) {
            $time_shot = microtime();
        }
        
        if ($this->is_full_network_synch()) {
            $result = $this->direct_network_roles_update();
        } else {
            $result = $this->wp_api_network_roles_update();            
        }

        if (defined('URE_DEBUG') && URE_DEBUG) {
            echo '<div class="updated fade below-h2">Roles updated for ' . ( microtime() - $time_shot ) . ' milliseconds</div>';
        }

        return $result;
    }
    // end of multisite_update_roles()

    
    /**
     * Process user request on update roles
     * 
     * @global wpdb $wpdb
     * @return boolean
     */
    protected function update_roles() {
        global $wpdb;

        if ($this->multisite && is_super_admin() && $this->apply_to_all) {  // update Role for the all blogs/sites in the network (permitted to superadmin only)
            if (!$this->multisite_update_roles()) {
                return false;
            }
        } else {
            if (!$this->save_roles()) {
                return false;
            }
        }

        return true;
    }
    // end of update_roles()

    
    /**
     * Write message to the log file
     * 
     * @global type $wp_version
     * @param string $message
     * @param boolean $show_message
     */
    protected function log_event($message, $show_message = false) {
        global $wp_version;

        $file_name = URE_PLUGIN_DIR . 'user-role-editor.log';
        $fh = fopen($file_name, 'a');
        $cr = "\n";
        $s = $cr . date("d-m-Y H:i:s") . $cr .
                'WordPress version: ' . $wp_version . ', PHP version: ' . phpversion() . ', MySQL version: ' . mysql_get_server_info() . $cr;
        fwrite($fh, $s);
        fwrite($fh, $message . $cr);
        fclose($fh);

        if ($show_message) {
            $this->show_message('Error! ' . __('Error is occur. Please check the log file.', 'ure'));
        }
    }
    // end of log_event()

    
    /**
     * returns array without capabilities blocked for single site administrators
     * @param array $capabilities
     * @return array
     */
    protected function remove_caps_not_allowed_for_single_admin($capabilities) {
        
        foreach(array_keys($capabilities) as $cap) {
            if ($this->block_cap_for_single_admin($cap)) {
                unset($capabilities[$cap]);
            }
        }
        
        return $capabilities;
    }
    // end of remove_caps_not_allowed_for_single_admin()
    
    
    /**
     * process new role create request
     * 
     * @global WP_Roles $wp_roles
     * 
     * @return string   - message about operation result
     * 
     */
    protected function add_new_role() {

        global $wp_roles;

        $mess = '';
        $this->current_role = '';
        if (isset($_POST['user_role_id']) && $_POST['user_role_id']) {
            $user_role_id = utf8_decode($_POST['user_role_id']);
            // sanitize user input for security
            $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_role_id, $match);
            if (!$valid_name || ($valid_name && ($match[0] != $user_role_id))) { // some non-alphanumeric charactes found!
                return esc_html__('Error: Role ID must contain latin characters, digits, hyphens or underscore only!', 'ure');
            }
            $numeric_name = preg_match('/[0-9]*/', $user_role_id, $match);
            if ($numeric_name && ($match[0] == $user_role_id)) { // numeric name discovered
                return esc_html__('Error: WordPress does not support numeric Role name (ID). Add latin characters to it.', 'ure');
            }
            
            if ($user_role_id) {
                $user_role_name = isset($_POST['user_role_name']) ? $_POST['user_role_name'] : false;
                if (!empty($user_role_name)) {
                    $user_role_name = sanitize_text_field($user_role_name);
                } else {
                    $user_role_name = $user_role_id;  // as user role name is empty, use user role ID instead
                }

                if (!isset($wp_roles)) {
                    $wp_roles = new WP_Roles();
                }
                if (isset($wp_roles->roles[$user_role_id])) {
                    return sprintf('Error! ' . __('Role %s exists already', 'ure'), $user_role_id);
                }
                $user_role_id = strtolower($user_role_id);
                $this->current_role = $user_role_id;

                $user_role_copy_from = isset($_POST['user_role_copy_from']) ? $_POST['user_role_copy_from'] : false;
                if (!empty($user_role_copy_from) && $user_role_copy_from != 'none' && $wp_roles->is_role($user_role_copy_from)) {
                    $role = $wp_roles->get_role($user_role_copy_from);
                    $capabilities = $this->remove_caps_not_allowed_for_single_admin($role->capabilities);
                } else {
                    $capabilities = array('read' => 1, 'level_0' => 1);
                }
                // add new role to the roles array      
                $result = add_role($user_role_id, $user_role_name, $capabilities);
                if (!isset($result) || empty($result)) {
                    $mess = 'Error! ' . __('Error is encountered during new role create operation', 'ure');
                } else {
                    $mess = sprintf(__('Role %s is created successfully', 'ure'), $user_role_name);
                }
            }
        }
        return $mess;
    }
    // end of new_role_create()            

    
    /**
     * Deletes user role from the WP database
     */
    protected function delete_wp_roles($roles_to_del) {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $result = false;
        foreach($roles_to_del as $role_id) {
            if (!isset($wp_roles->roles[$role_id])) {
                $result = false;
                break;
            }                                            
            if ($this->role_contains_caps_not_allowed_for_simple_admin($role_id)) { // do not delete
                continue;
            }
            unset($wp_roles->role_objects[$role_id]);
            unset($wp_roles->role_names[$role_id]);
            unset($wp_roles->roles[$role_id]);                
            $result = true;
        }   // foreach()
        if ($result) {
            update_option($wp_roles->role_key, $wp_roles->roles);
        }
        
        return $result;
    }
    // end of delete_wp_roles()
    
    
    protected function delete_all_unused_roles() {        
        
        $this->roles = $this->get_user_roles();
        $roles_to_del = array_keys($this->get_roles_can_delete());  
        $result = $this->delete_wp_roles($roles_to_del);
        $this->roles = null;    // to force roles refresh
        
        return $result;        
    }
    // end of delete_all_unused_roles()
    
    
    /**
     * process user request for user role deletion
     * @global WP_Roles $wp_roles
     * @return type
     */
    protected function delete_role() {        

        $mess = '';        
        if (isset($_POST['user_role_id']) && $_POST['user_role_id']) {
            $role = $_POST['user_role_id'];
            if ($role==-1) { // delete all unused roles
                $result = $this->delete_all_unused_roles();
            } else {
                $result = $this->delete_wp_roles(array($role));
            }
            if (empty($result)) {
                $mess = 'Error! ' . __('Error encountered during role delete operation', 'ure');
            } elseif ($role==-1) {
                $mess = sprintf(__('Unused roles are deleted successfully', 'ure'), $role);
            } else {
                $mess = sprintf(__('Role %s is deleted successfully', 'ure'), $role);
            }
            unset($_POST['user_role']);
        }

        return $mess;
    }
    // end of ure_delete_role()

    
    /**
     * Change default WordPress role
     * @global WP_Roles $wp_roles
     * @return string
     */
    protected function change_default_role() {
        global $wp_roles;

        $mess = '';
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        if (!empty($_POST['user_role_id'])) {
            $user_role_id = $_POST['user_role_id'];
            unset($_POST['user_role_id']);
            $errorMessage = 'Error! ' . __('Error encountered during default role change operation', 'ure');
            if (isset($wp_roles->role_objects[$user_role_id]) && $user_role_id !== 'administrator') {
                $result = update_option('default_role', $user_role_id);
                if (empty($result)) {
                    $mess = $errorMessage;
                } else {
                    $mess = sprintf(__('Default role for new users is set to %s successfully', 'ure'), $wp_roles->role_names[$user_role_id]);
                }
            } else {
                $mess = $errorMessage;
            }
        }

        return $mess;
    }
    // end of change_default_role()
    
    
    /**
     * Not really used in the plugin - just storage for the translation strings
     */
    protected function translation_data() {
// for the translation purpose
        if (false) {
// Standard WordPress roles
            __('Editor', 'ure');
            __('Author', 'ure');
            __('Contributor', 'ure');
            __('Subscriber', 'ure');
// Standard WordPress capabilities
            __('Switch themes', 'ure');
            __('Edit themes', 'ure');
            __('Activate plugins', 'ure');
            __('Edit plugins', 'ure');
            __('Edit users', 'ure');
            __('Edit files', 'ure');
            __('Manage options', 'ure');
            __('Moderate comments', 'ure');
            __('Manage categories', 'ure');
            __('Manage links', 'ure');
            __('Upload files', 'ure');
            __('Import', 'ure');
            __('Unfiltered html', 'ure');
            __('Edit posts', 'ure');
            __('Edit others posts', 'ure');
            __('Edit published posts', 'ure');
            __('Publish posts', 'ure');
            __('Edit pages', 'ure');
            __('Read', 'ure');
            __('Level 10', 'ure');
            __('Level 9', 'ure');
            __('Level 8', 'ure');
            __('Level 7', 'ure');
            __('Level 6', 'ure');
            __('Level 5', 'ure');
            __('Level 4', 'ure');
            __('Level 3', 'ure');
            __('Level 2', 'ure');
            __('Level 1', 'ure');
            __('Level 0', 'ure');
            __('Edit others pages', 'ure');
            __('Edit published pages', 'ure');
            __('Publish pages', 'ure');
            __('Delete pages', 'ure');
            __('Delete others pages', 'ure');
            __('Delete published pages', 'ure');
            __('Delete posts', 'ure');
            __('Delete others posts', 'ure');
            __('Delete published posts', 'ure');
            __('Delete private posts', 'ure');
            __('Edit private posts', 'ure');
            __('Read private posts', 'ure');
            __('Delete private pages', 'ure');
            __('Edit private pages', 'ure');
            __('Read private pages', 'ure');
            __('Delete users', 'ure');
            __('Create users', 'ure');
            __('Unfiltered upload', 'ure');
            __('Edit dashboard', 'ure');
            __('Update plugins', 'ure');
            __('Delete plugins', 'ure');
            __('Install plugins', 'ure');
            __('Update themes', 'ure');
            __('Install themes', 'ure');
            __('Update core', 'ure');
            __('List users', 'ure');
            __('Remove users', 'ure');
            __('Add users', 'ure');
            __('Promote users', 'ure');
            __('Edit theme options', 'ure');
            __('Delete themes', 'ure');
            __('Export', 'ure');
        }
    }
    // end of ure_TranslationData()

    
    /**
     * placeholder - realized at the Pro version
     */
    protected function check_blog_user($user) {
        
        return true;
    }
    // end of check_blog_user()
    
    /**
     * placeholder - realized at the Pro version
     */    
    protected function network_update_user($user) {
        
        return true;
    }
    // end of network_update_user()
    
    
    /**
     * Update user roles and capabilities
     * 
     * @global WP_Roles $wp_roles
     * @param WP_User $user
     * @return boolean
     */
    protected function update_user($user) {
        global $wp_roles;
                
        if ($this->multisite) {
            if (!$this->check_blog_user($user)) {
                return false;
            }
        }
        
        $primary_role = $_POST['primary_role'];  
        if (empty($primary_role) || !isset($wp_roles->roles[$primary_role])) {
            $primary_role = '';
        }
        if (function_exists('bbp_filter_blog_editable_roles')) {  // bbPress plugin is active
            $bbp_user_role = bbp_get_user_role($user->ID);
        } else {
            $bbp_user_role = '';
        }

        // revoke all roles and capabilities from this user
        $user->roles = array();
        $user->remove_all_caps();

        // restore primary role
        if (!empty($primary_role)) {
            $user->add_role($primary_role);
        }

        // restore bbPress user role if she had one
        if (!empty($bbp_user_role)) {
            $user->add_role($bbp_user_role);
        }

        // add other roles to user
        foreach ($_POST as $key => $value) {
            $result = preg_match('/^wp_role_(.+)/', $key, $match);
            if ($result === 1) {
                $role = $match[1];
                if (isset($wp_roles->roles[$role])) {
                    $user->add_role($role);
                }
            }
        }

        // add individual capabilities to user
        if (count($this->capabilities_to_save) > 0) {
            foreach ($this->capabilities_to_save as $key => $value) {
                $user->add_cap($key);
            }
        }
        $user->update_user_level_from_caps();
        
        if ($this->apply_to_all) { // apply update to the all network
            if (!$this->network_update_user($user)) {
                return false;
            }
        }
        
        return true;
    }
    // end of update_user()

    
    /**
     * Add new capability
     * 
     * @global WP_Roles $wp_roles
     * @return string
     */
    protected function add_new_capability() {
        global $wp_roles;

        $mess = '';
        if (isset($_POST['capability_id']) && $_POST['capability_id']) {
            $user_capability = $_POST['capability_id'];
            // sanitize user input for security
            $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_capability, $match);
            if (!$valid_name || ($valid_name && ($match[0] != $user_capability))) { // some non-alphanumeric charactes found!    
                return 'Error! ' . __('Error: Capability name must contain latin characters and digits only!', 'ure');
                ;
            }

            if ($user_capability) {
                $user_capability = strtolower($user_capability);
                if (!isset($wp_roles)) {
                    $wp_roles = new WP_Roles();
                }
                $wp_roles->use_db = true;
                $administrator = $wp_roles->get_role('administrator');
                if (!$administrator->has_cap($user_capability)) {
                    $wp_roles->add_cap('administrator', $user_capability);
                    $mess = sprintf(__('Capability %s is added successfully', 'ure'), $user_capability);
                } else {
                    $mess = sprintf('Error! ' . __('Capability %s exists already', 'ure'), $user_capability);
                }
            }
        }

        return $mess;
    }
    // end of add_new_capability()

    
    /**
     * Delete capability
     * 
     * @global wpdb $wpdb
     * @global WP_Roles $wp_roles
     * @return string - information message
     */
    protected function delete_capability() {
        global $wpdb, $wp_roles;

        $mess = '';
        if (!empty($_POST['user_capability_id'])) {
            $capability_id = $_POST['user_capability_id'];
            $caps_to_remove = $this->get_caps_to_remove();
            if (!is_array($caps_to_remove) || count($caps_to_remove) == 0 || !isset($caps_to_remove[$capability_id])) {
                return sprintf(__('Error! You do not have permission to delete this capability: %s!', 'ure'), $capability_id);
            }

            // process users
            $usersId = $wpdb->get_col("SELECT $wpdb->users.ID FROM $wpdb->users");
            foreach ($usersId as $user_id) {
                $user = get_user_to_edit($user_id);
                if ($user->has_cap($capability_id)) {
                    $user->remove_cap($capability_id);
                }
            }

            // process roles
            foreach ($wp_roles->role_objects as $wp_role) {
                if ($wp_role->has_cap($capability_id)) {
                    $wp_role->remove_cap($capability_id);
                }
            }

            $mess = sprintf(__('Capability %s is removed successfully', 'ure'), $capability_id);
        }

        return $mess;
    }
    // end of remove_capability()

    
    /**
     * Returns list of user roles, except 1st one, and bbPress assigned as they are shown by WordPress and bbPress theirselves.
     * 
     * @param type $user WP_User from wp-includes/capabilities.php
     * @return array
     */
    public function other_user_roles($user) {

        global $wp_roles;

        if (!is_array($user->roles) || count($user->roles) <= 1) {
            return '';
        }

        // get bbPress assigned user role
        if (function_exists('bbp_filter_blog_editable_roles')) {
            $bb_press_role = bbp_get_user_role($user->ID);
        } else {
            $bb_press_role = '';
        }

        $roles = array();
        foreach ($user->roles as $key => $value) {
            if (!empty($bb_press_role) && $bb_press_role === $value) {
                // exclude bbPress assigned role
                continue;
            }
            $roles[] = $value;
        }
        array_shift($roles); // exclude primary role which is shown by WordPress itself

        return $roles;
    }
    // end of ure_other_user_roles()
    
    
    /**
     * Returns text presentation of user roles
     * 
     * @param type $roles user roles list
     * @return string
     */
    public function roles_text($roles) {
        global $wp_roles;

        if (is_array($roles) && count($roles) > 0) {
            $role_names = array();
            foreach ($roles as $role) {
                $role_names[] = $wp_roles->roles[$role]['name'];
            }
            $output = implode(', ', $role_names);
        } else {
            $output = '';
        }

        return $output;
    }
    // end of roles_text()
    

    /**
     * display opening part of the HTML box with title and CSS style
     * 
     * @param string $title
     * @param string $style 
     */
    protected function display_box_start($title, $style = '') {
        ?>
        			<div class="postbox" style="float: left; <?php echo $style; ?>">
        				<h3 style="cursor:default;"><span><?php echo $title ?></span></h3>
        				<div class="inside">
        <?php
    }
    // 	end of display_box_start()


    /**
     * close HTML box opened by display_box_start() call
     */
    function display_box_end() {
        ?>
        				</div>
        			</div>
        <?php
    }
    // end of display_box_end()
    
    
    public function about() {
        if (class_exists('User_Role_Editor_Pro')) {
            return;
        }

?>		  
            <h2>User Role Editor</h2>         
            
            <strong>Version:</strong> <?php echo URE_VERSION; ?><br/><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/vladimir.png'; ?>);" target="_blank" href="http://www.shinephp.com/"><?php _e("Author's website", 'ure'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/user-role-editor-icon.png'; ?>);" target="_blank" href="http://role-editor.com"><?php _e('Plugin webpage', 'ure'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/user-role-editor-icon.png'; ?>);" target="_blank" href="http://role-editor.com/download-plugin"><?php _e('Plugin download', 'ure'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/changelog-icon.png'; ?>);" target="_blank" href="http://role-editor.com/changelog"><?php _e('Changelog', 'ure'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/faq-icon.png'; ?>);" target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq"><?php _e('FAQ', 'ure'); ?></a><br/>
            <hr />
                <div style="text-align: center;">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" 
                               value="-----BEGIN PKCS7-----MIIHZwYJKoZIhvcNAQcEoIIHWDCCB1QCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYBME5QAQYFDddWBHA4YXI1x3dYmM77clH5s0CgokYnLVk0P8keOxMtYyNQo6xJs6pY1nJfE3tqNg8CZ3btJjmOUa6DsE+K8Nm6OxGHMQF45z8WAs+f/AvQWdSpPXD0eSMu9osNgmC3yv46hOT3B1J3rKkpeZzMThCdUfECqu+lluzELMAkGBSsOAwIaBQAwgeQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIeMSZk/UuZnuAgcAort75TUUbtDhmdTi1N0tR9W75Ypuw5nBw01HkZFsFHoGezoT95c3ZesHAlVprhztPrizl1UzE9COQs+3p62a0o+BlxUolkqUT3AecE9qs9dNshqreSvmC8SOpirOroK3WE7DStUvViBfgoNAPTTyTIAKKX24uNXjfvx1jFGMQGBcFysbb3OTkc/B6OiU2G951U9R8dvotaE1RQu6JwaRgwA3FEY9d/P8M+XdproiC324nzFel5WlZ8vtDnMyuPxOgggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xMTEyMTAwODU3MjdaMCMGCSqGSIb3DQEJBDEWBBSFh6YmkoVtYdMaDd5G6EN0dGcPpzANBgkqhkiG9w0BAQEFAASBgAB91K/+gsmpbKxILdCVXCkiOg1zSG+tfq2EZSNzf8z/R1E3HH8qPdm68OToILsgWohKFwE+RCwcQ0iq77wd0alnWoknvhBBoFC/U0yJ3XmA3Hkgrcu6yhVijY/Odmf6WWcz79/uLGkvBSECbjTY0GLxvhRlsh2nAioCfxAr1cFo-----END PKCS7-----">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">                        
                    </form>                        
                </div>
<?php         
    }
    // end of about()

    
    protected function set_current_role() {
        if (!isset($this->current_role) || !$this->current_role) {
            if (isset($_REQUEST['user_role']) && $_REQUEST['user_role'] && isset($this->roles[$_REQUEST['user_role']])) {
                $this->current_role = $_REQUEST['user_role'];
            } else {
                $this->current_role = $this->get_last_role_id();
            }
            $this->current_role_name = $this->roles[$this->current_role]['name'];
        }
    }
    // end of set_current_role()
    
    
    protected function show_admin_role_allowed() {
        $show_admin_role = $this->get_option('show_admin_role', 0);
        $show_admin_role = ((defined('URE_SHOW_ADMIN_ROLE') && URE_SHOW_ADMIN_ROLE==1) || $show_admin_role==1) && $this->user_is_admin();
        
        return $show_admin_role;
    }
    // end of show_admin_role()
    
    
    protected function role_edit_prepare_html() {
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        $show_admin_role = $this->show_admin_role_allowed();
        $this->role_default_html = '<select id="default_user_role" name="default_user_role" width="200" style="width: 200px">';
        $this->role_to_copy_html = '<select id="user_role_copy_from" name="user_role_copy_from" width="200" style="width: 200px">
            <option value="none" selected="selected">' . __('None', 'ure') . '</option>';
        $this->role_select_html = '<select id="user_role" name="user_role" onchange="ure_role_change(this.value);">';
        foreach ($this->roles as $key => $value) {
            $selected1 = $this->option_selected($key, $this->current_role);
            $selected2 = $this->option_selected($key, $this->wp_default_role);
            $disabled = ($key==='administrator' && $caps_access_restrict_for_simple_admin && !is_super_admin()) ? 'disabled' : '';
            if ($show_admin_role || $key != 'administrator') {
                $translated_name = esc_html__($value['name'], 'ure');  // get translation from URE language file, if exists
                if ($translated_name === $value['name']) { // get WordPress internal translation
                    $translated_name = translate_user_role($translated_name);
                }
                $translated_name .= ' (' . $key . ')';                
                $this->role_select_html .= '<option value="' . $key . '" ' . $selected1 .' '. $disabled .'>' . $translated_name . '</option>';
                $this->role_default_html .= '<option value="' . $key . '" ' . $selected2 .' '. $disabled .'>' . $translated_name . '</option>';
                $this->role_to_copy_html .= '<option value="' . $key .'" '. $disabled .'>' . $translated_name . '</option>';
            }
        }
        $this->role_select_html .= '</select>';
        $this->role_default_html .= '</select>';
        $this->role_to_copy_html .= '</select>';

        $roles_can_delete = $this->get_roles_can_delete();
        if ($roles_can_delete && count($roles_can_delete) > 0) {
            $this->role_delete_html = '<select id="del_user_role" name="del_user_role" width="200" style="width: 200px">';
            foreach ($roles_can_delete as $key => $value) {
                $this->role_delete_html .= '<option value="' . $key . '">' . __($value, 'ure') . '</option>';
            }
            $this->role_delete_html .= '<option value="-1" style="color: red;">' . __('Delete All Unused Roles', 'ure') . '</option>';
            $this->role_delete_html .= '</select>';
        } else {
            $this->role_delete_html = '';
        }

        $this->capability_remove_html = $this->get_caps_to_remove_html();
    }
    // end of role_edit_prepare_html()
    
    
    public function user_primary_role_dropdown_list($user_roles) {
?>        
        <select name="primary_role" id="primary_role">
<?php
        // Compare user role against currently editable roles
        $user_roles = array_intersect( array_values( $user_roles ), array_keys( get_editable_roles() ) );
        $user_primary_role  = array_shift( $user_roles );

        // print the full list of roles with the primary one selected.
        wp_dropdown_roles($user_primary_role);

        // print the 'no role' option. Make it selected if the user has no role yet.        
        $selected = ( empty($user_primary_role) ) ? 'selected="selected"' : '';
        echo '<option value="" '. $selected.'>' . __('&mdash; No role for this site &mdash;') . '</option>';
?>
        </select>
<?php        
    }
    // end of user_primary_role_dropdown_list()
    
    
    // returns true if $user has $capability assigned through the roles or directly
    // returns true if user has role with name equal $capability
    protected function user_can($capability) {
        
        if (isset($this->user_to_edit->caps[$capability])) {
            return true;
        }
        foreach ($this->user_to_edit->roles as $role) {
            if ($role===$capability) {
                return true;
            }
            if (!empty($this->roles[$role]['capabilities'][$capability])) {
                return true;
            }
        }
                
        return false;        
    }
    // end of user_can()           
    
    
    // returns true if current user has $capability assigned through the roles or directly
    // returns true if current user has role with name equal $capability
    public function user_has_capability($user, $cap) {

        global $wp_roles;

        if (is_multisite() && is_super_admin()) {
            return true;
        }

        if (isset($user->caps[$cap])) {
            return true;
        }
        foreach ($user->roles as $role) {
            if ($role === $cap) {
                return true;
            }
            if (!empty($wp_roles->roles[$role]['capabilities'][$cap])) {
                return true;
            }
        }

        return false;
    }
    // end of user_has_capability()           
        
    
}
// end of URE_Lib class