<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); ?>
	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php $is_frame_product = function_exists( 'wd_is_frame_product' ) ? wd_is_frame_product( get_the_ID() ) : false; ?>
		<?php if ( $is_frame_product ) : ?>
			<?php
			$frame_visualizer_path = get_theme_file_path( '/custom/frame-visualizer/art-visualizer.html' );
			$frame_visualizer_url  = function_exists( 'wd_get_frame_visualizer_url' )
				? wd_get_frame_visualizer_url( get_the_ID() )
				: get_theme_file_uri( '/custom/frame-visualizer/art-visualizer.html' );
			?>
			<?php if ( file_exists( $frame_visualizer_path ) ) : ?>
				<div class="frame-visualizer-wrapper" style="width: 100%; min-height: 900px; display:block; clear:both; float:none;">
					<iframe
						id="wd-frame-visualizer-iframe"
						src="<?php echo esc_url( $frame_visualizer_url ); ?>"
						title="Art Frame Visualizer"
						style="width: 100%; min-height: 900px; border: 0;"
						loading="lazy"
					></iframe>
				</div>
				<style>
					.frame-product-summary-wrapper {
						margin-top: 24px;
						padding: 18px 20px;
						border: 1px solid #ececec;
						border-radius: 12px;
						background: #fff;
						display: block !important;
						width: 100% !important;
						max-width: 100% !important;
						clear: both !important;
						float: none !important;
						position: relative;
						z-index: 2;
					}
					.frame-product-summary-wrapper h1.product_title {
						display: none !important;
					}
					.frame-product-summary-wrapper .summary {
						float: none !important;
						width: 100% !important;
						max-width: 100% !important;
						margin: 0 !important;
					}
					.frame-product-summary-wrapper .price {
						margin: 0 0 12px !important;
						font-size: 22px;
						font-weight: 700;
						display: none !important;
					}
					.frame-product-summary-wrapper .single_add_to_cart_button {
						min-height: 44px;
						padding: 10px 22px;
						border-radius: 8px;
					}
					.frame-product-summary-wrapper form.cart {
						display: flex;
						flex-wrap: wrap;
						align-items: center;
						gap: 12px;
						margin-top: 12px;
						width: 100%;
						display: none !important;
					}
					.frame-product-summary-wrapper form.cart .quantity {
						margin: 0 !important;
					}
					.frame-product-summary-wrapper form.cart .single_add_to_cart_button {
						margin-left: 0 !important;
					}
					.frame-product-summary-wrapper .yith-wcwl-add-to-wishlist,
					.frame-product-summary-wrapper .compare,
					.frame-product-summary-wrapper .product_meta,
					.frame-product-summary-wrapper .wd-compare-btn,
					.frame-product-summary-wrapper .wd-wishlist-btn,
					.frame-product-summary-wrapper .social-icons-wrapper,
					.frame-product-summary-wrapper .woodmart-compare-btn,
					.frame-product-summary-wrapper .single-product-share {
						display: none !important;
					}
					.frame-product-summary-wrapper .wd-product-pincode-checker {
						margin: 0 0 12px !important;
					}
					.frame-product-summary-wrapper .wd-product-pincode-checker input {
						min-height: 42px;
					}
					.frame-visualizer-wrapper + .frame-product-summary-wrapper {
						display: block !important;
						clear: both !important;
					}
				</style>
				<div class="frame-product-summary-wrapper" style="margin-top: 28px;">
					<?php
					/**
					 * Keep frame page clean: show core purchase controls only.
					 */
					woocommerce_template_single_price();
					woocommerce_template_single_add_to_cart();
					?>
				</div>
				<script>
					(function() {
						function enforcePurchasePanelPlacement() {
							var visualizerWrapper = document.querySelector('.frame-visualizer-wrapper');
							var purchasePanel = document.querySelector('.frame-product-summary-wrapper');
							if (!visualizerWrapper || !purchasePanel || !visualizerWrapper.parentNode) {
								return;
							}

							if (purchasePanel.previousElementSibling !== visualizerWrapper) {
								visualizerWrapper.parentNode.insertBefore(purchasePanel, visualizerWrapper.nextSibling);
							}

							purchasePanel.style.display = 'block';
							purchasePanel.style.float = 'none';
							purchasePanel.style.clear = 'both';
							purchasePanel.style.width = '100%';
						}

						function collectVisualizerData() {
							var iframe = document.getElementById('wd-frame-visualizer-iframe');
							if (!iframe || !iframe.contentDocument) {
								return {};
							}

							var doc = iframe.contentDocument;
							var fieldMap = {
								artwork_width: '.cart_artwork_width',
								artwork_height: '.cart_artwork_height',
								frame_width: '.cart_frame_width',
								frame_width_id: '.cart_frame_width_id',
								frame_color: '.cart_frame_color',
								frame_color_id: '.cart_frame_color_id',
								profile: '.frame_options_profile_text',
								mat_width: '.cart_mat_width',
								mat_top_width: '.cart_mat_top_width',
								mat_bottom_width: '.cart_mat_bottom_width',
								mat_left_width: '.cart_mat_left_width',
								mat_right_width: '.cart_mat_right_width',
								mat_color: '.cart_mat_color',
								mat_color_id: '.cart_mat_color_id',
								glaze: '.cart_glaze',
								glaze_id: '.cart_glaze_id',
								paper_type: '.cart_paper_type',
								paper_type_id: '.cart_paper_type_id',
								substrate: '.cart_substrate',
								substrate_id: '.cart_substrate_id',
								float: '.cart_float',
								spacers: '.cart_spacers',
								framing_process: '.cart_framing_process',
								scene_name: '.scene_control_swatch.active_scene_swatch',
								rotation: '#rotation_slider .noUi-handle',
								thumb_url: '.cart_thumb_url',
								thumb_token: '.cart_thumb_token',
								level_token: '.cart_level_token',
								product_name: '.cart_product_name',
								product_id: '.cart_product_id',
								bundle_id: '.cart_bundle_id'
							};

							var data = {};
							Object.keys(fieldMap).forEach(function(key) {
								var selector = fieldMap[key];
								var el = doc.querySelector(selector);
								if (!el) return;

								var value = '';
								if (key === 'scene_name') {
									value = el.getAttribute('data-scene-name') || '';
								} else if (key === 'rotation') {
									value = el.getAttribute('aria-valuenow') || '';
								} else if (typeof el.value !== 'undefined') {
									value = el.value || '';
								} else {
									value = (el.textContent || '').trim();
								}

								if (value !== '') {
									data[key] = value;
								}
							});

							// Fallbacks from visible controls (in case hidden cart fields are not synced yet).
							var artworkSizeSelect = doc.querySelector('#visualizer-artwork-size');
							if (artworkSizeSelect && typeof artworkSizeSelect.value !== 'undefined' && artworkSizeSelect.value) {
								var sizeValue = String(artworkSizeSelect.value || '');
								var parts = sizeValue.split('x');
								if (parts.length === 2) {
									if (!data.artwork_width) data.artwork_width = parts[0];
									if (!data.artwork_height) data.artwork_height = parts[1];
								}
								var selectedText = artworkSizeSelect.options && artworkSizeSelect.selectedIndex >= 0 ? artworkSizeSelect.options[artworkSizeSelect.selectedIndex].text : '';
								if (selectedText) {
									data.artwork_size_label = String(selectedText).trim();
								}
							}

							var frameDepthSelect = doc.querySelector('.frame_width_editor_select');
							if (frameDepthSelect) {
								var depthValue = '';
								if (typeof frameDepthSelect.value !== 'undefined') {
									depthValue = String(frameDepthSelect.value || '').trim();
								}
								if (!depthValue && frameDepthSelect.options && frameDepthSelect.selectedIndex >= 0) {
									depthValue = String(frameDepthSelect.options[frameDepthSelect.selectedIndex].text || '').trim();
								}
								if (depthValue) {
									data.frame_width = depthValue.replace(/"/g, '');
									data.frame_depth_label = depthValue;
								}
							}

							var matSelected = doc.querySelector('.selected_mat_color_display');
							if (matSelected) {
								var matText = String((matSelected.textContent || '')).replace(/^Selected:\s*/i, '').trim();
								if (matText && !data.mat_color) {
									data.mat_color = matText;
								}
							}

							var matTooltip = doc.querySelector('#new_mat_slider .noUi-tooltip');
							if (matTooltip) {
								var matBarText = String(matTooltip.textContent || '').trim();
								if (matBarText) {
									var matNum = matBarText.replace(/[^0-9.]/g, '');
									if (matNum) {
										data.mat_width = matNum;
										data.mat_bar_value = matNum;
									}
								}
							}

							var activeScene = doc.querySelector('.scene_control_swatch.active_scene_swatch');
							if (activeScene) {
								var sceneImg = activeScene.querySelector('img');
								if (sceneImg && sceneImg.getAttribute('src')) {
									try {
										data.scene_image_url = new URL(sceneImg.getAttribute('src'), iframe.contentWindow.location.href).href;
									} catch (e) {
										data.scene_image_url = sceneImg.getAttribute('src');
									}
								}
								var sceneName = activeScene.getAttribute('title') || activeScene.getAttribute('data-scene-name') || '';
								if (sceneName) {
									data.scene_name = String(sceneName).trim();
								}
							}

							var artworkLayer = doc.querySelector('.artwork_layer_1');
							if (artworkLayer && artworkLayer.style && artworkLayer.style.backgroundImage) {
								var bgImage = String(artworkLayer.style.backgroundImage || '');
								var match = bgImage.match(/url\((['"]?)(.*?)\1\)/i);
								if (match && match[2]) {
									var artworkUrl = String(match[2]).trim();
									if (artworkUrl && artworkUrl.indexOf('nautylus-watermark') === -1) {
										data.uploaded_artwork_url = artworkUrl;
									}
								}
							}
							if (iframe.contentWindow && iframe.contentWindow.__wdUploadedArtworkUrl) {
								var explicitUploadedUrl = String(iframe.contentWindow.__wdUploadedArtworkUrl || '').trim();
								if (explicitUploadedUrl) {
									data.uploaded_artwork_url = explicitUploadedUrl;
								}
							}
							if (iframe.contentWindow && iframe.contentWindow.__wdUploadedArtworkServerUrl) {
								var uploadedServerUrl = String(iframe.contentWindow.__wdUploadedArtworkServerUrl || '').trim();
								if (uploadedServerUrl) {
									data.uploaded_artwork_url = uploadedServerUrl;
								}
							}
							if (iframe.contentWindow && iframe.contentWindow.__wdUploadedArtworkDataUrl) {
								var uploadedDataUrl = String(iframe.contentWindow.__wdUploadedArtworkDataUrl || '').trim();
								if (uploadedDataUrl.indexOf('data:image/') === 0) {
									data.uploaded_artwork_data_url = uploadedDataUrl;
								}
							}
							if (iframe.contentWindow && iframe.contentWindow.__wdUploadedArtworkSelected) {
								data.uploaded_artwork_selected = '1';
							}

							return data;
						}

						function ensureMirroredPurchasePanel() {
							var iframe = document.getElementById('wd-frame-visualizer-iframe');
							var parentPanel = document.querySelector('.frame-product-summary-wrapper');
							if (!iframe || !iframe.contentDocument || !parentPanel) {
								return;
							}

							var iframeDoc = iframe.contentDocument;
							var rotationSlider = iframeDoc.querySelector('#rotation_slider');
							if (!rotationSlider) {
								return;
							}

							var originalPrice = parentPanel.querySelector('.price');
							var originalForm = parentPanel.querySelector('form.cart');
							var originalQtyInput = originalForm ? originalForm.querySelector('input.qty') : null;
							var originalAddBtn = originalForm ? originalForm.querySelector('.single_add_to_cart_button') : null;
							if (!originalForm || !originalAddBtn) {
								return;
							}

							var mirrorHost = iframeDoc.getElementById('wd-iframe-purchase-panel');
							if (!mirrorHost) {
								if (!iframeDoc.getElementById('wd-iframe-purchase-panel-style')) {
									var styleTag = iframeDoc.createElement('style');
									styleTag.id = 'wd-iframe-purchase-panel-style';
									styleTag.textContent = ''
										+ '#wd-iframe-purchase-panel{margin:16px 0 10px;padding:0;background:transparent;border:0;box-shadow:none}'
										+ '#wd-iframe-purchase-panel .wd-mirror-price{font-size:22px;font-weight:700;line-height:1.2;color:#111;margin:0 0 12px}'
										+ '#wd-iframe-purchase-panel .wd-mirror-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}'
										+ '#wd-iframe-purchase-panel .wd-mirror-qty{display:flex;align-items:center;gap:6px}'
										+ '#wd-iframe-purchase-panel .wd-mirror-qty button{width:36px;height:36px;border:1px solid #ddd;border-radius:8px;background:#fff;color:#111;font-size:18px;line-height:1;cursor:pointer;transition:all .2s ease}'
										+ '#wd-iframe-purchase-panel .wd-mirror-qty button:hover{background:#f5f5f5;border-color:#ccc}'
										+ '#wd-iframe-purchase-panel .wd-mirror-qty input{width:72px;height:36px;text-align:center;border:1px solid #ddd;border-radius:8px;font-size:14px}'
										+ '#wd-iframe-purchase-panel .wd-mirror-add-to-cart{height:38px;padding:0 18px;white-space:nowrap;background:#e91e63!important;border-color:#e91e63!important;color:#fff!important}'
										+ '#wd-iframe-purchase-panel .wd-mirror-add-to-cart:hover{background:#d81b60!important;border-color:#d81b60!important;color:#fff!important}'
										+ '@media (max-width:767px){#wd-iframe-purchase-panel .wd-mirror-row{flex-direction:column;align-items:stretch}#wd-iframe-purchase-panel .wd-mirror-qty{justify-content:center}#wd-iframe-purchase-panel .wd-mirror-add-to-cart{width:100%}}';
									iframeDoc.head.appendChild(styleTag);
								}

								mirrorHost = iframeDoc.createElement('div');
								mirrorHost.id = 'wd-iframe-purchase-panel';

								var priceEl = iframeDoc.createElement('div');
								priceEl.className = 'wd-mirror-price';
								mirrorHost.appendChild(priceEl);

								var row = iframeDoc.createElement('div');
								row.className = 'wd-mirror-row';

								var qtyWrap = iframeDoc.createElement('div');
								qtyWrap.className = 'wd-mirror-qty';

								var qtyMinus = iframeDoc.createElement('button');
								qtyMinus.type = 'button';
								qtyMinus.textContent = '-';

								var qtyInput = iframeDoc.createElement('input');
								qtyInput.type = 'number';
								qtyInput.min = '1';
								qtyInput.step = '1';
								qtyInput.value = originalQtyInput && originalQtyInput.value ? originalQtyInput.value : '1';

								var qtyPlus = iframeDoc.createElement('button');
								qtyPlus.type = 'button';
								qtyPlus.textContent = '+';

								qtyWrap.appendChild(qtyMinus);
								qtyWrap.appendChild(qtyInput);
								qtyWrap.appendChild(qtyPlus);
								row.appendChild(qtyWrap);

								var addBtn = iframeDoc.createElement('button');
								addBtn.type = 'button';
								addBtn.className = (originalAddBtn.className ? originalAddBtn.className + ' ' : '') + 'wd-mirror-add-to-cart';
								addBtn.textContent = originalAddBtn.textContent ? originalAddBtn.textContent.trim() : 'Add to cart';
								addBtn.style.cssText = 'background:#e91e63;color:#fff;border:0;border-radius:8px;';
								row.appendChild(addBtn);

								mirrorHost.appendChild(row);
								rotationSlider.parentNode.insertBefore(mirrorHost, rotationSlider.nextSibling);

								function syncToOriginal() {
									var value = parseInt(qtyInput.value || '1', 10);
									if (!value || value < 1) value = 1;
									qtyInput.value = String(value);
									if (originalQtyInput) {
										originalQtyInput.value = String(value);
										originalQtyInput.dispatchEvent(new Event('change', { bubbles: true }));
									}
								}

								qtyMinus.addEventListener('click', function() {
									qtyInput.value = String(Math.max(1, parseInt(qtyInput.value || '1', 10) - 1));
									syncToOriginal();
								});
								qtyPlus.addEventListener('click', function() {
									qtyInput.value = String(parseInt(qtyInput.value || '1', 10) + 1);
									syncToOriginal();
								});
								qtyInput.addEventListener('input', syncToOriginal);

								addBtn.addEventListener('click', function() {
									syncToOriginal();
									writePayloadToForm(originalForm);
									originalAddBtn.click();
								});
							}

							var mirroredPrice = mirrorHost.querySelector('.wd-mirror-price');
							if (mirroredPrice && originalPrice) {
								mirroredPrice.innerHTML = originalPrice.innerHTML;
							}
						}

						function writePayloadToForm(form) {
							if (!form) return;
							var payload = collectVisualizerData();
							var input = form.querySelector('input[name="wd_frame_visualizer_data"]');
							if (!input) {
								input = document.createElement('input');
								input.type = 'hidden';
								input.name = 'wd_frame_visualizer_data';
								form.appendChild(input);
							}
							input.value = JSON.stringify(payload || {});
						}

						function attachFrameDataOnSubmit(form) {
							if (!form) return;
							form.addEventListener('submit', function(event) {
								var iframe = document.getElementById('wd-frame-visualizer-iframe');
								if (iframe && iframe.contentWindow && iframe.contentWindow.__wdArtworkUploadInProgress) {
									alert('Image is still uploading. Please wait a moment and try again.');
									event.preventDefault();
									return false;
								}
								writePayloadToForm(form);
							});

							var addBtn = form.querySelector('.single_add_to_cart_button');
							if (addBtn) {
								addBtn.addEventListener('click', function(event) {
									var iframe = document.getElementById('wd-frame-visualizer-iframe');
									if (iframe && iframe.contentWindow && iframe.contentWindow.__wdArtworkUploadInProgress) {
										alert('Image is still uploading. Please wait a moment and try again.');
										event.preventDefault();
										return false;
									}
									writePayloadToForm(form);
								}, true);
							}
						}

						var cartForms = document.querySelectorAll('.frame-product-summary-wrapper form.cart');
						cartForms.forEach(function(form) {
							attachFrameDataOnSubmit(form);
						});

						enforcePurchasePanelPlacement();
						ensureMirroredPurchasePanel();
						setTimeout(enforcePurchasePanelPlacement, 300);
						setTimeout(ensureMirroredPurchasePanel, 300);
						setTimeout(enforcePurchasePanelPlacement, 1000);
						setTimeout(ensureMirroredPurchasePanel, 1000);

						// Keep hidden payload fresh for AJAX-based add-to-cart handlers.
						setInterval(function() {
							cartForms.forEach(function(form) {
								writePayloadToForm(form);
							});
							ensureMirroredPurchasePanel();
						}, 1200);
					})();
				</script>
			<?php else : ?>
				<?php wc_get_template_part( 'content', 'single-product' ); ?>
			<?php endif; ?>
		<?php else : ?>
			<?php wc_get_template_part( 'content', 'single-product' ); ?>
		<?php endif; ?>

	<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

<?php get_footer( 'shop' ); ?>