<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class SeekoTabs extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve tabs widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'seeko-tabs';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve tabs widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tabs (Seeko)', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve tabs widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'tabs' ];
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the button widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'seeko-elements', 'general' ];
	}


	/**
	 * Register tabs widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Tabs', 'elementor' ),
			]
		);

		$this->add_control(
			'title_align',
			[
				'label'       => __( 'Title align', 'seeko' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'left',
				'options'     => [
					'left'           => __( 'Left', 'seeko' ),
					'center'      => __( 'Center', 'seeko' ),
					'right' => __( 'Right', 'seeko' ),
					'full' => __( 'Full', 'seeko' ),
				],
			]
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'icon_type',
			[
				'label'       => __( 'Tab Icon Type', 'seeko' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'label_block' => true,
				'options'     => [
					''           => __( 'No Icon', 'seeko' ),
					'seeko'      => __( 'Seeko Icons', 'seeko' ),
					'seeko_dual' => __( 'Seeko Dual Color Icons', 'seeko' ),
					'fa'         => __( 'Font Awesome', 'seeko' ),
				],
			]
		);

		$repeater->add_control(
			'icon', [
				'label' => __( 'Icon', 'seeko' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'default' => '',
				'label_block' => true,
				'include' => svq_icons_array( 'icon ' ),
				'condition' => [
					'icon_type' => 'seeko',
				],
			]
		);

		$repeater->add_control(
			'icon_dual', [
				'label' => __( 'Icon', 'seeko' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'default' => '',
				'label_block' => true,
				'include' => sks_get_dual_icons_array( 'icon ', 'icon '),
				'condition' => [
					'icon_type' => 'seeko_dual',
				],
			]
		);

		$repeater->add_control(
			'icon_fa', [
				'label' => __( 'Icon', 'seeko' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'default' => '',
				'include' => array_flip( \Elementor\Control_Icon::get_icons() ),
				'label_block' => true,
				'condition' => [
					'icon_type' => 'fa',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$repeater->add_control(
			'image_align',
			[
				'label'       => __( 'Image align', 'seeko' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'left',
				'options'     => [
					'left'           => __( 'Left', 'seeko' ),
					'right' => __( 'Right', 'seeko' ),
				],
				'condition' => [
					'image!' => '',
				],
			]
		);

		$repeater->add_control(
			'tab_title',
			[
				'label'       => __( 'Title & Content', 'elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Tab Title', 'elementor' ),
				'placeholder' => __( 'Tab Title', 'elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label'       => __( 'Content', 'elementor' ),
				'default'     => __( 'Tab Content', 'elementor' ),
				'placeholder' => __( 'Tab Content', 'elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'show_label'  => false,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label'       => __( 'Tabs Items', 'elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'icon_type'   => '',
						'tab_title'   => __( 'Tab #1', 'elementor' ),
						'tab_content' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
					],
					[
						'icon_type'   => '',
						'tab_title'   => __( 'Tab #2', 'elementor' ),
						'tab_content' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => __( 'Tabs', 'elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sko-nav-tabs' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sko-nav-tabs li > a .sko-item' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sko-tab-content'               => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label'     => __( 'Title', 'elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label'     => __( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sko-nav-tabs .sko-item .sko-icon .icon:not(.sko-icon-hover), ' .
					'.sko-nav-tabs .sko-item .sko-title' => 'color: {{VALUE}};',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label'     => __( 'Active Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sko-nav-tabs li > a:hover .sko-item .sko-title, ' .
					'{{WRAPPER}} .sko-nav-tabs li > a:hover .sko-item .sko-icon-hover, ' .
					'{{WRAPPER}} .sko-nav-tabs li > a.active .sko-item .sko-title, ' .
					'{{WRAPPER}} .sko-nav-tabs li > a.active .sko-item .sko-icon-hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sko-nav-tabs li > a.active .sko-item' => 'border-color: {{VALUE}};',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
			]
		);

		$this->add_control(
			'tab_inactive_color',
			[
				'label'     => __( 'Inactive Icon Color', 'seeko' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sko-nav-tabs .sko-item .sko-icon .sko-icon-hover' => 'color: {{VALUE}};',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .sko-nav-tabs li > a .sko-item, {{WRAPPER}} .sko-nav-tabs li > a .sko-item h3',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label'     => __( 'Content', 'elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => __( 'Color', 'elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sko-tab-content .tab-pane' => 'color: {{VALUE}};',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .sko-tab-content .tab-pane',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$tabs = $this->get_settings_for_display( 'tabs' );
		$settings = $this->get_settings();

		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div class="seeko-elementor-tabs" role="tablist">
			<ul class="nav list-unstyled sko-nav-tabs sko-tabs-<?php echo esc_attr( $settings['title_align'] ); ?>">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

					$this->add_render_attribute( $tab_title_setting_key, [
						'id'            => 'seeko-tab-' . $id_int . $tab_count,
						'class'         => [ 'seeko-tab-title' ],
						'href'          => '#tab-content-' . $id_int . $tab_count,
						'data-toggle'   => 'tab',
						'role'          => 'tab',
						'aria-controls' => 'tab-content-' . $id_int . $tab_count,
					] );
					if ( $tab_count == 1 ) {
						$this->add_render_attribute( $tab_title_setting_key, [
							'class'         => [ 'active' ],
							'aria-selected' => 'true',
						] );
					}

					$icon = '';
					if ($item['icon_type'] == 'seeko') {
						$icon = '<i class="' .esc_attr( $item['icon'] ) . ' icon-lg"></i>';
					} elseif( $item['icon_type'] == 'fa' ) {
						$icon = '<i class="' .esc_attr( $item['icon_fa'] ) . ' icon-lg"></i>';
					} elseif( $item['icon_type'] == 'seeko_dual' ) {
						$icon = sks_generate_dual_icon( $item['icon_dual'], 'icon-lg' );
					}
					?>
					<li class="nav-item">
						<a <?php $this->print_render_attribute_string( $tab_title_setting_key ); ?>>
							<span class="sko-item">
								<span class="sko-icon"><?php echo $icon; ?></span>
								 <span class="h3 sko-title">
									<?php echo wp_kses_post( $item['tab_title'] ); ?>
								 </span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content sko-tab-content">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

					$this->add_render_attribute( $tab_content_setting_key, [
						'class'           => [ 'col' ],
					] );

					$this->add_render_attribute( $tab_content_setting_key . '_wrap', [
						'id'              => 'tab-content-' . $id_int . $tab_count,
						'class'           => [ 'tab-pane', 'fade' ],
						'role'            => 'tabpanel',
						'aria-labelledby' => 'seeko-tab-' . $id_int . $tab_count,
					] );

					if ( $tab_count === 1 ) {
						$this->add_render_attribute(  $tab_content_setting_key . '_wrap', [
							'class' => [ 'show', 'active' ],
						] );
					}

					$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
					?>
					<div <?php $this->print_render_attribute_string(  $tab_content_setting_key . '_wrap' ); ?>>
						<div class="row">
							<?php if ( isset($item['image']) && $item['image']['url'] !== '' && $item['image_align'] == 'left' ) : ?>
								<div class="col-md"><?php echo Group_Control_Image_Size::get_attachment_image_html( $item ); ?></div>
							<?php endif; ?>

							<div <?php $this->print_render_attribute_string( $tab_content_setting_key ); ?>>
								<?php echo $this->parse_text_editor( $item['tab_content'] ); /* WPCS: XSS OK. */ ?>
							</div>

							<?php if ( isset($item['image']) && $item['image']['url'] !== '' && $item['image_align'] == 'right' ) : ?>
								<div class="col-md"><?php echo Group_Control_Image_Size::get_attachment_image_html( $item ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
