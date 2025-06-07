'use strict';

import { IGrace, Common, Admin } from "./helpers/admin-helpers.js";



/* ========================================= Global Variables ========================================= */
const
    nav_menu           = 'nav-menu',
    nav_menu_list_item = `${nav_menu}-list-item`,
    nav_menu_item      = `${nav_menu}-item`;


/* ========================================= Functions & Events ========================================= */

// Load the preloader
$(window).on('load', () => $("#preloader").delay(500).fadeOut("slow"));

// Restore the closed menu when page load
$.each(Admin.loadClosedMenu(), (_, navMenu) => {
    $(`.${navMenu}`).toggleClass('close', $(window).width() > 991.98);

    if ($(window).width() <= 991.98) sessionStorage.clear();
});

// Add some classes, styles, and attributes on each image
Common.imageConfig();

// Truncate the text that has more than 70 characters
Common.truncateText();

/* ---------=========== Change Action ===========--------- */
$(document).on(IGrace.INPUT, (e) => {
    const target = $(e.target);

    /**
     * When adding a new product,
     * set the value of the old price input with the value of the new price input automatically
     */
    if (target.is(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.NEW_PRICE}`)) {
        $(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.OLD_PRICE}`).val(target.val());
    }

    /**
     * When adding a new category, subcategory or product,
     * set the value of the image to the value of the hidden input automatically
     * and remove the image preview when changing the image
     */
    Admin.imageConfig({
        target:     target,
        collection: IGrace.CATEGORY,
    });
    Admin.imageConfig({
        target:     target,
        collection: IGrace.CATEGORY,
        imageType:  IGrace.BANNER_IMAGE(),
    });
    Admin.imageConfig({
        target:     target,
        collection: IGrace.SUBCATEGORY,
    });
    Admin.imageConfig({
        target:     target,
        collection: IGrace.PRODUCT,
    });
});

/* ---------=========== End Change Action ===========--------- */


/* ---------=========== Keyup Action ===========--------- */
$(document).on(IGrace.KEYUP, (e) => {
    const
        target              = $(e.target),
        clear_search_button = $('.clear-search-btn');

    /**
     * When typing in the search field,
     * show the clear button,
     * otherwise, hide it
     */
    if (target.is('#search')) {
        const serch_value = $('#search').val();

        clear_search_button.css({
            opacity:    IGrace.IS_NOT_EMPTY(serch_value) ? '1' : '0',
            visibility: IGrace.IS_NOT_EMPTY(serch_value) ? 'visible' : 'hidden',
        });
    }
});

/* ---------=========== End Keyup Action ===========--------- */


