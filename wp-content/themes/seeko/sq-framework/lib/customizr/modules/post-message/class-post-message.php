<?php
/**
 * This class handles CSS post message for Customizer previewer.
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
 * Post message preview scripts.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
final class Seeko_Customizer_Post_Message {

	private $script = '';
	/**
	 * Module activate condition.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Class active state.
	 */
	public static function active() {
		return true;
	}

	/**
	 * Constructor.
	 *
	 * @access protected
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'postmessage' ), 12 );
	}

	/**
	 * Enqueues the postMessage script
	 * and adds variables to it using the wp_localize_script function.
	 * The rest is handled via JS.
	 */
	public function postmessage() {
		$fields = Kirki::$fields;
		foreach ( $fields as $field ) {
			if ( isset( $field['transport'] ) && 'postMessage' === $field['transport'] && isset( $field['settings'] )
			     && isset( $field['output'] ) && ! empty( $field['output'] ) ) {
				if ( strpos( $field['type'], 'seeko-', 0 ) === 0 ) {
					$this->script .= $this->get_callback( $field );
				}
			}
		}
		if ( $this->script !== '' ) {
			wp_add_inline_script( 'kirki_auto_postmessage', $this->script, 'after' );
		}

	}

	/**
	 * Get the callback function/method we're going to use for this field.
	 *
	 * @access private
	 * @since 3.0.0
	 * @param array $args The field args.
	 * @return string|array A callable function or method.
	 */
	protected function get_callback( $args ) {
		$data = '';
		switch ( $args['type'] ) {
			case 'seeko-input':
				$data = $this->script_input( $args );
				break;
			default:

		}
		return $data;
	}

	/**
	 * Sanitizes the arguments and makes sure they are all there.
	 *
	 * @access private
	 * @since 3.0.0
	 * @param array $args The arguments.
	 * @return array
	 */
	private function get_args( $args ) {

		// Make sure everything is defined to avoid "undefined index" errors.
		$args = wp_parse_args(
			$args, array(
				'element'       => '',
				'property'      => '',
				'prefix'        => '',
				'suffix'        => '',
				'units'         => '',
				'js_callback'   => array( '', '' ),
				'value_pattern' => '',
			)
		);

		// Element should be a string.
		if ( is_array( $args['element'] ) ) {
			$args['element'] = implode( ',', $args['element'] );
		}

		// Make sure arguments that are passed-on to callbacks are strings.
		if ( is_array( $args['js_callback'] ) && isset( $args['js_callback'][1] ) && is_array( $args['js_callback'][1] ) ) {
			$args['js_callback'][1] = wp_json_encode( $args['js_callback'][1] );
		}

		if ( ! isset( $args['js_callback'][1] ) ) {
			$args['js_callback'][1] = '';
		}
		return $args;
	}


	/**
	 * Generates script for seeko-input
	 *
	 * @access protected
	 * @since 3.0.0
	 * @param array $args  The arguments for this js_var.
	 * @return string
	 */
	protected function script_input( $args ) {

		$args = $this->get_args( $args );

		$script = 'wp.customize(\'' . $args['settings'] . '\',function(value){value.bind(function(newValue){';

		// append unique style tag if not exist
		// The style ID.
		$style_id = 'kirki-postmessage-' . str_replace( array( '[', ']' ), '', $args['settings'] );
		$script  .= 'if(null===document.getElementById(\'' . $style_id . '\')||\'undefined\'===typeof document.getElementById(\'' . $style_id . '\')){jQuery(\'head\').append(\'<style id="' . $style_id . '"></style>\');}';

		$data = 'var cssContent="";';

		foreach ( $args['output'] as $key => $js_var ) {

			if ( isset( $args['responsive'] ) && $args['responsive'] == true ) {

				$data .= 'var responsiveDevices = '. json_encode( Seeko_Customizer::$responsive_devices ) .';';

				$data .= '_.each(responsiveDevices, function (mediaQuery, device) {
		              if (newValue[device] && newValue[device]["size"]) {
		                if ( mediaQuery !== "global" ) {
		                    cssContent += mediaQuery + "{";
		                }
		                cssContent += "' .$js_var['element'] . '{' . $js_var['property'] . ' :" + newValue[device]["size"] + newValue[device]["unit"].replace("-","") + ";}";
	                    if ( mediaQuery !== "global" ) {
		                    cssContent += "}";
		                }
		              }
                });';
			} else {
				$data .= 'cssContent += "' .$js_var['element'] . '{' . $js_var['property'] . ' :" + newValue["size"] + newValue["unit"].replace("-","") + ";}";';
			}

		}

		$script .= $data . "jQuery('#{$style_id}').text(cssContent);jQuery('#{$style_id}').appendTo('head');";
		$script .= '});});';


		return $script;
	}
}
