<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Eris
 */

/**
 * Displays entry header
 *
 * @since Eris 1.0
 */
function eris_entry_header() {

	$edit_post_link = '';

	if ( is_user_logged_in() ) {
		$edit_post_link = '<a href="' . esc_url( get_edit_post_link() ) . '"></a>';
	}

	$posted_on = eris_post_date();

	if ( 'jetpack-portfolio' == get_post_type() ) {
		$categories_list = get_the_term_list( get_the_ID(), 'jetpack-portfolio-type', '', '&nbsp;', '' );
	} else {
		$categories_list = get_the_term_list( get_the_ID(), 'category', '', '&nbsp;', '' );
	}

	if ( is_single() ) {
		the_title( '<h1 class="entry-title">', '</h1>' );
	} else if ( 'quote' != get_post_format() && 'link' != get_post_format() ) {
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	}

	printf( '<div class="entry-meta"><span class="cat-links category-list">%1$s</span><span class="post-date">%2$s</span><span class="edit-link">%3$s</span></div>', $categories_list, $posted_on, $edit_post_link );

}

if ( ! function_exists( 'eris_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags.
 */
function eris_entry_footer() {

	if ( is_single() ) : ?>

		<footer class="entry-footer">

		<?php

			if ( 'jetpack-portfolio' == get_post_type() ) {
				$categories_list = get_the_term_list( get_the_ID(), 'jetpack-portfolio-type', '', ' ', '' );
			} else {
				$categories_list = get_the_category_list( ' ' );
			}

			if ( $categories_list && eris_categorized_blog() ) {
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'eris' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			if ( 'jetpack-portfolio' == get_post_type() ) {
				$tags_list = get_the_term_list( get_the_ID(), 'jetpack-portfolio-tag', '', ' ', '' );
			} else {
				$tags_list = get_the_tag_list( '', ' ' );
			}

			if ( $tags_list ) {

				printf( '<span class="tags-links"><span>' . esc_html__( 'Tagged %1$s', 'eris' ) . '</span></span>', $tags_list ); // WPCS: XSS OK.

			}

		?>

		</footer>

	<?php

	endif; // if is single
}
endif;

/**
 * Display the archive title based on the queried object.
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function eris_archive_title( $before = '', $after = '' ) {
	if ( is_category() ) {
		$title = sprintf( esc_html__( 'Category: %s', 'eris' ), '<span>' . single_cat_title( '', false ) . '</span>' );
	} elseif ( is_tag() ) {
		$title = sprintf( esc_html__( 'Tag: %s', 'eris' ), '<span>' . single_tag_title( '', false ) . '</span>' );
	} elseif ( is_author() ) {
		$title = sprintf( esc_html__( 'Author; %s', 'eris' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( esc_html__( 'Year: %s', 'eris' ), '<span>' . get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'eris' ) ) . '</span>' );
	} elseif ( is_month() ) {
		$title = sprintf( esc_html__( 'Month: %s', 'eris' ), '<span>' . get_the_date( esc_html_x( 'F Y', 'monthly archives date format', 'eris' ) ) . '</span>' );
	} elseif ( is_day() ) {
		$title = sprintf( esc_html__( 'Day: %s', 'eris' ), '<span>' . get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'eris' ) ) . '</span>' );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = esc_html_x( 'Asides', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = esc_html_x( 'Galleries', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = esc_html_x( 'Images', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = esc_html_x( 'Videos', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = esc_html_x( 'Quotes', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = esc_html_x( 'Links', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = esc_html_x( 'Statuses', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = esc_html_x( 'Audio', 'post format archive title', 'eris' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = esc_html_x( 'Chats', 'post format archive title', 'eris' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( esc_html__( '%s', 'eris' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( esc_html__( '%1$s: %2$s', 'eris' ), $tax->labels->singular_name, '<span>' . single_term_title( '', false ) . '</span>' );
	} else {
		$title = esc_html__( 'Archives', 'eris' );
	}

	/**
	 * Filter the archive title.
	 *
	 * @param string $title Archive title to be displayed.
	 */
	$title = apply_filters( 'get_the_archive_title', $title );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;  // WPCS: XSS OK.
	}
}


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function eris_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'eris_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'eris_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so eris_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so eris_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in eris_categorized_blog.
 */
function eris_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'eris_categories' );
}
add_action( 'edit_category', 'eris_category_transient_flusher' );
add_action( 'save_post',     'eris_category_transient_flusher' );

