<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('General Layout', 'woodmart'), 
	'id' => 'layout',
	'icon' => 'el-icon-website',
	'fields' => array (
		array (
			'id'       => 'site_width',
			'type'     => 'select',
			'title'    => esc_html__('Site width', 'woodmart'),
			'subtitle' => esc_html__('You can make your content wrapper boxed or full width', 'woodmart'),
			'options'  => array(
				'full-width' => esc_html__('Full width', 'woodmart'), 
				'boxed' => esc_html__('Boxed (with hidden overflow)', 'woodmart'), 
				'boxed-2' => esc_html__('Boxed', 'woodmart'), 
				'full-width-content' => esc_html__('Content full width', 'woodmart'), 
				'wide' => esc_html__('Wide (1600 px)', 'woodmart'), 
				'custom' => esc_html__('Custom', 'woodmart'), 
			),
			'default' => 'full-width',
			'tags'     => 'boxed full width wide'
		),
		array(
			'id'        => 'site_custom_width',
			'type'      => 'slider',
			'title'     => esc_html__( 'Custom width in pixels', 'woodmart' ),
			'desc'      => esc_html__( 'Specify your custom website container width in pixels.', 'woodmart' ),
			'default'   => 1222,
			'min'       => 1025,
			'step'      => 1,
			'max'       => 1920,
			'display_value' => 'label',
			'required' => array(
				array( 'site_width','equals', array( 'custom' ) ),
			)
		),
		array (
			'id'       => 'main_layout',
			'type'     => 'image_select',
			'title'    => esc_html__('Sidebar position', 'woodmart'), 
			'subtitle' => esc_html__('Select main content and sidebar alignment.', 'woodmart'),
			'options'  => array(
				'full-width'      => array(
					'alt'   => 'Without', 
					'title' => esc_html__( 'Without', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/none.png'
				),
				'sidebar-left'      => array(
					'alt'   => 'Left', 
					'title' => esc_html__( 'Left', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/left.png'
				),
				'sidebar-right'      => array(
					'alt'   => 'Right',
					'title' => esc_html__( 'Right', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/right.png'
				),
			),
			'default' => 'sidebar-right',
			'tags'     => 'sidebar left sidebar right'
		),
		array (
			'id'       => 'hide_main_sidebar_mobile',
			'type'     => 'switch',
			'title'    => esc_html__( 'Off canvas sidebar for mobile', 'woodmart' ),
			'subtitle' => esc_html__( 'You can can hide sidebar and show nicely on button click on the page.', 'woodmart' ),
			'default' => true,
		),
		array (
			'id'       => 'sidebar_width',
			'type'     => 'button_set',
			'title'    => esc_html__('Sidebar size', 'woodmart'),
			'subtitle' => esc_html__('You can set different sizes for your pages sidebar', 'woodmart'),
			'options'  => array(
				2 => esc_html__('Small', 'woodmart'), 
				3 => esc_html__('Medium', 'woodmart'),
				4 => esc_html__('Large', 'woodmart'),
			),
			'default' => 3,
			'tags'     => 'small sidebar large sidebar'
		),
	),
) );
