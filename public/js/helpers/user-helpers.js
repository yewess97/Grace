'use strict';

import { IGrace, Common } from "./common-helpers.js";
import "./plugins.js";


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

        if (!target.is(`input[name="${IGrace.FILTER_PRODUCTS()}_${relation}[]"]`)) return;

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
     * @return {*}
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
            html: `<p class="fs-8">Please ${IGrace.CAPITALIZE(IGrace.LOGIN)} to Continue</p>`,
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
     * @param collection
     * @param data
     * @param isClearAllCollection
     * @return {void}
     */
    updateUserCollectionContent: (collection, data, isClearAllCollection = false) => {
        const
            collection_main        = `#${collection}_main`,
            update_collection_main = $(collection_main).html($(data[IGrace.ROW]).find(collection_main).html());

        $.each(($(`.${IGrace.CLASS(`${collection}_${IGrace.TOTAL_ITEMS}`)}`)), (_, totalItems) => $(totalItems).html(data[`${collection}_${IGrace.TOTAL_ITEMS}`]));

        if (collection === IGrace.CART) {
            $.each(($(`.${IGrace.CLASS(`${IGrace.CART}_${IGrace.TOTAL_COST}`)}`)), (_, totalCost) => $(totalCost).html(IGrace.PRICE_FORMAT(data[IGrace.TOTAL_COST])));

            $(`#${IGrace.USER}_${IGrace.CART}_dropdown`).html($(data['header_row']).html());
        }

        const collection_content_update_actions = {
            true: ()  => update_collection_main,
            false: () => data[IGrace.TOTAL_ITEMS] === 0
                    ? update_collection_main
                    : $(`#${collection}_content`).html($(data[IGrace.ROW]).html()),
        };

        collection_content_update_actions[isClearAllCollection]();

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
     * Auth ajax request.
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
                        window.isFormDirty = false;
                        return location.href = data['redirect_to'];
                    }

                    if (data.status === `sent_${IGrace.EMAIL}`) {
                        success_message = `<p>Check your email to reset your password</p><p class="mt-3" style="font-size: var(--fifteen-pixels)">You will find the email in your inbox, otherwise, check your spam or junk folder</p>`;
                    }

                    if (data.status === `${IGrace.RESET_PASSWORD()}_${IGrace.SUCCESS}`) {
                        success_message = `Your ${IGrace.PASSWORD} has been changed successfully`;
                    }

                    target.trigger('reset');
                    window.isFormDirty = false;
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
                        target.find(`.${IGrace.LOGIN}-btn`).prop('disabled', false)
                            .find('.loading-spinner')
                            .remove();

                        return Common.errorMessage(authAction, Common.responseJsonError(err));
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },

    /**
     * Social Auth ajax request.
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


    /* ---------------------------------- QUICK VIEW PRODUCT REQUEST ---------------------------------- */
    /**
     * Get the product's data when quick view it.
     *
     * @return {void}
     */
    ajaxQuickViewProductRequest: () => {
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
            quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.ERROR))}`).html('');

            // Reset product quantity input
            quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_QUANTITY()}`).val(1).removeAttr('max');

            // Reset the user collections buttons
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
                    const
                        product = data[IGrace.PRODUCT],
                        is_product_in_wishlist = product[IGrace.PLURALIZE(IGrace.WISHLIST)]?.some((item) =>
                            item[IGrace.COLLECTION_ID(IGrace.PRODUCT)] === product[IGrace.ID]
                        );

                    quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.COLLECTION_ID(IGrace.PRODUCT)}, #${IGrace.ADD_REMOVE_WISHLIST()}_${IGrace.COLLECTION_ID(IGrace.PRODUCT)}`).val(product[IGrace.ID]);
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
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.RATING}`).starRating(data['average_rate']);
                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.CLASS(IGrace.SHORT_DESCRIPTION)}`).html(product[`${IGrace.SHORT_DESCRIPTION}`]);
                    Common.showMultiSelectData({
                        userType:          IGrace.USER,
                        collection:        product,
                        collectionName:    IGrace.PRODUCT,
                        relatedCollection: IGrace.PRODUCT_SIZE_QUICK_VIEW(),
                    });
                    quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_QUANTITY()}`).attr('max', product[IGrace.QUANTITY]);
                    quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}, .${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.CART))}`).css('display', product[IGrace.STATUS] === 1 ? 'block' : 'none');
                    quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-lg-btn > i`)
                        .removeClass('fa-regular fa-solid')
                        .addClass(is_product_in_wishlist ? 'fa-solid' : 'fa-regular');

                    Common.imageConfig();
                    quick_view_modal.modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- CREATE OR UPDATE REQUEST ---------------------------------- */
    /**
     * Create or Update a collection.
     *
     * @param form
     * @return {void}
     */
    ajaxCreateOrUpdateRequest: (form) => {
        $(document).on(IGrace.SUBMIT, `#${form}_form`, function (e) {
            e.preventDefault();

            const
                target             = $(this),
                route              = target.attr('action'),
                main_page          = target.data('main'),
                action             = form.split('_')[0],
                collection_name    = IGrace.CAPITALIZE(form.split('_')[1] ?? ''),
                place_order_button = $(`#place_${IGrace.ORDER}_btn`),
                form_data          = Common.filteredFormData(this),

                formReset = (target, action) => {
                    target.trigger('reset');
                    window.isFormDirty = false;
                    $(IGrace.ERROR_ELEMENT(action)).empty();
                };

            // FormData() accepts only POST method
            if (action === IGrace.UPDATE) form_data.append('_method', IGrace.PUT);

            // Because of the pagination
            if (collection_name === IGrace.CAPITALIZE(IGrace.ORDER)) {
                const
                    key = `${form}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`,
                    value = sessionStorage.getItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`);

                form_data.set(key, value ? value : '');
            }

            let success_message = `${collection_name} has been ${action === IGrace.ADD ? IGrace.ADDED() : IGrace.UPDATED()}`;

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: () => User.loadingSpinner(target, place_order_button, true),
                success: (data) => {
                    if ($.inArray(data.status, [`auth_${IGrace.SUCCESS}`, 'stripe_session_created']) > -1) {
                        window.isFormDirty = false;

                        return location.assign(data['redirect_to']);
                    }

                    if (collection_name === IGrace.CAPITALIZE(IGrace.ORDER)) {
                        success_message = '<p style="font-size:var(--eighteen-pixels)">We are glad and honored that you chose us <i class="fa-solid fa-face-grin-wink"></i></p><p class="mt-3" style="font-size:var(--eighteen-pixels)">Order has been placed successfully</p><p class="mt-2 fs-6">Have a nice day <i class="fa-solid fa-face-smile-beam"></i></p>';

                        place_order_button.prop('disabled', false)
                            .find('.loading-spinner')
                            .remove();

                        window.isFormDirty = false;

                        $(`${IGrace.CLASS(IGrace.ERROR_ELEMENT(IGrace.ADD_COLLECTION(IGrace.ORDER)))} ul`).empty();
                        $(`${IGrace.CLASS(IGrace.ERROR_ELEMENT(IGrace.ADD_COLLECTION(IGrace.ORDER)))}`).addClass('d-none');

                        return Common.successMessage(IGrace.SUCCESS, success_message, collection_name);
                    }

                    if (collection_name === IGrace.CAPITALIZE(IGrace.REVIEW)) {
                        const reviews_route = target.data(IGrace.PLURALIZE(IGrace.REVIEW));

                        window.isFormDirty = false;

                        return Common.successMessage(IGrace.SUCCESS, success_message, reviews_route);
                    }

                    Common.arrangeTableRows();
                    $(IGrace.MODAL(IGrace.USER)).modal('hide');
                    formReset(target, action);

                    Common.updateTableRows({
                        data:     data,
                        mainPage: main_page,
                        action:   action,
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

                        return formReset(target, action);
                    }

                    if (err.status === 429 && $(`.${IGrace.LOGIN}-btn`).length) {
                        return Common.errorMessage(action, Common.responseJsonError(err), err.status);
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- CREATE WISHLIST REQUEST ---------------------------------- */
    /**
     * Create Wishlist Items ajax request.
     *
     * @return {void}
     */
    ajaxCreateWishlistRequest: () => {
        $(document).on(IGrace.SUBMIT, `.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-form`, function (e) {
            e.preventDefault();

            const
                target          = $(this),
                route           = target.attr('action'),
                wishlist_button = target.prev().find(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-lg-btn`),
                product_id      = target.find(`#${IGrace.ADD_REMOVE_WISHLIST()}_${IGrace.COLLECTION_ID(IGrace.PRODUCT)}`).val(),
                form_data       = Common.filteredFormData(this);

            console.log(target);

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: () => {
                    wishlist_button.find('i')
                        .remove().end()
                        .find('.loading-spinner')
                        .remove();

                    User.loadingSpinner(target, wishlist_button);
                },
                success: (data) => {
                    const wishlist_action = {
                        true: () => {
                            const wishlist_config = {
                                [IGrace.DELETED()]: {
                                    action: `${IGrace.DELETED()} from`,
                                    icon: 'regular'
                                },
                                [IGrace.ADDED()]: {
                                    action: `${IGrace.ADDED()} to`,
                                    icon: 'solid'
                                }
                            };

                            const current_action = data.status === IGrace.DELETED()
                                ? wishlist_config[IGrace.DELETED()]
                                : wishlist_config[IGrace.ADDED()];

                            wishlist_button.find('.loading-spinner')
                                .remove().end()
                                .prepend($('<i>', {
                                    class: `fa-${current_action.icon} fa-heart`
                                }));

                            User.updateUserCollectionContent(IGrace.WISHLIST, data);

                            const success_message = `The ${IGrace.CAPITALIZE(IGrace.PRODUCT)} has been ${current_action.action} your ${IGrace.WISHLIST}`;

                            Common.successMessage(IGrace.SUCCESS, success_message);
                        },
                        false: () => Common.somethingWentWrongError(),
                    };

                    (wishlist_action[data[IGrace.COLLECTION_ID(IGrace.PRODUCT)] === parseInt(product_id)])();
                },
                error: (err) => {
                    if (err.status === 401) {
                        return User.confirmLoginMessage();
                    }

                    if (err.status === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (err.status === 422) {
                        return Common.swalResponseJsonErrorMessage(err, IGrace.WARNING);
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- CREATE OR UPDATE CART REQUEST ---------------------------------- */
    /**
     * Create or Update Cart Items ajax request.
     *
     * @param action
     * @return {void}
     */
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

            const products = $(`.${IGrace.CART} .${IGrace.PRODUCT}`).map((_, cartProduct) => {
                const
                    cartProductValueOf = (value) => $(cartProduct).find(`input[name="${IGrace.UPDATE_COLLECTION(IGrace.CART)}_${value}"]`).val(),

                    product_id       = cartProductValueOf(IGrace.COLLECTION_ID(IGrace.PRODUCT)),
                    product_size     = cartProductValueOf(IGrace.PRODUCT_SIZE()),
                    product_quantity = cartProductValueOf(`${IGrace.PRODUCT_QUANTITY()}_${product_id}`);

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

                    window.isFormDirty = false;
                    $(IGrace.ERROR_ELEMENT(action)).empty();

                    User.updateUserCollectionContent(IGrace.CART, data);

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


    /* ---------------------------------- DELETE ALL USER'S COLLECTION REQUEST ---------------------------------- */
    /**
     * Delete All User Collection Items ajax request.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteAllUserCollectionRequest: (collection) => {
        $(document).on(IGrace.CLICK, `#clear_${collection}`, function (e) {
            e.preventDefault();

            const route = $(this).attr('href');

            Common.confirmMessage(`clear your ${collection} from all ${IGrace.PLURALIZE(IGrace.PRODUCT)}?`)
                .then((deleteAllCollection) => {
                    if (deleteAllCollection.isConfirmed) {
                        $.ajax({
                            url: route,
                            method: IGrace.DELETE.toUpperCase(),
                            success: (data) => {
                                User.updateUserCollectionContent(collection, data, true);

                                Common.successMessage(IGrace.SUCCESS, `Your ${IGrace.CAPITALIZE(collection)} has been cleared`);
                            },
                            error: () => Common.somethingWentWrongError(),
                        });
                    }
                    else if (deleteAllCarts.dismiss === Swal.DismissReason.cancel) {
                        Common.cancelMessage(`Your ${collection} is safe`);
                    }
                });
        });
    },


    /* ---------------------------------- DELETE REQUEST ---------------------------------- */
    /**
     * Delete Item ajax request.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteRequest: (collection) => {
        $(document).on(IGrace.SUBMIT, `.${IGrace.CLASS(IGrace.DELETE_COLLECTION(collection))}-form`, function (e) {
            e.preventDefault();

            const
                target        = $(this),
                route         = target.attr('action'),
                collection_id = target.data(IGrace.ID),
                reviews_route = target.data(IGrace.PLURALIZE(IGrace.REVIEW)),
                form_data     = new FormData(target[0]),

                userCollectionSuccess = (collection, data) => {
                    const success_message = data.status && data.status === 'decremented'
                        ? `${IGrace.CAPITALIZE(IGrace.PRODUCT_QUANTITY().replace('_', ' '))} has been decreased by one from your ${IGrace.CART}`
                        : `${IGrace.CAPITALIZE(IGrace.PRODUCT)} has been removed from your ${collection}`;

                    Common.removeRow($(`#${collection}_item_${collection_id}`), () =>
                        User.updateUserCollectionContent(collection, data)
                    );

                    return Common.successMessage(data.status && data.status === 'decremented' ? 'Decreased' : IGrace.DELETED(), success_message);
                };

            Common.confirmMessage(`${IGrace.DELETE} ${collection === IGrace.CART ? `or decrease the ${IGrace.QUANTITY} of the ${IGrace.PRODUCT} from your ${IGrace.CART}` : `this ${collection}`}?`)
                .then((deleteConfirmation) => {
                    if (deleteConfirmation.isConfirmed) {
                        $.ajax({
                            url: route,
                            method: IGrace.DELETE.toUpperCase(),
                            data: form_data,
                            success: (data) => {
                                let success_message = `Your selected ${collection} has been ${IGrace.DELETED()}`;

                                if ($.inArray(collection, [IGrace.WISHLIST, IGrace.CART]) > -1) {
                                    userCollectionSuccess(IGrace.WISHLIST, data);
                                    userCollectionSuccess(IGrace.CART, data);
                                }

                                window.isFormDirty = false;

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


    /* ---------------------------------- UPDATE REVIEWS CONTENT REQUEST ---------------------------------- */
    /**
     * Update Reviews' Container ajax request.
     *
     * @param reviewsRoute
     * @return {void}
     */
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


    /* ---------------------------------- FILTER PRODUCTS REQUEST ---------------------------------- */
    /**
     * Get Filtered Products ajax request.
     *
     * @param args
     * @return {void}
     */
    ajaxFilterProducts: (args) => {
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

        // Get a fresh CSRF token for this new request
        form_data.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: route,
            method: IGrace.POST,
            data: form_data,
            success: (data) => {
                $(IGrace.ERROR_ELEMENT(action)).empty()
                    .parent()
                    .removeClass(`show-${IGrace.ERROR} mt-3`);

                Common.paginationResponse($('.pagination-container'), data);

                User.ajaxQuickViewProductRequest();
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

    /**
     * Get Filtered Products ajax request,
     * when submitting the filter form.
     *
     * @return {void}
     */
    ajaxFilterProductsRequest: () => {
        $(document).on(IGrace.SUBMIT, `#${IGrace.FILTER_PRODUCTS_FORM()}`, function (e) {
            e.preventDefault();

            const
                target             = $(this),
                route              = target.attr('action'),
                url_params         = new URLSearchParams(location.search),
                action             = target.attr(IGrace.ID).split('_')[0],
                filtered_form_data = Common.filteredFormData(this),
                no_results_img_src = target.data('no_results'),
                is_filter_products = route.includes(`${IGrace.FILTER}-${IGrace.PLURALIZE(IGrace.PRODUCT)}`),

                /**
                 * If the url has query parameters, append them to the route,
                 * otherwise, use the route as is.
                 */
                url = is_filter_products
                    ? `${route.split('?')[0]}?${$.param(Object.fromEntries(url_params.entries()))}`
                    : route,

                /**
                 * Make sure the same key has the same value in the form data object,
                 * (i.e., key: value or key: [value1, value2, ...]).
                 */
                form_data = [...filtered_form_data].reduce((formData, [key, value]) => ({
                    ...formData,
                    [key]: formData[key]
                        ? [].concat(formData[key], value)
                        : value
                }), {}),

                // List of form field names that contain security tokens and should never be stored
                security_fields = ['_token', 'csrf_token', 'authenticity_token'];

            // CRITICAL: Remove security fields before saving
            $.each((security_fields), (_, field) => delete form_data[field]);

            // Save the form data in the sessionStorage to be used in the pagination request later on.
            sessionStorage.setItem(IGrace.FILTER_PRODUCTS(), JSON.stringify(form_data));

            User.ajaxFilterProducts({
                route:             url,
                action:            action,
                noResultsImageSrc: no_results_img_src,
            });
        });
    },
}



/**
 * Export IGrace & Common & User Objects.
 */
export { IGrace, Common, User };
