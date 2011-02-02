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

require_once( 'OrdersDetails.class.php' );

class Orders {

	public static $ORDER_PENDING	= 'PENDING';
	public static $ORDER_PROCESSING	= 'PROCESSING';
	public static $ORDER_COMPLETED	= 'COMPLETED';
	public static $ORDER_CANCELLED	= 'CANCELLED';
	public static $ORDER_SUSPENDED	= 'SUSPENDED';

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_orders` (
		  `order_id`				bigint(20) unsigned NOT NULL auto_increment,
		  `created_at`				datetime			NOT NULL,
		  `customer_id`				bigint(20) unsigned NOT NULL,
		  `weight`					int(11)				NOT NULL default 0,
		  `shipping_method`			varchar(100)		NOT NULL,
		  `status`					varchar(50)			NOT NULL,
		  `order_currency_code`		char(3)				NOT NULL,
		  `shipping_amount`			decimal(13, 2)		NOT NULL default 0,
		  `discount_amount`			decimal(13, 2)		NOT NULL default 0,
		  `payment_name`			varchar(20)			NOT NULL,
  		  `payment_method`			varchar(100)		NOT NULL default \'\',
		  `payment_amount`			decimal(13, 2)		NOT NULL default 0,
		  `comment`					varchar(250)		NOT NULL,
		  `comment_internal`		varchar(250)		NOT NULL default \'\',
		  `code_tracking`			varchar(50)			NOT NULL,
		  `shipping_firstname`		varchar(50)			NOT NULL,
		  `shipping_lastname`		varchar(100)		NOT NULL,
		  `shipping_company`		varchar(50)			NOT NULL,
		  `shipping_street`			varchar(100)		NOT NULL,
		  `shipping_city`			varchar(100)		NOT NULL,
		  `shipping_city_id`		int(11) unsigned	NOT NULL default 0,
		  `shipping_region`			varchar(100)		NOT NULL,
		  `shipping_region_id`		int(11)	unsigned	NOT NULL default 0,
		  `shipping_postcode`		char(6)				NOT NULL,
 		  `shipping_country`		varchar(50)			NOT NULL,
		  `shipping_country_id`		char(2)				NOT NULL,
		  `shipping_telephone_1`	varchar(50)			NOT NULL,
		  `shipping_telephone_2`	varchar(50)			NOT NULL,
		  `shipping_fax`			varchar(50)			NOT NULL,
  		  `shipping_email`			varchar(50)			NOT NULL,
		  `billing_firstname`		varchar(50)			NOT NULL,
		  `billing_lastname`		varchar(100)		NOT NULL,
		  `billing_company`			varchar(50)			NOT NULL,
		  `billing_street`			varchar(100)		NOT NULL,
		  `billing_city`			varchar(100)		NOT NULL default 0,
		  `billing_city_id`			int(11)	unsigned	NOT NULL,
		  `billing_region`			varchar(100)		NOT NULL,
		  `billing_region_id`		int(11)	unsigned	NOT NULL default 0,
		  `billing_postcode`		char(6)				NOT NULL,
 		  `billing_country`			varchar(50)			NOT NULL,
		  `billing_country_id`		char(2)				NOT NULL,
		  `billing_telephone_1`		varchar(50)			NOT NULL,
		  `billing_telephone_2`		varchar(50)			NOT NULL,
		  `billing_fax`				varchar(50)			NOT NULL,
  		  `billing_email`			varchar(50)			NOT NULL,
		  PRIMARY KEY  (`order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function get( $order_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders where order_id = %d', $order_id ) );
	}

	static function is_owner( $order_id, $customer_id ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_orders where order_id = %d and customer_id = %d', $order_id, $customer_id ) );
		return $count == 1;
	}

