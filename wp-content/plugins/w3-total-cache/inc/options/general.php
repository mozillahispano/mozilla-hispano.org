<?php if (!defined('W3TC')) die();?>

<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<p>
	<?php 
		echo sprintf('The plugin is currently %1$s . If an option is disabled it means that either your current installation is not compatible or software installation is required.','<span class="w3tc-'.($enabled ? 'enabled">' . __('enabled', 'w3-total-cache') : 'disabled">' . __('disabled', 'w3-total-cache')) . '</span>.')
	?>
</p>

<?php if (!$this->_config_admin->get_boolean('common.visible_by_master_only') ||
    (is_super_admin() && (!w3_force_master() || is_network_admin()))): ?>
<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('General', 'w3-total-cache'), '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <label>
                        <input id="enabled" type="checkbox" name="enabled" value="1"<?php checked($enabled_checkbox, true); ?> />
                        <?php _e('Toggle all caching types on or off (at once)', 'w3-total-cache'); ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th>Preview mode:</th>
                <td>
                    <?php echo $this->nonce_field('w3tc'); ?>
                    <?php if ($this->_config->is_preview()): ?>
                        <input type="submit" name="w3tc_preview_disable" class="button-primary" value="<?php _e('Disable', 'w3-total-cache'); ?>" />
                        <?php echo $this->button_link(__('Preview', 'w3-total-cache'), w3_get_home_url() . '/?w3tc_preview=1', true); ?>
                        <?php echo $this->button_link(__('Deploy', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_preview_deploy', $this->_page), 'w3tc') ); ?>
                    <?php else: ?>
                        <input type="submit" name="w3tc_preview_enable" class="button-primary" value="<?php _e('Enable', 'w3-total-cache'); ?>" />
                    <?php endif; ?>
                    <br /><span class="description"><?php _e('Use preview mode to test configuration scenarios prior to releasing them (deploy) on the actual site. Preview mode remains active even after deploying settings until the feature is disabled.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Page Cache', 'w3-total-cache'), '', 'page_cache'); ?>
        <p><?php _e('Enable page caching to decrease the response time of the site.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <tr>
                <th><?php _e('Page cache:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('pgcache.enabled'); ?>&nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
                    <br /><span class="description"><?php _e('Caching pages will reduce the response time of your site and increase the scale of your web server.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Page cache method:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="pgcache.engine" <?php $this->sealing_disabled('pgcache') ?>>
                        <optgroup label="<?php _e('Shared Server (disk enhanced is best):', 'w3-total-cache'); ?>">
                            <option value="file"<?php selected($this->_config->get_string('pgcache.engine'), 'file'); ?>><?php _e('Disk: Basic', 'w3-total-cache'); ?></option>
                            <option value="file_generic"<?php selected($this->_config->get_string('pgcache.engine'), 'file_generic'); ?><?php if (! $check_rules): ?> disabled="disabled"<?php endif; ?>><?php _e('Disk: Enhanced', 'w3-total-cache'); ?></option>
                        </optgroup>
                        <optgroup label="<?php _e('Dedicated / Virtual Server:', 'w3-total-cache'); ?>">
                            <option value="apc"<?php selected($this->_config->get_string('pgcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: Alternative PHP Cache (APC)', 'w3-total-cache'); ?></option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('pgcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: eAccelerator', 'w3-total-cache'); ?></option>
                            <option value="xcache"<?php selected($this->_config->get_string('pgcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: XCache', 'w3-total-cache'); ?></option>
                        <option value="wincache"<?php selected($this->_config->get_string('pgcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: WinCache', 'w3-total-cache'); ?></option>
                        </optgroup>
                        <optgroup label="<?php _e('Multiple Servers:', 'w3-total-cache'); ?>">
                            <option value="memcached"<?php selected($this->_config->get_string('pgcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>><?php _e('Memcached', 'w3-total-cache'); ?></option>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="pgcache.late_init" value="0" />
                    <label><input id="pgcache_late_init" type="checkbox" name="pgcache.late_init" value="1"<?php checked($this->_config->get_boolean('pgcache.late_init'), true); ?> <?php disabled($this->_config->get_string('pgcache.engine'), 'file_generic') ?> /><?php _e('Use late "init"', 'w3-total-cache')?></label>
                    <br /><span class="description"><?php _e('Adds support for use of WordPress functionality in fragment caching calls. Usage of this option will increase response times.', 'w3-total-cache')?></span>
                </th>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('pgcache.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input type="submit" name="w3tc_flush_pgcache" value="<?php _e('Empty cache', 'w3-total-cache'); ?>"<?php if (! $pgcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Minify', 'w3-total-cache'), '', 'minify'); ?>
        <p><?php _e('Reduce load time by decreasing the size and number of <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> files. Automatically remove unncessary data from <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym>, feed, page and post <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <tr>
                <th><?php _e('Minify:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('minify.enabled', $this->_config->get_boolean('cloudflare.enabled') && $cloudflare_minify>0); ?>&nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
                    <?php if ($this->_config->get_boolean('cloudflare.enabled') && $cloudflare_minify>0): ?>
                    <br /><span class="description"><?php _e('Minify is disabled because CloudFlare minification is enabled.', 'w3-total-cache'); ?></span>
                    <?php endif ?>
                    <br /><span class="description"><?php _e('Minification can decrease file size of <acronym title="Hypertext Markup Language">HTML</acronym>, <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym> and feeds respectively by ~10% on average.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Minify mode:', 'w3-total-cache'); ?></th>
                <td>
                    <label><input type="radio" name="minify.auto" value="1"<?php checked($this->_config->get_boolean('minify.auto'), true); $this->sealing_disabled('minify'); ?> /> <?php _e('Auto', 'w3-total-cache'); ?></label>
                    <label><input type="radio" name="minify.auto" value="0"<?php checked($this->_config->get_boolean('minify.auto'), false); $this->sealing_disabled('minify'); ?> /> <?php _e('Manual', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Select manual mode to use fields on the minify settings tab to specify files to be minified, otherwise files will be minified automatically.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Minify cache method:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="minify.engine" <?php $this->sealing_disabled('minify'); ?>>
                        <optgroup label="<?php _e('Shared Server (disk is best):', 'w3-total-cache'); ?>">
                            <option value="file"<?php selected($this->_config->get_string('minify.engine'), 'file'); ?>><?php _e('Disk', 'w3-total-cache'); ?></option>
                        </optgroup>
                        <optgroup label="<?php _e('Dedicated / Virtual Server:', 'w3-total-cache'); ?>">
                            <option value="apc"<?php selected($this->_config->get_string('minify.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: Alternative PHP Cache (APC)', 'w3-total-cache'); ?></option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('minify.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: eAccelerator', 'w3-total-cache'); ?></option>
                            <option value="xcache"<?php selected($this->_config->get_string('minify.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: XCache', 'w3-total-cache'); ?></option>
                            <option value="wincache"<?php selected($this->_config->get_string('minify.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: WinCache', 'w3-total-cache'); ?></option>
                        </optgroup>
                            <optgroup label="<?php _e('Multiple Servers:', 'w3-total-cache'); ?>">
                            <option value="memcached"<?php selected($this->_config->get_string('minify.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>><?php _e('Memcached', 'w3-total-cache'); ?></option>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('<acronym title="Hypertext Markup Language">HTML</acronym> minifier:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="minify.html.engine"<?php $this->sealing_disabled('minify'); ?>>
                        <option value="html"<?php selected($this->_config->get_string('minify.html.engine'), 'html'); ?>><?php _e('Default', 'w3-total-cache'); ?></option>
                        <option value="htmltidy"<?php selected($this->_config->get_string('minify.html.engine'), 'htmltidy'); ?><?php if (! $check_tidy): ?> disabled="disabled"<?php endif; ?>><?php _e('HTML Tidy', 'w3-total-cache'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('<acronym title="JavaScript">JS</acronym> minifier:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="minify.js.engine"<?php $this->sealing_disabled('minify'); ?>>
                        <option value="js"<?php selected($this->_config->get_string('minify.js.engine'), 'js'); ?>><?php _e('JSMin (default)', 'w3-total-cache'); ?></option>
                        <option value="yuijs"<?php selected($this->_config->get_string('minify.js.engine'), 'yuijs'); ?>><?php _e('YUI Compressor', 'w3-total-cache'); ?></option>
                        <option value="ccjs"<?php selected($this->_config->get_string('minify.js.engine'), 'ccjs'); ?>><?php _e('Closure Compiler', 'w3-total-cache'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('<acronym title="Cascading Style Sheets">CSS</acronym> minifier:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="minify.css.engine"<?php $this->sealing_disabled('minify'); ?>>
                        <option value="css"<?php selected($this->_config->get_string('minify.css.engine'), 'css'); ?>><?php _e('Default', 'w3-total-cache'); ?></option>
                        <option value="yuicss"<?php selected($this->_config->get_string('minify.css.engine'), 'yuicss'); ?>><?php _e('YUI Compressor', 'w3-total-cache'); ?></option>
                        <option value="csstidy"<?php selected($this->_config->get_string('minify.css.engine'), 'csstidy'); ?>><?php _e('CSS Tidy', 'w3-total-cache'); ?></option>
                    </select>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('minify.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input type="submit" name="w3tc_flush_minify" value="<?php _e('Empty cache', 'w3-total-cache'); ?>"<?php if (! $minify_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Database Cache', 'w3-total-cache'), '', 'database_cache'); ?>
        <p><?php _e('Enable database caching to reduce post, page and feed creation time.', 'w3-total-cache'); ?></p>

         <table class="form-table">
            <tr>
                <th><?php _e('Database Cache:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('dbcache.enabled') ?>&nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
                    <br /><span class="description"><?php _e('Caching database objects decreases the response time of your site. Best used if object caching is not possible.', 'w3-total-cache'); ?>44</span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Database Cache Method:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="dbcache.engine" <?php $this->sealing_disabled('dbcache'); ?>>
                        <optgroup label="Shared Server:">
                            <option value="file"<?php selected($this->_config->get_string('dbcache.engine'), 'file'); ?>><?php _e('Disk', 'w3-total-cache'); ?></option>
                        </optgroup>
                        <optgroup label="Dedicated / Virtual Server:">
                            <option value="apc"<?php selected($this->_config->get_string('dbcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: Alternative PHP Cache (APC)', 'w3-total-cache'); ?></option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('dbcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: eAccelerator', 'w3-total-cache'); ?></option>
                            <option value="xcache"<?php selected($this->_config->get_string('dbcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: XCache', 'w3-total-cache'); ?></option>
                            <option value="wincache"<?php selected($this->_config->get_string('dbcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: WinCache', 'w3-total-cache'); ?></option>
                    </optgroup>
                        <optgroup label="Multiple Servers:">
                            <option value="memcached"<?php selected($this->_config->get_string('dbcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>><?php _e('Memcached', 'w3-total-cache'); ?></option>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('dbcache.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if (w3_is_enterprise() && $this->is_network_and_master()): ?>
             <?php include W3TC_INC_OPTIONS_DIR . '/enterprise/dbcluster_general_section.php' ?>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input type="submit" name="w3tc_flush_dbcache" value="<?php _e('Empty cache', 'w3-total-cache'); ?>"<?php if (! $dbcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Object Cache', '', 'object_cache'); ?>
        <p><?php _e('Enable object caching to further reduce execution time for common operations.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <tr>
                <th><?php _e('Object Cache:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('objectcache.enabled') ?>&nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
                    <br /><span class="description"><?php _e('Object caching greatly increases performance for highly dynamic sites that use the <a href="http://codex.wordpress.org/Class_Reference/WP_Object_Cache" target="_blank">Object Cache <acronym title="Application Programming Interface">API</acronym></a>.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Object Cache Method:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="objectcache.engine" <?php $this->sealing_disabled('objectcache'); ?>>
                        <optgroup label="<?php _e('Shared Server:', 'w3-total-cache'); ?>">
                            <option value="file"<?php selected($this->_config->get_string('objectcache.engine'), 'file'); ?>><?php _e('Disk', 'w3-total-cache'); ?></option>
                        </optgroup>
                        <optgroup label="<?php _e('Dedicated / Virtual Server:', 'w3-total-cache'); ?>">
                            <option value="apc"<?php selected($this->_config->get_string('objectcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: Alternative PHP Cache (APC)', 'w3-total-cache'); ?></option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('objectcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: eAccelerator', 'w3-total-cache'); ?></option>
                            <option value="xcache"<?php selected($this->_config->get_string('objectcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: XCache', 'w3-total-cache'); ?></option>
                            <option value="wincache"<?php selected($this->_config->get_string('objectcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>><?php _e('Opcode: WinCache', 'w3-total-cache'); ?></option>
                    </optgroup>
                        <optgroup label="<?php _e('Multiple Servers:', 'w3-total-cache'); ?>">
                            <option value="memcached"<?php selected($this->_config->get_string('objectcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>><?php _e('Memcached', 'w3-total-cache'); ?></option>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('objectcache.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input type="submit" name="w3tc_flush_objectcache" value="<?php _e('Empty cache', 'w3-total-cache'); ?>"<?php if (! $objectcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>
        <?php if (w3_is_pro() || w3_is_enterprise()): ?>
        <?php include W3TC_INC_OPTIONS_DIR . '/pro/fragmentcache_general_section.php' ?>
        <?php endif ?>
        <?php echo $this->postbox_header(__('Browser Cache', 'w3-total-cache'), '', 'browser_cache'); ?>
    <p><?php _e('Reduce server load and decrease response time by using the cache available in site visitor\'s web browser.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <tr>
                <th><?php _e('Browser Cache:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('browsercache.enabled') ?>&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description"><?php _e('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression and add headers to reduce server load and decrease file load time.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('browsercache.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('<acronym title="Content Delivery Network">CDN</acronym>', 'w3-total-cache'), '', 'cdn'); ?>
        <p><?php _e('Host static files with your content delivery network provider to reduce page load time.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <tr>
                <th><?php _e('<acronym title="Content Delivery Network">CDN</acronym>:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('cdn.enabled'); ?>&nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
                    <br /><span class="description"><?php _e('Theme files, media library attachments, <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym> files etc will appear to load instantly for site visitors.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('<acronym title="Content Delivery Network">CDN</acronym> Type:', 'w3-total-cache'); ?></th>
                <td>
                    <select name="cdn.engine" <?php $this->sealing_disabled('cdn'); ?>>
                        <optgroup label="Origin Pull / Mirror (recommended):">
                            <option value="akamai"<?php selected($this->_config->get_string('cdn.engine'), 'akamai'); ?>><?php _e('Akamai', 'w3-total-cache'); ?></option>
                            <option value="cf2"<?php selected($this->_config->get_string('cdn.engine'), 'cf2'); ?><?php if (!$check_curl): ?> disabled="disabled"<?php endif; ?>><?php _e('Amazon CloudFront', 'w3-total-cache'); ?></option>
							<option value="cotendo"<?php selected($this->_config->get_string('cdn.engine'), 'cotendo'); ?>><?php _e('Cotendo (Akamai)', 'w3-total-cache'); ?></option>
                            <option value="att"<?php selected($this->_config->get_string('cdn.engine'), 'att'); ?>><?php _e('AT&amp;T', 'w3-total-cache'); ?></option>
                            <option value="mirror"<?php selected($this->_config->get_string('cdn.engine'), 'mirror'); ?>><?php _e('Generic Mirror', 'w3-total-cache'); ?></option>
                            <option value="edgecast"<?php selected($this->_config->get_string('cdn.engine'), 'edgecast'); ?>><?php _e('Media Temple ProCDN / EdgeCast', 'w3-total-cache'); ?></option>
                            <option value="netdna"<?php selected($this->_config->get_string('cdn.engine'), 'netdna'); ?>><?php _e('NetDNA', 'w3-total-cache'); ?></option>
                            <option value="maxcdn"<?php selected($this->_config->get_string('cdn.engine'), 'maxcdn'); ?>><?php _e('MaxCDN', 'w3-total-cache'); ?></option>
                        </optgroup>
                        <optgroup label="Origin Push:">
                            <option value="cf"<?php selected($this->_config->get_string('cdn.engine'), 'cf'); ?><?php if (!$check_curl): ?> disabled="disabled"<?php endif; ?>><?php _e('Amazon CloudFront', 'w3-total-cache'); ?></option>
                            <option value="s3"<?php selected($this->_config->get_string('cdn.engine'), 's3'); ?><?php if (!$check_curl): ?> disabled="disabled"<?php endif; ?>><?php _e('Amazon Simple Storage Service (S3)', 'w3-total-cache'); ?></option>
							<option value="azure"<?php selected($this->_config->get_string('cdn.engine'), 'azure'); ?>><?php _e('Microsoft Azure Storage', 'w3-total-cache'); ?></option>
                            <option value="rscf"<?php selected($this->_config->get_string('cdn.engine'), 'rscf'); ?><?php if (!$check_curl): ?> disabled="disabled"<?php endif; ?>><?php _e('Rackspace Cloud Files', 'w3-total-cache'); ?></option>
							<option value="ftp"<?php selected($this->_config->get_string('cdn.engine'), 'ftp'); ?><?php if (!$check_ftp): ?> disabled="disabled"<?php endif; ?>><?php _e('Self-hosted / File Transfer Protocol Upload', 'w3-total-cache'); ?></option>
                        </optgroup>
                    </select><br />
                    <span class="description"><?php _e('Select the <acronym title="Content Delivery Network">CDN</acronym> type you wish to use.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('cdn.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input id="cdn_purge" type="button" value="<?php _e('Purge cache', 'w3-total-cache'); ?>"<?php if (!$cdn_enabled || !w3_can_cdn_purge($this->_config->get_string('cdn.engine'))): ?> disabled="disabled"<?php endif; ?> class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Reverse Proxy', 'w3-total-cache'), '', 'varnish'); ?>
        <p>
        	<?php echo sprintf( __('Purge policies are set on the <a href="%s">Page Cache settings</a> page.', 'w3-total-cache'), network_admin_url('admin.php?page=w3tc_pgcache') ); ?>
        </p>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('varnish.enabled'); ?> <?php _e('Enable varnish cache purging', 'w3-total-cache'); ?></label><br />
                </th>
            </tr>
             <tr>
                 <th><label for="pgcache_varnish_servers"><?php _e('Varnish servers:', 'w3-total-cache'); ?></label></th>
                 <td>
                    <textarea id="pgcache_varnish_servers" name="varnish.servers"
                          cols="40" rows="5" <?php $this->sealing_disabled('varnish'); ?>><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('varnish.servers'))); ?></textarea><br />
                    <span class="description"><?php _e('Specify the IP addresses of your varnish instances above. The <acronym title="Varnish Configuration Language">VCL</acronym>\'s <acronym title="Access Control List">ACL</acronym> must allow this request.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('varnish.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input type="submit" name="w3tc_flush_varnish" value="<?php _e('Purge cache', 'w3-total-cache'); ?>"<?php if (! $varnish_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php if (w3_is_enterprise()): ?>
        <?php echo $this->postbox_header('Amazon <acronym title="Simple Notification Service">SNS</acronym>', '', 'amazon_sns'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <input type="hidden" name="cluster.messagebus.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="cluster.messagebus.enabled" value="1"<?php checked($this->_config->get_boolean('cluster.messagebus.enabled'), true); ?> /> <?php _e('Manage the cache purge queue via <acronym title="Simple Notification Service">SNS</acronym>', 'w3-total-cache'); ?></label><br />
                </th>
            </tr>
            <tr>
                <th><label for="cluster_messagebus_sns_region"><?php _e('SNS region:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input id="cluster_messagebus_sns_region"
                        class="w3tc-ignore-change" type="text"
                        name="cluster.messagebus.sns.region"
                        value="<?php echo htmlspecialchars($this->_config->get_string('cluster.messagebus.sns.region')); ?>" size="60" /><br />
                    <span class="description"><?php _e('Specify the Amazon SNS service endpoint hostname. If empty, then default "sns.us-east-1.amazonaws.com" will be used.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cluster_messagebus_sns_api_key"><?php _e('<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input id="cluster_messagebus_sns_api_key"
                        class="w3tc-ignore-change" type="text"
                        name="cluster.messagebus.sns.api_key"
                        value="<?php echo htmlspecialchars($this->_config->get_string('cluster.messagebus.sns.api_key')); ?>" size="60" /><br />
                    <span class="description"><?php _e('Specify the <acronym title="Application Programming Interface">API</acronym> Key.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="cluster_messagebus_sns_api_secret"><?php _e('<acronym title="Application Programming Interface">API</acronym> secret:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input id="cluster_messagebus_sns_api_secret"
                        class="w3tc-ignore-change" type="text"
                        name="cluster.messagebus.sns.api_secret"
                        value="<?php echo htmlspecialchars($this->_config->get_string('cluster.messagebus.sns.api_secret')); ?>" size="60" /><br />
                    <span class="description"><?php _e('Specify the <acronym title="Application Programming Interface">API</acronym> secret.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($this->_config->get_string('cluster.messagebus.sns.topic_arn') != ''): ?>
            <tr>
                 <th><label><?php _e('Topic <acronym title="Identification">ID</acronym>:', 'w3-total-cache'); ?></label></th>
                 <td>
                    <?php echo htmlspecialchars($this->_config->get_string('cluster.messagebus.sns.topic_arn')); ?>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                 <th><label for="cluster_messagebus_sns_topic_arn_subscribe"><?php _e('Topic:', 'w3-total-cache'); ?></label></th>
                 <td>
                    <input id="cluster_messagebus_sns_topic_arn_subscribe"
                        class="w3tc-ignore-change" type="text"
                        name="cluster_messagebus_sns_topic_arn_subscribe"
                        value="" size="60" />
                    <input type="submit" name="w3tc_sns_subscribe" class="button"
                        value="Subscribe" /><br />
                    <span class="description"><?php _e('Subscribe to the <acronym title="Simple Notification Service">SNS</acronym> topic.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>
        <?php endif; ?>

        <?php echo $this->postbox_header(__('Network Performance &amp; Security powered by CloudFlare', 'w3-total-cache'), '', 'cloudflare'); ?>
        <p>
        	<?php echo sprintf( __('CloudFlare protects and accelerates websites. <a href="%s" target="_blank">Sign up now for free</a> to get started,
        	or if you have an account simply log in to obtain your <acronym title="Application Programming Interface">API</acronym> key from the <a href="https://www.cloudflare.com/my-account.html">account page</a> to enter it below.
        	Contact the CloudFlare <a href="http://www.cloudflare.com/help.html" target="_blank">support team</a> with any questions.', 'w3-total-cache'), 'https://www.cloudflare.com/sign-up.html?affiliate=w3edge&amp;seed_domain=' . w3_get_host() . '&amp;email=' . htmlspecialchars($cloudflare_signup_email) . '&amp;username=' . htmlspecialchars($cloudflare_signup_user) ); ?>
        </p>

        <table class="form-table">
            <tr>
                <th><?php _e('CloudFlare:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox('cloudflare.enabled'); ?>&nbsp;<strong><?php _e('Enable', 'w3-total-cache'); ?></strong></label>
                </td>
            </tr>
            <tr>
                <th><label for="cloudflare_email"><?php _e('CloudFlare account email:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input id="cloudflare_email" class="w3tc-ignore-change"
                        type="text" name="cloudflare.email"
                        <?php $this->sealing_disabled('cloudflare'); ?>
                        value="<?php echo htmlspecialchars($this->_config->get_string('cloudflare.email')); ?>" size="60" />
                </td>
            </tr>
            <tr>
                <th><label for="cloudflare_key"><?php _e('<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input id="cloudflare_key" class="w3tc-ignore-change"
                        type="password" name="cloudflare.key"
                        <?php $this->sealing_disabled('cloudflare'); ?>
                        value="<?php echo htmlspecialchars($this->_config->get_string('cloudflare.key')); ?>" size="60" /> (<a href="https://www.cloudflare.com/my-account.html"><?php _e('find it here', 'w3-total-cache'); ?></a>)
                </td>
            </tr>
            <tr>
                <th><?php _e('Domain:', 'w3-total-cache'); ?></th>
                <td>
                    <input id="cloudflare_zone" type="text" name="cloudflare.zone"
                        <?php $this->sealing_disabled('cloudflare'); ?>
                        value="<?php echo htmlspecialchars($this->_config->get_string('cloudflare.zone', w3_get_host())); ?>" size="40" />
                </td>
            </tr>
            <tr>
                <th><?php _e('Security level:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="hidden" name="cloudflare_sec_lvl_old" value="<?php echo $cloudflare_seclvl; ?>" />
                    <select name="cloudflare_sec_lvl_new"
                        class="w3tc-ignore-change"
                        <?php $this->sealing_disabled('cloudflare'); ?>>
                        <?php foreach ($cloudflare_seclvls as $cloudflare_seclvl_key => $cloudflare_seclvl_label): ?>
                        <option value="<?php echo $cloudflare_seclvl_key; ?>"<?php selected($cloudflare_seclvl, $cloudflare_seclvl_key); ?>><?php echo $cloudflare_seclvl_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('Rocket Loader:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="hidden" name="cloudflare_async_old" value="<?php echo $cloudflare_rocket_loader; ?>" />
                    <select name="cloudflare_async_new"
                            class="w3tc-ignore-change"
                        <?php $this->sealing_disabled('cloudflare'); ?>>
                        <?php foreach ($cloudflare_rocket_loaders as $cloudflare_rocket_loader_key => $cloudflare_rocket_loader_label): ?>
                        <option value="<?php echo $cloudflare_rocket_loader_key; ?>"<?php selected($cloudflare_rocket_loader, $cloudflare_rocket_loader_key); ?>><?php echo $cloudflare_rocket_loader_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('Minification:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="hidden" name="cloudflare_minify_old" value="<?php echo $cloudflare_minify; ?>" />
                    <select name="cloudflare_minify_new"
                            class="w3tc-ignore-change"
                        <?php $this->sealing_disabled('cloudflare'); ?>>
                        <?php foreach ($cloudflare_minifications as $cloudflare_minify_key => $cloudflare_minify_label): ?>
                        <option value="<?php echo $cloudflare_minify_key; ?>"<?php selected($cloudflare_minify, $cloudflare_minify_key); ?>><?php echo $cloudflare_minify_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('Development mode:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="hidden" name="cloudflare_devmode_old" value="<?php echo $cloudflare_devmode; ?>" />
                    <select name="cloudflare_devmode_new"
                        class="w3tc-ignore-change"
                        <?php $this->sealing_disabled('cloudflare'); ?>>
                        <?php foreach ($cloudflare_devmodes as $cloudflare_devmode_key => $cloudflare_devmode_label): ?>
                        <option value="<?php echo $cloudflare_devmode_key; ?>"<?php selected($cloudflare_devmode, $cloudflare_devmode_key); ?>><?php echo $cloudflare_devmode_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($cloudflare_devmode_expire): ?>
                    <?php echo sprintf( __('Will automatically turn off at $s', 'w3-total-cache'), date('m/d/Y H:i:s', $cloudflare_devmode_expire) ); ?>>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($this->is_network_and_master() && !w3_force_master()): ?>
                <tr>
                    <th><?php _e('Network policy:', 'w3-total-cache'); ?></th>
                    <td>
                        <?php $this->checkbox_admin('cloudflare.configuration_sealed'); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache'); ?></label>
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
            <input id="cloudflare_purge_cache" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="<?php _e('Purge cache', 'w3-total-cache'); ?>"<?php if (! $cloudflare_enabled): ?> disabled="disabled"<?php endif; ?> />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header(__('Monitoring', 'w3-total-cache'), '', 'monitoring')?>
        <?php if (!$new_relic_installed): ?>
            <p><?php echo sprintf(__('
                New Relic may not be installed on this server. %s. Visit %s for installation instructions.', 'w3-total-cache')
                , '<a href="' . esc_attr( NEWRELIC_SIGNUP_URL ) . '" target="_blank">' . __('Sign up for a (free) account', 'w3-total-cache') . '</a>'
                , '<a href="https://newrelic.com/docs/php/new-relic-for-php" target="_blank">New Relic</a>')
                ?>
            </p>
        <?php endif; ?>
        <?php if (!$this->is_master() && '' == $this->_config->get_string('newrelic.api_key')): ?>
        <p><span><?php _e('The network administrator has not provided the API Key.', 'w3-total-cache')?></span></p>
        <?php else: ?>
    <table class="form-table">
        <tr>
            <th>
                <label>New Relic:</label>
            </th>
            <td>
                <?php $this->checkbox('newrelic.enabled', false, '', false); ?> <strong><?php _e('Enable', 'w3-total-cache') ?></strong>
            </td>
        </tr>
        <?php if($this->is_master()): ?>
        <tr>
            <th>
                <label for="newrelic_api_key"><acronym title="Application Programming Interface">API</acronym> key:</label>
            </th>
            <td>
                <input id ="newrelic_api_key" name="newrelic.api_key" type="text" value="<?php echo esc_attr($this->_config->get_string('newrelic.api_key'))?>" size="45"/>
                <input id ="newrelic_account_id" name="newrelic.account_id" type="hidden" value="<?php echo esc_attr($this->_config->get_string('newrelic.account_id'))?>"/>
                <input id="newrelic_verify_api_key" type="button" value="<?php echo sprintf(__('Verify %s', 'w3-total-cache'), 'API Key') ?>"/>
            </td>
        </tr>
        <?php endif ?>
        <tr>
            <th>
                <label><?php _e('Application name:' ,'w3-total-cache') ?></label>
            </th>
            <td>
        <?php if($this->is_master() || !$this->_config->get_boolean('newrelic.use_network_wide_id')): ?>
                <p><?php _e('Obtain application ID via:', 'w3-total-cache')?> <br />
                    <label id="lbl_manual" for="manual" ><input id="manual" name="application_id_method" type="radio" title="Manual" value="manual" checked="checked" /><?php _e('Enter application name below:', 'w3-total-cache') ?></label>
                    <label for="dropdown"><input id="dropdown"  name="application_id_method" type="radio" title="Manual" value="dropdown" /><?php _e('Select from the following list:', 'w3-total-cache') ?></label>
                </p>
                <?php endif ?>
                <div id="newrelic_application_name_textbox_div">
                    <input id="newrelic_appname"  name="newrelic.appname" type="text" value="<?php esc_attr_e($newrelic_conf_appname)?>" <?php disabled($this->_config->get_boolean('newrelic.use_network_wide_id') && !$this->is_master()) ?>>
                </div>
                <?php if($this->is_master() || !$this->_config->get_boolean('newrelic.use_network_wide_id')): ?>
                <div id="newrelic_application_id_dropdown_div" style="display:none">
                    <select id="newrelic_application_id_dropdown" name="newrelic.application_id" <?php disabled($this->_config->get_boolean('newrelic.use_network_wide_id') && !$this->is_master()) ?>>
                        <option value=""><?php _e('-- Select Application --', 'w3-total-cache')?></option>
                        <?php foreach($newrelic_applications as $id => $name): ?>
                        <option value="<?php echo esc_attr($id)?>" <?php echo ($id == $newrelic_application)?'selected="selected"' : '' ?>><?php echo esc_textarea($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$this->is_master()): ?>
                    <input id ="newrelic_api_key" type="hidden" value="0" />
                    <?php endif ?>
                    <input id="newrelic_retrieve_applications" type="button" value="Retrieve Applications"/>
                </div>
                <p><span class="description"><?php _e('Note: Changing application name may create a new application in New Relic if no application with that name already exists.', 'w3-total-cache')?></span></p>
        <?php endif;?>
            </td>
        </tr>
        <?php if ($this->is_network_and_master()): ?>
        <tr>
            <th><?php _e('Use above application name and ID for all sites in network:', 'w3-total-cache')?></th>
            <td><?php $this->checkbox('newrelic.use_network_wide_id'); ?></label></td>
        </tr>
        <?php if (!w3_force_master()): ?>
        <tr>
            <th><?php _e('Network policy:', 'w3-total-cache') ?></th>
            <td>
                <?php $this->checkbox_admin('newrelic.configuration_sealed', $this->_config->get_boolean('newrelic.use_network_wide_id')); ?> <?php _e('Apply the settings above to the entire network.', 'w3-total-cache') ?></label>
            </td>
        </tr>
        <?php endif; ?>
        <?php endif; ?>
    </table>
    <p class="submit">
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
    </p>
        <?php endif ?>
        <?php echo $this->postbox_footer(); ?>
    
        <?php echo $this->postbox_header(__('Miscellaneous', 'w3-total-cache'), '', 'miscellaneous'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <input type="hidden" name="widget.pagespeed.enabled" value="0" />
                    <label><input type="checkbox" name="widget.pagespeed.enabled" value="1"<?php checked($this->_config->get_boolean('widget.pagespeed.enabled'), true); ?> />  <?php _e('Enable Google Page Speed dashboard widget', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Display Google Page Speed results on the WordPress dashboard.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th><label for="widget_pagespeed_key"><?php _e('Page Speed <acronym title="Application Programming Interface">API</acronym> Key:', 'w3-total-cache'); ?></label></th>
                <td>
                    <input id="widget_pagespeed_key" type="text" name="widget.pagespeed.key" value="<?php echo $this->_config->get_string('widget.pagespeed.key'); ?>" size="60" /><br />
                    <span class="description"><?php _e('To acquire an <acronym title="Application Programming Interface">API</acronym> key, visit the <a href="https://code.google.com/apis/console" target="_blank"><acronym title="Application Programming Interface">API</acronym>s Console</a>. Go to the Project Home tab, activate the Page Speed Online <acronym title="Application Programming Interface">API</acronym>, and accept the Terms of Service.
                    Then go to the <acronym title="Application Programming Interface">API</acronym> Access tab. The <acronym title="Application Programming Interface">API</acronym> key is in the Simple <acronym title="Application Programming Interface">API</acronym> Access section.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php if ($this->is_network_and_master()): ?>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('common.force_master') ?> <?php _e('Use single network configuration file for all sites.', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Only one configuration file for whole network will be created and used. Recommended if all sites have the same configuration.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox_admin('common.visible_by_master_only') ?> <?php _e('Hide performance settings', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Prevent sites from independently managing their performance settings.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <?php endif; ?>
            <?php if (w3_is_nginx()): ?>
            <tr>
                <th><?php _e('Nginx server configuration file path', 'w3-total-cache'); ?></th>
                <td>
                    <input type="text" name="config.path" value="<?php echo htmlspecialchars($this->_config->get_string('config.path')); ?>" size="80" />
                    <br /><span class="description"><?php _e('If empty the default path will be used..', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="config.check" value="0" />
                    <label><input type="checkbox" name="config.check" value="1"<?php checked($this->_config->get_boolean('config.check'), true); ?> /> <?php _e('Verify rewrite rules', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Notify of server configuration errors, if this option is disabled, the server configuration for active settings can be found on the <a href="admin.php?page=w3tc_install">install</a> tab.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="file_locking" value="0"<?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> />
                    <label><input type="checkbox" name="file_locking" value="1"<?php checked($file_locking, true); ?><?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> /> <?php _e('Enable file locking', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Not recommended for <acronym title="Network File System">NFS</acronym> systems.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="file_nfs" value="0"<?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> />
                    <label><input type="checkbox" name="file_nfs" value="1"<?php checked($file_nfs, true); ?><?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> /> <?php _e('Optimize disk enhanced page and minify disk caching for <acronym title="Network File System">NFS</acronym>', 'w3-total-cache'); ?></label>
                    <br /><span class="description"><?php _e('Try this option if your hosting environment uses a network based file system for a possible performance improvement.', 'w3-total-cache'); ?></span>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Debug', '', 'debug'); ?>
        <p><?php _e('Detailed information about each cache will be appended in (publicly available) <acronym title="Hypertext Markup Language">HTML</acronym> comments in the page\'s source code. Performance in this mode will not be optimal, use sparingly and disable when not in use.', 'w3-total-cache'); ?></p>

        <table class="form-table">
            <tr>
                <th><?php _e('Debug Mode:', 'w3-total-cache'); ?></th>
                <td>
                    <?php $this->checkbox_debug('pgcache.debug') ?> <?php _e('Page Cache', 'w3-total-cache'); ?></label><br />
                    <?php $this->checkbox_debug('minify.debug') ?> <?php _e('Minify', 'w3-total-cache'); ?></label><br />
                    <?php $this->checkbox_debug('dbcache.debug') ?> <?php _e('Database Cache', 'w3-total-cache'); ?></label><br />
                    <?php $this->checkbox_debug('objectcache.debug') ?> <?php _e('Object Cache', 'w3-total-cache'); ?></label><br />
                    <?php if (w3_is_pro() || w3_is_enterprise()): ?>
                    <?php $this->checkbox_debug('fragmentcache.debug') ?> <?php _e('Fragment Cache', 'w3-total-cache'); ?></label><br />
                    <?php endif; ?>
                    <?php $this->checkbox_debug('cdn.debug') ?> <?php _e('<acronym title="Content Delivery Network">CDN</acronym>', 'w3-total-cache'); ?></label><br />
                    <?php $this->checkbox_debug('varnish.debug') ?> <?php _e('Reverse Proxy', 'w3-total-cache'); ?></label><br />
                    <?php if (w3_is_enterprise()): ?>
                    <?php $this->checkbox_debug('cluster.messagebus.debug') ?> <?php _e('Amazon <acronym title="Simple Notification Service">SNS</acronym>', 'w3-total-cache'); ?></label><br />
                    <?php endif; ?>
                    <span class="description"><?php _e('If selected, detailed caching information will be appear at the end of each page in a <acronym title="Hypertext Markup Language">HTML</acronym> comment. View a page\'s source code to review.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="<?php _e('Save all settings', 'w3-total-cache'); ?>" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post" enctype="multipart/form-data">
    <div class="metabox-holder">
        <?php echo $this->postbox_header(__('Import / Export Settings', 'w3-total-cache'), '', 'settings'); ?>
        <?php echo $this->nonce_field('w3tc'); ?>
        <table class="form-table">
            <tr>
                <th><?php _e('Import configuration:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="file" name="config_file" />
                    <input type="submit" name="w3tc_config_import" class="w3tc-button-save button" value="<?php _e('Upload', 'w3-total-cache'); ?>" />
                    <br /><span class="description"><?php _e('Upload and replace the active settings file.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Export configuration:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="submit" name="w3tc_config_export" class="button" value="<?php _e('Download', 'w3-total-cache'); ?>" />
                    <br /><span class="description"><?php _e('Download the active settings file.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
            <tr>
                <th><?php _e('Reset configuration:', 'w3-total-cache'); ?></th>
                <td>
                    <input type="submit" name="w3tc_config_reset" class="button" value="<?php _e('Restore Default Settings', 'w3-total-cache'); ?>" />
                    <br /><span class="description"><?php _e('Revert all settings to the defaults. Any settings staged in preview mode will not be modified.', 'w3-total-cache'); ?></span>
                </td>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>
<?php endif ?>
<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
