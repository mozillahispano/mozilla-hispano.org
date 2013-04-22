<?php if (!defined('W3TC')) die(); ?>

<?php if ($this->_support_reminder): ?>
<script type="text/javascript">/*<![CDATA[*/
jQuery(function($) {
    w3tc_lightbox_support_us('<?php echo wp_create_nonce('w3tc'); ?>');
});
/*]]>*/</script>
<?php endif; ?>

<div class="wrap" id="w3tc">
    <h2 class="logo">W3 Total Cache <span>by W3 EDGE <sup>&reg;</sup></span></h2>

    <?php foreach ($this->_errors as $error): ?>
    <div class="error">
        <p><?php echo $error; ?></p>
    </div>
    <?php endforeach; ?>

    <?php if (!$this->_disable_cache_write_notification && $this->_rule_errors_autoinstall != ''): ?>
    <div class="error">
        <p>
            The following configuration changes are needed to ensure optimal performance:<br />
        </p>
            <ul style="padding-left: 20px">
                <?php foreach ($this->_rule_errors as $error): ?>
                    <li><?php echo $error[0]; ?></li>
                <?php endforeach; ?>
            </ul>

        <p>
            If permission allow this can be done automatically, by clicking here:
            <?php echo $this->_rule_errors_autoinstall ?>.
            <?php echo $this->_rule_errors_hide ?>
        </p>
    </div>
    <?php endif; ?>

    <?php if (!$this->_disable_file_operation_notification && $this->_rule_errors_root): ?>
    <div class="error">
        <p>
            The following configuration changes are needed to ensure optimal performance:<br />
        </p>
        <ul style="padding-left: 20px">
            <?php foreach ($this->_rule_errors_root as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>

    <?php if (isset($this->_ftp_form) && ($this->_use_ftp_form || $this->_rule_errors_root)): ?>
        <p>
            If permission allow this can be done using the <a href="#ftp_upload_form">FTP form</a> below. <?php echo $this->_rule_errors_root_hide; ?>
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
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#general">General</a> |
                    <a href="#page_cache">Page Cache</a> |
                    <a href="#minify">Minify</a> |
                    <a href="#database_cache">Database Cache</a> |
                    <a href="#object_cache">Object Cache</a> |
                    <?php if (w3_is_pro() || w3_is_enterprise()): ?>
                        <a href="#fragment_cache">Fragment Cache</a> |
                    <?php endif; ?>
                    <a href="#browser_cache">Browser Cache</a> |
                    <a href="#cdn"><acronym title="Content Delivery Network">CDN</acronym></a> |
                    <a href="#varnish">Varnish</a> |
                    <?php if (w3_is_enterprise()): ?>
                        <a href="#amazon_sns">Amazon <acronym title="Simple Notification Service">SNS</acronym></a> |
                    <?php endif; ?>
                    <a href="#cloudflare">Cloudflare</a> |
                    <a href="#monitoring">Monitoring</a> |
                    <a href="#miscellaneous">Miscellaneous</a> |
                    <a href="#debug">Debug</a> |
                    <a href="#settings">Import / Export Settings</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_pgcache':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#general">General</a> |
                    <a href="#advanced">Advanced</a> |
                    <a href="#cache_preload">Cache Preload</a> |
                    <a href="#purge_policy">Purge Policy</a> |
                    <a href="#notes">Note(s)</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_minify':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#general">General</a> |
                    <a href="#html_xml"><acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym></a> |
                    <a href="#js"><acronym title="JavaScript">JS</acronym></a> |
                    <a href="#css"><acronym title="Cascading Style Sheet">CSS</acronym></a> |
                    <a href="#advanced">Advanced</a> |
                    <a href="#notes">Note(s)</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_dbcache':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#general">General</a> |
                    <a href="#advanced">Advanced</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_objectcache':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#advanced">Advanced</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_browsercache':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#general">General</a> |
                    <a href="#css_js"><acronym title="Cascading Style Sheet">CSS</acronym> &amp; <acronym title="JavaScript">JS</acronym></a> |
                    <a href="#html_xml"><acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym></a> |
                    <a href="#media">Media</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_mobile':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#manage">Manage User Agent Groups</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_referrer':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#manage">Manage Referrer Groups</a>
        <?php
                    break;
        ?>
        <?php
                case 'w3tc_cdn':
        ?>
                    Jump to: 
                    <a href="#toplevel_page_w3tc_general">Main Menu</a> |
                    <a href="#general">General</a> |
                    <a href="#configuration">Configuration</a> |
                    <a href="#advanced">Advanced</a> |
                    <a href="#notes">Note(s)</a>
        <?php
                    break;
        ?>

        <?php
            }            
        ?>
    </p>
<?php endif ?>