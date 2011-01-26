<?
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

class Remboursement extends TCP_Plugin {

	function getTitle() {
		return 'Remboursement';
	}

	function getDescription() {
		return 'Remboursement payment method.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
			<th scope="row">
				<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
			</th><td>
				<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
			</td>
		</tr><tr valign="top">
			<th scope="row">
				<label for="percentage"><?php _e( 'Percentage', 'tcp' );?>:</label>
			</th><td>
				<input type="text" id="percentage" name="percentage" size="5" maxlength="8" value="<?php echo isset( $data['percentage'] ) ? $data['percentage'] : '';?>" />
				<br /><span class="description"><?php _e( 'Leave this field to blank (or zero) to use the fix value', 'tcp' );?></span>
			</td>
		</tr><tr valign="top">
			<th scope="row">
				<label for="fix"><?php _e( 'Fix', 'tcp' );?>:</label>
			</th><td>
				<input type="text" id="fix" name="fix" size="5" maxlength="8" value="<?php echo isset( $data['fix'] ) ? $data['fix'] : '';?>" />
			</td>
		</tr><?php
	}

	function saveEditFields( $data ) {
		$data['notice'] = isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		$data['percentage'] = isset( $_REQUEST['percentage'] ) ? (float)$_REQUEST['percentage'] : '0';
		$data['fix'] = isset( $_REQUEST['fix'] ) ? (float)$_REQUEST['fix'] : '0';
		return $data;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart, $currency ) {
		$cost = $this->getCost( $instance, $shippingCountry, $shoppingCart );
		return __( 'Remboursement. The cost of the service is ', 'tcp' ) . number_format( $cost, 2 ) . '&nbsp;' . $currency;
	}

	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$percentage = isset( $data['percentage'] ) ? $data['percentage'] : 0;
		if ( $percentage > 0 )
			return $shoppingCart->getTotal() * $percentage / 100;
		else {
			$fix = isset( $data['fix'] ) ? $data['fix'] : 0;
			return $fix;
		}
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $currency, $order_id ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$cost = $this->getCost( $instance, $shippingCountry, $shoppingCart );
		?><p><?php echo $data['notice'];?></p><p><?php _e( 'The cost of the service is' , 'tcp');?> <?php echo number_format( $cost, 2);?> <?php echo $currency;?></p><?php
		require_once( dirname( dirname (__FILE__ ) ) . '/daos/Orders.class.php' );
		Orders::editStatus( $order_id, $data['new_status'] );
	}
}
?>