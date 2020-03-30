<?php

class SVQ_Pmpro {

	/**
	 * @var SVQ_Pmpro The single instance of the class
	 * @since 1.01
	 */
	protected static $_instance = null;

	/**
	 * Main SVQ_FW Instance
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @return SVQ_Pmpro - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->init();
	}

	public function init() {
		/* admin changes */
		add_action( 'pmpro_membership_level_after_other_settings', [ $this, 'level_settings' ] );
		add_action( 'pmpro_save_membership_level', [ $this, 'save_membership_level' ] );
		add_filter( 'pmpro_level_description', [ $this, 'description_with_no_list' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		/* add options */
		add_filter( 'svq_theme_settings', [ $this, 'theme_settings' ] );

		if ( svq_option( 'pmpro_bp_register', 1 ) ) {
			add_action( 'bp_before_registration_submit_buttons', [ $this, 'bp_register' ], 22 );

			add_filter( 'bp_signup_usermeta', [ $this, 'save_register_meta' ] );
			add_action( 'bp_core_activated_user', [ $this, 'add_user_meta' ], 10, 3 );

			add_filter( 'login_redirect', [ $this, 'redirect_to_membership' ], 20, 3 );
			add_action( 'pmpro_after_checkout', [ $this, 'after_checkout' ] );
		}
	}

	public function enqueue_scripts() {
		if ( ! file_exists( get_stylesheet_directory_uri() . '/paid-memberships-pro/css/frontend.css' ) ) {
			$min = '';
			if ( svq_option( 'dev_mode', false ) == false ) {
				$min = '.min';
			}
			wp_deregister_style( 'pmpro_frontend' );
			wp_dequeue_style( 'pmpro_frontend' );

			wp_register_style( 'pmpro-frontend', get_template_directory_uri() . '/paid-memberships-pro/css/frontend' . $min . '.css', array(), PMPRO_VERSION, 'screen' );
			wp_enqueue_style( 'pmpro-frontend' );
		}

		wp_dequeue_script( 'pmpro-blocks-frontend-js' );

		wp_dequeue_style( 'pmpro_print' );
	}

	public function add_user_meta( $user_id, $key, $user ) {
		if ( isset( $user['meta']['bp_pmpro_level'] ) && ! empty( $user['meta']['bp_pmpro_level'] ) ) {
			update_user_meta( $user_id, 'bp_pmpro_level', $user['meta']['bp_pmpro_level'] );
		}
	}

	public function after_checkout( $user_id ) {
		$meta = get_user_meta( $user_id, 'bp_pmpro_level' );
		if ( $meta ) {
			delete_user_meta( $user_id, 'bp_pmpro_level' );
		}
	}

	public function redirect_to_membership( $redirect_to, $request, $user ) {

		global $wpdb;
		//is there a user to check?
		if ( isset( $user->ID ) ) {

			if ( pmpro_isAdmin( $user->ID ) ) {
				return $redirect_to;
			}

			if ( $wpdb->get_var( "SELECT membership_id FROM $wpdb->pmpro_memberships_users WHERE status = 'active' AND user_id = '" . $user->ID . "' LIMIT 1" ) ) {
				return $redirect_to;
			}

			$meta = get_user_meta( $user->ID, 'bp_pmpro_level', true );
			if ( $meta ) {
				$redirect_to = pmpro_url( 'checkout' ) . '?level=' . $meta;
			} else {
				$redirect_to = pmpro_url();
			}
		}

		return $redirect_to;
	}

	public function save_register_meta( $meta ) {

		if ( isset( $_REQUEST['bp_pmpro_level'] ) ) {

			$meta['bp_pmpro_level'] = $_REQUEST['bp_pmpro_level'];
		}

		return $meta;
	}

	public function theme_settings( $sq ) {
		$sq['sec']['svq_section_pmpro'] = array(
			'title'    => esc_html__( 'PMPRO Memberships', 'seeko' ),
			'panel'    => 'seeko',
			'priority' => 31
		);

		$sq['set']['svq_section_pmpro'][] = array(
			'id'          => 'pmpro_bp_register',
			'type'        => 'switch',
			'title'       => esc_html__( 'BuddyPress Register integration', 'seeko' ),
			'default'     => 1,
			'section'     => 'svq_section_pmpro',
			'description' => esc_html__( 'Add the membership levels at registration. Users can choose a membership, confirm their account and redirected at login to pay for the selected membership', 'seeko' ),
			'transport'   => 'postMessage',
		);

		return $sq;

	}


	public function bp_register() {
		if ( ! function_exists( 'pmpro_url' ) ) {
			return;
		}

		$levels = pmpro_getAllLevels( false, true );
		if ( empty( $levels ) ) {
			return;
		}

		$temp_content = pmpro_loadTemplate( 'levels', 'local', 'pages' );

		?>

		<script>
			window.addEventListener('DOMContentLoaded', function () {
				jQuery(document).ready(function () {
					jQuery('.btn-pricing a').on('click', function (e) {
						e.preventDefault();


						if (jQuery(this).closest('form').find('input[name=bp_pmpro_level]').length === 0) {
							jQuery('<input>').attr({
								type: 'hidden',
								name: 'bp_pmpro_level'
							}).appendTo('form');
						}

						var levelId = 0;
						var levelIds = jQuery(this).attr('href').split('?', 2);
						if (1 in levelIds) {
							levelId = levelIds[1].replace('level=', '');
						}

						jQuery('input[name=bp_pmpro_level]').val(levelId);

						jQuery('#signup_submit').trigger('click');
						return false;
					});
				});
			});
		</script>
		<style>
			#signup_submit {
				display: none;
			}

			.pricing-card .btn-pricing {
				height: auto !important;
			}

			.pricing-card .btn-pricing .btn {
				opacity: 1 !important;
			}

			.pricing-card:hover .pricing-price {
				padding: 1.125rem 0 !important;
			}
		</style>

		<?php

		$levels_page = apply_filters( "pmpro_pages_shortcode_levels", $temp_content );

		$levels_page = str_replace( esc_html__( 'Select', 'paid-memberships-pro' ), esc_html__( 'Select & Sign Up', 'seeko' ), $levels_page );
		// This variable has been safely escaped in the logic above
		echo $levels_page; // WPCS: XSS OK.
	}

