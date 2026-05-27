'use strict'

import { IGrace } from "./IGrace.js";


/**
 * Handles the image preview configuration for collection images
 * when adding or updating a collection.
 *
 * @param options
 * @return {*}
 */
$.fn.imagePreviewConfig = function (options) {
    const settings = $.extend({
        collection: null,
        imageType:  IGrace.MAIN_IMAGE(),
    }, options);

    return this.each(function () {
        const
            target                            = $(this),
            is_add_or_update_collection_image = [IGrace.ADD_COLLECTION(settings.collection), IGrace.UPDATE_COLLECTION(settings.collection)]
                .some((actionCollection) =>
                    target.is(`#${actionCollection}_${settings.imageType}`));

        if (is_add_or_update_collection_image) {
            target.parents()
                .eq(1)
                .next()
                .val(target.val());

            target.next()
                .find('label')
                .next()
                .remove();
        }
    });
};


/**
 * Handles the display of the image preview for collection images
 * when adding or updating a collection.
 *
 * @param options
 * @return {*}
 */
$.fn.showHideImagePreview = function(options) {
    const settings = $.extend({
        collection: null,
        imageType:  IGrace.MAIN_IMAGE(),
    }, options);

    return this.each(function () {
        const target = $(this);

        if (target.hasClass(`${IGrace.CLASS(IGrace.UPDATE_COLLECTION(settings.collection))}-${IGrace.CLASS(settings.imageType)}-content`)) {
            const image_preview = $(`#${IGrace.UPDATE_COLLECTION(settings.collection)}_${settings.imageType}_preview`);

            if (target.find('div').length > 0) {
                return image_preview.addClass('d-none');
            }

            target.prev().val('');

            target.parents()
                .eq(1)
                .next()
                .val('');

            image_preview.removeClass('d-none');
        }
    });
};
