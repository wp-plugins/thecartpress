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

class TCPThemeCompatibilitySettings {

	private $updated = false;

	function __construct() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Theme Compatibility', 'tcp' ), false, array( 'TCPThemeCompatibilitySettings', __FILE__ ), plugins_url( 'thecartpress/images/miranda/theme_settings_48.png' ) );
	}

	function admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_appearance();
		add_menu_page( '', __( 'TCP Look&Feel', 'tcp' ), 'tcp_edit_settings', $base, '', plugins_url( 'thecartpress/images/tcp.png', TCP_FOLDER ), 42 );
		$page = add_submenu_page( $base, __( 'Theme Compatibility Settings', 'tcp' ), __( 'Theme Compatibility', 'tcp' ), 'tcp_edit_settings', $base, array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
		    'id'      => 'overview',
		    'title'   => __( 'Overview' ),
		    'content' =>
	            '<p>' . __( 'You can customize Theme Compatibility.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-theme' ); ?><h2><?php _e( 'Theme Compatibility Settings', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
if ( isset( $_POST['current_post_type'] ) && strlen( trim( $_POST['current_post_type'] ) ) > 0 ) {
	$current_post_type = $_POST['current_post_type'];
	$suffix = '-' . $current_post_type;
} else {
	$suffix = '';
	$current_post_type = '';
}
$use_default_loop				= $thecartpress->get_setting( 'use_default_loop' . $suffix, 'only_settings' );
$load_default_buy_button_style	= $thecartpress->get_setting( 'load_default_buy_button_style' . $suffix, true );
$load_default_shopping_cart_checkout_style	= $thecartpress->get_setting( 'load_default_shopping_cart_checkout_style' . $suffix, true );
$load_default_loop_style		= $thecartpress->get_setting( 'load_default_loop_style' . $suffix, true );
$responsive_featured_thumbnails	= $thecartpress->get_setting( 'responsive_featured_thumbnails' . $suffix, true );
$products_per_page				= $thecartpress->get_setting( 'products_per_page' . $suffix, '10' );
$see_buy_button_in_content		= $thecartpress->get_setting( 'see_buy_button_in_content' . $suffix, true );
$align_buy_button_in_content	= $thecartpress->get_setting( 'align_buy_button_in_content' . $suffix, 'north' );
$see_price_in_content			= $thecartpress->get_setting( 'see_price_in_content' . $suffix );
$image_size_grouped_by_button	= $thecartpress->get_setting( 'image_size_grouped_by_button' . $suffix, 'thumbnail' );
$see_image_in_content			= $thecartpress->get_setting( 'see_image_in_content' . $suffix );
$image_size_content				= $thecartpress->get_setting( 'image_size_content', 'thumbnail' );
$image_align_content			= $thecartpress->get_setting( 'image_align_content' . $suffix );
$image_link_content				= $thecartpress->get_setting( 'image_link_content' . $suffix );
$see_buy_button_in_excerpt		= $thecartpress->get_setting( 'see_buy_button_in_excerpt' . $suffix );
$align_buy_button_in_excerpt	= $thecartpress->get_setting( 'align_buy_button_in_excerpt', 'north' );
$see_price_in_excerpt			= $thecartpress->get_setting( 'see_price_in_excerpt' . $suffix );
$see_image_in_excerpt			= $thecartpress->get_setting( 'see_image_in_excerpt' . $suffix );
$image_size_excerpt				= $thecartpress->get_setting( 'image_size_excerpt' . $suffix, 'thumbnail' );
$image_align_excerpt			= $thecartpress->get_setting( 'image_align_excerpt' . $suffix );
$image_link_excerpt				= $thecartpress->get_setting( 'image_link_excerpt' . $suffix ); ?>

<form method="post" action="">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
		<label for="current_post_type"><?php _e( 'Post type', 'tcp' ); ?></label>
	</th>
	<td>
		<?php $post_types = get_post_types( '', 'object' ); ?>

		<select id="current_post_type" name="current_post_type">
			<option value="" <?php selected( true, $current_post_type ); ?>><?php _e( 'Default', 'tcp'); ?></option>
			<?php foreach( $post_types as $i => $post_type ) : ?>
			<option value="<?php echo $i; ?>" <?php selected( $i, $current_post_type ); ?>
			<?php if ( $thecartpress->get_setting( 'use_default_loop-' . $i, false ) !== false ) : ?> style="font-weight: bold;"<?php endif; ?>
			>
			<?php echo $post_type->labels->singular_name; ?>
			</option>
			<?php endforeach; ?>
		</select>

		<input type="submit" name="load_post_type_settings" value="<?php _e( 'Load post type settings', 'tcp' ); ?>" class="button-secondary"/>
		<input type="submit" name="delete_post_type_settings" value="<?php _e( 'Delete post type settings', 'tcp' ); ?>" class="button-secondary"/>

		<p class="description"><?php _e( 'Allows to create different configuration for each Post Type.', 'tcp' ); ?></p>

		<span class="description"><?php _e( 'Options in bold have a specific configuration.', 'tcp' ); ?>
		<?php _e( 'Remember to save the changes before to load new post type settings.', 'tcp' ); ?>
		</span>

	</td>
</tr>

</tbody>
</table>

<h3><?php _e( 'TheCartPress Styles', 'tcp' ); ?></h3>

<p class="description"><?php _e( 'Allows to load default styles provided by TheCartPress. To create your own styles, deactivate these settings. You could, also, customise by copying these CSS files to your theme.', 'tcp' ); ?></p>

<div class="postbox">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
	<label for="load_default_buy_button_style"><?php _e( 'Load default buy button style', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_default_buy_button_style" name="load_default_buy_button_style" value="yes" <?php checked( true, $load_default_buy_button_style ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="load_default_shopping_cart_checkout_style"><?php _e( 'Load default shopping cart & checkout style', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_default_shopping_cart_checkout_style" name="load_default_shopping_cart_checkout_style" value="yes" <?php checked( true, $load_default_shopping_cart_checkout_style ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="load_default_loop_style"><?php _e( 'Load default loop style', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_default_loop_style" name="load_default_loop_style" value="yes" <?php checked( true, $load_default_loop_style ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="responsive_featured_thumbnails"><?php _e( 'Use responsive featured thumbnails', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="responsive_featured_thumbnails" name="responsive_featured_thumbnails" value="yes" <?php checked( true, $responsive_featured_thumbnails ); ?> />
		<p class="description"><?php _e( 'If this option is not checked the original image size or styles defined in your theme will be loaded', 'tcp' ); ?></p>
	</td>
</tr>

</tbody>
</table>

</div>

<h3><?php _e( 'Theme Compatibility', 'tcp'); ?></h3>

<div class="postbox">

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
		onclick="hide_excerpt();" /> <label for="use_default_loop_2011"><strong><?php _e( 'Use TCP default Templates (twentyeleven based)', 'tcp' ); ?></strong></label>
		<br/>

		<input type="radio" id="use_default_loop_2010" name="use_default_loop" value="yes_2010" <?php checked( 'yes_2010', $use_default_loop ); ?>
		onclick="hide_excerpt();" /> <label for="use_default_loop_2010"><strong><?php _e( 'Use TCP default Templates (twentyten based)', 'tcp' ); ?></strong></label>
		<p class="description"><?php _e( 'To show Product Pages with default/basic template provided by TheCartPress.', 'tcp' ); ?></p>
		<p class="description"><?php printf( __( 'If this setting is activated you must configure the grid using <a href="%s">Loop settings</a> menu.', 'tcp' ), add_query_arg( 'page', 'loop_settings', get_admin_url() . 'admin.php' ) ); ?></p>
		<p class="description"><?php _e( 'TheCartPress provides two version of default templates, one for themes based on "Twenty Eleven" and another for themes based on "Twenty Ten".', 'tcp' ); ?></p>

		<input type="radio" id="use_default_loop_none" name="use_default_loop" value="none" <?php checked( 'none', $use_default_loop ); ?> 
		onclick="show_excerpt();"/> <label for="use_default_loop_none"><strong><?php _e( 'None', 'tcp' ); ?></strong></label>
		<p class="description"><?php _e( 'Use your own templates. Total flexibility for developers and theme constructors.', 'tcp' ); ?></p>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="products_per_page"><?php _e( 'Product pages show at most', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="products_per_page" name="products_per_page" value="<?php echo $products_per_page; ?>" class="small-text tcp_count" maxlength="4"/>
		<?php _e( 'products', 'tcp'); ?>
	</td>
</tr>

</tbody>
</table>

</div>

<h3 class="hndle"><?php _e( 'How to display Single Content', 'tcp' ); ?></h3>

<div class="postbox">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
	<label for="see_buy_button_in_content"><?php _e( 'See buy button in content', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="see_buy_button_in_content" name="see_buy_button_in_content" value="yes" <?php checked( true, $see_buy_button_in_content ); ?> />
		<p class="description"><?php _e( 'Allows to show the buy button in the product description.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'The "in content" settings only can be activated if the product single template doesn\'t use the template tags to show data.', 'tcp' ); ?></p>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for=""><?php _e( 'Align of buy button in content', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="align_buy_button_in_content" name="align_buy_button_in_content">
			<option value="north" <?php selected( 'north', $align_buy_button_in_content ); ?>><?php _e( 'North', 'tcp' ); ?></option>
			<option value="south" <?php selected( 'south', $align_buy_button_in_content ); ?>><?php _e( 'South', 'tcp' ); ?></option>
			<option value="both" <?php selected( 'both', $align_buy_button_in_content ); ?>><?php _e( 'Both', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="see_price_in_content"><?php _e( 'See price in content', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="see_price_in_content" name="see_price_in_content" value="yes" <?php checked( true, $see_price_in_content ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="image_size_grouped_by_button"><?php _e( 'Image size grouped buy button', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="image_size_grouped_by_button" name="image_size_grouped_by_button">
			<option value="none" <?php selected( 'none', $image_size_grouped_by_button ); ?>><?php _e( 'No image', 'tcp' ); ?></option>
			<option value="thumbnail" <?php selected( 'thumbnail', $image_size_grouped_by_button ); ?>><?php _e( 'Thumbnail', 'tcp' ); ?></option>
			<option value="64" <?php selected( '64', $image_size_grouped_by_button ); ?>><?php _e( '64x64', 'tcp' ); ?></option>
			<option value="48" <?php selected( '48', $image_size_grouped_by_button ); ?>><?php _e( '48x48', 'tcp' ); ?></option>
			<option value="32" <?php selected( '32', $image_size_grouped_by_button ); ?>><?php _e( '32x32', 'tcp' ); ?></option>
		</select>
		<p class="description"><?php _e( 'allows to select the size of the image to show in the grouped products.', 'tcp' ); ?></p>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="see_image_in_content"><?php _e( 'See image in content', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="see_image_in_content" name="see_image_in_content" value="yes" <?php checked( true, $see_image_in_content ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="image_size_content"><?php _e( 'Image size in content', 'tcp' ); ?></label>
	</th>
	<td>
		<?php $image_sizes = get_intermediate_image_sizes(); ?>
		<select id="image_size_content" name="image_size_content">
		<?php foreach( $image_sizes as $image_size ) : ?>
			<option value="<?php echo $image_size; ?>" <?php selected( $image_size, $image_size_content ); ?>><?php echo $image_size; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="image_align_content"><?php _e( 'Image align in content', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="image_align_content" name="image_align_content">
			<option value="" <?php selected( '', $image_align_content ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="alignleft" <?php selected( 'alignleft', $image_align_content ); ?>><?php _e( 'Align Left', 'tcp' ); ?></option>
			<option value="aligncenter" <?php selected( 'aligncenter', $image_align_content ); ?>><?php _e( 'Align Center', 'tcp' ); ?></option>
			<option value="alignright" <?php selected( 'alignright', $image_align_content ); ?>><?php _e( 'Align Right', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="image_link_content"><?php _e( 'Image link in content', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="image_link_content" name="image_link_content">
			<option value="" <?php selected( '', $image_link_content ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="file" <?php selected( 'file', $image_link_content ); ?>><?php _e( 'File url', 'tcp' ); ?></option>
			<option value="post" <?php selected( 'post', $image_link_content ); ?>><?php _e( 'Post url', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

</table>
</tbody>

</div>

<div id="excerpt_content" class="postbox">

<h3><?php _e( 'How to display Loop Content', 'tcp' ); ?></h3>

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
	<label for="see_buy_button_in_excerpt"><?php _e( 'See buy button in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="see_buy_button_in_excerpt" name="see_buy_button_in_excerpt" value="yes" <?php checked( true, $see_buy_button_in_excerpt ); ?> />
		<p class="description"><?php _e( 'Allows to show the buy button in the product lists.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'The "in excerpt" settings only can be activated if the products template doesn\'t use the template tags to show data.', 'tcp' ); ?></p>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="align_buy_button_in_excerpt"><?php _e( 'Align of buy button in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="align_buy_button_in_excerpt" name="align_buy_button_in_excerpt">
			<option value="north" <?php selected( 'north', $align_buy_button_in_excerpt ); ?>><?php _e( 'North', 'tcp' ); ?></option>
			<option value="south" <?php selected( 'south', $align_buy_button_in_excerpt ); ?>><?php _e( 'South', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="see_price_in_excerpt"><?php _e( 'See price in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="see_price_in_excerpt" name="see_price_in_excerpt" value="yes" <?php checked( true, $see_price_in_excerpt ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="see_image_in_excerpt"><?php _e( 'See image in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="see_image_in_excerpt" name="see_image_in_excerpt" value="yes" <?php checked( true, $see_image_in_excerpt ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="see_image_in_excerpt"><?php _e( 'Image size in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<?php $image_sizes = get_intermediate_image_sizes(); ?>
		<select id="image_size_excerpt" name="image_size_excerpt">
		<?php foreach( $image_sizes as $image_size ) : ?>
			<option value="<?php echo $image_size; ?>" <?php selected( $image_size, $image_size_excerpt ); ?>><?php echo $image_size; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for=""><?php _e( 'Image align in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="image_align_excerpt" name="tcp_settings[image_align_excerpt]">
			<option value="" <?php selected( '', $image_align_excerpt ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="alignleft" <?php selected( 'alignleft', $image_align_excerpt ); ?>><?php _e( 'Align Left', 'tcp' ); ?></option>
			<option value="aligncenter" <?php selected( 'aligncenter', $image_align_excerpt ); ?>><?php _e( 'Align Center', 'tcp' ); ?></option>
			<option value="alignright" <?php selected( 'alignright', $image_align_excerpt ); ?>><?php _e( 'Align Right', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="image_link_excerpt"><?php _e( 'Image link in excerpt', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="image_link_excerpt" name="image_link_excerpt">
			<option value="" <?php selected( '', $image_link_excerpt ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="file" <?php selected( 'file', $image_link_excerpt ); ?>><?php _e( 'File url', 'tcp' ); ?></option>
			<option value="post" <?php selected( 'post', $image_link_excerpt ); ?>><?php _e( 'Post url', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

</tbody>
</table>

</div><!-- #excerpt_content -->

<?php do_action( 'tcp_theme_compatibility_settings_page', $suffix ); ?>

<script>
jQuery(document).ready( function() {
<?php global $thecartpress;
if ( $thecartpress->get_setting( 'use_default_loop', 'none' ) != 'none' ) : ?>
	hide_excerpt();
<?php endif;
if ( ! $thecartpress->get_setting( 'load_default_loop_style', true ) ) : ?>
	jQuery('#responsive_featured_thumbnails').parent().parent().hide();
<?php endif; ?>
	jQuery('#load_default_loop_style').click( function() {
		if ( jQuery(this).attr('checked') ) {
			jQuery('#responsive_featured_thumbnails').parent().parent().show();
		} else {
			jQuery('#responsive_featured_thumbnails').parent().parent().hide();
		}
	});
});

function hide_excerpt() {
	jQuery('#excerpt_content').hide();
	/*jQuery('#see_buy_button_in_excerpt').parent().parent().hide();
	jQuery('#align_buy_button_in_excerpt').parent().parent().hide();
	jQuery('#see_price_in_excerpt').parent().parent().hide();
	jQuery('#see_image_in_excerpt').parent().parent().hide();
	jQuery('#image_size_excerpt').parent().parent().hide();
	jQuery('#image_align_excerpt').parent().parent().hide();
	jQuery('#image_link_excerpt').parent().parent().hide();*/
}

function show_excerpt() {
	jQuery('#excerpt_content').show();
	/*jQuery('#see_buy_button_in_excerpt').parent().parent().show();
	jQuery('#align_buy_button_in_excerpt').parent().parent().show();
	jQuery('#see_price_in_excerpt').parent().parent().show();
	jQuery('#see_image_in_excerpt').parent().parent().show();
	jQuery('#image_size_excerpt').parent().parent().show();
	jQuery('#image_align_excerpt').parent().parent().show();
	jQuery('#image_link_excerpt').parent().parent().show();*/
}
</script>
<?php wp_nonce_field( 'tcp_theme_compatibility_settings' ); ?>
<?php submit_button( null, 'primary', 'save-theme_compatibility-settings' ); ?>
</form>
</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_theme_compatibility_settings' );
		if ( isset( $_POST['load_post_type_settings'] ) ) return;
		if ( strlen( $_POST['current_post_type'] ) > 0 ) $suffix = '-' . $_POST['current_post_type'];
		else $suffix = '';
		if ( isset( $_POST['delete_post_type_settings'] ) ) {
			if ( strlen( $suffix ) == 0 ) return;
			$settings = get_option( 'tcp_settings' );
			unset( $settings['use_default_loop' . $suffix] );
			unset( $settings['load_default_buy_button_style . $suffix'] );
			unset( $settings['load_default_shopping_cart_checkout_style' . $suffix] );
			unset( $settings['load_default_loop_style' . $suffix] );
			unset( $settings['responsive_featured_thumbnails' . $suffix] );
			unset( $settings['products_per_page' . $suffix] );
			unset( $settings['see_buy_button_in_content' . $suffix] );
			unset( $settings['align_buy_button_in_content' . $suffix] );
			unset( $settings['see_price_in_content' . $suffix] );
			unset( $settings['image_size_grouped_by_button' . $suffix] );
			unset( $settings['see_image_in_content' . $suffix] );
			unset( $settings['image_size_content' . $suffix] );
			unset( $settings['image_align_content' . $suffix] );
			unset( $settings['image_link_content' . $suffix] );
			unset( $settings['see_buy_button_in_excerpt' . $suffix] );
			unset( $settings['align_buy_button_in_excerpt' . $suffix] );
			unset( $settings['see_price_in_excerpt' . $suffix] );
			unset( $settings['see_image_in_excerpt' . $suffix] );
			unset( $settings['image_size_excerpt' . $suffix] );
			unset( $settings['image_align_excerpt' . $suffix] );
			unset( $settings['image_link_excerpt' . $suffix] );
			$settings = apply_filters( 'tcp_theme_compatibility_unset_settings_action', $settings, $suffix );
			update_option( 'tcp_settings', $settings );
			$this->updated = true;
			global $thecartpress;
			$thecartpress->load_settings();
			return;
		}
		$settings = get_option( 'tcp_settings' );
		$settings['use_default_loop' . $suffix]				= isset( $_POST['use_default_loop'] ) ? $_POST['use_default_loop'] : 'only_settings';
		$settings['load_default_buy_button_style' . $suffix]= isset( $_POST['load_default_buy_button_style'] ) ? $_POST['load_default_buy_button_style'] == 'yes' : false;
		$settings['load_default_shopping_cart_checkout_style' . $suffix]	= isset( $_POST['load_default_shopping_cart_checkout_style'] ) ? $_POST['load_default_shopping_cart_checkout_style'] == 'yes' : false;
		$settings['load_default_loop_style' . $suffix]		= isset( $_POST['load_default_loop_style'] ) ? $_POST['load_default_loop_style'] == 'yes' : false;
		if ( $settings['load_default_loop_style' . $suffix] ) $settings['responsive_featured_thumbnails']	= isset( $_POST['responsive_featured_thumbnails'] ) ? $_POST['responsive_featured_thumbnails'] == 'yes' : false;
		else $settings['responsive_featured_thumbnails' . $suffix] = false;
		$settings['products_per_page' . $suffix]			= isset( $_POST[ 'products_per_page' ] ) ? $_POST[ 'products_per_page' ] : false;
		$settings['see_buy_button_in_content' . $suffix]	= isset( $_POST['see_buy_button_in_content'] ) ? $_POST['see_buy_button_in_content'] == 'yes' : false;
		$settings['align_buy_button_in_content' . $suffix]	= isset( $_POST['align_buy_button_in_content'] ) ? $_POST['align_buy_button_in_content'] : false;		update_option( 'tcp_settings', $settings );
		$settings['see_price_in_content' . $suffix]			= isset( $_POST['see_price_in_content'] ); // ? $_POST['see_price_in_content'] == 'yes' : false;
		$settings['image_size_grouped_by_button' . $suffix]	= isset( $_POST['image_size_grouped_by_button'] ) ? $_POST['image_size_grouped_by_button'] : 'thumbnail';
		$settings['see_image_in_content' . $suffix]			= isset( $_POST['see_image_in_content'] ) ? $_POST['see_image_in_content'] == 'yes' : false;
		$settings['image_size_content' . $suffix]			= isset( $_POST['image_size_content'] ) ? $_POST['image_size_content'] : 'thumbnail';
		$settings['image_align_content' . $suffix]			= isset( $_POST['image_align_content'] ) ? $_POST['image_align_content'] : 'north';
		$settings['image_link_content' . $suffix]			= isset( $_POST['image_link_content'] ) ? $_POST['image_link_content'] : '';
		$settings['see_buy_button_in_excerpt' . $suffix]	= isset( $_POST['see_buy_button_in_excerpt'] ) ? $_POST['see_buy_button_in_excerpt'] == 'yes' : false;
		$settings['align_buy_button_in_excerpt' . $suffix]	= isset( $_POST['align_buy_button_in_excerpt'] ) ? $_POST['align_buy_button_in_excerpt'] : '';
		$settings['see_price_in_excerpt' . $suffix]			= isset( $_POST['see_price_in_excerpt'] ) ? $_POST['see_price_in_excerpt'] == 'yes' : false;
		$settings['see_image_in_excerpt' . $suffix]			= isset( $_POST['see_image_in_excerpt'] ) ? $_POST['see_image_in_excerpt'] == 'yes' : false;
		$settings['image_size_excerpt' . $suffix]			= isset( $_POST['image_size_excerpt'] ) ? $_POST['image_size_excerpt'] : 'thumbnail';
		$settings['image_align_excerpt' . $suffix]			= isset( $_POST['image_align_excerpt'] ) ? $_POST['image_align_excerpt'] : 'SOUTH';
		$settings['image_link_excerpt' . $suffix]			= isset( $_POST['image_link_excerpt'] ) ? $_POST['image_link_excerpt'] : '';
		$settings = apply_filters( 'tcp_theme_compatibility_settings_action', $settings, $suffix );
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPThemeCompatibilitySettings();
?>