'use strict';

import { IGrace, Common } from "./common-helpers.js";


const Admin = {

    /* ---------------------------------- NAVBAR SETTINGS ---------------------------------- */
    /**
     * Stringify the closed menu array and save it in the session storage.
     *
     * @param closedMenu
     * @return {string}
     */
    saveClosedMenu: (closedMenu) => sessionStorage.closedMenu = JSON.stringify(closedMenu),


    /**
     * Parse the closed menu string from the session storage and return it as an array.
     *
     * @return {any}
     */
    loadClosedMenu: () => JSON.parse(sessionStorage.closedMenu || '[]'),


    /**
     * Toggle the 'close' class on the specified navigation menu and update the closed menu array in the session storage,
     * based on the action (close or open).
     * This allows the application to remember which menus are closed even after a page refresh.
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


    /* ---------------------------------- GOOGLE CHARTS ---------------------------------- */
    /**
     * Get the chart data from the specified element's data attribute, parse it,
     * and return it in a format suitable for Google Charts.
     *
     * @param args
     * @return {*|*[]}
     */
    getChartData: (args) => {
        const { element, dataKey, label, legendData } = args;

        const dashboard_main = $(`.${IGrace.DASHBOARD}-main`);

        if (!dashboard_main.length) {
            return [];
        }

        return JSON.parse(element.getAttribute(`data-${IGrace.PLURALIZE(dataKey)}`))
            .map((item) => ([
                item[label],
                item[`${IGrace.PLURALIZE(label.includes(IGrace.COUNTRY) ? dataKey : legendData)}_count`]
            ]));
    },


    /**
     * Fill the geo map chart with the total registered users for each country.
     *
     * @see https://developers.google.com/chart/interactive/docs/gallery/geochart
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
     *
     * @see https://developers.google.com/chart/interactive/docs/gallery/piechart#configuration
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
     * Empty the image preview and error message
     * when changing the collection main image in the add or update form.
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
     * Configure the collection images input settings in the add or update form,
     * including allowed file types, maximum file size, and error messages.
     *
     * @param actionCollection
     * @param imageType
     * @return {void}
     */
    setImageConfig: (actionCollection, imageType = IGrace.MAIN_IMAGE()) => {
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

        $(`${image_container} .aks-file-upload-content`).addClass(IGrace.CLASS(`${image}-content`))
            .children()
            .addClass('cursor-pointer');

        $(`#${image}`).attr({
            'name':   image,
            'accept': '.png, .jpg, .jpeg',
        });
    },


    /**
     * Configure the product thumbnail images input settings in the add or update form.
     *
     * @param action
     * @return {void}
     */
    setThumbImagesConfig: (action) => {
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

        $(`#${action}_${IGrace.PLURALIZE(IGrace.THUMB_IMAGE())} .aks-file-upload-content`)
            .addClass(IGrace.CLASS(`${IGrace.PLURALIZE(thumb_image.replace('#', ''))}-content`))
            .children()
            .addClass('cursor-pointer');

        $(thumb_image).attr('accept', '.png, .jpg, .jpeg');
    },


    /**
     * Display the image preview for the collection main image or product thumbnail images in the update form,
     * or display a message if there are no thumbnail images to preview.
     *
     * @param args
     * @return {*}
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



    /* ---------------------------------- CREATE OR UPDATE REQUEST ---------------------------------- */
    /**
     * Handle the create or update ajax request for the specified form.
     *
     * @param form
     * @return {void}
     */
    ajaxCreateOrUpdateRequest: (form) => {
        $(document).on(IGrace.SUBMIT, `#${form}_form`, function (e) {
            e.preventDefault();

            const
                target                    = $(this),
                route                     = target.attr('action'),
                action_btn                = target.find('.action-btn'),
                [action, collection_name] = form.split('_'),
                form_data                 = Common.filteredFormData(this);

            let main_page = target.data('main');

            // FormData() accepts only POST method
            if (action === IGrace.UPDATE) {
                form_data.append('_method', IGrace.PUT);
            }

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                beforeSend: () => action_btn.loadingSpinner({ isDisabled: true }),
                success: (data) => {
                    window.isFormDirty = false;
                    target.trigger('reset');
                    $('input[type="hidden"]').val('');
                    $.each((target.find('.aks-file-upload-label')), (_, uploadImageLabel) => $(uploadImageLabel).nextAll().remove());
                    $.each((target.find('.selected-items')), (_, selectedItemsElem) => {
                        $(selectedItemsElem).empty();
                        $(selectedItemsElem).prev().removeAttr('hidden');
                        $(selectedItemsElem).prevAll(':eq(1)').html().replace(/^\d+/, '0');
                    });
                    $(IGrace.ERROR_ELEMENT(action)).empty();
                    $(IGrace.MODAL(IGrace.ADMIN)).modal('hide');

                    const data_actions = {
                        true: () => {
                            Common.updateTableRows({
                                data:     data,
                                mainPage: main_page,
                                action:   action,
                            });
                        },
                        false: () => main_page = IGrace.ADMIN,
                    };

                    data_actions[!IGrace.IS_IN_ARRAY([IGrace.ORDER, IGrace.REVIEW], collection_name)]();

                    // Remove the loading spinner
                    action_btn.loadingSpinner();

                    Common.successMessage(IGrace.SUCCESS, `${IGrace.CAPITALIZE(collection_name)} has been ${action === IGrace.ADD ? IGrace.ADDED() : IGrace.UPDATED()}`, main_page);
                },
                error: (err) => {
                    if (err.status === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (err.status === 422) {
                        // Remove the loading spinner
                        action_btn.loadingSpinner();

                        return Common.errorMessage(action, Common.responseJsonError(err));
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- EDIT REQUEST ---------------------------------- */
    /**
     * Edit Category ajax request.
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

                    window.isFormDirty = false;
                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.CATEGORY, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY));
        });
    },

    /**
     * Edit Subcategory ajax request.
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

                    window.isFormDirty = false;
                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.SUBCATEGORY, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY));
        });
    },

    /**
     * Edit Product ajax request.
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
                    const
                        product       = data[IGrace.PRODUCT],
                        short_desc_id = `${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.SHORT_DESCRIPTION}`,
                        long_desc_id  = `${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.LONG_DESCRIPTION}`;

                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.COLLECTION_ID(IGrace.PRODUCT))}`).val(product[IGrace.ID]);
                    $(`#${IGrace.UPDATE_COLLECTION(IGrace.PRODUCT)}_${IGrace.NAME}`).val(product[IGrace.NAME]);

                    /**
                     * ".val()" prevents formatting issues or broken tags if the HTML string contains nested quotes or unescaped characters,
                     * while "tinymce.get(id).setContent(html)" ensures that the content is properly rendered in the TinyMCE editor,
                     * preserving the intended formatting and structure of the HTML string
                     */
                    $(`#${short_desc_id}`).val(product[`${IGrace.SHORT_DESCRIPTION}`]);
                    tinymce.get(short_desc_id)?.setContent(product[`${IGrace.SHORT_DESCRIPTION}`] ?? '');
                    $(`#${long_desc_id}`).val(product[`${IGrace.LONG_DESCRIPTION}`]);
                    tinymce.get(long_desc_id)?.setContent(product[`${IGrace.LONG_DESCRIPTION}`] ?? '');

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

                    status.find('option')
                        .removeAttr('selected')
                        .filter((_, productStatus) => +productStatus.value === +product[IGrace.STATUS])
                        .attr('selected', true);

                    window.isFormDirty = false;
                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.PRODUCT, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.PRODUCT));
        });
    },

    /**
     * Edit User ajax request.
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

                    role.find('option')
                        .removeAttr('selected')
                        .filter((_, userRole) => +userRole.value === +user[IGrace.ROLE])
                        .attr('selected', true);

                    window.isFormDirty = false;
                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.USER, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },

    /**
     * Edit Order ajax request.
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

                    status.find('option')
                        .removeAttr('selected')
                        .filter((_, orderStatus) => +orderStatus.value === +order[IGrace.STATUS])
                        .attr('selected', true);

                    window.isFormDirty = false;
                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ORDER, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },


    /* ---------------------------------- FILTER REQUEST ---------------------------------- */
    /**
     * Handle the filter ajax request for the specified collection and element (form or input).
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
                form_data          = new FormData(filter_form[0]),
                dashboard_main     = `.${IGrace.DASHBOARD}-main`,
                no_results_img_src = filter_form.data('no_results'),
                updateRoute = (route, form_data) => {
                    return function(params) {
                        if (typeof params === 'string') params = [params];

                        const query = params
                            .map((param) => `${param}=${encodeURIComponent(form_data.get(param))}`)
                            .join('&');

                        return `${route}${Common.routeParamsSeperator(route)}${query}`;
                    };
                },

                add_params = updateRoute(filter_form.attr('action'), form_data);

            let url = filter_form.attr('action');

            if (collection.includes(IGrace.USER)) {
                url = add_params(`${IGrace.FILTER}_${IGrace.PLURALIZE(IGrace.USER)}_${IGrace.ROLE}`);
            }

            if (collection.includes(IGrace.ORDER)) {
                url = add_params([`${IGrace.FILTER_ORDERS()}_start_date`, `${IGrace.FILTER_ORDERS()}_end_date`]);
            }

            $.ajax({
                url: url,
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

                    window.isFormDirty = false;
                    $(IGrace.ERROR_ELEMENT(IGrace.FILTER)).empty();
                },
                error: (err) => {
                    if (err.status === 422) {
                        return Common.errorMessage(IGrace.FILTER, Common.responseJsonError(err));
                    }

                    if (err.status === 404) {
                        return Common.searchFilterErrorResponse(no_results_img_src);
                    }

                    Common.somethingWentWrongError();
                },
            });
        });
    },


    /* ---------------------------------- NOTIFICATION REQUEST ---------------------------------- */
    /**
     * Handle the ajax request to mark a notification as read
     * when clicking on the specified element.
     *
     * @param className
     * @return {void}
     */
    ajaxMarkNotificationAsReadRequest: (className) => {
        $(document).on(IGrace.CLICK, `.${className}`, function (e) {
            e.preventDefault();

            const route = $(this).attr('href');

            const deleteNotificationTemplate = (id) =>
                $(`#notification${id}`).append(`
                    <form action="${location.origin}/${IGrace.ADMIN}/notifications/${IGrace.DELETE}-notification?id=${id}" method="post" role="form" class="delete-notification-form">
                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" role="button" title="Delete Notification" data-tooltip="tooltip" data-mdb-placement="top" class="fs-6 bg-transparent text-danger border-0">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                `);

            $.post(route)
                .done((data) => {
                    const
                        notification_count = $('.notifications-count'),
                        notification_item  = $(`#notification${data[IGrace.ID]}`);

                    if (className.includes('all')) {
                        notification_count.css('display', 'none');
                        notification_item.removeClass('bg-highlight');
                        $('.mark-as-read-icon').remove();
                        $.each((data[IGrace.PLURALIZE(IGrace.ID)]), (_, id) => deleteNotificationTemplate(id));
                        return;
                    }

                    notification_count.text() > 1
                        ? notification_count.text(notification_count.text() - 1)
                        : notification_count.css('display', 'none');

                    notification_item.removeClass('bg-highlight')
                        .end()
                        .find('.mark-as-read-icon')
                        .remove();

                    deleteNotificationTemplate(data[IGrace.ID]);
                })
                .fail(Common.somethingWentWrongError);
        });
    },

    /**
     * Handle the ajax request to delete a notification
     * when submitting the specified form.
     *
     * @return {void}
     */
    ajaxDeleteNotificationRequest: () => {
        $(document).on(IGrace.CLICK, `.${IGrace.DELETE}-notification-form`, function (e) {
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
