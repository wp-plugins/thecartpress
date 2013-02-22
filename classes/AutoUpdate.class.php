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

//Code based on https://github.com/omarabid/Self-Hosted-WordPress-Plugin-repository/blob/master/wp_autoupdate.php

class TCPAutoUpdate
{
	/**
	 * The plugin current version
	 * @var string
	 */
	public $current_version;

	/**
	 * The plugin remote update path
	 * @var string
	 */
	public $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	public $slug;

	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 * @param string $current_version
	 * @param string $update_path
	 * @param string $plugin_slug
	 * @change by TCP, the order of the parameters
	 */
	function __construct( $current_version, $plugin, $update_path = 'http://extend.thecartpress.com/xmlrpc.php' ) {
		// Set the class public variables
		$this->current_version = $current_version;
		$this->update_path = $update_path;
		$this->plugin_slug = $plugin;
		list ($t1, $t2) = explode('/', $plugin);
		$this->slug = str_replace('.php', '', $t2);
		// define the alternative API for updating checking
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update') );
		// Define the alternative response for information checking
		add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $transient
	 */
	public function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		// Get the remote version
		$remote_version = $this->getRemote_version();
		// If a newer version is available, add the update
		if ( version_compare( $this->current_version, $remote_version, '<' ) ) {
			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->new_version = $remote_version;
			$obj->url = $this->update_path;
			$obj->package = $this->update_path;
			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 */
	public function check_info($false, $action, $arg)
	{
		if ($arg->slug === $this->slug) {
			$information = $this->getRemote_information();
			return $information;
		}
		return false;
	}

	/**
	 * Return the remote version
	 * @return string $remote_version
	 */
	public function getRemote_version() {
		$request = $this->get_remote_post( 'tcp.getPluginVersion' );
		return $request;

	}

	/**
	 * Get information about the remote version
	 * @return bool|object
	 */
	public function getRemote_information() {
		$request = $this->get_remote_post( 'tcp.getPluginInfo' );
		return unserialize( $request );
	}

	/**
	 * Return the status of the plugin licensing
	 * @return boolean $remote_license
	 */
	public function getRemote_license() {
		$request = $this->get_remote_post( 'tcp.getPluginLicense' );
		return $request;
	}
	
	public function get_remote_post( $method ) {
		if ( function_exists ( 'xmlrpc_encode_request' ) ) {
			$xml = xmlrpc_encode_request( $method, array( 'plugin_slug' => $this->slug ) );
			$curl_hdl = curl_init();
			curl_setopt( $curl_hdl, CURLOPT_URL, $this->update_path );
			curl_setopt( $curl_hdl, CURLOPT_HEADER, 0 ); 
			curl_setopt( $curl_hdl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl_hdl, CURLOPT_POST, true );
			curl_setopt( $curl_hdl, CURLOPT_POSTFIELDS, $xml );
			// Invoke RPC command
			$response = curl_exec( $curl_hdl );
			curl_close( $curl_hdl );
			$result = xmlrpc_decode_request( $response, $method );
			return $result;
		}
	}
}
?>
