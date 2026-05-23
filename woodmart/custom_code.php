<?php
/**
 * Custom code - shortcodes and custom functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether a product belongs to "Frame" category tree.
 *
 * Matches by slug/name and also supports child categories of Frame.
 *
 * @param int $product_id Product post ID.
 * @return bool
 */
function wd_is_frame_product( $product_id ) {
	$product_id = (int) $product_id;

	if ( $product_id <= 0 ) {
		return false;
	}

	$frame_keys = array( 'frame', 'frames' );
	$terms      = get_the_terms( $product_id, 'product_cat' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return false;
	}

	foreach ( $terms as $term ) {
		$term_slug = strtolower( (string) $term->slug );
		$term_name = strtolower( (string) $term->name );

		if ( in_array( $term_slug, $frame_keys, true ) || in_array( $term_name, $frame_keys, true ) ) {
			return true;
		}

		$ancestors = get_ancestors( (int) $term->term_id, 'product_cat' );
		if ( empty( $ancestors ) ) {
			continue;
		}

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( (int) $ancestor_id, 'product_cat' );
			if ( ! $ancestor || is_wp_error( $ancestor ) ) {
				continue;
			}

			$ancestor_slug = strtolower( (string) $ancestor->slug );
			$ancestor_name = strtolower( (string) $ancestor->name );

			if ( in_array( $ancestor_slug, $frame_keys, true ) || in_array( $ancestor_name, $frame_keys, true ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Build visualizer URL with product context query args.
 *
 * @param int $product_id Product post ID.
 * @return string
 */
function wd_get_frame_visualizer_url( $product_id ) {
	$base_url = get_theme_file_uri( '/custom/frame-visualizer/art-visualizer.html' );
	$product  = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
	$price    = '';

	if ( $product && is_a( $product, 'WC_Product' ) && function_exists( 'wc_price' ) ) {
		$price = html_entity_decode( wp_strip_all_tags( wc_price( (float) wc_get_price_to_display( $product ) ) ) );
	}

	$args = array(
		'wc_product_id' => (int) $product_id,
		'wc_product'    => get_the_title( $product_id ),
		'wc_price'      => $price,
		'wc_image'      => get_the_post_thumbnail_url( $product_id, 'large' ),
	);

	$visualizer_config = wd_get_frame_visualizer_config_for_product( $product_id );
	if ( ! empty( $visualizer_config ) ) {
		$json = wp_json_encode( $visualizer_config );
		if ( $json ) {
			$args['wd_cfg'] = rawurlencode( base64_encode( $json ) );
		}
	}

	return add_query_arg( $args, $base_url );
}

/**
 * Parse delimited lines from textarea.
 *
 * @param string $raw  Raw textarea value.
 * @param array  $keys Keys for parts.
 * @return array
 */
function wd_parse_delimited_lines( $raw, $keys ) {
	$rows = preg_split( '/\r\n|\r|\n/', (string) $raw );
	$out  = array();

	foreach ( $rows as $row ) {
		$row = trim( $row );
		if ( '' === $row ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $row ) );
		$item  = array();

		foreach ( $keys as $idx => $name ) {
			$value = isset( $parts[ $idx ] ) ? $parts[ $idx ] : '';
			if ( 'image' === $name ) {
				$item[ $name ] = esc_url_raw( $value );
			} elseif ( 'enabled' === $name ) {
				$item[ $name ] = in_array( strtolower( $value ), array( '1', 'true', 'yes', 'on' ), true );
			} else {
				$item[ $name ] = sanitize_text_field( $value );
			}
		}

		if ( empty( $item[ $keys[0] ] ) ) {
			continue;
		}

		$out[] = $item;
	}

	return $out;
}

/**
 * Parse slider setting row: min|max|step|start.
 *
 * @param string $raw Raw value.
 * @return array
 */
function wd_parse_slider_setting( $raw ) {
	$rows = preg_split( '/\r\n|\r|\n/', (string) $raw );
	if ( empty( $rows ) ) {
		return array();
	}
	$first = trim( (string) $rows[0] );
	if ( '' === $first ) {
		return array();
	}
	$parts = array_map( 'trim', explode( '|', $first ) );
	if ( count( $parts ) < 4 ) {
		return array();
	}

	$min   = (float) $parts[0];
	$max   = (float) $parts[1];
	$step  = (float) $parts[2];
	$start = (float) $parts[3];

	// Guard against malformed admin values that can freeze noUi slider.
	if ( $max <= $min ) {
		$max = $min + 1;
	}
	if ( $step <= 0 ) {
		$step = 1;
	}
	if ( $start < $min ) {
		$start = $min;
	}
	if ( $start > $max ) {
		$start = $max;
	}

	return array(
		'min'   => $min,
		'max'   => $max,
		'step'  => $step,
		'start' => $start,
	);
}

/**
 * Build config array for frame visualizer from product/variation meta.
 *
 * @param int $product_id Product ID.
 * @return array
 */
function wd_get_frame_visualizer_config_for_product( $product_id ) {
	$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
	$config  = array();

	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return $config;
	}

	$scene_raw = (string) get_post_meta( $product_id, '_wd_frame_scene_options', true );
	$style_raw = (string) get_post_meta( $product_id, '_wd_frame_style_options', true );
	$size_raw  = (string) get_post_meta( $product_id, '_wd_frame_artwork_sizes', true );
	$depth_raw = (string) get_post_meta( $product_id, '_wd_frame_depth_options', true );
	$mat_raw   = (string) get_post_meta( $product_id, '_wd_frame_mat_colors', true );
	$mat_slider_raw = (string) get_post_meta( $product_id, '_wd_frame_mat_slider', true );
	$rotation_slider_raw = (string) get_post_meta( $product_id, '_wd_frame_rotation_slider', true );
	$rotation_enabled = 'yes' === get_post_meta( $product_id, '_wd_frame_rotation_enabled', true );
	$show_upload_meta = get_post_meta( $product_id, '_wd_frame_show_upload', true );
	$show_artwork_size_meta = get_post_meta( $product_id, '_wd_frame_show_artwork_size', true );
	$show_scene_meta = get_post_meta( $product_id, '_wd_frame_show_scene', true );
	$show_style_meta = get_post_meta( $product_id, '_wd_frame_show_style', true );
	$show_matting_meta = get_post_meta( $product_id, '_wd_frame_show_matting', true );
	$show_upload = '' === $show_upload_meta ? true : ( 'yes' === $show_upload_meta );
	$show_artwork_size = '' === $show_artwork_size_meta ? true : ( 'yes' === $show_artwork_size_meta );
	$show_scene = '' === $show_scene_meta ? true : ( 'yes' === $show_scene_meta );
	$show_style = '' === $show_style_meta ? true : ( 'yes' === $show_style_meta );
	$show_matting = '' === $show_matting_meta ? true : ( 'yes' === $show_matting_meta );

	if ( $product->is_type( 'variable' ) ) {
		$default_attrs = method_exists( $product, 'get_default_attributes' ) ? (array) $product->get_default_attributes() : array();
		$variation_id  = 0;

		if ( method_exists( $product, 'get_children' ) ) {
			foreach ( (array) $product->get_children() as $child_id ) {
				$variation = wc_get_product( $child_id );
				if ( ! $variation || ! $variation->exists() ) {
					continue;
				}
				if ( ! empty( $default_attrs ) ) {
					$matched = true;
					foreach ( $default_attrs as $tax => $value ) {
						$var_value = $variation->get_attribute( str_replace( 'attribute_', '', $tax ) );
						if ( (string) $var_value !== (string) $value ) {
							$matched = false;
							break;
						}
					}
					if ( ! $matched ) {
						continue;
					}
				}
				$variation_id = (int) $child_id;
				break;
			}
		}

		if ( $variation_id > 0 ) {
			$scene_variation_raw = (string) get_post_meta( $variation_id, '_wd_frame_scene_options', true );
			$style_variation_raw = (string) get_post_meta( $variation_id, '_wd_frame_style_options', true );
			$size_variation_raw  = (string) get_post_meta( $variation_id, '_wd_frame_artwork_sizes', true );
			$depth_variation_raw = (string) get_post_meta( $variation_id, '_wd_frame_depth_options', true );
			$mat_variation_raw   = (string) get_post_meta( $variation_id, '_wd_frame_mat_colors', true );
			$mat_slider_variation_raw = (string) get_post_meta( $variation_id, '_wd_frame_mat_slider', true );
			$rotation_slider_variation_raw = (string) get_post_meta( $variation_id, '_wd_frame_rotation_slider', true );
			$rotation_variation  = get_post_meta( $variation_id, '_wd_frame_rotation_enabled', true );
			$show_upload_variation = get_post_meta( $variation_id, '_wd_frame_show_upload', true );
			$show_artwork_size_variation = get_post_meta( $variation_id, '_wd_frame_show_artwork_size', true );
			$show_scene_variation = get_post_meta( $variation_id, '_wd_frame_show_scene', true );
			$show_style_variation = get_post_meta( $variation_id, '_wd_frame_show_style', true );
			$show_matting_variation = get_post_meta( $variation_id, '_wd_frame_show_matting', true );

			if ( '' !== $scene_variation_raw ) {
				$scene_raw = $scene_variation_raw;
			}
			if ( '' !== $style_variation_raw ) {
				$style_raw = $style_variation_raw;
			}
			if ( '' !== $size_variation_raw ) {
				$size_raw = $size_variation_raw;
			}
			if ( '' !== $depth_variation_raw ) {
				$depth_raw = $depth_variation_raw;
			}
			if ( '' !== $mat_variation_raw ) {
				$mat_raw = $mat_variation_raw;
			}
			if ( '' !== $mat_slider_variation_raw ) {
				$mat_slider_raw = $mat_slider_variation_raw;
			}
			if ( '' !== $rotation_slider_variation_raw ) {
				$rotation_slider_raw = $rotation_slider_variation_raw;
			}
			if ( '' !== $rotation_variation ) {
				$rotation_enabled = 'yes' === $rotation_variation;
			}
			if ( '' !== $show_upload_variation ) {
				$show_upload = 'yes' === $show_upload_variation;
			}
			if ( '' !== $show_artwork_size_variation ) {
				$show_artwork_size = 'yes' === $show_artwork_size_variation;
			}
			if ( '' !== $show_scene_variation ) {
				$show_scene = 'yes' === $show_scene_variation;
			}
			if ( '' !== $show_style_variation ) {
				$show_style = 'yes' === $show_style_variation;
			}
			if ( '' !== $show_matting_variation ) {
				$show_matting = 'yes' === $show_matting_variation;
			}
		}
	}

	$scenes = wd_parse_delimited_lines( $scene_raw, array( 'key', 'label', 'image' ) );
	$styles = wd_parse_delimited_lines( $style_raw, array( 'key', 'label', 'image' ) );
	$sizes  = wd_parse_delimited_lines( $size_raw, array( 'label', 'width', 'height' ) );
	$depths = wd_parse_delimited_lines( $depth_raw, array( 'value', 'label' ) );
	$mats   = wd_parse_delimited_lines( $mat_raw, array( 'key', 'label', 'color' ) );
	$mat_slider      = wd_parse_slider_setting( $mat_slider_raw );
	$rotation_slider = wd_parse_slider_setting( $rotation_slider_raw );

	if ( ! empty( $scenes ) ) {
		$config['scenes'] = $scenes;
	}
	if ( ! empty( $styles ) ) {
		$config['styles'] = $styles;
	}
	if ( ! empty( $sizes ) ) {
		$config['artwork_sizes'] = $sizes;
	}
	if ( ! empty( $depths ) ) {
		$config['depth_options'] = $depths;
	}
	if ( ! empty( $mats ) ) {
		$config['mat_colors'] = $mats;
	}
	if ( ! empty( $mat_slider ) ) {
		$config['mat_slider'] = $mat_slider;
	}
	if ( ! empty( $rotation_slider ) ) {
		$config['rotation_slider'] = $rotation_slider;
	}
	$config['rotation_enabled'] = $rotation_enabled;
	$config['visibility'] = array(
		'upload'       => $show_upload,
		'artwork_size' => $show_artwork_size,
		'scene'        => $show_scene,
		'style'        => $show_style,
		'matting'      => $show_matting,
		'rotation'     => $rotation_enabled,
	);

	return $config;
}

/**
 * Product-level frame visualizer fields.
 */
function wd_add_frame_visualizer_product_fields() {
	global $post;
	$product_id = $post ? (int) $post->ID : 0;
	if ( $product_id <= 0 || ! wd_is_frame_product( $product_id ) ) {
		return;
	}

	echo '<div class="options_group show_if_simple show_if_variable">';

	woocommerce_wp_textarea_input(
		array(
			'id'          => '_wd_frame_scene_options',
			'label'       => __( 'Frame Scenes', 'woodmart' ),
			'description' => __( 'Use visual builder below (single/multiple image selection supported).', 'woodmart' ),
			'desc_tip'    => true,
		)
	);

	woocommerce_wp_textarea_input(
		array(
			'id'          => '_wd_frame_style_options',
			'label'       => __( 'Frame Styles', 'woodmart' ),
			'description' => __( 'Use visual builder below (image previews + multi select).', 'woodmart' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_textarea_input(
		array(
			'id'          => '_wd_frame_artwork_sizes',
			'label'       => __( 'Artwork Sizes', 'woodmart' ),
			'description' => __( 'One per line: Label|Width|Height (e.g. 12" x 8"|12|8)', 'woodmart' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_textarea_input(
		array(
			'id'          => '_wd_frame_depth_options',
			'label'       => __( 'Frame Depth Dropdown', 'woodmart' ),
			'description' => __( 'One per line: value|label (e.g. 0.75|0.75")', 'woodmart' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_textarea_input(
		array(
			'id'          => '_wd_frame_mat_colors',
			'label'       => __( 'Mat Colors', 'woodmart' ),
			'description' => __( 'Use predefined color list + custom color picker below.', 'woodmart' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_wd_frame_mat_slider',
			'label'       => __( 'Mat Slider (min|max|step|start)', 'woodmart' ),
			'description' => __( 'Example: 0|6|0.5|2', 'woodmart' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_wd_frame_rotation_slider',
			'label'       => __( 'Rotation Slider (min|max|step|start)', 'woodmart' ),
			'description' => __( 'Example: 0|55|1|30', 'woodmart' ),
			'desc_tip'    => true,
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'          => '_wd_frame_show_upload',
			'label'       => __( 'Show Upload Artwork', 'woodmart' ),
			'description' => __( 'Disable to hide upload actions in visualizer.', 'woodmart' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $product_id, '_wd_frame_show_upload', true ) ? get_post_meta( $product_id, '_wd_frame_show_upload', true ) : 'yes',
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'          => '_wd_frame_show_artwork_size',
			'label'       => __( 'Show Artwork Size', 'woodmart' ),
			'description' => __( 'Disable to hide artwork size control.', 'woodmart' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $product_id, '_wd_frame_show_artwork_size', true ) ? get_post_meta( $product_id, '_wd_frame_show_artwork_size', true ) : 'yes',
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'          => '_wd_frame_show_scene',
			'label'       => __( 'Show Scene Background', 'woodmart' ),
			'description' => __( 'Disable to hide scene background selector.', 'woodmart' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $product_id, '_wd_frame_show_scene', true ) ? get_post_meta( $product_id, '_wd_frame_show_scene', true ) : 'yes',
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'          => '_wd_frame_show_style',
			'label'       => __( 'Show Frame Style', 'woodmart' ),
			'description' => __( 'Disable to hide frame style controls.', 'woodmart' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $product_id, '_wd_frame_show_style', true ) ? get_post_meta( $product_id, '_wd_frame_show_style', true ) : 'yes',
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'          => '_wd_frame_show_matting',
			'label'       => __( 'Show Matting', 'woodmart' ),
			'description' => __( 'Disable to hide matting controls.', 'woodmart' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $product_id, '_wd_frame_show_matting', true ) ? get_post_meta( $product_id, '_wd_frame_show_matting', true ) : 'yes',
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'          => '_wd_frame_rotation_enabled',
			'label'       => __( 'Enable 3D Rotation', 'woodmart' ),
			'description' => __( 'Disable to hide 3D Rotation control in visualizer.', 'woodmart' ),
			'desc_tip'    => true,
		)
	);

	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'wd_add_frame_visualizer_product_fields' );

/**
 * Save product-level frame visualizer fields.
 *
 * @param WC_Product $product Product object.
 */
function wd_save_frame_visualizer_product_fields( $product ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}
	if ( ! wd_is_frame_product( $product->get_id() ) ) {
		return;
	}

	$scene_raw = isset( $_POST['_wd_frame_scene_options'] ) ? wp_unslash( $_POST['_wd_frame_scene_options'] ) : '';
	$style_raw = isset( $_POST['_wd_frame_style_options'] ) ? wp_unslash( $_POST['_wd_frame_style_options'] ) : '';
	$size_raw  = isset( $_POST['_wd_frame_artwork_sizes'] ) ? wp_unslash( $_POST['_wd_frame_artwork_sizes'] ) : '';
	$depth_raw = isset( $_POST['_wd_frame_depth_options'] ) ? wp_unslash( $_POST['_wd_frame_depth_options'] ) : '';
	$mat_raw   = isset( $_POST['_wd_frame_mat_colors'] ) ? wp_unslash( $_POST['_wd_frame_mat_colors'] ) : '';
	$mat_slider = isset( $_POST['_wd_frame_mat_slider'] ) ? wp_unslash( $_POST['_wd_frame_mat_slider'] ) : '';
	$rotation_slider = isset( $_POST['_wd_frame_rotation_slider'] ) ? wp_unslash( $_POST['_wd_frame_rotation_slider'] ) : '';
	$show_upload = isset( $_POST['_wd_frame_show_upload'] ) ? 'yes' : 'no';
	$show_artwork_size = isset( $_POST['_wd_frame_show_artwork_size'] ) ? 'yes' : 'no';
	$show_scene = isset( $_POST['_wd_frame_show_scene'] ) ? 'yes' : 'no';
	$show_style = isset( $_POST['_wd_frame_show_style'] ) ? 'yes' : 'no';
	$show_matting = isset( $_POST['_wd_frame_show_matting'] ) ? 'yes' : 'no';
	$rotation  = isset( $_POST['_wd_frame_rotation_enabled'] ) ? 'yes' : 'no';

	$product->update_meta_data( '_wd_frame_scene_options', sanitize_textarea_field( $scene_raw ) );
	$product->update_meta_data( '_wd_frame_style_options', sanitize_textarea_field( $style_raw ) );
	$product->update_meta_data( '_wd_frame_artwork_sizes', sanitize_textarea_field( $size_raw ) );
	$product->update_meta_data( '_wd_frame_depth_options', sanitize_textarea_field( $depth_raw ) );
	$product->update_meta_data( '_wd_frame_mat_colors', sanitize_textarea_field( $mat_raw ) );
	$product->update_meta_data( '_wd_frame_mat_slider', sanitize_text_field( $mat_slider ) );
	$product->update_meta_data( '_wd_frame_rotation_slider', sanitize_text_field( $rotation_slider ) );
	$product->update_meta_data( '_wd_frame_show_upload', $show_upload );
	$product->update_meta_data( '_wd_frame_show_artwork_size', $show_artwork_size );
	$product->update_meta_data( '_wd_frame_show_scene', $show_scene );
	$product->update_meta_data( '_wd_frame_show_style', $show_style );
	$product->update_meta_data( '_wd_frame_show_matting', $show_matting );
	$product->update_meta_data( '_wd_frame_rotation_enabled', $rotation );
}
add_action( 'woocommerce_admin_process_product_object', 'wd_save_frame_visualizer_product_fields' );

/**
 * Variation-level fields for frame visualizer options.
 *
 * @param int     $loop           Loop index.
 * @param array   $variation_data Variation data.
 * @param WP_Post $variation      Variation post.
 */
function wd_add_frame_visualizer_variation_fields( $loop, $variation_data, $variation ) {
	$parent_id = isset( $variation->post_parent ) ? (int) $variation->post_parent : 0;
	if ( $parent_id <= 0 || ! wd_is_frame_product( $parent_id ) ) {
		return;
	}

	$scene_value = get_post_meta( $variation->ID, '_wd_frame_scene_options', true );
	$style_value = get_post_meta( $variation->ID, '_wd_frame_style_options', true );
	$size_value  = get_post_meta( $variation->ID, '_wd_frame_artwork_sizes', true );
	$depth_value = get_post_meta( $variation->ID, '_wd_frame_depth_options', true );
	$mat_value   = get_post_meta( $variation->ID, '_wd_frame_mat_colors', true );
	$mat_slider  = get_post_meta( $variation->ID, '_wd_frame_mat_slider', true );
	$rotation_slider = get_post_meta( $variation->ID, '_wd_frame_rotation_slider', true );
	$rotation    = get_post_meta( $variation->ID, '_wd_frame_rotation_enabled', true );
	$show_upload = get_post_meta( $variation->ID, '_wd_frame_show_upload', true );
	$show_artwork_size = get_post_meta( $variation->ID, '_wd_frame_show_artwork_size', true );
	$show_scene = get_post_meta( $variation->ID, '_wd_frame_show_scene', true );
	$show_style = get_post_meta( $variation->ID, '_wd_frame_show_style', true );
	$show_matting = get_post_meta( $variation->ID, '_wd_frame_show_matting', true );
	?>
	<p class="form-row form-row-full">
		<label><?php esc_html_e( 'Frame Scenes (key|Label|Image URL)', 'woodmart' ); ?></label>
		<textarea
			name="wd_frame_scene_options[<?php echo esc_attr( $variation->ID ); ?>]"
			rows="3"
			style="width:100%;"
		><?php echo esc_textarea( $scene_value ); ?></textarea>
	</p>
	<p class="form-row form-row-full">
		<label><?php esc_html_e( 'Frame Styles (id|Label|Image URL)', 'woodmart' ); ?></label>
		<textarea
			name="wd_frame_style_options[<?php echo esc_attr( $variation->ID ); ?>]"
			rows="3"
			style="width:100%;"
		><?php echo esc_textarea( $style_value ); ?></textarea>
	</p>
	<p class="form-row form-row-full">
		<label><?php esc_html_e( 'Artwork Sizes (Label|Width|Height)', 'woodmart' ); ?></label>
		<textarea name="wd_frame_artwork_sizes[<?php echo esc_attr( $variation->ID ); ?>]" rows="3" style="width:100%;"><?php echo esc_textarea( $size_value ); ?></textarea>
	</p>
	<p class="form-row form-row-full">
		<label><?php esc_html_e( 'Depth Dropdown (value|label)', 'woodmart' ); ?></label>
		<textarea name="wd_frame_depth_options[<?php echo esc_attr( $variation->ID ); ?>]" rows="3" style="width:100%;"><?php echo esc_textarea( $depth_value ); ?></textarea>
	</p>
	<p class="form-row form-row-full">
		<label><?php esc_html_e( 'Mat Colors (id|Label|CSS Color)', 'woodmart' ); ?></label>
		<textarea name="wd_frame_mat_colors[<?php echo esc_attr( $variation->ID ); ?>]" rows="3" style="width:100%;"><?php echo esc_textarea( $mat_value ); ?></textarea>
	</p>
	<p class="form-row form-row-first">
		<label><?php esc_html_e( 'Mat Slider (min|max|step|start)', 'woodmart' ); ?></label>
		<input type="text" name="wd_frame_mat_slider[<?php echo esc_attr( $variation->ID ); ?>]" value="<?php echo esc_attr( $mat_slider ); ?>" style="width:100%;" />
	</p>
	<p class="form-row form-row-last">
		<label><?php esc_html_e( 'Rotation Slider (min|max|step|start)', 'woodmart' ); ?></label>
		<input type="text" name="wd_frame_rotation_slider[<?php echo esc_attr( $variation->ID ); ?>]" value="<?php echo esc_attr( $rotation_slider ); ?>" style="width:100%;" />
	</p>
	<p class="form-row form-row-full">
		<label>
			<input type="checkbox" name="wd_frame_show_upload[<?php echo esc_attr( $variation->ID ); ?>]" value="yes" <?php checked( 'yes', $show_upload ); ?> />
			<?php esc_html_e( 'Show Upload Artwork controls for this variation', 'woodmart' ); ?>
		</label>
	</p>
	<p class="form-row form-row-full">
		<label>
			<input type="checkbox" name="wd_frame_show_artwork_size[<?php echo esc_attr( $variation->ID ); ?>]" value="yes" <?php checked( 'yes', $show_artwork_size ); ?> />
			<?php esc_html_e( 'Show Artwork Size for this variation', 'woodmart' ); ?>
		</label>
	</p>
	<p class="form-row form-row-full">
		<label>
			<input type="checkbox" name="wd_frame_show_scene[<?php echo esc_attr( $variation->ID ); ?>]" value="yes" <?php checked( 'yes', $show_scene ); ?> />
			<?php esc_html_e( 'Show Scene Background for this variation', 'woodmart' ); ?>
		</label>
	</p>
	<p class="form-row form-row-full">
		<label>
			<input type="checkbox" name="wd_frame_show_style[<?php echo esc_attr( $variation->ID ); ?>]" value="yes" <?php checked( 'yes', $show_style ); ?> />
			<?php esc_html_e( 'Show Frame Style for this variation', 'woodmart' ); ?>
		</label>
	</p>
	<p class="form-row form-row-full">
		<label>
			<input type="checkbox" name="wd_frame_show_matting[<?php echo esc_attr( $variation->ID ); ?>]" value="yes" <?php checked( 'yes', $show_matting ); ?> />
			<?php esc_html_e( 'Show Matting for this variation', 'woodmart' ); ?>
		</label>
	</p>
	<p class="form-row form-row-full">
		<label>
			<input type="checkbox" name="wd_frame_rotation_enabled[<?php echo esc_attr( $variation->ID ); ?>]" value="yes" <?php checked( 'yes', $rotation ); ?> />
			<?php esc_html_e( 'Enable 3D Rotation for this variation', 'woodmart' ); ?>
		</label>
	</p>
	<?php
}
add_action( 'woocommerce_product_after_variable_attributes', 'wd_add_frame_visualizer_variation_fields', 20, 3 );

/**
 * Save variation-level frame visualizer fields.
 *
 * @param int $variation_id Variation ID.
 */
function wd_save_frame_visualizer_variation_fields( $variation_id ) {
	$parent_id = (int) wp_get_post_parent_id( $variation_id );
	if ( $parent_id <= 0 || ! wd_is_frame_product( $parent_id ) ) {
		return;
	}

	$scene_value = '';
	$style_value = '';
	$size_value  = '';
	$depth_value = '';
	$mat_value   = '';
	$mat_slider  = '';
	$rotation_slider = '';
	$show_upload = 'no';
	$show_artwork_size = 'no';
	$show_scene = 'no';
	$show_style = 'no';
	$show_matting = 'no';
	$rotation    = 'no';

	if ( isset( $_POST['wd_frame_scene_options'][ $variation_id ] ) ) {
		$scene_value = sanitize_textarea_field( wp_unslash( $_POST['wd_frame_scene_options'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_style_options'][ $variation_id ] ) ) {
		$style_value = sanitize_textarea_field( wp_unslash( $_POST['wd_frame_style_options'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_artwork_sizes'][ $variation_id ] ) ) {
		$size_value = sanitize_textarea_field( wp_unslash( $_POST['wd_frame_artwork_sizes'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_depth_options'][ $variation_id ] ) ) {
		$depth_value = sanitize_textarea_field( wp_unslash( $_POST['wd_frame_depth_options'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_mat_colors'][ $variation_id ] ) ) {
		$mat_value = sanitize_textarea_field( wp_unslash( $_POST['wd_frame_mat_colors'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_mat_slider'][ $variation_id ] ) ) {
		$mat_slider = sanitize_text_field( wp_unslash( $_POST['wd_frame_mat_slider'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_rotation_slider'][ $variation_id ] ) ) {
		$rotation_slider = sanitize_text_field( wp_unslash( $_POST['wd_frame_rotation_slider'][ $variation_id ] ) );
	}
	if ( isset( $_POST['wd_frame_show_upload'][ $variation_id ] ) ) {
		$show_upload = 'yes';
	}
	if ( isset( $_POST['wd_frame_show_artwork_size'][ $variation_id ] ) ) {
		$show_artwork_size = 'yes';
	}
	if ( isset( $_POST['wd_frame_show_scene'][ $variation_id ] ) ) {
		$show_scene = 'yes';
	}
	if ( isset( $_POST['wd_frame_show_style'][ $variation_id ] ) ) {
		$show_style = 'yes';
	}
	if ( isset( $_POST['wd_frame_show_matting'][ $variation_id ] ) ) {
		$show_matting = 'yes';
	}
	if ( isset( $_POST['wd_frame_rotation_enabled'][ $variation_id ] ) ) {
		$rotation = 'yes';
	}

	update_post_meta( $variation_id, '_wd_frame_scene_options', $scene_value );
	update_post_meta( $variation_id, '_wd_frame_style_options', $style_value );
	update_post_meta( $variation_id, '_wd_frame_artwork_sizes', $size_value );
	update_post_meta( $variation_id, '_wd_frame_depth_options', $depth_value );
	update_post_meta( $variation_id, '_wd_frame_mat_colors', $mat_value );
	update_post_meta( $variation_id, '_wd_frame_mat_slider', $mat_slider );
	update_post_meta( $variation_id, '_wd_frame_rotation_slider', $rotation_slider );
	update_post_meta( $variation_id, '_wd_frame_show_upload', $show_upload );
	update_post_meta( $variation_id, '_wd_frame_show_artwork_size', $show_artwork_size );
	update_post_meta( $variation_id, '_wd_frame_show_scene', $show_scene );
	update_post_meta( $variation_id, '_wd_frame_show_style', $show_style );
	update_post_meta( $variation_id, '_wd_frame_show_matting', $show_matting );
	update_post_meta( $variation_id, '_wd_frame_rotation_enabled', $rotation );
}
add_action( 'woocommerce_save_product_variation', 'wd_save_frame_visualizer_variation_fields', 20, 1 );

/**
 * Add media picker button near frame image fields in product edit.
 */
function wd_frame_visualizer_admin_media_script() {
	global $post;
	if ( ! $post || 'product' !== get_post_type( $post ) ) {
		return;
	}
	if ( ! wd_is_frame_product( (int) $post->ID ) ) {
		return;
	}
	?>
	<script>
	(function($){
		var builderCss = '' +
			'.wd-mat-color-builder .wd-mat-row input[type="text"]{width:100%;max-width:100%;box-sizing:border-box;}' +
			'.wd-mat-color-builder .wd-mat-row input[type="color"]{width:100%;min-width:70px;height:34px;padding:0;border:1px solid #dcdcde;background:#fff;box-sizing:border-box;}' +
			'.wd-mat-color-builder .wd-mat-row{display:grid;grid-template-columns:90px minmax(160px,1fr) 90px auto;gap:8px;margin-bottom:8px;align-items:center;}' +
			'.wd-mat-color-builder .button-link-delete{white-space:nowrap;}' +
			'.wd-mat-color-builder .wd-presets-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px 12px;margin-top:6px;}' +
			'.wd-mat-color-builder .wd-preset-item{display:flex;align-items:center;gap:8px;min-width:0;}' +
			'.wd-mat-color-builder .wd-preset-item span.wd-label{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;}' +
			'@media (max-width:1280px){.wd-mat-color-builder .wd-mat-row{grid-template-columns:1fr;}}';
		if (!document.getElementById('wd-visualizer-builder-style')) {
			$('<style id="wd-visualizer-builder-style"></style>').text(builderCss).appendTo(document.head);
		}

		var predefinedMatColors = [
			{ id: '1', label: 'White', color: '#ffffff' },
			{ id: '2', label: 'Black', color: '#222222' },
			{ id: '18', label: 'Antique White', color: '#fff7eb' },
			{ id: '7', label: 'Cream', color: '#f4edd2' },
			{ id: '8', label: 'Cool Gray', color: '#b8bec2' },
			{ id: '9', label: 'Charcoal', color: '#5c5c5c' }
		];

		function parseRows(text, expectedParts) {
			var rows = [];
			(text || '').split(/\r?\n/).forEach(function(line){
				line = (line || '').trim();
				if (!line) return;
				var parts = line.split('|');
				var row = [];
				for (var i = 0; i < expectedParts; i++) {
					row.push((parts[i] || '').trim());
				}
				rows.push(row);
			});
			return rows;
		}

		function openMediaPicker(multiple, callback) {
			var frame = wp.media({ title: 'Select image', multiple: !!multiple, library: { type: 'image' } });
			frame.on('select', function(){
				var selection = frame.state().get('selection');
				var items = [];
				selection.each(function(att){ items.push(att.toJSON()); });
				callback(items);
			});
			frame.open();
		}

		function buildSceneStyleBuilder($textarea, mode) {
			if ($textarea.data('wd-builder-bound')) return;
			$textarea.data('wd-builder-bound', true).hide();

			var title = mode === 'scene' ? 'Frame Scenes' : 'Frame Styles';
			var keyLabel = mode === 'scene' ? 'Scene Key' : 'Style ID';
			var $box = $('<div class="wd-visualizer-builder"></div>');
			var $head = $('<div style="margin:6px 0 8px;"><strong>'+title+'</strong></div>');
			var $rows = $('<div class="wd-rows"></div>');
			var $btnAdd = $('<button type="button" class="button">Add Row</button>');
			var $btnMulti = $('<button type="button" class="button" style="margin-left:8px;">Add Multiple Images</button>');

			function slugify(str) {
				return String(str || '')
					.toLowerCase()
					.replace(/[^a-z0-9]+/g, '_')
					.replace(/^_+|_+$/g, '')
					.substring(0, 40);
			}

			function generateAutoKey(label, idx) {
				if (mode === 'scene') {
					return slugify(label) || ('scene_' + (idx + 1));
				}
				return String(1000 + idx);
			}

			function rowTemplate(key, label, image) {
				var $row = $('<div class="wd-row" style="display:grid;grid-template-columns:120px 1fr 180px auto;gap:8px;margin-bottom:8px;align-items:center;"></div>');
				var initialKey = (key || '').trim();
				var hasCustomKey = !!initialKey;
				var autoKey = initialKey;
				var $key = $('<input type="text" class="regular-text" placeholder="'+keyLabel+'" readonly>');
				var $label = $('<input type="text" class="regular-text" placeholder="Label">').val(label || '');
				var $imgWrap = $('<div style="display:flex;align-items:center;gap:8px;"></div>');
				var $preview = $('<img alt="" style="width:42px;height:42px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:none;">');
				var $pick = $('<button type="button" class="button button-small">Image</button>');
				var $unlock = $('<button type="button" class="button button-small">Custom ID</button>');
				var $remove = $('<button type="button" class="button-link-delete">Remove</button>');
				var imageUrl = image || '';

				function refreshAutoKey() {
					if (!hasCustomKey) {
						autoKey = generateAutoKey($label.val(), $rows.find('.wd-row').index($row));
						$key.val(autoKey);
					}
				}

				function syncPreview() {
					if (imageUrl) { $preview.attr('src', imageUrl).show(); } else { $preview.hide(); }
					refreshAutoKey();
					serialize();
				}

				$pick.on('click', function(e){
					e.preventDefault();
					openMediaPicker(false, function(items){
						if (items[0] && items[0].url) {
							imageUrl = items[0].url;
							if (!$label.val()) $label.val(items[0].title || '');
							syncPreview();
						}
					});
				});
				$unlock.on('click', function(e){
					e.preventDefault();
					hasCustomKey = true;
					$key.prop('readonly', false).focus();
				});
				$remove.on('click', function(e){ e.preventDefault(); $row.remove(); serialize(); });
				$key.on('input', serialize);
				$label.on('input', function(){ refreshAutoKey(); serialize(); });

				$imgWrap.append($preview, $pick, $unlock);
				$row.append($key, $label, $imgWrap, $remove);
				$key.val(initialKey);
				$row.data('getLine', function(){
					var k = ($key.val() || '').trim();
					var l = ($label.val() || '').trim();
					var i = (imageUrl || '').trim();
					if (!k || !i) return '';
					return [k, l, i].join('|');
				});
				$rows.append($row);
				refreshAutoKey();
				syncPreview();
			}

			function serialize() {
				var lines = [];
				$rows.find('.wd-row').each(function(){
					var line = $(this).data('getLine')();
					if (line) lines.push(line);
				});
				$textarea.val(lines.join('\n')).trigger('change');
			}

			$btnAdd.on('click', function(e){ e.preventDefault(); rowTemplate('', '', ''); });
			$btnMulti.on('click', function(e){
				e.preventDefault();
				openMediaPicker(true, function(items){
					items.forEach(function(item, idx){
						var autoKey = mode === 'scene' ? 'scene_' + Math.floor(Date.now()/1000) + '_' + idx : String(1000 + idx);
						rowTemplate(autoKey, item.title || '', item.url || '');
					});
					serialize();
				});
			});

			($textarea.val() || '').split(/\r?\n/).forEach(function(line){
				line = (line || '').trim();
				if (!line) return;
				var parts = line.split('|');
				var key = (parts[0] || '').trim();
				var label = (parts[1] || '').trim();
				var image = (parts.slice(2).join('|') || '').trim();
				if (!image) {
					var m = line.match(/https?:\/\/\S+/i);
					if (m && m[0]) image = m[0].trim();
				}
				rowTemplate(key, label, image);
			});
			$box.append($head, $rows, $('<div style="margin:8px 0 14px;"></div>').append($btnAdd, $btnMulti));
			$textarea.after($box);
		}

		function buildMatColorBuilder($textarea) {
			if ($textarea.data('wd-builder-bound')) return;
			$textarea.data('wd-builder-bound', true).hide();

			var $box = $('<div class="wd-mat-color-builder"></div>');
			var $presetWrap = $('<div style="margin:8px 0;"></div>');
			var $customWrap = $('<div style="margin:8px 0;"></div>');
			var $addCustom = $('<button type="button" class="button">Add Custom Color</button>');

			function getAllRows() {
				return parseRows($textarea.val(), 3);
			}

			function renderCustomRow(id, label, color) {
				var $row = $('<div class="wd-mat-row"></div>');
				var $id = $('<input type="text" class="regular-text" placeholder="ID">').val(id || '');
				var $label = $('<input type="text" class="regular-text" placeholder="Label">').val(label || '');
				var $color = $('<input type="color">').val(color || '#ffffff');
				var $remove = $('<button type="button" class="button-link-delete">Remove</button>');
				$remove.on('click', function(e){ e.preventDefault(); $row.remove(); serialize(); });
				$id.on('input', serialize); $label.on('input', serialize); $color.on('input', serialize);
				$row.append($id, $label, $color, $remove);
				$row.data('line', function(){
					var v1 = ($id.val() || '').trim();
					var v2 = ($label.val() || '').trim();
					var v3 = ($color.val() || '').trim();
					if (!v1 || !v3) return '';
					return [v1, v2, v3].join('|');
				});
				$customWrap.append($row);
			}

			function serialize() {
				var lines = [];
				$presetWrap.find('input[type="checkbox"]:checked').each(function(){
					var p = $(this).data('preset');
					lines.push([p.id, p.label, p.color].join('|'));
				});
				$customWrap.find('.wd-mat-row').each(function(){
					var line = $(this).data('line')();
					if (line) lines.push(line);
				});
				$textarea.val(lines.join('\n')).trigger('change');
			}

			var current = getAllRows();
			var selectedPresetIds = {};
			current.forEach(function(r){ selectedPresetIds[r[0]] = true; });

			$presetWrap.append('<div style="margin-bottom:6px;"><strong>Mat Colors (Predefined)</strong></div>');
			var $presetGrid = $('<div class="wd-presets-grid"></div>');
			predefinedMatColors.forEach(function(preset){
				var $label = $('<label class="wd-preset-item"></label>');
				var $check = $('<input type="checkbox">').prop('checked', !!selectedPresetIds[preset.id]).data('preset', preset);
				var $chip = $('<span style="display:inline-block;width:14px;height:14px;border:1px solid #ccc;border-radius:2px;"></span>').css('background', preset.color);
				$check.on('change', serialize);
				$label.append($check, $chip, $('<span class="wd-label"></span>').text(preset.label));
				$presetGrid.append($label);
			});
			$presetWrap.append($presetGrid);

			$customWrap.append('<div style="margin:10px 0 6px;"><strong>Custom Colors</strong></div>');
			current.forEach(function(r){
				var isPreset = predefinedMatColors.some(function(p){ return p.id === r[0]; });
				if (!isPreset) renderCustomRow(r[0], r[1], r[2]);
			});
			$addCustom.on('click', function(e){ e.preventDefault(); renderCustomRow('', '', '#ffffff'); serialize(); });

			$box.append($presetWrap, $customWrap, $('<div style="margin:8px 0 14px;"></div>').append($addCustom));
			$textarea.after($box);
			serialize();
		}

		function buildArtworkSizeBuilder($textarea) {
			if ($textarea.data('wd-builder-bound')) return;
			$textarea.data('wd-builder-bound', true).hide();

			var $box = $('<div class="wd-size-builder"></div>');
			var $rows = $('<div></div>');
			var $add = $('<button type="button" class="button">Add Size</button>');
			var presets = [
				['12" x 8"', '12', '8'],
				['16" x 12"', '16', '12'],
				['20" x 16"', '20', '16'],
				['24" x 18"', '24', '18']
			];

			function serialize() {
				var lines = [];
				$rows.find('.wd-size-row').each(function() {
					var line = $(this).data('line')();
					if (line) lines.push(line);
				});
				$textarea.val(lines.join('\n')).trigger('change');
			}

			function addRow(label, width, height) {
				var $row = $('<div class="wd-size-row" style="display:grid;grid-template-columns:1fr 90px 90px auto;gap:8px;margin-bottom:8px;align-items:center;"></div>');
				var $label = $('<input type="text" class="regular-text" placeholder="Label (e.g. 12 x 8)">').val(label || '');
				var $w = $('<input type="number" min="1" step="1" placeholder="W">').val(width || '');
				var $h = $('<input type="number" min="1" step="1" placeholder="H">').val(height || '');
				var $remove = $('<button type="button" class="button-link-delete">Remove</button>');
				$remove.on('click', function(e){ e.preventDefault(); $row.remove(); serialize(); });
				$label.on('input', serialize); $w.on('input', serialize); $h.on('input', serialize);
				$row.data('line', function(){
					var l = ($label.val() || '').trim();
					var w = ($w.val() || '').trim();
					var h = ($h.val() || '').trim();
					if (!w || !h) return '';
					if (!l) l = w + '" x ' + h + '"';
					return [l, w, h].join('|');
				});
				$row.append($label, $w, $h, $remove);
				$rows.append($row);
			}

			var existing = parseRows($textarea.val(), 3);
			if (existing.length) {
				existing.forEach(function(r){ addRow(r[0], r[1], r[2]); });
			} else {
				presets.forEach(function(r){ addRow(r[0], r[1], r[2]); });
			}

			$add.on('click', function(e){ e.preventDefault(); addRow('', '', ''); serialize(); });
			$box.append('<div style="margin:6px 0 8px;"><strong>Artwork Sizes</strong></div>', $rows, $('<div style="margin:8px 0 14px;"></div>').append($add));
			$textarea.after($box);
			serialize();
		}

		function buildDepthBuilder($textarea) {
			if ($textarea.data('wd-builder-bound')) return;
			$textarea.data('wd-builder-bound', true).hide();
			var $box = $('<div class="wd-depth-builder"></div>');
			var $rows = $('<div></div>');
			var $add = $('<button type="button" class="button">Add Depth</button>');

			function serialize() {
				var lines = [];
				$rows.find('.wd-depth-row').each(function() {
					var line = $(this).data('line')();
					if (line) lines.push(line);
				});
				$textarea.val(lines.join('\n')).trigger('change');
			}

			function addRow(value, label) {
				var $row = $('<div class="wd-depth-row" style="display:grid;grid-template-columns:120px 1fr auto;gap:8px;margin-bottom:8px;align-items:center;"></div>');
				var $value = $('<input type="number" step="0.125" min="0" placeholder="Value">').val(value || '');
				var $label = $('<input type="text" class="regular-text" placeholder="Label (e.g. 1.125 in)">').val(label || '');
				var $remove = $('<button type="button" class="button-link-delete">Remove</button>');
				$remove.on('click', function(e){ e.preventDefault(); $row.remove(); serialize(); });
				$value.on('input', serialize); $label.on('input', serialize);
				$row.data('line', function() {
					var v = ($value.val() || '').trim();
					var l = ($label.val() || '').trim();
					if (!v) return '';
					if (!l) l = v + '"';
					return [v, l].join('|');
				});
				$row.append($value, $label, $remove);
				$rows.append($row);
			}

			var existing = parseRows($textarea.val(), 2);
			if (existing.length) {
				existing.forEach(function(r){ addRow(r[0], r[1]); });
			} else {
				addRow('0.75', '0.75"');
				addRow('1.625', '1.625"');
			}

			$add.on('click', function(e){ e.preventDefault(); addRow('', ''); serialize(); });
			$box.append('<div style="margin:6px 0 8px;"><strong>Frame Depth Options</strong></div>', $rows, $('<div style="margin:8px 0 14px;"></div>').append($add));
			$textarea.after($box);
			serialize();
		}

		function buildSliderBuilder($input, title, defaults) {
			if ($input.data('wd-builder-bound')) return;
			$input.data('wd-builder-bound', true).hide();

			function parseRaw(raw) {
				var parts = String(raw || '').split('|');
				return {
					min: (parts[0] || defaults.min),
					max: (parts[1] || defaults.max),
					step: (parts[2] || defaults.step),
					start: (parts[3] || defaults.start)
				};
			}

			var v = parseRaw($input.val());
			var $box = $('<div class="wd-slider-builder" style="margin:6px 0 14px;"></div>');
			var $grid = $('<div style="display:grid;grid-template-columns:repeat(4,120px);gap:8px;align-items:end;"></div>');
			var $min = $('<input type="number" step="0.1">').val(v.min);
			var $max = $('<input type="number" step="0.1">').val(v.max);
			var $step = $('<input type="number" step="0.1" min="0.1">').val(v.step);
			var $start = $('<input type="number" step="0.1">').val(v.start);

			function wrap(label, input) {
				var $w = $('<label style="display:flex;flex-direction:column;gap:4px;font-weight:600;"></label>');
				$w.append($('<span></span>').text(label), input);
				return $w;
			}

			function serialize() {
				$input.val([($min.val()||''), ($max.val()||''), ($step.val()||''), ($start.val()||'')].join('|')).trigger('change');
			}
			$min.on('input', serialize); $max.on('input', serialize); $step.on('input', serialize); $start.on('input', serialize);
			$grid.append(wrap('Min', $min), wrap('Max', $max), wrap('Step', $step), wrap('Start', $start));
			$box.append('<div style="margin-bottom:8px;"><strong>'+title+'</strong></div>', $grid);
			$input.after($box);
			serialize();
		}

		function attachVisualizerBuilders() {
			$('textarea[name^="_wd_frame_scene_options"], textarea[name^="wd_frame_scene_options"]').each(function(){ buildSceneStyleBuilder($(this), 'scene'); });
			$('textarea[name^="_wd_frame_style_options"], textarea[name^="wd_frame_style_options"]').each(function(){ buildSceneStyleBuilder($(this), 'style'); });
			$('textarea[name^="_wd_frame_mat_colors"], textarea[name^="wd_frame_mat_colors"]').each(function(){ buildMatColorBuilder($(this)); });
			$('textarea[name^="_wd_frame_artwork_sizes"], textarea[name^="wd_frame_artwork_sizes"]').each(function(){ buildArtworkSizeBuilder($(this)); });
			$('textarea[name^="_wd_frame_depth_options"], textarea[name^="wd_frame_depth_options"]').each(function(){ buildDepthBuilder($(this)); });
			$('input[name^="_wd_frame_mat_slider"], input[name^="wd_frame_mat_slider"]').each(function(){ buildSliderBuilder($(this), 'Mat Slider Settings', { min: 0, max: 6, step: 0.5, start: 2 }); });
			$('input[name^="_wd_frame_rotation_slider"], input[name^="wd_frame_rotation_slider"]').each(function(){ buildSliderBuilder($(this), 'Rotation Slider Settings', { min: 0, max: 55, step: 1, start: 30 }); });
		}

		$(document).ready(attachVisualizerBuilders);
		$(document).on('woocommerce_variations_loaded woocommerce_variations_added', attachVisualizerBuilders);
	})(jQuery);
	</script>
	<?php
}
add_action( 'admin_footer-post.php', 'wd_frame_visualizer_admin_media_script' );
add_action( 'admin_footer-post-new.php', 'wd_frame_visualizer_admin_media_script' );

/**
 * Ensure media library is available on product edit for upload buttons.
 */
function wd_frame_visualizer_enqueue_admin_media( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'product' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'wd_frame_visualizer_enqueue_admin_media' );

/**
 * Persist visualizer selections in cart item data.
 *
 * @param array $cart_item_data Existing cart item data.
 * @param int   $product_id     Product ID.
 * @param int   $variation_id   Variation ID.
 * @param array $variation      Selected variation attributes.
 * @return array
 */
function wd_capture_frame_visualizer_cart_item_data( $cart_item_data, $product_id, $variation_id = 0, $variation = array() ) {
	if ( ! wd_is_frame_product( $product_id ) ) {
		return $cart_item_data;
	}

	$raw = array();
	$json_source = '';
	if ( isset( $_POST['wd_frame_visualizer_data'] ) ) {
		$json_source = wp_unslash( $_POST['wd_frame_visualizer_data'] );
	} elseif ( isset( $_REQUEST['wd_frame_visualizer_data'] ) ) {
		$json_source = wp_unslash( $_REQUEST['wd_frame_visualizer_data'] );
	}
	if ( is_string( $json_source ) && '' !== $json_source ) {
		$json_payload = $json_source;
		if ( is_string( $json_payload ) && '' !== $json_payload ) {
			$decoded_payload = json_decode( $json_payload, true );
			if ( is_array( $decoded_payload ) ) {
				$raw = $decoded_payload;
			}
		}
	}

	if ( isset( $_REQUEST['cart']['cart_items_attributes'][0] ) && is_array( $_REQUEST['cart']['cart_items_attributes'][0] ) ) {
		$raw = wp_unslash( $_REQUEST['cart']['cart_items_attributes'][0] );
	}

	$meta = array();

	$artwork_width  = isset( $raw['artwork_width'] ) ? sanitize_text_field( (string) $raw['artwork_width'] ) : '';
	$artwork_height = isset( $raw['artwork_height'] ) ? sanitize_text_field( (string) $raw['artwork_height'] ) : '';
	$artwork_size_label = isset( $raw['artwork_size_label'] ) ? sanitize_text_field( (string) $raw['artwork_size_label'] ) : '';
	if ( '' !== $artwork_width && '' !== $artwork_height ) {
		$meta[] = array(
			'label' => 'Art Work Size',
			'value' => $artwork_width . '" x ' . $artwork_height . '"',
		);
	} elseif ( '' !== $artwork_size_label ) {
		$meta[] = array(
			'label' => 'Art Work Size',
			'value' => $artwork_size_label,
		);
	}

	$frame_style = isset( $raw['profile'] ) ? sanitize_text_field( (string) $raw['profile'] ) : '';
	if ( '' === $frame_style && ! empty( $raw['frame_color'] ) ) {
		$frame_style = sanitize_text_field( (string) $raw['frame_color'] );
	}
	$frame_depth = isset( $raw['frame_width'] ) ? sanitize_text_field( (string) $raw['frame_width'] ) : '';
	if ( '' === $frame_depth && ! empty( $raw['frame_depth_label'] ) ) {
		$frame_depth = sanitize_text_field( str_replace( '"', '', (string) $raw['frame_depth_label'] ) );
	}
	if ( '' !== $frame_style ) {
		$frame_value = $frame_style;
		if ( '' !== $frame_depth ) {
			$frame_value .= ' (Depth: ' . $frame_depth . '")';
		}
		$meta[] = array(
			'label' => 'Frame Style',
			'value' => $frame_value,
		);
	}

	$mat_color = isset( $raw['mat_color'] ) ? sanitize_text_field( (string) $raw['mat_color'] ) : '';
	$mat_bar   = isset( $raw['mat_width'] ) ? sanitize_text_field( (string) $raw['mat_width'] ) : '';
	if ( '' === $mat_bar && isset( $raw['mat_top_width'] ) ) {
		$mat_bar = sanitize_text_field( (string) $raw['mat_top_width'] );
	}
	if ( '' === $mat_bar && isset( $raw['mat_bar_value'] ) ) {
		$mat_bar = sanitize_text_field( (string) $raw['mat_bar_value'] );
	}
	if ( '' !== $mat_color || '' !== $mat_bar ) {
		$mat_value = $mat_color;
		if ( '' !== $mat_bar ) {
			$mat_value .= ( '' !== $mat_value ? ' | ' : '' ) . 'Bar: ' . $mat_bar . '"';
		}
		$meta[] = array(
			'label' => 'Mat Color',
			'value' => $mat_value,
		);
	}

	$scene_name = isset( $raw['scene_name'] ) ? sanitize_text_field( (string) $raw['scene_name'] ) : '';
	if ( '' !== $scene_name ) {
		$meta[] = array(
			'label' => 'Background Selection',
			'value' => $scene_name,
		);
	}

	if ( ! empty( $meta ) ) {
		$cart_item_data['wd_frame_visualizer_meta'] = $meta;
		$cart_item_data['wd_frame_visualizer_key']  = md5( wp_json_encode( $meta ) . microtime() );
	}

	if ( ! empty( $variation_id ) ) {
		$cart_item_data['wd_selected_variation_id'] = absint( $variation_id );
	}

	$uploaded_selected = ! empty( $raw['uploaded_artwork_selected'] ) && '1' === (string) $raw['uploaded_artwork_selected'];

	if ( ! empty( $raw['thumb_url'] ) && ! $uploaded_selected ) {
		$thumb_url = esc_url_raw( (string) $raw['thumb_url'] );
		if ( $thumb_url && empty( $cart_item_data['wd_scene_background_url'] ) ) {
			// Keep generated thumb as scene/render preview fallback, not uploaded artwork.
			$cart_item_data['wd_scene_background_url'] = $thumb_url;
		}
	}
	if ( ! empty( $raw['uploaded_artwork_url'] ) ) {
		$uploaded_artwork_url = trim( (string) $raw['uploaded_artwork_url'] );
		$is_blob_url          = 0 === strpos( $uploaded_artwork_url, 'blob:' );
		if ( $is_blob_url ) {
			$cart_item_data['wd_uploaded_artwork_blob_url'] = $uploaded_artwork_url;
		} else {
			$uploaded_artwork_url = esc_url_raw( $uploaded_artwork_url );
		}
		if ( $uploaded_artwork_url && ! $is_blob_url ) {
			$cart_item_data['wd_uploaded_artwork_url'] = $uploaded_artwork_url;
		}
	}
	if ( empty( $cart_item_data['wd_uploaded_artwork_url'] ) && ! empty( $raw['uploaded_artwork_data_url'] ) ) {
		$data_url = (string) $raw['uploaded_artwork_data_url'];
		$saved    = wd_store_frame_data_url_image( $data_url );
		if ( $saved ) {
			$cart_item_data['wd_uploaded_artwork_url'] = $saved;
		}
	}
	if ( ! empty( $raw['scene_image_url'] ) && ! $uploaded_selected ) {
		$scene_image_url = esc_url_raw( (string) $raw['scene_image_url'] );
		if ( $scene_image_url ) {
			$cart_item_data['wd_scene_background_url'] = $scene_image_url;
		}
	}

	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'wd_capture_frame_visualizer_cart_item_data', 20, 4 );

/**
 * Persist a data:image URL as a media attachment and return URL.
 *
 * @param string $data_url Base64 data URL.
 * @return string
 */
function wd_store_frame_data_url_image( $data_url ) {
	$data_url = trim( (string) $data_url );
	if ( '' === $data_url || 0 !== strpos( $data_url, 'data:image/' ) ) {
		return '';
	}

	if ( ! preg_match( '#^data:image/([a-zA-Z0-9+]+);base64,(.*)$#', $data_url, $matches ) ) {
		return '';
	}

	$ext_map = array(
		'jpeg' => 'jpg',
		'jpg'  => 'jpg',
		'png'  => 'png',
		'gif'  => 'gif',
		'webp' => 'webp',
		'avif' => 'avif',
	);
	$ext = strtolower( $matches[1] );
	$ext = isset( $ext_map[ $ext ] ) ? $ext_map[ $ext ] : 'jpg';

	$binary = base64_decode( $matches[2], true );
	if ( false === $binary || strlen( $binary ) < 64 ) {
		return '';
	}
	if ( strlen( $binary ) > 20 * 1024 * 1024 ) {
		return '';
	}

	$filename = 'frame-artwork-' . wp_generate_password( 10, false, false ) . '.' . $ext;
	$upload   = wp_upload_bits( $filename, null, $binary );
	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
		return '';
	}

	$filetype = wp_check_filetype( $filename, null );
	$attach   = array(
		'post_mime_type' => ! empty( $filetype['type'] ) ? $filetype['type'] : 'image/' . $ext,
		'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attachment_id = wp_insert_attachment( $attach, $upload['file'] );
	if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
		return '';
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	if ( $metadata ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}

	$url = wp_get_attachment_url( $attachment_id );
	return $url ? esc_url_raw( $url ) : '';
}

/**
 * Show frame visualizer selections in cart/checkout.
 *
 * @param array $item_data Existing item data.
 * @param array $cart_item Cart item.
 * @return array
 */
function wd_render_frame_visualizer_cart_item_data( $item_data, $cart_item ) {
	if ( empty( $cart_item['wd_frame_visualizer_meta'] ) || ! is_array( $cart_item['wd_frame_visualizer_meta'] ) ) {
		return $item_data;
	}

	foreach ( $cart_item['wd_frame_visualizer_meta'] as $row ) {
		if ( empty( $row['label'] ) || ! isset( $row['value'] ) ) {
			continue;
		}
		$item_data[] = array(
			'name'  => wp_kses_post( $row['label'] ),
			'value' => wp_kses_post( $row['value'] ),
		);
	}

	if ( ! empty( $cart_item['wd_uploaded_artwork_url'] ) || ! empty( $cart_item['wd_uploaded_artwork_blob_url'] ) ) {
		$url      = ! empty( $cart_item['wd_uploaded_artwork_url'] ) ? esc_url( $cart_item['wd_uploaded_artwork_url'] ) : esc_attr( (string) $cart_item['wd_uploaded_artwork_blob_url'] );
		$is_blob  = ! empty( $cart_item['wd_uploaded_artwork_blob_url'] ) && empty( $cart_item['wd_uploaded_artwork_url'] );
		$img_html   = '<a href="' . $url . '" target="_blank" rel="noopener noreferrer"><img src="' . $url . '" alt="Uploaded artwork" style="max-width:70px; height:auto; border:1px solid #ddd; border-radius:4px;" /></a>';
		if ( $is_blob ) {
			$img_html = '<span style="display:inline-block;padding:4px 8px;border:1px solid #ddd;border-radius:4px;font-size:12px;color:#555;">Uploaded from device</span>';
		}
		$item_data[] = array(
			'name'    => 'Uploaded Artwork',
			'value'   => $is_blob ? 'Uploaded from device (local preview)' : $url,
			'display' => wp_kses_post( $img_html ),
		);
	}
	if ( empty( $cart_item['wd_uploaded_artwork_url'] ) && empty( $cart_item['wd_uploaded_artwork_blob_url'] ) && ! empty( $cart_item['wd_scene_background_url'] ) ) {
		$url      = esc_url( $cart_item['wd_scene_background_url'] );
		$img_html = '<a href="' . $url . '" target="_blank" rel="noopener noreferrer"><img src="' . $url . '" alt="Scene background" style="max-width:70px; height:auto; border:1px solid #ddd; border-radius:4px;" /></a>';
		$item_data[] = array(
			'name'    => 'Scene Background Image',
			'value'   => $url,
			'display' => wp_kses_post( $img_html ),
		);
	}

	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'wd_render_frame_visualizer_cart_item_data', 20, 2 );

/**
 * Save frame visualizer selections to order item meta.
 *
 * @param WC_Order_Item_Product $item          Order item.
 * @param string                $cart_item_key Cart item key.
 * @param array                 $values        Cart values.
 * @param WC_Order              $order         Order object.
 */
function wd_save_frame_visualizer_order_item_meta( $item, $cart_item_key, $values, $order ) {
	if ( empty( $values['wd_frame_visualizer_meta'] ) || ! is_array( $values['wd_frame_visualizer_meta'] ) ) {
		return;
	}

	foreach ( $values['wd_frame_visualizer_meta'] as $row ) {
		if ( empty( $row['label'] ) || ! isset( $row['value'] ) ) {
			continue;
		}
		$item->add_meta_data( sanitize_text_field( $row['label'] ), sanitize_text_field( (string) $row['value'] ), true );
	}

	if ( ! empty( $values['wd_uploaded_artwork_url'] ) ) {
		$item->add_meta_data( '_wd_uploaded_artwork_url', esc_url_raw( (string) $values['wd_uploaded_artwork_url'] ), true );
	}
	if ( ! empty( $values['wd_scene_background_url'] ) ) {
		$item->add_meta_data( '_wd_scene_background_url', esc_url_raw( (string) $values['wd_scene_background_url'] ), true );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'wd_save_frame_visualizer_order_item_meta', 20, 4 );

/**
 * Hide internal artwork URL meta key from default meta list.
 *
 * @param array $hidden Hidden order item meta keys.
 * @return array
 */
function wd_hide_uploaded_artwork_internal_meta( $hidden ) {
	$hidden[] = '_wd_uploaded_artwork_url';
	$hidden[] = '_wd_scene_background_url';
	$hidden[] = '_wd_uploaded_artwork_blob_url';
	return $hidden;
}
add_filter( 'woocommerce_hidden_order_itemmeta', 'wd_hide_uploaded_artwork_internal_meta' );

/**
 * Render uploaded artwork preview in admin order item section.
 *
 * @param int                   $item_id Order item ID.
 * @param WC_Order_Item_Product $item    Order item object.
 * @param WC_Product            $product Product object.
 */
function wd_render_uploaded_artwork_admin_preview( $item_id, $item, $product ) {
	$uploaded_url = wc_get_order_item_meta( $item_id, '_wd_uploaded_artwork_url', true );
	$scene_url    = wc_get_order_item_meta( $item_id, '_wd_scene_background_url', true );

	if ( $uploaded_url ) {
		$uploaded_url = esc_url( $uploaded_url );
		echo '<p class="wd-uploaded-artwork-preview" style="margin-top:8px;">';
		echo '<strong>' . esc_html__( 'Uploaded Artwork', 'woodmart' ) . ':</strong><br />';
		echo '<a href="' . $uploaded_url . '" target="_blank" rel="noopener noreferrer">';
		echo '<img src="' . $uploaded_url . '" alt="' . esc_attr__( 'Uploaded Artwork', 'woodmart' ) . '" style="max-width:120px;height:auto;border:1px solid #ddd;border-radius:4px;" />';
		echo '</a>';
		echo '</p>';
	}

	if ( $scene_url ) {
		$scene_url = esc_url( $scene_url );
		echo '<p class="wd-scene-background-preview" style="margin-top:8px;">';
		echo '<strong>' . esc_html__( 'Scene Background Image', 'woodmart' ) . ':</strong><br />';
		echo '<a href="' . $scene_url . '" target="_blank" rel="noopener noreferrer">';
		echo '<img src="' . $scene_url . '" alt="' . esc_attr__( 'Scene Background', 'woodmart' ) . '" style="max-width:120px;height:auto;border:1px solid #ddd;border-radius:4px;" />';
		echo '</a>';
		echo '</p>';
	}
}
add_action( 'woocommerce_after_order_itemmeta', 'wd_render_uploaded_artwork_admin_preview', 20, 3 );

/**
 * Show uploaded artwork in order details and emails.
 *
 * @param int                   $item_id       Order item ID.
 * @param WC_Order_Item_Product $item          Order item object.
 * @param WC_Order              $order         Order object.
 * @param bool                  $plain_text    Plain text email flag.
 */
function wd_render_uploaded_artwork_in_order_outputs( $item_id, $item, $order, $plain_text ) {
	$uploaded_url = wc_get_order_item_meta( $item_id, '_wd_uploaded_artwork_url', true );
	$scene_url    = wc_get_order_item_meta( $item_id, '_wd_scene_background_url', true );

	if ( $plain_text ) {
		if ( $uploaded_url ) {
			echo "\nUploaded Artwork: " . esc_url_raw( $uploaded_url ) . "\n";
		}
		if ( $scene_url ) {
			echo "\nScene Background Image: " . esc_url_raw( $scene_url ) . "\n";
		}
		return;
	}

	if ( $uploaded_url ) {
		$uploaded_url = esc_url( $uploaded_url );
		echo '<div class="wd-uploaded-artwork-email" style="margin-top:8px;">';
		echo '<strong>' . esc_html__( 'Uploaded Artwork', 'woodmart' ) . ':</strong><br />';
		echo '<a href="' . $uploaded_url . '" target="_blank" rel="noopener noreferrer">';
		echo '<img src="' . $uploaded_url . '" alt="' . esc_attr__( 'Uploaded Artwork', 'woodmart' ) . '" style="max-width:90px;height:auto;border:1px solid #ddd;border-radius:4px;" />';
		echo '</a>';
		echo '</div>';
	}

	if ( $scene_url ) {
		$scene_url = esc_url( $scene_url );
		echo '<div class="wd-scene-background-email" style="margin-top:8px;">';
		echo '<strong>' . esc_html__( 'Scene Background Image', 'woodmart' ) . ':</strong><br />';
		echo '<a href="' . $scene_url . '" target="_blank" rel="noopener noreferrer">';
		echo '<img src="' . $scene_url . '" alt="' . esc_attr__( 'Scene Background', 'woodmart' ) . '" style="max-width:90px;height:auto;border:1px solid #ddd;border-radius:4px;" />';
		echo '</a>';
		echo '</div>';
	}
}
add_action( 'woocommerce_order_item_meta_end', 'wd_render_uploaded_artwork_in_order_outputs', 20, 4 );

/**
 * AJAX: upload frame artwork file and return permanent media URL.
 */
function wd_upload_frame_artwork_ajax() {
	if ( empty( $_FILES['artwork_file'] ) || empty( $_FILES['artwork_file']['tmp_name'] ) ) {
		wp_send_json_error( array( 'message' => 'No file uploaded.' ) );
	}

	$file = $_FILES['artwork_file'];
	if ( ! empty( $file['size'] ) && (int) $file['size'] > 20 * 1024 * 1024 ) {
		wp_send_json_error( array( 'message' => 'File too large. Max 20MB.' ) );
	}

	$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
	$mime  = ! empty( $check['type'] ) ? $check['type'] : '';
	if ( 0 !== strpos( (string) $mime, 'image/' ) ) {
		wp_send_json_error( array( 'message' => 'Only image files are allowed.' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$upload_overrides = array(
		'test_form' => false,
		'mimes'     => array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'gif'          => 'image/gif',
			'webp'         => 'image/webp',
			'avif'         => 'image/avif',
		),
	);

	$moved = wp_handle_upload( $file, $upload_overrides );
	if ( empty( $moved['file'] ) || ! empty( $moved['error'] ) ) {
		wp_send_json_error( array( 'message' => ! empty( $moved['error'] ) ? $moved['error'] : 'Upload failed.' ) );
	}

	$attachment = array(
		'post_mime_type' => $moved['type'],
		'post_title'     => sanitize_file_name( wp_basename( $moved['file'] ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attachment_id = wp_insert_attachment( $attachment, $moved['file'] );
	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		wp_send_json_error( array( 'message' => 'Could not create attachment.' ) );
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $moved['file'] );
	if ( $metadata ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}

	$url = wp_get_attachment_url( $attachment_id );
	if ( ! $url ) {
		wp_send_json_error( array( 'message' => 'Could not get uploaded URL.' ) );
	}

	wp_send_json_success(
		array(
			'url'           => esc_url_raw( $url ),
			'attachment_id' => (int) $attachment_id,
		)
	);
}
add_action( 'wp_ajax_wd_upload_frame_artwork', 'wd_upload_frame_artwork_ajax' );
add_action( 'wp_ajax_nopriv_wd_upload_frame_artwork', 'wd_upload_frame_artwork_ajax' );

/**
 * True when the classic WooCommerce product data form is being saved.
 *
 * @return bool
 */
function wd_is_woocommerce_product_admin_save() {
	if ( empty( $_POST['woocommerce_meta_nonce'] ) ) {
		return false;
	}

	return (bool) wp_verify_nonce(
		sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ),
		'woocommerce_save_data'
	);
}

/**
 * Persist WAU "Enable File Uploads" on the product object before WC saves it.
 *
 * The plugin only hooks woocommerce_process_product_meta (priority 10), which can
 * run in the wrong order or be skipped; saving here keeps the checkbox reliable.
 *
 * @param WC_Product $product Product object.
 * @return void
 */
function wd_save_wau_product_enable_on_product_object( $product ) {
	if ( ! $product instanceof WC_Product || ! wd_is_woocommerce_product_admin_save() ) {
		return;
	}

	$enabled = isset( $_POST['_wau_product_enable'] ) ? 'yes' : 'no';
	$product->update_meta_data( '_wau_product_enable', $enabled );

	if ( isset( $_POST['_wau_min_files'] ) ) {
		$product->update_meta_data( '_wau_min_files', absint( wp_unslash( $_POST['_wau_min_files'] ) ) );
	}
	if ( isset( $_POST['_wau_max_files'] ) ) {
		$product->update_meta_data( '_wau_max_files', absint( wp_unslash( $_POST['_wau_max_files'] ) ) );
	}
	if ( isset( $_POST['_wau_product_charge'] ) ) {
		$product->update_meta_data( '_wau_product_charge', 'yes' );
	} else {
		$product->update_meta_data( '_wau_product_charge', 'no' );
	}
}
add_action( 'woocommerce_admin_process_product_object', 'wd_save_wau_product_enable_on_product_object', 99, 1 );

/**
 * Backup save for WAU product meta (runs after core product data save).
 *
 * @param int $post_id Product post ID.
 * @return void
 */
function wd_save_wau_product_enable_meta_backup( $post_id ) {
	if ( ! wd_is_woocommerce_product_admin_save() ) {
		return;
	}

	$post_id = absint( $post_id );
	if ( $post_id <= 0 ) {
		return;
	}

	$enabled = isset( $_POST['_wau_product_enable'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_wau_product_enable', $enabled );

	if ( isset( $_POST['_wau_min_files'] ) ) {
		update_post_meta( $post_id, '_wau_min_files', absint( wp_unslash( $_POST['_wau_min_files'] ) ) );
	}
	if ( isset( $_POST['_wau_max_files'] ) ) {
		update_post_meta( $post_id, '_wau_max_files', absint( wp_unslash( $_POST['_wau_max_files'] ) ) );
	}

	update_post_meta(
		$post_id,
		'_wau_product_charge',
		isset( $_POST['_wau_product_charge'] ) ? 'yes' : 'no'
	);
}
add_action( 'woocommerce_process_product_meta', 'wd_save_wau_product_enable_meta_backup', 99, 1 );

/**
 * Whether the current request includes a WAU plugin file upload.
 *
 * @return bool
 */
function wd_request_has_wau_file_upload() {
	if ( empty( $_FILES['wau_file_addon'] ) ) {
		return false;
	}

	$files = $_FILES['wau_file_addon'];

	if ( isset( $files['name'] ) && is_array( $files['name'] ) ) {
		foreach ( $files['name'] as $name ) {
			if ( '' !== (string) $name ) {
				return true;
			}
		}
		return false;
	}

	return ! empty( $files['name'] );
}

/**
 * Remove WAU "file required" error notices after we bypass validation.
 *
 * @return void
 */
function wd_clear_wau_file_required_notices() {
	$notices = wc_get_notices( 'error' );
	if ( empty( $notices ) ) {
		return;
	}

	$keep = array();
	foreach ( $notices as $notice ) {
		$text = isset( $notice['notice'] ) ? (string) $notice['notice'] : '';
		if ( false !== strpos( $text, 'Please select a file to continue' ) ) {
			continue;
		}
		$keep[] = $notice;
	}

	wc_clear_notices( 'error' );
	foreach ( $keep as $notice ) {
		wc_add_notice( $notice['notice'], 'error' );
	}
}

/**
 * WooCommerce Addon Uploads Pro blocks add-to-cart when _wau_product_enable is on
 * and "mandatory upload" is enabled, but Woodmart AJAX uses form.serialize() and
 * never sends files. Frame products use wd_upload_frame_artwork instead of WAU.
 *
 * @param bool $passed     Validation result.
 * @param int  $product_id Product ID.
 * @return bool
 */
function wd_bypass_wau_mandatory_for_standard_products( $passed, $product_id ) {
	if ( $passed ) {
		return $passed;
	}

	if ( 'yes' !== get_post_meta( $product_id, '_wau_product_enable', true ) ) {
		return $passed;
	}

	// Frame flow uses the visualizer upload, not WAU file fields.
	if ( function_exists( 'wd_is_frame_product' ) && wd_is_frame_product( $product_id ) ) {
		wd_clear_wau_file_required_notices();
		return true;
	}

	// No files in request (Woodmart serialize / empty upload): allow add to cart without WAU file.
	// When files are sent via FormData, do not bypass — plugin should attach them to the cart item.
	if ( ! wd_request_has_wau_file_upload() ) {
		wd_clear_wau_file_required_notices();
		return true;
	}

	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'wd_bypass_wau_mandatory_for_standard_products', 20, 2 );

/**
 * Ensure product add-to-cart form accepts file uploads (WAU plugin).
 *
 * @return void
 */
function wd_enqueue_wau_multipart_add_to_cart_script() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	$product_id = get_queried_object_id();
	if ( $product_id <= 0 || 'yes' !== get_post_meta( $product_id, '_wau_product_enable', true ) ) {
		return;
	}

	$script_path = get_theme_file_path( 'custom/js/wd-wau-add-to-cart.js' );
	$script_url  = get_theme_file_uri( 'custom/js/wd-wau-add-to-cart.js' );
	$version     = file_exists( $script_path ) ? (string) filemtime( $script_path ) : '1.0.0';

	wp_enqueue_script(
		'wd-wau-add-to-cart',
		$script_url,
		array( 'jquery' ),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'wd_enqueue_wau_multipart_add_to_cart_script', 10050 );

/**
 * Collect WAU "Uploaded Media" URLs for an order line item.
 *
 * @param int $item_id Order item ID.
 * @return string[]
 */
function wd_get_wau_upload_urls_for_order_item( $item_id ) {
	$item_id = absint( $item_id );
	if ( $item_id <= 0 || ! function_exists( 'wc_get_order_item_meta' ) ) {
		return array();
	}

	$urls = wc_get_order_item_meta( $item_id, 'Uploaded Media', false );
	if ( ! is_array( $urls ) ) {
		$urls = $urls ? array( $urls ) : array();
	}

	$clean = array();
	foreach ( $urls as $url ) {
		$url = esc_url_raw( (string) $url );
		if ( $url ) {
			$clean[] = $url;
		}
	}

	return array_values( array_unique( $clean ) );
}

/**
 * Resolve a media URL to a readable local filesystem path when possible.
 *
 * @param string $url Media URL.
 * @return string|false
 */
function wd_wau_url_to_local_path( $url ) {
	$url = esc_url_raw( $url );
	if ( ! $url ) {
		return false;
	}

	$attachment_id = attachment_url_to_postid( $url );
	if ( $attachment_id ) {
		$path = get_attached_file( $attachment_id );
		if ( $path && file_exists( $path ) ) {
			return $path;
		}
	}

	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['baseurl'] ) && ! empty( $upload_dir['basedir'] ) ) {
		$base_url  = set_url_scheme( $upload_dir['baseurl'] );
		$base_path = $upload_dir['basedir'];
		$check_url = set_url_scheme( $url );

		if ( 0 === strpos( $check_url, $base_url ) ) {
			$relative = ltrim( substr( $check_url, strlen( $base_url ) ), '/' );
			$path     = wp_normalize_path( $base_path . '/' . $relative );
			if ( file_exists( $path ) ) {
				return $path;
			}
		}
	}

	return false;
}

/**
 * Download all WAU uploads for one order line item as a ZIP (admin).
 *
 * @return void
 */
function wd_wau_download_item_uploads_zip_handler() {
	$item_id  = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : 0;
	$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
	$nonce    = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';

	if ( $item_id <= 0 || $order_id <= 0 || ! wp_verify_nonce( $nonce, 'wd_wau_zip_' . $item_id ) ) {
		wp_die( esc_html__( 'Invalid download request.', 'woodmart' ), 403 );
	}

	if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You do not have permission to download these files.', 'woodmart' ), 403 );
	}

	if ( ! class_exists( 'ZipArchive' ) ) {
		wp_die( esc_html__( 'ZIP support is not available on this server (ZipArchive missing).', 'woodmart' ), 500 );
	}

	$item = WC_Order_Factory::get_order_item( $item_id );
	if ( ! $item || 'line_item' !== $item->get_type() || (int) $item->get_order_id() !== $order_id ) {
		wp_die( esc_html__( 'Order item not found.', 'woodmart' ), 404 );
	}

	$urls = wd_get_wau_upload_urls_for_order_item( $item_id );
	if ( empty( $urls ) ) {
		wp_die( esc_html__( 'No uploaded files found for this item.', 'woodmart' ), 404 );
	}

	$zip      = new ZipArchive();
	$tmp_file = wp_tempnam( 'wd-wau-uploads-' . $item_id );
	if ( ! $tmp_file ) {
		wp_die( esc_html__( 'Could not create temporary file.', 'woodmart' ), 500 );
	}

	$opened = $zip->open( $tmp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE );
	if ( true !== $opened ) {
		@unlink( $tmp_file );
		wp_die( esc_html__( 'Could not create ZIP archive.', 'woodmart' ), 500 );
	}

	$added = 0;
	$index = 1;

	foreach ( $urls as $url ) {
		$local_path = wd_wau_url_to_local_path( $url );
		$ext        = '';

		if ( $local_path ) {
			$ext = pathinfo( $local_path, PATHINFO_EXTENSION );
		} else {
			$parsed = wp_parse_url( $url );
			if ( ! empty( $parsed['path'] ) ) {
				$ext = pathinfo( $parsed['path'], PATHINFO_EXTENSION );
			}
		}

		$entry_name = sprintf( 'upload-%02d', $index );
		if ( $ext ) {
			$entry_name .= '.' . strtolower( preg_replace( '/[^a-z0-9]/i', '', $ext ) );
		}

		if ( $local_path && file_exists( $local_path ) && is_readable( $local_path ) ) {
			$zip->addFile( $local_path, $entry_name );
			++$added;
		} else {
			$response = wp_remote_get(
				$url,
				array(
					'timeout'   => 30,
					'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
				)
			);

			if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
				$body = wp_remote_retrieve_body( $response );
				if ( '' !== $body ) {
					$zip->addFromString( $entry_name, $body );
					++$added;
				}
			}
		}

		++$index;
	}

	$zip->close();

	if ( $added < 1 ) {
		@unlink( $tmp_file );
		wp_die( esc_html__( 'Could not add any files to the ZIP.', 'woodmart' ), 500 );
	}

	$filename = sprintf( 'order-%d-item-%d-uploads.zip', $order_id, $item_id );

	nocache_headers();
	header( 'Content-Type: application/zip' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	header( 'Content-Length: ' . filesize( $tmp_file ) );

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	readfile( $tmp_file );
	@unlink( $tmp_file );
	exit;
}
add_action( 'wp_ajax_wd_wau_download_item_uploads_zip', 'wd_wau_download_item_uploads_zip_handler' );

/**
 * "Download all" button after WAU upload links in order admin.
 *
 * @param int                 $item_id Order item ID.
 * @param WC_Order_Item|false $item    Order item object.
 * @return void
 */
function wd_render_wau_download_all_zip_button( $item_id, $item ) {
	static $rendered = array();

	$item_id = absint( $item_id );
	if ( $item_id <= 0 || isset( $rendered[ $item_id ] ) ) {
		return;
	}

	if ( ! $item || ! is_a( $item, 'WC_Order_Item' ) || 'line_item' !== $item->get_type() ) {
		return;
	}

	$urls = wd_get_wau_upload_urls_for_order_item( $item_id );
	if ( empty( $urls ) ) {
		return;
	}

	$order_id = (int) $item->get_order_id();
	if ( $order_id <= 0 ) {
		return;
	}

	$rendered[ $item_id ] = true;

	$download_url = add_query_arg(
		array(
			'action'   => 'wd_wau_download_item_uploads_zip',
			'item_id'  => $item_id,
			'order_id' => $order_id,
			'nonce'    => wp_create_nonce( 'wd_wau_zip_' . $item_id ),
		),
		admin_url( 'admin-ajax.php' )
	);

	$count = count( $urls );

	echo '<div class="wd-wau-download-all-wrap" style="margin-top:10px;clear:both;">';
	echo '<a href="' . esc_url( $download_url ) . '" class="button button-primary wd-wau-download-all-zip" target="_blank" rel="noopener noreferrer">';
	echo esc_html(
		sprintf(
			/* translators: %d: number of uploaded files */
			_n( 'Download all (%d file)', 'Download all (%d files)', $count, 'woodmart' ),
			$count
		)
	);
	echo '</a>';
	echo '</div>';
}
add_action( 'woocommerce_after_order_itemmeta', 'wd_render_wau_download_all_zip_button', 99, 2 );

/**
 * Register Subscriber post type (stores email in post_title)
 */
function wd_register_subscriber_post_type() {
	register_post_type( 'wd_subscriber', array(
		'labels'             => array(
			'name'               => 'Subscribers',
			'singular_name'      => 'Subscriber',
			'menu_name'          => 'Subscribers',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Subscriber',
			'edit_item'          => 'Edit Subscriber',
			'new_item'           => 'New Subscriber',
			'view_item'          => 'View Subscriber',
			'search_items'       => 'Search Subscribers',
			'not_found'          => 'No subscribers found',
			'not_found_in_trash' => 'No subscribers found in Trash',
		),
		'public'             => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-email-alt',
		'capability_type'    => 'post',
		'supports'            => array( 'title' ),
		'has_archive'        => false,
	) );
}
add_action( 'init', 'wd_register_subscriber_post_type' );

/**
 * AJAX: Subscribe email (save as subscriber / check duplicate)
 */
function wd_ajax_subscribe_email() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'wd_subscribe' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid request.' ) );
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}
	$email = strtolower( $email );

	$exists = get_posts( array(
		'post_type'      => 'wd_subscriber',
		'post_status'    => array( 'publish', 'draft' ),
		'numberposts'    => 1,
		'meta_key'       => '_subscriber_email',
		'meta_value'     => $email,
	) );

	if ( ! empty( $exists ) ) {
		wp_send_json_error( array( 'message' => 'This email is already subscribed.' ) );
	}

	$id = wp_insert_post( array(
		'post_type'   => 'wd_subscriber',
		'post_title'  => $email,
		'post_status' => 'publish',
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_subscriber_email', $email );
	}

	if ( is_wp_error( $id ) ) {
		wp_send_json_error( array( 'message' => 'Could not subscribe. Please try again.' ) );
	}

	wp_send_json_success( array( 'message' => 'Thank you! You are now subscribed.' ) );
}
add_action( 'wp_ajax_wd_subscribe_email', 'wd_ajax_subscribe_email' );
add_action( 'wp_ajax_nopriv_wd_subscribe_email', 'wd_ajax_subscribe_email' );

/**
 * Shortcode: Top Banner Slider (Owl) – BG option + upper text per slide, no button
 * Usage:
 *   [banner_slider]
 *     [banner_slide bg="https://example.com/bg.jpg" subtext="Get your" title="CUSTOMIZED GIFTS" price="Starts From ₹ 299"]
 *     [banner_slide bg="#fce4ec" subtext="Offer" title="BIG SALE" price="Up to 50% off"]
 *   [/banner_slider]
 * bg = full image URL or hex color (e.g. #fce4ec)
 */
function wd_banner_slide_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'bg'      => '',
		'subtext' => '',
		'title'   => '',
		'price'   => '',
	), $atts, 'banner_slide' );

	$bg_style = '';
	if ( ! empty( $atts['bg'] ) ) {
		$bg = trim( $atts['bg'] );
		if ( preg_match( '#^(https?://|/)#', $bg ) || preg_match( '#\.(jpg|jpeg|png|gif|webp)(\?|$)#i', $bg ) ) {
			$bg_style = 'background-image:url(' . esc_attr( $bg ) . ');background-size:cover;background-position:center;';
		} else {
			$bg_style = 'background-color:' . esc_attr( $bg ) . ';';
		}
	}

	ob_start();
	?>
	<div class="wd-banner-slide" style="<?php echo $bg_style; ?>">
		<div class="wd-banner-slide__content">
			<?php if ( $atts['subtext'] !== '' ) : ?>
				<p class="wd-banner-slide__subtext"><?php echo esc_html( $atts['subtext'] ); ?></p>
			<?php endif; ?>
			<?php if ( $atts['title'] !== '' ) : ?>
				<h2 class="wd-banner-slide__title"><?php echo esc_html( $atts['title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( $atts['price'] !== '' ) : ?>
				<p class="wd-banner-slide__price"><?php echo esc_html( $atts['price'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'banner_slide', 'wd_banner_slide_shortcode' );

function wd_banner_slider_shortcode( $atts, $content = '' ) {
	wp_enqueue_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4' );
	wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array( 'owl-carousel-css' ), '2.3.4' );
	wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );

	$owl_banner = "
		jQuery(function($){
			$('.wd-banner-slider').owlCarousel({
				items: 1,
				loop: true,
				nav: false,
				dots: false,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplayHoverPause: true
			});
		});
	";
	wp_add_inline_script( 'owl-carousel-js', $owl_banner );

	ob_start();
	?>
	<style>
	.wd-banner-slider-wrap { overflow: hidden; }
	.wd-banner-slider { margin: 0; }
	.wd-banner-slider .wd-banner-slide { min-height: 520px; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
	.wd-banner-slider .wd-banner-slide__content { text-align: center; max-width: 800px; }
	.wd-banner-slider .wd-banner-slide__subtext { margin: 0 0 8px; font-size: 18px; font-weight: 500; color: #c41e5a; font-style: italic; }
	.wd-banner-slider .wd-banner-slide__title { margin: 0 0 12px; font-size: 32px; font-weight: 700; color: #000; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.02em; }
	.wd-banner-slider .wd-banner-slide__price { margin: 0; font-size: 16px; color: #333; }
	.wd-banner-slider.owl-carousel .owl-dots { display: none !important; }
	@media (max-width: 767px) { .wd-banner-slider .wd-banner-slide { min-height: 380px; padding: 30px 16px; } .wd-banner-slider .wd-banner-slide__title { font-size: 24px; } .wd-banner-slider .wd-banner-slide__subtext { font-size: 16px; } }
	</style>
	<div class="wd-banner-slider-wrap">
		<div class="owl-carousel wd-banner-slider">
			<?php echo do_shortcode( $content ); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'banner_slider', 'wd_banner_slider_shortcode' );

/**
 * Shortcode: custom_shortcode
 * Usage: [custom_shortcode]
 * Output: hello world
 */
function custom_shortcode_callback() {
	return 'hello world';
}
add_shortcode( 'custom_shortcode', 'custom_shortcode_callback' );

/**
 * Shortcode: Browse by categories (from new_html.html)
 * Usage: [browse_by_categories]
 * Output: Browse by categories section with Owl Carousel
 */
function browse_by_categories_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	// Enqueue Owl Carousel only when shortcode is used
	wp_enqueue_style(
		'owl-carousel-css',
		'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
		array(),
		'2.3.4'
	);
	wp_enqueue_style(
		'owl-carousel-theme',
		'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css',
		array( 'owl-carousel-css' ),
		'2.3.4'
	);
	wp_enqueue_script(
		'owl-carousel-js',
		'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
		array( 'jquery' ),
		'2.3.4',
		true
	);

	ob_start();
	?>
	.wd-browse-categories { padding: 40px 0 50px; background: #fff; }
	.wd-browse-categories .wd-browse-title { font-size: 36px; font-weight: 700; color: #c41e5a; margin-bottom: 32px; text-align: center; letter-spacing: -0.02em; line-height: 1.2; }
	.wd-browse-categories .wd-category-slider { margin: 0 -8px; }
	.wd-browse-categories .wd-category-slider .owl-item { padding: 0 8px; }
	.wd-browse-categories .wd-category-card { display: block; text-decoration: none; color: inherit; text-align: center; }
	.wd-browse-categories .wd-category-card__img-wrap { position: relative; width: 100%; padding-bottom: 100%; border-radius: 50%; overflow: hidden; background: #f5f5f5; margin: 0 auto 12px; max-width: 160px; }
	.wd-browse-categories .wd-category-card__img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; }
	.wd-browse-categories .wd-category-card__label { margin: 0; padding: 0; font-size: 15px; font-weight: 700; color: #222; text-align: center; }
	.wd-browse-categories .wd-category-slider.owl-carousel .owl-nav { display: none !important; }
	.wd-browse-categories .wd-category-slider.owl-carousel .owl-dots { display: none !important; }
	<?php
	$browse_css = ob_get_clean();

	$owl_init = "
		jQuery(function($){
			$('.wd-browse-categories .wd-category-slider').length && $('.wd-browse-categories .wd-category-slider').owlCarousel({ items: 6, loop: true, margin: 16, nav: false, dots: false, autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, responsive: { 0: { items: 2, margin: 10 }, 480: { items: 3, margin: 12 }, 768: { items: 4, margin: 14 }, 1024: { items: 5, margin: 16 }, 1200: { items: 6, margin: 16 } } });
			$('.wd-new-arrivals .wd-new-arrivals-slider').length && $('.wd-new-arrivals .wd-new-arrivals-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
			$('.wd-best-selling .wd-best-selling-slider').length && $('.wd-best-selling .wd-best-selling-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
			$('.wd-craft-with-love .wd-craft-with-love-slider').length && $('.wd-craft-with-love .wd-craft-with-love-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, navContainer: '.wd-craft-with-love-nav', navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
		});
	";
	wp_add_inline_script( 'owl-carousel-js', $owl_init );

	ob_start();
	?>
	<style>
	<?php echo $browse_css; ?>
	</style>
	<!-- CATEGORY START -->
	<div class="wd-browse-categories">
		<div class="container">
			<h2 class="wd-browse-title">Browse by categories</h2>
			<div class="owl-carousel wd-category-slider">
				<div class="item">
					<a href="#" class="wd-category-card">
						<div class="wd-category-card__img-wrap">
							<img class="wd-category-card__img" src="<?php echo esc_url( $img_base . '/cate-1.png' ); ?>" alt="anniversary">
						</div>
						<p class="wd-category-card__label">Anniversary</p>
					</a>
				</div>
				<div class="item">
					<a href="#" class="wd-category-card">
						<div class="wd-category-card__img-wrap">
							<img class="wd-category-card__img" src="<?php echo esc_url( $img_base . '/cate-2.png' ); ?>" alt="home decor">
						</div>
						<p class="wd-category-card__label">Home decor</p>
					</a>
				</div>
				<div class="item">
					<a href="#" class="wd-category-card">
						<div class="wd-category-card__img-wrap">
							<img class="wd-category-card__img" src="<?php echo esc_url( $img_base . '/cate-3.png' ); ?>" alt="greeting cards">
						</div>
						<p class="wd-category-card__label">Greeting cards</p>
					</a>
				</div>
				<div class="item">
					<a href="#" class="wd-category-card">
						<div class="wd-category-card__img-wrap">
							<img class="wd-category-card__img" src="<?php echo esc_url( $img_base . '/cate-4.png' ); ?>" alt="gift for him">
						</div>
						<p class="wd-category-card__label">Gift for him</p>
					</a>
				</div>
				<div class="item">
					<a href="#" class="wd-category-card">
						<div class="wd-category-card__img-wrap">
							<img class="wd-category-card__img" src="<?php echo esc_url( $img_base . '/cate-5.png' ); ?>" alt="gift for her">
						</div>
						<p class="wd-category-card__label">Gift for her</p>
					</a>
				</div>
				<div class="item">
					<a href="#" class="wd-category-card">
						<div class="wd-category-card__img-wrap">
							<img class="wd-category-card__img" src="<?php echo esc_url( $img_base . '/cate-6.png' ); ?>" alt="photo gift">
						</div>
						<p class="wd-category-card__label">Photo gift</p>
					</a>
				</div>
			</div>
		</div>
	</div>
	<!-- CATEGORY END -->
	<?php
	return ob_get_clean();
}
add_shortcode( 'browse_by_categories', 'browse_by_categories_shortcode' );

/**
 * Shortcode: New Arrivals Sitewide Discounts (from new_html.html)
 * Usage: [new_arrivals]
 * Place below [browse_by_categories] on home page.
 */
function new_arrivals_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	wp_enqueue_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4' );
	wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array( 'owl-carousel-css' ), '2.3.4' );
	wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );

	ob_start();
	?>
	.wd-new-arrivals { padding: 50px 0 60px; background: #fff; }
	.wd-new-arrivals .wd-new-arrivals-title { font-size: 32px; font-weight: 700; color: #222; margin-bottom: 28px; text-align: center; }
	.wd-new-arrivals .wd-new-arrivals-title span { color: #c41e5a; }
	.wd-new-arrivals .wd-new-arrivals-slider { margin: 0 -6px; }
	.wd-new-arrivals .wd-new-arrivals-slider .owl-item { padding: 0 6px; }
	.wd-new-arrivals .wd-product-card { background: #fff; border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; transition: transform .25s ease, box-shadow .25s ease; height: 100%; display: flex; flex-direction: column; position: relative; }
	.wd-new-arrivals .wd-product-card:hover { transform: translateY(-4px); }
	.wd-new-arrivals .wd-product-card__link { display: block; text-decoration: none; color: inherit; flex: 1; }
	.wd-new-arrivals .woodmart-add-btn { display: block; width: 100%; margin-top: 0; padding: 0 16px 16px; }
	.wd-new-arrivals .woodmart-add-btn .button { display: block !important; width: 100% !important; text-align: center; box-sizing: border-box; }
	.wd-new-arrivals .wd-product-card__badge { position: absolute; top: 12px; left: 12px; background: #c41e5a; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 4px; z-index: 2; text-transform: uppercase; }
	.wd-new-arrivals .wd-product-card__img-wrap { position: relative; padding-bottom: 100%; background: #f8f8f8; overflow: hidden; }
	.wd-new-arrivals .wd-product-card__img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; }
	.wd-new-arrivals .wd-product-card__body { padding: 16px; }
	.wd-new-arrivals .wd-product-card__title { margin: 0 0 10px; font-size: 16px; font-weight: 600; color: #222; line-height: 1.3; min-height:30px  }
	.wd-new-arrivals .wd-product-card__price { margin-bottom: 10px; }
	.wd-new-arrivals .wd-product-card__price del { font-size: 14px; color: #999; margin-right: 6px; }
	.wd-new-arrivals .wd-product-card__price .wd-price-current,
	.wd-new-arrivals .wd-product-card__price .amount,
	.wd-new-arrivals .wd-product-card__price .woocommerce-Price-amount { font-size: 18px; font-weight: 700; color: #c41e5a; }
	.wd-new-arrivals .wd-product-card__price del .amount,
	.wd-new-arrivals .wd-product-card__price del .woocommerce-Price-amount { color: #999; font-weight: normal; }
	.wd-new-arrivals .wd-product-card__rating { color: #ffb400; font-size: 18px; margin-bottom: 12px; letter-spacing: 2px; line-height: 1; }
	.wd-new-arrivals .wd-product-card__btn { display: inline-block; background: #de056f !important; color: #fff !important; font-size: 13px; font-weight: 600; padding: 10px 18px; border-radius: 6px; text-align: center; transition: background .25s ease; }
	.wd-new-arrivals .wd-product-card__btn:hover { background: #b80459 !important; color: #fff !important; }
	.wd-new-arrivals .wd-new-arrivals-slider.owl-carousel .owl-nav button { position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; background: #fff !important; box-shadow: 0 4px 16px rgba(0,0,0,.12) !important; color: #333 !important; font-size: 20px !important; }
	.wd-new-arrivals .wd-new-arrivals-slider.owl-carousel .owl-nav button:hover { background: #c41e5a !important; color: #fff !important; }
	.wd-new-arrivals .wd-new-arrivals-slider.owl-carousel .owl-nav .owl-prev { left: -18px; }
	.wd-new-arrivals .wd-new-arrivals-slider.owl-carousel .owl-nav .owl-next { right: -18px; }
	.wd-new-arrivals .wd-new-arrivals-slider.owl-carousel .owl-dots { display: none !important; }
	.wd-new-arrivals .wd-new-arrivals-empty { padding: 24px 16px; text-align: center; color: #666; margin: 0; }
	<?php
	$arrivals_css = ob_get_clean();

	$owl_init_both = "
		jQuery(function($){
			$('.wd-browse-categories .wd-category-slider').length && $('.wd-browse-categories .wd-category-slider').owlCarousel({ items: 6, loop: true, margin: 16, nav: false, dots: false, autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, responsive: { 0: { items: 2, margin: 10 }, 480: { items: 3, margin: 12 }, 768: { items: 4, margin: 14 }, 1024: { items: 5, margin: 16 }, 1200: { items: 6, margin: 16 } } });
			$('.wd-new-arrivals .wd-new-arrivals-slider').length && $('.wd-new-arrivals .wd-new-arrivals-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
			$('.wd-best-selling .wd-best-selling-slider').length && $('.wd-best-selling .wd-best-selling-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
			$('.wd-craft-with-love .wd-craft-with-love-slider').length && $('.wd-craft-with-love .wd-craft-with-love-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, navContainer: '.wd-craft-with-love-nav', navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
		});
	";
	wp_add_inline_script( 'owl-carousel-js', $owl_init_both );

	ob_start();
	?>
	<style>
	<?php echo $arrivals_css; ?>
	</style>
	<?php
	$new_arrivals_products = array();
	if ( class_exists( 'WooCommerce' ) ) {
		$new_query = new WP_Query( array(
			'post_type'      => 'product',
			'posts_per_page' => 12,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );
		if ( $new_query->have_posts() ) {
			while ( $new_query->have_posts() ) {
				$new_query->the_post();
				$new_arrivals_products[] = wc_get_product( get_the_ID() );
			}
			wp_reset_postdata();
		}
	}
	?>
	<div class="custom_container">
		<div class="wd-new-arrivals">
			<div class="container">
				<h2 class="wd-new-arrivals-title">New Arrivals Sitewide Discounts & <span>Savings up to 25%</span></h2>
				<div class="owl-carousel wd-new-arrivals-slider">
				<?php
				foreach ( $new_arrivals_products as $product ) :
					if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
						continue;
					}
					$pid   = $product->get_id();
					$url   = $product->get_permalink();
					$img_id = $product->get_image_id();
					$name  = $product->get_name();
					$on_sale = $product->is_on_sale();
					$price_html = $product->get_price_html();
					$add_to_cart_url = $product->add_to_cart_url();
					$avg = (float) $product->get_average_rating();
					$full = (int) round( $avg );
					$empty = 5 - $full;
					$product_type = $product->get_type();
					$sku = $product->get_sku();
					$success_msg = sprintf( '"%s" has been added to your cart', $name );
				?>
					<div class="item">
						<div class="wd-product-card">
							<a href="<?php echo esc_url( $url ); ?>" class="wd-product-card__link">
								<?php if ( $on_sale ) : ?>
									<span class="wd-product-card__badge">Sale</span>
								<?php endif; ?>
								<div class="wd-product-card__img-wrap">
									<?php if ( $img_id ) : ?>
										<img class="wd-product-card__img" src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
									<?php else : ?>
										<img class="wd-product-card__img" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
									<?php endif; ?>
								</div>
								<div class="wd-product-card__body">
									<h3 class="wd-product-card__title"><?php echo esc_html( $name ); ?></h3>
									<div class="wd-product-card__price"><?php echo $price_html; ?></div>
									<div class="wd-product-card__rating"><span class="wd-stars-filled"><?php echo str_repeat( '★', $full ); ?></span><span class="wd-stars-empty"><?php echo str_repeat( '☆', $empty ); ?></span></div>
								</div>
							</a>
							<div class="woodmart-add-btn">
								<a href="<?php echo esc_url( $add_to_cart_url ); ?>" data-quantity="1" class="button product_type_<?php echo esc_attr( $product_type ); ?> add_to_cart_button ajax_add_to_cart add-to-cart-loop wd-product-card__btn" data-product_id="<?php echo esc_attr( $pid ); ?>" data-product_sku="<?php echo esc_attr( $sku ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Add to cart: "%s"', $name ) ); ?>" rel="nofollow" data-success_message="<?php echo esc_attr( $success_msg ); ?>" role="button"><span>Add to Cart</span></a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				<?php if ( empty( $new_arrivals_products ) ) : ?>
					<div class="item">
						<p class="wd-new-arrivals-empty">No products yet. New arrivals will appear here.</p>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<!-- NEW ARRIVALS END -->
	<?php
	return ob_get_clean();
}
add_shortcode( 'new_arrivals', 'new_arrivals_shortcode' );

/**
 * Shortcode: Get your customized gifts (from new_html.html)
 * Usage: [customized_gifts]
 */
function customized_gifts_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';
	$banner_bg = $img_base . '/gift-bg.png';

	wp_enqueue_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4' );
	wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array( 'owl-carousel-css' ), '2.3.4' );
	wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );

	ob_start();
	?>
	.wd-customized-gifts { padding: 1px 0 15px; background: #fff; }
	.wd-customized-gifts .wd-customized-gifts-inner { display: flex; flex-wrap: wrap; align-items: stretch; gap: 30px; }
	.wd-customized-gifts .wd-customized-gifts-cta { flex: 0 0 50%; min-width: 340px; background: #dc2626 url(<?php echo esc_url( $banner_bg ); ?>) no-repeat bottom right / 106% auto; border-radius: 37px; padding: 28px 32px; color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; position: relative; }
	.wd-customized-gifts .wd-customized-gifts-cta::before { content: ''; position: absolute; inset: 0; border-radius: 37px; z-index: 0; }
	.wd-customized-gifts .wd-customized-gifts-cta h2, .wd-customized-gifts .wd-customized-gifts-cta p, .wd-customized-gifts .wd-customized-gifts-cta .wd-cta-btn { position: relative; z-index: 1; }
	.wd-customized-gifts .wd-customized-gifts-cta h2 { margin: 0 0 12px; font-size: 3.4rem; font-weight: 700; line-height: 1.25; color: #fff; text-transform: capitalize; text-align: center; }
	.wd-customized-gifts .wd-customized-gifts-cta p { margin: 0 0 24px; font-size: 18px; font-weight:bold; opacity: 1; color: #fff; text-align: center; }
	.wd-customized-gifts .wd-customized-gifts-cta .wd-cta-btn { display: inline-block;
    background: #fff !important;
    color: #b91c1c !important;
    font-size: 15px;
    font-weight: 400;
    padding: 9px 28px;
    border-radius: 10px;
    text-decoration: none;
    transition: transform .2s ease, background .2s ease;
    text-align: center;
	}
	.wd-customized-gifts .wd-customized-gifts-cta .wd-cta-btn:hover { transform: translateY(-2px); background: #ffb3b3 !important; color: #991b1b !important; }
	.wd-customized-gifts .wd-customized-gifts-right { flex: 1; min-width: 0; }
	.wd-customized-gifts .wd-customized-gifts-right-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
	.wd-customized-gifts .wd-customized-gifts-right h3 { margin: 0; font-size: 22px; font-weight: 700; color: #222; text-align: left; }
	.wd-customized-gifts .wd-holiday-nav { display: flex; gap: 8px; }
	.wd-customized-gifts .wd-holiday-nav .owl-prev,
	.wd-customized-gifts .wd-holiday-nav .owl-next { width: 36px; height: 36px; border-radius: 50%; background: #fff !important; border: 1px solid #e5e5e5 !important; color: #333 !important; font-size: 18px !important; padding: 0 !important; margin: 0 !important; line-height: 1; display: flex !important; align-items: center; justify-content: center; cursor: pointer; transition: all .25s ease; }
	.wd-customized-gifts .wd-holiday-nav .owl-prev:hover,
	.wd-customized-gifts .wd-holiday-nav .owl-next:hover { background: #c41e5a !important; color: #fff !important; border-color: #c41e5a !important; }
	.wd-customized-gifts .wd-customized-gifts-slider { margin: 0; }
	.wd-customized-gifts .wd-holiday-slide-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
	.wd-customized-gifts .wd-customized-gifts-slider .owl-item { padding: 0; }
	.wd-customized-gifts .wd-product-card { background: #fff; border-radius: 10px; overflow: hidden; border:1px solid #f0f0f0; transition: transform .25s ease, box-shadow .25s ease; height: 100%; display: flex; flex-direction: column; position: relative; }
	.wd-customized-gifts .wd-product-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
	.wd-customized-gifts .wd-product-card__link { display: block; text-decoration: none; color: inherit; flex: 1; }
	.wd-customized-gifts .wd-product-card__price .amount,
	.wd-customized-gifts .wd-product-card__price .woocommerce-Price-amount { font-weight: 700; color: #de056f; }
	.wd-customized-gifts .wd-product-card__price del .amount,
	.wd-customized-gifts .wd-product-card__price del .woocommerce-Price-amount { color: #999; font-weight: normal; }
	.wd-customized-gifts .wd-holiday-no-products { grid-column: 1 / -1; padding: 24px; text-align: center; color: #666; margin: 0; }
	.wd-customized-gifts .wd-product-card__badge { position: absolute; top: 8px; left: 8px; background: #c41e5a; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; z-index: 2; text-transform: uppercase; }
	.wd-customized-gifts .wd-product-card__img-wrap { position: relative; padding-bottom: 62%; background: #f8f8f8; overflow: hidden; }
	.wd-customized-gifts .wd-product-card__img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; }
	.wd-customized-gifts .wd-product-card__body { padding: 10px; }
	.wd-customized-gifts .wd-product-card__title { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #222; line-height: 1.25; text-align: left; }
	.wd-customized-gifts .wd-product-card__price { margin-bottom: 4px; }
	.wd-customized-gifts .wd-product-card__price del { font-size: 12px; color: #999; margin-right: 4px; }
	.wd-customized-gifts .wd-product-card__price .wd-price-current { font-size: 15px; font-weight: 700; color: #c41e5a; }
	.wd-customized-gifts .wd-product-card__rating { margin-bottom: 8px; font-size: 18px; letter-spacing: 2px; line-height: 1; color: #ffb400; }
	.wd-customized-gifts .wd-product-card__rating.wd-static-stars .wd-stars-filled { color: #ffb400; }
	.wd-customized-gifts .wd-product-card__rating.wd-static-stars .wd-stars-empty { color: #ffb400; }
	.wd-customized-gifts .wd-product-card__body { padding-bottom: 4px; }
	.wd-customized-gifts .woodmart-add-btn { display: block; width: 100%; margin-top: 8px; padding: 0; }
	.wd-customized-gifts .woodmart-add-btn .button { display: block !important; width: 100% !important; background: #de056f !important; color: #fff !important; font-size: 12px; font-weight: 600; padding: 10px 16px; border-radius: 6px; text-align: center; transition: background .25s ease; text-decoration: none !important; border: none; box-sizing: border-box; }
	.wd-customized-gifts .woodmart-add-btn .button:hover { background: #b80459 !important; color: #fff !important; }
	@media (max-width: 767px) { .wd-customized-gifts .wd-customized-gifts-cta { flex: 0 0 100%; } .wd-customized-gifts .wd-holiday-slide-grid { grid-template-columns: 1fr; } }
	<?php
	$customized_gifts_css = ob_get_clean();

	$owl_holiday = "
		jQuery(function($){
			$('.wd-customized-gifts .wd-customized-gifts-slider').owlCarousel({
				items: 1,
				loop: true,
				nav: true,
				dots: false,
				navContainer: '.wd-holiday-nav',
				navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>']
			});
		});
	";
	wp_add_inline_script( 'owl-carousel-js', $owl_holiday );

	$page_id = get_queried_object_id();
	if ( ! $page_id && is_front_page() && get_option( 'page_on_front' ) ) {
		$page_id = (int) get_option( 'page_on_front' );
	}
	if ( ! $page_id && ! empty( $GLOBALS['post'] ) ) {
		$page_id = get_the_ID();
	}
	$cta_heading   = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'custom_gifts_cta_heading', $page_id ) : '';
	$cta_price     = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'custom_gifts_cta_price_text', $page_id ) : '';
	$cta_btn_url   = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'custom_gifts_cta_button_url', $page_id ) : '';
	$holiday_title = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'custom_gifts_holiday_title', $page_id ) : '';
	if ( (string) $cta_heading === '' ) { $cta_heading = 'Get Your Customized Gifts'; }
	if ( (string) $cta_price === '' ) { $cta_price = 'Starts from ₹ 299'; }
	if ( (string) $cta_btn_url === '' ) { $cta_btn_url = '#'; }
	if ( (string) $holiday_title === '' ) { $holiday_title = 'This Holiday'; }

	ob_start();
	?>
	<style>
	<?php echo $customized_gifts_css; ?>
	</style>
	<div class="wd-customized-gifts">
		<div class="container">
			<div class="wd-customized-gifts-inner">
				<div class="wd-customized-gifts-cta">
					<h2><?php echo esc_html( $cta_heading ); ?></h2>
					<p><?php echo esc_html( $cta_price ); ?></p>
					<a href="<?php echo esc_url( $cta_btn_url ); ?>" class="wd-cta-btn">Shop Now</a>
				</div>
				<div class="wd-customized-gifts-right">
					<div class="wd-customized-gifts-right-header">
						<h3><?php echo esc_html( $holiday_title ); ?></h3>
						<div class="wd-holiday-nav"></div>
					</div>
					<div class="owl-carousel wd-customized-gifts-slider">
					<?php
					$holiday_products = array();
					if ( class_exists( 'WooCommerce' ) ) {
						$holiday_query = new WP_Query( array(
							'post_type'      => 'product',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'orderby'        => 'menu_order title',
							'order'          => 'ASC',
							'tax_query'      => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'slug',
									'terms'    => 'anniversary-gifts',
								),
							),
						) );
						if ( $holiday_query->have_posts() ) {
							while ( $holiday_query->have_posts() ) {
								$holiday_query->the_post();
								$holiday_products[] = wc_get_product( get_the_ID() );
							}
							wp_reset_postdata();
						}
					}
					$holiday_chunks = array_chunk( $holiday_products, 4 );
					foreach ( $holiday_chunks as $chunk ) :
						if ( empty( $chunk ) ) {
							continue;
						}
					?>
						<div class="item">
							<div class="wd-holiday-slide-grid">
							<?php
							foreach ( $chunk as $product ) {
								if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
									continue;
								}
								$pid    = $product->get_id();
								$url    = $product->get_permalink();
								$img_id = $product->get_image_id();
								$name   = $product->get_name();
								$on_sale = $product->is_on_sale();
								$price_html = $product->get_price_html();
								$add_to_cart_url = $product->add_to_cart_url();
								$rating_html = wc_get_rating_html( $product->get_average_rating() );
								$product_type = $product->get_type();
								$sku = $product->get_sku();
								$success_msg = sprintf( '"%s" has been added to your cart', $name );
								?>
								<div class="wd-product-card">
									<a href="<?php echo esc_url( $url ); ?>" class="wd-product-card__link">
										<?php if ( $on_sale ) : ?>
											<span class="wd-product-card__badge">Sale</span>
										<?php endif; ?>
										<div class="wd-product-card__img-wrap">
											<?php if ( $img_id ) : ?>
												<img class="wd-product-card__img" src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
											<?php else : ?>
												<img class="wd-product-card__img" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
											<?php endif; ?>
										</div>
										<div class="wd-product-card__body">
											<h3 class="wd-product-card__title"><?php echo esc_html( $name ); ?></h3>
											<div class="wd-product-card__price"><?php echo $price_html; ?></div>
											<?php
												$avg = (float) $product->get_average_rating();
												$full = (int) round( $avg );
												$empty = 5 - $full;
												?>
												<div class="wd-product-card__rating wd-static-stars"><span class="wd-stars-filled"><?php echo str_repeat( '★', $full ); ?></span><span class="wd-stars-empty"><?php echo str_repeat( '☆', $empty ); ?></span></div>
										</div>
									</a>
									<div class="woodmart-add-btn">
										<a href="<?php echo esc_url( $add_to_cart_url ); ?>" data-quantity="1" class="button product_type_<?php echo esc_attr( $product_type ); ?> add_to_cart_button ajax_add_to_cart add-to-cart-loop" data-product_id="<?php echo esc_attr( $pid ); ?>" data-product_sku="<?php echo esc_attr( $sku ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Add to cart: "%s"', $name ) ); ?>" rel="nofollow" data-success_message="<?php echo esc_attr( $success_msg ); ?>" role="button"><span>Add to cart</span></a>
									</div>
								</div>
								<?php
							}
							?>
							</div>
						</div>
					<?php
					endforeach;
					if ( empty( $holiday_products ) ) :
					?>
						<div class="item">
							<div class="wd-holiday-slide-grid">
								<p class="wd-holiday-no-products">No products in "This Holiday" yet. Add category slug <strong>this-holiday</strong> to products.</p>
							</div>
						</div>
					<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'customized_gifts', 'customized_gifts_shortcode' );

/**
 * Shortcode: Our best selling products listed for you
 * Usage: [best_selling]
 */
function best_selling_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	wp_enqueue_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4' );
	wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array( 'owl-carousel-css' ), '2.3.4' );
	wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );

	ob_start();
	?>
	.wd-best-selling { padding: 50px 0 60px; background: #fff; }
	.wd-best-selling .wd-best-selling-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
	.wd-best-selling .wd-best-selling-title { margin: 0; font-size: 32px; font-weight: 700; color: #222; text-align: left; }
	.wd-best-selling .wd-best-selling-title span { color: #c41e5a; }
	.wd-best-selling .wd-best-selling-nav { display: flex; gap: 8px; }
	.wd-best-selling .wd-best-selling-nav .owl-prev,
	.wd-best-selling .wd-best-selling-nav .owl-next { width: 44px; height: 44px; border-radius: 50%; background: #fff !important; box-shadow: 0 4px 16px rgba(0,0,0,.12) !important; color: #333 !important; font-size: 20px !important; padding: 0 !important; margin: 0 !important; line-height: 1; display: flex !important; align-items: center; justify-content: center; cursor: pointer; transition: background .25s ease, color .25s ease; border: none !important; }
	.wd-best-selling .wd-best-selling-nav .owl-prev:hover,
	.wd-best-selling .wd-best-selling-nav .owl-next:hover { background: #c41e5a !important; color: #fff !important; }
	.wd-best-selling .wd-best-selling-slider { margin: 0 -6px; }
	.wd-best-selling .wd-best-selling-slider .owl-item { padding: 0 6px; }
	.wd-best-selling .wd-product-card { background: #fff; border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; transition: transform .25s ease, box-shadow .25s ease; height: 100%; display: flex; flex-direction: column; position: relative; }
	.wd-best-selling .wd-product-card:hover { transform: translateY(-4px); }
	.wd-best-selling .wd-product-card__link { display: block; text-decoration: none; color: inherit; flex: 1; }
	.wd-best-selling .woodmart-add-btn { display: block; width: 100%; margin-top: 0; padding: 0 16px 16px; }
	.wd-best-selling .woodmart-add-btn .button { display: block !important; width: 100% !important; text-align: center; box-sizing: border-box; }
	.wd-best-selling .wd-product-card__badge { position: absolute; top: 12px; left: 12px; background: #c41e5a; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 4px; z-index: 2; text-transform: uppercase; }
	.wd-best-selling .wd-product-card__img-wrap { position: relative; padding-bottom: 100%; background: #f8f8f8; overflow: hidden; }
	.wd-best-selling .wd-product-card__img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; }
	.wd-best-selling .wd-product-card__body { padding: 16px; }
	.wd-best-selling .wd-product-card__title { margin: 0 0 10px; font-size: 16px; font-weight: 600; color: #222; line-height: 1.3; min-height: 30px; }
	.wd-best-selling .wd-product-card__price { margin-bottom: 10px; }
	.wd-best-selling .wd-product-card__price del { font-size: 14px; color: #999; margin-right: 6px; }
	.wd-best-selling .wd-product-card__price .wd-price-current,
	.wd-best-selling .wd-product-card__price .amount,
	.wd-best-selling .wd-product-card__price .woocommerce-Price-amount { font-size: 18px; font-weight: 700; color: #c41e5a; }
	.wd-best-selling .wd-product-card__price del .amount,
	.wd-best-selling .wd-product-card__price del .woocommerce-Price-amount { color: #999; font-weight: normal; }
	.wd-best-selling .wd-product-card__rating { color: #ffb400; font-size: 18px; margin-bottom: 12px; letter-spacing: 2px; line-height: 1; }
	.wd-best-selling .wd-product-card__btn { display: inline-block; background: #de056f !important; color: #fff !important; font-size: 13px; font-weight: 600; padding: 10px 18px; border-radius: 6px; text-align: center; transition: background .25s ease; }
	.wd-best-selling .wd-product-card__btn:hover { background: #b80459 !important; color: #fff !important; }
	.wd-best-selling .woodmart-add-btn .button { background: #de056f !important; color: #fff !important; font-size: 13px; font-weight: 600; padding: 10px 18px; border-radius: 6px; transition: background .25s ease; text-decoration: none !important; border: none; }
	.wd-best-selling .woodmart-add-btn .button:hover { background: #b80459 !important; color: #fff !important; }
	.wd-best-selling .wd-best-selling-slider.owl-carousel .owl-dots { display: none !important; }
	.wd-best-selling .wd-best-selling-empty { padding: 24px 16px; text-align: center; color: #666; margin: 0; }
	<?php
	$best_selling_css = ob_get_clean();

	$owl_best = "
		jQuery(function($){
			$('.wd-best-selling .wd-best-selling-slider').length && $('.wd-best-selling .wd-best-selling-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, navContainer: '.wd-best-selling-nav', navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
		});
	";
	wp_add_inline_script( 'owl-carousel-js', $owl_best );

	ob_start();
	?>
	<style>
	<?php echo $best_selling_css; ?>
	</style>
	<div class="wd-best-selling">
		<div class="container">
			<div class="wd-best-selling-header">
				<h2 class="wd-best-selling-title">Our <span>best selling</span> products listed for you.</h2>
				<div class="wd-best-selling-nav"></div>
			</div>
			<div class="owl-carousel wd-best-selling-slider">
			<?php
			$best_selling_products = array();
			if ( class_exists( 'WooCommerce' ) ) {
				$best_query = new WP_Query( array(
					'post_type'      => 'product',
					'posts_per_page' => 12,
					'post_status'    => 'publish',
					'meta_key'       => 'total_sales',
					'orderby'        => 'meta_value_num',
					'order'          => 'DESC',
				) );
				if ( $best_query->have_posts() ) {
					while ( $best_query->have_posts() ) {
						$best_query->the_post();
						$best_selling_products[] = wc_get_product( get_the_ID() );
					}
					wp_reset_postdata();
				}
				// Fallback: when no sales yet, show latest products
				if ( empty( $best_selling_products ) ) {
					$fallback = new WP_Query( array(
						'post_type'      => 'product',
						'posts_per_page' => 12,
						'post_status'    => 'publish',
						'orderby'        => 'date',
						'order'          => 'DESC',
					) );
					if ( $fallback->have_posts() ) {
						while ( $fallback->have_posts() ) {
							$fallback->the_post();
							$best_selling_products[] = wc_get_product( get_the_ID() );
						}
						wp_reset_postdata();
					}
				}
			}
			foreach ( $best_selling_products as $product ) :
				if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
					continue;
				}
				$pid   = $product->get_id();
				$url   = $product->get_permalink();
				$img_id = $product->get_image_id();
				$name  = $product->get_name();
				$on_sale = $product->is_on_sale();
				$price_html = $product->get_price_html();
				$add_to_cart_url = $product->add_to_cart_url();
				$avg = (float) $product->get_average_rating();
				$full = (int) round( $avg );
				$empty = 5 - $full;
				$product_type = $product->get_type();
				$sku = $product->get_sku();
				$success_msg = sprintf( '"%s" has been added to your cart', $name );
			?>
				<div class="item">
					<div class="wd-product-card">
						<a href="<?php echo esc_url( $url ); ?>" class="wd-product-card__link">
							<?php if ( $on_sale ) : ?>
								<span class="wd-product-card__badge">Sale</span>
							<?php endif; ?>
							<div class="wd-product-card__img-wrap">
								<?php if ( $img_id ) : ?>
									<img class="wd-product-card__img" src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
								<?php else : ?>
									<img class="wd-product-card__img" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
								<?php endif; ?>
							</div>
							<div class="wd-product-card__body">
								<h3 class="wd-product-card__title"><?php echo esc_html( $name ); ?></h3>
								<div class="wd-product-card__price"><?php echo $price_html; ?></div>
								<div class="wd-product-card__rating"><span class="wd-stars-filled"><?php echo str_repeat( '★', $full ); ?></span><span class="wd-stars-empty"><?php echo str_repeat( '☆', $empty ); ?></span></div>
							</div>
						</a>
						<div class="woodmart-add-btn">
							<a href="<?php echo esc_url( $add_to_cart_url ); ?>" data-quantity="1" class="button product_type_<?php echo esc_attr( $product_type ); ?> add_to_cart_button ajax_add_to_cart add-to-cart-loop" data-product_id="<?php echo esc_attr( $pid ); ?>" data-product_sku="<?php echo esc_attr( $sku ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Add to cart: "%s"', $name ) ); ?>" rel="nofollow" data-success_message="<?php echo esc_attr( $success_msg ); ?>" role="button"><span>Add to cart</span></a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			<?php if ( empty( $best_selling_products ) ) : ?>
				<div class="item">
					<p class="wd-best-selling-empty">No products yet. Sales data will appear here.</p>
				</div>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'best_selling', 'best_selling_shortcode' );

/**
 * Shortcode: We craft all our products with love
 * Usage: [craft_with_love]
 */
function craft_with_love_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	wp_enqueue_style( 'owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4' );
	wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array( 'owl-carousel-css' ), '2.3.4' );
	wp_enqueue_script( 'owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );

	ob_start();
	?>
	.wd-craft-with-love { padding: 50px 0 60px; background: #fff; }
	.wd-craft-with-love .wd-craft-with-love-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
	.wd-craft-with-love .wd-craft-with-love-title { margin: 0; font-size: 32px; font-weight: 700; color: #222; text-align: left; }
	.wd-craft-with-love .wd-craft-with-love-title span { color: #c41e5a; }
	.wd-craft-with-love .wd-craft-with-love-nav { display: flex; gap: 8px; }
	.wd-craft-with-love .wd-craft-with-love-nav .owl-prev,
	.wd-craft-with-love .wd-craft-with-love-nav .owl-next { width: 44px; height: 44px; border-radius: 50%; background: #fff !important; box-shadow: 0 4px 16px rgba(0,0,0,.12) !important; color: #333 !important; font-size: 20px !important; padding: 0 !important; margin: 0 !important; line-height: 1; display: flex !important; align-items: center; justify-content: center; cursor: pointer; transition: background .25s ease, color .25s ease; border: none !important; }
	.wd-craft-with-love .wd-craft-with-love-nav .owl-prev:hover,
	.wd-craft-with-love .wd-craft-with-love-nav .owl-next:hover { background: #c41e5a !important; color: #fff !important; }
	.wd-craft-with-love .wd-craft-with-love-slider { margin: 0 -6px; }
	.wd-craft-with-love .wd-craft-with-love-slider .owl-item { padding: 0 6px; }
	.wd-craft-with-love .wd-product-card { background: #fff; border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; transition: transform .25s ease, box-shadow .25s ease; height: 100%; display: flex; flex-direction: column; position: relative; }
	.wd-craft-with-love .wd-product-card:hover { transform: translateY(-4px); }
	.wd-craft-with-love .wd-product-card .wd-product-card__link { display: block; text-decoration: none; color: inherit; flex: 1; }
	.wd-craft-with-love .wd-product-card__badge { position: absolute; top: 12px; left: 12px; background: #c41e5a; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 4px; z-index: 2; text-transform: uppercase; }
	.wd-craft-with-love .wd-product-card__img-wrap { position: relative; padding-bottom: 100%; background: #f8f8f8; overflow: hidden; }
	.wd-craft-with-love .wd-product-card__img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; }
	.wd-craft-with-love .wd-product-card__body { padding: 16px; }
	.wd-craft-with-love .wd-product-card__title { margin: 0 0 10px; font-size: 16px; font-weight: 600; color: #222; line-height: 1.3; min-height: 30px; }
	.wd-craft-with-love .wd-product-card__price { margin-bottom: 10px; }
	.wd-craft-with-love .wd-product-card__price del { font-size: 14px; color: #999; margin-right: 6px; }
	.wd-craft-with-love .wd-product-card__price .wd-price-current,
	.wd-craft-with-love .wd-product-card__price .amount,
	.wd-craft-with-love .wd-product-card__price .woocommerce-Price-amount { font-size: 18px; font-weight: 700; color: #c41e5a; }
	.wd-craft-with-love .wd-product-card__price del .amount,
	.wd-craft-with-love .wd-product-card__price del .woocommerce-Price-amount { color: #999; font-weight: normal; }
	.wd-craft-with-love .wd-product-card__rating { color: #ffb400; font-size: 18px; margin-bottom: 12px; letter-spacing: 2px; line-height: 1; }
	.wd-craft-with-love .wd-product-card__btn { display: inline-block; background: #de056f !important; color: #fff !important; font-size: 13px; font-weight: 600; padding: 10px 18px; border-radius: 6px; text-align: center; transition: background .25s ease; }
	.wd-craft-with-love .wd-product-card__btn:hover { background: #b80459 !important; color: #fff !important; }
	.wd-craft-with-love .woodmart-add-btn { display: block; width: 100%; margin-top: 0; padding: 0 16px 16px; }
	.wd-craft-with-love .woodmart-add-btn .button { display: block !important; width: 100% !important; background: #de056f !important; color: #fff !important; font-size: 13px; font-weight: 600; padding: 10px 18px; border-radius: 6px; text-align: center; transition: background .25s ease; text-decoration: none !important; border: none; box-sizing: border-box; }
	.wd-craft-with-love .woodmart-add-btn .button:hover { background: #b80459 !important; color: #fff !important; }
	.wd-craft-with-love .wd-craft-with-love-slider.owl-carousel .owl-dots { display: none !important; }
	.wd-craft-with-love .wd-craft-with-love-empty { padding: 24px 16px; text-align: center; color: #666; margin: 0; }
	<?php
	$craft_css = ob_get_clean();

	$owl_craft = "
		jQuery(function($){
			$('.wd-craft-with-love .wd-craft-with-love-slider').length && $('.wd-craft-with-love .wd-craft-with-love-slider').owlCarousel({ items: 4, loop: true, margin: 12, nav: true, dots: false, navContainer: '.wd-craft-with-love-nav', navText: ['<span aria-label=\"Prev\">&#10094;</span>','<span aria-label=\"Next\">&#10095;</span>'], autoplay: true, autoplayTimeout: 4000, autoplayHoverPause: true, responsive: { 0: { items: 1, margin: 8 }, 480: { items: 2, margin: 8 }, 768: { items: 3, margin: 10 }, 1024: { items: 4, margin: 12 } } });
		});
	";
	wp_add_inline_script( 'owl-carousel-js', $owl_craft );

	$page_id = get_queried_object_id();
	if ( ! $page_id && is_front_page() && get_option( 'page_on_front' ) ) {
		$page_id = (int) get_option( 'page_on_front' );
	}
	if ( ! $page_id && ! empty( $GLOBALS['post'] ) ) {
		$page_id = get_the_ID();
	}
	$craft_product_ids = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'craft_with_love_products', $page_id ) : null;
	$craft_products    = array();
	if ( ! empty( $craft_product_ids ) && is_array( $craft_product_ids ) && class_exists( 'WooCommerce' ) ) {
		foreach ( $craft_product_ids as $pid ) {
			$product = wc_get_product( $pid );
			if ( $product && is_a( $product, 'WC_Product' ) ) {
				$craft_products[] = $product;
			}
		}
	}

	ob_start();
	?>
	<style>
	<?php echo $craft_css; ?>
	</style>
	<div class="wd-craft-with-love">
		<div class="container">
			<div class="wd-craft-with-love-header">
				<h2 class="wd-craft-with-love-title">We craft all <span>our products</span> with love.</h2>
				<div class="wd-craft-with-love-nav"></div>
			</div>
			<div class="owl-carousel wd-craft-with-love-slider">
			<?php
			if ( ! empty( $craft_products ) ) {
				foreach ( $craft_products as $product ) {
					$pid    = $product->get_id();
					$url    = $product->get_permalink();
					$img_id = $product->get_image_id();
					$name   = $product->get_name();
					$on_sale = $product->is_on_sale();
					$price_html = $product->get_price_html();
					$add_to_cart_url = $product->add_to_cart_url();
					$avg = (float) $product->get_average_rating();
					$full = (int) round( $avg );
					$empty = 5 - $full;
					$product_type = $product->get_type();
					$sku = $product->get_sku();
					$success_msg = sprintf( '"%s" has been added to your cart', $name );
					?>
				<div class="item">
					<div class="wd-product-card">
						<a href="<?php echo esc_url( $url ); ?>" class="wd-product-card__link">
							<?php if ( $on_sale ) : ?>
								<span class="wd-product-card__badge">Sale</span>
							<?php endif; ?>
							<div class="wd-product-card__img-wrap">
								<?php if ( $img_id ) : ?>
									<img class="wd-product-card__img" src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
								<?php else : ?>
									<img class="wd-product-card__img" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
								<?php endif; ?>
							</div>
							<div class="wd-product-card__body">
								<h3 class="wd-product-card__title"><?php echo esc_html( $name ); ?></h3>
								<div class="wd-product-card__price"><?php echo $price_html; ?></div>
								<div class="wd-product-card__rating"><span class="wd-stars-filled"><?php echo str_repeat( '★', $full ); ?></span><span class="wd-stars-empty"><?php echo str_repeat( '☆', $empty ); ?></span></div>
							</div>
						</a>
						<div class="woodmart-add-btn">
							<a href="<?php echo esc_url( $add_to_cart_url ); ?>" data-quantity="1" class="button product_type_<?php echo esc_attr( $product_type ); ?> add_to_cart_button ajax_add_to_cart add-to-cart-loop wd-product-card__btn" data-product_id="<?php echo esc_attr( $pid ); ?>" data-product_sku="<?php echo esc_attr( $sku ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Add to cart: "%s"', $name ) ); ?>" rel="nofollow" data-success_message="<?php echo esc_attr( $success_msg ); ?>" role="button"><span>Add to Cart</span></a>
						</div>
					</div>
				</div>
					<?php
				}
			} else {
				$static_items = array(
					array( 'img' => 'best-sell-1.png', 'title' => 'Baby Clock', 'price' => '<del>$143</del> <span class="wd-price-current">$57</span>' ),
					array( 'img' => 'best-sell-2.png', 'title' => 'Couple Keychain', 'price' => '<del>$143</del> <span class="wd-price-current">$57</span>' ),
					array( 'img' => 'best-sell-3.png', 'title' => 'Crafty Album', 'price' => '<del>$143</del> <span class="wd-price-current">$57</span>' ),
					array( 'img' => 'best-sell-4.png', 'title' => 'Panda Lamp', 'price' => '<del>$143</del> <span class="wd-price-current">$57</span>' ),
				);
				foreach ( $static_items as $s ) {
					?>
				<div class="item">
					<a href="#" class="wd-product-card">
						<span class="wd-product-card__badge">Sale</span>
						<div class="wd-product-card__img-wrap">
							<img class="wd-product-card__img" src="<?php echo esc_url( $img_base . '/' . $s['img'] ); ?>" alt="<?php echo esc_attr( $s['title'] ); ?>">
						</div>
						<div class="wd-product-card__body">
							<h3 class="wd-product-card__title"><?php echo esc_html( $s['title'] ); ?></h3>
							<div class="wd-product-card__price"><?php echo $s['price']; ?></div>
							<div class="wd-product-card__rating">★★★★★</div>
							<span class="wd-product-card__btn">Add to Cart</span>
						</div>
					</a>
				</div>
					<?php
				}
			}
			?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'craft_with_love', 'craft_with_love_shortcode' );

/**
 * Shortcode: Recommendation - Our best Exclusive Deals
 * Usage: [recommendation]
 * Add images in theme: custom/images/recom-1.jpg, recom-2.jpg, recom-3.jpg, recom-4.jpg, recom-5.jpg
 */
function recommendation_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	ob_start();
	?>
	.wd-recommendation { padding: 50px 0 60px; background: #F9F5F6; }
	.wd-recommendation .wd-recommendation-title { font-size: 36px; font-weight: 700; color: #E6007A; margin: 0 0 8px; text-align: center; letter-spacing: -0.02em; }
	.wd-recommendation .wd-recommendation-subtitle { font-size: 16px; color: #555; margin: 0 0 32px; text-align: center; }
	.wd-recommendation-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
	.wd-recommendation .wd-recom-box { border-radius: 14px; padding: 28px 24px; min-height: 280px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; background-size: cover; background-position: center; background-repeat: no-repeat; }
	.wd-recommendation .wd-recom-box.wd-recom-box--first-double { grid-column: 1 / span 2; }
	.wd-recommendation .wd-recom-box.wd-recom-box--row2-col1 { grid-row: 2; grid-column: 1; }
	.wd-recommendation .wd-recom-box.wd-recom-box--row2-col2 { grid-row: 2; grid-column: 2; }
	.wd-recommendation .wd-recom-box.wd-recom-box--row2-col3 { grid-row: 2; grid-column: 3; }
	.wd-recommendation .wd-recom-box::before { content: ''; position: absolute; inset: 0; z-index: 0; border-radius: 14px; opacity: 0; transition: opacity 0.35s ease; }
	.wd-recommendation .wd-recom-box:hover::before { opacity: 1; }
	.wd-recommendation .wd-recom-box .wd-recom-box-content { position: relative; z-index: 1; text-align: center; opacity: 0; transition: opacity 0.35s ease; }
	.wd-recommendation .wd-recom-box:hover .wd-recom-box-content { opacity: 1; }
	.wd-recommendation .wd-recom-box h3 { margin: 0 0 10px; font-size: 20px; font-weight: 700; color: #fff; line-height: 1.3; text-shadow: 0 1px 3px rgba(0,0,0,.3); }
	.wd-recommendation .wd-recom-box span { display: block; margin-bottom: 16px; font-size: 15px; color: rgba(255,255,255,.95); text-shadow: 0 1px 2px rgba(0,0,0,.2); }
	.wd-recommendation .wd-recom-box .wd-recom-btn { display: inline-block; background: #fff; color: #c41e5a !important; font-size: 14px; font-weight: 700; padding: 10px 24px; border-radius: 8px; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease; }
	.wd-recommendation .wd-recom-box .wd-recom-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.25); color: #c41e5a !important; }
	.wd-recommendation .wd-recom-box-1 { grid-column: 1 / span 2; background-color: #c82e66; background-image: url(<?php echo esc_url( $img_base ); ?>/recom-1.png); }
	.wd-recommendation .wd-recom-box-1::before { background: linear-gradient(90deg, rgba(200,46,102,.92) 0%, rgba(139,21,56,.75) 100%); }
	.wd-recommendation .wd-recom-box-2 { grid-column: 3; background-color: #2b3345; background-image: url(<?php echo esc_url( $img_base ); ?>/recom-2.png); }
	.wd-recommendation .wd-recom-box-2::before { background: linear-gradient(180deg, rgba(43,51,69,.88) 0%, rgba(26,32,44,.9) 100%); }
	.wd-recommendation .wd-recom-box-3 { grid-row: 2; grid-column: 1; background-color: #c82e66; background-image: url(<?php echo esc_url( $img_base ); ?>/recom-3.png); }
	.wd-recommendation .wd-recom-box-3::before { background: linear-gradient(180deg, rgba(200,46,102,.85) 0%, rgba(160,24,72,.9) 100%); }
	.wd-recommendation .wd-recom-box-4 { grid-row: 2; grid-column: 2; background-color: #2b3345; background-image: url(<?php echo esc_url( $img_base ); ?>/recom-4.png); }
	.wd-recommendation .wd-recom-box-4::before { background: linear-gradient(180deg, rgba(43,51,69,.88) 0%, rgba(26,32,44,.9) 100%); }
	.wd-recommendation .wd-recom-box-5 { grid-row: 2; grid-column: 3; background-color: #c82e66; background-image: url(<?php echo esc_url( $img_base ); ?>/recom-5.png); }
	.wd-recommendation .wd-recom-box-5::before { background: linear-gradient(180deg, rgba(200,46,102,.85) 0%, rgba(139,21,56,.9) 100%); }
	.wd-recommendation .wd-recom-box-dynamic::before { background: linear-gradient(180deg, rgba(0,0,0,.5) 0%, rgba(0,0,0,.75) 100%); }
	@media (max-width: 767px) { .wd-recommendation-grid { grid-template-columns: 1fr; } .wd-recommendation .wd-recom-box-1 { grid-column: 1; } .wd-recommendation .wd-recom-box--first-double { grid-column: 1; } .wd-recommendation .wd-recom-box--row2-col1, .wd-recommendation .wd-recom-box--row2-col2, .wd-recommendation .wd-recom-box--row2-col3, .wd-recommendation .wd-recom-box-3, .wd-recommendation .wd-recom-box-4, .wd-recommendation .wd-recom-box-5 { grid-row: auto; grid-column: auto; } }
	<?php
	$recommendation_css = ob_get_clean();

	// Page ID for ACF: prefer front page when on home, then queried object, then current post
	$page_id = 0;
	if ( is_front_page() && get_option( 'page_on_front' ) ) {
		$page_id = (int) get_option( 'page_on_front' );
	}
	if ( ! $page_id ) {
		$page_id = get_queried_object_id();
	}
	if ( ! $page_id && ! empty( $GLOBALS['post'] ) ) {
		$page_id = get_the_ID();
	}

	$rec_title  = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'recommendation_title', $page_id ) : null;
	$rec_sub    = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'recommendation_subtitle', $page_id ) : null;
	$rec_boxes  = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'recommendation_boxes', $page_id ) : null;
	$rec_boxes  = is_array( $rec_boxes ) ? $rec_boxes : array();
	$use_acf    = count( $rec_boxes ) > 0;

	$title_text    = ( $use_acf && (string) $rec_title !== '' ) ? $rec_title : 'Recommendation';
	$subtitle_text = ( $use_acf && (string) $rec_sub !== '' ) ? $rec_sub : 'Our best Exclusive Deals listed for you.';

	ob_start();
	?>
	<style>
	<?php echo $recommendation_css; ?>
	</style>
	<div class="wd-recommendation">
		<div class="container">
			<h2 class="wd-recommendation-title"><?php echo esc_html( $title_text ); ?></h2>
			<p class="wd-recommendation-subtitle"><?php echo esc_html( $subtitle_text ); ?></p>
			<div class="wd-recommendation-grid">
			<?php
			if ( $use_acf ) {
				$rec_boxes = array_slice( $rec_boxes, 0, 5 );
				foreach ( $rec_boxes as $index => $row ) {
					$img   = isset( $row['box_image'] ) && is_array( $row['box_image'] ) ? $row['box_image'] : null;
					$title = isset( $row['box_title'] ) ? $row['box_title'] : 'Get your customized gifts';
					$price = isset( $row['box_price_text'] ) ? $row['box_price_text'] : 'Starts from ₹ 299';
					$url   = isset( $row['box_button_url'] ) && $row['box_button_url'] ? $row['box_button_url'] : '#';
					$bg_style = 'background-color:#c82e66;';
					if ( $img && ! empty( $img['url'] ) ) {
						$bg_style .= 'background-image:url(' . esc_url( $img['url'] ) . ');';
					}
					$box_class = 'wd-recom-box wd-recom-box-dynamic';
					if ( $index === 0 ) { $box_class .= ' wd-recom-box--first-double'; }
					if ( $index === 2 ) { $box_class .= ' wd-recom-box--row2-col1'; }
					if ( $index === 3 ) { $box_class .= ' wd-recom-box--row2-col2'; }
					if ( $index === 4 ) { $box_class .= ' wd-recom-box--row2-col3'; }
					?>
				<div class="<?php echo esc_attr( $box_class ); ?>" style="<?php echo $bg_style ? esc_attr( $bg_style ) : ''; ?>">
					<div class="wd-recom-box-content">
						<h3><?php echo esc_html( $title ); ?></h3>
						<span><?php echo wp_kses_post( $price ); ?></span>
						<a href="<?php echo esc_url( $url ); ?>" class="wd-recom-btn">Shop Now</a>
					</div>
				</div>
					<?php
				}
			} else {
				$static_boxes = array(
					array( 'class' => 'wd-recom-box-1', 'title' => 'Get your customized gifts', 'price' => 'Starts from <b>₹ 299</b>' ),
					array( 'class' => 'wd-recom-box-2', 'title' => 'Get your customized gifts', 'price' => 'Starts from <b>₹ 299</b>' ),
					array( 'class' => 'wd-recom-box-3', 'title' => 'Get your customized gifts', 'price' => 'Starts from <b>₹ 299</b>' ),
					array( 'class' => 'wd-recom-box-4', 'title' => 'Get your customized gifts', 'price' => 'Starts from <b>₹ 299</b>' ),
					array( 'class' => 'wd-recom-box-5', 'title' => 'Get your customized gifts', 'price' => 'Starts from <b>₹ 299</b>' ),
				);
				foreach ( $static_boxes as $s ) {
					?>
				<div class="wd-recom-box <?php echo esc_attr( $s['class'] ); ?>">
					<div class="wd-recom-box-content">
						<h3><?php echo esc_html( $s['title'] ); ?></h3>
						<span><?php echo wp_kses_post( $s['price'] ); ?></span>
						<a href="#" class="wd-recom-btn">Shop Now</a>
					</div>
				</div>
					<?php
				}
			}
			?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'recommendation', 'recommendation_shortcode' );

/**
 * Shortcode: My Reels (video/reel thumbnails grid) – ACF repeater, fallback static
 * Usage: [my_reels]
 */
function my_reels_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	$page_id = get_queried_object_id();
	if ( ! $page_id && is_front_page() && get_option( 'page_on_front' ) ) {
		$page_id = (int) get_option( 'page_on_front' );
	}
	if ( ! $page_id && ! empty( $GLOBALS['post'] ) ) {
		$page_id = get_the_ID();
	}
	$reels = ( $page_id && function_exists( 'get_field' ) ) ? get_field( 'my_reels', $page_id ) : null;
	$reels = is_array( $reels ) ? $reels : array();

	// Static fallback when no repeater rows
	if ( empty( $reels ) ) {
		$reels = array(
			array( 'image' => null, 'url' => '#', 'alt' => 'Reel 1' ),
			array( 'image' => null, 'url' => '#', 'alt' => 'Reel 2' ),
			array( 'image' => null, 'url' => '#', 'alt' => 'Reel 3' ),
			array( 'image' => null, 'url' => '#', 'alt' => 'Reel 4' ),
		);
		$fallback_imgs = array( 'video-1.png', 'video-2.png', 'video-4.png', 'video-5.png' );
		foreach ( $reels as $i => &$row ) {
			$row['_fallback_src'] = $img_base . '/' . ( isset( $fallback_imgs[ $i ] ) ? $fallback_imgs[ $i ] : 'video-1.png' );
		}
		unset( $row );
	}

	ob_start();
	?>
	.wd-my-reels { padding: 50px 0 60px; background: #fff; }
	.wd-my-reels-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
	.wd-my-reels-grid a { display: block; border-radius: 12px; overflow: hidden; aspect-ratio: 1; }
	.wd-my-reels-grid img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s ease; }
	.wd-my-reels-grid a:hover img { transform: scale(1.05); }
	@media (max-width: 991px) { .wd-my-reels-grid { grid-template-columns: repeat(2, 1fr); } }
	@media (max-width: 575px) { .wd-my-reels-grid { grid-template-columns: 1fr; aspect-ratio: 16/10; } }
	<?php
	$reels_css = ob_get_clean();

	ob_start();
	?>
	<style><?php echo $reels_css; ?></style>
	<div class="wd-my-reels">
		<div class="container">
			<div class="wd-my-reels-grid">
				<?php
				foreach ( $reels as $row ) {
					$url  = isset( $row['url'] ) ? $row['url'] : '#';
					$alt  = isset( $row['alt'] ) ? $row['alt'] : '';
					if ( ! empty( $row['_fallback_src'] ) ) {
						$src = $row['_fallback_src'];
					} elseif ( ! empty( $row['image'] ) && is_array( $row['image'] ) && ! empty( $row['image']['url'] ) ) {
						$src = $row['image']['url'];
					} else {
						$src = $img_base . '/video-1.png';
					}
					?>
				<a href="<?php echo esc_url( $url ); ?>"><img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>"></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'my_reels', 'my_reels_shortcode' );

/**
 * Shortcode: Get Special Discounts (newsletter / mail list)
 * Usage: [special_discounts]
 * Image: custom/images/letter.png
 */
function special_discounts_shortcode() {
	$img_base = get_template_directory_uri() . '/custom/images';

	wp_enqueue_style( 'sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css', array(), '11' );
	wp_enqueue_script( 'sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), '11', true );
	wp_localize_script( 'sweetalert2', 'wdSpecialDiscounts', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_add_inline_script( 'sweetalert2', "
		document.addEventListener('DOMContentLoaded', function() {
			var form = document.getElementById('wd-special-discounts-form');
			if (!form || typeof Swal === 'undefined') return;
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				var emailInp = document.getElementById('wd-discount-email');
				var btn = form.querySelector('.wd-discount-btn');
				var email = emailInp ? emailInp.value.trim() : '';
				if (!email) return;
				if (btn) btn.disabled = true;
				var data = new FormData();
				data.append('action', 'wd_subscribe_email');
				data.append('nonce', form.querySelector('[name=\"wd_subscribe_nonce\"]') ? form.querySelector('[name=\"wd_subscribe_nonce\"]').value : '');
				data.append('email', email);
				fetch(window.wdSpecialDiscounts && window.wdSpecialDiscounts.ajax_url ? window.wdSpecialDiscounts.ajax_url : '', { method: 'POST', body: data, credentials: 'same-origin' })
					.then(function(r) { return r.json(); })
					.then(function(res) {
						if (res.success) {
							if (emailInp) emailInp.value = '';
							Swal.fire({ icon: 'success', title: 'Subscribed!', text: (res.data && res.data.message) ? res.data.message : 'Thank you! You are now subscribed.', confirmButtonColor: '#E91E63' });
						} else {
							Swal.fire({ icon: 'error', title: 'Oops...', text: (res.data && res.data.message) ? res.data.message : 'This email is already subscribed.', confirmButtonColor: '#E91E63' });
						}
					})
					.catch(function() {
						Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.', confirmButtonColor: '#E91E63' });
					})
					.finally(function() { if (btn) btn.disabled = false; });
			});
		});
	", 'after' );

	ob_start();
	?>
	.wd-special-discounts { padding: 0; overflow: hidden; }
	.wd-special-discounts-inner { display: flex; flex-wrap: wrap; align-items: stretch; min-height: 200px; }
	.wd-special-discounts__img-wrap { flex: 0 0 33.333%; min-width: 280px; display: flex; align-items: center; justify-content: center; padding: 20px; }
	.wd-special-discounts__img-wrap img { max-width: 100%; height: auto; max-height: 220px; object-fit: contain; }
	.wd-special-discounts__content { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 40px 30px 40px 24px; ; }
	.wd-special-discounts__content h2 { margin: 0 0 8px; font-size: 28px; font-weight: 700; color: #000; }
	.wd-special-discounts__content p { margin: 0 0 20px; font-size: 16px; color: #0000009c; }
	.wd-special-discounts__form { display: flex; flex-wrap: nowrap; max-width: 480px; border-radius: 999px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
	.wd-special-discounts__form input[type="email"] { flex: 1; min-width: 0; padding: 14px 20px; border: none; border-radius: 999px 0 0 999px; font-size: 15px; background: #fff; color: #333; }
	.wd-special-discounts__form input[type="email"]::placeholder { color: #888; }
	.wd-special-discounts__form .wd-discount-btn { background: #E91E63; color: #fff !important; font-size: 15px; font-weight: 700; padding: 14px 28px; border: none; border-radius: 0 999px 999px 0; cursor: pointer; text-decoration: none; transition: background .25s ease; white-space: nowrap; }
	.wd-special-discounts__form .wd-discount-btn:hover { background: #c2185b; color: #fff !important; }
	@media (max-width: 767px) { .wd-special-discounts__img-wrap { flex: 0 0 100%; min-height: 160px; } .wd-special-discounts__content { padding: 30px 20px; } }
	<?php
	$discount_css = ob_get_clean();

	ob_start();
	?>
	<style><?php echo $discount_css; ?></style>
	<div class="wd-special-discounts">
		<div class="wd-special-discounts-inner">
			<div class="wd-special-discounts__img-wrap">
				<img src="<?php echo esc_url( $img_base . '/letter.png' ); ?>" alt="">
			</div>
			<div class="wd-special-discounts__content">
				<h2>Get Special Discounts</h2>
				<p>by joining our mail list</p>
				<form id="wd-special-discounts-form" class="wd-special-discounts__form" action="#" method="post">
					<?php wp_nonce_field( 'wd_subscribe', 'wd_subscribe_nonce', false ); ?>
					<input type="email" name="discount_email" id="wd-discount-email" placeholder="Enter Email Address" required>
					<button type="submit" class="wd-discount-btn">Subscribe</button>
				</form>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'special_discounts', 'special_discounts_shortcode' );

/**
 * WooCommerce -> WhatsApp template automation.
 *
 * NOTE: For better security move token/phone-id into wp-config.php constants.
 */
if ( ! defined( 'WD_WA_ACCESS_TOKEN' ) ) {
	define( 'WD_WA_ACCESS_TOKEN', 'EAAU0kMTKwXABRUeNdxRByC4k1gygP0jTJLlMeIaS1EjKiYc3GeB8joCFHRxohjoO200l5kpuZAfecT26OtV47X30ZADXmzqwZAtYYTELS0cl7is6O3zMtOK5dTYa88PCJkIk9JJt9Yd1LfZAZCNB6Pvy7omc8wFwtyjPWlKzNo20JsMycuhD5wMB6GeFXZBUVgxQZDZD' );
}
if ( ! defined( 'WD_WA_PHONE_NUMBER_ID' ) ) {
	define( 'WD_WA_PHONE_NUMBER_ID', '1048758624993759' );
}
if ( ! defined( 'WD_WA_API_VERSION' ) ) {
	define( 'WD_WA_API_VERSION', 'v25.0' );
}
if ( ! defined( 'WD_WA_TEMPLATE_LANGUAGE' ) ) {
	define( 'WD_WA_TEMPLATE_LANGUAGE', 'en' );
}

/**
 * Register custom statuses for shipment lifecycle.
 */
function wd_register_custom_wc_statuses() {
	register_post_status(
		'wc-shipped',
		array(
			'label'                     => 'Shipped',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' ),
		)
	);

	register_post_status(
		'wc-out-for-delivery',
		array(
			'label'                     => 'Out for delivery',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'Out for delivery <span class="count">(%s)</span>', 'Out for delivery <span class="count">(%s)</span>' ),
		)
	);
}
add_action( 'init', 'wd_register_custom_wc_statuses' );

/**
 * Add custom statuses into WooCommerce status list.
 *
 * @param array $order_statuses Existing statuses.
 * @return array
 */
function wd_add_custom_wc_statuses( $order_statuses ) {
	$customized = array();

	foreach ( $order_statuses as $status_key => $status_label ) {
		$customized[ $status_key ] = $status_label;

		// Insert shipped after processing for natural operational flow.
		if ( 'wc-processing' === $status_key ) {
			$customized['wc-shipped'] = 'Shipped';
		}

		// Insert out for delivery after shipped.
		if ( 'wc-shipped' === $status_key ) {
			$customized['wc-out-for-delivery'] = 'Out for delivery';
		}
	}

	// Fallback insert if positions were not reached.
	if ( ! isset( $customized['wc-shipped'] ) ) {
		$customized['wc-shipped'] = 'Shipped';
	}
	if ( ! isset( $customized['wc-out-for-delivery'] ) ) {
		$customized['wc-out-for-delivery'] = 'Out for delivery';
	}

	return $customized;
}
add_filter( 'wc_order_statuses', 'wd_add_custom_wc_statuses' );

/**
 * Normalize customer phone into WhatsApp API expected format (E.164 digits only).
 *
 * @param string $raw_phone Raw billing phone.
 * @return string
 */
function wd_wa_normalize_phone( $raw_phone ) {
	$phone = preg_replace( '/\D+/', '', (string) $raw_phone );
	if ( empty( $phone ) ) {
		return '';
	}

	// Convert 00 prefix to plain country format.
	if ( 0 === strpos( $phone, '00' ) ) {
		$phone = substr( $phone, 2 );
	}

	$default_country_code = (string) apply_filters( 'wd_wa_default_country_code', '92' );
	$default_country_code = preg_replace( '/\D+/', '', $default_country_code );
	$default_country_code = $default_country_code ? $default_country_code : '92';

	// Local format (e.g. 03...) -> add default country code.
	if ( 0 === strpos( $phone, '0' ) ) {
		$phone = $default_country_code . substr( $phone, 1 );
	}

	return $phone;
}

/**
 * Base64 URL-safe encode.
 *
 * @param string $binary Raw binary/string data.
 * @return string
 */
function wd_wa_base64url_encode( $binary ) {
	return rtrim( strtr( base64_encode( $binary ), '+/', '-_' ), '=' );
}

/**
 * Base64 URL-safe decode.
 *
 * @param string $value Encoded URL-safe base64 string.
 * @return string|false
 */
function wd_wa_base64url_decode( $value ) {
	$remainder = strlen( $value ) % 4;
	if ( $remainder ) {
		$value .= str_repeat( '=', 4 - $remainder );
	}

	return base64_decode( strtr( $value, '-_', '+/' ), true );
}

/**
 * Encrypt and sign track-order payload.
 *
 * @param int    $order_id Order ID.
 * @param string $email    Billing email.
 * @return string
 */
function wd_wa_create_tracking_token( $order_id, $email ) {
	$payload = wp_json_encode(
		array(
			'order_id' => absint( $order_id ),
			'email'    => sanitize_email( $email ),
			'ts'       => time(),
		)
	);
	if ( ! $payload ) {
		return '';
	}

	$key       = hash( 'sha256', wp_salt( 'auth' ), true );
	$iv_length = (int) openssl_cipher_iv_length( 'aes-256-cbc' );
	try {
		$iv = random_bytes( $iv_length );
	} catch ( Exception $e ) {
		$iv = openssl_random_pseudo_bytes( $iv_length );
	}
	$cipher    = openssl_encrypt( $payload, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );
	if ( false === $cipher ) {
		return '';
	}

	$signature = hash_hmac( 'sha256', $iv . $cipher, $key, true );
	return wd_wa_base64url_encode( $iv . $signature . $cipher );
}

/**
 * Decrypt and verify track-order payload token.
 *
 * @param string $token Encrypted token.
 * @return array|false
 */
function wd_wa_parse_tracking_token( $token ) {
	$raw = wd_wa_base64url_decode( (string) $token );
	if ( false === $raw || strlen( $raw ) < 49 ) {
		return false;
	}

	$key       = hash( 'sha256', wp_salt( 'auth' ), true );
	$iv_length = (int) openssl_cipher_iv_length( 'aes-256-cbc' );
	$iv        = substr( $raw, 0, $iv_length );
	$signature = substr( $raw, $iv_length, 32 );
	$cipher    = substr( $raw, $iv_length + 32 );

	$expected_signature = hash_hmac( 'sha256', $iv . $cipher, $key, true );
	if ( ! hash_equals( $expected_signature, $signature ) ) {
		return false;
	}

	$decrypted = openssl_decrypt( $cipher, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );
	if ( false === $decrypted ) {
		return false;
	}

	$data = json_decode( $decrypted, true );
	if ( ! is_array( $data ) || empty( $data['order_id'] ) || empty( $data['email'] ) ) {
		return false;
	}

	// Token validity: 30 days.
	if ( ! empty( $data['ts'] ) && ( time() - absint( $data['ts'] ) ) > ( 30 * DAY_IN_SECONDS ) ) {
		return false;
	}

	return array(
		'order_id' => absint( $data['order_id'] ),
		'email'    => sanitize_email( $data['email'] ),
	);
}

/**
 * Build secure tracking URL for WhatsApp message.
 *
 * @param WC_Order $order Order object.
 * @return string
 */
function wd_wa_get_secure_tracking_url( $order ) {
	$token = wd_wa_create_tracking_token( $order->get_id(), $order->get_billing_email() );
	if ( ! $token ) {
		return home_url( '/track-order/' );
	}

	return add_query_arg(
		array(
			'wdot' => $token,
		),
		home_url( '/track-order/' )
	);
}

/**
 * Auto-fill Woo order tracking form when secure token is present.
 *
 * @return void
 */
function wd_wa_prefill_track_order_request() {
	if ( is_admin() || ! function_exists( 'is_page' ) || ! is_page( 'track-order' ) ) {
		return;
	}

	if ( empty( $_GET['wdot'] ) ) {
		return;
	}

	$token_data = wd_wa_parse_tracking_token( sanitize_text_field( wp_unslash( $_GET['wdot'] ) ) );
	if ( ! $token_data ) {
		return;
	}

	$order = wc_get_order( $token_data['order_id'] );
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	if ( strtolower( trim( $order->get_billing_email() ) ) !== strtolower( trim( $token_data['email'] ) ) ) {
		return;
	}

	// WooCommerce tracking shortcode reads these POST values.
	$order_lookup_value = (string) $order->get_order_number();

	// Populate all request bags because themes/plugins may read POST/GET/REQUEST differently.
	$_POST['orderid']     = $order_lookup_value;
	$_POST['order_email'] = (string) $token_data['email'];
	$_POST['track']       = '1';

	$_GET['orderid']     = $order_lookup_value;
	$_GET['order_email'] = (string) $token_data['email'];
	$_GET['track']       = '1';

	$_REQUEST['orderid']     = $order_lookup_value;
	$_REQUEST['order_email'] = (string) $token_data['email'];
	$_REQUEST['track']       = '1';
}
add_action( 'wp', 'wd_wa_prefill_track_order_request', 5 );

/**
 * Auto-submit track-order form when secure token link is used.
 *
 * @return void
 */
function wd_wa_auto_submit_track_order_form() {
	if ( is_admin() || ! function_exists( 'is_page' ) || ! is_page( 'track-order' ) ) {
		return;
	}

	if ( empty( $_GET['wdot'] ) ) {
		return;
	}
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var form = document.querySelector('form.woocommerce-form-track-order, form.track_order, form[name="track_order"]');
			if (!form) {
				var orderInput = document.querySelector('input[name="orderid"]');
				form = orderInput ? orderInput.closest('form') : null;
			}
			if (!form || form.dataset.wdAutoSubmitted === '1') {
				return;
			}

			var orderInput = form.querySelector('input[name="orderid"]');
			var emailInput = form.querySelector('input[name="order_email"]');
			if (!orderInput || !emailInput || !orderInput.value || !emailInput.value) {
				return;
			}

			form.dataset.wdAutoSubmitted = '1';
			form.submit();
		});
	</script>
	<?php
}
add_action( 'wp_footer', 'wd_wa_auto_submit_track_order_form', 99 );

/**
 * Build template components (body params) for WhatsApp templates.
 *
 * @param string   $template_name Template name.
 * @param WC_Order $order         Order object.
 * @return array
 */
function wd_wa_get_template_components( $template_name, $order ) {
	$first_name = trim( (string) $order->get_billing_first_name() );
	$first_name = $first_name ? $first_name : __( 'Customer', 'woodmart' );

	$order_number = (string) $order->get_order_number();
	$order_total  = number_format( (float) $order->get_total(), 2, '.', '' );
	$order_date   = $order->get_date_created();
	$order_date   = $order_date ? wp_date( get_option( 'date_format' ), $order_date->getTimestamp() ) : wp_date( get_option( 'date_format' ) );
	$payment      = $order->get_payment_method_title();
	$payment      = $payment ? $payment : __( 'N/A', 'woodmart' );

	$build_header_body = static function( $header_value, $body_values ) {
		$components = array(
			array(
				'type'       => 'header',
				'parameters' => array(
					array(
						'type' => 'text',
						'text' => (string) $header_value,
					),
				),
			),
		);

		$body_params = array();
		foreach ( $body_values as $value ) {
			$body_params[] = array(
				'type' => 'text',
				'text' => (string) $value,
			);
		}

		if ( ! empty( $body_params ) ) {
			$components[] = array(
				'type'       => 'body',
				'parameters' => $body_params,
			);
		}

		return $components;
	};

	// Optional shipment-specific placeholders (can be overridden through filter/meta).
	$courier_name  = (string) $order->get_meta( '_wd_courier_name', true );
	$tracking_id   = (string) $order->get_meta( '_wd_tracking_id', true );
	$tracking_link = (string) $order->get_meta( '_wd_tracking_link', true );

	$courier_name  = $courier_name ? $courier_name : __( 'Courier Partner', 'woodmart' );
	$tracking_id   = $tracking_id ? $tracking_id : __( 'Tracking ID pending', 'woodmart' );
	$tracking_link = $tracking_link ? $tracking_link : wd_wa_get_secure_tracking_url( $order );

	$components_map = array(
		'order_pending_payment'    => $build_header_body( $first_name, array( $order_number, $order_total, $order_date ) ),
		'order_processing'         => $build_header_body( $first_name, array( $order_number, $order_total, $payment ) ),
		'order_on_hold'            => $build_header_body( $first_name, array( $order_number, $order_total ) ),
		'order_completed'          => $build_header_body( $first_name, array( $order_number, $order_total, $order_date ) ),
		'order_cancelled'          => $build_header_body( $first_name, array( $order_number, $order_total ) ),
		'order_refunded'           => $build_header_body( $first_name, array( $order_number, $order_total ) ),
		'order_shipped'            => $build_header_body( $first_name, array( $order_number, $order_total ) ),
		'shipment_courier_details' => $build_header_body( $first_name, array( $order_number, $courier_name, $tracking_id ) ),
		'order_tracking_link'      => $build_header_body( $first_name, array( $order_number, $tracking_link ) ),
		'out_for_delivery'         => $build_header_body( $first_name, array( $order_number, $courier_name ) ),
	);

	$components = isset( $components_map[ $template_name ] ) ? $components_map[ $template_name ] : $build_header_body( $first_name, array( $order_number, $order_total ) );

	/**
	 * Allow full override of template components from other snippets/plugins.
	 */
	return apply_filters( 'wd_wa_template_components', $components, $template_name, $order );
}

/**
 * Send one WhatsApp template message for an order.
 *
 * @param int    $order_id       WooCommerce order ID.
 * @param string $template_name  WhatsApp approved template name.
 * @param string $language_code  Template language code.
 * @return bool
 */
function wd_wa_send_template_for_order( $order_id, $template_name, $language_code = '' ) {
	$order = wc_get_order( $order_id );
	if ( ! $order instanceof WC_Order ) {
		return false;
	}

	if ( empty( WD_WA_ACCESS_TOKEN ) || empty( WD_WA_PHONE_NUMBER_ID ) ) {
		return false;
	}

	$customer_phone = wd_wa_normalize_phone( $order->get_billing_phone() );
	if ( empty( $customer_phone ) ) {
		return false;
	}

	$dedupe_key = '_wd_wa_sent_' . sanitize_key( $template_name );
	if ( $order->get_meta( $dedupe_key ) ) {
		return true;
	}

	$endpoint = sprintf( 'https://graph.facebook.com/%s/%s/messages', WD_WA_API_VERSION, WD_WA_PHONE_NUMBER_ID );
	$language = $language_code ? $language_code : WD_WA_TEMPLATE_LANGUAGE;

	$payload = array(
		'messaging_product' => 'whatsapp',
		'to'                => $customer_phone,
		'type'              => 'template',
		'template'          => array(
			'name'     => $template_name,
			'language' => array(
				'code' => $language,
			),
			'components' => wd_wa_get_template_components( $template_name, $order ),
		),
	);

	/**
	 * Allow custom template components/parameters from other snippets/plugins.
	 */
	$payload = apply_filters( 'wd_wa_template_payload', $payload, $order, $template_name );

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 25,
			'headers' => array(
				'Authorization' => 'Bearer ' . WD_WA_ACCESS_TOKEN,
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		return false;
	}

	$order->update_meta_data( $dedupe_key, gmdate( 'c' ) );
	$order->save();

	return true;
}

/**
 * Schedule template send after a delay.
 *
 * @param int    $order_id      Order ID.
 * @param string $template_name Template name.
 * @param int    $delay_seconds Delay in seconds.
 * @return void
 */
function wd_wa_schedule_template_send( $order_id, $template_name, $delay_seconds ) {
	$delay_seconds = max( 1, absint( $delay_seconds ) );
	$args          = array( absint( $order_id ), sanitize_key( $template_name ) );
	$timestamp     = time() + $delay_seconds;

	if ( ! wp_next_scheduled( 'wd_wa_send_template_event', $args ) ) {
		wp_schedule_single_event( $timestamp, 'wd_wa_send_template_event', $args );
	}
}

/**
 * Process delayed WhatsApp sends.
 *
 * @param int    $order_id      Order ID.
 * @param string $template_name Template name.
 * @return void
 */
function wd_wa_process_scheduled_send( $order_id, $template_name ) {
	wd_wa_send_template_for_order( absint( $order_id ), sanitize_key( $template_name ) );
}
add_action( 'wd_wa_send_template_event', 'wd_wa_process_scheduled_send', 10, 2 );

/**
 * Return status -> template mapping for WhatsApp automation.
 *
 * @return array
 */
function wd_wa_get_status_template_map() {
	return array(
		'pending'          => array( 'order_pending_payment' ),
		'processing'       => array( 'order_processing' ),
		'on-hold'          => array( 'order_on_hold' ),
		'completed'        => array( 'order_completed' ),
		'cancelled'        => array( 'order_cancelled' ),
		'refunded'         => array( 'order_refunded' ),
		'shipped'          => array( 'order_shipped' ),
		'out-for-delivery' => array( 'out_for_delivery' ),
	);
}

/**
 * Status -> template workflow.
 *
 * @param int      $order_id   Order ID.
 * @param string   $from       Previous status slug.
 * @param string   $to         New status slug.
 * @param WC_Order $order      Order object.
 * @return void
 */
function wd_wa_on_order_status_changed( $order_id, $from, $to, $order ) {
	$status_template_map = wd_wa_get_status_template_map();

	$enabled_statuses = wd_wa_get_enabled_statuses( array_keys( $status_template_map ) );
	if ( ! in_array( $to, $enabled_statuses, true ) ) {
		return;
	}

	if ( isset( $status_template_map[ $to ] ) ) {
		foreach ( $status_template_map[ $to ] as $template_name ) {
			wd_wa_send_template_for_order( $order_id, $template_name );
		}
	}

	// Multi-step sequence for shipped status.
	if ( 'shipped' === $to ) {
		wd_wa_schedule_template_send( $order_id, 'shipment_courier_details', 5 * MINUTE_IN_SECONDS );
		wd_wa_schedule_template_send( $order_id, 'order_tracking_link', 10 * MINUTE_IN_SECONDS );
	}
}
add_action( 'woocommerce_order_status_changed', 'wd_wa_on_order_status_changed', 20, 4 );

/**
 * Return all statuses available for WhatsApp automation settings.
 *
 * @return array
 */
function wd_wa_get_available_statuses() {
	return array(
		'pending'          => __( 'Pending payment', 'woodmart' ),
		'processing'       => __( 'Processing', 'woodmart' ),
		'on-hold'          => __( 'On hold', 'woodmart' ),
		'completed'        => __( 'Completed', 'woodmart' ),
		'cancelled'        => __( 'Cancelled', 'woodmart' ),
		'refunded'         => __( 'Refunded', 'woodmart' ),
		'shipped'          => __( 'Shipped', 'woodmart' ),
		'out-for-delivery' => __( 'Out for delivery', 'woodmart' ),
	);
}

/**
 * Read enabled statuses from DB and ensure valid values only.
 *
 * @param array $fallback Fallback statuses if option missing.
 * @return array
 */
function wd_wa_get_enabled_statuses( $fallback = array() ) {
	$available = array_keys( wd_wa_get_available_statuses() );
	$saved     = get_option( 'wd_wa_enabled_statuses', array() );
	$saved     = is_array( $saved ) ? $saved : array();
	$enabled   = array_values( array_intersect( $available, $saved ) );

	if ( empty( $enabled ) ) {
		$fallback = empty( $fallback ) ? $available : $fallback;
		$enabled  = array_values( array_intersect( $available, $fallback ) );
	}

	return $enabled;
}

/**
 * Add WhatsApp status settings page under WooCommerce menu.
 *
 * @return void
 */
function wd_wa_register_settings_submenu() {
	add_submenu_page(
		'woocommerce',
		__( 'WhatsApp Status Settings', 'woodmart' ),
		__( 'WhatsApp Status Settings', 'woodmart' ),
		'manage_woocommerce',
		'wd-wa-status-settings',
		'wd_wa_render_settings_page'
	);
}
add_action( 'admin_menu', 'wd_wa_register_settings_submenu', 99 );

/**
 * Handle WhatsApp status settings save request.
 *
 * @return void
 */
function wd_wa_handle_settings_save() {
	if ( ! isset( $_POST['wd_wa_settings_nonce'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	check_admin_referer( 'wd_wa_save_settings', 'wd_wa_settings_nonce' );

	$submitted = isset( $_POST['enabled_statuses'] ) ? (array) wp_unslash( $_POST['enabled_statuses'] ) : array();
	$submitted = array_map( 'sanitize_text_field', $submitted );
	$available = array_keys( wd_wa_get_available_statuses() );
	$enabled   = array_values( array_intersect( $available, $submitted ) );

	update_option( 'wd_wa_enabled_statuses', $enabled );
	add_settings_error( 'wd_wa_messages', 'wd_wa_saved', __( 'WhatsApp status settings saved.', 'woodmart' ), 'updated' );
}
add_action( 'admin_init', 'wd_wa_handle_settings_save' );

/**
 * Render WhatsApp status settings page.
 *
 * @return void
 */
function wd_wa_render_settings_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$available_statuses = wd_wa_get_available_statuses();
	$enabled_statuses   = wd_wa_get_enabled_statuses( array_keys( $available_statuses ) );
	$template_map       = wd_wa_get_status_template_map();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'WhatsApp Status Settings', 'woodmart' ); ?></h1>
		<?php settings_errors( 'wd_wa_messages' ); ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'wd_wa_save_settings', 'wd_wa_settings_nonce' ); ?>
			<p>
				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'woodmart' ); ?>">
			</p>
			<table class="widefat striped" style="max-width:900px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Order Status', 'woodmart' ); ?></th>
						<th><?php esc_html_e( 'Linked Template(s)', 'woodmart' ); ?></th>
						<th><?php esc_html_e( 'Enable Message Trigger', 'woodmart' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $available_statuses as $status_key => $status_label ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $status_label ); ?></strong> <code><?php echo esc_html( $status_key ); ?></code></td>
							<td>
								<?php
								$templates = isset( $template_map[ $status_key ] ) ? $template_map[ $status_key ] : array();
								echo $templates ? esc_html( implode( ', ', $templates ) ) : '&mdash;';
								?>
							</td>
							<td>
								<label>
									<input type="checkbox" name="enabled_statuses[]" value="<?php echo esc_attr( $status_key ); ?>" <?php checked( in_array( $status_key, $enabled_statuses, true ) ); ?>>
									<?php esc_html_e( 'Enabled', 'woodmart' ); ?>
								</label>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p>
				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'woodmart' ); ?>">
			</p>
		</form>
	</div>
	<?php
}

/**
 * Add bulk actions to quickly move orders to shipping statuses.
 *
 * @param array $actions Existing bulk actions.
 * @return array
 */
function wd_add_shipping_order_bulk_actions( $actions ) {
	$actions['mark_shipped']          = __( 'Change status to Shipped', 'woodmart' );
	$actions['mark_out_for_delivery'] = __( 'Change status to Out for delivery', 'woodmart' );

	return $actions;
}
add_filter( 'bulk_actions-edit-shop_order', 'wd_add_shipping_order_bulk_actions', 30 );
add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'wd_add_shipping_order_bulk_actions', 30 );

/**
 * Handle custom shipping bulk actions for both legacy orders screen and HPOS screen.
 *
 * @param string $redirect_to Redirect URL.
 * @param string $action      Triggered bulk action.
 * @param array  $order_ids   Selected order IDs.
 * @return string
 */
function wd_handle_shipping_order_bulk_actions( $redirect_to, $action, $order_ids ) {
	$status = '';
	if ( 'mark_shipped' === $action ) {
		$status = 'shipped';
	} elseif ( 'mark_out_for_delivery' === $action ) {
		$status = 'out-for-delivery';
	}

	if ( empty( $status ) || empty( $order_ids ) ) {
		return $redirect_to;
	}

	$updated = 0;
	foreach ( $order_ids as $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order ) {
			continue;
		}

		$order->update_status( $status, __( 'Order status changed via bulk action.', 'woodmart' ), true );
		$updated++;
	}

	return add_query_arg(
		array(
			'wd_bulk_status'  => $status,
			'wd_bulk_updated' => $updated,
		),
		$redirect_to
	);
}
add_filter( 'handle_bulk_actions-edit-shop_order', 'wd_handle_shipping_order_bulk_actions', 20, 3 );
add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', 'wd_handle_shipping_order_bulk_actions', 20, 3 );

/**
 * Show admin notice after shipping bulk action.
 *
 * @return void
 */
function wd_shipping_bulk_action_admin_notice() {
	if ( empty( $_REQUEST['wd_bulk_updated'] ) || empty( $_REQUEST['wd_bulk_status'] ) ) {
		return;
	}

	$updated = absint( wp_unslash( $_REQUEST['wd_bulk_updated'] ) );
	$status  = sanitize_text_field( wp_unslash( $_REQUEST['wd_bulk_status'] ) );

	if ( $updated <= 0 ) {
		return;
	}

	$status_label = 'shipped' === $status ? __( 'Shipped', 'woodmart' ) : __( 'Out for delivery', 'woodmart' );
	/* translators: 1: number of orders, 2: new status label */
	$message = sprintf( _n( '%1$s order moved to %2$s.', '%1$s orders moved to %2$s.', $updated, 'woodmart' ), number_format_i18n( $updated ), $status_label );

	echo '<div class="updated notice is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}
add_action( 'admin_notices', 'wd_shipping_bulk_action_admin_notice' );

/**
 * Add per-order quick actions in WooCommerce Orders list.
 *
 * @param array    $actions Order actions.
 * @param WC_Order $order   Order object.
 * @return array
 */
function wd_add_shipping_order_row_actions( $actions, $order ) {
	if ( ! $order instanceof WC_Order ) {
		return $actions;
	}

	$order_id = $order->get_id();

	$actions['wd_mark_shipped'] = array(
		'url'    => wp_nonce_url(
			add_query_arg(
				array(
					'action'   => 'wd_mark_order_status',
					'order_id' => $order_id,
					'status'   => 'shipped',
				),
				admin_url( 'admin-post.php' )
			),
			'wd_mark_order_status_' . $order_id
		),
		'name'   => __( 'Mark shipped', 'woodmart' ),
		'action' => 'processing',
	);

	$actions['wd_mark_out_for_delivery'] = array(
		'url'    => wp_nonce_url(
			add_query_arg(
				array(
					'action'   => 'wd_mark_order_status',
					'order_id' => $order_id,
					'status'   => 'out-for-delivery',
				),
				admin_url( 'admin-post.php' )
			),
			'wd_mark_order_status_' . $order_id
		),
		'name'   => __( 'Mark out for delivery', 'woodmart' ),
		'action' => 'processing',
	);

	return $actions;
}
add_filter( 'woocommerce_admin_order_actions', 'wd_add_shipping_order_row_actions', 30, 2 );

/**
 * Handle row-level custom order status action.
 *
 * @return void
 */
function wd_handle_mark_order_status_action() {
	if ( ! current_user_can( 'edit_shop_orders' ) ) {
		wp_die( esc_html__( 'You are not allowed to edit orders.', 'woodmart' ) );
	}

	$order_id = isset( $_GET['order_id'] ) ? absint( wp_unslash( $_GET['order_id'] ) ) : 0;
	$status   = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';

	if ( ! $order_id || ! in_array( $status, array( 'shipped', 'out-for-delivery' ), true ) ) {
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' ) );
		exit;
	}

	check_admin_referer( 'wd_mark_order_status_' . $order_id );

	$order = wc_get_order( $order_id );
	if ( $order instanceof WC_Order ) {
		$order->update_status( $status, __( 'Order status changed from row action.', 'woodmart' ), true );
	}

	$redirect = wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' );
	$redirect = add_query_arg(
		array(
			'wd_bulk_status'  => $status,
			'wd_bulk_updated' => 1,
		),
		$redirect
	);

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_wd_mark_order_status', 'wd_handle_mark_order_status_action' );

/**
 * Keep Woo primary action buttons visually consistent with Add to Cart color.
 *
 * @return void
 */
function wd_style_woocommerce_primary_checkout_buttons() {
	?>
	<style id="wd-woo-primary-checkout-buttons">
		.woocommerce .wc-proceed-to-checkout a.checkout-button,
		.woocommerce-page .wc-proceed-to-checkout a.checkout-button,
		.woocommerce .cart_totals .wc-proceed-to-checkout a.button.checkout-button,
		.woocommerce-page .cart_totals .wc-proceed-to-checkout a.button.checkout-button,
		.woocommerce #payment #place_order,
		.woocommerce-page #payment #place_order,
		.woocommerce-checkout #place_order,
		.woocommerce-checkout button#place_order,
		.woocommerce-checkout input#place_order {
			background-color: #e91e63 !important;
			border-color: #e91e63 !important;
			color: #ffffff !important;
			display: inline-block !important;
			padding: 12px 22px !important;
			border-width: 1px !important;
			border-style: solid !important;
			border-radius: 8px !important;
			text-decoration: none !important;
			opacity: 1 !important;
			box-shadow: none !important;
		}

		.woocommerce .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce-page .wc-proceed-to-checkout a.checkout-button:hover,
		.woocommerce .cart_totals .wc-proceed-to-checkout a.button.checkout-button:hover,
		.woocommerce-page .cart_totals .wc-proceed-to-checkout a.button.checkout-button:hover,
		.woocommerce #payment #place_order:hover,
		.woocommerce-page #payment #place_order:hover,
		.woocommerce-checkout #place_order:hover,
		.woocommerce-checkout button#place_order:hover,
		.woocommerce-checkout input#place_order:hover {
			background-color: #c2185b !important;
			border-color: #c2185b !important;
			color: #ffffff !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'wd_style_woocommerce_primary_checkout_buttons', 99 );
add_action( 'wp_footer', 'wd_style_woocommerce_primary_checkout_buttons', 999 );

/**
 * Force style on rendered checkout action buttons (handles theme/AJAX overrides).
 *
 * @return void
 */
function wd_force_checkout_buttons_runtime_style() {
	?>
	<script id="wd-force-checkout-buttons-style">
		(function () {
			function applyBtnStyle(el) {
				if (!el) return;
				el.style.setProperty('background', '#e91e63', 'important');
				el.style.setProperty('background-color', '#e91e63', 'important');
				el.style.setProperty('border-color', '#e91e63', 'important');
				el.style.setProperty('border', '1px solid #e91e63', 'important');
				el.style.setProperty('color', '#ffffff', 'important');
				el.style.setProperty('fill', '#ffffff', 'important');
				el.style.setProperty('padding', '12px 22px', 'important');
				el.style.setProperty('border-radius', '8px', 'important');
				el.style.setProperty('text-decoration', 'none', 'important');
				el.style.setProperty('opacity', '1', 'important');
				el.style.setProperty('--wp--preset--color--primary', '#e91e63', 'important');
				el.style.setProperty('--wc-form-primary', '#e91e63', 'important');

				var textEls = el.querySelectorAll('.wc-block-components-button__text, .wc-block-components-checkout-place-order-button__text');
				textEls.forEach(function (textEl) {
					textEl.style.setProperty('color', '#ffffff', 'important');
				});
			}

			function applyAll() {
				var selectors = [
					'.wc-proceed-to-checkout a.checkout-button',
					'.cart_totals .wc-proceed-to-checkout a.button.checkout-button',
					'#place_order',
					'button#place_order',
					'input#place_order',
					'.wc-block-cart__submit .wc-block-components-button',
					'.wc-block-cart__submit .wc-block-cart__submit-button',
					'.wc-block-components-checkout-place-order-button'
				];
				document.querySelectorAll(selectors.join(',')).forEach(applyBtnStyle);
			}

			document.addEventListener('DOMContentLoaded', applyAll);
			window.addEventListener('load', applyAll);
			setTimeout(applyAll, 600);
			setTimeout(applyAll, 1600);

			var observer = new MutationObserver(function () {
				applyAll();
			});
			observer.observe(document.documentElement, { childList: true, subtree: true });
		})();
	</script>
	<?php
}
add_action( 'wp_footer', 'wd_force_checkout_buttons_runtime_style', 1000 );

/**
 * Render cart "Proceed to checkout" button with primary theme classes.
 *
 * @return void
 */
function wd_render_custom_proceed_to_checkout_button() {
	$style = 'background:#e91e63 !important;border:1px solid #e91e63 !important;color:#fff !important;padding:12px 22px !important;border-radius:8px !important;display:inline-block !important;text-decoration:none !important;';
	echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="checkout-button button alt wc-forward btn btn-color-primary" style="' . esc_attr( $style ) . '">' . esc_html__( 'Proceed to checkout', 'woocommerce' ) . '</a>';
}

/**
 * Replace Woo default proceed-to-checkout button output.
 *
 * @return void
 */
function wd_replace_default_proceed_to_checkout_button() {
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		add_action( 'woocommerce_proceed_to_checkout', 'wd_render_custom_proceed_to_checkout_button', 20 );
	}
}
add_action( 'wp', 'wd_replace_default_proceed_to_checkout_button', 30 );

/**
 * Replace checkout place-order button HTML with theme primary classes.
 *
 * @param string $button_html Default place order button HTML.
 * @return string
 */
function wd_custom_place_order_button_html( $button_html ) {
	$button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );
	$style       = 'background:#e91e63 !important;border:1px solid #e91e63 !important;color:#fff !important;padding:12px 22px !important;border-radius:8px !important;display:inline-block !important;text-decoration:none !important;';

	return '<button type="submit" class="button alt btn btn-color-primary" style="' . esc_attr( $style ) . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $button_text ) . '" data-value="' . esc_attr( $button_text ) . '">' . esc_html( $button_text ) . '</button>';
}
add_filter( 'woocommerce_order_button_html', 'wd_custom_place_order_button_html', 20, 1 );

/**
 * Fix stuck checkout overlay on order-pay endpoint.
 *
 * @return void
 */
function wd_fix_order_pay_overlay_issue() {
	if ( ! function_exists( 'is_checkout_pay_page' ) || ! is_checkout_pay_page() ) {
		return;
	}

	// Disabled on order-pay: this script/CSS breaks Razorpay checkout modal (black box / browser not supported).
	return;

	?>
	<style id="wd-fix-order-pay-overlay">
		body.woocommerce-order-pay .blockOverlay,
		body.woocommerce-order-pay .woocommerce .blockOverlay,
		body.woocommerce-order-pay form.checkout.processing > .blockOverlay,
		body.woocommerce-order-pay .blockUI,
		body.woocommerce-order-pay .woocommerce .blockUI {
			display: none !important;
			opacity: 0 !important;
			visibility: hidden !important;
			pointer-events: none !important;
		}
		body.woocommerce-order-pay {
			overflow: auto !important;
		}
	</style>
	<script>
		(function () {
			function isLikelyOverlay(el) {
				if (!el || !el.style) return false;
				var cls = (el.className || '').toString();
				if (cls.indexOf('razorpay') !== -1) {
					return false;
				}
				if (cls.indexOf('blockOverlay') !== -1 || cls.indexOf('blockUI') !== -1) {
					return true;
				}
				var style = (el.getAttribute('style') || '').toLowerCase();
				if (!style) return false;
				var isFixed = style.indexOf('position: fixed') !== -1;
				var hasInset = style.indexOf('inset: 0') !== -1 || (style.indexOf('top: 0') !== -1 && style.indexOf('left: 0') !== -1);
				var hasBigZ = /z-index:\s*(\d{3,}|[1-9]\d{2,})/.test(style);
				var hasOpacity = style.indexOf('opacity') !== -1 || style.indexOf('background') !== -1;
				return isFixed && hasInset && hasBigZ && hasOpacity;
			}

			function clearOverlay() {
				if (!document.body) {
					return;
				}
				document.querySelectorAll('.blockOverlay, .blockUI').forEach(function (el) {
					if (el && el.parentNode) {
						el.remove();
					}
				});
				document.querySelectorAll('body.woocommerce-order-pay *').forEach(function (el) {
					if (el && isLikelyOverlay(el) && el.parentNode) {
						el.remove();
					}
				});
				document.querySelectorAll('form.checkout.processing, form.processing, .processing').forEach(function (el) {
					if (el && el.classList) {
						el.classList.remove('processing');
						el.style.removeProperty('position');
					}
				});
				document.body.classList.remove('processing');
				document.body.style.overflow = 'auto';
			}
			document.addEventListener('DOMContentLoaded', clearOverlay);
			window.addEventListener('load', clearOverlay);
			setTimeout(clearOverlay, 300);
			setTimeout(clearOverlay, 1200);
			setInterval(clearOverlay, 1200);

			var observer = new MutationObserver(function () {
				clearOverlay();
			});
			observer.observe(document.documentElement, { childList: true, subtree: true });
		})();
	</script>
	<?php
}
add_action( 'wp_head', 'wd_fix_order_pay_overlay_issue', 999 );

/**
 * Shop page UI tweaks:
 * - Reduce top banner spacing.
 * - Hide top header category count labels.
 * - Keep sidebar category product counts visible in theme pink color.
 *
 * @return void
 */
function wd_shop_header_and_category_count_tweaks() {
	if ( ! function_exists( 'is_shop' ) ) {
		return;
	}

	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}
	?>
	<style id="wd-shop-header-category-count-tweaks">
		/* 1) Reduce large top/bottom spacing in shop title banner */
		body.post-type-archive-product .page-title,
		body.tax-product_cat .page-title,
		body.tax-product_tag .page-title {
			margin-bottom: 16px !important;
			background-color: #DE056F !important;
		}
		body.post-type-archive-product .title-size-large,
		body.tax-product_cat .title-size-large,
		body.tax-product_tag .title-size-large {
			padding-top: 18px !important;
			padding-bottom: 18px !important;
		}
		body.post-type-archive-product .title-size-default,
		body.tax-product_cat .title-size-default,
		body.tax-product_tag .title-size-default {
			padding-top: 14px !important;
			padding-bottom: 14px !important;
		}

		/* 2) Remove categories strip from top banner area */
		.page-title .woodmart-product-categories,
		.page-title .category-nav-link,
		.page-title .category-products-count,
		.page-title .more-products,
		.shop-title-wrapper + .woodmart-product-categories {
			display: none !important;
		}

		/* 3) Sidebar category product counts in compact theme-like badge */
		.widget_product_categories .count {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			min-width: 24px !important;
			height: 24px !important;
			padding: 0 8px !important;
			border-radius: 999px !important;
			font-size: 11px !important;
			line-height: 1 !important;
			font-weight: 600 !important;
			color: #ffffff !important;
			border-color: #DE056F !important;
			background: #DE056F !important;
			visibility: visible !important;
			transition: none !important;
		}
		.widget_product_categories li:hover > .count,
		.widget_product_categories li.current-cat > .count {
			color: #ffffff !important;
			background-color: #DE056F !important;
			border-color: #DE056F !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'wd_shop_header_and_category_count_tweaks', 999 );

/**
 * Ensure Woo product-category widget always renders product counts.
 *
 * @param array $args Widget args.
 * @return array
 */
function wd_force_sidebar_product_category_counts( $args ) {
	if ( ! function_exists( 'is_shop' ) ) {
		return $args;
	}

	if ( is_shop() || is_product_taxonomy() ) {
		$args['show_count'] = 1;
	}

	return $args;
}
add_filter( 'woocommerce_product_categories_widget_args', 'wd_force_sidebar_product_category_counts', 20 );

/**
 * Header topbar quick content overrides:
 * - Update toll-free phone.
 * - Update support email.
 * - Remove NEWSLETTER link from pre-header.
 *
 * @return void
 */
function wd_override_topbar_contact_content() {
	?>
	<script id="wd-topbar-contact-override">
		(function () {
			function normalizeText(text) {
				return (text || '').replace(/\s+/g, ' ').trim().toLowerCase();
			}

			function applyTopbarOverrides() {
				var topBar = document.querySelector('.whb-top-bar, .topbar-menu, .topbar, .wd-header-top, .whb-main-header');
				if (!topBar) return;

				// Update phone value safely (only small text/link nodes).
				topBar.querySelectorAll('a[href^="tel:"], span, a, p, small').forEach(function (el) {
					var txt = normalizeText(el.textContent);
					if (txt === '+73 099 321 312' || txt === '73099321312' || txt === '+73 099 321312') {
						el.textContent = '+91 76039 36119';
						if (el.tagName.toLowerCase() === 'a') {
							el.setAttribute('href', 'tel:+917603936119');
						}
					}
				});

				// Update email value safely.
				topBar.querySelectorAll('a[href^="mailto:"], span, a, p, small').forEach(function (el) {
					var txt = normalizeText(el.textContent);
					if (txt === 'hand@made.com') {
						el.textContent = 'info@handcrafts.com';
						if (el.tagName.toLowerCase() === 'a') {
							el.setAttribute('href', 'mailto:info@handcrafts.com');
						}
					}
				});

				// Remove NEWSLETTER link only from top bar nav.
				topBar.querySelectorAll('a').forEach(function (link) {
					if (normalizeText(link.textContent) === 'newsletter') {
						var li = link.closest('li');
						if (li) li.remove();
						else link.remove();
					}
				});
			}

			document.addEventListener('DOMContentLoaded', applyTopbarOverrides);
			window.addEventListener('load', applyTopbarOverrides);
			setTimeout(applyTopbarOverrides, 300);
			setTimeout(applyTopbarOverrides, 1200);
		})();
	</script>
	<?php
}
add_action( 'wp_footer', 'wd_override_topbar_contact_content', 1001 );

/**
 * Footer content overrides:
 * - Update phone number.
 * - Remove fax line.
 * - Convert "Our Stores" to "YAHA MENU" with real menu links.
 * - Remove "Our Sitemap" from Useful links.
 * - Remove Footer Menu column.
 *
 * @return void
 */
function wd_override_footer_content() {
	?>
	<script id="wd-footer-content-override">
		(function () {
			function normalize(text) {
				return (text || '').replace(/\s+/g, ' ').trim().toLowerCase();
			}

			function applyFooterOverrides() {
				var footer = document.querySelector('footer.footer-container, footer, .main-footer');
				if (!footer) return;

				// 1) Update footer phone and remove fax line.
				footer.querySelectorAll('.textwidget, p, div').forEach(function (el) {
					if (!el || !el.innerHTML) return;
					if (el.innerHTML.indexOf('Phone:') !== -1) {
						el.innerHTML = el.innerHTML
							.replace(/Phone:\s*[^<\n]+/gi, 'Phone: +91 76039 36119')
							.replace(/<br>\s*<i[^>]*><\/i>\s*Fax:\s*[^<\n]+/gi, '')
							.replace(/Fax:\s*[^<\n]+/gi, '');
					}
				});

				// 2) Build real menu links from header main nav.
				var menuLinks = [];
				document.querySelectorAll('.main-nav .item-level-0 > a, .whb-secondary-menu .item-level-0 > a').forEach(function (a) {
					var text = (a.textContent || '').trim();
					var href = a.getAttribute('href') || '#';
					if (!text || text.length > 40) return;
					if (!menuLinks.some(function (m) { return normalize(m.text) === normalize(text); })) {
						menuLinks.push({ text: text, href: href });
					}
				});
				if (!menuLinks.length) {
					menuLinks = [
						{ text: 'Home', href: '/' },
						{ text: 'Shop', href: '/shop/' },
						{ text: 'Order Tracking', href: '/track-order/' },
						{ text: 'Contact Us', href: '/contact-us/' }
					];
				}

				function toTitleCase(str) {
					return (str || '').toLowerCase().replace(/\b\w/g, function (m) { return m.toUpperCase(); });
				}

				// 3) "Our Stores" -> "MENU" and replace list with selected links.
				footer.querySelectorAll('.widget-title').forEach(function (titleEl) {
					if (normalize(titleEl.textContent) === 'our stores') {
						titleEl.textContent = 'MENU';
						var widget = titleEl.closest('.widget, .woodmart-widget, .footer-column') || titleEl.parentElement;
						if (!widget) return;
						var targetList = widget.querySelector('ul.menu');
						if (!targetList) {
							targetList = document.createElement('ul');
							targetList.className = 'menu';
							widget.appendChild(targetList);
						}
						targetList.innerHTML = '';
						var homeLinkObj    = menuLinks.find(function (m) { return normalize(m.text) === 'home'; }) || { text: 'Home', href: '/' };
						var contactLinkObj = menuLinks.find(function (m) { return normalize(m.text) === 'contact us'; }) || { text: 'Contact Us', href: '/contact-us/' };
						var faqsLinkObj    = menuLinks.find(function (m) { return normalize(m.text) === 'faqs' || normalize(m.text) === 'faq'; }) || { text: 'Faqs', href: '/faqs/' };

						[homeLinkObj, contactLinkObj, faqsLinkObj].forEach(function (item) {
							var li = document.createElement('li');
							var a = document.createElement('a');
							a.href = item.href;
							a.textContent = toTitleCase(item.text);
							li.appendChild(a);
							targetList.appendChild(li);
						});
					}
				});

				// 4) Update Useful links:
				// - remove "Our Sitemap", "Contact Us", "Privacy Policy", "Returns", "Terms & Conditions"
				// - add "Shop" + "Order Tracking"
				footer.querySelectorAll('.widget-title').forEach(function (titleEl) {
					if (normalize(titleEl.textContent) === 'useful links') {
						var widget = titleEl.closest('.widget, .woodmart-widget, .footer-column') || titleEl.parentElement;
						if (!widget) return;
						var usefulList = widget.querySelector('ul.menu');
						if (!usefulList) return;

						widget.querySelectorAll('li, a').forEach(function (el) {
							var txt = normalize(el.textContent);
							if (
								txt === 'our sitemap' ||
								txt === 'contact us' ||
								txt === 'privacy policy' ||
								txt === 'returns' ||
								txt === 'terms & conditions' ||
								txt === 'terms and conditions'
							) {
								var li = el.closest('li');
								if (li) li.remove();
								else el.remove();
							}
						});

						var latestNewsObj = null;
						Array.prototype.forEach.call(usefulList.querySelectorAll('a'), function (a) {
							if (normalize(a.textContent) === 'latest news') {
								latestNewsObj = { text: 'Latest News', href: a.getAttribute('href') || '#' };
							}
						});

						var shopLinkObj  = menuLinks.find(function (m) { return normalize(m.text) === 'shop'; }) || { text: 'Shop', href: '/shop/' };
						var trackLinkObj = menuLinks.find(function (m) { return normalize(m.text) === 'order tracking'; }) || { text: 'Order Tracking', href: '/track-order/' };
						if (!latestNewsObj) {
							latestNewsObj = { text: 'Latest News', href: '/blog/' };
						}

						// Force requested order: Shop -> Order Tracking -> Latest News.
						usefulList.innerHTML = '';
						[shopLinkObj, trackLinkObj, latestNewsObj].forEach(function (item) {
							var li = document.createElement('li');
							var a = document.createElement('a');
							a.href = item.href;
							a.textContent = toTitleCase(item.text);
							li.appendChild(a);
							usefulList.appendChild(li);
						});
					}
				});

				// 5) Build (or create) "Footer Menu" as last column with moved links.
				var footerSidebar = footer.querySelector('.footer-sidebar, .widgets, .row');
				if (footerSidebar) {
					var footerMenuWidget = null;
					footer.querySelectorAll('.widget-title').forEach(function (titleEl) {
						if (normalize(titleEl.textContent) === 'footer menu') {
							footerMenuWidget = titleEl.closest('.widget, .woodmart-widget, .footer-column') || null;
						}
					});

					if (!footerMenuWidget) {
						var col = document.createElement('div');
						col.className = 'footer-column footer-column-custom col-12 col-sm-4 col-lg-2';
						col.innerHTML = '<div class="woodmart-widget widget footer-widget widget_text"><h5 class="widget-title">Footer Menu</h5><div class="textwidget"><ul class="menu"></ul></div></div>';
						footerSidebar.appendChild(col);
						footerMenuWidget = col;
					}

					var footerList = footerMenuWidget.querySelector('ul.menu');
					if (footerList) {
						footerList.innerHTML = '';
						[
							{ text: 'Privacy Policy', href: '/privacy-policy/' },
							{ text: 'Returns', href: '#' },
							{ text: 'Terms & Conditions', href: '/terms-conditions/' }
						].forEach(function (item) {
							var li = document.createElement('li');
							var a = document.createElement('a');
							a.href = item.href;
							a.textContent = item.text;
							li.appendChild(a);
							footerList.appendChild(li);
						});
					}
				}
			}

			document.addEventListener('DOMContentLoaded', applyFooterOverrides);
			window.addEventListener('load', applyFooterOverrides);
			setTimeout(applyFooterOverrides, 300);
			setTimeout(applyFooterOverrides, 1200);
		})();
	</script>
	<?php
}
add_action( 'wp_footer', 'wd_override_footer_content', 1002 );