<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoGroupsCarousel extends Widget_Base {

	public function get_name() {
		return 'groups-carousel';
	}

	public function get_title() {
		return __( 'Groups Carousel', 'seeko' );
	}

	public function get_icon() {
		return 'eicon-slideshow';
	}

	public function get_categories() {
		return [ 'seeko-elements' ];
	}

	public function get_fields() {
		return svq_bp_fields_array();
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_groups_carousel',
			[
				'label' => __( 'Settings', 'seeko' ),
			]
		);

		$this->add_control(
			'full-width',
			[
				'label'       => __( 'Carousel Style', 'seeko' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Boxed', 'seeko' ),
				'label_on' => esc_html__( 'Full-Width', 'seeko' ),
				'default' => '',
				'return_value' => '1',
				'description' => esc_html__( 'Enable Full width Carousel', 'seeko' ),
			]
		);


		$this->add_control(
			'type',
			[
				'label'   => __( 'Sort', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'newest'       => __( 'Newest', 'seeko' ),
					'active'       => __( 'Most Active', 'seeko' ),
					'popular'      => __( 'Most Popular', 'seeko' ),
					'random'       => __( 'Random', 'seeko' ),
					'alphabetical' => __( 'Alphabetical', 'seeko' ),
				]
			]
		);

		$this->add_control(
			'total',
			[
				'label'       => __( 'Total groups', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '12',
				'placeholder' => __( 'Total groups', 'seeko' ),
			]
		);

		$this->add_control(
			'scroll',
			[
				'label'       => __( 'Groups to scroll', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);


		$this->add_control(
			'visible',
			[
				'label'       => __( 'Visible groups - Large Desktop', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '5',
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-lg',
			[
				'label'       => __( 'Visible groups - Desktop', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 4,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-md',
			[
				'label'       => __( 'Visible groups - Tablet', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 3,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-sm',
			[
				'label'       => __( 'Visible groups - Mobile', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-xs',
			[
				'label'       => __( 'Visible groups - Small Mobile', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
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

		wp_enqueue_script( 'slick' );

		$settings = $this->get_settings();
		$rand     = mt_rand( 99, 999 );
		global $groups_template;

		$this->add_render_attribute( 'wrapper', [
			'class' => 'svq-sh-carousel groups dir-list',
		] );

		if ( $settings['full-width'] == 1 ) {
			$this->add_render_attribute( 'wrapper', [
				'class' => 'svq-sh-carousel-full',
			] );
		}

		?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

			<div class="row mb-3">

				<div class="col-12 d-flex justify-content-between">
					<a href="#" class="svq-slick-prev btn btn-xs" data-carousel="slick-<?php echo esc_attr( $rand ); ?>-0">
						<i class="icon icon-arrow-left"></i>
					</a>
					<a href="#" class="svq-slick-next btn btn-xs" data-carousel="slick-<?php echo esc_attr( $rand ); ?>-0">
						<i class="icon icon-arrow-right"></i>
					</a>
				</div>

			</div>
			<section class="item-list svq-slick" data-carousel="slick-<?php echo esc_attr( $rand ); ?>-0" data-arrows="false"
			         data-infinite="true"
			         data-show-slides="<?php echo esc_attr( $settings['visible'] ); ?>"
			         data-lg-show-slides="<?php echo esc_attr( $settings['visible-lg'] ); ?>"
			         data-md-show-slides="<?php echo esc_attr( $settings['visible-md'] ); ?>"
			         data-sm-show-slides="<?php echo esc_attr( $settings['visible-sm'] ); ?>"
			         data-xs-show-slides="<?php echo esc_attr( $settings['visible-xs'] ); ?>"
			         data-scroll-slides="<?php echo esc_attr( $settings['scroll'] ); ?>">

				<?php
				$query_string = '&type=' . $settings['type'] . '&per_page=' . $settings['total'] . '&max=' . $settings['total'];

				if ( bp_has_groups( bp_ajax_querystring( 'groups' ) . $query_string ) ) :

					while ( bp_groups() ) : bp_the_group(); ?>

						<div class="col-lg-3">
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
												<?php echo ucwords( esc_html( $type ) ); ?>
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
						</div>

						<?php
					endwhile;
				else :
					esc_html_e( 'No groups found by the criteria.', 'seeko' );
				endif;
				?>
			</section>

		</div>

		<?php
	}

}
