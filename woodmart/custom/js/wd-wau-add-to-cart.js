/**
 * Send WAU file uploads with add-to-cart (Woodmart uses serialize() which drops files).
 */
(function () {
	'use strict';

	function formHasWauFiles(form) {
		var inputs = form.querySelectorAll('input.wau-files[type="file"]');
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].files && inputs[i].files.length > 0) {
				return true;
			}
		}
		return false;
	}

	function isWoodmartAjaxAddToCartEnabled() {
		if (typeof woodmart_settings === 'undefined') {
			return false;
		}
		var v = woodmart_settings.ajax_add_to_cart;
		return v !== false && v !== 'no' && v !== '0' && v !== 0 && v !== '';
	}

	function handleWauCartSubmit(event) {
		var form = event.target;
		if (!form || !form.matches || !form.matches('form.cart')) {
			return;
		}

		if (!formHasWauFiles(form)) {
			return;
		}

		if (!isWoodmartAjaxAddToCartEnabled()) {
			return;
		}

		var wrapper = form.closest('.single-product-page');
		if (
			wrapper &&
			(wrapper.classList.contains('product-type-external') ||
				wrapper.classList.contains('product-type-zakeke'))
		) {
			return;
		}

		event.preventDefault();
		event.stopImmediatePropagation();

		if (typeof jQuery === 'undefined' || typeof woodmart_settings === 'undefined') {
			form.submit();
			return;
		}

		var $ = jQuery;
		var $form = $(form);
		var $btn = $form.find('.single_add_to_cart_button');
		var formData = new FormData(form);

		formData.append('action', 'woodmart_ajax_add_to_cart');

		if ($btn.val()) {
			formData.set('add-to-cart', $btn.val());
		}

		$btn.removeClass('added not-added').addClass('loading');
		$(document.body).trigger('adding_to_cart', [$btn, formData]);

		$.ajax({
			url: woodmart_settings.ajaxurl,
			data: formData,
			method: 'POST',
			processData: false,
			contentType: false,
			success: function (response) {
				if (!response) {
					return;
				}

				if (response.error && response.product_url) {
					window.location = response.product_url;
					return;
				}

				if (
					typeof wc_add_to_cart_params !== 'undefined' &&
					wc_add_to_cart_params.cart_redirect_after_add === 'yes'
				) {
					window.location = wc_add_to_cart_params.cart_url;
					return;
				}

				$btn.removeClass('loading');

				var fragments = response.fragments;
				var cartHash = response.cart_hash;

				if (fragments) {
					$.each(fragments, function (key) {
						$(key).addClass('updating');
					});
					$.each(fragments, function (key, value) {
						$(key).replaceWith(value);
					});
				}

				if (response.notices && response.notices.indexOf('error') > 0) {
					$('body').append(response.notices);
					$btn.addClass('not-added');
				} else {
					if (woodmart_settings.add_to_cart_action === 'widget' && $.magnificPopup) {
						$.magnificPopup.close();
					}
					$btn.addClass('added');
					$(document.body).trigger('added_to_cart', [fragments, cartHash, $btn]);
				}
			},
			error: function () {
				$btn.removeClass('loading');
				// Fallback: normal POST so files still reach the server.
				form.submit();
			},
		});
	}

	document.addEventListener('submit', handleWauCartSubmit, true);
})();
