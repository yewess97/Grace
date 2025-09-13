'use strict';


const IGrace = {

    /*################################### Actions & Request Methods ###################################*/
    /**
     * Request Methods.
     */
    GET  : 'GET',
    POST : 'POST',
    PUT  : 'PUT',

    /**
     * Actions.
     */
    ADD     : 'add',
    EDIT    : 'edit',
    UPDATE  : 'update',
    REMOVE  : 'remove',
    DELETE  : 'delete',
    RESTORE : 'restore',
    SEARCH  : 'search',
    FILTER  : 'filter',

    /**
     * Take an action with a collection.
     */
    ADD_COLLECTION     : (collection) => `${IGrace.ADD}_${collection}`,
    UPDATE_COLLECTION  : (collection) => `${IGrace.UPDATE}_${collection}`,
    DELETE_COLLECTION  : (collection) => `${IGrace.DELETE}_${collection}`,

    /*################################### End Actions ###################################*/


    /*################################### Event Listeners ###################################*/
    /**
     * Event Listeners.
     */
    CLICK  : 'click',
    CHANGE : 'change',
    INPUT  : 'input',
    KEYUP  : 'keyup',
    SUBMIT : 'submit',

    /*################################### End Event Listeners ###################################*/


    /*################################### Statuses ###################################*/
    /**
     * Statuses.
     */
    SUCCESS : 'success',
    WARNING : 'warning',
    ERROR   : 'error',

    /**
     * Action message.
     */
    ADDED    : () => `${IGrace.ADD}ed`,
    UPDATED  : () => `${IGrace.UPDATE}d`,
    REMOVED  : () => `${IGrace.REMOVE}d`,
    DELETED  : () => `${IGrace.DELETE}d`,
    RESTORED : () => `${IGrace.RESTORE}d`,

    /*################################### End Statuses ###################################*/


    /*################################### Roles ###################################*/
    /**
     * Roles.
     */
    ADMIN : 'admin',
    USER  : 'user',

    /*################################### End Roles ###################################*/


    /*################################### Collections ###################################*/
    /**
     * Collections.
     */
    DASHBOARD   : 'dashboard',
    CATEGORY    : 'category',
    SUBCATEGORY : 'subcategory',
    PRODUCT     : 'product',
    CART        : 'cart',
    ORDER       : 'order',
    ADDRESS     : 'address',
    REVIEW      : 'review',

    COLLECTION_ID       : (collection) => `${collection}_${IGrace.ID}`,
    RELATED_CATEGORY    : ()           => `related_${IGrace.CATEGORY}`,
    RELATED_SUBCATEGORY : ()           => `related_${IGrace.SUBCATEGORY}`,
    CART_PRODUCT        : ()           => `${IGrace.CART}_${IGrace.PRODUCT}`,

    /*################################### End Collections ###################################*/


    /*################################### Authentication Attributes ###################################*/
    /**
     * Authentication Attributes.
     */
    REGISTER : 'register',
    LOGIN    : 'login',
    LOGOUT   : 'logout',
    PASSWORD : 'password',

    FORGOT_PASSWORD : () => `forgot_${IGrace.PASSWORD}`,
    RESET_PASSWORD  : () => `reset_${IGrace.PASSWORD}`,

    /*################################### End Authentication Attributes ###################################*/


    /*################################### Common Attributes ###################################*/
    /**
     * Common Attributes.
     */
    ID                : 'id',
    NAME              : 'name',
    IMAGE             : 'image',
    SHORT_DESCRIPTION : 'short_description',
    LONG_DESCRIPTION  : 'long_description',
    OLD_PRICE         : 'old_price',
    NEW_PRICE         : 'new_price',
    SIZE              : 'size',
    QUANTITY          : 'quantity',
    STATUS            : 'status',
    CITY              : 'city',
    STATE             : 'state',
    COUNTRY           : 'country',
    POSTAL_CODE       : 'postal_code',
    EMAIL             : 'email',
    ROLE              : 'role',
    RATING            : 'rating',
    TITLE             : 'title',
    BODY_TEXT         : 'body_text',
    CHECKOUT          : 'checkout',
    TOTAL_ITEMS       : 'total_items',
    TOTAL_COST        : 'total_cost',
    ROW               : 'row',
    CONDITION         : 'condition',

    /**
     * Common Attributes Methods.
     */
    MAIN_IMAGE              : () => IGrace.IMAGE_TYPE('main'),
    BANNER_IMAGE            : () => IGrace.IMAGE_TYPE('banner'),
    THUMB_IMAGE             : () => IGrace.IMAGE_TYPE('thumb'),
    PRODUCT_SIZE            : () => `${IGrace.PRODUCT}_${IGrace.SIZE}`,
    PRODUCT_SIZE_QUICK_VIEW : () => `${IGrace.PRODUCT_SIZE()}_quick_view`,
    PRODUCT_QUANTITY        : () => `${IGrace.PRODUCT}_${IGrace.QUANTITY}`,
    FIRST_NAME              : () => `first_${IGrace.NAME}`,
    LAST_NAME               : () => `last_${IGrace.NAME}`,
    ADDRESS1                : () => `${IGrace.ADDRESS}_1`,
    ADDRESS2                : () => `${IGrace.ADDRESS}_2`,
    CART_TOTAL_ITEMS        : () => `${IGrace.CART}_${IGrace.TOTAL_ITEMS}`,
    CART_TOTAL_COST         : () => `${IGrace.CART}_${IGrace.TOTAL_COST}`,
    FILTER_PRODUCTS         : () => `${IGrace.FILTER}_${IGrace.PLURALIZE(IGrace.PRODUCT)}`,
    FILTER_PRODUCTS_SORT    : () => `${IGrace.FILTER_PRODUCTS()}_sort`,
    FILTER_PRODUCTS_FORM    : () => `${IGrace.FILTER_PRODUCTS()}_form`,
    FILTER_ORDERS           : () => `${IGrace.FILTER}_${IGrace.PLURALIZE(IGrace.ORDER)}`,

    /*################################### End Common Attributes ###################################*/


    /*################################### Common Elements ###################################*/
    /**
     * Common Elements.
     */
    MODAL         : (role)   => `.${role}-modal`,
    ERROR_ELEMENT : (action) => `.${action}-${IGrace.ERROR}`,

    /*################################### End Common Elements ###################################*/


    /*################################### Other Features ###################################*/
    /**
     * Capitalize String.
     *
     * @param string
     * @return {string}
     */
    CAPITALIZE: (string) =>
        string.split(' ')
            .map((sentence) => sentence.charAt(0).toUpperCase() + sentence.slice(1))
            .join(' '),

    /**
     * Singularize Word.
     *
     * @param word
     * @return {string}
     */
    SINGULARIZE: (word) => {
        const singular_rules = [
            { regex: /(ox)en$/i, replacement: '$1' }, // oxen -> ox
            { regex: /([ml])ice$/i, replacement: '$1ouse' }, // mice -> mouse, lice -> louse
            { regex: /(matr|vert|ind)ices$/i, replacement: '$1ix' }, // matrices -> matrix
            { regex: /(x|ch|ss|sh)es$/i, replacement: '$1' }, // boxes -> box, brushes -> brush
            { regex: /([^aeiouy]|qu)ies$/i, replacement: '$1y' }, // cherries -> cherry
            { regex: /(hive)s$/i, replacement: '$1' }, // hives -> hive
            { regex: /([^f])ves$/i, replacement: '$1fe' }, // wives -> wife
            { regex: /([lr])ves$/i, replacement: '$1f' }, // leaves -> leaf
            { regex: /ses$/i, replacement: 'sis' }, // analyses -> analysis
            { regex: /([ti])a$/i, replacement: '$1um' }, // data -> datum
            { regex: /(buffal|tomat)oes$/i, replacement: '$1o' }, // tomatoes -> tomato
            { regex: /(bus)es$/i, replacement: '$1' }, // buses -> bus
            { regex: /(alias|status)es$/i, replacement: '$1' }, // aliases -> alias
            { regex: /(octop|vir)i$/i, replacement: '$1us' }, // octopi -> octopus
            { regex: /(ax|test)es$/i, replacement: '$1is' }, // axes -> axis
            { regex: /s$/i, replacement: '' } // remove 's' for default singular form
        ];

        for (let { regex, replacement } of singular_rules) {
            if (regex.test(word)) return word.replace(regex, replacement);
        }

        return word;
    },

    /**
     * Pluralize Word.
     *
     * @param word
     * @return {string}
     *
     */
    PLURALIZE: (word) => {
        const plural_rules = [
            { regex: /^(ox)$/i, replacement: '$1en' }, // ox -> oxen
            { regex: /([m|l])ouse$/i, replacement: '$1ice' }, // mouse -> mice, louse -> lice
            { regex: /(matr|vert|ind)(ix|ex)$/i, replacement: '$1ices' }, // matrix -> matrices
            { regex: /(x|ch|ss|sh)$/i, replacement: '$1es' }, // box -> boxes, brush -> brushes
            { regex: /([^aeiouy]|qu)y$/i, replacement: '$1ies' }, // cherry -> cherries
            { regex: /(hive)$/i, replacement: '$1s' }, // hive -> hives
            { regex: /(?:([^f])fe|([lr])f)$/i, replacement: '$1$2ves' }, // wife -> wives, leaf -> leaves
            { regex: /sis$/i, replacement: 'ses' }, // analysis -> analyses
            { regex: /([ti])um$/i, replacement: '$1a' }, // datum -> data
            { regex: /(buffal|tomat)o$/i, replacement: '$1oes' }, // buffalo -> buffaloes, tomato -> tomatoes
            { regex: /(bu)s$/i, replacement: '$1ses' }, // bus -> buses
            { regex: /(alias|status)$/i, replacement: '$1es' }, // alias -> aliases
            { regex: /(octop|vir)us$/i, replacement: '$1i' }, // octopus -> octopi
            { regex: /(ax|test)is$/i, replacement: '$1es' }, // axis -> axes
            { regex: /s$/i, replacement: 's' }, // no change for words ending in s (already plural)
            { regex: /$/, replacement: 's' } // default: add 's'
        ];

        for (let { regex, replacement } of plural_rules) {
            if (regex.test(word)) return word.replace(regex, replacement);
        }

        return word;
    },

    /**
     * Get the image type.
     *
     * @param type
     * @return {string}
     */
    IMAGE_TYPE: (type) => `${type}_${IGrace.IMAGE}`,

    /**
     * Convert an element to be an id.
     *
     * @param element
     * @return {string}
     */
    IDENTIFY: (element) => element.replace(/-/g, '_'),

    /**
     * Convert an element to be a class.
     *
     * @param element
     * @return {string}
     */
    CLASS: (element) => element.replace(/_/g, '-'),

    /**
     * Delete or Restore an/many element(s).
     *
     * @param action
     * @param collection
     * @param isId
     * @return {string}
     *
     */
    COLLECTION_ACTION: (action, collection, isId = false) => {
        const collection_action = `${action}-${collection}`;

        return isId
            ? `#${IGrace.IDENTIFY(collection_action)}_${action.includes(IGrace.EDIT) ? 'modal' : 'btn'}`
            : `.${collection_action}-btn`;
    },

    /**
     * Format Price.
     *
     * @param price
     * @return {string}
     */
    PRICE_FORMAT: (price) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'EGP' }).format(price),

    /**
     * Check if a string is not empty.
     *
     * @param string
     * @return {boolean}
     */
    IS_NOT_EMPTY: (string) => ($.type(string) === 'string' && string.length > 0 && string.trim()) || $.type(string) !== 'undefined' || string !== null,

    /**
     * Check if a string or integer is in an array.
     *
     * @param haystack
     * @param needle
     * @return {boolean}
     */
    IS_IN_ARRAY: (haystack, needle) => $.type(needle) === 'string'
        ? haystack.some((word) => needle.includes(word))
        : haystack.includes(needle),

    /*################################### End Other Features ###################################*/
}



/**
 * Export IGrace Object.
 */
export { IGrace };
