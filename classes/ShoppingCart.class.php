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
 * Session shopping cart
 */
class ShoppingCart {

	public static $OTHER_COST_SHIPPING_ID	= 'shipping';
	public static $OTHER_COST_PAYMENT_ID	= 'payment';
	
	private $visited_post_ids = array();
	private $wish_list_post_ids = array();
	private $shopping_cart_items = array();
	private $other_costs = array();
	private $freeShipping = false;
	private $discount = 0;
	private $order_id = 0;
	
	function add( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 1, $unit_price = 0, $tax = 0, $unit_weight = 0 ) {
		if ( ! is_numeric( $post_id ) || ! is_numeric( $option_1_id ) || ! is_numeric( $option_2_id ) ) return;
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$is_downloadable = tcp_is_downloadable( $post_id );
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			if ( ! $is_downloadable ) {
				$sci = new ShoppingCartItem( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $tax, $unit_weight );
				$sci = apply_filters( 'tcp_add_to_shopping_cart', $sci );
				if ( $sci ) {
					$sci = $this->shopping_cart_items[$shopping_cart_id];
					$sci->add( $count );
				}
			}
		} else {
			$sci = new ShoppingCartItem( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $tax, $unit_weight );
			$sci = apply_filters( 'tcp_add_to_shopping_cart', $sci );
			if ( $sci ) {
				if ( $is_downloadable ) {
					$sci->setDownloadable( true );
					$sci->setCount( 1 );
				} else {
					$sci->setDownloadable( false );
				}
				$this->shopping_cart_items[$shopping_cart_id] = $sci;
			}
		}
		$this->removeOrderId();
	}

	function modify( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			if ($count > 0) {
				if ( ! tcp_is_downloadable( $post_id ) ) {
					$this->shopping_cart_items[$shopping_cart_id]->setCount( $count );
				} else {
					$this->shopping_cart_items[$shopping_cart_id]->setCount( 1 );
				}
			} else {
				$this->delete( $post_id, $option_1_id , $option_2_id );
			}
			$this->removeOrderId();
		}
	}

	function delete( $post_id, $option_1_id = 0, $option_2_id = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) )
			unset( $this->shopping_cart_items[$shopping_cart_id] );
		$this->removeOrderId();
	}

	function deleteAll() {
		unset( $this->shopping_cart_items );
		$this->shopping_cart_items = array();
		unset( $this->other_costs );
		$this->other_costs = array();
		$this->deleteAllDiscounts();
		$this->removeOrderId();
	}

	function getItemsId() {
		$ids = array();
		foreach( $this->shopping_cart_items as $item )
			$ids[] = $item->getPostId();
		return $ids;
	}

	function getItems() {
		return $this->shopping_cart_items;
	}

	/**
	 * Returns the item if it is in the cart
	 */
	function getItem( $post_id, $option_1_id = 0 , $option_2_id = 0) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			return $this->shopping_cart_items[$shopping_cart_id];
		} elseif ( $option_1_id == 0 && $option_2_id == 0) {
			foreach( $this->shopping_cart_items as $item ) {
				if ( $item->getPostId() == $post_id ) {
					return $item;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	/**
	 * Returns the total amount in the cart
	 * @see getTotalForShipping()
	 */
	function getTotal( $otherCosts = false ) {
		$total = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item ) {
			//$total += $shopping_cart_item->getTotal();
			$total += $shopping_cart_item->getTotalWithTax();
		}
		if ( $otherCosts )
			$total += $this->getTotalOtherCosts();
		$total = apply_filters( 'tcp_shopping_cart_get_total', $total );
		return $total - $this->discount;
	}

	/**
	 * Return the number of articles in the cart
	 */
	function getCount() {
		$count = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item )
			$count += $shopping_cart_item->getCount();
		return $count;
	}

	function getWeight() {
		$weight = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item )
			$weight += $shopping_cart_item->getWeight();
		return $weight;
	}

	/**
	 * Returns true if the cart is empty
	 */
	function isEmpty() {
		return count( $this->shopping_cart_items ) == 0;
	}

	/**
	 * Returns true if the product exists in the cart
	 */
	function exists( $post_id, $option_1_id = 0 , $option_2_id = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		return isset( $this->shopping_cart_items[$shopping_cart_id] );
	}

	/**
	 * Return true if all the products in the cart are downloadable
	 */
	function isDownloadable() {
		foreach( $this->shopping_cart_items as $item )
			if ( ! $item->isDownloadable() ) return false;
		return true;
	}

	/**
	 * Order_id if the cart has been saved in the database
	 * @since 1.1.0
	 */
	function setOrderId( $order_id ) {
		$this->order_id = $order_id;
		return $this;
	}

	function getOrderId() {
		return $this->order_id;
	}

	function hasOrderId() {
		return $this->order_id > 0;
	}

	function removeOrderId() {
		return $this->setOrderId(0);
	}

	/**
	 * Returns the total amount to calculate shipping cost
	 */
	function getTotalForShipping() {
		$total = 0;
		foreach( $this->shopping_cart_items as $item )
			if ( ! $item->isDownloadable() && ! $item->isFreeShipping() )
				$total += $item->getTotal();
		return $total;
	}

	/**
	 * Visited functions
	 */
	function addVisitedPost( $post_id ) {
		if ( isset( $this->visited_post_ids[$post_id] ) )
			$this->visited_post_ids[$post_id]++;
		else
			$this->visited_post_ids[$post_id] = 0;
	}

	function getVisitedPosts() {
		return $this->visited_post_ids;
	}

	function deleteVisitedPost() {
		unset( $this->visited_post_ids );
		$this->visited_post_ids = array();
	}
	/**
	 * End Visited functions
	 */

	/**
	 * WishList functions
	 */
	function addWishList( $post_id ) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList = (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			$wishList[$post_id] = 1;
			update_user_meta( $user_id, 'tcp_wish_list', $wishList );
		} else {
			$this->wish_list_post_ids[$post_id] = 1;
		}
	}

	function isInWishList( $post_id ) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList =  (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			return isset( $wishList[$post_id] );
		} else {
			return isset( $this->wish_list_post_ids[$post_id] );
		}
	}

	function getWishList() {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList = (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			if ( count( $this->wish_list_post_ids ) > 0 ) {
				foreach( $this->wish_list_post_ids as $id => $item )
					$wishList[$id] = 1;
				update_user_meta( $user_id, 'tcp_wish_list', $wishList );
				unset( $this->wish_list_post_ids );
				$this->wish_list_post_ids = array();
			}
			return $wishList;
		} else {
			return $this->wish_list_post_ids;
		}
	}

	function deleteWishListItem( $post_id) {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			$wishList = (array)get_user_meta( $user_id, 'tcp_wish_list', true );
			unset( $wishList[$post_id] );
			update_user_meta( $user_id, 'tcp_wish_list', $wishList );
		} else {
			unset( $this->wish_list_post_ids[$post_id] );
		}
		
	}

	function deleteWishList() {
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {	
			update_user_meta( $user_id, 'tcp_wish_list', array() );
		} else {
			unset( $this->wish_list_post_ids );
			$this->wish_list_post_ids = array();
		}
	}

