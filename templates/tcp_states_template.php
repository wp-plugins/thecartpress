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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * To Use:
 * add_action( 'admin_footer', 'tcp_states_footer_scripts' );
 * add_action( 'wp_footer', 'tcp_states_footer_scripts' );
 */
function tcp_states_footer_scripts() {
	?><script type="text/javascript">
	<?php include_once( dirname( dirname( __FILE__ ) ) . '/js/tcp_state_scripts.php' );?>
	</script><?php
}

function tcp_get_billing_region() {
	if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
			$billing_region_id = $_SESSION['tcp_checkout']['billing']['billing_region_id'];
		} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'Y' ) {
			require_once( dirname( dirname( __FILE__ ) ) .'/daos/Addresses.class.php' );
			$billing_address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			$billing_region_id = $billing_address->region_id;
		}
		return $billing_region_id;
	} else {
		return '';
	}
}

function tcp_get_shipping_region() {
	if ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] == 'new' ) {
			$shipping_region_id = $_SESSION['tcp_checkout']['shipping']['shipping_region_id'];
		} elseif ( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] == 'BIL' ) {
			if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) && $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
				$shipping_region_id = $_SESSION['tcp_checkout']['billing']['billing_region_id'];
			} else {
				require_once( dirname( dirname( __FILE__ ) ) .'/daos/Addresses.class.php' );
				$shipping_address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
				$shipping_region_id = $shipping_address->region_id;
			}
		} else {//selected_shipping_address == Y
			require_once( dirname( dirname( __FILE__ ) ) .'/daos/Addresses.class.php' );
			$shipping_address = Addresses::get( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
			$shipping_region_id = $shipping_address->region_id;
		}
		return $shipping_region_id;
	} else {
		return '';
	}
}
?>
