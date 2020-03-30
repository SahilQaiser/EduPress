<?php
/**
 * SQ Hide Admin bar
 *
 *
 * @package SQ Hide Admin bar
 * @since 1.0.0
 */

/**
 * Plugin Name: SQ Hide Admin bar
 * Plugin URI:  https://seventhqueen.com/
 * Description: Hides the admin bar in front-end
 * Author:      SeventhQueen
 * Version:     1.0.0
 * Text Domain: sq-hide-admin-bar
 * Domain Path: /languages
 * License:     GPLv2 or later (license.txt)
 */
remove_action('wp_footer', 'wp_admin_bar_render', 1000);
add_filter('show_admin_bar', '__return_false');
