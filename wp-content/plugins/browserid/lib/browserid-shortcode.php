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

if (!class_exists('MozillaPersonaShortcode')) {
	class MozillaPersonaShortcode {
		private $ui = null;

		public function __construct($options) {
			$this->ui = $options['ui'];
		}

		public function Init() {
			// Shortcode
			add_shortcode('browserid_loginout', 
					array(&$this, 'Shortcode_loginout'));

			add_shortcode('mozilla_persona', 
					array(&$this, 'Shortcode_loginout'));
		}

		// Shortcode "mozilla_persona"
		public function Shortcode_loginout() {
			return $this->ui->Get_loginout_html();
		}

	}
}
?>
