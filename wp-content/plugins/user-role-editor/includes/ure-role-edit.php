<?php

/*
 * 
 * User Role Editor plugin: role editor page
 * 
 */

if (!defined('URE_PLUGIN_URL')) {
  die;  // Silence is golden, direct call is prohibited
}

// create roles backup if it's not created yet
ure_makeRolesBackup();

if (!isset($ure_currentRole) || !$ure_currentRole) {
  if (isset($_REQUEST['user_role']) && $_REQUEST['user_role'] && isset($ure_roles[$_REQUEST['user_role']])) {
    $ure_currentRole = $_REQUEST['user_role'];
  } else {
    $ure_currentRole = $ure_rolesId[count($ure_rolesId) - 1];
  }
  $ure_currentRoleName = $ure_roles[$ure_currentRole]['name'];
}

$youAreAdmin = defined('URE_SHOW_ADMIN_ROLE') && ure_is_admin();

$roleDefaultHTML = '<select id="default_user_role" name="default_user_role" width="200" style="width: 200px">';
$roleToCopyHTML = '<select id="user_role_copy_from" name="user_role_copy_from" width="200" style="width: 200px">
  <option value="none" selected="selected">'.__('None', 'ure').'</option>';
$roleSelectHTML = '<select id="user_role" name="user_role" onchange="ure_role_change(this.value);">';
foreach ($ure_roles as $key=>$value) {
  $selected1 = ure_optionSelected($key, $ure_currentRole);
  $selected2 = ure_optionSelected($key, $defaultRole);
  if ($youAreAdmin || $key!='administrator') {
		$translated_name = __($value['name'], 'ure');  // get translation from URE language file, if exists
		if ($translated_name===$value['name']) { // get WordPress internal translation
			$translated_name = translate_user_role($translated_name);
		}
    $translated_name .= ' ('. $key .')';
    $roleSelectHTML .= '<option value="'.$key.'" '.$selected1.'>'.$translated_name.'</option>';    
    $roleDefaultHTML .= '<option value="'.$key.'" '.$selected2.'>'.$translated_name.'</option>';
    $roleToCopyHTML .= '<option value="'.$key.'" >'.$translated_name.'</option>';
  }
}
$roleSelectHTML .= '</select>';
$roleDefaultHTML .= '</select>';
$roleToCopyHTML .= '</select>';

$ure_rolesCanDelete = ure_getRolesCanDelete($ure_roles);
if ($ure_rolesCanDelete && count($ure_rolesCanDelete)>0) {
  $roleDeleteHTML = '<select id="del_user_role" name="del_user_role" width="200" style="width: 200px">';
  foreach ($ure_rolesCanDelete as $key=>$value) {
    $roleDeleteHTML .= '<option value="'.$key.'">'.__($value, 'ure').'</option>';
  }
  $roleDeleteHTML .= '</select>';
} else {
  $roleDeleteHTML = '';
}

$capabilityRemoveHTML = ure_getCapsToRemoveHTML();

?>

						<div class="has-sidebar-content">

<?php
						ure_displayBoxStart(__('Select Role and change its capabilities list', 'ure'), 'min-width:700px;');
?>
              <div style="float: left;"><?php echo __('Select Role:', 'ure').' '.$roleSelectHTML; ?></div>
<?php
  if ($ure_caps_readable) {
    $checked = 'checked="checked"';
  } else {
    $checked = '';
  }
?>
              <div style="display:inline;float:right;">
                <input type="checkbox" name="ure_caps_readable" id="ure_caps_readable" value="1" <?php echo $checked; ?> onclick="ure_turn_caps_readable(0);"/>
                <label for="ure_caps_readable"><?php _e('Show capabilities in human readable form', 'ure'); ?></label><br />
<?php
    if ($ure_show_deprecated_caps) {
      $checked = 'checked="checked"';
    } else {
      $checked = '';
    }
