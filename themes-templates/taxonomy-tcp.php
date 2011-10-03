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
 * The template for displaying Category Archive pages.
 *
 * @package TheCartPress
 * @subpackage 
 * @since 1.1.3
 */

get_header(); ?>
		<div id="container">
			<div id="content" role="main">
				<h1 class="page-title"><span><?php single_cat_title( '' ); ?></span></h1>
				<?php
					$category_description = category_description();
					if ( ! empty( $category_description ) )
						echo '<div class="archive-meta">' . $category_description . '</div>';
					require_once( dirname( __FILE__ ) . '/loop-tcp-grid.php' );
					?>
			</div><!-- #content -->
		</div><!-- #container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
