'use strict';

import { IGrace, Common, User } from "./helpers/user-helpers.js";


// Ajax Setup
Common.ajaxSetup();

// Remove the errors when the edit modal is closed/hidden
Common.removeErrorsWhenEditModelHides(IGrace.USER);

// A global flag (attached to window) so both files/modules share the same reference
window.isFormDirty = false;

// Track changes on any form input, including dynamic ones
$(document).on("input change", "input, textarea, select", () => window.isFormDirty = true);


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


/* ---------------------------------- WISHLIST ---------------------------------- */

// Add wishlist
User.ajaxCreateWishlistRequest();

// Delete wishlist
User.ajaxDeleteRequest(IGrace.WISHLIST);

// Delete all user's wishlists
User.ajaxDeleteAllUserCollectionRequest(IGrace.WISHLIST);

/* ---------------------------------- END WISHLIST ---------------------------------- */


/* ---------------------------------- CART ---------------------------------- */

// Add Cart
User.ajaxCreateOrUpdateCartRequest(IGrace.ADD);

// Update Cart
User.ajaxCreateOrUpdateCartRequest(IGrace.UPDATE);

// Delete Cart
User.ajaxDeleteRequest(IGrace.CART);

// Delete all user's carts
User.ajaxDeleteAllUserCollectionRequest(IGrace.CART);

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


/* ---------------------------------- FILTER PRODUCTS ---------------------------------- */
User.ajaxFilterProductsRequest();

/* ---------------------------------- END FILTER PRODUCTS ---------------------------------- */


/* ---------------------------------- SEARCH & CLEAR SEARCH/FILTER ---------------------------------- */

// Search
Common.ajaxSearchRequest();

// Clear the Search/Filter
Common.ajaxClearSearchFilterRequest();

/* ---------------------------------- END SEARCH & CLEAR SEARCH/FILTER ---------------------------------- */


/* ---------------------------------- NOTIFICATIONS ---------------------------------- */
Common.eventGetNotificationsRequest();

/* ---------------------------------- END NOTIFICATIONS ---------------------------------- */


/* ---------------------------------- PAGINATION ---------------------------------- */
Common.ajaxPagination();

/* ---------------------------------- END PAGINATION ---------------------------------- */


// Warn the user before leaving/refreshing the form
Common.warnBeforeLeaving();
