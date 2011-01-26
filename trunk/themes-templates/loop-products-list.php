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

set_post_thumbnail_size( 50, 50 );
if ( ! isset( $instance ) ) $instance = get_option( 'ttc_settings' );	
$see_title			= isset( $instance['see_title'] ) ? $instance['see_title'] : true;
$see_image			= isset( $instance['see_image'] ) ? $instance['see_image'] : true;
$image_size			= isset( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
$see_excerpt		= isset( $instance['see_excerpt'] ) ? $instance['see_excerpt'] : true;
$see_content		= isset( $instance['see_content'] ) ? $instance['see_content'] : true;
$see_price			= isset( $instance['see_price'] ) ? $instance['see_price'] : true;
$see_buy_button		= isset( $instance['see_buy_button'] ) ? $instance['see_buy_button'] : true;
$see_meta_data		= isset( $instance['see_meta_data'] ) ? $instance['see_meta_data'] : true;
$number_of_columns	= isset( $instance['columns'] ) ? (int)$instance['columns'] : 2;

if ( have_posts() ) while ( have_posts() ) : the_post();?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( $instance['see_title'] ) : ?>
		<h3 class="entry-title"><a href="<?php the_permalink();?>" border="0"><?php echo the_title(); ?></a></h3>
	<?php endif;?>

	<?php if ( $instance['see_image'] ) : ?>
		<div class="entry-image">
			<a href="<?php the_permalink();?>" border="0"><?php echo the_post_thumbnail( $instance['image_size'] , array( 'alt' => get_the_title(), 'title' => get_the_title() ) ); ?></a>
		</div><!-- entry-image -->
	<?php endif;?>

	<?php if ( $instance['see_price'] ) :?>
		<div class="entry-product_custom">
			<p class="entry_tcp_price"><?php echo __( 'price', 'tcp' );?>:&nbsp;<?php echo tcp_the_price_label();?>&nbsp;<?php tcp_the_currency();?>(<?php echo tcp_the_tax_label();?>)</p>
		<div>
	<?php endif;?>

	<?php if ( $instance['see_excerpt'] ) :?>
		<div class="entry-content">	
			<?php the_excerpt();?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
	<?php endif;?>
	<?php if ( $instance['see_buy_button']) : ?>
		<?php if ( tcp_get_the_product_type() == 'SIMPLE' ) : ?>
			<div class="entry-buy-button">	
				<?php tcp_the_buy_button();?>
			</div>
		<?php endif;?>
	<?php endif;?>
	
	<?php if ( $instance['see_author'] ) :?>
		<?php if ( get_the_author_meta( 'description') ) : // If a user has filled out their description, show a bio on their entries  ?>
			<div id="entry-author-info">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
				</div><!-- #author-avatar -->
				<div id="author-description">
					<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author_meta() ); ?></h2>
					<?php the_author_meta( 'description'); ?>
					<div id="author-link">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author_meta() ); ?>
						</a>
					</div><!-- #author-link	-->
				</div><!-- #author-description -->
			</div><!-- #entry-author-info -->
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( $instance['see_meta_data'] ) : ?>
		<div class="entry-utility">
		<?php 
			$tag_list = get_the_term_list( 0, 'tcp_product_tag', '', ', ' );
			$supplier_list = get_the_term_list( 0, 'tcp_product_supplier', '', ', ' );
			if ( $tag_list && $supplier_list ) {
				$posted_in = __( 'This entry was posted in %1$s and tagged %2$s and supplied by %3$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentytencart' );
			} elseif ( $tag_list ) {
				$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentyten' );
			} elseif ( $supplier_list ) {
				$posted_in = __( 'This entry was posted in %1$s and supplied by %3$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentytencart' );
			} elseif ( is_object_in_taxonomy( get_post_type(), 'tcp_product_category' ) ) {
				$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentyten' );
			} else {
				$posted_in = __( 'Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentyten' );
			}
			// Prints the string, replacing the placeholders.
			printf(
				$posted_in,
				get_the_term_list(0, 'tcp_product_category', '', ' , '),
				$tag_list,
				$supplier_list,
				get_permalink(),
				the_title_attribute( 'echo=0' )
			);?>
			<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-utility -->
	<?php endif;?>
	</div><!-- #post-## -->
<?php endwhile;?>
