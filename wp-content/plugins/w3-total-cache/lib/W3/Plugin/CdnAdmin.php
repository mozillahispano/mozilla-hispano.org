<?php

/**
 * W3 Total Cache CDN Plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/http.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_CdnAdmin
 */
class W3_Plugin_CdnAdmin extends W3_Plugin {
    /**
     * Instantiates worker with common functionality on demand
     *
     * @return W3_Plugin_CdnCommon
     */
    function _get_common() {
        return w3_instance('W3_Plugin_CdnCommon');
    }

    /**
     * Activation action
     */
    function activate() {
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
        global $wpdb;

        $this->schedule();
        $this->schedule_upload();

        if ($this->_config->get_boolean('cdn.enabled') && !w3_is_cdn_mirror($this->_config->get_string('cdn.engine')) && !$this->table_create(true)) {
            $error = sprintf('Unable to create table <strong>%s%s</strong>: %s', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE, $wpdb->last_error);

            w3_activate_error($error);
        }
        if ($this->_config->get_boolean('cdn.enabled')) {
            if (w3_can_modify_rules(w3_get_browsercache_rules_cache_path())) {
                try {
                    $this->write_rules();
                } catch (Exception $e)
                {}
            }
        }
    }

    /**
     * Deactivation action
     */
    function deactivate() {
        $errors = array('errors' => array(), 'errors_short_form' => array(), 'ftp_form' => null);
        $results = array();

        $this->table_delete();
        $this->unschedule_upload();
        $this->unschedule();
        if (w3_can_modify_rules(w3_get_browsercache_rules_cache_path())) {
            $results[] = $this->remove_rules_with_message();
        }
        if ($results) {
            foreach ($results as $result) {
                if ($result['errors']) {
                    $errors['errors'] = array_merge($errors['errors'], $result['errors']);
                    $errors['errors_short_form'] = array_merge($errors['errors_short_form'], $result['errors_short_form']);
                    if (!isset($errors['ftp_form']))
                        $errors['ftp_form'] = $result['ftp_form'];
                }
            }
        }
        return $errors;

    }

    /**
     * Called from admin interface after configuration is changed
     */
    function after_config_change() {
        $this->schedule();
        $this->schedule_upload();
    }

