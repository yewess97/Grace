<?php


#################################### Actions ####################################
/**
 * Main Actions.
 */
define("ADD",              'add');
define("CREATE",           'create');
define("EDIT",             'edit');
define("UPDATE",           'update');
define("SAVE_CHANGES",     'save changes');
define("REMOVE",           'remove');
define('DELETE',           'delete');
define("DESTROY",          'destroy');
define("RESTORE",          'restore');
define("FILTER",           'filter');
define("STORE_OR_UPDATE",  'storeOr'.ucfirst(UPDATE));
define("DESTROY_MULTIPLE", DESTROY.'Multiple');
define("RESTORE_MULTIPLE", RESTORE.'Multiple');

/**
 * Auth-Related Actions.
 */
define("REGISTER", 'register');
define("LOGIN",    'login');
define("LOGOUT",   'logout');

#################################### End Actions ####################################


#################################### Models Names ####################################
/**
 * Models Names.
 */
define("CATEGORY_MODEL",    'category');
define("SUBCATEGORY_MODEL", 'subcategory');
define("PRODUCT_MODEL",     'product');
define("WISHLIST_MODEL",    'wishlist');
define("CART_MODEL",        'cart');
define("ORDER_MODEL",       'order');
define("ORDER_ITEM_MODEL",  ORDER_MODEL.'_item');
define("USER_MODEL",        'user');
define("ADDRESS_MODEL",     'address');
define("REVIEW_MODEL",      'review');

#################################### End Models Names ####################################


#################################### Database Attributes ####################################
/**
 * Common Attributes.
 */
define("ID",         'id');
define("NAME",       'name');
define("SLUG",       'slug');
define("MAIN_IMAGE", 'main_image');
define("STATUS",     'status');
define("CONDITION",  'condition');
define("PASSWORD",   'password');
define("PRICE",      'price');

/**
 * Category Attributes.
 */
define("BANNER_IMAGE", 'banner_image');

/**
 * Product Attributes.
 */
define("SHORT_DESCRIPTION", 'short_description');
define("LONG_DESCRIPTION",  'long_description');
define("OLD_PRICE",         'old_'.PRICE);
define("NEW_PRICE",         'new_'.PRICE);
define("QUANTITY",          'quantity');

/**
 * Product's Size & Thumbnail Image Attributes.
 */
define("SIZE",        'size');
define("THUMB_IMAGE", 'thumb_image');

/**
 * Order Attributes.
 */
define("TRACKING_NUM", 'tracking_num');
define("NUM_ITEMS",    'num_items');
define("TOTAL_COST",   'total_cost');

/**
 * OrderItem Attributes.
 */
define("PRODUCT_NAME",        PRODUCT_MODEL.'_'.NAME);
define("PRODUCT_MAIN_IMAGE",  PRODUCT_MODEL.'_'.MAIN_IMAGE);
define("PRODUCT_SIZE",        PRODUCT_MODEL.'_'.SIZE);
define("PRODUCT_QUANTITY",    PRODUCT_MODEL.'_'.QUANTITY);
define("PRODUCT_TOTAL_PRICE", PRODUCT_MODEL.'_total_'.PRICE);

/**
 * Cart Attributes.
 */
define("TOTAL_ITEMS", 'total_items');

/**
 * User Attributes.
 */
define("FIRST_NAME",            'first_'.NAME);
define("LAST_NAME",             'last_'.NAME);
define("EMAIL",                 'email');
define("PASSWORD_CONFIRMATION", PASSWORD.'_confirmation');
define("ROLE",                  'role');
define("LAST_SEEN",             'last_seen');

/**
 * Address Attributes.
 */
define("ADDRESS1",    'address_1');
define("ADDRESS2",    'address_2');
define("CITY",        'city');
define("STATE",       'state');
define("COUNTRY",     'country');
define("POSTAL_CODE", 'postal_code');

/**
 * Review Attributes.
 */
define("RATING",    'rating');
define("TITLE",     'title');
define("BODY_TEXT", 'body_text');

/**
 * Password Resets Attributes.
 */
define("TOKEN", 'token');

/**
 * Dates Attributes.
 */
define("DATES", [toPastTense(CREATE).'_at', toPastTense(UPDATE).'_at', toPastTense(DELETE).'_at']);

#################################### End Database Attributes ####################################


#################################### Database Tables Names ####################################
/**
 * Database Tables Names.
 */
define("CATEGORIES_TABLE",           pluralize(CATEGORY_MODEL));
define("SUBCATEGORIES_TABLE",        pluralize(SUBCATEGORY_MODEL));
define("PRODUCTS_TABLE",             pluralize(PRODUCT_MODEL));
define("PRODUCT_SIZES_TABLE",        pluralize(PRODUCT_SIZE));
define("THUMB_IMAGES_TABLE",         pluralize(THUMB_IMAGE));
define("WISHLISTS_TABLE",            pluralize(WISHLIST_MODEL));
define("CARTS_TABLE",                pluralize(CART_MODEL));
define("ORDERS_TABLE",               pluralize(ORDER_MODEL));
define("ORDER_ITEMS_TABLE",          pluralize(ORDER_ITEM_MODEL));
define("USERS_TABLE",                pluralize(USER_MODEL));
define("ADDRESSES_TABLE",            pluralize(ADDRESS_MODEL));
define("REVIEWS_TABLE",              pluralize(REVIEW_MODEL));
define("CATEGORY_SUBCATEGORY_TABLE", CATEGORY_MODEL.'_'.SUBCATEGORY_MODEL);
define("CATEGORY_PRODUCT_TABLE",     CATEGORY_MODEL.'_'.PRODUCT_MODEL);
define("PRODUCT_SUBCATEGORY_TABLE",  PRODUCT_MODEL.'_'.SUBCATEGORY_MODEL);
define("PASSWORD_RESETS_TABLE",      PASSWORD.'_resets');

