<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Custom JS', 'woodmart'),
	'id' => 'custom_js',
	'icon' => 'el-icon-magic',
	'fields' => array (
		array (
			'id' => 'custom_js',
			'type' => 'ace_editor',
			'mode' => 'javascript',
			'title' => esc_html__('Global Custom JS', 'woodmart'),
		),
		array (
			'id' => 'js_ready',
			'type' => 'ace_editor',
			'mode' => 'javascript',
			'title' => esc_html__('On document ready', 'woodmart'),
			'desc' => esc_html__('Will be executed on $(document).ready()', 'woodmart')
		),
	),
) );