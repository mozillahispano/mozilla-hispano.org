<?php
/*
Template Name: Labs Release Notes
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php the_title();?> - Notas de la versión</title>
	<style type="text/css">
		body {
			font-size: 90%;
		}

		img {
			border: 0;
		}
	</style>
</head>

<body>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	<?php endwhile; endif; ?>

	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
	<script type="text/javascript">
	_uacct = "UA-2846159-1";
	urchinTracker();
	</script>
</body>
</html>
