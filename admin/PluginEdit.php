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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Countries.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );

$plugin_id = isset( $_REQUEST['plugin_id'] ) ? $_REQUEST['plugin_id'] : '';
$plugin_type = isset( $_REQUEST['plugin_type'] ) ? $_REQUEST['plugin_type'] : '';
$instance = isset( $_REQUEST['instance'] ) ? (int)$_REQUEST['instance'] : 0;

if ( isset( $_REQUEST['tcp_plugin_save'] ) ) {
	$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
	if ( ! $plugin_data ) $plugin_data = array();
	$plugin_data[$instance] = array();
	
	if ( isset( $_REQUEST['all_countries'] ) ) {
		$plugin_data[$instance]['all_countries'] = $_REQUEST['all_countries'];
		$plugin_data[$instance]['countries'] = array();
	} else {
		$plugin_data[$instance]['all_countries']	= '';
		$plugin_data[$instance]['countries'] = isset( $_REQUEST['countries'] ) ? $_REQUEST['countries'] : array();
	}
	$plugin_data[$instance]['new_status'] = isset( $_REQUEST['new_status'] ) ? $_REQUEST['new_status'] : Orders::$ORDER_PENDING;
	$plugin = tcp_get_plugin( $plugin_id );
	$plugin_data[$instance] = $plugin->saveEditfields( $plugin_data[$instance] );
	update_option( 'tcp_plugins_data_' . $plugin_id, $plugin_data );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Instance saved', 'tcp' );?>
	</p></div><?php
} elseif ( isset( $_REQUEST['tcp_plugin_delete'] ) ) {
	$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
	unset( $plugin_data[$instance] );
	update_option( 'tcp_plugins_data_' . $plugin_id, $plugin_data );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Instance deleted', 'tcp' );?>
	</p></div><?php
}
$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
$plugin = tcp_get_plugin( $plugin_id );
$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
$instance_href = $admin_path . 'PluginEdit.php&plugin_id=' . $plugin_id . '&plugin_type=' . $plugin_type . '&instance=';
?>

<div class="wrap">
<h2><?php echo __( 'Plugin', 'tcp' );?>: <?php echo $plugin->getTitle();?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo $admin_path;?>PluginsList.php&plugin_type=<?php echo $plugin_type?>"><?php _e( 'return to the list', 'tcp' );?></a></li>
</ul><!-- subsubsub -->
<div class="clear"></div>

<div class="instances">
<?php
if ( is_array( $plugin_data ) ) :
	foreach($plugin_data as $instance_id => $instance_data) :
		if ( $instance_id == $instance) :?>
			<span><?php _e( 'instance', 'tcp' );?> <?php echo $instance_id;?></span>&nbsp;|&nbsp;
		<?php else: ?>
			<a href="<?php echo $instance_href, $instance_id;?>"><?php _e( 'instance', 'tcp' );?> <?php echo $instance_id;?></a>&nbsp;|&nbsp;
		<?php endif;?>
	<?php endforeach;?>
	<a href="<?php echo $instance_href, $instance_id + 1;?>"><?php _e( 'new instance', 'tcp' );?></a>
<?php endif;?>
</div>

<?php if ( is_array( $plugin_data ) ) {
	$data = isset( $plugin_data[$instance] ) ? $plugin_data[$instance] : array();
} else
	$data = array();

$new_status = isset( $data['new_status'] ) ? $data['new_status'] : Orders::$ORDER_PENDING;
?>
<form method="post">
	<input type="hidden" name="plugin_id" value="<?php echo $plugin_id;?>" />
	<input type="hidden" name="plugin_type" value="<?php echo $plugin_type;?>" />
	<input type="hidden" name="instance" value="<?php echo $instance;?>" />
	<table class="form-table">
	<tbody>
	<tr valign="top">
	<th scope="row">
		<label for="new_status"><?php _e( 'Status', 'tcp' );?></label>
	</th>
	<td>
		<select class="postform" id="new_status" name="new_status">
			<option value="<?php echo Orders::$ORDER_PENDING;?>"<?php selected( Orders::$ORDER_PENDING, $new_status );?>><?php _e( 'pending', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_PROCESSING;?>"<?php selected( Orders::$ORDER_PROCESSING, $new_status );?>><?php _e( 'processing', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_COMPLETED;?>"<?php selected( Orders::$ORDER_COMPLETED, $new_status );?>><?php _e( 'completed', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_CANCELLED;?>"<?php selected( Orders::$ORDER_CANCELLED, $new_status );?>><?php _e( 'cancelled', 'tcp' );?></option>
			<option value="<?php echo Orders::$ORDER_SUSPENDED;?>"<?php selected( Orders::$ORDER_SUSPENDED, $new_status );?>><?php _e( 'suspended', 'tcp' );?></option>
		</select> <span class="description"><?php _e( 'It is only used by payment plugins', 'tcp' );?></span>
	</td></tr>
	<tr valign="top">
	<th scope="row">
		<label for="all_countries"><?php _e( 'All countries', 'tcp' );?>:</label>
	</th>
	<td>
		<input type="checkbox" name="all_countries" id="all_countries" <?php checked( isset( $data['all_countries'] ) ? $data['all_countries'] : '', 'yes' );?> value="yes"
		onclick="if (this.checked) jQuery('.sel_countries').hide(); else jQuery('.sel_countries').show();"/>
	</td></tr>
	<tr valign="top" class="sel_countries" <?php
		$all = isset( $data['all_countries'] ) ? $data['all_countries'] : '';
		if ( $all == 'yes' ) echo 'style="display:none;"';
		?>>
	<th scope="row">
		<label for="countries"><?php _e( 'select countries', 'tcp' );?>:</label>
	</th>
	<td>
<?php $countries = isset( $data['countries'] ) ? $data['countries'] : array();?>
		<select class="postform" id="countries" name="countries[]" multiple="true" size="10" style="height: auto;">
			<?php $countries_db = Countries::getAll();
			foreach( $countries_db as $country ) :?>
			<option value="<?php echo $country->iso;?>" <?php tcp_selected_multiple( $countries, $country->iso );?>><?php echo $country->name;?></option>
			<?php endforeach;?>
		</select>
	</td></tr>
<?php $plugin->showEditFields( $data );?>
	</tbody></table>
	<p class="submit">
		<input name="tcp_plugin_save" value="<?php _e( 'save', 'tcp' );?>" type="submit" class="button-primary" />
		<input name="tcp_plugin_delete" value="<?php _e( 'delete', 'tcp' );?>" type="submit" class="button-secondary" />
	</p>
</form>
</div><!-- wrap -->
