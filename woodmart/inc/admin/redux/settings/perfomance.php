<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Performance', 'woodmart'),
	'id' => 'performance',
	'icon' => 'el-icon-graph',
	'fields' => array (
		array (
			'id'       => 'minified_css',
			'type'     => 'switch',
			'title'    => esc_html__('Include minified CSS', 'woodmart'),
			'subtitle' => esc_html__('Minified version of style.css file will be loaded (style.min.css)', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'minified_js',
			'type'     => 'switch',
			'title'    => esc_html__('Include minified JS', 'woodmart'),
			'subtitle' => esc_html__('Minified version of functions.js file will be loaded', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'combined_js',
			'type'     => 'switch',
			'title'    => esc_html__('Combine JS files', 'woodmart'),
			'subtitle' => esc_html__('Combine all third party libraries and theme functions into one JS file (theme.min.js)', 'woodmart'),
			'default' => false,
		),
		array (
			'id' => 'disable_nanoscroller',
			'type' => 'button_set',
			'title' => esc_html__( 'Nanoscroller library', 'woodmart' ),
			'subtitle' => esc_html__( 'This library adds nice style to elements with scroll bar like cart widget, filters widget, AJAX search results etc. In modern browsers we can style them without this JS libary so you can disable it.', 'woodmart' ),
			'options' => array(
				'enable' => esc_html__( 'Enable', 'woodmart' ), 
				'disable' => esc_html__( 'Disable', 'woodmart' ),
				'webkit' => esc_html__( 'Enable for old browsers', 'woodmart' ),
			),
			'default' => 'enable',
		),
		array (
			'id'       => 'disable_owl_mobile_devices',
			'type'     => 'switch',
			'title'    => esc_html__('Disable OWL Carousel script on mobile devices', 'woodmart'),
			'subtitle' => esc_html__('Using native browser\'s scrolling feature on mobile devices may improve your page loading and performance on some devices. Desktop will be handled with OWL Carousel JS library.', 'woodmart'),
			'default' => false,
			'class'   => 'without-border'
		),
		array (
			'id' => 'divider_lazy',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Lazy loading options', 'woodmart' ),
		),
		array (
			'id'       => 'lazy_loading',
			'type'     => 'switch',
			'title'    => esc_html__('Lazy loading for images', 'woodmart'),
			'subtitle' => esc_html__('Enable this option to optimize your images loading on the website. They will be loaded only when user will scroll the page.', 'woodmart'),
			'default' => false
		),
		array (
			'id'       => 'lazy_effect',
			'type'     => 'button_set',
			'title'    => esc_html__('Appearance effect', 'woodmart'),
			'subtitle' => esc_html__('When enabled, your images will be replaced with their blurred small previews. And when the visitor will scroll the page to that image, it will be replaced with an original image.', 'woodmart'),
			'default' => 'fade',
			'options'  => array(
				'fade' => esc_html__('Fade', 'woodmart'), 
				'blur' => esc_html__('Blur', 'woodmart'),
				'none' => esc_html__('None', 'woodmart'),
			),
		),
		array (
			'id'       => 'lazy_generate_previews',
			'type'     => 'switch',
			'title'    => esc_html__('Generate previews', 'woodmart'),
			'subtitle' => esc_html__('Create placeholders previews as miniatures from the original images.', 'woodmart'),
			'default' => true,
		),
		array (
			'id'       => 'lazy_base_64',
			'type'     => 'switch',
			'title'    => esc_html__('Base 64 encode for placeholders', 'woodmart'),
			'subtitle' => esc_html__('This option allows you to decrease a number of HTTP requests replacing images with base 64 encoded sources.', 'woodmart'),
			'default' => true,
		),
		array (
			'id'       => 'lazy_proprtion_size',
			'type'     => 'switch',
			'title'    => esc_html__('Proportional placeholders size', 'woodmart'),
			'subtitle' => esc_html__('Will generate proportional image size for the placeholder based on original image size.', 'woodmart'),
			'default' => true,
		),
		array(
			'id'        => 'lazy_loading_offset',
			'type'      => 'slider',
			'title'     => esc_html__( 'Offset', 'woodmart' ),
			'subtitle'  => esc_html__( 'Start load images X pixels before the page is scrolled to the item', 'woodmart' ),
			'default'   => 0,
			'min'       => 0,
			'step'      => 10,
			'max'       => 1000,
			'display_value' => 'label',
		),
		// array (
		//     'id'       => 'lazy_use_default_placeholder',
		//     'type'     => 'switch',
		//     'title'    => esc_html__('Use default placeholder', 'woodmart'),
		//     'subtitle' => esc_html__('If you enable this option, miniatures will not be generated but the gray squared image placeholder will be used.', 'woodmart'),
		//     'default' => true,
		//     'required' => array(
		//          array('lazy_loading','equals', array(true)),
		//     )
		// ),
		array (
			'id'       => 'lazy_custom_placeholder',
			'type' => 'media',
			'desc' => 'Upload image: png, ico',
			'title'    => esc_html__('Upload custom placeholder image', 'woodmart'),
			'subtitle' => esc_html__('Add your custom image placeholder that will be used before the original image will be loaded.', 'woodmart'),
			'default' => true,
		),
	),
) );
