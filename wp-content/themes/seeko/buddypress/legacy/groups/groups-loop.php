<?php
/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter().
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<?php

/**
 * Fires before the display of groups from the groups loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_groups_loop' );

global $groups_template;
//Groups Directory
$classes = svq_get_col_classes( svq_bp_get_layout( 'groups_dir' ) );

// Member profile - Groups
if( bp_is_user() && bp_is_groups_component() ) {
	$classes = svq_get_col_classes( 'left' );
}
?>

<?php if ( bp_get_current_group_directory_type() ) : ?>
	<p class="current-group-type"><?php bp_current_group_directory_type_message() ?></p>
<?php endif; ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) . '&per_page=' . svq_option( 'bp_groups_perpage', 24 ) ) ) : ?>

	<?php

	/**
	 * Fires before the listing of the groups list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list list-unstyled row" aria-live="assertive" aria-atomic="true" aria-relevant="all">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li <?php bp_group_class( $classes ); ?>>
			<div class="item-container">
				<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<figure class="img-dynamic aspect-ratio avatar">
							<a class="img-card" href="<?php bp_group_permalink(); ?>">
								<?php svq_bp_group_avatar(); ?>
							</a>
						</figure>

					</div>
				<?php endif; ?>

				<div class="item-card">
					<div class="item">

						<div class="item-meta">
							<div class="meta">
								<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>
							</div>
						</div>

						<h4 class="item-title h5"><?php bp_group_link(); ?></h4>

						<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>

						<?php

						/**
						 * Fires inside the listing of an individual group listing item.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_directory_groups_item' ); ?>

					</div>

					<div class="action">

						<?php
						global $groups_template;
						$groups_template->group;
						$type = $groups_template->group->status;
						?>

						<span class="meta privacy <?php echo esc_attr( $type ); ?>">
							<i class="icon icon-public-fill"></i>
							<?php echo ucwords( $type ); ?>
						</span>

						<?php

						/**
						 * Fires inside the action section of an individual group listing item.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_directory_groups_actions' ); ?>


					</div>
				</div>

			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the listing of the groups list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of groups from the groups loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_groups_loop' );
