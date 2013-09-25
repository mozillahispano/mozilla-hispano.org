jQuery(document).ready(function(){
	   		var b='';
			jQuery.each(jQuery.browser, function(i, val) {
	   			if (i!='mozilla' && val==true){
	   				jQuery(".enlace_xpi").click(function() {
						alert("Necesitas Mozilla Firefox para instalar esta extensión");
						return false;
					});
					
					// Mostramos la caja de aviso a todos los navegadores
					jQuery("#ie_warn").css("display", "block");
	   			}
			});
			
			// Efectos para las cajas de los temas de TFox
			jQuery(".page-template-labs-tfox-themes-php .theme p.actions").hide();
			
			jQuery(".page-template-labs-tfox-themes-php .theme").hover(
				function() {
					jQuery(this).children("p.actions").fadeIn("fast");
				},
				function() {
					jQuery(this).children("p.actions").fadeOut("fast");
				}
			);
	});

// Función para enviar los temas a TFo
function installTheme(elm){
   var evt = document.createEvent("Events");  
   evt.initEvent("tfox-install-theme", true, false);
   elm.dispatchEvent(evt);
}
