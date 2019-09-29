<?php
/**
 * Storefront Powerpack Customizer Pricing Tables Class
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Pricing_Tables' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Pricing_Tables extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'setup' ) );
		}

		public function setup() {
			add_action( 'wp_enqueue_scripts', array( $this, 'spt_styles' ), 999 );
			add_filter( 'body_class', array( $this, 'spt_body_class' ) );

			// Setup the shortcodes
			add_shortcode( 'pricing_column', array( $this, 'spt_column' ) );
			add_shortcode( 'pricing_table', array( $this, 'spt_pricing_table' ) );
		}

		/**
		 * Enqueue CSS and custom styles.
		 * @since 2.0.0
		 * @return void
		 */
		public function spt_styles() {
			wp_enqueue_style( 'sp-princing-tables-styles', SP_PLUGIN_URL . 'includes/customizer/pricing-tables/assets/css/style.css', '', storefront_powerpack()->version );

			$header_background_color 			= get_theme_mod( 'spt_header_background_color' );
			$header_text_color 					= get_theme_mod( 'spt_header_text_color' );
			$header_highlight_background_color 	= get_theme_mod( 'spt_header_highlight_background_color' );
			$header_highlight_text_color 		= get_theme_mod( 'spt_header_highlight_text_color' );

			$spt_style = '
			.storefront-pricing-column h2.column-title {
				background-color: ' . $header_background_color . ';
				color: ' . $header_text_color . ';
			}

			.storefront-pricing-column.highlight h2.column-title {
				background-color: ' . $header_highlight_background_color . ';
				color: ' . $header_highlight_text_color . ';
			}';

			wp_add_inline_style( 'sp-princing-tables-styles', $spt_style );
		}

		/**
		 * Storefront Pricing Tables Body Class
		 * Adds a class based on the extension name and any relevant settings.
		 */
		public function spt_body_class( $classes ) {
			$classes[] = 'storefront-pricing-tables-active';

			return $classes;
		}

		/**
		 * Display pricing table wrapper
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 * @since 2.0.0
		 */
		public function spt_pricing_table( $atts, $content = null ) {
			extract( shortcode_atts( array(
				'columns'		=> '',
				'alignment'		=> '',
			), $atts ) );

			if ( '' == $columns ) {
				$columns = get_theme_mod( 'spt_columns' );
			}

			if ( '' == $alignment ) {
				$alignment = get_theme_mod( 'spt_alignment' );
			}

			return '<div class="storefront-pricing-table align-' . esc_attr( $alignment ). ' columns-' . esc_attr( $columns ) . '">' . do_shortcode( $content ) . '</div>';
		}

		/**
		 * Display pricing table column
		 *
		 * @param array $atts
		 * @return string
		 * @since 2.0.0
		 */
		public function spt_column( $atts ) {
			extract( shortcode_atts( array(
				'title'			=> '',
				'id'			=> '',
				'features'		=> '',
				'highlight'		=> '',
				'image'			=> 'true',
			), $atts ) );

			$product = wc_get_product( $id );

			if ( ! $product ) {
				return;
			}

			if ( $title ) {
				$title_output = $title;
			} elseif ( ! $title && $id ) {
				$title_output = $product->get_title();
			} else {
				$title_output = '';
			}

			if ( 'true' == $highlight ) {
				$highlight_class = 'highlight';
			} else {
				$highlight_class = '';
			}

			if ( 'true' == $image && $id ) {
				$image_output = $product->get_image( 'shop_single' );
			} elseif ( 'false' == $image ) {
				$image_output = '';
			} else {
				$image_output = '<img src="' . $image . '" alt="' . $title_output . '" />';
			}

			ob_start();

			?>
			<div class="storefront-pricing-column <?php echo $highlight_class; ?>">
				<?php if ( '' != $title_output ) { ?>
					<h2 class="column-title"><?php echo esc_attr( $title_output ); ?></h2>
				<?php } ?>

				<?php
					echo wp_kses_post( $image_output );
					$this->spt_build_list( $features );
					echo do_shortcode( '[add_to_cart id="' . $id . '"]' );
				?>
			</div>
			<?php

			return ob_get_clean();
		}

		/**
		 * Build a list from an array
		 *
		 * @param array $features
		 * @return void
		 * @since 2.0.0
		 */
		private function spt_build_list( $features ) {
			$items = explode( '|', $features );

			echo '<ul class="features">';
		    foreach ( $items as $item ) {
		        echo '<li>' . $item . '</li>';
		    }
		    echo '</ul>';
		}
	}

endif;

return new SP_Frontend_Pricing_Tables();