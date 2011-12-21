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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );

class TCPDownloadableList {
	function show( $echo = true ) {
		ob_start(); ?>
<script language="JavaScript">
function tcp_refresh(url) {
	setTimeout('tcp_reload()', 3000);
	window.open(url, 'downloadable');
}

function tcp_reload() {
	window.location.reload( false );
}
</script>
<div class="wrap">

<?php if ( is_admin() ) : ?>
	<h2><?php echo __( 'Downloadable products', 'tcp' );?></h2>
	<div class="clear"></div>
<?php endif; ?>
<?php
global $current_user;
get_currentuserinfo();
$orders = Orders::getProductsDownloadables( $current_user->ID );
if ( is_array( $orders ) && count( $orders ) > 0 ) {
	$path = get_bloginfo('url') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/VirtualProductDownloader.php';
	//$path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/VirtualProductDownloader.php';
	$max_date = date( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
	foreach( $orders as $order ) : ?>
		<div id="tcp_down_item" style="display: in-line;">
		<ul>
			<?php $url = $path;
			$url = add_query_arg( 'order_detail_id', $order->order_detail_id, $url ); ?>
			<li><a href="#" onclick="tcp_refresh('<?php echo $url; ?>');return false;" title="<?php _e( 'download the product', 'tcp' );?>"><?php echo get_the_title( $order->post_id );?></a></li>
			<li><a href="#" onclick="tcp_refresh('<?php echo $url; ?>');return false;" title="<?php _e( 'download the product', 'tcp' );?>"><?php echo get_the_post_thumbnail( $order->post_id );?></a></li>
		<?php if ( $order->expires_at != $max_date ) : ?>
			<li><?php printf( __('expires at %s', 'tcp' ), $order->expires_at );?></li>
		<?php endif;?>
		<?php if ( $order->max_downloads > -1 ) : ?>
			<li><?php printf( __('remaining number of downloads are %s', 'tcp' ), $order->max_downloads );?></li>
		<?php endif;?>
		</ul>
		</div>
	<?php endforeach;
} else  {
	_e( 'No products to download', 'tcp' );
}?>
</div>
	<?php
	$out = ob_get_clean();
	if ( $echo ) echo $out;
	else return $out;
	}
}
?>
