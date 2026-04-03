'use strict';

import { IGrace, Common, User } from "./helpers/user-helpers.js";
import "./helpers/common-plugins.js";
import "./helpers/user-plugins.js";


$(document).ready(() => {
    /* ========================================= Global Variables ========================================= */
    const products_main_view = $(`.${IGrace.PLURALIZE(IGrace.PRODUCT)}-view-sort`);


    /* ========================================= Functions & Events ========================================= */

    // Load the preloader
    $(window).on('load', () => $("#preloader").delay(500).fadeOut("slow"));

    // Show the offers sales one after the other every 3 seconds
    $.each(($('.nav-offer .offer-sale')), (_, offerSale) => {
        const offers      = $(offerSale).find('p');
        let current_offer = 0;

        setInterval(
            () =>
                offers.eq(current_offer)
                    .toggleClass('active', false)
                    .end()
                    .eq(current_offer = (current_offer + 1) % offers.length)
                    .toggleClass('active', true)
            , 3000
        );
    });


    /* ---------=========== Carousel Config ============--------- */
    let
        customers_reviews_display_items_count = 1,
        partners_display_items_count          = 4,
        related_products_display_items_count  = 4;

    // Adjust the display items count based on the screen size
    if ($(window).width() >= 320 && $(window).width() < 768) {
        partners_display_items_count         = 2;
        related_products_display_items_count = 2;
    }
    if ($(window).width() >= 768 && $(window).width() < 992) {
        customers_reviews_display_items_count = 2;
        partners_display_items_count          = 3;
    }
    if ($(window).width() >= 768 && $(window).width() < 1200) {
        related_products_display_items_count = 3;
    }

    $(window).width() < 992
        ? $('.products-filter-form').children().remove()
        : $('.products-filter-form-menu').children().remove();

    $(window).width() < 1200
        ? $('.header-search').children().remove()
        : $('.nav-search').children().remove();

    // Configure the carousels
    $('.home-carousel').carouselSlider({
        displayItemsCount: 1,
        nav:               false,
        dots:              true,
    });

    $(`.customers-${IGrace.PLURALIZE(IGrace.REVIEW)}-carousel`).carouselSlider({
        displayItemsCount: customers_reviews_display_items_count,
        nav:               false,
        dots:              true,
        autoplay:          false,
    });

    $('.partners-carousel').carouselSlider({
        displayItemsCount: partners_display_items_count,
    });

    $(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.PLURALIZE(IGrace.THUMB_IMAGE()))}-carousel`).carouselSlider({
        displayItemsCount: 4,
        autoplay:          false,
    });

    $(`.related-${IGrace.PLURALIZE(IGrace.PRODUCT)}-carousel`).carouselSlider({
        displayItemsCount: related_products_display_items_count,
    });

    /* ---------=========== End Carousel Config ============--------- */


    // Change the products main view
    if (products_main_view.length) {
        User.changeProductsView();
    }

    // Adjust the height of the more details description
    $('.more-details-desc').first().css({
        'margin-top':  'var(--ten-pixels)',
        'line-height': 'var(--twenty-five-pixels)',
    });

    // Show the main image according to the selected thumb image
    const product_thumb_images = $(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.THUMB_IMAGE())}`);
    $.each(product_thumb_images, (_, productThumbImage) => {
        $(productThumbImage).on(IGrace.CLICK, () => {
            $(`.${IGrace.CLASS(IGrace.THUMB_IMAGE())}-border`).removeClass(`${IGrace.CLASS(IGrace.THUMB_IMAGE())}-border`);
            $(productThumbImage).addClass(`${IGrace.CLASS(IGrace.THUMB_IMAGE())}-border`);
            $(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.MAIN_IMAGE())} img`).attr('src', $(productThumbImage).children('img').attr('src'));
        });
    });

    /**
     * Set the value of the address input with the value of the address radio automatically,
     * when the page is reloaded based on the selected address in the session storage
     */
    const selected_address = sessionStorage.getItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`);
    if (selected_address) {
        $(`#${IGrace.ORDER}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`).val(`${selected_address},`);
    }


    /* ---------=========== Change (Input) Action ============--------- */
    $(document).on(IGrace.INPUT, (e) => {
        const
            target       = $(e.target),
            price_inputs = '.price-input input[type="number"]',
            price_ranges = '.price-range input[type="range"]';

        // Submit the "filter products form" when the "sort select" is changed
        if (target.is(`select#${IGrace.FILTER_PRODUCTS_SORT()}`)) {
            $(`form#${IGrace.FILTER_PRODUCTS_FORM()}`).find(`input#${IGrace.FILTER_PRODUCTS_SORT()}`)
                .val(target.val())
                .end()
                .submit();
        }

        // Syncs price inputs with range sliders
        if (target.is(price_inputs) ||target.is(price_ranges)) {
            const
                price_range_container = target.closest('.filter-content'),
                inputs                = price_range_container.find(price_inputs),
                ranges                = price_range_container.find(price_ranges);

            if (target.is(price_inputs)) {
                inputs.handlePriceRangeFilter({ selectors: ranges });
            }

            if (target.is(price_ranges)) {
                ranges.handlePriceRangeFilter({ selectors: inputs });
            }
        }

        /**
         * When adding a new order,
         * set the value of the address input with the value of the address radio automatically
         */
        if (target.is(`input[name="${IGrace.ADD_COLLECTION(IGrace.ORDER)}_payment_method"]:radio`)) {
            target.closest(`#${IGrace.ADD_COLLECTION(IGrace.ORDER)}_form`)
                .find(`#${IGrace.ORDER}_payment_method`)
                .val(`${target.val()},`);
        }

        /**
         * When adding a new review,
         * set the value of the rating input with the value of the rating radio automatically
         */
        if (target.is(`input[name="${IGrace.ADD_COLLECTION(IGrace.REVIEW)}_${IGrace.RATING}"]:radio`)) {
            target.parent()
                .next()
                .val(target.val());
        }
    });

    /* ---------=========== End Change (Input) Action ============--------- */


    /* ---------=========== Mutation Observer Action ===========--------- */
    const observer = new MutationObserver((mutations) => {
        $.each((mutations), (_, mutation) => {
            if (mutation.type === 'childList' || mutation.type === 'subtree' || mutation.type === 'attributes') {
                // Change the products main view
                if (products_main_view.length) {
                    User.changeProductsView();

                    const is_single = products_main_view.attr('data-grid-main-view') === '1';

                    $(`.${IGrace.PLURALIZE(IGrace.PRODUCT)}-content .${IGrace.PRODUCT}-item`)
                        .find('.grid-view-single-item')
                        .toggleClass('d-md-flex d-block', is_single).toggleClass('d-none', !is_single)
                        .end()
                        .find('.grid-view-multiple-items')
                        .toggleClass('d-block', !is_single).toggleClass('d-none', is_single);
                }

                // Show or hide the number of selected items when selecting multiple items
                $(mutation.target).showHideMultiSelectedItems();
            }
        });
    });

    // Observe the products main view data attribute
    if (products_main_view.length) {
        observer.observe(products_main_view[0], {
            attributes:      true,
            attributeFilter: ['data-grid-main-view'],
        });
    }

    // Observe the body for any changes
    observer.observe(document.body, {
        childList: true,
        subtree:   true,
    });

    /* ---------=========== End Mutation Observer Action ===========--------- */


    /* ---------=========== Click Action ============--------- */
    let
        add_multi_selected_product_sizes_values            = [],
        add_multi_selected_product_sizes_quick_view_values = [],
        filter_products_categories_values                  = [],
        filter_products_subcategories_values               = [],
        filter_products_sizes_values                       = [];

    $(document).on(IGrace.CLICK, (e) => {
        const target = $(e.target);

        const
            nav_menu                     = 'nav-menu',
            nav_submenu                  = 'nav-submenu',
            footer_menu                  = 'footer-menu',
            nav_menu_toggler             = $(`.${nav_menu}-toggler > i`),
            nav_menu_close               = `.${nav_menu}-close`,
            nav_menu_overlay             = `.${nav_menu}-overlay`,
            nav_menu_list_header         = `.${nav_menu}-list-header`,
            nav_menu_list_content        = `.${nav_menu}-list-content`,
            nav_menu_list_icon           = `.${nav_menu}-list-icon`,
            nav_menu_list_title          = `.${nav_menu}-list-title`,
            nav_submenu_list_header      = `.${nav_submenu}-list-header`,
            nav_submenu_list_content     = `.${nav_submenu}-list-content`,
            nav_submenu_item_title       = `.${nav_submenu}-item-title`,
            nav_submenu_item_badge       = `.${nav_submenu}-item-badge`,
            nav_submenu_item_rotate_icon = `.${nav_submenu}-item-rotate-icon`,
            footer_menu_list_header      = `.${footer_menu}-list-header`,
            footer_menu_item_title       = `.${footer_menu}-item-title`,
            footer_menu_item_rotate_icon = `.${footer_menu}-item-rotate-icon`;

        const
            quantity_input           = target.parent().find(`.${IGrace.QUANTITY}-input`),
            current_quantity_value   = +quantity_input.val();

        // Active the responsive nav menu and overlay
        if (target.is(nav_menu_toggler)) {
            target.parent()
                .next()
                .add($(nav_menu_overlay))
                .addClass('active');
        }

        // Hide the responsive nav menu and close any list
        if (target.is(`${nav_menu_overlay}, ${nav_menu_close}`)) {
            $(`.${nav_menu}`).add($(`.${nav_menu} *`))
                .add($(nav_menu_overlay))
                .removeClass('active');

            $(nav_menu_list_content).add(nav_submenu_list_content)
                .removeClass('show');

            $(nav_submenu_item_rotate_icon).removeClass('rotate-180');
        }

        // Show or hide the nav menu list
        if (target.is(`${nav_menu_list_header}, ${nav_menu_list_icon}, ${nav_menu_list_title}`)) {
            const nav_menu_list_header_parent = target.is(nav_menu_list_header)
                ? target
                : target.parents(nav_menu_list_header).first();

            nav_menu_list_header_parent.toggleClass('active');
        }

        // Show or hide the nav submenu list
        if (target.is(`${nav_submenu_list_header}, ${nav_submenu_item_title}, ${nav_submenu_item_badge}, ${nav_submenu_item_rotate_icon}`)) {
            const nav_menu_list_item = target.parents(`.${nav_menu}-list-item`).toggleClass('active');
            nav_menu_list_item.find(nav_submenu_item_rotate_icon).toggleClass('rotate-180');
        }

        // Show or hide the footer menu list
        if (target.is(`${footer_menu_list_header}, ${footer_menu_item_title}, ${footer_menu_item_rotate_icon}`)) {
            const footer_item = target.parents('.footer-item').first();
            footer_item.find(footer_menu_item_rotate_icon).toggleClass('rotate-180');
        }

        // Change the products grid view
        if (target.hasClass('grid')) {
            products_main_view.attr('data-grid-main-view', target.data('grid-view'));
        }

        // Increment or decrement the quantity
        if (target.hasClass('decrement-btn')) {
            quantity_input.val(Math.max(current_quantity_value - 1, 1));
        }
        if (target.hasClass('increment-btn')) {
            quantity_input.val(current_quantity_value + 1);
        }

        // Clear the products filter
        if (target.hasClass(`clear-${IGrace.FILTER}`)) {
            target.closest('form')[0].reset();
            $('input[type="hidden"]').removeAttr('value');
        }

        // Submit the wishlist's form
        if (target.is(`.${IGrace.CLASS(IGrace.ADD_REMOVE_WISHLIST())}-btn, .fa-heart`)) {
            e.preventDefault();

            target.closest('form')
                .next()
                .submit();
        }

        // Remove the product from the wishlist
        if (target.is(`.${IGrace.WISHLIST} .${IGrace.PRODUCT}-remove`)) {
            $(`#${IGrace.WISHLIST}_${IGrace.PRODUCT}_remove_form`).attr('action', target.data('route')).submit();
        }

        // Remove the product from the cart
        if (target.is(`.${IGrace.CART} .${IGrace.PRODUCT}-remove`)) {
            const cart_product_remove_form = $(`#${IGrace.CART}_${IGrace.PRODUCT}_remove_form`);
            cart_product_remove_form.attr('action', target.data('route')).submit();
            let cartProductInfo = (dbColumn) => cart_product_remove_form.find(`input[name="${IGrace.DELETE_COLLECTION(IGrace.CART)}_${dbColumn}"]`).val(target.data(dbColumn));
            cartProductInfo(IGrace.PRODUCT_SIZE());
            cartProductInfo(IGrace.PRODUCT_QUANTITY());
        }

        // Show or hide the review form
        if (target.hasClass(`write-${IGrace.REVIEW}-btn`)) {
            $(`#${IGrace.ADD_COLLECTION(IGrace.REVIEW)}_form`).fadeToggle('slow').toggleClass('show-form');
        }

        // Handle the "Select All" checkbox and the "hidden input" value for the selected items in the filter-multi-select
        target.filterProductsMultiItems({
            multiSelectedValuesList: filter_products_categories_values,
            relation:                IGrace.PLURALIZE(IGrace.CATEGORY),
        });

        target.filterProductsMultiItems({
            multiSelectedValuesList: filter_products_subcategories_values,
            relation:                IGrace.PLURALIZE(IGrace.SUBCATEGORY),
        });

        target.filterProductsMultiItems({
            multiSelectedValuesList: filter_products_sizes_values,
            relation:                IGrace.PLURALIZE(IGrace.SIZE),
        });

        target.selectAllMultiItems({
            actionCollection: IGrace.ADD_COLLECTION(IGrace.CART),
            multiSelectedValuesList: add_multi_selected_product_sizes_quick_view_values,
            relation:                IGrace.PRODUCT_SIZE_QUICK_VIEW(),
        });

        target.selectAllMultiItems({
            actionCollection: IGrace.ADD_COLLECTION(IGrace.CART),
            multiSelectedValuesList: add_multi_selected_product_sizes_values,
            relation:                IGrace.PRODUCT_SIZE(),
        });

        // Check/Uncheck the (check_all) checkbox and checkboxes in the table
        target.checkRows();
    });

    // Count the number of characters of the review body
    $(`.${IGrace.REVIEW}-${IGrace.CLASS(IGrace.BODY_TEXT)}`).charsCounter();

    // Set up the form multiselect settings
    const
        add_cart_product_sizes            = $(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_SIZE()}`),
        add_cart_product_sizes_quick_view = $(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_SIZE_QUICK_VIEW()}`);

    if (add_cart_product_sizes.length) {
        add_cart_product_sizes.formMultiSelectConfig();
    }

    if (add_cart_product_sizes_quick_view.length) {
        add_cart_product_sizes_quick_view.formMultiSelectConfig();
    }

    // Set up the form select settings
    Common.formSelectConfig();

    // Arrange the table rows
    Common.arrangeTableRows();

    // Add some classes, styles, and attributes on each image
    Common.imageConfig();

    // Get the countries
    Common.ajaxGetCountries();

    // Truncate the text that has more than 70 characters
    Common.truncateText();

    // Configure the checkout addresses
    User.checkoutAddressesConfig();

    // Scroll to top action
    Common.scrollToTop();

    // Set up the tooltip
    $('[data-tooltip="tooltip"]').tooltip();
});