/* ---------=========== Mutation Observer Action ===========--------- */
const observer = new MutationObserver((mutations) => {
    $.each((mutations), (_, mutation) => {
        if (mutation.type === 'childList' || mutation.type === 'subtree' || mutation.type === 'attributes') {
            const
                target             = $(mutation.target),
                add_category       = IGrace.ADD_COLLECTION(IGrace.CATEGORY),
                update_category    = IGrace.UPDATE_COLLECTION(IGrace.CATEGORY),
                add_subcategory    = IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY),
                update_subcategory = IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY),
                add_product        = IGrace.ADD_COLLECTION(IGrace.PRODUCT),
                update_product     = IGrace.UPDATE_COLLECTION(IGrace.PRODUCT);

            const
                banner_image = IGrace.BANNER_IMAGE(),
                thumb_images = IGrace.PLURALIZE(IGrace.THUMB_IMAGE());

            const
                add_category_common = {
                    target:           target,
                    actionCollection: add_category,
                },
                update_category_common = {
                    target:           target,
                    actionCollection: update_category,
                },
                add_product_common = {
                    target:           target,
                    actionCollection: add_product,
                },
                update_product_common = {
                    target:           target,
                    actionCollection: update_product,
                };

            /**
             * When adding or updating a category, subcategory or product,
             * show or hide the image preview
             */
            Admin.showHideImagePreview({
                ...add_category_common,
            });
            Admin.showHideImagePreview({
                ...update_category_common,
            });
            Admin.showHideImagePreview({
                ...add_category_common,
                imageType: banner_image,
            });
            Admin.showHideImagePreview({
                ...update_category_common,
                imageType: banner_image,
            });
            Admin.showHideImagePreview({
                target:           target,
                actionCollection: add_subcategory,
            });
            Admin.showHideImagePreview({
                target:           target,
                actionCollection: update_subcategory,
            });
            Admin.showHideImagePreview({
                ...add_product_common,
            });
            Admin.showHideImagePreview({
                ...update_product_common,
            });
            Admin.showHideImagePreview({
                ...add_product_common,
                imageType: thumb_images,
            });
            Admin.showHideImagePreview({
                ...update_product_common,
                imageType: thumb_images,
            });

            // Show or hide the number of selected items when selecting multiple items
            Common.showHideMultiSelectedItems(target);

            // Remove the 'show' class from any list in the closed nav menu
            if (target.is(`.${nav_menu}.close .${nav_menu_list_item} .nav-submenu-list`)
                && target.hasClass('show')
                && target.hasClass('collapse'))
            {
                target.removeClass('show');
            }
        }
    });
});

observer.observe(document.body, {
    childList:       true,
    subtree:         true,
    attributes:      true,
    attributeFilter: ['class'],
});

/* ---------=========== End Mutation Observer Action ===========--------- */


/* ---------=========== Click Action ===========--------- */

let
    add_multi_selected_related_categories_values       = [],
    add_multi_selected_related_subcategories_values    = [],
    add_multi_selected_sizes_values                    = [],
    update_multi_selected_related_categories_values    = [],
    update_multi_selected_related_subcategories_values = [],
    update_multi_selected_sizes_values                 = [];

