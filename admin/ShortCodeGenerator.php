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

$shortcodes_data = get_option( 'tcp_shortcodes_data' );
$shortcode_id = isset( $_REQUEST['shortcode_id'] ) ? $_REQUEST['shortcode_id'] : -1;

function tcp_exists_shortcode_id( $id ) {
	global $shortcodes_data;
	global $shortcode_id;

	foreach( $shortcodes_data as $i => $data )
		if ( $shortcode_id != $i && $data['id'] == $id )
			return true;
	return false;
}

if ( isset( $_REQUEST['tcp_shortcode_save'] ) ) {
	if ( ! isset( $_REQUEST['id'] ) || strlen( trim( $_REQUEST['id'] ) ) == 0 ) {?>
		<div id="message" class="error"><p>
			<?php _e( 'The field Identifier must be filled', 'tcp' );?>
		</p></div><?php
	} elseif ( tcp_exists_shortcode_id( $_REQUEST['id'] ) ) {?>
		<div id="message" class="error"><p>
			<?php _e( 'The field Identifier is repeated', 'tcp' );?>
		</p></div><?php
	} else {
		if ( ! $shortcodes_data ) $shortcodes_data = array();
		$shortcodes_data[$shortcode_id] = array (
			'id'					=> isset( $_REQUEST['id'] ) ? str_replace( ' ', '_', trim( $_REQUEST['id'] ) ) : 'id_' . $shortcode_id,
			'title'					=> '', //isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '',
			'desc'					=> isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '',
			'post_type'				=> isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : '',
			'use_taxonomy'			=> isset( $_REQUEST['use_taxonomy'] ) ? $_REQUEST['use_taxonomy'] == 'yes' : false,
			'taxonomy'				=> isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : '',
			'included'				=> isset( $_REQUEST['included'] ) ? $_REQUEST['included'] : array(),
			'term'					=> isset( $_REQUEST['term'] ) ? $_REQUEST['term'] : '',
			'loop'					=> isset( $_REQUEST['loop'] ) ? $_REQUEST['loop'] : 'default',
			'columns'				=> isset( $_REQUEST['columns'] ) ? (int)$_REQUEST['columns'] : 2,
			'see_title'				=> isset( $_REQUEST['see_title'] ) ? $_REQUEST['see_title'] == 'yes' : false,
			'see_image'				=> isset( $_REQUEST['see_image'] ) ? $_REQUEST['see_image'] == 'yes' : false,
			'image_size'			=> isset( $_REQUEST['image_size'] ) ? $_REQUEST['image_size'] : 'thumbnail',
			'see_content'			=> isset( $_REQUEST['see_content'] ) ? $_REQUEST['see_content'] == 'yes' : false,
			'see_excerpt'			=> isset( $_REQUEST['see_excerpt'] ) ? $_REQUEST['see_excerpt'] == 'yes' : false,
			'see_author'			=> isset( $_REQUEST['see_author'] ) ? $_REQUEST['see_author'] == 'yes' : false,
			'see_meta_data'			=> isset( $_REQUEST['see_meta_data'] ) ? $_REQUEST['see_meta_data'] == 'yes' : false,
			'see_meta_utilities'	=> isset( $_REQUEST['see_meta_utilities'] ) ? $_REQUEST['see_meta_utilities'] == 'yes' : false,
			'see_price'				=> isset( $_REQUEST['see_price'] ) ? $_REQUEST['see_price'] == 'yes' : false,
			'see_buy_button'		=> isset( $_REQUEST['see_buy_button'] ) ? $_REQUEST['see_buy_button'] == 'yes' : false,
			'see_first_custom_area' => isset( $_REQUEST['see_first_custom_area'] ) ? $_REQUEST['see_first_custom_area'] == 'yes' : false,
			'see_second_custom_area'=> isset( $_REQUEST['see_second_custom_area'] ) ? $_REQUEST['see_second_custom_area'] == 'yes' : false,
			'see_third_custom_area' => isset( $_REQUEST['see_third_custom_area'] ) ? $_REQUEST['see_third_custom_area'] == 'yes' : false,
		);
		update_option( 'tcp_shortcodes_data', $shortcodes_data );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Shortcode saved', 'tcp' );?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_shortcode_delete'] ) ) {
	unset( $shortcodes_data[$shortcode_id] );
	update_option( 'tcp_shortcodes_data', $shortcodes_data );
	$shortcode_id = -1;?>
	<div id="message" class="updated"><p>
		<?php _e( 'Shortcode saved', 'tcp' );?>
	</p></div><?php

}

