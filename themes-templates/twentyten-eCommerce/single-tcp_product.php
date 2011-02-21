<?php
/**
 * The Template for displaying all single products.
 *
 * @package WordPress
 * @subpackage Twenty_Ten_Ecommerce
 * @since Twenty Ten Ecommerce 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<?php
			/* Run the loop to output the post.
			 */
			get_template_part( 'loop', 'single-product' );
			?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
