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
?>
<div class="wrap">
	<h2><?php _e( 'Set up', 'tcp' );?></h2>
	<ul class="subsubsub">
	</ul>
	<div class="clear"></div>
<?php
global $thecartpress;

$disable_shopping_cart = isset( $thecartpress->settings['disable_shopping_cart'] ) ? $thecartpress->settings['disable_shopping_cart'] : false;
if ( ! $disable_shopping_cart ) :
	$warnings_msg = array();
	$shopping_cart_page_id = get_option( 'tcp_shopping_cart_page_id' );
	if ( ! $shopping_cart_page_id || ! get_page( $shopping_cart_page_id ) ) {
		$shopping_cart_page_id = TheCartPress::createShoppingCartPage();
		$warnings_msg[] = __( 'The Shopping Cart page has been created', 'tcp' );
	}
	$page_id = get_option( 'tcp_checkout_page_id' );
	if ( ! $page_id || ! get_page( $page_id ) ) {
		TheCartPress::createCheckoutPage( $shopping_cart_page_id );
		$warnings_msg[] = __( 'The Checkout page has been created', 'tcp' );
	}
	if ( count( $warnings_msg ) > 0 ) : ?>
		<ul id="tcp_fix_bug">
		<?php foreach( $warnings_msg as $msg ) :?>
			<li><?php echo $msg;?></li>
		<?php endforeach;?>
		</ul>
		<p class="description"><?php _e( 'All problems have been solved', 'tcp' );?></p>
		<script>
		jQuery('#message_checking_error').hide();
		</script>
	<?php endif; ?>
		<p class="description"><?php _e( 'The checking result is Ok.', 'tcp' );?></p>
<?php endif;?>
</div>
