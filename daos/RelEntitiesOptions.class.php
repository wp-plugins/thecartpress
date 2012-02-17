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

require_once( TCP_DAOS_FOLDER . '/RelEntities.class.php' );

/**
 * 
 * This clas adds method to RelEntities class
 * @author sensei
 * 
 */
class RelEntitiesOptions extends RelEntities {

	static function getOptionsTree( $id_from ) {
		global $wpdb;
		$options = array();
		$options_1 = $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_rel_entities 
			where id_from = %d and rel_type = %s order by list_order', $id_from, 'OPTIONS' ) );
		if ( is_array( $options_1 ) && count( $options_1 ) )
			foreach( $options_1 as $option_1 ) {
				$options_2 = $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_rel_entities 
					where id_from = %d and rel_type = %s order by list_order', $option_1->id_to, 'OPTIONS' ) );
				if ( is_array( $options_2 ) && count( $options_2 ) ) {
					$options[$option_1->id_to] = array();
					foreach( $options_2 as $option_2 ) {
						$options[$option_1->id_to][$option_2->id_to] = $option_2->id_to;
					}
				} else {
					$options[$option_1->id_to] = $option_1->id_to;
				}
			}
		return $options;
	}
}
?>
