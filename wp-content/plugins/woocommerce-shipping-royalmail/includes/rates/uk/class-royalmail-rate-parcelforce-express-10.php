<?php
/**
 * Parcelforce express10 rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_Parcelforce_Express_10 class.
 *
 * Up-to-date as of 03/15/2019 (no change from 2018 rates).
 * As per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 7.
 */
class RoyalMail_Rate_Parcelforce_Express_10 extends RoyalMail_Rate {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'parcelforce_express_10';

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
			200 => array(
				2000   => 2982,
				5000   => 3084,
				10000  => 3426,
				15000  => 4104,
				20000  => 4644,
				25000  => 5772,
				30000  => 6192,
			),
		),
	);

	/**
	 * Boxes for express9, 10, AM, 24, 48 – maximum length of 1.5m and 3m length/girth
	 * combined.
	 * Boxes for express48large – maximum length of 2.5m and 5m length/girth combined.
	 *
	 * Include few variations of this box to cover odd shaped items.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array(
		'packet' => array(
			'length'   => 1500,  // Max length.
			'width'    => 750,   // Max width.
			'height'   => 750,   // Max height.
			'weight'   => 30000, // Max weight.
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

				$this->debug( __( 'Parcelforce Express 10 Delivery package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

				$bands   = $this->get_rate_bands();
				$matched = false;

				foreach ( $bands as $coverage => $weight_bands ) {
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

		$quotes                           = array();
		$quotes['parcelforce-express-10'] = $quote;

		return $quotes;
	}

}
