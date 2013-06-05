<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label for="cdn_akamai_username">Username:</label></th>
    <td>
        <input id="cdn_akamai_username" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.akamai.username" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.akamai.username')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_password">Password:</label></th>
    <td>
        <input id="cdn_akamai_password" class="w3tc-ignore-change"
           <?php $this->sealing_disabled('cdn') ?> type="password" name="cdn.akamai.password" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.akamai.password')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_email_notification">Email notification:</label></th>
    <td>
        <textarea id="cdn_akamai_email_notification" name="cdn.akamai.email_notification"
            <?php $this->sealing_disabled('cdn') ?> cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.akamai.email_notification'))); ?></textarea>
        <br />
        <span class="description">Specify email addresses for completed removal notifications. One email per line.</span>
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_zone">Domain to purge:</label></th>
    <td>
        <select  id="cdn_akamai_zone" name="cdn.akamai.zone">
            <option value="production" <?php selected($this->_config->get_string('cdn.akamai.zone'), 'production'); ?>>Production</option>
            <option value="staging" <?php selected($this->_config->get_string('cdn.akamai.zone'), 'staging'); ?>>Staging</option>
        </select>
    </td>
</tr>
<tr>
    <th><label for="cdn_akamai_action">Purge action:</label></th>
    <td>
        <select  id="cdn_akamai_action" name="cdn.akamai.action">
            <option value="invalidate" <?php selected($this->_config->get_string('cdn.akamai.action'), 'invalidate'); ?>>Invalidate</option>
            <option value="remove" <?php selected($this->_config->get_string('cdn.akamai.action'), 'remove'); ?>>Remove</option>
        </select>
    </td>
</tr>
<tr>
	<th><label for="cdn_akamai_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_akamai_ssl" name="cdn.akamai.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.akamai.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.akamai.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.akamai.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
    <th>Replace site's hostname with:</th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.akamai.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description">Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'akamai', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test akamai" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