#################################### End Database Tables Names ####################################


#################################### For Relations ####################################
/**
 * For Relations.
 */
define("RELATED_PRODUCTS",      'related_'.PRODUCTS_TABLE);
define("RELATED_CATEGORIES",    'related_'.CATEGORIES_TABLE);
define("RELATED_SUBCATEGORIES", 'related_'.SUBCATEGORIES_TABLE);

define("SIZES",        pluralize(SIZE));
define("THUMB_IMAGES", capitalizeSecond(THUMB_IMAGES_TABLE));
define("ORDER_ITEMS",  capitalizeSecond(ORDER_ITEMS_TABLE));

#################################### End For Relations ####################################


#################################### Other Standards ####################################
/**
 * Application Standards.
 */
define("COMMON_COLLECTIONS",   'common_collections');
define("SERVICES",             'services');
define("PAYMENT",              'payment');
define("ABOUT_US",             'about_us');
define("CONTACT_US",           'contact_us');
define("TRASHED",              'trashed');
define("ROW",                  'row');
define("LAST_PAGE",            'last_page');
define("MAIN_IMAGES_FOLDER",   pluralize(MAIN_IMAGE));
define("BANNER_IMAGES_FOLDER", pluralize(BANNER_IMAGE));
define("TRASHED_RELATIONS",    TRASHED.'_relations');

/**
 * Auth Standards.
 */
define("AUTH",                   'auth');
define("AUTH_ACTION",            AUTH.'_action');
define("AUTH_SUCCESS",           AUTH.'_success');
define("AUTH_FAILED",            AUTH.'.failed');
define("LOGIN_SOCIAL_PROVIDERS", explode(',', config('auth.providers.login_social')));

/**
 * Password Management Standards.
 *
 */
define("FORGOT_PASSWORD", 'forgot_'.PASSWORD);
define("RESET_PASSWORD",  'reset_'.PASSWORD);

/**
 * User-Related Actions Standards.
 */
define("REGISTER_USER",        userAuthAction(REGISTER));
define("LOGIN_USER",           userAuthAction(LOGIN));
define("FORGOT_PASSWORD_USER", userAuthAction(FORGOT_PASSWORD));
define("RESET_PASSWORD_USER",  userAuthAction(RESET_PASSWORD));

/**
 * Admin Standards.
 */
define("ADMIN",                        'admin');
define("DASHBOARD",                    'dashboard');
define("SUBCATEGORIES_PRODUCTS_COUNT", SUBCATEGORIES_TABLE.'_'.PRODUCTS_TABLE.'_count');

/**
 * User Standards.
 */
define("FULL_NAME",      'full_'.NAME);
define("PROFILE",        'profile');
define("USER_ADDRESSES", USER_MODEL.'_'.ADDRESSES_TABLE);
define("UPDATE_ADDRESS", UPDATE.'_'.ADDRESS_MODEL);

/**
 * Products Standards.
 */
define("HOME_PRODUCTS",               'home_'.PRODUCTS_TABLE);
define("NEW_PRODUCTS",                'new_'.PRODUCTS_TABLE);
define("QUICK_VIEW",                  'quick_view');
define("PRODUCTS_LIST",               PRODUCTS_TABLE.'_list');
define("PRODUCT_DETAILS",             PRODUCT_MODEL.'_details');
define("PRODUCT_SIZE_QUICK_VIEW",     PRODUCT_SIZE.'_'.QUICK_VIEW);
define("PRODUCT_QUANTITY_QUICK_VIEW", PRODUCT_QUANTITY.'_'.QUICK_VIEW);
define("PRODUCTS_PRICES",             PRODUCTS_TABLE.'_'.pluralize(PRICE));
define("PRODUCTS_PAGINATION_ROUTE",   PRODUCTS_TABLE.'_pagination_route');
define("USERS_PAGINATION_ROUTE",      USERS_TABLE.'_pagination_route');
define("ORDERS_PAGINATION_ROUTE",     ORDERS_TABLE.'_pagination_route');
define("SORT",                        'sort');
define("MIN_PRICE",                   'min_'.PRICE);
define("MAX_PRICE",                   'max_'.PRICE);

/**
 * Wishlist Standards.
 */
define("ADD_TO_WISHLIST",      ADD.' to '.WISHLIST_MODEL);
define("USER_WISHLIST_ITEMS",  USER_MODEL.'_'.WISHLIST_MODEL.'_items');
define("EMPTY_WISHLIST",       'empty_'.WISHLIST_MODEL);
define("DELETE_ALL_WISHLISTS", DELETE.'_all_'.WISHLISTS_TABLE);

/**
 * Cart Standards.
 */
define("ADD_TO_CART",      ADD.' to '.CART_MODEL);
define("USER_CART_ITEMS",  USER_MODEL.'_'.CART_MODEL.'_items');
define("EMPTY_CART",       'empty_'.CART_MODEL);
define("DELETE_ALL_CARTS", DELETE.'_all_'.CARTS_TABLE);

/**
 * Checkout Standards.
 */
define("CHECKOUT",                'checkout');
define("CHECKOUT_USER_ADDRESSES", CHECKOUT.'_'.USER_ADDRESSES);
define("CHECKOUT_PAYMENT",        CHECKOUT.'_'.PAYMENT);

/**
 * Order Standards.
 */
define("USER_ORDERS",        USER_MODEL.'_'.ORDERS_TABLE);
define("CREATE_ORDER",       CREATE.'_'.ORDER_MODEL);
define("UPDATE_ORDER",       UPDATE.'_'.ORDER_MODEL);
define("PAYMENT_METHOD",     PAYMENT.'_method');
define("PAYMENT_ID",         collectionId(PAYMENT));
define("PAYMENT_STATUS",     PAYMENT.'_'.STATUS);
define("ORDER_DETAILS",      ORDER_MODEL.'_details');
define("ORDER_PRODUCT_SIZE", ORDER_MODEL.'_'.PRODUCT_SIZE);
define("ORDER_USER_NAME",    ORDER_MODEL.'_'.USER_MODEL.'_'.NAME);
define("START_DATE",         'start_date');
define("END_DATE",           'end_date');

