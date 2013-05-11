<?php

 function wp2bb_admin() {
   add_options_page('Wp2BB Configuration', 'Wp2BB', 8, 'wp2bb.php', 'wp2bb_options');
 }

 function wp2bb_options() {

  // variables for the field and option names 

  $hidden_field_name = 'mt_submit_hidden';

  $opt_phpbbpath = 'wp2bb_phpbbpath';
  $opt_phpbburl = 'wp2bb_phpbburl';
  $opt_defforum  = 'wp2bb_defforum';
  $opt_defpforum  = 'wp2bb_defpforum';
  $opt_username= 'wp2bb_username';
  $opt_usercolor  = 'wp2bb_usercolor';
  $opt_msgtext  = 'wp2bb_msgtext';
  $opt_msgtextpage  = 'wp2bb_msgtextpage';
  $opt_comments0 = 'wp2bb_comments0';
  $opt_comments1 = 'wp2bb_comments1';
  $opt_commentsx = 'wp2bb_commentsx';
  $opt_reply  = 'wp2bb_reply';
  $opt_onthefly = 'wp2bb_onthefly';
  $opt_excludecats = 'wp2bb_excludecats' ;

  // Read in existing option value from database

  $opt_phpbbpath_val = get_option( $opt_phpbbpath );
  $opt_phpbburl_val     = get_option( $opt_phpbburl);
  $opt_defforum_val  = get_option( $opt_defforum );
  $opt_defpforum_val = get_option( $opt_defpforum );
  $opt_username_val = get_option( $opt_username );
  $opt_usercolor_val = get_option( $opt_usercolor );
  $opt_msgtext_val = get_option( $opt_msgtext);
  $opt_msgtextpage_val = get_option( $opt_msgtextpage);
  $opt_comments0_val = get_option( $opt_comments0);
  $opt_comments1_val = get_option( $opt_comments1);
  $opt_commentsx_val = get_option( $opt_commentsx);
  $opt_reply_val = get_option( $opt_reply);
  $opt_onthefly_val = get_option( $opt_onthefly);
  $opt_excludecats_val = get_option( $opt_excludecats);

  if( $_POST[ $hidden_field_name ] == 'Y' ) {

    $opt_phpbbpath_val = $_POST[ $opt_phpbbpath];
    $opt_phpbburl_val     = $_POST[ $opt_phpbburl];
    $opt_defforum_val  = $_POST[ $opt_defforum];
    $opt_defpforum_val = $_POST[ $opt_defpforum];
    $opt_username_val = $_POST[ $opt_username];
    $opt_usercolor_val = $_POST[ $opt_usercolor];
    $opt_msgtext_val = $_POST[ $opt_msgtext];
    $opt_msgtextpage_val = $_POST[ $opt_msgtextpage];
    $opt_comments0_val = $_POST[ $opt_comments0];
    $opt_comments1_val = $_POST[ $opt_comments1];
    $opt_commentsx_val = $_POST[ $opt_commentsx];
    $opt_reply_val = $_POST[ $opt_reply];
    $opt_onthefly_val = $_POST[ $opt_onthefly];
    $opt_excludecats_val = $_POST[ $opt_excludecats];

    $opt_msgtext_val = str_replace('\"','"',$opt_msgtext_val);
    $opt_msgtext_val = str_replace("\'","'",$opt_msgtext_val);
    $opt_msgtext_val = str_replace('\\\\','\\',$opt_msgtext_val);

    $opt_msgtextpage_val = str_replace('\"','"',$opt_msgtextpage_val);
    $opt_msgtextpage_val = str_replace("\'","'",$opt_msgtextpage_val);
    $opt_msgtextpage_val = str_replace('\\\\','\\',$opt_msgtextpage_val);

    $opt_comments0_val = str_replace('\"','"',$opt_comments0_val);
    $opt_comments0_val = str_replace("\'","'",$opt_comments0_val);
    $opt_comments0_val = str_replace('\\\\','\\',$opt_comments0_val);

    $opt_comments1_val = str_replace('\"','"',$opt_comments1_val);
    $opt_comments1_val = str_replace("\'","'",$opt_comments1_val);
    $opt_comments1_val = str_replace('\\\\','\\',$opt_comments1_val);

    $opt_commentsx_val = str_replace('\"','"',$opt_commentsx_val);
    $opt_commentsx_val = str_replace("\'","'",$opt_commentsx_val);
    $opt_commentsx_val = str_replace('\\\\','\\',$opt_commentsx_val);

    $opt_reply_val = str_replace('\"','"',$opt_reply_val);
    $opt_reply_val = str_replace("\'","'",$opt_reply_val);
    $opt_reply_val = str_replace('\\\\','\\',$opt_reply_val);

    update_option( $opt_phpbbpath, $opt_phpbbpath_val );
    update_option( $opt_phpbburl, $opt_phpbburl_val );
    update_option( $opt_defforum, $opt_defforum_val );
    update_option( $opt_defpforum, $opt_defpforum_val );
    update_option( $opt_username, $opt_username_val );
    update_option( $opt_usercolor, $opt_usercolor_val );
    update_option( $opt_msgtext, $opt_msgtext_val );
    update_option( $opt_msgtextpage, $opt_msgtextpage_val );
    update_option( $opt_comments0, $opt_comments0_val );
    update_option( $opt_comments1, $opt_comments1_val );
    update_option( $opt_commentsx, $opt_commentsx_val );
    update_option( $opt_reply, $opt_reply_val );
    update_option( $opt_onthefly, $opt_onthefly_val );
    update_option( $opt_excludecats, $opt_excludecats_val );

  ?>

  <div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
  <?php }
     echo '<div class="wrap">';    
     echo "<h2>" . __( 'Wp2BB Plugin Options', 'mt_trans_domain' ) . "</h2>";
     echo '<a href="http://www.alfredodehoces.com/wp2bb">Wp2BB</a> by <a href="http://www.alfredodehoces.com">Alfredo de Hoces</a>';

    if (!file_exists($opt_phpbbpath_val . '/config.php')) {
      echo '<div style="border:5px solid #aa0000; background:#feeeee; margin:0px;margin-top:10px; padding:4px;clear:both;"><p>';
      echo '&nbsp;&nbsp;<b>PhpBB not found!</b> Please set PhpBB local path before creating or updating posts</p></div>';
    }

  ?>

  <!-- Here is the form -->     
  <form name="form1" method="post" 
   action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
  <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

  <table class="form-table">

  <tr valign="top">
    <th scope="row"><label><?php _e("phpBB local path:", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_phpbbpath" value="<?php echo $opt_phpbbpath_val; ?>" size="60">
    <?php 
        if (file_exists($opt_phpbbpath_val . '/config.php')) echo ('<br/>PhpBB found'); 
        else echo ('<br><strong>PhpBB NOT FOUND!</strong>');
     ?>
    </td>
    <td>Local path of your phpbb installation<br/>(probably <?php
          $wpl=substr(__FILE__,0,stripos(__FILE__, '/wp-content'));
          $bbl= substr($wpl,0,stripos($wpl,strrchr($wpl,'/'))) . '<em>/myforum</em>';
          echo ('<strong>' . $bbl . '</strong>'); 
         ?>)</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("PhpBB forum URL:", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_phpbburl" value="<?php echo $opt_phpbburl_val; ?>" size="60"></td>
    <td>Public URL of your forum<br/>(i.e. "http://www.mydomain.com/myforum")</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Default forum for posts:", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_defforum" value="<?php echo $opt_defforum_val; ?>" size="60"></td>
    <td>Numeric ID of the phpBB forum where topics for new posts will be created (if none specified in the 'forum' custom field of the WP post). If blank, no topics will be created unless 'forum' custom field of the post is set</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Default forum for pages:", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_defpforum" value="<?php echo $opt_defpforum_val;?>" size="60"></td>
    <td>Numeric ID of the phpBB forum where topics for new pages will be created (if none specified in the 'forum' custom field of the WP page). If blank, no topics will be created unless 'forum' custom field of the page is set</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Exclude categories", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_excludecats" value="<?php echo $opt_excludecats_val;?>" size="60"></td>
    <td>Comma separated list of Wordpress categories ID's to be excluded. Any post or page in this categories will not have correspondent messages in the forum</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Forum user name:", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_username" value="<?php echo $opt_username_val;?>" size="60"></td>
    <td>PhpBB existing user used for creating new topics (i.e. 'admin'). If left blank or user does not exist, 'guest' will be used</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Forum user color:", 'mt_trans_domain' ); ?></label></th>
    <td><input type="text" name="wp2bb_usercolor" value="<?php echo $opt_usercolor_val;?>" size="60"></td>
    <td>Hex color in which user name will be displayed in the forum (i.e. 'AA0000'). Leave blank for default forum color</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Topic message text for posts:", 'mt_trans_domain' ); ?></label></th>
    <td><textarea id="details" name="wp2bb_msgtext" rows="5" cols="60"><?php echo $opt_msgtext_val;?></textarea></td>
    <td>Message text for the topics in the forum created for every new page in the blog (if none specified in the 'msg' custom field of the WP page)<br/>
     Some text replacement keywords can be used:<br/>
    {$post} Wordpress post text (in HTML)<br/>
    {$title} Wordpress post title<br/>
    {$purl} Wordpress post Permalink</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Topic message text for pages:", 'mt_trans_domain' ); ?></label></th>
    <td><textarea id="details" name="wp2bb_msgtextpage" rows="5" cols="60"><?php echo $opt_msgtextpage_val;?></textarea></td>
    <td>Message text for the topic in the forum created for every new page in the blog (if none specified in the 'msg' custom field of the WP page)<br/>
     Some text replacement keywords can be used:<br/>
    {$post} Wordpress post text (in HTML)<br/>
    {$title} Wordpress post title<br/>
    {$purl} Wordpress post Permalink</td>
  </tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("'0 messages in the forum' text:", 'mt_trans_domain' ); ?></label></th>
    <td><textarea id="comments0" name="wp2bb_comments0" rows="3" cols="60"><?php echo $opt_comments0_val;?></textarea></td>
    <td>Text displayed when there are no comments in the forum for the post</tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("'1 message in the forum' text:", 'mt_trans_domain' ); ?></label></th>
    <td><textarea id="comments1" name="wp2bb_comments1" rows="3" cols="60"><?php echo $opt_comments1_val;?></textarea></td>
    <td>Text displayed when there is 1 comment in the forum for the post</tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("'x messages in the forum' text:", 'mt_trans_domain' ); ?></label></th>
    <td><textarea id="commentsx" name="wp2bb_commentsx" rows="3" cols="60"><?php echo $opt_commentsx_val;?></textarea></td>
    <td>Text displayed when there is more than 1 comments in the forum for the post<br/>
     Some text replacement keywords can be used:<br/>
    {$x} Actual number of messages in the forum</tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("'Add a message in the forum' text:", 'mt_trans_domain' ); ?></label></th>
    <td><textarea id="textreply" name="wp2bb_reply" rows="3" cols="60"><?php echo $opt_reply_val;?></textarea></td>
    <td>Text displayed in the 'add a reply in the forum' quick link</tr>

  <tr valign="top">
    <th scope="row"><label><?php _e("Add topics for old posts:", 'mt_trans_domain' ); ?></label></th>
    <td>
    <select name="wp2bb_onthefly">
        <option value="">No</option>
        <option value="1" <?php if($opt_onthefly_val) {echo ' selected';} ?> >Yes</option>
    </select>
    </td>
    <td>If enabled, topics in the forum will be created for old entries in your blog (entries written before wp2bb was installed). These topics will be created when the entries are browsed by your readers</tr>

  </table>

  <p class="submit">
    <input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ); ?>" />
  </p>

  </form>
  </div>

<?php } ?>