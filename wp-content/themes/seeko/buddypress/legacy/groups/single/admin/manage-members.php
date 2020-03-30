<?php
/**
 * BuddyPress - Groups Admin - Manage Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

$classes = svq_get_col_classes( 'left' );
?>

<h2 class="bp-screen-reader-text"><?php _e( 'Manage Members', 'buddypress' ); ?></h2>

<?php

/**
 * Fires before the group manage members admin display.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_group_manage_members_admin' ); ?>

<div aria-live="polite" aria-relevant="all" aria-atomic="true">

	<div class="bp-widget group-members-list group-admins-list">
		<h4 class="section-header"><?php _e( 'Administrators', 'buddypress' ); ?></h4>

		<?php if ( bp_group_has_members( array( 'per_page' => 15, 'group_role' => array( 'admin' ), 'page_arg' => 'mlpage-admin' ) ) ) : ?>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="admins-list" class="item-list list-unstyled row">
				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>
					<li class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">
						<div class="item-container">
							<div class="item-avatar">
								<figure class="img-dynamic aspect-ratio avatar">
									<a class="img-card" href="#">
										<?php bp_group_member_avatar_thumb('type=full'); ?>
									</a>
								</figure>
							</div>

							<div class="item-card">
								<div class="item">
									<div class="item-meta">
										<span class="joined"><?php bp_group_member_joined_since(); ?></span>
									</div>

									<div class="item-title h5">
										<?php bp_group_member_link(); ?>
									</div>

									<?php

									/**
									 * Fires inside the item section of a member admin item in group management area.
									 *
									 * @since 1.1.0
									 * @since 2.7.0 Added $section parameter.
									 *
									 * @param $section Which list contains this item.
									 */
									do_action( 'bp_group_manage_members_admin_item', 'admins-list' ); ?>
								</div>

								<div class="action">
									<?php if ( count( bp_group_admin_ids( false, 'array' ) ) > 1 ) : ?>
										<a class="button confirm admin-demote-to-member btn btn-sm btn-tag-salmon" data-svq-title="<?php esc_attr_e( 'Demote to Member', 'buddypress' ); ?>" href="<?php bp_group_member_demote_link(); ?>">
											<i class="icon icon-member"></i>
											<span><?php _e( 'Demote', 'seeko' ); ?></span>
										</a>
									<?php endif; ?>

									<?php

									/**
									 * Fires inside the action section of a member admin item in group management area.
									 *
									 * @since 2.7.0
									 *
									 * @param $section Which list contains this item.
									 */
									do_action( 'bp_group_manage_members_admin_actions', 'admins-list' ); ?>
								</div>
							</div>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

		<?php else: ?>

		<div id="message" class="info alert alert-info">>
			<p><?php _e( 'No group administrators were found.', 'buddypress' ); ?></p>
		</div>

		<?php endif; ?>
	</div>

	<div class="bp-widget group-members-list group-mods-list">
		<h4 class="section-header"><?php _e( 'Moderators', 'buddypress' ); ?></h4>

		<?php if ( bp_group_has_members( array( 'per_page' => 15, 'group_role' => array( 'mod' ), 'page_arg' => 'mlpage-mod' ) ) ) : ?>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="mods-list" class="item-list row list-unstyled">

				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>
					<li class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">
						<div class="item-container">
							<div class="item-avatar">
								<figure class="img-dynamic aspect-ratio avatar">
									<a class="img-card" href="#">
										<?php bp_group_member_avatar_thumb(); ?>
									</a>
								</figure>
							</div>

							<div class="item-card">
								<div class="item">
									<div class="item-meta">
										<span class="joined"><?php bp_group_member_joined_since(); ?></span>
									</div>

									<h4 class="item-title h5">
										<?php bp_group_member_link(); ?>
									</h4>
									<?php

									/**
									 * Fires inside the item section of a member admin item in group management area.
									 *
									 * @since 1.1.0
									 * @since 2.7.0 Added $section parameter.
									 *
									 * @param $section Which list contains this item.
									 */
									do_action( 'bp_group_manage_members_admin_item', 'admins-list' ); ?>
								</div>

								<div class="action">
									<a class="button confirm mod-demote-to-member btn btn-sm btn-tag-emerald"  data-svq-title="<?php esc_attr_e( 'Demote to Member', 'buddypress' ); ?>" href="<?php bp_group_member_demote_link(); ?>">
										<i class="icon icon-member"></i>
										<span><?php _e( 'Demote', 'seeko' ); ?></span>
									</a>
									<a href="<?php bp_group_member_promote_admin_link(); ?>"
									   data-svq-title="<?php esc_attr_e( 'Promote to Admin', 'buddypress' ); ?>"
									   class="button confirm mod-promote-to-admin bp-btn btn-square btn btn-sm">
										<i class="icon icon-admin"></i>
									</a>

									<?php

									/**
									 * Fires inside the action section of a member admin item in group management area.
									 *
									 * @since 2.7.0
									 *
									 * @param $section Which list contains this item.
									 */
									do_action( 'bp_group_manage_members_admin_actions', 'mods-list' ); ?>

								</div>
							</div>
						</div>
					</li>
				<?php endwhile; ?>

			</ul>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

		<?php else: ?>

			<div id="message" class="info alert alert-info">
				<p><?php _e( 'No group moderators were found.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>
	</div>

	<div class="bp-widget group-members-list">
		<h4 class="section-header"><?php _e( "Members", 'buddypress' ); ?></h4>

		<?php if ( bp_group_has_members( array( 'per_page' => 15, 'exclude_banned' => 0 ) ) ) : ?>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="members-list" class="item-list list-unstyled row" aria-live="assertive" aria-relevant="all">
				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

					<li class="<?php bp_group_member_css_class(); ?> <?php echo esc_attr( join( ' ', $classes ) ); ?>">
						<div class="item-container">
							<div class="item-avatar">
								<figure class="img-dynamic aspect-ratio avatar">
									<a class="img-card" href="#">
										<?php bp_group_member_avatar_thumb(); ?>
									</a>
								</figure>

							</div>

							<div class="item-card">
								<div class="item">
									<div class="item-meta">
										<span class="joined"><?php bp_group_member_joined_since(); ?></span>
									</div>

									<h4 class="item-title h5">
										<?php bp_group_member_link(); ?>
										<?php
										if ( bp_get_group_member_is_banned() ) {
											echo ' <span class="banned">';
											_e( '(banned)', 'buddypress' );
											echo '</span>';
										} ?>
									</h4>

									<?php

									/**
									 * Fires inside the item section of a member admin item in group management area.
									 *
									 * @since 1.1.0
									 * @since 2.7.0 Added $section parameter.
									 *
									 * @param $section Which list contains this item.
									 */
									do_action( 'bp_group_manage_members_admin_item', 'admins-list' ); ?>
								</div>

								<div class="action">
									<?php if ( bp_get_group_member_is_banned() ) : ?>

										<a href="<?php bp_group_member_unban_link(); ?>" class="button confirm member-unban"><?php esc_html_e( 'Remove Ban', 'buddypress' ); ?></a>

									<?php else : ?>

										<a href="<?php bp_group_member_promote_mod_link(); ?>" class="button confirm member-promote-to-mod btn btn-sm btn-tag-violet" data-svq-title="<?php esc_html_e( 'Promote to Mod', 'buddypress' ); ?>">
											<i class="icon icon-moderator"></i>
											<span>Moderator</span>
										</a>
										<a href="<?php bp_group_member_promote_admin_link(); ?>" class="button confirm member-promote-to-admin bp-btn btn-square btn btn-sm" data-svq-title="<?php esc_html_e( 'Promote to Admin', 'buddypress' ); ?>">
											<i class="icon icon-admin"></i>
										</a>

										<a href="<?php bp_group_member_ban_link(); ?>" class="button confirm member-ban"><?php esc_html_e( 'Kick &amp; Ban', 'buddypress' ); ?></a>
									<?php endif; ?>

									<a href="<?php bp_group_member_remove_link(); ?>" class="button confirm"><?php esc_html_e( 'Remove from group', 'buddypress' ); ?></a>

									<?php

									/**
									 * Fires inside the action section of a member admin item in group management area.
									 *
									 * @since 2.7.0
									 *
									 * @param $section Which list contains this item.
									 */
									do_action( 'bp_group_manage_members_admin_actions', 'members-list' ); ?>
								</div>
							</div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

		<?php else: ?>

			<div id="message" class="info alert alert-info">
				<p><?php _e( 'No group members were found.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>
	</div>

</div>

<?php

/**
 * Fires after the group manage members admin display.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_group_manage_members_admin' );
