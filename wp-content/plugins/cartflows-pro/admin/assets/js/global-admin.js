( function( $ ) {

	var _show_image_field_bump_offer = function(){

		$('.field-wcf-order-bump-style select').on('change', function(e) {

			e.preventDefault();

			var $this 	= $(this),
				selected_value = $this.val();
			
			
			$('.field-wcf-order-bump-image').removeClass("hide");
		});
	}

	var _show_image_field_bump_offer_event = function(){

		var get_wrap  	  = $('.wcf-product-order-bump');
			get_field_row = get_wrap.find('.field-wcf-order-bump-style'),
			get_field     = get_field_row.find('select'),
			get_value     = get_field.val();

		var get_img_field = get_wrap.find('.field-wcf-order-bump-image');

			console.log(get_img_field);

			$('.field-wcf-order-bump-image').removeClass("hide");
	}

	$( document ).ready(function() {
		_show_image_field_bump_offer_event();

		_show_image_field_bump_offer();
	});
	

} )( jQuery ); 