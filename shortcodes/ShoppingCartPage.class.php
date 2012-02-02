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

class TCPShoppingCartPage {

	function __construct() {
		add_shortcode( 'tcp_shopping_cart', array( $this, 'show' ) );
	}

	function show( $notice = '' ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/CartTable.class.php' );
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/CartSourceSession.class.php' );
		$cart_table = new TCPCartTable( ); 
		ob_start(); ?>
<div class="tcp_shopping_cart_page">
<?php  if ( $shoppingCart->isEmpty() ) : ?>
	<span class="tcp_shopping_cart_empty"><?php echo __( 'The cart is empty', 'tcp' );?></span>
<?php else : ?>
	<div class="entry-content" id="shopping_cart">
	<?php if ( strlen( $notice ) > 0 ) : ?>
		<p class="tcp_shopping_cart_notice"><?php echo $notice; ?></p>
	<?php endif;
	do_action( 'tcp_shopping_cart_before_cart' );
	$cart_table->show( new TCPCartSourceSession() );
	do_action( 'tcp_shopping_cart_after_cart' ); ?>
		<ul class="tcp_sc_links">
			<li class="tcp_sc_checkout"><a href="<?php tcp_the_checkout_url();?>"><?php _e( 'Checkout', 'tcp' );?></a></li>
			<li class="tcp_sc_continue"><a href="<?php tcp_the_continue_url();?>"><?php _e( 'Continue shopping', 'tcp' );?></a></li>
			<?php do_action( 'tcp_shopping_cart_after_links' );?>
		</ul>
	</div><!-- .entry-content -->
	<?php endif; ?>
</div><!-- .tcp_shopping_cart_page -->
<?php return ob_get_clean();
	}

	//TODO to remove!!!
	/*function show_old( $notice = '' ) {
		global $thecartpress;
		$shoppingCart		= TheCartPress::getShoppingCart();
		ob_start(); ?>
		<div class="tcp_shopping_cart_page">
		<?php if ( $shoppingCart->isEmpty() ) : ?>
			<span class="tcp_shopping_cart_empty"><?php echo __( 'The cart is empty', 'tcp' );?></span>
		<?php else : ?>
			<div class="entry-content" id="shopping_cart">
				<?php if ( strlen( $notice ) > 0 ) {
					echo '<p class="tcp_shopping_cart_notice">', $notice, '</p>';
				};
				do_action( 'tcp_shopping_cart_before_cart' );?>
				<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
				<tbody>
				<tr class="tcp_cart_title_row">
					<th><?php echo __( 'Name', 'tcp' );?></th>
					<th><?php echo __( 'Price', 'tcp' );?></th>
					<th><?php echo __( 'Units', 'tcp' );?></th>
					<th colspan="2"><?php echo __( 'Subtotal', 'tcp' );?></th>
				</tr>
			<?php foreach( $shoppingCart->getItems() as $item ) : ?>
				<tr class="tcp_cart_product_row">
					<td class="tcp_cart_name">
					<?php if ( tcp_is_visible( $item->getPostId() ) ) : ?>
						<a href="<?php echo get_permalink( tcp_get_current_id( $item->getPostId(), get_post_type( $item->getPostId() ) ) );?>"><?php echo tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );?>
					<?php else :
						$post_id = tcp_get_the_parent( $item->getPostId() );
						if ( $post_id > 0 ) : ?>
							<a href="<?php echo get_permalink( tcp_get_current_id( $post_id, get_post_type( $post_id ) ) );?>"><?php echo tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );?>
						<?php else : ?>
							<a href="<?php echo get_permalink( tcp_get_current_id( $item->getPostId(), get_post_type( $item->getPostId() ) ) );?>"><?php echo tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );?>
						<?php endif;
					endif;?>
					<?php //if ( $item->getOption1Id() > 0 ) echo '<br />', get_the_title( tcp_get_current_id( $item->getOption1Id(), get_post_type( $item->getOption1Id() ) ) );?>
					<?php //if ( $item->getOption2Id() > 0 ) echo '-', get_the_title( tcp_get_current_id( $item->getOption2Id(), get_post_type( $item->getOption1Id() ) ) );?></a>
					</td>
					<td class="tcp_cart_unit_price">
						<?php echo tcp_format_the_price( $item->getPriceToShow() ); ?>
						<?php if ( $item->getDiscount() > 0 ) :?>
						<span class="tcp_cart_discount"><?php printf( __( 'Discount %s', 'tcp' ), tcp_format_the_price( $item->getDiscount() ) );?></span>
						<?php endif;?>
					</td>
					<form method="post">
						<td class="tcp_cart_units">
							<input type="hidden" name="tcp_post_id" id="tcp_post_id" value="<?php echo $item->getPostId();?>" />
							<input type="hidden" name="tcp_option_1_id" id="tcp_option_1_id" value="<?php echo $item->getOption1Id();?>" />
							<input type="hidden" name="tcp_option_2_id" id="tcp_option_2_id" value="<?php echo $item->getOption2Id();?>" />
						<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
							<input name="tcp_count" id="tcp_count" value="<?php echo $item->getCount();?>" size="2" maxlength="4" type="text" />
							<input name="tcp_modify_item_shopping_cart" value="<?php echo __( 'Modify', 'tcp' );?>" type="submit" />
						<?php else : ?>
								1&nbsp;
						<?php endif;?>
						<input name="tcp_delete_item_shopping_cart" value="<?php echo __( 'Delete', 'tcp' );?>" type="submit" />
						<?php do_action( 'tcp_cart_units', $item ); ?>
						</td>
					</form>
					<td class="tcp_cart_price">
						<?php echo tcp_format_the_price( $item->getTotalToShow() );?>
					</td>
				</tr>
			<?php endforeach;?>
			<?php $discount = $shoppingCart->getCartDiscountsTotal();
			$total = $shoppingCart->getTotalToShow();
			if ( $discount > 0) : ?>
				<tr class="tcp_cart_subtotal_row">
					<td colspan="3" class="tcp_cart_subtotal_title"><?php echo __( 'Subtotal', 'tcp' );?></td>
					<td class="tcp_cart_subtotal"><?php echo tcp_format_the_price( $total + $discount );?></td>
				</tr>
				<tr class="tcp_cart_discount_row">
					<td colspan="3" class="tcp_cart_discount_title"><?php echo __( 'Discount', 'tcp' );?></td>
					<td class="tcp_cart_discount"><?php echo tcp_format_the_price( $discount );?></td>
				</tr>
			<?php endif;?>
				<tr class="tcp_cart_total_row">
					<td colspan="3" class="tcp_cart_total_title"><?php echo __( 'Total', 'tcp' );?></td>
					<td class="tcp_cart_total"><?php echo tcp_format_the_price( $total );?></td>
				</tr>
			</tbody>
			</table>

			<ul class="tcp_sc_links">
				<li class="tcp_sc_checkout"><a href="<?php tcp_the_checkout_url();?>"><?php _e( 'Checkout', 'tcp' );?></a></li>
				<li class="tcp_sc_continue"><a href="<?php tcp_the_continue_url();?>"><?php _e( 'Continue shopping', 'tcp' );?></a></li>
				<?php do_action( 'tcp_shopping_cart_after_links' );?>
			</ul>
		</div><!-- .entry-content -->
		<?php endif;
		do_action( 'tcp_shopping_cart_after_cart' ); ?>
		</div><!-- .tcp_shopping_cart_page -->
		<?php return ob_get_clean();
	}*/
}

new TCPShoppingCartPage();
?>
