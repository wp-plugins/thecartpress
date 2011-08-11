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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );

class OrdersSummaryDashboard {

	function show() {
		if ( current_user_can( 'manage_options' ) ) {
			$customer_id = -1;
		} else {
			global $current_user;
			get_currentuserinfo();
			$customer_id = $current_user->ID;
		}	
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';?>
		<div class="alignright browser-icon">
		<a href="http://thecartpress.com/" target="_blank"><img alt="" src="../wp-content/plugins/thecartpress/images/tcp_logo.png"></a>
		</div>
<div class="table table_content">
	<table width="100%"><tbody>
	<tr class="first">
		<td class="first b"><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=PENDING';?>"><?php echo Orders::getCountPendingOrders( $customer_id );?></a></td>
		<td class="t "><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=PENDING';?>"><?php _e( 'Pending orders', 'tcp' );?></a></td>
	</tr><tr>
		<td class="first b"><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=PROCESSING';?>"><?php echo Orders::getCountProcessingOrders( $customer_id );?></a></td>
		<td class="t "><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=PROCESSING';?>"><?php _e( 'Processing orders', 'tcp' );?></a></td>
	</tr><tr>
		<td class="first b"><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=COMPLETED';?>"><?php echo Orders::getCountCompletedOrders( $customer_id );?></a></td>
		<td class="t "><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=COMPLETED';?>"><?php _e( 'Completed orders', 'tcp' );?></a></td>
	</tr>
	</tr><tr>
		<td class="first b"><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=SUSPENDED';?>"><?php echo Orders::getCountSuspendedOrders( $customer_id );?></a></td>
		<td class="t "><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=SUSPENDED';?>"><?php _e( 'Suspended orders', 'tcp' );?></a></td>
	</tr>
	</tr><tr>
		<td class="first b"><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=CANCELLED';?>"><?php echo Orders::getCountCancelledOrders( $customer_id );?></a></td>
		<td class="t "><a href="<?php echo $admin_path . 'OrdersListTable.class.php&status=CANCELLED';?>"><?php _e( 'Cancelled orders', 'tcp' );?></a></td>
	</tr>
	</tbody></table>
</div><?php
	}
}
?>
