<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Eris
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<div class="container">

			<div class="row">

				<div class="site-info col-sm-5">
					<div class="footer-site-branding">

						<!-- Display website logo -->
						<?php eris_the_custom_logo(); ?>

						<?php if ( is_front_page() && is_home() ) : ?>
							<h2 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
						<?php else : ?>
							<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php endif;?>

					</div>

					<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'eris' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'eris' ), 'WordPress' ); ?></a>
					<span class="sep"> | </span>
					<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'eris' ), 'Eris', '<a href="http://themeskingdom.com" rel="designer">Themes Kingdom</a>' ); ?>

				</div><!-- .site-info -->

				<div class="col-sm-7">

					<div class="row">
						<?php eris_footer_widgets(); ?>
					</div><!-- .row -->

				</div><!-- .col-sm-6 -->

			</div><!-- .row -->

		</div><!-- .container -->

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
