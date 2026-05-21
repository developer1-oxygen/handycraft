<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Other', 'woodmart'),
	'id' => 'other',
	'icon' => 'el-icon-cog',
	'fields' => array (
		array (
			'id'       => 'dummy_import',
			'type'     => 'switch',
			'title'    => esc_html__( 'Enable Dummy Content import function', 'woodmart' ),
			'default' => true
		),
		array(
			'id'       => 'woodmart_slider',
			'type'     => 'switch',
			'title'    => esc_html__('Enable custom slider', 'woodmart'),
			'description' => esc_html__('If you enable this option, a new post type for sliders will be added to your Dashboard menu. You will be able to create sliders with WPBakery Page Builder and place them on any page on your website.', 'woodmart'),
			'default' => true
		),
		array(
			'id'       => 'allow_upload_svg',
			'type'     => 'switch',
			'title'    => esc_html__('Allow SVG upload', 'woodmart'),
			'description' => wp_kses( __('Allow SVG uploads as well as SVG format for custom fonts. We suggest you to use <a href="https://ru.wordpress.org/plugins/safe-svg/">this plugin</a> to be sure that all uploaded content is safe. If you will install this plugin, you can disable this option.', 'woodmart'), array( 'a' => array( 'href' => true, 'target' => true, ), 'br' => array(), 'strong' => array() ) ),
			'default' => true
		),
	),
) );
