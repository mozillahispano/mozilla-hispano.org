<?php
/*
Template Name: Labs Page
*/
get_header(); ?>

<?php 
	// Recogemos los metadatos en variables
	$labs_xpi = get_post_meta($post->ID, 'labs_xpi', true);
	$labs_icon = get_post_meta($post->ID, 'labs_icon', true);
	$labs_hash = get_post_meta($post->ID, 'labs_hash', true);
	$labs_version = get_post_meta($post->ID, 'labs_version', true);
	
	$labs_manual = get_post_meta($post->ID, 'labs_manual', true);
	$labs_ayuda = get_post_meta($post->ID, 'labs_ayuda', true);
	$labs_bugs = get_post_meta($post->ID, 'labs_bugs', true);
	$labs_desarrollo = get_post_meta($post->ID, 'labs_desarrollo', true);
	$labs_amo = get_post_meta($post->ID, 'labs_amo', true);
?>
		<div id="contenido">
		
			<div class="post portada-individual" id="post-<?php the_ID(); ?>">
				<h2 class="post-title"><?php the_title(); ?></h2>
				
				<div class="navigation">
					<div class="alignright"><a href="/labs/">Mozilla Hispano Labs</a> » <?php the_title();?></div>
				</div>
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
				<div class="texto-portada-individual">
					<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div>
			</div>
			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		<?php endwhile; endif; ?>
		
		<div id="barra">
			<p class="descarga">
				<a class="enlace_xpi" href="<?php echo $labs_xpi?>" iconURL="<?php echo $labs_icon ?>"
				hash="<?php echo $labs_hash ?>" onclick="return install(event);">Instalar ahora</a>
				<br />
				Versión: <?php echo $labs_version ?>
			</p>
			<ul id="enlaces">
				<?php if(!empty($labs_manual)): ?><li><a href="<?php echo $labs_manual ?>">Manual de ayuda</a></li><?php endif; ?>
				<?php if(!empty($labs_ayuda)): ?><li><a href="<?php echo $labs_ayuda ?>">Foro de ayuda a usuarios</a></li><?php endif; ?>
				<?php if(!empty($labs_bugs)): ?><li><a href="<?php echo $labs_bugs ?>">Informa de un error o propuesta</a></li><?php endif; ?>
				<?php if(!empty($labs_desarrollo)): ?><li><a href="<?php echo $labs_desarrollo ?>">Colabora con su desarrollo</a></li><?php endif; ?>
				<?php if(!empty($labs_amo)): ?><li><a href="<?php echo $labs_amo ?>">Valorar en Mozilla Addons</a></li><?php endif; ?>
			</ul>
		</div>
		
		</div>

<?php get_footer(); ?>
</div>
