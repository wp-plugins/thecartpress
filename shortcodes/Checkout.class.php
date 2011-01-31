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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Addresses.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Countries.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersDetails.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/classes/OrderPage.class.php' );

class Checkout {
	function show() {
		$settings = get_option( 'tcp_settings' );
		$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';
		$error_billing = array();
		$error_shipping = array();
		$has_validation_billing_error = false;
		$has_validation_shipping_error = false;
		$has_validation_error = false;
		$shipping_country = $this->getShippingCountry();//shipping country's iso.
		global $current_user;
		get_currentuserinfo();
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( isset( $_REQUEST['tcp_load_plugins'] ) || isset( $_REQUEST['tcp_show_cart'] ) || isset( $_REQUEST['tcp_create_order'] ) ) {
			$selected_billing_address = isset( $_REQUEST['selected_billing_address'] ) ? $_REQUEST['selected_billing_address'] : 'N';
			$selected_shipping_address = isset( $_REQUEST['selected_shipping_address'] ) ? $_REQUEST['selected_shipping_address'] : 'N';
			$use_billing_address = isset( $_REQUEST['use_billing_address'] ) ? $_REQUEST['use_billing_address'] : 'N';
			if ( $selected_billing_address == 'new' ) {
				if ( ! isset( $_REQUEST['billing_firstname'] ) || strlen( $_REQUEST['billing_firstname'] ) == 0 )
					$error_billing['billing_firstname'][] = __( 'The billing firstname field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['billing_lastname'] ) || strlen( $_REQUEST['billing_lastname'] ) == 0 )
					$error_billing['billing_lastname'][] = __( 'The billing lastname field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['billing_street'] ) || strlen( $_REQUEST['billing_street'] ) == 0 )
					$error_billing['billing_street'][] = __( 'The billing street field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['billing_city'] ) || strlen( $_REQUEST['billing_city'] ) == 0)
					$error_billing['billing_city'][] = __( 'The billing city field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['billing_region'] ) || strlen( $_REQUEST['billing_region'] ) == 0 )
					$error_billing['billing_region'][] = __( 'The billing region field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['billing_postcode'] ) || strlen( $_REQUEST['billing_postcode'] ) == 0 )
					$error_billing['billing_postcode'][] = __( 'The billing postcode field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['billing_email'] ) || strlen( $_REQUEST['billing_email'] ) == 0 )
					$error_billing['billing_email'][] = __( 'The billing email field must be completed', 'tcp' );
				$has_validation_billing_error = count( $error_billing ) > 0;
			}
			if ($selected_shipping_address == 'new') {
				if ( ! isset( $_REQUEST['shipping_firstname'] ) || strlen( $_REQUEST['shipping_firstname'] ) == 0 )
					$error_shipping['shipping_firstname'][] = __( 'The shipping firstname field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['shipping_lastname'] ) || strlen( $_REQUEST['shipping_lastname'] ) == 0 )
					$error_shipping['shipping_lastname'][] = __( 'The shipping lastname field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['shipping_street'] ) || strlen( $_REQUEST['shipping_street'] ) == 0 )
					$error_shipping['shipping_street'][] = __( 'The shipping street field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['shipping_city'] ) || strlen( $_REQUEST['shipping_city'] ) == 0 )
					$error_shipping['shipping_city'][] = __( 'The shipping city field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['shipping_region'] ) || strlen( $_REQUEST['shipping_region'] ) == 0)
					$error_shipping['shipping_region'][] = __( 'The shipping region field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['shipping_postcode'] ) || strlen( $_REQUEST['shipping_postcode'] ) == 0 )
					$error_shipping['shipping_postcode'][] = __( 'The shipping postcode field must be completed', 'tcp' );
				if ( ! isset( $_REQUEST['shipping_email'] ) || strlen( $_REQUEST['shipping_email'] ) == 0 )
					$error_shipping['shipping_email'][] = __( 'The shipping email field must be completed', 'tcp' );
				$has_validation_shipping_error = count( $error_shipping ) > 0;
			}
			$has_validation_error = $has_validation_billing_error || $has_validation_shipping_error;
			$legal_notice_accept = isset( $_REQUEST['legal_notice_accept'] ) ? $_REQUEST['legal_notice_accept'] : '';
			if ( !$has_validation_error && isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept == 'Y' ) {
				$selected_billing_address = isset( $_REQUEST['selected_billing_address'] ) ? $_REQUEST['selected_billing_address'] : 'N';
				$selected_shipping_address = isset( $_REQUEST['selected_shipping_address'] ) ? $_REQUEST['selected_shipping_address'] : 'N';
				do_action( 'tcp_checkout_start' );
				$order = array();
				//$order = array_merge( $_REQUEST, $order );
				$order['created_at'] = date( 'Y-m-d H:i:s' );
				$order['status'] = Orders::$ORDER_PENDING;
				$order['comment'] = isset( $_REQUEST['comment'] ) ? $_REQUEST['comment'] : '';
				$order['order_currency_code'] = $currency;
				if ( $selected_billing_address == 'Y' ) {
					$address = Addresses::get( $_REQUEST['selected_billing_id'] );
					$order['billing_firstname']		= $address->firstname;
					$order['billing_lastname']		= $address->lastname;
					$order['billing_company']		= $address->company;
					$order['billing_street']		= $address->street;
					$order['billing_city']			= $address->city;
					$order['billing_city_id']		= $address->city_id;
					$order['billing_region']		= $address->region;
					$order['billing_region_id']		= $address->region_id;
					$order['billing_postcode']		= $address->postcode;
					$order['billing_country']		= ''; //$address->country;
					$order['billing_country_id']	= $address->country_id;
					$order['billing_telephone_1']	= $address->telephone_1;
					$order['billing_telephone_2']	= $address->telephone_2;
					$order['billing_fax']			= $address->fax;
					$order['billing_email']			= $address->email;
					$create_billing_address = false;
				} else {
					$order['billing_firstname']		= $_REQUEST['billing_firstname'];
					$order['billing_lastname']		= $_REQUEST['billing_lastname'];
					$order['billing_company']		= $_REQUEST['billing_company'];
					$order['billing_street']		= $_REQUEST['billing_street'];
					$order['billing_city']			= $_REQUEST['billing_city'];
					$order['billing_city_id']		= 0; //$_REQUEST['billing_city_id'];
					$order['billing_region']		= $_REQUEST['billing_region'];
					$order['billing_region_id']		= 0; //$_REQUEST['billing_region_id'];
					$order['billing_postcode']		= $_REQUEST['billing_postcode'];
					$order['billing_country']		= '';//$_REQUEST['billing_country'];
					$order['billing_country_id']	= $_REQUEST['billing_country_id'];
					$order['billing_telephone_1']	= $_REQUEST['billing_telephone_1'];
					$order['billing_telephone_2']	= $_REQUEST['billing_telephone_2'];
					$order['billing_fax']			= $_REQUEST['billing_fax'];
					$order['billing_email']			= $_REQUEST['billing_email'];
					$create_billing_address = true;
				}
				if ( $selected_shipping_address == 'Y' ) {
					$address = Addresses::get( $_REQUEST['selected_shipping_id'] );
					$order['shipping_firstname']	= $address->firstname;
					$order['shipping_lastname']		= $address->lastname;
					$order['shipping_company']		= $address->company;
					$order['shipping_street']		= $address->street;
					$order['shipping_city']			= $address->city;
					$order['shipping_city_id']		= $address->city_id;
					$order['shipping_region']		= $address->region;
					$order['shipping_region_id']	= $address->region_id;
					$order['shipping_postcode']		= $address->postcode;
					$order['shipping_country']		= ''; //$address->country;
					$order['shipping_country_id']	= $address->country_id;
					$order['shipping_telephone_1']	= $address->telephone_1;
					$order['shipping_telephone_2']	= $address->telephone_2;
					$order['shipping_fax']			= $address->fax;
					$order['shipping_email']		= $address->email;
					$create_shipping_address = false;
				} elseif ( $selected_shipping_address == 'BIL' ) {
					$order['shipping_firstname']	= $order['billing_firstname'];
					$order['shipping_lastname']		= $order['billing_lastname'];
					$order['shipping_company']		= $order['billing_company'];
					$order['shipping_street']		= $order['billing_street'];
					$order['shipping_city']			= $order['billing_city'];
					$order['shipping_city_id']		= $order['billing_city_id'];
					$order['shipping_region']		= $order['billing_region'];
					$order['shipping_region_id']	= $order['billing_region_id'];
					$order['shipping_postcode']		= $order['billing_postcode'];
					$order['shipping_country']		= ''; //$order['billing_country'];
					$order['shipping_country_id']	= $order['billing_country_id'];
					$order['shipping_telephone_1']	= $order['billing_telephone_1'];
					$order['shipping_telephone_2']	= $order['billing_telephone_2'];
					$order['shipping_fax']			= $order['billing_fax'];
					$order['shipping_email']		= $order['billing_email'];
					$create_shipping_address = false;
				} else {
					$order['shipping_firstname']	= $_REQUEST['shipping_firstname'];
					$order['shipping_lastname']		= $_REQUEST['shipping_lastname'];
					$order['shipping_company']		= $_REQUEST['shipping_company'];
					$order['shipping_street']		= $_REQUEST['shipping_street'];
					$order['shipping_city']			= $_REQUEST['shipping_city'];
					$order['shipping_city_id']		= 0; //$_REQUEST['shipping_city_id'];
					$order['shipping_region']		= $_REQUEST['shipping_region'];
					$order['shipping_region_id']	= 0; //$_REQUEST['shipping_region_id'];
					$order['shipping_postcode']		= $_REQUEST['shipping_postcode'];
					$order['shipping_country']		= ''; //$_REQUEST['shipping_country'];
					$order['shipping_country_id']	= $_REQUEST['shipping_country_id'];
					$order['shipping_telephone_1']	= $_REQUEST['shipping_telephone_1'];
					$order['shipping_telephone_2']	= $_REQUEST['shipping_telephone_2'];
					$order['shipping_fax']			= $_REQUEST['shipping_fax'];
					$order['shipping_email']		= $_REQUEST['shipping_email'];
					$create_shipping_address = true;
				}
				if ( is_user_logged_in() )
					$order['customer_id'] = $current_user->ID;
				else
					$order['customer_id'] = 0;
				if ( isset( $_REQUEST['shipping_method_id'] ) ) { //sending
					$smi = $_REQUEST['shipping_method_id'];
					$smi = explode( '#', $smi );
					$class = $smi[0];
					$instance = $smi[1];
					$shipping_method = new $class();
					$shipping_amount = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
					$order['shipping_amount'] = $shipping_amount;
					$order['shipping_method'] = $class;
				} else {
					$order['shipping_amount'] = 0;
					$order['shipping_method'] = '';
				}
				if ( isset( $_REQUEST['payment_method_id'] ) ) {
					$pmi = $_REQUEST['payment_method_id'];
					$pmi = explode ('#', $pmi );
					$class = $pmi[0];
					$instance = $pmi[1];
					$payment_method = new $class();
					$payment_amount = $payment_method->getCost( $instance, $shipping_country, $shoppingCart );
					$order['payment_amount'] = $payment_amount;
					$order['payment_method'] = $class;
					$order['payment_name']   = '';
				} else {
					$order['payment_amount'] = 0;
					$order['payment_method'] = '';
					$order['payment_name']   = '';
				}
				$order['discount_amount'] = 0;
				$order['weight'] = $shoppingCart->getWeight();
				$order['comment_internal'] = '';
				$order['code_tracking'] = '';
				//TODO more values???
				if ( isset( $order['billing_country'] ) && strlen( $order['billing_country'] ) == 0 ) {
					$country = Countries::get( $order['billing_country_id'] );
					$order['billing_country'] = $country->name;
				}
				if ( $order['shipping_country_id'] == $order['billing_country_id'] )
					$order['shipping_country'] = $order['billing_country'];
				elseif ( isset( $order['shipping_country'] ) && strlen( $order['shipping_country'] ) == 0 ) {
					$country = Countries::get( $order['shipping_country_id'] );
					$order['shipping_country'] = $country->name;
				}
				$order_id = Orders::insert( $order );
				$no_stock_enough = false;
				foreach( $shoppingCart->getItems() as $item ) {
					$post = get_post( $item->getPostId() );
					$sku = tcp_get_the_sku();
					$days_to_expire = (int)get_post_meta( $post->ID, 'tcp_days_to_expire', true );
					if ( $days_to_expire > 0 ) {
						$today = date( 'Y-m-d' );
						$expires_at = date ( 'Y-m-d', strtotime( date( 'Y-m-d', strtotime( $today ) ) . " +$days_to_expire day" ) );
					} elseif ( $days_to_expire == 0 ) {
						$expires_at = date( 'Y-m-d' );
					} else {
						$expires_at = date( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
					}
					$ordersDetails = array();
					$ordersDetails['order_id']			= $order_id;
					$ordersDetails['post_id']			= $item->getPostId();
					$ordersDetails['option_1_id']		= $item->getOption1Id();
					$ordersDetails['option_2_id']		= $item->getOption2Id();
					$ordersDetails['weight']			= $item->getWeight();
					$ordersDetails['is_downloadable']	= $item->isDownloadable() ? 'Y' : '';
					$ordersDetails['sku']				= $sku;
					$ordersDetails['name']				= $post->post_title;
					$ordersDetails['option_1_name']		= $item->getOption1Id() > 0 ? get_the_title( $item->getOption1Id() ) : '';
					$ordersDetails['option_2_name']		= $item->getOption2Id() > 0 ? get_the_title( $item->getOption2Id() ) : '';
					$ordersDetails['price']				= $item->getUnitPrice();
					$ordersDetails['original_price']	= $item->getUnitPrice();//TODO cupons???
					$ordersDetails['tax']				= $item->getTax();
					$ordersDetails['qty_ordered']		= $item->getCount();
					$ordersDetails['max_downloads']		= (int)get_post_meta( $post->ID, 'tcp_max_downloads', true );
					$ordersDetails['expires_at']		= $expires_at;
					$stock = tcp_get_the_stock( $item->getPostId() );
					if ( $stock == -1 || $stock - $item->getCount() >= 0 ) {
						tcp_set_the_stock( $item->getPostId(), $stock - $item->getCount() );
						OrdersDetails::insert( $ordersDetails );
					} else
						$no_stock_enough = true;
				}
				if ( $create_shipping_address )
					$this->createNewShippingAddress( $order );
				if ( $create_billing_address )
					$this->createNewBillingAddress( $order );
				if ( $order['customer_id'] > 0 ) {//for downloadable products the customer must be registered
					//$virtualProductsDAO = new VirtualProductsDAO();
					//$virtualProductsDAO->createVirtualProducts($productsCart, $order->customer_id, $order_id);
				}
//
// Payment Area
//
				do_action( 'tcp_checkout_ok', $order_id );
				echo '<div class="tcp_payment_area">' . "\n";
				if ( $no_stock_enough ) echo '<p>', __( 'There was an error when creating the order. Please contact the seller.', 'tcp' ), '</p>';
				$order_page = OrderPage::show( $order_id, true, false );
				$_SESSION['order_page'] = $order_page;
				echo $order_page;
				echo '<p>' . __( 'The next step helps you to pay using the payment method choosen by you.', 'tcp' ) . '</p>';
				if ( isset( $_REQUEST['payment_method_id'] ) ) {
					$pmi = $_REQUEST['payment_method_id'];
					$pmi = explode( '#', $pmi );
					$class = $pmi[0];
					$instance = $pmi[1];
					$payment_method = new $class();
					$payment_method->showPayForm( $instance, $shipping_country, $shoppingCart, $currency, $order_id );
				}
				//if ( isset( $settings['emails'] ) ) {
				$mails = isset( $settings['emails'] ) ? explode( ',', $settings['emails'] ) : array();
				if ( strlen( $order['shipping_email'] ) > 0 ) $mails[] = $order['shipping_email'];
				if ( strlen( $order['billing_email'] ) > 0 && $order['shipping_email'] != $order['billing_email'] ) $mails[] = $order['billing_email'];
				$to = implode( ',', $mails );
				$from = isset( $settings['from_email'] ) && strlen( $settings['from_email'] ) > 0 ? $settings['from_email'] : 'no-response@thecartpress.com';
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'To: '.$to."\r\n";
				$headers .= 'From: '.$from."\r\n";
				//$headers .= 'Cc: '.$cc."\r\n";
				//$headers .= 'Bcc: '.$bcc."\r\n";
				$subject = 'Order from '.get_bloginfo( 'name' );
				$message = $_SESSION['order_page'];
				wp_mail( $to, $subject, $message, $headers );
				echo '<br />';
				echo '<a href="' . plugins_url( 'thecartpress/admin/PrintOrder.php' ) . '" target="_blank">' . __( 'Print', 'tcp' ) . '</a>';
				echo '</div>' . "\n";
				$shoppingCart->deleteAll();
				return;
			}
		}
		if ( $shoppingCart->isEmpty() ) :?>
<span class="tcp_shopping_cart_empty"><?php _e( 'The cart is empty', 'tcp' );?></span>
		<?php elseif ( ! $shoppingCart->isThereStock() ) :
			require_once( dirname( __FILE__ ) . '/ShoppingCartPage.class.php' );
			$shoppingCartPage = new ShoppingCartPage();
			$shoppingCartPage->show( __( 'You are trying to check out your order but, at this moment, there are not enough stock of some products. Please review the list of products.', 'tcp' ) );
		else :?>
<div id="checkout" class="checkout">
<!--//
//1. Identify layer
//-->
	<div class="identify_layer" id="identify_layer">
		<h3><?php _e( '1. Checkout method', 'tcp' );?><?php if ( is_user_logged_in() ) :
		 	?><span>:&nbsp;<?php echo $current_user->user_nicename;?></span><?php
		endif;?></h3>
		<div class="identify_layer_info checkout_info clearfix" id="identify_layer_info" <?php
		if ( is_user_logged_in() || isset( $_REQUEST['tcp_load_plugins'] ) || isset( $_REQUEST['tcp_show_cart'] ) || isset( $_REQUEST['tcp_create_order'] ) ) :
			?> style="display: none"<?php
		endif;?>>
		<?php if ( ! is_user_logged_in() ) :?>
			<div id="login_form">
				<h4><?php _e( 'Login', 'tcp' );?></h4>
				<?php 
				$user_registration = isset( $settings['user_registration'] ) ? $settings['user_registration'] : false;
				if ( ! $user_registration ) :?>
				<p>
					<strong><?php _e( 'Already registered?', 'tcp' );?></strong>
					<br><?php _e( 'Please log in below:', 'tcp' );?>
				</p>
				<?php endif;
				$args = array(
					'echo'				=> true,
					'redirect'			=> get_permalink(), // get_option( 'tcp_checkout_page_id' ) ),
					'form_id'			=> 'loginform',
					'label_username'	=> __( 'Username', 'tcp' ),
					'label_password'	=> __( 'Password', 'tcp' ),
					'label_remember'	=> __( 'Remember Me', 'tcp' ),
					'label_log_in'		=> __( 'Log In', 'tcp' ),
					'id_username'		=> 'user_login',
					'id_password'		=> 'user_pass',
					'id_remember'		=> 'rememberme',
					'id_submit'			=> 'wp-submit',
					'remember'			=> true,
					'value_username'	=> '',
					'value_remember'	=> false
				);
				wp_login_form( $args );?>
			</div><!--login_form-->
			<div id="login_guess">
			<?php if ( get_option( 'users_can_register' ) ) :?>
				<?php if ( ! $user_registration ) :?>
					<h4><?php _e( 'Checkout as registered', 'tcp' );?></h4>
				<?php endif;?>
				<p><strong><?php _e( 'Register with us for future convenience:', 'tcp' )?></strong></p>
				<ul class="disc">
					<li><?php _e( 'Fast and easy chechout', 'tcp' );?></li>
					<li><?php _e( 'Easy access to yours orders history and status', 'tcp' );?></li>
					<?php wp_register( '<li>', '</li>', true );?>
				</ul>
			<?php endif;?>
			<?php do_action( 'tcp_checkout_identify' );?>
			<?php if ( ! $user_registration ) :?>
				<h4><?php _e( 'Checkout as a guest', 'tcp' );?></h4>
				<p><strong><?php _e( 'Or you can make as a guest.', 'tcp' );?></strong></p>
				<ul>
					<li><?php _e( 'If you prefer this way then press the next button', 'tcp' );?>
				</ul>
				<p><input type="button" value="<?php _e( 'Continue', 'tcp' );?>" onClick="jQuery('.billing_layer_info').show();jQuery('.identify_layer_info').hide();" class="tcp_continue tcp_continue_identity" /></p>
			<?php else :?>
				 <p><strong><?php _e( 'User registration is required. Please, log in or register. ', 'tcp' );?></strong></p>
			<?php endif;?>
			</div><!-- login_guess -->
		<?php endif;?>
		</div> <!-- identify_layer_info -->
	</div> <!-- identify_layer -->
<!--//
//2. Billing layer
//-->
	<form method="post">
	<div id="billing_layer" class="billing_layer">
		<h3><?php _e( '2. Billing options', 'tcp' );?></h3>
		<div class="billing_layer_info checkout_info clearfix" id="billing_layer_info"<?php
			if ( ( isset( $_REQUEST['tcp_load_plugins'] ) || isset( $_REQUEST['tcp_show_cart'] ) || isset( $_REQUEST['tcp_create_order'] ) ) && ! $has_validation_billing_error)
				echo ' style="display: none"';
			elseif ( ! is_user_logged_in() && ! $has_validation_billing_error) 
				echo ' style="display: none"';?>>
			<?php
			$addresses = Addresses::getCustomerAddresses( $current_user->ID );
			if ( count( $addresses ) > 0 ):
				$default_address_id = Addresses::getCustomerDefaultBillingAddresses( $current_user->ID );?>
				<div id="selected_billing_area">
					<label for="selected_billing_id"> <?php _e( 'Select billing address:', 'tcp' );?></label>
					<br />
					<select id="selected_billing_id" name="selected_billing_id">
					<?foreach( $addresses as $address ) :?>
						<option value="<?php echo $address->address_id;?>" <?php selected( $address->address_id, $default_address_id );?>><?php echo $address->name;?></option>
					<?php endforeach;?>
					</select>
				</div> <!-- selected_billing_area -->
				<input type="radio" id="selected_billing_address" name="selected_billing_address" value="Y"<?php
					if ( ( ! isset( $_REQUEST['selected_billing_address'] ) && count( $addresses ) > 0 ) || ( isset( $_REQUEST['selected_billing_address'] ) && $_REQUEST['selected_billing_address'] == 'Y' ) ) :
						?> checked="true"<?php
					endif;
					?> onChange="jQuery('#selected_billing_area').show();jQuery('#new_billing_area').hide();" />
				<label for="selected_billing_address"><?php _e( 'Send to the selected address', 'tcp' )?></label>
				<br />
			<?php endif;?>
			<input type="radio" id="new_billing_address" name="selected_billing_address" value="new" <?php
				if ( ( ! isset( $_REQUEST['selected_billing_address'] ) && count( $addresses ) == 0 ) || ( isset( $_REQUEST['selected_billing_address'] ) && $_REQUEST['selected_billing_address'] == 'new' ) ) :
					?>checked="true"<?php
				endif;
				?> onChange="jQuery('#new_billing_area').show();jQuery('#selected_billing_area').hide();" />
			<label for="new_billing_address"><?php _e( 'New billing address', 'tcp' );?></label>
			<div id="new_billing_area" <?php
				if ( isset( $_REQUEST['selected_billing_address'] ) && $_REQUEST['selected_billing_address'] == 'new' ) :
				?><?php elseif ( count( $addresses ) > 0 ) :
					?>style="display:none"<?php
				endif;?>>
				<ul>
					<li><label for="billing_firstname"><?php _e( 'Firstname', 'tcp' );?>:</label>
					<input type="text" id="billing_firstname" name="billing_firstname" value="<?php $this->getValue( 'billing_firstname' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_billing, 'billing_firstname' );?></li>
					<li><label for="billing_lastname"><?php _e( 'Lastname', 'tcp' );?>:</label>
					<input type="text" id="billing_lastname" name="billing_lastname" value="<?php $this->getValue( 'billing_lastname' );?>" size="40" maxlength="100" />
					<?php $this->showErrorMsg( $error_billing, 'billing_lastname' );?></li>
					<li><label for="billing_company"><?php _e( 'Company', 'tcp' );?>:</label>
					<input type="text" id="billing_company" name="billing_company" value="<?php $this->getValue( 'billing_company' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_billing, 'billing_company' );?></li>
					<li><label for="billing_street"><?php _e( 'Address', 'tcp' );?>:</label>
					<input type="text" id="billing_street" name="billing_street" value="<?php $this->getValue( 'billing_street' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_billing, 'billing_street' );?></li>
					<li><label for="billing_city"><?php _e( 'City', 'tcp' );?>:</label>
					<input type="text" id="billing_city" name="billing_city" value="<?php $this->getValue( 'billing_city' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_billing, 'billing_city' );?></li>
					<li><label for="billing_region"><?php _e( 'Region', 'tcp' );?>:</label>
					<input type="text" id="billing_region" name="billing_region" value="<?php $this->getValue( 'billing_region' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_billing, 'billing_region' );?></li>
					<li><label for="billing_postcode"><?php _e( 'Postal code', 'tcp' );?>:</label>
					<input type="text" id="billing_postcode" name="billing_postcode" value="<?php $this->getValue( 'billing_postcode' );?>" size="5" maxlength="5" />
					<?php $this->showErrorMsg( $error_billing, 'billing_postcode' );?></li>
					<li><label for="billing_country_id"><?php _e( 'Country', 'tcp' );?>:</label>
					<select id="billing_country_id" name="billing_country_id">
					<?php $countries = Countries::getAll();
					foreach($countries as $country) :?>
						<option value="<?php echo $country->iso;?>" <?php selected( $country->iso, $this->getValue( 'billing_country_id', false ) )?>><?php echo $country->name;?></option>
					<?php endforeach;?>
					</select>
					<?php $this->showErrorMsg( $error_billing, 'billing_country_id' );?></li>
					<li><label for="billing_telephone_1"><?php _e( 'Telephone 1', 'tcp' );?>:</label>
					<input type="text" id="billing_telephone_1" name="billing_telephone_1" value="<?php $this->getValue( 'billing_telephone_1' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_billing, 'billing_telephone_1' );?></li>
					<li><label for="billing_telephone_2"><?php _e( 'Telephone 2', 'tcp' );?>:</label>
					<input type="text" id="billing_telephone_2" name="billing_telephone_2" value="<?php $this->getValue( 'billing_telephone_2' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_billing, 'billing_telephone_2' );?></li>
					<li><label for="billing_fax"><?php _e( 'Fax', 'tcp' );?>:</label>
					<input type="text" id="billing_fax" name="billing_fax" value="<?php $this->getValue( 'billing_fax' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_billing, 'billing_fax' );?></li>
					<li><label for="billing_email"><?php _e( 'eMail', 'tcp' );?>:</label>
					<input type="text" id="billing_email" name="billing_email" value="<?php $this->getValue( 'billing_email' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_billing, 'billing_email' );?></li>
				</ul>
			</div> <!-- new_billing_area -->
			<?php do_action( 'tcp_checkout_billing' );?>
			<p>
			<?php if ( ! is_user_logged_in() ) :?>
				<input type="button" value="<?php _e( 'Back', 'tcp' );?>" class="tcp_back tcp_back_billing"
					onclick="jQuery('.billing_layer_info').hide();jQuery('.identify_layer_info').show();"/>
			<?php endif;?>
				<input type="button" value="<?php _e( 'Continue', 'tcp' );?>" class="tcp_continue tcp_continue_billing"
					onclick="jQuery('.billing_layer_info').hide();jQuery('.shipping_layer_info').show();"/>
			</p>
		</div> <!-- billing_layer_info -->
	</div> <!-- billing_layer -->
<!--//
//3. Shipping layer
//-->
	<div id="shipping_layer" class="shipping_layer">
		<h3><?php _e( '3. Shipping options', 'tcp' );?></h3>
		<div class="shipping_layer_info checkout_info clearfix" id="shipping_layer_info"<?php
		if ( isset( $_REQUEST['tcp_load_plugins'] ) &&  $has_validation_shipping_error ){}
		else echo 'style=" display: none;"';?>>
		<?php if ( count( $addresses ) > 0 ):
			$default_address_id = Addresses::getCustomerDefaultShippingAddresses( $current_user->ID );?>
			<div id="selected_shipping_area">
				<label for="selected_shipping_id"> <?php _e( 'Select shipping address:', 'tcp' );?></label>
				<br />
				<select id="selected_shipping_id" name="selected_shipping_id">
				<?php if ( ! $addresses ) $addresses = Addresses::getCustomerAddresses( $current_user->ID );
				foreach( $addresses as $address) :?>
					<option value="<?php echo $address->address_id;?>" <?php selected( $address->address_id, $default_address_id );?>><?php echo $address->name;?></option>
				<?php endforeach;?>
				</select>
			</div> <!-- selected_billing_area -->
			<input type="radio" id="selected_shipping_address" name="selected_shipping_address" value="Y"<?php
			if ( ( ! isset( $_REQUEST['selected_shipping_address'] ) && count( $addresses ) > 0 ) || $_REQUEST['selected_shipping_address'] == 'Y' ) : 
				?> checked="true"<?php
			endif;
			?> onChange="jQuery('#selected_shipping_area').show();jQuery('#new_shipping_area').hide();" />
			<label for="selected_shipping_address"><?php _e( 'Send to the selected address', 'tcp' )?></label>
			<br />
		<?php endif;?>
			<input type="radio" id="use_billing_address" name="selected_shipping_address" value="BIL" <?php
			if ( ( ! isset( $_REQUEST['selected_shipping_address'] ) && count( $addresses ) == 0 ) || ( isset( $_REQUEST['selected_shipping_address'] ) && $_REQUEST['selected_shipping_address'] == 'BIL' ) ) :
				?> checked="true"<?php
			endif;
			?> onChange="jQuery('#selected_shipping_area').hide();jQuery('#new_shipping_area').hide();" />
			<label for="use_billing_address"><?php _e( 'Use billing address', 'tcp' );?></label>
			<br />
			<input type="radio" id="new_shipping_address" name="selected_shipping_address" value="new"<?php
			if ( ( ! isset( $_REQUEST['selected_shipping_address'] ) && count( $addresses ) == 0 ) || ( isset( $_REQUEST['selected_shipping_address'] ) && $_REQUEST['selected_shipping_address'] == 'new' ) ) :
				?> checked="true"<?php
			endif;
			?> onChange="jQuery('#selected_shipping_area').hide();jQuery('#new_shipping_area').show();" />
			<label for="new_shipping_address"><?php _e( 'New shipping address', 'tcp' );?></label>
			<div id="new_shipping_area" <?php
				if ( ( ! isset( $_REQUEST['selected_shipping_address'] ) && count( $addresses ) == 0 ) || ( isset( $_REQUEST['selected_shipping_address'] ) && $_REQUEST['selected_shipping_address'] == 'new' ) ) :
					?><?php else : ?> style="display:none"<?php
				endif;?>>	
				<ul>
					<li><label for="shipping_firstname"><?php _e( 'Firstname', 'tcp' );?>:</label>
					<input type="text" id="shipping_firstname" name="shipping_firstname" value="<?php $this->getValue( 'shipping_firstname' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_firstname' );?></li>
					<li><label for="shipping_lastname"><?php _e( 'Lastname', 'tcp' );?>:</label>
					<input type="text" id="shipping_lastname" name="shipping_lastname" value="<?php $this->getValue( 'shipping_lastname' );?>"size="40" maxlength="100" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_lastname' );?></li>
					<li><label for="shipping_company"><?php _e( 'Company', 'tcp' );?>:</label>
					<input type="text" id="shipping_company" name="shipping_company" value="<?php $this->getValue( 'shipping_company' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_company' );?></li>
					<li><label for="shipping_street"><?php _e( 'Address', 'tcp' );?>:</label>
					<input type="text" id="shipping_street" name="shipping_street" value="<?php $this->getValue( 'shipping_street' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_street' );?></li>
					<li><label for="shipping_city"><?php _e( 'City', 'tcp' );?>:</label>
					<input type="text" id="shipping_city" name="shipping_city" value="<?php $this->getValue( 'shipping_city' );?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_city' );?></li>
					<li><label for="shipping_region"><?php _e( 'Region', 'tcp' );?>:</label>
					<input type="text" id="shipping_region" name="shipping_region" value="<?php $this->getValue( 'shipping_region' );?>"size="20" maxlength="50" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_region' );?></li>
					<li><label for="shipping_postcode"><?php _e( 'Postal code', 'tcp' );?>:</label>
					<input type="text" id="shipping_postcode" name="shipping_postcode" value="<?php $this->getValue( 'shipping_postcode' );?>"size="5" maxlength="5" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_postcode' );?></li>
					<li><label for="shipping_country_id"><?php _e( 'Country', 'tcp' );?>:</label>
					<select id="shipping_country_id" name="shipping_country_id">
					<?php $countries = Countries::getAll();
					foreach($countries as $country) :?>
						<option value="<?php echo $country->iso;?>" <?php selected( $country->iso, $this->getValue( 'shipping_country_id', false ) );?>><?php echo $country->name;?></option>
					<?php endforeach;?>
					</select>
					<?php $this->showErrorMsg( $error_shipping, 'shipping_country_id' );?></li>
					<li><label for="shipping_telephone_1"><?php _e( 'Telephone 1', 'tcp' );?>:</label>
					<input type="text" id="shipping_telephone_1" name="shipping_telephone_1" value="<?php $this->getValue( 'shipping_telephone_1' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_telephone_1' );?></li>
					<li><label for="shipping_telephone_2"><?php _e( 'Telephone 2', 'tcp' );?>:</label>
					<input type="text" id="shipping_telephone_2" name="shipping_telephone_2" value="<?php $this->getValue( 'shipping_telephone_2' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_telephone_2' );?></li>
					<li><label for="shipping_fax"><?php _e( 'Fax', 'tcp' );?>:</label>
					<input type="text" id="shipping_fax" name="shipping_fax" value="<?php $this->getValue( 'shipping_fax' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_fax' );?></li>
					<li><label for="shipping_email"><?php _e( 'eMail', 'tcp' );?>:</label>
					<input type="text" id="shipping_email" name="shipping_email" value="<?php $this->getValue( 'shipping_email' );?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( $error_shipping, 'shipping_email' );?></li>
				</ul>
			</div> <!-- new_shipping_area -->
			<?php do_action( 'tcp_checkout_shipping' );?>
			<p>
				<input type="button" value="<?php _e( 'Back', 'tcp' );?>" class="tcp_back tcp_back_shipping"
					onclick="jQuery('.shipping_layer_info').hide();jQuery('.billing_layer_info').show();" />
				<input type="submit" name="tcp_load_plugins" value="<?php _e( 'Continue', 'tcp' );?>"
					class="tcp_continue tcp_continue_shipping" />
			</p>
		</div> <!-- shipping_layer_info -->
	</div> <!-- shipping_layer -->
<!--//
//4. Shipping method
//-->
	<div id="sending_layer" class="sending_layer">
		<h3><?php _e( '4. Sending methods', 'tcp' );?></h3>
		<?php if ( ( isset( $_REQUEST['tcp_load_plugins'] ) || isset( $_REQUEST['tcp_show_cart'] ) ) && ! $has_validation_error || ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) ) : // $shoppingCart->areAllDownLoable() || $shoppingCart->isFreeShipping() ) :?>
		<div class="sending_layer_info checkout_info clearfix" id="sending_layer_info"<?php
		if ( $shoppingCart->isDownloadable() || isset( $_REQUEST['tcp_show_cart'] )  || ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) ) echo ' style="display:none"';?>>
			<?php $applicable_plugins = tcp_get_applicable_shipping_plugins( $shipping_country, $shoppingCart );
			if ( is_array( $applicable_plugins ) && count( $applicable_plugins ) > 0 ) :?>
				<ul>
				<?php 
				$first_plugin_value = false;
				foreach( $applicable_plugins as $plugin_data ) :
					$tcp_plugin = $plugin_data['plugin'];
					$instance = $plugin_data['instance'];
					$plugin_name = get_class( $tcp_plugin );
					$plugin_value = $plugin_name . '#' . $instance;
					if ( ! $first_plugin_value ) $first_plugin_value = $plugin_value;?>
					<li>
						<input type="radio" id="<?php echo $plugin_name;?>" name="shipping_method_id" value="<?php echo $plugin_value;?>" 
						<?php checked( $plugin_value, isset( $_REQUEST['shipping_method_id'] ) ? $_REQUEST['shipping_method_id'] : $first_plugin_value );?> />
						<label for="<?php echo $plugin_name;?>"><?php echo $tcp_plugin->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart, $currency );?></label>
					</li>
				<?php endforeach;?>
				</ul>
			<?php endif;?>
			<?php do_action( 'tcp_checkout_sending' );?>
			<p>
				<input type="button" value="<?php _e( 'Back', 'tcp' );?>" class="tcp_back tcp_back_sending"
					onclick="jQuery('.sending_layer_info').hide();jQuery('.shipping_layer_info').show();"/>
				<input type="button" value="<?php _e( 'Continue', 'tcp' );?>" class="tcp_continue tcp_continue_sending"
					onclick="jQuery('.sending_layer_info').hide();jQuery('.payment_layer_info').show();"/>
			</p>
		</div><!-- sending_layer_info -->
		<?php endif;?>
	</div><!-- sending_layer -->
<!--//
//5. Payments method
//-->
	<div id="payment_layer" class="payment_layer" >
		<h3><?php _e( '5. Payment methods', 'tcp' );?></h3>
		<?php if ( isset( $_REQUEST['tcp_load_plugins'] ) || isset( $_REQUEST['tcp_show_cart'] ) || ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) ) : // $shoppingCart->areAllDownLoable() || $shoppingCart->isFreeShipping() ) :?>
		<div class="payment_layer_info checkout_info clearfix" id="payment_layer_info"<?php
		if ( ! $shoppingCart->isDownloadable() || ! isset( $_REQUEST['tcp_load_plugins'] ) ) : 
			?> style="display:none"<?php
		endif;?>>
		<?php $applicable_plugins = tcp_get_applicable_payment_plugins( $shipping_country, $shoppingCart );
			if ( is_array( $applicable_plugins ) && count( $applicable_plugins ) > 0 ) :?>
				<ul>
				<?php
				$first_plugin_value = false;
				foreach( $applicable_plugins as $plugin_data ) :
					$tcp_plugin = $plugin_data['plugin'];
					$instance = $plugin_data['instance'];
					$plugin_name = get_class( $tcp_plugin );
					$plugin_value = $plugin_name . '#' . $instance;
					if ( ! $first_plugin_value ) $first_plugin_value = $plugin_value;?>
					<li>
						<input type="radio" id="<?php echo $plugin_name;?>"	name="payment_method_id" value="<?php echo $plugin_value;?>" 
						<?php checked( $plugin_value, isset( $_REQUEST['payment_method_id'] ) ? $_REQUEST['payment_method_id'] : $first_plugin_value );?> />
						<label for="<?php echo $plugin_name;?>"><?php echo $tcp_plugin->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart, $currency );?></label>
					</li>
				<?php endforeach;?>
				</ul>
			<?php endif;?>
			<?php do_action( 'tcp_checkout_payments' );?>
			<p>
				<input type="button" value="<?php _e( 'Back', 'tcp' );?>" class="tcp_back tcp_back_payment"
					onclick="<?php
					if ( $shoppingCart->isDownloadable() ) : 
						?>jQuery('.shipping_layer_info').show();<?php
						else: 
						?>jQuery('.sending_layer_info').show();<?php
					endif;?>jQuery('.payment_layer_info').hide();"/>
				<input type="submit" name="tcp_show_cart" value="<?php _e( 'Continue', 'tcp' );?>"
					class="tcp_continue tcp_continue_payment" />
			</p>
		</div><!-- sending_layer_info -->
		<?php endif;?>
	</div><!-- sending_layer -->
<!--//
//6. Cart
//-->
	<div id="cart_layer" class="cart_layer">
		<h3><?php _e( '6. Cart', 'tcp' );?></h3>
		<?php if ( isset( $_REQUEST['tcp_show_cart'] ) || ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) ) :?>
		<div id="cart_layer_info" class="cart_layer_info checkout_info clearfix" <?php
		 if ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) 
		 	echo 'style="display:none;"';?>>
			<?php $this->createOrderCart( $shipping_country, $shoppingCart, $currency, true );?>
			<?php do_action( 'tcp_checkout_cart_before' );?>
			<label for="comment"><?php _e( 'Comments:', 'tcp' );?></label><br />
			<textarea id="comment" name="comment" cols="40" rows="3" maxlength="255"><?php echo isset( $_REQUEST['comment'] ) ? $_REQUEST['comment'] : '';?></textarea>
			<?php do_action( 'tcp_checkout_cart_after' );?>
			<p>
				<input type="button" value="<?php _e( 'Back', 'tcp' );?>" class="tcp_back tcp_back_cart"
					onclick="jQuery('.cart_layer_info').hide();jQuery('.payment_layer_info').show();" />
				<input type="button" value="<?php _e( 'Continue', 'tcp' );?>" class="tcp_continue tcp_continue_cart"
					onclick="jQuery('.cart_layer_info').hide();jQuery('.legal_notice_layer_info').show();" />
			</p>
		</div><!-- cart_layer_info -->
		<?php endif;?>	
	</div><!-- cart_layer -->
<!--//
//7. Legal notice
//-->
	<div id="legal_notice_layer" class="legal_notice_layer">
		<h3><?php _e('7. Legal notice', 'tcp');?></h3>
		<?php $legal_notice_accept = isset( $_REQUEST['legal_notice_accept'] ) ? $_REQUEST['legal_notice_accept'] : '';?>
		<?php if ( isset( $_REQUEST['tcp_show_cart'] ) || ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) ) :?>
			<div id="legal_notice_layer_info" class="legal_notice_layer_info checkout_info clearfix"<?php
			if ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) :?>
			<?php else :
				?> style="display:none;"<?php
			endif;?>>
			<?php $legal_notice = isset( $settings['legal_notice'] ) ? $settings['legal_notice'] : __( 'Legal notice', 'tcp' );
			if ( strlen( $legal_notice ) ) :?>
				<label for="legal_notice"><?php _e('Legal notice:', 'tcp');?></label><br />
				<textarea id="legal_notice" cols="60" rows="8" readonly="true"><?php echo $legal_notice;?></textarea>
				<br />
				<label for="legal_notice_accept"><?php _e( 'Accept conditions:', 'tcp' );?></label>
				<input type="checkbox" id="legal_notice_accept" name="legal_notice_accept" value="Y" />
				<?php if ( isset( $_REQUEST['tcp_create_order'] ) && $legal_notice_accept != 'Y' ) :?>
					<p class="error"><?php _e( 'You must accept the conditions!!', 'tcp' );?></p>
				<?php endif;?>
			<?php else : ?>
				<input type="hidden" name="legal_notice_accept" value="Y" />
				<p><?php _e( 'When you click in the \'create order\' button the order
				 will be created and if you have choose an external payment method the system will show a button 
				 to go to the external web (usually your bank\'s payment gateway)','tcp' );?></p>
			<?php endif;?>
			<p>
			<input type="button" value="<?php _e( 'Back', 'tcp' );?>" class="tcp_back tcp_back_legal_notice"
				onclick="jQuery('.legal_notice_layer_info').hide();jQuery('.cart_layer_info').show();"/>
			<input type="submit" id="tcp_create_order" name="tcp_create_order" value="<?php _e( 'Create order', 'tcp' );?>"
				class="tcp_continue tcp_continue_legal_notice" />
			</p>
		</div> <!-- legal_notice_layer_info-->
	<?php endif;?>
	</div> <!-- legal_notice_layer -->
	</form>
</div> <!-- entry-content -->
		<?php endif;
	}
	
	private function createOrderCart( $shipping_country, $shoppingCart , $currency, $echo = true ) {
		$out = '<table class="widefat fixed" cellspacing="0">' . "\n";
		$out .= '<thead>' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th>' . __( 'Name', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'Price', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'Units', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'Weight', 'tcp' ) . '</th>' . "\n";
		$out .= '<th>' . __( 'Total', 'tcp' ) . '</th>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '</thead>' . "\n";
		$out .= '<tbody>' . "\n";

		$total = 0;
		$i = 0;
		foreach( $shoppingCart->getItems() as $item ) {
			$out .= '<tr ';
			if ( $i++ & 1 == 1 ) $out .= 'class="par"';
			$out .= '>' . "\n";
			$out .= '<td>' . get_the_title( $item->getPostId() );
			$out .= $item->getOption1Id() > 0 ? '<br />' . get_the_title( $item->getOption1Id() ) : '';
			$out .= $item->getOption2Id() > 0 ? '-' . get_the_title( $item->getOption2Id() ) : '';
			$out .= '</td>' . "\n";
			if ( $item->getTax() > 0 )
				$out .= '<td>' . $this->numberFormat( $item->getUnitPrice(), $currency ) . ' (' . $item->getTax() . '%)</td>' . "\n";
			else
				$out .= '<td>' . $this->numberFormat( $item->getUnitPrice(), $currency ) .'</td>' . "\n";
			$out .= '<td>' . $item->getCount() . '</td>' . "\n";
			$out .= '<td>' . $item->getWeight() . '</td>' . "\n";
			$price = $item->getTotal();
			$total += $price;
			$out .= '<td>' . $this->numberFormat( $price, $currency ) . '</td>' . "\n";
			$out .= '</tr>' . "\n";
		}
		$out .= '<tr id="shipping_cost"';
		if ( $i++ & 1 == 1 ) $out .= ' class="par"';
		$out .= '>' . "\n";
		$out .= '<td colspan="4" style="text-align:right">' . __( 'Shipping cost', 'tcp' ) .'</td>' . "\n";

		if ( isset( $_REQUEST['shipping_method_id'] ) ) { //sending
			$smi = $_REQUEST['shipping_method_id'];
			$smi = explode( '#', $smi );
			$class = $smi[0];
			$instance = $smi[1];
			$shipping_method = new $class();
			$shipping_cost = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
		} else
			$shipping_cost = 0;
		if ( isset( $_REQUEST['payment_method_id'] ) ) {
			$pmi = $_REQUEST['payment_method_id'];
			$pmi = explode( '#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method = new $class();
			$payment_cost = $payment_method->getCost( $instance, $shipping_country, $shoppingCart );
		} else
			$payment_cost = 0;
		$out .= '<td>' . $this->numberFormat( $shipping_cost, $currency ) . '</td>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '<tr id="payment_cost"';
		if ( $i++ & 1 == 1 ) $out .= ' class="par"';
		$out .= '>' . "\n";
		$out .= '<td colspan="4" style="text-align:right">' . __( 'Payment cost', 'tcp' ) . '</td>' . "\n";
		$out .= '<td>' . $this->numberFormat( $payment_cost, $currency ) . '</td>' . "\n";
		$out .= '</tr>' . "\n";
		$total += $shipping_cost + $payment_cost;
		$total = apply_filters( 'tcp_shopping_cart_create_order', $total );
		$out .= '<tr id="total"';
		if ( $i++ & 1 == 1 ) $out .= ' class="par"';
		$out .= '>' . "\n";
		$out .= '<td colspan="4" style="text-align:right;">' . __( 'Total', 'tcp' ) . '</td>' . "\n";
		$out .= '<td style="color:red;"><span id="total">' . $this->numberFormat( $total, $currency ) . '</span></td>' . "\n";
		$out .= '</tr>';
		$out .= '</tbody></table>' . "\n";
		if ( $echo )
			echo $out;
		else
			return $out;
	}

	private static function numberFormat( $number, $currency = '', $decimal = 2 ) {
		$text = number_format( $number, $decimal, ',', '.' );
		$text .= '&nbsp;' . $currency;
		return $text;
	}

	private function getValue( $id, $echo = true ) {
		$txt = isset( $_REQUEST[$id] ) ? $_REQUEST[$id] : '';
		if ( $echo )
			echo $txt;
		else
			return $txt;
	}

	private function showErrorMsg( $error_array, $id ) {
		if ( isset( $error_array[$id] ) )
			//foreach($error_shipping[$id] )
			echo '<span class="description">', $error_array[$id][0], '</span>';
	}
	
	private function createNewBillingAddress( $order ) {
		if ( $order['customer_id'] > 0 ) {
			$address = array();
			$address['customer_id'] = $order['customer_id'];
			$address['default_shipping'] = 'N';
			$address['default_billing'] = 'N';
			$address['name'] = 'billing address';
			$address['firstname'] = $order['billing_firstname'];
			$address['lastname'] = $order['billing_lastname'];
			$address['company'] = $order['billing_company'];
			$address['street'] = $order['billing_street'];
			$address['city'] = $order['billing_city'];
			$address['city_id'] = $order['billing_city_id'];
			$address['region'] = $order['billing_region'];
			$address['region_id'] = $order['billing_region_id'];
			$address['postcode'] = $order['billing_postcode'];
			$address['country'] = $order['billing_country'];
			$address['country_id'] = $order['billing_country_id'];
			$address['telephone_1'] = $order['billing_telephone_1'];
			$address['telephone_2'] = $order['billing_telephone_2'];
			$address['fax'] = $order['billing_fax'];
			$address['email'] = $order['billing_email'];
			Addresses::save($address);
		}
	}

	function createNewShippingAddress($order)
	{
		if ($order['customer_id'] > 0)
		{
			$address = array();
			$address['customer_id'] = $order['customer_id'];
			$address['default_shipping'] = 'N';
			$address['default_billing'] = 'N';
			$address['name'] = 'shipping address';
			$address['firstname'] = $order['shipping_firstname'];
			$address['lastname'] = $order['shipping_lastname'];
			$address['company'] = $order['shipping_company'];
			$address['street'] = $order['shipping_street'];
			$address['city'] = $order['shipping_city'];
			$address['city_id'] = $order['shipping_city_id'];
			$address['region'] = $order['shipping_region'];
			$address['region_id'] = $order['shipping_region_id'];
			$address['postcode'] = $order['shipping_postcode'];
			$address['country'] = $order['shipping_country'];
			$address['country_id'] = $order['shipping_country_id'];
			$address['telephone_1'] = $order['shipping_telephone_1'];
			$address['telephone_2'] = $order['shipping_telephone_2'];
			$address['fax'] = $order['shipping_fax'];
			$address['email'] = $order['shipping_email'];
			Addresses::save($address);
		}
	}

	private function getShippingCountry() {
		$shipping_country = '';
		if ( isset( $_REQUEST['selected_shipping_address'] ) && $_REQUEST['selected_shipping_address'] == 'new' )
			$shipping_country = $_REQUEST['shipping_country_id'];
		elseif ( isset( $_REQUEST['selected_shipping_address'] ) && $_REQUEST['selected_shipping_address'] == 'BIL' )
			if ( isset( $_REQUEST['selected_billing_address'] ) && $_REQUEST['selected_billing_address'] == 'new' )
				$shipping_country = $_REQUEST['billing_country_id'];
			else {
				$address_id = $_REQUEST['selected_billing_id'];
				$address = Addresses::get( $address_id );
				$shipping_country = $address->country_id;
			}
		else //selected_shipping_address == Y
			if ( isset( $_REQUEST['selected_shipping_id'] ) ) {
				$address_id = $_REQUEST['selected_shipping_id'];
				$address = Addresses::get( $address_id );
				$shipping_country = $address->country_id;
			}
		return $shipping_country;
	}
}
?>
