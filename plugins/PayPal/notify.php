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

$custom = isset( $_REQUEST['custom'] ) ? $_REQUEST['custom'] : '0-1-CANCELLED-TCPPayPal-0';//Order_id-test_mode-new_status-class-instance
$custom = explode( '-', $custom );
$order_id = $custom[0];
$test_mode = $custom[1] == '1';
$new_status = $custom[2];
$classname = $custom[3];
$instance = $custom[4];
$transaction_id = isset( $_REQUEST['txn_id'] ) ? $_REQUEST['txn_id'] : '';

require_once('paypal.class.php' );
$wordpress_path = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/';
include_once( $wordpress_path . 'wp-config.php' );			//loads WordPress
$thecartpress_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/';
require_once( $thecartpress_path . 'daos/Orders.class.php');
require_once( $thecartpress_path . 'checkout/ActiveCheckout.class.php');

$cancelled_status = tcp_get_cancelled_order_status();
$completed_status = tcp_get_completed_order_status();

if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ko' ) {
	Orders::editStatus( $order_id, $cancelled_status, $transaction_id, __( 'Customer cancel at PayPal', 'tcp' ) );
	$redirect = add_query_arg( 'tcp_checkout', 'ko', get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) ) );
?>
<html><head><title>Canceling Payment</title>
<script language="javascript">
//<!--
window.location="<?php echo $redirect;?>";
//-->
</script>
</head>
<body><noscript><meta http-equiv="refresh" content="1;url=<?php echo $redirect;?>"></noscript>
<p>Canceling your order. Please wait...</p>
</body></html>
<?php
} else {
	$data = tcp_get_payment_plugin_data( $classname, $instance );
	$p = new paypal_class( $test_mode, $data['logging'] );
	if ( $p->validate_ipn() ) {
		$order_row = Orders::getOrderByTransactionId( $classname, $transaction_id );
		if ( $p->ipn_data['business'] == $data['business'] ) {  //check it is right business
			if ( isset( $order_row ) ) $comment = $order_row->comment_internal;
			else $comment = '';
			$additional = $p->ipn_data['payment_status'] . ' ';
			switch ( $p->ipn_data['payment_status'] ) {
				case 'Completed':
				case 'Canceled_Reversal':
				case 'Processed': //should check price, but with profile options, we can't know it, could check currency
					$comment .= 'mc_gross=' . $p->ipn_data['mc_gross'] . ' ' . $p->ipn_data['mc_currency'];
					$comment .= ', mc_shipping=' . $p->ipn_data['mc_shipping'] . ', tax=' . $p->ipn_data['tax'];
					if ( $p->ipn_data['receipt_id'] ) $additional .= "\nPayPal Receipt ID: " . $p->ipn_data['receipt_id'];
					if ( $p->ipn_data['memo'] ) $additional .= "\nCustomer comment: " . $p->ipn_data['memo'];
					if ( Orders::isDownloadable( $order_id ) )
						Orders::editStatus( $order_id, $completed_status, $transaction_id, $comment . $additional );
					else
						Orders::editStatus( $order_id, $new_status, $transaction_id, $comment . $additional );
					break;
				case 'Refunded':
				case 'Reversed':
					$additional = $p->ipn_data['payment_status']. ': ' . $p->ipn_data['reason_code'];
					Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, $comment . $additional );
					break;
				case 'Expired':
				case 'Failed':
					Orders::editStatus( $order_id, Orders::$ORDER_PROCESSING, $transaction_id, $p->ipn_data['payment_status'] );
					require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/checkout/ActiveCheckout.class.php' );
					break;
				case 'Pending':
					$additional .= $p->ipn_data['pending_reason'];
					Orders::editStatus( $order_id, Orders::$ORDER_PENDING, $transaction_id, $comment . $additional );
					break;
				case 'Expired':
				case 'Failed':
				case 'Denied':
				case 'Voided':
					Orders::editStatus( $order_id, $cancelled_status, $transaction_id, $comment . $additional );
					break;
				default :
					break;
			}
			if ( 'Pending' != $p->ipn_data['payment_status'] ) // so you don't get 2 emails for normal transaction
				ActiveCheckout::sendMails( $order_id, $additional );
		}
	} else {
	 //save for further investigation?
	}
}
?>
