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
 * @since 1.2.6
 */
function tcp_is_user_locked( $user_id ) {
	return (bool)get_user_meta( $user_id, 'tcp_locked', true );
}

/**
 * @since 1.2.6
 */
function tcp_set_user_locked( $user_id, $locked = true ) {
	update_user_meta( $user_id, 'tcp_locked', (bool)$locked );
	tcp_set_user_locked_date( $user_id );
	return (bool)$locked;
}

/**
 * @since 1.2.6
 */
function tcp_get_user_locked_date( $user_id ) {
	return (int)get_user_meta( $user_id, 'tcp_locked_date', true );
}

/**
 * @since 1.2.6
 */
function tcp_set_user_locked_date( $user_id, $time = false ) {
	if ( ! $time ) $time = time();
	update_user_meta( $user_id, 'tcp_locked_date', $time );
	return $time;
}

/**
 * @since 1.2.6
 */
function tcp_delete_user_locked( $user_id ) {
	delete_user_meta( $user_id, 'tcp_locked' );
	tcp_delete_user_locked_date( $user_id );
}

/**
 * @since 1.2.6
 */
function tcp_delete_user_locked_date( $user_id ) {
	delete_user_meta( $user_id, 'tcp_locked_date' );
}
?>