<?php
    global $yarpp;
    $switch = (isset($_GET['go']) && $_GET['go'] === 'basic') ? $switch = true : null;

    if (isset($_GET['aid']) && isset($_GET['v']) && isset($_GET['st'])) {

        $yarpp->yarppPro['aid'] = (trim($_GET['aid']) !== '') ? $_GET['aid'] : null;
        $yarpp->yarppPro['st']  = (trim($_GET['st'])  !== '') ? rawurlencode($_GET['st']) : null;
        $yarpp->yarppPro['v']   = (trim($_GET['v'])   !== '') ? rawurlencode($_GET['v'])  : null;

        update_option('yarpp_pro', $yarpp->yarppPro);

    }

    $src = urlencode(admin_url().'options-general.php?page='.$_GET['page']);
    $aid = (isset($yarpp->yarppPro['aid']) && $yarpp->yarppPro['aid']) ? $yarpp->yarppPro['aid'] : 0;
    $st  = (isset($yarpp->yarppPro['st'])  && $yarpp->yarppPro['st'])  ? $yarpp->yarppPro['st']  : 0;
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
                <a href="<?php echo plugins_url('includes/',dirname(__FILE__)).'yarpp_switch.php' ?>" id="yarpp_switch_button" data-go="basic" class="button">
                    I only want access to <em>YARPP Basic</em> features
                </a>
                &nbsp;&nbsp
                <a href="options-general.php?page=yarpp" id="yarpp_switch_cancel"  class="button">
                    No, thanks. Keep <em>YARPP Pro</em> features enabled
                </a>
            </p>

        <?php else: ?>

            <p>
                The settings below allow you to configure the additional features of <em>YARPP Pro</em>. Make money by displaying
                sponsored ads, easily customize thumbnail display, pull related content from multiple domains, and get
                detailed reporting.
            </p>

        <?php endif ?>
        </div>

</div>
    <?php if (!$switch): ?>
    <iframe id="yarpp_pro_dashboard" src="<?php echo $url ?>" frameborder="0" border="0" cellspacing="0" scrolling="no">'.
    </iframe>
    <?php endif ?>
</div>