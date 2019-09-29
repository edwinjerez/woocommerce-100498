<?php
/**
 * Parcelforce express24 rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_Parcelforce_Express_24 class.
 *
 * Up-to-date as of 03/15/2019 (no change from 2018 rates).
 * As per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 7.
 */
class RoyalMail_Rate_Parcelforce_Express_24 extends RoyalMail_Rate {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'parcelforce_express_24';

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
			100 => array(
				2000   => 1668,
				5000   => 1770,
				10000  => 2112,
				15000  => 2796,
				20000  => 3336,
				25000  => 4458,
				30000  => 4878,
			),
		),
	);

	/**
	 * Boxes for express9, 10, AM, 24, 48 – maximum length of 1.5m and 3m length/girth
	 * combined.
	 *
	 * Boxes express48large – maximum length of 2.5m and 5m length/girth combined.
	 *
	 * Include few variations of this box to cover odd shaped items.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array(
		'packet' => array(
			'length'   => 1500,
			'width'    => 750,
			'height'   => 750,
			'weight'   => 30000,
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

				$this->debug( __( 'Parcelforce Express 24 Delivery package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

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
		$quotes['parcelforce-express-24'] = $quote;

		return $quotes;
	}

}
