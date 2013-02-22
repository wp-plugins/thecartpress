<?php
/*
Plugin Name: TheCartPress
Plugin URI: http://thecartpress.com
Description: TheCartPress (Multi language support)
Version: 1.2.7.1
Author: TheCartPress team
Author URI: http://thecartpress.com
License: GPL
Parent: thecartpress
*/

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

define ( 'DONOTCACHEPAGE', 'TCP' ); //WPSuperCache

define( 'TCP_FOLDER'			, dirname( __FILE__ ) . '/' );
define( 'TCP_ADMIN_FOLDER'		, TCP_FOLDER . 'admin/' );
define( 'TCP_APPEARANCE_FOLDER'	, TCP_FOLDER . 'appearance/' );
define( 'TCP_SETTINGS_FOLDER'	, TCP_FOLDER . 'settings/' );
define( 'TCP_CLASSES_FOLDER'	, TCP_FOLDER . 'classes/' );
define( 'TCP_TEMPLATES_FOLDER'	, TCP_FOLDER . 'templates/' );
define( 'TCP_DAOS_FOLDER'		, TCP_FOLDER . 'daos/' );
define( 'TCP_WIDGETS_FOLDER'	, TCP_FOLDER . 'widgets/' );
define( 'TCP_METABOXES_FOLDER'	, TCP_FOLDER . 'metaboxes/' );
define( 'TCP_SHORTCODES_FOLDER'	, TCP_FOLDER . 'shortcodes/' );
define( 'TCP_PLUGINS_FOLDER'	, TCP_FOLDER . 'plugins/' );
define( 'TCP_CHECKOUT_FOLDER'	, TCP_FOLDER . 'checkout/' );
define( 'TCP_MODULES_FOLDER'	, TCP_FOLDER . 'modules/' );
define( 'TCP_CUSTOM_POST_TYPE_FOLDER', TCP_FOLDER . 'customposttypes/' );
define( 'TCP_THEMES_TEMPLATES_FOLDER', TCP_FOLDER . 'themes-templates/' );

define( 'TCP_ADMIN_PATH', 'admin.php?page=' . plugin_basename( TCP_FOLDER ) . '/admin/' );

class TheCartPress {

	public static $tcp_shoppingCart = false;
	public $settings = array();

	private $saleable_post_types = array();

