<?php
/**
 * Galleria Structure
 *
 * @author   WooThemes
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Galleria_Structure' ) ) :

class Galleria_Structure {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'layout_adjustments' ) );
		add_filter( 'storefront_products_per_page', array( $this, 'products_per_page' ) );
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'change_breadcrumb_delimeter' ) );
	}

	/**
	 * Layout adjustments
	 * @return rearrange markup through add_action and remove_action
	 * @todo use storefront_is_woocommerce_activated() when Storefront 2.2 is available.
	 */
	public function layout_adjustments() {
		if ( class_exists( 'WooCommerce' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( 'Galleria_Structure', 'galleria_product_loop_title_price_wrap' ), 20 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 2 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 1 );
			add_action( 'woocommerce_after_shop_loop_item_title', array( 'Galleria_Structure', 'galleria_product_loop_title_price_wrap_close' ), 2 );

			add_action( 'woocommerce_before_subcategory_title', array( 'Galleria_Structure', 'galleria_product_loop_title_price_wrap' ), 11 );
			add_action( 'woocommerce_after_subcategory_title', array( 'Galleria_Structure', 'galleria_product_loop_title_price_wrap_close' ), 2 );

			remove_action( 'storefront_header', 'storefront_header_cart', 60 );
			add_action( 'storefront_header', 'storefront_header_cart', 4 );

			remove_action( 'storefront_header', 'storefront_product_search', 40 );
			add_action( 'storefront_header', 'storefront_product_search', 3 );
		}

		remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
		add_action( 'storefront_header', 'storefront_secondary_navigation', 6 );

		remove_action( 'storefront_header', 'storefront_site_branding', 20 );
		add_action( 'storefront_header', 'storefront_site_branding', 5 );

		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 30 );

		add_action( 'storefront_header', array( 'Galleria_Structure', 'galleria_primary_navigation_wrapper' ), 49 );
		add_action( 'storefront_header', array( 'Galleria_Structure', 'galleria_primary_navigation_wrapper_close' ), 61 );

		add_action( 'storefront_header', array( 'Galleria_Structure', 'galleria_top_bar_wrapper' ), 1 );
		add_action( 'storefront_header', array( 'Galleria_Structure', 'galleria_top_bar_wrapper_close' ), 6 );

		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'galleria_loop_item_wrapper' ), 5 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'galleria_loop_item_wrapper_close' ) );
	}

	/**
	 * Product title wrapper
	 * @return void
	 */
	public static function galleria_product_loop_title_price_wrap() {
		echo '<section class="g-product-title">';
	}

	/**
	 * Product title wrapper close
	 * @return void
	 */
	public static function galleria_product_loop_title_price_wrap_close() {
		echo '</section>';
	}

	/**
	 * Primary navigation wrapper
	 * @return void
	 */
	public static function galleria_primary_navigation_wrapper() {
		echo '<section class="g-primary-navigation">';
	}

	/**
	 * Primary navigation wrapper close
	 * @return void
	 */
	public static function galleria_primary_navigation_wrapper_close() {
		echo '</section>';
	}

	/**
	 * Top bar wrapper
	 * @return void
	 */
	public static function galleria_top_bar_wrapper() {
		echo '<section class="g-top-bar">';
	}

	/**
	 * Top bar wrapper close
	 * @return void
	 */
	public static function galleria_top_bar_wrapper_close() {
		echo '</section>';
	}

	/**
	 * Loop item wrapper
	 * @return void
	 */
	public function galleria_loop_item_wrapper() {
		echo '<div class="g-loop-item">';
	}

	/**
	 * Loop item wrapper close
	 * @return void
	 */
	public function galleria_loop_item_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Products per page
	 * @return int products to display per page
	 */
	public function products_per_page( $per_page ) {
		$per_page = 19;
		return intval( $per_page );
	}

	public function change_breadcrumb_delimeter( $defaults ) {
		$defaults['delimiter'] = ' <span>/</span> ';
		return $defaults;
	}
}

endif;

return new Galleria_Structure();
