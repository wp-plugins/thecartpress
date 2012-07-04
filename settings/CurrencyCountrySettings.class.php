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

class TCPCurrencyCountrySettings {

	private $updated = false;

	function __construct() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Localize', 'tcp' ), false, array( 'TCPCurrencyCountrySettings', __FILE__ ), plugins_url( 'thecartpress/images/miranda/currency_settings_48.png' ) );
	}

	function admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_settings();
		$page = add_submenu_page( $base, __( 'Currency & Country Settings', 'tcp' ), __( 'Localize', 'tcp' ), 'tcp_edit_settings', 'currency_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
		    'id'      => 'overview',
		    'title'   => __( 'Overview' ),
		    'content' =>
				'<p>' . __( 'You can customize TheCartPress behaviour about international sales customisation currencies.', 'tcp' ) . '</p>' .
				'<p>' . __( 'Set Unit weight to use across your site.'. 'tcp' ) . '</p>' .
				'<p>' . __( 'You can customize Countries to use across your installation.', 'tcp' ) . '</p>'
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
	<?php screen_icon(); ?><h2><?php _e( 'Currency Settings', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$currency			= $thecartpress->get_setting( 'currency', 'EUR' );
$currency_layout	= $thecartpress->get_setting( 'currency_layout', '%1$s%2$s (%3$s)' );
$decimal_currency	= $thecartpress->get_setting( 'decimal_currency', 2 );
$decimal_point		= $thecartpress->get_setting( 'decimal_point', '.' );
$thousands_separator= $thecartpress->get_setting( 'thousands_separator', ',' );
$unit_weight		= $thecartpress->get_setting( 'unit_weight', 'gr');
$country			= $thecartpress->get_setting( 'country', '' );
$billing_isos		= $thecartpress->get_setting( 'billing_isos', array() );
$shipping_isos		= $thecartpress->get_setting( 'shipping_isos', array() ); ?>

<form method="post" action="">
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
<tr valign="top">
	<th scope="row">
		<label for="continue_url"><?php _e( 'Unit weight', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="unit_weight" name="unit_weight">
			<option value="kg." <?php selected( 'kg.', $unit_weight ); ?>><?php _e( 'Kilogram (kg)', 'tcp' ); ?></option>
			<option value="gr." <?php selected( 'gr.', $unit_weight ); ?>><?php _e( 'Gram (gr)', 'tcp' ); ?></option>
			<option value="T." <?php selected( 'T.', $unit_weight ); ?>><?php _e( 'Ton (t)', 'tcp' ); ?></option>
			<option value="mg." <?php selected( 'mg.', $unit_weight ); ?>><?php _e( 'Milligram (mg)', 'tcp' ); ?></option>
			<option value="ct." <?php selected( 'ct.', $unit_weight ); ?>><?php _e( 'Karat (ct)', 'tcp' ); ?></option>
			<option value="oz." <?php selected( 'oz.', $unit_weight ); ?>><?php _e( 'Ounce (oz)', 'tcp' ); ?></option>
			<option value="lb." <?php selected( 'lb.', $unit_weight ); ?>><?php _e( 'Pound (lb)', 'tcp' ); ?></option>
			<option value="oz t." <?php selected( 'oz t.', $unit_weight ); ?>><?php _e( 'Troy ounce (oz t)', 'tcp' ); ?></option>
			<option value="dwt." <?php selected( 'dwt.', $unit_weight ); ?>><?php _e( 'Pennyweight (dwt)', 'tcp' ); ?></option>
			<option value="gr. (en)" <?php selected( 'gr. (en)', $unit_weight ); ?>><?php _e( 'Grain (gr)', 'tcp' ); ?></option>
			<option value="cwt." <?php selected( 'cwt.', $unit_weight ); ?>><?php _e( 'Hundredweight (cwt)', 'tcp' ); ?></option>
			<option value="st." <?php selected( 'st.', $unit_weight ); ?>><?php _e( 'Ston (st)', 'tcp' ); ?></option>
			<option value="T. (long)" <?php selected( 'T. (long)', $unit_weight ); ?>><?php _e( 'Long ton (T long)', 'tcp' ); ?></option>
			<option value="T. (short)" <?php selected( 'T. (short)', $unit_weight ); ?>><?php _e( 'Short ton (T shors)', 'tcp' ); ?></option>
			<option value="hw. (long)" <?php selected( 'hw. (long)', $unit_weight ); ?>><?php _e( 'Long Hundredweights (hw long)', 'tcp' ); ?></option>
			<option value="hw. (short)" <?php selected( 'hw. (short)', $unit_weight ); ?>><?php _e( 'Short Hundredweights (hw short)', 'tcp' ); ?></option>
			<option value="koku" <?php selected( 'koku', $unit_weight ); ?>><?php _e( 'koku', 'tcp' ); ?></option>
			<option value="kann" <?php selected( 'kann', $unit_weight ); ?>><?php _e( 'kann', 'tcp' ); ?></option>
			<option value="kinn" <?php selected( 'kinn', $unit_weight ); ?>><?php _e( 'kinn', 'tcp' ); ?></option>
			<option value="monnme" <?php selected( 'monnme', $unit_weight ); ?>><?php _e( 'monnme', 'tcp' ); ?></option>
			<option value="tael" <?php selected( 'tael', $unit_weight ); ?>><?php _e( 'tael', 'tcp' ); ?></option>
			<option value="ku ping" <?php selected( 'ku ping', $unit_weight ); ?>><?php _e( 'ku ping', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

<tr>
	<th colspan="2"><h2><?php _e( 'Countries Settings', 'tcp'); ?></h2></th>
</tr>

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
<tr valign="top">
	<th scope="row">
	<label for="all_billing_isos"><?php _e( 'Allowed billing countries', 'tcp' ); ?></label>
	</th>
	<td>
		<p><?php _e( 'All countries', 'tcp' ); ?>:&nbsp;<input type="checkbox" name="all_billing_isos" id="all_billing_isos" <?php checked( count( $billing_isos ), 0 ); ?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_billing_isos').hide(); tcp_select_none('billing_isos'); } else { jQuery('.sel_billing_isos').show(); }"/></p>
		<div class="sel_billing_isos" <?php if ( count( $billing_isos ) == 0 ) echo 'style="display:none;"'; ?> >
			<select id="billing_isos" name="billing_isos[]" style="height:auto" size="8" multiple>
			<?php $countries = Countries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso; ?>" <?php tcp_selected_multiple( $billing_isos, $item->iso ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p>
			<input type="button" value="<?php _e( 'EU', 'tcp'); ?>" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>" onclick="tcp_select_eu('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp'); ?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' ); ?>" onclick="tcp_select_nafta('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp'); ?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' ); ?>" onclick="tcp_select_caricom('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp'); ?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' ); ?>" onclick="tcp_select_mercasur('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' ); ?>" onclick="tcp_select_can('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp'); ?>" title="<?php _e( 'To select countries from African Union', 'tcp' ); ?>" onclick="tcp_select_au('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp'); ?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' ); ?>" onclick="tcp_select_apec('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' ); ?>" onclick="tcp_select_asean('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'None', 'tcp'); ?>" title="<?php _e( 'Deselect all', 'tcp' ); ?>" onclick="tcp_select_none('billing_isos');" class="button-secondary"/>
			</p>
			<script>
			jQuery('#billing_isos').tcp_convert_multiselect();
			</script>
		</div>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="all_shipping_isos"><?php _e( 'Allowed Shipping countries', 'tcp' ); ?></label>
	</th>
	<td>
		<p><?php _e( 'All countries', 'tcp' ); ?>:&nbsp;<input type="checkbox" name="all_shipping_isos" id="all_shipping_isos" <?php checked( count( $shipping_isos ), 0 ); ?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_shipping_isos').hide(); tcp_select_none('shipping_isos'); } else { jQuery('.sel_shipping_isos').show(); }"/>
		</p>
		<div class="sel_shipping_isos" <?php if ( ! $shipping_isos ) echo 'style="display:none;"'; ?>>
			<select id="shipping_isos" name="shipping_isos[]" style="height:auto" size="8" multiple>
			<?php $countries = Countries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso; ?>" <?php tcp_selected_multiple( $shipping_isos, $item->iso ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p>
			<input type="button" value="<?php _e( 'EU', 'tcp'); ?>" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>" onclick="tcp_select_eu('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp'); ?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' ); ?>" onclick="tcp_select_nafta('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp'); ?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' ); ?>" onclick="tcp_select_caricom('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp'); ?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' ); ?>" onclick="tcp_select_mercasur('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' ); ?>" onclick="tcp_select_can('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp'); ?>" title="<?php _e( 'To select countries from African Union', 'tcp' ); ?>" onclick="tcp_select_au('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp'); ?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' ); ?>" onclick="tcp_select_apec('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' ); ?>" onclick="tcp_select_asean('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'None', 'tcp'); ?>" title="<?php _e( 'Deselect all', 'tcp' ); ?>" onclick="tcp_select_none('shipping_isos');" class="button-secondary"/>
			</p>
			<script>
			jQuery('#shipping_isos').tcp_convert_multiselect();
			</script>
		</div>
	</td>
</tr>
</tbody>
</table>

<?php wp_nonce_field( 'tcp_currency_settings' ); ?>
<?php submit_button( null, 'primary', 'save-currency-settings' ); ?>
</form>
</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_currency_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['currency']				= isset( $_POST['currency'] ) ? $_POST['currency'] : 'EUR';		
		$settings['currency_layout']		= isset( $_POST['currency_layout'] ) ? $_POST['currency_layout'] : '%1$s%2$s (%3$s)';
		$settings['decimal_currency']		= isset( $_POST['decimal_currency'] ) ? $_POST['decimal_currency'] : 2;
		$settings['decimal_point']			= isset( $_POST['decimal_point'] ) ? $_POST['decimal_point'] : '.';
		$settings['thousands_separator']	= isset( $_POST['thousands_separator'] ) ? $_POST['thousands_separator'] : ',';
		$settings['unit_weight']			= isset( $_POST['unit_weight'] ) ? $_POST['unit_weight'] : 'gr';
		if ( isset( $_POST['all_shipping_isos'] ) && $_POST['all_shipping_isos'] == 'yes' ) $settings['shipping_isos'] = array();
		else $settings['shipping_isos'] = isset( $_POST['shipping_isos'] ) ? $_POST['shipping_isos'] : array();
		if ( isset( $_POST['all_billing_isos'] ) && $_POST['all_billing_isos'] == 'yes' ) $settings['billing_isos'] = array();
		else $settings['billing_isos'] = isset( $_POST['billing_isos'] ) ? $_POST['billing_isos'] : array();
		$settings['country'] = isset( $_POST['country'] ) ? $_POST['country'] : '';
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPCurrencyCountrySettings();
?>
