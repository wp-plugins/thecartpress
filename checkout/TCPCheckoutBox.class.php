<?php

/**
 * Parent class for all boxes for the checkout
 */
class TCPCheckoutBox {
	private $config_settings = array();

	function __construct( $config_settings ) {
		$this->config_settings = $config_settings;
	}

	function get_title() {
	}

	function get_class() {
		return '';
	}

	function show_config_settings() {
	}

	function save_config_settings() {
	}

	function delete_config_settings() {
	}

	/**
	 * Returns true if the box needs a form tag encapsulating it
	 */
	function is_form_encapsulated() {
		return true;
	}

	/**
	 *@return possible values: -1 jump to the step - 1, 0 -> No jump, 1 jump to step + 1
	 */
	function before_action() {
		return 0;
	}

	function after_action() {
		return true;
	}

	function show() {
		return true;
	}
}
?>
