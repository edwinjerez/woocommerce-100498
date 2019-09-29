<?php
/**
 * Storefront Powerpack Frontend Blog Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Blog' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Blog extends SP_Frontend {

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
			add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
			add_filter( 'body_class', array( $this, 'body_classes' ) );
			add_filter( 'post_class', array( $this, 'post_classes' ) );
			add_action( 'homepage', array( $this, 'homepage' ), 80 );
			add_action( 'wp', array( $this, 'layout' ), 999 );
		}

		/**
		 * Enqueue CSS.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function styles() {
			wp_enqueue_style( 'sp-blog-styles', SP_PLUGIN_URL . 'includes/customizer/blog/assets/css/style.css' );
		}

		/**
		 * Tweaks layout based on settings.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public function layout() {
			$post_layout_archive     = get_theme_mod( 'sbc_post_layout_archive' );
			$post_layout_single      = get_theme_mod( 'sbc_post_layout_single' );
			$post_layout_homepage    = get_theme_mod( 'sbc_post_layout_homepage' );
			$blog_archive_full_width = get_theme_mod( 'sbc_blog_archive_layout' );
			$blog_single_full_width  = get_theme_mod( 'sbc_blog_single_layout' );

			// Archives.
			if ( 'meta-inline-bottom' === $post_layout_archive && $this->is_blog_archive() ) {
				remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
				add_action( 'storefront_loop_post',    'storefront_post_meta', 35 );
			}

			if ( 'meta-hidden' === $post_layout_archive && $this->is_blog_archive() ) {
				remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
			}

			// Single posts.
			if ( 'meta-inline-bottom' === $post_layout_single && is_singular( 'post' ) ) {
				remove_action( 'storefront_single_post', 'storefront_post_meta', 20 );
				add_action( 'storefront_single_post',    'storefront_post_meta', 35 );
			}

			if ( 'meta-hidden' === $post_layout_single && is_singular( 'post' ) ) {
				remove_action( 'storefront_single_post', 'storefront_post_meta', 20 );
			}

			if ( 'meta-inline-bottom' === $post_layout_homepage && is_page_template( 'template-homepage.php' ) ) {
				remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
				add_action( 'storefront_loop_post',	   'storefront_post_meta', 35 );
			}

			if ( 'meta-hidden' === $post_layout_homepage && is_page_template( 'template-homepage.php' ) ) {
				remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
			}

			if ( $this->is_blog_archive() && true === $blog_archive_full_width ) {
				remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
			}

			if ( is_singular( 'post' ) && true === $blog_single_full_width ) {
				remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
			}
		}

		/**
		 * Adds a class based on the extension name and any relevant settings.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function body_classes( $classes ) {
			global $storefront_version;

			$post_layout_archive     = get_theme_mod( 'sbc_post_layout_archive' );
			$post_layout_single      = get_theme_mod( 'sbc_post_layout_single' );
			$post_layout_homepage    = get_theme_mod( 'sbc_post_layout_homepage' );
			$blog_archive_full_width = get_theme_mod( 'sbc_blog_archive_layout' );
			$blog_single_full_width  = get_theme_mod( 'sbc_blog_single_layout' );
			$magazine                = get_theme_mod( 'sbc_magazine_layout' );

			if ( version_compare( $storefront_version, '2.0.0', '>=' ) ) {
				$version = '-2';
			} else {
				$version = '';
			}

			// Archives.
			if ( $this->is_blog_archive() ) {
				$classes[] = 'sbc-' . $post_layout_archive . $version;
			}

			if ( $this->is_blog_archive() && true === (bool) $blog_archive_full_width ) {
				$classes[] = 'storefront-full-width-content';
			}

			if ( $this->is_blog_archive() && true === (bool) $magazine ) {
				$classes[] = 'sbc-magazine';
			}

			// Single.
			if ( is_singular( 'post' ) ) {
				$classes[] = 'sbc-' . $post_layout_single . $version;
			}

			if ( is_singular( 'post' ) && true === (bool) $blog_single_full_width ) {
				$classes[] = 'storefront-full-width-content';
			}

			// Homepage.
			if ( is_page_template( 'template-homepage.php' ) ) {
				$classes[] = 'sbc-' . $post_layout_homepage . $version;
			}

			return $classes;
		}

		/**
		 * Applies classes to the post tag.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function post_classes( $classes ) {
			$magazine = get_theme_mod( 'sbc_magazine_layout' );

			if ( true === $magazine && $this->is_blog_archive() ) {
				global $wp_query;

				// Set "odd" or "even" class if is not single.
				$classes[] = $wp_query->current_post % 2 == 0 ? 'sbc-even' : 'sbc-odd';
			}

			return $classes;
		}

		public static function homepage() {
			$display_homepage_blog = get_theme_mod( 'sbc_homepage_blog_toggle' );
			$title                 = get_theme_mod( 'sbc_homepage_blog_title' );
			$homepage_blog_columns = get_theme_mod( 'sbc_homepage_blog_columns' );
			$homepage_blog_limit   = get_theme_mod( 'sbc_homepage_blog_limit' );

			if ( true === $display_homepage_blog ) {
				$args = array(
					'post_type'           => 'post',
					'posts_per_page'      => absint( $homepage_blog_limit ),
					'ignore_sticky_posts' => true,
				);

				$query 	= new WP_Query( $args );

				echo '<div class="storefront-product-section storefront-blog columns-' . esc_attr( $homepage_blog_columns ) . '">';

				echo apply_filters( 'storefront_homepage_blog_section_title_html', $blog_section_title = '<h2 class="section-title">' . esc_attr( $title ) . '</h2>', $title );

				if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
						get_template_part( 'content' );
				endwhile;
					wp_reset_postdata();
				else :
					echo '<p>' . esc_attr__( 'Sorry, no posts matched your criteria.', 'storefront-powerpack' ) . '</p>';
				endif;

				echo '</div>';
			}
		}

		/**
		 * Returns true when viewing a non WooCommerce archive.
		 *
		 * @return bool
		 */
		private function is_blog_archive() {
			return ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) && ( is_archive() || is_search() || is_category() || is_tag() || ( is_home() && ! is_page_template( 'template-homepage.php' ) ) );
		}
	}

endif;

return new SP_Frontend_Blog();