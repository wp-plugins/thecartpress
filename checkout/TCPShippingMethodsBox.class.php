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

require_once( dirname( __FILE__ ) . '/TCPCheckoutBox.class.php' );

class TCPShippingMethodsBox extends TCPCheckoutBox {
	private $errors = array();

	function get_title() {
		return __( 'Sending methods', 'tcp' );
	}

	function get_class() {
		return 'sending_layer';
	}

	function before_action() {
		$shoppingCart = TheCartPress::getShoppingCart();		
		if ( $shoppingCart->isDownloadable() ) {
			unset( $_SESSION['tcp_checkout']['shipping_methods'] );
			return 1;
		} else {
			return 0;
		}
	}

	function after_action() {
		if ( ! isset( $_REQUEST['shipping_method_id'] ) )
			$this->errors['shipping_method_id'] = __( 'You must select a shipping method', 'tcp' );
		if ( count( $this->errors ) > 0 ) {
			return false;
		} else {
			$shipping_method = array(
				'shipping_method_id' => isset( $_REQUEST['shipping_method_id'] ) ? $_REQUEST['shipping_method_id'] : 0,
			);
			$_SESSION['tcp_checkout']['shipping_methods'] = $shipping_method;
			return true;
		}
	}

	function show() {
		$shoppingCart = TheCartPress::getShoppingCart();
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
				$shipping_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_addres'] == 'Y' ) {
				$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			}
		} else { //if ( $selected_billing_address == 'Y' ) {
			$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
		}
		if ( ! $shipping_country ) $shipping_country = '';
		$applicable_sending_plugins = tcp_get_applicable_shipping_plugins( $shipping_country, $shoppingCart ); ?>
		<div class="sending_layer_info checkout_info clearfix" id="sending_layer_info"><?php
		if ( is_array( $applicable_sending_plugins ) && count( $applicable_sending_plugins ) > 0 ) : ?>
			<ul><?php
			$shipping_method_id = isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ? $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] : false;
			foreach( $applicable_sending_plugins as $plugin_data ) :
				$tcp_plugin = $plugin_data['plugin'];
				$instance = $plugin_data['instance'];
				$plugin_name = get_class( $tcp_plugin );
				$plugin_value = $plugin_name . '#' . $instance;
				if ( ! $shipping_method_id ) $shipping_method_id = $plugin_value;?>
				<li>
				<input type="radio" id="<?php echo $plugin_name;?>_<?php echo $instance;?>" name="shipping_method_id" value="<?php echo $plugin_value;?>" <?php checked( $plugin_value, $shipping_method_id );?> />
				<label for="<?php echo $plugin_name;?>_<?php echo $instance;?>"><?php echo $tcp_plugin->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );?></label>
				</li>
			<?php endforeach;?>
			</ul>
			<?php if ( isset( $this->errors['shipping_method_id'] ) ) : ?><br/><span class="error"><?php echo $this->errors['shipping_method_id'];?></span><?php endif;?>
		<?php else : ?>
			<?php _e( 'There is not applicable methods', 'tcp' ); ?>
		<?php endif;
		do_action( 'tcp_checkout_sending' );?>
		</div><!-- sending_layer_info --><?php
		return true;
	}
}
?>
