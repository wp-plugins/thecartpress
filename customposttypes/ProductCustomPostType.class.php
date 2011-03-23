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
 * This class defines the post type 'tcp_product'.
 */
require_once( dirname( dirname( __FILE__ ) ).'/daos/RelEntities.class.php' );

class ProductCustomPostType {
	public static $PRODUCT			= 'tcp_product';
	public static $PRODUCT_CATEGORY	= 'tcp_product_category';
	public static $PRODUCT_TAG		= 'tcp_product_tag';
	public static $SUPPLIER_TAG		= 'tcp_product_supplier'; //_tag';
	
	function __construct() {
		global $thecartpress;
		$labels = array(
			'name'					=> _x( 'Products', 'post type general name', 'tcp' ),
			'singular_name'			=> _x( 'Product', 'post type singular name', 'tcp' ),
			'add_new'				=> _x( 'Add New', 'product', 'tcp' ),
			'add_new_item'			=> __( 'Add New', 'tcp' ),
			'edit_item'				=> __( 'Edit Product', 'tcp' ),
			'new_item'				=> __( 'New Product', 'tcp' ),
			'view_item'				=> __( 'View Product', 'tcp' ),
			'search_items'			=> __( 'Search Products', 'tcp' ),
			'not_found'				=> __( 'No products found', 'tcp' ),
			'not_found_in_trash'	=> __( 'No products found in Trash', 'tcp' ),
			'parent_item_colon'		=> '',
		);
		$register = array (
			'label'				=> __( 'Products', 'tcp' ),
			'singular_label'	=> __( 'Product', 'tcp' ),
			'labels'			=> $labels,
			'public'			=> true,
			'show_ui'			=> true,
			'_builtin'			=> false, // It's a custom post type, not built in! (http://kovshenin.com/archives/extending-custom-post-types-in-wordpress-3-0/)
			'_edit_link'		=> 'post.php?post=%d',
			'capability_type'	=> 'post',
			'hierarchical'		=> false, //allways false
			'query_var'			=> true,
			'supports'			=> array( 'title', 'excerpt', 'editor', 'thumbnail', 'comments', ),
			'taxonomies'		=> array( ProductCustomPostType::$PRODUCT_CATEGORY ), // Permalinks format
			'rewrite'			=> array( 'slug' => isset( $thecartpress->settings['product_rewrite'] ) ? $thecartpress->settings['product_rewrite'] : 'products' ),
		);
		register_post_type( ProductCustomPostType::$PRODUCT, $register );
		add_filter( 'post_row_actions', array( $this, 'postRowActions' ) );
		add_filter( 'manage_edit-' . ProductCustomPostType::$PRODUCT . '_columns', array( $this, 'customColumnsDefinition' ) );
		$labels = array(
			'name'				=> _x( 'Categories', 'taxonomy general name', 'tcp' ),
			'singular_name'		=> _x( 'Category', 'taxonomy singular name', 'tcp' ),
			'search_items'		=> __( 'Search Categories', 'tcp' ),
			'all_items'			=> __( 'All Categories', 'tcp' ),
			'parent_item'		=> __( 'Parent Category', 'tcp' ),
			'parent_item_colon'	=> __( 'Parent Category:', 'tcp' ),
			'edit_item'			=> __( 'Edit Category', 'tcp' ), 
			'update_item'		=> __( 'Update Category', 'tcp' ),
			'add_new_item'		=> __( 'Add New Category', 'tcp' ),
			'new_item_name'		=> __( 'New Category Name', 'tcp' ),
		); 	
		$register = array (
			'labels'		=> $labels,
			'hierarchical'	=> true,
			'query_var'		=> true, //'cat_prods',
			'label'			=> __( 'Category', 'tcp' ),
			'rewrite'		=> array('slug' => isset( $thecartpress->settings['category_rewrite'] ) ? $thecartpress->settings['category_rewrite'] : 'product_category' ), //false
		);
		register_taxonomy( ProductCustomPostType::$PRODUCT_CATEGORY, ProductCustomPostType::$PRODUCT, $register );
		register_taxonomy( ProductCustomPostType::$PRODUCT_TAG, ProductCustomPostType::$PRODUCT, array(
			'public'		=> true,
			'hierarchical'	=> false,
			'query_var'		=> true,
			'rewrite'		=> array('slug' => isset( $thecartpress->settings['tag_rewrite'] ) ? $thecartpress->settings['tag_rewrite'] : 'product_tag' ), //false
			'label'			=> __( 'Products Tags', 'tcp' ),
		) );
		register_taxonomy( ProductCustomPostType::$SUPPLIER_TAG, ProductCustomPostType::$PRODUCT, array(
			'hierarchical'	=> true,
			'query_var'		=> true,
			'rewrite'		=> array('slug' => isset( $thecartpress->settings['supplier_rewrite'] ) ? $thecartpress->settings['supplier_rewrite'] : 'product_supplier' ), //false
			//'label'		=> __('Suppliers', 'tcp' ),
			'labels'		=> array(
				'name'				=> _x( 'Suppliers', 'taxonomy general name', 'tcp' ),
				'singular_name'		=> _x( 'Supplier', 'taxonomy singular name', 'tcp' ),
				'search_items'		=> __( 'Search Suppliers', 'tcp' ),
				'all_items'			=> __( 'All Suppliers', 'tcp' ),
				'edit_item'			=> __( 'Edit Suppliers', 'tcp' ), 
				'update_item'		=> __( 'Update Suppliers', 'tcp' ),
				'add_new_item'		=> __( 'Add New Suppliers', 'tcp' ),
				'new_item_name'		=> __( 'New Suppliers Name', 'tcp' ),
			),
		) );

		if ( is_admin() ) {
			add_action( 'manage_posts_custom_column', array( $this, 'managePostCustomColumns' ) );
			add_action( 'restrict_manage_posts', array( $this, 'restrictManagePosts' ) );
			add_filter( 'parse_query', array( $this, 'parseQuery' ) ); //TODO 3.1
			//for quick edit
			//add_action('quick_edit_custom_box', array( $this, 'quickEditCustomBox' ), 10, 2 );
		}
	}

