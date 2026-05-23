/**
 * WAU: compress on file select, pre-upload immediately, fast add-to-cart (IDs only).
 */
(function () {
	'use strict';

	var cfg = window.wdWauCompress || {
		ajaxurl: '',
		nonce: '',
		maxWidth: 1920,
		maxHeight: 1920,
		quality: 0.82,
		minQuality: 0.55,
		targetMaxBytes: 450000,
		skipBelowBytes: 180000,
		i18n: {
			optimizing: 'Optimizing…',
			uploading: 'Uploading…',
			ready: 'Ready',
			error: 'Upload failed',
			waiting: 'Please wait for uploads to finish',
		},
	};

	var IMAGE_TYPES = /^image\/(jpeg|jpg|png|webp|bmp|heic|heif)$/i;
	var COMPRESS_SKIP = /^(image\/gif|image\/svg\+xml|application\/pdf)$/i;

	/** input element -> { ids: number[], busy: number } */
	var inputState = typeof WeakMap !== 'undefined' ? new WeakMap() : null;
	var inputStateFallback = [];

	function getInputState(input) {
		if (inputState) {
			if (!inputState.has(input)) {
				inputState.set(input, { ids: [], busy: 0 });
			}
			return inputState.get(input);
		}
		for (var i = 0; i < inputStateFallback.length; i++) {
			if (inputStateFallback[i].el === input) {
				return inputStateFallback[i].state;
			}
		}
		var st = { ids: [], busy: 0 };
		inputStateFallback.push({ el: input, state: st });
		return st;
	}

	function formatBytes(n) {
		if (n < 1024) {
			return n + ' B';
		}
		if (n < 1048576) {
			return Math.round(n / 1024) + ' KB';
		}
		return (n / 1048576).toFixed(1) + ' MB';
	}

	function ensureUploadStyles() {
		if (document.getElementById('wd-wau-upload-styles')) {
			return;
		}
		var style = document.createElement('style');
		style.id = 'wd-wau-upload-styles';
		style.textContent =
			'.wd-wau-upload-status{display:flex;align-items:center;gap:12px;margin:12px 0 14px;padding:14px 16px;' +
			'border-radius:8px;font-size:15px;font-weight:600;line-height:1.35;max-width:420px;' +
			'box-shadow:0 2px 8px rgba(0,0,0,.08);transition:background .2s,border-color .2s}' +
			'.wd-wau-upload-status--loading{color:#0d47a1;background:#e3f2fd;border:2px solid #64b5f6}' +
			'.wd-wau-upload-status--success{color:#1b5e20;background:#e8f5e9;border:2px solid #66bb6a}' +
			'.wd-wau-upload-status--error{color:#b71c1c;background:#ffebee;border:2px solid #ef5350}' +
			'.wd-wau-upload-status.is-hidden{display:none!important}' +
			'.wd-wau-spinner{width:28px;height:28px;flex-shrink:0;border:3px solid rgba(13,71,161,.2);' +
			'border-top-color:#1565c0;border-radius:50%;animation:wd-wau-spin .75s linear infinite}' +
			'@keyframes wd-wau-spin{to{transform:rotate(360deg)}}' +
			'.wd-wau-status-icon{display:flex;align-items:center;justify-content:center;width:28px;height:28px;' +
			'flex-shrink:0;border-radius:50%;font-size:16px;font-weight:700}' +
			'.wd-wau-status-icon--ok{color:#fff;background:#43a047}' +
			'.wd-wau-status-icon--err{color:#fff;background:#e53935}' +
			'.wd-wau-status-text{flex:1}' +
			'.wd-wau-file-row--busy{opacity:.85;pointer-events:none}';
		document.head.appendChild(style);
	}

	function uploadRowForInput(input) {
		return input.closest('.wau-upload-wrap, .wau-field, .wau-file-upload, .form-row, p') || input.parentElement;
	}

	function statusElForInput(input) {
		ensureUploadStyles();
		var row = uploadRowForInput(input);
		var el = row ? row.querySelector('.wd-wau-upload-status') : null;
		if (!el) {
			el = document.createElement('div');
			el.className = 'wd-wau-upload-status is-hidden';
			el.setAttribute('role', 'status');
			el.setAttribute('aria-live', 'polite');
			input.insertAdjacentElement('afterend', el);
		}
		return el;
	}

	function setRowBusy(input, busy) {
		var row = uploadRowForInput(input);
		if (!row) {
			return;
		}
		if (busy) {
			row.classList.add('wd-wau-file-row--busy');
		} else {
			row.classList.remove('wd-wau-file-row--busy');
		}
	}

	/**
	 * @param {HTMLInputElement} input
	 * @param {string} text
	 * @param {'idle'|'loading'|'success'|'error'} state
	 */
	function setStatus(input, text, state) {
		var el = statusElForInput(input);
		if (!el) {
			return;
		}

		if (!text || state === 'idle') {
			el.className = 'wd-wau-upload-status is-hidden';
			el.innerHTML = '';
			setRowBusy(input, false);
			return;
		}

		setRowBusy(input, state === 'loading');

		el.className = 'wd-wau-upload-status wd-wau-upload-status--' + state;

		if (state === 'loading') {
			el.innerHTML =
				'<span class="wd-wau-spinner" aria-hidden="true"></span>' +
				'<span class="wd-wau-status-text">' + text + '</span>';
			return;
		}

		if (state === 'success') {
			el.innerHTML =
				'<span class="wd-wau-status-icon wd-wau-status-icon--ok" aria-hidden="true">✓</span>' +
				'<span class="wd-wau-status-text">' + text + '</span>';
			return;
		}

		if (state === 'error') {
			el.innerHTML =
				'<span class="wd-wau-status-icon wd-wau-status-icon--err" aria-hidden="true">!</span>' +
				'<span class="wd-wau-status-text">' + text + '</span>';
		}
	}

	function formHasWauFiles(form) {
		var inputs = form.querySelectorAll('input.wau-files[type="file"]');
		for (var i = 0; i < inputs.length; i++) {
			var st = getInputState(inputs[i]);
			if (st.ids.length > 0) {
				return true;
			}
			if (inputs[i].files && inputs[i].files.length > 0) {
				return true;
			}
		}
		return false;
	}

	function formHasPendingUploads(form) {
		var inputs = form.querySelectorAll('input.wau-files[type="file"]');
		for (var i = 0; i < inputs.length; i++) {
			if (getInputState(inputs[i]).busy > 0) {
				return true;
			}
		}
		return false;
	}

	function collectPreuploadIds(form) {
		var ids = [];
		var inputs = form.querySelectorAll('input.wau-files[type="file"]');
		for (var i = 0; i < inputs.length; i++) {
			var st = getInputState(inputs[i]);
			for (var j = 0; j < st.ids.length; j++) {
				ids.push(st.ids[j]);
			}
		}
		return ids;
	}

	function syncFormPreuploadHiddenInputs(form) {
		if (!form) {
			return;
		}
		form.querySelectorAll('input.wd-wau-preupload-id').forEach(function (el) {
			el.remove();
		});
		var ids = collectPreuploadIds(form);
		for (var i = 0; i < ids.length; i++) {
			var hidden = document.createElement('input');
			hidden.type = 'hidden';
			hidden.name = 'wd_wau_preupload_ids[]';
			hidden.className = 'wd-wau-preupload-id';
			hidden.value = String(ids[i]);
			form.appendChild(hidden);
		}
	}

	function isWoodmartAjaxAddToCartEnabled() {
		if (typeof woodmart_settings === 'undefined') {
			return false;
		}
		var v = woodmart_settings.ajax_add_to_cart;
		return v !== false && v !== 'no' && v !== '0' && v !== 0 && v !== '';
	}

	function compressImageFile(file) {
		return new Promise(function (resolve) {
			if (!file || !IMAGE_TYPES.test(file.type) || COMPRESS_SKIP.test(file.type)) {
				resolve(file);
				return;
			}
			if (file.size > 0 && file.size <= cfg.skipBelowBytes) {
				resolve(file);
				return;
			}

			var reader = new FileReader();
			reader.onerror = function () {
				resolve(file);
			};
			reader.onload = function () {
				var img = new Image();
				img.onerror = function () {
					resolve(file);
				};
				img.onload = function () {
					var w = img.width;
					var h = img.height;
					var maxW = cfg.maxWidth || 1920;
					var maxH = cfg.maxHeight || 1920;
					var scale = Math.min(1, maxW / w, maxH / h);
					var cw = Math.max(1, Math.round(w * scale));
					var ch = Math.max(1, Math.round(h * scale));
					var canvas = document.createElement('canvas');
					canvas.width = cw;
					canvas.height = ch;
					var ctx = canvas.getContext('2d');
					ctx.drawImage(img, 0, 0, cw, ch);

					var quality = cfg.quality || 0.82;
					var minQ = cfg.minQuality || 0.55;
					var target = cfg.targetMaxBytes || 450000;

					function tryBlob(q, attempt) {
						canvas.toBlob(
							function (blob) {
								if (!blob) {
									resolve(file);
									return;
								}
								if (blob.size > target && q > minQ && attempt < 6) {
									tryBlob(Math.max(minQ, q - 0.08), attempt + 1);
									return;
								}
								var name = (file.name || 'upload.jpg').replace(/\.[^.]+$/, '') + '.jpg';
								resolve(
									new File([blob], name, {
										type: 'image/jpeg',
										lastModified: Date.now(),
									})
								);
							},
							'image/jpeg',
							q
						);
					}
					tryBlob(quality, 0);
				};
				img.src = reader.result;
			};
			reader.readAsDataURL(file);
		});
	}

	function preuploadFile(file) {
		var fd = new FormData();
		fd.append('action', 'wd_wau_preupload');
		fd.append('nonce', cfg.nonce || '');
		fd.append('file', file, file.name || 'upload.jpg');

		return fetch(cfg.ajaxurl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: fd,
			credentials: 'same-origin',
		}).then(function (res) {
			return res.json();
		});
	}

	function processInputFiles(input) {
		var form = input.closest('form.cart');
		var files = input.files;
		if (!files || !files.length) {
			getInputState(input).ids = [];
			setStatus(input, '', 'idle');
			if (form) {
				syncFormPreuploadHiddenInputs(form);
			}
			return;
		}

		var st = getInputState(input);
		var total = files.length;
		var processed = 0;
		st.ids = [];
		st.busy = total;
		setStatus(input, cfg.i18n.optimizing, 'loading');

		var chain = Promise.resolve();
		var uploaded = [];
		var hadError = false;
		var lastErrorMsg = '';
		var lastFileSize = 0;

		function progressLabel(phase) {
			if (total <= 1) {
				return phase;
			}
			return phase + ' (' + Math.min(processed + 1, total) + ' / ' + total + ')';
		}

		function finishBatch() {
			if (st.busy > 0) {
				return;
			}
			if (!hadError && uploaded.length > 0) {
				var readyText = cfg.i18n.ready;
				if (total > 1) {
					readyText += ' — ' + uploaded.length + ' ' + (cfg.i18n.filesUploaded || 'files uploaded');
				} else if (lastFileSize > 0) {
					readyText += ' (' + formatBytes(lastFileSize) + ')';
				}
				setStatus(input, readyText, 'success');
				return;
			}
			setStatus(input, lastErrorMsg || cfg.i18n.error, 'error');
		}

		for (var i = 0; i < files.length; i++) {
			(function (file) {
				chain = chain
					.then(function () {
						setStatus(input, progressLabel(cfg.i18n.optimizing), 'loading');
						return compressImageFile(file);
					})
					.then(function (compressed) {
						setStatus(input, progressLabel(cfg.i18n.uploading), 'loading');
						return preuploadFile(compressed);
					})
					.then(function (json) {
						processed += 1;
						st.busy = Math.max(0, st.busy - 1);
						if (json && json.success && json.data && json.data.id) {
							uploaded.push(json.data.id);
							st.ids = uploaded.slice();
							if (json.data.size) {
								lastFileSize = json.data.size;
							}
							if (form) {
								syncFormPreuploadHiddenInputs(form);
							}
						} else {
							hadError = true;
							lastErrorMsg = (json && json.data && json.data.message) || cfg.i18n.error;
						}
						finishBatch();
					})
					.catch(function () {
						processed += 1;
						st.busy = Math.max(0, st.busy - 1);
						hadError = true;
						lastErrorMsg = cfg.i18n.error;
						finishBatch();
					});
			})(files[i]);
		}
	}

	function bindFileInputs() {
		document.querySelectorAll('input.wau-files[type="file"]').forEach(function (input) {
			if (input.dataset.wdWauBound) {
				return;
			}
			input.dataset.wdWauBound = '1';
			input.addEventListener('change', function () {
				processInputFiles(input);
			});
		});
	}

	function buildLeanFormData(form) {
		var fd = new FormData();

		Array.prototype.forEach.call(form.elements, function (el) {
			if (!el.name || el.disabled) {
				return;
			}
			if (el.type === 'file') {
				return;
			}
			if (el.classList && el.classList.contains('wd-wau-preupload-id')) {
				fd.append(el.name, el.value);
				return;
			}
			var tag = el.tagName;
			if (tag === 'SELECT' && el.multiple) {
				Array.prototype.forEach.call(el.selectedOptions, function (opt) {
					fd.append(el.name, opt.value);
				});
				return;
			}
			if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) {
				return;
			}
			if (tag === 'BUTTON' && el.type === 'submit') {
				return;
			}
			fd.append(el.name, el.value);
		});

		return fd;
	}

	function handleWauCartSubmit(event) {
		var form = event.target;
		if (!form || !form.matches || !form.matches('form.cart')) {
			return;
		}

		if (!formHasWauFiles(form)) {
			return;
		}

		if (formHasPendingUploads(form)) {
			event.preventDefault();
			event.stopImmediatePropagation();
			alert(cfg.i18n.waiting);
			return;
		}

		var preuploadIds = collectPreuploadIds(form);
		if (!preuploadIds.length) {
			event.preventDefault();
			event.stopImmediatePropagation();
			alert(cfg.i18n.error);
			return;
		}

		syncFormPreuploadHiddenInputs(form);

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
		var formData = buildLeanFormData(form);

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
				form.submit();
			},
		});
	}

	document.addEventListener('submit', handleWauCartSubmit, true);
	document.addEventListener('DOMContentLoaded', bindFileInputs);
	if (document.readyState !== 'loading') {
		bindFileInputs();
	}

	var observer =
		typeof MutationObserver !== 'undefined'
			? new MutationObserver(function () {
					bindFileInputs();
			  })
			: null;
	if (observer && document.body) {
		observer.observe(document.body, { childList: true, subtree: true });
	}
})();
