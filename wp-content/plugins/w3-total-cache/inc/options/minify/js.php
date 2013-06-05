<?php if (!defined('W3TC')) die(); ?>
<?php $this->checkbox('minify.js.strip.comments', false, 'js_') ?> <?php _e('Preserved comment removal (not applied when combine only is active)', 'w3-total-cache'); ?></label><br />
<?php $this->checkbox('minify.js.strip.crlf', false, 'js_') ?> <?php _e('Line break removal (not safe, not applied when combine only is active)', 'w3-total-cache'); ?></label><br />