/*	function volcarWishList() {
		$current_user = wp_get_current_user();
		if ( $current_user->ID > 0 ) {
			update_user_meta( $current_user->ID, 'tcp_wish_list', $this->wish_list_post_ids );
			return true;
		} else {
			return false;
		}
	}*/
	/**
	 * End WishList functions
	 */

	function isThereStock( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
		if ( $post_id == 0 ) {
			foreach( $this->shopping_cart_items as $item ) {
				$stock = tcp_get_the_stock( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
				if ( $stock == 0 || ( $stock > -1 && $stock < $item->getCount() ) )
					return false;
			}
			return true;
		} else {
			$stock = tcp_get_the_stock( $post_id, $option_1_id, $option_2_id );
			if ( $stock == 0 || ( $stock > -1 && $stock < $item->getCount() ) )
				return false;
			else
				return true;
		}
	}
	
	/**
	 * Other costs API
	 */
	function addOtherCost( $id, $cost = 0, $desc = '', $order = 0 ) {
		if ( $cost == 0 )
			$this->deleteOtherCost( $id );
		else
			$this->other_costs[$id] = new ShoppingCartOtherCost( $cost, $desc, $order );
	}

	function deleteOtherCost( $id ) {
		if ( isset( $this->other_costs[$id] ) )
			unset( $this->other_costs[$id] );
	}

	function getOtherCosts() {
		return $this->other_costs;
	}

	function getOtherCostById( $id ) {
		$otherCost = $this->getOtherCosts();
		return isset( $otherCost[$id] ) ? $otherCost[$id] : false;
	}

	function deleteOtherCosts() {
		unset( $this->other_costs );
		$this->other_costs = array();
	}

	function getTotalOtherCosts() {
		$total = 0;
		foreach( $this->other_costs as $other_cost ) {
			$total += $other_cost->getCost();
		}
		return $total;
	}
	
	function setFreeShipping( $freeShipping = true ) {
		$this->freeShipping = (bool)$freeShipping;
	}

	function isFreeShipping() {
		return $this->freeShipping;
	}

	function setDiscount( $discount ) {
		$this->discount = $discount;
	}

	function getDiscount() {
		return $this->discount;
	}

	function getAllDiscounts() {
		$discount = $this->getDiscount();
		foreach( $this->shopping_cart_items as $item )
			$discount += $item->getDiscount();
		return $discount;
	}

	function deleteAllDiscounts() {
		$this->setDiscount( 0 );
		foreach( $this->shopping_cart_items as $item )
			$item->setDiscount( 0 );
	}
}

