<?php
/*
Template Name: Comunidad
*/
get_header(); ?>

	<div id="contenido">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post portada-individual" id="post-<?php the_ID(); ?>">
		<h2 class="post-title"><?php the_title(); ?></h2>
			<div class="texto-portada-individual">
				<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
		</div>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</div>

<?php get_footer(); ?>
