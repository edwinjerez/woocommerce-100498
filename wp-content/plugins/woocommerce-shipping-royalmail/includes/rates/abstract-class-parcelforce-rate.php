<?php
/**
 * Base class Parcelforce rate.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * International Parcelforce rate
 *
 * @since 2.5.3
 * @version 2.5.3
 */
abstract class Parcelforce_Rate extends RoyalMail_Rate {
	/**
	 * List of countries categorized as far east.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var array
	 */
	protected $far_east = array(
		'CN',
		'HK',
		'MO',
		'JP',
		'MN',
		'KP',
		'KR',
		'TW',
		'BN',
		'KH',
		'TL',
		'ID',
		'LA',
		'MY',
		'MM',
		'PH',
		'SG',
		'TH',
		'VN',
		'RU',
	);

	/**
	 * List of countries categorized as Australasia.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var array
	 */
	protected $australasia = array(
		'AU',
		'PF',
		'NU',
		'TO',
		'CX',
		'KI',
		'PG',
		'TV',
		'CC',
		'NR',
		'PN',
		'VU',
		'CK',
		'NC',
		'SB',
		'WF',
		'FJ',
		'NZ',
		'TK',
		'WS',
	);

	/**
	 * Maximum inclusive compensation.
	 *
	 * All of our express services include compensation cover ranging from £100
	 * to £200, except globaleconomy.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_inclusive_compensation;

	/**
	 * Maximum total cover.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_total_cover;

	/**
	 * Get the international zone for the package.
	 *
	 * Sending within UK will be handled by RoyalMail rates.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param string $destination Destination.
	 *
	 * @return string Zone.
	 */
	public function get_zone( $destination ) {
		if ( in_array( $destination, array( 'JE', 'GG', 'IM' ) ) ) {
			return '4';
		} elseif ( 'IR' === $destination ) {
			return '5';
		} elseif ( in_array( $destination, array( 'BE', 'NL', 'LU' ) ) ) {
			return '6';
		} elseif ( in_array( $destination, array( 'FR', 'DE', 'DK' ) ) ) {
			return '7';
		} elseif ( in_array( $destination, array( 'IT', 'ES', 'PT', 'GR' ) ) ) {
			return '8';
		} elseif ( in_array( $destination, WC()->countries->get_european_union_countries() ) ) {
			return '9';
		} elseif ( in_array( $destination, array( 'US', 'CA' ) ) ) {
			return '10';
		} elseif ( in_array( $destination, $this->far_east ) ) {
			return '11';
		} elseif ( in_array( $destination, $this->australasia ) ) {
			return '11';
		} else {
			return '12';
		}
	}

	/**
	 * Get volumetric weight.
	 *
	 * Since WC_Shipping_Royalmail_Rates::get_items converts the dimensions to
	 * mm, this is calculated in mm instead of cm.
	 *
	 * @see http://www.parcelforce.com/help-and-advice/sending/volumetric-charging.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param float|int $l Length.
	 * @param float|int $w Width.
	 * @param float|int $h Height.
	 *
	 * @return Calculated weight in gram.
	 */
	public function get_volumetric_weight( $l, $w, $h ) {
		return ( $l * $w * $h ) / 5000;
	}

