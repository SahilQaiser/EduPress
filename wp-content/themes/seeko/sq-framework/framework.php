<?php

class SVQ_FW {

    /*
     * saved config options
     */
    public static $config = array();

	/*saved custom css */
	public static $custom_css;

	/* list of required plugins */
	public $tgm_plugins;

	/**
	 * @var SVQ_FW The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * Main SVQ_FW Instance
	 *
	 * Ensures only one instance of SQ is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @return SVQ_FW - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	

	/**
	 * Constructor method for the class. It controls the load order of the required files for running
	 * the framework.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		/* Define framework, parent theme, and child theme constants. */
		$this->constants();
		
		$this->init();

		/* Load core functions */
		$this->core();

		/* Initialize the framework's default actions and filters. */
		add_action( 'after_setup_theme', array( $this, 'default_filters' ), 3 );

		add_action( 'after_setup_theme', [ $this, 'customizr_init'], 14 );
		add_action( 'after_setup_theme', [ $this, 'options_init'], 14 );
		add_action( 'after_setup_theme', [ $this, 'dynamic_css_init'], 14 );

		/* Load the framework functions. */
		add_action( 'after_setup_theme', array( $this, 'functions' ), 12 );

	}
	
	public function init() {
		self::set_config( 'slug', 'svq-theme' );
	}

	public static function add_css( $data ) {
		self::$custom_css .= $data;
	}

	public static function get_config( $name ) {
		if (isset(self::$config[$name])) {
			return self::$config[$name];
		}

		return false;
	}

	public static function init_config( $data ) {
		self::$config = $data;
	}

	public static function set_config($name, $value) {
		self::$config[$name] = $value;
	}

	/**
	 * Defines the constant paths for use within the core framework, parent theme, and child theme.  
	 *
	 * @since 1.0.0
	 */
	function constants() {

		/* Sets the framework version number. */
		define( 'SVQ_VERSION', '3.1' );
        
		/* Sets the framework domain */
		define( 'SVQ_DOMAIN', str_replace( " ", "_", strtolower( wp_get_theme() ) ) );

		/* Sets the path to the parent theme directory. */
		define( 'THEME_DIR', get_template_directory() );

		/* Sets the path to the parent theme directory URI. */
		define( 'THEME_URI', get_template_directory_uri() );

		/* Sets the path to the child theme directory. */
		define( 'CHILD_THEME_DIR', get_stylesheet_directory() );

		/* Sets the path to the child theme directory URI. */
		define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );

		/* Sets the path to the core framework directory. */
		define( 'SVQ_DIR', trailingslashit( THEME_DIR ) . 'sq-framework' );

		/* Sets the path to the core framework directory URI. */
		define( 'SVQ_URI', trailingslashit( THEME_URI ) . 'sq-framework' );

		/* Sets the path to the theme framework folder. */
		define( 'SVQ_FW_DIR', trailingslashit( THEME_DIR ) . 'sq-framework' );

		/* Sets the url to the theme framework folder. */
		define( 'SVQ_FW_URI', trailingslashit( THEME_URI ) . 'sq-framework' );

		/* Sets the path to the theme library folder. */
		define( 'SVQ_LIB_DIR', trailingslashit( THEME_DIR ) . 'lib' );
		
		/* Sets the url to the theme library folder. */
		define( 'SVQ_LIB_URI', trailingslashit( THEME_URI ) . 'lib' );
	}

	/**
	 * Loads the core framework functions.  These files are needed before loading anything else in the 
	 * framework because they have required functions for use.
	 *
	 * @since 1.0.0
	 */
	function core() {

		/* Load required plugins library */
		require_once SVQ_DIR. '/lib/class-tgm-plugin-activation.php';

		/* Load the core framework functions. */
		require_once( trailingslashit( SVQ_DIR ) . 'lib/function-core.php' );

		/* Load Customizr files */


		require_if_theme_supports ( 'svq-customizr', SVQ_DIR. '/lib/customizr/fallback.php' );
		require_if_theme_supports ( 'svq-customizr', SVQ_DIR. '/lib/customizr/init.php' );

		if ( ! class_exists( 'Seeko_Customizer_Utils' ) ) {
			require_if_theme_supports ( 'svq-customizr', SVQ_DIR. '/lib/customizr/includes/class-utils.php' );
		}

		require_if_theme_supports ( 'svq-customizr', SVQ_DIR. '/lib/customizr/recommend.php' );
	}

	/**
	 * Loads the framework functions.
	 *
	 * @since 1.0.0
	 */
	function functions() {

		// Include breadcrumb
		if ( ! is_admin() ) {
            require_once(SVQ_DIR.'/lib/function-breadcrumb.php');
		}
	}

	
	/**
	 * Adds the default framework actions and filters.
	 *
	 * @since 1.0.0
	 */
	function default_filters() 
	{

		/* Remove bbPress theme compatibility if current theme supports bbPress. */
		if ( current_theme_supports( 'bbpress' ) ) {
			remove_action( 'bbp_init', 'bbp_setup_theme_compat', 8 );
		}
		
		/* Make text widgets and term descriptions shortcode aware. */
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'term_description', 'do_shortcode' );
		
	}

	public function is_active( $code= '' ) {

		return true;
	}

	public function verify_purchase( $tf_code = '', $force = false ) {

		if( self::get_config( 'item_id' ) == '' ) {
			return false;
		}

		if ( $tf_code === '' ) {
			$tf_code = get_option( 'envato_purchase_code_' . self::get_config( 'item_id' ), '' );
		}

		if( $tf_code && $tf_code !== '' ) {

			if ( $force ) {
				delete_transient( 'svq_license_' . self::get_config( 'item_id' ) );
			}

			if ( $license_data = get_transient( 'svq_license_' . self::get_config( 'item_id' ) ) ) {
				return $license_data;
			} else {

				$data = $this->get_purchase_data( $tf_code );

				if ( ! $data ) {
					return false;
				}

				if ( isset( $data->supported_until ) ) {
					$license_data = [];
					$license_data['active']          = true;
					$license_data['supported_until'] = $data->supported_until;
					set_transient( 'svq_license_' . self::get_config( 'item_id' ), $license_data, 60 * 60 * 24 );

					return $license_data;
				}
			}
		}

		return false;
	}


	public function get_purchase_data( $code ) {
		$theme = self::get_config( 'item_id' );

		$purchase_get = wp_remote_get( 'http://updates.seventhqueen.com/verify-purchase/?product=' . $theme . '&code=' . $code );

		// Check for error
		if ( ! is_wp_error( $purchase_get ) ) {
			$response = wp_remote_retrieve_body( $purchase_get );

			// Check for error
			if ( ! is_wp_error( $response ) ) {
				$response = json_decode( $response );
				return $response;
			}
		}

		return false;

	}

	/* Method to register TGMPA plugins */
	public function required_plugins() {
		// Change this to your theme text domain, used for internationalising strings
		$theme_text_domain = 'seeko';

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'id'                => 'tgmpa-svq-' . SVQ_THEME_VERSION,
			'default_path'      => '',                           // Default absolute path to pre-packaged plugins
			'menu'              => 'install-required-plugins',   // Menu slug
			'has_notices'       => true,                         // Show admin notices or not
			'is_automatic'      => true,            // Automatically activate plugins after installation or not
			'message'           => '',               // Message to output right before the plugins table
			'strings'           => array(
				'page_title'                                => esc_html__( 'Install Required Plugins', 'seeko' ),
				'menu_title'                                => esc_html__( 'Install Plugins', 'seeko' ),
				'installing'                                => esc_html__( 'Installing Plugin: %s', 'seeko' ), // %1$s = plugin name
				'oops'                                      => esc_html__( 'Something went wrong with the plugin API.', 'seeko' ),
				'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.','seeko' ), // %1$s = plugin name(s)
				'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.','seeko' ), // %1$s = plugin name(s)
				'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.','seeko' ), // %1$s = plugin name(s)
				'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.','seeko' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.','seeko' ), // %1$s = plugin name(s)
				'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.','seeko' ), // %1$s = plugin name(s)
				'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.','seeko' ), // %1$s = plugin name(s)
				'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.','seeko' ), // %1$s = plugin name(s)
				'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'seeko' ),
				'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'seeko' ),
				'return'                                    => esc_html__( 'Return to Required Plugins Installer', 'seeko' ),
				'plugin_activated'                          => esc_html__( 'Plugin activated successfully.', 'seeko' ),
				'complete'                                  => esc_html__( 'All plugins installed and activated successfully. %s', 'seeko' ) // %1$s = dashboard link
			)
		);

		tgmpa( $this->tgm_plugins, $config );

	}

	function customizr_init() {

		if ( current_theme_supports( 'svq-customizr' ) ) {
			$path = SVQ_LIB_DIR . '/customizr.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}
	function options_init() {

		if ( current_theme_supports( 'svq-options' ) ) {
			$path = SVQ_LIB_DIR . '/options.php';
			if ( file_exists( $path ) ) {
				require $path;
			}
		}
	}
	function dynamic_css_init() {

		if ( current_theme_supports( 'dynamic-css' ) ) {
			require_once SVQ_FW_DIR . '/lib/dynamic-css/dynamic-css.php';

		}
	}
}

/**
 * Returns the main instance of SeventhQueen
 *
 * @since  1.0
 * @return SVQ_FW
 */
function SQF() {
	return SVQ_FW::instance();
}

/* Create instance */
SQF();