<?php
/**
 * Australia Post Shipping Method
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Shipping_Australia_Post class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_Australia_Post extends WC_Shipping_Method {
	/**
	 * Default Api
	 *
	 * @var string
	 */
	private $default_api_key = 'smUZLviAk6JIkIHvL0xgzP1yToSu7iQJ';
	private $max_weight;
	private $services;
	private $extra_cover;
	private $delivery_confirmation;

	private $endpoints = array(
		'calculation' => 'https://digitalapi.auspost.com.au/api/postage/{type}/{doi}/calculate.json',
		'services'    => 'https://digitalapi.auspost.com.au/api/postage/{type}/{doi}/service.json',
	);

	// Four of the external territories of Australia are officially assigned their own country codes in ISO 3166-1
	private $au_territories = array(
		'AU', 'CC', 'CX', 'HM', 'NF',
	);

	private $sod_cost     = 2.95;
	private $int_sod_cost = 5.49;
	private $found_rates;
	private $rate_cache;
	private $is_international = false;
	private $default_boxes;

	/**
	 * Packing Method
	 *
	 * @var string
	 */
	private $packing_method;

	/**
	 * Shipping prices excluding tax.
	 *
	 * @var string
	 */
	private $excluding_tax;

	/**
	 * Origin postal code.
	 *
	 * @var string
	 */
	private $origin;

	/**
	 * Api Key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Custom boxes.
	 *
	 * @var array
	 */
	private $boxes;

	/**
	 * Enabled Services.
	 *
	 * @var array
	 */
	private $custom_services;

	/**
	 * Show 'all' rates or not
	 *
	 * @var string
	 */
	private $offer_rates;

	/**
	 * Enable debug mode.
	 *
	 * @var bool
	 */
	private $debug;

	/**
	 * Use satchel rates. 'enable', 'disable' or 'prioritize'.
	 *
	 * @var string
	 */
	private $satchel_rates;

	/**
	 * __construct function.
	 *
	 * @param int $instance_id Instance Id.
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );

		$this->id                 = 'australia_post';
		$this->method_title       = __( 'Australia Post', 'woocommerce-shipping-australia-post' );
		$this->method_description = __( 'The Australia Post extension obtains rates dynamically from the Australia Post API during cart/checkout.', 'woocommerce-shipping-australia-post' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'settings',
		);
		$this->init();
	}

	/**
	 * Is_available function.
	 *
	 * @param array $package Package to check.
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( empty( $package['destination']['country'] ) ) {
			return false;
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}

	/**
	 * Initialize settings
	 *
	 * @version 2.4.0
	 * @since 2.4.0
	 * @return void
	 */
	private function set_settings() {
		// Define user set variables.
		$this->title                 = $this->get_option( 'title', $this->method_title );
		$this->excluding_tax         = $this->get_option( 'excluding_tax', 'no' );
		$this->origin                = $this->get_option( 'origin', '' );
		$this->api_key               = $this->get_option( 'api_key', $this->default_api_key );
		$this->packing_method        = $this->get_option( 'packing_method', 'per_item' );
		$this->boxes                 = $this->get_option( 'boxes', array() );
		$this->custom_services       = $this->get_option( 'services', array() );
		$this->offer_rates           = $this->get_option( 'offer_rates', 'all' );
		$this->debug                 = 'yes' === $this->get_option( 'debug_mode' );
		$this->satchel_rates         = $this->get_option( 'satchel_rates' );
		$this->services              = include dirname( __FILE__ ) . '/data/data-services.php';
		$this->extra_cover           = include dirname( __FILE__ ) . '/data/data-extra-cover.php';
		$this->delivery_confirmation = include dirname( __FILE__ ) . '/data/data-sod.php';
		$this->default_boxes         = include dirname( __FILE__ ) . '/data/data-box-sizes.php';

		// Used for weight based packing only.
		$this->max_weight = $this->get_option( 'max_weight', '20' );
	}

	/**
	 * Init function.
	 *
	 * @return void
	 */
	private function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->set_settings();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'test_api_key' ), -10 );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );
	}

	/**
	 * Get default and custom boxes merged
	 *
	 * @return array
	 */
	private function get_all_boxes() {
		$enabled_boxes = array();
		foreach ( $this->boxes as $key => &$box ) {

			if ( isset( $box['id'] ) && isset( $box['enabled'] ) ) {
				$enabled_boxes[ $box['id'] ] = $box['enabled'];
				unset( $this->boxes[ $key ] );
			}

			// Update type field for BC reasons. 'is_letter' is the old busted way 'type' is the new hotness.
			if ( empty( $box['type'] ) && ! empty( $box['is_letter'] ) ) {
				$box['type'] = 'envelope';
			} elseif ( empty( $box['type'] ) ) {
				$box['type'] = 'box';
			}
			if ( empty( $box['name'] ) ) {
				$box['name'] = 'Box ' . ( $key + 1 );
			}
		}

		foreach ( $this->default_boxes as &$box ) {
			if ( isset( $enabled_boxes[ $box['id'] ] ) ) {
				$box['enabled'] = $enabled_boxes[ $box['id'] ];
			} else {
				$box['enabled'] = true;
			}
		}
		return array_merge( $this->default_boxes, $this->boxes );
	}

	/**
	 * Process settings on save
	 *
	 * @since 2.4.0
	 * @version 2.4.0
	 * @return void
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		$this->set_settings();
	}

	/**
	 * For letter boxes convert the metrics to match it as users have set on on the product.
	 *
	 * Example:
	 * the letter height is entered as `mm` but the product value is entered in `cm`.
	 *
	 * @since 1.9.0
	 * @param array $boxes saved settings.
	 * @return array $boxes
	 */
	public function convert_letter_boxes_to_match_product_metrics( $boxes ) {
		foreach ( $boxes as $index => $box ) {
			if ( ( isset( $box['type'] ) && 'letter' === $box['type'] ) || ( isset( $box['is_letter'] ) && $box['is_letter'] ) ) {

				$updated_box = array();

				$updated_box['is_letter']    = $box['is_letter'];
				$updated_box['outer_length'] = wc_get_dimension( $box['outer_length'], 'cm', 'mm' );
				$updated_box['outer_width']  = wc_get_dimension( $box['outer_width'], 'cm', 'mm' );
				$updated_box['outer_height'] = wc_get_dimension( $box['outer_height'], 'cm', 'mm' );
				$updated_box['inner_length'] = wc_get_dimension( $box['inner_length'], 'cm', 'mm' );
				$updated_box['inner_width']  = wc_get_dimension( $box['inner_width'], 'cm', 'mm' );
				$updated_box['inner_height'] = wc_get_dimension( $box['inner_height'], 'cm', 'mm' );
				$updated_box['box_weight']   = wc_get_weight( $box['box_weight'], 'kg', 'g' );
				$updated_box['max_weight']   = wc_get_weight( $box['max_weight'], 'kg', 'g' );

				$boxes[ $index ] = $updated_box;

			}
		}
		return $boxes;
	}

	/**
	 * Output a debug message.
	 *
	 * @since 1.0.0
	 * @version 2.4.4
	 *
	 * @param string $message Debug message.
	 * @param string $type    Debug type ('notice', 'error', etc).
	 */
	public function debug( $message, $type = 'notice' ) {
		// Probably called in wp-admin page.
		if ( ! function_exists( 'wc_add_notice' ) ) {
			return;
		}

		// Debug setting is disabled.
		if ( ! $this->debug ) {
			return;
		}

		// Only store owner or administrator can see the debug notice.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wc_add_notice( $message, $type );
	}

	/**
	 * Generate_services_html function.
	 *
	 * @return string
	 */
	public function generate_services_html() {
		ob_start();
		include dirname( __FILE__ ) . '/views/html-services.php';
		return ob_get_clean();
	}

	/**
	 * Generate_box_packing_html function.
	 *
	 * @return string
	 */
	public function generate_box_packing_html() {
		ob_start();
		include dirname( __FILE__ ) . '/views/html-box-packing.php';
		return ob_get_clean();
	}

	/**
	 * Validate_box_packing_field function.
	 *
	 * @param string $key Field key.
	 * @param string $value Posted Value.
	 *
	 * @return array
	 */
	public function validate_box_packing_field( $key, $value ) {
		$boxes = array();

		$post_data = $_POST; // WPCS: CSRF ok, input var ok.

		if ( isset( $post_data['boxes_outer_length'] ) ) {
			$boxes_name         = isset( $post_data['boxes_name'] ) ? $post_data['boxes_name'] : array();
			$boxes_outer_length = isset( $post_data['boxes_outer_length'] ) ? $post_data['boxes_outer_length'] : array();
			$boxes_outer_width  = isset( $post_data['boxes_outer_width'] ) ? $post_data['boxes_outer_width'] : array();
			$boxes_outer_height = isset( $post_data['boxes_outer_height'] ) ? $post_data['boxes_outer_height'] : array();
			$boxes_inner_length = isset( $post_data['boxes_inner_length'] ) ? $post_data['boxes_inner_length'] : array();
			$boxes_inner_width  = isset( $post_data['boxes_inner_width'] ) ? $post_data['boxes_inner_width'] : array();
			$boxes_inner_height = isset( $post_data['boxes_inner_height'] ) ? $post_data['boxes_inner_height'] : array();
			$boxes_box_weight   = isset( $post_data['boxes_box_weight'] ) ? $post_data['boxes_box_weight'] : array();
			$boxes_max_weight   = isset( $post_data['boxes_max_weight'] ) ? $post_data['boxes_max_weight'] : array();
			$boxes_type         = isset( $post_data['boxes_type'] ) ? $post_data['boxes_type'] : array();
			$boxes_enabled      = isset( $post_data['boxes_enabled'] ) ? $post_data['boxes_enabled'] : array();
			$boxes_count        = count( $boxes_outer_length );

			$default_boxes_count = count( $this->default_boxes );
			for ( $i = 0; $i < $boxes_count; $i++ ) {

				if ( $i < $default_boxes_count ) {
					$boxes[] = array(
						'enabled' => isset( $boxes_enabled[ $i ] ) ? true : false,
						'id'      => $this->default_boxes[ $i ]['id'],
					);
				} elseif ( $boxes_outer_length[ $i ] && $boxes_outer_width[ $i ] && $boxes_outer_height[ $i ] && $boxes_inner_length[ $i ] && $boxes_inner_width[ $i ] && $boxes_inner_height[ $i ] ) {

					$boxes[] = array(
						'name'         => sanitize_text_field( $boxes_name[ $i ] ),
						'outer_length' => floatval( $boxes_outer_length[ $i ] ),
						'outer_width'  => floatval( $boxes_outer_width[ $i ] ),
						'outer_height' => floatval( $boxes_outer_height[ $i ] ),
						'inner_length' => floatval( $boxes_inner_length[ $i ] ),
						'inner_width'  => floatval( $boxes_inner_width[ $i ] ),
						'inner_height' => floatval( $boxes_inner_height[ $i ] ),
						'box_weight'   => floatval( $boxes_box_weight[ $i ] ),
						'max_weight'   => floatval( $boxes_max_weight[ $i ] ),
						'type'         => in_array( $boxes_type[ $i ], array( 'box', 'tube', 'envelope', 'packet' ), true ) ? $boxes_type[ $i ] : 'box',
						'enabled'      => isset( $boxes_enabled[ $i ] ) ? true : false,
					);
				}
			}
		}

		return $boxes;
	}

	/**
	 * Validate_services_field function.
	 *
	 * @param mixed $key Key.
	 * @return array
	 */
	public function validate_services_field( $key ) {
		$services        = array();
		$posted_services = $_POST['australia_post_service'];

		foreach ( $posted_services as $code => $settings ) {
			$services[ $code ] = array(
				'name'                  => wc_clean( $settings['name'] ),
				'order'                 => wc_clean( $settings['order'] ),
				'enabled'               => isset( $settings['enabled'] ) ? true : false,
				'adjustment'            => wc_clean( $settings['adjustment'] ),
				'adjustment_percent'    => str_replace( '%', '', wc_clean( $settings['adjustment_percent'] ) ),
				'extra_cover'           => isset( $settings['extra_cover'] ) ? true : false,
				'delivery_confirmation' => isset( $settings['delivery_confirmation'] ) ? true : false,
			);
		}

		return $services;
	}

	/**
	 * Clear_transients function.
	 *
	 * @return void
	 */
	public function clear_transients() {
		delete_transient( 'wc_australia_post_quotes' );
	}

	/**
	 * Init_form_fields function.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'          => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-australia-post' ),
				'default'     => __( 'Australia Post', 'woocommerce-shipping-australia-post' ),
			),
			'origin'         => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'description' => __( 'Enter the postcode for the <strong>sender</strong>.', 'woocommerce-shipping-australia-post' ),
				'default'     => '',
			),
			'excluding_tax'  => array(
				'title'       => __( 'Tax', 'woocommerce-shipping-australia-post' ),
				'label'       => __( 'Calculate Rates Excluding Tax', 'woocommerce-shipping-australia-post' ),
				'type'        => 'checkbox',
				'description' => __( "Calculate shipping rates excluding tax (if you plan to add tax via WooCommerce's tax system). By default rates returned by the Australia Post API include tax.", 'woocommerce-shipping-australia-post' ),
				'default'     => 'no',
			),
			'rates'          => array(
				'title'       => __( 'Rates and Services', 'woocommerce-shipping-australia-post' ),
				'type'        => 'title',
				'description' => __( 'The following settings determine the rates you offer your customers.', 'woocommerce-shipping-australia-post' ),
			),
			'packing_method' => array(
				'title'   => __( 'Parcel Packing Method', 'woocommerce-shipping-australia-post' ),
				'type'    => 'select',
				'default' => '',
				'class'   => 'packing_method',
				'options' => array(
					'per_item'    => __( 'Default: Pack items individually', 'woocommerce-shipping-australia-post' ),
					'weight'      => __( 'Weight of all items', 'woocommerce-shipping-australia-post' ),
					'box_packing' => __( 'Recommended: Pack into boxes with weights and dimensions', 'woocommerce-shipping-australia-post' ),
				),
			),
			'max_weight'     => array(
				'title'       => __( 'Maximum weight (kg)', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'default'     => '20',
				'description' => __( 'Maximum weight per package in kg.', 'woocommerce-shipping-australia-post' ),
			),
			'boxes'          => array(
				'type' => 'box_packing',
			),
			'satchel_rates'  => array(
				'title'   => __( 'Satchel Rates', 'woocommerce-shipping-australia-post' ),
				'type'    => 'select',
				'options' => array(
					'on'       => __( 'Enable Satchel Rates', 'woocommerce-shipping-australia-post' ),
					'priority' => __( 'Prioritze Satchel Rates', 'woocommerce-shipping-australia-post' ),
					'off'      => __( 'Disable Satchel Rates', 'woocommerce-shipping-australia-post' ),
				),
				'default' => 'off',
			),
			'offer_rates'    => array(
				'title'       => __( 'Offer Rates', 'woocommerce-shipping-australia-post' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'all',
				'options'     => array(
					'all'      => __( 'Offer the customer all returned rates', 'woocommerce-shipping-australia-post' ),
					'cheapest' => __( 'Offer the customer the cheapest rate only, anonymously', 'woocommerce-shipping-australia-post' ),
				),
			),
			'services'       => array(
				'type' => 'services',
			),
		);

		$this->form_fields = array(
			'api'        => array(
				'title'       => __( 'API Settings', 'woocommerce-shipping-australia-post' ),
				'type'        => 'title',
				'description' => __( 'Your API access details are obtained from the Australia Post website. You can obtain your <a href="https://auspost.com.au/devcentre/pacpcs-registration.asp">own key here</a>, or just use ours.', 'woocommerce-shipping-australia-post' ),
			),
			'api_key'    => array(
				'title'       => __( 'API Key', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'description' => __( 'Leave blank to use our API Key.', 'woocommerce-shipping-australia-post' ),
				'default'     => '',
				'placeholder' => $this->default_api_key,
			),
			'debug_mode' => array(
				'title'       => __( 'Debug Mode', 'woocommerce-shipping-australia-post' ),
				'label'       => __( 'Enable debug mode', 'woocommerce-shipping-australia-post' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'description' => __( 'Enable debug mode to show debugging information on your cart/checkout.', 'woocommerce-shipping-australia-post' ),
			),
		);
	}

	/**
	 * Tests the entered API key against the service to see if a forbidden error is returned.
	 * If it is, the key is rejected and an error message is displayed.
	 */
	public function test_api_key() {
		if ( empty( $_POST['woocommerce_australia_post_api_key'] ) ) {  // WPCS: CSRF ok, input var ok.
			return;
		}

		$test_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'parcel',
				'domestic',
			),
			$this->endpoints['calculation']
		);
		$test_request  = 'weight=5&height=5&width=5&length=5&from_postcode=3149&to_postcode=3149&service_code=AUS_PARCEL_REGULAR';
		$test_headers  = array( 'AUTH-KEY' => $_POST['woocommerce_australia_post_api_key'] );  // WPCS: CSRF ok, input var ok.

		// We don't want to use $this->get_response here because we don't want the result cached,
		// we want to avoid the front end debug notices, and we want to get back the actual status code.
		$response      = wp_remote_get(
			$test_endpoint . '?' . $test_request,
			array(
				'headers' => $test_headers,
			)
		);
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 403 !== $response_code ) {
			return;
		}

		echo '<div class="error">
			<p>' . esc_html__( 'The Australia Post API key you entered is invalid. Please make sure you entered a valid key (<a href="https://auspost.com.au/devcentre/pacpcs-registration.asp">which can be obtained here</a>) and not your WooCommerce license key. Our API key will be used instead.', 'woocommerce-shipping-australia-post' ) . '</p>
		</div>';

		$_POST['woocommerce_australia_post_api_key'] = '';
	}

	/**
	 * Get max extra cover from quote.
	 *
	 * @since 2.4.2
	 *
	 * @param float  $item_value Item value.
	 * @param object $quote      Quote from API.
	 *
	 * @return int Max extra cover
	 */
	protected function _get_max_extra_cover_from_quote( $item_value, $quote ) {
		$max_extra_cover = $quote->max_extra_cover;

		if ( ! isset( $quote->options->option ) || ! is_array( $quote->options->option ) ) {
			return $max_extra_cover;
		}

		$options = $quote->options->option;
		foreach ( $options as $option ) {
			if ( ! isset( $option->suboptions->option->max_extra_cover ) ) {
				continue;
			}

			$max_extra_cover_option = $option->suboptions->option->max_extra_cover;

			if ( $item_value > $max_extra_cover_option ) {
				continue;
			}

			$max_extra_cover = $max_extra_cover_option;
		}

		return $max_extra_cover;
	}

	/**
	 * Calculates the extra cover cost
	 * For International its $9.60 for first $100, $2.50 for each additional $100
	 * For domestic its $1.50 per $100
	 * We are assuming max cover for each items
	 *
	 * @since 2.3.12
	 * @version 2.3.12
	 *
	 * @param float $item_value Value of current item.
	 * @param int   $max_extra_cover The maximum allowed value to cover.
	 * @return float $cover_cost
	 */
	public function calculate_extra_cover_cost( $item_value, $max_extra_cover ) {
		$extra_cover_cost          = 1.50;
		$int_extra_cover           = 2.50;
		$int_extra_cover_base_cost = 9.60;

		if ( empty( $item_value ) ) {
			return 0;
		}

		// Make sure item value is no more than max cover value.
		if ( $item_value > $max_extra_cover ) {
			$item_value = $max_extra_cover;
		}

		if ( $this->is_international ) {
			if ( $item_value < 100 ) {
				return $int_extra_cover_base_cost;
			}

			return $int_extra_cover_base_cost + ( ceil( ( $item_value - 100 ) / 100 ) * $int_extra_cover );
		}

		return $extra_cover_cost * ( ceil( $item_value / 100 ) );
	}

	/**
	 * See if rate is satchel
	 *
	 * @param string $code Service code.
	 * @return boolean
	 */
	public function is_satchel( $code ) {
		return strpos( $code, '_SATCHEL_' ) !== false;
	}

	/**
	 * See if rate is Courier Post.
	 *
	 * @param string $code Service code.
	 * @return bool
	 */
	public function is_courier_post( $code ) {
		return strpos( $code, 'COURIER' ) !== false;
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @param array $package Current package.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$this->found_rates      = array();
		$this->rate_cache       = get_transient( 'wc_australia_post_quotes' );
		$this->is_international = $this->is_international( $package );
		$headers                = $this->get_request_header();
		$package_requests       = $this->get_package_requests( $package );

		if ( empty( $this->rate_cache ) ) {
			$this->rate_cache = array();
		}

		$this->debug( __( 'Australia Post debug mode is on - to hide these messages, turn debug mode off in the settings.', 'woocommerce-shipping-australia-post' ) );

		// Prepare endpoints.
		$letter_services_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'letter',
				( $this->is_international ? 'international' : 'domestic' ),
			),
			$this->endpoints['services']
		);

		$letter_calculation_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'letter',
				( $this->is_international ? 'international' : 'domestic' ),
			),
			$this->endpoints['calculation']
		);

		$services_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'parcel',
				( $this->is_international ? 'international' : 'domestic' ),
			),
			$this->endpoints['services']
		);

		$calculation_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'parcel',
				( $this->is_international ? 'international' : 'domestic' ),
			),
			$this->endpoints['calculation']
		);

		if ( $package_requests ) {

			foreach ( $package_requests as $key => $package_request ) {

				$request = http_build_query( array_merge( $package_request, $this->get_request( $package ) ), '', '&' );

				if ( isset( $package_request['thickness'] ) ) {
					$response = $this->get_response( $letter_services_endpoint, $request, $headers );
				} else {
					$response = $this->get_response( $services_endpoint, $request, $headers );
				}

				if ( isset( $response->services->service ) && is_array( $response->services->service ) ) {

					// Loop our known services.
					foreach ( $this->services as $service => $values ) {

						$rate_code            = (string) $service;
						$rate_id              = $this->id . ':' . $rate_code;
						$rate_name            = (string) $values['name'];
						$rate_cost            = null;
						$optional_extras_cost = 0;

						// Main service code.
						foreach ( $response->services->service as $quote ) {
							if ( ( isset( $values['alternate_services'] ) && in_array( $quote->code, $values['alternate_services'], true ) ) || $service === $quote->code ) {

								$delivery_confirmation = false;
								$rate_set              = false;

								if ( $this->is_satchel( $quote->code ) && 'off' === $this->satchel_rates ) {
									continue;
								}

								if ( $this->is_satchel( $quote->code ) ) {

									if ( 'priority' === $this->satchel_rates ) {
										$rate_cost = $quote->price;
										$rate_set  = true;
									}
									if ( ! empty( $this->custom_services[ $rate_code ]['delivery_confirmation'] ) ) {
										$delivery_confirmation = true;
									}
								} elseif ( ! empty( $this->custom_services[ $rate_code ]['delivery_confirmation'] ) ) {
									$delivery_confirmation = true;
								}

								/**
								 * You must add $2.95 for Signature on Delivery
								 * if your item is valued above $300.
								 *
								 * Please note that this doesn't apply to Courier
								 * Post.
								 *
								 * Don't be confused why we're checking `$package_request['extra_cover']`,
								 * because it's actually product's price.
								 *
								 * @see https://auspost.com.au/parcels-mail/sending-in-australia/domestic-parcels/optional-extras-domestic
								 * @see https://github.com/woocommerce/woocommerce-shipping-australia-post/issues/84
								 */
								if ( ! $this->is_courier_post( $quote->code ) && ! empty( $this->custom_services[ $rate_code ]['extra_cover'] ) && $package_request['extra_cover'] >= 300 ) {
									$delivery_confirmation = true;
								}

								if ( is_null( $rate_cost ) ) {
									$rate_cost = $quote->price;
									$rate_set  = true;
								} elseif ( $quote->price < $rate_cost ) {
									$rate_cost = $quote->price;
									$rate_set  = true;
								}

								if ( $rate_set ) {
									// Reset extras cost to 0 since we do not want to duplicate costs for each service.
									$optional_extras_cost = 0;

									// User wants extra cover.
									if ( ! empty( $this->custom_services[ $rate_code ]['extra_cover'] ) && isset( $package_request['extra_cover'] ) ) {
										$max_extra_cover       = $this->_get_max_extra_cover_from_quote( $package_request['extra_cover'], $quote );
										$optional_extras_cost += $this->calculate_extra_cover_cost( $package_request['extra_cover'], $max_extra_cover );
									}

									// User wants SOD or an item is valued above $300.
									if ( $delivery_confirmation ) {
										if ( $this->is_international ) {
											$optional_extras_cost += $this->int_sod_cost;
										} else {
											$optional_extras_cost += $this->sod_cost;
										}
									}
								}
							}
						}

						if ( $rate_cost ) {
							$rate_cost += $optional_extras_cost;
							$this->prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $package_request, $package );
						}
					}
				}
			}
		}

		// Set transient.
		set_transient( 'wc_australia_post_quotes', $this->rate_cache, YEAR_IN_SECONDS );

		// Ensure rates were found for all packages.
		if ( $this->found_rates ) {
			foreach ( $this->found_rates as $key => $value ) {
				if ( $value['packages'] < count( $package_requests ) ) {
					unset( $this->found_rates[ $key ] );
				}
			}
		}

		// Add rates.
		if ( $this->found_rates ) {
			if ( 'all' === $this->offer_rates ) {

				uasort( $this->found_rates, array( $this, 'sort_rates' ) );

				foreach ( $this->found_rates as $key => $rate ) {
					$this->add_rate( $rate );
				}
			} else {

				$cheapest_rate = array(
					'cost' => PHP_INT_MAX,
				);

				foreach ( $this->found_rates as $key => $rate ) {
					if ( $cheapest_rate['cost'] > $rate['cost'] ) {
						$cheapest_rate = $rate;
					}
				}

				$cheapest_rate['label'] = $this->title; // will use generic rate label defined by user.
				$this->add_rate( $cheapest_rate );

			}
		}
	}

	/**
	 * Checks if destination is international
	 *
	 * @since 2.3.12
	 * @version 2.3.12
	 * @param array $package Current Package.
	 * @return bool
	 */
	public function is_international( $package ) {
		if ( ! in_array( $package['destination']['country'], $this->au_territories ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Prepare_rate function.
	 *
	 * @param mixed $rate_code
	 * @param mixed $rate_id
	 * @param mixed $rate_name
	 * @param mixed $rate_cost
	 */
	private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $package_request = '', $package = array() ) {
		// Name adjustment.
		if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) ) {
			$rate_name = $this->custom_services[ $rate_code ]['name'];
		}

		// Cost adjustment %.
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment_percent'] ) ) {
			$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $rate_code ]['adjustment_percent'] ) / 100 ) );
		}

		// Cost adjustment..
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment'] ) ) {
			$rate_cost = $rate_cost + floatval( $this->custom_services[ $rate_code ]['adjustment'] );
		}

		// Exclude Tax?
		if ( 'yes' === $this->excluding_tax && ! $this->is_international ) {
			$tax_rate  = apply_filters( 'woocommerce_shipping_australia_post_tax_rate', 0.10 );
			$rate_cost = $rate_cost / ( $tax_rate + 1 );
		}

		// Enabled check.
		if ( isset( $this->custom_services[ $rate_code ] ) && empty( $this->custom_services[ $rate_code ]['enabled'] ) ) {
			return;
		}

		// Merging.
		if ( isset( $this->found_rates[ $rate_id ] ) ) {
			$rate_cost = $rate_cost + $this->found_rates[ $rate_id ]['cost'];
			$packages  = 1 + $this->found_rates[ $rate_id ]['packages'];
		} else {
			$packages = 1;
		}

		// Sort.
		if ( isset( $this->custom_services[ $rate_code ]['order'] ) ) {
			$sort = $this->custom_services[ $rate_code ]['order'];
		} else {
			$sort = 999;
		}

		$this->found_rates[ $rate_id ] = array(
			'id'       => $rate_id,
			'label'    => $rate_name,
			'cost'     => $rate_cost,
			'sort'     => $sort,
			'packages' => $packages,
		);
	}

	/**
	 * Perform remote request to AU Post API and returns the response if succeed.
	 *
	 * @since 1.0.0
	 * @version 2.4.4
	 *
	 * @param string $endpoint Endpoint URL where the request is made into.
	 * @param string $request  Request args.
	 * @param array  $headers  Request headers.
	 *
	 * @return mixed Response.
	 */
	private function get_response( $endpoint, $request, $headers ) {
		// If response exists in the cache, returns it.
		if ( is_array( $this->rate_cache ) && isset( $this->rate_cache[ md5( $request ) ] ) ) {
			$response = $this->rate_cache[ md5( $request ) ];

			$this->debug( 'Using cached Australia Post REQUEST and RESPONSE.' );
			$this->debug_request_response( $request, $response );

			return $response;
		}

		$response = wp_remote_get(
			$endpoint . '?' . $request,
			array(
				'timeout' => 70,
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->debug( sprintf( 'Australia Post request error (%1$s): %2$s', $response->get_error_code(), $response->get_error_message() ), 'error' );
			return false;
		}

		$response = json_decode( $response['body'] );
		if ( is_null( $response ) ) {
			$this->debug( 'Unable to decode JSON body from Australia Post response.', 'error' );
			return false;
		}

		// Cache the result in case the same request is made again.
		if ( ! is_array( $this->rate_cache ) ) {
			$this->rate_cache = array();
		}
		$this->rate_cache[ md5( $request ) ] = $response;

		$this->debug_request_response( $request, $response );

		return $response;
	}

	/**
	 * Debug request and response.
	 *
	 * @since 2.4.4
	 * @version 2.4.4
	 *
	 * @param string $request  HTTP request to the Australia Post API.
	 * @param array  $response HTTP response from the Australia Post API.
	 */
	private function debug_request_response( $request, $response ) {
		// @codingStandardsChangeSetting WordPress.PHP.DevelopmentFunctions exclude error_log
		$this->debug( 'Australia Post REQUEST: <pre>' . print_r( htmlspecialchars( $request ), true ) . '</pre>' );
		$this->debug( 'Australia Post RESPONSE: <pre>' . print_r( $response, true ) . '</pre>' );
	}

	/**
	 * Sort_rates function.
	 *
	 * @param array $a Rate to sort.
	 * @param array $b Rate to sort.
	 *
	 * @return int
	 */
	public function sort_rates( $a, $b ) {
		if ( $a['sort'] === $b['sort'] ) {
			return 0;
		}

		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}

	/**
	 * Get header with auth key.
	 *
	 * @return array
	 */
	private function get_request_header() {
		return array(
			'AUTH-KEY' => $this->api_key,
		);
	}

	/**
	 * Get request array from package.
	 *
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function get_request( $package ) {
		$request = array();

		$request['from_postcode'] = str_replace( ' ', '', strtoupper( $this->origin ) );

		$request['country_code'] = $package['destination']['country'];

		if ( in_array( $package['destination']['country'], $this->au_territories ) ) {
			$request['to_postcode'] = str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) );
		}

		return $request;
	}

	/**
	 * Get_request function.
	 *
	 * @param array $package Current package.
	 * @return array
	 */
	private function get_package_requests( $package ) {

		// Choose selected packing.
		switch ( $this->packing_method ) {
			case 'weight':
				$requests = $this->weight_only_shipping( $package );
				break;
			case 'box_packing':
				$requests = $this->box_shipping( $package );
				break;
			case 'per_item':
			default:
				$requests = $this->per_item_shipping( $package );
				break;
		}

		return $requests;
	}

	/**
	 * Weight_only_shipping function.
	 *
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function weight_only_shipping( $package ) {
		if ( ! class_exists( 'WC_Boxpack' ) ) {
			include_once 'box-packer/class-wc-boxpack.php';
		}

		$packer = new WC_Boxpack();

		// Get weight of order.
		foreach ( $package['contents'] as $item_id => $values ) {

			/**
			 * Product object.
			 *
			 * @var WC_Product $values['data']
			 */

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return null;
			}

			$weight = wc_get_weight( $values['data']->get_weight(), 'kg' );
			$price  = $values['data']->get_price();
			for ( $i = 0; $i < $values['quantity']; $i++ ) {
				$packer->add_item( 0, 0, 0, $weight, $price );
			}
		}

		$box = $packer->add_box( 1, 1, 1, 0 );
		$box->set_max_weight( $this->max_weight );
		$packer->pack();
		$packages = $packer->get_packages();

		if ( count( $packages ) > 1 ) {
			$this->debug( __( 'Package is too heavy. Splitting.', 'woocommerce-shipping-australia-post' ), 'error' );
			$this->debug( 'Splitting into ' . count( $packages ) . ' packages.' );
		}

		$requests = array();
		foreach ( $packages as $p ) {
			$parcel                = array();
			$parcel['weight']      = str_replace( ',', '.', round( $p->weight, 2 ) );
			$parcel['extra_cover'] = ceil( $p->value );

			// Domestic parcels require dimensions.
			if ( ! $this->is_international ) {
				$dimension        = 1;
				$parcel['height'] = $dimension;
				$parcel['width']  = $dimension;
				$parcel['length'] = $dimension;
			}

			if ( $parcel['weight'] > 22 || $parcel['length'] > 105 ) {
				$this->debug( sprintf( __( 'Product %d has invalid weight/dimensions. Aborting. See <a href="http://auspost.com.au/personal/parcel-dimensions.html">http://auspost.com.au/personal/parcel-dimensions.html</a>', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return;
			}

			$requests[] = $parcel;
		}

		return $requests;
	}

	/**
	 * Generate shipping request based on individual product dimensions.
	 *
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function per_item_shipping( $package ) {
		$requests = array();

		// Get weight of order.
		foreach ( $package['contents'] as $item_id => $values ) {

			/**
			 * Product object.
			 *
			 * @var WC_Product $values['data']
			 */

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ) );
				continue;
			}

			if ( ! $values['data']->get_weight() || ! $values['data']->get_length() || ! $values['data']->get_height() || ! $values['data']->get_width() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is missing weight/dimensions. Aborting.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return $requests;
			}

			$parcel = array();

			$parcel['weight'] = wc_get_weight( $values['data']->get_weight(), 'kg' );

			$dimensions = array(
				wc_get_dimension( $values['data']->get_length(), 'cm' ),
				wc_get_dimension( $values['data']->get_height(), 'cm' ),
				wc_get_dimension( $values['data']->get_width(), 'cm' ),
			);

			sort( $dimensions );

			// Min sizes - girth minimum is 16.
			$girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];

			if ( $girth < 16 ) {
				if ( $dimensions[0] < 4 ) {
					$dimensions[0] = 4;
				}
				if ( $dimensions[1] < 5 ) {
					$dimensions[1] = 5;
				}
			}

			if ( $parcel['weight'] > 22 || $dimensions[2] > 105 ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product %d has invalid weight/dimensions. Aborting. See <a href="http://auspost.com.au/personal/parcel-dimensions.html">http://auspost.com.au/personal/parcel-dimensions.html</a>', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return $requests;
			}

			$parcel['height'] = $dimensions[0];
			$parcel['width']  = $dimensions[1];
			$parcel['length'] = $dimensions[2];

			$parcel['extra_cover'] = ceil( $values['data']->get_price() );

			for ( $i = 0; $i < $values['quantity']; $i++ ) {
				$requests[] = $parcel;
			}
		}

		return $requests;
	}

	/**
	 * Generate shipping requests by packing items in boxes.
	 *
	 * @since 2.4.5 Reset $request variable on each loop.
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function box_shipping( $package ) {
		$requests = array();

		if ( ! class_exists( 'WC_Boxpack' ) ) {
			include_once 'box-packer/class-wc-boxpack.php';
		}

		$boxpack = new WC_Boxpack( array( 'prefer_packets' => true ) );

		// Needed to ensure box packer works correctly.
		$boxes = $this->convert_letter_boxes_to_match_product_metrics( $this->get_all_boxes() );
		// Define boxes.
		if ( $boxes ) {
			foreach ( $boxes as $key => $box ) {

				if ( isset( $box['enabled'] ) && ! $box['enabled'] ) {
					continue;
				}
				$newbox = $boxpack->add_box( $box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight'] );
				$newbox->set_id( $box['name'] );

				if ( ! empty( $box['inner_length'] ) && ! empty( $box['inner_width'] ) && ! empty( $box['inner_height'] ) ) {
					$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );
				}

				if ( $box['max_weight'] ) {
					$newbox->set_max_weight( $box['max_weight'] );
				}

				if ( ! empty( $box['type'] ) ) {
					$newbox->set_type( $box['type'] );
				}
			}
		}

		// Add items.
		foreach ( $package['contents'] as $item_id => $values ) {

			/**
			 * Product object.
			 *
			 * @var WC_Product $values['data']
			 */

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ) );
				continue;
			}

			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() && $values['data']->get_weight() ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

				for ( $i = 0; $i < $values['quantity']; $i++ ) {

					$boxpack->add_item(
						wc_get_dimension( $dimensions[2], 'cm' ),
						wc_get_dimension( $dimensions[1], 'cm' ),
						wc_get_dimension( $dimensions[0], 'cm' ),
						wc_get_weight( $values['data']->get_weight(), 'kg' ),
						$values['data']->get_price()
					);
				}
			} else {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is missing weight/dimensions. Aborting.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return $requests;
			}
		}

		// Pack it.
		$boxpack->pack();

		// Get packages.
		$packages = $boxpack->get_packages();

		foreach ( $packages as $package ) {
			$this->debug(
				( $package->unpacked ? 'Unpacked Item ' : 'Packed ' ) . $package->id . ' - ' . $package->length . 'x' . $package->width . 'x' . $package->height
			);

			$request = array();

			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );

			if ( 'envelope' !== $package->type ) {
				$request['height'] = $dimensions[0];
				$request['weight'] = $package->weight;
				$request['width']  = $dimensions[1];
				$request['length'] = $dimensions[2];
			} else {
				// convert values back to what the API expects.
				// Changed in $this->convert_letter_boxes_to_match_product_metrics().
				$request['thickness'] = wc_get_dimension( $dimensions[0], 'cm', 'mm' );
				$request['width']     = wc_get_dimension( $dimensions[1], 'cm', 'mm' );
				$request['length']    = wc_get_dimension( $dimensions[2], 'cm', 'mm' );
				$request['weight']    = wc_get_weight( $package->weight, 'kg', 'g' );
			}

			$request['extra_cover'] = ceil( $package->value );

			$requests[] = $request;
		}

		return $requests;
	}
}
