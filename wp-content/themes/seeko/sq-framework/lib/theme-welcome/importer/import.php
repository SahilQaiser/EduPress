<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** ---------------------------------------------------------------------------
 * Import Demo Data
 * @author SeventhQueen
 * @version 1.0
 * ---------------------------------------------------------------------------- */
class SVQImport {

	private static $instance;

	public $config = [];
	private static $pages_data = array();
	public $error = '';
	public $messages = array();
	public $session = '';
	public $data_imported = false;

	/**
	 * Array with pages imported
	 *
	 * @var array
	 */

	public $pages_imported = array();

	/**
	 * Save mapping for search and replace
	 * @var array
	 */
	private $url_remap = array();

	/**
	 * Keep track of images imported
	 *
	 * @var array
	 */

	public $images_imported = array();
	/**
	 * Save the images that will be imported
	 * @var array
	 */

	public $total_images = array();
	/**
	 * Keep a history of all imported images on the site
	 * @var null
	 */

	public $image_history = null;
	/**
	 * Save images from post content for later import
	 * @var array
	 */
	public $content_images = array();
	/**
	 * Save attached posts images for later import
	 * @var array
	 */
	public $attached_images = array();
	/**
	 * Save slide media images for later import
	 * @var array
	 */
	public $slide_meta_images = array();
	/**
	 * Save elementor images for later import
	 * @var array
	 */
	public $elementor_images = array();
	/**
	 * Save featured images for later import
	 * @var array
	 */
	public $featured_images = array();
	/**
	 * Save external id and url for image import
	 * @var array
	 */

	public $failed_images = [];

	public $remote_images = array();
	public $remote_url_base = '';
	public $local_url_base = '';
	public $processes = 0;
	public $done_processes = 0;
	public $progress_pid = null;
	private $progress = null;


	public static function getInstance( $config = [] ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $config );
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct( $config = [] ) {

		add_action( 'admin_menu', array( $this, 'init' ) );

		add_action( 'admin_init', array( $this, 'do_import' ), 12 );

		$defaults     = array(
			'theme_name'  => 'KLEO',
			'theme_lower' => 'kleo',
			'slug'        => 'sq-panel',
			'data_set'    => [],
		);
		$this->config = wp_parse_args( $config, $defaults );
		if ( ! empty( $this->config['data_set'] ) ) {
			SVQImport::add_demo_sets( $this->config['data_set'] );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'import_assets' ) );

		add_action( 'wp_ajax_sq_single_import', array( $this, 'do_ajax' ) );
		add_action( 'wp_ajax_sq_set_as_home', array( $this, 'set_as_homepage' ) );

		add_action( 'admin_init', [ $this, 'activate_one_plugin' ] );

