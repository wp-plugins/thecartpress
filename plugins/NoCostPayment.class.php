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
		return 'No payment method.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart = false ) {
		//if ( $shoppingCart === false ) $shoppingCart = TheCartPress::getShoppingCart();
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$title = isset( $data['title'] ) ? $data['title'] : '';
		$title = tcp_string( 'TheCartPress', 'pay_NoCostPayment-title', $title );
		return $title; //__( 'No payment.', 'tcp' );// . ': ' . $shoppingCart->getTotal();
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
			<th scope="row">
				<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
			</th><td>
				<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="redirect"><?php _e( 'Redirect automatically', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="checkbox" id="redirect" name="redirect" value="yes" <?php checked( isset( $data['redirect'] ) ? $data['redirect'] : false ); ?> />
				<p class="description"><?php _e( 'If checked, Checkout page will completed the order. Customers will not need to click on "Finish" button.', 'tcp' ); ?></p>
			</td>
		</tr><?php
	}

	function saveEditFields( $data ) {
		$data['notice'] = isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		$data['redirect'] = isset( $_REQUEST['redirect'] );
		return $data;
	}

	function sendPurchaseMail() {
		return false;
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$url = add_query_arg( 'order_id', $order_id, tcp_get_the_checkout_ok_url() );
		$title = isset( $data['title'] ) ? $data['title'] : '';
		$redirect = isset( $data['redirect'] ) ? $data['redirect'] : false; ?>
		<p><?php echo tcp_string( 'TheCartPress', 'pay_NoCostPayment-title', $title ); ?></p>
		<p><?php echo $data['notice'];?></p>
		<p><input type="button" id="tcp_no_cost_payment_button" class="tcp_pay_button" value="<?php _e( 'Finish', 'tcp' );?>" onclick="window.location.href = '<?php echo $url; ?>';"/></p>
		<?php require_once( TCP_DAOS_FOLDER . '/Orders.class.php' );
		Orders::editStatus( $order_id, $data['new_status'] ); //Orders::$ORDER_PROCESSING );
		require_once( TCP_CHECKOUT_FOLDER . '/ActiveCheckout.class.php' );
		ActiveCheckout::sendMails( $order_id );
		if ( $redirect ) : ?>
		<script type="text/javascript">
			jQuery( '#tcp_no_cost_payment_button' ).click();
		</script><?php endif;
	}
}
?>