/**
 * Review Standards.
 */
define("CUSTOMERS_REVIEWS", 'customers_'.REVIEWS_TABLE);
define("AVERAGE_RATE",      'average_rate');
define("REVIEW_RATING",     REVIEW_MODEL.'_'.RATING);

/*
 * Cache Standards.
 */
define("CATEGORIES_FOR_SUBCATEGORIES_CACHE_KEY", CATEGORIES_TABLE.'_for'.SUBCATEGORIES_TABLE);
define("CATEGORIES_FOR_PRODUCTS_CACHE_KEY",      CATEGORIES_TABLE.'_for'.PRODUCTS_TABLE);
define("SUBCATEGORIES_FOR_PRODUCTS_CACHE_KEY",   SUBCATEGORIES_TABLE.'_for'.PRODUCTS_TABLE);

#################################### End Other Standards ####################################


#################################### Foreign Keys ####################################
/**
 * Foreign Keys.
 */
define("CATEGORY_ID",    collectionId(CATEGORY_MODEL));
define("SUBCATEGORY_ID", collectionId(SUBCATEGORY_MODEL));
define("PRODUCT_ID",     collectionId(PRODUCT_MODEL));
define("ORDER_ID",       collectionId(ORDER_MODEL));
define("USER_ID",        collectionId(USER_MODEL));
define("ADDRESS_ID",     collectionId(ADDRESS_MODEL));
define("REVIEW_ID",      collectionId(REVIEW_MODEL));

/**
 * The Foreign Keys For Update.
 */
define("UPDATE_CATEGORY_ID",    collectionId(CATEGORY_MODEL,    true));
define("UPDATE_SUBCATEGORY_ID", collectionId(SUBCATEGORY_MODEL, true));
define("UPDATE_PRODUCT_ID",     collectionId(PRODUCT_MODEL,     true));
define("UPDATE_ORDER_ID",       collectionId(ORDER_MODEL,       true));
define("UPDATE_USER_ID",        collectionId(USER_MODEL,        true));
define("UPDATE_ADDRESS_ID",     collectionId(ADDRESS_MODEL,     true));
define("UPDATE_REVIEW_ID",      collectionId(REVIEW_MODEL,      true));

#################################### End Foreign Keys ####################################


#################################### Database Needed Attributes ####################################
/**
 * Login.
 */
define("LOGIN_ATTRIBUTES", [
    EMAIL,
    PASSWORD,
]);

/**
 * Reset Password.
 */
define("RESET_PASSWORD_ATTRIBUTES", [
    EMAIL,
    TOKEN,
    PASSWORD,
    PASSWORD_CONFIRMATION,
]);

/**
 * Category.
 */
define("CATEGORY_ATTRIBUTES", [
    NAME,
    MAIN_IMAGE,
    BANNER_IMAGE,
]);

/**
 * Subcategory.
 */
define("SUBCATEGORY_ATTRIBUTES", [
    NAME,
    MAIN_IMAGE,
    RELATED_CATEGORIES,
]);

/**
 * Product.
 */
define("PRODUCT_ATTRIBUTES", [
    NAME,
    SHORT_DESCRIPTION,
    LONG_DESCRIPTION,
    MAIN_IMAGE,
    RELATED_CATEGORIES,
    RELATED_SUBCATEGORIES,
    SIZES,
    OLD_PRICE,
    NEW_PRICE,
    QUANTITY,
    STATUS,
]);

/**
 * Product item.
 */
define("PRODUCT_ITEM_ATTRIBUTES", [
    ID,
    NAME,
    SLUG,
    SHORT_DESCRIPTION,
    MAIN_IMAGE,
    OLD_PRICE,
    NEW_PRICE,
    STATUS,
]);

/**
 * Filter Products.
 */
define("FILTER_PRODUCTS_ATTRIBUTES", [
    CATEGORIES_TABLE,
    SUBCATEGORIES_TABLE,
    SIZES,
    MIN_PRICE,
    MAX_PRICE,
    SORT,
]);

/**
 * Cart Common.
 */
define("CART_COMMON_ATTRIBUTES", [
    PRODUCT_SIZE,
    PRODUCT_QUANTITY,
]);

/**
 * Quick View Common.
 */
define("QUICK_VIEW_COMMON_ATTRIBUTES", [
    PRODUCT_SIZE_QUICK_VIEW,
    PRODUCT_QUANTITY_QUICK_VIEW,
]);

/**
 * Address.
 */
define("ADDRESS_ATTRIBUTES", [
    ADDRESS1,
    ADDRESS2,
    CITY,
    STATE,
    COUNTRY,
    POSTAL_CODE,
]);

/**
 * Order.
 */
define("ORDER_ATTRIBUTES", [
    TRACKING_NUM,
    NUM_ITEMS,
    TOTAL_COST,
    STATUS,
    PAYMENT_METHOD,
    PAYMENT_ID,
]);

/**
 * Order item.
 */
define("ORDER_ITEM_ATTRIBUTES", [
    PRODUCT_NAME,
    PRODUCT_MAIN_IMAGE,
    ...CART_COMMON_ATTRIBUTES,
    PRODUCT_TOTAL_PRICE,
]);

/**
 * Filter by dates.
 */
define("FILTER_BY_DATES_ATTRIBUTES", [
    START_DATE,
    END_DATE,
]);

/**
 * User.
 */
define("USER_ATTRIBUTES", [
    FIRST_NAME,
    LAST_NAME,
    EMAIL,
    PASSWORD,
    ROLE,
    ...array_map(static fn($provider) => collectionId($provider), LOGIN_SOCIAL_PROVIDERS),
]);

/**
 * User Selected.
 */
