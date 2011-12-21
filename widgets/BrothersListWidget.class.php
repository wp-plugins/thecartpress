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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once( TCP_WIDGETS_FOLDER . 'CustomListWidget.class.php' );

/**
 * Shows products of the same category of the displayed current product
 */
class BrothersListWidget extends CustomListWidget {

	function BrothersListWidget() {
		parent::CustomListWidget( 'tcpbrotherslist', __( 'Allow to create brothers lists', 'tcp' ), 'TCP Brothers List' );
	}

	function widget( $args, $instance ) {
		if ( ! is_single() ) return;
		global $post;
		if ( $post ) {
			$post_type = get_post_type_object( $post->post_type );
			if ( count( $post_type->taxonomies ) == 0 ) return;
			$terms = get_the_terms( $post->ID, $post_type->taxonomies[0] );
			$title = '';
			$ids = array();
			if ( is_array( $terms ) && count( $terms ) ) {
				foreach( $terms as $term ) {

					$ids[] = tcp_get_default_id( $term->term_id, $term->taxonomy );
					if ( $title == '' ) $title = $term->name;
					else $title .= ' - ' . $term->name;
				}
			}
			$instance['title'] .= ': ' . $title;
			$loop_args = array(
				'post_type'			=> $post->post_type, //TCP_PRODUCT_POST_TYPE,
				'posts_per_page'	=> $instance['limit'],
				'exclude'			=> array( $post->ID, ),//TODO
				'tax_query'			=> array(
					array(
						'taxonomy'	=> $post_type->taxonomies[0],
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
