<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
@include_once("cabecera-pie.php");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />


<?php echo pintaCss(); ?>

<script type="application/x-javascript" src="/wp-content/themes/mozillahispano2/js/install_addon.php?id=<?php the_title(); ?>" ></script>

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/style.css" type="text/css" media="screen" />

<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/css/responsive.css" type="text/css" media="screen" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

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

<script type="application/x-javascript" src="/wp-content/themes/mozillahispano2/js/labs_functions.js" ></script>
<?php echo pintaJs(); ?>

</head>
<body <?php body_class(); ?>>
	<div id="lienzo">
		<div id="tullido">
			<?php echo pintaCabecera(); ?>
			<div id="cuerpo" class="clearfix">