		if ( isset( $_REQUEST['sq_single_import'] ) && $_REQUEST['sq_single_import'] ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				add_filter( 'wp_redirect', function () {
					return false;
				} );
			}
		}

	}

	/**
	 * Add multiple demo sets
	 */
	static function add_demo_sets( $data ) {
		self::$pages_data = self::$pages_data + $data;
	}


	/**
	 * Add a demo set
	 */
	static function add_demo_set( $slug, $data = [] ) {
		self::$pages_data[ $slug ] = $data;
	}

	/** ---------------------------------------------------------------------------
	 * Add theme Page
	 * ---------------------------------------------------------------------------- */
	function init() {

		/*add_theme_page(
			$this->config['theme_name'] . esc_html__( ' Demo Data', 'seeko' ),
			$this->config['theme_name'] . esc_html__( ' Demo Data', 'seeko' ),
			'edit_theme_options',
			'sq_import',
			array( $this, 'import' )
		);*/

	}

	/** ---------------------------------------------------------------------------
	 * Enqueue scripts
	 * ---------------------------------------------------------------------------- */

	public function import_assets() {
		if ( isset( $_GET['page'] ) && ( 'sq_import' == $_GET['page'] || 'sq-panel' == $_GET['page'] ) ) {

			wp_enqueue_script( 'jquery-ui-tooltip' );

			wp_enqueue_style( 'kleo-import-css', SVQ_PANEL_URI . '/importer/assets/import.css', array(), SVQ_THEME_VERSION );
			wp_enqueue_script( 'kleo-import-js', SVQ_PANEL_URI . '/importer/assets/import.js', array(
				'jquery',
				'jquery-ui-tooltip'
			), SVQ_THEME_VERSION, true );
		}

	}

	public function set_as_homepage() {
		if ( session_id() ) {
			session_write_close();
		}
		check_ajax_referer( 'import_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'We&apos;re sorry, something went wrong.', 'seeko' ),
			) );
			exit;
		}

		if ( isset( $_POST['pid'] ) ) {
			$post_id = $_POST['pid'];
			if ( get_post_status( $post_id ) == 'publish' ) {
				if ( 'page' == get_post_type( $post_id ) ) {
					update_option( 'page_on_front', $post_id );
					update_option( 'show_on_front', 'page' );
					wp_send_json_success( array(
						'message' => __( 'Successfully set as homepage!', 'seeko' ),
					) );
					exit;
				}
			}
		}
		wp_send_json_success( array(
			'message' => __( 'An error occurred setting the page as home!!!', 'seeko' ),
		) );
		exit;

	}

	function do_ajax() {
		if ( session_id() ) {
			session_write_close();
		}

		check_ajax_referer( 'import_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => $this->set_error_message( __( 'We&apos;re sorry, the demo failed to import.', 'seeko' ) ),
			) );
			exit;
		}

		if ( ! isset( $_POST['options'] ) ) {
			wp_send_json_error( array(
				'message' => $this->set_error_message( __( 'Something went wrong. Please try again.', 'seeko' ) ),
			) );
			exit;
		}

		$data = array();

		parse_str( $_POST['options'], $data );

		if ( ! isset( $data['import_demo'] ) ) {
			wp_send_json_error( array(
				'message' => $this->set_error_message( __( 'Something went wrong with the data sent. Please try again.', 'seeko' ) ),
			) );
			exit;
		}

		$demo_sets   = self::get_demo_sets();
		$current_set = $data['import_demo'];

		if ( ! array_key_exists( $current_set, $demo_sets ) ) {
			wp_send_json_error( array(
				'message' => $this->set_error_message( __( 'Something went wrong with the data sent. Please try again.', 'seeko' ) ),
			) );
			exit;
		}

		$set_data     = $demo_sets[ $current_set ];
		$progress_pid = $_POST['pid'];

		$response = $this->process_import( array(
			'set_data' => $set_data,
			'pid'      => $progress_pid,
			'data'     => $data,
		) );

		$this->send_ajax_response( $response );
	}

	private function send_ajax_response( $data ) {
		if ( is_wp_error( $data ) ) {
			wp_send_json_error( array(
				'message' => $this->set_error_message(
					__( 'There was an error in the import process. Try to do the import once again!', 'seeko' ) .
					'<br>' . $data->get_error_message()
				),
				'debug'   => implode( ',', $this->messages ),
			) );
			exit;
		} elseif ( is_array( $data ) ) {
			$response            = $data;
			$response['debug']   = implode( ',', $this->messages );
			$response['message'] = $data['message'];
		} else {
			$response            = [];
			$response['process'] = $data;
			$response['debug']   = implode( ',', $this->messages );
			$response['message'] = implode( ',', $this->messages );
		}

		/* make sure we are regenerating theme dynamic file */
		//svq_generate_dynamic_css();

		wp_send_json_success( $response );
	}

	private function set_error_message( $msg ) {
		$header = '<div class="bg-msg fail-msg"><span class="dashicons dashicons-warning"></span></div>';

		return $header . $msg;
	}

	private function set_success_message( $msg ) {
		$header = '<div class="bg-msg success-msg"><span class="dashicons dashicons-yes"></span></div>';

		return $header . $msg;
	}

	/**
	 * Retrieve the demo sets
	 */
	static function get_demo_sets() {
		return self::$pages_data;
	}

	private function should_process_step( $step ) {
		if ( isset( $this->progress['steps'] ) ) {
			if ( isset( $this->progress['steps'][ $step ] ) ) {
				return false;
			}
		}

		return true;
	}


	public function get_progress( $pid ) {
		$data = get_transient( 'sq_import_' . $pid );

		return $data;
	}

	public function set_progress( $pid, $data, $stop_process = false ) {

		if ( $pid ) {

			$new_data = $data;

			if ( $current_data = $this->get_progress( $pid ) ) {
				$new_data = array_merge( $current_data, $data );
			}

			if ( isset( $data['step'] ) ) {
				$new_data['steps'][ $data['step'] ] = true;
			}

			$new_data['done_processes'] = $this->done_processes;
			$new_data['processes']      = $this->processes;

			if ( ! isset( $data['progress'] ) ) {
				if ( 0 == $this->done_processes ) {
					$new_data['progress'] = 1;
				} else {
					$new_data['progress'] = floor( $this->done_processes / $this->processes * 100 );
				}
			}

			/* Extra persistent data */
			if ( ! empty( $this->pages_imported ) ) {
				$new_data['pages_imported'] = $this->pages_imported;
			}

			set_transient( 'sq_import_' . $pid, $new_data, 60 * 60 );

			if ( $stop_process === true ) {
				$this->send_ajax_response( [
					'message'  => $new_data['text'],
					'process'  => $new_data['step'],
					'progress' => $new_data['progress'],
				] );
			}
		}
	}

	/**
	 * Process all the import steps
	 *
	 * @param array $options
	 *
	 * @return array|WP_Error
	 */
	public function process_import( $options ) {

		$imported         = false;
		$content_imported = false;

		$set_data           = $options['set_data'];
		$progress_pid       = $options['pid'];
		$this->progress_pid = $progress_pid;
		$progress           = $this->get_progress( $progress_pid );
		$this->progress     = $progress;
		$data               = $options['data'];

		if ( isset( $progress['image_imported'] ) ) {
			$this->images_imported = $progress['image_imported'];
		}
		if ( isset( $progress['pages_imported'] ) ) {
			$this->pages_imported = $progress['pages_imported'];
		}
		if ( isset( $progress['imported'] ) ) {
			$imported = true;
		}
		if ( isset( $progress['content_imported'] ) ) {
			$content_imported = true;
		}
		if ( isset( $progress['done_processes'] ) ) {
			$this->done_processes = $progress['done_processes'];
		} else {
			$this->done_processes = 0;
		}
		if ( isset( $progress['processes'] ) ) {
			$this->processes = $progress['processes'];
		} else {
			$this->processes = count( $data ) + 1;
		}

		if ( isset( $progress['remote_url_base'] ) ) {
			$this->remote_url_base = $progress['remote_url_base'];
		}
		if ( isset( $progress['total_images'] ) ) {
			$this->total_images = $progress['total_images'];
		}
		if ( isset( $progress['elementor_images'] ) ) {
			$this->elementor_images = $progress['elementor_images'];
		}
		if ( isset( $progress['url_remap'] ) ) {
			$this->url_remap = $progress['url_remap'];
		}
		if ( isset( $progress['attached_images'] ) ) {
			$this->attached_images = $progress['attached_images'];
		}
		if ( isset( $progress['slide_meta_images'] ) ) {
			$this->slide_meta_images = $progress['slide_meta_images'];
		}
		if ( isset( $progress['featured_images'] ) ) {
			$this->featured_images = $progress['featured_images'];
		}
		if ( isset( $progress['content_images'] ) ) {
			$this->content_images = $progress['content_images'];
		}
		if ( isset( $progress['failed_images'] ) ) {
			$this->failed_images = $progress['failed_images'];
		}

		// Importer classes

		if ( ! class_exists( 'WP_Importer' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}

		if ( ! class_exists( 'SVQ_WP_Import' ) ) {
			$this->activate_plugins( [ 'plugins' => [ 'sq-theme-core' ] ] );
			exit;
		}

		//activate required plugins
		if ( isset( $data['activate_plugins'] ) && $this->should_process_step( 'plugins' ) ) {
			$this->processes += count( $set_data['plugins'] ) - 1;
			$this->activate_plugins( $set_data );
		}

		//import pages xml
		if ( isset( $data['import_page'] ) && isset( $set_data['page'] ) && $this->should_process_step( 'pages' ) ) {

			$imported         = true;
			$content_imported = true;

			$file_path = $set_data['page'] . '.xml';
			$this->import_content( $file_path, true );

			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'             => 'Imported pages',
				'imported'         => true,
				'content_imported' => true,
				'step'             => 'pages',
			), true );

		}

		//import_stax_zones
		if ( isset( $data['import_stax'] ) && isset( $set_data['stax'] ) && $this->should_process_step( 'stax' ) ) {

			$imported = true;

			$this->import_stax_zones( $set_data['stax'] );
			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'     => 'Imported Stax templates...',
				'imported' => true,
				'step'     => 'stax',
			), true );

		}

		//import widgets
		if ( isset( $data['import_widgets'] ) && isset( $set_data['widgets'] ) && $this->should_process_step( 'widgets' ) ) {

			$imported = true;

			$widget_path = $set_data['widgets'];

			if ( isset( $set_data['widgets_sidebars'] ) && $set_data['widgets_sidebars'] ) {
				$this->import_sidebars( SVQ_LIB_URI . '/demo/sidebar_data.txt' );
			}

			//widgets
			$file_path = SVQ_LIB_DIR . '/demo/' . $widget_path . '.txt';
			if ( file_exists( $file_path ) ) {
				$file_data = svq_fs_get_contents( $file_path );
				if ( $file_data ) {
					$this->import_widget_data( $file_data );

					$this->messages[] = esc_html__( 'Imported widgets', 'seeko' );
				}
			}
			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'     => esc_html__( 'Imported widgets...', 'seeko' ),
				'imported' => true,
				'step'     => 'widgets',
			), true );
		}

		//check options
		if ( isset( $data['import_options'] ) && isset( $set_data['options'] ) && $this->should_process_step( 'options' ) ) {

			$imported = true;

			$this->import_options( $set_data['options'] );

			$this->messages[] = esc_html__( 'Imported Theme options', 'seeko' );
			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'     => esc_html__( 'Imported Theme Options...', 'seeko' ),
				'imported' => true,
				'step'     => 'options'
			), true );
		}

		//check revslider
		if ( isset( $data['import_revslider'] ) && isset( $set_data['revslider'] ) && $this->should_process_step( 'revslider' ) ) {

			$imported = true;

			global $kleo_config;
			$file_path = $kleo_config['upload_basedir'] . '/slider_imports';
			$sliders   = (array) $set_data['revslider'];
			if ( ! empty( $sliders ) ) {
				foreach ( $sliders as $file_name ) {
					/* if a slider doesn't already exist */
					if ( $this->check_existing_slider( $file_name ) ) {

						/* Download the file and import it */
						if ( $this->check_revslider_file( $file_path, $file_name . '.zip' ) ) {
							//file name provided without extension
							$this->import_revslider( $file_path, $file_name );
							$this->messages[] = sprintf( esc_html__( 'Imported Revslider %s', 'seeko' ), $file_name );
						}
					}
				}
			}
			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'     => 'Imported Revolution slider...',
				'imported' => true,
				'step'     => 'revslider',
			), true );
		}

		//check extra
		if ( isset( $set_data['extra'] ) && is_array( $set_data['extra'] ) ) {

			$extra_imported = false;
			foreach ( $set_data['extra'] as $extra ) {
				if ( ! isset( $data[ 'import_' . $extra['id'] ] ) || ! isset( $extra['id'] ) ) {
					continue;
				}
				if ( ! $this->should_process_step( 'extra_' . $extra['id'] ) ) {
					continue;
				}

				$imported = true;

				if ( 'menu' != $extra['id'] ) {
					$content_imported = true;
				}

				$ok_to_import = true;
				if ( 'menu' == $extra['id'] ) {
					if ( isset( $extra['slug'] ) && is_nav_menu( $extra['slug'] ) ) {
						$ok_to_import = false;
					}
				}
				if ( $ok_to_import ) {
					$file_path = $extra['data'] . '.xml';
					$this->import_content( $file_path, true );
					$this->messages[] = sprintf( esc_html__( '%s complete', 'seeko' ), $extra['name'] );
				}

				if ( 'menu' == $extra['id'] ) {
					$location = [];
					if ( isset( $extra['location'] ) && $extra['location'] ) {
						$location = $extra['location'];
					}
					$this->import_menu_location( $location );
				}

				$this->set_progress( $progress_pid, array(
					'text'             => 'Imported ' . ucfirst( $extra['id'] ) . '...',
					'imported'         => true,
					'content_imported' => true,
					'step'             => 'extra_' . $extra['id'],
				) );


				$extra_imported = true;
			}
			if ( $extra_imported ) {
				$this->done_processes ++;
			}
		}

		//check bp profile fields
		if ( isset( $data['import_bp_fields'] ) && isset( $set_data['bp_fields'] ) && $this->should_process_step( 'bp_fields' ) ) {

			$imported = true;

			$this->import_bp_fields( $set_data['bp_fields'] );
			$this->messages[] = esc_html__( 'Imported BuddyPress profile fields', 'seeko' );
			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'     => 'Imported BuddyPress profile fields',
				'imported' => true,
				'step'     => 'bp_fields',
			) );
		}

		if ( isset( $data['import_pmpro'] ) && isset( $set_data['pmpro'] ) && $this->should_process_step( 'pmpro' ) ) {

			$imported = true;

			$this->import_pmpro( $set_data['pmpro'] );

			$this->messages[] = esc_html__( 'Imported PMPRO Levels', 'seeko' );
			$this->done_processes ++;

			$this->set_progress( $progress_pid, array(
				'text'     => 'Imported PMPRO Levels',
				'imported' => true,
				'step'     => 'pmpro',
			) );
		}

		//replace imported image URLs with self hosted images
		if ( $content_imported ) {
			$this->processes ++;
			$this->post_process_posts();
			$this->done_processes ++;
		}

		do_action( 'svq_after_import', $set_data, $data, $this );

		$success_message = '<h3>' . __( 'Awesome. Your import is ready!!!', 'seeko' ) . '</h3>';

		$posts_summary = '';
		if ( ! empty( $this->pages_imported ) ) {
			$this->pages_imported = array_reverse( $this->pages_imported, true );
			foreach ( $this->pages_imported as $pid => $item ) {
				$posts_summary .= get_the_title( $pid );
				$posts_summary .= '<a href="#" title="' . esc_attr__( 'Set as HomePage', 'seeko' ) . '" class="sq-set-as-home" data-pid="' . esc_attr( $pid ) . '">' .
				                  '<span class="dashicons dashicons-admin-home"></span> ' .
				                  '</a>' .
				                  '<a target="_blank" href="' . get_permalink( $pid ) . '" title="' . esc_attr__( 'View Page', 'seeko' ) . '">' .
				                  '<span class="dashicons dashicons-visibility"></span>' .
				                  '</a>' .
				                  '<a target="_blank" title="' . esc_attr__( 'Edit Page', 'seeko' ) . '" href="' . esc_url( get_admin_url( null, 'post.php?post=' . $pid . '&action=edit' ) ) . '">' .
				                  '<span class="dashicons dashicons-edit"></span>' .
				                  '</a><br>';
			}
		} else {
			if ( isset( $data['import_page'] ) ) {
				$success_message = esc_html__( 'Your selected page already exists. Please check also Trash!', 'seeko' );
			}
		}

		if ( $posts_summary ) {
			$success_message .= '<p class="import-summary">' .
			                    __( 'Imported Pages:', 'seeko' ) . '<br>' .
			                    $posts_summary .
			                    '</p>';
		}

		if ( ! $imported ) {
			$this->error .= __( 'Nothing was selected for import!!!', 'seeko' );
		}

		return $this->get_import_response( $this->set_success_message( $success_message ) );
	}

	private function get_import_response( $success_message, $process = null ) {
		if ( '' == $this->error ) {
			$response = [
				'message' => $success_message,
			];
			if ( $process !== null ) {
				$response['process'] = $process;
			}

			return $response;
		} else {
			return new WP_Error( '__k__', $this->error );
		}
	}

	public function activate_one_plugin() {
		if ( isset( $_GET['svq_action'] ) && $_GET['svq_action'] == 'activate_plugin' ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! isset( $_GET['svq_nonce'] ) || ! wp_verify_nonce( $_GET['svq_nonce'], 'activate_plugin' ) ) {
				return;
			}

			$plugin = $_GET['plugin'];
			$this->activate_plugins( [ 'plugins' => [ $plugin ] ] );
			if( isset($_GET['redirect']) ) {
				wp_safe_redirect( $_GET['redirect'] );
			} else {
				wp_safe_redirect( svq_full_url() );
			}

		}
	}

	public function activate_plugins( $set_data, $set_progress = false ) {
		if ( isset( $set_data['plugins'] ) && ! empty( $set_data['plugins'] ) ) {
			foreach ( $set_data['plugins'] as $plugin ) {
				if ( ! class_exists( 'SQ_Addons_Manager' ) ) {
					require_once( SVQ_PANEL_DIR . '/class-addons-manager.php' );
				}
				$msg              = '';
				$plugin_nice_name = ucfirst( str_replace( array( '_', '-' ), ' ', $plugin ) );

				if ( ! SQ_Addons_Manager()->is_plugin_installed( $plugin ) ) {
					$install = SQ_Addons_Manager()->do_plugin_install( $plugin, false );
					if ( isset( $install['error'] ) ) {
						$this->error .= '<br>' . $plugin_nice_name . ': ' . $install['error'];
					}
					$msg              = sprintf( esc_html__( 'Installed %s plugin', 'seeko' ), $plugin_nice_name );
					$this->messages[] = $msg;
				}

				if ( ! SQ_Addons_Manager()->check_plugin_active( $plugin ) ) {
					$activate = SQ_Addons_Manager()->do_plugin_activate( $plugin, false );
					if ( isset( $activate['error'] ) ) {
						$this->error .= '<br>' . $plugin_nice_name . ': ' . $activate['error'];
					}
					$msg              = sprintf( esc_html__( 'Activated %s plugin', 'seeko' ), $plugin_nice_name );
					$this->messages[] = $msg;
				}

				if ( $set_progress ) {
					$this->done_processes ++;
					if ( $msg ) {
						$this->set_progress( $this->progress_pid, array(
							'text' => $msg,
							'step' => 'plugin_' . $plugin,
						), true );
					}
				}
			}

			//make sure to set plugins process as complete
			if ( $set_progress ) {
				$this->set_progress(
					$this->progress_pid,
					array(
						'text' => esc_html__( 'Plugins installed. Starting content import.', 'seeko' ),
						'step' => 'plugins',
					),
					true
				);
			}

		}

	}

	/**
	 * Import PMPRO Levels
	 * @param string $file
	 *
	 * @return bool
	 */
	public function import_pmpro( $file = 'pmpro' ) {


		$imported_ids = get_option( 'svq_' . get_template() . '_import_pmpro_' . $file );
		if ( ! is_array( $imported_ids )) {
			$imported_ids = [];
		}

		$file_data = wp_remote_get( SVQ_LIB_URI . '/demo/'. $file .'.txt' );
		if ( is_wp_error( $file_data ) ) {
			return false;
		}
		$data = wp_remote_retrieve_body( $file_data );
		$data = json_decode( $data );
		global $wpdb;

		foreach( $data->levels as $level ) {

			$wpdb->replace(
				$wpdb->pmpro_membership_levels,
				array(
					'id'=> isset( $imported_ids[ $level->id ] ) ? $imported_ids[ $level->id ] : 0,
					'name' => $level->name,
					'description' => $level->description,
					'confirmation' => '',
					'initial_payment' => $level->initial_payment,
					'billing_amount' => $level->billing_amount,
					'cycle_number' => $level->cycle_number,
					'cycle_period' => $level->cycle_period,
					'billing_limit' => $level->billing_limit,
					'trial_amount' => $level->trial_amount,
					'trial_limit' => $level->trial_limit,
					'expiration_number' => $level->expiration_number,
					'expiration_period' => $level->expiration_period,
					'allow_signups' => 1
				),
				array(
					'%d',		//id
					'%s',		//name
					'%s',		//description
					'%s',		//confirmation
					'%f',		//initial_payment
					'%f',		//billing_amount
					'%d',		//cycle_number
					'%s',		//cycle_period
					'%d',		//billing_limit
					'%f',		//trial_amount
					'%d',		//trial_limit
					'%d',		//expiration_number
					'%s',		//expiration_period
					'%d',		//allow_signups
				)
			);

			if ( isset( $imported_ids[ $level->id ] ) ) {
				$the_id = $imported_ids[ $level->id ];
			} elseif ( ! $wpdb->insert_id ) {
				continue;
			} else {
				$the_id = $wpdb->insert_id;
			}

			$imported_ids[ $level->id ] = $the_id;

			if( isset( $level->seeko_pmpro_color ) ) {
				$color = sanitize_text_field( $level->seeko_pmpro_color );
			} else {
				$color = false;
			}

			if( isset( $level->seeko_pmpro_popular ) ) {
				$popular = 'yes';
			} else {
				$popular = false;
			}

			$options = get_option( get_template() . '_pmpro');
			if ( ! $options ) {
				$options = [];
			}
			$options[ $the_id ] = [
				'color' => $color,
				'popular' => $popular,
			];

			update_option( get_template() . '_pmpro', $options, 'no' );

		}

		update_option( 'svq_' . get_template() . '_import_pmpro_' . $file , $imported_ids );

		return true;
	}

	/** ---------------------------------------------------------------------------
	 * Import | Content
	 *
	 * @param string $file
	 * @param bool $force_attachments
	 *
	 * ---------------------------------------------------------------------------- */
	function import_content( $file = 'all.xml', $force_attachments = false ) {

		if ( ! class_exists( 'WP_Importer' ) || ! class_exists( 'SVQ_WP_Import' ) ) {
			return new WP_Error( '__k__', __( 'SQ Import Helper plugin needs to be active. Please try again.', 'seeko' ) );
		}

		$import = new SVQ_WP_Import();
		$xml    = SVQ_LIB_DIR . '/demo/' . $file;

		if ( true == $force_attachments ) {
			$import->fetch_attachments = true;
		} else {
			$import->fetch_attachments = ( $_POST && array_key_exists( 'attachments', $_POST ) && $_POST['attachments'] ) ? true : false;
		}


		ob_start();
		$import->import( $xml );
		return ob_end_clean();
	}

	function import_sidebars( $path ) {
		//add any extra sidebars
		$sidebars_file_data = wp_remote_get( $path );
		if ( ! is_wp_error( $sidebars_file_data ) ) {
			$sidebars_data = unserialize( wp_remote_retrieve_body( $sidebars_file_data ) );
			$old_sidebars  = get_option( 'sbg_sidebars' );
			if ( ! empty( $old_sidebars ) ) {
				$sidebars_data = array_merge( $sidebars_data, $old_sidebars );
			}
			update_option( 'sbg_sidebars', $sidebars_data );
		}
	}

	/** ---------------------------------------------------------------------------
	 * Parse JSON import file
	 *
	 * @param $json_data
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 * ---------------------------------------------------------------------------- */
	function import_widget_data( $json_data ) {

		$json_data    = json_decode( $json_data, true );
		$sidebar_data = $json_data[0];
		$widget_data  = $json_data[1];

		// prepare widgets table
		$widgets = array();
		foreach ( $widget_data as $k_w => $widget_type ) {
			if ( $k_w ) {
				$widgets[ $k_w ] = array();
				foreach ( $widget_type as $k_wt => $widget ) {
					if ( is_int( $k_wt ) ) {
						$widgets[ $k_w ][ $k_wt ] = 1;
					}
				}
			}
		}

		// sidebars
		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i ++ ) {
				$widget               = array();
				$widget['type']       = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
				if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
					unset( $sidebar_data[ $title ][ $i ] );
				}
			}
			$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
		}

		// widgets
		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value2 ) {
				$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
			}
		}

		$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
		$this->parse_import_data( $sidebar_data );
	}

	/** ---------------------------------------------------------------------------
	 * Import widgets
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 * ---------------------------------------------------------------------------- */
	function parse_import_data( $import_array ) {
		$sidebars_data = $import_array[0];
		$widget_data   = $import_array[1];

		$current_sidebars = get_option( 'sidebars_widgets' );
		$new_widgets      = array();

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			$current_sidebars[ $import_sidebar ] = array();

			foreach ( $import_widgets as $import_widget ) :
				//if the sidebar exists
				if ( isset( $current_sidebars[ $import_sidebar ] ) ) :

					$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					$new_widget_name     = self::get_new_widget_name( $title, $index );
					$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );
					if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
							$new_index ++;
						}
					}
					if ( ! $current_widget_data ) {
						$current_widget_data = array();
					}

					$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
					if ( array_key_exists( $title, $new_widgets ) ) {
						$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
						$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];

						if ( isset( $new_widgets[ $title ]['_multiwidget'] ) ) {
							unset( $new_widgets[ $title ]['_multiwidget'] );
						}

						$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
					} else {
						$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
						$current_multiwidget               = isset( $current_widget_data['_multiwidget'] ) ? $current_widget_data['_multiwidget'] : '';
						$new_multiwidget                   = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
						$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;

						if ( isset( $current_widget_data['_multiwidget'] ) ) {
						unset( $current_widget_data['_multiwidget'] );
						}

						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[ $title ]               = $current_widget_data;
					}
				endif;
			endforeach;
		endforeach;
		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );
			foreach ( $new_widgets as $title => $content ) {
				$content = apply_filters( 'widget_data_import', $content, $title );
				update_option( 'widget_' . $title, $content );
			}

			return true;
		}

		return false;
	}

	/** ---------------------------------------------------------------------------
	 * Get new widget name
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 * ---------------------------------------------------------------------------- */
	function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array();
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( ! empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index ++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;

		return $new_widget_name;
	}

	/**
	 * Import theme options
	 *
	 * @param string $file
	 */
	function import_options( $file = '' ) {
		if ( '' == $file ) {
			return;
		}

		$file_path = SVQ_LIB_DIR . '/demo/options/' . $file . '.txt';
		$file_data = svq_fs_get_contents( $file_path );
		$options   = get_option( 'kleo_' . KLEO_DOMAIN );
		if ( $file_data ) {
			if ( $data = json_decode( $file_data, true ) ) {

				foreach ( $data as $k => $v ) {
					$options[ $k ] = $v;
				}
				$options['stime'] = time();
				update_option( 'kleo_' . KLEO_DOMAIN, $options );

				svq_generate_dynamic_css();

			}
		}

	}

	/**
	 * Check if a Revslider with the given name exists
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function check_existing_slider( $name ) {

		if ( ! class_exists( 'RevSlider' ) ) {
			$this->error = 'Please activate Revolution slider and do the import again!';

			return false;
		}
		$revslider = new RevSlider();
		$sliders   = $revslider->getArrSliders();

		foreach ( $sliders as $slider ) {
			if ( $name == $slider->getAlias() ) {
				return false;
			}
		}

		return true;
	}

	public function check_revslider_file( $file_path, $file_name ) {

		$file_final_path = trailingslashit( $file_path ) . $file_name;

		if ( ! file_exists( $file_final_path ) || 0 < filesize( $file_final_path ) ) {

			if ( ! is_dir( $file_path ) ) {
				wp_mkdir_p( $file_path );
			}

			// Get remote file
			$response = wp_remote_get( 'https://seventhqueen.com' . '/support/files/kleo/revslider/' . $file_name );

			// Check for error
			if ( is_wp_error( $response ) ) {
				$this->error = 'Revolution slider could not be imported. Import manually from WP admin - Revolution Slider';
				$this->error .= '<br><small>Details: ' . $response->get_error_code() . '</small>';

				return false;
			}

			// Parse remote HTML file
			$file_contents = wp_remote_retrieve_body( $response );

			// Check for error
			if ( is_wp_error( $file_contents ) ) {
				$this->error = 'Revolution slider could not be imported. Import manually from WP admin - Revolution Slider';

				return false;
			}

			if ( ! svq_fs_put_contents( $file_final_path, $file_contents ) ) {
				$this->error = 'Revolution slider could not be written to disk. Check file permissions with hosting provider. Import manually from WP admin - Revolution Slider';

				return false;
			}
		}

		return true;
	}

	/** ---------------------------------------------------------------------------
	 * Import | RevSlider
	 *
	 * @param string $path
	 * @param string $name
	 * ---------------------------------------------------------------------------- */
	function import_revslider( $path, $name = '' ) {

		if ( class_exists( 'RevSlider' ) ) {

			ob_start();
			//filename provided without extension
			$full_path = trailingslashit( $path ) . $name . '.zip';

			if ( $this->check_existing_slider( $name ) && file_exists( $full_path ) ) {
				$slider = new RevSlider();
				$slider->importSliderFromPost( true, true, $full_path );
			}
			$this->messages[] = ob_get_clean();
		}

	}

	public function import_bp_fields( $bp_fields, $extra_replace = true ) {
		if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'xprofile' ) ) {
			return;
		}

		$imported_ids = [];
		$existing_ids = [];
		$i            = 0;
		foreach ( $bp_fields as $field ) {

			$i ++;
			if ( ! $existing_ids[$field['old_id']] = xprofile_get_field_id_from_name( $field['name'] ) ) {
				$id             = xprofile_insert_field(
					array(
						'field_group_id' => 1,
						'name'           => $field['name'],
						'can_delete'     => $field['can_delete'],
						'field_order'    => $i,
						'is_required'    => $field['is_required'],
						'type'           => $field['type'],
					)
				);
				$imported_ids[$field['old_id']] = $id;

				if ( $id && isset( $field['options'] ) && ! empty( $field['options'] ) ) {
					$j = 0;
					foreach ( $field['options'] as $option ) {
						$j ++;
						xprofile_insert_field( array(
							'field_group_id' => 1,
							'parent_id'      => $id,
							'type'           => $field['type'],
							'name'           => $option,
							'option_order'   => $j,
						) );
					}
				}
			}
		}

		if ( $extra_replace ) {

			$ids = $existing_ids + $imported_ids;
			$this->replace_sks_data( $ids );
			//$this->replace_bps_data( $ids, 'Main page form' );

		}

	}

	/**
	 * @param string $file
	 *
	 * @return bool
	 */
	public function import_stax_zones( $file = '' ) {

		if ( ! defined( 'STAX_VERSION' ) ) {
			$this->error = 'Stax Header Builder plugin needs to be active';

			return false;
		}

		// Get remote file
		$response = wp_remote_get( $file );

		// Check for error
		if ( is_wp_error( $response ) ) {
			$this->error = 'Stax Import file could not be downloaded. Please manually import the templates.';
			$this->error .= '<br><small>Details: ' . $response->get_error_code() . '</small>';

			return false;
		}

		// Parse remote HTML file
		$file_contents = wp_remote_retrieve_body( $response );

		// Check for error
		if ( is_wp_error( $file_contents ) ) {
			$this->error = 'Stax Import file could not be downloaded. Please manually import the templates.';

			return false;
		}

		$processed_data = \Stax\Import::instance()->process_content( $file_contents );

		if ( $processed_data && ! empty( $processed_data ) ) {
			foreach ( $processed_data as $data ) {

				if ( ! isset( $data['fonts'] ) ) {
					$data['fonts'] = [];
				}

				\Stax\Model_Zones::instance()->make( $data );
			}
		}


		return true;
	}


	public function replace_sks_data( $ids ) {
		if ( ! empty( $ids ) ) {

			$q_args = array(
				'post_type' => 'seeko_form',
				'post_status' => 'publish',
				'posts_per_page' => 3,
				'meta_key'   => 'sq_import',
				'meta_value' => '1',
			);

			$the_query = new WP_Query( $q_args );

			// The Loop
			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					$post_meta = get_post_meta( get_the_ID(), '_seeko_options', true );
					$meta = apply_filters( 'seeko_search_get_form_meta', $post_meta );

					if ( isset( $meta['context'] ) && isset( $meta['context']['members'] ) && isset( $meta['context']['members']['fields'] ) ) {
						foreach ( $meta['context']['members']['fields'] as $k => $item ) {
							if ( isset( $ids[ $item['id'] ] ) ) {
								$meta['context']['members']['fields'][ $k ]['id'] = $ids[ $item['id'] ];
							}
						}
					}

					update_post_meta( get_the_ID(), '_seeko_options', $meta );
					delete_post_meta( get_the_ID(), 'sq_import' );
					update_post_meta( get_the_ID(), '_sq_imported', '1' );
				}

			}
			/* Restore original Post Data */
			wp_reset_postdata();

		}
	}

	public function replace_bps_data( $ids, $page_title ) {
		if ( ! empty( $ids ) ) {

			$field_code = array();
			foreach ( $ids as $id ) {
				$field_code[] = 'field_' . $id;
			}

			//Main page form
			//bps_form
			$args  = array(
				'post_type'  => 'bps_form',
				'title'      => $page_title,
				'meta_key'   => 'sq_import',
				'meta_value' => '1',
			);
			$query = new WP_Query( $args );
			$posts = $query->posts;

			if ( ! empty( $posts ) && is_array( $posts ) ) {
				foreach ( $posts as $post ) {

					$form_values = get_post_meta( $post->ID, 'bps_options' );
					foreach ( $form_values as $form_value ) {
						if ( isset( $form_value['field_name'] ) ) {
							$new_option_value               = $form_value;
							$new_option_value['field_name'] = $ids;
							$new_option_value['field_code'] = $field_code;

							delete_post_meta( $post->ID, 'bps_options' );
							update_post_meta( $post->ID, 'bps_options', $new_option_value );

							update_post_meta( $post->ID, '_sq_imported', '1' );

							break;
						}
					}
				}
			}


			/* Restore original Post Data */
			wp_reset_postdata();
		}
	}

	private function get_imported_posts() {
		$args  = array(
			'post_type'  => array( 'post', 'page', 'elementor_library' ),
			'meta_query' => array(
				array(
					'key'   => 'sq_import',
					'value' => '1',
				),
				array(
					'key'     => '_sq_imported',
					'compare' => 'NOT EXISTS',
				),
			),
		);
		$query = new WP_Query( $args );

		/* Restore original Post Data */
		wp_reset_postdata();

		return $query->posts;
	}

	private function post_process_posts() {

		$upload_dir = wp_upload_dir();
		if ( is_ssl() ) {
			if ( strpos( $upload_dir['baseurl'], 'https://' ) === false ) {
				$upload_dir['baseurl'] = str_ireplace( 'http', 'https', $upload_dir['baseurl'] );
			}
		}
		$this->local_url_base = trailingslashit( $upload_dir['baseurl'] );

		//calculate total images & pre process data

		if ( $this->should_process_step( 'calc_images' ) ) {
			$posts = $this->get_imported_posts();
			foreach ( $posts as $post ) {

				//save the imported page
				if ( 'page' == get_post_type( $post->ID ) ) {
					$this->pages_imported[ $post->ID ] = $post->ID;
				}

				$import_base = '';
				/* set import domain */
				if ( get_post_meta( $post->ID, 'sq_base', true ) ) {
					$import_base = get_post_meta( $post->ID, 'sq_base', true );
				}

				//set import remote base
				if ( $import_base ) {
					$this->remote_url_base = trailingslashit( $import_base );
				}

				do_action( 'sq_import_before_process', $post, $this );

				/* Fetch images for import */
				$this->get_images_from_post( $post );

				/* Try to convert VC Grid ids */
				$this->process_vc_grids( $post );

				/* Set GeoDirectory homepage to imported page */
				$this->set_geodir_home( $post );

				if ( $featured_image = get_post_meta( $post->ID, '_thumbnail_id', true ) ) {
					$this->featured_images[ $post->ID ] = $featured_image;
				}

				do_action( 'sq_import_after_process', $post, $this );

				//add import meta
				add_post_meta( $post->ID, '_sq_imported', 1 );
			}

			$this->set_progress( $this->progress_pid, array(
				'text'              => esc_html__( 'Reading images for the import', 'seeko' ),
				'step'              => 'calc_images',
				'pages_imported'    => $this->pages_imported,
				'remote_url_base'   => $this->remote_url_base,
				'total_images'      => $this->total_images,
				'elementor_images'  => $this->elementor_images,
				'url_remap'         => $this->url_remap,
				'featured_images'   => $this->featured_images,
				'attached_images'   => $this->attached_images,
				'content_images'    => $this->content_images,
				'slide_meta_images' => $this->slide_meta_images,
			), true );
		}

		/* Import images from content */
		$this->process_post_images();

		//set featured images
		$this->remap_featured_images();

		//replace any found images
		$this->replace_attachment_urls();

		//delete meta from imported content
		$this->delete_import_data();

	}

	// return the difference in length between two strings

	public function get_images_from_post( $post ) {

		/* get attached images */
		if ( $attached_images = get_post_meta( $post->ID, 'sq_attach', true ) ) {
			if ( ! empty( $attached_images ) ) {
				$this->attached_images[ $post->ID ] = $attached_images;
				foreach ( $attached_images as $attached_image ) {
					$this->total_images[ md5( $attached_image ) ] = $attached_image;
				}
			}
		}

		$img_data = get_post_meta( $post->ID, 'sq_img_data', true );

		/* Get images from VC single image and VC gallery */
		if ( ! empty( $img_data ) && preg_match_all( '/(images="[0-9,]+")|(include="[0-9,]+")|(image="[0-9]+")/i', $post->post_content, $matches ) ) {

			foreach ( $matches[0] as $match ) {

				//get image links by ids
				$img_id = str_replace( array( 'image="', 'include="', 'images="', '"' ), '', $match );

				if ( isset( $img_data[ $img_id ] ) ) {
					$img_url = $img_data[ $img_id ];


					$img_id_array  = explode( ',', $img_id );
					$img_url_array = explode( ',', $img_url );

					$this->content_images[] = array(
						'post_id'   => $post->ID,
						'id_array'  => $img_id_array,
						'url_array' => $img_url_array,
						'match'     => $match,
						'new_match' => str_replace( $img_id, $img_url, $match ),
					);
					foreach ( $img_url_array as $img_url ) {
						$this->total_images[ md5( $img_url ) ] = $img_url;
					}
				}
			}
		}

		/* Get images from media slider */
		if ( $meta = get_post_meta( $post->ID, '_kleo_slider', true ) ) {

			if ( ! empty( $meta ) ) {
				$this->slide_meta_images[ $post->ID ] = $meta;
				foreach ( $meta as $m ) {
					$this->total_images[ md5( $m ) ] = $m;
				}
			}
		}

		/* get Elementor images */
		if ( $meta = get_post_meta( $post->ID, '_elementor_data', true ) ) {

			if ( $meta && ! empty( $meta ) ) {

				preg_match_all( '/https[^"\']*?(jpg|png|gif|jpeg)/i', $meta, $matches );

				if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {

					$this->elementor_images[ $post->ID ] = $matches[0];
					foreach ( wp_unslash( $matches[0] ) as $m ) {
						$this->total_images[ md5( $m ) ] = $m;
					}
				}
			}

		}

		return false;

	}

	public function process_vc_grids( $post ) {
		$grid_data = get_post_meta( $post->ID, 'sq_vc_grids', true );

		/* Get images from VC single image and VC gallery */
		if ( ! empty( $grid_data ) && preg_match_all( '/item="[0-9]+"/i', $post->post_content, $matches ) ) {

			foreach ( $matches[0] as $match ) {

				//get image links by ids
				$grid_id = str_replace( array( 'item="', '"' ), '', $match );
				if ( isset( $grid_data[ $grid_id ] ) ) {
					$grid_name = $grid_data[ $grid_id ];

					if ( $query = $this->get_post_by_slug( $grid_name ) ) {
						$current_grid              = get_post( $query );
						$this->url_remap[ $match ] = 'item="' . $current_grid->ID . '"';
					}
				}
			}
		}
	}

	public function get_post_by_slug( $slug ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s", $slug ) );
	}

	public function set_geodir_home( $post ) {
		if ( $meta = get_post_meta( $post->ID, '_kleo_header_content', true ) ) {
			if ( strpos( $meta, '[gd_homepage_' ) !== false ) {
				update_option( 'geodir_home_page', $post->ID );
			}
		}
	}

	function process_post_images() {

		$old_base_no_http = str_replace( array( 'http://', 'https://' ), '', $this->remote_url_base );

		//attached images
		if ( ! empty( $this->attached_images ) ) {
			foreach ( $this->attached_images as $post_id => $attached_images ) {
				if ( ! empty( $attached_images ) ) {

					foreach ( $attached_images as $k => $v ) {
						$this->remote_images[ $k ] = $v;
						$this->import_image( $v, $post_id );
					}
				}
			}
		}

		//content images
		if ( ! empty( $this->content_images ) ) {

			foreach ( $this->content_images as $content_image ) {
				$post_id       = $content_image['post_id'];
				$img_id_array  = $content_image['id_array'];
				$img_url_array = $content_image['url_array'];
				$match         = $content_image['match'];
				$new_match     = $content_image['new_match'];

				$count = 0;
				foreach ( $img_url_array as $remote_url ) {

					$this->remote_images[ $img_id_array[ $count ] ] = $remote_url;

					$new_image = $this->import_image( $remote_url, $post_id );
					if ( ! empty( $new_image ) && isset( $new_image['id'] ) ) {
						$new_match = str_replace( $remote_url, $new_image['id'], $new_match );
					}

					$count ++;
				}
				$this->url_remap[ $match ] = $new_match;

			}
			//failsafe domain replace
			$this->url_remap[ 'http://' . $old_base_no_http ]  = $this->local_url_base;
			$this->url_remap[ 'https://' . $old_base_no_http ] = $this->local_url_base;
		}

		//Media slider images
		if ( ! empty( $this->slide_meta_images ) ) {
			foreach ( $this->slide_meta_images as $post_id => $slide_meta_image ) {
				$updated = false;
				foreach ( $slide_meta_image as $key => $slide ) {
					$image = $this->import_image( $slide, $post_id );
					if ( ! empty( $image ) && isset( $image['id'] ) ) {
						$slide_meta_image[ $key ] = $image['url'];
						$updated                  = true;
					}
				}
				if ( $updated ) {
					update_post_meta( $post_id, '_kleo_slider', $slide_meta_image );
				}
			}
		}

		//Update Elementor img
		if ( ! empty( $this->elementor_images ) ) {
			foreach ( $this->elementor_images as $post_id => $el_images ) {
				$updated    = false;
				$new_images = [];

				foreach ( $el_images as $key => $el_img ) {

					$image = $this->import_image( wp_unslash( $el_img ), $post_id );

					if ( ! empty( $image ) && isset( $image['id'] ) ) {
						$new_images[ $key ] = wp_slash( $image['url'] );
						$updated            = true;
					}
				}

				if ( $updated ) {

					$meta = get_post_meta( $post_id, '_elementor_data', true );
					$meta = str_replace( $el_images, $new_images, $meta );

					$sq_attach = $this->attached_images[ $post_id ];
					if ( ! empty( $sq_attach ) ) {

						$new_ids = [];
						preg_match_all( '/"id":([0-9]+)}/', $meta, $matches );
						foreach ( $matches[1] as $k => $match ) {

							if ( isset( $sq_attach[ $match ] ) ) {

								//get local imported id
								$imported_url = $sq_attach[ $match ];
								if ( in_array( $imported_url, $this->images_imported ) ) {
									$new_img_id = array_search( $imported_url, $this->images_imported );
									if ( $new_img_id ) {
										$new_ids[ $k ] = '"id":' . $new_img_id . '}';
									} else {
										unset( $matches[0][ $k ] );
									}
								} else {
									unset( $matches[0][ $k ] );
								}
							} else {
								unset( $matches[0][ $k ] );
							}

						}
						if ( ! empty( $new_ids ) ) {
							$meta = str_replace( $matches[0], $new_ids, $meta );
						}

						//img type 2
						$new_ids = [];
						preg_match_all( '/"image":{"id":([0-9]+),/', $meta, $matches );
						foreach ( $matches[1] as $k => $match ) {

							if ( isset( $sq_attach[ $match ] ) ) {

								//get local imported id
								$imported_url = $sq_attach[ $match ];
								if ( in_array( $imported_url, $this->images_imported ) ) {
									$new_img_id = array_search( $imported_url, $this->images_imported );
									if ( $new_img_id ) {
										$new_ids[ $k ] = '"image":{"id":' . $new_img_id . ',';
									} else {
										unset( $matches[0][ $k ] );
									}

								} else {
									unset( $matches[0][ $k ] );
								}
							} else {
								unset( $matches[0][ $k ] );
							}

						}
						if ( ! empty( $new_ids ) ) {
							$meta = str_replace( $matches[0], $new_ids, $meta );
						}

					}

					//update links
					$meta = str_replace( wp_slash( $this->remote_url_base ), wp_slash( $this->local_url_base ), $meta );

					update_post_meta( $post_id, '_elementor_data', wp_slash( $meta ) );
				}
			}
		}


		return false;
	}

	/**
	 * Import remote image
	 *
	 * @param string $link
	 * @param integer $post_id
	 *
	 * @return mixed|bool|array;
	 */
	private function import_image( $link = '', $post_id = null, $add_count = false ) {

		if ( in_array( $link, $this->failed_images ) ) {
			return false;
		}

		$total_images   = count( $this->total_images );
		$imported_image = array();
		if ( ! $post_id || '' == $link ) {
			return $imported_image;
		}
		$local_url = $this->remote_to_local_url( $link, $post_id );

		//$this->messages[] = 'Importing image: ' . $link;

		if ( null == $this->image_history ) {
			$this->image_history = get_option( 'sq_image_history', array() );
		}

		/* Look in imported images history */
		if ( ! empty( $this->image_history ) ) {
			foreach ( $this->image_history as $item ) {
				if ( $link == $item['remote'] ) {
					$local_url = $item['local'];
				}
			}
		}

		if ( $img_id = attachment_url_to_postid( $local_url ) ) {
			$imported_image['id']             = $img_id;
			$imported_image['url']            = $local_url;
			$this->images_imported[ $img_id ] = $link;

			//$this->messages[] = 'Image already uploaded.';

			return $imported_image;
		}

		//if image is not found locally, continue the quest
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$new_image = media_sideload_image( $link, $post_id, null, 'src' );

		if ( ! is_wp_error( $new_image ) ) {

			$this->messages[] = 'Image just uploaded: ' . $link;

			$img_id                = attachment_url_to_postid( $new_image );
			$imported_image['id']  = $img_id;
			$imported_image['url'] = $new_image;

			$this->images_imported[ $img_id ]    = $link;
			$this->image_history[ md5( $link ) ] = array(
				'remote' => $link,
				'local'  => $new_image,
			);

			if ( ! empty( $this->image_history ) ) {
				update_option( 'sq_image_history', $this->image_history );
			}

			if ( $add_count ) {
				$this->total_images[ md5( $link ) ] = $link;
			}
		} else {
			$this->failed_images[] = $link;
			$this->messages[] = 'Failed to upload: ' . $link . ' Err: ' . $new_image->get_error_message();
		}

		if ( $total_images > 0 ) {
			$text             = 'Importing Images ' . count( $this->images_imported ) . '/' . $total_images;
			$this->messages[] = $text;

			$this->set_progress( $this->progress_pid, array(
				//'text' => implode( ', ', $this->messages ),
				'text'            => $text,
				'images_imported' => $this->images_imported,
				'step'            => 'images',
				'failed_images' => $this->failed_images
			), true );
		}

		return $imported_image;
	}

	public function remote_to_local_url( $url, $post_id ) {
		$remote_base_no_protocol = str_replace( array( 'http://', 'https://' ), '', $this->remote_url_base );
		$url_no_protocol         = str_replace( array( 'http://', 'https://' ), '', $this->local_url_base );

		if ( false !== strpos( $url_no_protocol, $remote_base_no_protocol ) ) {
			$local_url = str_replace( 'https://' . $remote_base_no_protocol, $this->local_url_base, $url );
			$local_url = str_replace( 'http://' . $remote_base_no_protocol, $this->local_url_base, $local_url );
		} else {
			$time = current_time( 'mysql' );
			if ( $post = get_post( $post_id ) ) {
				if ( substr( $post->post_date, 0, 4 ) > 0 ) {
					$time = $post->post_date;
				}
			}
			$uploads   = wp_upload_dir( $time );
			$name      = basename( $url );
			$filename  = wp_unique_filename( $uploads['path'], $name );
			$local_url = $uploads['path'] . "/$filename";
		}

		return $local_url;
	}

	public function remap_featured_images() {
		if ( ! empty( $this->featured_images ) ) {
			foreach ( $this->featured_images as $post_id => $image_id ) {
				if ( isset( $this->remote_images[ $image_id ] ) ) {

					$remote_url = $this->remote_images[ $image_id ];
					$new_image  = $this->import_image( $remote_url, $post_id );
					if ( ! empty( $new_image ) && isset( $new_image['id'] ) ) {
						update_post_meta( $post_id, '_thumbnail_id', $new_image['id'] );
					}
				}
			}
		}
	}

	function replace_attachment_urls() {
		global $wpdb;

		if ( empty( $this->url_remap ) ) {
			return;
		}

		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $this->url_remap, array( $this, 'cmpr_strlen' ) );

		foreach ( $this->url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
			// remap enclosure urls
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url ) );
		}
	}

	/**
	 * Delete post meta required by import logic
	 */
	function delete_import_data() {
		delete_post_meta_by_key( 'sq_img_data' );
		delete_post_meta_by_key( 'sq_attach' );
		delete_post_meta_by_key( 'sq_vc_grids' );
		delete_post_meta_by_key( 'sq_domain' );
		delete_post_meta_by_key( 'sq_base' );
		delete_post_meta_by_key( 'sq_import' );
	}

	function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}

	public function do_import() {
		if ( array_key_exists( 'kleo_import_nonce', $_POST ) ) {

			if ( wp_verify_nonce( $_POST['kleo_import_nonce'], 'import_nonce' ) ) {

				/* DEMO Sets */
				if ( isset( $_POST['import_demo'] ) ) {

					$demo_sets   = self::get_demo_sets();
					$current_set = $_POST['import_demo'];
					$set_data    = $demo_sets[ $current_set ];

					if ( ! array_key_exists( $current_set, $demo_sets ) ) {
						$this->error .= __( 'Something went wrong with the data sent. Please try again.', 'seeko' );
					}

					$data = array();
					foreach ( $_POST as $k => $v ) {
						if ( is_array( $v ) && in_array( $current_set, $v ) ) {
							$data[ $k ][] = $current_set;
						}
					}

					$response = $this->process_import( array(
						'set_data' => $set_data,
						'pid'      => false,
						'data'     => $data,
					) );

					if ( is_wp_error( $response ) ) {
						$this->error .= $response->get_error_message();
					} else {
						$this->data_imported = true;
					}
				} else {

					if ( ! class_exists( 'WP_Importer' ) ) {
						require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
					}

					if ( ! class_exists( 'SVQ_WP_Import' ) ) {
						//require SVQ_WP_Import
					}

					if ( class_exists( 'WP_Importer' ) && class_exists( 'SVQ_WP_Import' ) ) {
						switch ( $_POST['import'] ) {

							case 'all':
								// Full Demo Data ---------------------------------
								$this->import_content();
								$this->import_menu_location();
								$this->import_widget();

								// set home & blog page
								$home = get_page_by_title( 'Home Default' );
								$blog = get_page_by_title( 'Blog' );
								if ( $home->ID && $blog->ID ) {
									update_option( 'show_on_front', 'page' );
									update_option( 'page_on_front', $home->ID ); // Front Page
									update_option( 'page_for_posts', $blog->ID ); // Blog Page
								}
								break;

							case 'content':
								if ( $_POST['content'] ) {
									$_POST['content'] = htmlspecialchars( stripslashes( $_POST['content'] ) );
									$file             = 'content/' . $_POST['content'] . '.xml';
									$this->import_content( $file );

									if ( $_POST['content'] == 'pages' ) {
										// set home & blog page
										$home = get_page_by_title( 'Home Default' );
										$blog = get_page_by_title( 'Blog' );
										if ( $home->ID && $blog->ID ) {
											update_option( 'show_on_front', 'page' );
											update_option( 'page_on_front', $home->ID ); // Front Page
											update_option( 'page_for_posts', $blog->ID ); // Blog Page
										}
									}

								} else {
									$this->import_content();
								}
								break;

							case 'page':
								// page ---------------------------------------
								$_POST['page'] = htmlspecialchars( stripslashes( $_POST['page'] ) );
								$file          = 'pages/' . $_POST['page'] . '.xml';
								$this->import_content( $file );
								break;

							case 'menu':
								// Menu -------------------------------------------
								$this->import_content( 'menu.xml' );
								$this->import_menu_location();
								break;

							case 'widgets':
								// Widgets ----------------------------------------
								$this->import_widget();
								break;

							default:
								// Empty select.import
								$this->error = __( 'Please select data to import.', 'seeko' );
								break;
						}
					}
					$this->data_imported = true;
				}
			}
		}
	}

	/**
	 * Import | Menu - Locations
	 *
	 * @param array $locations Menu locations and names
	 */
	function import_menu_location( $locations = [] ) {

		if ( empty( $locations ) ) {
			return;
		}
		$menus = wp_get_nav_menus();

		$current_menus = get_theme_mod( 'nav_menu_locations' );

		foreach ( $locations as $key => $val ) {
			foreach ( $menus as $menu ) {
				if ( $menu->slug == $val ) {
					$current_menus[ $key ] = absint( $menu->term_id );
				}
			}
		}
		set_theme_mod( 'nav_menu_locations', $current_menus );
	}

	/** ---------------------------------------------------------------------------
	 * Import | Widgets
	 * @return bool
	 * ---------------------------------------------------------------------------- */
	function import_widget() {

		//add any extra sidebars
		$this->import_sidebars( SVQ_LIB_URI . '/demo/sidebar_data.txt' );

		//widgets
		$file_path = SVQ_LIB_URI . '/demo/widget_data.txt';
		$file_data = wp_remote_get( $file_path );
		if ( is_wp_error( $file_data ) ) {
			return false;
		}
		$data = wp_remote_retrieve_body( $file_data );
		$this->import_widget_data( $data );

		return true;
	}

	/** ---------------------------------------------------------------------------
	 * Import
	 * ---------------------------------------------------------------------------- */
	function import() {

		$this->show_message();

		?>

		<div id="kleo-wrapper" class="kleo-import wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>


			<form class="kleo-import-form" action="" method="post"
			      onSubmit="if(!confirm('Really import the data?')){return false;}">

				<input type="hidden" name="kleo_import_nonce" value="<?php echo wp_create_nonce( 'import_nonce' ); ?>"/>

				<h3>Import Demo pack</h3>

				<?php $this->generate_boxes_html(); ?>

				<div class="clear clearfix"></div>

			</form>

		</div>
		<?php
	}

	public function show_message() {
		// message box
		if ( $this->error ) {
			echo '<div class="error settings-error">';
			echo '<p><strong>' . $this->error . '</strong></p>';
			echo '</div>';
		} elseif ( $this->data_imported ) {
			echo '<div class="updated settings-error">';
			echo '<p><strong>' . __( 'Import successful. Have fun!', 'seeko' ) . '</strong></p>';
			echo '</div>';
		}
	}

	public function generate_boxes_html() {
		?>
		<div class="demos-wrapper">

			<?php
			$demo_sets = self::get_demo_sets();

			?>

			<?php foreach ( $demo_sets as $k => $v ) : ?>

				<div class="import-demo">
					<div class="demo-wrapper" <?php if ( isset( $v['slug'] ) ) {
						echo 'data-slug="' . esc_attr( $v['slug'] ) . '"';
					} ?>>
						<?php if ( isset( $v['slug'] ) && $this->get_post_by_slug( $v['slug'] ) ) : ?>
							<div class="sq-imported-label"></div>
						<?php endif; ?>
						<div class="img-wrapper">
							<img src="<?php echo esc_attr( $v['img'] ); ?>">
							<span class="solid-bg"></span>
							<a href="<?php echo esc_url( $v['link'] ); ?>" target="_blank">
								<span class="preview-btn">
									<span
										class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'PREVIEW', 'seeko' ); ?>
								</span>
							</a>
						</div>
						<div class="demo-options">
							<div class="to-left">
								<span class="demo-title"><?php echo esc_html( $v['name'] ); ?></span>
								<div class="demo-checkboxes">
									<?php if ( isset( $v['page'] ) ) : ?>
										<label><input type="checkbox" name="import_page[]" checked
										              value="<?php echo esc_attr( $k ); ?>"
										              class="check-page"> <?php esc_html_e( 'Import Page', 'seeko' ); ?>
										</label>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['extra'] ) && ! empty( $v['extra'] ) ) : ?>
										<?php foreach ( $v['extra'] as $extra ) : ?>
											<?php
											if ( isset( $extra['checked'] ) && $extra['checked'] ) {
												$checked = ' checked="checked"';
											} else {
												$checked = '';
											}
											?>
											<label>
												<input type="checkbox"
												       name="import_<?php echo esc_attr( $extra['id'] ); ?>[]"
												       value="<?php echo esc_attr( $k ); ?>"
												       class="check-page"<?php echo $checked; /* WPCS: XSS OK. */ ?>> <?php echo esc_html( $extra['name'] ); ?>
											</label>
											<br>
										<?php endforeach; ?>
									<?php endif; ?>

									<?php if ( isset( $v['widgets'] ) ) : ?>
										<label><input type="checkbox" name="import_widgets[]" checked
										              value="<?php echo esc_attr( $k ); ?>"> <?php esc_html_e( 'Import Widgets', 'seeko' ); ?>
										</label>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['stax'] ) ) : ?>
										<label><input type="checkbox" name="import_stax[]" checked
										              value="<?php echo esc_attr( $k ); ?>"> <?php esc_html_e( 'Import Stax Templates', 'seeko' ); ?>
										</label>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['revslider'] ) ) : ?>
										<label><input type="checkbox" name="import_revslider[]" checked
										              value="<?php echo esc_attr( $k ); ?>"> <?php esc_html_e( 'Import Revolution Slider', 'seeko' ); ?>
										</label>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['bp_fields'] ) ) : ?>
										<label><input type="checkbox" name="import_bp_fields[]" checked
										              value="<?php echo esc_attr( $k ); ?>"> <?php esc_html_e( 'Import Dummy Profile fields', 'seeko' ); ?>
										</label>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['pmpro'] ) ) : ?>
										<label><input type="checkbox" name="import_pmpro[]" checked
										              value="<?php echo esc_attr( $k ); ?>"> <?php esc_html_e( 'Import Sample Membership Levels', 'seeko' ); ?>
										</label>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['options'] ) ) : ?>
										<label><input type="checkbox" name="import_options[]" checked
										              value="<?php echo esc_attr( $v['options'] ); ?>"> <?php esc_html_e( 'Import Theme options', 'seeko' ); ?>
										</label>
										<?php
										$extra_options_data = __( 'This will change some of your theme options. Make sure to export them first.', 'seeko' );
										echo ' <span class="dashicons dashicons-editor-help tooltip-me" title="' . esc_attr( $extra_options_data ) . '"></span>';
										?>
										<br>
									<?php endif; ?>

									<?php if ( isset( $v['plugins'] ) && ! empty( $v['plugins'] ) ) : ?>
										<label>
											<input type="checkbox" name="activate_plugins[]" checked
											       value="<?php echo esc_attr( $k ); ?>">
											<?php echo esc_html__( 'Activate required plugins', 'seeko' ); ?>
										</label>
										<?php
										$extra_plugin_data = ucwords( str_replace( '-', ' ', implode( ', ', $v['plugins'] ) ) );
										echo ' <span class="dashicons dashicons-editor-help tooltip-me" title="' . esc_attr( $extra_plugin_data ) . '"></span>';
										?>
										<br>
									<?php endif; ?>

									<?php
									$extra_data = isset( $v['details'] ) ? $v['details'] : '';
									if ( '' != $extra_data ) : ?>
										<span
											class="demo-detail">Extra notes: <?php echo wp_kses_post( $extra_data ); ?></span>
									<?php endif; ?>
									<br>
									<small>It is recommended to leave all options checked to reproduce our demo.</small>
									<br>
								</div>
							</div>
							<button type="submit" name="import_demo" value="<?php echo esc_attr( $k ); ?>"
							        class="button button-primary import-demo-btn">Import
							</button>
							<div class="clear clearfix"></div>
						</div>
					</div>
				</div>

			<?php endforeach; ?>

		</div>
		<?php
	}

}
