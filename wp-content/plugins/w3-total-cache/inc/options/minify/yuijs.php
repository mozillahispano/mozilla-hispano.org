<?php if (!defined('W3TC')) die(); ?>
<?php $this->checkbox('minify.yuijs.options.nomunge', false, 'js_') ?> Minify only, do not obfuscate local symbols</label><br />
<?php $this->checkbox('minify.yuijs.options.preserve-semi', false, 'js_') ?> Preserve unnecessary semicolons</label><br />
<?php $this->checkbox('minify.yuijs.options.disable-optimizations', false, 'js_') ?> Disable all the built-in micro optimizations</label><br />
