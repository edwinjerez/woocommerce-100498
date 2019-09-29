(function($){

	var wcf_offer_button_action = function() {
		
		console.log( 'Offer Action' );

		$('a[href*="wcf-up-offer"], a[href*="wcf-down-offer"]').each(function(e) {

			var $this 	= $(this), 
				href 	= $this.attr('href');

			if ( href.indexOf( 'wcf-up-offer-yes' ) !== -1 ) {
				$this.attr( 'id', 'wcf-upsell-offer' );
			}

			if ( href.indexOf( 'wcf-down-offer-yes' ) !== -1 ) {
				$this.attr( 'id', 'wcf-downsell-offer' );
			}
		});
		
		$(document).on( 'click', 'a[href*="wcf-up-offer"], a[href*="wcf-down-offer"]', function(e) {
			
			e.preventDefault();

			console.log( $(this) );

			var $this 			= $(this), 
				href			= $this.attr('href'),
				offer_action 	= 'yes',
				offer_type		= 'upsell',
				step_id 		= cartflows_offer.step_id,
				product_id 		= cartflows_offer.product_id,
				order_id 		= cartflows_offer.order_id,
				order_key 		= cartflows_offer.order_key;

			if ( href.indexOf( 'wcf-up-offer' ) !== -1 ) {
				
				offer_type = 'upsell';

				if ( href.indexOf( 'wcf-up-offer-yes' ) !== -1 ) {
					offer_action = 'yes';
				}else{
					offer_action = 'no';
				}
			}

			if ( href.indexOf( 'wcf-down-offer' ) !== -1 ) {

				offer_type = 'downsell';

				if ( href.indexOf( 'wcf-down-offer-yes' ) !== -1 ) {
					offer_action = 'yes';
				}else{
					offer_action = 'no';
				}
			}

			if ( 'yes' === cartflows_offer.skip_offer && 'yes' === offer_action ) {
				return;
			}

			$( 'body' ).trigger( 'wcf-show-loader' );

			if ( 'yes' === offer_action ) {
				action = 'wcf_' + offer_type + '_accepted';
			}else{
				action = 'wcf_' + offer_type + '_rejected';
			}

			$.ajax({
	            url: cartflows.ajax_url,
				data: {
					action: action,
					offer_action: offer_action,
					step_id: step_id,
					product_id: product_id,
					order_id: order_id,
					order_key: order_key,
				},
				dataType: 'json',
				type: 'POST',
				success: function ( data ) {
					
					var msg = data.message;
					var msg_class = 'wcf-payment-' + data.status;

					$( "body").trigger( "wcf-update-msg", [ msg, msg_class ] );

					setTimeout(function() {
						window.location.href = data.redirect;
					}, 500);
				}
			});

			return false;
		});

		/* Will Remove later */
		$(document).on( 'click', '.wcf-upsell-offer, .wcf-downsell-offer', function(e) {
			
			e.preventDefault();

			var $this 			= $(this), 
				offer_action 	= $this.data('action'),
				offer_type		= 'upsell',
				step_id 		= $this.data('step'),
				product_id 		= $this.data('product'),
				order_id 		= $this.data('order'),
				order_key 		= $this.data('order_key');
			
			if ( $this.hasClass( 'cartflows-skip' ) && 'yes' === offer_action ) {
				return;
			}
			
			$( 'body' ).trigger( 'wcf-show-loader' );

			if ( $this.hasClass('wcf-downsell-offer') ) {
				offer_type = 'downsell';
			}

			if ( 'yes' === offer_action ) {
				action = 'wcf_' + offer_type + '_accepted';
			}else{
				action = 'wcf_' + offer_type + '_rejected';
			}

			$.ajax({
	            url: cartflows.ajax_url,
				data: {
					action: action,
					offer_action: offer_action,
					step_id: step_id,
					product_id: product_id,
					order_id: order_id,
					order_key: order_key,
				},
				dataType: 'json',
				type: 'POST',
				success: function ( data ) {
					
					var msg = data.message;
					var msg_class = 'wcf-payment-' + data.status;

					$( "body").trigger( "wcf-update-msg", [ msg, msg_class ] );

					setTimeout(function() {
						window.location.href = data.redirect;
					}, 500);
				}
			});

			return false;
		});
		/* Will Remove later */
	}

	$(document).ready(function($) {

		$( "body" ).on( "wcf-show-loader", function( event ) {
			console.log('Show Loader');
			$('.wcf-loader-bg').addClass('show');
		});

		$( "body" ).on( "wcf-hide-loader", function( event ) {
			console.log('Hide Loader');
			$('.wcf-loader-bg').removeClass('show');
		});

		$( "body" ).on( "wcf-update-msg", function( event, msg, msg_class ) {
			$('.wcf-order-msg .wcf-process-msg').text( msg ).addClass( msg_class );
		});

		wcf_offer_button_action();
	});
})(jQuery);