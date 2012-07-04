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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once( TCP_WIDGETS_FOLDER . 'CustomListWidget.class.php' );

class CrossSellingWidget extends CustomListWidget {

	function CrossSellingWidget() {
		parent::CustomListWidget( 'tcpcrossselleing', __( 'Allow to create a Cross Selling List', 'tcp' ), 'TCP Cross selling' );
	}

	function widget( $args, $instance ) {
//		require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
//		$post_ids = OrdersDetails::getCrossSelling( get_the_ID() );
		$post_ids = tcp_the_cross_selling( get_the_ID() );
		if ( ! is_array( $post_ids ) || count( $post_ids ) == 0 ) return;
		$ids = array();
		$id_to_remove = get_the_ID();
		foreach( $post_ids as $id )
			if ( $id->id != $id_to_remove )
				$ids[] = $id->id;
		if ( count( $ids ) == 0 ) return;
		$loop_args = array(
			'post__in'			=> $ids,
			'post_type'			=> tcp_get_saleable_post_types(), //TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> $instance['limit'],
		);
		parent::widget( $args, $loop_args, $instance );
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Cross Selling', 'tcp' ) );
		parent::show_post_type_form( $instance );
	}
}
?>
