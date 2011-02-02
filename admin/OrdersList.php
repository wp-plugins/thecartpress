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

require_once( dirname( dirname( __FILE__ ) ).'/daos/Orders.class.php' );

$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';

$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';

if ( is_user_logged_in() )
	if ( current_user_can( 'tcp_edit_orders' ) ) {
		if ( isset( $_REQUEST['tcp_quick_order_edit'] ) ) {
			Orders::quickEdit( $_REQUEST['order_id'], $_REQUEST['new_status'], $_REQUEST['new_code_tracking'] );?>
			<div id="message" class="updated"><p>
				<?php _e( 'Order saved', 'tcp' );?>
			</p></div><?php
		}
		$orders_db = Orders::getOrders( $status );
	} else {
		global $current_user;
		get_currentuserinfo();
		$orders_db = Orders::getOrders( $status, $current_user->ID );
	}
else return;

$orders = array();
if ( is_array( $orders_db ) && count( $orders_db ) > 0 )
	foreach( $orders_db as $order ) {
		$orders[] = array(
			'order_id'		=> $order->order_id,
			'date'			=> $order->created_at,
			'user'			=> $order->shipping_firstname . ' ' . $order->shipping_lastname,
			'status'		=> $order->status,
			'total'			=> ($order->price * (1 + $order->tax / 100)) * $order->qty_ordered + $order->shipping_amount + $order->payment_amount,
			'code_tracking'	=> $order->code_tracking,
			'payment_method'=> $order->payment_method,
		);
	}?>
<div class="wrap">

<h2><?php echo __( 'Orders', 'tcp' );?></h2>

<div class="clear"></div>

<form method="post">
<div class="tablenav">
<p class="search-box">
<label for="status"><?php _e( 'Status', 'tcp' );?>:</label>
	<select class="postform" id="status" name="status">
		<option value="" <?php selected( '', $status );?>><?php _e( 'all', 'tcp' );?></option>
		<option value="<?php echo Orders::$ORDER_PENDING;?>" <?php selected( Orders::$ORDER_PENDING, $status );?>><?php _e( 'pending', 'tcp' );?></option>
		<option value="<?php echo Orders::$ORDER_PROCESSING;?>" <?php selected( Orders::$ORDER_PROCESSING, $status );?>><?php _e( 'processing', 'tcp' );?></option>
		<option value="<?php echo Orders::$ORDER_COMPLETED;?>" <?php selected( Orders::$ORDER_COMPLETED, $status );?>><?php _e( 'completed', 'tcp' );?></option>
		<option value="<?php echo Orders::$ORDER_CANCELLED;?>" <?php selected( Orders::$ORDER_CANCELLED, $status );?>><?php _e( 'cancelled', 'tcp' );?></option>
		<option value="<?php echo Orders::$ORDER_SUSPENDED;?>" <?php selected( Orders::$ORDER_SUSPENDED, $status );?>><?php _e( 'suspended', 'tcp' );?></option>
	</select>
	<input type="submit" class="button-secondary" value="<?php _e( 'Filter', 'tcp' );?>" id="post-query-submit" />
</p>
</div>
</form>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Date', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'User', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'payment', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Total', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Date', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'User', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'payment', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Total', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</tfoot>
<tbody>

<?php if ( count( $orders ) == 0 ) :?>
	<tr><td colspan="5"><?php _e( 'The list of orders is empty', 'tcp' );?></td></tr>
