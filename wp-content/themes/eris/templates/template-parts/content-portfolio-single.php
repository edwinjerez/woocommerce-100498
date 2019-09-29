<?php
/**
 * Content portfolio single standard
 *
 * @package Eris
 */

?>


<?php if ( has_excerpt() ) : ?>

    <div class="container container-small">

        <div class="hero verticalize-container">
            <div class="verticalize">

                <div class="entry-header">
                    <!-- Entry header -->
                    <?php eris_entry_header(); ?>
                </div>

                <?php the_excerpt(); ?>

            </div>
        </div>

    </div><!-- .container.container-small -->

<?php endif; ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <?php if ( !has_excerpt() ) { ?>

                <div class="container container-medium">
                    <!-- Featured media -->
                    <?php eris_featured_media(); ?>

                    <div class="entry-header">
                        <!-- Entry header -->
                        <?php eris_entry_header(); ?>
                    </div>
                </div><!-- .container-medium -->

            <?php } else { ?>

                <div class="container container-medium">
                    <!-- Featured media -->
                    <?php eris_featured_media(); ?>
                </div><!-- .container-medium -->

            <?php } ?>

            <div class="container container-small">
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

                <?php eris_author_bio(); ?>

            </div><!-- .container-small -->

        </article><!-- #post-## -->

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

    </main>
</div>
