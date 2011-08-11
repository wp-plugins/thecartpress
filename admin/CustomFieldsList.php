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

function tcp_create_id( $post_type, $label ) {
	$internal_id = 'tcp_' . str_replace ( ' ' , '_' , $label );
	$i = 0;
	while ( tcp_exists_custom_field_def( $post_type, $internal_id ) ) {
		$internal_id = $internal_id . '_' . $i++;
	}
	return $internal_id;
}

if ( isset( $_REQUEST['post_type'] ) ) {
	$post_type =  $_REQUEST['post_type'];
} else {
	$post_type = post_type_exists( 'tcp_product' ) ? 'tcp_product' : 'post';
}

if ( isset( $_REQUEST['tcp_save_custom_field'] ) ) {
	$label = isset( $_REQUEST['label'] ) ? trim( $_REQUEST['label'] ) : '';
	if ( strlen( $label ) > 0 ) {
		$id = tcp_create_id( $post_type, $label );
		$type = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : 'string';
		$values = isset( $_REQUEST['values'] ) ? $_REQUEST['values'] : 0;
		$desc = isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
		tcp_add_custom_field_def( $post_type, $id, $label, $type, $values, $desc );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Custom field saved', 'tcp' );?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_modify_custom_field'] ) ) {
	$custom_field_id = isset( $_REQUEST['custom_field_id'] ) ? trim( $_REQUEST['custom_field_id'] ) : -1;//array index
	$label = isset( $_REQUEST['label'] ) ? trim( $_REQUEST['label'] ) : '';
	if ( strlen( $label ) > 0 ) {
		tcp_delete_custom_field_def( $post_type, $custom_field_id );
		$internal_id = isset( $_REQUEST['internal_id'] ) ? $_REQUEST['internal_id'] : 'internal_id';
		$type = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : 'string';
		$values = isset( $_REQUEST['values'] ) ? $_REQUEST['values'] : 0;
		$desc = isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
		tcp_add_custom_field_def( $post_type, $internal_id, $label, $type, $values, $desc );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Custom field saved', 'tcp' );?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_delete_custom_field'] ) ) {
	$id = isset( $_REQUEST['custom_field_id'] ) ? trim( $_REQUEST['custom_field_id'] ) : -1;
	if ( $id > -1 ) {
		$custom_fields = tcp_get_custom_fields_def( $post_type );
		if ( isset( $custom_fields[$id] ) && isset( $custom_fields[$id]['id'] ) ) {
			$custom_field_id = $custom_fields[$id]['id'];
			tcp_delete_custom_field_def( $post_type, $id );
			global $wpdb;
			$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'postmeta where meta_key = %s', $custom_field_id ) );?>
			<div id="message" class="updated"><p>
				<?php _e( 'Custom field deleted', 'tcp' );?>
			</p></div><?php
		}
	}
}
?>
<div class="wrap">

<h2><?php _e( 'Custom Fields', 'tcp' );?></h2>
<div class="clear"></div>

<p>
<form method="post">
<label><?php _e( 'Post type', 'tcp');?></label>: 
<select name="post_type" id="post_type">
<?php foreach( get_post_types() as $type ) :
	if ( $type != 'tcp_product_option' ) :?>
	<option value="<?php echo $type;?>"<?php selected( $post_type, $type ); ?>><?php echo $type;?></option>
	<?php endif;
endforeach;?>
</select>
<input type="submit" id="tcp_filter" name="tcp_filter" value="<?php _e( 'filter', 'tcp' );?>" class="button-secondary"/>
</form>
</p>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Key', 'tcp' );?> (<?php _e( 'to get the custom field value', 'tcp' );?>)</th>
	<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Type', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Key', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Type', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>
