<?php
/**
 * Adds a link for webfonts.
 *
 * @package     Kirki
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license    https://opensource.org/licenses/MIT
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'Kirki_Modules_Webfonts_Link' ) ) {
	/**
	 * Manages the way Google Fonts are enqueued.
	 */
	final class Kirki_Modules_Webfonts_Link {

		/**
		 * The config ID.
		 *
		 * @access protected
		 * @since 3.0.0
		 * @var string
		 */
		protected $config_id;

		/**
		 * The Kirki_Modules_Webfonts object.
		 *
		 * @access protected
		 * @since 3.0.0
		 * @var object
		 */
		protected $webfonts;

		/**
		 * The Kirki_Fonts_Google object.
		 *
		 * @access protected
		 * @since 3.0.0
		 * @var Kirki_Fonts_Google
		 */
		protected $googlefonts;

		/**
		 * The google link
		 *
		 * @access public
		 * @var string
		 */
		public $link = '';

		/**
		 * Constructor.
		 *
		 * @access public
		 * @since 3.0
		 * @param string $config_id   The config-ID.
		 * @param object $webfonts    The Kirki_Modules_Webfonts object.
		 * @param object $googlefonts The Kirki_Fonts_Google object.
		 * @param array  $args        Extra args we want to pass.
		 */
		public function __construct( $config_id, $webfonts, $googlefonts, $args = array() ) {

			$this->config_id   = $config_id;
			$this->webfonts    = $webfonts;
			$this->googlefonts = $googlefonts;
		}

	}
}