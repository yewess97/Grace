'use strict'

import { IGrace } from "./IGrace.js";


/**
 * Handles the "check all" and individual row checkboxes functionality in a table.
 *
 * @return {*}
 */
$.fn.checkRows = function() {
    return this.each(function() {
        const
            target    = $(this),
            check_all = $('#check_all'),
            check_row = $(`.check-${IGrace.ROW}`);

        // Check/Uncheck the (check_all) checkbox and checkboxes in the table
        if (target.is('#custom_check_all')) {
            const is_checked_all = check_all.is(':checked');

            if (check_all.is(':indeterminate')) {
                check_all.prop('indeterminate', false);
            }

            check_all.prop('checked', !is_checked_all);
            check_row.prop('checked', !is_checked_all);
        }

        // Check/Uncheck the target (individual) checkbox in the table and (check_all) checkbox
        if (target.hasClass(`custom-check-${IGrace.ROW}`)) {
            target.prev().prop('checked', !target.prev().is(':checked'));

            const checked_count = check_row.filter(':checked').length;

            check_all.prop({
                'checked':       checked_count === check_row.length,
                'indeterminate': checked_count > 0 && checked_count < check_row.length,
            });
        }
    });
};


/**
 * Handles the initialization of the filterMultiSelect plugin for multiple select inputs in forms,
 * with custom configuration and UI adjustments.
 *
 * @return {*}
 */
$.fn.formMultiSelectConfig = function() {
    return this.each(function() {
        const target = $(this);

        let relation = target.prop(IGrace.ID)
            .split('_')
            .slice(2)
            .join('_')
            .split('_');

        relation = relation.length > 1
            ? relation[1]
            : relation[0];

        // Initialize filterMultiSelect
        target.filterMultiSelect({
            placeholderText:           `Select ${IGrace.CAPITALIZE(relation)}`,
            filterText:                'Search...',
            selectAllText:             'Select All',
            selectionLimit:            0,
            caseSensitive:             false,
            allowEnablingAndDisabling: false,
        });

        // Remove dropdown class
        target.removeClass('dropdown');

        // Update search input name
        $('.filter.dropdown-item > input').attr('name', `search_${relation}`);
    });
};


/**
 * Handles the display of the selected items in the filter multi-select dropdown,
 * showing a summary when more than 3 items are selected.
 *
 * @return {*}
 */
$.fn.showHideMultiSelectedItems = function() {
    return this.each(function() {
        const target = $(this);

        if (!target.hasClass('selected-items')) {
            return;
        }

        const related_collections = [IGrace.CATEGORY, IGrace.SUBCATEGORY];

        const
            num_selected_items_element = target.prevAll(':eq(1)'),
            selected_items_length      = target.children().length,
            multiselect                = target.parents('.filter-multi-select'),
            multiselect_label          = multiselect.prev(),
            multiselect_items          = target.parent().next().find('.items'),
            multiselect_max_num_items  = multiselect_items.children().length - 1,
            multiselect_hidden_input   = multiselect.next(),
            select_all                 = multiselect_items.find('.custom-control:first-child'),
            select_all_label           = select_all.find('.custom-control-label'),
            select_all_checkbox        = select_all.find('.custom-checkbox'),
            checked_items              = multiselect_items.find('.custom-control:not(:first-child) > .custom-checkbox:checked'),
            is_hidden                  = selected_items_length > 3;

        const
            multiselect_related_collection_label = related_collections.some((collection) =>
                multiselect_label.html().includes(IGrace.CAPITALIZE(IGrace.PLURALIZE(collection)))
            ),
            top_value = (multiselect_related_collection_label && selected_items_length > 0 && selected_items_length <= 3)
                ? '7%'
                : '18%';

        multiselect_label.css('top', top_value);
        target.attr('hidden', is_hidden);

        num_selected_items_element.attr('hidden', !is_hidden)
            .removeClass('mr-2')
            .addClass('me-2');

        select_all_label.html(selected_items_length === multiselect_max_num_items ? 'Unselect All' : 'Select All');

        let selected_values = [];

        $.each((checked_items), (_, checkedItem) => selected_values.push($(checkedItem).val()));

        selected_values = selected_values.filter(Boolean).join(','); // "filter(Boolean)" removes empty values

        select_all_checkbox.val(selected_values);
        multiselect_hidden_input.val(selected_values);

        num_selected_items_element.html(`${selected_items_length}/${multiselect_max_num_items} Selected items`);
    });
};


