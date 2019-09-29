<?php
/**
 * Storefront Powerpack Frontend Parallax Hero Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Parallax_Hero' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Parallax_Hero extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'setup' ) );
		}

		/**
		 * WordPress hooks.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function setup() {
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
			add_action( 'homepage', array( $this, 'homepage_parallax_hero' ), 10 );
			add_filter( 'body_class', array( $this, 'body_classes' ) );

			// Create a shortcode to display the hero
			add_shortcode( 'parallax_hero', array( $this, 'display_parallax_hero_shortcode' ) );
		}

		/**
		 * Register Assets for later use.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function scripts() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_script( 'sp-parallax-hero-stellar-init', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/js/stellar-init' . $suffix . '.js', array( 'jquery' ), storefront_powerpack()->version );
			wp_register_script( 'sp-parallax-hero-full-height', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/js/full-height' . $suffix . '.js', array( 'jquery' ), storefront_powerpack()->version );
			wp_register_script( 'sp-parallax-hero-script', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/js/general' . $suffix . '.js', array( 'jquery' ), storefront_powerpack()->version );
			wp_register_script( 'stellar', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/js/vendor/jquery.stellar.min.js', array( 'jquery' ), '0.6.2' );
		}

		/**
		 * Enqueue CSS.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function styles() {
			global $post, $storefront_version;

			wp_enqueue_style( 'sp-parallax-hero-styles', SP_PLUGIN_URL . 'includes/customizer/parallax-hero/assets/css/style.css', '', storefront_powerpack()->version );

			$link_color        = get_theme_mod( 'sph_hero_link_color' );
			$sph_heading_color = get_theme_mod( 'sph_heading_color' );
			$accent_color      = get_theme_mod( 'storefront_accent_color' );

			$sph_style = '
			.sph-hero a:not(.button) {
				color: ' . $link_color . ';
			}

			.overlay.animated h1:after {
				color: ' . $sph_heading_color . ';
			}

			.overlay.animated span:before {
				background-color: ' . $accent_color . ';
			}';

			if ( version_compare( $storefront_version, '2.2.0', '<' ) ) {
				$sph_style .= '
				.page-template-template-homepage .site-main .sph-hero:first-child {
					margin-top: -4.236em;
				}
				';
			}

			// Custom CSS for shortcodes
			if ( $post && true === has_shortcode( $post->post_content, 'parallax_hero' ) ) {
				preg_match_all( '/' . get_shortcode_regex() . '/sx', $post->post_content, $shortcode_matches, PREG_SET_ORDER );

				foreach ( $shortcode_matches as $shortcode ) {
					if ( 'parallax_hero' !== $shortcode[2] ) {
						continue;
					}

					if ( empty( $shortcode[3] ) ) {
						continue;
					}

					$atts = shortcode_parse_atts( $shortcode[3] );

					if ( ! isset( $atts['heading_text_color'] ) ) {
						continue;
					}

					$hash = md5( json_encode( $atts ) );

					$sph_style .= '
					#sph-' . $hash . ' .overlay.animated h1:after {
						color: ' . $atts['heading_text_color'] . ';
					}';
				}
			}

			wp_add_inline_style( 'sp-parallax-hero-styles', $sph_style );
		}

		/**
		 * Add custom body classes.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function body_classes( $classes ) {
			if ( apply_filters( 'sph_do_mobile', false ) ) {
				$classes[] = 'sph-do-mobile';
			}

			$background_video_image_fallback = sanitize_text_field( get_theme_mod( 'sph_hero_background_video_image_fallback' ) );

			if ( '' !== $background_video_image_fallback ) {
				$classes[] = 'sph-video-image-fallback';
			}

			return $classes;
		}

		/**
		 * Display the hero section
		 *
		 * @see get_theme_mod()
		 * @since 2.0.0
		 * @return void
		 */
		public static function display_parallax_hero( $atts ) {
			$atts = extract( shortcode_atts( array(
				'heading_text'                    => sanitize_text_field( get_theme_mod( 'sph_hero_heading_text' ) ),
				'heading_text_color'              => get_theme_mod( 'sph_heading_color' ),
				'description_text'                => wp_kses_post( get_theme_mod( 'sph_hero_text' ) ),
				'description_text_color'          => get_theme_mod( 'sph_hero_text_color' ),
				'background_media'                => sanitize_text_field( get_theme_mod( 'sph_hero_background_media' ) ),
				'background_image'                => sanitize_text_field( get_theme_mod( 'sph_hero_background_image' ) ),
				'background_video'                => sanitize_text_field( get_theme_mod( 'sph_hero_background_video' ) ),
				'background_video_image_fallback' => sanitize_text_field( get_theme_mod( 'sph_hero_background_video_image_fallback' ) ),
				'background_color'                => sanitize_text_field( get_theme_mod( 'sph_background_color' ) ),
				'background_size'                 => get_theme_mod( 'sph_background_size' ),
				'button_text'                     => sanitize_text_field( get_theme_mod( 'sph_hero_button_text' ) ),
				'button_url'                      => sanitize_text_field( get_theme_mod( 'sph_hero_button_url' ) ),
				'alignment'                       => get_theme_mod( 'sph_alignment' ),
				'layout'                          => 'fixed',
				'parallax'                        => get_theme_mod( 'sph_hero_parallax' ),
				'parallax_scroll'                 => get_theme_mod( 'sph_parallax_scroll_ratio' ),
				'parallax_offset'                 => get_theme_mod( 'sph_parallax_offset' ),
				'overlay_color'                   => get_theme_mod( 'sph_overlay_color' ),
				'overlay_opacity'                 => get_theme_mod( 'sph_overlay_opacity' ),
				'full_height'                     => get_theme_mod( 'sph_hero_full_height' ),
				'style'                           => '',
				'overlay_style'                   => '',
				'background_img'                  => false,
				'shortcode_uid'                   => false,
			), $atts, 'parallax_hero' ) );

			// Get RGB color of overlay from HEX
			list( $r, $g, $b ) = sscanf( $overlay_color, "#%02x%02x%02x" );

			// Determine the file type of the background item
			$is_image = false;
			$is_video = false;

			// Image or video?
			if ( ! $background_img ) { // Support for shortcode
				if ( $background_media && 'none' !== $background_media ) {
					if ( 'image' === $background_media ) {
						if ( isset( $background_image ) && '' !== $background_image  ) {
							$background_img = wp_get_attachment_url( absint( $background_image ) );
						}
					}

					if ( 'video' === $background_media ) {
						$background_img = $background_video;
					}
				} elseif ( '' !== get_theme_mod( 'sph_hero_background_img' ) ) { // < 1.5.0
					$background_img = get_theme_mod( 'sph_hero_background_img' );
				}
			}

			if ( $background_img ) {
				$filetype = wp_check_filetype( $background_img );

				// Is it a video or an image?
				if ( $filetype['ext'] === 'jpg' || $filetype['ext'] === 'jpeg' || $filetype['ext'] === 'gif' || $filetype['ext'] === 'png' || $filetype['ext'] === 'bmp' || $filetype['ext'] === 'tif' || $filetype['ext'] === 'tiff' || 'ico' ) {
					$is_image = true;
					$is_video = false;
				}

				if ( $filetype['ext'] === 'mp4' || $filetype['ext'] === 'm4v' || $filetype['ext'] === 'mov' || $filetype['ext'] === 'wmv' || $filetype['ext'] === 'avi' || $filetype['ext'] === 'mpg' || $filetype['ext'] === 'ogv' || $filetype['ext'] === '3gp' || $filetype['ext'] === '3g2' ) {
					$is_video = true;
					$is_image = false;
				}
			}

			// Include the parallax script if required and set the scroll ratio variable
			$stellar = '';

			if ( true === $parallax ) {
				wp_enqueue_script( 'sp-parallax-hero-stellar-init' );
				wp_enqueue_script( 'stellar' );

				$stellar = 'data-stellar-background-ratio="' . $parallax_scroll . '"';
			}

			$full_height_class = '';

			if ( true === $full_height ) {
				$full_height_class = 'sph-full-height';
				wp_enqueue_script( 'sp-parallax-hero-full-height' );
			}

			// If shortcode, append id for custom CSS
			$section_id = '';
			if ( false !== $shortcode_uid ) {
				$section_id = 'id="sph-' . $shortcode_uid . '"';
			}

			/**
			 * If the background item is an image the parallax attributes need to be applied to the main wrapper
			 */
			if ( true === $is_image ) { ?>
				<section <?php echo $section_id; ?> data-stellar-vertical-offset="<?php echo intval( $parallax_offset ); ?>" <?php echo $stellar; ?> class="sph-hero <?php echo esc_attr( $alignment ) . ' ' . esc_attr( $layout ) . ' ' . $full_height_class; ?>" style="<?php echo esc_attr( $style ); ?>background-image: url(<?php echo esc_url( $background_img ); ?>); background-color: <?php echo esc_attr( $background_color ); ?>; color: <?php echo esc_attr( $description_text_color ); ?>; background-size: <?php echo esc_attr( $background_size ); ?>;">
			<?php } else { ?>
				<section <?php echo $section_id; ?> class="sph-hero <?php echo esc_attr( $alignment ) . ' ' . esc_attr( $layout ) . ' ' . $full_height_class; ?>" style="background-color: <?php echo esc_attr( $background_color ); ?>; color: <?php echo esc_attr( $description_text_color ); ?>;">
			<?php } ?>

				<?php
				/**
				 * If the background item is a video, let's load the html5 video player
				 */
				if ( true === $is_video ) { ?>
				<div class="video-wrapper" data-stellar-vertical-offset="<?php echo intval( $parallax_offset ); ?>" data-stellar-ratio="<?php echo esc_attr( $parallax_scroll ); ?>">
					<video src="<?php echo esc_url( $background_img ); ?>" <?php echo apply_filters( 'storefront_parallax_hero_video_attributes', $atts = 'autoplay loop preload muted' ); ?> class="sph-video" height="auto" width="auto"></video>

					<?php
						$fallback_image = false;
						if ( '' !== $background_video_image_fallback ) {
							$fallback_image = wp_get_attachment_url( absint( $background_video_image_fallback ) );
						}
					?>

					<?php if ( $fallback_image ) { ?>
					<div class="sph-video-image-fallback" style="background-image: url(<?php echo esc_url( $fallback_image ); ?>);"></div>
					<?php } ?>
				</div>
				<?php } ?>

				<div class="overlay animated" style="background-color: rgba(<?php echo $r . ', ' . $g . ', ' . $b . ', ' . $overlay_opacity; ?>);<?php echo $overlay_style; ?>">

					<div class="sph-inner-wrapper">

						<div class="col-full sph-inner">

							<?php do_action( 'sph_content_before' ); ?>

							<h1 style="color: <?php echo $heading_text_color; ?>;" data-content="<?php echo esc_attr( $heading_text ); ?>"><span><?php echo esc_attr( $heading_text ); ?></span></h1>

							<div class="sph-hero-content-wrapper">
								<div class="sph-hero-content">
									<?php echo wpautop( $description_text ); ?>

									<?php if ( $button_text && $button_url ) { ?>
										<p>
											<a href="<?php echo $button_url; ?>" class="button"><?php echo $button_text; ?></a>
										</p>
									<?php } ?>
								</div>
							</div>

							<?php do_action( 'sph_content_after' ); ?>
						</div>

					</div>

				</div>
			</section>
			<?php

			// Load the general sph scripts
			wp_enqueue_script( 'sp-parallax-hero-script' );
		}

		/**
		 * Display the hero section via shortcode
		 *
		 * @see display_parallax_hero()
		 * @since 2.0.0
		 * @return string
		 */
		public static function display_parallax_hero_shortcode( $atts ) {
			$atts = ( is_array( $atts ) ? $atts : array() );
			//$hero = new SP_Frontend_Parallax_Hero();

			// Generate unique id for this shortcode
			$hash                  = md5( json_encode( $atts ) );
			$atts['shortcode_uid'] = $hash;

			ob_start();
			$this->display_parallax_hero( $atts );
			return ob_get_clean();
		}

		/**
		 * Display the hero section via homepage action
		 *
		 * @see display_parallax_hero()
		 * @since 2.0.0
		 * @return void
		 */
		public static function homepage_parallax_hero( $atts ) {
			if ( 'enable' !== get_theme_mod( 'sph_hero_enable' ) ) {
				return;
			}

			// Default just for homepage customizer one needs to be full, so set that there
			$atts = array( 'layout' => get_theme_mod( 'sph_layout' ) );

			SP_Frontend_Parallax_Hero::display_parallax_hero( $atts );
		}
	}

endif;

return new SP_Frontend_Parallax_Hero();