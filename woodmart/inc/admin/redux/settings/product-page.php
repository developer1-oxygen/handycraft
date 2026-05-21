<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Product Page', 'woodmart'),
	'id' => 'product_page',
	'icon' => 'el-icon-tags',
	'fields' => array (
		array (
			'id'       => 'single_product_layout',
			'type'     => 'image_select',
			'title'    => esc_html__('Single Product Sidebar', 'woodmart'),
			'subtitle' => esc_html__('Select main content and sidebar alignment for single product pages.', 'woodmart'),
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
			'default' => 'full-width'
		),
		array (
			'id'       => 'full_height_sidebar',
			'type'     => 'switch',
			'title'    => esc_html__( 'Full height sidebar', 'woodmart' ),
			'subtitle' => esc_html__( 'If you have a lot of widgets added to the sidebar your single product page layout may look incosistent. Try to enable this option in this situation.', 'woodmart' ),
			'default'  => false,
			'required' => array(
				array( 'single_product_layout', 'not', 'full-width' ),
			)
		),
		array (
			'id'       => 'single_sidebar_width',
			'type'     => 'button_set',
			'title'    => esc_html__('Sidebar size', 'woodmart'),
			'subtitle' => esc_html__('You can set different sizes for your single product pages sidebar', 'woodmart'),
			'options'  => array(
				2 =>  esc_html__('Small', 'woodmart'),
				3 =>  esc_html__('Medium', 'woodmart'),
				4 =>  esc_html__('Large','woodmart')
			),
			'default' => 3
		),
		array (
			'id'       => 'single_full_width',
			'type'     => 'switch',
			'title'    => esc_html__( 'Full width product page', 'woodmart' ),
			'subtitle' => esc_html__( 'Stretch the single product page\'s content.', 'woodmart' ),
			'default'  => false,
		),
		array (
			'id'       => 'single_product_header',
			'type'     => 'select',
			'title'    => esc_html__( 'Single product header', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use different header for your single product page.', 'woodmart' ),
			'options'  => woodmart_get_whb_headers_array( true ),
			'default' => 'none',
			'class'    => 'without-border',
		),
		array (
			'id' => 'single_product_design_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Single product page layout and style', 'woodmart' ),
		),
		array (
			'id'       => 'product_design',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Product page design', 'woodmart' ),
			'subtitle' => esc_html__( 'Choose between different predefined designs', 'woodmart' ),
			'options'  => array(
				'default' => array(
					'title' => esc_html__( 'Default', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/product-page/product-page-default.jpg'
				),
				'alt' => array(
					'title' => esc_html__( 'Centered', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/product-page/product-page-alt.jpg'
				),
			),
			'default' => 'default'
		),
		array (
			'id'       => 'product_sticky',
			'type'     => 'switch',
			'title'    => esc_html__( 'Sticky product', 'woodmart' ),
			'subtitle' => esc_html__( 'If you turn on this option, the section with description will be sticky when you scroll the page. In case when the description is higher than images, the images section will be fixed instead.', 'woodmart' ),
			'default'  => false,
		),
		array (
			'id'       => 'swatches_scroll_top_desktop',
			'type'     => 'switch',
			'title'    => esc_html__( 'Scroll top on variation select [desktop]', 'woodmart' ),
			'subtitle' => esc_html__( 'When you turn on this option and click on some variation with image, the page will be scrolled up to show that variation image in the main product gallery.', 'woodmart' ),
			'default'  => false,
		),
		array (
			'id'       => 'swatches_scroll_top_mobile',
			'type'     => 'switch',
			'title'    => esc_html__( 'Scroll top on variation select [mobile]', 'woodmart' ),
			'subtitle' => esc_html__( 'When you turn on this option and click on some variation with image, the page will be scrolled up to show that variation image in the main product gallery.', 'woodmart' ),
			'default'  => false,
		),
		array (
			'id'       => 'product_summary_shadow',
			'type'     => 'switch',
			'title'    => esc_html__( 'Add shadow to product summary block', 'woodmart' ),
			'subtitle' => esc_html__( 'Useful when you set background color for the single product page to gray for example.', 'woodmart' ),
			'default'  => false,
			'class'    => 'without-border'
		),
		array (
			'id' => 'single_product_option_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Single product page options', 'woodmart' ),
		),
		array (
			'id'       => 'size_guides',
			'type'     => 'switch',
			'title'    => esc_html__( 'Size guides', 'woodmart' ),
			'subtitle' => wp_kses( __( 'Turn on the size guide feature on the website. Read more information about this function in <a href="https://xtemos.com/docs/woodmart/faq-guides/create-size-guide-table/" target="_blank">our documentation</a>.', 'woodmart' ), array( 'a' => array( 'href' => true, 'target' => true, ), 'br' => array(), 'strong' => array() ) ),
			'default' => true
		),
		array (
			'id'       => 'single_ajax_add_to_cart',
			'type'     => 'switch',
			'title'    => esc_html__( 'AJAX Add to cart', 'woodmart' ),
			'subtitle' => esc_html__( 'Turn on the AJAX add to cart option on the single product page. Will not work with plugins that add some custom fields to the add to cart form.', 'woodmart' ),
			'default' => true
		),
		array (
			'id'       => 'single_sticky_add_to_cart',
			'type'     => 'switch',
			'title'    => esc_html__( 'Sticky add to cart', 'woodmart' ),
			'subtitle' => esc_html__( 'Add to cart section will be displayed at the bottom of your screen when you scroll down the page.', 'woodmart' ),
			'default'  => false
		),
		array (
			'id'       => 'mobile_single_sticky_add_to_cart',
			'type'     => 'switch',
			'title'    => esc_html__( 'Sticky add to cart on mobile', 'woodmart' ),
			'subtitle' => esc_html__( 'You can leave this option for desktop only or enable it for all devices sizes.', 'woodmart' ),
			'default'  => false,
			'required' => array(
				array( 'single_sticky_add_to_cart', 'equals', true ),
			)
		),
		array (
			'id'       => 'content_before_add_to_cart',
			'type'     => 'editor',
			'title'    => esc_html__( 'Before "Add to cart button" text area', 'woodmart' ),
			'subtitle' => esc_html__( 'Place any text, HTML or shortcodes here.', 'woodmart' ),
		),
		array (
			'id'       => 'content_after_add_to_cart',
			'type'     => 'editor',
			'title'    => esc_html__( 'After "Add to cart button" text area', 'woodmart' ),
			'subtitle' => esc_html__( 'Place any text, HTML or shortcodes here.', 'woodmart' ),
		),
	),
) );


