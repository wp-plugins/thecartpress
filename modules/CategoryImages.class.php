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

class TCPCategoryImages {
	function __construct() {
		add_filter( 'attachment_fields_to_edit', array( &$this, 'attachment_fields_to_edit' ), 20, 2 );
	}

	function attachment_fields_to_edit( $fields, $post ) {
		if ( isset( $fields['image-size'] ) && isset( $post->ID ) ) {
			$image_id = (int) $post->ID;
			ob_start(); ?>
			<div class="tcp-modal-control" id="tcp-modal-control-<?php echo $image_id; ?>">
				<span class="button create-association"><?php _e( 'Associate with <span class="term-name">this term</span>', 'tcp' ); ?></span>
				<span class="remove-association"><?php _e( 'Remove association with <span class="term-name">this term</span>', 'tcp' ); ?></span>
				<input class="tcp-button-image-id" name="'tcp-button-image-id-<?php echo $image_id; ?>" type="hidden" value="<?php echo $image_id; ?>" />
				<input class="tcp-button-nonce-create" name="tcp-button-nonce-create-<?php echo $image_id; ?>" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'tcp-create-association' ) ); ?>" />
				<input class="tcp-button-nonce-remove" name="tcp-button-nonce-remove-<?php echo $image_id; ?>" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'tcp-remove-association' ) ); ?>" />
			</div>
			<?php $fields['image-size']['extra_rows']['tcp-plugin-button']['html'] = ob_get_clean();
		}
		return $fields;
	}
}

//new TCPCategoryImages();
?>