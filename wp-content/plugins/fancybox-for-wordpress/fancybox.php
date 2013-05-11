<?php
/*
Plugin Name: FancyBox for WordPress
Plugin URI: http://plugins.josepardilla.com/fancybox-for-wordpress/
Description: Integrates <a href="http://fancybox.net/">FancyBox</a> by <a href="http://klade.lv/">Janis Skarnelis</a> into WordPress.
Version: 3.0.2
Author: Jos&eacute; Pardilla
Author URI: http://josepardilla.com/

 * FancyBox is Copyright (c) 2008 - 2010 Janis Skarnelis
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html

*/



/**
 * Constants
 */

define( 'FBFW_VERSION', '3.0.2' );
define( 'FBFW_PATH', plugin_dir_path(__FILE__) );
define( 'FBFW_URL', plugin_dir_url(__FILE__) );



/**
 * Store default settings in an array
 */

function mfbfw_defaults() {

	$defaults_array = array(

		// Appearance
		'border'                => '',
		'borderColor'           => '#BBBBBB',
		'showCloseButton'       => 'on',
		'closeHorPos'           => 'right',
		'closeVerPos'           => 'top',
		'paddingColor'          => '#FFFFFF',
		'padding'               => '10',
		'overlayShow'           => 'on',
		'overlayColor'          => '#666666',
		'overlayOpacity'        => '0.3',
		'titleShow'             => 'on',
		'titlePosition'         => 'inside',
		'titleColor'            => '#333333',
		'showNavArrows'         => 'on',

		// Animations
		'zoomOpacity'           => 'on',
		'zoomSpeedIn'           => '500',
		'zoomSpeedOut'          => '500',
		'zoomSpeedChange'       => '300',
		'transitionIn'          => 'fade',
		'transitionOut'         => 'fade',
		'easing'                => '',
		'easingIn'              => 'easeOutBack',
		'easingOut'             => 'easeInBack',
		'easingChange'          => 'easeInOutQuart',

		// Behaviour
		'imageScale'            => 'on',
		'centerOnScroll'        => 'on',
		'hideOnContentClick'    => '',
		'hideOnOverlayClick'    => 'on',
		'enableEscapeButton'    => 'on',
		'cyclic'                => '',
		'mouseWheel'            => '',

		// Gallery Type
		'galleryType'           => 'all',
		'customExpression'      => 'jQuery(thumbnails).addClass("fancybox").attr("rel","fancybox").getTitle();',

		// Other
		'autoDimensions'        => 'on',
		'frameWidth'            => '560',
		'frameHeight'           => '340',
		'loadAtFooter'          => '',
		'callbackEnable'        => '',
		'callbackOnStart'       => 'function() { alert("Start!"); }',
		'callbackOnCancel'      => 'function() { alert("Cancel!"); }',
		'callbackOnComplete'    => 'function() { alert("Complete!"); }',
		'callbackOnCleanup'     => 'function() { alert("CleanUp!"); }',
		'callbackOnClose'       => 'function() { alert("Close!"); }',

		// Troubleshooting
		'nojQuery'              => '',

		// Extra Calls
		'extraCallsEnable'      => '',
		'extraCalls'            => '',

		// Uninstall
		'uninstall'             => ''

	);

	return $defaults_array;
}



/**
 * When plugin is installed, write default settings and update version
 */

function mfbfw_install() {

	$defaults_array = mfbfw_defaults();
	add_option( 'mfbfw', $defaults_array );
	update_option( 'mfbfw_active_version', FBFW_VERSION );

}
register_activation_hook( __FILE__, 'mfbfw_install' );



/**
 * If requested, when plugin is deactivated, remove settings
 */

function mfbfw_uninstall() {
	$settings = get_option( 'mfbfw' );
	if ( isset($settings['uninstall']) && $settings['uninstall'] ) {
		delete_option( 'mfbfw' );
		delete_option( 'mfbfw_active_version' );
	}
}
register_deactivation_hook( __FILE__, 'mfbfw_uninstall' );



/**
 * Here we load FancyBox JS with jQuery and jQuery.easing if necessary
 */

