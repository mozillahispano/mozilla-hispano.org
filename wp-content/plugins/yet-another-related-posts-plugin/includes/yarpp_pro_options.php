<?php
    global $yarpp;
    $switch = (isset($_GET['go']) && $_GET['go'] === 'basic') ? $switch = true : null;

    if (isset($_GET['aid']) && isset($_GET['v']) && isset($_GET['st'])) {
        $yarpp->yarppPro['aid'] = (trim($_GET['aid']) !== '') ? $_GET['aid'] : null;
        $yarpp->yarppPro['st']  = (trim($_GET['st'])  !== '') ? rawurlencode($_GET['st']) : null;
        $yarpp->yarppPro['v']   = (trim($_GET['v'])   !== '') ? rawurlencode($_GET['v'])  : null;

        update_option('yarpp_pro', $yarpp->yarppPro);
    }

    if (isset($_POST['aid']) && isset($_POST['v'])) {
        $yarpp->yarppPro['aid'] = (trim($_POST['aid']) !== '') ? $_POST['aid'] : null;
        $yarpp->yarppPro['v']   = (trim($_POST['v'])   !== '') ? $_POST['v']   : null;

        update_option('yarpp_pro', $yarpp->yarppPro);
    }

    $src = urlencode(admin_url().'options-general.php?page='.$_GET['page']);
    $aid = (isset($yarpp->yarppPro['aid']) && $yarpp->yarppPro['aid']) ? $yarpp->yarppPro['aid'] : 0;
    $st  = (isset($yarpp->yarppPro['st'])  && $yarpp->yarppPro['st'])  ? $yarpp->yarppPro['st']  : 0;
    $v   = (isset($yarpp->yarppPro['v'])  && $yarpp->yarppPro['v'])    ? $yarpp->yarppPro['v']   : 0;
    $d   = urlencode(get_home_url());
    $url = 'https://yarpp.adkengage.com/AdcenterUI/PublisherUI/PublisherDashboard.aspx?src='.$src.'&d='.$d.'&aid='.$aid.'&st='.$st.'&plugin=1';
?>

<script>
    /*
     * Hide Screen Options using jQuery because add_filter('screen_options_show_screen', '__return_false')
     * removes it system wide.
     */
    jQuery("#screen-options-link-wrap").hide();
</script>

<div class="wrap">
    <h2>
        <?php _e('Yet Another Related Posts Plugin Options','yarpp');?>
        <small>
            <?php echo apply_filters('yarpp_version_html', esc_html(get_option('yarpp_version'))) ?>
        </small>
    </h2>
    <div id="yarpp_switch_container">
        <ul id="yarpp_switch_tabs">
            <li class="<?php echo (($switch) ? null : 'disabled')?>">
                <a href="options-general.php?page=yarpp&go=basic">YARPP Basic</a>
            </li>
            <li class="<?php echo (($switch) ? 'disabled': null)?>">
                <a href="options-general.php?page=yarpp">YARPP Pro</a>
            </li>
        </ul>
        <div class="yarpp_switch_content">
        <?php if ($switch): ?>
            <p>
                You currently have <em>YARPP Pro</em> enabled, giving you access to even more powerful features.
            </p>
            <p>
                If you are no longer interested in these enhancements and wish to keep only YARPP Basic features, click
                the button below.  Please note that by switching to YARPP Basic you will no longer be able to make money
                by displaying sponsored ads, nor will you have access to detailed reporting or pull related content from
                multiple domains.
            </p>
            <p>
                <a href="<?php echo plugins_url('includes/',dirname(__FILE__)).'yarpp_switch.php' ?>" data-go="basic" class="button yarpp_switch_button">
                    I only want access to <em>YARPP Basic</em> features
                </a>
                &nbsp;&nbsp
                <a href="options-general.php?page=yarpp" id="yarpp_switch_cancel"  class="button">
                    No, thanks. Keep <em>YARPP Pro</em> features enabled
                </a>
            </p>
        <?php else: ?>
            <p>
                <strong>Access more powerful features with YARPP Pro!</strong>
                <br/>
                <ul>
                    <li>Earn money from sponsored content</li>
                    <li>Pull related content from multiples sites</li>
                </ul>
                <ul>
                    <li>Easily customize thumbnail layout</li>
                    <li>Get detailed traffic reports</li>
                </ul>
                <div class="clear"></div>
            </p>
        <?php endif ?>
        </div>
    </div>

    <?php if (!$switch): ?>
    <div id="yarpp_pro_dashboard_wrapper">
        <iframe
            id="yarpp_pro_dashboard"
            src="<?php echo $url ?>"
            frameborder="0"
            border="0"
            cellspacing="0"
            scrolling="yes"
            >
        </iframe>
    </div>

    <!-- MARK: API Setting override (uncomment ajax handler on options_switch.js lines 17-38)
    <div class="postbox">
        <h3 class="hndle">
            <span style="margin-left:0.8em">API Settings</span>
        </h3>
        <div class="inside">
            <div id="yarpp_pro_api_settings_note" class="yarpp_form_row">
                <p>
                Explanation of what, why and how to use this box!!!!
                </p>
                <br/>
                <a id="yarpp_pro_api_settings_unlock" class="button">Unlock API Settings</a>
            </div>
            <form id="yarpp_pro_api_settings" action="?page=yarpp" method="post">

                <div class="yarpp_form_row">
                    <label class="yarpp_pro_label">Affiliate ID: </label>
                    <input id="yarpp_pro_aid" type="text" name="aid" value="<?php echo ($aid) ? $aid : null ?>" disabled />
                    <span class="yarpp_warning"></span>
                </div>

                <div class="yarpp_form_row">
                    <label class="yarpp_pro_label">API Key: </label>
                    <input id="yarpp_pro_api_key" type="text" name="v" value="<?php echo ($v) ? $v : null ?>" disabled />
                    <span class="yarpp_warning"></span>
                </div>

                <div class="yarpp_form_row">
                    <input id="yarpp_pro_settings_submit" class="submit-btn" type="submit" value="Save API Settings" disabled />
                </div>
            </form>
        </div>
    </div>
    -->
    <?php endif ?>
</div>