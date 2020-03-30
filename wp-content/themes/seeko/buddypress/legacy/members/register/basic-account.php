<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */
?>

<div class="register-section" id="basic-details-section">

	<?php /***** Basic Account Details ******/ ?>

	<div class="form-group">
		<label for="signup_username"><?php _e( 'Username', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
		<?php

		/**
		 * Fires and displays any member registration username errors.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_signup_username_errors' ); ?>
		<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" <?php bp_form_field_attributes( 'username' ); ?>/>
	</div>

	<div class="form-group">
		<label for="signup_email"><?php _e( 'Email Address', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
		<?php

		/**
		 * Fires and displays any member registration email errors.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_signup_email_errors' ); ?>
		<input type="email" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" <?php bp_form_field_attributes( 'email' ); ?>/>
	</div>

	<div class="form-group">
		<label for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
		<?php

		/**
		 * Fires and displays any member registration password errors.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_signup_password_errors' ); ?>
		<input type="password" name="signup_password" id="signup_password" value="" class="password-entry" <?php bp_form_field_attributes( 'password' ); ?>/>
		<div id="pass-strength-result"></div>
	</div>

	<div class="form-group">
		<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
		<?php

		/**
		 * Fires and displays any member registration password confirmation errors.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_signup_password_confirm_errors' ); ?>
		<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" class="password-entry-confirm" <?php bp_form_field_attributes( 'password' ); ?>/>
	</div>

	<?php

	/**
	 * Fires and displays any extra member registration details fields.
	 *
	 * @since 1.9.0
	 */
	do_action( 'bp_account_details_fields' ); ?>

</div><!-- #basic-details-section -->

<?php

/**
 * Fires after the display of member registration account details fields.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_account_details_fields' ); ?>
