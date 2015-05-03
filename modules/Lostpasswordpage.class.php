<?php
/**
 * Lost Password Page
 *
 * Adds a Lost Passpord page
 *
 * @package TheCartPress
 * @subpackage Modules
 */

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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPLostPasswordPage' ) ) :

/**
 * Adds a lost Password page
 * If the setting "use_thecartpress_reset_password_page" is activated,
 * WordPress will use this custom page.
 *
 * @since 1.3.9.1
 */
class TCPLostPasswordPage {

	static function tcp_init( $thecartpress ) {
		if ( $thecartpress->get_setting( 'use_thecartpress_reset_password_page', true ) ) {
			add_filter( 'lostpassword_url', array( 'TCPLostPasswordPage', 'lostpassword_url' ), 10, 2 );
		}
	}

	/**
	 * Uses TheCartPress lost password url (My account page).
	 * This property is activated if the setting 'use_thecartpress_reset_password_page' is activated
	 *
	 * @since 1.3.9.1
	 * @param $lostpassword_url, current url
	 * @param $redirect, redirect url
	 */
	static function lostpassword_url( $lostpassword_url, $redirect ) {
		$url = tcp_get_the_my_account_url();
		$url = add_query_arg( 'action', 'lostpassword', $url );
		$url = add_query_arg( 'redirect_to', $redirect, $url );
		return $url;
	}
}

add_action( 'tcp_init', array( 'TCPLostPasswordPage', 'tcp_init' ) );

endif; // class_exists check