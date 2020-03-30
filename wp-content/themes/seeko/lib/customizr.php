<?php
/**
 * An example file demonstrating how to add all controls.
 *
 * @package     Kirki
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       3.0.12
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A proxy function. Automatically passes-on the config-id.
 *
 * @param array $args The field arguments.
 */
function svq_customizr_add_field( $args ) {
	Seeko_Customizer::add_field( $args );
}

/**
 * First of all, add the config.
 *
 * @link https://aristath.github.io/kirki/docs/getting-started/config.html
 */
Seeko_Customizer::add_config(
	'seeko_options', array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'theme_mod',
	)
);

$all_options = apply_filters( 'svq_theme_settings', array() );

foreach ( $all_options['panels'] as $panel_id => $panel ) {

	Seeko_Customizer::add_panel(
		$panel_id, array(
			'priority'    => $panel['priority'],
			'title'       => $panel['title'],
			'description' => isset( $panel['description'] ) ? $panel['description'] : '',
		)
	);
}
foreach ( $all_options['sec'] as $section_id => $section ) {

	$section_args = array(
		'title'       => $section['title'],
		'description' => isset( $section['description'] )? $section['description'] : '' ,
		'panel'       => isset( $section['panel'] )? $section['panel'] : '' ,
	);
	Seeko_Customizer::add_section( str_replace( '-', '_', $section_id ) , $section_args );
}

foreach ( $all_options['set'] as $section_id => $settings ) {
	foreach ( $settings as $s ) {

		if (isset( $s['type'] ) && $s['type'] == 'image_select' ) {
			$s['type'] = 'radio-image';
			$s['choices'] = [];
			foreach ( $s['options'] as $k => $option ) {
				$s['choices'][$k] = $option['img'];
			}
		}

		if ( isset( $s['id'] ) ) {
			$s['settings'] = $s['id'];
			unset( $s['id'] );
		}

		if ( isset( $s['title'] ) ) {
			$s['label'] = $s['title'];
			unset( $s['title'] );
		}

		svq_customizr_add_field( $s );
	}

}

function seeko_kirki_styling( $config ) {
	return wp_parse_args( array(
		'disable_loader' => true,
	), $config );
}
add_filter( 'kirki_config', 'seeko_kirki_styling' );

/**
 * Allow to remove method for an hook when, it's a class method used and class don't have variable, but you know the class name
 *
 * @param string $hook_name
 * @param string $class_name
 * @param string $method_name
 * @param int $priority
 *
 * @return bool
 */
function svq_remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;
	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}
	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
				// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
				if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
				} else {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}
	}
	return false;
}

if ( class_exists( 'Kirki' ) ) {

	/* Enable google link*/
	if ( svq_option( 'webfonts_method', 'embed' ) == 'link' ) {
		require_once SVQ_FW_DIR . '/lib/customizr/google_link.php';
		add_filter( 'kirki_googlefonts_load_method', function() {
			return 'link';
		} );
		add_action('wp', function() {
			svq_remove_filters_for_anonymous_class( 'wp', 'Kirki_Modules_Webfonts_Embed', 'init', 9 );
			svq_remove_filters_for_anonymous_class( 'kirki_dynamic_css', 'Kirki_Modules_Webfonts_Embed', 'the_css', 11 );
		}, 8);
	}

	if ( ! is_customize_preview() ) {
		add_filter( 'kirki_seeko_options_stylesheet', function () {
			return 'svq-dynamic';
		} );
	}

	if ( class_exists('Kirki_Modules')) {
		$active_modules  = Kirki_Modules::get_active_modules();
		$css_var_modules = $active_modules['css-vars'];

		remove_action( 'wp_head', array( $css_var_modules, 'the_style' ), 0 );
	}

}

add_filter( 'kirki_enqueue_google_fonts', function ( $fonts ) {
	if( ! is_singular() && ! is_admin() ) {
		$quote_val = svq_option( 'font_family_distinct', [ 'font-family' => 'Merriweather'] );
		if ( $quote_val && isset( $quote_val['font-family'] ) ) {
			unset( $fonts[ $quote_val['font-family'] ] );
		}
	}
	return $fonts;
});