<?php

/***************************************************
 * :: Header section render
 ***************************************************/

function svq_show_header() {
	svq_get_template_part( 'page-parts/header' );
}

add_action( 'svq_header', 'svq_show_header' );


/***************************************************
 * :: Footer section render
 ***************************************************/

function svq_show_footer() {
	get_sidebar( 'footer' );
	get_template_part( 'page-parts/footer-socket' );
}

add_action( 'svq_footer', 'svq_show_footer' );


/***************************************************
 * :: Single section render
 ***************************************************/

function svq_show_single() {
	get_template_part( 'page-parts/post/single' );
}

add_action( 'svq_single', 'svq_show_single' );

/***************************************************
 * :: Archive section render
 ***************************************************/

function svq_show_archive() {
	get_template_part( 'page-parts/archive' );
}

add_action( 'svq_archive', 'svq_show_archive' );


/***************************************************
 * :: Add body classes
 ***************************************************/

add_filter( 'body_class', 'svq_body_classes' );

/*
 * Adds specific classes to body element
 *
 * @param array $classes
 * @return array
 * @since 1.0
 */
function svq_body_classes( $classes = array() ) {

	if ( is_admin_bar_showing() && svq_option( 'admin_bar', 1 ) == 1 ) {
		$classes[] = 'adminbar-enable';
	}

	//is customizer
	if ( is_customize_preview() ) {
		$classes[] = 'customize-preview';
	}

	if ( svq_option( 'enable_shadow', 1, true ) ) {
		$classes[] = 'enable-shadow';
	}

	//footer is inactive
	if ( ! is_active_sidebar( 'footer-1' ) && ! is_active_sidebar( 'footer-2' ) && ! is_active_sidebar( 'footer-3' )
	     && ! is_active_sidebar( 'footer-4' ) ) {
		SVQ_FW::set_config( 'footer_inactive', true );
	}

	return $classes;
}


/***************************************************
 * :: Dynamic variables
 ***************************************************/

SVQ_FW::set_config( 'colors',
	array(
		'primary'   => [ 'title' => esc_html__( 'Primary color', 'seeko' ), 'type' => 'color', 'default' => '#F21483' ],
		'secondary' => [
			'title'   => esc_html__( 'Secondary color', 'seeko' ),
			'type'    => 'color',
			'default' => '#7c7c7c'
		],
		'tertiary'  => [
			'title'   => esc_html__( 'Tertiary color', 'seeko' ),
			'type'    => 'color',
			'default' => '#555EFF'
		],
		'success'   => [ 'title' => esc_html__( 'Success color', 'seeko' ), 'type' => 'color', 'default' => '#1FCB41' ],
		'info'      => [ 'title' => esc_html__( 'Info color', 'seeko' ), 'type' => 'color', 'default' => '#10B8C1' ],
		'warning'   => [ 'title' => esc_html__( 'Warning color', 'seeko' ), 'type' => 'color', 'default' => '#FFB91A' ],
		'danger'    => [ 'title' => esc_html__( 'Danger color', 'seeko' ), 'type' => 'color', 'default' => '#FF0000' ],
		'light'     => [ 'title' => esc_html__( 'Light color', 'seeko' ), 'type' => 'color', 'default' => '#F8F8F8' ],
		'dark'      => [ 'title' => esc_html__( 'Dark color', 'seeko' ), 'type' => 'color', 'default' => '#000000' ],
	)
);

SVQ_FW::set_config( 'styling_variables',
	array(
		'body-color'     => [
			'title'   => esc_html__( 'Text color', 'seeko' ),
			'type'    => 'color',
			'default' => '#333333',
		],
		'headings-color' => [
			'title'   => esc_html__( 'Headings color', 'seeko' ),
			'type'    => 'color',
			'default' => '#000000',
		],
		'body_bg_enable' => [
			'title'     => esc_html__( 'Custom Body Background', 'seeko' ),
			'type'      => 'switch',
			'default'   => '0',
			'transport' => 'auto'
		],
		'body_bg'        => [
			'title'    => esc_html__( 'BG image', 'seeko' ),
			'type'     => 'background',
			'default'  => array(
				'background-color'      => '#ffffff',
				'background-image'      => '',
				'background-repeat'     => 'no-repeat',
				'background-position'   => 'center center',
				'background-size'       => 'auto',
				'background-attachment' => 'scroll',
			),
			'output'   => array(
				array(
					'element'  => 'body',
					'property' => 'background',
				),
			),
			'required' => array(
				array(
					'setting'  => 'body_bg_enable',
					'operator' => '!=',
					'value'    => '0',
				),
			),
		],
		'border-color'   => [
			'title'   => esc_html__( 'Border color', 'seeko' ),
			'type'    => 'color',
			'default' => '#eeeeee'
		],
		'border-radius'  => [
			'title'   => esc_html__( 'Border radius', 'seeko' ),
			'type'    => 'seeko-input',
			'default' => [ 'size' => 8, 'unit' => 'px' ],
			'units'       => [ 'px' ],
			'defaultUnit' => 'px',
			'column' => '6',
			'transport' => 'postMessage',
		],
	)
);

SVQ_FW::set_config( 'typography',
	[
		'typography_base',
		'typography_heading',
		'font_family_distinct',
		'typography_h1',
		'typography_h2',
		'typography_h3',
		'typography_h4',
		'typography_h5',
		'typography_h6',
	]
);

SVQ_FW::set_config( 'blog_intro', esc_html__( 'Easily connect with our stories, events and news.', 'seeko' ) );


