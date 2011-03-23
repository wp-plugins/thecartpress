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

class OrderPanel {
	static function show() {
		if ( isset( $_REQUEST['tcp_order_type'] ) )
			$tcp_order_type = $_REQUEST['tcp_order_type'];
		else
			$tcp_order_type = isset( $_SESSION['tcp_order_type'] ) ? $_SESSION['tcp_order_type'] : 'order';
		$_SESSION['tcp_order_type'] = $tcp_order_type;
		if ( isset( $_REQUEST['tcp_order_desc'] ) )
			$tcp_order_desc =  $_REQUEST['tcp_order_desc'];
		else
			$tcp_order_desc = isset( $_SESSION['tcp_order_desc'] ) ? $_SESSION['tcp_order_desc'] : 'asc';
		$_SESSION['tcp_order_desc'] = $tcp_order_desc;?>
		<div class="tcp_order_panel">
			<form method="POST">
			<label for="tcp_order_type"><?php _e( 'Order products by', 'tcp' ); ?></label>:
			<select id="tcp_order_type" name="tcp_order_type">
				<option value="order" <?php selected( $tcp_order_type, 'order' );?>><?php _e( 'Order', 'tcp' ); ?></option>
				<option value="price" <?php selected( $tcp_order_type, 'price' );?>><?php _e( 'Price', 'tcp' ); ?></option>
				<option value="title" <?php selected( $tcp_order_type, 'title' );?>><?php _e( 'Title', 'tcp' ); ?></option>
				<option value="author" <?php selected( $tcp_order_type, 'author' );?>><?php _e( 'Author', 'tcp' ); ?></option>
				<option value="date" <?php selected( $tcp_order_type, 'date' );?>><?php _e( 'Date', 'tcp' ); ?></option>
				<option value="rand" <?php selected( $tcp_order_type, 'rand' );?>><?php _e( 'Random', 'tcp' ); ?></option>
				<option value="comment_count" <?php selected( $tcp_order_type, 'comment_count' );?>><?php _e( 'Popular', 'tcp' ); ?></option>

			</select>
			<input type="radio" name="tcp_order_desc" id="tcp_order_asc" value="asc" <?php checked( $tcp_order_desc, 'asc' );?>/>
			<label for="tcp_order_asc"><?php _e( 'Asc.', 'tcp' ); ?></label>
			<input type="radio" name="tcp_order_desc" id="tcp_order_desc" value="desc" <?php checked( $tcp_order_desc, 'desc' );?>/>
			<label for="tcp_order_desc"><?php _e( 'Desc.', 'tcp' ); ?></label>
			<input type="submit" id="tcp_change_order" name="tcp_change_order" value="<?php _e( 'Order', 'tcp' );?>" />
			</form>
		</div><?php
	}
}
?>
