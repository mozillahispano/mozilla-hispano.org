<?php /*
Example template: random
This template gives you some random other post in case there are no related posts
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>
<h3>Related Posts</h3>
<?php if ($related_query->have_posts()):?>
<ol>
	<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
	<li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a><!-- (<?php the_score(); ?>)--></li>
	<?php endwhile; ?>
</ol>

<?php else: 
$related_query->query("orderby=rand&order=asc&limit=1");
$related_query->the_post();?>
<p>No related posts were found, so here's a consolation prize: <a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>.</p>
<?php endif; ?>
