<?php
/**
 * Parcelforce express48 rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_Parcelforce_Express_48 class.
 *
 * Up-to-date as of 03/15/2019 (no change from 2018 rates).
 * As per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 7.
 */
class RoyalMail_Rate_Parcelforce_Express_48 extends RoyalMail_Rate {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'parcelforce_express_48';

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
				2000   => 1212,
				5000   => 1314,
				10000  => 1662,
				15000  => 2340,
				20000  => 2880,
				25000  => 4008,
				30000  => 4422,
			),
		),
	);

	/**
	 * Boxes for express9, 10, AM, 24, 48 – maximum length of 1.5m and 3m length/girth
	 * combined
	 *
	 * Boxes express48large – maximum length of 2.5m and 5m length/girth combined.
	 *
	 * Include few variations of this box to cover odd shaped items.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array(
		'packet' => array(
			'length'   => 2500,
			'width'    => 1250,
			'height'   => 1250,
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

				$this->debug( __( 'Parcelforce Express 48 Delivery package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

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
		$quotes['parcelforce-express-48'] = $quote;

		return $quotes;
	}

}
