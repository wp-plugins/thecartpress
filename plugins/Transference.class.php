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

class Transference extends TCP_Plugin {

	function getTitle() {
		return 'Transference';
	}

	function getDescription() {
		return 'Transference payment method.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data ) { ?>
		<tr valign="top">
		<th scope="row">
			<label for="notice"><?php _e( 'Notice', 'tcp' ); ?>:</label>
		</th><td>
			<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : ''; ?></textarea>
		</td></tr><tr valign="top">
		<th scope="row">
			<label for="owner"><?php _e( 'Owner', 'tcp' ); ?>:</label>
		</th><td>
			<input type="text" id="owner" name="owner" size="40" maxlength="50" value="<?php echo isset( $data['owner'] ) ? $data['owner'] : ''; ?>" />
		</td></tr><tr valign="top">
		<th scope="row">
			<label for="bank"><?php _e( 'bank', 'tcp' ); ?>:</label>
		</th><td>
			<input type="text" id="bank" name="bank" size="40" maxlength="50" value="<?php echo isset( $data['bank'] ) ? $data['bank'] : ''; ?>" />
		</td></tr><tr valign="top">
		<th scope="row">
			<label for="account"><?php _e( 'Account', 'tcp' ); ?>:</label>
		</th><td><?php
			if ( isset( $data['account'] ) ) {
				$account1 = substr( $data['account'], 0, 4 );
				$account2 = substr( $data['account'], 4, 4 );
				$account3 = substr( $data['account'], 8, 2 );
				$account4 = substr( $data['account'], 10, 10 );
			} else {
				$account1 = '';
				$account2 = '';
				$account3 = '';
				$account4 = '';
			}?>
			<input type="text" id="account1" name="account1" size="4" maxlength="4" value="<?php echo $account1; ?>" />
			<input type="text" id="account2" name="account2" size="4" maxlength="4" value="<?php echo $account2; ?>" />
			<input type="text" id="account3" name="account3" size="2" maxlength="2" value="<?php echo $account3; ?>" />
			<input type="text" id="account4" name="account4" size="10" maxlength="10" value="<?php echo $account4; ?>" />
		</td></tr><tr valign="top">
		<th scope="row">
			<label for="iban"><?php _e( 'IBAN', 'tcp' ); ?>:</label>
		</th><td>
			<input type="text" id="iban" name="iban" size="20" maxlength="40" value="<?php echo isset( $data['iban'] ) ? $data['iban'] : ''; ?>" />
		</td></tr><tr valign="top">
		<th scope="row">
			<label for="swift"><?php _e( 'SWIFT', 'tcp' ); ?>:</label>
		</th><td>
			<input type="text" id="swift" name="swift" size="20" maxlength="40" value="<?php echo isset( $data['swift'] ) ? $data['swift'] : ''; ?>" />
		</td></tr>
	<?php }

	function saveEditFields( $data ) {
		$data['notice']		= isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		$data['owner']		= isset( $_REQUEST['owner'] ) ? $_REQUEST['owner'] : '';
		$data['bank']		= isset( $_REQUEST['bank'] ) ? $_REQUEST['bank'] : '';
		$account1			= isset( $_REQUEST['account1'] ) ? $_REQUEST['account1'] : '';
		$account2			= isset( $_REQUEST['account2'] ) ? $_REQUEST['account2'] : '';
		$account3			= isset( $_REQUEST['account3'] ) ? $_REQUEST['account3'] : '';
		$account4			= isset( $_REQUEST['account4'] ) ? $_REQUEST['account4'] : '';
		$data['account']	= $account1 . $account2 . $account3 . $account4;
		$data['iban']		= isset( $_REQUEST['iban'] ) ? $_REQUEST['iban'] : '';
		$data['swift']		= isset( $_REQUEST['swift'] ) ? $_REQUEST['swift'] : '';
		return $data;
	}

	function sendPurchaseMail() {
		return false;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( 'Transference', $instance );
		return isset( $data['title'] ) ? $data['title'] : $this->getTitle();
	}

	function getNotice( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		ob_start(); ?>
		<p><?php echo $data['notice']; ?></p>
		<table class="tcp-bank-account">
			<tr><th scope="row"><?php _e( 'Owner', 'tcp' ); ?>: </th><td><?php echo $data['owner']; ?></td></tr>
			<tr><th scope="row"><?php _e( 'Bank', 'tcp' ); ?>: </th><td><?php echo $data['bank']; ?></td></tr>
			<tr><th scope="row"><?php _e( 'Account', 'tcp' ); ?>: </th><td><?php echo $data['account']; ?></td></tr>
			<tr><th scope="row"><?php _e( 'IBAN', 'tcp' ); ?>: </th><td><?php echo $data['iban']; ?></td></tr>
			<tr><th scope="row"><?php _e( 'SWIFT', 'tcp' ); ?>: </th><td><?php echo $data['swift']; ?></td></tr>
		</table>
		<?php return ob_get_clean();
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		$url = add_query_arg( 'tcp_checkout', 'ok', tcp_get_the_checkout_url() );
		$url = add_query_arg( 'order_id', $order_id, $url );
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$additional = $this->getNotice( $instance, $shippingCountry, $shoppingCart, $order_id );
		echo $additional; ?>
		<p>
			<input type="button" class="tcp_pay_button" id="tcp_transference" value="<?php _e( 'Finish', 'tcp' ); ?>" onclick="window.location.href='<?php echo $url; ?>';"/>
		</p>
		<?php require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		$new_status = isset( $data['new_status'] ) ? $data['new_status'] : Orders::$ORDER_PROCESSING;
		Orders::editStatus( $order_id, $new_status, 'no-id' );
		require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
		ActiveCheckout::sendMails( $order_id );//, $additional );
	}
}
?>