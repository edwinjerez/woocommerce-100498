<?php
/**
 * Special Delivery Guaranteed by 9am rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_Special_Delivery_9am class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See UK Guaranteed page 5.
 */
class RoyalMail_Rate_Special_Delivery_9am extends RoyalMail_Rate {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'special_delivery_9am';

	/**
	 * Pricing bands.
	 *
	 * Key is coverage / compensation for loss or damage and value is key-value
	 * array where key is weight (up to and including) and value is the price
	 * in penny.
	 *
	 * @var array
	 */
	protected $bands = array(
		'2018' => array(
			50 => array(
				100   => 1836,
				500   => 2076,
				1000  => 2250,
				2000  => 2694,
			),
			1000 => array(
				100   => 2056,
				500   => 2296,
				1000  => 2470,
				2000  => 2914,
			),
			'more' => array(
				100   => 2406,
				500   => 2646,
				1000  => 2820,
				2000  => 3264,
			),
		),
		'2019' => array(
			50 => array(
				100   => 1928,
				500   => 2180,
				1000  => 2363,
				2000  => 2829,
			),
			1000 => array(
				100   => 2159,
				500   => 2411,
				1000  => 2594,
				2000  => 3060,
			),
			'more' => array(
				100   => 2526,
				500   => 2778,
				1000  => 2961,
				2000  => 3427,
			),
		),
	);

	/**
	 * Shipping boxes.
	 *
	 * @var array
	 */
	protected $boxes = array(
		'packet' => array(
			'length'   => 610,  // Max length.
			'width'    => 460,  // Max width.
			'height'   => 460,  // Max height.
			'weight'   => 2000, // Max weight.
		),
	);

	/**
	 * Get quotes for this rate.
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $destination Address to ship to.
	 * @return array
	 */
	public function get_quotes( $items, $packing_method, $destination ) {
		$quote    = false;
		$packages = $this->get_packages( $items, $packing_method );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( empty( $package->id ) ) {
					// Try a tube or fail.
					if ( $package->length < 900 && $package->length + ( $package->width * 2 ) < 1040 ) {
						$package->id = 'packet';
					} else {
						return false; // Unpacked item.
					}
				}

				$this->debug( __( 'Special Delivery package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

				$bands   = $this->get_rate_bands();
				$matched = false;

				foreach ( $bands as $coverage => $weight_bands ) {
					if ( is_numeric( $coverage ) && $package->value > $coverage ) {
						continue;
					}
					foreach ( $weight_bands as $weight => $value ) {

						if ( is_numeric( $weight ) && $package->weight <= $weight ) {
							$quote += $value;
							$matched = true;
							break 2;
						}
					}
				}

				if ( ! $matched ) {
					return;
				}
			}
		}

		// Rates include 20% VAT.
		$quote = $quote / 1.2;
		$quote = $quote / 100;

		$quotes                         = array();
		$quotes['special-delivery-9am'] = $quote;

		return $quotes;
	}
}
