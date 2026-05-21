var woodmartAdminModule, woodmart_media_init;

(function($) {
    "use strict";
    // Class for import box element.
    var ImportBox = function( $form ) {

        this.form = $form;

        this.interval = 0;

        this.sequence = false;

        this.mainArea = $('.woodmart-main-import-area');

        this.responseArea = this.form.find('.woodmart-response');

        this.progressBar = this.form.find('.woodmart-import-progress');

        this.verSelect = this.form.find('.woodmart_version');

        this.pagePreviews();

        // Events 
        $form.on('submit', { importBox: this }, this.formSubmit )

    };

    ImportBox.prototype.formSubmit = function(e) {
        e.preventDefault();

        var importBox = e.data.importBox;

        var $form = importBox.form;

        if( $form.hasClass('form-in-action') ) return;

        $form.addClass('form-in-action');

        var version = $form.find('.woodmart_version').val();

        if( $form.find('#full_import').prop('checked') == true ) {
            importBox.sequence = true;
            version = $form.find('.woodmart_versions').val();

            var subLenght = 3;

            var ajaxSuccess = function( response ) {

                if( ! versions[i] ) {
                    // importBox.handleResponse(response);
                    // importBox.responseArea.html( '' ).fadeIn();
                }
            };

            var ajaxComplete = function() {

                if( ! versions[i] ) {

                    importBox.form.removeClass('form-in-action');

                    importBox.updateProgress( importBox.progressBar, 100, 0 );

                    importBox.initialClearer = setTimeout(function() {
                        importBox.destroyProgressBar(200);
                    }, 2000 );

                    importBox.mainArea.addClass( "imported-full right-after-import imported-" +  versions.join(" imported-") );
                    importBox.mainArea.find('.full-import-box').remove();
                } else {
                    importBox.updateProgress( importBox.progressBar, progressSteps * i, 350 );
                    importBox.callImportAJAX( versions.slice(i, i + subLenght).join(','), ajaxSuccess, ajaxComplete );
                    i = i + subLenght;
                }
            };

            var versions = version.split(',');
            var i = 0;
            var progressSteps = 95 / versions.length;

            importBox.callImportAJAX( versions[i++], ajaxSuccess, ajaxComplete );

            importBox.updateProgress( importBox.progressBar, progressSteps, 350 );

            return;
        }

        clearInterval( importBox.initialClearer );

        importBox.fakeLoading( 30, 50, 70 );

        importBox.clearResponseArea();

        importBox.callImportAJAX( version, function(response) {

            importBox.clearResponseArea();
            importBox.handleResponse(response);

        }, function() {

            importBox.clearFakeLoading();

            importBox.form.removeClass('form-in-action');

            importBox.updateProgress( importBox.progressBar, 100, 0 );

            importBox.progressBar.parent().find('.woodmart-notice').remove();

            importBox.mainArea.addClass( "right-after-import imported-" +  version );
            // importBox.mainArea.removeClass( "imported-full");

            importBox.initialClearer = setTimeout(function() {
                importBox.destroyProgressBar(200);
            }, 2000 );
        } );
    };


    ImportBox.prototype.callImportAJAX = function( version, success, complete ) {
        var box = this;

        $.ajax({
            url: woodmartConfig.ajax,
            data: {
                woodmart_version: version,
                action: "woodmart_import_data",
				sequence: box.sequence,
				security: woodmartConfig.import_nonce,
            },
            timeout: 1000000,
            success: function( response ) {

                if( success ) success( response );

            },
            error: function( response ) {
                box.responseArea.html( '<div class="woodmart-warning">Import AJAX problem. Please, try import data manually.</div>' ).fadeIn();
                console.log('import ajax ERROR');
            },
            complete: function() {

                if( complete ) complete();

                //console.log('import ajax complete');
            },
        });
    };

    ImportBox.prototype.handleResponse = function( response ) {
        var rJSON = { status: '', message: '' };

        try {
            rJSON = JSON.parse(response);
        } catch( e ) {}

        if( ! response ) {
            this.responseArea.html( '<div class="woodmart-warning">Empty AJAX response, please try again.</div>' ).fadeIn();
        } else if( rJSON.status == 'success' ) {
            console.log(rJSON.message);
            this.responseArea.html( '<div class="woodmart-success">All data imported successfully!</div>' ).fadeIn();
        } else if( rJSON.status == 'fail' ) {
            this.responseArea.html( '<div class="woodmart-error">' + rJSON.message + '</div>' ).fadeIn();
        } else {
            this.responseArea.html( '<div class="">' + response + '</div>' ).fadeIn();
        }

    };


    ImportBox.prototype.fakeLoading = function(fake1progress, fake2progress, noticeProgress) {
        var that = this;

        this.destroyProgressBar(0);

        this.updateProgress( this.progressBar, fake1progress, 350 );

        this.fake2timeout = setTimeout( function() {
            that.updateProgress( that.progressBar, fake2progress, 100 );
        }, 25000 );

        this.noticeTimeout = setTimeout( function() {
            that.updateProgress( that.progressBar, noticeProgress, 100 );
            that.progressBar.after( '<p class="woodmart-notice small">Please, wait. Theme needs much time to download all attachments</p>' );
        }, 60000 );

        this.errorTimeout = setTimeout( function() {
            that.progressBar.parent().find('.woodmart-notice').remove();
            that.progressBar.after( '<p class="woodmart-notice small">Something wrong with import. Please, try to import data manually</p>' );
        }, 3100000 );
    };

    ImportBox.prototype.clearFakeLoading = function() {
        clearTimeout( this.fake2timeout );
        clearTimeout( this.noticeTimeout );
        clearTimeout( this.errorTimeout );
    };

    ImportBox.prototype.destroyProgressBar = function( hide ) {
        this.progressBar.hide( hide ).attr('data-progress', 0).find('div').width(0);
    };

    ImportBox.prototype.clearResponseArea = function() {
        this.responseArea.fadeOut(200, function() {
            $(this).html( '' );
        });
    };

    ImportBox.prototype.updateProgress = function( el, to, interval ) {
        el.show();
        var box = this;

        clearInterval( box.interval );

        var from = el.attr('data-progress'),
            i = from;

        if( interval == 0 ) {
            el.attr('data-progress', 100).find('div').width(el.attr('data-progress') + '%');
        } else {
            box.interval = setInterval(function() {
                i++;
                el.attr('data-progress', i).find('div').width(el.attr('data-progress') + '%');
                if( i >= to ) clearInterval( box.interval );
            }, interval);
        }

    };

    ImportBox.prototype.pagePreviews = function() {
        var preview = this.form.find('.page-preview'),
            image = preview.find('img'),
            dir = image.data('dir'),
            newImage = '';

        image.on('load', function() {
          // do stuff on success
            $(this).removeClass('loading-image');
        }).on('error', function() {
          // do stuff on smth wrong (error 404, etc.)
            $(this).removeClass('loading-image');
        }).each(function() {
            if(this.complete) {
              $(this).load();
            } else if(this.error) {
              $(this).error();
            }
        });

        this.verSelect.on('change', function() {
            var page = $(this).val();

            if( page == '' || page == '--select--' ) page = 'base';

            newImage = dir + page + '/preview.jpg';

            image.addClass('loading-image').attr('src', newImage);
        });
    };


    $.fn.import_box = function() {
        new ImportBox( this );
        return this;
    };

    woodmartAdminModule = (function() {

        var woodmartAdmin = {

            listElement : function(){
                var $editor = $( '#vc_ui-panel-edit-element' );

                $editor.on( 'vcPanel.shown', function() {
                    if( $editor.attr( 'data-vc-shortcode' ) != 'woodmart_list' ) return;

                    var $groupField = $editor.find( '[data-param_type="param_group"]' ),
                        $groupFieldOpenBtn = $groupField.find( '.column_toggle:first' );

                    setTimeout( function() {
                        $groupFieldOpenBtn.click();
                    }, 300 );
                } );
            },

            sizeGuideInit : function(){
                if ( $.fn.editTable ) {
                    $( '.woodmart-sguide-table-edit' ).each( function() {
                        $( this ).editTable();
                    } );
                }
            },

            importAction: function() {

                $('.woodmart-import-form').each(function() {
                    $(this).import_box();
                })
            },

            attributesMetaboxes: function() {

                if( ! $('body').hasClass('product_page_product_attributes') ) return;

                var orderByRow = $('#attribute_orderby').parent(),
                    orderByTableRow = $('#attribute_orderby').parents('tr'),
                    //Select swatch size
                    selectedSize = ( woodmartConfig.attributeSwatchSize != undefined && woodmartConfig.attributeSwatchSize.length > 1 ) ? woodmartConfig.attributeSwatchSize : '',
                    labelSelectSize = '<label for="attribute_swatch_size">Attributes swatch size</label>',
                    descriptionSelectSize = '<p class="description">If you will set color or images swatches for terms of this attribute.</p>',
                    selectSize = [
                        '<select name="attribute_swatch_size" id="attribute_swatch_size">',
                            '<option value="default"' + (( selectedSize == 'default' ) ?  ' selected="selected"' : '') + '>Default</option>',
                            '<option value="large"' + (( selectedSize == 'large' ) ?  ' selected="selected"' : '') + '>Large</option>',
                            '<option value="xlarge"' + (( selectedSize == 'xlarge' ) ?  ' selected="selected"' : '') + '>Extra large</option>',
                        '</select>',
                    ].join(''),
                    //Checkbox show attribute on product
                    showOnProduct = ( woodmartConfig.attributeShowOnProduct != undefined && woodmartConfig.attributeShowOnProduct.length > 1 ) ? woodmartConfig.attributeShowOnProduct : '',
                    labelShowAttr = '<label for="attribute_show_on_product">Show attribute label on products</label>',
                    checkboxShowAttr = '<input' + ( ( showOnProduct == 'on' ) ?  ' checked="checked"' : '' ) + ' name="attribute_show_on_product" id="attribute_show_on_product" type="checkbox">',
                    descriptionShowAttr = '<p class="description">Enable this if you want to show this attribute label on products in your store.</p>',
                    metaHTMLTable = [
                        //Select swatch size
                        '<tr class="form-field form-required">',
                            '<th scope="row" valign="top">',
                                labelSelectSize,
                            '</th>',
                            '<td>',
                                selectSize,
                                descriptionSelectSize,
                            '</td>',
                        '</tr>',

                        //Checkbox show attribute on product
                        '<tr class="form-field form-required">',
                            '<th scope="row" valign="top">',
                                labelShowAttr,
                            '</th>',
                            '<td>',
                                checkboxShowAttr,
                                descriptionShowAttr,
                            '</td>',
                        '</tr>',
                    ].join(''),

                    metaHTMLParagraph = [
                        //Select swatch size
                        '<div class="form-field">',
                            labelSelectSize,
                            selectSize,
                            descriptionSelectSize,
                        '</div>',

                        //Checkbox show attribute on product
                        '<div class="form-field">',
                            labelShowAttr,
                            checkboxShowAttr,
                            descriptionShowAttr,
                        '</div>',
                    ].join('');

                if( orderByTableRow.length > 0 ) {
                    orderByTableRow.after( metaHTMLTable );
                } else {
                    orderByRow.after( metaHTMLParagraph );
                }
			},

			variationGallery: function () {

				$('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {

					$('.woodmart-variation-gallery-wrapper').each(function () {

						var $this = $(this);
						var $galleryImages = $this.find('.woodmart-variation-gallery-images');
						var $imageGalleryIds = $this.find('.variation-gallery-ids');
						var galleryFrame;

						$this.find('.woodmart-add-variation-gallery-image').on('click', function (event) {
							event.preventDefault();

							// If the media frame already exists, reopen it.
							if (galleryFrame) {
								galleryFrame.open();
								return;
							}

							// Create the media frame.
							galleryFrame = wp.media.frames.product_gallery = wp.media({
								states: [
									new wp.media.controller.Library({
										filterable: 'all',
										multiple: true
									})
								]
							});

							// When an image is selected, run a callback.
							galleryFrame.on('select', function () {
								var selection = galleryFrame.state().get('selection');
								var attachment_ids = $imageGalleryIds.val();

								selection.map(function (attachment) {
									attachment = attachment.toJSON();

									if (attachment.id) {
										var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
										attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;

										$galleryImages.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '"><a href="#" class="delete woodmart-remove-variation-gallery-image"><span class="dashicons dashicons-dismiss"></span></a></li>');

										$this.trigger('woodmart_variation_gallery_image_added');
									}
								});

								$imageGalleryIds.val(attachment_ids);

								$this.parents('.woocommerce_variation').eq(0).addClass('variation-needs-update');
								$('#variable_product_options').find('input').eq(0).change();

							});

							// Finally, open the modal.
							galleryFrame.open();
						});

						// Image ordering.
						if (typeof $galleryImages.sortable !== 'undefined') {
							$galleryImages.sortable({
								items: 'li.image',
								cursor: 'move',
								scrollSensitivity: 40,
								forcePlaceholderSize: true,
								forceHelperSize: false,
								helper: 'clone',
								opacity: 0.65,
								placeholder: 'wc-metabox-sortable-placeholder',
								start: function (event, ui) {
									ui.item.css('background-color', '#f6f6f6');
								},
								stop: function (event, ui) {
									ui.item.removeAttr('style');
								},
								update: function () {
									var attachment_ids = '';

									$galleryImages.find('li.image').each(function () {
										var attachment_id = $(this).attr('data-attachment_id');
										attachment_ids = attachment_ids + attachment_id + ',';
									});

									$imageGalleryIds.val(attachment_ids);

									$this.parents('.woocommerce_variation').eq(0).addClass('variation-needs-update');
									$('#variable_product_options').find('input').eq(0).change();
								}
							});
						}

						// Remove images.
						$(document).on('click', '.woodmart-remove-variation-gallery-image', function (event) {
							event.preventDefault();
							$(this).parent().remove();

							var attachment_ids = '';

							$galleryImages.find('li.image').each(function () {
								var attachment_id = $(this).attr('data-attachment_id');
								attachment_ids = attachment_ids + attachment_id + ',';
							});

							$imageGalleryIds.val(attachment_ids);

							$this.parents('.woocommerce_variation').eq(0).addClass('variation-needs-update');
							$('#variable_product_options').find('input').eq(0).change();
						});

					});

				});
			},

            product360ViewGallery: function() {

                // Product gallery file uploads.
                var product_gallery_frame;
                var $image_gallery_ids = $( '#product_360_image_gallery' );
                var $product_images    = $( '#product_360_images_container' ).find( 'ul.product_360_images' );

                $( '.add_product_360_images' ).on( 'click', 'a', function( event ) {
                    var $el = $( this );

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( product_gallery_frame ) {
                        product_gallery_frame.open();
                        return;
                    }

                    // Create the media frame.
                    product_gallery_frame = wp.media.frames.product_gallery = wp.media({
                        // Set the title of the modal.
                        title: $el.data( 'choose' ),
                        button: {
                            text: $el.data( 'update' )
                        },
                        states: [
                            new wp.media.controller.Library({
                                title: $el.data( 'choose' ),
                                filterable: 'all',
                                multiple: true
                            })
                        ]
                    });

                    // When an image is selected, run a callback.
                    product_gallery_frame.on( 'select', function() {
                        var selection = product_gallery_frame.state().get( 'selection' );
                        var attachment_ids = $image_gallery_ids.val();

                        selection.map( function( attachment ) {
                            attachment = attachment.toJSON();

                            if ( attachment.id ) {
                                attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                                var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                                $product_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                            }
                        });

                        $image_gallery_ids.val( attachment_ids );
                    });

                    // Finally, open the modal.
                    product_gallery_frame.open();
                });

                // Image ordering.
                if ( typeof $product_images.sortable !== 'undefined' ) {
                    $product_images.sortable({
                        items: 'li.image',
                        cursor: 'move',
                        scrollSensitivity: 40,
                        forcePlaceholderSize: true,
                        forceHelperSize: false,
                        helper: 'clone',
                        opacity: 0.65,
                        placeholder: 'wc-metabox-sortable-placeholder',
                        start: function( event, ui ) {
                            ui.item.css( 'background-color', '#f6f6f6' );
                        },
                        stop: function( event, ui ) {
                            ui.item.removeAttr( 'style' );
                        },
                        update: function() {
                            var attachment_ids = '';

                            $( '#product_360_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                                var attachment_id = $( this ).attr( 'data-attachment_id' );
                                attachment_ids = attachment_ids + attachment_id + ',';
                            });

                            $image_gallery_ids.val( attachment_ids );
                        }
                    });
                }

                // Remove images.
                $( '#product_360_images_container' ).on( 'click', 'a.delete', function() {
                    $( this ).closest( 'li.image' ).remove();

                    var attachment_ids = '';

                    $( '#product_360_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                        var attachment_id = $( this ).attr( 'data-attachment_id' );
                        attachment_ids = attachment_ids + attachment_id + ',';
                    });

                    $image_gallery_ids.val( attachment_ids );

                    // Remove any lingering tooltips.
                    $( '#tiptip_holder' ).removeAttr( 'style' );
                    $( '#tiptip_arrow' ).removeAttr( 'style' );

                    return false;
                });
            },

            multiFonts: function () {
                $('.redux-container-woodmart_multi_fonts').each(function () {
                    var el = $(this);

                    redux.field_objects.media.init();

                    el.find('.woodmart-multi-fonts-remove').live('click', function () {
                        $(this).parents('.woodmart-miltifont-repeater').slideUp('medium', function () {
                            $(this).remove();
                        });
                        redux.field_objects.media.init();
                    });

                    el.find('.woodmart-multi-fonts-add').click(function () {
                        var _this = $(this);
                        var id = _this.attr('data-id');
                        var order = parseInt(_this.attr('data-order')) + 1;
                        var name = _this.attr('data-name');
                        var mediaId = '_' + Math.random().toString(36).substr(2, 9);
                        var i = 0;
                        var fields = [
                            '[font-name]',
                            '[font-woff]',
                            '[font-woff2]',
                            '[font-ttf]',
                            '[font-svg]',
                            '[font-eot]',
                        ];
                        var cloneStandart = $('#' + id + ' .woodmart-miltifont-repeater:last-child');
                        el.find('#' + id).append(cloneStandart.clone());

                        //Сleaning clone element after clone
                        var lastElement = el.find('#' + id + ' .woodmart-miltifont-repeater:last-child');
                        lastElement.removeAttr('style');
                        lastElement.find('select').attr('name', name + '[' + order + '][font-weight]');
                        lastElement.find('input[type="text"]').each(function () {
                            var url = (fields[i] != '[font-name]') ? '[url]' : '';
                            $(this).parent().attr('id', mediaId);
                            $(this).attr('name', name + '[' + order + ']' + fields[i] + url);
                            $(this).siblings('.upload-id').attr('name', name + '[' + order + ']' + fields[i] + '[id]');
                            $(this).val('');
                            i++;
                        });

                        //Accordion after clone
                        lastElement.siblings().removeClass('active');
                        lastElement.addClass('active');

                        _this.attr('data-order', order);
                        redux.field_objects.media.init();
                    });
                });
            },

            multiFontsAccordion: function () {
                $(document).on('click', '.woodmart-miltifonts-accordion', function () {
                    var $parent = $(this).parent();
                    $parent.toggleClass('active');
                    $parent.siblings().removeClass('active');
                });
            },

            advancedTypography: function() {
                var isSelecting = false,
                    selVals = [],
                    select2Defaults = {
                        width: '100%',
                        triggerChange: true,
                        allowClear: true
                    },
                    defaultVariants = {
                        '100': 'Thin 100',
                        '200': 'Light 200',
                        '300': 'Regular 300',
                        '400': 'Medium 400',
                        '500': 'Normal 500',
                        '600': 'Normal 600',
                        '700': 'Bold 700',
                        '800': 'Black 800',
                        '900': 'Black 900',
                        '100italic': 'Thin 100 Italic',
                        '200italic': 'Light 200 Italic',
                        '300italic': 'Regular 300 Italic',
                        '400italic': 'Medium 400 Italic',
                        '500italic': 'Normal 500 Italic',
                        '600italic': 'Normal 600 Italic',
                        '700italic': 'Bold 700 Italic',
                        '800italic': 'Black 800 Italic',
                        '900italic': 'Black 900 Italic',
                    };

                $('.woodmart-advanced-typography-field').each(function() {
                    var $parent = $(this);

                    $parent.find('.woodmart-atf-section:not(.woodmart-atf-template)').each(function() {
                        var $section = $(this),
                            id = $section.data('id');

                        initTypographySection($parent, id);
                    });

                    $parent.on('click', '.woodmart-atf-btn-add', function(e) {
                        e.preventDefault();

                        var $template = $parent.find('.woodmart-atf-template').clone(),
                            key = $parent.data('key') + 1;

                        $parent.find('.woodmart-atf-sections').append($template);
                        var regex = /{{index}}/gi;

                        $template
                            .removeClass('woodmart-atf-template hide')
                            .html($template.html().replace(regex, key))
                            .attr('data-id', $template.attr('data-id').replace(regex, key));

                        $parent.data('key', key);

                        initTypographySection($parent, $template.attr('data-id'));
                    });

                    $parent.on('click', '.woodmart-atf-btn-remove', function(e) {
                        e.preventDefault();

                        $(this).parent().remove();
                    });
                });



                function initTypographySection($parent, id) {
                    var $section = $parent.find('[data-id="' + id + '"]'),
                        $family = $section.find( '.woodmart-atf-family' ),
                        $familyInput = $section.find('.woodmart-atf-family-input'),
                        $googleInput = $section.find('.woodmart-atf-google-input'),
                        $customInput = $section.find('.woodmart-atf-custom-input'),
                        $customSelector = $section.find('.woodmart-atf-custom-selector'),
                        $selector = $section.find('.woodmart-atf-selector'),
                        $color = $section.find('.woodmart-atf-color'),
                        $colorHover = $section.find('.woodmart-atf-color-hover'),
                        $responsiveControls = $section.find('.woodmart-atf-responsive-controls');

                    if ( $family.data( 'value' ) !== "" ) {
                        $family.val( $family.data( 'value' ) );
                    }

                    syncronizeFontVariants( $section, true, false );

                    //init when value is changed
                    $section.find( '.woodmart-atf-family, .woodmart-atf-style, .woodmart-atf-subset' ).on(
                        'change', function() {
                            syncronizeFontVariants($section, false, false);
                        }
                    );

                    var data = [{id: 'none', text: 'none'}];

                    $family.select2(
                        {
                            matcher: function( term, text ) {
                                return text.toUpperCase().indexOf( term.toUpperCase() ) === 0;
                            },

                            query: function( query ) {
                                return window.Select2.query.local( data )( query );
                            },

                            initSelection: function( element, callback ) {
                                var data = {id: element.val(), text: element.val()};
                                callback( data );
                            },
                            allowClear: true,
                            // when one clicks on the font-family select box
                        }
                    ).on(
                        "select2-opening", function( e ) {

                            // Google font isn use?
                            var usingGoogleFonts = true;

                            // Set up data array
                            var buildData = [];

                            // If custom fonts, push onto array
                            if ( redux.customfonts !== undefined ) {
                                buildData.push( redux.customfonts );
                            }

                            // If standard fonts, push onto array
                            if ( redux.stdfonts !== undefined ) {
                                buildData.push( redux.stdfonts );
                            }

                            // If googfonts on and had data, push into array
                            if ( usingGoogleFonts == 1 || usingGoogleFonts === true && redux.googlefonts !== undefined ) {
                                buildData.push( redux.googlefonts );
                            }

                            // output data to drop down
                            data = buildData;

                            // get placeholder
                            var selFamily = $familyInput.attr( 'value' );
                            if ( !selFamily ) {
                                selFamily = null;
                            }

                            // select current font
                            $family.select2( 'val', selFamily );

                        }
                    ).on(
                        'select2-selecting', function( val, object ) {
                            var fontName = val.object.text;

                            $familyInput.attr( 'value', fontName );

                            // option values
                            selVals = val;
                            isSelecting = true;

                            syncronizeFontVariants( $section, false, true );
                        }
                    ).on(
                        'select2-clearing', function( val, choice ) {
                            $family
                                .attr( 'data-value', '' )
                                .attr( 'placeholder', 'Font Family' );

                            $familyInput.val( '' );

                            $googleInput.val( 'false' );

                            syncronizeFontVariants( $section, false, true );
                        }
                    );

                    // CSS selector multi select field
                    $selector.select2(select2Defaults)
                        .on(
                            'select2-selecting', function( e ) {
                                if( e.val != 'custom') return;
                                $customInput.val(true);
                                $customSelector.removeClass('hide');

                            }
                        ).on(
                            'select2-removing', function( e ) {
                                if( e.val != 'custom') return;
                                $customInput.val('');
                                $customSelector.val('').addClass('hide');
                            }
                        );

                    // Color picker fields
                    $color.wpColorPicker({
                        change: function(event, ui){
                            // needed for palette click
                            setTimeout(function() {
                                updatePreview( $section );
                            }, 5)
                        }
                    });
                    $colorHover.wpColorPicker();

                    // Responsive font size and line height
                    $responsiveControls.on('click', '.woodmart-atf-responsive-opener', function() {
                        var $this = $(this);
                        $this.parent().find('.woodmart-atf-control-tablet, .woodmart-atf-control-mobile').toggleClass('show hide');
                    })
                    .on('change', 'input', function() {
                        updatePreview( $section );
                    });
                }

                function updatePreview( $section ) {
                    var sectionFields = {
                        familyInput: $section.find('.woodmart-atf-family-input'),
                        weightInput: $section.find('.woodmart-atf-weight-input'),
                        preview: $section.find('.woodmart-atf-preview'),
                        sizeInput: $section.find('.woodmart-atf-size-container .woodmart-atf-control-desktop input'),
                        heightInput: $section.find('.woodmart-atf-height-container .woodmart-atf-control-desktop input'),
                        colorInput: $section.find('.woodmart-atf-color')
                    }

                    var size = sectionFields.sizeInput.val(),
                        height = sectionFields.heightInput.val(),
                        weight = sectionFields.weightInput.val(),
                        color = sectionFields.colorInput.val(),
                        family = sectionFields.familyInput.val();


                    if ( !height ) {
                        height = size;
                    }

                    //show in the preview box the font
                    sectionFields.preview
                        .css( 'font-weight', weight )
                        .css( 'font-family', family + ', sans-serif' )
                        .css( 'font-size', size + 'px' )
                        .css( 'line-height', height + 'px' );

                    if ( family === 'none' && family === '' ) {
                        //if selected is not a font remove style "font-family" at preview box
                        sectionFields.preview.css( 'font-family', 'inherit' );
                    }

                    if ( color ) {
                        sectionFields.preview
                            .css( 'color', color )
                            .css( 'background-color', redux.field_objects.typography.contrastColour( color ) );
                    }


					sectionFields.preview.slideDown();

					$('#redux-compiler-hook').val(1);
                }

                function loadGoogleFont( family, style, script ) {

                    if ( family == null || family == "inherit" ) return;

                    //add reference to google font family
                    //replace spaces with "+" sign
                    var link = family.replace( /\s+/g, '+' );

                    if ( style && style !== "" ) {
                        link += ':' + style.replace( /\-/g, " " );
                    }

                    if ( script && script !== "" ) {
                        link += '&subset=' + script;
                    }

                    if ( typeof (WebFont) !== "undefined" && WebFont ) {
                        WebFont.load( {google: {families: [link]}} );
                    }
                }

                function syncronizeFontVariants( $section, init, changeFamily ) {
                    var data = [];

                    var sectionFields = {
                        family: $section.find('.woodmart-atf-family'),
                        familyInput: $section.find('.woodmart-atf-family-input'),
                        style: $section.find('select.woodmart-atf-style'),
                        styleInput: $section.find('.woodmart-atf-style-input'),
                        weightInput: $section.find('.woodmart-atf-weight-input'),
                        subsetInput: $section.find('.woodmart-atf-subset-input'),
                        subset: $section.find('select.woodmart-atf-subset'),
                        googleInput: $section.find('.woodmart-atf-google-input'),
                        preview: $section.find('.woodmart-atf-preview'),
                        sizeInput: $section.find('.woodmart-atf-size-container .woodmart-atf-control-desktop input'),
                        heightInput: $section.find('.woodmart-atf-height-container .woodmart-atf-control-desktop input'),
                        colorInput: $section.find('.woodmart-atf-color')
                    }

                    // Set all the variables to be checked against
                    var family = sectionFields.familyInput.val();

                    if ( !family ) {
                        family = null; //"inherit";
                    }

                    var style = sectionFields.style.val();
                    var script = sectionFields.subset.val();


                    // Is selected font a google font?
                    var google;
                    if ( isSelecting === true ) {
                        google = redux.field_objects.typography.makeBool( selVals.object['data-google'] );
                        sectionFields.googleInput.val( google );
                    } else {
                        google = redux.field_objects.typography.makeBool(
                            sectionFields.googleInput.val()
                        ); // Check if font is a google font
                    }

                    // Page load. Speeds things up memory wise to offload to client
                    if ( init ) {
                        style = sectionFields.style.data( 'value' );
                        script = sectionFields.subset.data( 'value' );

                        if ( style !== "" ) {
                            style = String( style );
                        }

                        if ( typeof (script) !== undefined ) {
                            script = String( script );
                        }
                    }

                    // Something went wrong trying to read google fonts, so turn google off
                    if ( redux.fonts.google === undefined ) {
                        google = false;
                    }

                    // Get font details
                    var details = '';
                    if ( google === true && ( family in redux.fonts.google) ) {
                        details = redux.fonts.google[family];
                    } else {
                        details = defaultVariants;
                    }

                    sectionFields.subsetInput.val( script );

                    // If we changed the font. Selecting variable is set to true only when family field is opened
                    if ( isSelecting || init || changeFamily) {
                        var html = '<option value=""></option>';

                        // Google specific stuff
                        if ( google === true ) {

                            // STYLES
                            var selected = "";
                            $.each(
                                details.variants, function( index, variant ) {
                                    if ( variant.id === style || redux.field_objects.typography.size( details.variants ) === 1 ) {
                                        selected = ' selected="selected"';
                                        style = variant.id;
                                    } else {
                                        selected = "";
                                    }

                                    html += '<option value="' + variant.id + '"' + selected + '>' + variant.name.replace(
                                            /\+/g, " "
                                        ) + '</option>';
                                }
                            );


                            // destroy select2
                            sectionFields.style.select2( "destroy" );

                            // Instert new HTML
                            sectionFields.style.html( html );

                            // Init select2
                            sectionFields.style.select2( select2Defaults );

                            // SUBSETS
                            selected = "";
                            html = '<option value=""></option>';

                            $.each(
                                details.subsets, function( index, subset ) {
                                    if ( subset.id === script || redux.field_objects.typography.size( details.subsets ) === 1 ) {
                                        selected = ' selected="selected"';
                                        script = subset.id;
                                        sectionFields.subset.val( script );
                                    } else {
                                        selected = "";
                                    }
                                    html += '<option value="' + subset.id + '"' + selected + '>' + subset.name.replace(
                                            /\+/g, " "
                                        ) + '</option>';
                                }
                            );

                            // Destroy select2
                            sectionFields.subset.select2( "destroy" );

                            // Inset new HTML
                            sectionFields.subset.html( html );

                            // Init select2
                            sectionFields.subset.select2( select2Defaults );

                            sectionFields.subset.parent().fadeIn( 'fast' );
                            // $( '#' + mainID + ' .typography-family-backup' ).fadeIn( 'fast' );
                        } else {
                            if ( details ) {
                                $.each(
                                    details, function( index, value ) {
                                        if ( index === style || index === "normal" ) {
                                            selected = ' selected="selected"';
                                            sectionFields.style.find( '.select2-chosen' ).text( value );
                                        } else {
                                            selected = "";
                                        }

                                        html += '<option value="' + index + '"' + selected + '>' + value.replace(
                                                '+', ' '
                                            ) + '</option>';
                                    }
                                );

                                // Destory select2
                                sectionFields.style.select2( "destroy" );

                                // Insert new HTML
                                sectionFields.style.html( html );

                                // Init select2
                                sectionFields.style.select2( select2Defaults );

                                // Prettify things
                                sectionFields.subset.parent().fadeOut( 'fast' );
                            }
                        }

                        sectionFields.familyInput.val( family );
                    }

                    // Check if the selected value exists. If not, empty it. Else, apply it.
                    if ( sectionFields.style.find("option[value='" + style + "']" ).length === 0 ) {
                        style = "";
                        sectionFields.style.select2( 'val', '' );
                    } else if ( style === "400" ) {
                        sectionFields.style.select2( 'val', style );
                    }

                    // Weight and italic
                    if ( style.indexOf( "italic" ) !== -1 ) {
                        sectionFields.preview.css( 'font-style', 'italic' );
                        sectionFields.styleInput.val( 'italic' );
                        style = style.replace( 'italic', '' );
                    } else {
                        sectionFields.preview.css( 'font-style', "normal" );
                        sectionFields.styleInput.val( '' );
                    }

                    sectionFields.weightInput.val( style );

                    // Handle empty subset select
                    if ( sectionFields.subset.find("option[value='" + script + "']" ).length === 0 ) {
                        script = "";
                        sectionFields.subset.select2( 'val', '' );
                        sectionFields.subsetInput.val( script );
                    }

                    if( google ) loadGoogleFont( family, style, script );

                    if( ! init ) updatePreview( $section );

                    isSelecting = false;
                }
            },

            imageHotspot: function () {
                $('#vc_ui-panel-edit-element').on('vcPanel.shown', function () {
                    var _this = $(this);
                    var shortcode = _this.data('vc-shortcode');

                    if (shortcode != 'woodmart_image_hotspot' && shortcode != 'woodmart_hotspot') return;

                    var _background_id = vc.shortcodes.findWhere({ id: vc.active_panel.model.attributes.parent_id }).attributes.params.img;
                    var preview = '.woodmart-image-hotspot-preview';

                    $(preview).addClass('loading');
                    $.ajax({
                        url: woodmartConfig.ajax,
                        dataType: 'json',
                        data: {
                            image_id: _background_id,
							action: 'woodmart_get_hotspot_image',
							security: woodmartConfig.get_hotspot_image_nonce,
                        },
                        success: function (response) {
                            $(preview).removeClass('loading');

                            if (response.status == 'success') {
                                _this.find('.woodmart-image-hotspot-image').append(response.html).fadeIn(500);
                                $(preview).css('min-width', _this.find('.woodmart-hotspot-img').outerWidth());
                            } else if (response.status == 'warning') {
                                $('.woodmart-image-hotspot-preview').remove();
                                $('.woodmart-image-hotspot-position').after(response.html);
                            }
                        },
                        error: function (response) {
                            console.log('ajax error');
                        },
                    });
                });
            },

            vcTemplatesLibrary: function () {
                var $head = $('.woodmart-templates-heading'),
                    $list = $('.woodmart-templates-list'),
                    $search = $head.find('.woodmart-templates-search');

                $search.on('keyup', function(e) {
                    var val = $(this).val().toLowerCase();

                    $list.find('.woodmart-template-item').each(function() {
                        var $this = $(this);

                        if( ($this.attr('data-template_name') + $this.attr('data-template_title')).toLowerCase().indexOf(val) > -1) {
                            $this.removeClass('hide-by-search').addClass('show-by-search');
                        } else {
                            $this.addClass('hide-by-search').removeClass('show-by-search');
                        }
                    });
                });

                /* filters */

                $list.on('click', '.woodmart-templates-tags a', function(e) {
                    e.preventDefault();

                    var slug = $(this).data('slug');

                    $(this).parent().parent().find('.active-tag').removeClass('active-tag');
                    $(this).parent().addClass('active-tag');

                    $list.find('.woodmart-template-item').each(function() {
                        var $this = $(this);

                        if( $this.hasClass('tag-' + slug) ) {
                            $this.removeClass('hide-by-tag').addClass('show-by-tag');
                        } else {
                            $this.addClass('hide-by-tag').removeClass('show-by-tag');
                        }
                    });

                });

                /* loader function */

                $list.on('click', '[data-template-handler]', function() {
                    $list.addClass('xtemos-loading element-adding');
                });

                var $vcTemplatesBtn = $('#vc_templates-editor-button, #vc_templates-more-layouts'),
                    templatesLoaded = false,
                    templates;

                $vcTemplatesBtn.on('click', function() {

                    setTimeout(function() {
                        $list.find('.woodmart-template-item').show();

                        if( $list.hasClass('element-adding'))
                            $list.removeClass('element-adding xtemos-loading');

                        // $list.find('.woodmart-template-loading').removeClass('woodmart-template-loading');
                        $search.val('');
                        loadTemplates();
                    }, 100);

                });

                function loadTemplates() {
                    if( templatesLoaded ) return;
                    templatesLoaded = true;

                    $.ajax({
                        url: woodmartConfig.demoAjaxUrl,
                        data: {
                            action: 'woodmart_load_templates'
                        },
                        dataType: 'json',
                        crossDomain: true,
                        method: 'POST',
                        success: function (data) {
                            console.log(data)
                            if( data.count > 0 ) {
                                renderElements(data.elements)
                                renderTags(data.tags, data.count)
                            }

                        },
                        error: function (err) {
                            $('.woodmart-templates-list').prepend('Can\'t load templates from the server.').removeClass('xtemos-loading');
                            console.log('can\'t load templates from the server', err);
                        },
                    })

                }

                function renderTags(tags, count) {
                    var html = '';
                    Object.keys(tags).map(function(objectKey, index) {
                        var tag = tags[objectKey];
                        html = html + renderTag(tag);
                    });
                    html = '<div class="woodmart-templates-tags"><ul><li class="active-tag"><a href="#all" data-slug="all"><span class="tab-preview-name">All</span> <span class="tab-preview-count">' + count + '</span></a></li>' + html +'</ul></div>';
                    // console.log(html)
                    $('.woodmart-templates-list').prepend(html);
                }

                function renderTag(tag) {
                    var html = '';
                    html += '<li><a href="#' + tag.slug + '" data-slug="' + tag.slug + '"><span class="tab-preview-name">' + tag.title + '</span> <span class="tab-preview-count">' + tag.count + '</span></a></li>';

                    return html;
                }

                function renderElements(elements) {
                    var html = '';
                    Object.keys(elements).map(function(objectKey, index) {
                        var element = elements[objectKey];
                        html = renderElement(element) + html;
                    });
                    // console.log(html)
                    $('.woodmart-templates-list').prepend(html).removeClass('xtemos-loading');
                }

                function renderElement(element) {
                    var html = '';
                    html += '<div class="woodmart-template-item ' + element.class + '" data-template_id="' + element.id + '" data-template_unique_id="' + element.id + '" data-template_name="' + element.slug + '" data-template_type="woodmart_templates"  data-template_title="' + element.title + '" data-vc-content=".vc_ui-template-content">';
                        html += '<div class="woodmart-template-image">';
                            html += '<img src="' + element.image + '" title="' + element.title + '" alt="' + element.title + '" />';
                            html += '<div class="woodmart-template-actions">';
                                html += '<a class="woodmart-template-preview" label="Preview this template" title="Preview this template" href="' + element.link + '" target="_blank">Preview</a>';
                                html += '<a class="woodmart-template-add" label="Add this template" title="Add this template" data-template-handler="">Add template</a>';
                            html += '</div>';
                        html += '</div>';
                        html += '<h3 class="woodmart-template-title">' + element.title + '</h3>';
                        // html += '<div class="woodmart-template-preview"><a href="' + element.link + '" target="_blank">preview</a></div>';
                        // html += '<div class="vc_ui-template-content" data-js-content>';
                        // html += '</div>';
                    html += '</div>';

                    return html;
                }

            },

            cssGenerator: function() {
                // General.
                var $form = $('.woodmart-generator-form');
                $form.on('change', '[type=\"checkbox\"]', prepare);
                prepare();

                // Extra section.
                // var $wcExtraInput = $('input[name="wooComm"]');
                // var $wcExtraSection = $('.woodmart-woo-extra');
                // $wcExtraInput.on('change', function () {
                //     if ($(this).prop('checked')) {
                //         $wcExtraSection.find("[type=\"checkbox\"]").prop('checked', 'checked');
                //     } else {
                //         $wcExtraSection.find("[type=\"checkbox\"]").prop('checked', '');
                //     }
                // });
                //
                // $wcExtraSection.find("[type=\"checkbox\"]").on('change', function () {
                //     if ( ! $wcExtraInput.prop('checked') ) {
                //         $wcExtraInput.prop('checked', 'checked');
                //     }
                //     var checked = false;
                //
                //     setTimeout(function () {
                //         $wcExtraSection.find('.css-checkbox').each(function() {
                //             var $input = $(this).find("[type=\"checkbox\"]");
                //
                //             if ($input.prop('checked') ) {
                //                 checked = true;
                //             }
                //         });
                //
                //         if ( ! checked ) {
                //             $wcExtraInput.prop('checked', '');
                //         }
                //     }, 100);
                // });

                // General.
                function prepare() {
                    var fields = {};
                    var $this = $(this);
                    var id = $this.attr('id');
                    var checked = $this.prop('checked');
                    var $children = $form.find('[data-parent="' + id + '"] [type=\"checkbox\"]');

                    $children.prop('checked', checked);

                    var parentChecked = function( $this ) {
                        $form.find('[name="' + $this.parent().data('parent') + '"]').each(function() {
                            $(this).prop('checked', 'checked');
                            if ( 'none' !== $(this).parent().data('parent') ) {
                                parentChecked($(this));
                            }
                        });
                    };

                    if ( 'none' !== $this.parent().data('parent') ) {
                        parentChecked($(this));
                    }

                    var uncheckedEmpty = function( $this ) {
                        var id = $this.parent().data('parent');
                        var $children = $form.find('[data-parent="' + id + '"]');

                        // if ( 'wooComm' === id ) {
                        //     Object.assign($children, $('.woodmart-woo-extra').find('.css-checkbox'));
                        // }
                        // console.log(id);

                        if ($children.length > 0) {
                            var checked = false;

                            $children.each(function() {
                                if ( $(this).find('[type="checkbox"]').prop('checked') ) {
                                    checked = true;
                                }
                            });

                            if ( ! checked ) {
                                $form.find('[name="' + id + '"]').prop('checked', '');
                                uncheckedEmpty($form.find('[name="' + id + '"]'));
                            }
                        }
                    };

                    uncheckedEmpty($(this));

                    $form.find("[type=\"checkbox\"]").each(function() {
                        // if ($(this).parents('.woodmart-column').hasClass('woodmart-woo-extra')) {
                        //     if ( $('input[name="wooComm"]').prop('checked') ) {
                        //         fields[$(this).prop('name')] = $(this).prop('checked') ? true : false;
                        //     } else {
                        //         fields[$(this).prop('name')] = false;
                        //     }
                        //     return true;
                        // }

                        fields[this.name] = $(this).prop('checked') ? true : false;
                    });

                    var base64 = btoa(JSON.stringify(fields));

                    $form.find('[name="css-data"]').val(base64);
                }

                $('.css-update-button').on('click', function(e) {
                    e.preventDefault();
                    $form.find('[name="generate-css"]').click();
                });

                $form.on('click', '[name="generate-css"]', function() {
                    $form.parents('.woodmart-box-content').addClass('xtemos-loading')
                });
			},

			compilerRunDetect: function () {

				compilerRun();

				$('.redux-group-tab-link-a').on('click', function () {
					compilerRun();
				});

				function compilerRun() {
					var currentSectionVal = $('#currentSection').val();

					if (currentSectionVal == 8 || currentSectionVal == 9 || currentSectionVal == 39) {
						$('#redux-compiler-hook').val(1);
					}
				}
			},
        };

        return {
            init: function() {

                woodmartAdmin.importAction();

                $(document).ready(function() {
                    woodmartAdmin.listElement();
                    woodmartAdmin.sizeGuideInit();
                    woodmartAdmin.attributesMetaboxes();
                    woodmartAdmin.product360ViewGallery();

					woodmartAdmin.variationGallery();
					woodmartAdmin.compilerRunDetect();

                    //Redux fields
                    woodmartAdmin.multiFonts();
                    woodmartAdmin.multiFontsAccordion();
                    woodmartAdmin.advancedTypography();

                    woodmartAdmin.vcTemplatesLibrary();
					woodmartAdmin.cssGenerator();
                });

            },

            mediaInit: function(selector, button_selector, image_selector)  {
                var clicked_button = false;
                $(selector).each(function (i, input) {
                    var button = $(input).next(button_selector);
                    button.click(function (event) {
                        event.preventDefault();
                        var selected_img;
                        clicked_button = $(this);

                        // check for media manager instance
                        // if(wp.media.frames.gk_frame) {
                        //     wp.media.frames.gk_frame.open();
                        //     return;
                        // }
                        // configuration of the media manager new instance
                        wp.media.frames.gk_frame = wp.media({
                            title: 'Select image',
                            multiple: false,
                            library: {
                                type: 'image'
                            },
                            button: {
                                text: 'Use selected image'
                            }
                        });

                        // Function used for the image selection and media manager closing
                        var gk_media_set_image = function() {
                            var selection = wp.media.frames.gk_frame.state().get('selection');

                            // no selection
                            if (!selection) {
                                return;
                            }

                            // iterate through selected elements
                            selection.each(function(attachment) {
                                var url = attachment.attributes.url;
                                clicked_button.prev(selector).val(attachment.attributes.id);
                                $(image_selector).attr('src', url).show();
                            });
                        };

                        // closing event for media manger
                        wp.media.frames.gk_frame.on('close', gk_media_set_image);
                        // image selection event
                        wp.media.frames.gk_frame.on('select', gk_media_set_image);
                        // showing media manager
                        wp.media.frames.gk_frame.open();
                    });
               });
            }

        }

    }());

})(jQuery);

woodmart_media_init = woodmartAdminModule.mediaInit;

jQuery(document).ready(function() {
    woodmartAdminModule.init();
});
