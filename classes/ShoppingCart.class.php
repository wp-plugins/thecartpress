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

	private $visited_post_ids = array();
	private $shopping_cart_items = array();
	private $other_costs = array();
	
	function add( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 1, $unit_price = 0, $tax = 0, $unit_weight = 0 ) {
		if ( ! is_numeric( $post_id ) || ! is_numeric( $option_1_id ) || ! is_numeric( $option_2_id ) ) return;
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		$is_downloadable = tcp_is_downloadable( $post_id );
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) ) {
			if ( ! $is_downloadable ) {
				$sci = $this->shopping_cart_items[$shopping_cart_id];
				$sci->add( $count );
			}
		} else {
			$sci = new ShoppingCartItem( $post_id, $option_1_id, $option_2_id, $count, $unit_price, $tax, $unit_weight );
			if ( $is_downloadable ) {
				$sci->setDownloadable( true );
				$sci->setCount( 1 );
			} else
				$sci->setDownloadable( false );
			$this->shopping_cart_items[$shopping_cart_id] = $sci;
		}
	}

	/**
	 * Returns the total amount in the cart
	 * @see getTotalNoDownloadable()
	 */
	function getTotal( $otherCosts = false ) {
		$total = 0;
		foreach( $this->shopping_cart_items as $shopping_cart_item )
			$total += $shopping_cart_item->getTotal();
		if ( $otherCosts )
			$total += $this->getTotalOtherCosts();
		$total = apply_filters( 'tcp_shopping_cart_get_total', $total );
		return $total;
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

	function deleteAll() {
		unset( $this->shopping_cart_items );
		$this->shopping_cart_items = array();
		unset( $this->other_costs );
		$this->other_costs = array();
	}

	function delete( $post_id, $option_1_id = 0, $option_2_id = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) )
			unset( $this->shopping_cart_items[$shopping_cart_id] );
	}

	function modify( $post_id, $option_1_id = 0, $option_2_id = 0, $count = 0 ) {
		$shopping_cart_id = $post_id . '_' . $option_1_id . '_' . $option_2_id;
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) )
			if ($count > 0) {
				if ( ! tcp_is_downloadable( $post_id ) )
					$this->shopping_cart_items[$shopping_cart_id]->setCount( $count );
				else
					$this->shopping_cart_items[$shopping_cart_id]->setCount( 1 );
			} else
				$this->delete( $post_id, $option_1_id , $option_2_id );
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
		if ( isset( $this->shopping_cart_items[$shopping_cart_id] ) )
			return $this->shopping_cart_items[$shopping_cart_id];
		else
			return false;
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
	 * Returns the total amount of the no downloadable products in the cart.
	 * This function is used, for example, to calculate sending cost
	 * because the downloadable products has not sending cost.
	 */
	function getTotalNoDownloadable( $otherCosts = false ) {
		$total = 0;
		foreach( $this->shopping_cart_items as $item )
			if ( ! $item->isDownloadable() )
				$total += $item->getTotal();
		if ( $otherCosts )
			$total += $this->getTotalOtherCosts();
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

	/**
	 * Visited functions
	 */
	function getVisitedPosts() {
		return $this->visited_post_ids;
	}

	/**
	 * Visited functions
	 */
	function removeVisitedPost() {
		unset( $this->visited_post_ids );
		$this->visited_post_ids = array();
	}
	
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
	function addOtherCost( $id, $cost = 0, $desc = '' ) {
		$this->other_costs[$id] = new ShoppingCartOtherCost( $cost, $desc );
	}

	function removeOtherCost( $id ) {
		unset( $this->other_costs[$id] );
	}

	function getTotalOtherCosts() {
		$total = 0;
		foreach( $this->other_costs as $other_cost )
			$total += $other_cost->getCost();
		return $total;
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
	//
	private $is_downloadable = false;

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

	function getCount() {
		return $this->count;
	}

	function setCount($count) {
		$this->count = $count;
	}

	function getUnitPrice() {
		return $this->unit_price;
	}

	function getTax() {
		return (float)$this->tax;
	}

	function getUnitWeight() {
		return $this->unit_weight;
	}

	function getPrice() {
		return $this->unit_price * $this->count;
	}

	function getTotal() {
		if ( $this->tax == 0 )
			return $this->unit_price * $this->count;
		else {
			$price = $this->unit_price * (1 + $this->tax / 100);
			return $price * $this->count;
		}
	}

	function getWeight() {
		return $this->unit_weight * $this->count;
	}
	
	function isDownloadable() {
		return $this->is_downloadable;
	}

	function setDownloadable( $is_downloadable ) {
		$this->is_downloadable = $is_downloadable;
	}
}

class ShoppingCartOtherCost {
	private $cost;
	private $desc;

	function __construct( $cost = 0, $desc = '') {
		$this->cost = (double)$cost;
		$this->desc = $desc;
	}

	function getCost() {
		return $this->cost;
	}

	function getDesc() {
		return $this->desc;
	}
}
?>
