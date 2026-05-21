<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Shop', 'woodmart'),
	'id' => 'shop',
	'icon' => 'el-icon-shopping-cart',
	'fields' => array (
		array(
			'id'       => 'search_by_sku',
			'type'     => 'switch',
			'title'    => esc_html__('Search by product SKU', 'woodmart'), 
			'default' => true
		),
		array (
			'id'       => 'shop_filters',
			'type'     => 'switch',
			'title'    => esc_html__('Shop filters', 'woodmart'),
			'subtitle' => esc_html__('Enable shop filters widget\'s area above the products.', 'woodmart'),
			'default' => false
		),
		array (
			'id'       => 'shop_filters_always_open',
			'type'     => 'switch',
			'title'    => esc_html__( 'Shop filters area always opened', 'woodmart' ),
			'subtitle' => esc_html__( 'If you enable this option the shop filters will be always opened on the shop page.', 'woodmart' ),
			'default' => false,
			'required' => array(
				array( 'shop_filters', 'equals', true ),
			)
		),
		array (
			'id'       => 'shop_filters_type',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Shop filters content type', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use widgets or custom HTML block with our Product filters WPBakery element.', 'woodmart' ),
			'options'  => array(
				'widgets' => esc_html__( 'Widgets', 'woodmart' ),
				'content' => esc_html__( 'Custom content', 'woodmart' ),
			),
			'default' => 'widgets',
			'required' => array(
				array( 'shop_filters', 'equals', true ),
			)
		),
		array (
			'id'       => 'shop_filters_content',
			'type'     => 'select',
			'title'    => esc_html__( 'Shop filters custom content', 'woodmart' ),
			'subtitle' => esc_html__( 'You can create an HTML Block in Dashboard -> HTML Blocks and add Product filters WPBakery element there.', 'woodmart' ),
			'options'  => array_flip( woodmart_get_static_blocks_array() ),
			'required' => array(
				array( 'shop_filters_type', 'equals', 'content' ),
			)
		),
		array (
			'id'       => 'shop_filters_close',
			'type'     => 'switch',
			'title'    => esc_html__( 'Stop close filters after click', 'woodmart' ),
			'subtitle' => esc_html__( 'This option will prevent filters area from closing when you click on certain filter links.', 'woodmart' ),
			'default' => false,
			'required' => array(
				array( 'shop_filters_always_open', 'equals', false ),
			)
		),
		array (
			'id'       => 'ajax_shop',
			'type'     => 'switch',
			'title'    => esc_html__('AJAX shop', 'woodmart'),
			'subtitle' => esc_html__('Enable AJAX functionality for filters widgets on shop.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'ajax_scroll',
			'type'     => 'switch',
			'title'    => esc_html__('Scroll to top after AJAX', 'woodmart'), 
			'subtitle' => esc_html__('Disable - Enable scroll to top after AJAX.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'add_to_cart_action',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Action after add to cart', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose between showing informative popup and opening shopping cart widget. Only for shop page.', 'woodmart' ),
			'options'  => array(
				'popup' => array(
					'title' => esc_html__( 'Show popup', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/add-to-cart-action/popup.jpg'
				),
				'widget' => array(
					'title' => esc_html__( 'Display widget', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/add-to-cart-action/widget.jpg'
				),
				'nothing' => array(
					'title' => esc_html__( 'No action', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/add-to-cart-action/nothing.jpg'
				),
			),
			'default' => 'widget',
		),

		array (
			'id'       => 'add_to_cart_action_timeout',
			'type'     => 'switch',
			'title'    => esc_html__('Hide widget automatically', 'woodmart'),
			'subtitle' => esc_html__('After adding to cart the shopping cart widget will be hidden automatically', 'woodmart'),
			'default'  => false,
			'required' => array(
				array( 'add_to_cart_action', '!=', 'nothing' ),
			)
		),

		array(
			'id'        => 'add_to_cart_action_timeout_number',
			'type'      => 'slider',
			'title'     => esc_html__( 'Hide widget after', 'woodmart' ),
			'desc'      => esc_html__( 'Set the number of seconds for the shopping cart widget to be displayed after adding to cart', 'woodmart' ),
			'default'   => 3,
			'min'       => 3,
			'step'      => 1,
			'max'       => 20,
			'required' => array(
				array( 'add_to_cart_action', '!=', 'nothing' ),
				array( 'add_to_cart_action_timeout', '=', true ),
			)
		),
		
		array (
			'id'       => 'quick_shop_variable',
			'type'     => 'switch',
			'title'    => esc_html__('"Quick Shop" for variable products', 'woodmart'),
			'subtitle' => esc_html__('Allow your users to purchase variable products directly from the shop page.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'cat_desc_position',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Category description position', 'woodmart' ),
			'subtitle' => esc_html__( 'You can change default products category description position and move it below the products.', 'woodmart' ),
			'options'  => array(
				'before' => esc_html__( 'Before product grid', 'woodmart' ),
				'after' => esc_html__( 'After product grid', 'woodmart' ),
			),
			'default' => 'before'
		),
		array (
			'id'       => 'empty_cart_text',
			'type'     => 'textarea',
			'title'    => esc_html__('Empty cart text', 'woodmart'),
			'subtitle' => esc_html__('Text will be displayed if user don\'t add any products to cart', 'woodmart'),      
			'default'  => 'Before proceed to checkout you must add some products to your shopping cart.<br> You will find a lot of interesting products on our "Shop" page.',
			'class'   => 'without-border'
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => 'Products grid',
	'id' => 'shop-grid',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'shop_per_page',
			'type'     => 'text',
			'title'    => esc_html__('Products per page', 'woodmart'),
			'subtitle' => esc_html__('Number of products per page', 'woodmart'),
			'default' => 12
		),
		array (
			'id'       => 'per_page_links',
			'type'     => 'switch',
			'title'    => esc_html__('Products per page links', 'woodmart'),
			'subtitle' => esc_html__('Allow customers to change number of products per page', 'woodmart'),
			'default' => true
		),
		array (
			'id' => 'per_page_options',
			'type' => 'text',
			'title' => esc_html__('Products per page variations', 'woodmart'),
			'default' => '9,24,36',
			'desc' => esc_html__('For ex.: 12,24,36,-1. Use -1 to show all products on the page', 'woodmart'),
			'required' => array(
					array('per_page_links','equals',true),
			)
		),
		array (
			'id'       => 'shop_view',
			'type'     => 'button_set',
			'title'    => __('Shop products view', 'woodmart'), 
			'subtitle' => __('You can set different view mode for the shop page', 'woodmart'),
			'options'  => array(
				'grid' => __('Grid', 'woodmart'),  
				'list' => __('List', 'woodmart'),  
				'grid_list' => __('Grid / List', 'woodmart'), 
				'list_grid' => __('List / Grid', 'woodmart'), 
			),
			'default' => 'grid'
		),
		array (
			'id'       => 'products_columns',
			'type'     => 'button_set',
			'title'    => esc_html__('Products columns', 'woodmart'),
			'subtitle' => esc_html__('How many products you want to show per row', 'woodmart'),
			'options'  => array(
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6'
			),
			'default' => 3,
			'required' => array(
					array('shop_view','not','list'),
			)
		),
		array (
			'id'       => 'products_columns_mobile',
			'type'     => 'button_set',
			'title'    => esc_html__('Products columns on mobile', 'woodmart'),
			'subtitle' => esc_html__('How many products you want to show per row on mobile devices', 'woodmart'),
			'options'  => array(
				1 => '1',
				2 => '2',
			),
			'default' => 2,
			'required' => array(
					array('shop_view','not','list'),
			)
		),
		array (
			'id'       => 'products_spacing',
			'type'     => 'button_set',
			'title'    => esc_html__('Space between products', 'woodmart'),
			'subtitle' => esc_html__('You can set different spacing between blocks on shop page', 'woodmart'),
			'options'  => array(
				0 => '0',
				2 => '2',
				6 => '5',
				10 => '10',
				20 => '20',
				30 => '30'
			),
			'default' => 30,
			'required' => array(
					array('shop_view','not','list'),
			)
		),
		array (
			'id'       => 'shop_pagination',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Products pagination', 'woodmart' ),
			'subtitle'     => esc_html__( 'Choose a type for the pagination on your shop page.', 'woodmart' ),
			'options'  => array(
				'pagination' => esc_html__( 'Pagination', 'woodmart'),
				'more-btn' => esc_html__('"Load more" button', 'woodmart'),
				'infinit' => esc_html__( 'Infinit scrolling', 'woodmart'),
			),
			'default' => 'pagination'
		),
		array (
			'id'       => 'per_row_columns_selector',
			'type'     => 'switch',
			'title'    => esc_html__('Number of columns selector', 'woodmart'),
			'subtitle' => esc_html__('Allow customers to change number of columns per row', 'woodmart'),
			'default' => true,
			'required' => array(
					array('shop_view','not','list'),
			)
		),
		array (
			'id'       => 'products_columns_variations',
			'type'     => 'button_set',
			'title'    => esc_html__('Available products columns variations', 'woodmart'),
			'subtitle' => esc_html__('What columns users may select to be displayed on the product page', 'woodmart'),
			'multi'    => true,
			'options'  => array(
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6'
			),
			'default' => array(2,3,4),
			'required' => array(
					array('per_row_columns_selector','equals',true),
			)
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Products styles', 'woodmart'),
	'id' => 'products-styles',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'products_masonry',
			'type'     => 'switch',
			'title'    => esc_html__( 'Masonry grid', 'woodmart' ),
			'subtitle'     => esc_html__( 'Useful if your products have different height.', 'woodmart' ),
			'default' => false,
			'required' => array(
				array( 'shop_view', 'not', 'list' ),
			)
		),
		array (
			'id'       => 'products_different_sizes',
			'type'     => 'switch',
			'title'    => esc_html__('Products grid with different sizes', 'woodmart'),
			'subtitle' => esc_html__('In this situation, some of the products will be twice bigger in width than others. Recommended to use with 6 columns grid only.', 'woodmart'),
			'default' => false,
			'required' => array(
					array('shop_view','not','list'),
			)
		),
		array (
			'id'       => 'products_hover',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Hover on product', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose one of those hover effects for products', 'woodmart' ),
			'options'  => woodmart_get_config( 'product-hovers' ),
			'default' => 'base',
			'required' => array(
				array( 'shop_view', 'not', 'list' ),
			)
		),
		array (
			'id'       => 'base_hover_mobile_click',
			'type'     => 'switch',
			'title'    => esc_html__( 'Open product on click on mobile', 'woodmart' ), 
			'subtitle' => esc_html__( 'If you disable this option, when user click on the product on mobile devices, it will see its description text and add to cart button. The product page will be opened on second click.', 'woodmart' ),
			'default' => false,
			'required' => array(
				array( 'products_hover', '=', 'base' ),
			)
		),
		array (
			'id'       => 'products_bordered_grid',
			'type'     => 'switch',
			'title'    => esc_html__( 'Bordered grid', 'woodmart' ), 
			'subtitle' => esc_html__( 'Add borders between the products in your grid', 'woodmart' ),
			'default' => false
		),
		array (
			'id'       => 'hover_image',
			'type'     => 'switch',
			'title'    => esc_html__('Hover image', 'woodmart'), 
			'subtitle' => esc_html__('Disable - Enable hover image for products on the shop page.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'base_hover_content',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Hover content', 'woodmart' ),
			'options'  => array(
				'excerpt' => esc_html__( 'Excerpt', 'woodmart' ),
				'additional_info' => esc_html__( 'Additional information', 'woodmart' ),
				'none' => esc_html__( 'None', 'woodmart' ),
			),
			'required' => array(
				array( 'products_hover', 'equals', 'base' ),
			),
			'default' => 'excerpt'
		),
		array (
			'id'       => 'grid_stock_progress_bar',
			'type'     => 'switch',
			'title'    => esc_html__( 'Stock progress bar', 'woodmart' ),
			'subtitle' => esc_html__( 'Display a number of sold and in stock products as a progress bar.', 'woodmart' ),
			'default' => false
		),
		array (
			'id'       => 'shop_countdown',
			'type'     => 'switch',
			'title'    => esc_html__('Countdown timer', 'woodmart'),
			'subtitle' => esc_html__('Show timer for products that have scheduled date for the sale price', 'woodmart'),
			'default' => false
		),
		array (
			'id'       => 'categories_under_title',
			'title'    => esc_html__('Show product category next to title', 'woodmart'),
			'type'     => 'switch',
			'default'  => false
		),
		array (
			'id'       => 'brands_under_title',
			'title'    => esc_html__('Show product brands next to title', 'woodmart'),
			'type'     => 'switch',
			'default'  => false
		),
		array (
			'id'       => 'product_title_lines_limit',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Product title lines limit', 'woodmart' ),
			'options'  => array(
				'one' => esc_html__( 'One line', 'woodmart' ),
				'two' => esc_html__( 'Two line', 'woodmart' ),
				'none' => esc_html__( 'None', 'woodmart' ),
			),
			'default' => 'none'
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Сategories styles', 'woodmart'),
	'id' => 'сategories-styles',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'categories_design',
			'type'     => 'image_select',
			'title'    => esc_html__('Categories design', 'woodmart'),
			'subtitle' => esc_html__('Choose one of those designs for categories', 'woodmart'),
			'options'  => woodmart_get_config( 'categories-designs' ),
			'default' => 'default'
		),
		array (
			'id'       => 'categories_with_shadow',
			'title'    => esc_html__( 'Categories with shadow', 'woodmart' ),
			'type'     => 'button_set',
			'options'  => array(
				'enable' => esc_html__( 'Enable', 'woodmart' ),
				'disable' => esc_html__( 'Disable', 'woodmart' ),
			),
			'default'  => 'enable',
			'required' => array(
				array( 'categories_design', 'equals', array( 'alt', 'default' ) ),
			),
		),
		array (
			'id'       => 'hide_categories_product_count',
			'title'    => esc_html__( 'Hide product count on category', 'woodmart' ),
			'type'     => 'switch',
			'default'  => false
		),
	),
) );


Redux::setSection( $opt_name, array(
	'title' => esc_html__('Sidebar & Page title', 'woodmart'),
	'id' => 'shop-layout',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'shop_layout',
			'type'     => 'image_select',
			'title'    => esc_html__('Shop Layout', 'woodmart'),
			'subtitle' => esc_html__('Select main content and sidebar alignment for shop pages.', 'woodmart'),
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
			'default' => 'sidebar-left'
		),
		array (
			'id'       => 'shop_sidebar_width',
			'type'     => 'button_set',
			'title'    => esc_html__('Sidebar size', 'woodmart'),
			'subtitle' => esc_html__('You can set different sizes for your shop pages sidebar', 'woodmart'),
			'options'  => array(
				2 => esc_html__( 'Small', 'woodmart'),
				3 => esc_html__( 'Medium', 'woodmart'),
				4 => esc_html__( 'Large', 'woodmart'),
			),
			'default' => 3,
			'required' => array(
					array('shop_layout','!=','full-width'),
			)
		),
		array (
			'id'       => 'shop_hide_sidebar',
			'type'     => 'switch',
			'title'    => esc_html__('Off canvas sidebar for mobile', 'woodmart'),
			'subtitle' => esc_html__('You can can hide sidebar and show nicely on button click on the shop page.', 'woodmart'),
			'default' => true,
			'required' => array(
					array('shop_layout','!=','full-width'),
			)
		),
		array (
			'id'       => 'shop_hide_sidebar_tablet',
			'type'     => 'switch',
			'title'    => esc_html__('Off canvas sidebar for tablet', 'woodmart'),
			'subtitle' => esc_html__('You can can hide sidebar and show nicely on button click on the shop page.', 'woodmart'),
			'default' => true,
			'required' => array(
					array('shop_layout','!=','full-width'),
			)
		),
		array (
			'id'       => 'shop_hide_sidebar_desktop',
			'type'     => 'switch',
			'title'    => esc_html__('Off canvas sidebar for desktop', 'woodmart'),
			'subtitle' => esc_html__('You can can hide sidebar and show nicely on button click on the shop page.', 'woodmart'),
			'default' => false,
			'required' => array(
					array('shop_layout','!=','full-width'),
			)
		),
		array (
			'id'       => 'sticky_filter_button',
			'type'     => 'switch',
			'title'    => esc_html__( 'Sticky off canvas sidebar button', 'woodmart' ),
			'subtitle' => esc_html__( 'Display the filters button fixed on the screen for mobile and tablet devices.', 'woodmart' ),
			'default' => false,
			'class'   => 'without-border'
		),
		array (
			'id' => 'shop_page_title_option',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Shop page title options', 'woodmart' ),
		),
		array (
			'id'       => 'shop_title',
			'type'     => 'switch',
			'title'    => esc_html__('Shop title', 'woodmart'),
			'subtitle' => esc_html__('Show title for shop page, product categories or tags.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'shop_categories',
			'type'     => 'switch',
			'title'    => esc_html__('Categories in page title', 'woodmart'),
			'subtitle' => esc_html__('This categories menu is generated automatically based on all categories in the shop. You are not able to manage this menu as other WordPress menus.', 'woodmart'),
			'default' => 1
		),
		array (
			'id'       => 'shop_categories_ancestors',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show current category ancestors', 'woodmart' ),
			'subtitle'     => esc_html__( 'If you visit category Man, for example, only man\'s subcategories will be shown in the page title like T-shirts, Coats, Shoes etc.', 'woodmart' ),
			'default' => 0,
			'required' => array(
				array( 'shop_categories', 'equals', true ),
			)
		),
		array (
			'id'       => 'show_categories_neighbors',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show category neighbors if there is no children', 'woodmart' ),
			'subtitle'     => esc_html__( 'If the category you visit doesn\'t contain any subcategories, the page title menu will display this category\'s neighbors categories.', 'woodmart' ),
			'default' => 0,
			'required' => array(
				array( 'shop_categories_ancestors', 'equals', true ),
			)
		),
		array (
			'id'       => 'shop_products_count',
			'type'     => 'switch',
			'title'    => esc_html__('Show products count for each category', 'woodmart'),
			'default' => 1,
			'required' => array(
					array('shop_categories','equals',true),
			)
		)
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Attribute swatches', 'woodmart'),
	'id' => 'shop-swatches',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'grid_swatches_attribute',
			'type'     => 'select',
			'title'    => esc_html__('Grid swatch attribute to display', 'woodmart'),
			'subtitle' => esc_html__('Choose attribute that will be shown on products grid', 'woodmart'),
			'data'     => 'taxonomy',
		),
		array (
			'id'       => 'swatches_use_variation_images',
			'type'     => 'switch',
			'title'    => esc_html__('Use images from product variations', 'woodmart'),
			'subtitle' => esc_html__('If enabled swatches buttons will be filled with images choosed for product variations and not with images uploaded to attribute terms.', 'woodmart'),
			'default' => false
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Brands', 'woodmart'),
	'id' => 'shop-brand',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'brands_attribute',
			'type'     => 'select',
			'title'    => esc_html__('Brand attribute', 'woodmart'),
			'subtitle' => esc_html__('If you want to show brand image on your product page select desired attribute here', 'woodmart'),
			'data'  => 'taxonomy',
			'default' => 'pa_brand',
			'class'   => 'without-border'
		),
		array (
			'id' => 'brand_single_page_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Brand on the single product page', 'woodmart' ),
		),
		array (
			'id'       => 'product_page_brand',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show brand on the single product page', 'woodmart' ),
			'subtitle' => esc_html__( 'You can disable/enable product\'s brand image on the single page.', 'woodmart' ),
			'default'  => true
		),
		array (
			'id'       => 'product_brand_location',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Brand position on the product page', 'woodmart' ),
			'subtitle' => esc_html__( 'Select a position of the brand image on the single product page.', 'woodmart' ),
			'options'  => array(
				'about_title' => esc_html__( 'Above product title', 'woodmart' ),
				'sidebar' => esc_html__(' Sidebar', 'woodmart' ),
			),
			'required' => array(
				array( 'product_page_brand', 'equals', true ),
			),
			'default' => 'about_title'
		),
		array (
			'id'       => 'brand_tab',
			'type'     => 'switch',
			'title'    => esc_html__('Show tab with brand information', 'woodmart'),
			'subtitle' => esc_html__('If enabled you will see additional tab with brand description on the single product page. Text will be taken from "Description" field for each brand (attribute term).', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'brand_tab_name',
			'type'     => 'switch',
			'title'    => esc_html__( 'Use brand name for tab title', 'woodmart' ),
			'subtitle' => esc_html__( 'If you enable this option, the tab with brand\'s information will be called like "About Nike".', 'woodmart' ),
			'default'  => false
		),
	),
) );

	Redux::setSection( $opt_name, array(
	'title' => 'Quick view',
	'id' => 'shop-quick-view',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'quick_view',
			'type'     => 'switch',
			'title'    => esc_html__( 'Quick View', 'woodmart' ),
			'subtitle' => esc_html__( 'Enable Quick view option. Ability to see the product information with AJAX.', 'woodmart' ),
			'default' => true
		),
		array (
			'id'       => 'quick_view_variable',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show variations on quick view', 'woodmart' ),
			'subtitle' => esc_html__( 'Enable Quick view option for variable products. Will allow your users to purchase variable products directly from the quick view.', 'woodmart' ),
			'default' => true,
			'required' => array(
				array( 'quick_view', 'equals', true ),
			)
		),
		array (
			'id'       => 'quick_view_layout',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Quick view layout', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose between horizontal and vertical layouts for the quick view window.', 'woodmart' ),
			'options'  => array(
				'horizontal' => array(
					'title' => esc_html__( 'Horizontal', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/quick-view-layout/horizontal.jpg'
				),
				'vertical' => array(
					'title' => esc_html__( 'Vertical', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/quick-view-layout/vertical.jpg'
				),
			),
			'required' => array(
				array( 'quick_view', 'equals', true ),
			),
			'default' => 'horizontal',
		),
		array (
			'id'        => 'quickview_width',
			'type'      => 'slider',
			'title'     => esc_html__( 'Quick view width', 'woodmart' ),
			'subtitle'  => esc_html__( 'Set width of the quick view in pixels.', 'woodmart' ),
			'default'   => 920,
			'min'       => 400,
			'step'      => 10,
			'max'       => 1200,
			'required' => array(
				array( 'quick_view', 'equals', true ),
			),
			'display_value' => 'label',
		), 
	),
) );