define("USER_SELECTED_ATTRIBUTES", [
    ID,
    FIRST_NAME,
    LAST_NAME,
    EMAIL,
]);

/**
 * Review.
 */
define("REVIEW_ATTRIBUTES", [
    RATING,
    TITLE,
    BODY_TEXT,
    PRODUCT_ID,
]);

#################################### End Database Needed Attributes ####################################


#################################### Database Fillable Attributes ####################################
/**
 * Subcategory.
 */
define("SUBCATEGORY_FILLABLE_ATTRIBUTES", [
    NAME,
    SLUG,
    MAIN_IMAGE,
]);

/**
 * Category.
 */
define("CATEGORY_FILLABLE_ATTRIBUTES", [
    ...SUBCATEGORY_FILLABLE_ATTRIBUTES,
    BANNER_IMAGE,
]);

/**
 * Product.
 */
define("PRODUCT_FILLABLE_ATTRIBUTES", [
    NAME,
    SLUG,
    SHORT_DESCRIPTION,
    LONG_DESCRIPTION,
    MAIN_IMAGE,
    OLD_PRICE,
    NEW_PRICE,
    QUANTITY,
    STATUS,
]);

/**
 * Wishlist.
 */
define("WISHLIST_FILLABLE_ATTRIBUTES", [
    USER_ID,
    PRODUCT_ID,
]);

/**
 * Cart.
 */
define("CART_FILLABLE_ATTRIBUTES", [
    ...WISHLIST_FILLABLE_ATTRIBUTES,
    ...CART_COMMON_ATTRIBUTES,
]);

/**
 * Address.
 */
define("ADDRESS_FILLABLE_ATTRIBUTES", [
    ...ADDRESS_ATTRIBUTES,
    USER_ID,
]);

/**
 * User.
 */
define("USER_FILLABLE_ATTRIBUTES", [
    ...USER_ATTRIBUTES,
    LAST_SEEN,
]);

/**
 * Order.
 */
define("ORDER_FILLABLE_ATTRIBUTES", [
    ...ORDER_ATTRIBUTES,
    USER_ID,
    ADDRESS_ID,
]);

/**
 * Order item.
 */
define("ORDER_ITEM_FILLABLE_ATTRIBUTES", [
    ...ORDER_ITEM_ATTRIBUTES,
    ORDER_ID,
]);

/**
 * Review.
 */
define("REVIEW_FILLABLE_ATTRIBUTES", [
    ...REVIEW_ATTRIBUTES,
    USER_ID,
]);

#################################### Database Fillable Attributes ####################################


#################################### Titles ####################################
/**
 * Add Collection Titles.
 */
define("ADD_CATEGORY_TITLE",    collectionAction(ADD, CATEGORY_MODEL,    true));
define("ADD_SUBCATEGORY_TITLE", collectionAction(ADD, SUBCATEGORY_MODEL, true));
define("ADD_PRODUCT_TITLE",     collectionAction(ADD, PRODUCT_MODEL,     true));
define("ADD_USER_TITLE",        collectionAction(ADD, USER_MODEL,        true));
define("ADD_ADDRESS_TITLE",     collectionAction(ADD, ADDRESS_MODEL,     true));

/**
 * Edit Collection Titles.
 */
define("EDIT_CATEGORY_TITLE",    collectionAction(EDIT, CATEGORY_MODEL,    true));
define("EDIT_SUBCATEGORY_TITLE", collectionAction(EDIT, SUBCATEGORY_MODEL, true));
define("EDIT_PRODUCT_TITLE",     collectionAction(EDIT, PRODUCT_MODEL,     true));
define("EDIT_ORDER_TITLE",       collectionAction(EDIT, ORDER_MODEL,       true));
define("EDIT_USER_TITLE",        collectionAction(EDIT, USER_MODEL,        true));
define("EDIT_ADDRESS_TITLE",     collectionAction(EDIT, ADDRESS_MODEL,     true));
define("EDIT_REVIEW_TITLE",      collectionAction(EDIT, REVIEW_MODEL,      true));

/**
 * Other Titles.
 */
define("PRODUCTS_LIST_TITLE",  PRODUCTS_LIST.'_'.TITLE);
define("USER_PROFILE_TITLE",   USER_MODEL.'_'.PROFILE.'_'.TITLE);
define("USER_ADDRESSES_TITLE", USER_ADDRESSES.'_'.TITLE);
define("ORDERS_TITLE",         ORDERS_TABLE.'_'.TITLE);
define("ORDER_NUMBER_TITLE",   ORDER_MODEL.'_number_'.TITLE);

#################################### End Titles ####################################


#################################### Collections Actions ####################################
/**
 * Create Or Update Collections Actions.
 */
define("CREATE_UPDATE_CATEGORY",    createOrUpdate(CATEGORY_MODEL));
define("CREATE_UPDATE_SUBCATEGORY", createOrUpdate(SUBCATEGORY_MODEL));
define("CREATE_UPDATE_PRODUCT",     createOrUpdate(PRODUCT_MODEL));
define("CREATE_UPDATE_CART",        createOrUpdate(CART_MODEL));
define("CREATE_UPDATE_ORDER",       createOrUpdate(ORDER_MODEL));
define("CREATE_UPDATE_USER",        createOrUpdate(USER_MODEL));
define("CREATE_UPDATE_ADDRESS",     createOrUpdate(ADDRESS_MODEL));
define("CREATE_UPDATE_REVIEW",      createOrUpdate(REVIEW_MODEL));

/**
 * Edit Collections Actions.
 */
define("EDIT_CATEGORY",    collectionAction(EDIT, CATEGORY_MODEL));
define("EDIT_SUBCATEGORY", collectionAction(EDIT, SUBCATEGORY_MODEL));
define("EDIT_PRODUCT",     collectionAction(EDIT, PRODUCT_MODEL));
define("EDIT_ORDER",       collectionAction(EDIT, ORDER_MODEL));
define("EDIT_USER",        collectionAction(EDIT, USER_MODEL));
define("EDIT_ADDRESS",     collectionAction(EDIT, ADDRESS_MODEL));
define("EDIT_REVIEW",      collectionAction(EDIT, REVIEW_MODEL));

