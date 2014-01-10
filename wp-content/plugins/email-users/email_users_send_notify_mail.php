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
	if (!current_user_can(MAILUSERS_NOTIFY_USERS_CAP)) {		
        wp_die(printf('<div class="error fade"><p>%s</p></div>',
            __('You are not allowed to notify users about posts and pages.', MAILUSERS_I18N_DOMAIN)));
	} 
?>

<?php
	global $user_identity, $user_email, $user_ID;

    // Update Custom Meta Filters
    do_action('mailusers_update_custom_meta_filters') ;

	$err_msg = '';
    $from_sender = 0;
	
	get_currentuserinfo();
	$from_name = $user_identity;
	$from_address = $user_email;
    $override_name = mailusers_get_from_sender_name_override() ;
    $override_address = mailusers_get_from_sender_address_override() ;
    $exclude_sender = mailusers_get_from_sender_exclude() ;
    $exclude_id = ($exclude_sender) ? '' : $user_ID ;
	$mail_format = mailusers_get_default_mail_format();

	// Send the email if it has been requested
	if (array_key_exists('send', $_POST) && $_POST['send']=='true') {	
		// Analyse form input, check for blank fields
		if ( isset( $_POST['post_id'] ) ) {
			$post_id = $_POST['post_id'];
		}
		
		if ( !isset( $_POST['send_targets'] ) && !isset( $_POST['send_users'] ) ) {
			$err_msg = $err_msg . __('You must select at least a recipient.', MAILUSERS_I18N_DOMAIN) . '<br/>';
		} else {
			$send_targets = isset($_POST['send_targets']) ? $_POST['send_targets'] : array();
			$send_users = isset($_POST['send_users']) ? $_POST['send_users'] : array();
		}
		
		if ( !isset( $_POST['subject'] ) || trim($_POST['subject'])=='' ) {
			$err_msg = $err_msg . __('You must enter a subject.', MAILUSERS_I18N_DOMAIN) . '<br/>';
		} else {
			$original_subject = $_POST['subject'];
		}
		
		if ( !isset( $_POST['mailcontent'] ) || trim($_POST['mailcontent'])=='' ) {
			$err_msg = $err_msg . __('You must enter some content.', MAILUSERS_I18N_DOMAIN) . '<br/>';
		} else {
			$original_mail_content = $_POST['mailcontent'];
		}
		
		if ( !isset( $_POST['from_sender'] ) || trim($_POST['from_sender'])=='' ) {
			$from_sender = 0;
		} else {
			$from_sender = $_POST['from_sender'];
		}
	}

	if (!isset($send_targets)) {
		$send_targets = array();
	}

	if (!isset($send_users)) {
		$send_users = array();
	}

	if (!isset($mail_format)) {
		$mail_format = mailusers_get_default_mail_format();
	}

	if (!isset($original_subject)) {
		$original_subject = '';
	}

	if (!isset($original_mail_content)) {
		$original_mail_content = '';
	}	
	
    //  Override the send from address?
    if (($from_sender == 1) && !empty($override_address) && is_email($override_address)) {
     	$original_mail_content = preg_replace( '/' . $from_name . '/', mailusers_preg_quote($override_name), $original_mail_content );

        $from_address = $override_address ;
        if (!empty($override_name)) $from_name = $override_name ;
    }

	// If error, we simply show the form again
	if (array_key_exists('send', $_POST) && ($_POST['send']=='true') && ($err_msg == '')) {
		// No error, send the mail

?>

	<div class="wrap">
	<?php 
		// Fetch users
		// --

        $recipients = array() ;

        $send_ug = array() ;
        $send_filters = array() ;
        $send_roles = array() ;
        $send_uam = array() ;
        $send_groups = array() ;

        //  Loop through the various types of potential recipients
        //  and extract the 
        foreach ($send_targets as $target)
        {
            //  Decompose the target value so we know what we're dealing with
            list($key, $value) = explode('-', $target, 2) ;

            //  Once known, put the target value in the proper pile
            switch ($key)
            {
                case 'filter':
                    $send_filters[] = $value ;
                    break ;

                case 'user group':
                    $send_ug[] = $value ;
                    break ;

                case 'uam':
                    $send_uam[] = $value ;
                    break ;

                case 'groups':
                    $send_groups[] = $value ;
                    break ;

                default:
                    $send_roles[] = $value ;
                    break ;
            }
        }

        //  Extract the recipinents from the various target sources
        $users_from_roles_and_filters = array() ;

        if (!empty($send_filters))
            $users_from_roles_and_filters = array_merge($users_from_roles_and_filters,
                mailusers_get_recipients_from_custom_meta_filters($send_filters, $exclude_id, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META));

        if (class_exists(MAILUSERS_USER_GROUPS_CLASS) && !empty($send_ug))
            $users_from_roles_and_filters = array_merge($users_from_roles_and_filters,
                mailusers_get_recipients_from_user_groups($send_ug, $exclude_id, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META));

        if (class_exists(MAILUSERS_USER_ACCESS_MANAGER_CLASS) && !empty($send_uam))
        {
            $users_from_roles_and_filters = array_merge($users_from_roles_and_filters,
                mailusers_get_recipients_from_uam_group($send_uam, $exclude_id, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META));
        }

        if (class_exists(MAILUSERS_ITTHINX_GROUPS_CLASS) && !empty($send_groups))
        {
            $users_from_roles_and_filters = array_merge($users_from_roles_and_filters,
                mailusers_get_recipients_from_itthinx_groups_group($send_groups, $exclude_id, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META));
        }

        if (!empty($send_roles))
            $users_from_roles_and_filters = array_merge($users_from_roles_and_filters,
                mailusers_get_recipients_from_roles($send_roles, $exclude_id, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META));

		// Fetch users
		// --

        if (!empty($send_users))
		    $users_from_ids = mailusers_get_recipients_from_ids($send_users, $exclude_id, MAILUSERS_ACCEPT_NOTIFICATION_USER_META);
        else
            $users_from_ids = array() ;

		$recipients = array_merge($users_from_roles_and_filters, $users_from_ids);

		if (empty($recipients)) {
	?>
			<p><strong><?php _e('No recipients were found.', MAILUSERS_I18N_DOMAIN); ?></strong></p>
	<?php
		} else {	
			$num_sent = mailusers_send_mail($recipients, format_to_post($original_subject), $original_mail_content, $mail_format, $from_name, $from_address);
	?>
			<div class="updated fade">
				<p><?php echo sprintf(__("Notification sent to %s user(s).", MAILUSERS_I18N_DOMAIN), $num_sent); ?></p>
			</div>
	<?php
			include 'email_users_notify_form.php';
		}
	?>
	</div>
	
<?php
	} else {
		// Redirect to the form page
		include 'email_users_notify_form.php';
	}
?>
