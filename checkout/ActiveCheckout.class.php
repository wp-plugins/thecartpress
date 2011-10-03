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
		$order_id = isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : 0;
		if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ok' ) {
			//We have to check if the order wasn't cancelled
			$order_status = Orders::getStatus( $order_id );
			$cancelled = tcp_get_cancelled_order_status();
			if ( $order_status == $cancelled ) $_REQUEST['tcp_checkout'] = 'ko';
		}
		if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ok' ) {
			$html = tcp_do_template( 'tcp_checkout_end', false );
			if ( strlen( $html ) == 0 ) {
				$html .= '<div class="tcp_payment_area">' . "\n" . '<div class="tcp_order_successfully">';
				$checkout_successfully_message = isset( $thecartpress->settings['checkout_successfully_message'] ) ? $thecartpress->settings['checkout_successfully_message'] : '';
				if ( strlen( $checkout_successfully_message ) > 0 ) {
					$html .= '<p>' . str_replace ( "\n" , '<p></p>', $checkout_successfully_message ) . '</p>';
				} else {
					$html .= '<span class="tcp_checkout_ok">' . __( 'The order has been completed successfully.', 'tcp' );
					if ( $shoppingCart->hasDownloadable() )
						$html .= '<br/>' . sprintf( __( 'Please, to download the products visit <a href="%s">My Downloads</a> page (login required).', 'tcp' ), home_url( 'wp-admin/admin.php?page=thecartpress/admin/DownloadableList.php' ) );
					$html .= '</span>';
				}
				$html .= '</div>' . "\n" . '</div>';
			}
			TheCartPress::removeShoppingCart();
			//if ( $order_id > 0 ) ActiveCheckout::sendMails( $order_id );
			$html .= '<br>';
			$html .= isset( $_SESSION['order_page'] ) ? $_SESSION['order_page'] : '';
			unset( $_SESSION['order_page'] );
			$html .= '<br />';
			$html .= '<a href="' . plugins_url( 'thecartpress/admin/PrintOrder.php' ) . '" target="_blank">' . __( 'Print', 'tcp' ) . '</a>';
			do_action( 'tcp_checkout_end', $order_id );
			return $html;
		} elseif  ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ko' ) {
			$html = tcp_do_template( 'tcp_checkout_end_ko', false );
			if ( strlen( $html ) > 0 ) {
				echo $html;
			} else {
				$html = '<div class="tcp_payment_area">' . "\n" . '<div class="tcp_order_unsuccessfully">';
				$checkout_unsuccessfully_message = __( 'Transaction Error. The order has been cancelled', 'tcp');
				if ( strlen( $checkout_unsuccessfully_message ) > 0 ) {
					$html .= '<p>' . str_replace ( "\n" , '<p></p>', $checkout_unsuccessfully_message ). '</p>';
				} else {
					$html .= '<span class="tcp_checkout_ko">' . __( 'Transaction Error. The order has been cancelled', 'tcp') . '</span>';
				}
				$html .= '<br/>' . sprintf( __( 'Retry again the <a href="%s">checkout process</a>', 'tcp' ), tcp_get_the_checkout_url() );
				$html .= '</div>' . "\n" . '</div>';
			}
			//ActiveCheckout::sendMails( $order_id, true, $html );
			return $html;
		} elseif ( $shoppingCart->isEmpty() ) { 
			return '<span class="tcp_shopping_cart_empty">' . __( 'The cart is empty', 'tcp' ) . '</span>';
		} else {
			$param = array(
				'validate'	=> true,
				'msg'		=> '',
			);
			$param = apply_filters( 'tcp_checkout_validate_before_enter', $param );
			if ( ! $param['validate'] ) {
				require_once( dirname( dirname( __FILE__ ) ) . '/shortcodes/ShoppingCartPage.class.php' );
				$shoppingCartPage = new TCP_ShoppingCartPage();
				echo $shoppingCartPage->show( $param['msg'] );
				return;
			}
			require_once( dirname( __FILE__ ) . '/TCPCheckoutManager.class.php' );
			$checkoutManager = new TCPCheckoutManager();
			return $checkoutManager->show();
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
			wp_mail( $to_customer, $subject, $message_to_customer, $headers );
			$to = isset( $thecartpress->settings['emails'] ) ? $thecartpress->settings['emails'] : '';				
			if ( strlen( $to ) ) {
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'To: ' . $to . "\r\n";
				$headers .= 'From: ' . $from . "\r\n";
				$message_to_merchant = apply_filters( 'tcp_send_order_mail_to_merchant', $message, $order_id );
				wp_mail( $to, $subject, $message_to_merchant, $headers );
			}
		}
	}
}
?>
