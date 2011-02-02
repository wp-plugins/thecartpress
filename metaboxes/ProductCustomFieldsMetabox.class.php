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

require_once( dirname(dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
require_once( dirname(dirname( __FILE__ ) ) . '/daos/Taxes.class.php' );
		
class ProductCustomFieldsMetabox {

	function registerMetaBox() {
		add_meta_box( 'tcp-product-custom-fields', __( 'Product setup', 'tcp' ), array( &$this, 'showCustomFields' ), ProductCustomPostType::$PRODUCT, 'normal', 'high' );
	}

	function showCustomFields() {
		global $post;
		if ( $post->post_type != ProductCustomPostType::$PRODUCT ) return;
		if ( !current_user_can( 'edit_post', $post->ID ) ) return;
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		if ( $tcp_product_parent_id > 0 ) {
			$create_grouped_relation = true;
		} else {
			$create_grouped_relation = false;
			if ( $post->ID > 0 )
				$tcp_product_parent_id = RelEntities::getParent( $post->ID );
		}?>
		<ul class="subsubsub">
		<?php 
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
		$count = RelEntities::count( $post->ID );
		if ( $count > 0 ) $count = ' (' . $count . ')';
		else $count = '';
		//$product_type = get_post_meta( $post->ID, 'tcp_type', true );
		$product_type = tcp_get_the_product_type( $post->ID );
		$post_id = tcp_get_default_id( $post->ID );
		if ( $product_type != '' && $product_type != 'SIMPLE' ) :?>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=GROUPED"><?php _e( 'assigned products', 'tcp' );?> <?php echo $count;?></a></li>
		<?php endif;
		$count = RelEntities::count( $post_id, 'PROD-PROD' );
		if ( $count > 0 ) $count = ' (' . $count . ')';
		else $count = '';?>
		<?php if ( $product_type != '' && $product_type != 'SIMPLE' ) :?>
			<li>|</li>
		<?endif;?>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=PROD-PROD"><?php _e( 'related products', 'tcp' );?> <?php echo $count;?></a></li>
		<?php $count = RelEntities::count( $post_id, 'PROD-POST' );
		if ( $count > 0 ) $count = ' (' . $count . ')';
		else $count = '';?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&post_type_to=post&rel_type=PROD-POST"><?php _e( 'related posts', 'tcp' );?> <?php echo $count;?></a></li>
		<?php if ( $tcp_product_parent_id > 0 ) :?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $tcp_product_parent_id;?>&rel_type=GROUPED"><?php _e( 'parent assigned products', 'tcp' );?> <?php echo $count;?></a></li>
			<li>|</li>
			<li><a href="post.php?action=edit&post=<?php echo $tcp_product_parent_id;?>"><?php _e( 'edit parent product', 'tcp' );?></a></li>
		<?php endif;?>
		<?php if ( tcp_is_downloadable( $post->ID ) ) :?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>UploadFiles.php&post_id=<?php echo $post_id;?>"><?php echo __( 'file upload', 'tcp' ), $count;?></a></li>
		<?php endif;?>
		<?php do_action( 'tcp_product_metabox_toolbar', $post_id );?>
		</ul>
		<?php if ( $create_grouped_relation ): ?>
			<input type="hidden" name="tcp_product_parent_id" id="tcp_product_parent_id" value="<?php echo $tcp_product_parent_id;?>" />
		<?php endif;?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp-product-custom-fields', 'tcp-product-custom-fields_wpnonce', false, true );?>
			<table class="form-table"><tbody>
			<tr valign="top">
				<th scope="row"><label for="tcp_type"><?php _e( 'Type', 'tcp' );?>:</label></th>
				<td><select name="tcp_type" id="tcp_type">
						<option value="SIMPLE" <?php selected( $product_type, 'SIMPLE' );?>><?php _e( 'Simple', 'tcp' );?></option>
						<option value="GROUPED" <?php selected( $product_type, 'GROUPED' );?>><?php _e( 'Grouped', 'tcp' );?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_price"><?php _e( 'Price', 'tcp' );?>:</label></th>
				<td><input name="tcp_price" id="tcp_price" value="<?php echo htmlspecialchars( get_post_meta( $post->ID, 'tcp_price', true ) );?>" class="regular-text" type="text" style="width:12em"></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_tax_id"><?php _e( 'Tax', 'tcp' );?>:</label></th>
				<td>
					<select name="tcp_tax_id" id="tcp_tax_id">
					<?php $tax_id = tcp_get_the_tax_id( $post_id );
					$taxes = Taxes::getAll();
					foreach ( $taxes as $tax ) : ?>
						<option value="<?php echo $tax->tax_id;?>" <?php selected( $tax_id, $tax->tax_id );?>><?php echo $tax->title;?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_weight"><?php _e( 'Weight', 'tcp' );?>:</label></th>
				<td><input name="tcp_weight" id="tcp_weight" value="<?php echo htmlspecialchars( get_post_meta( $post->ID, 'tcp_weight', true ) );?>" class="regular-text" type="text" style="width:12em"></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_is_visible"><?php _e( 'Is visible (in loop or catalogue)', 'tcp' );?>:</label></th>
				<td><input type="checkbox" name="tcp_is_visible" id="tcp_is_visible" value="yes" <?php if ( get_post_meta( $post->ID, 'tcp_is_visible', true ) ):?>checked <?php endif;?> /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_sku"><?php _e( 'SKU', 'tcp' );?>:</label></th>
				<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( get_post_meta( $post->ID, 'tcp_sku', true ) );?>" class="regular-text" type="text" style="width:12em"></td>
			</tr>
			<tr valign="top">
			<?php	$settings = get_option( 'tcp_settings' );
					$stock_management = isset( $settings['stock_management'] ) ? $settings['stock_management'] : false;?>
				<th scope="row"><label for="tcp_stock"><?php _e( 'Stock', 'tcp' );?>:</label>
				<?php if ( ! $stock_management ) : 
					$path = 'admin.php?page=tcp_settings_page';?>
					<span class="description"><?php printf( __( 'Stock management is disabled. See the <a href="%s">settings</a> page to change this value.', 'tcp' ), $path );?></span>
				<?php endif;?>
				</th>
				<td><input name="tcp_stock" id="tcp_stock" value="<?php echo htmlspecialchars( get_post_meta( $post->ID, 'tcp_stock', true ) );?>" class="regular-text" type="text" style="width:10em">
				<br /><span class="description"><?php _e( 'Use value -1 (or left blank) for stores/products with no stock management.', 'tcp' );?></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_is_downloadable"><?php _e( 'Is downloadable', 'tcp' );?>:</label></th>
				<td><input type="checkbox" name="tcp_is_downloadable" id="tcp_is_downloadable" value="yes" <?php if ( get_post_meta( $post->ID, 'tcp_is_downloadable', true ) ):?>checked <?php endif;?> 
				onclick="if (this.checked) jQuery('.tcp_is_downloadable').show(); else jQuery('.tcp_is_downloadable').hide();"/>
			</tr>
			<?php
			if ( get_post_meta( $post->ID, 'tcp_is_downloadable', true ) )
				$style = '';
			else
				$style = 'style="display:none;"';
			?>
			<tr valign="top" class="tcp_is_downloadable" <?php echo $style;?>>
				<th scope="row"><label for="tcp_max_downloads"><?php _e( 'Max. downloads', 'tcp' );?>:</label></th>
				<td><input name="tcp_max_downloads" id="tcp_max_downloads" value="<?php echo (int)get_post_meta( $post->ID, 'tcp_max_downloads', true );?>" class="regular-text" type="text" style="width:4em" maxlength="4">
				<span class="description"><?php _e( 'If you don\'t want to set a number of maximun downloads, set this value to -1.', 'tcp' );?></span>
				</td>
			</tr>
			<tr valign="top" class="tcp_is_downloadable" <?php echo $style;?>>
				<th scope="row"><label for="tcp_days_to_expire"><?php _e( 'Days to expire', 'tcp' );?>:</label></th>
				<td><input name="tcp_days_to_expire" id="tcp_days_to_expire" value="<?php echo (int)get_post_meta( $post->ID, 'tcp_days_to_expire', true );?>" class="regular-text" type="text" style="width:4em" maxlength="4">
				<span class="description"><?php _e( 'Days to expire from the buying day. You can use -1 value.', 'tcp' );?></span>
				</td>
			</tr>
			<?php do_action( 'tcp_product_metabox_custom_fields', $post_id );?>
			</tbody></table>
		</div> <!-- form-wrap -->
		<?php
	}

	function saveCustomFields( $post_id, $post ) {
		if ( $post->post_type != ProductCustomPostType::$PRODUCT ) return;
		if ( !isset( $_POST[ 'tcp-product-custom-fields_wpnonce' ] ) || !wp_verify_nonce( $_POST[ 'tcp-product-custom-fields_wpnonce' ], 'tcp-product-custom-fields' ) ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		if ( $tcp_product_parent_id > 0 )
			if ( ! RelEntities::exists( $tcp_product_parent_id, $post_id ) )
				RelEntities::insert( $tcp_product_parent_id, $post_id );
		$post_id = tcp_get_default_id( $post_id );
		$tax_id = isset( $_POST['tcp_tax_id'] ) ? (int)$_POST['tcp_tax_id'] : 1;
		if ( $tax_id > 1 ) {
			$tax = Taxes::get( $tax_id );
			update_post_meta( $post_id, 'tcp_tax_id',  $tax_id );
			update_post_meta( $post_id, 'tcp_tax',  $tax->tax );
			update_post_meta( $post_id, 'tcp_tax_label', $tax->title );
		} else {
			update_post_meta( $post_id, 'tcp_tax_id', 0 );
			update_post_meta( $post_id, 'tcp_tax',  0 );
			update_post_meta( $post_id, 'tcp_tax_label', '' );
		}
		update_post_meta( $post_id, 'tcp_is_visible', isset( $_POST['tcp_is_visible'] )  ? $_POST['tcp_is_visible'] == 'yes': false );
		update_post_meta( $post_id, 'tcp_is_downloadable', isset( $_POST['tcp_is_downloadable'] )  ? $_POST['tcp_is_downloadable'] == 'yes': false );
		update_post_meta( $post_id, 'tcp_max_downloads', isset( $_POST['tcp_max_downloads'] )  ? (int)$_POST['tcp_max_downloads'] : 0 );
		update_post_meta( $post_id, 'tcp_days_to_expire', isset( $_POST['tcp_days_to_expire'] )  ? (int)$_POST['tcp_days_to_expire'] : 0 );
		update_post_meta( $post_id, 'tcp_type', isset( $_POST['tcp_type'] )  ? $_POST['tcp_type'] : 'SIMPLE' );
		update_post_meta( $post_id, 'tcp_price', isset( $_POST['tcp_price'] )  ? (float)$_POST['tcp_price'] : 0 );
		update_post_meta( $post_id, 'tcp_weight', isset( $_POST['tcp_weight'] )  ? (float)$_POST['tcp_weight'] : 0 );
		update_post_meta( $post_id, 'tcp_sku', isset( $_POST['tcp_sku'] )  ? $_POST['tcp_sku'] : '' );
		$tcp_stock = isset( $_POST['tcp_stock'] )  ? $_POST['tcp_stock'] : -1;
		if ( $tcp_stock == '' ) $tcp_stock = -1;
		update_post_meta( $post_id, 'tcp_stock', (int)$tcp_stock );
		do_action( 'tcp_product_metabox_save_custom_fields', $post_id );
		$this->refreshMoira();
	}

	function deleteCustomFields( $post_id ) {
		$post = get_post( $post_id );
		if ( $post->post_type != ProductCustomPostType::$PRODUCT ) return;
		if ( !isset( $_POST[ 'tcp-product-custom-fields_wpnonce' ] ) || !wp_verify_nonce( $_POST[ 'tcp-product-custom-fields_wpnonce' ], 'tcp-product-custom-fields' ) ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
		$post_id = tcp_get_default_id( $post_id );
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		if ( $tcp_product_parent_id > 0 )
			RelEntities::delete( $tcp_product_parent_id, $post_id );
		delete_post_meta( $new_post_id, 'tcp_price' );
		delete_post_meta( $new_post_id, 'tcp_tax_id' );
		delete_post_meta( $new_post_id, 'tcp_tax' );
		delete_post_meta( $new_post_id, 'tcp_tax_label' );
		delete_post_meta( $new_post_id, 'tcp_type' );
		delete_post_meta( $new_post_id, 'tcp_is_visible' );
		delete_post_meta( $new_post_id, 'tcp_is_downloadable' );
		delete_post_meta( $new_post_id, 'tcp_max_downloads' );
		delete_post_meta( $new_post_id, 'tcp_days_to_expire' );
		delete_post_meta( $new_post_id, 'tcp_weight' );
		delete_post_meta( $new_post_id, 'tcp_sku' );
		delete_post_meta( $new_post_id, 'tcp_stock' );
		do_action( 'tcp_product_metabox_delete_custom_fields', $post_id );
		$this->refreshMoira();
	}

	function refreshMoira() {
		$settings = get_option( 'tcp_settings' );
		$search_engine_activated = isset( $settings['search_engine_activated'] ) ? $settings['search_engine_activated'] : true;
		if ( $search_engine_activated ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/classes/TheCartPressSearchEngine.class.php' );
			TheCartPressSearchEngine::refresh();
		}
	}
}
?>
