<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */
?>

<?php if ( bp_is_active( 'xprofile' ) ) : ?>

<?php
/**
* Fires before the display of member registration xprofile fields.
*
* @since 1.2.4
*/
do_action( 'bp_before_signup_profile_fields' );
?>

<div class="register-section" id="profile-details-section">

	<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
	<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

			<div<?php bp_field_css_class( 'editfield' ); ?>>
				<fieldset>

					<div class="form-group">
						<?php
						$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
						$field_type->edit_field_html();

						/**
						 * Fires before the display of the visibility options for xprofile fields.
						 *
						 * @since 1.7.0
						 */
						do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
						?>
					</div>

					<?php
					if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
						<div class="accordion-card">
							<div class="accordion-header field-visibility-settings-toggle" id="heading-<?php bp_the_profile_field_id() ?>">

								<div class="accordion-head"><span id="<?php bp_the_profile_field_input_name(); ?>-2">
									<?php
									printf(
										__( 'This field can be seen by: %s', 'buddypress' ),
										'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
									);
									?>
									</span>

									<button type="button" class="btn btn-xs" aria-describedby="<?php bp_the_profile_field_input_name(); ?>-2" aria-expanded="true" aria-expanded="false" data-toggle="collapse" data-target="#collapse-<?php bp_the_profile_field_id() ?>" aria-expanded="false" aria-controls="collapse-<?php bp_the_profile_field_id() ?>" ><?php _ex( 'Change', 'Change profile field visibility level', 'buddypress' ); ?></button>
								</div>
							</div>

							<div class="collapse" id="collapse-<?php bp_the_profile_field_id() ?>" class="collapse" aria-labelledby="heading-<?php bp_the_profile_field_id() ?>">
								<div class="field-visibility-settings accordion-body" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">

									<fieldset class="mb-2">
										<legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

										<?php bp_profile_visibility_radio_buttons();?>

									</fieldset>
									<button type="button" class="btn btn-xs" data-toggle="collapse" data-target="#collapse-<?php bp_the_profile_field_id() ?>" aria-expanded="false" aria-controls="collapse-<?php bp_the_profile_field_id() ?>"><?php _e( 'Close', 'buddypress' ) ?></button>
								</div>
							</div>
						</div>
					<?php else : ?>
						<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
							<?php
							printf(
								__( 'This field can be seen by: %s', 'buddypress' ),
								'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
							);
							?>
						</p>
					<?php endif ?>

					<?php

					/**
					 * Fires after the display of the visibility options for xprofile fields.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_custom_profile_edit_fields' ); ?>

				</fieldset>
			</div>

		<?php endwhile; ?>

		<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

	<?php endwhile; endif; endif; ?>

	<?php

	/**
	 * Fires and displays any extra member registration xprofile fields.
	 *
	 * @since 1.9.0
	 */
	do_action( 'bp_signup_profile_fields' ); ?>

</div><!-- #profile-details-section -->

<?php

/**
 * Fires after the display of member registration xprofile fields.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_signup_profile_fields' ); ?>

<?php endif; ?>

