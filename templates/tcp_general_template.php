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

function tcp_the_shopping_cart_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id' ), 'page' ) );
	if ( $echo )
		echo $url;
	else
		return $url;
}

function tcp_get_the_shopping_cart_url() {
	return tcp_the_shopping_cart_url( false );
}

function tcp_the_checkout_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );
	if ( $echo )
		echo $url;
	else
		return $url;
}

function tcp_get_the_checkout_url() {
	return tcp_the_checkout_url( false );
}

function tcp_the_continue_url( $echo = true) {
	global $thecartpress;
	$url = isset( $thecartpress->settings['continue_url'] ) && strlen( $thecartpress->settings['continue_url'] ) > 0 ? $thecartpress->settings['continue_url'] : get_home_url();
	if ( $echo ) echo $url;
	else return $url;
}

function tcp_get_the_continue_url() {
	return tcp_the_continue_url( false );
}


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
	$unit_weight		= isset( $thecartpress->settings['unit_weight'] ) ? $thecartpress->settings['unit_weight'] : 'gr';
	$stock_management	= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
	$shoppingCart		= TheCartPress::getShoppingCart();
	$summary = '<ul class="tcp_shopping_cart_resume">';
	$discount = $shoppingCart->getAllDiscounts();
	if ( $discount > 0 )
		$summary .= '<li><span class="tcp_resumen_discount">' . __( 'Discount', 'tcp' ) . ':</span>&nbsp;' . tcp_format_the_price( $discount ) . '</li>';
	$summary .= '<li><span class="tcp_resumen_subtotal">' . __( 'Total', 'tcp' ) . ':</span>&nbsp;' . tcp_format_the_price( $shoppingCart->getTotalToShow( false ) ) . '</li>';

	if ( isset( $args['see_product_count'] ) ? $args['see_product_count'] : false )
		$summary .=	'<li><span class="tcp_resumen_count">' . __( 'N<sup>o</sup> products', 'tcp' ) . ':</span>&nbsp;' . $shoppingCart->getCount() . '</li>';

	if ( $stock_management && isset( $args['see_stock_notice'] ) ? $args['see_stock_notice'] : false )
		if ( ! $shoppingCart->isThereStock() )
			$summary .= '<li><span class="tcp_no_stock_nough">' . printf( __( 'No enough stock for some products. Visit the <a href="%s">Shopping Cart</a> to see more details.', 'tcp' ), tcp_get_the_shopping_cart_url() ) . '</span></li>';

	$see_weight = isset( $args['see_weight'] ) ? $args['see_weight'] : false;
	if ( $see_weight && $shoppingCart->getWeight() > 0 ) 
		$summary .= '<li><span class="tcp_resumen_weight">' . __( 'Weigth', 'tcp' ) . ':</span>&nbsp;' . tcp_number_format( $shoppingCart->getWeight() ) . '&nbsp;' . $unit_weight . '</li>';
		
	if ( isset( $args['see_shopping_cart'] ) ? $args['see_shopping_cart'] : true )
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_shopping_cart_link"><a href="' . tcp_get_the_shopping_cart_url() . '">' . __( 'Shopping cart', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_checkout'] ) ? $args['see_checkout'] : true )
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_checkout_link"><a href="' . tcp_get_the_checkout_url() . '">' . __( 'Checkout', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_delete_all'] ) ? $args['see_delete_all'] : false ) 
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_delete_all_link"><form method="post"><input type="submit" name="tcp_delete_shopping_cart" value="' . __( 'Delete', 'tcp' ) . '"/></form></li>';
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

function tcp_get_number_of_attachments( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$args = array(
		'post_type'		=> 'attachment',
		'numberposts'	=> -1,
		'post_status'	=> null,
		'post_parent'	=> $post_id,
		);
	$attachments = get_posts( $args );
	if ( is_array( $attachments ) )
		return count( $attachments );
	else
		return 0;
}

function tcp_get_sorting_fields() {
	$sorting_fields = array(
		array(
			'value'	=> 'order',
			'title'	=> __( 'Suggested', 'tcp' ),
		),
		array(
			'value'	=> 'price',
			'title' => __( 'Price', 'tcp' ),
		),
		array(
			'value'	=> 'title',
			'title'	=> __( 'Title', 'tcp' ),
		),
		array(
			'value'	=> 'author',
			'title'	=> __( 'Author', 'tcp' ),
		),
		array(
			'value'	=> 'date',
			'title'	=> __( 'Date', 'tcp' ),
		),
		array(
			'value'	=> 'comment_count',
			'title'	=> __( 'Popular', 'tcp' ),
		)
	);
	return apply_filters( 'tcp_sorting_fields', $sorting_fields );
}

function tcp_the_sort_panel() {
	$filter = new TCPFilterNavigation();
	$order_type = $filter->get_order_type();
	$order_desc = $filter->get_order_desc();
	$settings = get_option( 'ttc_settings' );
	$disabled_order_types = isset( $settings['disabled_order_types'] ) ? $settings['disabled_order_types'] : array();
	$sorting_fields = tcp_get_sorting_fields(); ?>
<div class="tcp_order_panel">
	<form method="post">
	<span class="tcp_order_type">
	<label for="tcp_order_type">
		<?php _e( 'Order by', 'tcp' ); ?>:&nbsp;
		<select id="tcp_order_type" name="tcp_order_type">
		<?php foreach( $sorting_fields as $sorting_field ) : 
			if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endif;
		endforeach; ?>
		</select>
	</label>
	</span><!-- .tcp_order_type -->
	<span class="tcp_order_desc">
	<label>
		<input type="radio" name="tcp_order_desc" id="tcp_order_asc" value="asc" <?php checked( $order_desc, 'asc' );?>/>
		<?php _e( 'Asc.', 'tcp' ); ?>
	</label>
	<label>
		<input type="radio" name="tcp_order_desc" id="tcp_order_desc" value="desc" <?php checked( $order_desc, 'desc' );?>/>
		<?php _e( 'Desc.', 'tcp' ); ?>
	</label>
	<span class="tcp_order_submit"><input type="submit" name="tcp_order_by" value="<?php _e( 'Order', 'tcp' );?>" /></span>
	</span><!-- .tcp_order_desc -->
	</form>
</div><!-- .tcp_order_panel --><?php
}

function tcp_attribute_list( $taxonomies = false ) {
	global $post;
	if ( $taxonomies === false ) $taxonomies = get_object_taxonomies( $post->post_type );
	if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) : ?>
		<table class="tcp_attribute_list">
		<tbody>
		<?php $par = true;
		foreach( $taxonomies as $tax ) :
			$taxonomy = get_taxonomy( $tax );
			$terms = wp_get_post_terms( $post->ID, $tax );
			if ( count( $terms ) > 0 ) : ?>
			<tr <?php if ( $par ) echo 'class="tcp_odd"'; $par = !$par; ?>>
				<th scope="row"><?php echo $taxonomy->labels->name; ?></th>
				<td><?php foreach( $terms as $term ) echo $term->name . '&nbsp;'; ?></td>
			</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php endif;
}
?>
