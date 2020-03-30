<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoPostsCarousel extends Widget_Base {

	public function get_name() {
		return 'seeko-posts-carousel';
	}

	public function get_title() {
		return __( 'Posts Carousel (Seeko)', 'seeko' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
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
			'total',
			[
				'label'       => __( 'Total posts', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '8',
				'placeholder' => __( 'Total posts', 'seeko' ),
			]
		);

		$this->add_control(
			'sort',
			[
				'label'   => esc_html__( 'Sort', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'newest'  => esc_html__( 'Newest', 'seeko' ),
					'oldest'  => esc_html__( 'Oldest', 'seeko' ),
					'random' => esc_html__( 'Random', 'seeko' ),
				]
			]
		);

		$this->add_control(
			'scroll',
			[
				'label'       => __( 'Posts to scroll', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);


		$this->add_control(
			'visible',
			[
				'label'       => __( 'Visible Posts - Large Desktop', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '5',
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-lg',
			[
				'label'       => __( 'Visible Posts - Desktop', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 4,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-md',
			[
				'label'       => __( 'Visible Posts - Tablet', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 3,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-sm',
			[
				'label'       => __( 'Visible Posts - Mobile', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);

		$this->add_control(
			'visible-xs',
			[
				'label'       => __( 'Visible Posts - Small Mobile', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 2,
				'placeholder' => '',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		wp_enqueue_script( 'slick' );

		$settings = $this->get_settings();
		$rand     = mt_rand( 99, 999 );

		/* Query args */
		$args = array(
			'showposts'    => $settings['total'],
		);

		if ( $settings['sort'] == 'oldest' ) {
			$args['order'] = 'ASC';
		} elseif( $settings['sort'] == 'random' ) {
			$args['orderby'] = 'rand';
			$args['order'] = 'ASC';
		}

		$this->add_render_attribute( 'wrapper', [
			'class' => 'svq-sh-carousel',
		] );

		if ($settings['full-width'] == 1) {
			$this->add_render_attribute( 'wrapper', [
				'class' => 'svq-sh-carousel-full',
			] );
		}

		// The Query
		$the_query = new \WP_Query( $args );
		if ( $the_query->have_posts() ) : ?>

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
			<section class="svq-slick" data-carousel="slick-<?php echo esc_attr( $rand ); ?>-0" data-arrows="false"
			         data-infinite="true"
			         data-show-slides="<?php echo esc_attr( $settings['visible'] ); ?>"
			         data-lg-show-slides="<?php echo esc_attr( $settings['visible-lg'] ); ?>"
			         data-md-show-slides="<?php echo esc_attr( $settings['visible-md'] ); ?>"
			         data-sm-show-slides="<?php echo esc_attr( $settings['visible-sm'] ); ?>"
			         data-xs-show-slides="<?php echo esc_attr( $settings['visible-xs'] ); ?>"
			         data-scroll-slides="<?php echo esc_attr( $settings['scroll'] ); ?>">

				<?php
				?>
				<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
					<div class="col-lg-3">
						<?php get_template_part('page-parts/post/card-content'); ?>
					</div>
				<?php endwhile; ?>

			</section>

		</div>

		<?php
		endif;
		/* Restore original Post Data */
		wp_reset_postdata();
	}

}
