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

if (!class_exists('MozillaPersonaOptions')) {
	class MozillaPersonaOption {
		private $page;
		private $section;
		private $name;
		private $description;
		private $title;
		private $default_value = '';
		private $beta = false;
		private $risky = false;

        	protected $plugin_options_key;
        	protected $general_settings_key;
        	protected $advanced_settings_key;

		public function __construct($options) {
            		$this->plugin_options_key = 'browserid_options';
            		$this->general_settings_key = c_bid_general_options;
            		$this->advanced_settings_key = c_bid_advanced_options;
            
			$this->name = $options['name'];
			$this->title = $options['title'];

			if (isset($options['default_value']))
				$this->default_value = $options['default_value'];

			if (isset($options['description']))
				$this->description = $options['description'];

			if (isset($options['class']))
				$this->class = $options['class'];

			if (isset($options['beta']))
				$this->beta = $options['beta'];

			if (isset($options['risky']))
				$this->risky = $options['risky'];
		}

		public function Register() {
			add_settings_field($this->Get_name(), $this->Get_title(),
					array($this, 'Print_option'),
							$this->Get_page(), $this->Get_section());

			add_filter('persona_validation-' . $this->Get_name(),
					array($this, 'Validate'), 10, 2);
		}


		public function Set_page($page) {
			$this->page = $page;
		}

		public function Get_page() {
			return $this->page;
		}

		public function Set_section($section) {
			$this->section = $section;
		}

		public function Get_section() {
			return $this->section;
		}

		public function Get_name() {
			return $this->name;
		}

		public function Get_title() {
			return $this->title;
		}

		public function Get_description() {
			return $this->description;
		}

		public function Get_value() {
			$option = get_option($this->page);

			if (isset($option[$this->name])
					&& !empty($option[$this->name])) {
				return $option[$this->name];
			}

			return $this->default_value;
		}

		public function Get_default_value() {
			return $this->default_value;
		}

		public function Validate($value, $options) {
			return $value;
		}

		public function Validation_error($message) {
			add_settings_error($this->name, $this->name, $message, 'error');
			return '';
		}

		public function Print_option() {
			$this->Print_option_input();

			if ($this->beta) {
				echo '<strong>' . __('Beta!', c_bid_text_domain) . '</strong>';
			}

			if ($this->risky) {
				echo '<strong>' . __('Security risk!', c_bid_text_domain) . '</strong>';
			}

			$this->Print_extra_html();

			$description = $this->Get_description();
			if ($description) {
				echo '<br />' . $description;
			}
		}

		protected function Print_option_input() {
			wp_die('Print_option_input must be overridden for '
						. $this->Get_name());
		}

		protected function Print_extra_html() {
			// do nothing by default
		}

		public function Get_passed_option($options, $option_name) {
			return isset($options[$option_name]) ? $options[$option_name] : '';
		}

		public function Get_name_attribute() {
			return $this->Get_page() . '[' . $this->Get_name(). ']';
		}

		/**
		* Build a string of HTML
		* @element_name - name of the element
		* @attributes - list of attributes to add to the element.
		* @attributes.html - 'special' attribute used to specify
		*		the element's innerHtml.
		*/
		private function Build_element_html(
							$element_name, $attributes) {
			$attribute_text = ' ';
			foreach ($attributes as $attribute_name => $attribute_value) {
				if (! empty($attribute_value) && $attribute_name !== "html") {
					$attribute_text .=
						' ' . $attribute_name . '="' . esc_attr($attribute_value) . '"';
				}
			}

			$inner_html = $this->Get_passed_option($attributes, 'html');

			$text_to_print = '<' . $element_name . $attribute_text . '>'
									. $inner_html . '</' . $element_name . '>';
			return $text_to_print;
		}

		public function Print_element(
							$element_name, $attributes) {
			echo $this->Build_element_html(
								$element_name, $attributes);
		}

		protected function Is_https() {
			return (!empty($_SERVER['HTTPS'])
						&& $_SERVER['HTTPS'] !== 'off' ||
							$_SERVER['SERVER_PORT'] == 443);

		}

		protected function Is_absolute_path_url($value) {
			return preg_match('/^\/[^\/]/', $value);
		}

		protected function Is_http_url($value) {
			return preg_match('/^http:\/\//', $value);
		}

		protected function Is_https_url($value) {
			return preg_match('/^https:\/\//', $value);
		}

		protected function Is_http_or_https_url($value) {
			return preg_match('/^http(s)?:\/\//', $value);
		}

		protected function Is_image_data_uri($value) {
			return preg_match('/^data:image\//', $value);
		}

		protected function Get_invalid_text($option_name) {
			return sprintf(__('Invalid %s', c_bid_text_domain),
							$option_name);
		}

		protected function Trim_input($value) {
			return trim($value);
		}

	}

	class MozillaPersonaTextOption extends MozillaPersonaOption {
		private $class;

		public function __construct($options) {
			if (isset($options['class']))
				$this->class = $options['class'];

			parent::__construct($options);
		}

		protected function Print_option_input() {
			$this->Print_element('input', array(
				'id' => $this->Get_name(),
				'type' => 'text',
				'size' => '50',
				'value' =>
					htmlspecialchars($this->Get_value(), ENT_QUOTES),
				'name' => $this->Get_name_attribute(),
				'class' => $this->class
			));

		}

		public function Validate($value, $options) {
			return trim($value);
		}
	}

	class MozillaPersonaFilePickerOption extends MozillaPersonaTextOption {
		private $type;

		public function __construct($options) {
			$this->type = $options['type'];

			parent::__construct($options);
		}

		protected function Print_extra_html() {
			$this->Print_element('button', array(
				'for'			=> $this->Get_name(),
				'data-title'	=> $this->Get_title(),
				'data-type'		=> $this->type,
				'class'			=> 'js-persona__file-picker',
				'html'			=> __('Choose from media', c_bid_text_domain)
			));
		}
	}


	class MozillaPersonaCheckboxOption extends MozillaPersonaOption {
		protected function Print_option_input() {
			$attributes = array(
				'id' => $this->Get_name(),
				'type' => 'checkbox',
				'name' => $this->Get_name_attribute()
			);

			if ($this->Get_value()) {
				$attributes['checked'] = 'checked';
			}

			$this->Print_element('input', $attributes);
		}

		public function Validate($value, $options) {
			if ($this->Is_on_off($value)) return $value;

			$this->Validation_error(
					sprintf(__('%s must be on or off', c_bid_text_domain),
							$this->Get_title()));

			return false;
		}

		private function Is_on_off($value) {
			return $value === "on" || $value === "off";
		}
	}

	class MozillaPersonaSiteName extends MozillaPersonaTextOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_sitename',
				'title' => __('Site name', c_bid_text_domain),
				'default_value' => get_bloginfo('name')
			));
		}
	}

	class MozillaPersonaSiteLogo extends MozillaPersonaFilePickerOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_sitelogo',
				'title' => __('Site logo', c_bid_text_domain),
				'type' => 'image'
			));
		}

		public function Validate($value, $options) {
			$value = trim($value);
			if ($value === '') return '';

			if ($this->Is_absolute_path_url($value)) {
				// absolute paths are only allowed if site is HTTPS
				if ($this->Is_https()) return $value;
				return $this->Validation_error(
						__('sitelogo URLs beginning with / must be served over https', c_bid_text_domain));
			}

			if ($this->Is_http_url($value)) {
				return $this->Validation_error(
						__('sitelogo must begin with https, / or data:image', c_bid_text_domain));
			}

			if ($this->Is_https_url($value)) return esc_url_raw($value, array('https'));
			if ($this->Is_image_data_uri($value)) return $value;

			return $this->Validation_error(
					$this->Get_invalid_text($this->Get_title()));
		}
	}

	class MozillaPersonaBackgroundColor extends MozillaPersonaTextOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_background_color',
				'title' => __('Dialog background color', c_bid_text_domain),
				'class' => 'js-persona__color-picker'
			));
		}

		public function Validate($value, $options) {
			$value = trim($value);
			if ($value === '') return '';

			if (!preg_match('/^#/', $value)) $value = "#" . $value;

			if (preg_match('/^#[0-9a-fA-F]{3}$|^#[0-9a-fA-F]{6}$/', $value))
					return $value;

			return $this->Validation_error(
						$this->Get_invalid_text($this->Get_title()));
		}
	}

	class MozillaPersonaTermsOfService extends MozillaPersonaFilePickerOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_terms_of_service',
				'title' => __('Terms of Service', c_bid_text_domain),
				'type' => 'text'
			));
		}

		public function Validate($value, $options) {
			$value = trim($value);
			if ($value === '') return '';

			$privacy_policy = $this->Get_privacy_policy($options);
			if (! $privacy_policy || $privacy_policy === '') {
				return $this->Validation_error(
							__('Privacy Policy and Terms of Service must be set together', c_bid_text_domain));
			}

			if ($this->Is_http_or_https_url($value)) return esc_url_raw($value, array('http', 'https'));
			if ($this->Is_absolute_path_url($value)) return esc_url_raw($value);

			return $this->Validation_error(
						$this->Get_invalid_text($this->Get_title()));
		}

		private function Get_privacy_policy($options) {
			$option = new MozillaPersonaPrivacyPolicy();
			$option_name = $option->Get_name();
			return isset($options[$option_name]) ? $options[$option_name] : '';
		}
	}

	class MozillaPersonaPrivacyPolicy extends MozillaPersonaFilePickerOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_privacy_policy',
				'title' => __('Privacy Policy', c_bid_text_domain),
				'type' => 'text'
			));
		}

		public function Validate($value, $options) {
			$value = trim($value);
			if ($value === '') return '';

			$terms_of_service = $this->Get_terms_of_service($options);
			if (! $terms_of_service || $terms_of_service === '') {
				return $this->Validation_error(
							__('Privacy Policy and Terms of Service must be set together', c_bid_text_domain));
			}

			if ($this->Is_http_or_https_url($value)) return esc_url_raw($value, array('http', 'https'));
			if ($this->Is_absolute_path_url($value)) return esc_url_raw($value);

			return $this->Validation_error(
						$this->Get_invalid_text($this->Get_title()));
		}

		private function Get_terms_of_service($options) {
			$option = new MozillaPersonaTermsOfService();
			$option_name = $option->Get_name();
			return isset($options[$option_name]) ? $options[$option_name] : '';
		}
	}

	class MozillaPersonaOnlyAuth extends MozillaPersonaCheckboxOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_only_auth',
				'title' => __('Disable non-Persona logins', c_bid_text_domain)
			));
		}
	}

	class MozillaPersonaEnableForComments extends MozillaPersonaCheckboxOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_comments',
				'title' => __('Enable for comments', c_bid_text_domain)
			));
		}
	}

	class MozillaPersonaEnableForBbPress extends MozillaPersonaCheckboxOption {
		public function __construct() {
			parent::__construct(array(
				'beta' => true,
				'name' => 'browserid_bbpress',
				'title' => __('Enable bbPress integration', c_bid_text_domain),
				'description' => __('Enables anonymous posting implicitly', c_bid_text_domain)
			));
		}
	}

	class MozillaPersonaLoginHtml extends MozillaPersonaTextOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_login_html',
				'title' => __('Login button HTML', c_bid_text_domain),
				'default_value' => __('Sign in with your email', c_bid_text_domain)
			));
		}
	}

	class MozillaPersonaLogoutHtml extends MozillaPersonaTextOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_logout_html',
				'title' => __('Logout button HTML', c_bid_text_domain),
				'default_value' => __('Logout', c_bid_text_domain)
			));
		}
	}

	class MozillaPersonaRedirectionUrl extends MozillaPersonaTextOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_login_redir',
				'title' => __('Login redirection URL', c_bid_text_domain),
				'description' => __('Default WordPress dashboard', c_bid_text_domain),
				'default_value' => null
			));
		}
	}

	class MozillaPersonaCommentHtml extends MozillaPersonaTextOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_comment_html',
				'title' => __('Comment button HTML', c_bid_text_domain),
				'default_value' => __('Post comment', c_bid_text_domain)
			));
		}
	}

	class MozillaPersonaButtonColor extends MozillaPersonaOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_button_color',
				'title' => __('Button color', c_bid_text_domain),
				'default_value' => 'blue'
			));
		}

		public function Validate($value, $options) {
			$value = trim($value);
			if ($value === '') return $value;

			if ($value === 'blue'
					|| $value === 'dark'
					|| $value === 'orange') {
				return $value;
			}

			$this->Validation_error(
						__('Button color must be either blue, dark or orange', c_bid_text_domain));

			return $this->Get_default_value();
		}

		protected function Print_option_input() {
			echo "<ul>";
			$this->Print_persona_button_selection(
					__('Blue', c_bid_text_domain), 'blue');
			$this->Print_persona_button_selection(
					__('Black', c_bid_text_domain), 'dark');
			$this->Print_persona_button_selection(
					__('Orange', c_bid_text_domain), 'orange');
			echo "</ul>";
		}

		private function Print_persona_button_selection($name, $value) {
			$color = $this->Get_value();
			$chk = ($color == $value ? " checked='checked'" : '');
?>
			<li class='persona-button--select-color'>
				<input id='<?php echo $value; ?>' name='<?php echo $this->Get_name_attribute(); ?>'
					class='persona-button--select-color-radio'
					type='radio' value='<?php echo $value; ?>' <?php echo $chk; ?> />
				<label class='persona-button <?php echo $value; ?>' for='<?php echo $value; ?>'>
					<span class='persona-button__text'><?php echo $name; ?></span>
				</label>
			</li>
<?php
		}

	}

	class MozillaPersonaHttpOrHttpsUrlOption extends MozillaPersonaTextOption {
		public function Validate($value, $options) {
			$value = trim($value);
			if ($value === '') return '';

			if ($this->Is_http_or_https_url($value)) {
				return esc_url_raw($value, array('http', 'https'));
			}

			return $this->Validation_error(
						sprintf(__('%s must be an http or https URL', c_bid_text_domain),
								$this->Get_title()));
		}
	}

	class MozillaPersonaSource extends MozillaPersonaHttpOrHttpsUrlOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_persona_source',
				'default_value' => c_bid_source,
				'description' =>
						__('Default', c_bid_text_domain) . ' ' . c_bid_source,
				'title' => __('Persona source', c_bid_text_domain),
			));
		}
	}

	class MozillaPersonaVerificationServer extends MozillaPersonaHttpOrHttpsUrlOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_vserver',
				'default_value' => c_bid_verifier . '/verify',
				'description' =>
						__('Default', c_bid_text_domain) . ' ' . c_bid_verifier . '/verify',
				'title' => __('Verification server', c_bid_text_domain)
			));
		}

		public function Get_value() {
			$vserver = parent::Get_value();
			if ($vserver) return $vserver;

			$source = $this->Get_persona_source();
			if ($source != c_bid_source)
				$vserver = $source . '/verify';
			else
				$vserver = $this->Get_default_value();

			return $vserver;
		}

		private function Get_persona_source() {
			$persona_source = new MozillaPersonaSource();
			return $persona_source->Get_value();
		}
	}

	class MozillaPersonaDebug extends MozillaPersonaCheckboxOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_debug',
				'title' => __('Debug mode', c_bid_text_domain),
				'risky' => true
			));
		}

		public function Print_debug_info() {
			$options = get_option($this->general_settings_key);
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

			/*echo '<p><strong>PHP audience</strong>:
			' . htmlentities($this->audience) . '</p>';*/
			echo '<script type="text/javascript">';
			echo 'document.write("<p><strong>JS audience</strong>: " + window.location.hostname + "</p>");';
			echo '</script>';

			echo '<br /><pre>Options=' . htmlentities(print_r($options, true)) . '</pre>';
			echo '<br /><pre>BID request=' . htmlentities(print_r($request, true)) . '</pre>';
			echo '<br /><pre>BID response=' . htmlentities(print_r($response, true)) . '</pre>';
			echo '<br /><pre>PHP request=' . htmlentities(print_r($_REQUEST, true)) . '</pre>';
			echo '<br /><pre>PHP server=' . htmlentities(print_r($_SERVER, true)) . '</pre>';
		}
	}

	class MozillaPersonaUseUncompressedResources
			extends MozillaPersonaCheckboxOption {
		public function __construct() {
			parent::__construct(array(
				'name' => 'browserid_use_uncompressed_resources',
				'title' => __('Use uncompressed resources', c_bid_text_domain),
				'default_value' => false
			));
		}
	}


	class MozillaPersonaOptions {

		// fields is a dictionary of fields, the key to each value
		//		is the field's name as stored in the database.
		private $fields = array();

		public function  __construct() {
            		$this->plugin_options_key = 'browserid_options';
            		$this->general_settings_key = c_bid_general_options;
            		$this->advanced_settings_key = c_bid_advanced_options;

			$fields = array();
		}

		// The general approach is to register each setting each time a page is
		// loaded. When a setting is registered, it's configuration is stored
		// into the settings dictionary.
		public function Init() {
			$this->Update_options_format();
			$this->Load_settings();

			$this->Register_general_fields();
			$this->Register_advanced_fields();

			if (is_admin()) {
				add_action('admin_init', array(&$this, 'Register_general_tab'));
				add_action('admin_init', array(&$this, 'Register_advanced_tab'));
				add_action('admin_init', array(&$this, 'Register_all_fields'));

				add_action('admin_menu',
						array(&$this, 'Add_persona_to_settings_list_action'));
			}
		}

		public function Add_persona_to_settings_list_action() {
			if (function_exists('add_options_page'))
				add_options_page(
					__('Mozilla Persona', c_bid_text_domain) . ' ' . __('Administration', c_bid_text_domain),
					__('Mozilla Persona', c_bid_text_domain),
					'manage_options',
					$this->plugin_options_key,
					array(&$this, 'Render_admin_page'));
		}

		private function Update_options_format() {
			$old_options = get_option(c_bid_old_options);

			$new_general_options = get_option( $this->general_settings_key );
			$should_update_options_format = ! empty($old_options) &&
													empty($new_general_options);

			if ($should_update_options_format) {
				// just copy the old options into each of the new options.
				$new_general_options = array_merge( array(), $old_options );
				update_option( $this->general_settings_key, $new_general_options );

				$new_advanced_options = array_merge( array(), $old_options );
				update_option( $this->advanced_settings_key, $new_advanced_options );

				delete_option(c_bid_old_options);
			}
		}

		private function Load_settings() {
			$this->general_settings = (array) get_option( $this->general_settings_key );
			$this->advanced_settings = (array) get_option( $this->advanced_settings_key );

			// Merge with defaults
			$this->general_settings = array_merge( array(
				'general_option' => 'General value'
			), $this->general_settings );

			$this->advanced_settings = array_merge( array(
				'advanced_option' => 'Advanced value'
			), $this->advanced_settings );
		}

		public function Register_all_fields() {
			foreach ($this->fields as $field_name => $field) {
				$field->Register();
			}
		}

		public function Register_general_tab() {
			$this->plugin_settings_tabs[$this->general_settings_key] =
														__('General', c_bid_text_domain);

			register_setting($this->general_settings_key,
					$this->general_settings_key, array(&$this, 'Validate_input'));

			add_settings_section('section_general',
					__('General Plugin Settings', c_bid_text_domain),
					array(&$this, 'General_settings_description'), $this->general_settings_key);
		}

		private function Register_general_fields() {
			$this->Add_general_setting(new MozillaPersonaSiteName());
			$this->Add_general_setting(new MozillaPersonaSiteLogo());
			$this->Add_general_setting(new MozillaPersonaBackgroundColor());
			$this->Add_general_setting(new MozillaPersonaTermsOfService());
			$this->Add_general_setting(new MozillaPersonaPrivacyPolicy());

			$this->Add_general_setting(new MozillaPersonaButtonColor());

			$this->Add_general_setting(new MozillaPersonaLoginHtml());
			$this->Add_general_setting(new MozillaPersonaLogoutHtml());
			$this->Add_general_setting(new MozillaPersonaRedirectionUrl());

			$this->Add_general_setting(new MozillaPersonaOnlyAuth());

			$this->Add_general_setting(new MozillaPersonaEnableForComments());
			$this->Add_general_setting(new MozillaPersonaCommentHtml());

			$this->Add_general_setting(new MozillaPersonaEnableForBbPress());
		}

		public function General_settings_description() {
			// Nothing to print here!
		}

		private function Add_general_setting($setting) {
			$setting->Set_page($this->general_settings_key);
			$setting->Set_section('section_general');
			$this->fields[$setting->Get_name()] = $setting;
		}

		public function Register_advanced_tab() {
			$this->plugin_settings_tabs[$this->advanced_settings_key] =
														__('Advanced', c_bid_text_domain);

			register_setting($this->advanced_settings_key,
					$this->advanced_settings_key, array(&$this, 'Validate_input'));

			add_settings_section('section_advanced',
					__('Advanced Plugin Settings', c_bid_text_domain),
					array(&$this, 'Advanced_settings_description'), $this->advanced_settings_key);
		}

		private function Register_advanced_fields() {
			$this->Add_advanced_setting(new MozillaPersonaSource());
			$this->Add_advanced_setting(new MozillaPersonaVerificationServer());
			$this->Add_advanced_setting(new MozillaPersonaUseUncompressedResources());
			$this->Add_advanced_setting(new MozillaPersonaDebug());
		}

		private function Add_advanced_setting($setting) {
			$setting->Set_page($this->advanced_settings_key);
			$setting->Set_section('section_advanced');
			$this->fields[$setting->Get_name()] = $setting;
		}

		public function Advanced_settings_description() {
			echo '<p class="persona__warning persona__warning-heading">';
			echo __('Changing these options can cause you to be locked out of your site!',
						c_bid_text_domain);
			echo '</p>';
		}

		public function Validate_input($input) {
			foreach( $input as $key => $value ) {
				$input[$key] = apply_filters( 'persona_validation-' . $key,
									$value, $input );
			}

			return $input;
		}


		public function Deactivate() {
			if (get_option($this->general_settings_key)) delete_option($this->general_settings_key);
			if (get_option($this->advanced_settings_key)) delete_option($this->advanced_settings_key);
		}

		public function Render_admin_page() {
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
			?>
			<div class="wrap">
				<?php screen_icon(); ?>
				<h2><?php _e('Mozilla Persona', c_bid_text_domain); ?></h2>
				<?php $this->plugin_options_tabs(); ?>
				<form method="post" action="options.php">
					<?php wp_nonce_field( 'update-options' ); ?>
					<?php settings_fields($tab); ?>
					<?php do_settings_sections($tab); ?>
					<?php submit_button(); ?>
				</form>
			</div>
<?php
			if ($this->Is_debug() && $tab === $this->advanced_settings_key) {
				$debug = new MozillaPersonaDebug();
				$debug->Print_debug_info();
			}
			else {
				delete_option(c_bid_option_request);
				delete_option(c_bid_option_response);
			}
		}

		public function plugin_options_tabs() {
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;

			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}


		// These are public accessor functions to get option values.
		public function Get_sitename() {
			return $this->Get_field_value('browserid_sitename');
		}

		public function Get_sitelogo() {
			return $this->Get_field_value('browserid_sitelogo');
		}

		public function Get_background_color() {
			return $this->Get_field_value('browserid_background_color');
		}

		public function Get_terms_of_service() {
			return $this->Get_field_value('browserid_terms_of_service');
		}

		public function Get_privacy_policy() {
			return $this->Get_field_value('browserid_privacy_policy');
		}

		public function Get_login_html() {
			return $this->Get_field_value('browserid_login_html');
		}

		public function Get_logout_html() {
			return $this->Get_field_value('browserid_logout_html');
		}

		public function Get_login_redir() {
			return $this->Get_field_value('browserid_login_redir');
		}

		public function Is_comments() {
			return $this->Get_field_value('browserid_comments');
		}

		public function Get_comment_html() {
			return $this->Get_field_value('browserid_comment_html');
		}

		public function Is_bbpress() {
			return $this->Get_field_value('browserid_bbpress');
		}

		public function Get_persona_source() {
			return $this->Get_field_value('browserid_persona_source');
		}

		public function Get_vserver() {
			return $this->Get_field_value('browserid_vserver');
		}

		// The audience is a non-settable option
		public function Get_audience() {
			return $_SERVER['HTTP_HOST'];
		}

		public function Is_debug() {
			return $this->Get_field_value('browserid_debug');
		}

		public function Print_browserid_only_auth() {
			$this->Print_checkbox_input('browserid_only_auth');
		}

		public function Is_browserid_only_auth() {
			return $this->Get_field_value('browserid_only_auth');
		}

		public function Get_button_color() {
			return $this->Get_field_value('browserid_button_color', 'blue');
		}

		public function Use_uncompressed_resources() {
			return $this->Get_field_value(
					'browserid_use_uncompressed_resources');
		}


		private function Get_field_value($field_name) {
			if (isset($this->fields[$field_name])) {
				return $this->fields[$field_name]->Get_value();
			}
		}
	}
}
?>
