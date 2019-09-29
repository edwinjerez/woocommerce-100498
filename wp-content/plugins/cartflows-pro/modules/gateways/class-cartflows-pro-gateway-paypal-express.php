<?php
/**
 * Paypal Gateway.
 *
 * @package cartflows
 */

/**
 * Class Cartflows_Pro_Gateway_Paypal_Express.
 */
class Cartflows_Pro_Gateway_Paypal_Express extends Cartflows_Pro_Paypal_Gateway_helper {

	/**
	 * Member Variable.
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Key name variable.
	 *
	 * @var key
	 */
	public $key = 'ppec_paypal';

	/**
	 *  Initiator.
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

		add_filter( 'woocommerce_paypal_express_checkout_request_body', array( $this, 'modify_paypal_arguments' ), 999 );
	}

	/**
	 * Modify paypal arguements to set paramters before checkout express.
	 *
	 * @param array $data parameters array.
	 * @return array
	 */
	public function modify_paypal_arguments( $data ) {

		wcf()->logger->log( __CLASS__ . '::' . __FUNCTION__ . ' : Entering ' );

		// translators: blog name.
		$description = sprintf( _x( 'Orders with %s', 'data sent to PayPal', 'cartflows-pro' ), get_bloginfo( 'name' ) );

		$description = html_entity_decode( $description, ENT_NOQUOTES, 'UTF-8' );

		if ( true === wcf_pro()->utils->is_reference_transaction() && $data && isset( $data['METHOD'] ) && 'SetExpressCheckout' == $data['METHOD'] && ! isset( $data['L_BILLINGTYPE0'] ) ) {
			$data['RETURNURL']                      = add_query_arg( array( 'create-billing-agreement' => true ), $data['RETURNURL'] );
			$data['L_BILLINGTYPE0']                 = 'MerchantInitiatedBillingSingleAgreement';
			$data['L_BILLINGAGREEMENTDESCRIPTION0'] = $description;
			$data['L_BILLINGAGREEMENTCUSTOM0']      = '';
		}

		if ( true === wcf_pro()->utils->is_reference_transaction() && $data && isset( $data['METHOD'] ) && 'DoReferenceTransaction' == $data['METHOD'] ) {

			$step_id       = isset( $_POST['step_id'] ) ? (int) $_POST['step_id'] : '';
			$order_id      = isset( $_POST['order_id'] ) ? (int) $_POST['order_id'] : '';
			$order         = wc_get_order( $order_id );
			$offer_package = wcf_pro()->utils->get_offer_data( $step_id );

			/**
			 * If we do not have the current order set that means its not the upsell accept call but the call containing subscriptions.
			 */
			$data['AMT']     = $offer_package['total'];
			$data['ITEMAMT'] = $offer_package['total'];

			// We setup shippingamt as 0.
			if ( ( isset( $offer_package['shipping'] ) && isset( $offer_package['shipping']['diff'] ) ) && 0 < $offer_package['shipping']['diff'] ) {
				$data['SHIPPINGAMT'] = 0;
				$data['SHIPDISCAMT'] = ( isset( $offer_package['shipping'] ) && isset( $offer_package['shipping']['diff'] ) ) ? $offer_package['shipping']['diff']['cost'] : 0;

			} else {
				$data['SHIPPINGAMT'] = ( isset( $offer_package['shipping'] ) && isset( $offer_package['shipping']['diff'] ) ) ? $offer_package['shipping']['diff']['cost'] : 0;
			}

			$data['TAXAMT']       = ( isset( $offer_package['taxes'] ) ) ? $offer_package['taxes'] : 0;
			$data['INVNUM']       = 'WC-' . $order->get_id();
			$data['INSURANCEAMT'] = 0;
			$data['HANDLINGAMT']  = 0;
			$data                 = $this->remove_previous_line_items( $data );

			$data['L_NAME'] = $offer_package['name'];
			$data['L_DESC'] = $offer_package['desc'];
			$data['L_AMT']  = wc_format_decimal( $offer_package['price'], 2 );
			$data['L_QTY']  = $offer_package['qty'];

			$item_amt = $offer_package['total'];

			$data['ITEMAMT'] = $item_amt;
		}

		if ( true === wcf_pro()->utils->is_reference_transaction() && isset( $data['METHOD'] ) && 'DoExpressCheckoutPayment' == $data['METHOD'] ) {

			if ( isset( $data['PAYMENTREQUEST_0_CUSTOM'] ) ) {
				$get_custom_attrs = json_decode( $data['PAYMENTREQUEST_0_CUSTOM'] );
				if ( isset( $get_custom_attrs->order_id ) ) {
					$get_order = wc_get_order( $get_custom_attrs->order_id );

					try {
						$checkout         = wc_gateway_ppec()->checkout;
						$checkout_details = $checkout->get_checkout_details( $data['TOKEN'] );

						$checkout->create_billing_agreement( $get_order, $checkout_details );

						$token = $get_order->get_meta( '_ppec_billing_agreement_id' );

						if ( ! empty( $token ) ) {

							// Saving meta by our own.
							update_post_meta( $get_custom_attrs->order_id, '_ppec_billing_agreement_id', $token );
						}
					} catch ( Exception $e ) {
						wcf()->logger->log( 'Order #' . $get_custom_attrs->order_id . ': Unable to create a token for express checkout for order' );
						wcf()->logger->log( 'Details Below: ' . print_r( $e->getMessage(), true ) );
					}
				}
			}
		}

		return $data;

	}

