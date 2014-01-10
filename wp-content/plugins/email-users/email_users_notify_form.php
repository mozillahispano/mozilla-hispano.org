<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/*  Copyright 2006 Vincent Prat  

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>

<?php 
    global $post, $user_identity, $user_email, $user_ID; 

    if (!current_user_can(MAILUSERS_NOTIFY_USERS_CAP)) {
        wp_die(printf('<div class="error fade"><p>%s</p></div>',
            __('You are not allowed to notify users about posts and pages.', MAILUSERS_I18N_DOMAIN)));
    } 
    
    // Get the post id, look for a GET parameter followed by a POST parameter

    if ( isset($_GET['post_id']) ) {
        $post_id = $_GET['post_id'];
    }
    else if ( isset($_POST['post_id']) ) {
        $post_id = $_POST['post_id'];
    }

    if ( !isset($post_id) && !isset($err_msg) ) {
        $err_msg .= sprintf(__('Trying to notify users of a %s without passing the %s id!',
            MAILUSERS_I18N_DOMAIN), __(ucwords($post_type)), __(ucwords($post_type)));
    }
    

    $screen = get_current_screen() ;
    $post_type = $screen->post_type == '' ? 'post' : $screen->post_type ;
        
    if (!isset($post_id)) { ?>
    <div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php printf(__('Notify Users of a %s', MAILUSERS_I18N_DOMAIN), __(ucwords($post_type))); ?></h2>
    <form name="SetPost" action="" method="post">
        <p><?php printf(__('Please select the %s that you wish to notify users about.', MAILUSERS_I18N_DOMAIN), __(ucwords($post_type))); ?></p>
        <select style="width:300px;" name="post_id">
        <?php
         global $post ;
         $lastposts = get_posts(array('numberposts' => -1, 'post_type' => $post_type));
         foreach($lastposts as $post) :
            setup_postdata($post);
         ?>
        <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
         <?php endforeach; ?>
        </select>

        <p class="submit">
            <input class="button-primary" type="submit" name="Submit" value="<?php printf(__('Select %s', MAILUSERS_I18N_DOMAIN), __(ucwords($post_type))); ?> &raquo;" />
        </p>
    </form>
    </div>
    <?php } else {
    if (!isset($send_targets)) {
        $send_targets = array();
    }    
    if (!isset($send_users)) {
        $send_users = array();
    }

    $mail_format = mailusers_get_default_mail_format() == 'html' ? 
        __('HTML', MAILUSERS_I18N_DOMAIN) :    __('Plain text', MAILUSERS_I18N_DOMAIN);
                    
    $subject = mailusers_get_default_subject();
    $mail_content = mailusers_get_default_body();

    // Replace the template variables concerning the blog details
    // --
    $subject = mailusers_replace_blog_templates($subject);
    $mail_content = mailusers_replace_blog_templates($mail_content);
        
    // Replace the template variables concerning the sender details
    // --    
    get_currentuserinfo();

    $from_name = $user_identity;
    $from_address = $user_email;
    $override_name = mailusers_get_from_sender_name_override();
    $override_address = mailusers_get_from_sender_address_override();
    $subject = mailusers_replace_sender_templates($subject, $from_name);
    $mail_content = mailusers_replace_sender_templates($mail_content, $from_name);
        
    // Replace the template variables concerning the post details
    // --
    $post = get_post( $post_id );
    $post_title = $post->post_title;
    $post_url = get_permalink( $post_id );            
    $post_content = explode( '<!--more-->', $post->post_content, 2 );
    $post_excerpt = get_the_excerpt() ;
    $post_author = get_userdata( $post->post_author )->display_name;

    if (empty($post_excerpt)) $post_excerpt = $post_content[0];

    if (mailusers_get_default_mail_format()=='html') {
        $post_excerpt = wpautop($post_excerpt);
    }
    // Process short codes?
    if (mailusers_get_shortcode_processing() == 'true') {
        $post_content = do_shortcode($post_content) ;
        $post_excerpt = do_shortcode($post_excerpt) ;
    }
    
    //  Deal with post content in array form
    if (is_array($post_content)) $post_content = $post_content[0] ;

    $subject = mailusers_replace_post_templates($subject, $post_title, $post_author, $post_excerpt, $post_content, $post_url);
    $mail_content = mailusers_replace_post_templates($mail_content, $post_title, $post_author, $post_excerpt, $post_content, $post_url);
?>

<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php printf(__('Notify Users of a %s' , MAILUSERS_I18N_DOMAIN), __(ucwords(get_post_type($post_id)))); ?></h2>
        
    <?php if (isset($err_msg) && $err_msg!='') { ?>
        <div class="error fade"><h4><?php echo $err_msg; ?></h4></div>
        <p><?php _e('Please correct the errors displayed above and try again.', MAILUSERS_I18N_DOMAIN); ?></p>
    <?php } ?>
    
        
    <!--<form name="SendEmail" action="admin.php?page=mailusers-notify-user-page" method="post">-->
    <form name="SendEmail" action="" method="post">
        <input type="hidden" name="send" value="true" />
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
        <input type="hidden" name="mail_format" value="<?php echo mailusers_get_default_mail_format(); ?>" />
        <input type="hidden" name="fromName" value="<?php echo $from_name;?>" />
        <input type="hidden" name="fromAddress" value="<?php echo $from_address;?>" />
        <input type="hidden" name="subject" value="<?php echo format_to_edit($subject);?>" />
        
        <table class="form-table" width="100%" cellspacing="2" cellpadding="5">
        <tr>
            <th scope="row" valign="top"></th>
            <td><strong><?php _e('Mail will be sent as:', MAILUSERS_I18N_DOMAIN); ?> <?php echo $mail_format; ?></strong></td>
        </tr>
        <tr>
            <th scope="row" valign="top"><label for="fromName"><?php _e('Sender', MAILUSERS_I18N_DOMAIN); ?></label></th>
            <?php if (empty($override_address)) { ?>
            <td><?php echo $from_name;?> &lt;<?php echo $from_address;?>&gt;</td>
            <?php } else { ?>
            <td><input name="from_sender" type="radio" value="0" checked/><?php echo $from_name;?> &lt;<?php echo $from_address;?>&gt;<br/><input name="from_sender" type="radio" value="1"/><?php echo $override_name;?> &lt;<?php echo $override_address;?>&gt;</td>
            <?php }?>
        </tr>
        <tr>
            <th scope="row" valign="top"><label for="send_targets"><?php _e('Recipients', MAILUSERS_I18N_DOMAIN); ?>
            <br/><br/>
            <small><?php _e('Use CTRL key to select/deselect multiple items', MAILUSERS_I18N_DOMAIN); ?></small>
            <br/><br/>
            <small><?php _e('The users that did not agree to receive notifications do not appear here.', MAILUSERS_I18N_DOMAIN); ?></small></label></th>
            <td>
                <select name="send_targets[]" multiple="yes" size="8" style="width: 250px; height: 250px;">
                <?php 

                    $prefix = __('Filter', MAILUSERS_I18N_DOMAIN) ;
                    $targets = mailusers_get_group_meta_filters($user_ID, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META);

                    foreach ($targets as $key => $value)
                    {
                        $index = strtolower($prefix . '-' . $key); ?>
                        <option value="<?php echo $index; ?>"
                        <?php echo (in_array($index, $send_targets) ? ' selected="yes"' : '');?>>
                       <?php printf('%s - %s', $prefix, $value); ?>
                        </option>
                        <?php
                    }

                    $prefix = __('Role', MAILUSERS_I18N_DOMAIN) ;
                    $targets = mailusers_get_roles($user_ID, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META);

                    foreach ($targets as $key => $value)
                    {
                        $index = strtolower($prefix . '-' . $key); ?>
                        <option value="<?php echo $index; ?>"
                        <?php echo (in_array($index, $send_targets) ? ' selected="yes"' : '');?>>
                        <?php printf('%s - %s', $prefix, __($value)); ?>
                        </option>
                        <?php 
                    }

                    //  Is the User Groups plugin active?
                    if (class_exists(MAILUSERS_USER_GROUPS_CLASS))
                    {
                        $prefix = __('User Group', MAILUSERS_I18N_DOMAIN) ;
                        $targets = mailusers_get_user_groups($user_ID, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META);

                        foreach ($targets as $key => $value)
                        {
                            $index = strtolower($prefix . '-' . $key); ?>
                            <option value="<?php echo $index; ?>"
                            <?php echo (in_array($index, $send_targets) ? ' selected="yes"' : '');?>>
                            <?php printf('%s - %s', $prefix, __($value)); ?>
                            </option>
                            <?php
                        }
                    }

                    //  Is the User Access Manager plugin active?
                    if (class_exists(MAILUSERS_USER_ACCESS_MANAGER_CLASS))
                    {
                        $prefix = __('UAM', MAILUSERS_I18N_DOMAIN) ;
                        $targets = mailusers_get_uam_groups($user_ID, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META);

                        foreach ($targets as $key => $value)
                        {
                            $index = strtolower($prefix . '-' . $key); ?>
                            <option value="<?php echo $index; ?>"
                            <?php echo (in_array($index, $send_targets) ? ' selected="yes"' : '');?>>
                            <?php printf('%s - %s', $prefix, __($value)); ?>
                            </option>
                            <?php
                        }
                    }

                    //  Is the ItThinx Groups plugin active?
                    if (class_exists(MAILUSERS_ITTHINX_GROUPS_CLASS))
                    {
                        $prefix = __('Groups', MAILUSERS_I18N_DOMAIN) ;
                        $targets = mailusers_get_itthinx_groups($user_ID, MAILUSERS_ACCEPT_MASS_EMAIL_USER_META);

                        foreach ($targets as $key => $value)
                        {
                            $index = strtolower($prefix . '-' . $key); ?>
                            <option value="<?php echo $index; ?>"
                            <?php echo (in_array($index, $send_targets) ? ' selected="yes"' : '');?>>
                            <?php printf('%s - %s', $prefix, __($value)); ?>
                            </option>
                            <?php
                        }
                    }
                ?>
                </select> 

                <select name="send_users[]" multiple="yes" size="8" style="width: 400px; height: 250px;">
                <?php 
                    //  Display of users is based on plugin setting
                    $na = __('N/A', MAILUSERS_I18N_DOMAIN);
                    $sortby = mailusers_get_default_sort_users_by();
    

                    $users = mailusers_get_users($user_ID, MAILUSERS_ACCEPT_NOTIFICATION_USER_META);
                    foreach ($users as $user) {
                        switch ($sortby) {
                            case 'fl' :  //  First Last
                                $name = sprintf('%s %s',
                                    is_null($user->first_name) ? $na : $user->first_name,
                                    is_null($user->last_name) ? $na : $user->last_name);
                                break;

                            case 'flul' :  //  First Last User Login
                                $name = sprintf('%s %s (%s)',
                                    is_null($user->first_name) ? $na : $user->first_name,
                                    is_null($user->last_name) ? $na : $user->last_name,
                                    $user->user_login);
                                break;

                            case 'lf' :
                                $name = sprintf('%s, %s',
                                    is_null($user->last_name) ? $na : $user->last_name,
                                    is_null($user->first_name) ? $na : $user->first_name);
                                break;

                            case 'lful' :
                                $name = sprintf('%s, %s (%s)',
                                    is_null($user->last_name) ? $na : $user->last_name,
                                    is_null($user->first_name) ? $na : $user->first_name,
                                    $user->user_login);
                                break;

                            case 'ul' :
                                $name = sprintf('%s', $user->user_login);
                                break;

                            case 'uldn' :
                                $name = sprintf('%s (%s)',
                                    $user->user_login, $user->display_name);
                                break;

                            case 'ulfl' :
                                $name = sprintf('%s (%s %s)', $user->user_login,
                                    is_null($user->first_name) ? $na : $user->first_name,
                                    is_null($user->last_name) ? $na : $user->last_name);
                                break;

                            case 'ullf' :
                                $name = sprintf('%s (%s, %s)', $user->user_login,
                                    is_null($user->last_name) ? $na : $user->last_name,
                                    is_null($user->first_name) ? $na : $user->first_name);
                                break;

                            case 'dnul' :
                                $name = sprintf('%s (%s)',
                                    $user->display_name, $user->user_login);
                                break;

                            case 'dn' :
                            case 'none' :
                            default:
                                $name = $user->display_name;
                                break;
                        }
                ?>
                    <option value="<?php echo $user->ID; ?>" <?php 
                        echo (in_array($user->ID, $send_users) ? ' selected="yes"' : '');?>>
                         <?php printf('%s - %s', __('User', MAILUSERS_I18N_DOMAIN), $name); ?>
                    </option>
                <?php 
                    }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top"><label for="subject"><?php _e('Subject', MAILUSERS_I18N_DOMAIN); ?></label></th>
            <td><?php echo mailusers_get_default_mail_format()=='html' ? $subject : '<pre>' . format_to_edit($subject) . '</pre>';?></td>
        </tr>
        <tr>
            <th scope="row" valign="top"><label for="mailcontent"><?php _e('Message', MAILUSERS_I18N_DOMAIN); ?></label></th>
            <td><?php echo mailusers_get_default_mail_format()=='html' ? $mail_content : '<pre>' . wordwrap(strip_tags($mail_content), 80, "\n") . '</pre>';?>
                <textarea rows="10" cols="80" name="mailcontent" id="mailcontent" style="width: 647px; display: none;" readonly="yes"><?php echo $mail_content;?></textarea>
            </td>
        </tr>
        </table>
        
        <p class="submit">
            <input class="button-primary" type="submit" name="Submit" value="<?php _e('Send Email', MAILUSERS_I18N_DOMAIN); ?> &raquo;" />
        </p>    
    </form>    
    
    <?php } ?>
</div>
