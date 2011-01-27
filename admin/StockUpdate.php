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

$cat_slug = isset( $_REQUEST['category_slug'] ) ? $_REQUEST['category_slug'] : '';
$added_stock = isset( $_REQUEST['added_stock'] ) ? (int)$_REQUEST['added_stock'] : 0;
if ( isset( $_REQUEST['tcp_update_stock'] ) ) {
	$args = array(
		'post_type'				=> 'tcp_product',
		'tcp_product_category'	=>  $cat_slug ,
	);
	$query = new WP_query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$post = $query->next_post();
			$new_stock = isset( $_REQUEST['tcp_new_stock_' . $post->ID] ) ? $_REQUEST['tcp_new_stock_' . $post->ID] : '';
			if ( $new_stock == '' )
				update_post_meta( $post->ID, 'tcp_stock', -1 );
			else
				update_post_meta( $post->ID, 'tcp_stock', (int)$new_stock );
		}?>
		<div id="message" class="updated"><p>
			<?php _e( 'Stock updated.', 'tcp' );?>
		</p></div><?php
	}
	wp_reset_query();
}
?>
<div class="wrap">
<h2><?php _e( 'Stock Update', 'tcp' );?></h2>
<div class="clear"></div>

<form method="post">
	<table class="form-table" >
	<tbody>
	<tr valign="top">
	<th scope="row"><label for="category_slug"><?php _e( 'Category', 'tcp' );?>:</label></th>
	<td>
		<select id="category_slug" name="category_slug">
			<option value="0"><?php _e( 'no one selected', 'tcp' );?></option>
		<?php $terms = get_terms( 'tcp_product_category', array( 'hide_empty' => true ) );
		foreach( $terms as $term ): ?>
			<option value="<?php echo $term->slug;?>"<?php selected( $cat_slug, $term->slug ); ?>><?php echo esc_attr( $term->name );?></option>
		<?php endforeach; ?>
		</select>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="added_stock"><?php _e( 'Units to add', 'tcp' );?>:</label></th>
	<td>
		<input type="text" name="added_stock" id="added_stock" value="<?php echo $added_stock;?>" sixe="4" maxlength="8"/>
	</td>
	</tr>
	</tbody>
	</table>
	<p class="submit">
		<input type="submit" id="tcp_search" name="tcp_search" class="button-secondary" value="<?php _e('Search') ?>" />
	</p>
	<?php if ( isset( $_REQUEST['tcp_search'] ) && strlen( $cat_slug ) > 0 ) :
		$args = array(
			'post_type'				=> 'tcp_product',
			'tcp_product_category'	=>  $cat_slug ,
		);
		$query = new WP_query( $args );
		if ( $query->have_posts() ) :?>
		<div>
			<h3><?php _e( 'Updated products', 'tcp' );?></h3>
			<table class="widefat fixed" cellspacing="0"><!-- No assigned -->
			<thead>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Actual stock', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'New stock', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Actual stock', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'New stock', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$fix = 0;
			while ( $query->have_posts() ) :
				$post = $query->next_post();
				$stock = tcp_get_the_stock( $post->ID );
				if ( $stock > -1 )
					$new_stock = $stock + $added_stock;
				else
					$new_stock = $stock;
			?>
			<tr>
				<td><?php echo $post->post_title;?></td>
				<td><?php echo $stock;?></td>
				<td><input type="text" value="<?php echo $new_stock;?>" id="tcp_new_stock_<?php echo $post->ID;?>" name="tcp_new_stock_<?php echo $post->ID;?>" size="13" maxlength="13" />
				<input type="button" value="<?php _e( 'no stock', 'tcp' );?>" onclick="jQuery('#tcp_new_stock_<?php echo $post->ID;?>').val(-1);" class="button-secondary" /></td>
				<td>&nbsp;</td>
			</tr>
			<?php endwhile;?>
			</tbody>
			</table>
		</div>
		<?php endif;
		wp_reset_query();?>
	<p class="submit">
		<input type="submit" id="tcp_update_stock" name="tcp_update_stock" class="button-primary" value="<?php _e('Update') ?>" />
	</p>
	<?php endif;?>
</form>