function mfbfw_register_scripts() {

	$settings = get_option( 'mfbfw' );

	// Check if script should be loaded in footer
	if ( isset($settings['loadAtFooter']) && $settings['loadAtFooter'] ) {
		$footer = true;
	} else {
		$footer = false;
	}

	// Check if plugin should not call jQuery script (for troubleshooting only)
	if ( isset($settings['nojQuery']) && $settings['nojQuery'] ) {
		$jquery = false;
	} else {
		$jquery = array('jquery');
	}

	// Register scripts
	wp_register_script('fancybox', FBFW_URL . 'fancybox/jquery.fancybox.js', $jquery, '1.3.4', $footer ); // Main Fancybox script
	wp_register_script('jqueryeasing', FBFW_URL . 'js/jquery.easing.1.3.min.js', false, '1.3', $footer ); // Easing animations script
	wp_register_script('jquerymousewheel', FBFW_URL . 'js/jquery.mousewheel.3.0.4.pack.js', false, '3.0.4', $footer ); // Mouse wheel support script


}
add_action( 'init', 'mfbfw_register_scripts' );

function mfbfw_scripts() {

	$settings = get_option( 'mfbfw' );

	wp_enqueue_script( 'fancybox' ); // Load fancybox

	if ( isset($settings['easing']) && $settings['easing'] ) {
		wp_enqueue_script( 'jqueryeasing' ); // Load easing javascript file if required
	}

	if ( isset($settings['mouseWheel']) && $settings['mouseWheel'] ) {
		wp_enqueue_script( 'jquerymousewheel' ); // Load mouse wheel javascript file if required
	}

}
add_action( 'wp_enqueue_scripts', 'mfbfw_scripts' ); // Load Scripts



/**
 * Link to FancyBox stylesheet and apply some custom styles
 */

function mfbfw_styles() {

	$settings = get_option( 'mfbfw' );
	wp_enqueue_style( 'fancybox', FBFW_URL . 'fancybox/fancybox.css' );

	?>

	<style type="text/css">
		#fancybox-close{<?php echo $settings['closeHorPos']; ?>:-15px;<?php echo $settings['closeVerPos']; ?>:-15px}
		<?php if ( isset($settings['paddingColor']) && $settings['paddingColor'] ) { echo "div#fancybox-content{border-color:" . $settings['paddingColor'] . "}\n"; } ?>
		<?php if ( isset($settings['paddingColor']) && $settings['paddingColor'] && $settings['titlePosition'] == "inside" ) { echo "div#fancybox-title{background-color:" . $settings['paddingColor'] . "}\n"; } ?>
		div#fancybox-outer{background-color:<?php echo $settings['paddingColor']; if ( isset($settings['border']) && $settings['border'] ) { echo "; border:1px solid " . $settings['borderColor']; } echo "}\n"; ?>
		<?php if ( isset($settings['titleColor']) && $settings['titleColor'] && $settings['titlePosition'] == "inside" ) { echo "div#fancybox-title-inside{color:" . $settings['titleColor'] . "}\n"; } ?>
	</style>

	<?php

}
add_action( 'wp_enqueue_scripts', 'mfbfw_styles' );



/**
 * Load FancyBox with the settings set
 */

