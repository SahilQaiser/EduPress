<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoMembersGrid extends Widget_Base {

	public function get_name() {
		return 'members-grid';
	}

	public function get_title() {
		return esc_html__( 'Members Grid', 'seeko' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'seeko-elements' ];
	}
	
	public function get_fields() {
		return svq_bp_fields_array();
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_members_carousel',
			[
				'label' => esc_html__( 'Settings', 'seeko' ),
			]
		);

		$this->add_control(
			'type',
			[
				'label'   => esc_html__( 'Sort', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'newest'  => esc_html__( 'Newest', 'seeko' ),
					'active'  => esc_html__( 'Most Active', 'seeko' ),
					'popular' => esc_html__( 'Most Popular', 'seeko' ),
				]
			]
		);

		$this->add_control(
			'total',
			[
				'label'       => __( 'Total members', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '12',
				'placeholder' => __( 'Total members', 'seeko' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => [
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
				]
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		if ( ! function_exists( 'bp_is_active' ) ) {
			esc_html_e( 'You need BuddyPress plugin to be active!', 'seeko' );
			return;
		}

		$settings = $this->get_settings();
		$rand = mt_rand(99, 999);

		global $members_template;

		if ( $settings['columns'] == '5' ) {
			$layout = 'full';
		} else {
			$layout = $settings['columns'];
		}

		$classes = svq_get_col_classes( $layout );
		$classes[] = 'avatar-status';

		?>
		<div id="members-dir-list" class="members dir-list">

			<?php
			$query_string = '&type=' . $settings['type'] . '&per_page=' . $settings['total'] . '&max=' . $settings['total'];

			if ( bp_has_members( bp_ajax_querystring( 'members' ) . $query_string ) ) : ?>

				<ul id="members-list-grid" class="item-list list-unstyled row" aria-live="assertive" aria-relevant="all">

					<?php while ( bp_members() ) : bp_the_member(); ?>

						<li <?php bp_member_class( $classes ); ?>>
							<div class="item-container">
								<div class="item-avatar">
									<figure class="img-dynamic aspect-ratio avatar">

										<a class="img-card" href="<?php bp_member_permalink(); ?>">
											<?php svq_bp_loop_avatar(); ?>
										</a>

									</figure>
								</div>

								<div class="item-card">
									<div class="item">

										<div class="item-meta">
											<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>">
												<?php bp_member_last_active(); ?>
											</span>
										</div>

										<h4 class="item-title h5">
											<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

											<?php if ( bp_get_member_latest_update() ) : ?>

												<span class="update"> <?php bp_member_latest_update(); ?></span>

											<?php endif; ?>
										</h4>

										<?php

										/**
										 * Fires inside the display of a directory member item.
										 *
										 * @since 1.1.0
										 */
										do_action( 'bp_directory_members_item' ); ?>

										<?php
										/***
										 * If you want to show specific profile fields here you can,
										 * but it'll add an extra query for each member in the loop
										 * (only one regardless of the number of fields you show):
										 *
										 * bp_member_profile_data( 'field=the field name' );
										 */
										?>
									</div>

									<div class="action"><?php

										/**
										 * Fires inside the members action HTML markup to display actions.
										 *
										 * @since 1.1.0
										 */
										do_action( 'bp_directory_members_actions' ); ?></div>
								</div>

							</div>
						</li>

					<?php endwhile; ?>
				</ul>

			<?php else :
				esc_html_e( 'No members found by the criteria.', 'seeko' );
			endif;
			?>
		</div>

		<?php
	}

}
