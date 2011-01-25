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

class TaxonomyTreesPostTypeWidget extends WP_Widget {
	function TaxonomyTreesPostTypeWidget() {
		$widget = array(
			'classname'		=> 'taxonomytreesposttype',
			'description'	=> __('Use this widget to add trees of different taxonomis', 'tcp'),
		);
		$control = array(
			'width'		=> 400,
			'id_base'	=> 'taxonomytreesposttype-widget',
		);
		$this->WP_Widget('taxonomytreesposttype-widget', 'TCP Taxonomy trees', $widget, $control);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )	echo $before_title, $title, $after_title;
		$args = array(
			//'show_option_all'    => ,
			//'orderby'            => 'name',
			//'order'              => 'ASC',
			'show_last_update'   => 0,
			'style'              => 'list',
			'show_count'         => ( $instance['see_number_products'] ) ? 1 : 0,
			'hide_empty'         => ( $instance['hide_empty_taxonomies'] ) ? 1 : 0,
			'use_desc_for_title' => 1,
			'child_of'           => 0,
			//'feed'               => ,
			//'feed_type'          => ,
			//'feed_image'         => ,
			//'exclude'            => ,
			//'exclude_tree'       => ,
			//'include'            => ,
			'current_category'   => 0,
			'hierarchical'       => true,
			'title_li'           => '', //$options['txt_title_li'],
			'number'             => NULL,
			'echo'               => 0,
			'depth'              => 0,
			'taxonomy'			 => $instance['taxonomy'],
		);
		$excluded_taxonomies = $instance['excluded_taxonomies'];
		if ( is_array( $excluded_taxonomies ) ) $args['exclude'] = implode( ",", $excluded_taxonomies );
		$included_taxonomies = $instance['included_taxonomies'];
		if ( is_array( $included_taxonomies ) ) $args['include'] = implode( ",", $included_taxonomies );
		$order_included = $instance['order_included'];
		if ( strlen( $order_included ) > 0 ) {
			$this->orderIncluded = explode( '#', $order_included );
			add_filter( 'get_terms', array( $this, 'orderTaxonomies' ) );
		}
		echo '<ul>'.wp_list_categories( $args ).'</ul>';
		if ( strlen( $order_included ) > 0 )
			remove_filter( 'get_terms', array( $this, 'orderTaxonomies' ) );
		echo $after_widget;
	}

	//for order taxonomies list
	function orderTaxonomies( $terms ) { 
		usort( $terms, array( $this, 'compare' ) );
		return $terms;
	}

	//for order taxonomies list
	function compare( $a, $b ) {
		if ( $a == $b ) return 0;
		foreach( $this->orderIncluded as $id )
			if ( $id == $a->term_id ) return -1;
			elseif ( $id == $b->term_id ) return 1;
		return 0;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']					= strip_tags( $new_instance['title'] );
		$instance['post_type']				= $new_instance['post_type'];
		$instance['taxonomy']				= $new_instance['taxonomy'];
		$instance['see_number_products']	= $new_instance['see_number_products'];
		$instance['hide_empty_taxonomies']	= $new_instance['hide_empty_taxonomies'];
		$instance['included_taxonomies']	= $new_instance['included_taxonomies'];
		$instance['order_included']			= $new_instance['order_included'];
		$instance['excluded_taxonomies']	= $new_instance['excluded_taxonomies'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'					=> 'Taxonomy trees post type',
			'post_type'				=> 'tcp_product',
			'taxonomy'				=> 'tcp_product_category',
			'see_number_products'	=> true,
			'hide_empty_taxonomies'	=> true,
			'order_included'		=> '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$see_number_products = isset( $instance['see_number_products'] ) ? (bool) $instance['see_number_products'] : false;
		$hide_empty_taxonomies = isset( $instance['hide_empty_taxonomies'] ) ? (bool) $instance['hide_empty_taxonomies'] : false;
		?>
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
			<span class="description"><? _e( 'press save to load the taxonomies list.', 'tcp' );?></span>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
			<?php foreach(get_object_taxonomies( $instance['post_type'] ) as $taxonomy): $tax = get_taxonomy( $taxonomy ); ?>
				<option value="<?php echo esc_attr( $taxonomy );?>"<?php selected( $instance['taxonomy'], $taxonomy ); ?>><?php echo $tax->labels->name;?></option>
			<?php endforeach; ?>
			</select>
			<span class="description"><? _e( 'Press save to load the next lists', 'tcp' );?></span>
		</p><p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_number_products' ); ?>" name="<?php echo $this->get_field_name( 'see_number_products' ); ?>"<?php checked( $see_number_products ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_number_products' ); ?>"><?php _e( 'See children number', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hide_empty_taxonomies' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty_taxonomies' ); ?>"<?php checked( $hide_empty_taxonomies ); ?> />
			<label for="<?php echo $this->get_field_id( 'hide_empty_taxonomies' ); ?>"><?php _e( 'Hide empty taxonomies', 'tcp' ); ?></label>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'included_taxonomies' ); ?>"><?php _e( 'Included', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'included_taxonomies' ); ?>[]" id="<?= $this->get_field_id( 'included_taxonomies' ); ?>" class="widefat" multiple="true" size="8" style="height: auto">
				<option value="0"<?php $this->selected_multiple( $instance['excluded_taxonomies'], 0 ); ?>><?php _e( 'All', 'tcp' );?></option>
			<?php $args = array (
				'taxonomy'		=> $instance['taxonomy'],
				'hide_empty'	=> false,
			);
			$categories = get_categories( $args );
			$this->orderIncluded = explode( '#', $instance['order_included'] );
			usort( $categories, array( $this, 'compare' ) );
			foreach( $categories as $cat ): ?>
				<option value="<?php echo esc_attr( $cat->term_id );?>"<?php $this->selected_multiple( $instance['included_taxonomies'], $cat->term_id ); ?>><?php echo $cat->cat_name;?></option>
			<?php endforeach; ?>
			</select>
			<input type="button" onclick="tcp_select_up('<?php echo $this->get_field_id( 'included_taxonomies' ); ?>', '<?php echo $this->get_field_id( 'order_included' ); ?>');" id="tcp_up" value="<? _e( 'up', 'tcp' );?>" class="button-secondary"/>
		    <input type="button" onclick="tcp_select_down('<?php echo $this->get_field_id( 'included_taxonomies' ); ?>', '<?php echo $this->get_field_id( 'order_included' ); ?>');" id="tcp_down" value="<? _e( 'down', 'tcp' );?>" class="button-secondary"/>
		    <input type="hidden" id="<?php echo $this->get_field_id( 'order_included' ); ?>" name="<?php echo $this->get_field_name( 'order_included' ); ?>" value="<?php echo $instance['order_included'];?>"/>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'excluded_taxonomies' ); ?>"><?php _e( 'Excluded', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'excluded_taxonomies' ); ?>[]" id="<?php echo $this->get_field_id( 'excluded_taxonomies' ); ?>" class="widefat" multiple="true" size="6" style="height: auto">
				<option value="0"<?php $this->selected_multiple( $instance['excluded_taxonomies'], 0 ); ?>><?php _e('No one', 'tcp');?></option>
			<?php $args = array (
				'taxonomy'		=> $instance['taxonomy'],
				'hide_empty'	=> false,
			);
			foreach( get_categories( $args ) as $cat ): ?>
				<option value="<?php echo esc_attr( $cat->term_id);?>"<?php $this->selected_multiple($instance['excluded_taxonomies'], $cat->term_id ); ?>><?php echo $cat->cat_name;?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	function selected_multiple( $values, $value ) {
		if ( in_array( $value, $values ) )
			echo ' selected';
	}
}
?>
