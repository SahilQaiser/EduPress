<?php
/**
 * Adds utils.
 *
 * @package Seventor
 * @since 1.0.0
 */

namespace Seventor;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;

/**
 * Seventor utils class.
 *
 * Seventor utils handler class is responsible for different utility methods
 * used by Seventor.
 *
 * @since 1.0.0
 */
class Utils {



	/**
	 * Get site domain.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Site domain.
	 */
	public static function get_site_domain() {
		// @codingStandardsIgnoreStart
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );

		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		return $sitename;
		// @codingStandardsIgnoreEnd
	}


	/**
	 * Get responsive class base on settings key.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $prefix Before class string.
	 * @param  array  $key Settings key.
	 * @param  string $settings Settings stored.
	 *
	 * @return string Responsive class.
	 */
	public static function get_responsive_class( $prefix = '', $key = '', $settings ) {
		if ( empty( $prefix ) || empty( $key ) ) {
			return;
		}

		$devices = [
			\Elementor\Controls_Stack::RESPONSIVE_DESKTOP,
			\Elementor\Controls_Stack::RESPONSIVE_TABLET,
			\Elementor\Controls_Stack::RESPONSIVE_MOBILE,
		];

		$classes = [];

		foreach ( $devices as $device_name ) {
			$temp_key = \Elementor\Controls_Stack::RESPONSIVE_DESKTOP === $device_name ? $key : $key . '_' . $device_name;

			if ( ! isset( $settings[ $temp_key ] ) ) {
				return;
			}

			$device = \Elementor\Controls_Stack::RESPONSIVE_DESKTOP === $device_name ? '' : '-' . $device_name;

			$classes[] = sprintf( $prefix . $settings[ $temp_key ], $device );
		}

		return implode( ' ', $classes );
	}


	/**
	 * Get element settings recursively.
	 *
	 * Retrieve specific element settings by model ID.
	 *
	 * @param  array  $elements Page elements.
	 * @param  string $model_id Element model id.
	 *
	 * @return array|false Return array if element found.
	 */
	public static function find_element_recursive( $elements, $model_id ) {
		foreach ( $elements as $element ) {
			if ( $model_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = self::find_element_recursive( $element['elements'], $model_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Wrapper around the core WP get_plugins function, making sure it's actually available.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $plugin_folder Optional. Relative path to single plugin folder.
	 *
	 * @return array Array of installed plugins with plugin information.
	 */
	public static function get_plugins( $plugin_folder = '' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins( $plugin_folder );
	}

	/**
	 * Checks if a plugin is installed. Does not take must-use plugins into account.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $slug Required. Plugin slug.
	 *
	 * @return bool True if installed, false otherwise.
	 */
	public static function is_plugin_installed( $slug ) {
		return ! empty( self::get_plugins( '/' . $slug ) );
	}

	/**
	 * Get automatic direction based on RTL/LTR.
	 *
	 * @since 1.0.0
	 *
	 * @param string $direction The direction.
	 *
	 * @return string The direction.
	 */
	public static function get_direction( $direction ) {
		if ( ! is_rtl() ) {
			return $direction;
		}

		if ( false !== stripos( $direction, 'left' ) ) {
			return str_replace( 'left', 'right', $direction );
		}

		if ( false !== stripos( $direction, 'right' ) ) {
			return str_replace( 'right', 'left', $direction );
		}

		return $direction;
	}

	/**
	 * Get post ID based on document.
	 *
	 * @since 1.0.0
	 */
	public static function get_current_post_id() {
		if ( isset( Elementor::$instance->documents ) ) {
			return Elementor::$instance->documents->get_current()->get_main_id();
		}

		return get_the_ID();
	}
}
