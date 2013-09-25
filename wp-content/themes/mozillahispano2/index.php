<?php
/**
* @package WordPress
* @subpackage Default_Theme
*/
get_header(); ?>
	<div id="contenido">
		<div class="home" id="content-bar">
			<?php echo do_shortcode('[orbit-slider]') ?>
	</div>

<div id="main-content">
	<?php 
		/* Fix para que funcione la paginacion*/
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		query_posts('posts_per_page=5&paged='.$paged);
		if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class('portada-individual') ?> id="post-<?php the_ID(); ?>">
				<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

				<div class="post-content">
				
					<?php
					// Imagen destacada o post thumbnail del artículo
					if ( has_post_thumbnail() )
					{
						// the current post has a thumbnail
						the_post_thumbnail( array(130,185) );
						} else {
						// the current post lacks a thumbnail
						echo '<img src="/wp-content/themes/mozillahispano/img/post-default.png" alt="Articulo" />';
					}
					?>
					<div class="texto-portada-individual">
						<p class="dia-publicacion"><?php the_time('j F, Y') ?> <!--<?php the_time('G:i');?> :: <?php the_author_posts_link(); ?>--></p>
						<?php the_excerpt(); ?>
						<p><a title="Leer el resto del artículo" href="<?php the_permalink() ?>">Leer más...</a></p>
					</div>

				</div>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Artículos antiguos') ?></div>
			<div class="alignright"><?php previous_posts_link('Artículos más recientes &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">No encontrado</h2>
		<p class="center">Lo sentimos, pero está buscado algo que no está aquí.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>

</div><!-- #main-content -->

<?php get_sidebar(); ?>

</div><!-- #contenido -->

<?php get_footer(); ?>
