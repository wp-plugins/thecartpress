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

$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';

$plugin_type = isset( $_REQUEST['plugin_type'] ) ? $_REQUEST['plugin_type'] : '';

if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) return;?>

<div class="wrap">
<h2><?php echo __( 'Plugins', 'tcp' );?></h2>
<ul class="subsubsub"></ul>
<div class="clear"></div>


<form method="post">
<div class="tablenav">
<p class="search-box">
<label for="plugin_type"><?php _e( 'Plugin type', 'tcp' );?>:</label>
	<select class="postform" id="plugin_type" name="plugin_type">
		<option value="" <?php selected( '', $plugin_type );?>><?php _e( 'all', 'tcp' );?></option>
		<option value="shipping" <?php selected( 'shipping', $plugin_type );?>><?php _e( 'shipping', 'tcp' );?></option>
		<option value="payment" <?php selected( 'payment', $plugin_type );?>><?php _e( 'payment', 'tcp' );?></option>
	</select>
	<input type="submit" class="button-secondary" value="<?php _e( 'Filter', 'tcp' );?>" id="post-query-submit" />
</p>
</div>
</form>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</tfoot>
<tbody>
<?php if ( $plugin_type == 'shipping' ) {
	global $tcp_shipping_plugins;
	$plugins = $tcp_shipping_plugins;
} elseif ( $plugin_type == 'payment' ) {
	global $tcp_payment_plugins;
	$plugins = $tcp_payment_plugins;
} else {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;
	$plugins = $tcp_shipping_plugins + $tcp_payment_plugins;
}
foreach( $plugins as $id => $plugin ) :?>
	<tr>
		<td><?php echo $plugin->getTitle();?></td>
		<td><?php echo $plugin->getDescription();?></td>
		<td style="width: 20%;">
		<a href="<?php echo $admin_path;?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $plugin_type;?>"><?php _e( 'edit', 'tcp' );?></a>
		</td>
	</tr>
<?php endforeach;?>
</tbody></table>
</div> <!-- end wrap -->
