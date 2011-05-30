<?php

require_once( dirname( __FILE__ ) . '/TCPCheckoutBox.class.php' );

class TCPNoticeBox extends TCPCheckoutBox {

	private $errors = array();

	function get_title() {
		return __( 'Checkout notice', 'tcp' );
	}

	function get_class() {
		return 'legal_notice_layer';
	}
	
	function after_action() {
		if ( ! isset( $_REQUEST['legal_notice_accept'] ) || strlen( $_REQUEST['legal_notice_accept'] ) == 0 )
			$this->errors['legal_notice_accept'] = __( 'You must accept the conditions!!', 'tcp' );
		return count( $this->errors ) == 0;
	}

	function show() {
		$legal_notice_accept = isset( $_REQUEST['legal_notice_accept'] ) ? $_REQUEST['legal_notice_accept'] : '';?>
		<div id="legal_notice_layer_info" class="legal_notice_layer_info checkout_info clearfix"><?php
			global $thecartpress;
			$legal_notice = isset( $thecartpress->settings['legal_notice'] ) ? $thecartpress->settings['legal_notice'] : '';
			if ( strlen( $legal_notice ) > 0 ) : ?>
				<label for="legal_notice"><?php _e( 'Notice:', 'tcp' );?></label><br />
				<textarea id="legal_notice" cols="60" rows="8" readonly="true"><?php echo $legal_notice;?></textarea>
				<br />
				<label for="legal_notice_accept"><?php _e( 'Accept conditions:', 'tcp' );?></label>
				<input type="checkbox" id="legal_notice_accept" name="legal_notice_accept" value="Y" />
				<?php if ( isset( $this->errors['legal_notice_accept'] ) ) : ?><br/><span class="error"><?php echo $this->errors['legal_notice_accept'];?></span><?php endif;?>
			<?php else : ?>
				<input type="hidden" name="legal_notice_accept" value="Y" />
				<p><?php _e( 'When you click in the \'create order\' button the order will be created and if you have chosen an external payment method the system will show a button to go to the external web (usually your bank\'s payment gateway)','tcp' );?></p>
			<?php endif;?>
		</div> <!-- legal_notice_layer_info--><?php
		return true;
	}
}
?>
