<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoGroupsGrid extends Widget_Base {

	public function get_name() {
		return 'groups-grid';
	}

	public function get_title() {
		return esc_html__( 'Groups Grid', 'seeko' );
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
			'section_groups_grid',
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
					'newest'       => esc_html__( 'Newest', 'seeko' ),
					'active'       => esc_html__( 'Most Active', 'seeko' ),
					'popular'      => esc_html__( 'Most Popular', 'seeko' ),
					'random'       => esc_html__( 'Random', 'seeko' ),
					'alphabetical' => esc_html__( 'Alphabetical', 'seeko' ),
				]
			]
		);

		$this->add_control(
			'total',
			[
				'label'       => esc_html__( 'Total groups', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '12',
				'placeholder' => esc_html__( 'Total groups', 'seeko' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => [
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

		if ( ! bp_is_active( 'groups' ) ) {
			esc_html_e( 'Groups component needs to be active!', 'seeko' );

			return;
		}

		$settings = $this->get_settings();
		$rand     = mt_rand( 99, 999 );

		global $groups_template;
		if ( $settings['columns'] == '5' ) {
			$layout = 'full';
		} else {
			$layout = 'four';
		}

		$classes = svq_get_col_classes( $layout );
		?>

		<div class="groups dir-list">

			<?php
			$query_string = '&type=' . $settings['type'] . '&per_page=' . $settings['total'] . '&max=' . $settings['total'];

			if ( bp_has_groups( bp_ajax_querystring( 'groups' ) . $query_string ) ) : ?>

				<ul class="item-list list-unstyled row" aria-live="assertive" aria-atomic="true" aria-relevant="all">

				<?php while ( bp_groups() ) : bp_the_group(); ?>

					<li <?php bp_group_class( $classes ); ?>>
						<div <?php bp_group_class(); ?>>
							<div class="item-container">
								<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
									<div class="item-avatar">
										<figure class="img-dynamic have-shadow aspect-ratio avatar">
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
						</div>
					</li>

					<?php
				endwhile; ?>
				</ul>

			<?php else :
				esc_html_e( 'No groups found by the criteria.', 'seeko' );
			endif;
			?>

		</div>

		<?php
	}

}
