<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_CdnActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;
    private $_page;
    function __construct() {
        $this->_config = w3_instance('W3_Config');
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $this->_page = W3_Request::get_string('page');
    }

    /**
     * CDN queue action
     *
     * @return void
     */
    function action_cdn_queue() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');
        $cdn_queue_action = W3_Request::get_string('cdn_queue_action');
        $cdn_queue_tab = W3_Request::get_string('cdn_queue_tab');

        $notes = array();

        switch ($cdn_queue_tab) {
            case 'upload':
            case 'delete':
            case 'purge':
                break;

            default:
                $cdn_queue_tab = 'upload';
        }

        switch ($cdn_queue_action) {
            case 'delete':
                $cdn_queue_id = W3_Request::get_integer('cdn_queue_id');
                if (!empty($cdn_queue_id)) {
                    $w3_plugin_cdn->queue_delete($cdn_queue_id);
                    $notes[] = __('File successfully deleted from the queue.', 'w3-total-cache');
                }
                break;

            case 'empty':
                $cdn_queue_type = W3_Request::get_integer('cdn_queue_type');
                if (!empty($cdn_queue_type)) {
                    $w3_plugin_cdn->queue_empty($cdn_queue_type);
                    $notes[] = __('Queue successfully emptied.', 'w3-total-cache');
                }
                break;

            case 'process':
                $w3_plugin_cdn_normal = w3_instance('W3_Plugin_Cdn');
                $n = $w3_plugin_cdn_normal->cron_queue_process();
                $notes[] = sprintf(__('Number of processed queue items: %d', 'w3-total-cache'), $n);
                break;
        }

        $nonce = wp_create_nonce('w3tc');
        $queue = $w3_plugin_cdn->queue_get();
        $title = __('Unsuccessful file transfer queue.', 'w3-total-cache');

        include W3TC_INC_DIR . '/popup/cdn_queue.php';
    }

    /**
     * CDN export library action
     *
     * @return void
     */
    function action_cdn_export_library() {
        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

        $total = $w3_plugin_cdn->get_attachments_count();
        $title = __('Media Library export', 'w3-total-cache');

        include W3TC_INC_DIR . '/popup/cdn_export_library.php';
    }

    /**
     * CDN export library process
     *
     * @return void
     */
    function action_cdn_export_library_process() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

        $limit = W3_Request::get_integer('limit');
        $offset = W3_Request::get_integer('offset');

        $count = null;
        $total = null;
        $results = array();

        @$w3_plugin_cdn->export_library($limit, $offset, $count, $total, $results);

        $response = array(
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count,
            'total' => $total,
            'results' => $results
        );

        echo json_encode($response);
    }

    /**
     * CDN import library action
     *
     * @return void
     */
    function action_cdn_import_library() {
        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');
        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');

        $cdn = $w3_plugin_cdncommon->get_cdn();

        $total = $w3_plugin_cdn->get_import_posts_count();
        $cdn_host = $cdn->get_domain();

        $title = __('Media Library import', 'w3-total-cache');

        include W3TC_INC_DIR . '/popup/cdn_import_library.php';
    }

    /**
     * CDN import library process
     *
     * @return void
     */
    function action_cdn_import_library_process() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

        $limit = W3_Request::get_integer('limit');
        $offset = W3_Request::get_integer('offset');

        $count = null;
        $total = null;
        $results = array();

        @$w3_plugin_cdn->import_library($limit, $offset, $count, $total, $results);

        $response = array(
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count,
            'total' => $total,
            'results' => $results
        );

        echo json_encode($response);
    }

    /**
     * CDN rename domain action
     *
     * @return void
     */
    function action_cdn_rename_domain() {
        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

        $total = $w3_plugin_cdn->get_rename_posts_count();

        $title = __('Modify attachment URLs', 'w3-total-cache');

        include W3TC_INC_DIR . '/popup/cdn_rename_domain.php';
    }

    /**
     * CDN rename domain process
     *
     * @return void
     */
    function action_cdn_rename_domain_process() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

        $limit = W3_Request::get_integer('limit');
        $offset = W3_Request::get_integer('offset');
        $names = W3_Request::get_array('names');

        $count = null;
        $total = null;
        $results = array();

        @$w3_plugin_cdn->rename_domain($names, $limit, $offset, $count, $total, $results);

        $response = array(
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count,
            'total' => $total,
            'results' => $results
        );

        echo json_encode($response);
    }

    /**
     * CDN export action
     *
     * @return void
     */
    function action_cdn_export() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $w3_plugin_cdn = w3_instance('W3_Plugin_Cdn');

        $cdn_export_type = W3_Request::get_string('cdn_export_type', 'custom');

        switch ($cdn_export_type) {
            case 'includes':
                $title = __('Includes files export', 'w3-total-cache');
                $files = $w3_plugin_cdn->get_files_includes();
                break;

            case 'theme':
                $title = __('Theme files export', 'w3-total-cache');
                $files = $w3_plugin_cdn->get_files_theme();
                break;

            case 'minify':
                $title = __('Minify files export', 'w3-total-cache');
                $files = $w3_plugin_cdn->get_files_minify();
                break;

            default:
            case 'custom':
                $title = __('Custom files export', 'w3-total-cache');
                $files = $w3_plugin_cdn->get_files_custom();
                break;
        }

        include W3TC_INC_DIR . '/popup/cdn_export_file.php';
    }

    /**
     * CDN export process
     *
     * @return void
     */
    function action_cdn_export_process() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');

        $files = W3_Request::get_array('files');

        $upload = array();
        $results = array();

        foreach ($files as $file) {
            $local_path = $w3_plugin_cdncommon->docroot_filename_to_absolute_path($file);
            $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($w3_plugin_cdncommon->docroot_filename_to_uri($file));
            $upload[] = $w3_plugin_cdncommon->build_file_descriptor($local_path, $remote_path);
        }

        $w3_plugin_cdncommon->upload($upload, false, $results);

        $response = array(
            'results' => $results
        );

        echo json_encode($response);
    }

    /**
     * CDN purge action
     *
     * @return void
     */
    function action_cdn_purge() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');

        $title = __('Content Delivery Network (CDN): Purge Tool', 'w3-total-cache');
        $results = array();

        $path = ltrim(str_replace(w3_get_home_url(), '', get_stylesheet_directory_uri()), '/');
        include W3TC_INC_DIR . '/popup/cdn_purge.php';
    }

    /**
     * CDN purge post action
     *
     * @return void
     */
    function action_cdn_purge_post() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');

        $title = __('Content Delivery Network (CDN): Purge Tool', 'w3-total-cache');
        $results = array();

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $files = W3_Request::get_array('files');

        $purge = array();

        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');

        foreach ($files as $file) {
            $local_path = $w3_plugin_cdncommon->docroot_filename_to_absolute_path($file);
            $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($w3_plugin_cdncommon->docroot_filename_to_uri($file));

            $purge[] = $w3_plugin_cdncommon->build_file_descriptor($local_path, $remote_path);
        }

        if (count($purge)) {
            $w3_plugin_cdncommon->purge($purge, false, $results);
        } else {
            $errors[] = __('Empty files list.', 'w3-total-cache');
        }

        $path = str_replace(w3_get_home_url(), '', get_stylesheet_directory_uri());
        include W3TC_INC_DIR . '/popup/cdn_purge.php';
    }

    /**
     * CDN Purge Post
     *
     * @return void
     */
    function action_cdn_purge_attachment() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $results = array();
        $attachment_id = W3_Request::get_integer('attachment_id');

        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

        if ($w3_plugin_cdn->purge_attachment($attachment_id, $results)) {
            w3_admin_redirect(array(
                'w3tc_note' => 'cdn_purge_attachment'
            ), true);
        } else {
            w3_admin_redirect(array(
                'w3tc_error' => 'cdn_purge_attachment'
            ), true);
        }
    }

    /**
     * CDN Test action
     *
     * @return void
     */
    function action_cdn_test() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        w3_require_once(W3TC_LIB_W3_DIR . '/Cdn.php');

        $engine = W3_Request::get_string('engine');
        $config = W3_Request::get_array('config');

        //TODO: Workaround to support test case cdn/a04
        if (!isset($config['host'])) {
            $config = W3_Request::get_string('config');
            $config = json_decode($config, true);
        }

        $config = array_merge($config, array(
            'debug' => false
        ));

        if (!is_array($config['domain'])) {
            $config['domain'] = explode(',', $config['domain']);
        }

        if (w3_is_cdn_engine($engine)) {
            $result = true;
            $error = null;
        } else {
            $result = false;
            $error = __('Incorrect engine.', 'w3-total-cache');
        }
        if (!isset($config['docroot']))
            $config['docroot'] = w3_get_document_root();

        if ($result) {
            $w3_cdn = W3_Cdn::instance($engine, $config);

            @set_time_limit($this->_config->get_integer('timelimit.cdn_test'));

            if ($w3_cdn->test($error)) {
                $result = true;
                $error = __('Test passed', 'w3-total-cache');
            } else {
                $result = false;
                $error = sprintf(__('Error: %s', 'w3-total-cache'), $error);
            }
        }

        $response = array(
            'result' => $result,
            'error' => $error
        );

        echo json_encode($response);
    }


    /**
     * Create container action
     *
     * @return void
     */
    function action_cdn_create_container() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        w3_require_once(W3TC_LIB_W3_DIR . '/Cdn.php');

        $engine = W3_Request::get_string('engine');
        $config = W3_Request::get_array('config');

        $config = array_merge($config, array(
            'debug' => false
        ));

        $result = false;
        $error = __('Incorrect type.', 'w3-total-cache');
        $container_id = '';

        switch ($engine) {
            case 's3':
            case 'cf':
            case 'cf2':
            case 'rscf':
            case 'azure':
                $result = true;
                break;
        }

        if ($result) {
            $w3_cdn = W3_Cdn::instance($engine, $config);

            @set_time_limit($this->_config->get_integer('timelimit.cdn_container_create'));

            if ($w3_cdn->create_container($container_id, $error)) {
                $result = true;
                $error = __('Created successfully.', 'w3-total-cache');
            } else {
                $result = false;
                $error = sprintf(__('Error: %s', 'w3-total-cache'), $error);
            }
        }

        $response = array(
            'result' => $result,
            'error' => $error,
            'container_id' => $container_id
        );

        echo json_encode($response);
    }

    /**
     * S3 bucket location lightbox
     *
     * @return void
     */
    function action_cdn_s3_bucket_location() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $type = W3_Request::get_string('type', 's3');

        $locations = array(
            '' => 'US (Default)',
            'us-west-1' => __('US-West (Northern California)', 'w3-total-cache'),
            'EU' => 'Europe',
            'ap-southeast-1' => __('AP-SouthEast (Singapore)', 'w3-total-cache'),
        );

        include W3TC_INC_DIR . '/lightbox/cdn_s3_bucket_location.php';
    }


    function action_cdn_create_netdna_maxcdn_pull_zone_form() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $type = W3_Request::get_string('type', 'maxcdn');
        include W3TC_INC_DIR . '/lightbox/create_netdna_maxcdn_pull_zone.php';
    }


    /**
     * Create NetDNA/MaxCDN pullzone
     */
    function action_cdn_create_netdna_maxcdn_pull_zone() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $type = W3_Request::get_string('type');
        $name = W3_Request::get_string('name');
        $label = W3_Request::get_string('label');
        w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
        $cdn_engine= $this->_config->get_string('cdn.engine');

        $authorization_key = $this->_config->get_string("cdn.$cdn_engine.authorization_key");
        $alias = $consumerkey = $consumersecret = '';

        if ($authorization_key) {
            $keys = explode('+', $authorization_key);
            if (sizeof($keys) == 3) {
                list($alias, $consumerkey, $consumersecret) =  $keys;
            }
        }
        $api = new NetDNA($alias, $consumerkey, $consumersecret);
        $url = w3_get_home_url();
        $zone = array();
        $zone['name'] = $name;
        $zone['label'] = $label;
        $zone['url'] = $url;
        try {
            $response = $api->create_pull_zone($zone);
            try {
                $this->_config->set('cdn.enabled', true);
                if (!$this->_config->get_array("cdn.$cdn_engine.domain")) {
                    $this->_config->set("cdn.$cdn_engine.domain", array("$name.$alias.netdna-cdn.com"));
                }
                $this->_config->save();
            } catch (Exception $ex) {}
            echo json_encode(array('status' => 'success','message' => 'Pull Zone created.', 'temporary_url' => "$name.$alias.netdna-cdn.com", 'data' => $response));
        } catch (Exception $ex) {
            echo json_encode(array('status' => 'error','message' => $ex->getMessage()));
        }
    }
}
