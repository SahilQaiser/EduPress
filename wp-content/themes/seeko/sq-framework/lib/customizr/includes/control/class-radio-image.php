<?php
/**
 * Handles radio image control class.
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
 * Radio image control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Radio_Image extends Seeko_Customizer_Base_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-radio-image';

	/**
	 * An Underscore (JS) template for control wrapper.
	 *
	 * Use to create the control template.
	 *
	 * @since 1.0.0
	 */
	protected function control_template() {
		?>
		<div class="seeko-control seeko-radio-image-control">
			<div class="seeko-radio-image-control-buttons">
				<# _.each( data.choices, function( image, key ) { #>
					<input class="seeko-radio-image-control-radio" {{{ data.inputAttrs }}} type="radio" value="{{ key }}" name="{{ data.id }}" id="{{ data.id }}-{{ key }}" {{{ data.link }}} <# if ( key === data.value ) { #> checked <# } #>>
					<label class="seeko-radio-image-control-button" for="{{ data.id }}-{{ key }}"><img alt="{{ image.alt }}" title="{{ image.alt }}" src="{{ image.img }}" /></label>
				<# } ) #>
			</div>
		</div>
		<?php
	}
}