/**
 * Remove Collections Actions.
 */
define("REMOVE_CATEGORY",    collectionAction(REMOVE, CATEGORY_MODEL));
define("REMOVE_SUBCATEGORY", collectionAction(REMOVE, SUBCATEGORY_MODEL));
define("REMOVE_PRODUCT",     collectionAction(REMOVE, PRODUCT_MODEL));
define("REMOVE_ORDER",       collectionAction(REMOVE, ORDER_MODEL));
define("REMOVE_USER",        collectionAction(REMOVE, USER_MODEL));
define("REMOVE_ADDRESS",     collectionAction(REMOVE, ADDRESS_MODEL));
define("REMOVE_REVIEW",      collectionAction(REMOVE, REVIEW_MODEL));

/**
 * Delete Collections Actions.
 */
define("DELETE_CATEGORY",    collectionAction(DELETE, CATEGORY_MODEL));
define("DELETE_SUBCATEGORY", collectionAction(DELETE, SUBCATEGORY_MODEL));
define("DELETE_PRODUCT",     collectionAction(DELETE, PRODUCT_MODEL));
define("DELETE_CART",        collectionAction(DELETE, CART_MODEL));
define("DELETE_ORDER",       collectionAction(DELETE, ORDER_MODEL));
define("DELETE_USER",        collectionAction(DELETE, USER_MODEL));
define("DELETE_ADDRESS",     collectionAction(DELETE, ADDRESS_MODEL));
define("DELETE_REVIEW",      collectionAction(DELETE, REVIEW_MODEL));

/**
 * Restore Collections Actions.
 */
define("RESTORE_CATEGORY",    collectionAction(RESTORE, CATEGORY_MODEL));
define("RESTORE_SUBCATEGORY", collectionAction(RESTORE, SUBCATEGORY_MODEL));
define("RESTORE_PRODUCT",     collectionAction(RESTORE, PRODUCT_MODEL));
define("RESTORE_ORDER",       collectionAction(RESTORE, ORDER_MODEL));
define("RESTORE_USER",        collectionAction(RESTORE, USER_MODEL));
define("RESTORE_ADDRESS",     collectionAction(RESTORE, ADDRESS_MODEL));
define("RESTORE_REVIEW",      collectionAction(RESTORE, REVIEW_MODEL));

#################################### End Collections Actions ####################################


#################################### Errors ####################################
/**
 * Auth Errors.
 */
define("MANY_ATTEMPTS",              'many_attempts');
define("INVALID_CREDENTIALS",        'invalid_credentials');
define("REGISTER_USER_ERROR",        collectionActionError(REGISTER,        USER_MODEL));
define("LOGIN_USER_ERROR",           collectionActionError(LOGIN,           USER_MODEL));
define("FORGOT_PASSWORD_USER_ERROR", collectionActionError(FORGOT_PASSWORD, USER_MODEL));
define("RESET_PASSWORD_USER_ERROR",  collectionActionError(RESET_PASSWORD,  USER_MODEL));

/**
 * Add Collection Errors.
 */
define("ADD_CATEGORY_ERROR",     collectionActionError(ADD, CATEGORY_MODEL));
define("ADD_SUBCATEGORY_ERROR",  collectionActionError(ADD, SUBCATEGORY_MODEL));
define("ADD_PRODUCT_ERROR",      collectionActionError(ADD, PRODUCT_MODEL));
define("ADD_CART_PRODUCT_ERROR", collectionActionError(ADD, CART_MODEL.'_'.PRODUCT_MODEL));
define("ADD_ORDER_ERROR",        collectionActionError(ADD, ORDER_MODEL));
define("ADD_USER_ERROR",         collectionActionError(ADD, USER_MODEL));
define("ADD_ADDRESS_ERROR",      collectionActionError(ADD, ADDRESS_MODEL));
define("ADD_REVIEW_ERROR",       collectionActionError(ADD, REVIEW_MODEL));

/**
 * Update Collection Errors.
 */
define("UPDATE_CATEGORY_ERROR",    collectionActionError(UPDATE, CATEGORY_MODEL));
define("UPDATE_SUBCATEGORY_ERROR", collectionActionError(UPDATE, SUBCATEGORY_MODEL));
define("UPDATE_PRODUCT_ERROR",     collectionActionError(UPDATE, PRODUCT_MODEL));
define("UPDATE_USER_ERROR",        collectionActionError(UPDATE, USER_MODEL));
define("UPDATE_ADDRESS_ERROR",     collectionActionError(UPDATE, ADDRESS_MODEL));
define("UPDATE_ORDER_ERROR",       collectionActionError(UPDATE, ORDER_MODEL));
define("UPDATE_REVIEW_ERROR",      collectionActionError(UPDATE, REVIEW_MODEL));

/**
 * Filter Collection Errors.
 */
define("FILTER_DASHBOARD_ERROR", collectionActionError(FILTER, DASHBOARD));
define("FILTER_PRODUCTS_ERROR",  collectionActionError(FILTER, PRODUCTS_TABLE));
define("FILTER_USERS_ERROR",     collectionActionError(FILTER, USERS_TABLE));
define("FILTER_ORDERS_ERROR",    collectionActionError(FILTER, ORDERS_TABLE));
define("FILTER_REVIEWS_ERROR",   collectionActionError(FILTER, REVIEWS_TABLE));

#################################### End Errors ####################################


#################################### Views ####################################
/**
 * Auth Views.
 */
define("LOGIN_REGISTER_VIEW",  authView(kebabAll(LOGIN.'_'.REGISTER)));
define("FORGOT_PASSWORD_VIEW", authView(kebabAll(FORGOT_PASSWORD)));
define("RESET_PASSWORD_VIEW",  authView(kebabAll(RESET_PASSWORD)));

