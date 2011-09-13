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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Shows a Cart table.
 */
class TCP_CartTable {

	static function show( $source, $echo = true ) {
		$out = '';
		if ( $source->see_address() ) {
			if ( $source->get_shipping_firstname() == "" ) {
				$style = 'style="display:none"';
			} else {
				$style = 'style="padding-bottom:1em;"';
			} 
			$out .= '<div id="shipping_info" ' . $style . '>' . "\n";
			$out .= '<h3>' . __( 'Shipping address', 'tcp' ) . '</h3>' . "\n";
			$out .= $source->get_shipping_firstname() . ' ' . $source->get_shipping_lastname() . '<br />' . "\n";
			if ( strlen( $source->get_shipping_company() ) > 0 ) $out .= $source->get_shipping_company() . '<br />' . "\n";
			$out .= $source->get_shipping_street() . '<br />' . "\n";
			$out .= $source->get_shipping_postcode() . ', ' . $source->get_shipping_city() . '<br />' . "\n";
			$out .= $source->get_shipping_region() . ', ' . $source->get_shipping_country() . '<br />' . "\n";
			$telephone = $source->get_shipping_telephone_1();
			if ( strlen( $source->get_shipping_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_shipping_telephone_2();
			if ( strlen( $telephone ) > 0) $out .= __('Telephones', 'tcp') . ': ' . $telephone . '<br />' . "\n";
			if ( strlen( $source->get_shipping_fax() ) > 0) $out .= __('Fax', 'tcp') . ': ' . $source->get_shipping_fax() . '<br />' . "\n";
			if ( strlen( $source->get_shipping_email() ) > 0) $out .= $source->get_shipping_email() . '<br />' . "\n";
			$out .= '</div><!-- shipping_info-->' . "\n";
		
			$out .= '<div id="billing_info" style="padding-bottom:1em;">' . "\n";
			$out .= '<h3>' . __( 'Billing address', 'tcp' ) . '</h3>' . "\n";
			$out .= $source->get_billing_firstname() . ' ' . $source->get_billing_lastname() . '<br />' . "\n";
			if ( strlen( $source->get_billing_company() ) > 0 ) $out .= $source->get_billing_company() . '<br>' . "\n";
			$out .= $source->get_billing_street() . '<br>' . "\n";
			$out .= $source->get_billing_postcode() . ', ' . $source->get_billing_city() . '<br>' . "\n";
			$out .= $source->get_billing_region() . ', ' . $source->get_billing_country() . '<br>' . "\n";
			$telephone = $source->get_billing_telephone_1();
			if ( strlen( $source->get_billing_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_billing_telephone_2();
			if ( strlen( $telephone ) > 0) $out .= __('Telephones', 'tcp') . ': ' . $telephone . '<br>' . "\n";
			if ( strlen( $source->get_billing_fax() ) > 0) $out .= __('Fax', 'tcp') . ': ' . $source->get_billing_fax() . '<br>' . "\n";
			if ( strlen( $source->get_billing_email() ) > 0) $out .= $source->get_billing_email() . '<br><br><br><br>' . "\n";
			$out .= '</div><!-- billing_info -->' . "\n";
		}
		$out .= '<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">' . "\n";
		$out .= '<thead>' . "\n";
		$out .= '<tr class="tcp_cart_title_row">' . "\n";
		if ( $source->see_full() ) $out .= '<th class="tcp_cart_id">' . __( 'Id.', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_cart_name">' . __( 'Name', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_cart_price">' . __( 'Price', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_cart_units">' . __( 'Units', 'tcp' ) . '</th>' . "\n";
		if ( $source->see_full() ) $out .= '<th class="tcp_cart_sku">' . __( 'Sku', 'tcp' ) . '</th>' . "\n";
		if ( $source->see_full() ) $out .= '<th class="tcp_cart_weight">' . __( 'Weight', 'tcp' ) . '</th>' . "\n";
		$out .= '<th class="tcp_cart_total">' . __( 'Total', 'tcp' ) . '</th>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '</thead>' . "\n";
		$out .= '<tbody>' . "\n";

		if ( $source->has_order_details() ) {
			global $thecartpress;
			$stock_management	= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
			$total = 0;
			$total_tax = 0;
			$i = 0;
			$det = '';
			foreach( $source->get_orders_details() as $order_detail ) {
				$det .= '<tr class="tcp_cart_product_row';
				if ( $i++ & 1 == 1 ) $det .= ' par ';
				$det .= '">' . "\n";
				if ( $source->see_full() ) $det .= '<td class="tcp_cart_id">' . $order_detail->get_post_id() . '</td>' . "\n";
				$det .= '<td class="tcp_cart_name">';
				$name = $order_detail->get_name();

				if ( ! $source->see_product_link() ) {
					$det .= $name;
				} elseif ( tcp_is_visible( $order_detail->get_post_id() ) ) {
					$det .= '<a href="' . get_permalink( tcp_get_current_id( $order_detail->get_post_id(), get_post_type( $order_detail->get_post_id() ) ) ) . '">' . $name . '</a>';
				} else {
					$post_id = tcp_get_the_parent( $order_detail->get_post_id() );
					if ( $post_id > 0 ) {
						$det .= '<a href="' . get_permalink( tcp_get_current_id( $post_id, get_post_type( $post_id ) ) ) . '">' . $name . '</a>';
					} else {
						$det .= '<a href="' . get_permalink( tcp_get_current_id( $order_detail->get_post_id(), get_post_type( $order_detail->get_post_id() ) ) ) . '">' . $name . '</a>';
					}
				}

				$det .= '</td>' . "\n";
				$det .= '<td class="tcp_cart_price">' . tcp_format_the_price( $order_detail->get_price() );
				if ( $order_detail->get_discount() > 0 ) 
					$det .= '&nbsp;<span class="tcp_cart_discount">' . sprintf( __( 'Discount %s', 'tcp' ), tcp_format_the_price( $order_detail->get_discount() ) ) . '</span>';
				$det .= '</td>' . "\n";
				$det .= '<td class="tcp_cart_units">';
				if ( ! $source->is_editing_units() ) {
					$det .= tcp_number_format( $order_detail->get_qty_ordered(), 0 );
				} else {
					$det .= '<form method="post">' . "\n";
					$det .= '<input type="hidden" name="tcp_post_id" id="tcp_post_id" value="' . $order_detail->get_post_id() . '" />' . "\n";
					$det .= '<input type="hidden" name="tcp_option_1_id" id="tcp_option_1_id" value="' . $order_detail->get_option_1_id() . '" />' . "\n";
					$det .= '<input type="hidden" name="tcp_option_2_id" id="tcp_option_2_id" value="' . $order_detail->get_option_2_id() . '" />' . "\n";
					if ( ! tcp_is_downloadable( $order_detail->get_post_id() ) ) {
						$det .= '<input name="tcp_count" id="tcp_count" value="' . $order_detail->get_qty_ordered() . '" size="2" maxlength="4" type="text" />' . "\n";
						$det .= '<input name="tcp_modify_item_shopping_cart" value="' . __( 'Modify', 'tcp' ) . '" type="submit" />' . "\n";
					} else {
						$det .= '1&nbsp;' . "\n";
					}
					$det .= '<input name="tcp_delete_item_shopping_cart" value="' . __( 'Delete', 'tcp' ) . '" type="submit" />' . "\n";
					if ( $stock_management ) {
						$stock = tcp_get_the_stock( $order_detail->get_post_id(), $order_detail->get_option_1_id(), $order_detail->get_option_2_id() );
						if ( $stock == 0 ) {
							$det .= '<span class="tcp_no_stock">' . __( 'Out of stock', 'tcp' ) . '</span>' . "\n";
						} elseif ( $stock != -1 && $stock < $order_detail->get_qty_ordered() ) {
							$det .= '<span class="tcp_no_stock_enough">' . sprintf( __( 'No enough stock. Only %s items available.', 'tcp' ), $stock ) . '</span>';
						}
					}
					$det .= '</form>';
				}
				$det .= '</td>' . "\n";
				if ( $source->see_full() ) $det .= '<td class="tcp_cart_sku">' . $order_detail->get_sku() . '</td>' . "\n";
				if ( $source->see_full() )$det .= '<td class="tcp_cart_weight">' . tcp_number_format( $order_detail->get_weight(), 0 ). '&nbsp;' . tcp_get_the_unit_weight() . '</td>' . "\n";
				$price = $order_detail->get_price() * $order_detail->get_qty_ordered() - $order_detail->get_discount();
				$tax = round( $order_detail->get_price() * ( $order_detail->get_tax() / 100 ), tcp_get_decimal_currency() ) * $order_detail->get_qty_ordered();
				$total_tax += $tax;
				$total += $price;
				$det .= '<td class="tcp_cart_total">' . tcp_format_the_price( $price ) . '</td>' . "\n";
				$det .= '</tr>' . "\n";
			}
			$out .= $det;
			$det = '';
		}

		$out .= '<tr class="tcp_cart_subtotal_row">' . "\n";
		if ( $source->see_full() ) $out .= '<td colspan="6"';
		else $out .= '<td colspan="3"';
		$out .= ' class="tcp_cart_subtotal_title">' . __( 'Subtotal', 'tcp' ) . '</td>' . "\n";
		$out .= '<td class="tcp_cart_subtotal">' . tcp_format_the_price( $total ) . '</td>' . "\n";
		$out .= '</tr>' . "\n";

		$discount = $source->get_discount();
		if ( $discount > 0 ) {
			$dis = '<tr id="discount" class="tcp_cart_discount_row';
			if ( $i++ & 1 == 1 ) $dis .= ' tcp_par';
			$dis .= '">' . "\n";
			if ( $source->see_full() ) $dis .= '<td colspan="6"';
			else $dis .= '<td colspan="3"';
			$dis .= ' class="tcp_cart_discount_title">' . __( 'Discount', 'tcp' ) . '</td>' . "\n";
			$dis .= '<td class="tcp_cart_discount">' . tcp_format_the_price( $discount ) . '</td>' . "\n";
			$dis .= '</tr>' . "\n";
			$out .= $dis;
			$dis = '';
			$total = $total - $discount;
		}
		if ( $source->see_other_costs() ) {
			if ( $source->has_orders_costs() ) {
				$cost = '';
				foreach( $source->get_orders_costs() as $order_cost ) {
					$cost .= '<tr id="other_costs" class="tcp_cart_other_costs_row">' . "\n";
					if ( $source->see_full() ) $cost .= '<td colspan="6"';
					else $cost .= '<td colspan="3"';
					$cost .= ' class="tcp_cart_other_costs_title">' . $order_cost->get_description() . '</td>' . "\n";
					$cost .= '<td class="tcp_cart_other_costs">' . tcp_format_the_price( $order_cost->get_cost() ) . '</td>' . "\n";
					$tax = $order_cost->get_cost() * ( $order_cost->get_tax() / 100 );
					$total_tax += $tax;
					$total += $order_cost->get_cost();
					$cost .= '</tr>' . "\n";
				}
				$out .= $cost;
			}
		}
		if ( $source->see_tax_summary() && $total_tax > 0 ) {
			$out_tax = '<tr class="tcp_cart_tax_row">' . "\n";
			if ( $source->see_full() ) $out_tax .= '<td colspan="6"';
			else $out_tax .= '<td colspan="3"';
			$out_tax .= 'class="tcp_cart_tax_title">' . __( 'Taxes', 'tcp' ) . '</td>' . "\n";
			$out_tax .= '<td class="tcp_cart_tax">' . tcp_format_the_price( $total_tax ) . '</td>' . "\n";
			$out_tax .= '</tr>';
			$out .= $out_tax;
			$out_tax = '';
		} else {
			$total_tax = 0;
		}

		$total += $total_tax;
		$out .= '<tr class="tcp_cart_total_row">' . "\n";
		if ( $source->see_full() ) $out .= '<td colspan="6"';
		else $out .= '<td colspan="3"';
		$out .= 'class="tcp_cart_total_title">' . __( 'Total', 'tcp' ) . '</td>' . "\n";
		$out .= '<td class="tcp_cart_total">' . tcp_format_the_price( $total ) . '</td>' . "\n";
		$out .= '</tr>';
		$out .= '</tbody></table>' . "\n";

		if ( $source->see_comment() && strlen( $source->get_comment() ) > 0 ) $out .= '<p>' . $source->get_comment() . '</p>';
		if ( $echo ) echo $out;
		else return $out;
	}

}
?>
