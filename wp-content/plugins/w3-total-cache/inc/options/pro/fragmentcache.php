<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Fragment caching via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('fragmentcache.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($fragmentcache_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_fragmentcache" value="Empty the entire cache"<?php if (! $fragmentcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        if needed.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('Overview', '', 'overview'); ?>
        <table class="form-table">
        <tr>
            <th>Registered fragment groups:</th>
            <td>
                <ul>
                    <?php foreach ($registered_groups as $group => $actions)
                    echo '<li>',$group,': ',implode(',', $actions), '</li>';
                    ?>
                </ul>
                <span class="description">Groups that will be flushed on actions.</span>
            </td>
        </tr>
        <?php if (w3_is_network()): ?>
            <tr>
                <th>Registered site wide fragment groups:</th>
                <td>
                    <ul>
                        <?php foreach ($registered_global_groups as $group => $actions)
                        echo '<li>',$group,': ',implode(',', $actions), '</li>';
                        ?>
                    </ul>
                    <span class="description">Site wide groups that will be flushed on actions.</span>
                </td>
            </tr>
        <?php endif ?>
        </table>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Advanced', '', 'advanced'); ?>
        <table class="form-table">
            <?php if ($this->_config->get_string('fragmentcache.engine') == 'memcached'): ?>
            <tr>
                <th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
                <td>
                    <input id="memcached_servers" type="text"
                        <?php $this->sealing_disabled('fragmentcache') ?> name="fragmentcache.memcached.servers" value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('fragmentcache.memcached.servers'))); ?>" size="100" />
                    <input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test" />
                    <span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
                    <br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th style="width: 250px;"><label for="fragmentcache_lifetime">Default lifetime of cached fragments:</label></th>
                <td>
                    <input id="fragmentcache_lifetime" type="text"
                        <?php $this->sealing_disabled('fragmentcache') ?> name="fragmentcache.lifetime" value="<?php echo $this->_config->get_integer('fragmentcache.lifetime'); ?>" size="8" /> seconds
                    <br /><span class="description">Determines the expiration time of unchanged cache items. The higher the value, the larger the cache.</span>
                </td>
            </tr>
            <tr>
                <th><label for="fragmentcache_file_gc">Garbage collection interval:</label></th>
                <td>
                    <input id="fragmentcache_file_gc" type="text"
                        <?php $this->sealing_disabled('fragmentcache') ?> name="fragmentcache.file.gc" value="<?php echo $this->_config->get_integer('fragmentcache.file.gc'); ?>" size="8" /> seconds
                    <br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
                </td>
            </tr>
            <tr>
                <th>Manual fragment groups:</th>
                <td>
                    <textarea id="fragmentcache_groups" name="fragmentcache.groups"
                        <?php $this->sealing_disabled('fragmentcache') ?>
                              cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('fragmentcache.groups'))); ?></textarea><br />
                    <span class="description">Specify fragment groups that should be handled one per line. Actions to be performed should be entered on same line comma delimited, e.g. group, action1, action2.</span>
                </td>
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