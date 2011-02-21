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
 * Another example for configurable loop.
 *
 * @package WordPress
 * @subpackage Twenty_Ten_Ecommerce
 * @since Twenty Ten Ecommerce 1.0
 */
?>
<?php arras_above_content() ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php arras_above_post() ?>
	<div id="post-<?php the_ID() ?>" <?php arras_single_post_class() ?>>

        <?php arras_postheader() ?>
        
        <div class="entry-content clearfix">
		<?php the_content( __('<p>Read the rest of this entry &raquo;</p>', 'arras') ); ?>  
        <?php wp_link_pages(array('before' => __('<p><strong>Pages:</strong> ', 'arras'), 
			'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div>



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
                            
                        <?php endif; ?>
                    <?php endif; ?>
                            <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?>
                            <?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?></span>
			</div><!-- .entry-utility -->




		<?php arras_postfooter() ?>

        <?php 
		if ( arras_get_option('display_author') ) {
			arras_post_aboutauthor();
		}
        ?>
    </div>
    
	<?php arras_below_post() ?>
	<a name="comments"></a>
    <?php comments_template('', true); ?>
	<?php arras_below_comments() ?>
    
<?php endwhile; else: ?>

<?php arras_post_notfound() ?>

<?php endif; ?>

<?php arras_below_content() ?>
