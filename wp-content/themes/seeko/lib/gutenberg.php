<?php

add_filter( 'block_editor_settings', 'svq_gutenberg_enqueue' );

/**
 * Enqueue styles to Gutenberg Editor.
 *
 * @param $settings
 * @return array
 */
function svq_gutenberg_enqueue( $settings ) {
	$file_location = trailingslashit( SVQ_FW::get_config( 'custom_style_path' ) ) . SVQ_FW::get_config( 'custom_style_name' );
	if ( ! file_exists( $file_location ) ) {
		$file_location = get_template_directory() . '/assets/theme-dynamic.css';
	}
	$styles = svq_fs_get_contents( $file_location );
	$styles = str_replace( ', button,', ', .svq-button,', $styles );

	if ( ! empty( $styles ) ) {
		$settings['styles'][] = array( 'css' => $styles );
	}

	return $settings;
}


function svq_gutenberg_supported_features() {

	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	//add_theme_support( 'wp-block-styles' );

	add_theme_support( 'editor-color-palette', array(

		array(
			'name' => esc_html__( 'Primary theme color', 'seeko' ),
			'slug' => 'primary',
			'color' => svq_option('primary', '#F21483'),
		),
		array(
			'name' => esc_html__( 'Secondary theme color', 'seeko' ),
			'slug' => 'secondary',
			'color' => svq_option('secondary', '#7c7c7c'),
		),
		array(
			'name' => esc_html__( 'Tertiary theme color', 'seeko' ),
			'slug' => 'tertiary',
			'color' => svq_option('tertiary', '#555EFF'),
		),
		array(
			'name' => esc_html__( 'Pale pink', 'seeko' ),
			'slug' => 'pale-pink',
			'color' => '#f78da7',
		),
		array(
			'name' => esc_html__( 'Vivid red', 'seeko' ),
			'slug' => 'vivid-red',
			'color' => '#cf2e2e',
		),
		array(
			'name' => esc_html__( 'Luminous vivid orange', 'seeko' ),
			'slug' => 'luminous-vivid-orange',
			'color' => '#ff6900',
		),
		array(
			'name'  => esc_html__( 'Luminous vivid amber', 'seeko' ),
			'slug'  => 'luminous-vivid-amber',
			'color' => '#fcb900',
		),
		array(
			'name'  => esc_html__( 'Light green cyan', 'seeko' ),
			'slug'  => 'light-green-cyan',
			'color' => '#7bdcb5',
		),
		array(
			'name'  => esc_html__( 'Vivid green cyan', 'seeko' ),
			'slug'  => 'vivid-green-cyan',
			'color' => '#00d084',
		),
		array(
			'name'  => esc_html__( 'Pale cyan blue', 'seeko' ),
			'slug'  => 'pale-cyan-blue',
			'color' => '#8ed1fc',
		),
		array(
			'name'  => esc_html__( 'Vivid cyan blue', 'seeko' ),
			'slug'  => 'vivid-cyan-blue',
			'color' => '#0693e3',
		),
		array(
			'name'  => esc_html__( 'Very light gray', 'seeko' ),
			'slug'  => 'very-light-gray',
			'color' => '#eeeeee',
		),
		array(
			'name'  => esc_html__( 'Cyan bluish gray', 'seeko' ),
			'slug'  => 'cyan-bluish-gray',
			'color' => '#abb8c3',
		),
		array(
			'name'  => esc_html__( 'Very dark gray', 'seeko' ),
			'slug'  => 'very-dark-gray',
			'color' => '#313131',
		),
	) );
}

add_action( 'after_setup_theme', 'svq_gutenberg_supported_features', 16 );