?>
                <input type="checkbox" name="ure_show_deprecated_caps" id="ure_show_deprecated_caps" value="1" <?php echo $checked; ?> onclick="ure_turn_deprecated_caps(0);"/>
                <label for="ure_show_deprecated_caps"><?php _e('Show deprecated capabilities', 'ure'); ?></label>
              </div>
<?php
if (is_multisite() && is_main_site( get_current_blog_id() ) && is_super_admin()) {
  $hint = __('If checked, then apply action to ALL sites of this Network');
  if ($ure_apply_to_all) {
    $checked = 'checked="checked"';
    $fontColor = 'color:#FF0000;';
  } else {
    $checked = '';
    $fontColor = '';
  }
?>
              <div style="float: right; margin-left:10px; margin-right: 20px; <?php echo $fontColor;?>" id="ure_apply_to_all_div"><input type="checkbox" name="ure_apply_to_all" id="ure_apply_to_all" value="1" <?php echo $checked; ?> title="<?php echo $hint;?>" onclick="ure_applyToAllOnClick(this)"/>
                <label for="ure_apply_to_all" title="<?php echo $hint;?>"><?php _e('Apply to All Sites', 'ure');?></label>
              </div>
<?php
}
?>
<br /><br />
<hr />
<?php _e('Core capabilities:', 'ure'); ?>
        <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
          <tr>
            <td style="vertical-align:top;">
								<?php ure_show_capabilities( true, true ); ?>
            </td>
						<td>
							<?php ure_toolbar($ure_currentRole, $ure_object, $roleDeleteHTML, $capabilityRemoveHTML);?>
						</td>
          </tr>
       </table>
<?php 
	$quant = count( $ure_fullCapabilities ) - count( ure_getBuiltInWPCaps() );
	if ($quant>0) {
		echo '<hr />';
		_e('Custom capabilities:', 'ure'); 
?>
        <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
          <tr>
            <td style="vertical-align:top;">
								<?php ure_show_capabilities( false, true );	?>
            </td>
						<td></td>
          </tr>
      </table>
<?php
	}  // if ($quant>0)
?>

  <input type="hidden" name="object" value="role" />
<?php
  ure_displayBoxEnd();
?>  
<div style="clear: left; float: left; width: 800px;">
</div>    
		</div>

<script language="javascript" type="text/javascript">

  var ure_current_role = '<?php echo $ure_currentRole; ?>';

</script>

<!-- popup dialogs markup -->
<div id="ure_add_role_dialog" class="ure-modal-dialog" style="padding: 10px;">
  <form id="ure_add_role_form" name="ure_add_role_form" method="POST">    
    <div class="ure-label"><?php echo __('Role name (ID): ', 'ure'); ?></div>
    <div class="ure-input"><input type="text" name="user_role_id" id="user_role_id" size="25"/></div>
    <div class="ure-label"><?php echo __('Display Role Name: ', 'ure'); ?></div>
    <div class="ure-input"><input type="text" name="user_role_name" id="user_role_name" size="25"/></div>
    <div class="ure-label"><?php echo __('Make copy of: ', 'ure'); ?></div>
    <div class="ure-input"><?php echo $roleToCopyHTML; ?></div>        
  </form>
</div>

<div id="ure_delete_role_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php _e('Select Role:', 'ure');?></div>
    <div class="ure-input"><?php echo $roleDeleteHTML; ?></div>
  </div>
</div>


<div id="ure_default_role_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <?php echo $roleDefaultHTML; ?>
  </div>  
</div>


<div id="ure_delete_capability_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php _e('Delete:', 'ure');?></div>
    <div class="ure-input"><?php echo $capabilityRemoveHTML; ?></div>
  </div>  
</div>

<div id="ure_add_capability_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php echo __('Capability name (ID): ', 'ure'); ?></div>
    <div class="ure-input"><input type="text" name="capability_id" id="capability_id" size="25"/></div>
  </div>  
</div>