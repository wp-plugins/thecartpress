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
		$this->WP_Widget( 'shoppingcart-widget', 'TCP  shopping cart', $widget, $control );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$settings = get_option( 'tcp_settings' );
		$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';
		$stock_management = isset( $settings['stock_management'] ) ? $settings['stock_management'] : false;
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		echo '<ul class="tcp_shopping_cart">';
		$shoppingCart = TheCartPress::getShoppingCart();
		$see_stock_notice	= isset( $instance['see_stock_notice'] ) ? $instance['see_stock_notice'] : false;
		$see_modify_item	= isset( $instance['see_modify_item'] ) ? $instance['see_modify_item'] : true;
		$see_weight			= isset( $instance['see_weight'] ) ? $instance['see_weight'] : true;
		$see_delete_item	= isset( $instance['see_delete_item'] ) ? $instance['see_delete_item'] : true;
		$see_shopping_cart	= isset( $instance['see_shopping_cart'] ) ? $instance['see_shopping_cart'] : true;
		$see_checkout		= isset( $instance['see_checkout'] ) ? $instance['see_checkout'] : true;
		$see_delete_all		= isset( $instance['see_delete_all'] ) ? $instance['see_delete_all'] : true;
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
					<li><span class="tcp_unit_price"><?php _e( 'price', 'tcp' );?>:&nbsp;<?php echo number_format( $item->getUnitPrice(), 2 );?>&nbsp;<?php echo $currency;?></span><span class="tcp_tax_label">(<?php tcp_the_tax_label();?>)</span></li>
					<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
					<li>
						<?php if ( $see_modify_item ) :?>
							<input type="text" name="tcp_count" id="tcp_count" value="<?php echo $item->getCount();?>" size="2" maxlength="4" />
							<input type="submit" name="tcp_modify_item_shopping_cart" value="<?php _e( 'Modify', 'tcp' );?>"/>
						<?php else :?>
							<span class="tcp_units"><?php _e( 'Units', 'tcp' );?>:&nbsp;<?php echo $item->getCount();?></span>
						<?php endif;?>
						<?php if ( $stock_management && $see_stock_notice ) :
							$stock = tcp_get_the_stock( $item->getPostId() );
							if ( $stock != -1 && $stock < $item->getCount() ) :?>
								<span class="tcp_no_stock_enough"><?php printf( __( 'No enough stock for this product. Only %s items available.', 'tcp' ), $stock );?></span>
							<?php endif;
						endif?>
					</li>
					<?php endif;?>
					<li><span class="tcp_subtotal"><?php _e( 'Total', 'tcp' );?>:&nbsp;<?php echo number_format( $item->getTotal(), 2 );?>&nbsp;<?php echo $currency;?></li>
				<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
					<?php if ( $see_weight ) :?>
						<li><span class="tcp_weight"><?php _e( 'Weight', 'tcp' );?>:</span>&nbsp;<?php echo $item->getWeight();?>&nbsp;</li>
					<?php endif;?>
				<?php endif;?>
				<?php do_action( 'tcp_shopping_cart_widget_item', $item );?>
				<?php if ( $see_delete_item ) :?>
					<li><input type="submit" name="tcp_delete_item_shopping_cart" value="<?php _e( 'Delete item', 'tcp' );?>"/></li>
				<?php endif;?>
				</ul>
			</form></li>
		<?php endforeach;?>
		<?php if ( $see_shopping_cart ) :?>
			<li><a href="<?echo get_permalink( get_option( 'tcp_shopping_cart_page_id' ) );?>"><?php _e( 'shopping cart', 'tcp' );?></a></li>
		<?php endif;?>
		<?php if ( $see_checkout ) :?>
			<li><a href="<?echo get_permalink( get_option( 'tcp_checkout_page_id' ) );?>"><?php _e( 'checkout', 'tcp' );?></a></li>
		<?php endif;?>
		<?php if ( $see_delete_all ) :?>
			<li><form method="post"><input type="submit" name="tcp_delete_shopping_cart" value="<?php _e( 'delete shopping cart', 'tcp' );?>"/></form></li>
		<?php endif;?>
		</ul>
		<?php echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']				= strip_tags( $new_instance['title'] );
		$instance['see_stock_notice']	= $new_instance['see_stock_notice'];
		$instance['see_weight']			= $new_instance['see_weight'];
		$instance['see_modify_item']	= $new_instance['see_modify_item'];
		$instance['see_delete_item']	= $new_instance['see_delete_item'];
		$instance['see_delete_all']		= $new_instance['see_delete_all'];
		$instance['see_shopping_cart']	= $new_instance['see_shopping_cart'];
		$instance['see_checkout']		= $new_instance['see_checkout'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'				=> 'Shopping Cart',
			'see_weight'		=> true,
			'see_modify_item'	=> true,
			'see_delete_item'	=> true,
			'see_delete_all'	=> true,
			'see_stock_notice'	=> false,
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$see_stock_notice	= isset( $instance['see_stock_notice'] ) ? (bool)$instance['see_stock_notice'] : false;
		$see_weight			= isset( $instance['see_weight'] )		? (bool) $instance['see_weight']		: false;
		$see_modify_item	= isset( $instance['see_modify_item'] )	? (bool) $instance['see_modify_item']	: false;
		$see_delete_item	= isset( $instance['see_delete_item'] )	? (bool) $instance['see_delete_item']	: false;
		$see_delete_all		= isset( $instance['see_delete_all'] )	? (bool) $instance['see_delete_all']	: false;
		$see_shopping_cart	= isset( $instance['see_shopping_cart'] ) ? (bool)$instance['see_shopping_cart'] : false;
		$see_checkout		= isset( $instance['see_checkout'] ) ? (bool)$instance['see_checkout'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_stock_notice'); ?>" name="<?php echo $this->get_field_name( 'see_stock_notice' ); ?>"<?php checked( $see_stock_notice ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_stock_notice' ); ?>"><?php _e('See stock notices', 'tcp'); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_weight' ); ?>" name="<?php echo $this->get_field_name( 'see_weight' ); ?>"<?php checked( $see_weight ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_weight' ); ?>"><?php _e( 'See weigth', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_modify_item' ); ?>" name="<?php echo $this->get_field_name( 'see_modify_item' ); ?>"<?php checked( $see_modify_item ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_modify_item' ); ?>"><?php _e( 'See modify button', 'tcp' ); ?></label>			
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_delete_item'); ?>" name="<?php echo $this->get_field_name( 'see_delete_item' ); ?>"<?php checked( $see_delete_item ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_delete_item' ); ?>"><?php _e( 'See delete button', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_delete_all'); ?>" name="<?php echo $this->get_field_name( 'see_delete_all' ); ?>"<?php checked( $see_delete_all ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_delete_all' ); ?>"><?php _e( 'See delete all button', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_shopping_cart'); ?>" name="<?php echo $this->get_field_name( 'see_shopping_cart' ); ?>"<?php checked( $see_shopping_cart ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_shopping_cart' ); ?>"><?php _e('See shopping cart link', 'tcp'); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('see_checkout'); ?>" name="<?php echo $this->get_field_name( 'see_checkout' ); ?>"<?php checked( $see_checkout ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_checkout' ); ?>"><?php _e('See checkout link', 'tcp'); ?></label>
		</p>
		<?php
	}

	private function getProductTitle( $post_id, $option_1_id, $option_2_id ) {
		$title = get_the_title( $post_id );
		if ( $option_1_id > 0 ) $title .= '-' . get_the_title( $option_1_id );
		if ( $option_2_id > 0 ) $title .= '-' . get_the_title( $option_2_id );
		if ( ! tcp_is_visible( $post_id ) )
			$post_id = tcp_get_the_parent( $post_id );
		if ( $post_id > 0 )
			$title = '<a href="' . get_permalink( $post_id ) . '" title="' . $title . '">' . $title . '</a>';
		return $title;
	}
}
?>
