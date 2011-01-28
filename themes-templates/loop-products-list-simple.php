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

set_post_thumbnail_size( 100, 100 );
$currency = tcp_the_currency( false ); 
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

?>
<ul>
<?php if ( have_posts() ) while ( have_posts() ) : the_post();?>
		<li>
			<ul>
				<?php if ( $see_title ) : ?>
					<li><<?php echo $title_tag;?>><a href="<?php echo get_permalink( );?>" border="0"><?php the_title();?></a></<?php echo $title_tag;?>></li>
				<?php endif;?>
				<?php if ( $see_image ) : ?>
					<li><a href="<?php the_permalink();?>" border="0"><?php echo the_post_thumbnail();?></a></li>
				<?php endif;?>
				<?php if ( $see_price ) : ?>
					<li><?php tcp_the_price_label();?> <?php echo $currency;?> (<?php echo tcp_the_tax_label();?>)</li>
				<?php endif;?>
				<?php if ( $see_content ) : ?>
					<li><?php the_content();?></li>
				<?php endif;?>
				<?php if ( $see_excerpt ) : ?>
					<li><?php the_excerpt();?></li>
				<?php endif;?>
				<?php if ( $see_buy_button ) : ?>
					<?php if ( tcp_get_the_product_type() == 'SIMPLE' ) : ?>
						<li><?php tcp_the_buy_button();?></li>
					<?php endif;?>
				<?php endif;?>
				<?php if ( $see_first_custom_area ) :?>
first
				<?php endif;?>
				<?php if ( $see_second_custom_area ) :?>
Second
				<?php endif;?>
				<?php if ( $see_third_custom_area ) :?>
third
				<?php endif;?>

			</ul>
		</li>
<?php endwhile; ?>
</ul>
