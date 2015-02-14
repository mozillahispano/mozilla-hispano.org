<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

	<div id="contenido">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class('portada-individual') ?> id="post-<?php the_ID(); ?>">


			<h2 class="post-title"><?php the_title(); ?></h2>

			<p class="dia-publicacion"><?php the_time('j F, Y') ?> <?php the_time('G:i');?> por <?php the_author_posts_link(); ?></p>

			<p class="autor-categorias"><span><?php the_tags('', ' ', ''); ?></span></p>

			<div class="texto-portada-individual articulo-single">
				<?php the_content('Leer el resto del artículo &raquo;'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php //the_tags( '<p>Tags: ', ', ', '</p>'); ?>
				<?php
				/* Nota para los artículos que importamos extenamente */
				if (function_exists("is_syndicated")) {
					if (is_syndicated() and (get_the_author() !== get_syndication_source())):
					    echo '<p><cite class="feed">Publicado por '; the_author(); echo ' en <a href="';  the_syndication_source_link(); echo '">';
					    the_syndication_source();
					    echo '</a></cite></p>';
					endif;
				}
				?>
			</div>
			
			<h2>Compartir artículo:</h2>
				<!-- Social Media buttons -->
			<div data-social-share-privacy='true'></div>
			
			<p><?php edit_post_link('Editar esta entrada','','.');?></p>
			
			<?php comments_template(); ?>
		</div>

	<?php
	endwhile; else: ?>

		<p>Lo sentimos, no se ha encontrado el artículo.</p>

	<?php endif; ?>
	
	<?php get_sidebar(); ?>

	</div> <!-- Fin contenido -->

<?php get_footer(); ?>
