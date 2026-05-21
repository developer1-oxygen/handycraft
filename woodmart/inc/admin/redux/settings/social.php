<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Social profiles', 'woodmart' ),
	'id' => 'social',
	'icon' => 'el-icon-group',
	'fields' => array (
		array (
			'id' => 'sticky_social',
			'type' => 'switch',
			'default' => false,
			'title' => esc_html__( 'Sticky social links', 'woodmart' ),
			'subtitle' => esc_html__( 'Social buttons will be fixed on the screen when you scroll the page.', 'woodmart' ),
		),
		array (
			'id'       => 'sticky_social_type',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Sticky social links type', 'woodmart' ),
			'options'  => array(
				'share' => esc_html__( 'Share', 'woodmart' ), 
				'follow' => esc_html__( 'Follow', 'woodmart' ),
			),
			'default' => 'follow',
			'required' => array(
				array( 'sticky_social', 'equals', array( true ) ),
			)
		),
		array (
			'id'       => 'sticky_social_position',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Sticky social links position', 'woodmart' ),
			'options'  => array(
				'left' => esc_html__( 'Left', 'woodmart' ), 
				'right' => esc_html__( 'Right', 'woodmart' ),
			),
			'default' => 'right',
			'required' => array(
				array( 'sticky_social', 'equals', array( true ) ),
			)
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Links to social profiles', 'woodmart'),
	'id' => 'social-follow',
	'subsection' => true,
	'fields' => array (
		array (
			'id'   => 'info_follow',
			'type' => 'info',
			'style' => 'success',
			'desc' => esc_html__('Configurate your [social_buttons] shortcode. You can leave field empty to remove particular link. Note that there are two types of social buttons. First one is SHARE buttons [social_buttons type="share"]. It displays icons that share your page in social media. And the second one is FOLLOW buttons [social_buttons type="follow"]. Simply displays links to your social profiles. You can configure both types here.', 'woodmart')
		),
		array (
			'id'       => 'fb_link',
			'type'     => 'text',
			'title'    => esc_html__('Facebook link', 'woodmart'),
			'default' => '#'
		),
		array (
			'id'       => 'twitter_link',
			'type'     => 'text',
			'title'    => esc_html__('Twitter link', 'woodmart'),
			'default' => '#'
		),
		array (
			'id'       => 'isntagram_link',
			'type'     => 'text',
			'title'    => esc_html__('Instagram', 'woodmart'),
			'default' => '#'
		),
		array (
			'id'       => 'pinterest_link',
			'type'     => 'text',
			'title'    => esc_html__('Pinterest link', 'woodmart'),
			'default' => '#'
		),
		array (
			'id'       => 'youtube_link',
			'type'     => 'text',
			'title'    => esc_html__('Youtube link', 'woodmart'),
			'default' => '#'
		),
		array (
			'id'       => 'tumblr_link',
			'type'     => 'text',
			'title'    => esc_html__('Tumblr link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'linkedin_link',
			'type'     => 'text',
			'title'    => esc_html__('LinkedIn link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'vimeo_link',
			'type'     => 'text',
			'title'    => esc_html__('Vimeo link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'flickr_link',
			'type'     => 'text',
			'title'    => esc_html__('Flickr link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'github_link',
			'type'     => 'text',
			'title'    => esc_html__('Github link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'dribbble_link',
			'type'     => 'text',
			'title'    => esc_html__('Dribbble link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'behance_link',
			'type'     => 'text',
			'title'    => esc_html__('Behance link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'soundcloud_link',
			'type'     => 'text',
			'title'    => esc_html__('SoundCloud link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'spotify_link',
			'type'     => 'text',
			'title'    => esc_html__('Spotify link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'ok_link',
			'type'     => 'text',
			'title'    => esc_html__('OK link', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'vk_link',
			'type'     => 'text',
			'title'    => esc_html__('VK link', 'woodmart'), 
			'default' => ''
		),
		array (
			'id'       => 'whatsapp_link',
			'type'     => 'text',
			'title'    => esc_html__('WhatsApp link', 'woodmart'), 
			'default' => ''
		),
		array (
			'id'       => 'snapchat_link',
			'type'     => 'text',
			'title'    => esc_html__('Snapchat link', 'woodmart'), 
			'default' => ''
		),
		array (
			'id'       => 'tg_link',
			'type'     => 'text',
			'title'    => esc_html__( 'Telegram link', 'woodmart' ), 
			'default' => ''
		),
		array (
			'id'       => 'social_email',
			'type'     => 'switch',
			'default'  => false,
			'title'    => esc_html__('Email for social links', 'woodmart')
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => 'Share buttons',
	'id' => 'social-share',
	'subsection' => true,
	'fields' => array (
		array (
			'id'   => 'info_share',
			'type' => 'info',
			'style' => 'success',
			'desc' => esc_html__('Configurate your [social_buttons] shortcode. You can leave field empty to remove particular link. Note that there are two types of social buttons. First one is SHARE buttons [social_buttons type="share"]. It displays icons that share your page in social media. And the second one is FOLLOW buttons [social_buttons type="follow"]. Simply displays links to your social profiles. You can configure both types here.', 'woodmart')
		),
		array (
			'id'       => 'share_fb',
			'default'  => true,
			'type'     => 'switch',
			'title'    => esc_html__('Share in facebook', 'woodmart')
		),
		array (
			'id'       => 'share_twitter',
			'default'  => true,
			'type'     => 'switch',
			'title'    => esc_html__('Share in twitter', 'woodmart')
		),
		array (
			'id'       => 'share_pinterest',
			'type'     => 'switch',
			'default'  => true,
			'title'    => esc_html__('Share in pinterest', 'woodmart')
		),
		array (
			'id'       => 'share_linkedin',
			'type'     => 'switch',
			'default'  => true,
			'title'    => esc_html__('Share in linkedin', 'woodmart')
		),
		array (
			'id'       => 'share_ok',
			'type'     => 'switch',
			'default'  => false,
			'title'    => esc_html__('Share in OK', 'woodmart')
		),
		array (
			'id'       => 'share_whatsapp',
			'type'     => 'switch',
			'default'  => false,
			'title'    => esc_html__('Share in whatsapp', 'woodmart')
		),
		array (
			'id'       => 'share_vk',
			'type'     => 'switch',
			'default'  => false,
			'title'    => esc_html__('Share in VK', 'woodmart')
		),
		array (
			'id'       => 'share_tg',
			'type'     => 'switch',
			'default'  => true,
			'title'    => esc_html__( 'Share in Telegram', 'woodmart' )
		),
		array (
			'id'       => 'share_email',
			'type'     => 'switch',
			'default'  => false,
			'title'    => esc_html__('Email for share links', 'woodmart')
		),
	),
) );
