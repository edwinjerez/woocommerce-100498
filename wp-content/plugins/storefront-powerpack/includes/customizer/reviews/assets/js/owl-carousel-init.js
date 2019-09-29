/* global carousel_parameters */
jQuery(document).ready( function() {
	jQuery( '.product-reviews' ).each( function() {
		var columns = jQuery( this ).data( 'columns' );

		if ( ! columns ) {
			columns = carousel_parameters.columns;
		}

		jQuery( this ).owlCarousel({
			items:             columns,
			autoHeight:        true,
			itemsDesktop:      [ 1199, columns ],
			itemsDesktopSmall: [ 979, columns ],
			itemsTablet:       [ 768, 1 ],
			itemsMobile:       [ 479, 1 ],
			theme:             'sr-carousel',
			pagination:        false,
			navigation:        true,
			navigationText:    [ carousel_parameters.previous, carousel_parameters.next ]
		});
	});
});