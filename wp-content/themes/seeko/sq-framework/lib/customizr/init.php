<?php
/**
 * This class handles customizer function.
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
 * Extends WordPress customizer capability.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 */
final class _Seeko_Customizer_Init {

	/**
	 * List of auto-loaded modules.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $modules = [
		'kirki-extend' => 'Seeko_Customizer_Kirki_Extend',
		'post-message' => 'Seeko_Customizer_Post_Message',
	];

	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! class_exists('Kirki') ) {
			return;
		}
		$this->define_constants();
		$this->includes();
		$this->add_hooks();
		$this->load_modules();
	}

	/**
	 * Define customizer constants.
	 *
	 * @since 1.0.0
	 */
	protected function define_constants() {
		define( 'SEEKO_MIN_JS', '' );
		define( 'SEEKO_MIN_CSS', '' );
		define( 'SEEKO_VERSION', '1.0' );
		define( 'SEEKO_CUSTOMIZER_PATH', trailingslashit( SVQ_FW_DIR . '/lib/customizr' ) );
		define( 'SEEKO_CUSTOMIZER_URL', trailingslashit( SVQ_FW_URI . '/lib/customizr' ) );

	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	protected function includes() {
		include_once SEEKO_CUSTOMIZER_PATH . 'includes/class-autoloader.php';
	}

	/**
	 * Add filters and actions.
	 *
	 * @since 1.0.0
	 */
	protected function add_hooks() {
		add_action( 'customize_register', [ $this, 'register_control_types' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'kirki_telemetry', '__return_false' );
	}

	/**
	 * Load modules.
	 *
	 * @since 1.0.0
	 */
	protected function load_modules() {

		foreach ( $this->modules as $module ) {
			if ( class_exists( $module ) && ( ! method_exists( $module, 'active' ) || $module::active() ) ) {
				new $module();
			}
		}
	}

	/**
	 * Register all control types.
	 *
	 * @since 1.0.0
	 *
	 * @param object $wp_customize Global customize object.
	 */
	public function register_control_types( $wp_customize ) {
		foreach ( Seeko_Customizer::$control_types as $control_type ) {
			$wp_customize->register_control_type( $control_type );
		}
	}

	/**
	 * Enqueue styles and scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( 'seeko-stepper', SEEKO_CUSTOMIZER_URL . 'assets/lib/stepper/stepper.js', [], '1.0.0', true );
		wp_register_script( 'seeko-url-polyfill', SEEKO_CUSTOMIZER_URL . 'assets/lib/url-polyfill/url-polyfill' . SEEKO_MIN_JS . '.js', [], '1.1.0', false );
		wp_enqueue_script( 'seeko-customizer', SEEKO_CUSTOMIZER_URL . 'assets/js/customizer' . SEEKO_MIN_JS . '.js', [ 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable', 'seeko-stepper', 'seeko-url-polyfill' ], SEEKO_VERSION, true );

		wp_enqueue_style( 'seeko-customizer', SEEKO_CUSTOMIZER_URL . 'assets/css/customizer'  . SEEKO_MIN_CSS . '.css', [], SEEKO_VERSION );
	}

}

// Run customizer class.
new _Seeko_Customizer_Init();
