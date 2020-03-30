<?php

/***************************************************
 * :: Render
 ***************************************************/

function svq_show_breadcrumb() {
	svq_get_template_part( 'page-parts/breadcrumb' );
}


function svq_the_breadcrumb( $force_show = false ) {

	/* Don't show if is set to off */
	if ( ! svq_option( 'breadcrumb_enable', 1 ) && $force_show == false ) {
		return;
	}

	$breadcrumb_data = svq_breadcrumb( array(
		'show_title'      => true,
		'show_browse'     => false,
		'separator'       => '',
		'container'       => 'ol',
		'container_class' => 'svq-breadcrumb',
		'item_tag'        => 'li',
		'show_home'       => esc_html__( 'Home', 'seeko' ),
		'echo'            => false,
	) );
	echo apply_filters( 'svq_breadcrumb', $breadcrumb_data );
}


/***************************************************
 * :: Theme options
 ***************************************************/

add_filter( 'svq_theme_settings', 'svq_breadcrumb_settings' );

function svq_breadcrumb_settings( $sq ) {


	$sq['sec']['svq_section_breadcrumb'] = array(
		'title'    => esc_html__( 'Breadcrumb', 'seeko' ),
		'panel'    => 'seeko',
		'priority' => 16
	);


	$sq['set']['svq_section_breadcrumb'][] = array(
		'id'          => 'breadcrumb_enable',
		'type'        => 'switch',
		'title'       => esc_html__( 'Enable Breadcrumb section', 'seeko' ),
		'default'     => '1',
		'section'     => 'svq_section_breadcrumb',
		'description' => esc_html__( 'Show the breadcrumb section on your site', 'seeko' ),
		'customizer'  => true,
		'transport'   => 'refresh'
	);

	$sq['set']['svq_section_breadcrumb'][] = [
		'id'        => 'breadcrumb_text',
		'title'     => esc_html__( 'Text color', 'seeko' ),
		'type'      => 'color',
		'default'   => '#333333',
		'output'    => array(
			array(
				'element'  => '.svq-breadcrumb, .svq-breadcrumb li a, .svq-breadcrumb li.active, .svq-breadcrumb li + li::before',
				'property' => 'color',
			),
		),
		'section'   => 'svq_section_breadcrumb',
		'transport' => 'auto',
	];

	return $sq;
}


/***************************************************
 * :: EXTRA LOGIC FUNCTIONS
 ***************************************************/

add_filter( 'body_class', 'svq_add_breadcrumb_class' );
function svq_add_breadcrumb_class( $classes = array() ) {
	if ( ! svq_option( 'breadcrumb_enable', true, true ) ) {
		$classes[] = 'breadcrumb-disabled';
	}

	return $classes;
}


/**
 * Display the classes for the breadcrumb element.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function svq_breadcrumb_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', svq_get_breadcrumb_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the breadcrumb element as an array.
 *
 * @since 1.0
 *
 * @param string|array $class One or more classes to add to the class list.
 *
 * @return array Array of classes.
 */
function svq_get_breadcrumb_class( $class = '' ) {

	$classes = array( 'svq-breadcrumb-wrap' );

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = apply_filters( 'svq_breadcrumb_class', $classes, $class );

	return array_unique( $classes );
}
