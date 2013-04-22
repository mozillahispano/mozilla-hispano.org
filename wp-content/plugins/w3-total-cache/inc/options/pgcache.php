<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Page caching via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('pgcache.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($pgcache_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
        To rebuild the page cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_pgcache" value="empty cache"<?php if (! $pgcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        operation.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('General', '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.home'); ?> <?php echo get_option('show_on_front') == 'posts' ? 'Cache front page': 'Cache posts page';?></label><br />
                    <span class="description">For many blogs this is your most visited page, it is recommended that you cache it.</span>
                </th>
            </tr>
            <?php if (get_option( 'show_on_front') != 'posts'): ?>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.reject.front_page'); ?> Don't cache front page</label><br />
                    <span class="description">By default the front page is cached when using static front page in reading settings.</span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.feed') ?> Cache feeds: site, categories, tags, comments</label><br />
                    <span class="description">Even if using a feed proxy service (like <a href="http://en.wikipedia.org/wiki/FeedBurner" target="_blank">FeedBurner</a>), enabling this option is still recommended.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.ssl') ?> Cache <acronym titlte="Secure Socket Layer">SSL</acronym> (<acronym title="HyperText Transfer Protocol over SSL">https</acronym>) requests</label><br />
                    <span class="description">Cache <acronym titlte="Secure Socket Layer">SSL</acronym> requests (uniquely) for improved performance.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.query', ($this->_config->get_string('pgcache.engine') == 'file_generic')) ?> Cache <acronym title="Uniform Resource Identifier">URI</acronym>s with query string variables</label><br />
                    <span class="description">Search result (and similar) pages will be cached if enabled.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.cache.404') ?> Cache 404 (not found) pages</label><br />
                    <span class="description">Reduce server load by caching 404 pages. If the disk enhanced method of disk caching is used, 404 pages will be returned with a 200 response code. Use at your own risk.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.check.domain', $disable_check_domain) ?> Cache requests only for <?php echo w3_get_home_domain(); ?> hostname</label><br />
                    <span class="description">Cache only requests with the same <acronym title="Uniform Resource Indicator">URL</acronym> as the site's <a href="options-general.php">site address</a>.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.reject.logged') ?> Don't cache pages for logged in users</label><br />
                    <span class="description">Unauthenticated users may view a cached version of the last authenticated user's view of a given page. Disabling this option is not recommended.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <?php $this->checkbox('pgcache.reject.logged_roles') ?> Don't cache pages for following user roles</label><br />
                    <span class="description">Select user roles that should not receive cached pages:</span>
                    
                    <div id="pgcache_reject_roles">
                        <?php $saved_roles = $this->_config->get_array('pgcache.reject.roles'); ?>
                        <input type="hidden" name="pgcache.reject.roles" value="" /><br />
                        <?php foreach( get_editable_roles() as $role_name => $role_data ) : ?>
                            <input type="checkbox" name="pgcache.reject.roles[]" value="<?php echo $role_name ?>" <?php checked( in_array( $role_name, $saved_roles ) ) ?> id="role_<?php echo $role_name ?>" />
                            <label for="role_<?php echo $role_name ?>"><?php echo $role_data['name'] ?></label>
                        <?php endforeach; ?>
                    </div>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Cache Preload', '', 'cache_preload'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('pgcache.prime.enabled') ?> Automatically prime the page cache</label><br />
                </th>
            </tr>
            <tr>
                <th><label for="pgcache_prime_interval">Update interval:</label></th>
                <td>
                    <input id="pgcache_prime_interval" type="text" name="pgcache.prime.interval" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo $this->_config->get_integer('pgcache.prime.interval'); ?>" size="8" /> seconds<br />
                    <span class="description">The number of seconds to wait before creating another set of cached pages.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_prime_limit">Pages per interval:</label></th>
                <td>
                    <input id="pgcache_prime_limit" type="text" name="pgcache.prime.limit" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo $this->_config->get_integer('pgcache.prime.limit'); ?>" size="8" /><br />
                    <span class="description">Limit the number of pages to create per batch. Fewer pages may be better for under-powered servers.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_prime_sitemap">Sitemap <acronym title="Uniform Resource Indicator">URL</acronym>:</label></th>
                <td>
                    <input id="pgcache_prime_sitemap" type="text" name="pgcache.prime.sitemap" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo $this->_config->get_string('pgcache.prime.sitemap'); ?>" size="100" /><br />
                    <span class="description">A <a href="http://www.xml-sitemaps.com/validate-xml-sitemap.html" target="_blank">compliant</a> sitemap can be used to specify the pages to maintain in the primed cache. Pages will be cached according to the priorities specified in the <acronym title="Extensible Markup Language">XML</acronym> file. Due to it's completeness and integrations, <a href="http://wordpress.org/extend/plugins/wordpress-seo/" target="_blank">WordPress SEO</a> is recommended for use with this feature.</span>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('pgcache.prime.post.enabled') ?> Prime post cache on publish.</label><br />
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php
            $modules = array();
            if ($pgcache_enabled) $modules[] = 'Page Cache';
            if ($varnish_enabled) $modules [] = 'Varnish';
            if ($cdn_mirror_purge_enabled) $modules[] = 'CDN';
        echo $this->postbox_header('Purge Policy: ' . implode(', ', $modules), '', 'purge_policy'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    Specify the pages and feeds to purge when posts are created, edited, or comments posted. The defaults are recommended because additional options may reduce server performance:

                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <th style="padding-left: 0;">
                                <?php if (get_option('show_on_front') != 'posts'): ?>
                                <?php $this->checkbox('pgcache.purge.front_page') ?> Front page</label><br />
                                <?php endif; ?>
                                <?php $this->checkbox('pgcache.purge.home') ?>  <?php echo get_option('show_on_front') == 'posts' ? 'Front page': 'Posts page';?></label><br />
                                <?php $this->checkbox('pgcache.purge.post') ?> Post page</label><br />
                                <?php $this->checkbox('pgcache.purge.feed.blog') ?> Blog feed</label><br />
                            </th>
                            <th>
                                <?php $this->checkbox('pgcache.purge.comments') ?> Post comments pages</label><br />
                                <?php $this->checkbox('pgcache.purge.author') ?> Post author pages</label><br />
                                <?php $this->checkbox('pgcache.purge.terms') ?> Post terms pages</label><br />
                            </th>
                            <th>
                                <?php $this->checkbox('pgcache.purge.feed.comments') ?> Post comments feed</label><br />
                                <?php $this->checkbox('pgcache.purge.feed.author') ?> Post author feed</label><br />
                                <?php $this->checkbox('pgcache.purge.feed.terms') ?> Post terms feeds</label>
                            </th>
                            <th>
                                <?php $this->checkbox('pgcache.purge.archive.daily') ?> Daily archive pages</label><br />
                                <?php $this->checkbox('pgcache.purge.archive.monthly') ?> Monthly archive pages</label><br />
                                <?php $this->checkbox('pgcache.purge.archive.yearly') ?> Yearly archive pages</label><br />
                            </th>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    Specify the feed types to purge:<br />
                    <input type="hidden" name="pgcache.purge.feed.types" value="" />
                    <?php foreach($feeds as $feed): ?>
                        <label>
                            <input type="checkbox" name="pgcache.purge.feed.types[]" 
                                value="<?php echo $feed; ?>"
                                <?php checked(in_array($feed, $this->_config->get_array('pgcache.purge.feed.types')), true); ?> 
                                <?php $this->sealing_disabled('pgcache') ?>
                                />
                        <?php echo $feed; ?>
                        <?php if ($feed == $default_feed): ?>(default)<?php endif; ?></label><br />
                    <?php endforeach; ?>
                </th>
            </tr>
            <tr>
                <th><label for="pgcache_purge_postpages_limit">Limit page purging:</label></th>
                <td>
                    <input id="pgcache_purge_postpages_limit" name="pgcache.purge.postpages_limit" <?php $this->sealing_disabled('pgcache') ?> type="text" value="<?php echo $this->_config->get_integer('pgcache.purge.postpages_limit'); ?>" /><br />
                    <span class="description">Specify number of pages that lists posts (archive etc) that should be purged on post updates etc, i.e example.com/ ... example.com/page/5. <br />0 means all pages that lists posts are purged, i.e example.com/page/2 ... .</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_purge_pages">Additional pages:</label></th>
                <td>
                    <textarea id="pgcache_purge_pages" name="pgcache.purge.pages"
                        <?php $this->sealing_disabled('pgcache') ?>
                              cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.purge.pages'))); ?></textarea><br />
                    <span class="description">Specify additional pages to purge. Including parent page in path. Ex: parent/posts.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_purge_sitemap_regex">Purge sitemaps:</label></th>
                <td>
                    <input id="pgcache_purge_sitemap_regex" name="pgcache.purge.sitemap_regex" <?php $this->sealing_disabled('pgcache') ?> value="<?php echo esc_attr($this->_config->get_string('pgcache.purge.sitemap_regex')) ?>" type="text" /><br />
                    <span class="description">Specify a regular expression that matches your sitemaps.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Advanced', '', 'advanced'); ?>
        <table class="form-table">
            <?php if ($this->_config->get_string('pgcache.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
                <td>
                    <input id="memcached_servers" type="text" 
                        name="pgcache.memcached.servers" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('pgcache.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        type="button" value="Test" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($this->_config->get_string('pgcache.engine') == 'file_generic'): ?>
            <tr>
                <th><label>Compatibility mode</label></th>
                <td>
                    <?php $this->checkbox('pgcache.compatibility') ?> Enable compatibility mode</label><br />
                    <span class="description">Decreases performance by ~20% at scale in exchange for increasing interoperability with more hosting environemnts and WordPress idiosyncracies. This option should be enabled for most sites.</span>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($this->_config->get_string('pgcache.engine') != 'file' && $this->_config->get_string('pgcache.engine') != 'file_generic'): ?>
            <tr>
                <th><label for="pgcache_lifetime">Maximum lifetime of cache objects:</label></th>
                <td>
                    <input id="pgcache_lifetime" type="text" name="pgcache.lifetime"
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo $this->_config->get_integer('pgcache.lifetime'); ?>" size="8" /> seconds
                    <br /><span class="description">Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.</span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="pgcache_file_gc">Garbage collection interval:</label></th>
                <td>
                    <input id="pgcache_file_gc" type="text" name="pgcache.file.gc" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        value="<?php echo $this->_config->get_integer('pgcache.file.gc'); ?>" size="8"<?php if ($this->_config->get_string('pgcache.engine') != 'file' && $this->_config->get_string('pgcache.engine') != 'file_generic'): ?> disabled="disabled"<?php endif; ?> /> seconds
                    <br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_comment_cookie_ttl">Comment cookie lifetime:</label></th>
                <td>
                        <input id="pgcache_comment_cookie_ttl" type="text" name="pgcache.comment_cookie_ttl" value="<?php echo $this->_config->get_integer('pgcache.comment_cookie_ttl'); ?>" size="8" /> seconds
                        <br /><span class="description">Significantly reduce the default <acronym title="Time to Live">TTL</acronym> for comment cookies to reduce the number of authenticated user traffic. Enter -1 to revert to default <acronym title="Time to Live">TTL</acronym>.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_reject_ua">Rejected user agents:</label></th>
                <td>
                    <textarea id="pgcache_reject_ua" name="pgcache.reject.ua" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.reject.ua'))); ?></textarea><br />
                    <span class="description">Never send cache pages for these user agents.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_reject_cookie">Rejected cookies:</label></th>
                <td>
                    <textarea id="pgcache_reject_cookie" name="pgcache.reject.cookie" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.reject.cookie'))); ?></textarea><br />
                    <span class="description">Never cache pages that use the specified cookies.</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_reject_uri">Never cache the following pages:</label></th>
                <td>
                    <textarea id="pgcache_reject_uri" name="pgcache.reject.uri" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.reject.uri'))); ?></textarea><br />
                    <span class="description">Always ignore the specified pages / directories. Supports regular expression (See <a href="<?php echo network_admin_url('admin.php?page=w3tc_faq#q82')?>">FAQ</a>)</span>
                </td>
            </tr>
            <tr>
                <th><label for="pgcache_accept_files">Cache exception list:</label></th>
                <td>
                    <textarea id="pgcache_accept_files" name="pgcache.accept.files" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.accept.files'))); ?></textarea><br />
                    <span class="description">Cache the specified pages / directories even if listed in the "never cache the following pages" field. Supports regular expression (See <a href="<?php echo network_admin_url('admin.php?page=w3tc_faq#q82')?>">FAQ</a>)</span>
                </td>
            </tr>
            <?php if (substr($permalink_structure, -1) == '/'): ?>
            <tr>
                <th><label for="pgcache_accept_uri">Non-trailing slash pages:</label></th>
                <td>
                    <textarea id="pgcache_accept_uri" name="pgcache.accept.uri" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.accept.uri'))); ?></textarea><br />
                    <span class="description">Cache the specified pages even if they don't have tailing slash.</span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="pgcache_cache_headers">Specify page headers:</label></th>
                <td>
                    <textarea id="pgcache_cache_headers" name="pgcache.cache.headers" 
                        <?php $this->sealing_disabled('pgcache') ?>
                        cols="40" rows="5"<?php if (!W3TC_PHP5 || $this->_config->get_string('pgcache.engine') == 'file_generic'): ?> disabled="disabled"<?php endif; ?>><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.cache.headers'))); ?></textarea><br />
                    <span class="description">Specify additional page headers to cache.</span>
                </td>
            </tr>
            <?php if (w3_is_nginx() && $this->_config->get_string('pgcache.engine') == 'file_generic'): ?>
            <tr>
                <th><label>Handle <acronym title="Extensible Markup Language">XML</acronym> mime type</label></th>
                <td>
                    <?php $this->checkbox('pgcache.cache.nginx_handle_xml', true) ?> Handle XML mime type</label><br />
                    <span class="description">Return correct Content-Type header for XML files. Slows down cache engine.</span>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Note(s)', '', 'notes'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li>Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "<acronym title="Hypertext Markup Language">HTML</acronym>" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
                        <li>The <acronym title="Time to Live">TTL</acronym> of page cache files is set via the "Expires header lifetime" field in the "<acronym title="Hypertext Markup Language">HTML</acronym>" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>