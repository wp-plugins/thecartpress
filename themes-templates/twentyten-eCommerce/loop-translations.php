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
set_post_thumbnail_size( 100, 100 ); ?>

<div>
<?php if ( have_posts() ) while ( have_posts() ) : the_post();?>
	<table border="0">
		<tr>
			<td><a href="<?php the_permalink(); ?>"><?php echo the_post_thumbnail();?></a></td>
			<td style="vertical-align:top;"><strong><?php the_title();?></strong><?php the_content();?></td>
		</tr>
	</table>
<?php endwhile; ?>
</div>
