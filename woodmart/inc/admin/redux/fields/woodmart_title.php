<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

// **********************************************************************//
// Title
// **********************************************************************//

if( ! class_exists( 'ReduxFramework_woodmart_title' ) && class_exists( 'ReduxFramework' ) ) {

    class ReduxFramework_woodmart_title extends ReduxFramework {
    
        function __construct( $field = array(), $value = '', $parent = null ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        }

        public function render() {
            echo '</td></tr></table>';

            echo '<div class="woodmart-settings-title">';
                if ( isset( $this->field['wood-title'] ) && $this->field['wood-title'] ) {
                    echo '<h4 class="woodmart-title">' . esc_html( $this->field['wood-title'] ) . '</h4>';
                }
                if ( isset( $this->field['wood-desc'] ) && $this->field['wood-desc'] ) {
                    echo '<p class="woodmart-title-desc">' . esc_html( $this->field['wood-desc'] ) . '</p>';
                }
            echo '</dev>';

            echo '</div><table class="form-table no-border" style="margin-top: 0;"><tbody><tr style="border-bottom:0; display:none;"><th style="padding-top:0;"></th><td style="padding-top:0;">';
        }
    }
}