<?php
/*
Plugin Name: Mozilla Persona
Plugin URI: http://wordpress.org/extend/plugins/browserid/
Plugin Repo: https://github.com/shane-tomlinson/browserid-wordpress
Description: Mozilla Persona, the safest & easiest way to sign in
Version: 0.45
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

//error_reporting(E_ALL);

// Check PHP version
if (version_compare(PHP_VERSION, '5.0.0', '<'))
	die('Mozilla Persona requires at least PHP 5, installed version is ' . PHP_VERSION);

// Define constants
define('c_bid_text_domain', 'browserid');
define('c_bid_version', '0.45');
define('c_bid_option_request', 'bid_request');
define('c_bid_option_response', 'bid_response');
define('c_bid_browserid_login_cookie', 'bid_browserid_login_' . COOKIEHASH);


define('c_bid_source', 'https://login.persona.org');
define('c_bid_verifier', 'https://verifier.login.persona.org');


// Define class
if (!class_exists('MozillaPersona')) {
	class MozillaPersona {
		// Class variables
		var $debug = null;

		// Constructor
		function __construct() {
			// Register de-activation
			register_deactivation_hook(__FILE__, array(&$this, 'Deactivate'));

			// Register actions & filters
			add_action('init', array(&$this, 'Init'), 0);

			// Authentication
			add_action('set_auth_cookie',
					array(&$this, 'Set_auth_cookie_action'), 10, 5);
			add_action('clear_auth_cookie',
					array(&$this, 'Clear_auth_cookie_action'));

			if (self::Is_option_browserid_only_auth()) {
				add_filter('wp_authenticate_user',
						array(&$this, 'Disallow_non_persona_logins_filter'));
			}

			add_filter('check_password',
					array(&$this, 'Allow_fake_password_if_persona_login'));

			add_action('login_form',
					array(&$this, 'Add_persona_to_login_form'));


			// Registration
			if (self::Is_option_browserid_only_auth()) {
				add_action('register_form',
						array(&$this, 'Add_persona_to_registration_form'));
				add_action('user_register',
						array(&$this, 'Sign_in_new_persona_user'));
				add_filter('registration_errors',
						array(&$this, 'Disallow_non_persona_registration_filter'));
				add_filter('registration_redirect',
						array(&$this, 'Registration_redirect_filter'));
			}

			// Lost password
			if (self::Is_option_browserid_only_auth()) {
				add_action('lost_password',
						array(&$this, 'Disallow_lost_password'));
				add_filter('allow_password_reset',
						array(&$this, 'Disallow_password_reset'));
				add_filter('show_password_fields',
						array(&$this, 'Hide_password_fields'));
				add_filter('gettext',
						array(&$this, 'Hide_lost_password_text'));
			}

			// Widgets and admin menu
			add_action('widgets_init', create_function('', 'return register_widget("BrowserID_Widget");'));
			if (is_admin()) {
				// Action link in the plugins page
				add_filter('plugin_action_links', array(&$this, 'Plugin_action_links_filter'), 10, 2);

				add_action('admin_menu', array(&$this, 'Admin_menu_action'));
				add_action('admin_init', array(&$this, 'Admin_init_action'));

				if (self::Is_option_browserid_only_auth()) {
					add_action('admin_action_createuser',
							array(&$this, 'Admin_action_createuser'));
				}
			}

			// top toolbar logout button override
			add_action('admin_bar_menu', array(&$this, 'Admin_toolbar_action'), 999);

			add_action('http_api_curl', array(&$this, 'http_api_curl'));

			// Comment integration
			if (self::Is_option_comments()) {
				add_filter('comment_form_default_fields', array(&$this, 'Comment_form_action_default_fields_filter'));
				add_action('comment_form', array(&$this, 'Comment_form_action'));
				add_filter('pre_comment_approved', array(&$this, 'Pre_comment_approved_filter'), 20, 2);
			}

			// bbPress integration
			if (self::Is_option_bbpress()) {
				add_action('bbp_allow_anonymous', create_function('', 'return !is_user_logged_in();'));
				add_action('bbp_is_anonymous', create_function('', 'return !is_user_logged_in();'));
				add_action('bbp_theme_before_topic_form_submit_button', array(&$this, 'bbPress_submit'));
				add_action('bbp_theme_before_reply_form_submit_button', array(&$this, 'bbPress_submit'));
			}

			// Shortcode
			add_shortcode('browserid_loginout', array(&$this, 'Shortcode_loginout'));
			add_shortcode('mozilla_persona', array(&$this, 'Shortcode_loginout'));


			$this->user_registering_with_browserid = false;
		}

		// Handle plugin activation
		function Activate() {
			global $wpdb;
			$options = get_option('browserid_options');
			if (empty($options['browserid_login_html']))
				$options['browserid_login_html'] =
					__('Sign in with your email', c_bid_text_domain);

			if (empty($options['browserid_logout_html']))
				$options['browserid_logout_html'] =
					__('Logout', c_bid_text_domain);

			update_option('browserid_options', $options);
		}

		// Handle plugin deactivation
		function Deactivate() {
			if(get_option('browserid_options'))
				delete_option('browserid_options');
		}

		// Add a "Settings" link to the plugin list page.
		function Plugin_action_links_filter($links, $file) {
			static $this_plugin;

			if (!$this_plugin) {
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

		// Initialization
		function Init() {
			$this->browserid_login = false;

			// Check for assertion
			$assertion = self::Get_assertion();
			if (!empty($assertion)) {
				return self::Check_assertion($assertion);
			}

			// I18n
			$l10npath = dirname(plugin_basename(__FILE__)) . '/languages/';
			load_plugin_textdomain(c_bid_text_domain, false, $l10npath);

			self::Add_external_dependencies();

			// On the login pages, if there is an error, surface it to be
			// printed into the templates.
			if (isset($_REQUEST['browserid_error'])) {
				global $error;
				$error = $_REQUEST['browserid_error'];
			}
		}

		// Add external dependencies - both JS & CSS
		function Add_external_dependencies() {
			// Add the Persona button styles.
			wp_register_style('persona-style',
					plugins_url('browserid.css', __FILE__),
					array(), c_bid_version);
			wp_enqueue_style('persona-style');

			// Enqueue BrowserID scripts
			wp_register_script('browserid',
					self::Get_option_persona_source() . '/include.js', 
					array(), c_bid_version, true);

			// This one script takes care of all work.
			wp_register_script('browserid_common',
					plugins_url('browserid.js', __FILE__),
					array('jquery', 'browserid'), c_bid_version, true);

			$data_array = array(
				'urlLoginSubmit' => get_site_url(null, '/'),
				'urlLoginRedirect' => self::Get_login_redirect_url(),
				'urlRegistrationRedirect'
						=> self::Get_registration_redirect_url(),
				'urlLogoutRedirect' => wp_logout_url(),
				'msgError' => self::Get_error_message(),
				'msgFailed' => self::Get_verification_failed_message(),
				'isPersonaOnlyAuth' => self::Is_option_browserid_only_auth(),
				'isPersonaUsedWithComments' => self::Is_option_comments(),

				// From here down is passed to the Persona dialog.
				'siteName' => self::Get_sitename(),
				'siteLogo' => self::Get_sitelogo(),
				'backgroundColor' => self::Get_background_color(),
				'termsOfService' => self::Get_terms_of_service(),
				'privacyPolicy' => self::Get_privacy_policy(),
				'loggedInUser' => self::Get_browserid_loggedin_user(),
			);
			wp_localize_script( 'browserid_common', 'browserid_common',
					$data_array );
			wp_enqueue_script('browserid_common');
		}


		// Get the redirect URL from the request
		function Get_request_redirect_url() {
			return (isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null);
		}

		// Get the login redirect URL
		function Get_login_redirect_url() {
			// first, if a redirect is specified in the request, use that.
			// second, if it is a new user and a new user redirect url is
			// specified, go there.
			// third, if if the global login redirect  is specified, use that.
			// forth, use the admin URL.

			$option_redirect_url = self::Get_option_login_redir();
			$request_redirect_url = self::Get_request_redirect_url();

			if(!empty($request_redirect_url)) {
				$redirect_to = $request_redirect_url;
			} else if(!empty($option_redirect_url)) {
				$redirect_to = $option_redirect_url;
			} else {
				$redirect_to = admin_url();
			}

			return $redirect_to;
		}

		// Get the registration redirect URL
		function Get_registration_redirect_url() {
			return admin_url() . 'profile.php';
		}

		// Get the error message
		function Get_error_message() {
			return (isset($_REQUEST['browserid_error']) ? $_REQUEST['browserid_error'] : null);
		}

		// Get the verification failed message
		function Get_verification_failed_message() {
			return __('Verification failed', c_bid_text_domain);
		}

		// Get the currently logged in user, iff they authenticated
		// using BrowserID
		function Get_browserid_loggedin_user() {
			global $user_email;
			get_currentuserinfo();

			if ( isset( $_COOKIE[c_bid_browserid_login_cookie] ) ) {
				return $user_email;
			}

			return null;
		}

		// Check if an assertion is received. If one has been, verify it and
		// log the user in. If not, continue.
		function Check_assertion($assertion) {
			// Verify assertion
			$response = self::Post_assertion_to_verifier($assertion);

			// Decode response. If the response is invalid, an error
			// message will be printed.
			$result = self::Check_verifier_response($response);

			if ($result) {
				$email = $result['email'];
				// Succeeded
				if (self::Is_comment())
					self::Handle_comment($email);
				else if (self::Is_registration())
					self::Handle_registration($email);
				else
					self::Handle_login($email);
			}
		}

		// Get the audience
		function Get_audience() {
			return $_SERVER['HTTP_HOST'];
		}

		// Get an assertion from that request
		function Get_assertion() {
			// Workaround for Microsoft IIS bug
			if (isset($_REQUEST['?browserid_assertion']))
				$_REQUEST['browserid_assertion'] = $_REQUEST['?browserid_assertion'];

			return isset($_REQUEST['browserid_assertion']) ?
					$_REQUEST['browserid_assertion'] : null;
		}

		function Get_rememberme() {
			return (isset($_REQUEST['rememberme']) && $_REQUEST['rememberme'] == 'true');
		}

		// Post the assertion to the verifier. If the assertion does not
		// verify, an error message will be displayed and no more processing
		// will occur
		function Post_assertion_to_verifier($assertion) {
			$audience = self::Get_audience();

			// Get verification server URL
			$vserver = self::Get_option_vserver();

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
					'audience' => $audience
				),
				'cookies' => array(),
				'sslverify' => true
			);

			if (self::Is_option_debug())
				update_option(c_bid_option_request, $vserver . ' ' . print_r($args, true));

			// Verify assertion
			$response = wp_remote_post($vserver, $args);

			// If error, print the error message and exit.
			if (is_wp_error($response)) {
				// Debug info
				$message = __($response->get_error_message());
				if (self::Is_option_debug()) {
					update_option(c_bid_option_response, $response);
				}

				self::Handle_error($message, $message, $response);
			}

			// Persist debug info
			if (self::Is_option_debug()) {
				$response['vserver'] = self::Get_option_vserver();
				$response['audience'] = self::Get_audience();
				$response['rememberme'] = self::Get_rememberme();
				update_option(c_bid_option_response, $response);
			}


			return $response;
		}

		// Check result. If result is either invalid or indicates a bad
		// assertion, an error message will be printed and processing
		// will stop. If verification succeeds, response will be returned.
		function Check_verifier_response($response) {
			$result = json_decode($response['body'], true);

			if (empty($result) || empty($result['status'])) {
				// No result or status
				$message = __('Verification response invalid',
									c_bid_text_domain);

				$debug_message = $message . PHP_EOL . $response['response']['message'];
			}
			else if ($result['status'] != 'okay') {
				// Bad status
				$message = __('Verification failed', c_bid_text_domain);
				if (isset($result['reason']))
					$message .= ': ' . __($result['reason'], c_bid_text_domain);

				$debug_message = $message . PHP_EOL;
			}
			else {
				// Succeeded
				return $result;
			}

			// Verification has failed, display erorr and stop processing.
			$debug_message .= 'audience=' . self::Get_audience() . PHP_EOL;
			$debug_message .= 'vserver=' . parse_url(self::Get_option_vserver(), PHP_URL_HOST) . PHP_EOL;
			$debug_message .= 'time=' . time();

			self::Handle_error($message, $debug_message, $result);
		}

		// Determine if login or comment
		function Is_comment() {
			$options = get_option('browserid_options');
			if (self::Is_option_comments() || self::Is_option_bbpress())
				return (isset($_REQUEST['browserid_comment']) ? $_REQUEST['browserid_comment'] : null);
			else
				return null;
		}

		function Is_registration() {
			$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
			return $action == 'register';
		}

		// Generic error handling
		function Handle_error($message, $debug_message = '', $result = '') {
			if (self::Is_option_debug() && !empty($debug_message)) {
				header('Content-type: text/plain');
				echo $debug_message . PHP_EOL;

				if (!empty($result)) {
					print_r($result);
				}
			} else {
				$post_id = self::Is_comment();
				$redirect = self::Get_request_redirect_url();
				$url = ($post_id ? get_permalink($post_id) : wp_login_url($redirect));
				$url .= (strpos($url, '?') === false ? '?' : '&') . 'browserid_error=' . urlencode($message);
				if ($post_id)
					$url .= '#browserid_' . $post_id;
				wp_redirect($url);
			}

			exit();
		}

		// Process login
		function Handle_login($email) {
			// Login
			$user = self::Login_by_email($email, self::Get_rememberme());
			if ($user) {
				// Beam me up, Scotty!
				$redirect_to = self::Get_login_redirect_url();
				$redirect_to = apply_filters('login_redirect', $redirect_to, '', $user);
				wp_redirect($redirect_to);
				exit();
			}
			else {
				$message = __('You must already have an account to log in with Persona.', c_bid_text_domain);
				self::Handle_error($message);
			}
		}

		// Login user using e-mail address
		function Login_by_email($email, $rememberme) {
			$userdata = get_user_by('email', $email);
			return self::Login_by_userdata($userdata, $rememberme);
		}

		// Login user using id
		function Login_by_id($user_id, $rememberme) {
			$userdata = get_user_by('id', $user_id);
			return self::Login_by_userdata($userdata, $rememberme);
		}

		// Login user by userdata
		function Login_by_userdata($userdata, $rememberme) {
			global $user;
			$user = null;

			if ($userdata) {
				$this->browserid_login = true;
				$user = wp_signon(array(
					'user_login' => $userdata->user_login,
					'user_password' => 'fake_password',
					'remember' => true
				));
			}
			return $user;
		}

		// Process comment
		function Handle_comment($email) {
			// Initialize
			$author = $_REQUEST['author'];
			$url = $_REQUEST['url'];

			// Check WordPress user
			$userdata = get_user_by('email', $email);
			if ($userdata) {
				$author = $userdata->display_name;
				$url = $userdata->user_url;
			}
			else if (empty($author) || empty($url)) {
				// Check Gravatar profile
				$response = wp_remote_get('http://www.gravatar.com/' . md5($email) . '.json');
				if (!is_wp_error($response)) {
					$json = json_decode($response['body']);
					if (empty($author))
						$author = $json->entry[0]->displayName;
				}
			}

			if (empty($author)) {
				// Use first part of e-mail
				$parts = explode('@', $email);
				$author = $parts[0];
			}


			// Update post variables
			$_POST['author'] = $author;
			$_POST['email'] = $email;
			$_POST['url'] = $url;
			// bbPress
			$_POST['bbp_anonymous_name'] = $author;
			$_POST['bbp_anonymous_email'] = $email;
			$_POST['bbp_anonymous_website'] = $url;
		}

		// Set a cookie that keeps track whether the user signed in
		// using BrowserID
		function Set_auth_cookie_action($auth_cookie, $expire, $expiration, $user_id, $scheme) {
			// Persona should only manage Persona logins. If this is
			// a Persona login, keep track of it so that the user is
			// not automatically logged out if they log in via other means.
			if ($this->browserid_login) {
				$secure = $scheme == "secure_auth";
				setcookie(c_bid_browserid_login_cookie, 1, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
			}
			else {
				// If the user is not logged in via BrowserID, clear the
				// cookie.
				self::Clear_auth_cookie_action();
			}
		}

		// Clear the cookie that keeps track of whether the user
		// signed in using BrowserID
		function Clear_auth_cookie_action() {
			$expire = time() - YEAR_IN_SECONDS;
			setcookie(c_bid_browserid_login_cookie, ' ', $expire, COOKIEPATH, COOKIE_DOMAIN);
		}

		function Disallow_non_persona_logins_filter($user) {
			if (! $this->browserid_login) {
				return new WP_error('invalid_login',
						'Only BrowserID logins are allowed');
			}
			return $user;
		}


		function Allow_fake_password_if_persona_login($check) {
			// Passwords are handled by assertions in Persona authentication.
			// If this is a Persona login, the password is always good. This
			// allows for the fake password to be passed to wp_login above.
			if ($this->browserid_login) {
				return true;
			}
			return $check;
		}

		// Add login button to login page
		function Add_persona_to_login_form() {
			echo '<p>' . self::Get_loginout_html(false) . '<br /><br /></p>';
		}

		// Add Persona button to registration form and remove the email form.
		function Add_persona_to_registration_form() {
			// Only enable registration via Persona if Persona is the only
			// authentication mechanism or else the user will not see the
			// "check your email" screen.
			if (self::Is_option_browserid_only_auth()) {
				echo '<input type="hidden" name="browserid_assertion" id="browserid_assertion" />';

				$html = __('Register', c_bid_text_domain) ;

				self::Print_persona_button_html("js-persona__register", $html);
			}
		}

		// Process registration - get the email address from the assertion and
		// process the rest of the form.
		function Handle_registration($email) {
			if (self::Is_option_browserid_only_auth()) {
				// Keep track of whether the user is registering with
				// BrowserID. Non BrowserID registrations are disabled in
				// BrowserID only auth.
				$this->user_registering_with_browserid = true;
				$_POST['user_email'] = $email;
			}
		}


		// Now that the user is registered, log them in
		function Sign_in_new_persona_user($user_id) {
			if ($this->browserid_login) {
				return self::Login_by_id($user_id, false);
			}
		}

		// Check if traditional registration has been disabled.
		function Disallow_non_persona_registration_filter($errors) {
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

		function Registration_redirect_filter($redirect_to) {
			if ($redirect_to) return $redirect_to;

			if (self::Is_option_browserid_only_auth()) {
				// The user successfully signed up using Persona,
				// send them to their profile page
				return self::Get_registration_redirect_url();
			}

			return '';
		}

		// If only BrowserID logins are allowed, a reset password form should
		// not be shown.
		function Disallow_lost_password() {
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			login_header(__('Password reset disabled', c_bid_text_domain),
				'<p class="message">' . sprintf(__('%s uses Mozilla Persona to sign in and does not use passwords. Password reset is disabled.', c_bid_text_domain), $blogname) . "</p>");
			login_footer('user_login');
			exit();
		}

		// Disable reset password if in BrowserID only mode
		function Disallow_password_reset() {
			return false;
		}

		// Disable change password form if in BrowserID only mode
		function Hide_password_fields() {
			return false;
		}

		// In Disable Non-Persona auth mode, Hide the "Lost your password?"
		// link from the login page by not giving it any text. If the user
		// still lands on the reset password page, a nice error screen is
		// shown saying "no way, Jose."
		function Hide_lost_password_text($text) {
			if ($text == 'Lost your password?') {
				$text = '';
			}
			return $text;
		}

		// bbPress integration
		function bbPress_submit() {
			$id = bbp_get_topic_id();
			if (empty($id))
				$id = bbp_get_forum_id();
			self::Comment_form_action($id);
		}

		// Imply anonymous commenting
		function bbPress_anonymous() {
			return !is_user_logged_in();
		}

		// Get rid of the email field in the comment form
		function Comment_form_action_default_fields_filter($fields) {
			if (self::Is_option_comments()) {
				unset($fields['email']);
			}
			return $fields;
		}

		// Add BrowserID to comment form
		function Comment_form_action($post_id) {
			if (!is_user_logged_in()) {
				$html = $this->Get_option_comment_html();
				$this->Print_persona_button_html("js-persona__submit-comment", $html);
			}

			// Display error message
			if (isset($_REQUEST['browserid_error'])) {
				self::Print_persona_error($_REQUEST['browserid_error'], 'persona__error-comment');
			}
		}

		// If Persona-Only auth is enabled, comment must be submitted with an
		// assertion.
		function Pre_comment_approved_filter($approved, $commentdata) {
			$assertion = self::Get_assertion();
			if (empty($assertion)) {
				if ( defined('DOING_AJAX') )
					die(__('Comment must be submitted using Persona'));

				wp_die(__('Comment must be submitted using Persona'));
			}
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

			$color = $this->Get_option_button_color();
			$button_html = sprintf($button_html,
				"Mozilla Persona",
				"persona-button " . $color,
				$classname,
				"persona-button__text",
				$html,
				self::What_is());

			return $button_html;
		}

		// Print a Persona button
		function Print_persona_button_html($classname, $html) {
			echo self::Get_persona_button_html($classname, $html);
		}

		// Shortcode "mozilla_persona"
		function Shortcode_loginout() {
			return self::Get_loginout_html();
		}

		// Git spiffy logout text for Persona
		function Get_logout_text() {
			// User logged in
			$options = get_option('browserid_options');
			$html = __($options['browserid_logout_html'], c_bid_text_domain);

			return $html;
		}


		// Build HTML for login/out button/link
		function Get_loginout_html($check_login = true) {
			$options = get_option('browserid_options');

			if ($check_login && is_user_logged_in()) {
				$html = self::Get_logout_text();

				// Simple link
				if (empty($html))
					return '';
				else
					return '<a href="#" class="js-persona__logout">' . $html . '</a>';
			}
			else {
				// User not logged in
				$html = __($options['browserid_login_html'], c_bid_text_domain);
				// Button
				$html = self::Get_persona_button_html("js-persona__login", $html);

				return $html;
			}
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

		// Override logout on site menu
		function Admin_toolbar_action($wp_toolbar) {
			$logged_in_user = self::Get_browserid_loggedin_user();

			// If the user is signed in via Persona, replace their toolbar logout
			// with a logout that will work with Persona.
			if ( $logged_in_user ) {
				$wp_toolbar->remove_node('logout');
				$wp_toolbar->add_node(array(
					'id' => 'logout',
					'title' => self::Get_logout_text(),
					'parent' => 'user-actions',
					'href' => '#',
					'meta' => array(
						'class' => 'js-persona__logout'
					)
				));
			}
		}


		// Register options page
		function Admin_menu_action() {
			if (function_exists('add_options_page'))
				add_options_page(
					__('Mozilla Persona', c_bid_text_domain) . ' ' . __('Administration', c_bid_text_domain),
					__('Mozilla Persona', c_bid_text_domain),
					'manage_options',
					__FILE__,
					array(&$this, 'Administration'));
		}

		// Define options page
		function Admin_init_action() {
			register_setting('browserid_options', 'browserid_options', null);
			add_settings_section('plugin_main', null, array(&$this, 'Options_main'), 'browserid');
			add_settings_field('browserid_sitename', __('Site name:', c_bid_text_domain), array(&$this, 'Option_sitename'), 'browserid', 'plugin_main');
			add_settings_field('browserid_sitelogo', __('Site logo:', c_bid_text_domain), array(&$this, 'Option_sitelogo'), 'browserid', 'plugin_main');
			add_settings_field('browserid_background_color', __('Dialog background color:', c_bid_text_domain), 
					array(&$this, 'Option_background_color'), 'browserid', 'plugin_main');
			add_settings_field('browserid_terms_of_service', __('Terms of service:', c_bid_text_domain), 
					array(&$this, 'Option_terms_of_service'), 'browserid', 'plugin_main');
			add_settings_field('browserid_privacy_policy', __('Privacy policy:', c_bid_text_domain), 
					array(&$this, 'Option_privacy_policy'), 'browserid', 'plugin_main');
			add_settings_field('browserid_only_auth', __('Disable non-Persona logins:', c_bid_text_domain), array(&$this, 'Option_browserid_only_auth'), 'browserid', 'plugin_main');
			add_settings_field('browserid_button_color', __('Login button color:', c_bid_text_domain), 
					array(&$this, 'Option_button_color'), 'browserid', 'plugin_main');
			add_settings_field('browserid_login_html', __('Login button HTML:', c_bid_text_domain), array(&$this, 'Option_login_html'), 'browserid', 'plugin_main');
			add_settings_field('browserid_logout_html', __('Logout button HTML:', c_bid_text_domain), array(&$this, 'Option_logout_html'), 'browserid', 'plugin_main');

			add_settings_field('browserid_login_redir', __('Login redirection URL:', c_bid_text_domain), array(&$this, 'Option_login_redir'), 'browserid', 'plugin_main');
			add_settings_field('browserid_comments', __('Enable for comments:', c_bid_text_domain), array(&$this, 'Option_comments'), 'browserid', 'plugin_main');
			add_settings_field('browserid_comment_html', __('Comment button HTML:', c_bid_text_domain), 
					array(&$this, 'Option_comment_html'), 'browserid', 'plugin_main');
			add_settings_field('browserid_bbpress', __('Enable bbPress integration:', c_bid_text_domain), array(&$this, 'Option_bbpress'), 'browserid', 'plugin_main');
			add_settings_field('browserid_persona_source', __('Persona source:', c_bid_text_domain), array(&$this, 'Option_persona_source'), 'browserid', 'plugin_main');
			add_settings_field('browserid_vserver', __('Verification server:', c_bid_text_domain), array(&$this, 'Option_vserver'), 'browserid', 'plugin_main');
			add_settings_field('browserid_debug', __('Debug mode:', c_bid_text_domain), array(&$this, 'Option_debug'), 'browserid', 'plugin_main');
		}

		// set a fake password when creating a password for a user.
		// only called if "BrowserID Only" auth is set.
		function Admin_action_createuser() {
			if (! (isset( $_POST['pass1']) && isset( $_POST['pass2']))) {
				$user_pass = wp_generate_password( 12, false);
				$_POST['pass1'] = $user_pass;
				$_POST['pass2'] = $user_pass;
			}
		}

		// Main options section
		function Options_main() {
			// Empty
		}

		// Print a text input for a plugin option
		function Print_option_text_input($option_name, $default_value = null, $info = null) {
			$option_value = $this->Get_option($option_name, $default_value);
			echo sprintf("<input id='%s' name='browserid_options[%s]' type='text' size='50' value='%s' />",
				$option_name,
				$option_name,
				htmlspecialchars($option_value, ENT_QUOTES));

			if ($info) {
				echo '<br />' . $info;
			}
		}


		// Generic Get_option to get an option, if it is not set, return the 
		// default value
		function Get_option($option_name, $default_value = '') {
			$options = get_option('browserid_options');
			if (isset($options[$option_name]) 
					&& !empty($options[$option_name])) {
				return $options[$option_name];
			}
			return $default_value;
		}


		// Site name option
		function Option_sitename() {
			self::Print_option_text_input('browserid_sitename', self::Get_sitename());
		}

		// Get (customized) site name
		function Get_sitename() {
			$name = $this->Get_option('browserid_sitename');
			if (empty($name))
				$name = get_bloginfo('name');
			return $name;
		}


		// Site logo option
		function Option_sitelogo() {
			self::Print_option_text_input('browserid_sitelogo', null,
					__('Absolute path, works only with SSL', c_bid_text_domain));
		}

		// Get site logo
		function Get_sitelogo() {
			$options = get_option('browserid_options');
			// sitelogo is only valid with SSL connections
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
				return $this->Get_option('browserid_sitelogo');
			return '';
		}


		// backgroundColor option
		function Option_background_color() {
			self::Print_option_text_input('browserid_background_color', null,
					__('3 or 6 character hex value. e.g. #333 or #333333', c_bid_text_domain));
		}

		// Get backgroundColor
		function Get_background_color() {
			return $this->Get_option('browserid_background_color');
		}


		// termsOfService option
		function Option_terms_of_service() {
			self::Print_option_text_input('browserid_terms_of_service', null,
					__('URL or absolute path, works only with SSL and must be defined together with Privacy policy', c_bid_text_domain));
		}

		function Get_terms_of_service() {
			return $this->Get_option('browserid_terms_of_service');
		}


		// privacyPolicy option
		function Option_privacy_policy() {
			self::Print_option_text_input('browserid_privacy_policy', null,
					__('URL or absolute path, works only with SSL and must be and must be defined together with Terms of service', c_bid_text_domain));
		}

		function Get_privacy_policy() {
			return $this->Get_option('browserid_privacy_policy');
		}


		// Login HTML option
		function Option_login_html() {
			self::Print_option_text_input('browserid_login_html', 
					__('Sign in with your email', c_bid_text_domain));
		}

		// Logout HTML option
		function Option_logout_html() {
			self::Print_option_text_input('browserid_logout_html', __('Logout', c_bid_text_domain));
		}

		// Login redir URL option
		function Option_login_redir() {
			self::Print_option_text_input('browserid_login_redir', null,
					__('Default WordPress dashboard', c_bid_text_domain));
		}

		// Get the login redir URL
		function Get_option_login_redir() {
			return $this->Get_option('browserid_login_redir', null);
		}

		// Enable comments integration
		function Option_comments() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_comments']) && $options['browserid_comments'] ? " checked='checked'" : '');
			echo "<input id='browserid_comments' name='browserid_options[browserid_comments]' type='checkbox'" . $chk. "/>";
		}

		// Can a user leave a comment using BrowserID
		function Is_option_comments() {
			return $this->Get_option('browserid_comments', false);
		}

		function Option_comment_html() {
			self::Print_option_text_input('browserid_comment_html', __('Post Comment', c_bid_text_domain));
		}

		function Get_option_comment_html() {
			return $this->Get_option('browserid_comment_html', __('post comment', c_bid_text_domain));
		}

		// Enable bbPress integration
		function Option_bbpress() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_bbpress']) && $options['browserid_bbpress'] ? " checked='checked'" : '');
			echo "<input id='browserid_bbpress' name='browserid_options[browserid_bbpress]' type='checkbox'" . $chk. "/>";
			echo '<strong>Beta!</strong>';
			echo '<br />' . __('Enables anonymous posting implicitly', c_bid_text_domain);
		}

		function Is_option_bbpress() {
			$options = get_option('browserid_options');

			return isset($options['browserid_bbpress']) &&
						$options['browserid_bbpress'];
		}

		// Persona shim source option
		function Option_persona_source() {
			self::Print_option_text_input('browserid_persona_source', c_bid_source,
					__('Default', c_bid_text_domain) . ' ' . c_bid_source);
		}

		function Get_option_persona_source() {
			return $this->Get_option('browserid_persona_source', c_bid_source);
		}

		// Verification server option
		function Option_vserver() {
			self::Print_option_text_input('browserid_vserver', self::Get_option_vserver(),
					__('Default', c_bid_text_domain) . ' ' . c_bid_verifier . '/verify');
		}

		function Get_option_vserver() {
			$options = get_option('browserid_options');
			$source = self::Get_option_persona_source();

			if (isset($options['browserid_vserver']) && $options['browserid_vserver'])
				$vserver = $options['browserid_vserver'];
			else if ($source != c_bid_source)
				$vserver = $source . '/verify';
			else
				$vserver = c_bid_verifier . '/verify';

			return $vserver;
		}

		// Debug option
		function Option_debug() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_debug']) && $options['browserid_debug'] ? " checked='checked'" : '');
			echo "<input id='browserid_debug' name='browserid_options[browserid_debug]' type='checkbox'" . $chk. "/>";
			echo '<strong>' . __('Security risk!', c_bid_text_domain) . '</strong>';
		}

		// Is the debug option set
		function Is_option_debug() {
			$options = get_option('browserid_options');
			return ((isset($options['browserid_debug']) && $options['browserid_debug']));
		}

		// Only allow Persona logins
		function Option_browserid_only_auth() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_only_auth']) && $options['browserid_only_auth'] ? " checked='checked'" : '');
			echo "<input id='browserid_only_auth' name='browserid_options[browserid_only_auth]' type='checkbox'" . $chk. "/>";
		}

		// Does the site have browserid only authentication enabled.
		function Is_option_browserid_only_auth() {
			$options = get_option('browserid_options');

			return isset($options['browserid_only_auth']) && $options['browserid_only_auth'];
		}

		function Option_button_color() {
			echo "<ul>";
			$this->Print_persona_button_selection(__('Blue', c_bid_text_domain), 'blue');
			$this->Print_persona_button_selection(__('Black', c_bid_text_domain), 'dark');
			$this->Print_persona_button_selection(__('Orange', c_bid_text_domain), 'orange');
			echo "</ul>";
		}

		function Get_option_button_color() {
			return $this->Get_option('browserid_button_color', 'blue');
		}

		function Print_persona_button_selection($name, $value) {
			$color = $this->Get_option_button_color();
			$chk = ($color == $value ? " checked='checked'" : '');

			echo "<li class='persona-button--select-color'>" .
					 "<input name='browserid_options[browserid_button_color]' " .
							"class='persona-button--select-color-radio'" .
							"type='radio' value='". $value ."'" . $chk. "/>" .
					 "<label class='persona-button " . $value ."'>" .
						 "<span class='persona-button__text'>" . $name . "</span>" .
					 "</label>" .
				 "</li>";
		}

		// Render options page
		function Administration() {
?>
			<div class="wrap">
				<h2><?php _e('Mozilla Persona', c_bid_text_domain); ?></h2>
				<form method="post" action="options.php">
					<?php settings_fields('browserid_options'); ?>
					<?php do_settings_sections('browserid'); ?>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</form>
			</div>
<?php
			if (self::Is_option_debug()) {
				$options = get_option('browserid_options');
				$request = get_option(c_bid_option_request);
				$response = get_option(c_bid_option_response);
				if (is_wp_error($response))
					$result = $response;
				else
					$result = json_decode($response['body'], true);

				echo '<p><strong>Site URL</strong>: ' . get_site_url() . ' (WordPress address / folder)</p>';
				echo '<p><strong>Home URL</strong>: ' . get_home_url() . ' (Blog address / Home page)</p>';

				if (!empty($result) && !is_wp_error($result)) {
					echo '<p><strong>PHP Time</strong>: ' . time() . ' > ' . date('c', time()) . '</p>';
					echo '<p><strong>Assertion valid until</strong>: ' . $result['expires'] . ' > ' . date('c', $result['expires'] / 1000) . '</p>';
				}

				echo '<p><strong>PHP audience</strong>: ' . htmlentities($_SERVER['HTTP_HOST']) . '</p>';
				echo '<script type="text/javascript">';
				echo 'document.write("<p><strong>JS audience</strong>: " + window.location.hostname + "</p>");';
				echo '</script>';

				echo '<br /><pre>Options=' . htmlentities(print_r($options, true)) . '</pre>';
				echo '<br /><pre>BID request=' . htmlentities(print_r($request, true)) . '</pre>';
				echo '<br /><pre>BID response=' . htmlentities(print_r($response, true)) . '</pre>';
				echo '<br /><pre>PHP request=' . htmlentities(print_r($_REQUEST, true)) . '</pre>';
				echo '<br /><pre>PHP server=' . htmlentities(print_r($_SERVER, true)) . '</pre>';
			}
			else {
				delete_option(c_bid_option_request);
				delete_option(c_bid_option_response);
			}
		}

		function http_api_curl($handle) {
			curl_setopt($handle, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
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

// Define widget
class BrowserID_Widget extends WP_Widget {
	// Widget constructor
	function BrowserID_Widget() {
		$widget_ops = array(
			'classname' => 'browserid_widget',
			'description' => __('Mozilla Persona login button', c_bid_text_domain)
		);
		$this->WP_Widget('BrowserID_Widget', 'Mozilla Persona', $widget_ops);
	}

	// Widget contents
	function widget($args, $instance) {
		global $persona_plugin;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if (!empty($title))
			echo $before_title . $title . $after_title;

		echo "<ul><li class='only-child'>" . $persona_plugin->Get_loginout_html() . "</li></ul>";
		echo $after_widget;
	}

	// Update settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	// Render settings
	function form($instance) {
		if (empty($instance['title']))
			$instance['title'] = null;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
<?php
	}
}

// Start plugin
global $persona_plugin;
if (empty($persona_plugin)) {
	$persona_plugin = new MozillaPersona();
	// Check pre-requisites
	$persona_plugin->Check_prerequisites();

	register_activation_hook(__FILE__, array(&$persona_plugin, 'Activate'));
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

		// Get plugin options
		$options = get_option('browserid_options');

		// XXX Collapse this in to the Get_browserid_only_auth
		if ((isset($options['browserid_only_auth']) &&
					$options['browserid_only_auth'])) {
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
