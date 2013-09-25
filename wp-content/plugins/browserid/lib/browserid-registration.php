<?php
/*
	Copyright (c) 2011, 2012, 2013 Shane Tomlinson, Marcel Bokhorst

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once('browserid-constants.php');

// Used in wp_new_user_notification
global $browserid_only_auth;

if (!class_exists('MozillaPersonaRegistration')) {
	class MozillaPersonaRegistration {
		private $browserid_only_auth = false;
		private $user_registering_with_browserid = false;
		private $ui = null;
		private $login = null;
		
		public function __construct($options) {
			$this->browserid_only_auth = $options['browserid_only_auth'];

			global $browserid_only_auth;
			$browserid_only_auth = $options['browserid_only_auth'];

			$this->ui = $options['ui'];
			$this->login = $options['login'];
		}

		public function Init() {
			if (! $this->browserid_only_auth) return;

			add_action('register_form',
					array(&$this, 'Add_browserid_to_registration_form'));
			add_action('user_register',
					array(&$this, 'Complete_new_user_creation'));
			add_filter('registration_errors',
					array(&$this, 'Disallow_non_browserid_registration_filter'));
			add_filter('registration_redirect',
					array(&$this, 'Registration_redirect_filter'));
			add_action('admin_init',
					array(&$this, 'Remove_password_nag_if_browserid_registration'));
		}

		public function Is_registration() {
			$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
			return $action == 'register';
		}

		public function Handle_registration($email) {
			if ($this->browserid_only_auth) {
				// Keep track of whether the user is registering with
				// BrowserID. Non BrowserID registrations are disabled in
				// BrowserID only auth.
				$this->user_registering_with_browserid = true;
				$_POST['user_email'] = $email;
			}
		}


		// Add Persona button to registration form and remove the email form.
		public function Add_browserid_to_registration_form() {
			echo '<input type="hidden" name="browserid_assertion" id="browserid_assertion" />';

			$html = __('Register', c_bid_text_domain) ;
			$this->ui->Print_persona_button_html("js-persona__register", $html);
		}

		// Now that the user is registered, set a fake password and log them in
		public function Complete_new_user_creation($user_id) {
			add_user_meta($user_id, 'browserid_registration', $this->user_registering_with_browserid);
			if ($this->user_registering_with_browserid) {
				$this->login->Login_by_id($user_id, false);
			}
		}

		public function Remove_password_nag_if_browserid_registration() {
			global $user_ID;
			$registered_with_browserid = get_user_meta($user_ID, 'browserid_registration', true);
			if ($registered_with_browserid === true) {
				delete_user_setting('default_password_nag', $user_ID);
				update_user_option($user_ID, 'default_password_nag', false, true);
			}
		}

		// Check if traditional registration has been disabled.
		public function Disallow_non_browserid_registration_filter($errors) {
			if (! $this->user_registering_with_browserid) {
				$blogname = wp_specialchars_decode(
									get_option('blogname'), ENT_QUOTES);
				$errors->add('invalid_registration',
						sprintf(__('<strong>ERROR</strong>:  '
						. '%s uses Mozilla Persona for registration. '
						. 'Please register using Persona.',
						c_bid_text_domain), $blogname));
			}

			return $errors;
		}

		public function Registration_redirect_filter($redirect_to) {
			if ($redirect_to) return $redirect_to;

			// The user successfully signed up using Persona,
			// send them to their profile page
			return $this->Get_registration_redirect_url();
		}

		// Get the registration redirect URL
		public function Get_registration_redirect_url() {
			return admin_url() . 'profile.php';
		}
	}
}

if (!function_exists('wp_new_user_notification')) {
	function wp_new_user_notification($user_id, $plaintext_pass = '') {
		$user = get_userdata( $user_id );

		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

		@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

		if ( empty($plaintext_pass) )
			return;

		$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
		$title = '';

		global $browserid_only_auth;
		if ($browserid_only_auth) {
			$message .= sprintf(__('%s uses Mozilla Persona to sign in and does not use passwords', c_bid_text_domain), $blogname) . "\r\n";
			$title .= sprintf(__('[%s] Your username'), $blogname);
		} else {
			$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
			$title .= sprintf(__('[%s] Your username and password'), $blogname);
		}
		$message .= wp_login_url() . "\r\n";

		wp_mail($user_email, $title, $message);
	}
}
?>
