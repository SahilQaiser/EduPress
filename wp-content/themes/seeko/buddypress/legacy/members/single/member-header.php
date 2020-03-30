<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */


$avatar_class = 'img-dynamic have-shadow aspect-ratio avatar avatar-status';
if ( svq_get_member_online_class( bp_displayed_user_id() ) ) {
	$avatar_class .= ' avatar-online';
}
?>

<?php

/**
 * Fires before the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_member_header' ); ?>

<div id="cover-image-container">
	<a id="header-cover-image"></a>

	<div id="item-header-cover-image">
		<div id="item-header-avatar">

			<div class="user-avatar-wrapper">
				<!-- Avatar -->
				<figure id="user-avatar" class="<?php echo esc_attr( $avatar_class ); ?>">
					<a class="img-card" href="<?php bp_displayed_user_link(); ?>">
						<?php svq_bp_displayed_avatar( true ); ?>
					</a>
				</figure>

				<?php do_action('bp_member_header_after_avatar'); ?>
			</div>

			<div class="user-meta-wrapper">
				<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
					<span class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></span>
				<?php endif; ?>
				<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_user_last_activity( bp_displayed_user_id() ) ); ?>"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

			</div>

			<h1 id="user-name" class="h3"><?php bp_displayed_user_fullname(); ?></h1>

			<?php do_action( 'bp_member_header_list_items' ); ?>


			<div id="item-buttons"><?php

				/**
				 * Fires in the member header actions section.
				 *
				 * @since 1.2.6
				 */
				do_action( 'bp_member_header_actions' ); ?></div><!-- #item-buttons -->

		</div><!-- #item-header-avatar -->

		<div id="item-header-content">

			<div id="template-notices" class="alert alert-info" role="alert" aria-atomic="true"><?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
				do_action( 'template_notices' ); ?></div><?php

			/**
			 * Fires before the display of the member's header meta.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_before_member_header_meta' ); ?>

			<div id="item-meta">

				<?php if ( bp_is_active( 'activity' ) ) : ?>

					<div id="latest-update">

						<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

					</div>

				<?php endif; ?>

				<?php

				/**
				 * Fires after the group header actions section.
				 *
				 * If you'd like to show specific profile fields here use:
				 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_profile_header_meta' );

				?>

			</div><!-- #item-meta -->

			<?php

			/**
			 * Fires after the display of a member's header.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_after_member_header' ); ?>

		</div><!-- #item-header-content -->

	</div><!-- #item-header-cover-image -->
</div><!-- #cover-image-container -->