	public function level_settings() {
		$options = [];

		if ( isset( $_REQUEST['edit'] ) ) {
			$level_id    = intval( $_REQUEST['edit'] );
			$all_options = get_option( 'seeko_pmpro' );

			if ( isset( $all_options[ $level_id ] ) ) {
				$options = $all_options[ $level_id ];
			}

		}

		$color = 'normal';
		if ( isset( $options['color'] ) ) {
			$color = $options['color'];
		}
		$popular = 'no';
		if ( isset( $options['popular'] ) ) {
			$popular = $options['popular'];
		}

		$color_options = [
			'normal'       => 'Normal',
			'basic'        => 'Basic',
			'intermediate' => 'Intermediate',
			'pro'          => 'Pro',
		];

		$popular_options = [
			'no'  => 'No',
			'yes' => 'Yes',
		];
		?>

		<h3 class="topborder"><?php esc_html_e( 'SEEKO Theme Settings', 'seeko' ); ?></h3>

		<div class="seeko-pmpro">
			<label for="seeko_pmpro_color"><strong>Level styling</strong></label>
			<select id="seeko_pmpro_color" name="seeko_pmpro_color">
				<?php foreach ( $color_options as $k => $color_option ) : ?>
					<option <?php selected( $color, $k ); ?>
						value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $color_option ); ?></option>
				<?php endforeach; ?>
			</select><br>
			<?php esc_html_e( 'Choose the styling of the level on the Membership levels Page', 'seeko' ); ?>
			<br><br>

			<label for="seeko_pmpro_popular"><strong>Popular level</strong></label>
			<select id="seeko_pmpro_color" name="seeko_pmpro_popular">
				<?php foreach ( $popular_options as $k => $popular_option ) : ?>
					<option <?php selected( $popular, $k ); ?>
						value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $popular_option ); ?></option>
				<?php endforeach; ?>
			</select><br>
			<?php esc_html_e( 'Adds a Popular label on Memberships Levels Page', 'seeko' ); ?>
		</div>

		<?php


	}

	public function save_membership_level( $level_id ) {

		if ( $level_id <= 0 ) {
			return;
		}

		if ( isset( $_REQUEST['seeko_pmpro_color'] ) ) {
			$color = sanitize_text_field( $_REQUEST['seeko_pmpro_color'] );
		} else {
			$color = false;
		}

		if ( isset( $_REQUEST['seeko_pmpro_popular'] ) ) {
			$popular = sanitize_text_field( $_REQUEST['seeko_pmpro_popular'] );
		} else {
			$popular = false;
		}

		$options = get_option( 'seeko_pmpro' );
		if ( ! $options ) {
			$options = [];
		}
		$options[ $level_id ] = [
			'color'   => $color,
			'popular' => $popular,
		];

		update_option( 'seeko_pmpro', $options, 'no' );
	}

	public function list_from_description( $description ) {

		$regex = '~<li.*?>(.*?)</li>~is';
		preg_match_all( $regex, $description, $items );

		if ( isset( $items[1] ) && ! empty( $items[1] ) ) {
			$output = '<ul class="pricing-features list-unstyled">';
			foreach ( $items[1] as $item ) {
				if ( strpos( $item, '[x]' ) !== false ) {
					$item   = str_replace( '[x]', '', $item );
					$output .= '<li class="unavailable"><span>' . $item . '</span></li>';
				} else {
					$output .= '<li><span>' . $item . '</span></li>';
				}
			}
			$output .= '</ul>';
			$output .= preg_replace( '~<ul.*?>(.*?)</ul>~is', '', $description, 1 );

			return $output;
		}

		return $description;
	}

	public function description_with_no_list( $description ) {
		$regex  = '~<ul.*?>(.*?)</ul>~is';
		$output = preg_replace( $regex, '', $description, 1 );
		if ( $output == '<p>&nbsp;</p>' ) {
			$output = '';
		}

		return $output;
	}
}

SVQ_Pmpro::instance();