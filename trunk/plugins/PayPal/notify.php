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

/*$subject = 'Instant Payment Notification - Recieved Payment';
$to = '';    //  your email
$body =  "An instant payment notification was recieved\n";
$body .= "from ".$p->ipn_data['payer_email']." on ".date('m/d/Y');
$body .= " at ".date('g:i A')."\n\nDetails:\n";
foreach ($_REQUEST as $key => $value)
	$body .= "\n$key: $value";
mail($to, $subject, $body);*/

$custom = isset( $_REQUEST['custom'] ) ? $_REQUEST['custom'] : '0-1-CANCELLED';//Order_id-test_mode-new_status
$custom = split( '-', $custom );
$order_id = $custom[0];
$test_mode = $custom[1] == '1';
$new_status = $custom[2];

//mail($to, $subject, "order_id=$order_id\ntest_mode=$test_mode\nnew_status=$new_status");

require_once(' paypal.class.php' );
$p = new paypal_class( $test_mode );

$wordpress_path = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/';
include_once( $wordpress_path . 'wp-config.php' );
include_once( $wordpress_path . 'wp-includes/wp-db.php' );

//global $wpdb;

$thecartpress_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/';
require_once( $thecartpress_path . 'daos/Orders.class.php');

if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'ok' )
	if ( $p->validate_ipn() )
		if ( Orders::isDownloadable( $order_id ) )
			Orders::editStatus( $order_id, Orders::$ORDER_COMPLETED );
		else
			Orders::editStatus( $order_id, $new_status );
	else
		Orders::editStatus( $order_id, Orders::$ORDER_CANCELLED, 'Error IPN (PayPal).' );
else
	Orders::editStatus( $order_id, Orders::$ORDER_CANCELLED, 'Cancel PayPal.' );
?>