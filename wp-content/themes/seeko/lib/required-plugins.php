<?php

function svq_get_required_plugins() {

	$required_plugins = [];

	if ( function_exists( 'stax_fs' ) && stax_fs()->is_premium() && ! defined( 'STAX_DEV' ) ) {
		$stax_slug = 'stax-premium';
	} else {
		$stax_slug = 'stax';
	}
	$required_plugins[] = [
		'name'               => 'STAX Header Builder',
		'slug'               => $stax_slug,
		'version'            => '1.0.3',
		'required'           => true,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Build your header with an advanced Drag & Drop interface',
	];


	$required_plugins[] = [
		'name'               => 'Elementor Page Builder',
		'slug'               => 'elementor',
		'version'            => '2.0',
		'required'           => true,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Build your pages with an advanced Drag & Drop interface',
	];
	$required_plugins[] = [
		'name'               => 'Contact Form 7',
		'slug'               => 'contact-form-7',
		'version'            => '5.0',
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Easily build contact forms',
	];
	$required_plugins[] = [
		'name'               => 'Seeko Pro Search',
		'slug'               => 'seeko-search',
		'version'            => svq_get_plugin_version( 'seeko-search', '1.3.5' ),
		'source'             => svq_get_plugin_url( 'seeko-search' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Search Members, Groups, Posts with advanced customizable forms.',
	];
	$required_plugins[] = [
		'name'               => 'Profile matching',
		'slug'               => 'bp-matching',
		'version'            => svq_get_plugin_version( 'bp-matching' ),
		'source'             => svq_get_plugin_url( 'bp-matching' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Display compatibility between members on their profiles',
	];
	$required_plugins[] = [
		'name'               => 'Login Booster - Ajax Login & Facebook',
		'slug'               => 'sq-login-booster',
		'version'            => svq_get_plugin_version( 'sq-login-booster', '1.3.7' ),
		'source'             => svq_get_plugin_url( 'sq-login-booster' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Add Ajax login in modal and Facebook login.',
	];
	$required_plugins[] = [
		'name'               => 'Sidebar Generator',
		'slug'               => 'sq-sidebar-generator',
		'version'            => svq_get_plugin_version( 'sq-sidebar-generator' ),
		'source'             => svq_get_plugin_url( 'sq-sidebar-generator' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Generates as many sidebars as you need. Then place them on any page you wish.',
	];
	$required_plugins[] = [
		'name'               => 'Theme Super Core',
		'slug'               => 'sq-theme-core',
		'version'            => svq_get_plugin_version( 'sq-theme-core', '1.5.0' ),
		'source'             => svq_get_plugin_url( 'sq-theme-core' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Enables Importing and advanced features for SeventhQueen Themes.',
	];
	$required_plugins[] = [
		'name'               => 'Envato Market - Auto Theme Updates',
		// The plugin name
		'slug'               => 'envato-market',
		// The plugin slug (typically the folder name)
		'source'             => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
		// The plugin source
		'required'           => true,
		// If false, the plugin is only 'recommended' instead of required
		'version'            => '2.0.1',
		// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
		'force_activation'   => false,
		// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
		'force_deactivation' => false,
		// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
		'external_url'       => '',
		// If set, overrides default API URL and points to an external URL
		'description'        => 'Enables automatic theme updates on your site',
	];
	$required_plugins[] = [
		'name'               => 'Speed Optimization',
		'slug'               => 'sq-speed-optimization',
		'version'            => svq_get_plugin_version( 'sq-speed-optimization' ),
		'source'             => svq_get_plugin_url( 'sq-speed-optimization' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Adds options to Remove Query Strings from assets and defer some scripts to optimize page speed.',
	];
	$required_plugins[] = [
		'name'               => 'Rainbow Categories',
		'slug'               => 'sq-rainbow-categories',
		'version'            => svq_get_plugin_version( 'sq-rainbow-categories' ),
		'source'             => svq_get_plugin_url( 'sq-rainbow-categories' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Adds colored categories to the blog.',
	];
	$required_plugins[] = [
		'name'               => 'Posts Like',
		'slug'               => 'sq-likes',
		'version'            => svq_get_plugin_version( 'sq-likes' ),
		'source'             => svq_get_plugin_url( 'sq-likes' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Adds option to like post from single post page.',
	];
	$required_plugins[] = [
		'name'               => 'Hide Admin Bar',
		'slug'               => 'sq-hide-admin-bar',
		'version'            => svq_get_plugin_version( 'sq-hide-admin-bar' ),
		'source'             => svq_get_plugin_url( 'sq-hide-admin-bar' ),
		'required'           => false,
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Hides the admin bar in front-end area.',
	];
	$required_plugins[] = [
		'name'               => 'BuddyPress & PMPRO restrict',
		'slug'               => 'bp-restrict',
		'required'           => false,
		'version'            => '1.0',
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Adds restrictions settings to Buddypress areas for Paid Memberships Pro plugin'
	];
	$required_plugins[] = [
		'name'               => 'Import/Export theme settings',
		'slug'               => 'customizer-export-import',
		'required'           => false,
		'version'            => '0.8',
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Adds a section to the Customizer to import/export options.',
	];
	$required_plugins[] = [
		'name'               => 'BuddyPress',
		'slug'               => 'buddypress',
		'required'           => false,
		'version'            => '3.0',
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Build any type of community website with member profiles, activity streams, user groups, messaging, and more.',
	];
	$required_plugins[] = [
		'name'               => 'rtMedia',
		'slug'               => 'buddypress-media',
		'required'           => false,
		'version'            => '',
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Allows BuddyPress users to create image, video or audio galleries.'
	];
	$required_plugins[] = [
		'name'               => 'Paid Memberships Pro',
		'slug'               => 'paid-memberships-pro',
		'required'           => false,
		'version'            => '1.8',
		'force_activation'   => false,
		'force_deactivation' => false,
		'external_url'       => '',
		'description'        => 'Add memberships levels and create access restrictions for your users.'
	];

	return $required_plugins;
}

//add required plugins
SQF()->tgm_plugins = svq_get_required_plugins();

require_once SVQ_DIR . '/lib/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', array( SQF(), 'required_plugins' ) );


function svq_get_plugin_url( $name ) {
	return 'http://updates.seventhqueen.com/check/?action=download&slug=' . $name . '.zip';
}

/**
 * Get the source of the plugin depending on the version available
 *
 * @param string $name
 * @param string $force_version
 *
 * @return string
 */
function svq_get_plugin_version( $name, $force_version = null ) {

	$version = '';
	if ( isset( $_GET['sq_force_updates'] ) ) {
		delete_transient( SVQ_FW::get_config( 'slug' ) . '_' . $name );
	}

	if ( $version = get_transient( SVQ_FW::get_config( 'slug' ) . '_' . $name ) ) {
		//
	} else {

		$url = 'http://updates.seventhqueen.com/check/?action=get_metadata&slug=' . $name;

		$purchase_get = wp_remote_get( $url );

		// Check for error
		if ( ! is_wp_error( $purchase_get ) ) {
			$response = wp_remote_retrieve_body( $purchase_get );

			// Check for error
			if ( ! is_wp_error( $response ) ) {
				$response = json_decode( $response );
				$version  = $response->version;
			}
		}

		set_transient( SVQ_FW::get_config( 'slug' ) . '_' . $name, $version, 60 * 60 * 24 );
	}
	if ( isset( $force_version ) && version_compare( $force_version, $version, '>' ) ) {
		$version = $force_version;
	}

	return $version;
}
