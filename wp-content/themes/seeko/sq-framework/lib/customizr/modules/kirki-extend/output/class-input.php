<?php
/**
 * Handles Input control css output.
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
 * Overrides Kirki CSS output.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Kirki_Extend_Output_Input extends Seeko_Customizer_Kirki_Extend_Base_Output {

	/**
	 * CSS output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $output         Defined single output.
	 * @param array $filtered_value Filtered settings value.
	 */
	protected function apply_output( $output, $filtered_value ) {

		$output = array_merge(
			[
				'element'     => '',
				'property'    => '',
				'media_query' => 'global',
				'prefix'      => '',
				'suffix'      => '',
			],
			$output
		);

		if ( ! is_array( $filtered_value ) ) {
			return;
		}

		$value = array_merge(
			[
				'size' => '',
				'unit' => '',
			],
			$filtered_value
		);

		$css_value = self::format_value( $value );

		$this->styles[ $output['media_query'] ][ $output['element'] ][ $output['property'] ] = $output['prefix'] . $this->apply_value_pattern( $output, $css_value ) . $output['suffix'];
	}

	/**
	 * Format theme mod array value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value The field's value.
	 *
	 * @return string The formatted value.
	 */
	public static function format_value( $value ) {

		if ( ! isset( $value['size'] ) || '' === $value['size'] || ! isset( $value['unit'] ) ) {
			return '';
		}

		$unit = '-' !== $value['unit'] ? $value['unit'] : '';

		$css_value = $value['size'] . $unit;

		return $css_value;
	}
}
