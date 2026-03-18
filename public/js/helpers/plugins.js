'use strict'


/**
 * Update ratings dynamically.
 *
 * @param rating
 * @return {*}
 */
$.fn.starRating = function(rating) {
    return this.each(function() {
        const container   = $(this).empty();
        const filled_star = '<span class="position-relative fs-4 star-fill" aria-label="Filled Star">★</span>';
        const empty_star  = '<span class="position-relative fs-4 star-empty" aria-label="Empty Star">☆</span>';

        container.html(filled_star.repeat(rating) + empty_star.repeat(5 - rating));
    });
};
