<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Eris
 */

get_header(); ?>

<div class="container container-medium">

	<div id="primary" class="content-area listing">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					eris_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<div id="post-load">

			<?php

				while ( have_posts() ) : the_post();

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'templates/template-parts/content', get_post_format() );

				endwhile;

			?>

			</div>
			<!-- #post-load -->

		<?php

			// Archives numbered paging
			eris_numbers_pagination();

		else :

			get_template_part( 'templates/template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

</div><!-- .container -->

<?php get_footer(); ?>
