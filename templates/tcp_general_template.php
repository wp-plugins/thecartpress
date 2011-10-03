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

function tcp_the_shopping_cart_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id' ), 'page' ) );
	if ( $echo )
		echo $url;
	else
		return $url;
}

function tcp_get_the_shopping_cart_url() {
	return tcp_the_shopping_cart_url( false );
}

function tcp_the_checkout_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );
	if ( $echo )
		echo $url;
	else
		return $url;
}

function tcp_get_the_checkout_url() {
	return tcp_the_checkout_url( false );
}

function tcp_the_continue_url( $echo = true) {
	global $thecartpress;
	$url = isset( $thecartpress->settings['continue_url'] ) && strlen( $thecartpress->settings['continue_url'] ) > 0 ? $thecartpress->settings['continue_url'] : get_home_url();
	if ( $echo ) echo $url;
	else return $url;
}

function tcp_get_the_continue_url() {
	return tcp_the_continue_url( false );
}


/**
 * Display Taxonomy Tree.
 *
 * This function is primarily used by themes which want to hardcode the Taxonomy
 * Tree into the sidebar and also by the TaxonomyTree widget in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_taxonomy_tree'.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_taxonomy_tree( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomy_tree' );
	if ( ! $args )
		$args = array(
			'style'			=> 'list',
			'show_count'	=> true,
			'hide_empty'	=> true,
			'taxonomy'		=> 'tcp_product_category',
			'title_li'		=> '',
			'echo'			=> false,
		);
	$tree = '<ul>' . wp_list_categories( $args ) . '</ul>';
	$tree = apply_filters( 'tcp_get_taxonomy_tree', $tree );
	if ( $echo )
		echo $before, $tree, $after;
	else
		return $before . $tree . $after;
}

/**
 * Display Shopping Cart Summary.
 *
 * This function is primarily used by themes which want to hardcode the Resumen
 * Shopping Cart into the sidebar and also by the ShoppingCartSummary widget
 * in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_shopping_cart_summary'.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_shopping_cart_summary( $args = false, $echo = true ) {
	do_action( 'tcp_get_shopping_cart_before_summary' );
	if ( ! $args )
		$args = array(
			'see_product_count' => false,
			'see_stock_notice'	=> true,
			'see_weight'		=> true,
			'see_delete_all'	=> false,
			'see_shopping_cart'	=> true,
			'see_checkout'		=> true,
		);
	global $thecartpress;
	$unit_weight		= isset( $thecartpress->settings['unit_weight'] ) ? $thecartpress->settings['unit_weight'] : 'gr';
	$stock_management	= isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
	$shoppingCart		= TheCartPress::getShoppingCart();
	$summary = '<ul class="tcp_shopping_cart_resume">';
	$discount = $shoppingCart->getAllDiscounts();
	if ( $discount > 0 )
		$summary .= '<li><span class="tcp_resumen_discount">' . __( 'Discount', 'tcp' ) . ':</span>&nbsp;' . tcp_format_the_price( $discount ) . '</li>';
	$summary .= '<li><span class="tcp_resumen_subtotal">' . __( 'Total', 'tcp' ) . ':</span>&nbsp;' . tcp_format_the_price( $shoppingCart->getTotalToShow( false ) ) . '</li>';

	if ( isset( $args['see_product_count'] ) ? $args['see_product_count'] : false )
		$summary .=	'<li><span class="tcp_resumen_count">' . __( 'N<sup>o</sup> products', 'tcp' ) . ':</span>&nbsp;' . $shoppingCart->getCount() . '</li>';

	if ( $stock_management && isset( $args['see_stock_notice'] ) ? $args['see_stock_notice'] : false )
		if ( ! $shoppingCart->isThereStock() )
			$summary .= '<li><span class="tcp_no_stock_nough">' . printf( __( 'No enough stock for some products. Visit the <a href="%s">Shopping Cart</a> to see more details.', 'tcp' ), tcp_get_the_shopping_cart_url() ) . '</span></li>';

	$see_weight = isset( $args['see_weight'] ) ? $args['see_weight'] : false;
	if ( $see_weight && $shoppingCart->getWeight() > 0 ) 
		$summary .= '<li><span class="tcp_resumen_weight">' . __( 'Weigth', 'tcp' ) . ':</span>&nbsp;' . tcp_number_format( $shoppingCart->getWeight() ) . '&nbsp;' . $unit_weight . '</li>';
		
	if ( isset( $args['see_shopping_cart'] ) ? $args['see_shopping_cart'] : true )
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_shopping_cart_link"><a href="' . tcp_get_the_shopping_cart_url() . '">' . __( 'Shopping cart', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_checkout'] ) ? $args['see_checkout'] : true )
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_checkout_link"><a href="' . tcp_get_the_checkout_url() . '">' . __( 'Checkout', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_delete_all'] ) ? $args['see_delete_all'] : false ) 
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_delete_all_link"><form method="post"><input type="submit" name="tcp_delete_shopping_cart" value="' . __( 'Delete', 'tcp' ) . '"/></form></li>';
	$summary = apply_filters( 'tcp_get_shopping_cart_summary', $summary, $args );
	$summary .= '</ul>';
	if ( $echo )
		echo $summary;
	else
		return $summary;
}

function tcp_get_taxonomies_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomies_cloud' );
	if ( ! $args )
		$args = array(
			'taxonomy'	=> 'tcp_product_tag',
			'echo'		=> false,
    	);
	$cloud = wp_tag_cloud( $args );
	$cloud = apply_filters( 'tcp_get_taxonomies_cloud', $cloud );
	if ( $echo )
		echo $before, $cloud, $after;
	else
		return $before . $cloud . $after;
}

function tcp_get_tags_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_tags_cloud' );
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_tags_cloud', $cloud );
	if ( $echo )
		echo $cloud;
	else
		return $cloud;
}

function tcp_get_suppliers_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_suppliers_cloud' );
	if ( ! $args )
		$args = array(
			'taxonomy'	=> 'tcp_product_supplier',
			'echo'		=> false,
    	);
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_suppliers_cloud', $cloud );
	if ( $echo )
		echo $cloud;
	else
		return $cloud;
}

function tcp_get_number_of_attachments( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$args = array(
		'post_type'		=> 'attachment',
		'numberposts'	=> -1,
		'post_status'	=> null,
		'post_parent'	=> $post_id,
		);
	$attachments = get_posts( $args );
	if ( is_array( $attachments ) )
		return count( $attachments );
	else
		return 0;
}

function tcp_get_sorting_fields() {
	$sorting_fields = array(
		array(
			'value'	=> 'order',
			'title'	=> __( 'Suggested', 'tcp' ),
		),
		array(
			'value'	=> 'price',
			'title' => __( 'Price', 'tcp' ),
		),
		array(
			'value'	=> 'title',
			'title'	=> __( 'Title', 'tcp' ),
		),
		array(
			'value'	=> 'author',
			'title'	=> __( 'Author', 'tcp' ),
		),
		array(
			'value'	=> 'date',
			'title'	=> __( 'Date', 'tcp' ),
		),
		array(
			'value'	=> 'comment_count',
			'title'	=> __( 'Popular', 'tcp' ),
		)
	);
	return apply_filters( 'tcp_sorting_fields', $sorting_fields );
}

function tcp_the_sort_panel() {
	$filter = new TCPFilterNavigation();
	$order_type = $filter->get_order_type();
	$order_desc = $filter->get_order_desc();
	$disabled_order_types = isset( $settings['disabled_order_types'] ) ? $settings['disabled_order_types'] : array();
	$sorting_fields = tcp_get_sorting_fields(); ?>
<div class="tcp_order_panel">
	<form method="post">
	<span class="tcp_order_type">
	<label for="tcp_order_type">
		<?php _e( 'Order by', 'tcp' ); ?>:&nbsp;
		<select id="tcp_order_type" name="tcp_order_type">
		<?php foreach( $sorting_fields as $sorting_field ) : 
			if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endif;
		endforeach; ?>
		</select>
	</label>
	</span><!-- .tcp_order_type -->
	<span class="tcp_order_desc">
	<label>
		<input type="radio" name="tcp_order_desc" id="tcp_order_asc" value="asc" <?php checked( $order_desc, 'asc' );?>/>
		<?php _e( 'Asc.', 'tcp' ); ?>
	</label>
	<label>
		<input type="radio" name="tcp_order_desc" id="tcp_order_desc" value="desc" <?php checked( $order_desc, 'desc' );?>/>
		<?php _e( 'Desc.', 'tcp' ); ?>
	</label>
	<span class="tcp_order_submit"><input type="submit" name="tcp_order_by" value="<?php _e( 'Order', 'tcp' );?>" /></span>
	</span><!-- .tcp_order_desc -->
	</form>
</div><!-- .tcp_order_panel --><?php
}


/**
 * Display calendar with days that have products as links.
 *
 * The calendar is cached, which will be retrieved, if it exists. If there are
 * no products for the month, then it will not be displayed.
 *
 * @since 1.1.3
 *
 * @param bool $initial Optional, default is true. Use initial calendar names.
 * @param bool $echo Optional, default is true. Set to false for return.
 */
