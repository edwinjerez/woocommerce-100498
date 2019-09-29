<?php
/**
 * Storefront Powerpack Reviews Admin Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Reviews_Admin' ) ) :

	/**
	 * The admin class
	 */
	class SP_Reviews_Admin {
		/**
		 * Setup class.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {

			// Shortcode generator
			require_once dirname( __FILE__ ) . '/shortcodes/class-sp-reviews-shortcode-generator.php';
		}
	}

endif;

return new SP_Reviews_Admin();
