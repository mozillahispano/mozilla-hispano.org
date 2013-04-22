<?php
/*  
Plugin Name: Wp2BB
Plugin URI: http://www.alfredodehoces.com/wp2bb
Description: Wordpress comments and phpBB forum integration. Automatically adds topics in your phpBB forum as posts and pages are published in your Wordpress blog. It also shows quick links to the forum in your posts/pages and the latest forum messages in your sidebar.
Version: 1.4.1
Author: Alfredo de Hoces
Author URI: http://www.alfredodehoces.com/wp2bb/about/

Copyright 2008 Alfredo de Hoces (email: wp2bb@alfredodehoces.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// -------------------------------------------------------------------------------------------------
// INCLUDES AND SETUP
// -------------------------------------------------------------------------------------------------

$phpEx = 'php';
$phpbb_root_path = get_option( 'wp2bb_phpbbpath' ) . '/';
$host = get_option( 'wp2bb_wpurl' );
$username=get_option( 'wp2bb_username' );
$usercolour=get_option( 'wp2bb_usercolor' );

include("wp2bbforum.php");
include("wp2bbadmin.php");

if (file_exists($phpbb_root_path . '/config.php')) {
  $db = new $sql_db();
  $cache = new cache();
}


// -------------------------------------------------------------------------------------------------
// HOOKS AND FILTERS
// -------------------------------------------------------------------------------------------------

 add_action('admin_menu',   'wp2bb_admin');
 add_action('publish_post', 'wp2bb_publish',9999);
 add_action('publish_page', 'wp2bb_publish',9999);
 add_filter('the_content',  'wp2bb_addoldpost');
 add_action('plugins_loaded', 'registerwidgets',9999);


// -------------------------------------------------------------------------------------------------
// WIDGET (latest comments)
// -------------------------------------------------------------------------------------------------

  
  function registerwidgets() 
  {
     register_sidebar_widget('Wp2BB', 'wp2bb_widget'); 
     register_widget_control('Wp2BB', 'wp2bb_widget_control');
  }


 function remove_bbcode($msgtext)
 {
    $search ='|[[\/\!]*?[^\[\]]*?]|si';
    $text = preg_replace($search, '', $msgtext);
 return $text;
 }


  function wp2bb_widget_control() 
  {
    $options = $newoptions = get_option('wp2bb_widget');

    if ( $_POST["wp2bb-submit"] ) 
    {
	$newoptions['ww_title'] = strip_tags(stripslashes($_POST["ww-title"]));
	$newoptions['ww_forums'] = strip_tags(stripslashes($_POST["ww-forums"]));
	$newoptions['ww_messages'] = strip_tags(stripslashes($_POST["ww-messages"]));
	if ( empty($newoptions['ww_title']) ) $newoptions['ww_title'] = 'Latest messages in the forum';
	if ( empty($newoptions['ww_messages']) ) $newoptions['title'] = '5';
    }

    if ( $options != $newoptions ) 
    {
	$options = $newoptions;
	update_option('wp2bb_widget', $options);
    }

    $title = htmlspecialchars($options['title'], ENT_QUOTES);

    ?>
    <p>

      <label for="ww-title">
        <?php _e('Title:'); ?>
        <input style="width: 250px;" id="ww-title" name="ww-title" type="text" value="<?php echo$newoptions['ww_title']; ?>" />
      </label>

      <label for="ww-messages">
        <?php _e('Numer of messages:'); ?>
        <input style="width: 250px;" id="ww-messages" name="ww-messages" type="text" value="<?php echo$newoptions['ww_messages']; ?>" />
      </label>

      <label for="ww-forums">
        <?php _e('Forums to inspect (comma separated):'); ?>
        <input style="width: 250px;" id="ww-forums" name="ww-forums" type="text" value="<?php echo$newoptions['ww_forums']; ?>" />
      </label>

    </p>
    <input type="hidden" id="wp2bb-submit" name="wp2bb-submit" value="1" />
    <?php
  }


  function wp2bb_widget($args) 
  {
      extract($args);
      $wwoptions=get_option( 'wp2bb_widget' );
      $title=$wwoptions['ww_title'];
      $forums=$wwoptions['ww_forums'];
      $messages=$wwoptions['ww_messages'];

      global $db, $phpbb_root_path;
      include($phpbb_root_path . 'config.php');
      $connection = mysql_connect($dbhost, $dbuser, $dbpasswd);
      $db->sql_connect( $dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);

      $query = "SELECT p.post_id, p.post_time, p.post_username,  u.username, p.post_subject, t.topic_title, p.forum_id, p.topic_id, p.post_username, p.post_text FROM ".$table_prefix."posts p INNER JOIN ".$table_prefix."topics t ON p.topic_id=t.topic_id INNER JOIN ".$table_prefix."users u ON p.poster_id=u.user_id WHERE p.post_id<>t.topic_first_post_id";

      if($forums) $query=$query." AND p.forum_id IN (".$forums.")";
      $query =  $query . " ORDER BY p.post_id DESC LIMIT ". $messages;

      $results = $db->sql_query($query );
      $forumlink = get_option( 'wp2bb_phpbburl' );
      $contents="<div id='forum_messages'><ul>";

      while ($row = mysql_fetch_assoc($results)) {

        $link=$forumlink.'/viewtopic.php?f='.$row['forum_id'].'&amp;t='.$row['topic_id'].'#p'.$row['post_id'];
        $tit=$row['post_subject'];
        if(!$tit) $tit=$row['topic_title'];

        $tm=strip_tags($row['post_text']);
        $tm=remove_bbcode($tm);
    
        $user=$row['post_username'];
        if(!$user) $user=$row['username'];
  
        if (strlen($tm)>125) 
        {
            $tm=substr($tm,0,125);
            $needle=strrchr ($tm," ");
            $tm=substr($tm,0,strrpos($tm,$needle)). '(...)';
        }

        $contents=$contents.'<li>';
        $contents=$contents.'<a href="'.$link.'">';
        $contents=$contents.$tit;
        $contents=$contents.'</a>';
        $contents=$contents.'<p>'.$tm.'</p>';
        $contents=$contents.'<div class="forum_messages_date">'.date('j/m/y h:m',$row['post_time'])." by <b>".$user."</b>".'</div>';
        $contents=$contents.'</li>';
      }

      $contents=$contents ."</ul></div>";

      ?>
              <?php echo $before_widget; ?>
                  <?php echo $before_title . $title . $after_title . $contents; ?>
              <?php echo $after_widget; ?>
      <?php
  }


// -------------------------------------------------------------------------------------------------
// TEMPLATE TAGS FOR POSTS
// -------------------------------------------------------------------------------------------------

  function wp2bb() {
    $res='&nbsp;';

    global $db, $config, $phpEx, $template;
    $id=get_the_ID();

    if(!$id) {
     echo '(post id not found)';   
     return false;
    }
 
    $phpbb_root_path = get_option( 'wp2bb_phpbbpath' ) . '/';

    include($phpbb_root_path . 'config.php');
    $connection = mysql_connect($dbhost, $dbuser, $dbpasswd);
    $db->sql_connect( $dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);
 
    $mipost= get_post($id);
    $props = get_post_custom($id);
    $xtopic=$props['topic'][0];

    if($xtopic) {
      $xforum=$props['forum'][0];
      $phpbbq = "SELECT topic_replies FROM ".$table_prefix."topics where topic_id=".$xtopic;
      $results = $db->sql_query($phpbbq);
      $resultsrow = $db->sql_fetchrow($results);
      $topic_replies = $resultsrow['topic_replies'];
      $phpbburl = get_option( 'wp2bb_phpbburl' ) . '/';
      $res = $res . '&nbsp;<a href="'.$phpbburl.'viewtopic.php?f='.$xforum.'&amp;t='.$xtopic.'">';

      $msgs='messages in the forum';
      if($topic_replies=='0') $msgs=get_option( 'wp2bb_comments0' );
      else if($topic_replies=='1') $msgs=get_option( 'wp2bb_comments1' );
      else $msgs=get_option( 'wp2bb_commentsx' );

      $msgs=str_replace('{$x}',$topic_replies,$msgs);
      $res = $res . $msgs . '</a>';

      //reply 

      $res=$res . '&nbsp;<a href="' . $phpbburl . 'posting.php?mode=reply&amp;f=' . $xforum . '&amp;t=' . $xtopic . '">' . get_option('wp2bb_reply'). '</a>';
    }
    else if (!catexcluded($id)) {
     // echo '([topic] field not found)';   
     return false;
    }

  echo $res;
  }


// -------------------------------------------------------------------------------------------------
// PLUGIN ACTIONS
// -------------------------------------------------------------------------------------------------

 function wp2bb_addoldpost($txt) {

    global $phpbb_root_path;  

    $id=get_the_ID();
    if (!$id) return $txt;   
  
    if (!file_exists($phpbb_root_path . '/config.php')) return $txt;

    $mipost= get_post($id);
    $props = get_post_custom($id);
    $xtopic=$props['topic'][0];

    // no topic in the forum, create?
    if($xtopic=='') {
     if ((get_option('wp2bb_onthefly'))&&($mipost->post_status=='publish')) {
         wp2bb_publish($id);
      }
    }

 return $txt;
 }
 
 
 function catexcluded($postid) {
   $postcats = get_the_category($postid);
   $excludedcats = explode(",",get_option( 'wp2bb_excludecats' )); 
   foreach($postcats as $category) {     
    $catid=$category->cat_ID;
     if (in_array($catid,$excludedcats)) return true;
   } 
 return false;
 }
  
  
 function wp2bb_publish($id) {

  global $phpbb_root_path, $sql_db, $db, $username, $usercolour;  
  include($phpbb_root_path . 'config.php');

  $mipost= get_post($id);
  $posttext = $mipost->post_content;

  if (!file_exists($phpbb_root_path . '/config.php')) {return $posttext;}

  list($usec, $sec) = explode(" ", microtime());
  $props = get_post_custom($id);
  $posttitle = $mipost->post_title;
  //$posttime = mysql2date('U',$mipost->post_date);
  $posttime = mysql2date('U',$mipost->post_date_gmt)-14400;
  $posttype = $mipost->post_type;
  $postlink = post_permalink($id);

  $xforo=$props['forum'][0];
  $xtopic=$props['topic'][0];
  $replacedtext=$props['msg'][0];

  if ($xforo=='')  {
   if ($posttype=='post') {$xforo=get_option( 'wp2bb_defforum' );}
   else if ($posttype=='page') {$xforo=get_option( 'wp2bb_defpforum' );}
  }

  if (($xforo)&&($xforo!='0')&&($mipost->post_status=='publish')&&
      (!catexcluded($id))) {

    if ($replacedtext=='') {
      if ($posttype=='post') {$replacedtext = get_option( 'wp2bb_msgtext' );}
      else if ($posttype=='page') {$replacedtext = get_option( 'wp2bb_msgtextpage' );} 
    }

    $replacedtext = str_replace('\"','"',$replacedtext);
    $replacedtext = str_replace("\'","'",$replacedtext);
    $replacedtext = str_replace('\\\\','\\',$replacedtext);

    $posttitle2 = str_replace('{$title}','{$$title$$}', $posttitle);
    $posttext2 = str_replace('{$post}','{$$post$$}', $posttext);

    $replacedtext = str_replace('{$purl}', $postlink, $replacedtext);
    $replacedtext = str_replace('{$title}', $posttitle2, $replacedtext);
    $replacedtext = str_replace('{$post}', $posttext2, $replacedtext);
    $replacedtext = str_replace('{$$title$$}', '{$title}', $replacedtext);
    $replacedtext = str_replace('{$$post$$}', '{$post}', $replacedtext);

    // Plugin credits -- please do not delete
    // $replacedtext = $replacedtext . '<div style="font-name:small fonts; font-size:8pt;margin:0;padding:0;margin-top:5px;"><a href="http://www.alfredodehoces.com/wp2bb">Wp2BB</a> by <a href="http://www.alfredodehoces.com">Alfredo de Hoces</a></div>';
    // screw the credits

    $my_subject	= utf8_normalize_nfc($posttitle);
    $my_text = utf8_normalize_nfc($replacedtext);
    $poll = $uid = $bitfield = $options = ''; 

    generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
    generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);
    
    $data = array('forum_id'=>$xforo,'icon_id'=>false,'enable_bbcode'=>true,'enable_smilies'=>true,
                  'enable_urls'=>true,'enable_sig'=>true,'message'=>$my_text,
                  'message_md5'=>md5($my_text),'bbcode_bitfield'=>'','bbcode_uid'=>$uid,
                  'post_edit_locked'=>0,'topic_title'=>$my_subject,'notify_set'=>false,
                  'notify'=> false,'enable_indexing'  => true);

    $connection = mysql_connect($dbhost, $dbuser, $dbpasswd);
    $db->sql_connect( $dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);
    $auth = new auth();     

   if($xtopic) {
     // create a phpbb post in the appropiate forum
     update_post($xtopic, $my_subject, $my_text);
   } 
   else {
     // insert new post
     $xtopic = submit_post($auth, $user, $my_subject, $username, $usercolour, $posttime, $poll, $data);
     if ($props['forum'][0]=='') {add_post_meta($id, 'forum', $xforo, false);}
     add_post_meta($id, 'topic', $xtopic, false);
   }
 }

}

?>
