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

//Multilingua support: WPML or Qtranslate
function tcp_get_admin_language_iso() {
	if ( strlen( WPLANG ) > 0 ) {
		$lang_country = explode ( '_', WPLANG );
		if ( is_array( $lang_country ) && count( $lang_country ) > 0 ) {
			return $lang_country[0];
		} else {
			return '';
		}
	} else {
		return '';
	}
}

global $sitepress;
if ( $sitepress ) {
	include_once( dirname( __FILE__ ) . '/tcp_wpml_template.php' );
} else {
	include_once( dirname( __FILE__ ) . '/tcp_qt_template.php' );
}
//End Multilingua support


function tcp_the_currency( $echo = true ) {
	global $thecartpress;
	$currency = isset( $thecartpress->settings['currency'] ) ? $thecartpress->settings['currency'] : 'EUR';
	$currency = apply_filters( 'tcp_the_currency', $currency );
	if ( $echo )
		echo $currency;
	else
		return $currency;
}

function tcp_get_the_currency() {
	return tcp_the_currency( false );
}

function tcp_get_the_currency_iso() {
	return tcp_the_currency_iso( false );
}

function tcp_the_currency_iso( $echo = true ) {
	global $thecartpress;
	$currency = isset( $thecartpress->settings['currency'] ) ? $thecartpress->settings['currency'] : 'EUR';
	$currency = apply_filters( 'tcp_the_currency_iso', $currency );
	if ( $echo )
		echo $currency;
	else
		return $currency;
}

function tcp_the_unit_weight( $echo = true ) {
	global $thecartpress;
	$unit_weight = isset( $thecartpress->settings['unit_weight'] ) ? $thecartpress->settings['unit_weight'] : 'gr';
	$unit_weight = apply_filters( 'tcp_the_unit_weight', $unit_weight );
	if ( $echo )
		echo $unit_weight;
	else
		return $unit_weight;
}

function tcp_get_the_unit_weight( $echo = true ) {
	return tcp_the_unit_weight( false );
}

function tcp_get_default_currency(  ) {
	global $thecartpress;
	return isset( $thecartpress->settings['currency'] ) ? $thecartpress->settings['currency'] : '';
}

function tcp_the_buy_button( $post_id = 0, $echo = true ) {
	BuyButton::show( $post_id, $echo );
}

function tcp_the_order_panel() {
	OrderPanel::show();
}

function tcp_the_price( $before = '', $after = '', $echo = true ) {
	//$price = tcp_the_meta( 'tcp_price', $before, $after, false );
	$price = tcp_number_format( tcp_get_the_price() );
	$price = $before . $price . $after;
	$price = apply_filters( 'tcp_the_price', $price );
	if ( $echo )
		echo $price;
	else
		return $price;
}

function tcp_get_the_price( $post_id = 0 ) {
	$price = (float)tcp_get_the_meta( 'tcp_price', $post_id );
	$price = apply_filters( 'tcp_get_the_price', $price, $post_id );
	return $price;
}

function tcp_the_price_label( $before = '', $after = '', $echo = true ) {
	$price = tcp_get_the_price_label();
	if ( strlen( $price ) == 0 ) {
		//$price = apply_filters( 'tcp_the_price_label', '' );
		return '';
	}
	$price = $before . $price . $after;
	$price = apply_filters( 'tcp_the_price_label', $price );
	if ( $echo )
		echo $price;
	else
		return $price;
}

function tcp_get_the_price_label( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$type = tcp_get_the_meta( 'tcp_type', $post_id );
	if ( $type == 'GROUPED' ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
		$products = RelEntities::select( $post_id, $type );
		if ( is_array( $products ) && count( $products ) > 0 ) {
			$min = (float)tcp_get_the_price( $products[0]->id_to );
			$max = $min;
			foreach( $products as $product ) {
				$price = (float)tcp_get_the_price ( $product->id_to );
				if ( $price < $min ) $min = $price;
				if ( $price > $max ) $max = $price;
			}
			if ( $min != $max ) {
				$price = tcp_number_format( $min ) . __( ' to ', 'tcp' ) . tcp_number_format( $max );
				return apply_filters( 'tcp_get_the_price_label', $price );
			} else {
				$price = tcp_number_format( $min );
			}
		} else {
			$price = 0;
		}
	} else {
		$price = tcp_number_format( tcp_get_the_price( $post_id ) );
	}
	$price = apply_filters( 'tcp_get_the_price_label', $price, $post_id );
	return $price;
}

