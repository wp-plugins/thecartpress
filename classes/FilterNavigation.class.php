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

class TCPFilterNavigation {
	private $min_price = 0;
	private $max_price = 0;
	private $filter_by_price_range = false;
	private $order_type;
	private $order_desc;
	
	function __construct() {
		if ( ! isset( $_SESSION['tcp_filter'] ) ) $_SESSION['tcp_filter'] = array();
		$filter = $_SESSION['tcp_filter'];
		if ( isset( $_REQUEST['tcp_min_price'] ) && isset( $_REQUEST['tcp_max_price'] ) ) {
			$this->min_price = $_REQUEST['tcp_min_price'];
			$this->max_price = $_REQUEST['tcp_max_price'];
			$filter['tcp_min_price'] = $this->min_price;
			$filter['tcp_max_price'] = $this->max_price;
			$this->filter_by_price_range = true;
		} elseif ( isset( $filter['tcp_min_price'] ) && isset( $filter['tcp_max_price'] ) ) {
				$this->min_price = $filter['tcp_min_price'];
				$this->max_price = $filter['tcp_max_price'];
				$this->filter_by_price_range = true;
		} else {
			$this->filter_by_price_range = false;
		}

		if ( isset( $_REQUEST['tcp_order_type'] ) && isset( $_REQUEST['tcp_order_desc'] ) ) {
			$filter['order_type'] = isset( $_REQUEST['tcp_order_type'] ) ? $_REQUEST['tcp_order_type'] : 'order';
			$filter['order_desc'] = isset( $_REQUEST['tcp_order_desc'] ) ? $_REQUEST['tcp_order_desc'] : 'asc';
		} elseif ( isset( $filter['order_type'] ) && isset( $filter['order_desc'] ) ) {
		} else {
			$settings = get_option( 'ttc_settings' );
			$filter['order_type'] = isset( $settings['order_type'] ) ? $settings['order_type'] : 'order';
			$filter['order_desc'] = isset( $settings['order_desc'] ) ? $settings['order_desc'] : 'asc';
		}
		$this->order_type = $filter['order_type'];
		$this->order_desc = $filter['order_desc'];
		$_SESSION['tcp_filter'] = $filter;
	}

	function is_filter_by_price_range() {
		return $this->filter_by_price_range;
	}

	function get_min_price() {
		return $this->min_price;
	}

	function get_max_price() {
		return $this->max_price;
	}

	function get_order_type() {
		return $this->order_type;
	}

	function get_order_desc() {
		return $this->order_desc;
	}
}
?>
