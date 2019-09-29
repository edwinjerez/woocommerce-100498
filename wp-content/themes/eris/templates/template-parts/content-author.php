<?php
/**
 * The template for displaying Author Bio
 *
 * @package Eris
 */

if ( ! is_single() ) {
	return;
}
?>

<div class="container container-small entry-author">

	<section class="author-box">

		<figure class="author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'ID' ) ); ?>
		</figure>
		<div class="author-info">
			<h6 class="author-name">
				<?php printf( '<span>%s</span>', esc_html__( 'Posted by:', 'eris' ) ); ?>
				<?php the_author(); ?>
			</h6>
			<p><?php echo get_the_author_meta( 'description' ); ?></p>
		</div>

	</section>

</div>
