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
tabindex=\"0\"></audio>
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
	<input type="text" placeholder="Buscar" value="' . esc_attr(apply_filters('the_search_query', get_search_query())) . '"
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

function listar_fecha($atts, $content = null) {
	extract(shortcode_atts(array(
                "tag" => '',
                "month" => '',
		"year" => ''
        ), $atts));
	$myposts = get_posts('monthnum='.$month.'&year=' . $year . '&order=DESC&orderby=date&tag='.$tag);

        foreach($myposts as $post) :
        	setup_postdata($post);
        	$list.='<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';

		if ( has_post_thumbnail() )
		{
			// the current post has a thumbnail
			$list.='<p style="float:left; margin-right: 1em;">' . get_the_post_thumbnail( array(130,185) ) . '</p>';
		}

		$list.='<p>' . get_the_excerpt() . '</p>';
        endforeach;
        wp_reset_query();
        return $list;
}
add_shortcode("listar-fecha", "listar_fecha");

/* Activamos los background updates en directorios con control de versiones */
add_filter( 'automatic_updates_is_vcs_checkout', '__return_false' );

/* Eliminamos el meta generator del header */
remove_action('wp_head', 'wp_generator');
/* Quitamos los enlaces a los feeds, lo meteremos a mano */
remove_theme_support('automatic-feed-links');

/* Conseguimos sacar los posts más vistos */
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
// Remove issues with prefetching adding extra views
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
?>
