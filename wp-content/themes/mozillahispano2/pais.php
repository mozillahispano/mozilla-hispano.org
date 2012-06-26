<?php
/*
Template Name: País
*/
get_header(); ?>
	<div id="contenido">
<div id="main-content">
	
	<?php 
	
		/* Recuperamos metadatos */
		$countryCode = get_post_meta($post->ID, 'countryCode', true);
		$countryName = get_post_meta($post->ID, 'countryName', true);
		// Evitamos que los espacios den problemas en los enlaces a los rss
		$cleanCountryName = str_replace(' ','-20', $countryName);
		$countryFlickrTag = get_post_meta($post->ID, 'countryFlickrTag', true);
        $countryForumID = get_post_meta($post->ID, 'countryForumID', true);
        $countryImage = get_post_meta($post->ID, 'countryImage', true);
    ?>
    
    <h2 class="title">Comunidad en <?php echo $countryName ?></h2>

    <?php 
        if ($countryImage != '') {
            echo '<img id="countryImage" src="' . $countryImage  . '" alt="Comunidad en ' . $countryName  . '" />';
        }
    ?>
    
    <p class="countryRss"><a title="Canal RSS de los artículos" href="http://www.mozilla-hispano.org/etiqueta/<?php echo $countryCode ?>/feed/"></a></p>
    
	<?php
		/* Fix para que funcione la paginacion*/
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		query_posts('tag=' . $countryCode . '&posts_per_page=5&paged='.$paged);
		if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class('portada-individual') ?> id="post-<?php the_ID(); ?>">
				<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				
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

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Artículos antiguos') ?></div>
			<div class="alignright"><?php previous_posts_link('Artículos más recientes &raquo;') ?></div>
		</div>

	<?php else : ?>
		<p class="center">Lo sentimos, pero actualmente no hay artículos para éste país.</p>
	<?php endif; ?>
</div>

<?php
	function countryRss($feed, $limit)
	{
		$rss = fetch_feed($feed);
		
		if (!is_wp_error($rss))
		{ 
			// Checks that the object is created correctly 
			// Figure out how many total items there are, but limit it. 
			$maxitems = $rss->get_item_quantity($limit); 

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items(0, $maxitems); 
		}

		$content = '<ul>';
		
		if ($maxitems == 0)
		{
			$content .= '<li>Sin elementos.</li>';
		}
		else
		{
			// Loop through each feed item and display each item as a hyperlink.
			foreach ($rss_items as $item)
			{
					$content .= '<li>';
					$content .='<a href="' . esc_url($item->get_permalink()) . '">';
					$content .= esc_html( $item->get_title() ); 
					$content .= '</a>';
					$content .='</li>';
			}
		}
		$content .= '</ul>';$content .= "Feed: [$feed, $limit]";
		
		return $content;
	}
?>

<div id="barra">
	
	<h3>Últimos eventos</h3>
	<?php echo countryRss('https://www.mozilla-hispano.org/documentacion/Especial:Ask/-5B-5BCategor%C3%ADa:Evento-5D-5D-0A-5B-5Bpais::' . $cleanCountryName . '-5D-5D/-3F%3DNombre-23/-3FFechainicio%3DFecha/-3FPais/-3FCiudad/-3FUrl/mainlabel%3DNombre/limit%3D50/order%3DDESC,DESC/sort%3DFechainicio,/format%3Drss', 5) ?>
	
	<p class="more"><a href="https://www.mozilla-hispano.org/documentacion/Eventos/<?php echo $countryName?>">Ver todos</a></p>
	
	
	<h3>Colaboradores</h3>
	<?php echo countryRss('https://www.mozilla-hispano.org/documentacion/Especial:Ask/-5B-5BCategor%C3%ADa:Colaborador-5D-5D-20-5B-5Bpais::' . $cleanCountryName . '-5D-5D/limit%3D1000/order%3DDESC/sort%3DNombre/format%3Drss', 100) ?>
	<p class="more"><a href="http://www.mozilla-hispano.org/documentacion/Colabora">¿Quiéres colaborar?</a></p>
	
	<h3>Últimas fotos</h3>
	<!-- Start of Flickr Badge -->
	<style type="text/css">
	/*
	Las imágenes son envueltas en divs clasificadas como "flickr_badge_image" con ids "flickr_badge_imageX" donde "X" es un número entero especificando una posición ordinaria. ¡A continuación encontrarás algunos estilos para que comiences!
	*/
	#flickr_badge_uber_wrapper {text-align:center; width:150px;}
	#flickr_badge_wrapper {padding:10px 0 10px 0;}
	.flickr_badge_image {margin:0 10px 10px 10px;}
	.flickr_badge_image img {border: 1px solid black !important;}
	#flickr_badge_source {text-align:left; margin:0 10px 0 10px;}
	#flickr_badge_icon {float:left; margin-right:5px;}
	#flickr_www {display:block; padding:0 10px 0 10px !important; font: 11px Arial, Helvetica, Sans serif !important; color:#3993ff !important;}
	#flickr_badge_uber_wrapper a:hover,
	#flickr_badge_uber_wrapper a:link,
	#flickr_badge_uber_wrapper a:active,
	#flickr_badge_uber_wrapper a:visited {text-decoration:none !important; background:inherit !important;color:#3993ff;}
	#flickr_badge_wrapper {}
	#flickr_badge_source {padding:0 !important; font: 11px Arial, Helvetica, Sans serif !important; color:#666666 !important;}
	</style>
	<div id="flickr_badge_uber_wrapper"><a href="http://www.flickr.com" id="flickr_www">www.<strong style="color:#3993ff">flick<span style="color:#ff1c92">r</span></strong>.com</a><div id="flickr_badge_wrapper">
	<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=5&amp;display=latest&amp;size=t&amp;layout=x&amp;source=all_tag&amp;tag=<?php echo $countryFlickrTag ?>"></script>
	</div></div>
	<!-- End of Flickr Badge -->
	<p class="more"><a href="http://www.flickr.com/photos/tags/<?php echo $countryFlickrTag ?>/">Ver todas</a></p>

    <h3>Mensajes en el foro</h3>
    <?php echo countryRss('https://www.mozilla-hispano.org/foro/feed.php?f=' . $countryForumID  . '', 6) ?>
    <p class="more"><a href="https://www.mozilla-hispano.org/foro/viewforum.php?f=<?php echo $countryForumID ?>">Ver todos</a></p>
</div>
</div><!-- contenido -->


<?php get_footer(); ?>


