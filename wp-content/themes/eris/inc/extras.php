<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Eris
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 *
 * @since Eris 1.0
 */
function eris_body_classes( $classes ) {

    // Portfolio layout settings
    $portfolio_layout = get_theme_mod( 'portfolio_layout_setting', 'shuffle' );
    $sticky_header    = get_theme_mod( 'sticky_header_setting', 1 );
    $menu_type        = get_theme_mod( 'header_display_setting', 0 );

    // featured slider

    if ( is_front_page() ) {
        if ( eris_has_featured_posts() && !is_tax( 'jetpack-portfolio-type' ) && !is_paged() ) {
            $classes[] = 'slider-initialized';
        }

        if ( is_page_template( 'templates/portfolio-page.php' ) && !is_home() && !eris_has_featured_posts() && ( '' != get_post_field( 'post_content', get_the_ID() ) ) && !is_tax( 'jetpack-portfolio-type' ) && !is_paged() ) {
            $classes[] = 'headline-template';
        }
    }

    // Portfolio archives classes
    if ( is_page_template( 'templates/portfolio-page.php' ) || ( is_tax( 'jetpack-portfolio-type' ) ) ) {
        if ( 'shuffle' == $portfolio_layout ) {
            $classes[] = 'shuffle-layout';
        } else {
            $classes[] = esc_attr( 'layout-' . $portfolio_layout );
        }
    }

    if ( is_single() && 'jetpack-portfolio' == get_post_type() ) {
        $project_layout = get_theme_mod( 'single_project_layout_setting', 0 );

        if ( $project_layout ) {
            $classes[] = 'split-layout';
        } else {
            if ( has_excerpt() ) {
                $classes[] = 'single-portfolio-headline';
            }
        }
    }

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

    // Menu type class
    if ( $menu_type ) {
        $classes[] = 'hamburger-menu';
    } else {
        $classes[] = 'standard-menu';
    }

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

    // Sticky header
    if ( $sticky_header ) {
        $classes[] = 'sticky-header';
    }

    // No sidebar class
    if ( !is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }

    // Adds a class of tk-theme-frontend when viewing frontend.
    if ( !is_admin() ) {
        $classes[] = 'tk-theme-frontend';
    }

	return $classes;
}
add_filter( 'body_class', 'eris_body_classes' );

/**
 * Adds custom classes to portfolio wrapper
 */
function eris_portfolio_class() {
    // Get portoflio layout settings
    $portfolio_layout = get_theme_mod( 'portfolio_layout_setting', 'shuffle' );

    if ( 'shuffle' != $portfolio_layout ) {
        echo esc_attr( 'masonry' );
    } else {
        return;
    }
}

/**
 * Display classes for grid sizer helper
 */
function eris_grid_sizer_class() {

    // Get portfolio layout setting
    $portfolio_layout = get_theme_mod( 'portfolio_layout_setting', 'shuffle' );
    $classes          = array();
    $classes[]        = 'grid-sizer';

    if ( 'shuffle' != $portfolio_layout ) {
        if ( 'three-columns' == $portfolio_layout ) {
            $classes[] = 'col-md-4 col-sm-6';
        } else {
            $classes[] = 'col-lg-3 col-md-4 col-sm-6';
        }
    }

    echo esc_attr( implode( ' ', $classes ) );
}

/**
 * Adds custom classes to the array of post classes.
 *
 * @param array $classes Classes for the post element.
 * @return array
 *
 * @since Eris 1.0
 */
function eris_post_classes( $classes ) {

    // Get portoflio layout settings
    $portfolio_layout = get_theme_mod( 'portfolio_layout_setting', 'shuffle' );

    if ( ( 'jetpack-portfolio' == get_post_type() && !is_single() && !is_search() ) || ( is_tax( 'jetpack-portfolio-type' ) ) ) :

        if ( 'shuffle' != $portfolio_layout ) {
            if ( 'three-columns' == $portfolio_layout ) {
                $classes[] = 'col-md-4 col-sm-6';

            } else {
                $classes[] = 'col-lg-3 col-md-4 col-sm-6';
            }

        }

    endif;

	return $classes;
}
add_filter( 'post_class', 'eris_post_classes' );

