<?php
/**
 * Storefront Powerpack Frontend Product Hero Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Product_Hero' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Product_Hero extends SP_Frontend {

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
			add_filter( 'body_class', array( $this, 'body_classes' ) );
			add_action( 'homepage', array( $this, 'homepage' ), 5 );

			// Create a shortcode to display the hero
			add_shortcode( 'product_hero', array( $this, 'display_product_hero_shortcode' ) );
		}

		/**
		 * Register Assets for later use.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function scripts() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_script( 'sp-product-hero-script', SP_PLUGIN_URL . 'includes/customizer/product-hero/assets/js/general' . $suffix . '.js', array( 'jquery' ), storefront_powerpack()->version );
			wp_register_script( 'sp-product-hero-full-height', SP_PLUGIN_URL . 'includes/customizer/product-hero/assets/js/full-height' . $suffix . '.js', array( 'jquery' ), storefront_powerpack()->version );
			wp_register_script( 'stellar', SP_PLUGIN_URL . 'includes/customizer/product-hero/assets/js/jquery.stellar.min.js', array( 'jquery' ), '0.6.2' );
		}

		/**
		 * Enqueue CSS.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function styles() {
			wp_enqueue_style( 'sp-product-hero-styles', SP_PLUGIN_URL . 'includes/customizer/product-hero/assets/css/style.css' );

			$link_color = get_theme_mod( 'sprh_hero_link_color' );

			$sph_style = '
			.sprh-hero a:not(.button) {
				color: ' . $link_color . ';
			}';

			wp_add_inline_style( 'sp-product-hero-styles', $sph_style );
		}

		/**
		 * Storefront Product Hero Body Class
		 * Adds a class based on the extension name and any relevant settings.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function body_classes( $classes ) {
			$classes[] = 'storefront-product-hero-active';

			return $classes;
		}

		/**
		 * Display the hero section
		 * @see get_theme_mod()
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public static function display_product_hero( $atts ) {
			$atts = extract( shortcode_atts( array(
				'heading_text' 				=> sanitize_text_field( get_theme_mod( 'sprh_hero_heading_text' ) ),
				'heading_text_color' 		=> get_theme_mod( 'sprh_heading_color' ),
				'description_text' 			=> get_theme_mod( 'sprh_hero_text' ),
				'description_text_color' 	=> get_theme_mod( 'sprh_hero_text_color' ),
				'background_img' 			=> sanitize_text_field( get_theme_mod( 'sprh_hero_background_img' ) ),
				'background_color'			=> sanitize_text_field( get_theme_mod( 'sprh_background_color' ) ),
				'background_size' 			=> get_theme_mod( 'sprh_background_size' ),
				'layout' 					=> get_theme_mod( 'sprh_alignment'  ),
				'width' 					=> 'fixed',
				'parallax' 					=> get_theme_mod( 'sprh_hero_parallax' ),
				'parallax_scroll' 			=> get_theme_mod( 'sprh_parallax_scroll_ratio' ),
				'parallax_offset' 			=> get_theme_mod( 'sprh_parallax_offset'  ),
				'overlay_color' 			=> get_theme_mod( 'sprh_overlay_color' ),
				'overlay_opacity' 			=> get_theme_mod( 'sprh_overlay_opacity' ),
				'full_height' 				=> get_theme_mod( 'sprh_hero_full_height' ),
				'style'						=> '',
				'overlay_style'				=> '',
				'product_id'				=> get_theme_mod( 'sprh_featured_product' ),
				'product_image'				=> get_theme_mod( 'sprh_product_image' ),
				'product_price'				=> get_theme_mod( 'sprh_product_price' ),
				'product_rating'			=> get_theme_mod( 'sprh_product_rating' ),
			), $atts, 'product_hero' ) );

			// Get RGB color of overlay from HEX
			list( $r, $g, $b ) = sscanf( $overlay_color, "#%02x%02x%02x" );

			$stellar = '';

			// Get product
			$product_data = wc_get_product( $product_id );

			if ( ! $product_data ) {
				return;
			}

			if ( true == $parallax ) {
				wp_enqueue_script( 'sp-product-hero-script' );
				wp_enqueue_script( 'stellar' );

				$stellar = 'data-stellar-background-ratio="' . $parallax_scroll . '"';
			}

			$full_height_class = '';

			if ( true == $full_height ) {
				$full_height_class = 'sprh-full-height';
				wp_enqueue_script( 'sp-product-hero-full-height' );
			}

			// Display the product hero only when a product has been set.
			?>
			<section data-stellar-vertical-offset="<?php echo intval( $parallax_offset ); ?>" <?php echo $stellar; ?> class="sprh-hero <?php echo 'sprh-layout-' . $layout . ' ' . $width . ' ' . $full_height_class; ?>" style="<?php echo $style; ?>background-image: url(<?php echo $background_img; ?>); background-color: <?php echo $background_color; ?>; color: <?php echo $description_text_color; ?>; background-size: <?php echo $background_size; ?>;">
				<div class="overlay" style="background-color: rgba(<?php echo $r . ', ' . $g . ', ' . $b . ', ' . $overlay_opacity; ?>);<?php echo $overlay_style; ?>">
					<div class="col-full">

						<?php do_action( 'sprh_content_before' ); ?>

						<div class="sprh-featured-image">
							<?php
								if ( true == $product_image ) {
									echo '<a href="' . get_permalink( $product_id ) . '">' . get_the_post_thumbnail( $product_id, 'shop_single' ) . '</a>';
								}
							?>
						</div>

						<div class="sprh-hero-content-wrapper">

							<h3 style="color: <?php echo $heading_text_color; ?>;">
								<?php
									if ( '' != $heading_text ) {
										echo $heading_text;
									} else {
										echo $product_data->get_title();
									}
								?>
							</h3>

							<div class="sprh-hero-content">
								<?php
									if ( true == $product_rating ) {
										if ( version_compare( WC_VERSION, '2.7.0', '<' ) ) {
											echo $product_data->get_rating_html();
										} else {
											echo wc_get_rating_html( $product_data->get_average_rating() );
										}
									}

									if ( '' != $description_text ) {
										echo wpautop( wp_kses_post( $description_text ) );
									} else {
										echo wpautop( get_post_field( 'post_content', $product_id ) );
									}
								?>

								<?php
									if ( true == $product_price ) {
										echo do_shortcode( '[add_to_cart id="' . $product_id . '"]' );
									}
								?>

								<p class="more-details">
									<a href="<?php echo get_permalink( $product_id ); ?>" class="button alt"><?php _e( 'More details &rarr;', 'storefront-product-hero' ); ?></a>
								</p>
							</div>

						</div>

						<?php do_action( 'sprh_content_after' ); ?>
					</div>
				</div>
			</section>
			<?php
		}

		/**
		 * Display the hero section via shortcode
		 * @see display_product_hero()
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public static function display_product_hero_shortcode( $atts ) {
			ob_start();
			SP_Frontend_Product_Hero::display_product_hero( $atts );
			return ob_get_clean();
		}

		/**
		 * Display the hero section via homepage action
		 * @see display_product_hero()
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public static function homepage( $atts ) {
			if ( 'enable' !== get_theme_mod( 'sprh_enable' ) ) {
				return;
			}

			// Default just for homepage customizer one needs to be full, so set that there
			$atts = array( 'width' => get_theme_mod( 'sprh_layout' ) );

			SP_Frontend_Product_Hero::display_product_hero( $atts );
		}

		/**
		 * Homepage callback
		 *
		 * @since 2.0.0
		 * @return bool
		 */
		public function storefront_homepage_template_callback() {
			return is_page_template( 'template-homepage.php' ) ? true : false;
		}
	}

endif;

return new SP_Frontend_Product_Hero();