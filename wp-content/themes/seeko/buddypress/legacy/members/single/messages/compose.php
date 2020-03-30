<?php
/**
 * BuddyPress - Members Single Messages Compose
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>
<h2 class="bp-screen-reader-text"><?php
	/* translators: accessibility text */
	esc_html_e( 'Compose Message', 'buddypress' );
?></h2>

<form action="<?php bp_messages_form_action('compose' ); ?>" method="post" id="send_message_form" class="standard-form" enctype="multipart/form-data">

	<?php

	/**
	 * Fires before the display of message compose content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_messages_compose_content' ); ?>

	<div class="form-group">
		<label for="send-to-input"><?php esc_html_e("Send To (Username or Friend's Name)", 'buddypress' ); ?></label>
		<ul class="first acfb-holder list-unstyled">
			<li>
				<?php bp_message_get_recipient_tabs(); ?>
				<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
			</li>
		</ul>
	</div>

	<?php if ( bp_current_user_can( 'bp_moderate' ) ) : ?>
	<div class="form-group form-check">
		<input type="checkbox" id="send-notice" name="send-notice" value="1" />
		<label for="send-notice"><?php esc_html_e( "This is a notice to all users.", 'buddypress' ); ?></label>
	</div>
	<?php endif; ?>

	<div class="form-group">
	<label for="subject"><?php esc_html_e( 'Subject', 'buddypress' ); ?></label>
	<input type="text" name="subject" id="subject" value="<?php bp_messages_subject_value(); ?>" />
	</div>

	<div class="form-group">
	<label for="message_content"><?php esc_html_e( 'Message', 'buddypress' ); ?></label>
	<textarea name="content" id="message_content" data-autoresize="true"><?php bp_messages_content_value(); ?></textarea>
	</div>
	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames(); ?>" />

	<?php

	/**
	 * Fires after the display of message compose content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_messages_compose_content' ); ?>

	<div class="submit">
		<input type="submit" class="btn btn-primary" value="<?php esc_attr_e( "Send Message", 'buddypress' ); ?>" name="send" id="send" />
	</div>

	<?php wp_nonce_field( 'messages_send_message' ); ?>
</form>

<script type="text/javascript">
	document.getElementById("send-to-input").focus();
</script>

