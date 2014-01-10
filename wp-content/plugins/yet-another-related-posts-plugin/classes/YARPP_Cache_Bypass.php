<?php

class YARPP_Cache_Bypass extends YARPP_Cache {

    public $name                = "bypass";
    public $demo_time           = false;

    private $related_postdata   = array();
    private $related_IDs        = array();
    private $demo_limit         = 0;

    /**
     * SETUP/STATUS
     */
    function __construct(&$core) {
        parent::__construct($core);
    }

    public function is_enabled() {
        return true; // always enabled.
    }

    public function cache_status() {
        return 0; // always uncached
    }

    public function stats() {
        return array(); // always unknown
    }

    public function uncached($limit = 20, $offset = 0) {
        return array(); // nothing to cache
    }

    /**
     * MAGIC FILTERS
     */
    public function where_filter($arg) {
        global $wpdb;

        // modify the where clause to use the related ID list.
        if (!count($this->related_IDs)) $this->related_IDs = array(0);

        $arg = preg_replace("!{$wpdb->posts}.ID = \d+!","{$wpdb->posts}.ID in (".join(',',$this->related_IDs).")",$arg);

        // if we have recent set, add an additional condition
        if ((bool) $this->args['recent']) $arg .= " and post_date > date_sub(now(), interval {$this->args['recent']}) ";

        return $arg;
    }

    public function orderby_filter($arg) {
        global $wpdb;

        /*
         * Only order by score if the score function is added in fields_filter,
         * which only happens if there are related posts in the post-data.
         */
        if ($this->score_override && is_array($this->related_postdata) && count($this->related_postdata)) {
            return str_replace("$wpdb->posts.post_date","score",$arg);
        }

        return $arg;
    }

    public function fields_filter($arg) {
        global $wpdb;

        if (is_array($this->related_postdata) && count($this->related_postdata)) {
            $scores = array();
            foreach ($this->related_postdata as $related_entry) {
                $scores[] = " WHEN {$related_entry['ID']} THEN {$related_entry['score']}";
            }
            $arg .= ", CASE {$wpdb->posts}.ID" . join('',$scores) ." END as score";
        }
        return $arg;
    }

    public function demo_request_filter($arg) {
        global $wpdb;

        $wpdb->query("set @count = 0;");

        $loremipsum =
        'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cras tincidunt justo a urna. Ut turpis. Phasellus'.
        'convallis, odio sit amet cursus convallis, eros orci scelerisque velit, ut sodales neque nisl at ante. '.
        'Suspendisse metus. Curabitur auctor pede quis mi. Pellentesque lorem justo, condimentum ac, dapibus sit '.
        'amet, ornare et, erat. Quisque velit. Etiam sodales dui feugiat neque suscipit bibendum. Integer mattis. '.
        'Nullam et ante non sem commodo malesuada. Pellentesque ultrices fermentum lectus. Maecenas hendrerit neque ac '.
        'est. Fusce tortor mi, tristique sed, cursus at, pellentesque non, dui. Suspendisse potenti.';

        return
            "SELECT
                SQL_CALC_FOUND_ROWS ID + {$this->demo_limit} as ID,
                post_author,
                post_date,
                post_date_gmt,
                '{$loremipsum}' as post_content,
		        concat('".__('Example post ','yarpp')."', @count:=@count+1) as post_title,
		        0 as post_category,
		        '' as post_excerpt,
		        'publish' as post_status,
		        'open' as comment_status,
		        'open' as ping_status,
		        '' as post_password,
		        concat('example-post-',@count) as post_name,
		        '' as to_ping,
		        '' as pinged,
		        post_modified,
		        post_modified_gmt,
		        '' as post_content_filtered,
		        0 as post_parent,
		        concat('PERMALINK',@count) as guid,
		        0 as menu_order,
		        'post' as post_type,
		        '' as post_mime_type,
		        0 as comment_count,
		        'SCORE' as score
		FROM $wpdb->posts
		ORDER BY ID DESC LIMIT 0, {$this->demo_limit}";
    }

    public function limit_filter($arg) {
        global $wpdb;
        return ($this->online_limit) ? " limit {$this->online_limit} " : $arg;
    }

    /**
     * RELATEDNESS CACHE CONTROL
     */
    public function begin_yarpp_time($reference_ID, $args) {
        global $wpdb;

        $this->yarpp_time = true;
        $options = array(
            'threshold',
            'show_pass_post',
            'past_only',
            'weight',
            'require_tax',
            'exclude',
            'recent',
            'limit'
        );
        $this->args = $this->core->parse_args($args, $options);

        $this->related_postdata = $wpdb->get_results($this->sql($reference_ID, $args), ARRAY_A);
        $this->related_IDs = wp_list_pluck($this->related_postdata, 'ID');

        add_filter('posts_where',array(&$this,'where_filter'));
        add_filter('posts_orderby',array(&$this,'orderby_filter'));
        add_filter('posts_fields',array(&$this,'fields_filter'));
        add_filter('post_limits',array(&$this,'limit_filter'));

        add_action('pre_get_posts',array(&$this,'add_signature'));
        add_action('parse_query',array(&$this,'set_score_override_flag')); // sets the score override flag.
    }

    public function begin_demo_time($limit) {
        $this->demo_time = true;
        $this->demo_limit = $limit;

        add_action('pre_get_posts',array(&$this,'add_signature'));
        add_filter('posts_request',array(&$this,'demo_request_filter'));
    }

    public function end_yarpp_time() {
        $this->yarpp_time = false;

        remove_filter('posts_where',array(&$this,'where_filter'));
        remove_filter('posts_orderby',array(&$this,'orderby_filter'));
        remove_filter('posts_fields',array(&$this,'fields_filter'));
        remove_filter('post_limits',array(&$this,'limit_filter'));

        remove_action('pre_get_posts',array(&$this,'add_signature'));
        remove_action('parse_query',array(&$this,'set_score_override_flag'));
    }

    public function end_demo_time() {
        $this->demo_time = false;

        remove_action('pre_get_posts',array(&$this,'add_signature'));
        remove_filter('posts_request',array(&$this,'demo_request_filter'));
    }

    // @return YARPP_NO_RELATED | YARPP_RELATED
    // @used by enforce
    protected function update($reference_ID) {
        global $wpdb;

        return YARPP_RELATED;
    }

    public function related($reference_ID = null, $related_ID = null) {
        global $wpdb;

        if ( !is_int( $reference_ID ) && !is_int( $related_ID ) ) {
            _doing_it_wrong( __METHOD__, 'reference ID and/or related ID must be set', '3.4' );
            return;
        }

        // reverse lookup
        if ( is_int($related_ID) && is_null($reference_ID) ) {
            _doing_it_wrong( __METHOD__, 'YARPP_Cache_Bypass::related cannot do a reverse lookup', '3.4' );
            return;
        }

        $results = $wpdb->get_results($this->sql($reference_ID), ARRAY_A);
        if ( !$results || !count($results) )
            return false;

        $results_ids = wp_list_pluck( $results, 'ID' );
        if ( is_null($related_ID) ) {
            return $results_ids;
        } else {
            return in_array( $related_ID, $results_ids );
        }
    }
}