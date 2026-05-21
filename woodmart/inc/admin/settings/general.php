<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}
use XTS\Options;

/**
 * General
 */
Options::add_section(
	array(
		'id'       => 'general_section',
		'name'     => esc_html__( 'General', 'woodmart' ),
		'priority' => 10,
		'icon'     => 'dashicons dashicons-admin-home',
	)
);

/**
 * General
 */

Options::add_field(
	array(
		'id'          => 'favicon',
		'name'        => esc_html__( 'Favicon image', 'woodmart' ),
		'description' => esc_html__( 'Upload image: png, ico', 'woodmart' ),
		'type'        => 'upload',
		'section'     => 'general_section',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'favicon_retina',
		'name'        => esc_html__( 'Favicon retina image', 'woodmart' ),
		'description' => esc_html__( 'Upload image: png, ico', 'woodmart' ),
		'type'        => 'upload',
		'section'     => 'general_section',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'       => 'page_comments',
		'name'     => esc_html__( 'Show comments on page', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'general_section',
		'default'  => '1',
		'priority' => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'google_map_api_key',
		'name'        => esc_html__( 'Google map API key', 'woodmart' ),
		'type'        => 'text_input',
		'description' => wp_kses(
			__( 'Obtain API key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a> to use our Google Map VC element.', 'woodmart' ),
			'default'
		),
		'section'     => 'general_section',
		'tags'        => 'google api key',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'           => 'custom_404_page',
		'name'         => esc_html__( 'Custom 404 page', 'woodmart' ),
		'type'         => 'select',
		'description'  => esc_html__( 'You can make your custom 404 page', 'woodmart' ),
		'section'      => 'general_section',
		'options'      => woodmart_get_pages( true ),
		'empty_option' => true,
		'priority'     => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'sticky_notifications',
		'name'     => esc_html__( 'Sticky notifications', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'general_section',
		'default'  => '1',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'insta_token',
		'name'        => esc_html__( 'Instagram generate token', 'woodmart' ),
		'group'       => esc_html__( 'Instagram API', 'woodmart' ),
		'description' => 'Configure your "Valid redirect URIs" in your Instagram application settings with the following URL  <strong>' . admin_url( 'admin.php?page=xtemos_options&tab=general_section' ) . '</strong><br> To get this data, follow the instructions in our documentation <a href="https://xtemos.com/docs/woodmart/faq-guides/setup-instagram-api/" target="_blank">here</a>.',
		'type'        => 'instagram_api',
		'section'     => 'general_section',
		'priority'    => 60,
	)
);

/**
 * Mobile bottom navbar.
 */
Options::add_section(
	array(
		'id'       => 'general_navbar_section',
		'parent'   => 'general_section',
		'name'     => esc_html__( 'Mobile bottom navbar', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'sticky_toolbar',
		'name'        => esc_html__( 'Enable Sticky navbar', 'woodmart' ),
		'description' => esc_html__( 'Sticky navigation toolbar will be shown at the bottom on mobile devices.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'general_navbar_section',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'sticky_toolbar_label',
		'name'        => esc_html__( 'Navbar labels', 'woodmart' ),
		'description' => esc_html__( 'Show/hide labels under icons in the mobile navbar.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'general_navbar_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'sticky_toolbar_fields',
		'name'        => esc_html__( 'Select buttons', 'woodmart' ),
		'description' => esc_html__( 'Choose which buttons will be used for sticky navbar.', 'woodmart' ),
		'type'        => 'select',
		'multiple'    => true,
		'select2'     => true,
		'section'     => 'general_navbar_section',
		'options'     => woodmart_get_sticky_toolbar_fields( true ),
		'default'     => array(
			'shop',
			'sidebar',
			'wishlist',
			'cart',
			'account',
		),
		'priority'    => 30,
	)
);


Options::add_field(
	array(
		'id'       => 'link_1_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [1]', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'link_1_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [1]', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'priority' => 41,
	)
);

Options::add_field(
	array(
		'id'       => 'link_1_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [1]', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'link_2_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [2]', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'priority' => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'link_2_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [2]', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'priority' => 61,
	)
);

Options::add_field(
	array(
		'id'       => 'link_2_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [2]', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'       => 'link_3_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [3]', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'priority' => 80,
	)
);

Options::add_field(
	array(
		'id'       => 'link_3_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [3]', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'priority' => 81,
	)
);


Options::add_field(
	array(
		'id'       => 'link_3_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'group'    => esc_html__( 'Custom button [3]', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'priority' => 90,
	)
);
