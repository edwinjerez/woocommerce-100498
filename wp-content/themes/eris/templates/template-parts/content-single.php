<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Eris
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="container container-small">

		<div class="entry-content">

			<?php
				the_content( sprintf(
					/* translators: %s: Name of current post. */
					wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'eris' ), array( 'span' => array( 'class' => array() ) ) ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				) );

				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'eris' ),
					'after'  => '</div>',
				) );
			?>

			<?php if ( is_single() ) : ?>

				<?php eris_entry_footer(); ?>

			<?php endif; ?>

		</div><!-- .entry-content -->


	</div><!-- .container.container-small -->

</article><!-- #post-## -->