function tcp_the_tax_id( $before = '', $after = '', $echo = true ) {
	$tax = tcp_the_meta( 'tcp_tax_id', $before, $after, false );
	$tax = apply_filters( 'tcp_the_tax_id', $tax );
	if ( $echo )
		echo $tax;
	else
		return $tax;
}

function tcp_get_the_tax_id( $post_id = 0 ) {
	$tax = tcp_get_the_meta( 'tcp_tax_id', $post_id );
	return apply_filters( 'tcp_get_the_tax_id', $tax, $post_id );
}

function tcp_the_tax( $before = '', $after = '', $echo = true ) {
	$tax = tcp_number_format( tcp_get_the_tax() );
	$tax = $before . $tax . $after;
	$tax = apply_filters( 'tcp_the_weight', $tax );
	if ( $echo )
		echo $tax;
	else
		return $tax;
}

function tcp_get_the_tax( $post_id = 0 ) {
	$tax = (float)tcp_get_the_meta( 'tcp_tax', $post_id );
	return apply_filters( 'tcp_get_the_tax', $tax, $post_id );
}

function tcp_the_tax_label( $before = '', $after = '', $echo = true ) {
	$tax = tcp_the_meta( 'tcp_tax_label', $before, $after, false );
	$tax = apply_filters( 'tcp_the_tax_label', $tax );
	if ( $echo )
		echo $tax;
	else
		return $tax;
}

function tcp_get_the_tax_label( $post_id = 0 ) {
	$tax = tcp_get_the_meta( 'tcp_tax_label', $post_id );
	$tax = apply_filters( 'tcp_get_the_tax_label', $tax, $post_id );
	return $tax;
}

function tcp_the_price_tax( $before = '', $after = '', $echo = true ) {
	$price = tcp_get_the_price_tax();
	if ( strlen( $price ) == 0 ) return;
	else $price = tcp_number_format( $price );
	$price = $before . $price . $after;
	$price = apply_filters( 'tcp_the_price_tax', $price );
	if ( $echo )
		echo $price;
	else
		return $price;
}

function tcp_get_the_price_tax( $post_id = 0 ) {
	$price = tcp_get_the_meta( 'tcp_price', $post_id );
	if ( ! $price ) return;
	if ( strlen( $price ) == 0 ) $price = 0;
	$tax = tcp_get_the_meta( 'tcp_tax', $post_id );
	if ( ! $tax ) return;
	if ( strlen( $tax ) == 0 ) $tax = 0;
	if ( $tax > 0 ) $price = $price * 1 + ($tax / 100);
	return apply_filters( 'tcp_get_the_price_tax', $price, $post_id );
}

function tcp_get_the_product_type( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_type', $post_id );
}

function tcp_get_the_weight( $post_id = 0 ) {
	$weight = (float)tcp_get_the_meta( 'tcp_weight', $post_id );
	$weight = apply_filters( 'tcp_get_the_weight', $weight, $post_id );
	return $weight;
}

function tcp_the_weight( $before = '', $after = '', $echo = true ) {
	$weight = tcp_number_format( tcp_get_the_weight() );
	$weight = $before . $weight . $after;
	$weight = apply_filters( 'tcp_the_weight', $price );
	if ( $echo )
		echo $weight;
	else
		return $weight;
}

function tcp_get_the_order( $post_id = 0 ) {
	return (int)tcp_get_the_meta( 'tcp_order', $post_id );
}

function tcp_the_sku( $before = '', $after = '', $echo = true ) {
	$sku = tcp_the_meta( 'tcp_sku', $before, $after, false );
	$sku = apply_filters( 'tcp_the_sku', $sku );
	if ( $echo )
		echo $sku;
	else
		return $sku;
}

function tcp_get_the_sku( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$sku = tcp_get_the_meta( 'tcp_sku', $option_2_id );
		if ( strlen( $sku ) == 0 )
			return tcp_get_the_sku( $post_id, $option_1_id );
	} elseif ( $option_1_id > 0) {
		$sku = tcp_get_the_meta( 'tcp_sku', $option_1_id );
		if ( strlen( $sku ) == 0 )
			return tcp_get_the_sku( $post_id );
	} else
		$sku = tcp_get_the_meta( 'tcp_sku', $post_id );
	$sku = apply_filters( 'tcp_get_the_sku', $sku, $post_id, $option_1_id, $option_2_id );
	return $sku;
}

