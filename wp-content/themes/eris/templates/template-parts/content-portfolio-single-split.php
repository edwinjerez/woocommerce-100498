<?php
/**
 * Content portfolio single standard
 *
 * @package Eris
 */

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <div class="container">

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <div class="entry-header">
                        <!-- Entry header -->
                        <?php eris_entry_header(); ?>
                    </div>

                    <div class="entry-content">
                        <?php

                            the_content();

                            wp_link_pages( array(
                               'before'   => '<div class="page-links clear">',
                               'after'    => '</div>',
                               'pagelink' => '<span class="page-link">%</span>',
                            ) );
                        ?>

                        <?php eris_entry_footer(); ?>

                    </div>

                </article><!-- #post-## -->

                <div class="featured-media">
                    <?php eris_featured_media(); ?>
                </div>


        </div><!-- .container -->

    </main>
</div>

<div class="container">
    <?php

        // Display Related posts
        if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
            echo do_shortcode( '[jetpack-related-posts]' );
        }

        the_post_navigation();
    ?>
</div>

<?php
    // If comments are open or we have at least one comment, load up the comment template
    if ( comments_open() || '0' != get_comments_number() ) :
        comments_template();
    endif;

?>
