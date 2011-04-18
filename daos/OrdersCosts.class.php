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

class OrdersCosts {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_orders_costs` (
		  `order_cost_id`		bigint(20) unsigned NOT NULL auto_increment,
		  `order_id`			bigint(20) unsigned NOT NULL,
		  `description`			varchar(255)		NOT NULL,
		  `cost`				decimal(13,2)		NOT NULL default 0,
		  `cost_order`			varchar(4) 			NOT NULL default \'\',
		  PRIMARY KEY  (`order_cost_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function get( $order_cost_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders_costs where order_cost_id = %d', $order_cost_id ) );
	}

	static function getCosts( $order_id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders_costs where order_id = %d order by cost_order', $order_id ) );
	}

	static function getTotalCost( $order_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'select sum(cost) from ' . $wpdb->prefix . 'tcp_orders_costs where order_id = %d', $order_id ) );
	}
	static function insert( $ordersCosts ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_orders_costs', array (
				'order_id'			=> $ordersCosts['order_id'],
				'description'		=> $ordersCosts['description'],
				'cost'				=> $ordersCosts['cost'],
				'cost_order'		=> $ordersCosts['cost_order'],
			),
			array( '%d', '%s', '%f', '%s' )
		);
		return $wpdb->insert_id;
	}
}
?>
