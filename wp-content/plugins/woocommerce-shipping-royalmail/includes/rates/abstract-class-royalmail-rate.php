<?php
/**
 * Base class for RoyalMail rate.
 *
 * @package WC_RoyalMail
 */

/**
 * RoyalMail Rate class
 */
abstract class RoyalMail_Rate {

	/**
	 * List of country codes under EUR.
	 *
	 * @var array EUR country codes
	 */
	protected $europe = array(
		'AL',
		'AD',
		'AM',
		'AT',
		'BY',
		'BE',
		'BA',
		'BG',
		'CH',
		'CY',
		'CZ',
		'DE',
		'DK',
		'EE',
		'ES',
		'FO',
		'FI',
		'FR',
		'GE',
		'GI',
		'GR',
		'HU',
		'HR',
		'IE',
		'IS',
		'IT',
		'LT',
		'LU',
		'LV',
		'MC',
		'MK',
		'MT',
		'NO',
		'NL',
		'PO',
		'PT',
		'RO',
		'RU',
		'SE',
		'SI',
		'SK',
		'SM',
		'TR',
		'UA',
		'VA',
	);

	/**
	 * List of country codes under World Zone 2.
	 *
	 * @var array World Zone country codes
	 */
	protected $world_zone_2 = array(
		'AU',
		'PW',
		'IO',
		'CX',
		'CC',
		'CK',
		'FJ',
		'PF',
		'TF',
		'KI',
		'MO',
		'NR',
		'NC',
		'NZ',
		'PG',
		'NU',
		'NF',
		'LA',
		'PN',
		'TO',
		'TV',
		'WS',
		'AS',
	);

	/**
	 * Name of the rate (e.g. 'special_delivery_1pm').
	 *
	 * @var string ID/Name of rate
	 */
	protected $rate_id = '';

	/**
	 * Bands is an array of pricing bands where key is coverage / compensation
	 * for loss or damange (this will numeric value), or size (e.g. 'letter');
	 * value is an key-value array where key is package weight and value is the
	 * price. Data is nested within top-level array where key is the is the
	 * calendar year during which the pricing bands became active.
	 *
	 * @var array Pricing bands
	 */
	protected $bands = array();

	/**
	 * Shipping boxes.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array();

	/**
	 * Array of years and the dates on which the pricing becomes effective for
	 * that year (in YYYY-MM_DD format).
	 *
	 * @var array Rate year data
	 */
	protected $rate_year_starts = array(
		'2018' => '2018-03-26',
		'2019' => '2019-03-25',
	);

	/**
	 * Output a message.
	 *
	 * @param string $message Message.
	 * @param string $type    Message type.
	 */
	public function debug( $message, $type = 'notice' ) {
		if ( WC_ROYALMAIL_DEBUG ) {
			wc_add_notice( $message, $type );
		}
	}

	/**
	 * Get calendar year for currently effective rates.
	 *
	 * @return string Calendar year (e.g. '2018').
	 */
	public function get_rate_year() {
		// Get first year in list and use it as a default.
		reset( $this->rate_year_starts );
		$current_rate_year = key( $this->rate_year_starts );

		$current_time = current_time( 'timestamp' );
		foreach ( $this->rate_year_starts as $year => $start ) {
			if ( $current_time > strtotime( $start ) ) {
				$current_rate_year = $year;
			}
		}
		return $current_rate_year;
	}

	/**
	 * Get this rates pricing Bands.
	 *
	 * @param mixed $band Coverage or compensation for loss / damage, or size.
	 *                    If not specified all prices are returned.
	 *
	 * @return array
	 */
	public function get_rate_bands( $band = false ) {
		$rate_year = $this->get_rate_year();

		// Get the price data for the rate year, or the last defined year if not defined.
		if ( isset( $this->bands[ $rate_year ] ) ) {
			$current_bands = $this->bands[ $this->get_rate_year() ];
		} else {
			$current_bands = end( $this->bands );
		}

		$bands = apply_filters( 'woocommerce_shipping_royalmail_' . $this->rate_id . '_rate_bands', $current_bands );
		if ( $band ) {
			return isset( $bands[ $band ] ) ? $bands[ $band ] : array();
		} else {
			return $bands;
		}
	}

	/**
	 * Get this rates boxes.
	 *
	 * @return array
	 */
	public function get_rate_boxes() {
		return apply_filters( 'woocommerce_shipping_royalmail_' . $this->rate_id . '_rate_boxes', $this->boxes );
	}

	/**
	 * Get the zone for the package.
	 *
	 * @param string $destination Destination.
	 *
	 * @return string Zone.
	 */
	public function get_zone( $destination ) {
		if ( 'GB' === $destination ) {
			return 'UK';
		} elseif ( in_array( $destination, WC()->countries->get_european_union_countries() ) ) {
			return 'EU';
		} elseif ( in_array( $destination, $this->europe ) ) {
			return 'EUR';
		} elseif ( in_array( $destination, $this->world_zone_2 ) ) {
			return '2';
		} else {
			return '1';
		}
	}