	/**
	 * Inserts an order.
	 *
	 * @param $order is an array with all the values of the table orders.
	 */
	static function insert( $order ) {
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'tcp_orders', array(
			'created_at'			=> $order['created_at'],
			'customer_id'			=> $order['customer_id'],
			'weight'				=> $order['weight'],
			'shipping_method'		=> $order['shipping_method'],
			'status'				=> $order['status'],
			'order_currency_code'	=> $order['order_currency_code'],
			'shipping_amount'		=> $order['shipping_amount'],
			'discount_amount'		=> $order['discount_amount'],
			'payment_name'			=> $order['payment_name'],
			'payment_method'		=> $order['payment_method'],
			'payment_amount'		=> $order['payment_amount'],
			'comment'				=> $order['comment'],
			'comment_internal'		=> $order['comment_internal'],
			'code_tracking'			=> $order['code_tracking'],
			'shipping_firstname'	=> $order['shipping_firstname'],
			'shipping_lastname'		=> $order['shipping_lastname'],
			'shipping_company'		=> $order['shipping_company'],
			'shipping_street'		=> $order['shipping_street'],
			'shipping_city'			=> $order['shipping_city'],
			'shipping_city_id'		=> $order['shipping_city_id'],
			'shipping_region'		=> $order['shipping_region'],
			'shipping_region_id'	=> $order['shipping_region_id'],
			'shipping_postcode'		=> $order['shipping_postcode'],
			'shipping_country'		=> $order['shipping_country'],
			'shipping_country_id'	=> $order['shipping_country_id'],
			'shipping_telephone_1'	=> $order['shipping_telephone_1'],
			'shipping_telephone_2'	=> $order['shipping_telephone_2'],
			'shipping_fax'			=> $order['shipping_fax'],
			'shipping_email'		=> $order['shipping_email'],
			'billing_firstname'		=> $order['billing_firstname'],
			'billing_lastname'		=> $order['billing_lastname'],
			'billing_company'		=> $order['billing_company'],
			'billing_street'		=> $order['billing_street'],
			'billing_city'			=> $order['billing_city'],
			'billing_city_id'		=> $order['billing_city_id'],
			'billing_region'		=> $order['billing_region'],
			'billing_region_id'		=> $order['billing_region_id'],
			'billing_postcode'		=> $order['billing_postcode'],
			'billing_country'		=> $order['billing_country'],
			'billing_country_id'	=> $order['billing_country_id'],
			'billing_telephone_1'	=> $order['billing_telephone_1'],
			'billing_telephone_2'	=> $order['billing_telephone_2'],
			'billing_fax'			=> $order['billing_fax'],
			'billing_email'			=> $order['billing_email'],
		), array('%s', '%d', '%d', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%f', '%s', '%s', '%s',
				 '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s',
				 '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s',
				 '%s', '%s')
		);
		return $wpdb->insert_id;
	}

	static function getCountPendingOrders( $customer_id = -1 ) {
		return Orders::getCountOrdersByStatus( $customer_id, Orders::$ORDER_PENDING );
	}

	static function getCountProcessingOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( $customer_id, Orders::$ORDER_PROCESSING );
	}

	static function getCountCompletedOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( $customer_id, Orders::$ORDER_COMPLETED );
	}

	static function getCountCancelledOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( $customer_id, Orders::$ORDER_CANCELLED );
	}

	static function getCountSuspendedOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( $customer_id, Orders::$ORDER_SUSPENDED );
	}

	static function getCountOrdersByStatus( $customer_id = -1, $status = 'PENDING' ) {
		global $wpdb;
		$sql = 'select count(*) from ' . $wpdb->prefix . 'tcp_orders where status=%s';
		if ( $customer_id > -1 ) $sql .= ' and customer_id = %d';
		return $wpdb->get_var( $wpdb->prepare( $sql, $status, $customer_id ) );
	}

	/**
	 * Returns a join between orders and details orders
	 */
	static function getOrders( $status = 'PENDING', $customer_id = -1 ) {
		global $wpdb;
		$sql = 'select o.order_id, od.order_detail_id, shipping_firstname,
				shipping_lastname, created_at, status, post_id, price, tax,
				qty_ordered, shipping_amount, payment_method, payment_amount,
				order_currency_code, code_tracking, is_downloadable,
				max_downloads, expires_at
				from ' . $wpdb->prefix . 'tcp_orders o left join '.
				$wpdb->prefix . 'tcp_orders_details od
				on o.order_id = od.order_id';
		if ( strlen( $status ) > 0 )
			if ( $customer_id > -1 )
				$sql = $wpdb->prepare( $sql . ' where status = %s and customer_id = %d', $status, $customer_id );
			else
				$sql = $wpdb->prepare( $sql . ' where status = %s', $status );
		elseif ( $customer_id > -1 )
			$sql = $wpdb->prepare( $sql . ' where customer_id = %d', $customer_id );
		$sql .= ' order by created_at desc';
		return $wpdb->get_results( $sql );
	}
	
	static function quickEdit( $order_id, $new_status, $new_code_tracking ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'		=> $new_status,
				'code_tracking'	=> $new_code_tracking,
			),
			array(
				'order_id'		=> $order_id,
			), 
			array( '%s', '%s' ), array( '%d' ) );
		Orders::edit_downloadable_details( $order_id, $new_status );
	}

	static function edit( $order_id, $new_status, $new_code_tracking, $comment, $comment_internal ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'			=> $new_status,
				'code_tracking'		=> $new_code_tracking,
				'comment'			=> $comment,
				'comment_internal'	=> $comment_internal,
			),
			array(
				'order_id'		=> $order_id,
			), 
			array( '%s', '%s', '%s', '%s' ), array( '%d' ) );
		Orders::edit_downloadable_details( $order_id, $new_status );
	}

	static function editStatus( $order_id, $new_status, $comment_internal = '' ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'			=> $new_status,
				'comment_internal'	=> $comment_internal,
			),
			array(
				'order_id'		=> $order_id,
			), 
			array( '%s', '%s' ), array( '%d' ) );
		Orders::edit_downloadable_details( $order_id, $new_status );
	}

	static function edit_downloadable_details( $order_id, $new_status ) {
		if ( $new_status ==  Orders::$ORDER_COMPLETED ) {
			$details = OrdersDetails::getDetails( $order_id );
			foreach( $details as $detail ) {
				$days_to_expire = get_post_meta( $detail->post_id, 'tcp_days_to_expire', true );
				if ( $days_to_expire > 0 ) {
					$today = date( 'Y-m-d' );
					$expires_at = date ( 'Y-m-d', strtotime( date( 'Y-m-d', strtotime( $today ) ) . " +$days_to_expire day" ) );
					OrdersDetails::edit_downloadable_data( $detail->order_detail_id, $expires_at );
				}
			}
		}
	}

	static function isDownloadable( $order_id ) {
		return OrdersDetails::isDownloadable( $order_id );
	}

	static function getProductsDownloadables( $customer_id ) {
		global $wpdb;
		$today = date ( 'Y-m-d' );
		$max_date = date ( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
		$sql = $wpdb->prepare( 'select o.order_id as order_id, order_detail_id, post_id, expires_at, max_downloads from 
			' . $wpdb->prefix . 'tcp_orders o left join ' . $wpdb->prefix . 'tcp_orders_details d 
			on o.order_id = d.order_id
			where customer_id = %d and d.is_downloadable = \'Y\' and status=\'COMPLETED\'
			and ( ( d.expires_at > %s and ( d.max_downloads = -1 or d.max_downloads > 0 ) )
				or ( d.expires_at = %s and ( d.max_downloads > 0 or d.max_downloads = -1 ) ) )'
			, $customer_id, $today, $max_date );
		return $wpdb->get_results( $sql );
	}

	static function isProductDownloadable( $customer_id, $orders_details_id ) {
		global $wpdb;
		$today = date ( 'Y-m-d' );
		$max_date = date ( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
		$sql = $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_orders o
			left join ' . $wpdb->prefix . 'tcp_orders_details d on o.order_id = d.order_id
			where customer_id = %d and order_detail_id = %d and d.is_downloadable = \'Y \' and status=\'COMPLETED\'
			and ( ( d.expires_at > %s and ( d.max_downloads = -1 or d.max_downloads > 0 ) )
				or ( d.expires_at = %s and ( d.max_downloads > 0 or d.max_downloads = -1 ) ) )'
			, $customer_id, $orders_details_id, $today, $max_date );

		$count = $wpdb->get_var( $sql );
		return $count > 0;
	}

	static function takeAwayDownload( $order_detail_id ) {
		global $wpdb;

		$sql = 'update ' . $wpdb->prefix . 'tcp_orders_details set 
			max_downloads = max_downloads - 1 where order_detail_id = %d';
		$wpdb->query( $wpdb->prepare( $sql, $order_detail_id ) );

	}
}
?>
