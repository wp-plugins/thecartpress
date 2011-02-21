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

add_image_size( 'mini-thumbnail', 45, 45 ); // mini thumbnails 
add_image_size( 'medium-thumbnail', 95, 75 ); // medium thumbnails 

if ( ! function_exists( 'twentytencart_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentytencart_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;



if ( ! function_exists( 'twentytencart_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentytencart_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_term_list(0, 'tcp_product_tag', '', ', ');
	$supplier_list = get_the_term_list(0, 'tcp_product_supplier', '', ', ');
	if ( $tag_list && $supplier_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s and supplied by %3$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentytencart' );
	} elseif ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} elseif ( $supplier_list ) {
		$posted_in = __( 'This entry was posted in %1$s and supplied by %3$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentytencart' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'tcp_product_category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%4$s" title="Permalink to %5$s" rel="bookmark">permalink</a>.', 'twentyten' );
	}
	
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_term_list(0, 'tcp_product_category', '', ' , '),
		$tag_list,
		$supplier_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

add_action( 'admin_menu', 'twentytencart_admin_menu', 99 );
add_action( 'admin_init', 'twentytencart_admin_init' );
add_action( 'twentyten_credits', 'twentytencart_credits' );

function twentytencart_admin_menu() {
	//add_options_page( __( 'TwentytenCart', 'tcp' ), __( 'TwentytenCart settings', 'tcp' ), 'tcp_edit_settings', 'themes', 'twentytencart_show_settings' );
	add_submenu_page( 'themes.php', __( 'TCP Loop settings', 'tcp' ), __( 'TCP Loop settings', 'tcp' ), 'tcp_edit_settings', __FILE__, 'twentytencart_show_settings' );
}

function twentytencart_admin_init() {
	register_setting( 'twentytencart_options', 'ttc_settings', 'twentytencart_validate' );
	add_settings_section( 'main_section', __( 'Main settings', 'tcp' ) , 'twentytencart_show_main_section', __FILE__ );
	add_settings_field( 'see_title', __( 'See title:', 'tcp' ), 'twentytencart_see_title', __FILE__ , 'main_section' );
	add_settings_field( 'see_image', __( 'See image:', 'tcp' ), 'twentytencart_see_image', __FILE__ , 'main_section' );
	add_settings_field( 'image_size', __( 'Image size:', 'tcp' ), 'twentytencart_image_size', __FILE__ , 'main_section' );
	add_settings_field( 'see_excerpt', __( 'See excerpt:', 'tcp' ), 'twentytencart_see_excerpt', __FILE__ , 'main_section' );
	add_settings_field( 'see_content', __( 'See content:', 'tcp' ), 'twentytencart_see_content', __FILE__ , 'main_section' );
	add_settings_field( 'see_author', __( 'See author:', 'tcp' ), 'twentytencart_see_author', __FILE__ , 'main_section' );
	add_settings_field( 'see_price', __( 'See price:', 'tcp' ), 'twentytencart_see_price', __FILE__ , 'main_section' );
	add_settings_field( 'see_buy_button', __( 'See buy button:', 'tcp' ), 'twentytencart_see_buy_button', __FILE__ , 'main_section' );
	add_settings_field( 'see_meta_data', __( 'See meta data:', 'tcp' ), 'twentytencart_see_meta_data', __FILE__ , 'main_section' );
	add_settings_field( 'see_meta_utilities', __( 'See meta utilities:', 'tcp' ), 'twentytencart_see_meta_utilities', __FILE__ , 'main_section' );
	add_settings_field( 'columns', __( 'Columns:', 'tcp' ), 'twentytencart_columns', __FILE__ , 'main_section' );	
	add_settings_field( 'see_first_custom_area', __( 'See first custom area', 'tcp' ), 'twentytencart_see_first_custom_area', __FILE__ , 'main_section' );
	add_settings_field( 'see_second_custom_area', __( 'See second custom area', 'tcp' ), 'twentytencart_see_second_custom_area', __FILE__ , 'main_section' );
	add_settings_field( 'see_third_custom_area', __( 'See third custom area', 'tcp' ), 'twentytencart_see_third_custom_area', __FILE__ , 'main_section' );
}

function twentytencart_credits() {
?><a href="<?php echo esc_url( __( 'http://thecartpress.com/', 'tcp' ) ); ?>" title="<?php esc_attr_e( 'eCommerce platform', 'tcp' ); ?>" ><?php printf( __( 'Powered by %s.', 'tcp' ), 'TheCartPress' ); ?></a><?php
}

function twentytencart_show_settings() {?>
	<div class="wrap">
		<h2><?php _e( 'TCP Loop Settings', 'tcp' );?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('twentytencart_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div><?php
}

function twentytencart_show_main_section() {
}

function twentytencart_see_title() {
	$settings = get_option( 'ttc_settings' );
	$see_title = isset( $settings['see_title'] ) ? $settings['see_title'] : true;?>
	<input type="checkbox" name="ttc_settings[see_title]" id="see_title" value="yes" <?php checked( $see_title, true );?> /><?php
}

function twentytencart_see_image() {
	$settings = get_option( 'ttc_settings' );
	$see_image = isset( $settings['see_image'] ) ? $settings['see_image'] : true;?>
	<input type="checkbox" name="ttc_settings[see_image]" id="see_image" value="yes" <?php checked( $see_image, true );?> /><?php
}

function twentytencart_image_size() {
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

function twentytencart_see_excerpt() {
	$settings = get_option( 'ttc_settings' );
	$see_excerpt = isset( $settings['see_excerpt'] ) ? $settings['see_excerpt'] : true;?>
	<input type="checkbox" name="ttc_settings[see_excerpt]" id="see_excerpt" value="yes" <?php checked( $see_excerpt, true );?> /><?php
}

function twentytencart_see_content() {
	$settings = get_option( 'ttc_settings' );
	$see_content = isset( $settings['see_content'] ) ? $settings['see_content'] : false;?>
	<input type="checkbox" name="ttc_settings[see_content]" id="see_content" value="yes" <?php checked( $see_content, true );?> /><?php
}

function twentytencart_see_author() {
	$settings = get_option( 'ttc_settings' );
	$see_author = isset( $settings['see_author'] ) ? $settings['see_author'] : false;?>
	<input type="checkbox" name="ttc_settings[see_author]" id="see_author" value="yes" <?php checked( $see_author, true );?> /><?php
}

function twentytencart_see_price() {
	$settings = get_option( 'ttc_settings' );
	$see_price = isset( $settings['see_price'] ) ? $settings['see_price'] : true;?>
	<input type="checkbox" name="ttc_settings[see_price]" id="see_price" value="yes" <?php checked( $see_price, true );?> /><?php
}

function twentytencart_see_buy_button() {
	$settings = get_option( 'ttc_settings' );
	$see_buy_button = isset( $settings['see_buy_button'] ) ? $settings['see_buy_button'] : false;?>
	<input type="checkbox" name="ttc_settings[see_buy_button]" id="see_buy_button" value="yes" <?php checked( $see_buy_button, true );?> /><?php
}

function twentytencart_see_meta_data() {
	$settings = get_option( 'ttc_settings' );
	$see_meta_data = isset( $settings['see_meta_data'] ) ? $settings['see_meta_data'] : false;?>
	<input type="checkbox" name="ttc_settings[see_meta_data]" id="see_meta_data" value="yes" <?php checked( $see_meta_data, true );?> /><?php
}

function twentytencart_see_meta_utilities() {
	$settings = get_option( 'ttc_settings' );
	$see_meta_utilities = isset( $settings['see_meta_utilities'] ) ? $settings['see_meta_utilities'] : false;?>
	<input type="checkbox" name="ttc_settings[see_meta_utilities]" id="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities, true );?> /><?php
}

function twentytencart_columns() {
	$settings = get_option( 'ttc_settings' );
	$columns = isset( $settings['columns'] ) ? (int)$settings['columns'] : 2;?>
	<input id="columns" name="ttc_settings[columns]" value="<?php echo $columns;?>" size="2" maxlength="2" type="text" /><?php
}

function twentytencart_see_first_custom_area() {
	$settings = get_option( 'ttc_settings' );
	$see_first_custom_area = isset( $settings['see_first_custom_area'] ) ? $settings['see_first_custom_area'] : false;?>
	<input type="checkbox" name="ttc_settings[see_first_custom_area]" id="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area, true );?> /><?php
}

function twentytencart_see_second_custom_area() {
	$settings = get_option( 'ttc_settings' );
	$see_second_custom_area = isset( $settings['see_second_custom_area'] ) ? $settings['see_second_custom_area'] : false;?>
	<input type="checkbox" name="ttc_settings[see_second_custom_area]" id="see_second_custom_area" value="yes" <?php checked( $see_second_custom_area, true );?> /><?php
}

function twentytencart_see_third_custom_area() {
	$settings = get_option( 'ttc_settings' );
	$see_third_custom_area = isset( $settings['see_third_custom_area'] ) ? $settings['see_third_custom_area'] : false;?>
	<input type="checkbox" name="ttc_settings[see_third_custom_area]" id="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area, true );?> /><?php
}

function twentytencart_validate( $input ) {
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
?>
