<?
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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Currencies.class.php' );

class TCP_Settings {

	function __construct() {
		if ( is_admin() ) {
			add_action('admin_init', array( $this, 'adminInit' ) );
			add_action('admin_menu', array( $this, 'adminMenu' ) );
		}
	}

	function adminInit() {
		register_setting( 'thecartpress_options', 'tcp_settings', array( $this, 'validate' ) );
		add_settings_section( 'main_section', __( 'Main settings', 'tcp' ) , array( $this, 'show_main_section' ), __FILE__ );
		add_settings_field( 'user_registration', __( 'User registration required', 'tcp' ), array( $this, 'show_user_registration' ), __FILE__ , 'main_section' );
		add_settings_field( 'emails', __( '@mails to send orders', 'tcp' ), array( $this, 'show_emails' ), __FILE__ , 'main_section' );
		add_settings_field( 'from_email', __( 'From email', 'tcp' ), array( $this, 'show_from_email' ), __FILE__ , 'main_section' );
		add_settings_field( 'stock_management', __( 'Stock management', 'tcp' ), array( $this, 'show_stock_management' ), __FILE__ , 'main_section' );
		add_settings_field( 'disable_shopping_cart', __( 'Disable shopping cart', 'tcp' ), array( $this, 'show_disable_shopping_cart' ), __FILE__ , 'main_section' );
		add_settings_field( 'currency', __( 'Currency', 'tcp' ), array( $this, 'show_currency' ), __FILE__ , 'main_section' );
		add_settings_field( 'unit_weight', __( 'Unit weight', 'tcp' ), array( $this, 'show_unit_weight' ), __FILE__ , 'main_section' );
		add_settings_field( 'downloadable_path', __( 'Downloadable path', 'tcp' ), array( $this, 'show_downloadable_path' ), __FILE__ , 'main_section' );

		add_settings_section( 'checkout_section', __( 'Checkout settings', 'tcp' ) , array( $this, 'show_checkout_section' ), __FILE__ );
		add_settings_field( 'legal_notice', __( 'Legal notice', 'tcp' ), array( $this, 'show_legal_notice' ), __FILE__ , 'checkout_section' );

		add_settings_section( 'theme_compability_section', __( 'Theme compability settings', 'tcp' ) , array( $this, 'show_theme_compability_section' ), __FILE__ );
		add_settings_field( 'load_default_styles', __( 'Load default styles', 'tcp' ), array( $this, 'show_load_default_styles' ), __FILE__ , 'theme_compability_section' );
		add_settings_field( 'see_buy_button_in_content', __( 'See buy button in content', 'tcp' ), array( $this, 'show_see_buy_button_in_content' ), __FILE__ , 'theme_compability_section' );
		add_settings_field( 'see_buy_button_in_excerpt', __( 'See buy button in excerpt', 'tcp' ), array( $this, 'show_see_buy_button_in_excerpt' ), __FILE__ , 'theme_compability_section' );

		add_settings_section( 'search_engine_section', __( 'Search engine', 'tcp' ) , array( $this, 'show_search_engine_section' ), __FILE__ );
		add_settings_field( 'search_engine_activated', __( 'Search engine activated', 'tcp' ), array( $this, 'show_search_engine_activated' ), __FILE__ , 'search_engine_section' );
	}

	function adminMenu() {
		$base = dirname( dirname( __FILE__ ) ) . '/admin/OrdersList.php';
		add_submenu_page( $base, __( 'TheCartPress settings', 'tcp' ), __( 'Settings', 'tcp' ), 'tcp_edit_settings', 'tcp_settings_page', array( $this, 'showSettings' ) );
	}
	
