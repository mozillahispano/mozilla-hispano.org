<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
@include_once("cabecera-pie.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php echo pintaCss(); ?>

<script type="application/x-javascript" src="/wp-content/themes/mozillahispano2/js/install_addon.php?id=<?php the_title(); ?>" ></script>

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/css/mozcamplatam.css" type="text/css" />

<?php
	// Custom CSS and JS for WP pages using custom fields
	$id = $wp_query->post->ID;
	$css_docs = get_post_meta($id, 'custom_css', false);
	$js_docs = get_post_meta($id, 'custom_javascript', false);
	if (!empty($css_docs))
	{
		echo '<!--Custom css for '.get_the_title().'.-->'."\n";
		foreach ($css_docs as $css)
		{
					echo '<link rel="stylesheet" href="'.$css.'" type="text/css" media="screen" />'."\n";
			}
	}
	if (!empty($js_docs))
	{
		echo '<!--Custom javascript for '.get_the_title().'.-->'."\n";
		foreach ($js_docs as $js)
		{
			echo '<script src="'.$js.'" type="text/javascript"></script>'."\n";
		}
	}
?>

<?php echo pintaJs(); ?>

</head>
<body <?php body_class(); ?>>
	<div id="lienzo">
		<div id="tullido">
			<?php echo pintaCabecera(); ?>
			<div id="cuerpo" class="clearfix">
