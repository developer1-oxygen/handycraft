<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* ------------------------------------------------------------------------------------------------
* 3D view - images in 360 slider
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'woodmart_shortcode_3d_view' ) ) {
	function woodmart_shortcode_3d_view( $atts, $content ) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $class = '';
		extract( shortcode_atts( array(
			'images' => '',
			'img_size' => 'full',
			'title' => '',
			'link' => '',
			'style' => '',
			'el_class' => ''
		), $atts ) );

		$id = rand( 100, 999 );

		$images = explode( ',', $images );

		if( $link != '' ) {
			$class .= ' cursor-pointer';
		}

		$class .= ' ' . $el_class;

		$frames_count = count( $images );

		if ( $frames_count < 2 ) return;

		$images_js_string = '';

		$width = $height = 0;

		ob_start(); ?>
			<div class="woodmart-threed-view<?php echo esc_attr( $class ); ?> threed-id-<?php echo esc_attr( $id ); ?>" <?php if( ! empty( $link ) ): ?>onclick="window.location.href='<?php echo esc_js( $link ) ?>'"<?php endif; ?> >
				<?php if ( ! empty( $title ) ): ?>
					<h3 class="threed-title"><span><?php echo wp_kses( $title, woodmart_get_allowed_html() ); ?></span></h3>
				<?php endif ?>
				<ul class="threed-view-images">
					<?php if ( count( $images ) > 0 ): ?>
						<?php $i=0; foreach ( $images as $img_id ): $i++; ?>
							<?php
								$img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'threed-view-image image-' . $i ) );
								$width = $img['p_img_large'][1];
								$height = $img['p_img_large'][2];
								$images_js_string .= "'" . $img['p_img_large'][0] . "'";
								if( $i < $frames_count ) {
									$images_js_string .= ",";
								}
							?>
						<?php endforeach ?>
					<?php endif ?>
				</ul>
			    <div class="spinner">
			        <span>0%</span>
			    </div>
			</div>
		<?php
		woodmart_enqueue_script( 'woodmart-threesixty' );
		
		wp_add_inline_script( 'woodmart-theme', 'jQuery(document).ready(function( $ ) {
		    $(".threed-id-' . esc_js( $id ) . '").ThreeSixty({
		        totalFrames: ' . esc_js( $frames_count ) . ',
		        endFrame: ' . esc_js( $frames_count ) . ',
		        currentFrame: 1,
		        imgList: ".threed-view-images",
		        progress: ".spinner",
		        imgArray: ' . "[".$images_js_string."]" . ',
		        height: ' . esc_js( $height ) . ', 	
		        width: ' . esc_js( $width ) . ',
		        responsive: true,
		        navigation: true
		    });
		});', 'after' );

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