/**
 * Handles the "Select All" and individual item selection functionality for multiple select inputs in forms,
 * updating the selected values list and the hidden input accordingly.
 *
 * @param options
 * @return {*}
 */
$.fn.selectAllMultiItems = function(options) {
    const settings = $.extend({
        actionCollection:        null,
        relation:                null,
        multiSelectedValuesList: null,
    }, options);

    return this.each(function() {
        const target = $(this);

        if (!target.is(`input[name="${settings.actionCollection}_${settings.relation}[]"]`)) {
            return;
        }

        const
            is_select_all                   = target.next().html().includes('All'),
            all_items                       = target.parents('.items').find('input[type="checkbox"]'),
            select_all_checkbox             = all_items.first();

        let selected_values                   = settings.multiSelectedValuesList;
        const target_value_in_selected_values = $.inArray(target.val(), selected_values);

        const check_actions = {
            true: () => {
                const select_actions = {
                    true: () => {
                        selected_values.length = 0;
                        select_all_checkbox.val('');

                        $.each((all_items), (_, selectedItem) => selected_values.push($(selectedItem).val() || ''));

                        select_all_checkbox.next().html('Unselect All');
                    },
                    false: () => {
                        if (target_value_in_selected_values === -1) {
                            selected_values.push(target.val());
                        }
                    },
                };

                select_actions[is_select_all]();
            },
            false: () => {
                const select_actions = {
                    true: ()  => selected_values.length = 0,
                    false: () => {
                        if (target_value_in_selected_values !== -1) {
                            selected_values.splice(target_value_in_selected_values, 1);
                        }
                    },
                };

                select_actions[is_select_all]();

                select_all_checkbox.next().html('Select All');
            },
        };

        check_actions[target.is(':checked')]();

        selected_values = selected_values.filter(Boolean).join(','); // "filter(Boolean)" removes empty values

        select_all_checkbox.val(selected_values);
    });
};


/**
 * Handles the display of a loading spinner on a specified element,
 * typically used to indicate that a process is ongoing,
 * and optionally disables the element to prevent further interactions while the process is active.
 *
 * @param options
 * @return {*}
 */
$.fn.loadingSpinner = function(options) {
    const settings = $.extend({
        isDisabled: false,
    }, options);

    return this.each(function() {
        const target = $(this);

        const loading_spinner_actions = {
            true: () => {
                target.prop('disabled', true);

                target.prepend($('<img>', {
                    src:    target.closest('form').data('loading_spinner'),
                    alt:    'Loading',
                    class:  'img-fluid loading-spinner',
                    width:  30,
                    height: 30,
                }));
            },
            false: () =>
                target.prop('disabled', false)
                    .find('.loading-spinner')
                    .remove(),
        };

        loading_spinner_actions[settings.isDisabled]();
    });
};



// /**
//  * Handles the characters counting in a textarea.
//  *
//  * @return {*}
//  */
// $.fn.charsCounter = function () {
//     return this.each(function () {
//
//         $(this).on(IGrace.KEYUP, function (e) {
//             e.preventDefault();
//
//             const
//                 target     = $(this),
//                 text_value = target.val() || '',
//                 max_length = target.attr('maxlength') || 0,
//                 counter    = max_length - text_value.length,
//
//                 counter_element = target.prop('class').includes(IGrace.REVIEW)
//                     ? target.parents().eq(1)
//                         .next()
//                         .next()
//                         .find('> .chars-counter')
//                     : target.parent()
//                         .next()
//                         .addClass('mt-3 mb-2');
//
//             text_value.length
//                 ? counter_element.text(`${counter} characters remaining`)
//                 : counter_element.text('').removeClass('mt-3 mb-2');
//         });
//     });
// };