Redux::setSection( $opt_name, array(
	'title' => esc_html__( 'Images', 'woodmart' ),
	'id' => 'product_page-images',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'single_product_style',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Product image width', 'woodmart' ),
			'subtitle' => esc_html__( 'You can choose different page layout depending on the product image size you need', 'woodmart' ),
			'options'  => array(
				1 => array(
					'title' => esc_html__( 'Small image', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image-width/small.jpg'
				),
				2 => array(
					'title' => esc_html__( 'Medium', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image-width/medium.jpg'
				),
				3 => array(
					'title' => esc_html__( 'Large', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image-width/large.jpg'
				),
				4 => array(
					'title' => esc_html__( 'Full width (container)', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image-width/fw-container.jpg'
				),
				5 => array(
					'title' => esc_html__( 'Full width (window)', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image-width/fw-window.jpg'
				),
			),
			'default' => 2,
			'required' => array(
				array( 'product_design', 'not', 'sticky' ),
			)
		),
		array (
			'id'       => 'thums_position',
			'type'     => 'image_select',
			'title'    => esc_html__('Thumbnails position', 'woodmart'),
			'subtitle' => esc_html__('Use vertical or horizontal position for thumbnails', 'woodmart'),
			'options'  => array(
				'left' => array(
					'title' => esc_html__( 'Left (vertical position)', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/left.jpg'
				),
				'bottom' => array(
					'title' => esc_html__( 'Bottom (horizontal carousel)', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/bottom.jpg'
				),
				'bottom_column' => array(
					'title' => esc_html__( 'Bottom (1 column)', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/bottom_column.jpg'
				),
				'bottom_grid' => array(
					'title' => esc_html__( 'Bottom (2 columns)', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/bottom_grid.jpg'
				),
				'bottom_combined' => array(
					'title' => esc_html__( 'Combined grid', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/bottom_combined.jpg'
				),
				'centered' => array(
					'title' => esc_html__( 'Centered', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/centered.jpg'
				),
				'without' => array(
					'title' => esc_html__( 'Without', 'woodmart' ),
					'img' => WOODMART_ASSETS_IMAGES . '/settings/single-product-image/without.jpg'
				),
			),
			'default' => 'bottom',
			'required' => array(
				array( 'product_design', 'not', 'sticky' ),
			)
		),
		array (
			'id'       => 'variation_gallery',
			'type'     => 'switch',
			'title'    => esc_html__( 'Additional variations images', 'woodmart' ), 
			'subtitle' => esc_html__( 'Add an ability to upload additional images for each variation in variable products.', 'woodmart' ),
			'default'  => true
		),
		array (
			'id'       => 'image_action',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Main image click action', 'woodmart' ), 
			'subtitle' => esc_html__( 'Enable/disable zoom option or switch to photoswipe popup.', 'woodmart' ),
			'options'  => array(
				'zoom' => esc_html__( 'Zoom', 'woodmart' ), 
				'popup' => esc_html__( 'Photoswipe popup', 'woodmart' ), 
				'none' => esc_html__( 'None', 'woodmart' ), 
			),
			'default' => 'zoom',
		),            
		array (
			'id'       => 'photoswipe_icon',
			'type'     => 'switch',
			'title'    => esc_html__('Show "Zoom image" icon', 'woodmart'), 
			'subtitle' => esc_html__('Click to open image in popup and swipe to zoom', 'woodmart'),
			'default'  => true
		),
		array (
			'id'       => 'product_slider_auto_height',
			'type'     => 'switch',
			'title'    => esc_html__( 'Main carousel auto height', 'woodmart' ), 
			'subtitle' => esc_html__( 'Useful when you have product images with different height.', 'woodmart' ),
			'default'  => false
		),
		array (
			'id'       => 'product_images_captions',
			'type'     => 'switch',
			'title'    => esc_html__( 'Images captions on Photo Swipe lightbox', 'woodmart' ),
			'subtitle' => esc_html__( 'Display caption texts below images when you open the photoswipe popup. Captions can be added to your images via the Media library.', 'woodmart' ),
			'default'  => false
		),
	),
) );


Redux::setSection( $opt_name, array(
	'title' => esc_html__('Show / hide elements', 'woodmart'),
	'id' => 'product_page-show-hide',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'single_breadcrumbs_position',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Breadcrumbs position', 'woodmart' ), 
			'subtitle' => esc_html__( 'Set different position for breadcrumbs section on your product\'s page.', 'woodmart' ),
			'options'  => array(
				'default' => esc_html__( 'Default', 'woodmart' ), 
				'below_header' => esc_html__( 'Below header', 'woodmart' ), 
				'summary' => esc_html__( 'Product summary', 'woodmart' ), 
			),
			'default' => 'default',
		), 
		array (
			'id'       => 'products_nav',
			'title'    => esc_html__( 'Products navigation', 'woodmart' ), 
			'subtitle' => esc_html__( 'Display next/previous products navigation.', 'woodmart' ),
			'type'     => 'switch',
			'default'  => true
		),
		array (
			'id'       => 'product_short_description',
			'type'     => 'switch',
			'title'    => esc_html__( 'Short description', 'woodmart' ),
			'subtitle' => esc_html__( 'Enable/disable short description text in the product\'s summary block.', 'woodmart' ),
			'default'  => true
		),
		array (
			'id'       => 'attr_after_short_desc',
			'title'    => esc_html__( 'Show attributes table after short description', 'woodmart' ), 
			'subtitle' => esc_html__( 'You can display attributes table instead of short description.', 'woodmart' ),
			'type'     => 'switch',
			'default'  => false
		),
		array (
			'id'       => 'product_show_meta',
			'type'     => 'button_set',
			'title'    => esc_html__('Show product meta', 'woodmart'),
			'desc' => esc_html__('Categories, tags, SKU', 'woodmart'),
			'options'  => array(
				'add_to_cart' => esc_html__('After "Add to cart" button', 'woodmart'),
				'after_tabs' => esc_html__('After tabs', 'woodmart'),
				'hide' => esc_html__('Hide', 'woodmart'),
			),
			'default' => 'add_to_cart'
		),
		array (
			'id'       => 'single_stock_progress_bar',
			'type'     => 'switch',
			'title'    => esc_html__( 'Stock progress bar', 'woodmart' ),
			'subtitle' => esc_html__( 'Display a number of sold and in stock products as a progress bar.', 'woodmart' ),
			'default' => false
		),
		array (
			'id'       => 'product_countdown',
			'type'     => 'switch',
			'title'    => esc_html__('Countdown timer', 'woodmart'),
			'subtitle' => esc_html__('Show timer for products that have scheduled date for the sale price', 'woodmart'),
			'default' => false
		),
		array (
			'id'       => 'sale_countdown_variable',
			'type'     => 'switch',
			'title'    => esc_html__( 'Countdown for variable products', 'woodmart' ),
			'subtitle' => esc_html__( 'Sale end date will be based on the first variation date of the product.', 'woodmart' ),
			'default' => false
		),
		array (
			'id'       => 'upsells_position',
			'type'     => 'button_set',
			'title'    => esc_html__('Upsells products position', 'woodmart'),
			'subtitle' => esc_html__('If use "Sidebar" be sure that you have enabled it for the product page layout', 'woodmart'),
			'options'  => array(
				'standard' => esc_html__('Standard', 'woodmart'), 
				'sidebar' => esc_html__('Sidebar', 'woodmart'), 
			),
			'default' => 'standard',
			'class'   => 'without-border'
		),
		//Related products
		array (
			'id' => 'related_title',
			'type' => 'woodmart_title',
			'wood-title' => esc_html__( 'Related products options', 'woodmart' ),
		),
		array (
			'id'       => 'related_products',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show related products', 'woodmart' ),
			'subtitle' => esc_html__( 'Related Products is a section that pulls products from your store that share the same tags or categories as the current product.', 'woodmart' ),
			'default' => true
		),
		array (
			'id'       => 'related_product_view',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Related product view', 'woodmart' ),
			'subtitle' => esc_html__( 'You can set different view mode for the related products. These settings will be applied for upsells products as well.', 'woodmart' ),
			'options'  => array(
				'grid' => 'Grid',
				'slider' => 'Slider',
			),
			'default' => 'slider'
		),
		array (
			'id'       => 'related_product_count',
			'type'     => 'text',
			'title'    => esc_html__( 'Related product count', 'woodmart' ), 
			'subtitle' => esc_html__( 'The total number of related products to display.', 'woodmart' ),
			'default'  => 8
		),
		array (
			'id'       => 'related_product_columns',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Related product columns', 'woodmart' ),
			'subtitle' => esc_html__( 'How many products you want to show per row.', 'woodmart' ),
			'options'  => array(
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6'
			),
			'default' => 4
		)
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Share buttons', 'woodmart'),
	'id' => 'product_page-share',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'product_share',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show share buttons', 'woodmart' ),
			'subtitle' => esc_html__( 'Display share buttons icons on the single product page.', 'woodmart' ),
			'default' => true
		),
		array (
			'id'       => 'product_share_type',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Share buttons type', 'woodmart' ),
			'subtitle' => esc_html__( 'You can switch between share and follow buttons on the single product page.', 'woodmart' ),
			'options'  => array(
				'share' => esc_html__( 'Share', 'woodmart' ),
				'follow' => esc_html__( 'Follow', 'woodmart' ),
			),
			'default' => 'share',
			'required' => array(
				array( 'product_share', 'equals', true ),
			)
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title' => esc_html__('Tabs', 'woodmart'),
	'id' => 'product_page-tabs',
	'subsection' => true,
	'fields' => array (
		array (
			'id'       => 'product_tabs_layout',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Tabs layout', 'woodmart' ),
			'subtitle' => esc_html__( 'Select which style for products tabs do you need.', 'woodmart' ),
			'options'  => array(
				'tabs' => esc_html__( 'Tabs', 'woodmart' ),
				'accordion' => esc_html__( 'Accordion', 'woodmart' ),
			),
			'default' => 'tabs'
		),
		array (
			'id'       => 'product_tabs_location',
			'type'     => 'button_set',
			'title'    => esc_html__('Tabs location', 'woodmart'),
			'options'  => array(
				'standard' => esc_html__('Standard', 'woodmart'),
				'summary' => esc_html__('After "Add to cart" button', 'woodmart'),
			),
			'default' => 'standard',
			'required' => array(
					array('product_tabs_layout','equals', array('accordion')),
			)
		),
		array (
			'id'       => 'reviews_location',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Reviews location', 'woodmart' ),
			'subtitle' => esc_html__( 'Option for the location of the reviews form and reviews list section.', 'woodmart' ),
			'options'  => array(
				'tabs' => esc_html__( 'Tabs', 'woodmart' ),
				'separate' => esc_html__( 'Separate section', 'woodmart' ),
			),
			'default' => 'tabs'
		),
		array (
			'id'       => 'hide_tabs_titles',
			'title'    => esc_html__( 'Hide tabs headings', 'woodmart' ),
			'subtitle' => esc_html__( 'Don\'t show duplicated titles for product tabs.', 'woodmart' ),
			'type'     => 'switch',
			'default'  => true
		),
		array (
			'id'       => 'additional_tab_title',
			'type'     => 'text',
			'title'    => esc_html__('Additional tab title', 'woodmart'),
			'subtitle' => esc_html__('Leave empty to disable custom tab', 'woodmart'),
			'default'  => 'Shipping & Delivery'
		),
		array (
			'id'       => 'additional_tab_text',
			'type'     => 'textarea',
			'title'    => esc_html__( 'Additional tab content', 'woodmart' ),
			'subtitle' => esc_html__( 'You can use any text, HTML or shortcodes here.', 'woodmart' ),
			'default'  => ''
		),
	),
) );
