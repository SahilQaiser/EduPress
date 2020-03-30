<?php
/**
 * Handles background control class.
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
 * Background control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Group_Control_Background extends Seeko_Customizer_Base_Group_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-background';

	/**
	 * Set the fields for this control.
	 *
	 * @since 1.0.0
	 */
	protected function set_fields() {
		$this->add_field( 'label', [
			'type'  => 'seeko-label',
			'label' => esc_html__( 'Background Color Type', 'seeko' ),
		] );

		$this->add_field( 'type', [
			'type'     => 'seeko-choose',
			'column'   => '4',
			'default'  => 'classic',
			'choices'  => [
				'classic'  => [
					'icon' => 'background-type-color',
				],
				'gradient' => [
					'icon' => 'background-type-gradient',
				],
			],
		] );

		$this->add_field( 'color', [
			'type'     => 'color',
			'column'   => '12',
			'icon'     => 'background-color',
			'cssClass' => 'for-classic',
			'responsive' => true,
		] );

		$this->add_field( 'image', [
			'type'     => 'seeko-image',
			'column'   => '6',
			'label'    => esc_html__( 'BG Image', 'seeko' ),
			'cssClass' => 'for-classic',
		] );

		$this->add_field( 'position', [
			'type'       => 'seeko-position',
			'column'     => '6',
			'label'      => esc_html__( 'Position', 'seeko' ),
			'cssClass'   => 'for-classic',
		] );

		$this->add_field( 'repeat', [
			'type'       => 'seeko-choose',
			'column'     => '6',
			'label'      => esc_html__( 'Repeat', 'seeko' ),
			'default'    => 'no-repeat',
			'choices'    => [
				'repeat' => [
					'icon' => 'repeat',
				],
				'repeat-x' => [
					'icon' => 'repeat-x',
				],
				'repeat-y' => [
					'icon' => 'repeat-y',
				],
				'no-repeat' => [
					'icon' => 'x',
				],
			],
			'cssClass'   => 'for-classic',
		] );

		$this->add_field( 'attachment', [
			'type'     => 'seeko-choose',
			'column'   => '6',
			'text'     => esc_html__( 'Fixed', 'seeko' ),
			'default'  => 'scroll',
			'choices'  => [
				'fixed' => [
					'icon' => 'check',
				],
				'scroll' => [
					'icon' => 'x',
				],
			],
			'cssClass' => 'for-classic',
		] );

		$this->add_field( 'size', [
			'type'     => 'seeko-choose',
			'column'   => '6',
			'text'     => esc_html__( 'Cover', 'seeko' ),
			'default'  => 'auto',
			'choices'  => [
				'cover' => [
					'icon' => 'check',
				],
				'auto' => [
					'icon' => 'x',
				],
			],
			'cssClass' => 'for-classic',
		] );

		$this->add_field( 'divider', [
			'type'        => 'seeko-divider',
			'dividerType' => 'empty',
			'cssClass'    => 'for-gradient seeko-divider-control-empty',
		] );

		$gradient_colors = [
			'color_from' => [
				'type'     => 'color',
				'column'   => '6',
				'icon'     => is_rtl() ? 'direction-arrow' : 'background-color',
				'cssClass' => 'for-gradient',
			],
			'color_to' => [
				'type'     => 'color',
				'column'   => '6',
				'icon'     => is_rtl() ? 'background-color' : 'direction-arrow',
				'cssClass' => 'for-gradient',
			],
		];

		if ( is_rtl() ) {
			$gradient_colors = array_reverse( $gradient_colors );
		}

		foreach ( $gradient_colors as $key => $value ) {
			$this->add_field( $key, $value );
		}

		$this->add_field( 'gradient_type', [
			'type'    => 'seeko-choose',
			'column'  => '6',
			'default' => 'linear',
			'choices' => [
				'linear' => [
					'label' => esc_html__( 'Linear', 'seeko' ),
				],
				'radial' => [
					'label' => esc_html__( 'Radial', 'seeko' ),
				],
			],
			'cssClass' => 'for-gradient',
		] );

		$this->add_field( 'angle', [
			'type'       => 'seeko-text',
			'inputType'  => 'number',
			'inputAttrs' => [
				'placeholder' => 90,
			],
			'icon'       => 'angle',
			'column'     => '6',
			'default'    => 90,
			'cssClass'   => 'for-gradient',
		] );
	}

	/**
	 * Include fields.
	 *
	 * @since 1.0.0
	 */
	protected function include_fields() {
		if ( empty( $this->include ) ) {
			return;
		}

		$include = $this->include;

		if ( in_array( 'video', $include, true ) ) {
			$this->update_field( 'type', [
				'choices'  => [
					'classic'  => [
						'icon' => 'background-type-color',
					],
					'gradient' => [
						'icon' => 'background-type-gradient',
					],
					'video' => [
						'icon' => 'background-type-video',
					],
				],
			] );

			$this->add_field( 'video_divider', [
				'type'        => 'seeko-divider',
				'dividerType' => 'empty',
				'cssClass'    => 'for-video',
			] );

			$this->add_field( 'video_link', [
				'type'       => 'seeko-text',
				'column'     => '8',
				'inputType'  => 'url',
				'inputAttrs' => [
					'placeholder' => esc_html__( 'Social or Self hosted video link', 'seeko' ),
				],
				'label'      => esc_html__( 'Video Link', 'seeko' ),
				'cssClass'   => 'for-video',
			] );

			$this->add_field( 'video_fallback', [
				'type'     => 'seeko-image',
				'column'   => '4',
				'label'    => esc_html__( 'Video Fallback', 'seeko' ),
				'cssClass' => 'for-video',
			] );
		}
	}

	/**
	 * Exclude fields.
	 *
	 * @since 1.0.0
	 */
	protected function exclude_fields() {
		if ( ! empty( $this->exclude ) && in_array( 'image', $this->exclude, true ) ) {
			$this->exclude = [
				'image',
				'position',
				'repeat',
				'attachment',
				'size',
			];
		}

		parent::exclude_fields();
	}

	/**
	 * Format CSS value from theme mod array value.
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
		$value = wp_parse_args(
			$value,
			[
				'type'       => 'classic',
				'position'   => 'top left',
				'repeat'     => 'no-repeat',
				'attachment' => 'scroll',
				'size'       => 'auto',
			]
		);

		if ( isset( $value['image'] ) && ! empty( $value['image'] ) ) {
			$value['image'] = "url({$value['image']})";
		}

		return $value;
	}

	/**
	 * Format theme mod array value into a valid background value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value The field's value.
	 *
	 * @return string The formatted background value.
	 */
	public static function format_value( $value ) {
		$value = array_merge(
			[
				'type'          => 'classic',
				'color'         => '',
				'image'         => '',
				'repeat'        => 'no-repeat',
				'attachment'    => '',
				'position'      => 'top left',
				'gradient_type' => 'linear',
				'angle'         => '90',
				'color_from'    => 'transparent',
				'color_to'      => 'transparent',
			],
			$value
		);

		if ( 'classic' === $value['type'] ) {
			if ( ! empty( $value['image'] ) ) {
				return sprintf(
					'%1$s %2$s %3$s %4$s %5$s',
					'url(' . $value['image'] . ')',
					$value['color'],
					$value['attachment'],
					$value['repeat'],
					seeko_get_direction( $value['position'] )
				);
			}

			if ( ! empty( $value['color'] ) ) {
				return $value['color'];
			}
		}

		if ( 'gradient' === $value['type'] ) {
			if ( ! is_numeric( $value['angle'] ) ) {
				$value['angle'] = '90';
			}

			$gradient = 'radial' === $value['gradient_type'] ? sprintf( 'radial-gradient(%1$s, %2$s)', $value['color_from'], $value['color_to'] ) : sprintf( 'linear-gradient(%1$sdeg, %2$s, %3$s)', $value['angle'], $value['color_from'], $value['color_to'] );

			return $gradient;
		}
	}


}
