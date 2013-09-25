<?php
/*
Plugin Name: Mozilla Persona
Plugin URI: http://wordpress.org/extend/plugins/browserid/
Plugin Repo: https://github.com/shane-tomlinson/browserid-wordpress
Description: Mozilla Persona, the safest & easiest way to sign in
Version: 0.48
Author: Shane Tomlinson
Author URI: https://shanetomlinson.com
Original Author: Marcel Bokhorst
Original Author URI: http://blog.bokhorst.biz/about/
*/

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

// Check PHP version
if (version_compare(PHP_VERSION, '5.0.0', '<'))
	die('Mozilla Persona requires at least PHP 5, installed version is ' . PHP_VERSION);

include_once('lib/browserid-constants.php');
include_once('lib/browserid-verifier.php');
include_once('lib/browserid-widget.php');
include_once('lib/browserid-options.php');
include_once('lib/browserid-comments.php');
include_once('lib/browserid-registration.php');
include_once('lib/browserid-lostpassword.php');
include_once('lib/browserid-bbpress.php');
include_once('lib/browserid-login.php');
include_once('lib/browserid-admin.php');
include_once('lib/browserid-shortcode.php');
include_once('lib/browserid-activation.php');
include_once('lib/browserid-assertion-handler.php');


// Define class
if (!class_exists('MozillaPersona')) {
	class MozillaPersona {
		// Class variables
		var $debug = null;
		private $options = null;
		private $registration = null;
		private $lostpassword = null;
		private $bbpress = null;
		private $login = null;
		private $administration = null;
		private $verifier = null;
		private $activation = null;
		private $assertion_handler = null;
		private $widget = null;

		// Constructor
		function __construct() {
			$this->options = new MozillaPersonaOptions();
			$this->options->Init();

			$this->activation = new MozillaPersonaPluginActivation(array(
				'plugin_options' => $this->options,
				'file' => __FILE__
			));

			// Register actions & filters
			add_action('init', array(&$this, 'Init'), 0);

			// frontend
			add_action('wp_enqueue_scripts',
					array(&$this, 'Add_external_dependencies'));

			// login screen
			add_action('login_enqueue_scripts',
					array(&$this, 'Add_external_dependencies'));

			// admin pages
			add_action('admin_enqueue_scripts',
					array(&$this, 'Add_external_dependencies'));
			add_action('admin_enqueue_scripts',
					array(&$this, 'Add_external_dependencies_settings'));
			add_action('admin_print_styles-settings_page_browserid-wordpress',
					array(&$this, 'Add_external_dependencies_settings'));
		}

		// Initialization
		function Init() {
			$this->Init_l10n();

			$this->Init_comments();
			$this->Init_login();
			$this->Init_registration();
			$this->Init_bbpress();
			$this->Init_verifier();
			$this->Init_assertion_handler();

			if ($this->assertion_handler->Handle_assertion()) return;

			$this->Init_administration();
			$this->Init_shortcode();
			$this->Init_widget();
			$this->Init_lostpassword();

			$this->Set_error_from_request();
		}

		private function Init_comments() {
			$this->comments = new MozillaPersonaComments(array(
				'is_comments_enabled' => $this->options->Is_comments(),
				'is_bbpress_enabled' => $this->options->Is_bbpress(),
				'ui' => $this,
				'button_html' => $this->options->Get_comment_html()
			));
			$this->comments->Init();
		}

		private function Init_login() {
			$this->login = new MozillaPersonaLogin(array(
				'is_browserid_only_auth' => $this->options->Is_browserid_only_auth(),
				'ui' => $this,
				'option_redirect_url' => $this->options->Get_login_redir(),
				'request_redirect_url' => $this->Get_request_redirect_url(),
				'login_html' => $this->options->Get_login_html(),
				'logout_html' => $this->options->Get_logout_html()
			));
			$this->login->Init();
		}

		private function Init_verifier() {
			$this->verifier = new MozillaPersonaVerifier(array(
				'vserver' => $this->options->Get_vserver(),
				'audience' => $this->options->Get_audience(),
				'ui' => $this,
				'is_debug' => $this->options->Is_debug(),
				'rememberme' => $this->login->Get_rememberme()
			));
			$this->verifier->Init();
		}

		private function Init_registration() {
			$this->registration = new MozillaPersonaRegistration(array(
				'login' => $this->login,
				'browserid_only_auth' => $this->options->Is_browserid_only_auth(),
				'ui' => $this
			));
			$this->registration->Init();
		}

		private function Init_bbpress() {
			$this->bbpress = new MozillaPersonaBbPress(array(
				'is_bbpress_enabled' => $this->options->Is_bbpress(),
				'comments' => $this->comments
			));
			$this->bbpress->Init();
		}

		private function Init_lostpassword() {
			$this->lostpassword = new MozillaPersonaLostPassword(array(
				'browserid_only_auth' => $this->options->Is_browserid_only_auth(),
				'ui' => $this
			));
			$this->lostpassword->Init();
		}

		private function Init_administration() {
			$this->administration = new MozillaPersonaAdministration(array(
				'logged_in_user' => $this->login->Get_browserid_logged_in_user(),
				'browserid_only_auth' => $this->options->Is_browserid_only_auth(),
				'audience' => $this->options->Get_audience(),
				'is_debug' => $this->options->Is_debug(),
				'ui' => $this,
				'logout_html' => $this->options->Get_logout_html()
			));
			$this->administration->Init();
		}

		private function Init_shortcode() {
			$this->shortcode = new MozillaPersonaShortcode(array(
				'ui' => $this
			));
			$this->shortcode->Init();
		}

		private function Init_assertion_handler() {
			$this->assertion_handler = new MozillaPersonaAssertionHandler(array(
				'login' => $this->login,
				'comments' => $this->comments,
				'registration' => $this->registration,
				'verifier' => $this->verifier
			));
			$this->assertion_handler->Init();
		}

		private function Init_widget() {
			$this->widget = new MozillaPersonaWidget();
			$this->widget->Init();
		}

		private function Init_l10n() {
			$l10npath = dirname(plugin_basename(__FILE__)) . '/languages/';
			load_plugin_textdomain(c_bid_text_domain, false, $l10npath);
		}

		// Add external dependencies - both JS & CSS
		public function Add_external_dependencies() {
			// Add the Persona button styles.
			wp_enqueue_style('persona-style',
					$this->CssUrl('browserid'),
					array(), c_bid_version);

			wp_register_script('browserid',
					$this->options->Get_persona_source() . '/include.js',
					array(), c_bid_version, true);

			// This one script takes care of all work.
			wp_enqueue_script('browserid_common',
					$this->JavascriptUrl('browserid'),
					array('jquery', 'browserid'), c_bid_version, true);

			$data_array = array(
				'urlLoginSubmit'
						=> get_site_url(null, '/'),
				'urlLoginRedirect'
						=> $this->login->Get_login_redirect_url(),
				'urlRegistrationRedirect'
						=> $this->registration->Get_registration_redirect_url(),
				'urlLogoutRedirect'
						=> wp_logout_url(),
				'msgError'
						=> $this->Get_error_message(),
				'msgFailed'
						=> $this->verifier->Get_verification_failed_message(),
				'isPersonaOnlyAuth'
						=> $this->options->Is_browserid_only_auth(),
				'isPersonaUsedWithComments'
						=> $this->options->Is_comments() &&
								! is_user_logged_in(),

				// From here down is passed to the Persona dialog.
				'siteName'
						=> $this->options->Get_sitename(),
				'siteLogo'
						=> $this->options->Get_sitelogo(),
				'backgroundColor'
						=> $this->options->Get_background_color(),
				'termsOfService'
						=> $this->options->Get_terms_of_service(),
				'privacyPolicy'
						=> $this->options->Get_privacy_policy(),
				'loggedInUser'
						=> $this->login->Get_browserid_logged_in_user(),
			);

			/**
			* wp_localize_script calls json_encode which escapes all of the
			* URLs and replaces any / with \/. This messes with certain servers
			* that do not normalize the extra \ and refuse to serve the
			* siteLogo.
			* To avoid the double escaping, manually write out the script,
			* replacing any \/ with /.
			* All parameters passed to Persona will be properly escaped.
			* See issue #47
			* https://github.com/shane-tomlinson/browserid-wordpress/issues/47
			*/
			$encoded_browserid_common = str_replace('\\/', '/',
					   json_encode( $data_array) );
?>
<script>
       var browserid_common = <?php echo $encoded_browserid_common; ?>
</script>
<?php
	}

		public function Add_external_dependencies_settings() {
			// Enqueue color picker for styles
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_media();

			// Enqueue js for settings page
			wp_enqueue_script('browserid_settings',
				$this->JavascriptUrl('browserid-settings'),
				array('jquery', 'wp-color-picker'), c_bid_version, true);
		}

		private function JavascriptUrl($root_name) {
			$file_name = $root_name;
			if (! $this->options->Use_uncompressed_resources())
				$file_name .= '.min';

			$file_name .= ".js";
			return plugins_url($file_name, __FILE__);
		}

		private function CssUrl($root_name) {
			$file_name = $root_name;
			if (! $this->options->Use_uncompressed_resources())
				$file_name .= '.min';

			$file_name .= ".css";
			return plugins_url($file_name, __FILE__);
		}

		private function Set_error_from_request() {
			// On the login pages, if there is an error, surface it to be
			// printed into the templates.
			if (isset($_REQUEST['browserid_error'])) {
				global $error;
				$error = $_REQUEST['browserid_error'];
			}
		}

		// Get the error message
		function Get_error_message() {
			return (isset($_REQUEST['browserid_error']) ? $_REQUEST['browserid_error'] : null);
		}


		function Handle_error($message, $debug_message = '', $result = '') {
			if ($this->options->Is_debug() && !empty($debug_message)) {
				header('Content-type: text/plain');
				echo $debug_message . PHP_EOL;

				if (!empty($result)) {
					print_r($result);
				}
			} else {
				// XXX I don't understand this.
				$post_id = $this->comments->Is_comment();
				$redirect = $this->Get_request_redirect_url();
				$url = ($post_id ? get_permalink($post_id) : wp_login_url($redirect));
				$url .= (strpos($url, '?') === false ? '?' : '&') . 'browserid_error=' . urlencode($message);
				if ($post_id)
					$url .= '#browserid_' . $post_id;
				wp_redirect($url);
			}

			exit();
		}

		// Print a persona error.
		function Print_persona_error($error, $classname = '') {
			echo $this->Get_persona_error_html($error, $classname);
		}

		// Get html for a Persona error
		function Get_persona_error_html($error, $classname = '') {
			$error = htmlspecialchars(stripslashes($error),
							ENT_QUOTES, get_bloginfo('charset'));

			$html = sprintf('<div class="persona__error %s">%s</div>', $classname, $error);
			return $html;
		}


		// Get the Persona Button HTML
		function Get_persona_button_html($classname, $html) {
			$button_html = ''
					. '<a href="#" title="%s" class="%s %s">'
					.	'<span class="%s">%s</span>'
					. '</a> %s';

			$color = $this->options->Get_button_color();
			$button_html = sprintf($button_html,
				"Mozilla Persona",
				"persona-button " . $color,
				$classname,
				"persona-button__text",
				$html,
				$this->What_is());

			return $button_html;
		}

		// Print a Persona button
		function Print_persona_button_html($classname, $html) {
			echo $this->Get_persona_button_html($classname, $html);
		}

		function What_is() {
			$html = '<p class="persona__whatis"><a href="%s" class="%s" target="_blank">%s</a></p>';

			$html = sprintf($html,
						"https://login.persona.org",
						"persona__whatis_link",
						__('What is Persona?', c_bid_text_domain)
						);

			return $html;
		}



		// Build HTML for login/out button/link
		function Get_loginout_html($check_login = true) {
			return $this->login->Get_loginout_html();
		}

		function Get_assertion() {
			return $this->assertion_handler->Get_assertion();
		}

		// Get the redirect URL from the request
		function Get_request_redirect_url() {
			return (isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null);
		}



		// Check environment
		function Check_prerequisites() {
			// Check WordPress version
			global $wp_version;
			if (version_compare($wp_version, '3.1') < 0)
				die('Mozilla Persona requires at least WordPress 3.1');

			// Check basic prerequisities
			self::Check_function('add_action');
			self::Check_function('wp_enqueue_script');
			self::Check_function('json_decode');
			self::Check_function('parse_url');
			self::Check_function('md5');
			self::Check_function('wp_remote_post');
			self::Check_function('wp_remote_get');
		}

		function Check_function($name) {
			if (!function_exists($name))
				die('Required WordPress function "' . $name . '" does not exist');
		}
	}
}

// Start plugin
global $persona_plugin;
if (empty($persona_plugin)) {
	$persona_plugin = new MozillaPersona();
	// Check pre-requisites
	$persona_plugin->Check_prerequisites();

}

// Template tag "mozilla_persona"
if (!function_exists('mozilla_persona')) {
	function mozilla_persona() {
		global $persona_plugin;
		echo $persona_plugin->Get_loginout_html();
	}
}

// Template tag "browserid_loginout"
if (!function_exists('browserid_loginout')) {
	function browserid_loginout() {
		global $persona_plugin;
		echo $persona_plugin->Get_loginout_html();
	}
}
?>
