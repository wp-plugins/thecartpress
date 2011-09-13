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
		return 'PayPal Standard payment method.<br>Author: <a href="http://thecartpress.com">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="business"><?php _e( 'PayPal eMail', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="business" name="business" size="40" maxlength="50" value="<?php echo isset( $data['business'] ) ? $data['business'] : '';?>" />
		</td></tr>

		<tr valign="top">
		<th scope="row">
			<?php _e( 'PayPal prompt for shipping address', 'tcp' );?>:
		</th><td>
			<input type="radio" id="no_shipping_0" name="no_shipping" value="0" <?php checked( 0 , isset( $data['no_shipping'] ) ? $data['no_shipping'] : 0 );?> />
			<label for="no_shipping"><?php _e( 'PayPal prompt for an address, but do not require one', 'tcp' );?></label><br />
			<input type="radio" id="no_shipping_1" name="no_shipping" value="1" <?php checked( 1 , isset( $data['no_shipping'] ) ? $data['no_shipping'] : 0 );?> />
			<label for="no_shipping"><?php _e( 'PayPal do not prompt for an address', 'tcp' );?></label><br />
			<input type="radio" id="no_shipping_2" name="no_shipping" value="2" <?php checked( 2 , isset( $data['no_shipping'] ) ? $data['no_shipping'] : 0 );?> />
			<label for="no_shipping"><?php _e( 'PayPal prompt for an address, and require one', 'tcp' );?></label><br />
			<span class="description"><?php _e( 'Be sure to match this in the Checkout Editor', 'tcp' );?></span>
		</td></tr>
		<tr valign="top">
		<th scope="row">&nbsp;
		</th><td>
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="profile_shipping"><?php _e( 'Use PayPal profile shipping', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="profile_shipping" name="profile_shipping" value="yes" <?php checked( true , isset( $data['profile_shipping'] ) ? $data['profile_shipping'] : false );?> />
			<?php if ( preg_match("/lb|kg/", tcp_get_the_unit_weight() ) == 0 ) : ?>
			<span class="description"><strong><?php _e( 'Change your Unit Weight in TCP Settings. PayPal only allows lbs or kgs.', 'tcp' );?></strong></span>
			<?php endif; ?>
			<span class="description"> <?php _e( 'Be sure to enable shipping overrides in your PayPal profile.', 'tcp' );?></span>
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="profile_taxes"><?php _e( 'Use PayPal profile taxes', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="profile_taxes" name="profile_taxes" value="yes" <?php checked( true , isset( $data['profile_taxes'] ) ? $data['profile_taxes'] : false );?> />
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<?php _e( 'PayPal is sent', 'tcp' );?>:
		</th><td>
			<input type="radio" id="send_detail" name="send_detail" value="0" <?php checked( 0 , isset( $data['send_detail'] ) ? $data['send_detail'] : 0 );?> />
			<label for="send_detail"><?php _e( 'one total amount', 'tcp' );?></label><br />
			<input type="radio" id="send_detail" name="send_detail" value="1" <?php checked( 1 , isset( $data['send_detail'] ) ? $data['send_detail'] : 0 );?> />
			<label for="send_detail"><?php _e( 'item detail, each with amount', 'tcp' );?></label><br />
		</td></tr>
		<tr valign="top">
		<th scope="row">&nbsp;
		</th><td>
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="logging"><?php _e( 'Log IPN data', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="logging" name="logging" value="yes" <?php checked( true , isset( $data['logging'] ) ? $data['logging'] : false );?> />
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="cpp_cart_border_color"><?php _e( 'Cart border color', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="cart_border_color" name="cpp_cart_border_color" size="6" maxlength="8" value="<?php echo isset( $data['cpp_cart_border_color'] ) ? $data['cpp_cart_border_color'] : '';?>" />
			<span class="description"><?php _e( 'Optional, for customizing the PayPal page, and can be set from your PayPal account.<br /> Enter a 6 digit hex color code.', 'tcp' );?></span>
		</td></tr>

		<tr valign="top">
		<th scope="row">
			<label for="test_mode"><?php _e( 'PayPal sandbox test mode', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="test_mode" name="test_mode" value="yes" <?php checked( true , isset( $data['test_mode'] ) ? $data['test_mode'] : false );?> />
		</td></tr><?php
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( 'TCPPayPal', $instance );
		$title = isset( $data['title'] ) ? $data['title'] : $this->getTitle();
		return $title;
	}

	function saveEditFields( $data ) {
		$data['business']				= isset( $_REQUEST['business'] ) ? $_REQUEST['business'] : '';
		$data['profile_shipping']		= isset( $_REQUEST['profile_shipping'] ) ? $_REQUEST['profile_shipping'] == 'yes' : false;
		$data['profile_taxes']			= isset( $_REQUEST['profile_taxes'] ) ? $_REQUEST['profile_taxes'] == 'yes' : false;
		$data['no_shipping']			= isset( $_REQUEST['no_shipping'] ) ? $_REQUEST['no_shipping'] : 0;
		$data['send_detail']			= isset( $_REQUEST['send_detail'] ) ? $_REQUEST['send_detail'] : 0;
		$data['logging']				= isset( $_REQUEST['logging'] ) ? $_REQUEST['logging'] == 'yes' : false;
		$data['cpp_cart_border_color']	= isset( $_REQUEST['cpp_cart_border_color'] ) ? $_REQUEST['cpp_cart_border_color'] : '';
		$data['test_mode']				= isset( $_REQUEST['test_mode'] ) ? $_REQUEST['test_mode'] == 'yes' : false;
		return $data;
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$business = $data['business'];
		$test_mode = $data['test_mode'];
		$profile_shipping = $data['profile_shipping'];
		$profile_taxes = $data['profile_taxes'];
		$no_shipping = isset($data['no_shipping'])?$data['no_shipping']:0 ;
		$send_detail = isset($data['send_detail'])?$data['send_detail']:0 ;
		$logging = $data['logging'];
		$merchant = get_bloginfo( 'name' );
		$new_status = $data['new_status'];
		$p = new paypal_class( $test_mode, $logging );
		$p->add_field( 'charset', 'utf-8' );
		$p->add_field( 'business', $business );
		$p->add_field( 'return', add_query_arg( 'tcp_checkout', 'ok', get_permalink() ) );
		$p->add_field( 'cancel_return', add_query_arg( 'tcp_checkout', 'ko', plugins_url( 'thecartpress/plugins/PayPal/notify.php' ) ) );
		$p->add_field( 'notify_url', plugins_url( 'thecartpress/plugins/PayPal/notify.php' ) );
		$p->add_field( 'custom', $order_id . '-' . $test_mode . '-' . $new_status . '-' . get_class($this) . '-' . $instance);
		$p->add_field( 'currency_code', tcp_get_the_currency_iso() );
		$p->add_field( 'cbt', __( 'Return to ', 'tcp' ) . $merchant );		 //text for the Return to Merchant button
		$p->add_field( 'no_shipping', $no_shipping );
		if ( empty($send_detail) && empty($profile_shipping) && empty($profile_taxes) ) {		// Buy Now - one total
			$p->add_field( 'item_name', __( 'Purchase from ', 'tcp' ) . $merchant );
			$p->add_field( 'amount', tcp_number_format( $shoppingCart->getTotal( true ) ) );
		} else { // Cart upload
			$p->add_field( 'cmd', '_cart' );
			$p->add_field( 'upload', '1' );
			$discount = $shoppingCart->getAllDiscounts();
			if ( $discount > 0 )
				$p->add_field( 'discount_amount_cart', number_format( $discount, 2, '.', '' ) );
			$i = 1;
			if ( $send_detail ) { // item detail
				foreach( $shoppingCart->getItems() as $item ) {
					$p->add_field( "item_name_$i", strip_tags( html_entity_decode( $item->getTitle(), ENT_QUOTES ) ) );
					$p->add_field( "amount_$i", number_format( $item->getUnitPrice(), 2, '.', '' ) );
					$p->add_field( "quantity_$i", (int) $item->getUnits() );
					if ( $profile_shipping ) {
						$p->add_field( "weight_$i", $item->getWeight() );
						if ( $item->isFreeShipping() ) $p->add_field( "shipping_$i", '0' );
					}
					$i++;
				}
			} else { // one total for items, with separate other costs
				$p->add_field( "item_name_$i", __( 'Purchase from ', 'tcp' ) . $merchant );
				$p->add_field( 'amount_' . $i++, number_format( $shoppingCart->getTotalToShow() + $discount, 2, '.', '' ) );
				if ( $profile_shipping ) {
					$p->add_field( 'weight_cart', $shoppingCart->getWeight() );
					$p->add_field( 'quantity', $shoppingCart->getCount() );
				}
			}

			foreach( $shoppingCart->getOtherCosts() as $cost ) { // TODO Not internationalized!
				$desc = $cost->getDesc();
				if ( false !== stripos( $desc, __( 'Shipping cost', 'tcp' ) ) ) { //TODO
					if ( empty( $profile_shipping ) ) $p->add_field( 'shipping_1', $cost->getCost() );
				} else {
					$p->add_field( "item_name_$i", $desc );
					$p->add_field( 'amount_' . $i++, $cost->getCost() );
				}
			}
			$taxes = $shoppingCart->getTotal() - $shoppingCart->getTotalToShow();
			if ( empty( $profile_taxes ) ) $p->add_field( 'tax_cart', number_format( $taxes, 2, '.', '' ) );
			if ( $profile_shipping ) $p->add_field( 'weight_unit', (trim(tcp_get_the_unit_weight(),'.')=='kg'? "kgs":"lbs") );		//only 2 options for PayPal
		}
		require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/daos/Orders.class.php' );
		$order = Orders::get( $order_id );
		$p->add_field( 'first_name', $order->billing_firstname ); //utf8_decode
		$p->add_field( 'last_name', $order->billing_lastname );
		$p->add_field( 'address1', $order->billing_street );
		$p->add_field( 'city', $order->billing_city );
		$p->add_field( 'state', $order->billing_region_id );
		$p->add_field( 'zip', $order->billing_postcode );
		$p->add_field( 'country', $order->billing_country_id );
		$p->add_field( 'email', $order->billing_email );
		if ( ! empty( $data['cpp_cart_border_color'] ) ) $p->add_field( 'cpp_cart_border_color', $data['cpp_cart_border_color'] );
		echo $p->submit_paypal_post();


		/*if ( ! $this->isSupportedCurrency( $currency_code ) ) {
			require_once( dirname( __FILE__ ) . '/PayPal_Platform_PHP_SDK/lib/AdaptivePayments.php' );
			$ap = new AdaptivePayments();
			$requestEnvelope = new RequestEnvelope();
			$requestEnvelope->detailLevel = 0;
			$requestEnvelope->errorLanguage = 'en_US';
			$baseAmountList = new CurrencyList();
			$baseAmountList->currency = array( 'amount' => $payment_amount, 'code' => $currency_code );
			$convertToCurrencyList = new CurrencyCodeList();
			$convertToCurrencyList->currencyCode = 'EUR';
			$ccReq = new ConvertCurrencyRequest();
			$ccReq->baseAmountList = $baseAmountList;
			$ccReq->convertToCurrencyList = $convertToCurrencyList;
			$ccReq->requestEnvelope = $requestEnvelope;
			$result = $ap->ConvertCurrency($ccReq);
			$error = $ap->isSuccess != 'Success';
			if ( ! $error ) {
				$resultingCurrencyList = $result->estimatedAmountTable->currencyConversionList;
				//$baseAmount = $resultingCurrencyList->baseAmount->amount;
				//$baseAmountCode = $resultingCurrencyList->baseAmount->code;
				$payment_amount = $resultingCurrencyList->currencyList->currency->amount;
				$currency_code = $resultingCurrencyList->currencyList->currency->code;
			} else {
				$error_msg = $ap->getErrorMessage();
			}
		} else {
			$error = false;
		}*/

		/*if ( ! $error ) {
			$p = new paypal_class( $test_mode );
			$p->add_field( 'charset', 'utf-8' );
			$p->add_field( 'business', $business );
			$p->add_field( 'return', $return_url );
			$p->add_field( 'cancel_return', $cancel_url );
			$p->add_field( 'notify_url', $notify_url );
			$p->add_field( 'custom', $order_id . '-' . $test_mode . '-' . $new_status );
			if ( $transaction_type == 0 ) {
				$p->add_field( 'cmd', '_xclick' );
				$p->add_field( 'item_name', __( 'Shopping cart ', 'tcp' ) . get_bloginfo( 'name' ) );
				$p->add_field( 'amount', number_format( $payment_amount, 2, '.', '' ) );
			} else {
				$p->add_field( 'cmd', '_cart' );
				$p->add_field( 'upload', '1' );
				$item = Orders::get( $order_id );
				$discount_amount = $item->discount_amount;
				$items = OrdersDetails::getDetails( $order_id );
				$i = 1;
				foreach( $items as $item ) {
					$p->add_field( 'item_name_' . $i, $item->name );
					$p->add_field( 'item_number_' . $i, $item->sku );
					$p->add_field( 'quantity_' . $i, $item->qty_ordered );
					$p->add_field( 'amount_' . $i, $item->price );
					if ( $item->tax > 0 ) {
						$tax = $item->price * $item->tax / 100;
						$p->add_field( 'tax_' . $i, number_format( $tax, 2, '.', '' ) );
					}
					if ( $discount_amount > 0 ) {
						$price = $item->qty_ordered * $item->price;
						if ( $price > $discount_amount) {
							$p->add_field( 'discount_amount_' . $i, $discount_amount );
							$discount_amount = 0;
						} else {
							$price -= 0.1;
							$p->add_field( 'discount_amount_' . $i, $price );
							$discount_amount -= $price;
						}
					}
					$i++;
				}*/
/*Use discount_amount_cart to charge a single discount amount for the entire cart.
Use discount_amount_x to set a discount amount associated with item x
Use discount_rate_cart to charge a single discount percentage for the entire cart.
discount_rate_cart - Applies to entire cart however, this variable will only work with the "Upload" Method. Not the standard Add to Cart variables.
This variable will be ignored if you are including any individual sales tax amount or rate in your upload method code. This is because the sales tax needs to be calculated after the discount is applied to your items therefore, the discount is applied to the item Subtotal, not the Total.
Note, If you just using the standard Add to Cart buttons, there no Discount variables for the entire cart. as they "only" apply a Discount to an individual item.*/
				/*$items = OrdersCosts::getCosts( $order_id );
				foreach( $items as $item ) {
					$p->add_field( 'item_name_' . $i, $item->description );
					$p->add_field( 'quantity_' . $i, 1 );
					$p->add_field( 'amount_' . $i, $item->cost );
					if ( $item->tax > 0 ) {
						$tax = $item->cost * $item->tax / 100;
						$p->add_field( 'tax_' . $i, number_format( $tax, 2, '.', '' ) );
					}
					$i++;
				}
			}
			$p->add_field( 'currency_code', $currency_code );
			require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/daos/Orders.class.php' );
			$order = Orders::get( $order_id );
			$p->add_field( 'first_name', $order->billing_firstname );//utf8_decode
			$p->add_field( 'last_name', $order->billing_lastname );
			$p->add_field( 'address1', $order->billing_street );
			$p->add_field( 'city', $order->billing_city );
			$p->add_field( 'state', $order->billing_region_id );
			$p->add_field( 'zip', $order->billing_postcode );
			$p->add_field( 'country', $order->billing_country_id );
			echo $p->submit_paypal_post();
		} else {
			echo '<p class="error">' . $error_msg . '</p>';
		}*/
	}

	function isSupportedCurrency( $currency_iso ) {
		$supported = array( 'AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD' );
		return in_array( $currency_iso, $supported );
	}
}
?>
