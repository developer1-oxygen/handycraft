<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

// **********************************************************************//
// Gradient
// **********************************************************************//

if( ! class_exists( 'ReduxFramework_woodmart_gradient' ) && class_exists( 'ReduxFramework' ) ) {

    class ReduxFramework_woodmart_gradient extends ReduxFramework {
    
        function __construct( $field = array(), $value = '', $parent = null ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        }

        public function render() {
            echo woodmart_get_gradient_field( $this->field['name'], $this->value );
        }
    
        public function output() {
            if ( ! empty( $this->value ) ) {
                $style = '';
                if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                    $css = Redux_Functions::parseCSS( $this->field['output'], $style, woodmart_get_gradient_css( $this->value ) );
                    $this->parent->outputCSS .= $css;
                }
            }
        }         
    }
}