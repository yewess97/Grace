'use strict';

import { IGrace, Common } from "./common-helpers.js";


const Admin = {

    /* ---------------------------------- NAVBAR SETTINGS ---------------------------------- */

    /**
     * Save/Set the value of the (closedMenu) key as an array.
     *
     * @param closedMenu
     * @return {string}
     */
    saveClosedMenu: (closedMenu) => sessionStorage.closedMenu = JSON.stringify(closedMenu),


    /**
     * Parse the value (i.e., make it as a string) or return an empty array,
     * according to the existing of the (closedMenu) key.
     *
     * @returns {Array}
     */
    loadClosedMenu: () => JSON.parse(sessionStorage.closedMenu || '[]'),


    /**
     * Add or Remove (close) class and save the action in the session storage.
     *
     * @param navMenu
     * @param action
     * @return {void}
     */
    menuAction: (navMenu, action) => {
        const closed_menu = Admin.loadClosedMenu();

        $(`.${navMenu}`).toggleClass('close');

        action === 'close'
            ? closed_menu.push(navMenu)
            : closed_menu.pop();

        Admin.saveClosedMenu(closed_menu);
    },


    /**
     * Add a "responsive_" prefix to a specified attribute of given elements.
     *
     * @param args
     * @return {void}
     */
    addResponsivePrefix: (args) => {
        const { elements, attribute = IGrace.ID, callback = null } = args;

        $.each((elements), (_, element) => {
            const element_value = $(element).attr(attribute);

            if (element_value) {
                const element_new_value = callback
                    ? callback(element_value)
                    : `responsive_${element_value}`;

                return $(element).attr(attribute, element_new_value);
            }
        });
    },


    /* ---------------------------------- GOOGLE CHARTS ---------------------------------- */

    /**
     * Get the chart data.
     *
     * @param args
     * @return {array}
     */
    getChartData: (args) => {
        const { element, dataKey, label, legendData } = args;

        const dashboard_main = $(`.${IGrace.DASHBOARD}-main`);

        if (!dashboard_main.length) return [];

        return JSON.parse(element.getAttribute(`data-${IGrace.PLURALIZE(dataKey)}`))
            .map((item) => ([
                item[label],
                item[`${IGrace.PLURALIZE(label.includes(IGrace.COUNTRY) ? dataKey : legendData)}_count`]
            ]));
    },


    /**
     * Fill the map chart with the total of registered users per country.
     * See https://developers.google.com/chart/interactive/docs/gallery/geochart
     *
     * @param geoMapChart
     * @param registeredUsersCountries
     * @return {void}
     */
    drawGeoChart: (geoMapChart, registeredUsersCountries) => {
        const
            chart = new google.visualization.GeoChart(geoMapChart),

            data = google.visualization.arrayToDataTable([
                ['Country', 'Registered Users'],
                ...registeredUsersCountries,
            ]),

            options = {
                legend:          'none',
                backgroundColor: 'transparent',
                tooltip:         {isHtml: true},
            };

        chart.draw(data, options);
    },


    /**
     * Fill the pie chart with the total products for each subcategory.
     * See https://developers.google.com/chart/interactive/docs/gallery/piechart#configuration
     *
     * @param pieChart
     * @param subcategories
     * @return {void}
     */
    drawPieChart: (pieChart, subcategories) => {
        const
            chart                   = new google.visualization.PieChart(pieChart),
            closed_menu_session_key = sessionStorage.getItem('closedMenu');

        let data = google.visualization.arrayToDataTable([
            ['Subcategory', 'Total Products'],
            ...subcategories,
        ]);

        const
            default_options = {
                backgroundColor: 'transparent',
                chartArea:       {width: '100%', height: '100%'},
                is3D:            true,
            },

            hide_legend = {
                legend:  'none',
                tooltip: {isHtml: true},
            };

        chart.draw(
            data,
            closed_menu_session_key?.includes('nav-menu')
                ? default_options
                : {...default_options, ...hide_legend}
        );
    },


    /**
     * Configure the geo map chart.
     *
     * @return {void}
     */
    googleGeoChartConfig: () => {
        const
            dashboard_main = $(`.${IGrace.DASHBOARD}-main`),
            geo_map_chart  = document.querySelector('#geo_map_chart');

        const registered_users = Admin.getChartData({
            element: geo_map_chart,
            dataKey: IGrace.USER,
            label:   IGrace.COUNTRY,
        });

        if (dashboard_main.length) {
            google.charts.load('current', {'packages': 'geochart'});

            google.charts.setOnLoadCallback(() => Admin.drawGeoChart(geo_map_chart, registered_users));
        }
    },


    /**
     * Configure the pie map chart.
     *
     * @return {void}
     */
    googlePieChartConfig: () => {
        const
            dashboard_main = $(`.${IGrace.DASHBOARD}-main`),
            pie_chart      = document.querySelector('#pie_chart');

        let subcategories = Admin.getChartData({
            element:    pie_chart,
            dataKey:    IGrace.SUBCATEGORY,
            label:      IGrace.NAME,
            legendData: IGrace.PRODUCT,
        });

        if (dashboard_main.length) {
            google.charts.load('current', {'packages': 'corechart'});

            google.charts.setOnLoadCallback(() => Admin.drawPieChart(pie_chart, subcategories ?? [['No Data', 1]]));
        }
    },



    /* ---------------------------------- IMAGES SETTINGS ---------------------------------- */

    /**
     * Empty the image error.
     *
     * @param actionCollection
     * @return {void}
     */
    emptyImageError: (actionCollection) => {
        $(document).on(IGrace.CHANGE, `#${actionCollection}_${IGrace.MAIN_IMAGE()}`, function () {
            $(`#${actionCollection}_${IGrace.MAIN_IMAGE()}_${IGrace.ERROR}`).empty();

            if (actionCollection.includes(IGrace.CATEGORY) && !actionCollection.includes(IGrace.SUBCATEGORY)) {
                $(`#${actionCollection}_${IGrace.BANNER_IMAGE()}_${IGrace.ERROR}`).empty();
            }
        });
    },


    /**
     * Configure the collection image input settings.
     *
     * @param actionCollection
     * @param imageType
     * @return {void}
     */
    setImage: (actionCollection, imageType = IGrace.MAIN_IMAGE()) => {
        let image = `#${actionCollection}_${imageType}`;

        const
            image_container = `${image}_container`,
            image_type      = imageType.replace('_', ' ');

        const image_options = {
            label:         `Choose ${IGrace.CAPITALIZE(image_type)}`, // label text
            input:         image,   // input selector
            dragDrop:      false,   // drag & drop upload
            multiple:      false,   // multiple file upload
            fileType:      ['png', 'jpg', 'jpeg'], // allowed file formats
            fileTypeError: `Allowed ${image_type} formats are png, jpg, jpeg`, // allowed file formats error
            maxSize:       '2 MB', // maximum uploaded file size
            maxSizeError:  `Max size of the ${image_type} should not exceed `, // maximum uploaded file size error
        };

        $(image_container).aksFileUpload(image_options);

        image = image.replace('#', '');

        $(`${image_container} .aks-file-upload-content`).addClass(IGrace.CLASS(`${image}-content`));

        $(`#${image}`).attr({
            'name':   image,
            'accept': '.png, .jpg, .jpeg',
        });
    },


    /**
     * Set the image value to the hidden input automatically when adding a new collection,
     * and remove the image preview when changing the image.
     *
     * @param args
     * @return {void}
     */
    imageConfig: (args) => {
        const { target, collection, imageType = IGrace.MAIN_IMAGE() } = args;

        const is_add_or_update_collection_image = [IGrace.ADD_COLLECTION(collection), IGrace.UPDATE_COLLECTION(collection)]
            .some((action) => target.is(`#${action}_${imageType}`));

        if (is_add_or_update_collection_image) {
            target.parents()
                .eq(1)
                .next()
                .val(target.val());

            target.next()
                .find('div:first-child')
                .parents()
                .eq(1)
                .remove();
        }
    },


    /**
     * Show the image preview in the edit form.
     *
     * @param args
     * @return {void}
     */
    showImageOnEdit: (args) => {
        const { collection, imageType, imageSrc } = args;

        const
            [collection_name, collection_var] = collection,
            image_preview                     = $(`#${IGrace.UPDATE_COLLECTION(collection_name)}_${imageType}_preview`);

        image_preview.removeAttr('class').html('');

        if (imageType.includes(IGrace.THUMB_IMAGE())) {
            return $.isEmptyObject(imageSrc)
                ? image_preview.addClass('my-3 fs-6 fw-600 text-center').html('*No Thumbnail Images to Preview*')
                : image_preview.addClass('row row-cols-2 row-cols-md-3 justify-content-center align-items-center gap-3 my-3')
                    .append(imageSrc.map((imageSource) => `
                    <div class="col w-auto">
                        <img src="${imageSource}" class="img-thumbnail" width="200px" height="200px" alt="${collection_var[IGrace.NAME]}">
                    </div>
                `).join(''));
        }

        image_preview.addClass('d-grid place-items-center my-3')
            .html(`<img src="${imageSrc}" class="img-thumbnail" width="200px" height="200px" alt="${collection_var[IGrace.NAME]}">`);
    },


    /**
     * Configure the product thumbnail images input settings.
     *
     * @param action
     * @return {void}
     */
    setThumbImages: (action) => {
        const thumb_image = `#${action}_${IGrace.PRODUCT}_${IGrace.THUMB_IMAGE()}`;

        const thumb_images_options = {
            label:         'Choose Thumbnail Images', // label text
            input:         thumb_image, // input selector
            dragDrop:      false,       // drag & drop upload
            multiple:      true,        // multiple file upload
            fileType:      ['png', 'jpg', 'jpeg'], // allowed file formats
            fileTypeError: 'Allowed thumbnail images formats are png, jpg, jpeg', // allowed file formats error
            maxSize:       '2 MB', // maximum uploaded file size
            maxSizeError:  'There is a thumbnail image exceeds allowed size, Max size is', // maximum uploaded file size error
            maxFile:       10, // maximum number of uploaded files
            maxFileError:  'Thumbnail images number exceeds upload limit, Max limit is', // maximum number of uploaded files error

        };

        $(`#${action}_${IGrace.PLURALIZE(IGrace.THUMB_IMAGE())}`).aksFileUpload(thumb_images_options);

        $(`#${action}_${IGrace.PLURALIZE(IGrace.THUMB_IMAGE())} .aks-file-upload-content`).addClass(IGrace.CLASS(`${IGrace.PLURALIZE(thumb_image.replace('#', ''))}-content`));

        $(thumb_image).attr('accept', '.png, .jpg, .jpeg');
    },


    /**
     * Show or Hide the image preview when adding/updating a collection.
     *
     * @param args
     * @return {void}
     */
    showHideImagePreview: (args) => {
        const { target, actionCollection, imageType = IGrace.MAIN_IMAGE() } = args;

        if (target.hasClass(`${IGrace.CLASS(actionCollection)}-${IGrace.CLASS(imageType)}-content`)) {
            setTimeout(() => {
                const image_preview = $(`#${actionCollection}_${imageType}_preview`);

                if (target.find('div').length > 0) {
                    return image_preview.addClass('d-none');
                }

                target.prev().val('');

                target.parents()
                    .eq(1)
                    .next()
                    .val('');

                image_preview.removeClass('d-none');
            }, 100);
        }
    },



    /* ---------------------------------- CREATE OR UPDATE REQUEST ---------------------------------- */
    ajaxCreateOrUpdateRequest: (form) => {
        $(document).on(IGrace.SUBMIT, `#${form}_form`, function (e) {
            e.preventDefault();

            const
                target               = $(this),
                route                = target.attr('action'),
                main_page            = target.data('main'),
                [action, collection] = form.split('_'),
                form_data            = Common.filteredFormData(this);

            // FormData() accepts only POST method
            if (action === IGrace.UPDATE) form_data.append('_method', IGrace.PUT);

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                success: (data) => {
                    $(IGrace.MODAL(IGrace.ADMIN)).modal('hide');
                    target.trigger('reset');
                    $(IGrace.ERROR_ELEMENT(action)).empty();

                    const data_actions = {
                        true: () => {
                            Common.updateTableRows({
                                data:       data,
                                mainPage:   main_page,
                                collection: collection,
                                action:     action,
                            });
                        },
                        false: () => main_page = IGrace.ADMIN,
                    };

                    data_actions[!IGrace.IS_IN_ARRAY([IGrace.ORDER, IGrace.REVIEW], collection)]();

                    Common.successMessage(IGrace.SUCCESS, `${IGrace.CAPITALIZE(collection)} has been ${action === IGrace.ADD ? IGrace.ADDED() : IGrace.UPDATED()}`, main_page);
                },
                error: (err) => {
                    if (err.status === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (err.status === 422) {
                        return Common.errorMessage(action, Common.responseJsonError(err));
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- EDIT REQUEST ---------------------------------- */

    /**
     * Edit Category Ajax Request.
     *
     * @return {void}
     */
    ajaxEditCategoryRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.CATEGORY), function (e) {
            e.preventDefault();

            const
                target       = $(this),
                route        = target.data('route'),
                main_image   = target.data(IGrace.MAIN_IMAGE()),
                banner_image = target.data(IGrace.BANNER_IMAGE());

            $.get(route)
                .done((data) => {
                    const category = data[IGrace.CATEGORY];

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.CATEGORY))}`).val(category[IGrace.ID]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.CATEGORY)}_${IGrace.NAME}`).val(category[IGrace.NAME]);
                    Admin.showImageOnEdit({
                        collection: [IGrace.CATEGORY, category],
                        imageType:  IGrace.MAIN_IMAGE(),
                        imageSrc:   main_image,
                    });
                    Admin.showImageOnEdit({
                        collection: [IGrace.CATEGORY, category],
                        imageType:  IGrace.BANNER_IMAGE(),
                        imageSrc:   banner_image,
                    });

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.CATEGORY, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY));
        });
    },

    /**
     * Edit Subcategory Ajax Request.
     *
     * @return {void}
     */
    ajaxEditSubcategoryRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.SUBCATEGORY), function (e) {
            e.preventDefault();

            const
                target     = $(this),
                route      = target.data('route'),
                main_image = target.data(IGrace.MAIN_IMAGE());

            $.get(route)
                .done((data) => {
                    const subcategory = data[IGrace.SUBCATEGORY];

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.SUBCATEGORY))}`).val(subcategory[IGrace.ID]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY)}_${IGrace.NAME}`).val(subcategory[IGrace.NAME]);
                    Admin.showImageOnEdit({
                        collection: [IGrace.SUBCATEGORY, subcategory],
                        imageType:  IGrace.MAIN_IMAGE(),
                        imageSrc:   main_image,
                    });
                    Common.showMultiSelectData({
                        userType:          IGrace.ADMIN,
                        collection:        subcategory,
                        collectionName:    IGrace.SUBCATEGORY,
                        relatedCollection: IGrace.RELATED_CATEGORY(),
                    });

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.SUBCATEGORY, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY));
        });
    },

    /**
     * Edit Product Ajax Request.
     *
     * @return {void}
     */
    ajaxEditProductRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.PRODUCT), function (e) {
            e.preventDefault();

            const
                target       = $(this),
                route        = target.data('route'),
                main_image   = target.data(IGrace.MAIN_IMAGE()),
                thumb_images = target.data(IGrace.PLURALIZE(IGrace.THUMB_IMAGE())).split(' ').filter(Boolean),
                status       = $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.STATUS}`);

            $.get(route)
                .done((data) => {
                    const product = data[IGrace.PRODUCT];

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.PRODUCT))}`).val(product[IGrace.ID]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.NAME}`).val(product[IGrace.NAME]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.SHORT_DESCRIPTION}`).html(product[`${IGrace.SHORT_DESCRIPTION}`]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.LONG_DESCRIPTION}`).html(product[`${IGrace.LONG_DESCRIPTION}`]);
                    Admin.showImageOnEdit({
                        collection: [IGrace.PRODUCT, product],
                        imageType:  IGrace.MAIN_IMAGE(),
                        imageSrc:   main_image,
                    });
                    Admin.showImageOnEdit({
                        collection: [IGrace.PRODUCT, product],
                        imageType:  IGrace.PLURALIZE(IGrace.THUMB_IMAGE()),
                        imageSrc:   thumb_images,
                    });
                    const commonMultiSelectDataArgs = {
                        userType:       IGrace.ADMIN,
                        collection:     product,
                        collectionName: IGrace.PRODUCT,
                    };
                    Common.showMultiSelectData({
                        ...commonMultiSelectDataArgs,
                        relatedCollection: IGrace.RELATED_CATEGORY(),
                    });
                    Common.showMultiSelectData({
                        ...commonMultiSelectDataArgs,
                        relatedCollection: IGrace.RELATED_SUBCATEGORY(),
                    });
                    Common.showMultiSelectData({
                        ...commonMultiSelectDataArgs,
                        relatedCollection: IGrace.SIZE,
                    });
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.OLD_PRICE}`).val(product[`${IGrace.OLD_PRICE}`]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.NEW_PRICE}`).val(product[`${IGrace.NEW_PRICE}`]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.QUANTITY}`).val(product[`${IGrace.QUANTITY}`]);
                    status.find('option').removeAttr('selected')
                        .filter((_, productStatus) => +productStatus.value === +product[IGrace.STATUS])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.PRODUCT, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.PRODUCT));
        });
    },

    /**
     * Edit User Ajax Request.
     *
     * @return {void}
     */
    ajaxEditUserRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.USER), function (e) {
            e.preventDefault();

            const
                target = $(this),
                route  = target.data('route'),
                role   = $(`#${IGrace.UPDATE_COLLECTION(IGrace.USER)}_${IGrace.ROLE}`);

            $.get(route)
                .done((data) => {
                    const user = data[IGrace.USER];

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.USER))}`).val(user[IGrace.ID]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.USER)}_${IGrace.FIRST_NAME()}`).val(user[IGrace.FIRST_NAME()]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.USER)}_${IGrace.LAST_NAME()}`).val(user[IGrace.LAST_NAME()]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.USER)}_${IGrace.EMAIL}`).val(user[IGrace.EMAIL]);
                    role.find('option').removeAttr('selected')
                        .filter((_, userRole) => +userRole.value === +user[IGrace.ROLE])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.USER, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },

    /**
     * Edit Order Ajax Request.
     *
     * @return {void}
     */
    ajaxEditOrderRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ORDER), function (e) {
            e.preventDefault();

            const
                target = $(this),
                route  = target.data('route'),
                status = $(`#${IGrace.UPDATE_COLLECTION(IGrace.ORDER)}_${IGrace.STATUS}`);

            $.get(route)
                .done((data) => {
                    const order = data[IGrace.ORDER];

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.ORDER))}`).val(order[IGrace.ID]);
                    status.find('option').removeAttr('selected')
                        .filter((_, orderStatus) => +orderStatus.value === +order[IGrace.STATUS])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ORDER, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- FILTER REQUEST ---------------------------------- */
    /**
     * Filter Ajax Request.
     *
     * @param args
     * @return {void}
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
                route              = filter_form.attr('action'),
                form_data          = new FormData(filter_form[0]),
                dashboard_main     = `.${IGrace.DASHBOARD}-main`,
                no_results_img_src = filter_form.data('no_results');

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                success: (data) => {
                    const actions = {
                        [IGrace.DASHBOARD]: () => {
                            $(dashboard_main).html($(data).find(dashboard_main).html());

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
                    if (err.status === 422) {
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


    /* ---------------------------------- NOTIFICATION REQUEST ---------------------------------- */
    /**
     * Mark Notification as read Ajax Request.
     * 
     * @param className
     * @return {void}
     */
    ajaxMarkNotificationAsReadRequest: (className) => {
        $(document).on(IGrace.CLICK, `.${className}`, function (e) {
            e.preventDefault();

            const route = $(this).attr('href');

            $.post(route)
                .done((data) => {
                    if (className.includes('all')) {
                        $('.notifications-count').css('display', 'none');
                        $('.notification-item').removeClass('highlight-background');
                        $('.mark-as-read-icon').remove();
                        return;
                    }

                    const notification_item = $(`#notification${data[IGrace.ID]}`);

                    $('.notifications-count').text() > 1 
                        ? $('.notifications-count').text($('.notifications-count').text() - 1)
                        : $('.notifications-count').css('display', 'none');
                        
                    notification_item.removeClass('highlight-background');
                    notification_item.find('.mark-as-read-icon').remove();
                })
                .fail(Common.somethingWentWrongError);
        });
    },

    /**
     * Delete Notification Ajax Request
     */
    ajaxDeleteNotificationRequest: () => {
        $(document).on(IGrace.CLICK, '.delete-notification-form', function (e) {
            e.preventDefault();

            const route = $(this).attr('action');

            $.ajax({
                url: route,
                method: IGrace.DELETE.toUpperCase(),
                success: (data) => $(`#notification${data[IGrace.ID]}`).remove(),
                error: () => Common.somethingWentWrongError(),
            });
        });
    },
}



/**
 * Export IGrace & Common & Admin Objects.
 */
export { IGrace, Common, Admin };
