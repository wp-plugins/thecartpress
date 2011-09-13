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
	<a href="http://thecartpress.com/" target="_blank" title="<?php _e( 'link to TheCartPress site', 'tcp'); ?>"><img alt="" src="../wp-content/plugins/thecartpress/images/tcp_logo.png"></a>
</div>

<div class="table table_content">
	<table width="100%"><tbody>
	<?php $order_status_list = tcp_get_order_status();
	foreach ( $order_status_list as $order_status ) : 
		if ( $order_status['show_in_dashboard'] ) : ?>
	<tr class="first">
		<td class="first b"><a href="<?php echo $admin_path, 'OrdersListTable.class.php&status=', $order_status['name']; ?>"><?php echo Orders::getCountOrdersByStatus( $order_status['name'], $customer_id );?></a></td>
		<td class="t "><a href="<?php echo $admin_path, 'OrdersListTable.class.php&status=', $order_status['name']; ?>"><?php echo $order_status['label']; ?></a></td>
	</tr>
		<?php endif;
	endforeach; ?>
	</tbody></table>
	<hr style="color: white;">
	<p><a class="tcp_link_to_tcp" href="http://thecartpress.com/" target="_blank" title="<?php _e( 'link to TheCartPress site', 'tcp'); ?>"><?php _e( 'Visit TheCartPress site', 'tcp'); ?></a>
	| <a class="tcp_link_to_tcp" href="http://community.thecartpress.com/forums/" target="_blank" title="<?php _e( 'link to TheCartPress community', 'tcp'); ?>"><?php _e( 'Visit TheCartPress community', 'tcp'); ?></a></p>
</div><?php
	}
}
?>
