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

/**
 * The loop that displays products in configurable GRID mode.
 *
 * @package TheCartPRess
 * @subpackage 
 * @since 1.1.6
 */
?>

<?php /* If there are no products to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>


<?php /* Start the Loop.*/ ?>

<table class="tcp_products_list">
<tr class="tcp_first-row">
<?php global $thecartpress;
if ( ! isset( $instance ) ) $instance = get_option( 'ttc_settings' );
$suffix = '-' . get_post_type( get_the_ID() );
if ( ! isset( $instance['title_tag' . $suffix] ) ) $suffix = '';
	
$see_title				= isset( $instance['see_title' . $suffix] ) ? $instance['see_title' . $suffix] : true;
$title_tag				= isset( $instance['title_tag' . $suffix] ) ? $instance['title_tag' . $suffix] : '';
$see_image				= isset( $instance['see_image' . $suffix] ) ? $instance['see_image' . $suffix] : true;
$image_size				= isset( $instance['image_size' . $suffix] ) ? $instance['image_size' . $suffix] : 'thumbnail';
$see_excerpt			= isset( $instance['see_excerpt' . $suffix] ) ? $instance['see_excerpt' . $suffix] : true;
$see_content			= isset( $instance['see_content' . $suffix] ) ? $instance['see_content' . $suffix] : false;
$see_price				= isset( $instance['see_price' . $suffix] ) ? $instance['see_price' . $suffix] : true;
$see_buy_button			= isset( $instance['see_buy_button' . $suffix] ) ? $instance['see_buy_button' . $suffix] : false;
$see_author				= isset( $instance['see_author' . $suffix] ) ? $instance['see_author' . $suffix] : false;
$see_posted_on			= isset( $instance['see_posted_on' . $suffix] ) ? $instance['see_posted_on' . $suffix] : false;
$see_taxonomies			= isset( $instance['see_taxonomies' . $suffix] ) ? $instance['see_taxonomies' . $suffix] : false;
$see_meta_utilities		= isset( $instance['see_meta_utilities' . $suffix] ) ? $instance['see_meta_utilities' . $suffix] : false;
$see_sorting_panel		= isset( $instance['see_sorting_panel' . $suffix] ) ? $instance['see_sorting_panel' . $suffix] : false;
$number_of_columns		= isset( $instance['columns' . $suffix] ) ? (int)$instance['columns' . $suffix] : 2;
//custom areas. Usefull to insert other template tag from WordPress or anothers plugins 
$see_first_custom_area	= isset( $instance['see_first_custom_area' . $suffix] ) ? $instance['see_first_custom_area' . $suffix] : false;
$see_second_custom_area	= isset( $instance['see_second_custom_area' . $suffix] ) ? $instance['see_second_custom_area' . $suffix] : false;
$see_third_custom_area	= isset( $instance['see_third_custom_area' . $suffix] ) ? $instance['see_third_custom_area' . $suffix] : false;
$see_pagination			= isset( $instance['see_pagination' . $suffix] ) ? $instance['see_pagination' . $suffix] : false;
$column = $number_of_columns;

if ( isset( $instance['title_tag' . $suffix] ) && $instance['title_tag' . $suffix] != '' ) {
	$title_tag = '<' . $instance['title_tag' . $suffix] . ' class="entry-title">';
	$title_end_tag = '</' . $instance['title_tag' . $suffix] . '>';
} else {
	$title_tag = '';
	$title_end_tag = '';
} ?>

<?php if ( $see_sorting_panel ) {
	tcp_the_sort_panel();
}

