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

/**
 * The loop that displays products in configurable GRID mode.
 *
 * @package WordPress
 * @subpackage Twenty_Ten_Ecommerce
 * @since Twenty Ten Ecommerce 1.0
 */
?>
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( isset( $wp_query ) && $wp_query->max_num_pages > 1 ) : ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
	</div><!-- #nav-above -->
<?php endif; ?>

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
<?php
$currency = tcp_the_currency( false ); 
if ( ! isset( $instance ) ) $instance = get_option( 'ttc_settings' );	
$see_title				= isset( $instance['see_title'] ) ? $instance['see_title'] : true;
$see_image				= isset( $instance['see_image'] ) ? $instance['see_image'] : true;
$image_size				= isset( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
$see_excerpt			= isset( $instance['see_excerpt'] ) ? $instance['see_excerpt'] : true;
$see_content			= isset( $instance['see_content'] ) ? $instance['see_content'] : false;
$see_price				= isset( $instance['see_price'] ) ? $instance['see_price'] : true;
$see_buy_button			= isset( $instance['see_buy_button'] ) ? $instance['see_buy_button'] : false;
$see_author				= isset( $instance['see_author'] ) ? $instance['see_author'] : true;
$see_meta_data			= isset( $instance['see_meta_data'] ) ? $instance['see_meta_data'] : false;
$see_meta_utilities		= isset( $instance['see_meta_utilities'] ) ? $instance['see_meta_utilities'] : false;
$number_of_columns		= isset( $instance['columns'] ) ? (int)$instance['columns'] : 2;
//custom areas. Usefull to insert other template tag from WordPress or anothers plugins 
$see_first_custom_area	= isset( $instance['see_first_custom_area'] ) ? $instance['see_first_custom_area'] : false;
$see_second_custom_area	= isset( $instance['see_second_custom_area'] ) ? $instance['see_second_custom_area'] : false;
$see_third_custom_area	= isset( $instance['see_third_custom_area'] ) ? $instance['see_third_custom_area'] : false;

$column = 0;

while ( have_posts() ) : the_post();
?>
<?php
	if ($column == 0)
	{
		echo '<tr>';
		$column = $number_of_columns;
	}
?>
<td id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	$column--;
?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( $see_title ) : ?>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<?php endif; ?>
		<?php if ( $see_meta_data ) : ?>
			<div class="entry-meta">
				<?php twentyten_posted_on(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
			<?php if ( $see_price ) :?>
			<div class="entry-price">	
				<?php tcp_the_price_label('', ' ' . $currency);?> <?php tcp_the_tax_label('(', ')');?>
			</div>
			<?php endif;?>
			<?php if ( $see_image ) : ?>
			<div class="entry-post-thumbnail">
				<a class="size-thumbnail" href="<?php the_permalink(); ?>"><?php the_post_thumbnail($image_size); ?></a>
			</div><!-- .entry-post-thumbnail -->
			<?php endif; ?>	
			<?php if ( $see_excerpt ) : ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<?php endif; ?>
			<?php if ( $see_buy_button ) :?>
			<div class="entry-buy-button">	
				<?php tcp_the_buy_button();?>
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
                        <div id="author-link">
                            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                                <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author_meta() ); ?>
                            </a>
                        </div><!-- #author-link	-->
                    </div><!-- #author-description -->
                </div><!-- #entry-author-info -->
            <?php endif; ?>
        <?php endif; ?>

		<?php if ( $see_meta_utilities ) : ?>
			<div class="entry-utility">
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
						<span class="meta-sep">|</span>
					<?php endif; ?>
				<?php endif; ?>
						<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
		<?php endif; ?>
		</div><!-- #post-## -->
		<?php comments_template( '', true ); ?>
</td>
<?php
	if ($column == 0) echo '</tr>';
?>

<?php endwhile; // End the loop. Whew. ?>
<?php
	for(; $column > 0; $column-- )
		echo '<td>&nbsp;</td>';
?>
</tr></table>
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( isset( $wp_query ) &&  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>
