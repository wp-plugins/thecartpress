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

class ResumenShoppingCartWidget extends WP_Widget {
	function ResumenShoppingCartWidget() {
		$widget = array(
			'classname'		=> 'resumenshoppingcart',
			'description'	=> __( 'Use this widget to add a resumen of the shopping cart', 'tcp' ),
		);
		$control = array(
			'width'		=> 300,
			'id_base'	=> 'resumenshoppingcart-widget',
		);
		$this->WP_Widget( 'resumenshoppingcart-widget', 'TCP Resumen shopping cart', $widget, $control );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )	echo $before_title, $title, $after_title;
		$currency = tcp_the_currency( false );
		$shoppingCart = TheCartPress::getShoppingCart();?>
		<ul class="tcp_shopping_cart_resume">
			<li><span class="tcp_resumen_subtotal"><?php _e( 'total', 'tcp' );?>:</span>&nbsp;<?php echo number_format( $shoppingCart->getTotal(), 2 );?>&nbsp;<?php echo $currency;?></li>
			<li><span class="tcp_resumen_count"><?php _e( 'nº products:', 'tcp' );?>:</span>&nbsp;<?php echo $shoppingCart->getCount();?></li>
		<?php if ( $instance['see_weight'] ) :?>
			<li><span class="tcp_resumen_weight"><?php _e( 'weigth', 'tcp' );?>:</span>&nbsp;<?php echo $shoppingCart->getWeight();?>&nbsp;<?php echo $currency;?></li>
		<?php endif;?>
		<?php if ( $instance['see_shopping_cart'] ) :?>
			<li><a href="<?echo get_permalink( get_option( 'tcp_shopping_cart_page_id' ) );?>"><?php _e( 'shopping cart', 'tcp' );?></a></li>
		<?php endif;?>
		<?php if ( $instance['see_checkout'] ) :?>
			<li><a href="<?echo get_permalink( get_option( 'tcp_checkout_page_id' ) );?>"><?php _e( 'checkout', 'tcp' );?></a></li>
		<?php endif;?>
		</ul>
		<?php if ($instance['see_delete_all']) :?>
			<form method="post"><input type="submit" name="tcp_delete_shopping_cart" value="<?php _e( 'delete shopping cart', 'tcp' );?>"/></form>
		<?php endif;?>
		<?php echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']				= strip_tags( $new_instance['title'] );
		$instance['see_weight']			= $new_instance['see_weight'];
		$instance['see_delete_all']		= $new_instance['see_delete_all'];
		$instance['see_shopping_cart']	= $new_instance['see_shopping_cart'];
		$instance['see_checkout']		= $new_instance['see_checkout'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'				=> 'Resumen',
			'see_weight'		=> true,
			'see_delete_all'	=> true,
		);
		$see_weight			= isset( $instance['see_weight'] ) ? (bool)$instance['see_weight'] : false;
		$see_delete_all		= isset( $instance['see_delete_all'] ) ? (bool)$instance['see_delete_all'] : false;
		$see_shopping_cart	= isset( $instance['see_shopping_cart'] ) ? (bool)$instance['see_shopping_cart'] : false;
		$see_checkout		= isset( $instance['see_checkout'] ) ? (bool)$instance['see_checkout'] : false;
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_weight'); ?>" name="<?php echo $this->get_field_name( 'see_weight' ); ?>"<?php checked( $see_weight ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_weight' ); ?>"><?php _e('See weigth', 'tcp'); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_delete_all'); ?>" name="<?php echo $this->get_field_name( 'see_delete_all' ); ?>"<?php checked( $see_delete_all ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_delete_all' ); ?>"><?php _e('See delete button', 'tcp'); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_shopping_cart'); ?>" name="<?php echo $this->get_field_name( 'see_shopping_cart' ); ?>"<?php checked( $see_shopping_cart ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_shopping_cart' ); ?>"><?php _e('See shopping cart link', 'tcp'); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_checkout'); ?>" name="<?php echo $this->get_field_name( 'see_checkout' ); ?>"<?php checked( $see_checkout ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_checkout' ); ?>"><?php _e('See checkout link', 'tcp'); ?></label>
		</p>
		<?php
	}
}
?>
