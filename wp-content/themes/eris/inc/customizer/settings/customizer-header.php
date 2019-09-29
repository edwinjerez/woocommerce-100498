<?php
/**
 * Customization of theme header
 *
 * @package Eris
 */

/**
 * Section
 */
$wp_customize->add_section( 'header_settings', array(
    'title'    => esc_html__( 'Header Settings', 'eris' ),
    'priority' => 120,
    'panel'    => 'theme_options_panel'
) );

/**
 * Settings
 */

// Header layout
$wp_customize->add_setting( 'header_display_setting', array(
    'default'           => 0,
    'sanitize_callback' => 'eris_sanitize_checkbox'
));

$wp_customize->add_control( 'header_display_setting', array(
    'label'       => esc_html__( 'Hide header menu behind a button', 'eris' ),
    'priority'    => 0,
    'section'     => 'header_settings',
    'type'        => 'checkbox'
) );

// Sticky header
$wp_customize->add_setting( 'sticky_header_setting', array(
    'default'           => 1,
    'sanitize_callback' => 'eris_sanitize_checkbox'
));

$wp_customize->add_control( 'sticky_header_setting', array(
    'label'    => esc_html__( 'Enable sticky header', 'eris' ),
    'priority' => 1,
    'section'  => 'header_settings',
    'type'     => 'checkbox'
) );

