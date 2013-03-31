<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

	<div id="contenido" class="narrowcolumn" role="main">
	
		
	<div id="main-content">
		<?php if (have_posts()) : ?>
		<h2 class="pagetitle">Resultados de la búsqueda</h2>
			<div class="navigation">
				<div class="alignleft"><?php next_posts_link('&laquo; Artículos anteriores') ?></div>
				<div class="alignright"><?php previous_posts_link('Artículos más recientes &raquo;') ?></div>
			</div>


			<?php while (have_posts()) : the_post(); ?>

				<div <?php post_class('portada-individual') ?>>
					<h2  class="post-title" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Enlace permanente a <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					
					<p class="dia-publicacion"><?php the_time('j F, Y') ?> <?php the_time('G:i');?> :: <?php the_author_posts_link(); ?></p>
					
					<p class="autor-categorias"><span><?php the_tags('', ' ', ''); ?></span></p>

					<p class="postmetadata"><?php edit_post_link('Editar', '', ''); ?>  <?php// comments_popup_link('Sin comentarios &#187;', '1 comentario &#187;', '% comentarios &#187;'); ?></p>
				</div>

			<?php endwhile; ?>

			<div class="navigation">
				<div class="alignleft"><?php next_posts_link('&laquo; Artículos anteriores') ?></div>
				<div class="alignright"><?php previous_posts_link('Artículos más recientes &raquo;') ?></div>
			</div>

		<?php else : ?>
			<div class="portada-individual">
				<div class="texto-portada-individual">
					<h2>Resultados</h2>
						<p>No se encontró nada. Prueba una búsqueda diferente.</p>
						<?php get_search_form(); ?>
				</div>
			</div>

		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
	
	</div>



<?php get_footer(); ?>

