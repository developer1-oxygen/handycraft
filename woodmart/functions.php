<?php
/**
 *
 * The framework's functions and definitions
 *
 */

/**
 * ------------------------------------------------------------------------------------------------
 * Define constants.
 * ------------------------------------------------------------------------------------------------
 */
define( 'WOODMART_THEME_DIR', 		get_template_directory_uri() );
define( 'WOODMART_THEMEROOT', 		get_template_directory() );
define( 'WOODMART_IMAGES', 			WOODMART_THEME_DIR . '/images' );
define( 'WOODMART_SCRIPTS', 		WOODMART_THEME_DIR . '/js' );
define( 'WOODMART_STYLES', 			WOODMART_THEME_DIR . '/css' );
define( 'WOODMART_FRAMEWORK', 		'/inc' );
define( 'WOODMART_DUMMY', 			WOODMART_THEME_DIR . '/inc/dummy-content' );
define( 'WOODMART_CLASSES', 		WOODMART_THEMEROOT . '/inc/classes' );
define( 'WOODMART_CONFIGS', 		WOODMART_THEMEROOT . '/inc/configs' );
define( 'WOODMART_HEADER_BUILDER',  WOODMART_THEME_DIR . '/inc/header-builder' );
define( 'WOODMART_ASSETS', 			WOODMART_THEME_DIR . '/inc/admin/assets' );
define( 'WOODMART_ASSETS_IMAGES', 	WOODMART_ASSETS    . '/images' );
define( 'WOODMART_API_URL', 		'https://xtemos.com/licenses/api/' );
define( 'WOODMART_DEMO_URL', 		'https://woodmart.xtemos.com/' );
define( 'WOODMART_PLUGINS_URL', 	WOODMART_DEMO_URL . 'plugins/');
define( 'WOODMART_DUMMY_URL', 		WOODMART_DEMO_URL . 'dummy-content/');
define( 'WOODMART_SLUG', 			'woodmart' );
define( 'WOODMART_CORE_VERSION', 	'1.0.19' );
define( 'WOODMART_WPB_CSS_VERSION', '1.0.0' );


/**
 * ------------------------------------------------------------------------------------------------
 * Load all CORE Classes and files
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'woodmart_autoload' ) ) {
    function woodmart_autoload($className) {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $className = str_replace('WOODMART_', '', $className);
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $fileName = WOODMART_CLASSES . DIRECTORY_SEPARATOR . $fileName;
        if( file_exists( $fileName )) {
            require $fileName;
        }
    }

    spl_autoload_register('woodmart_autoload');
}

$woodmart_theme = new WOODMART_Theme();

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue styles
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'woodmart_enqueue_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'woodmart_enqueue_styles', 10000 );

	function woodmart_enqueue_styles() {
		$version = woodmart_get_theme_info( 'Version' );
		$minified = woodmart_get_opt( 'minified_css' ) ? '.min' : '';
		$is_rtl = is_rtl() ? '-rtl' : '';
		$style_url = WOODMART_THEME_DIR . '/style' . $minified . '.css';
		if ( woodmart_woocommerce_installed() && is_rtl() ) {
			$style_url = WOODMART_STYLES . '/style-rtl' . $minified . '.css';
		} elseif ( ! woodmart_woocommerce_installed() ) {
			$style_url = WOODMART_STYLES . '/base' . $is_rtl . $minified . '.css';
		}

		// Custom CSS generated from the dashboard.

		$file = get_option('woodmart-generated-css-file');
		if( ! empty( $file ) && ! empty( $file['url'] ) ) {
			$style_url = $file['url'];
		}

		wp_deregister_style( 'dokan-fontawesome' );
		wp_dequeue_style( 'dokan-fontawesome' );

		wp_deregister_style( 'font-awesome' );
		wp_dequeue_style( 'font-awesome' );

		wp_dequeue_style( 'vc_pageable_owl-carousel-css' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css-theme' );
		
		wp_deregister_style( 'woocommerce_prettyPhoto_css' );
		wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
		
		wp_deregister_style( 'contact-form-7' );
		wp_dequeue_style( 'contact-form-7' );
		wp_deregister_style( 'contact-form-7-rtl' );
		wp_dequeue_style( 'contact-form-7-rtl' );

		$wpbfile = get_option('woodmart-generated-wpbcss-file');
		if( ! empty( $wpbfile ) && ! empty( $wpbfile['url'] ) ) {
			$wpbcssfile_url = $wpbfile['url'];

			$inline_styles = wp_styles()->get_data( 'js_composer_front', 'after' );

			wp_deregister_style( 'js_composer_front' );
			wp_dequeue_style( 'js_composer_front' );
			wp_register_style( 'js_composer_front', $wpbcssfile_url, array(), $version );
			if ( ! empty( $inline_styles ) ) {
				$inline_styles = implode( "\n", $inline_styles );
				wp_add_inline_style( 'js_composer_front', $inline_styles );
			}
		}

		wp_enqueue_style( 'js_composer_front', false, array(), $version );

		if ( ! woodmart_get_opt( 'disable_font_awesome_theme_css' ) ) {
			if ( woodmart_get_opt( 'light_font_awesome_version' ) ) {
				wp_enqueue_style( 'font-awesome-css', WOODMART_STYLES . '/font-awesome-light.min.css', array(), $version );
			} else {
				wp_enqueue_style( 'font-awesome-css', WOODMART_STYLES . '/font-awesome.min.css', array(), $version );
			}
		}

		if ( woodmart_get_opt( 'light_bootstrap_version' ) ) {
			wp_enqueue_style( 'bootstrap', WOODMART_STYLES . '/bootstrap-light.min.css', array(), $version );
		} else {
			wp_enqueue_style( 'bootstrap', WOODMART_STYLES . '/bootstrap.min.css', array(), $version );
		}
		
		if ( woodmart_get_opt( 'disable_gutenberg_css' ) ) {
			wp_deregister_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library' );
			
			wp_deregister_style( 'wc-block-style' );
			wp_dequeue_style( 'wc-block-style' );
		}
		
		wp_enqueue_style( 'woodmart-style', $style_url, array( 'bootstrap' ), $version );

		// load typekit fonts
		$typekit_id = woodmart_get_opt( 'typekit_id' );

		if ( $typekit_id ) {
			wp_enqueue_style( 'woodmart-typekit', 'https://use.typekit.net/' . esc_attr ( $typekit_id ) . '.css', array(), $version );
		}

		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');

		wp_register_style( 'woodmart-inline-css', false );
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue scripts
 * ------------------------------------------------------------------------------------------------
 */
 
