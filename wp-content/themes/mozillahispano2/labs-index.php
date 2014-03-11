<?php
/*
Template Name: Portada Labs
*/
get_header(); ?>

<!-- Carga del Javascript y estilos del bloque de Github -->

	<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/css/github.min.css">
	<script src="/wp-content/themes/mozillahispano2/js/jquery.github.min.js"></script>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#github-block").github();
		});
	</script>

<!-- FIN Carga del Javascript del bloque de Github -->

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>">
		
			<div>
				<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div>
		<?php endwhile; endif; ?>
		
		<div id="labs-sidebar">
				<h2>Colabora en Github</h2>

				<div id="github-block"></div>

				<h2>Labs en YouTube</h2>
				<?php if(function_exists('db_yt_rss_markup')) { db_yt_rss_markup(); }; ?>

		</div>
		
		<div id="ultimas-noticias">
                    <h2><a href="http://www.mozilla-hispano.org/etiqueta/labs/feed/"><img src="/wp-content/themes/mozillahispano/img/rss.png" alt="feed" /></a> Artículos</h2>
			<?php 
				query_posts('tag=labs&posts_per_page=5');
				if (have_posts()) : ?>

				<?php while (have_posts()) : the_post(); ?>

					<div <?php post_class('portada-individual') ?> id="post-<?php the_ID(); ?>">
						<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						
						<!--<p class="autor-categorias"><span><?php the_tags('', ' ', ''); ?></span></p>-->
						
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
				
				<p id="labs-more"><a href="/etiqueta/labs/">Ver todos los artículos</a></p>
			<?php endif; ?>

		</div>

<?php get_footer(); ?>
