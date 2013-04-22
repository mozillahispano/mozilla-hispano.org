<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Browser caching is currently <span class="w3tc-<?php if ($browsercache_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_browser_cache" value="Update media query string" <?php disabled(! ($browsercache_enabled && $browsercache_update_media_qs)) ?> class="button" /> to make existing file modifications visible to visitors with a primed cache.
    </p>
</form>
<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('General', '', 'general'); ?>
        <p>Specify global browser cache policy.</p>

        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <label>
                    <input id="browsercache_last_modified" type="checkbox" name="expires"
                        <?php $this->sealing_disabled('browsercache') ?>
                           value="1"<?php checked($browsercache_last_modified, true); ?> /> Set Last-Modified header</label>
                    <br /><span class="description">Set the Last-Modified header to enable 304 Not Modified response.</span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <label>
                        <input id="browsercache_expires" type="checkbox" name="expires"
                            <?php $this->sealing_disabled('browsercache') ?>
                            value="1"<?php checked($browsercache_expires, true); ?> /> Set expires header</label>
                    <br /><span class="description">Set the expires header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_cache_control" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?> name="cache_control" value="1"<?php checked($browsercache_cache_control, true); ?> /> Set cache control header</label>
                    <br /><span class="description">Set pragma and cache-control headers to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_etag" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="etag" value="1"<?php checked($browsercache_etag, true); ?> /> Set entity tag (eTag)</label>
                    <br /><span class="description">Set the Etag header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_w3tc" type="checkbox" name="w3tc"
                        <?php $this->sealing_disabled('browsercache') ?> value="1"<?php checked($browsercache_w3tc, true); ?> /> Set W3 Total Cache header</label>
                    <br /><span class="description">Set this header to assist in identifying optimized files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_compression" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="compression"<?php checked($browsercache_compression, true); ?> value="1" /> Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression</label>
                    <br /><span class="description">Reduce the download time for text-based files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_replace" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="replace" value="1"<?php checked($browsercache_replace, true); ?> /> Prevent caching of objects after settings change</label>
                    <br /><span class="description">Whenever settings are changed, a new query string will be generated and appended to objects allowing the new policy to be applied.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <label><input id="browsercache_nocookies" type="checkbox"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="nocookies" value="1"<?php checked($browsercache_nocookies, true); ?> /> Disable cookies for static files</label>
                    <br /><span class="description">Removes Set-Cookie header for responses.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.no404wp', !w3_can_check_rules()) ?> Do not process 404 errors for static objects with WordPress</label>
                    <br /><span class="description">Reduce server load by allowing the web server to handle 404 (not found) errors for static files (images etc).</span>
                </th>
            </tr>
            <tr>
                <th><label for="browsercache_no404wp_exceptions">404 error exception list:</label></th>
                <td>
                    <textarea id="browsercache_no404wp_exceptions"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="browsercache.no404wp.exceptions" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('browsercache.no404wp.exceptions'))); ?></textarea><br />
                    <span class="description">Never process 404 (not found) events for the specified files.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('<acronym title="Cascading Style Sheet">CSS</acronym> &amp; <acronym title="JavaScript">JS</acronym>', '', 'css_js'); ?>
        <p>Specify browser cache policy for Cascading Style Sheets and JavaScript files.</p>

        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.last_modified') ?> Set Last-Modified header</label>
                    <br /><span class="description">Set the Last-Modified header to enable 304 Not Modified response.</span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.expires') ?> Set expires header</label>
                    <br /><span class="description">Set the expires header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_cssjs_lifetime">Expires header lifetime:</label>
                </th>
                <td>
                    <input id="browsercache_cssjs_lifetime" type="text"
                       <?php $this->sealing_disabled('browsercache') ?>
                       name="browsercache.cssjs.lifetime" value="<?php echo $this->_config->get_integer('browsercache.cssjs.lifetime'); ?>" size="8" /> seconds
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.cache.control') ?> Set cache control header</label>
                    <br /><span class="description">Set pragma and cache-control headers to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_cssjs_cache_policy">Cache Control policy:</label>
                </th>
                <td>
                    <select id="browsercache_cssjs_cache_policy"
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="browsercache.cssjs.cache.policy">
                        <?php $value = $this->_config->get_string('browsercache.cssjs.cache.policy'); ?>
                        <option value="cache"<?php selected($value, 'cache'); ?>>cache ("public")</option>
                        <option value="cache_public_maxage"<?php selected($value, 'cache_public_maxage'); ?>>cache with max-age ("public, max-age=EXPIRES_SECONDS")</option>
                        <option value="cache_validation"<?php selected($value, 'cache_validation'); ?>>cache with validation ("public, must-revalidate, proxy-revalidate")</option>
                        <option value="cache_maxage"<?php selected($value, 'cache_maxage'); ?>>cache with max-age and validation ("max-age=EXPIRES_SECONDS, public, must-revalidate, proxy-revalidate")</option>
                        <option value="cache_noproxy"<?php selected($value, 'cache_noproxy'); ?>>cache without proxy ("private, must-revalidate")</option>
                        <option value="no_cache"<?php selected($value, 'no_cache'); ?>>no-cache ("max-age=0, private, no-store, no-cache, must-revalidate")</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.etag') ?> Set entity tag (eTag)</label>
                    <br /><span class="description">Set the Etag header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.w3tc') ?> Set W3 Total Cache header</label>
                    <br /><span class="description">Set this header to assist in identifying optimized files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.compression') ?> Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression</label>
                    <br /><span class="description">Reduce the download time for text-based files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.replace') ?> Prevent caching of objects after settings change</label>
                    <br /><span class="description">Whenever settings are changed, a new query string will be generated and appended to objects allowing the new policy to be applied.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.cssjs.nocookies') ?> Disable cookies for static files</label>
                    <br /><span class="description">Removes Set-Cookie header for responses.</span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="Extensible Markup Language">XML</acronym>', '', 'html_xml'); ?>
        <p>Specify browser cache policy for posts, pages, feeds and text-based files.</p>

        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.last_modified') ?> Set Last-Modified header</label>
                    <br /><span class="description">Set the Last-Modified header to enable 304 Not Modified response.</span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.expires') ?> Set expires header</label>
                    <br /><span class="description">Set the expires header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th style="width: 250px;">
                    <label for="browsercache_html_lifetime">Expires header lifetime:</label>
                </th>
                <td>
                    <input id="browsercache_html_lifetime" type="text" 
                       name="browsercache.html.lifetime"
                       <?php $this->sealing_disabled('browsercache') ?>
                       value="<?php echo $this->_config->get_integer('browsercache.html.lifetime'); ?>" size="8" /> seconds
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.cache.control') ?> Set cache control header</label>
                    <br /><span class="description">Set pragma and cache-control headers to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_html_cache_policy">Cache Control policy:</label>
                </th>
                <td>
                    <select id="browsercache_html_cache_policy" name="browsercache.html.cache.policy"
                        <?php $this->sealing_disabled('browsercache') ?>>
                        <?php $value = $this->_config->get_string('browsercache.html.cache.policy'); ?>
                        <option value="cache"<?php selected($value, 'cache'); ?>>cache ("public")</option>
                        <option value="cache_public_maxage"<?php selected($value, 'cache_public_maxage'); ?>>cache with max-age ("public, max-age=EXPIRES_SECONDS")</option>
                        <option value="cache_validation"<?php selected($value, 'cache_validation'); ?>>cache with validation ("public, must-revalidate, proxy-revalidate")</option>
                        <option value="cache_maxage"<?php selected($value, 'cache_maxage'); ?>>cache with max-age and validation ("max-age=EXPIRES_SECONDS, public, must-revalidate, proxy-revalidate")</option>
                        <option value="cache_noproxy"<?php selected($value, 'cache_noproxy'); ?>>cache without proxy ("private, must-revalidate")</option>
                        <option value="no_cache"<?php selected($value, 'no_cache'); ?>>no-cache ("max-age=0, private, no-store, no-cache, must-revalidate")</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.etag') ?> Set entity tag (eTag)</label>
                    <br /><span class="description">Set the Etag header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.w3tc') ?> Set W3 Total Cache header</label>
                    <br /><span class="description">Set this header to assist in identifying optimized files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.html.compression') ?> Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression</label>
                    <br /><span class="description">Reduce the download time for text-based files.</span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Media &amp; Other Files', '', 'media'); ?>
        <table class="form-table">
            <?php if (!w3_is_nginx()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.last_modified') ?> Set Last-Modified header</label>
                    <br /><span class="description">Set the Last-Modified header to enable 304 Not Modified response.</span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.expires') ?> Set expires header</label>
                    <br /><span class="description">Set the expires header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th style="width: 250px;">
                    <label for="browsercache_other_lifetime">Expires header lifetime:</label>
                </th>
                <td>
                    <input id="browsercache_other_lifetime" type="text"
                       <?php $this->sealing_disabled('browsercache') ?>
                       name="browsercache.other.lifetime" value="<?php echo $this->_config->get_integer('browsercache.other.lifetime'); ?>" size="8" /> seconds
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.cache.control') ?> Set cache control header</label>
                    <br /><span class="description">Set pragma and cache-control headers to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="browsercache_other_cache_policy">Cache Control policy:</label>
                </th>
                <td>
                    <select id="browsercache_other_cache_policy" 
                        <?php $this->sealing_disabled('browsercache') ?>
                        name="browsercache.other.cache.policy">
                        <?php $value = $this->_config->get_string('browsercache.other.cache.policy'); ?>
                        <option value="cache"<?php selected($value, 'cache'); ?>>cache ("public")</option>
                        <option value="cache_public_maxage"<?php selected($value, 'cache_public_maxage'); ?>>cache with max-age ("public, max-age=EXPIRES_SECONDS")</option>
                        <option value="cache_validation"<?php selected($value, 'cache_validation'); ?>>cache with validation ("public, must-revalidate, proxy-revalidate")</option>
                        <option value="cache_maxage"<?php selected($value, 'cache_maxage'); ?>>cache with max-age and validation ("max-age=EXPIRES_SECONDS, public, must-revalidate, proxy-revalidate")</option>
                        <option value="cache_noproxy"<?php selected($value, 'cache_noproxy'); ?>>cache without proxy ("private, must-revalidate")</option>
                        <option value="no_cache"<?php selected($value, 'no_cache'); ?>>no-cache ("max-age=0, private, no-store, no-cache, must-revalidate")</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.etag') ?> Set entity tag (eTag)</label>
                    <br /><span class="description">Set the Etag header to encourage browser caching of files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.w3tc') ?> Set W3 Total Cache header</label>
                    <br /><span class="description">Set this header to assist in identifying optimized files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.compression') ?> Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression</label>
                    <br /><span class="description">Reduce the download time for text-based files.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.replace') ?> Prevent caching of objects after settings change</label>
                    <br /><span class="description">Whenever settings are changed, a new query string will be generated and appended to objects allowing the new policy to be applied.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('browsercache.other.nocookies') ?> Disable cookies for static files</label>
                    <br /><span class="description">Removes Set-Cookie header for responses.</span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
