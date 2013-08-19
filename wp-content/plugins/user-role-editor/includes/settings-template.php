<?php
/*
 * User Role Editor WordPress plugin options page
 *
 * @Author: Vladimir Garagulya
 * @URL: http://role-editor.com
 * @package UserRoleEditor
 *
 */


?>
<div class="wrap">
  <div class="icon32" id="icon-options-general"><br/></div>
  <h2><?php esc_html_e('User Role Editor - Options', 'ure'); ?></h2>
  <hr/>
  
  <form method="post" action="<?php echo $link; ?>?page=settings-<?php echo URE_PLUGIN_FILE;?>" >   
    <table id="ure_settings">
      <tr>
        <td><label for="show_admin_role"><?php esc_html_e('Show Administrator role at User Role Editor:', 'ure'); ?></label></td>
        <td><input type="checkbox" name="show_admin_role" id="show_admin_role" value="1" 
            <?php echo ($show_admin_role==1) ? 'checked="checked"' : ''; ?>
            <?php echo defined('URE_SHOW_ADMIN_ROLE') ? 'disabled="disabled" title="Predefined by \'URE_SHOW_ADMIN_ROLE\' constant at wp-config.php"' : ''; ?> /> 
        </td>
      </tr>
      <tr>
        <td><label for="caps_readable"><?php esc_html_e('Show capabilities in the human readable form:', 'ure'); ?></label></td>
        <td>
            <input type="checkbox" name="caps_readable" id="caps_readable" value="1" 
                <?php echo ($caps_readable==1) ? 'checked="checked"' : ''; ?> /> 
        </td>
      </tr>
      <tr>
        <td><label for="show_deprecated_caps"><?php esc_html_e('Show deprecated capabilities:', 'ure'); ?></label></td>
        <td>
            <input type="checkbox" name="show_deprecated_caps" id="show_deprecated_caps" value="1" 
                <?php echo ($show_deprecated_caps==1) ? 'checked="checked"' : ''; ?> /> 
        </td>
      </tr>      
<?php
    if ($this->lib->multisite) {
?>
      <tr>
        <td><label for="allow_edit_users_to_not_super_admin"><?php esc_html_e('Allow create, edit and delete user to not super-admininstrators:', 'ure'); ?></label></td>
        <td>
            <input type="checkbox" name="allow_edit_users_to_not_super_admin" id="allow_edit_users_to_not_super_admin" value="1" 
                <?php echo ($allow_edit_users_to_not_super_admin==1) ? 'checked="checked"' : ''; ?> /> 
        </td>
      </tr>      
      
<?php      
    }

    do_action('ure_settings_show');
?>
    </table>
    <?php wp_nonce_field('user-role-editor'); ?>   
    <p class="submit">
      <input type="submit" class="button-primary" name="user_role_editor_settings_update" value="<?php _e('Save', 'ure') ?>" />
    </p>  

  </form>  
</div>

