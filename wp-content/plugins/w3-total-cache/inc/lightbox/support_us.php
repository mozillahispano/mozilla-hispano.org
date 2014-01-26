<?php if (!defined('W3TC')) die(); ?>
<h3><?php _e('Support Us, It\'s Free!', 'w3-total-cache'); ?></h3>

<p><?php _e('We noticed you\'ve been using W3 Total cache for at least 30 days, please help us improe WordPress:', 'w3-total-cache'); ?></p>

<form action="admin.php?page=<?php echo $this->_page; ?>&amp;w3tc_config_save_support_us" method="post">
    <p>
    	<label>
        	<?php _e('Link to us:', 'w3-total-cache'); ?>
        	<select name="support" class="select-support-type">
        		<option value=""><?php esc_attr_e('select location', 'w3-total-cache'); ?></option>
                <?php foreach ($supports as $support_id => $support_name): ?>
            	<option value="<?php echo esc_attr($support_id); ?>"<?php echo selected($this->_config->get_string('common.support'), $support_id); ?>><?php echo htmlspecialchars($support_name); ?></option>
            	<?php endforeach; ?>
        	</select>
        </label>, <input type="button" class="button button-tweet" value="<?php echo sprintf(__('tell your friends%s with a tweet', 'w3-total-cache'), '" />'); ?>
        (<input type="hidden" name="tweeted" value="0" /><label><input type="checkbox" name="tweeted" value="1"<?php checked($this->_config->get_boolean('common.tweeted', true)); ?> /> <?php _e('I\'ve tweeted', 'w3-total-cache'); ?></label>)
        <?php echo sprintf(__('and login to wordpress.org to give us a great %srating%s', 'w3-total-cache'),'<input type="button" class="button button-rating" value="', '" />.') ?>
    </p>

    <div style="text-align: center;">
        <h3><?php _e('Thanks in advance!', 'w3-total-cache'); ?></h3>
        <p>
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" class="button-primary" value="<?php _e('Save &amp; close', 'w3-total-cache'); ?>"> or
        	<?php echo w3_button_hide_note(__('Don\'t show this prompt again', 'w3-total-cache'), 'support_us'); ?>
        </p>
    </div>
</form>