if ( $shortcode_id == -1 ) {
	if ( is_array( $shortcodes_data ) && count( $shortcodes_data ) > 0 ) {
		$shortcode_id = array_shift( array_keys( $shortcodes_data ) );
		$shortcode_data = $shortcodes_data[$shortcode_id];
	 } else {
		 $shortcode_id = 0;
		$shortcode_data = array();
	 }
} elseif ( isset( $shortcodes_data[$shortcode_id] ) ) {
	$shortcode_data = $shortcodes_data[$shortcode_id];
} else {
	$shortcode_data = array();
}

$admin_path = 'admin.php?page=' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/admin/';
$shortcode_href = $admin_path . 'ShortCodeGenerator.php&shortcode_id=';
?>

<div class="wrap">
	<h2><?php _e( 'ShortCode Generator', 'tcp' );?></h2>
	<ul class="subsubsub">
	</ul><!-- subsubsub -->

	<div class="clear"></div>

	<div class="instances">
	<?php if ( is_array( $shortcodes_data ) && count( $shortcodes_data ) > 0 ) :
		foreach( $shortcodes_data as $id => $data ) :
			if ( $shortcode_id == $id ) : ?>
			<span><?php echo $data['id'];?></span>&nbsp;|&nbsp;
			<?php else: ?>
			<a href="<?php echo $shortcode_href, $id;?>"><?php echo $data['id'];?></a>&nbsp;|&nbsp;
			<?php endif;?>
		<?php endforeach;?>
		<?php if ( isset( $shortcodes_data[$shortcode_id] ) ) :
				$last_id = array_pop( array_keys( $shortcodes_data ) ) + 1; ?>
		<a href="<?php echo $shortcode_href, $last_id;?>"><?php _e( 'new shortcode', 'tcp' );?></a>
		<?php endif;?>
	<?php else: ?>
		<?php _e( 'No shortcodes, create one now:', 'tcp' );?>
	<?php endif; 
	$identifier				= isset( $shortcode_data['id'] ) ? $shortcode_data['id'] : '';
	$title					= ''; //isset( $shortcode_data['title'] ) ? $shortcode_data['title'] : '';
	$desc					= isset( $shortcode_data['desc'] ) ? $shortcode_data['desc'] : '';
	$post_type				= isset( $shortcode_data['post_type'] ) ? $shortcode_data['post_type'] : 'tcp_product';
	$taxonomy				= isset( $shortcode_data['taxonomy'] ) ? $shortcode_data['taxonomy'] : 'tcp_product_category';
	$use_taxonomy			= isset( $shortcode_data['use_taxonomy'] ) ? $shortcode_data['use_taxonomy'] == 'yes' : false;
	$included				= isset( $shortcode_data['included'] ) ? $shortcode_data['included'] : array();
	$term					= isset( $shortcode_data['term'] ) ? $shortcode_data['term'] : '';
	$loop					= isset( $shortcode_data['loop'] ) ? $shortcode_data['loop'] : '';
	$columns				= isset( $shortcode_data['columns'] ) ? $shortcode_data['columns'] : 2;
	$see_title				= isset( $shortcode_data['see_title'] ) ? $shortcode_data['see_title'] == 'yes' : true;
	$see_image				= isset( $shortcode_data['see_image'] ) ? $shortcode_data['see_image'] == 'yes' : false;
	$image_size				= isset( $shortcode_data['image_size'] ) ? $shortcode_data['image_size'] : 'thumbnail';
	$see_content			= isset( $shortcode_data['see_content'] ) ? $shortcode_data['see_content'] == 'yes' : false;
	$see_excerpt			= isset( $shortcode_data['see_excerpt'] ) ? $shortcode_data['see_excerpt'] == 'yes' : false;
	$see_author				= isset( $shortcode_data['see_author'] ) ? $shortcode_data['see_author'] == 'yes' : false;
	$see_meta_data			= isset( $shortcode_data['see_meta_data'] ) ? $shortcode_data['see_meta_data'] == 'yes' : false;
	$see_meta_utilities		= isset( $shortcode_data['see_meta_utilities'] ) ? $shortcode_data['see_meta_utilities'] == 'yes' : false;
	$see_price				= isset( $shortcode_data['see_price'] ) ? $shortcode_data['see_price'] == 'yes' : false;
	$see_buy_button			= isset( $shortcode_data['see_buy_button'] ) ? $shortcode_data['see_buy_button'] == 'yes' : false;
	$use_taxonomy 			= isset( $shortcode_data['use_taxonomy'] ) ? $shortcode_data['use_taxonomy'] == 'yes' : false;
	$see_first_custom_area	= isset( $shortcode_data['see_first_custom_area'] ) ? $shortcode_data['see_first_custom_area'] == 'yes' : false;
	$see_second_custom_area	= isset( $shortcode_data['see_second_custom_area'] ) ? $shortcode_data['see_second_custom_area'] == 'yes' : false;
	$see_third_custom_area	= isset( $shortcode_data['see_third_custom_area'] ) ? $shortcode_data['see_third_custom_area'] == 'yes' : false;
	if ( $use_taxonomy ) {
		$use_taxonomy_style = '';
		$included_style = 'display: none;';
	} else {
		$use_taxonomy_style = 'display: none;';
		$included_style = '';
	}?>
	</div>
	<script>
		function tcp_show_taxonomy(checked) {
			if (checked) {
				jQuery('.tcp_taxonomy_controls').show();
				jQuery('.tcp_post_included').hide();
			} else {
				jQuery('.tcp_taxonomy_controls').hide();
				jQuery('.tcp_post_included').show();
			}
		}
	</script>
	<form method="post">
		<input type="hidden" name="shortcode_id" value="<?php echo $shortcode_id;?>" />
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="id"><?php _e( 'Identifier', 'tcp' );?>:</label>
				<br/><span class="description"><?php _e( 'Don\'t use whitespace. For example use the_identifier', 'tcp' );?></span>
			</th>
			<td>
				<input type="text" name="id" id="id" value="<?php echo $identifier;?>" size="40" maxlength="255" />
				<br/><span><?php printf( __( 'Usage: [tcp_list id="%s"]', 'tcp' ), $identifier );?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="desc"><?php _e( 'Description', 'tcp' );?>:</label>
			</th>
			<td>
				<textarea name="desc" id="desc" cols="40" rows="6" maxlength="255" /><?php echo $desc;?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="post_type"><?php _e( 'Post type', 'tcp' );?>:</label>
			</th>
			<td>
				<select name="post_type" id="post_type">
				<?php foreach( get_post_types() as $post_type_item ) : ?>
					<option value="<?php echo $post_type_item;?>"<?php selected( $post_type, $post_type_item );?>><?php echo $post_type_item;?></option>
				<?php endforeach; ?>
				</select>
				<span class="description"><?php _e( 'Save to load the list of taxonomies', 'tcp' );?></span>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="use_taxonomy"><?php _e( 'Use Taxonomy', 'tcp' ); ?></label>
			</th>
			<td>
				<input type="checkbox" class="checkbox" onclick="tcp_show_taxonomy(this.checked);" id="use_taxonomy" name="use_taxonomy" value="yes" <?php checked( $use_taxonomy, true ); ?> />
			</td>
		</p>
		</tr>
		<tr valign="top" class="tcp_taxonomy_controls" style="<?php echo $use_taxonomy_style;?>">
			<th scope="row">
				<label for="taxonomy"><?php _e( 'Taxonomy', 'tcp' );?>:</label>
			</th>
			<td>
				<?php if ( strlen( $post_type ) > 0 ) : ?>
				<select name="taxonomy" id="taxonomy">
				<option value="" <?php selected( $taxonomy, '' );?>><?php _e( 'all', 'tcp' );?></option>
				<?php foreach( get_object_taxonomies( $post_type ) as $taxonomy_item ) : $tax = get_taxonomy( $taxonomy_item );?>
					<option value="<?php echo esc_attr( $taxonomy_item );?>"<?php selected( $taxonomy, $taxonomy_item );?>><?php echo esc_attr( $tax->labels->name );?></option>
				<?php endforeach;?>
				</select>
				<span class="description"><?php _e( 'Save to load the list of terms', 'tcp' );?></span>
				<?php endif;?>
			</td>
		</tr>
		<tr valign="top" class="tcp_taxonomy_controls" style="<?php echo $use_taxonomy_style;?>">
			<th scope="row">
				<label for="term"><?php _e( 'Term', 'tcp' )?>:</label>
			</th>
			<td>
				<select name="term" id="term">
				<?php if ( strlen( $taxonomy ) > 0 ) : 
					$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
					if ( is_array( $terms ) && count( $terms ) )
						foreach( $terms as $term_item ) : 
							if ( $term_item->term_id == tcp_get_default_id( $term_item->term_id, $taxonomy ) ) :?>
								<option value="<?php echo $term_item->slug;?>"<?php selected( $term, $term_item->slug );?>><?php echo esc_attr( $term_item->name );?></option>
							<?php endif;
						endforeach;
				endif;?>
				</select>
			</td>
		</tr>
		<tr valign="top" class="tcp_post_included" style="<?php echo $included_style;?>">
			<th scope="row">
				<label for="included"><?php _e( 'Included', 'tcp' )?>:</label>
			</th>
			<td>
				<select name="included[]" id="included" multiple="true" size="8" style="height: auto">
					<option value="" <?php selected( $included, '' ); ?>><?php _e( 'all', 'tcp' );?></option>
				<?php
				$args = array(
					'post_type'	=> $post_type,
				);
				if ( $post_type == 'tcp_product' ) {
					$args['meta_key'] = 'tcp_is_visible';
					$args['meta_value'] = true;
				}
				$query = new WP_query( $args );
				if ( $query->have_posts() ) while ( $query->have_posts() ): $query->the_post();?>
					<option value="<?php the_ID();?>"<?php tcp_selected_multiple( $included, get_the_ID() ); ?>><?php the_title();?></option>
				<?php endwhile;
				wp_reset_postdata(); wp_reset_query();?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="loop"><?php _e( 'Loop', 'tcp' );?>:</label>
				<br/>(<?php _e( 'theme', 'tcp' );?>:&nbsp;<?php echo get_template();?>)
			</th>
			<td>
				<select name="loop" id="loop">
					<option value="" <?php selected( $loop, '' );?>"><?_e( 'default', 'tcp' );?></option>
				<?php
				$files = array();
				$folder = STYLESHEETPATH;
				if ( $handle = opendir( $folder ) ) while ( false !== ( $file = readdir( $handle ) ) ) :
					if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 ) : ?>
						<option value="<?php echo $folder . '/' . $file;?>" <?php selected( $loop, $folder . '/' . $file );?>"><?echo $file; ?></option>
					<?php 
						$files[] = $file;
					endif;?>
				<?php endwhile; closedir( $handle );
			
				$folder = get_template_directory();
				if ( STYLESHEETPATH != $folder )
					if ( $handle = opendir($folder ) ) while ( false !== ( $file = readdir( $handle ) ) ) :
						if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 && ! in_array( $file, $files ) ) : ?>
							<option value="<?php echo $folder . '/' . $file;?>" <?php selected( $loop, $folder . '/' . $file );?>">[<?php _e( 'parent', 'tcp' );?>] <?echo $file; ?></option>
						<?php endif;?>
					<?php endwhile; closedir( $handle );?>
				</select>
			</td>
		</tr>
		</tbody>
		</table>
		<p>
			<input type="button" onclick="jQuery('#advanced').toggle();" value="<?php _e( 'show/hide advanced options', 'tcp' );?>" class="button-secondary" />
		</p>
	<div id="advanced" style="display:none;">
		<p>
			<label for="columns"><?php _e( 'Nº columns', 'tcp' );?>:</label>
			<input id="columns" name="columns" type="text" value="<?php echo $columns;?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_title" name="see_title" value="yes" <?php checked( $see_title );?> />
			<label for="see_title"><?php _e( 'Show title', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_image" name="see_image" value="yes" <?php checked( $see_image );?> />
			<label for="see_image"><?php _e( 'Show image', 'tcp' );?></label>
		</p>
		<p>
			<label for="image_size"><?php _e( 'Image size', 'tcp' );?></label>
			<select id="image_size" name="image_size">
			<?php $imageSizes = get_intermediate_image_sizes();
			foreach($imageSizes as $imageSize) : ?>
				<option value="<?php echo $imageSize;?>" <?php selected( $imageSize, $image_size);?>><?php echo $imageSize;?></option>
			<?php endforeach;?>
			?>
			</select>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_content" name="see_content" value="yes" <?php checked( $see_content );?> />
			<label for="see_content"><?php _e( 'Show content', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_excerpt" name="see_excerpt" value="yes" <?php checked( $see_excerpt );?> />
			<label for="see_excerpt"><?php _e( 'Show excerpt', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_author" name="see_author" value="yes" <?php checked( $see_author );?> />
			<label for="see_author"><?php _e( 'Show author', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_meta_data" name="see_meta_data" value="yes" <?php checked( $see_meta_data );?> />
			<label for="see_meta_data"><?php _e( 'Show tags', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_meta_utilities" name="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities );?> />
			<label for="see_meta_utilities"><?php _e( 'Show utilities', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_price" name="see_price" value="yes" <?php checked( $see_price );?> />
			<label for="see_price"><?php _e( 'Show price', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_buy_button" name="see_buy_button" value="yes" <?php checked( $see_buy_button );?> />
			<label for="see_buy_button"><?php _e( 'Show buy button', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_first_custom_area" name="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area );?> />
			<label for="see_first_custom_area"><?php _e( 'Show first custom area', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_second_custom_area" name="see_first_custom_area" value="yes" <?php checked( $see_second_custom_area );?> />
			<label for="see_second_custom_area"><?php _e( 'Show second custom area', 'tcp' );?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_third_custom_area" name="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area );?> />
			<label for="see_third_custom_area"><?php _e( 'Show third custom area', 'tcp' );?></label>
		</p>
	</div>		
		<p>
			<input name="tcp_shortcode_save" value="<?php _e( 'save', 'tcp' );?>" type="submit" class="button-primary" />
			<?php if ( isset( $shortcodes_data[$shortcode_id] ) ) : ?><input name="tcp_shortcode_delete" value="<?php _e( 'delete', 'tcp' );?>" type="submit" class="button-secondary" /><?php endif;?>
		</p>
	</form>
</div>