while ( have_posts() ) : the_post();
if ( $column == 0 ) : $column = $number_of_columns ?>
	</tr>
	<tr>
	<?php endif;
	$tcp_col = $number_of_columns - $column + 1;
	$class = array( 'tcp_' . $number_of_columns . '_cols', 'tcp_col_' . $tcp_col ); ?>
	<td id="td-post-<?php the_ID(); ?>" class="tcp_col <?php echo implode( ' ', $class ); ?>">
	<?php $column--; ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( $see_title ) : ?>
			<?php echo $title_tag; ?><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a><?php echo $title_end_tag; ?>
		<?php endif; ?>
		<?php if ( $see_posted_on ) : ?>
			<div class="entry-meta">
				<?php tcp_posted_on(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
			<?php if ( $see_price ) :?>
			<div class="entry-price">
				<?php tcp_the_price_label();?>
			</div>
			<?php endif;?>
			<?php if ( $see_image ) : ?>
			<div class="entry-post-thumbnail">
				<?php if ( $see_buy_button ) : ?>
					<?php $image = '<a class="tcp_size-' . $image_size . '" href="' . tcp_get_permalink() . '">';
					$image .= tcp_get_the_thumbnail( get_the_ID(), 0, 0, $image_size ) . '</a>';
					$args = array(
						'size'	=> $image_size,
						//'align'	=> $image_align,
						'link'	=> $thecartpress->get_setting( 'image_link_content', 'permalink' ),
					);
					$image = apply_filters( 'tcp_get_image_in_excerpt', $image, get_the_ID(), $args );
					echo $image; ?>
				<?php else : ?>
					<a class="size-<?php echo $image_size;?>" href="<?php echo tcp_get_permalink(); ?>"><?php the_post_thumbnail($image_size); ?></a>				
				<?php endif; ?>
			</div><!-- .entry-post-thumbnail -->
			<?php endif; ?>	
			<?php if ( $see_excerpt ) : ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<?php endif; ?>
			<?php if ( $see_buy_button ) : ?>
			<div class="entry-buy-button">	
				<?php tcp_the_buy_button(); ?>
			</div>
			<?php endif;?>
		<?php if ( $see_content ) : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
		<?php endif; ?>
		<?php if ( $see_first_custom_area ) :?>
        <?php endif;?>
        <?php if ( $see_second_custom_area ) :?>
        <?php endif;?>
        <?php if ( $see_third_custom_area ) :?>
        <?php endif;?>
        <?php if ( $see_author ) :?>
            <?php if ( get_the_author_meta( 'description') ) : // If a user has filled out their description, show a bio on their products  ?>
                <div id="entry-author-info">
                    <div id="author-avatar">
                        <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
                    </div><!-- #author-avatar -->
                    <div id="author-description">
                        <h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author_meta() ); ?></h2>
                        <?php the_author_meta( 'description'); ?>
                    </div><!-- #author-description -->
                </div><!-- #entry-author-info -->
            <?php endif; ?>
        <?php endif; ?>
			<div class="entry-utility">
			<?php if ( $see_taxonomies ) : ?>
				<?php if ( count( get_the_terms( 0, 'tcp_product_category' ) ) ) : ?>
					<span class="cat-links">
						<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_term_list(0, 'tcp_product_category', '', ', ' ) ); ?>
					</span>
					<span class="meta-sep">|</span>
					<?php
						$tags_list = get_the_term_list( 0, 'tcp_product_tag', '', ', ' );
						if ( $tags_list ): ?>
						<span class="tag-links">
							<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
						</span>
						<span class="meta-sep">|</span>
					<?php endif;
						$tags_list = get_the_term_list( 0, 'tcp_product_supplier', '', ', ' );
						if ( $tags_list ) : ?>
						<span class="tag-links">
							<?php printf( __( '<span class="%1$s">Supplied by</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?> 
			<?php if ( $see_meta_utilities ) : ?>
	                <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?>
	                <?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?></span>
			<?php endif; ?> 
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->
		<?php comments_template( '', true ); ?>
</td>
<?php endwhile; // End the loop ?>
<?php for(; $column > 0; $column-- ) : 
	$class = array( 'tcp_' . $number_of_columns . '_cols', 'tcp_col_' . ++$tcp_col );?>
	<td class="tcp_col <?php echo implode( ' ', $class ); ?> tcp_td_empty">&nbsp;</td>
<?php endfor; ?>
</tr></table>

<?php /* Display pagination */
if ( $see_pagination ) tcp_get_the_pagination(); ?>