function mfbfw_init() {

	$settings = get_option( 'mfbfw' );
	$version = get_option( 'mfbfw_active_version' );

	echo "\n<!-- Fancybox for WordPress v" . $version . ' -->'; ?>

<script type="text/javascript">
jQuery(function(){

jQuery.fn.getTitle = function() { // Copy the title of every IMG tag and add it to its parent A so that fancybox can show titles
	var arr = jQuery("a.fancybox");
	jQuery.each(arr, function() {
		var title = jQuery(this).children("img").attr("title");
		jQuery(this).attr('title',title);
	})
}

// Supported file extensions
var thumbnails = jQuery("a:has(img)").not(".nolightbox").filter( function() { return /\.(jpe?g|png|gif|bmp)$/i.test(jQuery(this).attr('href')) });

<?php if ( $settings['galleryType'] == 'post' ) {

		// Gallery type BY POST and we are on post or page (so only one post or page is visible)
		if ( is_single() | is_page() ) {
			echo 'thumbnails.addClass("fancybox").attr("rel","fancybox").getTitle();';
		}

		// Gallery type BY POST, but we are neither on post or page, so we make a different rel attribute on each post
		else {
			echo 'var posts = jQuery(".post");

posts.each(function() {
	jQuery(this).find(thumbnails).addClass("fancybox").attr("rel","fancybox"+posts.index(this)).getTitle()
});';
		}

	}

	// Gallery type ALL
	elseif ( $settings['galleryType'] == 'all' ) {
		echo 'thumbnails.addClass("fancybox").attr("rel","fancybox").getTitle();';
	}

	// Gallery type NONE
	elseif ( $settings['galleryType'] == 'none' ) {
		echo 'thumbnails.addClass("fancybox").getTitle();';
	}

	// Else, gallery type is custom, so we just print the custom expression
	else {
		echo $settings['customExpression'];
	}

	// Now we call fancybox and apply it on any link with a rel atribute that starts with "fancybox", with the options set on the admin panel
	?>

jQuery("a.fancybox").fancybox({
	'cyclic': <?php if ( isset($settings['cyclic']) && $settings['cyclic'] ) { echo "true"; } else { echo "false"; } ?>,
	'autoScale': <?php if ( isset($settings['imageScale']) && $settings['imageScale'] ) { echo "true"; } else { echo "false"; } ?>,
	'padding': <?php echo $settings['padding']; ?>,
	'opacity': <?php if ( isset($settings['zoomOpacity']) && $settings['zoomOpacity'] ) { echo "true"; } else { echo "false"; } ?>,
	'speedIn': <?php echo $settings['zoomSpeedIn']; ?>,
	'speedOut': <?php echo $settings['zoomSpeedOut']; ?>,
	'changeSpeed': <?php echo $settings['zoomSpeedChange']; ?>,
	'overlayShow': <?php if ( isset($settings['overlayShow']) && $settings['overlayShow'] ) { echo "true"; } else { echo "false"; } ?>,
	'overlayOpacity': <?php echo '"' . $settings['overlayOpacity'] . '"'; ?>,
	'overlayColor': <?php echo '"' . $settings['overlayColor'] . '"'; ?>,
	'titleShow': <?php if ( isset($settings['titleShow']) && $settings['titleShow'] ) { echo "true"; } else { echo "false"; } ?>,
	'titlePosition': '<?php echo $settings['titlePosition']; ?>',
	'enableEscapeButton': <?php if ( isset($settings['enableEscapeButton']) && $settings['enableEscapeButton'] ) { echo "true"; } else { echo "false"; } ?>,
	'showCloseButton': <?php if ( isset($settings['showCloseButton']) && $settings['showCloseButton'] ) { echo "true"; } else { echo "false"; } ?>,
	'showNavArrows': <?php if ( isset($settings['showNavArrows']) && $settings['showNavArrows'] ) { echo "true"; } else { echo "false"; } ?>,
	'hideOnOverlayClick': <?php if ( isset($settings['hideOnOverlayClick']) && $settings['hideOnOverlayClick'] ) { echo "true"; } else { echo "false"; } ?>,
	'hideOnContentClick': <?php if ( isset($settings['hideOnContentClick']) && $settings['hideOnContentClick'] ) { echo "true"; } else { echo "false"; } ?>,
	'width': <?php echo $settings['frameWidth']; ?>,
	'height': <?php echo $settings['frameHeight']; ?>,
	'transitionIn': <?php echo '"' . $settings['transitionIn'] . '"'; ?>,
	'transitionOut': <?php echo '"' . $settings['transitionOut'] . '"'; ?>,
<?php if ( isset($settings['callbackEnable'], $settings['callbackOnStart']) && $settings['callbackEnable'] && $settings['callbackOnStart'] ) echo "\t'onStart': ". $settings['callbackOnStart'] .","."\n"; ?>
<?php if ( isset($settings['callbackEnable'], $settings['callbackOnCancel']) && $settings['callbackEnable'] && $settings['callbackOnCancel'] ) echo "\t'onCancel': ". $settings['callbackOnCancel'] .","."\n"; ?>
<?php if ( isset($settings['callbackEnable'], $settings['callbackOnCleanup']) && $settings['callbackEnable'] && $settings['callbackOnCleanup'] ) echo "\t'onCleanup': ". $settings['callbackOnCleanup'] .","."\n"; ?>
<?php if ( isset($settings['callbackEnable'], $settings['callbackOnComplete']) && $settings['callbackEnable'] && $settings['callbackOnComplete'] ) echo "\t'onComplete': ". $settings['callbackOnComplete'] .","."\n"; ?>
<?php if ( isset($settings['callbackEnable'], $settings['callbackOnClose']) && $settings['callbackEnable'] && $settings['callbackOnClose'] ) echo "\t'onClosed': ". $settings['callbackOnClose'] .","."\n"; ?>
	'centerOnScroll': <?php if ( isset($settings['centerOnScroll']) && $settings['centerOnScroll'] ) { echo "true"; } else { echo "false"; } ?><?php if ( isset($settings['easing']) && $settings['easing'] ) { ?>,
	'easingIn': <?php echo '"' . $settings['easingIn'] . '"'; ?>,
	'easingOut': <?php echo '"' . $settings['easingOut'] . '"'; ?>,
	'easingChange': <?php echo '"' . $settings['easingChange'] . '"';
} ?>

});

<?php if ( isset($settings['extraCallsEnable']) && $settings['extraCallsEnable'] ) { echo $settings['extraCalls'];  echo "\n"; } ?>

})
</script>
<?php echo "<!-- END Fancybox for WordPress -->\n";
}
add_action( 'wp_head', 'mfbfw_init' );



