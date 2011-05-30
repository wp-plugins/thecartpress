<?php

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Addresses.class.php' );

class TCPPaymentMethodsBox extends TCPCheckoutBox {
	private $errors = array();

	function get_title() {
		return __( 'Payment methods', 'tcp' );
	}

	function get_class() {
		return 'payment_layer';
	}

	function after_action() {
		if ( ! isset( $_REQUEST['payment_method_id'] ) )
			$this->errors['payment_method_id'] = __( 'You must select a payment method', 'tcp' );
		if ( count( $this->errors ) > 0 ) {
			return false;
		} else {
			$payment_method = array(
				'payment_method_id' => isset( $_REQUEST['payment_method_id'] ) ? $_REQUEST['payment_method_id'] : 0,
			);
			$_SESSION['tcp_checkout']['payment_methods'] = $payment_method;
			return true;
		}
	}

	function show() {
		$shoppingCart = TheCartPress::getShoppingCart();
		$billing_country = '';
		$selected_billing_address = isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : false;
		if ( $selected_billing_address == 'new' ) {
			$billing_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
		} else { //if ( $selected_billing_address == 'Y' ) {
			$billing_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
		}
		$shipping_country = '';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			$shipping_country = $billing_country;
		} elseif ( $selected_shipping_address == 'Y' ) {
			$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
		}
		$applicable_plugins = tcp_get_applicable_payment_plugins( $billing_country, $shoppingCart );?>
		<div class="payment_layer_info checkout_info clearfix" id="payment_layer_info">
		<?php if ( is_array( $applicable_plugins ) && count( $applicable_plugins ) > 0 ) : ?>
			<ul><?php
			$first_plugin_value = false;
			$payment_method_id = isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ? $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] : false;
			foreach( $applicable_plugins as $plugin_data ) :
				$tcp_plugin = $plugin_data['plugin'];
				$instance = $plugin_data['instance'];
				$plugin_name = get_class( $tcp_plugin );
				$plugin_value = $plugin_name . '#' . $instance;
				if ( ! $payment_method_id ) $payment_method_id = $plugin_value;?>
				<li>
					<input type="radio" id="<?php echo $plugin_name;?>"	name="payment_method_id" value="<?php echo $plugin_value;?>" 
					<?php checked( $plugin_value, $payment_method_id );?> />
					<label for="<?php echo $plugin_name;?>"><?php echo $tcp_plugin->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );?></label>
				</li>
			<?php endforeach;?>
			</ul>
			<?php if ( isset( $this->errors['payment_method_id'] ) ) : ?><br/><span class="error"><?php echo $this->errors['payment_method_id'];?></span><?php endif;?>
		<?php endif;?>
		<?php do_action( 'tcp_checkout_payments' );?>
		</div><!-- payment_layer_info --><?php
		return true;
	}
}
?>
