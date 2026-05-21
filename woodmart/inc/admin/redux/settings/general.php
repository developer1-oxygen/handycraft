<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('General', 'woodmart'), 
	'id' => 'general',
	'icon' => 'el-icon-home',
	'fields' => array (
		array (
			'id' => 'favicon',
			'type' => 'media',
			'desc' => 'Upload image: png, ico',
			'operator' => 'and',
			'title' => 'Favicon image',
		),
		array (
			'id' => 'favicon_retina',
			'type' => 'media',
			'desc' => 'Upload image: png, ico',
			'operator' => 'and',
			'title' => 'Favicon retina image',
		),
		array (
			'id'       => 'page_comments',
			'type'     => 'switch',
			'title'    => esc_html__('Show comments on page', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'google_map_api_key',
			'type'     => 'text',
			'title'    => esc_html__('Google map API key', 'woodmart'),
			'subtitle' => wp_kses( __('Obtain API key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a> to use our Google Map VC element.', 'woodmart'), array( 'a' => array( 'href' => true, 'target' => true, ), 'br' => array(), 'strong' => array() ) ),
			'tags'     => 'google api key'
		),
		array (
			'id' => 'custom_404_page',
			'type' => 'select',
			'title' => esc_html__( 'Custom 404 page', 'woodmart' ),
			'subtitle' => esc_html__( 'You can make your custom 404 page', 'woodmart' ),
			'options' => woodmart_get_pages(),
			'default' => 'default',
		),
		array (
			'id' => 'sticky_notifications',
			'type' => 'switch',
			'title' => esc_html__('Sticky notifications', 'woodmart'),
			'default' => true
		),
	),
) );

Redux::setSection(
	$opt_name,
	array(
		'title'      => esc_html__( 'Mobile bottom navbar', 'woodmart' ),
		'id'         => 'sticky_toolbar_section',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'sticky_toolbar',
				'type'     => 'switch',
				'title'    => esc_html__( 'Enable Sticky navbar', 'woodmart' ),
				'subtitle' => esc_html__( 'Sticky navigation toolbar will be shown at the bottom on mobile devices.', 'woodmart' ),
				'default'  => false,
			),
			array(
				'id'       => 'sticky_toolbar_label',
				'type'     => 'switch',
				'title'    => esc_html__( 'Navbar labels', 'woodmart' ),
				'subtitle' => esc_html__( 'Show/hide labels under icons in the mobile navbar.', 'woodmart' ),
				'default'  => true,
			),
			array(
				'id'       => 'sticky_toolbar_fields',
				'type'     => 'sorter',
				'title'    => esc_html__( 'Select buttons', 'woodmart' ),
				'subtitle' => esc_html__( 'Choose which buttons will be used for sticky navbar.', 'woodmart' ),
				'options'  => woodmart_get_sticky_toolbar_fields(),
			),
			
			array (
				'id'         => 'link_1_title',
				'type'       => 'woodmart_title',
				'wood-title' => esc_html__( 'Custom button [1]', 'woodmart' ),
			),
			array (
				'id'       => 'link_1_url',
				'type'     => 'text',
				'title'    => esc_html__( 'Custom button URL', 'woodmart' ),
			),
			array (
				'id'       => 'link_1_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Custom button text', 'woodmart' ),
			),
			array (
				'id'       => 'link_1_icon',
				'type'     => 'media',
				'title'    => esc_html__( 'Custom button icon', 'woodmart' ),
			),

			array (
				'id'         => 'link_2_title',
				'type'       => 'woodmart_title',
				'wood-title' => esc_html__( 'Custom button [2]', 'woodmart' ),
			),
			array (
				'id'       => 'link_2_url',
				'type'     => 'text',
				'title'    => esc_html__( 'Custom button URL', 'woodmart' ),
			),
			array (
				'id'       => 'link_2_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Custom button text', 'woodmart' ),
			),
			array (
				'id'       => 'link_2_icon',
				'type'     => 'media',
				'title'    => esc_html__( 'Custom button icon', 'woodmart' ),
			),

			array (
				'id'         => 'link_3_title',
				'type'       => 'woodmart_title',
				'wood-title' => esc_html__( 'Custom button [3]', 'woodmart' ),
			),
			array (
				'id'       => 'link_3_url',
				'type'     => 'text',
				'title'    => esc_html__( 'Custom button URL', 'woodmart' ),
			),
			array (
				'id'       => 'link_3_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Custom button text', 'woodmart' ),
			),
			array (
				'id'       => 'link_3_icon',
				'type'     => 'media',
				'title'    => esc_html__( 'Custom button icon', 'woodmart' ),
			),
		),
	)
);

