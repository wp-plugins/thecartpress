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
 
class CustomPostTypeListWidget extends CustomListWidget {

	function CustomPostTypeListWidget() {
		$widget_settings = array(
			'classname'		=> 'customposttypelist',
			'description'	=> __( 'Allow to create Custom Post Type Lists', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'customposttypelist-widget'
		);
		$this->WP_Widget( 'customposttypelist-widget', 'TCP Custom Post Type List', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $wp_query;
		$paged = isset( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1;

		$loop_args = array(
			'post_type'			=> isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> isset( $instance['limit'] ) ? $instance['limit'] : -1,
		);

		$see_pagination = isset( $instance['pagination'] ) ? $instance['pagination'] : false;
		if ( $see_pagination ) {
			$loop_args['paged'] = $paged;
		}

		if ( isset( $instance['use_taxonomy'] ) && $instance['use_taxonomy'] ) {
			$taxonomy = ( $instance['taxonomy'] == 'category' ) ? 'category_name' : $instance['taxonomy'];
			if ( strlen( $taxonomy ) > 0 ) {
				$loop_args[$taxonomy] = $instance['term'];
			}
		} else {
			if ( isset( $instance['included'] ) && count( $instance['included'] ) > 0 && strlen( $instance['included'][0] ) > 0 ) {
				$loop_args['post__in'] = $instance['included'];
			}
		}
		$loop_args = apply_filters( 'tcp_custom_post_type_list_widget', $loop_args, $instance );
		parent::widget( $args, $loop_args, $instance );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']			= strip_tags( $new_instance['title'] );
		$instance['post_type']		= $new_instance['post_type'];
		$instance['use_taxonomy']	= $new_instance['use_taxonomy'] == 'yes';
		$instance['taxonomy']		= $new_instance['taxonomy'];
		$instance['term']			= $new_instance['term'];
		$instance['related_type']	= $new_instance['related_type'];
		$instance['included']		= $new_instance['included'];
		$instance['limit']			= (int)$new_instance['limit'];
		$instance['loop']			= $new_instance['loop'];
		$instance['order_type']		= $new_instance['order_type'];
		$instance['order_desc']		= $new_instance['order_desc'];
		$instance['columns']		= (int)$new_instance['columns'];
		$instance['see_title']		= $new_instance['see_title'] == 'yes';
		$instance['title_tag']		= $new_instance['title_tag'];
		$instance['see_image']		= $new_instance['see_image'] == 'yes';
		$instance['image_size']		= $new_instance['image_size'];
		$instance['see_content']	= $new_instance['see_content'] == 'yes';
		$instance['see_excerpt']	= $new_instance['see_excerpt'] == 'yes';
		$instance['see_author']		= $new_instance['see_author'] == 'yes';
		$instance['see_posted_on']	= $new_instance['see_posted_on'] == 'yes';
		$instance['see_taxonomies']	= $new_instance['see_taxonomies'] == 'yes';
		$instance['see_meta_utilities']	= $new_instance['see_meta_utilities'] == 'yes';
		$instance['see_price']		= $new_instance['see_price'] == 'yes';
		$instance['see_buy_button']	= $new_instance['see_buy_button'] == 'yes';
		$instance['see_first_custom_area']	= $new_instance['see_first_custom_area'] == 'yes';
		$instance['see_second_custom_area']	= $new_instance['see_second_custom_area'] == 'yes';
		$instance['see_third_custom_area']	= $new_instance['see_third_custom_area'] == 'yes';
		return apply_filters( 'tcp_custom_post_type_list_widget_update', $instance, $new_instance );
	}

	function form( $instance ) {
		$defaults = array(
			'title'			=> 'Custom Post Type List',
			'post_type'		=> TCP_PRODUCT_POST_TYPE,
			'use_taxonomy'	=> true,
			'taxonomy'		=> true,
			'term'			=> TCP_PRODUCT_CATEGORY,
			'included'		=> array(),
			'limit'			=>  5,
			'loop'			=> '',
			'order_type'	=> 'date',
			'order_desc'	=> 'asc',
			'columns'		=> 2,
			'see_title'		=> true,
			'title_tag'		=> '',
			'see_image'		=> false,
			'image_size'	=> 'thumbnail',
			'see_content'	=> false,
			'see_excerpt'	=> false,
			'see_posted_on'	=> false,
			'see_taxonomies'=> false,
			'see_meta_utilities' => false,
			'see_price'		=> true,
			'see_buy_button'=> false,
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$see_title			= isset( $instance['see_title'] ) ? $instance['see_title'] : false;
		$title_tag			= isset( $instance['title_tag'] ) ? $instance['title_tag'] : '';
		$order_type			= isset( $instance['order_type'] ) ? $instance['order_type'] : 'date';
		$order_desc			= isset( $instance['order_desc'] ) ? $instance['order_desc'] : 'asc';
		$see_image			= isset( $instance['see_image'] ) ? $instance['see_image'] : false;
		$image_size			= isset( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
		$see_content		= isset( $instance['see_content'] ) ? $instance['see_content'] : false;
		$see_excerpt		= isset( $instance['see_excerpt'] ) ? $instance['see_excerpt'] : false;
		$see_author			= isset( $instance['see_author'] ) ? $instance['see_author'] : false;
		$see_posted_on		= isset( $instance['see_posted_on'] ) ? $instance['see_posted_on'] : false;
		$see_taxonomies		= isset( $instance['see_taxonomies'] ) ? $instance['see_taxonomies'] : false;
		$see_meta_utilities	= isset( $instance['see_meta_utilities'] ) ? $instance['see_meta_utilities'] : false;
		$see_price			= isset( $instance['see_price'] ) ? $instance['see_price'] : false;
		$see_buy_button		= isset( $instance['see_buy_button'] ) ? $instance['see_buy_button'] : false;
		$use_taxonomy 		= isset( $instance['use_taxonomy'] ) ? $instance['use_taxonomy'] : false;
		$see_first_custom_area 	= isset( $instance['see_first_custom_area'] ) ? $instance['see_first_custom_area'] : false;
		$see_second_custom_area = isset( $instance['see_second_custom_area'] ) ? $instance['see_second_custom_area'] : false;
		$see_third_custom_area 	= isset( $instance['see_third_custom_area'] ) ? $instance['see_third_custom_area'] : false;
		if ( $use_taxonomy ) {
			$use_taxonomy_style = '';
			$included_style = 'display: none;';
		} else {
			$use_taxonomy_style = 'display: none;';
			$included_style = '';
		}
		$related_type = isset( $instance['related_type'] ) ? $instance['related_type'] : '';
		if ( $related_type != '')
			$p_included_style = 'display: none;';
		else 
			$p_included_style = '';
		?>
		<script>
		function tcp_show_taxonomy(checked) {
			if (checked) {
				jQuery('.tcp_taxonomy_controls').show();
				jQuery('.tcp_post_included').hide();
			} else {
				jQuery('.tcp_taxonomy_controls').hide();
				jQuery('.tcp_post_included').show();
			}
		}
		</script>
	<div id="column_1">
		<?php do_action( 'tcp_custom_post_type_list_widget_form_before', $this ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $post_type ) : ?>
				<option value="<?php echo $post_type->name; ?>"<?php selected( $instance['post_type'], $post_type->name ); ?>><?php echo $post_type->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			<span class="description"><?php _e( 'Press save to load the next list', 'tcp' ); ?></span>
		</p><p style="margin-bottom:0;">
			<input type="checkbox" class="checkbox" onclick="tcp_show_taxonomy(this.checked);" id="<?php echo $this->get_field_id( 'use_taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'use_taxonomy' ); ?>" value="yes" <?php checked( $use_taxonomy, true ); ?> />
			<label for="<?php echo $this->get_field_id( 'use_taxonomy' ); ?>"><?php _e( 'Use Taxonomy', 'tcp' ); ?></label>
		</p>
		<div class="tcp_taxonomy_controls" style="<?php echo $use_taxonomy_style; ?>">
			<p style="margin-top:0;">
				<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'tcp' )?>:</label>
				<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
					<option value="" <?php selected( $instance['taxonomy'], '' ); ?>><?php _e( 'all', 'tcp' ); ?></option>
				<?php foreach( get_object_taxonomies( $instance['post_type'] ) as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
					<option value="<?php echo esc_attr( $taxonomy ); ?>"<?php selected( $instance['taxonomy'], $taxonomy ); ?>><?php echo esc_attr( $tax->labels->name ); ?></option>
				<?php endforeach; ?>
				</select>
				<span class="description"><?php _e( 'Press save to load the next list', 'tcp' ); ?></span>
			</p><p>
				<label for="<?php echo $this->get_field_id( 'term' ); ?>"><?php _e( 'Term', 'tcp' )?>:</label>
				<select name="<?php echo $this->get_field_name( 'term' ); ?>" id="<?php echo $this->get_field_id( 'term' ); ?>" class="widefat">
				<?php if ( $instance['taxonomy'] ) : 
					$term_slug = isset( $instance['term'] ) ? $instance['term'] : '';
					$terms = get_terms( $instance['taxonomy'], array( 'hide_empty' => false ) );
					if ( is_array( $terms ) && count( $terms ) )
						foreach( $terms as $term ) : 
							if ( $term->term_id == tcp_get_default_id( $term->term_id, $instance['taxonomy'] ) ) :?>
								<option value="<?php echo $term->slug; ?>"<?php selected( $term_slug, $term->slug ); ?>><?php echo esc_attr( $term->name ); ?></option>
							<?php endif;
						endforeach;
				endif; ?>
				</select>
			</p>
		</div> <!-- tcp_taxonomy_controls -->
		<div class="tcp_post_included" style="<?php echo $included_style; ?>">
			<div id="p_included" style="<?php echo $p_included_style; ?>"><p style="margin-top:0;">
				<label for="<?php echo $this->get_field_id( 'included' ); ?>"><?php _e( 'Included', 'tcp' )?>:</label>
				<select name="<?php echo $this->get_field_name( 'included' ); ?>[]" id="<?php echo $this->get_field_id( 'included' ); ?>" class="widefat" multiple size="8" style="height: auto">
					<option value="" <?php selected( $instance['included'], '' ); ?>><?php _e( 'all', 'tcp' ); ?></option>
				<?php
				$args = array(
					'post_type'			=> $instance['post_type'],
					'posts_per_page'	=> -1,
				);
				if ( $instance['post_type'] == TCP_PRODUCT_POST_TYPE ) {
					$args['meta_key'] = 'tcp_is_visible';
					$args['meta_value'] = true;
				}
				$included = isset( $instance['included'] ) ? $instance['included'] : array();
				if ( ! is_array( $included ) ) $included = array();
				$posts = get_posts( $args );
				if ( is_array( $posts ) && count( $posts ) ) :
					foreach( $posts as $post ) : ?>
					<option value="<?php echo $post->ID; ?>"<?php tcp_selected_multiple( $included, $post->ID ); ?>><?php echo $post->post_title; ?></option>
					<?php endforeach;
				endif; ?>
				</select>
				</p>
			</div><!-- p_included -->
		</div><!-- tcp_post_included -->
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'tcp' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $instance['limit']; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'loop' ); ?>"><?php _e( 'Loop', 'tcp' ); ?>:</label>
			&nbsp;(<?php _e( 'theme', 'tcp' ); ?>:&nbsp;<?php echo get_template(); ?>)
			<select name="<?php echo $this->get_field_name( 'loop' ); ?>" id="<?php echo $this->get_field_id( 'loop' ); ?>" class="widefat">
				<option value="" <?php selected( $instance['loop'], '' ); ?>><?php _e( 'default', 'tcp' ); ?></option>
			<?php
			$files = array();
			$folder = STYLESHEETPATH;
			if ( $handle = opendir( $folder ) ) :
				while ( false !== ( $file = readdir( $handle ) ) ) :
					if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 ) : ?>
					<option value="<?php echo $folder . '/' . $file; ?>" <?php selected( $instance['loop'], $folder . '/' . $file ); ?>><?php echo $file; ?></option>
					<?php $files[] = $file;
					endif; ?>
				<?php endwhile;
				closedir( $handle );
			endif;

			$folder = get_template_directory();
			if ( STYLESHEETPATH != $folder ) :
				if ( $handle = opendir($folder ) ) :
					while ( false !== ( $file = readdir( $handle ) ) ) :
					if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 && ! in_array( $file, $files ) ) : ?>
						<option value="<?php echo $folder . '/' . $file; ?>" <?php selected( $instance['loop'], $folder . '/' . $file ); ?>>[<?php _e( 'parent', 'tcp' ); ?>] <?php echo $file; ?></option>
					<?php endif;
					endwhile;
				closedir( $handle );
				endif;
			endif; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order_type' ); ?>"><?php _e( 'Order by', 'tcp' ); ?></label>:
			<?php $sorting_fields = tcp_get_sorting_fields();
			//$sorting_fields[] = array( 'value' => 'rand', 'title' => __( 'Random', 'tcp' ) ); ?>
			<select id="<?php echo $this->get_field_id( 'order_type' ); ?>" name="<?php echo $this->get_field_name( 'order_type' ); ?>">
			<?php foreach( $sorting_fields as $sorting_field ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endforeach; ?>
			</select>
			<input type="radio" name="<?php echo $this->get_field_name( 'order_desc' ); ?>" id="<?php echo $this->get_field_id( 'order_desc' ); ?>" value="asc" <?php checked( $instance['order_desc'], 'asc' ); ?>/>
			<label for="<?php echo $this->get_field_id( 'order_desc' ); ?>"><?php _e( 'Asc.', 'tcp' ); ?></label>
			<input type="radio" name="<?php echo $this->get_field_name( 'order_desc' ); ?>" id="<?php echo $this->get_field_id( 'order_desc' ); ?>" value="desc" <?php checked( $instance['order_desc'], 'desc' ); ?>/>
			<label for="<?php echo $this->get_field_id( 'order_desc' ); ?>"><?php _e( 'Desc.', 'tcp' ); ?></label>
		</p>
		<p>
			<?php $advanced_id = 'column_advanced_' . $this->get_field_id( 'columns' ); ?>
			<input type="button" onclick="jQuery('#<?php echo $advanced_id; ?>').toggle();" value="<?php _e( 'show/hide advanced options', 'tcp' ); ?>" class="button-secondary" />
		</p>
		<?php do_action( 'tcp_custom_post_type_list_widget_form_after', $this ); ?>
	</div>
	<div id="<?php echo $advanced_id; ?>" style="display:none;">
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'N<sup>o</sup> columns', 'tcp' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="text" value="<?php echo $instance['columns']; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_title' ); ?>" name="<?php echo $this->get_field_name( 'see_title' ); ?>" value="yes" <?php checked( $see_title ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_title' ); ?>"><?php _e( 'Show title', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title_tag' ); ?>"><?php _e( 'Title tag', 'tcp' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'title_tag' ); ?>" name="<?php echo $this->get_field_name( 'title_tag' ); ?>">
				<option value="" <?php selected( $title_tag, '' ); ?>><?php _e( 'No tag', 'tcp' ); ?></option>
				<option value="h2" <?php selected( $title_tag, 'h2' ); ?>>h2</option>
				<option value="h3" <?php selected( $title_tag, 'h3' ); ?>>h3</option>
				<option value="h4" <?php selected( $title_tag, 'h4' ); ?>>h4</option>
				<option value="h5" <?php selected( $title_tag, 'h5' ); ?>>h5</option>
				<option value="h6" <?php selected( $title_tag, 'h6' ); ?>>h6</option>
				<option value="p" <?php selected( $title_tag, 'p' ); ?>>p</option>
				<option value="div" <?php selected( $title_tag, 'div' ); ?>>div</option>
				<option value="span" <?php selected( $title_tag, 'span' ); ?>>span</option>
			</select>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_image' ); ?>" name="<?php echo $this->get_field_name( 'see_image' ); ?>" value="yes" <?php checked( $see_image ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_image' ); ?>"><?php _e( 'Show image', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image size', 'tcp' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
			<?php $imageSizes = get_intermediate_image_sizes();
			foreach($imageSizes as $imageSize) : ?>
				<option value="<?php echo $imageSize; ?>" <?php selected( $imageSize, $image_size ); ?>><?php echo $imageSize; ?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_content' ); ?>" name="<?php echo $this->get_field_name( 'see_content' ); ?>" value="yes" <?php checked( $see_content ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_content' ); ?>"><?php _e( 'Show content', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'see_excerpt' ); ?>" value="yes" <?php checked( $see_excerpt ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_excerpt' ); ?>"><?php _e( 'Show excerpt', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_author' ); ?>" name="<?php echo $this->get_field_name( 'see_author' ); ?>" value="yes" <?php checked( $see_author ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_author' ); ?>"><?php _e( 'Show about author', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_posted_on' ); ?>" name="<?php echo $this->get_field_name( 'see_posted_on' ); ?>" value="yes" <?php checked( $see_posted_on ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_posted_on' ); ?>"><?php _e( 'Show posted on', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_taxonomies' ); ?>" name="<?php echo $this->get_field_name( 'see_taxonomies' ); ?>" value="yes" <?php checked( $see_taxonomies ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_taxonomies' ); ?>"><?php _e( 'Show taxonomies', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_meta_utilities' ); ?>" name="<?php echo $this->get_field_name( 'see_meta_utilities' ); ?>" value="yes" <?php checked( $see_meta_utilities ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_meta_utilities' ); ?>"><?php _e( 'Show utilities', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_price' ); ?>" name="<?php echo $this->get_field_name( 'see_price' ); ?>" value="yes" <?php checked( $see_price ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_price' ); ?>"><?php _e( 'Show price', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_buy_button' ); ?>" name="<?php echo $this->get_field_name( 'see_buy_button' ); ?>" value="yes" <?php checked( $see_buy_button ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_buy_button' ); ?>"><?php _e( 'Show buy button', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_first_custom_area' ); ?>" name="<?php echo $this->get_field_name( 'see_first_custom_area' ); ?>" value="yes" <?php checked( $see_first_custom_area ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_first_custom_area' ); ?>"><?php _e( 'Show first custom area', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_second_custom_area' ); ?>" name="<?php echo $this->get_field_name( 'see_second_custom_area' ); ?>" value="yes" <?php checked( $see_second_custom_area ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_second_custom_area' ); ?>"><?php _e( 'Show second custom area', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_third_custom_area' ); ?>" name="<?php echo $this->get_field_name( 'see_third_custom_area' ); ?>" value="yes" <?php checked( $see_third_custom_area ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_third_custom_area' ); ?>"><?php _e( 'Show third custom area', 'tcp' ); ?></label>
		</p>
	</div>
	<?php
	}
}
?>