add_filter( 'svq_get_dynamic_variables', 'svq_base_dynamic_variables' );
function svq_base_dynamic_variables( $variables ) {


	//Site colors
	foreach ( SVQ_FW::get_config( 'colors' ) as $slug => $s ) {
		if ( svq_option( $slug, $s['default'] ) ) {
			$variables[ $slug ] = svq_option( $slug, $s['default'] );
		}

	}

	//Styling colors
	foreach ( SVQ_FW::get_config( 'styling_variables' ) as $slug => $s ) {

		if ( $slug == 'body_bg_enable' ) {
			continue;
		}
		$val = svq_option( $slug, $s['default'] );

		if ( $slug == 'body_bg' ) {
			if ( is_array( $val ) && isset( $val['background-color'] ) ) {
				$variables['body-bg'] = $val['background-color'];
				continue;
			}
		}

		if ( $val !== '' ) {
			if ( $slug == 'border-radius' && is_array( $val ) ) {
				$val = ( (int) $val['size'] ) / 16 . 'rem';
			}
			$variables[ $slug ] = $val;
		}
	}

	//Base Typography
	$typography_val = svq_option( 'typography_base' );
	if ( $typography_val ) {
		if (strpos( $typography_val['font-family'], ' ' ) !== false) {
			$typography_val['font-family'] = '"' . $typography_val['font-family'] . '"';
		}

		$variables['font-family-primary'] = $typography_val['font-family'];
		if ( strpos( $typography_val['variant'], 'italic' ) !== false ) {
			$typography_val['variant']    = str_replace( 'italic', '', $typography_val['variant'] );
			$variables['font-style-base'] = 'italic';
		}
		if ( $typography_val['variant'] == 'regular' ) {
			$typography_val['variant'] = 'normal';
		}
		$variables['font-weight-base'] = $typography_val['variant'];
	}

	if ( $font_size_base = svq_option( 'font_size_base' ) ) {
		if (isset( $font_size_base['desktop'] ) ) {
			$variables['font-size-base-lg'] = $font_size_base['desktop']['size'] . $font_size_base['desktop']['unit'];
		}
		if (isset( $font_size_base['tablet'] ) ) {
			$variables['font-size-base-md'] = $font_size_base['tablet']['size'] . $font_size_base['tablet']['unit'];
		}
		if (isset( $font_size_base['mobile'] ) ) {
			$variables['font-size-base-sm'] = $font_size_base['mobile']['size'] . $font_size_base['mobile']['unit'];
		}
	}

	if ( $line_height_base = svq_option( 'line_height_base' ) ) {
		$variables['line-height-base'] = $line_height_base['size'] . str_replace( '-', '', $line_height_base['unit'] );
	}

	//Headings base
	$typography_val = svq_option( 'typography_heading' );
	if ( $typography_val ) {
		if (strpos( $typography_val['font-family'], ' ' ) !== false) {
			$typography_val['font-family'] = '"' . $typography_val['font-family'] . '"';
		}
		$variables['headings-font-family'] = $typography_val['font-family'] . ', Arial, sans-serif';
		$variables['headings-line-height'] = $typography_val['line-height'];
		if ( strpos( $typography_val['variant'], 'italic' ) !== false ) {
			$typography_val['variant']        = str_replace( 'italic', '', $typography_val['variant'] );
			$variables['headings-font-style'] = 'italic';
		}
		if ( $typography_val['variant'] == 'regular' ) {
			$typography_val['variant'] = 'normal';
		}
		$variables['headings-font-weight'] = $typography_val['variant'];

	}

	$typography_val = svq_option( 'font_family_distinct' );
	if ( $typography_val && isset( $typography_val['font-family'] ) && $typography_val['font-family'] != '' ) {
		if (strpos( $typography_val['font-family'], ' ' ) !== false) {
			$typography_val['font-family'] =  '"' . $typography_val['font-family'] . '"';
		}
		$variables['font-family-distinct'] = $typography_val['font-family'];
	}

	//H1-H6
	for ( $i = 1; $i <= 6; $i ++ ) {
		$typography_val = svq_option( 'typography_h' . $i );
		if ( $typography_val ) {
			if ( strpos( $typography_val['variant'], 'italic' ) !== false ) {
				$typography_val['variant']             = str_replace( 'italic', '', $typography_val['variant'] );
				$variables[ 'h' . $i . '-font-style' ] = 'italic';
			}
			if ( $typography_val['variant'] == 'regular' ) {
				$typography_val['variant'] = 'normal';
			}
			if ( isset( $typography_val['variant'] ) && $typography_val['variant'] ) {
				$variables[ 'h' . $i . '-font-weight' ] = $typography_val['variant'];
			}
		}

		if ( $h_font_size = svq_option('font_size_h'. $i ) ) {

			if (isset( $h_font_size['desktop'] ) ) {
				$variables['h' . $i . '-font-size-lg'] = $h_font_size['desktop']['size'] . $h_font_size['desktop']['unit'];
			}
			if (isset( $h_font_size['tablet'] ) ) {
				$variables['h' . $i . '-font-size-md'] = $h_font_size['tablet']['size'] . $h_font_size['tablet']['unit'];
			}
			if (isset( $h_font_size['mobile'] ) ) {
				$variables['h' . $i . '-font-size'] = $h_font_size['mobile']['size'] . $h_font_size['mobile']['unit'];
			}
		}
	}

	return $variables;
}


/***************************************************
 * :: Theme options
 ***************************************************/

add_filter( 'svq_theme_settings', 'svq_base_settings' );

