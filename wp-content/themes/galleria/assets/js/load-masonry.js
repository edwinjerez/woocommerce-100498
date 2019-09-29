/**
 * load-masonry.js
 *
 * Javascript used to load the masonry script.
 */
( function() {
	jQuery( window ).load( function() {
		if ( jQuery( window ).width() > 767 ) {
			if ( jQuery( 'body' ).hasClass( 'galleria-masonry' ) ) {
				jQuery( '.site-main ul.products' ).masonry({
					columnWidth: 'li.product',
					itemSelector: 'li.product',
					percentPosition: true
				});
			}
		}
	});

	// Integration with Jetpack inifinite scroll
	jQuery( document.body ).on( 'post-load', function () {
		if ( jQuery( window ).width() > 767 ) {
			if ( jQuery( 'body' ).hasClass( 'galleria-masonry' ) ) {
				jQuery( '.site-main ul.products' ).masonry({
					columnWidth: 'li.product',
					itemSelector: 'li.product',
					percentPosition: true
				});
			}
		}
	} );
} )();