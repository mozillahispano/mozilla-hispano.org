<?php
/*
Plugin Name: Mozilla Persona
Plugin URI: http://wordpress.org/extend/plugins/browserid/
Description: Mozilla Persona, the safest & easiest way to sign in
Version: 0.42
Author: Marcel Bokhorst
Author URI: http://blog.bokhorst.biz/about/
*/

/*
	Copyright (c) 2011, 2012 Marcel Bokhorst

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

#error_reporting(E_ALL);

// Check PHP version
if (version_compare(PHP_VERSION, '5.0.0', '<'))
	die('Mozilla Persona requires at least PHP 5, installed version is ' . PHP_VERSION);

// Define constants
define('c_bid_text_domain', 'browserid');
define('c_bid_option_version', 'bid_version');
define('c_bid_option_request', 'bid_request');
define('c_bid_option_response', 'bid_response');

// Define class
if (!class_exists('M66BrowserID')) {
	class M66BrowserID {
		// Class variables
		var $debug = null;

		// Constructor
		function __construct() {
			// Get plugin options
			$options = get_option('browserid_options');

			// Debug mode
			$this->debug = (isset($options['browserid_debug']) && $options['browserid_debug']);

			// Register de-activation
			register_deactivation_hook(__FILE__, array(&$this, 'Deactivate'));

			// Register actions & filters
			add_action('init', array(&$this, 'Init'), 0);
			add_filter('login_message', array(&$this, 'Login_message'));
			add_action('login_form', array(&$this, 'Login_form'));
			add_action('widgets_init', create_function('', 'return register_widget("BrowserID_Widget");'));
			if (is_admin()) {
				add_action('admin_menu', array(&$this, 'Admin_menu'));
				add_action('admin_init', array(&$this, 'Admin_init'));
			}
			add_action('http_api_curl', array(&$this, 'http_api_curl'));

			// Comment integration
			if (isset($options['browserid_comments']) && $options['browserid_comments'])
				add_action('comment_form', array(&$this, 'Comment_form'));

			// bbPress integration
			if (isset($options['browserid_bbpress']) && $options['browserid_bbpress']) {
				add_action('bbp_allow_anonymous', create_function('', 'return !is_user_logged_in();'));
				add_action('bbp_is_anonymous', create_function('', 'return !is_user_logged_in();'));
				add_action('bbp_theme_before_topic_form_submit_button', array(&$this, 'bbPress_submit'));
				add_action('bbp_theme_before_reply_form_submit_button', array(&$this, 'bbPress_submit'));
			}

			// Shortcode
			add_shortcode('browserid_loginout', array(&$this, 'Shortcode_loginout'));
			add_shortcode('mozilla_persona', array(&$this, 'Shortcode_loginout'));
		}

		// Handle plugin activation
		function Activate() {
			global $wpdb;
			$version = get_option(c_bid_option_version);
			if ($version < 2) {
				$options = get_option('browserid_options');
				$options['browserid_logout_html'] = __('Logout', c_bid_text_domain);
				update_option('browserid_options', $options);
			}
			update_option(c_bid_option_version, 2);
		}

		// Handle plugin deactivation
		function Deactivate() {
			// TODO: delete options
		}

		// Initialization
		function Init() {
			// I18n
			load_plugin_textdomain(c_bid_text_domain, false, dirname(plugin_basename(__FILE__)));

			// Check for assertion
			self::Check_assertion();

			// Enqueue BrowserID scripts
			wp_register_script('browserid', 'https://login.persona.org/include.js', array(), '', true);
			wp_register_script('browserid_login', plugins_url('login.js', __FILE__), array('browserid'), '', true);

			// Prepare for comments and bbPress
			$options = get_option('browserid_options');
			if ((isset($options['browserid_comments']) && $options['browserid_comments']) ||
				(isset($options['browserid_bbpress']) && $options['browserid_bbpress'])) {
				wp_enqueue_script('browserid_comments', plugins_url('comments.js', __FILE__), array('jquery', 'browserid'), '', true);
				$data_array = array(
					'browserid_failed' => __('Verification failed', c_bid_text_domain),
					'browserid_sitename' => self::Get_sitename(),
					'browserid_sitelogo' => self::Get_sitelogo()
				);
				wp_localize_script('browserid_comments', 'browserid_comments', $data_array);
			}
		}

		function Check_assertion() {
			// Workaround for Microsoft IIS bug
			if (isset($_REQUEST['?browserid_assertion']))
				$_REQUEST['browserid_assertion'] = $_REQUEST['?browserid_assertion'];

			// Verify received assertion
			if (isset($_REQUEST['browserid_assertion'])) {
				// Get options
				$options = get_option('browserid_options');

				// Get assertion/audience/remember me
				$assertion = $_REQUEST['browserid_assertion'];
				$audience = $_SERVER['HTTP_HOST'];

				$rememberme = (isset($_REQUEST['rememberme']) && $_REQUEST['rememberme'] == 'true');

				// Get verification server URL
				if (isset($options['browserid_vserver']) && $options['browserid_vserver'])
					$vserver = $options['browserid_vserver'];
				else
					$vserver = 'https://verifier.login.persona.org/verify';

				// No SSL verify?
				$noverify = (isset($options['browserid_noverify']) && $options['browserid_noverify']);

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
					'sslverify' => !$noverify
				);
				if ($this->debug)
					update_option(c_bid_option_request, $vserver . ' ' . print_r($args, true));

				// Verify assertion
				$response = wp_remote_post($vserver, $args);

				// Check result
				if (is_wp_error($response)) {
					// Debug info
					$message = __($response->get_error_message());
					if ($this->debug) {
						update_option(c_bid_option_response, $response);
						header('Content-type: text/plain');
						echo $message . PHP_EOL;
						print_r($response);
						exit();
					}
					else
						self::Handle_error($message);
				}
				else {
					// Persist debug info
					if ($this->debug) {
						$response['vserver'] = $vserver;
						$response['audience'] = $audience;
						$response['rememberme'] = $rememberme;
						update_option(c_bid_option_response, $response);
					}

					// Decode response
					$result = json_decode($response['body'], true);

					// Check result
					if (empty($result) || empty($result['status'])) {
						// No result or status
						$message = __('Verification void', c_bid_text_domain);
						if ($this->debug) {
							header('Content-type: text/plain');
							echo $message . PHP_EOL;
							echo $response['response']['message'] . PHP_EOL;
							print_r($response);
							exit();
						}
						else
							self::Handle_error($message);
					}
					else if ($result['status'] == 'okay' &&
							$result['audience'] == $audience) {
						// Check expiry time
						$novalid = (isset($options['browserid_novalid']) && $options['browserid_novalid']);
						if ($novalid || time() < $result['expires'] / 1000)
						{
							// Succeeded
							if (self::Is_comment())
								self::Handle_comment($result);
							else
								self::Handle_login($result, $rememberme);
						}
						else {
							$message = __('Verification invalid', c_bid_text_domain);
							if ($this->debug) {
								header('Content-type: text/plain');
								echo $message . PHP_EOL;
								echo 'time=' . time() . PHP_EOL;
								print_r($result);
								exit();
							}
							else
								self::Handle_error($message);
						}
					}
					else {
						// Failed
						$message = __('Verification failed', c_bid_text_domain);
						if (isset($result['reason']))
							$message .= ': ' . __($result['reason'], c_bid_text_domain);
						if ($this->debug) {
							header('Content-type: text/plain');
							echo $message . PHP_EOL;
							echo 'audience=' . $audience . PHP_EOL;
							echo 'vserver=' . parse_url($vserver, PHP_URL_HOST) . PHP_EOL;
							echo 'time=' . time() . PHP_EOL;
							print_r($result);
							exit();
						}
						else
							self::Handle_error($message);
					}
				}
			}
		}

		// Determine if login or comment
		function Is_comment() {
			$options = get_option('browserid_options');
			if ((isset($options['browserid_comments']) && $options['browserid_comments']) ||
				(isset($options['browserid_bbpress']) && $options['browserid_bbpress']))
				return (isset($_REQUEST['browserid_comment']) ? $_REQUEST['browserid_comment'] : null);
			else
				return null;
		}

		// Generic error handling
		function Handle_error($message) {
			$post_id = self::Is_comment();
			$redirect = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null;
			$url = ($post_id ? get_permalink($post_id) : wp_login_url($redirect));
			$url .= (strpos($url, '?') === false ? '?' : '&') . 'browserid_error=' . urlencode($message);
			if ($post_id)
				$url .= '#browserid_' . $post_id;
			wp_redirect($url);
			exit();
		}

		// Process login
		function Handle_login($result, $rememberme) {
			$options = get_option('browserid_options');
			// Login
			$user = self::Login_by_email($result['email'], $rememberme);
			if ($user) {
				// Beam me up, Scotty!
				if (isset($options['browserid_login_redir']) && $options['browserid_login_redir'])
					$redirect_to = $options['browserid_login_redir'];
				else if (isset($_REQUEST['redirect_to']))
					$redirect_to = $_REQUEST['redirect_to'];
				else
					$redirect_to = admin_url();
				$redirect_to = apply_filters('login_redirect', $redirect_to, '', $user);
				wp_redirect($redirect_to);
				exit();
			}
			else {
				// User not found?
				$message = __('Login failed', c_bid_text_domain);
				$message .= ' (' . $result['email'] . ')';
				if ($this->debug) {
					header('Content-type: text/plain');
					echo $message . PHP_EOL;
					print_r($result);
					exit();
				}
				else
					self::Handle_error($message);
			}
		}

		// Login user using e-mail address
		function Login_by_email($email, $rememberme) {
			global $user;
			$user = null;

			$userdata = get_user_by('email', $email);
			if ($userdata) {
				$user = new WP_User($userdata->ID);
				wp_set_current_user($userdata->ID, $userdata->user_login);
				wp_set_auth_cookie($userdata->ID, $rememberme);
				do_action('wp_login', $userdata->user_login);
			}
			return $user;
		}

		// Process comment
		function Handle_comment($result) {
			// Initialize
			$email = $result['email'];
			$author = '';
			$url = '';

			// Check WordPress user
			$userdata = get_user_by('email', $email);
			if ($userdata) {
				$author = $userdata->display_name;
				$url = $userdata->user_url;
			}
			else {
				// Check Gravatar profile
				$response = wp_remote_get('http://www.gravatar.com/' . md5($email) . '.json');
				if (!is_wp_error($response)) {
					$json = json_decode($response['body']);
					$author = $json->entry[0]->displayName;
					$url = $json->entry[0]->profileUrl;
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

		// Filter login error message
		function Login_message($message) {
			if (isset($_REQUEST['browserid_error']))
				$message .= '<div id="login_error"><strong>' . htmlentities(stripslashes($_REQUEST['browserid_error'])) . '</strong></div>';
			return $message;
		}

		// Add login button to login page
		function Login_form() {
			echo '<p>' . self::Get_loginout_html(false) . '<br /><br /></p>';
		}

		// bbPress integration
		function bbPress_submit() {
			$id = bbp_get_topic_id();
			if (empty($id))
				$id = bbp_get_forum_id();
			self::Comment_form($id);
		}

		// Imply anonymous commenting
		function bbPress_anonymous() {
			return !is_user_logged_in();
		}

		// Add BrowserID to comment form
		function Comment_form($post_id) {
			if (!is_user_logged_in()) {
				// Get link content
				$options = get_option('browserid_options');
				if (empty($options['browserid_comment_html']))
					$html = '<img src="' . self::Get_image_url() . '" style="border: none; vertical-align: middle; margin-right: 5px;" />';
				else
					$html = $options['browserid_comment_html'];

				// Render link
				echo '<a href="#" id="browserid_' . $post_id . '" onclick="return browserid_comment(' . $post_id . ');" title="Mozilla Persona" class="browserid">' . $html . '</a>';
				echo self::What_is();

				// Display error message
				if (isset($_REQUEST['browserid_error'])) {
					echo '<span style="color: red; font-weight: bold; margin: 10px; vertical-align: top;">';
					echo htmlspecialchars(stripslashes($_REQUEST['browserid_error']), ENT_QUOTES, get_bloginfo('charset'));
					echo '</span>';
				}
			}
		}

		// Shortcode "mozilla_persona"
		function Shortcode_loginout() {
			return self::Get_loginout_html();
		}

		// Build HTML for login/out button/link
		function Get_loginout_html($check_login = true) {
			$options = get_option('browserid_options');
			wp_enqueue_script('browserid_login');
			$redirect = (isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null);
			$data_array = array(
				'browserid_siteurl' => get_site_url(null, '/'),
				'browserid_redirect' => $redirect,
				'browserid_failed' => __('Verification failed', c_bid_text_domain),
				'browserid_sitename' => self::Get_sitename(),
				'browserid_sitelogo' => self::Get_sitelogo()
			);
			wp_localize_script( 'browserid_login', 'browserid_text', $data_array );

			if ($check_login && is_user_logged_in()) {
				// User logged in
				if (empty($options['browserid_logout_html']))
					$html = '';
				else
					$html = $options['browserid_logout_html'];
				// Simple link
				if (empty($html))
					return '';
				else
					return '<a href="' . wp_logout_url() . '">' . $html . '</a>';
			}
			else {
				// User not logged in
				if (empty($options['browserid_login_html']))
					$html = '<img src="' . self::Get_image_url() . '" style="border: 0;" />';
				else
					$html = $options['browserid_login_html'];
				// Button
				$html = '<a href="#" onclick="return browserid_login();"  title="Mozilla Persona" class="browserid">' . $html . '</a>';
				$html .= '<br />' . self::What_is();

				return $html;
			}
		}

		// Get localized image URL
		function Get_image_url() {
			$image_url = plugins_url('browserid-en_US.png', __FILE__);
			$locale = get_bloginfo('language');
			$locale = str_replace('-', '_', $locale);
			if (!empty($locale)) {
				$image = 'browserid-' . $locale . '.png';
				$image_file = dirname(__FILE__) . '/' . $image;
				if (file_exists($image_file))
					$image_url = plugins_url($image, __FILE__);
			}
			return $image_url;
		}

		function What_is() {
			return '<a href="https://login.persona.org/" target="_blank" style="font-size: smaller;">' . __('What is Persona?', c_bid_text_domain) . '</a>';
		}

		// Get (customized) site name
		function Get_sitename() {
			$name = null;
			$options = get_option('browserid_options');
			if (isset($options['browserid_sitename']))
				$name = $options['browserid_sitename'];
			if (empty($name))
				$name = get_bloginfo('name');
			return $name;
		}

		// Get site logo
		function Get_sitelogo() {
			$options = get_option('browserid_options');
			// sitelogo is only valid with SSL connections
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
				if (isset($options['browserid_sitelogo']))
					return $options['browserid_sitelogo'];
			return '';
		}

		// Register options page
		function Admin_menu() {
			if (function_exists('add_options_page'))
				add_options_page(
					__('Mozilla Persona', c_bid_text_domain) . ' ' . __('Administration', c_bid_text_domain),
					__('Mozilla Persona', c_bid_text_domain),
					'manage_options',
					__FILE__,
					array(&$this, 'Administration'));
		}

		// Define options page
		function Admin_init() {
			register_setting('browserid_options', 'browserid_options', null);
			add_settings_section('plugin_main', null, array(&$this, 'Options_main'), 'browserid');
			add_settings_field('browserid_sitename', __('Site name:', c_bid_text_domain), array(&$this, 'Option_sitename'), 'browserid', 'plugin_main');
			add_settings_field('browserid_sitelogo', __('Site logo:', c_bid_text_domain), array(&$this, 'Option_sitelogo'), 'browserid', 'plugin_main');
			add_settings_field('browserid_login_html', __('Custom login HTML:', c_bid_text_domain), array(&$this, 'Option_login_html'), 'browserid', 'plugin_main');
			add_settings_field('browserid_logout_html', __('Custom logout HTML:', c_bid_text_domain), array(&$this, 'Option_logout_html'), 'browserid', 'plugin_main');
			add_settings_field('browserid_login_redir', __('Login redirection URL:', c_bid_text_domain), array(&$this, 'Option_login_redir'), 'browserid', 'plugin_main');
			add_settings_field('browserid_comments', __('Enable for comments:', c_bid_text_domain), array(&$this, 'Option_comments'), 'browserid', 'plugin_main');
			add_settings_field('browserid_bbpress', __('Enable bbPress integration:', c_bid_text_domain), array(&$this, 'Option_bbpress'), 'browserid', 'plugin_main');
			add_settings_field('browserid_comment_html', __('Custom comment HTML:', c_bid_text_domain), array(&$this, 'Option_comment_html'), 'browserid', 'plugin_main');
			add_settings_field('browserid_vserver', __('Verification server:', c_bid_text_domain), array(&$this, 'Option_vserver'), 'browserid', 'plugin_main');
			add_settings_field('browserid_novalid', __('Do not check valid until time:', c_bid_text_domain), array(&$this, 'Option_novalid'), 'browserid', 'plugin_main');
			add_settings_field('browserid_noverify', __('Do not verify SSL certificate:', c_bid_text_domain), array(&$this, 'Option_noverify'), 'browserid', 'plugin_main');
			add_settings_field('browserid_debug', __('Debug mode:', c_bid_text_domain), array(&$this, 'Option_debug'), 'browserid', 'plugin_main');
		}

		// Main options section
		function Options_main() {
			// Empty
		}

		// Site name option
		function Option_sitename() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_sitename']))
				$options['browserid_sitename'] = null;
			echo "<input id='browserid_sitename' name='browserid_options[browserid_sitename]' type='text' size='100' value='{$options['browserid_sitename']}' />";
			echo '<br />' . __('Default the WordPress site name', c_bid_text_domain);
		}

		// Site logo option
		function Option_sitelogo() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_sitelogo']))
				$options['browserid_sitelogo'] = null;
			echo "<input id='browserid_sitelogo' name='browserid_options[browserid_sitelogo]' type='text' size='100' value='{$options['browserid_sitelogo']}' />";
			echo '<br />' . __('Absolute path, works only with SSL', c_bid_text_domain);
		}

		// Login HTML option
		function Option_login_html() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_login_html']))
				$options['browserid_login_html'] = null;
			echo "<input id='browserid_login_html' name='browserid_options[browserid_login_html]' type='text' size='100' value='{$options['browserid_login_html']}' />";
		}

		// Logout HTML option
		function Option_logout_html() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_logout_html']))
				$options['browserid_logout_html'] = null;
			echo "<input id='browserid_logout_html' name='browserid_options[browserid_logout_html]' type='text' size='100' value='{$options['browserid_logout_html']}' />";
		}

		// Login redir URL option
		function Option_login_redir() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_login_redir']))
				$options['browserid_login_redir'] = null;
			echo "<input id='browserid_login_redir' name='browserid_options[browserid_login_redir]' type='text' size='100' value='{$options['browserid_login_redir']}' />";
			echo '<br />' . __('Default WordPress dashboard', c_bid_text_domain);
		}

		// Enable comments integration
		function Option_comments() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_comments']) && $options['browserid_comments'] ? " checked='checked'" : '');
			echo "<input id='browserid_comments' name='browserid_options[browserid_comments]' type='checkbox'" . $chk. "/>";
			echo '<strong>Beta!</strong>';
		}

		// Enable bbPress integration
		function Option_bbpress() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_bbpress']) && $options['browserid_bbpress'] ? " checked='checked'" : '');
			echo "<input id='browserid_bbpress' name='browserid_options[browserid_bbpress]' type='checkbox'" . $chk. "/>";
			echo '<strong>Beta!</strong>';
			echo '<br />' . __('Enables anonymous posting implicitly', c_bid_text_domain);
		}

		// Comment HTML option
		function Option_comment_html() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_comment_html']))
				$options['browserid_comment_html'] = null;
			echo "<input id='browserid_comment_html' name='browserid_options[browserid_comment_html]' type='text' size='100' value='{$options['browserid_comment_html']}' />";
		}

		// Verification server option
		function Option_vserver() {
			$options = get_option('browserid_options');
			if (empty($options['browserid_vserver']))
				$options['browserid_vserver'] = null;
			echo "<input id='browserid_vserver' name='browserid_options[browserid_vserver]' type='text' size='100' value='{$options['browserid_vserver']}' />";
			echo '<br />' . __('Default https://verifier.login.persona.org/verify', c_bid_text_domain);
		}

		// No valid until option
		function Option_novalid() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_novalid']) && $options['browserid_novalid'] ? " checked='checked'" : '');
			echo "<input id='browserid_novalid' name='browserid_options[browserid_novalid]' type='checkbox'" . $chk. "/>";
			echo '<strong>' . __('Security risk!', c_bid_text_domain) . '</strong>';
		}

		// No SSL verify option
		function Option_noverify() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_noverify']) && $options['browserid_noverify'] ? " checked='checked'" : '');
			echo "<input id='browserid_noverify' name='browserid_options[browserid_noverify]' type='checkbox'" . $chk. "/>";
			echo '<strong>' . __('Security risk!', c_bid_text_domain) . '</strong>';
		}

		// Debug option
		function Option_debug() {
			$options = get_option('browserid_options');
			$chk = (isset($options['browserid_debug']) && $options['browserid_debug'] ? " checked='checked'" : '');
			echo "<input id='browserid_debug' name='browserid_options[browserid_debug]' type='checkbox'" . $chk. "/>";
			echo '<strong>' . __('Security risk!', c_bid_text_domain) . '</strong>';
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
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				</form>
			</div>
<?php
			if ($this->debug) {
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

				echo '<p><strong>PHP audience</strong>: ' . $_SERVER['HTTP_HOST'] . '</p>';
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
		echo M66BrowserID::Get_loginout_html();
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

// Check pre-requisites
M66BrowserID::Check_prerequisites();

// Start plugin
global $m66browserid;
if (empty($m66browserid)) {
	$m66browserid = new M66BrowserID();
	register_activation_hook(__FILE__, array(&$m66browserid, 'Activate'));
}

// Template tag "mozilla_persona"
if (!function_exists('mozilla_persona')) {
	function mozilla_persona() {
		echo M66BrowserID::Get_loginout_html();
	}
}

// Template tag "browserid_loginout"
if (!function_exists('browserid_loginout')) {
	function browserid_loginout() {
		echo M66BrowserID::Get_loginout_html();
	}
}

?>
