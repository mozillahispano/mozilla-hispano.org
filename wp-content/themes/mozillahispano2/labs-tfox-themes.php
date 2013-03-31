<?php
/*
Template Name: Labs Tfox Themes
*/
get_header(); 

function getThemeRaw($aTheme, $ruta){
    $path = $ruta;
    $path .= '/' . $aTheme . "/theme.css";
    
    $f = fopen($path,"r");
    $data='';
    $data.=fread($f,500);
    fclose($f);
    
    return $data;
}


// Leer los metadatos de un theme
// getMetaData(nombre)
function getMetaData($aTheme, $ruta){
    $metadataStart = "==tuentitheme==";
	
    $datos = array(            
        "theme"=>$aTheme,     
        "author"=>"Desconocido",
        "name"=>"Desconocido",  
        "version"=>"0.0"
    );
    $text = getThemeRaw($aTheme, $ruta);
    $index = strpos($text,$metadataStart);
    
    if($index !== false){                                  
        $text = substr($text, $index + strlen($metadataStart));
	
        $end = strpos($text,$metadataStart);
        if($end !== false){
            $text=substr($text,0,$end);
        }
        $trozos = preg_split('/[\n\r]+/',$text);
        for($i = 0, $l = count($trozos); $i<$l; $i++){
            $matches = array();
            if(preg_match('/^\s*([a-zA-Z]+)\s*:\s*(\S(?:.*\S)?)\s*$/',$trozos[$i],$matches) &&
               isset($datos[strtolower($matches[1])])){;
                $datos[strtolower($matches[1])] = $matches[2];                
            }                                                              
        }                                                                  
    }      
    return $datos;
}

// Leer todos los themes disponibles en la carpeta
function arrayThemes()
{
	$ruta = '/archivos/labs/tfox/themes/';
	$directorio = $_SERVER["DOCUMENT_ROOT"] . $ruta;
	$baseURL = "http://www.mozilla-hispano.org";
	if (is_dir($directorio)) {
		$mydir=opendir($directorio);
		$num = 0;
		
		while($archivo=readdir($mydir))
		{
				$rutaArchivo = $directorio . $archivo;
				
				// Leemos todas las carpetas existentes y guardamos los datos
				if (is_dir($rutaArchivo) && $archivo != "." && $archivo != "..") {
					
					$datos=getMetaData($archivo, $directorio);
					$themes[$num] = array('name'=>$datos['name'], 'screenshot'=>$ruta . $archivo . '/screenshot.png', 'icon'=>$baseURL . $ruta . $archivo . '/icon.png', 'theme'=>$baseURL . $ruta . $archivo . '/theme.css', 'author'=>$datos['author'], 'version'=>$datos['version']);
					$num++;
				}
		}
		
		closedir($mydir);
	}
	return $themes;
}
?>
		<div id="contenido">
		
			<div class="post portada-individual" id="post-<?php the_ID(); ?>">
				<h2 class="post-title"><?php the_title(); ?></h2>
				
				<div class="navigation">
					<div class="alignright"><a href="/labs/">Mozilla Hispano Labs</a> » <a href="<?php echo get_permalink($post->post_parent); ?>"><?php echo get_the_title($post->post_parent); ?></a> » <?php the_title();?></div>
				</div>
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
				<div class="texto-portada-individual">
					<?php the_content('<p class="serif">Leer el resto de esta página &raquo;</p>'); ?>
					
					<div id="themes">
					
					<?php
						// Mostramos todos los themes disponibles
						$themes = arrayThemes();
						foreach($themes as $i => $value) {
							echo '
								<div style="background-image:url(\'' . $themes[$i]['screenshot'] . '\')" class="theme" theme="{\'name\':\'' . $themes[$i]['name'] . '\',\'icon\':\'' . $themes[$i]['icon'] . '\',\'screenshot\':\'' . $themes[$i]['screenshot'] . '\',\'url\':\'' . $themes[$i]['theme'] . '\',\'author\':\'' . $themes[$i]['author'] . '\'}">
								
								<p class="actions"> <a href="#" onclick="installTheme(this); return false" class="install">Instalar</a> <a title="' . $themes[$i]['name'] . '" class="fancybox preview" rel="fancybox" href="' . $themes[$i]['screenshot'] . '">Vista previa</a></p>
								<p class="details"><span class="theme-name">' . $themes[$i]['name'] . '</span><br /><span class="theme-author">por ' . $themes[$i]['author'] . '</span></p>
								
								</div>
							';
						}
					?>
						
					</div>

					<?php wp_link_pages(array('before' => '<p><strong>Páginas:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				</div>
			</div>
			<?php edit_post_link('Edit this entry.', '<p style="clear: both">', '</p>'); ?>
		<?php endwhile; endif; ?>
		</div>

<?php 
	// Recogemos los metadatos de la página padre en variables
	$labs_xpi = get_post_meta($post->post_parent, 'labs_xpi', true);
	$labs_icon = get_post_meta($post->post_parent, 'labs_icon', true);
	$labs_hash = get_post_meta($post->post_parent, 'labs_hash', true);
	$labs_version = get_post_meta($post->post_parent, 'labs_version', true);
	
	$labs_manual = get_post_meta($post->post_parent, 'labs_manual', true);
	$labs_ayuda = get_post_meta($post->post_parent, 'labs_ayuda', true);
	$labs_bugs = get_post_meta($post->post_parent, 'labs_bugs', true);
	$labs_desarrollo = get_post_meta($post->post_parent, 'labs_desarrollo', true);
	$labs_amo = get_post_meta($post->post_parent, 'labs_amo', true);
?>

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

<?php get_footer(); ?>
</div>