class ShoppingCartItem {
	private $post_id;
	private $option_1_id;
	private $option_2_id;
	private $count;
	private $unit_price;
	private $tax;
	private $unit_weight;
	private $is_downloadable = false;
	private $discount = 0;
	private $free_shipping = false;

	function __construct( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 1, $unit_price = 0, $tax = 0, $unit_weight = 0 ) {
		$this->post_id		= (int)$post_id;
		$this->option_1_id	= (int)$option_1_id;
		$this->option_2_id	= (int)$option_2_id;
		$this->count		= (int)$count;
		$this->unit_price	= (float)$unit_price;
		$this->tax			= (float)$tax;
		$this->unit_weight	= (float)$unit_weight;
		do_action( 'tcp_shopping_cart_item_created', $this );
	}

	function add( $count ) {
		$this->count += $count;
	}

	function getShoppingCartId() {
		return $this->post_id . '_' . $this->option_1_id . '_' . $this->option_2_id;
	}

	function getPostId() {
		return $this->post_id;
	}

	function getOption1Id() {
		return $this->option_1_id;
	}

	function getOption2Id() {
		return $this->option_2_id;
	}

	function getTitle() {
		return tcp_get_the_title( $this->post_id, $this->option_1_id, $this->option_2_id );
	}

	function getCount() {
		return $this->count;
	}

	function getUnits() {
		return $this->getCount();
	}

	function setCount($count) {
		$this->count = $count;
	}

	function getUnitPrice() {
		$price = $this->unit_price;
		return apply_filters( 'tcp_item_get_unit_price', $price, $this->getPostId() );
	}

	function getTax() {
		$tax = (float)$this->tax;
		return apply_filters( 'tcp_item_get_tax', $tax, $this->getPostId() );
	}

	function getUnitWeight() {
		$weight = $this->unit_weight;
		return apply_filters( 'tcp_item_get_unit_weight', $weight, $this->getPostId() );
	}

	function getPrice() {
		$price = $this->getUnitPrice() * $this->count;
		return apply_filters( 'tcp_item_get_price', $price, $this->getPostId() );
	}

	//since 1.1.0
	function getPriceWithTax() {
		$price = $this->getUnitPrice() * ( 1 + $this->getTax() / 100 );
		return apply_filters( 'tcp_item_get_price_with_tax', $price, $this->getPostId() );
	}

	function getTotal() {
		//if ( $this->getTax() == 0 )
			$total = ( $this->getUnitPrice() * $this->getCount() ) - $this->getDiscount();
		//else {
		//	$price = $this->unit_price * ( 1 + $this->getTax() / 100 );
		//	$price = $price * $this->count;
		//	$total = $price - $this->getDiscount();
		//}
		$total = apply_filters( 'tcp_shopping_cart_get_item_total', $total, $this->getPostId() );
		return $total;
	}

	//since 1.1.0
	function getTotalWithTax() {
		if ( $this->getTax() == 0 )
			$total = ( $this->getUnitPrice() * $this->getCount() ) - $this->getDiscount();
		else {
			$price = $this->getUnitPrice() * ( 1 + $this->getTax() / 100 );
			$price = $price * $this->getCount();
			$total = $price - $this->getDiscount();
		}
		$total = apply_filters( 'tcp_shopping_cart_get_item_total_with_tax', $total, $this->getPostId() );
		return $total;
	}

	function getWeight() {
		$weight = $this->getUnitWeight() * $this->count;
		return apply_filters( 'tcp_shopping_cart_get_weight', $weight, $this->getPostId() );
	}

	function isDownloadable() {
		return $this->is_downloadable;
	}

	function setDownloadable( $is_downloadable = true ) {
		$this->is_downloadable = $is_downloadable;
	}

	function setDiscount( $discount ) {
		$this->discount = $discount;
	}

	function addDiscount( $discount ) {
		$this->discount += $discount;
	}

	function getDiscount() {
		$discount = $this->discount;
		return apply_filters( 'tcp_item_get_discount', $discount, $this->getPostId() );
	}

	function setFreeShipping( $free_shipping = true ) {
		$this->free_shipping = $free_shipping;
	}

	function isFreeShipping() {
		return $this->free_shipping;
	}
}

class ShoppingCartOtherCost {
	private $cost;
	private $desc;
	private $order;

	function __construct( $cost = 0, $desc = '', $order = 0 ) {
		$this->cost = (float)$cost;
		$this->desc = $desc;
		$this->order = $order;
	}
	
	function __toString() {
		return $this->order . $this->desc;
	}

	function getCost() {
		return $this->cost;
	}

	function getDesc() {
		return $this->desc;
	}

	function getOrder() {
		return $this->order;
	}
}
?>
