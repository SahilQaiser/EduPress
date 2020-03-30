<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor counter widget.
 *
 * Elementor widget that displays stats and numbers in an escalating manner.
 *
 * @since 1.0.0
 */
class SeekoBpCounter extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve counter widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'counterbp';
	}

	public function get_categories() {
		return [ 'seeko-elements' ];
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve counter widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'BP Counter', 'seeko' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve counter widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'jquery-numerator' ];
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
		return [ 'counter' ];
	}

	public function get_fields() {
		return svq_bp_fields_array();
	}
	
	/**
	 * Register counter widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_counter',
			[
				'label' => esc_html__( 'Members Counter', 'seeko' ),
			]
		);

		$this->add_control(
			'field_id',
			[
				'label' => esc_html__( 'Field Name', 'seeko' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_fields(),
				'description' => esc_html__( 'Select the field to get statistics by', 'seeko' ),
			]
		);

		$this->add_control(
			'value',
			[
				'label' => __( 'Field Value', 'seeko' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => esc_html__( 'Value to get same members by. Example: Rome if the Field name is City .', 'seeko' ),
			]
		);

		$this->add_control(
			'online',
			[
				'label' => esc_html__( 'Get online users only', 'seeko' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'seeko' ),
				'label_on' => esc_html__( 'On', 'seeko' ),
				'default' => '',
				'return_value' => '1',
				'description' => esc_html__( 'Enable to count online users only', 'seeko' ),
			]
		);

		$this->add_control(
			'starting_number',
			[
				'label' => esc_html__( 'Starting Number', 'seeko' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$this->add_control(
			'prefix',
			[
				'label' => esc_html__( 'Number Prefix', 'seeko' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => 1,
			]
		);

		$this->add_control(
			'text_prefix',
			[
				'label' => esc_html__( 'Text Prefix', 'seeko' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => 1,
			]
		);

		$this->add_control(
			'suffix',
			[
				'label' => esc_html__( 'Number Suffix', 'seeko' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Plus', 'seeko' ),
			]
		);

		$this->add_control(
			'duration',
			[
				'label' => esc_html__( 'Animation Duration', 'seeko' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 2000,
				'min' => 100,
				'step' => 100,
			]
		);

		$this->add_control(
			'thousand_separator',
			[
				'label' => __( 'Thousand Separator', 'seeko' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'seeko' ),
				'label_off' => __( 'Hide', 'seeko' ),
			]
		);

		$this->add_control(
			'thousand_separator_char',
			[
				'label' => __( 'Separator', 'seeko' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'thousand_separator' => 'yes',
				],
				'options' => [
					'' => 'Default',
					'.' => 'Dot',
					' ' => 'Space',
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'seeko' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Cool Statistic', 'seeko' ),
				'placeholder' => esc_html__( 'Cool Statistic', 'seeko' ),
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'View', 'seeko' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_number',
			[
				'label' => esc_html__( 'Number', 'seeko' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'number_color',
			[
				'label' => esc_html__( 'Text Color', 'seeko' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-counter-number-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_number',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-counter-number-wrapper',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'seeko' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'seeko' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-counter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
				'selector' => '{{WRAPPER}} .elementor-counter-title',
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Render counter widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		$online = isset( $settings['online'] ) && $settings['online'] == 1 ? true : false;

		if ( ! function_exists( 'bp_is_active' ) ) {
			echo esc_html__( 'BuddyPress needs to be installed', 'seeko' );
			return;
		}

		if ( $online ) {
			$ending_number = bp_get_online_users( $settings['value'], $settings['field_id'] );
		} else {
			if ( $settings['field_id'] && $settings['value'] ) {
				$ending_number = sq_bp_member_stats( $settings['field_id'], $settings['value'] );
			} else {
				//get total member count
				$ending_number = bp_get_total_member_count();
			}
		}

		if ((int) $settings['prefix'] > 0 ) {
			$ending_number = (int) $settings['prefix'] . $ending_number;
		}

		$this->add_render_attribute( 'counter', [
			'class' => 'elementor-counter-number',
			'data-duration' => $settings['duration'],
			'data-to-value' => $ending_number,
		] );

		if ( ! empty( $settings['thousand_separator'] ) ) {
			$delimiter = empty( $settings['thousand_separator_char'] ) ? ',' : $settings['thousand_separator_char'];
			$this->add_render_attribute( 'counter', 'data-delimiter', $delimiter );
		}
		?>
		<div class="elementor-element elementor-widget" data-element_type="counter.default">
			<div class="elementor-counter">
				<div class="elementor-counter-number-wrapper h1">
					<span class="elementor-counter-number-prefix"><?php echo wp_kses_post( $settings['text_prefix'] ); ?></span>
					<span <?php echo $this->get_render_attribute_string( 'counter' ); ?>>
						<?php echo wp_kses_post( $settings['starting_number'] ); ?>
					</span>
					<span class="elementor-counter-number-suffix"><?php echo wp_kses_post( $settings['suffix'] ); ?></span>
				</div>
				<?php if ( $settings['title'] ) : ?>
					<div class="elementor-counter-title"><?php echo wp_kses_post( $settings['title'] ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
