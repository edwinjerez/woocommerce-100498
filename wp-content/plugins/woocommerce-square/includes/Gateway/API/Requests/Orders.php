<?php
/**
 * WooCommerce Square
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@woocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Square to newer
 * versions in the future. If you wish to customize WooCommerce Square for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-square/
 *
 * @author    WooCommerce
 * @copyright Copyright: (c) 2019, Automattic, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace WooCommerce\Square\Gateway\API\Requests;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;
use SquareConnect\Api\OrdersApi;
use SquareConnect\Model as SquareModel;
use WooCommerce\Square\API;
use WooCommerce\Square\Handlers\Product;
use WooCommerce\Square\Utilities\Money_Utility;

/**
 * The Orders API request class.
 *
 * @since 2.0.0
 */
class Orders extends API\Request {


	/**
	 * Initializes a new Catalog request.
	 *
	 * @since 2.0.0
	 *
	 * @param \SquareConnect\ApiClient $api_client the API client
	 */
	public function __construct( $api_client ) {

		$this->square_api = new OrdersApi( $api_client );
	}


	/**
	 * Sets the data for creating an order.
	 *
	 * @since 2.0.0
	 *
	 * @param string $location_id location ID
	 * @param \WC_Order $order order object
	 */
	public function set_create_order_data( $location_id, \WC_Order $order ) {

		$this->square_api_method = 'createOrder';
		$this->square_request    = new SquareModel\CreateOrderRequest();

		$order_model = new SquareModel\Order();
		$order_model->setReferenceId( $order->get_order_number() );

		$line_items = array_merge( $this->get_product_line_items( $order ), $this->get_fee_line_items( $order ), $this->get_shipping_line_items( $order ) );

		$order_model->setLineItems( $line_items );

		$order_model->setTaxes( $this->get_order_taxes( $order ) );

		if ( $order->get_discount_total() ) {

			$order_model->setDiscounts( [ new SquareModel\OrderLineItemDiscount( [
				'name'         => __( 'Discount', 'woocommerce-square' ),
				'type'         => 'FIXED_AMOUNT',
				'amount_money' => Money_Utility::amount_to_money( $order->get_discount_total(), $order->get_currency() ),
			] ) ] );
		}

		$this->square_request->setIdempotencyKey( wc_square()->get_idempotency_key( $order->unique_transaction_ref ) );
		$this->square_request->setOrder( $order_model );

		$this->square_api_args = [
			$location_id,
			$this->square_request,
		];
	}


	/**
	 * Gets Square line item objects for an order's product items.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return SquareModel\OrderLineItem[]
	 */
	protected function get_product_line_items( \WC_Order $order ) {

		$line_items = [];

		foreach ( $order->get_items() as $item ) {

			if ( ! $item instanceof \WC_Order_Item_Product ) {
				continue;
			}

			$line_item = new SquareModel\OrderLineItem();

			$line_item->setQuantity( (string) $item->get_quantity() );

			$square_id = $item->get_meta( Product::SQUARE_VARIATION_ID_META_KEY );

			if ( $square_id ) {

				$line_item->setCatalogObjectId( $square_id );

			} else {

				$line_item->setName( $item->get_name() );
				$line_item->setBasePriceMoney( Money_Utility::amount_to_money( $order->get_item_subtotal( $item ), $order->get_currency() ) );
			}

			$line_items[] = $line_item;
		}

		return $line_items;
	}


	/**
	 * Gets Square line item objects for an order's fee items.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return SquareModel\OrderLineItem[]
	 */
	protected function get_fee_line_items( \WC_Order $order ) {

		$line_items = [];

		foreach ( $order->get_fees() as $item ) {

			if ( ! $item instanceof \WC_Order_Item_Fee ) {
				continue;
			}

			$line_item = new SquareModel\OrderLineItem();

			$line_item->setQuantity( (string) 1 );

			$line_item->setName( $item->get_name() );
			$line_item->setBasePriceMoney( Money_Utility::amount_to_money( $item->get_total(), $order->get_currency() ) );

			$line_items[] = $line_item;
		}

		return $line_items;
	}


	/**
	 * Gets Square line item objects for an order's shipping items.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return SquareModel\OrderLineItem[]
	 */
	protected function get_shipping_line_items( \WC_Order $order ) {

		$line_items = [];

		foreach ( $order->get_shipping_methods() as $item ) {

			if ( ! $item instanceof \WC_Order_Item_Shipping ) {
				continue;
			}

			$line_item = new SquareModel\OrderLineItem();

			$line_item->setQuantity( (string) 1 );

			$line_item->setName( $item->get_name() );
			$line_item->setBasePriceMoney( Money_Utility::amount_to_money( $item->get_total(), $order->get_currency() ) );

			$line_items[] = $line_item;
		}

		return $line_items;
	}


	/**
	 * Gets the tax line items for an order.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order
	 * @return SquareModel\OrderLineItemTax[]
	 */
	protected function get_order_taxes( \WC_Order $order ) {

		$taxes = [];

		foreach ( $order->get_taxes() as $tax ) {

			$tax_item = new SquareModel\OrderLineItemTax( [
				'name' => $tax->get_name(),
				'type' => 'ADDITIVE',
			] );

			$pre_tax_total = (float) $order->get_total() - (float) $order->get_total_tax();
			$total_tax     = (float) $tax->get_tax_total() + (float) $tax->get_shipping_tax_total();

			$percentage = ( $total_tax / $pre_tax_total ) * 100;

			$tax_item->setPercentage( Framework\SV_WC_Helper::number_format( $percentage ) );

			$taxes[] = $tax_item;
		}

		return $taxes;
	}


}
