<?php
/**
 * Handles box model control class.
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
 * Box model control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Box_Model extends Seeko_Customizer_Base_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-box-model';

	/**
	 * Control's exclude box model parts.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $exclude = [];

	/**
	 * Control's unit.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $units = [
		'px',
		'%',
		'em',
		'rem',
	];

	/**
	 * Control's global default unit.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $default_unit = 'rem';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['exclude']      = $this->exclude;
		$this->json['units']        = $this->units;
		$this->json['default_unit'] = self::$default_unit;
	}

	/**
	 * An Underscore (JS) template for control wrapper.
	 *
	 * Use to create the control template.
	 *
	 * @since 1.0.0
	 */
	protected function control_template() {

		?>
		<#
		sides = [
			'top',
			'right',
			'bottom',
			'left'
		]
		properties = {
			margin: {
				title: '<?php esc_attr_e( 'Margin', 'seeko' ); ?>',
				min: -1000,
			},
			padding: {
				title: '<?php esc_attr_e( 'Padding', 'seeko' ); ?>',
				min: 0,
			}
		}
		units = data.units
		selectorClass = _.isArray( units ) && 1 === _.size( units ) ? 'disabled' : ''
		#>
		<div class="seeko-control seeko-box-model-control">
			<# _.each( properties, function ( props, key ) { #>
				<# if ( data.exclude.indexOf( key ) < 0 ) { #>
					<div class="seeko-box-model-control-property">
						<span class="seeko-box-model-control-title">{{ props.title }}</span>
						<# _.each( sides, function ( side ) {
							propertyName = key + '_' + side
							value = ! _.isUndefined( data.value[ propertyName ] ) ? data.value[ propertyName ] : '' #>
							<input class="seeko-box-model-control-input seeko-box-model-control-{{ side }}" min="{{ props.min }}" {{{ data.inputAttrs }}} type="text" value="{{ value }}" placeholder="-" {{{ data.link }}} data-setting-property-link="{{ propertyName }}" />
						<# } ) #>
				<# } #>
			<# } ) #>
			<# _.each( properties, function ( title, key ) { #>
				<# if ( data.exclude.indexOf( key ) < 0 ) { #>
					</div>
				<# } #>
			<# } ) #>
			<div class="seeko-unit-selector-wrapper">
				<# _.each( properties, function ( props, key ) { #>
					<# if ( data.exclude.indexOf( key ) < 0 ) { #>
						<# unitValue = _.isEmpty( data.value[ key + '_unit' ] ) ?  data.default_unit : data.value[ key + '_unit' ] #>
						<div class="seeko-control-units-container">
							<input type="hidden" value="{{ unitValue }}" {{{ data.link }}} data-setting-property-link="{{key + '_unit'}}" />
							<span class="seeko-unit-selector-label">{{key}}</span>
							<ul class="seeko-control-unit-selector">
								<li class="seeko-control-unit selected-unit {{ selectorClass }}">{{ unitValue }}</li>
								<# _.each( units, function ( unit ) { #>
									<li class="seeko-control-unit">{{ unit }}</li>
								<# } ) #>
							</ul>
						</div>
					<# } #>
				<# } ) #>
			</div>
		</div>
		<?php
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
	 */
	public static function format_properties( $value, $args ) {
		$args = array_merge( [ 'exclude' => [] ], $args );

		$positions = [ 'top', 'right', 'bottom', 'left' ];

		$vars = [];

		if ( ! in_array( 'margin', $args['exclude'], true ) ) {
			$margin_unit = isset( $value['margin_unit'] ) ? $value['margin_unit'] : self::$default_unit;

			foreach ( $positions as $position ) {
				// Accepts non-numeric value such as 'auto'.
				if ( array_key_exists( 'margin_' . $position, $value ) ) {
					$property_value = $value[ 'margin_' . $position ];
					$unit           = is_numeric( $property_value ) && 0 !== $property_value ? $margin_unit : '';
					$position       = seeko_get_direction( $position );

					$vars[ 'margin-' . $position ] = $property_value . $unit;
				}
			}
		}

		if ( ! in_array( 'padding', $args['exclude'], true ) ) {
			$padding_unit = isset( $value['padding_unit'] ) ? $value['padding_unit'] : self::$default_unit;

			foreach ( $positions as $position ) {
				// Does not accept any value that is not numeric.
				if ( array_key_exists( 'padding_' . $position, $value ) && is_numeric( $value[ 'padding_' . $position ] ) ) {
					$property_value = $value[ 'padding_' . $position ];
					$unit           = 0 !== $property_value ? $padding_unit : '';
					$position       = seeko_get_direction( $position );

					$vars[ 'padding-' . $position ] = $property_value . $unit;
				}
			}
		}

		return $vars;
	}
}
