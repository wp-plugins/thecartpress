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

class TaxonomyCloudsPostTypeWidget extends WP_Widget {
	function TaxonomyCloudsPostTypeWidget() {
		$widget = array(
			'classname'		=> 'taxonomycloudsposttype',
			'description'	=> __( 'Use this widget to add a taxonomy cloud for post types', 'tcp' ),
		);
		$control = array(
			'width'		=> 300,
			'id_base'	=> 'taxonomycloudsposttype-widget',
		);
		$this->WP_Widget( 'taxonomycloudsposttype-widget', 'TCP Taxonomy clouds', $widget, $control );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )	echo $before_title, $title, $after_title;
		$args = array(
			'smallest'  => $instance['min_size'],
			'largest'   => $instance['max_size'],
			'unit'      => $instance['size_units'],
			'number'    => $instance['number_tags'],
			'format'    => $instance['display_format'],
			'separator' => $instance['separator'],
			'orderby'   => 'name',
			'order'     => 'ASC',
			'link'      => 'view',
			'taxonomy'  => $instance['taxonomy'], //'post_tag',
			'echo'      => false,
		);
		if ( !$instance['separator'] ) $args['separator'] = "\n";
		if ( $instance['display_format'] == 'flat' )
			echo '<div>', wp_tag_cloud( $args ), '</div>';
		else
			echo wp_tag_cloud( $args );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']			= strip_tags( $new_instance['title'] );
		$instance['min_size']		= (int)$new_instance['min_size'];
		$instance['max_size']		= (int)$new_instance['max_size'];
		$instance['size_units']		= $new_instance['size_units'];
		$instance['number_tags']	= (int)$new_instance['number_tags'];
		$instance['display_format'] = $new_instance['display_format'];
		$instance['separator']		= $new_instance['separator'];
		$instance['post_type']		= $new_instance['post_type'];
		$instance['taxonomy']		= $new_instance['taxonomy'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'			=> 'Taxonomy clouds',
			'min_size'		=> 8,
			'max_size'		=> 22,
			'size_units'	=> 'pt',
			'number_tags'	=> 45,
			'display_format'=> 'flat',
			'separator'		=> "\n",
			'post_type'		=> 'tcp_product',
			'taxonomy'		=> 'tcp_product_tag',
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
			<?php foreach( get_post_types() as $post_type ): ?>
				<option value="<?php echo $post_type;?>"<?php selected( $instance['post_type'], $post_type ); ?>><?php echo $post_type;?></option>
			<?php endforeach; ?>
			</select>
			<span class="description"><? _e( 'Press save to load the next list', 'tcp' );?></span>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
			<?php foreach(get_object_taxonomies( $instance['post_type'] ) as $taxonomy): $tax = get_taxonomy( $taxonomy ); ?>
				<option value="<?php echo esc_attr( $taxonomy );?>"<?php selected( $instance['taxonomy'], $taxonomy ); ?>><?php echo $tax->labels->name;?></option>
			<?php endforeach; ?>
			</select>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'min_size' ); ?>"><?php _e( 'Min size', 'tcp' )?>:</label>
			<input id="<?php echo $this->get_field_id( 'min_size' ); ?>" name="<?php echo $this->get_field_name( 'min_size' ); ?>" type="text" value="<?php echo esc_attr( $instance['min_size'] ); ?>" size="3" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'max_size' ); ?>"><?php _e( 'Max size', 'tcp' )?>:</label>
			<input id="<?php echo $this->get_field_id( 'max_size' ); ?>" name="<?php echo $this->get_field_name( 'max_size' ); ?>" type="text" value="<?php echo esc_attr($instance['max_size']); ?>" size="3" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'size_units' ); ?>"><?php _e( 'Size units', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'size_units' ); ?>" id="<?php echo $this->get_field_id( 'size_units' ); ?>" class="widefat">
				<option value="pt" <?php selected( $instance['size_units'], 'pt' ); ?>><?php _e( 'points (pt)', 'tcp' )?></option>
				<option value="px" <?php selected( $instance['size_units'], 'px' ); ?>><?php _e( 'pixels (px)', 'tcp' )?></option>
				<option value="em" <?php selected( $instance['size_units'], 'em' ); ?>><?php _e( 'relative to current font (em)', 'tcp' )?></option>
				<option value="%"  <?php selected( $instance['size_units'],  '%' ); ?>><?php _e( 'percentage', 'tcp' )?></option>
			</select>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'number_tags' ); ?>"><?php _e( 'Nº of tags', 'tcp' )?>:</label>
			<input id="<?php echo $this->get_field_id( 'number_tags' ); ?>" name="<?php echo $this->get_field_name( 'number_tags' ); ?>" type="text" value="<?php echo esc_attr( $instance['number_tags'] ); ?>" size="3" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'display_format' ); ?>"><?php _e( 'Display format', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'display_format' ); ?>" id="<?php echo $this->get_field_id( 'display_format' ); ?>" class="widefat">
				<option value="flat" <?php selected( $instance['display_format'], 'flat' ); ?>><?php _e( 'tags are separated by whitespace defined by \'separator\' parameter', 'tcp' )?></option>
				<option value="list" <?php selected( $instance['display_format'], 'list' ); ?>><?php _e( 'tags are in UL with a class=\'wp-tag-cloud\'', 'tcp' )?></option>
			</select>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator', 'tcp' )?>:</label>
			<input id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator'); ?>" type="text" value="<?php echo esc_attr($instance['separator']); ?>" size="4" />
		</p>
		<?php
	}
}
?>