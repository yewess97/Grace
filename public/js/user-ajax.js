'use strict';

import { IGrace, Common, User } from "./helpers/user-helpers.js";


// Ajax Setup
Common.ajaxSetup();

// Remove the errors when the edit modal is closed/hidden
Common.removeErrorsWhenEditModelHides(IGrace.USER);


/*============================== Authentication & Authorization ==============================*/

// Register
User.ajaxAuthRequest(IGrace.REGISTER);

// Login
User.ajaxAuthRequest(IGrace.LOGIN);

// Forgot Password
User.ajaxAuthRequest(IGrace.FORGOT_PASSWORD());

// Reset Password
User.ajaxAuthRequest(IGrace.RESET_PASSWORD());

/*============================== End Authentication & Authorization ==============================*/


/*============================== Address ==============================*/

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

/*============================== End Address ==============================*/


/*============================== Product ==============================*/

// Product Quick View Data
User.ajaxGetProductDataRequest();

/*============================== End Product ==============================*/


/*============================== Cart ==============================*/

// Add Cart
User.ajaxCreateOrUpdateCartRequest(IGrace.ADD);

// Update Cart
User.ajaxCreateOrUpdateCartRequest(IGrace.UPDATE);

// Delete Cart
User.ajaxDeleteRequest(IGrace.CART);

// Delete all user's carts
User.ajaxDeleteAllCartsRequest();

/*============================== End Cart ==============================*/


/*============================== Checkout User Addresses ==============================*/

// Checkout user addresses configuration
User.ajaxCheckoutUserAddressesRequest();

/*============================== End Checkout User Addresses ==============================*/


/*============================== Order ==============================*/

// Place an order
User.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.ORDER));

/*============================== End Order ==============================*/


/*============================== Reviews ==============================*/

// Add Review
User.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.REVIEW));

// Edit Review
Common.ajaxEditReviewRequest();

// Update Review
User.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.REVIEW));

// Delete Review
User.ajaxDeleteRequest(IGrace.REVIEW);

/*============================== End Reviews ==============================*/


/*============================== Filter ==============================*/
$(document).on(IGrace.SUBMIT, `#${IGrace.FILTER}_${IGrace.PLURALIZE(IGrace.PRODUCT)}_form`, function (e) {
    e.preventDefault();

    const
        target             = $(this),
        route              = target.attr('action'),
        action             = target.attr(IGrace.ID).split('_')[0],
        form_data          = Common.filteredFormData(this),
        no_results_img_src = target.data('no_results');

    User.ajaxFilterProductsRequest({
        route:             route,
        action:            action,
        formData:          form_data,
        noResultsImageSrc: no_results_img_src,
    });
});

/*============================== End Filter ==============================*/


/*============================== Search & Clear Search/Filter ==============================*/

// Search
Common.ajaxSearchRequest();

// Clear the Search/Filter
Common.ajaxClearSearchFilterRequest();

/*============================== End Search & Clear Search/Filter ==============================*/


/*============================== Pagination ==============================*/

// Default pagination
Common.ajaxPagination();

/*============================== End Pagination ==============================*/
