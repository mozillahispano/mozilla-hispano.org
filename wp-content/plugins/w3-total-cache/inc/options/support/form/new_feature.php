<?php if (!defined('W3TC')) die(); ?>
<?php echo $this->postbox_header('Required Information', 'required'); ?>
<table class="form-table">
    <tr>
        <th>Request type:</th>
        <td><?php echo htmlspecialchars($this->_request_types[$request_type]); ?></td>
    </tr>
    <tr>
        <th><label for="support_url">Blog <acronym title="Uniform Resource Locator">URL</acronym>:</label></th>
        <td><input id="support_url" type="text" name="url" value="<?php echo htmlspecialchars($url); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_name">Name:</label></th>
        <td><input id="support_name" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_email">E-Mail:</label></th>
        <td><input id="support_email" type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_twitter">Twitter ID:</label></th>
        <td><input id="support_twitter" type="text" name="twitter" value="<?php echo htmlspecialchars($twitter); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_subject">Subject:</label></th>
        <td><input id="support_subject" type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_description">Issue description:</label></th>
        <td><textarea id="support_description" name="description" cols="70" rows="8"><?php echo htmlspecialchars($description); ?></textarea></td>
    </tr>
</table>
<?php echo $this->postbox_footer(); ?>

<?php echo $this->postbox_header('Additional Information'); ?>
<table class="form-table">
    <tr>
        <th><label for="support_phone">Phone:</label></th>
        <td><input id="support_phone" type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" size="80" /></td>
    </tr>
    <tr>
        <th><label for="support_forum_url">Forum Topic URL:</label></th>
        <td><input id="support_forum_url" type="text" name="forum_url" value="<?php echo htmlspecialchars($forum_url); ?>" size="80" /></td>
    </tr>
    <tr>
        <th colspan="2">
            <label for="support_subscribe_releases">Would you like to be notified when products are announced and updated?</label>
        </th>
    </tr>
    <tr>
        <td colspan="2">
            <input id="support_subscribe_releases" name="subscribe_releases" type="checkbox" value="Yes" <?php checked($subscribe_releases, true) ?> /> Yes, please notify me.
        </td>
    </tr>
</table>
<?php echo $this->postbox_footer(); ?>