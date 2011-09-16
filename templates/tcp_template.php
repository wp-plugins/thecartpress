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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//Multilingual support: WPML or Qtranslate (or by any other plugin)
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

$multilingual_template_path = apply_filters( 'tcp_get_multilingual_template_path', '' );
if ( strlen( $multilingual_template_path ) > 0 ) {
	include_once( $multilingual_template_path );
} else {
	global $sitepress;
	if ( $sitepress ) {
		include_once( dirname( __FILE__ ) . '/tcp_wpml_template.php' );
	} else {
		include_once( dirname( __FILE__ ) . '/tcp_qt_template.php' );
	}
}
//End Multilingual support

//Returns the title of a product (with/without options)
function tcp_get_the_title( $post_id = 0, $option_1_id = 0, $option_2_id = 0, $html = true, $show_parent = true ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$title = '';
	if ( $html ) $title .= '<span class="tcp_nested_title">';
	$title .= get_the_title( $post_id );
	if ( $html ) $title .= '</span>';
	if ( $option_1_id > 0 ) {
		$option_1_id = tcp_get_current_id( $option_1_id, 'tcp_product_option' );
		if ( $html ) $title .= ' <span class="tcp_nested_option_1">';
		else $title .= ' - ';
		$title .= get_the_title( $option_1_id );
		if ( $html ) $title .= '</span>';
	}
	if ( $option_2_id > 0 ) {
		$option_2_id = tcp_get_current_id( $option_2_id, 'tcp_product_option' );
		if ( $html ) $title .= ' <span class="tcp_nested_option_1">';
		else $title .= ' - ';
		$title .= get_the_title( $option_2_id );
		if ( $html ) $title .= '</span>';
	}
	if ( $show_parent && ! tcp_is_visible( $post_id ) ) {
		$post_id = tcp_get_the_parent( $post_id );
		$title = get_the_title( $post_id ) . ' - ' . $title;
	}
	return $title;
}

function tcp_the_title( $echo = true, $html = true ) {
	$title = tcp_get_the_title();
	if ( $echo )
		echo $title;
	else
		return $title;
}

function tcp_get_the_currency() {
	return tcp_the_currency( false );
}

