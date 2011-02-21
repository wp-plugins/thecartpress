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

require_once( dirname( dirname( __FILE__ ) ).'/daos/RelEntities.class.php' );

$post_id  = isset( $_REQUEST['post_id'] )  ? $_REQUEST['post_id']  : 0;
$rel_type = isset( $_REQUEST['rel_type'] ) ? $_REQUEST['rel_type'] : '';
$post_type_to = isset( $_REQUEST['post_type_to'] ) ? $_REQUEST['post_type_to'] : 'tcp_product';

$category_slug = isset( $_REQUEST['category_slug'] ) ? $_REQUEST['category_slug'] : false;
$products_type = isset( $_REQUEST['products_type'] ) ? $_REQUEST['products_type'] : false;

if ( isset( $_REQUEST['tcp_create_relation'] ) ) {
	$post_id_to = isset( $_REQUEST['post_id_to'] )	? $_REQUEST['post_id_to'] : 0;
	$units = isset( $_REQUEST['units'] )		? (int)$_REQUEST['units'] : 0;
	if ( $post_id_to > 0 ) {
		RelEntities::insert( $post_id, $post_id_to, $rel_type, 0, $units );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Relation created', 'tcp' );?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_delete_relation'] ) ) {
	$post_id_to = isset( $_REQUEST['post_id_to'] )		? $_REQUEST['post_id_to']	: 0;
	if ( $post_id > 0 ) {
		RelEntities::delete( $post_id, $post_id_to, $rel_type );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Relation deleted', 'tcp' );?>
		</p></div><?php
	}
}
if ( $post_id ) :
	$post = get_post( $post_id );
	if ( $post ) : ?>
<div class="wrap">
	<script>
	function show_delete_relation(id_to) {
		var id = "#div_delete_relation_" + id_to;
		jQuery(".delete_relation").hide();
		jQuery(id).show();
		return false;
	}
	</script>
	<h2><?php echo __( 'Assigned products/post for', 'tcp' );?>&nbsp;<i><?php echo $post->post_title;?></i></h2>
	<ul class="subsubsub">
		<li><a href="post.php?action=edit&post=<?php echo $post_id;?>"><?php _e( 'return to the parent', 'tcp' );?></a></li>
