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


class TCPBootStrap {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ), 9 );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'wp_footer', array( &$this, 'wp_footer' ) );
	}

	function init() {
		if ( is_admin() ) return;
		global $thecartpress;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
		if ( ! $disable_ecommerce )
			if ( $thecartpress->get_setting( 'load_bootstrap_css', false ) ) {
				wp_enqueue_style( 'bootstrap', plugins_url( 'thecartpress/css/bootstrap.min.css' ) );
			if ( $thecartpress->get_setting( 'load_bootstrap_responsive_css', false ) )
				wp_enqueue_style( 'bootstrap_responsive', plugins_url( 'thecartpress/css/bootstrap_responsive.min.css' ) );
		}
	}

	function admin_init() {
		add_action( 'tcp_theme_compatibility_settings_page_top', array( &$this, 'tcp_theme_compatibility_settings_page_top' ), 10, 2 );
		add_filter( 'tcp_theme_compatibility_settings_action', array( &$this, 'tcp_theme_compatibility_settings_action' ), 10, 2 );
	}

	function tcp_theme_compatibility_settings_page_top( $suffix, $thecartpress ) {
		$load_bootstrap_css = $thecartpress->get_setting( 'load_bootstrap_css' );
		$load_bootstrap_responsive_css = $thecartpress->get_setting( 'load_bootstrap_responsive_css' );
		$load_bootstrap_js = $thecartpress->get_setting( 'load_bootstrap_js' ); ?>
<h3><?php _e( 'Bootstrap', 'tcp' ); ?></h3>

<p class="description"><?php _e( 'Allows to load bootstrap styles and javascipt libraries. Useful for 21 century themes.', 'tcp' ); ?></p>

<div class="postbox">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
	<label for="load_bootstrap_css"><?php _e( 'Load Bootstrap CSS', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_bootstrap_css" name="load_bootstrap_css" value="yes" <?php checked( true, $load_bootstrap_css ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="load_bootstrap_responsive_css"><?php _e( 'Load Responsive CSS', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_bootstrap_responsive_css" name="load_bootstrap_responsive_css" value="yes" <?php checked( true, $load_bootstrap_responsive_css ); ?> />
	</td>
</tr>

<!--<tr valign="top">
	<th scope="row">
	<label for="load_bootstrap_custom_css"><?php _e( 'Load JS Libraries', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="textbox" id="load_bootstrap_custom_css" name="load_bootstrap_custom_css" value="<?php //echo $load_bootstrap_custom_css; ?>" />
	</td>
</tr>-->

<tr valign="top">
	<th scope="row">
	<label for="load_bootstrap_js"><?php _e( 'Load JS Libraries', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_bootstrap_js" name="load_bootstrap_js" value="yes" <?php checked( true, $load_bootstrap_js ); ?> />
	</td>
</tr>

</tbody>
</table>

</div>
<?php }

	function tcp_theme_compatibility_settings_action( $settings, $suffix ) {
		$settings['load_bootstrap_css'] = isset( $_POST['load_bootstrap_css'] );
		$settings['load_bootstrap_responsive_css'] = isset( $_POST['load_bootstrap_responsive_css'] );
		$settings['load_bootstrap_js'] = isset( $_POST['load_bootstrap_js'] );
		return $settings;
	}

	function wp_footer() {
		global $thecartpress;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
		if ( $disable_ecommerce ) return;
		if ( $thecartpress->get_setting( 'load_bootstrap_js', false ) ) {
			wp_register_script( 'tcp_bootstrap', plugins_url( 'thecartpress/js/bootstrap.min.js' ) );
			wp_enqueue_script( 'tcp_bootstrap' );
		}
	}
}

new TCPBootstrap();
