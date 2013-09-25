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

if (!class_exists('MozillaPersonaAssertionHandler')) {
	class MozillaPersonaAssertionHandler {

		private $login = null;
		private $comments = null;
		private $registration = null;
		private $verifier = null;

		public function __construct($options) {
			$this->login = $options['login'];
			$this->comments = $options['comments'];
			$this->registration = $options['registration'];
			$this->verifier = $options['verifier'];
		}

		public function Init() {
			// nothing to do.
		}

		public function Handle_assertion() {
			$assertion = $this->Get_assertion();
			$isAssertion = !empty($assertion);

			if ($isAssertion) $this->Check_assertion($assertion);

			return $isAssertion;
		}

		// Get an assertion from that request
		public function Get_assertion() {
			// Workaround for Microsoft IIS bug
			if (isset($_REQUEST['?browserid_assertion']))
				$_REQUEST['browserid_assertion'] = $_REQUEST['?browserid_assertion'];

			return isset($_REQUEST['browserid_assertion']) ?
					$_REQUEST['browserid_assertion'] : null;
		}


		// Check if an assertion is received. If one has been, verify it and
		// log the user in. If not, continue.
		private function Check_assertion($assertion) {
			$result = $this->verifier->Verify($assertion);

			if ($result) {
				$email = $result['email'];
				// Succeeded
				// XXX can we replace this with polymorphism somehow?
				if ($this->comments->Is_comment())
					$this->comments->Handle_comment($email);
				else if ($this->registration->Is_registration())
					$this->registration->Handle_registration($email);
				else
					$this->login->Handle_login($email);
			}
		}

	}
}
    
?>
