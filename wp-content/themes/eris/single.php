<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Eris
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div class="container container-medium">

			<div class="hero">
	    		<!-- Featured media -->
	    		<?php eris_featured_media(); ?>

	    		<div class="entry-header">
	    			<!-- Entry header -->
	    			<?php eris_entry_header(); ?>
	    		</div>
			</div>

		</div><!-- .container.container-medium -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<div class="container container-small">

					<?php

						get_template_part( 'templates/template-parts/content', 'single' );

						eris_author_bio();

					?>

				</div><!-- .container.container-small -->

				<div class="container">
					<?php

						// Display Related posts
						if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
							echo do_shortcode( '[jetpack-related-posts]' );
						}

						// Post navigation
						the_post_navigation();

					?>
				</div>

				<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				?>

			</main><!-- #main -->
		</div><!-- #primary -->

	<?php endwhile; // End of the loop. ?>

<?php

get_footer();
