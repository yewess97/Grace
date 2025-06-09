'use strict';

import { IGrace, Common, User } from "./helpers/user-helpers.js";


// Ajax Setup
Common.ajaxSetup();


// Remove the errors when the edit modal is closed/hidden
Common.removeErrorsWhenEditModelHides(IGrace.USER);


/* ---------------------------------- AUTHENTICATION & AUTHORIZATION ---------------------------------- */

// Register
User.ajaxAuthRequest(IGrace.REGISTER);

// Login
User.ajaxAuthRequest(IGrace.LOGIN);

// Social Login
User.ajaxSocialAuthRequest();

// Forgot Password
User.ajaxAuthRequest(IGrace.FORGOT_PASSWORD());

// Reset Password
User.ajaxAuthRequest(IGrace.RESET_PASSWORD());

/* ---------------------------------- END AUTHENTICATION & AUTHORIZATION ---------------------------------- */


/* ---------------------------------- ADDRESS ---------------------------------- */

// Add Address
User.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.ADDRESS));

// Edit Address
Common.ajaxEditAddressRequest();

// Update Address
User.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.ADDRESS));

// Delete Address
Common.ajaxDeleteRequest(IGrace.ADDRESS);

// Delete Selected Addresses
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.ADDRESS));

// Restore Address
Common.ajaxRestoreRequest(IGrace.ADDRESS);

// Restore Selected Addresses
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.ADDRESS));

/* ---------------------------------- END ADDRESS ---------------------------------- */


/* ---------------------------------- PRODUCT QUICK VIEW DATA ---------------------------------- */
User.ajaxGetProductDataRequest();

/* ---------------------------------- END PRODUCT QUICK VIEW DATA ---------------------------------- */


/* ---------------------------------- CART ---------------------------------- */

// Add Cart
User.ajaxCreateOrUpdateCartRequest(IGrace.ADD);

// Update Cart
User.ajaxCreateOrUpdateCartRequest(IGrace.UPDATE);

// Delete Cart
User.ajaxDeleteRequest(IGrace.CART);

// Delete all user's carts
User.ajaxDeleteAllCartsRequest();

/* ---------------------------------- END CART ---------------------------------- */


/* ---------------------------------- PLACE ORDER ---------------------------------- */
User.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.ORDER));

/* ---------------------------------- END PLACE ORDER ---------------------------------- */


/* ---------------------------------- REVIEWS ---------------------------------- */

// Add Review
User.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.REVIEW));

// Edit Review
Common.ajaxEditReviewRequest();

// Update Review
User.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.REVIEW));

// Delete Review
User.ajaxDeleteRequest(IGrace.REVIEW);

/* ---------------------------------- END REVIEWS ---------------------------------- */


/* ---------------------------------- FILTER ---------------------------------- */
$(document).on(IGrace.SUBMIT, `#${IGrace.FILTER_PRODUCTS()}_form, #${IGrace.FILTER_PRODUCTS_SORT()}_form`, function (e) {
    e.preventDefault();

    const
        target             = $(this),
        route              = target.attr('action'),
        action             = target.attr(IGrace.ID).split('_')[0],
        filtered_form_data = Common.filteredFormData(this),
        no_results_img_src = target.data('no_results'),

        /**
         * Make sure the same key has the same value in the form data object,
         * (i.e., key: value or key: [value1, value2, ...]).
         */
        form_data = [...filtered_form_data].reduce((formData, [key, value]) => ({
            ...formData,
            [key]: formData[key]
                ? [].concat(formData[key], value)
                : value
        }), {});

    // Save the form data in the sessionStorage to be used in the pagination request later on.
    sessionStorage.setItem(IGrace.FILTER_PRODUCTS(), JSON.stringify(form_data));

    User.ajaxFilterProductsRequest({
        route:             route,
        action:            action,
        noResultsImageSrc: no_results_img_src,
    });
});

/* ---------------------------------- END FILTER ---------------------------------- */


/* ---------------------------------- SEARCH & CLEAR SEARCH/FILTER ---------------------------------- */

// Search
Common.ajaxSearchRequest();

// Clear the Search/Filter
Common.ajaxClearSearchFilterRequest();

/* ---------------------------------- END SEARCH & CLEAR SEARCH/FILTER ---------------------------------- */


/* ---------------------------------- NOTIFICATIONS ---------------------------------- */
// Get Unread Notifications
Common.eventGetNotificationsRequest();

/* ---------------------------------- END NOTIFICATIONS ---------------------------------- */


/* ---------------------------------- PAGINATION ---------------------------------- */
Common.ajaxPagination();

/* ---------------------------------- END PAGINATION ---------------------------------- */
