/*
 * JavaScript for WikiEditor AddMediaWizard integration
 */

$( document ).ready( function() {
	if ( typeof mwAddMediaConfig == 'undefined' ) {
		mwAddMediaConfig = {};
	}
	mwAddMediaConfig['enabled_providers'] = [ 'wiki_commons', 'upload' ];
	// Transclude mwEmbed support
	mediaWiki.loader.load(
		'http://prototype.wikimedia.org/s-2/js/mwEmbed/remotes/mediaWiki.js?&uselang=' +  wgUserLanguage,
		'text/javascript'
	);
};