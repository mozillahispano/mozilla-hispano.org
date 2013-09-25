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

if (!class_exists('MozillaPersonaPluginActivation')) {
	class MozillaPersonaPluginActivation {
		private $plugin_options = null;

		public function __construct($options) {
			$this->plugin_options = $options['plugin_options'];

			register_activation_hook($options['file'],
					array(&$this, 'Activate'));
			register_deactivation_hook($options['file'],
					array(&$this, 'Deactivate'));
		}

		public function Activate() {
			// Nothing to do must yet.
		}

		public function Deactivate() {
			$this->plugin_options->Deactivate();
		}
	}
}
