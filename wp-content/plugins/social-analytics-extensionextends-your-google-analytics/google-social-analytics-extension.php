<?php
/**
 * Plugin Name: Google Social Analytics Extension(extends your Google Analytics)
 * Description: Extend your Google Analytics to track social buttons like the Facebook "like" , the Twitter "Tweet".  This plugin only contains additional script for Google Analytics.  So you need embed google analytics by yourself with another way. If your Analytics supports social tracking, please do not install it.
 * Author: Mitsuhiro Inomata
 * Version: 1.0.0
 * Requires at least: 2.8
 * Author URI: http://www.ecoop.net/
 * Plugin URI: http://www.ecoop.net/memo/archives/social-google-analytics-with-wourdpres.html
 * License: GPL
 */
function embed_google_social_analytics(){
?>
<script type="text/javascript"><!--
// Facebook
if(typeof _gaq != 'undefined' && typeof FB !='undefined'){
	// like
	FB.Event.subscribe('edge.create', function(targetUrl) {
	  _gaq.push(['_trackSocial', 'facebook', 'like', targetUrl]);
	});
	// unlike
	FB.Event.subscribe('edge.remove', function(targetUrl) {
	  _gaq.push(['_trackSocial', 'facebook', 'unlike', targetUrl]);
	});
	// send
	FB.Event.subscribe('message.send', function(targetUrl) {
	  _gaq.push(['_trackSocial', 'facebook', 'send', targetUrl]);
	});
}

// Twitter
if(typeof _gaq != 'undefined' && typeof twtter !='undefined'){
	function extractParamFromUri(uri, paramName) {
	  if (!uri) {
	    return;
	  }
	  var uri = uri.split('#')[0];  // Remove anchor.
	  var parts = uri.split('?');  // Check for query params.
	  if (parts.length == 1) {
	    return;
	  }
	  var query = decodeURI(parts[1]);
	 
	  // Find url param.
	  paramName += '=';
	  var params = query.split('&');
	  for (var i = 0, param; param = params[i]; ++i) {
	    if (param.indexOf(paramName) === 0) {
	      return unescape(param.split('=')[1]);
	    }
	  }
	}
	twttr.events.bind('tweet', function(event) {
	  if (event) {
	    var targetUrl;
	    if (event.target && event.target.nodeName == 'IFRAME') {
	      targetUrl = extractParamFromUri(event.target.src, 'url');
	    }
	    _gaq.push(['_trackSocial', 'twitter', 'tweet', targetUrl]);
	  }
	});
}

//--></script>
<?php
}

add_action('wp_footer', 'embed_google_social_analytics', 0);

