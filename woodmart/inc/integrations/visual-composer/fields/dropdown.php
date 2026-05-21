<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* Woodmart dropdown param
*/
if ( ! function_exists( 'woodmart_get_dropdown_param' ) ) {
	function woodmart_get_dropdown_param( $settings, $value ) {
        $output = '<select name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-input wpb-select ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '">';
            if ( ! empty( $settings['value'] ) ) {
                foreach ( $settings['value'] as $label => $data ) {
                    $style_value = isset( $settings['style'][ $data ] ) ? $settings['style'][ $data ] : '';

                    if ( is_array( $style_value ) ) {
                        if ( isset( $style_value['background-color'] ) && is_string( $style_value['background-color'] ) ) {
                            $style_value = $style_value['background-color'];
                        } else {
                            $style_value = '';
                        }
                    }

                    $color = ( function_exists( 'wc_light_or_dark' ) && is_string( $style_value ) && '' !== $style_value ) ? wc_light_or_dark( $style_value ) : '';
                    $selected = ( $value && $value == $data ) ? ' selected="selected"' : '';
                    $style = $style_value ? 'background-color:' . $style_value . ';color:' . $color . ';' : '';

                    $output .= '<option style="' . esc_attr( $style ) . '" class="' . esc_attr( $data ) . '" value="' . esc_attr( $data ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
                }
            }
        $output .= '</select>';

	    return $output;
    }
    
}
