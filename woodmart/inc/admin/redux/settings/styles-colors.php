<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Styles and colors', 'woodmart'),
	'id' => 'colors',
	'icon' => 'el-icon-brush'
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Styles and colors', 'woodmart'),
	'id' => 'colors',
	'icon' => 'el-icon-brush',
	'fields' => array (
		array(
			'id'       => 'primary-color',
			'type'     => 'color',
			'title'    => esc_html__('Primary Color', 'woodmart'), 
			'subtitle' => esc_html__('Pick a background color for the theme buttons and other colored elements.', 'woodmart'),
			'validate' => 'color',
			$output   => $woodmart_selectors['primary-color'],
			'default'  => '#83b735'
		),
		array(
			'id'       => 'secondary-color',
			'type'     => 'color',
			'title'    => esc_html__( 'Secondary Color', 'woodmart' ), 
			'subtitle' => esc_html__( 'Color for secondary elements on the website.', 'woodmart' ),
			'validate' => 'color',
			$output   => $woodmart_selectors['secondary-color'],
			'class'    => 'without-border',
			'default'  => '#fbbc34'
		),
		array(
			'id'          => 'android_browser_bar_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Android browser bar color', 'woodmart' ), 
			'subtitle' => wp_kses( __( 'Define color for the browser top bar on Android devices. <a href="https://developers.google.com/web/fundamentals/design-and-ux/browser-customization/#color_browser_elements">[Read more]</a>', 'woodmart' ), 'default' ),
			'validate'    => 'color',
			'default'     => '',
		),
		array (
			'id' => 'link_color_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Website links color', 'woodmart' ),
		),
		array(
			'id'       => 'link-color',
			'type'     => 'link_color',
			'title'    => esc_html__( 'Links color', 'woodmart' ), 
			'subtitle' => esc_html__( 'Set the color for links on your pages, posts and products content.', 'woodmart' ),
			'validate' => 'color',
			$output   => $woodmart_selectors['link-color'],
			'active'   => false,
			'class'    => 'without-border'
		),
		array (
			'id' => 'dark_version_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Website dark version', 'woodmart' ),
		),
		array(
			'id'       => 'dark_version',
			'type'     => 'switch',
			'title'    => esc_html__('Dark version', 'woodmart'), 
			'subtitle' => esc_html__('Turn your website color to dark version', 'woodmart'),
			'default' => false
		),
		array(
			'id'   => 'buttons_info',
			'type' => 'info',
			'style' => 'info',
			'desc' => esc_html__('Settings for all buttons used in the template.', 'woodmart')
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Pages background', 'woodmart'),
	'subsection' => true,
	'id' => 'colors-bgs',
	'fields' => array (
		array (
			'id'       => 'body-background',
			'type'     => 'background',
			'title'    => esc_html__('Site background', 'woodmart'),
			'subtitle' => esc_html__('Set background image or color for body. Only for BOXED layout', 'woodmart'),
			$output   => array('body'),
		),
		array (
			'id'       => 'pages-background',
			'type'     => 'background',
			'title'    => esc_html__('Wrapper background for ALL pages', 'woodmart'),
			$output   => array('.page .main-page-wrapper')
		),
		array (
			'id'       => 'shop-background',
			'type'     => 'background',
			'title'    => esc_html__('Background for SHOP pages', 'woodmart'),
			$output   => array('.woodmart-archive-shop .main-page-wrapper'),
		),
		array (
			'id'       => 'product-background',
			'type'     => 'background',
			'title'    => esc_html__('Single product background', 'woodmart'),
			'subtitle' => esc_html__('Set background for your products page. You can also specify different background for particular products while editing it.', 'woodmart'),
			$output   => array('.single-product .main-page-wrapper')
		),
		array (
			'id'       => 'blog-background',
			'type'     => 'background',
			'title'    => esc_html__('Background for BLOG', 'woodmart'),
			$output   => array('.woodmart-archive-blog .main-page-wrapper')
		),
		array (
			'id'       => 'blog-post-background',
			'type'     => 'background',
			'title'    => esc_html__('Background for BLOG single post', 'woodmart'),
			$output   => array('.single-post .main-page-wrapper')
		),
		array (
			'id'       => 'portfolio-background',
			'type'     => 'background',
			'title'    => esc_html__('Background for PORTFOLIO', 'woodmart'),
			$output   => array('.woodmart-archive-portfolio .main-page-wrapper')
		),
		array (
			'id'       => 'portfolio-project-background',
			'type'     => 'background',
			'title'    => esc_html__('Background for PORTFOLIO project', 'woodmart'),
			$output   => array('.single-portfolio .main-page-wrapper')
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Buttons', 'woodmart'),
	'subsection' => true,
	'id' => 'colors-btns',
	'fields' => array (
		array (
			'id'   => 'buttons_info',
			'type' => 'info',
			'style' => 'success',
			'desc' => esc_html__('There are three categories of buttons presented in the theme: default, WooCommerce buttons and accent buttons. You can choose different styles for all of them separately.', 'woodmart')
		),
		array (
			'id'       => 'btns_default_style',
			'type'     => 'image_select',
			'title'    => esc_html__('Default buttons styles', 'woodmart'),
			'subtitle' => esc_html__('Almost all standard buttons through the site', 'woodmart'),
			'options'  => array(
				'flat' => array(
					'title' => esc_html__( 'Flat', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/flat.jpg'
				),
				'3d' => array(
					'title' => esc_html__( '3D', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/3d.jpg'
				),
				'rounded' => array(
					'title' => esc_html__( 'Circle', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/circle.jpg'
				),
				'semi-rounded' => array(
					'title' => esc_html__( 'Rounded', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/semi-rounded.jpg'
				),
			),
			'default' => 'flat'
		),
		array (
			'id'       => 'btns_shop_style',
			'type'     => 'image_select',
			'title'    => esc_html__('WooCommerce buttons styles', 'woodmart'),
			'subtitle' => esc_html__('Shopping buttons like "Add to cart", "Checkout", "Login", "Register" etc.', 'woodmart'),
			'options'  => array(
				'flat' => array(
					'title' => esc_html__( 'Flat', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/flat.jpg'
				),
				'3d' => array(
					'title' => esc_html__( '3D', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/3d.jpg'
				),
				'rounded' => array(
					'title' => esc_html__( 'Circle', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/circle.jpg'
				),
				'semi-rounded' => array(
					'title' => esc_html__( 'Rounded', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/semi-rounded.jpg'
				),
			),
			'default' => '3d'
		),
		array (
			'id'       => 'btns_accent_style',
			'type'     => 'image_select',
			'title'    => esc_html__('Accent buttons styles', 'woodmart'),
			'subtitle' => esc_html__('"Call to action" buttons', 'woodmart'),
			'options'  => array(
				'flat' => array(
					'title' => esc_html__( 'Flat', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/flat.jpg'
				),
				'3d' => array(
					'title' => esc_html__( '3D', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/3d.jpg'
				),
				'rounded' => array(
					'title' => esc_html__( 'Circle', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/circle.jpg'
				),
				'semi-rounded' => array(
					'title' => esc_html__( 'Rounded', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/buttons/semi-rounded.jpg'
				),
			),
			'default' => 'flat',
			'class'   => 'without-border'
		),
		array (
			'id' => 'default_buttons_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Default buttons color ', 'woodmart' ),
			'wood-desc' => esc_html__( 'Set background and color schemes for default buttons in idle and hover states.', 'woodmart' )
		),
		array (
			'id'       => 'btns_default_bg',
			'type'     => 'color',
			'title'    => esc_html__('[Default] Background for buttons', 'woodmart'),
			$output   => array(
				'background-color' => current($woodmart_selectors['btns-default'])
			)
		),
		array (
			'id'       => 'btns_default_color_scheme',
			'type'     => 'select',
			'title'    => esc_html__('[Default] Text color scheme', 'woodmart'),
			'subtitle' => esc_html__('You can change colors of links for them', 'woodmart'),
			'options'  => array(
				'dark' => 'Dark',
				'light' => 'Light',
			),
			'default' => 'dark'
		),
		array (
			'id'       => 'btns_default_bg_hover',
			'type'     => 'color',
			'title'    => esc_html__('[Default hover] Background', 'woodmart'),
			$output   => array(
				'background-color' => woodmart_append_hover_state( $woodmart_selectors['btns-default'], false )
			),
			'tags' => 'buttons background button color buttons color'
		),
		array (
			'id'       => 'btns_default_color_scheme_hover',
			'type'     => 'select',
			'title'    => esc_html__('[Default hover] Text color scheme', 'woodmart'),
			'subtitle' => esc_html__('You can change colors of links for them', 'woodmart'),
			'options'  => array(
				'dark' => 'Dark',
				'light' => 'Light',
			),
			'default' => 'dark',
			'class'   => 'without-border'
		),
		array (
			'id' => 'shop_buttons_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Shop buttons color ', 'woodmart' ),
			'wood-desc' => esc_html__( 'Set background and color schemes for shop buttons in idle and hover states.', 'woodmart' )
		),
		array (
			'id'       => 'btns_shop_bg',
			'type'     => 'color',
			'title'    => esc_html__('[Shop] Background for buttons', 'woodmart'),
			$output   => array(
				'background-color' => current($woodmart_selectors['btns-shop'])
			),
			'default' => '#83b735'
		),
		array (
			'id'       => 'btns_shop_color_scheme',
			'type'     => 'select',
			'title'    => esc_html__('[Shop] Text color scheme', 'woodmart'),
			'subtitle' => esc_html__('You can change colors of links for them', 'woodmart'),
			'options'  => array(
				'dark' => 'Dark',
				'light' => 'Light',
			),
			'default' => 'light'
		),
		array (
			'id'       => 'btns_shop_bg_hover',
			'type'     => 'color',
			'title'    => esc_html__('[Shop hover] Background on hover', 'woodmart'),
			$output   => array(
				'background-color' => woodmart_append_hover_state( $woodmart_selectors['btns-shop'], false )
			),
			'default' => '#74a32f'
		),
		array (
			'id'       => 'btns_shop_color_scheme_hover',
			'type'     => 'select',
			'title'    => esc_html__('[Shop hover] Text color scheme on hover', 'woodmart'),
			'subtitle' => esc_html__('You can change colors of links for them', 'woodmart'),
			'options'  => array(
				'dark' => 'Dark',
				'light' => 'Light',
			),
			'default' => 'light',
			'class'   => 'without-border'
		),
		array (
			'id' => 'accent_buttons_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Accent buttons color ', 'woodmart' ),
			'wood-desc' => esc_html__( 'Set background and color schemes for accent buttons in idle and hover states.', 'woodmart' )
		),
		array (
			'id'       => 'btns_accent_bg',
			'type'     => 'color',
			'title'    => esc_html__('[Accent] Background for buttons', 'woodmart'),
			$output   => array(
				'background-color' => current($woodmart_selectors['btns-accent'])
			),
			'default' => '#83b735'
		),
		array (
			'id'       => 'btns_accent_color_scheme',
			'type'     => 'select',
			'title'    => esc_html__('[Accent] Text color scheme', 'woodmart'),
			'subtitle' => esc_html__('You can change colors of links for them', 'woodmart'),
			'options'  => array(
				'dark' => 'Dark',
				'light' => 'Light',
			),
			'default' => 'light'
		),
		array (
			'id'       => 'btns_accent_bg_hover',
			'type'     => 'color',
			'title'    => esc_html__('[Accent hover] Background on hover', 'woodmart'),
			$output   => array(
				'background-color' => woodmart_append_hover_state( $woodmart_selectors['btns-accent'], false )
			),
			'default' => '#74a32f'
		),
		array (
			'id'       => 'btns_accent_color_scheme_hover',
			'type'     => 'select',
			'title'    => esc_html__('[Accent hover] Text color scheme on hover', 'woodmart'),
			'subtitle' => esc_html__('You can change colors of links for them', 'woodmart'),
			'options'  => array(
				'dark' => 'Dark',
				'light' => 'Light',
			),
			'default' => 'light'
		),

	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Forms style', 'woodmart'),
	'subsection' => true,
	'id' => 'form_style',
	'fields' => array (
		array(
			'id'       => 'form_fields_style',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Form fields style', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose your form style', 'woodmart' ),
			'options'  => array(
				'rounded' => array(
					'title' => esc_html__( 'Circle', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/form-style/circle.jpg'
				),
				'semi-rounded' => array(
					'title' => esc_html__( 'Round', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/form-style/semi-rounded.jpg'
				),
				'square' => array(
					'title' => esc_html__( 'Square', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/form-style/square.jpg'
				),
				'underlined' => array(
					'title' => esc_html__( 'Underline', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/form-style/underlined.jpg'
				),
			),
			'default' => 'square'
		),
		array(
			'id'       => 'form_border_width',
			'type'     => 'button_set',
			'title'    => esc_html__('Form border width', 'woodmart'),
			'subtitle' => esc_html__('Choose your form border width', 'woodmart'),
			'options'  => array(
				1 => '1',
				2 => '2',
			),
			'default' => 2
		),
	),
) );
