<?php
// Get the logo (Header Builder image, then site branding, then Woodmart default).
$logo = function_exists( 'wd_get_site_logo_url' )
	? wd_get_site_logo_url()
	: WOODMART_IMAGES . '/wood-logo-dark.svg';

$protocol = woodmart_http() . "://";

$has_sticky_logo = ( isset( $params['sticky_image']['url'] ) && ! empty( $params['sticky_image']['url'] ) );

if(isset($params['image']['url']) && $params['image']['url'] != '') {
	$logo = $params['image']['url'];
}

if(isset($params['image']['id']) && $params['image']['id'] != '') {
	$attachment = wp_get_attachment_image_src( $params['image']['id'], 'full' );
	if( isset( $attachment[0] ) && ! empty( $attachment[0] ) )
		$logo = $attachment[0];
}

// Replace Woodmart placeholder logos with site branding (common on mobile header row).
if ( function_exists( 'wd_get_site_logo_url' ) && preg_match( '/wood-logo-(dark|white)\.svg/i', (string) $logo ) ) {
	$logo = wd_get_site_logo_url();
}

$logo = $protocol. str_replace(array('http://', 'https://'), '', $logo);

$width = isset($params['width']) ? (int) $params['width'] : 150;
$sticky_width = isset($params['sticky_width']) ? (int) $params['sticky_width'] : 150;

?>
<div class="site-logo">
	<div class="woodmart-logo-wrap<?php if( $has_sticky_logo ) echo " switch-logo-enable"; ?>">
		<a href="<?php echo esc_url( home_url('/') ); ?>" class="woodmart-logo woodmart-main-logo" rel="home">
			<?php echo '<img src="' . $logo . '" alt="' . get_bloginfo( 'name' ) . '" style="max-width: ' . esc_attr( $width ) . 'px;" />'; ?>
		</a>
		<?php if ( $has_sticky_logo ): ?>
			<?php
				$logo_sticky = $protocol . str_replace( array( 'http://', 'https://' ), '', $params['sticky_image']['url'] );
			 ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="woodmart-logo woodmart-sticky-logo" rel="home">
				<?php echo '<img src="' . $logo_sticky . '" alt="' . get_bloginfo( 'name' ) . '" style="max-width: ' . esc_attr( $sticky_width ) . 'px;" />'; ?>
			</a>
		<?php endif ?>
	</div>
</div>
