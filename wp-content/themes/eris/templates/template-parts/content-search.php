<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Eris
 */

?>

<div class="container container-small">

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<div class="search-post-type">
			<?php echo get_post_type() == 'jetpack-portfolio' ? esc_html__( 'portfolio', 'eris' ) : get_post_type(); ?>
		</div>

		<?php

			// Display featured image
			if ( 'jetpack-portfolio' == get_post_type() || ( is_tag() && 'jetpack-portfolio' == get_post_type() ) ) :
				eris_featured_image();
			endif;

		?>

		<div class="entry-header">
			<?php

				// Display header meta
				eris_entry_header();

			?>
		</div>

	</article><!-- #post-## -->

</div><!-- .container -->
