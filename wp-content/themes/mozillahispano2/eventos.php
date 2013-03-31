<?php
/*
Template Name: Eventos
*/
get_header();
$url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
?>

	<div id="contenido">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post portada-individual" id="post-<?php the_ID(); ?>">
		<h2 class="post-title"><?php the_title(); ?></h2>
			<div class="texto-portada-individual">
				<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>
				
				<?php if ($url == "www.mozilla-hispano.org/difusion/eventos/") {?>
				<p class="evento-rss"><a href="<?php eme_rss_link(justurl)?>">Canal RSS de eventos</a></p>
					<p>Esta página muestra información sobre los próximos eventos de la comunidad de Mozilla en español.</p>
						<p>Si tienes información sobre cualquier evento que se vaya a realizar y quiere que sea añadido al calendario, <a href="/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano#Difusi.C3.B3n">ponte en contacto con nosotros</a> e infórmanos.</p>
						<h3>Próximos eventos</h3>
						
							<?php eme_get_events_list("limit=5&scope=future&order=ASC"); ?> 
						
				<?php } ?>
				
				<?php wp_reset_query(); //Reseteo del query?>

				<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
		</div>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</div>

<?//php get_sidebar("events"); ?>

<?php get_footer(); ?>