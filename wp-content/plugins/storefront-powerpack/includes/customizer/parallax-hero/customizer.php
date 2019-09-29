<?php
/**
 * Storefront Powerpack Customizer Parallax Hero Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Parallax_Hero' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Parallax_Hero extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_PARALLAX_HERO_SECTION = 'sp_parallax_hero_section';

		/**
		 * Setup class.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'customize_preview_init', array( $this, 'customize_preview' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'control_scripts' ) );
		}

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function setting_defaults() {
			return $args = array(
				'sph_hero_enable'                          => 'disable',
				'sph_hero_heading_text'                    => __( 'Heading Text', 'storefront-powerpack' ),
				'sph_heading_color'                        => '#ffffff',
				'sph_hero_text'                            => __( 'Description Text', 'storefront-powerpack' ),
				'sph_hero_text_color'                      => '#5a6567',
				'sph_hero_link_color'                      => '#96588a',
				'sph_hero_button_text'                     => __( 'Go shopping', 'storefront-powerpack' ),
				'sph_hero_button_url'                      => home_url(),
				'sph_hero_background_media'                => 'none',
				'sph_hero_background_image'                => '',
				'sph_background_size'                      => 'auto',
				'sph_hero_background_video'                => '',
				'sph_hero_background_video_image_fallback' => '',
				'sph_background_color'                     => '#2c2d33',
				'sph_hero_parallax'                        => false,
				'sph_parallax_scroll_ratio'                => '0.5',
				'sph_parallax_offset'                      => 0,
				'sph_overlay_color'                        => '#000000',
				'sph_overlay_opacity'                      => '0.5',
				'sph_alignment'                            => 'center',
				'sph_layout'                               => 'full',
				'sph_hero_full_height'                     => false,
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

			wp_enqueue_script( 'sp-parallax-hero-customizer', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/js/customizer' . $suffix . '.js', array( 'customize-preview' ), storefront_powerpack()->version, true );
		}

		/**
		 * Enqueue styles and scripts.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function scripts() {
			wp_enqueue_style( 'sp-parallax-hero-customizer-css', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/css/customizer.css', array(), storefront_powerpack()->version, 'all' );
		}

		/**
		 * Inline customizer scripts.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function control_scripts() {
			?>
			<script>
				jQuery(document).ready(function($) {
					var $mediaButtonset            = $( '[id="input_sph_hero_background_media"]' );
						$bgImage                   = $( '[id="customize-control-sph_hero_background_image"]' ),
						$bgImageSize               = $( '[id="customize-control-sph_background_size"]' ),
						$bgImageVideo              = $( '[id="customize-control-sph_hero_background_video"]' );
						$bgImageVideoImageFallback = $( '[id="customize-control-sph_hero_background_video_image_fallback"]' );

					var SPHShowHide = function() {
						var value = $mediaButtonset.find( 'input:checked' ).val();

						switch ( value ) {
							case 'none':
								$bgImage.hide();
								$bgImageSize.hide();
								$bgImageVideo.hide();
								$bgImageVideoImageFallback.hide();
								break;
							case 'image':
								$bgImageVideo.hide();
								$bgImage.show();
								$bgImageSize.show();
								$bgImageVideoImageFallback.hide();
								break;
							case 'video':
								$bgImage.hide();
								$bgImageSize.hide();
								$bgImageVideo.show();
								$bgImageVideoImageFallback.show();
								break;
							default:
								$bgImage.hide();
								$bgImageSize.hide();
								$bgImageVideo.hide();
								$bgImageVideoImageFallback.hide();
						}
					};

					$mediaButtonset.buttonset();
					SPHShowHide();

					$mediaButtonset.find( 'input' ).on( 'click', function() {
						SPHShowHide();
					});
				});
			</script>
			<?php
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 2.0.0
		 * @return void
		 */
		public function customize_register( $wp_customize ) {
			$wp_customize->add_section( self::POWERPACK_PARALLAX_HERO_SECTION, array(
				'title'              => __( 'Parallax Hero', 'storefront-powerpack' ),
				'description'        => __( 'Customise the appearance and content of the hero component that is displayed on your homepage.', 'storefront-powerpack' ),
				'description_hidden' => true,
				'panel'              => self::POWERPACK_PANEL,
				'priority'           => 110,
			) );

	        /**
		     * On/off
		     */
			$wp_customize->add_setting( 'sph_hero_enable', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sph_hero_enable', array(
				'label'    => __( 'Display on homepage template', 'storefront-powerpack' ),
				'description' => __( 'This option affects only the output of Parallax Hero on the Homepage template. Shortcodes can still be used.', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_enable',
				'type'     => 'select',
				'priority' => 5,
				'choices'  => array(
					'disable' => __( 'Disable', 'storefront-powerpack' ),
					'enable'  => __( 'Enable', 'storefront-powerpack' )
				),
			) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_enable_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 7,
				) ) );
		    }

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'sp_parallax_hero_title_content', array(
					'label'    => __( 'Content', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'priority' => 10
				) ) );
			}

			/**
			 * Heading Text
			 */
		    $wp_customize->add_setting( 'sph_hero_heading_text', array(
				'sanitize_callback' => 'sanitize_text_field',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_heading_text', array(
				'label'    => __( 'Heading text', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_heading_text',
				'type'     => 'text',
				'priority' => 20,
	        ) ) );

	        /**
		     * Heading text color
		     */
		    $wp_customize->add_setting( 'sph_heading_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_heading_color', array(
				'label'    => 'Heading text color',
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_heading_color',
				'priority' => 30,
		    ) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_heading_text_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 35,
				) ) );
		    }

	        /**
			 * Text
			 */
		    $wp_customize->add_setting( 'sph_hero_text', array(
				'sanitize_callback' => 'wp_kses_post'
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_text', array(
				'label'    => __( 'Description text', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_text',
				'type'     => 'textarea',
				'priority' => 40,
	        ) ) );

	        /**
		     * Text color
		     */
		    $wp_customize->add_setting( 'sph_hero_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_hero_text_color', array(
				'label'    => 'Description text color',
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_text_color',
				'priority' => 50,
		    ) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_text_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 55,
				) ) );
		    }

		    /**
		     * Link color
		     */
		    $wp_customize->add_setting( 'sph_hero_link_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_hero_link_color', array(
				'label'    => 'Link color',
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_link_color',
				'priority' => 60,
		    ) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_link_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 65,
				) ) );
		    }

		    /**
			 * Button Text
			 */
		    $wp_customize->add_setting( 'sph_hero_button_text', array(
				'sanitize_callback' => 'sanitize_text_field',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_button_text', array(
				'label'    => __( 'Button text', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_button_text',
				'type'     => 'text',
				'priority' => 70,
	        ) ) );

	        /**
			 * Button Text
			 */
		    $wp_customize->add_setting( 'sph_hero_button_url', array(
				'sanitize_callback' => 'sanitize_text_field',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_button_url', array(
				'label'    => __( 'Button url', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_button_url',
				'type'     => 'text',
				'priority' => 80,
	        ) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_button_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 85,
				) ) );
		    }

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
				$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'sp_parallax_hero_title_background', array(
					'label'    => __( 'Background', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'priority' => 90
				) ) );
			}

	        /**
		     * Media buttonset
		     */
			$wp_customize->add_setting( 'sph_hero_background_media', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sph_hero_background_media', array(
				'label'    => __( 'Background media', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_hero_background_media',
				'type'     => 'select',
				'priority' => 100,
				'choices'  => array(
					'none'  => __( 'None', 'storefront-powerpack' ),
					'image' => __( 'Image', 'storefront-powerpack' ),
					'video' => __( 'Video', 'storefront-powerpack' )
				),
			) ) );

			/**
			 * Background image
			 */
			$wp_customize->add_setting( 'sph_hero_background_image', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'sph_hero_background_image', array(
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'label'       => __( 'Background image', 'storefront-powerpack' ),
				'description' => __( 'Upload a video to be displayed as the hero background', 'storefront-powerpack' ),
				'settings'    => 'sph_hero_background_image',
				'flex_width'  => false,
				'flex_height' => false,
				'width'       => 1920,
				'height'      => 2560,
				'priority'    => 110,
			) ) );

			/**
			 * Background size
			 */
			$wp_customize->add_setting( 'sph_background_size', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( 'sph_background_size', array(
					'label'       => __( 'Background image size', 'storefront-powerpack' ),
					'description' => __( 'When using a background image, specify which background size method to apply', 'storefront-powerpack' ),
					'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
					'settings'    => 'sph_background_size',
					'type'        => 'select',
					'priority'    => 120,
					'choices'     => array(
						'auto'  => 'Default',
						'cover' => 'Cover',
					),
				)
			);

	        /**
		     * Background Video
		     */
			$wp_customize->add_setting( 'sph_hero_background_video', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

		    $wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'sph_hero_background_video', array(
				'label'       => __( 'Background video', 'storefront-powerpack' ),
				'description' => __( 'Upload a video to be displayed as the hero background', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings'    => 'sph_hero_background_video',
				'priority'    => 130,
		    ) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_media_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 135,
				) ) );
		    }

			/**
			 * Background video image fallback
			 */
			$wp_customize->add_setting( 'sph_hero_background_video_image_fallback', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'sph_hero_background_video_image_fallback', array(
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'label'       => __( 'Background image video fallback', 'storefront-powerpack' ),
				'description' => __( 'Autoplay of videos is not support by some mobile browsers. Use this option to display an image instead.', 'storefront-powerpack' ),
				'settings'    => 'sph_hero_background_video_image_fallback',
				'flex_width'  => false,
				'flex_height' => false,
				'width'       => 1920,
				'height'      => 2560,
				'priority'    => 140,
			) ) );

			/**
			 * Background Color
			 */
			$wp_customize->add_setting( 'sph_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_background_color', array(
				'label'       => __( 'Background color', 'storefront-powerpack' ),
				'description' => __( 'Set the background color for the hero component (the background might not always be visible)' ),
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings'    => 'sph_background_color',
				'priority'    => 150,
			) ) );

		    if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 155,
				) ) );
		    }

		    /**
		     * Parallax
		     */
		    $wp_customize->add_setting( 'sph_hero_parallax', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_parallax', array(
				'label'       => __( 'Parallax', 'storefront-powerpack' ),
				'description' => __( 'Enable the parallax scrolling effect', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings'    => 'sph_hero_parallax',
				'type'        => 'checkbox',
				'priority'    => 160,
	        ) ) );

	        /**
		     * Parallax scroll speed
		     */
	        $wp_customize->add_setting( 'sph_parallax_scroll_ratio', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
	        ) );

	        $wp_customize->add_control( 'sph_parallax_scroll_ratio', array(
					'label'       => __( 'Parallax scroll speed', 'storefront-powerpack' ),
					'description' => __( 'The speed at which the parallax background scrolls relative to the window', 'storefront-powerpack' ),
					'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
					'settings'    => 'sph_parallax_scroll_ratio',
					'type'        => 'select',
					'priority'    => 170,
					'choices'     => array(
						'0.25' => '25%',
						'0.5'  => '50%',
						'0.75' => '75%',
					),
				)
			);

	        /**
	         * Parallax Offset
	         */
	        $wp_customize->add_setting( 'sph_parallax_offset', array(
				'sanitize_callback' => 'esc_attr',
	        ) );
			$wp_customize->add_control( 'sph_parallax_offset', array(
				'type'        => 'range',
				'priority'    => 180,
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'label'       => __( 'Parallax offset', 'storefront-powerpack' ),
				'description' => __( 'Offset the starting position of your background image', 'storefront-powerpack' ),
				'input_attrs' => array(
					'min'  => -500,
					'max'  => 500,
					'step' => 1,
			    ),
			) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_offset_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 185,
				) ) );
		    }

		    /**
		     * Overlay color
		     */
			$wp_customize->add_setting( 'sph_overlay_color', array(
				'description'       => __( 'Specify the overlay background color', 'storefront-powerpack' ),
				'sanitize_callback' => 'sanitize_hex_color',
		    ) );

		    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sph_overlay_color', array(
				'label'    => 'Overlay color',
				'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings' => 'sph_overlay_color',
				'priority' => 190,
		    ) ) );

		    /**
		     * Overlay opacity
		     */
	        $wp_customize->add_setting( 'sph_overlay_opacity', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
	        ) );

	        $wp_customize->add_control( 'sph_overlay_opacity', array(
					'label'    => __( 'Overlay opacity', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'settings' => 'sph_overlay_opacity',
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
				)
			);

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
		        $wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_parallax_hero_overlay_divider', array(
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'type'     => 'divider',
					'priority' => 205,
				) ) );
		    }

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
				$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'sp_parallax_hero_title_layout', array(
					'label'    => __( 'Layout', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'priority' => 210
				) ) );
			}

	        /**
		     * Alignment
		     */
		    $wp_customize->add_setting( 'sph_alignment', array(
		    	'sanitize_callback' => 'storefront_sanitize_choices',
		    ) );

	        $wp_customize->add_control( 'sph_alignment', array(
					'label'    => __( 'Text alignment', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'settings' => 'sph_alignment',
					'type'     => 'select',
					'priority' => 220,
					'choices'  => array(
						'left'   => 'Left',
						'center' => 'Center',
						'right'  => 'Right',
					),
				)
			);

			/**
		     * Layout
		     */
		    $wp_customize->add_setting( 'sph_layout', array(
		    	'sanitize_callback' => 'storefront_sanitize_choices',
		    ) );

	        $wp_customize->add_control( 'sph_layout', array(
					'label'    => __( 'Hero layout', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_PARALLAX_HERO_SECTION,
					'settings' => 'sph_layout',
					'type'     => 'select',
					'priority' => 230,
					'choices'  => array(
						'full'  => 'Full width',
						'fixed' => 'Fixed width',
					),
				)
			);

			/**
		     * Full height
		     */
		    $wp_customize->add_setting( 'sph_hero_full_height', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sph_hero_full_height', array(
				'label'       => __( 'Full height', 'storefront-powerpack' ),
				'description' => __( 'Set the hero component to full height. Works best when the Hero is the first element in your homepage content area.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_PARALLAX_HERO_SECTION,
				'settings'    => 'sph_hero_full_height',
				'type'        => 'checkbox',
				'priority'    => 240,
	        ) ) );
		}
	}

endif;

return new SP_Customizer_Parallax_Hero();