<!--		<li>&nbsp;|&nbsp;</li>
		<li><a href="post-new.php?post_type=<?php echo $post_type_to;?>&tcp_product_parent_id=<?php echo $post_id;?>"><?php _e( 'create new associated item', 'tcp' );?></a></li>-->
	</ul><!-- subsubsub -->
	<div class="clear"></div>
	<table class="widefat fixed" cellspacing="0"><!-- Assigned -->
	<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
		<?php if ($products_type == 'tcp_product' ) :?><th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' );?></th><?php endif;?>
		<th scope="col" class="manage-column"><?php _e( 'Units', 'tcp' );?></th>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
		<th scope="col" class="manage-column">&nbsp;</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
		<?php if ($products_type == 'tcp_product' ) :?><th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' );?></th><?php endif;?>
		<th scope="col" class="manage-column"><?php _e( 'Units', 'tcp' );?></th>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
		<th scope="col" class="manage-column">&nbsp;</th>
	</tr>
	</tfoot>
	<tbody>
	<?php
	$assigned_list = RelEntities::select( $post_id, $rel_type );
	if ( is_array( $assigned_list ) && count( $assigned_list ) > 0 ):
		foreach( $assigned_list as $assigned ) : $assigned_post = get_post( $assigned->id_to );?>
			<tr>
			<td><?php echo $assigned_post->post_title;?></td>
			<?php if ( $products_type == 'tcp_product' ) :?><td><?php echo tcp_get_the_price( $assigned->id_to );?></td><?php endif;?>
			<td><?php echo $assigned->units;?></td>
			<td><?php echo $assigned_post->post_title;?></td>
			<td>
				<a href="post.php?action=edit&post=<?php echo $assigned->id_to;?>"><?php _e( 'edit product', 'tcp' );?></a>
				&nbsp;|&nbsp;
				<a href="#" onclick="return show_delete_relation(<?php echo $assigned->id_to;?>);" class="delete"><?php _e( 'delete relation', 'tcp' );?></a>
				<div class="wrap delete_relation" id="div_delete_relation_<?php echo $assigned->id_to;?>" style="display: none; border: 1px dotted orange; padding: 2px">
					<form method="post" name="frm_delete_relation_<?php echo $assigned->id_to;?>" id="frm_create_relation_<?php echo $assigned_post->id_to;?>">
						<input id="tcp_delete_relation" name="tcp_delete_relation" value="y" type="hidden" />
						<input id="post_id" name="post_id" value="<?php echo $assigned->id_from;?>" type="hidden" />
						<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to;?>" type="hidden" />
						<input id="post_id_to" name="post_id_to" value="<?php echo $assigned->id_to;?>" type="hidden" />
						<input id="rel_type" name="rel_type" value="<?php echo $rel_type;?>" type="hidden" />
						<input id="products_type" name="products_type" value="<?php echo $products_type;?>" type="hidden" />
						<input id="category_slug" name="category_slug" value="<?php echo $category_slug;?>" type="hidden" />
						<p><?php _e( 'Do you really want to delete the relation?', 'tcp' );?></p>
						<a href="javascript:document.frm_delete_relation_<?php echo $assigned->id_to;?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' );?></a> |
						<a href="#" onclick="jQuery('#div_delete_relation_<?php echo $assigned->id_to;?>').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
					</form>
				</div>
			</td>
			</tr>
		<?php endforeach;?>
	<?php else: ?>
		<tr>
		<td colspan="<?php if ( $products_type == 'tcp_product' ) :?>5<?php else:?>4<?php endif;?>"><?php _e( 'No items to show', 'tcp' );?></td>
		</tr>
	<?php endif;?>
	</tbody>
	</table>

	<div class="wrap">
		<form name="frm" id="frm" method="post">
			<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
			<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to;?>" type="hidden" />
			<input id="rel_type" name="rel_type" value="<?php echo $rel_type;?>" type="hidden" />
			<p class="search-box" style="padding-top: 1em;">
				<label for="category_slug"><?php _e( 'Category', 'tcp' );?>:</label>
				<select id="category_slug" name="category_slug">
					<option value="0"><?php _e( 'no one selected', 'tcp' );?></option>
				<?php if ( $post_type_to == 'tcp_product')
					$terms = get_terms( 'tcp_product_category', array( 'hide_empty' => true ) );
				else
					$terms = get_terms( 'category', array( 'hide_empty' => true ) );
				foreach( $terms as $term ): ?>
					<option value="<?php echo $term->slug;?>"<?php selected( $category_slug, $term->slug ); ?>><?php echo esc_attr( $term->name );?></option>
				<?php endforeach; ?>
				</select>
				<?php if ( $post_type_to == 'tcp_product') : ?>
				<label for="products_type">Products type:</label>
				<select id="products_type" name="products_type">
					<option value="">no one</option>
					<option value="SIMPLE" <?php selected( $products_type, 'SIMPLE' ); ?>><?php _e( 'Simple', 'tcp' );?></option>
					<option value="GROUPED" <?php selected( $products_type, 'GROUPED' ); ?>><?php _e( 'Grouped', 'tcp' );?></option>
				</select>
				<?php endif;?>
				<input id="tcp_filter_products_type" name="tcp_filter_products_type" value="filter" type="submit">
			</p><!-- search-box -->
		</form>
	</div><!-- wrap -->

	<table class="widefat fixed" cellspacing="0"><!-- No assigned -->
	<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
		<th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' );?></th>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
		<th scope="col" class="manage-column">&nbsp;</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
		<th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' );?></th>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
		<th scope="col" class="manage-column">&nbsp;</th>
	</tr>
	</tfoot>
	<tbody>
	<?php
	if ( ( $post_type_to == 'tcp_product' && $category_slug && $products_type ) || ( $post_type_to == 'post' && $category_slug ) ) :
		$ids = array();
		$ids[] = $post_id;
		foreach( $assigned_list as $assigned )
				$ids[] = $assigned->id_to;
		$args = array (
			'post_type'				=> $post_type_to,
			'post__not_in'			=> $ids,
		);
		if ( $post_type_to == 'tcp_product' ) {
			$args['meta_key'] = 'tcp_type';
			$args['meta_value'] = $products_type;
			$args['tcp_product_category'] = $category_slug;
		} else {
			$args['cat_in'] = array( $category_slug );
		}

		$query = new WP_query( $args );
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();?>
				<tr>
				<td><?php the_title();?></td>
				<td><?php tcp_the_price();?></td>
				<td><?php the_excerpt();?></td>
				<td>
				<div class="wrap">
					<form method="post" name="frm_create_relation_<?php the_ID();?>" id="frm_create_relation_<?php the_ID();?>">
						<a href="post.php?action=edit&post=<?php the_ID();?>"><?php _e( 'edit product', 'tcp' );?></a>
						<input id="tcp_create_relation" name="tcp_create_relation" value="y" type="hidden" />
						<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
						<input id="post_id_to" name="post_id_to" value="<?php the_ID();?>" type="hidden" />
						<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to;?>" type="hidden" />
						<input id="rel_type" name="rel_type" value="<?php echo $rel_type;?>" type="hidden" />
						<input id="products_type" name="products_type" value="<?php echo $products_type;?>" type="hidden" />
						<input id="category_slug" name="category_slug" value="<?php echo $category_slug;?>" type="hidden" />
						| <label for="units"><?php _e( 'units', 'tcp' );?>:</label>
						<input id="units" name="units" value="1" size="2" maxlength="3" type="text" /><a href="javascript:document.frm_create_relation_<?php the_ID();?>.submit();"><?php _e( 'assign' , 'tcp' );?></a>
					</form>
				</div>
				</td>
				</tr>
			<?php endwhile;?>
		<?php else: ?>
			<tr>
			<td colspan="4"><?php _e( 'No items to show', 'tcp' );?></td>
			</tr>
		<?php endif;?>
	<?php else: ?>
		<tr>
		<td colspan="4"><?php _e( 'No items to show', 'tcp' );?></td>
		</tr>
	<?php endif;?>
	</tbody>
	</table>
</div><!-- wrap -->
	<?php endif;?>
<?php endif;?>
