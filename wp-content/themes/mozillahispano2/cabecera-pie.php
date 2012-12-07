<?php
function pintaCabecera()
{
$temp= '
<div id="cabecera">
				<div id="alojaLogo">
					<h1><a href="'.site_url().'/">Mozilla hispano, tu comunidad en español de Mozilla</a></h1>		
				</div>			
				<div id="menu">
					<ul class="clearfix">
						<li><a href="'.site_url().'/">Noticias</a></li>
						<li><span>Asistencia</span>
							<ul class="submenu">
								<li><a href="'.site_url().'/foro/" title="Foro de asistencia técnica">Foro de asistencia</a></li>
								<li><a href="'.site_url().'/documentacion/" title="Documentos, artículos y preguntas frecuentes de ayuda a los usuarios">Documentación</a></li>
							</ul>
						</li>
						<li><span title="Ayuda a difundir Mozilla en español">Difusión</span>
							<ul class="submenu">
								<li><a href="'.site_url().'/difusion/">Promociona Mozilla</a></li>
								<li><a href="'.site_url().'/foro/viewforum.php?f=6" title="Foro de difusión">Foros</a></li>
								<li><a href="'.site_url().'/documentacion/Eventos" title="Eventos Mozilla">Eventos</a></li>
								<li><a href="'.site_url().'/documentacion/Difusi%C3%B3n" title="Proyectos de difusión">Proyectos</a></li>
							</ul>
						</li>
						<li><a href="'.site_url().'/podcast/">Podcast</a></li>
                        <li><span title="Desarrolla con Labs">Labs</span>
                            <ul class="submenu">
                                <li><a href="'.site_url().'/labs/" title="Centro de desarrollo e innovación en la plataforma Mozilla">Blog y proyectos</a></li>
                                <li><a href="'.site_url().'/foro/viewforum.php?f=25">Foros</a></li>
                            </ul>
                        </li>
						<li><span>Comunidad</span>
							<ul class="submenu">
								<li><a href="'.site_url().'/planet/" title="Artículos de opinión, mensajes y fotos de los miembros de la comunidad">Planet</a></li>
								<li><a href="'.site_url().'/documentacion/Colaboradores" title="Listado de colaboradores">Colaboradores</a></li>
								<li><a href="'.site_url().'/ar/">Argentina</a></li>
								<li><a href="'.site_url().'/bo/">Bolivia</a></li>
								<li><a href="'.site_url().'/co/">Colombia</a></li>
								<li><a href="'.site_url().'/cr/">Costa Rica</a></li>
								<li><a href="'.site_url().'/cl/">Chile</a></li>
								<li><a href="'.site_url().'/cu/">Cuba</a></li>
								<li><a href="'.site_url().'/es/">España</a></li>
								<li><a href="'.site_url().'/ec/">Ecuador</a></li>
								<li><a href="'.site_url().'/ni/">Nicaragua</a></li>
								<li><a href="'.site_url().'/mx/">México</a></li>
								<li><a href="'.site_url().'/py/">Paraguay</a></li>
								<li><a href="'.site_url().'/pe/">Perú</a></li>
								<li><a href="'.site_url().'/uy/">Uruguay</a></li>
								<li><a href="'.site_url().'/ve/">Venezuela</a></li>
							</ul>
						</li>
						<li><span title="Únete y colabora con Mozilla Hispano">Participa</span>
							<ul class="submenu">
								<li><a href="'.site_url().'/documentacion/Colabora">Cómo participar</a></li>
								<li><a href="'.site_url().'/documentacion/Programa_de_mentores" title="Programa de mentores">Programa de mentores</a></li>
                                <li><a href="'.site_url().'/documentacion/Recursos_para_colaboradores" title="Recursos para colaboradores">Recursos para colaboradores</a></li>
                                <li><a href="'.site_url().'/foro/viewforum.php?f=11">Foros</a></li>
							</ul>
						</li>
					</ul>
				</div>	
				<div id="social">
					<ul>
						<li><a title="Síguenos en Twitter" id="twitter-icon" href="http://twitter.com/mozilla_hispano">Síguenos en Twitter</a></li>
						<li><a title="Síguenos en Facebook" id="facebook-icon" href="http://www.facebook.com/mozillahispano">Síguenos en Facebook</a></li>
						<li><a title="Síguenos en Google Plus" id="gplus-icon" href="https://plus.google.com/113725577998863887008/posts">Síguenos en Google Plus</a></li>
						<li><a title="Nuestros vídeos en YouTube" id="youtube-icon" href="http://www.youtube.com/mozillahispano">Nuestros vídeos en YouTube</a></li>
						<li><a title="Nuestros fotos en Flickr" id="flickr-icon" href="http://www.flickr.com/photos/tags/mozillahispano/">Nuestras fotos en Flickr</a></li>
						<li><a title="Feed RSS" id="rss-icon" href="http://feeds.mozilla-hispano.org/mozillahispano">Feed RSS</a></li>
					</ul>
				</div>
</div>
';
return $temp;
}

