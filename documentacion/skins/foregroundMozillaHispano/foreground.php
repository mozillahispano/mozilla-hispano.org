<?php

/**
 * Foreground Skin
 *
 * @file
 * @ingroup Skins
 * @author Garrick Van Buren, Jamie Thingelstad, Tom Hutchison
 * @license 2-clause BSD
 */

if( !defined( 'MEDIAWIKI' ) ) {
   die( 'This is a skin to the MediaWiki package and cannot be run standalone.' );
}

$wgExtensionCredits['skin'][] = array(
	'path'		 => __FILE__,
	'name'		 => 'Foreground',
	'url'		 => 'http://foreground.thingelstad.com/',
	'version'	 => '1.2-alpha',
	'author'	 => array(
		'Garrick Van Buren',
		'Jamie Thingelstad',
		'Tom Hutchison',
		'...'
		),
	'descriptionmsg' => 'foreground-desc'
);

$wgValidSkinNames['foreground'] = 'Foreground';

$wgAutoloadClasses['SkinForeground'] = __DIR__ . '/Foreground.skin.php';

$wgMessagesDirs['SkinForeground'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['SkinForeground'] = __DIR__ . '/Foreground.i18n.php';

$wgResourceModules['skins.foreground'] = array(
    'styles'         => array(
        'foregroundMozillaHispano/assets/stylesheets/normalize.css',
        'foregroundMozillaHispano/assets/stylesheets/font-awesome.css',
        'foregroundMozillaHispano/assets/stylesheets/foundation.css',
        'foregroundMozillaHispano/assets/stylesheets/foreground.css',
        'foregroundMozillaHispano/assets/stylesheets/foreground-print.css',
        'foregroundMozillaHispano/assets/stylesheets/jquery.autocomplete.css',
        'foregroundMozillaHispano/assets/stylesheets/responsive-tables.css',
        // Mozilla Hispano added
        '../../wp-content/themes/mozillahispano2/css/comun.css',
        '../../wp-content/themes/mozillahispano2/css/responsive.css',
        'foregroundMozillaHispano/assets/stylesheets/patch.css'
    ),
    'scripts'        => array(
        'foregroundMozillaHispano/assets/scripts/vendor/custom.modernizr.js',
        'foregroundMozillaHispano/assets/scripts/vendor/fastclick.js',
        'foregroundMozillaHispano/assets/scripts/vendor/responsive-tables.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.topbar.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.dropdown.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.section.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.clearing.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.cookie.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.placeholder.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.forms.js',
        'foregroundMozillaHispano/assets/scripts/foundation/foundation.alerts.js',
        'foregroundMozillaHispano/assets/scripts/foreground.js'
    ),
    'remoteBasePath' => &$GLOBALS['wgStylePath'],
    'localBasePath'  => &$GLOBALS['wgStyleDirectory']
);
