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

require_once( dirname(dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );

class OptionsMetabox {
	function registerMetaBox() {
		$saleable_post_types = tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-product-options', __( 'Products options', 'tcp' ), array( $this, 'show' ), $post_type, 'normal', 'high' );
	}

	function show() {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return;
		$post_id = tcp_get_default_id( $post->ID, $post->post_type );
		?>
<table class="widefat fixed">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Option', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Price and order', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Option', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Price and order', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;">&nbsp;</th>
</tr>
</tfoot>

<tbody>

<?php $options = RelEntities::select( $post_id, 'OPTIONS' );
if ( is_array( $options ) && count( $options ) > 0 ) :
	foreach( $options as $i => $option ) : $post = get_post( $option->id_to ); 
		if ( $post ) : ?>
	<tr>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo tcp_get_the_thumbnail( $post_id, $post->ID, 0, array( '50', '50' ) ); ?></a></td>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo $post->post_title;?></a></td>
		<td><?php echo $post->post_content;?></td>
		<td><?php echo $post->post_status;?></td>
		<td>
			<?php echo tcp_format_the_price( tcp_get_the_price( $post->ID ) ); ?>
			&nbsp;<label><?php echo _x( 'Order', 'to sort lists', 'tcp' );?>:</label>
			&nbsp;<?php echo tcp_get_the_order( $post->ID );?>
		</td>
		<td>
			<a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>"><?php _e( 'edit option', 'tcp' );?></a>
		</td>
	</tr>
		<?php $options_2 = RelEntities::select( $option->id_to, 'OPTIONS' );
		foreach( $options_2 as $j => $option_2 ) : $post_2 = get_post( $option_2->id_to ); ?>
	<tr>
		<td style="padding-left: 2em;"><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo tcp_get_the_thumbnail( $post_id, $option->id_to, $post_2->ID, array( '50', '50' ) ); ?></a></td>
		<td><span style="padding-left: 2em;">&nbsp;</span><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo $post_2->post_title;?></a></td>
		<td><?php echo $post_2->post_content;?></td>
		<td><?php echo $post_2->post_status;?></td>
		<td>
			<?php echo tcp_format_the_price( tcp_get_the_price( $post_2->ID ) );?>
			&nbsp;<label><?php echo _x( 'Order', 'to sort lists', 'tcp' );?>:&nbsp;</label><?php echo tcp_get_the_order( $post_2->ID );?>
		</td>
		<td>
			<a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>"><?php _e( 'edit option', 'tcp' );?></a>
		</td>
	</tr>
		<?php endforeach;?>
		<?php endif;?>
	<?php endforeach;?>
<?php else:?>
	<tr>
		<td colspan="6"><?php _e( 'The options list is empty', 'tcp' )?></td>
	</tr>
<?php endif;?>
</tbody>
</table><?php
	}
}
?>
