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

require_once( dirname(dirname( __FILE__ ) ) . '/daos/RelEntitiesOptions.class.php' );
		
class OptionCustomFieldsMetabox {

	function registerMetaBox() {
		add_meta_box( 'tcp-option-custom-fields', __( 'Option Custom Fields', 'tcp' ), array( &$this, 'showCustomFields' ), OptionCustomPostType::$PRODUCT_OPTION, 'normal', 'high' );
	}

	function showCustomFields() {
		global $post;
		global $thecartpress;
		$stock_management	= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;

		if ( $post->post_type != OptionCustomPostType::$PRODUCT_OPTION ) return;
		$post_id = tcp_get_default_id( $post->ID, OptionCustomPostType::$PRODUCT_OPTION );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';
		$is_translation = $lang != $source_lang;
		if ( $is_translation && $post_id == $post->ID) {
			_e( 'After saving the title and content, you will be able to edit the specific fields of the option.', 'tcp' );
			return;
		}
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		$tcp_product_option_parent_id = isset( $_REQUEST['tcp_product_option_parent_id'] ) ? $_REQUEST['tcp_product_option_parent_id'] : 0;
		if ( $tcp_product_parent_id == 0 ) {
			$tcp_product_parent_id = RelEntities::getParent( $post_id, 'OPTIONS' );
			$post_parent = get_post( $tcp_product_parent_id );
			if ( $post_parent->post_type == 'tcp_product_option') {
				$tcp_product_option_parent_id = $tcp_product_parent_id;
				$tcp_product_parent_id = RelEntities::getParent( $tcp_product_parent_id, 'OPTIONS' );
				$relEntity = RelEntities::get( $tcp_product_option_parent_id, $post_parent->ID, 'OPTIONS' );
			} else {
				$tcp_product_option_parent_id = 0;
			}
		}
		$product_parent = get_post( $tcp_product_parent_id );
		if ( $tcp_product_option_parent_id > 0 ) $option_parent = get_post( $tcp_product_option_parent_id );
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';?>
		<ul class="subsubsub">
			<li><a href="post.php?action=edit&post=<?php echo $tcp_product_parent_id;?>"><?php printf( __( 'return to %s', 'tcp' ), $product_parent->post_title );?></a></li>
		<?php if ( $tcp_product_option_parent_id > 0 ) : ?>
			<li>|</li>
			<li><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $tcp_product_option_parent_id;?>"><?php printf( __( 'return to %s', 'tcp' ), $option_parent->post_title );?></a></li>
		<?php endif;?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>OptionsList.php&post_id=<?php echo $tcp_product_parent_id;?>"><?php echo __( 'return to Options list', 'tcp' );?></a></li>
			<li>|</li>
			<li><a href="post-new.php?post_type=tcp_product_option&tcp_product_parent_id=<?php echo $tcp_product_parent_id;?>&tcp_product_option_parent_id=<?php echo $tcp_product_option_parent_id;?>" title="<?php echo __( 'create a new \'sister\' option', 'tcp' );?>"><?php echo __( 'create new option', 'tcp' );?></a></li>
		<?php if ( $tcp_product_option_parent_id == 0 ) : ?>
			<li>|</li>
			<li><a href="post-new.php?post_type=tcp_product_option&tcp_product_parent_id=<?php echo $tcp_product_parent_id;?>&tcp_product_option_parent_id=<?php echo $post->ID;?>" title="<?php echo __( 'create a new second level option', 'tcp' );?>"><?php echo __( 'create child option', 'tcp' );?></a></li>
		<?php endif;?>
			<?php do_action( 'tcp_option_metabox_toolbar', $post_id );?>
		</ul>
		<?php //if ( $create_grouped_relation ) : ?>
			<input type="hidden" name="tcp_product_parent_id" id="tcp_product_parent_id" value="<?php echo $tcp_product_parent_id;?>" />
			<input type="hidden" name="tcp_product_option_parent_id" id="tcp_product_option_parent_id" value="<?php echo $tcp_product_option_parent_id;?>" />
		<?php //endif;?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp-option-custom-fields', 'tcp-option-custom-fields_wpnonce', false, true );?>
			<table class="form-table"><tbody>
			<tr valign="top">
				<th scope="row"><label for="tcp_price"><?php _e( 'Price', 'tcp' );?>:</label></th>
				<td><input type="number" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price" id="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $post_id ) );?>" class="regular-text" style="width:12em">&nbsp;<?php tcp_the_currency();?>
				<p class="description"><?php _e( 'This price will be added to the price of the parent.', 'tcp' );?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_weight"><?php _e( 'Weight', 'tcp' );?>:</label></th>
				<td><input type="number" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_weight" id="tcp_weight" value="<?php echo tcp_number_format( tcp_get_the_weight( $post_id ) );?>" class="regular-text" style="width:12em" />&nbsp;<?php tcp_the_unit_weight(); ?>
				<p class="description"><?php _e( 'If value is zero then the weight will be the weight of the parent. This weight will not be added to the weight of the parent anyway.', 'tcp' );?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_order"><?php _e( 'Order', 'tcp' );?>:</label></th>
				<td><input name="tcp_order" id="tcp_order" value="<?php echo htmlspecialchars( tcp_get_the_order( $post_id ) );?>" class="regular-text" type="number" min="0" style="width:4em" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_sku"><?php _e( 'Sku', 'tcp' );?>:</label></th>
				<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( tcp_get_the_sku( $post_id ) );?>" class="regular-text" type="text" style="width:12em" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_stock"><?php _e( 'Stock', 'tcp' );?>:</label>
				<?php if ( ! $stock_management ) : 
					$path = 'admin.php?page=tcp_settings_page';?>
					<p class="description"><?php printf( __( 'Stock management is disabled. See the <a href="%s">settings</a> page to change this value.', 'tcp' ), $path );?></p>
				<?php endif;?></th>
				<td><input name="tcp_stock" id="tcp_stock" value="<?php echo tcp_get_the_stock( $post_id );?>" class="regular-text" type="number" min="-1" style="width:10em">
				<p class="description"><?php _e( 'Use value -1 (or left blank) for stores/products with no stock management.', 'tcp' );?></p></td>
			</tr>
			<?php do_action( 'tcp_options_metabox_custom_fields', $post_id );?>
			</tbody></table>
		</div> <!-- form-wrap -->
		<?php
	}

	function saveCustomFields( $post_id, $post ) {
		if ( $post->post_type != OptionCustomPostType::$PRODUCT_OPTION ) return array( $post_id, $post );
		if ( ! isset( $_POST[ 'tcp-option-custom-fields_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ 'tcp-option-custom-fields_wpnonce' ], 'tcp-option-custom-fields' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		$post_id = tcp_get_default_id( $post_id, OptionCustomPostType::$PRODUCT_OPTION );
		$tcp_parent_id = isset( $_REQUEST['tcp_product_option_parent_id'] ) ? $_REQUEST['tcp_product_option_parent_id'] : 0;
		if ( $tcp_parent_id == 0 ) $tcp_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		$price	= isset( $_POST['tcp_price'] )  ? tcp_input_number( $_POST['tcp_price'] ) : 0;
		$order	= isset( $_POST['tcp_order'] )  ? $_POST['tcp_order'] : '';
		$weight	= isset( $_POST['tcp_weight'] )  ? tcp_input_number( $_POST['tcp_weight'] ) : 0;
		$stock	= isset( $_POST['tcp_stock'] )  ? $_POST['tcp_stock'] : -1;
		$sku	= isset( $_POST['tcp_sku'] ) ? $_POST['tcp_sku'] : '';
		if ( $tcp_stock == '' ) $tcp_stock = -1;
		if ( ! Relentities::exists( $tcp_parent_id, $post_id, 'OPTIONS' ) ) {
			RelEntities::insert( $tcp_parent_id, $post_id, 'OPTIONS', $order );
		} else {
			RelEntities::update( $tcp_parent_id, $post_id, 'OPTIONS', $order );
		}
		update_post_meta( $post_id, 'tcp_price', $price );
		update_post_meta( $post_id, 'tcp_order', $order );
		update_post_meta( $post_id, 'tcp_weight', $weight );
		update_post_meta( $post_id, 'tcp_sku', $sku );
		update_post_meta( $post_id, 'tcp_stock', (int)$stock );
		do_action( 'tcp_options_metabox_save_custom_fields', $post_id );
	}

	function deleteCustomFields( $post_id ) {
		$post_id = tcp_get_default_id( $post_id, OptionCustomPostType::$PRODUCT_OPTION );
		//if ( ! isset( $_POST[ 'tcp-option-custom-fields_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ 'tcp-option-custom-fields_wpnonce' ], 'tcp-option-custom-fields' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$post = get_post( $post_id );
		if ( $post->post_type == OptionCustomPostType::$PRODUCT_OPTION ) {
			RelEntities::deleteAllTo( $post_id, 'OPTIONS' );
			delete_post_meta( $post_id, 'tcp_price' );
			delete_post_meta( $post_id, 'tcp_order' );
			delete_post_meta( $post_id, 'tcp_weight' );
			delete_post_meta( $post_id, 'tcp_sku' );
			delete_post_meta( $post_id, 'tcp_stock' );
		}
		$translations = tcp_get_all_translations( $post_id );
		if ( is_array( $translations ) && count( $translations ) > 0 ) {
			foreach( $translations as $translation ) {
				if ( $translation->element_id != $post_id ) {
					wp_delete_post( $post_id );
				}
			}
		}
		$options = RelEntities::select( $post_id, 'OPTIONS' );
		if ( is_array( $options ) ) {
			foreach( $options as $option ) {
				wp_delete_post( $option->id_to, true );
			}
		}
		RelEntities::deleteAll( $post_id, 'OPTIONS' );
		do_action( 'tcp_options_metabox_delete_custom_fields', $post_id );
	}
}
?>
