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

	private $settings = array();

	function __construct() {
		if ( is_admin() ) {
			$this->settings = get_option( 'tcp_settings' );
			add_action('admin_init', array( $this, 'adminInit' ) );
			add_action('admin_menu', array( $this, 'adminMenu' ) );
		}
	}

	function adminInit() {
		register_setting( 'thecartpress_options', 'tcp_settings', array( $this, 'validate' ) );
		add_settings_section( 'tcp_main_section', __( 'Main settings', 'tcp' ) , array( $this, 'show_main_section' ), __FILE__ );
		add_settings_field( 'stock_management', __( 'Stock management', 'tcp' ), array( $this, 'show_stock_management' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'disable_shopping_cart', __( 'Disable shopping cart', 'tcp' ), array( $this, 'show_disable_shopping_cart' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'currency', __( 'Currency', 'tcp' ), array( $this, 'show_currency' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'unit_weight', __( 'Unit weight', 'tcp' ), array( $this, 'show_unit_weight' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'downloadable_path', __( 'Downloadable path', 'tcp' ), array( $this, 'show_downloadable_path' ), __FILE__ , 'tcp_main_section' );

		add_settings_section( 'tcp_checkout_section', __( 'Checkout settings', 'tcp' ) , array( $this, 'show_checkout_section' ), __FILE__ );
		add_settings_field( 'user_registration', __( 'User registration required', 'tcp' ), array( $this, 'show_user_registration' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'emails', __( '@mails to send orders', 'tcp' ), array( $this, 'show_emails' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'from_email', __( 'From email', 'tcp' ), array( $this, 'show_from_email' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'legal_notice', __( 'Legal notice', 'tcp' ), array( $this, 'show_legal_notice' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'checkout_successfully_message', __( 'Checkout successfully message', 'tcp' ), array( $this, 'show_checkout_successfully_message' ), __FILE__ , 'tcp_checkout_section' );

		add_settings_section( 'tcp_permalinks_section', __( 'Permalinks settings', 'tcp' ) , array( $this, 'show_permalink_section' ), __FILE__ );
		add_settings_field( 'product_rewrite', __( 'Product base', 'tcp' ), array( $this, 'show_product_rewrite' ), __FILE__ , 'tcp_permalinks_section' );
		add_settings_field( 'category_rewrite', __( 'Category base', 'tcp' ), array( $this, 'show_category_rewrite' ), __FILE__ , 'tcp_permalinks_section' );
		add_settings_field( 'tag_rewrite', __( 'Tag base', 'tcp' ), array( $this, 'show_tag_rewrite' ), __FILE__ , 'tcp_permalinks_section' );
		add_settings_field( 'supplier_rewrite', __( 'Supplier base', 'tcp' ), array( $this, 'show_supplier_rewrite' ), __FILE__ , 'tcp_permalinks_section' );

		add_settings_section( 'tcp_theme_compability_section', __( 'Theme compability settings', 'tcp' ) , array( $this, 'show_theme_compability_section' ), __FILE__ );
		add_settings_field( 'load_default_styles', __( 'Load default styles', 'tcp' ), array( $this, 'show_load_default_styles' ), __FILE__ , 'tcp_theme_compability_section' );
		add_settings_field( 'see_buy_button_in_content', __( 'See buy button in content', 'tcp' ), array( $this, 'show_see_buy_button_in_content' ), __FILE__ , 'tcp_theme_compability_section' );
		add_settings_field( 'see_buy_button_in_excerpt', __( 'See buy button in excerpt', 'tcp' ), array( $this, 'show_see_buy_button_in_excerpt' ), __FILE__ , 'tcp_theme_compability_section' );
		add_settings_field( 'see_price_in_content', __( 'See price in content', 'tcp' ), array( $this, 'show_see_price_in_content' ), __FILE__ , 'tcp_theme_compability_section' );
		add_settings_field( 'see_price_in_excerpt', __( 'See price in excerpt', 'tcp' ), array( $this, 'show_see_price_in_excerpt' ), __FILE__ , 'tcp_theme_compability_section' );

		add_settings_section( 'tcp_admin_section', __( 'Admin settings', 'tcp' ) , array( $this, 'show_admin_section' ), __FILE__ );
		add_settings_field( 'hide_visibles', __( 'Hide visible products', 'tcp' ), array( $this, 'show_hide_visibles' ), __FILE__ , 'tcp_admin_section' );

		add_settings_section( 'tcp_search_engine_section', __( 'Search engine', 'tcp' ) , array( $this, 'show_search_engine_section' ), __FILE__ );
		add_settings_field( 'search_engine_activated', __( 'Search engine activated', 'tcp' ), array( $this, 'show_search_engine_activated' ), __FILE__ , 'tcp_search_engine_section' );
	}

	function adminMenu() {
		$base = dirname( dirname( __FILE__ ) ) . '/admin/OrdersList.php';
		add_submenu_page( $base, __( 'TheCartPress settings', 'tcp' ), __( 'Settings', 'tcp' ), 'tcp_edit_settings', 'tcp_settings_page', array( $this, 'showSettings' ) );
	}
	
	function showSettings() {?>
	<div class="wrap">
		<h2><?php _e( 'TheCartPress Settings', 'tcp' );?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'thecartpress_options' ); ?>
			<?php do_settings_sections( __FILE__ ); ?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
			</p>
		</form>
	</div><?php
	}

	function show_main_section() {
		$content = '';
		$content = apply_filters( 'tcp_main_section', $content );
		echo $content;
		echo '<div class="tcp_notice_from_thecartpress"><h4>', __( 'Notice from TheCartPress', 'tcp' ), '</h4>';
		wp_widget_rss_output( 'http://thecartpress.com/feed', array( 'items' => 3, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 0 ) );
		echo '</div>';
	}

	function show_user_registration() {
		$user_registration = isset( $this->settings['user_registration'] ) ? $this->settings['user_registration'] : false;?>
		<input type="checkbox" id="user_registration" name="tcp_settings[user_registration]" value="yes" <?php checked( true, $user_registration );?> /><?php
	}

	function show_emails() {
		$emails = isset( $this->settings['emails'] ) ? $this->settings['emails'] : '';?>
		<input type="text" id="emails" name="tcp_settings[emails]" value="<?php echo $emails;?>" size="40" maxlength="2550" />
		<span class="description"><?php _e( 'Comma (,) separated mails', 'tcp' );?></span><?php
	}

	function show_from_email() {
		$from_email = isset( $this->settings['from_email'] ) ? $this->settings['from_email'] : '';?>
		<input type="text" id="from_email" name="tcp_settings[from_email]" value="<?php echo $from_email;?>" size="40" maxlength="255" /><?php
	}

	function show_stock_management() {
		$stock_management = isset( $this->settings['stock_management'] ) ? $this->settings['stock_management'] : false;?>
		<input type="checkbox" id="stock_management" name="tcp_settings[stock_management]" value="yes" <?php checked( true, $stock_management );?> /><?php
	}

	function show_disable_shopping_cart() {
		$disable_shopping_cart = isset( $this->settings['disable_shopping_cart'] ) ? $this->settings['disable_shopping_cart'] : false;?>
		<input type="checkbox" id="disable_shopping_cart" name="tcp_settings[disable_shopping_cart]" value="yes" <?php checked( true, $disable_shopping_cart );?> />
		<span class="description"><?php _e( 'To use TheCartPress as a catalog.', 'tcp' );?></span><?php
	}
	
	function show_currency() {
		$currency = isset( $this->settings['currency'] ) ? $this->settings['currency'] : 'EUR';?>
		<select id="currency" name="tcp_settings[currency]">
		<?php $currencies = Currencies::getAll();
		foreach( $currencies as $currency_row ) :?>
			<option value="<?php echo $currency_row->iso;?>" <?php selected( $currency_row->iso, $currency );?>><?php echo $currency_row->currency;?></option>
		<?php endforeach;?>
		</select><?php
	}

	function show_unit_weight() {
		$unit_weight = isset( $this->settings['unit_weight'] ) ? $this->settings['unit_weight'] : 'gr';?>
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
		$downloadable_path = isset( $this->settings['downloadable_path'] ) ? $this->settings['downloadable_path'] : '';?>
		<input type="text" id="downloadable_path" name="tcp_settings[downloadable_path]" value="<?php echo $downloadable_path;?>" size="50" maxlength="255" />
		<br /><span class="description"><?php _e( 'To protect the downloadable files from public download, this path must be non-public directory.', 'tcp' );?></span>
		<br /><span class="description"><?php _e( 'Example:' , 'tcp' );?> <?php echo dirname( __FILE__ );?></span><?php
	}

	function show_checkout_section() {
	}

	function show_legal_notice() {
		$legal_notice = isset( $this->settings['legal_notice'] ) ? $this->settings['legal_notice'] : __( 'legal notice', 'tcp' );?>
		<textarea id="legal_notice" name="tcp_settings[legal_notice]" cols="40" rows="5" maxlength="1020"><?php echo $legal_notice;?></textarea><br />
		<span class="description"><?php _e( 'If the legal notice is blank the checkout doesn\'t show the Accept conditions check.', 'tcp' );?></span><?php
	}

	function show_checkout_successfully_message() {
		$checkout_successfully_message = isset( $this->settings['checkout_successfully_message'] ) ? $this->settings['checkout_successfully_message'] : __( 'The order has been completed successfully', 'tcp' );?>
		<textarea id="checkout_successfully_message" name="tcp_settings[checkout_successfully_message]" cols="40" rows="5" maxlength="1020"><?php echo $checkout_successfully_message;?></textarea><br />
		<span class="description"><?php _e( 'This text will show at the end of the checkout process.', 'tcp' );?></span><?php
	}

	function show_permalink_section() {
		$content = apply_filters( 'tcp_permalink_section', '' );
		if ( strlen( $content ) > 0 )
			echo '<span class="description">', $content, '</span>';
	}

	function show_product_rewrite() {
		$product_rewrite = isset( $this->settings['product_rewrite'] ) ? $this->settings['product_rewrite'] : 'product';?>
		<input type="text" id="product_rewrite" name="tcp_settings[product_rewrite]" value="<?php echo $product_rewrite;?>" size="50" maxlength="255" /><?php
	}

	function show_category_rewrite() {
		$category_rewrite = isset( $this->settings['category_rewrite'] ) ? $this->settings['category_rewrite'] : 'category';?>
		<input type="text" id="category_rewrite" name="tcp_settings[category_rewrite]" value="<?php echo $category_rewrite;?>" size="50" maxlength="255" /><?php
	}

	function show_tag_rewrite() {
		$tag_rewrite = isset( $this->settings['tag_rewrite'] ) ? $this->settings['tag_rewrite'] : 'tag';?>
		<input type="text" id="tag_rewrite" name="tcp_settings[tag_rewrite]" value="<?php echo $tag_rewrite;?>" size="50" maxlength="255" /><?php
	}

	function show_supplier_rewrite() {
		$supplier_rewrite = isset( $this->settings['supplier_rewrite'] ) ? $this->settings['supplier_rewrite'] : 'supplier';?>
		<input type="text" id="supplier_rewrite" name="tcp_settings[supplier_rewrite]" value="<?php echo $supplier_rewrite;?>" size="50" maxlength="255" /><?php
	}

	function show_theme_compability_section() {
		$content = __( 'You can uncheck all this options if your theme uses the <a href="http://thecartpress.com" target="_blank">TheCartPress template functions</a>.', 'tcp' );
		$content = apply_filters( 'tcp_theme_compability_section', $content );
		echo '<span class="description">', $content, '</span>';
	}

	function show_load_default_styles() {
		$load_default_styles = isset( $this->settings['load_default_styles'] ) ? $this->settings['load_default_styles'] : true;?>
		<input type="checkbox" id="load_default_styles" name="tcp_settings[load_default_styles]" value="yes" <?php checked( true, $load_default_styles );?> /><?php
	}

	function show_see_buy_button_in_content() {
		$see_buy_button_in_content = isset( $this->settings['see_buy_button_in_content'] ) ? $this->settings['see_buy_button_in_content'] : true;?>
		<input type="checkbox" id="see_buy_button_in_content" name="tcp_settings[see_buy_button_in_content]" value="yes" <?php checked( true, $see_buy_button_in_content );?> /><?php
	}

	function show_see_buy_button_in_excerpt() {
		$see_buy_button_in_excerpt = isset( $this->settings['see_buy_button_in_excerpt'] ) ? $this->settings['see_buy_button_in_excerpt'] : false;?>
		<input type="checkbox" id="see_buy_button_in_excerpt" name="tcp_settings[see_buy_button_in_excerpt]" value="yes" <?php checked( true, $see_buy_button_in_excerpt );?> /><?php
	}

	function show_see_price_in_content() {
		$see_price_in_content = isset( $this->settings['see_price_in_content'] ) ? $this->settings['see_price_in_content'] : false;?>
		<input type="checkbox" id="see_price_in_content" name="tcp_settings[see_price_in_content]" value="yes" <?php checked( true, $see_price_in_content );?> /><?php
	}

	function show_see_price_in_excerpt() {
		$see_price_in_excerpt = isset( $this->settings['see_price_in_excerpt'] ) ? $this->settings['see_price_in_excerpt'] : false;?>
		<input type="checkbox" id="see_price_in_excerpt" name="tcp_settings[see_price_in_excerpt]" value="yes" <?php checked( true, $see_price_in_excerpt );?> /><?php
	}

	function show_admin_section() {
	}

	function show_hide_visibles() {
		$hide_visibles = isset( $this->settings['hide_visibles'] ) ? $this->settings['hide_visibles'] : false;?>
		<input type="checkbox" id="hide_visibles" name="tcp_settings[hide_visibles]" value="yes" <?php checked( true, $hide_visibles );?> /><?php
	}

	function show_search_engine_section() {?>
		<span class="description"><?php _e( '', 'tcp' );?></span><?php
	}

	function show_search_engine_activated() {
		$search_engine_activated = isset( $this->settings['search_engine_activated'] ) ? $this->settings['search_engine_activated'] : true;?>
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
		$input['see_price_in_content']		= isset( $input['see_price_in_content'] ) ? $input['see_price_in_content'] == 'yes' : false;
		$input['see_price_in_excerpt']		= isset( $input['see_price_in_excerpt'] ) ? $input['see_price_in_excerpt'] == 'yes' : false;
		$input['downloadable_path']			= wp_filter_nohtml_kses( $input['downloadable_path'] );
		$input['product_rewrite']			=  wp_filter_nohtml_kses( $input['product_rewrite'] );
		$input['category_rewrite']			=  wp_filter_nohtml_kses( $input['category_rewrite'] );
		$input['tag_rewrite']				=  wp_filter_nohtml_kses( $input['tag_rewrite'] );
		$input['supplier_rewrite']			=  wp_filter_nohtml_kses( $input['supplier_rewrite'] );
		$input['checkout_successfully_message']	= wp_filter_nohtml_kses( $input['checkout_successfully_message'] );
		$input['load_default_styles']		= isset( $input['load_default_styles'] ) ? $input['load_default_styles'] == 'yes' : false;
		$input['hide_visibles']				= isset( $input['hide_visibles'] ) ? $input['hide_visibles'] == 'yes' : false;
		$input['search_engine_activated']	= isset( $input['search_engine_activated'] ) ? $input['search_engine_activated'] == 'yes' : false;
		$input = apply_filters( 'tcp_validate_settings', $input );
		return $input;
	}
}
?>
