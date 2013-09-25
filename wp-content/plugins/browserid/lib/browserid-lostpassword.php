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

if (!class_exists('MozillaPersonaLostPassword')) {
	class MozillaPersonaLostPassword {
		private $browserid_only_auth = false;
		private $ui = null;
		
		public function __construct($options) {
			$this->browserid_only_auth = $options['browserid_only_auth'];
			$this->ui = $options['ui'];
		}

		public function Init() {
			if (! $this->browserid_only_auth) return;

			add_action('lost_password',
					array(&$this, 'Disallow_lost_password'));
			add_filter('allow_password_reset',
					array(&$this, 'Disallow_password_reset'));
			add_filter('show_password_fields',
					array(&$this, 'Hide_password_fields'));
			add_filter('gettext',
					array(&$this, 'Hide_lost_password_text'));
		}

		// If only BrowserID logins are allowed, a reset password form should
		// not be shown.
		public function Disallow_lost_password() {
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(
								get_option('blogname'), ENT_QUOTES);
			login_header(__('Password reset disabled', c_bid_text_domain),
				'<p class="message">' . sprintf(__('%s uses Mozilla Persona to sign in and does not use passwords. Password reset is disabled.', c_bid_text_domain), $blogname) . "</p>");
			login_footer('user_login');
			exit();
		}

		// Disable reset password if in BrowserID only mode
		public function Disallow_password_reset() {
			return false;
		}

		// Disable change password form if in BrowserID only mode
		public function Hide_password_fields() {
			return false;
		}

		// In Disable Non-Persona auth mode, Hide the "Lost your password?"
		// link from the login page by not giving it any text. If the user
		// still lands on the reset password page, a nice error screen is
		// shown saying "no way, Jose."
		public function Hide_lost_password_text($text) {
			if ($text == 'Lost your password?') {
				$text = '';
			}

			return $text;
		}

	}
}