	/**
	 * Get quotes for this service.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array  $items          Items to be shipped.
	 * @param string $packing_method Selected packing method.
	 * @param string $destination    Destination (country) to ship to.
	 * @param array  $boxes          User-defined boxes.
	 *
	 * @return array Quotes.
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array() ) {

		$this->set_boxes( $boxes );

		// By default, Parcelforce doesn't define boxes to ship internationally.
		// Max weight is 30kg. Max dimensions of 1.5m length and 3m length and
		// girth combined. With boxes empty, packing method is ignored at all.
		if ( empty( $this->boxes ) ) {
			return $this->get_quotes_based_on_items( $items, $destination );
		}

		// Box packer will be used for 'per_item' or 'box_packing'. When 'per_item',
		// each box only packs one item.
		return $this->get_quotes_based_on_packages( $items, $packing_method, $destination );
	}

	/**
	 * Set boxes from user-defined (in setting) boxes.
	 *
	 * @since 2.5.3
	 * @version 2.5.5
	 *
	 * @param array $boxes User-defined boxes.
	 */
	protected function set_boxes( $boxes = array() ) {
		if ( ! empty( $boxes ) ) {
			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
				);
			}
		}
	}

	/**
	 * Get quotes based on items.
	 *
	 * Each item in items is considered packaged already in a box. Which means
	 * dimensions stored in product property will be used to calculate the
	 * cost.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array  $items       Items to ship.
	 * @param string $destination Destination.
	 *
	 * @return mixed Quotes.
	 */
	protected function get_quotes_based_on_items( $items, $destination ) {
		$total_actual_weight     = 0;
		$total_volumetric_weight = 0;
		$total_valued_items      = 0;

		foreach ( $items as $item ) {
			for ( $i = 0; $i < $item->qty; $i++ ) {
				$total_actual_weight += $item->weight;
				$total_volumetric_weight += $this->get_volumetric_weight(
					$item->length,
					$item->width,
					$item->height
				);

				$total_valued_items += $item->value;
			}
		}

		$chargeable_weight = ( $total_actual_weight > $total_volumetric_weight )
			? $total_actual_weight
			: $total_volumetric_weight;

		$zone  = $this->get_zone( $destination );
		$bands = $this->get_rate_bands( $zone );
		$quote = 0;
		foreach ( $bands as $max_weight => $price ) {
			if ( $chargeable_weight <= $max_weight ) {
				$quote = $price;
				break;
			}
		}

		// Don't return the quote if valued items is greater than maximum total
		// cover of the service.
		if ( $this->maximum_total_cover > 0 && $total_valued_items > $this->maximum_total_cover ) {
			return false;
		}

		// Additional compensation cost.
		$quote += $this->get_additional_compensation_cost( $total_valued_items );

		// Rate includes VAT.
		$quote = $quote / 1.2;

		return array(
			str_replace( '_', '-', $this->rate_id ) => $quote / 100,
		);
	}

	/**
	 * Get quotes based on packages.
	 *
	 * This method will be used if user-defined boxes are not empty.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $destination Address to ship to.
	 *
	 * @return mixed Quote.
	 */
	protected function get_quotes_based_on_packages( $items, $packing_method, $destination ) {
		$zone        = $this->get_zone( $destination );
		$packages    = $this->get_packages( $items, $packing_method );
		$class_quote = false;

		if ( $packages ) {
			foreach ( $packages as $package ) {
				$volumetric_weight = $this->get_volumetric_weight(
					$package->length,
					$package->width,
					$package->height
				);

				$chargeable_weight = ( $package->weight > $volumetric_weight )
					? $package->weight
					: $volumetric_weight;

				$zone  = $this->get_zone( $destination );
				$bands = $this->get_rate_bands( $zone );
				$quote = 0;
				foreach ( $bands as $max_weight => $price ) {
					if ( $chargeable_weight <= $max_weight ) {
						$quote = $price;
						break;
					}
				}

				// Don't return the quote if valued package is greater than maximum total
				// cover of the service.
				if ( $this->maximum_total_cover > 0 && $package->value > $this->maximum_total_cover ) {
					return false;
				}

				// Additional compensation cost.
				$quote += $this->get_additional_compensation_cost( $package->value );

				// Rate includes VAT.
				$quote = $quote / 1.2;

				$class_quote += $quote;
			}
		}

		// Return pounds.
		$quotes = array();
		$quotes[ str_replace( '_', '-', $this->rate_id ) ] = $class_quote / 100;

		return $quotes;
	}

	/**
	 * Get additional compensation cost.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @see http://www.parcelforce.com/help-and-advice/sending/enhanced-compensation.
	 *
	 * @param int $valued_item Valued item (product's price).
	 *
	 * @return int|double Additional compensation.
	 */
	public function get_additional_compensation_cost( $valued_item ) {
		// No compensation included for globaleconomy service and if it's under
		// max. inc. compensation there's no extra cost.
		if ( ! $this->maximum_inclusive_compensation || $valued_item <= $this->maximum_inclusive_compensation ) {
			return 0;
		}

		// £1.80 including VAT for the first extra £100 cover. The additional
		// cost is in pence since it will be added before converting back to £.
		$cost  = 180;
		$extra = ( $valued_item - $this->maximum_inclusive_compensation ) - 100;

		if ( 0 >= $extra ) {
			return $cost;
		}

		// £4.50 including VAT for every subsequent £100. The additional cost
		// is in pence since it will be added before converting back to £.
		$cost += ceil( $extra / 100 ) * 450;

		return apply_filters(
			'woocommerce_shipping_royalmail_parcelforce_additional_compensation',
			$cost
		);
	}
}
