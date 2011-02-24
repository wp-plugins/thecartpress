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

class ShoppingCartPage {

	function show( $notice = '') {
		$settings = get_option( 'tcp_settings' );
		$currency = isset( $settings['currency'] ) ? $settings['currency']: 'EUR';
		$stock_management = isset( $settings['stock_management'] ) ? $settings['stock_management'] : false;
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( $shoppingCart->isEmpty() ) :?>
			<span class="tcp_shopping_cart_empty"><?php echo __( 'The cart is empty', 'tcp' );?></span>
		<?php else :?>
			<div class="entry-content" id="shopping_cart">
				<?php if ( strlen( $notice ) > 0 ) {
					echo '<p class="tcp_shopping_cart_notice">', $notice, '</p>';
				};?>
				<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
				<tbody>
				<tr class="tcp_cart_title_row">
					<th><?php echo __( 'Name', 'tcp' );?></th>
					<th><?php echo __( 'Price', 'tcp' );?></th>
					<th><?php echo __( 'Units', 'tcp' );?></th>
					<th colspan="2"><?php echo __( 'Subtotal', 'tcp' );?></th>
				</tr>
			<?php $total = 0;
			foreach( $shoppingCart->getItems() as $item ) :?>
				<tr class="tcp_cart_product_row">
					<td class="tcp_cart_name">
					<?php if ( tcp_is_visible( $item->getPostId() ) ) : ?>
						<a href="<?php echo get_permalink( $item->getPostId() );?>"><?php echo get_the_title( tcp_get_current_id( $item->getPostId() ) );?>
					<?php else :
						$post_id = tcp_get_the_parent( $item->getPostId() );
						if ( $post_id > 0 ) : ?>
							<a href="<?php echo get_permalink( $post_id );?>"><?php echo get_the_title( tcp_get_current_id( $item->getPostId() ) );?>
						<?php else : ?>
							<a href="<?php echo get_permalink( $item->getPostId() );?>"><?php echo get_the_title( tcp_get_current_id( $item->getPostId() ) );?>
						<?php endif;
					endif;?>
					<?php if ( $item->getOption1Id() > 0 ) echo '<br />', get_the_title( tcp_get_current_id( $item->getOption1Id() ) );?>
					<?php if ( $item->getOption2Id() > 0 ) echo '-', get_the_title( tcp_get_current_id( $item->getOption2Id() ) );?></a>
					</td>
					<td class="tcp_cart_unit_price">
						<?php echo number_format( $item->getUnitPrice(), 2 ), '&nbsp;', $currency, '&nbsp;(', number_format( $item->getTax(), 0 ), '%)';?>
					</td>
					<form method="post">
						<td class="tcp_cart_units">
							<input type="hidden" name="tcp_post_id" id="tcp_post_id" value="<?php echo $item->getPostId();?>" />
							<input type="hidden" name="tcp_option_1_id" id="tcp_option_1_id" value="<?php echo $item->getOption1Id();?>" />
							<input type="hidden" name="tcp_option_2_id" id="tcp_option_2_id" value="<?php echo $item->getOption2Id();?>" />
						<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
							<input name="tcp_count" id="tcp_count" value="<?php echo $item->getCount();?>" size="3" maxlength="4" type="text" />
							<input name="tcp_modify_item_shopping_cart" value="<?php echo __( 'Modify', 'tcp' );?>" type="submit" />
							<input name="tcp_delete_item_shopping_cart" value="<?php echo __( 'Delete', 'tcp' );?>" type="submit" />
						<?php else : ?>
								1
						<?php endif;?>
					<?php if ( $stock_management ) :
						$stock = tcp_get_the_stock( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
						if ( $stock == 0 ) : ?>
							<span class="tcp_no_stock"><?php _e( 'Out of stock', 'tcp' );?></span>
						<?php elseif ( $stock != -1 && $stock < $item->getCount() ) : ?>
							<span class="tcp_no_stock_enough"><?php printf( __( 'No enough stock. Only %s items available.', 'tcp' ), $stock );?></span>
						<?php endif;
					endif;?>
						</td>
					</form>
					<td class="tcp_cart_price">
						<?php $total += $item->getTotal();?>
						<?php echo number_format( $item->getTotal(), 2 );?>&nbsp;&euro;
					</td>
				</tr>
			<?php endforeach;?>
				<tr class="tcp_cart_subtotal_row">
					<td colspan="3" class="tcp_cart_subtotal_title"><?php echo __( 'Subtotal', 'tcp' );?></td>
					<td class="tcp_cart_subtotal"><?php echo number_format( $total, 2 );?>&nbsp;&euro;</td>
				</tr>
				<tr class="tcp_cart_total_row">
					<td colspan="3" class="tcp_cart_total_title"><?php echo __( 'Total', 'tcp' );?></td>
					<td class="tcp_cart_total"><?php echo number_format( $total, 2 );?>&nbsp;&euro;</td>
				</tr>
			</tbody>
			</table>
			<ul class="tcp_sc_links">
				<li class="tcp_sc_checkout"><a href="<?echo get_permalink( get_option( 'tcp_checkout_page_id' ) );?>"><?php _e( 'Checkout', 'tcp' );?></a></li>
				<li class="tcp_sc_continue"><a href="<?echo get_home_url();?>"><?php _e( 'Continue shopping', 'tcp' );?></a></li>
				<li class="tcp_sc_delete_all"><form method="post"><input type="submit" id="tcp_delete_shopping_cart" name="tcp_delete_shopping_cart" value="<?php _e( 'Delete shopping cart', 'tcp' );?>"/></form></li>
			</ul>
		</div>
	<?php endif;
	}
}
?>
