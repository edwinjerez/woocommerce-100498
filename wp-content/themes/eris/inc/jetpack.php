<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.me/
 *
 * @package Eris
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.me/support/infinite-scroll/
 * See: https://jetpack.me/support/responsive-videos/
 */
function eris_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support( 'infinite-scroll', array(
		'wrapper'   => false,
		'container' => 'post-load',
		'render'	=> 'eris_infinite_scroll_render',
		'footer'	=> 'page',
	) );

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	// Add theme support for JetPack Portfolio
	add_theme_support( 'jetpack-portfolio' );

	// Add excerpt functionality to jetpack portfolio projects
	add_post_type_support( 'jetpack-portfolio', 'excerpt' );

	// Remove post formats functionality to jetpack portfolio projects
	remove_post_type_support( 'jetpack-portfolio', 'post-formats' );

	// Add Featured Content Support
	add_theme_support( 'featured-content', array(
		'filter'     => 'eris_get_featured_posts',
		'post_types' => array( 'page', 'post', 'jetpack-portfolio' )
	) );

	// Add support for Content Options
    add_theme_support( 'jetpack-content-options', array(
        'blog-display' => 'excerpt',
        'author-bio'   => true, // display or not the author bio: true or false
        'post-details' => array(
            'stylesheet' => 'eris-style', // name of the theme's stylesheet
            'date'       => '.entry-date', // the class used for the date
            'categories' => '.cat-links', // the class used for the categories
            'tags'       => '.tags-links', // the class used for the tags
        ),
    ) );
}
add_action( 'after_setup_theme', 'eris_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function eris_infinite_scroll_render() {
	while ( have_posts() ) { the_post();
		if ( is_search() || is_tag() ) :
			get_template_part( 'templates/template-parts/content', 'search' );
		elseif ( is_tax( 'jetpack-portfolio-type' ) ) :
			get_template_part( 'templates/template-parts/content', 'portfolio' );
		else :
			get_template_part( 'templates/template-parts/content', get_post_format() );
		endif;
	}
}

/**
 * Featured posts filter function
 */
function eris_get_featured_posts() {
    return apply_filters( 'eris_get_featured_posts', array() );
}

/**
 * Removing JP Related posts so it can be moved to other location
 */

function jetpackme_remove_rp() {
    if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
        $jprp = Jetpack_RelatedPosts::init();
        $callback = array( $jprp, 'filter_add_target_to_dom' );
        remove_filter( 'the_content', $callback, 40 );
    }
}
add_filter( 'wp', 'jetpackme_remove_rp', 20 );

 /**
 * A helper conditional function that returns a boolean value.
 */
function eris_has_featured_posts() {
	return (bool) eris_get_featured_posts();
}

/**
 * Return early if Author Bio is not available.
 */
function eris_author_bio() {
	if ( ! function_exists( 'jetpack_author_bio' ) ) {
		get_template_part( 'templates/template-parts/content', 'author' );
	} else {
		jetpack_author_bio();
	}
}
