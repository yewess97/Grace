'use strict';

import { IGrace, Common } from "./common-helpers.js";
import "./common-plugins.js";
import "./user-plugins.js";


const User = {

    /**
     * Change the products view in the products page.
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
     * Show a confirmation message to the user to login before performing an action that requires authentication,
     * and redirect them to the login page if they confirm.
     *
     * @return {void}
     */
    confirmLoginMessage: () => {
        Common.swalWithButtons.fire({
            title:             `${IGrace.CAPITALIZE(IGrace.LOGIN)} Required`,
            html:              `<p class="fs-8">Please ${IGrace.CAPITALIZE(IGrace.LOGIN)} to Continue</p>`,
            icon:              IGrace.WARNING,
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
     * Update the content of a user collection (cart or wishlist) after performing an action on it,
     * such as adding or removing an item, or clearing the collection.
     *
     * @param collection
     * @param data
     * @param isClearAllCollection
     * @return {void}
     */
    updateUserCollectionContent: (collection, data, isClearAllCollection = false) => {
        const collection_main = `.${collection}-main`;

        let new_content = $(data[IGrace.ROW]).find(collection_main);

        if (!new_content.length) {
            new_content = $(data[IGrace.ROW]).filter(collection_main);
        }

        const update_collection_main = $(collection_main).html(new_content.html());

        $.each(($(`.${IGrace.CLASS(`${collection}_${IGrace.TOTAL_ITEMS}`)}`)), (_, totalItems) =>
            $(totalItems).html(data[`${collection}_${IGrace.TOTAL_ITEMS}`]));

        if (collection === IGrace.CART) {
            $.each(($(`.${IGrace.CLASS(`${IGrace.CART}_${IGrace.TOTAL_COST}`)}`)), (_, totalCost) =>
                $(totalCost).html(IGrace.PRICE_FORMAT(data[IGrace.TOTAL_COST])));

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
     * Configure the checkout addresses selection
     * by allowing the user to select an address and storing the selected address in the session storage,
     * and applying a visual indication to the selected address card.
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


    /* ---------------------------------- AUTH REQUEST ---------------------------------- */
    /**
     * Handle the AJAX request for authentication actions (login, register, forgot password, reset password)
     * by submitting the corresponding form via AJAX,
     * showing a loading spinner on the submit button,
     * and displaying success or error messages based on the response from the server.
     * If the authentication is successful, the user will be redirected to the specified URL.
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
                beforeSend: () => target.loadingSpinner({
                    element:    target.find(`.${IGrace.LOGIN}-btn`),
                    isDisabled: true,
                }),
                success: (data) => {
                    let success_message;

                    if (data.status === `auth_${IGrace.SUCCESS}`) {
                        window.isFormDirty   = false;
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
     * Handle the AJAX request for social authentication
     * by sending a GET request to the corresponding social authentication route
     * when the user clicks on a social login button,
     * and redirecting the user to the specified URL upon successful response,
     * or showing an error message if the request fails.
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
     * Handle the AJAX request for the quick view product feature
     * by sending a GET request to the corresponding product route with a query parameter
     * indicating that it's a quick view request.
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

                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.RATING}`).starRating({ rating: data['average_rate'] });

                    quick_view_modal.find(`.${IGrace.PRODUCT}-info-${IGrace.CLASS(IGrace.SHORT_DESCRIPTION)}`).html(product[`${IGrace.SHORT_DESCRIPTION}`]);

                    Common.showMultiSelectData({
                        userType:          IGrace.USER,
                        collection:        product,
                        collectionName:    IGrace.PRODUCT,
                        relatedCollection: IGrace.PRODUCT_SIZE_QUICK_VIEW(),
                    });

                    quick_view_modal.find(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_QUANTITY()}`).attr('max', product[IGrace.QUANTITY]);

                    quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}, .${IGrace.CLASS(IGrace.ADD_COLLECTION(IGrace.CART))}`).css('display', product[IGrace.STATUS] === 1 ? 'block' : 'none');

                    quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-lg-btn`).attr('title', `${is_product_in_wishlist ? IGrace.CAPITALIZE(IGrace.REMOVE)+' From' : IGrace.CAPITALIZE(IGrace.ADD)+' To'} ${IGrace.CAPITALIZE(IGrace.WISHLIST)}`);

                    quick_view_modal.find(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-lg-btn`).attr('data-id', product[IGrace.ID]);

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
     * Handle the AJAX request for creating or updating an item in a collection.
     * (e.g., adding/updating an address, placing/updating an order, adding/updating a review)
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
                    key   = `${form}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`,
                    value = sessionStorage.getItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`);

                form_data.set(key, value ? value : '');
            }

            let success_message = `${collection_name} has been ${action === IGrace.ADD ? IGrace.ADDED() : IGrace.UPDATED()}`;

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: () => target.loadingSpinner({
                    element:    place_order_button,
                    isDisabled: true,
                }),
                success: (data) => {
                    if ($.inArray(data.status, [`auth_${IGrace.SUCCESS}`, 'stripe_session_created']) > -1) {
                        window.isFormDirty = false;

                        return location.replace(data['redirect_to']); // This is ideal for login redirects or error pages where you don't want the user to go back to the previous state.
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
     * Handle the AJAX request for adding or removing a product from the wishlist
     * by sending a POST request to the corresponding route with the product ID.
     *
     * @return {void}
     */
    ajaxCreateDeleteWishlistRequest: () => {
        $(document).on(IGrace.SUBMIT, `.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-form`, function (e) {
            e.preventDefault();

            const
                target           = $(this),
                route            = target.attr('action'),
                wishlist_buttons = $(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-lg-btn`),
                product_id       = +target.find(`#${IGrace.ADD_REMOVE_WISHLIST()}_${IGrace.COLLECTION_ID(IGrace.PRODUCT)}`).val(),
                form_data        = Common.filteredFormData(this),

                getProductWishlistButtons = () => wishlist_buttons.filter((_, wishlist_button) =>
                    $(wishlist_button).data(IGrace.ID) === product_id
                ),

                showLoadingSpinners = () => {
                    $.each((getProductWishlistButtons()), (_, wishlist_button) => {
                        const wishlist_btn = $(wishlist_button);

                        wishlist_btn.find('i').remove();

                        target.loadingSpinner({ element: wishlist_btn });
                    });
                },

                updateWishlistButtonIcons = (action, iconType) => {
                    $.each((getProductWishlistButtons()), (_, wishlist_button) => {
                        const wishlist_btn = $(wishlist_button);

                        wishlist_btn.find('.loading-spinner').remove();

                        const wishlist_btn_action = `${action.includes(IGrace.ADDED()) ? IGrace.CAPITALIZE(IGrace.REMOVE)+' From' : IGrace.CAPITALIZE(IGrace.ADD)+' To'} ${IGrace.CAPITALIZE(IGrace.WISHLIST)}`;

                        wishlist_btn.attr({
                            'title':                   wishlist_btn_action,
                            'aria-label':              wishlist_btn_action,
                            'data-mdb-original-title': wishlist_btn_action,
                        });

                        wishlist_btn.find('i').length
                            ? wishlist_btn.find('i').attr('class', `fa-${iconType} fa-heart`)
                            : wishlist_btn.prepend($('<i>', { class: `fa-${iconType} fa-heart` }));
                    });
                };

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: showLoadingSpinners,
                success: (data) => {
                    const wishlist_config = {
                        [IGrace.DELETED()]: {
                            action: `${IGrace.DELETED()} from`,
                            icon:   'regular',
                        },
                        [IGrace.ADDED()]: {
                            action: `${IGrace.ADDED()} to`,
                            icon:   'solid',
                        }
                    };

                    const current_action = data.status === IGrace.DELETED()
                        ? wishlist_config[IGrace.DELETED()]
                        : wishlist_config[IGrace.ADDED()];

                    updateWishlistButtonIcons(current_action.action, current_action.icon);

                    User.updateUserCollectionContent(IGrace.WISHLIST, data);

                    const success_message = `The ${IGrace.CAPITALIZE(IGrace.PRODUCT)} has been ${current_action.action} your ${IGrace.WISHLIST}`;

                    Common.successMessage(IGrace.SUCCESS, success_message);
                },
                error: (err) => {
                    updateWishlistButtonIcons(IGrace.DELETED(), 'regular');

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
     * Handle the AJAX request for adding a product to the cart or updating the cart
     * by sending a POST request to the corresponding route with the product details and quantities of all products in the cart,
     * and updating the cart content and total cost in the UI based on the response from the server.
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
            if (action === IGrace.UPDATE) {
                form_data.append('_method', IGrace.PUT);
            }

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

                    target.loadingSpinner({ element: cart_button });
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
     * Handle the AJAX request for deleting all items from a user collection (cart or wishlist)
     * by showing a confirmation message to the user.
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
                    else if (deleteAllCollection.dismiss === Swal.DismissReason.cancel) {
                        Common.cancelMessage(`Your ${collection} is safe`);
                    }
                });
        });
    },


    /* ---------------------------------- DELETE REQUEST ---------------------------------- */
    /**
     * Handle the AJAX request for deleting an item from a user collection (cart or wishlist)
     * by showing a confirmation message to the user,
     * and upon confirmation, sending a DELETE request to the corresponding route with the item ID,
     * and updating the collection content in the UI based on the response from the server.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteRequest: (collection) => {
        $(document).on(IGrace.SUBMIT, `.${IGrace.CLASS(IGrace.DELETE_COLLECTION(collection))}-form`, function (e) {
            e.preventDefault();

            const
                target          = $(this),
                route           = target.attr('action'),
                collection_id   = target.data(IGrace.ID),
                collection_item = $(`#${collection}_item_${collection_id}`),
                reviews_route   = target.data(IGrace.PLURALIZE(IGrace.REVIEW)),
                form_data       = new FormData(target[0]),

                userCollectionSuccess = (collection, data) => {
                    const success_message = data.status && data.status === 'decremented'
                        ? `${IGrace.CAPITALIZE(IGrace.PRODUCT_QUANTITY().replace('_', ' '))} has been decreased by one from your ${IGrace.CART}`
                        : `${IGrace.CAPITALIZE(IGrace.PRODUCT)} has been removed from your ${collection}`;

                    collection_item.css({ transition: "opacity 0.5s", opacity: 0 });

                    setTimeout(() => {
                        collection_item.remove();
                        User.updateUserCollectionContent(collection, data);
                    }, 500);

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
     * Handle the AJAX request for updating the reviews content on the product page.
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
     * Handle the AJAX request for filtering products
     * by sending a POST request to the corresponding route with the selected filter attributes and values,
     * and updating the products listing and pagination in the UI based on the response from the server,
     *
     * @param args
     * @return {void}
     */
    ajaxFilterProducts: (args) => {
        const { route, action, noResultsImageSrc } = args;

        const stored_filters = JSON.parse(sessionStorage.getItem(IGrace.FILTER_PRODUCTS())) || {};

        /**
         * Wrap the value inside an array [value] if it's not an array,
         * then append each value (single or multiple) separately to FormData.
         */
        const form_data = Object.entries(stored_filters).reduce((formData, [attribute, value]) => {
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
     * Bind the submit event on the filter products form to trigger the AJAX request for filtering products.
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
