<?php
/**
 * International Economy rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_International_Economy class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See Parcelforce WorldWide page 15.
 */
class RoyalMail_Rate_International_Economy extends RoyalMail_Rate {

	/**
	 * ID/Name of rate
	 *
	 * @var string
	 */
	protected $rate_id = 'international_economy';

	/**
	 * Pricing bands
	 *
	 * Key is size (e.g. 'letter') and value is an array where key is weight in
	 * gram and value is the price (in penny).
	 *
	 * @var array
	 */
	protected $bands = array(
		'2018' => array(
			'letter' => array(
				10  => 110,
				20  => 110,
				100 => 145,
			),
			'large-letter' => array(
				100  => 260,
				250  => 380,
				500  => 485,
				750  => 590,
			),
			'packet' => array(
				100  => 390,
				250  => 420,
				500  => 610,
				750  => 715,
				1000 => 855,
				1250 => 980,
				1500 => 1185,
				1750 => 1165,
				2000 => 1205,
			),
		),
		'2019' => array(
			'letter' => array(
				10  => 120,
				20  => 120,
				100 => 150,
			),
			'large-letter' => array(
				100  => 275,
				250  => 395,
				500  => 455,
				750  => 545,
			),
			'packet' => array(
				100  => 425,
				250  => 455,
				500  => 655,
				750  => 765,
				1000 => 885,
				1250 => 980,
				1500 => 1085,
				1750 => 1165,
				2000 => 1205,
			),
		),
	);

	/**
	 * Shipping boxes.
	 *
	 * @var array
	 */
	protected $default_boxes = array(
		'letter' => array(
			'length'   => 240, // Max L in mm.
			'width'    => 165, // Max W in mm.
			'height'   => 5,   // Max H in mm.
			'weight'   => 100, // Max Weight in grams.
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
			'weight'   => 500,
		),
		'square-parcel' => array(
			'length'   => 300,
			'width'    => 300,
			'height'   => 300,
			'weight'   => 500,
		),
		'parcel' => array(
			'length'   => 450,
			'width'    => 225,
			'height'   => 225,
			'weight'   => 500,
		),
	);

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method selected.
	 * @param string $destination address.
	 * @param array  $boxes User-defined boxes.
	 * @param int $instance_id.
	 *
	 * @return array
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$class_quote  = false;
		if ( ! empty( $boxes ) ) {
			$this->boxes = array();

			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
					'weight'     => 500,
				);
			}
		} else {
			$this->boxes = $this->default_boxes;
		}

		if ( in_array( $destination, $this->europe ) ) {
			foreach ( $this->bands as $year => $bands_group ) {
				unset( $this->bands[ $year ]['letter'], $this->boxes['letter'] );
			}
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

				$this->debug( __( 'Economy package:', 'woocommerce-shipping-royalmail' ) . ' <pre>' . print_r( $package, true ) . '</pre>' );

				$bands   = $this->get_rate_bands( $package->id );
				$quote   = 0;
				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( $package->weight <= $band ) {
						$quote += $value;
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return;
				}

				$class_quote += $quote;
			}
		}

		// Return pounds.
		$quotes = array();
		$quotes['international-economy'] = $class_quote / 100;

		return $quotes;
	}
}
