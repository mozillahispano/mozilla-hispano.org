<?php

if (!defined('W3TC')) {
    die();
}

$css_levels = array(
    'CSS2.1',
    'CSS2.0',
    'CSS1.0'
);

$css_level = $this->_config->get_string('minify.csstidy.options.css_level');
?>
<?php $this->checkbox('minify.csstidy.options.remove_bslash', false, 'css_') ?> Remove unnecessary backslashes</label><br />
<?php $this->checkbox('minify.csstidy.options.compress_colors', false, 'css_') ?> Compress colors</label><br />
<?php $this->checkbox('minify.csstidy.options.compress_font-weight', false, 'css_') ?> Compress font-weight</label><br />
<?php $this->checkbox('minify.csstidy.options.lowercase_s', false, 'css_') ?> Lowercase selectors</label><br />
<?php $this->checkbox('minify.csstidy.options.remove_last_;', false, 'css_') ?> Remove last ;</label><br />
<?php $this->checkbox('minify.csstidy.options.sort_properties', false, 'css_') ?> Sort Properties</label><br />
<?php $this->checkbox('minify.csstidy.options.sort_selectors', false, 'css_') ?> Sort Selectors (caution)</label><br />
<?php $this->checkbox('minify.csstidy.options.discard_invalid_properties', false, 'css_') ?> Discard invalid properties</label>
<select class="css_enabled" name="minify.csstidy.options.css_level" 
    <?php $this->sealing_disabled('minify') ?>>
    <?php foreach($css_levels as $_css_level): ?>
        <option value="<?php echo $_css_level; ?>"<?php selected($css_level, $_css_level); ?>><?php echo $_css_level; ?></option>
    <?php endforeach; ?>
</select><br />
<?php $this->checkbox('minify.csstidy.options.preserve_css', false, 'css_') ?> Preserve CSS</label><br />
<?php $this->checkbox('minify.csstidy.options.timestamp', false, 'css_') ?> Add timestamp</label><br />
