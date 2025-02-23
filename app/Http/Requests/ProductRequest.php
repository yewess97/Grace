<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    use FormRequestHelper;

    /**
     * Get the validation rules that apply to the request.
     *
     * @param string|null $id
     * @return array<string, mixed>
     */
    final public function rules(string $id = null): array
    {
        if ($this->operation === FILTER) {
            return [
                ...$this->multipleSelectionValidation(CATEGORIES_TABLE, 3, CATEGORIES_TABLE),
                ...$this->multipleSelectionValidation(SUBCATEGORIES_TABLE, 6, SUBCATEGORIES_TABLE),
                ...$this->multipleSelectionValidation(SIZES, 5, PRODUCT_SIZES_TABLE),
                ...$this->numberValidation(MIN_PRICE, 'numeric'),
                ...$this->numberValidation(MAX_PRICE, 'numeric'),
            ];
        }

        $rules = [
            ...$this->nameDescriptionRules($id, NAME, 3, 100),
            ...$this->nameDescriptionRules(null, SHORT_DESCRIPTION, 5, 1000),
            ...$this->nameDescriptionRules(null, LONG_DESCRIPTION, 10, 5000),
            ...$this->imageValidation(MAIN_IMAGE),
            ...$this->multipleSelectionValidation(RELATED_CATEGORIES, 3, CATEGORIES_TABLE),
            ...$this->multipleSelectionValidation(RELATED_SUBCATEGORIES, 6, SUBCATEGORIES_TABLE),
            ...$this->multipleSelectionValidation(SIZES, 5, PRODUCT_SIZES_TABLE),
            ...$this->numberValidation(OLD_PRICE, 'numeric'),
            ...$this->numberValidation(NEW_PRICE, 'numeric'),
            ...$this->numberValidation(QUANTITY, 'integer'),
            ...$this->booleanValidation(STATUS),
        ];

        if (Arr::has($this->modelAttributes, THUMB_IMAGE)) {
            $rules[$this->dataKeyOf(THUMB_IMAGE).'.*'] = ['image', 'mimes:png,jpg,jpeg', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        $cap_product           = ucfirst(PRODUCT_MODEL);
        $cap_categories        = ucfirst(pluralize(CATEGORY_MODEL));
        $cap_subcategories     = ucfirst(pluralize(SUBCATEGORY_MODEL));
        $cap_sizes             = ucfirst(pluralize(SIZE));
        $name_desc_regex_rules = 'characters, numbers, and some symbols';

        if ($this->operation === FILTER) {
            return [
                ...$this->multipleSelectionValidation(CATEGORIES_TABLE, 3, null, $cap_categories, true),
                ...$this->multipleSelectionValidation(SUBCATEGORIES_TABLE, 6, null, $cap_subcategories, true),
                ...$this->multipleSelectionValidation(SIZES, 5, null, $cap_sizes, true),
                ...$this->numberValidation(MIN_PRICE, 'numeric', true),
                ...$this->numberValidation(MAX_PRICE, 'numeric', true),
            ];
        }

        return [
            ...$this->validationMessages(NAME, 2, 50, $name_desc_regex_rules, PRODUCT_MODEL),
            ...$this->validationMessages(SHORT_DESCRIPTION, 5, 1000, $name_desc_regex_rules),
            ...$this->validationMessages(LONG_DESCRIPTION, 10, 5000, $name_desc_regex_rules),
            ...$this->imageValidation(MAIN_IMAGE, true),
            ...$this->multipleSelectionValidation(RELATED_CATEGORIES, 3, null, $cap_categories, true),
            ...$this->multipleSelectionValidation(RELATED_SUBCATEGORIES, 6, null, $cap_subcategories, true),
            ...$this->multipleSelectionValidation(SIZES, 5, null, $cap_sizes, true),
            ...$this->numberValidation(OLD_PRICE, 'numeric', true),
            ...$this->numberValidation(NEW_PRICE, 'numeric', true),
            ...$this->numberValidation(QUANTITY, 'integer', true),
            ...$this->booleanValidation(STATUS, true, "Available or Not Available"),
        ];
    }

    /**
     * Set the validation rules for the name & descriptions.
     *
     * @param string|null $id
     * @param string $attribute
     * @param int $min
     * @param int $max
     * @return array<string, string>
     */
    private function nameDescriptionRules(string|null $id, string $attribute, int $min, int $max): array
    {
        $unique_product = Rule::unique(PRODUCTS_TABLE, SLUG);

        return [
            $this->dataKeyOf($attribute) => [
                "required", "regex:/^[a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{}\'\"\\|:,.<>\/]*$/", "min:$min", "max:$max",
                ($this->operation === UPDATE && isset($id)) ? $unique_product->ignore($id) : $unique_product
            ],
        ];
    }

    /**
     * Price & Quantity validation rules & messages.
     *
     * @param string $attribute
     * @param string $numericType
     * @param bool $isMessage
     * @return array<string, string>
     */
    private function numberValidation(string $attribute, string $numericType, bool $isMessage = false): array
    {
        $cap_attribute = capitalizeAll($attribute);
        $numeric_rules = ['numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'max:9999.99'];
        $integer_rules = ['integer', 'regex:/^[1-9]\d*$/'];

        if ($isMessage) {
            return [
                ...$this->requiredMessage($this->dataKeyOf($attribute), $cap_attribute),
                "{$this->dataKeyOf($attribute)}.$numericType" => "$cap_attribute must be a number",
                "{$this->dataKeyOf($attribute)}.regex" => "$cap_attribute must be a number ".$numericType === 'numeric' ? "2 decimal places" : "",
                "{$this->dataKeyOf($attribute)}.max" => "$cap_attribute must be less than :max",
                "{$this->dataKeyOf($attribute)}.min" => "$cap_attribute must be at least :min",
            ];
        }

        return [
            $this->dataKeyOf($attribute) => [
                'required',
                $numericType === 'numeric' ? $numeric_rules : $integer_rules,
                'min:1'
            ],
        ];
    }

}
