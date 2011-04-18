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

/**
 * Display Taxonomy Tree.
 *
 * This function is primarily used by themes which want to hardcode the Taxonomy
 * Tree into the sidebar and also by the TaxonomyTree widget in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_taxonomy_tree'.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_taxonomy_tree( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomy_tree' );
	if ( ! $args )
		$args = array(
			'style'			=> 'list',
			'show_count'	=> true,
			'hide_empty'	=> true,
			'taxonomy'		=> 'tcp_product_category',
			'title_li'		=> '',
			'echo'			=> false,
		);
	$tree = '<ul>' . wp_list_categories( $args ) . '</ul>';
	$tree = apply_filters( 'tcp_get_taxonomy_tree', $tree );
	if ( $echo )
		echo $before, $tree, $after;
	else
		return $before . $tree . $after;
}

/**
 * Display Shopping Cart Summary.
 *
 * This function is primarily used by themes which want to hardcode the Resumen
 * Shopping Cart into the sidebar and also by the ShoppingCartSummary widget
 * in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_shopping_cart_summary'.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_shopping_cart_summary( $args = false, $echo = true ) {
	do_action( 'tcp_get_shopping_cart_before_summary' );
	if ( ! $args )
		$args = array(
			'see_product_count' => false,
			'see_stock_notice'	=> true,
			'see_weight'		=> true,
			'see_delete_all'	=> false,
			'see_shopping_cart'	=> true,
			'see_checkout'		=> true,
		);
	global $thecartpress;
	$currency			= tcp_get_the_currency();
	$unit_weight		= isset( $thecartpress->settings['unit_weight'] ) ? $thecartpress->settings['unit_weight'] : 'gr';
	$stock_management	= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
	$shoppingCart = TheCartPress::getShoppingCart();
	$summary = '<ul class="tcp_shopping_cart_resume">';
	$summary .= '<li><span class="tcp_resumen_subtotal">' . __( 'Total', 'tcp' ) . ':</span>&nbsp;' . tcp_number_format( $shoppingCart->getTotal() ) . '&nbsp;' . $currency . '</li>';
	
	$discount = $shoppingCart->getAllDiscount();
	if ( $discount > 0 )
		$summary .= '<li><span class="tcp_resumen_discount">' . __( 'Discount', 'tcp' ) . ':</span>&nbsp;' . tcp_number_format( $discount ) . '&nbsp;' . $currency  . '</li>';

	if ( isset( $args['see_product_count'] ) ? $args['see_product_count'] : false )
		$summary .=	'<li><span class="tcp_resumen_count">' . __( 'N<sup>o</sup> products', 'tcp' ) . ':</span>&nbsp;' . $shoppingCart->getCount() . '</li>';

	if ( $stock_management && isset( $args['see_stock_notice'] ) ? $args['see_stock_notice'] : false )
		if ( ! $shoppingCart->isThereStock() )
			$summary .= '<li><span class="tcp_no_stock__nough">' . printf( __( 'No enough stock for some products. Visit the <a href="%s">Shopping Cart</a> to see more details.', 'tcp' ), get_permalink( get_option( 'tcp_shopping_cart_page_id' ) ) ) . '</span></li>';

	if ( isset( $args['see_weight'] ) ? $args['see_weight'] : false && $shoppingCart->getWeight() > 0 ) 
		$summary .= '<li><span class="tcp_resumen_weight">' . __( 'Weigth', 'tcp' ) . ':</span>&nbsp;' . tcp_number_format( $shoppingCart->getWeight() ) . '&nbsp;' . $unit_weight . '</li>';

	if ( isset( $args['see_shopping_cart'] ) ? $args['see_shopping_cart'] : true )
		$summary .= '<li><a href="' . get_permalink( get_option( 'tcp_shopping_cart_page_id' ) ) . '">' . __( 'Shopping cart', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_checkout'] ) ? $args['see_checkout'] : true )
		$summary .= '<li><a href="' . get_permalink( get_option( 'tcp_checkout_page_id' ) ) . '">' . __( 'Checkout', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_delete_all'] ) ? $args['see_delete_all'] : false ) 
		$summary .= '<li><form method="post"><input type="submit" name="tcp_delete_shopping_cart" value="' . __( 'Delete shopping cart', 'tcp' ) . '"/></form></li>';
	$summary = apply_filters( 'tcp_get_shopping_cart_summary', $summary, $args );
	$summary .= '</ul>';
	if ( $echo )
		echo $summary;
	else
		return $summary;
}

function tcp_get_taxonomies_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomies_cloud' );
	if ( ! $args )
		$args = array(
			'taxonomy'	=> 'tcp_product_tag',
			'echo'		=> false,
    	);
	$cloud = wp_tag_cloud( $args );
	$cloud = apply_filters( 'tcp_get_taxonomies_cloud', $cloud );
	if ( $echo )
		echo $before, $cloud, $after;
	else
		return $before . $cloud . $after;
}

function tcp_get_tags_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_tags_cloud' );
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_tags_cloud', $cloud );
	if ( $echo )
		echo $cloud;
	else
		return $cloud;
}

function tcp_get_suppliers_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_suppliers_cloud' );
	if ( ! $args )
		$args = array(
			'taxonomy'	=> 'tcp_product_supplier',
			'echo'		=> false,
    	);
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_suppliers_cloud', $cloud );
	if ( $echo )
		echo $cloud;
	else
		return $cloud;
}
?>
