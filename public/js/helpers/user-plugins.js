'use strict'

import { IGrace } from "./IGrace.js";


/**
 * Handles the "carousel slider" functionality configurations.
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
 * Handles the chosen filter products multiple items with a hidden input.
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

        target.is(':checked') && !selected_values.includes(target.val())
            ? selected_values.push(target.val())
            : selected_values.splice($.inArray(target.val(), selected_values), 1);

        selected_values = selected_values.filter(Boolean).join(','); // "filter(Boolean)" removes empty values

        filter_collection_hidden_input.val(selected_values);
    });
};


/**
 * Handles the syncorization of price inputs with range sliders.
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
 * Handles the ratings update dynamically.
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
 * Handles the displaying of a loading spinner in an element.
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
