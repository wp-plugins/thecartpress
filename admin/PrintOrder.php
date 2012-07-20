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

$wordpress_path = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/';

include_once( $wordpress_path . 'wp-config.php' );
include_once( $wordpress_path . 'wp-includes/wp-db.php' );

$order_id	= isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : 0;

$current_user = wp_get_current_user();
if ( $current_user->ID == 0 ) {
	global $thecartpress;
	if ( $order_id != $thecartpress->getShoppingCart()->getOrderId() ) {
		return;
	}
} elseif ( ! current_user_can( 'tcp_edit_orders' ) ) {
	$thecartpress_path = $wordpress_path . '/wp-content/plugins/thecartpress/';
	require_once( $thecartpress_path . 'daos/Orders.class.php');
	if ( ! Orders::is_owner( $order_id, $current_user->ID ) ) {
		return;
	}
}
$file_name	= 'tcp_print_order.php';
$template	= get_stylesheet_directory() . '/' . $file_name;
$template	= apply_filters( 'tcp_get_print_order_template', $template, $order_id );
if ( file_exists( $template ) ) {
	include( $template );
} else {
	$template = get_template_directory() . '/' . $file_name;
	if ( get_stylesheet_directory() != get_template_directory() && file_exists( $template ) ) {
		include( $template );
	} else {
		$template = TCP_THEMES_TEMPLATES_FOLDER . $file_name;
		if ( file_exists( $template ) ) {
			include( $template );
		}
	}
}
?>