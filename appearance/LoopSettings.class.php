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

class TCPLoopSettings {

	function __construct() {
		$settings = get_option( 'tcp_settings' );
		if ( isset( $settings['use_default_loop'] ) ) {
			if ( $settings['use_default_loop'] != 'none' ) add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			if ( $settings['use_default_loop'] == 'yes' || $settings['use_default_loop'] == 'yes_2010' )
				add_filter( 'template_include', array( $this, 'template_include' ) );
		}
	}

	function admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_appearance();
		$page = add_submenu_page( $base, __( 'Loop Settings', 'tcp' ), __( 'Loop', 'tcp' ), 'tcp_edit_settings', 'loop_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
		    'id'      => 'overview',
		    'title'   => __( 'Overview' ),
		    'content' =>
	            '<p>' . __( 'You can customize your Theme Loops.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-loop-settings' ); ?><h2><?php _e( 'Loop Settings', 'tcp' ); ?></h2>

	<p class="description"><?php _e( 'Allows to configure how to display the products in the WordPress Loop', 'tcp' ); ?></p>

<?php $settings = get_option( 'ttc_settings' );
if ( isset( $settings['use_default_loop'] ) && $settings['use_default_loop'] == 'none' ) : 
	$url = add_query_arg( 'page', 'thecartpress/TheCartPress.class.php/appearance', get_admin_url() . 'admin.php' ); ?>

<p class="description"><?php _e( 'This admin panel is only available if the "Theme Templates" property is not equal to "none".', 'tcp' ); ?></p>
<p class="description"><?php printf( __( 'To modify this value you need to visit <a href="%s">Theme Compatibility Settings page</a>.', 'tcp' ), $url ); ?></p>
	
</div>

<?php else : ?>

	<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
		<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
	<?php endif; ?>

<?php $settings = get_option( 'ttc_settings' );

if ( isset( $_POST['current_post_type'] ) && strlen( trim( $_POST['current_post_type'] ) ) > 0 ) {
	$current_post_type = $_POST['current_post_type'];
	$suffix = '-' . $current_post_type;
} else {
	$suffix = '';
	$current_post_type = '';
}

$see_title				= isset( $settings['see_title' . $suffix ] ) ? $settings['see_title' . $suffix ] : true;
$title_tag				= isset( $settings['title_tag' . $suffix ] ) ? $settings['title_tag' . $suffix ] : 'h2';
$see_image				= isset( $settings['see_image' . $suffix ] ) ? $settings['see_image' . $suffix ] : true;
$image_size				= isset( $settings['image_size' . $suffix ] ) ? $settings['image_size' . $suffix ] : 'thumbnail';
$see_excerpt			= isset( $settings['see_excerpt' . $suffix ] ) ? $settings['see_excerpt' . $suffix ] : true;
$see_content			= isset( $settings['see_content' . $suffix ] ) ? $settings['see_content' . $suffix ] : false;
$see_author				= isset( $settings['see_author' . $suffix ] ) ? $settings['see_author' . $suffix ] : false;
$see_price				= isset( $settings['see_price' . $suffix ] ) ? $settings['see_price' . $suffix ] : false;
$see_buy_button			= isset( $settings['see_buy_button' . $suffix ] ) ? $settings['see_buy_button' . $suffix ] : true;
$see_posted_on			= isset( $settings['see_posted_on' . $suffix ] ) ? $settings['see_posted_on' . $suffix ] : false;
$see_taxonomies			= isset( $settings['see_taxonomies' . $suffix ] ) ? $settings['see_taxonomies' . $suffix ] : false;
$see_meta_utilities		= isset( $settings['see_meta_utilities' . $suffix ] ) ? $settings['see_meta_utilities' . $suffix ] : false;
$disabled_order_types	= isset( $settings['disabled_order_types' . $suffix ] ) ? $settings['disabled_order_types' . $suffix ] : array();
$order_type				= isset( $settings['order_type' . $suffix ] ) ? $settings['order_type' . $suffix ] : 'date';
$order_desc				= isset( $settings['order_desc' . $suffix ] ) ? $settings['order_desc' . $suffix ] : 'desc';
$see_sorting_panel		= isset( $settings['see_sorting_panel' . $suffix ] ) ? $settings['see_sorting_panel' . $suffix ] : false;
$columns				= isset( $settings['columns' . $suffix ] ) ? $settings['columns' . $suffix ] : 2;
$see_pagination			= isset( $settings['see_pagination' . $suffix ] ) ? $settings['see_pagination' . $suffix ] : false;
$see_first_custom_area	= isset( $settings['see_first_custom_area' . $suffix ] )  ? $settings['see_first_custom_area' . $suffix ] : false;
$see_second_custom_area	= isset( $settings['see_second_custom_area' . $suffix ] ) ? $settings['see_second_custom_area' . $suffix ] : false;
$see_third_custom_area	= isset( $settings['see_third_custom_area' . $suffix ] ) ? $settings['see_third_custom_area' . $suffix ] : false; ?>

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
			<?php if ( isset( $settings[ 'title_tag-' . $i] ) ) : ?> style="font-weight: bold;"<?php endif; ?>
			>
			<?php echo $post_type->labels->singular_name; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<input type="submit" name="load_post_type_settings" value="<?php _e( 'Load post type settings', 'tcp' ); ?>" class="button-secondary"/>
		<input type="submit" name="delete_post_type_settings" value="<?php _e( 'Delete post type settings', 'tcp' ); ?>" class="button-secondary"/>
		<p class="description"><?php _e( 'Allows to create different configuration for each Post Type.', 'tcp' ); ?></p>
		<span class="description"><?php _e( 'Options in bold have a specific configuration.', 'tcp' ); ?>
		<?php _e( 'Remember to save changes before to load new post type settings.', 'tcp' ); ?>
		</span>
	</td>
</tr>
</tbody>
</table>

<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
	<label for="see_title"><?php _e( 'See Title', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_title" id="see_title" value="yes" <?php checked( $see_title, true ); ?> />
	<p class="description"><?php _e( 'Allow to show or hide product titles (or any other post type) in the loops', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="title_tag"><?php _e( 'Title Tag', 'tcp' ); ?>:</label>
	</th>
	<td>
	<select id="title_tag" name="title_tag">
		<option value="" <?php selected( $title_tag, '' ); ?>><?php _e( 'No tag', 'tcp' ); ?></option>
		<option value="h2" <?php selected( $title_tag, 'h2' ); ?>>h2</option>
		<option value="h3" <?php selected( $title_tag, 'h3' ); ?>>h3</option>
		<option value="h4" <?php selected( $title_tag, 'h4' ); ?>>h4</option>
		<option value="h5" <?php selected( $title_tag, 'h5' ); ?>>h5</option>
		<option value="h6" <?php selected( $title_tag, 'h6' ); ?>>h6</option>
		<option value="p" <?php selected( $title_tag, 'p' ); ?>>p</option>
		<option value="div" <?php selected( $title_tag, 'div' ); ?>>div</option>
		<option value="span" <?php selected( $title_tag, 'span' ); ?>>span</option>
	</select>
	<p class="description"><?php _e( 'Allow to select which tag to use with product titles (or any other post type)', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_image"><?php _e( 'See Image', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_image" id="see_image" value="yes" <?php checked( $see_image, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="image_size"><?php _e( 'Image Size', 'tcp' ); ?>:</label>
	</th>
	<td>
	<select id="image_size" name="image_size">
		<?php $imageSizes = get_intermediate_image_sizes();
		foreach( $imageSizes as $imageSize ) : ?>
			<option value="<?php echo $imageSize;?>" <?php selected( $imageSize, $image_size ); ?>><?php echo $imageSize; ?></option>
		<?php endforeach;?>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_excerpt"><?php _e( 'See Excerpt', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_excerpt" id="see_excerpt" value="yes" <?php checked( $see_excerpt, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_content"><?php _e( 'See Content', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_content" id="see_content" value="yes" <?php checked( $see_content, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_author"><?php _e( 'See Author', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_author" id="see_author" value="yes" <?php checked( $see_author, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_price"><?php _e( 'See Price', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_price" id="see_price" value="yes" <?php checked( $see_price, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_buy_button"><?php _e( 'See Buy Button', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_buy_button" id="see_buy_button" value="yes" <?php checked( $see_buy_button, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_posted_on"><?php _e( 'See Posted On', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_posted_on" id="see_posted_on" value="yes" <?php checked( $see_posted_on, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_taxonomies"><?php _e( 'See Taxonomies', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_taxonomies" id="see_taxonomies" value="yes" <?php checked( $see_taxonomies, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_meta_utilities"><?php _e( 'See Meta Utilities', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_meta_utilities" id="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_sorting_panel"><?php _e( 'See Sorting Panel', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_sorting_panel" id="see_sorting_panel" value="yes" <?php checked( $see_sorting_panel, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="order_type_"><?php _e( 'Disabled order types', 'tcp' ); ?>:</label>
	</th>
	<td>
	<?php $sorting_fields = tcp_get_sorting_fields();
	foreach( $sorting_fields as $sorting_field ) : ?>
	<input type="checkbox" id="order_type_<?php echo $sorting_field['value']; ?>" name="disabled_order_types[]" value="<?php echo $sorting_field['value']; ?>" <?php tcp_checked_multiple( $disabled_order_types, $sorting_field['value'] ); ?>/> <?php echo $sorting_field['title']; ?><br/>
	<?php endforeach; ?>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_order_type"><?php _e( 'Order Type', 'tcp' ); ?>:</label>
	</th>
	<td>
	<?php $sorting_fields = tcp_get_sorting_fields(); ?>
	<select id="order_type" name="order_type">
	<?php foreach( $sorting_fields as $sorting_field ) :
		if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
		<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
		<?php endif;
	endforeach; ?>
	</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="order_desc"><?php _e( 'Order Desc', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="order_desc" id="order_desc" value="yes" <?php checked( $order_desc, 'desc' );?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="order_desc"><?php _e( 'Columns', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input id="columns" name="columns" value="<?php echo $columns;?>" size="2" maxlength="2" type="text" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_pagination"><?php _e( 'See Pagination', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_pagination" id="see_pagination" value="yes" <?php checked( $see_pagination, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_first_custom_area"><?php _e( 'See First Custom Area', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_first_custom_area" id="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_second_custom_area"><?php _e( 'See Second Custom Area', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_second_custom_area" id="see_second_custom_area" value="yes" <?php checked( $see_second_custom_area, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="see_third_custom_area"><?php _e( 'See Third Custom Area', 'tcp' ); ?>:</label>
	</th>
	<td>
	<input type="checkbox" name="see_third_custom_area" id="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area, true ); ?> />
	</td>
</tr>
<?php do_action( 'tcp_loop_settings_page', $settings ); ?>
</tbody>
</table>
<?php wp_nonce_field( 'tcp_loop_settings' ); ?>
<?php submit_button( null, 'primary', 'save-loop-settings' ); ?>
</form>
</div>
<?php endif;
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_loop_settings' );
		if ( isset( $_POST['load_post_type_settings'] ) ) return;
		if ( strlen( $_POST['current_post_type'] ) > 0 ) $suffix = '-' . $_POST['current_post_type'];
		else $suffix = '';
		if ( isset( $_POST['delete_post_type_settings'] ) ) {
			if ( strlen( $suffix ) == 0 ) return;
			$settings = get_option( 'ttc_settings' );
			unset( $settings['see_title' . $suffix] );
			unset( $settings['title_tag' . $suffix] );
			unset( $settings['see_image' . $suffix] );
			unset( $settings['image_size' . $suffix] );
			unset( $settings['see_excerpt' . $suffix] );
			unset( $settings['see_content' . $suffix] );
			unset( $settings['see_author' . $suffix] );
			unset( $settings['see_price' . $suffix] );
			unset( $settings['see_buy_button' . $suffix] );
			unset( $settings['see_posted_on' . $suffix] );
			unset( $settings['see_taxonomies' . $suffix] );
			unset( $settings['see_meta_utilities' . $suffix] );
			unset( $settings['disabled_order_types' . $suffix] );
			unset( $settings['order_type' . $suffix] );
			unset( $settings['order_desc' . $suffix] );
			unset( $settings['see_sorting_panel' . $suffix] );
			unset( $settings['columns' . $suffix] );
			unset( $settings['see_pagination' . $suffix] );
			unset( $settings['see_first_custom_area' . $suffix] );
			unset( $settings['see_second_custom_area' . $suffix] );
			unset( $settings['see_third_custom_area' . $suffix] );
			$settings = apply_filters( 'tcp_loop_unset_settings_action', $settings, $suffix );
			update_option( 'ttc_settings', $settings );
			$this->updated = true;
			global $thecartpress;
			$thecartpress->load_settings();
			return;
		}
		$settings = get_option( 'ttc_settings' );
		$settings['see_title' . $suffix]				= isset( $_REQUEST['see_title'] ) ? $_REQUEST['see_title']  == 'yes' : false;
		$settings['title_tag' . $suffix]				= $_REQUEST['title_tag'];
		$settings['see_image' . $suffix]				= isset( $_REQUEST['see_image'] ) ? $_REQUEST['see_image'] == 'yes' : false;
		$settings['image_size' . $suffix]				= $_REQUEST['image_size'];
		$settings['see_excerpt' . $suffix]				= isset( $_REQUEST['see_excerpt'] ) ? $_REQUEST['see_excerpt'] == 'yes' : false;
		$settings['see_content' . $suffix]				= isset( $_REQUEST['see_content'] ) ? $_REQUEST['see_content'] == 'yes' : false;
		$settings['see_author' . $suffix]				= isset( $_REQUEST['see_author'] ) ? $_REQUEST['see_author'] == 'yes' : false;
		$settings['see_price' . $suffix]				= isset( $_REQUEST['see_price'] ) ? $_REQUEST['see_price'] == 'yes' : false;
		$settings['see_buy_button' . $suffix]			= isset( $_REQUEST['see_buy_button'] ) ? $_REQUEST['see_buy_button']  == 'yes' : false;
		$settings['see_posted_on' . $suffix]			= isset( $_REQUEST['see_posted_on'] ) ? $_REQUEST['see_posted_on']  == 'yes' : false;
		$settings['see_taxonomies' . $suffix]			= isset( $_REQUEST['see_taxonomies'] ) ? $_REQUEST['see_taxonomies']  == 'yes' : false;
		$settings['see_meta_utilities' . $suffix]		= isset( $_REQUEST['see_meta_utilities'] ) ? $_REQUEST['see_meta_utilities']  == 'yes' : false;
		$settings['disabled_order_types' . $suffix] 	= isset( $_REQUEST['disabled_order_types'] ) ? $_REQUEST['disabled_order_types'] : array();
		$settings['order_type' . $suffix]				= $_REQUEST['order_type'];
		$settings['order_desc' . $suffix]				= isset( $_REQUEST['order_desc'] ) ? 'desc' : 'asc';
		$settings['see_sorting_panel' . $suffix]		= isset( $_REQUEST['see_sorting_panel'] ) ? $_REQUEST['see_sorting_panel'] == 'yes' : false;
		$settings['columns' . $suffix]					= (int)$_REQUEST['columns'];
		$settings['see_pagination' . $suffix]			= isset( $_REQUEST['see_pagination'] ) ? $_REQUEST['see_pagination']  == 'yes' : false;
		$settings['see_first_custom_area' . $suffix]	= isset( $_REQUEST['see_first_custom_area'] ) ? $_REQUEST['see_first_custom_area']  == 'yes' : false;
		$settings['see_second_custom_area' . $suffix]	= isset( $_REQUEST['see_second_custom_area'] ) ? $_REQUEST['see_second_custom_area']  == 'yes' : false;
		$settings['see_third_custom_area' . $suffix]	= isset( $_REQUEST['see_third_custom_area'] ) ? $_REQUEST['see_third_custom_area']  == 'yes' : false;
		$settings = apply_filters( 'tcp_loop_settings_action', $settings, $suffix );
		update_option( 'ttc_settings', $settings );
		$this->updated = true;
	}

	function template_include( $template ) {
		global $wp_query;
		if ( isset( $wp_query->tax_query ) ) {
			if ( is_array( $wp_query->tax_query->queries ) && count( $wp_query->tax_query->queries ) > 0 ) {
				foreach ( $wp_query->tax_query->queries as $tax_query ) { //@See Query.php: 1530
					if ( tcp_is_saleable_taxonomy( $tax_query['taxonomy'] ) ) {
						$template = $this->get_template_taxonomy();
						if ( $template ) return $template;
					}
				}
			}
		}

		/*global $post;
		if ( $post && tcp_is_saleable_post_type( $post->post_type ) ) {
			if ( is_single() ) $template = $this->get_template_single();
			else $template = $this->get_template_archive();
			if ( $template ) return $template;
		}*/
		
		return $template;
	}

	private function get_template_taxonomy() {
		$settings = get_option( 'tcp_settings' );
		if ( $settings['use_default_loop'] == 'yes' ) {
			$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyeleven/taxonomy.php';
		} elseif ( $settings['use_default_loop'] == 'yes_2010' ) {
			$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyten/taxonomy.php';
		} else {
			$template = false;
		}
		return $template;
	}

	private function get_template_archive( $product_type = false) {
		$settings = get_option( 'tcp_settings' );
		if ( $settings['use_default_loop'] == 'yes' ) {
			$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyeleven/archive-tcp_product.php';
		} elseif ( $settings['use_default_loop'] == 'yes_2010' ) {
			$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyten/archive-tcp_product.php';
		} else {
			$template = false;
		}
		return $template;
	}

	private  function get_template_single( $product_type = false ) {
		$settings = get_option( 'tcp_settings' );
		if ( $settings['use_default_loop'] == 'yes' ) {
			$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyeleven/single-tcp_product.php';
		} elseif ( $settings['use_default_loop'] == 'yes_2010' ) {
			$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyten/single-tcp_product.php';
		} else {
			$template = false;
		}
		return $template;
	}
}

new TCPLoopSettings();
?>