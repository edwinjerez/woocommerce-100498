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

	<div class="entry-meta">
		<span class="post-date">
			<?php eris_entry_header(); ?>
		</span>
	</div>

	<div class="entry-content">

		<?php if ( is_search() ) { ?>

				<header class="entry-header">

					<!-- Entry header -->
					<?php eris_entry_header(); ?>

				</header><!-- .entry-header -->

		<?php }

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
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php eris_entry_footer(); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->

