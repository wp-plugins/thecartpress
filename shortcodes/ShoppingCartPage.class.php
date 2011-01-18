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

	function show() {
		$currency = 'EUR';
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( $shoppingCart->isEmpty() ) :?>
			<span class="tcp_shopping_cart_empty"><?php echo __( 'The cart is empty', 'tcp' );?></span>
		<?php else :?>
			<div class="entry-content">
				<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
				<tbody>
				<tr class="tcp_cart_title_row">
					<th><?php echo __( 'name', 'tcp' );?></th>
					<th><?php echo __( 'price', 'tcp' );?></th>
					<th><?php echo __( 'units', 'tcp' );?></th>
					<th colspan="2"><?php echo __( 'subtotal', 'tcp' );?></th>
				</tr>
			<?php $total = 0;
			foreach( $shoppingCart->getItems() as $item ) :?>
				<tr class="tcp_cart_product_row">
					<td class="tcp_cart_name">
						<a href="<?php echo get_permalink( $item->getPostId() );?>"><?php echo get_the_title( $item->getPostId() );?>
						<?php if ( $item->getOption1Id() > 0 ) echo '<br />', get_the_title( $item->getOption1Id() );?>
						<?php if ( $item->getOption2Id() > 0 ) echo '-', get_the_title( $item->getOption2Id() );?></a>
					</td>
					<td class="tcp_cart_unit_price">
						<?php echo number_format( $item->getUnitPrice(), 2 ), '&nbsp;', $currency, '&nbsp;(', number_format( $item->getTax(), 0 ), '%)';?>
					</td>
					<td class="tcp_cart_units">
						<form method="post">
							<input type="hidden" name="tcp_post_id" id="tcp_post_id" value="<?php echo $item->getPostId();?>" />
							<input type="hidden" name="tcp_option_1_id" id="tcp_option_1_id" value="<?php echo $item->getOption1Id();?>" />
							<input type="hidden" name="tcp_option_2_id" id="tcp_option_2_id" value="<?php echo $item->getOption2Id();?>" />
						<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
							<input name="tcp_count" id="tcp_count" value="<?php echo $item->getCount();?>" size="2" maxlength="3" type="text" />
							<input name="tcp_modify_item_shopping_cart" value="<?php echo __( 'modify', 'tcp' );?>" type="submit" />
						<?php else : ?>
							1
						<?php endif;?>
						</form>
					</td>
					<td class="tcp_cart_price">
						<?php $total += $item->getTotal();?>
						<?php echo number_format( $item->getTotal(), 2 );?>&nbsp;&euro;
					</td>
				</tr>
			<?php endforeach;?>
				<tr class="tcp_cart_subtotal_row">
					<td colspan="3" class="tcp_cart_subtotal_title"><?php echo __( 'subtotal', 'tcp' );?></td>
					<td class="tcp_cart_subtotal"><?php echo number_format( $total, 2 );?>&nbsp;&euro;</td>
				</tr>
				<tr class="tcp_cart_total_row">
					<td colspan="3" class="tcp_cart_total_title"><?php echo __( 'total', 'tcp' );?></td>
					<td class="tcp_cart_total"><?php echo number_format( $total, 2 );?>&nbsp;&euro;</td>
				</tr>
			</tbody>
			</table>
		</div>
	<?php endif;
	}
}
?>
