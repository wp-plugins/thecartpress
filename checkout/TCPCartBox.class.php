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

class TCPCartBox extends TCPCheckoutBox {
	function get_title() {
		return __( 'Cart', 'tcp' );
	}

	function get_class() {
		return 'cart_layer';
	}
	
	function after_action() {
		$comment = array(
			'comment' => isset( $_REQUEST['comment'] ) ? $_REQUEST['comment'] : 0,
		);
		$_SESSION['tcp_checkout']['cart'] = $comment;
		return true;
	}

	function show() {
		$shipping_country = '';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			$selected_billing_address = isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : false;
			if ( $selected_billing_address == 'new' ) {
				$shipping_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			} else { //if ( $selected_billing_address == 'Y' ) {
				$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			}		
		} elseif ( $selected_shipping_address == 'Y' ) {
			$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
		}?>
		<div id="cart_layer_info" class="cart_layer_info checkout_info clearfix">
		 	<?php do_action( 'tcp_checkout_cart_before' );
			$this->showOrderCart( $shipping_country );
		 	do_action( 'tcp_checkout_cart_after' );
		 	if ( isset( $_REQUEST['comment'] ) ) {
				$comment = $_REQUEST['comment'];
			} elseif ( isset( $_SESSION['tcp_checkout']['cart']['comment'] ) ) {
				$comment = $_SESSION['tcp_checkout']['cart']['comment'];
			} else {
				$comment = '';
			}
		 	?>
			<label for="comment"><?php _e( 'Comments:', 'tcp' );?></label><br />
			<textarea id="comment" name="comment" cols="40" rows="3" maxlength="255"><?php echo $comment;?></textarea>
		</div><!-- cart_layer_info --><?php
		return true;
	}

	private function showOrderCart( $shipping_country ) {
		do_action( 'tcp_checkout_create_order_cart' );
		$shoppingCart = TheCartPress::getShoppingCart();?>
		<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
		<thead>
		<tr class="tcp_cart_title_row">
		<th class="tcp_cart_name"><?php _e( 'Name', 'tcp' );?></th>
		<th class="tcp_cart_unit_price"><?php _e( 'Price', 'tcp' );?></th>
		<th class="tcp_cart_units"><?php _e( 'Units', 'tcp' );?></th>
		<?php //TODO remove weight if...?>
		<th class="tcp_cart_weight"><?php _e( 'Weight', 'tcp' );?></th>
		<th class="tcp_cart_price"><?php _e( 'Total', 'tcp' );?></th>
		</tr>
		</thead>
		<tbody><?php
		$tax_amount = 0;
		$subtotal = 0;
		$i = 0;
		foreach( $shoppingCart->getItems() as $item ) {
			$post_id = tcp_get_current_id( $item->getPostId() );
			$option_1_id = tcp_get_current_id( $item->getOption1Id() );
			$option_2_id = tcp_get_current_id( $item->getOption2Id() );?>
			<tr class="tcp_cart_product_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif;?>">
			<td class="tcp_cart_name"><?php echo tcp_get_the_title( $post_id, $item->getOption1Id(), $item->getOption2Id() );?>
			</td><?php
			$price = tcp_get_the_price( $post_id );
			if ( $option_1_id > 0 ) $price += tcp_get_the_price( $option_1_id );
			if ( $option_2_id > 0 ) $price += tcp_get_the_price( $option_2_id );
			$price_without_tax = tcp_get_the_price_without_tax( $post_id, $price );
			$tax = tcp_get_the_tax_amount( $post_id, $price );
			//$tax = $price - $price_without_tax;?>
			<td class="tcp_cart_unit_price"><?php echo tcp_format_the_price( $price_without_tax );?></td>
			<td class="tcp_cart_units"><?php echo tcp_number_format( $item->getCount(), 0 );?></td>
			<?php //TODO remove weight if...?>
			<td class="tcp_cart_weight"><?php echo tcp_number_format( $item->getWeight(), 0 );?>&nbsp;<?php echo tcp_get_the_unit_weight();?></td><?php
			$tax = $tax * $item->getUnits();
			$tax_amount += $tax;
			$price = $price_without_tax * $item->getUnits();
			$subtotal += $price;?>
			<td class="tcp_cart_price"><?php echo tcp_format_the_price( $price );?></td>
			</tr><?php
		}
		//$discount = $shoppingCart->getDiscount();
		$discount = $shoppingCart->getAllDiscounts();
		if ( $discount > 0 ) : ?>
			<tr id="discount" class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif;?>">
			<td colspan="4" style="text-align:right"><?php _e( 'Discounts', 'tcp' );?></td>
			<td><?php echo tcp_format_the_price( $discount );?></td>
			</tr><?php
			$subtotal -= $discount;
		endif;
		if ( isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ) { //sending
			$smi = $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'];
			$smi = explode( '#', $smi );
			$class = $smi[0];
			$instance = $smi[1];
			$shipping_method = new $class();
			$shipping_cost = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, $shipping_cost, __( 'Shipping cost', 'tcp' ) );
		} else {
			$shoppingCart->deleteOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID );
		}
		if ( isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ) {
			$pmi = $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'];
			$pmi = explode( '#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method = new $class();
			$payment_cost = $payment_method->getCost( $instance, $shipping_country, $shoppingCart );
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID, $payment_cost, __( 'Payment cost', 'tcp' ) );
		} else {
			$shoppingCart->deleteOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID );
		}
		do_action( 'tcp_checkout_calculate_other_costs' );
		$costs = $shoppingCart->getOtherCosts();
		asort( $costs, SORT_STRING );
		foreach( $costs as $cost_id => $cost ) : ?>
			<tr id="other_costs"<?php if ( $i++ & 1 == 1 ) :?> class="tcp_par"<?php endif;?>>
			<td colspan="4" class="tcp_cost_' . $cost_id . '" style="text-align:right"><?php echo $cost->getDesc();?></td>
			<?php $cost_without_tax = tcp_get_the_shipping_cost_without_tax( $cost->getCost() );?>
			<td><?php echo tcp_format_the_price( $cost_without_tax );?></td>
			</tr><?php
			$tax_amount += tcp_calculate_tax_for_shipping( $cost->getCost() );
			$subtotal += $cost_without_tax;
		endforeach;
		$show_tax_summary = false;
		if ( $tax_amount == 0 ) {
			$show_tax_summary = tcp_get_display_zero_tax_subtotal();
		} elseif ( tcp_is_display_full_tax_summary() ) {
			$show_tax_summary = true;
		}
		if ( $show_tax_summary ) : ?>
			<tr id="subtotal" class="tcp_cart_subtotal_row <?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif;?>">
			<td colspan="4" class="tcp_cart_subtotal_title"><?php _e( 'Taxes', 'tcp');?></td>
			<td class="tcp_cart_subtotal"><span id="subtotal"><?php echo tcp_format_the_price( $tax_amount );?></span></td>
			</tr>
		<?php endif;
		$subtotal += $tax_amount;
		$total = apply_filters( 'tcp_checkout_set_total', $subtotal );
		do_action( 'tcp_checkout_before_total' );?>
		<tr id="total" class="tcp_cart_total_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif;?>">
		<td colspan="4" class="tcp_cart_total_title"><?php _e( 'Total', 'tcp');?></td>
		<td class="tcp_cart_total"><span id="total"><?php echo tcp_format_the_price( $total );?></span></td>
		</tr>
		</tbody></table><?php
		do_action( 'tcp_checkout_after_order_cart' );
	}
}
?>
