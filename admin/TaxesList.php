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

require_once( dirname( dirname( __FILE__ ) ).'/daos/Taxes.class.php' );

$tax_id = isset( $_REQUEST['tax_id'] ) ? $_REQUEST['tax_id'] : 0;
if ( isset( $_REQUEST['tcp_edit_tax'] ) ) {
	$error_tax = array();
	if ( ! isset( $_REQUEST['title'] ) && strlen( trim( $_REQUEST ['title'] ) ) == 0 )
		$error_tax['title'][] = __( 'The "title" field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['tax'] ) && strlen( trim( $_REQUEST ['tax'] ) ) == 0 && is_numeric( $_REQUEST ['tax'] ) )
		$error_tax['tax'][] = __( 'The "title" field must be a number and must be completed', 'tcp' );
	if ( count( $error_tax ) == 0 ) {
		if ( $tax_id > 0 ) {
			$new_tax = (int)$_REQUEST['tax'];
			$new_tax_label = $_REQUEST['title'];
			global $wpdb;
			$res = $wpdb->get_results( $wpdb->prepare( 'select post_id from ' . $wpdb->postmeta . ' where meta_key = \'tcp_tax_id\' and meta_value = %s', $tax_id ) );
			$ids = array();
			foreach( $res as $row )
				$ids[] = $row->post_id;
			$ids = implode( ',', $ids );
			if ( strlen( $ids ) > 0 ) {
				$wpdb->query( $wpdb->prepare( 'update ' . $wpdb->postmeta . ' set meta_value = %s
					where meta_key = \'tcp_tax\' and post_id in (' . $ids .')', $new_tax ) );
				$wpdb->query( $wpdb->prepare( 'update ' . $wpdb->postmeta . ' set meta_value = %s
					where meta_key = \'tcp_tax_label\' and post_id in (' . $ids .')', $new_tax_label ) );
			}
		}
		Taxes::save( $_REQUEST );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Tax saved', 'tcp' );?>
		</p></div><?php		
	}
} elseif ( isset( $_REQUEST['tcp_delete_tax'] ) ) {
	if ( $tax_id > 0 )
		if ( $tax_id > 0 ) {
			$old_tax = Taxes::get( $tax_id );
			Taxes::delete( $tax_id );
			global $wpdb;
			$res = $wpdb->get_results( $wpdb->prepare( 'select post_id from ' . $wpdb->postmeta . ' where meta_key = \'tcp_tax_id\' and meta_value = %s', $tax_id ) );
			$ids = array();
			foreach( $res as $row )
				$ids[] = $row->post_id;
			$ids = implode( ',', $ids );
			if ( strlen( $ids ) > 0 ) {
				$wpdb->query( 'update ' . $wpdb->postmeta . ' set meta_value = \'0\' where meta_key = \'tcp_tax\' and post_id in (' . $ids .')' );
				$wpdb->query( 'update ' . $wpdb->postmeta . ' set meta_value = \'\' where meta_key = \'tcp_tax_label\' and post_id in (' . $ids . ')' );
			}?>
		<div id="message" class="updated"><p>
			<?php _e( 'Tax deleted', 'tcp' );?>
		</p></div><?php
		}
}
$taxes = Taxes::getAll();
?>
<div class="wrap">

<h2><?php echo __( 'List of taxes', 'tcp' );?></h2>
<ul class="subsubsub">
</ul>
<div class="clear"></div>

<?php if ( isset( $error_tax ) && count( $error_tax ) > 0 ) : ?>
<p class="error">
	<?php foreach( $error_tax as $error ) :?>
		<span class="description"><?php echo $error[0];?></span><br />
	<?php endforeach;?>
</p>
<?php endif;?>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'tax', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Title', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'tax', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tfoot>
<tbody>
	<tr><td colspan="2">
		<a href="#" onclick="jQuery('.edit_tax').hide();jQuery('.delete_tax').hide();jQuery('#edit_0').show();"><?php _e( 'create new tax', 'tcp' );?></a>
		<div id="edit_0" href="#" class="edit_tax" style="display:none; width: 50%;border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_edit_<?php echo $tax->tax_id;?>">
			<input type="hidden" name="tax_id" value="0" />
			<input type="hidden" name="tcp_edit_tax" value="y" />
			<h3><?php _e( 'New tax', 'tcp' );?></h3>
			<p>
				<label for="title"><?php _e( 'Title', 'tcp' );?>:</label>
				<input type="text" id="title" name="title" size="40" maxlength="200" value=""/>
			</p><p>
				<label for="tax"><?php _e( 'Tax', 'tcp' );?>:</label>
				<input type="text" id="tax" name="tax" size="3" maxlength="3" value=""/>
			</p>
			<p>
			<input name="tcp_edit_tax" value="<?php _e( 'Save', 'tcp' );?>" type="submit" class="button-secondary" />
			&nbsp;<a href="#" onclick="jQuery('#edit_0').hide();"><?php _e( 'Cancel' , 'tcp' );?></a>
			</p>
			</form>
		</div>		
	</td><td>&nbsp;</td></tr>

<?php if ( count( $taxes ) == 0 ) :?>
	<tr><td colspan="3"><?php _e( 'The list of taxes is empty', 'tcp' );?></td></tr>
<?php else :?>
	 <?php foreach( $taxes as $tax ) :?> 
	<tr>
		<td><?php echo $tax->title;?></td>
		<td><?php echo $tax->tax;?>%</td>
		<td style="width: 20%;">
		<div><a href="#" onclick="jQuery('.edit_tax').hide();jQuery('.delete_tax').hide();jQuery('#edit_<?php echo $tax->tax_id;?>').show();" class="edit"><?php _e( 'edit', 'tcp' );?></a> | <a href="#" onclick="jQuery('.delete_tax').hide();jQuery('.edit_tax').hide();jQuery('#delete_<?php echo $tax->tax_id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a></div>
		
		<div id="edit_<?php echo $tax->tax_id;?>" class="edit_tax" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_edit_<?php echo $tax->tax_id;?>">
			<input type="hidden" name="tax_id" value="<?php echo $tax->tax_id;?>" />
			<input type="hidden" name="tcp_edit_tax" value="y" />
			<p>
				<label for="title"><?php _e( 'Title', 'tcp' );?></label>
				<input type="text" id="title" name="title" size="40" maxlength="200" value="<?php echo $tax->title;?>"/>
			</p><p>
				<label for="tax"><?php _e( 'Tax', 'tcp' );?></label>
				<input type="text" id="tax" name="tax" size="3" maxlength="3" value="<?php echo $tax->tax;?>"/>
			</p>
			<p>
			<input name="tcp_edit_tax" value="<?php _e( 'Save', 'tcp' );?>" type="submit" class="button-secondary" />
			&nbsp;<a href="#" onclick="jQuery('#edit_<?php echo $tax->tax_id;?>').hide();"><?php _e( 'Cancel' , 'tcp' );?></a>
			</p>
			</form>
		</div>		

		<div id="delete_<?php echo $tax->tax_id;?>" class="delete_tax" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_delete_<?php echo $tax->tax_id;?>">
			<input type="hidden" name="tax_id" value="<?php echo $tax->tax_id;?>" />
			<input type="hidden" name="tcp_delete_tax" value="y" />
			<p><?php _e( 'Do you really want to delete this tax?', 'tcp' );?></p>
			<a href="javascript:document.frm_delete_<?php echo $tax->tax_id;?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' );?></a> |
			<a href="#" onclick="jQuery('#delete_<?php echo $tax->tax_id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
			</form>
		</div>
		</td>
	</tr>
	<?php endforeach;
endif;?>
</tbody>
</table>

</div> <!-- end wrap -->