/**
 * Admin Views.
 */
define("ADMIN_DASHBOARD_VIEW",      adminView(DASHBOARD));
define("ADMIN_CATEGORIES_VIEW",     adminView(CATEGORIES_TABLE));
define("ADMIN_SUBCATEGORIES_VIEW",  adminView(SUBCATEGORIES_TABLE));
define("ADMIN_PRODUCTS_VIEW",       adminView(PRODUCTS_TABLE));
define("ADMIN_ORDERS_VIEW",         adminView(ORDERS_TABLE));
define("ADMIN_USERS_VIEW",          adminView(USERS_TABLE));
define("ADMIN_USER_ADDRESSES_VIEW", adminView(kebabAll(USER_ADDRESSES)));
define("ADMIN_REVIEWS_VIEW",        adminView(REVIEWS_TABLE));

/**
 * User Views.
 */
define("USER_HOME_VIEW",            userView('index'));
define("USER_PRODUCTS_VIEW",        userView(PRODUCTS_TABLE));
define("USER_PRODUCT_DETAILS_VIEW", userView(kebabAll(PRODUCT_DETAILS)));
define("USER_CART_VIEW",            userView(CART_MODEL));
define("USER_CHECKOUT_VIEW",        userView(CHECKOUT));
define("USER_PROFILE_VIEW",         userView(PROFILE));
define("USER_PAYMENT_VIEW",         userView(PAYMENT));
define("USER_ABOUT_US_VIEW",        userView(kebabAll(ABOUT_US)));
define("USER_CONTACT_US_VIEW",      userView(kebabAll(CONTACT_US)));

#################################### End Views ####################################


#################################### Admin Routes ####################################
/**
 * Admin Routes.
 */
define("ADMIN_DASHBOARD_ROUTE",      adminRoute(DASHBOARD));
define("ADMIN_CATEGORIES_ROUTE",     adminRoute(CATEGORIES_TABLE));
define("ADMIN_SUBCATEGORIES_ROUTE",  adminRoute(SUBCATEGORIES_TABLE));
define("ADMIN_PRODUCTS_ROUTE",       adminRoute(PRODUCTS_TABLE));
define("ADMIN_ORDERS_ROUTE",         adminRoute(ORDERS_TABLE));
define("ADMIN_ORDER_DETAILS_ROUTE",  adminRoute(ORDER_DETAILS));
define("ADMIN_USERS_ROUTE",          adminRoute(USERS_TABLE));
define("ADMIN_USER_ADDRESSES_ROUTE", adminRoute(USER_ADDRESSES));
define("ADMIN_REVIEWS_ROUTE",        adminRoute(REVIEWS_TABLE));

#################################### End Admin Routes ####################################


#################################### Components ####################################
/**
 * Components.
 */
define("HOME_PRODUCT_LEFT_SIDE_COMPONENT", component('home-'.PRODUCT_MODEL.'-left-side'));
define("USER_ADDRESSES_COMPONENT",         component(kebabAll(USER_ADDRESSES)));
define("ORDER_DETAILS_COMPONENT",          component(kebabAll(ORDER_DETAILS)));
define("REVIEWS_COMPONENT",                component(REVIEWS_TABLE));
define("PAGINATION_COMPONENT",             component('pagination-template'));
define("ERROR_COMPONENT",                  component('error'));

#################################### End Components ####################################


#################################### Emails ####################################
/**
 * Emails.
 */
define("ORDER_EMAIL",          emailView(ORDER_MODEL));
define("RESET_PASSWORD_EMAIL", emailView(RESET_PASSWORD));

#################################### End Emails ####################################


#################################### Partials ####################################
/**
 * Main Partials.
 */
define("ADD_USER_ADDRESS_PARTIAL",          partial(ADD.'-'.kebabAll(singularize(USER_ADDRESSES)), ADDRESSES_TABLE));
define("EDIT_USER_ADDRESS_PARTIAL",         partial(EDIT.'-'.kebabAll(singularize(USER_ADDRESSES)), ADDRESSES_TABLE));
define("USER_ADDRESSES_PAGINATION_PARTIAL", partial(kebabAll(USER_ADDRESSES.'_pagination'), ADDRESSES_TABLE));
define("REVIEW_RATING_PARTIAL",             partial(kebabAll(REVIEW_RATING), REVIEWS_TABLE));

/**
 * Collection Row Partials.
 */
define("CATEGORY_ROW_PARTIAL",    partial(CATEGORY_MODEL));
define("SUBCATEGORY_ROW_PARTIAL", partial(SUBCATEGORY_MODEL));
define("PRODUCT_ROW_PARTIAL",     partial(PRODUCT_MODEL));
define("ORDER_ROW_PARTIAL",       partial(ORDER_MODEL));
define("USER_ROW_PARTIAL",        partial(USER_MODEL));
define("ADDRESS_ROW_PARTIAL",     partial(ADDRESS_MODEL));
define("REVIEW_ROW_PARTIAL",      partial(REVIEW_MODEL));

/**
 * Errors Partials.
 */
define("UPDATE_REVIEW_ERRORS_PARTIAL", partial(pluralize(kebabAll(UPDATE_REVIEW_ERROR)), REVIEWS_TABLE));

/**
 * Other Partials.
 */
define("ADMIN_NAV_MENU_LAYOUT_PARTIAL", partial(ADMIN.'-nav-menu-layout', 'other'));
define("CART_CONTENT_PARTIAL",          partial(CART_MODEL.'-content', 'other'));
define("CART_HEADER_CONTENT_PARTIAL",   partial(CART_MODEL.'-header-content', 'other'));
define("PRODUCT_ITEM_COMMON_PARTIAL",   partial(PRODUCT_MODEL.'-item-common', 'other'));
define("TOP_BOTTOM_WEARS_PARTIAL",      partial('top-bottom-wears', 'other'));

