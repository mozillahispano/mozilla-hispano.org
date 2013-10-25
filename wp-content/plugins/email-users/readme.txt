=== Email Users ===
Contributors: vprat, mpwalsh8, marvinlabs
Donate link: http://michaelwalsh.org/wordpress/wordpress-plugins/email-users/
Tags: email, users, list, admin
Requires at least: 3.3
Tested up to: 3.6.1
Stable tag: 4.6.1

A plugin for WordPress which allows you to send an email to the registered blog users. Users can send personal emails to each other. Power users can email groups of users and even notify group of users of posts.

== Description ==

A plugin for WordPress which allows you to send an email to the registered blog users. Users can send personal emails to each other. Power users can email groups of users and even notify group of users of posts.

All the instructions for installation, the support forums, a FAQ, etc. can be found on the [plugin home page](http://wordpress.org/extend/plugins/email-users/) or on the [plugin overview page](http://michaelwalsh.org/wordpress/wordpress-plugins/email-users/).

This plugin is available under the GPL license, which means that it's free. If you use it for a commercial web site, if you appreciate my efforts or if you want to encourage me to develop and maintain it, please consider making a donation using Paypal, a secured payment solution. You just need to click the donate button on the the [plugin overview page](http://michaelwalsh.org/wordpress/wordpress-plugins/email-users/) and follow the instructions.

== Custom Filter Usage ==

Email Users provides the ability to send email to a very specific set of users using a custom meta filter.  To create a special mail list, you will need to add something similar to the following to your theme's functions.php file or create a separate plugin file.

The mailusers_register_user_custom_meta_filter() and mailusers_register_group_custom_meta_filter() actions each take 3-4 parameters:
1.  Label - text that will appear on the WordPress Email-Users menu (users) or in the Recipient List (groups).
1.  Meta Key - the meta key to search for in the user meta table.
1.  Meta Value - the value to match against in the user meta table.
1.  Meta Compare - optional, defaults to '='.  The type of comparison to be performed.

This example will filter the user list to only those users where the first name is Alex.
`
add_action( 'mailusers_user_custom_meta_filter', 'first_name_alex', 5 );

function first_name_alex()
{
    mailusers_register_user_custom_meta_filter('First Name: Alex', 'first_name', 'Alex');
}
`

Regular SQL comparisons (=, !=, etc.) can be performed.  Wildcard matches (LIKE, NOT LIKE) are not yet supported due to how the WordPress get_users() API currently handles LIKE comparison.  A patch has been submitted and hopefully it will be addressed in WordPress 3.6.  Once addressed, you will be able to create filters like the one below to specifically match last names which begin with the letter M.

`
add_action( 'mailusers_user_custom_meta_filter', 'last_names_starting_with_m', 5 );

function last_names_starting_with_m()
{
    mailusers_register_user_custom_meta_filter('Last Name: M', 'last_name', 'M%', 'LIKE');
}
`

In addition to filtering on User Meta data to build a custom list of users, you can now define custom groups based on User Meta data.

`
add_action( 'mailusers_group_custom_meta_filter', 'send_to_fire_department', 5 );

function send_to_fire_department()
{
    mailusers_register_group_custom_meta_filter('Fire Department', 'department', 'fire');
}


add_action( 'mailusers_group_custom_meta_filter', 'send_to_police_department', 5 );

function send_to_police_department()
{
    mailusers_register_group_custom_meta_filter('Police Department', 'department', 'police');
}
`

In addition to defining specific Meta Key and Value pairs, Email Users also supports a filter to generate the Meta Group filters based on a Meta Key.  The Meta Key filter supports two optional arguments - a Meta Value and a function callback to generate the label.  Neither is required.  When the label callback is used, it receives two arguments, both strings, the Meta Key and Meta Value.  It must return a string.

`
//  Define action to send to blog followers
add_action( 'mailusers_group_custom_meta_key_filter', 'send_to_my_blog_followers', 5 );

function send_to_my_blog_followers()
{
    mailusers_register_group_custom_meta_key_filter('blog_follower');
}

function send_to_departments_label($mk, $mv)
{
    return(ucwords($mk) . ' = ' . ucwords($mv)) ;
}

//  Define action to send to departments using custom callback to generate the label
add_action( 'mailusers_group_custom_meta_key_filter', 'send_to_departments', 5 );

function send_to_departments()
{
    mailusers_register_group_custom_meta_key_filter('department', null, 'send_to_departments_label');
}

function send_to_departments_label($mk, $mv)
{
    return(ucwords($mk) . ' = ' . ucwords($mv)) ;
}
`

New in v4.5.0 is an action, *mailusers_update_custom_meta_filters*, which can be used to dynamically update Meta Filters before they're used for recipient selection or email address retrieval.  The example below leverages the Meta Key *Department* and its various values to define and update a new Meta Key called *publicworks*.  Anytime a Group Email is sent or Post/Page notification is initiated, this action will fire and rebuild the *publicworks* meta key based on the values of the *department* meta key.  This sort of action could be used to create more complex meta value relationships or to integrate other plugins.

`
add_action( 'mailusers_group_custom_meta_filter', 'send_to_public_works', 5 );

function send_to_public_works()
{
    mailusers_register_group_custom_meta_filter('Public Works', 'publicworks', true);
}

add_action( 'mailusers_update_custom_meta_filters', 'update_publicworks_meta_filter', 5 );

function update_publicworks_meta_filter()
{
    $pw_mk = 'publicworks' ;
    $dept_mk = 'department' ;

    //  Define the valid matches - the array keys match user
    //  meta keys and the array values match the user meta values.
    //
    //  The array could contain a mixed set of meta keys and values
    //  in order to group users based on an arbitrary collection of
    //  user meta data.

    $publicworks = array(
        array($dept_mk => 'fire'),
        array($dept_mk => 'police'),
        array($dept_mk => 'water and sewer'),
        array($dept_mk => 'parks and recreation'),
    ) ;

    //  Remove all instances of the Public Works meta key
    //  to account for employees no longer with Public Works
	$uq = new WP_User_Query(array('meta_key' => $pw_mk)) ;

    foreach ($uq->get_results() as $u)
        delete_user_meta($u->ID, $pw_mk) ;

    //  Loop through the departs and select Users accordingly
    foreach  ($publicworks as $pw)
    {
    	$uq = new WP_User_Query(array('meta_key' => $dept_mk, 'meta_value' => $pw[$dept_mk])) ;

        //  Loop through the users in the department and tag them as Public Works employees
        foreach ($uq->get_results() as $u)
            update_user_meta($u->ID, $pw_mk, true) ;
    }
}
`

== Changelog ==

= Version 4.6.1 =
* Updated Spanish language translation files. (thank you Ponç J. Llaneras)
* New Bulgarian language translation files.  (thank you Borisa Djuraskovic)
* Fixed formatting issue in the plugin README file.

= Version 4.6.0 =
* Significantly improved debug functionality to chase down mail header issues.
* Check added to determine if wp_mail() has been overloaded by a theme or plugin.
* Rewrite of mailusers_send_mail() fucntion to construct headers as arrays insyead of as string.  The string would sometimes not break correctly and recognize the Bcc: field.
* Implemented new email footer option.
* Cleaned up presentation of Options page.
* Implemented new omit display names option.
* Updated language translation files.

= Version 4.5.5 =
* Forgot to register settings for new MIME-Type and X-Mailer settings.

= Version 4.5.4 =
* Version bump due to bad tagging in WordPress plugin repository.

= Version 4.5.3 =
* Fixed bug in Test Notification which failed to incorporate %POST_AUTHOR% keyword.
* Replaced bold font in Email-Users Info meta box on settings page.
* Added %POST_CONTENT% substitution keyword.
* Refactored construction of email headers.
* Added option to specifically CC sender.
* Fixed sorting problems on User Settings page.
* Added Search to User Settings page.
* Replaced First/Last Names on User Settings page with Display Name - needed to eliminate complex query.
* Replaced complex SQL query with proper call to get_users() for User Settings page, facilitated fixing several bugs on User Settings page.
* Fixed bug which preventing showing all posts and/or pages in Notify dropdown.
* Added translation for value of Role when used in select boxes.
* Resolved duplicate MIME-Version and X-Mailer header problem.
* Added new options to optionally add MIME-Version and X-Mailer headers as by default, they are added by WordPress and shouldn't be added by Email Users.
* Improved Information Panel on Email Users settings page, now shows status of any filters which could affect Email Users.

= Version 4.5.2 =
* Added Dashboard Widget to report number of users who accept each type of email and default settings.
* Added Message to Settings page to warn Admin when no users will not receive emails.
* Added Meta Box to Settings page to report number of users who accept each type of email.

= Version 4.5.1 =
* Fixed Post Excerpt to use WordPress API to allow usage of filters.
* Added support for %POST_AUTHOR% keyword replacement.
* Added Italian language translation.

= Version 4.5.0 =
* Added CSS class and ID to Post and Page Notification post boxes so they can be styled or easily hidden via CSS.
* Added integration with User Groups plugin.
* Added integration with User Access Manager Plugin.
* Cleaned up recipient selection for Group Email and Post/Page Notifications so it includes Filters, Roles, and groups from integrated plugins (when enabled).
* Added integration pane to Settings page to note which plugins Email-Users recorgnizes and enables integration with.
* Added *mailusers_update_custom_meta_filters* action to update of dynamic meta filters prior to their use.  Useful to creating and updating meta values based on other meta data or plugins.

= Version 4.4.4 =
* Bumped version because the tag wasn't done correctly.

= Version 4.4.3 =
* Fixed typo which prevented saving Sender Exclude option.
* Fixed bug with User Meta filters.
* Fixed bug with duplicate emails being sent in some instances when both Roles and Users selected.

= Version 4.4.2 =
* Fixed bug which caused email to be sent to all recipients instead of just those in a specific group.
* Addressed deprecated update_usermeta() usage.

= Version 4.4.1 =
* Added German translation files (thank you Tobias Bechtold).
* Fixed bug in Send to Groups where number of users receiving email was wrong.
* Added internationalization support to Send to Group status messages.
* Added support for sending email to users based on a custom meta filter.
* Added support for sending email to groups based on a custom meta filter.
* Added suppport for providing a bounce email address.
* Fixed bug in Options form which prevented translation of strings.
* Added support for defining email group meta filters based on meta key.
* Implemented solution from @maximinime to fix lost Email-Users settings.
* Removed invalid references to Marvin Labs.
* Fixed bug where Post Excerpt wasn't being used in email when present.
* Updated French and Spanish language files.

= Version 4.3.21 =
* Updated Spanish translation files.

= Version 4.3.20 =
* ReadMe file updates.

= Version 4.3.19 =
* Fixed missing DIV on landing page causing footer to appear in wrong spot.
* Added some more marketing information.
* Tweaked some more wording to be consistent with other areas of the plugin.
* Removed page layout development code.

= Version 4.3.18 =
* Updated plugin landing page to be cleaner and use modern WordPress styling.

= Version 4.3.17 =
* Fixed "%FROM_NAME%" does not get replaced properly in notifications when using override.
* Updated Plugin Settings page to be cleaner and use modern WordPress styling.

= Version 4.3.16 =
* Fixed "%FROM_NAME%" does not get replaced in notifications

= Version 4.3.15 =
* Replaced use of deprecated function *the_editor()* with *wp_editor().
* Fixed Javascript conflict which affects Dashboard and Menu Management resulting from enqueing the WordPress *'post'* library.
* Fixed bug where user settings are not saved correctly when toggling user setting control.
* Fixed bug when the dollar sign character ($) appears in the content of a page or post.
* Added option to include sender in recipient list.
* Numerous updates to make translation easier.
* Updated Spanish and French translation files.

= Version 4.3.14 =
* Bump in version number because one was missed in 4.3.13 preventing automatic updates from the WordPress plugin repository.  Duh.

= Version 4.3.13 =
* Bump in version number because one was missed in 4.3.12 preventing automatic updates from the WordPress plugin repository.

= Version 4.3.12 =
* Fixed bug(s) which prevented users with capabilities to email other users from doing so.
* Initial inclusion of Spanish language translation files (courtesy of Ponç J. Llaneras).
* Updated French language translation files.

= Version 4.3.11 =
* Fixed problem when using BCC limits where the last "chunk" of addresses were never sent the email.

= Version 4.3.10 =
* Fixed a problem with the "To:" header when sending email to a single user which appeared on some platforms (e.g. one IIS system that I know of).

= Version 4.3.9 =
* Removed some debug messages which slipped through, one of which caused a PHP error.

= Version 4.3.8 =
* Fixed a problem with Send to Users ignoring the Mass Email setting when selecting multiple users.
* Added messages to alert user when addresses were filtered out due to Mass Email setting.
* Added internationalization support for some additional messages.
* Inclusion of Russian language translation files.
* Fixed bug which resulted in duplicate emails for recipients when both roles and users were selected.

= Version 4.3.7 =
* Fixed minor typos.
* Fixed version number so Dashboard update will kick off.

= Version 4.3.6 =
* Fixed bug in User Settings Table Rows setting which stored number of rows in the wrong option field.
* Added options to set an "override" From Name and/or From Email Address which can be used when sending Mass or Post/Page Notifications.
* Added ability to override sender name and/or email address when sending Mass Email or Post/Page Notifications.  The default remains to use the name and email address from the currently logged in user.  When the Override Address is set on the Email Users Plugin Settings Page, the user will be presented with a Radio Button choice on email and notification pages where they can send the email using their login (default behavior) or select the Override Address and From Name.
* Added new option to enable process short codes embedded in posts and pages when sending notifications.
* Fixed a bunch of text messages to support translation.
* Initial inclusion of French language translation files (courtesy of Emilie DCCLXI).
* Inclusion of Persian language translation files.
* Fixed bug on User Settings page where number_format() warning was issued.  Reported on the WordPress.org Support Forum.

= Version 4.3.5 =
* Added some more values for the BCC limit setting
* Corrected one message for translation

= Version 4.3.4 =
* Fixed bug which caused some user recipients to be reported as having invalid email addresses.
* Added translation support to several error messages where it was missing.
* Fixed several more typos.

= Version 4.3.3 =
* Fixed typos which appears on the user profile page for the options to receive email and notifications.
* Added an option to allow the Admin User (requires role 'edit_users') to enable or disable users ability to control their Email Users settings.  By default users can control their own settings.

= Version 4.3.2 =
* Hid "Notify Users" submenu on Pages and Posts Menu for users who don't have the proper capability.
* Fixed problem where debug code was preventing mail from being sent to users.

= Version 4.3.1 =
* Migrated custom SQL query over to WordPress get_users() API.  Use of this API requires WordPress 3.3 or later.
* Fixed SQL bug in User Settings table when changing the column sort.
* Fixed plugin activation error which appears when running WordPress 3.4.

= Version 4.3.0 =
* Replaced slow, inefficient SQL queries to build "nice" user names with more efficient queries.  Thanks to the WP Hackers mailing list for the assistance.

= Version 4.2.0 =
* Fixed serious flaw in User Setting implementation which uses the WP_List_Table class.  The logic was not accounting for large number of users and would slow to a crawl because it was processing the entire list of users instead of just a subset for each page.

= Version 4.1.0 =
* Fixed bug which prevented default settings for a user from being added when a user was registered.
* Added new plugin options to set default state for notifications and mass email.  It is now possible to default new users to any combination of email and notifications settings.
 
= Version 4.0.0 =
* Code updated to use WordPress Menu API for Dashboard Menus.
* Code updated to use WordPress Options API for plugin settings.
* Updated plugin to eliminate WordPress deprecated function notices.
* Added new User Settings page to the pluin menu where bulk settings can be applied to one or more users.  This page makes reviewing user settings much easier than looking at users one at a time.
