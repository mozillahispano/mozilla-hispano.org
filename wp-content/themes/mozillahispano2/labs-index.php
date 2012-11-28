<?php
/*
Template Name: Portada Labs
*/
get_header(); ?>

<!-- Carga del Javascript y estilos del bloque de Github -->

	<link rel="stylesheet" href="<?php echo get_bloginfo ('template_url');?>/css/github.min.css">
	<script src="<?php echo get_bloginfo ('template_url');?>/js/jquery.github.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function(){
			$("#github-block").github();
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
				<h2>Proyectos</h2>
					<?php 
							// Vamos a sacar un listado de subpaginas de Labs y con los metadatos de cada una de ellas ;)
							$parent = $post->ID;
							
							query_posts('orderby=date&order=ASC&post_type=page&post_parent='.$parent);
							
					 		while (have_posts()) : the_post();
					?>
						<div class="col-logo">
							<a href="<?php the_permalink(); ?>"><img src="<?php echo get_post_meta($post->ID, 'labs_logo', true); ?>" alt="<?php the_title();?>"/></a>
						</div>
						
						<div class="col-proy">
							<h3><?php echo get_post_meta($post->ID, 'labs_cat', true); ?></h3>
							<h4><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h4>
							<p><?php echo get_post_meta($post->ID, 'labs_desc', true); ?></p>
							<p class="more-info"><a href="<?php the_permalink(); ?>">Más información sobre este proyecto</a></p>
						</div>
					<?php endwhile; ?>

				<h2>Colabora en Github</h2>

				<div id="github-block"></div>

		</div>
		
		<div id="ultimas-noticias">
			<h2>Artículos</h2>
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
				
				<p id="labs-more"><a href="/etiqueta/desarrollo/">Ver todos los artículos</a></p>
			<?php endif; ?>

		</div>

<?php get_footer(); ?>
