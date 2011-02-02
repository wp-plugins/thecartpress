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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
require_once( 'MP3Player.class.php' );

$tcp_post = false;

class BuyButton {
	static function show( $post_id = 0, $echo = true ) {
		$settings = get_option( 'tcp_settings' );
		$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';
		$stock_management = isset( $settings['stock_management'] ) ? (bool)$settings['stock_management'] : false;
		$disable_shopping_cart = isset( $settings['disable_shopping_cart'] ) ? (bool)$settings['disable_shopping_cart'] : false;
		if ( $post_id == 0 ) $post_id = get_the_ID();
		$out  = '<div class="tcp_buy_button">' . "\n";
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( tcp_is_downloadable( $post_id ) &&  $shoppingCart->exists( $post_id ) ) {
			$out .= '<div class="tcp_already_in_cart">' . "\n";
			$out .= __( 'The product is in the cart', 'tcp' ) . "\n";
			$out .= MP3Player::showPlayer( $post_id, MP3Player::$SMALL, false );
			$out .= '</div>' . "\n";
		} elseif ( tcp_get_the_product_type( $post_id ) == 'SIMPLE' ) {				
			$price = tcp_get_the_price( $post_id );
			$tax = tcp_get_the_tax( $post_id );
			$out .= '<script type="text/javascript">
			function add_to_the_cart_' . $post_id . '() {
				var count = jQuery("#tcp_count_' . $post_id .'").val();
				if (count == 0) count = 1;
				jQuery("#tcp_count_' . $post_id . '").val(count);
				jQuery("#tcp_frm_' . $post_id . '").submit();
			}
			</script>' . "\n";
			$out .=	'<form method="post" id="tcp_frm_' . $post_id . '">' . "\n";
			$out .= '<input type="hidden" name="tcp_post_id[]" id="tcp_post_id" value="' . $post_id . '" />' . "\n";
			$out .= '<input type="hidden" name="tcp_unit_price[]" id="tcp_unit_price" value="' . $price . '" />' . "\n";
			$out .= '<input type="hidden" name="tcp_tax[]" id="tcp_tax" value="' . $tax . '" />' . "\n";
			$out .= '<input type="hidden" name="tcp_unit_weight[]" id="tcp_unit_weight" value="' . tcp_get_the_weight( $post_id ) . '" />' . "\n";
			$out .= '<table class="tcp_buy_button"><tbody>' . "\n";
			$out .= '<tr>';
			$out .= '<th>' . __('Price', 'tcp') . '</th>' . "\n";
			if ( ! $disable_shopping_cart )
				$out .= '<th>' . __('Units', 'tcp') . '</th>' . "\n";
			$out .= '</tr><tr>' . "\n";
			$out .= '<td class="tcp_buy_button_price">' . "\n";
			global $tcp_post;
			$tcp_post = get_post( $post_id );
			$options_select = apply_filters( 'tcp_buy_button_options', '' );
			if ( strlen( $options_select ) > 0 )
				$out .= $options_select;
			else {
				$out .= '<input type="hidden" name="tcp_option_1_id[]" id="tcp_option_1_id_' . $post_id . '" value="0" />' . "\n";
				$out .= '<input type="hidden" name="tcp_option_2_id[]" id="tcp_option_2_id_' . $post_id . '" value="0" />' . "\n";
				$out .= $price . '&nbsp;' . $currency;
				$tax_label = tcp_get_the_tax_label( $post_id );
				if ( strlen( $tax_label ) > 0 ) $out .= '&nbsp;(' . $tax_label . ')';
			}
			$out .= '</td>' . "\n";
			if ( ! $disable_shopping_cart ) {
				$out .= '<td class="tcp_buy_button_units">';
				if ( tcp_is_downloadable( $post_id ) ) {
					$out .= MP3Player::showPlayer( $post_id, MP3Player::$SMALL, false );
					$out .= '<input type="hidden" name="tcp_count[]" id="tcp_count_' . $post_id . '" value="1" />';
				} else {
					if ( $stock_management && tcp_get_the_stock( $post_id ) == 0 ) {
						$out .= '<span class="tcp_no_stock">' . __( 'No stock for this product', 'tcp' ) . '</span>';
					} else {
						$out .= '<input type="text" name="tcp_count[]" id="tcp_count_' . $post_id . '" value="1" size="2" maxlength="3"/>';
						$item = $shoppingCart->getItem( $post_id );
						if ( $item )
							$out .='<span class="tcp_added_product_title">' . $item->getCount() . ' ' . __( 'units', 'tcp' ) . ' <a href="' . get_permalink( get_option( 'tcp_shopping_cart_page_id' ) ) . '">' . __( 'in your cart', 'tcp' ) . '</a><span>';
					}
				}
				if ( tcp_is_downloadable( $post_id ) || ! $stock_management || tcp_get_the_stock( $post_id ) != 0 ) {
					$html = '<input type="submit" name="tcp_add_to_shopping_cart" id="tcp_add_row" value="' . __( 'Add', 'tcp' ) . '"/>' . "\n";
					$out .= apply_filters( 'tcp_buy_button_add_button', $html, $post_id );
				}
				$out .= '</td>' . "\n";
			}
			$out .= '</tr>' . "\n";
			$out .= '</tbody></table>' . "\n";
			//$out .= '<input type="submit" name="tcp_add_to_shopping_cart" id="tcp_add_to_shopping_cart" value="' . __( 'Add selected to the cart', 'tcp' ) . '" />' . "\n";
			$out .= '</form>' . "\n";
		} else { // if ( tcp_get_the_product_type() == 'GROUPED' ) {
			$out .= '<script type="text/javascript">
				function add_to_the_cart_' . $post_id . '(id_to, id_from) {
					var count = jQuery("#tcp_count_" + id_to).val();
					if (count == 0) count = 1;
					jQuery(".tcp_count").each(function(i) {
						jQuery(this).val(0);
					});
					jQuery("#tcp_count_" + id_to).val(count);
					jQuery("#tcp_add_to_shopping_cart_" + id_from).click();
				}
				</script>' . "\n";
			$out .=	'<form method="post" id="tcp_buy_button_form_' . $post_id . '">' . "\n";
			$out .= '<table class="tcp_buy_button"><tbody>' . "\n";
			$out .= '<tr>';
			$out .= '<th>' . __('Name', 'tcp') . '</th>' . "\n";
			$out .= '<th>' . __('Price', 'tcp') . '</th>' . "\n";
			if ( ! $disable_shopping_cart ) {
				$out .= '<th>' . __('Units', 'tcp') . '</th>' . "\n";
			}
			$out .= '</tr>' . "\n";
			$products = RelEntities::select( get_the_ID() );
			$ids = array();
			foreach( $products as $product ) {
				$price = tcp_get_the_price( $product->id_to );
				$tax = tcp_get_the_tax_label( $product->id_to );
				$is_downloadable = tcp_is_downloadable( $product->id_to );
				$out .= '<tr>' . "\n";
				$out .= '<input type="hidden" name="tcp_post_id[]" id="tcp_post_id" value="' . $product->id_to . '" />' . "\n";
				$out .= '<input type="hidden" name="tcp_unit_price[]" id="tcp_unit_price" value="' . $price . '" />' . "\n";
				$out .= '<input type="hidden" name="tcp_tax[]" id="tcp_tax" value="' . $tax . '" />' . "\n";
				$out .= '<input type="hidden" name="tcp_unit_weight[]" id="tcp_unit_weight" value="' . tcp_get_the_weight( $product->id_to ) . '" />' . "\n";
				global $tcp_post;
				$tcp_post = get_post( $product->id_to );
				$options_select = apply_filters( 'tcp_buy_button_options', '' );
				if ( strlen( $options_select ) > 0 ) {
					$out .= '<td class="tcp_buy_button_name" colspan="2">' . $options_select . '</td>';
				} else {
					$out .= '<td class="tcp_buy_button_name"><input type="hidden" name="tcp_option_1_id[]" id="tcp_option_1_id_' . $product->id_to. '" value="0" />' . "\n";
					$out .= '<input type="hidden" name="tcp_option_2_id[]" id="tcp_option_2_id_' . $product->id_to. '" value="0" />' . "\n";
					if ( $is_downloadable )
						$out .= MP3Player::showPlayer( $product->id_to, MP3Player::$SMALL, false );
					$out .= get_the_title( $product->id_to );
					$out .= '</td>' . "\n";
					$out .= '<td class="tcp_buy_button_price">' . $price . '&nbsp;' . $currency . '&nbsp;(' . $tax . ')</td>' . "\n";
				}
				if ( ! $disable_shopping_cart ) {
					$out .= '<td class="tcp_buy_button_count">';
					if ( $is_downloadable )
						$out .= '<input type="hidden" name="tcp_count[]" class="tcp_count" id="tcp_count_' . $product->id_to . '" value="1"/>' . "\n";
					elseif ( ! $stock_management || tcp_get_the_stock( $product->id_to ) != 0 )
						$out .= '<input type="text" name="tcp_count[]" class="tcp_count" id="tcp_count_' . $product->id_to . '" value="0" size="2" maxlength="3"/>' . "\n";					
					if ( ! $is_downloadable || ( $is_downloadable && ! $shoppingCart->exists( $product->id_to ) ) ) {
						if ( ! $stock_management || tcp_get_the_stock( $product->id_to ) != 0 ) {
							$html = '<input type="button" onclick="add_to_the_cart_' . $product->id_from . '(' . $product->id_to . ', ' . $product->id_from . ');" name="tcp_add_row" id="tcp_add_row" value="' . __( 'Add', 'tcp' ) . '"/>' . "\n";
							$out .= apply_filters( 'tcp_buy_button_add_button', $html, $product->id_to );
						} else {
							$out .= '<span class="tcp_no_stock">' . __( 'No stock for this product', 'tcp' ) . '</span>';
						}
					}
					$item = $shoppingCart->getItem( $product->id_to );
					if ( $item )
						$out .='<span class="tcp_added_product_title">' . $item->getCount() . ' ' . __( 'units', 'tcp' ) . ' <a href="'.get_permalink( get_option( 'tcp_shopping_cart_page_id' ) ) . '">' . __( 'in your cart', 'tcp' ) . '</a><span>';
					$out .= '</td>' . "\n";
				}
				$out .= '</tr>' . "\n";
			}
			$out .= '</tbody></table>' . "\n";
			$out .= '<input type="submit" name="tcp_add_to_shopping_cart" id="tcp_add_to_shopping_cart_' . $post_id . '" value="' .__( 'Add selected to the cart', 'tcp' ) . '" />' . "\n";
			$out .= '</form>' . "\n";
		}
		$out .= '</div>' . "\n";
		if ( $echo )
			echo $out;
		else
			return $out;
	}
}
?>
