<?php
/**
 * This file is part of TheCartPtess.
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

if ( !function_exists( 'ad_selected_multiple' ) ) {
	function ad_selected_multiple( $values, $value, $echo = true ) {
		if ( in_array( $value, $values ) ) {
			if ( $echo ) {
				echo ' selected="true"';
			} else {
				return ' selected="true"';
			}
		}
	}
}

$load_from_request	= false;
$posttype_id		= isset( $_REQUEST['posttype_id'] ) ? $_REQUEST['posttype_id'] : -1;

if ( isset( $_REQUEST['save_posttype'] ) ) {
	$_REQUEST['name_id'] = str_replace( ' ' , '-', $_REQUEST['name_id'] );
	$_REQUEST['name_id'] = str_replace( '_' , '-', $_REQUEST['name_id'] );
	$posttype = array(
		'name'				=> isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Post type Name', 'tcp' ),
		'name_id'			=> isset( $_REQUEST['name_id'] ) ? $_REQUEST['name_id'] : __( 'posttype-name', 'tcp' ),
		'desc'				=> isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '',
		'activate'			=> isset( $_REQUEST['activate'] ),
		'singular_name'		=> isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' ),
		'add_new'			=> isset( $_REQUEST['add_new'] ) ? $_REQUEST['add_new'] : __( 'Add New', 'tcp' ),
		'add_new_item'		=> isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New', 'tcp' ),
		'edit_item'			=> isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit', 'tcp' ),
		'new_item'			=> isset( $_REQUEST['new_item'] ) ? $_REQUEST['new_item'] : __( 'Add New', 'tcp' ),
		'view_item'			=> isset( $_REQUEST['view_item'] ) ? $_REQUEST['view_item'] : __( 'View', 'tcp' ),
		'search_items'		=> isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search', 'tcp' ),
		'not_found'			=> isset( $_REQUEST['not_found'] ) ? $_REQUEST['not_found'] : __( 'Not found', 'tcp' ),
		'not_found_in_trash'=> isset( $_REQUEST['not_found_in_trash'] ) ? $_REQUEST['not_found_in_trash'] : __( 'Not found in Trash:', 'tcp' ),
//		'parent_item_colon'	=> isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' ),
		'public'			=> isset( $_REQUEST['public'] ),
		'show_ui'			=> isset( $_REQUEST['show_ui'] ),
		'show_in_menu'		=> isset( $_REQUEST['show_in_menu'] ),
		'can_export'		=> isset( $_REQUEST['can_export'] ),
		'show_in_nav_menus'	=> isset( $_REQUEST['show_in_nav_menus'] ),
//		'capability_type'	=> 'post',
		'supports'			=> isset( $_REQUEST['supports'] ) ? $_REQUEST['supports'] : array( 'title', 'excerpt', 'editor', ),
		'rewrite'			=> isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false,
		'has_archive'		=> isset( $_REQUEST['has_archive'] ) ? isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false : false,
//		'has_archive'		=> isset( $_REQUEST['has_archive'] ) && strlen( $_REQUEST['has_archive'] ) > 0 ? $_REQUEST['has_archive'] : false,
		//TheCartPress support
		'is_saleable'		=> isset( $_REQUEST['is_saleable'] ),
	);
	$posttypes = get_option( 'tcp-posttypes-generator' );
	if ( ! $posttypes ) $posttypes = array();
	if ( $posttype_id > -1 )
		$posttypes[$posttype_id] = $posttype;
	else {
		$posttypes[] = $posttype;
		$posttype_id = end( array_keys( $posttypes ) );
	}
	update_option( 'tcp-posttypes-generator', $posttypes );
	update_option( 'tcp_rewrite_rules', true ); ?>
	<div id="message" class="updated"><p>
		<?php _e( 'Post type saved', 'tcp' );?> 
	</p></div><?php
	$load_from_request = true;
} elseif ( $posttype_id > -1 ) {
	$posttypes = get_option( 'tcp-posttypes-generator' );
	if ( is_array( $posttypes ) && count( $posttypes ) > 0 ) {
		$posttype = $posttypes[$posttype_id];
		$name				= isset( $posttype['name'] ) ? $posttype['name'] : __( 'Post type Name', 'tcp' );
		$name_id			= isset( $posttype['name_id'] ) ? $posttype['name_id'] : __( 'posttype-name', 'tcp' );
		$desc				= isset( $posttype['desc'] ) ? $posttype['desc'] : '';
		$activate			= isset( $posttype['activate'] ) ? $posttype['activate'] : false;
		$singular_name		= isset( $posttype['singular_name'] ) ? $posttype['singular_name'] : __( 'Singular name', 'tcp' );
		$add_new			= isset( $posttype['add_new'] ) ? $posttype['add_new'] : __( 'Add New', 'tcp' );
		$add_new_item		= isset( $posttype['add_new_item'] ) ? $posttype['add_new_item'] : __( 'Add New', 'tcp' );
		$edit_item			= isset( $posttype['edit_item'] ) ? $posttype['edit_item'] : __( 'Edit', 'tcp' );
		$new_item			= isset( $posttype['new_item'] ) ? $posttype['new_item'] : __( 'Add New', 'tcp' );
		$view_item			= isset( $posttype['view_item'] ) ? $posttype['view_item'] : __( 'View', 'tcp' );
		$search_items		= isset( $posttype['search_items'] ) ? $posttype['search_items'] : __( 'Search', 'tcp' );
		$not_found			= isset( $posttype['not_found'] ) ? $posttype['not_found'] : __( 'Not found', 'tcp' );
		$not_found_in_trash = isset( $posttype['not_found_in_trash'] ) ? $posttype['not_found_in_trash'] : __( 'Not found in Trash:', 'tcp' );
//		$parent_item_colon	= isset( $posttype['parent_item_colon'] ) ? $posttype['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
		$public				= isset( $posttype['public'] ) ? $posttype['public'] : false;
		$show_ui			= isset( $posttype['show_ui'] ) ? $posttype['show_ui'] : false;
		$show_in_menu		= isset( $posttype['show_in_menu'] ) ? $posttype['show_in_menu'] : true;
		$can_export			= isset( $posttype['can_export'] ) ? $posttype['can_export'] : true;
		$show_in_nav_menus	= isset( $posttype['show_in_nav_menus'] ) ? $posttype['show_in_nav_menus'] : true;
//		$capability_type'	= 'post',
		$supports			= isset( $posttype['supports'] ) ? $posttype['supports'] : array( 'title', 'editor', );
		$rewrite			= isset( $posttype['rewrite'] ) && strlen( $posttype['rewrite'] ) > 0 ? $posttype['rewrite'] : false;
		$has_archive		= isset( $posttype['has_archive'] ) ? isset( $posttype['rewrite'] ) && strlen( $posttype['rewrite'] ) > 0 ? $posttype['rewrite'] : false : false;
//		$has_archive		= isset( $posttype['has_archive'] ) && strlen( $posttype['has_archive'] ) > 0 ? $posttype['has_archive'] : false;
		$is_saleable			= isset( $posttype['is_saleable'] ) ? $posttype['is_saleable'] : false;
	} else {
		$load_from_request = true;
	}
} else
	$load_from_request = true;

if ( $load_from_request ) {
	$name				= isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Post type Name', 'tcp' );
	$name_id			= isset( $_REQUEST['name_id'] ) ? $_REQUEST['name_id'] : __( 'posttype-name', 'tcp' );
	$activate			= isset( $_REQUEST['activate'] );
	$desc				= isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
	$singular_name		= isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' );
	$add_new			= isset( $_REQUEST['add_new'] ) ? $_REQUEST['add_new'] : __( 'Add New', 'tcp' );
	$add_new_item		= isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New', 'tcp' );
	$edit_item			= isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit', 'tcp' );
	$new_item			= isset( $_REQUEST['new_item'] ) ? $_REQUEST['new_item'] : __( 'Add New', 'tcp' );
	$view_item			= isset( $_REQUEST['view_item'] ) ? $_REQUEST['view_item'] : __( 'View', 'tcp' );
	$search_items		= isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search', 'tcp' );
	$not_found			= isset( $_REQUEST['not_found'] ) ? $_REQUEST['not_found'] : __( 'Not found', 'tcp' );
	$not_found_in_trash = isset( $_REQUEST['not_found_in_trash'] ) ? $_REQUEST['not_found_in_trash'] : __( 'Not found in Trash:', 'tcp' );
//	$parent_item_colon	= isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
	$public				= isset( $_REQUEST['public'] );
	$show_ui			= isset( $_REQUEST['show_ui'] );
	$show_in_menu		= isset( $_REQUEST['show_in_menu'] );
	$can_export			= isset( $_REQUEST['can_export'] );
	$show_in_nav_menus	= isset( $_REQUEST['show_in_nav_menus'] );
//	$capability_type'	= 'post',
	$supports			= isset( $_REQUEST['supports'] ) ? $_REQUEST['supports'] : array( 'title', 'excerpt', 'editor', );
	$rewrite			= isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false;
	$has_archive		= isset( $_REQUEST['has_archive'] ) ? isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false : false;
	//$has_archive		= isset( $_REQUEST['has_archive'] ) && strlen( $_REQUEST['has_archive'] ) > 0 ? $_REQUEST['has_archive'] : false;
	$is_saleable			= isset( $_REQUEST['is_saleable'] );
}
?>
<div class="wrap">
<h2><?php _e( 'Post type', 'tcp' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>PostTypeList.php"><?php _e( 'return to the list', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<form method="post">
	<input type="hidden" name="posttype_id" value="<?php echo $posttype_id;?>" />
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="name"><?php _e( 'Post type name', 'tcp' );?>:<span class="compulsory">(*)</span></label>
		</th>
		<td>
			<input type="text" id="name" name="name" value="<?php echo $name;?>" size="20" maxlength="50" />
			<?php //tcp_show_error_msg( $error_taxo, 'name' );?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="name_id"><?php _e( 'Name Id', 'tcp' );?>:<span class="compulsory">(*)</span>
			<br /><span class="description"><?php _e( 'No blank spaces', 'tcp' );?></span></label>
		</th>
		<td>
			<input type="text" id="name_id" name="name_id" value="<?php echo $name_id;?>" size="20" maxlength="50" />
			<?php //tcp_show_error_msg( $error_taxo, 'name' );?>
		</td>
	</tr>
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
			<label for="add_new"><?php _e( 'Add new', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="add_new" name="add_new" value="<?php echo $add_new;?>" size="20" maxlength="50" />
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
			<label for="edit_item"><?php _e( 'Edit item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="edit_item" name="edit_item" value="<?php echo $edit_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="new_item"><?php _e( 'New item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="new_item" name="new_item" value="<?php echo $new_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="view_item"><?php _e( 'View item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="view_item" name="view_item" value="<?php echo $view_item;?>" size="20" maxlength="50" />
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
			<label for="not_found"><?php _e( 'Not found', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="not_found" name="not_found" value="<?php echo $not_found;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="not_found_in_trash"><?php _e( 'Not found in Trash', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="not_found_in_trash" name="not_found_in_trash" value="<?php echo $not_found_in_trash;?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="public"><?php _e( 'Public', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="public" name="public" value="y" <?php checked( $public );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="show_ui"><?php _e( 'Show UI', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="show_ui" name="show_ui" value="y" <?php checked( $show_ui );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="show_in_menu"><?php _e( 'Show in menu', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="show_in_menu" name="show_in_menu" value="y" <?php checked( $show_in_menu );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="can_export"><?php _e( 'Can be exported', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="can_export" name="can_export" value="y" <?php checked( $can_export );?> />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="show_in_nav_menus"><?php _e( 'Show in nav menus', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="show_in_nav_menus" name="show_in_nav_menus" value="y" <?php checked( $show_in_nav_menus );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="supports"><?php _e( 'Support', 'tcp' );?>:</label>
		</th>
		<td>
			<select id="supports" name="supports[]" multiple="true" size="8" style="height: auto" />
				<option value="title" <?php ad_selected_multiple( $supports, 'title' );?>><?php _e( 'Title', 'tcp' );?></option>
				<option value="editor" <?php ad_selected_multiple( $supports, 'editor' );?>><?php _e( 'Editor', 'tcp' );?></option>
				<option value="author" <?php ad_selected_multiple( $supports, 'author' );?>><?php _e( 'Author', 'tcp' );?></option>
				<option value="thumbnail" <?php ad_selected_multiple( $supports, 'thumbnail' );?>><?php _e( 'Thumbnail', 'tcp' );?></option>
				<option value="excerpt" <?php ad_selected_multiple( $supports, 'excerpt' );?>><?php _e( 'Excerpt', 'tcp' );?></option>
				<option value="trackbacks" <?php ad_selected_multiple( $supports, 'trackbacks' );?>><?php _e( 'Trackbacks', 'tcp' );?></option>
				<option value="custom-fields" <?php ad_selected_multiple( $supports, 'custom-fields' );?>><?php _e( 'Custom fields', 'tcp' );?></option>
				<option value="comments" <?php ad_selected_multiple( $supports, 'comments' );?>><?php _e( 'Comments', 'tcp' );?></option>
				<option value="revisions" <?php ad_selected_multiple( $supports, 'revisions' );?>><?php _e( 'Revisions', 'tcp' );?></option>
				<option value="page-attributes" <?php ad_selected_multiple( $supports, 'page-attributes' );?>><?php _e( 'Page attributes', 'tcp' );?></option>
			</select>
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
	<tr valign="top">
		<th scope="row">
			<label for="has_archive"><?php _e( 'Has archive', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="has_archive" name="has_archive" value="yes" <?php checked( $has_archive != false );?> />
			<!--<input type="text" id="has_archive" name="has_archive" value="<?php echo $has_archive;?>" size="20" maxlength="50" />-->
		</td>
	</tr>
	<?php global $thecartpress;
	$disable_ecommerce = isset( $thecartpress->settings['disable_ecommerce'] ) ? $thecartpress->settings['disable_ecommerce'] : false;
	if ( ! $disable_ecommerce ) : ?>
	<tr valign="top">
		<th scope="row">
			<label for="is_saleable"><?php _e( 'Is saleable', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="is_saleable" name="is_saleable" <?php checked( $is_saleable );?> value="yes" />
		</td>
	</tr>
	<?php endif; ?>
	</table>

	<p class="submit">
		<input type="submit" name="save_posttype" id="save_posttype" value="<?php _e( 'Save' , 'tcp' );?>" class="button-primary" />
	</p>
</form>
