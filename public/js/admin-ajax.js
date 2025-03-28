'use strict';

import { IGrace, Common, Admin } from './helpers/admin-helpers.js';


// Ajax Setup
Common.ajaxSetup();


// Remove the errors when the edit modal is closed/hidden
Common.removeErrorsWhenEditModelHides(IGrace.ADMIN);


/* ---------------------------------- CATEGORIES ---------------------------------- */

// Remove any errors that may be occurred for the main & banner images when adding a new image
Admin.emptyImageError(IGrace.ADD_COLLECTION(IGrace.CATEGORY));

// Add Category
Admin.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.CATEGORY));

// Edit Category
Admin.ajaxEditCategoryRequest();

// Update Category
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.CATEGORY));

// Delete Category
Common.ajaxDeleteRequest(IGrace.CATEGORY);

// Delete Selected Categories
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.CATEGORY));

// Restore Category
Common.ajaxRestoreRequest(IGrace.CATEGORY);

// Restore Selected Categories
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.CATEGORY));

/* ---------------------------------- END CATEGORIES ---------------------------------- */


/* ---------------------------------- SUBCATEGORIES ---------------------------------- */

// Remove any errors that may be occurred for the main image when adding a new image
Admin.emptyImageError(IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY));

// Add Subcategory
Admin.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.SUBCATEGORY));

// Edit Subcategory
Admin.ajaxEditSubcategoryRequest();

// Update Subcategory
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.SUBCATEGORY));

// Delete Subcategory
Common.ajaxDeleteRequest(IGrace.SUBCATEGORY);

// Delete Selected Subcategories
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.SUBCATEGORY));

// Restore Subcategory
Common.ajaxRestoreRequest(IGrace.SUBCATEGORY);

// Restore Selected Subcategories
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.SUBCATEGORY));

/* ---------------------------------- END SUBCATEGORIES ---------------------------------- */


/* ---------------------------------- PRODUCTS ---------------------------------- */

// Remove any errors that may be occurred for the main image when adding a new image
Admin.emptyImageError(IGrace.ADD_COLLECTION(IGrace.PRODUCT));

// Add Product
Admin.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.PRODUCT));

// Edit Product
Admin.ajaxEditProductRequest();

// Update Product
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.PRODUCT));

// Delete Product
Common.ajaxDeleteRequest(IGrace.PRODUCT);

// Delete Selected Products
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.PRODUCT));

// Restore Product
Common.ajaxRestoreRequest(IGrace.PRODUCT);

// Restore Selected Products
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.PRODUCT));

/* ---------------------------------- END PRODUCTS ---------------------------------- */


/* ---------------------------------- USERS ---------------------------------- */

// Add User
Admin.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.USER));

// Edit User
Admin.ajaxEditUserRequest();

// Update User
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.USER));

// Delete User
Common.ajaxDeleteRequest(IGrace.USER);

// Delete Selected Users
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.USER));

// Restore User
Common.ajaxRestoreRequest(IGrace.USER);

// Restore Selected Users
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.USER));

/* ---------------------------------- END USERS ---------------------------------- */


/* ---------------------------------- ADDRESSES ---------------------------------- */

// Add Address
Admin.ajaxCreateOrUpdateRequest(IGrace.ADD_COLLECTION(IGrace.ADDRESS));

// Edit Address
Common.ajaxEditAddressRequest();

// Update Address
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.ADDRESS));

// Delete Address
Common.ajaxDeleteRequest(IGrace.ADDRESS);

// Delete Selected Addresses
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.ADDRESS));

// Restore Address
Common.ajaxRestoreRequest(IGrace.ADDRESS);

// Restore Selected Addresses
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.ADDRESS));

/* ---------------------------------- END ADDRESSES ---------------------------------- */


/* ---------------------------------- ORDERS ---------------------------------- */

// Edit Order
Admin.ajaxEditOrderRequest();

// Update Order
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.ORDER));

// Delete Order
Common.ajaxDeleteRequest(IGrace.ORDER);

// Delete Selected Orders
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.ORDER));

// Restore Order
Common.ajaxRestoreRequest(IGrace.ORDER);

// Restore Selected Orders
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.ORDER));

/* ---------------------------------- END ORDERS ---------------------------------- */


/* ---------------------------------- REVIEWS ---------------------------------- */

// Edit Review
Common.ajaxEditReviewRequest();

// Update Review
Admin.ajaxCreateOrUpdateRequest(IGrace.UPDATE_COLLECTION(IGrace.REVIEW));

// Delete Review
Common.ajaxDeleteRequest(IGrace.REVIEW);

// Delete Selected Reviews
Common.ajaxDeleteMultipleRequest(IGrace.PLURALIZE(IGrace.REVIEW));

// Restore Review
Common.ajaxRestoreRequest(IGrace.REVIEW);

// Restore Selected Reviews
Common.ajaxRestoreMultipleRequest(IGrace.PLURALIZE(IGrace.REVIEW));

/* ---------------------------------- END REVIEWS ---------------------------------- */


/* ---------------------------------- FILTER ---------------------------------- */

// Filter the Dashboard
Common.ajaxFilterRequest({
    collection: IGrace.DASHBOARD,
});

// Filter the Orders
Common.ajaxFilterRequest({
    collection: IGrace.PLURALIZE(IGrace.ORDER),
});

// Filter the Users
Common.ajaxFilterRequest({
    collection: IGrace.PLURALIZE(IGrace.USER),
    eventType: IGrace.CHANGE,
    element: IGrace.ROLE
});

/* ---------------------------------- END FILTER ---------------------------------- */


/* ---------------------------------- SEARCH & CLEAR SEARCH/FILTER ---------------------------------- */

// Search
Common.ajaxSearchRequest();

// Clear the Search/Filter
Common.ajaxClearSearchFilterRequest();

/* ---------------------------------- END SEARCH & CLEAR SEARCH/FILTER ---------------------------------- */


/* ---------------------------------- PAGINATION ---------------------------------- */
Common.ajaxPagination();

/* ---------------------------------- END PAGINATION ---------------------------------- */
