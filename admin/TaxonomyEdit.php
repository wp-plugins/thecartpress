<?php
/**
 * This file is part of wp-taxonomy-engine.
 * 
 * wp-taxonomy-engine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wp-taxonomy-engine is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wp-taxonomy-engine.  If not, see <http://www.gnu.org/licenses/>.
 */

$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
$load_from_request = false;
$taxonomy_id = isset( $_REQUEST['taxonomy_id'] ) ? $_REQUEST['taxonomy_id'] : -1;

if ( isset( $_REQUEST['save_taxo'] ) ) {
	$_REQUEST['name_id'] = str_replace( ' ' , '-', $_REQUEST['name_id'] );
	$_REQUEST['name_id'] = str_replace( '_' , '-', $_REQUEST['name_id'] );
	$taxo = array(
		'post_type'			=> isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'post',
		'name'				=> isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Category Name', 'tcp' ),
		'name_id'			=> isset( $_REQUEST['name_id'] ) ? $_REQUEST['name_id'] : __( 'category-name', 'tcp' ),
		'activate'			=> isset( $_REQUEST['activate']),
		'label'				=> isset( $_REQUEST['label'] ) ? $_REQUEST['label'] : __( 'Label', 'tcp' ),
		'singular_label'	=> isset( $_REQUEST['singular_label'] ) ? $_REQUEST['singular_label'] : __( 'Singular label', 'tcp' ),
		'singular_name'		=> isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' ),
		'search_items'		=> isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search Categories', 'tcp' ),
		'all_items'			=> isset( $_REQUEST['all_items'] ) ? $_REQUEST['all_items'] : __( 'All Categories', 'tcp' ),
		'parent_item'		=> isset( $_REQUEST['parent_item'] ) ? $_REQUEST['parent_item'] : __( 'Parent Category', 'tcp' ),
		'parent_item_colon'	=> isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' ),
		'edit_item'			=> isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit Category', 'tcp' ),
		'update_item'		=> isset( $_REQUEST['update_item'] ) ? $_REQUEST['update_item'] : __( 'Update Category', 'tcp' ),
		'add_new_item'		=> isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New Category', 'tcp' ),
		'new_item_name'		=> isset( $_REQUEST['new_item_name'] ) ? $_REQUEST['new_item_name'] : __( 'New Category Name', 'tcp' ),
		'desc'				=> isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '',
		'hierarchical'		=> isset( $_REQUEST['hierarchical'] ),
		'rewrite'			=> isset( $_REQUEST['rewrite'] ) ? strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false : false,
	);
	$taxonomies = get_option( 'tcp-taxonomies-generator' );
	if ( ! $taxonomies ) $taxonomies = array();
	if ( $taxonomy_id > -1 )
		$taxonomies[$taxonomy_id] = $taxo;
	else {
		$taxonomies[] = $taxo;
		$taxonomy_id = end( array_keys( $taxonomies ) );
	}
	update_option( 'tcp-taxonomies-generator', $taxonomies );
	update_option( 'tcp_rewrite_rules', true ); ?>
	<div id="message" class="updated"><p>
		<?php _e( 'Taxonomy saved', 'tcp' );?>
	</p></div><?php
	$load_from_request = true;
} elseif ( $taxonomy_id > -1 ) {
	$taxonomies = get_option( 'tcp-taxonomies-generator' );
	if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
		$taxo = $taxonomies[$taxonomy_id];
		$post_type			= isset( $taxo['post_type'] ) ? $taxo['post_type'] : 'post';
		$name				= isset( $taxo['name'] ) ? $taxo['name'] : __( 'Category name', 'tcp' );
		$name_id			= isset( $taxo['name_id'] ) ? $taxo['name_id'] : __( 'category-name', 'tcp' );
		$label				= isset( $taxo['label'] ) ? $taxo['label'] : __( 'Label', 'tcp' );
		$singular_label		= isset( $taxo['singular_label'] ) ? $taxo['singular_label'] : __( 'Singular label', 'tcp' );
		$activate			= isset( $taxo['activate'] ) ? $taxo['activate'] : false;
		$singular_name		= isset( $taxo['singular_name'] ) ? $taxo['singular_name'] : __( 'Singular name', 'tcp' );
		$search_items		= isset( $taxo['search_items'] ) ? $taxo['search_items'] : __( 'Search Categories', 'tcp' );
		$all_items			= isset( $taxo['all_items'] ) ? $taxo['all_items'] : __( 'All Categories', 'tcp' );
		$parent_item		= isset( $taxo['parent_item'] ) ? $taxo['parent_item'] : __( 'Parent Category:', 'tcp' );
		$parent_item_colon	= isset( $taxo['parent_item_colon'] ) ? $taxo['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
		$edit_item			= isset( $taxo['edit_item'] ) ? $taxo['edit_item'] : __( 'Edit Category', 'tcp' );
		$update_item		= isset( $taxo['update_item'] ) ? $taxo['update_item'] : __( 'Update Category', 'tcp' );
		$add_new_item		= isset( $taxo['add_new_item'] ) ? $taxo['add_new_item'] : __( 'Add New Category', 'tcp' );
		$new_item_name		= isset( $taxo['new_item_name'] ) ? $taxo['new_item_name'] : __( 'New Category Name', 'tcp' );
		$desc				= isset( $taxo['desc'] ) ? $taxo['desc'] : '';
		$hierarchical		= isset( $taxo['hierarchical'] ) ? $taxo['hierarchical'] : false;
		$rewrite			= isset( $taxo['rewrite'] ) ? strlen( $taxo['rewrite'] ) > 0 ? $taxo['rewrite'] : false : false;	
	} else {
		$load_from_request = true;
	}
} else {
	$load_from_request = true;
}
if ( $load_from_request ) {
	$post_type			= isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'post';
	$name				= isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Category Name', 'tcp' );
	$name_id			= isset( $_REQUEST['name_id'] ) ? $_REQUEST['name_id'] : __( 'category-name', 'tcp' );
	$activate			= isset( $_REQUEST['activate']);
//	$label				= isset( $_REQUEST['label'] ) ? $_REQUEST['label'] : __( 'Label', 'tcp' );
//	$singular_label		= isset( $_REQUEST['singular_label'] ) ? $_REQUEST['singular_label'] : __( 'Singular label', 'tcp' );
	$singular_name		= isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' );
	$search_items		= isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search Categories', 'tcp' );
	$all_items			= isset( $_REQUEST['all_items'] ) ? $_REQUEST['all_items'] : __( 'All Categories', 'tcp' );
	$parent_item		= isset( $_REQUEST['parent_item'] ) ? $_REQUEST['parent_item'] : __( 'Parent Category:', 'tcp' );
	$parent_item_colon	= isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
	$edit_item			= isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit Category', 'tcp' );
	$update_item		= isset( $_REQUEST['update_item'] ) ? $_REQUEST['update_item'] : __( 'Update Category', 'tcp' );
	$add_new_item		= isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New Category', 'tcp' );
	$new_item_name		= isset( $_REQUEST['new_item_name'] ) ? $_REQUEST['new_item_name'] : __( 'New Category Name', 'tcp' );
	$desc				= isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
	$hierarchical		= isset( $_REQUEST['hierarchical'] );
	$rewrite			= isset( $_REQUEST['rewrite'] ) ? $_REQUEST['rewrite'] : false;
}
?>
<div class="wrap">
<h2><?php _e( 'Taxonomy', 'tcp' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>TaxonomyList.php"><?php _e( 'return to the list', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<form method="post">
	<input type="hidden" name="taxonomy_id" value="<?php echo $taxonomy_id;?>" />
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="post_type"><?php _e( 'Post Type', 'tcp' );?>:</label>
		</th>
		<td>
			<select name="post_type" id="post_type">
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $type ) : ?>
				<option value="<?php echo $type->name; ?>"<?php selected( $post_type, $type->name ); ?>><?php echo $type->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="name"><?php _e( 'Taxonomy name', 'tcp' );?>:<span class="compulsory">(*)</span></label>
		</th>
		<td>
			<input type="text" id="name" name="name" value="<?php echo $name;?>" size="20" maxlength="50" />
			<?php //tcp_show_error_msg( $error_taxo, 'name' );?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="name_id"><?php _e( 'Name id', 'tcp' );?>:<span class="compulsory">(*)</span>
			<br /><span class="description"><?php _e( 'No blank spaces', 'tcp' );?></span></label>
		</th>
		<td>
			<input type="text" id="name_id" name="name_id" value="<?php echo $name_id;?>" size="20" maxlength="50" />
		</td>
	</tr>
