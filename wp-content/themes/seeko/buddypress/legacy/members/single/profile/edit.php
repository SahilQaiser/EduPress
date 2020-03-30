<?php
/**
 * BuddyPress - Members Single Profile Edit
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_profile_edit_content' );

if ( bp_has_profile( 'profile_group_id=' . bp_get_current_profile_group_id() ) ) :
	while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

<form action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php bp_the_profile_group_slug(); ?>">

	<?php

		/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
		do_action( 'bp_before_profile_field_content' ); ?>

		<h4><?php printf( __( "Editing '%s' Profile Group", 'buddypress' ), bp_get_the_profile_group_name() ); ?></h4>

		<?php if ( bp_profile_has_multiple_groups() ) : ?>
			<ul class="button-nav list-unstyled" aria-label="<?php esc_attr_e( 'Profile field groups', 'buddypress' ); ?>" role="navigation">

				<?php bp_profile_group_tabs(); ?>

			</ul>
		<?php endif ;?>

		<div class="clear"></div>

		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

			<div<?php bp_field_css_class( 'editfield' ); ?>>
				<fieldset>
					<div class="form-group">

						<?php
						$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
						$field_type->edit_field_html();

						/**
						 * Fires before the display of visibility options for the field.
						 *
						 * @since 1.7.0
						 */
						do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
						?>
					</div>

					<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>

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

									<fieldset class="">
										<legend><?php esc_html_e( 'Who can see this field?', 'buddypress' ) ?></legend>

										<?php bp_profile_visibility_radio_buttons();?>

									</fieldset>
									<button type="button" class="btn btn-xs" data-toggle="collapse" data-target="#collapse-<?php bp_the_profile_field_id() ?>" aria-expanded="false" aria-controls="collapse-<?php bp_the_profile_field_id() ?>"><?php esc_html_e( 'Close', 'buddypress' ) ?></button>
								</div>
							</div>
						</div>

					<?php else : ?>
						<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
							<?php
							printf(
								__( 'This field can be seen by: %s', 'buddypress' ),
								'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
							);
							?>
						</div>
					<?php endif ?>

					<?php

					/**
					 * Fires after the visibility options for a field.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_custom_profile_edit_fields' ); ?>

				</fieldset>
			</div>

		<?php endwhile; ?>

	<?php

	/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
	do_action( 'bp_after_profile_field_content' ); ?>

	<div class="submit">
		<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?> " />
	</div>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

	<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>

</form>

<?php endwhile; endif; ?>

<?php

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_edit_content' );
