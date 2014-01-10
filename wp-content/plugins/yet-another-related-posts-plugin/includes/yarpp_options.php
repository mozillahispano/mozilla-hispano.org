<?php
global $wpdb, $wp_version, $yarpp;

/* Enforce YARPP setup: */
$yarpp->enforce();

/* Check to see that templates are in the right place */
if (!$yarpp->diagnostic_custom_templates()) {

    $template_option = yarpp_get_option('template');
	if ($template_option !== false &&  $template_option !== 'thumbnails') yarpp_set_option('template', false);

	$template_option = yarpp_get_option('rss_template');
	if ($template_option !== false && $template_option !== 'thumbnails') yarpp_set_option('rss_template', false);
}

/**
 * @since 3.3  Move version checking here, in PHP.
 */
if (current_user_can('update_plugins')) {
	$yarpp_version_info = $yarpp->version_info();

    /*
	 * These strings are not localizable, as long as the plugin data on wordpress.org cannot be.
     */
	$slug = 'yet-another-related-posts-plugin';
	$plugin_name = 'Yet Another Related Posts Plugin';
	$file = basename(YARPP_DIR).'/yarpp.php';
	if ($yarpp_version_info['result'] === 'new') {

		/* Make sure the update system is aware of this version. */
		$current = get_site_transient('update_plugins');
		if (!isset($current->response[$file])) {
			delete_site_transient('update_plugins');
			wp_update_plugins();
		}
	
		echo '<div class="updated"><p>';
		$details_url = self_admin_url('plugin-install.php?tab=plugin-information&plugin='.$slug.'&TB_iframe=true&width=600&height=800');
		printf(
            __(
               'There is a new version of %1$s available.'.
               '<a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>'.
               'or <a href="%5$s">update automatically</a>.', 'yarpp'),
            $plugin_name,
            esc_url($details_url),
            esc_attr($plugin_name),
            $yarpp_version_info['current']['version'],
            wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=').$file, 'upgrade-plugin_'.$file)
        );
		echo '</p></div>';

	} else if ($yarpp_version_info['result'] === 'newbeta') {

		echo '<div class="updated"><p>';
		printf(
            __(
                "There is a new beta (%s) of Yet Another Related Posts Plugin. ".
                "You can <a href=\"%s\">download it here</a> at your own risk.", "yarpp"),
            $yarpp_version_info['beta']['version'],
            $yarpp_version_info['beta']['url']
        );
		echo '</p></div>';

	}
}

/* MyIsam message */
if (isset($_POST['myisam_override'])) {
	yarpp_set_option('myisam_override', 1);

	echo(
        "<div class='updated'>"
	    .__("The MyISAM check has been overridden. You may now use the \"consider titles\" and \"consider bodies\" relatedness criteria.",'yarpp')
	    ."</div>"
    );
	
	$yarpp->enable_fulltext(true);
}

$table_type = $yarpp->diagnostic_myisam_posts();

if ($table_type !== true) $yarpp->disable_fulltext();

