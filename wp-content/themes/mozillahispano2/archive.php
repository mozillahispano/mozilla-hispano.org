<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

<div id="contenido">
	<div id="main-content">
		<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Artículos etiquetados con &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archivo de <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archivo de <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archivo de <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Archivo del redactor</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Archivos</h2>
 	  <?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Artículos más antiguos') ?></div>
			<div class="alignright"><?php previous_posts_link('Artículos más recientes &raquo;') ?></div>
		</div>

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
			<div class="alignleft"><?php next_posts_link('&laquo; Artículos anteriores') ?></div>
			<div class="alignright"><?php previous_posts_link('Artículos más recientes &raquo;') ?></div>
		</div>
	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>No hay artículos.</h2>");
		}
		get_search_form();

	endif;
?>

	</div>

<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>
