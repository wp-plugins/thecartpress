<?php
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );

if ( isset( $_REQUEST['order_id'] ) ) {
	$order_id			= (int)$_REQUEST['order_id'];
	$card_number_1		= isset( $_REQUEST['card_number_1'] ) ? $_REQUEST['card_number_1'] : '';
	$card_number_2		= isset( $_REQUEST['card_number_2'] ) ? $_REQUEST['card_number_2'] : '';
	$card_number_3		= isset( $_REQUEST['card_number_3'] ) ? $_REQUEST['card_number_3'] : '';
	$card_number_4		= isset( $_REQUEST['card_number_4'] ) ? $_REQUEST['card_number_4'] : '';
	$card_number		= $card_number_1 . $card_number_2 . $card_number_3 . $card_number_4;
	$cvc				= isset( $_REQUEST['cvc'] ) ? $_REQUEST['cvc'] : '';
	$expiration_month	= isset( $_REQUEST['expiration_month'] ) ? $_REQUEST['expiration_month'] : '';
	$expiration_year	= isset( $_REQUEST['expiration_year'] ) ? $_REQUEST['expiration_year'] : '';
	$card_type			= isset( $_REQUEST['card_type'] ) ? $_REQUEST['card_type'] : '';
	$card_holder		= isset( $_REQUEST['card_holder'] ) ? $_REQUEST['card_holder'] : '';
	$new_status			= isset( $_REQUEST['new_status'] ) ? $_REQUEST['new_status'] : '';
	$created_at			= date( 'Y-m-d' );
	$url				= isset( $_REQUEST['return_url'] ) ? $_REQUEST['return_url'] : '';
	if ( CCValidator::validateCC( $card_number ) ) {
		$wordpress_path = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/';
		include_once( $wordpress_path.'wp-config.php' );
		include_once( $wordpress_path.'wp-includes/wp-db.php' );
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_offlines',
			array(
				'order_id'				=> $order_id,
				'card_holder'			=> $card_holder,
				'card_number'			=> $card_number,
				'cvc'					=> $cvc,
				'expiration_month'		=> $expiration_month,
				'expiration_year'		=> $expiration_year,
				'card_type'				=> $card_type,
				'created_at'			=> $created_at,
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'	=> $new_status,
			),
			array(
				'order_id'	=> $order_id,
			), 
			array( '%s' ), array( '%d' ) );
		header( 'Location: ' . $url );
		exit;
		die( __( 'OK data card', 'tcp' ) );
	} else {
		die( __( 'Wrong data card', 'tcp' ) );
	}
}

class CCValidator {
	static function validateCC($ccnum, $type = 'unknown') {
		//Clean up input
		$type = strtolower( $type );
		$ccnum = preg_replace( '/[-[:space:]]/', '', $ccnum );
		//Do type specific checks
		if ( $type == 'unknown' ) {
			//Skip type specific checks
		} elseif ( $type == 'mastercard'){
			if ( strlen($ccnum) != 16 || !ereg( '5[1-5]', $ccnum ) ) return 0;
		} elseif ( $type == 'visa'){
			if ( ( strlen($ccnum) != 13 && strlen( $ccnum ) != 16 ) || substr ($ccnum, 0, 1) != '4')
				return 0;
		} elseif ( $type == 'amex' ) {
			if ( strlen( $ccnum ) != 15 || !ereg( '3[47]', $ccnum ) )
				return 0;
		} elseif ($type == 'discover'){ 
			if (strlen($ccnum) != 16 || substr($ccnum, 0, 4) != '6011') 
			return 0; 
		} else { 
		    //invalid type entered 
		    return -1; 
		} 
		// Start MOD 10 checks 
		$dig = CCValidator::toCharArray($ccnum); 
		$numdig = sizeof ($dig);
		$j = 0;
		for ( $i=( $numdig - 2 ); $i >= 0; $i-=2 ) {
		    $dbl[$j] = $dig[$i] * 2;
		    $j++;
		}
		$dblsz = sizeof( $dbl );
		$validate = 0;
		for ( $i = 0; $i < $dblsz; $i++){
		    $add = CCValidator::toCharArray( $dbl[$i] );
		    for ($j = 0; $j < sizeof( $add ); $j++ ){
		        $validate += $add[$j];
		    }
		$add = '';
		}
		for ( $i = ( $numdig - 1 ); $i >= 0; $i -= 2 ) {
		    $validate += $dig[$i];
		}
		if ( substr( $validate, -1, 1 ) == '0' ) return 1;
		else return 0;
	}

	// takes a string and returns an array of characters
	static function toCharArray( $input ){
		$len = strlen( $input );
		for ($j = 0; $j < $len; $j++ ) {
		    $char[$j] = substr( $input, $j, 1 );
		} 
		return ( $char );
	}
}
?>
