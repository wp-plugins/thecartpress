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

if ( ! isset( $instance ) ) $instance = get_option( 'ttc_settings' );	
$see_title				= isset( $instance['see_title'] ) ? $instance['see_title'] : true;
$see_image				= isset( $instance['see_image'] ) ? $instance['see_image'] : true;
$image_size				= isset( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
$see_excerpt			= isset( $instance['see_excerpt'] ) ? $instance['see_excerpt'] : true;
$see_content			= isset( $instance['see_content'] ) ? $instance['see_content'] : false;
$see_price				= isset( $instance['see_price'] ) ? $instance['see_price'] : true;
$see_buy_button			= isset( $instance['see_buy_button'] ) ? $instance['see_buy_button'] : false;
$see_meta_data			= isset( $instance['see_meta_data'] ) ? $instance['see_meta_data'] : false;
$see_meta_utilities		= isset( $instance['see_meta_utilities'] ) ? $instance['see_meta_utilities'] : false;
$number_of_columns		= isset( $instance['columns'] ) ? (int)$instance['columns'] : 2;
//custom areas
$see_first_custom_area	= isset( $instance['see_first_custom_area'] ) ? $instance['see_first_custom_area'] : false;
$see_second_custom_area	= isset( $instance['see_second_custom_area'] ) ? $instance['see_second_custom_area'] : false;
$see_third_custom_area	= isset( $instance['see_third_custom_area'] ) ? $instance['see_third_custom_area'] : false;

if ( have_posts() ) while ( have_posts() ): the_post();?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-title">
			<?php echo the_title(); ?>
		</div>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>
		<div class="entry-content">
			<?php the_content( __('Continue reading <span class="meta-nav">&rarr;</span>', 'tcp' ) ); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">'.__( 'Pages:', 'tcp' ), 'after' => '</div>' ) ); ?>
		</div>
		<div class="entry-utility">
			<?php if ( count( get_the_category() ) ): ?>
				<span class="cat-links">
					<?php printf( __('<span class="%1$s">Posted in</span> %2$s', 'tcp'), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list(', ')); ?>
				</span>
				<span class="meta-sep">|</span>
			<?php endif; ?>
			<?php
				$tags_list = get_the_tag_list( '', ', ' );
				if ( $tags_list ): ?>
				<span class="tag-links">
					<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'tcp'), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list); ?>
				</span>
				<span class="meta-sep">|</span>
			<?php endif; ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'tcp'), __('1 Comment', 'tcp'), __('% Comments', 'tcp')); ?></span>
			<?php edit_post_link(__( 'Edit', 'tcp'), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?>
		</div>
	</div>
<?php endwhile;?>
