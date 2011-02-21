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

require_once( dirname(dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
		
class PostMetabox {

	function registerMetaBox() {
		add_meta_box( 'tcp-post-related-content', __( 'Related content', 'tcp' ), array( &$this, 'showRelatedContent' ), 'post', 'normal', 'high' );
	}

	function showRelatedContent() {
		global $post;
		if ( $post->post_type != 'post' ) return;
		if ( !current_user_can( 'edit_post', $post->ID ) ) return;
		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';
		$is_translation = $lang != $source_lang;
		$post_id = tcp_get_default_id( $post->ID, 'post' );
		if ( $is_translation && $post_id == $post->ID) {
			_e( 'After saving the title and content, you will be able to edit those relations.', 'tcp');
			return;
		}
		$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( dirname( __FILE__ ) ) ) . '/thecartpress' ) . '/admin/';?>
		<ul class="subsubsub">
			<?php $count = RelEntities::count( $post_id, 'POST-PROD' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = '';?>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=POST-PROD"><?php _e( 'related products', 'tcp' );?> <?php echo $count;?></a></li>
			<?php $count = RelEntities::count( $post_id, 'POST-POST' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = '';?>
			<li>|</li>
			<li><a href="<?php echo $admin_path;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=POST-POST&post_type_to=post"><?php _e( 'related posts', 'tcp' );?> <?php echo $count;?></a></li>
		</ul>
		<div class="clear"></div>
	<?php }

	function deleteCustomFields( $post_id ) {
		$post = get_post( $post_id );
		if ( $post->post_type != 'post' ) return;
		if ( !isset( $_POST[ 'tcp-product-custom-fields_wpnonce' ] ) || !wp_verify_nonce( $_POST[ 'tcp-product-custom-fields_wpnonce' ], 'tcp-product-custom-fields' ) ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
		RelEntities::deleteAll( $post_id, 'POST_PROD' );
	}
}
?>
