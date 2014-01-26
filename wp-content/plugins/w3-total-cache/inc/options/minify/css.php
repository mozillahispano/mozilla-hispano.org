<?php if (!defined('W3TC')) die(); ?>
<?php $this->checkbox('minify.css.strip.comments', false, 'css_') ?> <?php _e('Preserved comment removal (not applied when combine only is active)', 'w3-total-cache'); ?></label><br />
<?php $this->checkbox('minify.css.strip.crlf', false, 'css_') ?> <?php _e('Line break removal (not applied when combine only is active)', 'w3-total-cache'); ?></label><br />