<!--	<tr valign="top">
		<th scope="row">
			<label for="label"><?php _e( 'Label', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="label" name="label" value="<?php echo $label;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="singular_label"><?php _e( 'Singular label', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="singular_label" name="singular_label" value="<?php echo $singular_label;?>" size="20" maxlength="50" />
		</td>
	</tr>-->
	<tr valign="top">
		<th scope="row">
			<label for="activate"><?php _e( 'Activated', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="activate" name="activate" value="y" <?php checked( $activate );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="desc"><?php _e( 'Description', 'tcp' );?>:</label>
		</th>
		<td>
			<textarea id="desc" name="desc" cols="40" rows="4"><?php echo $desc;?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="singular_name"><?php _e( 'Singular name', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="singular_name" name="singular_name" value="<?php echo $singular_name;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="search_items"><?php _e( 'Search items', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="search_items" name="search_items" value="<?php echo $search_items;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="all_items"><?php _e( 'All items', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="all_items" name="all_items" value="<?php echo $all_items;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="parent_item"><?php _e( 'Parent item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="parent_item" name="parent_item" value="<?php echo $parent_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="parent_item_colon"><?php _e( 'Parent item colon', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="parent_item_colon" name="parent_item_colon" value="<?php echo $parent_item_colon;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="edit_item"><?php _e( 'Edit item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="edit_item" name="edit_item" value="<?php echo $edit_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="update_item"><?php _e( 'Update item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="update_item" name="update_item" value="<?php echo $update_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="add_new_item"><?php _e( 'Add new item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="add_new_item" name="add_new_item" value="<?php echo $add_new_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="new_item_name"><?php _e( 'New item name', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="new_item_name" name="new_item_name" value="<?php echo $new_item_name;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="hierarchical"><?php _e( 'Hierarchical', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="hierarchical" name="hierarchical" value="y" <?php checked( $hierarchical );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="rewrite"><?php _e( 'Rewrite', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="rewrite" name="rewrite" value="<?php echo $rewrite;?>" size="20" maxlength="50" />
		</td>
	</tr>
	</table>

	<p class="submit">
		<input type="submit" name="save_taxo" id="save_taxo" value="<?php _e( 'Save' , 'tcp' );?>" class="button-primary" />
	</p>
</form>
