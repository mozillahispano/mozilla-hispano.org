BARRA = {
	inicio : function() {
		/*jQuery("#menu")
			.append('<div id="ocultaBarra">esconder barra</div>')
			.find("#ocultaBarra")
				.bind("click", BARRA.interruptor);*/
		if 
(jQuery("body").attr("class").search(/(^|\s)page-Portada|page-Importar_un_certificado|page-Organización_de_Mozilla_Hispano|page-Selecciona_tus_exploradores_web_no_es_un_virus|page-Colabora|page-Programa_de_mentores|page-Difusión_Presentaciones(\s|jQuery)/) 
!= -1) {
			/* Si se trata de la portada o de un articulo concreto ocultamos la barra pero sin guardarlo en la cookie */
			BARRA.muestraBarra(false);
		}
		else
			if (jQuery.cookie("barraOculta") == "s") {
				/* Si prefiere la barra oculta le damos al interruptor para que pase de visible a oculta */
				BARRA.interruptor();
			}
	},
	interruptor : function() {
		if (jQuery("#barra-small:visible").length) {
			BARRA.muestraBarra(false);
			jQuery.cookie("barraOculta", "s", { path: '/' });
		}
		else {
			BARRA.muestraBarra(true);
			jQuery.cookie("barraOculta", "n", { path: '/' });
		}
	},
	muestraBarra : function(bEstado) {
		if (bEstado) {
			jQuery("#barra-small").show("normal");
			jQuery("#cont_wiki").removeClass("todoAncho");
			jQuery("#menu #ocultaBarra").text("esconder barra");
		}
		else {
			jQuery("#barra-small").hide("normal");
			jQuery("#cont_wiki").addClass("todoAncho");
			jQuery("#menu #ocultaBarra").text("mostrar barra");
		}
	}
}
jQuery(document).ready(BARRA.inicio);
if(jQuery.cookie("barraOculta") == "s") {
	document.documentElement.className += " barraDocHides";
}
