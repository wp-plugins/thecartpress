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
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Countries.class.php' );

$address_id = isset( $_REQUEST['address_id'] ) ? $_REQUEST['address_id'] : '0';

global $current_user;
get_currentuserinfo();
$customer_id = $current_user->ID;
if ( $address_id > 0 && $customer_id > 0 && ! Addresses::isOwner( $address_id, $current_user->ID ) )
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
$error_address = array();
	
if ( isset( $_REQUEST['tcp_save_address'] ) ) {
	if ( ! isset( $_REQUEST['name'] ) || strlen( $_REQUEST['name'] ) == 0 )
		$error_address['name'][] = __( 'The name field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['firstname'] ) || strlen( $_REQUEST['firstname'] ) == 0 )
		$error_address['firstname'][] = __( 'The firstname field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['lastname'] ) || strlen( $_REQUEST['lastname'] ) == 0 )
		$error_address['lastname'][] = __( 'The lastname field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['street'] ) || strlen( $_REQUEST['street'] ) == 0 )
		$error_address['street'][] = __( 'The street field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['city'] ) || strlen( $_REQUEST['city'] ) == 0)
		$error_address['city'][] = __( 'The city field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['region'] ) || strlen( $_REQUEST['region'] ) == 0 )
		$error_address['region'][] = __( 'The region field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['postcode'] ) || strlen( $_REQUEST['postcode'] ) == 0 )
		$error_address['postcode'][] = __( 'The postcode field must be completed', 'tcp' );
	if ( ! isset( $_REQUEST['email'] ) || strlen( $_REQUEST['email'] ) == 0 )
		$error_address['email'][] = __( 'The email field must be completed', 'tcp' );
	$has_validation_error = count( $error_address ) > 0;

	if ( ! $has_validation_error ) {
		$_REQUEST['customer_id'] = $customer_id;
		$_REQUEST['city_id'] = 0;
		$_REQUEST['region_id'] = 0;
		if ( ! isset( $_REQUEST['default_billing'] ) ) $_REQUEST['default_billing'] = '';
		if ( ! isset( $_REQUEST['default_shipping'] ) ) $_REQUEST['default_shipping'] = '';
		Addresses::save( $_REQUEST );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Address saved', 'tcp' );?>
		</p></div><?php
	} else {?>
		<div id="message" class="error"><p>
			<?php _e( 'Validation errors. The Address has not been saved', 'tcp' );?>
		</p></div><?php
	}
} elseif ( $address_id > 0 ) {
	$address = Addresses::get( $address_id );
}
$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';

function tcp_show_error_msg( $error_array, $id ) {
	if ( isset( $error_array[$id] ) )
		//foreach($error_shipping[$id] )
		echo '<span class="description">', $error_array[$id][0], '</span>';
}

function tcp_get_value( $id, $echo = true ) {
	$res = '';
	if ( isset( $_REQUEST[$id] ) )
		$res = $_REQUEST[$id];
	else {
		global $address;
		if ( isset( $address->$id ) )
			$res = $address->$id;
	}
	if ( $echo )
		echo $res;
	else
		return $res;
}
?>

<div class="wrap">
<h2><?php _e( 'Address', 'tcp' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>AddressesList.php"><?php _e( 'return to the list', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<form method="post">
	<input type="hidden" name="address_id" value="<?php echo $address_id;?>" />
	<table class="form-table">
	<tr valign="top">
	<th scope="row"><label for="name"><?php _e( 'Address name', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="name" name="name" value="<?php tcp_get_value( 'name' );?>" size="20" maxlength="50" />
		<?php tcp_show_error_msg( $error_address, 'name' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="firstname"><?php _e( 'Firstname', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="firstname" name="firstname" value="<?php tcp_get_value( 'firstname' );?>" size="20" maxlength="50" />
		<?php tcp_show_error_msg( $error_address, 'firstname' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="lastname"><?php _e( 'Lastname', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="lastname" name="lastname" value="<?php tcp_get_value( 'lastname' );?>" size="40" maxlength="100" />
		<?php tcp_show_error_msg( $error_address, 'lastname' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="company"><?php _e( 'Company', 'tcp' );?>:</label></th>
	<td>
		<input type="text" id="company" name="company" value="<?php tcp_get_value( 'company' );?>" size="20" maxlength="50" />
		<?php tcp_show_error_msg( $error_address, 'company' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="street"><?php _e( 'Address', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="street" name="street" value="<?php tcp_get_value( 'street' );?>" size="20" maxlength="50" />
		<?php tcp_show_error_msg( $error_address, 'street' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="city"><?php _e( 'City', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="city" name="city" value="<?php tcp_get_value( 'city' );?>" size="20" maxlength="50" />
		<?php tcp_show_error_msg( $error_address, 'city' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="region"><?php _e( 'Region', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="region" name="region" value="<?php tcp_get_value( 'region' );?>" size="20" maxlength="50" />
		<?php tcp_show_error_msg( $error_address, 'region' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="postcode"><?php _e( 'Postal code', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="postcode" name="postcode" value="<?php tcp_get_value( 'postcode' );?>" size="7" maxlength="7" />
		<?php tcp_show_error_msg( $error_address, 'postcode' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="country_id"><?php _e( 'Country', 'tcp' );?>:</label></th>
	<td>
		<select id="country_id" name="country_id">
		<?php $countries = Countries::getAll();
		foreach($countries as $country) :?>
			<option value="<?php echo $country->iso;?>" <?php selected( $country->iso, tcp_get_value( 'country_id', false ) )?>><?php echo $country->name;?></option>
		<?php endforeach;?>
		</select>
		<?php tcp_show_error_msg( $error_address, 'country_id' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="telephone_1"><?php _e( 'Telephone 1', 'tcp' );?>:</label></th>
	<td>
		<input type="text" id="telephone_1" name="telephone_1" value="<?php tcp_get_value( 'telephone_1' );?>" size="15" maxlength="20" />
		<?php tcp_show_error_msg( $error_address, 'telephone_1' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="telephone_2"><?php _e( 'Telephone 2', 'tcp' );?>:</label></th>
	<td>
		<input type="text" id="telephone_2" name="telephone_2" value="<?php tcp_get_value( 'telephone_2' );?>" size="15" maxlength="20" />
		<?php tcp_show_error_msg( $error_address, 'telephone_2' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="fax"><?php _e( 'Fax', 'tcp' );?>:</label></th>
	<td>
		<input type="text" id="fax" name="fax" value="<?php tcp_get_value( 'fax' );?>" size="15" maxlength="20" />
		<?php tcp_show_error_msg( $error_address, 'fax' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="email"><?php _e( 'eMail', 'tcp' );?>:<span class="compulsory">(*)</span></label></th>
	<td>
		<input type="text" id="email" name="email" value="<?php tcp_get_value( 'email' );?>" size="15" maxlength="20" />
		<?php tcp_show_error_msg( $error_address, 'email' );?></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="default_billing"><?php _e( 'Default billing', 'tcp' );?>:</label></th>
	<td>
		<input type="checkbox" id="default_billing" name="default_billing" value="Y" <?php checked( 'Y', tcp_get_value( 'default_billing', false ) );?> />
	</tr>
	<tr valign="top">
	<th scope="row"><label for="default_shipping"><?php _e( 'Default shipping', 'tcp' );?>:</label></th>
	<td>
		<input type="checkbox" id="default_shipping" name="default_shipping" value="Y" <?php checked( 'Y', tcp_get_value( 'default_shipping', false ) );?> />
	</tr>
	</table>

	<p class="submit">
		<input type="submit" id="tcp_save_address" name="tcp_save_address" class="button-primary" value="<?php _e('Save') ?>" />
	</p>
</form>
</div>