if (!yarpp_get_option('myisam_override') && $yarpp->diagnostic_fulltext_disabled()) {
	echo(
        "<div class='updated'>".
            sprintf(
                __("YARPP's \"consider titles\" and \"consider bodies\" relatedness criteria require your <code>%s</code>
                    table to use the <a href='http://dev.mysql.com/doc/refman/5.0/en/storage-engines.html'>MyISAM storage engine</a>,
                    but the table seems to be using the <code>%s</code> engine. These two options have been disabled.",'yarpp'),
                $wpdb->posts,
                $table_type
            ).
            "<br />".
            sprintf(
                __("To restore these features, please update your <code>%s</code> table by executing the following SQL
                    directive: <code>ALTER TABLE `%s` ENGINE = MyISAM;</code> . No data will be erased by altering the
                    table's engine, although there are performance implications.",'yarpp'),
                $wpdb->posts,
                $wpdb->posts
            ).
            "<br />".
            sprintf(
                __("If, despite this check, you are sure that <code>%s</code> is using the MyISAM engine, press this magic
                    button:",'yarpp'),
                $wpdb->posts
            ).
            "<br />".
            "<form method='post'>".
                "<input type='submit' class='button' name='myisam_override' value='".__("Trust me. Let me use MyISAM features.",'yarpp')."'/>".
            "</form>"
	    ."</div>"
    );
}

if(!$yarpp->enabled() && !$yarpp->activate()) {
	echo '<div class="updated">'.__('The YARPP database has an error which could not be fixed.','yarpp').'</div>';
}

/* This is not a yarpp pluging update, it is an yarpp uption update */
if (isset($_POST['update_yarpp'])) {
	$new_options = array();
	foreach ($yarpp->default_options as $option => $default) {
		if ( is_bool($default) )
			$new_options[$option] = isset($_POST[$option]);
		// @todo: do we really want to stripslashes here anymore?
		if ( (is_string($default) || is_int($default)) &&
			 isset($_POST[$option]) && is_string($_POST[$option]) )
			$new_options[$option] = stripslashes($_POST[$option]);
	}

	if ( isset($_POST['weight']) ) {
		$new_options['weight'] = array();
		$new_options['require_tax'] = array();
		foreach ( (array) $_POST['weight'] as $key => $value) {
			if ( $value == 'consider' )
				$new_options['weight'][$key] = 1;
			if ( $value == 'consider_extra' )
				$new_options['weight'][$key] = YARPP_EXTRA_WEIGHT;
		}
		foreach ( (array) $_POST['weight']['tax'] as $tax => $value) {
			if ( $value == 'consider' )
				$new_options['weight']['tax'][$tax] = 1;
			if ( $value == 'consider_extra' )
				$new_options['weight']['tax'][$tax] = YARPP_EXTRA_WEIGHT;
			if ( $value == 'require_one' ) {
				$new_options['weight']['tax'][$tax] = 1;
				$new_options['require_tax'][$tax] = 1;
			}
			if ( $value == 'require_more' ) {
				$new_options['weight']['tax'][$tax] = 1;
				$new_options['require_tax'][$tax] = 2;
			}
		}
	}
	
	if ( isset( $_POST['auto_display_post_types'] ) ) {
		$new_options['auto_display_post_types'] = array_keys( $_POST['auto_display_post_types'] );
	} else {
		$new_options['auto_display_post_types'] = array();
	}

	$new_options['recent'] = isset($_POST['recent_only']) ?
		$_POST['recent_number'] . ' ' . $_POST['recent_units'] : false;

	if ( isset($_POST['exclude']) )
		$new_options['exclude'] = implode(',',array_keys($_POST['exclude']));
	else
		$new_options['exclude'] = '';
	
	$new_options['template'] = $_POST['use_template'] == 'custom' ? $_POST['template_file'] : 
		( $_POST['use_template'] == 'thumbnails' ? 'thumbnails' : false );
	$new_options['rss_template'] = $_POST['rss_use_template'] == 'custom' ? $_POST['rss_template_file'] : 
		( $_POST['rss_use_template'] == 'thumbnails' ? 'thumbnails' : false );
	
	$new_options = apply_filters( 'yarpp_settings_save', $new_options );
	yarpp_set_option($new_options);

	echo '<div class="updated fade"><p>'.__('Options saved!','yarpp').'</p></div>';
}

wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('yarpp_display_demo', 'yarpp_display_demo-nonce', false);
wp_nonce_field('yarpp_display_exclude_terms', 'yarpp_display_exclude_terms-nonce', false);
wp_nonce_field('yarpp_optin_data', 'yarpp_optin_data-nonce', false);
wp_nonce_field('yarpp_set_display_code', 'yarpp_set_display_code-nonce', false);

if (!count($yarpp->admin->get_templates()) && $yarpp->admin->can_copy_templates()) {
    wp_nonce_field('yarpp_copy_templates', 'yarpp_copy_templates-nonce', false);
}

$switch = (isset($_GET['go']) && $_GET['go'] === 'pro') ? true : false;

?>

<div class="wrap">
    <h2>
        <?php _e('Yet Another Related Posts Plugin Options','yarpp');?>
        <small>
            <?php echo apply_filters('yarpp_version_html', esc_html(get_option('yarpp_version'))) ?>
        </small>
    </h2>

    <div id="yarpp_switch_container">

        <ul id="yarpp_switch_tabs">
            <li class="<?php echo (($switch) ? 'disabled': null)?>">
                <a href="options-general.php?page=yarpp">YARPP Basic</a>
            </li>
            <li class="<?php echo (($switch) ? null : 'disabled')?>">
                <a href="options-general.php?page=yarpp&go=pro">YARPP Pro</a>
            </li>
        </ul>

        <?php if ($switch): ?>

            <div class="yarpp_switch_content">
                <h1>YARPP Pro is now available!</h1>
                <p>
                    Access more powerful features with <em>YARPP Pro</em>! Try it today for <strong>FREE</strong>, switch back at any
                    time with no lost custom settings. <a href="http://www.yarpp.com" target="_blank">Find out more.</a>
                </p>
                <ul>
                    <li>
                        Make money by displaying sponsored ads.
                    </li>
                    <li>
                        Easily customize thumbnail display.
                    </li>
                    <li>
                        Pull related content from multiple domains.
                    </li>
                    <li>
                        Get detailed traffic reports.
                    </li>
                    <li>
                        No lost settings. Trying <em>YARPP Pro</em> will not delete your Basic settings. Switch back any time.
                    </li>
                    <li>
                        You at decide how much sponsored ad content to display, from 25% to 100% of your related content.
                        When visitors click, you get paid.<br/>
                        <em>(Note: Ad content will be loaded from an external site and usage data will be sent back to YARPP servers.)</em>
                    </li>
                    <li>
                        <em>YARPP Pro</em> does not support non-English ad content at this time so non-English sites will not
                        display sponsored ads.
                    </li>
                    <li>
                        <em>YARPP Pro</em> does not support custom post types at this time.
                    </li>
                    <li>
                        Some <em>YARPP Basic</em> features may not yet be available in <em>YARPP Pro</em>.
                    </li>
                </ul>
                <p>
                    <a href="<?php echo plugins_url('includes/', dirname(__FILE__)).'yarpp_switch.php' ?>" id="yarpp_switch_button" data-go="pro" class="button">
                        Enable YARPP Pro for FREE!
                    </a>
                    &nbsp;&nbsp
                    <a href="options-general.php?page=yarpp" id="yarpp_switch_cancel"  class="button">No, thanks</a>
                </p>
            </div>

        <?php else: ?>

            <div class="yarpp_switch_content">
                <p>
                    The settings below allow you to configure the basic version of Yet Another Related Post Plugin (YARPP).
                    Click on the "YARPP Pro" tab for enhanced functionality: Make money by displaying sponsored ads,
                    further customize thumbnail display, pull related content from multiple domains, and get detailed
                    reporting. <a href="http://yarpp.com" target="_blank">Learn more.</a>
                </p>
            </div>
    </div>

    <form method="post">

        <div id="poststuff" class="metabox-holder has-right-sidebar">

            <?php if (!$yarpp->get_option('rss_display')): ?>
                <style>
                    .rss_displayed {
                        display: none;
                    }
                </style>
            <?php endif ?>

            <!-- Side column -->
            <div class="inner-sidebar" id="side-info-column">
                <?php do_meta_boxes('settings_page_yarpp', 'side', array()) ?>
            </div>

            <!-- Main Content -->
            <div id="post-body-content">
                <?php do_meta_boxes('settings_page_yarpp', 'normal', array()) ?>
            </div>

            <script language="javascript">
                var spinner = '<?php echo esc_url(admin_url('images/wpspin_light.gif')) ?>',
                    loading = '<img class="loading" src="'+spinner+'" alt="loading..."/>';
            </script>

            <div>
                <input type="submit" class='button-primary' name="update_yarpp" value="<?php _e( 'Save Changes' )?>" />
            </div>

        </div><!--#poststuff-->

    </form>

    <?php endif ?>

</div>