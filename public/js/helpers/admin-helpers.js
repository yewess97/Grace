'use strict';

import { IGrace, Common } from "./common-helpers.js";


const Admin = {

    /* ---------------------------------- NAVBAR SETTINGS ---------------------------------- */

    /**
     * Save/Set the value of the (closedMenu) key as an array.
     *
     * @param closedMenu
     * @returns {string}
     */
    saveClosedMenu: (closedMenu) => sessionStorage.closedMenu = JSON.stringify(closedMenu),


    /**
     * Parse the value (i.e., make it as a string) or return an empty array,
     * according to the existing of the (closedMenu) key.
     *
     * @returns {any|*[]}
     */
    loadClosedMenu: () => sessionStorage.closedMenu
        ? JSON.parse(sessionStorage.closedMenu)
        : [],


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
     * @returns {any|*[]}
     */
    getChartData: (args) => {
        const { element, dataKey, label, legendData } = args;

        const
            dashboard_main = $(`.${IGrace.DASHBOARD}-main`),
            data_arr = [];

        if (dashboard_main.length) {
            const data = JSON.parse(element.getAttribute(`data-${IGrace.PLURALIZE(dataKey)}`));

            for (let item of data) {
                data_arr.push([
                    item[label],
                    item[`${IGrace.PLURALIZE(label.includes(IGrace.COUNTRY) ? dataKey : legendData)}_count`]
                ]);
            }
        }

        return data_arr;
    },


    /**
     * Fill the map chart with the total of registered users per country.
     * See https://developers.google.com/chart/interactive/docs/gallery/geochart
     *
     * @param geoMapChart
     * @param registeredUsersCountries
     * @return {void}
     */
    drawGeoChart: (geoMapChart = null, registeredUsersCountries = null) => {
        if (geoMapChart !== null) {
            const
                chart = new google.visualization.GeoChart(geoMapChart),

                data = google.visualization.arrayToDataTable([
                    ['Country', 'Registered Users'],
                    ...registeredUsersCountries,
                ]),

                options = {
                    legend: 'none',
                    backgroundColor: 'transparent',
                    tooltip: {isHtml: true},
                };

            chart.draw(data, options);
        }
    },


    /**
     * Fill the pie chart with the total products for each subcategory.
     * See https://developers.google.com/chart/interactive/docs/gallery/piechart#configuration
     *
     * @param pieChart
     * @param subcategories
     * @return {void}
     */
    drawPieChart: (pieChart = null, subcategories = null) => {
        if (pieChart !== null) {
            const
                chart = new google.visualization.PieChart(pieChart),
                closed_menu_session_key = sessionStorage.getItem('closedMenu');

            let data = google.visualization.arrayToDataTable([
                ['Subcategory', 'Total Products'],
                ...subcategories,
            ]);

            const
                default_options = {
                    backgroundColor: 'transparent',
                    chartArea: {width: '100%', height: '100%'},
                    is3D: true,
                },

                hide_legend = {
                    legend: 'none',
                    tooltip: {isHtml: true},
                };

            closed_menu_session_key && closed_menu_session_key.indexOf('nav-menu') >= 0
                ? chart.draw(data, default_options)
                : chart.draw(data, Object.assign(default_options, hide_legend));
        }
    },


    /**
     * Configure the geo map chart.
     *
     * @return {void}
     */
    googleGeoChartConfig: () => {
        const
            dashboard_main = $(`.${IGrace.DASHBOARD}-main`),
            geo_map_chart = document.querySelector('#geo_map_chart');

        const registered_users = Admin.getChartData({
            element: geo_map_chart,
            dataKey: IGrace.USER,
            label: IGrace.COUNTRY,
        });

        if (dashboard_main.length) {
            google.charts.load('current', {'packages': 'geochart'});

            google.charts.setOnLoadCallback(() => {
                Admin.drawGeoChart(geo_map_chart, registered_users);
            });
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
            pie_chart = document.querySelector('#pie_chart');

        let subcategories = Admin.getChartData({
            element: pie_chart,
            dataKey: IGrace.SUBCATEGORY,
            label: IGrace.NAME,
            legendData: IGrace.PRODUCT,
        });

        if (dashboard_main.length) {
            google.charts.load('current', {'packages': 'corechart'});

            google.charts.setOnLoadCallback(() => {
                Admin.drawPieChart(pie_chart, subcategories ?? [['No Data', 1]]);
            });
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
            image_type = imageType.replace('_', ' ');

        const image_options = {
            label: `Choose ${IGrace.CAPITALIZE(image_type)}`, // label text
            input: image, // input selector
            dragDrop: false, // drag & drop upload
            multiple: false, // multiple file upload
            fileType: ['png', 'jpg', 'jpeg'], // allowed file formats
            fileTypeError: `Allowed ${image_type} formats are png, jpg, jpeg`, // allowed file formats error
            maxSize: '2 MB', // maximum uploaded file size
            maxSizeError: `Max size of the ${image_type} should not exceed `, // maximum uploaded file size error
        };

        $(image_container).aksFileUpload(image_options);

        image = image.replace('#', '');

        $(`${image_container} .aks-file-upload-content`).addClass(IGrace.CLASS(`${image}-content`));

        $(`#${image}`).attr({
            'name': image,
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

        if (target.is(`#${IGrace.ADD_COLLECTION(collection)}_${imageType}`)
            || target.is(`#${IGrace.UPDATE_COLLECTION(collection)}_${imageType}`)) {
            target.parents().eq(1).next().val(target.val());
            target.next().find('div:first-child').parents().eq(1).remove();
        }
    },


    /**
     * Show the image preview in the edit form.
     *
     * @param args
     * @return {void}
     */
    showImageOnEdit: (args) => {
        const
            { collection, imageType, imageSrc } = args,
            [collection_name, collection_var] = collection,
            update_collection_image_preview = $(`#${IGrace.UPDATE_COLLECTION(collection_name)}_${imageType}_preview`);

        update_collection_image_preview.removeAttr('class');

        const image_actions = {
            true: () => {
                const image_src_actions = {
                    true: () => update_collection_image_preview.addClass('my-3 fs-6 fw-600 text-center')
                        .html('*No Thumbnail Images to Preview*'),
                    false: () => {
                        update_collection_image_preview.addClass('row row-cols-2 row-cols-md-3 justify-content-center align-items-center gap-3 my-3');
                        update_collection_image_preview.html('');

                        $.each(imageSrc, (_, image_source) =>
                            update_collection_image_preview.append(`
                                <div class="col w-auto">
                                    <img src="${image_source}" class="img-thumbnail" width="200px" height="200px" alt="${collection_var[IGrace.NAME]}">
                                </div>
                            `)
                        );
                    },
                };

                image_src_actions[$.isEmptyObject(imageSrc)]();
            },
            false: () =>
                update_collection_image_preview.addClass('d-grid place-items-center my-3')
                    .html(`<img src="${imageSrc}" class="img-thumbnail" width="200px" height="200px" alt="${collection_var[IGrace.NAME]}">`),
        };

        image_actions[imageType.includes(IGrace.THUMB_IMAGE())]();
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
            label: 'Choose Thumbnail Images', // label text
            input: thumb_image, // input selector
            dragDrop: false, // drag & drop upload
            multiple: true, // multiple file upload
            fileType: ['png', 'jpg', 'jpeg'], // allowed file formats
            fileTypeError: 'Allowed thumbnail images formats are png, jpg, jpeg', // allowed file formats error
            maxSize: '2 MB', // maximum uploaded file size
            maxSizeError: 'There is a thumbnail image exceeds allowed size, Max size is', // maximum uploaded file size error
            maxFile: 10, // maximum number of uploaded files
            maxFileError: 'Thumbnail images number exceeds upload limit, Max limit is', // maximum number of uploaded files error
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
                target.parents().eq(1).next().val('');
                image_preview.removeClass('d-none');
            }, 100);
        }
    },



    /* ---------------------------------- CREATE OR UPDATE REQUEST ---------------------------------- */
    ajaxCreateOrUpdateRequest: (form) => {
        $(document).on(IGrace.SUBMIT, `#${form}_form`, function (e) {
            e.preventDefault();

            const
                target     = $(this),
                route      = target.attr('action'),
                action     = form.split('_')[0],
                collection = form.split('_')[1],
                form_data  = Common.filteredFormData(this);

            let main_page  = target.data('main');

            if (action === IGrace.UPDATE) {
                // FormData() accepts only POST method
                form_data.append('_method', IGrace.PUT);
            }

            $.ajax({
                url: route,
                method: IGrace.POST,
                data: form_data,
                success: (data) => {
                    $(IGrace.MODAL(IGrace.ADMIN)).modal('hide');
                    target[0].reset();
                    $(IGrace.ERROR_ELEMENT(action)).empty();

                    const data_actions = {
                        true: () => {
                            let url = main_page;

                            const actions = {
                                [IGrace.ADD]: () => {
                                    $("tbody").append(data[IGrace.ROW]);
                                    url += `?page=${data['last_page']}`;
                                },
                                default: () => $(`#${IGrace.ROW}_${data[collection][IGrace.ID]}`).html($(data[IGrace.ROW]).html()),
                            };

                            (actions[action] || actions.default)();

                            $.ajax({
                                url: url,
                                method: IGrace.GET,
                                success: (successData) => Common.paginationResponse($('.pagination-container'), successData),
                                error: () => Common.somethingWentWrongError,
                            });
                        },
                        false: () => main_page = IGrace.ADMIN,
                    };

                    data_actions[$.inArray(collection, [IGrace.ORDER, IGrace.REVIEW]) === -1]();

                    Common.successMessage(IGrace.SUCCESS, `${IGrace.CAPITALIZE(collection)} has been ${action === IGrace.ADD ? IGrace.ADDED() : IGrace.UPDATED()}`, main_page);
                },
                error: (err) => {
                    if (Common.errorStatus(err) === 404) {
                        return Common.swalResponseJsonErrorMessage(err);
                    }

                    if (Common.errorStatus(err) === 422) {
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
                        imageType: IGrace.MAIN_IMAGE(),
                        imageSrc: main_image,
                    });
                    Admin.showImageOnEdit({
                        collection: [IGrace.CATEGORY, category],
                        imageType: IGrace.BANNER_IMAGE(),
                        imageSrc: banner_image,
                    });

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.CATEGORY, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY));
        });
    },

    /**
     * Edit Subcategory Ajax Request.
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
                        imageType: IGrace.MAIN_IMAGE(),
                        imageSrc: main_image,
                    });
                    Common.showMultiSelectData({
                        userType: IGrace.ADMIN,
                        collection: subcategory,
                        collectionName: IGrace.SUBCATEGORY,
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
     */
    ajaxEditProductRequest: () => {
        $(document).on(IGrace.CLICK, IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.PRODUCT), function (e) {
            e.preventDefault();

            const
                target       = $(this),
                route        = target.data('route'),
                main_image   = target.data(IGrace.MAIN_IMAGE()),
                thumb_images = target.data(IGrace.PLURALIZE(IGrace.THUMB_IMAGE())).split(' ').filter((thumb_image) => thumb_image !== ''),
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
                        imageType: IGrace.MAIN_IMAGE(),
                        imageSrc: main_image,
                    });
                    Admin.showImageOnEdit({
                        collection: [IGrace.PRODUCT, product],
                        imageType: IGrace.PLURALIZE(IGrace.THUMB_IMAGE()),
                        imageSrc: thumb_images,
                    });
                    const commonMultiSelectDataArgs = {
                        userType: IGrace.ADMIN,
                        collection: product,
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
                    status.find('option').removeAttr('selected');
                    status.find('option')
                        .filter((_, product_status) => +product_status.value === +product[IGrace.STATUS])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.PRODUCT, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);

            Admin.emptyImageError(IGrace.UPDATE_COLLECTION(IGrace.PRODUCT));
        });
    },

    /**
     * Edit User Ajax Request.
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
                    role.find('option').removeAttr('selected');
                    role.find('option')
                        .filter((_, user_role) => +user_role.value === +user[IGrace.ROLE])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.USER, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },

    /**
     * Edit Order Ajax Request.
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
                    status.find('option').removeAttr('selected');
                    status.find('option')
                        .filter((_, order_status) => +order_status.value === +order[IGrace.STATUS])
                        .attr('selected', true);

                    $(IGrace.COLLECTION_ACTION(IGrace.EDIT, IGrace.ORDER, true)).modal('show');
                })
                .fail(Common.somethingWentWrongError);
        });
    },
}



/**
 * Export IGrace & Common & Admin Objects.
 */
export { IGrace, Common, Admin };
