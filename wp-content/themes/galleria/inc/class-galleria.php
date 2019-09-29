<?php
/**
 * Galleria Functions
 *
 * @author   WooThemes
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Galleria' ) ) :

class Galleria {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts',               array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts',               array( $this, 'enqueue_child_styles' ), 99 );
		add_filter( 'storefront_woocommerce_args',      array( $this, 'woocommerce_support' ) );
		add_filter( 'swc_product_columns_default',      array( $this, 'galleria_loop_columns' ) );
		add_filter( 'storefront_related_products_args', array( $this, 'galleria_related_products_args' ) );
		add_filter( 'woocommerce_cross_sells_columns',  array( $this, 'galleria_cross_sells_columns' ) );
		add_filter( 'storefront_custom_logo_args',      array( $this, 'galleria_custom_logo_args' ) );
		add_action( 'wp',                               array( $this, 'galleria_storefront_woocommerce_customiser' ), 99 );
		add_action( 'body_class',                       array( $this, 'edit_body_class' ) );

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
			add_filter( 'storefront_loop_columns',      array( $this, 'galleria_loop_columns' ) );
		}
	}

	/**
	 * Override Storefront default theme settings for WooCommerce.
	 * @return array the modified arguments
	 */
	public function woocommerce_support( $args ) {
		$args['single_image_width']              = 501;
		$args['thumbnail_image_width']           = 684;
		$args['product_grid']['default_columns'] = 4;
		$args['product_grid']['default_rows']    = 3;

		return $args;
	}

	/**
	 * Enqueue Storefront Styles
	 * @return void
	 */
	public function enqueue_styles() {
		global $storefront_version;

		wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
	}

	/**
	 * Enqueue Storechild Styles
	 * @return void
	 * @todo use storefront_is_woocommerce_activated() when Storefront 2.2 is available.
	 */
	public function enqueue_child_styles() {
		global $galleria_version;

		wp_style_add_data( 'storefront-child-style', 'rtl', 'replace' );

		wp_enqueue_style( 'karla', '//fonts.googleapis.com/css?family=Karla:400,700', array( 'storefront-child-style' ) );
    	wp_enqueue_style( 'libre-baskerville', '//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic', array( 'storefront-child-style' ) );

		wp_enqueue_script( 'galleria', get_stylesheet_directory_uri() . '/assets/js/galleria.min.js', array( 'jquery' ), $galleria_version, true );
		wp_enqueue_script( 'modernizr', get_stylesheet_directory_uri() . '/assets/js/modernizr.min.js', array( 'jquery' ), '2.8.3', true );

		if ( class_exists( 'WooCommerce' ) ) {
			$masonry = get_theme_mod( 'galleria_masonry_layout', 1 );

			if ( $masonry && ( is_shop() || is_product_category() || is_product_tag() ) ) {
				wp_enqueue_script( 'masonry', array( 'jquery' ) );
				wp_enqueue_script( 'galleria-load-masonry', get_stylesheet_directory_uri() . '/assets/js/load-masonry.min.js', array( 'masonry' ), $galleria_version, true );
			}
		}
	}

	/**
	 * Adds a class to the body tag depending on whether the masonry layout is active or not.
	 * @return array body classes
	 */
	public function edit_body_class( $classes ) {
		global $storefront_version;

		if ( class_exists( 'WooCommerce' ) ) {
			$masonry = get_theme_mod( 'galleria_masonry_layout', 1 );

			if ( $masonry && ( is_shop() || is_product_category() || is_product_tag() ) ) {
				$classes[] = 'galleria-masonry';
			} else {
				$classes[] = 'galleria-no-masonry';
			}
		}

		if ( version_compare( $storefront_version, '2.3.0', '>=' ) ) {
			$classes[] = 'storefront-2-3';
		}

		return $classes;
	}

	/**
	 * Shop columns
	 * @return int number of columns
	 */
	public function galleria_loop_columns( $columns ) {
		$columns = 4;
		return $columns;
	}

	/**
	 * Adjust related products columns
	 * @return array $args the modified arguments
	 */
	public function galleria_related_products_args( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']		= 4;

		return $args;
	}

	/**
	 * Cross-Sells columns
	 * @return int number of columns
	 */
	public function galleria_cross_sells_columns( $columns ) {
		return 4;
	}

	/**
	 * Filter custom logo suggested size.
	 * @return array $args the modified arguments
	 */
	public function galleria_custom_logo_args( $args ) {
		$args['width']  = 390;
		$args['height'] = 145;

		return $args;
	}

	/**
	 * Storefront WooCommerce Customiser compatibility tweaks
	 */
	public function galleria_storefront_woocommerce_customiser() {
		if ( class_exists( 'Storefront_WooCommerce_Customiser' ) ) {
			$cart_link 	= get_theme_mod( 'swc_header_cart', true );
			$search 	= get_theme_mod( 'swc_header_search', true );

			if ( false == $cart_link ) {
				remove_action( 'storefront_header', 'storefront_header_cart', 4 );
			} else {
				add_action( 'storefront_header', 'storefront_header_cart', 4 );
			}

			if ( false == $search ) {
				remove_action( 'storefront_header', 'storefront_product_search', 3 );
			} else {
				add_action( 'storefront_header', 'storefront_product_search', 3 );
			}
		}
	}
}

endif;

return new Galleria();