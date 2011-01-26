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


class NoCostPayment extends TCP_Plugin {
	function getTitle() {
		return 'No Payment';
	}

	function getDescription() {
		return 'No payment method. Only for test purpose.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
		</th><td>
			<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
		</td></tr>
	<?php
	}

	function saveEditFields( $data ) {
		$data['notice'] = isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		return $data;
	}

	function isApplicable( $shippingCountry, $shoppingCart, $currency ) {
		return true;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart, $currency ) {
		return __( 'No payment!!, for test purpose.', 'tcp' );
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $currency, $order_id ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		echo '<p>', __( 'No payment!!, for test purpose.', 'tcp' ), '</p>';
		echo '<p>', $data['notice'], '</p>';
		require_once( dirname( dirname (__FILE__ ) ) . '/daos/Orders.class.php' );
		Orders::editStatus( $order_id, Orders::$ORDER_PROCESSING );
	}
}
?>
