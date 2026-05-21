<?php
if( $params['menu_id'] == '' ) return;

$extra_class = '';
$opened = get_post_meta( woodmart_get_the_ID(), '_woodmart_open_categories', true );
$icon_type = $params['icon_type'];

if ( woodmart_woocommerce_installed() && is_product() ) $opened = false;

$class = ( $params['color_scheme'] != 'inherit') ? 'color-scheme-' . $params['color_scheme'] : '';

if ( ! empty( $params['background'] ) && ! empty( $params['background']['background-color'] ) ) {
	$class .= ' has-bg';
}

$extra_class .= ( $opened ) ? ' opened-menu' : ' show-on-hover';

if ( $icon_type == 'custom' ) {
	$extra_class .= ' woodmart-cat-custom-icon';
}

?>

<div class="header-categories-nav<?php echo esc_attr( $extra_class ); ?>" role="navigation">
	<div class="header-categories-nav-wrap">
		<span class="whb-<?php echo esc_attr( $id ); ?> menu-opener <?php echo esc_attr( $class ); ?>">

			<?php if ( $icon_type == 'custom' ): ?>
				<span class="woodmart-custom-burger-icon"><?php echo whb_get_custom_icon( $params['custom_icon'] ); ?></span>
			<?php else: ?>
				<span class="woodmart-burger"></span>
			<?php endif; ?>

			<span class="menu-open-label">
				<?php esc_html_e('Browse Categories', 'woodmart'); ?>
			</span>
			<span class="arrow-opener"></span>
		</span>
		<div class="categories-menu-dropdown vertical-navigation woodmart-navigation">
			<?php 
				wp_nav_menu(
					array(
						'menu' => $params['menu_id'],
						'menu_class' => 'menu',
						'walker' => new WOODMART_Mega_Menu_Walker()
					)
				);
			 ?>
		</div>
	</div>
</div>
