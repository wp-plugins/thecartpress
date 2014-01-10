<?php
/**
 * Shortcuts Bar
 *
 * Bar with typical shortcut to configure TheCartPress
 *
 * @package TheCartPress
 * @subpackage Modules
 */

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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPShortcutsBar' ) ) :

class TCPShortcutsBar {

	function __construct() {
		$tcp_show_shortcut_bar = get_option( '_tcp_show_shortcut_bar', true );
		if ( $tcp_show_shortcut_bar ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	function admin_notices() {
		$icon_path = plugins_url( 'thecartpress/images/miranda/' );
		$shortcuts = array(
			'products' => array(
				'title'	=> __( 'Products', 'tcp' ),
				'icon'	=> $icon_path . 'products_48.png',
				'small_icon'	=> $icon_path . 'products_32.png',
				'url'	=> get_admin_url( null, 'edit.php?post_type=tcp_product' ),
			),
			'orders' => array(
				'title'	=> __( 'Orders', 'tcp' ),
				'icon'	=> $icon_path . 'order_history_48.png',
				'small_icon'	=> $icon_path . 'order_history_32.png',
				'url'	=> get_admin_url( null, '?page=thecartpress/admin/OrdersListTable.php' ),
			),
			'main_settings' => array(
				'title'	=> __( 'Main Settings', 'tcp' ),
				'icon'	=> $icon_path . 'main_settings_48.png',
				'small_icon'	=> $icon_path . 'main_settings_32.png',
				'url'	=> get_admin_url( null, 'admin.php?page=thecartpress/TheCartPress.class.php' ),
			),		
			'localize' => array(
				'title'	=> __( 'Localize', 'tcp' ),
				'icon'	=> $icon_path . 'localize_settings_48.png',
				'small_icon'	=> $icon_path . 'localize_settings_32.png',
				'url'	=> get_admin_url( null, 'admin.php?page=currency_settings' ),
			),
			'payment_methods' => array(
				'title'	=> __( 'Payment Methods', 'tcp' ),
				'icon'	=> $icon_path . 'payments_settings_48.png',
				'small_icon'	=> $icon_path . 'payments_settings_32.png',
				'url'	=> get_admin_url( null, 'admin.php?page=payment_settings' ),
			),
			'shipping_methods' => array(
				'title'	=> __( 'Shipping Methods', 'tcp' ),
				'icon'	=> $icon_path . 'shippings_settings_48.png',
				'small_icon'	=> $icon_path . 'shippings_settings_32.png',
				'url'	=> get_admin_url( null, 'admin.php?page=shipping_settings' ),
			),
			'checkout' => array(
				'title'	=> __( 'Checkout', 'tcp' ),
				'icon'	=> $icon_path . 'checkout_settings_48.png',
				'small_icon'	=> $icon_path . 'checkout_settings_32.png',
				'url'	=> get_admin_url( null, 'admin.php?page=checkout_settings' ),
			),
		);
		$shortcuts = apply_filters( 'tcp_shortcuts_bar', $shortcuts );
	 ?>
<div class="tcpf">
	<div class="alert alert-success">
		<div class="row">
			<?php foreach( $shortcuts as $id => $shortcut ) : ?>
			<div class="col-sm-2 col-md-1 col-lg-1">
				<div class="">
					<a href="<?php echo $shortcut['url']; ?>" title="<?php echo $shortcut['title']; ?>"><img src="<?php echo $shortcut['icon']; ?>" /></a>
				</div><!-- .thumbnail -->
			</div>
			<?php endforeach; ?>
		</div><!-- .row -->
	</div>
</div><!-- .tcpf --><?php
	}
}

new TCPShortcutsBar();
endif; // class_exists check