				<h2><?php _e('Troubleshooting Settings', 'mfbfw'); ?></h2>

				<p><span style="font-weight:bold;color:red;"><?php _e('Settings in this section should only be changed if you are having problems with the plugin!', 'mfbfw'); ?></span></p>

				<p><?php _e('If the plugin doesn\'t seem to work, first you should check for other plugins that may be conflicting with this one, especially other Lightbox, Slimbox, etc. Make sure all your plugins and WordPress itself are up to date (this plugin has only been tested in WordPress 2.7 and above).', 'mfbfw'); ?></p>

				<p><?php _e('Change them one at a time and test to see if they help. Remember that having a cache plugin may prevent changes from taking effect immidiately, so clear cache after saving changes here or deactivate cache until you finish editing these options.', 'mfbfw'); ?></p><br />

				<table class="form-table" style="clear:none;">
					<tbody>

						<tr valign="top">
							<th scope="row"><?php _e('Do not call jQuery', 'mfbfw'); ?></th>
							<td>
								<fieldset>

									<label for="nojQuery">
										<input type="checkbox" name="mfbfw[nojQuery]" id="nojQuery"<?php if ( isset($settings['nojQuery']) && $settings['nojQuery'] ) echo ' checked="yes"';?> />
										<?php _e('Skip jQuery call. Use this only if jQuery is being loaded twice (default: off)', 'mfbfw'); ?>
									</label><br />

								</fieldset>
							</td>
						</tr>

					</tbody>
				</table>