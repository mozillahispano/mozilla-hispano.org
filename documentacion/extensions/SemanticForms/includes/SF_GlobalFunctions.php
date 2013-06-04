<?php
/**
 * Global functions for Semantic Forms.
 *
 * @author Yaron Koren
 * @file
 * @ingroup SF
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 *  This is a delayed init that makes sure that MediaWiki is set up
 *  properly before we add our stuff.
 */
function sffSetupExtension() {
	// This global variable is needed so that other extensions can hook
	// into it to add their own input types.
	global $sfgFormPrinter;
	$sfgFormPrinter = new StubObject( 'sfgFormPrinter', 'SFFormPrinter' );
}

/**********************************************/
/***** language settings                  *****/
/**********************************************/

/**
 * Initialize a global language object for content language. This
 * must happen early on, even before user language is known, to
 * determine labels for additional namespaces. In contrast, messages
 * can be initialised much later, when they are actually needed.
 */
function sffInitContentLanguage( $langcode ) {
	global $sfgIP, $sfgContLang;

	if ( !empty( $sfgContLang ) ) { return; }

	$cont_lang_class = 'SF_Language' . str_replace( '-', '_', ucfirst( $langcode ) );
	if ( file_exists( $sfgIP . '/languages/' . $cont_lang_class . '.php' ) ) {
		include_once( $sfgIP . '/languages/' . $cont_lang_class . '.php' );
	}

	// fallback if language not supported
	if ( !class_exists( $cont_lang_class ) ) {
		include_once( $sfgIP . '/languages/SF_LanguageEn.php' );
		$cont_lang_class = 'SF_LanguageEn';
	}

	$sfgContLang = new $cont_lang_class();
}
