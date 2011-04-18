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

class ShoppingCartSummaryWidget extends WP_Widget {
	function ShoppingCartSummaryWidget() {
		$widget = array(
			'classname'		=> 'shoppingcartsummary',
			'description'	=> __( 'Use this widget to add a Shopping cart summary', 'tcp' ),
		);
		$control = array(
			'width'		=> 300,
			'id_base'	=> 'shoppingcartsummary-widget',
		);
		$this->WP_Widget( 'shoppingcartsummary-widget', 'TCP Shopping Cart Summary', $widget, $control );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : ' ' );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		tcp_get_shopping_cart_summary( $instance );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']				= strip_tags( $new_instance['title'] );
		$instance['see_stock_notice']	= isset( $new_instance['see_stock_notice'] );
		$instance['see_product_count']	= isset( $new_instance['see_product_count'] );
		$instance['see_weight']			= isset( $new_instance['see_weight'] );
		$instance['see_delete_all']		= isset( $new_instance['see_delete_all'] );
		$instance['see_shopping_cart']	= isset( $new_instance['see_shopping_cart'] );
		$instance['see_checkout']		= isset( $new_instance['see_checkout'] );
		$instance = apply_filters( 'tcp_shopping_cart_summary_widget_update', $instance, $new_instance );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'				=> __( 'Shopping cart', 'tcp' ),
			'see_weight'		=> true,
			'see_delete_all'	=> true,
		);
		$see_stock_notice	= isset( $instance['see_stock_notice'] ) ? (bool)$instance['see_stock_notice'] : false;
		$see_product_count	= isset( $instance['see_product_count'] ) ? (bool)$instance['see_product_count'] : false;
		$see_weight			= isset( $instance['see_weight'] ) ? (bool)$instance['see_weight'] : false;
		$see_delete_all		= isset( $instance['see_delete_all'] ) ? (bool)$instance['see_delete_all'] : false;
		$see_shopping_cart	= isset( $instance['see_shopping_cart'] ) ? (bool)$instance['see_shopping_cart'] : false;
		$see_checkout		= isset( $instance['see_checkout'] ) ? (bool)$instance['see_checkout'] : false;
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_product_count ' ); ?>" name="<?php echo $this->get_field_name( 'see_product_count' ); ?>"<?php checked( $see_product_count ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_product_count' ); ?>"><?php _e( 'See product count', 'tcp ' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_stock_notice ' ); ?>" name="<?php echo $this->get_field_name( 'see_stock_notice' ); ?>"<?php checked( $see_stock_notice ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_stock_notice' ); ?>"><?php _e( 'See stock notice', 'tcp ' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_weight ' ); ?>" name="<?php echo $this->get_field_name( 'see_weight' ); ?>"<?php checked( $see_weight ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_weight' ); ?>"><?php _e( 'See weigth', 'tcp ' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_delete_all ' ); ?>" name="<?php echo $this->get_field_name( 'see_delete_all' ); ?>"<?php checked( $see_delete_all ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_delete_all' ); ?>"><?php _e( 'See delete button', 'tcp ' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_shopping_cart ' ); ?>" name="<?php echo $this->get_field_name( 'see_shopping_cart' ); ?>"<?php checked( $see_shopping_cart ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_shopping_cart' ); ?>"><?php _e( 'See shopping cart link', 'tcp ' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_checkout ' ); ?>" name="<?php echo $this->get_field_name( 'see_checkout' ); ?>"<?php checked( $see_checkout ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_checkout' ); ?>"><?php _e( 'See checkout link', 'tcp ' ); ?></label>
		<?php do_action( 'tcp_shopping_cart_summary_widget_form', $this, $instance ); ?>
		</p><?php
	}
}
?>
