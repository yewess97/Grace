'use strict';

import { IGrace, Common, Admin } from "./helpers/admin-helpers.js";
import "./helpers/common-plugins.js";
import "./helpers/admin-plugins.js";


$(document).ready(() => {
    /* ========================================= Global Variables ========================================= */
    const
        nav_menu           = 'nav-menu',
        nav_menu_list_item = `${nav_menu}-list-item`,
        nav_menu_item      = `${nav_menu}-item`;

    /* ========================================= Functions & Events ========================================= */

    // Load the preloader
    Common.loadPreloader();

    // Restore the closed menu when page load
    $.each(Admin.loadClosedMenu(), (_, navMenu) => {
        $(`.${navMenu}`).toggleClass('close', $(window).width() > 991.98);

        if ($(window).width() <= 991.98) {
            sessionStorage.clear();
        }
    });

    // Add some classes, styles, and attributes on each image
    Common.imageConfig();

    // Truncate the text that has more than 70 characters
    Common.truncateText();

    /* ---------=========== Change (Input) Action ===========--------- */
    $(document).on(IGrace.INPUT, (e) => {
        const target = $(e.target);

        // Set the value of the old price input with the value of the new price input automatically
        if (target.is(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.NEW_PRICE}`)) {
            $(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.OLD_PRICE}`).val(target.val());
        }

        // Handles the image preview configurations when changing the image when adding a collection
        target.imagePreviewConfig({ collection: IGrace.CATEGORY });
        target.imagePreviewConfig({
            collection: IGrace.CATEGORY,
            imageType: IGrace.BANNER_IMAGE(),
        });
        target.imagePreviewConfig({ collection: IGrace.SUBCATEGORY });
        target.imagePreviewConfig({ collection: IGrace.PRODUCT });
    });

    /* ---------=========== End Change (Input) Action ===========--------- */


    /* ---------=========== Keyup Action ===========--------- */
    $(document).on(IGrace.KEYUP, (e) => {
        const target = $(e.target);

        // Handles the clear button visibility when typing
        if (target.is('#search')) {
            const
                clear_search_button = $('.clear-search-btn'),
                serch_value         = $('#search').val();

            clear_search_button.css({
                opacity:    IGrace.IS_NOT_EMPTY(serch_value) ? '1' : '0',
                visibility: IGrace.IS_NOT_EMPTY(serch_value) ? 'visible' : 'hidden',
            });
        }
    });

    /* ---------=========== End Keyup Action ===========--------- */


    /* ---------=========== Click Action ===========--------- */
    $(document).on(IGrace.CLICK, (e) => {
        const
            target                    = $(e.target),
            nav_menu_toggle           = `${nav_menu}-toggle`,
            nav_menu_toggler          = $(`.${nav_menu}-toggler > i`),
            nav_menu_item_rotate_icon = `${nav_menu_item}-rotate-icon`,
            nav_menu_close            = `.${nav_menu}-close`,
            nav_menu_overlay          = `.${nav_menu}-overlay`;

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

        // Handles the nav menu open/close functionality
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
            target.parents(`.${nav_menu_list_item}:not(.current-item)`)
                .toggleClass('active')
                .find(`.${nav_menu_item_rotate_icon}`)
                .toggleClass('rotate-180');
        }

        // Check/Uncheck the (check_all) checkbox and checkboxes in the table
        target.checkRows();
    });

    /* ---------=========== End Click Action ===========--------- */


    /* ---------=========== Mutation Observer Action ===========--------- */
    const observer = new MutationObserver((mutations) => {
        $.each((mutations), (_, mutation) => {
            if (mutation.type === 'childList' || mutation.type === 'subtree' || mutation.type === 'attributes') {
                const target = $(mutation.target);

                /**
                 * Handle the image preview visibility when updating a collection
                 */
                target.showHideImagePreview({ collection: IGrace.CATEGORY });
                target.showHideImagePreview({
                    collection: IGrace.CATEGORY,
                    imageType:  IGrace.BANNER_IMAGE(),
                });

                target.showHideImagePreview({ collection: IGrace.SUBCATEGORY });

                target.showHideImagePreview({ collection: IGrace.PRODUCT });
                target.showHideImagePreview({
                    collection: IGrace.PRODUCT,
                    imageType:  IGrace.PLURALIZE(IGrace.THUMB_IMAGE()),
                });

                // Show or hide the number of selected items when selecting multiple items
                target.showHideMultiSelectedItems();

                // Remove the 'show' class from any list in the closed nav menu
                if (target.is(`.${nav_menu}.close .${nav_menu_list_item} .nav-submenu-list`)
                    && target.hasClass('show')
                    && target.hasClass('collapse')
                ) {
                    target.removeClass('show');
                }
            }
        });
    });

    // Observe the body for any changes in the child elements, subtree, and attributes (specifically the class attribute)
    observer.observe(document.body, {
        childList:       true,
        subtree:         true,
        attributes:      true,
        attributeFilter: ['class'],
    });

    /* ---------=========== End Mutation Observer Action ===========--------- */

    // Show the geo and pie charts
    Admin.googleGeoChartConfig();
    Admin.googlePieChartConfig();

    // Get the countries
    if ([IGrace.DASHBOARD, IGrace.PLURALIZE(IGrace.ADDRESS)].some((directory) => Common.urlLastDirectory().includes(directory))) {
        Common.ajaxGetCountries();
    }

    // Configure the TinyMCE rich text editor for the specified textarea selector in the add or update form.
    tinymce.init({
        license_key: 'gpl',
        selector: `textarea.text-editor`,
        height: 300,
        menubar: true,
        a11y_advanced_options: true,
        placeholder: 'Type your text here',
        plugins: [
            'accordion', 'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'directionality', 'searchreplace', 'visualblocks', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'fullscreen undo redo | blocks fontfamily fontsize | ' +
            'bold italic underline forecolor backcolor | ' +
            'ltr rtl alignleft aligncenter alignright alignjustify lineheight | ' +
            'accordion bullist numlist outdent indent | media image link | ' +
            'removeformat | help'
    });

    // Set the main image configurations when adding/updating a collection
    Admin.setImageConfig(IGrace.ADD_COLLECTION(IGrace.CATEGORY));
    Admin.setImageConfig(IGrace.ADD_COLLECTION(IGrace.CATEGORY), IGrace.BANNER_IMAGE());
    Admin.setImageConfig(IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY));
    Admin.setImageConfig(IGrace.ADD_COLLECTION(IGrace.PRODUCT));

    Admin.setImageConfig(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY));
    Admin.setImageConfig(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY), IGrace.BANNER_IMAGE());
    Admin.setImageConfig(IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY));
    Admin.setImageConfig(IGrace.UPDATE_COLLECTION(IGrace.PRODUCT));

    // Set the thumb images configurations when adding/updating a product
    Admin.setThumbImagesConfig(IGrace.ADD);
    Admin.setThumbImagesConfig(IGrace.UPDATE);

    // Set up the form multiselect settings
    $(`#${IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY)}_${IGrace.PLURALIZE(IGrace.RELATED_CATEGORY())}`)?.formMultiSelectConfig();
    $(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.PLURALIZE(IGrace.RELATED_CATEGORY())}`)?.formMultiSelectConfig();
    $(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.PLURALIZE(IGrace.RELATED_SUBCATEGORY())}`)?.formMultiSelectConfig();
    $(`#${IGrace.ADD_COLLECTION(IGrace.PRODUCT)}_${IGrace.PLURALIZE(IGrace.SIZE)}`)?.formMultiSelectConfig();

    // Add (active) class on the first child of the carousel item
    $('.carousel-item:first-child').addClass('active');

    // Set up the form select settings
    Common.formSelectConfig();

    // Arrange the table rows
    Common.arrangeTableRows();

    // Scroll to top action
    Common.scrollToTop();

    // Set up the tooltip
    $('[data-tooltip="tooltip"]').tooltip();
});
