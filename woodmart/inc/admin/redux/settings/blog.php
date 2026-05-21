<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Blog', 'woodmart'),
	'id' => 'blog',
	'icon' => 'el-icon-pencil',
	'fields' => array (
		array (
			'id'       => 'blog_layout',
			'type'     => 'image_select',
			'title'    => esc_html__('Blog Layout', 'woodmart'),
			'subtitle' => esc_html__('Select main content and sidebar alignment for blog pages.', 'woodmart'),
			'options'  => array(
				'full-width'      => array(
					'alt'   => '1 Column', 
					'title' => esc_html__( '1 Column', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/none.png'
				),
				'sidebar-left'      => array(
					'alt'   => '2 Column Left', 
					'title' => esc_html__( '2 Column Left', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/left.png'
				),
				'sidebar-right'      => array(
					'alt'   => '2 Column Right',
					'title' => esc_html__( '2 Column Right', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/right.png'
				),
			),
			'default' => 'sidebar-right'
		),
		array (
			'id'       => 'blog_sidebar_width',
			'type'     => 'button_set',
			'title'    => esc_html__('Blog Sidebar size', 'woodmart'),
			'subtitle' => esc_html__('You can set different sizes for your blog pages sidebar', 'woodmart'),
			'options'  => array(
				2 => esc_html__( 'Small', 'woodmart' ),
				3 => esc_html__( 'Medium', 'woodmart' ),
				4 => esc_html__( 'Large', 'woodmart' ),
			),
			'default' => 3
		),
		array (
			'id'       => 'blog_design',
			'type'     => 'select',
			'title'    => esc_html__( 'Blog Design', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use different design for your blog styled for the theme.', 'woodmart' ),
			'options'  => array(
				'default' => esc_html__( 'Default', 'woodmart' ),
				'default-alt' => esc_html__( 'Default alternative', 'woodmart' ),
				'small-images' => esc_html__( 'Small images', 'woodmart' ),
				'chess' => esc_html__( 'Chess', 'woodmart' ),
				'masonry' => esc_html__( 'Masonry grid', 'woodmart' ),
				'mask' => esc_html__( 'Mask on image', 'woodmart'),
			),
			'default' => 'masonry'
		),
		array (
			'id'       => 'blog_columns',
			'type'     => 'button_set',
			'title'    => esc_html__('Blog items columns', 'woodmart'),
			'subtitle' => esc_html__('For masonry grid design', 'woodmart'),
			'options'  => array(
				2 => '2',
				3 => '3',
				4 => '4',
			),
			'default' => 3,
			'required' => array(
				array( 'blog_design', 'equals', array( 'masonry', 'mask' ) ),
			)
		),
		array (
			'id'       => 'blog_spacing',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Space between posts', 'woodmart' ),
			'subtitle' => esc_html__( 'You can set different spacing between posts on blog page', 'woodmart' ),
			'options'  => array(
				0 => '0',
				2 => '2',
				6 => '5',
				10 => '10',
				20 => '20',
				30 => '30'
			),
			'default' => 20,
			'required' => array(
				array( 'blog_design', 'equals', array( 'masonry', 'mask' ) ),
			)
		),
		array (
			'id'       => 'blog_style',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Blog Style', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use flat style or add a shadow to your blog posts.', 'woodmart' ),
			'options'  => array(
				'flat' => 'Flat',
				'shadow' => 'With shadow',
			),
			'default' => 'shadow',
			'class'   => 'without-border'
		),
		array (
			'id' => 'post_option_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Blog post option', 'woodmart' ),
		),
		array (
			'id'       => 'blog_excerpt',
			'type'     => 'button_set',
			'title'    => esc_html__('Posts excerpt', 'woodmart'),
			'subtitle' => esc_html__('If you will set this option to "Excerpt" then you are able to set custom excerpt for each post or it will be cutted from the post content. If you choose "Full content" then all content will be shown, or you can also add "Read more button" while editing the post and by doing this cut your excerpt length as you need.', 'woodmart'),
			'options'  => array(
				'excerpt' => 'Excerpt',
				'full' => 'Full content'
			),
			'default' => 'full'
		),
		array (
			'id'       => 'blog_words_or_letters',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Excerpt length by words or letters', 'woodmart' ),
			'subtitle' => esc_html__( 'Limit your excerpt text for posts by words or letters.', 'woodmart' ),
			'options'  => array(
				'word' => 'Words',
				'letter' => 'Letters'
			),
			'default' => 'letter',
			'required' => array(
				array( 'blog_excerpt', 'equals', 'excerpt' ),
			)
		),
		array (
			'id'       => 'blog_excerpt_length',
			'type'     => 'text',
			'title'    => esc_html__('Excerpt length', 'woodmart'),
			'subtitle' => esc_html__('Number of words or letters that will be displayed for each post if you use "Excerpt" mode and don\'t set custom excerpt for each post.', 'woodmart'),
			'default' => 135,
			'required' => array(
					array('blog_excerpt','equals', 'excerpt'),
			)
		),
		array (
			'id'       => 'blog_pagination',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Blog pagination', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose a type for the pagination on your blog page.', 'woodmart' ),
			'options'  => array(
				'pagination' => esc_html__( 'Pagination links', 'woodmart' ),
				'load_more' => esc_html__( '"Load more" button', 'woodmart' ),
				'infinit' => esc_html__( 'Infinit scrolling', 'woodmart' ),
			),
			'default' => 'pagination',
			'class'   => 'without-border'
		),
		array (
			'id' => 'single_post_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Single blog post options', 'woodmart' ),
		),
		array (
			'id'       => 'single_post_design',
			'type'     => 'select',
			'title'    => esc_html__( 'Single post design', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use different design for your single post page.', 'woodmart' ),
			'options'  => array(
				'default' => esc_html__( 'Default', 'woodmart' ),
				'large_image' => esc_html__( 'Large image', 'woodmart' ),
			),
			'default' => 'default'
		),
		array (
			'id'       => 'single_post_header',
			'type'     => 'select',
			'title'    => esc_html__( 'Single post header', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use different header for your single post page.', 'woodmart' ),
			'options'  => woodmart_get_whb_headers_array( true ),
			'default' => 'none',
		),
		array (
			'id'       => 'blog_share',
			'type'     => 'switch',
			'title'    => esc_html__('Share buttons', 'woodmart'),
			'subtitle' => esc_html__('Display share icons on single post page', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'blog_navigation',
			'type'     => 'switch',
			'title'    => esc_html__('Posts navigation', 'woodmart'),
			'subtitle' => esc_html__('Next and previous posts links on single post page', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'blog_author_bio',
			'type'     => 'switch',
			'title'    => esc_html__('Author bio', 'woodmart'),
			'subtitle' => esc_html__('Display information about the post author', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'blog_related_posts',
			'type'     => 'switch',
			'title'    => esc_html__('Related posts', 'woodmart'),
			'subtitle' => esc_html__('Show related posts on single post page (by tags)', 'woodmart'),
			'default' => true
		),
	),
) );
