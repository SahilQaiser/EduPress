<?php
/* SQ THEME PANEL */

class SQ_Panel {

	/**
	 * @var SQ_Panel The reference to *SQ_Panel* instance of this class
	 */
	protected static $_instance = null;

	public $config = [];

	public function __construct( $config = [] ) {

		$defaults = array(
			'theme_name' => 'KLEO',
			'theme_lower' => 'kleo',
			'slug' => 'sq-panel',
			'priority_addons' => [],
			'item_id' => '',
			'purchase_link' => '#',
		);
		$this->config = wp_parse_args( $config, $defaults );

		$this->set_constants();
		$this->set_hooks();
		$this->load_dependencies();

	}

	/**
	 * Returns the SQ_Panel instance of this class.
	 * @param $config
	 * @return SQ_Panel - Main instance
	 */
	public static function getInstance( $config = [] ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $config );
		}

		return self::$_instance;
	}

	private function set_constants() {
		if ( ! defined( 'SVQ_PANEL_DIR' ) ) {
			define( 'SVQ_PANEL_DIR', SVQ_FW_DIR . '/lib/theme-welcome' );
		}

		if ( ! defined( 'SVQ_PANEL_URI' ) ) {
			define( 'SVQ_PANEL_URI', SVQ_FW_URI . '/lib/theme-welcome' );
		}
	}

	public function load_dependencies() {
		require_once( SVQ_PANEL_DIR . '/class-addons-manager.php' );
		if ( ! class_exists( 'SVQImport' ) ) {
			require_once( SVQ_PANEL_DIR . '/importer/import.php' );
		}
	}

	public function set_hooks() {

		add_action( 'admin_menu', array( $this, 'register_panel_page' ) );
		add_action( 'admin_init', array( $this, 'redirect_to_panel' ), 0 );

		add_filter( 'http_request_args', [$this, 'stop_update_theme' ], 5, 2 );

		add_action( 'wp_ajax_sq_theme_registration', array( $this, 'theme_registration' ) );
		add_action( 'after_switch_theme', array( $this, 'check_code_at_activation' ) );

		if ( ( isset( $_GET['page'] ) && $_GET['page'] == $this->config['slug'] ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'sq_do_plugin_action' ) ) {

			add_action( 'admin_init', array( $this, 'config_addons' ), 12 );

			add_action( 'admin_enqueue_scripts', array( $this, 'panel_scripts' ) );
		}

	}


	/**
	 * Register CSS & JS Files
	 */
	function panel_scripts() {
		//CSS
		wp_register_style( $this->config['slug'], SVQ_PANEL_URI . "/assets/css/theme-panel.css", array(), SVQ_THEME_VERSION, "all" );
		wp_enqueue_style( $this->config['slug'] );

		//JS
		wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_register_script( $this->config['slug'], SVQ_PANEL_URI . "/assets/js/theme-panel.js", array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_enqueue_script( $this->config['slug'] );
	}

	public function register_panel_page() {
		add_theme_page(
		    $this->config['theme_name'] . esc_html__( ' Panel', 'seeko' ),
			$this->config['theme_name'] . esc_html__( ' Panel', 'seeko' ),
			'manage_options',
			$this->config['slug'],
			array( $this, 'panel_page' )
		);
	}

	function panel_page() {

		require( SVQ_PANEL_DIR . '/templates/welcome.php' );

	}

	public function redirect_to_panel() {
		// Theme activation redirect
		global $pagenow;
		if ( isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {

			wp_redirect( admin_url( "themes.php?page=" . $this->config['slug'] ) );
			exit;
		}
	}

	public function stop_update_theme( $r, $url ) {
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $r; // Not a theme update request. Bail immediately.
		}

		$themes = json_decode( $r['body']['themes'] );
		$theme = get_template();
		unset( $themes->themes->$theme );
		$r['body']['themes'] = json_encode( $themes );
		return $r;
	}

	public function theme_registration() {
		if ( ! isset( $_POST['sq_nonce'] ) || ! wp_verify_nonce( $_POST['sq_nonce'], 'sq_theme_registration' ) ) {
			wp_send_json_error( array( 'error' => 'Sorry, your nonce did not verify.' ) );
		}

		$option_name = "envato_purchase_code_" . $this->config['item_id'];
		$tf_code      = isset( $_POST['code'] ) ? $_POST['code'] : '';

		if ( ! empty( $tf_code ) ) {

			$has_purchased = SVQ_FW::instance()->is_active( $tf_code );

			if ( $has_purchased ) {

				//Update code
				update_option( $option_name, $tf_code );

				wp_send_json_success( array( 'message' => __( 'License successfully activated. Please refresh page.', 'seeko' ) ) );
			} else {
				wp_send_json_error( array( 'error' => 'Purchase code is not valid. Please check the license provided!' ) );
			}

		}

		wp_send_json_error( array( 'error' => 'Please enter your purchase code.' ) );
	}

	public function check_code_at_activation() {
		SVQ_FW::instance()->verify_purchase();
	}


	public function config_addons() {

		/* Move elements first */
		if( empty( $this->config['priority_addons'] ) ) {
			return;
		}

		$priority_list = array_reverse( $this->config['priority_addons'] );
		foreach ( $priority_list as $item ) {
			SQ_Addons_Manager()->plugins = array( $item => SQ_Addons_Manager()->plugins[ $item ] ) + SQ_Addons_Manager()->plugins;
		}

		$prepend = array(
			$this->config['theme_lower'] . '-child' => array(
				'addon_type'  => 'child_theme',
				'name'        => $this->config['theme_name'] . ' child theme',
				'slug'        => $this->config['theme_lower'] . '-child',
				'source'      => SVQ_LIB_DIR . '/inc/'. $this->config['theme_lower'] .'-child.zip',
				'source_type' => 'bundled',
				'version'     => '1.0',
				'required'    => true,
				'description' => 'Always activate the child theme to safely update '. $this->config['theme_name'] . ' and for better customization. <a href="https://codex.wordpress.org/Child_Themes" target="_blank">More on Child Themes</a>.',
			)
		);

		SQ_Addons_Manager()->plugins = $prepend + SQ_Addons_Manager()->plugins;
	}

}

