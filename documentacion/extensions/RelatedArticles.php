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


//Based on DynamicPageList extension
// Contributors: n:en:User:IlyaHaykinson n:en:User:Amgine
// http://en.wikinews.org/wiki/User:Amgine
// http://en.wikinews.org/wiki/User:IlyaHaykinson
//http://meta.wikimedia.org/wiki/DynamicPageList


$wgDLPminCategories = 1;                // Minimum number of categories to look for
$wgDLPmaxCategories = 1;                // Maximum number of categories to look for
$wgDLPMinResultCount = 1;               // Minimum number of results to allow
$wgDLPMaxResultCount = 5;              // Maximum number of results to allow
$wgDLPAllowUnlimitedResults = false;     // Allow unlimited results
$wgDLPAllowUnlimitedCategories = false; // Allow unlimited categories


$wgExtensionCredits['parserhook'][] = array(
'name' => 'See Also Other Links Within Category 24-07-2007',
'author' => 'Boudewijn Vahrmeijer',
'url' => 'http://www.leerwiki.nl/Hoofdpagina',
'version' => '1.11,1.10.1/1.9.3/1.9.2/1.8.2',
'description' => 'At the bottom of article, display of links to similar Articles (within the category)',
);

$wgUseCategoryBrowser = true;

// hook into Skintemplate.php
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = array("wfSeeAlsoDisplay");

function wfSeeAlsoDisplay(&$q,&$p) {
	global $wgOut,$wgArticle,$wgMessageCache;
   
    $wgMessageCache->addMessages( array(
					'dynamicpagelist_toomanycats' => 'DynamicPageList: ¡Demasiadas categorías!',
					'dynamicpagelist_toofewcats' => 'DynamicPageList: ¡Muy pocas categorías!',
					'dynamicpagelist_noresults' => 'No hay artículos relacionados',
					'dynamicpagelist_noincludecats' => 'DynamicPageList: debes incluir al menos una categoría, o especificar un nombre',
					)
				  );
	
	if ($wgArticle == null) return true;	
	if ($wgArticle->getTitle()->mNamespace != 0) return  true;

 
	// get category tree
	$tree=explode('<hr />',$q->getCategories());

	// kill the ugly category box below the page
    $p->set( 'catlinks', '');
    
	// set tree on top of text and register into $tpl
	$cats=explode('Category:',$tree[1]);
	$cats2=$cats[count($cats)-1];
	$cats3=explode('">',$cats2);
	$input='category = Preguntas frecuentes sobre Firefox';
	$list=DynamicPageList($input);
	$seeAlso='<h3>Artículos relacionados</h3>';

	$combine=$wgOut->mBodytext.$seeAlso.$list;
	$p->setRef( 'bodytext', $combine );


	return true;

}