	/*function quickEditCustomBox( $column_name, $post_type ) {
		if ( $post_type == ProductCustomPostType::$PRODUCT ) {
			global $post; //TODO
			if ('price' == $column_name)
				echo 'price:', tcp_get_the_price( $post->ID );
		}
	}*/

	function postRowActions( $actions, $post_line = null ) {
		global $post;
		if ( $post->post_type != 'tcp_product' ) return $actions;
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
		if ( $post->post_type == 'tcp_product' && tcp_get_the_product_type( $post->ID ) == 'GROUPED' ) 
			$actions['tcp_assigned'] = '<a href="' . $admin_path . 'AssignedProductsList.php&post_id=' . $post->ID . '&rel_type=GROUPED" title="' . esc_attr( __( 'See assigned products', 'tcp' ) ) . '">' . __( 'assigned products', 'tcp' ) . '</a>';
		$actions = apply_filters( 'tcp_product_row_actions', $actions );
		return $actions;
	}

	/**
	 * Custom definition for the products list
	 */
	function customColumnsDefinition( $columns ) {
		$columns = array(
			'cb'	=> '<input type="checkbox" />',
			'title'	=> __( 'Name', 'tcp' ),
			'price'	=> __( 'Type - price', 'tcp' ),
			'date'	=> __( 'date', 'tcp' ),
			//'comments'	=> __('Comments', 'tcp' ),
		);
		return $columns;
	}

	/**
	 * Prints the custom fields values in the products list
	 */
	function managePostCustomColumns( $column_name ) {
		global $post;
		if ( $post->post_type == ProductCustomPostType::$PRODUCT ) 
			if ( 'ID' == $column_name ) {
				echo $post->ID;
			} elseif ('price' == $column_name) {
				global $thecartpress;
				echo tcp_get_the_product_type( $post->ID ) . ' - ' . tcp_get_the_price( $post->ID ) . '&nbsp;' . $thecartpress->settings['currency'];
			}
	}

	/**
	 * Print filtering fields in the products list
	 */
	function restrictManagePosts() {
		global $typenow;
		if ( $typenow == ProductCustomPostType::$PRODUCT ) {
			global $wp_query;
			wp_dropdown_categories( array(
				'show_option_all'	=> __( 'View all categories', 'tcp' ),
				'taxonomy'			=> ProductCustomPostType::$PRODUCT_CATEGORY,
				'name'				=> 'tcp_product_cat',
				'orderby'			=> 'name',
				'selected'			=> isset( $wp_query->query['term'] ) ? $wp_query->query['term'] : '',
				'hierarchical'		=> true,
				'depth'				=> 3,
				'show_count'		=> true,
				'hide_empty'		=> true,
			) );?>
			<label for="tcp_product_type"><?php _e( 'type:', 'tcp' );?><label>
			<select name="tcp_product_type" id="tcp_product_type">
				<option value="" <?php selected( "", isset( $_REQUEST['tcp_product_type'] ) ? $_REQUEST['tcp_product_type'] : '' );?>><?php _e( 'all', 'tcp' );?></option>
				<option value="SIMPLE" <?php selected( "SIMPLE", isset( $_REQUEST['tcp_product_type'] ) ? $_REQUEST['tcp_product_type'] : '' );?>><?php _e( 'Simple', 'tcp' );?></option>
				<option value="GROUPED" <?php selected( "GROUPED", isset( $_REQUEST['tcp_product_type'] ) ? $_REQUEST['tcp_product_type'] : '' );?>><?php _e( 'Grouped', 'tcp' );?></option>
			</select>
		<?php
		}
	}

	/**
	 * This function is executed before the admin product list query. WP 3.1
	 */
	function parseQuery( $query ) {
		if ( isset( $_REQUEST['tcp_product_cat'] ) && $_REQUEST['tcp_product_cat'] > 0) {
			$query->query_vars['tax_query'] = array(
				array(
					'taxonomy'	=> ProductCustomPostType::$PRODUCT_CATEGORY,
					'terms'		=> array( $_REQUEST['tcp_product_cat'] ),
					'field'		=> 'id',
				),
			);
		}
		if ( isset( $_REQUEST['tcp_product_type'] ) && $_REQUEST['tcp_product_type'] != '' ) {
			$query->query_vars['meta_query'] = array(
				array(
					'key' => 'tcp_type',
					'value' => $_REQUEST['tcp_product_type'],
					'compare' => '=',
					'type' => 'string',
				),
			);
		}
		if ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == ProductCustomPostType::$PRODUCT ) {
			global $thecartpress;
			$hide_visible = isset( $thecartpress->settings['hide_visibles'] ) ? (bool)$thecartpress->settings['hide_visibles'] : true;
			if ( $hide_visible )
				$query->query_vars['meta_query'][] = array(
					'key'		=> 'tcp_is_visible',
					'value'		=> 1,
					'compare'	=> '=',
					'type'		=> 'numeric',
				);
		}
		return $query;
	}
}
?>
