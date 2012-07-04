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
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class TCPWishList {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		if ( is_admin() ) {
			add_action( 'tcp_main_settings_page', array( $this, 'tcp_main_settings_page' ) );
			add_filter( 'tcp_main_settings_action', array( $this, 'tcp_main_settings_action' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
		global $thecartpress;
		if ( $thecartpress ) {
			if ( $thecartpress->get_setting( 'enabled_wish_list', false ) ) {
				if ( is_admin() ) add_action( 'widgets_init', array( $this, 'widgets_init' ) );
				else add_action( 'wp_head', array( $this, 'wp_head' ) );
			}
		}
	}

	function wp_head() {
		if ( isset( $_REQUEST['tcp_add_to_wish_list'] ) ) {
			$tcp_new_wish_list_item = isset( $_REQUEST['tcp_new_wish_list_item'] ) ? $_REQUEST['tcp_new_wish_list_item'] : 0;
			if ( $tcp_new_wish_list_item > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->addWishList( $tcp_new_wish_list_item );
				do_action( 'tcp_add_wish_list', $tcp_new_wish_list_item );
			}
		} elseif ( isset( $_REQUEST['tcp_remove_from_wish_list'] ) ) {
			$post_id = isset( $_REQUEST['tcp_wish_list_post_id'] ) ? $_REQUEST['tcp_wish_list_post_id'] : 0;
			if ( $post_id > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->deleteWishListItem( $post_id );
				do_action( 'tcp_delete_wish_list_item', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_remove_wish_list'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$shoppingCart->deleteWishList();
			do_action( 'tcp_delete_wish_list' );
		}
	}

	function tcp_main_settings_page() {
		global $thecartpress;
		$enabled_wish_list = $thecartpress->get_setting( 'enabled_wish_list', false ); ?>
	
	<tr valign="top">
		<th scope="row">
		<label for="enabled_wish_list"><?php _e( 'Enabled Wish List', 'tcp' ); ?></label>
		</th>
		<td>
		<input type="checkbox" id="enabled_wish_list" name="enabled_wish_list" value="yes" <?php checked( true, $enabled_wish_list ); ?> />
		</td>
	</tr><?php
	}
	
	function tcp_main_settings_action( $settings ) {
		$settings['enabled_wish_list'] = isset( $_POST['enabled_wish_list'] ) ? $_POST['enabled_wish_list'] == 'yes' : false;
		return $settings;
	}

	function admin_menu() {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'enabled_wish_list', false ) ) return;
		$base = $thecartpress->get_base();
		add_submenu_page( $base, __( 'WishList', 'tcp' ), __( 'My wish List', 'tcp' ), 'tcp_edit_wish_list', TCP_ADMIN_FOLDER . 'WishList.php' );
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'enabled_wish_list', false ) ) return $out;
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( ! $shoppingCart->isInWishList( $post_id ) ) : 
			ob_start(); ?>
			<input type="hidden" value="" name="tcp_new_wish_list_item" id="tcp_new_wish_list_item_<?php echo $post_id; ?>" />
			<input type="submit" name="tcp_add_to_wish_list" class="tcp_add_to_wish_list" id="tcp_add_wish_list_<?php echo $post_id; ?>" value="<?php _e( 'Add to Wish list', 'tcp' ); ?>"
			onclick="jQuery('#tcp_new_wish_list_item_<?php echo $post_id; ?>').val('<?php echo $post_id; ?>');jQuery('#tcp_frm_<?php echo $post_id; ?>').attr('action', '');" />
			<?php do_action( 'tcp_buy_button_add_to_wish_list', $post_id );
			$out .= ob_get_clean();
		endif;
		return $out;
	}

	function widgets_init() {
		require_once( TCP_WIDGETS_FOLDER . 'WishListWidget.class.php' );
		register_widget( 'WishListWidget' );
	}
}

$wish_list = new TCPWishList();
?>
