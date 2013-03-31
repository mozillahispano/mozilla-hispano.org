<?php
/*
Template Name: Podcast Page
*/
get_header();
?>

	<div id="contenido">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class('portada-individual') ?> id="post-<?php the_ID(); ?>">
			<h2 class="post-title"><?php the_title(); ?></h2>
			
			<p class="dia-publicacion"><?php the_time('j F, Y') ?> <?php the_time('G:i');?> :: <?php the_author_posts_link(); ?></p>
			
			<p class="autor-categorias"><span><?php the_tags('', ' ', ''); ?></span></p>

			<div class="texto-portada-individual">
				<?php the_content('Leer el resto del artículo &raquo;'); ?>
				
				<p>Podéis descargar directamente el archivo o suscribiros al <a hreflang="es" href="http://feeds.mozilla-hispano.org/mozillahispano-podcast">RSS del podcast</a> con vuestro lector de podcast preferido.</p>

				<p>MP3 | <strong><a href="<?php get_post_meta($post->ID, 'podcast_mp3', true); ?>">El podcast de Mozilla Hispano #<?php get_post_meta($post->ID, 'podcast_num', true); ?></a></strong></p>

				<p>OGG | <strong><a href="<?php get_post_meta($post->ID, 'podcast_ogg', true); ?>">El podcast de Mozilla Hispano #<?php get_post_meta($post->ID, 'podcast_num', true); ?></a></strong></p>

				<p> 
					<audio controls="controls" src="<?php get_post_meta($post->ID, 'podcast_ogg', true); ?>" tabindex="0">
						
								<p><small>Streaming con flash</small></p>
										
												<p>
															<object width="300" height="25" data="http://blip.tv/scripts/flash/blipmp3player.swf?song_url=<?php get_post_meta($post->ID, 'podcast_mp3', true); ?>%3Fsource%3D1&amp;autoload=true&amp;song_title=Mozilla%20Hispano%20%23<?php get_post_meta($post->ID, 'podcast_num', true); ?>" type="application/x-shockwave-flash">
																		
																						<param value="http://blip.tv/scripts/flash/blipmp3player.swf?song_url=<?php get_post_meta($post->ID, 'podcast_mp3', true); ?>%3Fsource%3D1&amp;autoload=true&amp;song_title=Mozilla%20Hispano%20%23<?php get_post_meta($post->ID, 'podcast_num', true); ?>" name="movie"/>
																										
																														<p>Escucha el podcast en streaming mediante <a href="http://www.macromedia.com/downloads/">flash player.</a></p>
																																		
																																					</object> 
																																							</p>
																																								</audio>
																																								</p>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
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

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
