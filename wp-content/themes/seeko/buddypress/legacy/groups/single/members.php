<?php
/**
 * BuddyPress - Groups Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

$classes = svq_get_col_classes( 'left' );
?>

<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) ) ) : ?>

	<?php

	/**
	 * Fires before the display of the group members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_group_members_content' ); ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the group members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_group_members_list' ); ?>

	<ul id="member-list" class="item-list list-unstyled row">

		<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

			<li class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">
				<div class="item-container">
					<div class="item-avatar">
					<a href="<?php bp_group_member_domain(); ?>">
						<figure class="img-dynamic aspect-ratio avatar">
							<div class="img-card">
								<?php bp_group_member_avatar_thumb('type=full'); ?>
							</div>
						</figure>
					</a>
					</div>

					<div class="item-card">
						<div class="item">
							<div class="h5 item-title">
								<?php bp_group_member_link(); ?>
							</div>

							<div class="item-meta">
								<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_member_joined_since( array( 'relative' => false ) ) ); ?>"><?php bp_group_member_joined_since(); ?></span>
							</div>
						</div>


						<?php

						/**
						 * Fires inside the listing of an individual group member listing item.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_group_members_list_item' ); ?>

						<?php if ( bp_is_active( 'friends' ) ) : ?>

							<div class="action">

								<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>

								<?php

								/**
								 * Fires inside the action section of an individual group member listing item.
								 *
								 * @since 1.1.0
								 */
								do_action( 'bp_group_members_list_item_action' ); ?>

							</div>

						<?php endif; ?>
					</div>
				</div>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the display of the group members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires after the display of the group members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No members were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;
