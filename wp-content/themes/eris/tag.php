<?php
/**
 * The template for displaying tag archives.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Eris
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php printf( esc_html__( 'Tag: %s', 'eris' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<div id="post-load">

				<?php

					while ( have_posts() ) : the_post();

						get_template_part( 'templates/template-parts/content', 'search' );

					endwhile;

				?>

			</div>

			<div class="container container-small">
				<?php
						// Archives numbered paging
						eris_numbers_pagination();

					else :

						get_template_part( 'templates/template-parts/content', 'none' );

					endif;

				?>
			</div>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php

get_footer();

