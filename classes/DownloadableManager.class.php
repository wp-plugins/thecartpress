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
require_once( dirname( __FILE__ ) . '/IDownloadableManager.interface.php' );

class TCPDownloadableManager implements IDownloadableManager {

	public function getFiles( $post_id ) {
		return array(
			array( 'file_name' => 'file_A.png', 'name' => 'file A', 'ext' => 'png', 'size' => '100 kb', 'versions' => array( '0.0', '0.1', '0.2' ) ),
			array( 'file_name' => 'file_B.pdf', 'name' => 'file B', 'ext' => 'pdf', 'size' => '1.5 Mb', 'versions' => array( '1.0', '1.1', '2.0' ) ),
		);
	}

	public static function tcp_get_folder( $post_id, $create_if_not_exists = true ) {
		$path = tcp_get_downloadable_path() . 'tcp_downloadable_' . $post_id;
		if ( $path && $create_if_not_exists ) mkdir( $path, 077, true );
		return $path;
	}

	function tcp_get_downloadable_path() {
		global $thecartpress;
		return isset( $thecartpress->settings['downloadable_path'] ) ? trim( $thecartpress->settings['downloadable_path'] ) : false;
	}
}
?>
