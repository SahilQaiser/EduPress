<?php

//define dynamic styles path
$upload_dir = wp_upload_dir();
if ( is_ssl() ) {
	if ( strpos( $upload_dir['baseurl'], 'https://' ) === false ) {
		$upload_dir['baseurl'] = str_ireplace( 'http', 'https', $upload_dir['baseurl'] );
	}
}

SVQ_FW::set_config( 'upload_dir_path', $upload_dir['basedir'] );
SVQ_FW::set_config( 'custom_style_path', $upload_dir['basedir'] . '/custom_styles' );
SVQ_FW::set_config( 'custom_style_url', $upload_dir['baseurl'] . '/custom_styles' );
SVQ_FW::set_config( 'custom_style_name', 'svq_dynamic.css' );
SVQ_FW::set_config( 'dynamic_css_lang', 'scss' );
SVQ_FW::set_config( 'dynamic_css_import_paths', [ get_template_directory() . '/assets/scss/' ] );
SVQ_FW::set_config( 'dynamic_css_src', [ get_template_directory() . '/assets/scss/theme-dynamic.scss' ] );


/***************************************************
 * :: Render custom css resulted from theme options
 ***************************************************/
if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'svq_load_dynamic_css', 999 );
	add_action( 'wp_enqueue_scripts', 'svq_enqueue_dynamic_css', 99999 );
}

/**
 * Load generated CSS file containing theme customizations
 *
 */
function svq_load_dynamic_css() {

	if ( is_writable( trailingslashit( SVQ_FW::get_config( 'upload_dir_path' ) ) ) && ! svq_check_is_customizer_valid() ) {
		$dynamic_file = trailingslashit( SVQ_FW::get_config( 'custom_style_path' ) ) . SVQ_FW::get_config( 'custom_style_name' );
		$version      = SVQ_THEME_VERSION;
		if ( svq_option( 'stime', '' ) ) {
			$version .= '.' . svq_option( 'stime', '' );
		}

		//write the file if isn't there
		if ( ! file_exists( $dynamic_file ) || 0 == filesize( $dynamic_file ) ) {
			svq_generate_dynamic_css();
		}

		wp_register_style( 'svq-dynamic', trailingslashit( SVQ_FW::get_config( 'custom_style_url' ) ) . SVQ_FW::get_config( 'custom_style_name' ), array(), $version, 'all' );
	} else {
		/* Generate CSS styles in head section if write file was not possible */
		$lang = SVQ_FW::get_config( 'dynamic_css_lang' );

		if ( 'scss' == $lang ) {
			$css = svq_dynamic_scss_generate();
		} else {
			$css = svq_dynamic_less_generate();
		}

		if ( ! empty( $css ) ) {
			wp_add_inline_style( get_template(), $css );
		}

	}
}

function svq_enqueue_dynamic_css() {
	wp_enqueue_style( 'svq-dynamic' );
	do_action( 'svq_dynamic_after_enqueue' );
}


function svq_check_is_customizer_valid() {
	if ( is_customize_preview() ) {
		if ( isset( $_POST['customized'] ) && ! empty( $_POST['customized'] ) && $_POST['customized'] != '{}' ) {
			return true;
		}
	}
	return false;
}

function svq_dynamic_less_generate() {
	if ( ! class_exists( 'Less_Parser' ) ) {
		require_once dirname( __FILE__ ) . '/less/Less.php';
	}

	$options = array();

	if ( svq_option( 'dev_mode', false ) == false ) {
		$options['compress'] = true;
	}

	$variables   = apply_filters( 'svq_get_dynamic_variables', array() );
	$less_files  = apply_filters( 'svq_less_files', array() );
	$directories = array( THEME_DIR . "/assets/less" => '' );

	$css = '';

	try {

		$parser = new Less_Parser( $options );
		$parser->SetImportDirs( $directories );
		if ( ! empty( $less_files ) ) {
			foreach ( $less_files as $k => $v ) {
				$parser->parseFile( $k, '' );
			}
		}

		$parser->ModifyVars( $variables );
		$css = $parser->getCss();

	} catch ( Exception $e ) {
		esc_html_e( 'Something went wrong when compiling less files.', 'seeko' );
		echo esc_html( $e->getMessage() );
	}

	return $css;
}

