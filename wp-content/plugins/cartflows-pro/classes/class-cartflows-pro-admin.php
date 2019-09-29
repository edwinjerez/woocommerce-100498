<?php
/**
 * Cartflows Admin.
 *
 * @package cartflows
 */

/**
 * Class Cartflows_Pro_Admin.
 */
class Cartflows_Pro_Admin {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
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

		add_action( 'cartflows_global_admin_scripts', array( $this, 'global_scripts' ) );
		add_action( 'cartflows_admin_meta_scripts', array( $this, 'meta_scripts' ) );
		add_filter( 'cartflows_licence_args', array( $this, 'licence_args' ) );
		add_action( 'cartflows_after_settings_fields', array( $this, 'add_settings_fields' ) );
		add_filter( 'cartflows_common_settings_default', array( $this, 'set_default_settings' ) );

		add_action( 'admin_notices', array( $this, 'payment_gateway_support_notice' ) );

		// Change String of Offer Item Meta.
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'change_order_item_meta_title' ), 20, 3 );

		// Hide Order Bump Metadata from the order list.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'custom_woocommerce_hidden_order_itemmeta' ), 10, 1 );

		/* Add pro version class to body */
		add_action( 'admin_body_class', array( $this, 'add_admin_pro_body_class' ) );
	}

	/**
	 * License arguments for Rest API Request.
	 *
	 * @param  array $defaults License arguments.
	 * @return array           License arguments.
	 */
	function licence_args( $defaults ) {

		$data = get_option( 'wc_am_client_cartflows_api_key', array() );

		$licence_key = isset( $data['api_key'] ) ? esc_attr( $data['api_key'] ) : '';

		$args = array(
			'request'     => 'status',
			'product_id'  => CARTFLOWS_PRO_PRODUCT_TITLE,
			'instance'    => CartFlows_Pro_Licence::get_instance()->wc_am_instance_id,
			'object'      => CartFlows_Pro_Licence::get_instance()->wc_am_domain,
			'licence_key' => $licence_key,
		);

		return apply_filters( 'cartflows_pro_licence_args', wp_parse_args( $args, $defaults ) );
	}

	/**
	 * Redirect to thank page if upsell not exists
	 *
	 * Global Admin Styles.
	 *
	 * @since 1.0.0
	 */
	function global_scripts() {
		// Styles.
		wp_enqueue_style( 'cartflows-pro-global-admin', CARTFLOWS_PRO_URL . 'admin/assets/css/global-admin.css', array(), CARTFLOWS_PRO_VER );
		// Script.
		wp_enqueue_script( 'cartflows-pro-global-admin', CARTFLOWS_PRO_URL . 'admin/assets/js/global-admin.js', array( 'jquery' ), CARTFLOWS_PRO_VER );
	}

	/**
	 * Redirect to thank page if upsell not exists
	 *
	 * Global Admin Scripts.
	 *
	 * @since 1.0.0
	 */
	function meta_scripts() {

		wp_enqueue_script(
			'wcf-pro-admin-meta',
			CARTFLOWS_PRO_URL . 'admin/meta-assets/js/admin-edit.js',
			array( 'jquery' ),
			CARTFLOWS_PRO_VER,
			true
		);
	}

	/**
	 * Add setting fields in admin section
	 *
	 * @param array $settings settings array.
	 * @since 1.0.0
	 */
	function add_settings_fields( $settings ) {

		if ( ! wcf_pro()->is_woo_active ) {
			return;
		}
		echo Cartflows_Admin_Fields::checkobox_field(
			array(
				'id'    => 'wcf_paypal_reference_transactions',
				'name'  => '_cartflows_common[paypal_reference_transactions]',
				'title' => __( 'Enable PayPal Reference Transactions', 'cartflows-pro' ),
				'value' => $settings['paypal_reference_transactions'],
			)
		);
	}

	/**
	 * Get active payement gateways.
	 *
	 * @since 1.0.0
	 */
	public function get_active_payment_gateways() {

		$enabled_gateways = array();

		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		if ( isset( $available_gateways ) ) {

			foreach ( $available_gateways as $key => $gateway ) {

				if ( 'yes' == $gateway->enabled ) {
					$enabled_gateways[] = $key;
				}
			}

			if ( in_array( 'paypal', $enabled_gateways, false ) && in_array( 'stripe', $enabled_gateways, false ) ) {
				return false;
			} else {
				return true;
			}
		}

		return true;
	}

	/**
	 * Add notice for payement gateway support.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function payment_gateway_support_notice() {

		$is_payment_gateway_supported = '';

		if ( ! _is_wcf_base_offer_type() || ! wcf_pro()->is_woo_active ) {
			return;
		}

		$is_payment_gateway_supported = $this->get_active_payment_gateways();
		if ( $is_payment_gateway_supported ) {

			$class   = 'notice notice-info is-dismissible';
			$message = __( "CartFlows upsells / downsells works with PayPal & Stripe. We're adding support for other payment gateways soon!", 'cartflows-pro' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}
	}

	/**
	 * Set default options for settings.
	 *
	 * @param array $settings settings data.
	 * @since 1.0.0
	 */
	public function set_default_settings( $settings ) {

		$settings['paypal_reference_transactions'] = 'disable';

		return $settings;
	}

	/**
	 * Hide order meta-data from order list backend.
	 *
	 * @param array $arr order meta data.
	 * @return array
	 * @since 1.0.0
	 */
	function custom_woocommerce_hidden_order_itemmeta( $arr ) {
		$arr[] = '_cartflows_step_id';
		return $arr;
	}

	/**
	 * Changing a meta title
	 *
	 * @param  string        $key  The meta key.
	 * @param  WC_Meta_Data  $meta The meta object.
	 * @param  WC_Order_Item $item The order item object.
	 * @return string        The title.
	 */
	function change_order_item_meta_title( $key, $meta, $item ) {

		if ( '_cartflows_upsell' === $meta->key ) {
			$key = __( 'Upsell Offer', 'cartflows-pro' );
		} elseif ( '_cartflows_downsell' === $meta->key ) {
			$key = __( 'Downsell Offer', 'cartflows-pro' );
		}

		return $key;
	}

	/**
	 * Admin body classes.
	 *
	 * Body classes to be added to <body> tag in admin page
	 *
	 * @param String $classes body classes returned from the filter.
	 * @return String body classes to be added to <body> tag in admin page
	 */
	public static function add_admin_pro_body_class( $classes ) {

		$classes .= ' cartflows-pro-' . CARTFLOWS_PRO_VER;

		return $classes;
	}
}

/**
 *  Prepare if class 'Cartflows_Pro_Admin' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Admin::get_instance();
