<?php
/**
 * Cartflow default options.
 *
 * @package Cartflows
 */

/**
 * Initialization
 *
 * @since 1.0.0
 */
class Cartflows_Pro_Default_Meta {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Member Variable
	 *
	 * @var offer_fields
	 */
	private static $offer_fields = null;


	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor
	 */
	public function __construct() {

	}

	/**
	 *  Offer Default fields.
	 *
	 * @param int $post_id post id.
	 * @return array
	 */
	function get_offer_fields( $post_id ) {

		if ( null === self::$offer_fields ) {

			self::$offer_fields = array(
				'wcf-offer-product'        => array(
					'default'  => array(),
					'sanitize' => 'FILTER_CARTFLOWS_ARRAY',
				),
				'wcf-offer-quantity'       => array(
					'default'  => 1,
					'sanitize' => 'FILTER_SANITIZE_NUMBER_INT',
				),
				'wcf-offer-discount'       => array(
					'default'  => '',
					'sanitize' => 'FILTER_DEFAULT',
				),
				'wcf-offer-discount-value' => array(
					'default'  => '',
					'sanitize' => 'FILTER_SANITIZE_NUMBER_INT',
				),
				'wcf-offer-flat-shipping'  => array(
					'default'  => '',
					'sanitize' => 'FILTER_SANITIZE_NUMBER_INT',
				),
				'wcf-custom-script'        => array(
					'default'  => '',
					'sanitize' => 'FILTER_DEFAULT',
				),
			);
		}

		return apply_filters( 'cartflows_offer_meta_options', self::$offer_fields );
	}

	/**
	 *  Save Offer Meta fields.
	 *
	 * @param int $post_id post id.
	 * @return void
	 */
	function save_offer_fields( $post_id ) {

		$post_meta = $this->get_offer_fields( $post_id );

		wcf()->options->save_meta_fields( $post_id, $post_meta );
	}

	/**
	 *  Get checkout meta.
	 *
	 * @param int    $post_id post id.
	 * @param string $key options key.
	 * @param mix    $default options default value.
	 * @return string
	 */
	function get_offers_meta_value( $post_id, $key, $default = false ) {

		$value = wcf()->options->get_save_meta( $post_id, $key );

		if ( ! $value ) {

			if ( $default ) {

				$value = $default;
			} else {

				$fields = $this->get_offer_fields( $post_id );

				if ( isset( $fields[ $key ]['default'] ) ) {

					$value = $fields[ $key ]['default'];
				}
			}
		}

		return $value;
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Pro_Default_Meta::get_instance();
