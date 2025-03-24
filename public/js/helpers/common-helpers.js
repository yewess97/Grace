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
            cancelButton: 'btn ms-2',
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
     * Configure the rows checking settings.
     *
     * @param target
     * @return {void}
     */
    checkRowsConfig: (target) => {
        const
            check_all = $('#check_all'),
            check_row = $(`.check-${IGrace.ROW}`);

        // Check/Uncheck the (check_all) checkbox and checkboxes in the table
        if (target.is('#custom_check_all')) {
            const is_checked_all = check_all.is(':checked');

            if (check_all.is(':indeterminate')) {
                check_all.prop('indeterminate', false);
            }

            check_all.prop('checked', !is_checked_all);
            check_row.prop('checked', !is_checked_all);
        }

        // Check/Uncheck the target checkbox in the table and (check_all) checkbox
        if (target.hasClass(`custom-check-${IGrace.ROW}`)) {
            target.prev().prop('checked', !target.prev().is(':checked'));

            const checked_count = check_row.filter(':checked').length;
            check_all.prop({
                'indeterminate': checked_count > 0 && checked_count < check_row.length,
                'checked': checked_count === check_row.length
            });
        }
    },


    /**
     * Configure the form multi select settings.
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
            placeholderText: `Select ${IGrace.CAPITALIZE(relation)}`,
            filterText: 'Search...',
            selectAllText: 'Select All',
            selectionLimit: 0,
            caseSensitive: false,
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
        if (target.hasClass('selected-items')) {
            const
                num_selected_items_element = target.prevAll(':eq(1)'),
                selected_items_length = target.children().length,
                multiselect_items = target.parent().next().find('.items'),
                multiselect_max_num_items = multiselect_items.children().length - 1,
                multi_select = target.parents('.filter-multi-select'),
                multiselect_label = multi_select.prev(),
                multiselect_hidden_input = multi_select.next(),
                select_all = multiselect_items.find('.custom-control:first-child'),
                select_all_label = select_all.find('.custom-control-label'),
                select_all_checkbox = select_all.find('.custom-checkbox'),
                is_hidden = selected_items_length > 3,
                related_collection = (collection) => multiselect_label.html().includes(IGrace.CAPITALIZE(IGrace.PLURALIZE(collection))),
                multiselect_related_collection_label = related_collection(IGrace.CATEGORY) || related_collection(IGrace.SUBCATEGORY),
                top_value = multiselect_related_collection_label
                    ? (selected_items_length > 0 && selected_items_length <= 3 ? '7%' : '18%')
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
        }
    },


    /**
     * Show the data of the multi select input.
     *
     * @param args
     * @return {void}
     */
    showMultiSelectData: (args) => {
        const { userType, collection, collectionName, relatedCollection } = args;

        const
            action_collection = userType === IGrace.ADMIN
                ? IGrace.UPDATE_COLLECTION(collectionName)
                : IGrace.ADD_COLLECTION(IGrace.CART),
            relational_collection = userType === IGrace.ADMIN
                ? IGrace.PLURALIZE(relatedCollection)
                : relatedCollection,
            related_collection_words = relational_collection.split('_'),
            relation = `${related_collection_words.length > 1 ? related_collection_words[1] : related_collection_words[0]}`,
            select_element_class = userType === IGrace.ADMIN
                ? IGrace.CLASS(`${collectionName}-${relational_collection}`)
                : IGrace.CLASS(relational_collection),
            select_element_id = `${action_collection}_${relational_collection}`,
            select_element_name = `${select_element_id}[]`,
            action_collection_related_collection = $(`.${IGrace.CLASS(select_element_id)}`),
            multi_related_collection = action_collection_related_collection.data(relational_collection),
            filter_multi_select_element = action_collection_related_collection.find('.filter-multi-select'),
            related_collection_hidden_input = action_collection_related_collection.find(`input[name="${select_element_name}"]:hidden`);

        filter_multi_select_element.remove();
        related_collection_hidden_input.val('');

        let related_collection_select_element = $('<select>', {
            name: select_element_name,
            id: select_element_id,
            class: `${select_element_class} d-none`,
            multiple: true,
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
                        text: option_text,
                        value: option_value,
                    }));
                });
            },
            default: () => {
                $.each((collection[IGrace.PLURALIZE(relation)]), (_, product_size) =>
                    $.each((Object.entries(product_size).filter(([key, _]) => key === IGrace.SIZE)), (_, value) =>
                        $.each((Object.entries(multi_related_collection).filter(([_, size_value]) => +size_value === +value[1])), (_, [size, size_value]) =>
                            related_collection_select_element.append($('<option>', {
                                text: size,
                                value: size_value,
                            }))
                        )
                    )
                );
            },
        };

        (role_actions[userType] || role_actions.default)();

        related_collection_select_element.insertBefore(related_collection_hidden_input);

        const related_collection_element = $(`.${select_element_class}`);

        if (userType === IGrace.ADMIN) {
            $.each((collection[relation]), (_, related_collection) => related_collection_element.find('option')
                .filter((_, rel_collection) => +rel_collection.value === (relational_collection.includes(IGrace.SIZE) ? +related_collection[IGrace.SIZE] : +related_collection[IGrace.ID]))
                .attr('selected', true));
        }

        Common.formMultiSelectConfig(action_collection, relational_collection);
        Common.formSelectConfig();

        related_collection_element.remove();

        const all_multi_related_collection = filter_multi_select_element.end().find('.items input[type="checkbox"]');

        let multi_selected_related_collection_values = [];

        $.each((all_multi_related_collection), (_, related_collection) => {
            if ($(related_collection).is(':checked')) {
                multi_selected_related_collection_values.push($(related_collection).val());
            }
        });

        multi_selected_related_collection_values = multi_selected_related_collection_values.filter((related_collection_value) => related_collection_value !== '').join(',');

        all_multi_related_collection.first().val(multi_selected_related_collection_values);
        related_collection_hidden_input.val(multi_selected_related_collection_values);
    },


    /**
     * Handle the select-all checkbox for multiple items with hidden input.
     *
     * @param args
     * @return {any|*[]}
     */
    handleSelectAllMultiItemsWithHiddenInput: (args) => {
        let { target, actionCollection, multiSelectedValuesList, relation } = args;

        if (target.is(`input[name="${actionCollection}_${relation}[]"]`)) {
            const
                is_checked              = target.is(':checked'),
                is_select_all           = target.next().html().includes('All'),
                all_items               = target.parents('.items').find('input[type="checkbox"]'),
                select_all_checkbox     = all_items.first(),
                related_collection_hidden_input = target.parents('.filter-multi-select').next();

            const check_actions = {
                true: () => {
                    const select_actions = {
                        true: () => {
                            multiSelectedValuesList.length = 0;
                            select_all_checkbox.val('');
                            related_collection_hidden_input.val('');

                            $.each((all_items), (_, selected_item) => multiSelectedValuesList.push($(selected_item).val() || ''));

                            select_all_checkbox.next().html('Unselect All');
                        },
                        false: () => multiSelectedValuesList.push(target.val()),
                    };

                    select_actions[is_select_all]();
                },
                false: () => {
                    is_select_all
                        ? multiSelectedValuesList.length = 0
                        : multiSelectedValuesList.splice($.inArray(target.val(), multiSelectedValuesList), 1);

                    select_all_checkbox.next().html('Select All');
                },
            };

            check_actions[is_checked]();

            multiSelectedValuesList = multiSelectedValuesList.filter((value) => value !== '').join(',');

            select_all_checkbox.val(multiSelectedValuesList);
            related_collection_hidden_input.val(multiSelectedValuesList);
        }
    },


    /**
     * Configure the form select settings.
     *
     * @return {void}
     */
    formSelectConfig: () => {
        $('.viewbar').removeClass('dropdown-toggle').addClass('form-select');

        const form_select = $('.form-select');

        $.each((form_select), (_, select_element) => {
            // Add some styles on the label of the select element
            const label_style = {
                'top': '18%',
                'font-size': 'var(--twelve-pixels)',
            };
            $(select_element).hasClass('viewbar')
                ? $(select_element).parent().prev().css(label_style)
                : $(select_element).prev().css(label_style);

            // Add some styles on the select element
            $(select_element).css('padding-block', 'var(--twenty-four-pixels) 0.357rem');
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

                if (!value.toString().includes(',') || is_file_input) {
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
                text_value = target.val();

            let
                counter_element,
                counter = target.attr('maxlength') - text_value.length;

            textArea.includes(IGrace.REVIEW)
                ? counter_element = target.parents().eq(1).next().next().find('> .chars-counter')
                : counter_element = target.parent().next().addClass('mt-3 mb-2');

            counter_element.text(`${counter} characters remaining`);

            if ($.isEmptyObject(text_value)) {
                counter_element.empty();
                counter_element.removeClass('mt-3 mb-2');
            }
        });
    },


    /**
     * Add some classes, styles, and attributes on each image.
     *
     * @return {void}
     */
    imageConfig: () => $.each(($('img')), (_, image) =>
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
            truncate = $('.truncate p'),
            truncate_text = '.truncate-text',
            show_char = 70;

        $.each((truncate), (_, truncateElement) => {
            const data = $(truncateElement).html();
            if (data.length > show_char) {
                let content =
                    `
            <div class="truncate-text" style="display:block">
                ${data.substring(0, show_char)}
                <span>.... <a class="show-less fw-600">&nbsp;Show More</a></span>
            </div>
            <div class="truncate-text" style="display:none">
                ${data}
                <a class="show-less less fw-600 lh-sm">&nbsp; Show Less</a>
            </div>
            `;

                $(truncateElement).html(content);
            }
        });

        // Truncate the text if the (Show More) is clicked on
        $(document).on(IGrace.CLICK, '.show-less', function (e) {
            e.preventDefault();

            const
                closest_truncate_text = $(this).closest(truncate_text),
                is_less = $(this).hasClass('less');

            closest_truncate_text.prev(truncate_text).toggle(is_less);
            closest_truncate_text.slideToggle(is_less);
            closest_truncate_text.toggle(!is_less);
            closest_truncate_text.next(truncate_text).fadeToggle(!is_less);
        });
    },


    /**
     * Arrange the table rows.
     *
     * @param startIndex
     * @returns {*}
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
     * Update the table rows after deletion/restoration.
     *
     * @param ids
     * @return {void}
     */
    updateTableRows: (ids) => {
        $(`.check-${IGrace.ROW}`).prop('checked', false);
        $('#check_all').prop({'checked': false, 'indeterminate': false});

        $.each((ids), (_, id) => Common.removeRow($(`#${IGrace.ROW}_${id}`), () => Common.arrangeTableRows()));
    },


    /**
     * Scroll to the top of the page.
     *
     * @return {void}
     */
    scrollToTop: () => {
        const scroll_to_top = '.scroll-to-top';

        scroll(() => $(window).scrollTop() > 150 ? $(scroll_to_top).fadeIn('slow') : $(scroll_to_top).fadeOut('slow'));

        $(document).on(IGrace.CLICK, scroll_to_top, function (e) {
            e.preventDefault();

            $('html, body').animate({ scrollTop: 0 }, 500);
        });
    },


    /*================================== Concerned With AJAX Requests ==================================*/

    /**
     * Set up ajax request with csrf token and other settings.
     *
     * @return {void}
     */
    ajaxSetup: () => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            async: false,
            cache: false,
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

        if (country_elements.length) {
            $.getJSON('https://restcountries.com/v3.1/all', (countries) => {
                let countries_names = countries.map((country) => country.name.common);

                countries_names.sort((opt1, opt2) => opt1.localeCompare(opt2));

                const countries_options = countries_names.map((countryName) =>
                    `<option value="${countryName}">${countryName}</option>`
                ).join("");

                $.each((country_elements), (_, countryElement) =>
                    $(countryElement).append(countries_options)
                );
            })
                .fail(() => console.error("Failed to fetch countries!"));
        }
    },


    /**
     * Handle the error status from the server.
     *
     * @param error
     * @return {string}
     */
    errorStatus: (error) => error.status,


    /**
     * Handle the response message or errors from the server.
     *
     * @param error
     * @param isMessage
     * @return {string}
     */
    responseJsonError: (error, isMessage = false) =>
        isMessage
            ? error.responseJSON.message
            : error.responseJSON.errors,


    /**
     * Display the error messages in the given element
     * for the given errors object (returned from the server).
     *
     * @param element
     * @param errors
     * @param status
     * @return {void}
     */
    errorMessage: (element, errors, status = null) => {
        const
            error_element = `.${element}-${IGrace.ERROR}`,
            login_btn = $(`.${IGrace.LOGIN}-btn`),
            show_error_class = `show-${IGrace.ERROR}`,
            margin = Common.urlLastDirectory().includes(IGrace.CHECKOUT) ? 'mt-2 mb-1' : 'mt-3',
            hide_error_element = (error_element) => $(error_element).empty().parent().removeClass(`${show_error_class} ${margin}`);

        hide_error_element(error_element);

        $.each((errors), (attr, msg) => {
            attr = attr.replace(/\.\d+$/, ''); // Remove the trailing if there is
            const attr_err = $(`#${attr}_${IGrace.ERROR}`);
            $(attr_err).parent().addClass(`${show_error_class} ${!status ? margin : ''}`);

            if (status === 429) {
                let
                    seconds = msg,
                    error_message = `Too many ${IGrace.LOGIN} attempts. Please try again in <span id="count_down">${seconds}</span> seconds`;

                login_btn.attr('disabled', 'disabled');

                attr_err.html(`<li>${error_message}</li>`);

                const login_attempts_interval = setInterval(() => {
                    const login_actions = {
                        true: () => {
                            $('#count_down').text(seconds);
                            seconds--;
                        },
                        false: () => {
                            clearInterval(login_attempts_interval);
                            hide_error_element(attr_err);
                            login_btn.removeAttr('disabled');
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
            html: `Are you sure you want to ${message}`,
            icon: IGrace.WARNING,
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: `Yes, ${message.includes(IGrace.REMOVE) ? IGrace.REMOVE : IGrace.DELETE}!`,
            cancelButtonText: 'No, cancel!',
        }),


    /**
     * Display the success message.
     *
     * @param status
     * @param message
     * @param extra
     * @return {void}
     */
    successMessage: (status, message, extra = null) => {
        const
            properties = {
                title: `${IGrace.CAPITALIZE(status)}!`,
                html: message,
                icon: IGrace.SUCCESS,
                showConfirmButton: true,
            },

            swal_message = () => Swal.fire({
                ...properties,
                html: `${message} successfully!`,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            });

        if (extra) {
            if (extra.includes(IGrace.FORGOT_PASSWORD())) {
                return Common.swalWithButtons.fire({
                    ...properties,
                    confirmButtonText: 'Ok, Thanks',
                });
            }

            if (extra.includes(IGrace.RESET_PASSWORD())) {
                return Common.swalWithButtons.fire(properties)
                    .then((ok) => {
                        if (ok.isConfirmed) {
                            location.href = `/${IGrace.LOGIN}`;
                        }
                    });
            }

            if (extra.includes(IGrace.CAPITALIZE(IGrace.ORDER))) {
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

            if (extra.includes(IGrace.PLURALIZE(IGrace.REVIEW))) {
                swal_message();
                return setTimeout(() => User.ajaxUpdateReviewsContentRequest(extra), 1800);
            }

            if ($.inArray(message, [IGrace.CAPITALIZE(IGrace.ORDER), IGrace.CAPITALIZE(IGrace.REVIEW)])
                && message.includes(IGrace.UPDATED())
                && extra.includes(IGrace.ADMIN))
            {
                swal_message();
                return setTimeout(() => location.reload(), 1800);
            }
        }

        swal_message();
    },


    /**
     * Display the cancelation message when canceled.
     *
     * @param message
     * @return {void}
     */
    cancelMessage: (message) =>
        Swal.fire({
            title: 'Canceled',
            html: `${message} <i class="far fa-smile"></i>`,
            icon: IGrace.ERROR,
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true,
        }),


    /**
     * Display the error message in a sweet alert.
     *
     * @param error
     * @return {void}
     */
    swalResponseJsonErrorMessage: (error) => Common.somethingWentWrongError(Common.responseJsonError(error, true)),


    /**
     * Display that something went wrong with the ajax request
     * if there's an error but the validation error
     * if confirmed, reload the page.
     *
     * @return {void}
     */
    somethingWentWrongError: (message = "Something went wrong. <br> Please try again later!") =>
        Common.swalWithButtons.fire({
            title: 'Sorry!',
            html: message,
            icon: IGrace.ERROR,
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
     * @return {void}
     */
    removeErrorsWhenEditModelHides: (role) =>
        $(document).on('hidden.bs.modal', `.${role}-${IGrace.EDIT}-modal`, function (e) {
            e.preventDefault();

            $(this).find(IGrace.ERROR_ELEMENT(IGrace.UPDATE)).empty();
        }),


    /**
     * Set up the pagination response after an ajax request.
     *
     * @param element
     * @param data
     * @return {void}
     */
    paginationResponse: (element, data) => {
        element.html(data['html']);

        Common.truncateText();
        Common.imageConfig();
        Common.arrangeTableRows((data['current_page'] - 1) * data['per_page']);
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
     * @return {void}
     */
    searchFilterErrorResponse: (imageSrc) =>
        $('.search-table').html(
            `<div class="d-flex justify-content-center mt-5">
                <img src=${imageSrc} alt="No Results Found" class="img-fluid h-100" style="width:300px">
            </div>`
        ),


    /* ---------------------------------- EDIT REQUEST ---------------------------------- */

    /**
     * Edit Address Ajax Request.
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
                    country.find('option').removeAttr('selected');
                    country.find('option')
                        .filter((_, address_country) => address_country.value === address[IGrace.COUNTRY])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ADDRESS, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /**
     * Edit review ajax request
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
     * Ajax request to delete a single or multiple items.
     *
     * @param options
     * @returns {{method: string, success: *, url: string}}
     */
    ajaxDeleteItems: (options) => {
        const { deleteRoute, multiple, forceDeleteRequest, collectionId, selectedIds, collectionTrashed, successMessage } = options;

        return {
            url: `${deleteRoute}${multiple ? `?selected_ids=${selectedIds}` : '?'}${forceDeleteRequest > 0 ? `${multiple ? '&' : ''}force_delete=${forceDeleteRequest}` : ''}`,
            method: IGrace.DELETE.toUpperCase(),
            success: () => {
                multiple
                    ? Common.updateTableRows(selectedIds)
                    : Common.updateTableRows([collectionId]);

                Common.successMessage((collectionTrashed ? IGrace.DELETED() : IGrace.REMOVED()), successMessage);
            },
        };
    },


    /**
     * Show a force delete confirmation message before deletion.
     *
     * @param options
     * @return {void}
     */
    forceDeleteConfirmation: (options) => {
        const { error, deleteRoute, forceDeleteRequests, multiple, selectedIds, successMessage, cancelMessage } = options;

        const delete_options = {
            deleteRoute: deleteRoute,
            multiple:    multiple,
            selectedIds: selectedIds,
        };

        const force_delete_requests = [1, 2];

        Common.swalWithButtons.fire({
            title: 'Oops!',
            html: `${Common.responseJsonError(error, true)} <br><br> Are you sure you want to delete ${multiple ? 'the selected items' : 'this item'}?`,
            icon: IGrace.WARNING,
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: `Yes, ${IGrace.DELETE} ${multiple ? 'them' : 'it'}!`,
            cancelButtonText: 'No, cancel!',
        })
            .then((willDelete) => {
                if (willDelete.isConfirmed) {
                    let force_delete_request = $.type(forceDeleteRequests) !== 'undefined' && forceDeleteRequests.length === 1 ? force_delete_requests.pop() : force_delete_requests.shift();
                    $.ajax({
                        ...Common.ajaxDeleteItems({
                            ...delete_options,
                            successMessage: successMessage,
                            forceDeleteRequest: force_delete_request,
                        }),
                        error: (err) => Common.handleDeleteErrors({
                            error: err,
                            ...delete_options,
                            cancelMessage: cancelMessage,
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
        const { error, deleteRoute, forceDeleteRequests, multiple, selectedIds, successMessage, cancelMessage } = options;

        Common.errorStatus(error) === 404
            ? Common.forceDeleteConfirmation({
                error: error,
                deleteRoute: deleteRoute,
                forceDeleteRequests: forceDeleteRequests,
                multiple: multiple,
                selectedIds: selectedIds,
                successMessage: successMessage,
                cancelMessage: cancelMessage,
            })
            : Common.somethingWentWrongError();
    },


    /**
     * Delete Ajax Request.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.DELETE, collection), function (e) {
            e.preventDefault();

            const
                target             = $(this),
                delete_route       = target.data('route'),
                collection_id      = target.data(IGrace.ID),
                collection_name    = target.data(IGrace.NAME),
                collection_trashed = new URLSearchParams(location.search).get(IGrace.CONDITION),
                delete_success_message = `*${collection_name}* ${collection} has been ${collection_trashed ? IGrace.DELETED() : IGrace.REMOVED()}`,
                delete_cancel_message  = `${
                    collection === IGrace.ADDRESS
                        ? `The ${collection} of the ${IGrace.USER} (${collection_name})`
                        : `Your ${collection}`
                } is safe`;

            const delete_options = {
                deleteRoute:       delete_route,
                collectionId:      collection_id,
                collectionTrashed: collection_trashed,
                successMessage:    delete_success_message,
            };

            Common.confirmMessage(`${collection_trashed ? IGrace.DELETE : IGrace.REMOVE} this ${collection === IGrace.ADDRESS ? `${collection} of the ${IGrace.USER} (${collection_name})?` : `${collection} (${collection_name})?`} ${collection_trashed ? `<br><br> Rest items related to it will be ${IGrace.DELETED()}` : ''}`)
                .then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        $.ajax({
                            ...Common.ajaxDeleteItems(delete_options),
                            error: (err) => Common.handleDeleteErrors({
                                error: err,
                                ...delete_options,
                                cancelMessage: delete_cancel_message,
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
     * Delete multiple items Ajax Request.
     *
     * @param collection
     * @return {void}
     */
    ajaxDeleteMultipleRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.DELETE, collection, true), function (e) {
            e.preventDefault();

            const
                delete_all_route = $(this).data('route'),
                collections_trashed = new URLSearchParams(location.search).get(IGrace.CONDITION),
                selected_rows = $(`.check-${IGrace.ROW}:checked`).map((_, checked_row) => $(checked_row).val()).get(),
                is_multiple_selection = selected_rows.length > 1,
                delete_multi_success_message = `Selected ${is_multiple_selection ? `${collection} have` : `${IGrace.SINGULARIZE(collection)} has`} been ${collections_trashed ? IGrace.DELETED() : IGrace.REMOVED()}`,
                delete_multi_cancel_message = `Your selected ${is_multiple_selection ? `${collection} are` : `${IGrace.SINGULARIZE(collection)} is`} safe`;

            const delete_options = {
                deleteRoute:       delete_all_route,
                multiple:          true,
                selectedIds:       selected_rows,
                collectionTrashed: collections_trashed,
                successMessage:    delete_multi_success_message,
            };

            if ($.isEmptyObject(selected_rows)) {
                return Swal.fire({
                    title: 'Oops!',
                    text: `Please select at least one ${IGrace.SINGULARIZE(collection)} to ${collections_trashed ? IGrace.DELETE : IGrace.REMOVE}`,
                    icon: IGrace.WARNING,
                    showConfirmButton: true,
                });
            }

            Common.confirmMessage(`${collections_trashed ? IGrace.DELETE : IGrace.REMOVE} selected ${is_multiple_selection ? collection : IGrace.SINGULARIZE(collection)}? ${collections_trashed ? `<br><br> Rest items related to it will be ${IGrace.DELETED()}` : ''}`)
                .then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        $.ajax({
                            ...Common.ajaxDeleteItems(delete_options),
                            error: (err) => Common.handleDeleteErrors({
                                error: err,
                                ...delete_options,
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
     * Restore Ajax Request.
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
                collection_id           = target.data(IGrace.ID),
                collection_name         = target.data(IGrace.NAME),
                restore_success_message = `${collection === IGrace.ADDRESS ? `The ${collection} of the ${IGrace.USER} (${collection_name})` : `*${collection_name}* ${collection}`} has been ${IGrace.RESTORED()}`;

            $.ajax({
                url: restore_route,
                method: IGrace.PUT,
                success: () => {
                    console.log(collection_id);
                    Common.updateTableRows([collection_id]);

                    Common.successMessage(IGrace.RESTORED(), restore_success_message)
                },
                error: () => Common.somethingWentWrongError,
            });
        });
    },

    /**
     * Restore multiple items Ajax Request.
     *
     * @param collection
     * @return {void}
     */
    ajaxRestoreMultipleRequest: (collection) => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.RESTORE, collection, true), function (e) {
            e.preventDefault();

            const
                target = $(this),
                restore_all_route = target.data('route'),
                selected_rows = $(`.check-${IGrace.ROW}:checked`).map((_, checked_row) => $(checked_row).val()).get(),
                is_multiple_selection = selected_rows.length > 1,
                restore_multi_success_message = `Selected ${is_multiple_selection ?  `${collection} have` : `${IGrace.SINGULARIZE(collection)} has`} been ${IGrace.RESTORED()}`;

            if ($.isEmptyObject(selected_rows)) {
                return Swal.fire({
                    title: 'Oops!',
                    text: `Please select at least one ${IGrace.SINGULARIZE(collection)} to ${IGrace.RESTORE}`,
                    icon: IGrace.WARNING,
                    showConfirmButton: true,
                });
            }

            $.ajax({
                url: `${restore_all_route}?selected_ids=${selected_rows}`,
                method: IGrace.PUT,
                success: () => {
                    Common.updateTableRows(selected_rows);

                    Common.successMessage(IGrace.RESTORED(), restore_multi_success_message);
                },
                error: () => Common.somethingWentWrongError,
            });
        });
    },


    /* ---------------------------------- SEARCH & FILTER REQUEST ---------------------------------- */

    /**
     * Search Ajax Request.
     */
    ajaxSearchRequest: () => {
        $(document).on(IGrace.KEYUP, 'input[type="search"]', function (e) {
            e.preventDefault();

            const
                target             = $(this),
                search_form        = target.parents('#search_form'),
                route              = search_form.attr('action'),
                search_value       = target.val(),
                no_results_img_src = search_form.data('no_results');

            if (target.is('#search_products')) return;



            $.ajax({
                url: `${route}?search_value=${search_value}`,
                method: IGrace.GET,
                success: (data) => Common.searchFilterSuccessResponse(data),
                error: (err) => {
                    if (Common.responseJsonError(err, true) === 'no-results') {
                        return Common.searchFilterErrorResponse(no_results_img_src);
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /**
     * Filter Ajax Request.
     */
    ajaxFilterRequest: (args) => {
        const { collection, eventType = IGrace.SUBMIT, element = 'form' } = args;

        $(document).on(eventType, `#${IGrace.FILTER}_${collection}_${element}`, function (e) {
            e.preventDefault();

            const
                target = $(this),
                filter_form = eventType === IGrace.CHANGE
                    ? target.parents(`#${IGrace.FILTER}_${IGrace.PLURALIZE(IGrace.USER)}_form`)
                    : target,
                route = filter_form.attr('action'),
                form_data = new FormData(filter_form[0]),
                no_results_img_src = filter_form.data('no_results');

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                success: (data) => {
                    const actions = {
                        [IGrace.DASHBOARD]: () => {
                            $(`.${IGrace.DASHBOARD}-main`).html(data);

                            Admin.googleGeoChartConfig();
                            Admin.googlePieChartConfig();
                            Common.arrangeTableRows();
                        },
                        default: () => Common.searchFilterSuccessResponse(data),
                    };

                    (actions[collection] || actions.default)();

                    $(IGrace.ERROR_ELEMENT(IGrace.FILTER)).empty();
                },
                error: (err) => {
                    if (Common.errorStatus(err) === 422) {
                        return Common.errorMessage(IGrace.FILTER, Common.responseJsonError(err));
                    }

                    if (Common.responseJsonError(err, true) === 'no-results') {
                        return Common.searchFilterErrorResponse(no_results_img_src);
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /**
     * Clear Search/Filter Ajax Request.
     */
    ajaxClearSearchFilterRequest: () => {
        $(document).on(IGrace.CLICK, `#clear_${IGrace.SEARCH}, #clear_${IGrace.FILTER}`, function (e) {
            e.preventDefault();

            const
                target = $(this),
                route               = target.attr('href') ?? target.data('route'),
                search_form         = $('#search_form'),
                filter_form         = $(`.${IGrace.FILTER}-form`),
                clear_search_button = $(`.clear-${IGrace.SEARCH}-btn`),
                dashboard_main      = $(`.${IGrace.DASHBOARD}-main`),
                clear_form          = (form) => form[0].reset();

            $.ajax({
                url: route,
                success: (data) => {
                    if (dashboard_main.length) {
                        dashboard_main.html(data);

                        Admin.googleGeoChartConfig();
                        Admin.googlePieChartConfig();
                    }

                    Common.searchFilterSuccessResponse(data);

                    if (search_form.length) clear_form(search_form);
                    if (filter_form.length) clear_form(filter_form);
                    if (clear_search_button.length) clear_search_button.css({'opacity': '0', 'visibility': 'hidden'});

                    $(IGrace.ERROR_ELEMENT(IGrace.FILTER)).empty();
                },
                error: () => Common.somethingWentWrongError(),
            });
        });
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
                target           = $(this),
                route            = target.data('route'),
                page             = target.attr('href').split('page=')[1],
                page_query_param = `page=${page}`;

            let url = `${route}?${page_query_param}`;

            if (route.includes(IGrace.FILTER)) {
                return User.ajaxFilterProductsRequest({
                    route: route,
                    page: page,
                });
            }

            if (route.includes(IGrace.CHECKOUT)) {
                return User.ajaxCheckoutUserAddressesRequest(page);
            }

            if (route.includes('?')) {
                url = `${route}&${page_query_param}`;
            }

            $.ajax({
                url: url,
                method: IGrace.GET,
                success: (data) => Common.paginationResponse($('.pagination-container'), data),
                error: () => Common.somethingWentWrongError(),
            });
        });
    },
}



/**
 * Export IGrace & Common Objects.
 */
export { IGrace, Common };
