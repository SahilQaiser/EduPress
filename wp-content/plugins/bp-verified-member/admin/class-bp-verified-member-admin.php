<?php
/**
 * Class BP_Verified_Member_Admin
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-verified-member/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class BP_Verified_Member_Admin
 *
 * @author themosaurus
 * @package bp-verified-member/admin
 */
class BP_Verified_Member_Admin {

	/**
	 * The class managing the plugin meta box.
	 *
	 * @var BP_Verified_Member_Meta_Box
	 */
	public $meta_box;

	/**
	 * The class managing the plugin settings
	 *
	 * @var BP_Verified_Member_Settings
	 */
	public $settings;

	/**
	 * BP_Verified_Member_Admin constructor.
	 */
	public function __construct() {
		$this->meta_box = require 'meta-box/class-bp-verified-member-meta-box.php';
		$this->settings = require 'settings/class-bp-verified-member-settings.php';

		add_action( 'admin_enqueue_scripts',         array( $this, 'enqueue_scripts'             ), 10, 0 );
		add_filter( 'manage_users_columns',          array( $this, 'add_verified_column'         ), 10, 1 );
		add_filter( 'manage_users_custom_column',    array( $this, 'verified_column_content'     ), 10, 3 );
		add_filter( 'bulk_actions-users',            array( $this, 'register_users_bulk_actions' ), 10, 1 );
		add_filter( 'handle_bulk_actions-users',     array( $this, 'handle_users_bulk_actions'   ), 10, 3 );
		add_action( 'admin_notices',                 array( $this, 'users_bulk_action_notice'    ), 10, 0 );
	}

	/**
	 * Enqueue scripts in admin
	 */
	public function enqueue_scripts() {
		// Make sure these are loaded
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script( 'bp-verified-member-admin', BP_VERIFIED_MEMBER_PLUGIN_DIR_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), BP_VERIFIED_MEMBER_VERSION, true );

		global $bp_verified_member;
		$bp_verified_member->enqueue_scripts();
	}

	/**
	 * Add new column in users table.
	 *
	 * @param array $columns The users admin columns.
	 *
	 * @return array $columns The users admin columns.
	 */
	public function add_verified_column( $columns ) {
		$columns['bp-verified-member'] = esc_html__( 'Verified', 'bp-verified-member' );
		return $columns;
	}

	/**
	 * Add verified column content.
	 *
	 * @param string $output The column output.
	 * @param array $column_name The current column name.
	 * @param int $user_id The current user id.
	 *
	 * @return string The column output.
	 */
	public function verified_column_content( $output, $column_name, $user_id ) {
		if ( 'bp-verified-member' === $column_name ) {
			if ( ! empty( get_user_meta( $user_id, $this->meta_box->meta_keys['verified'], true ) ) ) {
				global $bp_verified_member;
				$output .= $bp_verified_member->get_verified_badge();
			}
			else {
				$output .= '<span class="dashicons dashicons-no"></span>';
			}
		}

		return $output;
	}

	/**
	 * Add a bulk action for users
	 *
	 * @param array $bulk_actions The list of bulk actions
	 *
	 * @return array The list of bulk actions
	 */
	public function register_users_bulk_actions( $bulk_actions ) {
		$bulk_actions['bp-verified-member_verify']   = esc_html__( 'Verify', 'bp-verified-member' );
		$bulk_actions['bp-verified-member_unverify'] = esc_html__( 'Unverify', 'bp-verified-member' );
		return $bulk_actions;
	}

	/**
	 * Handle the "verify" bulk actions for users
	 *
	 * @param string $redirect_to The redirection url after the bulk action is processed
	 * @param string $doaction The bulk action being processed
	 * @param array $user_ids The ids of the users being processed
	 *
	 * @return string The redirection url after the bulk action is processed
	 */
	public function handle_users_bulk_actions( $redirect_to, $doaction, $user_ids ) {
		if ( 'bp-verified-member_verify' === $doaction ) {
			foreach ( $user_ids as $user_id ) {
				update_user_meta( $user_id, $this->meta_box->meta_keys['verified'], true );
			}

			$redirect_to = add_query_arg( 'bp-verfied-member_bulk_verified', count( $user_ids ), $redirect_to );
		}
		elseif ( 'bp-verified-member_unverify' === $doaction ) {
			foreach ( $user_ids as $user_id ) {
				update_user_meta( $user_id, $this->meta_box->meta_keys['verified'], false );
			}

			$redirect_to = add_query_arg( 'bp-verfied-member_bulk_unverified', count( $user_ids ), $redirect_to );
		}

		return $redirect_to;
	}

	/**
	 * Display success message after processing users bulk action
	 */
	public function users_bulk_action_notice() {
		if ( ! empty( $_GET['bp-verfied-member_bulk_verified'] ) ) {
			$verified_count = intval( $_GET['bp-verfied-member_bulk_verified'] );

			printf( '<div id="message" class="updated fade"><p>' . _n( 'Verified %s user.', 'Verified %s users.', $verified_count, 'bp-verfied-member_bulk_verified' ) . '</p></div>', $verified_count );
		}

		if ( ! empty( $_GET['bp-verfied-member_bulk_unverified'] ) ) {
			$verified_count = intval( $_GET['bp-verfied-member_bulk_unverified'] );

			printf( '<div id="message" class="updated fade"><p>' . _n( 'Unverified %s user.', 'Unverified %s users.', $verified_count, 'bp-verfied-member_bulk_verified' ) . '</p></div>', $verified_count );
		}
	}
}
