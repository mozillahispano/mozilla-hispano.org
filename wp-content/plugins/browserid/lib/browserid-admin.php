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

if (!class_exists('MozillaPersonaAdministration')) {
	class MozillaPersonaAdministration {
		private $browserid_only_auth = false;
		private $logged_in_user = null;
		private $ui = null;
		private $audience = null;
		private $is_debug = false;
		private $logout_html = null;

		public function __construct($options) {
			$this->browserid_only_auth = $options['browserid_only_auth'];
			$this->logged_in_user = $options['logged_in_user'];
			$this->ui = $options['ui'];
			$this->is_debug = $options['is_debug'];
			$this->audience = $options['audience'];
			$this->logout_html = $options['logout_html'];
		}

		public function Init() {
			if (is_admin()) {
				add_filter('plugin_action_links', 
						array(&$this, 'Add_settings_link_filter'), 10, 2);

				if ($this->browserid_only_auth) {
					// XXX this could equally go in browserid-registration
					add_action('admin_action_createuser',
							array(&$this, 'Set_new_user_password_action'));
				}
			}

			add_action('admin_bar_menu', 
					array(&$this, 'Admin_toolbar_replace_logout_action'), 999);
		}

		// Add a "Settings" link to the plugin list page.
		public function Add_settings_link_filter($links, $file) {
			static $this_plugin;

			if (!$this_plugin) {
				// XXX fix this logic to find the plugin's root filename
				$this_plugin = plugin_basename(__FILE__);
			}

			if ($file == $this_plugin) {
				// The "page" query string value must be equal to the slug
				// of the Settings admin page we defined earlier, which in
				// this case equals "myplugin-settings".
				$settings_link = '<a href="'
					. get_bloginfo('wpurl')
					. '/wp-admin/admin.php?page=' . __FILE__ . '">'
					. __('Settings', c_bid_text_domain) . '</a>';
				array_unshift($links, $settings_link);
			}

			return $links;
		}


		// set a fake password when creating a password for a user.
		// only called if "BrowserID Only" auth is set.
		public function Set_new_user_password_action() {
			if (! (isset( $_POST['pass1']) && isset( $_POST['pass2']))) {
				$user_pass = wp_generate_password( 12, false);
				$_POST['pass1'] = $user_pass;
				$_POST['pass2'] = $user_pass;
			}
		}

		public function Admin_toolbar_replace_logout_action($wp_toolbar) {
			// If the user is signed in via Persona, replace their toolbar logout
			// with a logout that will work with Persona.
			if ( $this->logged_in_user ) {
				$wp_toolbar->remove_node('logout');
				$wp_toolbar->add_node(array(
					'id' => 'logout',
					'title' => $this->logout_html,
					'parent' => 'user-actions',
					'href' => '#',
					'meta' => array(
						'class' => 'js-persona__logout'
					)
				));
			}
		}

	}
}
?>
