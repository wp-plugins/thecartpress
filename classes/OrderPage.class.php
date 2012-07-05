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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Shows an Order
 * It's used in the cart area (into the checkout), in the print page and in the email page
 */
class OrderPage {

	/**
	 * Prints an order
	 * @param order_id
	 * @param args $defaults = array(
	 *		'see_address'		=> true,
	 *		'see_sku'			=> false,
	 *		'see_weight'		=> true,
	 *		'see_tax'			=> true,
	 *		'see_comment'		=> true,
	 *		'see_other_costs'	=> true,
	 *		'see_thumbnail'		=> false
	 *	);
	 */
	static function show( $order_id, $args = array(), $echo = true ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
			$current_user = wp_get_current_user();
			if ( ! Orders::is_owner( $order_id, $current_user->ID ) ) return;
			if ( $current_user->ID == 0 ) {
				global $thecartpress;
				if ( $order_id != $thecartpress->getShoppingCart()->getOrderId() ) return;
			}
		}
		require_once( TCP_CLASSES_FOLDER . 'CartTable.class.php' );
		require_once( TCP_CLASSES_FOLDER . 'CartSourceDB.class.php' );
		$cart_table = new TCPCartTable();
		return $cart_table->show( new TCP_CartSourceDB( $order_id, $args ), $echo );
	}
}
?>
