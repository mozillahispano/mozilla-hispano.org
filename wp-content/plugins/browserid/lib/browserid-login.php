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

if (!class_exists('MozillaPersonaLogin')) {
	class MozillaPersonaLogin {
		private $is_browserid_only_auth = false;
		private $ui = null;
		private $is_browserid_login = false;
		private $option_redirect_url = null;
		private $request_redirect_url = null;
		private $login_html = null;
		private $logout_html = null;

		public function __construct($options) {
			$this->is_browserid_only_auth = $options['is_browserid_only_auth'];
			$this->ui = $options['ui'];
			$this->option_redirect_url = $options['option_redirect_url'];
			$this->request_redirect_url = $options['request_redirect_url'];
			$this->login_html = $options['login_html'];
			$this->logout_html = $options['logout_html'];
		}

		public function Init() {
			// Authentication
			add_action('set_auth_cookie',
					array(&$this, 'Set_cookie_if_persona_login'), 10, 5);

			add_action('clear_auth_cookie',
					array(&$this, 'Clear_persona_login_cookie'));

			if ($this->is_browserid_only_auth) {
				add_filter('wp_authenticate_user',
						array(&$this, 'Disallow_non_persona_logins'));
			}

			add_filter('check_password',
					array(&$this, 'Allow_fake_password_if_persona_login'));

			add_action('login_form',
					array(&$this, 'Add_persona_to_login_form'));

		}

		public function Set_cookie_if_persona_login(
				$auth_cookie, $expire, $expiration, $user_id, $scheme) {
			// Persona should only manage Persona logins. If this is
			// a Persona login, keep track of it so that the user is
			// not automatically logged out if they log in via other means.
			if ($this->is_browserid_login) {
				$secure = $scheme == "secure_auth";
				setcookie(c_bid_browserid_login_cookie, 1, $expire, 
						COOKIEPATH, COOKIE_DOMAIN, $secure, true);
			}
			else {
				// If the user is not logged in via BrowserID, clear the
				// cookie.
				$this->Clear_persona_login_cookie();
			}
		}

		public function Clear_persona_login_cookie() {
			$expire = time() - YEAR_IN_SECONDS;
			setcookie(c_bid_browserid_login_cookie, ' ', $expire, 
					COOKIEPATH, COOKIE_DOMAIN);
		}

		public function Disallow_non_persona_logins($user) {
			if (! $this->is_browserid_login) {
				return new WP_error('invalid_login',
						'Only Persona logins are allowed');
			}

			return $user;
		}


		public function Allow_fake_password_if_persona_login($check) {
			// Passwords are handled by assertions in Persona authentication.
			// If this is a Persona login, the password is always good. This
			// allows for the fake password to be passed to wp_login above.
			if ($this->is_browserid_login) {
				return true;
			}

			return $check;
		}

		// Add login button to login page
		public function Add_persona_to_login_form() {
			echo 
			'<p>' . 
				$this->Get_loginout_html(false) . '<br /><br />' .
			'</p>';
		}

		public function Get_rememberme() {
			return (isset($_REQUEST['rememberme']) 
							&& $_REQUEST['rememberme'] == 'true');
		}

		// Process login
		public function Handle_login($email) {
			// Login
			$user = $this->Login_by_email($email, $this->Get_rememberme());
			if ($user) {
				// Beam me up, Scotty!
				$redirect_to = $this->Get_login_redirect_url();
				$redirect_to = apply_filters(
									'login_redirect', $redirect_to, '', $user);
				wp_redirect($redirect_to);
				exit();
			}
			else {
				$message = __('You must already have an account to log in with Persona.', c_bid_text_domain);
				$this->ui->Handle_error($message);
			}
		}

		// Login user using e-mail address
		public function Login_by_email($email, $rememberme) {
			$userdata = get_user_by('email', $email);
			return $this->Login_by_userdata($userdata, $rememberme);
		}

		// Login user using id
		public function Login_by_id($user_id, $rememberme) {
			$userdata = get_user_by('id', $user_id);
			return $this->Login_by_userdata($userdata, $rememberme);
		}

		// Login user by userdata
		public function Login_by_userdata($userdata, $rememberme) {
			global $user;
			$user = null;

			if ($userdata) {
				$this->is_browserid_login = true;
				$user = wp_signon(array(
					'user_login' => $userdata->user_login,
					'user_password' => 'fake_password',
					'remember' => true
				));
			}

			return $user;
		}

		// Get the currently logged in user, iff they authenticated
		// using BrowserID
		public function Get_browserid_logged_in_user() {
			global $user_email;
			get_currentuserinfo();

			if ( isset( $_COOKIE[c_bid_browserid_login_cookie] ) ) {
				return $user_email;
			}

			return null;
		}

		public function Get_login_redirect_url() {
			// first, if a redirect is specified in the request, use that.
			// second, if it is a new user and a new user redirect url is
			// specified, go there.
			// third, if if the global login redirect  is specified, use that.
			// forth, use the admin URL.

			if(!empty($this->request_redirect_url)) {
				$redirect_to = $this->request_redirect_url;
			} else if(!empty($this->option_redirect_url)) {
				$redirect_to = $this->option_redirect_url;
			} else {
				$redirect_to = admin_url();
			}

			return $redirect_to;
		}

		public function Get_loginout_html($check_login = true) {
			if ($check_login && is_user_logged_in()) {
				$html = $this->logout_html;

				// Simple link
				if (empty($html))
					return '';
				else
					return '<a href="#" class="js-persona__logout">' . $html . '</a>';
			}
			else {
				// User not logged in
				$html = $this->login_html;
				// Button
				$html = $this->ui->Get_persona_button_html("js-persona__login", $html);

				return $html;
			}
		}
    }
}
?>