$(document).on(IGrace.CLICK, (e) => {
    const
        target                    = $(e.target),
        nav_menu_toggle           = `${nav_menu}-toggle`,
        nav_menu_toggler          = $(`.${nav_menu}-toggler > i`),
        nav_menu_item_rotate_icon = `${nav_menu_item}-rotate-icon`,
        nav_menu_close            = `.${nav_menu}-close`,
        nav_menu_overlay          = `.${nav_menu}-overlay`,
        add_product               = IGrace.ADD_COLLECTION(IGrace.PRODUCT),
        update_product            = IGrace.UPDATE_COLLECTION(IGrace.PRODUCT),
        related_categories        = IGrace.PLURALIZE(IGrace.RELATED_CATEGORY()),
        related_subcategories     = IGrace.PLURALIZE(IGrace.RELATED_SUBCATEGORY()),
        sizes                     = IGrace.PLURALIZE(IGrace.SIZE),

        add_operation_args = {
            target:           target,
            actionCollection: add_product,
        },
        update_operation_args = {
            target:           target,
            actionCollection: update_product,
        };

    // Active the responsive nav menu and overlay
    if (target.is(nav_menu_toggler)) {
        target.parent()
            .next()
            .add($(nav_menu_overlay))
            .addClass('active');
    }

    // Hide the responsive nav menu and close any list
    if (target.is(`${nav_menu_overlay}, ${nav_menu_close}`)) {
        $(`.${nav_menu}`).add($(`.${nav_menu} .${nav_menu_list_item}:not(.current-item)`))
            .add($(nav_menu_overlay))
            .removeClass('active');

        $(`.${nav_menu} .${nav_menu_list_item}:not(.current-item) ul.nav-submenu-list`).removeClass('show');

        $(nav_menu_item_rotate_icon).removeClass('rotate-180');
    }

    /**
     * Add the (close) class to the nav element,
     * if the nav menu key exists in the session storage,
     * otherwise, add the (open) class and configure the charts
     */
    if (target.is(`.${nav_menu_toggle}, .${nav_menu_toggle}-icon`)) {
        const nav_menu_actions = {
            true: () => {
                Admin.menuAction(nav_menu, 'close');

                $(`.${nav_menu} .${nav_menu_list_item}:not(.current-item)`).removeClass('active');
                $(`.${nav_menu} .${nav_menu_list_item} ul.nav-submenu-list`).removeClass('show').addClass('collapse');
            },
            false: () => {
                Admin.menuAction(nav_menu, 'open');

                $(`.${nav_menu} .${nav_menu_list_item}.active ul.nav-submenu-list`).addClass('show').removeClass('collapse');
            },
        };

        nav_menu_actions[Admin.loadClosedMenu().indexOf(nav_menu) < 0]();

        setTimeout(() => {
            Admin.googleGeoChartConfig();
            Admin.googlePieChartConfig();
        }, 300);
    }

    // Toggle the (active) class on the nav menu list item and rotate the icon
    if (target.is(`.${nav_menu_item}, .${nav_menu_item}-icon-title, .${nav_menu_item}-icon, .${nav_menu_item}-title, .${nav_menu_item_rotate_icon}`) && !target.is(`.${nav_menu}.close .${nav_menu_item}-icon`)) {
        const target_parent = target.parents(`.${nav_menu_list_item}:not(.current-item)`).toggleClass('active');
        target_parent.find(`.${nav_menu_item_rotate_icon}`).toggleClass('rotate-180');
    }

    // Handle the "Select All" checkbox and the "hidden input" value for the selected items in the filter-multi-select
    if (Common.urlLastDirectory().includes(IGrace.PLURALIZE(IGrace.SUBCATEGORY))) {
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...add_operation_args,
            actionCollection:        IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY),
            multiSelectedValuesList: add_multi_selected_related_categories_values,
            relation:                related_categories,
        });

        update_multi_selected_related_categories_values = $('input[name="update_subcategory_related_categories[]"]:hidden').val().split(',');
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...update_operation_args,
            actionCollection:        IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY),
            multiSelectedValuesList: update_multi_selected_related_categories_values,
            relation:                related_categories,
        });
    }

    if (Common.urlLastDirectory().includes(IGrace.PLURALIZE(IGrace.PRODUCT))) {
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...add_operation_args,
            multiSelectedValuesList: add_multi_selected_related_categories_values,
            relation:                related_categories,
        });
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...add_operation_args,
            multiSelectedValuesList: add_multi_selected_related_subcategories_values,
            relation:                related_subcategories,
        });
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...add_operation_args,
            multiSelectedValuesList: add_multi_selected_sizes_values,
            relation:                sizes,
        });

        update_multi_selected_related_categories_values = $('input[name="update_product_related_categories[]"]:hidden').val().split(',');
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...update_operation_args,
            multiSelectedValuesList: update_multi_selected_related_categories_values,
            relation:                related_categories,
        });

        update_multi_selected_related_subcategories_values = $('input[name="update_product_related_subcategories[]"]:hidden').val().split(',');
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...update_operation_args,
            multiSelectedValuesList: update_multi_selected_related_subcategories_values,
            relation:                related_subcategories,
        });

        update_multi_selected_sizes_values = $('input[name="update_product_sizes[]"]:hidden').val().split(',');
        Common.handleSelectAllMultiItemsWithHiddenInput({
            ...update_operation_args,
            multiSelectedValuesList: update_multi_selected_sizes_values,
            relation:                sizes,
        });
    }

    // Check/Uncheck the (check_all) checkbox and checkboxes in the table
    Common.checkRowsConfig(target);
});

/* ---------=========== End Click Action ===========--------- */

// Show the geo and pie charts
Admin.googleGeoChartConfig();
Admin.googlePieChartConfig();

