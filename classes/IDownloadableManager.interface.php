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

interface IDownloadableManager {

	/**
	 * array(
	 *		array( 'file_name' => 'file_A.png', 'name' => 'file A', 'ext' => 'png', 'size' => '100 kb', 'versions' => array( '0.0', '0.1', '0.2' ) ),
	 *		array( 'file_name' => 'file_B.pdf', 'name' => 'file B', 'ext' => 'pdf', 'size' => '1.5 Mb', 'versions' => array( '1.0', '1.1', '2.0' ) ),
	 *	);
	 */
	public function getFiles( $post_id );

//	public function uploadFile( $post_id, $file_name );

//	public function deleteFiles( $post_id );

//	public function deleteFile( $post_id, $file_id, $version = '' );

}
