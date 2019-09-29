<?php
/**
 * Customizer sanitization functions
 *
 * @package Eris
 */

/**
 * Sanitize checkbox
 */
function eris_sanitize_checkbox( $checkbox ) {
    if ( $checkbox ) {
        $checkbox = 1;
    } else {
        $checkbox = false;
    }
    return $checkbox;
}

/**
 * Sanitize portfolio layout radio inputs
 */
function eris_sanitize_portfolio_layout( $selection ) {
	if ( !in_array( $selection, array( 'shuffle', 'three-columns', 'four-columns' ) ) ) {
		$selection = 'shuffle';
	} else {
		return $selection;
	}
}

/**
 * Sanitize portfolio header radio inputs
 */
function eris_sanitize_portfolio_header( $selection ) {
    if ( !in_array( $selection, array( 'default', 'featured-slider', 'excerpt-title' ) ) ) {
        $selection = 'default';
    } else {
        return $selection;
    }
}

