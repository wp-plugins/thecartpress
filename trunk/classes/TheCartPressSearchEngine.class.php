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
 * Allows to comunicate with TheCartPress search engine
 */
class TheCartPressSearchEngine {
	private $HOST = 'http://thecartpress.com/moira/';
	//private $HOST = 'http://localhost/moira/';

	function remove() {
		$url = $this->HOST . 'moira_add.php';
		$url .= '?delete_web=' . get_bloginfo('url');
		return $this->connectAndSend( $url );
	}

	function refresh() {
		$url = $this->HOST . 'moira_add.php';
		$url .= '?refresh_web=' . get_bloginfo('url');
		$guid = $this->generateNewGuid();
		$url .= '&guid=' . $guid;
		return $this->connectAndSend( $url );
	}

	function generateNewGuid() {
		$guid = $this->guid();
		$settings = get_option( 'tcp_settings' );
		$settings['search_engine_guid'] = $guid;
		update_option( 'tcp_settings', $settings );
		return $guid;
	}

	private function connectAndSend( $url ) {
		$handler = curl_init( $url );
		curl_setopt( $handler, CURLOPT_RETURNTRANSFER, 1 );
		$response = curl_exec( $handler );
		curl_close( $handler );
		return $response;
	}

	private function guid() {
		if ( function_exists( 'com_create_guid' ) )
		    return com_create_guid();
		else {
		    mt_srand( (double)microtime() * 10000 ); //optional for php 4.2.0 and up.
		    $char_id = strtoupper( md5( uniqid( rand(), true ) ) );
		    $hyphen = chr( 45 );// "-"
		    $uuid = chr( 123 )// "{"
		            .substr( $char_id,  0,  8 ) . $hyphen
		            .substr( $char_id,  8,  4 ) . $hyphen
		            .substr( $char_id, 12,  4 ) . $hyphen
		            .substr( $char_id, 16,  4 ) . $hyphen
		            .substr( $char_id, 20, 12 )
		            .chr( 125 );// "}"
		    return $uuid;
		}
	}
}
?>