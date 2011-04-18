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

class TCP_LoopsSettings {

	function __construct() {
		if ( is_admin() ) {
			$settings = get_option( 'tcp_settings' );
			if ( isset( $settings['use_tcp_loops'] ) && $settings['use_tcp_loops'] ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				//add_filter( 'contextual_help', array( $this, 'contextual_help' ) , 10, 3 );
			}
		}
	}

	function contextual_help( $contextual_help, $screen_id, $screen ) {
		if ( $screen_id == 'thecartpress_page_tcp_loopssettings_page' ) {
			$contextual_help = 'This is where I would provide help to the user on how everything in my admin panel works. Formatted HTML works fine in here too.';
		}
		return $contextual_help;
	}

	function admin_init() {
		register_setting( 'twentytencart_options', 'ttc_settings', array( $this, 'validate' ) );
		add_settings_section( 'main_section', __( 'Main settings', 'tcp' ) , array( $this, 'show_main_section' ), __FILE__ );
		add_settings_field( 'see_title', __( 'See title:', 'tcp' ), array( $this, 'see_title' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_image', __( 'See image:', 'tcp' ), array( $this, 'see_image' ), __FILE__ , 'main_section' );
		add_settings_field( 'image_size', __( 'Image size:', 'tcp' ), array( $this, 'image_size' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_excerpt', __( 'See excerpt:', 'tcp' ), array( $this, 'see_excerpt' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_content', __( 'See content:', 'tcp' ), array( $this, 'see_content' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_author', __( 'See author:', 'tcp' ), array( $this, 'see_author' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_price', __( 'See price:', 'tcp' ), array( $this, 'see_price' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_buy_button', __( 'See buy button:', 'tcp' ), array( $this, 'see_buy_button' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_meta_data', __( 'See meta data:', 'tcp' ), array( $this, 'see_meta_data' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_meta_utilities', __( 'See meta utilities:', 'tcp' ), array( $this, 'see_meta_utilities' ), __FILE__ , 'main_section' );
		add_settings_field( 'columns', __( 'Columns:', 'tcp' ), array( $this, 'columns' ), __FILE__ , 'main_section' );	
		add_settings_field( 'see_first_custom_area', __( 'See first custom area', 'tcp' ), array( $this, 'see_first_custom_area' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_second_custom_area', __( 'See second custom area', 'tcp' ), array( $this, 'see_second_custom_area' ), __FILE__ , 'main_section' );
		add_settings_field( 'see_third_custom_area', __( 'See third custom area', 'tcp' ), array( $this, 'see_third_custom_area' ), __FILE__ , 'main_section' );
	}

	function admin_menu() {
		$base = dirname( dirname( __FILE__ ) ) . '/admin/OrdersList.php';
		add_submenu_page( $base, __( 'TCP Loops settings', 'tcp' ), __( 'Loops Settings', 'tcp' ), 'tcp_edit_settings', 'ttc_settings_page', array( $this, 'show_settings' ) );
	}

	function show_settings() {?>
		<div class="wrap">
			<h2><?php _e( 'TCP Loop Settings', 'tcp' );?></h2>
			<form method="post" action="options.php">
				<?php settings_fields('twentytencart_options'); ?>
				<?php do_settings_sections(__FILE__); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'tcp') ?>" />
				</p>
			</form>
		</div><?php
	}

	function show_main_section() {
	}

	function see_title() {
		$settings = get_option( 'ttc_settings' );
		$see_title = isset( $settings['see_title'] ) ? $settings['see_title'] : true;?>
		<input type="checkbox" name="ttc_settings[see_title]" id="see_title" value="yes" <?php checked( $see_title, true );?> /><?php
	}

	function see_image() {
		$settings = get_option( 'ttc_settings' );
		$see_image = isset( $settings['see_image'] ) ? $settings['see_image'] : true;?>
		<input type="checkbox" name="ttc_settings[see_image]" id="see_image" value="yes" <?php checked( $see_image, true );?> /><?php
	}

	function image_size() {
		$settings = get_option( 'ttc_settings' );
		$image_size = isset( $settings['image_size'] ) ? $settings['image_size'] : 'thumbnail';?>
		<select id="image_size" name="ttc_settings[image_size]"><?php
		$imageSizes = get_intermediate_image_sizes();
		foreach( $imageSizes as $imageSize ) : ?>
			<option value="<?php echo $imageSize;?>" <?php selected( $imageSize, $image_size );?>><?php echo $imageSize;?></option>
		<?php endforeach;?>
		</select>
		<?php
	}

	function see_excerpt() {
		$settings = get_option( 'ttc_settings' );
		$see_excerpt = isset( $settings['see_excerpt'] ) ? $settings['see_excerpt'] : true;?>
		<input type="checkbox" name="ttc_settings[see_excerpt]" id="see_excerpt" value="yes" <?php checked( $see_excerpt, true );?> /><?php
	}

	function see_content() {
		$settings = get_option( 'ttc_settings' );
		$see_content = isset( $settings['see_content'] ) ? $settings['see_content'] : false;?>
		<input type="checkbox" name="ttc_settings[see_content]" id="see_content" value="yes" <?php checked( $see_content, true );?> /><?php
	}

	function see_author() {
		$settings = get_option( 'ttc_settings' );
		$see_author = isset( $settings['see_author'] ) ? $settings['see_author'] : false;?>
		<input type="checkbox" name="ttc_settings[see_author]" id="see_author" value="yes" <?php checked( $see_author, true );?> /><?php
	}

	function see_price() {
		$settings = get_option( 'ttc_settings' );
		$see_price = isset( $settings['see_price'] ) ? $settings['see_price'] : true;?>
		<input type="checkbox" name="ttc_settings[see_price]" id="see_price" value="yes" <?php checked( $see_price, true );?> /><?php
	}

	function see_buy_button() {
		$settings = get_option( 'ttc_settings' );
		$see_buy_button = isset( $settings['see_buy_button'] ) ? $settings['see_buy_button'] : false;?>
		<input type="checkbox" name="ttc_settings[see_buy_button]" id="see_buy_button" value="yes" <?php checked( $see_buy_button, true );?> /><?php
	}

	function see_meta_data() {
		$settings = get_option( 'ttc_settings' );
		$see_meta_data = isset( $settings['see_meta_data'] ) ? $settings['see_meta_data'] : false;?>
		<input type="checkbox" name="ttc_settings[see_meta_data]" id="see_meta_data" value="yes" <?php checked( $see_meta_data, true );?> /><?php
	}

	function see_meta_utilities() {
		$settings = get_option( 'ttc_settings' );
		$see_meta_utilities = isset( $settings['see_meta_utilities'] ) ? $settings['see_meta_utilities'] : false;?>
		<input type="checkbox" name="ttc_settings[see_meta_utilities]" id="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities, true );?> /><?php
	}

	function columns() {
		$settings = get_option( 'ttc_settings' );
		$columns = isset( $settings['columns'] ) ? (int)$settings['columns'] : 2;?>
		<input id="columns" name="ttc_settings[columns]" value="<?php echo $columns;?>" size="2" maxlength="2" type="text" /><?php
	}

	function see_first_custom_area() {
		$settings = get_option( 'ttc_settings' );
		$see_first_custom_area = isset( $settings['see_first_custom_area'] ) ? $settings['see_first_custom_area'] : false;?>
		<input type="checkbox" name="ttc_settings[see_first_custom_area]" id="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area, true );?> /><?php
	}

	function see_second_custom_area() {
		$settings = get_option( 'ttc_settings' );
		$see_second_custom_area = isset( $settings['see_second_custom_area'] ) ? $settings['see_second_custom_area'] : false;?>
		<input type="checkbox" name="ttc_settings[see_second_custom_area]" id="see_second_custom_area" value="yes" <?php checked( $see_second_custom_area, true );?> /><?php
	}

	function see_third_custom_area() {
		$settings = get_option( 'ttc_settings' );
		$see_third_custom_area = isset( $settings['see_third_custom_area'] ) ? $settings['see_third_custom_area'] : false;?>
		<input type="checkbox" name="ttc_settings[see_third_custom_area]" id="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area, true );?> /><?php
	}

	function validate( $input ) {
		$input['see_title']				= isset( $input['see_title'] ) ? $input['see_title']  == 'yes' : false;
		$input['see_image']				= isset( $input['see_image'] ) ? $input['see_image'] == 'yes' : false;
		$input['see_excerpt']			= isset( $input['see_excerpt'] ) ? $input['see_excerpt'] == 'yes' : false;
		$input['see_content']			= isset( $input['see_content'] ) ? $input['see_content'] == 'yes' : false;
		$input['see_author']			= isset( $input['see_author'] ) ? $input['see_author'] == 'yes' : false;
		$input['see_price']				= isset( $input['see_price'] ) ? $input['see_price'] == 'yes' : false;
		$input['see_buy_button']		= isset( $input['see_buy_button'] ) ? $input['see_buy_button']  == 'yes' : false;
		$input['see_meta_data']			= isset( $input['see_meta_data'] ) ? $input['see_meta_data']  == 'yes' : false;
		$input['see_meta_utilities']	= isset( $input['see_meta_utilities'] ) ? $input['see_meta_utilities']  == 'yes' : false;
		$input['columns']				= (int)$input['columns'];
		$input['see_first_custom_area']	= isset( $input['see_first_custom_area'] ) ? $input['see_first_custom_area']  == 'yes' : false;
		$input['see_second_custom_area']= isset( $input['see_second_custom_area'] ) ? $input['see_second_custom_area']  == 'yes' : false;
		$input['see_third_custom_area']	= isset( $input['see_third_custom_area'] ) ? $input['see_third_custom_area']  == 'yes' : false;
		return $input;
	}
}
?>
