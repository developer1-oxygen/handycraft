<?php

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
};

use XTS\Options;

/**
 * Dynamic css classwc-extra
 *
 * @since 1.0.0
 */
class WOODMART_Dynamiccss {

	/**
	 * Admin notices service instance.
	 *
	 * @var object
	 */
	private $_notices;

	/**
	 * Set up all properties
	 */
	public function __construct() {
		$this->_notices = WOODMART_Registry()->notices;
		add_action( 'admin_init', array( $this, 'check_file_version' ), 90 );
		add_action( 'admin_init', array( $this, 'save_css' ), 100 );
		add_action( 'admin_init', array( $this, 'write_file' ), 200 );
		add_action( 'wp', array( $this, 'print_styles' ), 100 );
	}
	
	/**
	 * Check file version.
	 *
	 * @since 1.0.0
	 */
	public function check_file_version() {
		$file_data = get_option( 'woodmart-dynamic-css-file' );
		
		if ( ! isset( $file_data['theme_version'] ) || ( isset( $file_data['theme_version'] ) && ! $file_data['theme_version'] ) || version_compare( woodmart_get_theme_info( 'Version' ), $file_data['theme_version'], '>') ) {
			delete_option( 'woodmart-dynamic-css-file' );
			delete_option( 'woodmart-dynamic-css-data' );
		}
	}

	/**
	 * Print styles.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() {
		$file = get_option( 'woodmart-dynamic-css-file' );

		if ( isset( $file['path'] ) && file_exists( $file['path'] ) && 'valid' === get_option( 'woodmart-dynamic-css-file-status' ) && 'file' === apply_filters( 'woodmart_dynamic_css_otput', 'file' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'file_css' ), 100001 );
		} else {
			add_action( 'wp_head', array( $this, 'inline_css' ), 200 );
		}
	}
		
	/**
	 * FIle css.
	 *
	 * @since 1.0.0
	 */
	public function file_css() {
		$file = get_option( 'woodmart-dynamic-css-file' );

		if ( isset( $file['url'] ) ) {
			if ( is_ssl() ) {
				$file['url'] = str_replace( 'http://', 'https://', $file['url'] );
			}

			wp_enqueue_style( 'woodmart-dynamic-style', $file['url'], array(), woodmart_get_theme_info( 'Version' ) );
		}
	}

	/**
	 * Inline css.
	 *
	 * @since 1.0.0
	 */
	public function inline_css() {
		$css = '';

		if ( get_option( 'woodmart-dynamic-css-data' ) && 'file' === apply_filters( 'woodmart_dynamic_css_otput', 'file' ) ) {
			$css .= get_option( 'woodmart-dynamic-css-data' );
		} else {
			$css .= Options::get_instance()->get_css_output();
		}
		
		$css .= $this->icons_font_css();
		$css .= $this->custom_css();
		$css .= $this->custom_fonts_css();

		if ( $css ) {
			echo '<style data-type="woodmart-dynamic-css">' . $css . '</style>'; // phpcs:ignore
		}
	}

	/**
	 * Write file.
	 *
	 * @since 1.0.0
	 */
	public function write_file() {
		global $wp_filesystem;

		if ( ! isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'xtemos_options' !== $_GET['page'] ) ) {
			return;
		}

		if ( ! $this->check_credentials() || ! $wp_filesystem ) {
			return;
		}

		$file = get_option( 'woodmart-dynamic-css-file' );

		if ( $file && $file['path'] ) {
			$wp_filesystem->delete( $file['path'] );
			delete_option( 'woodmart-dynamic-css-file' );
		}

		$css = get_option( 'woodmart-dynamic-css-data' );

		if ( ! $css ) {
			return;
		}
		
		$css .= $this->icons_font_css();
		$css .= $this->custom_css();
		$css .= $this->custom_fonts_css();

		$result = $wp_filesystem->put_contents(
			$this->get_file_info( 'path' ),
			$css
		);

