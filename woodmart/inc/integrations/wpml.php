<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * ------------------------------------------------------------------------------------------------
 * WPML Compatibility
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'woodmart_wpml_compatibility' ) ) {
	function woodmart_wpml_compatibility( $ajax_actions ) {
	   $ajax_actions[] = 'woodmart_ajax_add_to_cart';
	   $ajax_actions[] = 'woodmart_quick_view';
	   return $ajax_actions;
	}
	add_filter( 'wcml_multi_currency_ajax_actions', 'woodmart_wpml_compatibility', 10, 1 );
}
