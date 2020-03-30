<?php
/**
 * BuddyPress - Members Register
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<div id="buddypress">

	<?php

	/**
	 * Fires at the top of the BuddyPress member registration page template.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_register_page' ); ?>

	<div class="page" id="register-page">

		<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

		<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
				do_action( 'template_notices' ); ?>

			</div>

			<?php

			/**
			 * Fires before the display of the registration disabled message.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_before_registration_disabled' ); ?>

				<p><?php _e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>

			<?php

			/**
			 * Fires after the display of the registration disabled message.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_after_registration_disabled' ); ?>
		<?php endif; // registration-disabled signup step ?>

		<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
				do_action( 'template_notices' ); ?>

			</div>

			<?php
			$page_id = svq_bp_get_component_id();
			if ( $page_id && defined( 'ELEMENTOR_PATH' ) && get_post_meta( $page_id, '_elementor_edit_mode', true ) ) :
				svq_elementor_the_content( $page_id );
			?>
			<?php else : ?>

				<p><?php _e( 'Registering for this site is easy. Just fill in the fields below, and we\'ll get a new account set up for you in no time.', 'buddypress' ); ?></p>

				<?php

				/**
				 * Fires before the display of member registration account details fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_before_account_details_fields' ); ?>

				<div class="row">
					<div class="col-lg-6 col-md-6">
						<h3><?php _e( 'Account Details', 'buddypress' ); ?></h3>
						<?php bp_get_template_part( 'members/register/basic-account' ); ?>
					</div>

					<?php /***** Extra Profile Details ******/ ?>
					<div class="col-lg-6 col-md-6">
						<?php if ( bp_is_active( 'xprofile' ) ) : ?>
							<h3><?php _e( 'Profile Details', 'buddypress' ); ?></h3>
						<?php endif; ?>

						<?php bp_get_template_part( 'members/register/xprofile' ); ?>
					</div>

				</div>

				<?php bp_get_template_part( 'members/register/blog' ); ?>

				<?php bp_get_template_part( 'members/register/submit-buttons' ); ?>

			<?php endif; ?>


		<?php endif; // request-details signup step ?>

		<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

			<div class="container bp-register-completed">

				<div id="template-notices" role="alert" aria-atomic="true">
					<?php

					/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
					do_action( 'template_notices' ); ?>

				</div>

				<?php

				/**
				 * Fires before the display of the registration confirmed messages.
				 *
				 * @since 1.5.0
				 */
				do_action( 'bp_before_registration_confirmed' ); ?>

				<div id="template-notices" role="alert" aria-atomic="true">
					<?php if ( bp_registration_needs_activation() ) : ?>
						<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ); ?></p>
					<?php else : ?>
						<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ); ?></p>
					<?php endif; ?>
				</div>

				<?php

				/**
				 * Fires after the display of the registration confirmed messages.
				 *
				 * @since 1.5.0
				 */
				do_action( 'bp_after_registration_confirmed' ); ?>

			</div>
		<?php endif; // completed-confirmation signup step ?>

		<?php

		/**
		 * Fires and displays any custom signup steps.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php

	/**
	 * Fires at the bottom of the BuddyPress member registration page template.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_register_page' ); ?>

</div><!-- #buddypress -->
