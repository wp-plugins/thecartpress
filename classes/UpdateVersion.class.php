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

class TCPUpdateVersion {

	function update( $thecartpress ) {
		$version = (int)get_option( 'tcp_version' );
		if ( $version < 112 ) {
			global $wpdb;
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders MODIFY COLUMN `shipping_postcode` CHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders MODIFY COLUMN `billing_postcode` CHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_rel_entities WHERE field = \'units\'';
			if ( $wpdb->get_row( $sql ) ) {
				$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_rel_entities DROP COLUMN `units`;';
				$wpdb->query( $sql );
			}
			$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_rel_entities WHERE field = \'meta_value\'';
			if ( ! $wpdb->get_row( $sql ) ) {
				$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_rel_entities ADD COLUMN `meta_value` longtext NOT NULL AFTER `list_order`;';
				$wpdb->query( $sql );
			}
			require_once( TCP_DAOS_FOLDER . 'OrdersMeta.class.php' );
			OrdersMeta::createTable();
			//
			//TODO Deprecated 1.2.2
			//
		}
		if ( $version < 113 ) {
			$administrator = get_role( 'administrator' );
			if ( $administrator ) $administrator->add_cap( 'tcp_edit_wish_list' );
			$merchant = get_role( 'merchant' );
			if ( $merchant ) $merchant->add_cap( 'tcp_edit_wish_list' );
			$customer = get_role( 'customer' );
			if ( $customer ) $customer->add_cap( 'tcp_edit_wish_list' );
			$thecartpress->settings['use_default_loop']	= 'only_settings';
			update_option( 'tcp_settings', $thecartpress->settings );
			//
			//TODO Deprecated 1.2.3
			//
		}
		if ( $version < 117 ) {
			require_once( TCP_DAOS_FOLDER . 'OrdersDetailsMeta.class.php' );
			OrdersDetailsMeta::createTable();
			$new_post_types = array();
			$post_types = tcp_get_custom_post_types();
			foreach( $post_types as $id => $post_type ) {
				if ( isset( $post_type['name_id'] ) ) {
					$id = $post_type['name_id'];
					unset( $post_type['name_id'] );
				}
				$new_post_types[$id] = $post_type;
			}
			tcp_set_custom_post_types($new_post_types);

			$new_taxonomies = array();
			$taxonomies = tcp_get_custom_taxonomies();
			foreach( $taxonomies as $id => $taxonomy ) {
				if ( isset( $taxonomy['name_id'] ) ) {
					$id = $taxonomy['name_id'];
					unset( $taxonomy['name_id'] );
				}
				$new_taxonomies[$id] = $taxonomy;
			}
			tcp_set_custom_taxonomies( $new_taxonomies );

			$post_type_defs = tcp_get_custom_post_types();
			if ( isset( $post_type_defs[TCP_PRODUCT_POST_TYPE] ) ) {
				$rewrite = $thecartpress->get_setting( 'product_rewrite', '' );
				if ( strlen( $rewrite ) > 0 ) $post_type_defs[TCP_PRODUCT_POST_TYPE]['rewrite'] = $rewrite;
			}
			tcp_set_custom_post_types( $post_type_defs );

			$taxonomy_defs = tcp_get_custom_taxonomies();
			if ( isset( $taxonomy_defs[TCP_PRODUCT_CATEGORY] ) ) {
				$rewrite = $thecartpress->get_setting( 'category_rewrite', '' );
				if ( strlen( $rewrite ) > 0 ) $taxonomy_defs[TCP_PRODUCT_CATEGORY]['rewrite'] = array( 'slug' => $rewrite );
			}
			if ( isset( $taxonomy_defs[TCP_PRODUCT_TAG] ) ) {
				$rewrite = $thecartpress->get_setting( 'tag_rewrite', '' );
				if ( strlen( $rewrite ) > 0 ) $taxonomy_defs[TCP_PRODUCT_TAG]['rewrite'] = array( 'slug' => $rewrite );
			}
			//if ( isset( $taxonomy_defs[TCP_SUPPLIER_TAG] ) ) {
			//	$rewrite = $thecartpress->get_setting( 'supplier_rewrite', '' );
			//	if ( strlen( $rewrite ) > 0 ) $taxonomy_defs[TCP_SUPPLIER_TAG]['rewrite'] = array( 'slug' => $rewrite );
			//}
			tcp_set_custom_taxonomies( $taxonomy_defs );
			update_option( 'tcp_version', 117 );
			//
			//TODO Deprecated 1.2.7
			//
		}
		if ( $version < 118 ) {
			global $wpdb;
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders_details MODIFY COLUMN `name` CHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders_details MODIFY COLUMN `option_1_name` CHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders_details MODIFY COLUMN `option_2_name` CHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			$taxonomies = tcp_get_custom_taxonomies();
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) {
				$save = false;
				foreach( $taxonomies as $id => $taxonomy ) {
					if ( is_array( $taxonomy['rewrite'] ) ) {
						$taxonomies[$id]['rewrite'] = $taxonomy['rewrite']['slug'];
						$save = true;
					}
				}
				if ( $save ) tcp_set_custom_taxonomies( $taxonomies );
			}
			require_once( TCP_DAOS_FOLDER . 'OrdersCostsMeta.class.php' );
			OrdersCostsMeta::createTable();
			update_option( 'tcp_version', 118 );
			//
			//TODO Deprecated 1.2.8
			//
		}
		if ( $version < 120 ) {
			global $wpdb;
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_addresses MODIFY COLUMN `postcode` CHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );

			$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders WHERE field = \'billing_tax_id_number\'';
			if ( ! $wpdb->get_row( $sql ) ) {
				$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders ADD COLUMN `billing_tax_id_number` varchar(15) NOT NULL AFTER `billing_company`;';
				$wpdb->query( $sql );
			}
			//
			//TODO Deprecated 1.3
			//
		}
		if ( $version < 126 ) {
			global $wpdb;
			$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders WHERE field = \'payment_notice\'';
			$row = $wpdb->get_row( $sql );
			if ( ! $row ) {
				$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders ADD COLUMN `payment_notice` VARCHAR(500) NOT NULL AFTER `payment_method`;';
				$wpdb->query( $sql );
			}
			$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders WHERE field = \'shipping_notice\'';
			$row = $wpdb->get_row( $sql );
			if ( ! $row ) {
				$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders ADD COLUMN `shipping_notice` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `weight`;';
				$wpdb->query( $sql );
			}
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders MODIFY COLUMN `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders MODIFY COLUMN `comment_internal` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			$wpdb->query( $sql );
			//
			//TODO Deprecated 1.4
			//
		}
		if ( $version < 126.1 ) {
			global $wpdb;
			$sql = 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_orders WHERE field = \'payment_notice\'';
			$row = $wpdb->get_row( $sql );
			if ( $row ) {
				$sql = 'ALTER TABLE ' . $wpdb->prefix . 'tcp_orders MODIFY COLUMN `payment_notice` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
				$wpdb->query( $sql );
			}
			//
			//TODO Deprecated 1.4
			//
		}
		update_option( 'tcp_version', 126 );//TODO change to 126.1
	}
}
?>