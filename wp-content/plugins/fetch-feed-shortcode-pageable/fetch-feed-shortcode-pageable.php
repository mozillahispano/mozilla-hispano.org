<?php
/*
Plugin Name: Fetch Feed shortcode pageable
Version: 1.1
Description: Makes it easy to display an RSS feed on a page. Uses fetch_feed function instead auf depreciated fetch_rss. Supports configurable paging !
Author: soundwaves-productions
Plugin URI: http://www.soundwaves-productions.com/soundwavesblog/wordpress-plugins/fetch-feed-shortcode-pageable/
*/

function fetchfeedshortcodepageable_css() { 
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') .'/wp-content/plugins/fetch-feed-shortcode-pageable/css/style.css" />' . "\n";
 } 

add_action('wp_head', 'fetchfeedshortcodepageable_css'); 


function FetchFeedPageable_call( $atts ) {
	extract(shortcode_atts(array(  
	   "feed" 		=> '',  
		"showall" 		=> 'yes',  
		"num" 		=> '5', 
		"target"	=> '_self',
		"linktitle"	=> 'yes',
		"itemelements" =>'title,author,date,description',
		"pagesize" =>'10',
		"pagenum" =>'10'
	), $atts));
//echo "feed: ". $feed;
// script constants (could/should be in .cfg file) [icehose 08083: converted to defines] 
define("ROWCOUNT", $pagesize); // # rows per page (set by you) 
define("RANGE", $pagenum); // number of page numbers in pageBar (set by you) 
$currPage=get_Permalink();

require_once(ABSPATH.WPINC.'/feed.php');  

	if ( $feed != "" && $rss = fetch_feed( $feed ) ) {
		
		if ( $showall != 'yes' ) {
			$maxitems = $rss->get_item_quantity($num);
		} else {
			$maxitems = $rss->get_item_quantity(0);
		}
}

// GET working variables 
$iPageNum = 1; // set default page number 
if (!empty($_GET["pagenum"])) // page number passed via GET 
{ 
    $iPageNum = $_GET["pagenum"]; // get actual page number 
} 
$iCursor = 0; // set default page cursor 
if (!empty($_GET["cursor"])) // cursor passed via GET 
{ 
    $iCursor = $_GET["cursor"]; // get actual cursor value 
} 
$iRows = $maxitems;
//---- [icehose 080803a: start corrected code] 
// calculate local control variables 
$iPages = (int) ceil($iRows / ROWCOUNT); 
$iRange = min($iPages, RANGE); // compensates for small data sets 
if ($iRange % 2 == 0) // calculate modulo only once for both constants 
{ 
    $iRangeMin = (int) ($iRange / 2) - 1; 
    $iRangeMax = $iRangeMin + 1; 
} 
else 
{ 
    $iRangeMin = (int) ($iRange - 1) / 2; 
    $iRangeMax = $iRangeMin; 
} 
//---- [icehose 080803a: end corrected code] 

//---- Start new code 
// Dagon's original code calculated the following control variables using a round-about technique; 
//        however, the same variables can be calculated directly from page # and base constants as follows: 
if ($iPageNum < ($iRangeMax + 1)) // ramp up phase 
{ 
    $iPageMin = 1; 
    $iPageMax = $iRange; 
} 
else // stable state, with min function to take care of ramp down phase 
{ 
    $iPageMin = min(($iPageNum - $iRangeMin), ($iPages - ($iRange - 1))); 
    $iPageMax = min(($iPageNum + $iRangeMax), $iPages); 
} 
//---- End new code 

$sPageButtons = ""; // set default (for strict correctness) 
if ($iPages > 1 ) // we need to generate a pagination bar 
{ 
    $s = 0; // initialize 
    $c = 0; // initialize 
    $p = 0; // initialize 
    if ($iPageMin > 1) // generate at least Prev button (New: simplified control structure) 
    { 
        if ($iPageMin > 2) // but first generate left arrow button (New: simplified control structure) 
        { 
            $s = 1; // pro forma 
            $aPageButtons[++$p] = "<td><a class=prevLink href=".$currPage ."?pagenum=1&cursor=0>first page</a></td>\r"; 
            $sPageButtons .= "\t\t<a class=prevLink href=".$currPage ."?pagenum=1&cursor=0>first page</a>\r"; 
        } 
        $s = $iPageMin - 1; 
        $c = ($s - 1) * ROWCOUNT; 
        $aPageButtons[++$p] = "<td><a class=prevLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">&lt;</a></td>\r"; 
        $sPageButtons .= "\t\t<a class=prevLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">&lt;</a>\r"; 
    } 
    for ($i = $iPageMin; $i <= $iPageMax; $i++) // generate numbered buttons 
    { 
        if ($i == $iPageNum) 
        { 
            $s = $i; 
            $c = ($s - 1) * ROWCOUNT; 
            $aPageButtons[++$p] = "<td><b>".$i."</b></td>\r"; 
            $sPageButtons .= "\t\t<span><b>".$i."</b></span>\r"; 
        } 
        else 
        { 
            $s = $i; 
            $c = ($s - 1) * ROWCOUNT; 
            $aPageButtons[++$p] = "<td><a class=pageLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">".$i."</a></td>\r"; 
            $sPageButtons .= "\t\t<a class=pageLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">".$i."</a>\r"; 
        } 
    } 
    if ($iPageMax < $iPages) // generate Next button (New: simplified control structure) 
    { 
        $s = $iPageMax + 1; 
        $c = ($s - 1) * ROWCOUNT; 
        $aPageButtons[++$p] = "<td><a class=nextLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">&gt;</a></td>\r"; 
        $sPageButtons .= "\t\t<a class=nextLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">&gt;</a>\r"; 
        if ($s < $iPages) // also generate right arrow button (New: simplified control structure) 
        { 
            $s = $iPages; 
            $c = ($s - 1) * ROWCOUNT; 
            $aPageButtons[++$p] = "<td><a class=nextLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">last page</a></td>\r"; 
            $sPageButtons .= "\t\t<a class=prevLink href=".$currPage ."?pagenum=".$s."&cursor=".$c.">last page</a>\r"; 
        } 
    } 
    $aPage["PAGINATION"] = "\t<div id=pageDiv align=\"center\">\r".$sPageButtons."\t</div>\r"; // build <div>...</div> form 
} // end generation of pagination bar 

foreach($rss->get_items($iCursor, ROWCOUNT) as $item) {
	
			$ItemElementsTmp = explode(",",$itemelements);
			for($i = 0;$i < count($ItemElementsTmp);$i ++) 			{
		switch ($ItemElementsTmp[$i]) {
    case "title":
      $title= $item->get_title() ;
      if ($linktitle!='yes')  {
				$content .= '<div class="ItemTitle">'. esc_html($title) .'</div>';		
			} else {
				 	$link =$item->get_permalink() ;
			if ($target != '_self') {
				$content .= '<div class="ItemTitle"><a class="ItemTitleLink" href='.$link.' target="_blank">'. esc_html($title) .'</a></div>';
			}else{
				$content .= '<div class="ItemTitle"><a class="ItemTitleLink" href='.$link.' >'. esc_html($title) .'</a></div>';
			}
			}
      break;
    case "author":
       if ($author = $item->get_author()) 	{
					$authorname = $author->get_name();
					}
        			$content .= '<div class="ItemAuthor">'. esc_html($authorname) .'</div>';
        break;
    case "date":
    		$date=$item->get_date();
        			$content .= '<div class="ItemDate">'. esc_html($date) .'</div>';    		
				break;
    case "description":
     					$description =$item->get_description() ;
        			$content .= '<div class="ItemDescription">'. esc_html($description) .'</div>';
        break;
  	}
		}
				$content.='<br><hr class="ItemSeparator">';
	}
	$content.=$aPage["PAGINATION"];
	return $content;
}
add_shortcode( 'FetchFeedPageable', 'FetchFeedPageable_call' );

