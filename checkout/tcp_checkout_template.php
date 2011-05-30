<?php

$tcp_checkout_boxes = array();

function tcp_register_checkout_box( $class_name ) {
	global $tcp_checkout_boxes;
	$tcp_checkout_boxes[$class_name] = $class_name;
}

function tcp_remove_checkout_box( $class_name ) {
	global $tcp_checkout_boxes;
	unset( $tcp_checkout_boxes[$class_name] );
}

?>
