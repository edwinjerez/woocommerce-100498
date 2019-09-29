<?php
/**
 * Checkout post meta
 *
 * @package cartflows
 */

/**
 * Meta Boxes setup
 */
class Cartflows_Pro_Checkout_Meta {



	/**
	 * Instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Meta Option
	 *
	 * @var $meta_option
	 */
	private static $meta_option = null;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'cartflows_checkout_meta_options', array( $this, 'meta_fields' ), 10, 2 );

		add_action( 'cartflows_checkout_general_tab_content', array( $this, 'tab_content_checkout_general' ), 10, 2 );
		add_action( 'cartflows_pre_checkout_offer_tab_content', array( $this, 'tab_content_pre_checkout_offer' ), 10, 2 );
		add_action( 'cartflows_order_bump_tab_content', array( $this, 'tab_content_product_bump' ), 10, 2 );
		add_action( 'cartflows_custom_fields_tab_content', array( $this, 'tab_content_custom_fields' ), 10, 2 );

		// add_action( 'cartflows_checkout_style_tab_content', array( $this, 'tab_style_product_bump' ), 10, 2 );.
		add_filter( 'cartflows_checkout_tabs', array( $this, 'add_two_step_tab' ), 10, 2 );

		add_action( 'cartflows_checkout_tabs_content', array( $this, 'tab_content_checkout_design' ), 10, 2 );
	}

	/**
	 * Bump Order meta fields
	 *
	 * @param array $fields checkout fields.
	 * @param int   $post_id post ID.
	 */
	function meta_fields( $fields, $post_id ) {

		$fields['wcf-enable-product-options'] = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-product-opt-title'] = array(
			'default'  => __( 'Your Products', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);

		/* Product Selection */
		$fields['wcf-product-options'] = array(
			'default'  => 'force-all',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-enable-product-variation'] = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-product-variation-options'] = array(
			'default'  => 'inline',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-enable-product-quantity'] = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-checkout-discount-coupon'] = array(
			'default'  => array(),
			'sanitize' => 'FILTER_CARTFLOWS_ARRAY',
		);

		/* pre-checkout meta fields*/
		$fields['wcf-pre-checkout-offer'] = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-pre-checkout-offer-product']         = array(
			'default'  => array(),
			'sanitize' => 'FILTER_CARTFLOWS_ARRAY',
		);
		$fields['wcf-pre-checkout-offer-desc']            = array(
			'default'  => __( 'Write a few words about this awesome product and tell shoppers why they must get it. You may highlight this as "one time offer" and make it irresistible.', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-popup-title']     = array(
			'default'  => __( '{first_name}, Wait! Your Order Is Almost Complete...', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-popup-sub-title'] = array(
			'default'  => __( 'We have a special one time offer just for you.', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-product-title']   = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-popup-btn-text']  = array(
			'default'  => __( 'Yes, Add to My Order!', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-pre-checkout-offer-popup-skip-btn-text'] = array(
			'default'  => __( 'No, thanks!', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-discount']            = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-discount-value']      = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-pre-checkout-offer-bg-color']            = array(
			'default'  => '#eee',
			'sanitize' => 'FILTER_DEFAULT',
		);

		/* Order Bump Options */
		$fields['wcf-order-bump-style']    = array(
			'default'  => 'default',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump']          = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-position'] = array(
			'default'  => 'after-payment',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-image']    = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-product']  = array(
			'default'  => array(),
			'sanitize' => 'FILTER_CARTFLOWS_ARRAY',
		);

		$fields['wcf-order-bump-label']   = array(
			'default'  => __( 'Yes, I will take it!', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-hl-text'] = array(
			'default'  => __( 'ONE TIME OFFER', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-desc']    = array(
			'default'  => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, quod hic expedita consectetur vitae nulla sint adipisci cupiditate at. Commodi, dolore hic eaque tempora a repudiandae obcaecati deleniti mollitia possimus.', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-order-bump-discount']        = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-discount-value']  = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-order-bump-discount-coupon'] = array(
			'default'  => array(),
			'sanitize' => 'FILTER_CARTFLOWS_ARRAY',
		);

		/* Order Bump Style */
		$fields['wcf-bump-border-color']    = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-border-style']    = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-bg-color']        = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-label-color']     = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-label-bg-color']  = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-desc-text-color'] = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-hl-text-color']   = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-hl-bg-color']     = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-hl-tb-padding']   = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-bump-hl-lr-padding']   = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);

		/* Custom Fields Options*/
		$fields['wcf-show-coupon-field']                = array(
			'default'  => 'yes',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-checkout-additional-fields']       = array(
			'default'  => 'yes',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-advance-options-fields']           = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-custom-checkout-fields']           = array(
			'default'  => 'no',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-shipto-diff-addr-fields']          = array(
			'default'  => 'yes',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf_field_order_billing']              = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_field_order_shipping']             = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_label_text_field_billing']         = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_label_text_field_shipping']        = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_label_default_field_billing']      = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_label_default_field_shipping']     = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_label_placeholder_field_billing']  = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_label_placeholder_field_shipping'] = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_is_required_field_billing']        = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_is_required_field_shipping']       = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_select_option_field_billing']      = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		$fields['wcf_select_option_field_billing']      = array(
			'default'  => '',
			'sanitize' => 'FILTER_CARTFLOWS_CHECKOUT_FIELDS',
		);
		/* Two Step Default Options */

		$fields['wcf-checkout-box-note']      = array(
			'default'  => 'yes',
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-checkout-box-note-text'] = array(
			'default'  => __( 'Get Your FREE copy of CartFlows in just few steps.', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-checkout-box-note-text-color'] = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-checkout-box-note-bg-color'] = array(
			'default'  => '',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-checkout-step-one-title']     = array(
			'default'  => __( 'Shipping', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-checkout-step-one-sub-title'] = array(
			'default'  => __( 'Where to ship it?', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-checkout-step-two-title']         = array(
			'default'  => __( 'Payment', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-checkout-step-two-sub-title']     = array(
			'default'  => __( 'Of your order', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-checkout-offer-button-title']     = array(
			'default'  => __( 'For Special Offer Click Here', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		$fields['wcf-checkout-offer-button-sub-title'] = array(
			'default'  => __( 'Yes! I want this offer!', 'cartflows-pro' ),
			'sanitize' => 'FILTER_DEFAULT',
		);
		// $fields['wcf-checkout-two-step-title-text-color'] = array(
		// 'default'  => '',
		// 'sanitize' => 'FILTER_DEFAULT',
		// );
		// $fields['wcf-checkout-step-bg-color']             = array(
		// 'default'  => '',
		// 'sanitize' => 'FILTER_DEFAULT',
		// );
		// $fields['wcf-checkout-two-step-section-bg-color'] = array(
		// 'default'  => '#ffffff',
		// 'sanitize' => 'FILTER_DEFAULT',
		// );
		// $fields['wcf-checkout-active-step-bg-color']      = array(
		// 'default'  => '',
		// 'sanitize' => 'FILTER_DEFAULT',
		// );
		$fields['wcf-checkout-two-step-section-width'] = array(
			'default'  => '500',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$fields['wcf-checkout-two-step-section-border'] = array(
			'default'  => 'solid',
			'sanitize' => 'FILTER_DEFAULT',
		);

		$billing_fields = Cartflows_Helper::get_checkout_fields( 'billing', $post_id );

		$default_width = '';

		foreach ( $billing_fields as $key => $value ) {
			/* Add default width classes to the fields */
			switch ( $key ) {
				case 'billing_first_name':
				case 'billing_last_name':
				case 'billing_address_1':
				case 'billing_address_2':
					$default_width = '50';
					break;

				case 'billing_city':
				case 'billing_state':
				case 'billing_postcode':
					$default_width = '33';
					break;

				default:
					$default_width = '100';
					break;
			}

			$fields[ 'wcf-' . $key ] = array(
				'default'  => 'yes',
				'sanitize' => 'FILTER_DEFAULT',
			);

			$fields[ 'wcf-field-width_' . $key ] = array(
				'default'  => $default_width,
				'sanitize' => 'FILTER_DEFAULT',
			);
		}

			$shipping_fields = Cartflows_Helper::get_checkout_fields( 'shipping', $post_id );

		foreach ( $shipping_fields as $key => $value ) {
			/* Add default width classes to the fields */
			switch ( $key ) {
				case 'shipping_first_name':
				case 'shipping_last_name':
				case 'shipping_address_1':
				case 'shipping_address_2':
					$default_width = '50';
					break;

				case 'shipping_city':
				case 'shipping_state':
				case 'shipping_postcode':
					$default_width = '33';
					break;

				default:
					$default_width = '100';
					break;
			}

			$fields[ 'wcf-' . $key ] = array(
				'default'  => 'yes',
				'sanitize' => 'FILTER_DEFAULT',
			);

			$fields[ 'wcf-field-width_' . $key ] = array(
				'default'  => $default_width,
				'sanitize' => 'FILTER_DEFAULT',
			);
		}

		return $fields;
	}

	/**
	 * Checkout General Tab Content .
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_content_checkout_general( $options, $post_id ) {

		/* Apply Coupon on Checkout */
		echo wcf()->meta->get_hr_line_field( array() );
		echo wcf()->meta->get_coupon_selection_field(
			array(
				'label'       => __( 'Apply Coupon', 'cartflows-pro' ),
				'name'        => 'wcf-checkout-discount-coupon',
				'value'       => $options['wcf-checkout-discount-coupon'],
				'allow_clear' => true,
			)
		);

		echo wcf()->meta->get_hr_line_field( array() );
		?>
		<div class="wcf-pv-checkboxes">
		<?php

		/*Display Billing Checkout Custom Fields Box*/
		echo wcf()->meta->get_section(
			array(
				'label' => __( 'Product, Variations & Quantity Options', 'cartflows-pro' ),
			)
		);

		echo wcf()->meta->get_checkbox_field(
			array(
				'name'  => 'wcf-enable-product-options',
				'value' => $options['wcf-enable-product-options'],
				'after' => __( 'Enable Product Options', 'cartflows-pro' ),
			)
		);
		?>
		</div>
		<div class="wcf-pv-fields">
		<?php
		/* Your products Title */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Section Title', 'cartflows-pro' ),
				'name'  => 'wcf-product-opt-title',
				'value' => $options['wcf-product-opt-title'],
				'attr'  => array(
					'placeholder' => __( 'Your Products', 'cartflows-pro' ),
				),
			)
		);
		/* Product Options */
		echo wcf()->meta->get_radio_field(
			array(
				// 'label'   => __( 'Product Options', 'cartflows-pro' ),
				'name'    => 'wcf-product-options',
				'value'   => $options['wcf-product-options'],
				'options' => array(
					'force-all'          => __( 'Restrict user to purchase all products', 'cartflows-pro' ),
					'single-selection'   => __( 'Let user select one product from all options', 'cartflows-pro' ),
					'multiple-selection' => __( 'Let user select multiple products from all options', 'cartflows-pro' ),
				),
			)
		);

		echo wcf()->meta->get_checkbox_field(
			array(
				'name'  => 'wcf-enable-product-variation',
				'value' => $options['wcf-enable-product-variation'],
				'after' => __( 'Enable Variations', 'cartflows-pro' ),
			)
		);

		/* Variation Options */
		echo wcf()->meta->get_radio_field(
			array(
				'name'    => 'wcf-product-variation-options',
				'value'   => $options['wcf-product-variation-options'],
				'options' => array(
					'inline' => __( 'Show variations inline', 'cartflows-pro' ),
					'popup'  => __( 'Show variations in popup', 'cartflows-pro' ),
				),
			)
		);

		echo wcf()->meta->get_checkbox_field(
			array(
				'name'  => 'wcf-enable-product-quantity',
				'value' => $options['wcf-enable-product-quantity'],
				'after' => __( 'Enable Quantity', 'cartflows-pro' ),
			)
		);

		?>
		</div>
		<?php
	}

	/**
	 * Tab Content Pre Checkout Upsell.
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_content_pre_checkout_offer( $options, $post_id ) {

		echo wcf()->meta->get_checkbox_field(
			array(
				'name'  => 'wcf-pre-checkout-offer',
				'value' => $options['wcf-pre-checkout-offer'],
				'after' => __( 'Enable Pre-Checkout Upsell', 'cartflows-pro' ),
			)
		);

		/* Pre checkout offer Product Selection Field */
		echo wcf()->meta->get_product_selection_field(
			array(
				'name'        => 'wcf-pre-checkout-offer-product',
				'value'       => $options['wcf-pre-checkout-offer-product'],
				'label'       => __( 'Select Product', 'cartflows-pro' ),
				'help'        => __( 'Select Pre-Checkout Product.', 'cartflows-pro' ),
				'multiple'    => false,
				'allow_clear' => true,
			)
		);

		/* Pre checkout offer Popup Title */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Title Text', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-popup-title',
				'value' => $options['wcf-pre-checkout-offer-popup-title'],
				// 'help'  => __( 'Add title text for Pre-Checkout.', 'cartflows-pro' ),
				'attr'  => array(
					'placeholder' => __( '{first_name}, Wait! Your Order Is Almost Complete...', 'cartflows-pro' ),

				),
			)
		);

		/* Pre checkout offer Popup Subtitle */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Sub-title Text', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-popup-sub-title',
				// 'help'  => __( 'Add sub-title text for Pre-Checkout.', 'cartflows-pro' ),
				'value' => $options['wcf-pre-checkout-offer-popup-sub-title'],

			)
		);

		/* Pre checkout offer Popup Subtitle */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Product Title', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-product-title',
				'help'  => __( 'Enter to override default product title.', 'cartflows-pro' ),
				'value' => $options['wcf-pre-checkout-offer-product-title'],

			)
		);

		/* Pre checkout offer Product Description */
		echo wcf()->meta->get_area_field(
			array(
				'label' => __( 'Product Description', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-desc',
				'value' => $options['wcf-pre-checkout-offer-desc'],
				'attr'  => array(
					'placeholder' => __( 'Write a few words about this awesome product and tell shoppers why they must get it. You may highlight this as "one time offer" and make it irresistible.', 'cartflows-pro' ),
				),
			)
		);

		/* Pre checkout offer Popup button title */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Order Button Text', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-popup-btn-text',
				// 'help'  => __( 'Order Button Text for Pre-Checkout.', 'cartflows-pro' ),
				'value' => $options['wcf-pre-checkout-offer-popup-btn-text'],
				'attr'  => array(
					'placeholder' => __( 'Yes, Add to My Order!', 'cartflows-pro' ),
				),

			)
		);

		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Skip Button Text', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-popup-skip-btn-text',
				// 'help'  => __( 'Skip order button text for Pre-Checkout.', 'cartflows-pro' ),
				'value' => $options['wcf-pre-checkout-offer-popup-skip-btn-text'],
				'attr'  => array(
					'placeholder' => __( 'No, thanks!', 'cartflows-pro' ),
				),

			)
		);

		/* Pre checkout offer Product Discount */
		echo wcf()->meta->get_select_field(
			array(
				'label'   => __( 'Discount Type', 'cartflows-pro' ),
				'name'    => 'wcf-pre-checkout-offer-discount',
				'value'   => $options['wcf-pre-checkout-offer-discount'],
				'options' => array(
					''                 => __( 'Original', 'cartflows-pro' ),
					'discount_percent' => __( 'Discount Percentage', 'cartflows-pro' ),
					'discount_price'   => __( 'Discount Price', 'cartflows-pro' ),

				),
			)
		);

		/* Pre checkout offer Produc Discount Value */
		echo wcf()->meta->get_text_field(
			array(

				'label' => __( 'Discount Value', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-discount-value',
				'value' => $options['wcf-pre-checkout-offer-discount-value'],
			)
		);

		echo wcf()->meta->get_description_field(
			array(
				'name'    => 'wcf-pre-checkout-offer-price-notice',
				'content' => __( 'Select product and save once to see prices', 'cartflows-pro' ),
			)
		);

		echo wcf()->meta->get_display_field(
			array(
				'label'   => __( 'Original Price', 'cartflows-pro' ),
				'name'    => 'wcf-pre-checkout-offer-original-price',
				'content' => $this->get_pre_checkout_offer_original_price( $options, $post_id ),
			)
		);

		echo wcf()->meta->get_display_field(
			array(
				'label'   => __( 'Sell Price', 'cartflows-pro' ),
				'name'    => 'wcf-pre-checkout-offer-discount-price',
				'content' => $this->get_pre_checkout_offer_discount_price( $options, $post_id ),
			)
		);

		$this->tab_style_pre_checkout_offer( $options, $post_id );

	}

	/**
	 * Tab style
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_style_pre_checkout_offer( $options, $post_id ) {

		echo "<div class='wcf-cs-pre-checkout-offer-options'>";

		echo wcf()->meta->get_section(
			array(
				'label' => __( 'Pre-Checkout Upsell Style', 'cartflows-pro' ),
			)
		);

		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Background Color', 'cartflows-pro' ),
				'name'  => 'wcf-pre-checkout-offer-bg-color',
				'value' => $options['wcf-pre-checkout-offer-bg-color'],
			)
		);

		echo '</div>';

	}


	/**
	 * Tab Content Product Bump.
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_content_product_bump( $options, $post_id ) {

		/* Order Bump Field */
		echo wcf()->meta->get_checkbox_field(
			array(
				'name'  => 'wcf-order-bump',
				'value' => $options['wcf-order-bump'],
				'after' => __( 'Enable Order Bump', 'cartflows-pro' ),
			)
		);

		echo wcf()->meta->get_select_field(
			array(
				'label'   => __( 'Bump Order Skin', 'cartflows-pro' ),
				'name'    => 'wcf-order-bump-style',
				'value'   => $options['wcf-order-bump-style'],
				'options' => array(
					'style-1' => __( 'Style 1', 'cartflows-pro' ),
					'style-2' => __( 'Style 2', 'cartflows-pro' ),
				),
			)
		);

		/* Order Bump Product Selection Field */
		echo wcf()->meta->get_product_selection_field(
			array(
				'name'        => 'wcf-order-bump-product',
				'value'       => $options['wcf-order-bump-product'],
				'label'       => __( 'Select Product', 'cartflows-pro' ),
				'help'        => __( 'Select Order Bump Product.', 'cartflows-pro' ),
				'multiple'    => false,
				'allow_clear' => true,
			)
		);

		echo wcf()->meta->get_select_field(
			array(
				'label'   => __( 'Bump Order Position', 'cartflows-pro' ),
				'name'    => 'wcf-order-bump-position',
				'value'   => $options['wcf-order-bump-position'],
				'options' => array(
					'before-checkout' => __( 'Before Checkout', 'cartflows-pro' ),
					'after-customer'  => __( 'After Customer Details', 'cartflows-pro' ),
					'after-order'     => __( 'After Order', 'cartflows-pro' ),
					'after-payment'   => __( 'After Payment', 'cartflows-pro' ),
				),
			)
		);

		/* Select Product Image Field */
		echo wcf()->meta->get_image_field(
			array(
				'name'  => 'wcf-order-bump-image',
				'value' => $options['wcf-order-bump-image'],
				'label' => __( 'Product Image', 'cartflows-pro' ),
			)
		);

		/* Order Bump Label */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Checkbox Label', 'cartflows-pro' ),
				'name'  => 'wcf-order-bump-label',
				'value' => $options['wcf-order-bump-label'],
			)
		);

		/* Order Bunp Highlight Text */
		echo wcf()->meta->get_text_field(
			array(
				'label' => __( 'Highlight Text', 'cartflows-pro' ),
				'name'  => 'wcf-order-bump-hl-text',
				'value' => $options['wcf-order-bump-hl-text'],
			)
		);

		/* Order Bunp Product Description */
		echo wcf()->meta->get_area_field(
			array(
				'label' => __( 'Product Description', 'cartflows-pro' ),
				'name'  => 'wcf-order-bump-desc',
				'value' => $options['wcf-order-bump-desc'],
			)
		);

		/* Order Bunp Discount */
		echo wcf()->meta->get_select_field(
			array(
				'label'   => __( 'Discount Type', 'cartflows-pro' ),
				'name'    => 'wcf-order-bump-discount',
				'value'   => $options['wcf-order-bump-discount'],
				'options' => array(
					''                 => __( 'Original', 'cartflows-pro' ),
					'discount_percent' => __( 'Discount Percentage', 'cartflows-pro' ),
					'discount_price'   => __( 'Discount Price', 'cartflows-pro' ),
					'coupon'           => __( 'Coupon', 'cartflows-pro' ),
				),
			)
		);

		/* Order Bump Discount Value */
		echo wcf()->meta->get_text_field(
			array(

				'label' => __( 'Discount Value', 'cartflows-pro' ),
				'name'  => 'wcf-order-bump-discount-value',
				'value' => $options['wcf-order-bump-discount-value'],
			)
		);

		/* Order Bump Discount Selection */
		echo wcf()->meta->get_coupon_selection_field(
			array(
				'label'       => __( 'Select Coupon', 'cartflows-pro' ),
				'name'        => 'wcf-order-bump-discount-coupon',
				'value'       => $options['wcf-order-bump-discount-coupon'],
				'allow_clear' => true,
			)
		);

		echo wcf()->meta->get_description_field(
			array(
				'name'    => 'wcf-bump-price-notice',
				'content' => __( 'Select product and save once to see prices', 'cartflows-pro' ),
			)
		);

		echo wcf()->meta->get_display_field(
			array(
				'label'   => __( 'Original Price', 'cartflows-pro' ),
				'name'    => 'wcf-bump-original-price',
				'content' => $this->get_bump_original_price( $options, $post_id ),
			)
		);

		echo wcf()->meta->get_display_field(
			array(
				'label'   => __( 'Sell Price', 'cartflows-pro' ),
				'name'    => 'wcf-bump-discount-price',
				'content' => $this->get_bump_discount_price( $options, $post_id ),
			)
		);

		$this->tab_style_product_bump( $options, $post_id );
	}


	/**
	 * Tab style
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_style_product_bump( $options, $post_id ) {

		echo "<div class='wcf-cs-bump-options'>";

		echo wcf()->meta->get_section(
			array(
				'label' => __( 'Bump Order Style', 'cartflows-pro' ),
			)
		);

		echo wcf()->meta->get_select_field(
			array(
				'label'   => __( 'Border Style', 'cartflows-pro' ),
				'name'    => 'wcf-bump-border-style',
				'value'   => $options['wcf-bump-border-style'],
				'options' => array(
					'inherit' => __( 'Default', 'cartflows-pro' ),
					'dashed'  => __( 'Dashed', 'cartflows-pro' ),
					'dotted'  => __( 'Dotted', 'cartflows-pro' ),
					'solid'   => __( 'Solid', 'cartflows-pro' ),
					'none'    => __( 'None', 'cartflows-pro' ),
				),
			)
		);
		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Border Color', 'cartflows-pro' ),
				'name'  => 'wcf-bump-border-color',
				'value' => $options['wcf-bump-border-color'],
			)
		);
		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Background Color', 'cartflows-pro' ),
				'name'  => 'wcf-bump-bg-color',
				'value' => $options['wcf-bump-bg-color'],
			)
		);

		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Label Color', 'cartflows-pro' ),
				'name'  => 'wcf-bump-label-color',
				'value' => $options['wcf-bump-label-color'],
			)
		);

		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Label Background Color', 'cartflows-pro' ),
				'name'  => 'wcf-bump-label-bg-color',
				'value' => $options['wcf-bump-label-bg-color'],
			)
		);
		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Description Text Color', 'cartflows-pro' ),
				'name'  => 'wcf-bump-desc-text-color',
				'value' => $options['wcf-bump-desc-text-color'],
			)
		);
		echo wcf()->meta->get_color_picker_field(
			array(
				'label' => __( 'Highlight Text Color', 'cartflows-pro' ),
				'name'  => 'wcf-bump-hl-text-color',
				'value' => $options['wcf-bump-hl-text-color'],
			)
		);

		echo '</div>';
	}


	/**
	 * Tab Content Custom Fields.
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_content_custom_fields( $options, $post_id ) {

		echo '<div class="wcf-cc-fields">';
			echo '<div class="wcf-cc-checkbox-field">';

				echo wcf()->meta->get_checkbox_field(
					array(
						'name'  => 'wcf-show-coupon-field',
						'value' => $options['wcf-show-coupon-field'],
						'after' => __( 'Enable Coupon Field ', 'cartflows-pro' ),
					)
				);

				echo wcf()->meta->get_checkbox_field(
					array(
						'name'  => 'wcf-checkout-additional-fields',
						'value' => $options['wcf-checkout-additional-fields'],
						'after' => __( 'Enable Additional Field', 'cartflows-pro' ),
					)
				);

				echo wcf()->meta->get_checkbox_field(
					array(
						'name'  => 'wcf-shipto-diff-addr-fields',
						'value' => $options['wcf-shipto-diff-addr-fields'],
						'after' => __( 'Enable Ship To Different Address', 'cartflows-pro' ),
					)
				);

				echo wcf()->meta->get_checkbox_field(
					array(
						'name'  => 'wcf-custom-checkout-fields',
						'value' => $options['wcf-custom-checkout-fields'],
						'after' => __( 'Enable Custom Field Editor', 'cartflows-pro' ),
					)
				);

			echo '</div>';
		echo '</div>';

		$this->tab_custom_fields_options( $options, $post_id );
	}

	/**
	 * Tab Custom Fields Options
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_custom_fields_options( $options, $post_id ) {

		echo '<div class="wcf-cb-fields">';
				/*Display Billing Checkout Custom Fields Box*/
				echo wcf()->meta->get_section(
					array(
						'label' => __( 'Billing Checkout Fields', 'cartflows-pro' ),
					)
				);

				$all_billing_fields = '';

				$get_ordered_billing_fields = wcf()->options->get_checkout_meta_value( $post_id, 'wcf_field_order_billing' );

		if ( isset( $get_ordered_billing_fields ) && ! empty( $get_ordered_billing_fields ) ) {
			$billing_fields = $get_ordered_billing_fields;
		} else {
			$billing_fields = Cartflows_Helper::get_checkout_fields( 'billing', $post_id );
		}

			echo "<ul id='wcf-billing-field-sortable' class='billing-field-sortable wcf-field-row' >";
			$i = 0;

		foreach ( $billing_fields as $key => $value ) {
			if ( isset( $value['label'] ) ) {
				$field_name = $value['label'];
			} elseif ( 'billing_address_2' == $value ) {
				$field_name = 'Street address line 2';
			}

			if ( isset( $value['label'] ) ) {
				$field_name = $value['label'];
			} elseif ( 'billing_address_2' == $value ) {
				$field_name = 'Street address line 2';
			}

			if ( isset( $value['placeholder'] ) ) {
				$placeholder = $value['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $value['default'] ) ) {
				$default_value = $value['default'];
			} else {
				$default_value = '';
			}

			if ( isset( $value['required'] ) && true == $value['required'] ) {
				$is_require = 'yes';
			} else {
				$is_require = 'no';
			}

			if ( isset( $value['type'] ) && ! empty( $value['type'] ) ) {
				$type = $value['type'];
			} else {
				$type = '';
			}

			if ( isset( $value['options'] ) && ! empty( $value['options'] ) ) {
				$select_options = implode( ',', $value['options'] );
			} else {
				$select_options = '';
			}

			$field_args = array(
				'type'        => $type,
				'label'       => $field_name,
				'name'        => 'wcf-' . $key,
				'value'       => $options[ 'wcf-' . $key ],
				'placeholder' => $placeholder,
				'width'       => '',
				'after'       => 'Enable',
				'section'     => 'billing',
				'default'     => $default_value,
				'required'    => $is_require,
				'options'     => $select_options,
			);

			if ( isset( $value['custom'] ) && $value['custom'] ) {
				$field_args['after_html']  = '<span class="wcf-cpf-actions" data-type="billing" data-key="' . $key . '">';
				$field_args['after_html'] .= '<a class="wcf-cpf-action-remove wp-ui-text-notification">' . __( 'Remove', 'cartflows-pro' ) . '</a>';
				$field_args['after_html'] .= '</span>';
			}

			$all_billing_fields .= "<li class='wcf-field-item-edit-inactive wcf-field-item'>";

			$all_billing_fields .= $this->get_field_html( $field_args, $options );

			$all_billing_fields .= '</li>';
		}

			echo $all_billing_fields;

			echo '</ul>';

			echo '</div>';
			echo '<div class="wcf-sb-fields">';

			/*Display Shipping Checkout Custom Fields Box*/
			echo wcf()->meta->get_section(
				array(
					'label' => __( 'Shipping Checkout Fields', 'cartflows-pro' ),
				)
			);

			$all_shipping_fields = '';

			$get_ordered_shipping_fields = wcf()->options->get_checkout_meta_value( $post_id, 'wcf_field_order_shipping' );

		if ( isset( $get_ordered_shipping_fields ) && ! empty( $get_ordered_shipping_fields ) ) {
				$shipping_fields = $get_ordered_shipping_fields;
		} else {
			$shipping_fields = Cartflows_Helper::get_checkout_fields( 'shipping', $post_id );
		}

		echo "<ul id='wcf-shipping-field-sortable' class='shipping-field-sortable wcf-field-row' >";
		foreach ( $shipping_fields as $key => $value ) {
			if ( isset( $value['label'] ) ) {
				$field_name = $value['label'];
			} elseif ( 'shipping_address_2' == $key ) {
				$field_name = 'Street address line 2';
			}

			if ( isset( $value['placeholder'] ) ) {
				$placeholder = $value['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $value['default'] ) ) {
				$default_value = $value['default'];
			} else {
				$default_value = '';
			}

			if ( isset( $value['required'] ) && true == $value['required'] ) {
				$is_require = 'yes';
			} else {
				$is_require = 'no';
			}

			if ( isset( $value['type'] ) && ! empty( $value['type'] ) ) {
				$type = $value['type'];
			} else {
				$type = '';
			}

			if ( isset( $value['options'] ) && ! empty( $value['options'] ) ) {
				$select_options = implode( ',', $value['options'] );
			} else {
				$select_options = '';
			}

			$field_args = array(
				'type'        => $type,
				'label'       => $field_name,
				'name'        => 'wcf-' . $key,
				'value'       => $options[ 'wcf-' . $key ],
				'placeholder' => $placeholder,
				'width'       => '',
				'after'       => 'Enable',
				'section'     => 'shipping',
				'default'     => $default_value,
				'required'    => $is_require,
				'options'     => $select_options,
			);

			if ( isset( $value['custom'] ) && $value['custom'] ) {
				$field_args['after_html']  = '<span class="wcf-cpf-actions" data-type="shipping" data-key="' . $key . '"> | ';
				$field_args['after_html'] .= '<a class="wcf-cpf-action-remove"><span class="dashicons dashicons-trash"></span></a>';
				$field_args['after_html'] .= '</span>';
			}

			$all_shipping_fields .= "<li class='wcf-field-item-edit-inactive wcf-field-item'>";

			$all_shipping_fields .= $this->get_field_html( $field_args, $options );

			$all_shipping_fields .= '</li>';

			// $all_shipping_fields .= wcf()->meta->get_checkbox_field( $field_args );
		}

			echo $all_shipping_fields;

			echo '</ul>';

		echo '</div>';

		echo '<div style="clear: both;"></div>';

		echo '<div class="wcf-custom-field-box">';

				echo wcf()->meta->get_pro_checkout_field_repeater(
					array(
						'name' => 'wcf-checkout-custom-fields',
					)
				);
		echo '</div>';
	}

	/**
	 * Tab Checkout Design Options
	 *
	 * @param array $options options.
	 * @param int   $post_id post ID.
	 */
	function tab_content_checkout_design( $options, $post_id ) {

		echo "<div class='wcf-checkout-two-step wcf-tab-content widefat'>";

			echo wcf()->meta->get_checkbox_field(
				array(
					'name'  => 'wcf-checkout-box-note',
					'value' => $options['wcf-checkout-box-note'],
					'after' => __( 'Enable Checkout Note', 'cartflows-pro' ),
				)
			);

			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Note Text', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-box-note-text',
					'value' => $options['wcf-checkout-box-note-text'],
				)
			);

			echo wcf()->meta->get_color_picker_field(
				array(
					'label' => __( 'Text Color', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-box-note-text-color',
					'value' => $options['wcf-checkout-box-note-text-color'],
				)
			);

			echo wcf()->meta->get_color_picker_field(
				array(
					'label' => __( 'Note Box Background Color', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-box-note-bg-color',
					'value' => $options['wcf-checkout-box-note-bg-color'],
				)
			);

			echo wcf()->meta->get_section(
				array(
					'label' => __( 'Steps', 'cartflows-pro' ),
				)
			);

			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Step One Title', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-step-one-title',
					'value' => $options['wcf-checkout-step-one-title'],
				)
			);

			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Step One Sub Title', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-step-one-sub-title',
					'value' => $options['wcf-checkout-step-one-sub-title'],
				)
			);

			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Step Two Title', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-step-two-title',
					'value' => $options['wcf-checkout-step-two-title'],
				)
			);
			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Step Two Sub Title', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-step-two-sub-title',
					'value' => $options['wcf-checkout-step-two-sub-title'],
				)
			);

			echo wcf()->meta->get_number_field(
				array(
					'label' => __( 'Section Width', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-two-step-section-width',
					'value' => $options['wcf-checkout-two-step-section-width'],
				)
			);

			echo wcf()->meta->get_select_field(
				array(
					'label'   => __( 'Border', 'cartflows-pro' ),
					'name'    => 'wcf-checkout-two-step-section-border',
					'value'   => $options['wcf-checkout-two-step-section-border'],
					'options' => array(
						'none'  => __( 'None', 'cartflows-pro' ),
						'solid' => __( 'Solid', 'cartflows-pro' ),
					),
				)
			);

			// echo wcf()->meta->get_color_picker_field(
			// array(
			// 'label' => __( 'Text Color', 'cartflows-pro' ),
			// 'name'  => 'wcf-checkout-two-step-title-text-color',
			// 'value' => $options['wcf-checkout-two-step-title-text-color'],
			// )
			// );
			// echo wcf()->meta->get_color_picker_field(
			// array(
			// 'label' => __( 'Background Color', 'cartflows-pro' ),
			// 'name'  => 'wcf-checkout-two-step-section-bg-color',
			// 'value' => $options['wcf-checkout-two-step-section-bg-color'],
			// )
			// );
			// echo wcf()->meta->get_color_picker_field(
			// array(
			// 'label' => __( 'Step Background Color', 'cartflows-pro' ),
			// 'name'  => 'wcf-checkout-step-bg-color',
			// 'value' => $options['wcf-checkout-step-bg-color'],
			// )
			// );
			// echo wcf()->meta->get_color_picker_field(
			// array(
			// 'label' => __( 'Active Step Background Color', 'cartflows-pro' ),
			// 'name'  => 'wcf-checkout-active-step-bg-color',
			// 'value' => $options['wcf-checkout-active-step-bg-color'],
			// )
			// );.
			echo wcf()->meta->get_section(
				array(
					'label' => __( 'Offer Button', 'cartflows-pro' ),
				)
			);

			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Offer Button Title', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-offer-button-title',
					'value' => $options['wcf-checkout-offer-button-title'],
				)
			);

			echo wcf()->meta->get_text_field(
				array(
					'label' => __( 'Offer Button Sub Title', 'cartflows-pro' ),
					'name'  => 'wcf-checkout-offer-button-sub-title',
					'value' => $options['wcf-checkout-offer-button-sub-title'],
				)
			);

		echo '</div>';
	}

	/**
	 * Add two Step Menu.
	 *
	 * @param array $tabs list of tabs.
	 * @param char  $active_tab active tab name.
	 * @return array $tabs list of tabs.
	 */
	function add_two_step_tab( $tabs, $active_tab ) {

		foreach ( $tabs as $key => $tab ) {
			if ( 'wcf-checkout-style' == $tab['id'] ) {
				$new_tab[] = array(
					'title' => __( 'Two Step', 'cartflows-pro' ),
					'id'    => 'wcf-checkout-two-step',
					'class' => 'wcf-checkout-two-step' === $active_tab ? 'wcf-tab wp-ui-text-highlight active' : 'wcf-tab',
					'icon'  => 'dashicons-editor-ol',
				);

				array_splice( $tabs, 4, 0, $new_tab );
			}
		}

		return $tabs;
	}

	/**
	 * Get field html.
	 *
	 * @param array $field_args field arguments.
	 * @param array $options options.
	 * @return string
	 */
	function get_field_html( $field_args, $options ) {

		$is_checkbox = false;
		$is_require  = false;
		$is_select   = false;
		$display     = 'none';

		if ( 'checkbox' == $field_args['type'] ) {
			$is_checkbox = true;
		}

		if ( 'yes' == $field_args['required'] ) {
			$is_require = true;
		}

		if ( 'select' == $field_args['type'] ) {
			$is_select = true;
			$display   = 'block';
		}

		// $field_markup = wcf()->meta->get_only_checkbox_field( $field_args );
		ob_start();
		?>
		<div class="wcf-field-item-bar 
		<?php
		if ( 'no' == $options[ $field_args['name'] ] ) {
			echo 'disable';
		}
		?>
		">
			<div class="wcf-field-item-handle ui-sortable-handle">
				<label class="dashicons 
				<?php
				if ( 'no' == $options[ $field_args['name'] ] ) {
					echo 'dashicons-hidden';
				} else {
					echo 'dashicons-visibility';
				}
				?>
				" for="<?php echo $field_args['name']; ?>"></label>
				<span class="item-title">
					<span class="wcf-field-item-title"><?php echo $field_args['label']; ?> 
					<?php
					if ( $is_require ) {
						echo '<i>*</i>';
					}
					?>
					</span>
					<span class="is-submenu" style="display: none;">sub item</span>
				</span>
				<span class="item-controls">
					<span class="dashicons dashicons-menu"></span>
					<span class="item-order hide-if-js">
						<a href="#" class="item-move-up" aria-label="Move up">↑</a>
						|
						<a href="#" class="item-move-down" aria-label="Move down">↓</a>
					</span>
					<a class="item-edit" id="edit-64" href="javascript:void(0);" aria-label="My account. Menu item 1 of 5."><span class="screen-reader-text">Edit</span></a>
				</span>
			</div>
		</div>
		<div class="wcf-field-item-settings">
			<div class="wcf-field-item-settings-checkbox">
				<?php
					echo wcf()->meta->get_checkbox_field(
						array(
							'label' => __( 'Enable this field', 'cartflows-pro' ),
							'name'  => $field_args['name'],
							'value' => $options[ $field_args['name'] ],
						)
					);
				?>
			</div>
			<div class="wcf-field-item-settings-row-width">
				<?php
					echo wcf()->meta->get_select_field(
						array(
							'label'   => __( 'Field Width', 'cartflows-pro' ),
							'name'    => 'wcf-field-width_' . str_replace( 'wcf-', '', $field_args['name'] ),
							'value'   => $options[ 'wcf-field-width_' . str_replace( 'wcf-', '', $field_args['name'] ) ],
							'options' => array(
								'33'  => __( '33%', 'cartflows-pro' ),
								'50'  => __( '50%', 'cartflows-pro' ),
								'100' => __( '100%', 'cartflows-pro' ),
							),
						)
					);
				?>
			</div>
			<div class="wcf-field-item-settings-label">
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Field Label', 'cartflows-pro' ),
							'name'  => 'wcf_label_text_field_' . $field_args['section'] . '[' . str_replace( 'wcf-', '', $field_args['name'] ) . ']',
							'value' => $field_args['label'],
						)
					);

				?>
				<input type="hidden" name="wcf_field_order_<?php echo $field_args['section']; ?>[]" value="<?php echo str_replace( 'wcf-', '', $field_args['name'] ); ?>"> 
			</div>

			<div class="wcf-field-item-settings-select-options" style="display:
			<?php
			if ( isset( $display ) ) {
				print $display;
			}
			?>
			;">
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Options', 'cartflows-pro' ),
							'name'  => 'wcf_select_option_field_' . $field_args['section'] . '[' . str_replace( 'wcf-', '', $field_args['name'] ) . ']',
							'value' => $field_args['options'],
						)
					);

				?>
			</div>
			<div class="wcf-field-item-settings-default">
				<?php
				if ( true == $is_checkbox ) {
					echo wcf()->meta->get_select_field(
						array(
							'label'   => __( 'Default', 'cartflows-pro' ),
							'name'    => 'wcf_label_default_field_' . $field_args['section'] . '[' . str_replace( 'wcf-', '', $field_args['name'] ) . ']',
							'value'   => $field_args['default'],
							'options' => array(
								'1' => __( 'Checked', 'cartflows-pro' ),
								'0' => __( 'Un-Checked', 'cartflows-pro' ),
							),
						)
					);
				} else {
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Default', 'cartflows-pro' ),
							'name'  => 'wcf_label_default_field_' . $field_args['section'] . '[' . str_replace( 'wcf-', '', $field_args['name'] ) . ']',
							'value' => $field_args['default'],
						)
					);
				}
				?>
			</div>
			<div class="wcf-field-item-settings-placeholder" 
			<?php
			if ( true == $is_checkbox || true == $is_select ) {
				?>
			<?php } ?> >
				<?php
					echo wcf()->meta->get_text_field(
						array(
							'label' => __( 'Placeholder', 'cartflows-pro' ),
							'name'  => 'wcf_label_placeholder_field_' . $field_args['section'] . '[' . str_replace( 'wcf-', '', $field_args['name'] ) . ']',
							'value' => $field_args['placeholder'],
						)
					);
				?>
			</div>
			<div class="wcf-field-item-settings-require">
				<?php
					echo wcf()->meta->get_checkbox_field(
						array(
							'label' => __( 'Required', 'cartflows-pro' ),
							'name'  => 'wcf_is_required_field_' . $field_args['section'] . '[' . str_replace( 'wcf-', '', $field_args['name'] ) . ']',
							'value' => $field_args['required'],
						)
					);
				?>
			</div>
			<?php
			if ( isset( $field_args['after_html'] ) ) {
				?>
				<div class="wcf-field-item-settings-row-delete-cf">
				<?php echo $field_args['after_html']; ?>
				</div>
				<?php
			}
			?>

		</div>

		<?php

		return ob_get_clean();
	}

	/**
	 * Get original price
	 *
	 * @param array $options options.
	 * @param int   $post_id post id.
	 * @return string
	 */
	function get_pre_checkout_offer_original_price( $options, $post_id ) {

		$offer_product = $options['wcf-pre-checkout-offer-product'];

		$custom_price = __( 'Product not selected', 'cartflows-pro' );

		if ( isset( $offer_product[0] ) ) {
			$custom_price = __( 'Product not exists', 'cartflows-pro' );

			$product_id = $offer_product[0];

			$product = wc_get_product( $product_id );
			if ( $product->is_type( 'variable' ) ) {

				$default_attributes = $product->get_default_attributes();

				if ( ! empty( $default_attributes ) ) {

					foreach ( $product->get_children() as $c_in => $variation_id ) {

						if ( 0 === $c_in ) {
							$product_id = $variation_id;
						}

						$single_variation = new WC_Product_Variation( $variation_id );

						if ( $default_attributes == $single_variation->get_attributes() ) {

							$product_id = $variation_id;
							break;
						}
					}
				} else {

					$product_childrens = $product->get_children();

					if ( is_array( $product_childrens ) ) {

						foreach ( $product_childrens  as $c_in => $c_id ) {

							$product_id = $c_id;
							break;
						}
					}
				}
			}

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {
				$custom_price = $product->get_regular_price();
				return wc_price( $custom_price );
			}
		}

		return $custom_price;
	}

	/**
	 * Get discount price
	 *
	 * @param array $options options.
	 * @param int   $post_id post id.
	 * @return string
	 */
	function get_pre_checkout_offer_discount_price( $options, $post_id ) {

		$offer_product = $options['wcf-pre-checkout-offer-product'];

		$custom_price = __( 'Product not selected', 'cartflows-pro' );

		if ( isset( $offer_product[0] ) ) {
			$custom_price = __( 'Product not exists', 'cartflows-pro' );

			$product_id = $offer_product[0];

			$product = wc_get_product( $product_id );

			if ( $product->is_type( 'variable' ) ) {

				$default_attributes = $product->get_default_attributes();

				if ( ! empty( $default_attributes ) ) {

					foreach ( $product->get_children() as $c_in => $variation_id ) {

						if ( 0 === $c_in ) {
							$product_id = $variation_id;
						}

						$single_variation = new WC_Product_Variation( $variation_id );

						if ( $default_attributes == $single_variation->get_attributes() ) {

							$product_id = $variation_id;
							break;
						}
					}
				} else {

					$product_childrens = $product->get_children();

					if ( is_array( $product_childrens ) ) {

						foreach ( $product_childrens  as $c_in => $c_id ) {

							$product_id = $c_id;
							break;
						}
					}
				}
			}

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {

				if ( ! empty( $product->get_sale_price() ) ) {

					$custom_price = floatval( $product->get_sale_price() );
				} else {
					$custom_price = floatval( $product->get_regular_price() );
				}

				/* Offer Discount */
				$discount_type  = $options['wcf-pre-checkout-offer-discount'];
				$discount_value = floatval( $options['wcf-pre-checkout-offer-discount-value'] );

				if ( ! empty( $discount_type ) && $discount_value > 0 ) {
					$original_price = floatval( $product->get_regular_price() );

					if ( 'discount_percent' === $discount_type ) {

						$discount_custom_price = $original_price - ( ( $original_price * $discount_value ) / 100 );

					} elseif ( 'discount_price' === $discount_type ) {

						$discount_custom_price = $original_price - $discount_value;

					}
					$custom_price = $discount_custom_price;

				}

				return wc_price( $custom_price );
			}
		}

		return $custom_price;
	}

	/**
	 * Get original price
	 *
	 * @param array $options options.
	 * @param int   $post_id post id.
	 * @return string
	 */
	function get_bump_original_price( $options, $post_id ) {

		$offer_product = $options['wcf-order-bump-product'];

		$custom_price = __( 'Product not selected', 'cartflows-pro' );

		if ( isset( $offer_product[0] ) ) {
			$custom_price = __( 'Product not exists', 'cartflows-pro' );

			$product_id = $offer_product[0];

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {

				if ( $product->is_type( 'variable' ) ) {

					$default_attributes = $product->get_default_attributes();

					if ( ! empty( $default_attributes ) ) {

						foreach ( $product->get_children() as $c_in => $variation_id ) {

							if ( 0 === $c_in ) {
								$product_id = $variation_id;
							}

							$single_variation = new WC_Product_Variation( $variation_id );

							if ( $default_attributes == $single_variation->get_attributes() ) {

								$product_id = $variation_id;
								break;
							}
						}
					} else {

						$product_childrens = $product->get_children();

						if ( is_array( $product_childrens ) ) {

							foreach ( $product_childrens  as $c_in => $c_id ) {

								$product_id = $c_id;
								break;
							}
						}
					}
					$product = wc_get_product( $product_id );
				}
				$custom_price = $product->get_regular_price();

				return wc_price( $custom_price );
			}
		}

		return $custom_price;
	}

	/**
	 * Get discount price
	 *
	 * @param array $options options.
	 * @param int   $post_id post id.
	 * @return string
	 */
	function get_bump_discount_price( $options, $post_id ) {

		$offer_product = $options['wcf-order-bump-product'];

		$custom_price = __( 'Product not selected', 'cartflows-pro' );

		if ( isset( $offer_product[0] ) ) {
			$custom_price = __( 'Product not exists', 'cartflows-pro' );

			$product_id = $offer_product[0];

			$product = wc_get_product( $product_id );

			if ( is_object( $product ) ) {

				if ( $product->is_type( 'variable' ) ) {

					$default_attributes = $product->get_default_attributes();

					if ( ! empty( $default_attributes ) ) {

						foreach ( $product->get_children() as $c_in => $variation_id ) {

							if ( 0 === $c_in ) {
								$product_id = $variation_id;
							}

							$single_variation = new WC_Product_Variation( $variation_id );

							if ( $default_attributes == $single_variation->get_attributes() ) {

								$product_id = $variation_id;
								break;
							}
						}
					} else {

						$product_childrens = $product->get_children();

						if ( is_array( $product_childrens ) ) {

							foreach ( $product_childrens  as $c_in => $c_id ) {

								$product_id = $c_id;
								break;
							}
						}
					}
					$product = wc_get_product( $product_id );
				}

				if ( ! empty( $product->get_sale_price() ) ) {

					$custom_price = floatval( $product->get_sale_price() );
				} else {
					$custom_price = floatval( $product->get_regular_price() );
				}

				/* Offer Discount */
				$discount_type = $options['wcf-order-bump-discount'];

				if ( ! empty( $discount_type ) ) {

					$custom_price = floatval( $product->get_regular_price() );

					$discount_value = floatval( $options['wcf-order-bump-discount-value'] );

					if ( 'discount_percent' === $discount_type ) {
						if ( $discount_value > 0 ) {
							$custom_price = $custom_price - ( ( $custom_price * $discount_value ) / 100 );
						}
					} elseif ( 'discount_price' === $discount_type ) {
						if ( $discount_value > 0 ) {
							$custom_price = $custom_price - $discount_value;
						}
					} elseif ( 'coupon' === $discount_type ) {
						$discount_coupon = $options['wcf-order-bump-discount-coupon'];

						if ( is_array( $discount_coupon ) && ! empty( $discount_coupon ) ) {
							$discount_coupon = reset( $discount_coupon );
						}

						return __( 'Coupon will be applied on checkout', 'cartflows-pro' );
					}
				}

				return wc_price( $custom_price );
			}
		}

		return $custom_price;
	}


}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Checkout_Meta::get_instance();
