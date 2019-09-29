<?php
/**
 * Galleria Customizer
 *
 * Handles Customizer tweaks and modifications
 *
 * @author   WooThemes
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Galleria_Customizer' ) ) :

class Galleria_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$theme 					= wp_get_theme( 'storefront' );
		$storefront_version 	= $theme['Version'];

		add_action( 'wp_enqueue_scripts', 	             array( $this, 'add_customizer_css' ), 999 );
		add_action( 'customize_register', 	             array( $this, 'customize_register' ) );
		add_filter( 'body_class',                        array( $this, 'body_class' ) );
		add_filter( 'storefront_setting_default_values', array( $this, 'galleria_defaults' ) );

		/**
		 * The following can be removed when Storefront 2.1 lands
		 */
		add_action( 'customize_register', 	array( $this, 'edit_defaults' ), 99 );
		add_action( 'init',					array( $this, 'default_theme_mod_values' ) );
		if ( version_compare( $storefront_version, '2.0.0', '<' ) ) {
			add_action( 'init',				array( $this, 'default_theme_settings' ) );
		}
	}

	/**
	 * Returns an array with default storefront and extension options
	 * @return array
	 */
	public function galleria_defaults( $defaults = array() ) {
		$defaults['storefront_heading_color']               = '#2b2b2b';
		$defaults['storefront_footer_heading_color']        = '#2b2b2b';
		$defaults['storefront_header_background_color']     = '#ffffff';
		$defaults['storefront_footer_background_color']     = '#ffffff';
		$defaults['storefront_header_link_color']           = '#2b2b2b';
		$defaults['storefront_header_text_color']           = '#555555';
		$defaults['storefront_footer_link_color']           = '#000000';
		$defaults['storefront_text_color']                  = '#555555';
		$defaults['storefront_footer_text_color']           = '#555555';
		$defaults['storefront_accent_color']                = '#2b2b2b';
		$defaults['storefront_button_background_color']     = '#ffffff';
		$defaults['storefront_button_text_color']           = '#2b2b2b';
		$defaults['storefront_button_alt_background_color'] = '#2b2b2b';
		$defaults['storefront_button_alt_text_color']       = '#ffffff';
		$defaults['background_color']                       = 'ffffff';

		return $defaults;
	}

	/**
	 * Remove / Set Customizer settings (including extensions).
	 * @return void
	 */
	public function edit_defaults( $wp_customize ) {
		// Remove controls that are incompatible with this theme
		$wp_customize->get_setting( 'background_color' )->transport 				= 'refresh';
		$wp_customize->get_setting( 'storefront_header_link_color' )->transport 	= 'refresh';

		// Set default values for settings in customizer
		foreach ( Galleria_Customizer::galleria_defaults() as $mod => $val ) {
			$setting = $wp_customize->get_setting( $mod );

			if ( is_object( $setting ) ) {
				$setting->default = $val;
			}
		}
	}

	/**
	 * Returns a default theme_mod value if there is none set.
	 * @uses galleria_defaults()
	 * @return void
	 */
	public function default_theme_mod_values() {
		foreach ( Galleria_Customizer::galleria_defaults() as $mod => $val ) {
			add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
				return $setting ? $setting : $val;
			});
		}
	}

	/**
	 * Sets default theme color filters for storefront color values.
	 * This function is required for Storefront < 2.0.0 support
	 * @uses galleria_defaults()
	 * @return void
	 */
	public function default_theme_settings() {
		$prefix_regex = '/^storefront_/';
		foreach ( self::galleria_defaults() as $mod => $val) {
			if ( preg_match( $prefix_regex, $mod ) ) {
				$filter = preg_replace( $prefix_regex, 'storefront_default_', $mod );
				add_filter( $filter, function( $_ ) use ( $val ) {
					return $val;
				}, 99 );
			}
		}
	}

	/**
	 * Add body classes based on Galleria settings
	 *
	 * @param  array $classes the body classes.
	 * @return array $classes the body classes.
	 */
	public function body_class( $classes ) {
		$preloader = get_theme_mod( 'galleria_preloader' );

		if ( false === $preloader ) {
			$classes[] = 'preloader-disabled';
		} else {
			$classes[] = 'preloader-enabled';
		}

		return $classes;
	}

	/**
	 * Adds custom controls
	 * @return void
	 */
	public function customize_register( $wp_customize ) {
		$wp_customize->add_section( 'storefront_galleria' , array(
			'title'      			=> __( 'Galleria', 'galleria' ),
			'priority'   			=> 75,
		) );

		$wp_customize->add_setting( 'galleria_masonry_layout', array(
			'default'           => true,
			'sanitize_callback' => 'storefront_sanitize_checkbox',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'galleria_masonry_layout', array(
			'label'	   		=> __( 'Tiled Product Layout', 'galleria' ),
			'description' 	=> __( 'Toggle the tiled product layout on shop pages. Disabling this feature will make all product images the same size.', 'galleria' ),
			'section'  		=> 'storefront_galleria',
			'settings' 		=> 'galleria_masonry_layout',
			'type'          => 'checkbox',
			'priority' 		=> 10,
		) ) );

		$wp_customize->add_setting( 'galleria_preloader', array(
			'default'           => true,
			'sanitize_callback' => 'storefront_sanitize_checkbox',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'galleria_preloader', array(
			'label'	   		=> __( 'Preloader', 'galleria' ),
			'description' 	=> __( 'Toggle preloading effect on page load.', 'galleria' ),
			'section'  		=> 'storefront_galleria',
			'settings' 		=> 'galleria_preloader',
			'type'          => 'checkbox',
			'priority' 		=> 20,
		) ) );
	}

	/**
	 * Add custom CSS based on settings in Storefront core
	 * @return void
	 */
	public function add_customizer_css() {
		$header_bg_color 				= get_theme_mod( 'storefront_header_background_color' );
		$accent_color					= get_theme_mod( 'storefront_accent_color' );
		$header_link_color 				= get_theme_mod( 'storefront_header_link_color' );
		$text_color 					= get_theme_mod( 'storefront_text_color' );
		$button_text_color 				= get_theme_mod( 'storefront_button_text_color' );
		$button_background_color 		= get_theme_mod( 'storefront_button_background_color' );
		$button_alt_background_color 	= get_theme_mod( 'storefront_button_alt_background_color' );
		$button_alt_text_color 			= get_theme_mod( 'storefront_button_alt_text_color' );
		$bg_color                       = storefront_get_content_background_color();

		$brighten_factor 				= apply_filters( 'storefront_brighten_factor', 25 );
		$darken_factor 					= apply_filters( 'storefront_darken_factor', -25 );

		$style = '
			.onsale {
				background-color: ' . $button_alt_background_color . ';
				color: ' . $button_alt_text_color . ';
			}

			.woocommerce-pagination .page-numbers li .page-numbers.current,
			.pagination .page-numbers li .page-numbers.current {
				background-color: ' . $bg_color . ';
				color: ' . $text_color . ';
			}

			button, input[type="button"], input[type="reset"], input[type="submit"], .button, .added_to_cart, .widget a.button, .site-header-cart .widget_shopping_cart a.button,
			button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, .button:hover, .added_to_cart:hover, .widget a.button:hover, .site-header-cart .widget_shopping_cart a.button:hover {
				border-color: ' . $button_text_color . ';
			}

			.widget-area .widget a.button,
			.widget-area .widget a.button:hover {
				color: ' . $button_text_color . ';
			}

			.widget a.button.checkout,
			.widget a.button.checkout:hover,
			.widget a.button.alt,
			.widget a.button.alt:hover {
				color: ' . $button_alt_text_color . ';
			}


			.main-navigation ul li.smm-active .smm-mega-menu a.button,
			.main-navigation ul li.smm-active .smm-mega-menu a.button:hover {
				border-color: ' . $button_text_color . ' !important;
				color: ' . $button_text_color . ' !important;
				background-color: ' . $button_background_color . ' !important;
				border-width: 2px !important;
				border-style: solid !important;
			}

			@media screen and (min-width: 768px) {
				ul.products li.product:not(.product-category) .g-product-title,
				ul.products li.product:not(.product-category) .g-product-title h3,
				ul.products li.product:not(.product-category) .g-product-title h2,
				ul.products li.product:not(.product-category) .g-product-title .woocommerce-loop-category__title {
					background-color: ' . $header_link_color . ';
					color: ' . $header_bg_color . ';
				}

				ul.products li.product-category a,
				.smm-menu,
				.main-navigation .smm-mega-menu {
					background-color: ' . $header_bg_color . ';
				}

				ul.products li.product-category .g-product-title h3,
				ul.products li.product-category .g-product-title h2,
				ul.products li.product-category .g-product-title .woocommerce-loop-category__title {
					color: ' . $header_link_color . ';
				}

				ul.products li.product .g-product-title .price {
					color: ' . $header_bg_color . ';
				}

				.main-navigation ul.menu > li:first-child:before, .main-navigation ul.menu > li:last-child:after, .main-navigation ul.nav-menu > li:first-child:before, .main-navigation ul.nav-menu > li:last-child:after {
					color: ' . $header_link_color . ';
				}

				.site-header .g-primary-navigation,
				.footer-widgets,
				.site-footer,
				.main-navigation ul.menu ul.sub-menu, .main-navigation ul.nav-menu ul.sub-menu,
				.site-header-cart .widget_shopping_cart,
				.site-branding h1 a,
				.site-header .g-top-bar,
				.main-navigation .smm-row,
				.main-navigation .smm-mega-menu {
					border-color: ' . $header_link_color . ';
				}

				.site-header .site-branding {
					border-bottom-color: ' . $header_link_color . ';
				}

				ul.products li.product .star-rating span:before,
				ul.products li.product .star-rating:before {
					color: ' . $header_bg_color . ';
				}
			}';

		wp_add_inline_style( 'storefront-child-style', $style );
	}
}

endif;

return new Galleria_Customizer();