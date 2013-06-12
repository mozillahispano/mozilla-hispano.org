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
<?php
  $userInfo = ' <span style="font-weight: bold;"><a href="' . wp_nonce_url("user-edit.php?user_id={$ure_userToEdit->ID}", "ure_user_{$ure_userToEdit->ID}") .'" >' . $ure_userToEdit->user_login; 
  if ($ure_userToEdit->display_name!==$ure_userToEdit->user_login) {
    $userInfo .= ' ('.$ure_userToEdit->display_name.')';
  }
  $userInfo .= '</a></span>';
	ure_displayBoxStart(__('Change capabilities for user', 'ure').$userInfo, 'min-width:810px;');
 
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
  
		<input type="checkbox" name="ure_caps_readable" id="ure_caps_readable" value="1" <?php echo $checked; ?> onclick="ure_turn_caps_readable(<?php echo $ure_userToEdit->ID; ?>);"  />
    <label for="ure_caps_readable"><?php _e('Show capabilities in human readable form', 'ure'); ?></label>&nbsp;&nbsp;&nbsp;
<?php
    if ($ure_show_deprecated_caps) {
      $checked = 'checked="checked"';
    } else {
      $checked = '';
    }
?>
    <input type="checkbox" name="ure_show_deprecated_caps" id="ure_show_deprecated_caps" value="1" <?php echo $checked; ?> onclick="ure_turn_deprecated_caps(<?php echo $ure_userToEdit->ID; ?>);"/>
    <label for="ure_show_deprecated_caps"><?php _e('Show deprecated capabilities', 'ure'); ?></label>      
		</td>
	</tr>
	<tr>
		<td class="ure-user-roles">
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
			<td>
				<?php ure_toolbar($ure_currentRole, $ure_object);?>
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
<?php
	}  // if ($quant>0)
?>
		</td>
	</tr>
</table>
  <input type="hidden" name="object" value="user" />
  <input type="hidden" name="user_id" value="<?php echo $ure_userToEdit->ID; ?>" />
<?php
  ure_displayBoxEnd();
?>
  
</div>

