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

$tcp_checkout_boxes = array();

function tcp_register_checkout_box( $class_name ) {
	global $tcp_checkout_boxes;
	$tcp_checkout_boxes[$class_name] = $class_name;
}

function tcp_remove_checkout_box( $class_name ) {
	global $tcp_checkout_boxes;
	unset( $tcp_checkout_boxes[$class_name] );
}

?>
