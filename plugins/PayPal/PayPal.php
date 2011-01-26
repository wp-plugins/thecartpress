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

class PayPal extends TCP_Plugins {

	function getTitle() {
		return 'PayPal';
	}

	function getDescription() {
		return 'PayPal payment method.<br>Author: <a href="http://thecartpress.com">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="business"><?php _e( 'Bussines id.', 'tcp' );?>:</label>
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

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart, $currency ) {
		return 'PayPal';
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $currency ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$business = $data['business'];
		$test_mode = $data['test_mode'];
		$new_status = $data['new_status'];
		$return_url = home_url();
		$notify_url = plugin_basename( dirname( __FILE__ ) ) . 'notify.php?action=ok';
		$cancel_url = home_url();
		$paymentAmount = $shoppingCart->getTotal();

		$p = new paypal_class($test_mode);
		$p->add_field('business', $business);
		$p->add_field('return', $return_url);
		$p->add_field('cancel_return', $cancel_url);
		$p->add_field('notify_url', $notify_url);
		$p->add_field('custom', $order_id.'-'.$test_mode.'-'.$new_status);
		$p->add_field('item_name', 'Shopping cart '.bloginfo('name'));
		$p->add_field('amount', number_format($paymentAmount, 2, '.', ''));
		$p->add_field('currency_code', $currency);
		
		/*$p->add_field('first_name', 'John');
		$p->add_field('last_name', 'Doe');
		$p->add_field('address1', '345 Lark Ave');
		$p->add_field('city', 'San Jose');
		$p->add_field('state', 'CA');
		$p->add_field('zip', '95121');
		$p->add_field('country', 'US');*/

		return $p->submit_paypal_post();
	}

	function saveEditFields( $data ) {
		$data['business'] = $zones;
		$data['test_mode'] = $ranges;
		return $data;
	}
}
?>
