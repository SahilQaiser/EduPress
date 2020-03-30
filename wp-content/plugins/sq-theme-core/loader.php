<?php
/**
 * Plugin Name: SQ Theme Core
 * Plugin URI:  https://seventhqueen.com/
 * Description: Enables Importing, Theme options and features for SeventhQueen Themes
 * Author:      SeventhQueen
 * Version:     1.7.0
 * Text Domain: sq-theme-core
 * Domain Path: /languages
 * License:     GPLv2 or later (license.txt)
 */

define( 'SVQ_CORE_VERSION', '1.7.0' );
define( 'SVQ_CORE_FILE', __FILE__ );
define( 'SVQ_CORE_BASE_URL', plugins_url( '/', SVQ_CORE_FILE ) );
define( 'SVQ_CORE_BASE_PATH', plugin_dir_path( SVQ_CORE_FILE ) );

require_once SVQ_CORE_BASE_PATH . '/inc/sq-import/import.php';
require_once SVQ_CORE_BASE_PATH . '/inc/customizr/kirki.php';
require_once SVQ_CORE_BASE_PATH . '/inc/seventor/plugin.php';

if ( ! function_exists( 'svq_load_carbon' ) ) {
	/**
	 * Load Carbon Fields framework
	 */
	function svq_load_carbon() {
		if ( ! class_exists( '\Carbon_Fields\Container' ) ) {
			$carbon_path = SVQ_CORE_BASE_PATH . '/inc/carbon-fields/vendor/autoload.php';
			if ( file_exists( $carbon_path )
			     && current_theme_supports( 'svq-carbon-fields' ) && ! is_customize_preview() ) {
				include_once $carbon_path;

				\Carbon_Fields\Carbon_Fields::boot();
			}

		}
	}
}
add_action( 'after_setup_theme', 'svq_load_carbon', 12 );
