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
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Currencies.class.php' );

class TCP_Settings {
	function __construct( $register = true ) {
		if ( $register && is_admin() ) {
			add_action('admin_init', array( $this, 'admin_init' ) );
			add_action('admin_menu', array( $this, 'admin_menu' ) );
			//add_filter('contextual_help', array( $this, 'contextual_help') , 10, 3);
		}
	}

	function contextual_help( $contextual_help, $screen_id, $screen ) {
		if ( $screen_id == 'thecartpress_page_tcp_settings_page' ) {
			$contextual_help = 'This is where I would provide help to the user on how everything in my admin panel works. Formatted HTML works fine in here too.';
		}
		return $contextual_help;
	}

	function admin_init() {
		register_setting( 'thecartpress_options', 'tcp_settings', array( $this, 'validate' ) );
		add_settings_section( 'tcp_main_section', __( 'Main settings', 'tcp' ) , array( $this, 'show_main_section' ), __FILE__ );
		add_settings_field( 'after_add_to_cart', __( 'After adding to cart', 'tcp' ), array( $this, 'show_after_add_to_cart' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'stock_management', __( 'Stock management', 'tcp' ), array( $this, 'show_stock_management' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'disable_ecommerce', __( 'Disable eCommerce', 'tcp' ), array( $this, 'show_disable_ecommerce' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'disable_shopping_cart', __( 'Disable shopping cart', 'tcp' ), array( $this, 'show_disable_shopping_cart' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'enabled_wish_list', __( 'Enabled Wish list', 'tcp' ), array( $this, 'show_enabled_wish_list' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'currency_layout', __( 'Currency layout', 'tcp' ), array( $this, 'show_currency_layout' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'currency', __( 'Currency', 'tcp' ), array( $this, 'show_currency' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'decimal_currency', __( 'Currency decimals', 'tcp' ), array( $this, 'show_decimal_currency' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'decimal_point', __( 'Decimal point separator', 'tcp' ), array( $this, 'show_decimal_point' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'thousands_separator', __( 'Thousands separator', 'tcp' ), array( $this, 'show_thousands_separator' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'unit_weight', __( 'Unit weight', 'tcp' ), array( $this, 'show_unit_weight' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'hide_downloadable_menu', __( 'Hide downloadable menu', 'tcp' ), array( $this, 'show_hide_downloadable_menu' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'downloadable_path', __( 'Downloadable path', 'tcp' ), array( $this, 'show_downloadable_path' ), __FILE__ , 'tcp_main_section' );
		add_settings_field( 'continue_url', __( 'Continue Shopping in', 'tcp' ), array( $this, 'show_continue_url' ), __FILE__ , 'tcp_main_section' );

		add_settings_section( 'tcp_countries_section', __( 'Countries settings', 'tcp' ) , array( $this, 'show_countries_section' ), __FILE__ );
		add_settings_field( 'country', __( 'Country', 'tcp' ), array( $this, 'show_country' ), __FILE__ , 'tcp_countries_section' );
		add_settings_field( 'billing_isos', __( 'Allowed Billing countries', 'tcp' ), array( $this, 'show_countries_for_billing' ), __FILE__ , 'tcp_countries_section' );
		add_settings_field( 'shipping_isos', __( 'Allowed Shipping countries', 'tcp' ), array( $this, 'show_countries_for_shipping' ), __FILE__ , 'tcp_countries_section' );

		add_settings_section( 'tcp_tax_section', __( 'Tax settings', 'tcp' ) , array( $this, 'show_tax_section' ), __FILE__ );
		add_settings_field( 'default_tax_country', __( 'Default tax country', 'tcp' ), array( $this, 'show_default_tax_country' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'prices_include_tax', __( 'Prices include tax', 'tcp' ), array( $this, 'show_prices_include_tax' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'tax_based_on', __( 'Tax based on', 'tcp' ), array( $this, 'show_tax_based_on' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'shipping_cost_include_tax', __( 'Shipping cost include tax', 'tcp' ), array( $this, 'show_shipping_cost_include_tax' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'tax_for_shipping', __( 'Select tax for shipping/payment/other costs', 'tcp' ), array( $this, 'show_tax_for_shipping' ), __FILE__ , 'tcp_tax_section' );
		//add_settings_field( 'apply_tax_after_discount', __( 'Apply tax after discount', 'tcp' ), array( $this, 'show_apply_tax_after_discount' ), __FILE__ , 'tcp_tax_section' );
		//add_settings_field( 'apply_discount_on_prices_including_tax', __( 'Apply discount on prices including tax', 'tcp' ), array( $this, 'show_apply_discount_on_prices_including_tax' ), __FILE__ , 'tcp_tax_section' );
		//add_settings_field( 'Apply_tax_on', __( 'Apply tax on', 'tcp' ), array( $this, 'show_apply_tax_on' ), __FILE__ , 'tcp_tax_section' );
		//Apply Tax On: Original prices only or Custom price if available
		add_settings_field( 'display_prices_with_taxes', __( 'Display prices with taxes', 'tcp' ), array( $this, 'show_display_prices_with_taxes' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'display_shipping_cost_with_taxes', __( 'Display shipping prices with taxes', 'tcp' ), array( $this, 'show_display_shipping_cost_with_taxes' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'display_full_tax_summary', __( 'Display full tax summary', 'tcp' ), array( $this, 'show_display_full_tax_summary' ), __FILE__ , 'tcp_tax_section' );
		add_settings_field( 'display_zero_tax_subtotal', __( 'Display zero tax subtotal', 'tcp' ), array( $this, 'show_display_zero_tax_subtotal' ), __FILE__ , 'tcp_tax_section' );
		//add_settings_field( 'display_Cart_Order_prices', __( 'Display Cart/Order prices', 'tcp' ), array( $this, 'show_display_Cart_Order_prices' ), __FILE__ , 'tcp_tax_section' );

		add_settings_section( 'tcp_checkout_section', __( 'Checkout settings', 'tcp' ) , array( $this, 'show_checkout_section' ), __FILE__ );
		add_settings_field( 'user_registration', __( 'User registration required', 'tcp' ), array( $this, 'show_user_registration' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'emails', __( '@mails to send orders', 'tcp' ), array( $this, 'show_emails' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'from_email', __( 'From email', 'tcp' ), array( $this, 'show_from_email' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'legal_notice', __( 'Checkout notice', 'tcp' ), array( $this, 'show_legal_notice' ), __FILE__ , 'tcp_checkout_section' );
		add_settings_field( 'checkout_successfully_message', __( 'Checkout successfully message', 'tcp' ), array( $this, 'show_checkout_successfully_message' ), __FILE__ , 'tcp_checkout_section' );

		add_settings_section( 'tcp_permalinks_section', __( 'Permalinks settings', 'tcp' ) , array( $this, 'show_permalink_section' ), __FILE__ );
		add_settings_field( 'product_rewrite', __( 'Product base', 'tcp' ), array( $this, 'show_product_rewrite' ), __FILE__ , 'tcp_permalinks_section' );
		add_settings_field( 'category_rewrite', __( 'Category base', 'tcp' ), array( $this, 'show_category_rewrite' ), __FILE__ , 'tcp_permalinks_section' );
		add_settings_field( 'tag_rewrite', __( 'Tag base', 'tcp' ), array( $this, 'show_tag_rewrite' ), __FILE__ , 'tcp_permalinks_section' );
		add_settings_field( 'supplier_rewrite', __( 'Supplier base', 'tcp' ), array( $this, 'show_supplier_rewrite' ), __FILE__ , 'tcp_permalinks_section' );

		add_settings_section( 'tcp_theme_compatibility_section', __( 'Theme compatibility settings', 'tcp' ) , array( $this, 'show_theme_compatibility_section' ), __FILE__ );
		add_settings_field( 'use_tcp_loops', __( 'Use TCP Loops Configurables', 'tcp' ), array( $this, 'show_use_tcp_loops' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'load_default_buy_button_style', __( 'Load default buy button style', 'tcp' ), array( $this, 'show_load_default_buy_button_style' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'load_default_shopping_cart_checkout_style', __( 'Load default shopping cart & checkout style', 'tcp' ), array( $this, 'show_load_default_shopping_cart_checkout_style' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'load_default_loop_style', __( 'Load default loop style', 'tcp' ), array( $this, 'show_load_default_loop_style' ), __FILE__ , 'tcp_theme_compatibility_section' );

		add_settings_field( 'products_per_page', __( 'Product pages show at most', 'tcp' ), array( $this, 'show_products_per_page' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_buy_button_in_content', __( 'See buy button in content', 'tcp' ), array( $this, 'show_see_buy_button_in_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'align_buy_button_in_content', __( 'Align of buy button in content', 'tcp' ), array( $this, 'show_align_buy_button_in_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_buy_button_in_excerpt', __( 'See buy button in excerpt', 'tcp' ), array( $this, 'show_see_buy_button_in_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'align_buy_button_in_excerpt', __( 'Align of buy button in excerpt', 'tcp' ), array( $this, 'show_align_buy_button_in_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_price_in_content', __( 'See price in content', 'tcp' ), array( $this, 'show_see_price_in_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_price_in_excerpt', __( 'See price in excerpt', 'tcp' ), array( $this, 'show_see_price_in_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_image_in_content', __( 'See image in content', 'tcp' ), array( $this, 'show_see_image_in_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_size_content', __( 'Image size in content', 'tcp' ), array( $this, 'image_size_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_align_content', __( 'Image align in content', 'tcp' ), array( $this, 'image_align_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_link_content', __( 'Image link in content', 'tcp' ), array( $this, 'image_link_content' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_image_in_excerpt', __( 'See image in excerpt', 'tcp' ), array( $this, 'show_see_image_in_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_size_excerpt', __( 'Image size in excerpt', 'tcp' ), array( $this, 'image_size_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_align_excerpt', __( 'Image align in excerpt', 'tcp' ), array( $this, 'image_align_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_link_excerpt', __( 'Image link in excerpt', 'tcp' ), array( $this, 'image_link_excerpt' ), __FILE__ , 'tcp_theme_compatibility_section' );
		add_settings_section( 'tcp_admin_section', __( 'Admin settings', 'tcp' ) , array( $this, 'show_admin_section' ), __FILE__ );
		add_settings_field( 'hide_visibles', __( 'Hide invisible products', 'tcp' ), array( $this, 'show_hide_visibles' ), __FILE__ , 'tcp_admin_section' );
//		add_settings_field( 'show_back_end_label', __( 'Show back end label', 'tcp' ), array( $this, 'show_back_end_label' ), __FILE__ , 'tcp_admin_section' );
		add_settings_section( 'tcp_search_engine_section', __( 'Search engine', 'tcp' ) , array( $this, 'show_search_engine_section' ), __FILE__ );
		add_settings_field( 'search_engine_activated', __( 'Search engine activated', 'tcp' ), array( $this, 'show_search_engine_activated' ), __FILE__ , 'tcp_search_engine_section' );
	}

	function admin_menu() {
		global $thecartpress;
		$disable_ecommerce = isset( $thecartpress->settings['disable_ecommerce'] ) ? $thecartpress->settings['disable_ecommerce'] : false;
		if ( ! $disable_ecommerce )
			$base = $thecartpress->get_base();
		else
			$base = $thecartpress->get_base_tools();
		add_submenu_page( $base, __( 'TheCartPress settings', 'tcp' ), __( 'Settings', 'tcp' ), 'tcp_edit_settings', 'tcp_settings_page', array( $this, 'showSettings' ) );
	}

	function showSettings() {
		global $thecartpress;
		$thecartpress->loadSettings();?>
		<div class="wrap">
			<h2><?php _e( 'TheCartPress Settings', 'tcp' );?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'thecartpress_options' ); ?>
				<?php do_settings_sections( __FILE__ ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>
			</form>
		</div>
		<?php do_action( 'tcp_show_settings' );
	}

	function show_main_section() {
		$content = '';
		$content = apply_filters( 'tcp_main_section', $content );
		echo $content;
		echo '<div class="tcp_notice_from_thecartpress" style="float:right;"><div style="border: 1px dotted;padding-left: 1em;padding-right: 1em;"><h4>', __( 'Notice from TheCartPress', 'tcp' ), '</h4>';
		wp_widget_rss_output( 'http://thecartpress.com/feed', array( 'items' => 3, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 0 ) );
		echo '</div></div>';
	}

	function show_user_registration( $user_registration = false ) {
		if ( ! $user_registration ) {
			global $thecartpress;
			$user_registration = isset( $thecartpress->settings['user_registration'] ) ? $thecartpress->settings['user_registration'] : false;
		}?>
		<input type="checkbox" id="user_registration" name="tcp_settings[user_registration]" value="yes" <?php checked( true, $user_registration );?> /><?php
	}

	function show_emails( $emails = false ) {
		if ( ! $emails ) {
			global $thecartpress;
			$emails = isset( $thecartpress->settings['emails'] ) ? $thecartpress->settings['emails'] : '';
		}?>
		<input type="text" id="emails" name="tcp_settings[emails]" value="<?php echo $emails;?>" size="40" maxlength="2550" />
		<span class="description"><?php _e( 'Comma (,) separated mails', 'tcp' );?></span><?php
	}

	function show_from_email( $from_email = false ) {
		if ( ! $from_email ) {
			global $thecartpress;
			$from_email = isset( $thecartpress->settings['from_email'] ) ? $thecartpress->settings['from_email'] : '';
		}?>
		<input type="text" id="from_email" name="tcp_settings[from_email]" value="<?php echo $from_email;?>" size="40" maxlength="255" /><?php
	}

	function show_after_add_to_cart() {
		global $thecartpress;
		$after_add_to_cart = isset( $thecartpress->settings['after_add_to_cart'] ) ? $thecartpress->settings['after_add_to_cart'] : '';?>
		<select id="after_add_to_cart" name="tcp_settings[after_add_to_cart]">
			<option value="" <?php selected( $after_add_to_cart, '' );?>><?php _e( 'Nothing', 'tcp' );?></option>
			<option value="ssc" <?php selected( $after_add_to_cart, 'ssc' );?>><?php _e( 'Show the Shopping Cart', 'tcp' );?></option>
		</select><?php
	}

	function show_stock_management() {
		global $thecartpress;
		$stock_management = isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;?>
		<input type="checkbox" id="stock_management" name="tcp_settings[stock_management]" value="yes" <?php checked( true, $stock_management );?> /><?php
	}

	function show_disable_shopping_cart() {
		global $thecartpress;
		$disable_shopping_cart = isset( $thecartpress->settings['disable_shopping_cart'] ) ? $thecartpress->settings['disable_shopping_cart'] : false;?>
		<input type="checkbox" id="disable_shopping_cart" name="tcp_settings[disable_shopping_cart]" value="yes" <?php checked( true, $disable_shopping_cart );?> />
		<span class="description"><?php _e( 'To use TheCartPress as a catalog.', 'tcp' );?></span><?php
	}

	function show_disable_ecommerce() {
		global $thecartpress;
		$disable_ecommerce = isset( $thecartpress->settings['disable_ecommerce'] ) ? $thecartpress->settings['disable_ecommerce'] : false;?>
		<input type="checkbox" id="disable_ecommerce" name="tcp_settings[disable_ecommerce]" value="yes" <?php checked( true, $disable_ecommerce );?> />
		<span class="description"><?php _e( 'To use TheCartPress as a Framework (disabling all eCommerce functionalities).', 'tcp' );?></span><?php
	}

	function show_enabled_wish_list() {
		global $thecartpress;
		$enabled_wish_list = isset( $thecartpress->settings['enabled_wish_list'] ) ? $thecartpress->settings['enabled_wish_list'] : false;?>
		<input type="checkbox" id="enabled_wish_list" name="tcp_settings[enabled_wish_list]" value="yes" <?php checked( true, $enabled_wish_list );?> /><?php
	}

	function show_currency_layout( $currency_layout = false) {
		if ( ! $currency_layout ) {
			global $thecartpress;
			$currency_layout = isset( $thecartpress->settings['currency_layout'] ) ? $thecartpress->settings['currency_layout'] : '%1$s%2$s (%3$s)';
		}?>
		<label for="tcp_custom_layouts"><?php _e( 'Custom layouts', 'tcp' ); ?>:</label>
		<select id="tcp_custom_layouts" onchange="jQuery('#currency_layout').val(jQuery('#tcp_custom_layouts').val());">
			<option value="%1$s%2$s %3$s" <?php selected( '%1$s%2$s %3$s', $currency_layout); ?>><?php _e( 'Currency sign left, Currency ISO right: $100 USD', 'tcp' );?></option>
			<option value="%1$s%2$s" <?php selected( '%1$s%2$s', $currency_layout); ?>><?php _e( 'Currency sign left: $100', 'tcp' );?></option>
			<option value="%2$s% 1$s" <?php selected( '%2$s %1$s', $currency_layout); ?>><?php _e( 'Currency sign right: 100 &euro;', 'tcp' );?></option>
		</select>
		<br/>
		<input type="text" id="currency_layout" name="tcp_settings[currency_layout]" value="<?php echo $currency_layout;?>" size="20" maxlength="25" />
		<br/><span class="description"><?php _e( '%1$s -> Currency; %2$s -> Amount; %3$s -> ISO Code. By default, use %1$s%2$s (%3$s) -> $100 (USD).', 'tcp' );?>
		<br/><span class="description"><?php _e( 'For Example: For Euro use %2$s %1$s -> 100&euro;.', 'tcp' );?>
		<br/><span class="description"><?php _e( 'If this value is left to blank, then TheCartPress will take this layout from the languages configuration files (mo files).', 'tcp' );?>
		</span><?php
	}

	function show_currency( $currency = false ) {
		if ( ! $currency ) {
			global $thecartpress;
			$currency = isset( $thecartpress->settings['currency'] ) ? $thecartpress->settings['currency'] : 'EUR';
		}?>
		<select id="currency" name="tcp_settings[currency]">
		<?php $currencies = Currencies::getAll();
		foreach( $currencies as $currency_row ) :?>
			<option value="<?php echo $currency_row->iso;?>" <?php selected( $currency_row->iso, $currency );?>><?php echo $currency_row->currency;?></option>
		<?php endforeach;?>
		</select><?php
	}
	
	function show_decimal_currency( $decimal_currency = false) {
		if ( ! $decimal_currency ) {
			global $thecartpress;
			$decimal_currency = isset( $thecartpress->settings['decimal_currency'] ) ? $thecartpress->settings['decimal_currency'] : '2';
		}?>
		<input type="text" id="decimal_currency" name="tcp_settings[decimal_currency]" value="<?php echo $decimal_currency;?>" size="1" maxlength="1" /><?php
	}

	function show_decimal_point( $decimal_point = false ) {
		if ( ! $decimal_point ) {
			global $thecartpress;
			$decimal_point = isset( $thecartpress->settings['decimal_point'] ) ? $thecartpress->settings['decimal_point'] : '.';
		} ?>
		<input type="text" id="decimal_point" name="tcp_settings[decimal_point]" value="<?php echo $decimal_point;?>" size="1" maxlength="1" /><?php
	}

	function show_thousands_separator( $thousands_separator = false) {
		if ( ! $thousands_separator ) {
			global $thecartpress;
			$thousands_separator = isset( $thecartpress->settings['thousands_separator'] ) ? $thecartpress->settings['thousands_separator'] : ',';
		} ?>
		<input type="text" id="thousands_separator" name="tcp_settings[thousands_separator]" value="<?php echo $thousands_separator;?>" size="1" maxlength="1" /><?php
	}

	function show_unit_weight( $unit_weight = false ) {
		if ( ! $unit_weight ) {
			global $thecartpress;
			$unit_weight = isset( $thecartpress->settings['unit_weight'] ) ? $thecartpress->settings['unit_weight'] : 'gr';
		}?>
		<select id="unit_weight" name="tcp_settings[unit_weight]">
			<option value="kg." <?php selected( 'kg.', $unit_weight );?>><?php _e( 'Kilogram (kg)', 'tcp' );?></option>
			<option value="gr." <?php selected( 'gr.', $unit_weight );?>><?php _e( 'Gram (gr)', 'tcp' );?></option>
			<option value="T." <?php selected( 'T.', $unit_weight );?>><?php _e( 'Ton (t)', 'tcp' );?></option>
			<option value="mg." <?php selected( 'mg.', $unit_weight );?>><?php _e( 'Milligram (mg)', 'tcp' );?></option>
			<option value="ct." <?php selected( 'ct.', $unit_weight );?>><?php _e( 'Karat (ct)', 'tcp' );?></option> //Quilate
			//English
			<option value="oz." <?php selected( 'oz.', $unit_weight );?>><?php _e( 'Ounce (oz)', 'tcp' );?></option>
			<option value="lb." <?php selected( 'lb.', $unit_weight );?>><?php _e( 'Pound (lb)', 'tcp' );?></option>
			<option value="oz t." <?php selected( 'oz t.', $unit_weight );?>><?php _e( 'Troy ounce (oz t)', 'tcp' );?></option>
			<option value="dwt." <?php selected( 'dwt.', $unit_weight );?>><?php _e( 'Pennyweight (dwt)', 'tcp' );?></option>
			<option value="gr. (en)" <?php selected( 'gr. (en)', $unit_weight );?>><?php _e( 'Grain (gr)', 'tcp' );?></option>
			<option value="cwt." <?php selected( 'cwt.', $unit_weight );?>><?php _e( 'Hundredweight (cwt)', 'tcp' );?></option>
			<option value="st." <?php selected( 'st.', $unit_weight );?>><?php _e( 'Ston (st)', 'tcp' );?></option>
			<option value="T. (long)" <?php selected( 'T. (long)', $unit_weight );?>><?php _e( 'Long ton (T long)', 'tcp' );?></option>
			<option value="T. (short)" <?php selected( 'T. (short)', $unit_weight );?>><?php _e( 'Short ton (T shors)', 'tcp' );?></option>
			<option value="hw. (long)" <?php selected( 'hw. (long)', $unit_weight );?>><?php _e( 'Long Hundredweights (hw long)', 'tcp' );?></option>
			<option value="hw. (short)" <?php selected( 'hw. (short)', $unit_weight );?>><?php _e( 'Short Hundredweights (hw short)', 'tcp' );?></option>
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

	function show_hide_downloadable_menu() {
		global $thecartpress;
		$hide_downloadable_menu = isset( $thecartpress->settings['hide_downloadable_menu'] ) ? $thecartpress->settings['hide_downloadable_menu'] : false;?>
		<input type="checkbox" id="hide_downloadable_menu" name="tcp_settings[hide_downloadable_menu]" value="yes" <?php checked( $hide_downloadable_menu, true );?> />
		<br /><span class="description"><?php _e( 'This value is used in the Continue shopping link into the Shopping cart page.', 'tcp' );?></span>
		<br /><span class="description"><?php _e( 'If the value is left to blank then the "home url" will be used.', 'tcp' );?></span><?php
	}

	function show_downloadable_path() {
		global $thecartpress;
		$downloadable_path = isset( $thecartpress->settings['downloadable_path'] ) ? $thecartpress->settings['downloadable_path'] : '';?>
		<input type="text" id="downloadable_path" name="tcp_settings[downloadable_path]" value="<?php echo $downloadable_path;?>" size="50" maxlength="255" />
		<br /><span class="description"><?php _e( 'To protect the downloadable files from public download, this path must be non-public directory.', 'tcp' );?></span>
		<br /><span class="description"><?php printf( __( 'This is the path for this page in your server: %s' , 'tcp' ), dirname( __FILE__ ) );?></span><?php
	}
	
	function show_continue_url() {
		global $thecartpress;
		$continue_url = isset( $thecartpress->settings['continue_url'] ) ? $thecartpress->settings['continue_url'] : '';?>
		<input type="text" id="continue_url" name="tcp_settings[continue_url]" value="<?php echo $continue_url;?>" size="50" maxlength="255" /><?php
	}

	function show_countries_section() {
	}

	function show_country( $country = false ) {//default country
		if ( ! $country ) {
			global $thecartpress;
			$country = isset( $thecartpress->settings['country'] ) ? $thecartpress->settings['country'] : '';
		}?>
		<select id="country" name="tcp_settings[country]">
		<?php $countries = Countries::getAll();
		foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso;?>" <?php selected( $item->iso, $country );?>><?php echo $item->name;?></option>
		<?php endforeach;?>
		</select>
		<?php
	}

	function show_countries_for_shipping() {
		global $thecartpress;
		$shipping_isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : array();?>
		<?php _e( 'All countries', 'tcp' );?>:&nbsp;<input type="checkbox" name="all_shipping_isos" id="all_shipping_isos" <?php checked( count( $shipping_isos ), 0 );?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_shipping_isos').hide(); jQuery('#shipping_isos option').attr('selected', false);  } else { jQuery('.sel_shipping_isos').show(); }"/>
		<br />
		<div class="sel_shipping_isos" <?php if ( ! $shipping_isos ) echo 'style="display:none;"';?>>
			<select id="shipping_isos" name="tcp_settings[shipping_isos][]" style="height:auto" size="8" multiple="true">
			<?php $countries = Countries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso;?>" <?php tcp_selected_multiple( $shipping_isos, $item->iso );?>><?php echo $item->name;?></option>
			<?php endforeach;?>
			</select>
			<br/>
			<input type="button" value="<?php _e( 'EU', 'tcp');?>" title="<?php _e( 'To select countries from the European Union', 'tcp' );?>" onclick="tcp_select_eu('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp');?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' );?>" onclick="tcp_select_nafta('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp');?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' );?>" onclick="tcp_select_caricom('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp');?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' );?>" onclick="tcp_select_mercasur('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp');?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' );?>" onclick="tcp_select_can('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp');?>" title="<?php _e( 'To select countries from African Union', 'tcp' );?>" onclick="tcp_select_au('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp');?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' );?>" onclick="tcp_select_apec('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp');?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' );?>" onclick="tcp_select_asean('shipping_isos');" class="button-secondary"/>
		</div>
	<?php
	}

	function show_countries_for_billing() {
		global $thecartpress;
		$billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : array();?>
		<?php _e( 'All countries', 'tcp' );?>:&nbsp;<input type="checkbox" name="all_billing_isos" id="all_billing_isos" <?php checked( count( $billing_isos ), 0 );?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_billing_isos').hide(); jQuery('#billing_isos option').attr('selected', false);  } else { jQuery('.sel_billing_isos').show(); }"/>
		<br />
		<div class="sel_billing_isos" <?php if ( ! $billing_isos ) echo 'style="display:none;"';?>>
			<select id="billing_isos" name="tcp_settings[billing_isos][]" style="height:auto" size="8" multiple="true">
			<?php $countries = Countries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso;?>" <?php tcp_selected_multiple( $billing_isos, $item->iso );?>><?php echo $item->name;?></option>
			<?php endforeach;?>
			</select>
			<br/>
			<input type="button" value="<?php _e( 'EU', 'tcp');?>" title="<?php _e( 'To select countries from the European Union', 'tcp' );?>" onclick="tcp_select_eu('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp');?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' );?>" onclick="tcp_select_nafta('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp');?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' );?>" onclick="tcp_select_caricom('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp');?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' );?>" onclick="tcp_select_mercasur('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp');?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' );?>" onclick="tcp_select_can('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp');?>" title="<?php _e( 'To select countries from African Union', 'tcp' );?>" onclick="tcp_select_au('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp');?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' );?>" onclick="tcp_select_apec('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp');?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' );?>" onclick="tcp_select_asean('billing_isos');" class="button-secondary"/>
		</div>
	<?php
	}

	function show_tax_section() {
	}

	function show_default_tax_country() {
		global $thecartpress;
		$default_tax_country = isset( $thecartpress->settings['default_tax_country'] ) ? $thecartpress->settings['default_tax_country'] : '';
		if ( $default_tax_country == '' )
			$$default_tax_country = isset( $thecartpress->settings['country'] ) ? $thecartpress->settings['country'] : '';
		$billing_isos	= isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : array();
		$shipping_isos	= isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : array();
		$isos			= array_merge( $billing_isos, $shipping_isos );?>
		<select id="default_tax_country" name="tcp_settings[default_tax_country]">
		<?php if ( is_array( $isos ) && count( $isos ) > 0 ) {
			$countries = Countries::getSome( $isos );
		} else {
			$countries = Countries::getAll();
		}
		foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso;?>" <?php selected( $item->iso, $default_tax_country );?>><?php echo $item->name;?></option>
		<?php endforeach;?>
		</select>
		<?php
	}

	function show_prices_include_tax() { //Catalog prices (merchant imputs) include tax: yes or not
		global $thecartpress;
		$prices_include_tax = isset( $thecartpress->settings['prices_include_tax'] ) ? $thecartpress->settings['prices_include_tax'] : false;?>
		<input type="checkbox" id="prices_include_tax" name="tcp_settings[prices_include_tax]" value="yes" <?php checked( $prices_include_tax, true );?> /><?php
	}

	function show_tax_based_on() {//Tax Based On: shipping address , billing address, shipping origin
		global $thecartpress;
		$tax_based_on = isset( $thecartpress->settings['tax_based_on'] ) ? $thecartpress->settings['tax_based_on'] : '';?>
		<select id="tax_based_on" name="tcp_settings[tax_based_on]">
			<option value="origin" <?php selected( 'origin', $tax_based_on );?>><?php _e( 'Default tax country', 'tcp' );?></option>
			<option value="billing" <?php selected( 'billing', $tax_based_on );?>><?php _e( 'Billing address', 'tcp' );?></option>
			<option value="shipping" <?php selected( 'shipping', $tax_based_on );?>><?php _e( 'Shipping address', 'tcp' );?></option>
		</select>
		<?php
	}

	function show_shipping_cost_include_tax() { //Shipping cost include tax: yes or not
		global $thecartpress;
		$shipping_cost_include_tax = isset( $thecartpress->settings['shipping_cost_include_tax'] ) ? $thecartpress->settings['shipping_cost_include_tax'] : false;?>
		<input type="checkbox" id="shipping_cost_include_tax" name="tcp_settings[shipping_cost_include_tax]" value="yes" <?php checked( $shipping_cost_include_tax, true );?> /><?php
	}

	function show_tax_for_shipping() { //Tax Class for Shipping: select tax
		global $thecartpress;
		$tax_for_shipping = isset( $thecartpress->settings['tax_for_shipping'] ) ? $thecartpress->settings['tax_for_shipping'] : '';?>
		<select id="tax_for_shipping" name="tcp_settings[tax_for_shipping]">
			<option value="0"><?php _e( 'No tax', 'tcp' );?></option>
		<?php 
		require_once( dirname( dirname( __FILE__ ) ) . '/daos/Taxes.class.php' );
		$taxes = Taxes::getAll();
		foreach ( $taxes as $tax ) : ?>
			<option value="<?php echo $tax->tax_id;?>" <?php selected( $tax->tax_id, $tax_for_shipping );?>><?php echo $tax->title;?></option>
		<?php endforeach;?>
		</select><?php
	}

	function show_apply_tax_after_discount() { //Apply Tax After Discount: yes or not
		global $thecartpress;
		$apply_tax_after_discount = isset( $thecartpress->settings['apply_tax_after_discount'] ) ? $thecartpress->settings['apply_tax_after_discount'] : false;?>
		<input type="checkbox" id="apply_tax_after_discount" name="tcp_settings[apply_tax_after_discount]" value="yes" <?php checked( $apply_tax_after_discount, true );?> /><?php
	}

	function show_apply_discount_on_prices_including_tax() { //Apply Discount On Prices Including Tax: yes or not (Ignored when ‘Apply Tax After Discount’ option is enabled)
		global $thecartpress;
		$apply_discount_on_prices_including_tax = isset( $thecartpress->settings['apply_discount_on_prices_including_tax'] ) ? $thecartpress->settings['apply_discount_on_prices_including_tax'] : false;?>
		<input type="checkbox" id="apply_discount_on_prices_including_tax" name="tcp_settings[apply_discount_on_prices_including_tax]" value="yes" <?php checked( $apply_discount_on_prices_including_tax, true );?> /><?php
	}

	function show_display_prices_with_taxes() {//Display Product Prices with taxes: yes or not
		global $thecartpress;
		$display_prices_with_taxes = isset( $thecartpress->settings['display_prices_with_taxes'] ) ? $thecartpress->settings['display_prices_with_taxes'] : false;?>
		<input type="checkbox" id="display_prices_with_taxes" name="tcp_settings[display_prices_with_taxes]" value="yes" <?php checked( $display_prices_with_taxes, true );?> /><?php
	}

	function show_display_Cart_Order_prices() {//Display Cart/Order Prices: including tax or excluding tax or Including and excluding tax
	}

	function show_display_full_tax_summary() {//Display full tax summary: yes or not
		global $thecartpress;
		$display_full_tax_summary = isset( $thecartpress->settings['display_full_tax_summary'] ) ? $thecartpress->settings['display_full_tax_summary'] : false;?>
		<input type="checkbox" id="display_full_tax_summary" name="tcp_settings[display_full_tax_summary]" value="yes" <?php checked( $display_full_tax_summary, true );?> /><?php	
	}

	function show_display_shipping_cost_with_taxes() {//Display Shipping Prices with taxes: yes or not
		global $thecartpress;
		$display_shipping_cost_with_taxes = isset( $thecartpress->settings['display_shipping_cost_with_taxes'] ) ? $thecartpress->settings['display_shipping_cost_with_taxes'] : false;?>
		<input type="checkbox" id="display_shipping_cost_with_taxes" name="tcp_settings[display_shipping_cost_with_taxes]" value="yes" <?php checked( $display_shipping_cost_with_taxes, true );?> /><?php	
	}

	function show_display_zero_tax_subtotal() { //Display Zero Tax Subtotal: yes or not
		global $thecartpress;
		$display_zero_tax_subtotal = isset( $thecartpress->settings['display_zero_tax_subtotal'] ) ? $thecartpress->settings['display_zero_tax_subtotal'] : false;?>
		<input type="checkbox" id="display_zero_tax_subtotal" name="tcp_settings[display_zero_tax_subtotal]" value="yes" <?php checked( $display_zero_tax_subtotal, true );?> /><?php	
	}

	function show_checkout_section() {
	}

	function show_legal_notice() {
		global $thecartpress;
		$legal_notice = isset( $thecartpress->settings['legal_notice'] ) ? $thecartpress->settings['legal_notice'] : __( 'Checkout notice', 'tcp' );?>
		<textarea id="legal_notice" name="tcp_settings[legal_notice]" cols="40" rows="5" maxlength="1020"><?php echo $legal_notice;?></textarea>
		<br /><span class="description"><?php _e( 'If the checkout notice is blank, the Checkout page will try to use the template class called "tcp_checkout_notice"', 'tcp' );?></span>
		<br /><span class="description"><?php _e( 'The template class allows to create a multilingual notice', 'tcp' );?></span>
		<br /><span class="description"><?php _e( 'If the checkout notice is blank and no template is assigned, then the Checkout page will not show the "Accept conditions" check.', 'tcp' );?></span><?php
	}

	function show_checkout_successfully_message() {
		global $thecartpress;
		$checkout_successfully_message = isset( $thecartpress->settings['checkout_successfully_message'] ) ? $thecartpress->settings['checkout_successfully_message'] : __( 'The order has been completed successfully', 'tcp' );?>
		<textarea id="checkout_successfully_message" name="tcp_settings[checkout_successfully_message]" cols="40" rows="5" maxlength="1020"><?php echo $checkout_successfully_message;?></textarea>
		<br /><span class="description"><?php _e( 'This text will show at the end of the checkout process.', 'tcp' );?></span><?php
	}

	function show_permalink_section() {
		$content = apply_filters( 'tcp_permalink_section', '' );
		if ( strlen( $content ) > 0 )
			echo '<span class="description">', $content, '</span>';
	}

	function show_product_rewrite() {
		global $thecartpress;
		$product_rewrite = isset( $thecartpress->settings['product_rewrite'] ) ? $thecartpress->settings['product_rewrite'] : 'product';?>
		<input type="text" id="product_rewrite" name="tcp_settings[product_rewrite]" value="<?php echo $product_rewrite;?>" size="50" maxlength="255" /><?php
	}

	function show_category_rewrite() {
		global $thecartpress;
		$category_rewrite = isset( $thecartpress->settings['category_rewrite'] ) ? $thecartpress->settings['category_rewrite'] : 'product_category';?>
		<input type="text" id="category_rewrite" name="tcp_settings[category_rewrite]" value="<?php echo $category_rewrite;?>" size="50" maxlength="255" />
		<br /><span class="description"><?php printf( __( 'Category base for post is "%s". Remember to set a different value.', 'tcp' ), get_option( 'category_base' ) );?></span><?php
	}

	function show_tag_rewrite() {
		global $thecartpress;
		$tag_rewrite = isset( $thecartpress->settings['tag_rewrite'] ) ? $thecartpress->settings['tag_rewrite'] : 'product_tag';?>
		<input type="text" id="tag_rewrite" name="tcp_settings[tag_rewrite]" value="<?php echo $tag_rewrite;?>" size="50" maxlength="255" />
		<br /><span class="description"><?php printf( __( 'Tag base for post is "%s". Remember to set a different value.', 'tcp' ), get_option( 'tag_base' ) );?></span><?php
	}

	function show_supplier_rewrite() {
		global $thecartpress;
		$supplier_rewrite = isset( $thecartpress->settings['supplier_rewrite'] ) ? $thecartpress->settings['supplier_rewrite'] : 'product_supplier';?>
		<input type="text" id="supplier_rewrite" name="tcp_settings[supplier_rewrite]" value="<?php echo $supplier_rewrite;?>" size="50" maxlength="255" /><?php
	}

	function show_theme_compatibility_section() {
		global $thecartpress;
		$content = __( 'You can uncheck all these options if your theme uses the <a href="http://thecartpress.com" target="_blank">TheCartPress template functions</a>.', 'tcp' );
		$content = apply_filters( 'tcp_theme_compatibility_section', $content );
		echo '<span class="description">', $content, '</span>';
	}

	function show_use_tcp_loops() {
		global $thecartpress;
		$use_tcp_loops = isset( $thecartpress->settings['use_tcp_loops'] ) ? $thecartpress->settings['use_tcp_loops'] : true;?>
		<input type="checkbox" id="use_tcp_loops" name="tcp_settings[use_tcp_loops]" value="yes" <?php checked( true, $use_tcp_loops );?> />
		<br /><span><?php _e( 'To show the Loops Settings of TheCartPress', 'tcp' );?></span><?php
	}

	function show_load_default_buy_button_style() {
		global $thecartpress;
		$load_default_buy_button_style = isset( $thecartpress->settings['load_default_buy_button_style'] ) ? $thecartpress->settings['load_default_buy_button_style'] : true;?>
		<input type="checkbox" id="load_default_buy_button_style" name="tcp_settings[load_default_buy_button_style]" value="yes" <?php checked( true, $load_default_buy_button_style );?> /><?php
	}

	function show_load_default_shopping_cart_checkout_style() {
		global $thecartpress;
		$load_default_shopping_cart_checkout_style = isset( $thecartpress->settings['load_default_shopping_cart_checkout_style'] ) ? $thecartpress->settings['load_default_shopping_cart_checkout_style'] : true;?>
		<input type="checkbox" id="load_default_shopping_cart_checkout_style" name="tcp_settings[load_default_shopping_cart_checkout_style]" value="yes" <?php checked( true, $load_default_shopping_cart_checkout_style );?> /><?php
	}

	function show_load_default_loop_style() {
		global $thecartpress;
		$load_default_loop_style = isset( $thecartpress->settings['load_default_loop_style'] ) ? $thecartpress->settings['load_default_loop_style'] : true;?>
		<input type="checkbox" id="load_default_loop_style" name="tcp_settings[load_default_loop_style]" value="yes" <?php checked( true, $load_default_loop_style );?> /><?php
	}

	function show_products_per_page() {
		global $thecartpress;
		$products_per_page = isset( $thecartpress->settings['products_per_page'] ) ? $thecartpress->settings['products_per_page'] : '10';?>
		<input type="text" id="products_per_page" name="tcp_settings[products_per_page]" value="<?php echo $products_per_page;?>" class="small-text" maxlength="4" /><?php
		_e( 'products', 'tcp');
	}

	function show_see_buy_button_in_content() {
		global $thecartpress;
		$see_buy_button_in_content = isset( $thecartpress->settings['see_buy_button_in_content'] ) ? $thecartpress->settings['see_buy_button_in_content'] : true;?>
		<input type="checkbox" id="see_buy_button_in_content" name="tcp_settings[see_buy_button_in_content]" value="yes" <?php checked( true, $see_buy_button_in_content );?> /><?php
	}

	function show_align_buy_button_in_content() {
		global $thecartpress;
		$align_buy_button_in_content = isset( $thecartpress->settings['align_buy_button_in_content'] ) ? $thecartpress->settings['align_buy_button_in_content'] : 'north';?>
		<select id="see_buy_button_in_content" name="tcp_settings[align_buy_button_in_content]">
			<option value="north" <?php selected( 'north', $align_buy_button_in_content );?>><?php _e( 'North', 'tcp' );?></option>
			<option value="south" <?php selected( 'south', $align_buy_button_in_content );?>><?php _e( 'South', 'tcp' );?></option><?php
	}

	function show_see_buy_button_in_excerpt() {
		global $thecartpress;
		$see_buy_button_in_excerpt = isset( $thecartpress->settings['see_buy_button_in_excerpt'] ) ? $thecartpress->settings['see_buy_button_in_excerpt'] : false;?>
		<input type="checkbox" id="see_buy_button_in_excerpt" name="tcp_settings[see_buy_button_in_excerpt]" value="yes" <?php checked( true, $see_buy_button_in_excerpt );?> /><?php
	}

	function show_align_buy_button_in_excerpt() {
		global $thecartpress;
		$align_buy_button_in_excerpt = isset( $thecartpress->settings['align_buy_button_in_excerpt'] ) ? $thecartpress->settings['align_buy_button_in_excerpt'] : 'north';?>
		<select id="see_buy_button_in_excerpt" name="tcp_settings[align_buy_button_in_excerpt]">
			<option value="north" <?php selected( 'north', $align_buy_button_in_excerpt );?>><?php _e( 'North', 'tcp' );?></option>
			<option value="south" <?php selected( 'south', $align_buy_button_in_excerpt );?>><?php _e( 'South', 'tcp' );?></option><?php
	}

	function show_see_price_in_content() {
		global $thecartpress;
		$see_price_in_content = isset( $thecartpress->settings['see_price_in_content'] ) ? $thecartpress->settings['see_price_in_content'] : false;?>
		<input type="checkbox" id="see_price_in_content" name="tcp_settings[see_price_in_content]" value="yes" <?php checked( true, $see_price_in_content );?> /><?php
	}

	function show_see_price_in_excerpt() {
		global $thecartpress;
		$see_price_in_excerpt = isset( $thecartpress->settings['see_price_in_excerpt'] ) ? $thecartpress->settings['see_price_in_excerpt'] : false;?>
		<input type="checkbox" id="see_price_in_excerpt" name="tcp_settings[see_price_in_excerpt]" value="yes" <?php checked( true, $see_price_in_excerpt );?> /><?php
	}

	function show_see_image_in_content() {
		global $thecartpress;
		$see_image_in_content = isset( $thecartpress->settings['see_image_in_content'] ) ? $thecartpress->settings['see_image_in_content'] : false;?>
		<input type="checkbox" id="see_image_in_content" name="tcp_settings[see_image_in_content]" value="yes" <?php checked( true, $see_image_in_content );?> /><?php
	}

	function image_size_content() {
		global $thecartpress;
		$image_size_content = isset( $thecartpress->settings['image_size_content'] ) ? $thecartpress->settings['image_size_content'] : 'thumbnail';
		$image_sizes = get_intermediate_image_sizes();?>
		<select id="image_size_content" name="tcp_settings[image_size_content]">
		<?php foreach( $image_sizes as $image_size ) : ?>
			<option value="<?php echo $image_size;?>" <?php selected( $image_size, $image_size_content );?>><?php echo $image_size;?></option>
		<?php endforeach;?>
		</select><?php
	}

	function image_align_content() {
		global $thecartpress;
		$image_align_content = isset( $thecartpress->settings['image_align_content'] ) ? $thecartpress->settings['image_align_content'] : false;?>
		<select id="image_align_content" name="tcp_settings[image_align_content]">
			<option value="" <?php selected( '', $image_align_content );?>><?php _e( 'None', 'tcp' );?></option>
			<option value="alignleft" <?php selected( 'alignleft', $image_align_content );?>><?php _e( 'Align Left', 'tcp' );?></option>
			<option value="aligncenter" <?php selected( 'aligncenter', $image_align_content );?>><?php _e( 'Align Center', 'tcp' );?></option>
			<option value="alignright" <?php selected( 'alignright', $image_align_content );?>><?php _e( 'Align Right', 'tcp' );?></option>
		</select><?php
	}

	function image_link_content() {
		global $thecartpress;
		$image_link_content = isset( $thecartpress->settings['image_link_content'] ) ? $thecartpress->settings['image_link_content'] : false;?>
		<select id="image_link_content" name="tcp_settings[image_link_content]">
			<option value="" <?php selected( '', $image_link_content );?>><?php _e( 'None', 'tcp' );?></option>
			<option value="file" <?php selected( 'file', $image_link_content );?>><?php _e( 'File url', 'tcp' );?></option>
			<option value="post" <?php selected( 'post', $image_link_content );?>><?php _e( 'Post url', 'tcp' );?></option>
		</select><?php
	}

	function show_see_image_in_excerpt() {
		global $thecartpress;
		$see_image_in_excerpt = isset( $thecartpress->settings['see_image_in_excerpt'] ) ? $thecartpress->settings['see_image_in_excerpt'] : false;?>
		<input type="checkbox" id="see_image_in_excerpt" name="tcp_settings[see_image_in_excerpt]" value="yes" <?php checked( true, $see_image_in_excerpt );?> /><?php
	}

	function image_size_excerpt() {
		global $thecartpress;
		$image_size_excerpt = isset( $thecartpress->settings['image_size_excerpt'] ) ? $thecartpress->settings['image_size_excerpt'] : 'thumbnail';
		$image_sizes = get_intermediate_image_sizes();?>
		<select id="image_size_excerpt" name="tcp_settings[image_size_excerpt]">
		<?php foreach( $image_sizes as $image_size ) : ?>
			<option value="<?php echo $image_size;?>" <?php selected( $image_size, $image_size_excerpt );?>><?php echo $image_size;?></option>
		<?php endforeach;?>
		</select><?php
	}

	function image_align_excerpt() {
		global $thecartpress;
		$image_align_excerpt = isset( $thecartpress->settings['image_align_excerpt'] ) ? $thecartpress->settings['image_align_excerpt'] : false;?>
		<select id="image_align_excerpt" name="tcp_settings[image_align_excerpt]">
			<option value="" <?php selected( '', $image_align_excerpt );?>><?php _e( 'None', 'tcp' );?></option>
			<option value="alignleft" <?php selected( 'alignleft', $image_align_excerpt );?>><?php _e( 'Align Left', 'tcp' );?></option>
			<option value="aligncenter" <?php selected( 'aligncenter', $image_align_excerpt );?>><?php _e( 'Align Center', 'tcp' );?></option>
			<option value="alignright" <?php selected( 'alignright', $image_align_excerpt );?>><?php _e( 'Align Right', 'tcp' );?></option>
		</select><?php
	}

	function image_link_excerpt() {
		global $thecartpress;
		$image_link_excerpt = isset( $thecartpress->settings['image_link_excerpt'] ) ? $thecartpress->settings['image_link_excerpt'] : false;?>
		<select id="image_link_excerpt" name="tcp_settings[image_link_excerpt]">
			<option value="" <?php selected( '', $image_link_excerpt );?>><?php _e( 'None', 'tcp' );?></option>
			<option value="file" <?php selected( 'file', $image_link_excerpt );?>><?php _e( 'File url', 'tcp' );?></option>
			<option value="post" <?php selected( 'post', $image_link_excerpt );?>><?php _e( 'Post url', 'tcp' );?></option>
		</select><?php
	}

	function show_admin_section() {
	}

	function show_hide_visibles() {
		global $thecartpress;
		$hide_visibles = isset( $thecartpress->settings['hide_visibles'] ) ? $thecartpress->settings['hide_visibles'] : false;?>
		<input type="checkbox" id="hide_visibles" name="tcp_settings[hide_visibles]" value="yes" <?php checked( true, $hide_visibles );?> /><?php
	}

	function show_back_end_label() {
		global $thecartpress;
		$show_back_end_label = isset( $thecartpress->settings['show_back_end_label'] ) ? $thecartpress->settings['show_back_end_label'] : false;?>
		<input type="checkbox" id="show_back_end_label" name="tcp_settings[show_back_end_label]" value="yes" <?php checked( true, $show_back_end_label );?> /><?php
	}

	function show_search_engine_section() {?>
		<span class="description"><?php _e( '&nbsp;', 'tcp' );?></span><?php
	}

	function show_search_engine_activated() {
		global $thecartpress;
		$search_engine_activated = isset( $thecartpress->settings['search_engine_activated'] ) ? $thecartpress->settings['search_engine_activated'] : true;?>
		<input type="checkbox" id="search_engine_activated" name="tcp_settings[search_engine_activated]" value="yes" <?php checked( true, $search_engine_activated );?> /><?php
	}

	function validate( $input ) {
		$input['currency_layout']			= isset( $input['currency_layout'] ) ? $input['currency_layout'] : ''; //'%1$s%2$s (%3$s)';
		$input['decimal_point']				= isset( $input['decimal_point'] ) ? $input['decimal_point'] : '.';
		$input['thousands_separator']		= isset( $input['thousands_separator'] ) ? $input['thousands_separator'] : ',';
		$input['legal_notice']				=  wp_filter_nohtml_kses( isset( $input['legal_notice'] ) ? $input['legal_notice'] : '' );
		$input['from_email']				=  wp_filter_nohtml_kses( isset( $input['from_email'] ) ? $input['from_email'] : '' );
		$input['emails']					=  wp_filter_nohtml_kses( isset( $input['emails'] ) ? $input['emails'] : '' );
		$input['stock_management']			= isset( $input['stock_management'] ) ? $input['stock_management'] == 'yes' : false;
		$input['hide_downloadable_menu']	= isset( $input['hide_downloadable_menu'] ) ? $input['hide_downloadable_menu'] == 'yes' : false;
		$input['disable_ecommerce']			= isset( $input['disable_ecommerce'] ) ? $input['disable_ecommerce'] == 'yes' : false;
		if ( $input['disable_ecommerce'] )
			$input['disable_shopping_cart'] = true;
		else
			$input['disable_shopping_cart']	= isset( $input['disable_shopping_cart'] ) ? $input['disable_shopping_cart'] == 'yes' : false;
		$input['enabled_wish_list']			= isset( $input['enabled_wish_list'] ) ? $input['enabled_wish_list'] == 'yes' : false;
		$input['user_registration']			= isset( $input['user_registration'] ) ? $input['user_registration'] == 'yes' : false;

		if ( isset( $input['all_shipping_isos'] ) && $input['all_shipping_isos'] == 'yes' ) $input['shipping_isos'] = array();
		if ( isset( $input['all_billing_isos'] ) && $input['all_billing_isos'] == 'yes' ) $input['billing_isos'] = array();
		
		$input['prices_include_tax']		= isset( $input['prices_include_tax'] ) ? $input['prices_include_tax'] == 'yes' : false;
		$input['shipping_cost_include_tax']	= isset( $input['shipping_cost_include_tax'] ) ? $input['shipping_cost_include_tax'] == 'yes' : false;
		$input['apply_tax_after_discount']	= isset( $input['apply_tax_after_discount'] ) ? $input['apply_tax_after_discount'] == 'yes' : false;
		$input['apply_discount_on_prices_including_tax'] = isset( $input['apply_discount_on_prices_including_tax'] ) ? $input['apply_discount_on_prices_including_tax'] == 'yes' : false;

		$input['display_prices_with_taxes']	= isset( $input['display_prices_with_taxes'] ) ? $input['display_prices_with_taxes'] == 'yes' : false;
		$input['display_Cart_Order_prices']	= isset( $input['display_Cart_Order_prices'] ) ? $input['display_Cart_Order_prices'] == 'yes' : false;
		$input['display_shipping_cost_with_taxes']	= isset( $input['display_shipping_cost_with_taxes'] ) ? $input['display_shipping_cost_with_taxes'] == 'yes' : false;
		$input['display_full_tax_summary']	= isset( $input['display_full_tax_summary'] ) ? $input['display_full_tax_summary'] == 'yes' : false;
		$input['display_zero_tax_subtotal']	= isset( $input['display_zero_tax_subtotal'] ) ? $input['display_zero_tax_subtotal'] == 'yes' : false;

		$input['products_per_page']			= isset( $input['products_per_page'] ) ? (int)$input['products_per_page'] : 10;
		$input['see_buy_button_in_content']	= isset( $input['see_buy_button_in_content'] ) ? $input['see_buy_button_in_content'] == 'yes' : false;
		$input['see_buy_button_in_excerpt']	= isset( $input['see_buy_button_in_excerpt'] ) ? $input['see_buy_button_in_excerpt'] == 'yes' : false;
		$input['see_price_in_content']		= isset( $input['see_price_in_content'] ) ? $input['see_price_in_content'] == 'yes' : false;
		$input['see_price_in_excerpt']		= isset( $input['see_price_in_excerpt'] ) ? $input['see_price_in_excerpt'] == 'yes' : false;
		$input['see_image_in_content']		= isset( $input['see_image_in_content'] ) ? $input['see_image_in_content'] == 'yes' : false;
		$input['see_image_in_excerpt']		= isset( $input['see_image_in_excerpt'] ) ? $input['see_image_in_excerpt'] == 'yes' : false;
		$input['downloadable_path']			= wp_filter_nohtml_kses( isset( $input['downloadable_path'] ) ? $input['downloadable_path'] : '' );
		$input['continue_url']				= wp_filter_nohtml_kses( isset( $input['continue_url'] ) ? $input['continue_url'] : '' );
		$input['product_rewrite']			= wp_filter_nohtml_kses( isset( $input['product_rewrite'] ) ? $input['product_rewrite'] : '' );
		$input['category_rewrite']			= wp_filter_nohtml_kses( isset( $input['category_rewrite'] ) ? $input['category_rewrite'] : '' );
		$input['tag_rewrite']				= wp_filter_nohtml_kses( isset( $input['tag_rewrite'] ) ? $input['tag_rewrite'] : '' );
		$input['supplier_rewrite']			= wp_filter_nohtml_kses( isset( $input['supplier_rewrite'] ) ? $input['supplier_rewrite'] : '' );
		$input['use_tcp_loops']				= isset( $input['use_tcp_loops'] ) ? $input['use_tcp_loops'] == 'yes' : false;
		$input['checkout_successfully_message']				= wp_filter_nohtml_kses( isset( $input['checkout_successfully_message'] ) ? $input['checkout_successfully_message'] : '' );
		$input['load_default_buy_button_style']				= isset( $input['load_default_buy_button_style'] ) ? $input['load_default_buy_button_style'] == 'yes' : false;
		$input['load_default_shopping_cart_checkout_style']	= isset( $input['load_default_shopping_cart_checkout_style'] ) ? $input['load_default_shopping_cart_checkout_style'] == 'yes' : false;
		$input['load_default_loop_style']					= isset( $input['load_default_loop_style'] ) ? $input['load_default_loop_style'] == 'yes' : false;
		$input['hide_visibles']				= isset( $input['hide_visibles'] ) ? $input['hide_visibles'] == 'yes' : false;
		$input['show_back_end_label']		= isset( $input['show_back_end_label'] ) ? $input['show_back_end_label'] == 'yes' : false;
		$input['search_engine_activated']	= isset( $input['search_engine_activated'] ) ? $input['search_engine_activated'] == 'yes' : false;
		//validations
		if ( $input['decimal_point'] == '' ) {
			if ( $input['thousands_separator'] == '.' ) {
				$input['decimal_point'] = ',';
			} else {
				$input['decimal_point'] = '.';
			}
		} elseif ( $input['decimal_point'] == $input['thousands_separator'] ) {
			if ( $input['thousands_separator'] == '.' ) {
				$input['decimal_point'] = ',';
			} else {
				$input['decimal_point'] = '.';
			}
		}
		if ( get_option( 'category_base' ) == $input['category_rewrite'] ) $input['category_rewrite'] = 'tcp_' . $input['category_rewrite'];
		if ( get_option( 'tag_base' ) == $input['tag_rewrite'] ) $input['tag_rewrite'] = 'tcp_' . $input['tag_rewrite'];
		$input = apply_filters( 'tcp_validate_settings', $input );
		return $input;
	}
}
?>
