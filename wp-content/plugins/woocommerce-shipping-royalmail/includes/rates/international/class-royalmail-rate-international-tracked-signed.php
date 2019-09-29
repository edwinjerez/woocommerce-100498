<?php
/**
 * International-Tracked-and-Signed rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_International_Tracked_Signed class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 11.
 */
class RoyalMail_Rate_International_Tracked_Signed extends RoyalMail_Rate {

	/**
	 * ID/Name of rate
	 *
	 * @var string
	 */
	protected $rate_id = 'international_tracked_signed';

	/**
	 * List of countries that support Tracked and Signed service.
	 *
	 * @see http://www.royalmail.com/sites/default/files/Royal-Mail-International-Tracking-Signature-Services-List-April2017.pdf
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @var array
	 */
	protected $supported_countries = array(
		'AX',
		'AD',
		'AR',
		'AT',
		'BB',
		'BY',
		'BE',
		'BZ',
		'BG',
		'KH',
		'CA',
		'KY',
		'CK',
		'HR',
		'CY',
		'CZ',
		'DK',
		'EC',
		'FO',
		'FI',
		'FR',
		'GE',
		'DE',
		'GI',
		'GR',
		'GL',
		'HK',
		'HU',
		'IS',
		'ID',
		'IE',
		'IT',
		'JP',
		'LV',
		'LB',
		'LI',
		'LT',
		'LU',
		'MY',
		'MT',
		'MD',
		'NL',
		'NZ',
		'PL',
		'PT',
		'RO',
		'RU',
		'SM',
		'RS',
		'SG',
		'SK',
		'SI',
		'KR',
		'ES',
		'SE',
		'CH',
		'TH',
		'TO',
		'TT',
		'TR',
		'UG',
		'AE',
		'US',
		'VA',
	);

	/**
	 * Pricing bands - EU, NON EU, ZONE 1, Zone 2
	 *
	 * @var array
	 */
	protected $bands = array(
		'2018' => array(
			'letter' => array(
				10  => array( 600, 600, 600 ),
				20  => array( 600, 635, 635 ),
				100 => array( 650, 725, 725 ),
			),
			'large-letter' => array(
				100  => array( 785, 850, 870 ),
				250  => array( 830, 935, 965 ),
				500  => array( 910, 1115, 1150 ),
				750  => array( 960, 1245, 1290 ),
			),
			'packet' => array(
				100  => array( 895, 965, 990 ),
				250  => array( 905, 1015, 1050 ),
				500  => array( 1055, 1295, 1345 ),
				750  => array( 1160, 1515, 1580 ),
				1000 => array( 1255, 1735, 1815 ),
				1250 => array( 1350, 1850, 1960 ),
				1500 => array( 1445, 1970, 2110 ),
				1750 => array( 1505, 2060, 2225 ),
				2000 => array( 1525, 2110, 2300 ),
			),
		),
		'2019' => array(
			'letter' => array(
				10  => array( 610, 610, 610 ),
				20  => array( 610, 645, 645 ),
				100 => array( 655, 730, 730 ),
			),
			'large-letter' => array(
				100  => array( 800, 865, 885 ),
				250  => array( 845, 950, 980 ),
				500  => array( 880, 1085, 1120 ),
				750  => array( 915, 1200, 1245 ),
			),
			'packet' => array(
				100  => array( 925, 1000, 1025 ),
				250  => array( 934, 1055, 1090 ),
				500  => array( 1095, 1350, 1400 ),
				750  => array( 1205, 1580, 1650 ),
				1000 => array( 1285, 1780, 1860 ),
				1250 => array( 1350, 1845, 1955 ),
				1500 => array( 1435, 1935, 2075 ),
				1750 => array( 1470, 2010, 2170 ),
				2000 => array( 1490, 2035, 2220 ),
			),
		),
	);

	/**
	 * Shipping boxes
	 *
	 * @var array
	 */
	protected $default_boxes = array(
		'letter' => array(
			'length'   => 240, // Max L in mm.
			'width'    => 165, // Max W in mm.
			'height'   => 5,   // Max H in mm.
			'weight'   => 100,  // Max Weight in grams.
		),
		'large-letter' => array(
			'length'   => 353,
			'width'    => 250,
			'height'   => 25,
			'weight'   => 750,
		),
		'long-parcel' => array(
			'length'   => 600,
			'width'    => 150,
			'height'   => 150,
			'weight'   => 2000,
		),
		'square-parcel' => array(
			'length'   => 300,
			'width'    => 300,
			'height'   => 300,
			'weight'   => 2000,
		),
		'parcel' => array(
			'length'   => 450,
			'width'    => 225,
			'height'   => 225,
			'weight'   => 2000,
		),
	);

	/**
	 * Fixed compensation
	 *
	 * @var string
	 */
	private $compensation    = '250';

	/**
	 * Get quotes for this rate.
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $destination Address to ship to.
	 * @param  array  $boxes User-defined boxes.
	 * @return array
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array() ) {
		if ( ! in_array( $destination, $this->supported_countries ) ) {
			return array();
		}

		$class_quote = false;

		if ( ! empty( $boxes ) ) {
			$this->boxes = array();

			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
					'weight'     => 2000,
				);
			}
		} else {
			$this->boxes = $this->default_boxes;
		}

		$zone     = $this->get_zone( $destination );
		$packages = $this->get_packages( $items, $packing_method );

		if ( $packages ) {
			foreach ( $packages as $package ) {

				if ( 'letter' !== $package->id && 'large-letter' !== $package->id ) {
					$package->id = 'packet';
				}

				if ( 'packet' === $package->id && 900 < ( $package->length + $package->width + $package->height ) ) {
					return false; // Exceeding parcels requirement, unpacked.
				}

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$this->debug( __( 'International tracked and signed package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

				$bands   = $this->get_rate_bands( $package->id );
				$quote   = 0;
				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( $package->weight <= $band ) {
						switch ( $zone ) {
							case 'EU' :
							case 'EUR' :
								$quote += $value[0];
								break;
							case '1' :
								$quote += $value[1];
								break;
							case '2' :
								$quote += $value[2];
								break;
						}
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return;
				}

				$class_quote  += $quote;

				if ( $package->value > 50 ) {
					$class_quote += $this->compensation;
				}
			}
		}

		// Return pounds.
		$quotes = array();
		$quotes['international-tracked-signed'] = $class_quote / 100;

		return $quotes;
	}
}
