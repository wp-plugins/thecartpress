<?php
/*
Plugin Name: TheCartPress
Plugin URI: http://thecartpress.com
Description: TheCartPress (Multi language support)
Version: 1.0.5
Author: TheCartPress team
Author URI: http://thecartpress.com
License: GPL
*/

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

if ( ! is_admin() ) {
	require_once( dirname( __FILE__ ) . '/classes/ShoppingCart.class.php' );
	session_start();
}

require_once( dirname( __FILE__ ) . '/customposttypes/ProductCustomPostType.class.php' );
require_once( dirname( __FILE__ ) . '/classes/BuyButton.class.php' );
require_once( dirname( __FILE__ ) . '/classes/OrderPanel.class.php' );
require_once( dirname( __FILE__ ) . '/classes/TCP_Plugin.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/ResumenShoppingCartWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/ShoppingCartWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/LastVisitedWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/RelatedListWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/CustomPostTypeListWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/TaxonomyCloudsPostTypeWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/TaxonomyTreesPostTypeWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/OrderPanelWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/CommentsCustomPostTypeWidget.class.php' );
//require_once( dirname( __FILE__ ) . '/widgets/ArchivesCustomPostType.class.php' );

class TheCartPress {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'user_register', array( $this, 'userRegister' ) );
		if ( is_admin() ) {
			register_activation_hook( __FILE__, array( $this, 'activatePlugin' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivatePlugin' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'dashboardSetup' ) );
			add_action( 'admin_menu', array( $this, 'createMenu' ) );
			//Metaboxes
			require_once( dirname( __FILE__ ) . '/metaboxes/ProductCustomFieldsMetabox.class.php' );
			$productCustomFieldsMetabox = new ProductCustomFieldsMetabox();
			add_action( 'admin_menu', array( $productCustomFieldsMetabox, 'registerMetaBox' ) );
			add_action( 'save_post', array( $productCustomFieldsMetabox, 'saveCustomFields' ), 1, 2 );
			require_once( dirname( __FILE__ ) . '/metaboxes/PostMetabox.class.php' );
			$postMetabox = new PostMetabox();
			add_action( 'admin_menu', array( $postMetabox, 'registerMetaBox' ) );
			add_action( 'delete_post', array( $postMetabox, 'deleteCustomFields' ), 1, 2 );
			add_filter( 'admin_footer_text', array( $this, 'adminFooterText' ) );
		} else {
			add_filter( 'the_content', array( $this, 'contentFilter' ) );
			add_filter( 'the_excerpt', array( $this, 'excerptFilter' ) );
			add_action( 'wp_head', array( $this, 'wpHead' ) );
			add_action( 'wp_meta', array( $this, 'wpMeta' ) );
//			add_filter( 'parse_query', array( $this, 'parseQuery' ) );
			add_filter( 'parse_request', array( $this, 'parseRequest' ) );
			add_filter( 'posts_join', array( $this, 'postsJoin' ) );
			add_filter( 'posts_where', array( $this, 'postsWhere' ) );
			add_filter( 'posts_orderby', array( $this, 'postsOrderby' ) );
//			add_filter( 'posts_request', array( $this, 'postsRequest' ) );

			add_filter( 'get_previous_post_join', array( $this, 'postsJoinNext' ) );
			add_filter( 'get_previous_post_where', array( $this, 'postsWhereNext' ) );
			add_filter( 'get_next_post_join', array( $this, 'postsJoinNext' ) );
			add_filter( 'get_next_post_where', array( $this, 'postsWhereNext' ) );

			//ShoppingCartTable and CheckOut shortcodes, and more...
			require_once( dirname( __FILE__ ) . '/shortcodes/ShoppingCartPage.class.php' );
			$shoppingCartPage = new ShoppingCartPage();
			add_shortcode( 'tcp_shopping_cart', array( $shoppingCartPage, 'show' ) );
			require_once( dirname( __FILE__ ) . '/shortcodes/Checkout.class.php' );
			$checkOut = new CheckOut();
			add_shortcode( 'tcp_checkout', array( $checkOut, 'show' ) );	
			add_shortcode( 'tcp_buy_button', array( $this, 'shortCodeBuyButton' ) );
			require_once( dirname( __FILE__ ) . '/shortcodes/TCP_Shortcode.class.php' );
			$tcp_shortcode = new TCP_Shortcode();
			add_shortcode( 'tcp_list', array( $tcp_shortcode, 'show' ) );	
			add_filter( 'login_form_bottom', array( $this, 'loginFormBottom' ) );
		}
		add_action( 'admin_bar_menu', array( $this, 'addMenuAdminBar' ), 70 );
		add_action( 'widgets_init', array( $this, 'registerWidgets' ) );
		$this->loadingDefaultCheckoutPlugins();

		require_once( dirname( __FILE__ ) .'/admin/TCP_Settings.class.php' );
		new TCP_Settings();
	}

	function wpHead() {
		if ( is_single() && ! is_page() ) {//Last visited
			global $post;
			if ( $post->post_type == 'tcp_product' ) {
				do_action( 'tcp_visited_product', $post );
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->addVisitedPost( $post->ID );
			}
		}
		//Shopping Cart actions
		if ( isset( $_REQUEST['tcp_add_to_shopping_cart'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$post_id = isset( $_REQUEST['tcp_post_id'] ) ? $_REQUEST['tcp_post_id'] : 0;
			if ( is_array( $post_id ) ) {
				for( $i = 0; $i < count( $_REQUEST['tcp_post_id'] ); $i++ ) {
					$count = isset( $_REQUEST['tcp_count'][$i] ) ? (int)$_REQUEST['tcp_count'][$i] : 0;
					if ( $count > 0 ) {
						$post_id		= isset( $_REQUEST['tcp_post_id'][$i] ) ? $_REQUEST['tcp_post_id'][$i] : 0;
						$post_id		= tcp_get_default_id( $post_id );
						$tcp_option_id	= isset( $_REQUEST['tcp_option_id'][$i] ) ? $_REQUEST['tcp_option_id'][$i] : 0;
						if ( strlen( $tcp_option_id ) > 0 ) {
							$option_ids = explode( '-',  $tcp_option_id);
							if ( count( $option_ids ) == 2 ) {
								$option_1_id	= $option_ids[0];
								$price_1		= (float)tcp_get_the_price( $option_1_id );
								$option_2_id	= $option_ids[1];
								$price_2		= (float)tcp_get_the_price( $option_2_id );
							} else {
								$option_1_id	= $tcp_option_id;
								$price_1		= (float)tcp_get_the_price( $option_1_id );
								$option_2_id	= '0';
								$price_2		= 0;
							}
						} else {
							$option_1_id	= isset( $_REQUEST['tcp_option_1_id'][$i] ) ? $_REQUEST['tcp_option_1_id'][$i] : 0;
							$price_1		= $option_1_id > 0 ? tcp_get_the_price( $option_1_id ) : 0;
							$option_2_id	= isset( $_REQUEST['tcp_option_2_id'][$i] ) ? $_REQUEST['tcp_option_2_id'][$i] : 0;
							$price_2		= $option_2_id > 0 ? tcp_get_the_price( $option_2_id ) : 0;
						}
						$unit_price		= isset( $_REQUEST['tcp_unit_price'][$i] ) ? $_REQUEST['tcp_unit_price'][$i] : 0;
						$unit_price		+= $price_1 + $price_2;
						$tax			= isset( $_REQUEST['tcp_tax'][$i] ) ? $_REQUEST['tcp_tax'][$i] : 0;
						$unit_weight	= isset( $_REQUEST['tcp_unit_weight'][$i] ) ? $_REQUEST['tcp_unit_weight'][$i] : 0;
						$shoppingCart->add( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $tax, $unit_weight );
					}
				}
				do_action( 'tcp_add_shopping_cart', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_delete_shopping_cart'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$shoppingCart->deleteAll();
			do_action( 'tcp_delete_shopping_cart' );
		} elseif ( isset( $_REQUEST['tcp_delete_item_shopping_cart'] ) ) {
			$post_id = isset( $_REQUEST['tcp_post_id'] ) ? $_REQUEST['tcp_post_id'] : 0;
			if ( $post_id > 0 ) {
				$option_1_id	= isset( $_REQUEST['tcp_option_1_id'] ) ? $_REQUEST['tcp_option_1_id'] : 0;
				$option_2_id	= isset( $_REQUEST['tcp_option_2_id'] ) ? $_REQUEST['tcp_option_2_id'] : 0;
				$shoppingCart	= TheCartPress::getShoppingCart();
				$shoppingCart->delete( $post_id, $option_1_id, $option_2_id );
				do_action( 'tcp_delete_item_shopping_cart', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_modify_item_shopping_cart'] ) ) {
			$post_id = $_REQUEST['tcp_post_id'] ? $_REQUEST['tcp_post_id'] : 0;
			if ( $post_id > 0 ) {
				$option_1_id	= isset( $_REQUEST['tcp_option_1_id'] ) ? $_REQUEST['tcp_option_1_id'] : 0;
				$option_2_id	= isset( $_REQUEST['tcp_option_2_id'] ) ? $_REQUEST['tcp_option_2_id'] : 0;
				$count			= isset( $_REQUEST['tcp_count'] ) ? $_REQUEST['tcp_count'] : 0;
				$shoppingCart	= TheCartPress::getShoppingCart();
				$shoppingCart->modify( $post_id, $option_1_id, $option_2_id, $count );
				do_action( 'tcp_modify_shopping_cart', $post_id );
			}
		}
	}

	static function getShoppingCart() {
		if ( isset( $_SESSION['tcp_session'] ) ) 
			$shoppingCart = $_SESSION['tcp_session'];
		else {
			$shoppingCart = new ShoppingCart();
			$_SESSION['tcp_session'] = $shoppingCart;
		}
		return $shoppingCart;
	}

	function parseRequest( $requests ) {
		//Order actions
		if ( ! is_admin()) {
			if ( isset( $_REQUEST['tcp_order_type'] ) )
				$_SESSION['tcp_order_type'] = $_REQUEST['tcp_order_type'];
			elseif ( ! isset( $_SESSION['tcp_order_type'] ) )
				$_SESSION['tcp_order_type'] = 'date';
			if ( isset( $_REQUEST['tcp_order_desc'] ) )
				$_SESSION['tcp_order_desc'] =  $_REQUEST['tcp_order_desc'];
			elseif ( ! isset( $_SESSION['tcp_order_desc'] ) )
				$_SESSION['tcp_order_desc'] = 'desc';
			return $requests;
		}
	}

	function postsJoin( $join ) {
		global $wpdb;

		$join .= " LEFT JOIN {$wpdb->postmeta} tcp_postmeta_is_visible ON ({$wpdb->posts}.ID = tcp_postmeta_is_visible.post_id AND tcp_postmeta_is_visible.meta_key='tcp_is_visible' )";
		if ( isset( $_SESSION['tcp_order_type'] ) )
			if ( $_SESSION['tcp_order_type'] == 'price' )
				$join .= " LEFT JOIN {$wpdb->postmeta} tcp_postmeta_price ON ({$wpdb->posts}.ID = tcp_postmeta_price.post_id AND tcp_postmeta_price.meta_key='tcp_price')";
			elseif ( $_SESSION['tcp_order_type'] == 'order' )
				$join .= " LEFT JOIN {$wpdb->postmeta} tcp_postmeta_order ON ({$wpdb->posts}.ID = tcp_postmeta_order.post_id AND tcp_postmeta_order.meta_key='tcp_order')";
			elseif ( $_SESSION['tcp_order_type'] == 'author' )
				$join .= " INNER JOIN {$wpdb->users} tcp_users ON ({$wpdb->posts}.post_author = tcp_users.ID)";
		return $join;
	}

	function postsWhere( $where ) {
		$where .= " AND (tcp_postmeta_is_visible.meta_value='1' OR tcp_postmeta_is_visible.meta_value IS NULL)";
		return $where;
	}

	function postsOrderby( $orderby ) {
		global $wpdb;

		switch ( $_SESSION['tcp_order_type'] ) {
			case 'order':
				$orderby = "tcp_postmeta_order.meta_value {$_SESSION['tcp_order_desc']}, {$wpdb->posts}.post_title";
				break;
			case 'price':
				$orderby = "tcp_postmeta_price.meta_value {$_SESSION['tcp_order_desc']}, {$wpdb->posts}.post_title";
				break;
			case 'title':
				$orderby = "{$wpdb->posts}.post_title {$_SESSION['tcp_order_desc']}";
				break;
			case 'date':
				$orderby = "{$wpdb->posts}.post_date {$_SESSION['tcp_order_desc']}"; //, {$wpdb->posts}.post_title";
				break;
			case 'author':
				$orderby = "tcp_users.display_name {$_SESSION['tcp_order_desc']}, {$wpdb->posts}.post_title";
				break;
			case 'rand':
				$orderby = ' rand()';
				break;
			case 'comment_count':
				$orderby = "{$wpdb->posts}.comment_count {$_SESSION['tcp_order_desc']}, {$wpdb->posts}.post_title";
				break;
		}
		return $orderby;
	}

	function postsJoinNext( $join ) {
		global $wpdb;

		$join .= " LEFT JOIN {$wpdb->postmeta} tcp_postmeta_is_visible ON (p.ID = tcp_postmeta_is_visible.post_id AND tcp_postmeta_is_visible.meta_key='tcp_is_visible' )";
		return $join;
	}

	function postsWhereNext( $where ) {
		$where .= " AND (tcp_postmeta_is_visible.meta_value='1' OR tcp_postmeta_is_visible.meta_value IS NULL)";
		return $where;
	}

/*	function postsRequest( $input ) {
//echo '<br>SELECT: ', $input;
		return $input;
	}*/

	//WP 3.1
	/*function parseQuery( $query ) {
		if ( ! is_admin() ) {
			if ( is_tax( 'tcp_product_category' ) || is_tax( 'tcp_product_tag' ) || is_tax( 'tcp_product_supplier' ) ) {
//			if ( ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'tcp_product' ) 
//				|| isset( $query->query_vars['tcp_product_category'] ) 
//				|| isset( $query->query_vars['tcp_product_tag'] ) 
//				|| isset( $query->query_vars['tcp_product_supplier'] )
//				|| ( isset( $query->query_vars['tax_query'][0]['taxonomy'] ) && $query->query_vars['tax_query'][0]['taxonomy'] == 'tcp_product_category' )
//				|| ( isset( $query->query_vars['taxonomy'] ) && $query->query_vars['taxonomy'] == 'tcp_product_category' ) ) {
				if ( isset( $_REQUEST['tcp_order_type'] ) )
					$tcp_order_type = $_REQUEST['tcp_order_type'];
				else
					$tcp_order_type = isset( $_SESSION['tcp_order_type'] ) ? $_SESSION['tcp_order_type'] : 'order';
				$_SESSION['tcp_order_type'] = $tcp_order_type;
				if ( isset( $_REQUEST['tcp_order_desc'] ) )
					$tcp_order_desc =  $_REQUEST['tcp_order_desc'];
				else
					$tcp_order_desc = isset( $_SESSION['tcp_order_desc'] ) ? $_SESSION['tcp_order_desc'] : 'asc';
				$_SESSION['tcp_order_desc'] = $tcp_order_desc;
		        $query->query_vars['order'] = strtoupper( $_SESSION['tcp_order_desc'] );
		        $query->query_vars['meta_key'] = 'tcp_price';
		        $query->query_vars['post_type'] = 'tcp_product';
				switch ( $_SESSION['tcp_order_type'] ) {
					case 'order':
						$query->query_vars['orderby'] = 'meta_value_num';
						$query->query_vars['meta_key'] = 'tcp_order';
						break;
					case 'price':
						$query->query_vars['orderby'] = 'meta_value_num';
						$query->query_vars['meta_key'] = 'tcp_price';
						break;
					case 'title':
						$query->query_vars['orderby'] = 'title';
						break;
					case 'date':
						$query->query_vars['orderby'] = 'date';
						break;
					case 'author':
						$query->query_vars['orderby'] = 'author title';
						break;
					case 'rand':
						$query->query_vars['orderby'] = 'rand';
						break;
					case 'comment_count':
						$query->query_vars['orderby'] = 'comment_count title';
						break;
				}
				$query->query_vars['meta_query'] = 	array(
					array(
						'key'		=> 'tcp_price',
						'value'		=> 0,
						'compare'	=> '>=',
						'type'		=> 'numeric'
					),
					array(
						'key'		=> 'tcp_order',
						'value'		=> 0,
						'compare'	=> '>=',
						'type'		=> 'numeric'
					),
					array(
						'key'		=> 'tcp_is_visible',
						'value'		=> 1,
						'compare'	=> '=',
						'type'		=> 'numeric',
					),
				);
			}
		}
		return $query;
	}*/

	function loginFormBottom( $content ) {
		return '<p class="login-lostpassword"><a href="'. wp_lostpassword_url( get_permalink() ) . '" title="' . __( 'Lost Password', 'tcp' ) . '">' . __( 'Lost Password', 'tcp' ) . '</a></p>';
	}

	function shortCodeBuyButton( $atts ) {
		extract( shortcode_atts( array( 'post_id' => 0 ), $atts ) );
		return BuyButton::show( $post_id, false );
	}

	function wpMeta() {
		echo '<li><a href="http://thecartpress.com" title="', __( 'Powered by TheCartPress, eCommerce platform for WordPress', 'tcp' ), '">TheCartPress.com</a></li>';
	}

	function adminFooterText( $content ) {
		$pos = strrpos( $content, '</a>.' ) + strlen( '</a>' );
		$content = substr( $content, 0, $pos ) . ' and <a href="http://thecartpress.com">TheCartPress</a>' . substr( $content, $pos );
		return $content;
	}

	function addMenuAdminBar() {
		global $wp_admin_bar;
		if ( is_admin_bar_showing() && current_user_can( 'tcp_read_orders' ) ) {
			$wp_admin_bar->add_menu(
				array(
					'id'	=> 'the_cart_press',
					'title'	=>__( 'TheCartPress', 'tcp' ),
					'href'	=> admin_url( 'admin.php' ) . '?page=thecartpress/admin/OrdersList.php',
				)
			);
			$wp_admin_bar->add_menu(
				array(
					'parent'	=> 'the_cart_press',
					'id'		=> 'orders_list',
					'title'		=>__( 'Orders', 'tcp' ),
					'href'		=> admin_url( 'admin.php' ) . '?page=thecartpress/admin/OrdersList.php',
				)
			);
			if ( current_user_can( 'tcp_downloadable_products' ) )
				$wp_admin_bar->add_menu(
					array(
						'parent'	=> 'the_cart_press',
						'id'		=> 'download_area',
						'title'		=> __( 'Download area', 'tcp' ),
						'href'		=> admin_url( 'admin.php' ) . "?page=thecartpress/admin/DownloadableList.php",
					)
				);
			}
	}

	/**
	 * Runs when a user is registered (or created) before email
	 */
	function userRegister( $user_id ) {
		$user = new WP_User( $user_id );
		$user->set_role( 'customer' );
	}

	function dashboardSetup() {
		require_once( dirname( __FILE__ ) . '/widgets/OrdersResumeDashboard.class.php' );
		$ordersResumeDashboard = new OrdersResumeDashboard();
		wp_add_dashboard_widget( 'tcp_orders_resume', __( 'Orders resume', 'tcp' ), array( $ordersResumeDashboard, 'show' ) );
		wp_add_dashboard_widget( 'thecartpress_rss_widget', __( 'TheCartPress blog', 'tcp' ), array($this, 'theCartPressRSSDashboardWidget' ) );
	}

	function theCartPressRSSDashboardWidget() {
		wp_widget_rss_output( 'http://thecartpress.com/feed', array( 'items' => 5, 'show_author' => 1, 'show_date' => 1, 'show_summary' => 0 ) );
	}

	function registerWidgets() {
		register_widget( 'ResumenShoppingCartWidget' );
		register_widget( 'ShoppingCartWidget' );
		register_widget( 'LastVisitedWidget' );
		register_widget( 'RelatedListWidget' );
		register_widget( 'CustomPostTypeListWidget' );
		register_widget( 'TaxonomyCloudsPostTypeWidget' );
		register_widget( 'TaxonomyTreesPostTypeWidget' );
		register_widget( 'OrderPanelWidget' );
		register_widget( 'CommentsCustomPostTypeWidget' );
		//register_widget( 'ArchivesCustomPostType' );
	}

	function createMenu() {
		$base = dirname( __FILE__ ) . '/admin/OrdersList.php';
		add_menu_page( '', 'TheCartPress', 'tcp_read_orders', $base, '', plugins_url( '/images/tcp.png', __FILE__ ) );
		add_submenu_page( $base, __( 'Orders', 'tcp' ), __( 'Orders', 'tcp' ), 'tcp_read_orders', $base );
		add_submenu_page( $base, __( 'Addresses', 'tcp' ), __( 'Addresses', 'tcp' ), 'tcp_edit_addresses', dirname( __FILE__ ) . '/admin/AddressesList.php' );
		add_submenu_page( $base, __( 'Taxes', 'tcp' ), __( 'Taxes', 'tcp' ), 'tcp_edit_taxes', dirname( __FILE__ ) . '/admin/TaxesList.php' );
		add_submenu_page( $base, __( 'Plugins', 'tcp' ), __( 'Plugins', 'tcp' ), 'tcp_edit_plugins', dirname( __FILE__ ) . '/admin/PluginsList.php' );
		add_submenu_page( $base, __( 'Related Categories', 'tcp' ), __( 'Related Categories', 'tcp' ), 'tcp_edit_products', dirname( __FILE__ ) . '/admin/RelatedCats.php' );
		add_submenu_page( $base, __( 'Downloadable Products', 'tcp' ), __( 'Downloadable Products', 'tcp' ), 'tcp_downloadable_products', dirname( __FILE__ ) . '/admin/DownloadableList.php' );
		add_submenu_page( $base, __( 'Update Prices', 'tcp' ), __( 'Update Prices', 'tcp' ), 'tcp_update_price', dirname( __FILE__ ) . '/admin/PriceUpdate.php' );
		add_submenu_page( $base, __( 'Update Stock', 'tcp' ), __( 'Update Stock', 'tcp' ), 'tcp_update_stock', dirname( __FILE__ ) . '/admin/StockUpdate.php' );
		add_submenu_page( $base, __( 'Shortcodes', 'tcp' ), __( 'Shortcodes', 'tcp' ), 'tcp_shortcode_generator', dirname( __FILE__ ) . '/admin/ShortCodeGenerator.php' );
		//register pages
		add_submenu_page( 'tcp', __( 'list of Assigned products', 'tcp' ), __( 'list of Assigned products', 'tcp' ), 'tcp_edit_product', dirname( __FILE__ ) . '/admin/AssignedProductsList.php' );
		add_submenu_page( 'tcp', __( 'list of Orders', 'tcp' ), __( 'list of Orders', 'tcp' ), 'tcp_edit_orders', dirname( __FILE__ ) . '/admin/OrderEdit.php' );
		add_submenu_page( 'tcp', __( 'Plugin editor', 'tcp' ), __( 'Plugin editor', 'tcp' ), 'tcp_edit_plugins', dirname( __FILE__ ) . '/admin/PluginEdit.php' );
		add_submenu_page( 'tcp', __( 'Address editor', 'tcp' ), __( 'Address editor', 'tcp' ), 'tcp_edit_addresses', dirname( __FILE__ ) . '/admin/AddressEdit.php' );
		add_submenu_page( 'tcp', __( 'Upload files', 'tcp' ), __( 'Upload files', 'tcp' ), 'tcp_edit_product', dirname( __FILE__ ) . '/admin/UploadFiles.php' );
		add_submenu_page( 'tcp', __( 'Downloadable products', 'tcp' ), __( 'Downloadable products', 'tcp' ), 'tcp_downloadable_products', dirname( __FILE__ ) . '/admin/VirtualProductDownloader.php' );
	}

	function contentFilter( $content ) {
		if ( is_single() ) {
			global $post;
			if ( $post->post_type != 'tcp_product' ) return $content;
			$settings = get_option( 'tcp_settings' );
			$see_buy_button_in_content = isset( $settings['see_buy_button_in_content'] ) ? $settings['see_buy_button_in_content'] : true;
			$see_price_in_content = isset( $settings['see_price_in_content'] ) ? $settings['see_price_in_content'] : false;
			$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';
			if ( $see_buy_button_in_content )
				return BuyButton::show( $post->ID, false ) . $content;
			elseif ( $see_price_in_content ) {
				$tax = tcp_get_the_tax_label( $post->ID );
				if ( strlen( $tax ) ) $tax = '(' . $tax . ')';
				return '<p id="tcp_price_post-"' . $post->ID . '>' . tcp_get_the_price_label( $post->ID ) . ' ' . $currency . ' ' . $tax . '</p>' . $content;
			} else
				return $content;
		} else return $content;
	}

	function excerptFilter( $content ) {
		if ( ! is_single() ) {
			global $post;
			if ( $post->post_type != 'tcp_product' ) return $content;
			$settings = get_option( 'tcp_settings' );
			$see_buy_button_in_excerpt = isset( $settings['see_buy_button_in_excerpt'] ) ? $settings['see_buy_button_in_excerpt'] : false;
			$see_price_in_excerpt = isset( $settings['see_price_in_excerpt'] ) ? $settings['see_price_in_excerpt'] : true;
			$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'EUR';
			if ( $see_buy_button_in_excerpt ) 
				return BuyButton::show( $post->ID, false ) . $content;
			elseif ( $see_price_in_excerpt ) {
				$tax = tcp_get_the_tax_label( $post->ID );
				if ( strlen( $tax ) ) $tax = '(' . $tax . ')';
				return '<p id="tcp_price_post-"' . $post->ID . '>' . tcp_get_the_price_label( $post->ID ) . ' ' . $currency . ' ' . $tax . '</p>' . $content;
			} else
				return $content;
		} else return $content;
	}

	function loadingDefaultCheckoutPlugins() {
		//shipping methods
		require_once( dirname( __FILE__ ) . '/plugins/FreeTrans.class.php' );
		tcp_register_shipping_plugin( 'FreeTrans' );
		require_once( dirname( __FILE__ ) . '/plugins/FlatRate.class.php' );
		tcp_register_shipping_plugin( 'FlatRate' );
		require_once( dirname( __FILE__ ) . '/plugins/ShippingCost.class.php' );
		tcp_register_shipping_plugin( 'ShippingCost' );

		//payment methods
		require_once( dirname( __FILE__ ) . '/plugins/PayPal/PayPal.php' );
		tcp_register_payment_plugin( 'PayPal' );
		require_once( dirname( __FILE__ ) . '/plugins/Remboursement.class.php' );
		tcp_register_payment_plugin( 'Remboursement' );
		require_once( dirname( __FILE__ ) . '/plugins/NoCostPayment.class.php' );
		tcp_register_payment_plugin( 'NoCostPayment' );
		require_once( dirname( __FILE__ ) . '/plugins/Transference.class.php' );
		tcp_register_payment_plugin( 'Transference' );
		require_once( dirname( __FILE__ ) . '/plugins/CardOffLine/CardOffLine.class.php' );
		tcp_register_payment_plugin( 'CardOffLine' );
	}

	function activatePlugin() {
		global $wp_version;
		if ( version_compare( $wp_version, '3.0', '<' ) ) {
			exit( __( 'TheCartPress requires WordPress version 3.0 or newer.', 'tcp' ) );
		}
		require_once( dirname( __FILE__ ) . '/daos/RelEntities.class.php' );
		RelEntities::createTable();
		require_once( dirname( __FILE__ ) . '/daos/Addresses.class.php' );
		Addresses::createTable();
		require_once( dirname( __FILE__ ) . '/daos/Taxes.class.php' );
		Taxes::createTable();
		Taxes::initData();
		require_once( dirname( __FILE__ ) . '/daos/Countries.class.php' );
		Countries::createTable();
		Countries::initData();
		require_once( dirname( __FILE__ ) . '/daos/Orders.class.php' );
		Orders::createTable();
		require_once( dirname( __FILE__ ) . '/daos/OrdersDetails.class.php' );
		OrdersDetails::createTable();
		require_once( dirname( __FILE__ ) . '/daos/Currencies.class.php' );
		Currencies::createTable();
		Currencies::initData();
		//Pages: shopping cart and checkout
		if ( ! get_option( 'tcp_shopping_cart_page_id' ) ) {
			$post = array(
			  'comment_status'	=> 'closed',
			  'post_content'	=> '[tcp_shopping_cart]',
			  'post_status'		=> 'publish',
			  'post_title'		=> __( 'Shopping cart','tcp' ),
			  'post_type'		=> 'page',
			);
			$shopping_cart_page_id = wp_insert_post( $post );
			update_option( 'tcp_shopping_cart_page_id', $shopping_cart_page_id );
		} else
			wp_publish_post( (int)get_option( 'tcp_shopping_cart_page_id' ) );
		if ( ! get_option( 'tcp_checkout_page_id' ) ) {
			$post = array(
			  'comment_status'	=> 'closed',
			  'post_content'	=> '[tcp_checkout]',
			  'post_status'		=> 'publish',
			  'post_title'		=> __( 'Checkout','tcp' ),
			  'post_type'		=> 'page',
			);
			$checkout_page_id = wp_insert_post( $post );
			update_option( 'tcp_checkout_page_id', $checkout_page_id );
		} else
			wp_publish_post( (int)get_option( 'tcp_checkout_page_id' ) );
		//initial shipping and payment method
		add_option( 'tcp_plugins_data_shi_FreeTrans', array(
				array(
					'all_countries'	=> 'yes',
					'countries'		=> array(),
					'new_status'	=> 'PENDING',
					'minimun'		=> 0,
				),
			)
		);
		add_option( 'tcp_plugins_data_pay_Remboursement', array(
				array(
					'all_countries'	=> 'yes',
					'countries'		=> array(),
					'new_status'	=> 'PROCESSING',
					'notice'		=> 'Cash on delivery! (5%)',
					'percentage'	=> 5,
				),
			)
		);
		add_option( 'tcp_shortcodes_data', array( array(
			'id'					=> 'all_products',
			'title'					=> '',
			'desc'					=> 'List of all products',
			'post_type'				=> 'tcp_product',
			'use_taxonomy'			=> false,
			'taxonomy'				=> 'tcp_product_category',
			'included'				=> array(),
			'term'					=> '', //'tables',
			'loop'					=> '',
			'columns'				=> 2,
			'see_title'				=> true,
			'see_image'				=> false,
			'image_size'			=> 'thumbnail',
			'see_content'			=> false,
			'see_excerpt'			=> true,
			'see_author'			=> false,
			'see_meta_data'			=> false,
			'see_meta_utilities'	=> false,
			'see_price'				=> false,
			'see_buy_button'		=> false,
			'see_first_custom_area'	=> false,
			'see_second_custom_area'=> false,
			'see_third_custom_area'	=> false,
		) ) );
		$settings = array(
			'legal_notice'				=> __( 'Legal notice', 'tcp' ),
			'stock_management'			=> false,
			'disable_shopping_cart'		=> false,
			'user_registration'			=> false,
			'see_buy_button_in_content'	=> true,
			'see_buy_button_in_excerpt'	=> false,
			'see_price_in_content'		=> false,
			'see_price_in_excerpt'		=> true,
			'downloadable_path'			=> '',
			'load_default_styles'		=> true,
			'search_engine_activated'	=> true,
			'currency'					=> 'EUR',
			'unit_weight'				=> 'gr',
			'product_rewrite'			=> 'product',
			'category_rewrite'			=> 'product_category',
			'tag_rewrite'				=> 'product_tag',
			'supplier_rewrite'			=> 'product_supplier',
			'hide_visibles'				=> false,
		);
		add_option( 'tcp_settings', $settings );
		//Roles & capabilities
		add_role( 'customer', __( 'Customer', 'tcp' ) );
	 	$customer = get_role( 'customer' );
		$customer->add_cap( 'tcp_read_orders' );
		$customer->add_cap( 'tcp_edit_addresses' );
		$customer->add_cap( 'tcp_downloadable_products' );
		$subscriber = get_role( 'subscriber' );
		if ( $subscriber ) {
			$caps = (array)$subscriber->capabilities;
			foreach( $caps as $cap => $grant )
				if ( $grant ) $customer->add_cap( $cap );
		}
		$administrator = get_role( 'administrator' );
		$administrator->add_cap( 'tcp_edit_product' );
		$administrator->add_cap( 'tcp_edit_products' );
		$administrator->add_cap( 'tcp_edit_others_products' );
		$administrator->add_cap( 'tcp_publish_products' );
		$administrator->add_cap( 'tcp_read_product' );
		$administrator->add_cap( 'tcp_delete_product' );
		$administrator->add_cap( 'tcp_users_roles' );
		$administrator->add_cap( 'tcp_edit_orders' );
		$administrator->add_cap( 'tcp_read_orders' );
		$administrator->add_cap( 'tcp_edit_settings' );
		$administrator->add_cap( 'tcp_edit_plugins' );
		$administrator->add_cap( 'tcp_update_price' );
		$administrator->add_cap( 'tcp_update_stock' );
		$administrator->add_cap( 'tcp_downloadable_products' );
		$administrator->add_cap( 'tcp_edit_addresses' );
		$administrator->add_cap( 'tcp_edit_taxes' );
		$administrator->add_cap( 'tcp_shortcode_generator' );

		add_role( 'merchant', __( 'Merchant', 'tcp' ) );
	 	$merchant = get_role( 'merchant' );
		$merchant->add_cap( 'tcp_edit_product' );
		$merchant->add_cap( 'tcp_edit_products' );
		$merchant->add_cap( 'tcp_edit_others_products' );
		$merchant->add_cap( 'tcp_publish_products' );
		$merchant->add_cap( 'tcp_read_product' );
		$merchant->add_cap( 'tcp_delete_product' );
		$merchant->add_cap( 'tcp_edit_orders' );
		$merchant->add_cap( 'tcp_read_orders' );
		$merchant->add_cap( 'tcp_update_price' );
		$merchant->add_cap( 'tcp_update_stock' );
		$merchant->add_cap( 'tcp_edit_addresses' );
		$merchant->add_cap( 'tcp_downloadable_products' );
		$merchant->add_cap( 'tcp_users_roles' );				
		$merchant->add_cap( 'tcp_edit_settings' );
		$merchant->add_cap( 'tcp_edit_plugins' );
		$merchant->add_cap( 'tcp_edit_taxes' );
		$merchant->add_cap( 'tcp_shortcode_generator' );

		$editor = get_role( 'editor' );
		if ( $editor ) {
			$caps = (array)$editor->capabilities;
			foreach( $caps as $cap => $grant )
				if ( $grant ) {
					$merchant->add_cap( $cap );
				}
		}
	}

	function deactivatePlugin() {
		//delete pages
		$id = get_option( 'tcp_shopping_cart_page_id' );
		wp_delete_post( $id );
		$id = get_option( 'tcp_checkout_page_id' );
		wp_delete_post( $id );
		//remove roles
		remove_role( 'customer' );
		remove_role( 'merchant' );
	}

	function init() {
		new ProductCustomPostType();
		if ( is_admin() ) {
			wp_register_script( 'tcp_scripts', plugins_url( 'thecartpress/js/tcp_admin_scripts.js' ) );
			wp_enqueue_script( 'tcp_scripts' );
		} else {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'tcp_scripts' );
			$settings = get_option( 'tcp_settings' );
			$load_default_styles = isset( $settings['load_default_styles'] ) ? $settings['load_default_styles'] : true;
			if ( $load_default_styles )
				wp_enqueue_style( 'tcp_style', plugins_url( 'thecartpress/css/tcp_base.css' ) );	
		}
	    //feed
		//http://localhost/<tcp>/?feed=tcp-products
		add_feed( 'tcp-products', array( $this, 'registerProductsFeed' ) );
		global $wp_rewrite;
		add_action( 'generate_rewrite_rules', array( $this, 'rewriteRules' ) );
		$wp_rewrite->flush_rules();
		$version = (int)get_option( 'tcp_version', 0 );
		if ( $version < 104 ) {
			//
			//TODO Deprecated 1.1
			//
			global $wpdb;
			$sql = 'update ' . $wpdb->prefix .'term_taxonomy set taxonomy=\'tcp_product_supplier\'
			where taxonomy=\'tcp_product_supplier_tag\'';
			$wpdb->query( $sql );
		
			$posts = get_posts( array( 'post_type' => 'tcp_product', 'numberposts' => -1 ) );
			if ( is_array( $posts ) && count( $posts ) > 0 ) {
				foreach( $posts as $post ) {
					$order = tcp_get_the_order( $post->ID );
					if ( $order == 0 )
						update_post_meta( $post->ID, 'tcp_order', 0 );
				}
			}
			$count = $wpdb->get_var( 'select count(*) from ' . $wpdb->prefix . 'tcp_countries' );
			if ( $count == 234 ) {
				$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'YT\',\'MAYOTTE\',\'MAYOTTE\',\'MAYOTTE\',\'MAYOTTE\',\'MAYOTTE\',\'MYT\',175 ,0 ,0 ,0),
				 (\'ZA\',\'SOUTH AFRICA\',\'SOUTH AFRICA\',\'SUDÁFRICA\',\'SÜDAFRIKA, REPUBLIK\',\'AFRIQUE DU SUD\',\'ZAF\',710 ,0 ,0 ,0),
				 (\'ZM\',\'ZAMBIA\',\'ZAMBIA\',\'ZAMBIA\',\'SAMBIA\',\'ZAMBIE\',\'ZMB\',894 ,0 ,0 ,0),
				 (\'ZW\',\'ZIMBABWE\',\'ZIMBABWE\',\'ZIMBABUE\',\'SIMBABWE\',\'ZIMBABWE\',\'ZWE\',716 ,0 ,0 ,0);';
				$wpdb->query( $sql );
			}
			//
			//TODO Deprecated 1.1
			//
			update_option( 'tcp_version', 104 );
		}
		if ($version < 105 ) {
			//
			//TODO Deprecated 1.1
			//
			global $wpdb;
			$sql = 'ALTER TABLE `'. $wpdb->prefix . 'tcp_orders`
					MODIFY COLUMN `shipping_email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					MODIFY COLUMN `billing_email`  VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
			$wpdb->query( $sql );
			$sql = 'ALTER TABLE `'. $wpdb->prefix . 'tcp_addresses`
					MODIFY COLUMN `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
			$wpdb->query( $sql );
			remove_role( 'super_merchant' );			
			$settings = get_option( 'tcp_settings' );
			$settings['product_rewrite']	= 'product';
			$settings['category_rewrite']	= 'category';
			$settings['tag_rewrite']		= 'tag';
			$settings['supplier_rewrite']	= 'supplier';
			update_option( 'tcp_settings', $settings );
			add_option( 'tcp_shortcodes_data', array( array(
				'id'					=> 'all_products',
				'title'					=> '',
				'desc'					=> 'List of all products',
				'post_type'				=> 'tcp_product',
				'use_taxonomy'			=> false,
				'taxonomy'				=> 'tcp_product_category',
				'included'				=> array(),
				'term'					=> '', //'tables',
				'loop'					=> '',
				'columns'				=> 2,
				'see_title'				=> true,
				'see_image'				=> false,
				'image_size'			=> 'thumbnail',
				'see_content'			=> false,
				'see_excerpt'			=> true,
				'see_author'			=> false,
				'see_meta_data'			=> false,
				'see_meta_utilities'	=> false,
				'see_price'				=> false,
				'see_buy_button'		=> false,
				'see_first_custom_area'	=> false,
				'see_second_custom_area'=> false,
				'see_third_custom_area'	=> false,
			) ) );
			update_option( 'tcp_version', 105 );
			//
			//TODO Deprecated 1.1
			//
		}
	}

	/**
	 * Allows to generate the xml for thecartpress search engine
	 */
	function registerProductsFeed() {
		require_once( dirname( __FILE__ ) . '/classes/FeedForSearchEngine.class.php' );
		$feedForSearchEngine = new FeedForSearchEngine();
		$feedForSearchEngine->generateXML();
	}

	function rewriteRules( $wp_rewrite ) {
		$new_rules = array(
			'feed/(.+)' => 'index.php?feed=' . $wp_rewrite->preg_index( 1 )
		);
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}

new TheCartPress();

include_once( dirname( __FILE__ ) . '/templates/tcp_template.php' );
?>
