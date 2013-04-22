<?php
 
// (c) Jeffrey Phillips Freeman <freemo@gmail.com> & released to the public domain.
 
// MediaWiki AIM Extension Ver 1.0a (http://www.mediawiki.org/wiki/Extension:AIM)
 
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'CMO',
        'version' => '1.0',
        'author' => 'Zbigniew Braniecki',
        'url' => 'http://labs.braniecki.net',
        'description' => 'Adds several custom tags for Community Mozilla website'
);

// set up MediaWiki to react to the "<topicbox>" tag
$wgExtensionFunctions[] = "wfCMOBox";
 
function wfCMOBox() {
        global $wgParser;
        $wgParser->setHook( "cmobox", "CMOBox" );
}
 

function urlize ($input, $url) {
  $string = '';
	if ($url)
	  $string .= '<a href="'.$url.'">';
  $string .= $input;
	if ($url)
	  $string .= '</a>';
	return $string;
}
// the function that reacts to "<topicbox>"

function CMOBox( $input, $argv, $parser) {
  global $wgScriptPath;
  $parser->disableCache();
 
  $input = '<cmobox title="'.$argv['title'].'">'.$input.'</cmobox>';
  $xml = new SimpleXMLElement($input);

  /* Me lo cargo porque da problemas cuando no le pasas columnas, siempre 2
   * $cols = $argv['columns']?$argv['columns']:2;
   */
  $cols = 2;

  /* Me lo cargo porque da problemas cuando no se pasa el argumento
  if ($argv['float'])
    $style = 'style="float:'.$argv['float'].';width:50%"';
  else
  * */
    $style = 'style="width:100%"';

// start the rendering the html output
  $output = '<div class="topicbox" '.$style.'><h2>'.$parser->recursiveTagParse((string)$argv['title']).'</h2><table>';
  $i = 0;
  $j = 0;
  foreach ($xml->cmocell as $cmocell) {
	if ($i==0)
	  $output .= '<tr>';
  if (substr($cmocell['url'],0,7)!='http://' &&  substr($cmocell['url'],0,8)!='https://') {
    $title = Title::newFromText((string)$cmocell['url']);
    if ($title)
      $url = $title->getLocalURL();
    } else {
      if ($cmocell['url']) {
        $url = (string)$cmocell['url'];
		}
    }
	  $output .= '<td>';    
		
	/* Hack para evitar que si no metemos url de error */
	if (!isset($url)) {
			$url = "";
	}
		
	  $output .= '<h3>'.urlize($parser->recursiveTagParse((string)$cmocell->title), $url).'</h3>';
		$output .= '<p>';
    if ($cmocell['icon'])
	    $output .= urlize('<img src="'.$wgScriptPath.'/skins/bookjive/icons/'.$cmocell['icon'].'.png" alt="icon"/>', $url);
	  $output .= urlize('<span class="desc">'.$parser->recursiveTagParse((string)$cmocell->shortdesc).'</span>',$url);
	  if (strlen(trim($cmocell->longdesc))) {
	    $output .= urlize('<span class="longdesc">'.$parser->recursiveTagParse((string)$cmocell->longdesc).'</span>',$url);
	  }
    $output .= '</p>';
	  $output .= '</td>';
	  if ($i==($cols-1) || count($xml->cmocell)==$j+1)
	    $output .= '</tr>';
	  $i++;
		$j++;
	  if ($i==$cols)
	    $i=0;
  }

  $output .= '</table></div>';
 
  // send the output to MediaWiki
  return $output;
}