function tcp_the_stock( $before = '', $after = '', $echo = true ) {
	$stock = tcp_the_meta( 'tcp_stock', $before, $after, false );
	$stock = apply_filters( 'tcp_the_stock', $stock );
	if ( $echo )
		echo $stock;
	else
		return $stock;
}

function tcp_get_the_stock( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$stock = tcp_get_the_meta( 'tcp_stock', $option_2_id );
		if ( $stock == -1 )
			$stock = tcp_get_the_stock( $post_id, $option_1_id );
	} elseif ( $option_1_id > 0) {
		$stock = tcp_get_the_meta( 'tcp_stock', $option_1_id );
		if ( $stock == -1 )
			$stock = tcp_get_the_stock( $post_id );
	} else {
		$stock = tcp_get_the_meta( 'tcp_stock', $post_id );
		if ( strlen( $stock ) > 0 )
			$stock = (int)$stock;
		else
			$stock = -1;
	}
	$stock = apply_filters( 'tcp_get_the_stock', $stock, $post_id, $option_1_id, $option_2_id );
	return $stock;
}

function tcp_set_the_stock( $post_id, $option_1_id = 0, $option_2_id = 0, $stock = -1 ) {
	if ( (int)$stock > -1 ) {
		if ( $option_2_id > 0) {
			$old_stock = tcp_get_the_meta( 'tcp_stock', $option_2_id );
			if ( $old_stock == -1 ) {
				return tcp_set_the_stock( $post_id, $option_1_id, 0, $stock );
			} else {
				update_post_meta( $option_2_id, 'tcp_stock', (int)$stock );
				return true;
			}
		} elseif ( $option_1_id > 0) {
			$old_stock = tcp_get_the_meta( 'tcp_stock', $option_1_id );
			if ( $old_stock == -1 ) {
				return tcp_set_the_stock( $post_id, 0, 0, $stock );
			} else {
				update_post_meta( $option_1_id, 'tcp_stock', (int)$stock );
				return true;
			}
		} else {
			update_post_meta( $post_id, 'tcp_stock', (int)$stock );
			return true;
		}
	} else return false;
}

function tcp_is_downloadable( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_is_downloadable', $post_id );
}

function tcp_is_visible( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_is_visible', $post_id );
}

function tcp_hide_buy_button( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_hide_buy_button', $post_id );
}

function tcp_get_the_file( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_download_file', $post_id );
}

function tcp_set_the_file( $post_id, $upload_file ) {
	$default_id = tcp_get_default_id( $post_id );
	if ( $default_id != $post_id) $post_id = $default_id;
	update_post_meta( $post_id, 'tcp_download_file', $upload_file );
}

function tcp_the_meta( $meta_key, $before = '', $after = '', $echo = true ) {
	$meta_value = tcp_get_the_meta( $meta_key );
	if ( strlen( $meta_value ) == 0 ) return '';
	$meta_value = $before . $meta_value . $after;
	$meta_value = apply_filters( 'tcp_the_meta', $meta_value, $meta_key );
	if ( $echo )
		echo $meta_value;
	else
		return $meta_value;
}

function tcp_get_the_meta( $meta_key, $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if ( ! $meta_value ) {
		$default_id = tcp_get_default_id( $post_id );
		if ( $default_id != $post_id) $meta_value = get_post_meta( $default_id, $meta_key, true );
	}
	$meta_value = apply_filters( 'tcp_get_the_meta', $meta_value, $meta_key, $post_id );
	return $meta_value;
}


//to select in a multiple select control
function tcp_selected_multiple( $values, $value, $echo = true ) {
	if ( in_array( $value, $values ) )
		if ( $echo )
			echo ' selected="true"';
		else
			return ' selected="true"';
}

function tcp_get_the_parent( $post_id, $rel_type = 'GROUPED' ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
	return RelEntities::getParent( $post_id, $rel_type );
}

/**
 * Formats a float number to a string number to show in the screen
 * @since 1.0.7
 */
function tcp_number_format( $number, $decimals = 2 ) {
	global $thecartpress;
	return number_format( $number, $decimals,  $thecartpress->settings['decimal_point'], $thecartpress->settings['thousands_separator'] );
}

/**
 * Converts and typed number into a float number
 * @since 1.0.7
 */
function tcp_input_number( $input ) {
	global $thecartpress;

	$aux = str_replace( $thecartpress->settings['thousands_separator'], '', $input );
	$aux = str_replace( $thecartpress->settings['decimal_point'], '.', $aux );
	return (float)$aux;
}
?>
