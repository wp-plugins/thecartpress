<?php
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersDetails.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/OrdersCosts.class.php' );

class OrdersListTable extends  WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'plural' => 'Orders',
		) );
	}

	function ajax_user_can() {
		return false;
	}

	function prepare_items() {
		if ( ! is_user_logged_in() ) return;

		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
		$per_page = apply_filters( 'tcp_orders_per_page', 15 );
		$paged = $this->get_pagenum();
		if ( current_user_can( 'tcp_edit_orders' ) ) {				
			$this->items = Orders::getOrdersEx( $paged, $per_page, $status );
			$total_items = Orders::getCountOrdersByStatus( $status );
		} else {
			global $current_user;
			get_currentuserinfo();
			$this->items = Orders::getOrdersEx( $paged, $per_page, $status, $current_user->ID );
			$total_items = Orders::getCountOrdersByStatus( $status, $current_user->ID );
		}
		$total_pages = $total_items / $per_page;
		if ( $total_pages > (int)$total_pages ) {
			$total_pages = (int)$total_pages;
			$total_pages++;
		}
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> $total_pages,
		) );
	}

	function get_table_classes() {
		return array( 'widefat', 'fixed', 'pages', 'tcp_orders'  );
	}

	function get_column_info() {
		$orders_columns = array();
		//$orders_columns['cb'] = '<input type="checkbox" />';
		$orders_columns['order_id'] = _x( 'ID', 'column name', 'tcp' );
		$orders_columns['created_at'] = _x( 'Date', 'column name', 'tcp' );
		$orders_columns['customer_id'] = _x( 'User', 'column name', 'tcp' );
		$orders_columns['status'] = _x( 'Status', 'column name', 'tcp' );
		$orders_columns['payment_name'] = _x( 'Payment', 'column name', 'tcp' );
		$orders_columns['shipping_method'] = _x( 'Shipping', 'column name', 'tcp' );
		$orders_columns['total'] = _x( 'Total', 'column name', 'tcp' );
		$orders_columns = apply_filters( "tcp_manage_orders_columns", $orders_columns );
		return array( $orders_columns, array(), array() );
	}

	function column_cb( $item ) {
		?><input type="checkbox" name="order[]" value="<?php echo $item->order_id; ?>" /><?php
	}

	function column_total( $item ) {
		//$total = $item->shipping_amount - $item->discount_amount + $item->payment_amount;
		$total = - $item->discount_amount;
		$total = OrdersCosts::getTotalCost( $item->order_id, $total );
		echo tcp_format_the_price( OrdersDetails::getTotal( $item->order_id, $total ) );
	}

	function column_customer_id( $item ) {
		$user_data = get_userdata( $item->customer_id );
		if ( $user_data ) {
			//echo $user_data->first_name, ' ', $user_data->last_name, ' &lt;', $user_data->user_email, '&gt;';
			//user_login
			echo $user_data->user_nicename, ' &lt;', $user_data->user_email, '&gt;';
		} else {
			echo '&lt;', $item->billing_email, '&gt;';
		}
	}

	function column_order_id( $item ) {
		echo $item->order_id;
		$actions = array();
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
		$paged = isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 0;
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
		$href = $admin_path . 'OrderEdit.php&order_id= ' . $item->order_id . '&status=' . $status . '&paged=' . $paged;
		$actions['edit'] = '<a href="' . $href . '" title="' . esc_attr( __( 'Edit this order', 'tcp' ) ) . '">' . __( 'Edit' ) . '</a>';
		//$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline' ) ) . '">' . __( 'Quick&nbsp;Edit', 'tcp' ) . '</a>';
		echo $this->row_actions( $actions );
	}

	function column_default( $item, $column_name ) {
		echo isset( $item->$column_name ) ? $item->$column_name : '???';
	}
	
	function extra_tablenav( $which ) {
		if ( 'top' != $which ) return;
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : ''; ?>
		<label for="status"><?php _e( 'Status', 'tcp' );?>:</label>
		<select class="postform" id="status" name="status">
			<option value="" <?php selected( '', $status );?>><?php _e( 'all', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_PENDING;?>" <?php selected( Orders::$ORDER_PENDING, $status );?>><?php _e( 'pending', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_PROCESSING;?>" <?php selected( Orders::$ORDER_PROCESSING, $status );?>><?php _e( 'processing', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_COMPLETED;?>" <?php selected( Orders::$ORDER_COMPLETED, $status );?>><?php _e( 'completed', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_CANCELLED;?>" <?php selected( Orders::$ORDER_CANCELLED, $status );?>><?php _e( 'cancelled', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_SUSPENDED;?>" <?php selected( Orders::$ORDER_SUSPENDED, $status );?>><?php _e( 'suspended', 'tcp' );?></option>
		</select>
		<?php do_action( 'tcp_restrict_manage_orders' );
		submit_button( __( 'Filter' ), 'secondary', false, false, array( 'id' => 'order-query-submit' ) );
	}
	
	/*function get_bulk_actions() {
		$actions = array();
		$actions['edit'] = __( 'Edit', 'tcp' );
		return $actions;
	}

	function inline_edit() { ?>
		<form method="get" action="">
		<table style="display: none">
		<tbody id="inlineedit">
		<?php $bulk = 0;
		while ( $bulk < 2 ) : ?>
		<tr id="<?php echo $bulk ? 'bulk-edit' : 'inline-edit'; ?>" class="inline-edit-row inline-edit-row-<?php echo "$hclass inline-edit-$screen->post_type ";
			echo $bulk ? "bulk-edit-row bulk-edit-row-$hclass bulk-edit-$screen->post_type" : "quick-edit-row quick-edit-row-$hclass inline-edit-$screen->post_type";
		?>" style="display: none">
		<td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">

		<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
			<h4><?php echo $bulk ? __( 'Bulk Edit' ) : __( 'Quick Edit' ); ?></h4>
			<label>
				<span class="title"><?php _e( 'Title' ); ?></span>
				<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value="" /></span>
			</label>
		</td></tr>
		<?php $bulk++;
		endwhile; ?>
		</tbody>
		</table>
		</form>
	<?php }*/
}

$ordersListTable = new OrdersListTable();

//$doaction = $ordersListTable->current_action();

$ordersListTable->prepare_items();?>
<form id="posts-filter" method="get" action="">
<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 0; ?>" />

<div class="wrap">
<?php //screen_icon(); ?>
<h2><?php _e( 'Orders', 'tcp' );?></h2>
<div class="clear"></div>

<?php $ordersListTable->display(); ?>
</form>

<?php //if ( $ordersListTable->has_items() ) $ordersListTable->inline_edit(); ?>

</div>

