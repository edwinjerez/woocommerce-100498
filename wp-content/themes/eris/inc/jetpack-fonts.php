<?php

add_filter( 'typekit_add_font_category_rules', function( $category_rules ) {

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'b,
		strong',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'dfn',
		array(
			array( 'property' => 'font-style', 'value' => 'italic' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'h1',
		array(
			array( 'property' => 'font-size', 'value' => '2em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'small',
		array(
			array( 'property' => 'font-size', 'value' => '80%' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'sub,
		sup',
		array(
			array( 'property' => 'font-size', 'value' => '75%' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'code,
		kbd,
		pre,
		samp',
		array(
			array( 'property' => 'font-family', 'value' => 'monospace, monospace' ),
			array( 'property' => 'font-size', 'value' => '1em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'button,
		input,
		optgroup,
		select,
		textarea',
		array(
			array( 'property' => 'font', 'value' => 'inherit' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'optgroup',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.bypostauthor > .comment-body .comment-author b:after,
		.edit-link a:before,
		.error-404 .search-form:before,
		.featured-image a:after,
		.gallery-size-full[data-carousel-extra] .gallery-item .gallery-icon:after,
		.jp-carousel-next-button span:before,
		.jp-carousel-previous-button span:before,
		.listing .format-link .entry-content > p > a:before,
		.listing .format-link .entry-content:before,
		.nav-links a:after,
		.no-results .search-form:before,
		.paging-navigation .next:before,
		.paging-navigation .prev:before,
		.single .format-link .entry-content:before,
		.site-header .search-form:before,
		.slick-arrow:before,
		.slideshow-controls a:before,
		.widget_rss .widget-title a:first-of-type:before,
		[class*=" icon-"],
		[class^="icon-"],
		blockquote:before,
		body .tb-close-icon:before,
		q:before',
		array(
			array( 'property' => 'font-family', 'value' => 'icomoon !important' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
			array( 'property' => 'font-weight', 'value' => 'normal' ),
			array( 'property' => 'font-variant', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'body,
		button,
		input,
		keygen,
		select,
		textarea',
		array(
			array( 'property' => 'font-family', 'value' => 'sk-modernist, Helvetica Neue, Helvetica, Arial, sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'h1,
		h2,
		h3,
		h4,
		h5,
		h6,
		.site-title,
		.entry-title a',
		array(
			array( 'property' => 'font-family', 'value' => 'sk-modernist, Helvetica Neue, Helvetica, Arial, sans-serif' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);



	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'html',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.edit-link a,
		.error-404 .search-form input[type="submit"],
		.featured-image a,
		.gallery-size-full[data-carousel-extra] .gallery-item .gallery-icon,
		.hamburger-menu .menu-toggle,
		.masonry .jetpack-portfolio,
		.no-results .search-form input[type="submit"],
		.paging-navigation .next,
		.paging-navigation .prev,
		.shuffle-layout .portfolio-wrapper,
		.site-header .search-form input[type="submit"],
		.slick-arrow,
		.split-layout .site-main > .container,
		.twocolumn,
		body #jp-relatedposts .jp-relatedposts-items-visual',
		array(
			array( 'property' => 'font-size', 'value' => '0' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.dropdown-toggle',
		array(
			array( 'property' => 'font-size', 'value' => '9px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.menu-social-container .social-menu-trig,
		.portfolio-item .category-list,
		.portfolio-item .entry-meta,
		.portfolio-item .post-date,
		.widget .search-form input[type="submit"],
		.widget_blog_subscription input[type="submit"]',
		array(
			array( 'property' => 'font-size', 'value' => '10px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.author-name span,
		.category-list,
		.comment .reply,
		.comment-metadata,
		.listing .format-quote blockquote + cite,
		.listing .format-quote blockquote + p cite,
		.listing .format-quote blockquote cite,
		.listing .format-quote q + cite,
		.listing .format-quote q + p cite,
		.listing .format-quote q cite,
		.post-date,
		.rss-date,
		.sd-rating .rating-msg,
		.sd-rating .sd-title,
		.search-post-type,
		.shuffle-layout .entry-meta,
		.site-description,
		.widget input[type="email"],
		.widget input[type="password"],
		.widget input[type="search"],
		.widget input[type="tel"],
		.widget input[type="text"],
		.widget input[type="url"],
		.widget select,
		.widget textarea,
		.widget-title,
		.widget-title label,
		body #jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post .jp-relatedposts-post-context,
		body #jp-relatedposts .jp-relatedposts-items .jp-relatedposts-post .jp-relatedposts-post-date,
		div.sharedaddy .sd-block h3.sd-title,
		.author-title',
		array(
			array( 'property' => 'font-size', 'value' => '11px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#eu-cookie-law input[type="submit"],
		#eu-cookie-law input[type="submit"]:focus,
		#eu-cookie-law input[type="submit"]:hover,
		.error404 .page-content > p,
		.featured-slider .slick-dots,
		.featured-slider .slick-dots button,
		.gallery-count,
		.menu-social-container .social-menu-trig:before,
		.menu-social-container ul,
		.nav-menu .sub-menu li,
		.scroll-down,
		.scroll-up,
		.search-no-results .page-content > p,
		.widget_calendar tbody,
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		small',
		array(
			array( 'property' => 'font-size', 'value' => '12px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.entry-footer,
		.nav-menu > li,
		.read-more-link,
		.site-info,
		.wp-caption-text,
		div#eu-cookie-law',
		array(
			array( 'property' => 'font-size', 'value' => '13px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.archive.author .page-title,
		.archive.category .page-title,
		.archive.date .page-title,
		.archive.tag .page-title,
		.archive.tag .page-title,
		.search-results .page-title,
		.tax-jetpack-portfolio-tag .page-title',
		array(
			array( 'property' => 'font-size', 'value' => '13px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.author-info p,
		.category-filter a,
		.comment-notes,
		.logged-in-as,
		.single .format-quote blockquote + cite,
		.single .format-quote blockquote + p cite,
		.single .format-quote blockquote cite,
		.single .format-quote q + cite,
		.single .format-quote q + p cite,
		.single .format-quote q cite,
		.widget,
		.widget p,
		.widget_calendar tfoot,
		.widget_calendar thead,
		blockquote + cite,
		blockquote + p cite,
		blockquote cite,
		body .slideshow-window div.slideshow-controls a:nth-of-type(2),
		q + cite,
		q + p cite,
		q cite',
		array(
			array( 'property' => 'font-size', 'value' => '14px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.widget-grofile h4,
		h6',
		array(
			array( 'property' => 'font-size', 'value' => '14px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-content > p,
		.comment-content dd,
		.comment-content li,
		.comment-content table,
		label',
		array(
			array( 'property' => 'font-size', 'value' => '15px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.author-name,
		.author-title span,
		.widget_wpcom_social_media_icons_widget .genericon,
		body',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'body #jp-relatedposts .jp-relatedposts-items-visual h4.jp-relatedposts-post-title',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.entry-content li,
		.twocolumn p.half-width,
		p',
		array(
			array( 'property' => 'font-size', 'value' => '17px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'h5',
		array(
			array( 'property' => 'font-size', 'value' => '18px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.bypostauthor > .comment-body .comment-author b:after',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.error-404 input[type="search"],
		.headline-template .hero,
		.headline-template .hero p,
		.no-results input[type="search"],
		.paging-navigation .dots,
		.single-jetpack-portfolio .hero,
		.single-jetpack-portfolio .hero p,
		.site-header input[type="search"],
		blockquote,
		blockquote p,
		body #infinite-handle span,
		q',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'#big-search-trigger i',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.masonry .jetpack-portfolio .entry-title,
		.shuffle-layout .portfolio-item .entry-title',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.sidebar-trigger i',
		array(
			array( 'property' => 'font-size', 'value' => '22px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.comments-title span,
		body #jp-relatedposts h3.jp-relatedposts-headline',
		array(
			array( 'property' => 'font-size', 'value' => '22px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.error-404 .search-form:before,
		.no-results .search-form:before,
		.site-header .search-form:before',
		array(
			array( 'property' => 'font-size', 'value' => '24px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.single .format-quote blockquote,
		.single .format-quote blockquote p,
		.single .format-quote q',
		array(
			array( 'property' => 'font-size', 'value' => '24px' ),
		)
	);


	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.comment-reply-title,
		.comments-title,
		footer .site-title,
		h4',
		array(
			array( 'property' => 'font-size', 'value' => '24px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.emphasis',
		array(
			array( 'property' => 'font-size', 'value' => '26px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.featured-slider h2',
		array(
			array( 'property' => 'font-size', 'value' => '28px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.error404 .page-title,
		.search-no-results .page-title,
		.site-title,
		.tag.archive .page-title span',
		array(
			array( 'property' => 'font-size', 'value' => '30px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.widget-area .slideshow-controls a:first-of-type,
		.widget-area .slideshow-controls a:last-of-type',
		array(
			array( 'property' => 'font-size', 'value' => '30px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.nav-links a,
		.paging-navigation',
		array(
			array( 'property' => 'font-size', 'value' => '32px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.featured-image a:after',
		array(
			array( 'property' => 'font-size', 'value' => '32px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'h3',
		array(
			array( 'property' => 'font-size', 'value' => '32px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.search .entry-title,
		.tag.archive .entry-title',
		array(
			array( 'property' => 'font-size', 'value' => '36px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.format-iamge .featured-image a:after,
		.format-standard .featured-image a:after',
		array(
			array( 'property' => 'font-size', 'value' => '36px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.listing .format-link .entry-content > p > a:before,
		.listing .format-link .entry-content:before,
		.listing .format-quote blockquote:before,
		.listing .format-quote q:before,
		.single .format-link .entry-content:before,
		.single .format-quote blockquote:before,
		.single .format-quote q:before',
		array(
			array( 'property' => 'font-size', 'value' => '40px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.listing .format-quote blockquote,
		.listing .format-quote blockquote p,
		.listing .format-quote q,
		.single .format-quote blockquote,
		.single .format-quote blockquote p,
		.single .format-quote q',
		array(
			array( 'property' => 'font-size', 'value' => '40px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.listing .entry-title,
		.single .entry-title,
		h2',
		array(
			array( 'property' => 'font-size', 'value' => '40px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.gallery-size-full[data-carousel-extra] .gallery-item .gallery-icon:after,
		.slideshow-controls a:first-of-type,
		.slideshow-controls a:last-of-type',
		array(
			array( 'property' => 'font-size', 'value' => '42px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.slick-arrow:before',
		array(
			array( 'property' => 'font-size', 'value' => '45px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.jp-carousel-next-button span:before,
		.jp-carousel-previous-button span:before',
		array(
			array( 'property' => 'font-size', 'value' => '47px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.nav-links a:after,
		.paging-navigation .next:before,
		.paging-navigation .prev:before',
		array(
			array( 'property' => 'font-size', 'value' => '50px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.archive.author .page-title span,
		.archive.category .page-title span,
		.archive.date .page-title span,
		.archive.tag .page-title span,
		.search .page-title span,
		.tax-jetpack-portfolio-tag .page-title span,
		h1',
		array(
			array( 'property' => 'font-size', 'value' => '50px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.headline-template .hero h1',
		array(
			array( 'property' => 'font-size', 'value' => '54px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.dropcap:before',
		array(
			array( 'property' => 'font-size', 'value' => '100px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.dropcap:before',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'blockquote,
		q',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'blockquote + cite,
		blockquote + p cite,
		blockquote cite,
		q + cite,
		q + p cite,
		q cite',
		array(
			array( 'property' => 'font-style', 'value' => 'normal' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'code,
		kbd,
		tt,
		var',
		array(
			array( 'property' => 'font-family', 'value' => 'Monaco, Consolas, "Andale Mono", "DejaVu Sans Mono", monospace' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'code',
		array(
			array( 'property' => 'font-size', 'value' => '90%' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'big',
		array(
			array( 'property' => 'font-size', 'value' => '125%' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'cite,
		dfn,
		em,
		i',
		array(
			array( 'property' => 'font-style', 'value' => 'italic' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'address',
		array(
			array( 'property' => 'font-style', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'pre',
		array(
			array( 'property' => 'font-family', 'value' => '"Courier 10 Pitch", Courier, monospace' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'sub,
		sup',
		array(
			array( 'property' => 'font-size', 'value' => '75%' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'dt',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'th',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'td',
		array(
			array( 'property' => 'font-weight', 'value' => '300' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.scroll-down,
		.scroll-up',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.scroll-down i,
		.scroll-up i',
		array(
			array( 'property' => 'font-size', 'value' => '45px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.twocolumn .half-width',
		array(
			array( 'property' => 'font-size', 'value' => 'initial' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#eu-cookie-law input[type="submit"],
		button,
		div#respond .comment-form-fields input[type=submit],
		div#respond .form-submit input,
		div#respond .form-submit input#comment-submit,
		div#respond input[type=submit],
		div#respond p.form-submit input[type=submit],
		form#commentform #submit,
		input[type="button"],
		input[type="reset"],
		input[type="submit"]',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#eu-cookie-law input[type="submit"]:focus,
		#eu-cookie-law input[type="submit"]:hover',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.contact-form label.grunion-field-label',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#respond input[type="checkbox"] + label,
		form.contact-form input[type="checkbox"] + label,
		form.contact-form input[type="radio"] + label,
		form.contact-form label.checkbox,
		form.contact-form label.radio,
		input[type="checkbox"] + label,
		input[type="radio"] + label,
		label.checkbox,
		label.radio',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-subscription-form input[type="checkbox"] + label',
		array(
			array( 'property' => 'font-weight', 'value' => '300' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'input[type="checkbox"] + label:before,
		input[type="radio"] + label:before,
		label.checkbox:before,
		label.radio:before',
		array(
			array( 'property' => 'font-size', 'value' => '14px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget .search-form input[type="submit"],
		.widget_blog_subscription input[type="submit"]',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.error-404 input[type="search"],
		.no-results input[type="search"],
		.search-instructions,
		.site-header input[type="search"]',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.site-title',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.author-box a,
		.entry-content a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.entry-content cite a',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.nav-links a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.paging-navigation li',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation a',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation .current-menu-item > a,
		.main-navigation .current_page_ancestor > a,
		.main-navigation .current_page_item > a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.menu-social-container',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.menu-social-container .social-menu-trig:before',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.screen-reader-text:focus',
		array(
			array( 'property' => 'font-size', 'value' => '14px' ),
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.widget-title,
		.widget-title label',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget_calendar tbody',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget_calendar caption,
		.widget_calendar td,
		.widget_calendar th',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#today',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget_recent_comments,
		.widget_recent_entries',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.recentcommentsavatar tbody td a:first-of-type',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget_rss li > a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget_contact_info .confit-address a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget .jetpack-display-remote-posts h4,
		.widget .jetpack-display-remote-posts p',
		array(
			array( 'property' => 'font-size', 'value' => '100%' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widgets-list-layout-links',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.pd_top_rated_holder_posts > p a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#top_posts a',
		array(
			array( 'property' => 'font-weight', 'value' => '500' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget_authors img + strong',
		array(
			array( 'property' => 'font-weight', 'value' => '500' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.entry-content .read-more-link,
		.read-more-link',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.category-list',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-date a',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.listing .format-link .entry-content > p > a,
		.single .format-link .entry-content a',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.gallery-count',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.listing .format-link .category-list a,
		.listing .format-quote .category-list a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.search-post-type',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.featured-slider .slick-dots',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.category-filter a',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.category-filter .cat-active a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.headline-template .hero h1,
		.page-template-portfolio-page .site-content .hero h1',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.split-layout .container > article,
		.split-layout .featured-media',
		array(
			array( 'property' => 'font-size', 'value' => 'initial' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-notes,
		.logged-in-as',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'div#respond',
		array(
			array( 'property' => 'font-family', 'value' => 'inherit' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-form-posting-as strong',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'body #infinite-handle span',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.author-name,
		.author-title',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.author-name span',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.sharedaddy .sd-content ul li a.share-pocket:before',
		array(
			array( 'property' => 'font-size', 'value' => '18px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.sharedaddy .sd-content ul li a.share-email:before,
		.sharedaddy .sd-content ul li a.share-linkedin:before,
		.sharedaddy .sd-content ul li a.share-print:before,
		.sharedaddy .sd-content ul li a.share-reddit:before,
		.widget_wpcom_social_media_icons_widget .genericon-googleplus,
		.widget_wpcom_social_media_icons_widget .genericon-linkedin',
		array(
			array( 'property' => 'font-size', 'value' => '19px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.sharedaddy .sd-content ul li a.share-google-plus-1:before',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.jp-carousel-wrap #jp-carousel-comment-form-button-submit,
		.jp-carousel-wrap .jp-carousel-light #carousel-reblog-box input#carousel-reblog-submit,
		.jp-carousel-wrap textarea#jp-carousel-comment-form-comment-field,
		body .jp-carousel-wrap',
		array(
			array( 'property' => 'font-family', 'value' => 'geomanist, Helvetica Neue, Helvetica, Arial, sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'body #jp-relatedposts h3.jp-relatedposts-headline em',
		array(
			array( 'property' => 'font-style', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'body div#jp-relatedposts div.jp-relatedposts-items .jp-relatedposts-post .jp-relatedposts-post-title a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.sd-rating .sd-title,
		body div.sharedaddy .sd-social h3.sd-title,
		body div.sharedaddy h3.sd-title',
		array(
			array( 'property' => 'font-weight', 'value' => '500' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu .dropdown-toggle',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu .site-header nav',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu #menuMarker',
		array(
			array( 'property' => 'font-size', 'value' => '18px' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu .nav-menu > li',
		array(
			array( 'property' => 'font-size', 'value' => '30px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu .main-navigation .nav-menu .current-menu-item > .dropdown-toggle,
		.hamburger-menu .main-navigation .nav-menu .current-menu-item > a,
		.hamburger-menu .main-navigation .nav-menu .current_page_ancestor > .dropdown-toggle,
		.hamburger-menu .main-navigation .nav-menu .current_page_ancestor > a,
		.hamburger-menu .main-navigation .nav-menu .current_page_item > .dropdown-toggle,
		.hamburger-menu .main-navigation .nav-menu .current_page_item > a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu .nav-menu .sub-menu li',
		array(
			array( 'property' => 'font-size', 'value' => '14px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.hamburger-menu .nav-menu .sub-menu li .dropdown-toggle',
		array(
			array( 'property' => 'font-size', 'value' => '11px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.headline-template .hero h1',
		array(
			array( 'property' => 'font-size', 'value' => '50px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.menu-toggle',
		array(
			array( 'property' => 'font-size', 'value' => '0' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.menu-toggle i',
		array(
			array( 'property' => 'font-size', 'value' => '10px' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.dropdown-toggle',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.site-header nav',
		array(
			array( 'property' => 'font-size', 'value' => '20px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.nav-menu .main-navigation .current-menu-item > a,
		.nav-menu .main-navigation .current_page_ancestor > a,
		.nav-menu .main-navigation .current_page_item > a',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.nav-menu .sub-menu li',
		array(
			array( 'property' => 'font-size', 'value' => '14px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.nav-menu .sub-menu li .dropdown-toggle',
		array(
			array( 'property' => 'font-size', 'value' => '11px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation #menuMarker',
		array(
			array( 'property' => 'font-size', 'value' => '18px' ),
			array( 'property' => 'font-style', 'value' => 'normal' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.featured-slider h2',
		array(
			array( 'property' => 'font-size', 'value' => '24px' ),
		),
		array(
			'(min-width: 768px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.featured-slider .slick-dots li button',
		array(
			array( 'property' => 'font-size', 'value' => '0' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.featured-slider .slick-dots',
		array(
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.featured-slider .slick-dots li button:before',
		array(
			array( 'property' => 'font-size', 'value' => '15px' ),
		)
	);

	return $category_rules;
} );
