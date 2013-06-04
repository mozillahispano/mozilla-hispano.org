<?php

/**
 * The W3 Total Cache plugin
 *
 * @package wp-cli
 * @subpackage commands/third-party
 * @maintainer Anthony Somerset
 */
class W3TotalCache_Command extends WP_CLI_Command {

	/**
	 * Clear something from the cache
	 *
	 * @param array $args
	 * @param array $vars
	 */
	function flush($args = array(), $vars = array()) {
		$args = array_unique($args);

		do {
			$cache_type = array_shift($args);

			switch($cache_type) {
			case 'db':
			case 'database':
				try {
 					$w3_db = w3_instance('W3_CacheFlush');
 					$w3_db->dbcache_flush();
				}
				catch (Exception $e) {
 				  WP_CLI::error('Flushing the DB cache failed.');
				}
				WP_CLI::success('The DB cache is flushed successfully.');
				break;

			case 'minify':
				try {
 					$w3_minify = w3_instance('W3_CacheFlush');
 					$w3_minify->minifycache_flush();
				}
				catch (Exception $e) {
 				  WP_CLI::error('Flushing the minify cache failed.');
				}
				WP_CLI::success('The minify cache is flushed successfully.');
				break;

			case 'object':
				try {
 				  $w3_objectcache = w3_instance('W3_CacheFlush');
 					$w3_objectcache->objectcache_flush();
				}
				catch (Exception $e) {
 				  WP_CLI::error('Flushing the object cache failed.');
				}
				WP_CLI::success('The object cache is flushed successfully.');
				break;

			case 'post':
			default:
				if (isset($vars['post_id'])) {
					if (is_numeric($vars['post_id'])) {
					  try {
                        $w3_cacheflush = w3_instance('W3_CacheFlush');
                        $w3_cacheflush->pgcache_flush_post($vars['post_id']);
                        $w3_cacheflush->varnish_flush_post($vars['post_id']);
                      }
  				  catch (Exception $e) {
   					  WP_CLI::error('Flushing the page from cache failed.');
   					}
   					WP_CLI::success('The page is flushed from cache successfully.');
					} else {
						WP_CLI::error('This is not a valid post id.');
					}

					w3tc_pgcache_flush_post($vars['post_id']);
				}
				elseif (isset($vars['permalink'])) {
					$id = url_to_postid($vars['permalink']);

					if (is_numeric($id)) {
						try {
  					      $w3_cacheflush = w3_instance('W3_CacheFlush');
  					      $w3_cacheflush->pgcache_flush_post($id);
                          $w3_cacheflush->varnish_flush_post($id);
  				  }
  				  catch (Exception $e) {
   					  WP_CLI::error('Flushing the page from cache failed.');
   					}
   					WP_CLI::success('The page is flushed from cache successfully.');
					} else {
						WP_CLI::error('There is no post with this permalink.');
					}
				} else {
					if (isset($flushed_page_cache) && $flushed_page_cache)
						break;

					$flushed_page_cache = true;
					try {
                      $w3_cacheflush = w3_instance('W3_CacheFlush');
                      $w3_cacheflush->pgcache_flush();
                      $w3_cacheflush->varnish_flush();
 					}
 					catch (Exception $e) {
   					WP_CLI::error('Flushing the page cache failed.');
   			  }
 				  WP_CLI::success('The page cache is flushed successfully.');
				}
			}
		} while (!empty($args));
	} 


	/**
	 * Update query string function
	 */
	function querystring() {

		try {
  		$w3_querystring = w3_instance('W3_CacheFlush');
  		$w3_querystring->browsercache_flush();
  	}
		catch (Exception $e) {
  		WP_CLI::error('updating the query string failed. with error %s', $e);
		}

		WP_CLI::success('The query string was updated successfully.');

}

	/**
	 * Purge URL's from cdn and varnish if enabled
	 * @param array $args
	 */
	function cdn_purge($args = array()) {
	    $purgeitems = array();
        foreach ($args as $file) {
            $cdncommon = w3_instance('W3_Plugin_CdnCommon');
            $local_path = WP_ROOT . $file;
            $remote_path = $file;
            $purgeitems[] = $cdncommon->build_file_descriptor($local_path, $remote_path);
        }

   		try {
			  $w3_cdn_purge = w3_instance('W3_CacheFlush');
			  $w3_cdn_purge->cdn_purge_files($purgeitems);
		}
		catch (Exception $e) {
			WP_CLI::error('Files did not successfully purge with error %s', $e);
		}
		WP_CLI::success('Files purged successfully.');
		
	}

