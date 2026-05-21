<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Typography', 'woodmart'),
	'id' => 'typography',
	'icon' => 'el-icon-fontsize',
	'fields' => array (
		array(
			'id'          => 'text-font',
			'type'        => 'typography',
			'title'       => esc_html__('Text font', 'woodmart'),
			'all_styles'  => true,
			'google'      => true,
			'font-backup' => true,
			'text-align'  => false,
			'line-height' => false,
			'letter-spacing' => true,
			$output      => $woodmart_selectors['text-font'],
			'units'       =>'px',
			'subtitle'    => esc_html__('Set you typography options for body, paragraphs.', 'woodmart'),
			'default'     => array(
				'font-family' => 'Lato',
				'google'      => true,
				'font-backup' => 'Arial, Helvetica, sans-serif'
			),
			'tags'     => 'typography'
		),
		array(
			'id'          => 'primary-font',
			'type'        => 'typography',
			'title'       => esc_html__('Primary font', 'woodmart'),
			'all_styles'  => true,
			'google'      => true,
			'font-backup' => true,
			'font-size'   => false,
			'line-height' => false,
			'text-align'  => false,
			'letter-spacing' => true,
			$output      => $woodmart_selectors['primary-font'],
			'units'       =>'px',
			'subtitle'    => esc_html__('Set you typography options for titles, post names.', 'woodmart'),
			'default'     => array(
				'font-family' => 'Poppins',
				'google'      => true,
				'font-backup' => "'MS Sans Serif', Geneva, sans-serif"
			),
			'tags'     => 'typography'
		),
		array(
			'id'          => 'post-titles-font',
			'type'        => 'typography',
			'title'       => esc_html__('Entities names', 'woodmart'),
			'all_styles'  => true,
			'google'      => true,
			'font-backup' => true,
			'font-size'   => false,
			'color'       => false,
			'line-height' => false,
			'text-align'  => false,
			'letter-spacing' => true,
			$output      => $woodmart_selectors['titles-font'],
			'units'       =>'px',
			'subtitle'    => esc_html__('Titles for posts, products, categories and pages', 'woodmart'),
			'default'     => array(
				'font-family' => 'Poppins',
				'google'      => true,
				'font-backup' => "'MS Sans Serif', Geneva, sans-serif"
			),
			'tags'        => 'typography'
		),
		array(
			'id'          => 'secondary-font',
			'type'        => 'typography',
			'title'       => esc_html__('Secondary font', 'woodmart'),
			'all_styles'  => true,
			'google'      => true,
			'font-backup' => true,
			'font-size'   => false,
			'line-height' => false,
			'text-align'  => false,
			'letter-spacing' => true,
			$output      => $woodmart_selectors['secondary-font'],
			'units'       =>'px',
			'subtitle'    => esc_html__('Use for secondary titles (use CSS class "font-alt" or "title-alt")', 'woodmart'),
			'default'     => array(
				'font-family' => 'Lato',
				'font-weight' => 400,
				'google'      => true,
				'font-backup' => "'MS Sans Serif', Geneva, sans-serif"
			),
			'tags'     => 'typography'
		),
		array(
			'id'          => 'widget-titles-font',
			'type'        => 'typography',
			'title'       => esc_html__( 'Widget titles font', 'woodmart' ),
			'subtitle'    => esc_html__( 'Typography options for titles for widgets in your sidebars.', 'woodmart' ),
			'all_styles'  => true,
			'google'      => true,
			'font-backup' => true,
			'font-size'   => true,
			'line-height' => true,
			'text-align'  => true,
			'letter-spacing' => true,
			$output      => $woodmart_selectors['widget-titles-font'],
			'units'       =>'px',
			'default'     => array(
				'font-family' => 'Poppins',
				'font-weight' => 600,
				'google'      => true,
			),
			'tags'     => 'typography'
		),
		array(
			'id'          => 'navigation-font',
			'type'        => 'typography',
			'title'       => esc_html__( 'Navigation font', 'woodmart' ),
			'subtitle'    => esc_html__( 'This option will change typography for your header navigation.', 'woodmart' ),
			'all_styles'  => true,
			'google'      => true,
			'font-backup' => true,
			'font-size'   => true,
			'line-height' => false,
			'color'       => false,
			'text-align'  => false,
			'letter-spacing' => true,
			$output      => $woodmart_selectors['navigation-font'],
			'units'       =>'px',
			'default'     => array(
				'font-family' => 'Lato',
				'font-weight' => 700,
				'font-size' => '13px',
				'google'      => true,
				'font-backup' => "'MS Sans Serif', Geneva, sans-serif"
			),
			'tags'     => 'typography'
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Typekit Fonts', 'woodmart'),
	'id' => 'typekit_font',
	'subsection' => true,
	'fields' => array(
		array(
			'id'    => 'info_success2',
			'type'  => 'info',
			'style' => 'success',
			'desc'  => wp_kses( __( 'To use your Typekit font, you need to create an account on the <a href="https://typekit.com/" target="_blank">service</a> and obtain your key ID here. Then, you need to enter all custom fonts you will use separated with coma. After this, save Theme Settings and reload this page to be able to select your fonts in the list under the Theme Settings -> Typography section.', 'woodmart' ), array( 'a' => array( 'href' => true, 'target' => true, ), 'br' => array(), 'strong' => array() ) ),
		),
		array(
			'title' => 'Typekit Kit ID',
			'id' => 'typekit_id',
			'type' => 'text',
			'desc' => esc_html__('Enter your ', 'woodmart') . '<a target="_blank" href="https://typekit.com/account/kits">Typekit Kit ID</a>.',
		),
		array(
			'title' => 'Typekit Typekit Font Face',
			'id' => 'typekit_fonts',
			'type' => 'text',
			'desc' => esc_html__('Example: futura-pt, lato', 'woodmart'),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Custom Fonts', 'woodmart' ),
	'id' => 'custom_fonts',
	'subsection' => true,
	'fields' => array(
		array(
			'id'   => 'info_success',
			'type' => 'info',
			'style' => 'success',
			'desc' => wp_kses( __( 'In this section you can upload your custom fonts files. To ensure the best compatibility in all browsers you would better upload your fonts in all available formats. 
<br><strong>IMPORTANT NOTE</strong>: After uploading all files and entering the font name, you will have to save Theme Settings and <strong>RELOAD</strong> this page. Then, you will be able to go to Theme Settings -> Typography and select the custom font from the list. Find more information in our documentation <a href="https://xtemos.com/docs/woodmart/faq-guides/upload-custom-fonts/" target="_blank">here</a>.', 'woodmart' ), array( 'a' => array( 'href' => true, 'target' => true, ), 'br' => array(), 'strong' => array() ) ),
		),
		array (
			'id' => 'multi_custom_fonts',
			'type' => 'woodmart_multi_fonts',
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Advanced typography', 'woodmart' ),
	'id' => 'advanced_typography',
	'subsection' => true,
	'fields' => array(
		array (
			'id' => 'advanced_typography',
			'type' => 'woodmart_typography',
			$output => true
		),
	),
) );