<?php
/**
 * Handles position control class.
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
 * Position control class.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Position extends Seeko_Customizer_Base_Control {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-position';

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
		var left = '<?php echo is_rtl() ? 'right' : 'left'; ?>';
		var right = '<?php echo is_rtl() ? 'left' : 'right'; ?>';

		position = {
			'top-left':     { value: 'top left', icon: 'arrow-' + left + '-top' },
			'top':          { value: 'top center', icon: 'arrow-top' },
			'top-right':    { value: 'top right', icon: 'arrow-' + right + '-top' },
			'center-left':  { value: 'center left', icon: 'arrow-' + left },
			'center':       { value: 'center', icon: '' },
			'center-right': { value: 'center right', icon: 'arrow-' + right },
			'bottom-left':  { value: 'bottom left', icon: 'arrow-' + left + '-bottom' },
			'bottom':       { value: 'bottom center', icon: 'arrow-bottom' },
			'bottom-right': { value: 'bottom right', icon: 'arrow-' + right + '-bottom' }
		}
		#>
		<div class="seeko-control seeko-position-control">
			<div class="seeko-position-control-buttons">
				<# _.each( position, function( position, key ) { #>
					<input class="seeko-position-control-radio" {{{ data.inputAttrs }}} type="radio" value="{{ position.value }}" name="{{ data.id }}" id="{{ data.id }}-{{ key }}" {{{ data.link }}} <# if ( position.value === data.value ) { #> checked <# } #>>
					<label class="seeko-position-control-button seeko-position-control-{{ ( position.icon ) ? 'icon' : 'label' }}" for="{{ data.id }}-{{ key }}"><# if ( position.icon ) { #><img src="<?php echo esc_url( Seeko_Customizer_Utils::get_assets_url() ); ?>/img/{{ position.icon }}.svg" /><# } else { #>{{ position.label }}<# } #></label>
				<# } ) #>
			</div>
		</div>
		<?php
	}
}
