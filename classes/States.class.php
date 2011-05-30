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
 * along with TheCartPress-states.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once( dirname( dirname( __FILE__ ) ) .'/daos/Addresses.class.php' );

class TCPStates {

	function __construct() {
		add_action( 'init', array( $this, 'init' ), 99 );
		if ( is_admin() ) {
			add_filter( 'tcp_address_editor_load_regions', array( $this, 'load_states' ) );
		} else {
			add_filter( 'tcp_load_regions_for_billing', array( $this, 'load_states' ) );
			add_filter( 'tcp_load_regions_for_shipping', array( $this, 'load_states' ) );
		}
	}

	function init() {
		wp_register_script( 'tcp_state_scripts', plugins_url( 'thecartpress/js/tcp_state_scripts.php' ) );
		wp_enqueue_script( 'tcp_state_scripts' );
	}

	function load_states( $regions ) {
		return array();
	}
}

new TCPStates();

function tcp_get_billing_region() {
	if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
			$billing_region_id = $_SESSION['tcp_checkout']['billing']['billing_region_id'];
		} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'Y' ) {
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
			if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) == 'new' ) {
				$shipping_region_id = $_SESSION['tcp_checkout']['billing']['billing_region_id'];;
			} else {
				$shipping_address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
				$shipping_region_id = $shipping_address->region_id;
			}
		} else {//selected_shipping_address == Y
			$shipping_address = Addresses::get( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
			$shipping_region_id = $shipping_address->region_id;
		}
		return $shipping_region_id;
	} else {
		return '';
	}
}
?>