function tcp_get_calendar( $args, $initial = true, $echo = true ) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

	$post_type = isset( $args['post_type'] ) ? $args['post_type'] : 'tcp_product';
	$cache = array();
	$key = md5( $m . $monthnum . $year . $post_type );
	if ( $cache = wp_cache_get( 'tcp_get_calendar', 'tcp_calendar' ) ) {
		if ( is_array($cache) && isset( $cache[ $key ] ) ) {
			if ( $echo ) {
				echo apply_filters( 'tcp_get_calendar',  $cache[$key] );
				return;
			} else {
				return apply_filters( 'tcp_get_calendar',  $cache[$key] );
			}
		}
	}

	if ( !is_array($cache) )
		$cache = array();

	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = '$post_type' AND post_status = 'publish' LIMIT 1");
		if ( !$gotsome ) {
			$cache[ $key ] = '';
			wp_cache_set( 'tcp_get_calendar', $cache, 'calendar' );
			return;
		}
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp'));
		$thismonth = gmdate('m', current_time('timestamp'));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
	$last_day = date('t', $unixmonth);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date < '$thisyear-$thismonth-01'
		AND post_type = '$post_type' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");
	$next = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'
		AND post_type = '$post_type' AND post_status = 'publish'
			ORDER BY post_date ASC
			LIMIT 1");

	/* translators: Calendar caption: 1: month name, 2: 4-digit year */
	$calendar_caption = _x('%1$s %2$s', 'calendar caption');
	$calendar_output = '<table id="wp-calendar">
	<caption>' . sprintf($calendar_caption, $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		$wd = esc_attr($wd);
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ( $previous ) {
		$link = add_query_arg( 'post_type', $post_type, get_month_link( $previous->year, $previous->month ) );
		$calendar_output .= "\n\t\t".'<td colspan="3" id="prev"><a href="' . $link . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month), date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year)))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	$calendar_output .= "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		$link = add_query_arg( 'post_type', $post_type, get_month_link( $next->year, $next->month ) );
		$calendar_output .= "\n\t\t".'<td colspan="3" id="next"><a href="' . $link . '" title="' . esc_attr( sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month), date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) ) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
	} else {
		$calendar_output .= "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}

	$calendar_output .= '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	// Get days with posts
	$dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
		AND post_type = '$post_type' AND post_status = 'publish'
		AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N);
	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT ID, post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts "
		."WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00' "
		."AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' "
		."AND post_type = '$post_type' AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( (array) $ak_post_titles as $ak_post_title ) {

				$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
		}
	}


	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr($pad) .'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')) )
			$calendar_output .= '<td id="today">';
		else
			$calendar_output .= '<td>';

		if ( in_array($day, $daywithpost) ) {// any posts today?
			$link = add_query_arg( 'post_type', $post_type, get_day_link( $thisyear, $thismonth, $day ) );
			$calendar_output .= '<a href="' . $link . '" title="' . esc_attr( $ak_titles_for_day[ $day ] ) . "\">$day</a>";
		} else {
			$calendar_output .= $day;
		}
		$calendar_output .= '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr($pad) .'">&nbsp;</td>';

	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

	$cache[ $key ] = $calendar_output;
	wp_cache_set( 'get_calendar', $cache, 'calendar' );

	if ( $echo )
		echo apply_filters( 'tcp_get_calendar',  $calendar_output );
	else
		return apply_filters( 'tcp_get_calendar',  $calendar_output );

}

/**
 * Purge the cached results of tcp_get_calendar.
 *
 * @see tcp_get_calendar
 * @since 2.1.0
 */
function tcp_delete_get_calendar_cache() {
	wp_cache_delete( 'tcp_get_calendar', 'tcp_calendar' );
}
add_action( 'save_post', 'tcp_delete_get_calendar_cache' );
add_action( 'delete_post', 'tcp_delete_get_calendar_cache' );
add_action( 'update_option_start_of_week', 'tcp_delete_get_calendar_cache' );
add_action( 'update_option_gmt_offset', 'tcp_delete_get_calendar_cache' );
?>