function pintaPie()
{
$temp='
<div id="pie">
	<div id="pie-contenido">
		<div class="c2">
			<a href="'.site_url().'/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano#Licencia" title="Licencia para el uso del contenido de este proyecto"><img src="/images/cc-by-sa.png" alt="cc-by-sa"/></a>
		</div>

		<div class="c1">
			<strong>Acerca de</strong>
			<ul>
				<li><a href="'.site_url().'/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano" title="Conoce más sobre el proyecto">Acerca de Mozilla Hispano</a></li>
				<li><a href="'.site_url().'/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano#Contacto" title="Contacta con el proyecto">Contacto</a></li>
				<li><a href="'.site_url().'/documentacion/Colabora" title="Colabora con el proyecto">Colabora</a></li>
				<li><a href="'.site_url().'/comunidad/">Comunidad</a></li>
				<li><a href="'.site_url().'/marca/">Uso de marca y logos</a></li>
			</ul>
		</div>
		
		<div class="c1">
			<strong>Comunidad</strong>
			<ul>
				<li><a href="'.site_url().'/">Noticias</a></li>
				<li><a href="'.site_url().'/foro/">Foro</a></li>
				<li><a href="'.site_url().'/documentacion/">Documentación</a></li>
				<li><a href="'.site_url().'/planet/">Planet</a></li>
				<li><a href="'.site_url().'/labs/">Labs</a></li>
				<li><a href="'.site_url().'/difusion/">Difusión</a></li>
			</ul>
		</div>
		
		<div class="c1">
			<strong>Webs Mozilla</strong>
			<ul>
				<li><a href="http://addons.mozilla.org/es-ES/">Mozilla Addons</a></li>
				<li><a href="http://developer.mozilla.org/es">Mozilla Developer Network</a></li>
				<li><a href="http://www.mozilla.org/es-ES/newsletter/">Boletín Firefox</a></li>
				<li><a href="http://input.mozilla.com/es/">Firefox Input</a></li>
			</ul>
		</div>
		
		<div class="c1">
			<strong>Únete</strong>
			<ul>
				<li><a href="'.site_url().'/documentacion/Difusi%C3%B3n">Difusión</a></li>
				<li><a href="'.site_url().'/documentacion/Asistencia">Asistencia</a></li>
				<li><a href="'.site_url().'/documentacion/Noticias">Noticias</a></li>
				<li><a href="'.site_url().'/documentacion/Localizaci%C3%B3n">Localización</a></li>
                <li><a href="'.site_url().'/documentacion/Labs">Labs</a></li>
                <li><a href="'.site_url().'/documentacion/Control_de_calidad">Control de calidad</a></li>               
				<li><a href="'.site_url().'/documentacion/Administraci%C3%B3n_t%C3%A9cnica">Adm. Técnica</a></li>
			</ul>
		</div>
	</div>
</div>
';

$temp.=<<<PINTA
<script type='text/javascript' src='https://www.mozilla.org/tabzilla/media/js/tabzilla.js'></script>
<script type='text/javascript'>var tab=document.createElement('a');tab.href="https://www.mozilla.org/";tab.id="tabzilla";
tab.innerHTML="mozilla";var tullido=document.getElementById('tullido');tullido.insertBefore(tab,tullido.firstChild);</script>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-2846159-1");
pageTracker._initData();
pageTracker._trackPageview();
</script>
PINTA;

return $temp;
}

