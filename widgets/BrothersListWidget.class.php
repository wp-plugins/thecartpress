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

require_once( dirname( __FILE__) . '/CustomListWidget.class.php' );

class BrothersListWidget extends CustomListWidget {

	function BrothersListWidget() {
		parent::CustomListWidget( 'tcpbrotherslist', __( 'Allow to create brothers list', 'tcp' ), 'TCP Brothers List' );
	}

	function widget( $args, $instance ) {
		if ( ! is_single() ) return;
		global $post;
		if ( $post ) {
			$terms = get_the_terms( 0, 'tcp_product_category' );
			$ids = array();
			foreach( $terms as $term )
				$ids[] = $term->term_id;
			$loop_args = array(
				'post_type'			=> ProductCustomPostType::$PRODUCT,
				'posts_per_page'	=> $instance['limit'],
				'exclude'			=> $post->ID, //TODO
				'tax_query'			=> array(
					array(
						'taxonomy'	=> ProductCustomPostType::$PRODUCT_CATEGORY,
						'terms'		=> $ids,
						'field'		=> 'id',
					),
				),
			);
			parent::widget( $args, $loop_args, $instance );
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return parent::update( $new_instance, $instance );
	}

	function form( $instance ) {
		$defaults = array(
			'title'			=> __( 'Brothers list', 'tcp' ),
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<div id="particular">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		</div><?php
		parent::form( $instance );
	}
}
?>
