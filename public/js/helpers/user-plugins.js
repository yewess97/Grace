'use strict'

import { IGrace } from "./IGrace.js";


/**
 * Handles the initialization of the Owl Carousel plugin for a carousel slider element,
 * with custom configuration and accessibility adjustments.
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
        const target = $(this);

        target.owlCarousel({
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

        target.find('.owl-nav button').attr('type', 'button');
        target.find('.owl-nav .owl-prev').attr('title', 'Go to Previous');
        target.find('.owl-nav .owl-next').attr('title', 'Go to Next');
        target.find('.owl-dot').attr({
            type:  'button',
            title: 'Go to Slider'
        });
    });
};


/**
 * Handles the updating of a hidden input field with the selected values from multiple checkboxes in a filter form,
 * allowing for multiple selections and dynamic updates as checkboxes are checked or unchecked.
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

        if (!target.is(`input[name="${IGrace.FILTER_PRODUCTS()}_${settings.relation}[]"]`)) {
            return;
        }

        let selected_values = settings.multiSelectedValuesList;

        const check_actions = {
            true: ()  => selected_values.push(target.val()),
            false: () => {
                const index = $.inArray(target.val(), selected_values);

                if (index !== -1) {
                    selected_values.splice(index, 1);
                }
            },
        };

        check_actions[target.is(':checked') && !selected_values.includes(target.val())]();

        selected_values = selected_values.filter(Boolean).join(','); // "filter(Boolean)" removes empty values

        filter_collection_hidden_input.val(selected_values);
    });
};


/**
 * Handles the updating of the minimum and maximum price values in a price range filter form,
 * ensuring that the minimum value does not exceed the maximum value and vice versa,
 * and updating the corresponding input fields accordingly.
 *
 * @param options
 * @return {*}
 */
$.fn.handlePriceRangeFilter = function (options) {
    const settings = $.extend({
        selectors: null,
    }, options);

    let [min_value, max_value] = this.map((_, element) => {
        let value = parseFloat($(element).val());

        return isNaN(value)
            ? 0
            : value;
    }).get();

    if (min_value > max_value) {
        [min_value, max_value] = [max_value, min_value];
    }

    settings.selectors
        .eq(0)
        .val(min_value)
        .end()
        .eq(1)
        .val(max_value);

    return this;
};


/**
 * Handles the display of star ratings for products,
 * allowing for a dynamic number of filled and empty stars based on the provided rating value.
 *
 * @param options
 * @return {*}
 */
$.fn.starRating = function(options) {
    const settings = $.extend({
        rating: null,
    }, options);

    return this.each(function() {
        const
            target      = $(this),
            filled_star = '<span class="position-relative fs-4 star-fill" aria-label="Filled Star">★</span>',
            empty_star  = '<span class="position-relative fs-4 star-empty" aria-label="Empty Star">☆</span>';

        target.empty().html(filled_star.repeat(settings.rating) + empty_star.repeat(5 - settings.rating));
    });
};


/**
 * Handles the display of a loading spinner on a specified element,
 * typically used to indicate that a process is ongoing,
 * and optionally disables the element to prevent further interactions while the process is active.
 *
 * @param options
 * @return {*}
 */
$.fn.loadingSpinner = function(options) {
    const settings = $.extend({
        element:    null,
        isDisabled: false,
    }, options);

    return this.each(function() {
        const target = $(this);

        if (settings.isDisabled) {
            settings.element.prop('disabled', true);
        }

        settings.element.prepend($('<img>', {
            src:    target.data('loading_spinner'),
            alt:    'Loading',
            class:  'img-fluid loading-spinner',
            width:  30,
            height: 30,
        }));
    });
};
