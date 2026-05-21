<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Page title', 'woodmart'), 
	'id' => 'page_titles',
	'icon' => 'el-icon-check',
	'fields' => array (
		array (
			'id'       => 'page-title-design',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Page title design', 'woodmart' ),
			'subtitle' => esc_html__( 'Select page title section design or disable it completely on all pages.', 'woodmart' ),
			'options'  => array(
				'default' => array(
					'title' => esc_html__( 'Default', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/page-heading/default.jpg'
				),
				'centered' => array(
					'title' => esc_html__( 'Centered', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/page-heading/centered.jpg'
				),
				'disable' => array(
					'title' => esc_html__( 'Disable', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/page-heading/disable.jpg'
				),
			),
			'default' => 'centered',
			'tags'    => 'page heading title design',
			'class'   => 'without-border'
		),
		array (
			'id' => 'color-page-title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Page title color and background', 'woodmart' ),
		),
		array (
			'id'       => 'title-background',
			'type'     => 'background',
			'title'    => esc_html__('Pages title background', 'woodmart'),
			'subtitle' => esc_html__('Set background image or color, that will be used as a default for all page titles, shop page and blog.', 'woodmart'),
			'desc'     => esc_html__('You can also specify other image for particular page', 'woodmart'),
			$output   => array('.page-title-default'),
			'default'  => array(
				'background-color' => '#0a0a0a',
				'background-position' => 'center center',
				'background-size' => 'cover'
			),
			'tags'     => 'page title color page title background'
		),
		array (
			'id'       => 'page-title-color',
			'type'     => 'button_set',
			'title'    => esc_html__('Text color for page title', 'woodmart'),
			'subtitle' => esc_html__('You can set different colors depending on it\'s background. May be light or dark', 'woodmart'),
			'options'  => array(
				'default' => esc_html__('Default',  'woodmart'), 
				'light' => esc_html__('Light', 'woodmart'),  
				'dark' => esc_html__('Dark', 'woodmart'), 
			),
			'default' => 'light',
			'class'   => 'without-border'
		),

		array (
			'id' => 'options-page-title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Page title options', 'woodmart' ),
		),
		array (
			'id'       => 'page-title-size',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Page title size', 'woodmart' ),
			'subtitle' => esc_html__( 'You can set different sizes for your pages titles', 'woodmart' ),
			'options'  => array(
				'default' => esc_html__( 'Default',  'woodmart' ), 
				'small' => esc_html__( 'Small',  'woodmart' ), 
				'large' => esc_html__( 'Large', 'woodmart' ), 
			),
			'default' => 'default',
			'tags'     => 'page heading title size breadcrumbs size',
			'class'   => 'without-border'
		),
		array (
			'id' => 'options-breadcrumbs',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Breadcrumbs options', 'woodmart' ),
		),
		array (
			'id'       => 'breadcrumbs',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show breadcrumbs', 'woodmart' ),
			'subtitle' => esc_html__( 'Displays a full chain of links to the current page.', 'woodmart' ),
			'default' => true,
		),
		array (
			'id'       => 'yoast_shop_breadcrumbs',
			'type'     => 'switch',
			'title'    => esc_html__( 'Yoast breadcrumbs for shop', 'woodmart' ),
			'subtitle' => esc_html__( 'Requires Yoast SEO plugin to be installed. Replaces standard WooCommerce breadcrumbs with custom that come with the plugin.', 'woodmart' ),
			'description' => esc_html__( 'You need to enable and configure it in Dashboard -> SEO -> Search Appearance -> Breadcrumbs.', 'woodmart' ),
			'default' => false
		),
		array (
			'id'       => 'yoast_pages_breadcrumbs',
			'type'     => 'switch',
			'title'    => esc_html__( 'Yoast breadcrumbs for pages', 'woodmart' ),
			'subtitle' => esc_html__( 'Requires Yoast SEO plugin to be installed. Replaces our theme\'s breadcrumbs for pages and blog with custom that come with the plugin.', 'woodmart' ),
			'description' => esc_html__( 'You need to enable and configure it in Dashboard -> SEO -> Search Appearance -> Breadcrumbs.', 'woodmart' ),
			'default' => false
		),
	),
) );
