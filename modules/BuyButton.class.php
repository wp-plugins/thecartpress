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

class TCPBuyButton {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 90 );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter( 'tcp_get_buybutton_template', array( &$this, 'tcp_get_buybutton_template' ), 10, 2 );
	}

	function admin_init() {
		add_action( 'tcp_product_metabox_custom_fields', array( &$this, 'tcp_product_metabox_custom_fields' ) );
		add_action( 'tcp_product_metabox_save_custom_fields', array( &$this, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields', array( &$this, 'tcp_product_metabox_delete_custom_fields' ) );			
	}

	static function show( $post_id = 0, $echo = true  ) {
		$template = TCPBuyButton::get_template( $post_id );
		$custom_template = apply_filters( 'tcp_get_buybutton_template', $template, $post_id );
		if ( file_exists( $custom_template ) )  $template = $custom_template;
		ob_start();
		if ( $template ) include( $template );
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}

	static private function get_template( $post_id ) {
		if ( $post_id == 0 ) $post_id = get_the_ID();
		$post_type = get_post_type( $post_id );
		$product_type = strtolower( tcp_get_the_product_type( $post_id ) );
		$file_name_post_type = 'tcp_buybutton-' . $product_type . '-' . $post_type . '.php';
		$file_name = 'tcp_buybutton-' . $product_type . '.php';
		// child theme folder
		$template = get_stylesheet_directory() . '/' . $file_name_post_type;
		if ( file_exists( $template ) ) return $template;
		$template	= get_stylesheet_directory() . '/' . $file_name;
		if ( file_exists( $template ) ) return $template;
		// theme folder
		if ( get_stylesheet_directory() != get_template_directory() ) { 
			$template = get_template_directory() . '/' . $file_name_post_type;
			if ( file_exists( $template ) ) return $template;
			$template = get_template_directory() . '/' . $file_name;
			if ( file_exists( $template ) ) return $template;
		}
		// themes_templates folder
		$template = TCP_THEMES_TEMPLATES_FOLDER . $file_name_post_type;
		if ( file_exists( $template ) ) return $template;
		$template = TCP_THEMES_TEMPLATES_FOLDER . $file_name;
		if ( file_exists( $template ) ) return $template;
		return false;
	}

	function admin_menu() {
		global $thecartpress;
		if ( ! $thecartpress ) return;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce' );
		if ( ! $disable_ecommerce ) {
			$base = $thecartpress->get_base_appearance();
			add_submenu_page( $base, __( 'Buy buttons', 'tcp' ), __( 'Buy buttons', 'tcp' ), 'tcp_edit_orders', TCP_ADMIN_FOLDER . 'BuyButtonList.class.php' );
		}
	}

	static function get_buy_buttons() {
		$paths = array();
		$paths[] = array(
			'label'	=> __( 'Theme' ),
			'path'	=> get_stylesheet_directory() . '/tcp_buybutton*.php',
		);
		if ( get_stylesheet_directory() != get_template_directory() ) $paths[] = array(
			'label'	=> __( 'Parent theme', 'tcp' ),
			'path'	=> get_template_directory() . '/tcp_buybutton*.php',
		);
		$paths[] = array(
			'label'	=> __( 'Plugin' ),
			'path'	=> TCP_THEMES_TEMPLATES_FOLDER . 'tcp_buybutton*.php',
		);
		$paths = apply_filters( 'tcp_get_buy_buttons_paths', $paths );
		$buy_buttons = array();
		foreach( $paths as $path ) {
			$filenames = glob( $path['path'] );
			if ( $filenames != false ) {
				foreach( $filenames as $filename ) {
					$buy_buttons[] = array(
						'label'	=> $path['label'] . ': ' . basename( $filename, '.php' ),
						'path'	=> $filename,
		   			);
				}
			}
	   	}
		return $buy_buttons;
	}

	function tcp_product_metabox_custom_fields( $post_id ) {
		$selected_buy_button = get_post_meta( $post_id, 'tcp_selected_buybutton', true ); ?>
		
		<tr valign="top">
		<th scope="row"><label for="tcp_selected_buybutton"><?php _e( 'Buy button', 'tcp' );?>:</label></th>
		<td>
			<?php $buy_buttons = TCPBuyButton::get_buy_buttons(); ?>
			<select name="tcp_selected_buybutton" id="tcp_selected_buybutton">
			<option value="" <?php selected( '', $selected_buy_button ); ?>><?php _e( 'Default', 'tcp' ); ?></option>
			<?php foreach( $buy_buttons as $buy_button ) : ?>
			<option value="<?php echo $buy_button['path']; ?>" <?php selected( $buy_button['path'], $selected_buy_button ); ?>>
				<?php echo $buy_button['label']; ?>
			</option>
			<?php endforeach; ?>
			</select>
		</td>
		</tr>
	<?php }

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		update_post_meta( $post_id, 'tcp_selected_buybutton', isset( $_POST['tcp_selected_buybutton'] ) ? $_POST['tcp_selected_buybutton'] : '' );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_selected_buybutton' );
	}

	function tcp_get_buybutton_template( $template, $post_id ) {
		$selected_buy_button = get_post_meta( $post_id, 'tcp_selected_buybutton', true );
		if ( $selected_buy_button ) return $selected_buy_button;
		$post_type = get_post_type( $post_id );
		$product_type = tcp_get_the_product_type( $post_id );
		$selected_buy_button = get_option( 'tcp_buy_button_template-' .  $post_type . '-' . $product_type, '' );
		if ( $selected_buy_button ) return $selected_buy_button;
		return $template;
	}
}

new TCPBuyButton();
?>