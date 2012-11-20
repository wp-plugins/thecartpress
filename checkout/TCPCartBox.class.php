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
require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );

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
		} ?>
		<div id="cart_layer_info" class="checkout_info clearfix">
			<?php $settings = get_option( 'tcp_' . get_class( $this ), array() );
		 	do_action( 'tcp_checkout_cart_before', $settings );
			$this->show_order_cart( $shipping_country, $settings );
		 	do_action( 'tcp_checkout_cart_after' ); ?>
		 	<div class="tcp_go_to_shopping_cart">
		 		<a href="<?php tcp_the_shopping_cart_url(); ?>"><?php _e( 'Shopping Cart', 'tcp' ); ?></a>
		 	</div><!-- .tcp_go_to_shopping_cart -->
			<?php $see_comment = isset( $settings['see_comment'] ) ? $settings['see_comment'] : true;
			if ( $see_comment ) :
				if ( isset( $_REQUEST['comment'] ) ) {
					$comment = $_REQUEST['comment'];
				} elseif ( isset( $_SESSION['tcp_checkout']['cart']['comment'] ) ) {
					$comment = $_SESSION['tcp_checkout']['cart']['comment'];
				} else {
					$comment = '';
				} ?>
			<div class="tcp_comment">
				<label for="comment"><?php echo apply_filters( 'tcp_checkout_cart_comment_label', __( 'Comments:', 'tcp' ) ); ?></label>
				<p>
					<textarea id="comment" name="comment" cols="40" rows="3" maxlength="255"><?php echo $comment; ?></textarea>
				</p>
			</div><!-- .tcp_comment -->
			<?php endif; ?>
		</div><!-- cart_layer_info -->
		<?php return true;
	}

	function show_config_settings() {
		$settings		= get_option( 'tcp_' . get_class( $this ), array() );
		$see_sku		= isset( $settings['see_sku'] ) ? $settings['see_sku'] : true;
		//$see_weight		= isset( $settings['see_weight'] ) ? $settings['see_weight'] : true;
		$see_tax		= isset( $settings['see_tax'] ) ? $settings['see_tax'] : true;
		$see_tax_detail	= isset( $settings['see_tax_detail'] ) ? $settings['see_tax_detail'] : true;
		$see_comment	= isset( $settings['see_comment'] ) ? $settings['see_comment'] : true; ?>
		<table class="form-table">
		<tbody>
		<!--<tr valign="top">
			<th scope="row"><label for="see_weight"><?php _e( 'Display Weight column', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_weight" id="see_weight" value="yes" <?php checked( $see_weight );?>/></td>
		</tr>-->
		<tr valign="top">
			<th scope="row"><label for="see_tax"><?php _e( 'Display Tax column', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_tax" id="see_tax" value="yes" <?php checked( $see_tax );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_tax_detail"><?php _e( 'Display Tax detail', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_tax_detail" id="see_tax_detail" value="yes" <?php checked( $see_tax_detail );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_sku"><?php _e( 'Display SKU column', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_sku" id="see_sku" value="yes" <?php checked( $see_sku );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_comment"><?php _e( 'Display Comment', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_comment" id="see_comment" value="yes" <?php checked( $see_comment );?>/></td>
		</tr>
		<?php do_action( 'tcp_checkout_show_config_settings', $settings ); ?>
		</tbody>
		</table>
		<?php return true;
	}

	function save_config_settings() {
		$settings = array(
			//'see_weight'		=> isset( $_REQUEST['see_weight'] ) ? $_REQUEST['see_weight'] == 'yes' : false,
			'see_tax'			=> isset( $_REQUEST['see_tax'] ) ? $_REQUEST['see_tax'] == 'yes' : false,
			'see_tax_detail'	=> isset( $_REQUEST['see_tax_detail'] ) ? $_REQUEST['see_tax_detail'] == 'yes' : false,
			'see_sku'			=> isset( $_REQUEST['see_sku'] ) ? $_REQUEST['see_sku'] == 'yes' : false,
			'see_comment'		=> isset( $_REQUEST['see_comment'] ) ? $_REQUEST['see_comment'] == 'yes' : false,
		);
		$settings = apply_filters( 'tcp_cart_box_config_settings', $settings );
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	private function show_order_cart( $shipping_country, $args = array() ) {
		do_action( 'tcp_checkout_create_order_cart', $args );
		$see_sku	= isset( $args['see_sku'] ) ? $args['see_sku'] : true;
		//$see_weight	= isset( $args['see_weight'] ) ? $args['see_weight'] : true;
		global $thecartpress;
		if ( $thecartpress ) $see_weight = $thecartpress->get_setting( 'use_weight', true );
		$see_tax		= isset( $args['see_tax'] ) ? $args['see_tax'] : true;
		$see_tax_detail	= isset( $args['see_tax_detail'] ) ? $args['see_tax_detail'] : true;
//$see_tax_summary	= isset( $args['see_tax_summary'] ) ? $args['see_tax_summary'] : false;
//require_once( TCP_CLASSES_FOLDER . 'CartTable.class.php' );
//require_once( TCP_CLASSES_FOLDER . 'CartSourceSession.class.php' );
//$cart_table = new TCPCartTable();
//$cart_table->show( new TCPCartSourceSession( array( 'see_tax' => $see_tax, 'see_tax_summary' => $see_tax_summary, 'see_weight' => $see_weight, 'see_sku' => $see_sku, 'is_editing_units' => false, 'see_other_costs' => true ) ) );
		$shoppingCart = TheCartPress::getShoppingCart(); ?>
	<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
		<thead>
			<tr class="tcp_cart_title_row">
				<th class="tcp_cart_name"><?php _e( 'Name', 'tcp' ); ?></th>
				<th class="tcp_cart_price"><?php _e( 'Price', 'tcp' ); ?></th>
			<?php if ( $see_sku ) : ?>
				<th class="tcp_cart_sku"><?php _e( 'SKU', 'tcp' ); ?></th>
			<?php endif; ?>
			<?php if ( $see_tax ) : ?>
				<th class="tcp_cart_tax"><?php _e( 'Tax', 'tcp' ); ?></th>
			<?php endif; ?>
				<th class="tcp_cart_units"><?php _e( 'Units', 'tcp' ); ?></th>
			<?php if ( $see_weight ) : ?>
				<th class="tcp_cart_weight"><?php _e( 'Weight', 'tcp' ); ?></th>
			<?php endif; ?>
				<th class="tcp_cart_price"><?php _e( 'Total', 'tcp' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php $i = 0;
		$decimals = tcp_get_decimal_currency();
		$table_amount_without_tax = 0;
		$table_amount_with_tax = 0;
		foreach( $shoppingCart->getItems() as $item ) :
			$tax = $item->getTax();	//$tax = tcp_get_the_tax( $item->getPostId() );
			if ( ! tcp_is_display_prices_with_taxes() ) $discount = round( $item->getDiscount() / $item->getUnits(), $decimals );
			else $discount = 0;
			$unit_price_without_tax = tcp_get_the_price_without_tax( $item->getPostId(), $item->getUnitPrice() );
			$unit_price_without_tax = round( $unit_price_without_tax - $discount, $decimals );

			$tax_amount_per_unit = $unit_price_without_tax * $tax / 100;
			$tax_amount_per_unit = round( $tax_amount_per_unit, $decimals );
			$tax_amount = round( $tax_amount_per_unit * $item->getUnits(), $decimals );
			$line_price_without_tax = round( $unit_price_without_tax * $item->getUnits(), $decimals );

			$line_price_with_tax = $line_price_without_tax + $tax_amount;

			$table_amount_without_tax += $line_price_without_tax;
			$table_amount_with_tax += $line_price_with_tax; ?>
			<tr class="tcp_cart_product_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<?php $title = tcp_get_the_title( tcp_get_current_id( $item->getPostId() ), tcp_get_current_id( $item->getOption1Id() ), tcp_get_current_id( $item->getOption2Id() ) );
				$title = apply_filters( 'tcp_cart_box_title_item', $title, $item ); ?>
				<td class="tcp_cart_name">
					<?php echo $title; ?>
				</td>
				<td class="tcp_cart_unit_price">
				<?php if ( $discount > 0 ) : ?>
					<?php printf( __('%s (Discount %s)', 'tcp' ), tcp_format_the_price( $unit_price_without_tax ), tcp_format_the_price( $discount ) ); ?>
				<?php else : ?>
					<?php echo tcp_format_the_price( $unit_price_without_tax ); ?>
				<?php endif; ?>
				</td>
				<?php if ( $see_sku ) : ?>
				<td class="tcp_cart_sku">
					<?php echo tcp_get_the_sku( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() ); //tcp_get_the_sku( $item->getPostId() ); ?>
				</td>
				<?php endif; ?>
				<?php if ( $see_tax ) : ?>
				<td class="tcp_cart_tax">
					<?php echo tcp_format_the_price( $tax_amount_per_unit ); ?>
					<?php if ( $see_tax_detail ) : ?>&nbsp;(<?php echo tcp_number_format( $tax, 0 ); ?>%)<?php endif; ?>
				</td>
				<?php endif; ?>
				<td class="tcp_cart_units">
					<?php echo tcp_number_format( $item->getCount(), 0 ); ?>
				</td>
				<?php if ( $see_weight ) : ?>
				<td class="tcp_cart_weight">
					<?php echo tcp_number_format( $item->getWeight(), 0 ); ?>&nbsp;<?php echo tcp_get_the_unit_weight(); ?>
				</td>
				<?php endif; ?>
				<td class="tcp_cart_row_total">
					<?php if ( $see_tax ) : ?>
						<?php echo tcp_format_the_price( $line_price_with_tax ); ?>
					<?php else : ?>
						<?php echo tcp_format_the_price( $line_price_without_tax ); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach;
		$colspan = 1;
		if ( $see_weight ) $colspan++;
		if ( $see_tax ) $colspan++;
		if ( $see_sku ) $colspan++;
		if ( tcp_is_display_prices_with_taxes() ) $discount = $shoppingCart->getAllDiscounts();
		else $discount = $shoppingCart->getCartDiscountsTotal();
		if ( $discount > 0 ) : ?>
			<tr id="discount" class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif; ?>">
				<td style="text-align:right"><?php _e( 'Discounts', 'tcp' ); ?></td>
				<td colspan="<?php echo $colspan + 1; ?>">&nbsp;</td>
				<td class="tcp_cart_row_total">-<?php echo tcp_format_the_price( $discount ); ?></td>
			</tr>
		<?php endif;
		if ( isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ) { //sending
			if ( ! $shoppingCart->isFreeShipping() ) {
				$smi = $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'];
				$smi = explode( '#', $smi );
				$class = $smi[0];
				$instance = $smi[1];
				$shipping_method = new $class();
				$shipping_cost = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
				$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, $shipping_cost, __( 'Shipping cost', 'tcp' ) );
			}
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
		if ( $shoppingCart->isFreeShipping() ) : ?>
			<tr class="tcp_cart_free_shipping<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td class="tcp_cost_tcp_free_shipping" style="text-align:right"><?php _e( 'Free shipping', 'tcp' ); ?></td>
				<td colspan="<?php echo $colspan + 2; ?>">&nbsp;</td>
			</tr>
		<?php endif;
		$costs = $shoppingCart->getOtherCosts();
		asort( $costs, SORT_STRING );
		$colspan_cost = $colspan;
		if ( $see_sku ) $colspan_cost--;
		if ( $see_tax ) $colspan_cost--;
		foreach( $costs as $cost_id => $cost ) :
			$cost_without_tax = tcp_get_the_shipping_cost_without_tax( $cost->getCost() );
			$tax = tcp_get_the_shipping_tax();
			$tax_amount = $cost_without_tax * $tax / 100;

			$cost_with_tax = $cost_without_tax + $tax_amount;
			$cost_with_tax = round( $cost_with_tax, $decimals );
			$table_amount_with_tax += $cost_with_tax;

			$cost_without_tax = round( $cost_without_tax, $decimals );
			$table_amount_without_tax += $cost_without_tax; ?>
			<tr class="tcp_cart_other_costs_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td class="tcp_cost_' . $cost_id . '"><?php echo $cost->getDesc(); ?></td>
				<td class="tcp_cart_unit_price"><?php echo tcp_format_the_price( $cost_without_tax ); ?></td>

				<?php if ( $see_sku ) : ?>
				<td>&nbsp;</td>
				<?php endif; ?>

				<?php if ( $see_tax ) : ?>
				<td><?php echo tcp_format_the_price( $tax_amount ); ?></td>
				<?php endif; ?>

				<td colspan="<?php echo $colspan_cost; ?>">&nbsp;</td>
				<td class="tcp_cart_row_total"><?php echo tcp_format_the_price( $cost_with_tax ); ?></td>
			</tr>
		<?php endforeach; ?>
		<?php $table_amount_with_tax -= $discount;
		$total = apply_filters( 'tcp_checkout_set_total', $table_amount_with_tax );
		do_action( 'tcp_checkout_before_total', $args ); ?>

			<tr id="total" class="tcp_cart_total_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td class="tcp_cart_total_title"><?php _e( 'Total', 'tcp'); ?></td>
				<td colspan="<?php echo $colspan; ?>">&nbsp;</td>
				<td>&nbsp;</td>
				<td class="tcp_cart_total"><span id="total"><?php echo tcp_format_the_price( $total ); ?></span></td>
			</tr>
		</tbody>
		</table>
		<?php do_action( 'tcp_checkout_after_order_cart', $args );
		tcp_do_template( 'tcp_checkout_order_cart' );
	}
}
?>