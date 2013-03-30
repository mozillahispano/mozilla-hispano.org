<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

automatic_feed_links();

if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h2 class="widgettitle"><span>',
		'after_title' => '</span></h2>',
	));
register_sidebar( array(
    'id'          => 'events',
    'name'        => __( 'Eventos'),
    'description' => __( 'Barra para mostrar en eventos.'),
) );
}

/* Activamos las miniaturas de los artículos */
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 130, 185 ); // Post thumbnails

/* Nuestros shortcodes */
function listar_tag($atts, $content = null) {
        extract(shortcode_atts(array(
                "tag" => '',
                "num" => ''
        ), $atts));
        global $post;
        $myposts = get_posts('numberposts='.$num.'&order=DESC&orderby=date&tag='.$tag);
        $list='<ul class="listado-'.$tag.'">';
        foreach($myposts as $post) :
                setup_postdata($post);
             $list.='<li><a href="'.get_permalink().'">'.the_title("","",false).'</a> <small>'.get_the_time('j F, 
Y').'</small></li>';
        endforeach;
	#rewind_posts();
        $list.='</ul> ';
        return $list;
}
add_shortcode("listar-tag", "listar_tag");

function video($atts, $content = null) {
        extract(shortcode_atts(array(
                "width" => '',
                "height" => '',
                "poster" => '',
                "ogv" => '',
                "mp4" => '',
                "flash" => ''
        ), $atts));
        global $post;
        /* Tomamos tamaños o si no tomamos valores por defecto */
		if (empty($width)) $width = "640";
		if (empty($height)) $height = "510";
		
        $salida = "<video";
		$salida .= " width=\"" . $width . "\" ";
		$salida .= " height=\"" . $height . "\" ";

		if (!empty($poster))
			$salida .= "poster=\"" . $poster . "\" "; 

		$salida .= "controls>";

		$salida .= "<source src=\"" . $ogv . "\" type=\"video/ogg\" />";

		if (!empty($mp4))
			$salida .= "<source src=\"" . $mp4 . "\" type=\"video/mp4\" />";

		$salida .= "<object width=\"" . $width . "\" height=\"" . $height . "\" 
type=\"application/x-shockwave-flash\"";
		$salida .= " data=\"" . $flash . "\">";
		$salida .= "<param name=\"movie\" value=\"" . $flash . "\" />";
		$salida .=	"<p>Necesitas el plugin de Flash para ver este vídeo</p></object></video>";

		$salida .= "<p class=\"descarga-video\">Descargar vídeo <a href=\"" . $ogv . "\">en formato 
libre ogv</a>";

		if (!empty($mp4))
			$salida .= " o <a href=\"" . $mp4 . "\">en formato mp4</a>.";

		$salida .= "</p>";
        return $salida;
}
add_shortcode("video", "video");

function podcast_metadata() {
global $post;
$text="<p>Podéis descargar directamente el archivo o suscribiros al <a hreflang=\"es\" 
href=\"http://feeds.mozilla-hispano.org/mozillahispano-podcast\">RSS del podcast</a> con vuestro lector de 
podcast preferido, <a hreflang=\"es\" 
href=\"http://itunes.apple.com/es/podcast/el-podcast-de-mozilla-hispano/id347273991\">iTunes</a> o <a href=\"http://www.miroguide.com/audio/14695\">Miro</a>.</p>";

$text.= "<p>MP3 | <strong><a href=\"" . get_post_meta($post->ID, 'podcast_mp3', true) . "\">El podcast de Mozilla 
Hispano #" . get_post_meta($post->ID, 'podcast_num', true) . "</a></strong></p>";

$text.= "<p>OGG | <strong><a href=\"" . get_post_meta($post->ID, 'podcast_ogg', true) . "\">El podcast de Mozilla 
Hispano #" . get_post_meta($post->ID, 'podcast_num', true) . "</a></strong></p>";

$text.= "<p> 
	<audio controls=\"controls\" src=\"" . get_post_meta($post->ID, 'podcast_ogg', true) . "\" 
tabindex=\"0\">
		
				<p><small>Streaming con flash</small></p>
						
								<p>
											<object width=\"300\" 
height=\"25\" data=\"http://blip.tv/scripts/flash/blipmp3player.swf?song_url=" . get_post_meta($post->ID, 
'podcast_mp3', true) . "%3Fsource%3D1&amp;autoload=true&amp;song_title=Mozilla%20Hispano%20%23" . 
get_post_meta($post->ID, 'podcast_num', true) . "\" type=\"application/x-shockwave-flash\">
														
																		
<param value=\"http://blip.tv/scripts/flash/blipmp3player.swf?song_url=" . get_post_meta($post->ID, 
'podcast_mp3', true). "%3Fsource%3D1&amp;autoload=true&amp;song_title=Mozilla%20Hispano%20%23" . 
get_post_meta($post->ID, 'podcast_num', true) . "\" name=\"movie\"/>
																						
																										
<p>Escucha el podcast en streaming mediante <a href=\"http://www.macromedia.com/downloads/\">flash 
player.</a></p>
																														
																																	
</object> 
																																			
</p>
																																				
</audio>
																																				
</p>";
return $text;
}

add_shortcode("podcast-metadata", "podcast_metadata");

function get_search_MH_form() {
	do_action( 'get_search_form' );

	$search_form_template = locate_template(array('searchform.php'));
	if ( '' != $search_form_template ) {
		require($search_form_template);
		return;
	}

	$form = '<form method="get" id="searchform" action="/buscar.php" >
	<div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
	<input type="text" value="' . esc_attr(apply_filters('the_search_query', get_search_query())) . '" 
name="q" id="s" />
	<input type="hidden" name="desde" value="noti" />
	<input type="hidden" name="donde" value="noti" />
	<button type="submit" id="searchsubmit" class="button">'. esc_attr__('Search') .'</button>
	</div>
	</form>';

	echo apply_filters('get_search_form', $form);
}



function ultimos_posts() {
        global $post;

        if (function_exists('wpphpbb_topics')):
        	$listado=wpphpbb_topics();
			$salida = "<ul>";
				$salida .= $listado ;
			$salida .= "</ul>";
		endif;
		
        return $salida;
}
add_shortcode("ultimos-posts", "ultimos_posts");

function listar_noticias($atts, $content = null) {
        extract(shortcode_atts(array(
                "tag" => '',
                "num" => ''
        ), $atts));
        global $post;
        $tmp_post = $post;
        $myposts = get_posts('numberposts='.$num.'&order=DESC&orderby=date&tag='.$tag);
        
        foreach($myposts as $post) :
        	setup_postdata($post);
        	$list.="<div class=\"noticia-".$tag."\">";
        		$list.="<h3><a title=\"" . get_the_title() . "\" href=\"" . get_permalink() . "\">" . 
get_the_title() ."</a></h3>";
        		$list.='<p>' . get_the_excerpt() . '</p>';
        	$list.='</div>';
        endforeach;
        wp_reset_query();
        return $list;
}
add_shortcode("listar-noticias", "listar_noticias");

function listar_flickr($atts, $content = null) {

	extract(shortcode_atts(array(
                "tag" => '',
                "num" => ''
        ), $atts));

	require_once("/var/www/mozilla-hispano/eventos/flickr/phpFlickr.php"); //Incluyendo el API de Flickr
	$f = new phpFlickr("XXXXXXXXXXXXXXXXXX"); //Clase de Api, conseguir en: http://www.flickr.com/services/api/keys/
	$nsid = ""; //NSID Usuario, conseguir en: http://idgettr.com/
	//Incluir tag, ordenamieno, privacidad, y numero de imagenes a mostrar
	$photos = $f->photos_search(array("tags"=>$tag, "user_id"=>$nsid, "sort"=>"date-posted-desc", 
"privacy_filter"=>"1", "per_page"=>$num));
	$url    = "https://secure.flickr.com/photos/".$photo['id']."/"; //Url de la Imgen Original
	
	$salida='<div id="fotos">';
	if (is_array($photos['photo']))
	{
		$sw= 1;
		foreach ($photos['photo'] as $photo)
		{
			if ($sw == 1)
			{
				$salida .= "<div class='foto'>";
				$salida .= "<a href='".$f->buildPhotoURL($photo, "medium")."' 
title='".$photo['title']."' rel='" . $tag . "'><img alt='".$photo['title']."' 
title='".$photo['title']."' "."src='".$f->buildPhotoURL($photo, "square")."' /></a>";
				$sw=0;
				$salida.= "</div>";
 			}
			else
			{
				$salida .= "<div class='foto'>";
				$salida .= "<a href='".$f->buildPhotoURL($photo, "medium")."' 
title='".$photo['title']."' rel='" . $tag . "'><img alt='".$photo['title']."' 
title='".$photo['title']."' "."src='".$f->buildPhotoURL($photo, "square")."' /></a>";
				$salida.="</div>";
				$sw=1;
			}
		}
	}


	$salida.='</div><!-- Fotos -->';
							
	$salida.='<p class="all-photos"><a title="Canal RSS de las fotos" href="https://secure.flickr.com/services/feeds/photos_public.gne?tags=' . 
$tag . '&amp;lang=es-us&amp;format=rss_200"><img src="/wp-content/themes/mozillahispano/img/rss.png" 
alt=""/></a> <a href="https://secure.flickr.com/photos/tags/' . $tag . '/">Ver todas las 
fotos</a></p>';

	return $salida;
}
add_shortcode("listar-flickr", "listar_flickr");

/* Eliminamos el meta generator del header */
remove_action('wp_head', 'wp_generator');
/* Quitamos los enlaces a los feeds, lo meteremos a mano */
remove_theme_support('automatic-feed-links');
?>
