<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of the registration submit buttons.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_registration_submit_buttons' ); ?>

<div class="submit text-right">
	<input type="submit" class="btn-primary" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( 'Complete Sign Up', 'buddypress' ); ?>" />
</div>

<?php

/**
 * Fires after the display of the registration submit buttons.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_registration_submit_buttons' ); ?>

<?php wp_nonce_field( 'bp_new_signup' ); ?>
