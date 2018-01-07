<?php
function pintaCabecera()
{
$temp= <<<PINTA
<div id="cabecera">
				<div id="alojaLogo">
					<h1><a href="/">Mozilla hispano, tu comunidad en español de Mozilla</a></h1>
				</div>
				<div id="menu">
					<span id="toggle"></span>
					<ul id="menu-nav" class="clearfix">
						<li><a href="/">Noticias</a></li>
						<li><span>Asistencia</span>
							<ul class="submenu">
								<li><a href="https://support.mozilla.org/es/" title="Documentos, artículos y preguntas frecuentes de ayuda a los usuarios">Documentación</a></li>
								<li><a href="https://support.mozilla.org/es/questions/new" title="Haz una pregunta en el foro de asistencia">Haz una pregunta</a></li>
								<li><a href="/documentacion/Asistencia" title="Proyectos">Proyectos</a></li>
							</ul>
						</li>
						<li><span title="Ayuda a difundir Mozilla en español">Difusión</span>
							<ul class="submenu">
								<li><a href="/difusion/">Promociona Mozilla</a></li>
								<li><a href="https://foro.mozilla-hispano.org/c/difusion-eventos" title="Foro de difusión">Foros</a></li>
								<li><a href="/documentacion/Eventos" title="Eventos Mozilla">Eventos</a></li>
								<li><a href="/documentacion/Difusi%C3%B3n" title="Proyectos de difusión">Proyectos</a></li>
							</ul>
						</li>
						<li><a href="//foro.mozilla-hispano.org/" title="Foros de discusión">Foros</a></li>
						<li><span title="Desarrolla con Labs">Labs</span>
							<ul class="submenu">
								<li><a href="/labs/" title="Centro de desarrollo e innovación en la plataforma Mozilla">Blog y proyectos</a></li>
								<li><a href="https://foro.mozilla-hispano.org/c/labs">Foros</a></li>
							</ul>
						</li>
						<li><span>Comunidad</span>
							<ul class="submenu">
								<li><a href="/planet/" title="Artículos de opinión, mensajes y fotos de los miembros de la comunidad">Planet</a></li>
								<li><a href="/documentacion/Colaboradores" title="Listado de colaboradores">Colaboradores</a></li>
								<li><a href="/ar/">Argentina</a></li>
								<li><a href="/bo/">Bolivia</a></li>
								<li><a href="/co/">Colombia</a></li>
								<li><a href="/cr/">Costa Rica</a></li>
								<li><a href="/cl/">Chile</a></li>
								<li><a href="/cu/">Cuba</a></li>
								<li><a href="/es/">España</a></li>
								<li><a href="/ec/">Ecuador</a></li>
								<li><a href="/ni/">Nicaragua</a></li>
								<li><a href="/mx/">México</a></li>
								<li><a href="/py/">Paraguay</a></li>
								<li><a href="/pe/">Perú</a></li>
								<li><a href="/uy/">Uruguay</a></li>
								<li><a href="/ve/">Venezuela</a></li>
							</ul>
						</li>
						<li><span title="Únete y colabora con Mozilla Hispano">Participa</span>
							<ul class="submenu">
								<li><a href="/documentacion/Colabora">Cómo participar</a></li>
								<li><a href="/documentacion/Recursos_para_colaboradores" title="Recursos para colaboradores">Recursos para colaboradores</a></li>
								<li><a href="https://foro.mozilla-hispano.org/">Foro de discusión</a></li>
								<li><a href="/documentacion/Proyectos">Proyectos</a></li>
								<li><a href="/documentacion/Tareas">Tareas</a></li>
								<li><a href="/documentacion/Reuniones">Reuniones</a></li>
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
PINTA;
return $temp;
}

