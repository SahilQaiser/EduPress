<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */
?>

<?php if ( bp_get_blog_signup_allowed() ) : ?>

<?php
/**
 * Fires before the display of member registration blog details fields.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_blog_details_fields' ); ?>


<div class="register-section" id="blog-details-section">

	<h3><?php _e( 'Blog Details', 'buddypress' ); ?></h3>

	<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'buddypress' ); ?></label></p>

	<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

		<div class="form-group">
			<label for="signup_blog_url"><?php _e( 'Blog URL', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
			<?php

			/**
			 * Fires and displays any member registration blog URL errors.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_signup_blog_url_errors' ); ?>

			<?php if ( is_subdomain_install() ) : ?>
				<div class="input-group">
					<input type="text" class="form-control" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
					<div class="input-group-append">
						<span class="input-group-text">.<?php bp_signup_subdomain_base(); ?></span>
					</div>
				</div>

			<?php else : ?>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><?php echo esc_url ( home_url( '/' ) ); ?></span>
					</div>
					<input type="text" class="form-control" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
				</div>
			<?php endif; ?>
		</div>

		<div class="form-group">
			<label for="signup_blog_title"><?php _e( 'Site Title', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
			<?php

			/**
			 * Fires and displays any member registration blog title errors.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_signup_blog_title_errors' ); ?>
			<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />
		</div>

		<fieldset class="register-site">
			<legend class="label"><?php _e( 'Privacy: I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ); ?></legend>
			<?php

			/**
			 * Fires and displays any member registration blog privacy errors.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_signup_blog_privacy_errors' ); ?>

			<label for="signup_blog_privacy_public"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'buddypress' ); ?></label>
			<label for="signup_blog_privacy_private"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'buddypress' ); ?></label>
		</fieldset>

		<?php

		/**
		 * Fires and displays any extra member registration blog details fields.
		 *
		 * @since 1.9.0
		 */
		do_action( 'bp_blog_details_fields' ); ?>

	</div>

</div><!-- #blog-details-section -->

<?php

/**
 * Fires after the display of member registration blog details fields.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_blog_details_fields' ); ?>

<?php endif; ?>

