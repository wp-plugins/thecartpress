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

$per = isset( $_REQUEST['per'] ) ? (int)$_REQUEST['per'] : 0;
$fix = isset( $_REQUEST['fix'] ) ? (int)$_REQUEST['fix'] : 0;
$update_type = isset( $_REQUEST['update_type'] ) ? $_REQUEST['update_type'] : 'per';
$cat_slug = isset( $_REQUEST['category_slug'] ) ? $_REQUEST['category_slug'] : '';

if ( isset( $_REQUEST['tcp_update_price'] ) ) {
	$args = array(
		'post_type'				=> 'tcp_product',
		'tcp_product_category'	=>  $cat_slug ,
		'posts_per_page'		=> -1,
	);
	$args = apply_filters( 'tcp_update_price_query_args', $args );
	$query = new WP_query( $args );
	if ( $query->have_posts() ) {
		$current_user = wp_get_current_user();
		while ( $query->have_posts() ) {
			$post = $query->next_post();
			if ( ! current_user_can( 'tcp_edit_others_products' ) )
				if ( $post->post_author != $current_user->ID) {
					die( __( 'This product cannot be modified by the user ', 'tcp' ) );
				}
			if ( isset( $_REQUEST['tcp_new_price_' . $post->ID] ) ) {
				//$new_price = (float)$_REQUEST['tcp_new_price_' . $post->ID];
				$new_price = $_REQUEST['tcp_new_price_' . $post->ID];
				$new_price = tcp_input_number( $new_price );
				update_post_meta( $post->ID, 'tcp_price', $new_price );
			}
			do_action( 'tcp_update_price', $post );
		}?>
		<div id="message" class="updated"><p>
			<?php _e( 'Updated price.', 'tcp' );?>
		</p></div><?php
	}
	wp_reset_query();
}
?>
<div class="wrap">
<h2><?php _e( 'Prices Update', 'tcp' );?></h2>
<div class="clear"></div>

<form method="post">
	<table class="form-table">
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
	<th scope="row"><label for="by_category_per"><?php _e( 'New price', 'tcp' );?>:</label></th>
	<td>
		<input type="radio" id="by_category_per" name="update_type"
			onclick="if (this.checked) {jQuery('#div_per').show();jQuery('#div_fix').hide();}"
			value="per" <?php checked( $update_type, 'per' );?>/>
		<label for="by_category_per"><?php _e( 'percentage', 'tcp' );?></label>
		<span id="div_per"<?php if ( $update_type != 'per' ) : ?> style="display:none;"<?php endif;?>>&nbsp;<input type="text" name="per" value="<?php echo $per;?>" size="5" maxlength="5" />&nbsp;&#37;</span>
		<br />
		<input type="radio" id="by_category_fix" name="update_type"
			onclick="if (this.checked) {jQuery('#div_per').hide();jQuery('#div_fix').show();}"
			value="fix" <?php checked( $update_type, 'fix' );?> />
		<label for="by_category_fix"><?php _e( 'fix value', 'tcp' );?></label>
		<span id="div_fix"<?php if ( $update_type != 'fix' ) : ?> style="display:none;"<?php endif;?>>&nbsp;<input type="text" name="fix" value="<?php echo $fix;?>" size="5" maxlength="5" /><?php tcp_the_currency();?></span>
	</td>
	</tr>
	<?php do_action( 'tcp_update_price_search_controls' ); ?>
	</tbody>
	</table>
	<p class="submit">
		<input type="submit" id="tcp_search" name="tcp_search" class="button-secondary" value="<?php _e('Search') ?>" />
	</p>
	<?php if ( isset( $_REQUEST['tcp_search'] ) && strlen( $cat_slug ) > 0 ) :
		$args = array(
			'post_type'				=> 'tcp_product',
			'tcp_product_category'	=>  $cat_slug ,
			'posts_per_page'		=> -1,
		);
		$args = apply_filters( 'tcp_update_price_query_args', $args );
		$query = new WP_query( $args );
		if ( $query->have_posts() ) :?>
		<div>
			<h3><?php _e( 'Updated products', 'tcp' );?></h3>
			<table class="widefat fixed" cellspacing="0"><!-- No assigned -->
			<thead>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Actual price', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'New price', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Actual price', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'New price', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</tfoot>
			<tbody>
			<?php while ( $query->have_posts() ) :
				$post = $query->next_post();
				$price = tcp_get_the_price( $post->ID );
				if ( $update_type == 'per' ) {
					$new_price = $price * (1 + $per / 100);
				} else { //fixed
					$new_price = $price + $fix;
				}?>
			<tr>
				<td><a href="post.php?action=edit&post=<?php echo $post->ID;?>"><?php echo $post->post_title;?></a></td>
				<td><?php echo tcp_format_the_price( $price );?></td>
				<td><input type="text" value="<?php echo tcp_number_format( $new_price );?>" name="tcp_new_price_<?php echo $post->ID;?>" size="13" maxlength="13" /> <?php tcp_the_currency();?></td>
				<td>&nbsp;</td>
			</tr>
			<?php do_action( 'tcp_update_price_controls', $post );
			endwhile;?>
			</tbody>
			</table>
		</div>
		<?php endif;
		wp_reset_query();?>
	<p class="submit">
		<input type="submit" id="tcp_update_price" name="tcp_update_price" class="button-primary" value="<?php _e('Update') ?>" />
	</p>
	<?php endif;?>
</form>
