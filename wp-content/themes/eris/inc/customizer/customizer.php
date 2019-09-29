<?php
/**
 * Eris Theme Customizer.
 *
 * @package Eris
 */

// Load Customizer specific functions
require get_template_directory() . '/inc/customizer/functions/customizer-sanitization.php';

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function eris_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';


	/**
     * PANELS
     */
    $wp_customize->add_panel( 'theme_options_panel', array(
        'priority'    => 200,
        'capability'  => 'edit_theme_options',
        'title'       => esc_html__( 'Theme Options', 'eris' ),
        'description' => esc_html__( 'Eris Theme Options', 'eris' )
    ) );


    /**
     * SECTIONS AND SETTINGS
     */

    // Header settings
    require get_template_directory() . '/inc/customizer/settings/customizer-header.php';

    // Layout settings
    require get_template_directory() . '/inc/customizer/settings/customizer-layout.php';


}
add_action( 'customize_register', 'eris_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function eris_customize_preview_js() {
	wp_enqueue_script( 'eris-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'eris_customize_preview_js' );
