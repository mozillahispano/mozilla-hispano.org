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

if (!class_exists('MozillaPersonaWidget')) {
	class MozillaPersonaWidget{
		public function __construct() {
		}

		public function Init() {
			add_action('widgets_init',
					create_function('', 'return register_widget("MozillaPersonaLoginWidget");'));
		}
	}

	class MozillaPersonaLoginWidget extends WP_Widget {
		function __construct() {
			$widget_ops = array(
					'classname' => 'persona__widget',
					'description' => __('Mozilla Persona login button',
							c_bid_text_domain)
					);
			$this->WP_Widget('MozillaPersonaLoginWidget', 'Mozilla Persona',
							$widget_ops);
		}

		// Widget contents
		function widget($args, $instance) {
			global $persona_plugin;
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			echo $before_widget;
			if (!empty($title))
				echo $before_title . $title . $after_title;

			echo "<ul><li class='persona__widget-button-container'>" . $persona_plugin->Get_loginout_html() . "</li></ul>";
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
}

?>
