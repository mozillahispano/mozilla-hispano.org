<?php
/**
 * Uninstall procedure.
 * @since Version 4.0.7
 * @author Eliezer Vargas
 */

/* Exit if plugin delete hasn't be called */
if (!defined('WP_UNINSTALL_PLUGIN')) exit();

global $wpdb;

/* Yarpp option names */
$optNames = array(
    'yarpp',
    'yarpp_fulltext_disabled',
    'yarpp_optin_timeout',
    'yarpp_version',
    'yarpp_version_info',
    'yarpp_version_info_timeout',
    'yarpp_activated'
);

/**
 * Loop through option array and delete the option and clear and drop cache tables.
 * @param array $opts Array of yarpp's options
 * @param object $wpdb Wordpress db global
 */
function clean(Array $opts, $wpdb){

    foreach($opts as $opt){
        /* if option exist... delete it */
        if(get_option($opt)) delete_option($opt);
    }/*end foreach*/

    /* Truncate, clear wp cache and drop cache tables */
    $wpdb->query('TRUNCATE TABLE `'.$wpdb->prefix.'yarpp_related_cache`');
    wp_cache_flush();
    $wpdb->query('DROP TABLE `'.$wpdb->prefix.'yarpp_related_cache`');

}/*end clean */

/* Select right procedure for single or multi site */
if(is_multisite()) {

    /* Get sites ids */
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    /* Get main site id */
    $original_blog_id = get_current_blog_id();

    /* loop through all sites */
    foreach($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        clean($optNames, $wpdb);
    }/*end foreach*/

    switch_to_blog( $original_blog_id );

} else {
    clean($optNames, $wpdb);
}/*end if*/