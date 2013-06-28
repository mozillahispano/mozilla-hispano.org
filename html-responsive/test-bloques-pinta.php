<?php
require ('../wp-content/themes/mozillahispano2/cabecera-pie.php');
$cabecera = pintaCabecera();
$pie = pintaPie();
$js = pintaJs();
$css = pintaCss();

?><!doctype html>
<html lang="es" dir="ltr">
<head>
	<meta charset="utf-8"/>
	<?=$css?>
</head>
<body>
	<div id="lienzo">
		<div id="tullido">
			<a href="http://www.mozilla.org" id="tabzilla">Mozilla</a><!-- quitar cuando se meta en cabecera -->
			<?=$cabecera?>
			<div id="cuerpo">
				<div id="contenido">Contenido</div>
				<div id="barra">Barra</div>
			</div>
		</div>
		<?=$pie?>
	</div>
	<?=$js?>
</body>
</html>