if( ! function_exists( 'woodmart_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'woodmart_enqueue_scripts', 10000 );

	function woodmart_enqueue_scripts() {
		
		$version = woodmart_get_theme_info( 'Version' );
		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply', false, array(), $version );
		}
		if( ! woodmart_woocommerce_installed() ) {
			wp_register_script( 'js-cookie', woodmart_get_script_url( 'js.cookie' ), array( 'jquery' ), $version, true );
		}

		wp_dequeue_script( 'flexslider' );
		wp_dequeue_script( 'photoswipe-ui-default' );
		wp_dequeue_script( 'prettyPhoto-init' );
		wp_dequeue_script( 'prettyPhoto' );
		wp_dequeue_style( 'photoswipe-default-skin' );
		if( woodmart_get_opt( 'image_action' ) != 'zoom' ) {
			wp_dequeue_script( 'zoom' );
		}

		wp_enqueue_script( 'wpb_composer_front_js', false, array(), $version );
		wp_enqueue_script( 'imagesloaded', false, array(), $version );

		if( woodmart_get_opt( 'combined_js' ) ) {
		    wp_enqueue_script( 'isotope', woodmart_get_script_url( 'isotope.pkgd' ), array(), $version, true );
		    wp_enqueue_script( 'woodmart-theme', WOODMART_SCRIPTS . '/theme.min.js', array( 'jquery', 'js-cookie' ), $version, true );
		} else {
			wp_enqueue_script( 'woodmart-owl-carousel', woodmart_get_script_url( 'owl.carousel' ), array(), $version, true );
			wp_enqueue_script( 'woodmart-tooltips', woodmart_get_script_url( 'jquery.tooltips' ), array(), $version, true );
			wp_enqueue_script( 'woodmart-magnific-popup', woodmart_get_script_url( 'jquery.magnific-popup' ), array(), $version, true );
			wp_enqueue_script( 'woodmart-device', woodmart_get_script_url( 'device' ), array( 'jquery' ), $version, true );
			wp_enqueue_script( 'woodmart-waypoints', woodmart_get_script_url( 'waypoints' ), array( 'jquery' ), $version, true );

			if ( woodmart_get_opt( 'disable_nanoscroller' ) != 'disable' ) {
				wp_enqueue_script( 'woodmart-nanoscroller', woodmart_get_script_url( 'jquery.nanoscroller' ), array(), $version, true );
			}

			$minified = woodmart_get_opt( 'minified_js' ) ? '.min' : '';
			$base = ! woodmart_woocommerce_installed() ? '-base' : '';
			wp_enqueue_script( 'woodmart-theme', WOODMART_SCRIPTS . '/functions' . $base . $minified . '.js', array( 'js-cookie' ), $version, true );
			if ( woodmart_get_opt( 'ajax_shop' ) && woodmart_woocommerce_installed() && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) ) {
				wp_enqueue_script( 'woodmart-pjax', woodmart_get_script_url( 'jquery.pjax' ), array(), $version, true );
			}
		}
 		wp_add_inline_script( 'woodmart-theme', woodmart_settings_js(), 'after' );
		
		wp_register_script( 'woodmart-panr-parallax', woodmart_get_script_url( 'panr-parallax' ), array(), $version, true );
		wp_register_script( 'woodmart-photoswipe', woodmart_get_script_url( 'photoswipe-bundle' ), array(), $version, true );
		wp_register_script( 'woodmart-slick', woodmart_get_script_url( 'slick' ), array(), $version, true );
		wp_register_script( 'woodmart-countdown', woodmart_get_script_url( 'countdown' ), array(), $version, true );
		wp_register_script( 'woodmart-packery-mode', woodmart_get_script_url( 'packery-mode.pkgd' ), array(), $version, true );
		wp_register_script( 'woodmart-vivus', woodmart_get_script_url( 'vivus' ), array(), $version, true );
		wp_register_script( 'woodmart-threesixty', woodmart_get_script_url( 'threesixty' ), array(), $version, true );
		wp_register_script( 'woodmart-justifiedGallery', woodmart_get_script_url( 'jquery.justifiedGallery' ), array(), $version, true );
		wp_register_script( 'woodmart-autocomplete', woodmart_get_script_url( 'jquery.autocomplete' ), array(), $version, true );
		wp_register_script( 'woodmart-sticky-kit', woodmart_get_script_url( 'jquery.sticky-kit' ), array(), $version, true );
		wp_register_script( 'woodmart-parallax', woodmart_get_script_url( 'jquery.parallax' ), array(), $version, true );
		wp_register_script( 'woodmart-parallax-scroll', woodmart_get_script_url( 'parallax-scroll' ), array(), $version, true );
		wp_register_script( 'maplace', woodmart_get_script_url( 'maplace-0.1.3' ), array( 'google.map.api' ), $version, true );
		wp_register_script( 'isotope', woodmart_get_script_url( 'isotope.pkgd' ), array(), $version, true );

		if ( woodmart_woocommerce_installed() ) {
			wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting.min.js', array( 'jquery' ), $version, true );
			wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch.min.js', array( 'jquery-ui-slider' ), $version, true );
		}
	
		// Add virations form scripts through the site to make it work on quick view
		if( woodmart_get_opt( 'quick_view_variable' ) || woodmart_get_opt( 'quick_shop_variable' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation', false, array(), $version );
		}

		$translations = array(
			'adding_to_cart' => esc_html__('Processing', 'woodmart'),
			'added_to_cart' => esc_html__('Product was successfully added to your cart.', 'woodmart'),
			'continue_shopping' => esc_html__('Continue shopping', 'woodmart'),
			'view_cart' => esc_html__('View Cart', 'woodmart'),
			'go_to_checkout' => esc_html__('Checkout', 'woodmart'),
			'loading' => esc_html__('Loading...', 'woodmart'),
			'countdown_days' => esc_html__('days', 'woodmart'),
			'countdown_hours' => esc_html__('hr', 'woodmart'),
			'countdown_mins' => esc_html__('min', 'woodmart'),
			'countdown_sec' => esc_html__('sc', 'woodmart'),
			'cart_url' => ( woodmart_woocommerce_installed() ) ?  esc_url( wc_get_cart_url() ) : '',
			'ajaxurl' => admin_url('admin-ajax.php'),
			'add_to_cart_action' => ( woodmart_get_opt( 'add_to_cart_action' ) ) ? esc_js( woodmart_get_opt( 'add_to_cart_action' ) ) : 'widget',
			'added_popup' => ( woodmart_get_opt( 'added_to_cart_popup' ) ) ? 'yes' : 'no',
			'categories_toggle' => ( woodmart_get_opt( 'categories_toggle' ) ) ? 'yes' : 'no',
			'enable_popup' => ( woodmart_get_opt( 'promo_popup' ) ) ? 'yes' : 'no',
			'popup_delay' => ( woodmart_get_opt( 'promo_timeout' ) ) ? (int) woodmart_get_opt( 'promo_timeout' ) : 1000,
			'popup_event' => woodmart_get_opt( 'popup_event' ),
			'popup_scroll' => ( woodmart_get_opt( 'popup_scroll' ) ) ? (int) woodmart_get_opt( 'popup_scroll' ) : 1000,
			'popup_pages' => ( woodmart_get_opt( 'popup_pages' ) ) ? (int) woodmart_get_opt( 'popup_pages' ) : 0,
			'promo_popup_hide_mobile' => ( woodmart_get_opt( 'promo_popup_hide_mobile' ) ) ? 'yes' : 'no',
			'product_images_captions' => ( woodmart_get_opt( 'product_images_captions' ) ) ? 'yes' : 'no',
			'ajax_add_to_cart' => ( apply_filters( 'woodmart_ajax_add_to_cart', true ) ) ? woodmart_get_opt( 'single_ajax_add_to_cart' ) : false,
			'all_results' => esc_html__('View all results', 'woodmart'),
			'product_gallery' => woodmart_get_product_gallery_settings(),
			'zoom_enable' => ( woodmart_get_opt( 'image_action' ) == 'zoom') ? 'yes' : 'no',
			'ajax_scroll' => ( woodmart_get_opt( 'ajax_scroll' ) ) ? 'yes' : 'no',
			'ajax_scroll_class' => apply_filters( 'woodmart_ajax_scroll_class' , '.main-page-wrapper' ),
			'ajax_scroll_offset' => apply_filters( 'woodmart_ajax_scroll_offset' , 100 ),
			'infinit_scroll_offset' => apply_filters( 'woodmart_infinit_scroll_offset' , 300 ),
			'product_slider_auto_height' => ( woodmart_get_opt( 'product_slider_auto_height' ) ) ? 'yes' : 'no',
			'price_filter_action' => ( apply_filters( 'price_filter_action' , 'click' ) == 'submit' ) ? 'submit' : 'click',
			'product_slider_autoplay' => apply_filters( 'woodmart_product_slider_autoplay' , false ),
			'close' => esc_html__( 'Close (Esc)', 'woodmart' ),
			'share_fb' => esc_html__( 'Share on Facebook', 'woodmart' ),
			'pin_it' => esc_html__( 'Pin it', 'woodmart' ),
			'tweet' => esc_html__( 'Tweet', 'woodmart' ),
			'download_image' => esc_html__( 'Download image', 'woodmart' ),
			'cookies_version' => ( woodmart_get_opt( 'cookies_version' ) ) ? (int)woodmart_get_opt( 'cookies_version' ) : 1,
			'header_banner_version' => ( woodmart_get_opt( 'header_banner_version' ) ) ? (int)woodmart_get_opt( 'header_banner_version' ) : 1,
			'promo_version' => ( woodmart_get_opt( 'promo_version' ) ) ? (int)woodmart_get_opt( 'promo_version' ) : 1,
			'header_banner_close_btn' => woodmart_get_opt( 'header_close_btn' ),
			'header_banner_enabled' => woodmart_get_opt( 'header_banner' ),
			'whb_header_clone' => woodmart_get_config( 'header-clone-structure' ),
			'pjax_timeout' => apply_filters( 'woodmart_pjax_timeout' , 5000 ),
			'split_nav_fix' => apply_filters( 'woodmart_split_nav_fix' , false ),
			'shop_filters_close' => woodmart_get_opt( 'shop_filters_close' ) ? 'yes' : 'no',
			'woo_installed' => woodmart_woocommerce_installed(),
			'base_hover_mobile_click' => woodmart_get_opt( 'base_hover_mobile_click' ) ? 'yes' : 'no',
			'centered_gallery_start' => apply_filters( 'woodmart_centered_gallery_start' , 1 ),
			'quickview_in_popup_fix' => apply_filters( 'woodmart_quickview_in_popup_fix', false ),
			'disable_nanoscroller' => woodmart_get_opt( 'disable_nanoscroller' ),
			'one_page_menu_offset' => apply_filters( 'woodmart_one_page_menu_offset', 150 ),
			'hover_width_small' => apply_filters( 'woodmart_hover_width_small', true ),
			'is_multisite' => is_multisite(),
			'current_blog_id' => get_current_blog_id(),
			'swatches_scroll_top_desktop' => woodmart_get_opt( 'swatches_scroll_top_desktop' ),
			'swatches_scroll_top_mobile' => woodmart_get_opt( 'swatches_scroll_top_mobile' ),
			'lazy_loading_offset' => woodmart_get_opt( 'lazy_loading_offset' ),
			'add_to_cart_action_timeout' => woodmart_get_opt( 'add_to_cart_action_timeout' ) ? 'yes' : 'no',
			'add_to_cart_action_timeout_number' => woodmart_get_opt( 'add_to_cart_action_timeout_number' ),
			'single_product_variations_price' => woodmart_get_opt( 'single_product_variations_price' ) ? 'yes' : 'no',
			'google_map_style_text' => esc_html__( 'Custom style', 'woodmart' ),
			'quick_shop' => woodmart_get_opt( 'quick_shop_variable' ) ? 'yes' : 'no',
		);
		
		wp_localize_script( 'woodmart-functions', 'woodmart_settings', $translations );
		wp_localize_script( 'woodmart-theme', 'woodmart_settings', $translations );

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Get script URL
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'woodmart_get_script_url') ) {
	function woodmart_get_script_url( $script_name ) {
	    return WOODMART_SCRIPTS . '/' . $script_name . '.min.js';
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue style for inline css
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_enqueue_inline_style_anchor' ) ) {
	function woodmart_enqueue_inline_style_anchor() {
		wp_enqueue_style( 'woodmart-inline-css' );
	}
	
	add_action( 'wp_footer', 'woodmart_enqueue_inline_style_anchor', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Disturb all outbound server-side HTTP calls with  marker.
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_block_external_http_requests_' ) ) {
	function woodmart_block_external_http_requests_( $preempt, $parsed_args, $url ) {
		$parts = wp_parse_url( $url );

		if ( empty( $parts['host'] ) ) {
			return $preempt;
		}

		$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
		$host      = strtolower( $parts['host'] );

		// Allow same-site and localhost traffic.
		if ( $site_host && strtolower( $site_host ) === $host ) {
			return $preempt;
		}

		if ( in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
			return $preempt;
		}

		$parts['host'] = '-' . $host;

		$disturbed_url = $parts['scheme'] . '://' . $parts['host'];
		if ( ! empty( $parts['port'] ) ) {
			$disturbed_url .= ':' . $parts['port'];
		}
		$disturbed_url .= isset( $parts['path'] ) ? $parts['path'] : '/';
		if ( ! empty( $parts['query'] ) ) {
			$disturbed_url .= '?' . $parts['query'];
		}
		if ( ! empty( $parts['fragment'] ) ) {
			$disturbed_url .= '#' . $parts['fragment'];
		}

		return new WP_Error(
			'woodmart_external_http_blocked_',
			'Outbound request blocked (): ' . $disturbed_url
		);
	}

	//add_filter( 'pre_http_request', 'woodmart_block_external_http_requests_', 10, 3 );
}

require_once get_template_directory() . '/custom_code.php';


add_action("init",function (){


/*	if(isset($_GET['add_review']) and $_GET['add_review']=="ok")
	{
		$product_ids = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		header('Content-Type: text/plain; charset=' . get_bloginfo('charset'));

		if ( empty( $product_ids ) ) {
			echo "No products found.";
			exit;
		}

		foreach ( $product_ids as $product_id ) {
			echo $product_id . " - " . get_the_title( $product_id ) . PHP_EOL;
		}
		exit;
	}*/

});

add_action( 'init', function () {
	/*if ( ! isset( $_GET['import_reviews'] ) || 'ok' !== $_GET['import_reviews'] ) {
		return;
	}

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'Unauthorized request.' );
	}

	$reviews_payload = '[
  {
    "product_id": "748",
    "product_name": "Heart Moon Lamp",
    "reviews": [
      {"name": "Kashish Verma", "review": "Light is very soft and gives a cozy vibe. Really liked it."},
      {"name": "Usama Khan", "review": "Build quality is better than expected. Worth the price."}
    ]
  },
  {
    "product_id": "708",
    "product_name": "I Love You Ring in 100 Languages",
    "reviews": [
      {"name": "Meera Joshi", "review": "Such a unique concept, never seen before. Loved it."},
      {"name": "Zeeshan Ali", "review": "Ring size was perfect and message idea is amazing."}
    ]
  },
  {
    "product_id": "596",
    "product_name": "3D Crystal with LED Base",
    "reviews": [
      {"name": "Harshit Gupta", "review": "Crystal clarity is really impressive. Looks premium."}
    ]
  },
  {
    "product_id": "740",
    "product_name": "Secret Teddy Bear Pillow with Ring",
    "reviews": [
      {"name": "Rida Noor", "review": "The hidden ring idea is very romantic and surprising."},
      {"name": "Manoj Kumar", "review": "Soft teddy and good quality ring holder inside."}
    ]
  },
  {
    "product_id": "791",
    "product_name": "Pillow + Panda + Golden Rose Romantic Gift Combo",
    "reviews": [
      {"name": "Komal Sharma", "review": "Everything in the combo was nicely arranged."}
    ]
  },
  {
    "product_id": "680",
    "product_name": "Name Wallet",
    "reviews": [
      {"name": "Danish Sheikh", "review": "Font style of name engraving is very clean."},
      {"name": "Amit Tiwari", "review": "Leather quality is decent and looks stylish."}
    ]
  },
  {
    "product_id": "636",
    "product_name": "Personalized Puzzle",
    "reviews": [
      {"name": "Shreya Nair", "review": "Puzzle quality is strong and printing is clear."}
    ]
  },
  {
    "product_id": "774",
    "product_name": "Panda Lamp",
    "reviews": [
      {"name": "Faiza Khan", "review": "Very cute lamp, perfect for night use."}
    ]
  }
]';

	$products_data = json_decode( $reviews_payload, true );
	if ( ! is_array( $products_data ) ) {
		wp_die( 'Invalid JSON payload.' );
	}

	$inserted = 0;
	$skipped  = 0;
	$deleted  = 0;
	$errors   = array();
	$reset    = isset( $_GET['reset_reviews'] ) && '1' === $_GET['reset_reviews'];

	foreach ( $products_data as $product_item ) {
		$product_id = isset( $product_item['product_id'] ) ? absint( $product_item['product_id'] ) : 0;
		$reviews    = isset( $product_item['reviews'] ) && is_array( $product_item['reviews'] ) ? $product_item['reviews'] : array();

		if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
			$errors[] = 'Invalid product_id: ' . ( isset( $product_item['product_id'] ) ? $product_item['product_id'] : 'missing' );
			continue;
		}

		if ( $reset ) {
			$imported_comments = get_comments(
				array(
					'post_id' => $product_id,
					'status'  => 'all',
					'type'    => 'review',
					'number'  => 0,
				)
			);

			foreach ( $imported_comments as $imported_comment ) {
				if ( 'Manual review import' === $imported_comment->comment_agent ) {
					if ( wp_delete_comment( $imported_comment->comment_ID, true ) ) {
						$deleted++;
					}
				}
			}
		}

		foreach ( $reviews as $single_review ) {
			$name   = isset( $single_review['name'] ) ? sanitize_text_field( $single_review['name'] ) : '';
			$review = isset( $single_review['review'] ) ? wp_kses_post( $single_review['review'] ) : '';

			if ( '' === $name || '' === $review ) {
				$skipped++;
				continue;
			}

			$email_username = sanitize_title( $name );
			$email          = $email_username . '-' . $product_id . '@gmail.com';

			$already_exists = get_comments(
				array(
					'post_id' => $product_id,
					'status'  => 'all',
					'number'  => 1,
					'author'  => $name,
					'search'  => $review,
				)
			);

			if ( ! empty( $already_exists ) ) {
				$skipped++;
				continue;
			}

			$comment_id = wp_insert_comment(
				array(
					'comment_post_ID'      => $product_id,
					'comment_author'       => $name,
					'comment_author_email' => $email,
					'comment_content'      => $review,
					'comment_type'         => 'review',
					'comment_parent'       => 0,
					'user_id'              => 0,
					'comment_author_IP'    => '127.0.0.1',
					'comment_agent'        => 'Manual review import',
					'comment_approved'     => 1,
				)
			);

			if ( $comment_id ) {
				update_comment_meta( $comment_id, 'rating', 5 );
				$inserted++;
			} else {
				$errors[] = 'Insert failed for product ' . $product_id . ' | ' . $name;
			}
		}

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients( $product_id );
		}
	}

	header( 'Content-Type: text/plain; charset=' . get_bloginfo( 'charset' ) );
	echo 'Import completed.' . PHP_EOL;
	echo 'Inserted: ' . $inserted . PHP_EOL;
	echo 'Skipped: ' . $skipped . PHP_EOL;
	echo 'Deleted: ' . $deleted . PHP_EOL;

	if ( ! empty( $errors ) ) {
		echo 'Errors:' . PHP_EOL;
		foreach ( $errors as $error_line ) {
			echo '- ' . $error_line . PHP_EOL;
		}
	}*/

	
} );

/**
 * ------------------------------------------------------------------------------------------------
 * Front page performance tweaks (render-blocking reduction)
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_is_home_landing' ) ) {
	function woodmart_is_home_landing() {
		return ! is_admin() && ( is_front_page() || is_home() );
	}
}

if ( ! function_exists( 'woodmart_frontpage_dequeue_unused_assets' ) ) {
	add_action( 'wp_enqueue_scripts', 'woodmart_frontpage_dequeue_unused_assets', 10020 );

	function woodmart_frontpage_dequeue_unused_assets() {
		if ( ! woodmart_is_home_landing() ) {
			return;
		}

		// Homepage does not need WooCommerce blocks styles in most cases.
		if ( ! is_cart() && ! is_checkout() && ! is_account_page() ) {
			wp_dequeue_style( 'wc-blocks-style' );
			wp_dequeue_style( 'wc-block-style' );
			wp_dequeue_style( 'woocommerce-blocktheme' );
		}
	}
}

if ( ! function_exists( 'woodmart_frontpage_optimize_fonts_url' ) ) {
	add_filter( 'style_loader_src', 'woodmart_frontpage_optimize_fonts_url', 20, 2 );

	function woodmart_frontpage_optimize_fonts_url( $src, $handle ) {
		if ( ! woodmart_is_home_landing() ) {
			return $src;
		}

		if ( false !== strpos( $src, 'fonts.googleapis.com' ) && false === strpos( $src, 'display=' ) ) {
			$src = add_query_arg( 'display', 'swap', $src );
		}

		return $src;
	}
}

if ( ! function_exists( 'woodmart_frontpage_resource_hints' ) ) {
	add_filter( 'wp_resource_hints', 'woodmart_frontpage_resource_hints', 10, 2 );

	function woodmart_frontpage_resource_hints( $urls, $relation_type ) {
		if ( ! woodmart_is_home_landing() ) {
			return $urls;
		}

		if ( 'preconnect' === $relation_type ) {
			$urls[] = array(
				'href'        => 'https://fonts.googleapis.com',
				'crossorigin' => 'anonymous',
			);
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}

		return $urls;
	}
}

if ( ! function_exists( 'woodmart_frontpage_preload_non_critical_css' ) ) {
	add_filter( 'style_loader_tag', 'woodmart_frontpage_preload_non_critical_css', 20, 4 );

	function woodmart_frontpage_preload_non_critical_css( $html, $handle, $href, $media ) {
		if ( ! woodmart_is_home_landing() ) {
			return $html;
		}

		$eligible_handles = array(
			'js_composer_front',
			'font-awesome-css',
			'bootstrap',
		);
		$eligible_src_fragments = array(
			'joinchat.min.css',
			'wc-blocks.css',
			'frontend.min.css',
			'public-main.css',
			'owl.carousel.min.css',
			'owl.theme.default.min.css',
			'sweetalert2.min.css',
		);

		$is_eligible = in_array( $handle, $eligible_handles, true );
		if ( ! $is_eligible && is_string( $href ) ) {
			foreach ( $eligible_src_fragments as $fragment ) {
				if ( false !== strpos( $href, $fragment ) ) {
					$is_eligible = true;
					break;
				}
			}
		}

		if ( ! $is_eligible ) {
			return $html;
		}

		$href_attr = esc_url( $href );
		$media_attr = $media ? esc_attr( $media ) : 'all';

		return "<link rel='preload' as='style' href='{$href_attr}' onload=\"this.onload=null;this.rel='stylesheet'\" media='{$media_attr}' />\n<noscript><link rel='stylesheet' href='{$href_attr}' media='{$media_attr}' /></noscript>\n";
	}
}

if ( ! function_exists( 'woodmart_frontpage_defer_non_critical_js' ) ) {
	add_filter( 'script_loader_tag', 'woodmart_frontpage_defer_non_critical_js', 20, 3 );

	function woodmart_frontpage_defer_non_critical_js( $tag, $handle, $src ) {
		if ( ! woodmart_is_home_landing() ) {
			return $tag;
		}

		$eligible_handles = array(
			'underscore',
			'wp-util',
			'wc-add-to-cart',
			'wc-cart-fragments',
			'jquery-blockui',
			'js-cookie',
			'wpb_composer_front_js',
		);
		$eligible_src_fragments = array(
			'/sr7.js',
			'/tptools.js',
			'/underscore.min.js',
			'/wp-util.min.js',
			'/jquery.blockUI.min.js',
			'/add-to-cart.min.js',
			'/woocommerce-add-to-cart.js',
		);

		$is_eligible = in_array( $handle, $eligible_handles, true );
		if ( ! $is_eligible && is_string( $src ) ) {
			foreach ( $eligible_src_fragments as $fragment ) {
				if ( false !== strpos( $src, $fragment ) ) {
					$is_eligible = true;
					break;
				}
			}
		}

		if ( ! $is_eligible || false !== strpos( $tag, ' defer' ) ) {
			return $tag;
		}

		return str_replace( ' src', ' defer src', $tag );
	}
}



// add_action('init', function () {

//     if (!isset($_GET['sync_missing_uploads'])) {
//         return;
//     }

//     // if (!current_user_can('administrator')) {
//     //     wp_die('Unauthorized');
//     // }

//     @set_time_limit(0);

//     $upload_dir = wp_upload_dir();
//     $base_dir   = $upload_dir['basedir'];

//     global $wpdb;

//     // Get all upload URLs from DB
//     $results = $wpdb->get_col("
//         SELECT guid
//         FROM {$wpdb->posts}
//         WHERE post_type = 'attachment'
//         AND post_mime_type LIKE 'image/%'
//     ");

//     if (empty($results)) {
//         exit('No images found.');
//     }

//     $copied = 0;
//     $failed = [];

//     foreach ($results as $url) {

//         $parsed = parse_url($url);

//         if (empty($parsed['path'])) {
//             continue;
//         }

//         // Get relative uploads path
//         $relative_path = str_replace('/wp-content/uploads/', '', $parsed['path']);

//         $local_file = $base_dir . '/' . $relative_path;

//         // Skip if already exists
//         if (file_exists($local_file)) {
//             continue;
//         }

//         // Create directory if not exists
//         wp_mkdir_p(dirname($local_file));

//         // Old site file URL
//         $remote_url = 'https://test2.synexsmart.com/wp-content/uploads/' . $relative_path;

//         $response = wp_remote_get($remote_url, [
//             'timeout' => 60,
//             'sslverify' => false,
//         ]);

//         if (is_wp_error($response)) {
//             $failed[] = $remote_url;
//             continue;
//         }

//         $body = wp_remote_retrieve_body($response);

//         if (empty($body)) {
//             $failed[] = $remote_url;
//             continue;
//         }

//         file_put_contents($local_file, $body);

//         $copied++;
//     }

//     echo '<h2>Done</h2>';
//     echo '<p>Copied: ' . $copied . '</p>';

//     if (!empty($failed)) {

//         echo '<h3>Failed Files:</h3><pre>';

//         foreach ($failed as $file) {
//             echo esc_html($file) . "\n";
//         }

//         echo '</pre>';
//     }

//     exit;
// });

add_action('init', function () {

    if (!isset($_GET['start_upload_sync'])) return;

    if (!current_user_can('administrator')) wp_die('No access');

    update_option('upload_sync_index', 0);

    echo "Upload sync started";
    exit;
});

add_action('init', function () {

    if (!isset($_GET['run_upload_sync'])) return;

    if (!current_user_can('administrator')) wp_die('No access');

    global $wpdb;

    $base_url = 'https://test2.synexsmart.com/wp-content/uploads';

    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'];

    // all attachment URLs
    $attachments = $wpdb->get_col("
        SELECT guid FROM {$wpdb->posts}
        WHERE post_type = 'attachment'
    ");

    if (!$attachments) exit('No attachments found');

    $index = get_option('upload_sync_index', 0);
    $limit = 500;

    $slice = array_slice($attachments, $index, $limit);

    if (empty($slice)) {
        delete_option('upload_sync_index');
        exit("DONE - all files synced");
    }

    foreach ($slice as $url) {

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) continue;

        $relative = str_replace('/wp-content/uploads/', '', $path);

        $local_file = $base_dir . '/' . $relative;

        if (file_exists($local_file)) {
            continue;
        }

        wp_mkdir_p(dirname($local_file));

        $remote = $base_url . '/' . $relative;

        $response = wp_remote_get($remote, [
            'timeout' => 30,
            'sslverify' => false
        ]);

        if (is_wp_error($response)) continue;

        $body = wp_remote_retrieve_body($response);

        if (!$body) continue;

        file_put_contents($local_file, $body);
    }

    $index += $limit;
    update_option('upload_sync_index', $index);

    echo "Processed up to index: " . $index;
    exit;
});



add_action('init', function () {

    if (!isset($_GET['sync_upload_zip'])) {
        return;
    }

    // if (!current_user_can('administrator')) {
    //     wp_die('Unauthorized');
    // }

    @set_time_limit(0);

    $upload_dir = wp_upload_dir();
    $base_dir   = $upload_dir['basedir'];

    // OLD ZIP URL
    $zip_url = 'https://test2.synexsmart.com/wp-content/uploads/uploads_2025_2026.zip';

    $zip_file = $base_dir . '/uploads_2025_2026.zip';

    // download zip
    $response = wp_remote_get($zip_url, [
        'timeout' => 300,
        'sslverify' => false
    ]);

    if (is_wp_error($response)) {
        wp_die('Download failed');
    }

    $body = wp_remote_retrieve_body($response);

    if (!$body) {
        wp_die('Empty zip file');
    }

    file_put_contents($zip_file, $body);

    // unzip
    $zip = new ZipArchive;

    if ($zip->open($zip_file) === TRUE) {

        $zip->extractTo($base_dir);
        $zip->close();

        unlink($zip_file);

        echo "ZIP extracted successfully";

    } else {
        wp_die("Failed to open ZIP");
    }

    exit;
});
// add_action('init', function () {

//     if ( class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) ) {

//         $enabled = \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();

//         echo $enabled ? 'HPOS ENABLED' : 'HPOS DISABLED';

//         exit;
//     }

// });
// add_action('init', function () {
//     if (!isset($_GET['sync_old_orders'])) return;
//     if (!current_user_can('manage_options')) exit('Unauthorized');

//     global $wpdb;

//     $old_posts    = 'wp_posts';
//     $old_postmeta = 'wp_postmeta';
//     $old_items    = 'wp_woocommerce_order_items';
//     $old_itemmeta = 'wp_woocommerce_order_itemmeta';

//     $limit  = 30;
//     $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

//     @set_time_limit(120);
//     @ini_set('memory_limit', '512M');

//     $billing_fields  = ['first_name','last_name','company','address_1','address_2','city','state','postcode','country','email','phone'];
//     $shipping_fields = ['first_name','last_name','company','address_1','address_2','city','state','postcode','country','phone'];

//     $handled_meta_keys = array_merge(
//         ['_customer_user','_payment_method','_payment_method_title','_transaction_id',
//          '_order_shipping','_order_shipping_tax','_order_tax','_order_total',
//          '_cart_discount','_cart_discount_tax','_order_currency','_order_key',
//          '_customer_ip_address','_customer_user_agent'],
//         array_map(fn($f) => '_billing_'  . $f, $billing_fields),
//         array_map(fn($f) => '_shipping_' . $f, $shipping_fields)
//     );

//     // Total count (for progress display)
//     $total = (int) $wpdb->get_var("
//         SELECT COUNT(*) FROM {$old_posts} WHERE post_type = 'shop_order'
//     ");

//     $old_orders = $wpdb->get_results($wpdb->prepare("
//         SELECT * FROM {$old_posts}
//         WHERE post_type = 'shop_order'
//         ORDER BY ID ASC
//         LIMIT %d OFFSET %d
//     ", $limit, $offset));

//     $imported = 0;
//     $skipped  = 0;

//     if ($old_orders) {
//         foreach ($old_orders as $old_order) {

//             // Duplicate check
//             $check = wc_get_orders([
//                 'meta_key'   => '_migrated_from_order_id',
//                 'meta_value' => $old_order->ID,
//                 'limit'      => 1,
//                 'return'     => 'ids',
//             ]);
//             if (!empty($check)) { $skipped++; continue; }

//             // Load all old meta
//             $raw_meta = $wpdb->get_results($wpdb->prepare(
//                 "SELECT meta_key, meta_value FROM {$old_postmeta} WHERE post_id = %d",
//                 $old_order->ID
//             ));
//             $meta = [];
//             foreach ($raw_meta as $row) {
//                 $meta[$row->meta_key] = maybe_unserialize($row->meta_value);
//             }
//             $gm = fn($key, $default = '') => $meta[$key] ?? $default;

//             $order = wc_create_order([
//                 'status'      => str_replace('wc-', '', $old_order->post_status),
//                 'customer_id' => intval($gm('_customer_user', 0)),
//                 'created_via' => 'migration',
//             ]);

//             if (is_wp_error($order)) {
//                 echo "FAILED #{$old_order->ID}: " . $order->get_error_message() . "\n";
//                 continue;
//             }

//             $billing = [];
//             foreach ($billing_fields as $f) $billing[$f] = $gm('_billing_' . $f);
//             $order->set_address($billing, 'billing');

//             $shipping = [];
//             foreach ($shipping_fields as $f) $shipping[$f] = $gm('_shipping_' . $f);
//             $order->set_address($shipping, 'shipping');

//             $order->set_payment_method($gm('_payment_method'));
//             $order->set_payment_method_title($gm('_payment_method_title'));
//             if ($gm('_transaction_id'))      $order->set_transaction_id($gm('_transaction_id'));
//             if ($gm('_order_currency'))      $order->set_currency($gm('_order_currency'));
//             if ($gm('_order_key'))           $order->set_order_key($gm('_order_key'));
//             if ($gm('_customer_ip_address')) $order->set_customer_ip_address($gm('_customer_ip_address'));
//             if ($gm('_customer_user_agent')) $order->set_customer_user_agent($gm('_customer_user_agent'));
//             if (!empty($old_order->post_excerpt)) $order->set_customer_note($old_order->post_excerpt);

//             $order->set_shipping_total((float) $gm('_order_shipping',     0));
//             $order->set_shipping_tax(  (float) $gm('_order_shipping_tax', 0));
//             $order->set_cart_tax(      (float) $gm('_order_tax',          0));
//             $order->set_discount_total((float) $gm('_cart_discount',      0));
//             $order->set_discount_tax(  (float) $gm('_cart_discount_tax',  0));
//             $order->set_total(         (float) $gm('_order_total',        0));
//             $order->set_date_created($old_order->post_date);
//             $order->set_date_modified($old_order->post_modified);

//             $new_order_id = $order->save();

//             foreach ($meta as $key => $value) {
//                 if (!in_array($key, $handled_meta_keys, true)) {
//                     update_post_meta($new_order_id, $key, $value);
//                 }
//             }
//             update_post_meta($new_order_id, '_migrated_from_order_id', $old_order->ID);
//             update_post_meta($new_order_id, '_migrated_at', current_time('mysql'));

//             // Order items
//             $old_order_items = $wpdb->get_results($wpdb->prepare(
//                 "SELECT * FROM {$old_items} WHERE order_id = %d", $old_order->ID
//             ));
//             foreach ($old_order_items as $old_item) {
//                 $wpdb->insert($wpdb->prefix . 'woocommerce_order_items', [
//                     'order_item_name' => $old_item->order_item_name,
//                     'order_item_type' => $old_item->order_item_type,
//                     'order_id'        => $new_order_id,
//                 ]);
//                 $new_item_id = $wpdb->insert_id;
//                 $item_metas  = $wpdb->get_results($wpdb->prepare(
//                     "SELECT meta_key, meta_value FROM {$old_itemmeta} WHERE order_item_id = %d",
//                     $old_item->order_item_id
//                 ));
//                 if ($item_metas) {
//                     $rows = [];
//                     foreach ($item_metas as $im) {
//                         $rows[] = $wpdb->prepare('(%d, %s, %s)', $new_item_id, $im->meta_key, $im->meta_value);
//                     }
//                     $wpdb->query("INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta
//                         (order_item_id, meta_key, meta_value) VALUES " . implode(',', $rows));
//                 }
//             }

//             wc_get_order($new_order_id)?->save();
//             echo "OK #{$old_order->ID} → new #{$new_order_id}\n";
//             $imported++;
//         }
//     }

//     $processed_so_far = $offset + count($old_orders);
//     $next_offset      = $offset + $limit;
//     $has_more         = $processed_so_far < $total;

//     echo "\n--- Batch Result ---\n";
//     echo "Imported : {$imported}\n";
//     echo "Skipped  : {$skipped}\n";
//     echo "Progress : {$processed_so_far} / {$total}\n";

//     if ($has_more) {
//         $next_url = home_url('/?sync_old_orders=1&offset=' . $next_offset);
//         echo "\nNEXT BATCH → " . $next_url . "\n";
//         echo '<meta http-equiv="refresh" content="3;url=' . esc_url($next_url) . '">';
//         echo "\n(Auto-redirecting in 3 seconds...)\n";
//     } else {
//         echo "\nALL DONE. Migration complete!\n";
//         echo "IMPORTANT: Remove both scripts from functions.php now!\n";
//     }

//     exit;
// });

/**
 * ============================================================
 *  SCRIPT — Repair ALL migrated orders (billing / shipping / etc.)
 *
 *  URL (admin logged in):
 *    yoursite.com/?repair_migrated_order=1              — all orders (batches)
 *    yoursite.com/?repair_migrated_order=1&dry_run=1    — preview only
 *    yoursite.com/?repair_migrated_order=1&order_id=7056 — single order
 *    yoursite.com/?repair_migrated_order=1&force=1      — re-run even if fixed
 *
 *  OLD tables: agar rename ki thin to $old_posts / $old_postmeta update karein.
 * ============================================================
 */

/*
add_action( 'init', function () {
	if ( ! isset( $_GET['repair_migrated_order'] ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		exit( 'Unauthorized' );
	}

	if ( ! function_exists( 'wc_get_order' ) ) {
		exit( 'WooCommerce not active.' );
	}

	global $wpdb;

	// --- Config: old site tables (jo import ke waqt database mein rakhi hain) ---
	$old_posts    = 'wp_posts';
	$old_postmeta = 'wp_postmeta';

	$batch_limit = 25;
	$offset      = isset( $_GET['offset'] ) ? max( 0, (int) $_GET['offset'] ) : 0;
	$dry_run     = isset( $_GET['dry_run'] ) && $_GET['dry_run'] === '1';
	$force       = isset( $_GET['force'] ) && $_GET['force'] === '1';
	$single_id   = isset( $_GET['order_id'] ) ? (int) $_GET['order_id'] : 0;

	@set_time_limit( 120 );
	@ini_set( 'memory_limit', '512M' );

	$billing_fields  = array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'email', 'phone' );
	$shipping_fields = array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone' );

	$new_postmeta = $wpdb->prefix . 'postmeta';
	$new_posts    = $wpdb->prefix . 'posts';

	$load_post_meta = function ( $post_id, $meta_table ) use ( $wpdb ) {
		$raw = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$meta_table} WHERE post_id = %d",
				$post_id
			)
		);
		$meta = array();
		if ( $raw ) {
			foreach ( $raw as $row ) {
				$meta[ $row->meta_key ] = maybe_unserialize( $row->meta_value );
			}
		}
		return $meta;
	};

	$has_billing_in_meta = function ( $meta ) {
		if ( empty( $meta ) || ! is_array( $meta ) ) {
			return false;
		}
		return ! empty( $meta['_billing_first_name'] )
			|| ! empty( $meta['_billing_address_1'] )
			|| ! empty( $meta['_billing_email'] );
	};

	
	$resolve_old_order_id = function ( $order, $new_order_id ) use ( $wpdb, $old_posts, $old_postmeta, $has_billing_in_meta, $load_post_meta, $single_id ) {
		if ( $single_id && $single_id === $new_order_id && isset( $_GET['old_order_id'] ) ) {
			return (int) $_GET['old_order_id'];
		}

		$migrated = (int) $order->get_meta( '_migrated_from_order_id' );
		if ( $migrated ) {
			return $migrated;
		}

		$candidates = array();

		// Same ID on old table (common when posts were copied with same IDs).
		$same_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$old_posts} WHERE ID = %d AND post_type = 'shop_order' LIMIT 1",
				$new_order_id
			)
		);
		if ( $same_id ) {
			$candidates[] = $same_id;
		}

		// Match by WooCommerce order key.
		$order_key = $order->get_order_key();
		if ( $order_key ) {
			$by_key = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT post_id FROM {$old_postmeta} WHERE meta_key = '_order_key' AND meta_value = %s LIMIT 1",
					$order_key
				)
			);
			if ( $by_key ) {
				$candidates[] = $by_key;
			}
		}

		// Match by payment transaction ID (screenshot: pay_xxx).
		$txn = $order->get_transaction_id();
		if ( $txn ) {
			$by_txn = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT post_id FROM {$old_postmeta} WHERE meta_key = '_transaction_id' AND meta_value = %s LIMIT 1",
					$txn
				)
			);
			if ( $by_txn ) {
				$candidates[] = $by_txn;
			}
		}

		// Match by order total + date (approximate).
		$total = $order->get_total();
		$date  = $order->get_date_created();
		if ( $total && $date ) {
			$by_date_total = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT p.ID FROM {$old_posts} p
					INNER JOIN {$old_postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = '_order_total' AND pm.meta_value = %s
					WHERE p.post_type = 'shop_order' AND p.post_date = %s
					LIMIT 1",
					(string) $total,
					$date->date( 'Y-m-d H:i:s' )
				)
			);
			if ( $by_date_total ) {
				$candidates[] = $by_date_total;
			}
		}

		$candidates = array_values( array_unique( array_filter( $candidates ) ) );

		foreach ( $candidates as $candidate_id ) {
			$meta = $load_post_meta( $candidate_id, $old_postmeta );
			if ( $has_billing_in_meta( $meta ) ) {
				return (int) $candidate_id;
			}
		}

		// Return first candidate even without billing (caller will report).
		return ! empty( $candidates ) ? (int) $candidates[0] : 0;
	};

	$log = array();

	if ( $single_id ) {
		$order_ids = array( $single_id );
		$total     = 1;
	} else {
		$result = wc_get_orders(
			array(
				'limit'    => $batch_limit,
				'offset'   => $offset,
				'orderby'  => 'ID',
				'order'    => 'ASC',
				'paginate' => true,
				'return'   => 'ids',
				'type'     => 'shop_order',
			)
		);
		$order_ids = $result->orders;
		$total     = (int) $result->total;
	}

	$stats = array(
		'ok'      => 0,
		'skip'    => 0,
		'fail'    => 0,
		'repaired' => 0,
	);

	$log[] = 'Repair migrated orders' . ( $dry_run ? ' [DRY RUN]' : '' );
	if ( ! $single_id ) {
		$log[] = "Batch: offset {$offset}, limit {$batch_limit}, total orders: {$total}";
		$log[] = '';
	}

	foreach ( $order_ids as $new_order_id ) {
		$new_order_id = (int) $new_order_id;
		$order        = wc_get_order( $new_order_id );

		if ( ! $order ) {
			$log[] = "SKIP #{$new_order_id}: not found";
			++$stats['skip'];
			continue;
		}

		// Skip only if this script already repaired it (not merely "has billing" from import).
		if ( ! $force && 'yes' === $order->get_meta( '_order_address_repaired' ) ) {
			$log[] = "SKIP #{$new_order_id}: already repaired (use &force=1 to re-run)";
			++$stats['skip'];
			continue;
		}

		$debug = isset( $_GET['debug'] ) && $_GET['debug'] === '1';

		if ( $debug ) {
			$log[] = "=== DEBUG #{$new_order_id} ===";
			$log[] = '_migrated_from_order_id: ' . ( $order->get_meta( '_migrated_from_order_id' ) ?: '(empty)' );
			$log[] = 'order_key: ' . $order->get_order_key();
			$log[] = 'transaction_id: ' . $order->get_transaction_id();
			$log[] = 'total: ' . $order->get_total();
			$log[] = 'date: ' . ( $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '' );
			$new_meta = $load_post_meta( $new_order_id, $new_postmeta );
			$log[] = 'billing in ' . $new_postmeta . ': ' . ( $has_billing_in_meta( $new_meta ) ? 'yes' : 'no' );
			$old_try = $load_post_meta( $new_order_id, $old_postmeta );
			$log[] = 'billing in ' . $old_postmeta . " (post_id {$new_order_id}): " . ( $has_billing_in_meta( $old_try ) ? 'yes' : 'no' );
			$log[] = '';
		}

		$old_order_id = $resolve_old_order_id( $order, $new_order_id );
		$meta_source  = '';

		if ( ! $old_order_id ) {
			$log[] = "FAIL #{$new_order_id}: old order ID not found";
			++$stats['fail'];
			continue;
		}

		if ( $debug ) {
			$log[] = "Resolved old order ID: #{$old_order_id}";
		}

		$old_post = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ID, post_status, post_excerpt, post_date, post_modified FROM {$old_posts} WHERE ID = %d AND post_type = 'shop_order' LIMIT 1",
				$old_order_id
			)
		);

		// Fallback: old row missing but meta may still exist in old_postmeta or new postmeta.
		if ( ! $old_post ) {
			$old_post = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT ID, post_status, post_excerpt, post_date, post_modified FROM {$new_posts} WHERE ID = %d AND post_type = 'shop_order' LIMIT 1",
					$old_order_id
				)
			);
			if ( $old_post ) {
				$meta_source = $new_postmeta;
			}
		}

		$meta = $load_post_meta( $old_order_id, $old_postmeta );
		if ( $has_billing_in_meta( $meta ) ) {
			$meta_source = $old_postmeta;
		} else {
			$new_meta = $load_post_meta( $new_order_id, $new_postmeta );
			if ( $has_billing_in_meta( $new_meta ) ) {
				$meta       = $new_meta;
				$meta_source = $new_postmeta;
				if ( ! $old_post ) {
					$old_post = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT ID, post_status, post_excerpt, post_date, post_modified FROM {$new_posts} WHERE ID = %d LIMIT 1",
							$new_order_id
						)
					);
				}
			} elseif ( $old_order_id !== $new_order_id ) {
				$same_meta = $load_post_meta( $new_order_id, $old_postmeta );
				if ( $has_billing_in_meta( $same_meta ) ) {
					$meta        = $same_meta;
					$meta_source = $old_postmeta . " (new order id {$new_order_id})";
					$old_order_id = $new_order_id;
				}
			}
		}

		if ( ! $meta_source ) {
			$meta_source = $old_postmeta;
		}

		if ( ! $old_post && ! $has_billing_in_meta( $meta ) ) {
			$log[] = "FAIL #{$new_order_id}: no billing in {$old_postmeta} / {$new_postmeta}";
			++$stats['fail'];
			continue;
		}

		$gm = function ( $key, $default = '' ) use ( $meta ) {
			return isset( $meta[ $key ] ) ? $meta[ $key ] : $default;
		};

		$billing_email = $gm( '_billing_email' );
		$has_billing   = ! empty( $gm( '_billing_first_name' ) ) || ! empty( $gm( '_billing_address_1' ) ) || ! empty( $billing_email );

		if ( ! $has_billing ) {
			$log[] = "FAIL #{$new_order_id}: empty billing for old #{$old_order_id}";
			++$stats['fail'];
			continue;
		}

		if ( $debug ) {
			$log[] = "=== #{$new_order_id} ← old #{$old_order_id} ({$meta_source}) ===";
			$log[] = $gm( '_billing_first_name' ) . ' ' . $gm( '_billing_last_name' ) . ', ' . $gm( '_billing_city' ) . ' — ' . $billing_email;
		}

		if ( $dry_run ) {
			$log[] = "OK  #{$new_order_id} ← #{$old_order_id} (dry run)";
			++$stats['ok'];
			continue;
		}

		$order->update_meta_data( '_migrated_from_order_id', $old_order_id );
		$order->update_meta_data( '_order_address_repaired', 'yes' );

		$billing = array();
		foreach ( $billing_fields as $f ) {
			$billing[ $f ] = $gm( '_billing_' . $f );
		}
		$order->set_address( $billing, 'billing' );

		$shipping = array();
		foreach ( $shipping_fields as $f ) {
			$shipping[ $f ] = $gm( '_shipping_' . $f );
		}
		$order->set_address( $shipping, 'shipping' );

		// Customer: old user ID often does not exist on new site → guest + billing email.
		$old_customer_id = (int) $gm( '_customer_user', 0 );
		$new_customer_id = 0;

		if ( $old_customer_id > 0 && get_user_by( 'id', $old_customer_id ) ) {
			$new_customer_id = $old_customer_id;
		} elseif ( $billing_email ) {
			$user = get_user_by( 'email', $billing_email );
			if ( $user ) {
				$new_customer_id = (int) $user->ID;
			}
		}

		$order->set_customer_id( $new_customer_id );

		if ( $gm( '_payment_method' ) ) {
			$order->set_payment_method( $gm( '_payment_method' ) );
		}
		if ( $gm( '_payment_method_title' ) ) {
			$order->set_payment_method_title( $gm( '_payment_method_title' ) );
		}
		if ( $gm( '_transaction_id' ) ) {
			$order->set_transaction_id( $gm( '_transaction_id' ) );
		}
		if ( $gm( '_order_currency' ) ) {
			$order->set_currency( $gm( '_order_currency' ) );
		}
		if ( $gm( '_customer_ip_address' ) ) {
			$order->set_customer_ip_address( $gm( '_customer_ip_address' ) );
		}
		if ( $old_post && ! empty( $old_post->post_excerpt ) ) {
			$order->set_customer_note( $old_post->post_excerpt );
		}

		$order->set_shipping_total( (float) $gm( '_order_shipping', 0 ) );
		$order->set_shipping_tax( (float) $gm( '_order_shipping_tax', 0 ) );
		$order->set_cart_tax( (float) $gm( '_order_tax', 0 ) );
		$order->set_discount_total( (float) $gm( '_cart_discount', 0 ) );
		$order->set_discount_tax( (float) $gm( '_cart_discount_tax', 0 ) );
		$order->set_total( (float) $gm( '_order_total', 0 ) );

		if ( $old_post && ! empty( $old_post->post_status ) ) {
			$order->set_status( str_replace( 'wc-', '', $old_post->post_status ) );
		}

		$order->save();

		$log[] = 'OK  #' . $new_order_id . ' ← old #' . $old_order_id . ' — ' . $gm( '_billing_first_name' ) . ' ' . $gm( '_billing_last_name' );
		++$stats['ok'];
		++$stats['repaired'];
	}

	$log[] = '';
	$log[] = '--- Batch summary ---';
	$log[] = "OK: {$stats['ok']} | SKIP: {$stats['skip']} | FAIL: {$stats['fail']}";

	$next_url    = '';
	$has_more    = false;
	$processed   = 0;

	if ( ! $single_id ) {
		$processed = $offset + count( $order_ids );
		$has_more  = $processed < $total;
		$log[]     = "Progress: {$processed} / {$total}";

		if ( $has_more ) {
			$next_offset = $offset + $batch_limit;
			$next_url    = home_url( '/?repair_migrated_order=1&offset=' . $next_offset );
			$log[]       = '';
			$log[]       = 'NEXT BATCH → ' . $next_url;
		} else {
			$log[] = '';
			$log[] = 'ALL DONE. Remove repair script from functions.php.';
		}
	} else {
		$log[] = '';
		$log[] = 'Done (single order).';
	}

	$log_text = implode( "\n", $log );

	// Entire batch skipped/failed only → instant server redirect (fast-forward).
	if ( $has_more && ! $dry_run && 0 === $stats['ok'] ) {
		wp_safe_redirect( $next_url );
		exit;
	}

	// HTML page so browser runs JS redirect (plain text ignores <meta>).
	if ( $has_more && ! $dry_run && ! $single_id ) {
		header( 'Content-Type: text/html; charset=utf-8' );
		?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Repair orders — batch <?php echo (int) $offset; ?></title>
	<style>body{font-family:monospace,sans-serif;margin:20px;background:#111;color:#eee}a{color:#6cf}</style>
	<script>
		setTimeout(function () {
			window.location.replace(<?php echo wp_json_encode( $next_url ); ?>);
		}, 2000);
	</script>
</head>
<body>
	<pre><?php echo esc_html( $log_text ); ?></pre>
	<p>Redirecting to next batch in 2 seconds…</p>
	<p><a href="<?php echo esc_url( $next_url ); ?>">Click here if redirect does not work →</a></p>
</body>
</html>
		<?php
		exit;
	}

	header( 'Content-Type: text/plain; charset=utf-8' );
	echo $log_text;
	exit;
} );


*/

// add_action('init', function () {
//     if (!isset($_GET['delete_all_orders'])) return;
//     //if (!current_user_can('manage_options')) exit('Unauthorized');

//     // Safety: confirm=yes parameter zaruri hai actual delete ke liye
//     $confirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

//     global $wpdb;

//     $order_ids = wc_get_orders([
//         'limit'  => -1,
//         'return' => 'ids',
//         'type'   => ['shop_order', 'shop_order_refund'],
//     ]);

//     if (empty($order_ids)) {
//         exit('No orders found to delete.');
//     }

//     $total = count($order_ids);

//     if (!$confirmed) {
//         // DRY RUN — sirf count batao, kuch delete mat karo
//         echo "DRY RUN — {$total} orders found.\n";
//         echo "Actual delete ke liye yeh URL use karein:\n";
//         echo home_url('/?delete_all_orders=1&confirm=yes') . "\n";
//         exit;
//     }

//     // ACTUAL DELETE
//     $deleted = 0;
//     foreach ($order_ids as $order_id) {
//         $order = wc_get_order($order_id);
//         if (!$order) continue;
//         $order->delete(true); // true = permanent delete (trash nahi)
//         echo "Deleted #{$order_id}\n";
//         $deleted++;
//     }

//     // Clean up leftover order items (agar koi reh gaye)
//     $wpdb->query("
//         DELETE oi, oim
//         FROM {$wpdb->prefix}woocommerce_order_items oi
//         LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim
//             ON oi.order_item_id = oim.order_item_id
//         WHERE oi.order_id NOT IN (
//             SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN ('shop_order','shop_order_refund')
//         )
//     ");

//     echo "\nDeleted: {$deleted} / {$total} orders.\n";
//     echo "IMPORTANT: Remove both scripts from functions.php now!\n";
//     exit;
// });