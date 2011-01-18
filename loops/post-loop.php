<?php if ( $widget_loop->have_posts() ) while ( $widget_loop->have_posts() ): $widget_loop->the_post();?>
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
