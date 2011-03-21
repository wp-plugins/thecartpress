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

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Countries.class.php' );
//require_once( 'daos/Ranges.class.php' );
//require_once( 'daos/Zones.class.php' );
//require_once( 'daos/Costs.class.php' );

class ShippingCost extends TCP_Plugin {

	function getTitle() {
		return 'ShippingCost';
	}

	function getDescription() {
		return __( 'Calculate the shipping cost using a table of weights ranges and zones.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>', 'tcp' );
	}
	
	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		global $thecartpress;
		$cost = $this->getCost( $instance, $shippingCountry, $shoppingCart );
		return __( 'The cost of Shipping service will be ', 'tcp' ) . tcp_number_format( $cost ) . '&nbsp;' . tcp_get_the_currency();
	}

	function showEditFields( $data ) {
		$stored_data = isset( $data['costs'] );
		$ranges = isset( $data['ranges'] ) ? $data['ranges'] : array( 10, 20 );
		$zones = isset( $data['zones'] ) ? $data['zones'] : array(
			'0' => array( 'ES', 'FR', 'PT' ),
			'1' => array( 'CA', 'MX', 'US' ),
			'2' => array( 'CN', 'KR', 'JP' ),
		);
		$costs = isset( $data['costs'] ) ? $data['costs'] : array(
			10 => array(
				'0' => 1.5,
				'1' => 2.5,
				'2' => 3,
			),
			20 => array(
				'0' => 4.5,
				'1' => 5.5,
				'2' => 6
			),
		);
//var_dump( $costs ); echo '<br><br>';
//var_dump( $zones );
		if ( isset( $_REQUEST['tcp_copy_from_instance'] ) ) {
			$plugin_data = get_option( 'tcp_plugins_data_shi_' . get_class( $this ) );
			$data = reset( $plugin_data );
			$ranges = $data['ranges'];
			$zones = $data['zones'];
			$costs = $data['costs'];?>
			<div id="message" class="updated"><p>
				<?php _e( 'Remember to <strong>save</strong> before delete other rows or columns', 'tcp' );?>
			</p></div><?php
			$stored_data = false;
		} elseif ( isset( $_REQUEST['tcp_insert_range'] ) && isset( $_REQUEST['tcp_insert_range_value'] ) ) {
			$new_range = (int)$_REQUEST['tcp_insert_range_value'];
			$ranges[] = $new_range;
			foreach( $zones as $z => $zone )
				$costs[$new_range][$z] = 0;
			sort( $ranges );?>
			<div id="message" class="updated"><p>
				<?php _e( 'Remember to <strong>save</strong> before delete other rows or columns', 'tcp' );?>
			</p></div><?php
			$stored_data = false;
		} elseif ( isset( $_REQUEST['tcp_add_zone'] ) ) {
			$zones[] = array();
			foreach( $ranges as $range )
				$costs[$range][] = 0;
		} else
			foreach( $_REQUEST as $index => $value )
				if ( $this->startsWith( $index, 'tcp_delete_range-' ) ) {
					$names = explode( '-', $index );
					$range = $names[1];
					unset( $ranges[$range] );?>
					<div id="message" class="updated"><p>
						<?php _e( 'Remember to <strong>save</strong> before delete other rows or columns', 'tcp' );?>
					</p></div><?php
					$stored_data = false;
					break;
				} elseif ( $this->startsWith( $index, 'tcp_delete_zone-' ) ) {
					$names = explode( '-', $index );
					$zone = $names[1];
					unset( $zones[$zone] );
					?>
					<div id="message" class="updated"><p>
						<?php _e( 'Remember to <strong>save</strong> before delete other rows or columns', 'tcp' );?>
					</p></div><?php
					$stored_data = false;
					break;
				}?>
		</tbody></table>
	<?php if ( $stored_data ) : ?>
		<p><input type="submit" name="tcp_copy_from_instance" value="<?php _e( 'copy from first instance', 'tcp' );?>" class="button-secondary"/>
	<?php endif;?>

		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th class="manage-column"><?php _e( 'Weight ranges', 'tcp' );?></th>
			<?php foreach( $zones as $z => $isos ) : ?>
			<th scope="col" class="manage-column">
				<?php printf( __( 'Zone %d', 'tcp' ), $z );?>
				<?php if ( $stored_data ) : ?>
					<input type="submit" name="tcp_delete_zone-<?php echo $z;?>" value="<?php _e( 'delete', 'tcp' );?>" class="button-secondary"/>
				<?php endif;?>
				<input type="hidden" name="zones[]" value="<?php echo $z;?>"/>
			</th>
			<?php endforeach;?>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th class="manage-column"><?php _e( 'Weight ranges', 'tcp' );?></th>
			<?php foreach( $zones as $z => $isos ) : ?>
			<th scope="col" class="manage-column"><?php printf( __( 'Zone %d', 'tcp' ), $z );?></th>
			<?php endforeach;?>
			<th>&nbsp;</th>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach( $ranges as $r => $range ) : ?>
		<tr>
			<th scope="row">
				<?php printf( __( 'Range %d', 'tcp' ), $r );?>:
				<input type="text" name="ranges[]" value="<?php echo $range;?>" size="5" maxlength="10"/>
			</th>
			<?php foreach( $zones as $z => $zone ) : ?>
			<td><input type="text" name="cost-<?php echo $range;?>[]" value="<?php echo $costs[$range][$z];?>" size="6" maxlength="13"/></td>
			<?php endforeach;?>
			<td>
			<?php if ( $stored_data ) : ?>
			<input type="submit" name="tcp_delete_range-<?php echo $r;?>" value="<?php _e( 'delete range', 'tcp' );?>" class="button-secondary" />
			<?php endif;?>&nbsp;
			</td>
		</tr>
		<?php endforeach;?>
		<tr>
			<td colspan="<?php echo count( $zones ) + 2;?>">
			<?php if ( $stored_data ) : ?>
				<input type="submit" name="tcp_insert_range" value="<?php _e( 'insert new range', 'tcp' );?>" class="button-secondary" />
				<input type="text" name="tcp_insert_range_value" size="5" maxlength="10" />
			<?php endif;?>&nbsp;
			</td>
		</tr>
		</tbody></table>

		<p>&nbsp;</p>

		<table  class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
		<?php foreach( $zones as $z => $isos ) : ?>
			<th class="manage-column" colspan="2"><?php printf( __( 'Zone %s', 'tcp' ), $z );?></th>
		<?php endforeach;?>
		</tr>
		</thead>
		<tfoot>
		<tr>
		<?php foreach( $zones as $z => $isos ) : ?>
			<th class="manage-column" colspan="2"><?php printf( __( 'Zone %s', 'tcp' ), $z );?></th>
		<?php endforeach;?>
		</tr>
		</tfoot>
		<tbody>
		<tr>
		<?php foreach( $zones as $z => $isos ) : ?>
			<td>
				<select id="zones_isos_<?php echo $z;?>" name="zones_isos_<?php echo $z;?>[]" style="height:auto" size="8" multiple="true">
				<?php global $countries_db;
				foreach( $countries_db as $country ) :?>
					<option value="<?php echo $country->iso;?>" <?php tcp_selected_multiple( $isos, $country->iso );?>><?php echo $country->name;?></option>
				<?php endforeach;?>
				</select>
			</td>
			<td>
				<input type="button" value="<?php _e( 'EU', 'tcp');?>" title="<?php _e( 'To select countries from the European Union', 'tcp' );?>" onclick="tcp_select_eu('zones_isos_<?php echo $z;?>');" class="button-secondary"/>
				<input type="button" value="<?php _e( 'NAFTA', 'tcp');?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' );?>" onclick="tcp_select_nafta('zones_isos_<?php echo $z;?>');" class="button-secondary"/>
				<input type="button" value="<?php _e( 'CARICOM', 'tcp');?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' );?>" onclick="tcp_select_caricom('zones_isos_<?php echo $z;?>');" class="button-secondary"/>
				<input type="button" value="<?php _e( 'MERCASUR', 'tcp');?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' );?>" onclick="tcp_select_mercasur('zones_isos_<?php echo $z;?>');" class="button-secondary"/>
				<input type="button" value="<?php _e( 'CAN', 'tcp');?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' );?>" onclick="tcp_select_can('zones_isos_<?php echo $z;?>');" class="button-secondary"/>				
				<input type="button" value="<?php _e( 'AU', 'tcp');?>" title="<?php _e( 'To select countries from African Union', 'tcp' );?>" onclick="tcp_select_au('zones_isos_<?php echo $z;?>');" class="button-secondary"/>				
				<input type="button" value="<?php _e( 'APEC', 'tcp');?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' );?>" onclick="tcp_select_apec('zones_isos_<?php echo $z;?>');" class="button-secondary"/>
				<input type="button" value="<?php _e( 'ASEAN', 'tcp');?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' );?>" onclick="tcp_select_asean('zones_isos_<?php echo $z;?>');" class="button-secondary"/>
			</td>
		<?php endforeach;?>
		</tr>
		<?php if ( $stored_data ) : ?>
		<tr>
		<td colspan="<?php echo count( $zones );?>">
			<input type="submit" id="tcp_add_zone" name="tcp_add_zone" value="<?php _e( 'Add new zone', 'tcp' );?>" class="button-secondary" />
		</td>
		</tr>
		<?php endif;?>
	<?php
	}

	function saveEditFields( $data ) {
		$zones = isset( $_REQUEST['zones'] ) ? $_REQUEST['zones'] : array();
		$ranges = isset( $_REQUEST['ranges'] ) ? $_REQUEST['ranges'] : array();
		$costs = array();
		foreach( $zones as $z => $zone )
			foreach( $ranges as $range )
				$costs[$range][] = isset( $_REQUEST['cost-' . $range][$z] ) ? (float)$_REQUEST['cost-' . $range][$z] : 0;
		$new_zones = array();
		$z = 0;
		foreach( $zones as $zone )
			if ( isset( $_REQUEST['zones_isos_' . $zone] ) )
				$new_zones[$z++] = $_REQUEST['zones_isos_' . $zone];
			else
				$new_zones[$z++] = array();
		$data['zones'] = $new_zones;
		$data['ranges'] = $ranges;
		$data['costs'] = $costs;
		return $data;
	}

	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		$totalWeight = $shoppingCart->getWeight();
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		$zones = $data['zones'];
		$ranges = $data['ranges'];
		$costs = $data['costs'];
		foreach( $ranges as $range )
			if ( $range > $totalWeight ) {
				$selected_range = $range;
				break;
			}
		$selected_zone = 0;
		foreach( $zones as $z => $zone)
			if ( in_array( $shippingCountry, $zone ) ) {
				$selected_zone = $z;
				break;
			}
		return $costs[$selected_range][$selected_zone];
	}

	private function startsWith( $Haystack, $Needle ) {
    	return strpos( $Haystack, $Needle ) === 0;
	}
}
?>
