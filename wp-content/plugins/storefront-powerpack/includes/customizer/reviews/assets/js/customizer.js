/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	wp.customize( 'sr_heading_text', function( value ) {
		value.bind( function( to ) {
			$( '.storefront-reviews .section-title span' ).text( to );
		} );
	} );
} )( jQuery );
