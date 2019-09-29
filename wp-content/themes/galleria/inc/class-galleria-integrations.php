<?php
/**
 * Galleria_Integrations Class
 *
 * @author   WooThemes
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Galleria_Integrations' ) ) :

class Galleria_Integrations {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'after_switch_theme', 				array( $this, 'set_theme_mods' ) );
		add_action( 'customize_register', 				array( $this, 'edit_controls' ), 99 );
	}

	/**
	 * Remove unused/incompatible controls
	 * @return void
	 */
	public function edit_controls( $wp_customize ) {
		$wp_customize->remove_control( 'sd_header_layout' );
		$wp_customize->remove_control( 'sd_button_flat' );
		$wp_customize->remove_control( 'sd_button_shadows' );
		$wp_customize->remove_control( 'sd_button_background_style' );
		$wp_customize->remove_control( 'sd_button_rounded' );
		$wp_customize->remove_control( 'sd_button_size' );
		$wp_customize->remove_control( 'sd_header_layout_divider_after' );
		$wp_customize->remove_control( 'sd_button_divider_1' );
		$wp_customize->remove_control( 'sd_button_divider_2' );
	}

	/**
	 * Set / remove theme mods that are incompatible with this theme
	 * @return void
	 */
	public function set_theme_mods() {
		remove_theme_mod( 'sd_header_layout' );
		remove_theme_mod( 'sd_button_flat' );
		remove_theme_mod( 'sd_button_shadows' );
		remove_theme_mod( 'sd_button_background_style' );
		remove_theme_mod( 'sd_button_rounded' );
		remove_theme_mod( 'sd_button_size' );
		remove_theme_mod( 'storefront_footer_background_color' );
	}
}

endif;

return new Galleria_Integrations();