function tcp_the_currency( $echo = true ) {
	global $thecartpress;
	$currency = isset( $thecartpress->settings['currency'] ) ? $thecartpress->settings['currency'] : 'EUR';
	$currency = apply_filters( 'tcp_the_currency', $currency );
	if ( $echo )
		echo $currency;
	else
		return $currency;
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

function tcp_get_the_currency_iso() {
	return tcp_the_currency_iso( false );
}

function tcp_the_currency_layout( $echo = true ) {
	global $thecartpress;
	$currency_layout = isset( $thecartpress->settings['currency_layout'] ) ? $thecartpress->settings['currency_layout'] : _x( '%1$s%2$s (%3$s)', 'currency + price + (currency ISO)', 'tcp' );
	$currency_layout = apply_filters( 'tcp_the_currency_layout', $currency_layout );
	if ( $echo )
		echo $currency_layout;
	else
		return $currency_layout;
}

function tcp_get_the_currency_layout() {
	return tcp_the_currency_layout( false );
}

function tcp_get_decimal_currency() {
	global $thecartpress;
	$decimal_currency = isset( $thecartpress->settings['decimal_currency'] ) ? $thecartpress->settings['decimal_currency'] : '2';
	$decimal_currency = apply_filters( 'tcp_get_decimal_currency', $decimal_currency );
	return $decimal_currency;
}

function tcp_get_number_format_example( $number = 19.99, $see_eg = true, $echo = false ) {
	$out = '';
	if ( $see_eg ) $out .= 'e.g. ';
	$out .= tcp_number_format( $number );
	if ( $echo ) echo $out;
	else return $out;
}

function tcp_number_format_example( $number = 19.99, $see_eg = true ) {
	tcp_get_number_format_example( $number, $see_eg, true );
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

function tcp_get_the_unit_weight() {
	return tcp_the_unit_weight( false );
}

function tcp_get_default_currency() {
	global $thecartpress;
	return isset( $thecartpress->settings['currency'] ) ? $thecartpress->settings['currency'] : '';
}

function tcp_the_buy_button( $post_id = 0, $echo = true ) {
	$html = apply_filters( 'tcp_the_buy_button', '', $post_id );
	if ( strlen( $html ) > 0 ) {
		if ( $echo ) echo $html;
		else return $html;
	} else {
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/BuyButton.class.php' );	
		return BuyButton::show( $post_id, $echo );
	}
}

function tcp_get_the_buy_button( $post_id = 0 ) {
	return tcp_the_buy_button( $post_id, false );
}

/**
 * Returns the price of the current product
 * since 1.0.9
 */
function tcp_the_price( $before = '', $after = '', $echo = true ) {
	$price = tcp_number_format( tcp_get_the_price() );
	$price = $before . $price . $after;
	if ( $echo )
		echo $price;
	else
		return $price;
}

/**
 * Returns the price of the given product
 * since 1.0.9
 */
function tcp_get_the_price( $post_id = 0 ) {
	$price = (float)tcp_get_the_meta( 'tcp_price', $post_id );
	$price = (float)apply_filters( 'tcp_get_the_price', $price, $post_id );
	return $price;
}

/**
 * Adds the currency to the price
 * since 1.0.9
 */
function tcp_format_the_price( $price, $currency = '') {
	if ( $currency == '' ) $currency = tcp_get_the_currency();
	//$label = sprintf( _x( '%1$s%2$s (%3$s)', 'currency + price + (currency ISO)', 'tcp' ), $currency, tcp_number_format( $price, tcp_get_decimal_currency() ), tcp_get_the_currency_iso() );
	$layout = tcp_get_the_currency_layout();
	if ( strlen( $layout ) == 0 ) $layout = _x( '%1$s%2$s (%3$s)', 'currency + price + (currency ISO)', 'tcp' );
	$label = sprintf( $layout, $currency, tcp_number_format( $price, tcp_get_decimal_currency() ), tcp_get_the_currency_iso() );
	$label = apply_filters( 'tcp_format_the_price', $label );
	return $label;
}

/**
 * Since 1.1.1
 */
function tcp_get_the_price_to_show( $post_id = 0, $price = false ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	if ( $price === false ) $price = tcp_get_the_price( $post_id );
	if ( tcp_is_display_prices_with_taxes() ) {
		if ( tcp_is_prices_include_tax() ) {
			return $price;
		} else { //add tax from price
			$tax = tcp_get_the_tax( $post_id );
			$amount = $price * $tax / 100;
			return $price + $amount;
		}
	} elseif ( ! tcp_is_prices_include_tax() ) {
		return $price;
	} else { //remove tax from price
		$tax = tcp_get_the_tax( $post_id );
		return $price / ( 1 + $tax / 100 );
	}
}

/**
 * Display the price with currency
 * since 1.0.9
 */
function tcp_the_price_label( $before = '', $after = '', $echo = true ) {
	$label = tcp_get_the_price_label();
	$label = $before . $label . $after;
	if ( $echo )
		echo $label;
	else
		return $label;
}

/**
 * Returns the price with currency
 * since 1.0.9
 */
function tcp_get_the_price_label( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	//$type = tcp_get_the_meta( 'tcp_type', $post_id );
	$type = tcp_get_the_product_type( $post_id );
	if ( $type == 'SIMPLE' ) {
		$price = tcp_get_the_price_to_show( $post_id );
		$label = tcp_format_the_price( $price );
	} else if ( $type == 'GROUPED' ) {
		$min_max = tcp_get_min_max_price( $post_id );
		if ( is_array( $min_max ) ) {
			$min = $min_max[0];
			$max = $min_max[1];
			if ( $min != $max ) {
				$label = sprintf( _x( '%s to %s', 'min_price to max_price', 'tcp' ), tcp_format_the_price( $min ), tcp_format_the_price( $max ) );
			} else {
				$label = tcp_format_the_price( $min );
			}
		} else {
			$label = '';
		}
	} else {
		$label = apply_filters( 'tcp_get_the_price_label_unkonw_product_type', '', $post_id );
	}
	$label = apply_filters( 'tcp_get_the_price_label', $label, $post_id );
	return $label;
}

/**
 * Returns the (min, max) price for grouped products
 * since 1.1.0
 */
function tcp_get_min_max_price( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
	$products = RelEntities::select( $post_id, 'GROUPED' );
	if ( is_array( $products ) && count( $products ) > 0 ) {
		$min = 99999999999;
		$max = 0;
		foreach( $products as $product ) {
			if ( ! tcp_is_exclude_range( $product->id_to ) ) {
				$price = (float)tcp_get_the_price_to_show( $product->id_to );
				if ( $price > 0 ) {
					if ( $price < $min ) $min = $price;
					if ( $price > $max ) $max = $price;
				}
			}
		}
		if ( $min == 99999999999 ) $min = $max;
		return array( $min, $max );
	} else {
		return false;
	}
}

/**
 * Returns the min price of a grouped product
 * since 1.1.0
 */
function tcp_get_min_price( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$min_max = tcp_get_min_max_price( $post_id );
	if ( is_array( $min_max ) && count( $min_max ) == 2 ) {
		return $min_max[0];
	} else {
		return 0;
	}
}

/**
 * Returns the max price of a grouped product
 * since 1.1.0
 */
function tcp_get_max_price( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$min_max = tcp_get_min_max_price( $post_id );
	if ( is_array( $min_max ) && count( $min_max ) == 2 ) {
		return $min_max[1];
	} else {
		return 0;
	}
}

/**
 * Returns the price without tax
 * since 1.0.9
 */
function tcp_get_the_price_without_tax( $post_id, $price = false ) {
	if ( $price === false ) $price = tcp_get_the_price( $post_id );
	if ( tcp_is_prices_include_tax() ) {
		$tax = tcp_get_the_tax( $post_id );
		$price_without_tax = $price / (1 + $tax / 100 );
		return $price_without_tax;
	} else {
		return $price;
	}
}

/**
 * Returns the tax applied to a product
 * since 1.0.9
 */
function tcp_get_the_tax( $post_id = 0 ) {
	$tax_id = tcp_get_the_tax_id( $post_id );
	if ( $tax_id == 0 ) return 0;
	$country_iso = tcp_get_tax_country();
	$region_iso = tcp_get_tax_region();
	require_once( dirname( dirname ( __FILE__ ) ) . '/daos/TaxRates.class.php' );
	$tax = TaxRates::find( $country_iso, $region_iso, 'all', $tax_id );
	$tax = apply_filters( 'tcp_get_the_tax', $tax, $post_id );
	if ( $tax ) return $tax->rate; //$tax->label
	else return 0;
}

function tcp_the_tax( $before = '', $after = '', $echo = true ) {
	$tax = tcp_number_format( tcp_get_the_tax() );
	$tax = $before . $tax . $after;
	if ( $echo )
		echo $tax;
	else
		return $tax;
}

function tcp_get_the_tax_id( $post_id = 0 ) {
	$tax_id = tcp_get_the_meta( 'tcp_tax_id', $post_id );
	if ( ! $tax_id )
		return 0;//-1;
	else
		return $tax_id;
}

/**
 * Returns the tax title
 */
function tcp_get_the_tax_type( $post_id = 0 ) {
	$tax_id = tcp_get_the_meta( 'tcp_tax_id', $post_id );
	if ( ! $tax_id ) {
		return '';
	} else {
		require_once( dirname( dirname ( __FILE__ ) ) . '/daos/Taxes.class.php' );
		$tax_type = Taxes::get( $tax_id );
		if ( $tax_type )
			return $tax_type->title;
		else
			return '';
	}
}

//
//for themes
//
function tcp_posted_on() {
	printf( __( '<span class="tcp_posted_on"><span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a></span>', 'tcp' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}

function tcp_posted_by() {
	printf( __( '<span class="tcp_by_author"><span class="sep">by </span> <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', 'tcp' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		sprintf( esc_attr__( 'View all posts by %s', 'tcp' ), get_the_author() ),
		esc_html( get_the_author() )
	);
}
//
//End for themes
//

/**
 * Returns the default country to calculate tax
 * since 1.0.9
 */
function tcp_get_default_tax_country() {
	global $thecartpress;
	return isset( $thecartpress->settings['default_tax_country'] ) ? $thecartpress->settings['default_tax_country'] : '';
}

/**
 * Returns the country iso to calculate tax
 * since 1.0.9
 */
function tcp_get_tax_country() {
	$tax_based_on = tcp_get_tax_based_on();
	$country_iso = '';
	if ( $tax_based_on == 'billing' && function_exists( 'tcp_get_billing_country' ) ) {
		$country_iso = tcp_get_billing_country();
	} elseif ( $tax_based_on == 'shipping' && function_exists( 'tcp_get_shipping_country' ) ) {
		$country_iso = tcp_get_shipping_country();
	}
	if ( $country_iso != '') {
		return $country_iso;
	} else {
		return tcp_get_default_tax_country();
	}
}

/**
 * Returns the region iso to calculate tax
 * since 1.0.9
 */
function tcp_get_tax_region() {
	$tax_based_on = tcp_get_tax_based_on();
	$region_iso = '';
	if ( $tax_based_on == 'billing' && function_exists( 'tcp_get_billing_region' ) ) {
		$region_iso = tcp_get_billing_region();
	} elseif ( $tax_based_on == 'shipping' && function_exists( 'tcp_get_shipping_region' ) ) {
		$region_iso = tcp_get_shipping_region();
	}
	if ( $region_iso != '') {
		return $region_iso;
	} else {
		return 'all';
	}
}

/**
 * Calculates the shipping/payment/other costs tax
 * since 1.0.9
 */
function tcp_calculate_tax_for_shipping( $cost ) {
	$tax = tcp_get_the_shipping_tax();
	if ( $tax == 0) return 0;
	if ( tcp_is_shipping_cost_include_tax() )
		return $cost * $tax / ($tax + 100);
	else
		return $cost * $tax / 100;
}

/**
 * Returns the shipping cost without tax
 * since 1.0.9
 */
function tcp_get_the_shipping_cost_without_tax( $cost ) {
	if ( tcp_is_shipping_cost_include_tax() ) {
		$tax = tcp_get_the_shipping_tax();
		if ( $tax == 0 ) return $cost;
		$cost_without_tax = $cost / ( 1 + $tax / 100 );
		return $cost_without_tax;
	} else {
		return $cost;
	}
}

/**
 * Returns the tax (float) to apply to the shipping/payment/other costs
 * since 1.0.9
 */
function tcp_get_the_shipping_tax() {
	$tax_id = tcp_get_the_shipping_tax_id();
	if ( $tax_id == 0 ) return 0;
	require_once( dirname( dirname ( __FILE__ ) ) . '/daos/TaxRates.class.php' );
	$country_iso = tcp_get_tax_country();
	$region_iso = tcp_get_tax_region();
	$tax = TaxRates::find( $country_iso, $region_iso, 'all', $tax_id );
	$tax = apply_filters( 'tcp_get_the_shipping_tax', $tax );
	if ( $tax ) return $tax->rate;
	else return 0;
}

/**
 * Returns the tax id to apply to the shipping/payment/other costs
 * since 1.0.9
 */
function tcp_get_the_shipping_tax_id() {
	global $thecartpress;
	$tax_id = isset( $thecartpress->settings['tax_for_shipping'] ) ? $thecartpress->settings['tax_for_shipping'] : 0;
	$tax_id = apply_filters( 'tcp_get_the_shipping_tax_id', $tax_id );
	return $tax_id; 
}

function tcp_is_shipping_cost_include_tax() {
	global $thecartpress;
	return isset( $thecartpress->settings['shipping_cost_include_tax'] ) ? $thecartpress->settings['shipping_cost_include_tax'] : false;
}

function tcp_is_display_shipping_cost_with_taxes() {
	global $thecartpress;
	return isset( $thecartpress->settings['display_shipping_cost_with_taxes'] ) ? $thecartpress->settings['display_shipping_cost_with_taxes'] : false;
}

/**
 * Returns true if the prices include the taxes
 */
function tcp_is_prices_include_tax() {
	global $thecartpress;
	return isset( $thecartpress->settings['prices_include_tax'] ) ? $thecartpress->settings['prices_include_tax'] : false;
}

function tcp_get_tax_based_on() {
	global $thecartpress;
	return isset( $thecartpress->settings['tax_based_on'] ) ? $thecartpress->settings['tax_based_on'] : 'origin';
}

/**
 * Returns true if the prices must be displayed with taxes
 */
function tcp_is_display_prices_with_taxes() {
	global $thecartpress;
	return isset( $thecartpress->settings['display_prices_with_taxes'] ) ? $thecartpress->settings['display_prices_with_taxes'] : false;
}

/**
 * Returns true if the full tax summary must be displayed in the cart/order tables
 */
function tcp_is_display_full_tax_summary() {
	global $thecartpress;
	return isset( $thecartpress->settings['display_full_tax_summary'] ) ? $thecartpress->settings['display_full_tax_summary'] : false;
}

function tcp_get_display_zero_tax_subtotal() {
	global $thecartpress;
	return isset( $thecartpress->settings['display_zero_tax_subtotal'] ) ? $thecartpress->settings['display_zero_tax_subtotal'] : false;
}

/**
 * Returns true if the  tax summary must be displayed in the cart/order tables
 */
function display_zero_tax_subtotal() {
	global $thecartpress;
	return isset( $thecartpress->settings['display_zero_tax_subtotal'] ) ? $thecartpress->settings['display_zero_tax_subtotal'] : false;
}

//TODO Deprecated 1.1
//function tcp_the_tax_label( $before = '', $after = '', $echo = true ) {
//	return '';
//}
//TODO

function tcp_get_the_product_type( $post_id = 0 ) {
	$type = tcp_get_the_meta( 'tcp_type', $post_id );
	if ( $type == '' ) $type = 'SIMPLE';
	return $type;
}

function tcp_get_the_weight( $post_id = 0 ) {
	$weight = (float)tcp_get_the_meta( 'tcp_weight', $post_id );
	$weight = apply_filters( 'tcp_get_the_weight', $weight, $post_id );
	return $weight;
}

function tcp_the_weight( $before = '', $after = '', $echo = true ) {
	$weight = tcp_number_format( tcp_get_the_weight() );
	$weight = $before . $weight . $after;
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

function tcp_is_exclude_range( $post_id = 0 ) {
	$default_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
	return tcp_get_the_meta( 'tcp_exclude_range', $default_id );
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
	$default_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
	if ( $default_id != $post_id ) $post_id = $default_id;
	update_post_meta( $post_id, 'tcp_download_file', $upload_file );
}

function tcp_get_the_parent( $post_id, $rel_type = 'GROUPED' ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
	return RelEntities::getParent( $post_id, $rel_type );
}

function tcp_get_the_parents( $post_id, $rel_type = 'GROUPED' ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
	return RelEntities::getParents( $post_id, $rel_type );
}

function tcp_get_the_thumbnail( $post_id = 0, $size = 'thumbnail' ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$image = get_the_post_thumbnail( $post_id, $size );
	if ( ! $image ) {
		$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
		$image = get_the_post_thumbnail( $post_id, $size );
	}
	return $image;
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

function tcp_get_the_meta( $meta_key, &$post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if ( ! $meta_value ) {
		$default_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
		if ( $default_id != $post_id ) $meta_value = get_post_meta( $default_id, $meta_key, true );
	}
	$meta_value = apply_filters( 'tcp_get_the_meta', $meta_value, $meta_key, $post_id );
	return $meta_value;
}

//
//Saleable_post_type
//
function tcp_get_saleable_post_types() {
	$saleable_post_types = array( 'tcp_product' );
	$saleable_post_types = apply_filters( 'tcp_get_saleable_post_types', $saleable_post_types );
	return $saleable_post_types;
}

function tcp_is_saleable_post_type( $post_type ) {
	$saleable_post_types = tcp_get_saleable_post_types();
	return in_array( $post_type, $saleable_post_types );
}

function tcp_is_saleable_taxonomy( $taxonomy ) {
	$tax = get_taxonomy( $taxonomy );
	if ( isset( $tax->object_type[0] ) ) 
		return tcp_is_saleable_post_type( $tax->object_type[0] );
	else
		return false;
}

//
//Order status template functions
//
function tcp_get_order_status() {
	$status_list = array(
		array(
			'name'	=> Orders::$ORDER_PENDING,
			'label'	=>__( 'Pending', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> false,
		),
		array(
			'name'	=> Orders::$ORDER_PROCESSING,
			'label'	=>__( 'Processing', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> false,
		),
		array(
			'name'	=> Orders::$ORDER_COMPLETED,
			'label'	=>__( 'Completed', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> false,
			'is_completed'			=> true,
		),
		array(
			'name'	=> Orders::$ORDER_CANCELLED,
			'label'	=>__( 'Cancelled', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> true,
			'is_cancelled'			=> true,
		),
		array(
			'name'	=> Orders::$ORDER_SUSPENDED,
			'label'	=>__( 'Suspended', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> true,
		)
	);
	return apply_filters( 'tcp_get_order_status', $status_list );
}

function tcp_is_order_status_valid_for_deleting( $status ) {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $s )
		if ( $s['name'] == $status && isset( $s['valid_for_deleting'] ) && $s['valid_for_deleting'] )
			return true;
	return false;
}

function tcp_get_cancelled_order_status() {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $status )
		if ( isset( $status['is_cancelled'] ) && $status['is_cancelled'] )
			return $status['name'];
	return 'CANCELLED';
}

function tcp_get_completed_order_status() {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $status )
		if ( isset( $status['is_completed'] ) && $status['is_completed'] )
			return $status['name'];
	return 'COMPLETED';
}
//
// End Order status functions templates
//

//
//Product types
//
function tcp_get_product_types( $no_one = false ) {
	$types = array();
	if ( $no_one ) 	$types['']	= __( 'No one', 'tcp' );
	$types['SIMPLE']	= __( 'Simple', 'tcp' );
	$types['GROUPED']	= __( 'Grouped', 'tcp' );
	return apply_filters( 'tcp_get_product_types', $types );
}
//
//End product types
//

//
// Utils and Tools
//
/**
 * Selected in a multiple select control
 */
function tcp_selected_multiple( $values, $value, $echo = true ) {
	if ( in_array( $value, $values ) )
		if ( $echo )
			echo ' selected="true"';
		else
			return ' selected="true"';
}

/**
 * Checked in a multiple select control
 */
function tcp_checked_multiple( $values, $value, $echo = true ) {
	if ( in_array( $value, $values ) )
		if ( $echo )
			echo ' checked="true"';
		else
			return ' checked="true"';
}
/**
 * Formats a float number to a string number to show in the screen
 * @since 1.0.7
 */
function tcp_number_format( $number, $decimals = 2 ) {
	global $thecartpress;
	return number_format( $number, $decimals, $thecartpress->settings['decimal_point'], $thecartpress->settings['thousands_separator'] );
}

/**
 * Converts a typed number into a float number
 * @since 1.0.7
 */
function tcp_input_number( $input ) {
	global $thecartpress;

	$aux = str_replace( $thecartpress->settings['thousands_separator'], '', $input );
	$aux = str_replace( $thecartpress->settings['decimal_point'], '.', $aux );
	return (float)$aux;
}

/**
 * Returns the remote ip
 * @since 1.0.9
 */
function tcp_get_remote_ip() {
	if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) { // for proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		//$proxy = $_SERVER['REMOTE_ADDR'];
		//$host = @gethostbyaddr( $_SERVER['HTTP_X_FORWARDED_FOR'] );
	} else { // for normal user
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		//$host = @gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
	}
	return $ip;
}

/**
 * Returns a subfix from a request action
 * For example: if $_REQUEST['action_1'] exists,
 *  tcp_is_request('action'] -> '_1'
 *  tcp_is_request('other_action'] -> false
 */
function tcp_is_request( $name ) {
	foreach( $_REQUEST as $req => $value ) {
		$pos = strpos( $req, $name );
		if ( $pos !== false && $pos == 0 ) {
		//if ( strpos( $req, $action ) !== false ) {
			$index = substr( $req, strlen( $name ) );
			return strlen( $index ) > 0 ?  $index : false;
		}
	}
	return false;
}
/**
 * Returns values from request with the same prefix
 */
function tcp_get_request_array( $name ) {
	$values = array();
	foreach( $_REQUEST as $req => $value ) {
		$pos = strpos( $req, $name );
		if ( $pos !== false && $pos == 0 ) {
			$values[] = $value;
		}
	}
	return $values;
}

/**
 * Creates a select in html format
 * @param $options = array( 'value' => 'title', ...);
 */
function tcp_html_select( $name, $options, $value, $echo = true, $class = '', $id = false ) {
	if ( $id === false ) $id = $name;
	$out = '<select id="' . $id . '" name="' . $name . '"';
	if ( strlen( $class ) > 0 ) $out .= 'class="' . $class . '"';
	$out .= '>' . "\n";
	foreach( $options as $option_value => $option_text )
		$out .= '<option value="' . $option_value . '" ' . selected( $value, $option_value, false ) . '>' . $option_text . '</option>' . "\n";
	$out .= '</select>' . "\n";
	if ( $echo ) echo $out;
	else return $out;
}
?>
