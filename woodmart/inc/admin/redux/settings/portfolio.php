<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Portfolio', 'woodmart'),
	'id' => 'portfolio',
	'icon' => 'el-icon-th',
	'fields' => array (
		array (
			'id'       => 'disable_portfolio',
			'type'     => 'switch',
			'title'    => esc_html__( 'Disable portfolio', 'woodmart' ),
			'subtitle' => esc_html__( 'You can disable the custom post type for portfolio projects completely.', 'woodmart' ),
			'default'  => false
		),
		array (
			'id'       => 'portoflio_style',
			'type'     => 'image_select',
			'title'    => esc_html__('Portfolio Style', 'woodmart'),
			'subtitle' => esc_html__('You can use different styles for your projects.', 'woodmart'),
			'options'  => array(
				'hover' => array(
					'title' => esc_html__( 'Show text on mouse over', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/portfolio/hover.jpg',
				),
				'hover-inverse' => array(
					'title' => esc_html__( 'Alternative', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/portfolio/hover-inverse.jpg',
				),
				'text-shown' => array(
					'title' => esc_html__( 'Text under image', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/portfolio/text-shown.jpg',
				),
				'parallax' => array(
					'title' => esc_html__( 'Mouse move parallax', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/portfolio/hover.jpg',
				),
			),
			'default' => 'hover',
			'class'   => 'without-border'
		),
		array (
			'id' => 'portfolio_option_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Project options', 'woodmart' ),
		),
		array (
			'id'       => 'portoflio_filters',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show categories filters', 'woodmart' ),
			'subtitle' => esc_html__( 'Display categories list that allows you to filter your portfolio projects with animation on click.', 'woodmart' ),
			'default'  => true
		),
		array (
			'id'       => 'portfolio_full_width',
			'type'     => 'switch',
			'title'    => esc_html__('Full Width portfolio', 'woodmart'),
			'subtitle' => esc_html__('Makes container 100% width of the page', 'woodmart'),
			'default' => false
		),
		array (
			'id'       => 'projects_columns',
			'type'     => 'button_set',
			'title'    => esc_html__('Projects columns', 'woodmart'),
			'subtitle' => esc_html__('How many projects you want to show per row', 'woodmart'),
			'options'  => array(
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6'
			),
			'default' => 3
		),
		array (
			'id'       => 'portfolio_spacing',
			'type'     => 'button_set',
			'title'    => esc_html__('Space between projects', 'woodmart'),
			'subtitle' => esc_html__('You can set different spacing between blocks on portfolio page', 'woodmart'),
			'options'  => array(
				0 => '0',
				2 => '2',
				6 => '5',
				10 => '10',
				20 => '20',
				30 => '30'
			),
			'default' => 30
		),
		array (
			'id'       => 'portoflio_per_page',
			'type'     => 'text',
			'title'    => esc_html__('Items per page', 'woodmart'),
			'subtitle' => esc_html__( 'Number of portfolio projects that will be displayed on one page.', 'woodmart' ),
			'default'  => 12
		),
		array (
			'id'       => 'portfolio_pagination',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Portfolio pagination', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose a type for the pagination on your portfolio page.', 'woodmart' ),
			'options'  => array(
				'pagination' => esc_html__( 'Pagination links', 'woodmart'),
				'load_more' => esc_html__('"Load more" button', 'woodmart'),
				'infinit' => esc_html__('Infinit scrolling', 'woodmart'),
			),
			'default' => 'pagination'
		),
		array (
			'id'       => 'portoflio_orderby',
			'type'     => 'select',
			'title'    => esc_html__( 'Portfolio order by', 'woodmart' ),
			'subtitle' => esc_html__( 'Select a parameter for projects order.', 'woodmart' ),
			'options'  => array(
				'date' =>esc_html__( 'Date', 'woodmart'),
				'ID' => esc_html__( 'ID', 'woodmart'),
				'title' => esc_html__( 'Title', 'woodmart'),
				'modified' => esc_html__( 'Modified', 'woodmart'),
				'menu_order' => esc_html__( 'Menu order', 'woodmart')
			),
			'default' => 'date'
		),
		array (
			'id'       => 'portoflio_order',
			'type'     => 'button_set',
			'title'    => esc_html__('Portfolio order', 'woodmart'),
			'subtitle' => esc_html__( 'Choose ascending or descending order.', 'woodmart' ),
			'options'  => array(
				'DESC' =>esc_html__( 'DESC', 'woodmart'),
				'ASC' => esc_html__( 'ASC', 'woodmart'),
			),
			'default' => 'DESC',
			'class'   => 'without-border'
		),
		array (
			'id' => 'single_portfolio_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Single project options', 'woodmart' ),
		),
		array (
			'id'       => 'portfolio_navigation',
			'type'     => 'switch',
			'title'    => esc_html__('Projects navigation', 'woodmart'),
			'subtitle' => esc_html__('Next and previous projects links on single project page', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'portfolio_related',
			'type'     => 'switch',
			'title'    => esc_html__('Related Projects', 'woodmart'),
			'subtitle' => esc_html__('Show related projects carousel.', 'woodmart'),
			'default' => true
		),
	),
) );
