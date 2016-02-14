<?php
	ini_set('display_errors', "1");
	ini_set('error_reporting', E_ALL ^ E_NOTICE);
	$limit = $PlanetConfig->getMaxDisplay();
	$count = 0;
	header('Content-type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta http-equiv="Content-Style-Type" content="text/css" />

    <title><?php echo $PlanetConfig->getName(); ?></title>
    <?php include(dirname(__FILE__).'/head.tpl.php'); ?>
</head>

<body>
    <script type="text/javascript">
    document.body.className += 'js';
    </script>
    <div id="lienzo">
	<div id="tullido">
		<?php include(dirname(__FILE__).'/top.tpl.php'); ?>
		<div id="cuerpo" class="clearfix">
		    <div id="contenido" class="planet">
				<div id="main-content">
					<div id="texto-bienvenida">
						<p>Bienvenido a Planet Mozilla Hispano, en esta sección se listan los últimos artículos en los blogs, twitts y fotos de los miembros y colaboradores de la comunidad de Mozilla en español. Mozilla Hispano no es responsable de las opiniones de los autores en sus blogs personales.</p>
					</div>

					<?php if (0 == count($items)) : ?>
						<div class="portada-individual">
							<h2 class="post-title">
								Sin artículos
							</h2>
							<p class="article-content">Ahora mismo no hay artículos.</p>
						</div>
					<?php else : ?>
						<?php foreach ($items as $item): ?>
							<?php
							$arParsedUrl = parse_url($item->get_feed()->get_link());
							$host = preg_replace('/[^a-zA-Z]/i', '-', $arParsedUrl['host']);
							?>
							<div class="portada-individual <?php echo $host; ?>">
								<h2 class="post-title">
									<a href="<?php echo $item->get_permalink(); ?>" title="Ir al artículo original"><?php echo $item->get_title(); ?></a>
								</h2>
								<p class="dia-publicacion">

									<?php echo ($item->get_author()? $item->get_author()->get_name() : 'Anónimo'); ?>,
									<?php
									$ago = time() - $item->get_date('U');
									//echo '<span title="'.Duration::toString($ago).' ago" class="date">'.date('d/m/Y', $item->get_date('U')).'</span>';
									echo '<span id="post'.$item->get_date('U').'" class="date">'.$item->get_date('d/m/Y').'</span>';
									?>

									|

									Origen: <?php
									$feed = $item->get_feed();
									echo '<a href="'.$feed->getWebsite().'" class="source">'.$feed->getName().'</a>';
									?>
								</p>
								<div class="texto-portada-individual">
									<?php echo $item->get_content(); ?>
								</div>
							</div>
							<?php if (++$count == $limit) { break; } ?>
						<?php endforeach; ?>
					<?php endif; ?>
						<p id="more-link"><a href="?type=archive">Ver artículos más antiguos</a></p>
					</div>

					<?php include_once(dirname(__FILE__).'/sidebar.tpl.php'); ?>
				</div> <!-- contenido -->
		</div><!-- Cuerpo -->
	</div><!-- tullido -->
	</div><!-- lienzo -->
        <?php include(dirname(__FILE__).'/footer.tpl.php'); ?>

    <script src="app/js/mm.js" type="text/javascript"></script>
</body>
</html>
