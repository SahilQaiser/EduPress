<?php
/**
 * Handles toggle control class.
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
 * Toggle control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Toggle extends Seeko_Customizer_Base_Input_Group {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-toggle';

	/**
	 * An Underscore (JS) template for control field.
	 *
	 * @since 1.0.0
	 */
	protected function group_field_template() {
		?>
		<label class="seeko-toggle-control-label">
			<input class="seeko-toggle-control-checkbox screen-reader-text" {{{ data.inputAttrs }}} type="checkbox" id="{{ data.id }}" value="{{ data.value }}" <# if ( data.value ) { #> checked <# } #> hidden {{{ data.link }}} />
			<span class="seeko-toggle-control-switch"></span>
		</label>
		<?php
	}
}
