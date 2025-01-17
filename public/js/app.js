'use strict';

import { IGrace, Common, User } from "./helpers/user-helpers.js";


$(document).ready(() => {

    // Load the preloader
    $(window).on('load', () => $("#preloader").delay(500).fadeOut("slow"));

    const offers_sales = $('.nav-offer .offer-sale');

    $.each((offers_sales), (_, offer_sale) => {
        const offers = $(offer_sale).find('p');
        let current_offer = 0;

        setInterval(() => {
            offers.eq(current_offer).removeClass('active');
            current_offer = (current_offer + 1) % offers.length;
            offers.eq(current_offer).addClass('active');
        }, 3000);
    });

    Common.imageConfig();

    let
        customers_reviews_display = 1,
        partners_display = 4,
        related_products_display = 4;

    if ($(window).width() >= 320 && $(window).width() < 768) {
        partners_display = 2;
        related_products_display = 2;
    }

    if ($(window).width() >= 768 && $(window).width() < 992) {
        customers_reviews_display = 2;
        partners_display = 3;
    }

    if ($(window).width() >= 768 && $(window).width() < 1200) {
        related_products_display = 3;
    }

    $(window).width() < 992
        ? $('.products-filter-form').children().remove()
        : $('.products-filter-form-menu').children().remove();

    $(window).width() < 1200
        ? $('.header-search').children().remove()
        : $('.nav-search').children().remove();

    User.carouselSlider('.home-carousel', 1, false, true,  true);
    User.carouselSlider(`.customers-${IGrace.PLURALIZE(IGrace.REVIEW)}-carousel`, customers_reviews_display, false, true,  true);
    User.carouselSlider('.partners-carousel', partners_display, true,  false, true);
    User.carouselSlider(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.PLURALIZE(IGrace.THUMB_IMAGE()))}-carousel`, 4, true,  false, false);
    User.carouselSlider('.related-products-carousel', related_products_display, true,  false, true);


    const
        price_inputs = $(`.price-input input[type="number"]`),
        price_ranges = $(`.price-range input[type="range"]`);

    const
        min_price = parseFloat(price_inputs.first().val()),
        max_price = parseFloat(price_inputs.last().val()),
        min_range = parseFloat(price_ranges.first().val()),
        max_range = parseFloat(price_ranges.last().val());

    User.handlePriceFilter(price_inputs, price_ranges);
    User.handlePriceFilter(price_ranges, price_inputs);


    $('.more-details-desc').first().css({
        'margin-top': 'var(--ten-pixels)',
        'line-height': 'var(--twenty-five-pixels)',
    });


    const product_thumb_images = $(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.THUMB_IMAGE())}`);
    $.each(product_thumb_images, (_, productThumbImage) => {
        $(productThumbImage).on(IGrace.CLICK, () => {
            $(`.${IGrace.CLASS(IGrace.THUMB_IMAGE())}-border`).removeClass(`${IGrace.CLASS(IGrace.THUMB_IMAGE())}-border`);
            $(productThumbImage).addClass(`${IGrace.CLASS(IGrace.THUMB_IMAGE())}-border`);
            $(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.MAIN_IMAGE())} img`).attr('src', $(productThumbImage).children('img').attr('src'));
        });
    });


    $(document).on(IGrace.INPUT, (e) => {
        const target = $(e.target);

        if (target.is(`input[name="${IGrace.ADD_COLLECTION(IGrace.REVIEW)}_${IGrace.RATING}"]:radio`)) {
            target.parent()
                .next()
                .val(target.val());
        }

        if (target.is(`input[name="${IGrace.ADD_COLLECTION(IGrace.ORDER)}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}"]:radio`)) {
            target.closest(`#${IGrace.ADD_COLLECTION(IGrace.ORDER)}_form`)
                .find(`#${IGrace.ORDER}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`)
                .val(`${target.val()},`);
        }
    });

    const selected_address = sessionStorage.getItem(`selected_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`);
    if (selected_address) {
        $(`#${IGrace.ORDER}_${IGrace.COLLECTION_ID(IGrace.ADDRESS)}`).val(`${selected_address},`);
    }

    let
        add_multi_selected_product_sizes_values            = [],
        add_multi_selected_product_sizes_quick_view_values = [],
        filter_products_categories_values                  = [],
        filter_products_subcategories_values               = [],
        filter_products_sizes_values                       = [];

    $(document).on(IGrace.CLICK, (e) => {
        const target = $(e.target);

        const
            nav_menu = 'nav-menu',
            nav_submenu = 'nav-submenu',
            footer_menu = 'footer-menu',
            nav_menu_toggler = $(`.${nav_menu}-toggler > i`),
            nav_menu_close = `.${nav_menu}-close`,
            nav_menu_overlay = `.${nav_menu}-overlay`,
            nav_menu_list_header = `.${nav_menu}-list-header`,
            nav_menu_list_content = `.${nav_menu}-list-content`,
            nav_menu_list_icon = `.${nav_menu}-list-icon`,
            nav_menu_list_title = `.${nav_menu}-list-title`,
            nav_submenu_list_header = `.${nav_submenu}-list-header`,
            nav_submenu_list_content = `.${nav_submenu}-list-content`,
            nav_submenu_item_title = `.${nav_submenu}-item-title`,
            nav_submenu_item_badge = `.${nav_submenu}-item-badge`,
            nav_submenu_item_rotate_icon = `.${nav_submenu}-item-rotate-icon`,
            footer_menu_list_header = `.${footer_menu}-list-header`,
            footer_menu_item_title = `.${footer_menu}-item-title`,
            footer_menu_item_rotate_icon = `.${footer_menu}-item-rotate-icon`;

        const
            quantity_input = target.parent().find(`.${IGrace.QUANTITY}-input`),
            current_quantity_value = +quantity_input.val(),
            cart_product_remove_form = $(`#${IGrace.CART_PRODUCT()}_remove_form`);


        if (target.is(nav_menu_toggler)) {
            target.parent()
                .next()
                .add($(nav_menu_overlay))
                .addClass('active');
        }

        if (target.is(`${nav_menu_overlay}, ${nav_menu_close}`)) {
            $(`.${nav_menu}`).add($(`.${nav_menu} *`))
                .add($(nav_menu_overlay))
                .removeClass('active');

            $(nav_menu_list_content).add(nav_submenu_list_content)
                .removeClass('show');

            $(nav_submenu_item_rotate_icon).removeClass('rotate-180');
        }

        if (target.is(`${nav_menu_list_header}, ${nav_menu_list_icon}, ${nav_menu_list_title}`)) {
            const nav_menu_list_header_parent = target.is(nav_menu_list_header)
                ? target
                : target.parents(nav_menu_list_header).first();

            nav_menu_list_header_parent.toggleClass('active');
        }

        if (target.is(`${nav_submenu_list_header}, ${nav_submenu_item_title}, ${nav_submenu_item_badge}, ${nav_submenu_item_rotate_icon}`)) {
            const nav_menu_list_item = target.parents(`.${nav_menu}-list-item`).first();
            nav_menu_list_item.toggleClass('active');
            nav_menu_list_item.find(nav_submenu_item_rotate_icon).toggleClass('rotate-180');
        }

        if (target.is(`${footer_menu_list_header}, ${footer_menu_item_title}, ${footer_menu_item_rotate_icon}`)) {
            const footer_item = target.parents('.footer-item').first();
            footer_item.find(footer_menu_item_rotate_icon).toggleClass('rotate-180');
        }

        if (target.hasClass('decrement-btn')) {
            quantity_input.val(Math.max(current_quantity_value - 1, 1));
        }

        if (target.hasClass('increment-btn')) {
            quantity_input.val(current_quantity_value + 1);
        }

        if (target.hasClass(`clear-${IGrace.FILTER}`)) {
            $(`.${IGrace.FILTER}-checkbox`).prop('checked', false);

            $('input[type="hidden"]').val('');

            price_inputs.first()
                .val(min_price)
                .end()
                .last()
                .val(max_price);

            price_ranges.first()
                .val(min_range)
                .end()
                .last()
                .val(max_range);
        }

        if (target.hasClass(`${IGrace.CLASS(IGrace.CART_PRODUCT())}-remove`)) {
            cart_product_remove_form.attr('action', target.data('route')).submit();
            let cart_product_info = (dbColumn) => cart_product_remove_form.find(`input[name="${IGrace.DELETE_COLLECTION(IGrace.CART)}_${dbColumn}"]`).val(target.data(dbColumn));
            cart_product_info(IGrace.PRODUCT_SIZE());
            cart_product_info(IGrace.PRODUCT_QUANTITY());
        }

        if (target.hasClass(`write-${IGrace.REVIEW}-btn`)) {
            $(`#${IGrace.ADD_COLLECTION(IGrace.REVIEW)}_form`).fadeToggle('slow')
                .toggleClass('show-form');
        }

        User.handleFilterProductsMultiItemsWithHiddenInput({
            target: target,
            multiSelectedValuesList: filter_products_categories_values,
            relation: IGrace.PLURALIZE(IGrace.CATEGORY),
        });

        User.handleFilterProductsMultiItemsWithHiddenInput({
            target: target,
            multiSelectedValuesList: filter_products_subcategories_values,
            relation: IGrace.PLURALIZE(IGrace.SUBCATEGORY),
        });

        User.handleFilterProductsMultiItemsWithHiddenInput({
            target: target,
            multiSelectedValuesList: filter_products_sizes_values,
            relation: IGrace.PLURALIZE(IGrace.SIZE),
        });

        const common_select_all_multi_items = {
            target: target,
            actionCollection: IGrace.ADD_COLLECTION(IGrace.CART),
        };

        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...common_select_all_multi_items,
            multiSelectedValuesList: add_multi_selected_product_sizes_quick_view_values,
            relation: IGrace.PRODUCT_SIZE_QUICK_VIEW(),
        });

        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...common_select_all_multi_items,
            multiSelectedValuesList: add_multi_selected_product_sizes_values,
            relation: IGrace.PRODUCT_SIZE(),
        });

        Common.checkRowsConfig(target);
    });


    Common.charsCounter(`${IGrace.REVIEW}-${IGrace.CLASS(IGrace.PLURALIZE(IGrace.BODY_TEXT))}`);


    const observer = new MutationObserver((mutations) => {
        $.each((mutations), (key, mutation) => {
            if (mutation.type === 'childList' || mutation.type === 'subtree') {
                // Show or hide the number of selected items when selecting multiple items
                Common.showHideMultiSelectedItems($(mutation.target));
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });



    const
        add_cart_product_sizes_quick_view = $(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_SIZE_QUICK_VIEW()}`),
        add_cart_product_sizes = $(`#${IGrace.ADD_COLLECTION(IGrace.CART)}_${IGrace.PRODUCT_SIZE()}`);

    if (add_cart_product_sizes_quick_view.length) {
        Common.formMultiSelectConfig(IGrace.ADD_COLLECTION(IGrace.CART), IGrace.PRODUCT_SIZE_QUICK_VIEW());
    }

    if (add_cart_product_sizes.length) {
        Common.formMultiSelectConfig(IGrace.ADD_COLLECTION(IGrace.CART), IGrace.PRODUCT_SIZE());
    }

    Common.formSelectConfig();

    // Get the countries
    Common.getCountries();

    Common.scrollToTop();

    $('[data-tooltip="tooltip"]').tooltip();

});
