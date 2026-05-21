<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
 * ------------------------------------------------------------------------------------------------
 * Init Theme Settings and Options with Redux plugin
 * ------------------------------------------------------------------------------------------------
 */

	if ( ! class_exists( 'Redux' ) ) {
		return;
	}

	$opt_name = 'woodmart_options';
	
	if ( apply_filters( 'woodmart_dynamic_css', true ) && get_option( 'woodmart-dynamic-css-file' ) || ( isset( $_GET['page'] ) && $_GET['page'] == '_options' ) ) {
		$output = 'compiler';
	} else {
		$output = 'output';
	}
	
	$woodmart_selectors = woodmart_get_config( 'selectors' );

	$args = array(
		'opt_name'             => $opt_name,
		'display_name'         => woodmart_get_theme_info( 'Name' ),
		'display_version'      => woodmart_get_theme_info( 'Version' ),
		'menu_type'            => 'hidden',
		'allow_sub_menu'       => true,
		'menu_title'           => esc_html__( 'Theme Settings', 'woodmart' ),
		'page_title'           => esc_html__( 'Theme Settings', 'woodmart' ),
		'google_api_key'       => '',
		'google_update_weekly' => false,
		'async_typography'     => false,
		'admin_bar'            => true,
		'admin_bar_icon'       => 'dashicons-portfolio',
		'admin_bar_priority'   => 50,
		'global_variable'      => '',
		'dev_mode'             => false,
		'update_notice'        => true,
		'customizer'           => true,
		'page_priority'        => 61,
		'page_parent'          => 'themes.php',
		'page_permissions'     => 'manage_options',
		'menu_icon'            => WOODMART_ASSETS . '/images/theme-admin-icon.svg', 
		'last_tab'             => '',
		'page_icon'            => 'icon-themes',
		'page_slug'            => '_options',
		'save_defaults'        => true,
		'default_show'         => false,
		'default_mark'         => '',
		'show_import_export'   => true,
		'transient_time'       => 60 * MINUTE_IN_SECONDS,
		'output'               => true,
		'output_tag'           => true,
		'show_options_object'  => false,
		'footer_credit'        =>  '1.0',                  
		'database'             => '',
		'system_info'          => false,
		'ajax_save'            => apply_filters( 'woodmart_dynamic_css', true ) ? false : true,
		'hints'                => array(
			'icon'          => 'el el-question-sign',
			'icon_position' => 'right',
			'icon_color'    => 'lightgray',
			'icon_size'     => 'normal',
			'tip_style'     => array(
				'color'   => 'light',
				'shadow'  => true,
				'rounded' => false,
				'style'   => '',
			),
			'tip_position'  => array(
				'my' => 'top left',
				'at' => 'bottom right',
			),
			'tip_effect'    => array(
				'show' => array(
					'effect'   => 'slide',
					'duration' => '500',
					'event'    => 'mouseover',
				),
				'hide' => array(
					'effect'   => 'slide',
					'duration' => '500',
					'event'    => 'click mouseleave',
				),
			),
		)
	);

	Redux::setArgs( $opt_name, $args );

	$options = array(
		'general',
		'general-layout',
		'page-title',
		'footer',
		'typography',
		'styles-colors',
		'blog',
		'portfolio',
		'shop',
		'product-page',
		'login-register',
		'custom-css',
		'custom-js',
		'social',
		'perfomance',
		'other',
		'maintenance',
	);

	foreach ( $options as $file ) {
		$path = get_parent_theme_file_path( WOODMART_FRAMEWORK . '/admin/redux/settings/' . $file . '.php' );
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
	
	// Load extensions
	Redux::setExtensions( $opt_name, get_parent_theme_file_path( WOODMART_FRAMEWORK . '/admin/redux/fields/custom_typography/' ) );

	function woodmart_removeDemoModeLink() { // Be sure to rename this function to something more unique
		if ( class_exists('ReduxFrameworkPlugin') ) {
			remove_action( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
		}
		if ( class_exists('ReduxFrameworkPlugin') ) {
			remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );
		}
	}
	add_action('init', 'woodmart_removeDemoModeLink', 1520);
