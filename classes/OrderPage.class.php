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

/**
 * Shows order's info
 * It's used in the cart area (into the checkout), in the print page and in the email page
 */
class OrderPage {

	static function show( $order_id, $see_comment = true, $echo = true ) {
		$order = Orders::get( $order_id );
		$out  = '<div id="shipping_info">' . "\n";
		$out .= '<h3>' . __( 'Shipping address', 'tcp' ) . '</h3>' . "\n";
		$out .= $order->shipping_firstname . ' ' . $order->shipping_lastname . '<br />' . "\n";
		if ( strlen( $order->shipping_company ) > 0 ) $out .= $order->shipping_company . '<br />' . "\n";
		$out .= $order->shipping_street . '<br />' . "\n";
		$out .= $order->shipping_city . ', ' . $order->shipping_region . '<br />' . "\n";
		$out .= $order->shipping_postcode . ', ' . $order->shipping_country . '<br />' . "\n";
		$telephone = $order->shipping_telephone_1;
		if ( strlen( $order->shipping_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->shipping_telephone_2;
		if ( strlen( $telephone ) > 0) $out .= __('Telephones:', 'tcp') . ' ' . $telephone . '<br />' . "\n";
		if ( strlen( $order->shipping_fax ) > 0) $out .= __('Fax:', 'tcp') . ' ' . $order->shipping_fax . '<br />' . "\n";
		if ( strlen( $order->shipping_email ) > 0) $out .= $order->shipping_email . '<br />' . "\n";
		$out .= '</div>';
		$out .= '<div id="billing_info">' . "\n";
		$out .= '<h3>' . __( 'Billing address', 'tcp' ) . '</h3>' . "\n";
		$out .= $order->billing_firstname . ' ' . $order->billing_lastname . '<br />' . "\n";
		if ( strlen( $order->billing_company ) > 0 ) $out .= $order->billing_company . '<br>' . "\n";
		$out .= $order->billing_street . '<br>' . "\n";
		$out .= $order->billing_city . ', ' . $order->billing_region . '<br>' . "\n";
		$out .= $order->billing_postcode . ', ' . $order->billing_country . '<br>' . "\n";
		$telephone = $order->billing_telephone_1;
		if ( strlen( $order->billing_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->billing_telephone_2;
		if ( strlen( $telephone ) > 0) $out .= __('Telephones:', 'tcp') . ' ' . $telephone . '<br>' . "\n";
		if ( strlen( $order->billing_fax ) > 0) $out .= __('Fax:', 'tcp') . ' ' . $order->billing_fax . '<br>' . "\n";
		if ( strlen( $order->billing_email ) > 0) $out .= $order->billing_email . '<br>' . "\n";
		$out .= '</div>';
		$out .= '<table class="widefat fixed" cellspacing="0">' . "\n";
		$out .= '<thead>' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th>' . __( 'name', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'price', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'units', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'weight', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'total', 'tcp' ) . '</th>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '</thead>' . "\n";
		$out .= '<tbody>' . "\n";
		$ordersDetails = OrdersDetails::getDetails( $order_id );
		if (is_array( $ordersDetails ) ) {
			$total = 0;
			$i = 0;
			foreach( $ordersDetails as $orderDetail ) {
				$out .= '<tr ';
				if ( $i++ & 1 == 1 ) $out .= 'class="par"';
				$out .= '>' . "\n";
				$out .= '<td>' . $orderDetail->name;
				if ( strlen( $orderDetail->option_1_name ) > 0 ) $out .= '<br />' . $orderDetail->option_1_name;
				if ( strlen( $orderDetail->option_2_name ) > 0 ) $out .= '-' . $orderDetail->option_2_name;
				$out .= '</td>' . "\n";
				if ( $orderDetail->tax > 0 )
					$out .= '<td>' . OrderPage::numberFormat( $orderDetail->price, $order->order_currency_code ) . ' (' . $orderDetail->tax . '%)</td>' . "\n";
				else
					$out .= '<td>' . OrderPage::numberFormat( $orderDetail->price, $order->order_currency_code ) .'</td>' . "\n";
				$out .= '<td>' . $orderDetail->qty_ordered . '</td>' . "\n";
				$out .= '<td>' . $orderDetail->weight . '</td>' . "\n";
				if ( $orderDetail->tax > 0 ) {
					$tax = 1 + $orderDetail->tax / 100;
					$price = $orderDetail->price * $tax;
				} else
					$price = $orderDetail->price;
				$price = $price * $orderDetail->qty_ordered;
				$total += $price;
				$out .= '<td>' . OrderPage::numberFormat( $price, $order->order_currency_code ) . '</td>' . "\n";
				$out .= '</tr>' . "\n";
			}
			$out .= '<tr';
			if ( $i++ & 1 == 1 ) $out .= ' class="par"';
			$out .= '>' . "\n";
			$out .= '<td colspan="4" style="text-align:right">' . __( 'Shipping cost', 'tcp' ) .'</td>' . "\n";
			$out .= '<td>' . OrderPage::numberFormat( $order->shipping_amount, $order->order_currency_code ) . '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$out .= '<tr';
			if ( $i++ & 1 == 1 ) $out .= ' class="par"';
			$out .= '>' . "\n";
			$out .= '<td colspan="4" style="text-align:right">' . __( 'Payment cost', 'tcp' ) . '</td>' . "\n";
			$out .= '<td>' . OrderPage::numberFormat( $order->payment_amount, $order->order_currency_code ) . '</td>' . "\n";
			$out .= '</tr>' . "\n";
			$total += $order->shipping_amount + $order->payment_amount;
			$out .= '<tr';
			if ( $i++ & 1 == 1 ) $out .= ' class="par"';
			$out .= '>' . "\n";
			$out .= '<td colspan="4" style="text-align:right;">' . __( 'Total', 'tcp' ) . '</td>' . "\n";
			$out .= '<td style="color:red;">' . OrderPage::numberFormat( $total, $order->order_currency_code ) . '</td>' . "\n";
			$out .= '</tr>';
			$out .= '</tbody></table>' . "\n";
		}
		if ( $see_comment && strlen( $order->comment ) > 0 ) $out .= '<p>' . $order->comment . '</p>';
		if ( $echo )
			echo $out;
		else
			return $out;
	}

	private static function numberFormat( $number, $currency = '', $decimal = 2 ) {
		$text = number_format( $number, $decimal, ',', '.' );
		$text .= '&nbsp;' . $currency;
		return $text;
	}
}
?>
