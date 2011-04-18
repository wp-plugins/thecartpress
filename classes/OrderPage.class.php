<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersDetails.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersCosts.class.php' );

/**
 * Shows order's info
 * It's used in the cart area (into the checkout), in the print page and in the email page
 */
class OrderPage {

	static function show( $order_id, $see_comment = true, $echo = true ) {
		do_action( 'tcp_orderpage_create_order_cart', $order_id );
		$order = Orders::get( $order_id );
		$out  = '<div id="shipping_info" style="padding-bottom:1em;">' . "\n";
		$out .= '<h3>' . __( 'Shipping address', 'tcp' ) . '</h3>' . "\n";
		$out .= $order->shipping_firstname . ' ' . $order->shipping_lastname . '<br />' . "\n";
		if ( strlen( $order->shipping_company ) > 0 ) $out .= $order->shipping_company . '<br />' . "\n";
		$out .= $order->shipping_street . '<br />' . "\n";
		$out .= $order->shipping_postcode . ', ' . $order->shipping_city . '<br />' . "\n";
		$out .= $order->shipping_region . ', ' . $order->shipping_country . '<br />' . "\n";
		$telephone = $order->shipping_telephone_1;
		if ( strlen( $order->shipping_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->shipping_telephone_2;
		if ( strlen( $telephone ) > 0) $out .= __('Telephones', 'tcp') . ': ' . $telephone . '<br />' . "\n";
		if ( strlen( $order->shipping_fax ) > 0) $out .= __('Fax', 'tcp') . ': ' . $order->shipping_fax . '<br />' . "\n";
		if ( strlen( $order->shipping_email ) > 0) $out .= $order->shipping_email . '<br />' . "\n";
		$out .= '</div>' . "\n";
		$out .= '<div id="billing_info" style="padding-bottom:1em;">' . "\n";
		$out .= '<h3>' . __( 'Billing address', 'tcp' ) . '</h3>' . "\n";
		$out .= $order->billing_firstname . ' ' . $order->billing_lastname . '<br />' . "\n";
		if ( strlen( $order->billing_company ) > 0 ) $out .= $order->billing_company . '<br>' . "\n";
		$out .= $order->billing_street . '<br>' . "\n";
		$out .= $order->billing_postcode . ', ' . $order->billing_city . '<br>' . "\n";
		$out .= $order->billing_region . ', ' . $order->billing_country . '<br>' . "\n";
		$telephone = $order->billing_telephone_1;
		if ( strlen( $order->billing_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->billing_telephone_2;
		if ( strlen( $telephone ) > 0) $out .= __('Telephones', 'tcp') . ': ' . $telephone . '<br>' . "\n";
		if ( strlen( $order->billing_fax ) > 0) $out .= __('Fax', 'tcp') . ': ' . $order->billing_fax . '<br>' . "\n";
		if ( strlen( $order->billing_email ) > 0) $out .= $order->billing_email . '<br>' . "\n";
		$out .= '</div>' . "\n";
		//$out .= '<table class="tcp_details" cellspacing="0">' . "\n";
		$out .= '<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">' . "\n";
		$out .= '<thead>' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th class="tcp_order_page_name">' . __( 'Name', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_price">' . __( 'Price', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_units">' . __( 'Units', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_weight">' . __( 'Weight', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_order_page_total">' . __( 'Total', 'tcp' ) . '</th>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '</thead>' . "\n";
		$out .= '<tbody>' . "\n";
		$ordersDetails = OrdersDetails::getDetails( $order_id );
		if ( is_array( $ordersDetails ) ) {
			$total = 0;
			$i = 0;
			foreach( $ordersDetails as $orderDetail ) {
				$out .= '<tr ';
				if ( $i++ & 1 == 1 ) $out .= 'class="par"';
				$out .= '>' . "\n";
				$out .= '<td class="tcp_order_page_name">' . $orderDetail->name;
				if ( strlen( $orderDetail->option_1_name ) > 0 ) $out .= '<br />' . $orderDetail->option_1_name;
				if ( strlen( $orderDetail->option_2_name ) > 0 ) $out .= '-' . $orderDetail->option_2_name;
				$out .= '</td>' . "\n";
				if ( $orderDetail->tax > 0 )
					$out .= '<td class="tcp_order_page_price">' . tcp_number_format( $orderDetail->price ) . '&nbsp;' . $order->order_currency_code . ' (' . $orderDetail->tax . '%)</td>' . "\n";
				else
					$out .= '<td class="tcp_order_page_price">' . tcp_number_format( $orderDetail->price ) . '&nbsp;' . $order->order_currency_code .'</td>' . "\n";
				$out .= '<td class="tcp_order_page_units">' . tcp_number_format( $orderDetail->qty_ordered, 0 ) . '</td>' . "\n";
				$out .= '<td class="tcp_order_page_weight">' . tcp_number_format( $orderDetail->weight, 0 ). '&nbsp;' . tcp_get_the_unit_weight() . '</td>' . "\n";
				if ( $orderDetail->tax > 0 ) {
					$tax = 1 + $orderDetail->tax / 100;
					$price = $orderDetail->price * $tax;
				} else
					$price = $orderDetail->price;
				$price = $price * $orderDetail->qty_ordered;
				$total += $price;
				$out .= '<td class="tcp_order_page_total">' . tcp_number_format( $price ) . '&nbsp;' . $order->order_currency_code . '</td>' . "\n";
				$out .= '</tr>' . "\n";
			}
		}
		$discount = $order->discount_amount;
		if ( $discount > 0 ) {
			$dis = '<tr id="discount"';
			if ( $i++ & 1 == 1 ) $dis .= ' class="tcp_par"';
			$dis .= '>' . "\n";
			$dis .= '<td colspan="4" style="text-align:right">' . __( 'Discount', 'tcp' ) . '</td>' . "\n";
			$dis .= '<td>' . tcp_number_format( $discount ) . '&nbsp;' . $order->order_currency_code . '</td>' . "\n";
			$dis .= '</tr>' . "\n";
			$out .= $dis;
			$total = $total - $discount;
		}
		if ( $order->shipping_amount > 0 ) {
			$out .= '<tr';
			if ( $i++ & 1 == 1 ) $out .= ' class="par"';
			$out .= '>' . "\n";
			$out .= '<td colspan="4" style="text-align:right">' . __( 'Shipping cost', 'tcp' ) .'</td>' . "\n";
			$out .= '<td>' . tcp_number_format( $order->shipping_amount ) . '&nbsp;' . $order->order_currency_code . '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$total += $order->shipping_amount;
		}
		
		if ( $order->payment_amount > 0 ) {
			$out .= '<tr';
			if ( $i++ & 1 == 1 ) $out .= ' class="par"';
			$out .= '>' . "\n";
			$out .= '<td colspan="4" style="text-align:right">' . __( 'Payment cost', 'tcp' ) . '</td>' . "\n";
			$out .= '<td>' . tcp_number_format( $order->payment_amount ) . '&nbsp;' . $order->order_currency_code . '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$total += $order->payment_amount;
		}
		do_action( 'tcp_orderpage_calculate_other_costs', $order_id );
		$ordersCosts = OrdersCosts::getCosts( $order_id );
		if ( is_array( $ordersCosts ) ) {
			foreach( $ordersCosts as $ordersCost ) {
				$cost = '<tr ';
				if ( $i++ & 1 == 1 ) $cost .= 'class="par"';
				$cost .= '>' . "\n";
				$cost .= '<td colspan="4" style="text-align:right">' . $ordersCost->description . '</td>' . "\n";
				$cost .= '<td>' . tcp_number_format( $ordersCost->cost ) . '&nbsp;' . $order->order_currency_code .'</td>' . "\n";
				$total += $ordersCost->cost;
				$cost .= '</tr>' . "\n";
				$out .= $cost;
			}
		}
		do_action( 'tcp_orderpage_before_total', $order_id );
		$total = apply_filters( 'tcp_orderpage_set_total', $total, $order_id );
		$out .= '<tr';
		if ( $i++ & 1 == 1 ) $out .= ' class="par"';
		$out .= '>' . "\n";
		$out .= '<td colspan="4" style="text-align:right;">' . __( 'Total', 'tcp' ) . '</td>' . "\n";
		$out .= '<td style="color:red;">' . tcp_number_format( $total ) . '&nbsp;' . $order->order_currency_code . '</td>' . "\n";
		$out .= '</tr>';
		$out .= '</tbody></table>' . "\n";
		if ( $see_comment && strlen( $order->comment ) > 0 ) $out .= '<p>' . $order->comment . '</p>';
		if ( $echo )
			echo $out;
		else
			return $out;
	}
}
?>
