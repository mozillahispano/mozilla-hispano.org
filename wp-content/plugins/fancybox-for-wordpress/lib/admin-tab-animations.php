				<h2><?php _e('Animation Settings <span style="color:green">(basic)</span>', 'mfbfw'); ?></h2>

				<p><?php _e('These settings control the animations when opening and closing Fancybox, and the optional easing effects.', 'mfbfw'); ?></p>

			<table class="form-table" style="clear:none;">
					<tbody>

						<tr valign="top">
							<th scope="row"><?php _e('Zoom Options', 'mfbfw'); ?></th>
							<td>
								<fieldset>

									<label for="zoomOpacity">
										<input type="checkbox" name="mfbfw[zoomOpacity]" id="zoomOpacity"<?php if ( isset($settings['zoomOpacity']) && $settings['zoomOpacity'] ) echo ' checked="yes"';?> />
										<?php _e('Change content transparency during zoom animations (default: on)', 'mfbfw'); ?>
									</label><br /><br />

									<label for="zoomSpeedIn">
										<select name="mfbfw[zoomSpeedIn]" id="zoomSpeedIn">
											<?php
											foreach($msArray as $key=> $ms) {
												if($settings['zoomSpeedIn'] != $ms) $selected = '';
												else $selected = ' selected';
												echo "<option value='$ms'$selected>$ms</option>\n";
											} ?>
										</select>
										<?php _e('Speed in miliseconds of the zooming-in animation (default: 500)', 'mfbfw'); ?>
									</label><br /><br />

									<label for="zoomSpeedOut">
										<select name="mfbfw[zoomSpeedOut]" id="zoomSpeedOut">
											<?php
											foreach($msArray as $key=> $ms) {
												if($settings['zoomSpeedOut'] != $ms) $selected = '';
												else $selected = ' selected';
												echo "<option value='$ms'$selected>$ms</option>\n";
											} ?>
										</select>
										<?php _e('Speed in miliseconds of the zooming-out animation (default: 500)', 'mfbfw'); ?>
									</label><br /><br />

									<label for="zoomSpeedChange">
										<select name="mfbfw[zoomSpeedChange]" id="zoomSpeedChange">
											<?php
											foreach($msArray as $key=> $ms) {
												if($settings['zoomSpeedChange'] != $ms) $selected = '';
												else $selected = ' selected';
												echo "<option value='$ms'$selected>$ms</option>\n";
											} ?>
										</select>
										<?php _e('Speed in miliseconds of the animation when navigating thorugh gallery items (default: 300)', 'mfbfw'); ?>
									</label><br /><br />

								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e('Transition Type', 'mfbfw'); ?></th>
							<td>
								<fieldset>

									<label for="transitionIn">
										<select name="mfbfw[transitionIn]" id="transitionIn">
											<?php
											foreach($transitionTypeArray as $key=> $transitionIn) {
												if($settings['transitionIn'] != $transitionIn) $selected = '';
												else $selected = ' selected';
												echo "<option value='$transitionIn'$selected>$transitionIn</option>\n";
											}
											?>
										</select>
										<?php _e('Transition type when opening FancyBox. (default: fade)', 'mfbfw'); ?>
									</label><br /><br />

									<label for="transitionOut">
										<select name="mfbfw[transitionOut]" id="transitionOut">
											<?php
											foreach($transitionTypeArray as $key=> $transitionOut) {
												if($settings['transitionOut'] != $transitionOut) $selected = '';
												else $selected = ' selected';
												echo "<option value='$transitionOut'$selected>$transitionOut</option>\n";
											}
											?>
										</select>
										<?php _e('Transition type when closing FancyBox. (default: fade)', 'mfbfw'); ?>
									</label><br /><br />

								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e('Easing', 'mfbfw'); ?></th>
							<td>
								<fieldset>

									<label for="easing">
										<input type="checkbox" name="mfbfw[easing]" id="easing"<?php if ( isset($settings['easing']) && $settings['easing'] ) echo ' checked="yes"';?> />
										<?php _e('Activate easing (default: off)', 'mfbfw'); ?>
									</label><br />

									<small><em><?php _e('(Will load one additional javascript file, 8KB)', 'mfbfw'); ?></em></small><br /><br />

									<div id="easingBlock">

										<label for="easingIn">
											<select name="mfbfw[easingIn]" id="easingIn">
												<?php
												foreach($easingArray as $key=> $easingIn) {
													if( $settings['easingIn'] != $easingIn ) $selected = '';
													else $selected = ' selected';
													echo "<option value='$easingIn'$selected>$easingIn</option>\n";
												}
												?>
											</select>
											<?php _e('Easing method when opening FancyBox. (default: easeOutBack)', 'mfbfw'); ?>
										</label><br />

										<small><em><?php _e('(Requires opening transition type to be set to elastic)', 'mfbfw'); ?></em></small><br /><br />

										<label for="easingOut">
											<select name="mfbfw[easingOut]" id="easingOut">
												<?php
												foreach($easingArray as $key=> $easingOut) {
													if( $settings['easingOut'] != $easingOut ) $selected = '';
													else $selected = ' selected';
													echo "<option value='$easingOut'$selected>$easingOut</option>\n";
												}
												?>
											</select>
											<?php _e('Easing method when closing FancyBox. (default: easeInBack)', 'mfbfw'); ?>
										</label><br />

										<small><em><?php _e('(Requires closing transition type to be set to elastic)', 'mfbfw'); ?></em></small><br /><br />

										<label for="easingChange">
											<select name="mfbfw[easingChange]" id="easingChange">
												<?php
												foreach($easingArray as $key=> $easingChange) {
													if( isset($settings['easingChange']) && $settings['easingChange'] != $easingChange ) $selected = '';
													else $selected = ' selected';
													echo "<option value='$easingChange'$selected>$easingChange</option>\n";
												}
												?>
											</select>
											<?php _e('Easing method when navigating through gallery items. (default: easeInOutQuart)', 'mfbfw'); ?>
										</label><br />

										<small><em><?php _e('(There are 30 different easing methods, the first ones are the most boring. You can test them <a href="http://commadot.com/jquery/easing.php" target="_blank">here</a> or <a href="http://hosted.zeh.com.br/mctween/animationtypes.html" target="_blank">here</a>)', 'mfbfw'); ?></em></small><br /><br />

									</div>

								</fieldset>
							</td>
						</tr>

					</tbody>
				</table>