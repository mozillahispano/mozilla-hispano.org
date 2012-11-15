<?php
/**
 * bookjive nouveau
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/** */
require_once('includes/SkinTemplate.php');
// Archivo con la cabecera y pie común
include("../wp-content/themes/mozillahispano2/cabecera-pie.php");

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
/*class Skinbookjive extends SkinTemplate {
	/** Using bookjive. 
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'bookjive';
		$this->stylename = 'bookjive';
		$this->template  = 'bookjiveTemplate';
	}
}*/

class Skinbookjive extends SkinTemplate {
	/** Using monobook. */
	var $skinname = 'bookjive', $stylename = 'bookjive',
		$template = 'bookjiveTemplate';
}

/**
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class bookjiveTemplate extends QuickTemplate {
	/**
	 * Template filter callback for bookjive skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgUser;
		$skin = $wgUser->getSkin();

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
  <head>
    <meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
    <?php $this->html('headlinks') ?>
    <title><?php $this->text('pagetitle') ?></title>
    <style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css?20111201"; /*]]>*/</style>



	<?php echo pintaCss(); ?>

    <link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('stylepath') ?>/common/commonPrint.css" />
    <!--[if lt IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE50Fixes.css";</style><![endif]-->
    <!--[if IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE55Fixes.css";</style><![endif]-->
    <!--[if gte IE 6]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE60Fixes.css";</style><![endif]-->
    <!--[if IE]><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/IEFixes.js"></script>
    <meta http-equiv="imagetoolbar" content="no" /><![endif]-->
	<?php print Skin::makeGlobalVariablesScript( $this->data ); ?>
	<!-- Css links -->
	<?php $this->html('csslinks') ?>

		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
<?php	if($this->data['jsvarurl'  ]) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl'  ) ?>"><!-- site js --></script>
<?php	} ?>
<?php	if($this->data['pagecss'   ]) { ?>
		<style type="text/css"><?php $this->html('pagecss'   ) ?></style>
<?php	}
		if($this->data['usercss'   ]) { ?>
		<style type="text/css"><?php $this->html('usercss'   ) ?></style>
<?php	}
		if($this->data['userjs'    ]) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
<?php	}
		if($this->data['userjsprev']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
<?php	}
		if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>

		<!-- Semantic mediawiki -->
		<link rel="stylesheet" type="text/css" href="/documentacion/extensions/SemanticMediaWiki/skins/SMW_custom.css" />
		<script type="text/javascript" src="/documentacion/extensions/SemanticMediaWiki/skins/SMW_sorttable.js"></script>
		
		<!-- Head Scripts -->
		<?php $this->html('headscripts') ?>
		
		<?php echo pintaJs(); ?>
  </head>
  <body <?php if($this->data['body_ondblclick']) { ?>ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload'    ]) { ?>onload="<?php     $this->text('body_onload')     ?>"<?php } ?>
 class="mediawiki <?php $this->text('nsclass') ?> <?php $this->text('dir') ?> <?php $this->text('pageclass') ?>">

<div id="lienzo">
	<div id="tullido">
		<?php echo pintaCabecera(); ?>
		<div id="cuerpo" class="clearfix">
			<div id="contenido">
							<div id="cont_wiki">
								<div id="column-content">
									<div id="content">
										<a id="contentTop"></a>

										<div id="p-cactions">
										<ul>
											<?php			foreach($this->data['content_actions'] as $key => $tab) { ?>
											<li id="ca-<?php echo Sanitizer::escapeId($key) ?>"<?php
											if($tab['class']) { ?> class="<?php echo htmlspecialchars($tab['class']) ?>"<?php }
											?>><a href="<?php echo htmlspecialchars($tab['href']) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('ca-'.$key) ?>><?php
											echo htmlspecialchars($tab['text']) ?></a> </li>
											<?php			 } ?>
										</ul>
									</div>

									<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
									<h2 class="firstHeading"><?php $this->text('title') ?></h2>

									<div id="bodyContent">

										<h3 id="siteSub"><?php $this->msg('tagline') ?></h3>
										<div id="contentSub"><?php $this->html('subtitle') ?></div>
										<?php if($this->data['undelete']) { ?><div id="contentSub"><?php     $this->html('undelete') ?></div><?php } ?>
										<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>

										<?php $this->html('bodytext') ?>
										
										<?php if($this->data['catlinks']) { ?><div id="catlinks"><?php       $this->html('catlinks') ?></div><?php } ?>
										<!-- end content -->
										<div class="visualClear"></div>
		  						</div> <!-- bodycontent -->
								</div> <!-- content -->

	      				<div id="footer">

									<!-- <?php if($this->data['copyrightico']) { ?><div id="f-copyrightico"><?php $this->html('copyrightico') ?></div><?php } ?> -->

		  						<?php if($this->data['lastmod'   ]) { ?><?php    $this->html('lastmod')    ?> - <?php } ?>
		  						<?php if($this->data['viewcount' ]) { ?><?php  $this->html('viewcount')  ?> - <?php } ?>
		  						<?php if($this->data['numberofwatchingusers' ]) { ?><?php  $this->html('numberofwatchingusers') ?> - <?php } ?>
									<!--
										  <?php if($this->data['credits'   ]) { ?><?php    $this->html('credits')    ?> - <?php } ?>
										  <?php if($this->data['about'     ]) { ?><?php      $this->html('about')      ?> - <?php } ?>
										  <?php if($this->data['disclaimer']) { ?><?php $this->html('disclaimer') ?> - <?php } ?>
										  <?php if($this->data['tagline']) { ?><?php echo $this->data['tagline'] ?> - <?php } ?>
									-->
					      </div>
	      			</div> <!-- column content -->
	      			</div>
	      			<div id="barra-small">
								  <!--
								  <div class="portlet" id="p-logo">
								  <a style="background-image: url(<?php $this->text('logopath') ?>);"
									href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>"
									title="<?php $this->msg('mainpage') ?>"></a>
									</div>
									-->
									<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>

									<div id="buscador" class="portlet">
										<div id="searchBody" class="pBody">
											<!-- <form action="<?php $this->text('searchaction') ?>"> y el name del input era search -->
											<form action="/buscar.php">
											<div>
												<input name="donde" type="hidden" value="docu" />
												<input name="desde" type="hidden" value="docu" />
												<input class="text" id="q" name="q" type="text" <?php
													if($this->haveMsg('accesskey-search')) {
														?>accesskey="<?php $this->msg('accesskey-search') ?>"<?php }
													if( isset( $this->data['search'] ) ) {
														?> value="<?php $this->text('search') ?>"<?php } ?> />
												<!--<input type='submit' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('go') ?>" />&nbsp;-->
												<input type='submit' name="fulltext" class="submit" value="<?php $this->msg('search') ?>" />
											</div>
											</form>
										</div>
									</div>
                                <?php foreach ($this->data['sidebar'] as $bar => $cont) { ?>
                                
                                <div class='portlet caja' id='p-<?php echo htmlspecialchars($bar) ?>'>
								  <h3><span><?php $out = wfMsg( $bar ); if (wfEmptyMsg($bar, $out)) echo $bar; else echo $out; ?></span></h3>
								  <div class='pBody'>
									<ul class="cajacontenido">
							<?php 			foreach($cont as $key => $val) { ?>
											<li id="<?php echo htmlspecialchars($val['id']) ?>"<?php
												if ( $val['active'] ) { ?> class="active" <?php }
											?>><a href="<?php echo htmlspecialchars($val['href']) ?>"><?php echo htmlspecialchars($val['text']) ?></a></li>
							<?php			} ?>
									</ul>
								  </div>
							    </div>
				<?php } ?>

				<!--div id="p-cactions" class="portlet">
				  <h5><?php $this->msg('views') ?></h5>
				  <ul>
					<?php foreach($this->data['content_actions'] as $key => $action) {
					   ?><li id="ca-<?php echo htmlspecialchars($key) ?>"
					   <?php if($action['class']) { ?>class="<?php echo htmlspecialchars($action['class']) ?>"<?php } ?>
					   ><a href="<?php echo htmlspecialchars($action['href']) ?>"><?php
					   echo htmlspecialchars($action['text']) ?></a></li><?php
					 } ?>
				  </ul>
				</div-->
				<div class="portlet caja" id="p-personal">


				 <!-- p-personal -->
				  <h3><span><?php $this->msg('personaltools') ?></span></h3>
				  <div class="pBody">
					<ul class="cajacontenido">
					<?php foreach($this->data['personal_urls'] as $key => $item) {
					   ?><li id="pt-<?php echo htmlspecialchars($key) ?>"><a href="<?php
					   echo htmlspecialchars($item['href']) ?>"<?php
					   if(!empty($item['class'])) { ?> class="<?php
					   echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
					   echo htmlspecialchars($item['text']) ?></a></li><?php
					} ?>
					</ul>
				</div>
				</div>

				<div class="portlet caja" id="p-tb">
				  <h3><span><?php $this->msg('toolbox') ?></span></h3>
				  <div class="pBody">
					<ul class="cajacontenido">
					  <?php if($this->data['notspecialpage']) { foreach( array( 'whatlinkshere', 'recentchangeslinked' ) as $special ) { ?>
					  <li id="t-<?php echo $special?>"><a href="<?php
						echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
						?>"><?php echo $this->msg($special) ?></a></li>
					  <?php } } ?>
					  <?php if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
					  <li id="t-trackbacklink"><a href="<?php
						echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
						?>"><?php echo $this->msg('trackbacklink') ?></a></li>
					  <?php } ?>
					  <?php if($this->data['feeds']) { ?><li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="feed-<?php echo htmlspecialchars($key) ?>"><a href="<?php
					echo htmlspecialchars($feed['href']) ?>"><?php echo htmlspecialchars($feed['text'])?></a> </span>
					<?php } ?></li><?php } ?>
					  <?php foreach( array('contributions', 'emailuser', 'upload', 'specialpages') as $special ) { ?>
					  <?php if($this->data['nav_urls'][$special]) {?><li id="t-<?php echo $special ?>"><a href="<?php
					echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
					?>"><?php $this->msg($special) ?></a></li><?php } ?>
					  <?php } ?>
					  <?php if(!empty($this->data['nav_urls']['print']['href'])) { ?>
					  <li id="t-print"><a href="<?php
						echo htmlspecialchars($this->data['nav_urls']['print']['href'])
						?>"><?php echo $this->msg('printableversion') ?></a></li>
					  <?php } ?>
					</ul>
				  </div>
				</div>
				<?php if( $this->data['language_urls'] ) { ?><div id="p-lang" class="portlet">
				  <h5><?php $this->msg('otherlanguages') ?></h5>
				  <div class="pBody">
					<ul>
					  <?php foreach($this->data['language_urls'] as $langlink) { ?>
					  <li class="<?php echo htmlspecialchars($langlink['class'])?>">
					  <a href="<?php echo htmlspecialchars($langlink['href'])
					?>"><?php echo $langlink['text'] ?></a>
					  </li>
					  <?php } ?>
					</ul>
				  </div>
				</div>
				<?php } ?>

				<h3>Comprueba tus plugins</h3>
				<p><a href="http://www.mozilla.com/plugincheck/"><img src="https://support.mozilla.org/media/img/promo.plugins.png" alt="Plugins" /></a></p>
				<p>¿Qué es un plugin? ¿Por qué debo mantenerlos actualizados? Responde a estas preguntas e inicia una verificación instantánea para comprobar su actualización.</p>
				<p><a href="http://www.mozilla.com/plugincheck/">Más información »</a></p>

				</div>
	      		</div><!-- contenido -->
		</div><!-- Cuerpo -->
	</div><!-- tullido -->
	<?php echo pintaPie(); ?>

	</div>
    <?php $this->html('reporttime') ?>
    
	<?php $this->html( 'bottomscripts' ); /* JS call to runBodyOnloadHook */ ?>
	<!--<script type="text/javascript" src="/wp-content/themes/mozillahispano/js/jquery.js"></script>-->
	<!--<script type="text/javascript" src="/documentacion/extensions/SemanticForms/libs/jquery-1.4.2.min.js"></script>-->
	<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/barraHerramientas.js"></script>
	<!-- CMO Scripts-->
	<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/documentacionTopicbox.js"></script>
	<script type="text/javascript" src="/wp-content/themes/mozillahispano2/js/documentacion.js"></script>
  </body>
</html>
<?php
	wfRestoreWarnings();
	}
}
?>
