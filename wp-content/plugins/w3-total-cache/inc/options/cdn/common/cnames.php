<?php if (!defined('W3TC')) die(); ?>
<ol id="cdn_cnames">
<?php
if (! count($cnames)) {
    $cnames = array('');
}

$count = count($cnames);

foreach ($cnames as $index => $cname):
    $label = '';

    if ($count > 1):
    	switch ($index):
            case 0:
                $label = '(reserved for CSS)';
                break;

            case 1:
                $label = '(reserved for JS in <head>)';
                break;

            case 2:
                $label = '(reserved for JS after <body>)';
                break;

            case 3:
                $label = '(reserved for JS before </body>)';
                break;

            default:
                $label = '';
                break;
    	endswitch;
    endif;
?>
	<li>
		<input type="text" name="cdn_cnames[]"
                       <?php $this->sealing_disabled('cdn') ?> value="<?php echo htmlspecialchars($cname); ?>" size="60" />
		<input class="button cdn_cname_delete" type="button"
                       <?php $this->sealing_disabled('cdn') ?> value="Delete"<?php if (!$index): ?> style="display: none;"<?php endif; ?> />
		<span><?php echo htmlspecialchars($label); ?></span>
	</li>
<?php endforeach; ?>
</ol>
<input id="cdn_cname_add" class="button" type="button" value="Add CNAME" 
    <?php $this->sealing_disabled('cdn') ?> />
