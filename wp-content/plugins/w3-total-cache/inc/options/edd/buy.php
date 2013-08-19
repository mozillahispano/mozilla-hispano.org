<div>
    To enable more features you should <input type="button" class="button-primary button-buy-plugin {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="<?php _e('Upgrade', 'w3-total-cache') ?>" />
    <div id="w3tc-license-instruction" style="display: none;">
    <span class="description"><?php printf(__('Please enter the license key you receive after purchase %s.', 'w3-total-cache'),
            '<a href="' . (is_network_admin() ?
                                network_admin_url('admin.php?page=w3tc_general#licensing') :
                                admin_url('admin.php?page=w3tc_general#licensing')) .'">' . __('here', 'w3-total-cache') . '</a>')
        ?></span>
    </div>
</div>