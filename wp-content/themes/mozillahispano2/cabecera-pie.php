<?php
function pintaCabecera() {
	return file_get_contents(__DIR__.'/tpl/cabecera.tpl');
}

function pintaPie() {
	return file_get_contents(__DIR__.'/tpl/pie.tpl');
}

function pintaCss() {
	return file_get_contents(__DIR__.'/tpl/css.tpl');
}
function pintaJs() {
	return file_get_contents(__DIR__.'/tpl/js.tpl');
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