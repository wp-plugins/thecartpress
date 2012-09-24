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

class TCPCustomFields {

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( new CustomFieldsMetabox(), 'registerMetaBox' ), 99 );
		}
	}

	function admin_menu() {
		global $thecartpress;
		$base = $thecartpress->get_base_tools();
		add_submenu_page( $base, __( 'Custom fields', 'tcp' ), __( 'Custom fields', 'tcp' ), 'tcp_edit_products', TCP_ADMIN_FOLDER . 'CustomFieldsList.php' );
	}
}

new TCPCustomFields();

class CustomFieldsMetabox {

	function registerMetaBox() {
		$post_types = get_post_types();
		foreach( $post_types as $post_type )
			add_meta_box( 'tcp-custom-fields', __( 'Custom fields', 'tcp' ), array( $this, 'show' ), $post_type, 'normal', 'high' );
		add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'delete_post' ) );
	}

	function show() { 
		global $post; ?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp_custom_noncename', 'tcp_custom_noncename' );?>
			<table class="form-table">
			<tbody>
			<?php tcp_edit_custom_fields( $post->ID, $post->post_type ); ?>
			</tbody>
			</table>
		</div><?php
	}

	function save_post( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_custom_noncename'] ) ? $_POST['tcp_custom_noncename'] : '', 'tcp_custom_noncename' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		tcp_save_custom_fields( $post_id, $post->post_type );
		return array( $post_id, $post );
	}

	function delete_post( $post_id ) {
		if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
		tcp_delete_custom_fields( $post_id );
		return $post_id;
	}
}

/**
 * initial array ( 'post_type' => array( array( id, label, type, values, order), ... ), ... )
 */
function tcp_get_custom_fields_def( $post_type ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	return isset( $custom_fields[$post_type] ) ? $custom_fields[$post_type] : array();
}

function tcp_get_custom_fields( $post_id, $post_type = false ) {
	if ( ! $post_type ) $post_type = get_post_type( $post_id );
	$defs = tcp_get_custom_fields_def( $post_type );
	$fields = array();
	foreach( $defs as $def ) {
		$value = get_post_meta( $post_id, $def['label'], true );
		if ( $def['type'] == 'list' ) {
			if ( isset( $def['values'][$value] ) ) {
				$values = explode( ',', $def['values'] );
				$value = isset( $values[$value] ) ? $values[$value] : 0;
			}
		} else {
			$value = get_post_meta( $post_id, $def['values'], true );
		}
		$fields[] = array(
			'id'		=> $def['id'],
			'label'		=> $def['label'],//TODO multilingual
			'type'		=> $def['type'],
			'values'	=> $def['values'],
			'desc'		=> $def['desc'],
			'value'		=> $value,
		);
	}
	return $fields;
}

function tcp_add_custom_field_def( $post_type, $id, $label, $type, $values = 0, $desc = '' ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	$custom_fields[$post_type][] = array (
		'id'		=> $id,
		'label'		=> $label,
		'type'		=> $type,
		'values'	=> $values,
		'desc'		=> $desc,
	);
	update_option( 'tcp_custom_fields', $custom_fields );
}

function tcp_delete_custom_field_def( $post_type, $id ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	if ( isset( $custom_fields[$post_type][$id] ) ) unset( $custom_fields[$post_type][$id] );
	update_option( 'tcp_custom_fields', $custom_fields );
}

function tcp_exists_custom_field_def( $post_type, $id ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	if ( isset( $custom_fields[$post_type] ) ) {
		$custom_fields = $custom_fields[$post_type];
		if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 )
		 	foreach( $custom_fields as $field )
				if ( $field['id'] == $id ) return true;
	}
	return false;
}

function tcp_edit_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) : ?>
		<?php foreach( $custom_fields as $custom_field ) :
		$value = get_post_meta( $post_id, $custom_field['id'], true ); ?>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['label']; ?>:</label></th>
			<td>
			<?php if ( $custom_field['type'] == 'list' ) :?>
				<select name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>">
				<?php $poss_values = explode( ',', $custom_field['values'] );
				foreach( $poss_values as $poss_value ) : ?>
					<option value="<?php echo $poss_value; ?>" <?php selected( $value, $poss_value ); ?>><?php echo $poss_value; ?></option>
				<?php endforeach; ?>
				</select>
			<?php else : ?>
				<input name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>" value="<?php echo htmlspecialchars( $value ); ?>" class="regular-text" type="text<?php //echo $custom_field['type'] == 'number' ? 'number' : 'text'; ?>" style="width:20em">
			<?php endif; ?>
			<?php if ( isset( $custom_field['desc'] ) && strlen( $custom_field['desc'] ) > 0 ) : ?>
				<br/><span class="description"><?php echo $custom_field['desc']; ?></span>
			<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	<?php else : ?>
	<tr>
	<th><?php printf( __( 'No custom fields defined. Visit <a href="%s">Custom Fields Manager</a> to create custom fields.', 'tcp' ), add_query_arg( 'page', 'thecartpress/admin/CustomFieldsList.php', get_admin_url() . 'admin.php' ) ); ?></th>
	</tr>
	<?php endif;
}

function tcp_display_custom_fields( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) : 
		$par = true; ?>
		<table>
		<tbody>
		<?php foreach( $custom_fields as $custom_field ) :
			$value = get_post_meta( $post_id, $custom_field['id'], true ); 
			if ( strlen( $value ) > 0 ) : ?>
			<tr valign="top" <?php if ( $par ) echo 'class="tcp_odd"'; $par = !$par; ?>>
				<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['label']; ?>:</label></th>
				<td><?php echo htmlspecialchars( $value ); ?></td>
			</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php endif;
}

function tcp_save_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
		foreach( $custom_fields as $custom_field ) {
			$value = isset( $_POST[$custom_field['id']] ) ? $_POST[$custom_field['id']] : '';
			update_post_meta( $post_id, $custom_field['id'], $value );
		}
	}
}

function tcp_delete_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
		foreach( $custom_fields as $custom_field ) {
			delete_post_meta( $post_id, $custom_field['id'] );
		}
	}
}
?>