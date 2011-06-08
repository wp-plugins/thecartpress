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

class ActiveCheckout {//shortcode
	function show() {
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ok' ) {
			$html = tcp_do_template( 'tcp_checkout_end', false );
			$order_id = isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : 0;
			if ( strlen( $html ) > 0 ) {
				echo $html;
			} else {
				echo '<div class="tcp_payment_area">' . "\n" . '<div class="tcp_order_successfully">';
				$checkout_successfully_message = isset( $thecartpress->settings['checkout_successfully_message'] ) ? $thecartpress->settings['checkout_successfully_message'] : '';
				if ( strlen( $checkout_successfully_message ) > 0 )
					echo '<p>', str_replace ( "\n" , '<p></p>', $checkout_successfully_message ), '</p>';
				else
					 echo '<span class="tcp_checkout_ok">' . __( 'The order has been completed successfully.', 'tcp' ) . '</span>';
				echo '</div>' . "\n" . '</div>';
			}
			if ( $order_id > 0 ) ActiveCheckout::sendMails( $order_id );
			echo $_SESSION['order_page'];
			//unset( $_SESSION['order_page'] );//TODO
			echo '<br />';
			echo '<a href="' . plugins_url( 'thecartpress/admin/PrintOrder.php' ) . '" target="_blank">' . __( 'Print', 'tcp' ) . '</a>';
			do_action( 'tcp_checkout_end', $order_id );
			return;
		} elseif ( $shoppingCart->isEmpty() ) { 
			echo '<span class="tcp_shopping_cart_empty">' . __( 'The cart is empty', 'tcp' ) . '</span>';
		} else {
			require_once( dirname( __FILE__ ) . '/TCPCheckoutManager.class.php' );
			//Default checkout boxes
			require_once( dirname( __FILE__ ) . '/TCPSigninBox.class.php' );
			require_once( dirname( __FILE__ ) . '/TCPBillingBox.class.php' );
			require_once( dirname( __FILE__ ) . '/TCPShippingBox.class.php' );
			require_once( dirname( __FILE__ ) . '/TCPShippingMethodsBox.class.php' );
			require_once( dirname( __FILE__ ) . '/TCPPaymentMethodsBox.class.php' );
			require_once( dirname( __FILE__ ) . '/TCPCartBox.class.php' );
			require_once( dirname( __FILE__ ) . '/TCPNoticeBox.class.php' );
			new TCPCheckoutManager();
		}
	}

	static function sendMails( $order_id, $error = false, $error_text = '' ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/OrderPage.class.php' );
		global $thecartpress;
		$order = Orders::get( $order_id );
		if ( $order ) {
			$customer_email = array();
			if ( strlen( $order->shipping_email ) > 0 ) $customer_email[] = $order->shipping_email;
			if ( strlen( $order->billing_email ) > 0 && $order->shipping_email != $order->billing_email ) $customer_email[] = $order->billing_email;
			$to_customer = implode( ',', $customer_email );
			$from = isset( $thecartpress->settings['from_email'] ) && strlen( $thecartpress->settings['from_email'] ) > 0 ? $thecartpress->settings['from_email'] : 'no-response@thecartpress.com';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'To: ' . $to_customer . "\r\n";
			$headers .= 'From: ' . $from . "\r\n";
			//$headers .= 'Cc: ' . $cc . "\r\n";
			//$headers .= 'Bcc: ' . $bcc . "\r\n";
			$message = '';
			$subject = sprintf( __( 'Order from %s', 'tcp' ), get_bloginfo( 'name' ) );
			if ( $error ) {
				$subject = __( 'Error in transaction.', 'tcp' ) . ' ' . $subject;
				$message = $error_text;
			}
			$message .= isset( $_SESSION['order_page'] ) ? $_SESSION['order_page'] : OrderPage::show( $order_id, true, false );
			$message .= tcp_do_template( 'tcp_checkout_email', false );
			$message_to_customer = apply_filters( 'tcp_send_order_mail_to_customer', $message, $order_id );
			mail( $to_customer, $subject, $message_to_customer, $headers );
			$to = isset( $thecartpress->settings['emails'] ) ? $thecartpress->settings['emails'] : '';				
			if ( strlen( $to ) ) {
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'To: ' . $to . "\r\n";
				$headers .= 'From: ' . $from . "\r\n";
				$message_to_merchant = apply_filters( 'tcp_send_order_mail_to_merchant', $message, $order_id );
				mail( $to, $subject, $message, $headers );
			}
		}
	}
}
?>
