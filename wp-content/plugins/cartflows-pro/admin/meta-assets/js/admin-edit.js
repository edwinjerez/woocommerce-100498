(function($){

	/* Bump Order Fields Hide / Show */
	var wcf_bump_order_fields_events = function() {

		/* Ready */
		wcf_bump_order_fields();

		/* Change Order Bump*/
		$('.wcf-checkout-table .field-wcf-order-bump input:checkbox').on('change', function(e) {
			wcf_bump_order_fields();
		});
		
		/* Change Discount Type*/
		$('.wcf-checkout-table .field-wcf-order-bump-discount select').on('change', function(e) {
			wcf_bump_order_fields();
		});
	}

	var wcf_custom_fields_select_option_events = function() {

		/* Ready */
		wcf_custom_fields_select_option();

		/* Change Custom Field*/
		$('.wcf-column-right .wcf-checkout-custom-fields .wcf-custom-field-box .wcf-cpf-type select').on('change', function(e) {
			wcf_custom_fields_select_option();
		});
	}


	var wcf_custom_fields_select_option = function() {
		var wrap 					= $('.wcf-checkout-table'),
			data_key				= wrap.find('.wcf-column-right .wcf-checkout-custom-fields .wcf-custom-field-box .wcf-cpf-wrap .wcf-cpf-row').attr('data-key');
			select_field 			= wrap.find('.wcf-column-right .wcf-checkout-custom-fields .wcf-custom-field-box .wcf-cpf-type select');
			select_value			= select_field.val();
			option_field			= wrap.find('.wcf-column-right .wcf-checkout-custom-fields .wcf-custom-field-box .wcf-cpf-options');
			placeholder_field		= wrap.find('.wcf-column-right .wcf-checkout-custom-fields .wcf-custom-field-box .wcf-cpf-placeholder');

			default_field			= wrap.find('.wcf-column-right .wcf-checkout-custom-fields .wcf-custom-field-box .wcf-cpf-default .wcf-cpf-row-setting-field');
			add_default_checkbox_select_field	= '<select name="wcf-checkout-custom-fields['+ data_key +'][default]" class="wcf-cpf-default-checkbox"><option value="1">Checked</option><option value="0">Un-Checked</option></select>';
			add_default_field		= '<input type="text" value="" name="wcf-checkout-custom-fields['+ data_key +'][default]" class="wcf-cpf-default"><span id="wcf-cpf-default-error-msg"></span>';


			if( 'select' == select_value ){
				option_field.show();
				placeholder_field.hide();
				default_field.empty();
				default_field.append(add_default_field);
			}else if( 'checkbox' == select_value ){
				placeholder_field.hide();
				option_field.hide();
				default_field.empty();
				default_field.append(add_default_checkbox_select_field);
			}else if( 'hidden' == select_value ){
				placeholder_field.hide();
				option_field.hide();
				default_field.empty();
				default_field.append(add_default_field);
			}else{
				option_field.hide();
				placeholder_field.show();
				default_field.empty();
				default_field.append(add_default_field);
			}
	}

	var wcf_bump_order_fields = function() {

		var wrap 		= $('.wcf-checkout-table'),
			bump_order 	= wrap.find('.field-wcf-order-bump input:checkbox');

		var field_names = [
			'.field-wcf-order-bump-style',
			'.field-wcf-order-bump-product',
			'.field-wcf-order-bump-position',
			'.field-wcf-order-bump-image',
			'.field-wcf-order-bump-label',
			'.field-wcf-order-bump-hl-text',
			'.field-wcf-order-bump-desc',
			'.field-wcf-order-bump-discount',
			'.field-wcf-order-bump-discount-value',
			'.field-wcf-order-bump-discount-coupon',
			'.wcf-cs-bump-options',
			'.wcf-bump-price-notice',
			'.field-wcf-bump-original-price',
			'.field-wcf-bump-discount-price',
		];

		if ( bump_order.is(":checked") ) {
			$.each( field_names, function(i, val) {
				
				if ( '.field-wcf-order-bump-discount-value' === val ) {
					var discount_type = wrap.find('.field-wcf-order-bump-discount select').val();
					
					if ( 'discount_percent' === discount_type || 'discount_price' === discount_type ) {
						wrap.find( val ).show();
					}else{
						wrap.find( val ).hide();
					}
				}else if ( '.field-wcf-order-bump-discount-coupon' === val ){
					var discount_type = wrap.find('.field-wcf-order-bump-discount select').val();

					if ( 'coupon' === discount_type ) {
						wrap.find( val ).show();
					}else{
						wrap.find( val ).hide();
					}
				}else{
					wrap.find( val ).show();
				}
			})
		} else {
			$.each( field_names, function(i, val) {
				wrap.find( val ).hide();
			});
		}
	}

	var wcf_offer_fields_events = function() {
		
		/* Ready */
		wcf_offer_fields();

		/* Change Discount Type*/
		$('.wcf-offer-table .field-wcf-offer-discount select').on('change', function(e) {
			wcf_offer_fields();
		});
	}

	var wcf_offer_fields = function() {

		var wrap 	= $('.wcf-offer-table');

		if ( wrap.length < 1 ) {
			return;
		}

		var field_names = [
			'.field-wcf-offer-discount-value',
		];

		$.each( field_names, function(i, val) {

			if ( '.field-wcf-offer-discount-value' === val ) {
				
				var discount_type = wrap.find('.field-wcf-offer-discount select').val();
				
				if ( 'discount_percent' === discount_type || 'discount_price' === discount_type ) {
					wrap.find( val ).show();
				}else{
					wrap.find( val ).hide();
				}
			}
		});
		
	}


	var wcf_field_reordering = function() {

		var billing_field_container = $(".wcf-cb-fields"),
			shipping_field_container = $(".wcf-sb-fields");
		// For Billing Fields
		$( "#wcf-billing-field-sortable" ).sortable({
			forcePlaceholderSize: true,
			placeholder: "sortable-placeholder"
		});
		$( "#wcf-billing-field-sortable" ).disableSelection();
		
		// For Shipping Fields
		$( "#wcf-shipping-field-sortable" ).sortable({
			forcePlaceholderSize: true,
			placeholder: "sortable-placeholder"
		});
		
		$( "#wcf-shipping-field-sortable" ).disableSelection();

		$(this).closest('.wcf-field-item').toggleClass('wcf-field-item-edit-active');
		$(this).closest('.wcf-field-item').siblings().removeClass('wcf-field-item-edit-active');
		var action_btn = $(this).closest('.wcf-field-item').find('.item-edit');
			action_btn.removeClass('.item-edit');
			action_btn.addClass('item-edit-close');
			
		

		// shipping_field_container.find( '.item-controls' ).delegate( ".item-edit", 'click', function( e ) {
		// 	e.preventDefault();
		// 	e.stopPropagation();
			
		// 	$(this).closest('.wcf-field-item').toggleClass('wcf-field-item-edit-active');	
		// 	$(this).closest('.wcf-field-item').siblings().removeClass('wcf-field-item-edit-active');
		// } );

	}

	var check_uncheck_custom_field = function(){

		var billing_cf_container = $("#wcf-billing-field-sortable"),
			shipping_cf_container = $("#wcf-shipping-field-sortable");

			
				var get_label_class = $(this).closest('label').hasClass('dashicons-visibility'),
					get_label 		= $(this).closest('label'),
					get_item_bar	= get_label.closest('.wcf-field-item-bar');
					
				if( get_label_class ){
					$(this).closest('label').removeClass('dashicons-visibility');
					$(this).closest('label').addClass('dashicons-hidden ');
					get_item_bar.addClass('disable');
				}else{
					$(this).closest('label').removeClass('dashicons-hidden ');
					$(this).closest('label').addClass('dashicons-visibility');
					get_item_bar.removeClass('disable');
				}
			

			// shipping_cf_container.find( '.wcf-field-item-handle' ).delegate( "label", "click", function( e ) {
			// 	var get_label_class = $(this).closest('label').hasClass('dashicons-visibility'),
			// 		get_label 		= $(this).closest('label'),
			// 		get_item_bar	= get_label.closest('.wcf-field-item-bar');
				
			// 	if( get_label_class ){

			// 		$(this).closest('label').removeClass('dashicons-visibility');
			// 		$(this).closest('label').addClass('dashicons-hidden ');
			// 		get_item_bar.addClass('disable');
			// 	}else{
			// 		$(this).closest('label').removeClass('dashicons-hidden');
			// 		$(this).closest('label').addClass('dashicons-visibility');
			// 		get_item_bar.removeClass('disable');
			// 	}
			// } );
}

	/* Hide / Show Product Variation Fields Event */
	var wcf_product_variation_fields_events = function() {

		/* Ready */
		wcf_product_variation_fields();

		/* Change product variation Fields*/
		$('.wcf-column-right .wcf-checkout-general .wcf-pv-checkboxes .field-wcf-enable-product-options input:checkbox').on('change', function(e) {
			wcf_product_variation_fields();
		});

		$('.wcf-column-right .wcf-checkout-general .wcf-pv-fields .field-wcf-enable-product-variation input:checkbox').on('change', function(e) {
			wcf_product_variation_fields();
		});

	}

	/* Disable/Enable Product Variation Field section */
	var wcf_product_variation_fields = function() {

		var wrap 			= $('.wcf-checkout-table'),
			custom_fields 	= wrap.find('.wcf-column-right .wcf-checkout-general .wcf-pv-checkboxes .field-wcf-enable-product-options input:checkbox');

		var field_names = [
			'.wcf-pv-fields',
		];

		var pv_options_chkbox = wrap.find('.wcf-column-right .wcf-checkout-general .wcf-pv-fields .field-wcf-enable-product-variation input:checkbox');
		
		var pv_options = [
			'.field-wcf-product-variation-options',
		];

		if ( pv_options_chkbox.is(":checked") ) {
			$.each( pv_options, function(i, val) {
				wrap.find( val ).show();
			})
		} else {
			$.each( pv_options, function(i, val) {
				wrap.find( val ).hide();
			});
		}

		if ( custom_fields.is(":checked") ) {
			$.each( field_names, function(i, val) {
				wrap.find( val ).show();
			})
		} else {
			$.each( field_names, function(i, val) {
				wrap.find( val ).hide();
			});
		}

	}

	/* Advance Style Fields Hide / Show */
	var wcf_pro_advance_style_fields_events = function() {

		/* Ready */
		wcf_two_step_style_fields();

		wcf_pro_checkout_note_box();

		/* Select Advance Style Field */
		$('.wcf-column-right .wcf-checkout-style .wcf-cs-fields .wcf-cs-checkbox-field .field-wcf-checkout-layout select[name="wcf-checkout-layout"]').change(function(e) {
			wcf_two_step_style_fields();
		});

		/* Change Advance Style Field */
		$('.wcf-thankyou-table [name="wcf-tq-advance-options-fields"]').on('change', function(e) {
			wcf_thankyou_advance_style_fields();
		});

		/* Hide/Show checkout note box on check of enable checkout note checkbox */
		$('.wcf-column-right .wcf-checkout-two-step .field-wcf-checkout-box-note input:checkbox').on('change', function(e) {
			wcf_pro_checkout_note_box();
		});
	}

	/* Disable/Enable Pro Advance Style Field section for the two step */
	var wcf_two_step_style_fields = function() {

		var wrap 			= $('.wcf-checkout-table');

		var field_names = [
			'.wcf-checkout-two-step',
		];

		var val = $('select[name="wcf-checkout-layout"]').val();
		
		if ( val == 'two-step' ) {
			$.each( field_names, function(i, val) {
				// wrap.find( val ).show();
				wrap.find( "[data-tab=wcf-checkout-two-step]" ).show();
			})
		} else {
			$.each( field_names, function(i, val) {
				// wrap.find( val ).hide();
				wrap.find( "[data-tab=wcf-checkout-two-step]" ).hide();
			});
		}
	}
	/* Show/Hide Note text box */
	var wcf_pro_checkout_note_box = function() {

		var wrap 			= $('.wcf-checkout-table'),
			custom_fields 	= wrap.find('.wcf-column-right .wcf-checkout-two-step .field-wcf-checkout-box-note input:checkbox');

		var field_names = [
			'.field-wcf-checkout-box-note-text',
			'.field-wcf-checkout-box-note-text-color',
			'.field-wcf-checkout-box-note-bg-color',
		];

		if ( custom_fields.is(":checked") ) {
			$.each( field_names, function(i, val) {
				wrap.find( val ).show();
			})
		} else {
			$.each( field_names, function(i, val) {
				wrap.find( val ).hide();
			});
		}
	}

	/* Bump Order Fields Hide / Show */
	var wcf_pre_checkout_offer_fields_events = function() {

		/* Ready */
		wcf_pre_checkout_offer_fields();

		/* Change Pre-checkout Offer*/
		$('.wcf-checkout-table .field-wcf-pre-checkout-offer input:checkbox').on('change', function(e) {
			wcf_pre_checkout_offer_fields();
		});

		/* Change Discount Type*/
		$('.wcf-checkout-table .field-wcf-pre-checkout-offer-discount select').on('change', function(e) {
			wcf_pre_checkout_offer_fields();
		});
	}

		var wcf_pre_checkout_offer_fields = function() {

		var wrap 		= $('.wcf-checkout-table'),
			pre_checkout_offer 	= wrap.find('.field-wcf-pre-checkout-offer input:checkbox');

		var field_names = [
			'.field-wcf-pre-checkout-offer-product',
			'.field-wcf-pre-checkout-offer-product-title',
			'.field-wcf-pre-checkout-offer-desc',
			'.field-wcf-pre-checkout-offer-popup-title',
			'.field-wcf-pre-checkout-offer-popup-sub-title',
			'.field-wcf-pre-checkout-offer-discount',
			'.field-wcf-pre-checkout-offer-discount-value',
			'.wcf-pre-checkout-offer-price-notice',
			'.field-wcf-pre-checkout-offer-original-price',
			'.field-wcf-pre-checkout-offer-discount-price',
			'.field-wcf-pre-checkout-offer-popup-btn-text',
			'.field-wcf-pre-checkout-offer-popup-skip-btn-text',
			'.field-wcf-pre-checkout-offer-bg-color',
			'.wcf-cs-pre-checkout-offer-options',
		];

		if ( pre_checkout_offer.is(":checked") ) {
			$.each( field_names, function(i, val) {

				if ( '.field-wcf-pre-checkout-offer-discount-value' === val ) {
				
					var discount_type = wrap.find('.field-wcf-pre-checkout-offer-discount select').val();
				
					if ( 'discount_percent' === discount_type || 'discount_price' === discount_type ) {
						wrap.find( val ).show();
					}else{

						wrap.find( val ).hide();
					}
				}else{
					wrap.find( val ).show();

				}
				
				
			})
		} else {
			$.each( field_names, function(i, val) {
				wrap.find( val ).hide();
			});
		}
	}

	$(document).ready(function($) {

		/* Bump Order Show Hide Fields */
		wcf_bump_order_fields_events();

		/* Pre-checkout Offer Show Hide Fields */
		wcf_pre_checkout_offer_fields_events();

		wcf_field_reordering();

		/* Custom Fields Show Hide */
		/*wcf_custom_fields_events();*/

		/* Custom Fields Add Text area if the select option is selected */
		wcf_custom_fields_select_option_events();

		/* Hide Show advance options of pro */
		wcf_pro_advance_style_fields_events();

		/* Upsell/Downsell Show Hide Fields */
		wcf_offer_fields_events();

		/* Enable/Disable Product Variations Fields */
		wcf_product_variation_fields_events();

		/* Enable/Disable Product Variations Fields */
		wcf_product_variation_fields_events();

	});

	$( document ).delegate( ".wcf-field-item-edit-inactive .wcf-field-item-handle .item-controls a", 'click', wcf_field_reordering );
	$( document ).delegate( ".wcf-field-item-edit-inactive .wcf-field-item-handle label.dashicons", 'click', check_uncheck_custom_field );

})(jQuery);