function svq_base_settings( $sq ) {
	//
	// Settings Sections
	//

	$sq['panels']['seeko'] = array(
		'title'       => esc_html__( 'Seeko Options', 'seeko' ),
		'description' => __( 'Seeko Theme specific settings', 'seeko' ),
		'priority'    => 10
	);

	$sq['sec']['svq_section_layout']     = array(
		'title'    => esc_html__( 'Layout', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 8
	);
	$sq['sec']['svq_section_styling']    = array(
		'title'    => esc_html__( 'Site Colors', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 10
	);
	$sq['sec']['svq_section_typography'] = array(
		'title'    => esc_html__( 'Typography', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 12
	);

	$sq['sec']['svq_section_header'] = array(
		'title'    => esc_html__( 'Header', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 13
	);

	$sq['sec']['svq_section_blog'] = array(
		'title'    => esc_html__( 'Blog', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 14
	);

	$sq['sec']['svq_section_footer'] = array(
		'title'    => esc_html__( 'Footer', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 15
	);


	//
	// Layout
	//

	$sq['set']['svq_section_layout'][] = array(
		'section'    => 'svq_section_layout',
		'id'         => 'site_layout',
		'type'       => 'seeko-radio-image',
		'title'      => esc_html__( 'Site Layout', 'seeko' ),
		'choices'    => SVQ_FW::get_config( 'layouts' ),
		'default'    => 'full',
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_layout'][] = array(
		'id'          => 'enable_shadow',
		'type'        => 'switch',
		'title'       => esc_html__( 'Depth effect shadow', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_layout',
		'description' => esc_html__( 'Site elements like images and inputs will have nice shadows.', 'seeko' ),
		'customizer'  => true,
		'transport'   => 'refresh'
	);


	//
	// Styling.
	//

	$sq['set']['svq_section_styling'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_styling_colors',
		'default'    => '<h4 class="customizer-separator">General site styling</h4>',
		'section'    => 'svq_section_styling',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	foreach ( SVQ_FW::get_config( 'styling_variables' ) as $slug => $s ) {

		$s['id'] = $slug;
		$s['section'] = 'svq_section_styling';
		$s['transport'] = isset( $s['transport'] ) ? $s['transport'] : 'refresh';
		$s['output'] = isset( $s['output'] ) ? $s['output'] : false;
		$s['active_callback'] = isset( $s['required'] ) ? $s['required'] : false;

		$sq['set']['svq_section_styling'][] = $s;
	}

	$sq['set']['svq_section_styling'][] = array(
		'type'       => 'custom',
		'id'         => 'separator1',
		'default'    => '<h4 class="customizer-separator">Site Colors</h4>',
		'section'    => 'svq_section_styling',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	foreach ( SVQ_FW::get_config( 'colors' ) as $slug => $s ) {
		$sq['set']['svq_section_styling'][] = array(
			'id'              => str_replace( '-', '_', $slug ),
			'type'            => $s['type'],
			'title'           => $s['title'],
			'default'         => $s['default'],
			'section'         => 'svq_section_styling',
			'customizer'      => true,
			'transport'       => 'auto',
			'active_callback' => isset( $s['required'] ) ? $s['required'] : false,
		);
	}

	$sq['set']['svq_section_typography'][] = array(
		'id'          => 'webfonts_method',
		'type'        => 'select',
		'title'       => esc_html__( 'Google font load method', 'seeko' ),
		'default'     => 'embed',
		'choices'       => [
			'link' => 'Google Link',
			'embed' => 'Embedded in page',
		],
		'section'     => 'svq_section_typography',
		'description' => esc_html__( 'How to load Google fonts: Embedded method will add inline style using font-face. Google Link will add a link tag from Google API.', 'seeko' ),
		'customizer'  => true,
		'transport'   => 'postMessage'
	);

	$sq['set']['svq_section_typography'][] = array(
		'type'       => 'seeko-divider',
		'id'         => 'separator_typography_webfont',
		'section'    => 'svq_section_typography',
	);

	//svq_section_typography
	$sq['set']['svq_section_typography'][] = array(
		'type'      => 'typography',
		'settings'  => 'typography_base',
		'label'     => esc_attr__( 'Body Typography', 'seeko' ),
		'section'   => 'svq_section_typography',
		'priority'  => 10,
		'transport' => 'auto',
		'default'   => array(
			'font-family' => 'Muli',
			'variant'     => 'regular',
		),
		'choices' => array(
			'fonts'   => array(
				'google' => array( 'popularity', 300 ),
			),
			'variant' => [ 'regular', 'italic', '700' ],
		),
		'output'  => is_customize_preview() || is_admin() ? array(
			array(
				'element'  => 'body',
				'property' => 'font',
			),
		) : false,
	);

	$sq['set']['svq_section_typography'][] = array(
		'id'          => 'font_size_base',
		'type'        => 'seeko-input',
		'title'       => esc_html__( 'Font size', 'seeko' ),

		'default'     => [
			'desktop' => [
				'size' => '18',
				'unit' => 'px'
			],
			'tablet' => [
				'size' => '17',
				'unit' => 'px'
			],
			'mobile' => [
				'size' => '16',
				'unit' => 'px'
			],
		],
		'column'      => '6',
		'units'       => [ 'px', 'em', 'rem' ],
		'defaultUnit' => 'px',
		'responsive'  => true,

		'section'     => 'svq_section_typography',
		'transport'   => 'postMessage',
		'output'      => array(
			array(
				'element'     => 'body',
				'property'    => 'font-size',
			),
		),
	);

	$sq['set']['svq_section_typography'][] = array(
		'id'          => 'line_height_base',
		'type'        => 'seeko-input',
		'title'       => esc_html__( 'Line height', 'seeko' ),

		'default'     => [
			'size' => '1.75',
			'unit' => '-'
		],
		'column'      => '6',
		'units'       => [ '-', 'px', 'em', 'rem' ],
		'defaultUnit' => '-',

		'section'     => 'svq_section_typography',
		'transport'   => 'postMessage',
		'output'      => is_customize_preview() || is_admin() ? array(
			array(
				'element'     => 'body',
				'property'    => 'line-height',
			),
		) : false,
	);

	$sq['set']['svq_section_typography'][] = array(
		'type'       => 'seeko-divider',
		'id'         => 'separator_typography_base',
		'section'    => 'svq_section_typography',
	);

	//svq_section_typography
	$sq['set']['svq_section_typography'][] =
		array(
			'type'      => 'typography',
			'settings'  => 'typography_heading',
			'label'     => esc_attr__( 'Headings Default Typography', 'seeko' ),
			'section'   => 'svq_section_typography',
			//'priority'    => 11,
			'transport' => 'auto',
			'default'   => array(
				'font-family' => 'Fira Sans',
				'variant'     => '500',
				'line-height' => '1.4',
			),

			'choices'     => array(
				'fonts'   => array(
					'google' => array( 'popularity', 300 ),
				),
				'variant' => [ 'regular', 'italic' ],
			),
			'output'      => is_customize_preview() || is_admin() ? array(
				array(
					'element' => 'h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6',
					//'property' => 'font',
				),
			) : false,
			'description' => esc_html__( 'It will be used for all H1-H6 tags. You can override a specific heading below this section', 'seeko' ),
	);

	$sq['set']['svq_section_typography'][] = array(
		'type'       => 'seeko-divider',
		'id'         => 'separator_typography_heading',
		'section'    => 'svq_section_typography',
	);

	$heading_defaults = [
		[ '57', '41.5', '39' ],
		[ '43', '33', '31' ],
		[ '32', '26.5', '25' ],
		[ '24', '21', '20' ],
		[ '18', '17', '16' ],
		[ '13.5', '13.5', '13' ],

	];

	for ( $i = 1; $i <= 6; $i ++ ) {
		//svq_section_typography
		$sq['set']['svq_section_typography'][] =
			array(
				'type'      => 'typography',
				'settings'  => 'typography_h' . $i,
				'label'     => 'H' . $i . ' ' . esc_attr__( 'Typography', 'seeko' ),
				'section'   => 'svq_section_typography',
				//'priority'    => 11,
				'transport' => 'auto',
				'default'   => array(
					'font-family' => '',
					'variant'     => 'regular',
				),
				'choices'   => array(
					'fonts'   => array(
						'google' => array( 'popularity', 300 ),
					),
					'variant' => [ 'regular', 'italic', '700' ],
				),
				'output'    => is_customize_preview() || is_admin() ? array(
					array(
						'element'  => 'h' . $i . ', .h' . $i,
						'property' => 'font',
					),
				) : false,
			);

		$sq['set']['svq_section_typography'][] = array(
			'id'          => 'font_size_h' . $i,
			'type'        => 'seeko-input',
			'title'       => esc_html__( 'Font size', 'seeko' ),

			'default'     => [
				'desktop' => [
					'size' => $heading_defaults[ $i - 1 ][0],
					'unit' => 'px'
				],
				'tablet' => [
					'size' => $heading_defaults[ $i - 1 ][1],
					'unit' => 'px'
				],
				'mobile' => [
					'size' => $heading_defaults[ $i - 1 ][2],
					'unit' => 'px'
				],
			],
			'column'      => '6',
			'units'       => [ 'px', 'em', 'rem' ],
			'defaultUnit' => 'px',
			'responsive'  => true,

			'section'     => 'svq_section_typography',
			'transport'   => 'postMessage',
			'output'      => is_customize_preview() || is_admin() ? array(
				array(
					'element'     => 'h' . $i . ', .h' . $i,
					'property'    => 'font-size',
				),
			) : false,
		);

		$sq['set']['svq_section_typography'][] = array(
			'id'          => 'line_height_h' . $i,
			'type'        => 'seeko-input',
			'title'       => esc_html__( 'Line height', 'seeko' ),
			'column'      => '6',
			'units'       => [ '-', 'px', 'em', 'rem' ],
			'defaultUnit' => '-',
			'responsive'  => true,

			'section'     => 'svq_section_typography',
			'transport'   => 'postMessage',
			'output'    =>  array(
				array(
					'element'  => 'h' . $i . ', .h' . $i,
					'property' => 'line-height',
				),
			),
		);

		$sq['set']['svq_section_typography'][] = array(
			'type'       => 'seeko-divider',
			'id'         => 'separator_typography_h' . $i,
			'section'    => 'svq_section_typography',
		);
	}

	//svq_section_typography
	$sq['set']['svq_section_typography']['font_family_distinct'] =
		array(
			'type'      => 'typography',
			'settings'  => 'font_family_distinct',
			'label'     => esc_attr__( 'BlockQuote Single Page', 'seeko' ),
			'section'   => 'svq_section_typography',
			//'priority'    => 11,
			'transport' => 'auto',
			'default'   => array(
				'font-family' => 'Merriweather',
				'variant'     => '300italic',
			),

			'choices'     => array(
				'fonts' => array(
					'google' => array( 'popularity', 300 ),
				),
				//'variant' => [ 'italic' ],
			),
			'output'      => is_customize_preview() || is_admin() ? array(
				array(
					'element' => '.quote-highlight blockquote, .wp-block-pullquote blockquote, .wp-block-quote.is-large',
					//'property' => 'font',
				),
			) : false,
			'description' => esc_html__( 'It will be used for blockquote tags.', 'seeko' ),
		);

	//Header
	if ( defined('STAX_VERSION') ) {
		$sq['set']['svq_section_header'][] = array(
			'type'      => 'custom',
			'id'        => 'separator_header_stax_on',
			'default'   => '<h4 class="customizer-separator">' .
			               '<a target="_blank" href="' . esc_url( home_url( '/?stax-editor' ) ) . '">Go to Header Builder</a>' .
			               '</h4>' .
			               '<p>You are using our STAX Header builder to create an advanced header, awesome.</p>',
			'section'   => 'svq_section_header',
			'transport' => 'refresh'
		);
	}

	$current_url = admin_url( 'customize.php?autofocus[section]=svq_section_header' );
	$url = $current_url . '&svq_action=activate_plugin&plugin=stax';

	function svq_is_normal_header() {
		if ( ! defined('STAX_VERSION') ) {
			return true;
		}
		return false;
	}

	$sq['set']['svq_section_header'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_header_stax',
		'default'    => '<h4 class="customizer-separator">' .
		                'Activate Header Builder' .
		                '</h4>' .
		                '<p>Take advantage of our advanced header features by activating <strong>STAX Header builder</strong>.</p>' .
		                '<br><a class="button button-primary" target="_blank" href="' . esc_url( wp_nonce_url ( $url, 'activate_plugin', 'svq_nonce' ) ) . '">' .
		                '<img src="'. esc_url( get_template_directory_uri() . '/assets/img/stax-16.png' ) .'"> ' .
		                'Activate Header Builder</a>' .
		                '<br><br>Below is it just a limited set of settings.' .
		                '<h4 class="customizer-separator">Default header settings</h4>',
		'section'    => 'svq_section_header',
		'transport'  => 'refresh',
		'active_callback' => 'svq_is_normal_header',
	);

	$sq['set']['svq_section_header'][] = array(
		'type'      => 'typography',
		'settings'  => 'header_typography',
		'label'     => esc_attr__( 'Header Typography', 'seeko' ),
		'section'   => 'svq_section_header',
		'transport' => 'auto',
		'default'   => array(
			'font-family' => 'inherit',
			'variant'     => 'regular',
			'letter-spacing'  => '',
			'color'       => '#1f1f1f',
			'text-transform'  => 'none',
		),

		'choices' => array(
			'fonts'   => array(
				'google' => array( 'popularity', 300 ),
			),
		),
		'output'  => svq_is_normal_header() ? array(
			array(
				'element'  => '#header, #header a'
			),
		) : false,
		'active_callback' => 'svq_is_normal_header',
	);

	$sq['set']['svq_section_header'][] = array(
		'id'          => 'header_font_size',
		'type'        => 'seeko-input',
		'title'       => esc_html__( 'Font size', 'seeko' ),

		'default'     => [
			'desktop' => [
				'size' => '16',
				'unit' => 'px'
			],
			'tablet' => [
				'size' => '15',
				'unit' => 'px'
			],
			'mobile' => [
				'size' => '14',
				'unit' => 'px'
			],
		],
		'column'      => '6',
		'units'       => [ 'px', 'em', 'rem' ],
		'defaultUnit' => 'px',
		'responsive'  => true,

		'section'     => 'svq_section_header',
		'transport'   => 'postMessage',
		'output'  => svq_is_normal_header() ? array(
			array(
				'element'  => '#header, #header a',
				'property' => 'font-size',
			),
		) : false,
		'active_callback' => 'svq_is_normal_header',
	);

	$sq['set']['svq_section_header'][] = array(
		'id'          => 'header_line_height',
		'type'        => 'seeko-input',
		'title'       => esc_html__( 'Line height', 'seeko' ),
		'default'     => [
			'desktop' => [
				'size' => '',
				'unit' => '-'
			],
			'tablet' => [
				'size' => '',
				'unit' => '-'
			],
			'mobile' => [
				'size' => '',
				'unit' => '-'
			],
		],
		'column'      => '6',
		'units'       => [ '-', 'px', 'em', 'rem' ],
		'defaultUnit' => '-',
		'section'     => 'svq_section_header',
		'transport'   => 'postMessage',
		'output'  => svq_is_normal_header() ? array(
			array(
				'element'  => '#header, #header a',
				'property' => 'line-height'
			),
		) : false,
		'active_callback' => 'svq_is_normal_header',
	);


	$sq['set']['svq_section_header'][] = [
		'id'        => 'header_bg',
		'title'     => esc_html__( 'Background', 'seeko' ),
		'type'      => 'background',
		'default'   => array(
			'background-color'      => '#ffffff',
		),
		'output'    => svq_is_normal_header() ? array(
			array(
				'element'  => '#header',
				'property' => 'background',
			),
		) : false,
		'section'   => 'svq_section_header',
		'transport' => 'auto',
		'active_callback' => 'svq_is_normal_header',
	];

	$sq['set']['svq_section_header'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_header_drop',
		'default'    => '<h4 class="customizer-separator">Dropdown</h4>',
		'section'    => 'svq_section_header',
		'transport'  => 'refresh',
		'active_callback' => 'svq_is_normal_header',
	);

	$sq['set']['svq_section_header'][] = [
		'id'        => 'header_submenu_color',
		'title'     => esc_html__( 'Link Color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#333333',
		'output'    => svq_is_normal_header() ? array(
			array(
				'element'  => '#header ul.dropdown-menu li a',
				'property' => 'color',
			),
		) : false,
		'section'   => 'svq_section_header',
		'transport' => 'auto',
		'active_callback' => 'svq_is_normal_header',
	];

	$sq['set']['svq_section_header'][] = [
		'id'        => 'header_submenu_bg',
		'title'     => esc_html__( 'Background color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#ffffff',
		'output'    => svq_is_normal_header() ? array(
			array(
				'element'  => '#header ul.dropdown-menu',
				'property' => 'background-color',
			),
		) : false,
		'section'   => 'svq_section_header',
		'transport' => 'auto',
		'active_callback' => 'svq_is_normal_header',
	];



	//Blog

	$sq['set']['svq_section_blog'][] = array(
		'id'         => 'blog_layout',
		'type'       => 'seeko-radio-image',
		'title'      => esc_html__( 'Single Post Layout', 'seeko' ),
		'default'    => 'default',
		'choices'    => [
			                'default' =>
				                [
					                'alt' => 'Default',
					                'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                ]
		                ] + SVQ_FW::get_config( 'layouts' ),
		'section'    => 'svq_section_blog',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'         => 'blog_archive_layout',
		'type'       => 'seeko-radio-image',
		'title'      => esc_html__( 'Archive Layout', 'seeko' ),
		'default'    => 'default',
		'choices'    => [
			                'default' =>
				                [
					                'alt' => 'Default',
					                'img' => SVQ_LIB_URI . '/assets/images/default-layout.png',
				                ]
		                ] + SVQ_FW::get_config( 'layouts' ),
		'section'    => 'svq_section_blog',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_blog'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_blog_archive',
		'default'    => '<h4 class="customizer-separator">Archive page</h4>',
		'section'    => 'svq_section_blog',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_title',
		'type'        => 'text',
		'title'       => esc_html__( 'Blog Page title', 'seeko' ),
		'default'     => 'Blog',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Set the title of the Blog page when is set as homepage', 'seeko' ),
		'customizer'  => true,
		'transport'   => 'refresh'
	);
	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_intro',
		'type'        => 'text',
		'title'       => esc_html__( 'Blog Page Intro Text', 'seeko' ),
		'default'     => SVQ_FW::get_config( 'blog_intro' ),
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Set a small description text to show on the blog page', 'seeko' ),
		'partial_refresh' => array(
			'blog_intro' => array(
				'selector'        => '.svq-blog-intro',
				'render_callback' => function() {
					echo esc_html( svq_option( 'blog_intro', '' ) );
				},
			),
		),
	);
	$sq['set']['svq_section_blog'][] = array(
		'id'         => 'blog_show_cats',
		'type'       => 'switch',
		'title'      => esc_html__( 'Show categories on blog page', 'seeko' ),
		'default'    => '0',
		'section'    => 'svq_section_blog',
		//'description' => '',
		'partial_refresh' => array(
			'blog_cats' => array(
				'selector'        => '.blog-filters',
				'render_callback' => function() {
					if ( svq_option( 'blog_show_cats', 1 ) ) {
						svq_show_categories();
					}
				},
			),
		),
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_author_meta_archive',
		'type'        => 'switch',
		'title'       => esc_html__( 'Show author meta on archive', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Show author meta under post title on archives', 'seeko' ),
	);
	
	$sq['set']['svq_section_blog'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_blog_single',
		'default'    => '<h4 class="customizer-separator">Single Post page</h4>',
		'section'    => 'svq_section_blog',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_author_meta',
		'type'        => 'switch',
		'title'       => esc_html__( 'Show author meta on single posts', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Show author meta under post title on single posts', 'seeko' ),
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_nav',
		'type'        => 'switch',
		'title'       => esc_html__( 'Prev & Next articles', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Show previous and next articles on post header', 'seeko' ),
		'partial_refresh' => array(
			'blog_nav' => array(
				'selector'        => '.svq-post-nav',
				'render_callback' => function() {
					if ( svq_option( 'blog_nav', 1 ) ) {
						svq_post_nav();
					}
				},
			),
		),
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_author_bio',
		'type'        => 'switch',
		'title'       => esc_html__( 'Show author bio', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Show author details on single post page', 'seeko' ),
		'partial_refresh' => array(
			'blog_author_bio' => array(
				'selector'        => '.author-bio',
				'render_callback' => function() {
					if ( svq_option( 'blog_nav', 1 ) ) {
						get_template_part( 'author-bio' );
					}
				},
			),
		),
	);
	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_related',
		'type'        => 'switch',
		'title'       => esc_html__( 'Show related articles', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Show related posts on single post page', 'seeko' ),
	);

	$sq['set']['svq_section_blog'][] = array(
		'type'       => 'custom',
		'id'         => 'separator_blog_image',
		'default'    => '<h4 class="customizer-separator">Image settings</h4>',
		'section'    => 'svq_section_blog',
		'customizer' => true,
		'transport'  => 'refresh'
	);

	//blog_get_image - switch
	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_get_image',
		'type'        => 'switch',
		'title'       => esc_html__( 'Get image from post content', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Shows on archive only. If no featured image was added, get it from the post content', 'seeko' ),
		'customizer'  => true,
		'transport'   => 'refresh'
	);


	//blog_default_image - image
	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_default_image',
		'type'        => 'image',
		'title'       => esc_html__( 'Default Post Image', 'seeko' ),
		'default'     => '',
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Used when post has no featured image', 'seeko' ),
		//'customizer'  => true,
		'transport'   => 'postMessage'
	);

	$sq['set']['svq_section_blog'][] = array(
		'id'          => 'blog_default_image_format',
		'type'        => 'select',
		'multiple'    => 4,
		'choices'     => [
			'standard' => 'Standard',
			'image'    => 'Image',
			'gallery'  => 'Gallery',
			'video'    => 'Video',
			'quote'    => 'Quote',
		],
		'title'       => esc_html__( 'Default image post formats', 'seeko' ),
		'default'     => [ 'image' ],
		'section'     => 'svq_section_blog',
		'description' => esc_html__( 'Choose on what post formats to show the default image.', 'seeko' ),
		//'customizer'  => true,
		//'transport'   => 'postMessage',
	);

	//post_media_status
	$sq['set']['svq_section_blog'][] = array(
		'id'         => 'post_media_status',
		'type'       => 'switch',
		'title'      => esc_html__( 'Show featured image on post page', 'seeko' ),
		'default'    => '1',
		'section'    => 'svq_section_blog',
		'customizer' => true,
		'transport'  => 'refresh'
	);


	//Footer


	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_display',
		'title'     => esc_html__( 'Footer Display Type', 'seeko' ),
		'type'      => 'radio-buttonset',
		'default'   => 'theme',
		'choices'     => [
			'theme'   => esc_html__( 'Theme Widgets', 'seeko' ),
			'elementor' => esc_html__( 'Elementor Template', 'seeko' ),
		],
		'section'   => 'svq_section_footer',
	];

	$el_query = [
		'post_type' => 'elementor_library',
		'meta_query'     => [
			[
				'key'   => '_elementor_template_type',
				'value' => 'footer',
			],
		],
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_el_tpl',
		'title'     => esc_html__( 'Footer template', 'seeko' ),
		'type'      => 'select',
		'default'   => '',
		'choices'     => [ '-' ] + Seeko_Customizer_Utils::get_select_pages( $el_query ),
		'section'   => 'svq_section_footer',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '==',
				'value'    => 'elementor',
			),
		),
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_bg',
		'title'     => esc_html__( 'Background', 'seeko' ),
		'type'      => 'background',
		'default'   => array(
			'background-color'      => '#101010',
			'background-image'      => get_template_directory_uri() . '/assets/img/footer-picture.png',
			'background-repeat'     => 'no-repeat',
			'background-position'   => 'center center',
			'background-size'       => 'auto',
			'background-attachment' => 'scroll',
		),
		'output'    => array(
			array(
				'element'  => '#footer .svq-footer-inner',
				'property' => 'background',
			),
		),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_text',
		'title'     => esc_html__( 'Text color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#cccccc',
		'output'    => array(
			array(
				'element'  => '#footer, #footer .widget_recent_comments ul li:before, #footer .widget_rss ul li .rsswidget:before',
				'property' => 'color',
			),
		),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_heading',
		'title'     => esc_html__( 'Headings color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#ffffff',
		'output'    => array(
			array(
				'element'  => '#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6',
				'property' => 'color',
			),
		),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_link',
		'title'     => esc_html__( 'Link color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#efefef',
		'output'    => array(
			array(
				'element'  => '#footer a:not(.bp-login-widget-register-link):not(.logout):not(.btn-tag)',
				'property' => 'color',
			),
			array(
				'element'  => '.svq-footer-socket a',
				'property' => 'color',
			),
		),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	SVQ_FW::set_config( 'footer_copy_default', '' );

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_copy',
		'title'     => esc_html__( 'Copyright text', 'seeko' ),
		'type'      => 'editor',
		'default'   => SVQ_FW::get_config( 'footer_copy_default' ),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_socket_bg',
		'title'     => esc_html__( 'Background Color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#101010',
		'output'    => array(
			array(
				'element'  => '.svq-footer-socket',
				'property' => 'background-color',
			),
		),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];
	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_socket',
		'title'     => esc_html__( 'Text color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#cccccc',
		'output'    => array(
			array(
				'element'  => '.svq-footer-socket',
				'property' => 'color',
			),
		),
		'section'   => 'svq_section_footer',
		'transport' => 'auto',
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	$sq['set']['svq_section_footer'][] = [
		'id'        => 'footer_copy_font_size',
		'title'     => esc_html__( 'Copyright text size', 'seeko' ),
		'type'      => 'seeko-input',
		'default'   => [ 'mobile' => [ 'size' => '16', 'unit' => 'px' ] ],
		'units'       => [ 'px', 'em', 'rem' ],
		'defaultUnit' => 'px',
		'section'   => 'svq_section_footer',
		'transport' => 'postMessage',
		'responsive' => true,
		'output'    => array(
			array(
				'element'  => '.svq-footer-socket',
				'property' => 'font-size',
			),
		),
		'active_callback'    => array(
			array(
				'setting'  => 'footer_display',
				'operator' => '!=',
				'value'    => 'elementor',
			),
		),
	];

	return $sq;
}

/***************************************************
 * :: Layout
 ***************************************************/

SVQ_FW::set_config( 'site_layout_default', 'full' );
SVQ_FW::set_config( 'blog_layout_default', 'default' );
SVQ_FW::set_config( 'blog_archive_layout_default', 'default' );


if ( ! function_exists( 'svq_prepare_layout' ) ) {
	/**
	 * Prepare site layout with different customizations
	 * @global string $svq_custom_logo
	 */
	function svq_prepare_layout() {

		//Change the template
		$layout = svq_option( 'site_layout', SVQ_FW::get_config( 'site_layout_default' ) );

		if ( is_single() ) {

			if ( svq_option( 'blog_layout', SVQ_FW::get_config( 'blog_layout_default' ) ) == 'default' ) {
				$layout = svq_option( 'site_layout', SVQ_FW::get_config( 'site_layout_default' ) );
			} else {
				$layout = svq_option( 'blog_layout', SVQ_FW::get_config( 'blog_layout_default' ) );
			}

		} elseif ( is_archive() ) {
			if ( svq_option( 'blog_archive_layout', SVQ_FW::get_config( 'blog_archive_layout_default' ) ) == 'default' ) {
				$layout = svq_option( 'site_layout', SVQ_FW::get_config( 'site_layout_default' ) );
			} else {
				$layout = svq_option( 'blog_archive_layout', SVQ_FW::get_config( 'blog_archive_layout_default' ) );
			}

		}
		$layout = apply_filters( 'site_layout', $layout );
		svq_apply_layout( $layout );

	}
}

function svq_switch_layout( $layout ) {
	add_filter( 'site_layout', function () use ( $layout ) {

		return $layout;
	} );
}

add_action( 'wp_head', 'svq_prepare_layout' );


/***************************************************
 * :: Sidebar logic
 ***************************************************/
if ( ! function_exists( 'svq_apply_layout' ) ) {
	/**
	 * Change site layout
	 *
	 * @param bool $layout
	 * @param int $priority
	 */
	function svq_apply_layout( $layout = false, $priority = 10 ) {
		if ( $layout == false ) {
			$layout = svq_option( 'site_layout', SVQ_FW::get_config( 'site_layout_default' ) );
		}

		if ( 'left' == $layout || 'right' == $layout ) {

			add_filter( 'svq_main_row_class', function ( $classes ) use ( $layout ) {
				if ( is_single() ) {
					$classes['layout'] = 'layout-custom';
				} else {
					$classes['layout'] = 'layout';
				}
				$classes['sidebar'] = 'sidebar-' . $layout;

				return $classes;
			} );
			add_action( 'svq_after_content', 'svq_sidebar', $priority );

		} elseif ( 'full' == $layout || 'no' == $layout || 'default' == $layout ) {

			remove_action( 'svq_after_content', 'svq_sidebar' );
		}

		$body_class = 'tpl-' . $layout;
		add_filter( 'body_class', function ( $classes ) use ( $body_class ) {
			$classes['tpl'] = $body_class;

			return $classes;
		} );

		SVQ_FW::set_config( 'current_layout', $layout );

		do_action( 'svq_apply_layout', $layout );
	}
}

//get the global sidebar
if ( ! function_exists( 'svq_sidebar' ) ):
	function svq_sidebar() {
		get_sidebar();
	}
endif;


/***************************************************
 * :: Main section functions
 ***************************************************/


/**
 * Display the classes for the main section.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function svq_main_section_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', svq_get_main_section_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the menu element as an array.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 *
 * @return array Array of classes.
 */
function svq_get_main_section_class( $class = '' ) {

	$classes = array( 'content-wrapper', SVQ_FW::get_config( 'container_class' ) );
	if ( svq_has_shortcode( 'svq_bp_' ) ) {
		$classes[] = 'buddypress';
	}

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = apply_filters( 'svq_main_section_class', $classes, $class );

	return array_unique( $classes );
}

/**
 * Display the classes for the main row.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function svq_main_row_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', svq_get_main_row_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the main row.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 *
 * @return array Array of classes.
 */
function svq_get_main_row_class( $class = '' ) {

	$classes           = [ 'row' ];
	//$classes['layout'] = 'layout';
	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = apply_filters( 'svq_main_row_class', $classes, $class );

	return array_unique( $classes );
}


/**
 * Display the classes for the main column.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function svq_main_col_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', svq_get_main_col_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the main row.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 *
 * @return array Array of classes.
 */
function svq_get_main_col_class( $class = '' ) {

	$classes        = [ 'col-main' ];
	$classes['col'] = 'col-12';

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = apply_filters( 'svq_main_col_class', $classes, $class );

	return array_unique( $classes );
}

/***************************************************
 * :: SIDEBAR section functions
 ***************************************************/


/**
 * Display the classes for the main section.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function svq_sidebar_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', svq_get_sidebar_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the menu element as an array.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 *
 * @return array Array of classes.
 */
function svq_get_sidebar_class( $class = '' ) {

	$classes = array();

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = apply_filters( 'svq_sidebar_class', $classes, $class );

	return array_unique( $classes );
}


if ( ! function_exists( 'svq_post_thumbnail_html_fallback' ) ) {
	/**
	 * Get the Featured image URL of a post.
	 *
	 * @param $html
	 * @param $post_id
	 * @param $post_thumbnail_id
	 * @param $size
	 * @param $attr
	 *
	 * @return string
	 */
	function svq_post_thumbnail_html_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

		if ( $html == '' ) {

			global $post;
			if ( ! is_object( $post ) && $post_id != null ) {
				$post = setup_postdata( get_post( $post_id ) );
			}
			ob_start();
			ob_end_clean();

			$post_format = get_post_format( $post );
			if ( $post_format == false ) {
				$post_format = 'standard';
			}
			if ( svq_option( 'blog_get_image', 1 ) == 1 ) {

				if ( ! is_single() && isset( $post->post_content ) ) {

					preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post->post_content, $matches );
					$image_url = isset( $matches[1][0] ) ? $matches[1][0] : null;
					if ( $image_id = attachment_url_to_postid( strtok( $image_url, '?' ) ) ) {
						$html = wp_get_attachment_image( $image_id, $size, false, $attr );

						return $html;
					}
				}
			}

			$image_url = svq_option_url( 'blog_default_image', '' );

			if ( is_string( $image_url ) && $image_id = attachment_url_to_postid( strtok( $image_url, '?' ) ) ) {

				if ( in_array( $post_format, svq_option( 'blog_default_image_format' ) ) ) {
					$html = wp_get_attachment_image( $image_id, $size, false, $attr );

					return $html;
				}
			}
		}

		return $html;

	}

	add_filter( 'post_thumbnail_html', 'svq_post_thumbnail_html_fallback', 12, 5 );
}


add_filter( 'the_content', 'svq_quote_content' );
/**
 * Replace blockquote
 *
 * @param $content
 *
 * @return mixed|string
 */
function svq_quote_content( $content ) {

	/* Check if we're displaying a 'quote' post. */
	if ( has_post_format( 'quote' ) ) {

		/* Match any <blockquote> elements. */
		preg_match( '/<blockquote.*?>/', $content, $matches );

		/* If no <blockquote> elements were found, wrap the entire content in one. */
		if ( empty( $matches ) ) {
			if ( is_single() ) {
				$content = "<div class='quote-highlight'><blockquote>{$content}</blockquote></div>";
			} else {
				$content = "<blockquote>{$content}</blockquote>";
			}
		} else {
			if ( is_single() ) {
				$content = preg_replace('/<blockquote/', '<div class="quote-highlight"><blockquote', $content, 1);
				$content = preg_replace('/<\/blockquote>/', '</blockquote></div>', $content, 1);
			}
		}

	}

	return $content;
}

function svq_get_content_quote( $content ) {
	preg_match( '/<blockquote(.*?)>(.|\n)*?<\/blockquote>/i', $content, $matches );
	if ( isset( $matches[0] ) ) {
		if (strpos($matches[0], 'class=') !== false) {
			return str_replace( 'class="', 'class="h4 ', $matches[0] );
		} else {
			return str_replace( '<blockquote', '<blockquote class="h4"', $matches[0] );
		}

	}

	return '';
}
