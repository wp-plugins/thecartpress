<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Ten_Ecommerce
 * @since Twenty Ten Ecommerce 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

				<h1 class="page-title"><?php
					printf( __( 'Category Archives: %s', 'twentyten' ), '<span>' . $wp_query->queried_object->name . '</span>' );
				?></h1>
				<p class="archive-meta">
				<?php
					printf( __( 'Tag Archives: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				?>
                </p>
				<?php
                /* Run the loop for the tag archive to output the products
                 */
                 get_template_part( 'loop', 'products-grid' );
                ?>
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
