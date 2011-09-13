<?php
/*
Plugin Name: TheCartPress
Plugin URI: http://thecartpress.com
Description: TheCartPress (Multi language support)
Version: 1.1.1
Author: TheCartPress team
Author URI: http://thecartpress.com
License: GPL
Parent: thecartpress
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

function __autoload( $class_name ) {
    if ( $class_name == 'ShoppingCart' ) require_once( dirname( __FILE__ ) . '/classes/ShoppingCart.class.php' );
}
//if ( ! is_admin() ) require_once( dirname( __FILE__ ) . '/classes/ShoppingCart.class.php' );
require_once( dirname( __FILE__ ) . '/customposttypes/ProductCustomPostType.class.php' );
require_once( dirname( __FILE__ ) . '/customposttypes/TemplateCustomPostType.class.php' );
require_once( dirname( __FILE__ ) . '/metaboxes/CustomTemplateMetabox.class.php' );
require_once( dirname( __FILE__ ) . '/classes/TCP_Plugin.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/ShoppingCartSummaryWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/ShoppingCartWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/LastVisitedWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/WishListWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/RelatedListWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/CustomPostTypeListWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/TaxonomyCloudsPostTypeWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/TaxonomyTreesPostTypeWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/SortPanelWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/CommentsCustomPostTypeWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/BrothersListWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/CheckoutWidget.class.php' );
require_once( dirname( __FILE__ ) . '/widgets/TCPArchivesWidget.class.php' );
require_once( dirname( __FILE__ ) . '/admin/TCP_Settings.class.php' );
			
class TheCartPress {

	public $settings = array();
	private $saleable_post_types = array();

	function __construct() {	
		$this->loadSettings();
		if ( function_exists( 'add_theme_support' ) ) add_theme_support( 'post-thumbnails' );
		$disable_ecommerce = isset( $this->settings['disable_ecommerce'] ) ? $this->settings['disable_ecommerce'] : false;
		add_action( 'init', array( $this, 'init' ) );
		if ( ! $disable_ecommerce ) {
			add_action( 'user_register', array( $this, 'user_register' ) );
			if ( is_admin() ) {
				register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
				register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
				require_once( dirname( __FILE__ ) . '/metaboxes/ProductCustomFieldsMetabox.class.php' );
				$productCustomFieldsMetabox = new ProductCustomFieldsMetabox();
				add_action( 'admin_init', array( $productCustomFieldsMetabox, 'registerMetaBox' ) );
				require_once( dirname( __FILE__ ) . '/metaboxes/PostMetabox.class.php' );
				$postMetabox = new PostMetabox();
				add_action( 'admin_init', array( $postMetabox, 'registerMetaBox' ) );
				require_once( dirname( __FILE__ ) . '/metaboxes/TemplateMetabox.class.php' );
				$templateMetabox = new TCPTemplateMetabox();
				add_action( 'admin_init', array( $templateMetabox, 'registerMetaBox' ) );
				//if ( function_exists( 'register_theme_directory') ) register_theme_directory( WP_PLUGIN_DIR . '/thecartpress/themes-templates' );
				add_action( 'tcp_product_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
				add_action( 'tcp_product_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
				add_action( 'tcp_product_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );
				add_filter( 'screen_settings', array( $this, 'screen_settings' ), 10, 2);
			} else {
				add_filter( 'the_content', array( $this, 'the_content' ) );
				add_filter( 'the_excerpt', array( $this, 'the_excerpt' ) );
				add_action( 'wp_head', array( $this, 'wp_head' ) );
				add_filter( 'request', array( $this, 'request' ) );
				add_filter( 'posts_request', array( $this, 'posts_request' ) );
				add_filter( 'posts_join', array( $this, 'posts_join' ) );
				add_filter( 'posts_where', array( $this, 'posts_where' ) );
				add_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );
				add_filter( 'get_pagenum_link', array( $this, 'get_pagenum_link' ) );
				add_filter( 'get_previous_post_join', array( $this, 'postsJoinNext' ) );
				add_filter( 'get_previous_post_where', array( $this, 'postsWhereNext' ) );
				add_filter( 'get_next_post_join', array( $this, 'postsJoinNext' ) );
				add_filter( 'get_next_post_where', array( $this, 'postsWhereNext' ) );
				add_shortcode( 'tcp_buy_button', array( $this, 'shortCodeBuyButton' ) );
				add_shortcode( 'tcp_price', array( $this, 'shortCodePrice' ) );
				require_once( dirname( __FILE__ ) . '/shortcodes/ShoppingCartPage.class.php' );
				$shoppingCartPage = new TCP_ShoppingCartPage();
				add_shortcode( 'tcp_shopping_cart', array( $shoppingCartPage, 'show' ) );
				require_once( dirname( __FILE__ ) . '/checkout/ActiveCheckout.class.php' );
				$activeCheckout = new ActiveCheckout();
				add_shortcode( 'tcp_checkout', array( $activeCheckout, 'show' ) );
				add_filter( 'login_form_bottom', array( $this, 'loginFormBottom' ) );
				add_action( 'twentyten_credits', array( $this, 'twentyten_credits' ) );
				add_action( 'twentyeleven_credits', array( $this, 'twentyten_credits' ) );
			}
			add_filter( 'tcp_get_saleable_post_types', array( $this, 'tcp_get_saleable_post_types' ) );
			add_filter( 'tcp_the_currency', array( $this, 'tcp_the_currency' ) );
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 65 );
			add_action( 'wp_before_admin_bar_render', array( $this, 'wp_before_admin_bar_render' ) );
		}
		if ( is_admin() ) {
			add_filter( 'extra_plugin_headers', array( $this, 'extra_plugin_headers' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ) , 10, 4 );
			add_filter( 'views_plugins', array( $this, 'views_plugins' ) );
			add_filter( 'all_plugins', array( $this, 'all_plugins' ) );
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			$customTemplateMetabox = new TCPCustomTemplateMetabox();
			add_action( 'admin_init', array( $customTemplateMetabox, 'registerMetaBox' ) );
			new TCP_Settings();
			add_action( 'tcp_show_settings', array( $this,  'tcp_show_settings' ) );
			require_once( dirname( __FILE__ ) . '/admin/TCP_LoopsSettings.class.php' );
			new TCP_LoopsSettings();
			add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
		} else {
			require_once( dirname( __FILE__ ) . '/shortcodes/TCP_Shortcode.class.php' );
			$tcp_shortcode = new TCP_Shortcode();
			add_shortcode( 'tcp_list', array( $tcp_shortcode, 'show' ) );
			add_filter( 'single_template', array( $this, 'single_template' ) );
			add_filter( 'taxonomy_template', array( $this, 'taxonomy_template' ) );
			add_filter( 'archive_template', array( $this, 'archive_template' ) );
			add_action( 'wp_meta', array( $this, 'wp_meta' ) );
		}
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	function wp_head() {
		if ( is_single() && ! is_page() ) {//Last visited
			global $post;
			if ( tcp_is_saleable_post_type( $post->post_type ) ) {
				do_action( 'tcp_visited_product', $post );
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->addVisitedPost( $post->ID );
			}
		}
		//Shopping Cart actions
		if ( isset( $_REQUEST['tcp_add_to_shopping_cart'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$post_id = isset( $_REQUEST['tcp_post_id'] ) ? $_REQUEST['tcp_post_id'] : 0;
			do_action( 'tcp_before_add_shopping_cart', $post_id );
			if ( is_array( $post_id ) ) {
				for( $i = 0; $i < count( $_REQUEST['tcp_post_id'] ); $i++ ) {
					$count = isset( $_REQUEST['tcp_count'][$i] ) ? (int)$_REQUEST['tcp_count'][$i] : 0;
					if ( $count > 0 ) {
						$post_id		= isset( $_REQUEST['tcp_post_id'][$i] ) ? $_REQUEST['tcp_post_id'][$i] : 0;
						$post_id		= tcp_get_default_id( $post_id, get_post_type( $post_id ) );
						$tcp_option_id	= isset( $_REQUEST['tcp_option_id'][$i] ) ? $_REQUEST['tcp_option_id'][$i] : 0;
						if ( $tcp_option_id > 0 ) {
							$option_ids = explode( '-',  $tcp_option_id);
							if ( count( $option_ids ) == 2 ) {
								$option_1_id		= $option_ids[0];
								$price_1			= tcp_get_the_price( $option_1_id );
								$price_to_show_1	= tcp_get_the_price_to_show( $post_id, $price_1 );
								$price_1			= tcp_get_the_price_without_tax( $post_id, $price_1 );
								$option_2_id		= $option_ids[1];
								$price_2			= tcp_get_the_price( $option_2_id );
								$price_to_show_2	= tcp_get_the_price_to_show( $post_id, $price_2 );
								$price_2			= tcp_get_the_price_without_tax( $post_id, $price_2 );
							} else {
								$option_1_id		= $tcp_option_id;
								$price_1			= tcp_get_the_price( $option_1_id );
								$price_to_show_1	= tcp_get_the_price_to_show( $post_id, $price_1 );
								$price_1			= tcp_get_the_price_without_tax( $post_id, $price_1 );
								$option_2_id		= '0';
								$price_2			= 0;
								$price_to_show_2	= 0;
							}
						} else {
							$option_1_id		= isset( $_REQUEST['tcp_option_1_id'][$i] ) ? $_REQUEST['tcp_option_1_id'][$i] : 0;
							$price_1			= tcp_get_the_price( $option_1_id );
							$price_to_show_1	= $option_1_id > 0 ? tcp_get_the_price_to_show( $post_id, $price_1 ) : 0;
							$price_1			= $option_1_id > 0 ? tcp_get_the_price_without_tax( $post_id, $price_1 ) : 0;
							$option_2_id		= isset( $_REQUEST['tcp_option_2_id'][$i] ) ? $_REQUEST['tcp_option_2_id'][$i] : 0;
							$price_2			= tcp_get_the_price( $option_2_id );
							$price_to_show_2	= $option_2_id > 0 ? tcp_get_the_price_to_show( $post_id, $price_2 ) : 0;
							$price_2			= $option_2_id > 0 ? tcp_get_the_price_without_tax( $post_id, $price_2 ) : 0;
						}
						$price_to_show	= tcp_get_the_price_to_show( $post_id );
						//$price_to_show	= isset( $_REQUEST['tcp_price_to_show'][$i] ) ? $_REQUEST['tcp_price_to_show'][$i] : 0;
						$price_to_show	+= $price_to_show_1 + $price_to_show_2;
						$unit_price		= tcp_get_the_price( $post_id );
						//$unit_price		= isset( $_REQUEST['tcp_unit_price'][$i] ) ? $_REQUEST['tcp_unit_price'][$i] : 0;
						$unit_price		+= $price_1 + $price_2;
						//$tax			= isset( $_REQUEST['tcp_tax'][$i] ) ? $_REQUEST['tcp_tax'][$i] : 0;
						$tax			= tcp_get_the_tax( $post_id );
						//$unit_weight	= isset( $_REQUEST['tcp_unit_weight'][$i] ) ? $_REQUEST['tcp_unit_weight'][$i] : 0;
						$unit_weight	= tcp_get_the_weight( $post_id );
						$shoppingCart->add( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $tax, $unit_weight, $price_to_show );
					}
				}
				do_action( 'tcp_add_shopping_cart', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_delete_shopping_cart'] ) ) {
			do_action( 'tcp_before_delete_shopping_cart' );
			TheCartPress::removeShoppingCart();
			do_action( 'tcp_delete_shopping_cart' );
		} elseif ( isset( $_REQUEST['tcp_delete_item_shopping_cart'] ) ) {
			$post_id = isset( $_REQUEST['tcp_post_id'] ) ? $_REQUEST['tcp_post_id'] : 0;
			do_action( 'tcp_before_delete_item_shopping_cart', $post_id );
			if ( $post_id > 0 ) {
				$option_1_id	= isset( $_REQUEST['tcp_option_1_id'] ) ? $_REQUEST['tcp_option_1_id'] : 0;
				$option_2_id	= isset( $_REQUEST['tcp_option_2_id'] ) ? $_REQUEST['tcp_option_2_id'] : 0;
				$shoppingCart	= TheCartPress::getShoppingCart();
				$shoppingCart->delete( $post_id, $option_1_id, $option_2_id );
				do_action( 'tcp_delete_item_shopping_cart', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_modify_item_shopping_cart'] ) ) {
			$post_id = $_REQUEST['tcp_post_id'] ? $_REQUEST['tcp_post_id'] : 0;
			do_action( 'tcp_before_modify_shopping_cart', $post_id );
			if ( $post_id > 0 ) {
				$option_1_id	= isset( $_REQUEST['tcp_option_1_id'] ) ? $_REQUEST['tcp_option_1_id'] : 0;
				$option_2_id	= isset( $_REQUEST['tcp_option_2_id'] ) ? $_REQUEST['tcp_option_2_id'] : 0;
				$count			= isset( $_REQUEST['tcp_count'] ) ? $_REQUEST['tcp_count'] : 0;
				$shoppingCart	= TheCartPress::getShoppingCart();
				$shoppingCart->modify( $post_id, $option_1_id, $option_2_id, $count );
				do_action( 'tcp_modify_shopping_cart', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_add_to_wish_list'] ) ) {
			$tcp_new_wish_list_item = isset( $_REQUEST['tcp_new_wish_list_item'] ) ? $_REQUEST['tcp_new_wish_list_item'] : 0;
			if ( $tcp_new_wish_list_item > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->addWishList( $tcp_new_wish_list_item );
				do_action( 'tcp_add_wish_list', $tcp_new_wish_list_item );
			}
		} elseif ( isset( $_REQUEST['tcp_remove_from_wish_list'] ) ) {
			$post_id = isset( $_REQUEST['tcp_wish_list_post_id'] ) ? $_REQUEST['tcp_wish_list_post_id'] : 0;
			if ( $post_id > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->deleteWishListItem( $post_id );
				do_action( 'tcp_delete_wish_list_item', $post_id );
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

	static function removeShoppingCart() {
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( $shoppingCart ) $shoppingCart->deleteAll();
	}

	/**
	 * This function check if the plugin is ok
	 */
	public function checkThePlugin() {
		$warnings = array();
		$base = dirname( __FILE__ ) . '/admin/';
		$checking_path = $base . 'Checking.php';
		$checking_path = 'admin.php?page=' . plugin_basename( dirname( __FILE__ ) ) . '/admin/Checking.php';
		$disable_shopping_cart = isset( $this->settings['disable_shopping_cart'] ) ? $this->settings['disable_shopping_cart'] : false;
		if ( ! $disable_shopping_cart ) {
			$page_id = get_option( 'tcp_shopping_cart_page_id' );
			if ( ! $page_id || ! get_page( $page_id ) )
				$warnings[] = __( 'The <strong>Shopping Cart page</strong> has been deleted.', 'tcp' );
			$page_id = get_option( 'tcp_checkout_page_id' );
			if ( ! $page_id || ! get_page( $page_id ) )
				$warnings[] = __( 'The <strong>Checkout page</strong> has been deleted.', 'tcp' );
		}
		if ( count( $warnings ) > 0 ) : ?>
		<div id="message_checking_error" class="error"><p>
			<?php _e( 'Notice:', 'tcp' ); ?><br />
			<?php foreach( $warnings as $warning ) : ?>
				<?php echo $warning; ?><br/>
			<?php endforeach; ?>
			<p><?php printf( __( 'Visit the <a href="%s">Checking page</a> to fix those warnings.', 'tcp' ), $checking_path ); ?></p>
		</p></div>
		<?php endif;
	}

	function request( $query_string ) {
		if ( ! is_admin()) {
			if ( isset ( $query_string['tcp_product_category'] ) || isset ( $query_string['tcp_product_tag'] ) || isset ( $query_string['tcp_product_supplier'] ) ) {
				$products_per_page = isset( $this->settings['products_per_page'] ) ? (int)$this->settings['products_per_page'] : 10;
				$query_string['posts_per_page'] = $products_per_page;
			}
		}
		return $query_string;
	}

	function posts_join( $join ) {
		global $wpdb;
		$join .= " LEFT JOIN {$wpdb->postmeta} tcp_postmeta_is_visible ON ({$wpdb->posts}.ID = tcp_postmeta_is_visible.post_id AND tcp_postmeta_is_visible.meta_key='tcp_is_visible' )";
		if ( is_tax() || is_archive() ) {
			if ( isset( $_REQUEST['tcp_order_by'] ) ) {
				$order_type = isset( $_REQUEST['tcp_order_type'] ) ? $_REQUEST['tcp_order_type'] : 'order';
			} else {
				$settings = get_option( 'ttc_settings' );
				$order_type = isset( $settings['order_type'] ) ? $settings['order_type'] : 'order';
			}
			if ( $order_type == 'price' || $order_type == 'order' ) {
				$join .= " LEFT JOIN {$wpdb->postmeta} tcp_postmeta_order ON ({$wpdb->posts}.ID = tcp_postmeta_order.post_id AND tcp_postmeta_order.meta_key='tcp_$order_type')";
			} else {
				if ( isset( $_REQUEST['tcp_order_by'] ) ) {
					$order_desc = isset( $_REQUEST['tcp_order_desc'] ) ? $_REQUEST['tcp_order_desc'] : 'asc';
				} else {
					if ( ! $settings ) $settings = get_option( 'ttc_settings' );
					$order_desc = isset( $settings['order_desc'] ) ? $settings['order_desc'] : 'asc';
				}
				$join = apply_filters( 'tcp_sorting_join', $join, $order_type, $order_desc );
			}
		}
		return $join;
	}

	function posts_where( $where ) {
		$where .= ' AND (tcp_postmeta_is_visible.meta_value=\'1\' OR tcp_postmeta_is_visible.meta_value IS NULL)';
		return $where;
	}

	function posts_orderby( $order_by ) {
		if ( is_tax() || is_archive() ) {
			global $wp_query;
			$taxonomy = isset( $wp_query->tax_query->queries[0]['taxonomy'] ) ? $wp_query->tax_query->queries[0]['taxonomy'] : false;
			if ( $taxonomy && tcp_is_saleable_taxonomy( $taxonomy ) ) {
				if ( isset( $_REQUEST['tcp_order_by'] ) ) {
					$order_type = isset( $_REQUEST['tcp_order_type'] ) ? $_REQUEST['tcp_order_type'] : 'order';
					$order_desc = isset( $_REQUEST['tcp_order_desc'] ) ? $_REQUEST['tcp_order_desc'] : 'asc';
				} else {
					$settings = get_option( 'ttc_settings' );
					$order_type = isset( $settings['order_type'] ) ? $settings['order_type'] : 'order';
					$order_desc = isset( $settings['order_desc'] ) ? $settings['order_desc'] : 'asc';
				}
				global $wpdb;
				if ( $order_type == 'title' ) {
					$order_by = $wpdb->prefix . 'posts.post_title ' . $order_desc;
				} elseif ( $order_type == 'date' ) {
					$order_by .= ', ' . $wpdb->prefix . 'posts.post_title';
				} elseif ( $order_type == 'author' ) {
					$order_by = $wpdb->prefix . 'posts.post_author ' . $order_desc . ', ' . $wpdb->prefix . 'posts.post_title';
				} elseif ( $order_type == 'comment_count' ) {
					$order_by = $wpdb->prefix . 'posts.comment_count ' . $order_desc . ', ' . $wpdb->prefix . 'posts.post_title';
				} elseif ( $order_type == 'price' || $order_type == 'order' ) {
					$order_by = 'cast(tcp_postmeta_order.meta_value as decimal) ' . $order_desc;
				} else {
					$order_by = apply_filters( 'tcp_sorting_order_by', $order_by, $order_type, $order_desc );
				}
			}
		}
		return $order_by;
	}

	function get_pagenum_link( $result ) {
		if ( isset( $_REQUEST['tcp_order_by'] ) ) {
			$order_type = isset( $_REQUEST['tcp_order_type'] ) ? $_REQUEST['tcp_order_type'] : 'order';
			$order_desc = isset( $_REQUEST['tcp_order_desc'] ) ? $_REQUEST['tcp_order_desc'] : 'asc';
			$result = add_query_arg( 'tcp_order_type', $order_type, $result );
			$result = add_query_arg( 'tcp_order_desc', $order_desc, $result );
		}
		return $result;
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

	function posts_request( $input ) {
/*echo '<br>SELECT: ', $input;
global $wpdb;
$res = $wpdb->get_results( $input );
echo '<br>RES=', count( $res ), '<br>';*/
		return $input;
	}

	function loginFormBottom( $content ) {
		return '<p class="login-lostpassword"><a href="'. wp_lostpassword_url( get_permalink() ) . '" title="' . __( 'Lost Password', 'tcp' ) . '">' . __( 'Lost Password', 'tcp' ) . '</a></p>';
	}

	function twentyten_credits() { ?>
		<a href="http://thecartpress.com/'" title="<?php esc_attr_e( 'eCommerce platform', 'tcp' ); ?>" rel="generator"><?php printf( __( 'Powered by %s.', 'tcp' ), 'TheCartPress' ); ?></a><?php
	}

	function shortCodeBuyButton( $atts ) {
		extract( shortcode_atts( array( 'post_id' => 0 ), $atts ) );
		return tcp_get_the_buy_button( $post_id );
	}

	function shortCodePrice( $atts ) {
		extract( shortcode_atts( array( 'post_id' => 0 ), $atts ) );
		return tcp_get_the_price_label( $post_id );
	}

	function wp_meta() {
		echo '<li><a href="http://thecartpress.com" title="', __( 'Powered by TheCartPress, eCommerce platform for WordPress', 'tcp' ), '">TheCartPress.com</a></li>';
	}

	function admin_footer_text( $content ) {
		$pos = strrpos( $content, '</a>.' ) + strlen( '</a>' );
		$content = substr( $content, 0, $pos ) . ' and <a href="http://thecartpress.com">TheCartPress</a>' . substr( $content, $pos );
		return $content;
	}

	function admin_bar_menu() {
		global $wp_admin_bar;
		//if ( is_admin_bar_showing() && current_user_can( 'tcp_read_orders' ) ) {
		if ( current_user_can( 'tcp_read_orders' ) ) {
			$wp_admin_bar->add_menu(
				array(
					'id'	=> 'the_cart_press',
					'title'	=> __( 'Shopping', 'tcp' ),
					'href'	=> admin_url( 'admin.php' ) . '?page=thecartpress/admin/OrdersListTable.class.php',
				)
			);
			$wp_admin_bar->add_menu(
				array(
					'parent'	=> 'the_cart_press',
					'id'		=> 'orders_list',
					'title'		=>__( 'Orders', 'tcp' ),
					'href'		=> admin_url( 'admin.php' ) . '?page=thecartpress/admin/OrdersListTable.class.php',
				)
			);
			if ( current_user_can( 'tcp_downloadable_products' ) ) {
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
	}

	function wp_before_admin_bar_render() {
		global $wp_admin_bar;
		$tcp_admin_bar_hidden_items = get_option( 'tcp_admin_bar_hidden_items', array() );
		$menu_bar = $wp_admin_bar->menu;
		foreach( $menu_bar as $id => $menu ) {
			if ( isset( $tcp_admin_bar_hidden_items[$id] ) ) {
				unset( $wp_admin_bar->menu->$id );
			} else {
				foreach( $menu as $id_menu => $menu_item ) {
					if ( $id_menu == 'children' ) {
						foreach( $menu_item as $id_item => $item ) {
							if ( isset( $tcp_admin_bar_hidden_items[$id_item] ) ) {
								unset( $menu_item->$id_item );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Runs when a user is registered (or created) before email
	 */
	function user_register( $user_id ) {
		$user = new WP_User( $user_id );
		$user->set_role( 'customer' );
	}

	function wp_dashboard_setup() {
		require_once( dirname( __FILE__ ) . '/widgets/OrdersSummaryDashboard.class.php' );
		$ordersSummaryDashboard = new OrdersSummaryDashboard();
		wp_add_dashboard_widget( 'tcp_orders_resume', __( 'Orders Summary', 'tcp' ), array( $ordersSummaryDashboard, 'show' ) );
		wp_add_dashboard_widget( 'thecartpress_rss_widget', __( 'TheCartPress blog', 'tcp' ), array( $this, 'theCartPressRSSDashboardWidget' ) );
	}

	function theCartPressRSSDashboardWidget() {
		wp_widget_rss_output( 'http://thecartpress.com/feed', array( 'items' => 5, 'show_author' => 1, 'show_date' => 1, 'show_summary' => 0 ) );
	}

	function widgets_init() {
		$disable_ecommerce = isset( $this->settings['disable_ecommerce'] ) ? $this->settings['disable_ecommerce'] : false;
		if ( ! $disable_ecommerce ) {
			register_widget( 'ShoppingCartSummaryWidget' );
			register_widget( 'ShoppingCartWidget' );
			register_widget( 'LastVisitedWidget' );
			register_widget( 'WishListWidget' );
			register_widget( 'RelatedListWidget' );
			register_widget( 'CheckoutWidget' );//TODO At this moment, only for testing purpouse
		}
		register_widget( 'CustomPostTypeListWidget' );
		register_widget( 'TaxonomyCloudsPostTypeWidget' );
		register_widget( 'TaxonomyTreesPostTypeWidget' );
		register_widget( 'SortPanelWidget' );
		register_widget( 'CommentsCustomPostTypeWidget' );
		register_widget( 'BrothersListWidget' );
		register_widget( 'TCPArchivesWidget' );
	}

	function get_base() {
		$base = dirname( __FILE__ ) . '/admin/OrdersListTable.class.php';
		return $base;
	}

	function get_base_tools() {
		$base = dirname( __FILE__ ) . '/admin/ShortCodeGenerator.php';
		return $base;
	}

	//Plugin screen
	function extra_plugin_headers( $headers ) {
		$headers['parent'] = 'Parent';
		return $headers;
	}

	function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( isset( $plugin_data['Parent'] ) && strtolower( $plugin_data['Parent'] ) == 'thecartpress' && $plugin_data['Name'] != 'TheCartPress' )
			$plugin_meta[] = __( 'Child of TheCartPress', 'tcp' );
		return $plugin_meta;
	}

	function views_plugins( $views ) {
		global $plugins;
		$children = 0;
		foreach( $plugins['all'] as $id => $plugin_data )
			if ( isset( $plugin_data['Parent'] ) && strtolower( $plugin_data['Parent'] ) == 'thecartpress' )
				$children++;
		$views['thecartpress'] = sprintf( '<a href="%s" %s>%s</a>%s',
			add_query_arg( 'plugin_status', 'child_of_thecartpress', 'plugins.php' ),
			' class="child_of_thecartpress"',
			'TheCartPress',
			$children > 0 ? " ($children)" : ''
		);
		return $views;
	}

	function plugin_action_links( $links, $file ) {
		if ( $file == 'thecartpress/TheCartPress.class.php' && function_exists( 'admin_url' ) ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page=tcp_settings_page' ). '">' . __( 'Settings', 'tcp' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	function all_plugins( $plugins ) {
		if ( isset( $_REQUEST['plugin_status'] ) && $_REQUEST['plugin_status'] == 'child_of_thecartpress' )
			foreach( $plugins as $id => $plugin_data )
				if ( ! isset( $plugin_data['Parent'] ) ) 
					unset( $plugins[$id] );
				elseif( strtolower( $plugin_data['Parent'] ) != 'thecartpress' )
					unset( $plugins[$id] );
		return $plugins;
	}

	// End Plugins Screen
	function admin_menu() {
		$base = $this->get_base();
		$disable_ecommerce = isset( $this->settings['disable_ecommerce'] ) ? $this->settings['disable_ecommerce'] : false;
		if ( ! $disable_ecommerce ) {
			add_menu_page( '', 'TheCartPress', 'tcp_read_orders', $base, '', plugins_url( '/images/tcp.png', __FILE__ ) );
			add_submenu_page( $base, __( 'Orders', 'tcp' ), __( 'Orders', 'tcp' ), 'tcp_read_orders', $base );
			add_submenu_page( $base, __( 'First time setup', 'tcp' ), __( 'First time setup', 'tcp' ), 'tcp_edit_settings', dirname( __FILE__ ) . '/admin/FirstTimeSetUp.php' );
			add_submenu_page( $base, __( 'Taxes', 'tcp' ), __( 'Taxes', 'tcp' ), 'tcp_edit_taxes', dirname( __FILE__ ) . '/admin/TaxesList.php' );
			add_submenu_page( $base, __( 'Taxes Rates', 'tcp' ), __( 'Taxes Rates', 'tcp' ), 'tcp_edit_taxes', dirname( __FILE__ ) . '/admin/TaxesRates.php' );
			add_submenu_page( $base, __( 'Payment and Shipping Methods', 'tcp' ), __( 'Payment and Shipping methods', 'tcp' ), 'tcp_edit_plugins', dirname( __FILE__ ) . '/admin/PluginsList.php' );
			add_submenu_page( $base, __( 'Notices', 'tcp' ), __( 'Notices', 'tcp' ), 'tcp_edit_orders', 'edit.php?post_type=tcp_template' );
			add_submenu_page( $base, __( 'Addresses', 'tcp' ), __( 'Addresses', 'tcp' ), 'tcp_edit_addresses', dirname( __FILE__ ) . '/admin/AddressesList.php' );
			$hide_downloadable_menu = isset( $this->settings['hide_downloadable_menu'] ) ? $this->settings['hide_downloadable_menu'] : false;
			if ( ! $hide_downloadable_menu ) add_submenu_page( $base, __( 'Downloadable Products', 'tcp' ), __( 'Downloadable Products', 'tcp' ), 'tcp_downloadable_products', dirname( __FILE__ ) . '/admin/DownloadableList.php' );
			//registered pages
			add_submenu_page( 'tcpm', __( 'Order', 'tcp' ), __( 'Order', 'tcp' ), 'tcp_edit_orders', dirname( __FILE__ ) . '/admin/OrderEdit.php' );
			add_submenu_page( 'tcpm', __( 'list of Assigned products', 'tcp' ), __( 'list of Assigned products', 'tcp' ), 'tcp_edit_product', dirname( __FILE__ ) . '/admin/AssignedProductsList.php' );
			add_submenu_page( 'tcpm', __( 'Plugin editor', 'tcp' ), __( 'Plugin editor', 'tcp' ), 'tcp_edit_plugins', dirname( __FILE__ ) . '/admin/PluginEdit.php' );
			add_submenu_page( 'tcpm', __( 'Address editor', 'tcp' ), __( 'Address editor', 'tcp' ), 'tcp_edit_addresses', dirname( __FILE__ ) . '/admin/AddressEdit.php' );
			add_submenu_page( 'tcpm', __( 'Upload files', 'tcp' ), __( 'Upload files', 'tcp' ), 'tcp_edit_product', dirname( __FILE__ ) . '/admin/UploadFiles.php' );
			add_submenu_page( 'tcpm', __( 'Files', 'tcp' ), __( 'Files', 'tcp' ), 'tcp_edit_product', dirname( __FILE__ ) . '/admin/FilesList.php' );
			add_submenu_page( 'tcpm', __( 'Downloadable products', 'tcp' ), __( 'Downloadable products', 'tcp' ), 'tcp_downloadable_products', dirname( __FILE__ ) . '/admin/VirtualProductDownloader.php' );
			add_submenu_page( 'tcpm', __( 'TheCartPress checking', 'tcp' ), __( 'TheCartPress checking', 'tcp' ), 'tcp_edit_products', dirname( __FILE__ ) . '/admin/Checking.php' );
		}
		$base = $this->get_base_tools();
		add_menu_page( '', __( 'TCPress tools', 'tcp' ), 'tcp_edit_products', $base, '', plugins_url( '/images/tcp.png', __FILE__ ) );
		add_submenu_page( $base, __( 'Shortcodes Generator', 'tcp' ), __( 'Shortcodes', 'tcp' ), 'tcp_shortcode_generator', $base );
		add_submenu_page( $base, __( 'Custom fields', 'tcp' ), __( 'Custom fields', 'tcp' ), 'tcp_edit_products', dirname( __FILE__ ) . '/admin/CustomFieldsList.php' );
		add_submenu_page( $base, __( 'Custom templates', 'tcp' ), __( 'Custom templates', 'tcp' ), 'tcp_edit_products', dirname( __FILE__ ) . '/admin/CustomTemplatesList.php' );
		if ( ! $disable_ecommerce ) {
			add_submenu_page( $base, __( 'Related Categories', 'tcp' ), __( 'Related Categories', 'tcp' ), 'tcp_edit_products', dirname( __FILE__ ) . '/admin/RelatedCats.php' );
			add_submenu_page( $base, __( 'Checkout Editor', 'tcp' ), __( 'Checkout Editor', 'tcp' ), 'tcp_checkout_editor', dirname( __FILE__ ) . '/checkout/CheckoutEditor.php' );
			add_submenu_page( $base, __( 'Update Prices', 'tcp' ), __( 'Update Prices', 'tcp' ), 'tcp_update_price', dirname( __FILE__ ) . '/admin/PriceUpdate.php' );
			add_submenu_page( $base, __( 'Update Stock', 'tcp' ), __( 'Update Stock', 'tcp' ), 'tcp_update_stock', dirname( __FILE__ ) . '/admin/StockUpdate.php' );
		}
		add_submenu_page( $base, __( 'Manage post types', 'tcp' ), __( 'Manage post types', 'tcp' ), 'manage_options', dirname( __FILE__ ) . '/admin/PostTypeList.php' );
		add_submenu_page( $base, __( 'Manage taxonomies', 'tcp' ), __( 'Manage taxonomies', 'tcp' ), 'manage_options', dirname( __FILE__ ) . '/admin/TaxonomyList.php' );
		add_submenu_page( $base, __( 'Admin Bar Config', 'tcp' ), __( 'Admin Bar Config', 'tcp' ), 'tcp_edit_products', dirname( __FILE__ ) . '/admin/AdminBarConfig.php' );
		//register pages
		add_submenu_page( 'tcp', __( 'Post Type Editor', 'tcp' ), __( 'Post Type Editor', 'tcp' ), 'manage_options', dirname( __FILE__ ) . '/admin/PostTypeEdit.php' );
		add_submenu_page( 'tcp', __( 'Taxonomy Editor', 'tcp' ), __( 'Taxonomy Editor', 'tcp' ), 'manage_options', dirname( __FILE__ ) . '/admin/TaxonomyEdit.php' );
		//add_submenu_page( 'tcpm', __( 'Print Order', 'tcp' ), __( 'Print Order', 'tcp' ), 'tcp_edit_orders', dirname( __FILE__ ) . '/admin/PrintOrder.php' );
	}

	function the_content( $content ) {
		if ( is_single() ) {
			global $post;
			if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $content;
			$html = '';
			$see_buy_button_in_content	= isset( $this->settings['see_buy_button_in_content'] ) ? $this->settings['see_buy_button_in_content'] : true;
			$align_buy_button_in_content= isset( $this->settings['align_buy_button_in_content'] ) ? $this->settings['align_buy_button_in_content'] : 'north';
			$see_price_in_content		= isset( $this->settings['see_price_in_content'] ) ? $this->settings['see_price_in_content'] : false;
			if ( ! function_exists( 'has_post_thumbnail' ) )
				$see_image_in_content = false;
			else
				$see_image_in_content = isset( $this->settings['see_image_in_content'] ) ? $this->settings['see_image_in_content'] : false;
			$html = '';
			if ( $see_buy_button_in_content ) {
				$html = tcp_the_buy_button( $post->ID, false );
			} elseif ( $see_price_in_content ) {
				$html = '<p id="tcp_price_post-' . $post->ID . '">' . tcp_get_the_price_label( $post->ID ) . '</p>';
			}
			if ( $see_image_in_content && has_post_thumbnail( $post->ID ) ) {
				$image_size			= isset( $this->settings['image_size_content'] ) ? $this->settings['image_size_content'] : 'thumbnail';
				$image_align		= isset( $this->settings['image_align_content'] ) ? $this->settings['image_align_content'] : '';
				$image_link			= isset( $this->settings['image_link_content'] ) ? $this->settings['image_link_content'] : '';
				$thumbnail_id		= get_post_thumbnail_id( $post->ID );
				$attr				= array( 'class' => $image_align . ' size-' . $image_size . ' wp-image-' . $thumbnail_id . ' tcp_single_img_featured' );
			    //$image_attributes = array{0 => url, 1 => width, 2 => height};
				$image_attributes	= wp_get_attachment_image_src( $thumbnail_id, $image_size );
				if ( strlen( $image_link ) > 0 ) {
					if ( $image_link == 'file' ) {
						$href = $image_attributes[0];
					} else {
						$href = get_permalink( $thumbnail_id );
					}
					$image	= '<a href="' . $href . '">' . get_the_post_thumbnail( $post->ID, $image_size, $attr ) . '</a>';
				} else {
					$image = get_the_post_thumbnail( $post->ID, $image_size, $attr );
				}
				$thumbnail_post = get_post( $thumbnail_id );
				if ( ! empty( $thumbnail_post->post_excerpt ) ) {
					$width = $image_attributes[1];
					$image = '[caption id="attachment_' . $thumbnail_id . '" align="' . $image_align . ' tcp_featured_single_caption" width="' . $width  . '" caption="' . $thumbnail_post->post_excerpt  . '"]' . $image . '[/caption]';
				}
				$html .= $image;
			}
			$html = apply_filters( 'tcp_filter_content', $html, $post->ID );
			if ( $align_buy_button_in_content == 'north' ) {
				return $html . do_shortcode( $content );
			} else {
				return do_shortcode( $content ) . $html;
			}
		}
		return $content;
	}

	function the_excerpt( $content ) {
		if ( ! is_single() ) {
			global $post;
			if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $content;
			$see_buy_button_in_excerpt	= isset( $this->settings['see_buy_button_in_excerpt'] ) ? $this->settings['see_buy_button_in_excerpt'] : false;
			$align_buy_button_in_excerpt= isset( $this->settings['align_buy_button_in_excerpt'] ) ? $this->settings['align_buy_button_in_excerpt'] : 'north';
			$see_price_in_excerpt		= isset( $this->settings['see_price_in_excerpt'] ) ? $this->settings['see_price_in_excerpt'] : true;
			if ( ! function_exists( 'has_post_thumbnail' ) )
				$see_image_in_excerpt = false;
			else
				$see_image_in_excerpt = isset( $this->settings['see_image_in_excerpt'] ) ? $this->settings['see_image_in_excerpt'] : false;
			$html = '';
			if ( $see_buy_button_in_excerpt ) {
				$html .= tcp_the_buy_button( $post->ID, false );
			} elseif ( $see_price_in_excerpt ) {
				$html .= '<p id="tcp_price_post-"' . $post->ID . '>' . tcp_get_the_price_label( $post->ID ) . '</p>';
			}
			if ( $see_image_in_excerpt && has_post_thumbnail( $post->ID ) ) {
				$image_size		= isset( $this->settings['image_size_excerpt'] ) ? $this->settings['image_size_excerpt'] : 'thumbnail';
				$image_align	= isset( $this->settings['image_align_excerpt'] ) ? $this->settings['image_align_excerpt'] : '';
				$image_link		= isset( $this->settings['image_link_excerpt'] ) ? $this->settings['image_link_excerpt'] : '';
				$thumbnail_id	= get_post_thumbnail_id( $post->ID );
				$attr			= array( 'class' => $image_align . ' size-' . $image_size . ' wp-image-' . $thumbnail_id . ' tcp_single_img_featured' );
			    //$image_attributes = array{0 => url, 1 => width, 2 => height};
				$image_attributes	= wp_get_attachment_image_src( $thumbnail_id, $image_size );
				if ( strlen( $image_link ) > 0 ) {
					if ( $image_link == 'file' ) {
						$href = $image_attributes[0];
					} else {
						$href = get_permalink( $thumbnail_id );
					}
					$image	= '<a href="' . $href . '">' . get_the_post_thumbnail( $post->ID, $image_size, $attr ) . '</a>';
				} else {
					$image = get_the_post_thumbnail( $post->ID, $image_size, $attr );
				}
				$thumbnail_post	= get_post( $thumbnail_id );
				if ( ! empty( $thumbnail_post->post_excerpt ) ) {
				    //$image_attributes = array{0 => url, 1 => width, 2 => height};
					$image_attributes = wp_get_attachment_image_src( $thumbnail_id, $image_size );
					$width = $image_attributes[1];
					$image = '[caption id="attachment_' . $thumbnail_id . '" align="' . $image_align . ' tcp_featured_single_caption" width="' . $width  . '" caption="' . $thumbnail_post->post_excerpt  . '"]' . $image . '[/caption]';
				}
				$html .= $image;
			}
			$html = apply_filters( 'tcp_filter_excerpt', $html, $post->ID );
			if ( $align_buy_button_in_excerpt == 'north' )
				return do_shortcode( $html . $content );
			else
				return do_shortcode( $content . $html );
		}
		return $content;
	}

	function single_template( $single_template ) {
		global $post;
		$template = tcp_get_custom_template( $post->ID );
		if ( $template ) return apply_filters( 'tcp_single_template', $template );
		$template = tcp_get_custom_template_by_post_type( $post->post_type );
		if ( $template ) return apply_filters( 'tcp_single_template', $template );
		return apply_filters( 'tcp_single_template', $single_template );
	}

	function taxonomy_template( $taxonomy_template ) {
		$term = get_queried_object();
		if ( $term ) {
			$template = tcp_get_custom_template_by_term( $term->term_id );
			if ( $template ) return apply_filters( 'tcp_taxonomy_template', $template );
		}
		global $taxonomy;
		$template = tcp_get_custom_template_by_taxonomy( $taxonomy );
		if ( $template ) return apply_filters( 'tcp_taxonomy_template', $template );
		return apply_filters( 'tcp_taxonomy_template', $taxonomy_template );
	}

	function archive_template( $archive_template ) {
		global $post_type;
		$template = tcp_get_custom_template_by_post_type( $post_type );
		if ( $template ) return apply_filters( 'tcp_archive_template', $template );
		return apply_filters( 'tcp_archive_template', $archive_template );
	}

	function loadingDefaultCheckoutBoxes() {
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPBillingExBox.class.php', 'TCPBillingExBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPBillingBox.class.php', 'TCPBillingBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPShippingBox.class.php', 'TCPShippingBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPShippingMethodsBox.class.php', 'TCPShippingMethodsBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPPaymentMethodsBox.class.php', 'TCPPaymentMethodsBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPCartBox.class.php', 'TCPCartBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPNoticeBox.class.php', 'TCPNoticeBox' );
		tcp_register_checkout_box( dirname( __FILE__ ) . '/checkout/TCPSigninBox.class.php', 'TCPSigninBox' );
	}

	function loadingDefaultCheckoutPlugins() {
		//shipping methods
		require_once( dirname( __FILE__ ) . '/plugins/FreeTrans.class.php' );
		tcp_register_shipping_plugin( 'FreeTrans' );
		require_once( dirname( __FILE__ ) . '/plugins/FlatRate.class.php' );
		tcp_register_shipping_plugin( 'FlatRateShipping' );
		require_once( dirname( __FILE__ ) . '/plugins/ShippingCost.class.php' );
		tcp_register_shipping_plugin( 'ShippingCost' );
		//payment methods
		require_once( dirname( __FILE__ ) . '/plugins/PayPal/TCPPayPal.php' );
		tcp_register_payment_plugin( 'TCPPayPal' );
		require_once( dirname( __FILE__ ) . '/plugins/Remboursement.class.php' );
		tcp_register_payment_plugin( 'TCPRemboursement' );
		require_once( dirname( __FILE__ ) . '/plugins/NoCostPayment.class.php' );
		tcp_register_payment_plugin( 'NoCostPayment' );
		require_once( dirname( __FILE__ ) . '/plugins/Transference.class.php' );
		tcp_register_payment_plugin( 'Transference' );
		require_once( dirname( __FILE__ ) . '/plugins/CardOffLine/CardOffLine.class.php' );
		tcp_register_payment_plugin( 'TCPCardOffLine' );
		require_once( dirname( __FILE__ ) . '/plugins/authorize.net/TCPAuthorizeNet.class.php' );
		tcp_register_payment_plugin( 'TCPAuthorizeNet' );
	}

	function activate_plugin() {
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
		require_once( dirname( __FILE__ ) . '/daos/TaxRates.class.php' );
		TaxRates::createTable();
		TaxRates::initData();
		require_once( dirname( __FILE__ ) . '/daos/Countries.class.php' );
		Countries::createTable();
		Countries::initData();
		require_once( dirname( __FILE__ ) . '/daos/Orders.class.php' );
		Orders::createTable();
		require_once( dirname( __FILE__ ) . '/daos/OrdersDetails.class.php' );
		OrdersDetails::createTable();
		require_once( dirname( __FILE__ ) . '/daos/OrdersCosts.class.php' );
		OrdersCosts::createTable();
		require_once( dirname( __FILE__ ) . '/daos/Currencies.class.php' );
		Currencies::createTable();
		Currencies::initData();
		//Pages: shopping cart and checkout
		$shopping_cart_page_id = get_option( 'tcp_shopping_cart_page_id' );
		if ( ! $shopping_cart_page_id || ! get_page( $shopping_cart_page_id ) )
			$shopping_cart_page_id = TheCartPress::createShoppingCartPage();
		else
			wp_publish_post( (int)$shopping_cart_page_id );
		$page_id = get_option( 'tcp_checkout_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) ) {
			TheCartPress::createCheckoutPage( $shopping_cart_page_id );
		} else {
			wp_publish_post( (int)$page_id );
		}
		//initial shipping and payment method
		add_option( 'tcp_plugins_data_shi_FreeTrans', array(
				array(
					'title'				=> __( 'Free transport', 'tcp' ),
					'active'			=> true,
					'for_downloadable'	=> true,
					'all_countries'		=> 'yes',
					'countries'			=> array(),
					'new_status'		=> 'PENDING',
					'minimun'			=> 0,
				),
			)
		);
		add_option( 'tcp_plugins_data_pay_Remboursement', array(
				array(
					'title'				=> __( 'Cash on delivery', 'tcp' ),
					'active'			=> true,
					'for_downloadable'	=> false,
					'all_countries'		=> 'yes',
					'countries'			=> array(),
					'new_status'		=> 'PROCESSING',
					'notice'			=> 'Cash on delivery! (5%)',
					'percentage'		=> 5,
				),
			)
		);
		if ( ! get_option( 'tcp_shortcodes_data' ) )
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
		if ( ! get_option( 'tcp_settings' ) ) {
			$this->settings = array(
				'legal_notice'				=> __( 'Checkout notice', 'tcp' ),
				'stock_management'			=> false,
				'disable_shopping_cart'		=> false,
				'disable_ecommerce'			=> false,
				'user_registration'			=> false,
				'see_buy_button_in_content'	=> true,
				'see_buy_button_in_excerpt'	=> false,
				'see_price_in_content'		=> false,
				'see_price_in_excerpt'		=> true,
				'downloadable_path'			=> WP_PLUGIN_DIR . '/thecartpress/uploads',
				'load_default_buy_button_style'				=> true,
				'load_default_shopping_cart_checkout_style'	=> true,
				'load_default_loop_style'					=> true,
				'search_engine_activated'	=> true,
				'emails'					=> get_option('admin_email'),
				'currency'					=> 'EUR',
				'decimal_point'				=> '.',
				'thousands_separator'		=> ',',
				'unit_weight'				=> 'gr',
				'product_rewrite'			=> 'product',
				'category_rewrite'			=> 'product_category',
				'tag_rewrite'				=> 'product_tag',
				'supplier_rewrite'			=> 'product_supplier',
				'hide_visibles'				=> false,//hide_invisibles!!
			);
			add_option( 'tcp_settings', $this->settings );
		}
		//TheCartPRess::createExampleData();
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
		$administrator->add_cap( 'tcp_checkout_editor' );
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
		$merchant->add_cap( 'tcp_checkout_editor' );
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
		//feed: http://<site>/?feed=tcp-products
		global $wp_rewrite;
		add_action( 'generate_rewrite_rules', array( $this, 'rewrite_rules_feed' ) );
		$wp_rewrite->flush_rules();
	}

	static function createShoppingCartPage() {
		$post = array(
		  'comment_status'	=> 'closed',
		  'post_content'	=> '[tcp_shopping_cart]',
		  'post_status'		=> 'publish',
		  'post_title'		=> __( 'Shopping cart','tcp' ),
		  'post_type'		=> 'page',
		);
		$shopping_cart_page_id = wp_insert_post( $post );
		update_option( 'tcp_shopping_cart_page_id', $shopping_cart_page_id );
		return $shopping_cart_page_id;
	}

	static function createCheckoutPage( $shopping_cart_page_id = 0 ) {
		$post = array(
		  'comment_status'	=> 'closed',
		  'post_content'	=> '[tcp_checkout]',
		  'post_status'		=> 'publish',
		  'post_title'		=> __( 'Checkout','tcp' ),
		  'post_type'		=> 'page',
		  'post_parent'		=> $shopping_cart_page_id,
		);
		$checkout_page_id = wp_insert_post( $post );
		update_option( 'tcp_checkout_page_id', $checkout_page_id );
		return $checkout_page_id;
	}

	static function createExampleData() {
		$products = wp_count_posts( 'tcp_product' );
		if ( $products->publish + $products->draft == 0 ) {
			$args = array(
				'cat_name'				=> __( 'Category One', 'tcp' ),
				'category_description'	=> __( 'Category One for Product One', 'tcp' ),
				'taxonomy'				=> 'tcp_product_category',
			);
			$post = array(
			  'post_content'	=> 'Product One content, where you can read the best features of the Product One.',
			  'post_status'		=> 'publish',
			  'post_title'		=> __( 'Product One','tcp' ),
			  'post_type'		=> 'tcp_product',
			);
			$post_id = wp_insert_post( $post );
			add_post_meta( $post_id, 'tcp_tax_id',  0 );
			add_post_meta( $post_id, 'tcp_tax',  0 );
			add_post_meta( $post_id, 'tcp_tax_label', '' );
			add_post_meta( $post_id, 'tcp_is_visible', true );
			add_post_meta( $post_id, 'tcp_is_downloadable', false );
			add_post_meta( $post_id, 'tcp_type', 'SIMPLE' );
			add_post_meta( $post_id, 'tcp_price', 100 );
			add_post_meta( $post_id, 'tcp_weight', 12 );
			add_post_meta( $post_id, 'tcp_order', 10 );
			add_post_meta( $post_id, 'tcp_sku', 'SKU_ONE' );
			add_post_meta( $post_id, 'tcp_stock', -1 ); //No stock
			//$category_id = wp_insert_category( $args );
			$category_id = term_exists( 'Category One', 'tcp_product_category' );
			if ( ! $category_id ) {
				$category_id = wp_insert_term( 'Category One', 'tcp_product_category', $args );
			}
			wp_set_object_terms( $post_id, (int)$category_id->term_id, 'tcp_product_category' );
		}
	}

	function deactivate_plugin() {
		//delete pages
		$id = get_option( 'tcp_shopping_cart_page_id' );
		wp_delete_post( $id );
		$id = get_option( 'tcp_checkout_page_id' );
		wp_delete_post( $id );
		//remove roles
		remove_role( 'customer' );
		remove_role( 'merchant' );
	}

	function admin_init() {
		tcp_add_template_class( 'tcp_checkout_email', __( 'This notice will be added in the email to the customer when the Checkout process ends.', 'tcp' )  );
		tcp_add_template_class( 'tcp_checkout_notice', __( 'This notice will be showed in the Checkout Notice Box into the checkout process.', 'tcp' ) );
		tcp_add_template_class( 'tcp_checkout_end', __( 'This notice will be showed if the checkout process ends right.', 'tcp' ) );
		tcp_add_template_class( 'tcp_checkout_end_ko', __( 'This notice will be showed if the checkout process ends wrong: Declined payments, etc.', 'tcp' ) );
		tcp_add_template_class( 'tcp_error_stock_when_pay', __( 'This notice will be showed when the client is going to pay and there is no stock of any product in the cart.', 'tcp') );
		$this->checkThePlugin();
	}

	function loadSettings() {
		$this->settings = get_option( 'tcp_settings' );
	}

	function init() {
		require_once( dirname( __FILE__ ) . '/templates/tcp_template.php' );
		require_once( dirname( __FILE__ ) . '/templates/tcp_general_template.php' );
		require_once( dirname( __FILE__ ) . '/templates/tcp_template_template.php' );
		require_once( dirname( __FILE__ ) . '/templates/tcp_custom_fields_template.php' );
		require_once( dirname( __FILE__ ) . '/templates/tcp_custom_templates.php' );
		require_once( dirname( __FILE__ ) . '/templates/tcp_states_template.php' );
		require_once( dirname( __FILE__ ) . '/checkout/tcp_checkout_template.php' );

		if ( function_exists( 'load_plugin_textdomain' ) ) load_plugin_textdomain( 'tcp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		wp_register_script( 'tcp_scripts', plugins_url( 'thecartpress/js/tcp_admin_scripts.js' ) );
		$this->load_custom_post_types_and_custom_taxonomies();
		if ( ! is_admin() ) wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'tcp_scripts' );
		$disable_ecommerce = isset( $this->settings['disable_ecommerce'] ) ? $this->settings['disable_ecommerce'] : false;
		if ( ! $disable_ecommerce ) {
			if ( ! session_id() ) session_start();
			$load_default_buy_button_style = isset( $this->settings['load_default_buy_button_style'] ) ? $this->settings['load_default_buy_button_style'] : true;
			if ( $load_default_buy_button_style ) wp_enqueue_style( 'tcp_buy_button_style', plugins_url( 'thecartpress/css/tcp_buy_button.css' ) );
			$load_default_shopping_cart_checkout_style = isset( $this->settings['load_default_shopping_cart_checkout_style'] ) ? $this->settings['load_default_shopping_cart_checkout_style'] : true;
			if ( $load_default_shopping_cart_checkout_style ) wp_enqueue_style( 'tcp_shopping_cart_checkout_style', plugins_url( 'thecartpress/css/tcp_shopping_cart_checkout.css' ) );
			$load_default_loop_style = isset( $this->settings['load_default_loop_style'] ) ? $this->settings['load_default_loop_style'] : true;
			if ( $load_default_loop_style ) wp_enqueue_style( 'tcp_loop_style', plugins_url( 'thecartpress/css/tcp_loop.css' ) );
			if ( is_admin() ) wp_enqueue_style( 'tcp_dashboard_style', plugins_url( 'thecartpress/css/tcp_dashboard.css' ) );
			new ProductCustomPostType();
			new TemplateCustomPostType();

			$this->loadingDefaultCheckoutBoxes();
			$this->loadingDefaultCheckoutPlugins();

			//feed: http://<site>/?feed=tcp-products
			add_feed( 'tcp-products', array( $this, 'create_products_feed' ) );
		}
		$version = (int)get_option( 'tcp_version', 0 );
		if ( $version > 0 ) {
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
			if ( $version < 105 ) {
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
			
				$this->settings['product_rewrite']	= 'product';
				$this->settings['category_rewrite']	= 'product_category';
				$this->settings['tag_rewrite']		= 'product_tag';
				$this->settings['supplier_rewrite']	= 'product_supplier';
				update_option( 'tcp_settings', $this->settings );
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
				update_option( 'tcp_version', 106 );
				//
				//TODO Deprecated 1.1
				//
			}
			if ( $version < 107 ) {
				//
				//TODO Deprecated 2.1
				//
				$this->settings['decimal_point']		= '.';
				$this->settings['thousands_separator']	= ',';
				update_option( 'tcp_settings', $this->settings );
				global $wpdb;
				$sql = 'ALTER TABLE `' . $wpdb->prefix . 'tcp_orders`
					MODIFY COLUMN `shipping_city_id`	char(4)  NOT NULL DEFAULT \'\',
					MODIFY COLUMN `shipping_region_id`	char(2)  NOT NULL DEFAULT \'\',
					MODIFY COLUMN `billing_city_id`		char(4)  NOT NULL DEFAULT \'\',
					MODIFY COLUMN `billing_region_id`	char(2)  NOT NULL DEFAULT \'\';';
				$wpdb->query( $sql );
				$sql = 'ALTER TABLE `' . $wpdb->prefix . 'tcp_addresses`
					MODIFY COLUMN `city_id`		char(4)  NOT NULL DEFAULT \'\',
					MODIFY COLUMN `region_id`	char(2)  NOT NULL DEFAULT \'\';';
				$wpdb->query( $sql );
				$sql = 'ALTER TABLE `' . $wpdb->prefix . 'tcp_orders` MODIFY COLUMN `payment_name` VARCHAR(255) NOT NULL;';
				$wpdb->query( $sql );
				require_once( dirname( __FILE__ ) . '/daos/OrdersCosts.class.php' );
				OrdersCosts::createTable();
				update_option( 'tcp_version', 107 );
				//
				//TODO Deprecated 1.1
				//
			}
			if ( $version < 108 ) {
				if ( strlen( $this->settings['downloadable_path'] ) == 0 ) $this->settings['downloadable_path'] = WP_PLUGIN_DIR . '/thecartpress/uploads';
				update_option( 'tcp_settings', $this->settings );
				update_option( 'tcp_version', 108 );
				//
				//TODO Deprecated 2.1
				//

			}
			if ( $version < 109 ) {
				global $wpdb;
				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_taxes WHERE field = \'tax\'';
				$row = $wpdb->get_row( $sql );
				if ( $row ) {
					$sql = 'DROP TABLE ' . $wpdb->prefix . 'tcp_taxes;';
					$wpdb->get_row( $sql );
					require_once( dirname( __FILE__ ) . '/daos/Taxes.class.php' );
					Taxes::createTable();
				}
				require_once( dirname( __FILE__ ) . '/daos/TaxRates.class.php' );
				TaxRates::createTable();
				TaxRates::initData();
				$sql = 'delete FROM ' . $wpdb->prefix . 'postmeta where meta_key = \'tcp_tax_label\' or meta_key = \'tcp_tax\'';
				$wpdb->query( $sql );
				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders_costs WHERE field = \'tax\'';
				$row = $wpdb->get_row( $sql );
				if ( ! $row ) {
					$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders_costs ADD COLUMN `tax` float UNSIGNED NOT NULL DEFAULT 0 AFTER `cost`';
					$wpdb->query( $sql );
				}
				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders WHERE field = \'ip\'';
				$row = $wpdb->get_row( $sql );
				if ( ! $row ) {
					$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders ADD COLUMN ip VARCHAR(20) NOT NULL AFTER customer_id;';
					$wpdb->query( $sql );
				}
				update_option( 'tcp_version', 109 );
				//
				//TODO Deprecated 2.1
				//
			}
			if ( $version < 110 ) {
				global $wpdb;
				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders WHERE field = \'transaction_id\'';
				$row = $wpdb->get_row( $sql );
				if ( ! $row ) {
					$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders ADD COLUMN `transaction_id` VARCHAR(250)  NOT NULL AFTER `payment_amount`;';
					$wpdb->query( $sql );
				}

				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_addresses WHERE field = \'custom_id\'';
				if ( ! $wpdb->get_row( $sql ) ) {
					$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_addresses ADD COLUMN `custom_id` bigint(250) unsigned NOT NULL AFTER `customer_id`;';
					$wpdb->query( $sql );
				}
				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_addresses WHERE field = \'tax_id_number\'';
				if ( ! $wpdb->get_row( $sql ) ) {
					$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_addresses ADD COLUMN `tax_id_number` bigint(250) unsigned NOT NULL AFTER `company`;';
					$wpdb->query( $sql );
				}
				$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_addresses WHERE field = \'company_id\'';
				if ( ! $wpdb->get_row( $sql ) ) {
					$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_addresses ADD COLUMN `company_id` bigint(250) unsigned NOT NULL AFTER `tax_id_number`;';
					$wpdb->query( $sql );
				}
				update_option( 'tcp_version', 110 );
				//
				//TODO Deprecated 2.1
				//
			}
		}
	}

	/**
	 * Allows to generate the xml for thecartpress search engine
	 */
	function create_products_feed() {
		require_once( dirname( __FILE__ ) . '/classes/FeedForSearchEngine.class.php' );
		$feedForSearchEngine = new FeedForSearchEngine();
		$feedForSearchEngine->generateXML();
	}

	function rewrite_rules_feed( $wp_rewrite ) {
		$new_rules = array(
			'feed/(.+)' => 'index.php?feed=' . $wp_rewrite->preg_index( 1 )
		);
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}

	//TheCartPress hooks	
	function tcp_the_currency( $currency ) {
		if ( $currency == 'EUR' )
			return '&euro;';
		elseif ( $currency == 'CHF' )
			return 'SFr.';
		elseif ( $currency == 'GBP' )
			return '&pound;';
		elseif ( $currency == 'USD' )
			return '$';
		elseif ( $currency == 'JPY' )
			return '&yen;';
		elseif ( $currency == 'IRR' )
			return 'ریال';
		else
			return $currency;
	}

	function tcp_show_settings() { ?>
		<script>
		jQuery(document).ready(function() {
			jQuery('.form-table').hide();
			jQuery('div.wrap h3').hide();
			var sections = jQuery('<ul class="tabs_section"></ul>');
			sections.insertAfter('div.wrap h2');
			var first_li = true;
			jQuery('div.wrap h3').each( function() {
				var next = jQuery(this).nextAll('.form-table');
				if (next) next = next[0];
				if (next) {
					var a = jQuery('<a href="javascript: void(0);" class="section_a">' + jQuery(this).text() + '</a>');
					a.click(function() {
						jQuery('.form-table').hide();
						jQuery(next).toggle();
						jQuery('ul.tabs_section li.section_active').removeClass('section_active');
						jQuery(this).parent().addClass('section_active');
					});
					var li = jQuery('<li></li>');
					if (first_li) {
						first_li = false;
						li.addClass('section_active');
					}
					li.append(a);
					sections.append(li);
				}
			});
			var first_section = jQuery('div.wrap h3');
			if (first_section) first_section = first_section[0];
			var next = jQuery(first_section).nextAll('.form-table');
			if (next) next = next[0];
			jQuery(next).toggle();

/*			jQuery('input').mouseover(function() {
				jQuery(this).next('div.description').find('.description').each( function() {
					jQuery(this).addClass('active_description');
				});
			});
			jQuery('input').mouseout(function() {
				jQuery(this).next('div.description').find('.description').each( function() {
					jQuery(this).removeClass('active_description');
				});
			});*/
			//var btn = jQuery('.form-table').before('<input type="button" class="button-secondary" onclick="jQuery(this).next().toggle();" value="<?php _e( 'show/hide', 'tcp');?>"/>');
		});
		</script><?php
	}

	//
	//Custom fields hooks
	//
	function tcp_product_metabox_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( tcp_is_saleable_post_type( $post_type ) ) {
			tcp_display_custom_fields( $post_id, $post_type );
		}
	}

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( tcp_is_saleable_post_type( $post_type ) ) {
			tcp_save_custom_fields( $post_id, $post_type );
		}
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( tcp_is_saleable_post_type( $post_type ) ) {
			tcp_delete_custom_fields( $post_id, $post_type );
		}
	}
	//
	// end Custom fields hooks
	//

	function load_custom_post_types_and_custom_taxonomies() {
		$post_types = get_option( 'tcp-posttypes-generator' );
		$taxonomies = get_option( 'tcp-taxonomies-generator' );
		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			foreach( $post_types as $post_type ) {
				if ( $post_type['activate'] ) {
					$register = array (
						'labels'			=> $post_type,
						'public'			=> isset( $post_type['public'] ) ? $post_type['public'] : true,
						'show_ui'			=> isset( $post_type['show_ui'] ) ? $post_type['show_ui'] : true,
						'show_in_menu'		=> isset( $post_type['show_in_menu'] ) ? $post_type['show_in_menu'] : true,
						'can_export'		=> isset( $post_type['can_export'] ) ? $post_type['can_export'] : true,
						'show_in_nav_menus'	=> isset( $post_type['show_in_nav_menus'] ) ? $post_type['show_in_nav_menus'] : true,
						'_builtin'			=> false,
						'_edit_link'		=> 'post.php?post=%d',
						'capability_type'	=> 'post',
						'hierarchical'		=> false, //allways false
						'query_var'			=> true,
						'supports'			=> isset( $post_type['supports'] ) ? $post_type['supports'] : true,
						'taxonomies'		=> $this->tcp_get_custom_taxonomies_by_post_type( $post_type['name_id'], $taxonomies ),
						'rewrite'			=> strlen( $post_type['rewrite'] ) > 0 ? array( 'slug' => $post_type['rewrite'] ) : false,
						'has_archive'		=> strlen( $post_type['has_archive'] ) > 0 ? $post_type['has_archive'] : false,
					);
					register_post_type( $post_type['name_id'], $register );
					$is_saleable = isset( $post_type['is_saleable'] ) ? $post_type['is_saleable'] : false;
					if ( $is_saleable ) $this->saleable_post_types[] = $post_type['name_id'];
					if ( $register['has_archive'] ) ProductCustomPostType::register_post_type_archives( $post_type['name_id'], $register['has_archive'] );//TODO
				}
			}
		}
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
			foreach( $taxonomies as $taxonomy ) {
				if ( $taxonomy['activate'] ) {
					$register = array (
						'labels'		=> $taxonomy,
						'hierarchical'	=> $taxonomy['hierarchical'],
						'query_var'		=> true,
						'rewrite'		=> strlen( $taxonomy['rewrite'] ) > 0 ? $taxonomy['rewrite'] : false,
					);
					register_taxonomy( $taxonomy['name_id'], $taxonomy['post_type'], $register );
				}
			}
		}
	}

	function tcp_get_saleable_post_types( $saleable_post_types ) {
		return array_merge( $saleable_post_types, $this->saleable_post_types );
	}

	function tcp_get_custom_taxonomies_by_post_type( $post_type, $taxonomies ) {
		$result = array();
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
			foreach( $taxonomies as $taxonomy ) {
				if ( $taxonomy['activate'] && $taxonomy['post_type'] == $post_type ) {
					$result[] = $taxonomy['name_id'];
				}
			}
		}
		return $result;
	}

	function screen_settings( $current, $screen ) {
		$features = '<a href="http://thecartpress.com/features/wordpress-ecommerce-features/" target="_blank">' . __( 'TheCartPress features' , 'tcp' ) .'</a>';
		$support = '<a href="http://community.thecartpress.com/forums/" target="_blank">' . __( 'Support from TheCartPress community' , 'tcp' ) .'</a>';
		if ( 'thecartpress/admin/TaxesRates' == $screen->id || 'thecartpress/admin/TaxesList' == $screen->id ) {
			$text = $features . '<br/><a href="http://thecartpress.com/docs/working-with-the-thecartpress/tax-rates/" target="_blank">' . __( 'Help from TheCartPress website' , 'tcp' ) .'</a><br/>';
			$text .= '<a href="http://thecartpress.com/docs/working-with-the-thecartpress/settings-for-tax/" target="_blank">' . __( 'Settings for Taxes' , 'tcp' ) .'</a><br/>' . $support;
		} elseif ( 'thecartpress_page_tcp_settings_page' == $screen->id ) {
			$text = $features . '<br/><a href="http://thecartpress.com/docs/working-with-the-thecartpress/settings-for-countries/" target="_blank">' . __( 'Settings for Countries' , 'tcp' ) .'</a><br/>';
			$text .= '<a href="http://thecartpress.com/docs/working-with-the-thecartpress/settings-for-tax/" target="_blank">' . __( 'Settings for Taxes' , 'tcp' ) .'</a><br/>' . $support;
		} elseif ( strrpos( $screen->id, 'thecartpress') !== false || strrpos( $screen->id, 'tcp_') !== false ) {
			$text = $features . '<br/>' . $support;
		}
		if ( isset( $text ) ) add_contextual_help( $screen->id, $text );
		if ( WP_DEBUG ) $current .= $screen->id;
		return $current;
	}
}

$thecartpress = new TheCartPress();
?>
