<?php
require( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );
$user_name		= $_REQUEST['tcp_new_user_name'];
$user_pass		= $_REQUEST['tcp_new_user_pass'];
$user_pass_2	= $_REQUEST['tcp_repeat_user_pass'];
$redirect_to	= $_REQUEST['tcp_redirect_to'];
$user_email		= $_REQUEST['tcp_new_user_email'];
if ( $user_pass != $user_pass_2 ) {
	$tcp_register_error = __( 'Password incorrect', 'tcp' );
} else {
	$sanitized_user_login = sanitize_user( $user_name );
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( is_wp_error( $user_id ) ) {
		$tcp_register_error = $user_id->get_error_message();
	} else {
		$creds = array();
		$creds['user_login'] = $user_name;
		$creds['user_password'] = $user_pass;
		$creds['remember'] = false;
		$user = wp_signon( $creds, false );
		if ( is_wp_error( $user ) )
			$tcp_register_error = $user->get_error_message();
	}
}
if ( isset( $tcp_register_error ) )
	wp_redirect( add_query_arg( 'tcp_register_error', urlencode( $tcp_register_error ), $redirect_to ) );
else
	wp_redirect( $redirect_to );
?>
