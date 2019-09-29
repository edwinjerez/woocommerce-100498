<?php
/**
 * RoyalMail rates.
 *
 * @package WC_RoyalMail
 */

/**
 * WC_Shipping_Royalmail_Rates class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_Royalmail_Rates {

	/**
	 * List of items to ship.
	 *
	 * @var array
	 */
	private $items;

	/**
	 * Destination.
	 *
	 * @var array
	 */
	private $destination;

	/**
	 * User-defined boxes.
	 *
	 * @var array
	 */
	private $boxes;

	/**
	 * Available services for RoyalMail.
	 *
	 * @var array
	 */
	private $services = array(
		'uk' => array(
			'first-class',
			'second-class',
			'first-class-signed',
			'second-class-signed',
			'special-delivery-9am',
			'special-delivery-1pm',
			'parcelforce-express-9',
			'parcelforce-express-10',
			'parcelforce-express-am',
			'parcelforce-express-24',
			'parcelforce-express-48',

		),
		'international' => array(
			'international-tracked',
			'international-tracked-signed',
			'international-standard',
			'international-economy',
			'international-signed',
			'parcelforce-globaleconomy',
			'parcelforce-globalexpress',
			'parcelforce-globalpriority',
			'parcelforce-globalvalue',
		),
	);

	/**
	 * Constructor.
	 *
	 * @param array  $package        Package to ship.
	 * @param string $packing_method Packing method.
	 * @param array  $boxes          User-defined boxes.
	 */
	public function __construct( $package, $packing_method, $boxes = array(), $instance_id = '' ) {
		if ( ! class_exists( 'RoyalMail_Rate' ) ) {
			include_once 'rates/abstract-class-royalmail-rate.php';
		}

		if ( ! class_exists( 'Parcelforce_Rate' ) ) {
			include_once 'rates/abstract-class-parcelforce-rate.php';
		}

		if ( ! class_exists( 'WC_Boxpack' ) ) {
	  		include_once 'box-packer/class-wc-boxpack.php';
		}

		$this->items          = $this->get_items( $package );
		$this->destination    = $package['destination']['country'];
		$this->packing_method = $packing_method;
		$this->boxes          = $boxes;
		$this->instance_id    = $instance_id;
	}

	/**
	 * Output a message.
	 *
	 * @param string $message Message.
	 * @param string $type    Message type.
	 */
	public function debug( $message, $type = 'notice' ) {
		if ( defined( 'WC_ROYALMAIL_DEBUG' ) && WC_ROYALMAIL_DEBUG ) {
			wc_add_notice( $message, $type );
		}
	}

	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}

	/**
	 * Get quotes.
	 *
	 * @since 1.0.0
	 * @version 2.5.3
	 *
	 * @return array Quotes.
	 */
	public function get_quotes() {
		if ( empty( $this->items ) ) {
			return;
		}

		$quotes = array();
		foreach ( $this->get_services() as $service ) {
			$class = $this->get_service_class_name( $service );
			$file  = $this->get_service_class_path( $service );

			if ( file_exists( $file ) ) {
				include_once( $file );
			}

			if ( class_exists( $class ) ) {
				$service_class = new $class();
				$quotes        = array_merge(
					$quotes,
					(array) $service_class->get_quotes(
						$this->items,
						$this->packing_method,
						$this->destination,
						$this->boxes,
						$this->instance_id
					)
				);
			}
		}

		return array_filter( $quotes );
	}

	/**
	 * Get services from a given destination.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @return array Services.
	 */
	public function get_services() {
		return $this->services[ $this->get_service_type() ];
	}

	/**
	 * Get service type.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @return string Service type ('uk' or 'international').
	 */
	public function get_service_type() {
		return 'GB' === $this->destination ? 'uk' : 'international';
	}

	/**
	 * Get class path of a given service.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param string $service Service slug. See `self::services`.
	 *
	 * @return string Class filepath
	 */
	public function get_service_class_path( $service ) {
		return sprintf(
			'%s/includes/rates/%s/class-royalmail-rate-%s.php',
			$this->plugin_path(),
			$this->get_service_type(),
			str_replace( '_', '-', $service )
		);
	}

	/**
	 * Get class name of a given service.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param string $service Service slug. See `self::services`.
	 *
	 * @return string Class name
	 */
	public function get_service_class_name( $service ) {
		return 'RoyalMail_Rate_' . str_replace( '-', '_', ucwords( $service, '-' ) );
	}

	/**
	 * Get items from a given package.
	 *
	 * @param mixed $package Package to ship.
	 * @return array Items.
	 */
	private function get_items( $package ) {
	    $requests = array();

		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				/* translators: product item ID */
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-royalmail' ), $item_id ) );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				/* translators: product item ID */
	    		$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'woocommerce-shipping-royalmail' ), $item_id ), 'error' );
	    		return;
			}

			$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

			sort( $dimensions );

			$item            = new stdClass();
			$item->weight    = wc_get_weight( $values['data']->get_weight(), 'g' );
			$item->length    = wc_get_dimension( $dimensions[2], 'mm' );
			$item->width     = wc_get_dimension( $dimensions[1], 'mm' );
			$item->height    = wc_get_dimension( $dimensions[0], 'mm' );
			$item->qty       = $values['quantity'];
			$item->value     = $values['data']->get_price();

			$requests[] = $item;
		}

		return $requests;
	}
}
