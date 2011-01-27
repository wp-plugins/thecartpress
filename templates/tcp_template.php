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

function tcp_the_currency( $echo = true ) {
	$settings = get_option( 'tcp_settings' );
	$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';
	if ( $echo )
		echo $currency;
	else
		return $currency;
}

function tcp_the_buy_button( $post_id = 0, $echo = true ) {
	BuyButton::show( $post_id, $echo );
}

function tcp_the_price( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_price', $before, $after, $echo );
}

function tcp_get_the_price( $post_id = 0 ) {
	return (float)tcp_get_the_meta( 'tcp_price', $post_id );
}

function tcp_the_price_label( $before = '', $after = '', $echo = true ) {
	$price = tcp_get_the_price_label();
	if ( strlen( $price ) == 0 ) return '';
	$price = $before . $price . $after;
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
			if ( $min != $max )
				return $min . __( ' to ', 'tcp' ) . $max;
			else
				return $min;
		} else
			return 0;
	} else
		return tcp_get_the_price( $post_id );
}

function tcp_the_tax_id( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_tax_id', $before, $after, $echo );
}

function tcp_get_the_tax_id( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_tax_id', $post_id );
}

function tcp_the_tax( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_tax', $before, $after, $echo );
}

function tcp_get_the_tax( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_tax', $post_id );
}

function tcp_the_tax_label( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_tax_label', $before, $after, $echo );
}

function tcp_get_the_tax_label( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_tax_label', $post_id );
}

function tcp_the_price_tax( $before = '', $after = '', $echo = true ) {
	$price = tcp_get_the_price_tax();
	if ( strlen( $price ) == 0 ) return;
	$price = $before . $price . $after;
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
	return $price;
}

function tcp_get_the_product_type( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_type', $post_id );
}

function tcp_the_weight( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_weight', $before, $after, $echo );
}

function tcp_get_the_weight( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_weight', $post_id );
}

function tcp_the_sku( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_sku', $before, $after, $echo );
}

function tcp_get_the_sku( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_sku', $post_id );
}

function tcp_the_stock( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_stock', $before, $after, $echo );
}

function tcp_get_the_stock( $post_id = 0 ) {
	$stock = tcp_get_the_meta( 'tcp_stock', $post_id );
	if ( strlen( $stock ) > 0 )
		return (int)$stock;
	else
		return -1;
}

function tcp_set_the_stock( $post_id, $stock = -1 ) {
	if ( (int)$stock > -1 )
		update_post_meta( $post_id, 'tcp_stock', (int)$stock );
	else
		return false;
}

function tcp_is_downloadable( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_is_downloadable', $post_id );
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
	return $meta_value;
}

function tcp_get_default_id( $post_id, $post_type = 'tcp_product' ) {
	global $sitepress;
	if ( $sitepress ) {
		$default_language = $sitepress->get_default_language();
		return icl_object_id( $post_id, $post_type, true, $default_language );
	} else
		return $post_id;
}

//to select in a multiple select
function tcp_selected_multiple( $values, $value, $echo = true ) {
	if ( in_array( $value, $values ) )
	if ( $echo )
		echo ' selected="true"';
	else
		return ' selected="true"';
}
?>
