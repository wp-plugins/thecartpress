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

class ArchivesCustomPostType extends WP_Widget {

	private $post_type;
	
	function ArchivesCustomPostType() {
		$widget_settings = array(
			'classname'		=> 'archivescustomposttype',
			'description'	=> __( 'Allow to create lists of Archives for custom post types', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'archivescustomposttype-widget'
		);
		$this->WP_Widget( 'archivescustomposttype-widget', 'TCP Archives for Custom Post Type', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$c = $instance['count'] ? '1' : '0';
		$d = $instance['dropdown'] ? '1' : '0';
		//$title = apply_filters('widget_title', empty($instance['title']) ? __('Archives') : $instance['title'], $instance, $this->id_base);
		$title	= apply_filters( 'widget_title', $instance['title'] );
		$type	= isset( $instance['type'] ) ? $instance['type'] : 'monthly';
		$limit	= isset( $instance['limit'] ) ? (int)$instance['limit'] : 5;
		$this->post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : 'tcp_product';
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		add_filter( 'getarchives_where' , array( $this, 'getArchivesWhereFilter' ) , 10 , 2 );
		if ( $d ) {
			$args = array(
				'type'				=> $type,
				'format'			=> 'option',
				'show_post_count'	=> $c,
				'post_type'			=> $this->post_type,
				'limit'				=> $limit,
			);?>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
				<option value=""><?php echo esc_attr( __('Select Month', 'tcp' ) ); ?></option>
				<?php wp_get_archives( apply_filters( 'widget_archives_dropdown_args', $args ) );?>
			</select>
		<?php } else {
			$args = array(
				'type'				=> $type,
				'show_post_count'	=> $c,
				'post_type'			=> $this->post_type,
				'limit'				=> $limit,
			);?>
			<ul>
			<?php wp_get_archives( apply_filters( 'widget_archives_args', $args ) ); ?>
			</ul>
		<?php }
		remove_filter( 'getarchives_where' , array( $this, 'getArchivesWhereFilter' ) );
		echo $after_widget;
	}

	function getArchivesWhereFilter( $where , $r ) {
		if ( $this->post_type == '')
			return str_replace( "post_type = 'post' AND" , '' , $where );
		else
			return str_replace( "post_type = 'post'" , "post_type = '{$this->post_type}'" , $where );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$instance['title']		= strip_tags($new_instance['title']);
		$instance['count']		= $new_instance['count'] ? 1 : 0;
		$instance['dropdown']	= $new_instance['dropdown'] ? 1 : 0;
		$instance['type']		= $new_instance['type'];
		$instance['limit']		= (int)$new_instance['limit'];
		$instance['post_type']	= $new_instance['post_type'];
		return $instance;
	}

	function form( $instance ) {
		$title		= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : __( 'Archives for Custom Post Type', 'tcp' );
		$count		= isset( $instance['count'] ) ? $instance['count'] ? 'checked="checked"' : '' : '';
		$dropdown	= isset( $instance['dropdown'] ) ? $instance['dropdown'] ? 'checked="checked"' : '' : '';
		$post_type	= isset( $instance['post_type'] ) ? $instance['post_type']  : 'tcp_product';
		$type		= isset( $instance['type'] ) ? $instance['type']  : 'monthly';
		$limit		= isset( $instance['limit'] ) ? (int)$instance['limit'] : 5; ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p><p>
			<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as dropdown'); ?></label>
			<br/>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type', 'tcp' ); ?>:</label>
			<br />
			<select name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>" class="widefat">
				<option value="yearly" <?php selected( $type, 'yearly' );?>><?php _e( 'Yearly', 'tcp' );?></option>
				<option value="monthly" <?php selected( $type, 'monthly' );?>><?php _e( 'Monthly', 'tcp' );?></option>
				<option value="daily" <?php selected( $type, 'daily' );?>><?php _e( 'Daily', 'tcp' );?></option>
				<option value="weekly" <?php selected( $type, 'weekly' );?>><?php _e( 'Weekly', 'tcp' );?></option>
				<option value="postbypost" <?php selected( $type, 'postbypost' );?>><?php _e( 'Post by post', 'tcp' );?></option>
				<option value="alpha" <?php selected( $type, 'alpha' );?>><?php _e( 'Alpha', 'tcp' );?></option>
			</select>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Limit:', 'tcp' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $limit; ?>" size="3" maxlength="4" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
				<option value=""<?php selected( '', $post_type ); ?>><?php _e( 'All', 'tcp' );?></option>
			<?php foreach( get_post_types() as $posttype ) : ?>
				<option value="<?php echo $posttype;?>"<?php selected( $post_type, $posttype ); ?>><?php echo $posttype;?></option>
			<?php endforeach; ?>
			</select>
		</p>
<?php }
}
