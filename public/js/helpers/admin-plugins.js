'use strict'

import { IGrace } from "./IGrace.js";


$.fn.imageConfig = function(options) {
    const settings = $.extend({
        collection: null,
        imageType:  IGrace.MAIN_IMAGE,
    }, options);

    return this.each(function () {
        const
            target = $(this),
            is_add_or_update_collection_image = [IGrace.ADD_COLLECTION(settings.collection), IGrace.UPDATE_COLLECTION(settings.collection)]
                .some((actionCollection) => target.is(`#${actionCollection}_${settings.imageType}`));

        if (is_add_or_update_collection_image) {
            target.parents()
                .eq(1)
                .next()
                .val(target.val());

            setTimeout(() =>
                    target.next()
                        .find('div')
                        .remove()
                , 10);
        }
    });
};