	/**
	 * See if box could be a letter.
	 *
	 * @param  object $box Box.
	 * @return bool
	 */
	public function box_is_letter( $box ) {
		if ( $box->get_weight() > 100 ) {
			return false;
		}
		if ( $box->get_length() > 240 ) {
			return false;
		}
		if ( $box->get_width() > 165 ) {
			return false;
		}
		if ( $box->get_height() > 5 ) {
			return false;
		}
		return true;
	}

	/**
	 * See if box could be a letter.
	 *
	 * @param  object $box Box.
	 * @return bool
	 */
	public function box_is_large_letter( $box ) {
		if ( $box->get_weight() > 750 ) {
			return false;
		}
		if ( $box->get_length() > 353 ) {
			return false;
		}
		if ( $box->get_width() > 250 ) {
			return false;
		}
		if ( $box->get_height() > 25 ) {
			return false;
		}
		return true;
	}

	/**
	 * Pack items into boxes and return results.
	 *
	 * @since 1.0.0
	 * @version 2.5.3
	 *
	 * @param array  $items  Items to pack.
	 * @param string $method Method to pack items (e.g. 'Pack items individually').
	 *
	 * @return array Packed items.
	 */
	public function get_packages( $items, $method ) {
		$packages  = array();
		$boxpacker = $this->get_boxpack();

		if ( empty( $items ) ) {
			return $packages;
		}

		if ( 'per_item' === $method ) {
			$packages = $this->get_packages_using_per_item_method( $items, $boxpacker );
		} else {
			$packages = $this->get_packages_using_box_packing_method( $items, $boxpacker );
		}

		return $packages;
	}

	/**
	 * Get box packer instance populated with defined boxes.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @return WC_Boxpack Box packer.
	 */
	protected function get_boxpack() {
		$boxpack  = new WC_Boxpack();

		// Define boxes.
		foreach ( $this->get_rate_boxes() as $box_id => $box ) {
			$newbox = $boxpack->add_box(
				$box['length'],
				$box['width'],
				$box['height'],
				isset( $box['box_weight'] ) ? $box['box_weight'] : ''
			);

			if ( is_numeric( $box_id ) && $this->box_is_letter( $newbox ) ) {
				$box_id = 'letter';
				$newbox->set_type( 'envelope' );
			} elseif ( is_numeric( $box_id ) && $this->box_is_large_letter( $newbox ) ) {
				$box_id = 'large-letter';
				$newbox->set_type( 'envelope' );
			} elseif ( strstr( $box_id, 'packet' ) ) {
				$newbox->set_type( 'packet' );
			}

			$newbox->set_id( $box_id );

			if ( ! empty( $box['weight'] ) ) {
				$newbox->set_max_weight( $box['weight'] );
			}
		}

		return $boxpack;
	}

	/**
	 * Get packages using per item method.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array      $items   Items to pack.
	 * @param WC_Boxpack $boxpack Box packer instance.
	 *
	 * @return array Packages.
	 */
	protected function get_packages_using_per_item_method( $items, $boxpack ) {
		$packages = array();
		foreach ( $items as $item ) {
			$boxpack->clear_items();
			$boxpack->add_item(
				$item->length,
				$item->width,
				$item->height,
				$item->weight,
				$item->value
			);

			$boxpack->pack();
			$item_packages = $boxpack->get_packages();

			for ( $i = 0; $i < $item->qty; $i ++ ) {
				$packages = array_merge( $packages, $item_packages );
			}
		}

		return $packages;
	}

	/**
	 * Get packages using box packing method.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array      $items   Items to pack.
	 * @param WC_Boxpack $boxpack Box packer instance.
	 *
	 * @return array Packages.
	 */
	protected function get_packages_using_box_packing_method( $items, $boxpack ) {
		foreach ( $items as $item ) {
			for ( $i = 0; $i < $item->qty; $i ++ ) {
				$boxpack->add_item(
					$item->length,
					$item->width,
					$item->height,
					$item->weight,
					$item->value
				);
			}
		}

		// Pack it.
		$boxpack->pack();

		return $boxpack->get_packages();
	}

	/**
	 * Gets the instance options.
	 *
	 * @since 2.5.7
	 * @param int $instance_id The ID of the shipping instance.
	 * @return string $option_value
	 */
	public function get_instance_options( $instance_id = '' ) {
		if ( empty( $instance_id ) ) {
			return array();
		}

		return get_option( 'woocommerce_royal_mail_' . $instance_id . '_settings', array() );
	}
}