/**
 * Get Thumbnail Image Size Class
 *
 * @since Eris 1.0
 */
function eris_get_featured_image_class() {

    if ( 'jetpack-portfolio' == get_post_type() && 'shuffle' != get_theme_mod( 'portfolio_layout_setting', 'shuffle' ) ) {
        return;
    }

    if ( has_post_thumbnail() ) {

        $thumb_class = '';
        $imgData     = wp_get_attachment_metadata( get_post_thumbnail_id( get_the_ID() ) );
        if ( empty( $imgData ) ){
            return $thumb_class; //VIP: if $url is blank or empty getimagesize will throw a PHP warning. Let's bail before it gets to it if the url is not present
        }
        $width       = $imgData['width'];
        $height      = $imgData['height'];

        if ( $width > $height || $width == $height ) {
            $thumb_class = 'featured-landscape';
        } else {
            $thumb_class = 'featured-portrait';
        }

        return esc_attr( $thumb_class );

    }

}

/**
 * Check for embed content in post and extract
 *
 * @since Eris 1.0
 */
function eris_get_embeded_media() {
    $content   = get_the_content();
    $embeds    = get_media_embedded_in_content( $content );
    $video_url = wp_extract_urls( $content );

    if ( !empty( $embeds ) ) {

        // Check what is the first embed containg video tag, youtube or vimeo
        foreach( $embeds as $embed ) {
            if ( strpos( $embed, 'video' ) || strpos( $embed, 'youtube' ) || strpos( $embed, 'vimeo' ) ) {

                $id   = 'eris' . rand();
                $href = "#TB_inline?height=640&width=1000&inlineId=" . $id;

                if ( !is_single() && has_post_thumbnail() ) {

                    $video_url = '<div id="' . $id . '" style="display:none;">' . $embed . '</div>';
                    $video_url .= '<figure class="featured-image"><a class="thickbox" title="' . get_the_title() . '" href="' . $href . '">' . get_the_post_thumbnail() . '</a></figure>';

                    return $video_url;

                } else {

                    return $embed;

                }

            }
        }

    } else {

        if ( $video_url ) {

            if ( strpos( $video_url[0], 'youtube' ) || strpos( $video_url[0], 'vimeo' ) ) {

                $id   = 'eris' . rand();
                $href = "#TB_inline?height=640&width=1000&inlineId=" . $id;

                if ( !is_single() && has_post_thumbnail() ) {

                    $video_url = '<div id="' . $id . '" style="display:none;">' . wp_oembed_get( $video_url[0] ) . '</div>';
                    $video_url .= '<figure class="featured-image"><a class="thickbox" title="' . get_the_title() . '" href="' . $href . '">' . get_the_post_thumbnail() . '</a></figure>';

                    return $video_url;

                } else {

                    return wp_oembed_get( $video_url[0] );

                }

            }

        } else {
            // No video embedded found
            return $content;
        }
    }
}

/**
 * Remove parenthesses with dots from excerpt
 *
 * @since Eris 1.0
 */
function eris_excerpt_more( $more ) {
    return str_replace( '[...]', '', $more );
}
add_filter( 'excerpt_more', 'eris_excerpt_more' );

/**
 * Add read more text to excerpt
 *
 * @since Eris 1.0
 */
function eris_add_read_more_excerpt( $excerpt ) {
    $read_more_txt = sprintf(
        /* translators: %s: Name of current post. */
        wp_kses( __( 'Read more %s', 'eris' ), array( 'span' => array( 'class' => array() ) ) ),
        the_title( '<span class="screen-reader-text">"', '"</span>', false )
    );

    $read_more_link = '';

    if ( !is_single() ){
        $read_more_link = '<a class="read-more-link" title="' . get_the_title() . '" href=" ' . esc_url( get_permalink() ) . ' ">' . $read_more_txt . '</a>';
    }

    return $excerpt . $read_more_link;
}
add_filter( 'the_excerpt', 'eris_add_read_more_excerpt' );

