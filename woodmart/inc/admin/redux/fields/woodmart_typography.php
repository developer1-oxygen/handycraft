<?php if ( ! defined('WOODMART_THEME_DIR')) exit('No direct script access allowed');

// **********************************************************************//
// Advanced typography for multiple selectors
// **********************************************************************//

if ( ! class_exists( 'ReduxFramework_woodmart_typography' ) && class_exists( 'ReduxFramework' ) ) {

    class ReduxFramework_woodmart_typography extends ReduxFramework {

        private $_default_value;

        private $std_fonts = array(
            "Arial, Helvetica, sans-serif"                         => "Arial, Helvetica, sans-serif",
            "'Arial Black', Gadget, sans-serif"                    => "'Arial Black', Gadget, sans-serif",
            "'Bookman Old Style', serif"                           => "'Bookman Old Style', serif",
            "'Comic Sans MS', cursive"                             => "'Comic Sans MS', cursive",
            "Courier, monospace"                                   => "Courier, monospace",
            "Garamond, serif"                                      => "Garamond, serif",
            "Georgia, serif"                                       => "Georgia, serif",
            "Impact, Charcoal, sans-serif"                         => "Impact, Charcoal, sans-serif",
            "'Lucida Console', Monaco, monospace"                  => "'Lucida Console', Monaco, monospace",
            "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
            "'MS Sans Serif', Geneva, sans-serif"                  => "'MS Sans Serif', Geneva, sans-serif",
            "'MS Serif', 'New York', sans-serif"                   => "'MS Serif', 'New York', sans-serif",
            "'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
            "Tahoma,Geneva, sans-serif"                            => "Tahoma, Geneva, sans-serif",
            "'Times New Roman', Times,serif"                       => "'Times New Roman', Times, serif",
            "'Trebuchet MS', Helvetica, sans-serif"                => "'Trebuchet MS', Helvetica, sans-serif",
            "Verdana, Geneva, sans-serif"                          => "Verdana, Geneva, sans-serif",
        );

        function __construct( $field = array(), $value = array(), $parent = null ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;

            if( isset( $this->value['{{index}}'] ) ) unset($this->value['{{index}}']);

            $this->_default_value = array(
                'selector' => array(),
                'google' => false,
                'custom' => false,
                'custom-selector' => '',
                'font-family' => '',
                'font-weight' => '',
                'font-variant' => '',
                'font-style' => '',
                'font-size' => '',
                'line-height' => '',
                'color' => '',
                'font-subset' => '',
                'color' => '',
                'tablet' => array(
                    'font-size' => '',
                    'line-height' => '',
                ),
                'mobile' => array(
                    'font-size' => '',
                    'line-height' => '',
                ),
                'hover' => array(
                    'color' => '',
                )
            );

            $this->_selectors = woodmart_get_config('typography-selectors');
        }

        public function render() {
            // ar($this->value);
            // get last index from the array
            $key = 0;
            if( is_array( $this->value ) ) {
                end($this->value);         
                $key = key($this->value);
            }  
                   
            echo '<div id="' . $this->field['id'] . '" class="woodmart-advanced-typography-field" data-id="' . $this->field['id'] . '" data-key="' . $key . '">';

            echo '<div class="woodmart-atf-sections">';

            if( is_array( $this->value ) && count($this->value) > 0 ) {
                foreach ($this->value as $index => $value) {
                    $this->render_section($index);
                }
            } else {
                $this->render_section(0);
            }

            echo '</div>';

            $this->_section_template( false );

            echo '<div class="woodmart-atf-btn-add">Add rule</div>';

            echo '</div>';
        }

        public function render_section( $index ) {
            $value = $this->_default_value;
            if( isset( $this->value[$index] ) ) {
                $value = wp_parse_args( $this->value[$index], $value );
            }

            // Is selected font a Google font
            $google = '0';
            if ( isset( $this->parent->fonts['google'][ $value['font-family'] ] ) ) {
                $google = '1';
            }

            $this->_section_template( $index, array(
                'google' => $google,
                'value' => $value,
            ) );

        }

        private function _section_template( $i, $data = array() ) {

            $index = ( $i === false ) ? '{{index}}' : $i;

            extract( wp_parse_args( $data, array(
                'google' => '0',
                'value' => $this->_default_value
            ) ) );

            echo '<div class="woodmart-atf-section ' . ( ( $i === false ) ? 'woodmart-atf-template hide' : '' ) . '" data-id="' . $this->field['id'] . '-' . $index . '">';
                echo '<input type="hidden" class="woodmart-atf-custom-input" name="' . $this->_get_field_name($index, 'custom') .'" value="' . $value['custom'] . '"  />';

                echo '<select class="woodmart-atf-selector" name="' . $this->_get_field_name($index, 'selector][') .'" multiple="multiple" data-placeholder="' . __( 'Assigned to elements', 'woodmart' ) . '">';
                    $group = false;
                    foreach ($this->_selectors as $id => $selector) {
                        if( ! is_array($selector) ) continue;

                        if( ! isset( $selector['selector'] ) ) {
                            if( $group ) echo '</optgroup>';
                            echo '<optgroup label="' . $selector['title'] . '">';
                            $group = true;
                            continue;
                        }

                        $selected = in_array( $id, $value['selector'] ) ? ' selected="selected" ' : '';  
                        echo '<option value="' . $id . '" ' . $selected . '>';
							echo wp_kses( $selector['title'], woodmart_get_allowed_html() );
                        echo '</option>';
                        
                    }
                    if( $group ) echo '</optgroup>';
                echo '</select>';

                echo '<input type="text" placeholder="For ex.: .my-custom-class" class="woodmart-atf-custom-selector' . ( ( ! $value['custom'] ) ? ' hide' : '' ) . '" name="' . $this->_get_field_name($index, 'custom-selector') .'" value="' . $value['custom-selector'] . '"  />';

                echo '<div class="woodmart-atf-font-container">';
                    echo '<input type="hidden" class="woodmart-atf-google-input" name="' . $this->_get_field_name($index, 'google') .'" value="' . $google . '">';
                    
                    echo '<input type="hidden" class="woodmart-atf-family-input" name="' . $this->_get_field_name($index, 'font-family') .'" value="' . $value['font-family'] . '"  />';

                    echo '<div data-placeholder="' . __( 'Font family', 'woodmart' ) . '" class="woodmart-atf-family select2-container" placeholder="' . $value['font-family'] . '" data-value="' . $value['font-family'] . '"></div>';


                    echo '<div class="woodmart-atf-style-container" original-title="' . __( 'Font style', 'woodmart' ) . '">';

                        $style = $value['font-weight'] . $value['font-style'];

                        echo '<input type="hidden" class="woodmart-atf-weight-input" name="' . $this->_get_field_name($index, 'font-weight') .'" value="' . $value['font-weight'] . '"  /> ';

                        echo '<input type="hidden" class="woodmart-atf-style-input" name="' . $this->_get_field_name($index, 'font-style') .'"  /> ';

                        echo '<select data-placeholder="' . __( 'Style', 'woodmart' ) . '" class="woodmart-atf-style" original-title="' . __( 'Font style', 'woodmart' ) . '"  data-value="' . $style . '">';
                        echo '</select>';

                    echo '</div>';


                    echo '<div class="select_wrapper woodmart-atf-subsets-container" original-title="' . __( 'Font subsets', 'woodmart' ) . '">';

                        echo '<input type="hidden" class="woodmart-atf-subset-input" name="' . $this->_get_field_name($index, 'font-subset') .'"  /> ';

                        echo '<select data-placeholder="' . __( 'Subset', 'woodmart' ) . '" class="woodmart-atf-subset" original-title="' . __( 'Font subset', 'woodmart' ) . '"  data-value="' . $value['font-subset'] . '">';
                        echo '</select>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="woodmart-atf-size-container woodmart-atf-responsive-controls">';
                    echo '<div class="woodmart-atf-size-point woodmart-atf-control-desktop">';
                        echo '<label>Font size</label>';
                        echo '<div class="input-append">';
                            echo '<input type="number" name="' . $this->_get_field_name($index, 'font-size') . '" value="' . $value['font-size'] . '"  /><span class="add-on">px</span>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="woodmart-atf-responsive-opener" title="Responsive controls"></div>';
                    echo '<div class="woodmart-atf-size-point woodmart-atf-control-tablet ' . ( ( ! empty( $value['tablet']['font-size'] ) ) ? 'show' : 'hide') . '">';
                        echo '<label>Tablet</label>';
                        echo '<div class="input-append">';
                            echo '<input type="number" name="' . $this->_get_field_name($index, 'font-size', 'tablet') . '" value="' . $value['tablet']['font-size'] . '"  /><span class="add-on">px</span>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="woodmart-atf-size-point woodmart-atf-control-mobile ' . ( ( ! empty( $value['tablet']['font-size'] ) ) ? 'show' : 'hide') . '">';
                        echo '<label>Mobile</label>';
                        echo '<div class="input-append">';
                            echo '<input type="number" name="' . $this->_get_field_name($index, 'font-size', 'mobile') . '" value="' . $value['mobile']['font-size'] . '"  /><span class="add-on">px</span>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="woodmart-atf-height-container woodmart-atf-responsive-controls">';
                    echo '<div class="woodmart-atf-height-point woodmart-atf-control-desktop">';
                        echo '<label>Line height</label>';
                        echo '<div class="input-append">';
                            echo '<input type="number" name="' . $this->_get_field_name($index, 'line-height') .'" value="' . $value['line-height'] . '"/><span class="add-on">px</span>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="woodmart-atf-responsive-opener" title="Responsive controls"></div>';
                    echo '<div class="woodmart-atf-height-point woodmart-atf-control-tablet ' . ( ( ! empty( $value['tablet']['line-height'] ) ) ? 'show' : 'hide') . '">';
                        echo '<label>Tablet</label>';
                        echo '<div class="input-append">';
                            echo '<input type="number" name="' . $this->_get_field_name($index, 'line-height', 'tablet') .'" value="' . $value['tablet']['line-height'] . '"/><span class="add-on">px</span>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="woodmart-atf-height-point woodmart-atf-control-mobile ' . ( ( ! empty( $value['tablet']['line-height'] ) ) ? 'show' : 'hide') . '">';
                        echo '<label>Mobile</label>';
                        echo '<div class="input-append">';
                            echo '<input type="number" name="' . $this->_get_field_name($index, 'line-height', 'mobile') .'" value="' . $value['mobile']['line-height'] . '"/><span class="add-on">px</span>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="woodmart-atf-color-container">';
                    echo '<div class="woodmart-atf-color-point">';
                        echo '<label>Color</label>';
                        echo '<input type="text" placeholder="' . __( 'Color', 'woodmart' ) . '" name="' . $this->_get_field_name($index, 'color') .'" value="' . $value['color'] . '" class="woodmart-atf-color" />';
                    echo '</div>';
                    echo '<div class="woodmart-atf-color-point">';
                        echo '<label>Color on hover</label>';
                        echo '<input type="text" placeholder="' . __( 'Color', 'woodmart' ) . '" name="' . $this->_get_field_name($index, 'color', 'hover') .'" value="' . $value['hover']['color'] . '" class="woodmart-atf-color-hover" />';
                    echo '</div>';
                echo '</div>';

                echo '<p class="woodmart-atf-preview hide" ' . 'style="">1 2 3 4 5 6 7 8 9 0 A B C D E F G H I J K L M N O P Q R S T U V W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z</p>';

                echo '<div class="woodmart-atf-btn-remove">Remove</div>';
            echo '</div>';
        }

        public function output() {
            $value = $this->value;

            // ar($value);

            $style = '';

            foreach ($value as $i => $typography) {
                if( ! isset( $typography['selector'] ) ) continue;
                $selector = $this->_combine_selectors($typography['selector'], false, $typography['custom-selector']);
                $hover_selector = $this->_combine_selectors($typography['selector'], 'hover', $typography['custom-selector']);
                $style .= $this->_generate_css_code( $selector, $typography );

                if( isset( $typography['tablet'] ) && is_array( $typography['tablet'] ) ) {
                    $css_tablet = $this->_generate_css_code( $selector, $typography['tablet'] );
                    $style .= $this->_get_css_media_query('@media (max-width: 1024px)', $css_tablet ); 
                }

                if( isset( $typography['mobile'] ) && is_array( $typography['mobile'] ) ) {
                    $css_mobile = $this->_generate_css_code( $selector, $typography['mobile'] );
                    $style .= $this->_get_css_media_query('@media (max-width: 767px)', $css_mobile ); 
                }

                if( isset( $typography['hover'] ) && is_array( $typography['hover'] ) ) {
                    $style .= $this->_generate_css_code( $hover_selector, $typography['hover'] );
                }

                $this->_loadGoogleFont($typography);

            }

			if ( isset( $this->field['output'] ) ) {
            	$this->parent->outputCSS .= $style;
			} else {
				$this->parent->compilerCSS .= $style;
			}
        }

        private function _loadGoogleFont( $font ) {
            $all_styles = true;
            
            if ( empty( $font['font-family'] ) || ! filter_var( $font['google'], FILTER_VALIDATE_BOOLEAN ) ) return;

            // Added standard font matching check to avoid output to Google fonts call - kp
            // If no custom font array was supplied, the load it with default
            // standard fonts.
            if ( empty( $this->field['fonts'] ) ) {
                $this->field['fonts'] = $this->std_fonts;
            }

            // Ensure the fonts array is NOT empty
            if ( empty( $this->field['fonts'] ) ) return;

            //Make the font keys in the array lowercase, for case-insensitive matching
            $lcFonts = array_change_key_case( $this->field['fonts'] );

            // Rebuild font array with all keys stripped of spaces
            $arr = array();
            foreach ( $lcFonts as $key => $value ) {
                $key         = str_replace( ', ', ',', $key );
                $arr[ $key ] = $value;
            }

            $lcFonts = $arr;

            unset( $arr );

            // lowercase chosen font for matching purposes
            $lcFont = strtolower( $font['font-family'] );

            // Remove spaces after commas in chosen font for mathcing purposes.
            $lcFont = str_replace( ', ', ',', $lcFont );

            // If the lower cased passed font-family is NOT found in the standard font array
            // Then it's a Google font, so process it for output.
            if ( array_key_exists( $lcFont, $lcFonts ) ) return;

            $family = $font['font-family'];

            // Strip out spaces in font names and replace with with plus signs
            // TODO?: This method doesn't respect spaces after commas, hence the reason
            // for the std_font array keys having no spaces after commas.  This could be
            // fixed with RegEx in the future.
            $font['font-family'] = str_replace( ' ', '+', $font['font-family'] );

            // Push data to parent typography variable.
            if ( empty( $this->parent->typography[ $font['font-family'] ] ) ) {
                $this->parent->typography[ $font['font-family'] ] = array();
            }

            if ( $all_styles && isset( $this->parent->googleArray ) && ! empty( $this->parent->googleArray ) && isset( $this->parent->googleArray[ $family ] ) ) {
                $font['font-options'] = $this->parent->googleArray[ $family ];
            }

            if ( isset( $font['font-options'] ) && ! empty( $font['font-options'] ) && $all_styles ) {
                if ( isset( $font['font-options'] ) && ! empty( $font['font-options']['variants'] ) ) {
                    if ( ! isset( $this->parent->typography[ $font['font-family'] ]['all-styles'] ) || empty( $this->parent->typography[ $font['font-family'] ]['all-styles'] ) ) {
                        $this->parent->typography[ $font['font-family'] ]['all-styles'] = array();
                        foreach ( $font['font-options']['variants'] as $variant ) {
                            $this->parent->typography[ $font['font-family'] ]['all-styles'][] = $variant['id'];
                        }
                    }
                }
            }

            if ( ! empty( $font['font-weight'] ) ) {
                if ( empty( $this->parent->typography[ $font['font-family'] ]['font-weight'] ) || ! in_array( $font['font-weight'], $this->parent->typography[ $font['font-family'] ]['font-weight'] ) ) {
                    $style = $font['font-weight'];
                }

                if ( ! empty( $font['font-style'] ) ) {
                    $style .= $font['font-style'];
                }

                if ( empty( $this->parent->typography[ $font['font-family'] ]['font-style'] ) || ! in_array( $style, $this->parent->typography[ $font['font-family'] ]['font-style'] ) ) {
                    $this->parent->typography[ $font['font-family'] ]['font-style'][] = $style;
                }
            }

            if ( ! empty( $font['font-subset'] ) ) {
                if ( empty( $this->parent->typography[ $font['font-family'] ]['subset'] ) || ! in_array( $font['font-subset'], $this->parent->typography[ $font['font-family'] ]['subset'] ) ) {
                    $this->parent->typography[ $font['font-family'] ]['subset'][] = $font['font-subset'];
                }
            }
            
        }

        private function _get_css_media_query( $query, $css  ) {
            if( empty( $css ) ) return ''; 
            $code = $query . '{';
            $code .= $css;
            $code .= '}';
            return $code;
        }

        private function _generate_css_code( $selector, $rules ) {
            $css_rules = $this->_get_css_rule( 'font-family', $rules );
            $css_rules .= $this->_get_css_rule( 'font-weight', $rules );
            $css_rules .= $this->_get_css_rule( 'font-style', $rules );
            $css_rules .= $this->_get_css_rule( 'font-size', $rules );
            $css_rules .= $this->_get_css_rule( 'line-height', $rules );
            $css_rules .= $this->_get_css_rule( 'color', $rules );

            if( empty( $css_rules ) ) return ''; 
            $css = $selector;
            $css .= '{';
            $css .= $css_rules;
            $css .= '}';
            return $css;
        }

        private function _get_css_rule( $rule, $rules_array ) {
            if( ! isset( $rules_array[$rule] )  || empty( $rules_array[$rule] ) ) return '';
            $suffix = '';
            if( in_array( $rule, array('font-size', 'line-height') ) ) $suffix = 'px';
            return $rule . ': ' . $rules_array[$rule] . $suffix . ';';
        }

        private function _get_field_name( $index, $name, $state = false ) {
            $field_name = $this->field['name'] . $this->field['name_suffix'] . '[' . $index . ']';
            if( $state ) $field_name .= '[' . $state . ']';
            $field_name .= '[' . $name . ']';
            return $field_name;
        }

        private function _combine_selectors( $selectorIDs, $state = false, $custom = false ) {
            if( ! is_array( $selectorIDs ) ) return $selectorIDs;
            $delim = ', ';
			$selector = array();
			$string = '';
			$last_element = end($selectorIDs);

            if( $state ) {
                $delim = ':' . $state . ', ';
            }

            foreach ($selectorIDs as $i => $id) {
                if( ! isset( $this->_selectors[$id] ) ) continue;
                $current_selector = $this->_selectors[$id]['selector'];
                $current_delimeter = $delim;

                // hover different selector
                if( $state == 'hover' && isset( $this->_selectors[$id]['selector-hover'] ) ) {
                    $current_selector = $this->_selectors[$id]['selector-hover'];
                    $current_delimeter = ', ';
                }

                if($id == 'custom') $current_selector = $custom;

                $multiple = explode(', ', $current_selector);

                if ( count( $multiple ) > 0 ) {
					$string .= implode($current_delimeter, $multiple);

					if ($id !== $last_element) {
						$string .= $current_delimeter;
					}
				}
				
            }

            if( $state && $current_delimeter == $delim ) {
                $string .= ':' . $state;
            }

            // ar($string);

            return $string;
        } 

    }
}
