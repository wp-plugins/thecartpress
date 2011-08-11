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

class BuyButton {
	static function show( $post_id = 0, $echo = true ) {
		global $thecartpress;
		$stock_management		= isset( $thecartpress->settings['stock_management'] ) ? (bool)$thecartpress->settings['stock_management'] : false;
		$disable_shopping_cart	= isset( $thecartpress->settings['disable_shopping_cart'] ) ? (bool)$thecartpress->settings['disable_shopping_cart'] : false;
		$after_add_to_cart		= isset( $thecartpress->settings['after_add_to_cart'] ) ? $thecartpress->settings['after_add_to_cart'] : '';
		$enabled_wish_list		= isset( $thecartpress->settings['enabled_wish_list'] ) ? $thecartpress->settings['enabled_wish_list'] : '';

		if ( $after_add_to_cart == 'ssc' ) {
			$action = get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id', 0 ), 'page' ) );
		} else {
			$action = '';
		}
		if ( $post_id == 0 ) $post_id = tcp_get_default_id ( get_the_ID(), get_post_type( get_the_ID() ) );
		$out  = '<div class="tcp_buy_button">' . "\n";
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( tcp_is_downloadable( $post_id ) &&  $shoppingCart->exists( $post_id ) ) {
			$out .= '<div class="tcp_already_in_cart">' . "\n";
			$out .= sprintf( __( 'The product is in your <a href="%s">cart</a>' ,'tcp' ) , tcp_get_the_shopping_cart_url() ) . "\n";
			$out .= MP3Player::showPlayer( $post_id, MP3Player::$SMALL, false );
			$out .= '</div>' . "\n";
		} elseif ( tcp_get_the_product_type( $post_id ) == 'SIMPLE' ) {				
			$price = tcp_get_the_price_with_tax( $post_id );
			$tax = tcp_get_the_tax( $post_id );
			$out .= '<script type="text/javascript">
			function add_to_the_cart_' . $post_id . '() {
				var count = jQuery("#tcp_count_' . $post_id .'").val();
				if (count == 0) count = 1;
				jQuery("#tcp_count_' . $post_id . '").val(count);
				jQuery("#tcp_frm_' . $post_id . '").submit();
			}
			</script>' . "\n";
			$out .=	'<form method="post" id="tcp_frm_' . $post_id . '" action="' . $action . '">' . "\n";
			$out .= '<input type="hidden" name="tcp_post_id[]" id="tcp_post_id" value="' . $post_id . '" />' . "\n";
			$out .= '<input type="hidden" name="tcp_unit_price[]" id="tcp_unit_price" value="' . $price . '" />' . "\n";
			$out .= '<input type="hidden" name="tcp_tax[]" id="tcp_tax" value="' . $tax . '" />' . "\n";
			$out .= '<input type="hidden" name="tcp_unit_weight[]" id="tcp_unit_weight" value="' . tcp_get_the_weight( $post_id ) . '" />' . "\n";
			$out .= '<table class="tcp_buy_button"><tbody>' . "\n";
			$out .= '<tr>';
			$out .= '<th>' . __( 'Price', 'tcp' ) . '</th>' . "\n";
			if ( ! $disable_shopping_cart ) {
				$out .= '<th>';
				if ( ! tcp_is_downloadable( $post_id ) ) {
					$out .= __( 'Units', 'tcp' );
				} else {
					$out .= '&nbsp;';
				}
				$out .= '</th>' . "\n";
			} elseif ( $enabled_wish_list && ! $shoppingCart->isInWishList( $post_id ) ) {
				$out .= '<th>&nbsp;</th>' . "\n";
			}
			$out .= '</tr>';
			$out .= '<tr';
			$classes = apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id );
			if ( $stock_management && tcp_get_the_stock( $post_id ) == 0 ) $classes[] = 'tcp_out_of_stock';
			if ( is_array( $classes ) && count( $classes ) > 0 ) {
				$tr_classes = ' class="';
				foreach( $classes as $class ) {
					$tr_classes .= $class . ' ';
				}
				$tr_classes .= '"';
			} else {
				$tr_classes = '';
			}
			$out .= $tr_classes . '>' . "\n";
			$out .= '<td class="tcp_buy_button_price">' . "\n";

			$html = '<input type="hidden" name="tcp_option_1_id[]" id="tcp_option_1_id_' . $post_id . '" value="0" />' . "\n";
			$html .= '<input type="hidden" name="tcp_option_2_id[]" id="tcp_option_2_id_' . $post_id . '" value="0" />' . "\n";
			$html .= '<span class="tcp_price">' . tcp_get_the_price_label( $post_id ) . '</span>';
			$out .= apply_filters( 'tcp_buy_button_options', $html, $post_id );
			$out .= '</td>' . "\n";
			if ( $enabled_wish_list && ! $shoppingCart->isInWishList( $post_id ) ) {
				$html = '<input type="submit" name="tcp_add_to_wish_list" id="tcp_add_wish_list_' . $post_id . '" value="' . __( 'Add to Wish list', 'tcp' ) . '"';
				$html .= ' onclick="jQuery(\'#tcp_new_wish_list_item\').val(' . $post_id . ');jQuery(\'#tcp_frm_' . $post_id . '\').attr(\'action\', \'\');" />' . "\n";
				$wishlist = apply_filters( 'tcp_buy_button_add_to_wish_list', $html, $post_id );
			} else {
				$wishlist = '';
			}
			if ( ! $disable_shopping_cart ) {
				$out .= '<td class="tcp_buy_button_units">';
				if ( tcp_is_downloadable( $post_id ) ) {
					$html = MP3Player::showPlayer( $post_id, MP3Player::$SMALL, false );
					$html .= '<input type="hidden" name="tcp_count[]" id="tcp_count_' . $post_id . '" value="1" />';
					$out .= apply_filters( 'tcp_buy_button_unit_text', $html, $post_id );
				} else {
					if ( $stock_management && tcp_get_the_stock( $post_id ) == 0 ) {
						$out .= '<span class="tcp_no_stock">' . __( 'No stock for this product', 'tcp' ) . '</span>';
					} else {
						$html = '<input type="text" name="tcp_count[]" id="tcp_count_' . $post_id . '" value="1" size="2" maxlength="3"/>';
						$out .= apply_filters( 'tcp_buy_button_unit_text', $html, $post_id );
					}
				}
				if ( tcp_is_downloadable( $post_id ) || ! $stock_management || tcp_get_the_stock( $post_id ) != 0 ) {
					if ( ! tcp_hide_buy_button( $post_id ) ) {
						$html = '<input type="submit" name="tcp_add_to_shopping_cart" id="tcp_add_product_' . $post_id . '" value="' . __( 'Add', 'tcp' ) . '"/>' . "\n";
					} else {
						$html = '';
					}
					$out .= apply_filters( 'tcp_buy_button_add_button', $html, $post_id );
				}
				$out .= $wishlist;
				$item = $shoppingCart->getItem( tcp_get_default_id( $post_id, get_post_type( $post_id ) ) );
				if ( $item ) {
					$html ='<span class="tcp_added_product_title">' . sprintf ( __( '%s unit(s) <a href="%s">in your cart</a>', 'tcp' ), $item->getCount(), tcp_get_the_shopping_cart_url() ) . '<span>';
					$out .= apply_filters( 'tcp_buy_button_units_in_cart', $html, $post_id );
				}
				$out .= '</td>' . "\n";
			} elseif ( strlen( $wishlist ) > 0 ) {
				$out .= '<td class="tcp_buy_button_units">';
				$out .= $wishlist;
				$out .= '</td>' . "\n";
			}
			$out .= '</tr>' . "\n";
			$out .= '</tbody></table>' . "\n";
			$out .= '<input type="hidden" value="" name="tcp_new_wish_list_item" id="tcp_new_wish_list_item" />';
			$out .= '</form>' . "\n";
		} else { // if ( tcp_get_the_product_type() == 'GROUPED' ) {
			$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
			$out .= '<script type="text/javascript">
				function add_to_the_cart_' . $post_id . '(id_to) {
					var count = jQuery("#tcp_count_' . $post_id .'_" + id_to).val();
					if (count == 0) count = 1;
					jQuery(".tcp_count").each(function(i) {
						jQuery(this).val(0);
					});
					jQuery("#tcp_count_' . $post_id .'_" + id_to).val(count);
					//jQuery("#tcp_add_to_shopping_cart_' . $post_id . '").click();
					jQuery("#tcp_buy_button_form_' . $post_id . '").submit();			
				}
				</script>' . "\n";
			$out .=	'<form method="post" id="tcp_buy_button_form_' . $post_id . '" action="' . $action . '">' . "\n";
			$out .= '<table class="tcp_buy_button"><tbody>' . "\n";
			$out .= '<tr>';
			$out .= '<th>' . __('Name', 'tcp') . '</th>' . "\n";
			$out .= '<th>' . __('Price', 'tcp') . '</th>' . "\n";
			if ( ! $disable_shopping_cart ) {
				$out .= '<th>' . __('Units', 'tcp') . '</th>' . "\n";
			} else
			$out .= '</tr>' . "\n";
			$products = RelEntities::select( $post_id );
			foreach( $products as $product ) {
				$product_id = tcp_get_current_id( $product->id_to, get_post_type( $product->id_to ) );
				if ( get_post_status( $product_id ) == 'publish' ) {
					$tcp_exclude_range = get_post_meta( $product_id, 'tcp_exclude_range', true );
					$price	= tcp_get_the_price_with_tax( $product_id );
					$tax	= tcp_get_the_tax( $product_id );
					$stock	= tcp_get_the_stock( $product_id );
					$is_downloadable = tcp_is_downloadable( $product_id );
					$out .= '<tr';
					$classes = apply_filters( 'tcp_buy_button_get_product_classes', array(), $product_id );
					if ( $stock_management && tcp_get_the_stock( $product_id ) == 0 ) $classes[] = 'tcp_out_of_stock';
					if ( is_array( $classes ) && count( $classes ) > 0 ) {
						$tr_classes = ' class="';
						foreach( $classes as $class ) {
							$tr_classes .= $class . ' ';
						}
						$tr_classes .= '"';
					} else {
						$tr_classes = '';
					}
					$out .= $tr_classes . '>' . "\n";
					$out .= '<input type="hidden" name="tcp_post_id[]" id="tcp_post_id" value="' . $product_id . '" />' . "\n";
					$out .= '<input type="hidden" name="tcp_unit_price[]" id="tcp_unit_price" value="' . $price . '" />' . "\n";
					$out .= '<input type="hidden" name="tcp_tax[]" id="tcp_tax" value="' . $tax . '" />' . "\n";
					$out .= '<input type="hidden" name="tcp_unit_weight[]" id="tcp_unit_weight" value="' . tcp_get_the_weight( $product_id ) . '" />' . "\n";
					$out .= '<td class="tcp_buy_button_name">';
					if ( $is_downloadable )
						$out .= MP3Player::showPlayer( $product->id_to, MP3Player::$SMALL, false );
					$out .= get_the_title( $product_id );
					$out .= '</td>' . "\n";
					$out .= '<td class="tcp_buy_button_price">';
					$html = '<span class="tcp_price">' . tcp_get_the_price_label( $product_id ) . '</span>';
					$html .= '<input type="hidden" name="tcp_option_id[]" id="tcp_option_id_' . $product_id. '" value="0" />' . "\n";
					$html .= '<input type="hidden" name="tcp_option_1_id[]" id="tcp_option_1_id_' . $product_id. '" value="0" />' . "\n";
					$html .= '<input type="hidden" name="tcp_option_2_id[]" id="tcp_option_2_id_' . $product_id . '" value="0" />' . "\n";
					$out .= apply_filters( 'tcp_buy_button_options', $html, $product_id, $post_id );
					$out .= '</td>' . "\n";
					if ( ! $disable_shopping_cart ) {
						$out .= '<td class="tcp_buy_button_count">';
						if ( $is_downloadable ) {
							$html = '<input type="hidden" name="tcp_count[]" class="tcp_count" id="tcp_count_' . $post_id . '_' . $product_id . '" value="1"/>' . "\n";
							$out .= apply_filters( 'tcp_buy_button_unit_text', $html, $product_id, $post_id );
						} elseif ( ! $stock_management || $stock != 0 ) {
							$html = '<input type="text" name="tcp_count[]" class="tcp_count" id="tcp_count_' . $post_id . '_' . $product_id . '" value="0" size="2" maxlength="3"/>' . "\n";					
							$out .= apply_filters( 'tcp_buy_button_unit_text', $html, $product_id, $post_id );
						}
						if ( ! $is_downloadable || ( $is_downloadable && ! $shoppingCart->exists( $product_id ) ) ) {
							if ( ! $stock_management || $stock != 0 ) {
								if ( ! tcp_hide_buy_button( $product_id ) ) {
									$html = '<input type="button" onclick="add_to_the_cart_' . $product->id_from . '(' . $product_id . ');" name="tcp_add_row" id="tcp_add_row" value="' . __( 'Add', 'tcp' ) . '"/>' . "\n";
								} else {
									$html = '';
								}
								$out .= apply_filters( 'tcp_buy_button_add_button', $html, $product_id );
							} else {
								$out .= '<span class="tcp_no_stock">' . __( 'No stock for this product', 'tcp' ) . '</span>';
							}
						}
						if ( $enabled_wish_list && tcp_is_visible( $product_id ) && ! $shoppingCart->isInWishList( $product_id ) ) {
							$html = '<input type="submit" name="tcp_add_to_wish_list" id="tcp_add_wish_list_' . $product_id . '" value="' . __( 'Add to Wish list', 'tcp' ) . '"';
							$html .= ' onclick="jQuery(\'#tcp_new_wish_list_item\').val(' . $product_id . ');" />' . "\n";
							$out .= apply_filters( 'tcp_buy_button_add_to_wish_list', $html, $product_id );
						}
						$item = $shoppingCart->getItem( tcp_get_default_id( $product_id, get_post_type( $product_id ) ) );
						if ( $item ) {
							$html ='<span class="tcp_added_product_title">' . sprintf ( __( '%s unit(s) <a href="%s">in your cart</a>', 'tcp' ), $item->getCount(), tcp_get_the_shopping_cart_url() ) . '<span>';
							$out .= apply_filters( 'tcp_buy_button_units_in_cart', $html, $post_id );
						}
						$out .= '</td>' . "\n";
					}
					$out .= '</tr>' . "\n";
				}
			}
			$out .= '</tbody></table>' . "\n";
			if ( ! tcp_hide_buy_button( $post_id ) ) {
				$html = '<input type="submit" name="tcp_add_to_shopping_cart_button" class="tcp_add_to_shopping_cart" id="tcp_add_to_shopping_cart_' . $post_id . '" value="' .__( 'Add selected to the cart', 'tcp' ) . '" />' . "\n";
				$out .= apply_filters( 'tcp_buy_button_add_to_shopping_cart', $html, $post_id );
			}
			$out .= '<input type="hidden" name="tcp_add_to_shopping_cart" value="y" />' . "\n";
			if ( $enabled_wish_list && ! $shoppingCart->isInWishList( $post_id ) ) {
				$html = '<input type="submit" name="tcp_add_to_wish_list" id="tcp_add_wish_list' . $post_id . '" value="' . __( 'Add to Wish list', 'tcp' ) . '"';
				$html .= ' onclick="jQuery(\'#tcp_new_wish_list_item\').val(' . $post_id . ');" />' . "\n";
				$out .= apply_filters( 'tcp_buy_button_add_to_wish_list', $html, $post_id );
			}
			$out .= '<input type="hidden" value="" name="tcp_new_wish_list_item" id="tcp_new_wish_list_item" />';
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
