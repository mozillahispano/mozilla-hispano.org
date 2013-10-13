<?php

/**
 * Add Fancier Author Box display additional user fields
 *
 * @since 1.0
 */
add_action( 'edit_user_profile', 'ts_fab_extra_user_details' );
add_action( 'show_user_profile', 'ts_fab_extra_user_details' );
function ts_fab_extra_user_details( $user ) { ?>

	<h3>Fancier Author Box <?php _e( 'User Details', 'ts-fab' ); ?></h3>

	<table class="form-table">
		<?php
			$userid = $user->ID;
			$user_hide = get_user_meta( $userid, 'ts_fab_user_hide', false );
			( $user_hide == true ) ? $checked = 'checked="checked"' : $checked = '';
		?>
		<tr>
			<th><?php _e( 'Display Fancier Author Box', 'ts-fab' ); ?></th>
			<td>
				<label for="ts_fab_user_hide">
					<input type="checkbox" name="ts_fab_user_hide" id="ts_fab_user_hide" value="true" <?php echo $checked; ?> />
					<?php _e( 'Do not show Fancier Author Box in your posts, pages and custom posts', 'ts-fab' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th><label for="ts_fab_twitter">Twitter</label></th>

			<td>
				<input type="text" name="ts_fab_twitter" id="ts_fab_twitter" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_twitter', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Twitter username.', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Twitter -->
		<tr>
			<th><label for="ts_fab_facebook">Facebook</label></th>

			<td>
				<input type="text" name="ts_fab_facebook" id="ts_fab_facebook" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_facebook', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Facebook username or ID.', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Facebook -->
		<tr>
			<th><label for="ts_fab_googleplus">Google+</label></th>

			<td>
				<input type="text" name="ts_fab_googleplus" id="ts_fab_googleplus" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_googleplus', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Google+ ID.', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Google+ -->
		<tr>
			<th><label for="ts_fab_linkedin">LinkedIn</label></th>

			<td>
				<input type="text" name="ts_fab_linkedin" id="ts_fab_linkedin" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_linkedin', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your LinkedIn username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End LinkedIn -->
		<tr>
			<th><label for="ts_fab_instagram">Instagram</label></th>

			<td>
				<input type="text" name="ts_fab_instagram" id="ts_fab_instagram" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_instagram', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Instagram username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Instagram -->
		<tr>
			<th><label for="ts_fab_flickr">Flickr</label></th>

			<td>
				<input type="text" name="ts_fab_flickr" id="ts_fab_flickr" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_flickr', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Flickr username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Flickr -->
		<tr>
			<th><label for="ts_fab_pinterest">Pinterest</label></th>

			<td>
				<input type="text" name="ts_fab_pinterest" id="ts_fab_pinterest" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_pinterest', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Pinterest username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Pinterest -->
		<tr>
			<th><label for="ts_fab_tumblr">Tumblr</label></th>

			<td>
				<input type="text" name="ts_fab_tumblr" id="ts_fab_tumblr" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_tumblr', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Tumblr username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Tumblr -->
		<tr>
			<th><label for="ts_fab_youtube">YouTube</label></th>

			<td>
				<input type="text" name="ts_fab_youtube" id="ts_fab_youtube" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_youtube', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your YouTube username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End YouTube -->
		<tr>
			<th><label for="ts_fab_vimeo">Vimeo</label></th>

			<td>
				<input type="text" name="ts_fab_vimeo" id="ts_fab_vimeo" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_vimeo', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your Vimeo username', 'ts-fab' ); ?></span>
			</td>
		</tr><!-- End Vimeo -->

		<tr>
			<th><label for="ts_fab_position"><?php _e( 'Position', 'ts-fab' ); ?></label></th>

			<td>
				<input type="text" name="ts_fab_position" id="ts_fab_position" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_position', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your position.', 'ts-fab' ); ?></span>
			</td>
		</tr>

		<tr>
			<th><label for="ts_fab_company"><?php _e( 'Company', 'ts-fab' ); ?></label></th>

			<td>
				<input type="text" name="ts_fab_company" id="ts_fab_company" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_company', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your company.', 'ts-fab' ); ?></span>
			</td>
		</tr>

		<tr>
			<th><label for="ts_fab_company_url"><?php _e( 'Company URL', 'ts-fab' ); ?></label></th>

			<td>
				<input type="text" name="ts_fab_company_url" id="ts_fab_company_url" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_company_url', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Your company URL.', 'ts-fab' ); ?></span>
			</td>
		</tr>
	</table>

<?php }



/**
 * Save Fancier Author Box additional user fields
 *
 * @since 1.0
 */
add_action( 'personal_options_update', 'ts_fab_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'ts_fab_save_extra_profile_fields' );

function ts_fab_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	if( isset( $_POST['ts_fab_user_hide'] ) ) {
		update_user_meta( $user_id, 'ts_fab_user_hide', $_POST['ts_fab_user_hide'] );
	} else {
		delete_user_meta( $user_id, 'ts_fab_user_hide' );
	}
	
	update_user_meta( $user_id, 'ts_fab_twitter', strip_tags( $_POST['ts_fab_twitter'] ) );
	update_user_meta( $user_id, 'ts_fab_facebook', strip_tags( $_POST['ts_fab_facebook'] ) );
	update_user_meta( $user_id, 'ts_fab_googleplus', strip_tags( $_POST['ts_fab_googleplus'] ) );
	update_user_meta( $user_id, 'ts_fab_linkedin', strip_tags( $_POST['ts_fab_linkedin'] ) );
	update_user_meta( $user_id, 'ts_fab_instagram', strip_tags( $_POST['ts_fab_instagram'] ) );
	update_user_meta( $user_id, 'ts_fab_flickr', strip_tags( $_POST['ts_fab_flickr'] ) );
	update_user_meta( $user_id, 'ts_fab_pinterest', strip_tags( $_POST['ts_fab_pinterest'] ) );
	update_user_meta( $user_id, 'ts_fab_tumblr', strip_tags( $_POST['ts_fab_tumblr'] ) );
	update_user_meta( $user_id, 'ts_fab_youtube', strip_tags( $_POST['ts_fab_youtube'] ) );
	update_user_meta( $user_id, 'ts_fab_vimeo', strip_tags( $_POST['ts_fab_vimeo'] ) );
	update_user_meta( $user_id, 'ts_fab_position', strip_tags( $_POST['ts_fab_position'] ) );
	update_user_meta( $user_id, 'ts_fab_company', strip_tags( $_POST['ts_fab_company'] ) );
	update_user_meta( $user_id, 'ts_fab_company_url', esc_url_raw( $_POST['ts_fab_company_url'] ) );
	
}