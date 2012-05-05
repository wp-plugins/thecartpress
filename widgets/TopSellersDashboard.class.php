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

class TCPTopSellersDashboard {

	function show() { ?>
<div class="table table_content">
	<table style="width:100%" id="table_top_sellers">
	<tbody>
	<tr class="first">
		<td id="tcp_top_sellers_no_items" class="first b" colspan="2">
			<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" id="tcp_top_sellers_feedback" />
			<?php _e( 'No items to show', 'tcp' ); ?>
		</td>
	</tr>
	</tbody></table>
	<script>
	jQuery('.tcp_top_sellers_feedback').show();
    jQuery.ajax({
    	async	: true,
		type    : "GET",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: {
			action	: 'tcp_top_sellers_dashboard',
		},
		success : function(response) {
			response = eval(response);
			jQuery('#tcp_top_sellers_feedback').hide();
			if (response.length > 0) {
				jQuery('#tcp_top_sellers_no_items').hide();
				for(i in response) {
					var row = response[i];
					var html = '<tr><td class="first b"><a href="post.php?action=edit&post=' + row['id'] + '">' + row['title'] + '</a></td>';
					html += '<td class="t tcp_top_sellers_' + row['top'] + '">' + row['top'] + '</td></tr>';
					jQuery('#table_top_sellers tr:last').after(html);
				}
			}
		},
		error	: function(response) {
			jQuery('.tcp_top_sellers_feedback').hide();
		},
    });
	</script>
</div><?php
	}

	function tcp_top_sellers_dashboard() {
		if ( current_user_can( 'manage_options' ) ) {
			$customer_id = -1;
		} else {
			global $current_user;
			get_currentuserinfo();
			$customer_id = $current_user->ID;
		}
		$args = array(
			'post_type'			=> tcp_get_saleable_post_types(), //isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> 10,
			'meta_key'			=> 'tcp_total_sales',
			'orderby'			=> 'meta_value_num',
			'order'				=> 'desc',
			'fields'			=> 'ids',
		);
		if ( $customer_id > 0 ) $args['author'] = $customer_id;
		$ids = get_posts( $args );
		foreach ( $ids as $id ) {
			$result[] = array(
				'id'	=> $id,
				'title'	=> get_the_title( $id ),
				'top'	=> tcp_get_the_meta( 'tcp_total_sales', $id ),
			);
		}
		die( json_encode( $result ) );
	}

	function init() {
		add_action( 'wp_ajax_tcp_top_sellers_dashboard', array( $this, 'tcp_top_sellers_dashboard' ) );
	}

	function wp_dashboard_setup() {
		wp_add_dashboard_widget( 'tcp_top_sellers', __( 'Top Sellers', 'tcp' ), array( $this, 'show' ) );
	}

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
	}
}

new TCPTopSellersDashboard();
?>