/**
 * Displays post featured image
 *
 * @since  Eris 1.0
 */
function eris_featured_image() {

	if ( has_post_thumbnail() ) :

		if ( is_single() ) { ?>

			<figure class="featured-image <?php echo esc_attr( eris_get_featured_image_class() ); ?>">
				<?php the_post_thumbnail( 'eris-single-featured-image' ); ?>
			</figure>

		<?php } else { ?>

			<?php

				// Set image sizes depending on content display
				$thumb_size = 'eris-archive-image';

				if ( 'featured-portrait' == eris_get_featured_image_class() ) {
					$thumb_size = 'eris-archive-image-portrait';
				}

				if ( is_search() || is_tag() ) {
					$thumb_size = 'eris-search-image';
				}

			?>

			<figure class="featured-image <?php echo esc_attr( eris_get_featured_image_class() ); ?>">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $thumb_size ); ?></a>
			</figure>

		<?php }

	else :

		return;

	endif;

}

/**
 * Displays post featured image
 *
 * @since  Eris 1.0
 */
function eris_featured_media() {

	if ( eris_is_split_layout() ) {

		eris_featured_image();

		$images_with_captions = "/\[caption.*\](.*?)\[\/caption\]/";

		preg_match_all( $images_with_captions, get_the_content(), $captions );

		$images_with_caption = $captions[1];

		foreach ($images_with_caption as $image_with_caption) {
			echo wp_kses_post($image_with_caption);
		}

	    $find_images = '~<img [^>]* />~';

		preg_match_all( $find_images, get_the_content(), $all_images );

		$all_images_array = $all_images[0];

		foreach ($all_images_array as $image) {

			$check = str_replace($image, '', $images_with_caption, $count);

			if($count == 0) {
			    echo wp_kses_post($image);
			}

		}

	} else {

		if ( 'gallery' != get_post_format() && 'video' != get_post_format() ) {
			eris_featured_image();
		}

	}

	// If is gallery post format or jetpack-portfolio
	if ( 'gallery' == get_post_format() || eris_is_split_layout() ) :

		global $post;

        if ( get_post_galleries( $post ) && ! post_password_required() ) { ?>

            <div class="entry-gallery">
            	<?php

            		$all_galleries = get_post_galleries( $post );

            		if ( is_single() ) {
	            		foreach( $all_galleries as $gallery ) {
	            			echo $gallery;
	            		}
	            	} else {
	            		echo $all_galleries[0];
	            	}

            	?>
            </div><!-- .entry-gallery -->

		<?php }

	endif;

	// If is video post format or jetpack-portfolio
	if ( 'video' == get_post_format() || eris_is_split_layout() ) :

        if ( eris_get_embeded_media() ) { ?>

            <div class="entry-video <?php echo esc_attr( eris_get_featured_image_class() ); ?>">
                <?php echo eris_get_embeded_media(); ?>
            </div><!-- .entry-video -->

        <?php }

    endif;

}

/**
 * Eris custom paging function
 *
 * Creates and displays custom page numbering pagination in bottom of archives
 *
 * @since Eris 1.0
 */
function eris_numbers_pagination() {

	global $wp_query, $wp_rewrite;

	if ( $wp_query->max_num_pages > 1 ) :

		$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

		$pagination = array(
			'base'      => @add_query_arg( 'paged', '%#%' ),
			'format'    => '',
			'total'     => $wp_query->max_num_pages,
			'current'   => $current,
			'end_size'  => 1,
			'type'      => 'list',
			'prev_next' => true,
			'prev_text' => esc_html__( 'Prev', 'eris' ),
			'next_text' => esc_html__( 'Next', 'eris' )
		);

		if ( $wp_rewrite->using_permalinks() )
			$pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

		if ( ! empty( $wp_query->query_vars['s'] ) ) {
			$pagination['add_args'] = array( 's' => get_query_var( 's' ) );
		}

	    // Display pagination
		printf( '<nav class="navigation paging-navigation"><h1 class="screen-reader-text">%1$s</h1>%2$s</nav>',
			esc_html__( 'Page navigation', 'eris' ),
			paginate_links( $pagination )
		);

	endif;

}

