<?php
/**
 * Storefront Powerpack Customizer Reviews Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Reviews' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Reviews extends SP_Frontend {
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
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
			add_filter( 'body_class', array( $this, 'body_classes' ) );
			add_action( 'homepage', array( $this, 'storefront_homepage_reviews' ), 90 );

			// Setup the shortcodes
			add_shortcode( 'storefront_reviews', array( $this, 'reviews_shortcode' ) );
		}

		/**
		 * Regiser Assets for later use.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function scripts() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_register_script( 'owl-carousel', SP_PLUGIN_URL . 'includes/customizer/reviews/assets/js/owl-carousel.min.js', array( 'jquery' ), '1.3.3' );
			wp_register_script( 'owl-carousel-init', SP_PLUGIN_URL . 'includes/customizer/reviews/assets/js/owl-carousel-init' . $suffix . '.js', array( 'owl-carousel' ), storefront_powerpack()->version );

			$translation_array = array(
				'columns'  => get_theme_mod( 'storefront_reviews_columns' ),
				'previous' => __( 'Previous', 'storefront-powerpack' ),
				'next'     => __( 'Next', 'storefront-powerpack' ),
			);

			wp_localize_script( 'owl-carousel-init', 'carousel_parameters', $translation_array );
		}

		/**
		 * Enqueue CSS.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function styles() {
			wp_enqueue_style( 'sp-reviews-styles', SP_PLUGIN_URL . 'includes/customizer/reviews/assets/css/style.css', '', storefront_powerpack()->version );

			$content_bg_color = get_theme_mod( 'sp_content_frame_background' );
			$content_frame    = get_theme_mod( 'sp_content_frame' );
			$star_color       = get_theme_mod( 'storefront_reviews_star_color', apply_filters( 'storefront_default_accent_color', '#96588a' ) );
			$accent_color     = get_theme_mod( 'storefront_accent_color' );

			if ( $content_bg_color && 'true' == $content_frame ) {
				$bg_color = str_replace( '#', '', $content_bg_color );
			} else {
				$bg_color = get_theme_mod( 'background_color' );
			}

			$storefront_reviews_style = '
			.style-2 .sr-review-content {
				background-color: ' . storefront_adjust_color_brightness( $bg_color, 10 ) . ';
			}

			.style-2 .sr-review-content:after {
				border-top-color: ' . storefront_adjust_color_brightness( $bg_color, 10 ) . ' !important;
			}

			.star-rating span:before,
			.star-rating:before {
				color: ' . $star_color . ';
			}

			.star-rating:before {
				opacity: 0.25;
			}

			.sr-carousel .owl-prev:before, .sr-carousel .owl-next:before {
				color: ' . $accent_color . ';
			}

			ul.product-reviews li.product-review.style-3 .inner {
				background-color: ' . $this->hex_to_rgba( $bg_color, 0.8 ) . ';
			}';

			wp_add_inline_style( 'sp-reviews-styles', $storefront_reviews_style );
		}

		/**
		 * Add custom body classes.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function body_classes( $classes ) {
			global $storefront_version;

			if ( version_compare( $storefront_version, '2.3.0', '<' ) ) {
				$classes[] = 'storefront-reviews-compatibility';
			}

			return $classes;
		}

		/**
		 * Display the reviews
		 * @param  [type] $atts [description]
		 * @return [type]       [description]
		 */
		public static function reviews( $atts ) {
			$reviews_type = get_theme_mod( 'storefront_reviews_reviews_type' );
			$product      = 0;

			if ( 'specific-product' === $reviews_type ) {
				$product = get_theme_mod( 'storefront_reviews_product' );
			}

			$specific = '';

			if ( 'specific-reviews' == $reviews_type ) {
				$specific = get_theme_mod( 'storefront_reviews_specific_reviews' );
			}

			$atts = extract( shortcode_atts( array(
				'title'      => sanitize_text_field( get_theme_mod( 'storefront_reviews_heading_text' ) ),
				'columns'    => get_theme_mod( 'storefront_reviews_columns' ),
				'number'     => get_theme_mod( 'storefront_reviews_number' ),
				'scope'      => get_theme_mod( 'storefront_reviews_reviews_type' ),
				'product_id' => $product,
				'review_ids' => $specific,
				'layout'     => get_theme_mod( 'storefront_reviews_layout' ),
				'gravatar'   => get_theme_mod( 'storefront_reviews_gravatar' ),
				'carousel'   => get_theme_mod( 'storefront_reviews_carousel' ),

			), $atts, 'storefront_reviews' ) );

			// Evaluate string if coming from shortcode arg
			if ( is_string( $gravatar ) ) {
				$gravatar = ( 'false' === $gravatar ) ? false : true;
			}

			// Evaluate string if coming from shortcode arg
			if ( is_string( $carousel ) ) {
				$carousel = ( 'true' === $carousel ) ? true : false;
			}

			// Check for reviews
			$reviews = get_comments( array(
				'number'      => $number,
				'post_id'     => $product_id,
				'status'      => 'approve',
				'post_status' => 'publish',
				'comment__in' => $review_ids,
				'post_type'   => 'product',
				'parent'      => 0, )
			);

			// If reviews are found, do the stuff
			if ( $reviews ) {
				$carousel_class = '';

				if ( true === (bool) $carousel ) {
					$carousel_class = 'owl-carousel';
				}

				echo '<div class="storefront-product-section storefront-reviews">';

					echo '<div class="woocommerce columns-' . esc_attr( $columns ) . '">';

						if ( $title ) {
							echo '<h2 class="section-title"><span>' . wp_kses_post( $title ) . '</span></h2>';
						}

						echo '<ul class="product-reviews ' . esc_attr( $carousel_class ) . '" data-columns="' . esc_attr( $columns ) . '">';

						$count = 0;

						foreach ( (array) $reviews as $review ) {
							$gravatar_output = '';
							$gravatar_url    = '';

							if ( true == esc_attr( $gravatar ) ) {
								$gravatar_output = get_avatar( $review->comment_author_email );
								$gravatar_url    = get_avatar_url( $review->comment_author_email, array( 'size' => 500 ) );
							}

							$_product    = wc_get_product( $review->comment_post_ID );
							$rating      = intval( get_comment_meta( $review->comment_ID, 'rating', true ) );
							$rating_html = wc_get_rating_html( $rating );

							$count++;

							$class = '';

							if ( 0 == $count % $columns ) {
								$class = 'last';
							}

							if ( 1 == $count % $columns ) {
								$class = 'first';
							}

							echo '<li class="product-review ' . $class . ' ' . esc_attr( $layout ) . '">';

								if ( 'style-1' == $layout ) {
									echo '<a href="' . esc_url( get_comment_link( $review->comment_ID ) ) . '" class="sr-images">';
										echo wp_kses_post( $_product->get_image( 'shop_catalog' ) );
										echo wp_kses_post( $gravatar_output );
									echo '</a>';

									echo '<div class="sr-review-content">';

										echo wp_kses_post( $rating_html );

										echo '<p><strong>' . esc_attr( $_product->get_title() ) . '</strong> (' .wp_kses_post( $_product->get_price_html() ) . ') <br />' . __( 'reviewed by', 'storefront-reviews' ) . ' ' . get_comment_author_link( intval( $review->comment_ID ) ) . '</p>';

										echo '<hr />';

										echo wp_kses_post( wpautop( $review->comment_content ) );

										echo '<a href="' . esc_url( get_permalink( $review->comment_post_ID ) ) . '" class="sr-view-product">' . __( 'View this product', 'storefront-reviews' ) . ' &rarr;</a>';

									echo '</div>';
								} elseif ( 'style-2' == $layout ) {
									echo '<div class="sr-review-content">';

										echo wp_kses_post( $_product->get_image( 'shop_thumbnail' ) );

										echo wp_kses_post( $rating_html );

										echo '<p><strong>' . esc_attr( $_product->get_title() ) . '</strong> (' . wp_kses_post( $_product->get_price_html() ) . ')</p>';

										echo wp_kses_post( wpautop( $review->comment_content ) );

										echo '<a href="' . esc_url( get_permalink( $review->comment_post_ID ) ) . '" class="sr-view-product">' . __( 'View this product', 'storefront-reviews' ) . ' &rarr;</a>';

									echo '</div>';

									echo '<div class="sr-review-meta">';
										echo $gravatar_output;
										echo '<strong>' . get_comment_author_link( intval( $review->comment_ID ) ) . '</strong>' . '<br /><date>' . $review->comment_date . '</date>';
									echo '</div>';
								} elseif ( 'style-3' == $layout ) {
									echo '<div class="sr-review-content" style="background-image: url(' . esc_url( $gravatar_url ) . '); background-size: cover;">';

										echo '<div class="inner">';

											echo $_product->get_image( 'shop_thumbnail' );

											echo $rating_html;

											echo '<p><strong>' . esc_attr( $_product->get_title() ) . '</strong> (' .wp_kses_post( $_product->get_price_html() ) . ') <br />' . __( 'reviewed by', 'storefront-reviews' ) . ' ' . get_comment_author_link( $review->comment_ID ) . '</p>';

											echo '<hr />';

											echo wp_kses_post( wpautop( $review->comment_content ) );

											echo '<a href="' . esc_url( get_permalink( $review->comment_post_ID ) ) . '" class="sr-view-product">' . __( 'View this product', 'storefront-reviews' ) . ' &rarr;</a>';

										echo '</div>';

									echo '</div>';
								}

							echo '</li>';
						}

						echo '</ul>';

					echo '</div>';

				echo '</div>';

				if ( true === (bool) $carousel ) {
					wp_enqueue_script( 'owl-carousel' );
					wp_enqueue_script( 'owl-carousel-init' );
				}
			}
		}

		/**
		 * Hex to RGBA.
		 *
		 * @since 2.0.0
		 * @param $color The hex color.
		 * @param $opacity
		 * @return string The RGBA converted color.
		 */
		public function hex_to_rgba( $color, $opacity = false ) {
			$default = 'rgb(0,0,0)';

			// Return default if no color provided
			if ( empty( $color ) ) {
				return $default;
			}

			// Sanitize $color if "#" is provided
			if ( $color[0] == '#' ) {
				$color = substr( $color, 1 );
			}

			// Check if color has 6 or 3 characters and get values
			if ( strlen( $color ) == 6 ) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return $default;
			}

			// Convert hexadec to rgb
			$rgb = array_map( 'hexdec', $hex );

			// Check if opacity is set(rgba or rgb)
			if ( $opacity ) {
				if ( abs( $opacity ) > 1 ) {
					$opacity = 1.0;
				}

				$output = 'rgba( ' . implode( ', ', $rgb ) . ',' . $opacity . ' )';
			} else {
				$output = 'rgb( ' . implode( ', ', $rgb ) . ' )';
			}

			// Return rgb(a) color string
			return $output;
		}

		/**
		 * Display the reviews section via shortcode
		 *
		 * @since 2.0.0
		 * @see reviews()
		 * @param array
		 * @return string
		 */
		public function reviews_shortcode( $atts ) {
			ob_start();
			SP_Frontend_Reviews::reviews( $atts );
			return ob_get_clean();
		}

		/**
		 * Display the reviews on the homepage
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public static function storefront_homepage_reviews() {
			if ( 'enable' !== get_theme_mod( 'storefront_reviews_enable' ) ) {
				return;
			}

			$atts = array( 'heading_text' => sanitize_text_field( get_theme_mod( 'storefront_reviews_heading_text' ) ) );

			SP_Frontend_Reviews::reviews( $atts );
		}
	}

endif;

return new SP_Frontend_Reviews();