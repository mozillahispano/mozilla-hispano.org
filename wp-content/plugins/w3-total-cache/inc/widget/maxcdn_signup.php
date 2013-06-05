<?php if (!defined('W3TC')) die(); ?>
<div id="maxcdn-widget" class="sign-up maxcdn-netdna-widget-base">
    <?php if ($show_new): ?>
    <h4><?php _e('New customers', 'w3-total-cache')?></h4>
    <p><?php _e('MaxCDN is a service that lets you speed up your site even more with W3 Total Cache.', 'w3-total-cache')?></p>
    <a class="button-primary" href="<?php echo MAXCDN_SIGNUP_URL?>" target="_blank"><?php _e('Sign up', 'w3-total-cache')?></a>
    <p><span class="desc"><?php _e('30 day money back guarantee', 'w3-total-cache')?></span></p>
        <h4><?php _e('Current customers', 'w3-total-cache')?></h4>
        <p><?php _e("Once you've signed up or if you're an existing MaxCDN customer, to enable CDN:", 'w3-total-cache')?></p>
        <a class="button-primary" href="https://cp.maxcdn.com/i/w3tc" target="_blank"><?php _e('Authorize', 'w3-total-cache')?></a>
    <form action="admin.php?page=w3tc_dashboard" method="post">
        <p>
            <label for="cdn_maxcdn_authorization_key"><?php _e('Authorization key', 'w3-total-cache')?>:</label>
            <input name="maxcdn" value="1" type="hidden" />
            <input id="cdn_maxcdn_authorization_key" class="w3tc-ignore-change" type="text" <?php echo $is_sealed? 'disabled="disabled"':'' ?> name="cdn.maxcdn.authorization_key" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.maxcdn.authorization_key')); ?>" size="31" />
            <br />
            <input type="submit" name="w3tc_save_options" class="button-secondary" value="<?php _e('Save key')?>" />
        </p>
    </form>
     <?php else: ?>
    <h4><?php _e('Current customers', 'w3-total-cache')?></h4>
    <?php  if($authorized && (!$have_zone || is_null($zone_info))): ?>
    <button id="netdna-maxcdn-create-pull-zone" class="button-primary {type: 'maxcdn', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}"><?php _e('Create Pull Zone', 'w3-total-cache')?></button>
    <?php endif ?>
    <?php endif ?>
</div>