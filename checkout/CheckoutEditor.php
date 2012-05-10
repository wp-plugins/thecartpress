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

require_once( TCP_CHECKOUT_FOLDER .'TCPCheckoutManager.class.php' );

$initial_path = dirname( dirname( TCP_ADMIN_FOLDER ) ) . '/';

if ( isset( $_REQUEST['tcp_save_fields'] ) ) {
	$partial_path = $_REQUEST['tcp_box_path'];
	$class_name = $_REQUEST['tcp_box_name'];
	require_once( $initial_path . $partial_path );
	$box = new $class_name();
	$box->save_config_settings(); ?>
	<div id="message" class="updated"><p>
		<?php printf( __( 'Data for %s saved', 'tcp' ), $class_name ); ?>
	</p></div><?php
/*} elseif ( isset( $_REQUEST['tcp_down'] ) ) {
	$tcp_box_name = $_REQUEST['tcp_box_name'];
	$order_steps = TCPCheckoutManager::get_steps();
	$order = 0;
	foreach( $order_steps as $i => $class_name ) {
		if ( $tcp_box_name == $class_name ) {
			$order = $i;
			break;
		}
	}
	$order_steps[$order] = $order_steps[$order + 1];
	$order_steps[$order + 1] = $class_name;
	TCPCheckoutManager::update_steps( $order_steps );
} elseif ( isset( $_REQUEST['tcp_up'] ) ) {
	$tcp_box_name = $_REQUEST['tcp_box_name'];
	$order_steps = TCPCheckoutManager::get_steps();
	$order = 0;
	foreach( $order_steps as $i => $class_name ) {
		if ( $tcp_box_name == $class_name ) {
			$order = $i;
			break;
		}
	}
	$order_steps[$order] = $order_steps[$order - 1];
	$order_steps[$order - 1] = $class_name;
	TCPCheckoutManager::update_steps( $order_steps );
} elseif ( isset( $_REQUEST['tcp_activate'] ) ) {
	$class_name = $_REQUEST['tcp_box_name'];
	TCPCheckoutManager::add_step( $class_name );
} elseif ( isset( $_REQUEST['tcp_deactivate'] ) ) {
	$class_name = $_REQUEST['tcp_box_name'];
	TCPCheckoutManager::remove_step( $class_name );*/
} elseif ( isset( $_REQUEST['tcp_restore_default'] ) ) {
	TCPCheckoutManager::restore_default();
}
?>
<div class="wrap">
<h2><?php _e( 'Checkout Editor', 'tcp' ); ?></h2>
<ul class="subsubsub"></ul>

<form method="post">
<input type="submit" name="tcp_restore_default" value="<?php _e( 'Restore default values', 'tcp' ); ?>" class="button-secondary" />
</form>

<div class="clear"></div>

<?php global $tcp_checkout_boxes;
$order_steps = TCPCheckoutManager::get_steps(); ?>
<h3><?php _e( 'Activated boxes', 'tcp' ); ?> <img src="images/loading.gif" class="tcp_checkout_editor_feedback" style="display:none;"/></h3>
<ul class="tcp_activated_boxes">
<?php if ( count( $order_steps ) > 0 ) :
	foreach( $order_steps as $class_name ) if ( isset( $tcp_checkout_boxes[$class_name] ) ) : $partial_path = $tcp_checkout_boxes[$class_name]; ?>
	<li class="tcp_checkout_step tcp_checkout_step_<?php echo $class_name; ?>" target="<?php echo $class_name; ?>">
		<h4><?php echo $class_name; ?></h4>
		<a href="#open" target="<?php echo $class_name; ?>" class="tcp_checkout_step_open"><?php _e( 'open', 'tcp'); ?></a>
		
		<div id="tcp_checkout_box_edit_<?php echo $class_name; ?>" class="tcp_checkout_box_edit" style="display: none;">
		<form method="post">
			<?php require_once( $initial_path . $partial_path );
			$box = new $class_name(); ?>
			<?php if ( $box->show_config_settings() ) : ?>
				<input type="hidden" name="tcp_box_path" value="<?php echo $partial_path; ?>" />
				<input type="hidden" name="tcp_box_name" value="<?php echo $class_name; ?>" />
				<p><input type="submit" name="tcp_save_fields" id="tcp_save_<?php echo $class_name; ?>" value="<?php _e( 'save', 'tcp' ); ?>" class="button-primary"/></p>
				</script>
			<?php endif; ?>
		</form>
		</div>
	</li>
	<?php endif; ?>
