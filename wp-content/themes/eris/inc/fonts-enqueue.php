<?php
/**
 * Generate Font URLs
 *
 * @package  Eris
 */

 /**
  * Generate the custom font URL
  *
  * @return string
  */
 function eris_font_url() {
    /* Translators: If there are characters in your language that are not
    * supported by SK Modernist, translate this to 'off'. Do not translate
    * into your own language.
    */
    $sk_modernist = esc_html_x( 'on', 'SK Modernist font: on or off', 'eris' );

    if ( 'off' !== $sk_modernist ) {
        return get_stylesheet_directory_uri() . '/assets/fonts/Sk-Modernist/stylesheet.css';
    }

    return '';
 }