	function __construct() {
		require_once( TCP_TEMPLATES_FOLDER . 'manage_templates.php' );
		$this->load_settings();
		require_once( TCP_MODULES_FOLDER . 'manage_modules.php' );
		require_once( TCP_CLASSES_FOLDER . 'ShoppingCart.class.php' );
		require_once( TCP_CLASSES_FOLDER . 'TCP_Plugin.class.php' );
		require_once( TCP_CHECKOUT_FOLDER . 'tcp_checkout_template.php' );
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'last_init' ), 999 );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'after_setup_theme', array( &$this, 'after_setup_theme' ), 11 );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'shutdown', array( &$this, 'shutdown' ) );
		require_once( TCP_SHORTCODES_FOLDER . 'manage_shortcodes.php' );
		require_once( TCP_WIDGETS_FOLDER . 'manage_widgets.php' );
		require_once( TCP_CLASSES_FOLDER . 'DownloadableProducts.class.php' );
		require_once( TCP_CLASSES_FOLDER . 'CountrySelection.class.php' );
		$this->loading_default_checkout_plugins();
		register_activation_hook( __FILE__, array( &$this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate_plugin' ) );
	}

	function init() {
		if ( ! session_id() ) session_start();
		require_once( TCP_SETTINGS_FOLDER . 'manage_settings.php' );
		require_once( TCP_APPEARANCE_FOLDER . 'manage_appearance.php' );
		require_once( TCP_METABOXES_FOLDER . 'manage_metaboxes.php' );
		if ( function_exists( 'load_plugin_textdomain' ) ) load_plugin_textdomain( 'tcp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		wp_register_script( 'tcp_scripts', plugins_url( 'thecartpress/js/tcp_admin_scripts.js' ) );
		wp_enqueue_script( 'jquery' );
		$this->load_custom_post_types_and_custom_taxonomies();
		wp_enqueue_style( 'tcp_default_style', plugins_url( 'thecartpress/css/tcp_default.css' ) );
		$disable_ecommerce = $this->get_setting( 'disable_ecommerce', false );
		if ( ! $disable_ecommerce ) {
			if ( $this->get_setting( 'load_default_buy_button_style', true ) ) wp_enqueue_style( 'tcp_buy_button_style', plugins_url( 'thecartpress/css/tcp_buy_button.css' ) );
			if ( $this->get_setting( 'load_default_shopping_cart_checkout_style', true ) ) wp_enqueue_style( 'tcp_shopping_cart_checkout_style', plugins_url( 'thecartpress/css/tcp_shopping_cart_checkout.css' ) );
			if ( $this->get_setting( 'load_default_loop_style', true ) ) wp_enqueue_style( 'tcp_loop_style', plugins_url( 'thecartpress/css/tcp_loop.css' ) );
			if ( $this->get_setting( 'responsive_featured_thumbnails', true ) ) wp_enqueue_style( 'tcp_responsive_style', plugins_url( 'thecartpress/css/tcp_responsive.css' ) );
			new TemplateCustomPostType();
			$this->loading_default_checkout_boxes();
			//feed: http://<site>/?feed=tcp-products
			add_filter( 'the_content', array( &$this, 'the_content' ) );
			add_filter( 'the_content', array( &$this, 'the_excerpt' ) );
			add_action( 'pre_get_posts', array( &$this, 'pre_get_posts' ) );
			add_filter( 'get_pagenum_link', array( &$this, 'get_pagenum_link' ) );
			add_filter( 'tcp_get_saleable_post_types', array( &$this, 'tcp_get_saleable_post_types' ) );
			//add_action( 'user_register', array( $this, 'user_register' ) );
			require_once( TCP_CHECKOUT_FOLDER	. 'ActiveCheckout.class.php' );
			require_once( TCP_ADMIN_FOLDER . 'PrintOrder.class.php' );
		}
		$this->update_version();
	}

	function last_init() {
		$this->check_for_shopping_cart_actions();
	}

	function admin_init() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'tcp_scripts' );
		wp_enqueue_style( 'tcp_dashboard_style', plugins_url( 'thecartpress/css/tcp_dashboard.css' ) );
		$disable_ecommerce = $this->get_setting( 'disable_ecommerce', false );
		if ( ! $disable_ecommerce ) {
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) ); //to check the plugin
			//add_action( 'wp_ajax_tcp_checkout_steps_save', array( &$this, 'tcp_checkout_steps_save' ) );
			//default notices
			tcp_add_template_class( 'tcp_checkout_email', __( 'This notice will be added in the email to the customer when the Checkout process ends', 'tcp' )  );
			tcp_add_template_class( 'tcp_checkout_notice', __( 'This notice will be showed in the Checkout Notice Box into the checkout process', 'tcp' ) );
			tcp_add_template_class( 'tcp_checkout_end', __( 'This notice will be showed if the checkout process ends right', 'tcp' ) );
			tcp_add_template_class( 'tcp_checkout_end_ko', __( 'This notice will be showed if the checkout process ends wrong: Declined payments, etc.', 'tcp' ) );
			tcp_add_template_class( 'tcp_shopping_cart_empty', __( 'This notice will be showed at the Shopping Cart or Checkout page, if the Shopping Cart is empty', 'tcp' ) );
			tcp_add_template_class( 'tcp_checkout_order_cart', __( 'This notice will be showed at the Checkout Resume Cart', 'tcp' ) );
			tcp_add_template_class( 'tcp_checkout_billing_notice', __( 'This notice will be showed at Billing address in Checkout', 'tcp' ) );
			add_filter( 'tcp_get_default_roles', array( &$this, 'tcp_get_default_roles' ) );
		}
	}

	function update_version() {
		require_once( TCP_CLASSES_FOLDER . 'UpdateVersion.class.php' );
		$updateVersion = new TCPUpdateVersion();
		$updateVersion->update( $this );
	}

	function check_for_shopping_cart_actions() {
		if ( isset( $_REQUEST['tcp_add_to_shopping_cart'] ) ) {
			unset( $_REQUEST['tcp_add_to_shopping_cart'] );
			if ( ! isset( $_REQUEST['tcp_post_id'] ) ) return;
			$shoppingCart = TheCartPress::getShoppingCart();
			do_action( 'tcp_before_add_shopping_cart', $_REQUEST['tcp_post_id'] );
			for( $i = 0; $i < count( $_REQUEST['tcp_post_id'] ); $i++ ) {
				$count = isset( $_REQUEST['tcp_count'][$i] ) ? (int)$_REQUEST['tcp_count'][$i] : 0;
				if ( $count > 0 ) {
					$post_id = isset( $_REQUEST['tcp_post_id'][$i] ) ? $_REQUEST['tcp_post_id'][$i] : 0;
					$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
					$tcp_option_id = isset( $_REQUEST['tcp_option_id'][$i] ) ? $_REQUEST['tcp_option_id'][$i] : 0;
					if ( $tcp_option_id > 0 ) {
						$option_ids = explode( '-',  $tcp_option_id);
						if ( count( $option_ids ) == 2 ) {
							$option_1_id	= $option_ids[0];
							$price_1		= tcp_get_the_price( $option_1_id );
							$weight_1		= tcp_get_the_weight( $option_1_id );
							$option_2_id	= $option_ids[1];
							$price_2		= tcp_get_the_price( $option_2_id );
							$weight_2		= tcp_get_the_weight( $option_2_id );
						} else {
							$option_1_id	= $tcp_option_id;
							$price_1		= tcp_get_the_price( $option_1_id );
							$weight_1		= tcp_get_the_weight( $option_1_id );
							$option_2_id	= '0';
							$price_2		= 0;
							$weight_2		= 0;
						}
					} else {
						$option_1_id = isset( $_REQUEST['tcp_option_1_id'][$i] ) ? $_REQUEST['tcp_option_1_id'][$i] : 0;
						if ( $option_1_id > 0 ) {
							$price_1		= tcp_get_the_price( $option_1_id );
							$weight_1		= tcp_get_the_weight( $option_1_id );
							$option_2_id	= isset( $_REQUEST['tcp_option_2_id'][$i] ) ? $_REQUEST['tcp_option_2_id'][$i] : 0;
							if ( $option_2_id > 0 ) {
								$price_2	= tcp_get_the_price( $option_2_id );
								$weight_2	= tcp_get_the_weight( $option_2_id );
							} else {
								$price_2	= 0;
								$weight_2	= 0;
							}
						} else {
							$price_1	= 0;
							$weight_1	= 0;
							$option_2_id = 0;
							$price_2	= 0;
							$weight_2	= 0;
						}
					}
					$unit_price	= tcp_get_the_price( $post_id );
					$unit_price	+= $price_1 + $price_2;
					$unit_price	= apply_filters( 'tcp_price_to_add_to_shoppingcart', $unit_price, $post_id );
					if ( $weight_2 > 0 ) $unit_weight = $weight_2;
					elseif ( $weight_1 > 0 ) $unit_weight = $weight_1;
					else $unit_weight = tcp_get_the_weight( $post_id );
					////$unit_weight	= tcp_get_the_weight( $post_id ) + $weight_1 + $weight_2;
					$args = compact( 'i', 'post_id', 'count', 'unit_price', 'unit_weight' );
					$args = apply_filters( 'tcp_add_item_shopping_cart', $args );
					extract( $args );
					$item = $shoppingCart->add( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $unit_weight );
				}
			}
			do_action( 'tcp_add_shopping_cart', $_REQUEST['tcp_post_id'] );
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
				$count			= isset( $_REQUEST['tcp_count'] ) ? (int)$_REQUEST['tcp_count'] : 0;
				$shoppingCart	= TheCartPress::getShoppingCart();
				$shoppingCart->modify( $post_id, $option_1_id, $option_2_id, $count );
				do_action( 'tcp_modify_shopping_cart', $post_id );
			}
		}
	}

	static function getShoppingCart() {
		if ( ! session_id() ) session_start();
		if ( TheCartPress::$tcp_shoppingCart !== false ) {
			if ( isset( $_SESSION['tcp_session_refresh'] ) ) {
				TheCartPress::$tcp_shoppingCart->refresh();
				unset( $_SESSION['tcp_session_refresh'] );
			}
			return TheCartPress::$tcp_shoppingCart;
		}
		
		if ( isset( $_SESSION['tcp_session'] ) ) {
			if ( is_string( $_SESSION['tcp_session'] ) ) TheCartPress::$tcp_shoppingCart = unserialize( $_SESSION['tcp_session'] );
			else TheCartPress::$tcp_shoppingCart = $_SESSION['tcp_session'];
		}
		if ( TheCartPress::$tcp_shoppingCart === false ) {
			TheCartPress::$tcp_shoppingCart = new ShoppingCart();
			$_SESSION['tcp_session'] = serialize( TheCartPress::$tcp_shoppingCart );
		}
		if ( isset( $_SESSION['tcp_session_refresh'] ) ) {
			TheCartPress::$tcp_shoppingCart->refresh();
			unset( $_SESSION['tcp_session_refresh'] );
		}
		return TheCartPress::$tcp_shoppingCart;
	}

	static function removeShoppingCart() {
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( $shoppingCart ) $shoppingCart->deleteAll();
	}

	static function saveShoppingCart() {
		if ( TheCartPress::$tcp_shoppingCart !== false ) {
			$_SESSION['tcp_session'] = serialize( TheCartPress::$tcp_shoppingCart );
		}
	}

	static function refreshShoppingCart( $refresh = true ) {
		if ( ! session_id() ) session_start();
		if ( $refresh ) $_SESSION['tcp_session_refresh'] = $refresh;
		else unset( $_SESSION['tcp_session_refresh'] );
	}	

	function shutdown() {
		TheCartPress::saveShoppingCart();
	}

	/**
	 * Returns the value of the setting indexed by $setting_name
	 * @param string $setting_name
	 * @param mixed $default_value
	 * @return the setting indexed by $setting_name, if not value found, $default_value will be returned
	 * @since 1.1.6
	 */
	public function get_setting( $setting_name, $default_value = false ) {
		$post_type = get_post_type();
		if ( $post_type ) {
			$setting_name_by_post_type = $setting_name . '-'. $post_type;
			if ( isset( $this->settings[$setting_name_by_post_type] ) ) {
				$value = $this->settings[$setting_name_by_post_type];
				return apply_filters( 'tcp_get_setting', $value, $setting_name_by_post_type, $default_value );	
			}
		}
		$value = isset( $this->settings[$setting_name] ) ? $this->settings[$setting_name] : $default_value;
		return apply_filters( 'tcp_get_setting', $value, $setting_name, $default_value );
	}

	/**
	 * Checks the plugin
	 */
	function admin_notices() {
		$warnings = array();
		$page_id = get_option( 'tcp_shopping_cart_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) )
			$warnings[] = __( 'The <strong>Shopping Cart page</strong> has been deleted.', 'tcp' );
		$page_id = get_option( 'tcp_checkout_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) )
			$warnings[] = __( 'The <strong>Checkout page</strong> has been deleted.', 'tcp' );
		$page_id = get_option( 'tcp_my_account_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) )
			$warnings[] = __( 'My Account page has been deleted', 'tcp' );
		$warnings = apply_filters( 'tcp_check_the_plugin', $warnings );
		if ( count( $warnings ) > 0 ) : 
			$checking_path = TCP_ADMIN_PATH . 'Checking.php'; ?>
		<div id="message_checking_error" class="error"><p>
			<?php _e( 'Notice:', 'tcp' ); ?><br />
			<?php foreach( $warnings as $warning ) : ?>
				<?php echo $warning; ?><br/>
			<?php endforeach; ?></p>
			<p><?php printf( __( 'Visit the <a href="%s">Checking page</a> to fix those warnings.', 'tcp' ), $checking_path ); ?></p>
		</div>
		<?php endif;
	}

	function pre_get_posts( $query ) {
		if ( is_admin() ) return;
		$apply_filters = false;
		/*if ( $query->is_home() && $query->is_main_query() ) {
			global $thecartpress;
			$activate_frontpage = $thecartpress->get_setting( 'activate_frontpage' );
			if ( ! $activate_frontpage ) return ;
			$taxonomy_term = $thecartpress->get_setting( 'taxonomy_term', '' );
			if ( strlen( $taxonomy_term ) > 0 ) {
				$taxonomy_term = explode( ':', $taxonomy_term );
				$query->set( $taxonomy_term[0], $taxonomy_term[1] );
			}
			$apply_filters = true;
		}*/

		if ( $query->is_author ) {
			$post_types = get_post_types( array( 'public' => true ) );
			unset($post_types['attachment']);
			unset($post_types['page']);
			$query->set( 'post_type', $post_types );
		}


		if ( ! $apply_filters && isset( $query->tax_query ) ) {
			foreach ( $query->tax_query->queries as $tax_query ) { //@See Query.php: 1530
				if ( tcp_is_saleable_taxonomy( $tax_query['taxonomy'] ) ) {
					$apply_filters = true;
					break;
				}
			}
		}
		if ( ! $apply_filters && tcp_is_saleable_post_type( $query->get( 'post_type' ) ) ) {
			$apply_filters = true;
		}
		if ( $apply_filters ) {
			//TODO filter by custom field
			$meta_query = $query->get( 'meta_query' );
			$meta_query[] = array(
				'key'		=> 'tcp_is_visible',
				'value'		=> 1,
				'type'		=> 'NUMERIC',
				'compare'	=> '='
			);
			$query->set( 'meta_query', $meta_query );
			global $wp_the_query;
			if ( $query == $wp_the_query ) {
				$filter = new TCPFilterNavigation();
				if ( $filter->is_filter_by_layered() ) {
					$layered = $filter->get_layered();
					foreach( $layered as $tax => $layers ) {
						$query->set( $tax, '' );
						foreach( $layers as $layer ) {
							if ( $layer['type'] == 'taxonomy' ) {
								$query->set( $tax, get_query_var( $tax ) . $layer['term'] . ',' );
							} elseif ( $layer['type'] == 'dynamic_options' ) {
								$query->set( 'post__in', $layer['post__in'] );
							} else { //custom_field_def
								$meta_query = $query->get( 'meta_query' );
								$meta_query[] = array(
									'key' => $tax,
									'value' => $layer['value'],
									'compare' => '=',
								);
								$query->set( 'meta_query', $meta_query );
							}
						}
					}
				}
				if ( $filter->is_filter_by_price_range() ) {
					$meta_query = $wp_query->get( 'meta_query' );
					$meta_query[] = array(
						'key'		=> 'tcp_price',
						'value'		=> array( $filter->get_min_price(), $filter->get_max_price() ),
						'type'		=> 'NUMERIC',
						'compare'	=> 'BETWEEN'
					);
					$query->set( 'meta_query', $meta_query );
				}
				$query->set( 'posts_per_page', (int)$this->get_setting( 'products_per_page', 10 ) );
				if ( $filter->get_order_type() == 'price' ) {
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'tcp_price' );
				} elseif ( $filter->get_order_type() == 'order' ) {
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'tcp_order' );
				} else {
					$query->set( 'orderby', $filter->get_order_type() );
				}
				$query->set( 'order', $filter->get_order_desc() );
				$query = apply_filters( 'tcp_sort_main_loop', $query, $filter->get_order_type(), $filter->get_order_desc() );
			}
			$query = apply_filters( 'tcp_apply_filters_for_saleables', $query );
		}
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

	/*function user_register( $user_id ) {
		$user = new WP_User( $user_id );
		$user->set_role( 'customer' );
	}*/

	function get_base() {
		$base = TCP_ADMIN_FOLDER . 'OrdersListTable.php';
		//$base = 'edit.php?post_type=tcp_orders';
		return $base;
	}

	function get_base_tools() {
		$base = TCP_ADMIN_FOLDER . 'ShortCodeGenerator.php';
		return $base;
	}

	function get_base_settings() {
		return __FILE__;
	}

	function get_base_appearance() {
		return __FILE__ . '/appearance';
	}

	function admin_menu() {
		$base = $this->get_base();
		$disable_ecommerce = $this->get_setting( 'disable_ecommerce' );
		if ( ! $disable_ecommerce ) {
			add_menu_page( '', 'theCartPress', 'tcp_read_orders', $base, '', plugins_url( '/images/tcp.png', __FILE__ ), 40 );
			add_submenu_page( $base, __( 'Orders', 'tcp' ), __( 'Orders', 'tcp' ), 'tcp_read_orders', $base );
			if ( ! $this->get_setting( 'hide_downloadable_menu' ) ) add_submenu_page( $base, __( 'My Downloads', 'tcp' ), __( 'My Downloads', 'tcp' ), 'tcp_downloadable_products', TCP_ADMIN_FOLDER . 'DownloadableList.php' );
			add_submenu_page( $base, __( 'Addresses', 'tcp' ), __( 'Addresses', 'tcp' ), 'tcp_edit_address', TCP_ADMIN_FOLDER . 'AddressesList.php' );
			add_submenu_page( $base, __( 'Taxes', 'tcp' ), __( 'Taxes', 'tcp' ), 'tcp_edit_taxes', TCP_ADMIN_FOLDER . 'TaxesList.php' );
			add_submenu_page( $base, __( 'Related Categories', 'tcp' ), __( 'Related Categories', 'tcp' ), 'tcp_edit_products', TCP_ADMIN_FOLDER . 'RelatedCats.php' );
			//add_submenu_page( $base, __( 'Update Prices', 'tcp' ), __( 'Update Prices', 'tcp' ), 'tcp_update_price', TCP_ADMIN_FOLDER . 'PriceUpdate.php' );
			add_submenu_page( 'tcpml', __( 'Order', 'tcp' ), __( 'Order', 'tcp' ), 'tcp_edit_orders', TCP_ADMIN_FOLDER . 'OrderEdit.php' );
			add_submenu_page( 'tcpml', __( 'list of Assigned products', 'tcp' ), __( 'list of Assigned products', 'tcp' ), 'tcp_edit_product', TCP_ADMIN_FOLDER . 'AssignedProductsList.php' );
			add_submenu_page( 'tcpml', __( 'list of Assigned categories', 'tcp' ), __( 'list of Assigned categories', 'tcp' ), 'tcp_edit_product', TCP_ADMIN_FOLDER . 'AssignedCategoriesList.php' );
			add_submenu_page( 'tcpml', __( 'Plugin editor', 'tcp' ), __( 'Plugin editor', 'tcp' ), 'tcp_edit_plugins', TCP_ADMIN_FOLDER . 'PluginEdit.php' );
			add_submenu_page( 'tcpml', __( 'Address editor', 'tcp' ), __( 'Address editor', 'tcp' ), 'tcp_edit_address', TCP_ADMIN_FOLDER . 'AddressEdit.php' );
			add_submenu_page( 'tcpml', __( 'Upload files', 'tcp' ), __( 'Upload files', 'tcp' ), 'tcp_edit_product', TCP_ADMIN_FOLDER . 'UploadFiles.php' );
			add_submenu_page( 'tcpml', __( 'Files', 'tcp' ), __( 'Files', 'tcp' ), 'tcp_edit_product', TCP_ADMIN_FOLDER . 'FilesList.php' );
			add_submenu_page( 'tcpml', __( 'Downloadable products', 'tcp' ), __( 'Downloadable products', 'tcp' ), 'tcp_downloadable_products', TCP_ADMIN_FOLDER . 'VirtualProductDownloader.php' );
			add_submenu_page( 'tcpml', __( 'TheCartPress checking', 'tcp' ), __( 'TheCartPress checking', 'tcp' ), 'tcp_edit_products', TCP_ADMIN_FOLDER . 'Checking.php' );
		}
		$base = $this->get_base_tools();
		add_menu_page( '', __( 'TCP Tools', 'tcp' ), 'tcp_edit_products', $base, '', plugins_url( '/images/tcp.png', __FILE__ ), 43 );
		add_submenu_page( $base, __( 'Shortcodes Generator', 'tcp' ), __( 'Shortcodes', 'tcp' ), 'tcp_shortcode_generator', $base );
		add_submenu_page( $base, __( 'Manage post types', 'tcp' ), __( 'Manage post types', 'tcp' ), 'manage_options', TCP_ADMIN_FOLDER . 'PostTypeList.php' );
		add_submenu_page( $base, __( 'Manage taxonomies', 'tcp' ), __( 'Manage taxonomies', 'tcp' ), 'manage_options', TCP_ADMIN_FOLDER . 'TaxonomyList.php' );
		//add_submenu_page( $base, __( 'Admin Bar Config', 'tcp' ), __( 'Admin Bar Config', 'tcp' ), 'tcp_edit_products', TCP_ADMIN_FOLDER . 'AdminBarConfig.php' );
		add_submenu_page( 'tcpml', __( 'Post Type Editor', 'tcp' ), __( 'Post Type Editor', 'tcp' ), 'manage_options', TCP_ADMIN_FOLDER . 'PostTypeEdit.php' );
		add_submenu_page( 'tcpml', __( 'Taxonomy Editor', 'tcp' ), __( 'Taxonomy Editor', 'tcp' ), 'manage_options', TCP_ADMIN_FOLDER . 'TaxonomyEdit.php' );
	}

	function the_content( $content ) {
		if ( is_single() ) {
			global $post;
			if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $content;
			$suffix = '-' . $post->post_type;
			if ( $this->get_setting( 'align_buy_button_in_content' . $suffix, false ) === false ) $suffix = '';
			$see_buy_button_in_content	= $this->get_setting( 'see_buy_button_in_content' . $suffix, true );
			$align_buy_button_in_content= $this->get_setting( 'align_buy_button_in_content' . $suffix, 'north' );
			$see_price_in_content		= $this->get_setting( 'see_price_in_content' );
			if ( ! function_exists( 'has_post_thumbnail' ) ) $see_image_in_content = false;
			else $see_image_in_content	= $this->get_setting( 'see_image_in_content' );
			$html = '';
			if ( $see_buy_button_in_content ) {
				$html = tcp_the_buy_button( $post->ID, false );
			} elseif ( $see_price_in_content ) {
				$html = '<p id="tcp_price_post-' . $post->ID . '">' . tcp_get_the_price_label( $post->ID ) . '</p>';
			}
			if ( $see_image_in_content ) {
				$image_align = $this->get_setting( 'image_align_content', '' );
				$args = array(
					'size'	=> $this->get_setting( 'image_size_content', 'thumbnail' ),
					'align'	=> $image_align,
					'link'	=> $this->get_setting( 'image_link_content', 'permalink' ),
				);
				$args = apply_filters( 'tcp_get_image_in_content_args', $args, $post->ID );
				$image = tcp_get_the_thumbnail_with_permalink( $post->ID, $args, false );
				$image = apply_filters( 'tcp_get_image_in_content', $image, $post->ID, $args );
				/*$thumbnail_post = get_post( $thumbnail_id );
				if ( ! empty( $thumbnail_post->post_excerpt ) ) {
					$image_attributes = wp_get_attachment_image_src( $thumbnail_id, $args['size'] );
					$width = $image_attributes[1];
					$image = '[caption id="attachment_' . $thumbnail_id . '" align="' . $image_align . ' tcp_featured_single_caption" width="' . $width  . '" caption="' . $thumbnail_post->post_excerpt  . '"]' . $image . '[/caption]';
				}*/
				$content = $image . $content;
			}
			$html = apply_filters( 'tcp_filter_content', $html, $post->ID );
			if ( $align_buy_button_in_content == 'north' ) {
				return $html . do_shortcode( $content );
			} elseif ( $align_buy_button_in_content == 'south' ) {
				return do_shortcode( $content ) . $html;
			} else {
				return $html . do_shortcode( $content ) . $html;
			}
		}
		return $content;
	}

	function the_excerpt( $content ) {
		if ( ! is_single() ) {
			$use_default_loop = $this->get_setting( 'use_default_loop', 'only_settings' );
			if ( $use_default_loop != 'none' ) return $content;
			global $post;
			if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $content;
			$see_buy_button_in_excerpt	= $this->get_setting( 'see_buy_button_in_excerpt' );
			$align_buy_button_in_excerpt= $this->get_setting( 'align_buy_button_in_excerpt', 'north' );
			$see_price_in_excerpt		= $this->get_setting( 'see_price_in_excerpt', true );
			if ( ! function_exists( 'has_post_thumbnail' ) ) $see_image_in_excerpt = false;
			else $see_image_in_excerpt = $this->get_setting( 'see_image_in_excerpt' );
			$html = '';
			if ( $see_buy_button_in_excerpt ) {
				$html .= tcp_the_buy_button( $post->ID, false );
			} elseif ( $see_price_in_excerpt ) {
				$html .= '<p id="tcp_price_post-' . $post->ID . '">' . tcp_get_the_price_label( $post->ID ) . '</p>';
			}
			if ( $see_image_in_excerpt && has_post_thumbnail( $post->ID ) ) {
				$image_size		= $this->get_setting( 'image_size_excerpt', 'thumbnail' );
				$image_align	= $this->get_setting( 'image_align_excerpt', '' );
				$image_link		= $this->get_setting( 'image_link_excerpt', '' );
				$thumbnail_id	= get_post_thumbnail_id( $post->ID );
				$attr			= array( 'class' => $image_align . ' size-' . $image_size . ' wp-image-' . $thumbnail_id . ' tcp_single_img_featured tcp_thumbnail_' . $post->ID );
			    //$image_attributes = array{0 => url, 1 => width, 2 => height};
				$image_attributes = wp_get_attachment_image_src( $thumbnail_id, $image_size );
				if ( strlen( $image_link ) > 0 ) {
					if ( $image_link == 'file' ) $href = $image_attributes[0];
					else $href = get_permalink( $thumbnail_id );
					$image	= '<a href="' . $href . '">' . get_the_post_thumbnail( $post->ID, $image_size, $attr ) . '</a>';
				} else {
					$image = get_the_post_thumbnail( $post->ID, $image_size, $attr );
				}
				$thumbnail_post	= get_post( $thumbnail_id );
				$image = apply_filters( 'tcp_get_image_in_excerpt', $image, $post->ID );
				if ( ! empty( $thumbnail_post->post_excerpt ) ) {
					//$image_attributes = array{0 => url, 1 => width, 2 => height};
					$image_attributes = wp_get_attachment_image_src( $thumbnail_id, $image_size );
					$width = $image_attributes[1];
					$image = '[caption id="attachment_' . $thumbnail_id . '" align="' . $image_align . ' tcp_featured_single_caption" width="' . $width  . '" caption="' . $thumbnail_post->post_excerpt  . '"]' . $image . '[/caption]';
				}
				$content = $image . $content;//$html .= $image;
			}
			$html = apply_filters( 'tcp_filter_excerpt', $html, $post->ID );
			if ( $align_buy_button_in_excerpt == 'north' ) return do_shortcode( $html . $content );
			else return do_shortcode( $content . $html );
		}
		return $content;
	}

	function loading_default_checkout_boxes() {
		tcp_register_checkout_box( 'thecartpress/checkout/TCPBillingExBox.class.php', 'TCPBillingExBox', 'billing' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPBillingBox.class.php', 'TCPBillingBox', 'billing' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPShippingBox.class.php', 'TCPShippingBox', 'shipping' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPShippingMethodsBox.class.php', 'TCPShippingMethodsBox', 'shipping-methods' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPPaymentMethodsBox.class.php', 'TCPPaymentMethodsBox', 'payment-methods' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPCartBox.class.php', 'TCPCartBox', 'cart' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPNoticeBox.class.php', 'TCPNoticeBox', 'notice' );
		tcp_register_checkout_box( 'thecartpress/checkout/TCPSigninBox.class.php', 'TCPSigninBox', 'login' );
	}

	function loading_default_checkout_plugins() {
		//shipping methods
		require_once( TCP_PLUGINS_FOLDER .'FreeTrans.class.php' );
		tcp_register_shipping_plugin( 'FreeTrans' );
		require_once( TCP_PLUGINS_FOLDER .'FlatRate.class.php' );
		tcp_register_shipping_plugin( 'FlatRateShipping' );
		require_once( TCP_PLUGINS_FOLDER .'ShippingCost.class.php' );
		tcp_register_shipping_plugin( 'ShippingCost' );
		require_once( TCP_PLUGINS_FOLDER .'LocalPickUp.class.php' );
		tcp_register_shipping_plugin( 'TCPLocalPickUp' );
		//payment methods
		require_once( TCP_PLUGINS_FOLDER .'PayPal/TCPPayPal.php' );
		tcp_register_payment_plugin( 'TCPPayPal' );
		require_once( TCP_PLUGINS_FOLDER .'Remboursement.class.php' );
		tcp_register_payment_plugin( 'TCPRemboursement' );
		require_once( TCP_PLUGINS_FOLDER .'NoCostPayment.class.php' );
		tcp_register_payment_plugin( 'NoCostPayment' );
		require_once( TCP_PLUGINS_FOLDER .'Transference.class.php' );
		tcp_register_payment_plugin( 'Transference' );
		require_once( TCP_PLUGINS_FOLDER .'CardOffLine/CardOffLine.class.php' );
		tcp_register_payment_plugin( 'TCPCardOffLine' );
		require_once( TCP_PLUGINS_FOLDER .'authorize.net/TCPAuthorizeNet.class.php' );
		tcp_register_payment_plugin( 'TCPAuthorizeNet' );
		require_once( TCP_PLUGINS_FOLDER .'FreeProducts.class.php' );
		tcp_register_payment_plugin( 'TCPFreeProducts' );
	}

	function activate_plugin() {
		if ( ! session_id() ) session_start();
		unset( $_SESSION['tcp_session'] );
		update_option( 'tcp_rewrite_rules', true );
		global $wp_version;
		if ( version_compare( $wp_version, '3.0', '<' ) ) exit( __( 'TheCartPress requires WordPress version 3.0 or newer.', 'tcp' ) );
		require_once( TCP_CLASSES_FOLDER . 'Roles.class.php' );
		require_once( TCP_DAOS_FOLDER . 'manage_daos.php' );
		//Shopping Cart page
		$shopping_cart_page_id = get_option( 'tcp_shopping_cart_page_id' );
		if ( ! $shopping_cart_page_id || ! get_page( $shopping_cart_page_id ) ) $shopping_cart_page_id = TheCartPress::createShoppingCartPage();
		else wp_publish_post( (int)$shopping_cart_page_id );

		//Checkout page
		$page_id = get_option( 'tcp_checkout_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) ) TheCartPress::createCheckoutPage( $shopping_cart_page_id );
		else wp_publish_post( (int)$page_id );

		//Catalogue page
		$page_id = get_option( 'tcp_catalogue_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) ) TheCartPress::createCataloguePage();
		else wp_publish_post( (int)$page_id );

		//Page My Account
		$page_id = get_option( 'tcp_my_account_page_id' );
		if ( ! $page_id || ! get_page( $page_id ) ) TheCartPress::create_my_account_page();
		else wp_publish_post( (int)$page_id );

		ProductCustomPostType::create_default_custom_post_type_and_taxonomies();
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
		) );
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
		) );
		//default shortcode: "all products"
		if ( ! get_option( 'tcp_shortcodes_data' ) )
			add_option( 'tcp_shortcodes_data', array( array(
				'id'			=> 'all_products',
				'title'			=> '',
				'desc'			=> 'List of all products',
				'post_type'		=> 'tcp_product',
				'use_taxonomy'	=> false,
				'taxonomy'		=> 'tcp_product_category',
				'included'		=> array(),
				'term'			=> '', //'tables',
				'pagination'	=> true,
				'loop'			=> '',
				'columns'		=> 2,
				'see_title'		=> true,
				'see_image'		=> false,
				'image_size'	=> 'thumbnail',
				'see_content'	=> false,
				'see_excerpt'	=> true,
				'see_author'	=> false,
				'see_meta_data'	=> false,
				'see_price'		=> false,
				'see_buy_button'=> false,
				'see_meta_utilities'	=> false,
				'see_first_custom_area'	=> false,
				'see_second_custom_area'=> false,
				'see_third_custom_area'	=> false,
			) ) );
		if ( ! get_option( 'tcp_settings' ) ) {
			$this->settings = array(
				'legal_notice'			=> __( 'Checkout notice', 'tcp' ),
				'stock_management'		=> false,
				'stock_adjustment'		=> 1,
				'disable_shopping_cart'	=> false,
				'disable_ecommerce'		=> false,
				'user_registration'		=> false,
				'see_price_in_content'	=> false,
				'see_price_in_excerpt'	=> true,
				'downloadable_path'		=> WP_PLUGIN_DIR . '/thecartpress/uploads',
				'see_buy_button_in_content'	=> true,
				'see_buy_button_in_excerpt'	=> false,
				'load_default_buy_button_style'				=> true,
				'load_default_shopping_cart_checkout_style'	=> true,
				'load_default_loop_style'					=> true,
				'responsive_featured_thumbnails'			=> true,
				'search_engine_activated'	=> true,
				'emails'				=> get_option('admin_email'),
				'currency'				=> 'EUR',
				'decimal_point'			=> '.',
				'thousands_separator'	=> ',',
				'unit_weight'			=> 'gr',
				'hide_visibles'			=> false,//hide_invisibles!!
				'activate_ajax'			=> false,
				'send_email'			=> true,
			);
			add_option( 'tcp_settings', $this->settings );
		}
		TheCartPress::createExampleData();
	}

	static function createShoppingCartPage() {
		$post = array(
			'comment_status' => 'closed',
			'post_content' => '[tcp_shopping_cart]',
			'post_status' => 'publish',
			'post_title' => __( 'Shopping cart', 'tcp' ),
			'post_type' => 'page',
		);
		$shopping_cart_page_id = wp_insert_post( $post );
		update_option( 'tcp_shopping_cart_page_id', $shopping_cart_page_id );
		return $shopping_cart_page_id;
	}

	static function createCheckoutPage( $shopping_cart_page_id = 0 ) {
		$post = array(
			'comment_status' => 'closed',
			'post_content' => '[tcp_checkout]',
			'post_status' => 'publish',
			'post_title' => __( 'Checkout', 'tcp' ),
			'post_type' => 'page',
			'post_parent' => $shopping_cart_page_id,
		);
		$checkout_page_id = wp_insert_post( $post );
		update_option( 'tcp_checkout_page_id', $checkout_page_id );
		return $checkout_page_id;
	}

	static function create_my_account_page() {
		$page = array(
			'comment_status' => 'closed',
			'post_content' => 'My Account',
			'post_content' => '[tcp_my_account]',
			'post_status' => 'publish',
			'post_title' => __( 'My Account','tcp-fe' ),
			'post_type' => 'page',
		);
		$my_account_page_id = wp_insert_post( $page );
		update_option( 'tcp_my_account_page_id', $my_account_page_id );
		return $my_account_page_id;
	}

	static function createCataloguePage() {
		$post = array(
			'comment_status' => 'closed',
			'post_content' => '[tcp_list id="all_products"]',
			'post_status' => 'publish',
			'post_title' => __( 'Catalogue', 'tcp' ),
			'post_type' => 'page',
		);
		$catalogue_page_id = wp_insert_post( $post );
		update_option( 'tcp_catalogue_page_id', $catalogue_page_id );
		return $catalogue_page_id;
	}

	static function createExampleData() {
		$products = wp_count_posts( 'tcp_product' );
		if ( $products->publish + $products->draft == 0 ) {
			$args = array(
				'cat_name' => __( 'Category One', 'tcp' ),
				'category_description' => __( 'Category One for Product One', 'tcp' ),
				'taxonomy' => 'tcp_product_category',
			);
			$category_id = wp_insert_category( $args );
			$post = array(
				'post_content' => 'Product One content, where you can read the best features of the Product One.',
				'post_status' => 'publish',
				'post_title' => __( 'Product One','tcp' ),
				'post_type' => 'tcp_product',
			);
			$post_id = wp_insert_post( $post );
			add_post_meta( $post_id, 'tcp_tax_id',  0 );
			add_post_meta( $post_id, 'tcp_is_visible', true );
			add_post_meta( $post_id, 'tcp_is_downloadable', false );
			add_post_meta( $post_id, 'tcp_type', 'SIMPLE' );
			add_post_meta( $post_id, 'tcp_hide_buy_button', false );
			add_post_meta( $post_id, 'tcp_price', 100 );
			add_post_meta( $post_id, 'tcp_weight', 12 );
			add_post_meta( $post_id, 'tcp_order', 10 );
			add_post_meta( $post_id, 'tcp_sku', 'SKU_ONE' );
			add_post_meta( $post_id, 'tcp_stock', -1 ); //No stock
			$category_id = term_exists( 'Category One', 'tcp_product_category' );
			if ( ! $category_id ) $category_id = wp_insert_term( 'Category One', 'tcp_product_category' );
			if ( isset( $category_id->term_id ) ) wp_set_object_terms( $post_id, (int)$category_id->term_id, 'tcp_product_category' );
			else wp_set_object_terms( $post_id, (int)$category_id, 'tcp_product_category' );
		}
	}

	function deactivate_plugin() {
		wp_delete_post( get_option( 'tcp_shopping_cart_page_id' ) );
		wp_delete_post( get_option( 'tcp_checkout_page_id' ) );
		wp_delete_post( get_option( 'tcp_my_account_page_id' ) );
		wp_delete_post( get_option( 'tcp_catalogue_page_id' ) );
		remove_role( 'customer' );
		remove_role( 'merchant' );
		//tcp_delete_custom_post_type( TCP_PRODUCT_POST_TYPE );
		//tcp_delete_custom_taxonomy( TCP_PRODUCT_CATEGORY );
		//tcp_delete_custom_taxonomy( TCP_PRODUCT_TAG );
	}

	function load_settings() {
		$this->settings = get_option( 'tcp_settings', array() );
	}

	function after_setup_theme() {
		if ( function_exists( 'add_theme_support' ) ) add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Allows to generate the xml for search engine
	 */
	function create_products_feed() {
		require_once( TCP_CLASSES_FOLDER . 'FeedForSearchEngine.class.php' );
		$feedForSearchEngine = new FeedForSearchEngine();
		$feedForSearchEngine->generateXML();
	}

	function load_custom_post_types_and_custom_taxonomies() {
		$post_types = tcp_get_custom_post_types();
		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			foreach( $post_types as $id => $post_type ) {
				if ( $post_type['activate'] ) {
					$register = array(
						'labels' => $post_type,
						'public' => isset( $post_type['public'] ) ? $post_type['public'] : true,
						'show_ui' => isset( $post_type['show_ui'] ) ? $post_type['show_ui'] : true,
						'show_in_menu' => isset( $post_type['show_in_menu'] ) ? $post_type['show_in_menu'] : true,
						'can_export' => isset( $post_type['can_export'] ) ? $post_type['can_export'] : true,
						'show_in_nav_menus'	=> isset( $post_type['show_in_nav_menus'] ) ? $post_type['show_in_nav_menus'] : true,
						'_builtin' => false,
						'_edit_link' => 'post.php?post=%d',
						'capability_type' => 'post',
						'hierarchical' => false,
						'query_var' => isset( $post_type['query_var'] ) ? $post_type['query_var'] : true,
						'supports' => isset( $post_type['supports'] ) ? $post_type['supports'] : array(),
						'rewrite' => strlen( $post_type['rewrite'] ) > 0 ? array( 'slug' => _x( $post_type['rewrite'], 'URL slug', 'tcp' ) ) : false,
						'has_archive' => strlen( $post_type['has_archive'] ) > 0 ? $post_type['has_archive'] : false,
					);
					register_post_type( $id, $register );
					$is_saleable = isset( $post_type['is_saleable'] ) ? $post_type['is_saleable'] : false;
					if ( $is_saleable ) {
						$this->register_saleable_post_type( $id );
						//if ( $register['has_archive'] ) ProductCustomPostType::register_post_type_archives( $id, $register['has_archive'] );
						if ( is_admin() ) {
							global $productcustomposttype;
							add_filter( 'manage_edit-' . $id . '_columns', array( &$productcustomposttype, 'custom_columns_definition' ) );
							add_filter( 'manage_edit-' . $id . '_sortable_columns', array( &$productcustomposttype, 'sortable_columns' ) );
							add_filter( 'request', array( &$productcustomposttype, 'price_column_orderby' ) );
						}
					}
					do_action( 'tcp_load_custom_post_types', $id, $post_type );
				}
			}
		}
		$taxonomies = tcp_get_custom_taxonomies();
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
			foreach( $taxonomies as $id => $taxonomy ) {
				if ( $taxonomy['activate'] ) {
					$register = array (
						'labels' => $taxonomy,
						'hierarchical' => $taxonomy['hierarchical'],
						'query_var' => $id,
						'rewrite' => strlen( $taxonomy['rewrite'] ) > 0 ? array( 'slug' => _x( $taxonomy['rewrite'], 'URL slug', 'tcp' ) ) : false,
					);
					$post_types = $taxonomy['post_type'];
					if ( ! is_array( $post_types ) ) $post_types = array( $post_types );
					foreach( $post_types as $post_type ) {
						register_taxonomy( $id, $post_type, $register );
					}
					do_action( 'tcp_load_custom_taxonomies', $id, $taxonomy );
				}
			}
		}
		if ( get_option( 'tcp_rewrite_rules', false ) ) {
			flush_rewrite_rules();
			update_option( 'tcp_rewrite_rules', false );
		}
	}

	/**
	 * Register a post type as saleable
	 * @since 1.1.6
	 */
	function register_saleable_post_type( $saleable_post_type ) {
		$this->saleable_post_types[] = $saleable_post_type;
	}

	function tcp_get_saleable_post_types( $saleable_post_types ) {
		return array_merge( $saleable_post_types, $this->saleable_post_types );
	}
	
	/**
	 * Defines system roles. This is useful to not be managed in the Roles Manager
	 * @since 1.2.8
	 */
	function tcp_get_default_roles( $default_roles ) {
		$default_roles[] = 'customer';
		$default_roles[] = 'merchant';
		return $default_roles;
	}
}

require_once( TCP_CUSTOM_POST_TYPE_FOLDER . 'ProductCustomPostType.class.php' );
require_once( TCP_CUSTOM_POST_TYPE_FOLDER . 'TemplateCustomPostType.class.php' );

$thecartpress = new TheCartPress();

//add_action( 'all', create_function( '', 'echo \'<!-- TODO: \', current_filter(), \' -->' ."\n" . '\';' ) );

/*function tcp_wp( $wp ) {
	if ( isset( $wp->query_vars['checkout'] ) || isset( $wp->query_vars["pagename"] ) ) {
		global $thecartpress;
		if ( $thecartpress->get_setting( 'https_checkout', false ) ) {
			if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
				//wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
			} else {
				//wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			}
			//exit();
		}
	}
}
add_action( 'wp', 'tcp_wp' );*/
?>
