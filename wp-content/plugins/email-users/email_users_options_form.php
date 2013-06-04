<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/*  Copyright 2006 Vincent Prat  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>

<?php 
	wp_enqueue_script('postbox');
    wp_enqueue_style('dashboard');
    wp_enqueue_script('dashboard');

	if (!current_user_can('manage_options')) {
        wp_die(printf('<div class="error fade"><p>%s</p></div>',
            __('You are not allowed to change the options of this plugin.', MAILUSERS_I18N_DOMAIN)));

	} 
	
	if ( mailusers_get_installed_version() != mailusers_get_current_version() ) {
?>
	<div class="error fade">
		<p><?php _e('It looks like you have an old version of the plugin activated. Please deactivate the plugin and activate it again to complete the installation of the new version.', MAILUSERS_I18N_DOMAIN); ?>
	</p>		
	<p>
		<?php _e('Installed Version:', MAILUSERS_I18N_DOMAIN); ?> <?php echo mailusers_get_installed_version(); ?> <br/>
		<?php _e('Current Version:', MAILUSERS_I18N_DOMAIN); ?> <?php echo mailusers_get_current_version(); ?>
	</p>
	</div>		
<?php
	}
?>

<div class="wrap"><!-- wrap -->


<?php if (function_exists('screen_icon')) screen_icon(); ?>
<h2><?php _e('Email Users Settings', MAILUSERS_I18N_DOMAIN); ?></h2>

<?php 	
	if (isset($err_msg) && $err_msg!='') { ?>
		<div class="error fade"><p><?php echo $err_msg;?></p></div>
		<p><?php _e('Please correct the errors displayed above and try again.', MAILUSERS_I18N_DOMAIN); ?></p>
<?php	
	} ?>

<div> <!-- Postbox Containers -->
<div class="postbox-container" style="width:65%; border: 0px dashed blue;"><!-- 65% Postbox Container -->
<div class="metabox-holder">
<div class="meta-box-sortables">
<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Email-Users Plugin Settings', MAILUSERS_I18N_DOMAIN);?></span></h3>
<div class="inside">

<form name="EmailUsersOptions" action="options.php" method="post">		
	<?php settings_fields('email_users') ;?>
	<input type="hidden" name="mailusers_version" value="<?php echo mailusers_get_current_version(); ?>" />
	<table class="form-table" style="clear:none;" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th scope="row" valign="top">
			<label for="mail_format"><?php _e('Mail Format', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<select name="mailusers_default_mail_format" style="width: 235px;">
				<option value="html" <?php if (mailusers_get_default_mail_format()=='html') echo 'selected="true"';?>><?php _e('HTML', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="plaintext" <?php if (mailusers_get_default_mail_format()=='plaintext') echo 'selected="true"';?>><?php _e('Plain text', MAILUSERS_I18N_DOMAIN); ?></option>
			</select><br/>&nbsp;<?php _e('Send mail as plain text or HTML by default?', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<th scope="row" valign="top">
			<label for="sort_users_by"><?php _e('Sort Users By', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<select name="mailusers_default_sort_users_by" style="width: 235px;">
				<option value="none" <?php if (mailusers_get_default_sort_users_by()=='none') echo 'selected="true"';?>><?php _e('None', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="dn" <?php if (mailusers_get_default_sort_users_by()=='dn') echo 'selected="true"';?>><?php _e('Display Name', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="dnul" <?php if (mailusers_get_default_sort_users_by()=='dnul') echo 'selected="true"';?>><?php _e('Display Name (User Login)', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="fl" <?php if (mailusers_get_default_sort_users_by()=='fl') echo 'selected="true"';?>><?php _e('First Name Last Name', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="flul" <?php if (mailusers_get_default_sort_users_by()=='flul') echo 'selected="true"';?>><?php _e('First Name Last Name (User Login)', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="lf" <?php if (mailusers_get_default_sort_users_by()=='lf') echo 'selected="true"';?>><?php _e('Last Name, First Name', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="lful" <?php if (mailusers_get_default_sort_users_by()=='lful') echo 'selected="true"';?>><?php _e('Last Name, First Name (User Login)', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="ul" <?php if (mailusers_get_default_sort_users_by()=='ul') echo 'selected="true"';?>><?php _e('User Login', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="uldn" <?php if (mailusers_get_default_sort_users_by()=='uldn') echo 'selected="true"';?>><?php _e('User Login (Display Name)', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="ulfl" <?php if (mailusers_get_default_sort_users_by()=='ulfl') echo 'selected="true"';?>><?php _e('User Login (First Name Last Name)', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="ullf" <?php if (mailusers_get_default_sort_users_by()=='ullf') echo 'selected="true"';?>><?php _e('User Login (Last Name, First Name)', MAILUSERS_I18N_DOMAIN); ?></option>
			</select><br/>&nbsp;<?php _e('Determine how to sort and display names in the User selection list?', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<th scope="row" valign="top">
			<label for="max_bcc_recipients"><?php _e('BCC Limit', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<select name="mailusers_max_bcc_recipients" style="width: 235px;">
				<option value="0" <?php if (mailusers_get_max_bcc_recipients()=='0') echo 'selected="true"';?>><?php _e('None', MAILUSERS_I18N_DOMAIN); ?></option>
				<option value="1" <?php if (mailusers_get_max_bcc_recipients()=='1') echo 'selected="true"';?>>1</option>
				<option value="10" <?php if (mailusers_get_max_bcc_recipients()=='10') echo 'selected="true"';?>>10</option>
				<option value="30" <?php if (mailusers_get_max_bcc_recipients()=='30') echo 'selected="true"';?>>30</option>
				<option value="100" <?php if (mailusers_get_max_bcc_recipients()=='100') echo 'selected="true"';?>>100</option>
			</select><br/>&nbsp;<?php _e('Try 30 if you have problems sending emails to many users (some providers forbid too many recipients in BCC field).', MAILUSERS_I18N_DOMAIN); ?>
		</td>
	</tr>
	<tr>
		<th scope="row" valign="top">
            <label for="default_subject"><?php _e('Default<br/>Notification Subject', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input type="text" name="mailusers_default_subject" style="width: auto;" 
				value="<?php echo format_to_edit(mailusers_get_default_subject()); ?>" 
				size="80" /></td>
	</tr>
	<tr>
        <th><?php _e('From Sender<br/>Exclude', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input 	type="checkbox" name="mailusers_from_sender_exclude" id="mailusers_from__sender_exclude" value="true"
					<?php if (mailusers_get_from_sender_exclude()=='true') echo 'checked="checked"';?> ></input>
			<?php _e('Exclude sender from email recipient list.', MAILUSERS_I18N_DOMAIN); ?><br/>
		</td>
	</tr>
	<tr>
		<th scope="row" valign="top">
            <label for="from_sender_name_override"><?php _e('From Sender<br/>Name Override', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input type="text" name="mailusers_from_sender_name_override" style="width: 235px;" 
				value="<?php echo format_to_edit(mailusers_get_from_sender_name_override()); ?>" 
				size="80" id="from_sender_name_override"/><br/>&nbsp;<?php _e('A name that can be used in place of the logged in user\'s name when sending email or notifications.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<th scope="row" valign="top">
            <label for="from_sender_address_override"><?php _e('From Sender Email<br/>Address Override', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input type="text" name="mailusers_from_sender_address_override" style="width: 235px;" 
				value="<?php echo format_to_edit(mailusers_get_from_sender_address_override()); ?>" 
                size="80" id="from_sender_address_override"/><br/>&nbsp;<?php _e('An email address that can be used in place of the logged in user\'s email address when sending email or notifications.', MAILUSERS_I18N_DOMAIN); ?><br/><b><i><?php _e('Note:  Invalid email addresses are not saved.', MAILUSERS_I18N_DOMAIN); ?></i></b></td>
	</tr>
	<tr>
		<th scope="row" valign="top">
            <label for="send_bounces_to_address_override"><?php _e('Send Bounces To Email<br/>Address Override', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input type="text" name="mailusers_send_bounces_to_address_override" style="width: 235px;" 
				value="<?php echo format_to_edit(mailusers_get_send_bounces_to_address_override()); ?>" 
                size="80" id="from_sender_address_override"/><br/>&nbsp;<?php _e('An email address that can be used in place of the logged in user\'s email address to receive bounced email notifications.', MAILUSERS_I18N_DOMAIN); ?><br/><b><i><?php _e('Note:  Invalid email addresses are not saved.', MAILUSERS_I18N_DOMAIN); ?></i></b></td>
	</tr>
	<tr>
		<th scope="row" valign="top">
            <label for="mailusers_default_body"><?php _e('Default<br/>Notification Body', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
            <?php wp_editor(stripslashes(mailusers_get_default_body()), "mailusers_default_body");?>

		</td>
	</tr>
	<tr>
        <th><?php _e('Short Code<br/>Processing', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input 	type="checkbox" name="mailusers_shortcode_processing" id="mailusers_shortcode_processing" value="true"
					<?php if (mailusers_get_shortcode_processing()=='true') echo 'checked="checked"';?> ></input>
			<?php _e('Process short codes embedded in posts or pages.', MAILUSERS_I18N_DOMAIN); ?><br/>
		</td>
	</tr>
	<tr>
		<th scope="row" valign="top">
            <label for="user_settings_table_rows"><?php _e('User Settings<br/>Table Rows', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<select name="mailusers_user_settings_table_rows" style="width: 100px;">
                <option value="10" <?php if (mailusers_get_user_settings_table_rows()=='10') echo 'selected="true"'; ?>><?php _e('10', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="20" <?php if (mailusers_get_user_settings_table_rows()=='20') echo 'selected="true"'; ?>><?php _e('20', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="40" <?php if (mailusers_get_user_settings_table_rows()=='40') echo 'selected="true"'; ?>><?php _e('40', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="50" <?php if (mailusers_get_user_settings_table_rows()=='50') echo 'selected="true"'; ?>><?php _e('50', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="75" <?php if (mailusers_get_user_settings_table_rows()=='75') echo 'selected="true"'; ?>><?php _e('75', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="100" <?php if (mailusers_get_user_settings_table_rows()=='100') echo 'selected="true"'; ?>><?php _e('100', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="200" <?php if (mailusers_get_user_settings_table_rows()=='200') echo 'selected="true"'; ?>><?php _e('200', MAILUSERS_I18N_DOMAIN); ?></option>
                <option value="500" <?php if (mailusers_get_user_settings_table_rows()=='500') echo 'selected="true"'; ?>><?php _e('500', MAILUSERS_I18N_DOMAIN); ?></option>
			</select><br/>&nbsp;<?php _e('By default the table will display 20 rows.', MAILUSERS_I18N_DOMAIN); ?>
		</td>
	</tr>
	<tr>
    <th><?php _e('Default<br/>User Settings', MAILUSERS_I18N_DOMAIN); ?></th>
		<td>
			<input 	type="checkbox" name="mailusers_default_notifications" id="mailusers_default_notifications" value="true"
					<?php if (mailusers_get_default_notifications()=='true') echo 'checked="checked"';?> ></input>
			<?php _e('Receive post or page notification emails.', MAILUSERS_I18N_DOMAIN); ?><br/>
			<input 	type="checkbox"
					name="mailusers_default_mass_email" id="mailusers_default_mass_email" value="true"
					<?php if (mailusers_get_default_mass_email()=='true') echo 'checked="checked"';?> ></input>
			<?php _e('Receive emails sent to multiple recipients.', MAILUSERS_I18N_DOMAIN); ?><br/>
			<input 	type="checkbox"
					name="mailusers_default_user_control" id="mailusers_default_user_control" value="true"
					<?php if (mailusers_get_default_user_control()=='true') echo 'checked="checked"';?> ></input>
			<?php _e('Allow Users to control their own Email Users settings.', MAILUSERS_I18N_DOMAIN); ?>
		</td>
	</tr>
	</table>

	<p class="submit">
		<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', MAILUSERS_I18N_DOMAIN); ?> &raquo;" />
	</p>
</form>	

<br class="clear"/>

</div><!-- inside -->
</div><!-- postbox -->

<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Email-Users Defaults', MAILUSERS_I18N_DOMAIN); ?></span></h3>
<div class="inside">
<p><?php _e('Email-Users has default values for all settings.  When reset, the following values are used.', MAILUSERS_I18N_DOMAIN); ?></p>
<table class="widefat">
	<thead>
	<tr>
		<th><?php _e('Settings', MAILUSERS_I18N_DOMAIN); ?></th>
		<th><?php _e('Default Value', MAILUSERS_I18N_DOMAIN); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	$default_settings = mailusers_get_default_plugin_settings();
	foreach ($default_settings as $key => $value) {
?>
	<tr>
		<td width="200px"><b><?php echo ucwords(preg_replace(array('/mailusers_/', '/_/'), array('', ' '), $key)); ?></b></td>
		<td><?php echo htmlentities($value); ?></td>
	</tr>
<?php
	}
?>
	</tbody>
</table>
<form name="ResetPluginSettings" action="" method="post">
	<p class="submit">
		<input type="hidden" name="resetpluginsettings" value="true" />
		<input class="button-primary" type="submit" name="Submit" value="<?php _e('Apply Default Settings', MAILUSERS_I18N_DOMAIN); ?> &raquo;" />
	</p>
</form>	

<br class="clear"/>

</div><!-- inside -->
</div><!-- postbox -->

<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Email-Users Test Notification Mail', MAILUSERS_I18N_DOMAIN); ?></span></h3>
<div class="inside">
<p><?php _e('Use this Test Notification to verify proper operation of Email-Users.', MAILUSERS_I18N_DOMAIN);?></p>
<table class="widefat">
	<thead>
	<tr>
		<th colspan="2"><?php _e('Notification Mail Preview (updated when the options are saved)', MAILUSERS_I18N_DOMAIN); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	global $wpdb;
	$post_id = $wpdb->get_var("select max(id) from $wpdb->posts where post_type='post'");
	if (!isset($post_id)) {
?>
	<tr>
		<td colspan="2"><?php _e('No post found in the blog in order to build a notification preview.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
<?php
	} else {						
		$subject = mailusers_get_default_subject();
		$mail_content = mailusers_get_default_body();

		// Replace the template variables concerning the blog details
		// --
		$subject = mailusers_replace_blog_templates($subject);
		$mail_content = mailusers_replace_blog_templates($mail_content);
			
		// Replace the template variables concerning the sender details
		// --	
		get_currentuserinfo();
		global $user_identity, $user_email ;

		$from_name = $user_identity;
		$from_address = $user_email;
		$subject = mailusers_replace_sender_templates($subject, $from_name);
		$mail_content = mailusers_replace_sender_templates($mail_content, $from_name);
	
		$post = get_post( $post_id );
		$post_title = $post->post_title;
		$post_url = get_permalink( $post_id );			
		$post_content = explode( '<!--more-->', $post->post_content, 2 );
		$post_excerpt = $post_content[0];
		
		$subject = mailusers_replace_post_templates($subject, $post_title, $post_excerpt, $post_url);
		$mail_content = mailusers_replace_post_templates($mail_content, $post_title, $post_excerpt, $post_url);
?>
	<tr>
		<td><b><?php _e('Subject', MAILUSERS_I18N_DOMAIN); ?></b></td>
		<td><?php echo mailusers_get_default_mail_format()=='html' ? $subject : '<pre>' . format_to_edit($subject) . '</pre>';?></td>
	</tr>
	<tr>
		<td><b><?php _e('Message', MAILUSERS_I18N_DOMAIN); ?></b></td>
		<td><?php echo mailusers_get_default_mail_format()=='html' ? $mail_content : '<pre>' . wordwrap(strip_tags($mail_content), 80, "\n") . '</pre>';?></td>
	</tr>
<?php
	}
?>
	</tbody>
</table>
<form name="SendTestEmail" action="" method="post">
	<p class="submit">
		<input type="hidden" name="sendtestemail" value="true" />
		<input class="button-primary" type="submit" name="Submit" value="<?php _e('Send Test Notification to Yourself', MAILUSERS_I18N_DOMAIN); ?> &raquo;" />
	</p>
</form>	
<br class="clear"/>

</div><!-- inside -->
</div><!-- postbox -->

<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Email-Users Variables', MAILUSERS_I18N_DOMAIN);?></span></h3>
<div class="inside">
<p><?php _e('Variables you can include in the subject or body templates', MAILUSERS_I18N_DOMAIN); ?></p>
<table class="widefat">
	<thead>
	<tr>
		<th><?php _e('Variable', MAILUSERS_I18N_DOMAIN); ?></th>
		<th><?php _e('Description', MAILUSERS_I18N_DOMAIN); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><b>%BLOG_URL%</b></td>
		<td><?php _e('the link to the blog', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b>%BLOG_NAME%</b></td>
		<td><?php _e('the blog\'s name', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b>%FROM_NAME%</b></td>
		<td><?php _e('the WordPress user name of the person sending the mail', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b>%POST_TITLE%</b></td>
		<td><?php _e('the title of the post you want to highlight', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b>%POST_EXCERPT%</b></td>
		<td><?php _e('the excerpt of the post you want to highlight', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b>%POST_URL%</b></td>
		<td><?php _e('the link to the post you want to highlight', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	</tbody>
</table>
<br class="clear"/>

</div><!-- inside -->
</div><!-- postbox -->

<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Email-Users Capabilities', MAILUSERS_I18N_DOMAIN);?></span></h3>
<div class="inside">

<p><?php _e('Email Users uses capabilities to define what users are allowed to do. Below is a list of the capabilities used by the plugin and the default user role allowed to make these actions.', MAILUSERS_I18N_DOMAIN); ?> <?php _e('If you want to change the roles having those capabilities, you should use the plugin:', MAILUSERS_I18N_DOMAIN); ?> <a href="http://www.im-web-gefunden.de/wordpress-plugins/role-manager/" target="_blank">Role Manager</a></p>

<table class="widefat">
	<thead>
	<tr>
		<th><?php _e('Capability', MAILUSERS_I18N_DOMAIN); ?></th>
		<th><?php _e('Description', MAILUSERS_I18N_DOMAIN); ?></th>
		<th><?php _e('Default Roles', MAILUSERS_I18N_DOMAIN); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><b>manage-options</b></td>
		<td><?php _e('Access this options page.', MAILUSERS_I18N_DOMAIN); ?></td>
		<td><?php _e('Administrators only.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b><?php echo MAILUSERS_EMAIL_SINGLE_USER_CAP;?></b></td>
		<td><?php _e('Send an email to a single user.', MAILUSERS_I18N_DOMAIN); ?></td>
		<td><?php _e('Administrators, editors, authors and contributors.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b><?php echo MAILUSERS_EMAIL_MULTIPLE_USERS_CAP; ?></b></td>
		<td><?php _e('Send an email to various users at the same time.', MAILUSERS_I18N_DOMAIN); ?></td>
		<td><?php _e('Administrators, editors and authors.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b><?php echo MAILUSERS_NOTIFY_USERS_CAP; ?></b></td>
		<td><?php _e('Notify users of new posts.', MAILUSERS_I18N_DOMAIN); ?></td>
		<td><?php _e('Administrators and editors.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	<tr>
		<td><b><?php echo MAILUSERS_EMAIL_USER_GROUPS_CAP; ?></b></td>
		<td><?php _e('Send an email to user groups.', MAILUSERS_I18N_DOMAIN); ?></td>
		<td><?php _e('Administrators and editors.', MAILUSERS_I18N_DOMAIN); ?></td>
	</tr>
	</tbody>
</table>

<br/>
</div><!-- inside -->
</div><!-- postbox -->
</div><!-- meta-box-sortables -->
</div><!-- metabox-holder -->
</div><!-- 65% Postbox Container -->
<div class="postbox-container side" style="margin-left: 10px; min-width: 225px; width:25%; border: 0px dashed red;"><!-- 25% Postbox Container -->
<div class="metabox-holder">
<div class="meta-box-sortables">
<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Make a Donation', MAILUSERS_I18N_DOMAIN);?></span></h3>
<div class="inside">

<div style="text-align: center; font-size: 0.75em;padding:0px 5px;margin:0px auto;"><!-- PayPal box wrapper -->
<div><!-- PayPal box-->
	<p style="margin: 0.25em 0"><b>Email Users <?php echo mailusers_get_current_version(); ?></b></p>
	<p style="margin: 0.25em 0"><a href="http://email-users.vincentprat.info" target="_blank"><?php _e('Plugin\'s Home Page', MAILUSERS_I18N_DOMAIN); ?></a></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="EYKMSYDUL746U">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</div><!-- PayPal box -->
</div>

</div><!-- inside -->
</div><!-- postbox -->
<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('More Plugins from Mike Walsh', MAILUSERS_I18N_DOMAIN); ?></span></h3>
<div class="inside" style="">
<div style="padding:0px 5px;">
<div>
	<ul style="list-style-type: square;margin-left: 7px;">
		<li><?php _e('if you use Google Forms and want to integrate them with your WordPress site, try: ', MAILUSERS_I18N_DOMAIN); ?><a href="http://michaelwalsh.org/wordpress/wordpress-plugins/wpgform/">WordPress Google Form</a></li>
	</ul>
</div>
</div>
</div><!-- inside -->
</div><!-- postbox -->

<div class="postbox">
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class="hndle"><span><?php _e('Discover other Plugins by MarvinLabs', MAILUSERS_I18N_DOMAIN); ?></span></h3>
<div class="inside" style="">
<div style="padding:0px 5px;">
<div>
	<ul style="list-style-type: square;margin-left: 7px;">
		<li><?php _e('If Email-Users is not robust enough or if you want to allow your users to communicate with each other, try: ', MAILUSERS_I18N_DOMAIN); ?><a href="http://user-messages.marvinlabs.com">User Messages</a></li>
		<li><?php _e('If you lose time copy/pasting the same post structure every time, try: ', MAILUSERS_I18N_DOMAIN); ?><a href="http://post-templates.marvinlabs.com">Post Templates</a></li>
	</ul>
</div>
</div>
</div><!-- inside -->
</div><!-- postbox -->
</div><!-- meta-box-sortables -->
</div><!-- metabox-holder -->
</div><!-- 25% Postbox Container -->
</div><!-- Postbox Containers -->
</div><!-- wrap -->
