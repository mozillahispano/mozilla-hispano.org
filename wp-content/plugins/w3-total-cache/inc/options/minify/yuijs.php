<?php if (!defined('W3TC')) die(); ?>
<?php $this->checkbox('minify.yuijs.options.nomunge', false, 'js_') ?> <?php _e('Minify only, do not obfuscate local symbols', 'w3-total-cache'); ?></label><br />
<?php $this->checkbox('minify.yuijs.options.preserve-semi', false, 'js_') ?> <?php _e('Preserve unnecessary semicolons', 'w3-total-cache'); ?></label><br />
<?php $this->checkbox('minify.yuijs.options.disable-optimizations', false, 'js_') ?> <?php _e('Disable all the built-in micro optimizations', 'w3-total-cache'); ?></label><br />
