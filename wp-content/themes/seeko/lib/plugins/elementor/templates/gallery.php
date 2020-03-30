<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SeekoGallery extends Widget_Base {

	public function get_name() {
		return 'seeko-gallery';
	}

	public function get_title() {
		return esc_html__( 'Photo Gallery (Seeko)', 'seeko' );
	}

	public function get_icon() {
		return 'eicon-gallery-justified';
	}

	public function get_categories() {
		return [ 'seeko-elements' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_sko_text_block',
			[
				'label' => esc_html__( 'Text Block', 'seeko' ),
			]
		);

		$this->add_control(
			'position',
			[
				'label'   => esc_html__( 'Position', 'seeko' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'seeko' ),
					'right' => esc_html__( 'Right', 'seeko' )
				]
			]
		);
		$this->add_control(
			'title',
			[
				'label'   => __( 'Title', 'seeko' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'label'   => __( 'Text Typography', 'seeko' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .sko-photo-gallery h3',
			]
		);
		$this->add_control(
			'text',
			[
				'label'   => __( 'Text', 'seeko' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sko_photo_gallery',
			[
				'label' => esc_html__( 'Image Gallery (max 6)', 'seeko' ),
			]
		);



		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'      => __( 'Add Image', 'elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_label' => false,
				'dynamic'    => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'seeko' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder'     => 'Image caption',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link_to',
			[
				'label' => __( 'Link to', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'file',
				'options' => [
					'file' => __( 'Media File', 'elementor' ),
					'custom' => __( 'Custom links', 'seeko' ),
					'none' => __( 'None', 'elementor' ),
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'open_lightbox',
			[
				'label' => __( 'Open Lightbox', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'elementor' ),
					'yes' => __( 'Yes', 'elementor' ),
					'no' => __( 'No', 'elementor' ),
				],
				'label_block' => true,
				'condition' => [
					'link_to' => 'file',
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'seeko' ),
				'type'        => Controls_Manager::URL,
				//'default'     => '',
				'condition' => [
					'link_to' => 'custom',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'images',
			[
				'label'       => __( 'Images', 'seeko' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'title'   => '',
					],

				],
			]
		);

		$this->end_controls_section();

	}

	protected function get_image_link( $index ) {
		$settings = $this->get_settings();
		if ( $settings['links'] !== '' ) {
			$links = explode( "\n", $settings['links'] );
			if ( isset( $links[ $index -1 ] ) ) {
				return $links[ $index -1 ];
			}
		}

		return '#';
	}

	protected function render() {

		$settings = $this->get_settings();

		$items = $this->get_settings_for_display( 'images' );

		$count = count( (array) $settings['images'] );

		if ( $settings['title'] != '' || $settings['text'] != '' ) {
			$count ++;
		}

		if ( $count > 7 ) {
			$count = 7;
		}

		$classes = 'sko-tpl-' . $count;
		if ( $settings['position'] == 'right' ) {
			$classes .= ' sko-tpl-reverse';
		}

		?>
		<div class="sko-photo-gallery <?php echo esc_attr( $classes ); ?> ">

			<?php if ( $settings['title'] != '' || $settings['text'] != '' ) : ?>
				<div class="sko-item">
					<div class="sko-gallery-desc">

						<?php if ( $settings['title'] != '' ) : ?>

							<h3 class="h2"><?php echo wp_kses_post( $settings['title'] ); ?></h3>

						<?php endif; ?>

						<?php if ( $settings['title'] != '' ) : ?>

							<div><?php echo wp_kses_post( $settings['text'] ); ?></div>

						<?php endif; ?>

					</div>

				</div>
			<?php endif; ?>

			<?php
			$img_count = 0;
			$max_images = 7;
			if ( $settings['title'] != '' || $settings['text'] != '' ) {
				$max_images = 6;
			}

			if ( is_array( $items ) && count( $items ) > 0 ) {

				$this->add_render_attribute( 'link', [
					'class' => 'img-card',
				] );

				foreach ( $items as $index => $item ) {
					$img_count++;

					if ( $item['link_to'] === 'custom' ) {
						if( isset( $item['link']['url'] ) ) {
							$custom_link = str_replace('##site_url##', home_url( '/' ), $item['link']['url'] );
							if( function_exists( 'bp_is_active' ) ) {
								$custom_link = str_replace('##bp_members_search##', bp_get_members_directory_permalink() . '?members_search=', $custom_link );
								if (bp_is_active('groups')) {
									$custom_link = str_replace('##bp_groups_search##', bp_get_groups_directory_permalink() . '?groups_search=', $custom_link );
								}
							}

							$this->add_render_attribute( 'link' . $img_count, [
								'href' => esc_attr( $custom_link ),
							] );
						}

					} elseif ( $item['link_to'] === 'file' ) {

						$this->add_render_attribute( 'link' . $img_count, [
							'data-elementor-open-lightbox' => esc_attr( $item['open_lightbox'] ),
							'data-elementor-lightbox-slideshow' => esc_attr( $this->get_id() ),
							'href' => esc_url( $item['image']['url'] ),
						] );
					}

					if ( $img_count <= $max_images ) {
						?>
						<div class="sko-item">
							<figure class="img-dynamic aspect-ratio img-round">
								<a <?php $this->print_render_attribute_string( 'link' ); ?>
									<?php $this->print_render_attribute_string( 'link' . $img_count ); ?>>
									<?php echo wp_get_attachment_image( $item['image']['id'], 'large' ); ?>
								</a>
							</figure>

							<div class="sko-item-caption">
								<?php
									if ( $item['title'] ) {
										echo '<h4>';
										echo wp_kses_post( $item['title'] );
										echo '</h4>';
									}

									?>
							</div>
						</div>
						<?php
					}
				}
			}

			?>
		</div>

		<?php
	}

}
