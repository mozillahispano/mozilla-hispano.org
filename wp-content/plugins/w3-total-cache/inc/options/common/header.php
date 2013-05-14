<?php if (!defined('W3TC')) die(); ?>

<?php if ($this->_support_reminder): ?>
<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
    w3tc_lightbox_support_us('<?php echo wp_create_nonce('w3tc'); ?>');
});
/*]]>*/</script>
<?php endif; ?>
<div class="wrap" id="w3tc">
    <h2 class="logo"><?php _e('W3 Total Cache <span>by W3 EDGE <sup>&reg;</sup></span>', 'w3-total-cache'); ?></h2>

    <?php foreach ($this->_errors as $error): ?>
    <div class="error">
        <p><?php echo $error; ?></p>
    </div>
    <?php endforeach; ?>

    <?php if (!$this->_disable_cache_write_notification && $this->_rule_errors_autoinstall != ''): ?>
    <div class="error">
        <p>
            <?php _e('The following configuration changes are needed to ensure optimal performance:', 'w3-total-cache'); ?><br />
        </p>
            <ul style="padding-left: 20px">
                <?php foreach ($this->_rule_errors as $error): ?>
                    <li><?php echo $error[0]; ?></li>
                <?php endforeach; ?>
            </ul>

        <p>
            <?php _e('If permission allow this can be done automatically, by clicking here:', 'w3-total-cache'); ?>
            <?php echo $this->_rule_errors_autoinstall ?>.
            <?php echo $this->_rule_errors_hide ?>
        </p>
    </div>
    <?php endif; ?>

    <?php if (!$this->_disable_file_operation_notification && $this->_rule_errors_root): ?>
    <div class="error">
        <p>
            <?php _e('The following configuration changes are needed to ensure optimal performance:', 'w3-total-cache'); ?><br />
        </p>
        <ul style="padding-left: 20px">
            <?php foreach ($this->_rule_errors_root as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>

    <?php if (isset($this->_ftp_form) && ($this->_use_ftp_form || $this->_rule_errors_root)): ?>
        <p>
            <?php _e('If permission allow this can be done using the <a href="#ftp_upload_form">FTP form</a> below.', 'w3-total-cache'); ?> <?php echo $this->_rule_errors_root_hide; ?>
        </p>
    <?php endif ?>
    </div>
    <?php endif ?>

    <?php if (isset($this->_ftp_form) && ($this->_use_ftp_form || $this->_rule_errors_root)): ?>
    <div id="ftp_upload_form">
        <?php echo $this->_ftp_form ?>
    </div>
    <?php endif; ?>

    <?php foreach ($this->_notes as $note): ?>
    <div class="updated fade">
        <p><?php echo $note; ?></p>
    </div>
    <?php endforeach; ?>

    <?php if (!$this->_config_admin->get_boolean('common.visible_by_master_only') || (is_super_admin() &&
    (!w3_force_master() || is_network_admin()))): ?>
    <p id="w3tc-options-menu">
        <?php
            switch ($this->_page){
                case 'w3tc_general':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#page_cache"><?php _e('Page Cache', 'w3-total-cache'); ?></a> |
                    <a href="#minify">Minify</a> |
                    <a href="#database_cache"><?php _e('Database Cache', 'w3-total-cache'); ?></a> |
                    <a href="#object_cache"><?php _e('Object Cache', 'w3-total-cache'); ?></a> |
                    <?php if (w3_is_pro() || w3_is_enterprise()): ?>
                        <a href="#fragment_cache"><?php _e('Fragment Cache', 'w3-total-cache'); ?></a> |
                    <?php endif; ?>
                    <a href="#browser_cache"><?php _e('Browser Cache', 'w3-total-cache'); ?></a> |
                    <a href="#cdn"><?php _e('<acronym title="Content Delivery Network">CDN</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#varnish"><?php _e('Varnish', 'w3-total-cache'); ?></a> |
                    <?php if (w3_is_enterprise()): ?>
                        <a href="#amazon_sns"><?php _e('Amazon <acronym title="Simple Notification Service">SNS</acronym>', 'w3-total-cache'); ?></a> |
                    <?php endif; ?>
                    <a href="#cloudflare"><?php _e('Cloudflare', 'w3-total-cache'); ?></a> |
                    <a href="#monitoring"><?php _e('Monitoring', 'w3-total-cache'); ?></a> |
                    <a href="#miscellaneous"><?php _e('Miscellaneous', 'w3-total-cache'); ?></a> |
                    <a href="#debug"><?php _e('Debug', 'w3-total-cache'); ?></a> |
                    <a href="#settings"><?php _e('Import / Export Settings', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_pgcache':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a> |
                    <a href="#cache_preload"><?php _e('Cache Preload', 'w3-total-cache'); ?></a> |
                    <a href="#purge_policy"><?php _e('Purge Policy', 'w3-total-cache'); ?></a> |
                    <a href="#notes"><?php _e('Note(s)', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_minify':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#html_xml"><?php _e('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#js"><?php _e('<acronym title="JavaScript">JS</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#css"><?php _e('<acronym title="Cascading Style Sheet">CSS</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a> |
                    <a href="#notes"><?php _e('Note(s)', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_dbcache':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_objectcache':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_browsercache':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#css_js"><?php _e('<acronym title="Cascading Style Sheet">CSS</acronym> &amp; <acronym title="JavaScript">JS</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#html_xml"><?php _e('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>', 'w3-total-cache'); ?></a> |
                    <a href="#media"><?php _e('Media', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_mobile':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#manage"><?php _e('Manage User Agent Groups', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_referrer':
        ?>
                    <?php _e('Jump to: ', 'w3-total-cache'); ?>
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#manage"><?php _e('Manage Referrer Groups', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_cdn':
        ?>
                    <?php _e('Jump to:', 'w3-total-cache'); ?> 
                    <a href="#toplevel_page_w3tc_general"><?php _e('Main Menu', 'w3-total-cache'); ?></a> |
                    <a href="#general"><?php _e('General', 'w3-total-cache'); ?></a> |
                    <a href="#configuration"><?php _e('Configuration', 'w3-total-cache'); ?></a> |
                    <a href="#advanced"><?php _e('Advanced', 'w3-total-cache'); ?></a> |
                    <a href="#notes"><?php _e('Note(s)', 'w3-total-cache'); ?></a>
        <?php
                    break;
        ?>

        <?php
            }            
        ?>
    </p>
<?php endif ?>
