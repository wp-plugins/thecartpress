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

/**
 * All the checkout plugins must implement this class
 */ 
class TCP_Plugin {
	/**
	 * Returns the title of the plugin
	 * Must be implemented
	 */
	function getTitle() {
	}

	/**
	 * Returns the description of the plugin
	 * Must be implemented
	 */
	function getDescription() {
	}

	/**
	 * Shows the data that the plugin need to be edited
	 * Must be implemented
	 */
	function showEditFields( $data ) {
	}

	/**
	 * This functions is run when the edut plugin page is saved
	 * Must be implemented
	 */
	function saveEditFields( $data ) {
		return $data;
	}

	/**
	 * Returns if the plugin is applicable
	 * Must be implemented
	 */
	function isApplicable( $shippingCountry, $shoppingCart, $data ) {
		return true;
	}

	/**
	 * Returns the text label to show in the checkout.
	 * Must be implemented
	 */
	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
	}

	/**
	 * Returns the cost of the service
	 * Must be implemented
	 */
	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		return 0;
	}

	/**
	 * Shows the button or the notice after the orders have been saved
	 *
	 * Must be implemented only for payment methods
	 */
	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
	}

	function __construct() {
	}
}

$tcp_shipping_plugins = array();
$tcp_payment_plugins = array();

/**
 * Registers a shipping plugin
 */
function tcp_register_shipping_plugin( $class_name ) {
	global $tcp_shipping_plugins;
	$tcp_shipping_plugins['shi_' . $class_name] = new $class_name();
}

/**
 * Registers a payment plugin
 */
function tcp_register_payment_plugin( $class_name ) {
	global $tcp_payment_plugins;
	$tcp_payment_plugins['pay_' . $class_name] = new $class_name();
}

/**
 * Returns the plugin object from a given plugin_id
 */
function tcp_get_plugin( $plugin_id ) {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;

	if ( isset( $tcp_shipping_plugins[$plugin_id] ) )
		return $tcp_shipping_plugins[$plugin_id];
	elseif ( isset( $tcp_payment_plugins[$plugin_id] ) )
		return $tcp_payment_plugins[$plugin_id];
	else return null;
}

function tcp_get_plugin_type( $plugin_id ) {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;

	if ( isset( $tcp_shipping_plugins[$plugin_id] ) )
		return 'shipping';
	elseif ( isset( $tcp_payment_plugins[$plugin_id] ) )
		return 'payment';
	else return '';
}

function tcp_get_applicable_shipping_plugins( $shipping_country, $shoppingCart ) {
	if ( $shoppingCart->isDownloadable() )
		return array();
	else
		return tcp_get_applicable_plugins( $shipping_country, $shoppingCart );
}

function tcp_get_applicable_payment_plugins( $shipping_country, $shoppingCart ) {
	return tcp_get_applicable_plugins( $shipping_country, $shoppingCart, 'payment' );
}

function tcp_get_applicable_plugins( $shipping_country, $shoppingCart, $type = 'shipping' ) {
	if ( $type == 'shipping' ) {
		global $tcp_shipping_plugins;
		$tcp_plugins = $tcp_shipping_plugins;
	} else {
		global $tcp_payment_plugins;
		$tcp_plugins = $tcp_payment_plugins;
	}
	$applicable_plugins = array();
	foreach( $tcp_plugins as $plugin_id => $plugin ) {
		$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
		if ( is_array( $plugin_data ) && count( $plugin_data ) > 0 ) {
			$applicable_instance_id = -1;
			$applicable_for_country = false;
			foreach( $plugin_data as $instance_id => $instance ) {
				if ( $instance['active'] ) {
					$all_countries = isset( $instance['all_countries'] ) ? $instance['all_countries'] == 'yes' : false;
					if ( $all_countries ) {
						$applicable_instance_id = $instance_id;
						//TODO
						$data = $plugin_data[$applicable_instance_id];
						if ( $plugin->isApplicable( $shipping_country, $shoppingCart, $data ) )
							$applicable_plugins[] = array(
								'plugin'	=> $plugin,
								'instance'	=> $applicable_instance_id,
							);
					} else {
						$countries = isset( $instance['countries'] ) ? $instance['countries'] : array();
						if ( in_array( $shipping_country, $countries ) ) {
							$applicable_instance_id = $instance_id;
							$applicable_for_country = true;
							//TODO
							$data = $plugin_data[$applicable_instance_id];
							if ( $plugin->isApplicable( $shipping_country, $shoppingCart, $data ) )
								$applicable_plugins[] = array(
									'plugin'	=> $plugin,
									'instance'	=> $applicable_instance_id,
								);
							//break;
						}
					}
				}
			}
/*			if ( $applicable_instance_id > -1 ) {
				$data = $plugin_data[$applicable_instance_id];
				if ( $plugin->isApplicable( $shipping_country, $shoppingCart, $data ) )
					$applicable_plugins[] = array(
						'plugin'	=> $plugin,
						'instance'	=> $applicable_instance_id,
					);
			}
*/
		}
	}

	if ( $applicable_for_country )
		foreach( $applicable_plugins as $id => $plugin_instance ) {
			$data = tcp_get_shipping_plugin_data( get_class( $plugin_instance['plugin'] ), $plugin_instance['instance'] );
			$all_countrie =	isset( $data['all_countries'] ) ? $data['all_countries'] == 'yes' : false;
			if ( $all_countrie ) unset( $applicable_plugins[$id] );
		}
	return $applicable_plugins;
}

function tcp_get_shipping_plugin_data( $plugin_name, $instance ) {
	return tcp_get_plugin_data( 'shi_' . $plugin_name, $instance );
}

function tcp_get_payment_plugin_data( $plugin_name, $instance ) {
	return tcp_get_plugin_data( 'pay_' . $plugin_name, $instance );
}

function tcp_get_plugin_data( $plugin_id, $instance = -1 ) {
	$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
	if ( $instance == -1 )
		return $plugin_data;
	else
		return $plugin_data[$instance];
}
?>
