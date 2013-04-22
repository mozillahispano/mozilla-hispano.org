<?php

function mfbfw_options_page() {

	require_once( FBFW_PATH . 'lib/admin-head.php' );

	?>

	<div class="wrap">

	<div id="icon-plugins" class="icon32"></div><h2><?php printf( __('Fancybox for WordPress (version %s)', 'mfbfw'), $version ); ?></h2>

	<br />

	<form method="post" action="options.php" id="options">

		<?php settings_fields( 'mfbfw-options' ); ?>

		<div id="fbfwTabs">

			<ul>
				<li><a href="#fbfw-info"><?php _e( 'Info', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-appearance"><?php _e( 'Appearance', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-animations"><?php _e( 'Animations', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-behaviour"><?php _e( 'Behaviour', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-galleries"><?php _e( 'Galleries', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-other"><?php _e( 'Miscellaneous', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-calls"><?php _e( 'Extra Calls', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-troubleshooting"><?php _e( 'Troubleshooting', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-support" style="color:green;"><?php _e( 'Support', 'mfbfw' ); ?></a></li>
				<li><a href="#fbfw-uninstall" style="color:red;"><?php _e ('Uninstall', 'mfbfw' ); ?></a></li>
			</ul>

			<div id="fbfw-info">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-info.php' ); ?>
			</div>

			<div id="fbfw-appearance">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-appearance.php' ); ?>
			</div>

			<div id="fbfw-animations">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-animations.php' ); ?>
			</div>

			<div id="fbfw-behaviour">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-behaviour.php' ); ?>
			</div>

			<div id="fbfw-galleries">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-galleries.php' ); ?>
			</div>

			<div id="fbfw-other">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-other.php' ); ?>
			</div>

			<div id="fbfw-calls">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-calls.php' ); ?>
			</div>

			<div id="fbfw-troubleshooting">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-troubleshooting.php' ); ?>
			</div>

			<div id="fbfw-support">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-support.php' ); ?>
			</div>

			<div id="fbfw-uninstall">
				<?php require_once ( FBFW_PATH . 'lib/admin-tab-uninstall.php' ); ?>
			</div>

		</div>

		<p class="submit" style="text-align:center;">
			<input type="submit" name="mfbfw_update" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mfbfw' ); ?>" />
		</p>

	</form>

	<form method="post" action="">
		<div style="text-align:center;padding:0 0 1.5em;margin:-15px 0 5px;">
			<input type="submit" name="mfbfw_update" id="reset" onClick="return confirmDefaults();" class="button-secondary" value="<?php esc_attr_e( 'Revert to defaults', 'mfbfw' ); ?>" />
			<input type="hidden" name="action" value="reset" />
		</div>
	</form>

	<div id="mfbfwd" style="border-top:1px dashed #DDDDDD;margin:20px auto 40px;overflow:hidden;padding-top:25px;width:735px">

		<div style="background-color:#FFFFE0;border:1px solid #E6DB55;padding:0 .6em;margin:5px 15px 2px;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;height:90px;float:left;text-align:center;width:200px">
			<p style="line-height:1.5em;"><?php _e( 'If you use FancyBox and like it, buy the author a beer!', 'mfbfw' ); ?></p>
			<form id="donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input name="cmd" value="_donations" type="hidden">
				<input name="business" value="janis.skarnelis@gmail.com" type="hidden">
				<input name="item_name" value="FancyBox" type="hidden">
				<input name="amount" value="10.00" type="hidden">
				<input name="no_shipping" value="0" type="hidden">
				<input name="no_note" value="1" type="hidden">
				<input name="currency_code" value="EUR" type="hidden">
				<input name="tax" value="0" type="hidden">
				<input name="lc" value="LV" type="hidden">
				<input name="bn" value="PP-DonationsBF" type="hidden">
				<input type="image" style="margin:0;padding:0" border="0" src="<?php echo FBFW_URL ?>css/img/extra_donate.png" name="submit" alt="PayPal - The safer, easier way to pay online!"/>
			</form>
		</div>

		<div style="background-color:#FFFFE0;border:1px solid #E6DB55;padding:0 .6em;margin:5px 15px 2px;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;height:90px;float:left;margin-left:10px;text-align:center;width:200px">
			<p style="line-height:1.5em;"><?php _e( 'The author of this WordPress Plugin also likes beer :P', 'mfbfw' ); ?></p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick"/>
				<input type="hidden" name="hosted_button_id" value="3878319"/>
				<input type="image" style="margin:0;padding:0" border="0" src="<?php echo FBFW_URL ?>css/img/extra_donate.png" name="submit" alt="PayPal - The safer, easier way to pay online!"/>
				<img height="1" width="1" border="0" alt="" src="https://www.paypal.com/es_ES/i/scr/pixel.gif" />
			</form>
		</div>

		<div style="background-color:#9DD1F2;border:1px solid #419ED9;padding:0 .6em;margin:5px 15px 2px;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;height:90px;float:left;margin-left:10px;text-align:center;width:200px">
			<p style="line-height:1.5em;"><a href="http://twitter.com/moskis/"><?php _e( 'Follow me on Twitter for more WordPress Plugins and Themes', 'mfbfw' ); ?></a></p>
			<img height="16" width="16" border="0" alt="" src="<?php echo FBFW_URL ?>css/img/extra_twitter.png" />
		</div>

	</div>

</div>

<?php

}

?>
