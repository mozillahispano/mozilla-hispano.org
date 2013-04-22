<?php if (!defined('W3TC')) die(); ?>
<?php $this->checkbox('minify.js.strip.comments', false, 'js_') ?> Preserved comment removal (not applied when combine only is active)</label><br />
<?php $this->checkbox('minify.js.strip.crlf', false, 'js_') ?> Line break removal (not safe, not applied when combine only is active)</label><br />
