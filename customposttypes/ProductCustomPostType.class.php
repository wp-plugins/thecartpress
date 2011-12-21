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

define( 'TCP_PRODUCT_POST_TYPE'	, 'tcp_product' );
define( 'TCP_PRODUCT_CATEGORY'	, 'tcp_product_category' );
define( 'TCP_PRODUCT_TAG'		, 'tcp_product_tag' );
define( 'TCP_SUPPLIER_TAG'		, 'tcp_product_supplier' );

/**
 * This class defines the post type 'tcp_product'
 * @author sensei
 */
class ProductCustomPostType {
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
			'can_export'		=> true,
			'_builtin'			=> false, // It's a custom post type, not built in! (http://kovshenin.com/archives/extending-custom-post-types-in-wordpress-3-0/)
			'_edit_link'		=> 'post.php?post=%d',
			'capability_type'	=> 'post',
			'hierarchical'		=> false, //allways false
			'query_var'			=> true,
			'supports'			=> array( 'title', 'excerpt', 'editor', 'thumbnail', 'comments' ),
			'taxonomies'		=> array( TCP_PRODUCT_CATEGORY ), // Permalinks format
			'rewrite'			=> array( 'slug' => isset( $thecartpress->settings['product_rewrite'] ) ? $thecartpress->settings['product_rewrite'] : 'products' ),
			'has_archive'		=> isset( $thecartpress->settings['product_rewrite'] ) && $thecartpress->settings['product_rewrite'] != '' ? $thecartpress->settings['product_rewrite'] : 'products',
		);
		register_post_type( TCP_PRODUCT_POST_TYPE, $register );
		if ( $register['has_archive'] ) ProductCustomPostType::register_post_type_archives( TCP_PRODUCT_POST_TYPE, $register['has_archive'] );
		if ( is_admin() ) {
			add_filter( 'post_row_actions', array( $this, 'postRowActions' ) );
			$post_types = tcp_get_saleable_post_types();
			foreach( $post_types as $post_type ) {
				add_filter( 'manage_edit-' . $post_type . '_columns', array( $this, 'custom_columns_definition' ) );
			}
		}
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
			'rewrite'		=> array( 'slug' => isset( $thecartpress->settings['category_rewrite'] ) ? $thecartpress->settings['category_rewrite'] : 'product_category' ), //false
		);
		register_taxonomy( TCP_PRODUCT_CATEGORY, TCP_PRODUCT_POST_TYPE, $register );
		register_taxonomy( TCP_PRODUCT_TAG, TCP_PRODUCT_POST_TYPE, array(
			'public'		=> true,
			'hierarchical'	=> false,
			'query_var'		=> true,
			'rewrite'		=> array( 'slug' => isset( $thecartpress->settings['tag_rewrite'] ) ? $thecartpress->settings['tag_rewrite'] : 'product_tag' ), //false
			'label'			=> __( 'Products Tags', 'tcp' ),
		) );
		register_taxonomy( TCP_SUPPLIER_TAG, TCP_PRODUCT_POST_TYPE, array(
			'hierarchical'	=> true,
			'query_var'		=> true,
			'rewrite'		=> array( 'slug' => isset( $thecartpress->settings['supplier_rewrite'] ) ? $thecartpress->settings['supplier_rewrite'] : 'product_supplier' ), //false
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
			add_action( 'manage_posts_custom_column', array( $this, 'manage_posts_custom_column' ) );
			add_action( 'restrict_manage_posts', array( $this, 'restrictManagePosts' ) );
			add_filter( 'parse_query', array( $this, 'parseQuery' ) ); //TODO 3.1
			//for quick edit
			//add_action('quick_edit_custom_box', array( $this, 'quickEditCustomBox' ), 10, 2 );
		}
	}


	//http://vocecommunications.com/blog/2010/11/adding-rewrite-rules-for-custom-post-types/
	static function register_post_type_archives( $post_type, $base_path = '' ) {
//echo "register_post_type_archives( $post_type, $base_path )<br>";

		global $wp_rewrite;
		$permalink_prefix = $base_path;
		$permalink_structure = '%year%/%monthnum%/%day%/%' . $post_type . '%/';
		//we use the WP_Rewrite class to generate all the endpoints WordPress can handle by default.
		$rewrite_rules = $wp_rewrite->generate_rewrite_rules( $permalink_prefix . '/' . $permalink_structure, EP_ALL, true, true, true, true, true );
		//build a rewrite rule from just the prefix to be the base url for the post type
		$rewrite_rules = array_merge( $wp_rewrite->generate_rewrite_rules( $permalink_prefix ), $rewrite_rules );
		$rewrite_rules[$permalink_prefix . '/?$'] = 'index.php?paged=1';
		foreach( $rewrite_rules as $regex => $redirect ) {
			if ( strpos( $redirect, 'attachment=' ) === false ) {
				$redirect .= '&post_type=' . $post_type; //add the post_type to the rewrite rule
			}
			//turn all of the $1, $2,... variables in the matching regex into $matches[] form
			if ( 0 < preg_match_all('@\$([0-9])@', $redirect, $matches ) ) {
				for( $i = 0; $i < count( $matches[0] ); $i++ ) {
					$redirect = str_replace( $matches[0][$i], '$matches[' . $matches[1][$i] . ']', $redirect );
				}
			}
			$wp_rewrite->add_rule( $regex, $redirect, 'top' ); //add the rewrite rule to wp_rewrite
		}
	}

	/*function quickEditCustomBox( $column_name, $post_type ) {
		if ( $post_type == TCP_PRODUCT_POST_TYPE ) {
			global $post; //TODO
			if ('price' == $column_name)
				echo 'price:', tcp_get_the_price( $post->ID );
		}
	}*/

	function postRowActions( $actions, $post_line = null ) {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $actions;
		$actions = apply_filters( 'tcp_product_row_actions', $actions, $post );
		return $actions;
	}

	/**
	 * Custom definition for the products list
	 */
	function custom_columns_definition( $columns ) {
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'thumbnail'		=> __( 'Thumbnail', 'tcp' ),
			'title'			=> __( 'Name', 'tcp' ),
			'sku'			=> __( 'SKU', 'tcp' ),
			'price'			=> __( 'Price - Type', 'tcp' ),
			//'date'			=> __( 'Date', 'tcp' ),
			//'comments'	=> __('Comments', 'tcp' ),
		);
		global $thecartpress;
		$show_back_end_label = isset( $thecartpress->settings['show_back_end_label'] ) ? $thecartpress->settings['show_back_end_label'] : false;
		if ( ! $show_back_end_label ) unset( $columns['label'] );
		return apply_filters( 'tcp_custom_columns_definition', $columns );
	}

	/**
	 * Prints the custom fields values in the products list
	 */
	function manage_posts_custom_column( $column_name ) {
		global $post;
		if ( tcp_is_saleable_post_type( $post->post_type ) ) {
			if ( 'ID' == $column_name ) {
				echo $post->ID;
			} 
			elseif ( 'thumbnail' == $column_name ) {
				$image = tcp_get_the_thumbnail( $post->ID, 0, 0, array( '50', '50' )  );
				echo $image;
			} elseif ( 'sku' == $column_name ) {
				$sku = tcp_get_the_sku( $post->ID );
				if ( strlen( trim( $sku ) ) == 0 ) $sku = __( 'N/A', 'tcp' );
				echo $sku;
			} elseif ( 'price' == $column_name ) {
				$price = tcp_get_the_price( $post->ID );
				if ( $price > 0 ) echo '<strong>', tcp_format_the_price( $price ), '</strong>';
				echo '<br/>';
				$product_type = tcp_get_the_product_type( $post->ID );
				$types = tcp_get_product_types();
				if ( isset( $types[$product_type] ) ) echo $types[$product_type];
			}
			do_action( 'tcp_manage_posts_custom_column', $column_name, $post );
		}
	}

	/**
	 * Print filtering fields in the products list
	 */
	function restrictManagePosts() {
		global $typenow;
		if ( $typenow == TCP_PRODUCT_POST_TYPE ) {
		//if ( tcp_is_saleable_post_type( $post->post_type ) )
			global $wp_query;
			wp_dropdown_categories( array(
				'show_option_all'	=> __( 'View all categories', 'tcp' ),
				'taxonomy'			=> TCP_PRODUCT_CATEGORY,
				'name'				=> 'tcp_product_cat',
				'orderby'			=> 'name',
				'selected'			=> isset( $wp_query->query['term'] ) ? $wp_query->query['term'] : '',
				'hierarchical'		=> true,
				'depth'				=> 3,
				'show_count'		=> true,
				'hide_empty'		=> true,
			) );?>
			<label for="tcp_product_type"><?php _e( 'type:', 'tcp' );?></label>
			<?php $product_types = tcp_get_product_types( true, __( 'all', 'tcp' ) );
			tcp_html_select( 'tcp_product_type', $product_types, isset( $_REQUEST['tcp_product_type'] ) ? $_REQUEST['tcp_product_type'] : '' );
		}
	}

	/**
	 * This function is executed before the admin product list query. WP 3.1
	 */
	function parseQuery( $query ) {
		if ( isset( $_REQUEST['tcp_product_cat'] ) && $_REQUEST['tcp_product_cat'] > 0) {
			$query->query_vars['tax_query'] = array(
				array(
					'taxonomy'	=> TCP_PRODUCT_CATEGORY,
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
		
		if ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == TCP_PRODUCT_POST_TYPE ) {
			global $pagenow;
			if ( $pagenow == 'edit.php' ) {
				global $thecartpress;
				$hide_invisible = isset( $thecartpress->settings['hide_visibles'] ) ? (bool)$thecartpress->settings['hide_visibles'] : true;
				if ( $hide_invisible ) {
					$query->query_vars['meta_query'][] = array(
						'key'		=> 'tcp_is_visible',
						'value'		=> 1,
						'compare'	=> '=',
						'type'		=> 'numeric',
					);
				}
			}
		}
		return $query;
	}
}

new ProductCustomPostType();
?>