    /**
     * Tell APC to reload PHP files
     * @param array $args
     */
    function apc_reload_files($args = array()) {
        try {
            $method = array_shift($args);
            if (!in_array($method, array('SNS', 'local')))
                WP_CLI::error($method . ' is not supported. Change to SNS or local to reload APC files');
            if ($method == 'SNS') {
                $w3_cache = w3_instance('W3_CacheFlush');
                $w3_cache->apc_reload_files($args);
            } else {
                $url = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/apc.php';
                $path = parse_url($url, PHP_URL_PATH);
                $post = array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'body' => array( 'nonce' => wp_hash($path), 'command' => 'reload_files', 'files' => $args),
                );
                $result = wp_remote_post($url, $post);
                if (is_wp_error($result)) {
                    WP_CLI::error('Files did not successfully reload with error %s', $result);
                } elseif ($result['response']['code'] != '200') {
                    WP_CLI::error('Files did not successfully reload with message: '. $result['body']);
                }
            }
        }
        catch (Exception $e) {
            WP_CLI::error('Files did not successfully reload with error %s', $e);
        }
        WP_CLI::success('Files reloaded successfully.');

    }

    /**
     * Tell APC to reload PHP files
     * @param array $args
     */
    function apc_delete_based_on_regex($args = array()) {
        try {
            $method = array_shift($args);
            if (!in_array($method, array('SNS', 'local')))
                WP_CLI::error($method . ' is not supported. Change to SNS or local to delete APC files');

            if ($method == 'SNS') {
                $w3_cache = w3_instance('W3_CacheFlush');
                $w3_cache->apc_delete_files_based_on_regex($args[0]);
            } else {
                $url = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/apc.php';
                $path = parse_url($url, PHP_URL_PATH);
                $post = array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'body' => array( 'nonce' => wp_hash($path), 'command' => 'delete_files', 'regex' => $args[0]),
                );
                $result = wp_remote_post($url, $post);
                if (is_wp_error($result)) {
                    WP_CLI::error('Files did not successfully delete with error %s', $result);
                } elseif ($result['response']['code'] != '200') {
                    WP_CLI::error('Files did not successfully delete with message: '. $result['body']);
                }
            }
        }
        catch (Exception $e) {
            WP_CLI::error('Files did not successfully delete with error %s', $e);
        }
        WP_CLI::success('Files deleted successfully.');

    }
	
	/**
	 * triggers PgCache Garbage Cleanup 
	 */
	function pgcache_cleanup() {
    try {
        $pgcache_cleanup = w3_instance('W3_Plugin_PgCacheAdmin');
        $pgcache_cleanup->cleanup();
		}
		catch (Exception $e) {
			WP_CLI::error('PageCache Garbage cleanup did not start with error %s', $e);
		}
		WP_CLI::success('PageCache Garbage cleanup triggered successfully.');
		
	}

	/**
	 * Help function for this command
	 */
	public static function help() {
		WP_CLI::line( <<<EOB
usage: wp w3-total-cache flush [post|database|minify|object] [--post_id=<post-id>] [--permalink=<post-permalink>] 
  or : wp w3-total-cache querystring
  or : wp w3-total-cache cdn_purge <file> [<file2>]...
  or : wp w3-total-cache pgcache_cleanup

			 flush    			   flushes whole cache or specific items based on provided arguments
			 querystring			 update query string for all static files
			 cdn_purge         Purges command line provided files from Varnish and the CDN
			 pgcache_cleanup   Generally triggered from a cronjob, allows for manual Garbage collection of page cache to be triggered
             apc_reload_files SNS/local file.php file2.php file3.php Tells apc to compile files
             apc_delete_based_on_regex SNS/local expression Tells apc to delete files that match a RegEx mask
Available flush sub-commands:
			 --post_id=<id>                  flush a specific post ID
			 --permalink=<post-permalink>    flush a specific permalink
			 database                        flush the database cache
			 object                          flush the object cache
			 minify                          flush the minify cache
EOB
		);
	}
}

WP_CLI::addCommand('w3-total-cache', 'W3TotalCache_Command');
WP_CLI::addCommand('total-cache', 'W3TotalCache_Command');


