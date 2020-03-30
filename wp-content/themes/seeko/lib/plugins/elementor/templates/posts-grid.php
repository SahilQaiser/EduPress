<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoPostsGrid extends Widget_Base {

	public function get_name() {
		return 'seeko-posts-grid';
	}

	public function get_title() {
		return __( 'Posts Grid (Seeko)', 'seeko' );
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
				'label' => __( 'Settings', 'seeko' ),
			]
		);


		$this->add_control(
			'total',
			[
				'label'       => __( 'Total posts', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '12',
				'placeholder' => __( 'Total posts', 'seeko' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => [
					'3'  => '3',
					'4'  => '4',
				]
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

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();

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

		if ( $settings['columns'] == 4 ) {
			$responsive_classes = 'col-xl-3 col-lg-4 col-md-6';
		} else {
			$responsive_classes = 'col-xl-4 col-md-6';
		}
		// The Query
		$the_query = new \WP_Query( $args );
		if ( $the_query->have_posts() ) : ?>

		<div class="row">

			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<div class="<?php echo esc_attr( $responsive_classes ); ?> ">
					<?php get_template_part('page-parts/post/card-content'); ?>
				</div>
			<?php endwhile; ?>

		</div>

		<?php
		endif;
		/* Restore original Post Data */
		wp_reset_postdata();
	}

}
