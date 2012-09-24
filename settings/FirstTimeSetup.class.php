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

require_once( TCP_DAOS_FOLDER . 'Currencies.class.php' );
require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );

class TCPFirstTimeSetup {

	private $updated = false;

	function __construct() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'First time setup', 'tcp' ), false, array( 'TCPFirstTimeSetup', __FILE__ ), plugins_url( 'thecartpress/images/miranda/first_settings_48.png' ) );
	}

	function plugin_action_links( $links, $file ) {
		if ( $file == 'thecartpress/TheCartPress.class.php' && function_exists( 'admin_url' ) ) {
			$first_link = '<a href="' . admin_url( 'admin.php?page=first_time_setup' ). '">' . __( 'First time setup', 'tcp' ) . '</a>';
			array_unshift( $links, $first_link );
		}
		return $links;
	}

	function admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_settings();
		$page = add_submenu_page( $base, __( 'First Time Setup', 'tcp' ), __( 'First time', 'tcp' ), 'tcp_edit_settings', 'first_time_setup', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
		    'id'      => 'overview',
		    'title'   => __( 'Overview' ),
		    'content' =>
				'<p>' . __( 'You can customize TheCartPress behaviour using this few steps.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
		//wp_enqueue_script('custom-background');
		//wp_enqueue_style('farbtastic');
	}

	function admin_page() { ?>

<div class="wrap">

	<?php screen_icon( 'tcp-first' ); ?><h2><?php _e( 'First time setup', 'tcp' ); ?></h2>

<?php if ( ! empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>

	<p><?php _e( 'Congratulations! You have finished to setup your eCommerce software.', 'tcp' ); ?></p>

	<p><?php _e( 'The eCommerce has actived following shipping and payments methods:', 'tcp' ); ?></p>

	<h3><?php _e( 'Shipping methods', 'tcp' ); ?></h3>

	<?php global $tcp_shipping_plugins; echo $this->get_plugins_info( $tcp_shipping_plugins ); ?>

	<p><?php _e( 'To activate or deactivate Shipping methods you have to visit <a href="admin.php?page=shipping_settings">Shipping page</a>.', 'tcp' ); ?></p>

	<h3><?php _e( 'Payment methods', 'tcp' ); ?></h3>

	<?php global $tcp_payment_plugins; echo $this->get_plugins_info( $tcp_payment_plugins ); ?>

	<p><?php _e( 'To activate or deactivate Payment methods you have to visit <a href="admin.php?page=payment_settings">Payment page</a>.', 'tcp' ); ?></p>

	<p><?php _e( 'Remmember, you have more configuration settings in <a href="admin.php?page=thecartpress/TheCartPress.class.php">Settings page</a>.', 'tcp' ); ?></p>

<?php else: ?>

	<?php global $thecartpress;
	$country			= $thecartpress->get_setting( 'country', '' );

	$currency			= $thecartpress->get_setting( 'currency', 'EUR' );
	$currency_layout	= $thecartpress->get_setting( 'currency_layout', '%1$s%2$s (%3$s)' );
	$decimal_currency	= $thecartpress->get_setting( 'decimal_currency', 2 );
	$decimal_point		= $thecartpress->get_setting( 'decimal_point', '.' );
	$thousands_separator= $thecartpress->get_setting( 'thousands_separator', ',' );

	$user_registration	= $thecartpress->get_setting( 'user_registration' );
	$emails				= $thecartpress->get_setting( 'emails' );
	$from_email			= $thecartpress->get_setting( 'from_email' );

	$use_default_loop	= $thecartpress->get_setting( 'use_default_loop', 'only_settings' );
	?>

	<form method="post" action="">

	<div id="step_one" class="tcp_step">

		<h3><?php _e( 'Step One', 'tcp' ); ?></h3>

		<p class="description"><?php _e( 'Set base country for your eCommerce', 'tcp' ); ?></p>

		<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row">
				<label for="country"><?php _e( 'Base country', 'tcp' ); ?></label>
			</th>
			<td>
				<select id="country" name="country">
				<?php $countries = Countries::getAll();
				foreach( $countries as $item ) : ?>
					<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $country ); ?>><?php echo $item->name; ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>

		</tbody>
		</table>

		<p>
			<input class="tcp_next_step button-secondary" type="button" value="<?php _e( 'Next step', 'tcp' ); ?>" onclick="tcp_show_step('step_two');" />
		</p>

	</div><!-- #step_one -->

	<div id="step_two" class="tcp_step">

		<h3><?php _e( 'Step Two', 'tcp' ); ?></h3>

		<p class="description"><?php _e( 'Set Currency settings to use along the Store', 'tcp' ); ?></p>

		<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row">
				<label for="currency"><?php _e( 'Currency', 'tcp' ); ?></label>
			</th>
			<td>
				<select id="currency" name="currency">
				<?php $currencies = Currencies::getAll();
				foreach( $currencies as $currency_row ) : ?>
					<option value="<?php echo $currency_row->iso; ?>" <?php selected( $currency_row->iso, $currency ); ?>><?php echo $currency_row->currency; ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
			<label for="tcp_custom_layouts"><?php _e( 'Currency layouts', 'tcp' ); ?></label>
			</th>
			<td>
				<p><label for="tcp_custom_layouts"><?php _e( 'Default layouts', 'tcp' ); ?>:</label>
				<select id="tcp_custom_layouts" onchange="jQuery('#currency_layout').val(jQuery('#tcp_custom_layouts').val());">
					<option value="%1$s%2$s %3$s" <?php selected( '%1$s%2$s %3$s', $currency_layout); ?>><?php _e( 'Currency sign left, Currency ISO right: $100 USD', 'tcp' ); ?></option>
					<option value="%1$s%2$s" <?php selected( '%1$s%2$s', $currency_layout); ?>><?php _e( 'Currency sign left: $100', 'tcp' ); ?></option>
					<option value="%2$s %1$s" <?php selected( '%2$s %1$s', $currency_layout); ?>><?php _e( 'Currency sign right: 100 &euro;', 'tcp' ); ?></option>
				</select>
				</p>
				<label for="currency_layouts"><?php _e( 'Custom layout', 'tcp' ); ?>:</label>
				<input type="text" id="currency_layout" name="currency_layout" value="<?php echo $currency_layout; ?>" size="20" maxlength="25" />
				<p class="description"><?php _e( '%1$s -> Currency; %2$s -> Amount; %3$s -> ISO Code. By default, use %1$s%2$s (%3$s) -> $100 (USD).', 'tcp' ); ?></p>
				<p class="description"><?php _e( 'For Example: For Euro use %2$s %1$s -> 100&euro;.', 'tcp' ); ?></p>
				<p class="description"><?php _e( 'If this value is left to blank, then TheCartPress will take this layout from the languages configuration files (mo files). Look for the literal "%1$s%2$s (%3$s)."', 'tcp' ); ?></p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="decimal_currency"><?php _e( 'Currency decimals', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="text" id="decimal_currency" name="decimal_currency" value="<?php echo $decimal_currency; ?>" size="1" maxlength="1" class="tcp_count"/>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="decimal_point"><?php _e( 'Decimal point separator', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="text" id="decimal_point" name="decimal_point" value="<?php echo $decimal_point; ?>" size="1" maxlength="1" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="continue_url"><?php _e( 'Thousands separator', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="text" id="thousands_separator" name="thousands_separator" value="<?php echo $thousands_separator; ?>" size="1" maxlength="1" />
			</td>
		</tr>

		</tbody>
		</table>

		<p>
			<input class="tcp_prev_step button-secondary" type="button" value="<?php _e( 'Previuos step', 'tcp' ); ?>" onclick="tcp_show_step('step_one');" />
			<input class="tcp_next_step button-secondary" type="button" value="<?php _e( 'Next step', 'tcp' ); ?>" onclick="tcp_show_step('step_three');" />
		</p>

	</div><!-- #step_two -->

	<div id="step_three" class="tcp_step">

		<h3><?php _e( 'Step Three', 'tcp' ); ?></h3>

		<p class="description"><?php _e( 'Set Checkout options', 'tcp' ); ?></p>
	
		<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row">
				<label for="user_registration"><?php _e( 'User registration required', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="checkbox" id="user_registration" name="user_registration" value="yes" <?php checked( true, $user_registration ); ?> />
				<p class="description"><?php _e( 'Indicates if the clients should be or not registered to buy.', 'tcp' ); ?></p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
			<label for="emails"><?php _e( '@mails to send orders', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="text" id="emails" name="emails" value="<?php echo $emails; ?>" size="40" maxlength="2550" />
				<span class="description"><?php _e( 'Comma (,) separated mails', 'tcp' ); ?></span>
				<p class="description"><?php _e( 'These emails will receive orders notifications.', 'tcp' ); ?></p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
			<label for="from_email"><?php _e( 'From email', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="text" id="from_email" name="from_email" value="<?php echo $from_email; ?>" size="40" maxlength="255" />
				<p class="description"><?php _e( 'Host email. If not set, The emails will be sent to the customer from no-response@thecartpress.com', 'tcp' ); ?></p>		
			</td>
		</tr>

		</tbody>
		</table>

		<p>
			<input class="tcp_prev_step button-secondary" type="button" value="<?php _e( 'Previuos step', 'tcp' ); ?>" onclick="tcp_show_step('step_two');" />
			<input class="tcp_next_step button-secondary" type="button" value="<?php _e( 'Next step', 'tcp' ); ?>" onclick="tcp_show_step('step_four');" />
		</p>

	</div><!-- #step_three -->

	<div id="step_four" class="tcp_step">

		<h3><?php _e( 'Step Four', 'tcp' ); ?></h3>

		<p class="description"><?php _e( 'Theme Compatibility', 'tcp'); ?></p>

		<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row">
				<label for="use_default_loop_only"><?php _e( 'Theme templates', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="radio" id="use_default_loop_only" name="use_default_loop" value="only_settings" <?php checked( 'only_settings', $use_default_loop ); ?>
				onclick="hide_excerpt();"/> <label for="use_default_loop_only"><strong><?php _e( 'Use configurable TCP loops', 'tcp' ); ?></strong></label>
				<p class="description"><?php _e( 'If this setting is activated you should have a configurable TCP Loop in your theme. (eg: loop-tcp-grid.php)', 'tcp' ); ?></p>
				<p class="description"><?php printf( __( 'You must configure the grid using <a href="%s">Loop settings</a> menu.', 'tcp' ), add_query_arg( 'page', 'loop_settings', get_admin_url() . 'admin.php' ) ); ?></p>
				<p class="description"><?php _e( 'Total flexibility for developers and theme constructors.', 'tcp' ); ?></p>

				<input type="radio" id="use_default_loop_2011" name="use_default_loop" value="yes" <?php checked( 'yes', $use_default_loop ); ?>
				onclick="hide_excerpt();" /> <label for="use_default_loop"><strong><?php _e( 'Use TCP default Templates (twentyeleven based)', 'tcp' ); ?></strong></label>
				<br/>

				<input type="radio" id="use_default_loop_2010" name="use_default_loop" value="yes_2010" <?php checked( 'yes_2010', $use_default_loop ); ?>
				onclick="hide_excerpt();" /> <label for="use_default_loop"><strong><?php _e( 'Use TCP default Templates (twentyten based)', 'tcp' ); ?></strong></label>
				<p class="description"><?php _e( 'To show Product Pages with default/basic template provided by TheCartPress.', 'tcp' ); ?></p>
				<p class="description"><?php printf( __( 'If this setting is activated you must configure the grid using <a href="%s">Loop settings</a> menu.', 'tcp' ), add_query_arg( 'page', 'loop_settings', get_admin_url() . 'admin.php' ) ); ?></p>
				<p class="description"><?php _e( 'TheCartPress provides two version of default templates, one for themes based on "Twenty Eleven" and another for themes based on "Twenty Ten".', 'tcp' ); ?></p>

				<input type="radio" id="use_default_loop_none" name="use_default_loop" value="none" <?php checked( 'none', $use_default_loop ); ?> 
				onclick="show_excerpt();"/> <label for="use_default_loop_none"><strong><?php _e( 'None', 'tcp' ); ?></strong></label>
				<p class="description"><?php _e( 'Use your own templates. Total flexibility for developers and theme constructors.', 'tcp' ); ?></p>
			</td>
		</tr>

		</tbody>
		</table>

		<p>
			<input class="tcp_prev_step button-secondary" type="button" value="<?php _e( 'Previuos step', 'tcp' ); ?>" onclick="tcp_show_step('step_three');" />
			<?php submit_button( null, 'primary', 'save-first_time_setup' ); ?>
			<?php wp_nonce_field( 'tcp_first_time_setup' ); ?>
		</p>

	</div><!-- #step_four -->

	</form>

	</div>

	<script>
	function tcp_show_step( id ) {
		jQuery('.tcp_step').hide();
		jQuery('#' + id).show();
	}
	jQuery(document).ready(function() {
		jQuery('.tcp_step').hide();
		jQuery('#step_one').show();
	});
	</script>

<?php endif; ?>

<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_first_time_setup' );
		$settings = get_option( 'tcp_settings' );

		$settings['country']				= isset( $_POST['country'] ) ? $_POST['country'] : '';

		$settings['currency']				= isset( $_POST['currency'] ) ? $_POST['currency'] : 'EUR';		
		$settings['currency_layout']		= isset( $_POST['currency_layout'] ) ? $_POST['currency_layout'] : '%1$s%2$s (%3$s)';
		$settings['decimal_currency']		= isset( $_POST['decimal_currency'] ) ? $_POST['decimal_currency'] : 2;
		$settings['decimal_point']			= isset( $_POST['decimal_point'] ) ? $_POST['decimal_point'] : '.';
		$settings['thousands_separator']	= isset( $_POST['thousands_separator'] ) ? $_POST['thousands_separator'] : ',';

		$settings['user_registration']		= isset( $_POST['user_registration'] ) ? $_POST['user_registration'] == 'yes' : false;
		$settings['emails']					= $_POST['emails'];
		$settings['from_email']				= $_POST['from_email'];

		$settings['use_default_loop' ]		= isset( $_POST['use_default_loop'] ) ? $_POST['use_default_loop'] : 'only_settings';

		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
	
	function get_plugins_info( $plugins ) {
		ob_start();
		$total_active = 0;
		if ( is_array( $plugins ) && count( $plugins ) > 0 ) : ?>

			<table width="50%" style="padding-left: 5em;border: 1px lightgrey solid" border="0">

			<?php foreach( $plugins as $id => $plugin ) : ?>
				<tr>
				<td><?php echo $plugin->getTitle(); ?></td>
				<td>
				<?php $data = tcp_get_plugin_data( $id );
				if ( is_array( $data ) && count( $data ) > 0 ) :
					$n_active = 0;
					foreach( $data as $instances )
						if ( $instances['active'] )
							$n_active++;
					$total_active += $n_active; ?>
					<?php printf( __( 'N<sup>o</sup> of instances: %d, actives: %d ', 'tcp') ,  count( $data ), $n_active ); ?>
				<?php else : ?>
					<?php _e( 'Not in use', 'tcp' ); ?>
				<?php endif; ?>
				</td>
				</tr>
			<?php endforeach; ?>

			</table>

			<?php if ( $total_active == 0 ) : ?>
				<p><strong style="color:red"><?php _e( 'NOTICE: None is active!', 'tcp' ); ?></strong></p>
			<?php endif; ?>
		<?php endif;
		return ob_get_clean();
	}
}

new TCPFirstTimeSetup();
?>