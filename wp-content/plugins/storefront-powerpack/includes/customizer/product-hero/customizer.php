<?php
/**
 * Storefront Powerpack Customizer Product Hero Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Product_Hero' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Product_Hero extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_PRODUCT_HERO_SECTION = 'sp_product_hero_section';

		/**
		 * Setup class.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'customize_preview_init', array( $this, 'customize_preview' ) );
		}

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function setting_defaults() {
			return $args = array(
				'sprh_enable'                => 'disable',
				'sprh_featured_product'      => 'default',
				'sprh_alignment'             => 'left',
				'sprh_layout'                => 'full',
				'sprh_hero_full_height'      => false,
				'sprh_product_image'         => true,
				'sprh_product_price'         => true,
				'sprh_product_rating'        => true,
				'sprh_background_color'      => apply_filters( 'storefront_default_header_background_color', '#2c2d33' ),
				'sprh_overlay_color'         => apply_filters( 'storefront_product_hero_default_overlay_color', '#000000' ),
				'sprh_overlay_opacity'       => '0.5',
				'sprh_hero_background_img'   => '',
				'sprh_background_size'       => 'auto',
				'sprh_hero_parallax'         => true,
				'sprh_parallax_scroll_ratio' => '0.5',
				'sprh_parallax_offset'       => 0,
				'sprh_hero_heading_text'     => '',
				'sprh_heading_color'         => apply_filters( 'storefront_default_header_link_color', '#ffffff' ),
				'sprh_hero_text'             => '',
				'sprh_hero_text_color'       => apply_filters( 'storefront_default_header_text_color', '#5a6567'),
				'sprh_hero_link_color'       => apply_filters( 'storefront_default_accent_color', '#96588a' )
			);
		}

		/**
		 * Enqueue customize preview scripts.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function customize_preview() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'sp-product-hero-customizer', SP_PLUGIN_URL . 'includes/customizer/product-hero/assets/js/customizer' . $suffix . '.js', array( 'customize-preview' ), storefront_powerpack()->version, true );
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 2.0.0
		 * @return void
		 */
		public function customize_register( $wp_customize ) {
			require_once dirname( __FILE__ ) . '/controls/class-sp-product-hero-products-control.php';
			require_once dirname( __FILE__ ) . '/controls/class-sp-product-hero-content-layout-control.php';

	        /**
		     * On/off
		     */
			$wp_customize->add_setting( 'sprh_enable', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sprh_enable', array(
				'label'    => __( 'Display on homepage template', 'storefront-powerpack' ),
				'description' => __( 'This option affects only the output of Product Hero on the Homepage template. Shortcodes can still be used.', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_enable',
				'type'     => 'select',
				'priority' => 5,
				'choices'  => array(
					'disable' => __( 'Disable', 'storefront-powerpack' ),
					'enable'  => __( 'Enable', 'storefront-powerpack' )
				),
			) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sprh_enable_divider', array(
					'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 7,
				) ) );
		    }

			$wp_customize->add_section( self::POWERPACK_PRODUCT_HERO_SECTION, array(
				'title'              => __( 'Product Hero', 'storefront-powerpack' ),
				'description'        => __( 'Customise the appearance and content of the hero component that is displayed on your homepage.', 'storefront-powerpack' ),
				'description_hidden' => true,
				'panel'              => self::POWERPACK_PANEL,
				'priority'           => 130,
			) );

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'sprh_title_content', array(
					'label'    => __( 'Content', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
					'priority' => 10
				) ) );
			}

			/**
			 * Product selector
			 * See class-control-products.php
			 */
			$wp_customize->add_setting( 'sprh_featured_product', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new SP_Product_Hero_Products_Control( $wp_customize, 'sprh_featured_product', array(
					'label'       => __( 'Featured product', 'storefront-powerpack' ),
					'description' => __( 'Select a product to be featured in the product hero', 'storefront-powerpack' ),
					'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
					'settings'    => 'sprh_featured_product',
					'priority'    => 20,
			) ) );

			/**
			 * Product image
			 */
			$wp_customize->add_setting( 'sprh_product_image', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'sprh_product_image', array(
				'label'       => __( 'Product image', 'storefront-powerpack' ),
				'description' => __( 'Display the product featured image', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_product_image',
				'type'        => 'checkbox',
				'priority'    => 30,
			) );

			/**
			 * Product price
			 */
			$wp_customize->add_setting( 'sprh_product_price', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'sprh_product_price', array(
				'label'       => __( 'Product price / add to cart', 'storefront-powerpack' ),
				'description' => __( 'Display the product price / add to cart button', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_product_price',
				'type'        => 'checkbox',
				'priority'    => 40,
			) );

			/**
			 * Product rating
			 */
			$wp_customize->add_setting( 'sprh_product_rating', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'sprh_product_rating', array(
				'label'       => __( 'Product rating', 'storefront-powerpack' ),
				'description' => __( 'Display the product rating', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_product_rating',
				'type'        => 'checkbox',
				'priority'    => 50,
			) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_product_hero_divider_1', array(
						'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
						'type'     => 'divider',
						'priority' => 60,
				) ) );
			}

			/**
			 * Heading Text
			 */
			$wp_customize->add_setting( 'sprh_hero_heading_text', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( 'sprh_hero_heading_text', array(
				'label'       => __( 'Heading text', 'storefront-powerpack' ),
				'description' => __( '(Leave blank to display product title)', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_hero_heading_text',
				'type'        => 'text',
				'priority'    => 70,
			) );

			/**
			 * Heading text color
			 */
			$wp_customize->add_setting( 'sprh_heading_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sprh_heading_color', array(
				'label'    => 'Heading text color',
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_heading_color',
				'priority' => 80,
			) ) );

			/**
			 * Text
			 */
			$wp_customize->add_setting( 'sprh_hero_text', array(
				'sanitize_callback' => 'wp_kses_post'
			) );

			$wp_customize->add_control( 'sprh_hero_text', array(
				'label'       => __( 'Description text', 'storefront-powerpack' ),
				'description' => __( '(Leave blank to display product description)', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_hero_text',
				'type'        => 'textarea',
				'priority'    => 90,
			) );

	        /**
		     * Text color
		     */
			$wp_customize->add_setting( 'sprh_hero_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sprh_hero_text_color', array(
				'label'    => 'Description text color',
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_hero_text_color',
				'priority' => 100,
			) ) );

			/**
			 * Link color
			 */
			$wp_customize->add_setting( 'sprh_hero_link_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sprh_hero_link_color', array(
				'label'    => 'Link color',
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_hero_link_color',
				'priority' => 110,
			) ) );


			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_product_hero_divider_2', array(
						'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
						'type'     => 'divider',
						'priority' => 115,
				) ) );
			}

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'sprh_title_background', array(
					'label'    => __( 'Background', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
					'priority' => 120
				) ) );
			}


			/**
			 * Background
			 */
			$wp_customize->add_setting( 'sprh_hero_background_img', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'sprh_hero_background_img', array(
				'label'    => __( 'Background image', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_hero_background_img',
				'priority' => 130,
			) ) );

			/**
			 * Background size
			 */
			$wp_customize->add_setting( 'sprh_background_size', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( 'sprh_background_size', array(
				'label'    => __( 'Background size', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_background_size',
				'type'     => 'select',
				'priority' => 140,
				'choices'  => array(
					'auto'  => 'Default',
					'cover' => 'Cover',
				)
			) );

			/**
			 * Background Color
			 */
			$wp_customize->add_setting( 'sprh_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sprh_background_color', array(
				'label'    => 'Background color',
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_background_color',
				'priority' => 150,
			) ) );


			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_product_hero_divider_3', array(
						'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
						'type'     => 'divider',
						'priority' => 155,
				) ) );
			}

			/**
			 * Parallax
			 */
			$wp_customize->add_setting( 'sprh_hero_parallax', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'sprh_hero_parallax', array(
				'label'       => __( 'Parallax', 'storefront-powerpack' ),
				'description' => __( 'Enable the parallax scrolling effect', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_hero_parallax',
				'type'        => 'checkbox',
				'priority'    => 160,
			) );

	        /**
		     * Parallax scroll speed
		     */
			$wp_customize->add_setting( 'sprh_parallax_scroll_ratio', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( 'sprh_parallax_scroll_ratio', array(
				'label'       => __( 'Parallax scroll speed', 'storefront-powerpack' ),
				'description' => __( 'The speed at which the parallax background scrolls relative to the window', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_parallax_scroll_ratio',
				'type'        => 'select',
				'priority'    => 170,
				'choices'     => array(
					'0.25' => '25%',
					'0.5'  => '50%',
					'0.75' => '75%',
				),
			) );

			/**
			 * Parallax Offset
			 */
			$wp_customize->add_setting( 'sprh_parallax_offset', array(
				'sanitize_callback' => 'esc_attr',
			) );

			$wp_customize->add_control( 'sprh_parallax_offset', array(
				'type'        => 'range',
				'priority'    => 180,
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'label'       => __( 'Parallax offset', 'storefront-powerpack' ),
				'description' => __( 'Offset the starting position of your background image', 'storefront-powerpack' ),
				'input_attrs' => array(
					'min'   => -500,
					'max'   => 500,
					'step'  => 1,
				),
			) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_product_hero_divider_4', array(
						'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
						'type'     => 'divider',
						'priority' => 185,
				) ) );
			}

			/**
			 * Overlay color
			 */
			$wp_customize->add_setting( 'sprh_overlay_color', array(
				'description'       => __( 'Specify the overlay background color', 'storefront-powerpack' ),
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sprh_overlay_color', array(
				'label'    => 'Overlay color',
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_overlay_color',
				'priority' => 190,
			) ) );

			/**
			 * Overlay opacity
		 	*/
			$wp_customize->add_setting( 'sprh_overlay_opacity', array(
				'sanitize_callback' => array( $this, 'sanitize_opacity' ),
			) );

			$wp_customize->add_control( 'sprh_overlay_opacity', array(
				'label'    => __( 'Overlay opacity', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_overlay_opacity',
				'type'     => 'select',
				'priority' => 200,
				'choices'  => array(
					'0'   => '0%',
					'0.1' => '10%',
					'0.2' => '20%',
					'0.3' => '30%',
					'0.4' => '40%',
					'0.5' => '50%',
					'0.6' => '60%',
					'0.7' => '70%',
					'0.8' => '80%',
					'0.9' => '90%',
				),
			) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_product_hero_divider_5', array(
						'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
						'type'     => 'divider',
						'priority' => 205,
				) ) );
			}

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'sprh_title_layout', array(
					'label'    => __( 'Layout', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
					'priority' => 210
				) ) );
			}

			/**
			 * Content layout
			 * See class-control-content-layout.php
			 */
			$wp_customize->add_setting( 'sprh_alignment', array(
				'sanitize_callback' => 'esc_attr'
			) );

			$wp_customize->add_control( new SP_Product_Hero_Layout_Control( $wp_customize, 'sprh_alignment', array(
					'label'    => __( 'Content layout', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
					'settings' => 'sprh_alignment',
					'priority' => 230,
			) ) );

			/**
			 * Layout
			 */
			$wp_customize->add_setting( 'sprh_layout', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( 'sprh_layout', array(
				'label'    => __( 'Hero width', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings' => 'sprh_layout',
				'type'     => 'select',
				'priority' => 240,
				'choices'  => array(
					'full'  => 'Full width',
					'fixed' => 'Fixed width',
				),
			) );

			/**
			 * Full height
			 */
			$wp_customize->add_setting( 'sprh_hero_full_height', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'sprh_hero_full_height', array(
				'label'       => __( 'Full height', 'storefront-powerpack' ),
				'description' => __( 'Set the hero component to full height', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRODUCT_HERO_SECTION,
				'settings'    => 'sprh_hero_full_height',
				'type'        => 'checkbox',
				'priority'    => 250,
			) );
		}

		/**
		 * Sanitize the opacity option.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public function sanitize_opacity( $input ) {
			$valid = array(
				'0'   => '0%',
				'0.1' => '10%',
				'0.2' => '20%',
				'0.3' => '30%',
				'0.4' => '40%',
				'0.5' => '50%',
				'0.6' => '60%',
				'0.7' => '70%',
				'0.8' => '80%',
				'0.9' => '90%',
			);

			if ( array_key_exists( $input, $valid ) ) {
				return $input;
			} else {
				return '';
			}
		}
	}

endif;

return new SP_Customizer_Product_Hero();