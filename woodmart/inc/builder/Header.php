<?php if ( ! defined('WOODMART_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Class to handle header structure. Save/get to/from the database.
 * ------------------------------------------------------------------------------------------------
 */

if( ! class_exists( 'WOODMART_HB_Header' ) ) {
	class WOODMART_HB_Header {

		private $_elements;

		private $_id = 'none';

		private $_name = 'none';

		private $_structure;

		private $_settings;
		
		private $_header_options = array();
		
		/**
		 * Header elements css.
		 *
		 * @var array
		 */
		private $elements_css;

		private $_structure_elements = array('top-bar', 'general-header', 'header-bottom');

		private $_structure_elements_types = array('logo', 'search', 'cart', 'wishlist', 'account', 'compare', 'burger', 'mainmenu');

		public function __construct( $elements, $id, $new = false ) {
			$this->_elements = $elements;
			$this->_id = ( $id ) ? $id : WOODMART_HB_DEFAULT_ID;

			if( $new ) {
				$this->_create_empty();
			} else {
				$this->_load();
			}
		}

		private function _create_empty() {
			$this->set_settings();
			$this->set_structure();
			$this->set_elements_css();
		}

		private function _load() {
			// Get data from the database
			$data = get_option( 'whb_' . $this->get_id() );

			$name = ( isset( $data['name'] ) ) ? $data['name'] : WOODMART_HB_DEFAULT_NAME;
			$settings = ( isset( $data['settings'] ) ) ? $data['settings'] : array();
			$structure = ( isset( $data['structure'] ) ) ? $data['structure'] : false;

			$this->set_name( $name );
			$this->set_settings( $settings );
			$this->set_structure( $structure );

		}

		public function set_name( $name ) {
			$this->_name = $name;
		}

		public function set_structure( $structure = false ) {
			if( ! $structure ) $structure = woodmart_get_config( 'header-builder-structure' );
			$this->_structure = $structure;
		}

		public function set_settings( $settings = array() ) {
			$this->_settings = $settings;
		}
		
		public function get_elements_css() {
			return $this->elements_css;
		}
		
		public function reset_elements_css() {
			$this->elements_css = '';
		}
		
		/**
		 * Set header elements css.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $el Header structure.
		 */
		public function set_elements_css( $el = false ) {
			if ( ! $el ) {
				$el = woodmart_get_config( 'header-builder-structure' );
			}
			
			$selector = 'whb-' . $el['id'];
			$type     = $el['type'];
			
			if ( isset( $el['content'] ) && is_array( $el['content'] ) ) {
				foreach ( $el['content'] as $element ) {
					$this->set_elements_css( $element );
				}
			}
			
			$css        = '';
			$rules      = '';
			$border_css = '';
			
			if ( isset( $el['params']['background'] ) ) {
				$rules .= $this->generate_background_css( $el['params']['background']['value'] );
			}
			
			if ( isset( $el['params']['border'] ) ) {
				$sides      = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
				$border_css = $this->generate_border_css( $el['params']['border']['value'], $sides );
			}
			
			if ( isset( $el['params']['border'] ) && isset( $el['params']['border']['applyFor'] ) && 'boxed' === $el['params']['border']['applyFor'] ) {
				$css .= '.' . $selector . '-inner { ' . $border_css . ' }';
			} elseif ( $border_css ) {
				$rules .= $border_css;
			}
			
			if ( $rules ) {
				$css .= '.' . $selector . '{ ' . $rules . ' }';
			}
			
			$this->elements_css .= $css;
		}
		
		/**
		 * Generate color CSS code.
		 *
		 * @since 1.0.0
		 *
		 * @param array $color_data Color data.
		 *
		 * @return string
		 */
		public function generate_color_css( $color_data ) {
			$css = '';
			
			if ( isset( $color_data['r'] ) && isset( $color_data['g'] ) && isset( $color_data['b'] ) && isset( $color_data['a'] ) ) {
				$css .= 'color: rgba(' . $color_data['r'] . ', ' . $color_data['g'] . ', ' . $color_data['b'] . ', ' . $color_data['a'] . ');';
			}
			
			return $css;
		}
		
		/**
		 * Generate background CSS code.
		 *
		 * @since 1.0.0
		 *
		 * @param array $bg Background data.
		 *
		 * @return string
		 */
		public function generate_background_css( $bg ) {
			$css = '';
			
			if( isset( $bg['background-color'] ) ) extract( $bg['background-color'] );
			
			if( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
				$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
			}
			
			if( isset( $bg['background-image'] ) ) extract( $bg['background-image'] );
			
			if( isset( $url ) ) {
				$css .= 'background-image: url(' . $url . ');';
			}
			
			if( isset( $bg['background-size'] ) ) {
				$css .= 'background-size: ' . $bg['background-size'] . ';';
			}
			
			if( isset( $bg['background-attachment'] ) ) {
				$css .= 'background-attachment: ' . $bg['background-attachment'] . ';';
			}
			
			if( isset( $bg['background-position'] ) ) {
				$css .= 'background-position: ' . $bg['background-position'] . ';';
			}
			
			if( isset( $bg['background-repeat'] ) ) {
				$css .= 'background-repeat: ' . $bg['background-repeat'] . ';';
			}
			
			return $css;
		}
		
		/**
		 * Generate border CSS code.
		 *
		 * @since 1.0.0
		 *
		 * @param array $border Border data.
		 * @param array $sides Sides data.
		 *
		 * @return string
		 */
		public function generate_border_css( $border, $sides ) {
			$css = '';
			
			if( is_array( $border ) ) extract( $border );
			if( isset( $color ) ) extract( $color );
			
			if( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
				$css .= 'border-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
			}
			
			foreach ( $sides as $side ) {
				if ( isset( $width ) ) {
					$css .= 'border-' . $side . '-width: ' . $width . 'px;';
				}
				
				$css .= ( isset( $style ) ) ? 'border-' . $side . '-style: ' . $style . ';' : 'border-' . $side . '-style: solid;';
			}
			
			return $css;
		}

		public function get_id() {
			return $this->_id;
		}

		public function get_name() {
			return $this->_name;
		}

		public function get_structure() {
			return $this->_validate_structure( $this->_structure );
		}

		public function get_settings() {
			return $this->_validate_settings( $this->_settings );
		}

		private function _validate_structure( $structure ) {
			$structure = $this->_validate_sceleton( $structure );
			$structure = $this->_validate_element( $structure );
			return $structure;
		}

		public function save() {
			update_option( 'whb_' . $this->get_id(), $this->get_raw_data() );
		}

		public function get_raw_data() {
			return array(
				'structure' => $this->_structure,
				'settings' => $this->_settings,
				'elements_css' => $this->elements_css,
				'name' => $this->get_name(),
				'id' => $this->get_id()
			);
		}

		public function get_data() {
			return array(
				'structure' => $this->get_structure(),
				'settings' => $this->get_settings(),
				'name' => $this->get_name(),
				'id' => $this->get_id()
			);
		}

		private function _set_header_options( $elements ) {
			foreach ($elements as $element => $params) {
				if( ! in_array( $element, array_merge( $this->_structure_elements, $this->_structure_elements_types ) ) ) continue;
				foreach ($params as $key => $param) {
					if( isset( $param['value'] ) )
						$this->_header_options[ $element ][ $key ] = $param['value'];
				}
			}
		}

		public function get_options() {
			$this->_validate_settings( $this->_settings );
			return $this->_transform_settings_to_values( $this->_header_options );
		}

		private function _validate_settings( $settings ) {
			$default_settings = woodmart_get_config('header-builder-settings');

			$settings = $this->_validate_element_params( $settings, $default_settings );

			$this->_header_options = array_merge($settings, $this->_header_options);

			return $settings;
		}

		private function _transform_settings_to_values( $settings ) {
			foreach ($settings as $key => $value) {
				if( isset( $value['value'] ) ) $settings[$key] = $value['value'];
				if( in_array( $key, $this->_structure_elements ) ) {
					if( $value['hide_desktop'] ) $settings[$key]['height'] = 0;
					if( $value['hide_mobile'] ) $settings[$key]['mobile_height'] = 0;
				}

			}
			return $settings;
		}

		private function _validate_sceleton( $structure ) {

			$sceleton = $this->get_header_sceleton();

			$structure_params = $this->_grab_params_from_elements( $structure['content'] );

			$this->_set_header_options( $structure_params );

			$structure_elements = $this->_grab_content_from_elements( $structure['content'] );

			$sceleton = $this->fill_sceleton_with_params( $sceleton, $structure_params );
			$structure = $this->fill_sceleton_with_elements( $sceleton, $structure_elements );

			return $structure;
		}

		private function _grab_params_from_elements( $elements ) {

			$params = array();

			foreach ($elements as $key => $element) {

				if( isset( $element['params'] ) && is_array( $element['params'] ) ) {
					$params[$element['id']] = $element['params'];
				}

				if( in_array( $element['type'], $this->_structure_elements_types ) ) {
					$params[$element['type']] = $element['params'];
				}

				if( isset( $element['content'] ) && is_array( $element['content'] ) ) {
					$params = array_merge( $params, $this->_grab_params_from_elements( $element['content'] ) );
				}
			}

			return $params;
		}

		private function _grab_content_from_elements( $elements, $parent = 'root' ) {

			$structure_elements = array();
			$structure_elements[$parent] = array();

			foreach ($elements as $key => $element) {
				if( isset( $element['content'] ) && is_array( $element['content'] ) ) {
					$structure_elements = array_merge( $structure_elements, $this->_grab_content_from_elements( $element['content'], $element['id'] ) );
				} else {
					$structure_elements[$parent][$element['id']] = $element;
				}
			}

			if( empty( $structure_elements[$parent] ) ) unset( $structure_elements[$parent] );

			return $structure_elements;
		}

		public function get_header_sceleton() {
			return woodmart_get_config('header-sceleton');
		}

		public function fill_sceleton_with_elements( $sceleton, $default_structure ) {
			$sceleton = $this->_fill_element_with_content( $sceleton, $default_structure );

			return $sceleton;
		}

		private function _fill_element_with_content( $element, $structure ) {

			if( empty( $element['content'] ) && isset( $structure[ $element['id'] ] ) ) {
				$element['content'] = $structure[ $element['id'] ];
			} elseif( isset( $element['content'] ) && is_array( $element['content'] ) ) {
				$element['content'] = $this->_fill_elements_with_content( $element['content'], $structure );
			}

			return $element;
		}

		private function _fill_elements_with_content( $elements, $structure ) {
			foreach ($elements as $id => $element) {
				$elements[ $id ] = $this->_fill_element_with_content( $element, $structure );
			}

			return $elements;
		}

		public function fill_sceleton_with_params( $sceleton, $params ) {
			$sceleton = $this->_fill_element_with_params( $sceleton, $params );

			return $sceleton;
		}

		private function _fill_element_with_params( $element, $params ) {

			if( empty( $element['params'] ) && isset( $params[ $element['id'] ] ) ) {
				$element['params'] = $params[ $element['id'] ];
			} elseif( isset( $element['content'] ) && is_array( $element['content'] ) ) {
				$element['content'] = $this->_fill_elements_with_params( $element['content'], $params );
			}

			return $element;
		}

		private function _fill_elements_with_params( $elements, $params ) {
			foreach ($elements as $id => $element) {
				$elements[ $id ] = $this->_fill_element_with_params( $element, $params );
			}

			return $elements;
		}

		private function _validate_elements( $elements ) {
			foreach ($elements as $key => $element ) {
				$elements[$key] = $this->_validate_element( $element );
			}

			return $elements;
		}

		private function _validate_element( $el ) {

			$type = ucfirst($el['type']);

			if( ! isset( $this->_elements->elements_classes[$type] ) ) return $el;

			$elClass = $this->_elements->elements_classes[$type];

			$default = $elClass->get_args();

			$el = $this->_validate_element_args( $el, $default );

			return $el;
		}

		private function _validate_element_args( $args, $default ) {

			foreach( $default as $key => $value ) {
				if( $key == 'params' && isset( $args[ $key ] ) ) {
					$args[ $key ] = $this->_validate_element_params( $args[ $key ], $value );
				} elseif( $key == 'content' && isset( $args[ $key ] ) ) {
					$args[ $key ] = $this->_validate_elements( $args[ $key ] );
				} elseif( ! isset( $args[ $key ] ) ) {
					$args[ $key ] = $value;
				}
			}

			return $args;
		}

		private function _validate_element_params( $params, $default ) {

			$params = wp_parse_args( $params, $default );

			foreach( $params as $key => $value ) {
				if( ! isset( $default[ $key ] ) ) {
					unset( $params[ $key ] );
				} else {
					$params[ $key ] = $this->_validate_param( $params[ $key ], $default[ $key ] ) ;
				}
			}

			return $params;
		}

		private function _validate_param( $args, $default_args ) {
			foreach ($default_args as $key => $value) {
				// Validate image param by ID
				if( $args['type'] == 'image' && ! empty( $args['value'] ) && ! empty( $args['value']['id'] ) ) {
					$attachment = wp_get_attachment_image_src( $args['value']['id'], 'full' );
					if( isset( $attachment[0] ) && ! empty( $attachment[0] ) ) {
						$args['value']['url'] = $attachment[0];
						$args['value']['width'] = $attachment[1];
						$args['value']['height'] = $attachment[2];
					} else {
						$args['value'] = '';
					}
				}

				if ( $args['type'] == 'border' && isset( $default_args['sides'] ) && is_array( $args['value'] ) ) {

					$args['value']['sides'] = $default_args['sides'];
				}
				
				if( $key != 'value' || ( $key == 'value' && ! isset( $args['value'] ) ) ) $args[ $key ] = $value;
			}

			return $args;
		}

	}

}