function DynamicPageList( $input ) {
       global $wgUser;
    global $wgLang;
    global $wgContLang;
    global $wgDLPminCategories, $wgDLPmaxCategories,$wgDLPMinResultCount, $wgDLPMaxResultCount;
    global $wgDLPAllowUnlimitedResults, $wgDLPAllowUnlimitedCategories;
     
    $aParams = array();
    $bCountSet = false;

    $sStartList = '<ul>';
    $sEndList = '</ul>';
    $sStartItem = '<li>';
    $sEndItem = '</li>';

    $sOrderMethod = 'categoryadd';
    $sOrder = 'descending';
    $sRedirects = 'exclude';

    $bNamespace = false;
    $iNamespace = 0;

    $bSuppressErrors = false;
    $bShowNamespace = true;
    $bAddFirstCategoryDate = false;
    
    $aCategories = array();
    $aExcludeCategories = array();

    $aParams = explode("\n", $input);

    $parser = new Parser;
    $poptions = new ParserOptions;

    foreach($aParams as $sParam)
    {
      $aParam = explode("=", $sParam);
      if( count( $aParam ) < 2 )
         continue;
      $sType = trim($aParam[0]);
      $sArg = trim($aParam[1]);
      if ($sType == 'category')
      {
        $title = Title::newFromText( $parser->transformMsg($sArg, $poptions) );
        if( is_null( $title ) )
          continue;
        $aCategories[] = $title; 
      }
      else if ($sType == 'notcategory')
      {
        $title = Title::newFromText( $parser->transformMsg($sArg, $poptions) );
        if( is_null( $title ) )
          continue;
        $aExcludeCategories[] = $title; 
      }
      else if ('namespace' == $sType)
      {
        $ns = $wgContLang->getNsIndex($sArg);
	if (NULL != $ns)
	{
	  $iNamespace = $ns;
	  $bNamespace = true;
	}
	else
	{
	  $iNamespace = intval($sArg);
	  if ($iNamespace >= 0)
	  {
	    $bNamespace = true;
	  }
	  else
	  {
	    $bNamespace = false;
	  }
	}
      }
      else if ('count' == $sType)
      {
        //ensure that $iCount is a number;
        $iCount = IntVal( $sArg );
        $bCountSet = true;
      }
      else if ('mode' == $sType)
      {
	switch ($sArg)
	{
	case 'none':
	  $sStartList = '';
	  $sEndList = '';
	  $sStartItem = '';
	  $sEndItem = '<br />';
	  break;
	case 'ordered':
	  $sStartList = '<ol>';
	  $sEndList = '</ol>';
	  $sStartItem = '<li>';
	  $sEndItem = '</li>';
	  break;
	case 'unordered':
	default:
	  $sStartList = '<ul>';
	  $sEndList = '</ul>';
	  $sStartItem = '<li>';
	  $sEndItem = '</li>';
	  break;
	}
      }
      else if ('order' == $sType)
      {
        switch ($sArg)
	{
	case 'ascending':
	  $sOrder = 'ascending';
	  break;
	case 'descending':
	default:
	  $sOrder = 'descending';
	  break;
	}
      }
      else if ('ordermethod' == $sType)
      {
	switch ($sArg)
	{
	case 'lastedit':
	  $sOrderMethod = 'lastedit';
	  break;
	case 'categoryadd':
	default:
	  $sOrderMethod = 'categoryadd';
	  break;
	}
      }
      else if ('redirects' == $sType)
      {
      	switch ($sArg)
      	{
      	case 'include':
      	  $sRedirects = 'include';
      	  break;
      	case 'only':
      	  $sRedirects = 'only';
      	  break;
      	case 'exclude':
      	default:
      	  $sRedirects = 'exclude';
      	  break;
      	}
      }
      else if ('suppresserrors' == $sType)
      {
	if ('true' == $sArg)
	  $bSuppressErrors = true;
	else
	  $bSuppressErrors = false;
      }
      else if ('addfirstcategorydate' == $sType)
      {
        if ('true' == $sArg)
          $bAddFirstCategoryDate = true;
        else
          $bAddFirstCategoryDate = false;
      }
      else if ('shownamespace' == $sType)
      {
	if ('false' == $sArg)
	  $bShowNamespace = false;
	else
	  $bShowNamespace = true;
      }
    }

    $iCatCount = count($aCategories);
    $iExcludeCatCount = count($aExcludeCategories);
    $iTotalCatCount = $iCatCount + $iExcludeCatCount;

    if ($iCatCount < 1 && false == $bNamespace)
    {
      if (false == $bSuppressErrors)
	return htmlspecialchars( wfMsg( 'dynamicpagelist_noincludecats' ) ); // "!!no included categories!!";
      else
	return '';
    }

    if ($iTotalCatCount < $wgDLPminCategories)
    {
      if (false == $bSuppressErrors)
	return htmlspecialchars( wfMsg( 'dynamicpagelist_toofewcats' ) ); // "!!too few categories!!";
      else
	return '';
    }

    if ( $iTotalCatCount > $wgDLPmaxCategories && !$wgDLPAllowUnlimitedCategories )
    {
      if (false == $bSuppressErrors)
	return htmlspecialchars( wfMsg( 'dynamicpagelist_toomanycats' ) ); // "!!too many categories!!";
      else
	return '';
    }

    if ($bCountSet)
    {
      if ($iCount < $wgDLPMinResultCount)
        $iCount = $wgDLPMinResultCount;
      if ($iCount > $wgDLPMaxResultCount)
        $iCount = $wgDLPMaxResultCount;
    }
    else
    {
      if (!$wgDLPAllowUnlimitedResults)
      {
        $iCount = $wgDLPMaxResultCount;
        $bCountSet = true;
      }
    }
    
    //disallow showing date if the query doesn't have an inclusion category parameter
    if ($iCatCount < 1)
      $bAddFirstCategoryDate = false;


    //build the SQL query
    $dbr =& wfGetDB( DB_SLAVE );
    $sPageTable = $dbr->tableName( 'page' );
    $categorylinks = $dbr->tableName( 'categorylinks' );
    $sSqlSelectFrom = "SELECT page_namespace, page_title, c1.cl_timestamp FROM $sPageTable";

    if (true == $bNamespace)
      $sSqlWhere = ' WHERE page_namespace='.$iNamespace.' ';
    else
      $sSqlWhere = ' WHERE 1=1 ';
      
    switch ($sRedirects)
    {
      case 'only':
        $sSqlWhere .= ' AND page_is_redirect = 1 ';
        break;
      case 'exclude':
        $sSqlWhere .= ' AND page_is_redirect = 0 ';
        break;
    }

    $iCurrentTableNumber = 0;

    for ($i = 0; $i < $iCatCount; $i++) {
      $sSqlSelectFrom .= " INNER JOIN $categorylinks AS c" . ($iCurrentTableNumber+1);
      $sSqlSelectFrom .= ' ON page_id = c'.($iCurrentTableNumber+1).'.cl_from';
      $sSqlSelectFrom .= ' AND c'.($iCurrentTableNumber+1).'.cl_to='.
        $dbr->addQuotes( $aCategories[$i]->getDbKey() );

      $iCurrentTableNumber++;
    }

    for ($i = 0; $i < $iExcludeCatCount; $i++) {
      $sSqlSelectFrom .= " LEFT OUTER JOIN $categorylinks AS c" . ($iCurrentTableNumber+1);
      $sSqlSelectFrom .= ' ON page_id = c'.($iCurrentTableNumber+1).'.cl_from';
      $sSqlSelectFrom .= ' AND c'.($iCurrentTableNumber+1).'.cl_to='.
        $dbr->addQuotes( $aExcludeCategories[$i]->getDbKey() );

      $sSqlWhere .= ' AND c'.($iCurrentTableNumber+1).'.cl_to IS NULL';

      $iCurrentTableNumber++;
    }

    if ('descending' == $sOrder)
      $sSqlOrder = 'DESC';
    else
      $sSqlOrder = 'ASC';

    if ('lastedit' == $sOrderMethod)
      $sSqlWhere .= ' ORDER BY page_touched ';
    else
      $sSqlWhere .= ' ORDER BY c1.cl_timestamp ';

    $sSqlWhere .= $sSqlOrder;
    

    if ($bCountSet)
    {
      $sSqlWhere .= ' LIMIT ' . $iCount;
    }

    //DEBUG: output SQL query 
    //$output .= 'QUERY: [' . $sSqlSelectFrom . $sSqlWhere . "]<br />";    

    // process the query
    $res = $dbr->query($sSqlSelectFrom . $sSqlWhere);
	
    $sk =& $wgUser->getSkin();

    if ($dbr->numRows( $res ) == 0) 
    {
      if (false == $bSuppressErrors)
	return htmlspecialchars( wfMsg( 'dynamicpagelist_noresults' ) );
      else
	return '';
    }
    
    //start unordered list
    $output .= $sStartList . "\n";
	
    //process results of query, outputing equivalent of <li>[[Article]]</li> for each result,
    //or something similar if the list uses other startlist/endlist
    while ($row = $dbr->fetchObject( $res ) ) {
      $title = Title::makeTitle( $row->page_namespace, $row->page_title);
      $output .= $sStartItem;
      if (true == $bAddFirstCategoryDate)
        $output .= $wgLang->date($row->cl_timestamp) . ': ';

      if (true == $bShowNamespace)
	$output .= $sk->makeKnownLinkObj($title);
      else
	$output .= $sk->makeKnownLinkObj($title, htmlspecialchars($title->getText()));
      $output .= $sEndItem . "\n";
    }

    //end unordered list
    $output .= $sEndList . "\n";

    return $output;
}
?>
