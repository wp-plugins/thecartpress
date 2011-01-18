<?
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

class FlatRate extends TCP_Plugin {

	function getTitle() {
		return 'FlatRate';
	}

	function getDescription() {
		return 'Calculate the shipping cost by a flat or percentual formula.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart, $currency ) {
		$cost = $this->getCost( $instance, $shippingCountry, $shoppingCart );
		return __( 'Flat rate. The cost of the service is ', 'tcp' ) . number_format( $cost, 2 ) . '&nbsp;' . $currency;
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="calculate_by"><?php _e( 'Calculate', 'tcp' );?>:</label>
		</th><td>
			<select id="calculate_by" name="calculate_by">
				<option value="per" <?php selected( 'per', isset( $data['calculate_by'] ) ? $data['calculate_by'] : '' );?>><?php _e( 'Percentage', 'tcp' );?></option>
				<option value="fix" <?php selected( 'fix', isset( $data['calculate_by'] ) ? $data['calculate_by'] : '' );?>><?php _e( 'Fix', 'tcp' );?></option>
			</select>
		</td></tr>

		<tr valign="top">
		<th scope="row">
			<label for="fixed_cost"><?php _e( 'Fixed cost', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="fixed_cost" name="fixed_cost" value="<?php echo isset( $data['fixed_cost'] ) ? $data['fixed_cost'] : 0;?>" size="8" maxlength="13"/>
		</td></tr>

		<tr valign="top">
		<th scope="row">
			<label for="percentage"><?php _e( 'Percentage', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="percentage" name="percentage" value="<?php echo isset( $data['percentage'] ) ? $data['percentage'] : 0;?>" size="3" maxlength="5"/>
		</td></tr>

		<tr valign="top">
		<th scope="row">
			<label for="calculate_type"><?php _e( 'Type', 'tcp' );?>:</label>
		</th><td>
			<select id="calculate_type" name="calculate_type">
				<option value="by_order" <?php selected( 'by_order', isset( $data['calculate_type'] ) ? $data['calculate_type'] : '' );?>><?php _e( 'By order', 'tcp' );?></option>
				<option value="by_article" <?php selected( 'by_article', isset( $data['calculate_type'] ) ? $data['calculate_type'] : '' );?>><?php _e( 'By article', 'tcp' );?></option>
			</select>
		</td></tr>
	<?php
	}

	function saveEditFields( $data ) {
		$data['calculate_by'] = isset( $_REQUEST['calculate_by'] ) ? $_REQUEST['calculate_by'] : '';
		$data['fixed_cost'] = isset( $_REQUEST['fixed_cost'] ) ? (float)$_REQUEST['fixed_cost'] : '0';
		$data['percentage'] = isset( $_REQUEST['percentage'] ) ? (float)$_REQUEST['percentage'] : '0';
		$data['calculate_type'] = isset( $_REQUEST['calculate_type'] ) ? $_REQUEST['calculate_type'] : '';
		return $data;
	}

	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		if ( $data['calculate_by'] == 'fix' )
			if ( $data['calculate_type'] == 'by_order' )
				return $data['fixed_cost'];
			else //'by_article'
				return $data['fixed_cost'] * $shoppingCart->getCount();
		else //'percentage'
			return $shoppingCart->getTotalNoDownloadable() * $data['percentage'] / 100;
	}
}
?>
