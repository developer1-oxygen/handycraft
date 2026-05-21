<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* ------------------------------------------------------------------------------------------------
* Extra menu (part of the mega menu)
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'woodmart_shortcode_extra_menu' ) ) {
	function woodmart_shortcode_extra_menu($atts = array(), $content = null) {
		$output = $class = $liclass = $label_out = '';
		extract(shortcode_atts( array(
			'link' => '',
			'title' => '',
			'label' => 'primary',
			'label_text' => '',
			'css_animation' => 'none',
			'el_class' => ''
		), $atts ));

		if ( woodmart_get_menu_label_tag( $label, $label_text ) ) {
			$liclass .= woodmart_get_menu_label_class( $label );
		}

		if ( $el_class ) {
			$class .= ' ' . $el_class;
		}
		$class .= ' mega-menu-list';
		$class .= woodmart_get_css_animation( $css_animation );

		ob_start(); ?>

			<ul class="sub-menu<?php echo esc_attr( $class ); ?>" >
				<li class="<?php echo esc_attr( $liclass ); ?>"><a <?php echo woodmart_get_link_attributes( $link ); ?>><span class="nav-link-text"><?php echo wp_kses( $title, woodmart_get_allowed_html() ); ?></span><?php echo woodmart_get_menu_label_tag( $label, $label_text ); ?></a>
					<ul class="sub-sub-menu">
						<?php echo do_shortcode( $content ); ?>
					</ul>
				</li>
			</ul>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}


if( ! function_exists( 'woodmart_shortcode_extra_menu_list' ) ) {
	function woodmart_shortcode_extra_menu_list($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$output = $class = $label_out = '';
		extract(shortcode_atts( array(
			'link' => '',
			'title' => '',
			'label' => 'primary',
			'label_text' => '',
			'el_class' => ''
		), $atts ));

		if ( woodmart_get_menu_label_tag( $label, $label_text ) ) {
			$class .= woodmart_get_menu_label_class( $label );
		}

		if ( $el_class ) {
			$class .= ' ' . $el_class;
		}

		ob_start(); ?>

			<li class="<?php echo esc_attr( $class ); ?>"><a <?php echo woodmart_get_link_attributes( $link ); ?>><span class="nav-link-text"><?php echo wp_kses( $title, woodmart_get_allowed_html() ); ?></span><?php echo woodmart_get_menu_label_tag( $label, $label_text ); ?></a></li>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}

if( ! function_exists( 'woodmart_vc_get_link_attr' ) ) {
	function woodmart_vc_get_link_attr( $link ) {
		$link = ( '||' === $link ) ? '' : $link;
		if( function_exists( 'vc_build_link' ) ){
			$link = vc_build_link( $link );
		}
		return $link;
	}
}

if( ! function_exists( 'woodmart_get_link_attributes' ) ) {
	function woodmart_get_link_attributes( $link, $popup = false ) {
		//parse link
		$link = woodmart_vc_get_link_attr( $link );
		$use_link = false;
		if ( isset( $link['url'] ) && strlen( $link['url'] ) > 0 ) {
			$use_link = true;
			$a_href = apply_filters( 'woodmart_extra_menu_url', $link['url'] );
			if ( $popup ) $a_href = $link['url'];
			$a_title = $link['title'];
			$a_target = $link['target'];
		}

		$attributes = array();

		if ( $use_link ) {
			$attributes[] = 'href="' . trim( $a_href ) . '"';
			$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
			if ( ! empty( $a_target ) ) {
				$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
			}
		}

		$attributes = implode( ' ', $attributes );

		return $attributes;
	}
}


if( ! function_exists( 'woodmart_get_menu_label_tag' ) ) {
	function woodmart_get_menu_label_tag( $label, $label_text ) {
		if( empty( $label_text ) ) return '';
		$label_out = '<span class="menu-label menu-label-' . $label . '">' . esc_attr( $label_text ) . '</span>';
		return $label_out;
	}
}


if( ! function_exists( 'woodmart_get_menu_label_class' ) ) {
	function woodmart_get_menu_label_class( $label ) {
		$class = '';
		$class .= ' item-with-label';
		$class .= ' item-label-' . $label;
		return $class;
	}
}

