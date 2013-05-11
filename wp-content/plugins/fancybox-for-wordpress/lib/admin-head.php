<?php

if ( isset($_REQUEST['reset']) && $_REQUEST['reset'] )
	echo '<div id="message" class="updated fade"><p><strong>FancyBox for WordPress settings have been reset.</strong></p></div>';


// Get array with all the options
$settings = get_option( 'mfbfw' );

// Get Version
$version = get_option('mfbfw_active_version');

// Make selects data
$transitionTypeArray = array( 'fade', 'elastic', 'none' );
$overlayArray = array( 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1 );
$msArray = array( 0, 25, 50, 75, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1250, 1500, 1750, 2000 );
$easingArray = array( 'easeInQuad', 'easeOutQuad', 'easeInOutQuad', 'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart', 'easeOutQuart',
	'easeInOutQuart', 'easeInQuint', 'easeOutQuint', 'easeInOutQuint', 'easeInSine', 'easeOutSine', 'easeInOutSine', 'easeInExpo',
	'easeOutExpo', 'easeInOutExpo', 'easeInCirc', 'easeOutCirc', 'easeInOutCirc', 'easeInElastic', 'easeOutElastic', 'easeInOutElastic',
	'easeInBack', 'easeOutBack', 'easeInOutBack', 'easeInBounce', 'easeOutBounce', 'easeInOutBounce' );