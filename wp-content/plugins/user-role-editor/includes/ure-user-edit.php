<?php

/*
 * 
 * User Role Editor plugin: user capabilities editor page
 * 
 */

if (!defined('URE_PLUGIN_URL')) {
  die;  // Silence is golden, direct call is prohibited
}

?>

<div class="has-sidebar-content">
<script language="javascript" type="text/javascript">
  function ure_Actions(action, value) {
    var url = '<?php echo URE_WP_ADMIN_URL.'/'.URE_PARENT; ?>?page=user-role-editor.php&object=user&user_id=<?php echo $ure_userToEdit->ID; ?>';
    if (action=='cancel') {
      document.location = url;
      return true;
    } if (action!='update') {
      url += '&action='+ action;
      if (value!='' && value!=undefined) {
        url = url +'&user_role='+ escape(value);
      }
      document.location = url;
    } else {
      document.getElementById('ure-form').submit();
    }
    
  }// end of ure_Actions()


  function ure_onSubmit() {
    if (!confirm('<?php echo sprintf(__('User "%s" update: please confirm to continue', 'ure'), $ure_userToEdit->display_name); ?>')) {
      return false;
    }
  }

</script>
<?php
  $userInfo = ' <span style="font-weight: bold;"><a href="' . wp_nonce_url("user-edit.php?user_id={$ure_userToEdit->ID}", "ure_user_{$ure_userToEdit->ID}") .'" >' . $ure_userToEdit->user_login; 
  if ($ure_userToEdit->display_name!==$ure_userToEdit->user_login) {
    $userInfo .= ' ('.$ure_userToEdit->display_name.')';
  }
  $userInfo .= '</a></span>';
	ure_displayBoxStart(__('Change capabilities for user', 'ure').$userInfo);
 
?>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td>&nbsp;</td>		
		<td style="padding-left: 10px; padding-bottom: 5px;">
  <?php
  if ($ure_caps_readable) {
    $checked = 'checked="checked"';
  } else {
    $checked = '';
  }
?>
  
		<input type="checkbox" name="ure_caps_readable" id="ure_caps_readable" value="1" <?php echo $checked; ?> onclick="ure_Actions('capsreadable');" />
    <label for="ure_caps_readable"><?php _e('Show capabilities in human readable form', 'ure'); ?></label>&nbsp;&nbsp;&nbsp;
<?php
    if ($ure_show_deprecated_caps) {
      $checked = 'checked="checked"';
    } else {
      $checked = '';
    }
?>
    <input type="checkbox" name="ure_show_deprecated_caps" id="ure_show_deprecated_caps" value="1" <?php echo $checked; ?> onclick="ure_Actions('showdeprecatedcaps');"/>
    <label for="ure_show_deprecated_caps"><?php _e('Show deprecated capabilities', 'ure'); ?></label>      
		</td>
	</tr>
	<tr>
		<td style="vertical-align: text-top; padding-right: 10px; padding-top: 5px; font-size: 1.1em; border-top: 1px solid #ccc; border-right: 1px solid #ccc;">
			<div style="margin-bottom: 5px; font-weight: bold;"><?php echo __('Primary Role:', 'ure'); ?></div>
<?php 
$primary_role = array_shift(array_values($ure_userToEdit->roles));  // get 1st element from roles array
if (!empty($primary_role) && isset($ure_roles[$primary_role])) {
	echo $ure_roles[$primary_role]['name']; 
} else {
	echo 'None';
}
if (function_exists('bbp_filter_blog_editable_roles') ) {  // bbPress plugin is active
?>	
	<div style="margin-top: 5px;margin-bottom: 5px; font-weight: bold;"><?php echo __('bbPress Role:', 'ure'); ?></div>
<?php
	// Get the roles
	$dynamic_roles = bbp_get_dynamic_roles();
	$bbp_user_role = bbp_get_user_role($ure_userToEdit->ID);
	if (!empty($bbp_user_role)) {
		echo $dynamic_roles[$bbp_user_role]['name']; 
	}
}
?>
			<div style="margin-top: 5px;margin-bottom: 5px; font-weight: bold;"><?php echo __('Other Roles:', 'ure'); ?></div>
<?php
	$youAreAdmin = defined('URE_SHOW_ADMIN_ROLE') && ure_is_admin();
	foreach ($ure_roles as $role_id => $role) {
		if ( ($youAreAdmin || $role_id!='administrator') && ($role_id!==$primary_role) ) {			
			if ( user_can( $ure_userToEdit->ID, $role_id ) ) {
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			echo '<label for="wp_role_' . $role_id .'"><input type="checkbox"	id="wp_role_' . $role_id . '" name="wp_role_' . $role_id . '" value="' . $role_id . '"' . $checked .' />&nbsp;' . __($role['name'], 'ure') . '</label><br />';
		}		
	}
?>
		</td>
		<td style="padding-left: 5px; padding-top: 5px; border-top: 1px solid #ccc;">  
	<span style="font-weight: bold;"><?php _e('Core capabilities:', 'ure'); ?></span>
  <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="vertical-align:top;">
				<?php ure_show_capabilities( true, false ); ?>
      </td>
    </tr>
  </table>
<?php 
	$quant = count( $ure_fullCapabilities ) - count( ure_getBuiltInWPCaps() );
	if ($quant>0) {		
?>
	<span style="font-weight: bold;"><?php _e('Custom capabilities:', 'ure'); ?></span> 
  <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="vertical-align:top;">
				<?php ure_show_capabilities( false, false ); ?>
      </td>
    </tr>
  </table>	
		</td>
	</tr>
	<tr>
		<td colspan="2" style="border-top: 1px solid #ccc;">
<?php
	}  // if ($quant>0)
?>
  <input type="hidden" name="object" value="user" />
  <input type="hidden" name="user_id" value="<?php echo $ure_userToEdit->ID; ?>" />
  <div class="submit" style="padding-top: 0px;">
    <div style="float:left; padding-bottom: 10px;">
        <input type="submit" name="submit" value="<?php _e('Update', 'ure'); ?>" title="<?php _e('Save Changes', 'ure'); ?>" />
        <input type="button" name="cancel" value="<?php _e('Cancel', 'ure') ?>" title="<?php _e('Cancel not saved changes','ure');?>" onclick="ure_Actions('cancel');"/>
    </div>
  </div>
		</td>
	</tr>
</table>
<?php
  ure_displayBoxEnd();
?>
  
</div>

