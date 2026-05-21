<?php
/**
 * Template Name: Home Template
 *
 * Same as default page template (100% same layout). Use this template for the home page
 * so you can target it in ACF: set Location Rule to "Page Template" is equal to "Home Template".
 * No visual change – only for ACF settings on the home page.
 *
 * @package Woodmart
 */

get_header();

// Inline CSS: Elementor testimonial slider – content upper, photo/avatar lower (jaisa reference image)
?>
<style>
.max_container{ max-width:1300px !important; margin:0 auto ; }
/* Testimonial card: content upar, avatar neeche */
.testimonial .testimonial-inner { display: flex; flex-direction: column; }
.testimonial .testimonial-avatar { order: 2; margin-top: 20px; }
.testimonial .testimonial-content { order: 1; }
/* Card look: rounded, shadow */
.testimonial { background: #f9f9f9; border-radius: 14px; padding: 28px 24px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.testimonial .testimonial-content { font-size: 15px; line-height: 1.6; color: #444; position: relative; padding-left: 42px; }
/* Top left: quote sign – theme image quote.png */
.testimonial .testimonial-content::before { content: ''; position: absolute; top: -4px; left: 0; z-index: 0; width: 40px; height: 40px; background-image: url(<?php echo esc_url( get_template_directory_uri() . '/custom/images/quote.png' ); ?>); background-size: contain; background-repeat: no-repeat; background-position: left top; }
/* Quote + stars row: visible, top left */
.testimonial .testimonial-rating { margin-bottom: 12px; display: flex !important; align-items: center; gap: 10px; position: relative; z-index: 1; }
.testimonial .star-rating { display: inline-block !important; position: relative; height: 1.2em; width: 5.5em; font-size: 16px; letter-spacing: 2px; visibility: visible !important; opacity: 1 !important; }
.testimonial .star-rating::before { content: '\2605\2605\2605\2605\2605'; color: #e0e0e0; }
.testimonial .star-rating span { position: absolute; top: 0; left: 0; height: 100%; overflow: hidden; display: block !important; }
.testimonial .star-rating span::before { content: '\2605\2605\2605\2605\2605'; color: #ffb400; }
/* Avatar neeche: inline 50px, footer content avatar ke saath (jQuery move karta hai) */
.testimonial .testimonial-avatar { display: flex !important; align-items: center; gap: 14px; flex-shrink: 0; margin-top: 16px; }
.testimonial .testimonial-avatar{ border-radius:0px !important }
.testimonial .testimonial-avatar img,
.testimonial .testimonial-avatar .testimonial-avatar-image { width: 50px !important; min-width: 50px !important; height: 50px !important; max-width: 50px !important; max-height: 50px !important; border-radius: 50% !important; object-fit: cover !important; object-position: center center !important; display: inline-block !important; vertical-align: middle; }
.testimonial .testimonial-avatar > *:first-child { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; flex-shrink: 0; display: inline-block; vertical-align: middle; }
.testimonial .testimonial-avatar > *:first-child img { width: 100% !important; height: 100% !important; max-width: none !important; object-fit: cover !important; object-position: center !important; display: inline-block !important; }
.testimonial .testimonial-avatar .wd-testimonial-footer-copy { font-size: 16px; font-weight: 700; color: #222; }
.testimonial .testimonial-avatar .wd-testimonial-footer-copy span { display: block; font-size: 13px; font-weight: 400; color: #666; margin-top: 2px; }
.testimonial .testimonial-content{min-height:127px !important; text-align:left; }
.testimonial .testimonial-content footer { margin-top: 16px; font-size: 16px; font-weight: 700; color: #222; }
.testimonial .testimonial-content footer:empty { display: none; }
.testimonial .testimonial-content footer span { display: block; font-size: 13px; font-weight: 400; color: #666; margin-top: 2px; }
/* Testimonial slider container: 1000px width */
.entry-content .elementor-widget:has(.testimonial),
.entry-content .elementor-widget:has(.owl-stage-outer) { max-width: 1000px; margin-left: auto; margin-right: auto; width: 100%; }
/* Prev/Next arrows: white chevron icons inside black button */
.entry-content .owl-carousel { position: relative; }
.entry-content .owl-nav { display: flex !important; justify-content: space-between; position: absolute; top: 50%; left: -12px; right: -12px; transform: translateY(-50%); pointer-events: none; z-index: 2; }
.entry-content .owl-nav button { pointer-events: auto; width: 44px; height: 44px; background: #222 !important; border: none !important; border-radius: 8px; display: flex !important; align-items: center; justify-content: center; cursor: pointer; padding: 0 !important; visibility: visible !important; opacity: 1 !important; background-repeat: no-repeat !important; background-position: center !important; background-size: 20px 20px !important; }
.entry-content .owl-nav button span { display: none !important; }
.entry-content .owl-nav .owl-prev { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 18l-6-6 6-6'/%3E%3C/svg%3E") !important; }
.entry-content .owl-nav .owl-next { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 18l6-6-6-6'/%3E%3C/svg%3E") !important; }
/* Home hero preloader */
#wd-home-hero-loader { position: fixed; inset: 0; background: #ffffff; z-index: 99999; display: flex; align-items: center; justify-content: center; opacity: 1; visibility: visible; transition: opacity .3s ease, visibility .3s ease; }
#wd-home-hero-loader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
#wd-home-hero-loader .wd-loader-spinner { width: 46px; height: 46px; border: 4px solid #e5e5e5; border-top-color: #111111; border-radius: 50%; animation: wdHeroSpin 0.8s linear infinite; }
@keyframes wdHeroSpin { to { transform: rotate(360deg); } }
.page-title{ display:none !important }
.main-page-wrapper{ padding-top:5px !important }
</style>
<?php
// Get content width and sidebar position (same as page.php)
$content_class = woodmart_get_content_class();
?>

<div id="wd-home-hero-loader" aria-hidden="true">
	<span class="wd-loader-spinner"></span>
</div>

<div class="site-content <?php echo esc_attr( $content_class ); ?>" role="main">

	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'woodmart' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
			</div>

			<?php woodmart_entry_meta(); ?>

		</article><!-- #post -->

		<?php
		if ( woodmart_get_opt( 'page_comments' ) && ( comments_open() || get_comments_number() ) ) :
			comments_template();
		endif;
		?>

	<?php endwhile; ?>

</div><!-- .site-content -->

<?php get_sidebar(); ?>

<script>
(function() {
	function hideHomeHeroLoader() {
		var loader = document.getElementById('wd-home-hero-loader');
		if (!loader || loader.classList.contains('is-hidden')) return;
		loader.classList.add('is-hidden');
		setTimeout(function() {
			if (loader && loader.parentNode) {
				loader.parentNode.removeChild(loader);
			}
		}, 350);
	}

	function getHeroSection() {
		return document.querySelector('.entry-content .elementor-section-wrap > .elementor-section:first-child') ||
			document.querySelector('.entry-content .elementor-section:first-child') ||
			document.querySelector('.entry-content section:first-of-type') ||
			document.querySelector('.entry-content > *:first-child');
	}

	function waitForHeroReady() {
		var hero = getHeroSection();
		if (!hero) {
			// Hero abhi DOM me inject nahi hua (Elementor/JS). Thoda wait karke dobara check karo.
			setTimeout(waitForHeroReady, 120);
			return;
		}

		var images = hero.querySelectorAll('img');
		if (!images.length) {
			hideHomeHeroLoader();
			return;
		}

		var loadedCount = 0;
		var totalImages = images.length;
		function onAssetLoaded() {
			loadedCount++;
			if (loadedCount >= totalImages) {
				hideHomeHeroLoader();
			}
		}

		images.forEach(function(img) {
			if (img.complete && img.naturalWidth > 0) {
				onAssetLoaded();
				return;
			}
			img.addEventListener('load', onAssetLoaded, { once: true });
			img.addEventListener('error', onAssetLoaded, { once: true });
		});

		// Safety fallback: never keep loader stuck.
		setTimeout(hideHomeHeroLoader, 6000);
	}

	function moveFooterToAvatar() {
		if (typeof jQuery === 'undefined') return;
		jQuery('.testimonial').each(function() {
			var $card = jQuery(this);
			var $footer = $card.find('.testimonial-content footer');
			var $avatar = $card.find('.testimonial-avatar');
			if ($footer.length && $avatar.length && !$avatar.find('.wd-testimonial-footer-copy').length) {
				var footerHtml = $footer.html();
				if (footerHtml && footerHtml.trim()) {
					$avatar.append('<div class="wd-testimonial-footer-copy">' + footerHtml + '</div>');
					$footer.empty();
				}
			}
			$avatar.find('img, .testimonial-avatar-image').css({ 'display': 'inline-block', 'width': '50px', 'max-width': '50px' });
		});
	}
	if (typeof jQuery !== 'undefined') {
		jQuery(document).ready(moveFooterToAvatar);
		setTimeout(moveFooterToAvatar, 400);
	}
	// Page ke complete load ka wait nahi karna, hero ready hote hi loader hatao.
	waitForHeroReady();
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', waitForHeroReady, { once: true });
	}
})();
jQuery(document).ready(function(){
	setInterval(function(){
		jQuery("#post-3262").parent().removeClass("col-lg-9");
		jQuery("#post-3262").parent().removeClass("col-md-9");
		jQuery("#post-3262").parent().addClass("col-lg-12");
		jQuery("#post-3262").parent().addClass("col-md-12");
		jQuery("#post-3262").parent().addClass("col-12");
		//col-lg-12 col-12 col-md-12
	},500);
	//jQuery("#post-3262").parent().removeClass("col-lg-9");
	//jQuery("#post-3262").parent().removeClass("col-md-9");
	//jQuery("#post-3262").parent().addClass("col-lg-12");

});
</script>

<?php get_footer(); ?>
