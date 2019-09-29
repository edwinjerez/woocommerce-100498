<?php
/**
 * Storefront Powerpack Customizer Blog Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Blog' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Blog extends SP_Customizer {
		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_BLOG_SECTION = 'sp_blog_section';

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
				'sbc_post_layout_archive'   => 'default',
				'sbc_blog_archive_layout'   => false,
				'sbc_magazine_layout'       => false,
				'sbc_post_layout_single'    => 'default',
				'sbc_blog_single_layout'    => false,
				'sbc_homepage_blog_toggle'  => false,
				'sbc_homepage_blog_title'   => __( 'Recent Blog Posts', 'storefront-powerpack' ),
				'sbc_post_layout_homepage'  => 'default',
				'sbc_homepage_blog_columns' => '2',
				'sbc_homepage_blog_limit'   => '2'
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

			wp_enqueue_script( 'sp-blog-customizer', SP_PLUGIN_URL . 'includes/customizer/blog/assets/js/customizer' . $suffix . '.js', array( 'customize-preview' ), storefront_powerpack()->version, true );
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 2.0.0
		 * @return void
		 */
		public function customize_register( $wp_customize ) {
			$wp_customize->add_section( self::POWERPACK_BLOG_SECTION, array(
				'title'              => __( 'Blog', 'storefront-powerpack' ),
				'description'        => __( 'Customize the appearance of your blog posts and archives.', 'storefront-powerpack' ),
				'description_hidden' => true,
				'panel'              => self::POWERPACK_PANEL,
				'priority'           => 140,
			) );

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'storefront_blog_archive', array(
						'label'    => __( 'Archives', 'storefront-powerpack' ),
						'section'  => self::POWERPACK_BLOG_SECTION,
						'priority' => 10
				) ) );
			}

			/**
			 * Post layout
			 */
			$wp_customize->add_setting( 'sbc_post_layout_archive', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_post_layout_archive', array(
				'label'    => __( 'Post meta display', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_BLOG_SECTION,
				'settings' => 'sbc_post_layout_archive',
				'type'     => 'select',
				'priority' => 20,
				'choices'  => array(
					'default'            => __( 'Left of content', 'storefront-powerpack' ),
					'meta-right'         => __( 'Right of content', 'storefront-powerpack' ),
					'meta-inline-top'    => __( 'Above content', 'storefront-powerpack' ),
					'meta-inline-bottom' => __( 'Beneath content', 'storefront-powerpack' ),
					'meta-hidden'        => __( 'Hidden', 'storefront-powerpack' ),
				),
			) ) );

			/**
			 * Blog archive layout
			 */
			$wp_customize->add_setting( 'sbc_blog_archive_layout', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_blog_archive_layout', array(
				'label'       => __( 'Full width', 'storefront-powerpack' ),
				'description' => __( 'Display blog archives in a full width layout.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_BLOG_SECTION,
				'settings'    => 'sbc_blog_archive_layout',
				'type'        => 'checkbox',
				'priority'    => 30,
			) ) );

			/**
			 * Magazine layout
			 */
			$wp_customize->add_setting( 'sbc_magazine_layout', array(
				'sanitize_callback'	=> 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_magazine_layout', array(
				'label'       => __( 'Magazine layout', 'storefront-powerpack' ),
				'description' => __( 'Apply a "magazine" layout to blog archives.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_BLOG_SECTION,
				'settings'    => 'sbc_magazine_layout',
				'type'        => 'checkbox',
				'priority'    => 40,
			) ) );

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'storefront_blog_single', array(
					'label'    => __( 'Single posts', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_BLOG_SECTION,
					'priority' => 50
				) ) );
			}

			/**
			 * Single post layout
			 */
			$wp_customize->add_setting( 'sbc_post_layout_single', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_post_layout_single', array(
				'label'    => __( 'Post meta display', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_BLOG_SECTION,
				'settings' => 'sbc_post_layout_single',
				'type'     => 'select',
				'priority' => 60,
				'choices'  => array(
					'default'            => __( 'Left of content', 'storefront-powerpack' ),
					'meta-right'         => __( 'Right of content', 'storefront-powerpack' ),
					'meta-inline-top'    => __( 'Above content', 'storefront-powerpack' ),
					'meta-inline-bottom' => __( 'Beneath content', 'storefront-powerpack' ),
					'meta-hidden'        => __( 'Hidden', 'storefront-powerpack' ),
				),
			) ) );

			/**
			 * Blog single full width
			 */
			$wp_customize->add_setting( 'sbc_blog_single_layout', array(
				'sanitize_callback'	=> 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_blog_single_layout', array(
				'label'       => __( 'Full width', 'storefront-powerpack' ),
				'description' => __( 'Give the single blog post pages a full width layout.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_BLOG_SECTION,
				'settings'    => 'sbc_blog_single_layout',
				'type'        => 'checkbox',
				'priority'    => 70,
			) ) );

			if ( class_exists( 'SP_Section_Title_Separator_Control' ) ) {
					$wp_customize->add_control( new SP_Section_Title_Separator_Control( $wp_customize, 'storefront_blog_homepage', array(
					'label'    => __( 'Homepage', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_BLOG_SECTION,
					'priority' => 80
				) ) );
			}

			/**
			 * Homepage Blog Toggle
			 */
			$wp_customize->add_setting( 'sbc_homepage_blog_toggle', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_homepage_blog_toggle', array(
				'label'       => __( 'Display blog posts', 'storefront-powerpack' ),
				'description' => __( 'Toggle the display of blog posts on the homepage.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_BLOG_SECTION,
				'settings'    => 'sbc_homepage_blog_toggle',
				'type'        => 'checkbox',
				'priority'    => 90,
				)
			) );

			/**
			 * Homepage Blog Title
			 */
			$wp_customize->add_setting( 'sbc_homepage_blog_title', array(
				'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_homepage_blog_title', array(
				'label'    => __( 'Blog post title', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_BLOG_SECTION,
				'settings' => 'sbc_homepage_blog_title',
				'type'     => 'text',
				'priority' => 100,
				)
			) );

			/**
			 * Homepage post layout
			 */
			$wp_customize->add_setting( 'sbc_post_layout_homepage', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_post_layout_homepage', array(
				'label'    => __( 'Post meta display', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_BLOG_SECTION,
				'settings' => 'sbc_post_layout_homepage',
				'type'     => 'select',
				'priority' => 110,
				'choices'  => array(
					'default'            => __( 'Left of content', 'storefront-powerpack' ),
					'meta-right'         => __( 'Right of content', 'storefront-powerpack' ),
					'meta-inline-top'    => __( 'Above content', 'storefront-powerpack' ),
					'meta-inline-bottom' => __( 'Beneath content', 'storefront-powerpack' ),
					'meta-hidden'        => __( 'Hidden', 'storefront-powerpack' ),
				),
			) ) );

			/**
			 * Homepage Blog Columns
			 */
			$wp_customize->add_setting( 'sbc_homepage_blog_columns', array(
				 'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_homepage_blog_columns', array(
				'label'    => __( 'Blog post columns', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_BLOG_SECTION,
				'settings' => 'sbc_homepage_blog_columns',
				'type'     => 'select',
				'priority' => 120,
				'choices'  => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			) ) );

			/**
			 * Homepage Blog limit
			 */
			$wp_customize->add_setting( 'sbc_homepage_blog_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sbc_homepage_blog_limit', array(
					'label'    => __( 'Number of posts to display', 'storefront-powerpack' ),
					'section'  => self::POWERPACK_BLOG_SECTION,
					'settings' => 'sbc_homepage_blog_limit',
					'type'     => 'select',
					'priority' => 130,
					'choices'  => array(
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
						'11' => '11',
						'12' => '12',
					),
			) ) );
		}
	}

endif;

return new SP_Customizer_Blog();