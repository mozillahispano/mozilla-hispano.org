<?php
$all_people = &$Planet->getPeople();
usort($all_people, array('PlanetPerson', 'compare'));
?>
<div id="barra-small">
	<div id="sidebar-people">
		<h3>Gente (<?php echo count($all_people); ?>)</h3>
		<div class="cajacontenido">
	<ul>
			<?php foreach ($all_people as $person) : ?>
			<li>
				<a href="<?php echo htmlspecialchars($person->getFeed(), ENT_QUOTES, 'UTF-8'); ?>" title="Feed"><img src="postload.php?url=<?php echo urlencode(htmlspecialchars($person->getFeed(), ENT_QUOTES, 'UTF-8')); ?>" alt="" height="12" width="12" /></a>
				<a href="<?php echo $person->getWebsite(); ?>" title="Web de origen"><?php echo htmlspecialchars($person->getName(), ENT_QUOTES, 'UTF-8'); ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>

		<h3>Twitter</h3>
		<div>
			<script src="http://widgets.twimg.com/j/2/widget.js"></script>
			<script>
							new TWTR.Widget({
								version: 2,
								type: 'search',
								search: '#mozillahispano',
								interval: 6000,
								title: 'Mozilla Hispano',
								subject: '#mozillahispano',
								width: 220,
								height: 600,
								theme: {
							shell: {
								background: 'transparent',
								color: '#444444'
							},
							tweets: {
								background: '#ffffff',
								color: '#444444',
								links: '#264373'
							}
								},
								features: {
							scrollbar: true,
							loop: false,
							live: true,
							hashtags: false,
							timestamp: true,
							avatars: true,
							toptweets: false,
							behavior: 'all'
								}
							}).render().start();
			</script>
		</div>


<!--
		<h3>Últimas fotos</h3>
		<div class="cajacontenido">
			<div id="fotos">
				<?php
				if (false) { // Desactivamos las fotos de flickr porque la biblioteca no funciona en php 7
					require_once("../eventos/flickr/phpFlickr.php"); //Incluyendo el API de Flickr
					$f = new phpFlickr("03e1411633d36816cc74fb82204549ec"); //Clase de Api, conseguir en: http://www.flickr.com/services/api/keys/
					$nsid = ""; //NSID Usuario, conseguir en: http://idgettr.com/
					//Incluir tag, ordenamieno, privacidad, y numero de imagenes a mostrar
					$photos = $f->photos_search(array("tags"=>"mozilla-hispano", "user_id"=>$nsid, "sort"=>"date-posted-desc", "privacy_filter"=>"1", "per_page"=>"10"));
					$url	= "http://www.flickr.com/photos/".$photo['id']."/"; //Url de la Imgen Original
					if (is_array($photos['photo']))
					{
						echo "<div><ul>";
						$sw= 1;
						foreach ($photos['photo'] as $photo)
						{
							if ($sw == 1)
							{
								$salida = "<li class='foto'>";
								$salida .= "<a href='".$f->buildPhotoURL($photo, "medium")."' title='".$photo['title']."' class='thickbox' rel='mozilla-hispano'><img alt='".$photo['title']."' title='".$photo['title']."' "."src='".$f->buildPhotoURL($photo, "square")."' /></a>";
								$sw=0;
							}
							else
							{
								$salida .= "<a href='".$f->buildPhotoURL($photo, "medium")."' title='".$photo['title']."' class='thickbox' rel='mozilla-hispano'><img alt='".$photo['title']."' title='".$photo['title']."' "."src='".$f->buildPhotoURL($photo, "square")."' /></a>";
								echo $salida."</li>";
								$sw=1;
							}
						}

						echo "</ul></div>";
					}
				}
				?>
			</div><
			<p class="all-photos"><a href="http://api.flickr.com/services/feeds/photos_public.gne?tags=mozilla-hispano&amp;lang=es-us&amp;format=rss_200"><img width="12" height="12" alt="" src="postload.php?url=http://api.flickr.com/services/feeds/photos_public.gne?tags=mozilla-hispano&amp;lang=es-us&amp;format=rss_200"/> RSS de las fotos</a></p>
			<p class="all-photos"><a href="http://www.flickr.com/photos/tags/mozilla-hispano/">Ver todas las fotos</a></p>

		</div>
-->

		<h3>RSS</h3>
		<div class="cajacontenido">
				<ul>
						<li><img src="custom/img/feed.png" alt="feed" height="12" width="12" />&nbsp;<a href="http://feeds.mozilla-hispano.org/mozillahispano-planet">Canal RSS del Planet</a></li>
						<li><img src="custom/img/opml.png" alt="feed" height="12" width="12" /> <a href="custom/people.opml">Todos los orígenes en formato OPML</a></li>
				</ul>
		</div>



		<h3>Archivos</h3>
		<div class="cajacontenido">
			<ul>
					<li><a href="?type=archive">Ver todos los artículos</a></li>
			</ul>
		</div>

</div>
