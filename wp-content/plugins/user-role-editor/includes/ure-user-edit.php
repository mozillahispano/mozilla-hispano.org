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
	if (!is_multisite() || current_user_can('manage_network_users')) {
		$anchor_start = '<a href="' . wp_nonce_url("user-edit.php?user_id={$this->user_to_edit->ID}", 
          "ure_user_{$this->user_to_edit->ID}") .'" >';
		$anchor_end = '</a>';
	} else {
		$anchor_start = '';
		$anchor_end = '';
	}
  $user_info = ' <span style="font-weight: bold;">'.$anchor_start. $this->user_to_edit->user_login; 
  if ($this->user_to_edit->display_name!==$this->user_to_edit->user_login) {
    $user_info .= ' ('.$this->user_to_edit->display_name.')';
  }
  $user_info .= $anchor_end.'</span>';
 if (is_multisite() && is_super_admin($this->user_to_edit->ID)) {
   $user_info .= '  <span style="font-weight: bold; color:red;">'. esc_html__('Network Super Admin', 'ure') .'</span>';
 }
  
	 $this->display_box_start(__('Change capabilities for user', 'ure').$user_info, 'min-width:830px;');
 
?>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td>&nbsp;</td>		
		<td style="padding-left: 10px; padding-bottom: 5px;">
  <?php
  if ($this->caps_readable) {
    $checked = 'checked="checked"';
  } else {
    $checked = '';
  }
?>
  
		<input type="checkbox" name="ure_caps_readable" id="ure_caps_readable" value="1" 
      <?php echo $checked; ?> onclick="ure_turn_caps_readable(<?php echo $this->user_to_edit->ID; ?>);"  />
    <label for="ure_caps_readable"><?php _e('Show capabilities in human readable form', 'ure'); ?></label>&nbsp;&nbsp;&nbsp;
<?php
    if ($this->show_deprecated_caps) {
      $checked = 'checked="checked"';
    } else {
      $checked = '';
    }
?>
    <input type="checkbox" name="ure_show_deprecated_caps" id="ure_show_deprecated_caps" value="1" 
        <?php echo $checked; ?> onclick="ure_turn_deprecated_caps(<?php echo $this->user_to_edit->ID; ?>);"/>
    <label for="ure_show_deprecated_caps"><?php _e('Show deprecated capabilities', 'ure'); ?></label>      
		</td>
	</tr>	
	<tr>
		<td class="ure-user-roles">
			<div style="margin-bottom: 5px; font-weight: bold;"><?php echo __('Primary Role:', 'ure'); ?></div>
<?php 
$values = array_values($this->user_to_edit->roles);
$primary_role = array_shift($values);  // get 1st element from roles array
if (!empty($primary_role) && isset($this->roles[$primary_role])) {
	echo $this->roles[$primary_role]['name']; 
} else {
	echo 'None';
}
if (function_exists('bbp_filter_blog_editable_roles') ) {  // bbPress plugin is active
?>	
	<div style="margin-top: 5px;margin-bottom: 5px; font-weight: bold;"><?php echo __('bbPress Role:', 'ure'); ?></div>
<?php
	// Get the roles
	$dynamic_roles = bbp_get_dynamic_roles();
	$bbp_user_role = bbp_get_user_role($this->user_to_edit->ID);
	if (!empty($bbp_user_role)) {
		echo $dynamic_roles[$bbp_user_role]['name']; 
	}
}
?>
			<div style="margin-top: 5px;margin-bottom: 5px; font-weight: bold;"><?php echo __('Other Roles:', 'ure'); ?></div>
<?php
 $show_admin_role = $this->get_option('show_admin_role', 0);
	$you_are_admin = ((defined('URE_SHOW_ADMIN_ROLE') && URE_SHOW_ADMIN_ROLE==1) || $show_admin_role==1) && $this->user_is_admin();
	foreach ($this->roles as $role_id => $role) {
		if ( ($you_are_admin || $role_id!='administrator') && ($role_id!==$primary_role) ) {			
			if ( $this->user_can( $role_id ) ) {
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			echo '<label for="wp_role_' . $role_id .'"><input type="checkbox"	id="wp_role_' . $role_id . 
        '" name="wp_role_' . $role_id . '" value="' . $role_id . '"' . $checked .' />&nbsp;' . 
        __($role['name'], 'ure') . '</label><br />';
		}		
	}
 ?>
		</td>
		<td style="padding-left: 5px; padding-top: 5px; border-top: 1px solid #ccc;">  
	<span style="font-weight: bold;"><?php _e('Core capabilities:', 'ure'); ?></span>		
	<div style="display:table-inline; float: right; margin-right: 12px;">
		<?php _e('Quick filter:', 'ure'); ?>&nbsp;
		<input type="text" id="quick_filter" name="quick_filter" value="" size="20" onkeyup="ure_filter_capabilities(this.value);" />
	</div>		
	
  <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="vertical-align:top;">
				<?php $this->show_capabilities( true, false ); ?>
      </td>
			<td>
				<?php $this->toolbar();?>
			</td>
    </tr>
  </table>
<?php 
	$quant = count( $this->full_capabilities ) - count( $this->get_built_in_wp_caps() );
	if ($quant>0) {		
     echo '<hr />';
?> 
	<span style="font-weight: bold;"><?php _e('Custom capabilities:', 'ure'); ?></span> 
  <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="vertical-align:top;">
				<?php $this->show_capabilities( false, false ); ?>
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
  <input type="hidden" name="user_id" value="<?php echo $this->user_to_edit->ID; ?>" />
<?php
  $this->display_box_end();
?>
  
</div>