	/**
	 * Processes offer payment.
	 *
	 * @param array $order order details.
	 * @param array $product product details.
	 * @return bool
	 */
	public function process_offer_payment( $order, $product ) {

		$is_successful = false;
		try {

			$client   = wc_gateway_ppec()->client;
			$order_id = $order->get_id();

			$environment = $this->wc_gateway()->settings['environment'];
			$api_prefix  = '';

			if ( 'sandbox' === $environment ) {
				$api_prefix = 'sandbox_';
			}

			$this->setup_api_vars(
				$this->key,
				$environment,
				$this->wc_gateway()->get_option( $api_prefix . 'api_username' ),
				$this->wc_gateway()->get_option( $api_prefix . 'api_password' ),
				$this->wc_gateway()->get_option( $api_prefix . 'api_signature' )
			);

			$this->add_reference_trans_args( $this->get_token( $order ), $order, array(), $product );

			$this->add_credentials_param( $this->api_username, $this->api_password, $this->api_signature, 124 );

			$request         = new stdClass();
			$request->path   = '';
			$request->method = 'POST';
			$request->body   = $this->to_string();

			$response = $this->perform_request( $request );

			if ( $this->has_error_api_response( $response ) ) {
				wcf()->logger->log( 'PayPal DoReferenceTransactionCall Failed' );
				wcf()->logger->log( print_r( $response, true ) );
				$is_successful = false;

			} else {

				$is_successful = true;
			}
		} catch ( Exception $e ) {

			// translators: exception message.
			$order_note = sprintf( __( 'paypal Exp Transaction Failed (%s)', 'cartflows-pro' ), $e->getMessage() );
		}

		return $is_successful;
	}

	/**
	 * Get WooCommerce payment geteways.
	 *
	 * @return array
	 */
	public function wc_gateway() {

		global $woocommerce;

		$gateways = $woocommerce->payment_gateways->payment_gateways();

		return $gateways[ $this->key ];
	}

	/**
	 * Charge a payment against a reference token.
	 *
	 * @param string   $reference_id the ID of a reference object, e.g. billing agreement ID.
	 * @param WC_Order $order order object.
	 * @param array    $args arguments data.
	 * @param array    $offer_product offer product data.
	 * @since 1.0.0
	 */
	public function add_reference_trans_args( $reference_id, $order, $args = array(), $offer_product ) {

		$defaults = array(
			'amount'               => $offer_product['total'],
			'payment_type'         => 'Any',
			'payment_action'       => 'Sale',
			'return_fraud_filters' => 1,
			'notify_url'           => WC()->api_request_url( 'WC_Gateway_Paypal' ),
			'invoice_number'       => $order->get_id() . '-' . $offer_product['step_id'],
		);

		$args = wp_parse_args( $args, $defaults );

		$this->set_method( 'DoReferenceTransaction' );

		// Set base params.
		$this->add_parameters(
			array(
				'REFERENCEID'      => $reference_id,
				'BUTTONSOURCE'     => 'WooThemes_Cart',
				'RETURNFMFDETAILS' => $args['return_fraud_filters'],
			)
		);

		$this->add_payment_params( $order, $offer_product['step_id'], $args['payment_action'], true, true );
	}

	/**
	 * Limits description to 120 characters.
	 *
	 * @param string $description limit description.
	 * @return string
	 * @since 1.0.0
	 */
	private function limit_description( $description ) {

		$description = substr( $description, 0, 120 );

		return $description;
	}

	/**
	 * Return the parsed response object for the request
	 *
	 * @since 1.0.0
	 *
	 * @param string $raw_response_body raw response.
	 *
	 * @return object
	 */
	protected function get_parsed_response( $raw_response_body ) {

		wp_parse_str( urldecode( $raw_response_body ), $this->response_params );

		return $this->response_params;
	}

	/**
	 * Get billing agreement ID for paypal express.
	 *
	 * @since 1.0.0
	 *
	 * @param array $order order data.
	 *
	 * @return string
	 */
	public function get_token( $order ) {

		$get_id = $order->get_id();

		$token = $order->get_meta( '_ppec_billing_agreement_id' );
		if ( '' == $token ) {
			$token = get_post_meta( $get_id, '_ppec_billing_agreement_id', true );
		}
		if ( ! empty( $token ) ) {
			return $token;
		}

		return false;
	}

	/**
	 * Remove line items
	 *
	 * @since 1.0.0
	 *
	 * @param array $array object.
	 *
	 * @return array
	 */
	public function remove_previous_line_items( $array ) {

		if ( is_array( $array ) && count( $array ) > 0 ) {
			foreach ( $array as $key => $val ) {
				if ( false !== strpos( strtoupper( $key ), 'L_' ) ) {
					unset( $array[ $key ] );
				}
			}
		}

		return $array;
	}
}

/**
 *  Prepare if class 'Cartflows_Pro_Gateway_Paypal_Express' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Gateway_Paypal_Express::get_instance();
