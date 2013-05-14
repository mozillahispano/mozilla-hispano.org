<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<p>
	<?php echo sprintf(
		__('Content Delivery Network support via %1$s is currently %2$s.', 'w3-total-cache'),
		'<strong>'.w3_get_engine_name($this->_config->get_string('cdn.engine')).'</strong>',
		'<span class="w3tc-' . ($cdn_enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>'
	); ?>
</p>
<form id="w3tc_cdn" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
<?php if ($cdn_mirror): ?>
    Maximize <acronym title="Content Delivery Network">CDN</acronym> usage by <input id="cdn_rename_domain" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="modify attachment URLs" /> or
    <input id="cdn_import_library" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="importing attachments into the Media Library" />.
    <?php if (w3_can_cdn_purge($cdn_engine)): ?>
        <input id="cdn_purge" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Purge" /> objects from the <acronym title="Content Delivery Network">CDN</acronym> using this tool
    <?php endif; ?>
    <?php if ($cdn_mirror_purge_all): ?>
        or <input class="button" type="submit" name="w3tc_flush_cdn" value="purge CDN completely" />
    <?php endif; ?>
    <?php if (w3_can_cdn_purge($cdn_engine)): ?>
        .
    <?php endif; ?>
<?php else: ?>
    <?php _e('Prepare the <acronym title="Content Delivery Network">CDN</acronym> by:', 'w3-total-cache') ?>
    <input id="cdn_import_library" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('importing attachments into the Media Library', 'w3-total-cache') ?>" />.
    Check <input id="cdn_queue" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('unsuccessful file transfers', 'w3-total-cache') ?>" /> <?php _e('if some objects appear to be missing.', 'w3-total-cache') ?>
    <?php if (w3_can_cdn_purge($cdn_engine)): ?>
    <input id="cdn_purge" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Purge', 'w3-total-cache') ?>" /> <?php _e('objects from the <acronym title="Content Delivery Network">CDN</acronym> if needed.', 'w3-total-cache') ?>
    <?php endif; ?>
    <input id="cdn_rename_domain" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Modify attachment URLs" /> <?php _e('if the domain name of your site has ever changed.', 'w3-total-cache') ?>
<?php endif; ?>
    <?php echo $this->nonce_field('w3tc'); ?>
    <?php if ((!$this->_config_admin->get_boolean('common.visible_by_master_only') || (is_super_admin() && (!w3_force_master() || is_network_admin())))): ?>
    <input type="submit" name="w3tc_flush_browser_cache" value="<?php _e('Update media query string', 'w3-total-cache') ?>" <?php disabled(! ($browsercache_enabled && $browsercache_update_media_qs)) ?> class="button" /> <?php _e('to make existing file modifications visible to visitors with a primed cache.', 'w3-total-cache') ?>
    <?php endif ?>
</p>
</form>
<form id="cdn_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('General', 'w3-total-cache'), '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th<?php if ($cdn_mirror): ?> colspan="2"<?php else: ?> style="width: 300px;"<?php endif; ?>>
                    <?php $this->checkbox('cdn.uploads.enable') ?> <?php _e('Host attachments', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('If checked, all attachments will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </th>
                <?php if (! $cdn_mirror): ?>
                <td>
                    <input id="cdn_export_library" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Upload attachments', 'w3-total-cache') ?>" />
                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
                    <?php $this->checkbox('cdn.includes.enable') ?> <?php _e('Host wp-includes/ files', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('If checked, WordPress static core file types specified in the "wp-includes file types to upload" field below will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </th>
                <?php if (! $cdn_mirror): ?>
                <td>
                    <input class="button cdn_export {type: 'includes', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Upload includes files', 'w3-total-cache') ?>" />
                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
                    <?php $this->checkbox('cdn.theme.enable') ?> <?php _e('Host theme files', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('If checked, all theme file types specified in the "theme file types to upload" field below will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </th>
                <?php if (! $cdn_mirror): ?>
                <td>
                    <input class="button cdn_export {type: 'theme', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Upload theme files', 'w3-total-cache') ?>" />
                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
                    <?php $this->checkbox('cdn.minify.enable', !$minify_enabled) ?> <?php _e('Host minified <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> files', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('If checked, minified <acronym>CSS</acronym> and <acronym>JS</acronym> files will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </th>
                <?php if (! $cdn_mirror): ?>
                <td>
                    <input class="button cdn_export {type: 'minify', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Upload minify files', 'w3-total-cache') ?>"<?php if (!$minify_enabled): ?> disabled="disabled"<?php endif; ?> />
                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
                    <?php $this->checkbox('cdn.custom.enable') ?> <?php _e('Host custom files', 'w3-total-cache') ?></label><br />
                    <span class="description">
                    	<?php echo sprintf( __('If checked, any file names or paths specified in the "custom file list" field below will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>. Supports regular expression (See <a href="%s">FAQ</a>)', 'w3-total-cache'), network_admin_url('admin.php?page=w3tc_faq#q82') ); ?>
                    </span>
                </th>
                <?php if (! $cdn_mirror): ?>
                <td>
                    <input class="button cdn_export {type: 'custom', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Upload custom files', 'w3-total-cache') ?>" />
                </td>
                <?php endif; ?>
            </tr>
            <?php if (! $cdn_mirror): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdn.force.rewrite') ?> <?php _e('Force over-writing of existing files', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('If modified files are not always detected and replaced, use this option to over-write them.', 'w3-total-cache') ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdn.import.external') ?> <?php _e('Import external media library attachments', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('Download attachments hosted elsewhere into your media library and deliver them via <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdncache.enabled', !$cdn_supports_full_page_mirroring) ?> <?php _e('Enable mirroring of pages', 'w3-total-cache') ?></label><br/>
                    <span class="description">
                    	<?php echo sprintf(
                    		__('Enabling this option allows the <acronym title="Content Delivery Network">CDN</acronym> to handle requests for unauthenticated pages thereby reducing the traffic load on the origin server(s). Purge policies are set on the <a href="%s">Page Cache settings</a> tab.', 'w3-total-cache'), network_admin_url('admin.php?page=w3tc_pgcache#purge_policy')
                    	); ?>
                    </span>
                </th>
            </tr>
            <?php if ($cdn_supports_header): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdn.canonical_header') ?> <?php _e('Add canonical header', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('Adds canonical <acronym title="Hypertext Transfer Protocol">HTTP</acronym> header to assets files.', 'w3-total-cache') ?></span>
                </th>
            </tr>
            <?php endif; ?>
        </table>
<?php if ((!$this->_config_admin->get_boolean('common.visible_by_master_only') || (is_super_admin() && (!w3_force_master() || is_network_admin())))): ?>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache') ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Configuration', 'w3-total-cache'), '', 'configuration'); ?>
        <table class="form-table">
            <?php
                if (w3_is_cdn_engine($cdn_engine)) {
                    include W3TC_INC_DIR . '/options/cdn/' . $cdn_engine . '.php';
                }
            ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache') ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Advanced', 'w3-total-cache'), '', 'advanced'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdn.reject.ssl') ?> <?php _e('Disable <acronym title="Content Delivery Network">CDN</acronym> on <acronym title="Secure Sockets Layer">SSL</acronym> pages', 'w3-total-cache') ?></label><br />
                    <span class="description">When <acronym title="Secure Sockets Layer">SSL</acronym> pages are returned no <acronym title="Content Delivery Network">CDN</acronym> <acronym title="Uniform Resource Indicator">URL</acronym>s will appear in HTML pages.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdn.reject.logged_roles') ?> <?php _e('Don\'t replace <acronym title="Uniform Resource Indicator">URL</acronym>s for following user roles', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('Select user roles that will use the origin server exclusively:', 'w3-total-cache') ?></span>

                    <div id="cdn_reject_roles">
                        <?php $saved_roles = $this->_config->get_array('cdn.reject.roles'); ?>
                        <input type="hidden" name="cdn.reject.roles" value="" /><br />
                        <?php foreach( get_editable_roles() as $role_name => $role_data ) : ?>
                        <input type="checkbox" name="cdn.reject.roles[]" value="<?php echo $role_name ?>" <?php checked( in_array( $role_name, $saved_roles ) ) ?> id="role_<?php echo $role_name ?>" />
                        <label for="role_<?php echo $role_name ?>"><?php echo $role_data['name'] ?></label>
                        <?php endforeach; ?>
                    </div>
                </th>
            </tr>
            <?php if (! $cdn_mirror): ?>
            <tr>
                <th><label for="cdn_reject_uri">Disable <acronym title="Content Delivery Network">CDN</acronym> on the following pages:</label></th>
                <td>
                    <textarea id="cdn_reject_uri" name="cdn.reject.uri"
                        <?php $this->sealing_disabled('cdn') ?>
                              cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.reject.uri'))); ?></textarea><br />
                    <span class="description">Always ignore the specified pages / directories. Supports regular expression (See <a href="<?php echo network_admin_url('admin.php?page=w3tc_faq#q82')?>">FAQ</a>)</span>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('minify.upload', $this->_config->get_boolean('minify.auto')) ?> <?php _e('Automatically upload minify files', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('If <acronym title="Content Delivery Network">CDN</acronym> is enabled (and not using the origin pull method), your minified files will be automatically uploaded.', 'w3-total-cache') ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('cdn.autoupload.enabled') ?> <?php _e('Export changed files automatically', 'w3-total-cache') ?></label><br />
                    <span class="description"><?php _e('Automatically attempt to find and upload changed files.', 'w3-total-cache') ?></span>
                </th>
            </tr>
            <tr>
                <th><label for="cdn_autoupload_interval"><?php _e('Auto upload interval:', 'w3-total-cache') ?></label></th>
                <td>
                    <input id="cdn_autoupload_interval" type="text" 
                       name="cdn.autoupload.interval"
                       <?php $this->sealing_disabled('cdn') ?>
                       value="<?php echo $this->_config->get_integer('cdn.autoupload.interval'); ?>" size="8" /> <?php _e('seconds', 'w3-total-cache') ?><br />
                    <span class="description"><?php _e('Specify the interval between upload of changed files.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_limit_interval"><?php _e('Re-transfer cycle interval:', 'w3-total-cache') ?></label></th>
                <td>
                    <input id="cdn_limit_interval" type="text"
                       <?php $this->sealing_disabled('cdn') ?>
                       name="cdn.queue.interval" value="<?php echo htmlspecialchars($this->_config->get_integer('cdn.queue.interval')); ?>" size="10" /> <?php _e('seconds', 'w3-total-cache') ?><br />
                    <span class="description"><?php _e('The number of seconds to wait before upload attempt.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_limit_queue"><?php _e('Re-transfer cycle limit:', 'w3-total-cache') ?></label></th>
                <td>
                    <input id="cdn_limit_queue" type="text"
                       <?php $this->sealing_disabled('cdn') ?>
                       name="cdn.queue.limit" value="<?php echo htmlspecialchars($this->_config->get_integer('cdn.queue.limit')); ?>" size="10" /><br />
                    <span class="description"><?php _e('Number of files processed per upload attempt.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="width: 300px;"><label for="cdn_includes_files"><?php _e('wp-includes file types to upload:', 'w3-total-cache') ?></label></th>
                <td>
                    <input id="cdn_includes_files" type="text"
                       <?php $this->sealing_disabled('cdn') ?>
                       name="cdn.includes.files" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.includes.files')); ?>" size="100" /><br />
                    <span class="description"><?php _e('Specify the file types within the WordPress core to host with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_theme_files"><?php _e('Theme file types to upload:', 'w3-total-cache') ?></label></th>
                <td>
                    <input id="cdn_theme_files" type="text" name="cdn.theme.files"
                       <?php $this->sealing_disabled('cdn') ?>
                       value="<?php echo htmlspecialchars($this->_config->get_string('cdn.theme.files')); ?>" size="100" /><br />
                    <span class="description"><?php _e('Specify the file types in the active theme to host with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_import_files"><?php _e('File types to import:', 'w3-total-cache') ?></label></th>
                <td>
                    <input id="cdn_import_files" type="text" name="cdn.import.files"
                       <?php $this->sealing_disabled('cdn') ?>
                       value="<?php echo htmlspecialchars($this->_config->get_string('cdn.import.files')); ?>" size="100" /><br />
                    <span class="description"><?php _e('Automatically import files hosted with 3rd parties of these types (if used in your posts / pages) to your media library.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_custom_files">Custom file list:</label></th>
                <td>
                    <textarea id="cdn_custom_files" name="cdn.custom.files" 
                        <?php $this->sealing_disabled('cdn') ?> cols="40"
                        rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.custom.files'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify any files outside of theme or other common directories to host with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?>
                        <?php if (w3_is_network()): ?>
                        <br />
                        <?php _e('To upload files in blogs.dir for current blog write wp-content/&lt;currentblog&gt;/.', 'w3-total-cache') ?>
                        <?php endif ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_reject_ua"><?php _e('Rejected user agents:', 'w3-total-cache') ?></label></th>
                <td>
                    <textarea id="cdn_reject_ua" name="cdn.reject.ua" cols="40"
                        <?php $this->sealing_disabled('cdn') ?> rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.reject.ua'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify user agents that should not access files hosted with the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cdn_reject_files"><?php _e('Rejected files:', 'w3-total-cache') ?></label></th>
                <td>
                    <textarea id="cdn_reject_files" name="cdn.reject.files"
                        <?php $this->sealing_disabled('cdn') ?> cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.reject.files'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify the path of files that should not use the <acronym title="Content Delivery Network">CDN</acronym>.', 'w3-total-cache') ?></span>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="set_cookie_domain_old" value="<?php echo (int) $set_cookie_domain; ?>" />
                    <input type="hidden" name="set_cookie_domain_new" value="0" />
                    <label><input type="checkbox" name="set_cookie_domain_new"
                        <?php $this->sealing_disabled('cdn') ?> value="1"<?php checked($set_cookie_domain, true); ?> /> Set cookie domain to &quot;<?php echo htmlspecialchars($cookie_domain); ?>&quot;</label>
                    <br /><span class="description"><?php _e('If using subdomain for <acronym title="Content Delivery Network">CDN</acronym> functionality, this setting helps prevent new users from sending cookies in requests to the <acronym title="Content Delivery Network">CDN</acronym> subdomain.', 'w3-total-cache') ?></span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache') ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Note(s):', 'w3-total-cache'), '', 'notes'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li><?php _e('You can use placeholders {wp_content_dir}, {plugins_dir}, {uploads_dir} instead of writing folder paths (wp-content, wp-content/plugins, wp-content/uploads).', 'w3-total-cache') ?></li>
                        <li><?php _e('If using Amazon Web Services or Self-Hosted <acronym title="Content Delivery Network">CDN</acronym> types, enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "Media &amp; Other Files" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.', 'w3-total-cache') ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php endif ?>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>
<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>