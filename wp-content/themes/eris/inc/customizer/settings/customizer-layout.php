<?php
/**
 * Customization of theme layout
 *
 * @package Eris
 */

/**
 * Section
 */
$wp_customize->add_section( 'layout_settings', array(
    'title'    => esc_html__( 'Portfolio Layout Settings', 'eris' ),
    'priority' => 120,
    'panel'    => 'theme_options_panel'
) );

/**
 * Settings
 */

// Portfolio template layout
$wp_customize->add_setting( 'portfolio_layout_setting', array(
    'default'           => 'shuffle',
    'sanitize_callback' => 'eris_sanitize_portfolio_layout'
) );

$wp_customize->add_control( 'portfolio_layout_setting', array(
    'label'       => esc_html__( 'Portfolio layout', 'eris' ),
    'priority'    => 0,
    'section'     => 'layout_settings',
    'type'        => 'radio',
    'choices'     => array(
    	'shuffle'       => esc_html__( 'Two-column layout', 'eris' ),
        'three-columns' => esc_html__( 'Three-column layout', 'eris' ),
        'four-columns'  => esc_html__( 'Four-column layout', 'eris' ),
    ),
) );

// Single project split layout
$wp_customize->add_setting( 'single_project_layout_setting', array(
    'default'           => 0,
    'sanitize_callback' => 'eris_sanitize_checkbox'
) );

$wp_customize->add_control( 'single_project_layout_setting', array(
    'label'       => esc_html__( 'Display single project in Split Layout', 'eris' ),
    'description' => esc_html__( 'The Split layout puts your text on one side and images on the other. Useful for storytelling, when you want the text to run alongside the images.', 'eris' ),
    'priority'    => 0,
    'section'     => 'layout_settings',
    'type'        => 'checkbox'
) );


