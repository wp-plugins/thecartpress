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

$tcp_template_classes = array(); //to store the template classes

function tcp_add_template_class( $template_class, $description = '' ) {
	global $tcp_template_classes;
	$tcp_template_classes[$template_class] = $description;
}

function tcp_remove_template_class( $template_class ) {
	global $tcp_template_classes;
	unset( $tcp_template_classes[$template_class] );
}

function tcp_get_templates_classes() {
	global $tcp_template_classes;
	return array_keys( $tcp_template_classes );
}

function tcp_do_template( $template_class, $echo = true ) {
	$args = array(
		'post_type'			=> TemplateCustomPostType::$TEMPLATE,
		'posts_per_page'	=> -1,
		'suppress_filters'	=> true,
		'meta_query'		=> array(
			array(
				'key'			=> 'tcp_template_class',
				'value'			=> $template_class,
				'compare	'	=> '='
			)
		)
	);
	$query = new WP_Query( $args );
	$html = '';
	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id = tcp_get_current_id( get_the_ID(), TemplateCustomPostType::$TEMPLATE );
		$post = get_post( $post_id );
		//$html .= apply_filters( 'the_content', get_the_content() );
		$html .= apply_filters( 'the_content', $post->post_content );
	}
	wp_reset_postdata();
	wp_reset_query();
	if ( $echo )
		echo $html;
	else
		return $html;
}
?>