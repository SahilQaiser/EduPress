<?php


/***************************************************
:: Theme options
 ***************************************************/

add_filter( 'svq_theme_settings', 'svq_extras_settings' );

function svq_extras_settings( $sq )
{
    //
    // Settings Sections
    //


	$sq['sec']['svq_section_performance'] = array(
		'title' => esc_html__('Performance', 'seeko'),
		'panel'    => 'seeko',
		'priority' => 30
	);

	$sq['sec']['svq_section_404'] = array(
		'title' => esc_html__( '404 Page', 'seeko'),
		'panel'    => 'seeko',
		'priority' => 40
	);

	$sq['sec']['svq_section_misc'] = array(
		'title' => esc_html__('Miscellaneous', 'seeko'),
		'panel'    => 'seeko',
		'priority' => 50
	);


	//Performance

	$sq['set']['svq_section_performance'][] = array(
		'id' => 'img_lazy',
		'type' => 'switch',
		'title' => esc_html__('Lazy Load images', 'seeko'),
		'default' => 1,
		'section' => 'svq_section_performance',
		'description' => esc_html__('Load images as you scroll the page. Drastically improves site speed.', 'seeko'),
	);



	$sq['set']['svq_section_performance'][] = array(
		'id' => 'css_preload',
		'type' => 'switch',
		'title' => esc_html__('Preload theme Css files', 'seeko'),
		'default' => 0,
		'section' => 'svq_section_performance',
		'description' => wp_kses_data( sprintf(__( 'Tell the browser to prioritize theme css files. <a target="_blank" href="%s">Read more</a>', 'seeko'), ' https://developers.google.com/web/updates/2016/03/link-rel-preload') ),
		'customizer' => false,
	);


	// 404 Page

	//
	$sq['set']['svq_section_404'][] = array(
		'id' => '404_image',
		'type' => 'image',
		'title' => esc_html__('Custom image', 'seeko'),
		'default' => get_template_directory_uri() . '/assets/img/404.png',
		'section' => 'svq_section_404',
		'description' => esc_html__( 'Change the default image on 404 page', 'seeko' ),
		'partial_refresh' => array(
			'404_image' => array(
				'selector'        => '.svq-image-404',
				'render_callback' => function() {
					return '<img src="' . esc_url( svq_option( '404_image', get_template_directory_uri() . '/assets/img/404.png', true ) ) . '" class="svq-image-404 p-4 w-100" alt="404">';
				},
			),
		),
	);


	$pages = get_pages();
	$pages_array = [ '' =>  'Select' ];
	foreach ( $pages as $page ) {
		$pages_array[ $page->ID ] = $page->post_title;
	}

	$sq['set']['svq_section_404'][] = array(
		'id' => '404_suggestions',
		'title' => esc_html__( 'Suggest pages to visit', 'seeko' ),
		'type' => 'select',
		'choices' => $pages_array,
		'multiple' => 15,
		'default' => [],
		'section' => 'svq_section_404',
		'transport' => 'auto',
		'description' => esc_html__('This will show after the search in 404 page.', 'seeko'),
		'partial_refresh' => array(
			'404_suggest' => array(
				'selector'        => '.svq-404-suggest',
				'render_callback' => 'svq_404_suggestions',
			),
		),
	);

    //
    // Misc
    //

    $sq['set']['svq_section_misc'][] = array(
        'id' => 'maintenance_mode',
        'type' => 'switch',
        'title' => esc_html__('Maintenance Enabled', 'seeko'),
        'default' => 0,
        'section' => 'svq_section_misc',
        'description' => esc_html__('WARNING: It will make the site available to admins only', 'seeko'),
    );

    $sq['set']['svq_section_misc'][] = array(
        'id' => 'maintenance_msg',
        'type' => 'textarea',
        'title' =>  esc_html__('Maintenance Message', 'seeko'),
        'default' => 'We are not available for the moment!!!',
        'section' => 'svq_section_misc',
        'description' => esc_html__('The message that is visible for guests if you enabled maintenance', 'seeko'),
        'condition' => array( 'maintenance_mode', 1 )
    );

    $sq['set']['svq_section_misc'][] = array(
        'id' => 'page_comments_disable',
        'type' => 'switch',
        'title' => esc_html__('Disable page comments', 'seeko'),
        'default' => '0',
        'section' => 'svq_section_misc',
        'description' => esc_html__('Force disable comments on all pages', 'seeko'),
    );
    $sq['set']['svq_section_misc'][] = array(
        'id' => 'mobile_animations_off',
        'type' => 'switch',
        'title' => esc_html__('Disable Elementor Mobile animations', 'seeko'),
        'default' => '0',
        'section' => 'svq_section_misc',
        'description' => esc_html__('Force disable Elementor animations on mobile on all pages', 'seeko'),
    );

    $sq['set']['svq_section_misc'][] = array(
        'id' => 'quick_code',
        'type' => 'textarea',
        'title' =>  esc_html__( 'Quick Javascript', 'seeko' ),
        'default' => '',
        'section' => 'svq_section_misc',
        'description' => esc_html__('Small code to load in the footer of your site. You need to include the script tags if adding JS.', 'seeko'),
    );

    $sq['set']['svq_section_misc'][] = array(
        'id' => 'dev_mode',
        'type' => 'switch',
        'title' => esc_html__( 'Development mode', 'seeko' ),
        'default' => 0,
        'section' => 'svq_section_misc',
        'description' => esc_html__('This will load css/js files not minified.', 'seeko'),
    );

    return $sq;

}


/***************************************************
:: Render functions
 ***************************************************/

//
// MAINTENANCE MODE
//
if ( ! function_exists('svq_maintenance_mode') ) {
    function svq_maintenance_mode() {

        if ( svq_option('maintenance_mode', false) ) {

            if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
                wp_die(
	                 '<div style="margin: 0 auto;text-align:center">'
	                 . svq_get_the_logo()
	                 . '<br><br>'
                    . svq_option( 'maintenance_msg', '' )
                    . '</div>',
                    get_bloginfo( 'name' )
                );
            }
        }
    }
    add_action( 'get_header', 'svq_maintenance_mode' );
}


if ( svq_option( 'page_comments_disable', 0 ) ) {
    add_action( 'wp', 'svq_disable_page_comments' );
}

function svq_disable_page_comments() {
    if ( is_page() ) {
        add_filter( 'comments_open', '__return_false');
    }
}

if ( svq_option( 'mobile_animations_off', 0 ) ) {
	SVQ_FW::add_css( '@media only screen and (max-width : 768px) {.elementor-invisible{visibility:visible;}.animated {opacity: 1;filter: alpha(opacity=100);-webkit-animation: none;-moz-animation: none;-o-animation: none;animation: none;}}' );
}