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


class CardOffLine extends TCP_Plugin {

	function getTitle() {
		return 'card off line payment';
	}

	function getDescription() {
		return 'card off line payment.<br/>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
		</th><td>
			<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
		</td></tr><?php
	}

	function saveEditFields( $data ) {
		$data['notice'] = isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		return $data;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart, $currency ) {
		return __( 'Off line payment.', 'tcp' );
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $currency, $order_id ) {
		$data		= tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$notify_url	= plugins_url( 'thecartpress/plugins/CardOffLine/notify.php' );
		$return_url = add_query_arg( 'tcp_checkout', 'ok', get_permalink() ); //home_url();
		$new_status	= $data['new_status'];?>
		<p><?php _e( 'Off line payment:', 'tcp' );?></p>
		<p><?php echo $data['notice'];?></p>
		<form name="tcp_offline_payment" id="tcp_offline_payment" action="<?php echo $notify_url;?>" method="post">
		<input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
		<input type="hidden" name="return_url" value="<?php echo $return_url;?>" />
		<input type="hidden" name="new_status" value="<?php echo $new_status;?>" />
		<table>
		<tbody>
		<tr valign="top">
		<th scope="row">
			<label for="card_number_1"><?php _e( 'Card number', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="card_number_1" name="card_number_1" size="4" maxlength="4" />
			<input type="text" id="card_number_2" name="card_number_2" size="4" maxlength="4" />
			<input type="text" id="card_number_3" name="card_number_3" size="4" maxlength="4" />
			<input type="text" id="card_number_4" name="card_number_4" size="4" maxlength="4" />
			<span class="tcp_error_offline"></span>
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="expiration_month"><?php _e( 'Expiration date', 'tcp' );?>:</label>
		</th><td>
			<select id="expiration_month" name="expiration_month" >
				<?php for($i = 1; $i < 13; $i++)
				echo '<option value="', $i, '">', $i, '</option>', "\n";?>
			</select>/
			<select id="expiration_year" name="expiration_year" >
				<?php for($i = 01; $i < 99; $i++)
				echo '<option value="', $i, '">', $i, '</option>', "\n";?>
			</select>
		</td>
		</tr>
		</tbody>
		</table>
		<script>
		//http://sites.google.com/site/abapexamples/javascript/luhn-validation
		String.prototype.luhnCheck = function() {
			var luhnArr = [[0,2,4,6,8,1,3,5,7,9], [0,1,2,3,4,5,6,7,8,9]], sum = 0;
			this.replace(/\D+/g,"").replace(/[\d]/g, function(c, p, o){
				sum += luhnArr[ (o.length-p)&1 ][ parseInt(c,10) ];
			});
			return (sum%10 === 0) && (sum > 0);
		};

		function tcp_checkCard() {
			var card_number = jQuery('#card_number_1').val() + "" + jQuery('#card_number_2').val() + "" + jQuery('#card_number_3').val() + "" + jQuery('#card_number_4').val();
			if ( card_number.luhnCheck() )
				jQuery("#tcp_offline_payment").submit();
			else {
				alert("<?php _e( 'Wrong card number', 'tcp' );?>");
				jQuery(".tcp_error_offline").innerHtml("<?php _e( 'Wrong card number', 'tcp' );?>");
			}
		}
		</script>
		<p><input type="button" onclick="tcp_checkCard();" name="tcp_send_off_line_info" value="<?php echo __( 'Send', 'tcp' );?>" class="button-primary"/></p>
		</form>
		<?php
	}

	function __construct() {
		if ( is_admin() ) {
			add_action( 'tcp_admin_order_editor', array( $this, 'tcp_admin_order_editor' ) );
		} else {
			global $wpdb;
			$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_offlines` (
			  `order_id`			bigint(20)	unsigned NOT NULL,
			  `card_number`			varchar(20)	NOT NULL,
			  `expiration_month`	integer		NOT NULL,
			  `expiration_year`		integer		NOT NULL,
			  `created_at`			date		NOT NULL,
			  PRIMARY KEY (`order_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			$wpdb->query( $sql );
		}
	}
	
	function tcp_admin_order_editor( $order_id ) {
		global $wpdb;
		$offline = $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_offlines where order_id = %d', $order_id ) );
		if ( $offline ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="card_number"><?php _e( 'Card number', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" name="card_number_1" id="card_number_1" size="4" maxlength="4" value="<?php echo substr( $offline->card_number, 0, 4 );?>" readonly="true"/>
			<input type="text" name="card_number_2" id="card_number_2" size="4" maxlength="4" value="<?php echo substr( $offline->card_number, 4, 4 );?>" readonly="true"/>
			<input type="text" name="card_number_3" id="card_number_3" size="4" maxlength="4" value="<?php echo substr( $offline->card_number, 8, 4 );?>" readonly="true"/>
			<input type="text" name="card_number_4" id="card_number_4" size="4" maxlength="4" value="<?php echo substr( $offline->card_number, 12 );?>" readonly="true"/>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">
			<label for="expiration_month"><?php _e( 'Expiration date', 'tcp' );?>:</label>:
		</th>
		<td>
			<input type="text" name="expiration_month" id="expiration_month" size="2" maxlength="2" value="<?php echo $offline->expiration_month;?>" readonly="true"/>
			/
			<input type="text" name="expiration_year" id="expiration_year" size="2" maxlength="2" value="<?php echo $offline->expiration_year;?>" readonly="true"/>
		</td>
		</tr><?php
		}
	}
}
?>
