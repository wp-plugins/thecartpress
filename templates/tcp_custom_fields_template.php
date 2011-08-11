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
?>
