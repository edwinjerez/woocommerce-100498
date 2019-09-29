<?php
/**
 * International Signed rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_International_Signed class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 14.
 */
class RoyalMail_Rate_International_Signed extends RoyalMail_Rate {

	/**
	 * ID/Name of rate
	 *
	 * @var string
	 */
	protected $rate_id = 'international_signed';

	/**
	 * List of countries that support Signed service.
	 *
	 * @see http://www.royalmail.com/sites/default/files/Royal-Mail-International-Tracking-Signature-Services-List-April2017.pdf
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @var array
	 */
	protected $supported_countries = array(
		'AF',
		'AL',
		'DZ',
		'AO',
		'AI',
		'AG',
		'AM',
		'AW',
		'AU',
		'AZ',
		'BS',
		'BH',
		'BD',
		'BJ',
		'BM',
		'BT',
		'BO',
		'BQ',
		'BA',
		'BW',
		'BR',
		'IO',
		'VG',
		'BN',
		'BF',
		'BI',
		'CM',
		'CV',
		'CF',
		'TD',
		'CL',
		'CN',
		'CX',
		'CO',
		'KM',
		'CG',
		'CD',
		'CR',
		'CU',
		'CW',
		'DJ',
		'DM',
		'DO',
		'EG',
		'SV',
		'GQ',
		'ER',
		'EE',
		'ET',
		'FK',
		'FJ',
		'GF',
		'PF',
		'TF',
		'GA',
		'GM',
		'GH',
		'GD',
		'GP',
		'GT',
		'GN',
		'GW',
		'GY',
		'HT',
		'HN',
		'IN',
		'IR',
		'IQ',
		'IL',
		'CI',
		'JM',
		'JO',
		'KZ',
		'KE',
		'KI',
		'KW',
		'KG',
		'LA',
		'LS',
		'LR',
		'LY',
		'MO',
		'MK',
		'MG',
		'YT',
		'MW',
		'MV',
		'ML',
		'MQ',
		'MR',
		'MU',
		'MX',
		'MN',
		'ME',
		'MS',
		'MA',
		'MZ',
		'MM',
		'NA',
		'NR',
		'NP',
		'NC',
		'NI',
		'NE',
		'NG',
		'NU',
		'KP',
		'NO',
		'OM',
		'PK',
		'PW',
		'PA',
		'PG',
		'PY',
		'PE',
		'PH',
		'PN',
		'PR',
		'QA',
		'RE',
		'RW',
		'ST',
		'SA',
		'SN',
		'SC',
		'SL',
		'SB',
		'ZA',
		'SS',
		'LK',
		'BQ',
		'SH',
		'KN',
		'LC',
		'MF',
		'SX',
		'VC',
		'SD',
		'SR',
		'SZ',
		'SY',
		'TW',
		'TJ',
		'TZ',
		'TL',
		'TG',
		'TK',
		'TN',
		'TM',
		'TC',
		'TV',
		'UA',
		'UY',
		'UZ',
		'VU',
		'VE',
		'VN',
		'WF',
		'EH',
		'WS',
		'YE',
		'ZM',
		'ZW',
	);

	/**
	 * Pricing bands - EU, ZONE 1, Zone 2.
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
				100 => array( 785, 850, 870 ),
				250 => array( 830, 935, 965 ),
				500 => array( 910, 1115, 1150 ),
				750 => array( 960, 1245, 1290 ),
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
				100 => array( 800, 865, 885 ),
				250 => array( 845, 950, 980 ),
				500 => array( 880, 1085, 1120 ),
				750 => array( 915, 1200, 1245 ),
			),
			'packet' => array(
				100  => array( 925, 1000, 1025 ),
				250  => array( 935, 1055, 1090 ),
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
	 * Get quotes for this rate
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $destination Address to ship to.
	 * @param  array  $boxes User-defined boxes.
	 *
	 * @return array
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array() ) {
		if ( ! in_array( $destination, $this->supported_countries ) ) {
			return array();
		}

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

		$packages    = $this->get_packages( $items, $packing_method );
		$class_quote = false;

		if ( $packages ) {
			foreach ( $packages as $package ) {
				$class_quote += $this->get_quote( $package , $destination );
			}
		}

		// Return pounds.
		$quotes = array();
		$quotes['international-signed'] = $class_quote / 100;

		return $quotes;
	}

	/**
	 * Get quote.
	 *
	 * @since 2.5.1
	 * @version 2.5.1
	 *
	 * @param array  $package     Package to ship.
	 * @param string $destination Destination.
	 *
	 * @return bool|int|void
	 */
	public function get_quote( $package, $destination ) {

		$zone = $this->get_zone( $destination );

		if ( 'letter' !== $package->id && 'large-letter' !== $package->id ) {
			$package->id = 'packet';
		}

		if ( 'packet' === $package->id && 900 < ( $package->length + $package->width + $package->height ) ) {
			return false; // Exceeding parcels requirement, unpacked.
		}

		if ( ! $this->get_rate_bands( $package->id ) ) {
			return false; // Unpacked item.
		}

		$this->debug( __( 'International signed package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

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
		return $quote;
	}
}
