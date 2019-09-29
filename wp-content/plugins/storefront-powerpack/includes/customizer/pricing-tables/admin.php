<?php
/**
 * Storefront Powerpack Pricing Tables Admin Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Pricing_Tables_Admin' ) ) :

	/**
	 * The admin class
	 */
	class SP_Pricing_Tables_Admin {
		/**
		 * Setup class.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {

			// Shortcode generator
			require_once dirname( __FILE__ ) . '/shortcodes/class-sp-pricing-tables-shortcode-generator.php';
		}
	}

endif;

return new SP_Pricing_Tables_Admin();
