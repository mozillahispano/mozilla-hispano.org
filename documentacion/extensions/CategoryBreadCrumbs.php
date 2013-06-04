<?php

if ( ! defined( 'MEDIAWIKI' ) )
	die();

//--------------------------------------------------
// See http://www.leerwiki.nl for either updates
// or other extensions such as the Ajax Rating Script-,
// Image shadow- or EditpageMultipleInputboxes extension. 
// good luck with your Wiki! 
// B.Vahrmeijer
//----------------------------------------------------

$wgExtensionCredits['parserhook'][] = array(
'name' => 'Category Breadcrumb 24-07-2007',
'author' => 'Boudewijn Vahrmeijer',
'url' => 'http://www.leerwiki.nl/Hoofdpagina',
'version' => '1.11,1.10.1/1.9.3/1.9.2/1.8.2',
'description' => 'Category Breadcrumb for MediaWiki DMOZ style',
);

$wgUseCategoryBrowser = true;

// hook into Skintemplate.php
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = array("wfBreadCrumbsDisplay");

function wfBreadCrumbsDisplay(&$q,&$p) {
	global $wgOut,$wgArticle,$pathToRating;
	
	if ($wgArticle == null) return $out;	
	if ($wgArticle->getTitle()->mNamespace != 0) return  $out;

 
	// get category tree
	$tree=explode('<hr />',$q->getCategories());

	// kill the ugly category box below the page
    $p->set( 'catlinks', '');
    
	// set tree on top of text and register into $tpl
	$combine=$tree[1].$wgOut->mBodytext;
	$p->setRef( 'bodytext', $combine );

	return true;

}

?>
