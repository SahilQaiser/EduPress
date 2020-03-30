<?php
/**
 * Handles choose control class.
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
 * Choose control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Choose extends Seeko_Customizer_Base_Input_Group {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-choose';

	/**
	 * Choose multiple.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	public $multiple = false;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		// Convert choices icon to url.
		foreach ( $this->choices as $key => $choice ) {
			if ( isset( $choice['icon'] ) && ! empty( $choice['icon'] ) ) {
				$this->json['choices'][ $key ]['icon'] = $choice['icon'];
			}
		}

		// Create labels for basic associative array.
		foreach ( $this->choices as $key => $label ) {
			if ( ! is_array( $label ) ) {
				$this->json['choices'][ $key ] = [ 'label' => $label ];
			}
		}

		$this->json['multiple'] = $this->multiple;
	}

	/**
	 * An Underscore (JS) template for control field.
	 *
	 * @since 1.0.0
	 */
	protected function group_field_template() {
		?>
		<div class="seeko-control seeko-choose-control">
			<div class="seeko-choose-control-buttons">
				<# if ( data.multiple ) { #>
					<# _.each( data.choices, function( choice, key ) { #>
						<input class="seeko-choose-control-radio" {{{ data.inputAttrs }}} type="checkbox" value="{{ key }}" name="{{ data.id }}" id="{{ data.id }}-{{ key }}" <# if ( data.value.indexOf( key ) >= 0 ) { #> checked <# } #>>
						<label class="seeko-choose-control-button seeko-choose-control-{{ ( choice.icon ) ? 'icon' : 'label' }}" for="{{ data.id }}-{{ key }}"><# if ( choice.icon ) { #><img src="<?php echo esc_url( Seeko_Customizer_Utils::get_assets_url() ); ?>/img/{{ choice.icon }}.svg" /><# } else { #>{{ choice.label }}<# } #></label>
					<# } ) #>
					<input type="hidden" value="{{ data.value }}" {{{ data.link }}}>
				<# } else { #>
					<# _.each( data.choices, function( choice, key ) { #>
						<input class="seeko-choose-control-radio" {{{ data.inputAttrs }}} type="radio" value="{{ key }}" name="{{ data.id }}" id="{{ data.id }}-{{ key }}" {{{ data.link }}} <# if ( key === data.value ) { #> checked <# } #>>
						<label class="seeko-choose-control-button seeko-choose-control-{{ ( choice.icon ) ? 'icon' : 'label' }}" for="{{ data.id }}-{{ key }}"><# if ( choice.icon ) { #><img src="<?php echo esc_url( Seeko_Customizer_Utils::get_assets_url() ); ?>/img/{{ choice.icon }}.svg" /><# } else { #>{{ choice.label }}<# } #></label>
					<# } ) #>
				<# } #>
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
	 *
	 * @return array The formatted value.
	 */
	public static function format_value( $value ) {
		if ( ! is_rtl() ) {
			return $value;
		}

		if ( 'right' === $value ) {
			return 'left';
		}

		if ( 'left' === $value ) {
			return 'right';
		}

		return $value;
	}
}
