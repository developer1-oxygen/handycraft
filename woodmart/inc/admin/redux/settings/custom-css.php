<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Custom CSS', 'woodmart'),
	'id' => 'custom_css',
	'icon' => 'el-icon-css',
	'fields' => array (
		array (
			'id' => 'custom_css',
			'type' => 'ace_editor',
			'mode' => 'css',
			'title' => esc_html__('Global Custom CSS', 'woodmart'),
		),
		array (
			'id' => 'css_desktop',
			'type' => 'ace_editor',
			'mode' => 'css',
			'title' => esc_html__('Custom CSS for desktop', 'woodmart'),
		),
		array (
			'id' => 'css_tablet',
			'type' => 'ace_editor',
			'mode' => 'css',
			'title' => esc_html__('Custom CSS for tablet', 'woodmart'),
		),
		array (
			'id' => 'css_wide_mobile',
			'type' => 'ace_editor',
			'mode' => 'css',
			'title' => esc_html__('Custom CSS for mobile landscape', 'woodmart'),
		),
		array (
			'id' => 'css_mobile',
			'type' => 'ace_editor',
			'mode' => 'css',
			'title' => esc_html__('Custom CSS for mobile', 'woodmart'),
		),
	),
) );