Redux::setSection( $opt_name, array(
	'title' => 'Compare',
	'id' => 'shop-compare',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'compare',
			'type'     => 'switch',
			'title'    => esc_html__( 'Enable compare', 'woodmart' ),
			'subtitle' => esc_html__( 'Enable compare functionality built in with the theme. Read more information in our documentation.', 'woodmart' ),
			'default'  => true
		),
		array(
			'id'       => 'compare_page',
			'type'     => 'select',
			'multi'    => false,
			'data'     => 'posts',
			'args'     => array( 'post_type' =>  array( 'page' ), 'numberposts' => -1 ),
			'title'    => __( 'Compare page', 'woodmart' ),
			'subtitle' => __( 'Select a page for compare table. It should contain the shortcode shortcode: [woodmart_compare]', 'woodmart' ),
		),
		array (
			'id'       => 'compare_on_grid',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show button on product grid', 'woodmart' ),
			'subtitle' => esc_html__( 'Display compare product button on all products grids and lists.', 'woodmart' ),
			'default'  => true
		),
		array (
			'id'       => 'fields_compare',
			'type'     => 'sorter',
			'title'    => esc_html__( 'Select fields for compare table', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose which fields should be presented on the product compare page with table.', 'woodmart' ),
			'options'  => woodmart_compare_available_fields()
		),
		array (
			'id'       => 'empty_compare_text',
			'type'     => 'textarea',
			'title'    => esc_html__('Empty compare text', 'woodmart'),
			'subtitle' => esc_html__('Text will be displayed if user don\'t add any products to compare', 'woodmart'),      
			'default'  => 'No products added in the compare list. You must add some products to compare them.<br> You will find a lot of interesting products on our "Shop" page.',
			'class'   => 'without-border'
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Catalog mode', 'woodmart'),
	'id' => 'shop-catalog',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'catalog_mode',
			'type'     => 'switch',
			'title'    => esc_html__('Enable catalog mode', 'woodmart'),
			'subtitle' => esc_html__('You can hide all "Add to cart" buttons, cart widget, cart and checkout pages. This will allow you to showcase your products as an online catalog without ability to make a purchase.', 'woodmart'),
			'default' => false
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Login to see prices', 'woodmart' ),
	'id' => 'shop-login-prices',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'login_prices',
			'type'     => 'switch',
			'title'    => esc_html__( 'Login to see add to cart and prices', 'woodmart' ),
			'subtitle' => esc_html__( 'You can restrict shopping functions only for logged in customers.', 'woodmart' ),
			'default' => false
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Cookie Law Info', 'woodmart'),
	'id' => 'shop-cookie',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'cookies_info',
			'type'     => 'switch',
			'title'    => esc_html__('Show cookies info', 'woodmart'),
			'subtitle' => esc_html__('Under EU privacy regulations, websites must make it clear to visitors what information about them is being stored. This specifically includes cookies. Turn on this option and user will see info box at the bottom of the page that your web-site is using cookies.', 'woodmart'),
			'default' => false
		),
		array (
			'id'       => 'cookies_text',
			'type'     => 'editor',
			'title'    => esc_html__('Popup text', 'woodmart'),
			'subtitle' => esc_html__('Place here some information about cookies usage that will be shown in the popup.', 'woodmart'),
			'default' => esc_html__('We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.', 'woodmart'),
		),
		array (
			'id'       => 'cookies_policy_page',
			'type'     => 'select',
			'title'    => esc_html__('Page with details', 'woodmart'),
			'subtitle' => esc_html__('Choose page that will contain detailed information about your Privacy Policy', 'woodmart'),
			'data'     => 'pages'
		),
		array (
			'id'       => 'cookies_version',
			'type'     => 'text',
			'title'    => esc_html__('Cookies version', 'woodmart'),
			'subtitle' => esc_html__('If you change your cookie policy information you can increase their version to show the popup to all visitors again.', 'woodmart'),
			'default' => 1,
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Promo popup', 'woodmart'),
	'id' => 'shop-popup',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'promo_popup',
			'type'     => 'switch',
			'title'    => esc_html__('Enable promo popup', 'woodmart'),
			'subtitle' => esc_html__('Show promo popup to users when they enter the site.', 'woodmart'),
			'default' => 0
		),
		array (
			'id'       => 'popup_text',
			'type'     => 'editor',
			'title'    => esc_html__('Promo popup text', 'woodmart'),
			'subtitle' => esc_html__('Place here some promo text or use HTML block and place here it\'s shortcode', 'woodmart'),
			'default' => ''
		),
		array (
			'id'       => 'popup_event',
			'type'     => 'button_set',
			'title'    => esc_html__('Show popup after', 'woodmart'),
			'options'  => array(
				'time' => esc_html__('some time', 'woodmart'),
				'scroll' => esc_html__('user scroll', 'woodmart'),
			),
			'default' => 'time'
		),
		array (
			'id'       => 'promo_timeout',
			'type'     => 'text',
			'title'    => esc_html__('Popup delay', 'woodmart'),
			'subtitle' => esc_html__('Show popup after some time (in milliseconds)', 'woodmart'),
			'default' => '2000',
			'required' => array(
					array('popup_event','equals', 'time'),
			)
		),
		array (
			'id'       => 'promo_version',
			'type'     => 'text',
			'title'    => esc_html__( 'Popup version', 'woodmart' ),
			'subtitle' => esc_html__( 'If you change your promo popup you can increase its version to show the popup to all visitors again.', 'woodmart' ),
			'default' => 1,
		),
		array (
			'id'        => 'popup_scroll',
			'type'      => 'slider',
			'title'     => esc_html__('Show after user scroll down the page', 'woodmart'),
			'subtitle' => esc_html__('Set the number of pixels users have to scroll down before popup opens', 'woodmart'),
			'default'   => 1000,
			'min'       => 100,
			'step'      => 50,
			'max'       => 5000,
			'display_value' => 'label',
			'required' => array(
					array('popup_event','equals', 'scroll'),
			)
		),
		array (
			'id'        => 'popup_pages',
			'type'      => 'slider',
			'title'     => esc_html__('Show after number of pages visited', 'woodmart'),
			'subtitle' => esc_html__('You can choose how much pages user should change before popup will be shown.', 'woodmart'),
			'default'   => 0,
			'min'       => 0,
			'step'      => 1,
			'max'       => 10,
			'display_value' => 'label'
		),
		array (
			'id'       => 'popup-background',
			'type'     => 'background',
			'title'    => esc_html__('Popup background', 'woodmart'),
			'subtitle' => esc_html__('Set background image or color for promo popup', 'woodmart'),
			$output   => array('.woodmart-promo-popup'),
			'default'  => array(
				'background-color' => '#111111',
				'background-repeat' => 'no-repeat',
				'background-size' => 'contain',
				'background-position' => 'left center',
			)
		),
		array (
			'id'        => 'popup_width',
			'type'      => 'slider',
			'title'     => esc_html__( 'Popup width', 'woodmart' ),
			'subtitle'  => esc_html__( 'Set width of the promo popup in pixels.', 'woodmart' ),
			'default'   => 800,
			'min'       => 400,
			'step'      => 10,
			'max'       => 1000,
			'display_value' => 'label',
		),
		array (
			'id'       => 'promo_popup_hide_mobile',
			'type'     => 'switch',
			'title'    => esc_html__( 'Hide for mobile devices', 'woodmart' ),
			'subtitle' => esc_html__( 'You can disable this option for mobile devices completely.', 'woodmart' ),
			'default'  => 1
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => 'Header banner',
	'id' => 'header-banner',
	'subsection' => true,
	'fields' => array(
		array(
			'id'       => 'header_banner',
			'type'     => 'switch',
			'title'    => esc_html__( 'Header banner', 'woodmart' ),
			'subtitle' => esc_html__( 'Header banner above the header', 'woodmart' ),
			'default'  => false,
		),
		array(
			'id'       => 'header_banner_link',
			'type'     => 'text',
			'title'    => esc_html__( 'Banner link', 'woodmart' ),
			'subtitle' => esc_html__( 'The link will be added to the whole banner area.', 'woodmart' ),
			'tags'     => 'header banner text link'
		),
		array(
			'id'       => 'header_banner_shortcode',
			'type'     => 'editor',
			'title'    => esc_html__( 'Banner content', 'woodmart' ),
			'subtitle' => esc_html__( 'Place here shortcodes you want to see in the banner above the header. You can use shortcodes. Ex.: [social_buttons] or place an HTML Block built with WPBakery Page Builder builder there like [html_block id="258"]', 'woodmart' ),
			'tags'     => 'header banner text content'
		),
		array(
			'id'        => 'header_banner_height',
			'type'      => 'slider',
			'title'     => esc_html__( 'Banner height for desktop', 'woodmart' ),
			'subtitle'  => esc_html__( 'The height for the banner area in pixels on desktop devices.', 'woodmart' ),
			'default'   => 40,
			'min'       => 0,
			'step'      => 1,
			'max'       => 200,
			'display_value' => 'label'
		),
		array(
			'id'        => 'header_banner_mobile_height',
			'type'      => 'slider',
			'title'     => esc_html__( 'Banner height for mobile', 'woodmart' ),
			'subtitle'  => esc_html__( 'The height for the banner area in pixels on mobile devices.', 'woodmart' ),
			'default'   => 40,
			'min'       => 0,
			'step'      => 1,
			'max'       => 200,
			'display_value' => 'label'
		),
		array(
			'id'       => 'header_banner_color',
			'type'     => 'select',
			'title'    => esc_html__( 'Banner text color', 'woodmart' ),
			'subtitle'     => esc_html__( 'Set light or dark text color scheme depending on the banner\'s background color.', 'woodmart' ),
			'options'  => array(
				'dark' => esc_html__( 'Dark', 'woodmart' ), 
				'light' => esc_html__( 'Light', 'woodmart' ),  
			),
			'default' => 'light'
		),
		array(
			'id'       => 'header_banner_bg',
			'type'     => 'background',
			'title'    => esc_html__( 'Banner background', 'woodmart' ),
			$output   => array( '.header-banner' ),
			'tags'     => 'header banner color background'
		),
		array(
			'id'       => 'header_close_btn',
			'type'     => 'switch',
			'title'    => esc_html__( 'Close button', 'woodmart' ),
			'subtitle' => esc_html__( 'Show close banner button', 'woodmart' ),
			'default'  => true,
		),
		array(
			'id'       => 'header_banner_version',
			'type'     => 'text',
			'title'    => esc_html__( 'Banner version', 'woodmart' ),
			'subtitle' => esc_html__( 'If you change your banner you can increase their version to show the banner to all visitors again.', 'woodmart' ),
			'default' => 1,
			'required' => array(
				array( 'header_close_btn', 'equals', true ),
			),
		)
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Widgets', 'woodmart'),
	'id' => 'shop-widgets',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'categories_toggle',
			'type'     => 'switch',
			'title'    => esc_html__('Toggle function for categories widget', 'woodmart'),
			'subtitle' => esc_html__('Turn it on to enable accordion JS for the WooCommerce Product Categories widget. Useful if you have a lot of categories and subcategories.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'widgets_scroll',
			'type'     => 'switch',
			'title'    => esc_html__('Scroll for filters widgets', 'woodmart'),
			'subtitle' => esc_html__('You can limit your Layered Navigation widgets by height and enable nice scroll for them. Useful if you have a lot of product colors/sizes or other attributes for filters.', 'woodmart'),
			'default' => true
		),
		array(
			'id'        => 'widget_heights',
			'type'      => 'slider',
			'title'     => esc_html__( 'Height for filters widgets', 'woodmart' ),
			'subtitle'  => esc_html__( 'Set widgets height in pixels.', 'woodmart' ),
			'default'   => 280,
			'min'       => 100,
			'step'      => 1,
			'max'       => 800,
			'display_value' => 'label',
			'required' => array(
				array( 'widgets_scroll', 'equals', true ),
			)
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Product labels', 'woodmart'),
	'id' => 'shop-labels',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'label_shape',
			'type'     => 'image_select',
			'title'    => esc_html__('Label shape', 'woodmart'),
			'options'  => array(
				'rounded' => array(
					'title' => esc_html__( 'Rounded', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/product-label/rounded.jpg'
				),
				'rectangular' => array(
					'title' => esc_html__( 'Rectangular', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/product-label/rectangular.jpg'
				),
			),
			'default' => 'rounded'
		),
		array (
			'id'       => 'percentage_label',
			'type'     => 'switch',
			'title'    => esc_html__('Shop sale label in percentage', 'woodmart'),
			'subtitle' => esc_html__('Works with Simple, Variable and External products only.', 'woodmart'),
			'default' => true
		),
		array (
			'id'       => 'new_label',
			'type'     => 'switch',
			'title'    => esc_html__( '"New" label on products', 'woodmart' ),
			'subtitle' => esc_html__( 'This label is displayed for products if you enabled this option for particular items.', 'woodmart' ),
			'default' => true
		),
		array (
			'id'       => 'hot_label',
			'type'     => 'switch',
			'title'    => esc_html__('"Hot" label on products', 'woodmart'),
			'subtitle' => esc_html__('Your products marked as "Featured" will have a badge with "Hot" label.', 'woodmart'),
			'default' => true
		)
	),
) );
