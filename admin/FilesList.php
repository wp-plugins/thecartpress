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

$post_id  = isset( $_REQUEST['post_id'] )  ? $_REQUEST['post_id']  : 0;

function tcp_file_icon( $ext ) {
	echo '<img src="' . plugins_url( 'thecartpress/images/default-file.png' ) .'" border="0" width="20px" height="auto"/>';
}

$manager_def = apply_filters( 'tcp_get_downloadable_manager', array ( 'path' => dirname( dirname( __FILE__ ) ) . '/classes/DownloadableManager.class.php', 'class' => 'TCPDownloadableManager') );
require_once( $manager_def['path'] );
$manager = new $manager_def['class'];

$files = $manager->getFiles( $post_id );?>
<div class="wrap">

<h2><?php printf( __( 'Files of %s', 'tcp' ), get_the_title( $post_id ) );?></h2>
<?php if ( is_array( $files ) && count( $files ) > 0 ) : ?>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'size', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'size', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</tfoot>
<tbody>
<?php foreach( $files as $file ) : ?>
<tr>
	<td><?php tcp_file_icon( $file['ext'] );?> <?php echo $file['name'];?></td>
	<td><?php echo $file['size'];?></td>
	<td><a href="">delete</a> | <a href="">download</a></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
<?php else : ?>
<p><?php _e( 'The list of files is empty', 'tcp' );?><p>
<?php endif;?>
</div>
