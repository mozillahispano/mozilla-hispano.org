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

			<!-- Social Media buttons -->
			<div data-social-share-privacy='true'></div>

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
			<p class="postmetadata alt">
						<?php if (function_exists("wp2bb")) wp2bb(); ?>
						<?php// if ( comments_open() && pings_open() ) {
							// Both Comments and Pings are open ?>
							<!-- You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(); ?>" rel="trackback">trackback</a> from your own site.

						<?php// } elseif ( !comments_open() && pings_open() ) {
							// Only Pings are Open ?>
							Responses are currently closed, but you can <a href="<?php trackback_url(); ?> " rel="trackback">trackback</a> from your own site.

						<?php// } elseif ( comments_open() && !pings_open() ) {
							// Comments are open, Pings are not ?>
							You can skip to the end and leave a response. Pinging is currently not allowed.

						<?php// } elseif ( !comments_open() && !pings_open() ) {
							// Neither Comments, nor Pings are open ?>
							Both comments and pings are currently closed.-->

						<?php /*}*/ edit_post_link('Editar esta entrada','','.'); ?>
			</p>
		</div>

	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p>Lo sentimos, no se ha encontrado el artículo.</p>

<?php endif; ?>

<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>
