<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* ------------------------------------------------------------------------------------------------
* Buttons shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'woodmart_shortcode_button' ) ) {
	function woodmart_shortcode_button( $atts, $popup = false ) {
		extract( shortcode_atts( array(
			'title' 	 => 'GO',
			'link' 	 	 => '',
			'color' 	 => 'default',
			'style'   	 => 'default',
			'shape'   	 => 'rectangle',
			'size' 		 => 'default',
			'align' 	 => 'center',
			'button_inline' => 'no',
			'full_width' => 'no',

			'bg_color' => '',
			'bg_color_hover' => '',
			'color_scheme' => 'light',
			'color_scheme_hover' => 'light',
			'woodmart_css_id' => '',

			'css_animation' => 'none',
			'el_class' 	 => '',
		), $atts) );

		$attributes = woodmart_get_link_attributes( $link, $popup );

		$btn_class = 'btn';

		if ( ! $woodmart_css_id ) $woodmart_css_id = uniqid();
		$id = 'wd-' . $woodmart_css_id;

		$wrap_class = 'woodmart-button-wrapper';
		$wrap_class .= woodmart_get_css_animation( $css_animation );

		if ( $bg_color || $bg_color_hover ) {
			$btn_class .= ' btn-scheme-' . $color_scheme;
			$btn_class .= ' btn-scheme-hover-' . $color_scheme_hover;
		} else {
			$btn_class .= ' btn-color-' . $color;
		}
		$btn_class .= ' btn-style-' . $style;
		$btn_class .= ' btn-shape-' . $shape;
		$btn_class .= ' btn-size-' . $size;
		if( $full_width == 'yes' ) $btn_class .= ' btn-full-width';

		$wrap_class .= ' text-' . $align;
		if( $button_inline == 'yes' ) $wrap_class .= ' inline-element';

		if( $el_class != '' ) $btn_class .= ' ' . $el_class;

		$attributes .= ' class="' . $btn_class . '"';

		$output = '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( $wrap_class ) . '"><a ' . $attributes . '>' . esc_html ( $title ) . '</a>';

		if ( is_array( $bg_color ) ) $bg_color = 'rgba(' . $bg_color['r'] . ', ' . $bg_color['g'] . ', ' . $bg_color['b'] . ',' . $bg_color['a'] . ')';
		if ( is_array( $bg_color_hover ) ) $bg_color_hover = 'rgba(' . $bg_color_hover['r'] . ', ' . $bg_color_hover['g'] . ', ' . $bg_color_hover['b'] . ',' . $bg_color_hover['a'] . ')';
		

		if ( $bg_color && ! woodmart_is_css_encode( $bg_color ) || $bg_color_hover && ! woodmart_is_css_encode( $bg_color_hover ) ) {
			$css = '';
			// Custom Color
			$css .= '#' . $id . ' a {';
				if( $style == 'bordered' || $style == 'link') {
					$css .= 'border-color:' . $bg_color . ';';
				} else {
					$css .= 'background-color:' . $bg_color . ';';
				}
			$css .= '}';

			$css .= '#' . $id . ' a:hover {';
				if( $style == 'bordered') {
					$css .= 'border-color:' . $bg_color_hover . ';';
					$css .= 'background-color:' . $bg_color_hover . ';';
				} else if( $style == 'link' ) {
					$css .= 'border-color:' . $bg_color_hover . ';';
				} else {
					$css .= 'background-color:' . $bg_color_hover . ';';
				}
			$css .= '}';

			wp_add_inline_style( 'woodmart-inline-css', $css );
		}

		$output .= '</div>';

		return $output;

	}
}
