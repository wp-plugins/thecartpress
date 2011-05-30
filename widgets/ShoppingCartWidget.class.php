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

class ShoppingCartWidget extends WP_Widget {
	function ShoppingCartWidget() {
		$widget = array(
			'classname'		=> 'shoppingcart',
			'description'	=> __( 'Use this widget to add the shopping cart viewer', 'tcp' ),
		);
		$control = array(
			'width'		=> 300,
			'id_base'	=> 'shoppingcart-widget',
		);
		$this->WP_Widget( 'shoppingcart-widget', 'TCP Shopping Cart', $widget, $control );
	}

	function widget( $args, $instance ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		$hide_if_empty = isset( $instance['hide_if_empty'] ) ? $instance['hide_if_empty'] : false;
		if ( $hide_if_empty && $shoppingCart->isEmpty() ) return;
		extract( $args );		
		global $thecartpress;
		$unit_weight			= isset( $thecartpress->settings['unit_weight'] ) ? $thecartpress->settings['unit_weight'] : 'gr';
		$stock_management		= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		echo '<ul class="tcp_shopping_cart">';
		$see_stock_notice	= isset( $instance['see_stock_notice'] ) ? $instance['see_stock_notice'] : false;
		$see_modify_item	= isset( $instance['see_modify_item'] ) ? $instance['see_modify_item'] : true;
		$see_weight			= isset( $instance['see_weight'] ) ? $instance['see_weight'] : true;
		$see_delete_item	= isset( $instance['see_delete_item'] ) ? $instance['see_delete_item'] : true;
		$see_delete_all		= isset( $instance['see_delete_all'] ) ? $instance['see_delete_all'] : true;
		$see_shopping_cart	= isset( $instance['see_shopping_cart'] ) ? $instance['see_shopping_cart'] : true;
		$see_checkout		= isset( $instance['see_checkout'] ) ? $instance['see_checkout'] : true;
		foreach( $shoppingCart->getItems() as $item ) :?>
			<li><form method="post">
				<input type="hidden" name="tcp_post_id" id="tcp_post_id" value="<?php echo $item->getPostId();?>" />
				<input type="hidden" name="tcp_option_1_id" id="tcp_option_1_id" value="<?php echo $item->getOption1Id();?>" />
				<input type="hidden" name="tcp_option_2_id" id="tcp_option_2_id" value="<?php echo $item->getOption2Id();?>" />
				<input type="hidden" name="tcp_unit_price" id="tcp_unit_price" value="<?php echo $item->getUnitPrice();;?>" />
				<input type="hidden" name="tcp_tax" id="tcp_tax" value="<?php echo $item->getTax();?>" />
				<input type="hidden" name="tcp_unit_weight" id="tcp_unit_weight" value="<?php echo $item->getWeight();?>" />
				<ul>
					<li><span class="tcp_name"><?php echo $this->getProductTitle( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );?></span></li>
					<li><span class="tcp_unit_price"><?php _e( 'price', 'tcp' );?>:&nbsp;<?php echo tcp_format_the_price( $item->getUnitPrice() );?></span></li>
					<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
					<li>
						<?php if ( $see_modify_item ) :?>
							<input type="text" name="tcp_count" id="tcp_count" value="<?php echo $item->getCount();?>" size="2" maxlength="4" />
							<input type="submit" name="tcp_modify_item_shopping_cart" value="<?php _e( 'Modify', 'tcp' );?>"/>
						<?php else :?>
							<span class="tcp_units"><?php _e( 'Units', 'tcp' );?>:&nbsp;<?php echo $item->getCount();?></span>
						<?php endif;?>
						<?php if ( $stock_management && $see_stock_notice ) :
							$stock = tcp_get_the_stock( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
							if ( $stock != -1 && $stock < $item->getCount() ) :?>
								<span class="tcp_no_stock_enough"><?php printf( __( 'No enough stock for this product. Only %s items available.', 'tcp' ), $stock );?></span>
							<?php endif;
						endif?>
					</li>
					<?php endif;?>
					<?php if ( $item->getDiscount() > 0 ) : ?>
					<li><span class="tcp_discount"><?php _e( 'Discount', 'tcp' );?>:&nbsp;<?php echo tcp_format_the_price( $item->getDiscount() );?></span></li>
					<?php endif;?>
					<li><span class="tcp_subtotal"><?php _e( 'Total', 'tcp' );?>:&nbsp;<?php echo tcp_format_the_price( $item->getTotal() );?></li>
				<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
					<?php if ( $see_weight && $item->getWeight() > 0 ) :?>
						<li><span class="tcp_weight"><?php _e( 'Weight', 'tcp' );?>:</span>&nbsp;<?php echo tcp_number_format( $item->getWeight() );?>&nbsp;<?php echo $unit_weight;?></li>
					<?php endif;?>
				<?php endif;?>
				<?php do_action( 'tcp_shopping_cart_widget_item', $item );?>
				<?php if ( $see_delete_item ) :?>
					<li><input type="submit" name="tcp_delete_item_shopping_cart" value="<?php _e( 'Delete item', 'tcp' );?>"/></li>
				<?php endif;?>
				<?php do_action( 'tcp_get_shopping_cart_widget_item', $instance, $item );?>
				</ul>
			</form></li>
		<?php endforeach;?>
		<?php if ( $see_shopping_cart ) :?>
			<li><a href="<?php echo get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id' ), 'page' ) );?>"><?php _e( 'shopping cart', 'tcp' );?></a></li>
		<?php endif;?>
		<?php if ( $see_checkout ) :?>
			<li><a href="<?php echo get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );?>"><?php _e( 'checkout', 'tcp' );?></a></li>
		<?php endif;?>
		<?php if ( $see_delete_all ) :?>
			<li><form method="post"><input type="submit" name="tcp_delete_shopping_cart" value="<?php _e( 'delete shopping cart', 'tcp' );?>"/></form></li>
		<?php endif;?>
		<?php do_action( 'tcp_get_shopping_cart_widget', $instance );?>
		</ul>
		<?php echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']				= strip_tags( $new_instance['title'] );
		$instance['hide_if_empty']		= isset( $new_instance['hide_if_empty'] );
		$instance['see_stock_notice']	= isset( $new_instance['see_stock_notice'] ) ? true : false;
		$instance['see_weight']			= isset( $new_instance['see_weight'] ) ? true : false;
		$instance['see_modify_item']	= isset( $new_instance['see_modify_item'] ) ? true : false;
		$instance['see_delete_item']	= isset( $new_instance['see_delete_item'] ) ? true : false;
		$instance['see_delete_all']		= isset( $new_instance['see_delete_all'] ) ? true : false;
		$instance['see_shopping_cart']	= isset( $new_instance['see_shopping_cart'] ) ? true : false;
		$instance['see_checkout']		= isset( $new_instance['see_checkout'] ) ? true : false;
		$instance = apply_filters( 'tcp_shopping_cart_widget_update', $instance, $new_instance );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'				=> __( 'Shopping Cart', 'tcp' ),
			'see_weight'		=> true,
			'see_modify_item'	=> true,
			'see_delete_item'	=> false,
			'see_delete_all'	=> false,
			'see_stock_notice'	=> false,
			'see_shopping_cart'	=> true,
			'see_checkout'		=> true,
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$hide_if_empty		= isset( $instance['hide_if_empty'] ) ? (bool)$instance['hide_if_empty'] : false;
		$see_stock_notice	= isset( $instance['see_stock_notice'] ) ? (bool)$instance['see_stock_notice'] : false;
		$see_weight			= isset( $instance['see_weight'] )	? (bool)$instance['see_weight'] : false;
		$see_modify_item	= isset( $instance['see_modify_item'] ) ? (bool)$instance['see_modify_item'] : false;
		$see_delete_item	= isset( $instance['see_delete_item'] ) ? (bool)$instance['see_delete_item'] : false;
		$see_delete_all		= isset( $instance['see_delete_all'] ) ? (bool)$instance['see_delete_all'] : false;
		$see_shopping_cart	= isset( $instance['see_shopping_cart'] ) ? (bool)$instance['see_shopping_cart'] : false;
		$see_checkout		= isset( $instance['see_checkout'] ) ? (bool)$instance['see_checkout'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hide_if_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_if_empty' ); ?>"<?php checked( $hide_if_empty ); ?> />
			<label for="<?php echo $this->get_field_id( 'hide_if_empty' ); ?>"><?php _e( 'Hide if empty', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_stock_notice' ); ?>" name="<?php echo $this->get_field_name( 'see_stock_notice' ); ?>"<?php checked( $see_stock_notice ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_stock_notice' ); ?>"><?php _e( 'See stock notices', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_weight' ); ?>" name="<?php echo $this->get_field_name( 'see_weight' ); ?>"<?php checked( $see_weight ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_weight' ); ?>"><?php _e( 'See weigth', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_modify_item' ); ?>" name="<?php echo $this->get_field_name( 'see_modify_item' ); ?>"<?php checked( $see_modify_item ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_modify_item' ); ?>"><?php _e( 'See modify button', 'tcp' ); ?></label>			
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_delete_item' ); ?>" name="<?php echo $this->get_field_name( 'see_delete_item' ); ?>"<?php checked( $see_delete_item ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_delete_item' ); ?>"><?php _e( 'See delete button', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_delete_all' ); ?>" name="<?php echo $this->get_field_name( 'see_delete_all' ); ?>"<?php checked( $see_delete_all ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_delete_all' ); ?>"><?php _e( 'See delete all button', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_shopping_cart' ); ?>" name="<?php echo $this->get_field_name( 'see_shopping_cart' ); ?>"<?php checked( $see_shopping_cart ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_shopping_cart' ); ?>"><?php _e( 'See shopping cart link', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_checkout' ); ?>" name="<?php echo $this->get_field_name( 'see_checkout' ); ?>"<?php checked( $see_checkout ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_checkout' ); ?>"><?php _e( 'See checkout link', 'tcp' ); ?></label>
		<?php do_action( 'tcp_shopping_cart_widget_form', $this, $instance ); ?>
		</p>
		<?php
	}

	private function getProductTitle( $post_id, $option_1_id, $option_2_id ) {
		$post_id = tcp_get_current_id( $post_id );
		$title = tcp_get_the_title( $post_id, $option_1_id, $option_2_id );
		if ( ! tcp_is_visible( $post_id ) ) $post_id = tcp_get_the_parent( $post_id );
		$title = '<a href="' . get_permalink( $post_id ) . '">' . $title . '</a>';
		return $title;
	}
}
?>