function pintaCss()
{
$temp= '<link type="image/x-icon" href="'.site_url().'/favicon.ico" rel="shortcut icon" />';
$temp.= <<<PINTA
	<link rel="alternate" type="application/rss+xml" title="Noticias de Mozilla Hispano" href="http://feeds.mozilla-hispano.org/mozillahispano" />
	<link rel="alternate" type="application/rss+xml" title="Artículos en Planet Mozilla Hispano" href="http://feeds.mozilla-hispano.org/mozillahispano-planet" />
    	<link rel="alternate" type="application/rss+xml" title="El Podcast de Mozilla Hispano" href="http://feeds.mozilla-hispano.org/mozillahispano-podcast" />
PINTA;

$temp.='	
	<link rel="search" type="application/opensearchdescription+xml" title="Mozilla Hispano - Noticias" href="'.site_url().'/archivos/noticias.xml" />
	<link rel="search" type="application/opensearchdescription+xml" title="Mozilla Hispano - Foro" href="'.site_url().'/archivos/foro.xml" />
	<link rel="search" type="application/opensearchdescription+xml" title="Mozilla Hispano - Documentación" href="'.site_url().'/archivos/documentacion.xml" />

	<link type="text/css" rel="stylesheet" href="https://www.mozilla.org/tabzilla/media/css/tabzilla.css"  />
	<link rel="stylesheet" href="'.site_url().'/wp-content/themes/mozillahispano2/css/comun.css" type="text/css" />
';

return $temp;
}
function pintaJs()
{
$temp='
<script type="text/javascript" src="'.site_url().'/wp-content/themes/mozillahispano2/js/menu.js"></script>
';
return $temp;
}
// mozeuChooseRightLocale()
// Author: Pascal Chevrel
// Date : 2007-08_09
// Modification: Nukeador (30/09/07)
// Modification: stripTM (16/07/09) add es-mx & es-cl
// Modification: stripTM (13/08/11) Parche añadido para el enlace de thunderbird
// Modification: stripTM (20/08/11) Mozilla ya hace la detección de idioma, desde Mozilla nos recomiendan el uso de esas urls, el las páginas que se cacheaban en el servidor teníamos el problema que el primero que visitaba la página era el que establecía el idioma a los que venian despues. Se suprime desde el widget de WP la llamada a esta función ya que al ser fija sólo supone una carga extra al servidor.
// Description: Analyses visitors accept-language HTTP header and chooses the right link for the product
// Fallback is : es-es (Español de españa)
// Requires the name of the product ($product), type : string
// It prints a string with the download link
function mozeuChooseRightLocale($product){
	switch($product) {
		case 'firefox':
			echo 'http://www.mozilla.org/firefox/'; /* Sería más limpio un return, pero por compatibilidad se mantiene el echo */
			break;
		case 'thunderbird':
			echo 'http://www.mozilla.org/thunderbird/';
			break;
		default:
			echo '#';
			break;
	}
}
function mozeuChooseRightLocale_old($product){
	if ($product == 'thunderbird') {
		echo ('http://www.mozilla.org/thunderbird/');
		return ('http://www.mozilla.org/thunderbird/'); /* Debería devolver el string en lugar de pintarlo **/
	}
	//Default values
	$l = 'es';
	$link = "http://www.mozilla-europe.org/es/$product";

	if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$acclang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		for ($i = 0; $i < count($acclang); ++$i) {
			$L = explode(';', $acclang[$i]);
			$locale = trim($L[0]);
			if (($locale == 'es-ar') || ($locale == 'es-uy')){
				// Is it form Argentina or Uruguay?
				$locale = 'ar';
				$link = "http://www.mozilla.com/es-AR/$product/all.html#es-AR";
			}
			if (($locale == 'es-mx')){
				// México
				$locale = 'mx';
				$link = "http://www.mozilla.com/es-MX/$product/all.html#es-MX";
			}
			if (($locale == 'es-cl')){
				// Chile
				$locale = 'cl';
				$link = "http://www.mozilla.com/es-CL/$product/all.html#es-CL";
			}
			$l = $locale;
			break;
		}
	}
echo $link;
} // end mozeuChooseRightLocale

?>
