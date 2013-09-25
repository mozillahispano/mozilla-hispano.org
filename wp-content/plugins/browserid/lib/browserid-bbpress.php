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

if (!class_exists('MozillaPersonaBbPress')) {
	class MozillaPersonaBbPress {
		private $is_bbpress_enabled = true;
		private $comments = null;

		public function __construct($options) {
			$this->is_bbpress_enabled = $options['is_bbpress_enabled'];
			$this->comments = $options['comments'];
		}

		public function Init() {
			if (! $this->is_bbpress_enabled) return;

			add_action('bbp_allow_anonymous', 
					create_function('', 'return !is_user_logged_in();'));
			add_action('bbp_is_anonymous', 
					create_function('', 'return !is_user_logged_in();'));
			add_action('bbp_theme_before_topic_form_submit_button', 
					array(&$this, 'bbPress_submit'));
			add_action('bbp_theme_before_reply_form_submit_button', 
					array(&$this, 'bbPress_submit'));

		}

		// bbPress integration
		public function bbPress_submit() {
			$id = bbp_get_topic_id();
			if (empty($id))
				$id = bbp_get_forum_id();
			$this->comments->Comment_form_action($id);
		}

		// Imply anonymous commenting
		public function bbPress_anonymous() {
			return !is_user_logged_in();
		}
	}
}
?>
