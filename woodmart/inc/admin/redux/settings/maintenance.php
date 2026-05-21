<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Maintenance', 'woodmart'),
	'id' => 'maintenance',
	'icon' => 'el-icon-wrench',
	'fields' => array (
		array (
			'id'       => 'maintenance_mode',
			'type'     => 'switch',
			'title'    => esc_html__('Enable maintenance mode', 'woodmart'),
			'subtitle' => esc_html__('This will block non-logged users access to the site.', 'woodmart'),
			'description' => esc_html__('If enabled you need to create maintenance page in Dashboard - Pages - Add new. Choose "Template" to be "Maintenance" in "Page attributes". Or you can import the page from our demo in Dashboard - Woodmart - Base import', 'woodmart'),
			'default' => false
		),
	)
));
