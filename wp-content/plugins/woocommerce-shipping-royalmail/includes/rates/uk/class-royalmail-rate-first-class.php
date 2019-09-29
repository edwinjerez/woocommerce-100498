<?php
/**
 * First class rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_First_Class class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See UK Standard page 6.
 */
class RoyalMail_Rate_First_Class extends RoyalMail_Rate {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'first_class';

	const COMPENSATION_UP_TO_VALUE = 20;

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
				100 => 67,
			),
			'large-letter' => array(
				100 => 101,
				250 => 140,
				500 => 187,
				750 => 260,
			),
			'small-parcel-wide' => array(
				1000 => 345,
				2000 => 550,
			),
			'small-parcel-deep' => array(
				1000 => 345,
				2000 => 550,
			),
			'small-parcel-bigger' => array(
				1000 => 345,
				2000 => 550,
			),
			'medium-parcel' => array(
				1000  => 575,
				2000  => 895,
				5000  => 1585,
				10000 => 2190,
				20000 => 3340,
			),
		),
		'2019' => array(
			'letter' => array(
				100 => 70,
			),
			'large-letter' => array(
				100 => 106,
				250 => 150,
				500 => 197,
				750 => 272,
			),
			'small-parcel-wide' => array(
				1000 => 355,
				2000 => 550,
			),
			'small-parcel-deep' => array(
				1000 => 355,
				2000 => 550,
			),
			'small-parcel-bigger' => array(
				1000 => 355,
				2000 => 550,
			),
			'medium-parcel' => array(
				1000  => 580,
				2000  => 895,
				5000  => 1585,
				10000 => 2190,
				20000 => 3340,
			),
		),
	);

	/**
	 * Shipping boxes.
	 *
	 * @var array
	 */
	protected $boxes = array(
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
		'small-parcel-wide' => array(
			'length' => 450,
			'width'  => 350,
			'height' => 80,
			'weight' => 2000,
		),
		'small-parcel-deep' => array(
			'length' => 350,
			'width'  => 250,
			'height' => 160,
			'weight' => 2000,
		),
		'small-parcel-bigger' => array(
			'length' => 450,
			'width'  => 350,
			'height' => 160,
			'weight' => 2000,
		),
		'medium-parcel' => array(
			'length'   => 610,
			'width'    => 460,
			'height'   => 460,
			'weight'   => 20000,
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
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$class_quote           = 0;
		$packages              = $this->get_packages( $items, $packing_method );
		$options               = $this->get_instance_options( $instance_id );
		$compensation_optional = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $compensation_optional ) {
					return false; // Max. compensation is 20.
				}

				$quote = 0;

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$bands = $this->get_rate_bands( $package->id );

				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( is_numeric( $band ) && $package->weight <= $band ) {
						$quote += $value;
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return null;
				}

				$class_quote += $quote;
			}
		}

		// Return pounds.
		$quotes                = array();
		$quotes['first-class'] = $class_quote / 100;

		return $quotes;
	}
}
