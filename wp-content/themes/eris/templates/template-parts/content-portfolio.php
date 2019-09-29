<?php
/**
 * The template used for displaying projects on index view
 *
 * @package Eris
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="portfolio-item <?php echo esc_attr( eris_get_featured_image_class() ); ?>">

		<?php if ( has_post_thumbnail() ) : ?>

			<div class="featured-image">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'post-thumbnail', array('class' => 'eris-portfolio-featured-image skip-lazy') ); ?>
				</a>
			</div>

		<?php endif; ?>

		<!-- Entry header -->
		<div class="entry-header">
			<?php eris_entry_header(); ?>
		</div>

		<?php if ( ! has_post_thumbnail() ) : ?>

			<p>
				<?php the_excerpt(); ?>
			</p>

		<?php endif; ?>

	</div>

</article>

