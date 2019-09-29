<?php
/**
 * Displays Portfolio archives
 *
 * @package Eris
 */
get_header(); ?>

	<div class="container">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<?php

					if ( have_posts() ) : ?>

						<div class="page-header">
							<?php
								// Display term name
								printf( '<h2 class="page-title">%s</h2>', esc_html( eris_return_portfolio_page( 'title' ) ) );

								// Portfolio types filter
								eris_category_filter();
							?>
						</div>

						<div class="portfolio-wrapper <?php eris_portfolio_class(); ?>" id="post-load">

							<div class="<?php eris_grid_sizer_class(); ?>"></div>

							<?php while ( have_posts() ) : the_post(); ?>

								<?php get_template_part( 'templates/template-parts/content', 'portfolio' ); ?>

							<?php endwhile; ?>

						</div>

						<?php the_posts_navigation(); ?>

					<?php else : ?>

						<section class="no-results not-found">

							<header class="page-header">
								<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'eris' ); ?></h1>
							</header>
							<div class="page-content">
								<?php if ( current_user_can( 'publish_posts' ) ) : ?>

									<p><?php printf( wp_kses( __( 'Ready to publish your first project? <a href="%1$s">Get started here</a>.', 'eris' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php?post_type=jetpack-portfolio' ) ) ); ?></p>

								<?php else : ?>

									<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'eris' ); ?></p>
									<?php get_search_form(); ?>
									<div class="search-instructions"><?php esc_html_e( 'Press Enter / Return to begin your search.', 'eris' ); ?></div>

								<?php endif; ?>
							</div>

						</section>

					<?php endif; ?>

			</main>
		</div>
	</div><!-- .container -->

<?php get_footer(); ?>
