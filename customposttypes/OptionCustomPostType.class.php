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

/**
 * This class defines the post type 'tcp_product_options'.
 */
class OptionCustomPostType {

	public static $PRODUCT_OPTION = 'tcp_product_option';
	
	function __construct() {
		$labels = array(
			'name'					=> _x( 'Options', 'post type general name', 'tcp' ),
			'singular_name'			=> _x( 'Options', 'post type singular name', 'tcp' ),
			'add_new'				=> __( 'Add New Option', 'tcp' ),
			'add_new_item'			=> __( 'Add New Option', 'tcp' ),
			'edit_item'				=> __( 'Edit Option', 'tcp' ),
			'new_item'				=> __( 'New Option', 'tcp' ),
			'view_item'				=> __( 'View Option', 'tcp' ),
			'search_items'			=> __( 'Search Options', 'tcp' ),
			'not_found'				=> __( 'No options found', 'tcp' ),
			'not_found_in_trash'	=> __( 'No options found in Trash', 'tcp' ),
			'parent_item_colon'		=> '',
		);
		$register = array (
			'label'				=> __( 'Options', 'tcp' ),
			'singular_label'	=> __( 'Option', 'tcp' ),
			'labels'			=> $labels,
			'public'			=> false,
			'show_ui'			=> true,
			'show_in_menu'		=> false,
			'can_export'		=> true,
			'show_in_nav_menus'	=> false,
			'_builtin'			=> false, // It's a custom post type, not built in! (http://kovshenin.com/archives/extending-custom-post-types-in-wordpress-3-0/)
			'_edit_link'		=> 'post.php?post=%d',
			'capability_type'	=> 'page', //ProductPost::$PRODUCT,
			'hierarchical'		=> false, //allways false
			'query_var'			=> true,
			'supports'			=> array( 'title', 'editor', 'thumbnail', ),
			'rewrite'			=> false, array( 'slug' => __( 'options', 'tcp' ) ),
		);
		register_post_type( OptionCustomPostType::$PRODUCT_OPTION, $register );
	}
}
?>
