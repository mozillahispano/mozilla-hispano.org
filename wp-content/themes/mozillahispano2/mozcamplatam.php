<?php
/*
Template Name: Mozcamplatam
*/

get_header('mozcamplatam'); ?>
		<div id="contenido">
		
			<div class="post portada-individual" id="post-<?php the_ID(); ?>">
				
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
				<div class="texto-portada-individual">
					<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div>
			</div>
		</div>
			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		<?php endwhile; endif; ?>
		

</div>

<?php get_footer(); ?>
</div>
