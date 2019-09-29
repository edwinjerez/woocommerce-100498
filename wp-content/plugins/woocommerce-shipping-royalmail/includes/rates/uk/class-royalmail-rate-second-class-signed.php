<?php
/**
 * Second class rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * RoyalMail_Rate_Second_Class_Signed class.
 *
 * Updated on 03/15/2019. as per https://www.royalmail.com/sites/default/files/royal-mail-our-prices-25-march-2019.pdf.
 * See UK Standard page 6.
 */
class RoyalMail_Rate_Second_Class_Signed extends RoyalMail_Rate_Second_Class {

	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'second_class';

	const COMPENSATION_UP_TO_VALUE = 50;

	/**
	 * Cost for signed for delivery.
	 *
	 * @var integer
	 */
	private $signed_for_cost = 120;
	/**
	 * Cost for signed for delivery of a package.
	 *
	 * @var integer
	 */	
	private $signed_for_package_cost = 100;	

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method selected.
	 * @param string $destination address.
	 *
	 * @return array
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$class_quote           = false;
		$packages              = $this->get_packages( $items, $packing_method );
		$options               = $this->get_instance_options( $instance_id );
		$compensation_optional = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $compensation_optional ) {
					return false; // Max. compensation is 50.
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
					return;
				}

				if ( 'letter' === $package->id || 'large-letter' === $package->id ) {
					$class_quote += $quote + $this->signed_for_cost;					
				} else {
					$class_quote += $quote + $this->signed_for_package_cost;						
				}
			}
		}

		// Return pounds.
		$quotes                        = array();
		$quotes['second-class-signed'] = $class_quote / 100;

		return $quotes;
	}
}
