<?php if (!defined('W3TC')) die(); ?>
<?php
/**
 * @var bool $authorized
 * @var bool $have_zone
 */
?>
<div id="netdna-widget" class="sign-up maxcdn-netdna-widget-base">
    <h4><?php _e('Current customers', 'w3-total-cache')?></h4>
    <p><?php _e("Once you've signed up or if you're an existing NetDNA customer, to enable CDN:", 'w3-total-cache')?></p>
    <?php  if($authorized && (!$have_zone || is_null($zone_info))): ?>
        <button id="netdna-maxcdn-create-pull-zone" class="button-primary {type: 'netdna', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}"><?php _e('Create Pull Zone', 'w3-total-cache')?></button>
    <?php  elseif(!$authorized): ?>
        <a class="button-primary" href="https://cp.netdna.com/i/w3tc" target="_blank"><?php _e('Authorize', 'w3-total-cache')?></a>
        <form action="admin.php?page=w3tc_dashboard" method="post">
            <p>
                <label for="cdn_netdna_authorization_key"><?php _e('Authorization key', 'w3-total-cache')?>:</label>
                <input name="netdna" value="1" type="hidden" />
                <input id="cdn_netdna_authorization_key" class="w3tc-ignore-change" type="text" <?php echo $is_sealed? 'disabled="disabled"':'' ?> name="cdn.netdna.authorization_key" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.netdna.authorization_key')); ?>" size="31" />
                <br />
                <input type="submit" name="w3tc_save_options" class="button-secondary" value="<?php _e('Save key')?>" />
            </p>
        </form>
    <?php endif ?>
</div>