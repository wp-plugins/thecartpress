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

require_once('paypal.class.php');

class TCPPayPal extends TCP_Plugin {

	function getTitle() {
		return '<img border="0" alt="PayPal" src="https://www.paypal.com/es_ES/ES/i/logo/paypal_logo.gif" />';
	}

	function getName() {
		return 'PayPal';
	}

	function getDescription() {
		return 'PayPal payment method.<br>Author: <a href="http://thecartpress.com">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="business"><?php _e( 'Paypal eMail', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="business" name="business" size="40" maxlength="50" value="<?php echo isset( $data['business'] ) ? $data['business'] : '';?>" />
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="test_mode"><?php _e( 'Test mode', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="test_mode" name="test_mode" value="yes" <?php checked( true , isset( $data['test_mode'] ) ? $data['test_mode'] : false );?> />
		</td></tr><?php
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( 'TCPPayPal', $instance );
		$title = isset( $data['title'] ) ? $data['title'] : $this->getTitle();
		return $title;
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$business = $data['business'];
		$test_mode = $data['test_mode'];
		$new_status = $data['new_status'];
		$return_url = add_query_arg( 'tcp_checkout', 'ok', get_permalink() );
		$notify_url = plugins_url( 'thecartpress/plugins/PayPal/notify.php?action=ok' );
		$cancel_url = plugins_url( 'thecartpress/plugins/PayPal/notify.php' );//home_url();
		$paymentAmount = $shoppingCart->getTotal( true );
		$p = new paypal_class( $test_mode );
		$p->add_field( 'charset', 'utf-8' );
		$p->add_field( 'business', $business );
		$p->add_field( 'return', $return_url );
		$p->add_field( 'cancel_return', $cancel_url );
		$p->add_field( 'notify_url', $notify_url );
		$p->add_field( 'custom', $order_id . '-' . $test_mode . '-' . $new_status );
		$p->add_field( 'item_name', __( 'Shopping cart ', 'tcp' ) . get_bloginfo( 'name' ) );
		$p->add_field( 'amount', number_format( $paymentAmount, 2, '.', '' ) );
		$p->add_field( 'currency_code', tcp_get_the_currency_iso() );
		require_once( dirname( dirname( dirname( __FILE__ ) ) ) .'/daos/Orders.class.php' );
		$order = Orders::get( $order_id );
		$p->add_field( 'first_name', $order->billing_firstname );//utf8_decode
		$p->add_field( 'last_name', $order->billing_lastname );
		$p->add_field( 'address1', $order->billing_street );
		$p->add_field( 'city', $order->billing_city );
		$p->add_field( 'state', $order->billing_region_id );
		$p->add_field( 'zip', $order->billing_postcode );
		$p->add_field( 'country', $order->billing_country_id );
		echo $p->submit_paypal_post();
	}

	function saveEditFields( $data ) {
		$data['business'] = isset( $_REQUEST['business'] ) ? $_REQUEST['business'] : '';
		$data['test_mode'] = isset( $_REQUEST['test_mode'] ) ? $_REQUEST['test_mode'] == 'yes' : false;
		return $data;
	}
}
?>
