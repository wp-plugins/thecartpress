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
		$saleable_post_types = tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-product-custom-fields', __( 'Product setup', 'tcp' ), array( $this, 'show' ), $post_type, 'normal', 'high' );
		add_action( 'save_post', array( $this, 'save' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'delete' ) );
	}

	function show() {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return;
		$post_id = tcp_get_default_id( $post->ID, $post->post_type );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		global $thecartpress;
		$stock_management	= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
		$lang				= isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang		= isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$is_translation		= $lang != $source_lang;
		if ( $is_translation && $post_id == $post->ID) {
			_e( 'After saving the title and content, you will be able to edit the specific fields of the product.', 'tcp' );
			return;
		}
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		if ( $tcp_product_parent_id > 0 ) {
			$create_grouped_relation = true;
			$tcp_rel_type = isset( $_REQUEST['rel_type'] ) ? $_REQUEST['rel_type'] : 'GROUPED';
		} else {
			$create_grouped_relation = false;
			$tcp_rel_type = tcp_get_the_product_type();
			if ( $post_id > 0 )
				$tcp_product_parent_id = RelEntities::getParent( $post_id );
		}
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
		?>
		<ul class="subsubsub">
			<?php $count = RelEntities::count( $post_id, 'PROD-PROD' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&post_type_to=post&rel_type=PROD-PROD" title="<?php _e( 'For crossing sell, adds products to the current product', 'tcp' ); ?>"><?php _e( 'related products', 'tcp' );?> <?php echo $count;?></a></li>
			<?php $count = RelEntities::count( $post_id, 'PROD-POST' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&post_type_to=post&rel_type=PROD-POST"  title="<?php _e( 'For crossing sell, adds post to the current product', 'tcp' ); ?>"><?php _e( 'related posts', 'tcp' );?> <?php echo $count;?></a></li>
			<!--<li>|</li>
			<li><a href="<?php echo $admin_path;?>CopyProduct.php&post_id=<?php echo $post_id;?>"><?php _e( 'copy product', 'tcp' );?></a></li>
			-->
		<?php 
		$product_type = tcp_get_the_product_type();
		
		if ( 'SIMPLE' == $product_type ) :
			$parents = RelEntities::getParents( $post_id );
			if ( is_array( $parents ) && count( $parents ) > 0 ) :
				$parent = $parents[0]->id_from; ?>aaaa
				<li>|</li>
				<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $parent;?>&rel_type=GROUPED;"><?php _e( 'parent\'s assigned products', 'tcp' );?></a></li>
			<?php endif;
		else : //At this moment, if not SIMPLE then is GROUPED or any other type that looks like GROUPED
			$count = RelEntities::count( $post_id );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=<?php echo $tcp_rel_type; ?>"><?php _e( 'assigned products', 'tcp' );?><?php echo $count;?></a></li>
			<li>|</li>
			<li><a href="post-new.php?post_type=<?php echo ProductCustomPostType::$PRODUCT;?>&tcp_product_parent_id=<?php echo $post_id;?>&rel_type=<?php echo $tcp_rel_type; ?>"><?php _e( 'create new assigned product', 'tcp' );?></a></li>
		<?php endif; ?>
		<?php do_action( 'tcp_product_metabox_toolbar', $post_id );?>
		<?php if ( tcp_is_downloadable( $post_id ) ) :?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>UploadFiles.php&post_id=<?php echo $post_id;?>"><?php echo __( 'file upload', 'tcp' ), $count;?></a></li>
			<!--<li>|</li>
			<li><a href="<?php echo $admin_path;?>FilesList.php&post_id=<?php echo $post_id;?>"><?php echo __( 'files', 'tcp' ), $count;?></a></li>-->
		<?php endif;?>
		</ul>
		<?php if ( $create_grouped_relation ): ?>
			<input type="hidden" name="tcp_product_parent_id" value="<?php echo $tcp_product_parent_id; ?>" />
			<input type="hidden" name="tcp_rel_type" value="<?php echo $tcp_rel_type; ?>" />
		<?php endif;?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp_noncename', 'tcp_noncename' );?>
			<table class="form-table"><tbody>
			<tr valign="top">
				<th scope="row"><label for="tcp_type"><?php _e( 'Type', 'tcp' );?>:</label></th>
				<td><?php tcp_html_select( 'tcp_type', tcp_get_product_types(), $product_type ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_price"><?php _e( 'Price', 'tcp' );?>:</label></th>
				<td><input type="text" min="0" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price" id="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $post_id ) );?>" class="regular-text" style="width:12em" />&nbsp;<?php tcp_the_currency();?>
				<p class="description"><?php printf( __( 'Current number format is %s', 'tcp'), tcp_get_number_format_example( 9999.99, false ) ); ?></p></td>
			</tr>
			<?php do_action( 'tcp_product_metabox_custom_fields_after_price', $post_id );?>
			<tr valign="top">
				<th scope="row"><label for="tcp_tax_id"><?php _e( 'Tax', 'tcp' );?>:</label></th>
				<td>
					<select name="tcp_tax_id" id="tcp_tax_id">
						<option value="0"><?php _e( 'No tax', 'tcp' );?></option>
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
				<td><input type="text" min="0" placeholder="<?php tcp_number_format_example(); ?>" name="tcp_weight" id="tcp_weight" value="<?php echo tcp_number_format( (float)tcp_get_the_weight( $post_id ) );?>" class="regular-text" style="width:12em" />&nbsp;<?php tcp_the_unit_weight(); ?>
				<p class="description"><?php printf( __( 'Current number format is %s', 'tcp'), tcp_get_number_format_example( 9999.99, false ) ); ?></p></td>
			</tr>
			<?php do_action( 'tcp_product_metabox_custom_fields_after_price', $post_id );?>
			<tr valign="top">
				<th scope="row"><label for="tcp_is_visible"><?php _e( 'Is visible (in loops/catalogue)', 'tcp' );?>:</label></th>
				<td><?php
					if ( $create_grouped_relation ) {
						$is_visible = false;
					} elseif ( tcp_get_the_product_type( $post_id ) == '' ) {
						$is_visible = true; //by default
					} else {
						$is_visible = tcp_is_visible( $post_id );
					}
				?><input type="checkbox" name="tcp_is_visible" id="tcp_is_visible" value="yes" <?php checked( $is_visible, true );?> /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="tcp_hide_buy_button"><?php _e( 'Hide buy button', 'tcp' );?>:</label></th>
				<?php $tcp_hide_buy_button = get_post_meta( $post_id, 'tcp_hide_buy_button', true );?>
				<td><input type="checkbox" name="tcp_hide_buy_button" id="tcp_hide_buy_button" <?php checked( $tcp_hide_buy_button, true );?> />
				<p class="description"><?php _e( 'Allow to hide the buy button for this product', 'tcp' ); ?></p></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tcp_exclude_range"><?php _e( 'Exclude for range prices', 'tcp' );?>:</label></th>
				<?php $tcp_exclude_range = get_post_meta( $post_id, 'tcp_exclude_range', true );?>
				<td><input type="checkbox" name="tcp_exclude_range" id="tcp_exclude_range" <?php checked( $tcp_exclude_range, true );?> />
				<span class="description"><?php _e( 'If the product is assigned to a Grouped product, this options exclude the product from the range price of the parent product.', 'tcp' );?></span></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tcp_order"><?php _e( 'Order (in loops/catalogue)', 'tcp' );?>:</label></th>
				<td><input name="tcp_order" id="tcp_order" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_order', true ) );?>" class="regular-text" type="text" style="width:4em">
				<span class="description"><?php _e( 'Numerical order.', 'tcp' );?></span></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="tcp_sku"><?php _e( 'SKU', 'tcp' );?>:</label></th>
				<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_sku', true ) );?>" class="regular-text" type="text" style="width:12em"></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="tcp_stock"><?php _e( 'Stock', 'tcp' );?>:</label>
				<?php if ( ! $stock_management ) : 
					$path = 'admin.php?page=tcp_settings_page';?>
					<span class="description"><?php printf( __( 'Stock management is disabled. See the <a href="%s">settings</a> page to change this value.', 'tcp' ), $path );?></span>
				<?php endif;?>
				</th>
				<td><input name="tcp_stock" id="tcp_stock" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_stock', true ) );?>" class="regular-text" type="text" style="width:10em" />
				<br /><span class="description"><?php _e( 'Use value -1 (or left blank) for stores/products with no stock management.', 'tcp' );?></span></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="tcp_is_downloadable"><?php _e( 'Is downloadable', 'tcp' );?>:</label></th>
				<td><input type="checkbox" name="tcp_is_downloadable" id="tcp_is_downloadable" value="yes" <?php if ( get_post_meta( $post_id, 'tcp_is_downloadable', true ) ):?>checked <?php endif;?> 
				onclick="if (this.checked) jQuery('.tcp_is_downloadable').show(); else jQuery('.tcp_is_downloadable').hide();"/>
				<?php if ( tcp_is_downloadable( $post_id ) ) : ?>
					<span class="description"><?php _e( 'File','tcp' );?>:<?php echo tcp_get_the_file( $post_id );?></span>
				<?php endif;?>
				</td>
			</tr>
			<?php
			if ( get_post_meta( $post_id, 'tcp_is_downloadable', true ) )
				$style = '';
			else
				$style = 'style="display:none;"';
			?>
			<tr valign="top" class="tcp_is_downloadable" <?php echo $style;?>>
				<th scope="row"><label for="tcp_max_downloads"><?php _e( 'Max. downloads', 'tcp' );?>:</label></th>
				<td><input name="tcp_max_downloads" id="tcp_max_downloads" value="<?php echo (int)get_post_meta( $post_id, 'tcp_max_downloads', true );?>" class="regular-text" type="text" style="width:4em" maxlength="4" />
				<span class="description"><?php _e( 'If you don\'t want to set a number of maximun downloads, set this value to -1.', 'tcp' );?></span>
				</td>
			</tr>
			<tr valign="top" class="tcp_is_downloadable" <?php echo $style;?>>
				<th scope="row"><label for="tcp_days_to_expire"><?php _e( 'Days to expire', 'tcp' );?>:</label></th>
				<td><input name="tcp_days_to_expire" id="tcp_days_to_expire" value="<?php echo (int)get_post_meta( $post_id, 'tcp_days_to_expire', true );?>" class="regular-text" type="text" style="width:4em" maxlength="4" />
				<span class="description"><?php _e( 'Days to expire from the buying day. You can use -1 value.', 'tcp' );?></span>
				</td>
			</tr>
			<?php do_action( 'tcp_product_metabox_custom_fields', $post_id );?>
			</tbody></table>
		</div> <!-- form-wrap -->
		<?php
	}

	function save( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_noncename'] ) ? $_POST['tcp_noncename'] : '', 'tcp_noncename' ) ) return array( $post_id, $post );
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		$post_id = tcp_get_default_id( $post_id, $post->post_type );
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		$create_grouped_relation = $tcp_product_parent_id > 0;
		if ( $create_grouped_relation ) {
			$rel_type = isset( $_REQUEST['tcp_rel_type'] ) ? $_REQUEST['tcp_rel_type'] : 'GROUPED';
			if ( ! RelEntities::exists( $tcp_product_parent_id, $post_id, $rel_type ) ) 
				RelEntities::insert( $tcp_product_parent_id, $post_id, $rel_type );
			$args = array( 'fields' => 'ids' );
			$terms = wp_get_post_terms( $tcp_product_parent_id, ProductCustomPostType::$PRODUCT_CATEGORY, array( 'fields' => 'ids' ) );
			wp_set_post_terms( $post_id, $terms, ProductCustomPostType::$PRODUCT_CATEGORY );
			$terms = wp_get_post_terms( $tcp_product_parent_id, ProductCustomPostType::$PRODUCT_TAG, array( 'fields' => 'names' ) );
			wp_set_post_terms( $post_id, $terms, ProductCustomPostType::$PRODUCT_TAG );
			$terms = wp_get_post_terms( $tcp_product_parent_id, ProductCustomPostType::$SUPPLIER_TAG, array( 'fields' => 'ids' ) );
			wp_set_post_terms( $post_id, $terms, ProductCustomPostType::$SUPPLIER_TAG );
		}
		$tax_id = isset( $_POST['tcp_tax_id'] ) ? (int)$_POST['tcp_tax_id'] : 0;
		if ( $tax_id > 0 ) {
			$tax = Taxes::get( $tax_id );
			update_post_meta( $post_id, 'tcp_tax_id',  $tax_id );
		} else {
			update_post_meta( $post_id, 'tcp_tax_id', 0 );
		}
		update_post_meta( $post_id, 'tcp_hide_buy_button', isset( $_POST['tcp_hide_buy_button'] ) );
		update_post_meta( $post_id, 'tcp_exclude_range', isset( $_POST['tcp_exclude_range'] ) );
		update_post_meta( $post_id, 'tcp_is_downloadable', isset( $_POST['tcp_is_downloadable'] ) ? $_POST['tcp_is_downloadable'] == 'yes' : false );
		update_post_meta( $post_id, 'tcp_max_downloads', isset( $_POST['tcp_max_downloads'] ) ? (int)$_POST['tcp_max_downloads'] : 0 );
		update_post_meta( $post_id, 'tcp_days_to_expire', isset( $_POST['tcp_days_to_expire'] ) ? (int)$_POST['tcp_days_to_expire'] : 0 );
		if ( isset( $_POST['tcp_type'] ) ) {
			$type = $_POST['tcp_type'];
			$is_visible = isset( $_POST['tcp_is_visible'] ) ? $_POST['tcp_is_visible'] == 'yes' : false;
		} else {
			$type = 'SIMPLE';
			$is_visible = true;
		}
		update_post_meta( $post_id, 'tcp_type', $type );
		update_post_meta( $post_id, 'tcp_is_visible', $is_visible );
		if ( $type == 'GROUPED' )
			$price = 0;
		else {
			$price = isset( $_POST['tcp_price'] ) ? $_POST['tcp_price'] : 0;
			$price = tcp_input_number( $price );
		}
		update_post_meta( $post_id, 'tcp_price', $price );
		$weight = isset( $_POST['tcp_weight'] ) ? (float)$_POST['tcp_weight'] : 0;
		$weight = tcp_input_number( $weight );
		update_post_meta( $post_id, 'tcp_weight', $weight );
		update_post_meta( $post_id, 'tcp_order', isset( $_POST['tcp_order'] ) ? (int)$_POST['tcp_order'] : '' );
		update_post_meta( $post_id, 'tcp_sku', isset( $_POST['tcp_sku'] ) ? $_POST['tcp_sku'] : '' );
		$tcp_stock = isset( $_POST['tcp_stock'] ) ? $_POST['tcp_stock'] : -1;
		if ( $tcp_stock == '' ) $tcp_stock = -1;
		update_post_meta( $post_id, 'tcp_stock', (int)$tcp_stock );
		
		$translations = tcp_get_all_translations( $post_id, get_post_type( $post_id ) );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id ) {
					update_post_meta( $translation->element_id, 'tcp_is_visible', isset( $_POST['tcp_is_visible'] ) ? $_POST['tcp_is_visible'] == 'yes' : false );
					update_post_meta( $translation->element_id, 'tcp_hide_buy_button', isset( $_POST['tcp_hide_buy_button'] ) );
					update_post_meta( $translation->element_id, 'tcp_order', isset( $_POST['tcp_order'] ) ? (int)$_POST['tcp_order'] : '' );
					update_post_meta( $translation->element_id, 'tcp_price', isset( $_POST['tcp_price'] ) ? (float)$_POST['tcp_price'] : 0 );
				}
		do_action( 'tcp_product_metabox_save_custom_fields', $post_id );
		$this->refreshMoira();
		return array( $post_id, $post );
	}

	function delete( $post_id ) {
		$post = get_post( $post_id );
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
		$post_id = tcp_get_default_id( $post_id, $post->post_type );
		RelEntities::deleteAll( $post_id );
		RelEntities::deleteAllTo( $post_id );
		delete_post_meta( $post_id, 'tcp_price' );
		delete_post_meta( $post_id, 'tcp_tax_id' );
		delete_post_meta( $post_id, 'tcp_type' );
		delete_post_meta( $post_id, 'tcp_is_visible' );
		delete_post_meta( $post_id, 'tcp_hide_buy_button' );
		delete_post_meta( $post_id, 'tcp_is_downloadable' );
		delete_post_meta( $post_id, 'tcp_max_downloads' );
		delete_post_meta( $post_id, 'tcp_days_to_expire' );
		delete_post_meta( $post_id, 'tcp_weight' );
		delete_post_meta( $post_id, 'tcp_sku' );
		delete_post_meta( $post_id, 'tcp_stock' );
		delete_post_meta( $post_id, 'tcp_order' );
		$translations = tcp_get_all_translations( $post_id, get_post_type( $post_id ) );
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
		do_action( 'tcp_product_metabox_delete_custom_fields', $post_id );
		$this->refreshMoira();
	}

	function refreshMoira() {
		global $thecartpress;
		$search_engine_activated = isset( $thecartpress->settings['search_engine_activated'] ) ? $thecartpress->settings['search_engine_activated'] : true;
		if ( $search_engine_activated ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/classes/TheCartPressSearchEngine.class.php' );
			TheCartPressSearchEngine::refresh();
		}
	}
}
?>
