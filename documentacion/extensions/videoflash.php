<?php
 
/*******************************************************************************
*                                                                              *
* VideoFlash Extension by Alberto Sarullo, based on YouTube (Iubito) extension *
* http://www.mediawiki.org/wiki/Extension:VideoFlash                           *
*                                                                              * 
*                                                                              * 
* Tag :                                                                        *
*   <videoflash>v</videoflash>                                                 *
*                                                                              *
* Ex :                                                                         *
*   from url http://www.youtube.com/watch?v=4lhyH5TsuPg                        *
*   <videoflash>4lhyH5TsuPg</videoflash>                                       *
*                                                                              *
* Ex:                                                                          *
*   from url http://video.google.it/videoplay?docid=1811233136844420765        *
*   <videoflash type="googlevideo">1811233136844420765</videoflash>            *
*                                                                              *
* Ex:                                                                          *
*   from url http://en.sevenload.com/videos/7DQGFhH/Sexy-Tussis                *
*   <videoflash type="sevenload">7DQGFhH</videoflash>                          *
*                                                                              *
* Ex:                                                                          *
*   from url http://one.revver.com/watch/138657                                *
*   <videoflash type="revver">138657</videoflash>                              *
*                                                                              *
********************************************************************************/ 
 
$wgExtensionFunctions[] = 'wfVideoFlash';
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'VideoFlash',
        'description' => 'VideoFlash (YouTube, GoogleVideo, Dailymotion, sevenload...)',
        'author' => 'Alberto Sarullo',
        'url' => 'http://www.mediawiki.org/wiki/Extension:VideoFlash'
);
 
function wfVideoFlash() {
        global $wgParser;
        $wgParser->setHook('videoflash', 'renderVideoFlash');
}
 
 
# The callback function for converting the input text to HTML output
function renderVideoFlash($input, $args) {
        $input = htmlspecialchars($input);
 
        $type = "youtube";
        $params = explode ("|", $input);
        $id = $params[0];
        $width = 500;
        $height = 400;
        $style = '';
 
        $url['youtube']     = 'http://www.youtube.com/v/'.$id;
        $url['googlevideo'] = 'http://video.google.com/googleplayer.swf?docId='.$id;
        $url['dailymotion'] = 'http://www.dailymotion.com/swf/'.$id;
        $url['sevenload']   = 'http://en.sevenload.com/pl/'. $id .'/'. $width .'x'. $height .'/swf';
        $url['revver']      = 'http://flash.revver.com/player/1.0/player.swf?mediaId='.$id;
        $url['bliptv']      = 'http://blip.tv/scripts/flash/showplayer.swf?file=http%3A//blip.tv/rss/flash/'.$id;
        // add here other similar services
 
        if(count($args)>0 && $args['type'] && $url[$args['type']]){
           $type =  htmlspecialchars($args['type']);
        }
 
 
        if (count($params) > 1) {
           $width = $params[1];
           if (count($params) > 2) {
              $height = $params[2];
              if (count($params) > 3) {
                 $style = $params[3];
              }
           }
        }
 
        $output= '<object type="application/x-shockwave-flash" data="'.$url[$type].'" width="'.$width.'" height="'.$height.'" style="' . $style . '">'
                .'<param name="movie" value="'.$url[$type].'" /> <param name="allowfullscreen" value="true" />'
                .'<param name="wmode" value="transparent"></param>';
                /*.'<embed src="'.$url[$type]
                .'" type="application/x-shockwave-flash" wmode="transparent"'
                .' width="'.$width.'" height="'.$height.'" allowfullscreen="true" style="' 
                . $style . '"';*/
        if($type=='revver')
                        $output.='flashvars="mediaId='.$id.'&affiliateId=0"';
 
        $output.='></object>';
 
 
        return $output;
 }
 
?>
