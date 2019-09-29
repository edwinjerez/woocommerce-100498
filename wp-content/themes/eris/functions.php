<?php
/**
 * eris functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Eris
 */

if ( ! function_exists( 'eris_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function eris_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on eris, use a find and replace
	 * to change 'eris' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'eris', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Add support for custom logo
	add_theme_support( 'custom-logo', array(
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// Image sizes
	add_image_size( 'eris-archive-image', 2200, 99999, false );
	add_image_size( 'eris-archive-image-portrait', 550, 99999, false );
	add_image_size( 'eris-search-image', 160, 99999, false );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'menu-1' => esc_html__( 'Header', 'eris' ),
		'menu-2' => esc_html__( 'Social menu', 'eris' )
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'video',
		'gallery',
		'quote',
		'link'
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'eris_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add editor style
	add_editor_style( array( 'assets/css/editor-style.css', eris_font_url() ) );
}
endif;
add_action( 'after_setup_theme', 'eris_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function eris_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'eris_content_width', 1100 );
}
add_action( 'after_setup_theme', 'eris_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function eris_widgets_init() {

	// Define sidebars
	$sidebars = array(
		'sidebar-1' => esc_html__( 'Sidebar', 'eris' ),
		'sidebar-2' => esc_html__( 'Footer Widgets 1', 'eris' ),
		'sidebar-3' => esc_html__( 'Footer Widgets 2', 'eris' )
	);

	// Loop through each sidebar and register
	foreach ( $sidebars as $sidebar_id => $sidebar_name ) {
		register_sidebar( array(
			'name'          => $sidebar_name,
			'id'            => $sidebar_id,
			'description'   => sprintf ( esc_html__( 'Widget area for %s', 'eris' ), $sidebar_name ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}

}
add_action( 'widgets_init', 'eris_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function eris_scripts() {

	// Load Font face
	wp_enqueue_style( 'eris-fonts', eris_font_url() );

	// Main theme style
	wp_enqueue_style( 'eris-style', get_stylesheet_uri() );
	wp_enqueue_style( 'thickbox' );

	// Theme scripts
	wp_enqueue_script( 'eris-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'eris-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'eris-slick-slider', get_template_directory_uri() . '/assets/js/slick/slick.js', array(), false, true );

	if ( eris_is_split_layout() ) {
		wp_enqueue_script( 'scroll-to-fixed', get_template_directory_uri() . '/assets/js/scroll-to-fixed/scrolltofixed.js', array( 'jquery' ), false, true );
	}

	// Main JS file
	wp_enqueue_script( 'eris-call-scripts', get_template_directory_uri() . '/assets/js/common.js', array( 'jquery', 'jquery-effects-core', 'masonry' ), false, true );
}
add_action( 'wp_enqueue_scripts', 'eris_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load font URLs
 */
require get_template_directory() . '/inc/fonts-enqueue.php';


