<?php
/**
 * Add Library Footer Document.
 *
 * @package Seventor
 * @since 1.0.0
 */

namespace Seventor\Core\Library\Documents;

defined( 'ABSPATH' ) || die();

/**
 * Seventor footer library document.
 *
 * Seventor footer library document handler class is responsible for
 * handling a document of a footer type.
 *
 * @since 1.0.0
 */
class Footer extends Library_Document {

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['library_view'] = 'list';
		$properties['group']        = 'blocks';

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'footer';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Footer', 'sq-theme-core' );
	}
}
