<?php
/**
 * Add plugin main class.
 *
 * @package Seventor
 * @since 1.0.0
 */

namespace Seventor;

defined( 'ABSPATH' ) || die();

define( 'SEVENTOR__FILE__', __FILE__ );


use Elementor\Settings;

/**
 * Plugin class.
 *
 * @since 1.0.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Plugin {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Modules.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $modules = [];

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_name;

	/**
	 * The plugin version number.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_version;

	/**
	 * The minimum Elementor version number required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $minimum_elementor_version = '2.0.0';

	/**
	 * The plugin directory.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_path;

	/**
	 * The plugin URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_url;

	/**
	 * The plugin assets URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_assets_url;

	/**
	 * Disables class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'sq-theme-core' ), '1.0.0' );
	}

	/**
	 * Disables unserializing of the class.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'sq-theme-core' ), '1.0.0' );
	}

	/**
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'check_elementor_version' ] );
	}

	/**
	 * Checks Elementor version compatibility.
	 *
	 * First checks if Elementor is installed and active,
	 * then checks Elementor version compatibility.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function check_elementor_version() {
		if ( ! class_exists( '\\Seventor\\Utils' ) ) {
			if ( empty( self::$plugin_path ) ) {
				self::$plugin_path = trailingslashit( plugin_dir_path( SEVENTOR__FILE__ ) );
			}
			// Requires Utils class.
			require_once self::$plugin_path . 'utils.php';
		}

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			// don't go further.
			return;
		}

		// Check for the minimum required Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, self::$minimum_elementor_version, '>=' ) ) {
			if ( current_user_can( 'update_plugins' ) ) {
				add_action( 'admin_notices',
				[ $this, 'admin_notice_minimum_elementor_version' ] );
			}
			// don't go further.
			return;
		}

		spl_autoload_register( [ $this, 'autoload' ] );

		$this->define_constants();
		$this->add_hooks();
	}


	/**
	 * Displays notice on the admin dashboard if Elementor version is lower than the
	 * required minimum.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) { // WPCS: CSRF ok, input var ok.
			unset( $_GET['activate'] ); // WPCS: input var ok.
		}

		$message = sprintf(
			'<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">'
			/* translators: 1: Plugin name 2: Elementor */
			. esc_html__( '%1$s requires version %3$s or greater of %2$s plugin.', 'sq-theme-core' )
			. '</span>',
			'<strong>' . esc_html__( 'Seventor', 'sq-theme-core' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'sq-theme-core' ) . '</strong>',
			self::$minimum_elementor_version
		);

		$file_path   = 'elementor/elementor.php';
		$update_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

		$message .= sprintf(
			'<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">' .
			'<a class="button-primary" href="%1$s">%2$s</a></span>',
			$update_link, esc_html__( 'Update Elementor Now', 'sq-theme-core' )
		);

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}

	/**
	 * Autoload classes based on namesapce.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $class Name of class.
	 */
	public function autoload( $class ) {

		// Return if Seventor name space is not set.
		if ( false === strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		/**
		 * Prepare filename.
		 *
		 * @todo Refactor to use preg_replace.
		 */
		$filename = str_replace( __NAMESPACE__ . '\\', '', $class );
		$filename = str_replace( '\\', DIRECTORY_SEPARATOR, $filename );
		$filename = str_replace( '_', '-', $filename );
		$filename = dirname( __FILE__ ) . '/' . strtolower( $filename ) . '.php';

		// Return if file is not found.
		if ( ! is_readable( $filename ) ) {
			return;
		}

		include $filename;
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_constants() {

		self::$plugin_path       = trailingslashit( plugin_dir_path( SEVENTOR__FILE__ ) );
		self::$plugin_url        = trailingslashit( plugin_dir_url( SEVENTOR__FILE__ ) );
		self::$plugin_assets_url = trailingslashit( self::$plugin_url . 'assets' );
	}

	/**
	 * Adds required hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function add_hooks() {
		add_action( 'elementor/init', [ $this, 'init' ], 0 );

		add_action( 'elementor/element/column/layout/before_section_end', [ $this, 'add_column_order_control' ], 12, 2 );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 20 );
		}
	}


	/**
	 * Register modules.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_modules() {

		if ( ! class_exists( 'ElementorPro\Plugin' ) ) {
			new Core\Library\Module();
		}

		// Template library.
		new Core\Template\Module();
	}

	/**
	 * Adds actions after Elementor init.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		// Register modules.
		$this->register_modules();

		// Requires Utils class.
		require_once self::$plugin_path . 'utils.php';

		do_action( 'seventor/init' );
	}

	/**
	 * Add order control to column settings.
	 *
	 * @param object $element Element instance.
	 * @param array  $args    Element arguments.
	 */
	public function add_column_order_control( $element, $args ) {

		$existing_control = \Elementor\Plugin::$instance->controls_manager->get_control_from_stack( $element->get_unique_name(), 'column_order' );

		if ( is_wp_error( $existing_control ) ) {
			$args = [
				'position' => array(
					'at' => 'before',
					'of' => 'content_position',
				),
			];

			$element->add_responsive_control(
				'column_order',
				array(
					'label'       => esc_html__( 'Column Order', 'seventor' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'min'         => 0,
					'max'         => 100,
					'selectors'   => array(
						'{{WRAPPER}}.elementor-column' => 'order: {{VALUE}}',
					),
				),
				$args
			);
		}

	}


	/**
	 * Add Seventor tab in Elementor Settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $settings Settings.
	 */
	public function register_admin_fields( $settings ) {
		$settings->add_tab(
			'seventor', [
				'label' => __( 'SeventhQueen', 'sq-theme-core' ),
			]
		);
	}
}

/**
 * Returns the Plugin application instance.
 *
 * @since 1.0.0
 *
 * @return Plugin
 */
function seventor() {
	return Plugin::get_instance();
}

/**
 * Initializes the Plugin application.
 *
 * @since 1.0.0
 */
seventor();
