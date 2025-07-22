'use strict';

import { IGrace, Common } from "./common-helpers.js";


const User = {

    /**
     * Configure the carousel slider.
     *
     * @param args
     * @return {void}
     */
    carouselSliderConfig: (args) => {
        const { element, displayItemsCount, nav = true, dots = false, autoplay = true } = args;

        element.owlCarousel({
            items:              displayItemsCount,
            rewind:             true,
            margin:             10,
            nav:                nav,
            navText:            ['<i class= "fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
            dots:               dots,
            autoplay:           autoplay,
            autoplayTimeout:    4000,
            autoplayHoverPause: true,
        });

        $('.owl-nav button').attr('type', 'button');
        $('.owl-nav .owl-prev').attr('title', 'Go to Previous');
        $('.owl-nav .owl-next').attr('title', 'Go to Next');

        $('.owl-dot').attr({
            type:  'button',
            title: 'Go to Slider'
        });
    },


    /**
     * Change the products view.
     *
     * @return {void}
     */
    changeProductsView: () => {
        // Since products_main_view is a cached jQuery object, its .data() method does not automatically pick up new values when the (data-) attribute changes. This is a well-known limitation of .data() in jQuery.
        $(`.${IGrace.PLURALIZE(IGrace.PRODUCT)}-content`)
            .removeClass((_, className) => (className.match(/\brow-cols-md-\S+/g) || []).join(' '))
            .addClass(`row-cols-md-${$(`.${IGrace.PLURALIZE(IGrace.PRODUCT)}-view-sort`).attr('data-grid-main-view')}`);
    },


    /**
     * Handle the chosen filter products multiple items with hidden input.
     *
     * @param args
     * @return {void}
     */
    handleFilterProductsMultiItemsWithHiddenInput: (args) => {
        let { target, multiSelectedValuesList, relation } = args;

        if (!target.is(`input[name="${IGrace.FILTER}_${IGrace.PLURALIZE(IGrace.PRODUCT)}_${relation}[]"]`)) return;

        const filter_collection_hidden_input = target.parents('.filter-content').next();

        target.is(':checked')
            ? multiSelectedValuesList.push(target.val())
            : multiSelectedValuesList.splice($.inArray(target.val(), multiSelectedValuesList), 1);

        multiSelectedValuesList = multiSelectedValuesList.filter(Boolean).join(','); // filter(Boolean) removes empty values

        filter_collection_hidden_input.val(multiSelectedValuesList);
    },


    /**
     * Handle the inputs & range of the price filter.
     *
     * @param input
     * @param range
     * @return {void}
     */
    handlePriceFilter: (input, range) =>
        input.on(IGrace.INPUT, function () {
            let [min_value, max_value] = input.map((_, element) => parseFloat($(element).val()));

            if (min_value > max_value) [min_value, max_value] = [max_value, min_value];

            range.eq(0).val(min_value);
            range.eq(1).val(max_value);
        }),


    /**
     * Display the login confirmation message,
     * when the user not logged in.
     *
     * @return {void}
     */
    confirmLoginMessage: () => {
        Common.swalWithButtons.fire({
            title: `${IGrace.CAPITALIZE(IGrace.LOGIN)} Required`,
            html: `<p class="fs-8">Please ${IGrace.CAPITALIZE(IGrace.LOGIN)} to Continue <i class="ti ti-face-smile"></i></p>`,
            icon: IGrace.WARNING,
            showConfirmButton: true,
            confirmButtonText: `Go to ${IGrace.LOGIN} page`,
        })
            .then((login) => {
                if (login.isConfirmed) {
                    location.href = `/${IGrace.LOGIN}`;
                }
            });
    },


    /**
     * Update the cart content.
     *
     * @param data
     * @param isClearAllCart
     * @return {void}
     */
    updateCartContent: (data, isClearAllCart = false) => {
        const
            cart_main        = `#${IGrace.CART}_main`,
            update_cart_main = $(cart_main).html($(data[IGrace.ROW]).find(cart_main).html());

        $(`.${IGrace.CLASS(IGrace.CART_TOTAL_ITEMS())}`).html(data[IGrace.TOTAL_ITEMS]);

        $.each(($(`.${IGrace.CLASS(IGrace.CART_TOTAL_COST())}`)), (_, totalCost) => $(totalCost).html(IGrace.PRICE_FORMAT(data[IGrace.TOTAL_COST])));

        $(`#${IGrace.USER}_${IGrace.CART}_dropdown`).html($(data['header_row']).html());

        const cart_content_update_actions = {
            true: () => update_cart_main,
            false: () => data[IGrace.TOTAL_ITEMS] === 0
                    ? update_cart_main
                    : $(`#${IGrace.CART}_content`).html($(data[IGrace.ROW]).html()),
        };

        cart_content_update_actions[isClearAllCart]();

        Common.imageConfig();
    },


    /**
     * Set a border-danger class to the selected address,
     * and save the selected address in the session storage.
     *
     * @return {void}
     */
    checkoutAddressesConfig: () => {
        const
            radio_inputs   = $(`input[name="${IGrace.ADD_COLLECTION(IGrace.ORDER)}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}"]:radio`),
            selected_value = sessionStorage.getItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`);

        if (selected_value) {
            radio_inputs.filter(`[value="${selected_value}"]`)
                .prop('checked', true)
                .closest('.card')
                .addClass('border-danger');
        }

        radio_inputs.on(IGrace.CHANGE, function () {
            sessionStorage.setItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`, $(this).val());

            $.each((radio_inputs), (_, radioInput) =>
                $(radioInput).closest('.card').toggleClass('border-danger', $(radioInput).is(':checked'))
            );
        });
    },


    /**
     * Display a loading spinner.
     * 
     * @param target
     * @param element
     * @param {boolean} isDisabled
     * @return {void}
     */
    loadingSpinner: (target, element, isDisabled = false) => {
        if (isDisabled) element.prop('disabled', true);

        element.prepend($('<img>', {
            src: target.data('loading_spinner'),
            alt: 'Loading',
            class: 'img-fluid loading-spinner',
            width: 30,
            height: 30,
        }));
    },



    /* ---------------------------------- AUTH REQUEST ---------------------------------- */

    /**
     * Auth Ajax Request.
     * 
     * @param authAction
     * @return {void}
     */
    ajaxAuthRequest: (authAction) => {
        $(document).on(IGrace.SUBMIT, `#${authAction}_form`, function (e) {
            e.preventDefault();

            const
                target    = $(this),
                route     = target.attr('action'),
                form_data = Common.filteredFormData(this);

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: () => User.loadingSpinner(target, target.find(`.${IGrace.LOGIN}-btn`), true),
                success: (data) => {
                    let success_message;

                    if (data.status === `auth_${IGrace.SUCCESS}`) {
                        return location.href = data['redirect_to'];
                    }

                    if (data.status === `sent_${IGrace.EMAIL}`) {
                        success_message = `<p>Check your email to reset your password</p><p class="mt-3" style="font-size: var(--fifteen-pixels)">You will find the email in your inbox, otherwise, check your spam or junk folder</p>`;
                    }

                    if (data.status === `${IGrace.RESET_PASSWORD()}_${IGrace.SUCCESS}`) {
                        success_message = `Your ${IGrace.PASSWORD} has been changed successfully`;
                    }

                    target.trigger('reset');
                    $(IGrace.ERROR_ELEMENT(authAction)).empty();

                    return Common.successMessage(IGrace.SUCCESS, success_message, authAction);
                },
                error: (err) => {
                    if (err.status === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (err.status === 429 && $(`.${IGrace.LOGIN}-btn`).length) {
                        return Common.errorMessage(authAction, Common.responseJsonError(err), err.status);
                    }

                    if (err.status === 422 || IGrace.IS_IN_ARRAY([`${IGrace.FORGOT_PASSWORD()}_failed`, `${IGrace.RESET_PASSWORD()}_failed`], err.status)) {
                        return Common.errorMessage(authAction, Common.responseJsonError(err));
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },

    /**
     * Social Auth Ajax Request.
     * 
     * @return {void}
     */
    ajaxSocialAuthRequest: () => {
        $(document).on(IGrace.CLICK, `.social-${IGrace.LOGIN}-provider`, function (e) {
            e.preventDefault();

            const
                target = $(this),
                route  = target.attr('href');

            $.ajax({
                url: route,
                method: IGrace.GET,
                success: (data) => location.href = data['redirect_to'],
                error: (err) => IGrace.IS_IN_ARRAY([400, 500], err.status) 
                    ? Common.somethingWentWrongError(Common.responseJsonError(err, true)) 
                    : Common.somethingWentWrongError(),
            });
        });
    },


    /* ---------------------------------- CREATE OR UPDATE REQUEST ---------------------------------- */
    ajaxCreateOrUpdateRequest: (form) => {
        $(document).on(IGrace.SUBMIT, `#${form}_form`, function (e) {
            e.preventDefault();

            const
                target             = $(this),
                route              = target.attr('action'),
                main_page          = target.data('main'),
                action             = form.split('_')[0],
                collection         = IGrace.CAPITALIZE(form.split('_')[1] ?? ''),
                place_order_button = $(`#place_${IGrace.ORDER}_btn`),
                form_data          = Common.filteredFormData(this),

                form_reset = (target, action) => {
                    target.trigger('reset');
                    $(IGrace.ERROR_ELEMENT(action)).empty();
                };

            // FormData() accepts only POST method
            if (action === IGrace.UPDATE) form_data.append('_method', IGrace.PUT);

            // Because of the pagination
            if (collection === IGrace.CAPITALIZE(IGrace.ORDER)) {
                const 
                    key = `${form}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`,
                    value = sessionStorage.getItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`);

                form_data.set(key, value ? value : ''); // If the value is null, set it to an empty string
            }

            let success_message = `${collection} has been ${action === IGrace.ADD ? IGrace.ADDED() : IGrace.UPDATED()}`;

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: () => User.loadingSpinner(target, place_order_button, true),
                success: (data) => {
                    if ($.inArray(data.status, [`auth_${IGrace.SUCCESS}`, 'stripe_session_created']) > -1) {
                        return location.assign(data['redirect_to']);
                    }

                    if (collection === IGrace.CAPITALIZE(IGrace.ORDER)) {
                        success_message = '<p style="font-size:var(--eighteen-pixels)">We are glad and honored that you chose us <i class="fa-solid fa-face-grin-wink"></i></p><p class="mt-3" style="font-size:var(--eighteen-pixels)">Order has been placed successfully</p><p class="mt-2 fs-6">Have a nice day <i class="fa-solid fa-face-smile-beam"></i></p>';

                        place_order_button.prop('disabled', false)
                            .find('.loading-spinner')
                            .remove();

                        $(`${IGrace.CLASS(IGrace.ERROR_ELEMENT(IGrace.ADD_COLLECTION(IGrace.ORDER)))} ul`).empty();
                        $(`${IGrace.CLASS(IGrace.ERROR_ELEMENT(IGrace.ADD_COLLECTION(IGrace.ORDER)))}`).addClass('d-none');

                        return Common.successMessage(IGrace.SUCCESS, success_message, collection);
                    }

                    if (collection === IGrace.CAPITALIZE(IGrace.REVIEW)) {
                        const reviews_route = target.data(IGrace.PLURALIZE(IGrace.REVIEW));

                        return Common.successMessage(IGrace.SUCCESS, success_message, reviews_route);
                    }

                    Common.arrangeTableRows();
                    $(IGrace.MODAL(IGrace.USER)).modal('hide');
                    form_reset(target, action);

                    Common.updateTableRows({
                        data:       data,
                        mainPage:   main_page,
                        collection: collection,
                        action:     action,
                    });

                    Common.successMessage(IGrace.SUCCESS, success_message);
                },
                error: (err) => {
                    if (err.status === 401) {
                        return User.confirmLoginMessage();
                    }

                    if (IGrace.IS_IN_ARRAY([400, 403], err.status)) {
                        return Common.swalWithButtons.fire({
                            title:             'Sorry!',
                            html:              Common.responseJsonError(err, true),
                            icon:              IGrace.WARNING,
                            showConfirmButton: true,
                        });
                    }

                    if (err.status === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (err.status === 422) {
                        if (!Common.responseJsonError(err)[`${IGrace.REVIEW}_exists`]) {
                            place_order_button.prop('disabled', false)
                                .find('.loading-spinner')
                                .remove();

                            $(`${IGrace.CLASS(IGrace.ERROR_ELEMENT(IGrace.ADD_COLLECTION(IGrace.ORDER)))}`).removeClass('d-none');

                            return Common.errorMessage(action, Common.responseJsonError(err));
                        }

                        $(`#${IGrace.REVIEW}_exists`)
                            .removeClass('d-none')
                            .addClass('d-flex')
                            .find(`#${IGrace.REVIEW}_exists_message`)
                            .html(Common.responseJsonError(err, true));

                        $(`input[name="${IGrace.ADD_COLLECTION(IGrace.REVIEW)}_${IGrace.RATING}"][type="hidden"]`).val('');

                        return form_reset(target, action);
                    }

                    if (err.status === 429 && $(`.${IGrace.LOGIN}-btn`).length) {
                        return Common.errorMessage(action, Common.responseJsonError(err), err.status);
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- DELETE REQUEST ---------------------------------- */
    ajaxDeleteRequest: (collection) => {
        $(document).on(IGrace.SUBMIT, `.${IGrace.CLASS(IGrace.DELETE_COLLECTION(collection))}-form`, function (e) {
            e.preventDefault();

            const
                target        = $(this),
                route         = target.attr('action'),
                collection_id = target.data(IGrace.ID),
                reviews_route = target.data(IGrace.PLURALIZE(IGrace.REVIEW)),
                form_data     = new FormData(target[0]);

            Common.confirmMessage(`${IGrace.DELETE} ${collection === IGrace.CART ? `or decrease the ${IGrace.QUANTITY} of the ${IGrace.PRODUCT} from your ${IGrace.CART}` : `this ${collection}`}?`)
                .then((deleteConfirmation) => {
                    if (deleteConfirmation.isConfirmed) {
                        $.ajax({
                            url: route,
                            method: IGrace.DELETE.toUpperCase(),
                            data: form_data,
                            success: (data) => {
                                let success_message = `Your selected ${collection} has been ${IGrace.DELETED()}`;

                                if (collection === IGrace.CART) {
                                    success_message = data.status === 'decremented'
                                        ? `${IGrace.CAPITALIZE(IGrace.PRODUCT_QUANTITY().replace('_', ' '))} has been decreased by one from your ${IGrace.CART}`
                                        : `${IGrace.CAPITALIZE(IGrace.PRODUCT)} has been removed from your ${IGrace.CART}`;

                                    Common.removeRow($(`#${IGrace.CART}_item_${collection_id}`), () =>
                                        User.updateCartContent(data));

                                    return Common.successMessage(data.status === 'decremented' ? 'Decreased' : IGrace.DELETED(), success_message);
                                }

                                Common.successMessage(IGrace.DELETED(), success_message, reviews_route);
                            },
                            error: () => Common.somethingWentWrongError(),
                        });
                    }
                    else if (deleteConfirmation.dismiss === Swal.DismissReason.cancel) {
                        Common.cancelMessage(`Your ${collection} is safe`);
                    }
                });
        });
    },


    /* ---------------------------------- GET PRODUCT DATA REQUEST ---------------------------------- */
    ajaxGetProductDataRequest: () => {
        $(document).on(IGrace.CLICK, '.quick-view-btn', function (e) {
            e.preventDefault();

            const
                target            = $(this).hasClass('fa-eye') ? $(this).parent() : $(this),
                route             = target.data('route'),
                main_image        = target.data(IGrace.MAIN_IMAGE()),
                quick_view_modal  = $(`#${IGrace.PRODUCT}_quick_view_modal`);
            
            // Reset the quick view modal
            quick_view_modal.find('form').trigger('reset');

            // Clear all dynamic content
            quick_view_modal.find('input[type="hidden"]').val('');
            quick_view_modal.find(`.${IGrace.PRODUCT}-quick-view-img`).empty();
            quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.NAME}`).html('');
            quick_view_modal.find(`.${IGrace.PRODUCT}-info-quick-view-price .${IGrace.CLASS(IGrace.NEW_PRICE)}`).html('');
            quick_view_modal.find(`.${IGrace.PRODUCT}-info-quick-view-price .${IGrace.CLASS(IGrace.OLD_PRICE)}`).html('');
            quick_view_modal.find(`.${IGrace.PRODUCT}-info-availability span:last-child`).html('');
            quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.CLASS(IGrace.SHORT_DESCRIPTION)}`).html('');

            // Reset select2 or multi-select
            quick_view_modal.find('select[multiple]').val(null).trigger('change');

            // Clear any previous form error messages
            quick_view_modal.find(`.${IGrace.ADD}-${IGrace.ERROR}`).html('');

            // Reset product quantity input
            quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_QUANTITY()}`).val(1).removeAttr('max');

            // Reset the add to cart button
            if (quick_view_modal.find('.loading-spinner').length) {
                quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.CART))}-lg-btn`)
                    .find('.loading-spinner')
                    .remove().end()
                    .prepend($('<i>', {
                        class: 'ti ti-shopping-cart',
                    }));
            }

            // Get the product data via AJAX
            $.get(`${route}?quick_view=true`)
                .done((data) => {
                    const product = data[IGrace.PRODUCT];

                    quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.COLLECTION_ID(IGrace.PRODUCT)}`).val(product[IGrace.ID]);
                    quick_view_modal.find(`.${IGrace.PRODUCT}-quick-view-img`).html($('<img>', {
                        src: main_image,
                        alt: product[IGrace.NAME],
                    }));
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.NAME}`).html(product[IGrace.NAME]);
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-quick-view-price .${IGrace.CLASS(IGrace.NEW_PRICE)}`).html(`EGP ${product[`${IGrace.NEW_PRICE}`].toFixed(2)}`);
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-quick-view-price .${IGrace.CLASS(IGrace.OLD_PRICE)}`).html(
                        product[`${IGrace.OLD_PRICE}`] && product[`${IGrace.OLD_PRICE}`] !== product[`${IGrace.NEW_PRICE}`]
                            ? `EGP ${product[`${IGrace.OLD_PRICE}`].toFixed(2)}`
                            : ''
                    );
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-availability span:last-child`).html(product[IGrace.STATUS] ? 'In Stock' : 'Out of Stock');
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.CLASS(IGrace.SHORT_DESCRIPTION)}`).html(product[`${IGrace.SHORT_DESCRIPTION}`]);
                    Common.showMultiSelectData({
                        userType:          IGrace.USER,
                        collection:        product,
                        collectionName:    IGrace.PRODUCT,
                        relatedCollection: IGrace.PRODUCT_SIZE_QUICK_VIEW(),
                    });
                    quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_QUANTITY()}`).attr('max', product[IGrace.QUANTITY]);
                    quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.CART))}`).css('display', product[IGrace.STATUS] === 1 ? 'block' : 'none');

                    Common.imageConfig();
                    quick_view_modal.modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- FILTER PRODUCTS REQUEST ---------------------------------- */
    ajaxFilterProductsRequest: (args) => {
        const { route, action, noResultsImageSrc } = args;

        const
            stored_filters = JSON.parse(sessionStorage.getItem(IGrace.FILTER_PRODUCTS())) || {},

            /**
             * Check if value is an array, if not, wrap it inside [value],
             * then append each value (single or multiple) separately to FormData.
             */
            form_data = Object.entries(stored_filters).reduce((formData, [attribute, value]) => {
                $.each(($.isArray(value) ? value : [value]), (_, val) => formData.append(attribute, val));

                return formData;
            }, new FormData());

        $.ajax({
            url: route,
            method: IGrace.POST,
            data: form_data,
            success: (data) => {
                $(IGrace.ERROR_ELEMENT(action)).empty()
                    .parent()
                    .removeClass(`show-${IGrace.ERROR} mt-3`);

                Common.paginationResponse($('.pagination-container'), data);

                User.ajaxGetProductDataRequest();
            },
            error: (err) => {
                if (Common.responseJsonError(err, true) === 'no-results') {
                    return Common.searchFilterErrorResponse(noResultsImageSrc);
                }

                if (err.status === 422) {
                    $(IGrace.ERROR_ELEMENT(action)).removeClass('text-danger')
                        .addClass('alert fade show alert-danger fw-500')
                        .attr('data-mdb-color', 'danger');

                    return Common.errorMessage(action, Common.responseJsonError(err));
                }

                Common.somethingWentWrongError();
            },
        });
    },


    /* ---------------------------------- CREATE OR UPDATE CART REQUEST ---------------------------------- */
    ajaxCreateOrUpdateCartRequest: (action) => {
        $(document).on(IGrace.SUBMIT, `.${action}-${IGrace.CART}-form`, function (e) {
            e.preventDefault();

            const
                target        = $(this),
                route         = target.attr('action'),
                cart_button   = target.find(`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.CART))}-lg-btn`),
                form_data     = Common.filteredFormData(this);

            // FormData() accepts only POST method
            if (action === IGrace.UPDATE) form_data.append('_method', IGrace.PUT);

            const products = $(`.${IGrace.CLASS(IGrace.CART_PRODUCT())}`).map((_, cartProduct) => {
                const
                    cart_product_value_of = (value) => $(cartProduct).find(`input[name="${IGrace.UPDATE_COLLECTION(IGrace.CART)}_${value}"]`).val(),

                    product_id       = cart_product_value_of(IGrace.COLLECTION_ID(IGrace.PRODUCT)),
                    product_size     = cart_product_value_of(IGrace.PRODUCT_SIZE()),
                    product_quantity = cart_product_value_of(`${IGrace.PRODUCT_QUANTITY()}_${product_id}`);

                if ((product_id !== 0 && !isNaN(product_id)) && (!isNaN(product_size)) && (product_quantity !== 0 && !isNaN(product_quantity))) {
                    return {
                        id:       product_id,
                        size:     product_size,
                        quantity: product_quantity,
                    }
                }
            }).get();

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: action === IGrace.ADD
                    ? form_data
                    : JSON.stringify({
                        update_cart_product_id:       products.map((product) => product[IGrace.ID]),
                        update_cart_product_size:     products.map((product) => product[IGrace.SIZE]),
                        update_cart_product_quantity: products.map((product) => product[IGrace.QUANTITY]),
                        '_method': IGrace.PUT,
                    }),
                contentType: action === IGrace.ADD
                    ? false
                    : 'application/json',
                beforeSend: () => {
                    cart_button.find('i')
                        .remove().end()
                        .find('.loading-spinner')
                        .remove();
                        
                    User.loadingSpinner(target, cart_button);
                },
                success: (data) => {
                    let success_message = action === IGrace.ADD
                        ? `${IGrace.CAPITALIZE(IGrace.PRODUCT)} has been ${IGrace.ADDED()} to your ${IGrace.CART}`
                        : `Your ${IGrace.CART} has been ${IGrace.UPDATED()}`;

                    $.each(($(`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.CART))}-form`)), (_, form) => {
                        $(form).trigger('reset')
                            .find('input[type="checkbox"]').prop({'checked': false, 'indeterminate': false}).end()
                            .find('input[name*="quick_view"][type="hidden"]').val('').end()
                            .find('.selected-items').empty().end()
                            .find('.placeholder').removeAttr('hidden');
                    });

                    cart_button.find('.loading-spinner')
                        .remove().end()
                        .prepend($('<i>', {
                            class: 'ti ti-shopping-cart',
                        }));

                    $(IGrace.ERROR_ELEMENT(action)).empty();

                    User.updateCartContent(data);

                    Common.successMessage(IGrace.SUCCESS, success_message);
                },
                error: (err) => {
                    if (err.status === 401) {
                        return User.confirmLoginMessage();
                    }

                    if (err.status === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (err.status === 422) {
                        return Common.errorMessage(action, Common.responseJsonError(err));
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- DELETE ALL USER'S CARTS REQUEST ---------------------------------- */
    ajaxDeleteAllCartsRequest: () => {
        $(document).on(IGrace.CLICK, `#clear_${IGrace.CART}`, function (e) {
            e.preventDefault();

            const route = $(this).attr('href');

            Common.confirmMessage(`clear your ${IGrace.CART} from all ${IGrace.PLURALIZE(IGrace.PRODUCT)}?`)
                .then((deleteAllCarts) => {
                    if (deleteAllCarts.isConfirmed) {
                        $.ajax({
                            url: route,
                            method: IGrace.DELETE.toUpperCase(),
                            success: (data) => {
                                User.updateCartContent(data, true);

                                Common.successMessage(IGrace.SUCCESS, `Your ${IGrace.CAPITALIZE(IGrace.CART)} has been cleared`);
                            },
                            error: () => Common.somethingWentWrongError(),
                        });
                    }
                    else if (deleteAllCarts.dismiss === Swal.DismissReason.cancel) {
                        Common.cancelMessage(`Your ${IGrace.CART} is safe`);
                    }
                });
        });
    },


    /* ---------------------------------- UPDATE REVIEWS CONTENT REQUEST ---------------------------------- */
    ajaxUpdateReviewsContentRequest: (reviewsRoute) => {
        $.ajax({
            url: reviewsRoute,
            type: IGrace.GET,
            dataType: 'html',
            success: (data) => $(`.tab-pane.${IGrace.PLURALIZE(IGrace.REVIEW)}`).html(data),
            error: () => Common.somethingWentWrongError(),
        });

        const
            review_form = $(`#${IGrace.ADD_COLLECTION(IGrace.REVIEW)}_form`),
            widths = {
                [`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.REVIEW))}-title`]: '141.6px',
                [`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.REVIEW))}-body-text`]: '82.4px',
            };

        $.each((widths), (key, width) =>
            review_form.find(`${key} .not-form-notch`)
                .removeClass('not-form-notch d-none')
                .addClass('form-notch')
                .html(`
                    <div class="form-notch-leading" style="width: 9px;"></div>
                    <div class="form-notch-middle" style="width: ${width};"></div>
                    <div class="form-notch-trailing"></div>
                `)
        );
    },
}



/**
 * Export IGrace & Common & User Objects.
 */
export { IGrace, Common, User };
