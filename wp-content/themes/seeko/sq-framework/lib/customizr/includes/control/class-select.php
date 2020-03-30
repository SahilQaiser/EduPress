<?php
/**
 * Handles select control class.
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
 * Select control class.
 *
 * This is a special section for rendering tabs and child popups controls inside the Popup section.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
class Seeko_Customizer_Control_Select extends Seeko_Customizer_Base_Input_Group {

	/**
	 * Control's type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'seeko-select';

	/**
	 * Control's placeholder.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $placeholder = '';

	/**
	 * Delayed data loading for choices.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $load_choices = '';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['placeholder'] = $this->placeholder;

		// Choices to load.
		$this->json['load_choices'] = $this->load_choices;
	}

	/**
	 * An Underscore (JS) template for control field.
	 *
	 * @since 1.0.0
	 */
	protected function group_field_template() {
		?>
		<# if ( data.load_choices === 'widgets_area' ) { #>
			<# data.choices = <?php echo json_encode( Seeko_Customizer_Utils::get_select_widgets_area() ); // @codingStandardsIgnoreLine ?> #>
		<# } #>

		<# classes = 'seeko-select-control-field';
		if ( data ) {
			classes += ' seeko-renew-preview';
		} #>
		<select class="{{classes}}" {{{ data.inputAttrs }}} value="{{ data.value }}" id="{{ data.id }}" {{{ data.link }}}>
			<# if ( ! _.isEmpty( data.placeholder ) ) { #>
				<option value="" <# if ( _.isEmpty( data.value ) ) { #> selected<# } #>>{{{ data.placeholder }}}</option>
			<# } #>
			<# _.each( data.choices, function( label, key ) { #>
				<# if ( ! _.isEmpty( key ) ) { #>
					<# selected = ( data.value === key ) #>
					<option value="{{ key }}" <# if ( selected ) { #> selected<# } #>>{{{ label }}}</option>
				<# }#>
			<# } ); #>
		</select>
		<?php
	}
}
