<?php
/**
 * Default Box Sizes
 *
 * @package WC_Shipping_Australia_Post
 */

return array(
	array(
		'name'         => 'Small Satchel',
		'id'           => 'SMALL_SATCHEL',
		'max_weight'   => 0.5, // In kg.
		'box_weight'   => 0,
		'outer_length' => 31.5, // In cm. Intentionally less than inner dimensions so the satchel rates are returned.
		'outer_width'  => 20, // In Cm.
		'outer_height' => 1, // In Cm.
		'inner_length' => 33.5, // In cm.
		'inner_width'  => 22, // In Cm.
		'inner_height' => 3, // In Cm.
		'type'         => 'packet',
	),
	array(
		'name'         => 'Small-Medium Satchel',
		'id'           => 'AUS_PARCEL_EXPRESS_SATCHEL_1KG',
		'max_weight'   => 1, // In kg.
		'box_weight'   => 0,
		'outer_length' => 36.5, // In cm. Intentionally less than inner dimensions so the satchel rates are returned.
		'outer_width'  => 24.5, // In Cm.
		'outer_height' => 1, // In Cm.
		'inner_length' => 38.5, // In cm.
		'inner_width'  => 26.5, // In Cm.
		'inner_height' => 3, // In Cm.
		'type'         => 'packet',
	),
	array(
		'name'         => 'Medium Satchel',
		'id'           => 'MEDIUM_SATCHEL',
		'max_weight'   => 3, // In kg.
		'box_weight'   => 0,
		'outer_length' => 38.5, // In cm. Intentionally less than inner dimensions so the satchel rates are returned. Basically it's a TARDIS.
		'outer_width'  => 28.5, // In Cm.
		'outer_height' => 1, // In Cm.
		'inner_length' => 40.5, // In cm.
		'inner_width'  => 30.5, // In Cm.
		'inner_height' => 3, // In Cm.
		'type'         => 'packet',
	),
	array(
		'name'         => 'Large Satchel',
		'id'           => 'LARGE_SATCHEL',
		'max_weight'   => 5, // In kg.
		'box_weight'   => 0,
		'outer_length' => 49, // In cm. Intentionally less than inner dimensions so the satchel rates are returned. Or bag of holding may be more appropriate.
		'outer_width'  => 40.5, // In Cm.
		'outer_height' => 1, // In Cm.
		'inner_length' => 51, // In cm.
		'inner_width'  => 43.5, // In Cm.
		'inner_height' => 3, // In Cm.
		'type'         => 'packet',
	),
);
