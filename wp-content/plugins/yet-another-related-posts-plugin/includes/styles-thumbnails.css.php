<?php
/**
 * Dynamic styles for YARPP's built-in thumbnails template
 * @since 4.0
 */

$height             = (isset($_GET['height'])) ? (int) $_GET['height'] : 120;
$width              = (isset($_GET['width']))  ? (int) $_GET['width']  : 120;
$margin             = 5;
$width_with_margins = ($margin * 2) + $width;
$height_with_text   = $height + 50;
$extra_margin        = 7;

header('Content-Type: text/css');
?>
.yarpp-thumbnails-horizontal .yarpp-thumbnail, .yarpp-thumbnail-default, .yarpp-thumbnail-title {
	display: inline-block;
	*display: inline;
}
.yarpp-thumbnails-horizontal .yarpp-thumbnail {
	border: 1px solid rgba(127,127,127,0.1);
	width: <?php echo $width_with_margins; ?>px;
	height: <?php echo $height_with_text; ?>px;
	margin: <?php echo $margin; ?>px;
	margin-left: 0px;
	vertical-align: top;
}
.yarpp-thumbnail > img, .yarpp-thumbnail-default {
	width: <?php echo $width; ?>px;
	height: <?php echo $height; ?>px;
	margin: <?php echo $margin; ?>px;
}
.yarpp-thumbnails-horizontal .yarpp-thumbnail > img, .yarpp-thumbnails-horizontal .yarpp-thumbnail-default {
	display: block;
}
.yarpp-thumbnails-horizontal .yarpp-thumbnail-title {
	font-size: 1em;
	max-height: 2.8em;
	line-height: 1.4em;
	margin: <?php echo $extra_margin; ?>px;
	margin-top: 0px;
	width: <?php echo $width; ?>px;
	text-decoration: inherit;
	overflow: hidden;
}

.yarpp-thumbnail-default {
	overflow: hidden;
}
.yarpp-thumbnail-default > img {
	min-height: <?php echo $height; ?>px;
	min-width: <?php echo $width; ?>px;
}
