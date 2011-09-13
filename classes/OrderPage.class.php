<?php
/**
 * This file is part of TheCartPress.
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersDetails.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersCosts.class.php' );

/**
 * Shows an Order
 * It's used in the cart area (into the checkout), in the print page and in the email page
 */
class OrderPage {

	static function show( $order_id, $see_comment = true, $echo = true, $see_address = true, $see_full = false ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/CartTable.class.php' );
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/CartSourceDB.class.php' );
		$cart_table = new TCP_CartTable( );
		return $cart_table->show( new TCP_CartSourceDB( $order_id, $see_address, $see_full, true, $see_comment ), $echo );
	}

	//TODO to remove
/*	static function show2( $order_id, $see_comment = true, $echo = true, $see_address = true, $see_full = false ) {
		do_action( 'tcp_orderpage_create_order_cart', $order_id );
		$order = Orders::get( $order_id );
		$out = '';
		if ( $see_address ) {
			if ( $order->shipping_firstname == "" ) {
				$style = 'style="display:none"';
			} else {
				$style = 'style="padding-bottom:1em;"';
			} 
			$out .= '<div id="shipping_info" ' . $style . '>' . "\n";
			$out .= '<h3>' . __( 'Shipping address', 'tcp' ) . '</h3>' . "\n";
			$out .= stripslashes( $order->shipping_firstname ) . ' ' . stripslashes( $order->shipping_lastname ) . '<br />' . "\n";
			if ( strlen( $order->shipping_company ) > 0 ) $out .= stripslashes( $order->shipping_company ) . '<br />' . "\n";
			$out .= stripslashes( $order->shipping_street ) . '<br />' . "\n";
			$out .= $order->shipping_postcode . ', ' . stripslashes( $order->shipping_city ) . '<br />' . "\n";
			$out .= stripslashes( $order->shipping_region ) . ', ' . stripslashes( $order->shipping_country ) . '<br />' . "\n";
			$telephone = $order->shipping_telephone_1;
			if ( strlen( $order->shipping_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->shipping_telephone_2;
			if ( strlen( $telephone ) > 0) $out .= __('Telephones', 'tcp') . ': ' . $telephone . '<br />' . "\n";
			if ( strlen( $order->shipping_fax ) > 0) $out .= __('Fax', 'tcp') . ': ' . $order->shipping_fax . '<br />' . "\n";
			if ( strlen( $order->shipping_email ) > 0) $out .= $order->shipping_email . '<br />' . "\n";
			$out .= '</div><!-- shipping_info-->' . "\n";
		
			$out .= '<div id="billing_info" style="padding-bottom:1em;">' . "\n";
			$out .= '<h3>' . __( 'Billing address', 'tcp' ) . '</h3>' . "\n";
			$out .= stripslashes( $order->billing_firstname ) . ' ' . stripslashes( $order->billing_lastname ) . '<br />' . "\n";
			if ( strlen( $order->billing_company ) > 0 ) $out .= stripslashes( $order->billing_company ) . '<br>' . "\n";
			$out .= stripslashes( $order->billing_street ) . '<br>' . "\n";
			$out .= $order->billing_postcode . ', ' . stripslashes( $order->billing_city ) . '<br>' . "\n";
			$out .= stripslashes( $order->billing_region ) . ', ' . stripslashes( $order->billing_country ) . '<br>' . "\n";
			$telephone = $order->billing_telephone_1;
			if ( strlen( $order->billing_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->billing_telephone_2;
			if ( strlen( $telephone ) > 0) $out .= __('Telephones', 'tcp') . ': ' . $telephone . '<br>' . "\n";
			if ( strlen( $order->billing_fax ) > 0) $out .= __('Fax', 'tcp') . ': ' . $order->billing_fax . '<br>' . "\n";
			if ( strlen( $order->billing_email ) > 0) $out .= $order->billing_email . '<br><br><br><br>' . "\n";
			$out .= '</div><!-- billing_info -->' . "\n";
		}
		$out .= '<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">' . "\n";
		$out .= '<thead>' . "\n";
		$out .= '<tr class="tcp_cart_title_row">' . "\n";
		if ( $see_full ) {
			$out .= '<th class="tcp_order_page_id">' . __( 'Id.', 'tcp' ) . '</th>' . "\n";
		}
		$out .= '<th class="tcp_order_page_name">' . __( 'Name', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_price">' . __( 'Price', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_units">' . __( 'Units', 'tcp' ) . '</th>' . "\n";
		if ( $see_full ) {
			$out .= '<th class="tcp_order_page_sku">' . __( 'Sku', 'tcp' ) . '</th>' . "\n";
		}
		$out .= '<th class="tcp_order_page_weight">' . __( 'Weight', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_total">' . __( 'Total', 'tcp' ) . '</th>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '</thead>' . "\n";
		$out .= '<tbody>' . "\n";
		$ordersDetails = OrdersDetails::getDetails( $order_id );
		if ( is_array( $ordersDetails ) ) {
			$total = 0;
			$total_tax = 0;
			$i = 0;
			foreach( $ordersDetails as $orderDetail ) {
				$out .= '<tr class="tcp_cart_product_row';
				if ( $i++ & 1 == 1 ) $out .= ' par ';
				$out .= '">' . "\n";
				if ( $see_full ) {
					$out .= '<td class="tcp_order_page_id">' . $orderDetail->post_id . '</td>' . "\n";
				}
				$out .= '<td class="tcp_order_page_name">' . $orderDetail->name;
				if ( strlen( $orderDetail->option_1_name ) > 0 ) $out .= '<br />' . $orderDetail->option_1_name;
				if ( strlen( $orderDetail->option_2_name ) > 0 ) $out .= '-' . $orderDetail->option_2_name;
				$out .= '</td>' . "\n";
				if ( $orderDetail->tax > 0 )
					//$out .= '<td class="tcp_order_page_price">' . tcp_format_the_price( $orderDetail->price, $order->order_currency_code ) . '</td>' . "\n";
					$out .= '<td class="tcp_order_page_price">' . tcp_format_the_price( $orderDetail->price ) . '</td>' . "\n";
				else
					//$out .= '<td class="tcp_order_page_price">' . tcp_format_the_price( $orderDetail->price, $order->order_currency_code ) .'</td>' . "\n";
					$out .= '<td class="tcp_order_page_price">' . tcp_format_the_price( $orderDetail->price ) .'</td>' . "\n";
				$out .= '<td class="tcp_order_page_units">' . tcp_number_format( $orderDetail->qty_ordered, 0 ) . '</td>' . "\n";
				if ( $see_full ) {
					$out .= '<td class="tcp_order_page_sku">' . $orderDetail->sku . '</td>' . "\n";
				}
				$out .= '<td class="tcp_order_page_weight">' . tcp_number_format( $orderDetail->weight, 0 ). '&nbsp;' . tcp_get_the_unit_weight() . '</td>' . "\n";
				$price = $orderDetail->price * $orderDetail->qty_ordered;
				$tax = round( $orderDetail->price * ( $orderDetail->tax / 100 ), tcp_get_decimal_currency() ) * $orderDetail->qty_ordered;
				$total_tax += $tax;
				$total += $price;
				$out .= '<td class="tcp_order_page_total">' . tcp_format_the_price( $price ) . '</td>' . "\n";
				$out .= '</tr>' . "\n";
			}
		}
		$discount = $order->discount_amount;
		if ( $discount > 0 ) {
			$dis = '<tr id="discount" class="tcp_cart_discount_row';
			if ( $i++ & 1 == 1 ) $dis .= ' tcp_par';
			$dis .= '">' . "\n";
			$dis .= '<td colspan="4" class="tcp_cart_discount_title">' . __( 'Discount', 'tcp' ) . '</td>' . "\n";
			//$dis .= '<td class="tcp_cart_discount">' . tcp_format_the_price( $discount, $order->order_currency_code ) . '</td>' . "\n";
			$dis .= '<td class="tcp_cart_discount">' . tcp_format_the_price( $discount ) . '</td>' . "\n";
			$dis .= '</tr>' . "\n";
			$out .= $dis;
			$total = $total - $discount;
		}
		do_action( 'tcp_orderpage_calculate_other_costs', $order_id );
		$ordersCosts = OrdersCosts::getCosts( $order_id );
		if ( is_array( $ordersCosts ) ) {
			foreach( $ordersCosts as $ordersCost ) {
				$cost = '<tr ';
				if ( $i++ & 1 == 1 ) $cost .= 'class="par"';
				$cost .= '>' . "\n";
				$cost .= '<td colspan="4" style="text-align:right">' . $ordersCost->description . '</td>' . "\n";
				//$cost .= '<td>' . tcp_format_the_price( $ordersCost->cost, $order->order_currency_code ) . '</td>' . "\n";
				$cost .= '<td>' . tcp_format_the_price( $ordersCost->cost ) . '</td>' . "\n";
				$tax = $ordersCost->cost * ( $ordersCost->tax / 100 );
				$total_tax += $tax;
				$total += $ordersCost->cost;
				$cost .= '</tr>' . "\n";
				$out .= $cost;
			}
		}
		//if ( tcp_is_display_full_tax_summary() ) {
		if ( $total_tax > 0 ) {
			$out .= '<tr class="tcp_cart_subtotal_row ';
			if ( $i++ & 1 == 1 ) $out .= ' par';
			$out .= '">' . "\n";
			$out .= '<td colspan="4" class="tcp_cart_subtotal_title">' . __( 'Taxes', 'tcp' ) . '</td>' . "\n";
			//$out .= '<td class="tcp_cart_subtotal">' . tcp_format_the_price( $total_tax, $order->order_currency_code ) . '</td>' . "\n";
			$out .= '<td class="tcp_cart_subtotal">' . tcp_format_the_price( $total_tax ) . '</td>' . "\n";
			$out .= '</tr>';
		}
		do_action( 'tcp_orderpage_before_total', $order_id );
		$total += $total_tax;
		$total = apply_filters( 'tcp_orderpage_set_total', $total, $order_id );
		$out .= '<tr class="tcp_cart_total_row ';
		if ( $i++ & 1 == 1 ) $out .= ' par';
		$out .= '">' . "\n";
		$out .= '<td colspan="4" class="tcp_cart_total_title">' . __( 'Total', 'tcp' ) . '</td>' . "\n";
		//$out .= '<td class="tcp_cart_total">' . tcp_format_the_price( $total, $order->order_currency_code ) . '</td>' . "\n";
		$out .= '<td class="tcp_cart_total">' . tcp_format_the_price( $total ) . '</td>' . "\n";
		$out .= '</tr>';
		$out .= '</tbody></table>' . "\n";
		if ( $see_comment && strlen( $order->comment ) > 0 ) $out .= '<p>' . $order->comment . '</p>';
		if ( $echo )
			echo $out;
		else
			return $out;
	}*/
}
?>
