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

class UIImprovements {

	function tcp_the_currency( $currency ) {
		if ( $currency == 'EUR' ) return '&euro;';
		elseif ( $currency == 'CHF' ) return 'SFr.';
		elseif ( $currency == 'GBP' ) return '&pound;';
		elseif ( $currency == 'USD' || $currency == 'AUD' || $currency == 'CAD' || $currency == 'HKD' || $currency == 'SGD' ) return '$';
		elseif ( $currency == 'JPY' ) return '&yen;';
		elseif ( $currency == 'IRR' ) return 'ریال';
		elseif ( $currency == 'RUB' ) return 'Ƹ';
		elseif ( $currency == 'ZAR' ) return 'R';
		elseif ( $currency == 'VEB' ) return 'BsF';
		else return $currency;
	}

	function twentyten_credits() { ?>
		<a href="http://thecartpress.com/" title="<?php esc_attr_e( 'eCommerce platform', 'tcp' ); ?>" rel="generator"><?php printf( __( 'Powered by %s.', 'tcp' ), 'TheCartPress' ); ?></a><?php
	}

	function admin_footer_text( $content ) {
		$pos = strrpos( $content, '</a>.' ) + strlen( '</a>' );
		$content = substr( $content, 0, $pos ) . ' and <a href="http://thecartpress.com">TheCartPress</a>' . substr( $content, $pos );
		return $content;
	}

	function wp_meta() {
		echo '<li class="tcp_meta"><a href="http://thecartpress.com" title="', __( 'Powered by TheCartPress, eCommerce platform for WordPress', 'tcp' ), '">TheCartPress.com</a></li>';
	}

	function theCartPressRSSDashboardWidget() {
		wp_widget_rss_output( 'http://thecartpress.com/feed', array( 'items' => 5, 'show_author' => 1, 'show_date' => 1, 'show_summary' => 0 ) );
	}

	function wp_dashboard_setup() {
		wp_add_dashboard_widget( 'tcp_rss_widget', __( 'TheCartPress blog', 'tcp' ), array( $this, 'theCartPressRSSDashboardWidget' ) );
	}

	function post_class( $classes, $class, $post_id ) {
		//if ( tcp_is_saleable( $post_id ) ) $classes[] = 'tcp_hentry';
		return $classes;
	}

	function __construct() {
		add_filter( 'tcp_the_currency', array( $this, 'tcp_the_currency' ) );
		if ( is_admin() ) {
			add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
		} else {
			add_action( 'twentyten_credits', array( $this, 'twentyten_credits' ) );
			add_action( 'twentyeleven_credits', array( $this, 'twentyten_credits' ) );
			add_action( 'wp_meta', array( $this, 'wp_meta' ) );
			add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
		}
	}
}

new UIImprovements();
?>
