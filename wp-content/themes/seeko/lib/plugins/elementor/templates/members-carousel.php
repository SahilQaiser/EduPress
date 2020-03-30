<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoMembersCarousel extends Widget_Base {

	public function get_name() {
		return 'members-carousel';
	}

	public function get_title() {
		return esc_html__( 'Members Carousel', 'seeko' );
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
			'section_members_carousel',
			[
				'label' => esc_html__( 'Settings', 'seeko' ),
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
			'scroll',
			[
				'label'       => __( 'Members to scroll', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible',
			[
				'label'       => __( 'Visible members - Large Desktop', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '5',
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-lg',
			[
				'label'       => __( 'Visible members - Desktop', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 4,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-md',
			[
				'label'       => __( 'Visible members - Tablet', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 3,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-sm',
			[
				'label'       => __( 'Visible members - Mobile', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-xs',
			[
				'label'       => __( 'Visible members - Small Mobile', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);


		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'field_id', [
				'label' => __( 'Field Name', 'seeko' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_fields(),
				'default' => __( 'List Content' , 'seeko' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'field_value', [
				'label' => __( 'Field Value', 'seeko' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'label', [
				'label' => __( 'Tab Label', 'seeko' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'icon', [
				'label' => __( 'Tab Icon', 'seeko' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'default' => __( '' , 'seeko' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Member Types', 'seeko' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'label' => __( 'Women', 'seeko' ),
						'field_id' => xprofile_get_field_id_from_name( __( 'I am a', 'seeko' ) ),
						'field_value' => __( 'Woman', 'seeko' ),
						'icon' => 'icon icon-female',
					],
					[
						'label' => __( 'Men', 'seeko' ),
						'field_id' => xprofile_get_field_id_from_name( __( 'I am a', 'seeko' ) ),
						'field_value' => __( 'Man', 'seeko' ),
						'icon' => 'icon icon-male',
					],
				],
				'title_field' => '{{{ label }}}',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		if ( ! function_exists( 'bp_is_active' ) ) {
			esc_html_e( 'You need BuddyPress plugin to be active!', 'seeko' );
			return;
		}

		wp_enqueue_script( 'slick' );

		$settings = $this->get_settings();
		$rand = mt_rand(99, 999);

		$this->add_render_attribute( 'wrapper', [
			'class' => 'svq-sh-carousel',
		] );

		if ($settings['full-width'] == 1) {
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

					<?php if( ! empty( $settings['tabs'] ) && count( $settings['tabs'] ) > 1 ) : ?>

						<div class="nav svq-slick-filters" role="tablist">
							<?php foreach ( $settings['tabs'] as $k => $tab ) :
								$icon = $tab['icon'];
								?>

								<a class="svq-slick-filter btn btn-xs<?php echo esc_attr( $k == 0 ? ' active' : '' ); ?>"
								   id="<?php echo sanitize_title( $tab['label'] ); ?>-tab-<?php echo esc_attr( $rand ); ?>"
								   aria-controls="<?php echo sanitize_title($tab['label']); ?>-<?php echo esc_attr( $rand ); ?>"
								   data-toggle="tab"
								   href="#<?php echo sanitize_title($tab['label']); ?>-<?php echo esc_attr( $rand ); ?>" role="tab">
									<i class="<?php echo esc_attr( $icon ); ?>"></i>
									<span><?php echo esc_html( $tab['label'] ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>

					<?php endif; ?>

					<a href="#" class="svq-slick-next btn btn-xs" data-carousel="slick-<?php echo esc_attr( $rand ); ?>-0">
						<i class="icon icon-arrow-right"></i>
					</a>

				</div>
			</div>

			<?php if( ! empty( $settings['tabs'] ) && count( $settings['tabs'] ) > 1 ) : ?>
			<div class="tab-content">
			<?php endif; ?>

			<?php foreach ( $settings['tabs'] as $k => $tab ) : ?>

				<?php if( ! empty( $settings['tabs'] ) && count( $settings['tabs'] ) > 1 ) : ?>
				<div class="tab-pane fade<?php echo esc_attr( $k == 0 ? ' show active' : '' ); ?>" id="<?php echo sanitize_title($tab['label']); ?>-<?php echo esc_attr( $rand ); ?>" role="tabpanel" aria-labelledby="<?php echo sanitize_title( $tab['label'] ); ?>-tab-<?php echo esc_attr( $rand ); ?>">
				<?php endif; ?>

					<section class="item-list svq-slick row" data-carousel="slick-<?php echo esc_attr( $rand ); ?>-<?php echo esc_attr( $k ); ?>"
					         data-arrows="false" data-infinite="true"
					         data-show-slides="<?php echo esc_attr( $settings['visible'] );?>"
					         data-lg-show-slides="<?php echo esc_attr( $settings['visible-lg'] );?>"
					         data-md-show-slides="<?php echo esc_attr( $settings['visible-md'] );?>"
					         data-sm-show-slides="<?php echo esc_attr( $settings['visible-sm'] );?>"
					         data-xs-show-slides="<?php echo esc_attr( $settings['visible-xs'] );?>"
					         data-scroll-slides="<?php echo esc_attr( $settings['scroll'] );?>">

						<?php
						$query_string = '&type=' . $settings['type'] . '&per_page=' . $settings['total'] . '&max=' . $settings['total'];
						if ( isset( $tab['field_id'] ) && isset( $tab['field_value'] ) && ! empty( $tab['field_id'] ) &&! empty( $tab['field_value'] ) ) {
							$query_string .= svq_bp_custom_ids( $tab['field_id'], $tab['field_value'] );
						}

						if ( bp_has_members( bp_ajax_querystring( 'members' ) . $query_string ) ) :

							while ( bp_members() ) : bp_the_member(); ?>
							<div class="col-lg-3">
								<div <?php bp_member_class(); ?>>
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
								</div>
							</div>

								<?php
							endwhile;
						else :
							esc_html_e( 'No members found by the criteria.', 'seeko' );
						endif;
						?>
					</section>

				<?php if( ! empty( $settings['tabs'] ) && count( $settings['tabs'] ) > 1 ) : ?>
				</div>
				<?php endif; ?>

			<?php endforeach; ?>

			<?php if( ! empty( $settings['tabs'] ) && count( $settings['tabs'] ) > 1 ) : ?>
			</div>
			<?php endif; ?>

		</div>

		<?php
	}

}
