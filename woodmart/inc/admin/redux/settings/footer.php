<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Footer', 'woodmart'), 
	'id' => 'footer',
	'icon' => 'el-icon-photo',
	'fields' => array(
		array(
			'id'       => 'disable_footer',
			'type'     => 'switch',
			'title'    => esc_html__( 'Footer', 'woodmart' ),
			'subtitle' => esc_html__( 'Enable/disable footer on your website.', 'woodmart' ),
			'default'  => true
		),
		array(
			'id'       => 'footer-layout',
			'type'     => 'image_select',
			'title'    => esc_html__('Footer layout', 'woodmart'),
			'subtitle' => esc_html__('Choose your footer layout. Depending on columns number you will have different number of widget areas for footer in Appearance->Widgets', 'woodmart'),
			'options'  => array(
				1 => array(
					'title' => 'Single Column',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-1.png'
				),
				2 => array(
					'title' => 'Two Columns',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-2.png'
				),
				3 => array(
					'title' => 'Three Columns',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-3.png'
				),
				4 => array(
					'title' => 'Four Columns',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-4.png'
				),
				5 => array(
					'title' => 'Six Columns',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-5.png'
				),
				6 => array(
					'title' => '1/4 + 1/2 + 1/4',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-6.png'
				),
				7 => array(
					'title' => '1/2 + 1/4 + 1/4',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-7.png'
				),
				8 => array(
					'title' => '1/4 + 1/4 + 1/2',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-8.png'
				),
				9 => array(
					'title' => 'Two rows',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-9.png'
				),
				10 => array(
					'title' => 'Two rows',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-10.png'
				),
				11 => array(
					'title' => 'Two rows',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-11.png'
				),
				12 => array(
					'title' => 'Two rows',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-12.png'
				),
				13 => array(
					'title' => 'Five columns',
					'img' => WOODMART_ASSETS_IMAGES . '/settings/footer-13.png'
				),
			),
			'default' => 13
		),
		array(
			'id'       => 'scroll_top_btn',
			'type'     => 'switch',
			'title'    => esc_html__( 'Scroll to top button', 'woodmart' ),
			'subtitle' => esc_html__( 'This button moves you to the top of the page when you click it.', 'woodmart' ),
			'default' => true
		),
		array(
			'id'       => 'sticky_footer',
			'type'     => 'switch',
			'title'    => esc_html__( 'Sticky footer', 'woodmart' ),
			'subtitle' => esc_html__( 'The footer will be displayed behind the content of the page and will be visible when user scrolls to the bottom on the page.', 'woodmart' ),
			'default' => false,
		),
		array(
			'id'       => 'collapse_footer_widgets',
			'type'     => 'switch',
			'title'    => esc_html__( 'Collapse widgets on mobile', 'woodmart' ),
			'subtitle' => esc_html__( 'Widgets added to the footer will be collapsed by default and opened when you click on their titles.', 'woodmart' ),
			'default' => false,
			'class' => 'without-border'
		),
		array (
			'id' => 'footer_color_scheme',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Footer color scheme options', 'woodmart' ),
		),
		array(
			'id'       => 'footer-style',
			'type'     => 'select',
			'title'    => esc_html__('Footer text color', 'woodmart'),
			'subtitle' => esc_html__('Choose your footer color scheme', 'woodmart'),
			'options'  => array(
				'dark' => esc_html__('Dark', 'woodmart'),  
				'light' => esc_html__('Light', 'woodmart'), 
			),
			'default' => 'dark'
		),
		array(
			'id'       => 'footer-bar-bg',
			'type'     => 'background',
			'title'    => esc_html__( 'Footer background', 'woodmart' ),
			'subtitle' => esc_html__( 'You can set your footer section background color or upload some graphic.', 'woodmart' ),
			$output   => array( '.footer-container' ),
			'default'  => array(
				'background-color' => '#ffffff'
			),
			'tags'     => 'footer color',
			'class' => 'without-border'
		),
		array (
			'id' => 'footer_copyrights_option',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Footer copyrights options', 'woodmart' ),
		),
		array(
			'id'       => 'disable_copyrights',
			'type'     => 'switch',
			'title'    => esc_html__( 'Copyrights', 'woodmart' ),
			'subtitle' => esc_html__( 'Turn on/off a section with your copyrights under the footer.', 'woodmart' ),
			'default' => true
		),
		array(
			'id'       => 'copyrights-layout',
			'type'     => 'select',
			'title'    => esc_html__( 'Copyrights layout', 'woodmart' ),
			'subtitle' => esc_html__( 'Set different copyrights section layout.', 'woodmart' ),
			'options'  => array(
				'two-columns' => esc_html__( 'Two columns', 'woodmart' ),  
				'centered' => esc_html__( 'Centered', 'woodmart' ),  
			),
			'default' => 'two-columns'
		),
		array(
			'id'       => 'copyrights',
			'type'     => 'text',
			'title'    => esc_html__('Copyrights text', 'woodmart'),
			'subtitle' => esc_html__('Place here text you want to see in the copyrights area. You can use shortocdes. Ex.: [social_buttons]', 'woodmart'),
			'default' => '<small><a href="http://woodmart.xtemos.com"><strong>WOODMART</strong></a> <i class="fa fa-copyright"></i>  2018 CREATED BY <a href="http://xtemos.com"><strong><span style="color: red; font-size: 12px;">X</span>-TEMOS STUDIO</strong></a>. PREMIUM E-COMMERCE SOLUTIONS.</small>'
		),
		array(
			'id'       => 'copyrights2',
			'type'     => 'text',
			'title'    => esc_html__('Text next to copyrights', 'woodmart'),
			'subtitle' => esc_html__('You can use shortcodes. Ex.: [social_buttons] or place an HTML Block built with WPBakery Page Builder builder there like [html_block id="258"]', 'woodmart'),
			'default' => '<img src="//dummy.xtemos.com/woodmart/demos/wp-content/uploads/sites/2/2018/09/dummy-payments.png" alt="payments">', //'[social_buttons align="right" style="colored" size="small"]'
			'class' => 'without-border'
		),
		array (
			'id' => 'prefooter_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Prefooter area', 'woodmart' ),
		),
		array(
			'id'=>'prefooter_area',
			'type' => 'textarea',
			'title' => esc_html__('HTML before footer', 'woodmart'),
			'subtitle' => esc_html__('Custom HTML Allowed (wp_kses)', 'woodmart'),
			'desc' => esc_html__('This is the text before footer field, again good for additional info. You can place here any shortcode, for ex.: [html_block id=""]', 'woodmart'),
			'validate' => 'html_custom',
			'default' => '[html_block id="258"]',
			'allowed_html' => array(
				'a' => array(
					'href' => array(),
					'title' => array()
				),
				'br' => array(),
				'em' => array(),
				'p' => array(),
				'div' => array(),
				'strong' => array()
			),
			'tags'     => 'prefooter'
		),
	), 
) );
