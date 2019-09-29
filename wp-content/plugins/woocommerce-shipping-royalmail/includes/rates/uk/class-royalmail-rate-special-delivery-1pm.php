<?php
/**
 * Special Delivery Guaranteed by 1pm rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_Special_Delivery_1pm class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See UK Guaranteed page 5.
 */
class RoyalMail_Rate_Special_Delivery_1pm extends RoyalMail_Rate {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'special_delivery_1pm';

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
			500 => array(
				100   => 650,
				500   => 730,
				1000  => 860,
				2000  => 1100,
				10000 => 2660,
				20000 => 4120,
			),
			1000 => array(
				100   => 750,
				500   => 830,
				1000  => 960,
				2000  => 1200,
				10000 => 2760,
				20000 => 4220,
			),
			'more' => array(
				100   => 950,
				500   => 1030,
				1000  => 1160,
				2000  => 1400,
				10000 => 2960,
				20000 => 4420,
			),
		),
		'2019' => array(
			500 => array(
				100   => 660,
				500   => 740,
				1000  => 870,
				2000  => 1100,
				10000 => 2660,
				20000 => 4120,
			),
			1000 => array(
				100   => 760,
				500   => 840,
				1000  => 970,
				2000  => 1200,
				10000 => 2760,
				20000 => 4220,
			),
			'more' => array(
				100   => 960,
				500   => 1040,
				1000  => 1170,
				2000  => 1400,
				10000 => 2960,
				20000 => 4420,
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
			'length'   => 610,   // Max length.
			'width'    => 460,   // Max width.
			'height'   => 460,   // Max height.
			'weight'   => 20000, // Max weight.
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

		// Return pounds.
		$quotes                         = array();
		$quotes['special-delivery-1pm'] = $quote / 100;

		return $quotes;
	}
}
