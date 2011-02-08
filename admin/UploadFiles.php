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

require_once( dirname( dirname( __FILE__ ) ).'/daos/Orders.class.php' );

$post_id  = isset( $_REQUEST['post_id'] )  ? $_REQUEST['post_id']  : 0;

function tcp_upload_file( $post_id, $file ) {
	$rev_name = strrev( $_FILES['upload_file']['name'] );
	$i = strpos( $rev_name, '.' );
	$ext = strrev( substr( $rev_name, 0, $i ) );
	$settings = get_option( 'tcp_settings' );
	$downloadable_path = isset( trim( $settings['downloadable_path'] ) ) ? $settings['downloadable_path'] : '';
	if ( strlen( $settings['downloadable_path'] ) == 0 ) {
		wp_die( __( 'The path where the downloadable files must be saved is not set.', 'tcp' ) );
		return false;
	} else {
		$folder_path = $settings['downloadable_path'];
		global $wpdb;
		$folder_path .= '/' . $wpdb->prefix . 'tcp';
		if ( ! file_exists( $folder_path ) )
			if ( ! mkdir( $folder_path ) )
				return false;
		$file_path = $folder_path . '/upload_' . $post_id . '.' . $ext;
		tcp_set_the_file( $post_id, $file_path );
		if ( move_uploaded_file( $_FILES['upload_file']['tmp_name'], $file_path ) ) {
			do_action( 'tcp_uploaded_file', $file_path );
			return true;
		} else
			return false;
	}
}

if ( $post_id ) {
	$file_path = tcp_get_the_file( $post_id );
	if ( isset( $_REQUEST['tcp_upload_virtual_file'] ) ) {
		if ( tcp_upload_file( $post_id, $_FILES['upload_file'] ) ) {?>
			<div id="message" class="updated"><p><?php 
				printf (__( 'Upload completed, uploaded %f bytes', 'tcp' ), number_format( $_FILES['upload_file']['size'], 2 ) );
			?></p></div><?php
			$file_path = __( 'recent uploaded', 'tcp' );
		} else {?>
			<div id="message" class="updated"><p><?php 
				_e( 'Error, the upload has not been completed', 'tcp' );
			?></p></div><?php
		}
	} else if ( isset( $_REQUEST['tcp_delete_virtual_file'] ) ) {
		$file_path = tcp_get_the_file( $post_id );
		do_action( 'tcp_delete_upload_file', $file_path );
		if ( unlink( $file_path ) ) {?>
			<div id="message" class="updated"><p><?php 
				_e( 'The file has been deleted succesfuly', 'tcp' );
			?></p></div><?php
		} else {?>
			<div id="message" class="error"><p><?php
				_e( 'The file can not be deleted', 'tcp' );
			?></p></div><?php
		}
		tcp_set_the_file( $post_id, '' );
		$file_path = '';
	}
	$post = get_post( $post_id );
	if ( $post ) : ?>
		<div class="wrap">
			<h2><?php echo __( 'Upload file for', 'tcp' );?>&nbsp;<i><?php echo $post->post_title;?></i></h2>
			<ul class="subsubsub">
				<li><a href="post.php?action=edit&post=<?php echo $post_id;?>"><?php _e( 'return to the product', 'tcp' );?></a></li>
			</ul><!-- subsubsub -->
			<div class="clear"></div>

			<form method="post" enctype="multipart/form-data">
				<input type="hidden" name="post_id" value="<?php echo $post_id;?>" />
			<?php if ( strlen( $file_path ) > 0 ) : ?>
				<table class="form-table"><tbody>
				<tr valign="top">
					<th scope="row"><label for=""><?php _e( 'File', 'tcp');?></label></th>
					<td><?php echo $file_path;?></td>
				</tr>
				</tbody>
				</table>
				<p>
					<input type="submit" id="tcp_delete_virtual_file" name="tcp_delete_virtual_file" value="<?php _e( 'delete file', 'tcp' );?>" class="button-primary"/>
				</p>
			<?php else : ?>
				<table class="form-table"><tbody>
				<tr valign="top">
					<th scope="row"><label for="upload_file"><?php _e( 'File', 'tcp');?></label></th>
					<td><input type="file" id="upload_file" name="upload_file" class="regular-text"/></td>
				</tr>
				</tbody>
				</table>
				<p>
					<input type="submit" id="tcp_upload_virtual_file" name="tcp_upload_virtual_file" value="<?php _e( 'upload file', 'tcp' );?>" class="button-primary" />
				</p>
			<?php endif;?>
			</form>
		</div>
	<?php endif;
}?>
