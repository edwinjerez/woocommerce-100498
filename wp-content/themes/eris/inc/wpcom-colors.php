<?php
/* Custom Colors: Eris */

//Background
add_color_rule( 'bg', '#ffffff', array(
    array( 'body, .page-template-portfolio-page .site-content, .page-template-portfolio-page .site-footer, button:focus, input[type="button"]:focus, input[type="reset"]:focus, input[type="submit"]:focus, #eu-cookie-law input[type="submit"]:focus', 'background-color' ),

    array( 'button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, #eu-cookie-law input[type="submit"]:hover', 'background-color' ),

    array( '.comments-area, .featured-slider-wrap', 'background-color', '-0.2' ),
) );

add_color_rule( 'txt', '#000000', array(
    array( '.site-header .site-title a, .main-navigation a, #big-search-trigger, .sidebar-trigger, .featured-slider .slick-dots .slick-active:after, .gallery-count i', 'color', 'bg' ),

    array( '.main-navigation a:hover', 'color', 'bg', 2 ),

    array( 'h1, h2, h3, h4, h5, h6, .widget-title, .entry-title, .entry-title a, .scroll-down, .scroll-up, .menu-toggle i', 'color', 'bg' ),

    array( '.portfolio-item .entry-title a', 'color', '#fff' ),

    array( '.menu-toggle span, .menu-toggle span:before, .menu-toggle span:after', 'background-color', 'bg' ),

    array( 'h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover, .featured-slider .slick-arrow:hover:before', 'color', 'bg', 2 ),

    array( '.featured-slider .slick-arrow, .featured-slider .portfolio-item a, .featured-slider .slick-dots li button:before, .featured-slider .slick-dots li.slick-active button:before', 'color', 'bg' ),

    array( '.menu-social-container a', 'color', 'bg' ),

    array( '.comments-title span, .search-post-type, .bypostauthor > .comment-body .comment-author b:after, .widget_wpcom_social_media_icons_widget a', 'color', 'bg' ),

),
__( 'Menus & Headings' ) );

add_color_rule( 'link', '#000000', array(
    //Contrast with body background bg
    array( 'a, .emphasis, .format-link .entry-content p, blockquote, blockquote p, q, blockquote cite, blockquote + cite, blockquote + p cite, q cite, q + cite, q + p cite, .archive.category .page-title span, .archive.tag .page-title span, .tax-jetpack-portfolio-tag .page-title span, .archive.date .page-title span, .archive.author .page-title span, .search .page-title span, .tag.archive .page-title span, .no-results input[type="search"], .error-404 input[type="search"], .no-results .search-instructions, .error-404 .search-instructions, .nav-links a, body #infinite-handle button, body #infinite-handle button:hover, body #infinite-handle button:focus, .gallery-count, .widget-title, .widget-title label, .widget .widget-title a, .widget_calendar caption, .widget_calendar th, .widget_calendar tfoot a, .widget .search-form input[type="submit"]:focus, .widget_blog_subscription input[type="submit"]:focus, .paging-navigation .prev, .paging-navigation .next, .category-filter .cat-active a, .gallery-caption, .entry-gallery .gallery-size-full:after, .featured-slider .slick-dots button, author-title span', 'color', 'bg' ),

    array( '.comment-metadata a, .comment .reply a, .comment-metadata > * + *:before, .widget a, .tagcloud a, button:focus, input[type="button"]:focus, input[type="reset"]:focus, input[type="submit"]:focus, #eu-cookie-law input[type="submit"]:focus', 'color', 'bg' ),

    array( 'a:hover, .nav-menu > li > a:hover + a, div[class^="gr_custom_container"] a:hover, #big-search-trigger:hover, .sidebar-trigger:hover', 'color', 'bg', 2 ),

    array( 'button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, #eu-cookie-law input[type="submit"]:hover, .nav-links a:hover, .comment-metadata a:hover, .comment .reply a:hover, .entry-footer a:hover, .category-filter a:hover, .widget a:hover, .widget .search-form input[type="submit"]:hover, .widget_blog_subscription input[type="submit"]:hover, .paging-navigation a:hover, .listing .format-link .entry-content > p > a:hover, div#respond .comment-form-fields p.comment-form-posting-as a:hover, div#respond .comment-form-fields p.comment-form-log-out a:hover', 'color', 'bg', 2 ),

    //no Contrast
    array( '.menu-social-container .social-menu-trig, div.sharedaddy .sd-social h3.sd-title, button, input[type="button"], input[type="reset"], input[type="submit"], div#respond .form-submit input, div#respond .form-submit input#comment-submit, div#respond .comment-form-fields input[type=submit], div#respond p.form-submit input[type=submit], div#respond input[type=submit], form#commentform #submit, #eu-cookie-law input[type="submit"]', 'background-color' ),

    array( 'button, input[type="button"], input[type="reset"], input[type="submit"], div#respond .form-submit input, div#respond .form-submit input#comment-submit, div#respond .comment-form-fields input[type=submit], div#respond p.form-submit input[type=submit], div#respond input[type=submit], form#commentform #submit, #eu-cookie-law input[type="submit"]', 'border-color' ),
),
__( 'Links' ) );

add_color_rule( 'fg1', '#ffffff', array(
) );
add_color_rule( 'fg2', '#ffffff', array(
) );


//Extra rules
add_color_rule( 'extra', '#ffffff', array(
    array( '.menu-social-container .social-menu-trig,
            div.sharedaddy .sd-social h3.sd-title, button, input[type="button"], input[type="reset"], input[type="submit"], div#respond .form-submit input, div#respond .form-submit input#comment-submit, div#respond .comment-form-fields input[type=submit], div#respond p.form-submit input[type=submit], div#respond input[type=submit], form#commentform #submit, #eu-cookie-law input[type="submit"]', 'color', 'link' ),
) );

add_color_rule( 'extra', '#3e3e3e', array(
    //Contrast with body background bg
    array( 'select, body, .widget, .widget p, .rssSummary, p, .entry-content li, .menu-social-container .social-menu-trig:before, #respond .comment-form-fields p.comment-form-posting-as, #respond .comment-form-fields p.comment-form-log-out, #respond .comment-form-service a, input[type="text"], input[type="email"], input[type="tel"], input[type="url"], input[type="password"], input[type="search"], textarea', 'color', 'bg' ),
    array( 'blockquote:before, q:before, .listing .format-link .entry-content:before, .listing .format-link .entry-content > p > a:before, .single .format-link .entry-content:before, .paging-navigation a, .paging-navigation .dots', 'color', 'bg' ),
    array( '.site-header, .site-description', 'color', 'bg' ),
    array( '.featured-slider .slick-dots .slick-active:after, .gallery-count i', 'background-color', 'bg' ),
) );


//Additional palettes

add_color_palette( array(
    '#282828',
    '#ffffff',
    '#a5a5a5',
), 'Black' );

add_color_palette( array(
    '#e6eaef',
    '#0028ff',
    '#646464',
), 'Blue & Gray' );

add_color_palette( array(
    '#f1eeea',
    '#201453',
    '#4c4669',
), 'Navy Blue' );

add_color_palette( array(
    '#3a4750',
    '#eeeeee',
    '#646464',
), 'Dark Gray' );

add_color_palette( array(
    '#3a4750',
    '#f9c771',
    '#ebe7e0',
), 'Orange' );

add_color_palette( array(
    '#eeeeee',
    '#444444',
    '#f42323',
), 'Red' );
