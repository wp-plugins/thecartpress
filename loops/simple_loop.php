<?php set_post_thumbnail_size( 100, 100 );
$currency = tcp_the_currency( false ); 
/*
$instance['title_tag']
$instance['columns']
$instance['see_title']
$instance['see_image']
$instance['see_content']
$instance['see_excerpt']
$instance['see_tags']
$instance['see_price']
$instance['see_buy_button']
*/
?>
<ul>
<?php if ( $widget_loop->have_posts() ) while ( $widget_loop->have_posts() ): $widget_post = $widget_loop->next_post();
	$post_id = $widget_post->ID;?>
		<li>
			<ul>
				<?php if ( $instance['see_title']) : ?>
					<li><<?php echo $instance['title_tag'];?>><a href="<?php echo get_permalink( $post_id );?>" border="0"><?php echo $widget_post->post_title;?></a></<?php echo $instance['title_tag'];?>></li>
				<?php endif;?>
				<?php if ( $instance['see_image']) : ?>
					<li><a href="<?php echo get_permalink( $post_id );?>" border="0"><?php echo get_the_post_thumbnail( $post_id );?></a></li>
				<?php endif;?>
				<?php if ( $instance['see_price']) : ?>
					<li><?php echo tcp_get_the_price_label( $post_id );?> <?php echo $currency;?> (<?php echo tcp_get_the_tax_label( $post_id );?>)</li>
				<?php endif;?>
				<?php if ( $instance['see_content']) : ?>
					<li><?php echo $widget_post->post_content;?></li>
				<?php endif;?>
				<?php if ( $instance['see_excerpt']) : ?>
					<li><?php echo $widget_post->post_excerpt;?></li>
				<?php endif;?>
				<?php if ( $instance['see_buy_button']) : ?>
					<?php if ( tcp_get_the_product_type( $post_id ) == 'SIMPLE' ) : ?>
						<li><?php tcp_the_buy_button( $post_id );?></li>
					<?php endif;?>
				<?php endif;?>
			</ul>
		</li>
<?php endwhile; ?>
</ul>
