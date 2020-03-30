<?php
/**
 * Handles multicheck control class.
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
 * Multicheck control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Multicheck extends Seeko_Customizer_Base_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-multicheck';

	/**
	 * Choices via icons.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $icon_choices = [];

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['iconChoices'] = $this->icon_choices;
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
		<div class="seeko-control seeko-multicheck-control">
			<# if ( ! _.isEmpty( data.iconChoices ) ) { #>
				<div class="seeko-multicheck-control-icon-items">
					<# _.each( data.iconChoices, function( icon, key ) { #>
						<div class="seeko-multicheck-control-icon-item">
							<input class="seeko-multicheck-control-checkbox" {{{ data.inputAttrs }}} type="checkbox" value="{{ key }}" id="{{ data.id }}-{{ key }}" <# if ( data.value.indexOf( key ) >= 0 ) { #> checked <# } #>>
							<label class="seeko-multicheck-control-icon-label" for="{{ data.id }}-{{ key }}"><img src="<?php echo esc_url( Seeko_Customizer_Utils::get_assets_url() ); ?>/img/{{ icon }}.svg" /></label>
						</div>
					<# } ) #>
				</div>
			<# } #>
			<# if ( ! _.isEmpty( data.choices ) ) { #>
				<div class="seeko-multicheck-control-items">
					<# _.each( data.choices, function( label, key ) { #>
						<div class="seeko-multicheck-control-item">
							<input class="seeko-multicheck-control-checkbox" {{{ data.inputAttrs }}} type="checkbox" value="{{ key }}" id="{{ data.id }}-{{ key }}" <# if ( data.value.indexOf( key ) >= 0 ) { #> checked <# } #>>
							<label class="seeko-multicheck-control-label" for="{{ data.id }}-{{ key }}"><span class="seeko-multicheck-control-box"></span> {{ label }}</label>
						</div>
					<# } ) #>
				</div>
			<# } #>
			<input type="hidden" value="{{ data.value }}" {{{ data.link }}}>
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
	 * @return array The formatted properties.
	 */
	public static function format_properties( $value ) {
		$vars = [];

		foreach ( $value as $key ) {
			$vars[ $key ] = 'true';
		}

		return $vars;
	}
}
