'use strict'

import { IGrace } from "./IGrace.js";


/**
 * Handles check all functionality configurations for table rows.
 *
 * @return {*}
 */
$.fn.checkRows = function() {
    return this.each(function() {
        const
            target = $(this),
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

        // Check/Uncheck the target (individual) checkbox in the table and (check_all) checkbox
        if (target.hasClass(`custom-check-${IGrace.ROW}`)) {
            target.prev().prop('checked', !target.prev().is(':checked'));

            const checked_count = check_row.filter(':checked').length;

            check_all.prop({
                'checked':       checked_count === check_row.length,
                'indeterminate': checked_count > 0 && checked_count < check_row.length,
            });
        }
    });
};

/**
 * Handle the chosen filter products multiple items with hidden input.
 *
 * @param options
 * @return {*}
 */
$.fn.filterProductsMultiItems = function(options) {
    const settings = $.extend({
        multiSelectedValuesList: [],
        relation:                null,
    }, options);

    return this.each(function() {
        const target                         = $(this);
        const filter_collection_hidden_input = target.parents('.filter-content').next();

        if (!target.is(`input[name="${IGrace.FILTER_PRODUCTS()}_${settings.relation}[]"]`)) return;

        let selected_values = settings.multiSelectedValuesList;

        target.is(':checked') && !selected_values.includes(target.val())
            ? selected_values.push(target.val())
            : selected_values.splice($.inArray(target.val(), selected_values), 1);

        selected_values = selected_values.filter(Boolean).join(','); // filter(Boolean) removes empty values

        filter_collection_hidden_input.val(selected_values);
    });
};


/**
 * Handle the select-all checkbox functionality for multiple items with hidden input.
 *
 * @param options
 * @return {*}
 */
$.fn.selectAllMultiItems = function(options) {
    const settings = $.extend({
        actionCollection:        null,
        relation:                null,
        multiSelectedValuesList: [],
    }, options);

    return this.each(function() {
        const target = $(this);

        if (!target.is(`input[name="${settings.actionCollection}_${settings.relation}[]"]`)) return;

        const
            is_checked = target.is(':checked'),
            is_select_all = target.next().html().includes('All'),
            all_items = target.parents('.items').find('input[type="checkbox"]'),
            select_all_checkbox = all_items.first(),
            related_collection_hidden_input = target.parents('.filter-multi-select').next();

        let selected_values = settings.multiSelectedValuesList;

        const check_actions = {
            true: () => {
                const select_actions = {
                    true: () => {
                        selected_values.length = 0;
                        select_all_checkbox.val('');
                        related_collection_hidden_input.val('');

                        $.each((all_items), (_, selected_item) => selected_values.push($(selected_item).val() || ''));

                        select_all_checkbox.next().html('Unselect All');
                    },
                    false: () => selected_values.push(target.val()),
                };

                select_actions[is_select_all]();
            },
            false: () => {
                is_select_all
                    ? selected_values.length = 0
                    : selected_values.splice($.inArray(target.val(), selected_values), 1);

                select_all_checkbox.next().html('Select All');
            },
        };

        check_actions[is_checked]();

        selected_values = selected_values.filter(Boolean).join(','); // filter(Boolean) removes empty values

        select_all_checkbox.val(selected_values);
        related_collection_hidden_input.val(selected_values);
    });
};


/**
 * Configure the carousel slider.
 *
 * @param options
 * @return {*}
 */
$.fn.carouselSlider = function(options) {
    const settings = $.extend({
        displayItemsCount:  3,
        nav:                true,
        dots:               false,
        autoplay:           true,
        autoplayTimeout:    4000,
        autoplayHoverPause: true,
        margin:             10,
        rewind:             true,
        navText:            ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>']
    }, options);

    return this.each(function() {
        const element = $(this);

        element.owlCarousel({
            items:              settings.displayItemsCount,
            rewind:             settings.rewind,
            margin:             settings.margin,
            nav:                settings.nav,
            navText:            settings.navText,
            dots:               settings.dots,
            autoplay:           settings.autoplay,
            autoplayTimeout:    settings.autoplayTimeout,
            autoplayHoverPause: settings.autoplayHoverPause
        });

        element.find('.owl-nav button').attr('type', 'button');
        element.find('.owl-nav .owl-prev').attr('title', 'Go to Previous');
        element.find('.owl-nav .owl-next').attr('title', 'Go to Next');
        element.find('.owl-dot').attr({
            type:  'button',
            title: 'Go to Slider'
        });
    });
};


/**
 * Update ratings dynamically.
 *
 * @param rating
 * @return {*}
 */
$.fn.starRating = function(rating) {
    return this.each(function() {
        const container   = $(this).empty();
        const filled_star = '<span class="position-relative fs-4 star-fill" aria-label="Filled Star">★</span>';
        const empty_star  = '<span class="position-relative fs-4 star-empty" aria-label="Empty Star">☆</span>';

        container.html(filled_star.repeat(rating) + empty_star.repeat(5 - rating));
    });
};
