<?php
/**
 * This file is part of TheCartPress.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('paypal.class.php');
require_once('currencyconverter.class.php');

new TCPPaypalCurrencyConverter();

class TCPPayPal extends TCP_Plugin {

	function __construct() {
		parent::__construct();
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		add_action( 'wp_ajax_tcp_paypal_ipn', array( $this, 'tcp_paypal_ipn' ) );
		add_action( 'wp_ajax_nopriv_tcp_paypal_ipn', array( $this, 'tcp_paypal_ipn' ) );
	}

	function getTitle() {
		return 'PayPal Standard';
	}

	function getIcon() {
		return 'https://www.paypal.com/es_ES/ES/i/logo/paypal_logo.gif';
	}

	function getName() {
		return 'PayPal';
	}

	function getDescription() {
		return 'PayPal Standard payment method.<br>Author: <a href="http://thecartpress.com">TheCartPress team</a>';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( 'TCPPayPal', $instance );
		$title = isset( $data['title'] ) ? $data['title'] : $this->getTitle();
		return $title;
	}

	function showEditFields( $data ) { ?>
		<tr valign="top">
			<th scope="row">
				<label for="business"><?php _e( 'PayPal eMail', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="text" id="business" name="business" size="40" maxlength="50" value="<?php echo isset( $data['business'] ) ? $data['business'] : '';?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e( 'PayPal prompt for shipping address', 'tcp' );?>:
			</th>
			<td>
				<input type="radio" id="no_shipping_0" name="no_shipping" value="0" <?php checked( 0 , isset( $data['no_shipping'] ) ? $data['no_shipping'] : 0 );?> />
				<label for="no_shipping"><?php _e( 'PayPal prompt for an address, but do not require one', 'tcp' );?></label><br />
				<input type="radio" id="no_shipping_1" name="no_shipping" value="1" <?php checked( 1 , isset( $data['no_shipping'] ) ? $data['no_shipping'] : 0 );?> />
				<label for="no_shipping"><?php _e( 'PayPal do not prompt for an address', 'tcp' );?></label><br />
				<input type="radio" id="no_shipping_2" name="no_shipping" value="2" <?php checked( 2 , isset( $data['no_shipping'] ) ? $data['no_shipping'] : 0 );?> />
				<label for="no_shipping"><?php _e( 'PayPal prompt for an address, and require one', 'tcp' );?></label><br />
				<span class="description"><?php _e( 'Be sure to match this in the Checkout Editor', 'tcp' );?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Payment type', 'tcp' );?>:
			</th>
			<td>
				<?php $paymentaction = isset( $data['paymentaction'] ) ? $data['paymentaction'] : 'sale'; ?>
				<select name="paymentaction">
					<option value="sale" <?php selected( 'sale', $paymentaction ); ?>><?php _e( 'Sale', 'tcp' ); ?></option>
					<option value="authorization" <?php selected( 'authorization', $paymentaction ); ?>><?php _e( 'Authorization', 'tcp' ); ?></option>
					<option value="order" <?php selected( 'order', $paymentaction ); ?>><?php _e( 'Order', 'tcp' ); ?></option>
				</select>
				<span class="description"><?php _e( 'Indicates whether the payment is a final sale or an authorization for a final sale, to be captured later', 'tcp' );?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="redirect"><?php _e( 'Redirect automatically', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="checkbox" id="redirect" name="redirect" value="yes" <?php checked( isset( $data['redirect'] ) ? $data['redirect'] : false ); ?> />
				<p class="description"><?php _e( 'If checked, Checkout page will redirect automatically to the Paypal payment site. Otherwise, customers must click on "Pay with PayPal".', 'tcp' ); ?></p>
			</td>
		</tr>
<!--		<tr valign="top">
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
		</td></tr>-->
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td>&nbsp;</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="logging"><?php _e( 'Log IPN data', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="checkbox" id="logging" name="logging" value="yes" <?php checked( true , isset( $data['logging'] ) ? $data['logging'] : false );?> />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="cpp_cart_border_color"><?php _e( 'Cart border color', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="text" id="cart_border_color" name="cpp_cart_border_color" size="6" maxlength="8" value="<?php echo isset( $data['cpp_cart_border_color'] ) ? $data['cpp_cart_border_color'] : '';?>" />
				<span class="description"><?php _e( 'Optional, for customizing the PayPal page, and can be set from your PayPal account.<br /> Enter a 6 digit hex color code.', 'tcp' );?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="test_mode"><?php _e( 'PayPal sandbox test mode', 'tcp' );?>:</label>
			</th>
			<td>
				<input type="checkbox" id="test_mode" name="test_mode" value="yes" <?php checked( true , isset( $data['test_mode'] ) ? $data['test_mode'] : false );?> />
				<br/><a href="https://developer.paypal.com/?login_email=<?php echo isset( $data['business'] ) ? $data['business'] : '';?>" target="_blank">developer.paypal.com</a>
			</td>
		</tr>
		<?php do_action( 'tcp_paypal_show_edit_fields', $data ); ?>
		<?php
	}

	function saveEditFields( $data ) {
		$data['business']				= isset( $_REQUEST['business'] ) ? $_REQUEST['business'] : '';
		$data['profile_shipping']		= isset( $_REQUEST['profile_shipping'] );
		$data['profile_taxes']			= isset( $_REQUEST['profile_taxes'] );
		$data['no_shipping']			= isset( $_REQUEST['no_shipping'] ) ? $_REQUEST['no_shipping'] : 0;
		$data['paymentaction']			= isset( $_REQUEST['paymentaction'] ) ? $_REQUEST['paymentaction'] : 'sale';
		$data['redirect']				= isset( $_REQUEST['redirect'] );
		$data['send_detail']			= 0;//isset( $_REQUEST['send_detail'] ) ? $_REQUEST['send_detail'] : 0;
		$data['logging']				= isset( $_REQUEST['logging'] );
		$data['cpp_cart_border_color']	= isset( $_REQUEST['cpp_cart_border_color'] ) ? $_REQUEST['cpp_cart_border_color'] : '';
		$data['test_mode']				= isset( $_REQUEST['test_mode'] );
		$data =  apply_filters( 'tcp_paypal_save_edit_fields', $data );
		return $data;
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$business			= $data['business'];
		$test_mode			= $data['test_mode'];
		$profile_shipping	= $data['profile_shipping'];
		$profile_taxes		= $data['profile_taxes'];
		$paymentaction		= isset( $data['paymentaction'] ) ? $data['paymentaction'] : 'sale';
		$redirect			= isset( $data['redirect'] ) ? $data['redirect'] : false;
		$no_shipping		= isset( $data['no_shipping'] ) ? $data['no_shipping']: 0 ;
		$send_detail		= 0; //isset( $data['send_detail'] ) ? $data['send_detail']: 0 ;
		$logging			= $data['logging'];
		$merchant			= get_bloginfo( 'name' );
		$new_status			= $data['new_status'];
		$currency			= tcp_get_the_currency_iso();
		$currency			= apply_filters( 'tcp_paypal_get_convert_to', $currency, $data );
		$p = new tcp_paypal_class( $test_mode, $logging );
		$p->add_field( 'charset', 'utf-8' );
		$p->add_field( 'business', $business );
		$p->add_field( 'return', add_query_arg( 'tcp_checkout', 'ok', tcp_get_the_checkout_url() ) );
		//$p->add_field( 'cancel_return', add_query_arg( 'tcp_checkout', 'ko', plugins_url( 'thecartpress/plugins/PayPal/notify.php' ) ) );
		$p->add_field( 'cancel_return', add_query_arg( 'tcp_checkout', 'ko', tcp_get_the_checkout_url() ) );
		//$p->add_field( 'notify_url', plugins_url( 'thecartpress/plugins/PayPal/notify.php' ) );
		$p->add_field( 'notify_url', add_query_arg( 'action', 'tcp_paypal_ipn', admin_url( 'admin-ajax.php' ) ) );
		$p->add_field( 'custom', $order_id . '-' . $instance );
		//$p->add_field( 'custom', $order_id . '-' . $test_mode . '-' . $new_status . '-' . get_class( $this ) . '-' . $instance );
		$p->add_field( 'currency_code', $currency );
		$p->add_field( 'cbt', __( 'Return to ', 'tcp' ) . $merchant ); //text for the Return to Merchant button
		$p->add_field( 'no_shipping', $no_shipping );
		
		if ( $send_detail == 0 ) { // && empty( $profile_shipping ) && empty( $profile_taxes ) ) { // Buy Now - one total
			//$p->add_field( 'item_name', __( 'Purchase from ', 'tcp' ) . $merchant );
			$p->add_field( 'item_name', sprintf( __( 'Purchase from %s (Order No. %s)', 'tcp' ), $merchant, $order_id ) );
			$amount = 0;
			$taxes = 0;
			$decimals = tcp_get_decimal_currency();
			foreach( $shoppingCart->getItems() as $item ) {
				$tax = $item->getTax();//tcp_get_the_tax( $item->getPostId() );
				if ( ! tcp_is_display_prices_with_taxes() ) $discount = round( $item->getDiscount() / $item->getUnits(), $decimals );
				else $discount = 0;
				$unit_price_without_tax = tcp_get_the_price_without_tax( $item->getPostId(), $item->getUnitPrice() ) - $discount;
				$unit_price_without_tax = round( $unit_price_without_tax, $decimals );
				$tax_amount = $unit_price_without_tax * $tax / 100;
				$tax_amount = round( $tax_amount, $decimals );
				$line_tax_amount = $tax_amount * $item->getUnits();
				$line_price_without_tax = round( $unit_price_without_tax * $item->getUnits(), $decimals );
				$amount += $line_price_without_tax;
				$taxes += $line_tax_amount;
			}
			foreach( $shoppingCart->getOtherCosts() as $cost_id => $cost ) {
				if ( ShoppingCart::$OTHER_COST_SHIPPING_ID == $cost_id && $shoppingCart->isFreeShipping() ) break;
				$cost_without_tax = tcp_get_the_shipping_cost_without_tax( $cost->getCost() );
				$cost_without_tax = round( $cost_without_tax, $decimals );
				$tax = tcp_get_the_shipping_tax();
				$tax_amount = $cost_without_tax * $tax / 100;
				//$tax_amount = round( $tax_amount, $decimals );
				$amount += $cost_without_tax;
				$taxes += $tax_amount;
			}
			if ( tcp_is_display_prices_with_taxes() ) $amount -= $shoppingCart->getAllDiscounts();
			else $amount -= $shoppingCart->getCartDiscountsTotal();

			$amount = apply_filters( 'tcp_paypal_converted_amount', $amount, $data );
			$p->add_field( 'amount', number_format( $amount, 2 ) );

			$taxes = apply_filters( 'tcp_paypal_converted_amount', $taxes, $data );
			if ( $taxes > 0 ) $p->add_field( 'tax', number_format( $taxes, 2 ) );

			if ( tcp_is_display_prices_with_taxes() ) $discount = $shoppingCart->getAllDiscounts();
			else $discount = $shoppingCart->getCartDiscountsTotal();
			$discount = apply_filters( 'tcp_paypal_get_converted_amount', $discount, $data );
			if ( $discount > 0 ) $p->add_field( 'discount_amount_cart', number_format( $discount, 2, '.', '' ) );

		}/* else { //Item by item //TODO NOT IN USE
			$p->add_field( 'cmd', '_cart' );
			$p->add_field( 'upload', '1' );
			$discount = $shoppingCart->getAllDiscounts();
			$i = 1;
			foreach( $shoppingCart->getItems() as $item ) {
				$p->add_field( "item_name_$i", strip_tags( html_entity_decode( $item->getTitle(), ENT_QUOTES ) ) );
				$p->add_field( "amount_$i", number_format( $item->getUnitPrice(), 2, '.', '' ) );
				$p->add_field( "quantity_$i", $item->getUnits() );
				$tax = $item->getTax();//tcp_get_the_tax( $item->getPostId() );//$item->getTax()
				$tax = ( $item->getUnitPrice() * $tax / 100 ) * $item->getUnits();
				if ( $tax > 0 ) $p->add_field( "tax_$i", number_format( $tax, 2, '.', '' ) );
				if ( $discount > 0 ) {
					$price = $item->getUnits() * $item->getUnitPrice();
					if ( $price > $discount) {
						$p->add_field( "discount_amount_$i", $discount );
						$discount = 0;//TODO
					} else {
						$price -= 0.1;
						$p->add_field( "discount_amount_$i", $price );
						$discount -= $price;
					}
				}
				$i++;
			}
			$tax_percentage = tcp_get_the_shipping_tax();
			foreach( $shoppingCart->getOtherCosts() as $cost ) {
				$p->add_field( "item_name_$i", $cost->getDesc() );
				$p->add_field( "amount_$i", $cost->getCost() );
				$tax = $cost->getCost() * $tax_percentage / 100;
				if ( $tax > 0 ) $p->add_field( "tax_$i", number_format( $tax, 2, '.', '' ) );
				$i++;
			}
		}*/
		$p->add_field( 'paymentaction', $paymentaction );
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
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
		if ( $redirect ) : ?>
		<script type="text/javascript">
		//jQuery().ready( function() {
			jQuery( 'form[name=paypal_form]' ).submit();
		//} );
		</script>
		<p class="tcp_redirect"><?php _e( 'Redirecting to paypal, wait a moment', 'tcp'); ?></p>
		<?php endif;

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
	}

	/*function isSupportedCurrency( $currency_iso ) {
		$supported = array( 'AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD' );
		return in_array( $currency_iso, $supported );
	}*/

	function tcp_paypal_ipn() {
		$custom			= isset( $_POST['custom'] ) ? $_POST['custom'] : '321-0'; //-CANCELLED-TCPPayPal-0';//Order_id-test_mode-new_status-class-instance
		$transaction_id	= isset( $_POST['txn_id'] ) ? $_POST['txn_id'] : '';
		$custom		= explode( '-', $custom );
		$order_id	= $custom[0];
		$instance	= $custom[1];
		if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ko' ) {
			$cancelled_status = tcp_get_cancelled_order_status();
			Orders::editStatus( $order_id, $cancelled_status, $transaction_id, __( 'Customer cancel at PayPal', 'tcp' ) );
			ActiveCheckout::sendMails( $order_id, __( 'Customer cancel at PayPal', 'tcp' ) );
		} else {
			$data = tcp_get_payment_plugin_data( 'TCPPayPal', $instance );
			$test_mode	= $data['test_mode'];
			$new_status	= $data['new_status'];
			include( 'ipnlistener.class.php' );
			$listener = new IpnListener();
			$listener->use_sandbox = $test_mode;
			//To post over standard HTTP connection, use:
			//$listener->use_ssl = false;
			//To post using the fsockopen() function rather than cURL, use:
			//$listener->use_curl = false;
			try {
				$listener->requirePostMethod();
				$verified = $listener->processIpn();
			} catch ( Exception $e ) {
				$verified = false;
				Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, __( 'No validation. Error in connection.', 'tcp' ) );
				ActiveCheckout::sendMails( $order_id, __( 'No validation. Error in connection.', 'tcp' ) );
				exit(0);
			}
			if ( $verified ) {
		//  	Once you have a verified IPN you need to do a few more checks on the POST
		//		fields--typically against data you stored in your database during when the
		//		end user made a purchase (such as in the "success" page on a web payments
		//		standard button). The fields PayPal recommends checking are:
		//		1. Check the $_POST['payment_status'] is "Completed"
		//		2. Check that $_POST['txn_id'] has not been previously processed
		//		3. Check that $_POST['receiver_email'] is your Primary PayPal email
		//		4. Check that $_POST['payment_amount'] and $_POST['payment_currency']
		//		are correct
		//		Since implementations on this varies, I will leave these checks out of this
		//		example and just send an email using the getTextReport() method to get all
		//		of the details about the IPN.
				if ( $_POST['receiver_email'] == $data['business'] ) {
					$ok = false;
					//$order_row = Orders::getOrderByTransactionId( $classname, $transaction_id );
					$additional = "\npayment_status: " . $_POST['payment_status'];
					switch ( $_POST['payment_status'] ) {
						case 'Completed':
						case 'Canceled_Reversal':
						case 'Processed': //should check price, but with profile options, we can't know it, could check currency
							$comment = "\nmc_gross: " . $_POST['mc_gross'] . ' ' . $_POST['mc_currency'];
							$comment .= "\nmc_shipping: " . $_POST['mc_shipping'] . ', tax=' . $_POST['tax'];
							if ( isset( $_POST['receipt_id'] ) ) $additional .= "\nPayPal Receipt ID: " . $_POST['receipt_id'];
							if ( isset( $_POST['memo'] ) ) $additional .= "\nCustomer comment: " . $_POST['memo'];
							Orders::editStatus( $order_id, $new_status, $transaction_id, $comment . $additional );
							$ok = true;
							break;
						case 'Refunded':
						case 'Reversed':
							$additional .= "\nreason code: " . $_POST['reason_code'];
							Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, $additional );
							break;
						case 'Expired':
						case 'Failed':
							Orders::editStatus( $order_id, Orders::$ORDER_PROCESSING, $transaction_id, $additional );
							require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/checkout/ActiveCheckout.class.php' );
							break;
						case 'Pending':
							$additional .= "\npending_reason=" . $_POST['pending_reason'];
							Orders::editStatus( $order_id, Orders::$ORDER_PENDING, $transaction_id, $additional );
							break;
						case 'Expired':
						case 'Failed':
						case 'Denied':
						case 'Voided':
							Orders::editStatus( $order_id, $cancelled_status, $transaction_id, $additional );
							break;
						default :
							$additional .= "\nreason code: " . $_POST['reason_code'];
							Orders::editStatus( $order_id, Orders::$ORDER_PENDING, $transaction_id, $additional );
							break;
					}
					do_action( 'tcp_paypal_standard_do_payment', $order_id, array( 'TransactionID' => $transaction_id ), $ok );
					ActiveCheckout::sendMails( $order_id, $additional );
				} else {
					$additional = $_POST['payment_status']. ': receiver_email is wrong (' . $_POST['receiver_email'] . ')';
					Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, $additional );
					ActiveCheckout::sendMails( $order_id, $additional );
				}
			} else {
				Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, 'Invalid IPN' );
				ActiveCheckout::sendMails( $order_id, 'Invalid IPN' );
				//An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
				//a good idea to have a developer or sys admin manually investigate any
				//invalid IPN.
				//save for further investigation?
				//mail( debug_email, 'Invalid IPN', $listener->getTextReport() );
			}
		}
	}
}
?>