	function showSettings() {?>
	<div class="wrap">
		<h2><?php _e( 'TheCartPress Settings', 'tcp' );?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('thecartpress_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div><?php
	}

	function show_main_section() {
		$content = '';
		$content = apply_filters( 'tcp_main_section', $content );
		echo $content;
	}

	function show_user_registration() {
		$settings = get_option( 'tcp_settings' );
		$user_registration = isset( $settings['user_registration'] ) ? $settings['user_registration'] : false;?>
		<input type="checkbox" id="user_registration" name="tcp_settings[user_registration]" value="yes" <?php checked( true, $user_registration );?> /><?php
	}

	function show_emails() {
		$settings = get_option( 'tcp_settings' );
		$emails = isset( $settings['emails'] ) ? $settings['emails'] : '';?>
		<input id="emails" name="tcp_settings[emails]" value="<?php echo $emails;?>" size="40" maxlength="255" type="text">
		<span class="description"><?php _e( 'Comma (,) separated mails', 'tcp' );?></span><?php
	}

	function show_from_email() {
		$settings = get_option( 'tcp_settings' );
		$from_email = isset( $settings['from_email'] ) ? $settings['from_email'] : '';?>
		<input id="from_email" name="tcp_settings[from_email]" value="<?php echo $from_email;?>" size="40" maxlength="255" type="text"><?php
	}

	function show_stock_management() {
		$settings = get_option( 'tcp_settings' );
		$stock_management = isset( $settings['stock_management'] ) ? $settings['stock_management'] : false;?>
		<input type="checkbox" id="stock_management" name="tcp_settings[stock_management]" value="yes" <?php checked( true, $stock_management );?> /><?php
	}

	function show_disable_shopping_cart() {
		$settings = get_option( 'tcp_settings' );
		$disable_shopping_cart = isset( $settings['disable_shopping_cart'] ) ? $settings['disable_shopping_cart'] : false;?>
		<input type="checkbox" id="disable_shopping_cart" name="tcp_settings[disable_shopping_cart]" value="yes" <?php checked( true, $disable_shopping_cart );?> />
		<span class="description"><?php _e( 'To use TheCartPress as a catalog.', 'tcp' );?></span><?php
	}
	
	function show_currency() {
		$settings = get_option( 'tcp_settings' );
		$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';?>
		<select id="currency" name="tcp_settings[currency]">
		<?php $currencies = Currencies::getAll();
		foreach( $currencies as $currency_row ) :?>
			<option value="<?php echo $currency_row->iso;?>" <?php selected( $currency_row->iso, $currency );?>><?php echo $currency_row->currency;?></option>
		<?php endforeach;?>
		</select><?php
	}

	function show_unit_weight() {
		$settings = get_option( 'tcp_settings' );
		$unit_weight = isset( $settings['unit_weight'] ) ? $settings['unit_weight'] : 'gr';?>
		<select id="unit_weight" name="tcp_settings[unit_weight]">
			<option value="kg" <?php selected( 'kg', $unit_weight );?>><?php _e( 'Kilogram (kg)', 'tcp' );?></option>
			<option value="gr" <?php selected( 'gr', $unit_weight );?>><?php _e( 'Gram (gr)', 'tcp' );?></option>
			<option value="T" <?php selected( 'T', $unit_weight );?>><?php _e( 'Ton (t)', 'tcp' );?></option>
			<option value="mg" <?php selected( 'mg', $unit_weight );?>><?php _e( 'Milligram (mg)', 'tcp' );?></option>
			<option value="ct" <?php selected( 'ct', $unit_weight );?>><?php _e( 'Karat (ct)', 'tcp' );?></option> //Quilate
			//English
			<option value="oz" <?php selected( 'oz', $unit_weight );?>><?php _e( 'Ounce (oz)', 'tcp' );?></option>
			<option value="lb" <?php selected( 'lb', $unit_weight );?>><?php _e( 'Pound (lb)', 'tcp' );?></option>
			<option value="oz t" <?php selected( 'oz t', $unit_weight );?>><?php _e( 'Troy ounce (oz t)', 'tcp' );?></option>
			<option value="dwt" <?php selected( 'dwt', $unit_weight );?>><?php _e( 'Pennyweight (dwt)', 'tcp' );?></option>
			<option value="gr (en)" <?php selected( 'gr (en)', $unit_weight );?>><?php _e( 'Grain (gr)', 'tcp' );?></option>
			<option value="cwt" <?php selected( 'cwt', $unit_weight );?>><?php _e( 'Hundredweight (cwt)', 'tcp' );?></option>
			<option value="st" <?php selected( 'st', $unit_weight );?>><?php _e( 'Ston (st)', 'tcp' );?></option>
			<option value="T (long)" <?php selected( 'T (long)', $unit_weight );?>><?php _e( 'Long ton (T long)', 'tcp' );?></option>
			<option value="T (short)" <?php selected( 'T (short)', $unit_weight );?>><?php _e( 'Short ton (T shors)', 'tcp' );?></option>
			<option value="hw (long)" <?php selected( 'hw (long)', $unit_weight );?>><?php _e( 'Long Hundredweights (hw long)', 'tcp' );?></option>
			<option value="hw (short)" <?php selected( 'hw (short)', $unit_weight );?>><?php _e( 'Short Hundredweights (hw short)', 'tcp' );?></option>
			//Japanese
			<option value="koku" <?php selected( 'koku', $unit_weight );?>><?php _e( 'koku', 'tcp' );?></option>
			<option value="kann" <?php selected( 'kann', $unit_weight );?>><?php _e( 'kann', 'tcp' );?></option>
			<option value="kinn" <?php selected( 'kinn', $unit_weight );?>><?php _e( 'kinn', 'tcp' );?></option>
			<option value="monnme" <?php selected( 'monnme', $unit_weight );?>><?php _e( 'monnme', 'tcp' );?></option>
			//Chinesse
			<option value="tael" <?php selected( 'tael', $unit_weight );?>><?php _e( 'tael', 'tcp' );?></option>
			<option value="ku ping" <?php selected( 'ku ping', $unit_weight );?>><?php _e( 'ku ping', 'tcp' );?></option>
		</select><?php
	}

	function show_downloadable_path() {
		$settings = get_option( 'tcp_settings' );
		$downloadable_path = isset( $settings['downloadable_path'] ) ? $settings['downloadable_path'] : '';?>
		<input type="text" id="downloadable_path" name="tcp_settings[downloadable_path]" value="<?php echo $downloadable_path;?>" size="50" maxlength="255"/><br />
		<span class="description"><?php _e( 'To protect the downloadable files from public download, this path must be non-public directory ', 'tcp' );?></span><?php	
	}

	function show_checkout_section() {
	}

	function show_legal_notice() {
		$settings = get_option( 'tcp_settings' );
		$legal_notice = isset( $settings['legal_notice'] ) ? $settings['legal_notice'] : __( 'legal notice', 'tcp' );?>
		<textarea id="legal_notice" name="tcp_settings[legal_notice]" cols="40" rows="5" maxlength="1020"><?php echo $legal_notice;?></textarea><br />
		<span class="description"><?php _e( 'If the legal notice is blank the checkout doesn\'t show the Accept conditions check', 'tcp' );?></span><?php
	}


	function show_theme_compability_section() {
		$content = __( 'You can uncheck all this options if your theme uses the <a href="http://thecartpress.com" target="_blank">TheCartPress template functions</a>.', 'tcp' );
		$content = apply_filters( 'tcp_theme_compability_section', $content );
		echo '<span class="description">', $content, '</span>';
	}

	function show_load_default_styles() {
		$settings = get_option( 'tcp_settings' );
		$load_default_styles = isset( $settings['load_default_styles'] ) ? $settings['load_default_styles'] : true;?>
		<input type="checkbox" id="load_default_styles" name="tcp_settings[load_default_styles]" value="yes" <?php checked( true, $load_default_styles );?> /><?php
	}

	function show_see_buy_button_in_content() {
		$settings = get_option( 'tcp_settings' );
		$see_buy_button_in_content = isset( $settings['see_buy_button_in_content'] ) ? $settings['see_buy_button_in_content'] : true;?>
		<input type="checkbox" id="see_buy_button_in_content" name="tcp_settings[see_buy_button_in_content]" value="yes" <?php checked( true, $see_buy_button_in_content );?> /><?php
	}

	function show_see_buy_button_in_excerpt() {
		$settings = get_option( 'tcp_settings' );
		$see_buy_button_in_excerpt = isset( $settings['see_buy_button_in_excerpt'] ) ? $settings['see_buy_button_in_excerpt'] : false;?>
		<input type="checkbox" id="see_buy_button_in_excerpt" name="tcp_settings[see_buy_button_in_excerpt]" value="yes" <?php checked( true, $see_buy_button_in_excerpt );?> /><?php
	}

	function show_search_engine_section() {?>
		<span class="description"><?php _e( '', 'tcp' );?></span><?php
	}

	function show_search_engine_activated() {
		$settings = get_option( 'tcp_settings' );
		$search_engine_activated = isset( $settings['search_engine_activated'] ) ? $settings['search_engine_activated'] : true;?>
		<input type="checkbox" id="search_engine_activated" name="tcp_settings[search_engine_activated]" value="yes" <?php checked( true, $search_engine_activated );?> /><?php
	}

	function validate( $input ) {
		$input['legal_notice']				=  wp_filter_nohtml_kses( $input['legal_notice'] );
		$input['from_email']				=  wp_filter_nohtml_kses( $input['from_email'] );
		$input['emails']					=  wp_filter_nohtml_kses( $input['emails'] );
		$input['stock_management']			= isset( $input['stock_management'] ) ? $input['stock_management'] == 'yes' : false;
		$input['disable_shopping_cart']		= isset( $input['disable_shopping_cart'] ) ? $input['disable_shopping_cart'] == 'yes' : false;
		$input['user_registration']			= isset( $input['user_registration'] ) ? $input['user_registration'] == 'yes' : false;
		$input['see_buy_button_in_content']	= isset( $input['see_buy_button_in_content'] ) ? $input['see_buy_button_in_content'] == 'yes' : false;
		$input['see_buy_button_in_excerpt']	= isset( $input['see_buy_button_in_excerpt'] ) ? $input['see_buy_button_in_excerpt'] == 'yes' : false;
		$input['downloadable_path']			= wp_filter_nohtml_kses( $input['downloadable_path'] );
		$input['load_default_styles']		= isset( $input['load_default_styles'] ) ? $input['load_default_styles'] == 'yes' : false;
		$input['search_engine_activated']	= isset( $input['search_engine_activated'] ) ? $input['search_engine_activated'] == 'yes' : false;
		$input = apply_filters( 'tcp_validate_settings', $input );
		return $input;
	}
}
?>
