<?php
/**
 * This file is part of TheCartPress.
 * 
 * This progam is free software: you can redistribute it and/or modify
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

class TCPPluginsList {

	private $plugin_type;

	function __construct( $plugin_type = 'payment' ) {
		$this->plugin_type = $plugin_type;
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}

	function admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_settings();
		if ( $this->plugin_type == 'payment' ) {
			$title = __( 'Payment Methods', 'tcp' );
			$menu_slug = 'payment_settings';
		} else {
			$title = __( 'Shipping Methods', 'tcp' );
			$menu_slug = 'shipping_settings';
		}
		$page = add_submenu_page( $base, $title, $title, 'tcp_edit_settings', $menu_slug, array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
//		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
		    'id'      => 'overview',
		    'title'   => __( 'Overview' ),
		    'content' =>
	            '<p>' . __( 'You can customize Payment and Shipping plugins.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
	}

	function admin_page() { ?>
<div class="wrap">
	<?php screen_icon(); ?><h2><?php $this->plugin_type == 'payment' ? _e( 'Payment methods', 'tcp' ) : _e( 'Shipping methods', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<div class="clear"></div>

<form method="post">

<form method="post">

<div class="tablenav">
	<p class="search-box">
	<label for="plugin_type"><?php _e( 'Plugin type', 'tcp' ); ?>:</label>
		<select class="postform" id="plugin_type" name="plugin_type">
			<option value="" <?php selected( '', $this->plugin_type ); ?>><?php _e( 'all', 'tcp' ); ?></option>
			<option value="shipping" <?php selected( 'shipping', $this->plugin_type ); ?>><?php _e( 'shipping', 'tcp' ); ?></option>
			<option value="payment" <?php selected( 'payment', $this->plugin_type ); ?>><?php _e( 'payment', 'tcp' ); ?></option>
		</select>
		<input type="submit" class="button-secondary" value="<?php _e( 'Filter', 'tcp' ); ?>" id="post-query-submit" />
	</p>
</div>

</form>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Instances', 'tcp' ); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Plugin', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Instances', 'tcp' ); ?></th>
</tr>
</tfoot>
<tbody>
<?php if ( $this->plugin_type == 'shipping' ) {
	global $tcp_shipping_plugins;
	$plugins = $tcp_shipping_plugins;
} elseif ( $this->plugin_type == 'payment' ) {
	global $tcp_payment_plugins;
	$plugins = $tcp_payment_plugins;
} else {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;
	$plugins = $tcp_shipping_plugins + $tcp_payment_plugins;
}
foreach( $plugins as $id => $plugin ) :
	$tr_class = '';
	$data = tcp_get_plugin_data( $id );
	if ( is_array( $data ) && count( $data ) > 0 ) {
		$n_active = 0;
		foreach( $data as $instances )
			if ( $instances['active'] ) $n_active++;
		$out = sprintf( __( 'N<sup>o</sup> of instances: %d, actives: %d ', 'tcp') ,  count( $data ), $n_active );
		if ( $n_active > 0 )
			$tr_class = 'class="tcp_active_plugin"';
	} else {
		$out = __( 'Not in use', 'tcp' );
	} ?>

	<tr <?php echo $tr_class;?>>

		<td>

		<?php $icon = $plugin->getIcon();
		if ( $icon ) : ?>

			<a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $this->plugin_type;?>" title="<?php echo $plugin->getTitle(); ?>" class="tcp_payment_title">
			<img src="<?php echo $icon; ?>" height="32px" />
			</a>

		<?php else : ?>

			<a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $this->plugin_type;?>" title="<?php _e( 'Edit', 'tcp' ); ?>" class="tcp_payment_title"><?php echo $plugin->getTitle(); ?></a>

		<?php endif; ?>

			<div class="tcp_plugins_edit"><a href="<?php echo TCP_ADMIN_PATH; ?>PluginEdit.php&plugin_id=<?php echo $id;?>&plugin_type=<?php echo $this->plugin_type;?>"><?php _e( 'edit', 'tcp' ); ?></a></div>

		</td>

		<td><?php echo $plugin->getDescription(); ?></td>

		<td><?php echo $out;?></td>

	</tr>
<?php endforeach;?>
</tbody></table>
</div> <!-- end wrap -->
<?php
	}
}

new TCPPluginsList( 'payment' );
?>
