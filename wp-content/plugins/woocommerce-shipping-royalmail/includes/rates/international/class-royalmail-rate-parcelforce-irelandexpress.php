<?php
/**
 * Parcelforce irelandexpress rates.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * Rates for Parcelforce irelandexpress.
 *
 * Based on http://www.parcelforce.com/sites/default/files/Parcelforce_retail_prices_2017.pdf.
 */
class RoyalMail_Rate_Parcelforce_Irelandexpress extends Parcelforce_Rate {
	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'parcelforce_irelandexpress';

	/**
	 * Pricing bands.
	 *
	 * Key is zone and value is an array where key is weight (up to) in gram
	 * and value is the price (in penny).
	 *
	 * @var array
	 */
	protected $bands = array(
		'2018' => array(
			'5' => array(
				500   => 1649,
				1000  => 1649,
				1500  => 1649,
				2000  => 1649,
				2500  => 1748,
				3000  => 1748,
				3500  => 1748,
				4000  => 1748,
				4500  => 1748,
				5000  => 1748,
				5500  => 2090,
				6000  => 2090,
				6500  => 2090,
				7000  => 2090,
				7500  => 2090,
				8000  => 2090,
				8500  => 2090,
				9000  => 2090,
				9500  => 2090,
				10000 => 2090,
				15000 => 2764,
				20000 => 3301,
				25000 => 4414,
				30000 => 4828,
			),
		),
	);

	/**
	 * Maximum inclusive compensation.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_inclusive_compensation = 200;

	/**
	 * Maximum total cover.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_total_cover = 2500;
}
