<?php
/**
 * Handles border control class.
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
 * Border control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Group_Control_Border extends Seeko_Customizer_Base_Group_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-border';

	/**
	 * Set the fields for this control.
	 *
	 * @since 1.0.0
	 */
	protected function set_fields() {
		$this->add_field( 'style', [
			'type'    => 'seeko-select',
			'icon'    => 'border-style',
			'unit'    => 'px',
			'column'  => '5',
			'default' => 'solid',
			'choices' => [
				'dashed' => esc_html__( 'Dashed', 'seeko' ),
				'dotted' => esc_html__( 'Dotted', 'seeko' ),
				'solid'  => esc_html__( 'Solid', 'seeko' ),
			],
		] );

		$this->add_field( 'size', [
			'type'   => 'seeko-input',
			'icon'   => 'border-size',
			'units'  => [ 'px', '%', 'em', 'rem' ],
			'column' => '4',
		] );

		$this->add_field( 'width', [
			'type'       => 'seeko-input',
			'icon'       => 'border',
			'units'      => [ 'px' ],
			'column'     => '5',
			'responsive' => true,
		] );

		$this->add_field( 'radius', [
			'type'   => 'seeko-input',
			'icon'   => 'corner-radius',
			'units'  => [ 'px', '%' ],
			'column' => '4',
		] );

		$this->add_field( 'color', [
			'type'   => 'seeko-color',
			'icon'   => 'border-color',
			'column' => '3',
		] );
	}

	/**
	 * Format CSS value from theme mod array value.
	 *
	 * Add unit to border width.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value The field's value.
	 * @param array $args The field's arguments.
	 *
	 * @return array The formatted properties.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function format_properties( $value, $args ) {
		$with_unit = [ 'width', 'radius', 'size' ];

		foreach ( $with_unit as $property ) {
			if ( isset( $value[ $property ] ) && ! empty( $value[ $property ] ) ) {
				$value[ $property ] = Seeko_Customizer_Control_Input::format_value( $value[ $property ] );
			}
		}

		return $value;
	}
}
