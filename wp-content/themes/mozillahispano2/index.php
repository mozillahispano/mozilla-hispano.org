<?php
/**
* @package WordPress
* @subpackage Default_Theme
*/
get_header(); ?>
	<div id="contenido">
		<div class="home" id="content-bar">
			<?php echo do_shortcode('[orbit-slider]') ?>

	<!--<div id="feat-articles">
		<h2 class="lead"><a href="/etiqueta/destacado/"><span>Más</span> Artículos destacados</a></h2>
	<ol class="post-list hfeed title">
		<?php 
			// Listamos los 3 ultimos destacados
			global $post;
 			$tmp_post = $post;
 			$myposts = get_posts('tag=destacado&showposts=3&order=DESC&orderby=date');
			foreach($myposts as $post) :
   			setup_postdata($post);
		?>
			<li class="hentry post">
				<h3 class="entry-title"><a title="<?php the_title_attribute('echo=0'); ?>" rel="bookmark" href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
				<p class="entry-meta">
					<abbr class="published"><?php the_time(__('j F, Y')) ?></abbr> • por <?php the_author_posts_link(); ?> • <?php the_tags('', ' ', ''); ?> • <?php if (function_exists("wp2bb")) wp2bb(); ?>
			  	</p>
			</li>
		<?php endforeach; ?>
		<?php $post = $tmp_post; ?>
		</ol>
	</div> -->
	<!--<div id="feat-demos">
   		<h2 class="lead">--><!--<span>Más</span>--> <!-- Secciones destacadas</h2>
    
   		 <ol class="post-list hfeed homehead">
        <?php
            $args = array(
                'orderby'          => 'id',
                'order'            => 'DESC',
                'limit'            => 3,
                'category'         => '126',
                'echo'             => 1,
                'categorize'       => 0,
                'title_li'            => '',
                'category_before'  => '',
                'category_after'   => '' ,
		'before'	   => '<li class="hentry post demo"><h3 class="entry-title">',
		'after'	   => '</h3></li>',
		'class'		   => 'hentry post demo',
		'show_images'	   => '1',
		'show_name'	   => '1',
		'link_before'	   => '<span class="thumb">',
		'link_after'	   => '</span>',
                );
            wp_list_bookmarks( $args );
        ?>
      </ol>
 	</div>-->

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

				<!--<p class="postmetadata"><?php edit_post_link('Editar', '', ''); ?> <?php if (function_exists("wp2bb")) wp2bb(); ?><?php// comments_popup_link('Sin comentarios &#187;', '1 comentario &#187;', '% comentarios &#187;'); ?></p>-->
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
