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

//used by Last visited and RelatedList
class CustomListWidget extends WP_Widget {

	function CustomListWidget( $name, $description, $title, $width = 300 ) {
		$widget_settings = array(
			'classname'		=> $name,
			'description'	=> $description,
		);
		$control_settings = array(
			'width'		=> $width,
			'id_base'	=> $name . '-widget'
		);
		$this->WP_Widget( $name . '-widget', $title, $widget_settings, $control_settings );
	}

	function widget( $args, $loop_args, $instance ) {
		extract( $args );
		query_posts( $loop_args );
		if ( ! have_posts() ) return;
		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title ) echo $before_title, $title, $after_title;
		if ( strlen( $instance['loop'] ) > 0 && file_exists( $instance['loop'] ) ) {
			include( $instance['loop'] );
		} else if ( have_posts() ) while ( have_posts() ) : the_post();?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( $instance['see_title'] ) : ?>
				<div class="entry-title">
					<a href="<?php the_permalink( );?>" border="0"><?php echo the_title(); ?></a>
				</div>
				<?php endif;?>
				<?php if ( $instance['see_excerpt'] ) : ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div>
				<?php endif;?>
				<?php if ( $instance['see_price'] ) : ?>
				<div class="entry-product_custom">
					<p class="entry_tcp_price"><?php echo __( 'price', 'tcp' );?>:&nbsp;<?php echo tcp_get_the_price_label( get_the_ID() );?>&nbsp;<?php tcp_the_currency();?>(<?php echo tcp_get_the_tax_label( get_the_ID() );?>)</p>
				</div>
				<?php endif;?>
				<?php if ( $instance['see_content'] ) : ?>
				<div class="entry-content">
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'tcp' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">'.__( 'Pages:', 'tcp' ), 'after' => '</div>')); ?>
				</div>
				<?php endif;?>
				<?php if ( $instance['see_meta_data'] ) : ?>
				<div class="entry-utility">
					<?php if ( count( get_the_category() ) ): ?>
						<span class="cat-links">
							<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'tcp' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
						</span>
						<span class="meta-sep">|</span>
					<?php endif; ?>
					<?php
						$tags_list = get_the_tag_list( '', ', ' );
						if ( $tags_list ): ?>
						<span class="tag-links">
							<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'tcp' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
						</span>
						<span class="meta-sep">|</span>
					<?php endif; ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'tcp' ), __( '1 Comment', 'tcp' ), __( '% Comments', 'tcp' ) ); ?></span>
					<?php edit_post_link( __( 'Edit', 'tcp' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
				</div>
				<?php endif;?>
			</div>
		<?php endwhile;
		wp_reset_postdata();
		wp_reset_query();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['limit']			= (int)$new_instance['limit'];
		$instance['loop']			= $new_instance['loop'];
		$instance['columns']		= (int)$new_instance['columns'];
		$instance['see_title']		= $new_instance['see_title'] == 'yes';
		$instance['see_image']		= $new_instance['see_image'] == 'yes';
		$instance['image_size']		= $new_instance['image_size'] == 'yes';
		$instance['see_content']	= $new_instance['see_content'] == 'yes';
		$instance['see_excerpt']	= $new_instance['see_excerpt'] == 'yes';
		$instance['see_author']		= $new_instance['see_author'] == 'yes';
		$instance['see_meta_data']	= $new_instance['see_meta_data'] == 'yes';
		$instance['see_price']		= $new_instance['see_price'] == 'yes';
		$instance['see_buy_button']	= $new_instance['see_buy_button'] == 'yes';
		$instance['see_first_custom_area']	= $new_instance['see_first_custom_area'] == 'yes';
		$instance['see_second_custom_area']	= $new_instance['see_second_custom_area'] == 'yes';
		$instance['see_third_custom_area']	= $new_instance['see_third_custom_area'] == 'yes';
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'limit'				=>  5,
			'loop'				=> '',
			'columns'			=> 2,
			'see_title'			=> true,
			'see_image'			=> false,
			'image_size'		=> 'thumbnail',
			'see_content'		=> false,
			'see_excerpt'		=> false,
			'see_meta_data'		=> false,
			'see_price'			=> true,
			'see_buy_button'	=> false,
			'see_first_custom_area'		=> false,
			'see_second_custom_area'	=> false,
			'see_third_custom_area'		=> false,
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$see_title		= isset( $instance['see_title'] )		? $instance['see_title'] 	: false;
		$see_image		= isset( $instance['see_image'] )		? $instance['see_image']	: false;
		$see_content	= isset( $instance['see_content'] )		? $instance['see_content']	: false;
		$see_excerpt	= isset( $instance['see_excerpt'] )		? $instance['see_excerpt']	: false;
		$see_author		= isset( $instance['see_author'] )		? $instance['see_author']	: false;
		$see_meta_data	= isset( $instance['see_meta_data'] )	? $instance['see_meta_data']: false;
		$see_price		= isset( $instance['see_price'] )		? $instance['see_price']	: false;
		$see_buy_button	= isset( $instance['see_buy_button'] )	? $instance['see_buy_button']: false;
		$use_taxonomy 	= isset( $instance['use_taxonomy'] ) 	? $instance['use_taxonomy'] : false;
		$see_first_custom_area 	= isset( $instance['see_first_custom_area'] ) 	? $instance['see_first_custom_area'] : false;
		$see_second_custom_area = isset( $instance['see_second_custom_area'] ) 	? $instance['see_second_custom_area'] : false;
		$see_third_custom_area 	= isset( $instance['see_third_custom_area'] ) 	? $instance['see_third_custom_area'] : false;
		?>
	<div id="default">
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'tcp' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $instance['limit']; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'loop' ); ?>"><?php _e( 'Loop', 'tcp' ); ?>:</label>
			&nbsp;(<?php _e( 'theme', 'tcp' );?>:&nbsp;<?php echo get_template();?>)
			<select name="<?php echo $this->get_field_name( 'loop' ); ?>" id="<?php echo $this->get_field_id( 'loop' ); ?>" class="widefat">
				<option value="" <?php selected( $instance['loop'], "" ); ?>"><?_e( 'default', 'tcp' ); ?></option>
			<?php
			$files = array();
			$folder = STYLESHEETPATH;
			if ( $handle = opendir($folder ) ) while ( false !== ( $file = readdir( $handle ) ) ) :
				if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 ) : ?>
					<option value="<?php echo $folder . '/' . $file;?>" <?php selected( $instance['loop'], $folder . '/' . $file ); ?>"><?echo $file; ?></option>
				<?php 
					$files[] = $file;
				endif;?>
			<?php endwhile; 
			closedir( $handle );
			$folder = get_template_directory();
			if ( STYLESHEETPATH != $folder )
				if ( $handle = opendir($folder ) ) while ( false !== ( $file = readdir( $handle ) ) ) :
					if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 && ! in_array( $file, $files ) ) : ?>
						<option value="<?php echo $folder . '/' . $file;?>" <?php selected( $instance['loop'], $folder . '/' . $file ); ?>">[<?php _e( 'parent', 'tcp' );?>] <?echo $file; ?></option>
					<?php endif;?>
				<?php endwhile; closedir( $handle );?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'Nº columns', 'tcp' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="text" value="<?php echo $instance['columns']; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_title' ); ?>" name="<?php echo $this->get_field_name( 'see_title' ); ?>" value="yes" <?php checked( $see_title ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_title' ); ?>"><?php _e( 'Show title', 'tcp' ); ?></label>
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
				<option value="<?php echo $imageSize;?>" <?php selected( $imageSize, $instance['image_size'] );?>><?php echo $imageSize;?></option>
			<?php endforeach;?>
			?>
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
			<label for="<?php echo $this->get_field_id( 'see_author' ); ?>"><?php _e( 'Show author', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_meta_data' ); ?>" name="<?php echo $this->get_field_name( 'see_meta_data' ); ?>" value="yes" <?php checked( $see_meta_data ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_meta_data' ); ?>"><?php _e( 'Show tags', 'tcp' ); ?></label>
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
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_second_custom_area' ); ?>" name="<?php echo $this->get_field_name( 'see_first_custom_area' ); ?>" value="yes" <?php checked( $see_second_custom_area ); ?> />
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
