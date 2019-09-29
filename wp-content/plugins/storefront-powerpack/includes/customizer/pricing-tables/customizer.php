<?php
/**
 * Storefront Powerpack Customizer Pricing Tables Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Pricing_Tables' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Pricing_Tables extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_PRICING_TABLES_SECTION = 'sp_pricing_tables_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'spt_alignment'                         => 'left',
				'spt_header_background_color'           => '#2c2d33',
				'spt_header_text_color'                 => '#ffffff',
				'spt_header_highlight_background_color' => '#96588a',
				'spt_header_highlight_text_color'       => '#ffffff',
				'spt_columns'                           => '3'
			);
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 2.0.0
		 */
		public function customize_register( $wp_customize ) {
			require_once dirname( __FILE__ ) . '/controls/class-sp-pricing-tables-images-control.php';

			/**
			* Pricing Tables Section
			*/
			$wp_customize->add_section( self::POWERPACK_PRICING_TABLES_SECTION, array(
				'title'    => __( 'Pricing Tables', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 100,
			) );

			/**
			 * Image selector radios
			 */
			$wp_customize->add_setting( 'spt_alignment', array(
				'sanitize_callback' => 'esc_attr'
			) );

			$wp_customize->add_control( new SP_Pricing_Tables_Layout_Control( $wp_customize, 'spt_alignment', array(
				'label'    => __( 'Content alignment', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRICING_TABLES_SECTION,
				'settings' => 'spt_alignment',
				'priority' => 10,
			) ) );

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'spt_divider', array(
					'section'  => self::POWERPACK_PRICING_TABLES_SECTION,
					'type'     => 'divider',
					'priority' => 15,
				) ) );
			}

			/**
			 * Colors
			 */
			$wp_customize->add_setting( 'spt_header_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_background_color', array(
				'label'       => __( 'Headings', 'storefront-powerpack' ),
				'description' => __( 'Column header background color', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRICING_TABLES_SECTION,
				'settings'    => 'spt_header_background_color',
				'priority'    => 30,
			) ) );

			$wp_customize->add_setting( 'spt_header_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_text_color', array(
				'description' => __( 'Column header text color', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRICING_TABLES_SECTION,
				'settings'    => 'spt_header_text_color',
				'priority'    => 40,
			) ) );

			$wp_customize->add_setting( 'spt_header_highlight_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_highlight_background_color', array(
				'description' => __( 'Highlighted column header background color', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRICING_TABLES_SECTION,
				'settings'    => 'spt_header_highlight_background_color',
				'priority'    => 50,
			) ) );

			$wp_customize->add_setting( 'spt_header_highlight_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'spt_header_highlight_text_color', array(
				'description' => __( 'Highlighted column header text color', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PRICING_TABLES_SECTION,
				'settings'    => 'spt_header_highlight_text_color',
				'priority'    => 60,
			) ) );

			/**
			 * Pricing table columns
			 */
			$wp_customize->add_setting( 'spt_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'spt_columns', array(
				'label'    => __( 'Pricing table columns', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRICING_TABLES_SECTION,
				'settings' => 'spt_columns',
				'type'     => 'select',
				'priority' => 70,
				'choices'  => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			) ) );
		}
	}

endif;

return new SP_Customizer_Pricing_Tables();