function svq_dynamic_scss_generate() {
	if ( ! class_exists( 'Leafo\ScssPhp\Version', false ) ) {
		require_once dirname( __FILE__ ) . '/scss/scss.inc.php';
	}

	$variables = apply_filters( 'svq_get_dynamic_variables', array() );

	$css = '';

	try {

		$scss = new \Leafo\ScssPhp\Compiler();
		$scss->setImportPaths( SVQ_FW::get_config( 'dynamic_css_import_paths' ) );

		$scss->setVariables( $variables );

		$scss->setFormatter( 'Leafo\ScssPhp\Formatter\Nested' );

		$files = SVQ_FW::get_config( 'dynamic_css_src' );
		$css   = '';
		if ( $files && ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$css .= $scss->compile( svq_fs_get_contents( $file ) );
			}
		}

		//if ( svq_option( 'dev_mode', false ) == false )  {
			$css = svq_compress( $css );
		//}


	} catch ( Exception $e ) {
		esc_html_e( 'Something went wrong when compiling less files.', 'seeko' );
		echo esc_html( $e->getMessage() );
	}

	return $css;
}

function svq_generate_dynamic_css() {

	if ( ! is_writable( trailingslashit( SVQ_FW::get_config( 'upload_dir_path' ) ) ) ) {
		return;
	}

	// dir doesn't exist, make it
	if ( ! is_dir( SVQ_FW::get_config( 'custom_style_path' ) ) ) {
		wp_mkdir_p( SVQ_FW::get_config( 'custom_style_path' ) );
	}

	$lang = SVQ_FW::get_config( 'dynamic_css_lang' );

	if ( 'scss' == $lang ) {
		$css = svq_dynamic_scss_generate();
	} else {
		$css = svq_dynamic_less_generate();
	}

	$file_path = trailingslashit( SVQ_FW::get_config( 'custom_style_path' ) ) . SVQ_FW::get_config( 'custom_style_name' );

	if ( svq_fs_put_contents( $file_path, $css ) ) {
		// do nothing
	} elseif ( is_admin() ) {
		echo '<div class="error settings-error">';
		esc_html_e( 'Cannot write dynamic css file. Please check file permissions with hosting', 'seeko' );
		echo '</div>';
	}

}

if ( ! function_exists( 'svq_unlink_dynamic_css' ) ) {
	function svq_unlink_dynamic_css() {
		if ( file_exists( trailingslashit( SVQ_FW::get_config( 'custom_style_path' ) ) . SVQ_FW::get_config( 'custom_style_name' ) ) ) {
			// Delete it
			unlink( trailingslashit( SVQ_FW::get_config( 'custom_style_path' ) ) . SVQ_FW::get_config( 'custom_style_name' ) );

			if ( function_exists( 'set_theme_mod' ) ) {
				set_theme_mod( 'stime', time() );
			}

		}
	}
}

add_action( 'init', 'svq_manually_generate_css' );

function svq_manually_generate_css() {
	if ( is_super_admin() && isset( $_GET['regen_css'] ) ) {
		svq_unlink_dynamic_css();
	}
}

/* Regenerate CSS after customizer save */
add_action( 'customize_save_after', 'svq_unlink_dynamic_css' );


/* Regenerate on theme update */
add_action( 'after_setup_theme', 'svq_dynamic_check_version', 999 );
function svq_dynamic_check_version() {
	$svq_dynamic_version = get_option( 'svq_dynamic_' . SVQ_DOMAIN );
	$dynamic_file = trailingslashit( SVQ_FW::get_config( 'custom_style_path' ) ) . SVQ_FW::get_config( 'custom_style_name' );

	if ( $svq_dynamic_version == false ) {
		$needs_update = true;
	} else {
		$needs_update = version_compare( $svq_dynamic_version, SVQ_THEME_VERSION, '<' );
	}

	if ( $needs_update || ! file_exists( $dynamic_file ) ) {
		svq_generate_dynamic_css();
		update_option( 'svq_dynamic_' . SVQ_DOMAIN, SVQ_THEME_VERSION );
	}
}
