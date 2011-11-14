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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once( dirname( __FILE__ ) . '/TCPCheckoutBox.class.php' );

class TCPCartBox extends TCPCheckoutBox {
	function get_title() {
		return __( 'Cart', 'tcp' );
	}

	function get_class() {
		return 'cart_layer';
	}
	
	function before_action() {
		return apply_filters( 'tcp_before_cart_box', 0 );
	}

	function after_action() {
		$comment = array(
			'comment' => isset( $_REQUEST['comment'] ) ? $_REQUEST['comment'] : 0,
		);
		$_SESSION['tcp_checkout']['cart'] = $comment;
		do_action( 'tcp_after_cart_box' );
		return apply_filters( 'tcp_after_cart_box', true );
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
			}?>
		 	<div class="tcp_go_to_shopping_cart"><a href="<?php tcp_the_shopping_cart_url(); ?>"><?php _e( 'Shopping Cart', 'tcp' ); ?></a></div><!-- .tcp_go_to_shopping_cart -->
			<div class="tcp_comment"><label for="comment"><?php _e( 'Comments:', 'tcp' ); ?></label><br />
			<textarea id="comment" name="comment" cols="40" rows="3" maxlength="255"><?php echo $comment; ?></textarea></div><!-- .tcp_comment -->
		</div><!-- cart_layer_info --><?php
		return true;
	}

	private function showOrderCart( $shipping_country ) {
		do_action( 'tcp_checkout_create_order_cart' );
		$shoppingCart = TheCartPress::getShoppingCart(); ?>
		<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
		<thead>
		<tr class="tcp_cart_title_row">
		<th class="tcp_cart_name"><?php _e( 'Name', 'tcp' ); ?></th>
		<th class="tcp_cart_unit_price"><?php _e( 'Price', 'tcp' ); ?></th>
		<th class="tcp_cart_units"><?php _e( 'Units', 'tcp' ); ?></th>
		<?php //TODO remove weight if...?>
		<th class="tcp_cart_weight"><?php _e( 'Weight', 'tcp' ); ?></th>
		<th class="tcp_cart_price"><?php _e( 'Total', 'tcp' ); ?></th>
		</tr>
		</thead>
		<tbody><?php
		$i = 0;
		$decimals = tcp_get_decimal_currency();
		$table_amount_without_tax = 0;
		$table_amount_with_tax = 0;
		foreach( $shoppingCart->getItems() as $item ) :
			$tax = tcp_get_the_tax( $item->getPostId() );
			$res = tcp_get_price_and_tax( $item->getUnitPrice(), $tax );
			$unit_price_without_tax = round( $res[0], $decimals );
			$tax_amount = round( $res[1] * $item->getUnits(), $decimals );
			$line_price_without_tax = $unit_price_without_tax * $item->getUnits();
			$line_price_with_tax = $line_price_without_tax + $tax_amount; 
			$table_amount_without_tax += $line_price_without_tax;
			$table_amount_with_tax += $line_price_with_tax;
			?>
			<tr class="tcp_cart_product_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td class="tcp_cart_name"><?php echo tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() ); ?></td>
				<td class="tcp_cart_unit_price"><?php echo tcp_format_the_price( $unit_price_without_tax ); ?></td>
				<td class="tcp_cart_units"><?php echo tcp_number_format( $item->getCount(), 0 ); ?></td>
				<?php //TODO remove weight if...?>
				<td class="tcp_cart_weight"><?php echo tcp_number_format( $item->getWeight(), 0 ); ?>&nbsp;<?php echo tcp_get_the_unit_weight(); ?></td>
				<td><?php echo tcp_format_the_price( $line_price_without_tax ); ?></td>
			</tr>
		<?php endforeach;
		$discount = $shoppingCart->getAllDiscounts();
		if ( $discount > 0 ) : ?>
			<tr id="discount" class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif; ?>">
			<td colspan="4" style="text-align:right"><?php _e( 'Discounts', 'tcp' ); ?></td>
			<td><?php echo tcp_format_the_price( $discount ); ?></td>
			</tr><?php
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
		foreach( $costs as $cost_id => $cost ) :
			$tax = tcp_get_the_shipping_tax();
			$res = tcp_get_shipping_cost_and_tax( $cost->getCost(), $tax );
			$cost_without_tax = $res[0];
			$tax_amount = round( $res[1], $decimals );
			$cost_with_tax = $cost_without_tax + $tax_amount;
			$table_amount_with_tax += $cost_with_tax;
			$table_amount_without_tax += $cost_without_tax;
			?>
			<tr id="other_costs" class="tcp_cart_other_costs_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
			<td colspan="4" class="tcp_cost_' . $cost_id . '" style="text-align:right"><?php echo $cost->getDesc(); ?></td>
			<td><?php echo tcp_format_the_price( $cost_without_tax ); ?></td>
			</tr>
		<?php endforeach;
		$show_tax_summary = false;
		if ( $table_amount_without_tax == $table_amount_with_tax ) {
			$show_tax_summary = tcp_get_display_zero_tax_subtotal();
		} elseif ( tcp_is_display_full_tax_summary() ) {
			$show_tax_summary = true;
		}
		if ( $show_tax_summary ) : ?>
			<tr id="subtotal" class="tcp_cart_subtotal_row <?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td colspan="4" class="tcp_cart_subtotal_title"><?php _e( 'Taxes', 'tcp'); ?></td>
				<td class="tcp_cart_subtotal"><span id="subtotal"><?php echo tcp_format_the_price( $table_amount_with_tax - $table_amount_without_tax ); ?></span></td>
			</tr>
		<?php endif;

		$table_amount_with_tax -= $discount;
		$total = apply_filters( 'tcp_checkout_set_total', $table_amount_with_tax );
		do_action( 'tcp_checkout_before_total' ); ?>
		<tr id="total" class="tcp_cart_total_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
		<td colspan="4" class="tcp_cart_total_title"><?php _e( 'Total', 'tcp'); ?></td>
		<td class="tcp_cart_total"><span id="total"><?php echo tcp_format_the_price( $total ); ?></span></td>
		</tr>
		
		</tbody></table><?php
		do_action( 'tcp_checkout_after_order_cart' );
	}
}
?>
