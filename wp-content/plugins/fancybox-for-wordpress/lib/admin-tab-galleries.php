				<h2><?php _e('Gallery Settings <span style="color:red">(advanced)</span>', 'mfbfw'); ?></h2>

				<p><?php _e('Here you can choose if you want the plugin to group all images into a gallery, or make a gallery for each post. You can also define you own jQuery expression if you like.', 'mfbfw'); ?></p>

				<table class="form-table" style="clear:none;">
					<tbody>

						<tr valign="top">
							<th scope="row"><?php _e('Gallery Type', 'mfbfw'); ?></th>
							<td>
								<fieldset>

									<input id="galleryTypeAll" class="galleryType" type="radio" value="all" name="mfbfw[galleryType]"<?php if ($settings['galleryType'] == 'all') echo ' checked="yes"';?> />
									<label for="galleryTypeAll">
										<?php _e('Make a gallery for all images on the page (default)', 'mfbfw'); ?>
									</label><br />

									<input id="galleryTypeNone" class="galleryType" type="radio" value="none" name="mfbfw[galleryType]"<?php if ($settings['galleryType'] == 'none') echo ' checked="yes"';?> />
									<label for="galleryTypeNone">
										<?php _e('Do not group images in gallery automatically (use this if you want to make galleries manually with the <code>REL</code> attribute)', 'mfbfw'); ?>
									</label><br />

									<input id="galleryTypePost" class="galleryType" type="radio" value="post" name="mfbfw[galleryType]"<?php if ($settings['galleryType'] == 'post') echo ' checked="yes"';?> />
									<label for="galleryTypePost">
										<?php _e('Make a gallery for each post (will only work if your theme uses <code>class="post"</code> on each post, which is common in WordPress', 'mfbfw'); ?>
									</label><br />

									<input id="galleryTypeCustom" class="galleryType" type="radio" value="custom" name="mfbfw[galleryType]"<?php if ($settings['galleryType'] == 'custom') echo ' checked="yes"';?> />
									<label for="galleryTypeCustom">
										<?php _e('Use a custom expression to apply FancyBox', 'mfbfw'); ?>
									</label><br /><br />

									<div id="customExpressionBlock">

									<label for="mfbfw[customExpression]">
										<textarea rows="10" cols="50" class="large-text code" name="mfbfw[customExpression]" wrap="physical" id="customExpression"><?php echo ($settings['customExpression']); ?></textarea>
									</label><br />

									<small><strong><em><?php _e('Custom expression guidelines:', 'mfbfw'); ?></em></strong></small><br />

									<small><em><?php _e('&middot; The custom expression has to apply <code>class="fancybox"</code> to the links where you want to use FancyBox. Do not call the <code>fancybox()</code> function here, the plugin does this for you.', 'mfbfw'); ?></em></small><br />

									<small><em><?php _e('&middot; The jQuery <code>addClass()</code> function is a good way to add the class to the desired links conserving any existing class.', 'mfbfw'); ?></em></small><br />

									<small><em><?php _e('&middot; You can use <code>getTitle()</code> in your expression to copy the title attribute from the <code>IMG</code> tag to the <code>A</code> tag, so that FancyBox can show captions.', 'mfbfw'); ?></em></small><br />

									<small><em><?php _e('&middot; You can use <code>jQuery(thumbnails)</code> like in the example expression to apply FancyBox to thumbnails that link to these extensions: BMP, GIF, JPG, JPEG, PNG (both lowercase and uppercase).', 'mfbfw'); ?></em></small><br />

									<small><em><?php _e('&middot; If you want to do it manually you can use something like <code>jQuery("a:has(img)[href$=\'.jpg\']")</code> or whatever works for you.', 'mfbfw'); ?></em></small><br />

									<small><em><?php _e('See the <a href="http://docs.jquery.com/" target="_blank">jQuery Documentation</a> for more help.', 'mfbfw'); ?></em></small><br /><br />

									<small><strong><em><?php _e('Examples:', 'mfbfw'); ?></em></strong></small><br />

									<small><em><code>jQuery(thumbnails).addClass(&quot;fancybox&quot;).attr(&quot;rel&quot;,&quot;fancybox&quot;).getTitle();</code></em></small><br />

									<small><em><code>jQuery&quot;a:has(img)[href$='.jpg']&quot;).addClass&quot;fancybox&quot;).attr(&quot;rel&quot;,&quot;fancybox&quot;).getTitle();</code></em></small><br /><br />

									</div>

								</fieldset>
							</td>
						</tr>

					</tbody>
				</table>