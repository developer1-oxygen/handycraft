<?php
/**
 * HTML dropdown select control.
 *
 * @package xts
 */

namespace XTS\Options\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options\Field;

/**
 * Switcher field control.
 */
class Select extends Field {

	/**
	 * Displays the field control HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return void.
	 */
	public function render_control() {
		$options = $this->get_field_options();
		$classes = '';

		if ( empty( $options ) && ! isset( $this->args['empty_option'] ) ) {
			esc_html_e( 'Options for this field are not provided in the map function.', 'woodmart' );
			return;
		}

		$multiple = isset( $this->args['multiple'] ) && $this->args['multiple'] ? 'multiple' : '';
		$classes .= isset( $this->args['select2'] ) && $this->args['select2'] ? ' xts-type-select2' : '';

		$name = $this->get_input_name();

		if ( $multiple ) {
			$name = $this->get_input_name() . '[]';

			$order = $this->get_field_value();

			usort(
				$options,
				function ( $a, $b ) use ( $order ) {
					$pos_a = array_search( $a['value'], $order );
					$pos_b = array_search( $b['value'], $order );
					return $pos_a - $pos_b;
				}
			);
        }

		?>
			<select class="xts-select<?php echo esc_attr( $classes ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo esc_attr( $multiple ); ?>>
				<?php if ( isset( $this->args['empty_option'] ) && $this->args['empty_option'] ) : ?>
					<option value=""><?php esc_html_e( 'Select', 'xts-theme' ); ?></option>
				<?php endif; ?>

				<?php foreach ( $options as $key => $option ) : ?>
					<?php
					$value    = $this->get_field_value();
					$selected = false;

					if ( is_array( $value ) && in_array( $option['value'], $value, false ) ) {
						$selected = true;
					} elseif ( ! is_array( $value ) && strval( $value ) === strval( $option['value'] ) ) {
						$selected = true;
					}

					?>
					<option value="<?php echo esc_attr( $option['value'] ); ?>" <?php selected( true, $selected ); ?>><?php echo esc_html( $option['name'] ); ?></option>
				<?php endforeach ?>
			</select>
		<?php
	}

	/**
	 * Enqueue colorpicker lib.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		if ( isset( $this->args['select2'] ) && $this->args['select2'] ) {
			wp_enqueue_script( 'select2', WOODMART_ASSETS . '/js/select2.full.min.js', array(), woodmart_get_theme_info( 'Version' ), true );
		}
	}
}