#################################### End Partials ####################################


#################################### Modals ####################################
/**
 * Add Modals.
 */
define("ADD_CATEGORY_MODAL",    modal(ADD, CATEGORY_MODEL));
define("ADD_SUBCATEGORY_MODAL", modal(ADD, SUBCATEGORY_MODEL));
define("ADD_PRODUCT_MODAL",     modal(ADD, PRODUCT_MODEL));
define("ADD_USER_MODAL",        modal(ADD, USER_MODEL));

/**
 * Edit Modals.
 */
define("EDIT_CATEGORY_MODAL",    modal(EDIT, CATEGORY_MODEL));
define("EDIT_SUBCATEGORY_MODAL", modal(EDIT, SUBCATEGORY_MODEL));
define("EDIT_PRODUCT_MODAL",     modal(EDIT, PRODUCT_MODEL));
define("EDIT_ORDER_MODAL",       modal(EDIT, ORDER_MODEL));
define("EDIT_USER_MODAL",        modal(EDIT, USER_MODEL));
define("EDIT_REVIEW_MODAL",      modal(EDIT, REVIEW_MODEL));

/**
 * User Modals.
 */
define("USER_QUICK_VIEW_PRODUCT_MODAL", modal(kebabAll(QUICK_VIEW), PRODUCT_MODEL, true));
define("USER_EDIT_REVIEW_MODAL",        modal(EDIT, REVIEW_MODEL, true));

#################################### End Modals ####################################


#################################### Search & Filter ####################################
/**
 * Search.
 */
define("SEARCH_CATEGORIES",     searchFilter(CATEGORIES_TABLE));
define("SEARCH_SUBCATEGORIES",  searchFilter(SUBCATEGORIES_TABLE));
define("SEARCH_PRODUCTS",       searchFilter(PRODUCTS_TABLE));
define("ADMIN_SEARCH_PRODUCTS", searchFilter(table: PRODUCTS_TABLE, role: ADMIN));
define("SEARCH_ORDERS",         searchFilter(ORDERS_TABLE));
define("SEARCH_USERS",          searchFilter(USERS_TABLE));
define("SEARCH_ADDRESSES",      searchFilter(ADDRESSES_TABLE));
define("SEARCH_REVIEWS",        searchFilter(REVIEWS_TABLE));

/**
 * Filter.
 */
define("FILTER_DASHBOARD", searchFilter(DASHBOARD,      true));
define("FILTER_PRODUCTS",  searchFilter(PRODUCTS_TABLE, true));
define("FILTER_ORDERS",    searchFilter(ORDERS_TABLE,   true));

#################################### End Search & Filter ####################################


#################################### Pagination Views ####################################
/**
 * Admin Pagination Views.
 */
define("ADMIN_DASHBOARD_PAGINATION",      paginationView(DASHBOARD));
define("ADMIN_CATEGORIES_PAGINATION",     paginationView(CATEGORIES_TABLE));
define("ADMIN_SUBCATEGORIES_PAGINATION",  paginationView(SUBCATEGORIES_TABLE));
define("ADMIN_PRODUCTS_PAGINATION",       paginationView(PRODUCTS_TABLE));
define("ADMIN_ORDERS_PAGINATION",         paginationView(ORDERS_TABLE));
define("ADMIN_USERS_PAGINATION",          paginationView(USERS_TABLE));
define("ADMIN_USER_ADDRESSES_PAGINATION", paginationView(kebabAll(USER_ADDRESSES)));
define("ADMIN_REVIEWS_PAGINATION",        paginationView(REVIEWS_TABLE));

/**
 * User Pagination Views.
 */
define("PROFILE_ORDERS_PAGINATION",          paginationView(PROFILE.'-'.ORDERS_TABLE,               true));
define("USER_PRODUCTS_PAGINATION",           paginationView(PRODUCTS_TABLE,                         true));
define("CART_PAGINATION",                    paginationView(CART_MODEL,                             true));
define("CHECKOUT_USER_ADDRESSES_PAGINATION", paginationView(kebabAll(CHECKOUT_USER_ADDRESSES), true));

#################################### End Pagination Views ####################################


#################################### Pagination Cache Keys ####################################
/**
 * Admin Pagination Cache Keys.
 */
define("CATEGORIES_PAGINATION_CACHE_KEY",    paginationCacheKeyName(CATEGORIES_TABLE));
define("SUBCATEGORIES_PAGINATION_CACHE_KEY", paginationCacheKeyName(SUBCATEGORIES_TABLE));
define("PRODUCTS_PAGINATION_CACHE_KEY",      paginationCacheKeyName(PRODUCTS_TABLE));
define("USERS_PAGINATION_CACHE_KEY",         paginationCacheKeyName(USERS_TABLE));
define("ORDERS_PAGINATION_CACHE_KEY",        paginationCacheKeyName(ORDERS_TABLE));
define("REVIEWS_PAGINATION_CACHE_KEY",       paginationCacheKeyName(REVIEWS_TABLE));

/**
 * User Pagination Cache Keys.
 */
define("USER_ADDRESSES_PAGINATION_CACHE_KEY", paginationCacheKeyName(USER_ADDRESSES.'_'.auth()->id()));
define("USER_ORDERS_PAGINATION_CACHE_KEY",    paginationCacheKeyName(USER_ORDERS));

/**
 * Common Pagination Cache Keys.
 */
define("ADDRESSES_PAGINATION_CACHE_KEY", paginationCacheKeyName(ADDRESSES_TABLE));

#################################### End Pagination Cache Keys ####################################


#################################### Enums ####################################
/**
 * Product Size Enum.
 */
define("PRODUCT_SIZE_ENUM", [
    'S'   => 1,
    'M'   => 2,
    'L'   => 3,
    'XL'  => 4,
    'XXL' => 5,
]);

