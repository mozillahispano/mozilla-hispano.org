<?php
$count = 0;
$today = Array();
$week = Array();
$month = Array();
$older = Array();
$now = time();

foreach ($items as $item) {
    $age = ($now - $item->get_date('U')) / (60*60*24);
    if ($age < 1) {
        $today[] = $item;
    } elseif ($age < 7) {
        $week[] = $item;
    } elseif ($age < 30) {
        $month[] = $item;
    } else {
        $older[] = $item;
    }
}

header('Content-type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
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
    <div id="lienzo">
	<div id="tullido">
		<?php include(dirname(__FILE__).'/top.tpl.php'); ?>
		
		<div id="cuerpo" class="clearfix">
			<div id="contenido">
				<div id="main-content">
			    <div id="back-link"><p><a href="/planet">Portada de <?php echo $PlanetConfig->getName(); ?></a> > Archivos</p></div>
			    <?php if (0 == count($items)) :?>
			    <div class="portada-individual">
				<h2 class="post-title">Sin artículos</h2>
				<div class="texto-portada-individual">
				    <p class="article-content">Ahora mismo no hay artículos.</p>
				</div>
			    </div>
			    <?php endif; ?>
			    <?php if (count($today)): ?>
			    <div class="portada-individual">
				<h2>Hoy</h2>
				<div class="texto-portada-individual">
					<ul>
					<?php foreach ($today as $item): ?>
					    <?php $feed = $item->get_feed(); ?>
					    <li>
					    <?php echo $feed->getName() ?> : 
					    <a href="<?php echo $item->get_permalink(); ?>" title="Ir a la web de origen"><?php echo $item->get_title(); ?></a>
					    </li>
					<?php endforeach; ?>
					</ul>
				</div>
			    </div>
			    <?php endif; ?>
			    
			    <?php if (count($week)): ?>
			    <div class="portada-individual">
				<h2>Esta semana</h2>
				<div class="texto-portada-individual">
					<ul>
					<?php foreach ($week as $item): ?>
					    <?php $feed = $item->get_feed(); ?>
					    <li>
					    <?php echo $feed->getName() ?> : 
					    <a href="<?php echo $item->get_permalink(); ?>" title="Ir a la web de origen"><?php echo $item->get_title(); ?></a>
					    </li>
					<?php endforeach; ?>
					</ul>
				</div>
			    </div>
			    <?php endif; ?>
			    
			    <?php if (count($month)): ?>
			    <div class="portada-individual">
				<h2>Este mes</h2>
				<div class="texto-portada-individual">
					<ul>
					<?php foreach ($month as $item): ?>
					    <?php $feed = $item->get_feed(); ?>
					    <li>
					    <?php echo $feed->getName() ?> : 
					    <a href="<?php echo $item->get_permalink(); ?>" title="Ir a la web de origen"><?php echo $item->get_title(); ?></a>
					    </li>
					<?php endforeach; ?>
					</ul>
				</div>
			    </div>
			    <?php endif; ?>
			    
			    <?php if (count($older)): ?>
			    <div class="portada-individual">
				<h2>Elementos más antiguos</h2>
				<div class="texto-portada-individual">
					<ul>
					<?php foreach ($older as $item): ?>
					    <?php $feed = $item->get_feed(); ?>
					    <li>
					    <?php echo $feed->getName() ?> : 
					    <a href="<?php echo $item->get_permalink(); ?>" title="Ir a la web de origen"><?php echo $item->get_title(); ?></a>
					    </li>
					<?php endforeach; ?>
					</ul>
				</div>
			    </div>
			    <?php endif; ?>
			</div> <!-- main-content -->
			<?php include_once(dirname(__FILE__).'/sidebar.tpl.php'); ?>
			</div>
			</div><!-- contenido -->
			</div><!-- Cuerpo -->
	</div><!-- tullido -->
        
        <?php include(dirname(__FILE__).'/footer.tpl.php'); ?>
    </div>
    
    <script src="app/js/mm.js" type="text/javascript"></script>
</body>
</html>
