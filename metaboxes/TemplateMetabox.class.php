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

class TemplateMetabox {

	function registerMetaBox() {
		add_meta_box( 'tcp-template-template', __( 'Template class', 'tcp' ), array( &$this, 'showTemplateMetabox' ), TemplateCustomPostType::$TEMPLATE, 'normal', 'high' );
	}

	function showTemplateMetabox() {
		global $post;
		if ( $post->post_type != TemplateCustomPostType::$TEMPLATE ) return;
		if ( !current_user_can( 'edit_post', $post->ID ) ) return;
		$post_id = tcp_get_default_id( $post->ID );
		$template_class = get_post_meta( $post_id, 'tcp_template_class', true );?>
		<div class="form-wrap">
			<?php //wp_nonce_field( 'tcp-template-custom-fields', 'tcp-template-custom-fields_wpnonce', false, true );?>
			<table class="form-table"><tbody>
				<tr valign="top">
				<th scope="row"><label for="tcp_type"><?php _e( 'Template class', 'tcp' );?>:</label></th>
				<td>
					<select name="tcp_template_class" id="tcp_template_class">
						<option value="" <?php selected( $template_class, '' );?>><?php _e( 'No one selected', 'tcp' );?></option>
						<?php global $tcp_template_classes;
						foreach( $tcp_template_classes as $class => $value ) :?>
						<option value="<?php echo $class;?>" <?php selected( $template_class, $class );?>><?php echo $class;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<?php do_action( 'tcp_template_metabox_custom_fields', $post_id );?>
			</tbody>
			</table>
		</div>
	<?php }

	function saveCustomFields( $post_id, $post ) {
		if ( $post->post_type != TemplateCustomPostType::$TEMPLATE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$post_id = tcp_get_default_id( $post_id );
		$tcp_template_class = isset( $_REQUEST['tcp_template_class'] ) ? $_REQUEST['tcp_template_class'] : '';
		update_post_meta( $post_id, 'tcp_template_class', $tcp_template_class );
/*		$translations = tcp_get_all_translations( $post_id );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id )
					update_post_meta( $translation->element_id, 'tcp_template_class', $tcp_template_class );*/
		do_action( 'tcp_template_metabox_save_custom_fields', $post_id );
	}

	function deleteCustomFields( $post_id ) {
		$post = get_post( $post_id );
		if ( $post->post_type != TemplateCustomPostType::$TEMPLATE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$post_id = tcp_get_default_id( $post_id );
		delete_post_meta( $post_id, 'tcp_template_class' );
/*		$translations = tcp_get_all_translations( $post_id );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id )
					delete_post_meta( $translation->element_id, 'tcp_template_class' );*/
		do_action( 'tcp_template_metabox_delete_custom_fields', $post_id );
	}
}
?>