		if ( $result ) {
			update_option(
				'woodmart-dynamic-css-file',
				array(
					'url'           => $this->get_file_info( 'url' ),
					'path'          => $this->get_file_info( 'path' ),
					'theme_version' => woodmart_get_theme_info( 'Version' ),
				)
			);

			update_option( 'woodmart-dynamic-css-file-status', 'valid' );
		} else {
			$this->_notices->add_warning( 'Can\'t move file to uploads folder with wp_filesystem class.' );
			$this->_notices->show_msgs();
		}
	}

	/**
	 * Save css data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $css Css data.
	 */
	public function save_css( $css ) {
		if ( ! isset( $_GET['settings-updated'] ) ) {
			return;
		}

		$css = Options::get_instance()->get_css_output();

		update_option( 'woodmart-dynamic-css-data', $css );
		update_option( 'woodmart-dynamic-css-file-status', 'invalid' );
		delete_option( 'woodmart-dynamic-css-file-credentials' );
	}

	/**
	 * Check credentials.
	 *
	 * @since 1.0.0
	 */
	public function check_credentials() {
		global $wp_filesystem;

		if ( ( 'valid' === get_option( 'woodmart-dynamic-css-file-status' ) || 'requested' === get_option( 'woodmart-dynamic-css-file-credentials' ) ) && ! $_POST ) { // phpcs:ignore
			return false;
		}

		update_option( 'woodmart-dynamic-css-file-credentials', 'requested' );

		echo '<div class="woodmart-request-credentials">';
			$creds = request_filesystem_credentials( false, '', false, false, array_keys( $_POST ) );
		echo '</div>';

		if ( ! $creds ) {
			return false;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			$this->_notices->add_warning( 'Can\'t access your file system. The FTP access is wrong.' );
			$this->_notices->show_msgs();
			return false;
		}

		return true;
	}

	/**
	 * Get file info.
	 *
	 * @since 1.0.0
	 *
	 * @param string $target File info target.
	 *
	 * @return string
	 */
	public function get_file_info( $target ) {
		$file_name = 'woodmart-dynamic-' . time() . '.css';
		$uploads   = wp_upload_dir();

		if ( 'filename' === $target ) {
			return $file_name;
		}

		if ( 'path' === $target ) {
			return $uploads['path'] . '/' . $file_name;
		}

		return $uploads['url'] . '/' . $file_name;
	}

	/**
	 * Get custom css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function custom_css() {
		$output = '';
		$custom_css      = woodmart_get_opt( 'custom_css' );
		$css_desktop     = woodmart_get_opt( 'css_desktop' );
		$css_tablet      = woodmart_get_opt( 'css_tablet' );
		$css_wide_mobile = woodmart_get_opt( 'css_wide_mobile' );
		$css_mobile      = woodmart_get_opt( 'css_mobile' );

		if ( $custom_css ) {
			$output .= $custom_css;
		}
		
		if ( $css_desktop ) {
			$output .= '@media (min-width: 1025px) { ' . $css_desktop . ' }';
		}
		
		if ( $css_tablet ) {
			$output .= '@media (min-width: 768px) and (max-width: 1024px) {' . $css_tablet . ' }';
		}
		
		if ( $css_wide_mobile ) {
			$output .= '@media (min-width: 577px) and (max-width: 767px) { ' . $css_wide_mobile . ' }';
		}
		
		if ( $css_mobile ) {
			$output .= '@media (max-width: 576px) { ' . $css_mobile . ' }';
		}

		return $output;
	}

	/**
	 * Get custom fonts css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function custom_fonts_css() {
		$fonts = woodmart_get_opt( 'multi_custom_fonts' );

		$output = '';
		$font_display = woodmart_get_opt( 'google_font_display' );

		if ( isset( $fonts['{{index}}'] ) ) {
			unset( $fonts['{{index}}'] );
		}

		if ( ! $fonts ) {
			return $output;
		}

		foreach ( $fonts as $key => $value ) {
			$eot   = $this->get_custom_font_url( $value['font-eot'] );
			$woff  = $this->get_custom_font_url( $value['font-woff'] );
			$woff2 = $this->get_custom_font_url( $value['font-woff2'] );
			$ttf   = $this->get_custom_font_url( $value['font-ttf'] );
			$svg   = $this->get_custom_font_url( $value['font-svg'] );

			if ( ! $value['font-name'] ) {
				continue;
			}

			$output .= '@font-face {';
			$output .= 'font-family: "' . sanitize_text_field( $value['font-name'] ) . '";';

			if ( $eot ) {
				$output .= 'src: url("' . esc_url( $eot ) . '");';
				$output .= 'src: url("' . esc_url( $eot ) . '#iefix") format("embedded-opentype"),';
			}

			if ( $woff ) {
				$output .= 'url("' . esc_url( $woff ) . '") format("woff"),';
			}

			if ( $woff2 ) {
				$output .= 'url("' . esc_url( $woff2 ) . '") format("woff2"),';
			}

			if ( $ttf ) {
				$output .= 'url("' . esc_url( $ttf ) . '") format("truetype"),';
			}

			if ( $svg ) {
				$output .= 'url("' . esc_url( $svg ) . '#' . sanitize_text_field( $value['font-name'] ) . '") format("svg");';
			}

			if ( $value['font-weight'] ) {
				$output .= 'font-weight: ' . sanitize_text_field( $value['font-weight'] ) . ';';
			} else {
				$output .= 'font-weight: normal;';
			}

			if ( 'disable' !== $font_display ) {
				$output .= 'font-display:' . $font_display . ';';
			}

			$output .= 'font-style: normal;';
			$output .= '}';
		}

		return $output;
	}

	/**
	 * Icons font css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function icons_font_css() {
		$output = '';

		$font_display = woodmart_get_opt( 'icons_font_display' );

		// Our font.
		$output .= '@font-face {
			font-weight: normal;
			font-style: normal;
			font-family: "woodmart-font";
			src: url("' . WOODMART_THEME_DIR . '/fonts/woodmart-font.eot");
			src: url("' . WOODMART_THEME_DIR . '/fonts/woodmart-font.eot?#iefix") format("embedded-opentype"),
			url("' . WOODMART_THEME_DIR . '/fonts/woodmart-font.woff") format("woff"),
			url("' . WOODMART_THEME_DIR . '/fonts/woodmart-font.woff2") format("woff2"),
			url("' . WOODMART_THEME_DIR . '/fonts/woodmart-font.ttf") format("truetype"),
			url("' . WOODMART_THEME_DIR . '/fonts/woodmart-font.svg#woodmart-font") format("svg");';
		
		if ( 'disable' !== $font_display ) {
			$output .= 'font-display:' . $font_display . ';';
		}

		$output .= '}';
		
		if ( ! woodmart_get_opt( 'disable_font_awesome_theme_css' ) ) {
			$prefix = '';
			if ( woodmart_get_opt( 'light_font_awesome_version' ) ) {
				$prefix = '-light';
			}
			// FontAwesome
			$output .= '@font-face {
				font-family: "FontAwesome";
				src: url("' . WOODMART_THEME_DIR . '/fonts/fontawesome-webfont' . $prefix . '.eot?v=4.7.0");
				src: url("' . WOODMART_THEME_DIR . '/fonts/fontawesome-webfont' . $prefix . '.eot?#iefix&v=4.7.0") format("embedded-opentype"),
				url("' . WOODMART_THEME_DIR . '/fonts/fontawesome-webfont' . $prefix . '.woff2?v=4.7.0") format("woff2"),
				url("' . WOODMART_THEME_DIR . '/fonts/fontawesome-webfont' . $prefix . '.woff?v=4.7.0") format("woff"),
				url("' . WOODMART_THEME_DIR . '/fonts/fontawesome-webfont' . $prefix . '.ttf?v=4.7.0") format("truetype"),
				url("' . WOODMART_THEME_DIR . '/fonts/fontawesome-webfont' . $prefix . '.svg?v=4.7.0#fontawesomeregular") format("svg");
				font-weight: normal;
				font-style: normal;';
	
			if ( 'disable' !== $font_display ) {
				$output .= 'font-display:' . $font_display . ';';
			}
	
			$output .= '}';
		}

		return $output;
	}

	/**
	 * Get custom font url.
	 *
	 * @since 1.0.0
	 *
	 * @param array $font Font data.
	 *
	 * @return string
	 */
	public function get_custom_font_url( $font ) {
		$url = '';

		if ( isset( $font['id'] ) && $font['id'] ) {
			$url = wp_get_attachment_url( $font['id'] );
		} elseif ( is_array( $font ) ) {
			$url = $font['url'];
		}

		return $url;
	}
}
