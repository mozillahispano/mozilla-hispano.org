<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
    var minify_templates = {};
    <?php foreach ($templates as $theme_key => $theme_templates): ?>
    minify_templates['<?php echo addslashes($theme_key); ?>'] = {};
    <?php foreach ($theme_templates as $theme_template_key => $theme_template_name): ?>
    minify_templates['<?php echo addslashes($theme_key); ?>']['<?php echo addslashes($theme_template_key); ?>'] = '<?php echo addslashes($theme_template_name); ?>';
    <?php endforeach; ?>
    <?php endforeach; ?>
/*]]>*/</script>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Minify via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('minify.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($minify_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
        To rebuild the minify cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_minify" value="empty cache"<?php if (! $minify_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        operation.
        <?php if (!$auto): ?>
        Get minify hints using the
        <input type="button" class="button button-minify-recommendations {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="help" />
        wizard.
        <?php endif; ?><input type="submit" name="w3tc_flush_browser_cache" value="Update media query string"<?php disabled(! ($browsercache_enabled && $browsercache_update_media_qs)) ?> class="button" /> to make existing file modififications visible to visitors with a primed cache.
    </p>
</form>

<form id="minify_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('General', '', 'general'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('minify.rewrite', !w3_can_check_rules() || $minify_rewrite_disabled) ?> Rewrite <acronym title="Uniform Resource Locator">URL</acronym> structure</label><br />
                    <span class="description">If disabled, <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> embeddings will use GET variables instead of "fancy" links.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <?php $this->checkbox('minify.reject.logged') ?> Disable minify for logged in users</label><br />
                    <span class="description">Authenticated users will not receive minified pages if this option is enabled.</span>
                </th>
            </tr>
            <tr>
                <th>
                    <label for="minify_error_notification">Minify error notification:</label>
                </th>
                <td>
                    <select id="minify_error_notification" name="minify.error.notification"
                        <?php $this->sealing_disabled('minify') ?>>
                        <?php $value = $this->_config_admin->get_string('minify.error.notification'); ?>
                        <option value=""<?php selected($value, ''); ?>>Disabled</option>
                        <option value="admin"<?php selected($value, 'admin'); ?>>Admin Notification</option>
                        <option value="email"<?php selected($value, 'email'); ?>>Email Notification</option>
                        <option value="admin,email"<?php selected($value, 'admin,email'); ?>>Both Admin &amp; Email Notification</option>
                    </select>
                    <br /><span class="description">Notify when minify cache creation errors occur.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>', '', 'html_xml'); ?>
        <table class="form-table">
            <tr>
                <th><acronym title="Hypertext Markup Language">HTML</acronym> minify settings:</th>
                <td>
                    <?php $this->checkbox('minify.html.enable') ?> Enable</label><br />
                    <?php $this->checkbox('minify.html.inline.css', false, 'html_') ?> Inline <acronym title="Cascading Style Sheet">CSS</acronym> minification</label><br />
                    <?php $this->checkbox('minify.html.inline.js', false, 'html_') ?> Inline <acronym title="JavaScript">JS</acronym> minification</label><br />
                    <?php $this->checkbox('minify.html.reject.feed', false, 'html_') ?> Don't minify feeds</label><br />
                    <?php
                        $html_engine_file = '';

                        switch ($html_engine) {
                            case 'html':
                            case 'htmltidy':
                                $html_engine_file = W3TC_INC_DIR . '/options/minify/' . $html_engine . '.php';
                                break;
                        }

                        if (file_exists($html_engine_file)) {
                            include $html_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="minify_html_comments_ignore">Ignored comment stems:</label></th>
                <td>
                    <textarea id="minify_html_comments_ignore" 
                        <?php $this->sealing_disabled('minify') ?>
                        name="minify.html.comments.ignore" class="html_enabled" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.html.comments.ignore'))); ?></textarea><br />
                    <span class="description">Do not remove comments that contain these terms.</span>
                </td>
            </tr>
            <?php
                $html_engine_file2 = '';

                switch ($html_engine_file2) {
                    case 'html':
                    case 'htmltidy':
                        $html_engine_file = W3TC_INC_DIR . '/options/minify/' . $html_engine . '2.php';
                        break;
                }

                if (file_exists($html_engine_file2)) {
                    include $html_engine_file2;
                }
            ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('<acronym title="JavaScript">JS</acronym>', '', 'js'); ?>
        <table class="form-table">
            <tr>
                <th><acronym title="JavaScript">JS</acronym> minify settings:</th>
                <td>
                    <?php $this->checkbox('minify.js.enable') ?> Enable</label><br />
                    <fieldset><legend>Operations in areas:</legend>
                        <p>
                            <span>Embed type:</span>
                        </p>
                        <span class="oia-desc">Before <span class="html-tag">&lt;/head&gt;</span></span>
                        <?php $this->radio('minify.js.combine.header', false, false, 'js_') ?> Minify </label> <?php $this->radio('minify.js.combine.header', true, false, 'js_') ?> Combine only</label>
                        <select id="js_use_type_header" name="minify.js.header.embed_type" class="js_enabled">
                            <option value="blocking" <?php selected('blocking' ,$this->_config->get_string('minify.js.header.embed_type')) ?>>Default (blocking)</option>
                            <option value="nb-js" <?php selected('nb-js' ,$this->_config->get_string('minify.js.header.embed_type')) ?>>Non-blocking using JS</option>
                            <option value="nb-async" <?php selected('nb-async' ,$this->_config->get_string('minify.js.header.embed_type')) ?>>Non-blocking using "async"</option>
                            <option value="nb-defer" <?php selected('nb-defer' ,$this->_config->get_string('minify.js.header.embed_type')) ?>>Non-blocking using "defer"</option>
                            <?php if (!$auto): ?>
                            <option value="extsrc" <?php selected('extsrc' ,$this->_config->get_string('minify.js.header.embed_type')) ?>>Non-blocking using "extsrc"</option>
                            <option value="asyncsrc" <?php selected('asyncsrc' ,$this->_config->get_string('minify.js.header.embed_type')) ?>>Non-blocking using "asyncsrc"</option>
                            <?php endif; ?>
                        </select>
                        <?php if (!$auto): ?>
                        <br />
                        <span class="oia-desc">After <span class="html-tag">&lt;body&gt;</span></span>
                        <?php $this->radio('minify.js.combine.body', false, $auto, 'js_') ?> Minify </label> <?php $this->radio('minify.js.combine.body', true) ?> Combine only</label>
                            <select id="js_use_type_body" name="minify.js.body.embed_type" class="js_enabled">
                                <option value="blocking" <?php selected('blocking' ,$this->_config->get_string('minify.js.body.embed_type')) ?>>Default (blocking)</option>
                                <option value="nb-js" <?php selected('nb-js' ,$this->_config->get_string('minify.js.body.embed_type')) ?>>Non-blocking using JS</option>
                                <option value="nb-async" <?php selected('nb-async' ,$this->_config->get_string('minify.js.body.embed_type')) ?>>Non-blocking using "async"</option>
                                <option value="nb-defer" <?php selected('nb-defer' ,$this->_config->get_string('minify.js.body.embed_type')) ?>>Non-blocking using "defer"</option>
                                <option value="extsrc" <?php selected('extsrc' ,$this->_config->get_string('minify.js.body.embed_type')) ?>>Non-blocking using "extsrc"</option>
                                <option value="asyncsrc" <?php selected('asyncsrc' ,$this->_config->get_string('minify.js.body.embed_type')) ?>>Non-blocking using "asyncsrc"</option>
                            </select>
                            <br />
                        <span class="oia-desc">Before <span class="html-tag">&lt;/body&gt;</span></span>
                        <?php $this->radio('minify.js.combine.footer', false, $auto, 'js_') ?> Minify </label> <?php $this->radio('minify.js.combine.footer', true) ?> Combine only</label>
                            <select id="js_use_type_footer" name="minify.js.footer.embed_type" class="js_enabled">
                                <option value="blocking" <?php selected('blocking' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>>Default (blocking)</option>
                                <option value="nb-js" <?php selected('nb-js' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>>Non-blocking using JS</option>
                                <option value="nb-async" <?php selected('nb-async' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>>Non-blocking using "async"</option>
                                <option value="nb-defer" <?php selected('nb-defer' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>>Non-blocking using "defer"</option>
                                <option value="extsrc" <?php selected('extsrc' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>>Non-blocking using "extsrc"</option>
                                <option value="asyncsrc" <?php selected('asyncsrc' ,$this->_config->get_string('minify.js.footer.embed_type')) ?>>Non-blocking using "asyncsrc"</option>
                            </select>
                            <?php endif; ?>
                    </fieldset>
                    <?php
                        $js_engine_file = '';

                        switch ($js_engine) {
                            case 'js':
                            case 'yuijs':
                            case 'ccjs':
                                $js_engine_file = W3TC_INC_DIR . '/options/minify/' . $js_engine . '.php';
                                break;
                        }

                        if (file_exists($js_engine_file)) {
                            include $js_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <?php
                $js_engine_file2 = '';

                switch ($js_engine) {
                    case 'js':
                    case 'yuijs':
                    case 'ccjs':
                        $js_engine_file2 = W3TC_INC_DIR . '/options/minify/' . $js_engine . '2.php';
                        break;
                }

                if (file_exists($js_engine_file2)) {
                    include $js_engine_file2;
                }
            ?>
            <?php if (!$auto): ?>
            <tr>
                <th><acronym title="JavaScript">JS</acronym> file management:</th>
                <td>
                    <p>
                        <label>
                            Theme:
                            <select id="js_themes" class="js_enabled" name="js_theme"
                                <?php $this->sealing_disabled('minify') ?>>
                                <?php foreach ($themes as $theme_key => $theme_name): ?>
                                <option value="<?php echo htmlspecialchars($theme_key); ?>"<?php selected($theme_key, $js_theme); ?>><?php echo htmlspecialchars($theme_name); ?><?php if ($theme_key == $js_theme): ?> (active)<?php endif; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <br /><span class="description">Files are minified by template. First select the theme to manage, then add scripts used in all templates to the "All Templates" group. Use the menu above to manage scripts unique to a specific template. If necessary drag &amp; drop to resolve dependency issues (due to incorrect order).</span>
                    </p>
                    <ul id="js_files" class="minify-files">
                    <?php foreach ($js_groups as $js_theme => $js_templates): if (isset($templates[$js_theme])): ?>
                        <?php $index = 0; foreach ($js_templates as $js_template => $js_locations): ?>
                            <?php foreach ((array) $js_locations as $js_location => $js_config): ?>
                                <?php if (! empty($js_config['files'])): foreach ((array) $js_config['files'] as $js_file): $index++; ?>
                                <li>
                                    <table>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>File URI:</th>
                                            <th>Template:</th>
                                            <th colspan="3">Embed Location:</th>
                                        </tr>
                                        <tr>
                                            <td><?php echo $index; ?>.</td>
                                            <td>
                                                <input class="js_enabled" type="text"
                                                     <?php $this->sealing_disabled('minify') ?>
                                                     name="js_files[<?php echo htmlspecialchars($js_theme); ?>][<?php echo htmlspecialchars($js_template); ?>][<?php echo htmlspecialchars($js_location); ?>][]" value="<?php echo htmlspecialchars($js_file); ?>" size="70" />
                                            </td>
                                            <td>
                                                <select class="js_file_template js_enabled" <?php $this->sealing_disabled('minify') ?>>
                                                    <?php foreach ($templates[$js_theme] as $theme_template_key => $theme_template_name): ?>
                                                    <option value="<?php echo htmlspecialchars($theme_template_key); ?>"<?php selected($theme_template_key, $js_template); ?>><?php echo htmlspecialchars($theme_template_name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="js_file_location js_enabled" <?php $this->sealing_disabled('minify') ?>>
                                                    <option value="include" <?php selected($js_location,'include') ?>>Embed in &lt;head&gt;</option>
                                                    <option value="include-body" <?php selected($js_location, 'include-body') ?>>Embed after &lt;body&gt;</option>
                                                    <option value="include-footer" <?php selected($js_location, 'include-footer') ?>>Embed before &lt;/body&gt;</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="js_file_delete js_enabled button" type="button" value="Delete" />
                                                <input class="js_file_verify js_enabled button" type="button" value="Verify URI" />
                                            </td>
                                        </tr>
                                    </table>
                                </li>
                                <?php endforeach; endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>
                    </ul>
                    <div id="js_files_empty" class="w3tc-empty" style="display: none;">No <acronym title="JavaScript">JS</acronym> files added</div>
                    <input id="js_file_add" class="js_enabled button" type="button" value="Add a script" />
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('<acronym title="Cascading Style Sheet">CSS</acronym>', '', 'css'); ?>
        <table class="form-table">
            <tr>
                <th><acronym title="Cascading Style Sheet">CSS</acronym> minify settings:</th>
                <td>
                    <?php $this->checkbox('minify.css.enable') ?> Enable</label><br />
                    <?php $this->checkbox('minify.css.combine', false, 'css_') ?> Combine only</label><br />
                    <?php
                        $css_engine_file = '';

                        switch ($css_engine) {
                            case 'css':
                            case 'yuicss':
                            case 'csstidy':
                                $css_engine_file = W3TC_INC_DIR . '/options/minify/' . $css_engine . '.php';
                                break;
                        }

                        if (file_exists($css_engine_file)) {
                            include $css_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="minify_css_import">@import handling:</label></th>
                <td>
                    <select id="minify_css_import" class="css_enabled" name="minify.css.imports"
                        <?php $this->sealing_disabled('minify') ?>>
                        <?php foreach ($css_imports_values as $css_imports_key => $css_imports_value): ?>
                        <option value="<?php echo $css_imports_key; ?>"<?php selected($css_imports, $css_imports_key); ?>><?php echo $css_imports_value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php
                $css_engine_file2 = '';

                switch ($css_engine) {
                    case 'css':
                    case 'yuicss':
                    case 'csstidy':
                        $css_engine_file2 = W3TC_INC_DIR . '/options/minify/' . $css_engine . '2.php';
                        break;
                }

                if (file_exists($css_engine_file2)) {
                    include $css_engine_file2;
                }
            ?>
            <?php if (!$auto): ?>
            <tr>
                <th><acronym title="Cascading Style Sheet">CSS</acronym> file management:</th>
                <td>
                    <p>
                        <label>
                            Theme:
                            <select id="css_themes" class="css_enabled" name="css_theme"
                                <?php $this->sealing_disabled('minify') ?>>
                                <?php foreach ($themes as $theme_key => $theme_name): ?>
                                <option value="<?php echo htmlspecialchars($theme_key); ?>"<?php selected($theme_key, $css_theme); ?>><?php echo htmlspecialchars($theme_name); ?><?php if ($theme_key == $css_theme): ?> (active)<?php endif; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <br /><span class="description">Files are minified by template. First select the theme to manage, then add style sheets used in all templates to the "All Templates" group. Use the menu above to manage style sheets unique to a specific template. If necessary drag &amp; drop to resolve dependency issues (due to incorrect order).</span>
                    </p>
                    <ul id="css_files" class="minify-files">
                    <?php foreach ($css_groups as $css_theme => $css_templates): if (isset($templates[$css_theme])): ?>
                        <?php $index = 0; foreach ($css_templates as $css_template => $css_locations): ?>
                            <?php foreach ((array) $css_locations as $css_location => $css_config): ?>
                                <?php if (! empty($css_config['files'])): foreach ((array) $css_config['files'] as $css_file): $index++; ?>
                                <li>
                                    <table>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>File URI:</th>
                                            <th colspan="2">Template:</th>
                                        </tr>
                                        <tr>
                                            <td><?php echo $index; ?>.</td>
                                            <td>
                                                <input class="css_enabled" type="text"
                                                    <?php $this->sealing_disabled('minify') ?>
                                                    name="css_files[<?php echo htmlspecialchars($css_theme); ?>][<?php echo htmlspecialchars($css_template); ?>][<?php echo htmlspecialchars($css_location); ?>][]" value="<?php echo htmlspecialchars($css_file); ?>" size="70" /><br />
                                            </td>
                                            <td>
                                                <select class="css_file_template css_enabled" <?php $this->sealing_disabled('minify') ?>>
                                                <?php foreach ($templates[$css_theme] as $theme_template_key => $theme_template_name): ?>
                                                    <option value="<?php echo htmlspecialchars($theme_template_key); ?>"<?php selected($theme_template_key, $css_template); ?>><?php echo htmlspecialchars($theme_template_name); ?></option>
                                                <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="css_file_delete css_enabled button" type="button" value="Delete" />
                                                <input class="css_file_verify css_enabled button" type="button" value="Verify URI" />
                                            </td>
                                        </tr>
                                    </table>
                                </li>
                                <?php endforeach; endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>
                    </ul>
                    <div id="css_files_empty" class="w3tc-empty" style="display: none;">No <acronym title="Cascading Style Sheet">CSS</acronym> files added</div>
                    <input id="css_file_add" class="css_enabled button" type="button" value="Add a style sheet" />
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Advanced', '', 'advanced'); ?>
        <table class="form-table">
        <?php if ($this->_config->get_string('minify.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
                <td>
                    <input id="memcached_servers" type="text"
                        <?php $this->sealing_disabled('minify') ?>
                        name="minify.memcached.servers" value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('minify.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}"
                        type="button" value="Test" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="minify_lifetime">Update external files every:</label></th>
                <td>
                    <input id="minify_lifetime" type="text" name="minify.lifetime"
                        <?php $this->sealing_disabled('minify') ?>
                        value="<?php echo $this->_config->get_integer('minify.lifetime'); ?>" size="8" /> seconds<br />
                    <span class="description">Specify the interval between download and update of external files in the minify cache. Hint: 6 hours is 21600 seconds. 12 hours is 43200 seconds. 24 hours is 86400 seconds.</span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_file_gc">Garbage collection interval:</label></th>
                <td>
                    <input id="minify_file_gc" type="text" name="minify.file.gc"
                        <?php $this->sealing_disabled('minify') ?>
                        value="<?php echo $this->_config->get_integer('minify.file.gc'); ?>" size="8"<?php if ($this->_config->get_string('minify.engine') != 'file'): ?> disabled="disabled"<?php endif; ?> /> seconds
                    <br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_uri">Never minify the following pages:</label></th>
                <td>
                    <textarea id="minify_reject_uri" name="minify.reject.uri" 
                        <?php $this->sealing_disabled('minify') ?> cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.reject.uri'))); ?></textarea><br />
                    <span class="description">Always ignore the specified pages / directories.</span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_files_js">Never minify the following JS files:</label></th>
                <td>
                    <textarea id="minify_reject_files_js" name="minify.reject.files.js"
                        <?php $this->sealing_disabled('minify') ?> cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.reject.files.js'))); ?></textarea><br />
                    <span class="description">Always ignore the specified JS files.</span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_files_css">Never minify the following CSS files:</label></th>
                <td>
                    <textarea id="minify_reject_files_css" name="minify.reject.files.css"
                        <?php $this->sealing_disabled('minify') ?> cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.reject.files.css'))); ?></textarea><br />
                    <span class="description">Always ignore the specified CSS files.</span>
                </td>
            </tr>
            <tr>
                <th><label for="minify_reject_ua">Rejected user agents:</label></th>
                <td>
                    <textarea id="minify_reject_ua" name="minify.reject.ua"
                        <?php $this->sealing_disabled('minify') ?>
                        cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.reject.ua'))); ?></textarea><br />
                    <span class="description">Specify user agents that will never receive minified content.</span>
                </td>
            </tr>
            <?php if ($auto): ?>
            <tr>
                <th><label for="minify_cache_files">Include external files/libaries:</label></th>
                <td>
                    <textarea id="minify_cache_files" name="minify.cache.files"
                        <?php $this->sealing_disabled('minify') ?>
                              cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.cache.files'))); ?></textarea><br />
                    <span class="description">Specify external files/libraries that should be combined.</span>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Note(s):', '', 'notes'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li>Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "Cascading Style Sheets &amp; JavaScript" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
                        <li>The <acronym title="Time to Live">TTL</acronym> of page cache files is set via the "Expires header lifetime" field in the "Cascading Style Sheets &amp; JavaScript" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>