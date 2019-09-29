<?php
/**
 * The Template for displaying all single projects.
 *
 * @package Eris
 */

get_header();

	while ( have_posts() ) : the_post();

		// Display single portoflio single layout depending on tag added
		if ( eris_is_split_layout() ) {
			get_template_part( 'templates/template-parts/content', 'portfolio-single-split' );
		} else {
			get_template_part( 'templates/template-parts/content', 'portfolio-single' );
		}

	endwhile;

get_sidebar();
get_footer();

