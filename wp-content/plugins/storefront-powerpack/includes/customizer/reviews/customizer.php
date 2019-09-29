<?php
/**
 * Storefront Powerpack Customizer Reviews Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Reviews' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Reviews extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_REVIEWS_SECTION = 'sp_reviews_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'storefront_reviews_enable'           => 'disable',
				'storefront_reviews_heading_text'     => __( 'Recent Reviews', 'storefront-powerpack' ),
				'storefront_reviews_reviews_type'     => 'recent',
				'storefront_reviews_product'          => 0,
				'storefront_reviews_specific_reviews' => '',
				'storefront_reviews_layout'           => 'style-1',
				'storefront_reviews_gravatar'         => true,
				'storefront_reviews_number'           => '2',
				'storefront_reviews_columns'          => '2',
				'storefront_reviews_carousel'         => false,
				'storefront_reviews_star_color'       => '#96588a',
			);
		}

		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 *
		 * @since 2.0.0
		 */
		public function customize_preview_js() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'sp-reviews-customizer', SP_PLUGIN_URL . 'includes/customizer/reviews/assets/js/customizer' . $suffix . '.js', array( 'customize-preview' ), storefront_powerpack()->version, true );
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 2.0.0
		 */
		public function customize_register( $wp_customize ) {
			/**
			 * Reviews Section
			 */
			$wp_customize->add_section( self::POWERPACK_REVIEWS_SECTION, array(
				'title'    => __( 'Reviews', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 120,
			) );

	        /**
		     * On/off
		     */
			$wp_customize->add_setting( 'storefront_reviews_enable', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'storefront_reviews_enable', array(
				'label'    => __( 'Display on homepage template', 'storefront-powerpack' ),
				'description' => __( 'This option affects only the output of Reviews on the Homepage template. Shortcodes can still be used.', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_REVIEWS_SECTION,
				'settings' => 'storefront_reviews_enable',
				'type'     => 'select',
				'priority' => 5,
				'choices'  => array(
					'disable' => __( 'Disable', 'storefront-powerpack' ),
					'enable'  => __( 'Enable', 'storefront-powerpack' )
				),
			) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_reviews_enable_divider', array(
					'section'  => self::POWERPACK_REVIEWS_SECTION,
					'type'     => 'divider',
					'priority' => 7,
				) ) );
		    }

			/**
			 * Title
			 */
			$wp_customize->add_setting( 'storefront_reviews_heading_text', array(
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_heading_text', array(
				'label'    => __( 'Product reviews title', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_REVIEWS_SECTION,
				'settings' => 'storefront_reviews_heading_text',
				'type'     => 'text',
				'priority' => 10,
			) ) );

			$wp_customize->add_setting( 'storefront_reviews_reviews_type', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_reviews_type', array(
				'label'    => __( 'Reviews to display', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_REVIEWS_SECTION,
				'settings' => 'storefront_reviews_reviews_type',
				'type'     => 'select',
				'priority' => 11,
				'choices'  => array(
					'recent'           => 'Recent Reviews',
					'specific-product' => 'Reviews of a specific product',
					'specific-reviews' => 'Specific reviews',
				),
			) ) );

			/**
			 * Specific Product
			 */
			$wp_customize->add_setting( 'storefront_reviews_product', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_product', array(
				'label'           => __( 'Product ID', 'storefront-powerpack' ),
				'description'     => __( 'Display reviews from a specific product by adding the ID here', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_REVIEWS_SECTION,
				'settings'        => 'storefront_reviews_product',
				'type'            => 'text',
				'priority'        => 12,
				'active_callback' => array( $this, 'specific_product_callback' ),
			) ) );


			/**
			 * Specific Reviews
			 */
			$wp_customize->add_setting( 'storefront_reviews_specific_reviews', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_specific_reviews', array(
				'label'           => __( 'Review IDs', 'storefront-powerpack' ),
				'description'     => __( 'Comma separate specific review IDs to display them', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_REVIEWS_SECTION,
				'settings'        => 'storefront_reviews_specific_reviews',
				'type'            => 'text',
				'priority'        => 13,
				'active_callback' => array( $this, 'specific_reviews_callback' ),
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'layout_divider', array(
					'section'  => self::POWERPACK_REVIEWS_SECTION,
					'type'     => 'divider',
					'priority' => 14,
				) ) );
			}

			/**
			 * Style
			 */
			$wp_customize->add_setting( 'storefront_reviews_layout', array(
				'sanitize_callback' => 'sanitize_key'
			) );

			$wp_customize->add_control( new Storefront_Custom_Radio_Image_Control( $wp_customize, 'storefront_reviews_layout', array(
					'settings'    => 'storefront_reviews_layout',
					'section'     => self::POWERPACK_REVIEWS_SECTION,
					'label'       => __( 'Review display', 'storefront-powerpack' ),
					'description' => __( 'Choose a design/layout for the reviews', 'storefront-powerpack' ),
					'priority'    => 14,
					'choices'     => array(
						'style-1' => SP_PLUGIN_URL . 'includes/customizer/reviews/assets/images/style-1.png',
						'style-2' => SP_PLUGIN_URL . 'includes/customizer/reviews/assets/images/style-2.png',
						'style-3' => SP_PLUGIN_URL . 'includes/customizer/reviews/assets/images/style-3.png',
					)
			) ) );

			/**
			 * Gravatar
			 */
			$wp_customize->add_setting( 'storefront_reviews_gravatar', array(
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_gravatar', array(
				'label'       => __( 'Display Gravatar', 'storefront-powerpack' ),
				'description' => __( 'Display the reviwers Gravatar?', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_REVIEWS_SECTION,
				'settings'    => 'storefront_reviews_gravatar',
				'type'        => 'checkbox',
				'priority'    => 15,
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'id_divider', array(
					'section'  => self::POWERPACK_REVIEWS_SECTION,
					'type'     => 'divider',
					'priority' => 17,
				) ) );
			}

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'id_divider', array(
					'section'  => self::POWERPACK_REVIEWS_SECTION,
					'type'     => 'divider',
					'priority' => 18,
				) ) );
			}

			/**
			 * Number
			 */
			$wp_customize->add_setting( 'storefront_reviews_number', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_number', array(
				'label'       => __( 'Maximum reviews', 'storefront-powerpack' ),
				'description' => __( 'The maximum number of reviews to display', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_REVIEWS_SECTION,
				'settings'    => 'storefront_reviews_number',
				'type'        => 'select',
				'priority'    => 19,
				'choices'     => array(
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
				),
			) ) );

			/**
			 * Columns
			 */
			$wp_customize->add_setting( 'storefront_reviews_columns', array(
				'sanitize_callback'	=> 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_columns', array(
				'label'       => __( 'Review columns', 'storefront-powerpack' ),
				'description' => __( 'The number of columns reviews are arranged in to', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_REVIEWS_SECTION,
				'settings'    => 'storefront_reviews_columns',
				'type'        => 'select',
				'priority'    => 20,
				'choices'     => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			) ) );

			 if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'carousel_divider_before', array(
					'section'  => self::POWERPACK_REVIEWS_SECTION,
					'type'     => 'divider',
					'priority' => 20,
				) ) );
			}

			/**
			 * Carousel
			 */
			$wp_customize->add_setting( 'storefront_reviews_carousel', array(
				'sanitize_callback'	=> 'absint',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'storefront_reviews_carousel', array(
				'label'       => __( 'Carousel', 'storefront-powerpack' ),
				'description' => __( 'Display the reviews in a carousel', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_REVIEWS_SECTION,
				'settings'    => 'storefront_reviews_carousel',
				'type'        => 'checkbox',
				'priority'    => 20,
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'carousel_divider_after', array(
					'section'  => self::POWERPACK_REVIEWS_SECTION,
					'type'     => 'divider',
					'priority' => 21,
				) ) );
			}

			/**
			 * Color picker
			 */
			$wp_customize->add_setting( 'storefront_reviews_star_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_reviews_star_color', array(
				'label'       => __( 'Star color', 'storefront-powerpack' ),
				'description' => __( 'The color of the star ratings throughout the store', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_REVIEWS_SECTION,
				'settings'    => 'storefront_reviews_star_color',
				'priority'    => 22,
			) ) );
		}

		/**
		 * Specific product callback
		 * @return bool
		 */
		public function specific_product_callback( $control ) {
			return $control->manager->get_setting( 'storefront_reviews_reviews_type' )->value() == 'specific-product' ? true : false;
		}

		/**
		 * Specific reviews callback
		 * @return bool
		 */
		public function specific_reviews_callback( $control ) {
			return $control->manager->get_setting( 'storefront_reviews_reviews_type' )->value() == 'specific-reviews' ? true : false;
		}

		/**
		 * Homepage callback
		 * @return bool
		 */
		public function storefront_homepage_template_callback() {
			return is_page_template( 'template-homepage.php' ) ? true : false;
		}

	}

endif;

return new SP_Customizer_Reviews();