<?php else :
	$order_lines = array();
	$order_id ='';
	foreach( $orders as $order ) :
		if ( $order_id != $order['order_id'] ) {
			$order_lines[$order['order_id']] = array(
				'order_id'		=> $order['order_id'],
				'date'			=> $order['date'],
				'user'			=> $order['user'],
				'status'		=> $order['status'],
				'payment_method'=> $order['payment_method'],
				'code_tracking' => $order['code_tracking'],
				'total'			=> $order['total'],
			);
			$order_id = $order['order_id'];
		} else {
			$order_lines[$order['order_id']]['total'] += $order['total'];
		}
	endforeach;
	foreach( $order_lines as $order ) :?> 
	<tr>
		<td><?php echo $order['date'];?></td>
		<td><?php echo $order['user'];?></td>
		<td>
			<?php echo $order['status'];?>
			<?php do_action( 'tcp_admin_order_list', $order['order_id'] );?>
		</td>
		<td><?php echo $order['payment_method'];?></td>
		<td><?php echo $order['total'];?></td>
		<td style="width: 20%;">
		<?php if ( current_user_can( 'tcp_edit_orders' ) ) : ?>
			<div><a href="<?php echo $admin_path;?>OrderEdit.php&order_id=<?php echo $order['order_id'];?>&status=<?php echo $status;?>"><?php _e( 'edit/view', 'tcp' );?> | <a href="#" onclick="jQuery('.quick_edit').hide();jQuery('#quick_<?php echo $order['order_id'];?>').show();"><?php _e( 'quick edit', 'tcp' );?></a></div>
		<?php else : ?>
			&nbsp;
		<?php endif;?>
		</td>
	</tr>
		<?php if ( current_user_can( 'tcp_edit_orders' ) ) : ?>
	<tr id="quick_<?php echo $order['order_id'];?>" class="quick_edit" style="display:none;">
		<td colspan="5">
			<form method="post">
			<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
			<h4><?php _e( 'Quick Edit', 'tcp' );?></h4>

			<label>
				<span class="title"><?php _e( 'Status', 'tcp' );?></span>
				<span class="input-text-wrap">
				<select class="postform" id="new_status" name="new_status">
					<option value="<?php echo Orders::$ORDER_PENDING;?>"	<?php selected( Orders::$ORDER_PENDING,		$order['status'] );?>	><?php _e( 'pending', 'tcp' );?></option>
					<option value="<?php echo Orders::$ORDER_PROCESSING;?>"	<?php selected( Orders::$ORDER_PROCESSING,	$order['status'] );?>	><?php _e( 'processing', 'tcp' );?></option>
					<option value="<?php echo Orders::$ORDER_COMPLETED;?>"	<?php selected( Orders::$ORDER_COMPLETED,	$order['status'] );?>	><?php _e( 'completed', 'tcp' );?></option>
					<option value="<?php echo Orders::$ORDER_CANCELLED;?>"	<?php selected( Orders::$ORDER_CANCELLED,	$order['status'] );?>	><?php _e( 'cancelled', 'tcp' );?></option>
					<option value="<?php echo Orders::$ORDER_SUSPENDED;?>"	<?php selected( Orders::$ORDER_SUSPENDED,	$order['status'] );?>	><?php _e( 'suspended', 'tcp' );?></option>
				</select>
				</span>
			</label>
			<label>
				<span class="title"><?php _e( 'code tracking', 'tcp' );?></span>
				<span class="input-text-wrap"><input name="new_code_tracking" id="new_code_tracking" type="text" size="10" maxlength="50" value="<?php echo $order['code_tracking'];?>" /></span>
			</label>
			<input type="hidden" name="status" value="<?php echo $status;?>" />
			<input type="hidden" name="order_id" value="<?php echo $order['order_id'];?>" />
			
			<p class="submit inline-edit-save">
			<a class="button-secondary cancel alignleft" title="Cancel" href="#inline-edit" accesskey="c" 
			onclick="jQuery('#quick_<?php echo $order['order_id'];?>').hide();"><?php _e( 'cancel', 'tcp' );?></a>&nbsp;
			<input name="tcp_quick_order_edit" value="<?php _e( 'save', 'tcp' );?>" type="submit" class="button-primary" />
			<br class="clear">
			</p>
			</form>
		</td>
	</tr>
		<?php endif;?>
	<?php endforeach;
endif;?>
</tbody>
</table>

</div> <!-- end wrap -->
