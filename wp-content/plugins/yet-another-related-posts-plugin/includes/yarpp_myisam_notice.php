<?php

if (isset($_POST['myisam_override'])) {

    yarpp_set_option('myisam_override', true);
    $enabled = $yarpp->enable_fulltext();

    if($enabled){

        update_option('yarpp_fulltext_disabled', 0);
        echo(
            '<div class="updated" style="padding:5px">'.
            __(
                'The MyISAM check has been overridden. You may now use the "consider titles" and "consider bodies" relatedness criteria.',
                'yarpp'
            ).
            "</div>"
        );

    } else {

        yarpp_set_option('myisam_override', 0);
        echo(
            '<div class="updated" style="padding:5px">'.
                '<span style="color:red;font-weight:bold">Fulltext Index creation did not work!</span><br/>'.
                'Trying to force fulltext indexing on your table resulted in an error. Your posts table does not have fulltext indexing capabilities.<br/>'.
                'The "consider titles" and "consider bodies" relatedness criteria will remain disabled.'.
            '</div>'
        );

    }
}

$table_type = $yarpp->diagnostic_myisam_posts();
if ((bool) $table_type !== true) $yarpp->disable_fulltext();

if (!(bool) yarpp_get_option('myisam_override') && (bool) $yarpp->diagnostic_fulltext_disabled()) {
    echo(
        "<div class='updated' style='padding:15px'>".
            '<p>'.
                '<strong>"Consider Titles"</strong> and <strong>"Consider Bodies"</strong> are currently disabled.'.
                '&nbsp;&nbsp;<a href="#" id="yarpp_fulltext_expand">Show Details [+]</a>'.
            '</p>'.
            '<div id="yarpp_fulltext_details" class="hidden">'.
            '<p>'.
            sprintf(
                'YARPP&#39;s "consider titles" and "consider bodies" relatedness criteria require your <code>%s</code> '.
                'table to use the <code>MyISAM</code> engine'.
                'fulltext indexing feature. Unfortunately your table seems to be using the <code>%s</code> engine. '.
                'Because fulltext indexing is not supported by your current table engine, these two options have been disabled.',
                $wpdb->posts,
                $table_type
            ).
            '</p>'.
            '<p>'.
            sprintf(
                'To restore these features, please do the following:<br/>'.
                '<ol>'.
                    '<li>'.
                        'Convert your <code>%s</code> table to <code>MyISAM</code> engine by executing the '.
                        'following SQL code on your MySQL client or terminal:<br/>'.
                        '<code style="display:inline-block;margin:1.5em 1em">ALTER TABLE `%s` ENGINE = MyISAM;</code>'.
                    '</li>',
                $wpdb->posts,
                $wpdb->posts
            ).
            sprintf(
                '<li>'.
                    'Once your <code>%s</code> table has been successfully converted to the <code>MyISAM</code> engine, '.
                    'click the button below to create the fulltext indices.<br/>',
                $wpdb->posts
            ).
                '<form method="post" style="display:inline-block;margin:1.5em 1em">'.
                    "<input type='submit' class='button' name='myisam_override' value='Create fulltext indexes'/>".
                "</form>".
                '</li>'.
                '</ol>'.
            "</p>".
            '<p>'.
                'Note that, although no data should be lost by altering the table&#39;s engine, it is always recommended to perform a '.
                'full backup of the data before attempting to perform changes to your database.<br/>'.
                'See MySQL <a href="http://dev.mysql.com/doc/refman/5.0/en/storage-engines.html" target="_blank">storage engines</a> '.
                'documentation for details on MySQL engines.'.
            '</p>'.
            '</div>'.
        "</div>"
    );
}