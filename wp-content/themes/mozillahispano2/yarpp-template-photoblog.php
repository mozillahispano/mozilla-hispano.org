<?php /*
Example photoblog template for use with Yet Another Photoblog
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>
<h3>Related Photos</h3>
<?php if ($related_query->have_posts()):?>
<ol>
	<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
		<?php if (function_exists('yapb_is_photoblog_post')): if (yapb_is_photoblog_post()):?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php yapb_get_thumbnail(); ?></a></li>
		<?php endif; endif; ?>
	<?php endwhile; ?>
</ol>

<?php else: ?>
<p>No related photos.</p>
<?php endif; ?>
