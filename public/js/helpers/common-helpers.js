'use strict';

import { IGrace } from "./IGrace.js";
import { Admin } from "./admin-helpers.js";
import { User } from "./user-helpers.js";


const Common = {

    /*================================== Concerned With UI/UX ==================================*/
    /**
     * Set up the sweet alert swal buttons.
     *
     * @return {object}
     * @see https://sweetalert2.github.io/#configuration
     */
    swalWithButtons: Swal.mixin({
        customClass: {
            confirmButton: 'btn me-2',
            cancelButton:  'btn ms-2',
        },
        buttonsStyling: false,
    }),


    /**
     * Get the last directory from the url.
     *
     * @return {string}
     */
    urlLastDirectory: () => {
        const directories = location.pathname.split("/");

        return directories[directories.length - 1];
    },


    /**
     * Check if the route has parameters.
     *
     * @param route
     * @returns {string}
     */
    routeParamsSeperator: (route) =>
        route.includes('?')
            ? '&'
            : '?',


    /**
     * Get the current page number.
     *
     * @return {number}
     */
    currentPageNumber: () => $('.page-item.active').find('.page-link').html() || 1,


    /**
     * Configure the "form multiselect" settings.
     *
     * @param actionCollection
     * @param relation
     * @return {void}
     */
    formMultiSelectConfig: (actionCollection, relation) => {
        const element = $(`#${actionCollection}_${relation}`);

        relation = relation.split('_');
        relation = relation.length > 1
            ? relation[1]
            : relation[0];

        element.filterMultiSelect({
            placeholderText:           `Select ${IGrace.CAPITALIZE(relation)}`,
            filterText:                'Search...',
            selectAllText:             'Select All',
            selectionLimit:            0,
            caseSensitive:             false,
            allowEnablingAndDisabling: false,
        });

        element.removeClass('dropdown');

        $('.filter.dropdown-item > input').attr('name', `search_${relation}`);
    },


    /**
     * Show or hide the number of selected items when selecting multiple items.
     *
     * @param target
     * @return {void}
     */
    showHideMultiSelectedItems: (target) => {
        if (!target.hasClass('selected-items')) return;

        const
            num_selected_items_element           = target.prevAll(':eq(1)'),
            selected_items_length                = target.children().length,
            multiselect                          = target.parents('.filter-multi-select'),
            multiselect_label                    = multiselect.prev(),
            multiselect_items                    = target.parent().next().find('.items'),
            multiselect_max_num_items            = multiselect_items.children().length - 1,
            multiselect_hidden_input             = multiselect.next(),
            select_all                           = multiselect_items.find('.custom-control:first-child'),
            select_all_label                     = select_all.find('.custom-control-label'),
            select_all_checkbox                  = select_all.find('.custom-checkbox'),
            is_hidden                            = selected_items_length > 3,
            multiselect_related_collection_label = [IGrace.CATEGORY, IGrace.SUBCATEGORY].some((collection) =>
                multiselect_label.html().includes(IGrace.CAPITALIZE(IGrace.PLURALIZE(collection)))),
            top_value = (multiselect_related_collection_label && selected_items_length > 0 && selected_items_length <= 3)
                ? '7%'
                : '18%';


        multiselect_label.css('top', top_value);

        target.attr('hidden', is_hidden);

        num_selected_items_element.attr('hidden', !is_hidden)
            .removeClass('mr-2')
            .addClass('me-2');

        select_all_label.html(selected_items_length === multiselect_max_num_items ? 'Unselect All' : 'Select All');

        if (selected_items_length === 1) {
            select_all_checkbox.val(`${multiselect_hidden_input.val()},`);
            multiselect_hidden_input.val(`${multiselect_hidden_input.val()},`);
        }

        num_selected_items_element.html(`${selected_items_length}/${multiselect_max_num_items} Selected items`);
    },


    /**
     * Show the data of the multiselect input.
     *
     * @param args
     * @return {void}
     */
    showMultiSelectData: (args) => {
        const { userType, collection, collectionName, relatedCollection } = args;

        const
            is_admin = userType === IGrace.ADMIN,
            action_collection = is_admin
                ? IGrace.UPDATE_COLLECTION(collectionName)
                : IGrace.ADD_COLLECTION(IGrace.CART),
            relational_collection = is_admin
                ? IGrace.PLURALIZE(relatedCollection)
                : relatedCollection,
            related_collection_words = relational_collection.split('_'),
            relation = `${related_collection_words.length > 1
                ? related_collection_words[1]
                : related_collection_words[0]}`,
            select_element_class = IGrace.CLASS(
                is_admin
                    ? `${collectionName}-${relational_collection}`
                    : relational_collection
            ),
            select_element_id                    = `${action_collection}_${relational_collection}`,
            select_element_name                  = `${select_element_id}[]`,
            action_collection_related_collection = $(`.${IGrace.CLASS(select_element_id)}`),
            multi_related_collection             = action_collection_related_collection.data(relational_collection),
            filter_multi_select_element          = action_collection_related_collection.find('.filter-multi-select'),
            related_collection_hidden_input      = action_collection_related_collection.find(`input[name="${select_element_name}"]:hidden`);


        filter_multi_select_element.remove();
        related_collection_hidden_input.val('');

        let related_collection_select_element = $('<select>', {
            name:            select_element_name,
            id:              select_element_id,
            class:           `${select_element_class} d-none`,
            multiple:        true,
            'aria-required': true,
        });

        const role_actions = {
            [IGrace.ADMIN]: () => {
                $.each((multi_related_collection), (key, value) => {
                    const option_text = relational_collection.includes(IGrace.SIZE)
                        ? key
                        : value[IGrace.NAME];

                    const option_value = relational_collection.includes(IGrace.SIZE)
                        ? value
                        : value[IGrace.ID];

                    related_collection_select_element.append($('<option>', {
                        text:  option_text,
                        value: option_value,
                    }));
                });
            },
            default: () => {
                $.each((collection[IGrace.PLURALIZE(relation)]), (_, productSize) =>
                    $.each((Object.entries(productSize).filter(([key]) => key === IGrace.SIZE)), (_, value) =>
                        $.each((Object.entries(multi_related_collection).filter(([_, sizeValue]) => +sizeValue === +value[1])), (_, [size, sizeValue]) =>
                            related_collection_select_element.append($('<option>', {
                                text:  size,
                                value: sizeValue,
                            }))
                        )
                    )
                );
            },
        };

        (role_actions[userType] || role_actions.default)();

        related_collection_select_element.insertBefore(related_collection_hidden_input);

        const related_collection_element = $(`.${select_element_class}`);

        if (is_admin) {
            $.each((collection[relation]), (_, relatedCollection) => related_collection_element.find('option')
                .filter((_, rel_collection) => +rel_collection.value === (relational_collection.includes(IGrace.SIZE) ? +relatedCollection[IGrace.SIZE] : +relatedCollection[IGrace.ID]))
                .attr('selected', true));
        }

        Common.formMultiSelectConfig(action_collection, relational_collection);
        Common.formSelectConfig();

        related_collection_element.remove();

        const all_multi_related_collection = filter_multi_select_element.end().find('.items input[type="checkbox"]');

        const multi_selected_related_collection_values = all_multi_related_collection.filter(':checked')
            .map((_, relatedCollection) => $(relatedCollection).val())
            .get()
            .filter(Boolean) // filter(Boolean) removes empty values
            .join(',');

        all_multi_related_collection.first().val(multi_selected_related_collection_values);
        related_collection_hidden_input.val(multi_selected_related_collection_values);
    },


    /**
     * Configure the "form select" settings.
     *
     * @return {void}
     */
    formSelectConfig: () => {
        $('.viewbar').removeClass('dropdown-toggle').addClass('form-select');

        const form_select = $('.form-select');

        $.each((form_select), (_, selectElement) => {
            // Add some styles on the label of the select element
            const select_element_label = $(selectElement).hasClass('viewbar')
                ? $(selectElement).parent().prev()
                : $(selectElement).prev();

            select_element_label.css({
                'top':       '18%',
                'font-size': 'var(--twelve-pixels)',
            })

            // Add some styles on the select element
            $(selectElement).css('padding-block', 'var(--twenty-four-pixels) 0.357rem');
        });
    },


    /**
     * Custom filter the form data.
     *
     * @param target
     * @return {FormData}
     */
    filteredFormData: (target) => {
        return [...new FormData($(target)[0])]
            .reduce((formData, [attribute, value]) => {
                const is_file_input = $(target).find(`input[name="${attribute}"]:file`).length && value instanceof File;

                if (attribute.includes(`${IGrace.PASSWORD}`) || attribute.includes(`${IGrace.REVIEW}`) || !value.toString().includes(',') || is_file_input) {
                    formData.append(attribute, value);
                }

                return formData;
            }, new FormData());
    },


    /**
     * Count the characters in a textarea.
     *
     * @param textArea
     * @return {void}
     */
    charsCounter: (textArea) => {
        $(document).on(IGrace.KEYUP, `.${textArea}`, function (e) {
            e.preventDefault();

            const
                target     = $(this),
                text_value = target.val(),
                counter    = target.attr('maxlength') - text_value.length;

            let counter_element = textArea.includes(IGrace.REVIEW)
                ? target.parents().eq(1).next().next().find('> .chars-counter')
                : target.parent().next().addClass('mt-3 mb-2');

            counter_element.text(text_value ? `${counter} characters remaining` : '');

            if ($.isEmptyObject(text_value)) counter_element.removeClass('mt-3 mb-2');
        });
    },


    /**
     * Add some classes, styles, and attributes on each image.
     *
     * @return {void}
     */
    imageConfig: () => $.each(($('img:not(.loading-spinner)')), (_, image) =>
        $(image).addClass('img-fluid h-100')
            .css('mix-blend-mode', 'multiply')
            .attr('loading', 'lazy')
    ),


    /**
     * Truncate the text that has more than 70 characters.
     *
     * @return {void}
     */
    truncateText: () => {
        const
            truncate_elements = $('.truncate'),
            show_char_num     = 70;

        const buildTruncateContent = (data, textType) => `
            <p class="truncate-text">
                <span class="${textType}-text">${data}${textType.includes('short') ? '....' : ''}</span>
                <span class="${textType} show-toggle cursor-pointer fw-600">
                    Show ${textType.includes('short') ? 'More' : 'Less'}
                </span>
            </p>
        `;

        $.each((truncate_elements), (_, truncateElement) => {
            const
                truncate_element = $(truncateElement),
                full_text        = truncate_element.text().trim(); // the text with no tags

            if (full_text.length > show_char_num) {
                const short_text = full_text.substring(0, show_char_num);

                // Store the full text in data attribute so to be able to retrieve it
                truncate_element.data('full_text', full_text);

                truncate_element.html(buildTruncateContent(short_text, 'short'));
                truncate_element.find('.truncate-text').slideDown(180).fadeIn(180);
            }
        });

        // Toggle the truncation
        $(document).on(IGrace.CLICK, '.show-toggle', function (e) {
            e.preventDefault();

            const
                truncate_element = $(this).closest('.truncate'),
                truncate_text    = truncate_element.find('.truncate-text'),
                full_text        = truncate_element.data('full_text'),
                short_text       = full_text.substring(0, show_char_num);

            truncate_text.slideUp(180).fadeOut(180, () => {
                $(this).hasClass('short')
                    ? truncate_element.html(buildTruncateContent(full_text, 'full'))
                    : truncate_element.html(buildTruncateContent(short_text, 'short'));

                truncate_text.slideDown(180).fadeIn(180);
            });
        });
    },


    /**
     * Arrange the table rows.
     *
     * @param startIndex
     * @return {*}
     */
    arrangeTableRows: (startIndex = 0) =>
        $.each(($(".table tbody tr")), (key, row) => $(row).find(`.${IGrace.ROW}-num > p`).html(startIndex + (++key))),


    /**
     * Remove row with animation and update the content.
     *
     * @param element
     * @param callBack
     * @return {void}
     */
    removeRow: (element, callBack = null) => {
        element.css({ transition: "opacity 0.5s", opacity: 0 });

        setTimeout(() => {
            element.remove();

            if (callBack) callBack();
        }, 500);
    },


    /**
     * Update the table rows after
     * add, delete, restore, or pagination.
     *
     * @param args
     * @return {void}
     */
    updateTableRows: (args) => {
        let { data, mainPage, action } = args;

        const pagination_container = $('.pagination-container');

        const ajaxHandlePagination = () => {
            mainPage += `${Common.routeParamsSeperator(mainPage)}page=${action === IGrace.ADD ? data['last_page'] : Common.currentPageNumber()}`;

            $.get(mainPage)
                .done((successData) => Common.paginationResponse(pagination_container, successData))
                .fail(Common.somethingWentWrongError);
        };

        const actions = {
            [IGrace.ADD]: () => {
                $("tbody").append(data[IGrace.ROW]);
                ajaxHandlePagination();
            },
            [IGrace.UPDATE]: () => {
                $(`#${$(data[IGrace.ROW]).first().attr(IGrace.ID)}`).html($(data[IGrace.ROW]).html());
                ajaxHandlePagination();
            },
            default: () => Common.paginationResponse(pagination_container, data),
        };

        (actions[action] || actions.default)?.();

        $('input[type="checkbox"]').prop({'checked': false, 'indeterminate': false});
    },


    /**
     * Warn the user before leaving/refreshing the form
     *
     * @returns {*|jQuery}
     */
    warnBeforeLeaving: () =>
        $(window).on("beforeunload", function (e) {
            if (isFormDirty) {
                e.preventDefault();
                e.returnValue = "";
            }
        }),


    /**
     * Scroll to the top of the page.
     *
     * @return {void}
     */
    scrollToTop: () => {
        const scroll_to_top = '.scroll-to-top';

        $(window).scroll(() => $(scroll_to_top).toggleClass('visible', $(window).scrollTop() > 150));

        $(document).on(IGrace.CLICK, scroll_to_top, function (e) {
            e.preventDefault();

            $('html, body').animate({ scrollTop: 0 }, 500);
        });
    },


    /*================================== Concerned With AJAX Requests ==================================*/
    /**
     * Set up the ajax request.
     *
     * @return {void}
     */
    ajaxSetup: () => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            async:       false,
            cache:       false,
            processData: false,
            contentType: false,
        });
    },


    /**
     * Get the countries from the (restcountries.com) API.
     *
     * @return {void}
     */
    ajaxGetCountries: () => {
        const country_elements = $(`.${IGrace.ADDRESS}-${IGrace.COUNTRY}`);

        if (!country_elements.length) return;

        $.getJSON('https://restcountries.com/v3.1/all?fields=name')
            .done((countries) => {
                const countries_options = countries
                    .map((country) => country.name.common)
                    .sort((opt1, opt2) => opt1.localeCompare(opt2))
                    .map((countryName) => `<option value="${countryName}">${countryName}</option>`)
                    .join('');

                country_elements.append(countries_options);
            })
            .fail(() => console.error("Failed to fetch countries!"));
    },


    /**
     * Handle the response message or errors from the server.
     *
     * @param error
     * @param isMessage
     * @return {string}
     */
    responseJsonError: (error, isMessage = false) => error.responseJSON?.[isMessage ? 'message' : 'errors'],


    /**
     * Display the error messages in the given element
     * for the given errors object (returned from the server).
     *
     * @param action
     * @param errors
     * @param status
     * @return {void}
     */
    errorMessage: (action, errors, status = null) => {
        const
            error_element    = `.${action}-${IGrace.ERROR}`,
            login_btn        = $(`.${IGrace.LOGIN}-btn`),
            show_error_class = `show-${IGrace.ERROR}`,
            margin = Common.urlLastDirectory().includes(IGrace.CHECKOUT)
                ? 'mt-2 mb-1'
                : 'mt-3',
            hideErrorElement = (errorElement) =>
                $(errorElement).empty()
                .parent()
                .removeClass(`${show_error_class} ${margin}`);

        hideErrorElement(error_element);

        $.each((errors), (attr, msg) => {
            attr = attr.replace(/\.\d+$/, ''); // Remove the trailing if there is

            const attr_err = $(`#${attr}_${IGrace.ERROR}`);

            $(attr_err).parent().addClass(`${show_error_class} ${!status ? margin : ''}`);

            if (status === 429) {
                let seconds = msg;

                login_btn.attr('disabled', true);

                attr_err.html(`<li>Too many ${IGrace.LOGIN} attempts. Please try again in <span id="count_down">${seconds}</span></li>`);

                const login_attempts_interval = setInterval(() => {
                    const login_actions = {
                        true: () => $('#count_down').text(--seconds),
                        false: () => {
                            clearInterval(login_attempts_interval);
                            hideErrorElement(attr_err);
                            login_btn.removeAttr('disabled');
                            login_btn.find('.loading-spinner').remove();
                        },
                    };

                    login_actions[seconds > 0]();
                }, 1000);

                return;
            }

            $.each((msg), (_, errMsg) => attr_err.append(`<li role="listitem">${errMsg}</li>`));
        });
    },


    /**
     * Display the confirmation message before deletion.
     *
     * @param message
     * @return {object}
     */
    confirmMessage: (message) =>
        Common.swalWithButtons.fire({
            html:              `Are you sure you want to ${message}`,
            icon:              IGrace.WARNING,
            showConfirmButton: true,
            showCancelButton:  true,
            confirmButtonText: `Yes, ${message.includes(IGrace.REMOVE) ? IGrace.REMOVE : IGrace.DELETE}!`,
            cancelButtonText:  'No, cancel!',
        }),


    /**
     * Display the success message.
     *
     * @param status
     * @param message
     * @param extra
     * @return {*}
     */
    successMessage: (status, message, extra = null) => {
        const
            properties = {
                title:             `${IGrace.CAPITALIZE(status)}!`,
                html:              message,
                icon:              IGrace.SUCCESS,
                showConfirmButton: true,
            },

            swalMessage = () => Swal.fire({
                ...properties,
                html:              `${message} successfully!`,
                showConfirmButton: false,
                timer:             1800,
                timerProgressBar:  true,
            });

        if (extra) {
            if (extra.includes(IGrace.FORGOT_PASSWORD())) {
                return Common.swalWithButtons.fire({
                    ...properties,
                    confirmButtonText: 'Ok, Thanks',
                });
            }

            if (extra.includes(IGrace.RESET_PASSWORD())) {
                return Common.swalWithButtons.fire({
                    ...properties,
                    confirmButtonText: IGrace.CAPITALIZE(IGrace.LOGIN),
                })
                    .then((ok) => {
                        if (ok.isConfirmed) {
                            location.href = `/${IGrace.LOGIN}`;
                        }
                    });
            }

            if (IGrace.IS_IN_ARRAY([IGrace.CAPITALIZE(IGrace.ORDER), 'contact-us'], extra)) {
                return Common.swalWithButtons.fire({
                    ...properties,
                    confirmButtonText: 'Thanks',
                })
                    .then((ok) => {
                        if (ok.isConfirmed) {
                            location.href = `/${IGrace.PLURALIZE(IGrace.PRODUCT)}`;
                        }
                    });
            }

            if (extra.includes(IGrace.PLURALIZE(IGrace.PRODUCT))) {
                swalMessage();
                return setTimeout(() => User.ajaxUpdateReviewsContentRequest(extra), 1800);
            }

            if (IGrace.IS_IN_ARRAY([IGrace.CAPITALIZE(IGrace.ORDER), IGrace.CAPITALIZE(IGrace.REVIEW)], message)
                && message.includes(IGrace.UPDATED())
                && extra.includes(IGrace.ADMIN))
            {
                swalMessage();
                return setTimeout(() => location.reload(), 1800);
            }
        }

        swalMessage();
    },


    /**
     * Display the cancelation message when canceled.
     *
     * @param message
     * @return {*}
     */
    cancelMessage: (message) =>
        Swal.fire({
            title:             'Canceled',
            html:              `${message} <i class= "far fa-smile"></i>`,
            icon:              IGrace.ERROR,
            showConfirmButton: false,
            timer:             1800,
            timerProgressBar:  true,
        }),


    /**
     * Display the error message in a sweet alert.
     *
     * @param error
     * @return {*}
     */
    swalResponseJsonErrorMessage: (error) => Common.somethingWentWrongError(Common.responseJsonError(error, true)),


    /**
     * Display that something went wrong with the ajax request
     * if there's an error but the validation error
     * if confirmed, reload the page.
     *
     * @param message
     * @return {any}
     */
    somethingWentWrongError: (message = "Something went wrong. <br> Please try again later!") =>
        Common.swalWithButtons.fire({
            title:             'Sorry!',
            html:              message,
            icon:              IGrace.ERROR,
            showConfirmButton: true,
            confirmButtonText: "Refresh",
        })
            .then((refresh) => {
                if (refresh.isConfirmed) {
                    location.reload();
                }
            }),


    /**
     * Remove the errors when the edit modal is closed/hidden
     * to avoid showing the errors when the modal is opened again
     * after closing it without submitting the form.
     *
     * @param role
     * @return {*|jQuery}
     */
    removeErrorsWhenEditModelHides: (role) =>
        $(document).on('hidden.bs.modal', `.${role}-${IGrace.EDIT}-modal`, function (e) {
            e.preventDefault();

            $(this).find(IGrace.ERROR_ELEMENT(IGrace.UPDATE)).empty();
        }),


    /**
     * Set up the pagination response after an ajax request.
     *
     * @param paginationContainer
     * @param data
     * @return {void}
     */
    paginationResponse: (paginationContainer, data) => {
        paginationContainer.fadeOut(100, () => {
            paginationContainer.html(data[IGrace.ROW]).fadeIn(100);

            if (Common.urlLastDirectory().includes(IGrace.CHECKOUT)) {
                User.checkoutAddressesConfig();
            }

            Common.truncateText();
            Common.imageConfig();
            Common.arrangeTableRows((data['current_page'] - 1) * data['per_page']);
            $('.carousel-item:first-child').addClass('active');
        });
    },


    /**
     * Success response for search/filter.
     *
     * @param data
     * @return {void}
     */
    searchFilterSuccessResponse: (data) => {
        Common.paginationResponse($('.search-table'), data);

        $(`.carousel-item.${IGrace.ADMIN}-${IGrace.PRODUCT}-imgs:first-child`).addClass('active');
    },


    /**
     * Error response for search/filter.
     *
     * @param imageSrc
     * @return {*|jQuery}
     */
    searchFilterErrorResponse: (imageSrc) =>
        $('.search-table').html(
            `<div class="d-flex justify-content-center mt-5">
                <img src=${imageSrc} alt="No Results Found" class="img-fluid h-100" style="width:300px">
            </div>`
        ),


    /* ---------------------------------- EDIT REQUEST ---------------------------------- */
    /**
     * Edit Address ajax request.
     *
     * @return {void}
     */
    ajaxEditAddressRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ADDRESS), function (e) {
            e.preventDefault();

            const
                target  = $(this),
                route   = target.data('route'),
                country = $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.COUNTRY}`);

            $.get(route)
                .done((data) => {
                    const address = data[IGrace.ADDRESS];

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.ADDRESS))}`).val(address[IGrace.ID]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.ADDRESS1()}`).val(address[IGrace.ADDRESS1()]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.ADDRESS2()}`).val(address[IGrace.ADDRESS2()]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.CITY}`).val(address[IGrace.CITY]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.STATE}`).val(address[IGrace.STATE]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.POSTAL_CODE}`).val(address[IGrace.POSTAL_CODE]);
                    country.find('option').removeAttr('selected')
                        .filter((_, addressCountry) => addressCountry.value === address[IGrace.COUNTRY])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ADDRESS, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },

    /**
     * Edit Review ajax request.
     *
     * @return {void}
     */
    ajaxEditReviewRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.REVIEW), function (e) {
            e.preventDefault();

            const route = $(this).data('route');

            $.get(route)
                .done((data) => {
                    const
                        review = data[IGrace.REVIEW],
                        update_review_rating_container = $(`#${IGrace.RATING}_container`);

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.REVIEW))}`).val(review[IGrace.ID]);

                    update_review_rating_container.empty();

                    for (let i = 5; i >= 1; i--) {
                        const
                            rating_input = $(`<input type="radio" name="update_review_rating" id="update_review_rating${i}" value="${i}" ${i >= review[IGrace.RATING] ? 'checked' : ''}>`),
                            rating_label = $(`<label for="update_review_rating${i}" class="position-relative fs-4">☆</label>`);

                        update_review_rating_container.append(rating_input, rating_label);
                    }

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.REVIEW)}_${IGrace.TITLE}`).val(review[IGrace.TITLE]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.REVIEW)}_${IGrace.BODY_TEXT}`).html(review[IGrace.BODY_TEXT]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.REVIEW)}_${IGrace.COLLECTION_ID(IGrace.PRODUCT)}`).val(review[IGrace.COLLECTION_ID(IGrace.PRODUCT)]);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.REVIEW, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- DELETE REQUEST ---------------------------------- */
    /**
     * Delete a Single or Multiple Items ajax request.
     *
     * @param options
     * @return {object}
     */
    ajaxDeleteItems: (options) => {
        const { deleteRoute, isMultiple, selectedIds, forceDeleteRequest, mainPage, action, collectionTrashed, successMessage } = options;

        // Used object spread + short-circuiting to add the params
        const url_params = {
            ...(isMultiple && { selected_ids: selectedIds.join(',') }),
            ...(forceDeleteRequest > 0 && { force_delete: forceDeleteRequest }),
            page: Common.currentPageNumber(),
        };

        return {
            url: `${deleteRoute}?${$.param(url_params)}`,
            method: IGrace.DELETE.toUpperCase(),
            success: () => {
                $.get(`${mainPage}${Common.routeParamsSeperator(mainPage)}page=${url_params.page}`)
                    .done((data) => {
                        Common.updateTableRows({
                            data:   data,
                            action: action,
                        });
                    })
                    .fail(Common.somethingWentWrongError);

                Common.successMessage((collectionTrashed ? IGrace.DELETED() : IGrace.REMOVED()), successMessage);
            },
            error: (err) => Common.handleDeleteErrors({ error: err }),
        };
    },

    /**
     * Show a force delete confirmation message before deletion.
     *
     * @param options
     * @return {void}
     */
    forceDeleteConfirmation: (options) => {
        const { error, deleteRoute, isMultiple, selectedIds, forceDeleteRequests, mainPage, action, collectionTrashed, successMessage, cancelMessage } = options;

        const delete_options = {
            deleteRoute:       deleteRoute,
            isMultiple:        isMultiple,
            selectedIds:       selectedIds,
            mainPage:          mainPage,
            action:            action,
            collectionTrashed: collectionTrashed,
            successMessage:    successMessage,
        };

        let force_delete_requests = [1, 2];

        Common.swalWithButtons.fire({
            title: 'Oops!',
            html: `${Common.responseJsonError(error, true)} <br><br> Are you sure you want to delete ${isMultiple ? 'the selected items' : 'this item'}?`,
            icon: IGrace.WARNING,
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: `Yes, ${IGrace.DELETE} ${isMultiple ? 'them' : 'it'}!`,
            cancelButtonText: 'No, cancel!',

        })
            .then((willDelete) => {
                if (willDelete.isConfirmed) {
                    const force_delete_request = $.type(forceDeleteRequests) !== 'undefined' && forceDeleteRequests.length === 1
                        ? force_delete_requests.pop()
                        : force_delete_requests.shift();

                    $.ajax({
                        ...Common.ajaxDeleteItems({
                            ...delete_options,
                            forceDeleteRequest: force_delete_request,
                        }),
                        error: (err) => Common.handleDeleteErrors({
                            ...delete_options,
                            error:               err,
                            cancelMessage:       cancelMessage,
                            forceDeleteRequests: force_delete_requests,
                        }),
                    });
                }
                else if (willDelete.dismiss === Swal.DismissReason.cancel) {
                    Common.cancelMessage(cancelMessage);
                }
            });
    },

    /**
     * Handle the errors when deleting.
     *
     * @param options
     * @return {void}
     */
    handleDeleteErrors: (options) => {
        options.error.status === 404
            ? Common.forceDeleteConfirmation(options)
            : Common.somethingWentWrongError();
    },

    /**
     * Delete Item ajax request.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.DELETE, collection), function (e) {
            e.preventDefault();

            const
                target                 = $(this),
                delete_route           = target.data('route'),
                collection_name        = target.data(IGrace.NAME),
                main_page              = target.data('main'),
                collection_trashed     = new URLSearchParams(location.search).get(IGrace.CONDITION) === IGrace.TRASHED,
                delete_success_message = `*${collection_name}* ${collection} has been ${collection_trashed ? IGrace.DELETED() : IGrace.REMOVED()}`,
                delete_cancel_message  = `${
                    collection === IGrace.ADDRESS
                        ? `The ${collection} of the ${IGrace.USER} (${collection_name})`
                        : `Your ${collection}`
                } is safe`;

            const delete_options = {
                deleteRoute:       delete_route,
                mainPage:          main_page,
                action:            IGrace.DELETE,
                collectionTrashed: collection_trashed,
                successMessage:    delete_success_message,
            };

            Common.confirmMessage(`${collection_trashed ? IGrace.DELETE : IGrace.REMOVE} this ${collection === IGrace.ADDRESS ? `${collection} of the ${IGrace.USER} (${collection_name})?` : `${collection} (${collection_name})?`} ${collection_trashed ? `<br><br> Rest items related to it will be ${IGrace.DELETED()}` : ''}`)
                .then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        $.ajax({
                            ...Common.ajaxDeleteItems(delete_options),
                            error: (err) => Common.handleDeleteErrors({
                                ...delete_options,
                                error:          err,
                                cancelMessage:  delete_cancel_message,
                            }),
                        });
                    }
                    else if (willDelete.dismiss === Swal.DismissReason.cancel) {
                        Common.cancelMessage(delete_cancel_message);
                    }
                });
        });
    },

    /**
     * Delete Multiple Items ajax request.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteMultipleRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.DELETE, collection, true), function (e) {
            e.preventDefault();

            const
                target                       = $(this),
                delete_all_route             = target.data('route'),
                main_page                    = target.data('main'),
                collections_trashed          = new URLSearchParams(location.search).get(IGrace.CONDITION)  === IGrace.TRASHED,
                selected_rows                = $(`.check-${IGrace.ROW}:checked`).map((_, checkedRow) => $(checkedRow).val()).get(),
                is_multiple_selection        = selected_rows.length > 1,
                delete_multi_success_message = `Selected ${is_multiple_selection ? `${collection} have` : `${IGrace.SINGULARIZE(collection)} has`} been ${collections_trashed ? IGrace.DELETED() : IGrace.REMOVED()}`,
                delete_multi_cancel_message  = `Your selected ${is_multiple_selection ? `${collection} are` : `${IGrace.SINGULARIZE(collection)} is`} safe`;

            if ($.isEmptyObject(selected_rows)) {
                return Swal.fire({
                    title: 'Oops!',
                    text: `Please select at least one ${IGrace.SINGULARIZE(collection)} to ${collections_trashed ? IGrace.DELETE : IGrace.REMOVE}`,
                    icon: IGrace.WARNING,
                    showConfirmButton: true,
                });
            }

            const delete_options = {
                deleteRoute:       delete_all_route,
                isMultiple:        true,
                selectedIds:       selected_rows,
                mainPage:          main_page,
                action:            IGrace.DELETE,
                collectionTrashed: collections_trashed,
                successMessage:    delete_multi_success_message,
            };

            Common.confirmMessage(`${collections_trashed ? IGrace.DELETE : IGrace.REMOVE} selected ${is_multiple_selection ? collection : IGrace.SINGULARIZE(collection)}? ${collections_trashed ? `<br><br> Rest items related to it will be ${IGrace.DELETED()}` : ''}`)
                .then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        $.ajax({
                            ...Common.ajaxDeleteItems(delete_options),
                            error: (err) => Common.handleDeleteErrors({
                                ...delete_options,
                                error:         err,
                                cancelMessage: delete_multi_cancel_message,
                            }),
                        });
                    }
                    else if (willDelete.dismiss === Swal.DismissReason.cancel) {
                        Common.cancelMessage(delete_multi_cancel_message);
                    }
                });
        });
    },


    /* ---------------------------------- RESTORE REQUEST ---------------------------------- */
    /**
     * Restore Item ajax request.
     *
     * @param collection
     * @return {void}
     */
    ajaxRestoreRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.RESTORE, collection), function (e) {
            e.preventDefault();

            const
                target                  = $(this),
                restore_route           = target.data('route'),
                collection_name         = target.data(IGrace.NAME),
                main_page               = target.data('main'),
                restore_success_message = `${collection === IGrace.ADDRESS
                    ? `The ${collection} of the ${IGrace.USER} (${collection_name})`
                    : `*${collection_name}* ${collection}`} has been ${IGrace.RESTORED()}`;

            $.ajax({
                url: `${restore_route}?restore=true`,
                method: IGrace.PUT,
                success: () => {
                    $.get(`${main_page}${Common.routeParamsSeperator(main_page)}page=${Common.currentPageNumber()}`)
                        .done((data) => {
                            Common.updateTableRows({
                                data:   data,
                                action: IGrace.RESTORE,
                            });
                        })
                        .fail(Common.somethingWentWrongError);

                    Common.successMessage(IGrace.RESTORED(), restore_success_message)
                },
                error: () => Common.somethingWentWrongError,
            });
        });
    },

    /**
     * Restore Multiple Items ajax request.
     *
     * @param collection
     * @return {void}
     */
    ajaxRestoreMultipleRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.RESTORE, collection, true), function (e) {
            e.preventDefault();

            const
                target                        = $(this),
                restore_all_route             = target.data('route'),
                main_page                     = target.data('main'),
                selected_rows                 = $(`.check-${IGrace.ROW}:checked`).map((_, checkedRow) => $(checkedRow).val()).get(),
                is_multiple_selection         = selected_rows.length > 1,
                restore_multi_success_message = `Selected ${is_multiple_selection ? `${collection} have` : `${IGrace.SINGULARIZE(collection)} has`} been ${IGrace.RESTORED()}`;

            if ($.isEmptyObject(selected_rows)) {
                return Swal.fire({
                    title:             'Oops!',
                    text:              `Please select at least one ${IGrace.SINGULARIZE(collection)} to ${IGrace.RESTORE}`,
                    icon:              IGrace.WARNING,
                    showConfirmButton: true,
                });
            }

            $.ajax({
                url: `${restore_all_route}?selected_ids=${selected_rows}&restore=true`,
                method: IGrace.PUT,
                success: () => {
                    $.get(`${main_page}${Common.routeParamsSeperator(main_page)}page=${Common.currentPageNumber()}`)
                        .done((data) => {
                            Common.updateTableRows({
                                data:   data,
                                action: IGrace.RESTORE,
                            });
                        })
                        .fail(Common.somethingWentWrongError);

                    Common.successMessage(IGrace.RESTORED(), restore_multi_success_message);
                },
                error: () => Common.somethingWentWrongError,
            });
        });
    },


    /* ---------------------------------- SEARCH & FILTER REQUESTS ---------------------------------- */
    /**
     * Search ajax request.
     *
     * @return {void}
     */
    ajaxSearchRequest: () => {
        $(document).on(IGrace.KEYUP, `input[type="${IGrace.SEARCH}"]`, function (e) {
            e.preventDefault();

            const
                target             = $(this),
                search_form        = target.parents(`#${IGrace.SEARCH}_form`),
                route              = search_form.attr('action'),
                search_value       = target.val(),
                no_results_img_src = search_form.data('no_results');

            if (target.is(`#${IGrace.USER}_${IGrace.SEARCH}_${IGrace.PLURALIZE(IGrace.PRODUCT)}`)) return;

            $.get(`${route}${Common.routeParamsSeperator(route)}search_value=${search_value}`)
                .done((data) => Common.searchFilterSuccessResponse(data))
                .fail((err) =>
                    Common.responseJsonError(err, true) === 'no-results'
                        ? Common.searchFilterErrorResponse(no_results_img_src)
                        : Common.somethingWentWrongError()
                );
        });
    },

    /**
     * Clear Search/Filter ajax request.
     *
     * @return {void}
     */
    ajaxClearSearchFilterRequest: () => {
        $(document).on(IGrace.CLICK, `#clear_${IGrace.SEARCH}, #clear_${IGrace.FILTER}`, function (e) {
            e.preventDefault();

            const
                target              = $(this),
                route               = target.attr('href') ?? target.data('route'),
                search_form         = $('#search_form'),
                filter_form         = $(`.${IGrace.FILTER}-form`),
                clear_search_button = $(`.clear-${IGrace.SEARCH}-btn`),
                dashboard_main      = `.${IGrace.DASHBOARD}-main`,
                clearForm           = (form) => form.trigger('reset');

            $.get(route)
                .done((data) => {
                    if ($(dashboard_main).length) {
                        $(dashboard_main).html($(data).find(dashboard_main).html());

                        Admin.googleGeoChartConfig();
                        Admin.googlePieChartConfig();
                    }

                    Common.searchFilterSuccessResponse(data);

                    if (search_form.length) clearForm(search_form);
                    if (filter_form.length) clearForm(filter_form);
                    if (clear_search_button.length) clear_search_button.css({'opacity': '0', 'visibility': 'hidden'});

                    $(IGrace.ERROR_ELEMENT(IGrace.FILTER)).empty();
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- NOTIFICATIONS REQUEST ---------------------------------- */
    /**
     * Get Notifications event request.
     *
     * @return {void}
     */
    eventGetNotificationsRequest: () => {
        if (!(!!window.EventSource)) {
            Common.somethingWentWrongError('Your browser does not support EventSource (SSE)!');
            return;
        }

        const source = new EventSource('/notification');

        const
            notifications_details_list  = $('.notifications-details'),
            notifications_count_element = $('.notifications-count');

        const notificationsItems = (list) => list.find('li:not(.no-notifications) .mark-as-read-icon');

        const showHideNotificationsCount = (list) => notificationsItems(list).length > 0
            ? notifications_count_element.text(notificationsItems(list).length).css('display', 'inline-block')
            : notifications_count_element.css('display', 'none');

        showHideNotificationsCount(notifications_details_list);

        source.onmessage = (event) => {
            try {
                const
                    notification                = JSON.parse(event.data).notification,
                    notifications_details_list  = $('.notifications-details'),
                    notification_sound          = $('#notification_sound');

                if (notifications_details_list.find('.no-notifications').length) notifications_details_list.empty();

                if ($(`li[id='notification${notification.id}']`).length) return;

                notifications_details_list.prepend(`
                    <li role="listitem" id="notification${notification.id}" class="notification-item position-relative d-flex align-items-center w-100 bg-highlight">
                        <div class="d-grid gap-2"
                            <p class="notifications-text mb-2">${notification.message}</p>
                            <span class="notifications-timer text-muted">${notification.created_at}</span>
                        </div>

                        <a href="${location.origin}/${IGrace.ADMIN}/notifications/mark-as-read?id=${notification.id}" role="link" title="Mark as read" class="notifications-link mark-as-read-icon">
                            <span class="new-notification-circle d-inline-block rounded-pill bg-info"></span>
                        </a>
                    </li>
                `);

                showHideNotificationsCount(notifications_details_list);

                if (notification_sound.length) {
                    notification_sound.trigger('play');
                }
            }
            catch (error) {
                console.error('Failed to parse SSE message: ', error);
            }
        };

        source.onerror = () => source.close();
    },


    /* ---------------------------------- PAGINATION REQUEST ---------------------------------- */
    /**
     * Set up the pagination.
     *
     * @return {void}
     */
    ajaxPagination: () => {
        $(document).on(IGrace.CLICK, '.pagination .page-link', function (e) {
            e.preventDefault();

            const
                target = $(this),
                route  = target.data('route'),
                page   = target.attr('href').split('page=')[1],
                url    = `${route}${Common.routeParamsSeperator(route)}page=${page}`;

            if (route.includes(`${IGrace.FILTER}-${IGrace.PLURALIZE(IGrace.PRODUCT)}`)) {
                return User.ajaxFilterProducts({route: url});
            }

            $.get(url)
                .done((data) => {
                    Common.paginationResponse($('.pagination-container'), data);

                    window.isFormDirty = false;
                })
                .fail(Common.somethingWentWrongError);
        });
    },
}



/**
 * Export IGrace & Common Objects.
 */
export { IGrace, Common };
