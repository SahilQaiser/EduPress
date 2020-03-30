<?php
/**
 * This class handles extending Kirki plugin framework.
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
 * Define all customizer utils.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
final class Seeko_Customizer_Kirki_Extend {

	/**
	 * Control outputs.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $control_outputs = [
		'seeko-background' => 'Seeko_Customizer_Kirki_Extend_Output_Background',
		'seeko-box-model'  => 'Seeko_Customizer_Kirki_Extend_Output_Box_Model',
		'seeko-box-shadow' => 'Seeko_Customizer_Kirki_Extend_Output_Box_Shadow',
		'seeko-border'     => 'Seeko_Customizer_Kirki_Extend_Output_Border',
		'seeko-input'      => 'Seeko_Customizer_Kirki_Extend_Output_Input',
	];

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
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'kirki_section_types', [ $this, 'add_section_types' ] );
		add_filter( 'kirki_control_types', [ $this, 'add_control_types' ] );
		add_filter( 'kirki_seeko_options_output_control_classnames', [ $this, 'add_control_outputs' ] );
		add_action( 'init', [ $this, 'register_settings' ], 15 );
	}

	/**
	 * Add custom section types to Kirki.
	 *
	 * @since 1.0.0
	 *
	 * @param array $section_types Defined section types from Kirki.
	 *
	 * @return array
	 */
	public function add_section_types( $section_types = [] ) {
		return array_merge( $section_types, Seeko_Customizer::$section_types );
	}

	/**
	 * Add custom control types to Kirki.
	 *
	 * @since 1.0.0
	 *
	 * @param array $control_types Defined control types from Kirki.
	 *
	 * @return array
	 */
	public function add_control_types( $control_types = [] ) {
		return array_merge( $control_types, Seeko_Customizer::$control_types, Seeko_Customizer::$group_control_types );
	}

	/**
	 * Add custom control outputs to Kirki.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_control_outputs( $outputs ) {

		$outputs = $outputs + $this->control_outputs;

		$controls = array_merge( Seeko_Customizer::$control_types, Seeko_Customizer::$group_control_types );

		foreach ( $controls as $control => $class ) {
			if ( ! key_exists( $control, $outputs ) ) {
				$outputs[ $control ] = 'Seeko_Customizer_Kirki_Extend_Base_Output';
			}
		}

		return $outputs;
	}

	/**
	 * Register settings to Kirki.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		/**
		 * Add config.
		 *
		 * @link https://aristath.github.io/kirki/docs/getting-started/config.html
		 *
		 * @since 1.0.0
		 */
		Kirki::add_config(
			Seeko_Customizer::$config_id, [
				'capability'  => 'edit_theme_options',
				'option_type' => 'theme_mod',
			]
		);

		// Add panels.
		foreach ( Seeko_Customizer::$panels as $id => $panel ) {
			Kirki::add_panel( $id, $panel );
		}

		// Add sections.
		foreach ( Seeko_Customizer::$sections as $id => $section ) {
			Kirki::add_section( $id, $section );
		}

		// Add settings.
		foreach ( Seeko_Customizer::$fields as $id => $setting ) {
			Kirki::add_field( Seeko_Customizer::$config_id, $setting );
		}
	}
}