// Make the content text of the product short and long descriptions as a preformatted text.
$.each(($(`.${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.SHORT_DESCRIPTION)}, .${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.LONG_DESCRIPTION)}`)), (_, description_text) => $('<pre>').html($(description_text).html()).appendTo(description_text));

// Count the number of characters of the product short and long descriptions
Common.charsCounter(`${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.SHORT_DESCRIPTION)}`);
Common.charsCounter(`${IGrace.PRODUCT}-${IGrace.CLASS(IGrace.LONG_DESCRIPTION)}`);

// Add (active) class on the first child of the carousel item
$('.carousel-item:first-child').addClass('active');

/**
 * When adding a new category, subcategory or product, or updating an existing one,
 * set the main image of each one of them automatically
 */
Admin.setImage(IGrace.ADD_COLLECTION(IGrace.CATEGORY));
Admin.setImage(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY));
Admin.setImage(IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY));
Admin.setImage(IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY));
Admin.setImage(IGrace.ADD_COLLECTION(IGrace.PRODUCT));
Admin.setImage(IGrace.UPDATE_COLLECTION(IGrace.PRODUCT));

/**
 * When adding a new category or updating an existing one,
 * set the banner image of it automatically
 */
Admin.setImage(IGrace.ADD_COLLECTION(IGrace.CATEGORY),    IGrace.BANNER_IMAGE());
Admin.setImage(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY), IGrace.BANNER_IMAGE());

/**
 * When adding a new product or updating an existing one,
 * set the thumb images of it automatically
 */
Admin.setThumbImages(IGrace.ADD);
Admin.setThumbImages(IGrace.UPDATE);

// Set up the form multiselect settings
if (Common.urlLastDirectory().includes(IGrace.PLURALIZE(IGrace.SUBCATEGORY))) {
    Common.formMultiSelectConfig(IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY), IGrace.PLURALIZE(IGrace.RELATED_CATEGORY()));
}

if (Common.urlLastDirectory().includes(IGrace.PLURALIZE(IGrace.PRODUCT))) {
    Common.formMultiSelectConfig(IGrace.ADD_COLLECTION(IGrace.PRODUCT), IGrace.PLURALIZE(IGrace.RELATED_CATEGORY()));
    Common.formMultiSelectConfig(IGrace.ADD_COLLECTION(IGrace.PRODUCT), IGrace.PLURALIZE(IGrace.RELATED_SUBCATEGORY()));
    Common.formMultiSelectConfig(IGrace.ADD_COLLECTION(IGrace.PRODUCT), IGrace.PLURALIZE(IGrace.SIZE));
}

// Set up the form select settings
Common.formSelectConfig();

// Arrange the table rows
Common.arrangeTableRows();

// Get the countries
Common.ajaxGetCountries();

// Scroll to top action
Common.scrollToTop();


/* ---------=========== Responsiveness ===========--------- */

const
    responsive_nav_menu_list_items = $(`.responsive-${nav_menu} .${nav_menu_list_item}`),
    responsive_nav_menu_items      = $(`.responsive-${nav_menu} .${nav_menu_item}`),
    responsive_nav_submenu_lists   = $(`.responsive-${nav_menu} .nav-submenu-list`);

// Add the "responsive_" prefix to the IDs of all responsive nav menu list items
Admin.addResponsivePrefix({
    elements: responsive_nav_menu_list_items,
});

// Add the "responsive_" prefix to the href attributes of all responsive nav menu items
Admin.addResponsivePrefix({
    elements: responsive_nav_menu_items,
    attribute: 'href',
    callback: (responsive_nav_menu_item_href) =>
        responsive_nav_menu_item_href.startsWith('#')
            ? responsive_nav_menu_item_href.replace('#', '#responsive_')
            : responsive_nav_menu_item_href,
});

// Add the "responsive_" prefix to the IDs of all responsive nav submenu lists
Admin.addResponsivePrefix({
    elements: responsive_nav_submenu_lists,
});

/* ---------=========== End Responsiveness ===========--------- */