/**
 * Load text domain
 */

function mfbfw_textdomain() {

	if ( function_exists('load_plugin_textdomain') ) {
		load_plugin_textdomain( 'mfbfw', FBFW_URL . 'languages', 'fancybox-for-wordpress/languages' );
	}

}
add_action( 'init', 'mfbfw_textdomain' );



/**
 * Register Options
 */

function mfbfw_admin_options() {

	$settings = get_option( 'mfbfw' );

	if ( isset($_GET['page']) && $_GET['page'] == 'fancybox-for-wordpress' ) {

		if ( isset($_REQUEST['action']) && 'update' == $_REQUEST['action'] ) {

			$settings = stripslashes_deep( $_POST['mfbfw'] );
			$settings = array_map( 'convert_chars', $settings );

			update_option( 'mfbfw', $settings );
			wp_safe_redirect( add_query_arg('updated', 'true') );
			die;

		} else if ( isset($_REQUEST['action']) && 'reset' == $_REQUEST['action'] ) {

			$defaults_array = mfbfw_defaults(); // Store defaults in an array
			update_option( 'mfbfw', $defaults_array ); // Write defaults to database
			wp_safe_redirect( add_query_arg('reset', 'true') );
			die;

		}
	}

	register_setting( 'mfbfw-options', 'mfbfw' );

}
add_action( 'admin_init', 'mfbfw_admin_options' );



/**
 * Admin options page
 */

function mfbfw_admin_menu() {

	require FBFW_PATH . 'admin.php';

	$mfbfwadmin = add_submenu_page( 'options-general.php', 'Fancybox for WordPress Options', 'Fancybox for WP', 'manage_options', 'fancybox-for-wordpress', 'mfbfw_options_page' );

	add_action( 'admin_print_styles-' . $mfbfwadmin, 'mfbfw_admin_styles' );
	add_action( 'admin_print_scripts-' . $mfbfwadmin, 'mfbfw_admin_scripts' );

}
add_action('admin_menu', 'mfbfw_admin_menu');



/**
 * Load Admin CSS & JS (called in mfbfw_admin_menu())
 */

function mfbfw_admin_styles() {
	wp_enqueue_style( 'fancybox-admin', FBFW_URL . 'css/fancybox-admin.css' ); // Load custom CSS for Admin Page
	wp_enqueue_style( 'jquery-ui', FBFW_URL . 'css/jquery-ui.css' ); // Load jQuery UI Tabs CSS for Admin Page
}

function mfbfw_admin_scripts() {
	wp_enqueue_script( 'jquery-ui-tabs', array('jquery-ui-core') ); // Load jQuery UI Tabs JS for Admin Page
	wp_enqueue_script( 'fancybox-admin', FBFW_URL . 'js/admin.js', array('jquery') ); // Load specific JS for Admin Page
}



/**
 * Settings Button on Plugins Panel
 */

function mfbfw_plugin_action_links($links, $file) {

	static $this_plugin;
	if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );

	if ( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=fancybox-for-wordpress">' . __( 'Settings', 'mfbfw' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;

}
add_filter( 'plugin_action_links', 'mfbfw_plugin_action_links', 10, 2 );