<?php
/**
 * Parcelforce globaleconomy rates.
 *
 * @package WC_RoyalMail/Rate
 */

/**
 * Rates for Parcelforce globaleconomy.
 *
 * Based on http://www.parcelforce.com/sites/default/files/Parcelforce_retail_prices_2017.pdf.
 */
class RoyalMail_Rate_Parcelforce_Globaleconomy extends Parcelforce_Rate {
	/**
	 * ID/Name of rate.
	 *
	 * @var string
	 */
	protected $rate_id = 'parcelforce_globaleconomy';

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
			'10' => array(
				500   => 2445,
				1000  => 2695,
				1500  => 2945,
				2000  => 3195,
				2500  => 3830,
				3000  => 4465,
				3500  => 5100,
				4000  => 5735,
				4500  => 6370,
				5000  => 7005,
				5500  => 7370,
				6000  => 7735,
				6500  => 8100,
				7000  => 8465,
				7500  => 8830,
				8000  => 9195,
				8500  => 9560,
				9000  => 9925,
				9500  => 10290,
				10000 => 10655,
				15000 => 13005,
				20000 => 15305,
				25000 => 17605,
				30000 => 19905,
			),
			'11' => array(
				500   => 3150,
				1000  => 3765,
				1500  => 4380,
				2000  => 4995,
				2500  => 5595,
				3000  => 6195,
				3500  => 6795,
				4000  => 7395,
				4500  => 7995,
				5000  => 8595,
				5500  => 9045,
				6000  => 9495,
				6500  => 9945,
				7000  => 10395,
				7500  => 10845,
				8000  => 11295,
				8500  => 11745,
				9000  => 12195,
				9500  => 12645,
				10000 => 13095,
				15000 => 16395,
				20000 => 19595,
				25000 => 22995,
				30000 => 26295,
			),
			'12' => array(
				500   => 3300,
				1000  => 4005,
				1500  => 4710,
				2000  => 5415,
				2500  => 6120,
				3000  => 6825,
				3500  => 7530,
				4000  => 8235,
				4500  => 8940,
				5000  => 9645,
				5500  => 10285,
				6000  => 10925,
				6500  => 11565,
				7000  => 12205,
				7500  => 12845,
				8000  => 13485,
				8500  => 14125,
				9000  => 14765,
				9500  => 15405,
				10000 => 16045,
				15000 => 20445,
				20000 => 24895,
				25000 => 29345,
				30000 => 33795,
			),
		),
	);
}
