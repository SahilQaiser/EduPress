<?php
/**
 * Handles input control class.
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
 * Input control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Input extends Seeko_Customizer_Base_Input_Group {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-input';

	/**
	 * Control's unit.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $units = [
		'-',
		'px',
		'%',
		'vh',
		'vw',
		'em',
		'rem',
	];

	/**
	 * Control's default unit.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $default_unit = '';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['units']       = $this->units;
		$this->json['defaultUnit'] = $this->default_unit;
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
		value = _.isObject( data.value ) ? data.value : {}
		units = ! _.isEmpty( data.units ) ? data.units : [ '-' ]
		hasText = ! _.isUndefined( data.text ) && ! _.isEmpty( data.text )
		hasIcon = ! _.isUndefined( data.icon ) && ! _.isEmpty( data.icon )
		hasUnits = ! _.isUndefined( data.units ) && ! _.isEmpty( data.units )
		controlClass = 'seeko-control ' + data.type + '-control'
		controlClass += ( hasIcon || hasText || hasUnits ) ? ' seeko-input-group' : ''
		controlClass += ( hasIcon ) ? ' has-icon' : ''
		controlClass += ( hasText ) ? ' has-text' : ''
		controlClass += ( hasUnits ) ? ' has-units' : ''
		#>
		<div class="{{ controlClass }}" {{{ data.controlAttrs }}}>
			<?php
			$this->group_prefix_template();
			$this->group_field_template();
			$this->group_units_template();
			?>
		</div>
		<?php
	}

	/**
	 * An Underscore (JS) template for control field.
	 *
	 * @since 1.0.0
	 */
	protected function group_field_template() {
		?>
		<# size = value.size || '' #>
		<input class="seeko-input-control-input" {{{ data.inputAttrs }}} type="text" value="{{ size }}" {{{ data.link }}} data-setting-property-link="size" />
		<?php
	}

	/**
	 * An Underscore (JS) template for control field.
	 *
	 * @since 1.0.0
	 */
	protected function group_units_template() {
		?>
		<#
		defaultUnit = data.defaultUnit ? data.defaultUnit : _.first( units )
		unitValue = value.unit ? value.unit : defaultUnit
		selectorClass = 1 === _.size( units ) ? 'disabled' : ''
		#>
		<div class="seeko-control-units-container">
			<input type="hidden" value="{{ unitValue }}" {{{ data.link }}} data-setting-property-link="unit" />
			<ul class="seeko-control-unit-selector">
				<li class="seeko-control-unit selected-unit {{ selectorClass }}">{{ unitValue }}</li>
				<# _.each( units, function ( unit ) { #>
					<li class="seeko-control-unit">{{ unit }}</li>
				<# } ) #>
			</ul>
		</div>
		<?php
	}

	/**
	 * Format CSS value from theme mod array value.
	 *
	 * @since 1.0.0
	 *
	 * @return array Empty properties
	 */
	public static function format_properties() {
		return [];
	}

}
