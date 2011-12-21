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

/**
 * Returns the custom taxonomies
 * @since 1.1.6
 */
function tcp_get_custom_taxonomies( $post_type = '' ) {
	$taxonomies = get_option( 'tcp-taxonomies-generator', array() );
	if ( $post_type == '' ) {
		return $taxonomies;
	} else {
		$result = array();
		foreach( $taxonomies as $taxonomy ) {
			if ( $taxonomy['post_type'] == $post_type) {
				$result[] = $taxonomy;
			}
		}
		return $result;
	}
}

/**
 * Returns a custom taxonomy by id
 * @since 1.1.6
 */
function tcp_get_custom_taxonomy( $taxonomy_id ) {
	$taxonomies = tcp_get_custom_taxonomies();
	return isset( $taxonomies[$taxonomy_id] ) ? $taxonomies[$taxonomy_id] : false;
}
?>
