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

if (!class_exists('MozillaPersonaVerifier')) {
	class MozillaPersonaVerifier {
		private $ui = null;
		private $vserver = null;
		private $audience = null;
		private $is_debug = false;
		private $rememberme = false;

		public function __construct($options) {
			$this->ui = $options['ui'];

			$this->vserver = $options['vserver'];
			$this->audience = $options['audience'];

			$this->is_debug = $options['is_debug'];
			$this->rememberme = $options['rememberme'];
		}

		public function Init() {
			add_action('http_api_curl', array(&$this, 'http_api_curl'));
		}

		public function http_api_curl($handle) {
			// When making the request to the verifier, use the local 
			// cacert.pem which has the root cert for the Persona verifier.
			curl_setopt($handle, CURLOPT_CAINFO, dirname(__FILE__) . '/../cacert.pem');
		}

		public function Get_verification_failed_message() {
			return __('Verification failed', c_bid_text_domain);
		}

		// Post the assertion to the verifier. If the assertion does not
		// verify, an error message will be displayed and no more processing
		// will occur
		public function Verify($assertion) {
			$response = $this->Send_assertion_to_verifier($assertion);
			$result = $this->Check_response($response);

			$this->Persist_debug_info($result);

			if (is_wp_error($result)) {
				$this->Handle_response_errors($result);
			}

			return $result;
		}

		private function Send_assertion_to_verifier($assertion) {
			// Build arguments
			$args = array(
					'method' => 'POST',
					'timeout' => 30,
					'redirection' => 0,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => array(
						'assertion' => $assertion,
						'audience' => $this->audience
						),
					'cookies' => array(),
					'sslverify' => true
					);

			// Verify assertion
			$response = wp_remote_post($this->vserver, $args);

			if (is_wp_error($response)) {
				$this->Handle_response_errors($response);
			}

			return $response;
		}

		private function Check_response($response) {
			$result = json_decode($response['body'], true);

			if (empty($result) || empty($result['status'])) {
				return new WP_Error('verification_response_invalid', 
						__('Verification response invalid', c_bid_text_domain));
			}
			else if ($result['status'] != 'okay') {
				$message = __('Verification failed', c_bid_text_domain);
				if (isset($result['reason']))
					$message .= ': ' . __($result['reason'], c_bid_text_domain);

				return new WP_Error('verification_failed', $message);
			}

			// Success!
			return $result;
		}

		private function Persist_debug_info($response) {
			if ($this->is_debug) {
				$response['vserver'] = $this->vserver;
				$response['audience'] = $this->audience;
				$response['rememberme'] = $this->rememberme;
				update_option(c_bid_option_response, $response);
			}
		}

		// Check response. If response is either invalid or indicates a bad
		// assertion, an error message will be printed and processing
		// will stop. If verification succeeds, response will be returned.
		private function Handle_response_errors($response) {
			$message = __($response->get_error_message());
			$this->ui->Handle_error($message, $message, $response);
		}
	}
}
?>