/**
 * Sort Products Enum.
 */
define("SORT_PRODUCTS_ENUM", [
    'Best Selling'        => 'best-selling',
    'Alphabetically, A-Z' => 'title-ascending',
    'Alphabetically, Z-A' => 'title-descending',
    'Price, low to high'  => 'price-ascending',
    'Price, high to low'  => 'price-descending',
]);

/**
 * User Role Enum.
 */
define("USER_ROLE_ENUM", [
    'Customer'     => 0,
    ucfirst(ADMIN) => 1
]);

/**
 * Payment Method Enum.
 */
define("PAYMENT_METHOD_ENUM", [
    'Credit Card'      => 1,
    'Cash On Delivery' => 2,
]);

/**
 * Order Status Enum.
 */
define("ORDER_STATUS_ENUM", [
    'Processing' => 1,
    'Shipped'    => 2,
    'Delivered'  => 3,
    'Completed'  => 4,
    'Cancelled'  => 5,
]);

/**
 * Order Status Badge Enum.
 */
define("ORDER_STATUS_BADGE_ENUM", [
    'warning'   => 1,
    'secondary' => 2,
    'primary'   => 3,
    'success'   => 4,
    'danger'    => 5,
]);

/**
 * Order Status Icon Enum.
 */
define("ORDER_STATUS_ICON_ENUM", [
    'autorenew'      => 1,
    'local_shipping' => 2,
    'done_all'       => 3,
    'check_circle'   => 4,
    'block'          => 5,
]);

/**
 * Review Rating Enum.
 */
define("REVIEW_RATING_ENUM", [
    '★'     => 1,
    '★★'    => 2,
    '★★★'   => 3,
    '★★★★'  => 4,
    '★★★★★' => 5,
]);

#################################### End Enums ####################################


#################################### IGrace Helper Functions ####################################
/**
 * Get or Update a specified collection id.
 *
 * @param string $collectionName
 * @param bool $isUpdate
 * @return string
 */
function collectionId(string $collectionName, bool $isUpdate = false): string
{
    $collection_id = "{$collectionName}_".ID;

    return $isUpdate
        ? UPDATE."_$collection_id"
        : $collection_id;
}


/**
 * Add or Edit or Delete a specified collection.
 *
 * @param string $action
 * @param string $collectionName
 * @param bool $isTitle
 * @return string
 */
function collectionAction(string $action, string $collectionName, bool $isTitle = false): string
{
    $collection_action = "{$action}_$collectionName";

    return $isTitle
        ? capitalizeAll($collection_action)
        : $collection_action;
}


/**
 * Action error for a specified collection.
 *
 * @param string $action
 * @param string $collectionName
 * @return string
 */
function collectionActionError(string $action, string $collectionName): string
{
    return "{$action}_{$collectionName}_error";
}


/**
 * Create or Update a specified collection.
 *
 * @param string $collectionName
 * @return string
 */
function createOrUpdate(string $collectionName): string
{
    return CREATE.'_'.UPDATE."_$collectionName";
}


/**
* Admin Routes.
*
* @param string $viewName
* @return string
*/
function adminRoute(string $viewName): string
{
    return ADMIN."_$viewName";
}


/**
 * User Auth Action.
 *
 * @param string $authAction
 * @return string
 */
function userAuthAction(string $authAction): string
{
    return $authAction.'_'.USER_MODEL;
}


/**
 * Component.
 *
 * @param string $componentName
 * @return string
 */
function component(string $componentName): string
{
    return "components.$componentName";
}


/**
 * Partial.
 *
 * @param string $partialName
 * @param string $folderName
 * @return string
 */
function partial(string $partialName, string $folderName = 'table-row'): string
{
    if ($folderName === 'table-row') {
        $partialName .= '-'.ROW;
    }

    return "partials.$folderName.$partialName";
}


/**
 * Modal.
 *
 * @param string $action
 * @param string $collection
 * @param bool $isUser
 * @return string
 */
function modal(string $action, string $collection, bool $isUser = false): string
{
    return $isUser
        ? USER_MODEL.".modals.$action-$collection"
        : ADMIN.".modals.".pluralize($collection).".$action-$collection";
}


/**
 * Search or Filter a specified table.
 *
 * @param string $table
 * @param bool $isFilter
 * @param string|null $role
 * @return string
 */
function searchFilter(string $table, bool $isFilter = false, ?string $role = null): string
{
    return $isFilter
        ? "filter_$table"
        : (isset($role) ? $role.'_' : null)."search_$table";
}


/**
 * Auth view.
 *
 * @param string $viewName
 * @return string
 */
function authView(string $viewName): string
{
    return AUTH.".$viewName";
}


/**
 * Admin view.
 *
 * @param string $viewName
 * @return string
 */
function adminView(string $viewName): string
{
    return ADMIN.'.'.ADMIN."-$viewName";
}


/**
 * User view.
 *
 * @param string $viewName
 * @return string
 */
function userView(string $viewName): string
{
    return USER_MODEL.".$viewName";
}


/**
 * Email view.
 *
 * @param string $emailViewName
 * @return string
 */
function emailView(string $emailViewName): string
{
    return pluralize(EMAIL).'.'.kebabAll($emailViewName).'-'.EMAIL;
}


/**
 * Pagination view.
 *
 * @param string $viewName
 * @param bool $isUser
 * @return string
 */
function paginationView(string $viewName, bool $isUser = false): string
{
    $role = $isUser
        ? USER_MODEL
        : ADMIN;

    return "$role.pagination.".$viewName."-pagination";
}


/**
 * Generate the pagination cache key name.
 *
 * @param string $key
 * @param bool $isTrashed
 * @return string
 */
function paginationCacheKeyName(string $key, bool $isTrashed = false): string
{
    return $key.'_'.(conditionRequest() || $isTrashed ? TRASHED : 'main');
}

#################################### End IGrace Helper Functions ####################################
