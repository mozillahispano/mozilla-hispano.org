<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label>Specify account credentials:</label></th>
    <td>
        <a id="cdn_netdna_oauth" class="button" href="https://cp.netdna.com/account/api/create" target="_blank">I have NetDNA</a>
        <a id="cdn_maxcdn_oauth" class="button" href="https://cp.maxcdn.com/account/api/create" target="_blank">I have MaxCDN</a>
        <br />
        <span class="description">Click the button above for your provider, then log in, create an <acronym title="Application Programming Interface">API</acronym> key, and enter the details below.</span>
    </td>
</tr>
<tr>
    <th style="width: 300px;"><label for="cdn_netdna_alias">Alias:</label></th>
    <td>
        <input id="cdn_netdna_alias" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.netdna.alias" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.netdna.alias')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th style="width: 300px;"><label for="cdn_netdna_consumerkey">Consumer Key:</label></th>
    <td>
        <input id="cdn_netdna_consumerkey" class="w3tc-ignore-change" type="text"
           <?php $this->sealing_disabled('cdn') ?> name="cdn.netdna.consumerkey" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.netdna.consumerkey')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_netdna_consumersecret">Consumer Secret:</label></th>
    <td>
        <input id="cdn_netdna_consumersecret" class="w3tc-ignore-change"
           <?php $this->sealing_disabled('cdn') ?> type="password" name="cdn.netdna.consumersecret" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.netdna.consumersecret')); ?>" size="60" />
    </td>
</tr>
<tr>
	<th><label for="cdn_netdna_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_netdna_ssl" name="cdn.netdna.ssl" <?php $this->sealing_disabled('cdn') ?>>
			<option value="auto"<?php selected($this->_config->get_string('cdn.netdna.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.netdna.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.netdna.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
    <th>Replace site's hostname with:</th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.netdna.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description">Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'netdna', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test NetDNA / MaxCDN" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
