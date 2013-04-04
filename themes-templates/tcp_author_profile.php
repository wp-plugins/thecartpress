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

global $wpmu_version;
$redirect = get_permalink(); ?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="avatar" id="tcp_avatar" style="vertical-align: top;">
			<?php echo get_avatar( $current_user->ID, $size = '100' );  ?>
		</td>
		<td id="tcp_profile_info" style="vertical-align: top;">
			<div class="tcp_profile_name">
			<?php echo $current_user->display_name; ?> (<?php echo tcp_get_current_user_role_title( $current_user ); ?>)
			</div>
			<div class="tcp_last_login">
			<?php printf( __( 'Last login: %s', 'tcp' ), tcp_get_the_last_login( $current_user->ID ) ); ?>
			</div>
			<div class="tcp_profile_description">
			<?php $current_user->description; ?>
			</div>
			<?php if ( $user_level > 8 ) : ?>
				<?php if ( function_exists( 'bp_loggedin_user_link' ) ) : ?>
					<a href="<?php bp_loggedin_user_link(); ?>"><?php echo strtolower( __( 'Profile' ) ); ?></a>
				<?php else : ?>
					<br/>
					<a href="<?php bloginfo('wpurl') ?>/wp-admin/profile.php"><?php echo strtolower( __( 'Profile' ) ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
			<br />
			<a id="wp-logout" href="<?php echo wp_logout_url( $redirect ) ?>"><?php echo strtolower( __( 'Log Out' ) ); ?></a>
			<?php if ( ! empty( $wpmu_version ) || $user_level > 8 ) : ?>
				<br />
				<a href="<?php bloginfo( 'wpurl' ); ?>/wp-admin/"><?php _e( 'blog admin', 'tcp'); ?></a>
			<?php endif; ?>
		</td>
	</tr>
</table>

