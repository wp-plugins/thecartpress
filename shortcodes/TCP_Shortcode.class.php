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

class TCP_Shortcode {

	//@param id: identifier of one shortcode. See ShortcodeGenerator.
	function show( $atts ) {
		extract( shortcode_atts( array( 'id' => '' ), $atts ) );
		$shortcodes_data = get_option( 'tcp_shortcodes_data' );
		foreach( $shortcodes_data as $shortcode_data )
			if ( $shortcode_data['id'] == $id ) {
				$customPostTypeListWidget = new CustomPostTypeListWidget();
				$args = array(
					'before_widget'	=> '<div id="tcp_shortcode_' . $id . '" class="tcp_shortcode tcp_' . $id . '">',
					'after_widget'	=> '</div>',
					'before_title'	=> '',
					'after_title'	=> '',
				);
				ob_start();
				$customPostTypeListWidget->widget( $args, $shortcode_data );
				return ob_get_clean();
			}
		return sprintf( __( 'Mal formed shortcode: %s', 'tcp' ), $id );
	}
}
?>
