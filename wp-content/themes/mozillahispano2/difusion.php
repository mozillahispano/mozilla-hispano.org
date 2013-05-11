<?php
/*
Template Name: Difusion
*/

get_header(); ?>
		<div id="contenido">
		
			<div class="post portada-individual" id="post-<?php the_ID(); ?>">
				<h2 class="post-title"><?php the_title(); ?></h2>
				
				<div class="navigation">
					<div class="alignright"><a href="/difusion/">Difusión</a>
					<?php
						if ($post->post_parent) {
							$children = $post->post_title;
							$parent = get_page($post->post_parent);
							echo " » <a href='" . get_permalink($parent->ID) . "'>".$parent->post_title."</a> » ".$children;
						}
						elseif ( $post->post_parent == 0 ) {
							echo " » ";
							echo the_title();
						}
					?>
					</div>
				</div>
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
				<div class="texto-portada-individual">
					<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>

					<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div>
			</div>
			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		<?php endwhile; endif; ?>
		

<div id="barra-small">
	<ul>
		<li class="campana-destacada">
		<?php
		// Listamos la imagen de la ultima campaña destacada
            $args = array(
                'orderby'          => 'id',
                'order'            => 'DESC',
                'limit'            => 1,
                'category'         => '135',
                'echo'             => 1,
                'categorize'       => 0,
                'title_li'            => '',
                'category_before'  => '',
                'category_after'   => '' ,
		'before'	   => '',
		'after'	   => '',
		'class'		   => '',
		'show_images'	   => '1',
		'show_name'	   => '0',
		'link_before'	   => '',
		'link_after'	   => '',
                );
            wp_list_bookmarks( $args );
        ?>
		</li>

		<li id="campanas" class="widget">
			<h3>Campañas activas</h3>
			<ul>
				<?php
					$args = array(
						'orderby'          => 'id',
						'order'            => 'DESC',
						'limit'            => 10,
						'category'         => '136',
						'echo'             => 1,
						'categorize'       => 0,
						'title_li'            => '',
						'category_before'  => '',
						'category_after'   => '' ,
						'before'	   => '<li>',
						'after'	   => '</li>',
						'class'		   => '',
						'show_images'	   => '0',
						'show_name'	   => '1',
						'link_before'	   => '',
						'link_after'	   => '',
						);
					wp_list_bookmarks( $args );
				?>
			</ul>
		</li>

		<li id="material" class="widget">
			<h3>Material</h3>
			<div class="cajaPodcast">
				<a href="/difusion/material/"><img src="/wp-content/themes/mozillahispano/img/materiales.png" alt="Colabora"/></a>
				<p>Imágenes, banners, presentaciones y contenido multimedia</p>
			</div>
		</li>
		
			<li id="twitter" class="widget">
				<h2><span>Twitter</span></h2>
				<div class="cajaTwitter">
						<script src="http://widgets.twimg.com/j/2/widget.js"></script>
						    <script>
						    new TWTR.Widget({
						      version: 2,
						      type: 'search',
						      search: '#mozillahispano',
						      interval: 6000,
						      title: 'Mozilla Hispano',
						      subject: '#mozillahispano',
						      width: 220,
						      height: 600,
						      theme: {
							shell: {
							  background: 'transparent',
							  color: '#444444'
							},
							tweets: {
							  background: '#ffffff',
							  color: '#444444',
							  links: '#264373'
							}
						      },
						      features: {
							scrollbar: true,
							loop: false,
							live: true,
							hashtags: false,
							timestamp: true,
							avatars: true,
							toptweets: false,
							behavior: 'all'
						      }
						    }).render().start();
						</script>
				</div>
			</li>

		<li id="colabora" class="widget">
			<h2><span>Colabora</span></h2>
			<div class="cajaPodcast">
				<a href="/documentacion/Colabora"><img src="/wp-content/themes/mozillahispano/img/colabora-icon.png" alt="Colabora"/></a>
				<p>Descubre todas las área de colaboración y cómo ayudar</p>
			</div>
		</li>
	</ul>	
</div>
</div>

<?php get_footer(); ?>
</div>