/**
 * Removes parenthesses from category and archives widget
 *
 * @since Eris 1.0
 */
function eris_categories_postcount_filter( $variable ) {
    $variable = str_replace( '(', '<span class="post_count"> ', $variable );
    $variable = str_replace( ')', '</span>', $variable );
    return $variable;
}
add_filter( 'wp_list_categories','eris_categories_postcount_filter' );

function eris_archives_postcount_filter( $variable ) {
    $variable = str_replace( '(', '<span class="post_count"> ', $variable );
    $variable = str_replace( ')', '</span>', $variable );
    return $variable;
}
add_filter( 'get_archives_link', 'eris_archives_postcount_filter' );

/**
 * Filter content for gallery post format
 *
 * @since  Eris 1.0
 */
function eris_filter_post_content( $content ) {

    $orig_content = $content;
    if ( 'page' != get_post_type() ) :

        if ( 'video' == get_post_format() || eris_is_split_layout() ) {
            $video_content = get_media_embedded_in_content( $content );
            $video_url     = wp_extract_urls( $content );

            if ( $video_content ) {

                $video_content = '<div class="jetpack-video-wrapper">' . $video_content[0] . '</div>';
                $content = str_replace( $video_content, '', $content );
            }

            if ( $video_url ) {

                if ( strpos( $video_url[0], 'youtube' ) || strpos( $video_url[0], 'vimeo' ) ) {
                    $content = str_replace( $video_url[0], '', $content );
                }
            }

        }

        if ( 'gallery' == get_post_format() || eris_is_split_layout() ) {
            $regex   = '/\[gallery.*]/';
            $content = preg_replace( $regex, '', $content, -1 );
        }

        if ( eris_is_split_layout() ) {
            $content = preg_replace( '/\[caption.*?].*?(<img.*?\/?>).*?\[\/caption]/s', '', $content );
            $content = preg_replace( '/<img[^>]+./', '', $content );
        }

    endif;
    
    // Escape content if it has been filtered.
    if ( $content !== $orig_content ) {
      $content = wp_kses_post( $content );
    }

    return $content;
}

add_filter( 'the_content', 'eris_filter_post_content', 1, 1 );

/**
 * Get title of page that uses portfolio template
 *
 * @return  String [Page title]
 */
function eris_return_portfolio_page( $type ) {
    $pages = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'templates/portfolio-page.php'
    ) );

    if ( !empty( $pages ) ) {
        if ( 'id' == $type ) {
            return $pages[0]->ID;
        } else {
            return $pages[0]->post_title;
        }
    }
}

/**
 * Conditional helper for split layout projects
 */
function eris_is_split_layout() {

    $project_layout = get_theme_mod( 'single_project_layout_setting', 0 );

    if ( is_single() && 'jetpack-portfolio' == get_post_type() && $project_layout ) {
        return true;
    } else {
        return false;
    }

}


// Load our function when hook is set
function eris_modify_query_get_projects( $query ) {

    // Check if on frontend and main query is modified
    if ( ! is_admin() && $query->is_main_query() && is_tax( 'jetpack-portfolio-type' ) ) {

        $posts_per_page = get_option( 'jetpack_portfolio_posts_per_page' );
        $query->set( 'posts_per_archive_page', $posts_per_page );

    }
}
add_action( 'pre_get_posts', 'eris_modify_query_get_projects' );

/**
 * Add portfolio to tag page archive
 */
function eris_query_include_posts( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_tag() ) {
        $query->set( 'post_type', array( 'post', 'jetpack-portfolio' ) );
    }
}
add_action( 'pre_get_posts', 'eris_query_include_posts' );
