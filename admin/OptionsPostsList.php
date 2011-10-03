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

$post_type	= isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'tcp_product';
$taxonomy	= isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : 'tcp_product_category';
$cat_slug	= isset( $_REQUEST['category_slug'] ) ? $_REQUEST['category_slug'] : '';

if ( isset( $_REQUEST['tcp_options_posts_update'] ) ) {
	$post_ids = $_REQUEST['tcp_post_ids'];
	if ( is_array( $post_ids ) && count( $post_ids ) )
		foreach( $post_ids as $post_id ) {
			$copy_from = $_REQUEST['tcp_name_post_' . $post_id];
			$no_create_option = isset( $_REQUEST['tcp_no_create_option_' . $post_id] );
			echo $post_id, '->', $copy_from, ', ' , $no_create_option, '<br><br>';
		}
}
?>

<div class="wrap">
<h2><?php _e( 'Options Posts Update', 'tcp' );?></h2>
<div class="clear"></div>

<form method="post">
	<p class="search-box">
	<label for="post_type"><?php _e( 'Post type', 'tcp' )?>:</label>

	<select name="post_type" id="post_type">
	<?php foreach( tcp_get_saleable_post_types() as $pt ) :?>
		<option value="<?php echo $pt;?>"<?php selected( $post_type, $pt ); ?>><?php echo $pt;?></option>
	<?php endforeach;?>
	</select>
	<input type="submit" name="tcp_load_taxonomies" value="<?php _e( 'Load taxonomies', 'tcp' );?>" class="button-secondary"/>

	<label for="taxonomy"><?php _e( 'Taxonomy', 'tcp' )?>:</label>
	<select name="taxonomy" id="taxonomy">
	<?php foreach( get_object_taxonomies( $post_type ) as $taxmy ) : $tax = get_taxonomy( $taxmy ); ?>
		<option value="<?php echo esc_attr( $taxmy );?>"<?php selected( $taxmy, $taxonomy ); ?>><?php echo $tax->labels->name;?></option>
	<?php endforeach;?>
	</select>
	<input type="submit" name="tcp_load_terms" value="<?php _e( 'Load categories', 'tcp' );?>" class="button-secondary"/>
	<label for="category_slug"><?php _e( 'Category', 'tcp' );?>:</label>
	<select id="category_slug" name="category_slug">
		<option value="0"><?php _e( 'no one selected', 'tcp' );?></option>
	<?php $terms = get_terms( $taxonomy, array( 'hide_empty' => true ) );
	foreach( $terms as $term ): ?>
		<option value="<?php echo $term->slug;?>"<?php selected( $cat_slug, $term->slug ); ?>><?php echo esc_attr( $term->name );?></option>
	<?php endforeach; ?>
	</select>
	<?php do_action( 'tcp_options_posts_search_controls' ); ?>
	<input type="submit" name="tcp_search" value="<?php _e( 'Search', 'tcp' );?>" class="button-secondary"/>	
	</p>
	<?php if ( isset( $_REQUEST['tcp_search'] ) && strlen( $cat_slug ) > 0 ) :
		$args = array(
			'post_type'			=> $post_type,
			$taxonomy			=> $cat_slug ,
			'posts_per_page'	=> 1000,//TODO
		);
		$args = apply_filters( 'tcp_options_posts_query_args', $args );
		$posts = array();
		$posts_with_options = array();
		$query = new WP_query( $args );
		if ( $query->have_posts() )
			while ( $query->have_posts() ) {
				$post = $query->next_post();
				$posts[$post->ID] = array( 'title' => $post->post_title, 'label' => tcp_get_the_meta( 'tcp_back_end_label', $post->ID ) );
				if ( RelEntities::count( $post->ID, 'OPTIONS' ) > 0 ) {
					$posts_with_options[$post->ID] = array( 'title' => $post->post_title, 'label' => tcp_get_the_meta( 'tcp_back_end_label', $post->ID ) );
				}
			}
		wp_reset_query();
		if ( is_array( $posts_with_options ) && count( $posts_with_options ) ) {
			$html = '<select name="tcp_name_post_%s" id="tcp_name_post_%s" class="tcp_select_post">';
			$html .= '<option value="" select="true">' . __( 'No change', 'tcp' ) . '</option>';
			foreach ( $posts_with_options as $id => $post ) {
				$html .= '<option value="' . $id . '">' . $post['title'] . '</option>';
			}
			$html .= '</select>';
		}
		$first_row = true;
		if ( is_array( $posts ) && count( $posts ) ) :?>
		<script>
			function copy_to_all(id) {
				var s = jQuery('#tcp_name_post_' + id).val();
				jQuery('.tcp_select_post').each(function(i, select) {
					jQuery(select).val(s);
				});
				var s = jQuery('#tcp_no_create_option_' + id).attr('checked');
				jQuery('.tcp_checkbox_post').each(function(i, select) {
					if (s) {
						jQuery(select).attr('checked', true);
					} else {
						jQuery(select).removeAttr('checked');
					}
				});
			}
		</script>
		<div>
			<h3><?php _e( 'Updated', 'tcp' );?></h3>
			<p class="submit">
				<input type="submit" id="tcp_options_posts_update" name="tcp_options_posts_update" class="button-primary" value="<?php _e( 'Update' ) ?>" />
			</p>
			<table class="widefat fixed" cellspacing="0"><!-- No assigned -->
			<thead>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ( $posts as $id => $post ) : ?>
			<tr>
				<td><a href="post.php?action=edit&post=<?php echo $id;?>"><?php echo $post['title'];?></a></td>
				<td><?php echo $post['label'];?></td>
				<td>
					<input type="hidden" name="tcp_post_ids[]" value="<?php echo $id;?>"/>
					<?php printf( $html, $id, $id );?>
					<label for="tcp_create_option"><?php _e( 'No create options', 'tcp' );?></label>:
					<input type="checkbox" name="tcp_no_create_option_<?php echo $id;?>" id="tcp_no_create_option_<?php echo $id;?>" value="yes" class="tcp_checkbox_post"/>
					<?php if ( $first_row ) : $first_row = false;?>
						<input type="button" class="button-secondary" value="<?php _e( 'Copy to all', 'tcp' );?>" onclick="copy_to_all(<?php echo $id;?>);"/>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach;?>
			</tbody>
			</table>
		</div>
		<?php else : ?>
		<p><?php _e( 'No products to update', 'tcp' );?></p>
		<?php endif;?>
	<p class="submit">
		<input type="submit" id="tcp_options_posts_update" name="tcp_options_posts_update" class="button-primary" value="<?php _e( 'Update' ) ?>" />
	</p>
	<?php else : ?>
	<p><?php _e( 'First, you must perform a search:', 'tcp' );?></p>
	<?php endif;?>
</form>
