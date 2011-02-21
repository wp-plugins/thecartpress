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

function tcp_get_the_currency() {
	tcp_the_currency( false );
}

function tcp_the_buy_button( $post_id = 0, $echo = true ) {
	BuyButton::show( $post_id, $echo );
}

function tcp_the_order_panel() {
	OrderPanel::show();
}

function tcp_the_price( $before = '', $after = '', $echo = true ) {
	$price = tcp_the_meta( 'tcp_price', $before, $after, false );
	$price = apply_filters( 'tcp_the_price', $price );
	if ( $echo )
		echo $price;
	else
		return $price;
}

function tcp_get_the_price( $post_id = 0 ) {
	$price = (float)tcp_get_the_meta( 'tcp_price', $post_id );
	$price = apply_filters( 'tcp_get_the_price', $price );
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
				$price = $min . __( ' to ', 'tcp' ) . $max;
				$price = apply_filters( 'tcp_get_the_price_label', $price );
				return $price;
			} else {
				$price = apply_filters( 'tcp_get_the_price_label', $min );
				return $price;
			}
		} else {
			$price = apply_filters( 'tcp_get_the_price_label', 0 );
			return 0;
		}
	} else {
		$price = tcp_get_the_price( $post_id );
		$price = apply_filters( 'tcp_get_the_price_label', $price );
		return $price;
	}
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
	$tax = apply_filters( 'tcp_get_the_tax_id', $tax );
	return $tax;
}

function tcp_the_tax( $before = '', $after = '', $echo = true ) {
	$tax = tcp_the_meta( 'tcp_tax', $before, $after, false );
	$tax = apply_filters( 'tcp_the_tax', $tax );
	if ( $echo )
		echo $tax;
	else
		return $tax;
}

function tcp_get_the_tax( $post_id = 0 ) {
	$tax = tcp_get_the_meta( 'tcp_tax', $post_id );
	$tax = apply_filters( 'tcp_get_the_tax', $tax );
	return $tax;
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
	$tax = apply_filters( 'tcp_get_the_tax_label', $tax );
	return $tax;
}

function tcp_the_price_tax( $before = '', $after = '', $echo = true ) {
	$price = tcp_get_the_price_tax();
	if ( strlen( $price ) == 0 ) return;
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
	$price = apply_filters( 'tcp_get_the_price_tax', $price );
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

function tcp_get_the_order( $post_id = 0 ) {
	return (int)tcp_get_the_meta( 'tcp_order', $post_id );
}

function tcp_the_sku( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_sku', $before, $after, $echo );
}

function tcp_get_the_sku( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$sku = tcp_get_the_meta( 'tcp_sku', $option_2_id );
		if ( strlen( $sku ) > 0 )
			return $sku;
		else
			return tcp_get_the_sku( $post_id, $option_1_id );
	} elseif ( $option_1_id > 0) {
		$sku = tcp_get_the_meta( 'tcp_sku', $option_1_id );
		if ( strlen( $sku ) > 0 )
			return $sku;
		else
			return tcp_get_the_sku( $post_id );
	} else
		return tcp_get_the_meta( 'tcp_sku', $post_id );
}

function tcp_the_stock( $before = '', $after = '', $echo = true ) {
	return tcp_the_meta( 'tcp_stock', $before, $after, $echo );
}

function tcp_get_the_stock( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$stock = tcp_get_the_meta( 'tcp_stock', $option_2_id );
		if ( $stock == -1 )
			return tcp_get_the_stock( $post_id, $option_1_id );
		else
			return $stock;
	} elseif ( $option_1_id > 0) {
		$stock = tcp_get_the_meta( 'tcp_stock', $option_1_id );
		if ( $stock == -1 )
			return tcp_get_the_stock( $post_id );
		else
			return $stock;
	} else {
		$stock = tcp_get_the_meta( 'tcp_stock', $post_id );
		if ( strlen( $stock ) > 0 )
			return (int)$stock;
		else
			return -1;
	}
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

//Multilanguage
//Given a post_id this function returns the post_id in the default language
function tcp_get_default_id( $post_id, $post_type = 'tcp_product' ) {
	global $sitepress;
	if ( $sitepress ) {
		$default_language = $sitepress->get_default_language();
		return icl_object_id( $post_id, $post_type, true, $default_language );
	} else
		return $post_id;
}

//Given a post_id this function returns the equivalent post_id in the current language
function tcp_get_current_id( $post_id, $post_type = 'tcp_product' ) {
	global $sitepress;
	if ( $sitepress ) {
		$default_language = $sitepress->get_current_language();
		return icl_object_id( $post_id, $post_type, true, $default_language );
	} else
		return $post_id;
}

/**
 * Returns the list of translations from a given post_id
 * Example of returned array
 * array(2) {	["en"]=> object(stdClass)#45 (6) { ["translation_id"]=> string(2) "11" ["language_code"]=> string(2) "en" ["element_id"]=> string(1)  "9" ["original"]=> string(1) "1" ["post_title"]=> string(21) "Tom Sawyer Adventures"       ["post_status"]=> string(7) "publish" }
 * 				["es"]=> object(stdClass)#44 (6) { ["translation_id"]=> string(2) "12" ["language_code"]=> string(2) "es" ["element_id"]=> string(2) "10" ["original"]=> string(1) "0" ["post_title"]=> string(27) "Las Aventuras de Tom Sawyer" ["post_status"]=> string(7) "publish" } }
 */
function tcp_get_all_translations( $post_id, $post_type = 'tcp_product' ) {
	global $sitepress;
	if ( $sitepress ) {
		$trid = $sitepress->get_element_trid( $post_id, 'post_' . $post_type );
		return $sitepress->get_element_translations( $trid, 'post_'. $post_type );
	} else
		return false;
}

function tcp_get_default_language() {
	global $sitepress;
	if ( $sitepress )
		return $sitepress->get_default_language();
	else
		return null;
}

function tcp_get_current_language() {
	global $sitepress;
	if ( $sitepress )
		return $sitepress->get_current_language();
	else
		return null;
}

/**
 * This function adds a post identified by the $translate_post_id as a translation of the post identified by $post_id
 */
function tcp_add_translation( $post_id, $translate_post_id, $language, $post_type = 'tcp_product' ) {
	global $sitepress;
	if ( $sitepress ) {
		$trid = $sitepress->get_element_trid( $post_id, 'post_' . $post_type );
		$sitepress->set_element_language_details( $translate_post_id, 'post_' . $post_type, $trid, $language );
	}
}

/**
 * To register strings to translate. For Example to translate the titles of the wigets
 *
function tcp_register_string( $context, $name, $value ) {
	if ( function_exists( 'icl_register_string' ) )
		icl_register_string( $context, $name, $value );
}

function tcp_unregiser_string( $context, $name ) {
	if ( function_exists( 'icl_unregister_string' ) )
		icl_unregister_string( $context, $name );
}

/**
 * Returns the translation of a string identified by $context and $name
 *
function tcp_t( $context, $name, $value ) {
	if ( function_exists( 'icl_t' ) )
		return icl_t( $context, $name, $value );
	else
		return $value;
}*/
//end Multilanguage

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
?>