function pintaPie()
{
$temp= <<<PINTA
<div id="pie">
	<div id="pie-contenido">
		<div class="c2">
			<a href="/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano#Licencia" title="Licencia para el uso del contenido de este proyecto"><img src="/images/cc-by-sa.png" alt="cc-by-sa"/></a>
		</div>

		<div class="c1">
			<strong>Acerca de</strong>
			<ul>
				<li><a href="/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano" title="Conoce más sobre el proyecto">Acerca de Mozilla Hispano</a></li>
				<li><a href="/documentacion/Organizaci%C3%B3n_de_Mozilla_Hispano#Contacto" title="Contacta con el proyecto">Contacto</a></li>
				<li><a href="/documentacion/Colabora" title="Colabora con el proyecto">Colabora</a></li>
				<li><a href="/comunidad/">Comunidad</a></li>
				<li><a href="/marca/">Uso de marca y logos</a></li>
				<li><a href="https://www.mozilla.org/es-ES/about/governance/policies/participation/" title="Pautas de participación">Pautas de participación</a></li>
			</ul>
		</div>

		<div class="c1">
			<strong>Comunidad</strong>
			<ul>
				<li><a href="/">Noticias</a></li>
				<li><a href="https://foro.mozilla-hispano.org/">Foro de discusión</a></li>
				<li><a href="https://support.mozilla.org/es/">Documentación</a></li>
				<li><a href="/planet/">Planet</a></li>
				<li><a href="/labs/">Labs</a></li>
				<li><a href="/difusion/">Difusión</a></li>
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
				<li><a href="/documentacion/Difusi%C3%B3n">Difusión</a></li>
				<li><a href="/documentacion/Asistencia">Asistencia</a></li>
				<li><a href="/documentacion/Noticias">Noticias</a></li>
				<li><a href="/documentacion/Localizaci%C3%B3n">Localización</a></li>
                <li><a href="/documentacion/Labs">Labs</a></li>
                <li><a href="/documentacion/Control_de_calidad">Control de calidad</a></li>
				<li><a href="/documentacion/Administraci%C3%B3n_t%C3%A9cnica">Adm. Técnica</a></li>
			</ul>
		</div>
	</div>
</div>

<script type='text/javascript'>
var tab=document.createElement('div');
tab.id = "tabzilla";
var tab_a = document.createElement('a');
tab_a.href="https://www.mozilla.org/";
tab_a.textContent = "mozilla";
tab.appendChild(tab_a);
var tullido=document.getElementById('tullido');
tullido.insertBefore(tab,tullido.firstChild);
</script>

<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["setDomains", ["*.www.mozilla-hispano.org"]]);
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://stats.mozilla-hispano.org/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//stats.mozilla-hispano.org/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->

<script type="application/x-javascript" src="/wp-content/themes/mozillahispano2/js/labs_functions.js" ></script>
<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/menu.js"></script>
<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/responsive.js"></script>
<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/jquery.cookiebar.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery.cookieBar({});
	});

	// Forum migration warning
	if (document.URL.indexOf('/foro/') !== -1) {
		var dBody = document.querySelector('body');
		dBody.insertAdjacentHTML('afterbegin',
			'<div id="warning-foro" class="off">Este foro se guarda como histórico y no se pueden añadir más comentarios. Si aún quieres hacer una petición de ayuda accede a <a href="https://support.mozilla.org/es/">la plataforma de ayuda de Mozilla</a>. Si quieres participar en las discusiones y debates de la comunidad, <a href="http://foro.mozilla-hispano.org">accede a los nuevos foros de discusión</a>.</div>');
		// Slidein animation
		window.setTimeout(function() {
			document.getElementById("warning-foro").classList.remove("off");
		}, 500);
	}
</script>

PINTA;
return $temp;
}

function pintaCss()
{
$temp= <<<PINTA
	<link type="image/x-icon" href="/favicon.ico" rel="shortcut icon" />

	<link rel="alternate" type="application/rss+xml" title="Noticias de Mozilla Hispano" href="http://feeds.mozilla-hispano.org/mozillahispano" />
	<link rel="alternate" type="application/rss+xml" title="Artículos en Planet Mozilla Hispano" href="http://feeds.mozilla-hispano.org/mozillahispano-planet" />
	<link rel="alternate" type="application/rss+xml" title="El Podcast de Mozilla Hispano" href="http://feeds.mozilla-hispano.org/mozillahispano-podcast" />

	<link rel="search" type="application/opensearchdescription+xml" title="Mozilla Hispano - Noticias" href="/archivos/noticias.xml" />
	<link rel="search" type="application/opensearchdescription+xml" title="Mozilla Hispano - Documentación" href="/archivos/documentacion.xml" />

	<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/css/comun.css" type="text/css" />
	<link rel="stylesheet" href="/wp-content/themes/mozillahispano2/css/responsive.css" type="text/css" />

PINTA;
return $temp;
}
function pintaJs()
{
$temp= <<<PINTA
PINTA;
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

