<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="page-wrapper">
 *
 * @package WordPress
 * @subpackage Seeko
 * @since Seeko 1.0
 */
?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta name="author" content="SeventhQueen"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<script>document.documentElement.className = document.documentElement.className.replace(/(\s|^)no-js(\s|$)/, '$1js$2');</script>

	<?php if ( function_exists( 'bp_is_active' ) ) {
		bp_head();
	} ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'svq_after_body' ); ?>

<!-- Document Wrapper
============================================= -->
<div id="page-wrapper">

	<?php
	/**
	 * Included Header section using actions
	 *
	 * Adds header
	 * adds page title section
	 *
	 * Templates used:
	 * @see page-parts/_header/ - (outputs header)
	 * @see page-parts/breadcrumb/ - (outputs page breadcrumb)
	 *
	 * @hooked svq_show_header - 12
	 * @hooked svq_show_breadcrumb - 14
	 */
	// Support Elementor theme location.
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
		/**
		 * Fires in the header.
		 *
		 * @since 1.0.0
		 */
		do_action( 'svq_header' );
	}


	/**
	 * Before main content - action
	 */
	do_action( 'svq_before_main' );

	?>
