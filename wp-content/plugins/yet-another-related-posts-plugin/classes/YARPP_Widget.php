<?php
/*
 * Vaguely based on code by MK Safi
 * http://msafi.com/fix-yet-another-related-posts-plugin-yarpp-widget-and-add-it-to-the-sidebar/
 */
class YARPP_Widget extends WP_Widget {

	public function __construct() {
		parent::WP_Widget(false, $name = __('Related Posts (YARPP)','yarpp'));
	}

	public function widget($args, $instance) {
		global $yarpp;

		if (!is_singular()) return;

		extract($args);

		/* Compatibility with pre-3.5 settings: */
		if (isset($instance['use_template'])) {
			$instance['template'] = ($instance['use_template']) ? ($instance['template_file']) : false;
        }

		if ($yarpp->get_option('cross_relate')){
			$instance['post_type'] = $yarpp->get_post_types();
        } else if (in_array(get_post_type(), $yarpp->get_post_types())) {
			$instance['post_type'] = array(get_post_type());
        } else {
			$instance['post_type'] = array('post');
        }

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if ( !$instance['template'] ) {
			echo $before_title;
			echo $title;
			echo $after_title;
		}

		$instance['domain'] = 'widget';
		$yarpp->display_related(null, $instance, true);
		echo $after_widget;
	}

	public function update($new_instance, $old_instance) {
		if ( $new_instance['use_template'] == 'builtin' )
			$template = false;
		if ( $new_instance['use_template'] == 'thumbnails' )
			$template = 'thumbnails';
		if ( $new_instance['use_template'] == 'custom' )
			$template = $new_instance['template_file'];

		$instance = array(
			'promote_yarpp' => isset($new_instance['promote_yarpp']),
			'template' => $template
		);

		$choice = false === $instance['template'] ? 'builtin' :
			( $instance['template'] == 'thumbnails' ? 'thumbnails' : 'custom' );

		if ((bool) $instance['template'] ) // don't save the title change.
			$instance['title'] = $old_instance['title'];
		else // save the title change:
			$instance['title'] = $new_instance['title'];

		if ((bool) $instance['thumbnails_heading'] ) // don't save the title change.
			$instance['thumbnails_heading'] = $old_instance['thumbnails_heading'];
		else // save the title change:
			$instance['thumbnails_heading'] = $new_instance['thumbnails_heading'];
		
		return $instance;
	}

	public function form($instance) {
		global $yarpp;
	
		$instance = wp_parse_args(
            $instance,
            array(
                'title'                 => __('Related Posts (YARPP)','yarpp'),
                'thumbnails_heading'    => $yarpp->get_option('thumbnails_heading'),
                'template'              => false,
                'promote_yarpp'         => false
            )
        );
	
		// compatibility with pre-3.5 settings:
		if (isset($instance['use_template'])) {
			$instance['template'] = $instance['template_file'];
        }
	
		$choice = ($instance['template'] === false)
            ? 'builtin'
            : (($instance['template'] === 'thumbnails') ? 'thumbnails' : 'custom');

		// if there are YARPP templates installed...
		$templates = $yarpp->get_templates();

		if (!$yarpp->diagnostic_custom_templates() && $choice === 'custom') $choice = 'builtin';
		
		?>

		<p class='yarpp-widget-type-control'>
			<label style="padding-right: 10px; display: inline-block;" for="<?php echo $this->get_field_id('use_template_builtin'); ?>">
                <input id="<?php echo $this->get_field_id('use_template_builtin'); ?>" name="<?php echo $this->get_field_name('use_template'); ?>" type="radio" value="builtin" <?php checked($choice === 'builtin' ) ?>/>
                <?php _e("List",'yarpp'); ?>
            </label>
		    <br/>
			<label style="padding-right: 10px; display: inline-block;" for="<?php echo $this->get_field_id('use_template_thumbnails'); ?>">
                <input id="<?php echo $this->get_field_id('use_template_thumbnails'); ?>" name="<?php echo $this->get_field_name('use_template'); ?>" type="radio" value="thumbnails" <?php checked($choice === 'thumbnails') ?>/>
                <?php _e("Thumbnails", 'yarpp'); ?>
            </label>
		    <br/>
			<label style="padding-right: 10px; display: inline-block;" for="<?php echo $this->get_field_id('use_template_custom'); ?>">
                <input id="<?php echo $this->get_field_id('use_template_custom'); ?>" name="<?php echo $this->get_field_name('use_template'); ?>" type="radio" value="custom" <?php checked($choice === 'custom'); disabled(!count($templates)); ?>/>
                <?php _e("Custom", 'yarpp'); ?>
            </label>
		</p>

		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>"/>
            </label>
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('thumbnails_heading'); ?>">
                <?php _e('Heading:', 'yarpp'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('thumbnails_heading'); ?>" name="<?php echo $this->get_field_name('thumbnails_heading'); ?>" type="text" value="<?php echo esc_attr($instance['thumbnails_heading']); ?>"/>
            </label>
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('template_file'); ?>">
                <?php _e("Template file:",'yarpp');?>
            </label>
            <select name="<?php echo $this->get_field_name('template_file'); ?>" id="<?php echo $this->get_field_id('template_file'); ?>">
			<?php foreach ($templates as $template): ?>
			    <option value='<?php echo esc_attr($template['basename']); ?>' <?php selected($template['basename'], $instance['template']);?>>
                    <?php echo esc_html($template['name']); ?>
                </option>
			<?php endforeach; ?>
		</select>
        <p>

		<script type="text/javascript">
            jQuery(function($) {
                function ensureTemplateChoice(e) {
                    if (typeof e === 'object' && 'type' in e) {
                        e.stopImmediatePropagation();
                    }

                    var this_form = $(this).closest('form'),
                        widget_id = this_form.find('.widget-id').val();

                    // if this widget is just in staging:
                    if (/__i__$/.test(widget_id)) return;

                    var builtin     = !! $('#widget-'+widget_id+'-use_template_builtin').prop('checked'),
                        thumbnails  = !! $('#widget-'+widget_id+'-use_template_thumbnails').prop('checked'),
                        custom      = !! $('#widget-'+widget_id+'-use_template_custom').prop('checked');

                    $('#widget-' + widget_id + '-title').closest('p').toggle(builtin);
                    $('#widget-' + widget_id + '-thumbnails_heading').closest('p').toggle(thumbnails);
                    $('#widget-' + widget_id + '-template_file').closest('p').toggle(custom);

                    //console.log(widget_id, custom, builtin);
                }

                $('#wpbody').on('change', '.yarpp-widget-type-control input', ensureTemplateChoice);
                $('.yarpp-widget-type-control').each(ensureTemplateChoice);

            });
		</script>

		<p>
            <input class="checkbox" id="<?php echo $this->get_field_id('promote_yarpp'); ?>" name="<?php echo $this->get_field_name('promote_yarpp'); ?>" type="checkbox" <?php checked($instance['promote_yarpp']) ?> />
            <label for="<?php echo $this->get_field_id('promote_yarpp'); ?>">
                <?php _e("Help promote Yet Another Related Posts Plugin?",'yarpp'); ?>
            </label>
        </p>
		<?php
	}
}

/**
 * @since 2.0 Add as a widget
 */
function yarpp_widget_init() {
    register_widget('YARPP_Widget');
}

add_action('widgets_init', 'yarpp_widget_init');