    /**
     * Schedules cron events
     */
    function schedule() {
        if ($this->_config->get_boolean('cdn.enabled') && !w3_is_cdn_mirror($this->_config->get_string('cdn.engine'))) {
            if (!wp_next_scheduled('w3_cdn_cron_queue_process')) {
                wp_schedule_event(current_time('timestamp'), 'w3_cdn_cron_queue_process', 'w3_cdn_cron_queue_process');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Schedule upload event
     */
    function schedule_upload() {
        if ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cdn.autoupload.enabled') && !w3_is_cdn_mirror($this->_config->get_string('cdn.engine'))) {
            if (!wp_next_scheduled('w3_cdn_cron_upload')) {
                wp_schedule_event(current_time('timestamp'), 'w3_cdn_cron_upload', 'w3_cdn_cron_upload');
            }
        } else {
            $this->unschedule_upload();
        }
    }

    /**
     * Unschedules cron events
     */
    function unschedule() {
        if (wp_next_scheduled('w3_cdn_cron_queue_process')) {
            wp_clear_scheduled_hook('w3_cdn_cron_queue_process');
        }
    }

    /**
     * Unschedule upload event
     */
    function unschedule_upload() {
        if (wp_next_scheduled('w3_cdn_cron_upload')) {
            wp_clear_scheduled_hook('w3_cdn_cron_upload');
        }
    }

    /**
     * Create queue table
     *
     * @param bool $drop
     * @return int
     */
    function table_create($drop = false) {
        global $wpdb;

        if ($drop) {
            $sql = sprintf('DROP TABLE IF EXISTS `%s%s`', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

            $wpdb->query($sql);
        }

        $sql = sprintf("CREATE TABLE IF NOT EXISTS `%s%s` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `local_path` varchar(500) NOT NULL DEFAULT '',
            `remote_path` varchar(500) NOT NULL DEFAULT '',
            `command` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - Upload, 2 - Delete, 3 - Purge',
            `last_error` varchar(150) NOT NULL DEFAULT '',
            `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`),
            UNIQUE KEY `path` (`local_path`, `remote_path`),
            KEY `date` (`date`)
        ) /*!40100 CHARACTER SET latin1 */", $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

        $wpdb->query($sql);

        return $wpdb->result;
    }

    /**
     * Delete queue table
     *
     * @return int
     */
    function table_delete() {
        global $wpdb;

        $sql = sprintf('DROP TABLE IF EXISTS `%s%s`', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

        return $wpdb->query($sql);
    }

    /**
     * Purge attachment
     *
     * Upload _wp_attached_file, _wp_attachment_metadata, _wp_attachment_backup_sizes
     *
     * @param integer $attachment_id
     * @param array $results
     * @return boolean
     */
    function purge_attachment($attachment_id, &$results) {
        $files = $this->_get_common()->get_attachment_files($attachment_id);

        return $this->_get_common()->purge($files, false, $results);
    }

    /**
     * Updates file date in the queue
     *
     * @param integer $queue_id
     * @param string $last_error
     * @return integer
     */
    function queue_update($queue_id, $last_error) {
        global $wpdb;

        $sql = sprintf('UPDATE %s SET last_error = "%s", date = NOW() WHERE id = %d', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE, $wpdb->escape($last_error), $queue_id);

        return $wpdb->query($sql);
    }

    /**
     * Removes from queue
     *
     * @param integer $queue_id
     * @return integer
     */
    function queue_delete($queue_id) {
        global $wpdb;

        $sql = sprintf('DELETE FROM %s WHERE id = %d', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE, $queue_id);

        return $wpdb->query($sql);
    }

    /**
     * Empties queue
     *
     * @param integer $command
     * @return integer
     */
    function queue_empty($command) {
        global $wpdb;

        $sql = sprintf('DELETE FROM %s WHERE command = %d', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE, $command);

        return $wpdb->query($sql);
    }

    /**
     * Returns queue
     *
     * @param integer $limit
     * @return array
     */
    function queue_get($limit = null) {
        global $wpdb;

        $sql = sprintf('SELECT * FROM %s%s ORDER BY date', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

        if ($limit) {
            $sql .= sprintf(' LIMIT %d', $limit);
        }

        $results = $wpdb->get_results($sql);
        $queue = array();

        if ($results) {
            foreach ((array) $results as $result) {
                $queue[$result->command][] = $result;
            }
        }

        return $queue;
    }

    /**
     * Process queue
     *
     * @param integer $limit
     */
    function queue_process($limit) {
        $items = 0;
        
        $commands = $this->queue_get($limit);
        $force_rewrite = $this->_config->get_boolean('cdn.force.rewrite');

        if (count($commands)) {
            $cdn = $this->_get_common()->get_cdn();

            foreach ($commands as $command => $queue) {
                $files = array();
                $results = array();
                $map = array();

                foreach ($queue as $result) {
                    $files[] = $this->_get_common()->build_file_descriptor($result->local_path, $result->remote_path);
                    $map[$result->local_path] = $result->id;
                    $items++;
                }

                switch ($command) {
                    case W3TC_CDN_COMMAND_UPLOAD:
                        $dispatcher = w3_instance('W3_Dispatcher');
                        foreach ($files as $file) {
                            $local_file_name = $file['local_path'];
                            $remote_file_name = $file['remote_path'];
                            if (!file_exists($local_file_name)) {
                                $dispatcher->create_file_for_cdn($local_file_name);
                            }
                        }

                        $cdn->upload($files, $results, $force_rewrite);
                        
                        foreach ($results as $result) {
                            if ($result['result'] == W3TC_CDN_RESULT_OK) {
                                $dispatcher->on_cdn_file_upload($result['local_path']);
                            }
                        }
                        break;

                    case W3TC_CDN_COMMAND_DELETE:
                        $cdn->delete($files, $results);
                        break;

                    case W3TC_CDN_COMMAND_PURGE:
                        $cdn->purge($files, $results);
                        break;
                }

                foreach ($results as $result) {
                    if ($result['result'] == W3TC_CDN_RESULT_OK) {
                        $this->queue_delete($map[$result['local_path']]);
                    } else {
                        $this->queue_update($map[$result['local_path']], $result['error']);
                    }
                }
            }
        }
        
        return $items;
    }

    /**
     * Export library to CDN
     *
     * @param integer $limit
     * @param integer $offset
     * @param integer $count
     * @param integer $total
     * @param array $results
     * @return void
     */
    function export_library($limit, $offset, &$count, &$total, &$results) {
        global $wpdb;

        $count = 0;
        $total = 0;

        $upload_info = w3_upload_info();

        if ($upload_info) {
            $sql = sprintf('SELECT
        		pm.meta_value AS file,
                pm2.meta_value AS metadata
            FROM
                %sposts AS p
            LEFT JOIN
                %spostmeta AS pm ON p.ID = pm.post_ID AND pm.meta_key = "_wp_attached_file"
            LEFT JOIN
            	%spostmeta AS pm2 ON p.ID = pm2.post_ID AND pm2.meta_key = "_wp_attachment_metadata"
            WHERE
                p.post_type = "attachment"  AND (pm.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
            GROUP BY
            	p.ID', $wpdb->prefix, $wpdb->prefix, $wpdb->prefix);

            if ($limit) {
                $sql .= sprintf(' LIMIT %d', $limit);

                if ($offset) {
                    $sql .= sprintf(' OFFSET %d', $offset);
                }
            }

            $posts = $wpdb->get_results($sql);

            if ($posts) {
                $count = count($posts);
                $total = $this->get_attachments_count();
                $files = array();

                foreach ($posts as $post) {
                    $post_files = array();

                    if ($post->file) {
                        $file = $this->_get_common()->normalize_attachment_file($post->file);

                        $local_file = $upload_info['basedir'] . '/' . $file;
                        $remote_file = ltrim($upload_info['baseurlpath'] . $file, '/');

                        $post_files[] = $this->_get_common()->build_file_descriptor($local_file, $remote_file);
                    }

                    if ($post->metadata) {
                        $metadata = @unserialize($post->metadata);

                        $post_files = array_merge($post_files, $this->_get_common()->get_metadata_files($metadata));
                    }

                    $post_files = apply_filters('w3tc_cdn_add_attachment', $post_files);

                    $files = array_merge($files, $post_files);
                }

                $this->_get_common()->upload($files, false, $results);
            }
        }
    }

    /**
     * Imports library
     *
     * @param integer $limit
     * @param integer $offset
     * @param integer $count
     * @param integer $total
     * @param array $results
     * @return boolean
     */
    function import_library($limit, $offset, &$count, &$total, &$results) {
        global $wpdb;

        $count = 0;
        $total = 0;
        $results = array();

        $upload_info = w3_upload_info();
        $uploads_use_yearmonth_folders = get_option('uploads_use_yearmonth_folders');
        $document_root = w3_get_document_root();

        @set_time_limit($this->_config->get_integer('timelimit.cdn_import'));

        if ($upload_info) {
            /**
             * Search for posts with links or images
             */
            $sql = sprintf('SELECT
        		ID,
        		post_content,
        		post_date
            FROM
                %sposts
            WHERE
                post_status = "publish"
                AND (post_type = "post" OR post_type = "page")
                AND (post_content LIKE "%%src=%%"
                	OR post_content LIKE "%%href=%%")
       		', $wpdb->prefix);

            if ($limit) {
                $sql .= sprintf(' LIMIT %d', $limit);

                if ($offset) {
                    $sql .= sprintf(' OFFSET %d', $offset);
                }
            }

            $posts = $wpdb->get_results($sql);

            if ($posts) {
                $count = count($posts);
                $total = $this->get_import_posts_count();
                $regexp = '~(' . $this->get_regexp_by_mask($this->_config->get_string('cdn.import.files')) . ')$~';
                $import_external = $this->_config->get_boolean('cdn.import.external');

                foreach ($posts as $post) {
                    $matches = null;
                    $replaced = array();
                    $attachments = array();
                    $post_content = $post->post_content;

                    /**
                     * Search for all link and image sources
                     */
                    if (preg_match_all('~(href|src)=[\'"]?([^\'"<>\s]+)[\'"]?~', $post_content, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            list($search, $attribute, $origin) = $match;

                            /**
                             * Check if $search is already replaced
                             */
                            if (isset($replaced[$search])) {
                                continue;
                            }

                            $error = '';
                            $result = false;

                            $src = w3_normalize_file_minify($origin);
                            $dst = '';

                            /**
                             * Check if file exists in the library
                             */
                            if (stristr($origin, $upload_info['baseurl']) === false) {
                                /**
                                 * Check file extension
                                 */
                                $check_src = $src;

                                if (w3_is_url($check_src)) {
                                    $qpos = strpos($check_src, '?');

                                    if ($qpos !== false) {
                                        $check_src = substr($check_src, 0, $qpos);
                                    }
                                }

                                if (preg_match($regexp, $check_src)) {
                                    /**
                                     * Check for already uploaded attachment
                                     */
                                    if (isset($attachments[$src])) {
                                        list($dst, $dst_url) = $attachments[$src];
                                        $result = true;
                                    } else {
                                        if ($uploads_use_yearmonth_folders) {
                                            $upload_subdir = date('Y/m', strtotime($post->post_date));
                                            $upload_dir = sprintf('%s/%s', $upload_info['basedir'], $upload_subdir);
                                            $upload_url = sprintf('%s/%s', $upload_info['baseurl'], $upload_subdir);
                                        } else {
                                            $upload_subdir = '';
                                            $upload_dir = $upload_info['basedir'];
                                            $upload_url = $upload_info['baseurl'];
                                        }

                                        $src_filename = pathinfo($src, PATHINFO_FILENAME);
                                        $src_extension = pathinfo($src, PATHINFO_EXTENSION);

                                        /**
                                         * Get available filename
                                         */
                                        for ($i = 0; ; $i++) {
                                            $dst = sprintf('%s/%s%s%s', $upload_dir, $src_filename, ($i ? $i : ''), ($src_extension ? '.' . $src_extension : ''));

                                            if (!file_exists($dst)) {
                                                break;
                                            }
                                        }

                                        $dst_basename = basename($dst);
                                        $dst_url = sprintf('%s/%s', $upload_url, $dst_basename);
                                        $dst_path = ltrim(str_replace($document_root, '', w3_path($dst)), '/');

                                        if ($upload_subdir) {
                                            w3_mkdir($upload_subdir, 0777, $upload_info['basedir']);
                                        }

                                        $download_result = false;

                                        /**
                                         * Check if file is remote URL
                                         */
                                        if (w3_is_url($src)) {
                                            /**
                                             * Download file
                                             */
                                            if ($import_external) {
                                                $download_result = w3_download($src, $dst);

                                                if (!$download_result) {
                                                    $error = 'Unable to download file';
                                                }
                                            } else {
                                                $error = 'External file import is disabled';
                                            }
                                        } else {
                                            /**
                                             * Otherwise copy file from local path
                                             */
                                            $src_path = $document_root . '/' . urldecode($src);

                                            if (file_exists($src_path)) {
                                                $download_result = @copy($src_path, $dst);

                                                if (!$download_result) {
                                                    $error = 'Unable to copy file';
                                                }
                                            } else {
                                                $error = 'Source file doesn\'t exists';
                                            }
                                        }

                                        /**
                                         * Check if download or copy was successful
                                         */
                                        if ($download_result) {
                                            w3_require_once(W3TC_INC_DIR . '/functions/mime.php');

                                            $title = $dst_basename;
                                            $guid = ltrim($upload_info['baseurlpath'] . $title, ',');
                                            $mime_type = w3_get_mime_type($dst);

                                            $GLOBALS['wp_rewrite'] = new WP_Rewrite();

                                            /**
                                             * Insert attachment
                                             */
                                            $id = wp_insert_attachment(array(
                                                'post_mime_type' => $mime_type,
                                                'guid' => $guid,
                                                'post_title' => $title,
                                                'post_content' => '',
                                                'post_parent' => $post->ID
                                            ), $dst);

                                            if (!is_wp_error($id)) {
                                                /**
                                                 * Generate attachment metadata and upload to CDN
                                                 */
                                                require_once ABSPATH . 'wp-admin/includes/image.php';
                                                wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $dst));

                                                $attachments[$src] = array(
                                                    $dst,
                                                    $dst_url
                                                );

                                                $result = true;
                                            } else {
                                                $error = 'Unable to insert attachment';
                                            }
                                        }
                                    }

                                    /**
                                     * If attachment was successfully created then replace links
                                     */
                                    if ($result) {
                                        $replace = sprintf('%s="%s"', $attribute, $dst_url);

                                        // replace $search with $replace
                                        $post_content = str_replace($search, $replace, $post_content);

                                        $replaced[$search] = $replace;
                                        $error = 'OK';
                                    }
                                } else {
                                    $error = 'File type rejected';
                                }
                            } else

                            {
                                $error = 'File already exists in the media library';
                            }

                            /**
                             * Add new entry to the log file
                             */

                            $results[] = array(
                                'src' => $src,
                                'dst' => $dst_path,
                                'result' => $result,
                                'error' => $error
                            );
                        }
                    }

                    /**
                     * If post content was chenged then update DB
                     */
                    if ($post_content != $post->post_content) {
                        wp_update_post(array(
                            'ID' => $post->ID,
                            'post_content' => $post_content
                        ));
                    }
                }
            }
        }
    }

    /**
     * Rename domain
     *
     * @param array $names
     * @param integer $limit
     * @param integer $offset
     * @param integer $count
     * @param integer $total
     * @param integer $results
     * @return void
     */
    function rename_domain($names, $limit, $offset, &$count, &$total, &$results) {
        global $wpdb;

        @set_time_limit($this->_config->get_integer('timelimit.domain_rename'));

        $count = 0;
        $total = 0;
        $results = array();

        $upload_info = w3_upload_info();

        foreach ($names as $index => $name) {
            $names[$index] = str_ireplace('www.', '', $name);
        }

        if ($upload_info) {
            $sql = sprintf('SELECT
        		ID,
        		post_content,
        		post_date
            FROM
                %sposts
            WHERE
                post_status = "publish"
                AND (post_type = "post" OR post_type = "page")
                AND (post_content LIKE "%%src=%%"
                	OR post_content LIKE "%%href=%%")
       		', $wpdb->prefix);

            if ($limit) {
                $sql .= sprintf(' LIMIT %d', $limit);

                if ($offset) {
                    $sql .= sprintf(' OFFSET %d', $offset);
                }
            }

            $posts = $wpdb->get_results($sql);

            if ($posts) {
                $count = count($posts);
                $total = $this->get_rename_posts_count();
                $names_quoted = array_map('w3_preg_quote', $names);

                foreach ($posts as $post) {
                    $matches = null;
                    $post_content = $post->post_content;
                    $regexp = '~(href|src)=[\'"]?(https?://(www\.)?(' . implode('|', $names_quoted) . ')' . w3_preg_quote($upload_info['baseurlpath']) . '([^\'"<>\s]+))[\'"]~';

                    if (preg_match_all($regexp, $post_content, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $old_url = $match[2];
                            $new_url = sprintf('%s/%s', $upload_info['baseurl'], $match[5]);
                            $post_content = str_replace($old_url, $new_url, $post_content);

                            $results[] = array(
                                'old' => $old_url,
                                'new' => $new_url,
                                'result' => true,
                                'error' => 'OK'
                            );
                        }
                    }

                    if ($post_content != $post->post_content) {
                        wp_update_post(array(
                            'ID' => $post->ID,
                            'post_content' => $post_content
                        ));
                    }
                }
            }
        }
    }

    /**
     * Returns attachments count
     *
     * @return integer
     */
    function get_attachments_count() {
        global $wpdb;

        $sql = sprintf('SELECT COUNT(DISTINCT p.ID)
FROM %sposts AS p
LEFT JOIN %spostmeta AS pm ON p.ID = pm.post_ID
AND pm.meta_key =  "_wp_attached_file"
LEFT JOIN %spostmeta AS pm2 ON p.ID = pm2.post_ID
AND pm2.meta_key =  "_wp_attachment_metadata"
WHERE p.post_type = "attachment" AND (pm.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)', $wpdb->prefix, $wpdb->prefix, $wpdb->prefix);

        return $wpdb->get_var($sql);
    }

    /**
     * Returns import posts count
     *
     * @return integer
     */
    function get_import_posts_count() {
        global $wpdb;

        $sql = sprintf('SELECT
        		COUNT(*)
            FROM
                %sposts
            WHERE
                post_status = "publish"
                AND (post_type = "post" OR post_type = "page")
                AND (post_content LIKE "%%src=%%"
                	OR post_content LIKE "%%href=%%")
                ', $wpdb->prefix);

        return $wpdb->get_var($sql);
    }

    /**
     * Returns rename posts count
     *
     * @return integer
     */
    function get_rename_posts_count() {
        return $this->get_import_posts_count();
    }

    /**
     * Returns regexp by mask
     *
     * @param string $mask
     * @return string
     */
    function get_regexp_by_mask($mask) {
        $mask = trim($mask);
        $mask = w3_preg_quote($mask);

        $mask = str_replace(array(
            '\*',
            '\?',
            ';'
        ), array(
            '@ASTERISK@',
            '@QUESTION@',
            '|'
        ), $mask);

        $regexp = str_replace(array(
            '@ASTERISK@',
            '@QUESTION@'
        ), array(
            '[^\\?\\*:\\|"<>]*',
            '[^\\?\\*:\\|"<>]'
        ), $mask);

        return $regexp;
    }

    function update_cnames(&$error) {
        $cdn = $this->_get_common()->get_cdn();
        $cdn->update_cnames($error);
    }

    /**
     * Returns rules
     *
     * @param bool $cdnftp
     * @return string
     */
    function generate_rules($cdnftp = false) {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->generate_rules_apache($cdnftp);

            case w3_is_nginx():
                return $this->generate_rules_nginx($cdnftp);
        }

        return false;
    }

    /**
     * Returns apache rules
     *
     * @param bool $cdnftp
     * @return string
     */
    function generate_rules_apache($cdnftp = false) {
        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_CDN . "\n";
        $w3_dispatcher = w3_instance('W3_Dispatcher');

        if ($w3_dispatcher->should_cdn_generate_canonical($cdnftp)) {
            $mime_types = $this->_get_other_types();
            $extensions = array_keys($mime_types);
            $extensions_lowercase = array_map('strtolower', $extensions);
            $extensions_uppercase = array_map('strtoupper', $extensions);
            $host = ($cdnftp) ? w3_get_home_domain() : '%{HTTP_HOST}';
            $rules .= "<FilesMatch \"\\.(" . implode('|', array_merge($extensions_lowercase, $extensions_uppercase)) . ")$\">\n";
            $rules .= "   <IfModule mod_rewrite.c>\n";
            $rules .= "      RewriteEngine On\n";
            $rules .= "      RewriteCond %{HTTPS} !=on\n";
            $rules .= "      RewriteRule .* - [E=CANONICAL:http://$host%{REQUEST_URI},NE]\n";
            $rules .= "      RewriteCond %{HTTPS} =on\n";
            $rules .= "      RewriteRule .* - [E=CANONICAL:https://$host%{REQUEST_URI},NE]\n";
            $rules .= "   </IfModule>\n";
            $rules .= "   <IfModule mod_headers.c>\n";
            $rules .= '      Header set Link "<%{CANONICAL}e>; rel=\"canonical\""' . "\n";
            $rules .= "   </IfModule>\n";
            $rules .= "</FilesMatch>\n";
        }

        if (!$cdnftp)
            $rules .= "<FilesMatch \"\.(ttf|otf|eot|woff)$\">\n";
        $rules .= "<IfModule mod_headers.c>\n";
        $rules .= "    Header set Access-Control-Allow-Origin \"*\"\n";
        $rules .= "</IfModule>\n";
        if (!$cdnftp)
            $rules .= "</FilesMatch>\n";
        $rules .= W3TC_MARKER_END_CDN . "\n";

        return $rules;
    }

    /**
     * Returns nginx rules
     *
     * @param bool $cdnftp
     * @return string
     */
    function generate_rules_nginx($cdnftp = false) {
        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_CDN . "\n";
        $w3_dispatcher = w3_instance('W3_Dispatcher');
        if ($w3_dispatcher->should_cdn_generate_canonical($cdnftp)) {
            $mime_types = $this->_get_other_types();
            $extensions = array_keys($mime_types);
            $rules .= "location ~ \.(" . implode('|', $extensions) . ")$ {\n";
            $rules .= $this->generate_canonical_nginx($cdnftp);
            $rules .= "}\n";
        }

        if (!$cdnftp)
            $rules .= "location ~ \\.(ttf|otf|eot|woff)$ {\n";
        $rules .= "   add_header Access-Control-Allow-Origin \"*\";\n";
        if (!$cdnftp)
            $rules .= "}\n";
        $rules .= W3TC_MARKER_END_CDN . "\n";

        return $rules;
    }

    /**
     * Returns nginx canonical rule
     * @param $cdnftp
     * @return string
     */
    public function generate_canonical_nginx($cdnftp) {
        $home = ($cdnftp) ? w3_get_home_domain() : '$host';
        return '   add_header Link "<$scheme://' . $home . '$uri>; rel=\"canonical\"";' . "\n";
    }

    /**
     * Writes rules
     *
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function write_rules() {
        $path = w3_get_browsercache_rules_cache_path();

        if (file_exists($path)) {
            $data = @file_get_contents($path);

            if ($data === false) {
                return false;
            }
        } else {
            $data = '';
        }

        $replace_start = strpos($data, W3TC_MARKER_BEGIN_CDN);
        $replace_end = strpos($data, W3TC_MARKER_END_CDN);

        if ($replace_start !== false && $replace_end !== false && $replace_start < $replace_end) {
            $replace_length = $replace_end - $replace_start + strlen(W3TC_MARKER_END_CDN) + 1;
        } else {
            $replace_start = false;
            $replace_length = 0;

            $search = array(
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            );

            foreach ($search as $string => $length) {
                $replace_start = strpos($data, $string);

                if ($replace_start !== false) {
                    $replace_start += $length;
                    break;
                }
            }
        }

        $rules = $this->generate_rules();

        if ($replace_start !== false) {
            $data = w3_trim_rules(substr_replace($data, $rules, $replace_start, $replace_length));
        } else {
            $data = w3_trim_rules($data . $rules);
        }

        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
        w3_wp_write_to_file($path, $data);
    }

    /**
     * Check rules
     *
     * @return boolean
     */
    function check_rules() {
        $path = w3_get_browsercache_rules_cache_path();
        $search = $this->generate_rules();

        return (($data = @file_get_contents($path)) && strstr(w3_clean_rules($data), w3_clean_rules($search)) !== false);
    }

    /**
     * Removes cache rules
     *
     * @throws FilesystemCredentialException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    function remove_rules() {
        $path = w3_get_browsercache_rules_cache_path();

        if (file_exists($path)) {
            if (($data = @file_get_contents($path)) !== false) {
                $data = $this->erase_rules($data);

                w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
                w3_wp_write_to_file($path, $data);
            }
        }
    }

    /**
     * Erases cache rules
     *
     * @param string $data
     * @return string
     */
    function erase_rules($data) {
        $data = w3_erase_rules($data, W3TC_MARKER_BEGIN_CDN, W3TC_MARKER_END_CDN);

        return $data;
    }

    /**
     * Returns other mime types
     *
     * @return array
     */
    function _get_other_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/other.php';

        return $mime_types;
    }

    /**
     * Returns required rules for module
     * @return array
     */
    function get_required_rules() {
        $rewrite_rules = array();
        if ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_string('cdn.engine') == 'ftp') {
            $domain = $this->_get_common()->get_cdn()->get_domain();
            $cdn_rules_path = sprintf('ftp://%s/%s', $domain, w3_get_cdn_rules_path());
            $rewrite_rules[] = array('filename' => $cdn_rules_path, 'content' => $this->generate_rules());
        }

        if ($this->_config->get_boolean('cdn.enabled') || $this->_config->get_boolean('cloudflare.enabled')) {
            $browsercache_rules_cache_path = w3_get_browsercache_rules_cache_path();
            $rewrite_rules[] = array('filename' => $browsercache_rules_cache_path, 'content' => $this->generate_rules());
        }
        return $rewrite_rules;
    }

    /**
     * @return array
     */
    function remove_rules_with_message() {
        $ftp_form = null;
        $errors = array();
        $errors_short_form = array();

        if ($this->check_rules()) {
            try {
                $this->remove_rules();
            } catch(Exception $e) {
                $errors[] = sprintf('CDN/CloudFlare added rules that need to be removed. To remove them manually, edit the configuration file (<strong>%s</strong>) and remove all lines between and including <strong>%s</strong> and <strong>%s</strong> markers inclusive.'
                    , w3_get_browsercache_rules_cache_path(),
                    W3TC_MARKER_BEGIN_CDN, W3TC_MARKER_BEGIN_CDN);
                $errors_short_form[] = sprintf('Edit file (<strong>%s</strong>) and remove all lines between and including <strong>%s</strong> and <strong>%s</strong> markers inclusive.'
                    , w3_get_browsercache_rules_cache_path(),
                    W3TC_MARKER_BEGIN_CDN, W3TC_MARKER_BEGIN_CDN);
                if ($e instanceof FilesystemCredentialException)
                    $ftp_form = $e->ftp_form();
            }
        }
        return array('errors' => $errors, 'ftp_form' => $ftp_form, 'errors_short_form' => $errors_short_form);
    }
}
