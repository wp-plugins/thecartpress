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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Addresses.class.php' );
 
$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';

if ( is_user_logged_in() ) {
	global $current_user;
	get_currentuserinfo();
	if ( isset( $_REQUEST['tcp_delete_address'] ) ) {
		$address_id = isset( $_REQUEST['address_id'] ) ? $_REQUEST['address_id'] : 0;
		if ( $address_id > 0 &&	Addresses::isOwner( $address_id, $current_user->ID ) ) {
			Addresses::delete( $address_id );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Address deleted', 'tcp' );?>
		</p></div><?php
		}
	}
	$addresses = Addresses::getCustomerAddresses( $current_user->ID );
} else
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
?>
<div class="wrap">

<h2><?php echo __( 'List of addresses', 'tcp' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>AddressEdit.php"><?php _e( 'create new address', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Address', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Street', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Default', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Address', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Street', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Default', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>

<?php if ( count( $addresses ) == 0 ) :?>
	<tr><td colspan="5"><?php _e( 'The list of addresses is empty', 'tcp' );?></td></tr>
<?php else :
	 foreach( $addresses as $address ) :?> 
	<tr>
		<td><?php echo $address->name;?></td>
		<td><?php echo $address->firstname, ' ', $address->lastname;?></td>
		<td><?php echo $address->street, ' ', $address->city, ' (', $address->region, ')';?></td>
		<?php if ( $address->default_shipping == 'Y' ) $default = __( 'Shipping', 'tcp' );
		else $default = '';
		if ( $address->default_billing == 'Y' ) $default .= ' ' . __( 'Billing', 'tcp' );?>
		<td><?php echo $default;?></td>
		<td style="width: 20%;">
		<div><a href="<?php echo $admin_path;?>AddressEdit.php&address_id=<?php echo $address->address_id;?>"><?php _e( 'edit', 'tcp' );?></a> | <a href="#" onclick="jQuery('.delete_address').hide();jQuery('#delete_<?php echo $address->address_id;?>').show();" class="delete"><?php _e( 'delete', 'tcp' );?></a></div>
		<div id="delete_<?php echo $address->address_id;?>" class="delete_address" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post" name="frm_delete_<?php echo $address->address_id;?>">
			<input type="hidden" name="address_id" value="<?php echo $address->address_id;?>" />
			<input type="hidden" name="tcp_delete_address" value="y" />
			<p><?php _e( 'Do you really want to delete this address?', 'tcp' );?></p>
			<a href="javascript:document.frm_delete_<?php echo $address->address_id;?>.submit();" class="delete"><?php _e( 'Yes' , 'tcp' );?></a> |
			<a href="#" onclick="jQuery('#delete_<?php echo $address->address_id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
			</form>
		</div>
		</td>
	</tr>
	<?php endforeach;
endif;?>
</tbody>
</table>

</div> <!-- end wrap -->