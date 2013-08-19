<?php
/* 
 * User Role Editor plugin uninstall script
 * Author: vladimir@shinephp.com
 *
 */


if (!defined('ABSPATH') || !defined('WP_UNINSTALL_PLUGIN')) {
	 exit();  // silence is golden
}

global $wpdb;

if (!is_multisite()) {
  $backup_option_name = $wpdb->prefix.'backup_user_roles';
  delete_option($backup_option_name);
  delete_option('ure_caps_readable');
  delete_option('ure_show_deprecated_caps');
  delete_option('ure_hide_pro_banner');
  delete_option('user_role_editor');
} else {
  $old_blog = $wpdb->blogid;
  // Get all blog ids
  $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
  foreach ($blogIds as $blog_id) {
    switch_to_blog($blog_id);
    $backup_option_name = $wpdb->prefix.'backup_user_roles';
    delete_option($backup_option_name);
    delete_option('ure_caps_readable');
    delete_option('ure_show_deprecated_caps');      
    delete_option('ure_hide_pro_banner');
    delete_option('user_role_editor');
  }
  switch_to_blog($old_blog);
}

?>
