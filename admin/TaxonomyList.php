<?php
/**
 * This file is part of wp-taxonomy-engine.
 * 
 * wp-taxonomy-engine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wp-taxonomy-engine is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wp-taxonomy-engine.  If not, see <http://www.gnu.org/licenses/>.
 */

$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
?>
<div class="wrap">
<h2><?php _e( 'Taxonomies', 'wp_taxo' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>TaxonomyEdit.php"><?php _e( 'create new taxonomy', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Post type', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name Id', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Post type', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name Id', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>
<?php
$taxonomies = get_option( 'tcp-taxonomies-generator' );
if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) :
	if ( isset( $_REQUEST['tcp_delete_taxonomy'] ) && isset( $_REQUEST['taxonomy_id'] )  && isset( $taxonomies[$_REQUEST['taxonomy_id']] ) ) {
		unset( $taxonomies[$_REQUEST['taxonomy_id']] );
		update_option( 'tcp-taxonomies-generator', $taxonomies );
	}
	foreach( $taxonomies as $taxonomy_id => $taxonomy ) :?>
<tr>
	<td><?php echo $taxonomy['post_type'];?></td>
	<td><?php echo $taxonomy['name'];?></td>
	<td><?php echo $taxonomy['name_id'];?></td>
	<td><?php echo $taxonomy['desc'];?>&nbsp;</td>
	<td><?php $taxonomy['activate'] ? _e( 'Activated', 'tcp' ) : _e( 'No Activated', 'tcp' );?></td>
	<td><a href="<?php echo $admin_path?>TaxonomyEdit.php&taxonomy_id=<?php echo $taxonomy_id;?>"><?php _e( 'edit', 'tcp' );?></a>
	 | <a href="#" onclick="jQuery('.delete_taxonomy').hide();jQuery('#delete_<?php echo $taxonomy_id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a></div>
		<div id="delete_<?php echo $taxonomy_id;?>" class="delete_taxonomy" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_delete_<?php echo $taxonomy_id;?>">
			<input type="hidden" name="taxonomy_id" value="<?php echo $taxonomy_id;?>" />
			<input type="hidden" name="tcp_delete_taxonomy" value="y" />
			<p><?php _e( 'Do you really want to delete this taxonomy?', 'tcp' );?></p>
			<a href="javascript:document.frm_delete_<?php echo $taxonomy_id;?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' );?></a> |
			<a href="#" onclick="jQuery('#delete_<?php echo $taxonomy_id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
			</form>
	</td>
</tr>
	<?php endforeach;?>
<?php else : ?>
<tr>
	<td colspan="3"><?php _e( 'The list is empty', 'tcp' );?></td>
</tr>
<?php endif;?>
</tbody>
</table>

</div>
