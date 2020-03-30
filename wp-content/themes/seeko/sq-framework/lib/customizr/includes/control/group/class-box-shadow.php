<?php
/**
 * Handles box shadow control class.
 *
 * @package Seeko\Customizer
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Box_Shadow control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Group_Control_Box_Shadow extends Seeko_Customizer_Base_Group_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-box-shadow';

	/**
	 * Set the fields for this control.
	 *
	 * @since 1.0.0
	 */
	protected function set_fields() {
		$this->add_field( 'horizontal', [
			'type'       => 'seeko-text',
			'inputType'  => 'number',
			'label'      => esc_html__( 'Horizontal', 'seeko' ),
			'inputAttrs' => [ 'placeholder' => 0 ],
			'column'     => '3',
		] );

		$this->add_field( 'vertical', [
			'type'       => 'seeko-text',
			'inputType'  => 'number',
			'label'      => esc_html__( 'Vertical', 'seeko' ),
			'inputAttrs' => [ 'placeholder' => 0 ],
			'column'     => '3',
		] );

		$this->add_field( 'blur', [
			'type'       => 'seeko-text',
			'inputType'  => 'number',
			'label'      => esc_html__( 'Blur', 'seeko' ),
			'inputAttrs' => [ 'placeholder' => 0 ],
			'column'     => '3',
		] );

		$this->add_field( 'spread', [
			'type'       => 'seeko-text',
			'inputType'  => 'number',
			'label'      => esc_html__( 'Spread', 'seeko' ),
			'inputAttrs' => [ 'placeholder' => 0 ],
			'column'     => '3',
		] );

		$this->add_field( 'position', [
			'type'    => 'seeko-choose',
			'column'  => '4',
			'label'   => esc_html__( 'Position', 'seeko' ),
			'default' => '',
			'choices' => [
				'' => [
					'label' => esc_html__( 'Linear', 'seeko' ),
				],
				'inset' => [
					'label' => esc_html__( 'Inset', 'seeko' ),
				],
			],
		] );

		$this->add_field( 'color', [
			'type'   => 'seeko-color',
			'label'  => esc_html__( 'Color', 'seeko' ),
			'column' => '8',
		] );
	}

	/**
	 * Format theme mod array value into a valid box shadow value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value The field's value.
	 *
	 * @return string The formatted box shadow value.
	 */
	public static function format_value( $value ) {
		$value = array_merge(
			[
				'horizontal' => 0,
				'vertical'   => 0,
				'blur'       => 0,
				'spread'     => 0,
				'color'      => '#0000',
				'position'   => '',
				'unit'       => 'px',
			],
			$value
		);

		$value = sprintf(
			'%1$s%7$s %2$s%7$s %3$s%7$s %4$s%7$s %5$s %6$s',
			$value['horizontal'],
			$value['vertical'],
			$value['blur'],
			$value['spread'],
			$value['color'],
			$value['position'],
			$value['unit']
		);

		return $value;
	}
}
