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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Shows a Cart table.
 */
class TCPCartTable {

	static function show( $source, $echo = true, $default_template = false ) {
		ob_start();
		if ( $default_template ) {
			$located = TCP_THEMES_TEMPLATES_FOLDER . 'tcp_shopping_cart.php';
		} else {
			$located = locate_template( 'tcp_shopping_cart.php' );
			if ( strlen( $located ) == 0 ) $located = TCP_THEMES_TEMPLATES_FOLDER . 'tcp_shopping_cart.php';
		}
		$located = apply_filters( 'tcp_cart_table_template', $located, $source );
		require( $located );
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}
}
?>
