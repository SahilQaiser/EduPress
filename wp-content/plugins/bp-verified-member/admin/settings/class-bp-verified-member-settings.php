<?php
/**
 * Class BP_Verified_Member_Settings
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-verified-member/admin/settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BP_Verified_Member_Settings' ) ) :
	/**
	 * Class BP_Verified_Member_Settings
	 *
	 * @author themosaurus
	 * @package bp-verified-member/admin/settings
	 */
	class BP_Verified_Member_Settings {

		/**
		 * Option page slug.
		 *
		 * @var string
		 */
		private $page_slug = 'bp-verified-member';

		/**
		 * Option group slug.
		 *
		 * @var string
		 */
		private $option_group = 'bp_verified_member';

		/**
		 * Options default values.
		 *
		 * @var array
		 */
		private $defaults = array();

		/**
		 * BP_Verified_Member_Settings constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_option_page' ) );
			add_action( 'admin_init', array( $this, 'page_init'       ) );

			add_filter( 'bp_core_get_admin_tabs', array( $this, 'add_tab_in_buddypress_settings' ), 10, 1 );
			add_action( 'bp_admin_head',          array( $this, 'remove_settings_submenu_link'   ), 999   );

			$this->defaults = array(
				"{$this->option_group}_display_badge_in_activity_stream"  => 1,
				"{$this->option_group}_display_badge_in_profile_username" => 1,
				"{$this->option_group}_display_badge_in_profile_fullname" => 0,
				"{$this->option_group}display_badge_in_members_lists"     => 1,
				"{$this->option_group}_display_badge_in_bbp_topics"       => 1,
				"{$this->option_group}_display_badge_in_bbp_replies"      => 1,
				"{$this->option_group}_display_badge_in_wp_comments"      => 1,
				"{$this->option_group}_badge_color"                       => '#1DA1F2',
			);
		}

		/**
		 * Add options page.
		 */
		public function add_option_page() {
			$hook = add_options_page(
				esc_html__( 'Verified Member Settings', 'bp-verified-member' ),
				esc_html__( 'Verified Member for BuddyPress', 'bp-verified-member' ),
				'manage_options',
				$this->page_slug,
				array( $this, 'render_settings_page' )
			);

			add_action( "admin_head-$hook", 'bp_core_modify_admin_menu_highlight' );
		}

		/**
		 * Options page callback.
		 */
		public function render_settings_page() {
			?>

			<div class="wrap">
				<h1><?php esc_html__( 'Verified Member Settings', 'bp-verified-member' ); ?></h1>

				<h2 class="nav-tab-wrapper"><?php bp_core_admin_tabs( __( 'Verified Member', 'bp-verified-member' ) ); ?></h2>
				<form method="post" action="options.php">
					<?php
					// This prints out all hidden setting fields
					settings_fields( $this->option_group );
					do_settings_sections( $this->page_slug );
					submit_button();
					?>
				</form>
			</div>

			<?php
		}

		/**
		 * Get an option with default value.
		 *
		 * @param string $option_name The requested option name.
		 *
		 * @return mixed The requested option.
		 */
		public function get_option( $option_name ) {
			// Prefix $option_name if not already prefixed
			if ( substr( $option_name, 0, strlen( $this->option_group ) ) !== $this->option_group ) {
				$option_name = $this->option_group . '_' . $option_name;
			}

			return get_option( $option_name, $this->defaults[ $option_name ] );
		}

		/**
		 * Register and add settings and settings fields.
		 */
		public function page_init() {
			$settings = array(
				"{$this->option_group}_style_section"     => array(
					'title'  => esc_html__( 'Style Settings', 'bp-verified-member' ),
					'fields' => array(
						"{$this->option_group}_badge_color" => array(
							'label' => esc_html__( 'Verified Badge Color', 'bp-verified-member' ),
							'type'  => 'color',
						),
					),
				),
				"{$this->option_group}_activity_section"  => array(
					'title'  => esc_html__( 'Activities Settings', 'bp-verified-member' ),
					'fields' => array(
						"{$this->option_group}_display_badge_in_activity_stream" => array(
							'label'       => esc_html__( 'Display in Activities', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in Activity Stream', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
					),
				),
				"{$this->option_group}_profile_section"   => array(
					'title'  => esc_html__( 'Profiles Settings', 'bp-verified-member' ),
					'fields' => array(
						"{$this->option_group}_display_badge_in_profile_username" => array(
							'label'       => esc_html__( 'Display in Username', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in Profile Username', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
						"{$this->option_group}_display_badge_in_profile_fullname" => array(
							'label'       => esc_html__( 'Display in Fullname', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in Profile Fullname', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
					),
				),
				"{$this->option_group}_directory_section" => array(
					'title'  => esc_html__( 'Directories Settings', 'bp-verified-member' ),
					'fields' => array(
						"{$this->option_group}_display_badge_in_members_lists" => array(
							'label'       => esc_html__( 'Display in Directories', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in Members Lists', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
					),
				),
				"{$this->option_group}_bbp_section"       => array(
					'title'  => esc_html__( 'bbPress Settings', 'bp-verified-member' ),
					'fields' => array(
						"{$this->option_group}_display_badge_in_bbp_topics" => array(
							'label'       => esc_html__( 'Display in Topics', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in BBPress Topics', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
						"{$this->option_group}_display_badge_in_bbp_replies" => array(
							'label'       => esc_html__( 'Display in Replies', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in BBPress Replies', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
					),
				),
				"{$this->option_group}_wp_section"        => array(
					'title'  => esc_html__( 'WordPress Settings', 'bp-verified-member' ),
					'fields' => array(
						"{$this->option_group}_display_badge_in_wp_comments" => array(
							'label'       => esc_html__( 'Display in Comments', 'bp-verified-member' ),
							'description' => esc_html__( 'Display Verified Badge in WordPress Comments', 'bp-verified-member' ),
							'type'        => 'checkbox',
						),
					),
				),
			);

			foreach ( $settings as $section_id => $section ) {

				// Don't show bbPress settings if bbPress isn't activated
				if ( "{$this->option_group}_bbp_section" === $section_id && ! function_exists( 'bbpress' ) ) {
					continue;
				}

				add_settings_section(
					$section_id, // ID
					$section['title'], // Title
					'__return_false', // Callback
					$this->page_slug // Page
				);

				foreach ( $section['fields'] as $field_id => $field ) {
					register_setting(
						$this->option_group, // Option group
						$field_id, // Option name
						array( $this, "sanitize_{$field['type']}" ) // Sanitize
					);

					add_settings_field(
						$field_id, // ID
						$field['label'], // Title
						array( $this, "render_{$field['type']}_field" ), // Callback
						$this->page_slug, // Page
						$section_id, // Section
						array( 'id' => $field_id, 'description' => ! empty( $field['description'] ) ? $field['description'] : '' ) // Callback args
					);
				}
			}
		}

		/**
		 * Sanitize checkbox field.
		 *
		 * @param mixed $input Contains the setting value.
		 */
		public function sanitize_checkbox( $input ) {
			return ! empty( $input ) ? 1 : 0;
		}

		/**
		 * Sanitize color field.
		 *
		 * @param mixed $input Contains the setting value.
		 */
		public function sanitize_color( $input ) {
			return sanitize_hex_color( $input );
		}

		/**
		 * Render a checkbox field.
		 *
		 * @param array $args Field args.
		 */
		public function render_checkbox_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			$checked = ! empty( $this->get_option( $args['id'] ) ) ? 'checked' : '';
			?>
			<input type="checkbox" id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" <?php echo esc_attr( $checked ); ?> />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['description'] ); ?></label>
			<?php endif; ?>
			<?php
		}

		/**
		 * Render a color field.
		 *
		 * @param array $args Field args.
		 */
		public function render_color_field( $args ) {
			if ( empty( $args['id'] ) ) {
				return;
			}

			?>
			<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" value="<?php echo esc_attr( $this->get_option( $args['id'] ) ); ?>" class="color-picker" />
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<label for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo esc_html( $args['description'] ); ?></label>
			<?php endif; ?>
			<?php
		}

		/**
		 * Add new tab to BuddyPress settings.
		 *
		 * @param array $tabs The array of tabs.
		 *
		 * @return array The modified array of tabs.
		 */
		public function add_tab_in_buddypress_settings( $tabs ) {
			$tabs['bp_verified_member'] = array(
				'href' => bp_get_admin_url( add_query_arg( array( 'page' => 'bp-verified-member' ), 'options-general.php' ) ),
				'name' => __( 'Verified Member', 'bp-verified-member' ),
			);

			return $tabs;
		}

		/**
		 * Remove submenu link from the Settings menu.
		 */
		public function remove_settings_submenu_link() {
			remove_submenu_page( 'options-general.php', $this->page_slug );
		}
	}

endif;

return new BP_Verified_Member_Settings();