/**
 * Displays portfolio category filter
 *
 * @since Eris 1.0
 */
function eris_category_filter() {

	$categories_list = get_terms( 'jetpack-portfolio-type' );

	if ( ! empty( $categories_list ) && ! is_wp_error( $categories_list ) ) {

		if ( ! is_home() ) {
			if ( isset( get_queried_object()->term_id ) ) {
				$term_id = get_queried_object()->term_id;
			} else {
				$term_id = 0;
			}
		}

		$categories_list_display = '<ul class="category-filter ">';

		if ( is_tax( 'jetpack-portfolio-type' ) ) {

			$categories_list_display .= '<li><a href="' . esc_url( get_permalink( eris_return_portfolio_page( 'id' ) ) ) . '#page-title">' . esc_html__( 'All', 'eris' ) . '</a></li>';

		}

		foreach ( $categories_list as $term ) {

			if ( $term->term_id == $term_id ) {
				$active_class = 'cat-active';
			} else {
				$active_class = '';
			}

			$categories_list_display .= '<li class="' . esc_attr( $active_class ) . '"><a href="' . esc_url( get_term_link( $term ) ) . '">' . $term->name . '</a></li>';

		}

		$categories_list_display .= '</ul>';

		printf( $categories_list_display );

	}

}

/**
 * Side Social Menu
 *
 * @since Eris 1.0
 */
function eris_social_menu() {
	if ( has_nav_menu( 'menu-2' ) ) :

		$args = array(
			'theme_location'  => 'menu-2',
			'container_class' => 'menu-social-container'
		);

		echo '<span id="socMenuTrig" class="social-menu-trig">' . esc_html__( 'Follow', 'eris' ) . '</span>';

		wp_nav_menu( $args );

	endif;
}

/**
 * Generate and display Footer widgets
 *
 * @since Eris 1.0
 */
function eris_footer_widgets() {

	$footer_sidebars = array(
		'sidebar-2',
		'sidebar-3'
	);

	foreach ( $footer_sidebars as $footer_sidebar ) {

		if ( is_active_sidebar( $footer_sidebar ) ) { ?>

			<div class="col-sm-6 widget-area">
				<?php dynamic_sidebar( $footer_sidebar );	?>
			</div>

		<?php

		}

	}

}

/**
 * Displays header on portfolio page template
 *
 * @since Eris 1.0
 */
function eris_portfolio_template_slider() {

	if ( eris_has_featured_posts() ) { ?>

			<div class="featured-slider-wrap hero">
				<div class="verticalize-container container container-medium">

					<div class="featured-slider verticalize">

						<?php

							// Load featured images
							$featured_posts = eris_get_featured_posts();

							foreach ( ( array ) $featured_posts as $featured_post ) : ?>

								<article id="post-<?php echo esc_attr( $featured_post->ID ); ?>" <?php post_class( ' ', $featured_post->ID ); ?>>

									<div class="portfolio-item">

										<?php if ( has_post_thumbnail( $featured_post->ID ) ) : ?>

											<div class="featured-image">
												<a href="<?php echo esc_attr( get_permalink( $featured_post->ID ) ); ?>">
													<?php echo get_the_post_thumbnail( $featured_post->ID ); ?>
												</a>
											</div>

										<?php endif; ?>

										<?php printf( '<h2><a href="%1$s">%2$s</a></h2>', esc_attr( get_permalink( $featured_post->ID ) ), esc_html( get_the_title( $featured_post->ID ) ) ); ?>

									</div>

								</article>

							<?php

							endforeach;

						?>

					</div><!-- .featured-slider -->

				</div><!-- .verticalize-container -->
			</div>

	<?php

	}

}

/**
 * Custom logo display
 *
 * @since Eris 1.0
 */
function eris_the_custom_logo() {

	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}

}

/**
 * Create date post meta
 */
function eris_post_date() {

	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'eris' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	return $posted_on;
}

