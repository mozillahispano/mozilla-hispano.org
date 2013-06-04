<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label for="cdn_att_account">Account #:</label></th>
    <td>
        <input id="cdn_att_account" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.att.account" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.att.account')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_att_token">Token:</th>
    <td>
        <input id="cdn_att_token" class="w3tc-ignore-change" type="password"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.att.token" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.att.token')); ?>" size="60" />
    </td>
</tr>
<tr>
	<th><label for="cdn_att_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_att_ssl" name="cdn.att.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.att.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.att.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.att.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
    <th>Replace site's hostname with:</th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.att.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description">Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'att', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test AT&T" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
