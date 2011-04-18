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
require_once( dirname( dirname( __FILE__ ) ) . '/classes/OrderPage.class.php' );

$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';

$order_id = isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : '';
$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';

if ( isset( $_REQUEST['tcp_order_edit'] ) && current_user_can( 'tcp_edit_orders' ) ) {
	Orders::edit( $order_id, $_REQUEST['new_status'], $_REQUEST['code_tracking'],  $_REQUEST['comment'], $_REQUEST['comment_internal'] );
	do_action( 'tcp_admin_order_editor_save', $order_id );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Order saved', 'tcp' );?>
	</p></div><?php
} elseif ( isset( $_REQUEST['tcp_order_delete'] ) && current_user_can( 'tcp_edit_orders' ) ) {
	Orders::delete( $order_id );
	do_action( 'tcp_admin_order_editor_delete', $order_id );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Order deleted', 'tcp' );?>
	</p></div>
	<p><a href="<?php echo $admin_path;?>OrdersList.php&status=<?php echo $status?>"><?php _e( 'return to the list', 'tcp' );?></a></p>
	<?php
	return;
}
$order = Orders::get( $order_id );

if ( isset( $_REQUEST['send_email'] ) ) {
	if ( $_REQUEST['send_email'] == 'billing' )
		$to = $order->billing_email;
	else
		$to = $order->shipping_email;
	$from = isset( $thecartpress->settings['from_email'] ) && strlen( $thecartpress->settings['from_email'] ) > 0 ? $thecartpress->settings['from_email'] : 'no-response@thecartpress.com';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'To: ' . $to . "\r\n";
	$headers .= 'From: ' . $from . "\r\n";
	$subject = 'Order from '.get_bloginfo( 'name' );
	$message = OrderPage::show( $order_id, true, false );
	$message_to_customer = apply_filters( 'tcp_send_order_mail_to_customer', $message, $order_id );
	mail( $to, $subject, $message_to_customer, $headers );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Mail sent', 'tcp' );?>
	</p></div><?php
}
?>
<style>
#shipping_info {
	width: 50%;
	float: left;
}
</style>
<div class="wrap">

<h2><?php _e( 'Order', 'tcp' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>OrdersList.php&status=<?php echo $status?>"><?php _e( 'return to the list', 'tcp' );?></a></li>
<?php if ( $order && strlen( $order->billing_email ) > 0 ) : ?>
	<li>&nbsp;|&nbsp;</li>
	<li><a href="<?php echo add_query_arg( array( 'send_email' => 'billing' ), get_permalink() );?>"><?php _e( 'send email to billing email', 'tcp' );?></a></li>
<?php endif;?>
<?php if ( $order && strlen( $order->shipping_email ) > 0 ) : ?>
	<li>&nbsp;|&nbsp;</li>
	<li><a href="<?php echo add_query_arg( array( 'send_email' => 'shipping' ), get_permalink() );?>"><?php _e( 'send email to shipping email', 'tcp' );?></a></li>
<?php endif;?>
</ul><!-- subsubsub -->

<div class="clear"></div>
<?php 
$orderpage = OrderPage::show( $order_id, true, false );
$_SESSION['order_page'] = $orderpage;
//$orderpage = str_replace( '<table class="tcp_details"', '<table class="widefat fixed"', $orderpage );
$orderpage = str_replace( '<table class="tcp_details"', '<table class="tcp_shopping_cart_table"', $orderpage );
echo $orderpage;

if ( $order ) :?>
<form method="post" name="frm">
	<input type="hidden" name="status" value="<?php echo $status;?>" />
	<input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
	<table class="form-table">
	<tbody>
	<?php do_action( 'tcp_admin_order_before_editor', $order_id );?>
	<tr valign="top">
	<th scope="row">
		<label for="new_status"><?php _e( 'Status', 'tcp' );?>:</label>
	</th>
	<td>
		<select class="postform" id="new_status" name="new_status">
			<option value="<?php echo Orders::$ORDER_PENDING;?>"<?php selected( Orders::$ORDER_PENDING, $order->status );?>><?php _e( 'pending', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_PROCESSING;?>"<?php selected( Orders::$ORDER_PROCESSING, $order->status );?>><?php _e( 'processing', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_COMPLETED;?>"<?php selected( Orders::$ORDER_COMPLETED, $order->status );?>><?php _e( 'completed', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_CANCELLED;?>"<?php selected( Orders::$ORDER_CANCELLED, $order->status );?>><?php _e( 'cancelled', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_SUSPENDED;?>"<?php selected( Orders::$ORDER_SUSPENDED, $order->status );?>><?php _e( 'suspended', 'tcp' );?></option>
		</select>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row">
		<label for="code_tracking"><?php _e( 'Code tracking', 'tcp' );?>:</label>
	</th>
	<td>
		<input name="code_tracking" id="code_tracking" type="text" size="10" maxlength="50" value="<?php echo $order->code_tracking;?>" />
	</td>
	</tr>
	<tr valign="top">
	<th scope="row">
		<label for="comment"><?php _e( 'Customer\'s comment', 'tcp' );?>:</label>
	</th>
	<td>
		<textarea valign="top" name="comment" id="comment" rows="5" cols="40" maxlength="250"><?php echo $order->comment;?></textarea>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row">
		<label for="comment_internal"><?php _e( 'Internal comment', 'tcp' );?>:</label>
	</th>
	<td>
		<textarea valign="top" name="comment_internal" id="comment_internal" rows="5" cols="40" maxlength="250"><?php echo $order->comment_internal;?></textarea>
	</td>
	</tr>
	<?php do_action( 'tcp_admin_order_after_editor', $order_id );?>
	</tbody></table>
	<p class="submit">
		<input name="tcp_order_edit" value="<?php _e( 'save', 'tcp' );?>" type="submit" class="button-primary" />
		<?php if ( $order->status == Orders::$ORDER_SUSPENDED ) :?>
		<input name="tcp_order_delete" value="<?php _e( 'delete', 'tcp' );?>" type="submit" class="button-primary" />
		<?php endif;?>
	</p>
</form>
<?php endif;?>
</div><!-- wrap -->
