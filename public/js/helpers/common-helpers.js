'use strict';

import { IGrace } from "./IGrace.js";
import { Admin } from "./admin-helpers.js";
import { User } from "./user-helpers.js";
import "./common-plugins.js";


const Common = {
    /* ========================================= Global Variables ========================================= */
    countriesData: [],

    /*================================== Concerned With UI/UX ==================================*/
    /**
     * A customized version of SweetAlert2 with custom classes for the confirm and cancel buttons.
     *
     * @see https://sweetalert2.github.io/#configuration
     * @return {object}
     */
    swalWithButtons: Swal.mixin({
        customClass: {
            confirmButton: 'btn me-2',
            cancelButton:  'btn ms-2',
        },
        buttonsStyling: false,
    }),


    /**
     * Load the preloader element and fade it out when the page is fully loaded.
     *
     * @return {void}
     */
    loadPreloader: () => {
        const preloader = $('#preloader');

        const prealoader_loading = () => {
            preloader.length
                ? preloader.fadeOut('slow')
                : console.error("Preloader element not found!");
        }

        document.readyState === 'complete'
            ? prealoader_loading()
            : $(window).on('load', prealoader_loading);
    },


    /**
     * Get the last directory in the URL path to determine the current page or section.
     *
     * @return {string}
     */
    urlLastDirectory: () => {
        const directories = location.pathname.split("/");

        return directories[directories.length - 1];
    },


    /**
     * Determine the separator to use for adding query parameters to a URL
     * based on whether it already contains parameters.
     *
     * @param route
     * @return {string}
     */
    routeParamsSeperator: (route) =>
        route.includes('?')
            ? '&'
            : '?',


    /**
     * Get the current page number from the pagination component on the page.
     *
     * @return {*|number}
     */
    currentPageNumber: () => $('.page-item.active').find('.page-link').html() || 1,


    /**
     * Add some classes and styles on the select elements to make them look better.
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
     * Show the multi-select input element with the related collection data.
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
            related_collection_label             = action_collection_related_collection.find('.label-select'),
            filter_multi_select_element          = action_collection_related_collection.find('.filter-multi-select');

        filter_multi_select_element.remove();

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

        related_collection_label.after(related_collection_select_element);

        const related_collection_element = $(`.${select_element_class}`);

        if (is_admin) {
            $.each((collection[relation]), (_, relatedCollection) =>
                related_collection_element.find('option').filter((_, rel_collection) =>
                    +rel_collection.value === (relational_collection.includes(IGrace.SIZE)
                        ? +relatedCollection[IGrace.SIZE]
                        : +relatedCollection[IGrace.ID]))
                    .attr('selected', true)
            );
        }

        $(`#${select_element_id}`).formMultiSelectConfig();

        Common.formSelectConfig();

        related_collection_element.remove();

        const all_multi_related_collection = filter_multi_select_element.end().find('.items input[type="checkbox"]');

        const multi_selected_related_collection_values = all_multi_related_collection.filter(':checked')
            .map((_, relatedCollection) => $(relatedCollection).val())
            .get()
            .filter(Boolean) // "filter(Boolean)" removes empty values
            .join(',');

        all_multi_related_collection.first().val(multi_selected_related_collection_values);
    },


    /**
     * Strip the country code prefix from the formatted phone number string
     * to get the localized format for the input field.
     *
     * @param formattedStr
     * @param countryCallingCode
     * @return {string}
     */
    stripCountryPrefix: (formattedStr, countryCallingCode) => {
        let
            code_digits_count = countryCallingCode.replace(/\D/g, '').length,
            digit_counter     = 0,
            cut_index         = 0;

        $.each((formattedStr.split('')), (index, char) => {
            if (/\d/.test(char)) {
                digit_counter++;
            }

            if (digit_counter === code_digits_count) {
                cut_index = index + 1;
                return false;
            }
        });

        return formattedStr.substring(cut_index).replace(/^[^0-9]+/, '').trim();
    },


    /**
     * Generate a dynamic placeholder for the phone input field
     * based on the selected country code using the libphonenumber library if available.
     *
     * @param countryCallingCode
     * @return {string}
     */
    generateDynamicPlaceholder: (countryCallingCode) => {
        if (typeof libphonenumber !== 'undefined' && libphonenumber.AsYouType) {
            let
                generic_mock = "123456789012345".substring(0, 10),
                formatted_str = new libphonenumber.AsYouType().input(countryCallingCode + generic_mock);

            return Common.stripCountryPrefix(formatted_str, countryCallingCode);
        }

        return "101 183 6243";
    },


    /**
     * Form configuration for the phone input field in the address form,
     * including the country selector dropdown.
     *
     * @param action
     * @param countries
     * @return {void}
     */
    formPhoneConfig: (action, countries) => {
        const address_phone_container = $(`#${action}_${IGrace.ADDRESS}_${IGrace.PHONE}_container`);

        const
            country_selector              = address_phone_container?.find(`.${IGrace.ADDRESS}-${IGrace.PHONE}-${IGrace.COUNTRY}-selector`),
            selected_country_flag         = country_selector?.find('.selected-flag'),
            selected_country_calling_code = country_selector?.find('.selected-calling-code'),
            selected_country_chevron      = country_selector?.find('.chevron-icon'),
            phone_input                   = address_phone_container?.find(`.${action}-${IGrace.ADDRESS}-${IGrace.PHONE}`),
            dropdown_container            = address_phone_container?.find(`.${IGrace.ADDRESS}-${IGrace.PHONE}-dropdown-container`),
            search_input                  = dropdown_container?.find(`.${IGrace.COUNTRY}-search-input`),
            countries_list                = dropdown_container?.find(`.${IGrace.PLURALIZE(IGrace.COUNTRY)}-list`),
            phone_hidden_input            = address_phone_container?.closest('.form-group').find(`input[name="${action}_${IGrace.ADDRESS}_${IGrace.PHONE}"]`);

        const default_alpha2Code = address_phone_container?.data(`initial_${IGrace.COUNTRY}`);

        address_phone_container?.data('alpha2Code', default_alpha2Code);

        // Populate countries rows
        let countries_items = '';

        $.each((countries), (_, country) => {
            countries_items += `
                <li data-alpha2Code="${country.alpha2Code}" data-flag="${country.flag}" data-calling_code="${country.calling_code}">
                    <img src="${country.flag}" alt="${country.name}" class="dropdown-flag">
                    <span class="dropdown-name">${country.name}</span>
                    <span class="dropdown-calling-code">${country.calling_code}</span>
                </li>`;

            if(country.alpha2Code === default_alpha2Code) {
                selected_country_flag.attr('src', country.flag);
                selected_country_calling_code.text(country.calling_code);
                phone_input.attr('placeholder', Common.generateDynamicPlaceholder(country.calling_code));
            }
        });

        countries_list.html(countries_items);

        // Click Actions
        $(document).on(IGrace.CLICK, (e) => {
            const
                target               = $(e.target),
                country_flag         = countries_list.find('.dropdown-flag'),
                country_name         = countries_list.find('.dropdown-name'),
                country_calling_code = countries_list.find('.dropdown-calling-code');

            // Dropdown open/close
            if (target.is(country_selector) || target.is(selected_country_flag) || target.is(selected_country_calling_code) || target.is(selected_country_chevron)) {
                e.stopPropagation();

                dropdown_container.toggleClass('show');

                if (dropdown_container.hasClass('show')) {
                    search_input.val('')
                        .trigger(IGrace.KEYUP)
                        .focus();
                }
            }

            // Dropdown filter search
            if (target.is(search_input)) {
                e.stopPropagation();
            }

            // Country item selected
            if (target.is(country_flag) || target.is(country_name) || target.is(country_calling_code)) {
                const
                    alpha2Code   = target.parent().data('alpha2Code'),
                    flag_url     = target.parent().data('flag'),
                    calling_code = target.parent().data('calling_code');

                address_phone_container?.data('alpha2Code', alpha2Code);
                selected_country_flag.attr('src', flag_url);
                selected_country_calling_code.text(calling_code);
                dropdown_container.removeClass('show');

                phone_input.attr('placeholder', Common.generateDynamicPlaceholder(calling_code));
                phone_input.trigger(IGrace.INPUT).focus();
            }
        });

        // Keyup Action (Dropdown filter search)
        search_input.on(IGrace.KEYUP, function () {
            const countries_items = countries_list.find('li');

            let query = $(this).val()
                .toLowerCase()
                .trim();

            $.each((countries_items), (_, countryItem) => {
                let
                    name = $(countryItem).find('.dropdown-name')
                        .text()
                        .toLowerCase(),

                    calling_code = $(countryItem).find('.dropdown-calling-code')
                        .text()
                        .toLowerCase();

                $(countryItem).toggle(name.includes(query) || calling_code.includes(query));
            });
        });

        // Input Action (Format As-You-Type & Save Clean Value to Hidden Input)
        phone_input.on(IGrace.INPUT, function () {
            const target = $(this);

            let raw_digits = target.val().replace(/\D/g, '');

            const
                current_calling_code = selected_country_calling_code.text(),
                current_alpha2Code   = address_phone_container?.data('alpha2Code');

            if (raw_digits === '') {
                target.val('');
                phone_hidden_input.val('');
                return;
            }

            const phone_actions = {
                true: () => {
                    try {
                        let
                            combined_string         = current_calling_code + raw_digits,
                            global_formatted_number = new libphonenumber.AsYouType().input(combined_string),
                            localized_result        = Common.stripCountryPrefix(global_formatted_number, current_calling_code);

                        target.val(localized_result);

                        // Parse & set validation payload inside the hidden input field
                        const parsed_number = libphonenumber.parsePhoneNumberFromString(combined_string, current_alpha2Code);

                        const number_actions = {
                            true:  () => phone_hidden_input.val(parsed_number.number), // Outputs strict standard format (+201011836243)
                            false: () => phone_hidden_input.val(combined_string), // Work-in-progress input value fallback
                        }

                        number_actions[parsed_number && parsed_number.isValid()]();
                    }
                    catch (e) {
                        phone_hidden_input.val(current_calling_code + raw_digits);
                    }
                },
                false: () => {
                    target.val(raw_digits);
                    phone_hidden_input.val(current_calling_code + raw_digits);
                }
            }

            phone_actions[typeof libphonenumber !== 'undefined']();
        });
    },


    /**
     * Filter the form data to exclude the fields that have a comma in their value (except for file inputs)
     * or the password and review fields
     * to avoid any issues with the validation on the server side.
     *
     * @param target
     * @return {*}
     */
    filteredFormData: (target) => {
        return [...new FormData($(target)[0])]
            .reduce((formData, [attribute, value]) => {
                const
                    is_file_input       = $(target).find(`input[name="${attribute}"]:file`).length && value instanceof File,
                    attribute_has_words =
                        attribute.includes(`${IGrace.PASSWORD}`) ||
                        attribute.includes(`${IGrace.REVIEW}`) ||
                        attribute.includes('description');

                if (attribute_has_words || is_file_input || !value.toString().includes(',')) {
                    formData.append(attribute, value);
                }

                return formData;
            }, new FormData());
    },


    /**
     * Add some classes and styles on the image elements to make them look better,
     * and set the loading attribute to lazy for better performance.
     *
     * @return {*}
     */
    imageConfig: () => $.each(($('img:not(.loading-spinner)')), (_, image) =>
        $(image).addClass('img-fluid h-100')
            .css('mix-blend-mode', 'multiply')
            .attr('loading', 'lazy')
    ),


    /**
     * Truncate the text in the elements with the "truncate" class to a specified number of characters.
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
                target           = $(this),
                truncate_element = target.closest('.truncate'),
                truncate_text    = truncate_element.find('.truncate-text'),
                full_text        = truncate_element.data('full_text'),
                short_text       = full_text.substring(0, show_char_num);

            truncate_text.slideUp(180).fadeOut(180, () => {
                target.hasClass('short')
                    ? truncate_element.html(buildTruncateContent(full_text, 'full'))
                    : truncate_element.html(buildTruncateContent(short_text, 'short'));

                truncate_text.slideDown(180).fadeIn(180);
            });
        });
    },


    /**
     * Arrange the table rows after any action that affects the order of the rows (add, delete, restore, pagination)
     * by updating the row numbers accordingly.
     *
     * @param startIndex
     * @return {*}
     */
    arrangeTableRows: (startIndex = 0) =>
        $.each(($(".table tbody tr")), (key, row) => $(row).find(`.${IGrace.ROW}-num > p`).html(startIndex + (++key))),


    /**
     * Update the table rows after any action that affects the order of the rows
     * (add, update, delete, restore, pagination).
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
     * Display a warning message before leaving the page if there are unsaved changes in the form (form is dirty).
     *
     * @return {*}
     */
    warnBeforeLeaving: () =>
        $(window).on("beforeunload", function (e) {
            if (isFormDirty) {
                e.preventDefault();
                e.returnValue = "";
            }
        }),


    /**
     * Show the scroll to top button when scrolling down the page,
     * and handle its click event to scroll smoothly to the top of the page.
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
     * Set up the default settings for all ajax requests, including the CSRF token header for security.
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
     * Fetch the list of countries from the REST Countries API.
     *
     * @return {void}
     */
    ajaxGetCountries: () => {
        const country_element = $(`.${IGrace.ADDRESS}-${IGrace.COUNTRY}`);

        $.getJSON('/api/fetch-countries')
            .done((countries) => {
                // For Address Country
                const countries_options = countries.map((country) => country.name)
                    .sort((opt1, opt2) => opt1.localeCompare(opt2))
                    .map((countryName) => `<option value="${countryName}">${countryName}</option>`)
                    .join('');

                country_element?.append(countries_options);

                // For Address Phone Country Selector
                Common.countriesData = countries.map((country) => {
                        return {
                            alpha2Code:   country.alpha2Code,
                            flag:         country.flag,
                            name:         country.name,
                            calling_code: `+${country.callingCodes[0]}`,
                        };
                    })
                    .sort((item1, item2) => item1.name.localeCompare(item2.name));

                Common.formPhoneConfig(IGrace.ADD,    Common.countriesData);
                Common.formPhoneConfig(IGrace.UPDATE, Common.countriesData);
            })
            .fail(() => console.error("Failed to fetch countries!"));
    },


    /**
     * Extract the phone number data from the response and populate the phone input field in the address form.
     *
     * @param responseData
     * @return {void}
     */
    getPhoneData: (responseData) => {
        if(responseData.phone && typeof libphonenumber !== 'undefined') {
            const phone = responseData.phone;

            try {
                const parsed = libphonenumber.parsePhoneNumber(phone);

                if (parsed) {
                    const
                        country_calling_code = `+${parsed.countryCallingCode}`,
                        target_country = Common.countriesData.find((country) =>
                            country.calling_code === country_calling_code && country.alpha2Code === parsed.country
                        );

                    if (target_country) {
                        $(`#${IGrace.UPDATE}_${IGrace.ADDRESS}_${IGrace.PHONE}_container`).data('alpha2Code', target_country.alpha2Code);
                        $('.selected-flag').attr('src', target_country.flag);
                        $('.selected-code').text(target_country.calling_code);
                        $(`#${IGrace.UPDATE}_${IGrace.ADDRESS}_${IGrace.PHONE}`).val(parsed.nationalNumber).trigger(IGrace.INPUT);
                    }
                }
            }
            catch (e) {
                $(`#${IGrace.UPDATE}_${IGrace.ADDRESS}_${IGrace.PHONE}`).val(phone);
            }
        }
    },


    /**
     * Extract the error messages from the JSON response of an ajax request error.
     *
     * @param error
     * @param isMessage
     * @return {*}
     */
    responseJsonError: (error, isMessage = false) => error.responseJSON?.[isMessage ? 'message' : 'errors'],


    /**
     * Display the error messages returned from the server after an ajax request in the appropriate place in the form.
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
                        true:  () => $('#count_down').text(--seconds),
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
     * Display a confirmation message before performing a delete or remove action.
     *
     * @param message
     * @return {*}
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
     * Display the success message returned from the server after an ajax request in a sweet alert.
     *
     * @param status
     * @param message
     * @param extra
     * @return {number|*}
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
     * Display a cancellation message
     * when the user cancels the delete or remove action in the confirmation message.
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
     * Display the error message returned from the server after an ajax request in a sweet alert.
     *
     * @param error
     * @return {*}
     */
    swalResponseJsonErrorMessage: (error) => Common.somethingWentWrongError(Common.responseJsonError(error, true)),


    /**
     * Display a generic error message in a sweet alert when something goes wrong with an ajax request.
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
     * Remove the error messages from the form when the edit modal is hidden
     * to avoid showing old error messages when opening the modal again for another item.
     *
     * @param role
     * @return {*}
     */
    removeErrorsWhenEditModelHides: (role) =>
        $(document).on('hidden.bs.modal', `.${role}-${IGrace.EDIT}-modal`, function (e) {
            e.preventDefault();

            $(this).find(IGrace.ERROR_ELEMENT(IGrace.UPDATE)).empty();
        }),


    /**
     * Handle the pagination response by updating the pagination container with the new data.
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
     * Success response for search/filter by updating the search results table with the new data.
     *
     * @param data
     * @return {void}
     */
    searchFilterSuccessResponse: (data) => {
        Common.paginationResponse($('.search-table'), data);

        $(`.carousel-item.${IGrace.ADMIN}-${IGrace.PRODUCT}-imgs:first-child`).addClass('active');
    },


    /**
     * Error response for search/filter by showing a "No Results Found" message with an image in the search results table.
     *
     * @param imageSrc
     * @return {*}
     */
    searchFilterErrorResponse: (imageSrc) =>
        $('.search-table').add(`.${IGrace.PLURALIZE(IGrace.PRODUCT)}-content`).html(
            `<div class="d-flex justify-content-center mt-5 mx-auto">
                <img src=${imageSrc} alt="No Results Found" class="img-fluid h-100" style="width:300px!important">
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
                    country.find('option').removeAttr('selected')
                        .filter((_, addressCountry) => addressCountry.value === address[IGrace.COUNTRY])
                        .attr('selected', true);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.CITY}`).val(address[IGrace.CITY]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.STATE}`).val(address[IGrace.STATE]);
                    Common.getPhoneData(address);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.ADDRESS)}_${IGrace.POSTAL_CODE}`).val(address[IGrace.POSTAL_CODE]);

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
                        review                         = data[IGrace.REVIEW],
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
     * Get the ajax settings for deleting one or more items, with an optional force delete request parameter.
     *
     * @param options
     * @return {{url: string, method: string, success: function(): void, error: function(*): void}}
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
     * Display a confirmation message with the error message returned from the server
     * after a failed delete request due to related items.
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
     * Handle the errors returned from the server after a failed delete request by showing a confirmation message.
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
     * Search/Filter ajax request
     * by sending the search value to the server
     * and updating the search results table with the response data.
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

            if (target.is(`#${IGrace.USER}_${IGrace.SEARCH}_${IGrace.PLURALIZE(IGrace.PRODUCT)}`)) {
                return;
            }

            $.get(`${route}${Common.routeParamsSeperator(route)}search_value=${search_value}`)
                .done((data) => Common.searchFilterSuccessResponse(data))
                .fail((err) =>
                    err.status === 404
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

                    clear_search_button?.css({'opacity': '0', 'visibility': 'hidden'});

                    $(IGrace.ERROR_ELEMENT(IGrace.FILTER)).empty();
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- NOTIFICATIONS REQUEST ---------------------------------- */
    /**
     * Set up the EventSource connection
     * to receive real-time notifications from the server using Server-Sent Events (SSE).
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

                notification_sound?.trigger('play');
            }
            catch (error) {
                console.error('Failed to parse SSE message: ', error);
            }
        };

        source.onerror = () => source.close();
    },


    /* ---------------------------------- PAGINATION REQUEST ---------------------------------- */
    /**
     * Handle the pagination links click event by sending an ajax request to the server.
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
                return User.ajaxFilterProducts({ route: url });
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
