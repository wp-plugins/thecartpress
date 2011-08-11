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
<h2><?php _e( 'Post types', 'wp_taxo' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>PostTypeEdit.php"><?php _e( 'create new post type', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name Id', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name Id', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>
<?php
$posttypes = get_option( 'tcp-posttypes-generator' );
if ( is_array( $posttypes ) && count( $posttypes ) > 0 ) :
	if ( isset( $_REQUEST['tcp_delete_posttype'] ) && isset( $_REQUEST['posttype_id'] )  && isset( $posttypes[$_REQUEST['posttype_id']] ) ) {
		unset( $posttypes[$_REQUEST['posttype_id']] );
		update_option( 'tcp-posttypes-generator', $posttypes );
	}
	foreach( $posttypes as $posttype_id => $posttype ) :?>
<tr>
	<td><?php echo $posttype['name'];?></td>
	<td><?php echo $posttype['name_id'];?></td>
	<td><?php echo $posttype['desc'];?></td>
	<td><?php $posttype['activate'] ? _e( 'Activated', 'tcp' ) : _e( 'No Activated', 'tcp' );?></td>
	<td><a href="<?php echo $admin_path?>PostTypeEdit.php&posttype_id=<?php echo $posttype_id;?>"><?php _e( 'edit', 'tcp' );?></a>
	 | <a href="#" onclick="jQuery('.delete_posttype').hide();jQuery('#delete_<?php echo $posttype_id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a></div>
		<div id="delete_<?php echo $posttype_id;?>" class="delete_posttype" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_delete_<?php echo $posttype_id;?>">
			<input type="hidden" name="posttype_id" value="<?php echo $posttype_id;?>" />
			<input type="hidden" name="tcp_delete_posttype" value="y" />
			<p><?php _e( 'Do you really want to delete this post type?', 'tcp' );?></p>
			<a href="javascript:document.frm_delete_<?php echo $posttype_id;?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' );?></a> |
			<a href="#" onclick="jQuery('#delete_<?php echo $posttype_id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
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
