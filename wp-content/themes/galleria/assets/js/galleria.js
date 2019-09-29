/**
 * galleria.js
 *
 * Javascript used by the Galleria theme.
 */
( function() {
	jQuery( window ).load( function() {
		jQuery( 'body' ).addClass( 'loaded' );
		jQuery( '.preloader-enabled .site' ).addClass( 'animated fadeIn' );
		jQuery( '.checkout-button' ).addClass( 'animated bounce' );

		// The star rating on single product pages
		var value = jQuery( '.star-rating > span' ).width();
		jQuery( '.woocommerce-product-rating .star-rating > span' ).css( 'width', 0 );
		jQuery( '.woocommerce-product-rating .star-rating > span' ).animate({
			width: value
		}, 1500, function() {
		// Animation complete.
		});

		if ( jQuery( window ).width() > 767 ) {
			// Product category title
			jQuery( 'ul.products li.product-category .g-product-title' ).each(function() {
				var title 	= jQuery( this );
				var height 	= title.outerHeight();

				title.css( 'margin-top', -height/2 );
			});
		}
	});
} )();