<tr>
	<td colspan="5">
	<label for="label"><?php _e( 'Add new field definition', 'tcp' );?></label>:
		<form method="post">
			<input type="hidden" name="post_type" value="<?php echo $post_type;?>"/>
			<label for="label"><?php _e( 'Label', 'tcp' );?>: </label><input type="text" name="label" id="label" size="20" />
			<label for="type"><?php _e( 'Type', 'tcp' );?>: </label>
			<select id="type" name="type">
				<option value="string"><?php _e( 'Text', 'tcp' );?></option>
				<option value="number"><?php _e( 'Number', 'tcp' );?></option>
				<option value="list"><?php _e( 'list', 'tcp' );?></option>
			</select>
			<label for="values"><?php _e( 'Possible values', 'tcp' );?></label>: <input type="text" id="values" name="values" size="40"/><span><?php _e( 'For fields of type \'List\', enter a list of possible values separated by comma', 'tcp' );?></span>
			<br/><label for="desc"><?php _e( 'Description', 'tcp' );?></label>: <input type="text" id="desc" name="desc" size="40"/>
			<p><input type="submit" name="tcp_save_custom_field" value="<?php _e( 'Save' , 'tcp' );?>" class="button-secondary" /></p>
		</form>
	</td>
</tr>
<?php
$custom_fields = tcp_get_custom_fields_def( $post_type );
if ( count( $custom_fields ) == 0 ) : ?>
	<tr><td colspan="5"><?php printf( __( 'The list of Custom fields of %s is empty', 'tcp' ), $post_type );?></td></tr>
<?php else :
	foreach( $custom_fields as $id => $field ) :?>
	<tr>
		<td><?php echo $field['id'];?></td>
		<td><?php echo $field['label'];?></td>
		<td><?php echo $field['type'];?></td>
		<td><?php echo $field['desc'];?></td>
		<td style="width: 20%;">
		<a href="#" onclick="jQuery('.modify_custom_field').hide();jQuery('#modify_<?php echo $id;?>').show();"><?php _e( 'edit', 'tcp' );?></a> |
		<a href="#" onclick="jQuery('.delete_custom_field').hide();jQuery('#delete_<?php echo $id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a></div>
		<div id="delete_<?php echo $id;?>" class="delete_custom_field" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post">
				<input type="hidden" name="post_type" value="<?php echo $post_type;?>"/>
				<input type="hidden" name="custom_field_id" value="<?php echo $id;?>" />
				<p><?php _e( 'Do you really want to delete this custom field?', 'tcp' );?></p>
				<input type="submit" name="tcp_delete_custom_field" value="<?php _e( 'Yes' , 'tcp' );?>" class="button-secondary" /> |
				<a href="#" onclick="jQuery('#delete_<?php echo $id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
			</form>
		</div>
		</td>
	</tr>
	<tr id="modify_<?php echo $id;?>" class="modify_custom_field" style="display: none;">
		<td colspan="4">
			<form method="post">
				<input type="hidden" name="post_type" value="<?php echo $post_type;?>"/>
				<input type="hidden" name="custom_field_id" value="<?php echo $id;?>" />
				<input type="hidden" name="internal_id" value="<?php echo $field['id'];?>" />
				<label for="label_<?php echo $id;?>"><?php _e( 'Label', 'tcp' );?></label>:<input type="text" id="label_<?php echo $id;?>" name="label" value="<?php echo $field['label'];?>" size="20" />
				<label for="name_<?php echo $id;?>"><?php _e( 'Type', 'tcp' );?></label>: <select id="name_<?php echo $id;?>" name="type">
					<option value="string" <?php checked( $field['type'], 'string' );?>><?php _e( 'Text', 'tcp' );?></option>
					<option value="number" <?php checked( $field['type'], 'number' );?>><?php _e( 'Number', 'tcp' );?></option>
					<option value="list" <?php checked( $field['type'], 'list' );?>><?php _e( 'List', 'tcp' );?></option>
				</select>
				<label for="values_<?php echo $id;?>"><?php _e( 'Description', 'tcp' );?></label>: <input type="text" id="values_<?php echo $id;?>"" name="values" value="<?php echo $field['values'];?>" size="40"/><span><?php _e( 'For fields of type \'List\', enter a list of possible values separated by comma', 'tcp' );?></span>
				<br/><label for="desc_<?php echo $id;?>"><?php _e( 'Description', 'tcp' );?></label>: <input type="text" id="desc_<?php echo $id;?>" name="desc" size="40"/>
				<p>
				<input type="submit" name="tcp_modify_custom_field" value="<?php _e( 'modify' , 'tcp' );?>" class="button-secondary" /> |
				<a href="#" onclick="jQuery('#modify_<?php echo $id;?>').hide();"><?php _e( 'close' , 'tcp' );?></a></p>
			</form>
		</td>
	</tr>
	<?php endforeach;
endif;?>
</tbody>
</table>

</div> <!-- end wrap -->
