<?php
 
	include("../wp-content/themes/mozillahispano2/cabecera-pie.php");

	require ($_SERVER['DOCUMENT_ROOT'].'/eventos/twitter/multiTwitterOU.php');
	$twitter = new multiTwitterOU();
	$twitter->setUserTwitter('mozilla_hispano');
	$twitter->setPaso(5);

	echo "<?xml version='1.0' encoding='UTF-8'?>";
?>

<?=pintaCss()?>

<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/style.css" type="text/css" />
<link rel="stylesheet" media="screen" type="text/css" href="custom/style/planet_mh.css" title="Default" />
	<link rel="alternate" type="application/rss+xml" title="RSS de las fotos" href="http://api.flickr.com/services/feeds/photos_public.gne?tags=mozilla-hispano&amp;lang=es-us&amp;format=rss_200" />
<link rel="alternate" type="application/rss+xml" title="RSS de los twitts" href="http://search.twitter.com/search.atom?q=+%40mozilla_hispano" />
	<!-- JS y CSS para thickbox -->
	<script type="text/javascript" src="../wp-content/themes/mozillahispano2/js/jquery.js"></script>
	<script type="text/javascript" src="../eventos/js/recargaActualizable.js"></script><!-- Tiene que ir la primera -->
	<script type="text/javascript" src="../wp-content/themes/mozillahispano2/js/thickbox.js"></script>
	<link rel="stylesheet" href="../wp-content/themes/mozillahispano2/css/thickbox.css" type="text/css" media="screen" />
	<?=pintaJs()?>
