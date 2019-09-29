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

	<!-- Display featured image / video / gallery -->
	<?php eris_featured_media(); ?>

	<div class="entry-content">

		<header class="entry-header">

			<!-- Entry header -->
			<?php eris_entry_header(); ?>

		</header><!-- .entry-header -->

		<?php
			the_excerpt();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'eris' ),
				'after'  => '</div>',
			) );
		?>

	</div><!-- .entry-content -->

	<!-- Display post footer -->
	<?php eris_entry_footer(); ?>

</article><!-- #post-## -->

