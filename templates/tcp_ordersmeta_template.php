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

global $wpdb;
$variable_name = 'tcp_orders' . 'meta';
$wpdb->$variable_name = $wpdb->prefix . $variable_name;

function add_order_meta( $order_id, $meta_key, $meta_value, $unique = true ) {
	return add_metadata( 'tcp_orders', $order_id, $meta_key, $meta_value, $unique );
}

function delete_order_meta( $order_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'tcp_orders', $order_id, $meta_key, $meta_value );
}

function get_order_meta( $order_id, $meta_key, $single = true ) {
	return get_metadata( 'tcp_orders', $order_id, $meta_key, $single );
}

function update_order_meta( $order_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'tcp_orders', $order_id, $meta_key, $meta_value, $prev_value );
}
?>
