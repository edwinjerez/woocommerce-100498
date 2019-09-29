<?php
/**
 * International Standard rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_International_Standard class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 10.
 */
class RoyalMail_Rate_International_Standard extends RoyalMail_Rate {

	/**
	 * ID/Name of rate
	 *
	 * @var string
	 */
	protected $rate_id = 'international_standard';

	/**
	 * Pricing bands - EU, NON EU, ZONE 1/Zone 2
	 *
	 * @var array
	 */
	protected $bands = array(
		'2018' => array(
			'letter' => array(
				10  => array( 125, 125, 125 ),
				20  => array( 125, 145, 145 ),
				100 => array( 155, 225, 225 ),
			),
			'large-letter' => array(
				100 => array( 265, 330, 345 ),
				250 => array( 385, 485, 515 ),
				500 => array( 490, 710, 750 ),
				750 => array( 595, 915, 970 ),
			),
			'packet' => array(
				100  => array( 415, 485, 525 ),
				250  => array( 445, 555, 605 ),
				500  => array( 620, 850, 920 ),
				750  => array( 745, 1095, 1165 ),
				1000 => array( 865, 1335, 1410 ),
				1250 => array( 975, 1480, 1575 ),
				1500 => array( 1090, 1630, 1755 ),
				1750 => array( 1170, 1750, 1900 ),
				2000 => array( 1210, 1830, 2005 ),
			),
		),
		'2019' => array(
			'letter' => array(
				10  => array( 135, 135, 135 ),
				20  => array( 135, 155, 155 ),
				100 => array( 160, 230, 230 ),
			),
			'large-letter' => array(
				100 => array( 280, 345, 360 ),
				250 => array( 400, 500, 530 ),
				500 => array( 460, 680, 720 ),
				750 => array( 550, 870, 925 ),
			),
			'packet' => array(
				100  => array( 450, 525, 565 ),
				250  => array( 480, 600, 650 ),
				500  => array( 665, 910, 980 ),
				750  => array( 795, 1165, 1240 ),
				1000 => array( 895, 1380, 1455 ),
				1250 => array( 975, 1480, 1575 ),
				1500 => array( 1090, 1630, 1755 ),
				1750 => array( 1170, 1750, 1900 ),
				2000 => array( 1210, 1830, 2005 ),
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
	 * @param int $instance_id.
	 * @return array
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$standard_quote = false;

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

		$zone                  = $this->get_zone( $destination );
		$packages              = $this->get_packages( $items, $packing_method );
		$options               = $this->get_instance_options( $instance_id );
		$compensation_optional = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > 20 && ! $compensation_optional ) {
					return false; // Max. compensation is 20.
				}

				if ( 'letter' !== $package->id && 'large-letter' !== $package->id ) {
					$package->id = 'packet';
				}

				if ( 'packet' === $package->id && 900 < ( $package->length + $package->width + $package->height ) ) {
					return false; // Exceeding parcels requirement, unpacked.
				}

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$this->debug( __( 'International package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

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

				$standard_quote += $quote;
			}
		}

		// Return pounds.
		$quotes = array();
		$quotes['international-standard'] = $standard_quote / 100;

		return $quotes;
	}
}
