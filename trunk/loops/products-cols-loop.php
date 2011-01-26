<?php set_post_thumbnail_size( 50, 50 ); ?>

<?php if ( $widget_loop->have_posts() ) :
	$end_of_row = true;
?>
<table id="products-cols-list">
</tbody>
<?php while ( $widget_loop->have_posts() ) : $post = $widget_loop->next_post();
	if ( $end_of_row ) {
		echo '<tr>';
		$i = (int)$instance['columns'];
	}?>
<td>
	<div id="post-<?php echo $post->ID; ?>" <?php post_class( '', $post->ID ); ?>>
	<?php if ( $instance['see_title'] ) : ?>
		<h3 class="entry-title"><a href="<?php get_permalink( $post->ID );?>" border="0"><?php echo get_the_title( $post->ID ); ?></a></h3>
	<?php endif;?>

	<?php if ( $instance['see_image'] ) : ?>
		<div class="entry-image">
			<a href="<?php echo get_permalink( $post->ID );?>" border="0"><?php echo get_the_post_thumbnail( $post->ID, $instance['image_size'] , array( 'alt' => get_the_title( $post->ID ), 'title' => get_the_title( $post->ID ) ) ); ?></a>
		</div><!-- entry-image -->
	<?php endif;?>

	<?php if ( $instance['see_price'] ) :?>
		<div class="entry-product_custom">
			<p class="entry_tcp_price"><?php echo __( 'price', 'tcp' );?>:&nbsp;<?php echo tcp_get_the_price_label( $post->ID );?>&nbsp;<?php tcp_the_currency();?>(<?php echo tcp_get_the_tax_label( $post->ID );?>)</p>
		<div>
	<?php endif;?>

	<?php if ( $instance['see_excerpt'] ) :?>
		<div class="entry-content">	
			<?php echo $post->post_excerpt;?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
	<?php endif;?>
	<?php if ( $instance['see_buy_button']) : ?>
		<?php if ( tcp_get_the_product_type( $post->ID ) == 'SIMPLE' ) : ?>
			<div class="entry-buy-button">	
				<?php tcp_the_buy_button( $post->ID );?>
			</div>
		<?php endif;?>
	<?php endif;?>
	
	<?php if ( $instance['see_author'] ) :?>
		<?php if ( get_the_author_meta( 'description', $post->post_author ) ) : // If a user has filled out their description, show a bio on their entries  ?>
			<div id="entry-author-info">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email', $post->post_author ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
				</div><!-- #author-avatar -->
				<div id="author-description">
					<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author_meta( '', $post->post_author ) ); ?></h2>
					<?php the_author_meta( 'description', $post->post_author ); ?>
					<div id="author-link">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author_meta( '', $post->post_author ) ); ?>
						</a>
					</div><!-- #author-link	-->
				</div><!-- #author-description -->
			</div><!-- #entry-author-info -->
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( $instance['see_tags'] ) : ?>
		<div class="entry-utility">
		<?php 
			$tag_list = get_the_term_list(0, 'tcp_product_tag', '', ', ');
			$supplier_list = get_the_term_list(0, 'tcp_product_supplier_tag', '', ', ');
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
				get_permalink( $post->ID ),
				the_title_attribute( 'echo=0' )
			);?>
			<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-utility -->
	<?php endif;?>
	</div><!-- #post-## -->
</td>
<?php
	$end_of_row = ( --$i <= 0 );
	if ( $end_of_row ) echo '</tr>';
endwhile;?>
</tbody>
</table>
<?php endif;?>
