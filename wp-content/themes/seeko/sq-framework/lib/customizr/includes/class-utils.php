<?php
/**
 * This class handles customizer utils function.
 *
 * @package Seeko\Customizer
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define all customizer utils.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Seeko\Customizer
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class Seeko_Customizer_Utils {

	/**
	 * Assets url getter.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url of the assets.
	 */
	public static function get_assets_url() {
		return SEEKO_CUSTOMIZER_URL . 'assets';
	}

	/**
	 * Icon file url getter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name File name.
	 *
	 * @return string The formatted url of the icon.
	 */
	public static function get_icon_url( $file_name ) {
		return ( ! empty( $file_name ) ) ? SEEKO_CUSTOMIZER_URL . 'assets/img/' . $file_name . '.svg' : '';
	}

	/**
	 * Assets url getter.
	 *
	 * @since 1.0.0
	 *
	 * @return array Url of the assets.
	 */
	public static function get_text_decoration_choices() {
		return [
			'none'         => esc_html__( 'None', 'seeko' ),
			'underline'    => esc_html__( 'Underline', 'seeko' ),
			'overline'     => esc_html__( 'Overline', 'seeko' ),
			'line-through' => esc_html__( 'Line Through', 'seeko' ),
		];
	}

	/**
	 * Get all registered menus and format as a valid choices for select control.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of all registered menus.
	 */
	public static function get_select_menus() {
		$choices = [];

		$menus = get_terms( 'nav_menu', [ 'hide_empty' => false ] );

		foreach ( $menus as $menu ) {
			$choices[ $menu->slug ] = $menu->name;
		}

		return $choices;
	}

	/**
	 * Get all pages.
	 *
	 * @param $args
	 * @since 1.0.0
	 *
	 * @return array List of published pages.
	 */
	public static function get_select_pages( $args = [] ) {
		$choices = [];

		$defaults = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$args = wp_parse_args( $args, $defaults );

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$post_title = get_the_title();

				if ( '' === $post_title ) {
					/* translators: %d: page ID */
					$post_title = sprintf( __( '#%d (no title)' ), get_the_ID() );
				}

				$choices[ get_the_ID() ] = $post_title;
			}
		}

		return $choices;
	}


	/**
	 * Get all possible terms in a taxonomy as an array.
	 * Used in custom fields.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $taxonomy    Taxonomy slug.
	 * @param  boolean $add_default Prepend All {$taxonomy} to array.
	 *
	 * @return array   $terms       Array of existing terms.
	 */
	public static function get_terms( $taxonomy, $add_default = true ) {

		$terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'fields'     => 'id=>name',
		] );

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		if ( $add_default ) {
			$terms = [ '0' => esc_html__( 'All Categories', 'seeko' ) ] + $terms;
		}

		return $terms;
	}

	/**
	 * Get all registered widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of widgets area.
	 */
	public static function get_select_widgets_area() {
		global $wp_registered_sidebars;

		$choices = [];

		foreach ( $wp_registered_sidebars as $sidebar ) {
			$choices[ $sidebar['id'] ] = $sidebar['name'];
		}

		return $choices;
	}

	/**
	 * Get align choices.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type    The type 'justify-content', 'flex-direction'.
	 * @param  array  $exclude The items to be excluded.
	 *
	 * @return array The align choices.
	 */
	public static function get_align( $type = '', $exclude = [] ) {
		$first = is_rtl() ? 'right' : 'left';
		$last  = is_rtl() ? 'left' : 'right';

		if ( 'justify-content' === $type ) {
			$first = '';
			$last  = 'flex-end';
		}

		if ( 'flex-direction' === $type ) {
			$first = 'row';
			$last  = 'row-reverse';
		}

		$choices = [
			$first   => [
				'icon'  => is_rtl() ? 'alignment-right' : 'alignment-left',
			],
			'center' => [
				'icon'  => 'alignment-center',
			],
			$last    => [
				'icon'  => is_rtl() ? 'alignment-left' : 'alignment-right',
			],
		];

		foreach ( $exclude as $item ) {
			unset( $choices[ $item ] );
		}

		return $choices;
	}

	/**
	 * Filter permalink based on current language if WPML is active.
	 *
	 * @param string $post_id Post ID to get permalink.
	 *
	 * @since 1.0.0
	 *
	 * @return string Translated URL or main if WPML is not active.
	 */
	public static function get_permalink( $post_id ) {
		if ( ! $post_id ) {
			return false;
		}

		$url = get_permalink( intval( $post_id ) );

		if ( class_exists( 'SitePress' ) ) {
			return apply_filters( 'wpml_permalink', $url, ICL_LANGUAGE_CODE, true );
		}

		return $url;
	}

}