<?php endif; ?>
</ul>
<p class="description"><?php _e( 'Drag and drop to reorder', 'tcp' ); ?></p>

<?php $order_steps = TCPCheckoutManager::get_steps();
foreach( $order_steps as $class_name )
	if ( isset( $tcp_checkout_boxes[$class_name] ) )
		unset( $tcp_checkout_boxes[$class_name] );
$order_steps = array_diff( $tcp_checkout_boxes, $order_steps ); ?>

<h3><?php _e( 'Deactivated boxes', 'tcp' ); ?></h3>
<ul class="tcp_deactivated_boxes">
<?php if ( count( $order_steps ) > 0 ) :
	foreach( $tcp_checkout_boxes as $class_name => $partial_path ) : ?>
	<li class="tcp_checkout_step tcp_checkout_step_<?php echo $class_name; ?>" target="<?php echo $class_name; ?>">
		<h4><?php echo $class_name; ?></h4>
		<a href="#open" target="<?php echo $class_name; ?>" class="tcp_checkout_step_open"><?php _e( 'open', 'tcp'); ?></a>
		
		<div id="tcp_checkout_box_edit_<?php echo $class_name; ?>" class="tcp_checkout_box_edit" style="display: none;">
		<form method="post">
			<?php require_once( $initial_path . $partial_path );
			$box = new $class_name(); ?>
			<?php if ( $box->show_config_settings() ) : ?>
				<input type="hidden" name="tcp_box_path" value="<?php echo $partial_path; ?>" />
				<input type="hidden" name="tcp_box_name" value="<?php echo $class_name; ?>" />
				<p><input type="submit" name="tcp_save_fields" id="tcp_save_<?php echo $class_name; ?>" value="<?php _e( 'save', 'tcp' ); ?>" class="button-primary"/></p>
				</script>
			<?php endif; ?>
		</form>
		</div>
	</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
<p class="description"><?php _e( 'Drag and drop to Activated Box area', 'tcp' ); ?></p>

</div><!-- wrap -->
<script>
jQuery(document).ready(function() {
	jQuery('a.tcp_checkout_step_open').each( function() {
		var target = jQuery(this).attr('target');
		jQuery(this).click( function() {
			jQuery('div#tcp_checkout_box_edit_' + target).toggle();
			return false;
		});
	});
	jQuery('ul.tcp_activated_boxes').sortable({
		stop		: function(event, ui) { do_drop(); },
		connectWith	: 'ul.tcp_deactivated_boxes',
		placeholder	: 'tcp_checkout_placeholder',
	});
	jQuery('ul.tcp_deactivated_boxes').sortable({
		stop: function(event, ui) { do_drop(); },
		connectWith	: 'ul.tcp_activated_boxes',
		placeholder	: 'tcp_checkout_placeholder',
	});
});

function do_drop() {
	var lis = [];
	jQuery('ul.tcp_activated_boxes li.tcp_checkout_step').each(function() {
		lis.push(jQuery(this).attr('target'));
	});
	var li_string = '';
	for(var i in lis) {
		li_string += lis[i] + ',';
	}
	li_string = li_string.substring(0, li_string.length - 1);
	jQuery('.tcp_checkout_editor_feedback').show();
    jQuery.ajax({
    	async	: true,
		type    : "GET",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: {
			action	: 'tcp_checkout_steps_save',
			list	: li_string,
		},
		success : function(response) {
			jQuery('.tcp_checkout_editor_feedback').hide();
		},
		error	: function(response) {
			jQuery('.tcp_checkout_editor_feedback').hide();
		},
    });
}
</script>
